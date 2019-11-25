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
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists("calevents_repeatable_crontask")){
	class calevents_repeatable_crontask extends crontask{

		public function __construct(){
			$this->defaults['active']			= true;
			$this->defaults['repeat']			= true;
			$this->defaults['repeat_type']		= 'daily';
			$this->defaults['repeat_interval']	= 1;
			$this->defaults['ajax']				= true;
			$this->defaults['description']		= 'Creating repeatable events';
		}

		public function cleanExtensionField($value, $array=true){
			return ($array) ? ((isset($value) && is_array($value) && count($value) > 0) ? $value : false) : $value;
		}

		public function run(){
			// fetch the raid ids of the repeatable raids
			$repeatable_events = $this->pdh->get('calendar_events', 'repeatable_events');
			if(is_array($repeatable_events) && count($repeatable_events) > 0){
				foreach($repeatable_events as $parentid=>$a_childid){

					// the ID of the parent event have to be higher 0
					if($parentid > 0){
						
						$eventsdata			= $this->pdh->get('calendar_events', 'data', array($parentid));
						$start_timestamp	= $this->pdh->get('calendar_events', 'time_start', array($parentid));
						$end_timestamp		= $this->pdh->get('calendar_events', 'time_end', array($parentid));
						$eventduration		= $end_timestamp - $start_timestamp;
						$end_cronjob		= $this->time->time+((($this->config->get('calendar_repeat_crondays') > 0) ? $this->config->get('calendar_repeat_crondays') : 40) * 86400);
						$timezone			= $this->pdh->get('calendar_events', 'timezone', array($parentid));
						$repeating_value	= $this->pdh->get('calendar_events', 'repeating', array($parentid));
						$repeating_value	= ($repeating_value >= 1) ? $repeating_value : 0;

						// if the repeating value of parent is 0, do not longer add a new raid
						if($repeating_value === 0){
							continue;
						}
						$rptbl_period		= 86400*$repeating_value;
						$private			= $this->pdh->get('calendar_events', 'private', array($parentid));

						// if the calendar id is < 1, continue
						if($eventsdata['calendar_id'] < 1){
							continue;
						}

						// prevent null errors... this one will never occur, just for saety...
						if($this->time->time == null || $end_timestamp == null || $start_timestamp == null){
							continue;
						}

						// get the highest id of the clones and check if we have to add a new one
						$max_childid	= max($a_childid);
						$date_max_child	= $this->pdh->get('calendar_events', 'time_start', array($max_childid));
						$date_event_add	= $this->time->createRepeatableEvents($date_max_child, $rptbl_period, $timezone);

						// add an event if needed
						while($date_event_add < $end_cronjob){
							$clone_starttimestamp	= $date_event_add;
							$clone_endtimestamp		= $date_event_add + $eventduration;

							$a_data = array(
								$eventsdata['calendar_id'],
								$eventsdata['name'],
								$eventsdata['creator'],
								$clone_starttimestamp,
								$clone_endtimestamp,
								$eventsdata['repeating'],
								$eventsdata['notes'],
								$eventsdata['allday']
							);
							if($eventsdata['extension']['calendarmode'] == 'raid'){
								array_push($a_data, array(
									'raid_eventid'		=> $eventsdata['extension']['raid_eventid'],
									'calendarmode'		=> $eventsdata['extension']['calendarmode'],
									'raid_value'		=> $eventsdata['extension']['raid_value'],
									'deadlinedate'		=> $eventsdata['extension']['deadlinedate'],
									'raidmode'			=> $eventsdata['extension']['raidmode'],
									'raidleader'		=> $eventsdata['extension']['raidleader'],
									'distribution'		=> $eventsdata['extension']['distribution'],
									'attendee_count'	=> $eventsdata['extension']['attendee_count'],
									'created_on'		=> $this->time->time,
									'autosignin_group'	=> $eventsdata['extension']['autosignin_group'],
									'autosignin_status'	=> $eventsdata['extension']['autosignin_status'],
									'invited_raidgroup'	=> $this->in->getArray('invited_raidgroup', 'int'),
								));
							} else {
								array_push($a_data, array(
									'invited'			=> $eventsdata['extension']['invited'],
									'invited_usergroup'	=> $eventsdata['extension']['invited_usergroup'],
									'location'			=> $eventsdata['extension']['location'],
								));
							}
							array_push($a_data, $parentid);
							array_push($a_data, $private);

							$clonedraidid = $this->pdh->put('calendar_events', 'add_cevent', $a_data);
							$this->pdh->process_hook_queue();

							// Auto add the chars
							$this->pdh->put('calendar_events', 'auto_addchars', array($eventsdata['extension']['raidmode'], $clonedraidid, $eventsdata['extension']['raidleader'], $this->cleanExtensionField($eventsdata['extension']['autosignin_group']), $eventsdata['extension']['autosignin_status']));

							// set the date for the next event
							$date_event_add	= $this->time->createRepeatableEvents($date_event_add, $rptbl_period, $timezone);
						}
					}
				} // end of foreach
			}
			$this->pdh->process_hook_queue();
		}

	}
}
