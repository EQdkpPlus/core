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

if(!class_exists('pdh_r_adjustment')){
	class pdh_r_adjustment extends pdh_r_generic{
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public $default_lang = 'english';
		public $adjustments;

		public $hooks = array(
			'adjustment_update',
		);

		public $presets = array(
			'adj_date'			=> array('date', array('%adjustment_id%'), array()),
			'adj_reason'		=> array('reason', array('%adjustment_id%'), array()),
			'adj_value'			=> array('value', array('%adjustment_id%'), array()),
			'adj_reason_link'	=> array('link', array('%adjustment_id%', '%link_url%', '%link_url_suffix%'), array()),
			'adj_event'			=> array('event_name', array('%adjustment_id%'), array()),
			'adj_member'		=> array('member_name', array('%adjustment_id%'), array()),
			'adj_members'		=> array('m4agk4a', array('%adjustment_id%'), array()),
			'adj_raid'			=> array('raid_id', array('%adjustment_id%', '%raid_link_url%', '%raid_link_url_suffix%'), array()),
			'adjedit'			=> array('editicon', array('%adjustment_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		private $decayed = array();

		//Trunks
		private $index = array();
		private $objPagination = null;

		public function reset($affected_ids=array(), $strHook='', $arrAdditionalData=array()) {
			//tell apas which ids to delete
			if($strHook == ''){
				if(empty($affected_ids) && !empty($this->index)) $affected_ids = array_keys($this->index);
				$this->apa->enqueue_update('adjustment', $affected_ids);
			}

			$this->objPagination = register("cachePagination", array("adjustments", "adjustment_id", "__adjustments", array(), 100));
			return $this->objPagination->reset($affected_ids);
		}

		public function init(){
			$this->objPagination = register("cachePagination", array("adjustments", "adjustment_id", "__adjustments", array(), 100));
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

		public function get_value($adj_id, $dkp_id=0, $date=0) {
			if($dkp_id) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('adjustment', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $adj_id, 'value' =>  $this->objPagination->get($adj_id, 'adjustment_value'), 'date' => $this->get_date($adj_id));
					$val = $this->apa->get_value('adjustment', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? $val : $this->objPagination->get($adj_id, 'adjustment_value');
		}

		public function get_html_value($adj_id, $dkp_id=0) {
			return '<span class="' . color_item($this->get_value($adj_id, $dkp_id)) . '">'.runden($this->get_value($adj_id, $dkp_id)).'</span>';
		}

		public function get_apa_value($adj_id, $apa_id=false){
			$strApaValue =  $this->objPagination->get($adj_id, 'adjustment_apa_value');
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
			if($dkp_id && $this->apa->is_decay('adjustment', $dkp_id)) $caption = $this->apa->get_caption('adjustment', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('adjustment', 'value');
		}

		public function get_date($adj_id){
			return $this->objPagination->get($adj_id, "adjustment_date");
		}

		public function get_html_date($adj_id) {
			return $this->time->user_date($this->get_date($adj_id));
		}

		public function get_member($adj_id){
			return $this->objPagination->get($adj_id, "member_id");
		}

		public function get_member_name($adj_id) {
			return $this->pdh->get('member', 'name', array($this->get_member($adj_id)));
		}

		public function get_html_member_name($adj_id) {
			return $this->pdh->geth('member', 'name', array($this->get_member($adj_id)));
		}

		public function get_event($adj_id) {
			return $this->objPagination->get($adj_id, "event_id");
		}

		public function get_event_name($adj_id) {
			return $this->pdh->get('event', 'name', array($this->get_event($adj_id)));
		}

		public function get_reason($adj_id){
			$strReason = $this->objPagination->get($adj_id, "adjustment_reason");
			return strlen($strReason) ? $strReason : '';
		}

		public function get_raid_id($adj_id){
			return $this->objPagination->get($adj_id, "raid_id");
		}

		public function get_html_raid_id($adj_id, $base_url, $url_suffix = ''){
			return '<a href="'.$this->pdh->get('raid', 'raidlink', array($this->get_raid_id($adj_id), $base_url, $url_suffix)).'">'.$this->pdh->get('raid', 'event_name', array($this->get_raid_id($adj_id))).'</a>';
		}

		public function get_adjsofmember($member_id){
			return $this->objPagination->search("member_id", $member_id);
		}

		public function get_adjsofmembers($arrMemberIDs){
			$adj4member = array();
			foreach($arrMemberIDs as $member_id){
				$arrAdj = $this->get_adjsofmember($member_id);
				if (is_array($arrAdj)) $adj4member = array_merge($adj4member, $arrAdj);
			}
			return array_unique($adj4member);
		}

		public function get_adjsofuser($user_id){
			$arrMemberList = $this->pdh->get('member', 'connection_id', array($user_id));
			if(!$arrMemberList || count($arrMemberList) == 0) return array();

			$objQuery = $this->db->prepare("SELECT adjustment_id FROM __adjustments WHERE member_id :in")->in($arrMemberList)->execute();

			$adjustment_ids = array();

			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$adjustment_ids[] = (int)$row['adjustment_id'];
				}
			}

			return $adjustment_ids;
		}

		public function get_adjsofraid($raid_id, $blnGroupedByAdjKey=false){
			$adjustment_ids = $adjGrouped = array();
			if(is_array($this->index)){
				foreach($this->index as $adj_id){
					$intRaidID = $this->get_raid_id($adj_id);
					if($raid_id == $intRaidID){
						$adjustment_ids[] = $adj_id;
						$adjGrouped[$this->get_group_key($adj_id)] = $adj_id;
					}
				}
			}
			return ($blnGroupedByAdjKey) ? $adjGrouped : $adjustment_ids;
		}
		public function get_adjsofeventid($event_id) {
			return $this->objPagination->search("event_id", $event_id);
		}

		/**
		 * Returns the most recent (date wise) adjustment made for a given event and member.
		 * @param integer $event_id
		 * @param integer $member_id
		 * @param boolean $with_twink
		 * @return integer/boolean adjustment object id
		 */
		public function get_most_recent_adj_of_event_member($event_id, $member_id, $with_twink=true) {
			$member_ids = array($member_id);
			if($with_twink) {
				if(!$this->pdh->get('member', 'is_main', array($member_id))) {
					$member_id = $this->pdh->get('member', 'mainid', array($member_id));
				}

				$twinks = $this->pdh->get('member', 'other_members', $member_id);
				$member_ids = array_merge($member_ids, $twinks);
			}

			$objQuery = $this->db->prepare("SELECT adjustment_id FROM __adjustments WHERE member_id :in AND event_id = ? ORDER BY adjustment_date DESC LIMIT 1")->in($member_ids)->execute($event_id);

			if($objQuery){
				if($row = $objQuery->fetchAssoc()){
					return (int)$row['adjustment_id'];
				}
			}

			return false;
		}

		/**
		 * Returns the adjustments made for a given member within a time period.
		 * @param integer $member_id
		 * @param integer $from
		 * @param integer $to
		 * @param boolean $with_twink
		 * @return array adjustment object ids
		 */
		public function get_adj_of_member_in_interval($member_id, $from=0, $to=PHP_INT_MAX, $with_twink=true) {
			$member_ids = array($member_id);
			if($with_twink) {
				if(!$this->pdh->get('member', 'is_main', array($member_id))) {
					$member_id = $this->pdh->get('member', 'mainid', array($member_id));
				}

				$twinks = $this->pdh->get('member', 'other_members', $member_id);
				$member_ids = array_merge($member_ids, $twinks);
			}

			$objQuery = $this->db->prepare("SELECT adjustment_id FROM __adjustments WHERE member_id :in AND adjustment_date >= ? AND adjustment_date <= ?")->in($member_ids)->execute($from, $to);

			$adjustment_ids = array();

			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$adjustment_ids[] = (int)$row['adjustment_id'];
				}
			}

			return $adjustment_ids;
		}

		public function get_group_key($adj_id){
			return $this->objPagination->get($adj_id, "adjustment_group_key");
		}

		public function get_ids_of_group_key($group_key){
			return $this->objPagination->search("adjustment_group_key", $group_key);
		}

		public function get_link($adj_id, $baseurl, $url_suffix=''){
			return $baseurl.$this->SID.'&amp;a='.$adj_id.$url_suffix;
		}

		public function get_html_link($adj_id, $baseurl, $url_suffix='', $type='reason') {
			$allowed_types = array('reason', 'member');
			$type = (in_array($type, $allowed_types)) ? $type : 'reason';
			return "<a href='".$this->get_link($adj_id, $baseurl, $url_suffix)."'>".call_user_func_array(array($this, 'get_'.$type), array($adj_id))."</a>";
		}

		public function comp_link($params1, $params2){
			$method = (isset($params1[3]) && $params1[3] == 'member') ? 'get_member' : 'get_reason';
			return ($this->$method($params1[0]) < $this->$method($params2[0])) ? -1  : 1 ;

		}

		public function get_editicon($adj_id, $baseurl, $url_suffix='') {
			$out = "<a href='".$this->get_link($adj_id, $baseurl, $url_suffix)."'>
						<i class='fa fa-pencil fa-lg' title='".$this->user->lang('edit')."'></i>
					</a>";

			$out .= '&nbsp;&nbsp;&nbsp;<a href="'.$this->get_link($adj_id, $baseurl, '&copy=true').'">
				<i class="fa fa-copy fa-lg" title="'.$this->user->lang('copy').'"></i>
			</a>';

			return $out;
		}

		public function get_m4agk4a($adj_id) {
			return $this->pdh->aget('adjustment', 'member_name', 0, array($this->get_ids_of_group_key($this->get_group_key($adj_id))));
		}

		public function get_html_m4agk4a($adj_id) {
			return implode(', ', $this->pdh->aget('adjustment', 'html_member_name', 0, array($this->get_ids_of_group_key($this->get_group_key($adj_id)))));
		}

		public function comp_m4agk4a($params1, $params2){
			$members1 = implode(', ', $this->pdh->aget('adjustment', 'member_name', 0, array($this->get_ids_of_group_key($this->get_group_key($params1[0])))));
			$members2 = implode(', ', $this->pdh->aget('adjustment', 'member_name', 0, array($this->get_ids_of_group_key($this->get_group_key($params2[0])))));
			return ($members1 < $members2) ? -1  : 1 ;
		}
	}
}
