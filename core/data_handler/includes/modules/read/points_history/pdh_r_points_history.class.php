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

if ( !class_exists( "pdh_r_points_history" ) ) {
	class pdh_r_points_history extends pdh_r_generic{

		public $default_lang = 'english';

		public $points;

		private $arrLocalMappingCache = array();
		private $arrLocalPointsCache = array();
		private $arrCalculatedSingle = array();
		private $arrCalculatedMulti = array();

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
			$this->arrCalculatedMulti = array();
			$this->arrCalculatedSingle = array();
			$this->arrLocalMappingCache = array();
			$this->arrLocalPointsCache = array();
		}

		public function init() {

		}

		public function calculate_single_points($memberid, $multidkpid=1, $from=0, $to=PHP_INT_MAX){
			$strCacheKey = md5('single_'.$memberid.'.'.$multidkpid.'.'.$from.'.'.$to);

			if(isset($this->arrLocalPointsCache[$strCacheKey])) return $this->arrLocalPointsCache[$strCacheKey];

			if($from == 0){
				$arrLastSnapshot = $this->pdh->get('points', 'latest_snapshot', array($memberid, $multidkpid, $to));

				if($arrLastSnapshot){
					$intTime = (int)$arrLastSnapshot['time'];
					if($intTime >= $from && $intTime <= $to){
						if(strlen($arrLastSnapshot['misc'])){
							$arrPoints = unserialize_noclasses($arrLastSnapshot['misc']);
							unset($arrPoints['adjustment'][0]);
							unset($arrPoints['earned'][0]);
							unset($arrPoints['spent'][0]);
						}
					}

					//Get latest activities
					$arrEvents = $this->get_time_items($intTime+1, $to);

				} else {
					//No help from Snapshots
					$arrEvents = $this->get_time_items($from, $to);
					$arrPoints = array('earned' => array(), 'spent' => array(), 'adjustment' => array());
				}
			} else {
				$arrEvents = $this->get_time_items($from, $to);
				$arrPoints = array('earned' => array(), 'spent' => array(), 'adjustment' => array());
			}

			//For each item
			foreach($arrEvents['items'] as $itemID){
				$member_id = $this->pdh->get('item', 'buyer', array($itemID));
				if($member_id != $memberid) continue;

				$intItempoolId = $this->pdh->get('item', 'itempool_id', array($itemID));
				$intRaidId = $this->pdh->get('item', 'raid_id', array($itemID));
				if($intRaidId){
					$eventID = $this->pdh->get('raid', 'event', array($intRaidId));
					$arrMDkpPools = $this->pdh->get('event', 'multidkppools', array($eventID));
					if(in_array($multidkpid, $arrMDkpPools)){
						$value = $this->pdh->get('item', 'value', array($itemID, $multidkpid));
						if(!isset($arrPoints['spent'][$eventID])) $arrPoints['spent'][$eventID] = array();
						if(!isset($arrPoints['spent'][$eventID][$intItempoolId])) $arrPoints['spent'][$eventID][$intItempoolId] = 0;
						$arrPoints['spent'][$eventID][$intItempoolId] += $value;
					}
				}
			}


			//Adjustments
			foreach($arrEvents['adjustments'] as $adjID){
				$member_id = $this->pdh->get('adjustment', 'member', array($adjID));
				if($member_id != $memberid) continue;

				$eventID = $this->pdh->get('adjustment', 'event', array($adjID));
				$arrMDkpPools = $this->pdh->get('event', 'multidkppools', array($eventID));
				if(in_array($multidkpid, $arrMDkpPools)){
					if(!isset($arrPoints['adjustment'][$eventID])) $arrPoints['adjustment'][$eventID] = 0;
					$value = $this->pdh->get('adjustment', 'value', array($adjID, $multidkpid));
					$arrPoints['adjustment'][$eventID] += $value;
				}
			}



			//Raids
			foreach($arrEvents['raids'] as $raidID){
				$attendees = $this->pdh->get('raid', 'raid_attendees', array($raidID));
				if( !is_array($attendees) || !in_array($memberid, $attendees)) continue;

				$eventID = $this->pdh->get('raid', 'event', array($raidID));
				$arrMDkpPools = $this->pdh->get('event', 'multidkppools', array($eventID));
				if(in_array($multidkpid, $arrMDkpPools)){
					if(!isset($arrPoints['earned'][$eventID])) $arrPoints['earned'][$eventID] = 0;
					$value = $this->pdh->get('raid', 'value', array($raidID, $multidkpid));
					$arrPoints['earned'][$eventID] += $value;
				}
			}

			//calculate
			if(is_array($arrPoints['earned'])){
				foreach($arrPoints['earned'] as $event_id => $earned){
					if(!isset($arrPoints['earned'][0])) $arrPoints['earned'][0] = 0;
					$arrPoints['earned'][0] += $earned;
				}
			}

			if(is_array($arrPoints['spent'])){
				foreach($arrPoints['spent'] as $event_id => $itempools) {
					$arrPoints['spent'][$event_id][0] = 0;

					foreach($itempools as $itempool_id => $spent){
						if($itempool_id == 0) continue;
						if(!isset($arrPoints['spent'][0][0])) $arrPoints['spent'][0][0] = 0;
						$arrPoints['spent'][0][0] += $spent;
						if(!isset($arrPoints['spent'][$event_id][0])) $arrPoints['spent'][$event_id][0] = 0;
						$arrPoints['spent'][$event_id][0] += $spent;
						if(!isset($arrPoints['spent'][0][$itempool_id])) $arrPoints['spent'][0][$itempool_id] = 0;
						$arrPoints['spent'][0][$itempool_id] += $spent;
					}
				}
			}

			if(is_array($arrPoints['adjustment'])){
				foreach($arrPoints['adjustment'] as $event_id => $adjustment){
					$arrPoints['adjustment'][0] += $adjustment;
				}
			}

			$this->arrLocalPointsCache[$strCacheKey] = $arrPoints;
			$this->arrCalculatedSingle[$strCacheKey] = 1;

			return $arrPoints;

	}


	public function calculate_multi_points($memberid, $multidkpid = 1, $from=0, $to=PHP_INT_MAX){
			$strCacheKey = md5('multi_'.$memberid.'.'.$multidkpid.'.'.$from.'.'.$to);
			//already cached?
			if(isset($this->arrLocalPointsCache[$strCacheKey])) return true;

			$arrPoints = array();

			//twink stuff
			if($this->pdh->get('member', 'is_main', array($memberid))){
				$twinks = $this->pdh->get('member', 'other_members', $memberid);

				//main points
				$points = $this->calculate_single_points($memberid, $multidkpid, $from, $to);
				$arrPoints['earned'][0] = (isset($points['earned'][0])) ? $points['earned'][0] : 0;
				$arrPoints['spent'][0] = (isset($points['spent'][0])) ? $points['spent'][0] : array(0 => 0);
				$arrPoints['adjustment'][0] = (isset($points['adjustment'][0])) ? $points['adjustment'][0] : 0;

				//Accumulate points from twinks
				if(!empty($twinks) && is_array($twinks)){
					foreach($twinks as $twinkid){
						$twinkpoints = $this->calculate_single_points($twinkid, $multidkpid, $from, $to);
						$arrPoints['earned'][0] += $twinkpoints['earned'][0];
						$arrPoints['adjustment'][0] += $twinkpoints['adjustment'][0];
						//calculate points of member+twinks per event / itempool
						foreach(array('earned', 'adjustment') as $type) {
							if(isset($arrPoints[$memberid][$multidkpid][$type]) && is_array($arrPoints[$memberid][$multidkpid][$type])) {
								foreach($arrPoints[$memberid][$multidkpid][$type] as $id => $point) {
									if(!isset($arrPoints[$type][$id])) $arrPoints[$type][$id] = 0;
									$arrPoints[$type][$id] += $arrPoints[$twinkid][$multidkpid]['single'][$type][$id];
								}
							}
						}
						#$arrPoints['spent'][0][0] += $twinkpoints['spent'][0][0];
						foreach($twinkpoints['spent'] as $event_id => $vals) {
							foreach($vals as $ip_id => $val) {
								if(!isset($arrPoints['spent'][$event_id])) $arrPoints['spent'][$event_id] = array();
								if(!isset($arrPoints['spent'][$event_id][$ip_id])) $arrPoints['spent'][$event_id][$ip_id] = 0;
								$arrPoints['spent'][$event_id][$ip_id] += $val;
							}
						}
					}
				}
				$this->arrLocalPointsCache[$strCacheKey] = $arrPoints;
				return $arrPoints;
			} else {
				$main_id = $this->pdh->get('member', 'mainid', array($memberid));
				if($main_id) $arrPoints = $this->calculate_multi_points($main_id, $multidkpid, $from, $to);
				$this->arrLocalPointsCache[$strCacheKey] = $arrPoints;
				return $arrPoints;
			}
		}

		public function get_points($member_id, $multidkp_id, $from=0, $to=PHP_INT_MAX, $event_id=0, $itempool_id=0, $with_twink=true){
			if($with_twink){
				$arrPoints = $this->calculate_multi_points($member_id, $multidkp_id, $from, $to);
				return $arrPoints;
			} else {
				$arrPoints = $this->calculate_single_points($member_id, $multidkp_id, $from, $to);
				return $arrPoints;
			}
		}


		//ToDo: Build history for single character, with each items, adjustment and raid

		private function get_time_items($from, $to){
			$strCacheKey = md5($from.'.'.$to);
			if(isset($this->arrLocalMappingCache[$strCacheKey])) return $this->arrLocalMappingCache[$strCacheKey];

			$arrEvents = array('items' => array(), 'raids' => array(), 'adjustments' => array());
			//items
			$objQuery = $this->db->prepare("SELECT item_id FROM __items WHERE item_date >= ? AND item_date <= ?")->execute($from, $to);
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrEvents['items'][] = $row['item_id'];
				}
			}
			//raids
			$objQuery = $this->db->prepare("SELECT raid_id FROM __raids WHERE raid_date >= ? AND raid_date <= ?")->execute($from, $to);
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrEvents['raids'][] = $row['raid_id'];
				}
			}

			//adjustments
			$objQuery = $this->db->prepare("SELECT adjustment_id FROM __adjustments WHERE adjustment_date >= ? AND adjustment_date <= ?")->execute($from, $to);
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrEvents['adjustments'][] = $row['adjustment_id'];
				}
			}

			$this->arrLocalMappingCache[$strCacheKey] = $arrEvents;

			return $arrEvents;
		}
	}//end class
}//end if
