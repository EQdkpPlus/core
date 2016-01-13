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

if (!class_exists('pdh_r_calendar_raids_guests')){
	class pdh_r_calendar_raids_guests extends pdh_r_generic{

		private $guests;
		public $hooks = array(
			'guests_update',
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_calendar_raids_table.guests');
			$this->pdc->del('pdh_calendar_raids_table.guestsEvents');
			$this->pdc->del_prefix('plugin.guests');
			$this->guests = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->guests		= $this->pdc->get('pdh_calendar_raids_table.guests');
			$this->guestsEvent	= $this->pdc->get('pdh_calendar_raids_table.guestsEvents');
			if($this->guests !== NULL && $this->guestsEvent !== NULL){
				return true;
			}

			// empty array as default
			$this->guests	= array();

			$objQuery = $this->db->query('SELECT * FROM __calendar_raid_guests;');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->guests[$row['id']] = array(
						'name'				=> $row['name'],
						'email'				=> $row['email'],
						'note'				=> $row['note'],
						'timestamp_signup'	=> $row['timestamp_signup'],
						'raidgroup'			=> $row['raidgroup'],
						'class'				=> $row['class'],
						'creator'			=> $row['creator'],
						'status'			=> $row['status'],
						'eventid'			=> $row['calendar_events_id'],
					);
					$this->guestsEvent[$row['calendar_events_id']][$row['id']] = $this->guests[$row['id']];
				}
				$this->pdc->put('pdh_calendar_raids_table.guests', $this->guests, NULL);
				$this->pdc->put('pdh_calendar_raids_table.guestsEvents', $this->guestsEvent, NULL);
			}

			return true;
		}

		public function get_guests4approval(){
			$output = array();
			if(isset($this->guests) && count($this->guests) > 0){
				foreach($this->guests as $guestID => $guestData){
					if($guestData['status'] == 1){
						$output[] = $guestID;
					}
				}
			}
			return $output;
		}

		public function get_members($eventid=''){
			$output = ($eventid) ? ((isset($this->guestsEvent[$eventid])) ? $this->guestsEvent[$eventid] : '') : $this->guests;
			return (is_array($output)) ? $output : array();
		}

		public function get_check_email($eventid, $email){
			$guests = (isset($this->guestsEvent[$eventid])) ? $this->guestsEvent[$eventid] : array();
			if(count($guests) > 0){
				foreach($guests as $guestdata){
					if($guestdata['email'] == $email){
						return 'true';
					}
				}
			}
			return false;
		}

		public function get_guest($id){
			return $this->guests[$id];
		}

		public function get_class($id){
			return $this->guests[$id]['class'];
		}

		public function get_status($id){
			return $this->guests[$id]['status'];
		}

		public function get_eventlink($id, $external=false){
			$eventID	= $this->get_event($id, true);
			return (($external) ? $this->env->buildlink() : '').$this->routing->build("calendarevent", $this->pdh->get('event', 'name', array($eventID)), $eventID, false, true);
		}

		public function get_event($id, $raw=false){
			return ($raw) ? $this->guests[$id]['eventid'] : $this->pdh->get('calendar_events', 'name', array($this->guests[$id]['eventid']));

		}

		public function get_email($id){
			return $this->guests[$id]['email'];
		}

		public function get_note($id){
			return $this->guests[$id]['note'];
		}

		public function get_name($id){
			return $this->guests[$id]['name'];
		}

		public function get_date($id, $raw=false){
			return ($raw) ? $this->guests[$id]['timestamp_signup'] : $this->time->user_date($this->guests[$id]['timestamp_signup'], true);
		}

		public function get_group($id){
			return $this->guests[$id]['group'];
		}

		public function get_count($raidid, $status=0){
			$tmpcount	= 0;
			$tmpguests	= $this->guestsEvent[$raidid];
			if(isset($tmpguests) && is_array($tmpguests) && count($tmpguests) > 0){
				foreach($tmpguests as $guestdata){
					if($guestdata['status'] == $status){
						$tmpcount++;
					}
				}
			}
			return $tmpcount;
		}

	} //end class
} //end if class not exists
?>
