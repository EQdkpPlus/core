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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_member_attendance" ) ) {
	class pdh_r_member_attendance extends pdh_r_generic{

		public $default_lang = 'english';

		public $member_attendance;
		public $member_attendance_fromto;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update'
		);

		public $presets = array(
			'attendance_30' => array('attendance', array('%member_id%', '%dkp_id%', 30, '%with_twink%'), array(30)),
			'attendance_60' => array('attendance', array('%member_id%', '%dkp_id%', 60, '%with_twink%'), array(60)),
			'attendance_90' => array('attendance', array('%member_id%', '%dkp_id%', 90, '%with_twink%'), array(90)),
			'attendance_lt' => array('attendance', array('%member_id%', '%dkp_id%', 'LT', '%with_twink%'), array('LT')),
			'attendance_30_all' => array('attendance_all', array('%member_id%', '%ALL_IDS%', 30, '%with_twink%'), array('%ALL_IDS%', 30)),
			'attendance_60_all' => array('attendance_all', array('%member_id%', '%ALL_IDS%', 60, '%with_twink%'), array('%ALL_IDS%', 60)),
			'attendance_90_all' => array('attendance_all', array('%member_id%', '%ALL_IDS%', 90, '%with_twink%'), array('%ALL_IDS%', 90)),
			'attendance_lt_all' => array('attendance_all', array('%member_id%', '%ALL_IDS%', 'LT', '%with_twink%'), array('%ALL_IDS%', 'LT')),
			
			'attendance_fromto_all' => array('attendance_fromto_all', array('%member_id%', '%ALL_IDS%', '%from%', '%to%', '%with_twink%'), array('%ALL_IDS%')),
		);

		public $detail_twink = array(
			'attendance' => 'summed_up',
			'attendance_all' => 'summed_up',
		);

		private $twink2main = array();

		public function reset(){
			//we'll have to get all entries ourselfs!!
			$this->pdc->del_prefix('pdh_member_attendance');
			$this->member_attendance = NULL;
		}

		public function init(){
		}

		public function init_attendance($time_period, $mdkp_id){
			if($mdkp_id == '') {
				return false;
			}
			//cached data not outdated?
			$this->member_attendance[$time_period][$mdkp_id] = $this->pdc->get('pdh_member_attendance_'.$time_period.'_'.$mdkp_id);
			if($this->member_attendance[$time_period][$mdkp_id] != null || is_array($this->member_attendance[$time_period][$mdkp_id])){
				return true;
			}
			$this->twink2main = $this->pdh->aget('member', 'mainid', 0, array($this->pdh->get('member', 'id_list')));
			//initialise the basic array
			$this->member_attendance[$time_period] = array();
			foreach($this->pdh->get('member', 'id_list') as $member_id){
				$this->member_attendance[$time_period][$mdkp_id]['members'][$member_id]['count'] = 0;
				$this->member_attendance[$time_period][$mdkp_id]['members'][$member_id]['attended'] = 0;
				$this->member_attendance[$time_period][$mdkp_id]['mains'][$this->twink2main[$member_id]]['attended'] = 0;
				$this->member_attendance[$time_period][$mdkp_id]['mains'][$this->twink2main[$member_id]]['count'] = 0;
			}

			$first_date = 0;
			if($time_period != 'LT') {
				$first_date = $this->time->time-($time_period*86400);
				$first_date -= 3600*$this->time->date('H')+60*$this->time->date('i')+$this->time->date('s');
			}
			
			//get raids
			$raid_ids = $this->pdh->get('raid', 'id_list');

			//create array with all first_raid dates
			$first_raids = array();
			$temp = $this->pdh->aget('member_dates', 'first_raid', 0, array($this->pdh->get('member', 'id_list'), $mdkp_id));
			$member_first_raid[$mdkp_id] = $temp;
			if($time_period == 'LT') {
				$temp_first_raids = array_flip($temp);
				foreach($temp_first_raids as $first_raid => $nothing) {
					$first_raids[$first_raid] = array($mdkp_id => 0);
				}
			} else {
				$first_raids[$first_date] = array($mdkp_id => 0);
			}
			unset($temp);
			foreach($raid_ids as $raid_id){
				//raid not relevant for this attendance calculation
				$date = $this->pdh->get('raid', 'date', array($raid_id));
				if($date <= $first_date) continue;
				$eventid = $this->pdh->get('raid', 'event', array($raid_id));
				$mdkpids = $this->pdh->get('multidkp', 'mdkpids4eventid', array($eventid, false));
				if(!in_array($mdkp_id, $mdkpids)) continue;

				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				$mains = array();
				//increment attendence counter
				if(is_array($attendees)) {
					foreach($attendees as $attendee_id){
						$this->member_attendance[$time_period][$mdkp_id]['members'][$attendee_id]['attended']++;
						$mains[$this->twink2main[$attendee_id]] = true;
					}
					foreach($mains as $main_id => $tru) {
						$this->member_attendance[$time_period][$mdkp_id]['mains'][$main_id]['attended']++;
					}
				}
				//increment total counter
				foreach($first_raids as $first_raid => $num){
					if($date >= $first_raid) {
						$first_raids[$first_raid][$mdkp_id]++;
					}
				}
			}
			//connect total-raid counts to member_id
			$twink_first_dates = array();
			foreach($member_first_raid as $mdkp_id => $mfirst_raid) {
				foreach($mfirst_raid as $member_id => $first_raid) {
					if($time_period != 'LT') {
						$first_raid = $first_date;
					}
					$this->member_attendance[$time_period][$mdkp_id]['members'][$member_id]['count'] = $first_raids[$first_raid][$mdkp_id];
					//search for earliest date
					if(!isset($twink_first_dates[$this->twink2main[$member_id]][$mdkp_id]) OR $first_raid < $twink_first_dates[$this->twink2main[$member_id]][$mdkp_id]) {
						$twink_first_dates[$this->twink2main[$member_id]][$mdkp_id] = $first_raid;
						$this->member_attendance[$time_period][$mdkp_id]['mains'][$this->twink2main[$member_id]]['count'] = $first_raids[$first_raid][$mdkp_id];
					}
				}
			}
			//cache it and let it expire at midnight
			$stm = 86400-($this->time->time)%86400;
			$this->pdc->put('pdh_member_attendance_'.$time_period.'_'.$mdkp_id, $this->member_attendance[$time_period][$mdkp_id], $stm);
		}

		public function get_attendance($member_id, $multidkp_id, $time_period, $with_twinks=true, $count=false){
			if(!isset($this->member_attendance[$time_period][$multidkp_id])){
				$this->init_attendance($time_period, $multidkp_id);
			}
			$mainid = $this->pdh->get('member', 'mainid', array($member_id));
			if($with_twinks AND $this->member_attendance[$time_period][$multidkp_id]['mains'][$mainid]) {
				$with_twinks = 'mains';
				$member_id = $mainid;
			} else {
				$with_twinks = 'members';
			}
			$member_raidcount = $this->member_attendance[$time_period][$multidkp_id][$with_twinks][$member_id]['attended'];
			$total_raidcount = $this->member_attendance[$time_period][$multidkp_id][$with_twinks][$member_id]['count'];
			if ($count) {
				$return['total_raidcount'] = $total_raidcount ;
				$return['member_raidcount'] = $member_raidcount;
				$return['member_attendance'] = ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
				return $return;
			}
			return ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
		}
		
		public function get_attendance_all($member_id, $multidkp_id, $time_period, $with_twinks=true, $count=false){
			return $this->get_attendance($member_id, $multidkp_id, $time_period, $with_twinks, $count);
		}
		
		public function get_html_attendance_all($member_id, $multidkp_id, $time_period, $with_twinks=true, $count=false){
			return $this->get_html_attendance($member_id, $multidkp_id, $time_period, $with_twinks, $count);
		}

		public function get_html_attendance($member_id, $multidkp_id, $time_period, $with_twinks=true){
			if(!isset($this->member_attendance[$time_period][$multidkp_id])){
				$this->init_attendance($time_period, $multidkp_id);
			}
			$mainid = $this->pdh->get('member', 'mainid', array($member_id));
			if($with_twinks AND $this->member_attendance[$time_period][$multidkp_id]['mains'][$mainid]) {
				$with_twinks = 'mains';
				$member_id = $mainid;
			} else {
				$with_twinks = 'members';
			}

			$member_raidcount = $this->member_attendance[$time_period][$multidkp_id][$with_twinks][$member_id]['attended'];
			$total_raidcount = $this->member_attendance[$time_period][$multidkp_id][$with_twinks][$member_id]['count'];
			$percentage = ( $total_raidcount > 0 ) ? round(($member_raidcount/$total_raidcount) * 100) : 0;

			return '<span class="'.color_item($percentage, true).'">'.$percentage.'% ('.$member_raidcount.'/'.$total_raidcount.')</span>';
		}

		public function get_caption_attendance($period){
			if($period == 'LT'){
				return $this->pdh->get_lang('member_attendance', 'lifetime');
			}else{
				return sprintf($this->pdh->get_lang('member_attendance', 'attendance'), $period);
			}
		}
		
		public function get_caption_attendance_all($mdkpid, $period){
			if($period == 'LT'){
				return $this->pdh->get('multidkp', 'name', array($mdkpid)).' '.$this->pdh->get_lang('member_attendance', 'lifetime');
			}else{
				return $this->pdh->get('multidkp', 'name', array($mdkpid)).' '.sprintf($this->pdh->get_lang('member_attendance', 'attendance'), $period);
			}
		}
		
		
		//Attendance From To
		
		public function get_attendance_fromto_all($member_id, $multidkp_id, $from, $to, $with_twinks=true, $count=false){
			$strTimeHash = md5($from.'.'.$to);
			
			if(!isset($this->member_attendance_fromto[$strTimeHash][$multidkp_id])){
				$this->init_attendance_fromto($from, $to, $multidkp_id);
			}
			$mainid = $this->pdh->get('member', 'mainid', array($member_id));
			if($with_twinks AND $this->member_attendance_fromto[$strTimeHash][$multidkp_id]['mains'][$mainid]) {
				$with_twinks = 'mains';
				$member_id = $mainid;
			} else {
				$with_twinks = 'members';
			}
			$member_raidcount = $this->member_attendance_fromto[$strTimeHash][$multidkp_id][$with_twinks][$member_id]['attended'];
			$total_raidcount = $this->member_attendance_fromto[$strTimeHash][$multidkp_id][$with_twinks][$member_id]['count'];
			if ($count) {
				$return['total_raidcount'] = $total_raidcount ;
				$return['member_raidcount'] = $member_raidcount;
				$return['member_attendance'] = ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
				return $return;
			}
			return ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
			
		}
		
		public function get_html_attendance_fromto_all($member_id, $multidkp_id, $from, $to, $with_twinks=true, $count=false){
			$strTimeHash = md5($from.'.'.$to);
			
			if(!isset($this->member_attendance_fromto[$strTimeHash][$multidkp_id])){
				$this->init_attendance_fromto($from, $to, $multidkp_id);
			}
			$mainid = $this->pdh->get('member', 'mainid', array($member_id));
			if($with_twinks AND $this->member_attendance_fromto[$strTimeHash][$multidkp_id]['mains'][$mainid]) {
				$with_twinks = 'mains';
				$member_id = $mainid;
			} else {
				$with_twinks = 'members';
			}
			
			$member_raidcount = $this->member_attendance_fromto[$strTimeHash][$multidkp_id][$with_twinks][$member_id]['attended'];
			$total_raidcount = $this->member_attendance_fromto[$strTimeHash][$multidkp_id][$with_twinks][$member_id]['count'];
			$percentage = ( $total_raidcount > 0 ) ? round(($member_raidcount/$total_raidcount) * 100) : 0;
			
			return '<span class="'.color_item($percentage, true).'">'.$percentage.'% ('.$member_raidcount.'/'.$total_raidcount.')</span>';
			
		}
		
		public function get_caption_attendance_fromto_all($mdkpid){
			return $this->pdh->get('multidkp', 'name', array($mdkpid)).' '.$this->pdh->get_lang('member_attendance', 'attendance_fromto');
		}
		
		public function init_attendance_fromto($from, $to, $mdkp_id){
			if($mdkp_id == '') {
				return false;
			}
			$strTimeHash = md5($from.'.'.$to);
			
			//cached data not outdated?
			$this->member_attendance_fromto[$strTimeHash][$mdkp_id] = $this->pdc->get('pdh_member_attendance_fromto_'.$strTimeHash.'_'.$mdkp_id);
			if($this->member_attendance_fromto[$strTimeHash][$mdkp_id] != null || is_array($this->member_attendance_fromto[$strTimeHash][$mdkp_id])){
				return true;
			}
			$this->twink2main = $this->pdh->aget('member', 'mainid', 0, array($this->pdh->get('member', 'id_list')));
			//initialise the basic array
			$this->member_attendance_fromto[$strTimeHash] = array();
			foreach($this->pdh->get('member', 'id_list') as $member_id){
				$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['members'][$member_id]['count'] = 0;
				$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['members'][$member_id]['attended'] = 0;
				$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['mains'][$this->twink2main[$member_id]]['attended'] = 0;
				$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['mains'][$this->twink2main[$member_id]]['count'] = 0;
			}
			
			$first_date = $from;
			$last_date = $to;

			//get raids
			$raid_ids = $this->pdh->get('raid', 'id_list');
		
			//create array with all first_raid dates
			$first_raids = array();
			$temp = $this->pdh->aget('member_dates', 'first_raid', 0, array($this->pdh->get('member', 'id_list'), $mdkp_id));
			$member_first_raid[$mdkp_id] = $temp;

			$temp_first_raids = array_flip($temp);
			foreach($temp_first_raids as $first_raid => $nothing) {
				$first_raids[$first_raid] = array($mdkp_id => 0);
			}

			unset($temp);
			foreach($raid_ids as $raid_id){
				//raid not relevant for this attendance calculation
				$date = $this->pdh->get('raid', 'date', array($raid_id));
				if($date <= $first_date) continue;
				if($date > $last_date) continue;
				$eventid = $this->pdh->get('raid', 'event', array($raid_id));
				$mdkpids = $this->pdh->get('multidkp', 'mdkpids4eventid', array($eventid, false));
				if(!in_array($mdkp_id, $mdkpids)) continue;
		
				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				$mains = array();
				//increment attendence counter
				if(is_array($attendees)) {
					foreach($attendees as $attendee_id){
						if(!isset($this->member_attendance_fromto[$strTimeHash][$mdkp_id]['members'][$attendee_id])) continue;
						$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['members'][$attendee_id]['attended']++;
						$mains[$this->twink2main[$attendee_id]] = true;
					}
					foreach($mains as $main_id => $tru) {
						$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['mains'][$main_id]['attended']++;
					}
				}
				//increment total counter
				foreach($first_raids as $first_raid => $num){
					if($date >= $first_raid) {
						$first_raids[$first_raid][$mdkp_id]++;
					}
				}
			}
			//connect total-raid counts to member_id
			$twink_first_dates = array();
			foreach($member_first_raid as $mdkp_id => $mfirst_raid) {
				foreach($mfirst_raid as $member_id => $first_raid) {
					$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['members'][$member_id]['count'] = $first_raids[$first_raid][$mdkp_id];
					//search for earliest date
					if(!isset($twink_first_dates[$this->twink2main[$member_id]][$mdkp_id]) OR $first_raid < $twink_first_dates[$this->twink2main[$member_id]][$mdkp_id]) {
						$twink_first_dates[$this->twink2main[$member_id]][$mdkp_id] = $first_raid;
						$this->member_attendance_fromto[$strTimeHash][$mdkp_id]['mains'][$this->twink2main[$member_id]]['count'] = $first_raids[$first_raid][$mdkp_id];
					}
				}
			}
			//cache it and let it expire at midnight
			$stm = 86400-($this->time->time)%86400;
			$this->pdc->put('pdh_member_attendance_fromto_'.$strTimeHash.'_'.$mdkp_id, $this->member_attendance_fromto[$strTimeHash][$mdkp_id], $stm);
		}
		
	}//end class
}//end if
?>