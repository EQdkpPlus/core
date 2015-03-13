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
		$this->user->check_auth('u_cal_event_add');
		parent::__construct(false, $handler, array(), null, '', 'eventid');
		$this->process();
	}

	// fetch an event template as JSON data
	public function process_loadtemplate(){
		$jsondata = $this->pdh->get('calendar_raids_templates', 'templates', array($this->in->get('loadtemplate', 0)));
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

	// this function generates a dropdown with the events
	public function ajax_dropdown(){
		$output = new hdropdown('raid_eventid', array('options' => $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list'))), 'value' => $this->in->get('raidevent_id', 0), 'id' => 'input_eventid', 'class' => 'resettemplate_input'));
		echo($output);
		exit;
	}

	// send the email of the created raid to all users
	private function email_newraid($raidid){
		if($this->config->get('calendar_email_newraid') == 1){
			// fetch the static data of the raid
			$raidname		= $this->pdh->get('event', 'name', array($this->in->get('raid_eventid', 0)));
			$raidnotes		= $this->pdh->get('calendar_events', 'notes', array($raidid));
			$raiddate		= $this->time->user_date($this->time->fromformat($this->in->get('startdate'), 1), true);
			$mailsubject	= sprintf($this->user->lang('raidevent_mail_subject_newraid'), $raidname, $raiddate);
			$bodyvars = array(
				'RAID_NAME'		=> $raidname,
				'RAIDLEADER'	=> ($this->in->getArray('raidleader', 'int') > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($this->in->getArray('raidleader', 'int')))) : '',
				'RAID_DATE'		=> $raiddate,
				'RAID_NOTE'		=> ($raidnotes) ? nl2br($raidnotes) : '',
				'RAID_LINK'		=> $this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($raidid)), $raidid, false, true),
			);

			// send the email to all attendees
			$a_users = $this->pdh->get('user', 'active_users');
			if(is_array($a_users) && count($a_users) > 0){
				foreach($a_users as $userid){
					$emailadress	= $this->pdh->get('user', 'email', array($userid, true));
					if($emailadress && strlen($emailadress)){
						$bodyvars['USERNAME'] = $this->pdh->get('user', 'name', array($userid));
						$this->email->SendMailFromAdmin($emailadress, $mailsubject, 'calendar_viewcalraid_new.html', $bodyvars, $this->config->get('lib_email_method'));
					}
				}
			}
		}
	}

	// save an event template in database
	public function process_addtemplate(){
		if($this->in->get('raidmode') == 'role'){
			foreach($this->pdh->get('roles', 'roles', array()) as $classid=>$classname){
				$raid_clsdistri[$classid] = $this->in->get('roles_'.$classid.'_count', 0);
			}
		}else{
			$classdata = $this->game->get_primary_classes(array('id_0'));
			foreach($classdata as $classid=>$classname){
				$raid_clsdistri[$classid] = $this->in->get('classes_'.$classid.'_count', 0);
			}
		}
		$this->pdh->put('calendar_raids_templates', 'save_template', array(
			(($this->in->get('templatename')) ? $this->in->get('templatename') : 'template-'.rand(0,1000)),
			array(
				'input_eventid'		=> $this->in->get('raid_eventid', 0),
				'input_dkpvalue'	=> $this->in->get('raid_value'),
				'input_note'		=> $this->in->get('note'),
				'selectmode'		=> 'raid',
				'cal_raidmodeselect'=> $this->in->get('raidmode'),
				'dw_raidleader'		=> $this->in->getArray('raidleader', 'int'),
				'deadlinedate'		=> $this->in->get('deadlinedate', 0),
				'distribution'		=> $raid_clsdistri,
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
			foreach($seldata as $ddid=>$ddval){
				$out .= "<option value='".$ddid."'>".$ddval."</option>";
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
					$raid_clsdistri[$classid] = $this->in->get('roles_'.$classid.'_count', 0);
				}
			}else{
				$classdata = $this->game->get_primary_classes(array('id_0'));
				foreach($classdata as $classid=>$classname){
					$raid_clsdistri[$classid] = $this->in->get('classes_'.$classid.'_count', 0);
				}
			}

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
				)
			));

			// if the raid had been added, do the rest...
			if($raidid > 0){
				// Auto confirm / confirm by raid-add-setting
				$asi_groups	= $this->in->getArray('asi_group');
				$asi_status	= (is_array($asi_groups) && count($asi_groups) > 0) ? $this->in->get('asi_status') : false;
			
				$this->pdh->put('calendar_events', 'auto_addchars', array($this->in->get('raidmode'), $raidid, $this->in->getArray('raidleader', 'int'), $asi_groups, $asi_status));
				$this->email_newraid($raidid);
			}
		}else{
			$withtime = ($this->in->get('allday') == '1') ? 0 : 1;
			$this->pdh->put('calendar_events', 'add_cevent', array(
				$this->in->get('calendar_id'),
				$this->in->get('eventname'),
				$this->user->data['user_id'],
				$this->time->fromformat($this->in->get('startdate'), $withtime),
				$this->time->fromformat($this->in->get('enddate'), $withtime),
				$this->in->get('repeating'),
				$this->in->get('note'),
				$this->in->get('allday'),
			));
		}
		if($this->in->get('repeating') != 'none'){
			$this->timekeeper->run_cron('calevents_repeatable', true);
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
				)
			));
		}else{
			$this->pdh->put('calendar_events', 'update_cevents', array(
				$this->url_id,
				$this->in->get('calendar_id'),
				$this->in->get('eventname'),
				$this->time->fromformat($this->in->get('startdate'), 1),
				$this->time->fromformat($this->in->get('enddate'), 1),
				$this->in->get('repeating'),
				$this->in->get('edit_clones', 0),
				$this->in->get('note'),
				$this->in->get('allday'),
			));
		}
		
		//Flush Cache so the Cronjob can access the new data
		$this->pdh->process_hook_queue();
		
		if($this->in->get('repeating') != 'none'){
			//Process Queue, so the Cronjob has reliable data
			$this->pdh->process_hook_queue();
			$this->timekeeper->run_cron('calevents_repeatable', true, true);
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
			'none'		=> '--',
			'day'		=> $this->user->lang('calendar_repeat_daily'),
			'week'		=> $this->user->lang('calendar_repeat_weekly'),
			'twoweeks'	=> $this->user->lang('calendar_repeat_2weeks'),
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
				'COUNT'			=> (isset($eventdata['extension']) && $eventdata['extension']['distribution'][$row['id']]) ? $eventdata['extension']['distribution'][$row['id']] : '0',
				'ICON'			=> $this->game->decorate('roles', $row['id']),
				'DISABLED'		=> (isset($eventdata['extension']) && $eventdata['extension']['raidmode'] == 'class') ? 'disabled="disabled"' : ''
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
				'DISABLED'		=> (isset($eventdata['extension']) && $eventdata['extension']['raidmode'] == 'role') ? 'disabled="disabled"' : ''
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
			if($this->config->get('calendar_addraid_use_def_start') && preg_match('#[:]#', $this->config->get('calendar_addraid_def_starttime'))){
				$starttimestamp			= ($this->in->get('timestamp', 0) > 0) ? $this->time->newtime($this->in->get('timestamp', 0), $this->config->get('calendar_addraid_def_starttime')) : $this->time->fromformat($this->config->get('calendar_addraid_def_starttime'), $this->user->style['time']);
			}else{
				$starttimestamp			= ($this->in->get('timestamp', 0) > 0) ? $this->time->newtime($this->in->get('timestamp', 0)) : $this->time->time;
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

		if (isset($eventdata['extension']) && $eventdata['extension']['calendarmode']){
			$calendermode = $eventdata['extension']['calendarmode'];
		} elseif($this->url_id > 0){
			$calendermode = 'event';
		} else {
			$calendermode = ($this->config->get('calendar_addevent_mode')) ? $this->config->get('calendar_addevent_mode') : 'event';
		}

		// build json for calendar dropdown
		$calendar_data	= $this->pdh->get('calendars', 'data', array());
		$calendars		= array();
		if(is_array($calendar_data)){
			foreach($calendar_data as $calendar_id=>$calendar_value){
				if($calendar_value['restricted'] == '1' && !$this->user->check_auth('a_cal_addrestricted', false)){ continue; }
				if($calendar_value['type'] != '3'){
					$calendars[$calendar_id] = array(
						'id'		=> $calendar_id,
						'name'		=> $calendar_value['name'],
						'type'		=> $calendar_value['type']
					);
				}
			}
		}

		$this->tpl->assign_vars(array(
			'IS_EDIT'			=> ($this->url_id > 0) ? true : false,
			'IS_CLONED'			=> ((isset($eventdata['repeating']) && $eventdata['repeating'] != 'none') ? true : false),
			'DKP_ENABLED'		=> ($this->config->get('disable_points') == 0) ? true :false,
			#'IS_OPERATOR'		=> $this->user->check_auth('a_cal_revent_conf', false),
			'DR_CALENDAR_JSON'	=> json_encode($calendars),
			'DR_CALENDAR_CID'	=> (isset($eventdata['calendar_id'])) ? $eventdata['calendar_id'] : 0,
			'DR_REPEAT'			=> new hdropdown('repeating', array('options' => $drpdwn_repeat, 'value' => ((isset($eventdata['repeating'])) ? $eventdata['repeating'] : ''))),
			'DR_TEMPLATE'		=> new hdropdown('raidtemplate', array('options' => $this->pdh->get('calendar_raids_templates', 'dropdowndata'), 'id' => 'cal_raidtemplate')),
			'DR_CALENDARMODE'	=> new hdropdown('calendarmode', array('options' => $calendar_mode_array, 'value' => $calendermode, 'id' => 'selectmode', 'class' => 'dropdown')),
			'DR_EVENT'			=> new hdropdown('raid_eventid', array('options' => $this->pdh->aget('event', 'name', 0, array($this->pdh->sort($this->pdh->get('event', 'id_list'), 'event', 'name'))), 'value' => ((isset($eventdata['extension'])) ? $eventdata['extension']['raid_eventid'] : ''), 'id' => 'input_eventid', 'class' => 'resettemplate_input')),
			'DR_RAIDMODE'		=> new hdropdown('raidmode', array('options' => $raidmode_array, 'value' => ((isset($eventdata['extension'])) ? $eventdata['extension']['raidmode'] : ''), 'id' => 'cal_raidmodeselect')),
			'DR_RAIDLEADER'		=> $this->jquery->MultiSelect('raidleader', $raidleader_array, ((isset($eventdata['extension'])) ? $eventdata['extension']['raidleader'] : $this->pdh->get('member', 'mainchar', array($this->user->data['user_id']))), array('width' => 300, 'filter' => true)),
			'DR_GROUPS'			=> new hmultiselect('asi_group', array('options' => $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list'))), 'value' => $this->config->get('calendar_raid_autocaddchars'))),
			'DR_STATUS'			=> new hdropdown('asi_status', array('options' => $raidstatus, 'value' => 0)),
			'CB_ALLDAY'			=> new hcheckbox('allday', array('options' => array(1=>''), 'value' => ((isset($eventdata['allday'])) ? $eventdata['allday'] : 0), 'class' => 'allday_cb')),
			'RADIO_EDITCLONES'	=> new hradio('edit_clones', array('options' => $radio_repeat_array)),

			'JQ_DATE_START'		=> $this->jquery->Calendar('startdate', $this->time->user_date($defdates['start'], true, false, false), '', array('timepicker' => true, 'onselect' => $startdate_onselect)),
			'JQ_DATE_END'		=> $this->jquery->Calendar('enddate',$this->time->user_date($defdates['end'], true, false, false), '', array('timepicker' => true)),
			'DATE_DEADLINE'		=> ($defdates['deadline'] > 0) ? $defdates['deadline'] : 2,

			// data
			'EVENT_ID'			=> $this->url_id,
			'NOTE'				=> (isset($eventdata['notes'])) ? $eventdata['notes'] : '',
			'EVENTNAME'			=> (isset($eventdata['name'])) ? $eventdata['name'] : '',
			'RAID_VALUE'		=> (isset($eventdata['extension'])) ? $eventdata['extension']['raid_value'] : '',
			'ATTENDEE_COUNT'	=> (isset($eventdata['extension'])) ? $eventdata['extension']['attendee_count'] : 0,
			'RAIDMODE_NONE'		=> (isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'none') ? true : false,
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
?>