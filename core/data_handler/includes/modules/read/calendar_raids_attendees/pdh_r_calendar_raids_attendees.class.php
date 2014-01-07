<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_calendar_raids_attendees')){
	class pdh_r_calendar_raids_attendees extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user', 'pdh'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $presets = array(
			'raidattendees_status'	=> array('html_status', array('%calevent_id%', '%user_id%'), array()),
		);

		private $attendees;
		public $hooks = array(
			'calendar_raid_attendees_update',
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
			$this->pdc->del('pdh_calendar_raids_table.attendees');
			$this->attendees = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->attendees		= $this->pdc->get('pdh_calendar_raids_table.attendees');
			if($this->attendees !== NULL){
				return true;
			}

			// empty array as default
			$this->attendees	= array();
			$myresult		= $this->db->query('SELECT * FROM __calendar_raid_attendees');
			while ($row = $this->db->fetch_record($myresult)){
				$this->attendees[$row['calendar_events_id']][$row['member_id']] = array(
					'member_role'				=> $row['member_role'],
					'signup_status'				=> $row['signup_status'],
					'status_changedby'			=> $row['status_changedby'],
					'note'						=> $row['note'],
					'timestamp_signup'			=> $row['timestamp_signup'],
					'timestamp_change'			=> $row['timestamp_change'],
					'raidgroup'					=> $row['raidgroup'],
					'random_value'				=> $row['random_value'],
					'signedbyadmin'				=> $row['signedbyadmin'],
				);
			}
			if($myresult) $this->pdc->put('pdh_calendar_raids_table.attendees', $this->attendees, NULL);
			return true;
		}

		public function get_attendees($eventid=''){
			return ($eventid) ? ((isset($this->attendees[$eventid])) ? $this->attendees[$eventid] : '') : $this->attendees;
		}

		public function get_attendee_stats($eventid, $status){
			$origStatus = $status;
			$status = (!is_array($status)) ? array($status) : $status;
			$tmpattendee = array();
			if ($origStatus == 4) $status = array(0,1,2,3);
			if(isset($this->attendees[$eventid]) && count($this->attendees[$eventid]) > 0){
				foreach($this->attendees[$eventid] as $attendeeid=>$attendeedata){
					if(in_array($attendeedata['signup_status'], $status)){
						$tmpattendee[]	= $attendeeid;
					}
				}
				if ($origStatus == 4){
					return array_diff($this->pdh->get('member', 'id_list', array()), $tmpattendee);
				} else {
					return $tmpattendee;
				}
				
			}else{
				if ($origStatus == 4){
					return $this->pdh->get('member', 'id_list', array());
				} else {
					return array();
				}
			}
		}

		public function get_attendee_users($eventid){
			if(isset($this->attendees[$eventid]) && count($this->attendees[$eventid]) > 0){
				foreach($this->attendees[$eventid] as $attendeeid=>$attendeedata){
					$tmpuserid[] = $this->pdh->get('member', 'userid', array($attendeeid));
				}
				return $tmpuserid;
			}else{
				return array();
			}
		}

		public function get_myattendees($eventid, $userid){
			$memberlist = $this->pdh->get('member', 'connection_id', array($userid));
			if (is_array($memberlist)){
				foreach($memberlist as $memberid){
					if(isset($this->attendees[$eventid][$memberid])){
						$tmparray['member_id'] = $memberid;
						return array_merge($this->attendees[$eventid][$memberid], $tmparray);
					}
				}
			}
		}

		public function get_status($eventid, $memberid){
			return (isset($this->attendees[$eventid][$memberid])) ? $this->attendees[$eventid][$memberid]['signup_status'] : '4';
		}

		public function get_in_db($eventid, $memberid){
			return (isset($this->attendees[$eventid][$memberid])) ? true : false;
		}

		public function get_html_status($eventid, $userid){
			$memberdata = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $userid));
			if($memberdata['member_id'] > 0){
				$memberstatus = $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $memberdata['member_id']));
				if($memberstatus == 0 || $memberstatus == 1 || $memberstatus == 2 || $memberstatus == 3){
					return '<img src="'.$this->root_path.'images/calendar/status/status'.$memberstatus.'.png" alt="'.$this->user->lang(array('raidevent_raid_status', $memberstatus)).'" title="'.$this->user->lang(array('raidevent_raid_status', $memberstatus)).'" />';
				}
			}
		}

		public function get_note($raidid, $memberid){
			return $this->attendees[$raidid][$memberid]['note'];
		}

		public function get_statuscount($id){
			$outarray = array();
			if(isset($this->attendees[$id]) && is_array($this->attendees[$id])){
				foreach($this->attendees[$id] as $memberid=>$row){
					$outarray[$memberid] = $row['attendees_subscribed'];
				}
			}

			$count_status = 0;
			if(is_array($outarray) && count($outarray) != 0){
				$count_status =  array(
					$this->count_repeat_values('0', $outarray),
					$this->count_repeat_values('1', $outarray),
					$this->count_repeat_values('2', $outarray),
					$this->count_repeat_values('3', $outarray)
				);
			}
			return $count_status;
		}

		public function get_count($id){
			if(is_array($this->attendees[$id])){
				return count($this->attendees[$id]);
			}else{
				return 0;
			}
		}

		private function count_repeat_values($needle, $array){
			if(is_array($array)){
				foreach($array as $key=>$value){
					if($value == $needle){
						$needle_array[] = $key;
					}
				}
			}
			return count($needle_array);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_calendar_raids_attendees', pdh_r_calendar_raids_attendees::__shortcuts());
?>