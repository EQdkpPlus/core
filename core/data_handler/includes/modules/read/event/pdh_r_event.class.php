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

if(!class_exists('pdh_r_event')){
	class pdh_r_event extends pdh_r_generic{

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
			'elink'		=> array('eventlink', array('%event_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'),	array()),
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
			
			$objQuery = $this->db->query("SELECT event_id, event_name, event_value, event_icon FROM __events;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->events[$row['event_id']]['name'] = $row['event_name'];
					$this->events[$row['event_id']]['value'] = $row['event_value'];
					$this->events[$row['event_id']]['icon'] = $row['event_icon'];
				}
				$this->pdc->put('pdh_events_table', $this->events, null);
			}
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
		
		public function comp_name($params1, $params2){
			return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1  : 1 ;
		}
		
		public function get_value($event_id){
			return $this->events[$event_id]['value'];
		}
		
		public function get_html_value($event_id){
			return runden($this->events[$event_id]['value']);
		}

		public function get_icon($event_id, $withpath=false){
			if($withpath) return $this->game->decorate('events', $event_id, array(), 0, true);
			return $this->events[$event_id]['icon'];
		}

		public function get_html_icon($event_id, $width=16){
			return $this->game->decorate('events', $event_id, array(), $width);
		}

		public function get_eventlink($event_id, $baseurl, $url_suffix='', $blnUseController=false){
			if($blnUseController && $blnUseController !== "%use_controller%") return $baseurl.register('routing')->clean($this->get_name($event_id)).'-e'.$event_id.register('routing')->getSeoExtension().$this->SID.$url_suffix;
			return $baseurl.$this->SID.'&amp;event_id='.$event_id.$url_suffix;
		}

		public function get_html_eventlink($event_id, $baseurl, $url_suffix='', $blnUseController=false){
			return "<a href='".$this->get_eventlink($event_id, $baseurl, $url_suffix, $blnUseController)."'>".$this->get_name($event_id)."</a>";
		}
		
		public function comp_eventlink($params1, $params2){
			return ($this->get_name($params1[0]) < $this->get_name($params2[0])) ? -1  : 1 ;
		}

		public function get_editicon($event_id, $baseurl, $url_suffix=''){
			return "<a href='".$this->get_eventlink($event_id, $baseurl, $url_suffix)."'><i class='fa fa-pencil fa-lg' title='".$this->user->lang('edit')."'></i></a>";
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
							'link'	=> $this->routing->build('events', $value['name'], $id),
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}
}
?>