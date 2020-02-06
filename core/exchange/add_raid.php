<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if (!class_exists('exchange_add_raid')){
	class exchange_add_raid extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		/*
		* Required: Raid Date (Format: Y-m-d H:i)
		* Required: Raid Attendees (Member-IDs)
		* Required: EventID
		* Optional: Raid-Note
		* Required: Raid-Value (DKP)
		*
		* Returns: Status 0 on error, Status 1 and inserted Raid-ID on succes
		*/
		public function post_add_raid($params, $arrBody){
			$isAPITokenRequest = $this->pex->isApiWriteTokenRequest();
			if ($isAPITokenRequest ||  $this->user->check_auth('a_raid_add', false)){
				$blnTest = (isset($params['get']['test']) && $params['get']['test']) ? true : false;

				if (count($arrBody)){
					//Check required values
					if (!isset($arrBody['raid_date']) || !strlen($arrBody['raid_date'])) return $this->pex->error('required data missing', 'raid_date');
					if (!isset($arrBody['raid_attendees']) || !count($arrBody['raid_attendees']['member'])) return $this->pex->error('required data missing', 'raid_attendees');
					if (!isset($arrBody['raid_value']) || !strlen($arrBody['raid_value'])) return $this->pex->error('required data missing', 'raid_value');
					if (!isset($arrBody['raid_event_id']) || !strlen($arrBody['raid_event_id'])) return $this->pex->error('required data missing', 'raid_event_id');

					$intRaidDate = $this->time->fromformat($arrBody['raid_date'], "Y-m-d H:i");
					$arrRaidAttendees = array();

					$arrMemberIDList = $this->pdh->get('member', 'id_list', array());

					if(is_array($arrBody['raid_attendees']['member'])){
						foreach($arrBody['raid_attendees']['member'] as $objMemberID){
							if (in_array(intval($objMemberID), $arrMemberIDList)) $arrRaidAttendees[] = intval($objMemberID);
						}
					} else {
						$objMemberID = intval($arrBody['raid_attendees']['member']);
						if (in_array(intval($objMemberID), $arrMemberIDList)) $arrRaidAttendees[] = intval($objMemberID);

					}

					if(count($arrRaidAttendees) == 0) return $this->pex->error('required data missing', 'no member found');

					$fltRaidValue = (float)$arrBody['raid_value'];
					$arrEventIDList = $this->pdh->get('event', 'id_list');
					$intRaidEventID = intval($arrBody['raid_event_id']);
					if (!in_array($intRaidEventID, $arrEventIDList)) return $this->pex->error('required data missing', 'event not found');

					$strRaidNote = filter_var((string)$arrBody['raid_note'], FILTER_SANITIZE_STRING);

					if($blnTest) return array('test' => 'success');

					$raid_upd = $this->pdh->put('raid', 'add_raid', array($intRaidDate, $arrRaidAttendees, $intRaidEventID, $strRaidNote, $fltRaidValue));
					if (!$raid_upd) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();

					return array('raid_id' => $raid_upd);
				}
				return $this->pex->error('malformed input');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
