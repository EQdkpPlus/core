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

if ( !class_exists( "pdh_r_calendar_events" ) ) {
	class pdh_r_calendar_events extends pdh_r_generic{

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
			$this->pdc->del('pdh_calendar_events_table.events');
			$this->pdc->del('pdh_calendar_events_table.repeatable');
			$this->pdc->del('pdh_calendar_events_table.timestamps');
			$this->events				= NULL;
			$this->repeatable_events	= NULL;
			$this->event_timestamps		= NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->events				= $this->pdc->get('pdh_calendar_events_table.events');
			$this->repeatable_events	= $this->pdc->get('pdh_calendar_events_table.repeatable');
			$this->event_timestamps		= $this->pdc->get('pdh_calendar_events_table.timestamps');
			if($this->events !== NULL && $this->repeatable_events !== NULL && $this->event_timestamps !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __calendar_events");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
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
					$this->events[$row['id']]['extension']	= unserialize($row['extension']);
					$this->event_timestamps[$row['id']]		= $row['timestamp_start'];
	
					// set the repeatable array
					if($row['repeating'] != 'none'){
						$parentid	= ($row['cloneid'] > 0) ? $row['cloneid'] : $row['id'];
						$this->repeatable_events[$parentid][] = $row['id'];
					}
				}
				
				// sort the timestamps
				if(is_array($this->event_timestamps)) asort($this->event_timestamps);
	
				// set the cache
				$this->pdc->put('pdh_calendar_events_table.events', $this->events, null);
				$this->pdc->put('pdh_calendar_events_table.repeatable', $this->repeatable_events, null);
				$this->pdc->put('pdh_calendar_events_table.timestamps', $this->event_timestamps, null);
			}

		}

		public function get_id_list($raids_only=false, $start_date = 0, $end_date = 9999999999, $calfilter=false){
			$ids = array();
			if(($start_date != 0) || ($end_date != 9999999999)){
				$sqlstring	 = "SELECT id FROM __calendar_events WHERE";
				$sqlstring	.= (is_array($calfilter)) ? ' (calendar_id IN ('.implode(",", $calfilter).')) AND' : '';
				$sqlstring	.= " ((timestamp_start BETWEEN ".$this->db->escapeString($start_date)." AND ".$this->db->escapeString($end_date).") OR (timestamp_end BETWEEN ".$this->db->escapeString($start_date)." AND ".$this->db->escapeString($end_date)."))";

				$query = $this->db->query($sqlstring);
				if ($query){
					if($raids_only) {
						$what2filter	= (($raids_only === 'appointments') ? '2' : '1');
						while ( $row = $query->fetchAssoc() ){
							if($this->get_calendartype($row['id']) == $what2filter){
								$ids[] = $row['id'];
							}
						}
					}else{
						while ( $row = $query->fetchAssoc() ){
							$ids[] = $row['id'];
						}
					}
				}
			}else if(isset($this->events)){
				$ids = array_keys($this->events);
				if($raids_only) {
					foreach($ids as $key => $id) {
						if($this->get_calendartype($id) != '1' || $this->events[$id]['timestamp_end'] < $this->time->time) unset($ids[$key]);

						// us the claendarfilter
						if(is_array($calfilter) && !in_array($this->get_calendar_id($id), $calfilter)) unset($ids[$key]);
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
				return	isset($this->events[$id]) ? $this->events[$id]['name'] : '';
			}
		}

		public function get_creator($id){
			return ($this->events[$id]['creator']) ? $this->pdh->get('user', 'name', array($this->events[$id]['creator'])) : '';
		}

		public function get_creatorid($id){
			return ($this->events[$id]['creator']) ? $this->events[$id]['creator'] : 0;
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
			return (isset($this->events[$id]['timestamp_start']) ? $this->events[$id]['timestamp_start'] : 0);
		}

		public function get_html_time_start($id) {
			return $this->time->user_date($this->events[$id]['timestamp_start'], false, true);
		}

		public function get_time_end($id, $exclusivefix=false){
			return ($exclusivefix) ? $this->time->adddays($this->events[$id]['timestamp_end']) : $this->events[$id]['timestamp_end'];
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
			return '<a href="'.$this->routing->build('calendarevent', $this->get_name($id), $id).'"><i class="fa fa-lg fa-arrow-right"></i></a>';
		}

		public function get_edit($id){
			return '<i class="fa fa-pencil fa-lg hand" title="'.$this->user->lang('calendar_edit').'" onclick="editEvent(\''.$id.'\')"></i>';
		}

		public function get_raid_event($id){
			if(!isset($this->events[$id]['extension']['raid_eventid'])) return false;
			$raideventname	= $this->pdh->get('event', 'name', array($this->events[$id]['extension']['raid_eventid']));
			$raideventname	= ($this->get_raidstatus($id) == '1') ? '<span class="linethrough">'.$raideventname.'</span>' : $raideventname;
			return $this->pdh->geth('event', 'icon', array($this->events[$id]['extension']['raid_eventid'])).' '.$raideventname;
		}

		public function get_next_event($id){
			$this->helper_set_pointer($this->event_timestamps, $id);
			next($this->event_timestamps);
			$next_eventid	= key($this->event_timestamps);
			reset($this->event_timestamps);
			return ($next_eventid > 0 && $next_eventid != $id) ? $next_eventid : false;
		}
		
		public function get_next_raid($id){
			$this->helper_set_pointer($this->event_timestamps, $id);
			$blnRaidFound = false;
			$next_raid = 0;
			
			while(!$blnRaidFound){
				$nextResult = next($this->event_timestamps);
				if (!$nextResult) break;
				
				$next_eventid	= key($this->event_timestamps);
				if ($this->get_calendartype($next_eventid) == '1'){
					$next_raid = $next_eventid;
					$blnRaidFound = true;
					break;
				}
			}
			reset($this->event_timestamps);
			return ($next_raid > 0 && $next_raid != $id) ? $next_raid : false;
		}
		
		public function get_prev_event($id){
			$this->helper_set_pointer($this->event_timestamps, $id);
			prev($this->event_timestamps);
			$prev_eventid	= key($this->event_timestamps);
			reset($this->event_timestamps);
			return ($prev_eventid > 0 && $prev_eventid != $id) ? $prev_eventid : false;
		}
		
		public function get_prev_raid($id){
			$this->helper_set_pointer($this->event_timestamps, $id);
			$blnRaidFound = false;
			$prev_raid = 0;
				
			while(!$blnRaidFound){
				$prevResult = prev($this->event_timestamps);
				if (!$prevResult) break;
		
				$prev_eventid	= key($this->event_timestamps);
				if ($this->get_calendartype($prev_eventid) == '1'){
					$prev_raid = $prev_eventid;
					$blnRaidFound = true;
					break;
				}
			}
			reset($this->event_timestamps);
			return ($prev_raid > 0 && $prev_raid != $id) ? $prev_raid : false;
		}
		
		private function helper_set_pointer(&$array,$key){
			reset ($array);
			while (key($array) !== $key) {
				if (next($array) === false) throw new Exception('Invalid key');
			}
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->events)){
				foreach($this->events as $id => $value) {
					if(stripos($this->get_name($id), $search_value) !== false OR stripos($value['notes'], $search_value) !== false ) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id).' '.$this->get_html_time_start($id),
							'name'	=> $this->get_name($id),
							'link'	=> $this->routing->build('calendarevent', $this->get_name($id), $id),
						);
					}
				}
			}
			return $arrSearchResults;
		}

	    /* -----------------------------------------------------------------------
	    * Planned raid to RLI/Raid creation
	    * -----------------------------------------------------------------------*/
		public function get_export_data($id, $json=false){
			$exportdata = $this->get_data($id);
			
			// unset the extension sub array
			unset($exportdata['extension']);
			
			//now, add the extension array on the same level
			$exportdata =array_merge($exportdata, $this->get_extension($id));

			// now add the attendee data
			$exportdata['attendees']['confirmed']	= $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($id, '0'));
			$exportdata['attendees']['signedin']	= $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($id, '1'));
			$exportdata['attendees']['signedoff']	= $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($id, '2'));
			$exportdata['attendees']['backup']		= $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($id, '3'));
			$exportdata['attendees']['guests']		= $this->pdh->get('calendar_raids_guests', 'members', array($id));
			
			return ($json) ? json_encode($exportdata) : $exportdata;
		}

	    /* -----------------------------------------------------------------------
	    * Statistic stuff
		* - amount of raids in the x days
	    * -----------------------------------------------------------------------*/

		public function get_amount_raids($days, $retcount=true){
			$raids = array_filter($this->events, function ($element) use (&$days) {
				return ($element['timestamp_start'] > (time()-($days*86400))); 
			});
			return ($retcount) ? count($raids) : $raids;
		}
		
		public function get_amount_raids_fromto($from, $to, $retcount=true){
			$raids = array_filter($this->events, function ($element) use (&$from, &$to) {
				return ($element['timestamp_start'] > ($from) && ($element['timestamp_end'] < $to));
			});
			return ($retcount) ? count($raids) : $raids;
		}

	}//end class
}//end if
?>