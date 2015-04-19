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

if ( !class_exists( "pdh_r_suicide_kings_fixed" ) ) {
	class pdh_r_suicide_kings_fixed extends pdh_r_generic{

		public $default_lang = 'english';
		private $sk_list = array();

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
			'sk_fixed_position_all'	=> array('position', array('%member_id%', '%ALL_IDS%', '%with_twink%'), array('%ALL_IDS%', true, true)),
			'sk_fixed_position'		=> array('position', array('%member_id%', '%dkp_id%', '%with_twink%'), array('%dkp_id%')),
		);
		
		public $detail_twink = array(
			'position' => 'summed_up',
		);

		public function reset(){
			$this->pdc->del('pdh_suicide_kings_fixed_table');
			$this->sk_list = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->sk_list = $this->pdc->get('pdh_suicide_kings_fixed_table');
			if($this->sk_list !== null){
				return true;
			}

			//base list for all mdkp pools
			$member_list = $this->pdh->get('member', 'id_list');
			$member2main = $this->pdh->aget('member', 'mainid', 0, array($member_list));
			$main2member = $this->pdh->aget('member', 'other_members', 0, array(array_unique($member2main)));
			
			$arrMembers = $this->pdh->sort($this->pdh->get('member', 'id_list', array(false, false)), 'member', 'creation_date', 'asc');
			
			// mdkp2event list
			$mdkplist = $this->pdh->aget('multidkp', 'event_ids', 0, array($this->pdh->get('multidkp',  'id_list', array())));
			// raid-event list sorted by date
			$raid_ids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'asc');
			$raidlist = $this->pdh->maget(array('raid', 'raid', 'item'), array('event', 'raid_attendees', 'itemsofraid'), 0, array($raid_ids));
			
			foreach($mdkplist as $mdkp_id => $events) {
				$member_list = $main_list = array();
				// initialise list
				$startList = $this->config->get('sk_btm_startlist_'.$mdkp_id);
				if (!$startList){
					shuffle($arrMembers);
					$this->config->set('sk_btm_startlist_'.$mdkp_id, serialize($arrMembers));
				}
				
				foreach($startList as $intMemberID){
					if (in_array($intMemberID, $arrMembers)){
						$member_list[] = $intMemberID;
						$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
						if (!in_array($intMainID, $main_list)) $main_list[] = $intMainID;
					}
				}
				//New Members at the bottom
				foreach($arrMembers as $intMemberID){
					if (!in_array($intMemberID, $startList)){
						$member_list[] = $intMemberID;
						$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
						if (!in_array($intMainID, $main_list)) $main_list[] = $intMainID;
					}
				}
				$member_list = array_flip($member_list);
				$main_list = array_flip($main_list);
				
				$arrItempools = $this->pdh->get('multidkp', 'itempool_ids', array($mdkp_id));
				
				if(!isset($this->sk_list['multi'][$mdkp_id])) $this->sk_list['multi'][$mdkp_id] = $main_list;
				if(!isset($this->sk_list['single'][$mdkp_id])) $this->sk_list['single'][$mdkp_id] = $member_list;
				// iterate through raids
				foreach($raidlist as $raid_id => $raid) {
					if(!in_array($raid['event'], $events)) continue;
					$temp_list = array();
					$redistribute = array();
					asort($this->sk_list['single'][$mdkp_id]);
					foreach($this->sk_list['single'][$mdkp_id] as $member_id => $posi) {
						if(!in_array($member_id, $raid['raid_attendees'])) continue;
						$temp_list['single'][] = $member_id;
						$redistribute['single'][] = $posi;
					}
					asort($this->sk_list['multi'][$mdkp_id]);
					foreach($this->sk_list['multi'][$mdkp_id] as $main_id => $posi) {
						if(!in_array($main_id, $raid['raid_attendees'])) {
							$cont = true;
							if(!empty($main2member[$main_id])) {
								foreach($main2member[$main_id] as $member_id) {
									if(in_array($member_id, $raid['raid_attendees'])) {
										$cont = false;
										break;
									}
								}
							}
							if($cont) continue;
						}
						$temp_list['multi'][] = $main_id;
						$redistribute['multi'][] = $posi;
					}
					$items = $this->pdh->aget('item', 'buyer', 0, array($this->pdh->sort($raid['itemsofraid'], 'item', 'date', 'asc')));
					foreach($items as $itemid => $memberid) {
						if(!in_array($memberid, $raid['raid_attendees'])) continue; // ignore items assigned to members not present in raid - most likely special members
						//Ignore Items from different Itempool in this raid
						$itempool_id = $this->pdh->get('item', 'itempool_id', array($itemid));
						if(!in_array($itempool_id, $arrItempools)) continue;
						
						$key = array_search($memberid, $temp_list['single']);
						unset($temp_list['single'][$key]);
						$temp_list['single'][] = $memberid;
						$key = array_search($member2main[$memberid], $temp_list['multi']);
						unset($temp_list['multi'][$key]);
						$temp_list['multi'][] = $member2main[$memberid];
					}
					$temp_list['single'] = array_values($temp_list['single']);
					foreach($temp_list['single'] as $key => $member_id) {
						$this->sk_list['single'][$mdkp_id][$member_id] = $redistribute['single'][$key];
					}
					$temp_list['multi'] = array_values($temp_list['multi']);
					foreach($temp_list['multi'] as $key => $member_id) {
						$this->sk_list['multi'][$mdkp_id][$member_id] = $redistribute['multi'][$key];
					}
				}
			}

			$this->pdc->put('pdh_suicide_kings_fixed_table', $this->sk_list, null);
		}

		public function get_position($member_id, $multidkp_id, $with_twink = true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if ($with_twink == 'multi'){
				$member_id = ($this->pdh->get('member', 'is_main', array($member_id))) ? $member_id : $this->pdh->get('member', 'mainid', array($member_id));
			}
			return (isset( $this->sk_list[$with_twink][$multidkp_id][$member_id] )) ? $this->sk_list[$with_twink][$multidkp_id][$member_id] : 0;
		}
		
		public function get_html_position($member_id, $multidkp_id, $with_twink=true) {
			return $this->get_position($member_id, $multidkp_id, $with_twink)+1;
		}

		public function get_html_caption_position($mdkpid, $showname = false, $showtooltip = false, $tt_options = array()){
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

	}//end class
}//end if
?>