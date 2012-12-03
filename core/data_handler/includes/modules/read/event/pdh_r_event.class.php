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

if(!class_exists('pdh_r_event')){
	class pdh_r_event extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'db', 'user', 'pdh', 'config', 'game');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang = 'english';
		public $events = array();

		public $hooks = array(
			'event_update',
			'multidkp_update',
			'itempool_update'
		);

		public $presets = array(
			'ename'		=> array('name', array('%event_id%', '%no_root%'), array()),
			'eicon'		=> array('icon', array('%event_id%'), array()),
			'elink'		=> array('eventlink', array('%event_id%', '%link_url%', '%link_url_suffix%'),	array()),
			'emdkps'	=> array('multidkppools', array('%event_id%'),	array()),
			'eipools'	=> array('itempools',	array('%event_id%'), array()),
			'evalue'	=> array('value', array('%event_id%'), array()),
			'eventedit'	=> array('editicon', array('%event_id%', '%link_url%', '%link_url_suffix%'),	array()),
		);

		public function reset(){
			$this->pdc->del('pdh_events_table');
			$this->events = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->events = $this->pdc->get('pdh_events_table');
			if($this->events !== NULL){
				return true;
			}

			$this->events = array();
			$sql = "SELECT event_id, event_name, event_value, event_icon FROM __events;";
			$result = $this->db->query($sql);
			while( $row = $this->db->fetch_record($result)){
				$this->events[$row['event_id']]['name'] = $row['event_name'];
				$this->events[$row['event_id']]['value'] = $row['event_value'];
				$this->events[$row['event_id']]['icon'] = $row['event_icon'];
			}

			$this->db->free_result($result);
			$this->pdc->put('pdh_events_table', $this->events, null);
		}

		public function get_events(){
			return $this->events;
		}

		public function get_id_list(){
			return array_keys($this->events);
		}

		public function get_name($event_id){
			return (isset($this->events[$event_id])) ? $this->events[$event_id]['name'] : '';
		}

		public function get_html_name($event_id, $no_root=false) {
			return $this->get_html_icon($event_id, 16, 16, false, $no_root).' '.$this->get_name($event_id);
		}

		public function get_value($event_id){
			return $this->events[$event_id]['value'];
		}

		public function get_icon($event_id, $withpath=false, $fallback=false, $no_root=false){
			$root_path = ($no_root) ? '{ROOT_PATH}' : $this->root_path;
			$filepath = "games/".$this->config->get('default_game')."/events/".$this->events[$event_id]['icon'];
			if(is_file($this->root_path.$filepath)){
				return ($withpath) ? $root_path.$filepath : $this->events[$event_id]['icon'];
			}else{
				if($fallback){
					return ($withpath) ? $root_path."games/".$this->config->get('default_game')."/events/".'unknown.png' : 'unknown.png';
				}
			}
			return '';
		}

		public function get_html_icon($event_id, $width=16, $alt=true){
			$alt = ($alt) ? $this->get_name($event_id) : '';
			return $this->game->decorate('events', array($event_id, $width, false, $alt));
		}

		public function get_eventlink($event_id, $baseurl, $url_suffix=''){
			return $baseurl.$this->SID.'&amp;event_id='.$event_id.$url_suffix;
		}

		public function get_html_eventlink($event_id, $baseurl, $url_suffix=''){
			return "<a href='".$this->get_eventlink($event_id, $baseurl, $url_suffix)."'>".$this->get_name($event_id)."</a>";
		}

		public function get_editicon($event_id, $baseurl, $url_suffix=''){
			return "<a href='".$this->get_eventlink($event_id, $baseurl, $url_suffix)."'>
				<img src='".$this->root_path."images/glyphs/edit.png' alt='".$this->user->lang('edit')."' title='".$this->user->lang('edit')."' />
				</a>";
		}

		public function get_itempools($event_id){
			$mdkpids = $this->get_multidkppools($event_id);
			$ip_ids = array();
			foreach($mdkpids as $mdkpid){
				$itempool_ids = $this->pdh->get('multidkp', 'itempool_ids', array($mdkpid));
				foreach($itempool_ids as $ip_id){
					$ip_ids[] = $ip_id;
				}
			}
			return array_unique($ip_ids);
		}

		public function get_html_itempools($event_id){
			$ip_ids = $this->get_itempools($event_id);
			$names = array();
			foreach($ip_ids as $ip_id){
				$names[] = $this->pdh->get('itempool', 'name', array($ip_id));
			}
			return implode(', ', $names);
		}

		public function get_multidkppools($event_id){
			$mdkpids = $this->pdh->get('multidkp', 'mdkpids4eventid', array($event_id));
			return $mdkpids;
		}

		public function get_html_multidkppools($event_id){
			$mdkpids = $this->get_multidkppools($event_id);
			$names = array();
			foreach($mdkpids as $mdkpid){
				$names[] = $this->pdh->get('multidkp', 'name', array($mdkpid));
			}
			return implode(', ', $names);
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->events)){
				foreach($this->events as $id => $value) {
					if(stripos($value['name'], $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_icon($id),
							'name'	=> $this->get_name($id),
							'link'	=> $this->root_path.'viewevent.php'.$this->SID.'&amp;event_id='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_event', pdh_r_event::__shortcuts());
?>