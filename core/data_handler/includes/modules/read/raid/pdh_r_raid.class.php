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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_raid')){
	class pdh_r_raid extends pdh_r_generic{
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public $default_lang = 'english';

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update'
		);

		public $presets = array(
			'rdate' => array('date', array('%raid_id%'), array()),
			'rlink' => array('raidlink', array('%raid_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'), array()),
			'revent' => array('event_name', array('%raid_id%'), array()),
			'rnote' => array('note', array('%raid_id%'), array()),
			'rattcount' => array('attendee_count', array('%raid_id%'), array()),
			'ritemcount' => array('item_count', array('%raid_id%'), array()),
			'rvalue' => array('value', array('%raid_id%'), array()),
			'raidedit'=>array('editicon', array('%raid_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		private $decayed = array();
		
		//Trunks
		private $index = array();
		private $objPagination = null;

		//Finished
		public function reset($affected_ids=array()){
			//tell apas which ids to delete
			$apaAffectedIDs = (empty($affected_ids) && !empty($this->index)) ? $this->index : $affected_ids;
			$this->apa->enqueue_update('raid', $apaAffectedIDs);
				
			$affected_ids = (empty($affected_ids) || !$affected_ids) ? false : $affected_ids;
			$this->objPagination = register("cachePagination", array("raids", "raid_id", "__raids", array('additionalData' => "SELECT member_id, raid_id as object_key FROM __raid_attendees WHERE raid_id >= ? AND raid_id"), 100));
			return $this->objPagination->reset($affected_ids);
		}
	
		//Finished
		public function init(){
			$this->objPagination = register("cachePagination", array("raids", "raid_id", "__raids", array('additionalData' => "SELECT member_id, raid_id as object_key FROM __raid_attendees WHERE raid_id >= ? AND raid_id"), 100));
			$this->objPagination->initIndex();
			$this->index = $this->objPagination->getIndex();
		}

		//Finished
		public function get_id_list(){
			if(is_array($this->index)){
				return $this->index;
			}else{
				return array();
			}
		}

		//Finished
		public function get_added_by($id){
			return $this->objPagination->get($id, "raid_added_by");
		}

		//Finished
		public function get_updated_by($id){
			return $this->objPagination->get($id, "raid_updated_by");
		}

		//Finished
		public function get_event($id){
			return $this->objPagination->get($id, "event_id");
		}

		//Finished
		public function get_event_name($id){
			$strEventName = $this->pdh->get('event', 'name', array($this->get_event($id)));
			return (strlen($strEventName)) ? $strEventName : '';
		}

		//Finished
		public function get_date($id){
			return $this->objPagination->get($id, "raid_date");
		}

		//Finished
		public function get_html_date($id){
			$date = $this->get_date($id);
			return ( !empty($date) ) ? $this->time->user_date($date) : '&nbsp;';
		}

		//Finished
		public function get_value($id, $dkp_id=0, $date=0){
			if($dkp_id) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('raid', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->objPagination->get($id, 'raid_value'), 'date' => $this->get_date($id));
					$val = $this->apa->get_decay_val('raid', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? $val : $this->objPagination->get($id, 'raid_value');
		}

		//Finished
		public function get_html_value($id, $dkp_id=0){
			return '<span class="positive">' . runden($this->get_value($id, $dkp_id)) . '</span>';
		}

		//Finished
		public function get_caption_value($dkp_id=0) {
			$caption = '';
			if($dkp_id && $this->apa->is_decay('raid', $dkp_id)) $caption = $this->apa->get_caption('raid', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('raid', 'value');
		}

		//Finished
		public function get_note($id){
			return $this->objPagination->get($id, 'raid_note');
		}

		//Finished
		public function get_html_note($id){
			/*if ( ($this->pm->check('bosssuite', PLUGIN_INSTALLED)) && ($this->config->get('bs_linkBL')) ){
				require_once ($this->root_path.'plugins/bosssuite/mods/note2link.php');
			}else{
				public function bl_note2link($raidnote, $raidname){
					return $raidnote;
				}
			}
			return bl_note2link($this->get_note($id), $this->get_event_name($id));*/
			return $this->get_note($id);
		}
		
		//Finished
		public function get_additional_data($id){
			return $this->objPagination->get($id, 'raid_additional_data');
		}
		
		public function get_html_additional_data($id){
			$strData = $this->get_additional_data($id);
			return $this->bbcode->toHTML($strData);
		}

		//Finished
		public function get_raid_attendees($id, $skip_special = true){
			$arrMembers = array();
			$arrAttendees = $this->objPagination->get($id, 'additional');
			if ($arrAttendees && is_array($arrAttendees)){
				foreach($arrAttendees as $val){
					if ($skip_special && is_array($this->config->get('special_members')) && in_array($val['member_id'], $this->config->get('special_members'))) continue;
					
					$arrMembers[] = $val['member_id'];
				}
			}
			return $arrMembers;
		}

		//Finished
		public function get_attendee_count($raid_id, $skip_special = true){
			return count($this->get_raid_attendees($raid_id, $skip_special));
		}

		//Finished
		public function get_raid_count(){
			return count($this->index);
		}

		//Finished
		public function get_item_count($raid_id){
			return count($this->pdh->get('item', 'itemsofraid', array($raid_id)));
		}

		//Finished
		public function get_raidids4memberid($member_id){
			$raids4member = array();
			foreach($this->index as $raid_id){
				$arrMembers = $this->get_raid_attendees($raid_id, false);
				if(is_array($arrMembers) && in_array($member_id, $arrMembers)){
					$raids4member[] = $raid_id;
				}
			}
			return $raids4member;
		}
		
		// get the raids of a member with certain item ids
		public function get_raidids4memberid_item($member_id, $item_ids){
			$item_ids		= (!is_array($item_ids)) ? array($item_ids) : $item_ids;
			$raids4member	= array();
			foreach($this->index as $raid_id){
				$arrMembers	= $this->get_raid_attendees($raid_id, false);
				$arrItems	= $this->pdh->get('item', 'itemsofraid', array($raid_id));
				if(is_array($arrMembers) && in_array($member_id, $arrMembers) && multi_array_search($arrItems, $item_ids)){
					$raids4member[]	= $raid_id;
				}
			}
			return $raids4member;
		}

		public function get_raidids4memberids($arrMemberIDs){
			$raids4member = array();
			foreach($arrMemberIDs as $member_id){
				$arrRaids = $this->get_raidids4memberid($member_id);
				if (is_array($arrRaids)) $raids4member = array_merge($raids4member, $arrRaids);
			}
			return array_unique($raids4member);
		}
		
		//Finished
		public function get_raidids4userid($user_id){
			$arrMemberList = $this->pdh->get('member', 'connection_id', array($user_id));
			
			$raids4member = array();
			if (is_array($arrMemberList) && count($arrMemberList)){
				foreach($this->index as $raid_id){
					$arrMembers = $this->get_raid_attendees($raid_id, false);
					if(is_array($arrMembers)){					
						foreach($arrMembers as $memberid){
							if (in_array($memberid, $arrMemberList)) $raids4member[] = $raid_id;
						}	
					}
				}
			}
			return $raids4member;
		}

		//Finished
		public function get_raidids4eventid($event_id) {
			return $this->objPagination->search("event_id", $event_id);
		}

		
		public function get_raididsindateinterval($start_date, $end_date, $event_ids=false){
			$objQuery = $this->db->prepare("SELECT raid_id,event_id FROM __raids WHERE raid_date >= ? AND raid_date <= ? ORDER BY raid_date DESC")->execute($start_date, $end_date);
			$arrRaids = array();
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()){
					if($event_ids AND !in_array($row['event_id'], $event_ids)) {
						continue;
					}
					
					$arrRaids[] = $row['raid_id'];
				}
			}

			return $arrRaids;
		}

		//Finished
		public function get_lastnraids($count = 1){
			$objQuery = $this->db->prepare("SELECT raid_id FROM __raids ORDER BY raid_date DESC")->limit($count)->execute();
			$arrRaids = array();
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrRaids[] = $row['raid_id'];
				}
			}
			
			return $arrRaids;
		}

		//Finished
		public function get_raidlink($raid_id, $base_url, $url_suffix = '', $blnUseController=false){
			if ($blnUseController && $blnUseController !== '%use_controller%') return $base_url.register('routing')->clean($this->get_event_name($raid_id)).'-r'.$raid_id.register('routing')->getSeoExtension().$this->SID.$url_suffix;
			return $base_url.$this->SID . '&amp;r='.$raid_id.$url_suffix;
		}

		//Finished
		public function get_editicon($raid_id, $base_url, $url_suffix = ''){
			$out = '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix).'">
				<i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i>
			</a>';
			
			$out .= '&nbsp;&nbsp;&nbsp;<a href="'.$this->get_raidlink($raid_id, $base_url, '&copy=true').'">
				<i class="fa fa-copy fa-lg" title="'.$this->user->lang('copy').'"></i>
			</a>';
			
			return $out;
		}

		//Finished
		public function get_html_raidlink($raid_id, $base_url, $url_suffix='',$blnUseController=false){
			return '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix, $blnUseController).'">'.$this->get_event_name($raid_id).'</a>';
		}

		//Finished
		public function comp_raidlink($params1, $params2){
			return ($this->get_event_name($params1[0]) < $this->get_event_name($params2[0])) ? -1  : 1 ;
		}

		//Finished
		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->index)){
				foreach($this->index as $id) {
					if(stripos($this->get_note($id), $search_value) !== false OR stripos($this->get_event_name($id), $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id),
							'name'	=> $this->get_event_name($id),
							'link'	=> $this->routing->build('raids', $this->get_event_name($id), 'r'.$id),
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}
}
?>