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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_raid')){
	class pdh_r_raid extends pdh_r_generic{
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public $default_lang = 'english';

		public $hooks = array(
			'raid_update'
		);

		public $presets = array(
			'rdate'			=> array('date', array('%raid_id%'), array()),
			'rlink'			=> array('raidlink', array('%raid_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'), array()),
			'revent'		=> array('event_name', array('%raid_id%'), array()),
			'rnote'			=> array('note', array('%raid_id%'), array()),
			'rattcount'		=> array('attendee_count', array('%raid_id%'), array()),
			'ritemcount'	=> array('item_count', array('%raid_id%'), array()),
			'rvalue'		=> array('value', array('%raid_id%'), array()),
			'raidedit'		=>array('editicon', array('%raid_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		private $decayed = array();

		//Trunks
		private $index = array();
		private $objPagination = null;


		public function reset($affected_ids=array(), $strHook='', $arrAdditionalData=array()){
			if($strHook == 'raid_update'){
				//tell apas which ids to delete
				$apaAffectedIDs = (empty($affected_ids) && !empty($this->index)) ? $this->index : $affected_ids;
				$this->apa->enqueue_update('raid', $apaAffectedIDs);
			}

			$affected_ids = (empty($affected_ids) || !$affected_ids) ? false : $affected_ids;
			$this->objPagination = register("cachePagination", array("raids", "raid_id", "__raids", array('additionalData' => "SELECT member_id, raid_id as object_key FROM __raid_attendees WHERE raid_id >= ? AND raid_id < ?"), 100));
			return $this->objPagination->reset($affected_ids);
		}


		public function init(){
			$this->objPagination = register("cachePagination", array("raids", "raid_id", "__raids", array('additionalData' => "SELECT member_id, raid_id as object_key FROM __raid_attendees WHERE raid_id >= ? AND raid_id < ?"), 100));
			$this->objPagination->initIndex();
			$this->index = $this->objPagination->getIndex();
		}


		public function get_id_list(){
			if(is_array($this->index)){
				return $this->index;
			}else{
				return array();
			}
		}


		public function get_added_by($id){
			return $this->objPagination->get($id, "raid_added_by");
		}


		public function get_updated_by($id){
			return $this->objPagination->get($id, "raid_updated_by");
		}


		public function get_event($id){
			return $this->objPagination->get($id, "event_id");
		}


		public function get_event_name($id){
			$strEventName = $this->pdh->get('event', 'name', array($this->get_event($id)));
			return (strlen($strEventName)) ? $strEventName : '';
		}


		public function get_date($id){
			return $this->objPagination->get($id, "raid_date");
		}


		public function get_html_date($id){
			$date = $this->get_date($id);
			return ( !empty($date) ) ? $this->time->user_date($date) : '&nbsp;';
		}


		public function get_value($id, $dkp_id=0, $date=0){
			if($dkp_id) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('raid', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->objPagination->get($id, 'raid_value'), 'date' => $this->get_date($id));
					$val = $this->apa->get_value('raid', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? $val : $this->objPagination->get($id, 'raid_value');
		}


		public function get_html_value($id, $dkp_id=0){
			return '<span class="positive">' . runden($this->get_value($id, $dkp_id)) . '</span>';
		}

		public function get_apa_value($raid_id, $apa_id=false){
			$strApaValue =  $this->objPagination->get($raid_id, 'raid_apa_value');
			if($strApaValue != ""){
				$arrApaValue = unserialize_noclasses($strApaValue);
				if($apa_id){
					if(isset($arrApaValue[$apa_id])) return $arrApaValue[$apa_id];
				} else {
					return $arrApaValue;
				}
			}
			return false;
		}


		public function get_caption_value($dkp_id=0) {
			$caption = '';
			if($dkp_id && $this->apa->is_decay('raid', $dkp_id)) $caption = $this->apa->get_caption('raid', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('raid', 'value');
		}


		public function get_note($id){
			return $this->objPagination->get($id, 'raid_note');
		}


		public function get_html_note($id){
			return $this->get_note($id);
		}


		public function get_additional_data($id){
			return $this->objPagination->get($id, 'raid_additional_data');
		}
		
		public function get_connected_attendance($id){
			return $this->objPagination->get($id, 'raid_connected_attendance');
		}
		

		public function get_html_additional_data($id){
			$strData = $this->get_additional_data($id);
			return $this->bbcode->toHTML($strData);
		}


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


		public function get_attendee_count($raid_id, $skip_special = true){
			return count($this->get_raid_attendees($raid_id, $skip_special));
		}


		public function get_raid_count(){
			return count($this->index);
		}


		public function get_item_count($raid_id){
			return count($this->pdh->get('item', 'itemsofraid', array($raid_id)));
		}


		public function get_raidids4memberid($member_id){
			$objQuery = $this->db->prepare("SELECT raid_id FROM __raid_attendees WHERE member_id=? ORDER BY raid_id DESC")->execute($member_id);
			$arrRaids = array();
			$arrIndexMap = array_flip($this->index);
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()){
					if(!isset($arrIndexMap[(int)$row['raid_id']])) continue;
					$arrRaids[] = $row['raid_id'];
				}
			}

			return $arrRaids;
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


		public function get_raidids4userid($user_id){
			$arrMemberList = $this->pdh->get('member', 'connection_id', array($user_id));

			$raids4member = array();
			if (is_array($arrMemberList) && count($arrMemberList) > 0){
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

		/**
		 * Returns the raids a given member attended within a time period.
		 * @param integer $member_id
		 * @param integer $from
		 * @param integer $to
		 * @param boolean $with_twink
		 * @return array raid object ids
		 */
		public function get_raids_of_member_in_interval($member_id, $from=0, $to=PHP_INT_MAX, $with_twink=true) {
			$member_ids = array($member_id);
			if($with_twink) {
				if(!$this->pdh->get('member', 'is_main', array($member_id))) {
					$member_id = $this->pdh->get('member', 'mainid', array($member_id));
				}

				$twinks = $this->pdh->get('member', 'other_members', $member_id);
				$member_ids = array_merge($member_ids, $twinks);
			}

			$objQuery = $this->db->prepare("SELECT r.raid_id AS raid_id FROM __raids AS r JOIN __raid_attendees AS ra ON r.raid_id = ra.raid_id WHERE ra.member_id :in AND r.raid_date >= ? AND r.raid_date <= ?")->in($member_ids)->execute($from, $to);

			$adjustment_ids = array();

			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$adjustment_ids[] = (int)$row['raid_id'];
				}
			}

			return $adjustment_ids;
		}


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


		public function get_raidlink($raid_id, $base_url, $url_suffix = '', $blnUseController=false){
			if ($blnUseController && $blnUseController !== '%use_controller%') return $base_url.register('routing')->clean($this->get_event_name($raid_id)).'-r'.$raid_id.register('routing')->getSeoExtension().$this->SID.$url_suffix;
			return $base_url.$this->SID . '&amp;r='.$raid_id.$url_suffix;
		}


		public function get_editicon($raid_id, $base_url, $url_suffix = ''){
			$out = '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix).'">
				<i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i>
			</a>';

			$out .= '&nbsp;&nbsp;&nbsp;<a href="'.$this->get_raidlink($raid_id, $base_url, '&copy=true').'">
				<i class="fa fa-copy fa-lg" title="'.$this->user->lang('copy').'"></i>
			</a>';

			return $out;
		}


		public function get_html_raidlink($raid_id, $base_url, $url_suffix='',$blnUseController=false){
			$strAdditional = "";
			if($base_url === 'manage_raids.php'){
				$strConnected = $this->get_connected_attendance($raid_id);
				if($strConnected){
					$arrConnected = json_decode($strConnected);
					
					if(count($arrConnected)) $strAdditional .= ' - '.'<i class="fa fa-lg fa-link"></i> '.count($arrConnected);
				}
			}
			
			return '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix, $blnUseController).'">'.$this->get_event_name($raid_id).$strAdditional.'</a>';
		}


		public function comp_raidlink($params1, $params2){
			return ($this->get_event_name($params1[0]) < $this->get_event_name($params2[0])) ? -1  : 1 ;
		}


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
