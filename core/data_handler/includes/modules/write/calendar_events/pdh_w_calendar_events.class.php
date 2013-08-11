<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_events')) {
	class pdh_w_calendar_events extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'user', 'config', 'logs');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

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
					$statt = $this->db->query("UPDATE __calendar_events SET :params WHERE cloneid=?", array(
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
					), $cloneid_eventid);

					//now, alter the parent event
					$statt = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
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
					), $cloneid_eventid);
					$this->pdh->enqueue_hook('calendar_events_update');

				// and now, handle the single events
				}else{
					$statt = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
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
					), $id);
					if(!$statt) {
						return false;
					}
				}
				
				// add log entry
				$arrOld = array(
					'calendar_id'			=> $old['cal_id'],
					'name'					=> $old['name'],
					'timestamp_start'		=> "{D_".$old['startdate']."}",
					'timestamp_end'			=> "{D_".$old['enddate']."}",
					'allday'				=> $old['allday'],
					'notes'					=> $old['notes'],
					'repeating'				=> $old['repeat'],
					'extension'				=> serialize($extdata),
					'mode'					=> $extdata['calendarmode'],
				);
				$arrNew = array(
					'calendar_id'			=> $cal_id,
					'name'					=> $name,
					'timestamp_start'		=> "{D_".$startdate."}",
					'timestamp_end'			=> "{D_".$enddate."}",
					'allday'				=> $allday,
					'notes'					=> $notes,
					'repeating'				=> $repeat,
					'extension'				=> serialize($extension),
					'mode'					=> $extension['calendarmode'],
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

			$result = $this->db->query('INSERT INTO __calendar_events :params', array(
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
			));
			$id = $this->db->insert_id();
			// add log entry if user ID is available
			if(isset($this->user->data['user_id']) && $this->user->data['user_id'] != ANONYMOUS){
				$arrNew = array(
					'calendar_id'			=> $cal_id,
					'name'					=> (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name),
					'creator'				=> $creator,
					'timestamp_start'		=> "{D_".$startdate."}",
					'timestamp_end'			=> "{D_".$enddate."}",
					'allday'				=> ($allday > 0) ? $allday : 0,
					'private'				=> 0,
					'visible'				=> 1,
					'closed'				=> 0,
					'notes'					=> $notes,
					'repeating'				=> $repeat,
					'extension'				=> (is_array($extension)) ? serialize($extension) : '',
					'cloneid'				=> ($cloneid > 0) ? $cloneid : 0,
					'mode'					=> $extension['calendarmode'],
				);
				
				$log_action = $this->logs->diff(false, $arrNew, $this->arrLogLang);
				
				$this->log_insert('calendar_log_eventadded', $log_action, $id, (($extension['raid_eventid'] > 0) ? $this->pdh->get('event', 'name', array($extension['raid_eventid'])) : $name), true, 'calendar');
			}
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;
		}

		public function delete_cevent($id, $del_repeatable=false){
			$arrOld = $this->pdh->get('calendar_events', 'data', array($id));
			
			$field = (is_array($id)) ? implode(', ', $id) : $id;
			if($del_repeatable){
				$query = $this->db->query("SELECT DISTINCT cloneid FROM __calendar_events WHERE id IN(".$field.")");
				$clone_events	= array();
				while($row = $this->db->fetch_record($query)){
					//Don't delete events with cloneid = 0
					if (intval($row['cloneid']) == 0) continue;
					$clone_events[]	= $row['cloneid'];
					$this->db->query("DELETE FROM __calendar_events WHERE (cloneid=".$row['cloneid'].") OR (id=".$row['cloneid'].")");
				}
				$this->db->query("DELETE FROM __calendar_events WHERE (cloneid IN (".$field.")) OR (id IN (".$field."))");
			}else{
				$this->db->query("DELETE FROM __calendar_events WHERE id IN (".$field.")");
			}
			
			// delete the attendees
			$this->db->query("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id IN(".$field.")");
			if($del_repeatable && is_array($clone_events) && count($clone_events) > 0){
				$query = $this->db->query("SELECT DISTINCT id FROM __calendar_events WHERE cloneid IN(".implode(', ', $clone_events).")");
				while($row = $this->db->fetch_record($query)){
					$this->db->query("DELETE FROM __calendar_raid_attendees WHERE calendar_events_id=".$row['id']);
				}
			}
			
			//Logging
			$arrOld['timestamp_start'] = "{D_".$arrOld['timestamp_start']."}";
			$arrOld['timestamp_end'] = "{D_".$arrOld['timestamp_end']."}";
			$arrOld['extension'] = serialize($arrOld['extension']);
			$log_action = $this->logs->diff(false, $arrOld, $this->arrLogLang);
			$this->log_insert('calendar_log_eventdeleted', $log_action, $id, $this->pdh->get('calendar_events', 'name', array($id)), true, 'calendar');
			
			// perform the hooks
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return true;
		}
		
		public function change_closeraidstatus($id, $closed=true){
			$arrOld['closed'] = $this->pdh->get('calendar_events', 'raidstatus', array($id));
			$result = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
				'closed'	=> (($closed) ? 1 : 0),
			), $id);
			
			//Logging
			$arrNew['closed'] =  (($closed) ? 1 : 0);	
			$openclose = ($closed) ? 'closed' : 'opened';
			$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
			if ($log_action) $this->log_insert('calendar_log_raid'.$openclose, $log_action, $id, $this->pdh->get('calendar_events', 'name', array($id)), true, 'calendar');
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;

		}
		
		public function update_note($id, $note=''){
			$result = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
				'notes'	=> $note,
			), $id);
			$this->pdh->enqueue_hook('calendar_events_update', array($id));
			return $id;

		}
		
		public function delete_clones($cloneid){
			$this->db->query("DELETE FROM __calendar_events WHERE cloneid=?", false, $cloneid);
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

				$result = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
					'timestamp_start'	=> $eventdata['timestamp_start'] + (int)$general_delta_sec,
					'timestamp_end'		=> $eventdata['timestamp_end'] + (int)$general_delta_sec,
					'allday'			=> (($move) ? (($allday == 'true') ? 1 : 0) : $eventdata['allday']),
					'extension'			=> serialize($a_extension)
				), $eventid);
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
				$result = $this->db->query("UPDATE __calendar_events SET :params WHERE id=?", array(
					'timestamp_end'		=> $old_timestamp + (int)$general_delta_sec,
				), $eventid);
				
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
		
		public function auto_addchars($raidtype, $raidid, $raidleaders=array()){			
			//Auto confirm Groups
			$arrAutoconfirmGroups = unserialize($this->config->get('calendar_raid_autoconfirm'));
			$signupstatus	= 1; //Angemeldet

			// auto add groups
			$usergroups = unserialize($this->config->get('calendar_raid_autocaddchars'));
			if(is_array($usergroups) && count($usergroups) > 0){
				$userids = $this->pdh->get('user_groups_users', 'user_list', array($usergroups));
				if(is_array($userids)){
					foreach($userids as $userid){
						$memberid		= $this->pdh->get('member', 'mainchar', array($userid));
						$defaultrole	= $this->pdh->get('member', 'defaultrole', array($memberid));
						if($memberid > 0){
							if(($raidtype == 'role' && $defaultrole > 0) || $raidtype == 'class' || $raidtype == 'none'){
								//Autoconfirm
								if(is_array($arrAutoconfirmGroups) && count($arrAutoconfirmGroups) > 0 && $signupstatus == 1){
									if($this->user->check_group($usergroups, false, $userid)){
										$signupstatus = 0;
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
?>