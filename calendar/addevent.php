<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

class addevent extends page_generic {
	public static $shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config', 'html', 'time', 'env', 'logs'=> 'logs', 'timekeeper'=> 'timekeeper', 'email'=>'MyMailer');

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
		$output = $this->html->DropDown('raid_eventid', $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list'))), $this->in->get('raidevent_id', 0), '', '', 'input resettemplate_input', 'input_eventid');
		echo($output);
		exit;
	}

	// send the email of the created raid to all users
	private function email_newraid($raidid){
		if($this->config->get('calendar_email_newraid') == 1){
			// fetch the static data of the raid
			$raidname		= $this->pdh->get('event', 'name', array($this->in->get('raid_eventid', 0)));
			$raidnotes		= $this->pdh->get('event', 'notes', array($this->in->get('raid_eventid', 0)));
			$raiddate		= $this->time->user_date($this->time->fromformat($this->in->get('startdate'), 1), true);
			$mailsubject	= sprintf($this->user->lang('raidevent_mail_subject_newraid'), $raidname, $raiddate);
			$bodyvars = array(
				'RAID_NAME'		=> $raidname,
				'RAIDLEADER'	=> ($this->in->getArray('raidleader', 'int') > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($this->in->getArray('raidleader', 'int')))) : '',
				'DATE'			=> $raiddate,
				'NOTE'			=> ($raidnotes) ? nl2br($raidnotes) : '',
				'RAID_LINK'		=> $this->env->link.'calendar/viewcalraid.php?eventid='.$raidid,
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
			foreach($this->game->get('classes', 'id_0') as $classid=>$classname){
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
				foreach($this->game->get('classes', 'id_0') as $classid=>$classname){
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
			$this->pdh->put('calendar_events', 'auto_addchars', array($this->in->get('raidmode'), $raidid));
			$this->email_newraid($raidid);
		}else{
			$this->pdh->put('calendar_events', 'add_cevent', array(
				$this->in->get('calendar_id'),
				$this->in->get('eventname'),
				$this->user->data['user_id'],
				$this->time->fromformat($this->in->get('startdate'), 1),
				$this->time->fromformat($this->in->get('enddate'), 1),
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
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	// update the event in the database
	public function update(){
		if($this->in->get('calendarmode') == 'raid'){
			// fetch the count
			if($this->in->get('raidmode') == 'role'){
				foreach($this->pdh->get('roles', 'roles', array()) as $classid=>$classname){
					$raid_clsdistri[$classid] = $this->in->get('roles_'.$classid.'_count', 0);
				}
			}else{
				foreach($this->game->get('classes', 'id_0') as $classid=>$classname){
					$raid_clsdistri[$classid] = $this->in->get('classes_'.$classid.'_count', 0);
				}
			}

			$this->pdh->put('calendar_events', 'update_cevents', array(
				$this->url_id,
				$this->in->get('calendar_id', 1),
				'',
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
		if($this->in->get('repeating') != 'none'){
			$this->timekeeper->run_cron('calevents_repeatable', true);
		}
		$this->pdh->process_hook_queue();

		// close the dialog
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	// the main page display
	public function display() {
		// add custom css

		$eventdata	= $this->pdh->get('calendar_events', 'data', array($this->url_id));

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

		// The roles Fields
		foreach($this->pdh->get('roles', 'roles', array()) as $row){
			$this->tpl->assign_block_vars('raid_roles', array(
				'LABEL'			=> $row['name'],
				'NAME'			=> "roles_" . $row['id'] . "_count",
				'CLSSID'		=> $row['id'],
				'COUNT'			=> (isset($eventdata['extension']) && $eventdata['extension']['distribution'][$row['id']]) ? $eventdata['extension']['distribution'][$row['id']] : '0',
				'ICON'			=> $this->game->decorate('roles', array($row['id'])),
				'DISABLED'		=> (isset($eventdata['extension']) && $eventdata['extension']['raidmode'] == 'class') ? 'disabled="disabled"' : ''
			));
		}

		// The class fields
		foreach($this->game->get('classes', 'id_0') as $classid=>$classname){
			$this->tpl->assign_block_vars('raid_classes', array(
				'LABEL'			=> $classname,
				'NAME'			=> "classes_" . $classid . "_count",
				'CLSSID'		=> $classid,
				'COUNT'			=> (isset($eventdata['extension']['distribution'][$classid]) && $eventdata['extension']['distribution'][$classid]) ? $eventdata['extension']['distribution'][$classid] : '0',
				'ICON'			=> $this->game->decorate('classes', array($classid)),
				'DISABLED'		=> (isset($eventdata['extension']) && $eventdata['extension']['raidmode'] == 'role') ? 'disabled="disabled"' : ''
			));
		}

		// Raidleaders
		$raidleader_array = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
		asort($raidleader_array);

		// Load the default dates
		if($this->url_id > 0){
			$defdates = array(
				'start'		=> $eventdata['timestamp_start'],
				'end'		=> $eventdata['timestamp_end'],
				'deadline'	=> $eventdata['extension']['deadlinedate']
			);
		}else{
			$default_deadlineoffset	= (($this->config->get('calendar_addraid_deadline')) ? $this->config->get('calendar_addraid_deadline') : 1);
			$default_raidduration	= ((($this->config->get('calendar_addraid_duration')) ? $this->config->get('calendar_addraid_duration') : 120)*60);

			// if the default time should be used, set it...
			if($this->config->get('calendar_addraid_use_def_start') && preg_match('#[:]#', $this->config->get('calendar_addraid_def_starttime'))){
				$a_times				= explode(':', $this->config->get('calendar_addraid_def_starttime'));
				$starttimestamp			= ($this->in->get('timestamp', 0) > 0) ? ($this->in->get('timestamp', 0) + ($a_times[0]*3600) + ($a_times[1]*60)) : $this->time->fromformat($this->config->get('calendar_addraid_def_starttime'), 'H:i');
			}else{
				$starttimestamp			= ($this->in->get('timestamp', 0) > 0) ? ($this->in->get('timestamp', 0) + ($this->time->date('H')*3600) + ($this->time->date('i')*60)) : $this->time->time;
			}

			$defdates = array(
				'start'		=> $starttimestamp,
				'end'		=> $starttimestamp+$default_raidduration,
				'deadline'	=> $default_deadlineoffset,
			);
		}

		$beforeclosefunc = "
			$.post('addevent.php".$this->SID."&ajax_dropdown=true', { raidevent_id: $('body').data('raidevent_id') }, function(data) {
				$('#raidevent_dropdown').html(data);
			});";

		// Set the code for the second timepicker difference
		$startdate_onselect = "var startDate = $(this).datetimepicker('getDate');
				var endDate = new Date(startDate.getTime() + (".(($this->config->get('calendar_addraid_duration') > 0) ? $this->config->get('calendar_addraid_duration') : 120)."*60000))
				var newDate	= new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), endDate.getHours(), endDate.getMinutes());
				$('#cal_enddate').datetimepicker('setDate', newDate);";

		$this->jquery->spinner('deadlinedate');
		$this->jquery->Dialog('AddEventDialog', $this->user->lang('raidevent_raidevent_add'), array('url'=>$this->root_path.'admin/manage_events.php'.$this->SID.'&upd=true&simple_head=true&calendar=true', 'width'=>'600', 'height'=>'420', 'beforeclose'=>$beforeclosefunc));

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
			'DR_CALENDAR_JSON'	=> json_encode($calendars),
			'DR_CALENDAR_CID'	=> (isset($eventdata['calendar_id'])) ? $eventdata['calendar_id'] : 0,
			'DR_REPEAT'			=> $this->html->DropDown('repeating', $drpdwn_repeat, ((isset($eventdata['repeating'])) ? $eventdata['repeating'] : '')),
			'DR_TEMPLATE'		=> $this->html->DropDown('raidtemplate', $this->pdh->get('calendar_raids_templates', 'dropdowndata'), '', '', '', 'input', 'cal_raidtemplate'),
			'DR_CALENDARMODE'	=> $this->html->DropDown('calendarmode', $calendar_mode_array, $calendermode, '', '', 'input', 'selectmode'),
			'DR_EVENT'			=> $this->html->DropDown('raid_eventid', $this->pdh->aget('event', 'name', 0, array($this->pdh->sort($this->pdh->get('event', 'id_list'), 'event', 'name'))), ((isset($eventdata['extension'])) ? $eventdata['extension']['raid_eventid'] : ''), '', '', 'input resettemplate_input', 'input_eventid'),
			'DR_RAIDMODE'		=> $this->html->DropDown('raidmode', $raidmode_array, ((isset($eventdata['extension'])) ? $eventdata['extension']['raidmode'] : ''), '', '', 'input', 'cal_raidmodeselect'),
			'DR_RAIDLEADER'		=> $this->jquery->MultiSelect('raidleader', $raidleader_array, ((isset($eventdata['extension'])) ? $eventdata['extension']['raidleader'] : ''), array('width' => 300, 'filter' => true)),
			'CB_ALLDAY'			=> $this->html->widget(array('fieldtype'=>'checkbox','name'=>'allday','selected'=>((isset($eventdata['allday'])) ? $eventdata['allday'] : 0), 'class'=>'allday_cb')),
			'RADIO_EDITCLONES'	=> $this->html->widget(array('fieldtype'=>'radio','name'=>'edit_clones','options'=> array('0'=>$this->user->lang('calendar_event_editone'), '1'=>$this->user->lang('calendar_event_editall')))),

			'JQ_DATE_START'		=> $this->jquery->Calendar('startdate', $this->time->user_date($defdates['start'], true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true, 'onselect' => $startdate_onselect)),
			'JQ_DATE_END'		=> $this->jquery->Calendar('enddate',$this->time->user_date($defdates['end'], true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'DATE_DEADLINE'		=> ($defdates['deadline'] > 0) ? $defdates['deadline'] : 2,

			// data
			'EVENT_ID'			=> $this->url_id,
			'NOTE'				=> (isset($eventdata['notes'])) ? $eventdata['notes'] : '',
			'EVENTNAME'			=> (isset($eventdata['name'])) ? $eventdata['name'] : '',
			'RAID_VALUE'		=> (isset($eventdata['extension'])) ? $eventdata['extension']['raid_value'] : '',
			'ATTENDEE_COUNT'	=> (isset($eventdata['extension'])) ? $eventdata['extension']['attendee_count'] : 0,
			'RAIDMODE_NONE'		=> (isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'none') ? true : false
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('calendars_add_title'),
			'template_file'		=> 'calendar/addevent.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_addevent', addevent::$shortcuts);
registry::register('addevent');
?>