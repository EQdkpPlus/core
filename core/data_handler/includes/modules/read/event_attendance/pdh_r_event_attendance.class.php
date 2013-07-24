<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_event_attendance" ) ) {
	class pdh_r_event_attendance extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'jquery', 'time'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
			'event_attendance'	=> array('attendance', array('%member_id%', '%event_id%', 'LT'), array('LT')),
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
			if($this->attendance[$time_period][$member_id] != null || is_array($this->attendance[$time_period][$member_id])){
				return true;
			}
			$main_id = $this->pdh->get('member', 'mainid', array($member_id));
			$all_members = $this->pdh->get('member', 'other_members', array($member_id));
			$all_members[] = $member_id;

			if($time_period != 'LT') {
				//midnight of x days before
				$first_date = $this->time->time - ($time_period*86400);
				$first_date -= $first_date%86400;
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
			$this->pdc->put('pdh_member_attendance_'.$time_period.'_'.$member_id, $this->attendance[$time_period][$member_id], $stm);
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
			$percent = runden($data['member_attendance']*100);
			return $this->jquery->ProgressBar('evatt_'.$member_id.'_'.$event_id, $percent, $percent.'% ('.$data['member_raidcount'].'/'.$data['total_raidcount'].')', 'center', true);
			return '<span class="'.color_item($percent*100, true).'">'.$percent*100 .'% ('.$data['member_raidcount'].'/'.$data['total_raidcount'].')</span>';
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_event_attendance', pdh_r_event_attendance::__shortcuts());
?>