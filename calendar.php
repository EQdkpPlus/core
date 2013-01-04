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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');


class viewcalendar extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('jquery', 'user', 'tpl', 'in', 'pdh', 'html', 'config', 'core', 'time', 'env');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
		$this->user->check_auth('u_calendar_view');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	// sign into multiple raids in the raid list
	public function mass_signin(){
		$eventids = $this->in->getArray('selected_ids', 'int');
		if(is_array($eventids)){
			$usergroups		= unserialize($this->config->get('calendar_raid_autoconfirm'));
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
		if($this->user->check_auth('u_cal_event_add', false)){
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
		if($this->user->check_auth('u_cal_event_add', false)){
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
		if($this->user->check_auth('u_cal_event_add', false)){
			$delete_clones = ($this->in->get('delete_clones', 'false') != 'false') ? true : false;
			$status = $this->pdh->put('calendar_events', 'delete_cevent', array($this->in->get('deleteid', 0), $delete_clones));
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
							<div class="bluebox roundbox">
								<div class="icon_info">'.$this->user->lang('calendar_export_feed').'</div>
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
		$event_json	= array();
		$filters	= ($this->in->exists('filters', 'int')) ? $this->in->getArray('filters', 'int') : false;

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
							$startdate_out	= $startdate['year'].'-'.$startdate['month'].'-'.$startdate['day'].' '.((isset($startdate['hour'])) ? $startdate['hour'].':'.$startdate['min'] : '00:00');
							$enddate_out	= $enddate['year'].'-'.$enddate['month'].'-'.$enddate['day'].' '.((isset($enddate['hour'])) ? $enddate['hour'].':'.$enddate['min'] : '00:00');
							$allday			= (isset($enddate['hour']) && isset($startdate['hour'])) ? false : true;
							$eventcolor		= $this->pdh->get('calendars', 'color', $feed);
							$eventcolor_txt	= (get_brightness($eventcolor) > 130) ? 'black' : 'white';

							$event_json[] = array(
								'eventid'		=> $calid,
								'title'			=> $comp->getProperty( 'summary', 1),
								'start'			=> $startdate_out,
								'end'			=> $enddate_out,
								'allDay'		=> $allday,
								'note'			=> $comp->getProperty('description', 1),
								'color'			=> '#'.$eventcolor,
								'textColor'		=> $eventcolor_txt
							);
						}
					}
				}
			}
		}

		// add the calendar events to the json feed
		$calendars	= $this->pdh->get('calendars', 'idlist', array('nofeed', $filters));
		$caleventids	= $this->pdh->get('calendar_events', 'id_list', array(false, $this->in->get('start', 0), $this->in->get('end', 0)));
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
						$raidcal_status = unserialize($this->config->get('calendar_raid_status'));
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
						$deadlineflag = ($deadline) ? '<img src="'.$this->root_path.'images/calendar/clock_s.png" alt="Deadline" title="'.$this->user->lang('raidevent_raid_deadl_reach').'" />' : '';

						// Build the JSON
						$event_json[] = array(
							'title'			=> $this->in->decode_entity($this->pdh->get('calendar_events', 'name', array($calid))),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($calid))),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($calid))),
							'closed'		=> ($this->pdh->get('calendar_events', 'raidstatus', array($calid)) == 1) ? true : false,
							'editable'		=> true,
							'eventid'		=> $calid,
							'flag'			=> $deadlineflag.$this->pdh->get('calendar_raids_attendees', 'html_status', array($calid, $this->user->data['user_id'])),
							'url'			=> 'calendar/viewcalraid.php'.$this->SID.'&eventid='.$calid,
							'icon'			=> ($eventextension['raid_eventid']) ? $this->pdh->get('event', 'icon', array($eventextension['raid_eventid'], true, true)) : '',
							'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid)),
							'raidleader'	=> ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '',
							'rstatusdata'	=> $rstatusdata,
							'color'			=> '#'.$eventcolor,
							'textColor'		=> $eventcolor_txt
						);
					}else{
						$event_json[] = array(
							'eventid'		=> $calid,
							'title'			=> $this->pdh->get('calendar_events', 'name', array($calid)),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($calid))),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($calid))),
							'allDay'		=> ($this->pdh->get('calendar_events', 'allday', array($calid)) > 0) ? true : false,
							'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid)),
							'color'			=> '#'.$eventcolor,
							'textColor'		=> $eventcolor_txt
						);
					}
				}
			}
		}

		// Output the array as JSON
		echo json_encode($event_json);exit;
	}

	// the main page display
	public function display(){
		// include the calendar js/css.. css is included in base template dir, but can be overwritten by adding to template
		$this->tpl->js_file($this->root_path."libraries/jquery/js/fullcalendar/fullcalendar.min.js");
		if(is_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.css')){
			$this->tpl->css_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.css');
		}else{
			$this->tpl->css_file($this->root_path.'templates/base_template/fullcalendar.css');
		}
		$this->tpl->css_file($this->root_path.'templates/fullcalendar.print.css', 'print');

		//RSS-Feed for next Raids
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - Calendar Raids', 'calendar_raids.xml', array('u_calendar_view'));

		// set the multidimensional lang arrays to template
		$this->tpl->assign_array('daynames',			$this->user->lang('time_daynames'));
		$this->tpl->assign_array('daynames_short',	$this->user->lang('time_daynames_short'));
		$this->tpl->assign_array('monthnames',		$this->user->lang('time_monthnames'));
		$this->tpl->assign_array('monthnames_short',	$this->user->lang('time_monthnames_short'));

		//raid-list
		$settings = $this->pdh->get_page_settings('calendar', 'hptt_calendar_raidlist');
		$view_list = $this->pdh->get('calendar_events', 'id_list', array(true));

		$hptt = $this->get_hptt($settings, $view_list, $view_list, array('%user_id%' => $this->user->data['user_id']), $this->user->id);

		// Raid List
		$presel_charid = $this->pdh->get('member', 'mainchar', array($this->user->data['user_id']));
		$drpdwn_members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
		$memberrole = $this->jquery->dd_ajax_request('member_id', 'member_role', $drpdwn_members, array(), $presel_charid, 'calendar/viewcalraid.php'.$this->SID.'&ajax=role');
		$raidcal_status = unserialize($this->config->get('calendar_raid_status'));
		$raidstatus = array();
		if(is_array($raidcal_status)){
			foreach($raidcal_status as $raidcalstat_id){
				if($raidcalstat_id != 4 && $raidcalstat_id != 0){
					$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
				}
			}
		}

		$this->tpl->assign_vars(array(
			'STARTDAY'		=> ($this->config->get('pk_date_startday') == 'monday') ? '1' : '0',		//Sunday=0, Monday=1
			'RAID_LIST'		=> $hptt->get_html_table($this->in->get('sort'), '', 0, 100),
			'DD_CHARS'		=> $memberrole[0],
			'DD_ROLES'		=> $memberrole[1],
			'DD_STATUS'		=> $this->html->DropDown('member_signupstatus', $raidstatus, ''),
			'TXT_NOTE'		=> $this->html->TextField('member_note', '25'),
			'IS_OPERATOR'	=> $this->user->check_auth('u_cal_event_add', false),

			'CSRF_MOVE_TOKEN' => $this->CSRFGetToken('move'),
			'CSRF_RESIZE_TOKEN' => $this->CSRFGetToken('resize'),
			'CSRF_DELETEID_TOKEN' => $this->CSRFGetToken('deleteid'),
		));

		// template things
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('calendars'),
			'template_file'		=> 'calendar.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_viewcalendar', viewcalendar::__shortcuts());
registry::register('viewcalendar');
?>