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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_calendar_events" ) ) {
	class pdh_r_calendar_events extends pdh_r_generic{

		public $default_lang = 'english';
		public $events, $events_unique, $repeatable_events, $event_timestamps;

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
			$this->pdc->del('pdh_calendar_events_table.events_unique');
			$this->pdc->del('pdh_calendar_events_table.repeatable');
			$this->pdc->del('pdh_calendar_events_table.timestamps');
			$this->events				= NULL;
			$this->events_unique		= NULL;
			$this->repeatable_events	= NULL;
			$this->event_timestamps		= NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->events				= $this->pdc->get('pdh_calendar_events_table.events');
			$this->events_unique		= $this->pdc->get('pdh_calendar_events_table.events_unique');
			$this->repeatable_events	= $this->pdc->get('pdh_calendar_events_table.repeatable');
			$this->event_timestamps		= $this->pdc->get('pdh_calendar_events_table.timestamps');
			if($this->events !== NULL && $this->events_unique !== NULL && $this->repeatable_events !== NULL && $this->event_timestamps !== NULL){
				return true;
			}

			//Get from Cache
			$this->events				= array();
			$this->events_unique		= array();
			$this->repeatable_events	= array();
			$this->event_timestamps		= array();

			$objQuery = $this->db->query("SELECT * FROM __calendar_events ORDER BY id ASC");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->events[$row['id']] = array(
						'id'					=> (int)$row['id'],
						'calendar_id'			=> (int)$row['calendar_id'],
						'name'					=> $row['name'],
						'creator'				=> (int)$row['creator'],
						'timestamp_start'		=> (int)$row['timestamp_start'],
						'timestamp_end'			=> (int)$row['timestamp_end'],
						'allday'				=> (int)$row['allday'],
						'private'				=> (int)$row['private'],
						'visible'				=> (int)$row['visible'],
						'closed'				=> (int)$row['closed'],
						'notes'					=> $row['notes'],
						'repeating'				=> (int)$row['repeating'],
						'cloneid'				=> (int)$row['cloneid'],
						'timezone'				=> $row['timezone'],
					);
					$this->events[$row['id']]['extension']	= unserialize_noclasses($row['extension']);
					$this->event_timestamps[$row['id']]		= (int)$row['timestamp_start'];

					// unique event array
					$raidventID	= (isset($this->events[$row['id']]['extension']['raid_eventid']) && $this->events[$row['id']]['extension']['raid_eventid'] > 0) ? $this->events[$row['id']]['extension']['raid_eventid'] : 0;
					$uniqueID 	= ($raidventID > 0) ? $raidventID : $row['name'];
					if($uniqueID != ''){
						$this->events_unique[$uniqueID] = array(
							'ts'	=> (int)$row['timestamp_start'],
							'id'	=> $row['id'],
							'name'	=> $uniqueID
						);
					}

					// set the repeatable array
					if((int)$row['repeating'] > 0){
						$parentid	= ((int)$row['cloneid'] > 0) ? (int)$row['cloneid'] : (int)$row['id'];
						$this->repeatable_events[$parentid][] = (int)$row['id'];
					}
				}

				// sort the timestamps
				if(is_array($this->event_timestamps)) asort($this->event_timestamps);

				// set the cache
				$this->pdc->put('pdh_calendar_events_table.events', $this->events, null);
				$this->pdc->put('pdh_calendar_events_table.events_unique', $this->events_unique, null);
				$this->pdc->put('pdh_calendar_events_table.repeatable', $this->repeatable_events, null);
				$this->pdc->put('pdh_calendar_events_table.timestamps', $this->event_timestamps, null);
			}

		}

		public function get_id_list($raids_only=false, $start_date = 0, $end_date = PHP_INT_MAX, $idfilter=false, $filter=false){
			$ids = array();

			if(($start_date != 0) || ($end_date != PHP_INT_MAX)){
				$start_date	 = $this->time->newtime($start_date, '00:00', false);
				$end_date	 = ($end_date != PHP_INT_MAX) ? $this->time->newtime($end_date, '23:59', false) : $end_date;

				if(is_array($idfilter)){
					$objQuery = $this->db->prepare("SELECT id FROM __calendar_events WHERE (calendar_id :in)")->in($idfilter);
				} else {
					$objQuery = $this->db->prepare("SELECT id FROM __calendar_events");
				}

				$objQuery->addCondition("((timestamp_start BETWEEN ? AND ?) OR timestamp_end BETWEEN ? AND ?)", $start_date, $end_date, $start_date, $end_date);

				// apply the filtering
				switch($filter){
					case 'mine':
						$objQuery->addCondition("creator=?", $this->user->data['user_id']);
					break;
					case 'past':
						$objQuery->addCondition("timestamp_end<?", $this->time->time);
					break;
					case 'future':
						$objQuery->addCondition("timestamp_end>?", $this->time->time);
					break;
				}

				$query = $objQuery->execute();

				if ($query){
					if($raids_only) {
						$what2filter	= (($raids_only === 'appointments') ? '2' : '1');
						while ( $row = $query->fetchAssoc() ){
							if($this->get_calendartype($row['id']) == $what2filter){
								if($filter == 'attendance'){
									$mystatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($row['id'], $this->user->data['user_id'], false));
									// 0 and 1 (confirmed and signed in), rest is not attending
									if($mystatus == '' || $mystatus > 1){
										continue;
									}
								}

								// remove private events if no permission for it
								if(!$this->get_private_userperm($row['id'])){ continue; }
								$ids[] = $row['id'];
							}
						}
					}else{
						while ( $row = $query->fetchAssoc() ){
							if($filter == 'attendance'){
								$mystatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($row['id'], $this->user->data['user_id'], false));
								// 0 and 1 (confirmed and signed in), rest is not attending
								if($mystatus == '' || $mystatus > 1){
									continue;
								}
							}

							// remove private events if no permission for it
							if(!$this->get_private_userperm($row['id'])){ continue; }
							$ids[] = $row['id'];
						}
					}
				}
			}else if(isset($this->events)){
				$ids = array_keys($this->events);
				if($raids_only) {
					foreach($ids as $key => $id) {
						if($this->get_calendartype($id) != '1' || $this->events[$id]['timestamp_end'] < $this->time->time) unset($ids[$key]);

						// us the calendarfilter
						if(is_array($idfilter) && !in_array($this->get_calendar_id($id), $idfilter)) unset($ids[$key]);

						// remove private events if no permission for it
						if(!$this->get_private_userperm($id)) unset($ids[$key]);
					}
				}elseif(is_array($idfilter)){
					foreach($ids as $key => $id) {
						// us the calendarfilter
						if(is_array($idfilter) && !in_array($this->get_calendar_id($id), $idfilter)) unset($ids[$key]);
					}
				}
			}

			return $ids;
		}

		public function get_lastuniqueevents($amount=10){
			$items 	= $this->events_unique;
			usort($items, function ($a1, $a2) {
				if ($a1['ts'] == $a2['ts']) return 0;
				return ($a1['ts'] > $a2['ts']) ? -1 : 1;
			});
			return array_slice($items, -$amount);
		}

		public function get_repeatable_events($cloneid=0){
			return ($cloneid > 0) ? $this->repeatable_events[$cloneid] : $this->repeatable_events;
		}

		public function get_cloneid($id=''){
			return 	(isset($this->events[$id]['cloneid'])) ? $this->events[$id]['cloneid'] : 0;
		}

		public function get_data($id=''){
			return 	($id) ? ((isset($this->events[$id])) ? $this->events[$id] : array()) : $this->events;
		}

		public function get_template($id=0){
			if($id > 0){
				$extension = $this->get_extension($id);
				if(isset($extension['calendarmode']) && $extension['calendarmode'] == 'raid'){
					// it is a raid event
					return 	array(
						'input_eventid'			=> $extension['raid_eventid'],
						'input_dkpvalue'		=> $extension['raid_value'],
						'input_note'			=> $this->get_notes($id),
						'selectmode'			=> $extension['calendarmode'],
						'cal_raidmodeselect'	=> $extension['raidmode'],
						'dw_raidleader'			=> $extension['raidleader'],
						'distribution'			=> $extension['distribution'],
						'deadlinedate'			=> $extension['deadlinedate'],
					);
				}else{
					// it is a normal event
					return 	array(
						'input_eventid'			=> $this->get_name($id),
						'input_note'			=> $this->get_notes($id),
						'selectmode'			=> 'event',
					);
				}
			}
			return array();
		}

		public function get_timezone($id=''){
			return (isset($this->events[$id]['timezone'])) ? $this->events[$id]['timezone'] : 'UTC';
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

		public function get_calendarmode($id){
			$extension = $this->events[$id]['extension'];
			return (isset($extension['calendarmode']) && $extension['calendarmode'] == 'raid') ? 'raid' : 'event';
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
			return (isset($this->events[$id]['creator']) && $this->events[$id]['creator'] > 0) ? $this->events[$id]['creator'] : 0;
		}

		public function get_is_owner($id){
			$author	= ($this->events[$id]['creator']) ? $this->events[$id]['creator'] : 0;
			if($author > 0){
				return ($this->user->data['user_id'] == $author) ? true : false;
			}
			return false;
		}

		public function get_date($id) {
			return (isset($this->events[$id]['timestamp_start'])) ? $this->events[$id]['timestamp_start'] : 0;
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
			return ($exclusivefix) ? $this->time->createRepeatableEvents($this->events[$id]['timestamp_end'], 86400, $this->get_timezone($id)) : $this->events[$id]['timestamp_end'];
		}

		public function get_html_time_end($id) {
			return $this->time->user_date($this->events[$id]['timestamp_end'], false, true);
		}

		public function get_allday($id){
			return 	(isset($this->events[$id]['allday'])) ? $this->events[$id]['allday'] : 0;
		}

		public function get_private($id){
			return (isset($this->events[$id]['private'])) ? $this->events[$id]['private'] : 0;
		}

		public function get_raid_raidgroups($id, $userid = false, $asArray=false){
			$extension	= $this->get_extension($id);
			if($this->get_private($id) > 0){
				$userid		= ($userid > 0) ? $userid : $this->user->data['user_id'];
				if((isset($extension['calendarmode']) && $extension['calendarmode'] == 'raid')){
					return ($asArray) ? $this->pdh->get('raid_groups', 'name', array($extension['invited_raidgroup'])) : implode(',', $this->pdh->get('raid_groups', 'name', array($extension['invited_raidgroup'], true)));
				}
			}
			return false;
		}

		public function get_private_userperm($id, $userid=0){
			$extension	= $this->get_extension($id);
			if($this->get_private($id) > 0){
				$userid		= ($userid > 0) ? $userid : $this->user->data['user_id'];
				if((isset($extension['calendarmode']) && $extension['calendarmode'] == 'raid')){
					$owner		= $this->get_creatorid($id);
					$raidgroup	= $extension['invited_raidgroup'];
					$myattendees	= $this->pdh->get('calendar_raids_attendees', 'myattendees', array($id, $userid));
					$is_in_raidgroup	= $this->pdh->get('raid_groups_members', 'user_is_in_groups', array($userid, $raidgroup));
					return ($owner ==  $userid || (is_array($myattendees) && $myattendees['member_id'] > 0) || $is_in_raidgroup) ? true : false;
				}else{
					$owner		= $this->get_creatorid($id);
					return ($owner ==  $userid || (isset($extension['invited']) && in_array($userid, $extension['invited']))) ? true : false;
				}
			}
			return true;
		}

		public function get_transformed_raid_link($eventID){
			$raidextension	= $this->get_extension($eventID);
			$intRaidID		= (isset($raidextension['transformed']['id']) && $raidextension['transformed']['id'] > 0) ? $raidextension['transformed']['id'] : 0;
			$intRaidEventID	= (isset($raidextension['raid_eventid'])) ? $raidextension['raid_eventid'] : 0;

			if($intRaidID > 0 && $intRaidEventID > 0){
				return $this->pdh->get('event', 'html_icon', array($intRaidEventID)).$this->pdh->get('raid', 'html_raidlink', array($intRaidID, register('routing')->simpleBuild('raids'), '', true));
			}
		}

		// check calendar specific rights such as if the user is a raidleader or the creator
		public function get_check_operatorperm($raidid, $userid=0){
			$userid	= ($userid > 0) ? $userid : $this->user->data['user_id'];
			$creator			= $this->pdh->get('calendar_events', 'creatorid', array($raidid));
			$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($raidid));
			$raidleaders_chars	= (isset($ev_ext['raidleader']) && $ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
			$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
			if (!is_array($raidleaders_users)) $raidleaders_users = array();
			return (($creator == $userid) || in_array($userid, $raidleaders_users))  ? true : false;
		}

		public function get_is_invited($id, $userid=0){
			$extension	= $this->get_extension($id);
			$userid		= ($userid > 0) ? $userid : $this->user->data['user_id'];
			return (isset($extension['invited']) && in_array($userid, $extension['invited'])) ? true : false;
		}

		public function get_joined_invitation($id, $userid=0){
			if($this->get_is_invited($id, $userid)){
				$extension		= $this->get_extension($id);
				$userid			= ($userid > 0) ? $userid : $this->user->data['user_id'];
				if(isset($extension['invited_attendees'])){
					$inviteduser	= array_keys($extension['invited_attendees']);
					return (in_array($userid, $inviteduser)) ? true : false;
				}
			}
			return false;
		}

		public function get_sharedevent_attendees($id){
			$extension		= $this->get_extension($id);
			return (isset($extension['invited_attendees']) && count($extension['invited_attendees']) > 0) ? implode(', ', $this->pdh->get('user', 'names', array(array_keys($extension['invited_attendees'])))) : '';
		}

		public function get_event_attendees($id){
			$extension		= $this->get_extension($id);
			return (isset($extension['event_attendees']) && count($extension['event_attendees']) > 0) ? implode(', ', $this->pdh->get('user', 'names', array(array_keys($extension['event_attendees'])))) : '';
		}

		public function get_calendartype($id){
			return (isset($this->events[$id]['calendar_id'])) ? $this->pdh->get('calendars', 'type', array($this->events[$id]['calendar_id'])) : '';
		}

		public function get_visible($id){
			return 	(isset($this->events[$id]['visible'])) ? $this->events[$id]['visible'] : 0;
		}

		public function get_extension($id, $value=false){
			if($value && isset($this->events[$id]['extension'][$value])){
				return $this->events[$id]['extension'][$value];
			}elseif(isset($this->events[$id]['extension'])){
				return $this->events[$id]['extension'];
			}
			return array();
		}

		public function get_notes($id, $bbcode2html=false){
			return 	($bbcode2html) ?  $this->bbcode->toHTML($this->events[$id]['notes']) : $this->bbcode->remove_bbcode($this->events[$id]['notes']);
		}

		public function get_html_notes($id){
			if($this->get_notes($id,true) != ''){
				return 	'<span class="coretip-sticky" data-coretip="'.htmlspecialchars($this->get_notes($id,true)).'" data-coretiphead="Notiz"><i class="fa fa-comment fa-lg"></i></span>';
			}
			return 	'<span class="icon-grey"><i class="fa fa-comment fa-lg"></i></span>';
		}

		public function get_repeating($id){
			return ($this->events[$id]['repeating']) ? $this->events[$id]['repeating'] : 0;
		}

		public function get_detailslink($id){
			return '<a href="'.$this->routing->build('calendarevent', $this->get_name($id), $id).'"><i class="fa fa-lg fa-arrow-right"></i></a>';
		}

		public function get_raiddistribution($id, $classid=0){
			$extension		= $this->get_extension($id);
			return (isset($extension['distribution'][$classid])) ? $extension['distribution'][$classid] : ((isset($extension['distribution'])) ? $extension['distribution'] : 0);
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

		public function get_event_icon($id){
			$eventextension	= $this->events[$id]['extension'];
			if(isset($eventextension['raid_eventid']) && $eventextension['raid_eventid']){
				return ($eventextension['raid_eventid']) ? $this->pdh->get('event', 'icon', array($eventextension['raid_eventid'], true)) : '';
			}elseif(isset($eventextension['calevent_icon']) && !empty($eventextension['calevent_icon']) && $eventextension['calevent_icon'] != '0'){
				if(is_file($this->pfh->FolderPath("event_icons", "files").$eventextension['calevent_icon'])){
					return $this->pfh->FolderPath("event_icons", "files", "absolute").$eventextension['calevent_icon'];
				}elseif(is_file($this->root_path.'games/'.$this->game->get_game().'/icons/events/'.$eventextension['calevent_icon'])){
					return $this->env->buildlink().'games/'.$this->game->get_game().'/icons/events/'.$eventextension['calevent_icon'];
				}
			}
			return '';
		}

		public function get_raid_eventid($id){
			if(!isset($this->events[$id]['extension']['raid_eventid'])) return false;
			return $this->events[$id]['extension']['raid_eventid'];
		}

		public function get_next_event($id){
			try {
				$this->helper_set_pointer($this->event_timestamps, $id);
			}catch(Exception $e){
				return false;
			}
			next($this->event_timestamps);
			$next_eventid	= key($this->event_timestamps);
			reset($this->event_timestamps);
			return ($next_eventid > 0 && $next_eventid != $id) ? $next_eventid : false;
		}

		public function get_next_raid($id){
			try {
				$this->helper_set_pointer($this->event_timestamps, $id);
			}catch(Exception $e){
				return false;
			}
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
			try {
				$this->helper_set_pointer($this->event_timestamps, $id);
			}catch(Exception $e){
				return false;
			}
			prev($this->event_timestamps);
			$prev_eventid	= key($this->event_timestamps);
			reset($this->event_timestamps);
			return ($prev_eventid > 0 && $prev_eventid != $id) ? $prev_eventid : false;
		}

		public function get_prev_raid($id){
			try {
				$this->helper_set_pointer($this->event_timestamps, $id);
			}catch(Exception $e){
				return false;
			}
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
			$events = (is_array($this->events)) ? $this->events : array();
			$raids = array_filter($events, function ($element) use (&$days) {
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
