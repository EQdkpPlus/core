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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_events')) {
	class pdh_w_calendar_events extends pdh_w_generic {

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_events;");
			$this->pdh->enqueue_hook('calendar_events_update');
		}
		
		private $arrLogLang = array(
			'id' 					=> "{L_ID}",
			'calendar_id'			=> "{L_CALENDAR}",
			'name'					=> "{L_NAME}",
			'creator'				=> "{L_USER}",
			'timestamp_start'		=> "{L_CALENDAR_STARTDATE}",
			'timestamp_end'			=> "{L_CALENDAR_ENDDATE}",
			'allday'				=> "{L_CALENDAR_ALLDAY_EVENT}",
			'private'				=> "Private",
			'visible'				=> "Visible",
			'closed'				=> "Closed",
			'notes'					=> "{L_NOTE}",
			'repeating'				=> "{L_CALENDAR_REPEAT}",
			'extension'				=> "{L_EXTENSIONS}",
			'cloneid'				=> "Clone-ID",
			'mode'					=> "{L_CALENDAR_MODE}",
		);

		public function update_cevents($id, $cal_id, $name, $startdate, $enddate, $repeat, $editclones, $notes, $allday, $extension=false){
			$entered_notes			= $notes;
			$old['cal_id']			= $this->pdh->get('calendar_events', 'calendar_id', array($id));
			$old['name']			= ($name != false) ? $this->pdh->get('calendar_events', 'name', array($id)) : '';
			$old['startdate']		= $this->pdh->get('calendar_events', 'time_start', array($id));
			$old['enddate']			= $this->pdh->get('calendar_events', 'time_end', array($id));
			$old['repeat']			= $this->pdh->get('calendar_events', 'repeating', array($id));
			$old['notes']			= $this->pdh->get('calendar_events', 'notes', array($id));
			$old['allday']			= $this->pdh->get('calendar_events', 'allday', array($id));
			$changes				= false;

			foreach($old as $varname => $value) {
				if(${$varname} == '') {
					${$varname} = $value;
				}else{
					if(${$varname} != $value) {
						$changes = true;
					}
				}
			}
			
			// fix for empty notes
			if($old['notes'] != '' && $entered_notes == ''){
				$old['notes'] = $notes = '';
				$changes = true;
			}
			
			$tmp_old = $extdata_old = $this->pdh->get('calendar_events', 'extension', array($id));
			if(is_array($extension)) $tmp_new = $extdata = array_merge($extdata_old, $extension);
			unset($tmp_old['updated_on']);
			unset($tmp_new['updated_on']);
			asort($tmp_old); 
			asort($tmp_new);

			if($changes || (serialize($tmp_old) !== serialize($tmp_new))) {
				// the extensions array


				// Handle the cloned events..
				if(isset($editclones) && $editclones != 0){
					$cloneid				= $this->pdh->get('calendar_events', 'cloneid', array($id));
					$cloneid_eventid		= (($cloneid > 0) ? $cloneid : $id);
					$timestamp_start_diff	= intval((($old['startdate'] != $startdate) ? ($startdate-$old['startdate']) : 0));
					$timestamp_end_diff		= intval((($old['enddate'] != $enddate) ? ($enddate-$old['enddate']) : 0));
					
					if($editclones == '2'){
						// only future raids
						$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE cloneid=? AND timestamp_start > ?")->set(array(
							'calendar_id'			=> $cal_id,
							'name'					=> $name,
							'timestamp_start'		=> 'timestamp_start'.((substr($timestamp_start_diff, 0, 1) === '-') ? '' : '+').$timestamp_start_diff,
							'timestamp_end'			=> 'timestamp_end'.((substr($timestamp_end_diff, 0, 1) === '-') ? '' : '+').$timestamp_end_diff,
							'allday'				=> $allday,
							'private'				=> 0,
							'visible'				=> 1,
							'notes'					=> $notes,
							'repeating'				=> $repeat,
							'extension'				=> serialize($extdata),
						))->execute($cloneid_eventid, $this->time->time);
					}else{
						$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE cloneid=?")->set(array(
							'calendar_id'			=> $cal_id,
							'name'					=> $name,
							'timestamp_start'		=> 'timestamp_start'.((substr($timestamp_start_diff, 0, 1) === '-') ? '' : '+').$timestamp_start_diff,
							'timestamp_end'			=> 'timestamp_end'.((substr($timestamp_end_diff, 0, 1) === '-') ? '' : '+').$timestamp_end_diff,
							'allday'				=> $allday,
							'private'				=> 0,
							'visible'				=> 1,
							'notes'					=> $notes,
							'repeating'				=> $repeat,
							'extension'				=> serialize($extdata),
						))->execute($cloneid_eventid);
					}
					

					//now, alter the parent event
					$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
						'calendar_id'			=> $cal_id,
						'name'					=> $name,
						'timestamp_start'		=> 'timestamp_start'.((substr($timestamp_start_diff, 0, 1) === '-') ? '' : '+').$timestamp_start_diff,
						'timestamp_end'			=> 'timestamp_end'.((substr($timestamp_end_diff, 0, 1) === '-') ? '' : '+').$timestamp_end_diff,
						'allday'				=> $allday,
						'private'				=> 0,
						'visible'				=> 1,
						'notes'					=> $notes,
						'repeating'				=> $repeat,
						'extension'				=> serialize($extdata),
					))->execute($cloneid_eventid);
					
					$this->pdh->enqueue_hook('calendar_events_update');

				// and now, handle the single events
				}else{
					$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
						'calendar_id'			=> $cal_id,
						'name'					=> $name,
						'timestamp_start'		=> $startdate,
						'timestamp_end'			=> $enddate,
						'allday'				=> $allday,
						'private'				=> 0,
						'visible'				=> 1,
						'notes'					=> $notes,
						'repeating'				=> $repeat,
						'extension'				=> serialize($extdata),
					))->execute($id);
					
					if(!$objQuery) {
						return false;
					}
				}
				
				// add log entry
				$arrOld = array(
					'calendar_id'			=> $this->pdh->get('calendars', 'name', array($old['cal_id'])),
					'name'					=> $old['name'],
					'timestamp_start'		=> "{D_".$old['startdate']."}",
					'timestamp_end'			=> "{D_".$old['enddate']."}",
					'allday'				=> $this->logs->option_lang($old['allday']),
					'notes'					=> $old['notes'],
					'repeating'				=> '{L_calendar_log_repeat_'.$old['repeat'].'}',
					'extension'				=> serialize($extdata),
					'mode'					=> '{L_calendar_mode_'.$extdata['calendarmode'].'}',
				);
				$arrNew = array(
					'calendar_id'			=> $this->pdh->get('calendars', 'name', array($cal_id)),
					'name'					=> $name,
					'timestamp_start'		=> "{D_".$startdate."}",
					'timestamp_end'			=> "{D_".$enddate."}",
					'allday'				=> $this->logs->option_lang($allday),
					'notes'					=> $notes,
					'repeating'				=> '{L_calendar_log_repeat_'.$repeat.'}',
					'extension'				=> serialize($extension),
					'mode'					=> '{L_calendar_mode_'.$extension['calendarmode'].'}',
				);
				
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
		
				$this->log_insert('calendar_log_eventupdated', $log_action, $id, (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name), true, 'calendar');
			}

			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return true;
		}

		public function add_cevent($cal_id, $name, $creator, $startdate, $enddate, $repeat, $notes, $allday, $extension=false, $cloneid=0){
			// prevent adding events more than one week in the past
			if($startdate < ($this->time->time-604800)){
				return 0;
			}
			
			$objQuery = $this->db->prepare('INSERT INTO __calendar_events :p')->set(array(
				'calendar_id'			=> $cal_id,
				'name'					=> $name,
				'creator'				=> $creator,
				'timestamp_start'		=> $startdate,
				'timestamp_end'			=> $enddate,
				'allday'				=> ($allday > 0) ? $allday : 0,
				'private'				=> 0,
				'visible'				=> 1,
				'closed'				=> 0,
				'notes'					=> $notes,
				'repeating'				=> $repeat,
				'extension'				=> (is_array($extension)) ? serialize($extension) : '',
				'cloneid'				=> ($cloneid > 0) ? $cloneid : 0,
			))->execute();
			
			if ($objQuery){
				$id = $objQuery->insertId;
				$arrNew = array(
					'calendar_id'			=> $this->pdh->get('calendars', 'name', array($cal_id)),
					'name'					=> (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name),
					'creator'				=> $this->pdh->get('user', 'name', array($creator)),
					'timestamp_start'		=> "{D_".$startdate."}",
					'timestamp_end'			=> "{D_".$enddate."}",
					'allday'				=> $this->logs->option_lang(($allday > 0) ? $allday : 0),
					'private'				=> $this->logs->option_lang(0),
					'visible'				=> $this->logs->option_lang(1),
					'closed'				=> $this->logs->option_lang(0),
					'notes'					=> $notes,
					'repeating'				=> '{L_calendar_log_repeat_'.$repeat.'}',
					'extension'				=> (is_array($extension)) ? serialize($extension) : '',
					'cloneid'				=> ($cloneid > 0) ? $cloneid : 0,
					'mode'					=> '{L_calendar_mode_'.$extension['calendarmode'].'}',
				);
				
				$log_action = $this->logs->diff(false, $arrNew, $this->arrLogLang);
				
				$this->log_insert('calendar_log_eventadded', $log_action, $id, (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name), true, 'calendar', ((defined("IN_CRON") && IN_CRON) ? CRONJOB : false));		
			}

			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;
		}

		public function delete_cevent($id, $del_cc_selection='this'){
			
			$arrOld			= $this->pdh->get('calendar_events', 'data', array($id));
			$del_repeatable	= (in_array($del_cc_selection, array('all', 'future', 'past'))) ? true :false;
			$field			= (!is_array($id)) ? array($id) : $id;

			// delete mass-raids
			if($del_repeatable){
				// select the clone-ids of the events
				$objQuery = $this->db->prepare("SELECT DISTINCT cloneid, repeating, id FROM __calendar_events WHERE id :in")->in($field)->execute();
				if($objQuery){
					$delete_events = array();
					while($row = $objQuery->fetchAssoc()){
						//Don't delete events with cloneid = 0
						#if (intval($row['cloneid']) == 0) continue;
						if($row['cloneid'] == 0 && $row['repeating'] > 0){
							$row['cloneid'] = $row['id'];
						}

						// get the date of the selected event
						$current_time	= ($arrOld['timestamp_start'] > 0) ? $arrOld['timestamp_start'] : $this->time->time;

						// fetch the ids to delete
						if($del_cc_selection == 'future' || $del_cc_selection == 'past'){
							$objQuery2 = $this->db->prepare("SELECT id FROM __calendar_events WHERE ((cloneid=?) OR (id=?)) AND timestamp_start ".(($del_cc_selection == 'future') ? '>=' : '<=')." ?")->execute($row['cloneid'], $row['cloneid'], $current_time);
						}else{
							$objQuery2 = $this->db->prepare("SELECT id FROM __calendar_events WHERE (cloneid=?) OR (id=?)")->execute($row['cloneid'], $row['cloneid']);
						}

						// build the array with the ids to be deleted
						if($objQuery2){
							while($row2 = $objQuery2->fetchAssoc()){
								$delete_events[$row2['id']]	= $row2['id'];
							}
						}

						// end the mass-event-series if future events are deleted
						if($del_cc_selection == 'future'){
							$objTest = $this->db->prepare("UPDATE __calendar_events :p WHERE id=? OR cloneid=?")->set(array(
								'repeating'		=> 'none',
							))->execute($row['cloneid'], $row['cloneid']);
						}
					}
					// delete the events and attendees in the array
					if(is_array($delete_events) && count($delete_events) > 0){
						$this->db->prepare("DELETE FROM __calendar_events WHERE id :in")->in($delete_events)->execute();
						$this->db->prepare("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id :in")->in($delete_events)->execute();
					}
				}
			} else {
				$this->db->prepare("DELETE FROM __calendar_events WHERE id :in")->in($field)->execute();
				$this->db->prepare("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id :in")->in($field)->execute();
			}

			//Logging
			$arrOld['timestamp_start'] = "{D_".$arrOld['timestamp_start']."}";
			$arrOld['timestamp_end'] = "{D_".$arrOld['timestamp_end']."}";
			$arrOld['extension'] = serialize($arrOld['extension']);
			$log_action = $this->logs->diff(false, $arrOld, $this->arrLogLang);
			$this->log_insert('calendar_log_eventdeleted', $log_action, (is_array($id) ? $id[0] : $id), $arrOld['name'], true, 'calendar');
			
			// perform the hooks
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
			$this->pdh->enqueue_hook('calendar_events_update', array( (is_array($id) ? $id[0] : $id)));
			return true;
		}
		
		public function change_closeraidstatus($id, $closed=true){
			$arrOld['closed'] = $this->pdh->get('calendar_events', 'raidstatus', array($id));
			
			$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
				'closed'	=> (($closed) ? 1 : 0),
			))->execute($id);
			
			//Logging
			$arrNew['closed'] =  (($closed) ? 1 : 0);	
			$openclose = ($closed) ? 'closed' : 'opened';
			$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
			if ($log_action) $this->log_insert('calendar_log_raid'.$openclose, $log_action, $id, $this->pdh->get('calendar_events', 'name', array($id)), true, 'calendar');
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;

		}
		
		public function update_note($id, $note=''){
			$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'notes'	=> $note,
			))->execute($id);
			
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;

		}
		
		public function delete_clones($cloneid){
			$this->db->prepare("DELETE FROM __calendar_events WHERE cloneid=?")->execute($cloneid);
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			$this->log_insert('calendar_log_deletedclones', array(), $cloneid, $this->pdh->get('calendar_events', 'name', array($cloneid)), true, 'calendar');
			return true;
		}

		public function move_event($eventid, $daydelta, $minutedelta, $allday='false'){
			if($eventid > 0){
				$eventdata				= $this->pdh->get('calendar_events', 'data', array($eventid));
				$general_delta_sec		= ($daydelta * 86400) + ($minutedelta * 60);
				$a_extension			= (isset($eventdata['extension'])) ? $eventdata['extension'] : false;
				if(is_array($a_extension) && $a_extension['calendarmode'] == 'raid'){
					$a_extension['invitedate']		= $a_extension['invitedate'] + $general_delta_sec;
				}
				
				$arrOld = array(
						'timestamp_start'	=> "{D_".( $this->pdh->get('calendar_events', 'time_start', array($eventid)))."}",
						'timestamp_end'		=> "{D_".( $this->pdh->get('calendar_events', 'time_end', array($eventid)))."}",
						'allday'			=> $this->pdh->get('calendar_events', 'allday', array($eventid))
				);
				
				$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'timestamp_start'	=> $eventdata['timestamp_start'] + (int)$general_delta_sec,
					'timestamp_end'		=> $eventdata['timestamp_end'] + (int)$general_delta_sec,
					'allday'			=> (($move) ? (($allday == 'true') ? 1 : 0) : $eventdata['allday']),
					'extension'			=> serialize($a_extension)
				))->execute($eventid);
				
				$this->pdh->enqueue_hook('calendar_events_update', array($eventid));
				
				//Logging
				$arrNew = array(
					'timestamp_start'	=> "{D_".($eventdata['timestamp_start'] + (int)$general_delta_sec)."}",
					'timestamp_end'		=> "{D_".($eventdata['timestamp_end'] + (int)$general_delta_sec)."}",
					'allday'			=> (($move) ? (($allday == 'true') ? 1 : 0) : $eventdata['allday']),
				);
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
				$this->log_insert('calendar_log_eventupdated', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
				
				return $result;
			}
		}
		
		public function resize_event($eventid, $daydelta, $minutedelta){
			if($eventid > 0){
				$general_delta_sec	= (($daydelta * 86400) + ($minutedelta * 60));
				$old_timestamp		= $this->pdh->get('calendar_events', 'time_end', array($eventid));
				
				$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'timestamp_end'		=> $old_timestamp + (int)$general_delta_sec,
				))->execute($eventid);
				
				//Logging
				$arrNew = array(
						'timestamp_end'		=> $old_timestamp + (int)$general_delta_sec,
				);
				$log_action = $this->logs->diff(array('timestamp_end' => $old_timestamp), $arrNew, $this->arrLogLang);
				$this->log_insert('calendar_log_eventupdated', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
				
				$this->pdh->enqueue_hook('calendar_events_update', array($eventid));
				return $result;
			}
		}
		
		public function auto_addchars($raidtype, $raidid, $raidleaders=array(), $group=false, $status=false){
			//Auto confirm Groups
			$arrAutoconfirmGroups = $this->config->get('calendar_raid_autoconfirm');
			$signupstatus	= 1; //Angemeldet

			// auto add groups
			$usergroups = ($group && is_array($group)) ? $group : $this->config->get('calendar_raid_autocaddchars');
			if(is_array($usergroups) && count($usergroups) > 0){
				$userids = $this->pdh->get('user_groups_users', 'user_list', array($usergroups));
				if(is_array($userids)){
					foreach($userids as $userid){
						$memberid		= $this->pdh->get('member', 'mainchar', array($userid));
						$defaultrole	= $this->pdh->get('member', 'defaultrole', array($memberid));
						if($memberid > 0){
							if(($raidtype == 'role' && $defaultrole > 0) || $raidtype == 'class' || $raidtype == 'none'){
								//Autoconfirm
								if($status){
									$signupstatus = $status;
								}else{
									if(is_array($arrAutoconfirmGroups) && count($arrAutoconfirmGroups) > 0 && $signupstatus == 1){
										if($this->user->check_group($arrAutoconfirmGroups, false, $userid)){
											$signupstatus = 0;
										}
									}
								}

								$this->pdh->put('calendar_raids_attendees', 'update_status', array(
									$raidid,
									$memberid,
									(($defaultrole) ? $defaultrole : 0),
									$signupstatus,
									0,
									0,
									'',
								));
							}
						}
					}
					$this->pdh->process_hook_queue();
				}
			}

			// auto add and confirm the raidleaders
			if(is_array($raidleaders)){
				foreach($raidleaders as $raidleaderid){
					$defaultrole	= $this->pdh->get('member', 'defaultrole', array($raidleaderid));
					$this->pdh->put('calendar_raids_attendees', 'update_status', array(
						$raidid,
						$raidleaderid,
						(($defaultrole) ? $defaultrole : 0),
						0,	// status
						0,
						0,
						'',
					));
				}
			}

		}
	}
}
?>
