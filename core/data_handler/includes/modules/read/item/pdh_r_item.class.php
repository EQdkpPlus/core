<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_item')){
	class pdh_r_item extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'db', 'user', 'pdh', 'time', 'jquery', 'config', 'apa' => 'auto_point_adjustments');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang = 'english';
		public $items;

		public $hooks = array(
			'item_update',
			'itempool_update',
		);

		public $presets = array(
			'idate' => array('date', array('%item_id%'), array()),
			'ilink' => array('link', array('%item_id%', '%link_url%', '%link_url_suffix%'),	array()),
			'ilink_itt' => array('link_itt', array('%item_id%', '%link_url%', '%link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'), array()),
			'iraidlink' => array('raidlink', array('%item_id%', '%raid_link_url%', '%raid_link_url_suffix%'), array()),
			'iraididlink' => array('raididlink', array('%item_id%', '%raid_link_url%', '%raid_link_url_suffix%'), array()),
			'iname' => array('name', array('%item_id%'), array()),
			'ipoolname' => array('itempool_name', array('%item_id%'), array()),
			'ivalue' => array('value', array('%item_id%'), array()),
			'ibuyername' => array('buyer_name', array('%item_id%'), array()),
			'ibuyers' => array('m4igk4i', array('%item_id%'), array()),
			'itemsedit'=>array('editicon', array('%item_id%', '%link_url%', '%link_url_suffix%'),	array()),
		);

		private $decayed = array();

		public function reset($affected_ids=array()){
			//tell apas which ids to delete
			if(empty($affected_ids) && !empty($this->items)) $affected_ids = array_keys($this->items);
			$this->apa->enqueue_update('item', $affected_ids);
			$this->pdc->del('pdh_item_table');
			$this->items = NULL;
		}

		public function init(){
			//get cached data
			$this->items = $this->pdc->get('pdh_item_table');
			if($this->items !== NULL){
				return true;
			}

			$sql = "SELECT item_id, item_name, member_id, item_value, item_date, raid_id, game_itemid, item_group_key, itempool_id, item_color FROM __items;";
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ){
				$this->items[$row['item_id']]['name'] = $row['item_name'];
				$this->items[$row['item_id']]['buyer'] = $row['member_id'];
				$this->items[$row['item_id']]['value'] = $row['item_value'];
				$this->items[$row['item_id']]['date'] = $row['item_date'];
				$this->items[$row['item_id']]['raid_id'] = $row['raid_id'];
				$this->items[$row['item_id']]['game_itemid'] = $row['game_itemid'];
				$this->items[$row['item_id']]['group_key'] = $row['item_group_key'];
				$this->items[$row['item_id']]['itempool_id'] = $row['itempool_id'];
				$this->items[$row['item_id']]['item_color'] = $row['item_color'];
			}
			$this->db->free_result($result);
			$this->pdc->put('pdh_item_table', $this->items, null);
		}

		public function get_id_list(){
			if(is_array($this->items)){
				return array_keys($this->items);
			}else{
				return array();
			}
		}

		public function get_ids_by_name($name){
			$item_ids = array();
			foreach($this->items as $item_id => $details){
				if($this->get_name($item_id) == $name){
					$item_ids[] = $item_id;
				}
			}
			return $item_ids;
		}

		public function get_ids_by_ingameid($ingame_id){
			$item_ids = array();
			foreach($this->items as $item_id => $details){
				if($this->get_game_itemid($item_id) == $ingame_id){
					$item_ids[] = $item_id;
				}
			}
			return $item_ids;
		}

		public function get_name($id){
			if(!isset($this->items[$id])) return false;
			return $this->items[$id]['name'];
		}

		public function get_value($id, $dkp_id=0, $date=0){
			if($dkp_id ) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('item', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->items[$id]['value'], 'date' => $this->items[$id]['date']);
					$val = $this->apa->get_decay_val('item', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? (float)$val : (float)$this->items[$id]['value'];
		}

		public function get_caption_value($dkp_id=0) {
			$caption = '';
			if($dkp_id && $this->apa->is_decay('item', $dkp_id)) $caption = $this->apa->get_caption('item', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('item', 'value');
		}

		public function get_html_value($id, $dkp_id=0){
			return '<span class="negative">' . runden($this->get_value($id, $dkp_id)) . '</span>';
		}
		
		public function get_buyer($id){
			return $this->items[$id]['buyer'];
		}

		public function get_buyer_name($id) {
			return $this->pdh->get('member', 'name', array($this->get_buyer($id)));
		}

		public function get_html_buyer_name($id) {
			return $this->pdh->geth('member', 'name', array($this->get_buyer($id)));
		}

		public function get_date($id){
			return $this->items[$id]['date'];
		}

		public function get_html_date($id){
			return $this->time->user_date($this->get_date($id));
		}

		public function get_raid_id($id){
			return $this->items[$id]['raid_id'];
		}

		public function get_raid_name($item_id){
			return $this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id)));
		}

		public function get_raidlink($item_id, $base_url, $url_suffix = ''){
			return $this->pdh->get('raid', 'raidlink', array($this->get_raid_id($item_id), $base_url, $url_suffix));
		}

		public function get_html_raididlink($item_id, $base_url, $url_suffix) {
			return '<a href="'.$this->get_raidlink($item_id, $base_url, $url_suffix).'">#ID:'.$this->get_raid_id($item_id).' - '.$this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id))).' '.date(/*$this->user->style['date_notime_short']*/'d.m.y', $this->pdh->get('raid', 'date', array($this->get_raid_id($item_id)))).'</a>';
		}

		public function get_html_raidlink($item_id, $base_url, $url_suffix){
			return '<a href="'.$this->get_raidlink($item_id, $base_url, $url_suffix).'">'.$this->pdh->get('raid', 'event_name', array($this->get_raid_id($item_id))).'</a>';
		}

		public function comp_raidlink($params1, $params2){
			return ($this->get_raid_name($params1[0]) < $this->get_raid_name($params2[0])) ? -1  : 1 ;
		}

		public function get_game_itemid($id){
			if(!isset($this->items[$id])) return false;
			return $this->items[$id]['game_itemid'];
		}

		private $items_of_raid = array();

		public function get_itemsofraid($raid_id){
			if(!isset($this->items_of_raid[$raid_id])){
				$itemids = array();
				if(is_array($this->items)){
					foreach($this->items as $id => &$item){
						if($raid_id == $item['raid_id']){
							$itemids[] = $id;
						}
					}
				}
				$this->items_of_raid[$raid_id] = $itemids;
			}
			return $this->items_of_raid[$raid_id];
		}

		public function get_group_key($item_id){
			return $this->items[$item_id]['group_key'];
		}

		public function get_ids_of_group_key($group_key){
			$ids = array();
			if(is_array($this->items)){
				foreach($this->items as $id => $det){
					if($det['group_key'] == $group_key){
						$ids[] = $id;
					}
				}
			}
			return $ids;
		}

		public function get_itempool_id($item_id){
			return $this->items[$item_id]['itempool_id'];
		}

		public function get_itempool_name($item_id){
			return $this->pdh->get('itempool', 'name', array($this->items[$item_id]['itempool_id']));
		}

		public function get_item_ids_of_itempool($itempool_id){
			$ids = array();
			if (is_array($this->items)){
				foreach($this->items as $id => $item){
					if($item['itempool_id'] == $itempool_id){
						$ids[] = $id;
					}
				}
			}
			return $ids;
		}

		public function get_link($item_id, $baseurl, $url_suffix=''){
			return $baseurl.$this->SID.'&amp;i='.$item_id.$url_suffix;
		}

		public function get_html_link($item_id, $baseurl, $url_suffix=''){
			return "<a href='".$this->get_link($item_id, $baseurl, $url_suffix)."'>".$this->get_name($item_id)."</a>";
		}

		public function get_editicon($item_id, $baseurl, $url_suffix=''){
			return "<a href='".$this->get_link($item_id, $baseurl, $url_suffix)."'>
			<img src='".$this->root_path."images/glyphs/edit.png' alt='".$this->user->lang('edit')."' title='".$this->user->lang('edit')."' />
			</a>";
		}

		public function comp_link($params1, $params2){
			return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1  : 1 ;
		}

		public function get_itemids4memberid($member_id){
			$items4member = array();
			if (is_array($this->items)){
				foreach($this->items as $item_id => $item_details){
					if($item_details['buyer'] == $member_id){
						$items4member[] = $item_id;
					}
				}
			}
			return $items4member;
		}

		public function get_itemids4eventid($event_id) {
			$items4event = array();
			$raids = $this->pdh->get('raid', 'raidids4eventid', array($event_id));
			if (is_array($this->items)){
				foreach($this->items as $item_id => $item_details) {
					if(in_array($item_details['raid_id'], $raids)) {
						$items4event[] = $item_id;
					}
				}
			}
			return $items4event;
		}

		public function get_m4igk4i($item_id) {
			return $this->pdh->aget('item', 'buyer_name', 0, array($this->get_ids_of_group_key($this->get_group_key($item_id))));
		}

		public function get_html_m4igk4i($item_id) {
			return implode(', ', $this->pdh->aget('item', 'html_buyer_name', 0, array($this->get_ids_of_group_key($this->get_group_key($item_id)))));
		}

		public function get_itt_itemname($item_id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if(!isset($this->items[$item_id])) return false;
			if($this->config->get('infotooltip_use')) {
				$lang = (!$lang) ? $this->user->lang('XML_LANG') : $lang;
				$ext = '';
				if($direct) {
					$options = array(
						'url' => $this->root_path."infotooltip/infotooltip_feed.php?name=".urlencode(base64_encode($this->get_name($item_id)))."&lang=".$lang."&update=1&direct=1",
						'height' => '340',
						'width' => '400',
						'onclose' => $_SERVER['REQUEST_URI']
					);
					if($this->get_game_itemid($item_id) >= 1) {
						$options['url'] .= "&game_id=".$this->get_game_itemid($item_id);
					}
					$this->jquery->Dialog("infotooltip_update", "Item-Update", $options);
					$ext = '<span style="cursor:pointer;" onclick="infotooltip_update()">Refresh</span>';
				}
				return infotooltip($this->get_name($item_id), $this->get_game_itemid($item_id), $lang, $direct, $onlyicon, $noicon, '', false, false, $in_span, $this->items[$item_id]["item_color"]).$ext;
			}
			return $this->get_name($item_id);
		}

		public function get_link_itt($item_id, $baseurl, $url_suffix='', $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			return "<a href=\"".$this->get_link($item_id, $baseurl, $url_suffix)."\">".$this->get_itt_itemname($item_id, $lang, $direct, $onlyicon, $noicon, $in_span)."</a>";
		}

		public function comp_link_itt($params1, $params2) {
			return strcmp($this->get_name($params1[0]), $this->get_name($params2[0]));
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->items)){
				foreach($this->items as $id => $value) {
					if(stripos($value['name'], $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id),
							'name'	=> $this->get_name($id),
							'link'	=> $this->root_path.'viewitem.php'.$this->SID.'&amp;i='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_item', pdh_r_item::__shortcuts());
?>