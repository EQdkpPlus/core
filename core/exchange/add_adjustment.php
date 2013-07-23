<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2011-11-29 17:10:46 +0100 (Di, 29 Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11470 $
 * 
 * $Id: add_adjustment.php 11470 2011-11-29 16:10:46Z Godmod $
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_add_adjustment')){
	class exchange_add_adjustment extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time');
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_add_adjustment', exchange_add_adjustment::$shortcuts);
?>