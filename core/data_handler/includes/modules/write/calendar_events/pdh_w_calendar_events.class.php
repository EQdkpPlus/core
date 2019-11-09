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

		public function update_cevents($id, $cal_id, $name, $startdate, $enddate, $repeat, $editclones, $notes, $allday, $extension=false, $private=0){
			$entered_notes			= $notes;
			$old['cal_id']			= $this->pdh->get('calendar_events', 'calendar_id', array($id));
			$old['name']			= ($name != false) ? $this->pdh->get('calendar_events', 'name', array($id)) : '';
			$old['startdate']		= $this->pdh->get('calendar_events', 'time_start', array($id));
			$old['enddate']			= $this->pdh->get('calendar_events', 'time_end', array($id));
			$old['repeat']			= $this->pdh->get('calendar_events', 'repeating', array($id));
			$old['notes']			= $this->pdh->get('calendar_events', 'notes', array($id, true));
			$old['allday']			= $this->pdh->get('calendar_events', 'allday', array($id));
			$private_old			= $this->pdh->get('calendar_events', 'private', array($id));
			$private				= ((int)$private == 0 || (int)$private == 1) ? (int)$private : (int)$private_old;
			$creator				= $this->pdh->get('calendar_events', 'creatorid', array($id));
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

			if(isset($old['private']) && $old['private'] > 0 && $creator != $this->user->data['user_id']){
				return false;
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

				// Handle the cloned mass events..
				if(isset($editclones) && $editclones != 0){
					$cloneid				= $this->pdh->get('calendar_events', 'cloneid', array($id));
					$cloneid_eventid		= (($cloneid > 0) ? $cloneid : $id);
					$timestamp_start_diff	= ($old['startdate'] != $startdate) ? $this->time->dateDiff($old['startdate'], $startdate, 'sec', true) : "+0";
					$timestamp_end_diff		= ($old['enddate'] != $enddate) ? $this->time->dateDiff($old['enddate'], $enddate, 'sec', true) : "+0";

					// only future raids
					if($editclones == '2'){
						$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE cloneid=? AND timestamp_start > ?")->set(array(
							'calendar_id'			=> $cal_id,
							'name'					=> $name,
							'timestamp_start'		=> 'timestamp_start'.$timestamp_start_diff,
							'timestamp_end'			=> 'timestamp_end'.$timestamp_end_diff,
							'allday'				=> $allday,
							'private'				=> $private,
							'visible'				=> 1,
							'notes'					=> $notes,
							'repeating'				=> $repeat,
							'extension'				=> serialize($extdata),
						))->execute($cloneid_eventid, $this->time->time);

					// all other raids
					}else{
						$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE cloneid=?")->set(array(
							'calendar_id'			=> $cal_id,
							'name'					=> $name,
							'timestamp_start'		=> 'timestamp_start'.$timestamp_start_diff,
							'timestamp_end'			=> 'timestamp_end'.$timestamp_end_diff,
							'allday'				=> $allday,
							'private'				=> $private,
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
						'timestamp_start'		=> 'timestamp_start'.$timestamp_start_diff,
						'timestamp_end'			=> 'timestamp_end'.$timestamp_end_diff,
						'allday'				=> $allday,
						'private'				=> $private,
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
						'private'				=> $private,
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
				);

				if(isset($extension['calendarmode'])){
					$arrOld['mode'] = '{L_calendar_mode_'.$extdata['calendarmode'].'}';
					$arrNew['mode'] = '{L_calendar_mode_'.$extension['calendarmode'].'}';
				}

				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);

				if($this->hooks->isRegistered('calendarevent_updated')){
					$this->hooks->process('calendarevent_updated', array('id' => $id, 'data' => $arrNew));
				}

				$this->log_insert('calendar_log_eventupdated', $log_action, $id, ((isset($extension['raid_eventid']) && $extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name), true, 'calendar');
			}

			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return true;
		}

		public function add_cevent($cal_id, $name, $creator, $startdate, $enddate, $repeat, $notes, $allday, $extension=false, $cloneid=0, $private=0){
			// prevent adding events more than one week in the past
			if($startdate < ($this->time->time-604800)){
				return 0;
			}

			$timezone_creator	= $this->pdh->get('user', 'timezone', array($creator));
			$objQuery = $this->db->prepare('INSERT INTO __calendar_events :p')->set(array(
				'calendar_id'			=> $cal_id,
				'name'					=> $name,
				'creator'				=> $creator,
				'timezone'				=> $timezone_creator,
				'timestamp_start'		=> $startdate,
				'timestamp_end'			=> $enddate,
				'allday'				=> ($allday > 0) ? $allday : 0,
				'private'				=> (int)$private,
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
					'name'					=> ((isset($extension['raid_eventid']) && $extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name),
					'creator'				=> $this->pdh->get('user', 'name', array($creator)),
					'timezone'				=> $timezone_creator,
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
				);
				if(isset($extension['calendarmode'])){
					$arrNew['mode'] = '{L_calendar_mode_'.$extension['calendarmode'].'}';
				}

				$log_action = $this->logs->diff(false, $arrNew, $this->arrLogLang);

				$this->log_insert('calendar_log_eventadded', $log_action, $id, ((isset($extension['raid_eventid']) && $extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name), true, 'calendar', ((defined("IN_CRON") && IN_CRON) ? CRONJOB : false));

				if($this->hooks->isRegistered('calendarevent_added')){
					$this->hooks->process('calendarevent_added', array('id' => $id, 'data' => $arrNew));
				}
			}

			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;
		}

		public function delete_cevent($id, $del_cc_selection='this'){

			$arrOld			= $this->pdh->get('calendar_events', 'data', array($id));
			$del_repeatable	= (in_array($del_cc_selection, array('all', 'future', 'past'))) ? true :false;
			$field			= (!is_array($id)) ? array($id) : $id;

			// private event: only owner should be able to delete it
			if($arrOld['private'] > 0 && $arrOld['creator'] != $this->user->data['user_id']){
				return false;
			}

			// delete mass-raids
			if($del_repeatable){
				// select the clone-ids of the events
				$objQuery = $this->db->prepare("SELECT DISTINCT cloneid, repeating, id FROM __calendar_events WHERE id :in")->in($field)->execute();
				if($objQuery){
					$delete_events = array();
					while($row = $objQuery->fetchAssoc()){
						//Don't delete events which are not
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
								'repeating'		=> 0,
							))->execute($row['cloneid'], $row['cloneid']);
						}
					}
					// delete the events and attendees in the array
					if(is_array($delete_events) && count($delete_events) > 0){
						$this->db->prepare("DELETE FROM __calendar_events WHERE id :in")->in($delete_events)->execute();
						$this->db->prepare("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id :in")->in($delete_events)->execute();

						//delete notifications
						foreach($delete_events as $delid){
							$this->ntfy->deleteNotification('calenderevent_opened', $delid);
							$this->ntfy->deleteNotification('calenderevent_closed', $delid);
							$this->ntfy->deleteNotification('calenderevent_new', $delid);
							$this->ntfy->deleteNotification('calenderevent_invitation', $delid);
						}
					}
				}
			} else {
				$this->db->prepare("DELETE FROM __calendar_events WHERE id :in")->in($field)->execute();
				$this->db->prepare("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id :in")->in($field)->execute();
			}

			//Logging
			$arrOld['timestamp_start']	= "{D_".$arrOld['timestamp_start']."}";
			$arrOld['timestamp_end']	= "{D_".$arrOld['timestamp_end']."}";
			$extension					= $arrOld['extension'];
			$arrOld['extension']		= serialize($arrOld['extension']);
			$log_action					= $this->logs->diff(false, $arrOld, $this->arrLogLang);
			$eventname					=  (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : ((isset($arrOld['name'])) ? $arrOld['name'] : ""));
			$this->log_insert('calendar_log_eventdeleted', $log_action, (is_array($id) ? $id[0] : $id), $eventname, true, 'calendar');

			//Delete notifications
			foreach($field as $eventid){
				$this->ntfy->deleteNotification('calenderevent_opened', $eventid);
				$this->ntfy->deleteNotification('calenderevent_closed', $eventid);
				$this->ntfy->deleteNotification('calenderevent_new', $eventid);
				$this->ntfy->deleteNotification('calenderevent_invitation', $eventid);
			}

			// perform the hooks
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
			$this->pdh->enqueue_hook('calendar_events_update', array( (is_array($id) ? $id[0] : $id)));

			if($this->hooks->isRegistered('calendarevent_deleted')){
				$this->hooks->process('calendarevent_deleted', array('id' => $field, 'data' => $arrOld));
			}

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

			if($this->hooks->isRegistered('calendarevent_closeraidstatus')){
				$this->hooks->process('calendarevent_closeraidstatus', array('id' => $id, 'data' => $openclose));
			}

			return $id;

		}

		public function update_note($id, $note=''){
			$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'notes'	=> $note,
			))->execute($id);

			$this->pdh->enqueue_hook('calendar_events_update', array($id));

			if($this->hooks->isRegistered('calendarevent_updatenote')){
				$this->hooks->process('calendarevent_updatenote', array('id' => $id, 'data' => $note));
			}

			return $id;

		}

		public function update_timezone($id, $timezone=''){
			$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'timezone'	=> $timezone,
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

				if($this->hooks->isRegistered('calendarevent_moved')){
					$this->hooks->process('calendarevent_moved', array('id' => $eventid, 'data' => $arrNew));
				}
			}
			return (isset($objQuery) && $objQuery) ? $eventid : false;
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

				if($this->hooks->isRegistered('calendarevent_resized')){
					$this->hooks->process('calendarevent_resized', array('id' => $eventid, 'data' => $arrNew));
				}
			}
			return (isset($objQuery) && $objQuery) ? $eventid : false;
		}

		public function handle_invitation($eventid, $userid, $status='decline'){
			if($eventid > 0){
				$extensiondata		= $this->pdh->get('calendar_events', 'extension', array($eventid));
				$invite_attendees	= (isset($extensiondata['invited_attendees']) && is_array($extensiondata['invited_attendees'])) ? $extdata_old['invited_attendees'] : array();
				$current_status		= (isset($invite_attendees[$userid]) && $invite_attendees[$userid] > 0) ? $invite_attendees[$userid] : 0;

				if(($status == 'accept' && $current_status == 0) || ($status == 'decline' && $current_status == 1)){
					$extensiondata['invited_attendees'][$userid] = ($status == 'accept') ? 1 : 0;

					$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
						'extension'		=> serialize($extensiondata),
					))->execute($eventid);
					$this->pdh->enqueue_hook('calendar_events_update', array($eventid));

					if($this->hooks->isRegistered('calendarevent_handle_invitation')){
						$this->hooks->process('calendarevent_handle_invitation', array('id' => $eventid, 'data' => $extensiondata));
					}
				}
			}
			return (isset($objQuery) && $objQuery) ? $eventid : false;
		}

		public function handle_attendance($eventid, $userid, $status='decline'){
			if($eventid > 0 && $userid > 0){
				$extensiondata		= $this->pdh->get('calendar_events', 'extension', array($eventid));
				$attendance			= (isset($extensiondata['attendance']) && isset($extensiondata['attendance'][$userid])) ? $extensiondata['attendance'][$userid] : false;
				switch($status){
					case 'attend':		$attendancestatus = 1; break;
					case 'maybe':		$attendancestatus = 2; break;
					case 'decline':		$attendancestatus = 3; break;
					default:			$attendancestatus = 0;
				}
				if($attendancestatus > 0 && (!$attendance || $attendance != $status)){
					$extensiondata['attendance'][$userid] = $attendancestatus;

					$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
						'extension'		=> serialize($extensiondata),
					))->execute($eventid);
					$this->pdh->enqueue_hook('calendar_events_update', array($eventid));

					if($this->hooks->isRegistered('calendarevent_handle_attendance')){
						$this->hooks->process('calendarevent_handle_attendance', array('id' => $eventid, 'data' => $extensiondata));
					}
				}
			}
			return (isset($objQuery) && $objQuery) ? $eventid : false;
		}

		public function auto_addchars($raidtype, $raidid, $raidleaders=array(), $group=false, $status=false){
			//Auto confirm Groups
			$arrAutoconfirmGroups	= $this->config->get('calendar_raid_confirm_raidgroupchars');

			// auto add raid groups
			$raidgroups = ($group && is_array($group)) ? $group : $this->config->get('calendar_raid_add_raidgroupchars');
			if(is_array($raidgroups) && count($raidgroups) > 0){
				$memberids = $this->pdh->get('raid_groups_members', 'member_list', array($raidgroups, true));
				if(is_array($memberids)){
					foreach($memberids as $memberid){
						$userid			= $this->pdh->get('user', 'userid', array($memberid));
						$away_mode		= $this->pdh->get('calendar_raids_attendees', 'user_awaymode', array($userid, $raidid));
						$defaultrole	= $this->pdh->get('member', 'defaultrole', array($memberid));
						$signupstatus	= ($away_mode) ? 2 : 1;
						$signupnote		= $this->pdh->get('$raid_groups_members', 'charSelectionMethod', array($memberid));
						$signupnote_txt	= ($signupnote) ? $this->user_lang('raidevent_raid_note_'.$signupnote) : '';

						if($memberid > 0){
							if(($raidtype == 'role' && $defaultrole > 0) || $raidtype == 'class' || $raidtype == 'none'){
								//Autoconfirm
								if(!$away_mode && is_numeric($status)){
									$signupstatus = $status;
								}else{
									if(is_array($arrAutoconfirmGroups) && count($arrAutoconfirmGroups) > 0 && $signupstatus == 1){
										if($this->pdh->get('raid_groups_members', 'check_user_is_in_groups', array($userid, $arrAutoconfirmGroups))){
											$signupstatus = 0;
										}
									}
								}

								// hide empty roles if the game module allows it
								if($raidtype == 'role' && $defaultrole > 0 && $this->game->get_game_settings('calendar_hide_emptyroles')){
									$raiddistri = $this->pdh->get('calendar_events', 'raiddistribution', array($raidid, $defaultrole));
									if($raiddistri == 0){
										continue;
									}
								}

								$this->pdh->put('calendar_raids_attendees', 'update_status', array(
									$raidid,
									$memberid,
									(($defaultrole) ? $defaultrole : 0),
									$signupstatus,
									0,
									0,
									$signupnote_txt,
								));
							}
						}
					}
				}
			}

			// auto add and confirm the raidleaders
			if(is_array($raidleaders) && $this->config->get('calendar_raidleader_autoinvite') == 1){
				foreach($raidleaders as $raidleaderid){
					$away_mode		= $this->pdh->get('calendar_raids_attendees', 'attendee_awaymode', array($raidleaderid, $raidid));
					$rlstatus		= ($away_mode) ? 2 : 0;
					$defaultrole	= $this->pdh->get('member', 'defaultrole', array($raidleaderid));

					// hide empty roles if the game module allows it
					if($raidtype == 'role' && $defaultrole > 0 && $this->game->get_game_settings('calendar_hide_emptyroles')){
						$raiddistri = $this->pdh->get('calendar_events', 'raiddistribution', array($raidid, $defaultrole));
						if($raiddistri == 0){
							continue;
						}
					}

					$this->pdh->put('calendar_raids_attendees', 'update_status', array(
						$raidid,
						$raidleaderid,
						(($defaultrole) ? $defaultrole : 0),
						$rlstatus,	// status
						0,
						0,
						'',
					));
				}
			}
			$this->pdh->process_hook_queue();
		}

		public function add_extension($eventID, $arrExtension){
			$current_extensiondata = $this->pdh->get('calendar_events', 'extension', array($eventID));
			if(is_array($arrExtension)){
				$new_extensiondata = array_merge($current_extensiondata, $arrExtension);

				// write the extension data to the database
				$objQuery = $this->db->prepare("UPDATE __calendar_events :p WHERE id=?")->set(array(
					'extension'				=> serialize($new_extensiondata),
				))->execute($eventID);
				$this->pdh->enqueue_hook('calendar_events_update');

				if($this->hooks->isRegistered('calendarevent_addextension')){
					$this->hooks->process('calendarevent_addextension', array('id' => $eventID, 'data' => $new_extensiondata));
				}
			}
		}

		public function raid_transformed($eventID, $raidID){
			$data['transformed']	= array(
				'id'	=> $raidID,
				'date'	=> $this->time->time,
				'user'	=> $this->user->data['user_id']
			);
			$this->add_extension($eventID, $data);
			$this->pdh->enqueue_hook('calendar_events_update');
		}

		// continue an existing raid at a new date
		public function ContinueRaid($eventID, $startdate, $enddate, $note){
			$eventdata		= $this->pdh->get('calendar_events', 'data', array($eventID));
			$arrGuests      = $this->pdh->get('calendar_raids_guests', 'members', array($eventID));
			$arrAttendees   = $this->pdh->get('calendar_raids_attendees', 'attendees', array($eventID));

			$new_eventid = $this->add_cevent(
				$eventdata['calendar_id'],
				$eventdata['name'],
				$eventdata['creator'],
				$startdate,
				$enddate,
				0,
				$note,
				$eventdata['allday'],
				$eventdata['extension'],
				$eventdata['cloneid'],
				$eventdata['private']
			);
			$this->pdh->enqueue_hook('calendar_events_update');

			// clone the attendees
			foreach($arrAttendees as $attendeeID=>$attendeeData){
				$this->pdh->put('calendar_raids_attendees', 'update_status', array(
					$new_eventid,
					$attendeeID,
					$attendeeData['member_role'],
					$attendeeData['signup_status'],
					$attendeeData['raidgroup'],
					0,
					$attendeeData['note'],
					$attendeeData['signedbyadmin'],
				));
			}
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');

			// clone the guests
			foreach($arrGuests as $guestID=>$guestData){
				$this->pdh->put('calendar_raids_attendees', 'insert_guest', array(
					$new_eventid,
					$guestData['name'],
					$guestData['class'],
					$guestData['raidgroup'],
					$guestData['note'],
					$guestData['email'],
				));
			}
			$this->pdh->enqueue_hook('guests_update');
			$this->pdh->process_hook_queue();
		}
	}
}
