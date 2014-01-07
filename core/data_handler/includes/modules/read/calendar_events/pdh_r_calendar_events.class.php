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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_calendar_events" ) ) {
	class pdh_r_calendar_events extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user', 'time', 'pdh'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $events;
		public $repeatable_events;

		public $calendar_event_wl = array(
			'raid_eventid', 'raid_value', 'invitedate', 'deadlinedate', 'raidmode', 'distribution', 'raidleader'
		);

		public $hooks = array(
			'calendar_events_update',
		);

		public $presets = array(
			'calevents_id'			=> array('roleid',			array('%calevent_id%'),	array()),
			'calevents_date'		=> array('date',			array('%calevent_id%'),	array()),
			'calevents_weekday'		=> array('html_weekday',	array('%calevent_id%'),	array()),
			'calevents_duration'	=> array('duration',		array('%calevent_id%'),	array()),
			'calevents_name'		=> array('name',			array('%calevent_id%'),	array()),
			'calevents_creator'		=> array('creator',			array('%calevent_id%'),	array()),
			'calevents_calendar'	=> array('calendar',		array('%calevent_id%'),	array()),
			'calevents_edit'		=> array('edit',			array('%calevent_id%'),	array()),
			'calevents_start_time'	=> array('html_time_start', array('%calevent_id%'), array()),
			'calevents_end_time'	=> array('html_time_end',	array('%calevent_id%'), array()),
			'calevents_raid_event'	=> array('raid_event', 		array('%calevent_id%'), array()),
			'calevents_note'		=> array('notes', 			array('%calevent_id%'), array()),
			'calevents_detailslink'	=> array('detailslink', 	array('%calevent_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_calendar_events_table');
			$this->pdc->del('pdh_calendar_repeatable_events_table');
			$this->events = NULL;
			$this->repeatable_events = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->events				= $this->pdc->get('pdh_calendar_events_table');
			$this->repeatable_events	= $this->pdc->get('pdh_calendar_repeatable_events_table');
			if($this->events !== NULL && $this->repeatable_events !== NULL){
				return true;
			}

			$query = $this->db->query("SELECT * FROM __calendar_events");
			while ( $row = $this->db->fetch_record($query) ){
				$this->events[$row['id']] = array(
					'id'					=> $row['id'],
					'calendar_id'			=> $row['calendar_id'],
					'name'					=> $row['name'],
					'creator'				=> $row['creator'],
					'timestamp_start'		=> $row['timestamp_start'],
					'timestamp_end'			=> $row['timestamp_end'],
					'allday'				=> $row['allday'],
					'private'				=> $row['private'],
					'visible'				=> $row['visible'],
					'closed'				=> $row['closed'],
					'notes'					=> $row['notes'],
					'repeating'				=> $row['repeating'],
					'cloneid'				=> $row['cloneid'],
				);
				$this->events[$row['id']]['extension'] = unserialize($row['extension']);

				// set the repeatable array
				if($row['repeating'] != 'none'){
					$parentid	= ($row['cloneid'] > 0) ? $row['cloneid'] : $row['id'];
					$this->repeatable_events[$parentid][] = $row['id'];
				}
			}
			if($query){
				$this->db->free_result($query);
				$this->pdc->put('pdh_calendar_events_table', $this->events, null);
				$this->pdc->put('pdh_calendar_repeatable_events_table', $this->repeatable_events, null);
			}
		}

		public function get_id_list($raids_only=false, $start_date = 0, $end_date = 9999999999){
			$ids = array();
			if(($start_date != 0) || ($end_date != 9999999999)){
				$query = $this->db->query("SELECT id FROM __calendar_events WHERE timestamp_start BETWEEN '".$this->db->escape($start_date)."' AND '".$this->db->escape($end_date)."' OR timestamp_end BETWEEN '".$this->db->escape($start_date)."' AND '".$this->db->escape($end_date)."'");
				if($raids_only) {
					while ( $row = $this->db->fetch_record($query) ){
						if($this->get_calendartype($row['id']) == '1'){
							$ids[] = $row['id'];
						}
					}
				}else{
					while ( $row = $this->db->fetch_record($query) ){
						$ids[] = $row['id'];
					}
				}
			}else if(isset($this->events)){
				$ids = array_keys($this->events);
				if($raids_only) {
					foreach($ids as $key => $id) {
						if($this->get_calendartype($id) != '1' || $this->events[$id]['timestamp_end'] < $this->time->time) unset($ids[$key]);
					}
				}
			}

			return $ids;
		}

		public function get_repeatable_events($cloneid=0){
			return ($cloneid > 0) ? $this->repeatable_events[$cloneid] : $this->repeatable_events;
		}

		public function get_cloneid($id=''){
			return 	(isset($this->events[$id]['cloneid'])) ? $this->events[$id]['cloneid'] : 0;
		}

		public function get_data($id=''){
			return 	($id) ? $this->events[$id] : $this->events;
		}

		public function get_raidstatus($id){
			return (isset($this->events[$id]['closed'])) ? $this->events[$id]['closed'] : '';
		}

		public function get_calendar_id($id){
			return 	$this->events[$id]['calendar_id'];
		}

		public function get_calendar($id){
			return $this->pdh->get('calendars', 'name', array($this->events[$id]['calendar_id']));
		}

		public function get_name($id){
			$extension = $this->events[$id]['extension'];
			if(isset($extension['calendarmode']) && $extension['calendarmode'] == 'raid'){
				$raidname = $this->pdh->get('event', 'name', array($extension['raid_eventid']));
				return ($raidname) ? $raidname : $this->user->lang('raidevent_raid_notitle');
			}else{
				return	$this->events[$id]['name'];
			}
		}

		public function get_creator($id){
			return ($this->events[$id]['creator']) ? $this->pdh->get('user', 'name', array($this->events[$id]['creator'])) : '';
		}

		public function get_date($id) {
			return $this->events[$id]['timestamp_start'];
		}

		public function get_html_date($id) {
			return $this->time->user_date($this->events[$id]['timestamp_start']);
		}

		public function get_html_weekday($id) {
			return $this->time->date("l", $this->events[$id]['timestamp_start']);
		}

		public function get_duration($id){
			if($this->events[$id]['allday']){
				return $this->user->lang('calendar_allday');
			}else{
				if($this->events[$id]['timestamp_end'] && $this->events[$id]['timestamp_start']){
					$seconds = $this->events[$id]['timestamp_end'] - $this->events[$id]['timestamp_start'];
					return sprintf('%02d:%02d:%02d', floor($seconds/3600), floor($seconds/60) % 60, $seconds % 60);

				}else{
					return '--';
				}
			}

		}

		public function get_time_start($id){
			return 	$this->events[$id]['timestamp_start'];
		}

		public function get_html_time_start($id) {
			return $this->time->user_date($this->events[$id]['timestamp_start'], false, true);
		}

		public function get_time_end($id){
			return $this->events[$id]['timestamp_end'];
		}

		public function get_html_time_end($id) {
			return $this->time->user_date($this->events[$id]['timestamp_end'], false, true);
		}

		public function get_allday($id){
			return 	$this->events[$id]['allday'];
		}

		public function get_private($id){
			return 	$this->events[$id]['private'];
		}

		public function get_calendartype($id){
			return (isset($this->events[$id]['calendar_id'])) ? $this->pdh->get('calendars', 'type', array($this->events[$id]['calendar_id'])) : '';
		}

		public function get_visible($id){
			return 	$this->events[$id]['visible'];
		}

		public function get_extension($id){
			return 	$this->events[$id]['extension'];
		}

		public function get_notes($id){
			return 	$this->events[$id]['notes'];
		}

		public function get_repeating($id){
			return ($this->events[$id]['repeating']) ? $this->events[$id]['repeating'] : 'none';
		}

		public function get_detailslink($id){
			return '<a href="'.$this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;eventid='.$id.'" class="arrowright"></a>';
		}

		public function get_edit($id){
			return '<img src="'.$this->root_path.'images/glyphs/edit.png" alt="'.$this->user->lang('calendar_edit').'" title="'.$this->user->lang('calendar_edit').'" onclick="editEvent(\''.$id.'\')"/>';
		}

		public function get_raid_event($id){
			if(!isset($this->events[$id]['extension']['raid_eventid'])) return false;
			$raideventname	= $this->pdh->get('event', 'name', array($this->events[$id]['extension']['raid_eventid']));
			$raideventname	= ($this->get_raidstatus($id) == '1') ? '<span class="linethrough">'.$raideventname.'</span>' : $raideventname;
			return $this->pdh->geth('event', 'icon', array($this->events[$id]['extension']['raid_eventid'])).' '.$raideventname;
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->events)){
				foreach($this->events as $id => $value) {
					if(stripos($this->get_name($id), $search_value) !== false OR stripos($value['notes'], $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id).' '.$this->get_html_time_start($id),
							'name'	=> $this->get_name($id),
							'link'	=> $this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;event_id='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_calendar_events', pdh_r_calendar_events::__shortcuts());
?>