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

if ( !class_exists( "pdh_r_points_history" ) ) {
	class pdh_r_points_history extends pdh_r_generic{

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
			'earned_history' => array('earned_history', array('%member_id%', '%dkp_id%', 0, '%with_twink%'), array('%dkp_id%')),
			'spent_history' => array('spent_history', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array('%dkp_id%')),
			'adjustment_history' => array('adjustment_history', array('%member_id%', '%dkp_id%', 0, '%with_twink%'), array('%dkp_id%')),
			'current_history' => array('current_history', array('%member_id%', '%dkp_id%', 0, 0, '%with_twink%'), array('%dkp_id%', false, true)),
			'all_current_history' => array('current_history', array('%member_id%', '%ALL_IDS%', 0, 0, '%with_twink%'), array('%ALL_IDS%', true, true)),
		);

		public $detail_twink = array(
			'earned'		=> 'summed_up',
			'spent'			=> 'summed_up',
			'adjustment'	=> 'summed_up',
			'current'		=> 'summed_up',
		);

		public function reset(){
			$this->pdc->del_prefix('pdh_points_history');
			$this->points = NULL;
		}

		public function init() {
			//cached data not outdated?
			$this->points = $this->pdc->get('pdh_points_history');
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
							$raiddate = $this->pdh->get('raid', 'date', array($raid_id));
							foreach($attendees as $attendee){
								if(!isset($this->points[$attendee][$dkp_id]['single']['earned'][$event_id]))
									$this->points[$attendee][$dkp_id]['single']['earned'][$event_id] = array();
								$this->points[$attendee][$dkp_id]['single']['earned'][$event_id][] = array($value, $raiddate, $raid_id, $attendee);
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
									$this->points[$member_id][$dkp_id]['single']['spent'][$raid2event[$raid_id]][$itempool_id] = array();
								$this->points[$member_id][$dkp_id]['single']['spent'][$raid2event[$raid_id]][$itempool_id][] = array($value, $this->pdh->get('item', 'date', array($item_id)), $item_id, $member_id);
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
								$this->points[$member_id][$dkp_id]['single']['adjustment'][$event_id] = array();
							$this->points[$member_id][$dkp_id]['single']['adjustment'][$event_id][] = array($value, $this->pdh->get('adjustment', 'date', array($adjustment_id)), $adjustment_id, $member_id);
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
			$this->pdc->put('pdh_points_history', $this->points, null);
		}
		
		public function calculate_multi_points($memberid, $multidkpid = 1){
			//already cached?
			if(isset($this->points[$memberid][$multidkpid]['multi'])){
				return $this->points[$memberid][$multidkpid]['multi'];
			}

			//twink stuff
			if($this->pdh->get('member', 'is_main', array($memberid))){
				$twinks = $this->pdh->get('member', 'other_members', $memberid);

				
				$this->points[$memberid][$multidkpid]['multi'] = array();
				
				//main points
				if (!empty($this->points[$memberid][$multidkpid]['single'])) $this->points[$memberid][$multidkpid]['multi'] = $this->points[$memberid][$multidkpid]['single'];

				//Accumulate points from twinks
				if(!empty($twinks) && is_array($twinks)){
					foreach($twinks as $twinkid){
						if(isset($this->points[$twinkid][$multidkpid]) && isset($this->points[$twinkid][$multidkpid]['single']['earned'])){
							foreach($this->points[$twinkid][$multidkpid]['single']['earned'] as $event_id => $earned){
								if(!is_array($this->points[$memberid][$multidkpid]['multi']['earned'][$event_id])) $this->points[$memberid][$multidkpid]['multi']['earned'][$event_id] = array(); 
								$this->points[$memberid][$multidkpid]['multi']['earned'][$event_id] = array_merge($this->points[$memberid][$multidkpid]['multi']['earned'][$event_id], $earned);
							}
						}
						
						if(isset($this->points[$twinkid][$multidkpid]) && isset($this->points[$twinkid][$multidkpid]['single']['spent'])){
							foreach($this->points[$twinkid][$multidkpid]['single']['spent'] as $event_id => $itempools) {
								foreach($itempools as $itempool_id => $spent){
									if(!is_array($this->points[$memberid][$multidkpid]['multi']['spent'][ $event_id][$itempool_id])) $this->points[$memberid][$multidkpid]['multi']['spent'][ $event_id][$itempool_id] = array();
									$this->points[$memberid][$multidkpid]['multi']['spent'][ $event_id][$itempool_id] = array_merge($this->points[$memberid][$multidkpid]['multi']['spent'][ $event_id][$itempool_id], $spent);
									
								}
							}
						}
						
						if(isset($this->points[$twinkid][$multidkpid]) && isset($this->points[$twinkid][$multidkpid]['single']['adjustment'])){
							foreach($this->points[$twinkid][$multidkpid]['single']['adjustment'] as $event_id => $adjustment){
								if(!isset($this->points[$memberid][$multidkpid]['multi']['adjustment'][$event_id])) $this->points[$memberid][$multidkpid]['multi']['adjustment'][$event_id] = array();
								$this->points[$memberid][$multidkpid]['multi']['adjustment'][$event_id] = array_merge($this->points[$memberid][$multidkpid]['multi']['adjustment'][$event_id], $adjustment);
							}
						}
						
					}
				}
				
				return $this->points[$memberid][$multidkpid]['multi'];
			} else {
				$main_id = $this->pdh->get('member', 'mainid', array($memberid));
				if($main_id) $this->points[$memberid][$multidkpid]['multi'] = $this->calculate_multi_points($main_id, $multidkpid);
				return $this->points[$memberid][$multidkpid]['multi'];
			}
		}
		
		
		public function get_history($member_id, $multidkp_id, $from=0, $to=PHP_INT_MAX, $event_id=0, $itempool_id=0, $with_twink=true ){
			$arrDate = $arrOut = array();

			$cacheKey = md5($member_id.'.'.$multidkp_id.'.'.$from.'.'.$to.'.'.$event_id.'.'.$itempool_id.'.'.$with_twink);
			$cachedData = $this->pdc->get('pdh_points_history_'.$cacheKey);
			if($cachedData !== NULL){
				return $cachedData;
			}
			
			$arrEarned = $this->get_earned($member_id, $multidkp_id, $from, $to, $event_id, $with_twink);
			foreach($arrEarned as $val){
				$arrDate[] = $val[1];
				$arrOut[] = array(
					'value' => $val[0],
					'date'	=> $val[1],
					'type'	=> 'earned',
					'id'	=> $val[2],
					'character' => $val[3],
				);
			}
			
			$arrSpent = $this->get_spent($member_id, $multidkp_id, $from, $to, $event_id, $itempool_id, $with_twink);
			foreach($arrSpent as $val){
				$arrDate[] = $val[1];
				$arrOut[] = array(
						'value' => $val[0],
						'date'	=> $val[1],
						'type'	=> 'spent',
						'id'	=> $val[2],
						'character' => $val[3],
				);
			}
			
			$arrAdjustment = $this->get_adjustment($member_id, $multidkp_id, $from, $to, $event_id, $with_twink);
			foreach($arrAdjustment as $val){
				$arrDate[] = $val[1];
				$arrOut[] = array(
						'value' => $val[0],
						'date'	=> $val[1],
						'type'	=> 'adjustment',
						'id'	=> $val[2],
						'character' => $val[3],
				);
			}
			
			array_multisort($arrDate, SORT_ASC, SORT_NUMERIC, $arrOut);
			
			$this->pdc->put('pdh_points_history_'.$cacheKey, $arrOut, null);
			
			return $arrOut;
		}
		
		public function get_spent($member_id, $multidkp_id, $from=0, $to=PHP_INT_MAX, $event_id=0, $itempool_id=0, $with_twink=true){
			$strTwink = ($with_twink) ? 'multi' : 'single';
			$arrSpent = array();
			
			//Get Array
			if(!empty($this->points[$member_id][$multidkp_id][$strTwink]['spent'])) {
				foreach($this->points[$member_id][$multidkp_id][$strTwink]['spent'] as $_event_id => $itempools) {
					if ($event_id != 0 && $_event_id != $event_id) continue;
					foreach($itempools as $_itempool_id => $spent){
						
						if ($itempool_id != 0 && $itempool_id != $_itempool_id) continue;
						foreach($spent as $val){
							if ($val[1] < $from || $val[1] > $to) continue;
							$arrSpent[] = $val;
						}
					}
				}
			}
			
			//Sort
			$arrDate = array();
			foreach($arrSpent as $val){
				$arrDate[] = $val[1];
			}
			
			array_multisort($arrDate, SORT_ASC, SORT_NUMERIC, $arrSpent);
			return $arrSpent;
		}
		
		public function get_earned($member_id, $multidkp_id, $from=0, $to=PHP_INT_MAX, $event_id=0, $with_twink=true){
			
			$strTwink = ($with_twink) ? 'multi' : 'single';
			$arrEarned = array();
			//Get Array
			if (isset($this->points[$member_id][$multidkp_id][$strTwink]['earned'])){
				foreach($this->points[$member_id][$multidkp_id][$strTwink]['earned'] as $_event_id => $earned){
					if ($event_id != 0 && $_event_id != $event_id) continue;
					foreach($earned as $val){
						if ($val[1] < $from || $val[1] > $to) continue;
						$arrEarned[] = $val;
					}
				}
			}
				
			//Sort
			$arrDate = array();
			foreach($arrEarned as $val){
				$arrDate[] = $val[1];
			}
				
			array_multisort($arrDate, SORT_ASC, SORT_NUMERIC, $arrEarned);
			return $arrEarned;
		}
		
		public function get_adjustment($member_id, $multidkp_id, $from=0, $to=PHP_INT_MAX, $event_id=0, $with_twink=true){
			$strTwink = ($with_twink) ? 'multi' : 'single';
			
			$arrAdjustment = array();
			//Get Array
			if (isset($this->points[$member_id][$multidkp_id][$strTwink]['adjustment'])) {
				foreach($this->points[$member_id][$multidkp_id][$strTwink]['adjustment'] as $_event_id => $earned){
				if ($event_id != 0 && $_event_id != $event_id) continue;
					foreach($earned as $val){
						if ($val[1] < $from || $val[1] > $to) continue;
						$arrAdjustment[] = $val;
					}
				}
			}
			
			//Sort
			$arrDate = array();
			foreach($arrAdjustment as $val){
				$arrDate[] = $val[1];
			}
			
			array_multisort($arrDate, SORT_ASC, SORT_NUMERIC, $arrAdjustment);
			return $arrAdjustment;
			
		}
	}//end class
}//end if
?>