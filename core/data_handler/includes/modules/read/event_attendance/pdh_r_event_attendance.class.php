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

if ( !class_exists( "pdh_r_event_attendance" ) ) {
	class pdh_r_event_attendance extends pdh_r_generic{

		public $default_lang = 'english';

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update'
		);

		public $presets = array(
			'event_attendance'	=> array('attendance', array('%member_id%', '%event_id%', 'LT', '%with_twinks%'), array('LT')),
		);

		public $detail_twink = array(
			'attendance' => 'summed_up',
		);

		private $attendance = array();
		private $counts = array();

		public function reset(){
			$this->pdc->del_prefix('pdh_event_attendance');
			$this->pdc->del('pdh_event_att_count');
			$this->attendance = NULL;
			$this->counts = NULL;
		}

		public function init() {
			$this->counts = $this->pdc->get('pdh_event_att_count');
		}

		public function init_attendance($time_period, $member_id){
			if(empty($member_id)) return false;

			//cached data not outdated?
			$this->attendance[$time_period][$member_id] = $this->pdc->get('pdh_event_attendance_'.$time_period.'_'.$member_id);
			if($this->attendance[$time_period][$member_id] != null || is_array($this->attendance[$time_period][$member_id])) {
				return true;
			}
			$main_id = $this->pdh->get('member', 'mainid', array($member_id));
			$all_members = $this->pdh->get('member', 'other_members', array($member_id));
			$all_members[] = $member_id;

			if($time_period != 'LT') {
				//midnight of x days before
				$first_date = $this->time->time-($time_period*86400);
				$first_date -= 3600*$this->time->date('H')+60*$this->time->date('i')+$this->time->date('s');
				$first_date = array('member' => $first_date, 'main' => $first_date);
			} else {
				$first_date['member'] = $this->pdh->get('member_dates', 'first_raid', array($member_id, null, false));
				$first_date['main'] = $this->pdh->get('member_dates', 'first_raid', array($main_id));
			}
			//count total per event
			if(!isset($this->counts[$first_date['member']]) || !isset($this->counts[$first_date['main']])) {
				$raids = $this->pdh->maget('raid', array('date', 'event'), 0, array($this->pdh->get('raid', 'id_list')));
				foreach($raids as $raid_id => $raid) {
					$date = $raid['date'];
					$event_id = $raid['event'];
						if($date >= $first_date['member']) {
							if(!isset($this->counts[$first_date['member']][$event_id])) $this->counts[$first_date['member']][$event_id] = 0;
							$this->counts[$first_date['member']][$event_id]++;
						}
						if($date >= $first_date['main'] && $first_date['main'] != $first_date['member']) {
						if(!isset($this->counts[$first_date['main']][$event_id])) $this->counts[$first_date['main']][$event_id] = 0;
						$this->counts[$first_date['main']][$event_id]++;
					}
				}
				$this->pdc->put('pdh_event_att_count', $this->counts);
			}
			//get raids
			$raid_ids = array();
			foreach($all_members as $mem_id) {
				$raid_ids = array_merge($raid_ids, $this->pdh->get('raid', 'raidids4memberid', array($mem_id)));
			}
			$raid_ids = array_unique($raid_ids);
			$raids = $this->pdh->maget('raid', array('date', 'event'), 0, array($raid_ids));
			foreach($raids as $raid_id => $raid){
				$date = $raid['date'];
				$event_id = $raid['event'];
				//raid not relevant for this attendance calculation
					if($date < $first_date['main']) continue;
				if(!isset($this->attendance[$time_period][$member_id]['main'][$event_id]['attended'])) $this->attendance[$time_period][$member_id]['main'][$event_id]['attended'] = 0;
				$this->attendance[$time_period][$member_id]['main'][$event_id]['attended']++;
				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				if(in_array($member_id, $attendees)) {
					if(!isset($this->attendance[$time_period][$member_id]['member'][$event_id]['attended'])) $this->attendance[$time_period][$member_id]['member'][$event_id]['attended'] = 0;
					$this->attendance[$time_period][$member_id]['member'][$event_id]['attended']++;
				}
			}
			//cache it and let it expire at midnight
			$stm = 86400-$this->time->time%86400;
			$this->pdc->put('pdh_event_attendance_'.$time_period.'_'.$member_id, $this->attendance[$time_period][$member_id], $stm);
		}

		public function get_attendance($member_id, $event_id, $time_period, $with_twinks=true, $count=false){
			if(!isset($this->attendance[$time_period][$member_id])){
				$this->init_attendance($time_period, $member_id);
			}
			$mainid = $this->pdh->get('member', 'mainid', array($member_id));
			if($with_twinks && !empty($this->attendance[$time_period][$member_id]['main'][$event_id])) {
				$with_twinks = 'main';
				$member_id = $mainid;
				$first_date = $this->pdh->get('member_dates', 'first_raid', array($member_id));
			} else {
				$with_twinks = 'member';
				$first_date = $this->pdh->get('member_dates', 'first_raid', array($member_id, null, false));
			}
			if(empty($this->attendance[$time_period][$member_id][$with_twinks][$event_id]['attended'])) $this->attendance[$time_period][$member_id][$with_twinks][$event_id]['attended'] = 0;
			$member_raidcount = $this->attendance[$time_period][$member_id][$with_twinks][$event_id]['attended'];
			if(empty($this->counts[$first_date][$event_id])) $this->counts[$first_date][$event_id] = 0;
			$total_raidcount = $this->counts[$first_date][$event_id];
			if ($count) {
				$return['total_raidcount'] = $total_raidcount ;
				$return['member_raidcount'] = $member_raidcount;
				$return['member_attendance'] = ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
				return $return;
			}
			return ($total_raidcount > 0) ? $member_raidcount/$total_raidcount : '0';
		}

		public function get_html_attendance($member_id, $event_id, $time_period, $with_twinks=true){
			$data = $this->get_attendance($member_id, $event_id, $time_period, $with_twinks, true);
			$percent = round($data['member_attendance']*100);
			return $this->jquery->progressbar('evatt_'.$member_id.'_'.$event_id, $percent, array('text' => '%percentage% ('.$data['member_raidcount'].'/'.$data['total_raidcount'].')', 'directout' => true));
		}

		public function get_caption_attendance($period){
			if($period == 'LT'){
				return $this->pdh->get_lang('event_attendance', 'lifetime');
			}else{
				return sprintf($this->pdh->get_lang('event_attendance', 'attendance'), $period);
			}
		}
	}//end class
}//end if
?>