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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "inactive_crontask" ) ) {
	class inactive_crontask extends crontask {

		public function __construct(){
			$this->defaults['active']		= true;
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Update status of characters to inactive';
		}

		public function run() {
			if ((int)$this->config->get('inactive_period') === 0) return;

			$arrMarkedForInactive = array();

			//Check the Raids
			$members = $this->pdh->aget('member_dates', 'last_raid', 0, array($this->pdh->get('member', 'id_list'), null, false));
			$crit_time = $this->time->time - (24*3600*intval($this->config->get('inactive_period')));
			foreach($members as $member_id => $last_raid) {
				if($last_raid < $crit_time) $arrMarkedForInactive[$member_id] = $member_id;
			}

			//Now check the calendarevents for attendance
			$objQuery = $this->db->prepare("SELECT ca.* FROM __calendar_events ce, __calendar_raid_attendees ca WHERE ce.timestamp_start < ? AND ce.timestamp_start > ? AND ca.calendar_events_id = ce.id")->execute($this->time->time, $crit_time);
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					//set the members immediately active, as they have been confirmed or backup
					if((int)$row['signup_status'] === 0 || (int)$row['signup_status'] === 3){
						$member_id = (int)$row['member_id'];
						if((int)$this->config->get('auto_set_active') == 1){	
							$this->pdh->put('member', 'change_status', array($member_id, 1));
						}

						if(isset($arrMarkedForInactive[$member_id])) unset($arrMarkedForInactive[$member_id]);
					}
				}
			}

			//Now set them inactive
			foreach($arrMarkedForInactive as $member_id){
				$this->pdh->put('member', 'change_status', array($member_id, 0));
			}

			$this->pdh->process_hook_queue();
		}
	}
}
