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

if(!class_exists('pdh_r_raid')){
	class pdh_r_raid extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'db', 'pdh', 'time', 'config', 'user', 'apa' => 'auto_point_adjustments');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang = 'english';
		public $raids;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update'
		);

		public $presets = array(
			'rdate' => array('date', array('%raid_id%'), array()),
			'rlink' => array('raidlink', array('%raid_id%', '%link_url%', '%link_url_suffix%'), array()),
			'revent' => array('event_name', array('%raid_id%'), array()),
			'rnote' => array('note', array('%raid_id%'), array()),
			'rattcount' => array('attendee_count', array('%raid_id%'), array()),
			'ritemcount' => array('item_count', array('%raid_id%'), array()),
			'rvalue' => array('value', array('%raid_id%'), array()),
			'raidedit'=>array('editicon', array('%raid_id%', '%link_url%', '%link_url_suffix%'), array()),
		);

		private $decayed = array();

		public function reset($affected_ids=array()){
			//tell apas which ids to delete
			if(empty($affected_ids) && !empty($this->raids)) $affected_ids = array_keys($this->raids);
			$this->apa->enqueue_update('raid', $affected_ids);
			$this->pdc->del('pdh_raids_table');
			$this->raids = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->raids = $this->pdc->get('pdh_raids_table');
			if($this->raids !== NULL){
				return true;
			}

			$this->raids = array();

			$sql = "SELECT raid_id, event_id, raid_date, raid_value, raid_note, raid_added_by, raid_updated_by FROM __raids;";
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ){
				$this->raids[$row['raid_id']]['event'] = $row['event_id'];
				$this->raids[$row['raid_id']]['date'] = $row['raid_date'];
				$this->raids[$row['raid_id']]['value'] = $row['raid_value'];
				$this->raids[$row['raid_id']]['note'] = $row['raid_note'];
				$this->raids[$row['raid_id']]['added_by'] = $row['raid_added_by'];
				$this->raids[$row['raid_id']]['updated_by'] = $row['raid_updated_by'];
				$this->raids[$row['raid_id']]['members'] = array();
			}
			$this->db->free_result($result);

			$sql = "SELECT raid_id, member_id FROM __raid_attendees;";
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ){
				//if there are any raid_ids in the raid_attendees table, that are not present in the raids table anymore
				if(isset($this->raids[$row['raid_id']])){
					$this->raids[$row['raid_id']]['members'][] = $row['member_id'];
				}
			}
			$this->db->free_result($result);
			$this->pdc->put('pdh_raids_table', $this->raids, null);
		}

		public function get_id_list(){
			return (is_array($this->raids)) ? array_keys($this->raids) : NULL;
		}

		public function get_added_by($id){
			return $this->raids[$id]['added_by'];
		}

		public function get_updated_by($id){
			return $this->raids[$id]['updated_by'];
		}

		public function get_event($id){
			return $this->raids[$id]['event'];
		}

		public function get_event_name($id){
			return (isset($this->raids[$id])) ? $this->pdh->get('event', 'name', array($this->raids[$id]['event'])) : '';
		}

		public function get_date($id){
			return $this->raids[$id]['date'];
		}

		public function get_html_date($id){
			return ( !empty($this->raids[$id]['date']) ) ? $this->time->user_date($this->raids[$id]['date']) : '&nbsp;';
		}

		public function get_value($id, $dkp_id=0, $date=0){
			if($dkp_id) {
				if(!isset($this->decayed[$dkp_id])) $this->decayed[$dkp_id] = $this->apa->is_decay('raid', $dkp_id);
				if($this->decayed[$dkp_id]) {
					$data = array('id' => $id, 'value' => $this->raids[$id]['value'], 'date' => $this->raids[$id]['date']);
					$val = $this->apa->get_decay_val('raid', $dkp_id, $date, $data);
				}
			}
			return (isset($val)) ? $val : $this->raids[$id]['value'];
		}

		public function get_html_value($id, $dkp_id=0){
			return '<span class="positive">' . runden($this->get_value($id, $dkp_id)) . '</span>';
		}

		public function get_caption_value($dkp_id=0) {
			$caption = '';
			if($dkp_id && $this->apa->is_decay('raid', $dkp_id)) $caption = $this->apa->get_caption('raid', $dkp_id);
			return ($caption) ? $caption : $this->pdh->get_lang('raid', 'value');
		}

		public function get_note($id){
			return $this->raids[$id]['note'];
		}

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

		public function get_raid_attendees($id, $skip_special = true){
			//special members like disenchanted or banked
			if($skip_special){
				return $this->raids[$id]['members'];
			}elseif(is_array($this->raids[$id]['members'])){
				foreach ($this->raids[$id]['members'] as $member_id){
					//special members like disenchanted or banked
					if(!$skip_special || !in_array($member_id, $this->config->get('special_members'))){
						$members[] = $member_id;
					}
				}
				return $members;
			}
		}

		public function get_attendee_count($raid_id){
			return count($this->raids[$raid_id]['members']);
		}

		public function get_raid_count(){
			return count($this->raids);
		}

		public function get_item_count($raid_id){
			return count($this->pdh->get('item', 'itemsofraid', array($raid_id)));
		}

		public function get_raidids4memberid($member_id){
			$raids4member = array();
			foreach($this->raids as $raid_id => $raid_details){
				if(is_array($raid_details['members']) && in_array($member_id, $raid_details['members'])){
					$raids4member[] = $raid_id;
				}
			}
			return $raids4member;
		}

		public function get_raidids4eventid($event_id) {
			$raids4event = array();
			foreach($this->raids as $raid_id => $raid_details) {
				if($event_id == $raid_details['event']) {
					$raids4event[] = $raid_id;
				}
			}
			return $raids4event;
		}

		public function get_raididsindateinterval($start_date, $end_date, $event_ids=false){
			$raids = array();
			foreach($this->raids as $raid_id => $raid_details){
				if($event_ids AND !in_array($raid_details['event'], $event_ids)) {
					continue;
				}
				if( ($raid_details['date'] >= $start_date) && ($raid_details['date'] <= $end_date) ){
					$raids[] = $raid_id;
				}
			}
			return $raids;
		}

		public function get_lastnraids($count = 1, $bydate = false){
			$raid_count		= count($this->raids);
			$raids			= array();
			$start			= $raid_count-$count;
			$raidids		= array_keys($this->raids);
			if(!$bydate){
				arsort($raidids);
			}else{
				foreach($raidids as $id){
					$dates[$id] = $this->raids[$id]['date'];
				}
				arsort($dates);
				unset($raidids);
				foreach($dates as $id => $date){
					$raidids[] = $id;
				}
			}
			for(; $start<$raid_count; $start++){
				$raids[] = $raidids[$start];
			}
			return $raids;
		}

		public function get_raidlink($raid_id, $base_url, $url_suffix = ''){
			return $base_url.$this->SID . '&amp;r='.$raid_id.$url_suffix;
		}

		public function get_editicon($raid_id, $base_url, $url_suffix = ''){
			return '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix).'">
			<img src="'.$this->root_path.'images/glyphs/edit.png" alt="'.$this->user->lang('edit').'" title="'.$this->user->lang('edit').'" />
			</a>';
		}

		public function get_html_raidlink($raid_id, $base_url, $url_suffix=''){
			return '<a href="'.$this->get_raidlink($raid_id, $base_url, $url_suffix).'">'.$this->get_event_name($raid_id).'</a>';
		}

		public function comp_raidlink($params1, $params2){
			return ($this->get_event_name($params1[0]) < $this->get_event_name($params2[0])) ? -1  : 1 ;
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->raids)){
				foreach($this->raids as $id => $value) {
					if(stripos($value['note'], $search_value) !== false OR stripos($this->get_event_name($id), $search_value) !== false ) {


						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id),
							'name'	=> $this->get_event_name($id),
							'link'	=> $this->root_path.'viewraid.php'.$this->SID.'&amp;r='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_raid', pdh_r_raid::__shortcuts());
?>