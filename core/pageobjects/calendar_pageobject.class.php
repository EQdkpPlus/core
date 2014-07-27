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

class calendar_pageobject extends pageobject {

	public function __construct() {
		$handler = array(
			'move'				=> array('process' => 'move_event','csrf'=>true),
			'resize'			=> array('process' => 'resize_event','csrf'=>true),
			'deleteid'			=> array('process' => 'delete_event','csrf'=>true),
			'json'				=> array('process' => 'get_json'),
			'checkrepeatable'	=> array('process' => 'get_event_repeatable'),
			'mass_signin'		=> array('process' => 'mass_signin','csrf'=>true),
			'export_tooltip'	=> array('process' => 'export_tooltip'),
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}

	// check calendar specific rights such as if the user is a raidleader or the creator
	private function check_permission($raidid, $userid=0){
		$userid	= ($userid > 0) ? $userid : $this->user->data['user_id'];
		$creator			= $eventdata = $this->pdh->get('calendar_events', 'creatorid', array($raidid));
		$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($raidid));
		$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
		$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
		return (($creator == $userid) || in_array($userid, $raidleaders_users))  ? true : false;
	}

	// sign into multiple raids in the raid list
	public function mass_signin(){
		$eventids = $this->in->getArray('selected_ids', 'int');
		if(is_array($eventids)){
			$usergroups		= $this->config->get('calendar_raid_autoconfirm');
			$signupstatus	= $this->in->get('member_signupstatus', 0);
			if(is_array($usergroups) && count($usergroups) > 0 && $signupstatus == 1){
				if($this->user->check_group($usergroups, false)){
					$signupstatus = 0;
				}
			}
			$myrole = ($this->in->get('member_role', 0) > 0) ? $this->in->get('member_role', 0) : $this->pdh->get('member', 'defaultrole', array($this->in->get('member_id', 0)));	

			foreach($eventids as $eventid){
				$eventdata = $this->pdh->get('calendar_events', 'data', array($eventid));
				
				if ($eventdata['extension']['raidmode'] == 'role' && (int)$myrole == 0){
					continue;
				}
			
				// Build the Deadline
				$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
				if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
					$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
				}else{
					$deadlinetime	= $this->time->user_date($deadlinedate, true);
				}
				$mystatus = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $this->user->id));
				$mysignedstatus	= $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $mystatus['member_id']));
				
				if (((int)$eventdata['closed'] == 1) || !($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $mystatus['member_id'] > 0 && $mysignedstatus != 4 && $eventdata['timestamp_end'] > $this->time->time))){
					continue;
				}
			
				$oldmemberdata = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $this->user->data['user_id']));
				$this->pdh->put('calendar_raids_attendees', 'update_status', array(
					$eventid,
					$this->in->get('member_id', 0),
					$myrole,
					$signupstatus,
					$this->in->get('raidgroup', 0),
					(($oldmemberdata['member_id'] > 0) ? $oldmemberdata['member_id'] : 0),
					$this->in->get('member_note'),
				));
			}
		}
		$this->pdh->process_hook_queue();
	}

	// Operator/Admin: Move the event in the calendar
	public function move_event(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($this->in->get('eventid', 0))){
			$status = $this->pdh->put('calendar_events', 'move_event', array($this->in->get('eventid', 0), $this->in->get('daydelta', 0), $this->in->get('minutedelta', 0), $this->in->get('allday')));
			$this->pdh->process_hook_queue();
			echo((($status) ? $this->user->lang('raidevent_raid_move_succ') : $this->user->lang('raidevent_raid_move_fail')));
			exit;
		}else{
			echo('Nice try. No permission :)');
			exit;
		}

	}

	// Operator/Admin: Resize the event in the calendar
	public function resize_event(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($this->in->get('eventid', 0))){
			$status = $this->pdh->put('calendar_events', 'resize_event', array($this->in->get('eventid', 0), $this->in->get('daydelta', 0), $this->in->get('minutedelta', 0)));
			$this->pdh->process_hook_queue();
			echo((($status) ? $this->user->lang('raidevent_raid_move_succ') : $this->user->lang('raidevent_raid_move_fail')));
			exit;
		}else{
			echo('Nice try. No permission :)');
			exit;
		}
	}

	// Operator/Admin: Delete an event in the calendar
	public function delete_event(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($this->in->get('eventid', 0))){
			$clones_selection	= $this->in->get('cc_selection', 'this');
			$status				= $this->pdh->put('calendar_events', 'delete_cevent', array($this->in->get('deleteid', 0), $clones_selection));
			$this->pdh->process_hook_queue();
			echo($status);
			exit;
		}else{
			echo('Nice try. No permission :)');
			exit;
		}
	}

	public function export_tooltip(){
		// first, lets generate the link
		$exportlink	= $this->env->link.'exchange.php?out=icalfeed&module=calendar&key='.$this->user->data['exchange_key'];

		// build the output
		echo '<form><fieldset class="settings mediumsettings">
					<dl>
						<dt class="onerow">
							<div class="infobox infobox-large infobox-blue clearfix">
								<i class="fa fa-info-circle fa-4x pull-left"></i> '.$this->user->lang('calendar_export_feed').'
							</div>
						</dt>
					</dl>
					<dl>
					<dt><label>'.$this->user->lang('calendar_export_feedurl').'</label><br><span> </span></dt>
						<dd><input name="icalfeedurl" onClick="javascript:this.form.icalfeedurl.focus();this.form.icalfeedurl.select();" size="40" value="'.$exportlink.'" /></dd>
					</dl>
					<dl>
					<dt><label>'.$this->user->lang('calendar_export_download').'</label><br><span> </span></dt>
						<dd><a href="'.$exportlink.'">'.$this->user->lang('calendar_export_dl_ical').'</a></dd>
					</dl>
				</fieldset></form>';
		exit;
	}

	// Check if an event is repeatable
	public function get_event_repeatable(){
		$repeatingval = $this->pdh->get('calendar_events', 'repeating', array($this->in->get('checkrepeatable', 0)));
		echo(($repeatingval != 'none') ? 'true' : 'false');
		exit;
	}

	// fetch the data for the calendar and output as JSON
	public function get_json(){
		$event_json		= array();
		$filters		= ($this->in->exists('filters', 'int')) ? $this->in->getArray('filters', 'int') : false;
		#$range_start	= $this->time->fromformat($this->in->get('start', ''), DATE_ATOM);
		$range_start	= $this->time->fromformat($this->in->get('start', ''), 'Y-m-d');
		$range_end		= $this->time->fromformat($this->in->get('end', ''), 'Y-m-d');

		// parse the feeds
		$feeds = $this->pdh->get('calendars', 'idlist', array('feed', $filters));
		if(is_array($feeds) && count($feeds) > 0){
			foreach($feeds as $feed){
				$feedurl = $this->pdh->get('calendars', 'feed', array($feed));
				if(isValidURL($feedurl)){
					require_once($this->root_path.'libraries/icalcreator/iCalcreator.class.php');
					$vcalendar = new vcalendar(array( 'url' => $feedurl ));
					if( TRUE === $vcalendar->parse()){
						$vcalendar->sort();
						while($comp = $vcalendar->getComponent('vevent')){
							$startdate		= $comp->getProperty('dtstart', 1);
							$enddate		= $comp->getProperty('dtend', 1);
							
							// set the date for the events
							$allday			= (isset($enddate['hour']) && isset($startdate['hour'])) ? false : true;
							if($allday){
								$startdate_out	= sprintf("%04d", $startdate['year']).'-'.sprintf("%02d", $startdate['month']).'-'.sprintf("%02d", $startdate['day']).' 00:00';
								$enddate_out	= sprintf("%04d", $enddate['year']).'-'.sprintf("%02d", $enddate['month']).'-'.sprintf("%02d", $enddate['day']-1).' 00:00';
							}else{
								$startdate_out	= sprintf("%04d", $startdate['year']).'-'.sprintf("%02d", $startdate['month']).'-'.sprintf("%02d", $startdate['day']).' '.((isset($startdate['hour'])) ? sprintf("%02d", $startdate['hour']).':'.sprintf("%02d", $startdate['min']) : '00:00');
								$enddate_out	= sprintf("%04d", $enddate['year']).'-'.$enddate['month'].'-'.$enddate['day'].' '.((isset($enddate['hour'])) ? $enddate['hour'].':'.$enddate['min'] : '00:00');
							}

							// build the event colours
							$eventcolor		= $this->pdh->get('calendars', 'color', $feed);
							$eventcolor_txt	= (get_brightness($eventcolor) > 130) ? 'black' : 'white';

							$event_json[] = array(
								'eventid'		=> $calid,
								'title'			=> $comp->getProperty( 'summary', 1),
								'start'			=> $startdate_out,
								'end'			=> $enddate_out,
								'allDay'		=> $allday,
								'note'			=> $comp->getProperty('description', 1),
								'color'			=> $eventcolor.' !important',
								'textColor'		=> $eventcolor_txt.' !important'
							);
						}
					}
				}
			}
		}

		// add the calendar events to the json feed
		$calendars	= $this->pdh->get('calendars', 'idlist', array('nofeed', $filters));
		$caleventids	= $this->pdh->get('calendar_events', 'id_list', array(false, $range_start, $range_end));
		if(is_array($caleventids) && count($caleventids) > 0){
			foreach($caleventids as $calid){
				$eventextension	= $this->pdh->get('calendar_events', 'extension', array($calid));
				$raidmode		= $eventextension['calendarmode'];
				$eventcolor		= $this->pdh->get('calendars', 'color', $this->pdh->get('calendar_events', 'calendar_id', array($calid)));
				$eventcolor_txt	= (get_brightness($eventcolor) > 130) ? 'black' : 'white';

				if(in_array($this->pdh->get('calendar_events', 'calendar_id', array($calid)), $calendars)){
					if($raidmode == 'raid'){

						// fetch the attendees
						$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($calid));
						$attendees = array();
						if(is_array($attendees_raw)){
							foreach($attendees_raw as $attendeeid=>$attendeerow){
								$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
							}
						}

						// Build the guest array
						$guests	= array();
						if(registry::register('config')->get('calendar_raid_guests') == 1){
							$guestarray = registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($calid));
							if(is_array($guestarray)){
								foreach($guestarray as $guest_row){
									$guests[] = $guest_row['name'];
								}
							}
						}

						// fetch per raid data
						$raidcal_status = $this->config->get('calendar_raid_status');
						$rstatusdata = '';
						if(is_array($raidcal_status)){
							foreach($raidcal_status as $raidcalstat_id){
								if($raidcalstat_id != 4){
									$actcount  = ((isset($attendees[$raidcalstat_id])) ? count($attendees[$raidcalstat_id]) : 0);
									if($raidcalstat_id == 0){
										$actcount += (is_array($guests) ? count($guests) : 0);
									}
									$rstatusdata .= '<div class="raid_status'.$raidcalstat_id.'">'.$this->user->lang(array('raidevent_raid_status', $raidcalstat_id)).': '.$actcount.'</div>';
								}
							}
						}
						$rstatusdata .= '<div class="raid_status_total">'.$this->user->lang('raidevent_raid_required').': '.((isset($eventextension)) ? $eventextension['attendee_count'] : 0).'</div>';

						$deadlinedate	= $this->pdh->get('calendar_events', 'time_start', array($calid)) - ($eventextension['deadlinedate'] * 3600);
						$deadline = ($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $this->pdh->get('calendar_raids_attendees', 'status', array($calid, $this->user->id)) > 0 && $this->pdh->get('calendar_raids_attendees', 'status', array($calid, $this->user->id)) != 4 && $this->pdh->get('calendar_events', 'time_end', array($calid)) > $this->time->time)) ? false : true;
						$deadlineflag = ($deadline) ? '<i class="fa fa-lock fa-lg" title="'.$this->user->lang('raidevent_raid_deadl_reach').'"></i>' : '';

						// Build the JSON
						$event_json[] = array(
							'title'			=> $this->in->decode_entity($this->pdh->get('calendar_events', 'name', array($calid))),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($calid))),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($calid))),
							'closed'		=> ($this->pdh->get('calendar_events', 'raidstatus', array($calid)) == 1) ? true : false,
							'editable'		=> true,
							'eventid'		=> $calid,
							'flag'			=> $deadlineflag.$this->pdh->get('calendar_raids_attendees', 'html_status', array($calid, $this->user->data['user_id'])),
							'url'			=> $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($calid)), $calid),
							'icon'			=> ($eventextension['raid_eventid']) ? $this->pdh->get('event', 'icon', array($eventextension['raid_eventid'], true)) : '',
							'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid)),
							'raidleader'	=> ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '',
							'rstatusdata'	=> $rstatusdata,
							'color'			=> $eventcolor.' !important',
							'textColor'		=> $eventcolor_txt.' !important',
							'operator'		=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($calid)) ? true : false
						);
					}else{
						$event_json[] = array(
							'eventid'		=> $calid,
							'title'			=> $this->pdh->get('calendar_events', 'name', array($calid)),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($calid))),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($calid))),
							'allDay'		=> ($this->pdh->get('calendar_events', 'allday', array($calid)) > 0) ? true : false,
							'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid)),
							'color'			=> $eventcolor,
							'textColor'		=> $eventcolor_txt
						);
					}
				}
			}
		}

		// birthday calendar
		if($this->config->get('calendar_show_birthday') && $this->user->check_auth('u_userlist', false)){
			$birthday_y	= $this->time->date('Y', $range_end);
			$birthdays	= $this->pdh->get('user', 'birthday_list');
			if(is_array($birthdays)){
				foreach($birthdays as $birthday_uid=>$birthday_ts){
					if($birthday_ts > $range_start && $birthday_ts < $range_end){
						$event_json[] = array(
							'className'				=> 'cal_birthday',
							'title'					=> $this->pdh->get('user', 'name', array($birthday_uid)),
							'start'					=> $birthday_y.'-'.$this->time->date('m-d', $birthday_ts),
							'end'					=> $birthday_y.'-'.$this->time->date('m-d', $birthday_ts),
							'allDay'				=> true,
							'textColor'				=> '#000000',
							'backgroundColor'		=> '#E8E8E8',
							'borderColor'			=> '#7F7F7F'
						);
					}
				}
			}
		}

		// hooks
		if ($this->hooks->isRegistered('calendar')){
			$arrHooksData = $this->hooks->process('calendar', $arrHooksData, false);
			if (count($arrHooksData) > 0){
				$event_json = array_merge($arrHooksData, $event_json);
			}
		}

		// Output the array as JSON
		echo json_encode($event_json);exit;
	}

	// the main page display
	public function display(){
		// include the calendar js/css.. css is included in base template dir, but can be overwritten by adding to template
		$this->jquery->fullcalendar();

		//RSS-Feed for next Raids
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - Calendar Raids', 'calendar_raids.xml', array('u_calendar_view'));

		//raid-list
		$settings = $this->pdh->get_page_settings('calendar', 'hptt_calendar_raidlist');
		$view_list = $this->pdh->get('calendar_events', 'id_list', array(true));

		$hptt = $this->get_hptt($settings, $view_list, $view_list, array('%user_id%' => $this->user->data['user_id'], '%use_controller%' => true), $this->user->id);
		$hptt->setPageRef($this->strPath);
		// Raid List
		$presel_charid = $this->pdh->get('member', 'mainchar', array($this->user->data['user_id']));
		$drpdwn_members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
		$memberrole = $this->jquery->dd_ajax_request('member_id', 'member_role', $drpdwn_members, array(), $presel_charid, $this->routing->build('calendarevent').'&ajax=role');
		$raidcal_status = $this->config->get('calendar_raid_status');
		$raidstatus = array();
		if(is_array($raidcal_status)){
			foreach($raidcal_status as $raidcalstat_id){
				if($raidcalstat_id != 4 && $raidcalstat_id != 0){
					$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
				}
			}
		}

		// the delete - series - dropdown
		$deleteall_drpdown 	= array(
			'this'		=> $this->user->lang('calendar_deleteall_drpdwn_this'),
			'all'		=> $this->user->lang('calendar_deleteall_drpdwn_all'),
			'future'	=> $this->user->lang('calendar_deleteall_drpdwn_future'),
			'past'		=> $this->user->lang('calendar_deleteall_drpdwn_past'),
		);

		$this->tpl->assign_vars(array(
			'STARTDAY'		=> ($this->config->get('date_startday') == 'monday') ? '1' : '0',		//Sunday=0, Monday=1
			'JS_TIMEFORMAT'	=> ($this->config->get('default_jsdate_time') != '') ? $this->config->get('default_jsdate_time') : $this->user->lang('style_jstime'),
			'JS_DATEFORMAT'	=> ($this->config->get('default_jsdate_nrml') != '') ? $this->config->get('default_jsdate_nrml') : $this->user->lang('style_jsdate_nrml'),
			'JS_DATEFORMAT2'=> ($this->config->get('default_jsdate_short') != '') ? $this->config->get('default_jsdate_short') : $this->user->lang('style_jsdate_short'),
			'RAID_LIST'		=> $hptt->get_html_table($this->in->get('sort'), '', 0, 100),
			'DD_CHARS'		=> $memberrole[0],
			'DD_ROLES'		=> $memberrole[1],
			'DD_STATUS'		=> new hdropdown('member_signupstatus', array('options' => $raidstatus)),
			'DD_MULTIDEL'	=> new hdropdown('deleteall_selection', array('options' => $deleteall_drpdown)),
			'TXT_NOTE'		=> new htext('member_note', array('size' => '20')),
			'IS_OPERATOR'	=> $this->user->check_auth('u_cal_event_add', false),

			'CSRF_MOVE_TOKEN' => $this->CSRFGetToken('move'),
			'CSRF_RESIZE_TOKEN' => $this->CSRFGetToken('resize'),
			'CSRF_DELETEID_TOKEN' => $this->CSRFGetToken('deleteid'),
			'U_CALENDAR'	=> $this->strPath.$this->SID,
			'U_CALENDAREVENT' => $this->routing->build('CalendarEvent'),
			'U_EDIT_CALENDAREVENT' => $this->routing->build('EditCalendarEvent'),
		));

		// template things
		$this->set_vars(array(
			'template_file'		=> 'calendar/calendar.html',
			'display'			=> true
		));
	}
}
?>