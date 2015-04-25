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

if(!class_exists('pdh_r_item')){
	class pdh_r_item extends pdh_r_generic{
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		public $default_lang = 'english';
		public $items;

		public $hooks = array(
			'item_update',
			'itempool_update',
		);

		public $presets = array(
			'idate' => array('date', array('%item_id%'), array()),
			'ilink' => array('link', array('%item_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'),	array()),
			'ilink_itt' => array('link_itt', array('%item_id%', '%link_url%', '%link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%', false, '%use_controller%'), array()),
			'iraidlink' => array('raidlink', array('%item_id%', '%raid_link_url%', '%raid_link_url_suffix%', '%use_controller%'), array()),
			'iraididlink' => array('raididlink', array('%item_id%', '%raid_link_url%', '%raid_link_url_suffix%', '%use_controller%'), array()),
			'iname' => array('name', array('%item_id%'), array()),
			'ipoolname' => array('itempool_name', array('%item_id%'), array()),
			'ivalue' => array('value', array('%item_id%'), array()),
			'idroprate' => array('droprate', array('%item_id%'), array()),
			'ibuyername' => array('buyer_name', array('%item_id%'), array()),
			'ibuyerlink' => array('buyer_link', array('%item_id%', '%member_link_url%', '%member_link_url_suffix%', '%use_controller%'), array()),
			'ibuyers' => array('m4igk4i', array('%item_id%'), array()),
			'itemsedit'=>array('editicon', array('%item_id%', '%link_url%', '%link_url_suffix%'),	array()),
		);

		private $decayed = array();
		
		//Trunks
		private $index = array();
		private $objPagination = null;

		//Finished
		public function reset($affected_ids=array()){
			//tell apas which ids to delete
			$apaAffectedIDs = (empty($affected_ids) && !empty($this->index)) ? $this->index : $affected_ids;
			$this->apa->enqueue_update('item', $apaAffectedIDs);
			
			$affected_ids = (empty($affected_ids) || !$affected_ids) ? false : $affected_ids;
			$this->objPagination = register("cachePagination", array("items", "item_id", "__items", array(), 250));
			return $this->objPagination->reset($affected_ids);
		}
		
		//Finished
		public function init(){
			$this->objPagination = register("cachePagination", array("items", "item_id", "__items", array(), 250));
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
		public function get_ids_by_name($name){
			return $this->objPagination->search("item_name", $name, false);
		}

		//Finished
		public function get_ids_by_ingameid($ingame_id){
			return $this->objPagination->search("game_itemid", $ingame_id, false);
		}
		
		//Finished
		public function get_name($id){
			return $this->objPagination->get($id, 'item_name');
		}

		//Finished
		public function get_value($id, $dkp_id=0, $date=0){
			if($dkp_id ) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('item', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->objPagination->get($id, 'item_value'), 'date' => $this->objPagination->get($id, 'item_date'));
					$val = $this->apa->get_decay_val('item', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? (float)$val : (float)$this->objPagination->get($id, 'item_value');
		}
		
		//Finished
		public function get_caption_value($dkp_id=0) {
			$caption = '';
			if($dkp_id && $this->apa->is_decay('item', $dkp_id)) $caption = $this->apa->get_caption('item', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('item', 'value');
		}
		
		//Finished
		public function get_html_value($id, $dkp_id=0){
			return '<span class="negative">' . runden($this->get_value($id, $dkp_id)) . '</span>';
		}
		
		//Finished
		public function get_buyer($id){
			return $this->objPagination->get($id, 'member_id');
		}

		//Finished
		public function get_buyer_name($id) {
			return $this->pdh->get('member', 'name', array($this->get_buyer($id)));
		}
		
		//Finished
		public function get_html_buyer_name($id) {
			return $this->pdh->geth('member', 'name', array($this->get_buyer($id)));
		}
		
		//Finished
		public function get_buyer_link($id){
			return $this->get_buyer_name($id);
		}
		
		//Finished
		public function get_html_buyer_link($id, $base_url, $url_suffix = '', $blnUseController=false){
			$intBuyer = $this->get_buyer($id);
			return $this->pdh->geth('member', 'memberlink', array($intBuyer, $base_url, $url_suffix, false, false, true, $blnUseController));
		}

		//Finished
		public function get_date($id){
			return $this->objPagination->get($id, 'item_date');
		}

		//Finished
		public function get_html_date($id){
			return $this->time->user_date($this->get_date($id));
		}

		//Finished
		public function get_raid_id($id){
			return $this->objPagination->get($id, 'raid_id');
		}

		//Finished
		public function get_raid_name($item_id){
			return $this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id)));
		}

		//Finished
		public function get_raidlink($item_id, $base_url, $url_suffix = '', $blnUseController=false){
			return $this->pdh->get('raid', 'raidlink', array($this->get_raid_id($item_id), $base_url, $url_suffix, $blnUseController));
		}

		//Finished
		public function get_html_raididlink($item_id, $base_url, $url_suffix, $blnUseController=false) {
			return '<a href="'.$this->get_raidlink($item_id, $base_url, $url_suffix, $blnUseController).'">#ID:'.$this->get_raid_id($item_id).' - '.$this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id))).' '.date(/*$this->user->style['date_notime_short']*/'d.m.y', $this->pdh->get('raid', 'date', array($this->get_raid_id($item_id)))).'</a>';
		}

		//Finished
		public function get_html_raidlink($item_id, $base_url, $url_suffix, $blnUseController=false){
			return '<a href="'.$this->get_raidlink($item_id, $base_url, $url_suffix, $blnUseController).'">'.$this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id))).'</a>';
		}

		//Finished
		public function comp_raidlink($params1, $params2){
			return ($this->get_raid_name($params1[0]) < $this->get_raid_name($params2[0])) ? -1  : 1 ;
		}

		//Finished
		public function get_game_itemid($id){
			return $this->objPagination->get($id, 'game_itemid');
		}

		private $items_of_raid = array();

		//Finished
		public function get_itemsofraid($raid_id){
			if(!isset($this->items_of_raid[$raid_id])){
				$arrIDs = $this->objPagination->search("raid_id", $raid_id);
				$this->items_of_raid[$raid_id] = $arrIDs;
			}
			return $this->items_of_raid[$raid_id];
		}

		//Finished
		public function get_group_key($item_id){
			return $this->objPagination->get($item_id, 'item_group_key');
		}

		//Finished
		public function get_ids_of_group_key($group_key){
			$ids = $this->objPagination->search("item_group_key", $group_key);
			return $ids;
		}

		//Finished
		public function get_itempool_id($item_id){
			return $this->objPagination->get($item_id, 'itempool_id');
		}

		//Finished
		public function get_itempool_name($item_id){
			return $this->pdh->get('itempool', 'name', array($this->get_itempool_id($item_id)));
		}
		
		//Finished
		public function get_item_color($item_id){
			return $this->objPagination->get($item_id, 'item_color');
		}

		//Finished
		public function get_item_ids_of_itempool($itempool_id){
			$ids = $this->objPagination->search("itempool_id", $itempool_id);
			return $ids;
		}

		//Finished
		public function get_link($item_id, $baseurl, $url_suffix='', $blnUseController=false){
			if ($blnUseController  && $blnUseController !== '%use_controller%') return $baseurl.register('routing')->clean($this->get_name($item_id)).'-i'.$item_id.register('routing')->getSeoExtension().$this->SID.$url_suffix;
			return $baseurl.$this->SID.'&amp;i='.$item_id.$url_suffix;
		}

		//Finished
		public function get_html_link($item_id, $baseurl, $url_suffix='', $blnUseController=false){
			return "<a href='".$this->get_link($item_id, $baseurl, $url_suffix, $blnUseController)."'>".$this->get_name($item_id)."</a>";
		}

		//Finished
		public function get_editicon($item_id, $baseurl, $url_suffix=''){
			$out = "<a href='".$this->get_link($item_id, $baseurl, $url_suffix)."'>
				<i class='fa fa-pencil fa-lg' title='".$this->user->lang('edit')."'></i>
			</a>";
			
			$out .= '&nbsp;&nbsp;&nbsp;<a href="'.$this->get_link($item_id, $baseurl, '&copy=true').'">
				<i class="fa fa-copy fa-lg" title="'.$this->user->lang('copy').'"></i>
			</a>';
				
			return $out;
		}

		//Finished
		public function comp_link($params1, $params2){
			return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1  : 1 ;
		}

		//Finished
		public function get_itemids4memberid($member_id){
			$items4member = $this->objPagination->search("member_id", $member_id);
			return $items4member;
		}
		
		public function get_itemids4memberids($arrMemberIDs){
			$items4member = array();
			foreach($arrMemberIDs as $member_id){
				$arrItems = $this->get_itemids4memberid($member_id);
				if (is_array($arrItems)) $items4member = array_merge($items4member, $arrItems);
			}
			return array_unique($items4member);
		}
		
		//Finished
		public function get_itemids4userid($user_id){
			$arrMemberList = $this->pdh->get('member', 'connection_id', array($user_id));
			$items4member = array();
			if (is_array($this->index)){
				foreach($this->index as $item_id){
					$buyer = $this->get_buyer($item_id);
					if($buyer && in_array($buyer, $arrMemberList)){
						$items4member[] = $item_id;
					}
				}
			}
			return $items4member;
		}

		//Finished
		public function get_itemids4eventid($event_id) {
			$items4event = array();
			$raids = $this->pdh->get('raid', 'raidids4eventid', array($event_id));
			if (is_array($this->index)){
				foreach($this->index as $item_id) {		
					if(in_array($this->get_raid_id($item_id), $raids)) {
						$items4event[] = $item_id;
					}
				}
			}
			return $items4event;
		}

		//Finished
		public function get_m4igk4i($item_id) {
			return $this->pdh->aget('item', 'buyer_name', 0, array($this->get_ids_of_group_key($this->get_group_key($item_id))));
		}

		//Finished
		public function get_html_m4igk4i($item_id) {
			return implode(', ', $this->pdh->aget('item', 'html_buyer_name', 0, array($this->get_ids_of_group_key($this->get_group_key($item_id)))));
		}

		//Finished
		public function get_itt_itemname($item_id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if (!in_array($item_id, $this->index)) return false;

			if($this->config->get('infotooltip_use')) {
				$lang = (!$lang || $lang='') ? $this->user->lang('XML_LANG') : $lang;
				$ext = '';
				if($direct && !register('config')->get('infotooltip_own_enabled')) {
					$options = array(
						'url' => $this->server_path."infotooltip/infotooltip_feed.php?name=".urlencode(base64_encode($this->get_name($item_id)))."&lang=".$lang."&update=1&direct=1",
						'height' => '340',
						'width' => '400',
						'onclose' => $this->env->request
					);
					if($this->get_game_itemid($item_id) != '') {
						$options['url'] .= "&game_id=".$this->get_game_itemid($item_id);
					}
					$this->jquery->Dialog("infotooltip_update", "Item-Update", $options);
					$ext = '<br /><span style="cursor:pointer;" onclick="infotooltip_update()"><i class="fa fa-refresh"></i> Refresh Item</span>';
				}
				return infotooltip($this->get_name($item_id), $this->get_game_itemid($item_id), $lang, $direct, $onlyicon, $noicon, array(), $in_span, $this->get_item_color($item_id)).$ext;
			}
			return $this->get_name($item_id);
		}

		//Finished
		public function get_link_itt($item_id, $baseurl, $url_suffix='', $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false, $blnUseController=false) {
			$blnUseOwnTooltips = register('config')->get('infotooltip_own_enabled');
			if($blnUseOwnTooltips){
				$link = $this->get_itt_itemname($item_id, $lang, $direct, $onlyicon, $noicon, $in_span);
				$eqdkp_link = ' onclick="window.location=\''.$this->get_link($item_id, $baseurl, $url_suffix, $blnUseController).'\'; return false;"';

				return str_replace('data-eqdkplink=""', $eqdkp_link, $link);
			} else {
				return "<a href=\"".$this->get_link($item_id, $baseurl, $url_suffix, $blnUseController)."\">".$this->get_itt_itemname($item_id, $lang, $direct, $onlyicon, $noicon, $in_span)."</a>";
			}
		}

		//Finished
		public function comp_link_itt($params1, $params2) {
			return strcmp($this->get_name($params1[0]), $this->get_name($params2[0]));
		}
		
		//Finished
		public function get_search($search_value) {
			$arrSearchResults = array();
			
			$arrIDs = $this->objPagination->search("item_name", $search_value, true);
			foreach($arrIDs as $id){
				$arrSearchResults[] = array(
					'id'	=> $this->get_html_date($id),
					'name'	=> $this->get_name($id),
					'link'	=> $this->routing->build('items', $this->get_name($id), 'i'.$id),
				);
			}
			
			return $arrSearchResults;
		}
		
		public function get_droprate($item_id){
			$game_id = $this->get_game_itemid($item_id);
			$item_name = $this->pdh->get('item', 'name', array($item_id));
			$intItempoolID = $this->get_itempool_id($item_id);
			
			//Get Same Items
			$item_ids = array();
			if ($game_id > 1){
				$item_ids = $this->pdh->get('item', 'ids_by_ingameid', array($game_id));
			}else{
				$item_ids = $this->pdh->get('item', 'ids_by_name', array($item_name));
			}
			
			//Get Same Items in Itempool
			$arrPoolItems = array();
			foreach($item_ids as $iid){
				$itempool = $this->get_itempool_id($iid);
				if ($itempool === $intItempoolID) $arrPoolItems[] = $iid;
			}
			
			$intPoolItems = count($arrPoolItems);
			$intTotalPoolItems = count($this->get_item_ids_of_itempool($intItempoolID));
			
			if ($intTotalPoolItems === 0) return 0;
			return round(($intPoolItems / $intTotalPoolItems) * 100);
		}
		
		public function get_html_droprate($item_id){
			return $this->get_droprate($item_id).' %';
		}
	}
}
?>