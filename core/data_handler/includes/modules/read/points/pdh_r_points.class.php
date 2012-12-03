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

if ( !class_exists( "pdh_r_points" ) ) {
	class pdh_r_points extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'pdh', 'html', 'user');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';

		public $points;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'multidkp_update',
			'itempool_update'
		);

		public $presets = array(
			'earned' => array('earned', array('%member_id%', '%dkp_id%', 0, '%with_twink%'), array('%dkp_id%')),
			'spent' => array('spent', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array('%dkp_id%')),
			'adjustment' => array('adjustment', array('%member_id%', '%dkp_id%', 0, '%with_twink%'), array('%dkp_id%')),
			'current' => array('current', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array('%dkp_id%', false, true)),
			'all_current' => array('current', array('%member_id%', '%ALL_IDS%', 0, 0, '%with_twink%'), array('%ALL_IDS%', true, true)),
		);

		public $detail_twink = array(
			'earned' => 'summed_up',
			'spent' => 'summed_up',
			'adjustment' => 'summed_up',
			'current' => 'summed_up',
		);

		public function reset(){
			$this->pdc->del('pdh_points_table');
			$this->points = NULL;
		}

		public function init() {
			//cached data not outdated?
			$this->points = $this->pdc->get('pdh_points_table');
			if($this->points !== NULL){
				return true;
			}
			$this->points = array();
			$mdkpids = $this->pdh->maget('multidkp', array('event_ids', 'itempool_ids'), 0, array($this->pdh->get('multidkp', 'id_list')));
			$raid2event = array();
			foreach($mdkpids as $dkp_id => $evip) {
				if((!is_array($evip['event_ids']) || count($evip['event_ids']) < 1) && (!is_array($evip['itempool_ids']) || count($evip['itempool_ids']) < 1)) continue;
				//earned
				if(is_array($evip['event_ids'])) {
					foreach($evip['event_ids'] as $event_id) {
						$raid_ids = $this->pdh->get('raid', 'raidids4eventid', array($event_id));
						foreach($raid_ids as $raid_id) {
							$raid2event[$raid_id] = $event_id;
							$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
							if( !is_array($attendees) ) continue;
							$value = $this->pdh->get('raid', 'value', array($raid_id, $dkp_id));
							foreach($attendees as $attendee){
								if(!isset($this->points[$attendee][$dkp_id]['single']['earned'][$event_id]))
									$this->points[$attendee][$dkp_id]['single']['earned'][$event_id] = 0;
								$this->points[$attendee][$dkp_id]['single']['earned'][$event_id] += $value;
							}
						}
					}
				}

				//spent
				if(is_array($evip['itempool_ids'])) {
					foreach($evip['itempool_ids'] as $itempool_id) {
						$item_ids = $this->pdh->get('item', 'item_ids_of_itempool', array($itempool_id));
						if(is_array($item_ids)) {
							foreach($item_ids as $item_id){
								$member_id = $this->pdh->get('item', 'buyer', array($item_id));
								$value = $this->pdh->get('item', 'value', array($item_id, $dkp_id));
								$raid_id = $this->pdh->get('item', 'raid_id', array($item_id));
								if(!isset($this->points[$member_id][$dkp_id]['single']['spent'][$raid2event[$raid_id]][$itempool_id]))
									$this->points[$member_id][$dkp_id]['single']['spent'][$raid2event[$raid_id]][$itempool_id] = 0;
								$this->points[$member_id][$dkp_id]['single']['spent'][$raid2event[$raid_id]][$itempool_id] += $value;
							}
						}
					}
				}

				//adjustment
				if(is_array($evip['event_ids'])) {
					foreach($evip['event_ids'] as $event_id) {
						$adjustment_ids = $this->pdh->get('adjustment', 'adjsofeventid', array($event_id));
						foreach($adjustment_ids as $adjustment_id) {
							$member_id = $this->pdh->get('adjustment', 'member', array($adjustment_id));
							$value = $this->pdh->get('adjustment', 'value', array($adjustment_id, $dkp_id));
							if(!isset($this->points[$member_id][$dkp_id]['single']['adjustment'][$event_id]))
								$this->points[$member_id][$dkp_id]['single']['adjustment'][$event_id] = 0;
							$this->points[$member_id][$dkp_id]['single']['adjustment'][$event_id] += $value;
						}
					}
				}
			}
			//ok, that was the basic table, now we calculate the real values
			$members = $this->pdh->get('member', 'id_list', array(false, false));
			$mdkps = $this->pdh->get('multidkp',  'id_list', array());
			foreach($members as $member_id){
				foreach($mdkps as $mdkp_id){
					$this->calculate_multi_points($member_id, $mdkp_id);
				}
			}
			$this->pdc->put('pdh_points_table', $this->points, null);
		}

		public function get_earned($member_id, $multidkp_id, $event_id=0, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if(!isset($this->points[$member_id][$multidkp_id][$with_twink]['earned'][$event_id])) return 0;
			return $this->points[$member_id][$multidkp_id][$with_twink]['earned'][$event_id];
		}

		public function get_html_earned($member_id, $multidkp_id, $event_id=0, $with_twink=true){
			return '<span class="positive">'.runden($this->get_earned($member_id, $multidkp_id, $event_id, $with_twink)).'</span>';
		}

		public function get_spent($member_id, $multidkp_id, $event_id=0, $itempool_id=0, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if(!isset($this->points[$member_id][$multidkp_id][$with_twink]['spent'][$event_id][$itempool_id])) return 0;
			return $this->points[$member_id][$multidkp_id][$with_twink]['spent'][$event_id][$itempool_id];
		}

		public function get_html_spent($member_id, $multidkp_id, $event_id=0, $itempool_id=0, $with_twink=true){
			return '<span class="negative">'.runden($this->get_spent($member_id, $multidkp_id, $event_id, $itempool_id, $with_twink)).'</span>';
		}

		public function get_adjustment($member_id, $multidkp_id, $event_id=0, $with_twink=true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if(!isset($this->points[$member_id][$multidkp_id][$with_twink]['adjustment'][$event_id])) return 0;
			return $this->points[$member_id][$multidkp_id][$with_twink]['adjustment'][$event_id];
		}

		public function get_html_adjustment($member_id, $multidkp_id, $event_id=0, $with_twink=true){
			return '<span class="'.color_item($this->get_adjustment($member_id, $multidkp_id, $event_id, $with_twink)).'">'.runden($this->get_adjustment($member_id, $multidkp_id, $event_id, $with_twink)).'</span>';
		}

		public function get_current($member_id, $multidkp_id, $event_id=0, $itempool_id=0, $with_twink=true){
			return ($this->get_earned($member_id, $multidkp_id, $event_id, $with_twink) - $this->get_spent($member_id, $multidkp_id, $event_id, $itempool_id, $with_twink) + $this->get_adjustment($member_id, $multidkp_id, $event_id, $with_twink));
		}

		public function get_html_current($member_id, $multidkp_id,  $event_id=0, $itempool_id=0, $with_twink=true){
			$with_twink = (int)$with_twink;
			$current = $this->get_current($member_id, $multidkp_id, $event_id, $itempool_id, $with_twink);
			return '<span class="'.color_item($current).'">'.runden($current).'</span>';
		}

		public function get_html_caption_current($mdkpid, $showname, $showtooltip, $tt_options = array()){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = $this->pdh->get_lang('points', 'current');
			}

			if($showtooltip){
				$tooltip = $this->user->lang('events').": <br />";
				$events = $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
				if(is_array($events)) foreach($events as $event_id) $tooltip .= $this->pdh->get('event', 'name', array($event_id))."<br />";
				$text = $this->html->ToolTip($tooltip, $text, '', $tt_options);
			}
			return $text;
		}

		public function calculate_single_points($memberid, $multidkpid = 1){
			//already cached?
			if(isset($this->points[$memberid][$multidkpid]['single']['earned'][0])){
				return $this->points[$memberid][$multidkpid]['single'];
			}

			//init
			$this->points[$memberid][$multidkpid]['single']['earned'][0] = 0;
			$this->points[$memberid][$multidkpid]['single']['spent'][0][0] = 0;
			$this->points[$memberid][$multidkpid]['single']['adjustment'][0] = 0;

			//calculate
			if(is_array($this->points[$memberid][$multidkpid]['single']['earned'])){
				foreach($this->points[$memberid][$multidkpid]['single']['earned'] as $event_id => $earned){
					$this->points[$memberid][$multidkpid]['single']['earned'][0] += $earned;
				}
			}

			if(is_array($this->points[$memberid][$multidkpid]['single']['spent'])){
				foreach($this->points[$memberid][$multidkpid]['single']['spent'] as $event_id => $itempools) {
					foreach($itempools as $itempool_id => $spent){
						$this->points[$memberid][$multidkpid]['single']['spent'][0][0] += $spent;
						if(!isset($this->points[$memberid][$multidkpid]['single']['spent'][$event_id][0])) $this->points[$memberid][$multidkpid]['single']['spent'][$event_id][0] = 0;
						$this->points[$memberid][$multidkpid]['single']['spent'][$event_id][0] += $spent;
						if(!isset($this->points[$memberid][$multidkpid]['single']['spent'][0][$itempool_id])) $this->points[$memberid][$multidkpid]['single']['spent'][0][$itempool_id] = 0;
						$this->points[$memberid][$multidkpid]['single']['spent'][0][$itempool_id] += $spent;
					}
				}
			}

			if(is_array($this->points[$memberid][$multidkpid]['single']['adjustment'])){
				foreach($this->points[$memberid][$multidkpid]['single']['adjustment'] as $event_id => $adjustment){
					$this->points[$memberid][$multidkpid]['single']['adjustment'][0] += $adjustment;
				}
			}
			return $this->points[$memberid][$multidkpid]['single'];
		}


		public function calculate_multi_points($memberid, $multidkpid = 1){
			//already cached?
			if(isset($this->points[$memberid][$multidkpid]['multi'])){
				return $this->points[$memberid][$multidkpid]['multi'];
			}

			//twink stuff
			if($this->pdh->get('member', 'is_main', array($memberid))){
				$twinks = $this->pdh->get('member', 'other_members', $memberid);

				//main points
				$points = $this->calculate_single_points($memberid, $multidkpid);
				$this->points[$memberid][$multidkpid]['multi']['earned'][0] = $points['earned'][0];
				$this->points[$memberid][$multidkpid]['multi']['spent'][0] = $points['spent'][0];
				$this->points[$memberid][$multidkpid]['multi']['adjustment'][0] = $points['adjustment'][0];

				//Accumulate points from twinks
				if(!empty($twinks) && is_array($twinks)){
					foreach($twinks as $twinkid){
						$twinkpoints = $this->calculate_single_points($twinkid, $multidkpid);
						$this->points[$memberid][$multidkpid]['multi']['earned'][0] += $twinkpoints['earned'][0];
						$this->points[$memberid][$multidkpid]['multi']['adjustment'][0] += $twinkpoints['adjustment'][0];
						//calculate points of member+twinks per event / itempool
						foreach(array('earned', 'adjustment') as $type) {
							if(isset($this->points[$memberid][$multidkpid][$type]) && is_array($this->points[$memberid][$multidkpid][$type])) {
								foreach($this->points[$memberid][$multidkpid][$type] as $id => $point) {
									if(!isset($this->points[$memberid][$multidkpid]['multi'][$type][$id])) $this->points[$memberid][$multidkpid]['multi'][$type][$id] = 0;
									$this->points[$memberid][$multidkpid]['multi'][$type][$id] += $this->points[$twinkid][$multidkpid]['single'][$type][$id];
								}
							}
						}
						foreach($twinkpoints['spent'] as $event_id => $vals) {
							foreach($vals as $ip_id => $val) {
								if(!isset($this->points[$memberid][$multidkpid]['multi']['spent'][$event_id][$ip_id])) $this->points[$memberid][$multidkpid]['multi']['spent'][$event_id][$ip_id] = 0;
								$this->points[$memberid][$multidkpid]['multi']['spent'][$event_id][$ip_id] += $val;
							}
						}
					}
				} else {
					$this->points[$memberid][$multidkpid]['multi'] = $this->points[$memberid][$multidkpid]['single'];
				}
				return $this->points[$memberid][$multidkpid]['multi'];
			} else {
				$main_id = $this->pdh->get('member', 'mainid', array($memberid));
				if($main_id) $this->points[$memberid][$multidkpid]['multi'] = $this->calculate_multi_points($main_id, $multidkpid);
				return $this->points[$memberid][$multidkpid]['multi'];
			}
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_points', pdh_r_points::__shortcuts());
?>