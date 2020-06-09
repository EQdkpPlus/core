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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_zero_sum" ) ) {
	class pdh_r_zero_sum extends pdh_r_generic{
		
		public $default_lang = 'english';
		public $points;
		public $raid_vals;
		
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
				'zs_rvalue'			=> array('raidval', array('%raid_id%'), array('%dkp_id%')),
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
			
			//calculate points
			$arrPoints = array();
			
			//calculate raid values
			$arrRaids= array();
			if(is_array($raid_ids)){
				foreach($raid_ids as $raid_id){
					//no attendees => no value
					$attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
					if( !is_array( $attendees ) ){
						$arrRaids[$raid_id] = 0;
						continue;
					}
					//no items => no value
					$items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
					if( !is_array( $items ) ){
						$arrRaids[$raid_id] = 0;
						continue;
					}
					$arrRaids[$raid_id] = array();
					
					$event_id = $this->pdh->get('raid', 'event', array($raid_id));
					
					if( !is_array( $this->pdh->get('multidkp','mdkpids4eventid',array($event_id)) ) ){
						continue;
					}
					
					$blnCalculateZeroPool = false;
					
					foreach($this->pdh->get('multidkp','mdkpids4eventid',array($event_id)) as $mdkp_id){
						$arrRaids[$raid_id][$mdkp_id] = 0;
						$arrRaids[$raid_id][0] = 0;
						
						foreach($items as $item_id){
							$arrRaids[$raid_id][$mdkp_id] += $this->pdh->get('item', 'value', array($item_id, $mdkp_id));
							
							if(!$blnCalculateZeroPool){
								$arrRaids[$raid_id][0] += $this->pdh->get('item', 'value', array($item_id));
							}
						}
						
						$blnCalculateZeroPool = true;
						
						//rvalue = value / attendees
						if(count($attendees)){
							$arrRaids[$raid_id][$mdkp_id] = $arrRaids[$raid_id][$mdkp_id] / count($attendees);
						} else {
							$arrRaids[$raid_id][$mdkp_id] = 0;
						}
						
						foreach($attendees as $attendee){
							$arrPoints[$attendee][$mdkp_id]['single']['earned'][$event_id] += $arrRaids[$raid_id][$mdkp_id];
						}
						
					}
					if(count($attendees)){
						$arrRaids[$raid_id][0] = $arrRaids[$raid_id][0] / count($attendees);
					} else {
						$arrRaids[$raid_id][0] = 0;
					}
					
				}
			}
			
			$this->raid_vals = $arrRaids;
			
			$this->pdc->put('pdh_zero_sum_raids_table', $arrRaids, null);
			
			//spent
			$item_ids = $this->pdh->get('item', 'id_list');
			if(is_array($item_ids)){
				foreach($item_ids as $item_id){
					$itempool_id = $this->pdh->get('item', 'itempool_id', array($item_id));
					$member_id = $this->pdh->get('item', 'buyer', array($item_id));
					foreach($this->pdh->get('multidkp',  'mdkpids4itempoolid', array($itempool_id)) as $mdkp_id){
						$arrPoints[$member_id][$mdkp_id]['single']['spent'][$itempool_id] += $this->pdh->get('item', 'value', array($item_id, $mdkp_id));
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
						$arrPoints[$member_id][$mdkp_id]['single']['adjustment'][$event_id] += $this->pdh->get('adjustment', 'value', array($adjustment_id, $mdkp_id));
					}
				}
			}
			
			//ok, that was the basic table, now we calculate the real values
			foreach($this->pdh->get('member', 'id_list', array(false, false)) as $member_id){
				foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
					$arrPoints = $this->calculate_multi_points($arrPoints, $member_id, $mdkp_id);
				}
			}
			
			$this->pdc->put('pdh_zero_sum_points_table', $arrPoints, null);
			
			$this->points = $arrPoints;
		}
		
		public function calculate_single_points($arrPoints, $memberid, $multidkpid = 1){
			//already cached?
			if(isset($arrPoints[$memberid][$multidkpid]['single']['earned'][0])){
				return $arrPoints[$memberid][$multidkpid]['single'];
			}
			
			//init
			$arrPoints[$memberid][$multidkpid]['single']['earned'][0] = 0;
			$arrPoints[$memberid][$multidkpid]['single']['spent'][0] = 0;
			$arrPoints[$memberid][$multidkpid]['single']['adjustment'][0] = 0;
			
			//calculate
			if(is_array($arrPoints[$memberid][$multidkpid]['single']['earned'])){
				foreach($arrPoints[$memberid][$multidkpid]['single']['earned'] as $event_id => $earned){
					$arrPoints[$memberid][$multidkpid]['single']['earned'][0] += $earned;
				}
			}
			
			if(is_array($arrPoints[$memberid][$multidkpid]['single']['spent'])){
				foreach($arrPoints[$memberid][$multidkpid]['single']['spent'] as $itempool_id => $spent) {
					if(!isset($arrPoints[$memberid][$multidkpid]['single']['spent'][0])) $arrPoints[$memberid][$multidkpid]['single']['spent'][0] = 0;
					$arrPoints[$memberid][$multidkpid]['single']['spent'][0] += $spent;
				}
			}
			
			
			if(is_array($arrPoints[$memberid][$multidkpid]['single']['adjustment'])){
				foreach($arrPoints[$memberid][$multidkpid]['single']['adjustment'] as $event_id => $adjustment){
					$arrPoints[$memberid][$multidkpid]['single']['adjustment'][0] += $adjustment;
				}
			}
			return $arrPoints[$memberid][$multidkpid]['single'];
		}
		
		
		public function calculate_multi_points($arrPoints, $memberid, $multidkpid = 1, $blnReturnForMember=false){
			//already cached?
			if(isset($arrPoints[$memberid][$multidkpid]['multi'])){
				return ($blnReturnForMember) ? $arrPoints[$memberid][$multidkpid]['multi'] : $arrPoints;
			}
			
			//twink stuff
			if($this->pdh->get('member', 'is_main', array($memberid))){
				$twinks = $this->pdh->get('member', 'other_members', $memberid);
				
				//main points
				$arrSinglePoints = $this->calculate_single_points($arrPoints, $memberid, $multidkpid);
				$arrPoints[$memberid][$multidkpid]['single'] = $arrSinglePoints;
				$arrPoints[$memberid][$multidkpid]['multi']['earned'][0] = $arrSinglePoints['earned'][0];
				$arrPoints[$memberid][$multidkpid]['multi']['spent'][0] = $arrSinglePoints['spent'][0];
				$arrPoints[$memberid][$multidkpid]['multi']['adjustment'][0] = $arrSinglePoints['adjustment'][0];
				
				//Accumulate points from twinks
				if(!empty($twinks) && is_array($twinks)){
					foreach($twinks as $twinkid){
						$twinkpoints = $this->calculate_single_points($arrPoints, $twinkid, $multidkpid);
						$arrPoints[$memberid][$multidkpid]['multi']['earned'][0] += $twinkpoints['earned'][0];
						$arrPoints[$memberid][$multidkpid]['multi']['adjustment'][0] += $twinkpoints['adjustment'][0];
						//calculate points of member+twinks per event / itempool
						foreach(array('earned', 'adjustment') as $type) {
							if(isset($arrPoints[$memberid][$multidkpid][$type]) && is_array($arrPoints[$memberid][$multidkpid][$type])) {
								foreach($arrPoints[$memberid][$multidkpid][$type] as $id => $point) {
									if(!isset($arrPoints[$memberid][$multidkpid]['multi'][$type][$id])) $arrPoints[$memberid][$multidkpid]['multi'][$type][$id] = 0;
									$arrPoints[$memberid][$multidkpid]['multi'][$type][$id] += $arrPoints[$twinkid][$multidkpid]['single'][$type][$id];
								}
							}
						}
						foreach($twinkpoints['spent'] as $ip_id => $val) {
							if(!isset($arrPoints[$memberid][$multidkpid]['multi']['spent'][$ip_id])) $arrPoints[$memberid][$multidkpid]['multi']['spent'][$ip_id] = 0;
							$arrPoints[$memberid][$multidkpid]['multi']['spent'][$ip_id] += $val;
						}
					}
				} else {
					$arrPoints[$memberid][$multidkpid]['multi'] = $arrSinglePoints;
				}
				return ($blnReturnForMember) ? $arrPoints[$memberid][$multidkpid]['multi'] : $arrPoints;
			} else {
				$main_id = $this->pdh->get('member', 'mainid', array($memberid));
				
				if($main_id) $arrPoints[$memberid][$multidkpid]['multi'] = $this->calculate_multi_points($arrPoints, $main_id, $multidkpid, true);
				return ($blnReturnForMember) ? $arrPoints[$memberid][$multidkpid]['multi'] : $arrPoints;
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
				$text = (new htooltip('tt_event'.$event_id, array_merge(array('content' => $tooltip, 'label' => $text), $tt_options)))->output();
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
			return $this->raid_vals[$id][$dkp_id];
		}
		
		public function get_html_raidval($id, $dkp_id=0){
			return '<span class="positive">' . runden($this->get_raidval($id, $dkp_id)) . '</span>';
		}
		
		
		public function get_html_caption_raidval($mdkpid, $showname=false, $showtooltip=false){
			if($showname){
				$text = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}else{
				$text = $this->pdh->get_lang('raid', 'value');
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
