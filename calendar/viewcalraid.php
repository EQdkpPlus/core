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

class viewcalraid extends page_generic {
	public static $shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'env', 'config', 'html', 'time', 'logs'=> 'logs', 'comments'=> 'comments', 'email'=>'MyMailer');

	public function __construct() {
		$handler = array(
			'closedstatus'	=> array(
				array('process' => 'close_raid',	'value' => 'close', 'csrf'=>true),
				array('process' => 'open_raid',		'value' => 'open', 'csrf'=>true),
			),
			'ajax'	=> array(
				array('process' => 'role_ajax',	'value' => 'role'),
			),
			'savenote'			=> array('process' => 'save_raidnote', 'csrf'=>true),
			'update_status'		=> array('process' => 'update_status', 'csrf'=>true),
			'moderate_status'	=> array('process' => 'moderate_status', 'csrf'=>true),
			'confirmall'		=> array('process' => 'confirm_all', 'csrf'=>true),
			'ical'				=> array('process' => 'generate_ical'),
			'add_notsigned'		=> array('process' => 'add_notsigned_chars', 'csrf'=>true),
			'changecharmenu'	=> array('process' => 'change_char', 'csrf'=>true),
			'changenotemenu'	=> array('process' => 'change_note', 'csrf'=>true),
			'guestid'			=> array('process' => 'delete_guest', 'csrf'=>true),
		);
		$this->user->check_auth('u_calendar_view');
		parent::__construct(false, $handler, array(), null, '', 'eventid');		
		$this->process();
	}

	// check calendar specific rights such as if the user is a raidleader or the creator
	private function check_permission($mode='raidleader', $userid=0){
		$userid	= ($userid > 0) ? $userid : $this->user->data['user_id'];

		// seelct the mode
		switch($mode){
			
			// raidleaders
			case 'raidleader':
				$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($this->url_id));
				$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
				$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
				return (in_array($userid, $raidleaders_users)) ? true : false;
			break;

			// raidleaders & creators
			case 'raidleader_creator':
				$creator			= $eventdata = $this->pdh->get('calendar_events', 'creator', array($this->url_id));
				$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($this->url_id));
				$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
				$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
				return ($creator === $userid || in_array($userid, $raidleaders_users))  ? true : false;
			break;

			// creators
			case 'creator':
				$creator	= $eventdata = $this->pdh->get('calendar_events', 'creator', array($this->url_id));
				return ($creator === $userid) ? true : false;
			break;
		}
		return false;
	}

	// the role dropdown, attached to the character selection
	public function role_ajax(){
		$tmp_classID	= $this->pdh->get('member', 'classid', array($this->in->get('requestid')));
		$memberrole		= $this->pdh->get('calendar_raids_attendees', 'role', array($this->url_id, $this->in->get('requestid')));
		$myrole			= ($memberrole > 0) ? $memberrole : $this->pdh->get('member', 'defaultrole', array($this->in->get('requestid')));
		header('content-type: text/html; charset=UTF-8');
		echo $this->jquery->dd_create_ajax($this->pdh->get('roles', 'memberroles', array($tmp_classID)), array('selected'=>$myrole));exit;
	}

	// user changes his status for that raid
	public function update_status(){
		
		// check if the user is already in the database for that event and skip if already existing (avoid reload-cheating)
		if($this->pdh->get('calendar_raids_attendees', 'in_db', array($this->url_id, $this->in->get('member_id', 0)))){

			// the char is in the db, now, check if the status is unchanged
			if($this->pdh->get('calendar_raids_attendees', 'status', array($this->url_id, $this->in->get('member_id', 0))) == $this->in->get('signup_status', 4)){
				// check if the note changed
				if($this->pdh->get('calendar_raids_attendees', 'note', array($this->url_id, $this->in->get('member_id', 0))) != $this->in->get('signupnote')){
					$this->pdh->put('calendar_raids_attendees', 'update_note', array(
						$this->url_id,
						$this->in->get('member_id', 0),
						$this->in->get('signupnote', '')
					));
					$this->pdh->process_hook_queue();
				}

				// check if the role changed
				if($this->pdh->get('calendar_raids_attendees', 'role', array($this->url_id, $this->in->get('member_id', 0))) != $this->in->get('member_role', 0)){
					$this->pdh->put('calendar_raids_attendees', 'update_role', array(
						$this->url_id,
						$this->in->get('member_id', 0),
						$this->in->get('member_role', 0)
					));
					$this->pdh->process_hook_queue();
				}
				return false;
			}
		}

		// auto confirm if enabled
		$usergroups		= unserialize($this->config->get('calendar_raid_autoconfirm'));
		$signupstatus	= $this->in->get('signup_status', 4);
		if(is_array($usergroups) && count($usergroups) > 0 && $signupstatus == 1){
			if($this->user->check_group($usergroups, false)){
				$signupstatus = 0;
			}
		}

		$myrole = ($this->in->get('member_role', 0) > 0) ? $this->in->get('member_role', 0) : $this->pdh->get('member', 'defaultrole', array($this->in->get('member_id', 0)));
		$eventdata = $this->pdh->get('calendar_events', 'data', array($this->url_id));
		if ($eventdata['extension']['raidmode'] == 'role' && (int)$myrole == 0){
			return false;
		}
		
		// Build the Deadline
		$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
		if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
			$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
		}else{
			$deadlinetime	= $this->time->user_date($deadlinedate, true);
		}
		$mystatus = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($this->url_id, $this->user->id));
		$mysignedstatus	= $this->pdh->get('calendar_raids_attendees', 'status', array($this->url_id, $mystatus['member_id']));
		
		if (((int)$eventdata['closed'] == 1) || !($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $mystatus['member_id'] > 0 && $mysignedstatus != 4 && $eventdata['timestamp_end'] > $this->time->time))){
			return false;
		}

		$this->pdh->put('calendar_raids_attendees', 'update_status', array(
			$this->url_id,
			$this->in->get('member_id', 0),
			$myrole,
			$signupstatus,
			$this->in->get('raidgroup', 0),
			$this->in->get('subscribed_member_id', 0),
			$this->in->get('signupnote'),
		));
		$this->pdh->process_hook_queue();
	}

	// moderator/operator changes a status for an already signed in char
	public function moderate_status(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			$this->pdh->put('calendar_raids_attendees', 'moderate_status', array(
				$this->url_id,
				$this->in->get('moderation_raidstatus'),
				$this->in->getArray('modstat_change', 'int')
			));
			$this->pdh->process_hook_queue();

			// send mail to attendees
			$this->email_statuschange($this->in->getArray('modstat_change', 'int'), $this->in->get('moderation_raidstatus'));
		}
	}

	// delete a guest
	public function delete_guest(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			if($this->in->get('guestid', 0) > 0){
				$this->pdh->put('calendar_raids_guests', 'delete_guest', array($this->in->get('guestid', 0)));
			}
			$this->pdh->process_hook_queue();
		}
	}

	// moderator/operator add an unsigned char to the raid
	public function add_notsigned_chars(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			$this->pdh->put('calendar_raids_attendees', 'add_notsigned', array(
				$this->url_id,
				$this->in->getArray('memberid', 'int'),
				$this->in->get('notsigned_raidstatus', 1),
				$this->in->getArray('memrole', 'int')
			));
			$this->pdh->process_hook_queue();

			// send mail to attendees
			$this->email_statuschange($this->in->getArray('memberid', 'int'), $this->in->get('notsigned_raidstatus'));
		}
	}

	// moderator/operator changes a char for an already signed in char
	public function change_char(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			$this->pdh->put('calendar_raids_attendees', 'update_status', array(
				$this->url_id,
				$this->in->get('charchange_char', 0),
				$this->in->get('charchange_role', 0),
				$this->in->get('charchange_status', 0),
				$this->in->get('raidgroup', 0),
				$this->in->get('subscribed_member_id', 0),
				''
			));
			$this->pdh->process_hook_queue();
		}
	}

	// moderator/operator changes the note for an already signed in char
	public function change_note(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			$this->pdh->put('calendar_raids_attendees', 'update_note', array(
				$this->url_id,
				$this->in->get('subscribed_member_id', 0),
				$this->in->get('notechange_note', ''),
			));
			$this->pdh->process_hook_queue();
		}
	}

	// close an open raid
	public function close_raid(){
		if($this->user->check_auth('u_cal_event_add', false) || $this->check_permission('raidleader_creator')){
			$this->pdh->put('calendar_events', 'change_closeraidstatus', array($this->url_id));
			$this->pdh->process_hook_queue();
		}
		// send the email to the attendees
		$this->email_openclose('closed');
	}

	// open a closed raid
	public function open_raid(){
		if($this->user->check_auth('u_cal_event_add', false) || $this->check_permission('raidleader_creator')){
			$this->pdh->put('calendar_events', 'change_closeraidstatus', array($this->url_id, false));
			$this->pdh->process_hook_queue();
		}
		// send the email to the attendees
		$this->email_openclose('open');
	}

	// EMAIL function: status change of an attendee
	private function email_statuschange($a_attendees, $status=0){
		if($this->config->get('calendar_email_statuschange') == 1){
			// fetch the static data of the raid
			$eventextension	= $this->pdh->get('calendar_events', 'extension', array($this->url_id));
			$raidname		= $this->pdh->get('calendar_events', 'name', array($this->url_id));
			$raiddate		= $this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($this->url_id)));
			$mailsubject	= sprintf($this->user->lang('raidevent_mail_subject_schange'), $raidname, $raiddate);
			$bodyvars = array(
				'RAID_NAME'		=> $raidname,
				'STATUS'		=> $this->user->lang(array('raidevent_raid_status', $status)),
				'RAIDLEADER'	=> ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '',
				'DATE'			=> $raiddate,
				'RAID_LINK'		=> $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id,
			);

			// send the email to all attendees
			if(is_array($a_attendees) && count($a_attendees) > 0){
				foreach($a_attendees as $attendeeid){
					$attuserid		= $this->pdh->get('member', 'userid', array($attendeeid));
					$emailadress	= $this->pdh->get('user', 'email', array($attuserid, true));

					if($emailadress && strlen($emailadress)){
						$bodyvars['USERNAME'] = $this->pdh->get('user', 'name', array($attuserid));
						$this->email->SendMailFromAdmin($emailadress, $mailsubject, 'calendar_viewcalraid_statuschange.html', $bodyvars, $this->config->get('lib_email_method'));
					}
				}
			}
		}
	}

	// EMAIL function: open & close of an event
	private function email_openclose($status='closed'){
		if($this->config->get('calendar_email_openclose') == 1){
			// fetch the static data of the raid
			$eventextension	= $this->pdh->get('calendar_events', 'extension', array($this->url_id));
			$mailsubject	= ($status == 'open') ? sprintf($this->user->lang('raidevent_mail_subject_open'), $this->config->get('guildtag')) : sprintf($this->user->lang('raidevent_mail_subject_close'), $this->config->get('guildtag'));
			$bodyvars = array(
				'RAID_NAME'		=> $this->pdh->get('calendar_events', 'name', array($this->url_id)),
				'CLOSEDOPEN'	=> ($status == 'open') ? $this->user->lang('raidevent_mail_opened') : $this->user->lang('raidevent_mail_closed'),
				'RAIDLEADER'	=> ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '',
				'DATE'			=> $this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($this->url_id))),
				'RAID_LINK'		=> $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id,
			);

			// send the email to all attendees
			$attendees = $this->pdh->get('calendar_raids_attendees', 'attendee_users', array($this->url_id));
			foreach($attendees as $attuserid){
				$emailadress = $this->pdh->get('user', 'email', array($attuserid, true));
				if($emailadress && strlen($emailadress)){
					$bodyvars['USERNAME'] = $this->pdh->get('user', 'name', array($attuserid));
					$this->email->SendMailFromAdmin($emailadress, $mailsubject, 'calendar_viewcalraid_openclose.html', $bodyvars, $this->config->get('lib_email_method'));
				}
			}
		}
	}

	public function confirm_all(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')){
			$this->pdh->put('calendar_raids_attendees', 'confirm_all', array($this->url_id));
			$this->pdh->process_hook_queue();
		}
	}

	// generate an ical file for that raid
	public function generate_ical(){
		$eventdata	= $this->pdh->get('calendar_events', 'data', array($this->url_id));
		require($this->root_path.'libraries/icalcreator/iCalcreator.class.php');
		$v = new vcalendar;
		$v->setConfig('unique_id', $this->config->get('server_name'));
		$v->setProperty('x-wr-calname',	sprintf(registry::fetch('user')->lang('icalfeed_name'), registry::register('config')->get('guildtag')));
		$v->setProperty('X-WR-CALDESC',	registry::fetch('user')->lang('icalfeed_description'));
		// set the timezone - required by some clients
		$timezone 	= registry::register('config')->get('timezone');
		$v->setProperty( "X-WR-TIMEZONE", $timezone);
		iCalUtilityFunctions::createTimezone( $v, $timezone, array( "X-LIC-LOCATION" => $timezone));

		// Generate the vevents...
		$e = new vevent;
		$e->setProperty('dtstart',		array("timestamp" => $eventdata['timestamp_start'], "tz" => registry::register('config')->get('timezone')));
		$e->setProperty('dtend',		array("timestamp" => $eventdata['timestamp_end'], "tz" => registry::register('config')->get('timezone')));
		$e->setProperty('dtstamp',		array("timestamp" => $this->time->time));
		$e->setProperty('summary',		$this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])));
		$e->setProperty('description',	$eventdata['notes']);
		$e->setProperty('class',		'PUBLIC');
		$e->setProperty('categories',	'PERSONAL');
		$v->setComponent($e);

		// Save or Output the ICS File..
		if($icsfile == true){
			$v->setConfig('filename', $icsfile);
			$v->saveCalendar();
		}else{
			header('Content-type: text/calendar; charset=utf-8;');
			header('Content-Disposition: filename=raidevent-'.$eventdata['timestamp_start'].'.ics');
			echo $v->createCalendar();
			exit;
		}
	}

	// the main page display
	public function display() {
		// Show an error Message if no ID is set
		if(!$this->url_id){
			message_die($this->user->lang('calendar_page_noid'));
		}

		// Show an error message if the event is not a raid
		if($this->pdh->get('calendar_events', 'calendartype', array($this->url_id)) != '1'){
			message_die($this->user->lang('calendar_page_noraid'));
		}

		$eventdata	= $this->pdh->get('calendar_events', 'data', array($this->url_id));

		// COMMENT SYSTEM
		$this->comments->SetVars(array(
			'attach_id'	=> $this->url_id,
			'page'		=> 'viewcalraid',
			'auth'		=> 'a_cal_'
		));

		// check if roles are available
		$allroles		= $this->pdh->get('roles', 'roles', array());
		$rolewnclass	= false;

		foreach($allroles as $v_roles){
			if(count($v_roles['classes']) == 0){
				$rolewnclass	= true;
			}
		}

		// get the members
		$notsigned_filter		= unserialize($this->config->get('calendar_raid_nsfilter'));
		$this->members			= $this->pdh->maget('member', array('userid', 'name', 'classid', 'memberid'), 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(
										((in_array('inactive', $notsigned_filter)) ? false : true),
										((in_array('hidden', $notsigned_filter)) ? false : true),
										((in_array('special', $notsigned_filter)) ? false : true),
										((in_array('twinks', $notsigned_filter)) ? false : true),
									)), 'member', 'classname')));

		// get all attendees
		$this->attendees_raw	= $this->pdh->get('calendar_raids_attendees', 'attendees', array($this->url_id));
		$userlist				= $this->pdh->get('user', 'id_list', array());
		$attendee_ids			= (is_array($this->attendees_raw)) ? array_keys($this->attendees_raw) : array();
		$this->unsigned			= array();

		if(is_array($userlist) && count($userlist) > 0){
			foreach($userlist as $user_id){
				$user_chars		= $this->pdh->get('member', 'connection_id', array($user_id));
				$tmp_chars		= array();
				if(is_array($user_chars) && count($user_chars) > 0){
					foreach($user_chars as $char_id){
						$char_status			= $this->pdh->get('calendar_raids_attendees', 'status', array($this->url_id, $char_id));
						$tmp_chars[$char_id]	= $this->members[$char_id];

						// if one member of this char is in the raud, remove the members and go to next user
						if($char_status != '4'){
							$tmp_chars = array();
							break;
						}
					}
					if(count($tmp_chars) > 0){
						$this->unsigned += $tmp_chars;
					}
				}
			}
		}

		// Guests / rest
		$this->guests			= $this->pdh->get('calendar_raids_guests', 'members', array($this->url_id));
		$this->raidcategories	= ($eventdata['extension']['raidmode'] == 'role') ? $this->pdh->aget('roles', 'name', 0, array($this->pdh->get('roles', 'id_list'))) : $this->game->get('classes', 'id_0');
		$this->mystatus			= $this->pdh->get('calendar_raids_attendees', 'myattendees', array($this->url_id, $this->user->data['user_id']));
		$this->classbreakval	= ($this->config->get('calendar_raid_classbreak')) ? $this->config->get('calendar_raid_classbreak') : 4;
		$modulocount			= intval(count($this->raidcategories)/$this->classbreakval);
		$shownotes_ugroups		= unserialize($this->config->get('calendar_raid_shownotes'));

		// Build the attendees aray for this raid by class
		if(is_array($this->attendees_raw)){
			$this->attendees = $this->attendees_count = array();
			foreach($this->attendees_raw as $attendeeid=>$attendeedata){
				$attclassid = (isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'role') ? $attendeedata['member_role'] : $this->pdh->get('member', 'classid', array($attendeeid));
				$role_class = (($eventdata['extension']['raidmode'] == 'role') ? $attendeedata['member_role'] : $attclassid);
				$this->attendees[$attendeedata['signup_status']][$role_class][$attendeeid] = $attendeedata;
				$this->attendees_count[$attendeedata['signup_status']][$attendeeid] = true;
			}
		}else{
			$this->attendees = array();
		}

		//The Status & Member data
		$raidcal_status = unserialize($this->config->get('calendar_raid_status'));
		$this->raidstatus_full = $this->raidstatus = array();
		if(is_array($raidcal_status)){
			foreach($raidcal_status as $raidcalstat_id){
				if($raidcalstat_id != 4){	// do not use the not signed members
					$this->raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
				}
				$this->raidstatus_full[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
			}
		}

		// not signed in output
		if(in_array(4, $raidcal_status)){
			$a_js_disable = array();

			// sort the array
			foreach ($this->unsigned as $k_unsigned => $v_unsigned) {
				$sort_names[$k_unsigned]    = $v_unsigned['name'];
			    $sort_class[$k_unsigned]    = $v_unsigned['classid'];
			}
			if($this->config->get('calendar_raid_notsigned_classsort')){
				array_multisort($sort_class, SORT_ASC, $sort_names, SORT_ASC, $this->unsigned);
			}else{
				array_multisort($sort_names, SORT_ASC, $this->unsigned);
			}

			// build the json data
			foreach($this->unsigned as $us_key => $us_classdata){
				if($us_classdata['userid'] > 0){
					$myrolesrry		= $this->pdh->get('roles', 'memberroles', array($us_classdata['classid']));
					$array_json[]	= array(
						'id'			=> $us_classdata['memberid'],
						'name'			=> $us_classdata['name'],
						'class_id'		=> $us_classdata['classid'],
						'class_icon'	=> $this->game->decorate('classes', array($us_classdata['classid'])),
						'userid'		=> $us_classdata['userid'],
						'roles'			=> (($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')) && isset($eventdata['extension']['raidmode']) && $eventdata['extension']['raidmode'] == 'role') ? $myrolesrry : '',
						'defaultrole'	=> strlen($this->pdh->get('member', 'defaultrole', array($us_classdata['memberid']))) ? $this->pdh->get('member', 'defaultrole', array($us_classdata['memberid'])) : '',
					);
				}
			}
			$attendee_json	= json_encode($array_json);
			$this->tpl->add_js('var unsigned_attendees = '.$attendee_json);
		}

		$status_first = true;
		foreach($this->raidstatus as $statuskey=>$statusname){
			$this->jquery->Collapse('#viewraidcal_colapse_'.$statuskey);
			$statuscount	= (isset($this->attendees_count[$statuskey])) ? count($this->attendees_count[$statuskey]) : 0;

			// add the guest to the confirmed count
			if($statuskey == 0 && isset($this->guests) && is_array($this->guests) && count($this->guests) > 0){
				$statuscount = $statuscount+count($this->guests);
			}
			
			$this->tpl->assign_block_vars('raidstatus', array(
				'FIRSTROW'	=> ($status_first) ? true : false,
				'ID'		=> $statuskey,
				'NAME'		=> $statusname,
				'COUNT'		=> $statuscount,
				'MAXCOUNT'	=> $eventdata['extension']['attendee_count']
			));

			// the class categories
			$act_classcount = 0;
			$user_brakclm = true;
			$number_break=0;
			foreach ($this->raidcategories as $classid=>$classname){
				$act_classcount++;

				// the break-validation hack
				if($user_brakclm){
					$mybreak = false;
					if(($act_classcount%$this->classbreakval) == 0){
						$mybreak = ($number_break < $modulocount) ? true : false;
						$number_break++;
					}

				}

				$this->tpl->assign_block_vars('raidstatus.classes', array(
					'BREAK'			=> ($mybreak) ? true : false,
					'ID'			=> $classid,
					'NAME'			=> $classname,
					'CLASS_ICON'	=> ($eventdata['extension']['raidmode'] == 'role') ? $this->game->decorate('roles', array($classid)) : $this->game->decorate('classes', array($classid)),
					'MAX'			=> ($eventdata['extension']['raidmode'] == 'none' && $eventdata['extension']['distribution'][$classid] == 0) ? '' : '/'.$eventdata['extension']['distribution'][$classid],
					'COUNT'			=> (isset($this->attendees[$statuskey][$classid])) ? count($this->attendees[$statuskey][$classid]) : 0,
				));

				// The characters
				if(isset($this->attendees[$statuskey][$classid]) && is_array($this->attendees[$statuskey][$classid])){
					foreach($this->attendees[$statuskey][$classid] as $memberid=>$memberdata){
						// generate the member tooltip
						$membertooltip		= array();
						$memberrank			= $this->pdh->get('member', 'rankname', array($memberid));

						$membertooltip[]	= $this->pdh->get('member', 'name', array($memberid)).' ['.$this->user->lang('level').': '.$this->pdh->get('member', 'level', array($memberid)).']';
						if($eventdata['extension']['raidmode'] == 'role'){
							$real_classid = $this->pdh->get('member', 'classid', array($memberid));
							$membertooltip[]	= $this->game->decorate('classes', array($real_classid)).'&nbsp;'.$this->game->get_name('classes', $real_classid);
						}
						if($memberrank){
							$membertooltip[]	= $this->user->lang('rank').": ".$memberrank;
						}
						$membertooltip[]	= $this->user->lang('raidevent_raid_signedin').": ".$this->time->user_date($memberdata['timestamp_signup'], true, false, true);
						if($memberdata['timestamp_change'] > 0){
							$membertooltip[]	= $this->user->lang('raidevent_raid_changed').": ".$this->time->user_date($memberdata['timestamp_change'], true, false, true);
						}

						if($this->config->get('calendar_raid_random') == 1 && $memberdata['random_value'] > 0){
							$membertooltip[]	=  $this->user->lang('raidevent_raid_memtt_roll').': '.$memberdata['random_value'];
						}

						// Per game additional tooltip stuff
						$additionalTTdata = $this->game->callFunc('calendar_membertooltip', array($memberid));
						if($additionalTTdata){
							$membertooltip = array_merge($membertooltip, $additionalTTdata);
						}

						// Twinks in the tooltip
						$main_id = $this->pdh->get('member', 'mainid', array($memberid));
						if($main_id > 0){
							$membertooltip[]	= '';
							$membertooltip[]	= $this->user->lang('mainchar').': '.$this->pdh->get('member', 'name', array($main_id));
							$twinkarray			= $this->pdh->get('member', 'other_members', array($main_id));
							if(count($twinkarray) > 0){
								$twinknames = array();
								foreach($twinkarray as $twinkid){
									$twinknames[] = $this->pdh->get('member', 'name', array($twinkid));
								}
								$membertooltip[]	= $this->user->lang('twinks').': '.implode(', ', $twinknames);
							}
						}

						$drpdwn_twinks = $drpdwn_members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->pdh->get('member', 'userid', array($memberid))))));
						if($eventdata['extension']['raidmode'] == 'role'){
							$memberrole = $this->jquery->dd_ajax_request('charchange_char', 'charchange_role', $drpdwn_twinks, array(), 0, 'viewcalraid.php'.$this->SID.'&eventid='.$this->url_id.'&ajax=role');
							$charchangemenu = array(
								'chars'	=> $memberrole[0],
								'roles'	=> $memberrole[1]
							);
						}else{
							$charchangemenu = array(
								'chars'	=> $this->html->DropDown('charchange_char', $drpdwn_twinks, '0'),
								'roles'	=> ''
							);
						}

						// put the data to the template engine
						$sanitized_note = str_replace('"', "'", $memberdata['note']);
						$this->tpl->assign_block_vars('raidstatus.classes.status', array(
							'MEMBERID'			=> $memberid,
							'CLASSID'			=> $this->pdh->get('member', 'classid', array($memberid)),
							'NAME'				=> $this->pdh->get('member', 'name', array($memberid)),
							'RANDOM'			=> $memberdata['random_value'],
							'TOOLTIP'			=> implode('<br />', $membertooltip),
							'ADMINNOTE'			=> ($memberdata['signedbyadmin']) ? true : false,
							'NOTE'				=> ((trim($memberdata['note']) && $this->user->check_group($shownotes_ugroups, false)) ? $memberdata['note'] : false),
							'NOTE_TT'			=> ((trim($memberdata['note']) && $this->user->check_group($shownotes_ugroups, false)) ? $sanitized_note : false),
							'DD_CHARS'			=> $charchangemenu['chars'],
							'DD_ROLES'			=> $charchangemenu['roles'],
						));
					}
				}
			}
			$status_first = false;
		}

		// raid guests
		if(is_array($this->guests) && count($this->guests) > 0){
			foreach($this->guests as $guestid=>$guestsdata){
				$guest_clssicon	= $this->game->decorate('classes', array($guestsdata['class']));
				$guest_tooltip 	= $this->user->lang('raidevent_raid_signedin').": ".$this->time->user_date($guestsdata['timestamp_signup'], true, false, true)."<br/>".
									$guest_clssicon.'&nbsp;'.$this->game->get_name('classes', $guestsdata['class'])."<br/>".
									$guestsdata['note'];
				$this->tpl->assign_block_vars('guests', array(
					'NAME'		=> $guestsdata['name'],
					'ID'		=> $guestid,
					'CLASSID'	=> $guestsdata['class'],
					'CLASSICON'	=> $guest_clssicon,
					'TOOLTIP'	=> $guest_tooltip
				));
			}
		}

		// Dropdown Menu Array
		$optionsmenu = array(
			0 => array(
				'name'	=> $this->user->lang('raidevent_raid_edit'),
				'link'	=> 'javascript:EditRaid()',
				'img'	=> 'global/edit.png',
				'perm'	=> ($this->user->check_auth('u_cal_event_add', false) || $this->check_permission('creator')),
			),
			1 => array(
				'name'	=> ($eventdata['closed'] == '1') ? $this->user->lang('raidevent_raid_open') : $this->user->lang('raidevent_raid_close'),
				'link'	=> ($eventdata['closed'] == '1') ? $this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;eventid='.$this->url_id.'&amp;closedstatus=open&amp;link_hash='.$this->CSRFGetToken('closedstatus') :  $this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;eventid='.$this->url_id.'&amp;closedstatus=close&amp;link_hash='.$this->CSRFGetToken('closedstatus'),
				'img'	=> ($eventdata['closed'] == '1') ? 'calendar/open.png' : 'calendar/closed_s.png',
				'perm'	=> ($this->user->check_auth('u_cal_event_add', false) || $this->check_permission('raidleader_creator')),
			),
			2 => array(
				'name'	=> $this->user->lang('raidevent_raid_transform'),
				'link'	=> "javascript:TransformRaid('".$this->url_id."')",
				'img'	=> 'calendar/transform.png',
				'perm'	=> $this->user->check_auth('u_cal_event_add', false),
			),
			3 => array(
				'name'	=> $this->user->lang('raideventlist_export_ical'),
				'link'	=> $this->root_path.'calendar/viewcalraid.php'.$this->SID.'&amp;eventid='.$this->url_id.'&amp;ical=true',
				'img'	=> 'calendar/vcalendar_s.png',
				'perm'	=> true,
			),
			4 => array(
				'name'	=> $this->user->lang('raidevent_raid_export'),
				'link'	=> 'javascript:ExportDialog()',
				'img'	=> 'calendar/export.png',
				'perm'	=> true,
			),

			5 => array(
				'name'	=> $this->user->lang('calendars_add_title'),
				'link'	=> 'javascript:AddRaid()',
				'img'	=> 'glyphs/add.png',
				'perm'	=> $this->user->check_auth('u_cal_event_add', false),
			),
			6 => array(
				'name'	=> $this->user->lang('massmail_send'),
				'link'	=> $this->root_path.'admin/manage_massmail.php'.$this->SID.'&amp;event_id='.$this->url_id,
				'img'	=> 'admin/manage_massmail.png',
				'perm'	=> $this->user->check_auth('a_users_massmail', false),
			),
		);

		// preselect the memberid if not signed in
		$presel_charid = ($this->mystatus['member_id'] > 0) ? $this->mystatus['member_id'] : $this->pdh->get('user', 'mainchar', array($this->user->data['user_id']));

		$drpdwn_members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
		if($eventdata['extension']['raidmode'] == 'role'){
			$memberrole = $this->jquery->dd_ajax_request('member_id', 'member_role', $drpdwn_members, array(), $presel_charid, 'viewcalraid.php'.$this->SID.'&eventid='.$this->url_id.'&ajax=role');
		}

		// jQuery Windows
		$this->jquery->Dialog('AddRaid', $this->user->lang('calendar_win_add'), array('url'=>"addevent.php".$this->SID."&simple_head=true", 'width'=>'900', 'height'=>'580', 'onclose' => $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id));
		$this->jquery->Dialog('EditRaid', $this->user->lang('calendar_win_edit'), array('url'=>"addevent.php".$this->SID."&eventid=".$this->url_id."&simple_head=true", 'width'=>'900', 'height'=>'650', 'onclose' => $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id));
		$this->jquery->Dialog('ExportDialog', $this->user->lang('raidevent_raid_export_win'), array('url'=>"raidexport/exports.php".$this->SID."&eventid=".$this->url_id, 'width'=>'640', 'height'=>'470'));
		$this->jquery->Dialog('EditGuest', $this->user->lang('raidevent_raid_editguest_win'), array('url'=>"guests.php".$this->SID."&simple_head=true&guestid='+id+'", 'width'=>'490', 'height'=>'160', 'onclose' => $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id, 'withid' => 'id'));
		$this->jquery->Dialog('TransformRaid', $this->user->lang('raidevent_raid_transform'), array('url'=>"transform.php".$this->SID."&simple_head=true&eventid='+eventid+'", 'width'=>'400', 'height'=>'200', 'onclose' => $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id, 'withid' => 'eventid'));
		$this->jquery->Dialog('AddGuest', $this->user->lang('raidevent_raid_addguest_win'), array('url'=>"guests.php".$this->SID."&eventid='+eventid+'&simple_head=true", 'width'=>'490', 'height'=>'230', 'onclose' => $this->env->link.'calendar/viewcalraid.php'.$this->SID.'&eventid='.$this->url_id, 'withid' => 'eventid'));
		$this->jquery->Dialog('DeleteGuest', $this->user->lang('raidevent_raid_guest_del'), array('custom_js'=>"document.guestp.submit();", 'message'=>$this->user->lang('raidevent_raid_guest_delmsg'), 'withid'=>'id', 'onlickjs'=>'$("#guestid_field").val(id);', 'buttontxt'=>$this->user->lang('delete')), 'confirm');

		// already signed in message
		if($presel_charid > 0){
			$sstat_mname = $this->pdh->get('member', 'name', array($presel_charid));
			switch($this->mystatus['signup_status']){
				case 0: $alreadysignedinmsg = sprintf($this->user->lang(array('raidevent_raid_msg_status', 0)), $sstat_mname); break;
				case 1: $alreadysignedinmsg = sprintf($this->user->lang(array('raidevent_raid_msg_status', 1)), $sstat_mname); break;
				case 2: $alreadysignedinmsg = $this->user->lang(array('raidevent_raid_msg_status', 2)); break;
				case 3: $alreadysignedinmsg = sprintf($this->user->lang(array('raidevent_raid_msg_status', 3)), $sstat_mname); break;
			}
		}

		// Build the Deadline
		$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
		if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
			$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
		}else{
			$deadlinetime	= $this->time->user_date($deadlinedate, true);
		}

		$mysignedstatus		= $this->pdh->get('calendar_raids_attendees', 'status', array($this->url_id, $this->mystatus['member_id']));
		// the status drodown
		$status_dropdown = $this->raidstatus;
		if(isset($status_dropdown[0]) && $mysignedstatus != 0){
			unset($status_dropdown[0]);
		}

		$this->tpl->assign_vars(array(
			// error messages
			'RAID_CLOSED'			=> ($eventdata['closed'] == '1') ? true : false,
			'NOTEPERMISSION'		=> $this->user->check_group($shownotes_ugroups, false),
			'RAID_DEADLINE'			=> ($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $this->mystatus['member_id'] > 0 && $mysignedstatus != 4 && $eventdata['timestamp_end'] > $this->time->time)) ? false : true,

			// globals
			'NO_STATUSES'			=> (is_array($raidcal_status) && count($raidcal_status) < 1) ? true : false,
			'ROLESWOCLASS'			=> ($rolewnclass) ? true : false,
			'COMMENTS'				=> ($this->config->get('pk_enable_comments') == 1) ? $this->comments->Show() : '',
			'EVENT_ID'				=> $this->url_id,
			'MEMBERDATA_FILE'		=> ($eventdata['extension']['raidmode'] == 'role') ? 'calendar/viewcalraid_role.html' : 'calendar/viewcalraid_class.html',

			// settings endabled?
			'S_NOTSIGNED_VISIBLE'	=> (in_array(4, $raidcal_status) && ($this->user->check_auth('a_cal_revent_conf', false) || $this->config->get('calendar_raid_shownotsigned') || $this->check_permission('raidleader'))) ? true : false,
			'IS_CREATOR'			=> ($this->user->check_auth('u_cal_event_add', false) || $this->check_permission('creator')),
			'IS_OPERATOR'			=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader')),
			'SHOW_GUESTS'			=> ($this->config->get('calendar_raid_guests') && ($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission('raidleader') || count($this->guests) > 0)) ? true : false,
			'SHOW_RANDOMVALUE'		=> ($this->config->get('calendar_raid_random') == 1) ? true : false,
			'IS_SIGNEDIN'			=> ($this->mystatus['member_id'] > 0 && $mysignedstatus != 4) ? true : false,
			'NO_CHAR_ASSIGNED'		=> (count($drpdwn_members) > 0) ? false : true,
			'COLORED_NAMESBYCLASS'	=> ($this->config->get('calendar_raid_coloredclassnames')) ? true : false,

			//Data
			'MENU_OPTIONS'			=> $this->jquery->DropDownMenu('colortab', $optionsmenu, 'images', $this->user->lang('raidevent_raid_settbutton')),
			'DD_MYCHARS'			=> ($eventdata['extension']['raidmode'] == 'role') ? $memberrole[0] : $this->html->DropDown('member_id', $drpdwn_members, $presel_charid),
			'DD_MYROLE'				=> ($eventdata['extension']['raidmode'] == 'role') ? $memberrole[1] : '',
			'DD_SIGNUPSTATUS'		=> $this->html->DropDown('signup_status', $status_dropdown, $this->mystatus['signup_status']),
			'DD_MODSIGNUPSTATUS'	=> $this->html->DropDown('moderation_raidstatus', $this->raidstatus_full, '0'),
			'DD_NOTSIGNEDINSTATUS'	=> $this->html->DropDown('notsigned_raidstatus', $this->raidstatus, '0'),

			'SUBSCRIBED_MEMBER_ID'	=> $this->mystatus['member_id'],
			'ATTENDEES_COLSPAN'		=> count($this->raidcategories),
			'RAIDNAME'				=> $this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])),
			'RAIDICON'				=> $this->pdh->get('event', 'html_icon', array($eventdata['extension']['raid_eventid'], 40)),
			'RAIDLEADER'			=> ($eventdata['extension']['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'html_memberlink', 0, array($eventdata['extension']['raidleader'], $this->root_path.'viewcharacter.php', '', false, false, true))) : '',
			'RAIDVALUE'				=> ($eventdata['extension']['raid_value'] > 0) ? $eventdata['extension']['raid_value'] : '0',
			'RAIDNOTE'				=> ($eventdata['notes']) ? nl2br($eventdata['notes']) : '',
			'RAID_ADDEDBY'			=> $this->pdh->get('user', 'name', array($eventdata['creator'])),
			'RAIDDATE'				=> $this->time->user_date($eventdata['timestamp_start']),
			'RAIDTIME_START'		=> $this->time->user_date($eventdata['timestamp_start'], false, true),
			'RAIDTIME_END'			=> $this->time->user_date($eventdata['timestamp_end'], false, true),
			'RAIDTIME_DEADLINE'		=> $deadlinetime,
			'RAIDDATE_ADDED'		=> (isset($eventdata['extension']['created_on']) && $eventdata['extension']['created_on'] > 0) ? $this->time->user_date($eventdata['extension']['created_on'], true, false, true) : false,
			'PLAYER_NOTE'			=> $this->mystatus['note'],
			'COLUMN_WIDTH'			=> (isset($user_brakclm) && count($this->raidcategories) > $this->classbreakval) ? str_replace(',','.',100/$this->classbreakval) : str_replace(',','.',100/count($this->raidcategories)),

			// guests
			'GUEST_COUNT'			=> count($this->guests),

			// Language files
			'L_NOTSIGNEDIN'			=> $this->user->lang(array('raidevent_raid_status', 4)),
			'L_SIGNEDIN_MSG'		=> $alreadysignedinmsg,
			
			'CSRF_CHANGECHARMENU_TOKEN' => $this->CSRFGetToken('changecharmenu'),
			'CSRF_CHANGENOTEMENU_TOKEN' => $this->CSRFGetToken('changenotemenu'),
		));

		$this->core->set_vars(array(
			'page_title'		=> sprintf($this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])), $this->user->lang('raidevent_raid_show_title')),
			'template_file'		=> 'calendar/viewcalraid.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_viewcalraid', viewcalraid::$shortcuts);
registry::register('viewcalraid');
?>