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

class editcalendarevent_pageobject extends pageobject {

	public static $shortcuts = array('email'=>'MyMailer');

	public function __construct() {
		$handler = array(
			'deletetemplate'=> array('process' => 'process_deletetemplate', 'csrf'=>true),
			'addtemplate'	=> array('process' => 'process_addtemplate',  'csrf'=>true),
			'loadtemplate'	=> array('process' => 'process_loadtemplate'),
			'ajax_dropdown'	=> array('process' => 'ajax_dropdown'),
			'addevent'		=> array('process' => 'process_addevent',  'csrf'=>true)
		);

		if(($this->in->get('eventid', 0) > 0)){
			if($this->user->check_auth('a_cal_revent_conf', false) || $this->pdh->get('calendar_events', 'check_operatorperm', array($this->in->get('eventid', 0), $this->user->data['user_id'])) ){
				// permission granted
			}else{
				message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);
			}
		}else{
			$this->user->check_auth('u_cal_event_add');
		}

		parent::__construct(false, $handler, array(), null, '', 'eventid');
		$this->process();
	}

	// fetch an event template as JSON data
	public function process_loadtemplate(){
		$tmp_id		= $this->in->get('loadtemplate', '');
		$tmp_id		= explode('_', $tmp_id);
		if(isset($tmp_id[1])){
			$load_id	= $tmp_id[1];
			$load_type	= $tmp_id[0];
			$jsondata = ($load_type == 't') ? $this->pdh->get('calendar_raids_templates', 'templates', array($load_id)) : $this->pdh->get('calendar_events', 'template', array($load_id));
			$tmparray = array();
			$distribution = '';
			foreach($jsondata as $tplkey=>$tplvalue){
				if($tplkey == 'cal_raidmodeselect'){
					$distribution = $tplvalue;
				}
				if($tplkey == 'distribution'){
					foreach($tplvalue as $clssid=>$clssval)
					$tmparray[] = array(
						'field'	=> 'inp_'.$distribution.'_'.$clssid,
						'value'	=> $clssval
					);
				}else{
					$tmparray[] = array(
						'field'	=> $tplkey,
						'value'	=> $tplvalue
					);
				}
			}
			echo json_encode($tmparray);exit;
		}
	}

	// this function generates a dropdown with the events
	public function ajax_dropdown(){
		$output = (new hdropdown('raid_eventid', array('options' => $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list'))), 'value' => $this->in->get('raidevent_id', 0), 'id' => 'input_eventid', 'class' => 'resettemplate_input')))->output();
		echo($output);
		exit;
	}

	// send notification of the created event/raid to all users
	private function notify_newevent($eventID, $type='event'){
		$strEventName = $this->pdh->get('calendar_events', 'name', array($eventID));
		if($strEventName && $strEventName != "") $strEventName .= ', ';

		$a_users = $this->pdh->get('user', 'active_users');
		if(is_array($a_users) && count($a_users) > 0){
			foreach($a_users as $userid){
				$strEventTitle	= $strEventName.$this->time->date_for_user($userid, $this->pdh->get('calendar_events', 'time_start', array($eventID)), true).' ('.(($type=='raid') ? $this->user->lang('calendar_mode_raid') :  $this->user->lang('calendar_mode_event')).')';
				$this->ntfy->add('calendarevent_new', $eventID, $this->pdh->get('calendar_events', 'notes', array($eventID)), $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventID)), $eventID, true, true), $userid, $strEventTitle);
			}
		}
	}

	// send notification to invited
	private function notify_invitations($eventID, $invited_users=false, $type='event'){
		$eventextension	= $this->pdh->get('calendar_events', 'extension', array($eventID));
		$strEventName	= $this->pdh->get('calendar_events', 'name', array($eventID));
		if($strEventName && $strEventName != "") $strEventName .= ', ';

		if($type == 'event'){
			$a_users = (isset($invited_users) && is_array($invited_users)) ? $invited_users : ((isset($eventextension['invited'])) ? $eventextension['invited'] : false);
		}else{
			$a_users = (isset($invited_users) && is_array($invited_users)) ? $invited_users : ((isset($eventextension['invited'])) ? $eventextension['invited'] : false);
		}

		$a_users = array_unique($a_users);

		if(is_array($a_users) && count($a_users) > 0){
			foreach($a_users as $userid){
				$strEventTitle	= $strEventName.$this->time->date_for_user($userid, $this->pdh->get('calendar_events', 'time_start', array($eventID)), true).' ('.(($type=='raid') ? $this->user->lang('calendar_mode_raid') :  $this->user->lang('calendar_mode_event')).')';
				$this->ntfy->add('calendarevent_invitation', $eventID, $this->pdh->get('calendar_events', 'creator', array($eventID)), $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventID)), $eventID, true, true), $userid, $strEventTitle);
			}
		}
	}

	// remove notifications for invited
	private function remove_invitation_notification($eventID, $userID){
		$this->ntfy->deleteNotification('calenderevent_invitation', $eventID, $userID);
	}

	// save an event template in database
	public function process_addtemplate(){
		if($this->in->get('raidmode') == 'role'){
			foreach($this->pdh->get('roles', 'roles', array()) as $classid=>$classname){
				$raid_clsdistri[$classid] = $this->in->get('roles_'.$classid.'_count', 0);
			}
		}elseif($this->in->get('raidmode') == 'class'){
			$classdata = $this->game->get_primary_classes(array('id_0'));
			foreach($classdata as $classid=>$classname){
				$raid_clsdistri[$classid] = $this->in->get('classes_'.$classid.'_count', 0);
			}
		} else {
			$raid_clsdistri[0] = $this->in->get('raid_attendees_count', 0);
		}
		$this->pdh->put('calendar_raids_templates', 'save_template', array(
			(($this->in->get('templatename')) ? $this->in->get('templatename') : 'template-'.random_integer(0,1000)),
			array(
				'input_eventid'		=> $this->in->get('raid_eventid', 0),
				'input_dkpvalue'	=> $this->in->get('raid_value'),
				'input_note'		=> $this->in->get('note'),
				'selectmode'		=> 'raid',
				'cal_raidmodeselect'=> $this->in->get('raidmode'),
				'dw_raidleader'		=> $this->in->getArray('raidleader', 'int'),
				'deadlinedate'		=> $this->in->get('deadlinedate', 0),
				'distribution'		=> $raid_clsdistri,
				'calendar_id'		=> $this->in->get('calendar_id'),
			)
		));
		$this->pdh->process_hook_queue();
	}

	// delete an event template out of database
	public function process_deletetemplate(){
		$this->pdh->put('calendar_raids_templates', 'delete_template', array($this->in->get('deletetemplate', 0)));
		$this->pdh->process_hook_queue();

		// Build the new select
		$seldata = $this->pdh->get('calendar_raids_templates', 'dropdowndata');
		if(is_array($seldata)){
			foreach($seldata as $tpl_category=>$tpl_data){
				$out .= '<optgroup label="'.$this->user->lang('optgroup_'.$tpl_category).'">';
				foreach($tpl_data as $ddid=>$ddval){
					$out .= "<option value='".$ddid."'>".$ddval."</option>";
				}
				$out .= '</optgroup>';
			}
		}
		echo $out;
		exit;
	}

	// add the event to the database
	public function process_addevent(){
		if($this->in->get('calendarmode') == 'raid'){
			// fetch the count
			if($this->in->get('raidmode') == 'role'){
				foreach($this->pdh->get('roles', 'roles', array()) as $classid=>$classname){
					$intCount = $this->in->get('roles_'.$classid.'_count', 0);
					$raid_clsdistri[$classid] = ($intCount < 0) ? 0 : $intCount;
				}
			}else{
				$classdata = $this->game->get_primary_classes(array('id_0'));
				foreach($classdata as $classid=>$classname){
					$intCount = $this->in->get('classes_'.$classid.'_count', 0);
					$raid_clsdistri[$classid] = ($intCount < 0) ? 0 : $intCount;
				}
			}

			// Auto confirm / confirm by raid-add-setting
			$asi_groups		= $this->in->getArray('asi_group');
			$asi_status		= (is_array($asi_groups) && count($asi_groups) > 0) ? $this->in->get('asi_status', 0) : false;
			$invited_rgroup	= $this->in->getArray('invited_raidgroup', 'int');

			$raidid = $this->pdh->put('calendar_events', 'add_cevent', array(
				$this->in->get('calendar_id', 1),
				'',
				$this->user->data['user_id'],
				$this->time->fromformat($this->in->get('startdate'), 1),
				$this->time->fromformat($this->in->get('enddate'), 1),
				$this->in->get('repeating'),
				$this->in->get('note'),
				0,
				array(
					'raid_eventid'		=> $this->in->get('raid_eventid', 0),
					'calendarmode'		=> $this->in->get('calendarmode'),
					'raid_value'		=> $this->in->get('raid_value', 0),
					'deadlinedate'		=> $this->in->get('deadlinedate', 0.5),
					'raidmode'			=> $this->in->get('raidmode'),
					'raidleader'		=> $this->in->getArray('raidleader', 'int'),
					'distribution'		=> $raid_clsdistri,
					'attendee_count'	=> $this->in->get('raid_attendees_count', 0),
					'created_on'		=> $this->time->time,
					'autosignin_group'	=> $asi_groups,
					'autosignin_status'	=> (int)$asi_status,
					'invited_raidgroup'	=> $invited_rgroup,
				),
				0,
				$this->in->get('private', 0),
			));
			$this->pdh->process_hook_queue();

			// if the raid had been added, do the rest...
			if($raidid > 0){
				$this->pdh->put('calendar_events', 'auto_addchars', array($this->in->get('raidmode'), $raidid, $this->in->getArray('raidleader', 'int'), $asi_groups, $asi_status));
				if(is_array($invited_rgroup) && count($invited_rgroup) > 0){
					$invited_users	= $this->pdh->get('raid_groups_members', 'userOfGroups', array($invited_rgroup));
				}
				if($invited_users > 0 && $raidid > 0){
					$this->notify_invitations($raidid, $invited_users, 'raid');
				} elseif ($raidid > 0){
					$this->notify_newevent($raidid, 'raid');
				}
			}
		}else{
			$withtime			= ($this->in->get('allday') == '1') ? 0 : 1;
			$invited_users		= $this->in->getArray('invited', 'int');
			$invited_usergroup	= $this->in->getArray('invited_usergroup', 'int');
			if(is_array($invited_usergroup) && count($invited_usergroup) > 0){
				$invited_users = $this->pdh->get('user_groups_users', 'user_list', array($invited_usergroup));
			}

			// fetch gelocation lat/lon values
			$location_value		= $this->in->get('location');
			$location_longlat	= $this->geoloc->getCoordinates($location_value);

			// the query
			$raidid			= $this->pdh->put('calendar_events', 'add_cevent', array(
				$this->in->get('calendar_id'),
				$this->in->get('eventname'),
				$this->user->data['user_id'],
				$this->time->fromformat($this->in->get('startdate'), $withtime),
				$this->time->fromformat($this->in->get('enddate'), $withtime),
				$this->in->get('repeating'),
				$this->in->get('note'),
				$this->in->get('allday'),
				array(
					'invited'			=> $invited_users,
					'invited_usergroup'	=> $invited_usergroup,
					'location'			=> $location_value,
					'location-lat'		=> $location_longlat['latitude'],
					'location-lon'		=> $location_longlat['longitude'],
					'calevent_icon'		=> $this->in->get('calevent_icon'),
				),
				0,
				$this->in->get('private', 0),
			));

			// send the notification for a event invitiation
			if($invited_users > 0 && $raidid > 0){
				$this->notify_invitations($raidid, $invited_users);
			} elseif ($raidid > 0){
				$this->notify_newevent($raidid, 'event');
			}
		}
		if($this->in->get('repeating', 0) > 0){
			$this->cronjobs->run_cron('calevents_repeatable', true);
		}
		$this->pdh->process_hook_queue();

		// close the dialog
		if($this->in->exists('simple_head')){
			$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
		}else{
			redirect($this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($this->url_id)), $this->url_id, true, true));
		}
	}

	// check if there are attendees and if they have a role set
	private function check_roleraid_attendees($eventid){
		$attendees	= $this->pdh->get('calendar_raids_attendees', 'attendees', array($this->url_id));

		// check if there are attendees in this raid
		if(is_array($attendees) && count($attendees) > 0){
			foreach($attendees as $attendeeid=>$attendeedata){
				$member_role_id	= (int)$attendeedata['member_role'];

				// check if the role is empty or null
				if(!$member_role_id || $member_role_id == 0){
					// update the role for that event
					$default_role	= $this->pdh->get('member', 'defaultrole', array($attendeeid));
					if($default_role > 0){
						$this->pdh->put('calendar_raids_attendees', 'update_role', array($eventid, $attendeeid, $default_role));
					}else{
						// remove that attendee
					}

				}
			}
		}
	}

	// update the event in the database
	public function update(){
		if($this->in->get('calendarmode') == 'raid'){
			// fetch the count
			if($this->in->get('raidmode') == 'role'){
				foreach($this->pdh->get('roles', 'roles', array()) as $classid=>$classname){
					$raid_clsdistri[$classid] = $this->in->get('roles_'.$classid.'_count', 0);
				}
				$this->check_roleraid_attendees($this->url_id);
			}else{
				$classdata = $this->game->get_primary_classes(array('id_0'));
				foreach($classdata as $classid=>$classname){
					$raid_clsdistri[$classid] = $this->in->get('classes_'.$classid.'_count', 0);
				}
			}

			$this->pdh->put('calendar_events', 'update_cevents', array(
				$this->url_id,
				$this->in->get('calendar_id', 1),
				false,
				$this->time->fromformat($this->in->get('startdate'), 1),
				$this->time->fromformat($this->in->get('enddate'), 1),
				$this->in->get('repeating'),
				$this->in->get('edit_clones', 0),
				$this->in->get('note'),
				0,
				array(
					'updated_on'		=> time(),
					'raid_eventid'		=> $this->in->get('raid_eventid', 0),
					'calendarmode'		=> $this->in->get('calendarmode'),
					'raid_value'		=> $this->in->get('raid_value', 0),
					'deadlinedate'		=> $this->in->get('deadlinedate', 0.5),
					'raidmode'			=> $this->in->get('raidmode'),
					'raidleader'		=> $this->in->getArray('raidleader', 'int'),
					'distribution'		=> $raid_clsdistri,
					'attendee_count'	=> $this->in->get('raid_attendees_count', 0),
					'invited_raidgroup'	=> $this->in->getArray('invited_raidgroup', 'int'),
				),
				$this->in->get('private', 0),
			));
		}else{
			$withtime			= ($this->in->get('allday') == '1') ? 0 : 1;
			$invited_users		= $this->in->getArray('invited', 'int');
			$invited_usergroup	= $this->in->getArray('invited_usergroup', 'int');
			if(is_array($invited_usergroup) && count($invited_usergroup) > 0){
				$invited_users = $this->pdh->get('user_groups_users', 'user_list', array($invited_usergroup));
			}
			$this->pdh->put('calendar_events', 'update_cevents', array(
				$this->url_id,
				$this->in->get('calendar_id'),
				$this->in->get('eventname'),
				$this->time->fromformat($this->in->get('startdate'), $withtime),
				$this->time->fromformat($this->in->get('enddate'), $withtime),
				$this->in->get('repeating'),
				$this->in->get('edit_clones', 0),
				$this->in->get('note'),
				$this->in->get('allday'),
				array(
					'invited'			=> $invited_users,
					'invited_usergroup'	=> $invited_usergroup,
					'location'			=> $this->in->get('location'),
					'location-lat'		=> '',
					'location-lon'		=> '',
					'calevent_icon'		=> $this->in->get('calevent_icon'),
				),
				$this->in->get('private', 0),
			));

			// send notifications to newly invited users & remove notification for removed users
			$current_invited_users	= $this->pdh->get('calendar_events', 'extension', array($this->url_id, 'invited'));
			if($current_invited_users !== $invited_users){
				// the new users
				$invite_new_users	= array_diff($invited_users, $current_invited_users);
				if(count($invite_new_users) > 0){
					$this->notify_invitations($this->url_id, $invite_new_users);
				}

				// someone removed?
				$invite_removed_users	= array_diff($current_invited_users, $invited_users);
				if(count($invite_removed_users) > 0){
					$this->remove_invitation_notification($this->url_id, $invite_removed_users);
				}
			}
		}

		//Flush Cache so the Cronjob can access the new data
		$this->pdh->process_hook_queue();

		if($this->in->get('repeating') > 0){
			//Process Queue, so the Cronjob has reliable data
			$this->pdh->process_hook_queue();
			$this->cronjobs->run_cron('calevents_repeatable', true, true);
		}

		$this->pdh->process_hook_queue();

		// close the dialog
		if($this->in->exists('simple_head')){
			$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
		}else{
			redirect($this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($this->url_id)), $this->url_id, true, true));
		}
	}

	// the main page display
	public function display() {
		if(($this->in->get('hookid', 0) > 0) && $this->in->get('hookapp', '') != ''){
			$arrHookData	= $this->hooks->process('calendarevent_prefill', array('hookapp' => $this->in->get('hookapp'), 'hookid' => $this->in->get('hookid', 0)), true);
			$eventdata					= $arrHookData['eventdata'];
			$this->values_available		= true;
		}else{
			$eventdata					= $this->pdh->get('calendar_events', 'data', array($this->url_id));
			$this->values_available		= ($this->url_id > 0) ? true : false;
		}
		if($this->in->get('debug', 0) == 1){
			pd($eventdata);
		}

		// the repeat array
		$drpdwn_repeat = array(
			'0'			=> '--',
			'1'			=> $this->user->lang('calendar_repeat_daily'),
			'7'			=> $this->user->lang('calendar_repeat_weekly'),
			'14'		=> $this->user->lang('calendar_repeat_2weeks'),
			'custom'	=> $this->user->lang('calendar_repeat_custom'),
		);

		// raid/ role distri
		$raidmode_array = array(
			'class'		=> $this->user->lang('calendar_class_distri'),
			'role'		=> $this->user->lang('calendar_role_distri'),
			'none'		=> $this->user->lang('calendar_no_distri')
		);

		// Calendar Mode
		$calendar_mode_array = array(
			'event'		=> $this->user->lang('calendar_mode_event'),
			'raid'		=> $this->user->lang('calendar_mode_raid')
		);

		// Repeat array
		$radio_repeat_array	= array(
			'0'			=>$this->user->lang('calendar_event_editone'),
			'1'			=>$this->user->lang('calendar_event_editall'),
			'2'			=>$this->user->lang('calendar_event_editall_future'),
		);

		// raid status array
		$raidcal_status = $this->config->get('calendar_raid_status');
		$raidstatus = array();
		if(is_array($raidcal_status)){
			foreach($raidcal_status as $raidcalstat_id){
				if($raidcalstat_id != 4){
					$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
				}
			}
		}

		// The roles Fields
		foreach($this->pdh->get('roles', 'roles', array()) as $row){
			$this->tpl->assign_block_vars('raid_roles', array(
				'LABEL'			=> $row['name'],
				'NAME'			=> "roles_" . $row['id'] . "_count",
				'CLSSID'		=> $row['id'],
				'COUNT'			=> (isset($eventdata['extension']) && isset($eventdata['extension']['distribution']) && $eventdata['extension']['distribution'][$row['id']]) ? $eventdata['extension']['distribution'][$row['id']] : '0',
				'ICON'			=> $this->game->decorate('roles', $row['id']),
				'DISABLED'		=> (isset($eventdata['extension']) && isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'class') ? 'disabled="disabled"' : ''
			));
		}

		// The class fields
		$classdata = $this->game->get_primary_classes(array('id_0'));
		foreach($classdata as $classid=>$classname){
			$this->tpl->assign_block_vars('raid_classes', array(
				'LABEL'			=> $classname,
				'NAME'			=> "classes_" . $classid . "_count",
				'CLSSID'		=> $classid,
				'COUNT'			=> (isset($eventdata['extension']['distribution'][$classid]) && $eventdata['extension']['distribution'][$classid]) ? $eventdata['extension']['distribution'][$classid] : '0',
				'ICON'			=> $this->game->decorate('primary', $classid),
				'DISABLED'		=> (isset($eventdata['extension']) && isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'role') ? 'disabled="disabled"' : ''
			));
		}

		// Raidleaders
		$raidleader_array = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
		asort($raidleader_array);

		// Load the default dates
		$default_deadlineoffset	= (($this->config->get('calendar_addraid_deadline')) ? $this->config->get('calendar_addraid_deadline') : 1);
		if($this->values_available > 0){
			$defdates = array(
				'start'		=> $eventdata['timestamp_start'],
				'end'		=> $eventdata['timestamp_end'],
				'deadline'	=> (isset($eventdata['extension']['deadlinedate']) && $eventdata['extension']['deadlinedate'] > 0) ? $eventdata['extension']['deadlinedate'] : $default_deadlineoffset
			);
		}else{
			$default_raidduration	= ((($this->config->get('calendar_addraid_duration')) ? $this->config->get('calendar_addraid_duration') : 120)*60);

			// if the default time should be used, set it...
			$use_default_starttime = $this->config->get('calendar_addraid_use_def_start') && preg_match('#[:]#', $this->config->get('calendar_addraid_def_starttime'));
			if($this->in->get('timestamp', 0) > 0){
				$default_datetime	= ($this->in->get('calview', 'month') == 'month') ? $this->time->newtime($this->in->get('timestamp', 0)) : $this->time->convert_timestamp_from_utc($this->in->get('timestamp', 0));
				$starttimestamp		= ($use_default_starttime) ? $this->time->newtime($this->in->get('timestamp', 0), $this->config->get('calendar_addraid_def_starttime')) : $default_datetime;
			}else{
				$starttimestamp		= ($use_default_starttime) ? $this->time->fromformat($this->config->get('calendar_addraid_def_starttime'), $this->user->style['time']) : $this->time->time;
			}

			$defdates = array(
				'start'		=> $starttimestamp,
				'end'		=> $starttimestamp+$default_raidduration,
				'deadline'	=> $default_deadlineoffset,
			);
		}

		$beforeclosefunc = "
			$.post('".$this->routing->build('editcalendarevent')."&ajax_dropdown=true', { raidevent_id: $('body').data('raidevent_id') }, function(data) {
				$('#raidevent_dropdown').html(data);
			});";

		// Set the code for the second timepicker difference
		$startdate_onselect = "var startDate = $(this).datetimepicker('getDate');
				var endDate = new Date(startDate.getTime() + (".(($this->config->get('calendar_addraid_duration') > 0) ? $this->config->get('calendar_addraid_duration') : 120)."*60000))
				var newDate	= new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), endDate.getHours(), endDate.getMinutes());
				$('#cal_enddate').datetimepicker('setDate', newDate);";

		$this->jquery->Dialog('AddEventDialog', $this->user->lang('raidevent_raidevent_add'), array('url'=>$this->server_path.'admin/manage_events.php'.$this->SID.'&upd=true&simple_head=true&calendar=true', 'width'=>'600', 'height'=>'420', 'beforeclose'=>$beforeclosefunc));

		if (isset($eventdata['extension']) && isset($eventdata['extension']['calendarmode']) && $eventdata['extension']['calendarmode']){
			$calendermode = $eventdata['extension']['calendarmode'];
		} elseif($this->url_id > 0){
			$calendermode = 'event';
		} else {
			$calendermode = ($this->config->get('calendar_addevent_mode')) ? $this->config->get('calendar_addevent_mode') : 'event';
			if($this->config->get('disable_guild_features')) $calendermode = 'event';
		}

		// build json for calendar dropdown
		$calendar_data		= $this->pdh->get('calendars', 'data', array());
		$user_calendars		= $this->pdh->get('calendars', 'calendarids4userid', array($this->user->data['user_id']));
		$calendars			= array();
		if(is_array($calendar_data)){
			foreach($calendar_data as $calendar_id=>$calendar_value){
				if($calendar_value['restricted'] == '1' && !$this->user->check_auth('a_cal_addrestricted', false)){ continue; }
				if(!$this->values_available && !in_array($calendar_id, $user_calendars)){ continue; }
				if($calendar_value['type'] != '3'){
					$calendars[$calendar_id] = array(
						'id'		=> $calendar_id,
						'name'		=> $calendar_value['name'],
						'type'		=> $calendar_value['type']
					);
				}
			}
		}

		// the hack for the custom repeating period
		$dr_repeat_custom = 1;
		if(isset($eventdata['repeating']) && $eventdata['repeating'] > 0 && !in_array((int)$eventdata['repeating'], array(1,7,14))){
			$dr_repeat_custom		= $eventdata['repeating'];
			$eventdata['repeating']	= 'custom';
		}elseif($this->url_id > 0 && isset($eventdata['repeating']) && $eventdata['repeating'] == 0){
			$dr_repeat_custom = 0;
		}

		// build select-calendarmode dropdown
		foreach($calendar_mode_array as $cm_type => $cm_name){
			$this->tpl->assign_block_vars('calendar_modes', array(
				'IS_SELECTED'	=> ($calendermode == $cm_type)? true : false,
				'VALUE'			=> $cm_type,
				'TEXT'			=> $cm_name,
			));
		}

		$txt_repeating 		= '';
		$is_cloned_event	= (isset($eventdata['repeating']) && ($eventdata['repeating'] > 0 || ($eventdata['repeating'] == 'custom' && $dr_repeat_custom > 0))) ? true : false;
		if(isset($eventdata['repeating']) && ($eventdata['repeating'] == 'custom'  && $dr_repeat_custom > 0)){
			$txt_repeating = sprintf($this->user->lang('raidevent_repeat_raid_days'), $eventdata['repeating']);
		}elseif($eventdata['repeating'] > 0){
			$txt_repeating = $this->user->lang('calendar_log_repeat_'.$eventdata['repeating']);
		}
		$parent_event_name = $parent_event_url = '';
		if($is_cloned_event){
			//$eventdata['cloneid']
			$parent_event_name	= $this->pdh->get('calendar_events', 'name', array($eventdata['cloneid']));
			$parent_event_url	= $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventdata['cloneid'])), $eventdata['cloneid'], true, true);
		}
		$this->tpl->assign_vars(array(
			'IS_EDIT'			=> ($this->url_id > 0) ? true : false,
			'IS_CLONED'			=> $is_cloned_event,
			'DKP_ENABLED'		=> ($this->config->get('disable_points') == 0) ? true :false,
			'DR_CALENDAR_JSON'	=> json_encode($calendars),
			'DR_CALENDAR_CID'	=> (isset($eventdata['calendar_id'])) ? $eventdata['calendar_id'] : 0,
			'DR_REPEAT'			=> (new hdropdown('repeat_dd', array('options' => $drpdwn_repeat, 'value' => ((isset($eventdata['repeating']) && (($eventdata['repeating'] == 'custom'  && $dr_repeat_custom > 0) || $eventdata['repeating'] > 0)) ? $eventdata['repeating'] : '0'))))->output(),
			'REPEAT_CUSTOM'		=> $dr_repeat_custom,
			'DR_TEMPLATE'		=> (new hdropdown('raidtemplate', array('options' => $this->pdh->get('calendar_raids_templates', 'dropdowndata'), 'id' => 'cal_raidtemplate')))->output(),
			'DR_EVENT'			=> (new hdropdown('raid_eventid', array('options' => $this->pdh->aget('event', 'name', 0, array($this->pdh->sort($this->pdh->get('event', 'id_list'), 'event', 'name'))), 'value' => ((isset($eventdata['extension']['raid_eventid'])) ? $eventdata['extension']['raid_eventid'] : ''), 'id' => 'input_eventid', 'class' => 'resettemplate_input')))->output(),
			'DR_RAIDMODE'		=> (new hdropdown('raidmode', array('options' => $raidmode_array, 'value' => ((isset($eventdata['extension']) && isset($eventdata['extension']['raidmode'])) ? $eventdata['extension']['raidmode'] : ''), 'id' => 'cal_raidmodeselect')))->output(),
			'DR_RAIDLEADER'		=> (new hmultiselect('raidleader', array('options' => $raidleader_array, 'value' => ((isset($eventdata['extension']) && isset($eventdata['extension']['raidleader'])) ? $eventdata['extension']['raidleader'] : $this->pdh->get('member', 'mainchar', array($this->user->data['user_id']))), 'width' => 300, 'filter' => true)))->output(),
			'DR_GROUPS'			=> (new hmultiselect('asi_group', array('options' => $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list'))), 'value' => $this->config->get('calendar_raid_add_raidgroupchars'))))->output(),
			'DR_SHARE_USERS'	=> (new hmultiselect('invited', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'filter' => true, 'clickfunc' => 'var arrInvitedUG = $("#invited").multiselect("getChecked").map(function(){ return this.value; }).get(); if(arrInvitedUG.length > 0){ $("#invited_usergroup").multiselect("disable"); }else{ $("#invited_usergroup").multiselect("enable"); }' , 'value' => ((isset($eventdata['extension']['invited']) && $eventdata['extension']['invited']) ? $eventdata['extension']['invited'] : array()))))->output(),
			'DR_INVITED_UG'		=> (new hmultiselect('invited_usergroup', array('options' => $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list'))), 'clickfunc' => 'var arrInvitedUG = $("#invited_usergroup").multiselect("getChecked").map(function(){ return this.value; }).get(); if(arrInvitedUG.length > 0){ $("#invited").multiselect("disable"); }else{ $("#invited").multiselect("enable"); }' , 'value' => ((isset($eventdata['extension']['invited_usergroup']) && $eventdata['extension']['invited_usergroup']) ? $eventdata['extension']['invited_usergroup'] : array()))))->output(),
			'DR_INVITED_RG'		=> (new hmultiselect('invited_raidgroup', array('options' => $this->pdh->aget('raid_groups', 'name', 0, array($this->pdh->get('raid_groups', 'id_list'))), 'value' => ((isset($eventdata['extension']['invited_raidgroup']) && $eventdata['extension']['invited_raidgroup']) ? $eventdata['extension']['invited_raidgroup'] : array()))))->output(),
			'DR_STATUS'			=> (new hdropdown('asi_status', array('options' => $raidstatus, 'value' => 0)))->output(),
			'CB_ALLDAY'			=> (new hcheckbox('allday', array('options' => array(1=>''), 'value' => ((isset($eventdata['allday'])) ? $eventdata['allday'] : 0), 'class' => 'allday_cb', 'inputid' => 'cb_allday')))->output(),
			'CB_PRIVATE'		=> (new hcheckbox('private', array('id' => 'label_private', 'options' => array(1=>''), 'value' => ((isset($eventdata['private'])) ? $eventdata['private'] : 0))))->output(),
			'RADIO_EDITCLONES'	=> (new hradio('edit_clones', array('options' => $radio_repeat_array, 'orientation' => 'horizontal')))->output(),
			'BBCODE_NOTE'		=> (new hbbcodeeditor('note', array('rows' => 3, 'value' => ((isset($eventdata['notes'])) ? $eventdata['notes'] : ''), 'id' => 'input_note')))->output(),
			'LP_LOCATION'		=> (new hplacepicker('location', array('value' => ((isset($eventdata['extension']) && isset($eventdata['extension']['location'])) ? $eventdata['extension']['location'] : ''))))->output(),
			'DR_ICONSELECT'		=> (new hiconselect('calevent_icon', array('value' => ((isset($eventdata['extension']['calevent_icon']) && $eventdata['extension']['calevent_icon']) ? $eventdata['extension']['calevent_icon'] : array())	)))->output(),

			'JQ_DATE_START'		=> (new hdatepicker('startdate', array('value' => $this->time->user_date($defdates['start'], true, false, false), 'timepicker' => true, 'onselect' => $startdate_onselect, 'id' => 'cal_startdate')))->output(),
			'JQ_DATE_END'		=> (new hdatepicker('enddate', array('value' => $this->time->user_date($defdates['end'], true, false, false), 'timepicker' => true, 'id' => 'cal_enddate')))->output(),
			'DATE_DEADLINE'		=> ($defdates['deadline'] > 0) ? $defdates['deadline'] : 2,

			// data
			'EVENT_ID'			=> $this->url_id,
			'CALENDARMODE'		=> $calendermode,
			'LOCATION'			=> (isset($eventdata['extension']) && isset($eventdata['extension']['location'])) ? $eventdata['extension']['location'] : '',
			'NOTE'				=> (isset($eventdata['notes'])) ? $eventdata['notes'] : '',
			'EVENTNAME'			=> (isset($eventdata['name'])) ? $eventdata['name'] : '',
			'RAID_VALUE'		=> (isset($eventdata['extension']) && isset($eventdata['extension']['raid_value'])) ? $eventdata['extension']['raid_value'] : '',
			'ATTENDEE_COUNT'	=> (isset($eventdata['extension']) && isset($eventdata['extension']['attendee_count'])) ? $eventdata['extension']['attendee_count'] : 0,
			'RAIDMODE_NONE'		=> (isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'none') ? true : false,

			// lang
			'CLONED_REPEAT'		=> sprintf($this->user->lang('raidevent_repeat_raid_text'), $txt_repeating),
			'CLONED_INFO'		=> sprintf($this->user->lang('calendar_event_clones_info'), $parent_event_name, $parent_event_url),

			'CSRF_DELETETEMPLATE' => $this->CSRFGetToken('deletetemplate'),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('calendars_add_title'),
			'template_file'		=> 'calendar/addevent.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true
		));
	}
}
