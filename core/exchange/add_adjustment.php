<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_add_adjustment')){
	class exchange_add_adjustment extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();
		
		/*
		* Required: Adjustment Date (Format: Y-m-d H:i)
		* Required: Adjustment Reason
		* Required: Member IDs (EQdkp Character ID)
		* Required: Adjustment Value
		* Optional: Raid ID
		* Optional: Event ID
		*
		* Returns: Status 0 on error, Status 1 and inserted adjustment-ID on succes
		*/
		public function post_add_adjustment($params, $body){
			if ($this->user->check_auth('a_indivadj_add', false)){
				$xml = simplexml_load_string($body);
				if ($xml){
					//Check required values
					if (!isset($xml->adjustment_date) && !strlen($xml->adjustment_date)) return $this->pex->error('required data missing');
					if (!isset($xml->adjustment_value) && !strlen($xml->adjustment_value)) return $this->pex->error('required data missing');
					if (!isset($xml->adjustment_reason) && !strlen($xml->adjustment_reason)) return $this->pex->error('required data missing');
					if (!isset($xml->adjustment_members) && !is_object($xml->adjustment_members->member)) return $this->pex->error('required data missing');
					
					//Adjustment Date
					$intAdjDate = $this->time->fromformat($xml->adjustment_date, "Y-m-d H:i");
					
					//Adjustment Members
					$arrAdjMembers = array();
					$arrMemberIDList = $this->pdh->get('member', 'id_list', array());
					foreach($xml->adjustment_members->member as $objMemberID){
						if (in_array(intval($objMemberID), $arrMemberIDList)) $arrAdjMembers[] = intval($objMemberID);
					}
					if(count($arrAdjMembers) == 0) return $this->pex->error('required data missing');
					
					//Adjustment Value
					$fltAdjValue = (float)$xml->adjustment_value;
					
					//Adjustment Reason
					$strAdjReason = filter_var((string)$xml->adjustment_reason, FILTER_SANITIZE_STRING);
					
					//Adjustment Event ID
					$arrEventIDList = $this->pdh->get('event', 'id_list');
					$intAdjEventID = (isset($xml->adjustment_event_id) && in_array(intval($xml->adjustment_event_id), $arrEventIDList)) ? intval($xml->adjustment_event_id) : 0;
					
					//Adjustment Raid ID
					$arrRaidIDList = $this->pdh->get('raid', 'id_list');
					$intAdjRaidID = (isset($xml->adjustment_raid_id) && in_array(intval($xml->adjustment_raid_id), $arrRaidIDList)) ? intval($xml->adjustment_raid_id) : 0;
					
					//Insert Adjustment
					$mixAdjID = $this->pdh->put('adjustment', 'add_adjustment', array($fltAdjValue, $strAdjReason, $arrAdjMembers, $intAdjEventID, $intAdjRaidID, $intAdjDate));
					if (!$mixAdjID) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();
					
					return array('adjustment_id' => $mixAdjID);
				}
				return $this->pex->error('malformed xml');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>