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

if ( !class_exists( "pdh_r_suicide_kings" ) ) {
	class pdh_r_suicide_kings extends pdh_r_generic{
		public $default_lang = 'english';
		public $sk_list;

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
			'sk_position_all'	=> array('position', array('%member_id%', '%ALL_IDS%', '%with_twink%'), array('%ALL_IDS%', true, true)),
			'sk_position'		=> array('position', array('%member_id%', '%dkp_id%', '%with_twink%'), array('%dkp_id%')),
		);

		public function reset(){
			$this->pdc->del('pdh_suicide_kings_table');
			unset($this->sk_list);
		}
		
		
		public function init(){
			//cached data not outdated?
			$this->sk_list = $this->pdc->get('pdh_suicide_kings_table');
			if($this->sk_list !== null){
				return true;
			}
			$arrMembers = $this->pdh->sort($this->pdh->get('member', 'id_list', array(false, false)), 'member', 'creation_date', 'asc');
			$member_hash = array('single' => array(), 'multi' => array());
			foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
				$startList = $this->config->get('sk_startlist_'.$mdkp_id);
				if (!$startList){
					shuffle($arrMembers);
					$this->config->set('sk_startlist_'.$mdkp_id, serialize($arrMembers));
				}
				
				foreach($startList as $intMemberID){
					if (in_array($intMemberID, $arrMembers)){
						$member_hash['single'][] = $intMemberID;
						$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
						if (!in_array($intMainID, $member_hash['multi'])) $member_hash['multi'][] = $intMainID;
					}
				}
				//New Members at the bottom
				foreach($arrMembers as $intMemberID){
					if (!in_array($intMemberID, $startList)){
						$member_hash['single'][] = $intMemberID;
						$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
						if (!in_array($intMainID, $member_hash['multi'])) $member_hash['multi'][] = $intMainID;
					}
				}

				//With twinks (mainchar)
				foreach($member_hash['multi'] as $pos => $member_id){
					$this->sk_list['multi'][$mdkp_id][$member_id] = ++$pos;
				}
			}
			
			//now the fun begins
			$item_list = $this->pdh->get('item', 'id_list');
			usort($item_list, array(&$this, "sort_item_list"));
			
			foreach($item_list as $item_id){
				$tmp_buyer = $this->pdh->get('item', 'buyer', array($item_id));
				$buyer = $this->pdh->get('member', 'mainid', array($tmp_buyer));
				$raid_id = $this->pdh->get('item', 'raid_id', array($item_id));
				$tmp_raid_attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
				foreach($tmp_raid_attendees as $key => $value){
					$raid_attendees[] = $this->pdh->get('member', 'mainid', array($value));
				}
						
				//this is really bad, because the buyer didn't attend the raid!
				//shouldnt need to be in here
				if(!in_array($buyer, $raid_attendees)){
					$raid_attendees[] = $buyer;
				}
				$raid_attendees = array_unique($raid_attendees);
		
				$itempool_id = $this->pdh->get('item', 'itempool_id', array($item_id));
				foreach($this->pdh->get('multidkp',  'mdkpids4itempoolid', array($itempool_id)) as $mdkp_id){
					$buyer_pos = $this->sk_list['multi'][$mdkp_id][$buyer];
					$last_pos = -1;
		
					//find buyer position and last position of raid attendee
					$att_pos = array();
					foreach($raid_attendees as $member_id){
						$att_pos[$member_id] = $this->sk_list['multi'][$mdkp_id][$member_id];
						if($this->sk_list['multi'][$mdkp_id][$member_id] > $last_pos){
							$last_pos = $this->sk_list['multi'][$mdkp_id][$member_id];
						}
					}
					asort($att_pos);
		
					$prev_it_pos = -1;
					//now we need to reorganize our list.
					foreach($att_pos as $member_id => $pos){
						if($pos < $buyer_pos){
							continue;
						}
						if($pos == $buyer_pos){
							$this->sk_list['multi'][$mdkp_id][$buyer] = $last_pos;
							continue;
						}
						if($pos > $buyer_pos){
							$this->sk_list['multi'][$mdkp_id][$member_id] = ($prev_it_pos < 0) ? $buyer_pos : $prev_it_pos;
							$prev_it_pos = $pos;
						}
					}
		
				}
			}
			
			
			//No twinks (all chars)
		
			foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
				foreach($member_hash['single'] as $pos => $member_id){
					$this->sk_list['single'][$mdkp_id][$member_id] = ++$pos;
				}
			}
			//now the fun begins
			$item_list = $this->pdh->get('item', 'id_list');
			usort($item_list, array(&$this, "sort_item_list"));
			
			foreach($item_list as $item_id){
				$buyer = $this->pdh->get('item', 'buyer', array($item_id));
				$raid_id = $this->pdh->get('item', 'raid_id', array($item_id));
				$raid_attendees = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
		
				//this is really bad, because the buyer didn't attend the raid!
				//shouldnt need to be in here
				if(!in_array($buyer, $raid_attendees)){
					$raid_attendees[] = $buyer;
				}
		
				$itempool_id = $this->pdh->get('item', 'itempool_id', array($item_id));
				foreach($this->pdh->get('multidkp',  'mdkpids4itempoolid', array($itempool_id)) as $mdkp_id){
					$buyer_pos = $this->sk_list['single'][$mdkp_id][$buyer];
					$last_pos = -1;
		
					//find buyer position and last position of raid attendee
					$att_pos = array();
					foreach($raid_attendees as $member_id){
						$att_pos[$member_id] = $this->sk_list['single'][$mdkp_id][$member_id];
						if($this->sk_list['single'][$mdkp_id][$member_id] > $last_pos){
							$last_pos = $this->sk_list['single'][$mdkp_id][$member_id];
						}
					}
					asort($att_pos);
		
					$prev_it_pos = -1;
					//now we need to reorganize our list.
					foreach($att_pos as $member_id => $pos){
						if($pos < $buyer_pos){
							continue;
						}
						if($pos == $buyer_pos){
							$this->sk_list['single'][$mdkp_id][$buyer] = $last_pos;
							continue;
						}
						if($pos > $buyer_pos){
							$this->sk_list['single'][$mdkp_id][$member_id] = ($prev_it_pos < 0) ? $buyer_pos : $prev_it_pos;
							$prev_it_pos = $pos;
						}
					}
		
				}
			}

			$this->pdc->put('pdh_suicide_kings_table', $this->sk_list, null);
		}

		public function sort_item_list($a, $b){
			return $this->pdh->comp('item', 'date', 1, array($a), array($b));
		}

		public function get_position($member_id, $multidkp_id, $with_twink = true){
			$with_twink = ($with_twink) ? 'multi' : 'single';
			if ($with_twink == 'multi'){
				$member_id = ($this->pdh->get('member', 'is_main', array($member_id))) ? $member_id : $this->pdh->get('member', 'mainid', array($member_id));
			}
			return $this->sk_list[$with_twink][$multidkp_id][$member_id];
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