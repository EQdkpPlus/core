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

if ( !class_exists( "pdh_r_zero_sum" ) ) {
	class pdh_r_zero_sum extends pdh_r_generic{

		public $default_lang = 'english';
		public $points;
		public $raid_vals;
		private $decayed = array();

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
			'zs_current_all'	=> array('zerosum', array('%member_id%', '%ALL_IDS%', 0, 0, '%with_twink%'), array('%ALL_IDS%', true, true)),
			'zs_current'		=> array('zerosum', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array('%dkp_id%')),
			'zs_earned'			=> array('earned', array('%member_id%', '%dkp_id%', 0, '%with_twink%'), array()),
			'zs_spent'			=> array('spent', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array()),
			'zs_rvalue'			=> array('raidval', array('%raid_id%'), array()),
			'zs_rvalue_all'		=> array('raidval', array('%raid_id%', '%ALL_IDS%'), array('%ALL_IDS%', true, true)),
		);

		public $detail_twink = array(
			'zerosum' 	=> 'summed_up',
			'earned' 	=> 'summed_up',
			'spent' 	=> 'summed_up',
		);

		public function reset(){
			$this->pdc->del('pdh_zero_sum_raids_table');
			$this->pdc->del('pdh_zero_sum_points_table');
			$this->raid_vals = NULL;
			$this->points = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->points		= $this->pdc->get('pdh_zero_sum_points_table');
			$this->raid_vals	= $this->pdc->get('pdh_zero_sum_raids_table');
			if($this->points !== null && $this->raid_vals !== null){
				return true;
			}

			$raid_ids = $this->pdh->get('raid', 'id_list');

			//calculate raid values
			$this->raid_vals = array();
			if(is_array($raid_ids)){
				foreach($raid_ids as $raid_id){
					//no attendees => no value
					$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
					if( !is_array( $attendees ) ){
						$this->raid_vals[$raid_id] = 0;
						continue;
					}
					//no items => no value
					$items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
					if( !is_array( $items ) ){
						$this->raid_vals[$raid_id] = 0;
						continue;
					}
					$this->raid_vals[$raid_id] = 0;

					foreach($items as $item_id){
						$this->raid_vals[$raid_id] += $this->pdh->get('item', 'value', $params=array($item_id));
					}

					//rvalue = value / attendees
					$this->raid_vals[$raid_id] = $this->raid_vals[$raid_id] / count($attendees);
				}
			}
			$this->pdc->put('pdh_zero_sum_raids_table', $this->raid_vals, null);

			//calculate points
			$this->points = array();

			//earned
			if(is_array($raid_ids)){
				foreach($raid_ids as $raid_id){
					if( !is_array( $this->pdh->get('raid', 'raid_attendees', array($raid_id)) ) ){
						continue;
					}
					$event_id = $this->pdh->get('raid', 'event', array($raid_id));
					if( !is_array( $this->pdh->get('multidkp','mdkpids4eventid',array($event_id)) ) ){
						continue;
					}
					foreach($this->pdh->get('multidkp','mdkpids4eventid',array($event_id)) as $mdkp_id){
						foreach($this->pdh->get('raid', 'raid_attendees', array($raid_id)) as $attendee){
							$this->points[$attendee][$mdkp_id]['single']['earned'][$event_id] += $this->raid_vals[$raid_id];
						}
					}
				}
			}

			//spent
			$item_ids = $this->pdh->get('item', 'id_list');
			if(is_array($item_ids)){
				foreach($item_ids as $item_id){
					$itempool_id = $this->pdh->get('item', 'itempool_id', array($item_id));
					$member_id = $this->pdh->get('item', 'buyer', array($item_id));
					foreach($this->pdh->get('multidkp',  'mdkpids4itempoolid', array($itempool_id)) as $mdkp_id){
						$this->points[$member_id][$mdkp_id]['single']['spent'][$itempool_id] += $this->pdh->get('item', 'value', array($item_id));
					}
				}
			}

			//adjustment
			$adjustment_ids = $this->pdh->get('adjustment', 'id_list');
			if(is_array($adjustment_ids)){
				foreach($adjustment_ids as $adjustment_id){
					$event_id = $this->pdh->get('adjustment', 'event', array($adjustment_id));
					$member_id = $this->pdh->get('adjustment', 'member', array($adjustment_id));
					foreach($this->pdh->get('multidkp','mdkpids4eventid',array($event_id)) as $mdkp_id){
						$this->points[$member_id][$mdkp_id]['single']['adjustment'][$event_id] += $this->pdh->get('adjustment', 'value', array($adjustment_id));
					}
				}
			}

			//ok, that was the basic table, now we calculate the real values
			foreach($this->pdh->get('member', 'id_list', array(false, false)) as $member_id){
				foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
					$this->calculate_multi_points($member_id, $mdkp_id);
				}
			}
			$this->pdc->put('pdh_zero_sum_points_table', $this->points, null);
		}

		public function calculate_single_points($memberid, $multidkpid = 1){
			//already cached?
			if(isset($this->points[$memberid][$multidkpid]['single']['earned'][0])){
				return $this->points[$memberid][$multidkpid]['single'];
			}

			//init
			$this->points[$memberid][$multidkpid]['single']['earned'][0] = 0;
			$this->points[$memberid][$multidkpid]['single']['spent'][0] = 0;
			$this->points[$memberid][$multidkpid]['single']['adjustment'][0] = 0;

			//calculate
			if(is_array($this->points[$memberid][$multidkpid]['single']['earned'])){
				foreach($this->points[$memberid][$multidkpid]['single']['earned'] as $event_id => $earned){
					$this->points[$memberid][$multidkpid]['single']['earned'][0] += $earned;
				}
			}

			if(is_array($this->points[$memberid][$multidkpid]['single']['spent'])){
				foreach($this->points[$memberid][$multidkpid]['single']['spent'] as $itempool_id => $spent) {
					if(!isset($this->points[$memberid][$multidkpid]['single']['spent'][0])) $this->points[$memberid][$multidkpid]['single']['spent'][0] = 0;
					$this->points[$memberid][$multidkpid]['single']['spent'][0] += $spent;
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


		public function get_zerosum($member_id, $multidkp_id, $event_id=0, $itempool_id=0, $with_twink=true){
			return ($this->get_earned($member_id, $multidkp_id, $event_id, $with_twink) - $this->get_spent($member_id, $multidkp_id, $event_id, $itempool_id, $with_twink) + $this->get_adjustment($member_id, $multidkp_id, $event_id, $with_twink));
		}

		public function get_html_zerosum($member_id, $multidkp_id,  $event_id=0, $itempool_id=0, $with_twink=true){
			$with_twink = (int)$with_twink;
			$current = $this->get_zerosum($member_id, $multidkp_id, $event_id, $itempool_id, $with_twink);
			return '<span class="'.color_item($current).'">'.runden($current).'</span>';
		}

		public function get_html_caption_zerosum($mdkpid, $showname = false, $showtooltip = false, $tt_options = array()){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = $this->pdh->get_lang('points', 'current');
			}

			if($showtooltip){
				$tooltip = $this->user->lang('events').": <br />";
				$events = $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
				if(is_array($events)) foreach($events as $event_id) $tooltip .= $this->pdh->get('event', 'name', array($event_id))."<br />";
				$text = new htooltip('tt_event'.$event_id, array_merge(array('content' => $tooltip, 'label' => $text), $tt_options));
			}
			return $text;
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
			if(!isset($this->points[$member_id][$multidkp_id][$with_twink]['spent'][$itempool_id])) return 0;
			return $this->points[$member_id][$multidkp_id][$with_twink]['spent'][$itempool_id];
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

		public function get_raidval($id, $dkp_id=0, $date=0){
			if($dkp_id) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('raid', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->raid_vals[$id], 'date' => $this->pdh->get('raid', 'date', array($id)));
					$val = $this->apa->get_decay_val('raid', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? $val : $this->raid_vals[$id];
		}

		public function get_html_raidval($id, $dkp_id=0){
			return '<span class="positive">' . runden($this->get_raidval($id, $dkp_id)) . '</span>';
		}


		public function get_html_caption_raidval($mdkpid, $showname, $showtooltip){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = $this->pdh->get_lang('raid', 'value');;
			}

			if($showtooltip){
				$tooltip = $this->user->lang('events').": <br />";
				$events = $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
				if(is_array($events))
				foreach($events as $event_id)
				$tooltip .= $this->pdh->get('event', 'name', array($event_id))."<br />";
				$text = '<span class="coretip" data-coretip="'.$tooltip.'">'.$text.'</span>';
			}
			return $text;
		}
	}//end class
}//end if
?>