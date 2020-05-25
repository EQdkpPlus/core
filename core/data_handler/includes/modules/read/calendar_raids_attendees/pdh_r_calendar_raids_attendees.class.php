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

if (!class_exists('pdh_r_calendar_raids_attendees')){
	class pdh_r_calendar_raids_attendees extends pdh_r_generic{

		public $presets = array(
			'raidattendees_status'				=> array('html_status', array('%calevent_id%', '%user_id%'), array()),
			'raidcalstats_lastraid'				=> array('html_calstat_lastraid', array('%member_id%'), array()),
			'raidcalstats_raids_confirmed_90'	=> array('html_calstat_raids_confirmed', array('%member_id%', '90', '%with_twink%'), array()),
			'raidcalstats_raids_signedin_90'	=> array('html_calstat_raids_signedin', array('%member_id%', '90', '%with_twink%'), array()),
			'raidcalstats_raids_signedoff_90'	=> array('html_calstat_raids_signedoff', array('%member_id%', '90', '%with_twink%'), array()),
			'raidcalstats_raids_backup_90'		=> array('html_calstat_raids_backup', array('%member_id%', '90', '%with_twink%'), array()),
			'raidcalstats_raids_confirmed_60'	=> array('html_calstat_raids_confirmed', array('%member_id%', '60', '%with_twink%'), array()),
			'raidcalstats_raids_signedin_60'	=> array('html_calstat_raids_signedin', array('%member_id%', '60', '%with_twink%'), array()),
			'raidcalstats_raids_signedoff_60'	=> array('html_calstat_raids_signedoff', array('%member_id%', '60', '%with_twink%'), array()),
			'raidcalstats_raids_backup_60'		=> array('html_calstat_raids_backup', array('%member_id%', '60', '%with_twink%'), array()),
			'raidcalstats_raids_confirmed_30'	=> array('html_calstat_raids_confirmed', array('%member_id%', '30', '%with_twink%'), array()),
			'raidcalstats_raids_signedin_30'	=> array('html_calstat_raids_signedin', array('%member_id%', '30', '%with_twink%'), array()),
			'raidcalstats_raids_signedoff_30'	=> array('html_calstat_raids_signedoff', array('%member_id%', '30', '%with_twink%'), array()),
			'raidcalstats_raids_backup_30'		=> array('html_calstat_raids_backup', array('%member_id%', '30', '%with_twink%'), array()),

			'raidcalstats_raids_confirmed_fromto'	=> array('calstat_raids_confirmed_fromto', array('%member_id%', '%from%', '%to%', '%with_twink%'), array()),
			'raidcalstats_raids_signedin_fromto'	=> array('calstat_raids_signedin_fromto', array('%member_id%', '%from%', '%to%', '%with_twink%'), array()),
			'raidcalstats_raids_signedoff_fromto'	=> array('calstat_raids_signedoff_fromto', array('%member_id%', '%from%', '%to%', '%with_twink%'), array()),
			'raidcalstats_raids_backup_fromto'		=> array('calstat_raids_backup_fromto', array('%member_id%', '%from%', '%to%', '%with_twink%'), array()),
			'raidcalstats_raids_total_fromto'		=> array('calstat_raids_total_fromto', array('%member_id%', '%from%', '%to%', '%with_twink%'), array()),
		);

		private $attendees;
		private $attendees_fromto;

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
			$this->pdc->del('pdh_calendar_raids_table.lastraid');
			$this->pdc->del('pdh_calendar_raids_table.attendee_status');
			$this->pdc->del_prefix('pdh_calendar_raids_table.attendees_fromto');
			$this->attendees		= NULL;
			$this->lastraid			= NULL;
			$this->attendee_status	= NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->attendees		= $this->pdc->get('pdh_calendar_raids_table.attendees');
			$this->lastraid			= $this->pdc->get('pdh_calendar_raids_table.lastraid');
			$this->attendee_status	= $this->pdc->get('pdh_calendar_raids_table.attendee_status');

			if($this->attendees !== NULL && $this->lastraid !== NULL && $this->attendee_status !== NULL){
				return true;
			}

			// empty array as default
			$this->attendees		= array();
			$this->lastraid			= array();
			$this->attendee_status	= array();
			$raids_90d				= $this->pdh->get('calendar_events', 'amount_raids', array(90, false));
			$raids_60d				= $this->pdh->get('calendar_events', 'amount_raids', array(60, false));
			$raids_30d				= $this->pdh->get('calendar_events', 'amount_raids', array(30, false));

			$objQuery = $this->db->query('SELECT * FROM __calendar_raid_attendees');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					// fill the last attendee raid array
					$newdate	= $this->pdh->get('calendar_events', 'time_start', array($row['calendar_events_id']));
					$actdate	= (isset($this->lastraid[$row['member_id']])) ? $this->lastraid[$row['member_id']] : false;

					if((!$actdate || ($actdate && $newdate > $actdate) && $newdate < time())){
						$this->lastraid[$row['member_id']] = $newdate;
					}

					// attendee status array

					if(in_array($row['calendar_events_id'], array_keys($raids_90d))){
						if(in_array($row['calendar_events_id'], array_keys($raids_30d))){
							$days	= '30';
						}elseif(in_array($row['calendar_events_id'], array_keys($raids_60d))){
							$days	= '60';
						}else{
							$days	= '90';
						}
						if(isset($this->attendee_status[$row['member_id']][$row['signup_status']][$days])){
							$this->attendee_status[$row['member_id']][$row['signup_status']][$days]++;
						}else{
							$this->attendee_status[$row['member_id']][$row['signup_status']][$days] = 1;
						}
					}

					// fill the attendee array
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

				$this->pdc->put('pdh_calendar_raids_table.attendees', $this->attendees, NULL);
				$this->pdc->put('pdh_calendar_raids_table.lastraid', $this->lastraid, NULL);
				$this->pdc->put('pdh_calendar_raids_table.attendee_status', $this->attendee_status, NULL);
			}

			return true;
		}

		public function init_fromto($from, $to){
			$strTimeHash = md5($from.'.'.$to);

			$this->attendees_fromto[$strTimeHash] = $this->pdc->get('pdh_calendar_raids_table.attendees_fromto.'.$strTimeHash);

			if($this->attendees_fromto[$strTimeHash] !== NULL){
				return true;
			}

			// empty array as default
			$this->attendees_fromto	= array();
			$arrRaids = $this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to, false));

			$objQuery = $this->db->query('SELECT * FROM __calendar_raid_attendees');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					if(in_array($row['calendar_events_id'], array_keys($arrRaids))){
						// attendee status array
						if(isset($this->attendees_fromto[$strTimeHash][$row['member_id']][$row['signup_status']])){
							$this->attendees_fromto[$strTimeHash][$row['member_id']][$row['signup_status']]++;
						}else{
							$this->attendees_fromto[$strTimeHash][$row['member_id']][$row['signup_status']] = 1;
						}
					}
				}

				$this->pdc->put('pdh_calendar_raids_table.attendees_fromto.'.$strTimeHash, $this->attendees_fromto[$strTimeHash], NULL);
			}

			return true;
		}

		public function get_attendees($eventid=0, $raidgroup=0){
			if($eventid > 0){
				if(isset($this->attendees[$eventid])){
					$attendees	= $this->attendees[$eventid];
					if($raidgroup > 0){
						foreach($attendees as $attendeeID=>$attendeedata){
							if($attendeedata['raidgroup'] != $raidgroup){
								unset($attendees[$attendeeID]);
							}
						}
					}
					return $attendees;
				}else{
					return '';
				}
			}else{
				return $this->attendees;
			}
		}

		public function get_attendee_stats($eventid, $status, $raidgroup=0){
			$origStatus		= $status;
			$status			= (!is_array($status)) ? array($status) : $status;
			if ($origStatus == 4) $status = array(0,1,2,3);
			if(isset($this->attendees[$eventid]) && count($this->attendees[$eventid]) > 0){
				$temp_attendees_per_id	= $this->attendees[$eventid];

				// filter the attendees by group
				if($raidgroup > 0){
					$tmpattendee	= array();
					foreach($temp_attendees_per_id as $attendeeid=>$attendeedata){
						if($attendeedata['raidgroup'] != $raidgroup){
							unset($temp_attendees_per_id[$attendeeid]);
						}
					}
				}

				// now, do the rest
				$tmpattendee	= array();
				foreach($temp_attendees_per_id as $attendeeid=>$attendeedata){
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

		public function get_role($eventid, $memberid){
			return (isset($this->attendees[$eventid][$memberid])) ? $this->attendees[$eventid][$memberid]['member_role'] : '';
		}

		public function get_chars_with_wrong_role($eventid, $roles){
			$chars_out = array();
			if(isset($this->attendees[$eventid]) && is_array($this->attendees[$eventid])){
				foreach($this->attendees[$eventid] as $charID=>$charData){
					if(!in_array($charData['member_role'], $roles)){
						$chars_out[$charID] = $charData;
					}
				}
			}
			return $chars_out;
		}

		public function get_raidgroup($eventid, $memberid){
			return (isset($this->attendees[$eventid][$memberid])) ? $this->attendees[$eventid][$memberid]['raidgroup'] : 0;
		}

		public function get_has_already_signedin($eventid, $memberid){
			$userid		= $this->pdh->get('member', 'userid', array($memberid));
			$arrChars	= $this->pdh->get('member', 'connection_id', array($userid));

			foreach($arrChars as $tmpmemberid){
				if(isset($this->attendees[$eventid][$tmpmemberid])){
					if($memberid != $tmpmemberid){
						return $tmpmemberid;
					}
				}
			}
			return 0;
		}

		public function get_in_db($eventid, $memberid){
			$userid		= $this->pdh->get('member', 'userid', array($memberid));
			$arrChars	= $this->pdh->get('member', 'connection_id', array($userid));
			$inDB		= $this->db->prepare('SELECT * FROM __calendar_raid_attendees WHERE calendar_events_id=? AND member_id :in')->in($arrChars)->execute($eventid);
			return ($inDB->numRows > 0) ? true : false;
		}

		public function get_other_user_attendees($eventid, $memberid){
			$userid		= $this->pdh->get('member', 'userid', array($memberid));
			$arrChars	= $this->pdh->get('member', 'connection_id', array($userid));
			$objQuery	= $this->db->prepare('SELECT * FROM __calendar_raid_attendees WHERE calendar_events_id=? AND member_id :in')->in($arrChars)->execute($eventid);
			$arrOut = array();
			if($objQuery){
				while($arrRow = $objQuery->fetchAssoc()){
					if((int)$arrRow['member_id'] == $memberid) continue;
					$arrOut[(int)$arrRow['id']] = (int)$arrRow['id'];
				}
			}

			return $arrOut;
		}

		public function get_status_flag($status){
			switch($status){
				case 0:
					$flagcolor	= 'fa-star';
					$titletext	= $this->user->lang(array('raidevent_raid_status', 0));
					break;
				case 1:
					$flagcolor	= 'fa-user';
					$titletext	= $this->user->lang(array('raidevent_raid_status', 1));
					break;
				case 2:
					$flagcolor	= 'fa-star-o';
					$titletext	= $this->user->lang(array('raidevent_raid_status', 2));
					break;
				case 3:
					$flagcolor	= 'fa-star-half-o';
					$titletext	= $this->user->lang(array('raidevent_raid_status', 3));
					break;
				// TODO: check why we have a status 5
				case 5:
					$flagcolor	= 'fa-flag icon-color-blue';
					$titletext	= $this->user->lang(array('raidevent_raid_status', 5));
					break;
			}
			return '<i class="fa '.$flagcolor.' fa-lg raidcal-attendance-status-'.$status.'" title="'.$this->user->lang('raidevent_raid_status_title').$titletext.'"></i>';
		}

		public function get_html_status($eventid, $userid, $flag=true){
			$memberdata = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $userid));
			if(isset($memberdata['member_id']) && $memberdata['member_id'] > 0){
				$memberstatus = $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $memberdata['member_id']));
				if($memberstatus == 0 || $memberstatus == 1 || $memberstatus == 2 || $memberstatus == 3){
					return ($flag) ? $this->get_status_flag($memberstatus) : $memberstatus;
				}
			}
			return '';
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

		public function get_away_attendees($eventid){
			$arrAway		= array();
			$arrAttendees	= $this->get_attendee_stats($eventid, array(0,1));
			if(is_array($arrAttendees) && count($arrAttendees) > 0){
				foreach($arrAttendees as $memberid){
					if($this->get_attendee_awaymode($memberid, $eventid)){
						$arrAway[] = $memberid;
					}
				}
			}
			return $arrAway;
		}

		public function get_attendee_awaymode($id, $eventid=0){
			$userid		= $this->pdh->get('member', 'userid', array($id));
			return $this->get_user_awaymode($userid, $eventid);
		}

		public function get_user_awaymode($userid, $eventid=0){
			$awaymode_enabled	= $this->pdh->get('user', 'awaymode_enabled', array($userid, true));
			$awaymode_startdate	= $this->pdh->get('user', 'awaymode_startdate', array($userid));
			$awaymode_enddate	= $this->pdh->get('user', 'awaymode_enddate', array($userid));
			$event_starttime	= $this->time->removetimefromtimestamp(($eventid > 0) ? $this->pdh->get('calendar_events', 'date', array($eventid)) : $this->time->time);
			return ($awaymode_enabled && $event_starttime >= $awaymode_startdate && $event_starttime <= $awaymode_enddate) ? true : false;
		}

		/* -----------------------------------------------------------------------
		* Statistic stuff
		* - last raid members have attended to
		- Amount of raids attended in the last x days.
		- Amount of raids signed-up, but not attended.
		- Amount of raids signed-off
		* -----------------------------------------------------------------------*/

		public function get_calstat_lastraid($memberid){
			return (isset($this->lastraid[$memberid])) ? $this->lastraid[$memberid] : 0;
		}

		public function get_html_calstat_lastraid($memberid){
			$timestamp	= $this->get_calstat_lastraid($memberid);
			return ($timestamp > 0) ? $this->time->user_date($timestamp) : '--';
		}

		public function get_calstat_raids_status($memberid, $status=false, $days='90'){
			switch($days){
				case '30':
					$statsperdays	= $this->attendee_status[$memberid][$status]['30'];
				break;
				case '30':
					$statsperdays	= $this->attendee_status[$memberid][$status]['30'] + $this->attendee_status[$memberid][$status]['60'];
				break;
				case '90':
					$statsperdays	= $this->attendee_status[$memberid][$status]['30'] + $this->attendee_status[$memberid][$status]['60'] + $this->attendee_status[$memberid][$status]['90'];
				break;
			}
			return ($status !== false) ? $statsperdays : $this->attendee_status[$memberid];
		}

		public function get_html_calstat_raids_confirmed($memberid, $days, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status($intMemberID, 0, $days);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status($memberid, 0, $days);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids', array($days));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'%</span>';
		}

		public function get_html_calstat_raids_signedin($memberid, $days, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status($intMemberID, 1, $days);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status($memberid, 1, $days);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids', array($days));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'%</span>';
		}

		public function get_html_calstat_raids_signedoff($memberid, $days, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status($intMemberID, 2, $days);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status($memberid, 2, $days);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids', array($days));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'%</span>';
		}

		public function get_html_calstat_raids_backup($memberid, $days, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status($intMemberID, 3, $days);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status($memberid, 3, $days);
			}

			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids', array($days));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'%</span>';
		}

		/* -----------------------------------------------------------------------
		 * From To Attendance
		 * -----------------------------------------------------------------------*/
		public function get_calstat_raids_confirmed_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 0, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 0, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return $percentage;
		}

		public function get_html_calstat_raids_confirmed_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 0, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 0, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'% ('.$number_of_raids_att.'/'.$number_of_raids_all.')</span>';
		}

		public function get_calstat_raids_signedin_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 1, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 1, $from, $to);
			}

			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return $percentage;
		}

		public function get_html_calstat_raids_signedin_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 1, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 1, $from, $to);
			}

			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'% ('.$number_of_raids_att.'/'.$number_of_raids_all.')</span>';
		}

		public function get_calstat_raids_signedoff_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 2, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 2, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return $percentage;
		}

		public function get_html_calstat_raids_signedoff_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 2, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 2, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'% ('.$number_of_raids_att.'/'.$number_of_raids_all.')</span>';
		}

		public function get_calstat_raids_backup_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 3, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 3, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return $percentage;
		}

		public function get_html_calstat_raids_backup_fromto ($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_status_fromto($intMemberID, 3, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_status_fromto($memberid, 3, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'% ('.$number_of_raids_att.'/'.$number_of_raids_all.')</span>';
		}

		public function get_calstat_raids_total_fromto($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_totalstatus_fromto($intMemberID, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_totalstatus_fromto($memberid, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;

			return $percentage;
		}

		public function get_html_calstat_raids_total_fromto($memberid, $from, $to, $withTwinks=false){
			if($withTwinks){
				$arrOthers = $this->pdh->get('member', 'other_members', array($memberid));
				$arrOthers[] = $memberid;
				$arrOthers = array_unique($arrOthers);
				$number_of_raids_att = 0;
				foreach($arrOthers as $intMemberID){
					$number_of_raids_att += (int)$this->get_calstat_raids_totalstatus_fromto($intMemberID, $from, $to);
				}
			} else {
				$number_of_raids_att	= (int)$this->get_calstat_raids_totalstatus_fromto($memberid, $from, $to);
			}
			$number_of_raids_all	= (int)$this->pdh->get('calendar_events', 'amount_raids_fromto', array($from, $to));
			$percentage				= ($number_of_raids_all > 0) ? round(($number_of_raids_att/$number_of_raids_all)*100, 1) : 0;
			return '<span class="' . color_item($percentage, true) . '">'.$percentage.'% ('.$number_of_raids_att.'/'.$number_of_raids_all.')</span>';
		}

		public function get_calstat_raids_status_fromto($memberid, $status, $from, $to){
			$strTimeHash = md5($from.'.'.$to);
			$statsperdays = 0;
			if(!isset($this->attendees_fromto[$strTimeHash])) $this->init_fromto($from, $to);
			if(isset($this->attendees_fromto[$strTimeHash][$memberid]) && isset($this->attendees_fromto[$strTimeHash][$memberid][$status]) )
				$statsperdays = $this->attendees_fromto[$strTimeHash][$memberid][$status];
			return $statsperdays;
		}

		public function get_calstat_raids_totalstatus_fromto($memberid, $from, $to){
			$strTimeHash = md5($from.'.'.$to);
			$statsperdays = 0;
			if(!isset($this->attendees_fromto[$strTimeHash])) $this->init_fromto($from, $to);

			if(isset($this->attendees_fromto[$strTimeHash][$memberid])){
				foreach($this->attendees_fromto[$strTimeHash][$memberid] as $stats){
					$statsperdays += $stats;
				}
			}

			return $statsperdays;
		}

		public function get_twinks_with_highest_attendance($memberid){
			$other_chars		= $this->pdh->get('member', 'other_members', array($memberid));
			if(is_array($other_chars) && count($other_chars) > 0){
				// get the chars & add the own ID
				$mychars			= array_merge($other_chars, array($memberid));

				// add all attendances of members to an array
				$sttendance_chars = array();
				foreach($mychars as $charID){
					$sttendance_chars[$charID] = $this->get_calstat_raids_total_fromto($memberid, ($this->time->time - (90*24*3600)), $this->time->time, false);
				}
				// return the array key (memberID) of the highes attendance char
				return array_search(max($sttendance_chars), $sttendance_chars);
			}
			return $memberid;
		}

		/* -----------------------------------------------------------------------
		* Tools
		* -----------------------------------------------------------------------*/

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
