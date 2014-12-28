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

if ( !class_exists( "pdh_r_suicide_kings_bottom" ) ) {
	class pdh_r_suicide_kings_bottom extends pdh_r_generic{

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
			'sk_bottom_position_all'	=> array('position', array('%member_id%', '%ALL_IDS%', '%with_twink%'), array('%ALL_IDS%', true, true)),
			'sk_bottom_position'		=> array('position', array('%member_id%', '%dkp_id%', '%with_twink%'), array('%dkp_id%')),
		);

		public function reset(){
			$this->pdc->del('pdh_suicide_kings_bottom_table');
			$this->sk_list = NULL;
		}


		public function init(){
			//cached data not outdated?
			$this->sk_list = $this->pdc->get('pdh_suicide_kings_bottom_table');
			if($this->sk_list !== null){
				return true;
			}

			//base list for all mdkp pools
			$member_hash = array();
			$arrMembers = $this->pdh->sort($this->pdh->get('member', 'id_list', array(false, false)), 'member', 'creation_date', 'asc');

			/*
			foreach($member_list as $member_id){
				$member_hash['single'][$member_id] = md5($this->pdh->get('member','name', array($member_id)));
				$intMainID = $this->pdh->get('member', 'mainid', array($member_id));
				$member_hash['multi'][$intMainID] = md5($this->pdh->get('member','name', array($intMainID)));
			}
			*/
			
			//With Twinks (mainchar only)
			foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
				// initialise list
				$startList = $this->config->get('sk_fix_startlist_'.$mdkp_id);
				if (!$startList){
					shuffle($arrMembers);
					$this->config->set('sk_fix_startlist_'.$mdkp_id, serialize($arrMembers));
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
	
				
				$tmp_memberarray = array('multi'=>$member_hash['multi'], 'single'=>$member_hash['single']);
				
				$sort_list_lastitemdate = array('multi'=>array(), 'single'=>array());
				$sort_list_lastitemid = array('multi'=>array(), 'single'=>array());
				$sort_list_member = array('multi'=>array(), 'single'=>array());
				
				//---MULTI--------------------------------------------------------------------
				foreach($member_hash['multi'] as $member_id){
					//Get latest item date
					$latest_item = $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, true));
					$last_raid =  $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, true));

					$sort_list_lastitemdate['multi'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, true)) : 0;
					$sort_list_lastitemid['multi'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item', array($member_id, $mdkp_id, true)) : 0;
					
					$sort_list_member['multi'][] = $member_id;
					unset($tmp_memberarray['multi'][$member_id]);
				}

				array_multisort($sort_list_lastitemdate['multi'], SORT_ASC, $sort_list_lastitemid['multi'], SORT_ASC, $sort_list_member['multi']);
				
				//Position for member with items
				$i = 1;
				foreach ($sort_list_member['multi'] as $member_id){
					$this->sk_list['multi'][$mdkp_id][$member_id] = $i++;
				}
				
				//---SINGLE--------------------------------------------------------------------
				foreach($member_hash['single'] as $member_id){
					//Get latest item date
					$latest_item = $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, false));
					$last_raid =  $this->pdh->get('member_dates', 'last_raid', array($member_id, $mdkp_id, false));
					

					$sort_list_lastitemdate['single'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item_date', array($member_id, $mdkp_id, false)): 0;
					$sort_list_lastitemid['single'][$member_id] = ($latest_item) ? $this->pdh->get('member_dates', 'last_item', array($member_id, $mdkp_id, false)): 0;
					
					$sort_list_member['single'][] = $member_id;
					unset($tmp_memberarray['single'][$member_id]);
					
				}
				array_multisort($sort_list_lastitemdate['single'], SORT_ASC, $sort_list_lastitemid['single'], SORT_ASC, $sort_list_member['single']);
				//Position for member with items
				$i = 1;
				foreach ($sort_list_member['single'] as $member_id){
					$this->sk_list['single'][$mdkp_id][$member_id] = $i++;
				}
			}

			$this->pdc->put('pdh_suicide_kings_bottom_table', $this->sk_list, null);
		}

		public function sort_item_list($a, $b){
			$compResult = $this->pdh->comp('item', 'date', 1, array($a), array($b));
			if ($compResult == 0){
				return ($a < $b) ? -1 : 1;
			}
			return $compResult;
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