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

class calendar_pageobject extends pageobject {

	public function __construct() {
		$handler = array(
			'move'				=> array('process' => 'move_event','csrf'=>true),
			'resize'			=> array('process' => 'resize_event','csrf'=>true),
			'deleteid'			=> array('process' => 'delete_event','csrf'=>true),
			'json'				=> array('process' => 'get_json'),
			'checkrepeatable'	=> array('process' => 'get_event_repeatable'),
			'mass_signin'		=> array('process' => 'mass_signin','csrf'=>true),
			'handle_invitation'	=> array('process' => 'handle_invitation'),
			'export_tooltip'	=> array('process' => 'export_tooltip'),
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}

	// check calendar specific rights such as if the user is a raidleader or the creator
	private function check_permission($raidid, $userid=0){
		return $this->pdh->get('calendar_events', 'check_operatorperm', array($raidid, $userid));
	}

	// sign into multiple raids in the raid list
	public function mass_signin(){
		$eventids = $this->in->getArray('selected_ids', 'int');
		if(is_array($eventids)){
			$usergroups		= $this->config->get('calendar_raid_confirm_raidgroupchars');
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
				$mystatus		= $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $this->user->id));
				$mysignedstatus	= $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $mystatus['member_id']));

				$deadlinepassed	= ($deadlinedate < $this->time->time) ? true : false;
				if($deadlinepassed && $this->config->get('calendar_raid_allowstatuschange') == '1' && $mystatus['member_id'] > 0){
					if($mysignedstatus != 4 && $eventdata['timestamp_end'] > $this->time->time){
						if($signupstatus > $mysignedstatus || ($signupstatus == 2 && $mysignedstatus == 3)){
							$deadlinepassed	= false;
						}
					}
				}
				if (((int)$eventdata['closed'] == 1) || $deadlinepassed){
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

	public function handle_invitation(){
		$event_id	= $this->in->get('eventid', 0);
		if($this->pdh->get('calendar_events', 'private_userperm', array($event_id))){
			$status = $this->pdh->put('calendar_events', 'handle_invitation', array($this->in->get('eventid', 0), $this->user->data['user_id'], $this->in->get('status', 'decline')));
			$this->pdh->process_hook_queue();
			#$this->display();
		}else{
			echo('Nice try. No permission :)');
			exit;
		}

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
		$exportlink		= $this->env->link.'exchange.php?out=icalfeed&module=calendar&key='.$this->user->data['exchange_key'];
		$exporttypes	= (new hdropdown('type', array('options' => array(
							'raids'			=> $this->user->lang(array('calendar_export_types', 0)),
							'appointments'	=> $this->user->lang(array('calendar_export_types', 1)),
							'all'			=> $this->user->lang(array('calendar_export_types', 2)),
						))))->output();

		$calendarIds 		  = $this->pdh->get('calendars', 'idlist', array());
		$calendar_idlist	  = $this->pdh->aget('calendars', 'name', 0, array($calendarIds));
		$calendarExportFilter = (new hmultiselect('calendarExportFilter', array('options' => $calendar_idlist, 'preview_num' => 3, 'todisable' => array(), 'value' => $calendarIds, 'selectedtext'=>$this->user->lang('calendar_filter_bycalendar'), 'returnJS' => true)))->output();

		// build the output
		echo '
			<script>
				function replaceQueryParam(param, newval, search) {
					var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?")
					var query = search.replace(regex, "$1").replace(/&$/, "")
					return (query.length > 2 ? query + "&" : "?") + param + "=" + newval
				}
				$("#type").change(function(){
					str = replaceQueryParam("type", $(this).val(), $("#icalfeedurl").val());
					$("#icalfeedurl").val(str);
					$("#icaldllink").prop("href", str);
				}).trigger("change");
				$("#calendarExportFilter").change(function(){
					str = replaceQueryParam("calendars", $(this).val(), $("#icalfeedurl").val());
					$("#icalfeedurl").val(str);
					$("#icaldllink").prop("href", str);
				}).trigger("change");
			</script>
			<form><fieldset class="settings mediumsettings">
					<dl>
						<dt class="onerow">
							<div class="infobox infobox-large infobox-blue clearfix">
								<i class="fa fa-info-circle fa-4x pull-left"></i> '.$this->user->lang('calendar_export_feed').'
							</div>
						</dt>
					</dl>
					<dl>
					<dt><label>'.$this->user->lang('calendar_export_type').'</label><br><span> </span></dt>
						<dd>'.$exporttypes.'</dd>
					</dl>
					<dl><dt><label>'.$this->user->lang('calendar_export_choose_calendars').'</label><br><span /></dt><dd>
						'.$calendarExportFilter.'</dd>
					</dl>
					<dl>
					<dt><label>'.$this->user->lang('calendar_export_feedurl').'</label><br><span> </span></dt>
						<dd><input name="icalfeedurl" id="icalfeedurl" onClick="javascript:this.form.icalfeedurl.focus();this.form.icalfeedurl.select();" size="40" value="'.$exportlink.'" /></dd>
					</dl>
					<dl>
					<dt><label>'.$this->user->lang('calendar_export_download').'</label><br><span> </span></dt>
						<dd><a id="icaldllink" href="'.$exportlink.'">'.$this->user->lang('calendar_export_dl_ical').'</a></dd>
					</dl>
				</fieldset></form>';
		exit;
	}

	// Check if an event is repeatable
	public function get_event_repeatable(){
		$repeatingval = $this->pdh->get('calendar_events', 'repeating', array($this->in->get('checkrepeatable', 0)));
		echo(($repeatingval > 0) ? 'true' : 'false');
		exit;
	}

	public function get_json_helper($calender_id, $filterby = 'all'){
		$event_json		= array();
		$range_start	= $this->time->fromformat($this->in->get('start', ''), 'c');
		$range_end		= $this->time->fromformat($this->in->get('end', ''), 'c');

		if($calender_id > 0){
			$feedurl		= $this->pdh->get('calendars', 'feed', array($calender_id));

			// the event is a feed URL
			if(isValidURL($feedurl)){
				require_once($this->root_path.'libraries/icalcreator/iCalcreator.php');
				$vcalendar = new vcalendar(array( 'url' => $feedurl ));
				if( TRUE === $vcalendar->parse()){
					$vcalendar->sort();
					while($comp = $vcalendar->getComponent('vevent')){
						$startdate		= $comp->getProperty('dtstart', 1);
						$enddate		= $comp->getProperty('dtend', 1);

						// set the date for the events
						$allday			= (isset($enddate['hour']) && isset($startdate['hour'])) ? false : true;
						if($allday){
							$startdate_out	= $this->time->mktime(0,0,0,$startdate['month'],$startdate['day'],$startdate['year']);
							$startdate_out	= $this->time->mktime(0,0,0,$enddate['month'],$enddate['day'],$enddate['year']);
						}else{
							$startdate_out	= $this->time->mktime($startdate['hour'],$startdate['min'],0,$startdate['month'],$startdate['day'],$startdate['year']);
							$startdate_out	= $this->time->mktime($enddate['hour'],$enddate['min'],0,$enddate['month'],$enddate['day'],$enddate['year']);
						}

						// build the event colours
						$eventcolor		= $this->pdh->get('calendars', 'color', $calender_id);
						$eventcolor_txt	= (get_brightness($eventcolor) > 130) ? 'black' : 'white';

						$event_json[] = array(
							'eventid'		=> 0,
							'title'			=> $comp->getProperty( 'summary', 1),
							'start'			=> $this->time->date('c', $startdate_out),
							'end'			=> $this->time->date('c', $enddate_out),
							'allDay'		=> $allday,
							'note'			=> str_replace('\n', "<br />", ($comp->getProperty('description', 1))),
							'backgroundColor'	=> $eventcolor.' !important',
							'borderColor'	=> $eventcolor.' !important',
							'textColor'		=> $eventcolor_txt.' !important',
							'className'		=> 'calendarevent_'.$calender_id,
						);
					}
				}
			}else{
				// it is not a feed, do the math!
				$caleventids	= $this->pdh->get('calendar_events', 'id_list', array(false, $range_start, $range_end, array($calender_id), $filterby));
				if(is_array($caleventids) && count($caleventids) > 0){
					foreach($caleventids as $calid){
						$eventextension	= $this->pdh->get('calendar_events', 'extension', array($calid));
						$raidmode		= (isset($eventextension['calendarmode'])) ? $eventextension['calendarmode'] : false;
						$eventcolor		= $this->pdh->get('calendars', 'color', $this->pdh->get('calendar_events', 'calendar_id', array($calid)));
						$eventcolor_txt	= (get_brightness($eventcolor) > 130) ? 'black' : 'white';

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
							if(registry::register('config')->get('calendar_raid_guests') > 0){
								$guestarray = registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($calid));
								
								if(is_array($guestarray)){
									foreach($guestarray as $guest_row){
										if(!isset($guests[$guest_row['status']])) $guests[$guest_row['status']] = array();
										$guests[(int)$guest_row['status']][] = $guest_row['name'];
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
										$actcount += (is_array($guests[$raidcalstat_id]) ? count($guests[$raidcalstat_id]) : 0);
										$rstatusdata .= '<div class="raid_status'.$raidcalstat_id.'">'.$this->user->lang(array('raidevent_raid_status', $raidcalstat_id)).': '.$actcount.'</div>';
									}
								}
							}
							$rstatusdata .= '<div class="raid_status_total">'.$this->user->lang('raidevent_raid_required').': '.((isset($eventextension)) ? $eventextension['attendee_count'] : 0).'</div>';

							$deadlinedate	= $this->pdh->get('calendar_events', 'time_start', array($calid)) - ($eventextension['deadlinedate'] * 3600);
							$deadline = ($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $this->pdh->get('calendar_raids_attendees', 'status', array($calid, $this->user->id)) > 0 && $this->pdh->get('calendar_raids_attendees', 'status', array($calid, $this->user->id)) != 4 && $this->pdh->get('calendar_events', 'time_end', array($calid)) > $this->time->time)) ? false : true;

							// Build the JSON
							$event_json[] = array(
								'type'			=> 'raid',
								'eventid'		=> $calid,
								'editable'		=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($calid)) ? true : false,
								'title'			=> $this->in->decode_entity($this->pdh->get('calendar_events', 'name', array($calid))),
								'url'			=> (!is_utf8($str)) ? utf8_encode($this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($calid)), $calid)) : $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($calid)), $calid),
								'start'			=> $this->time->date('c', $this->pdh->get('calendar_events', 'time_start', array($calid))),
								'end'			=> $this->time->date('c', $this->pdh->get('calendar_events', 'time_end', array($calid))),
								'closed'		=> ($this->pdh->get('calendar_events', 'raidstatus', array($calid)) == 1) ? true : false,
								'flag'			=> $this->pdh->get('calendar_raids_attendees', 'html_status', array($calid, $this->user->data['user_id'])),
								'deadline'		=> ($deadline) ? true : false,
								'icon'			=> $this->pdh->get('calendar_events', 'event_icon', array($calid)),
								'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid, true)),
								'raidleader'	=> ($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : '',
								'rstatusdata'	=> $rstatusdata,
								'backgroundColor'	=> $eventcolor.' !important',
								'borderColor'	=> $eventcolor.' !important',
								'textColor'		=> $eventcolor_txt.' !important',
								'className'		=> 'calendarevent_'.$calender_id.(($this->pdh->get('calendar_events', 'private', array($calid)) == 1) ? ' calendar_raid_private' : ''),
								'isinvited'		=> $this->pdh->get('calendar_events', 'is_invited', array($calid)),
								'raidgroups'	=> ($this->pdh->get('calendar_events', 'private', array($calid)) == 1) ? $this->pdh->get('calendar_events', 'raid_raidgroups', array($calid)) : '',
							);
						}else{
							$alldayevents	= ($this->pdh->get('calendar_events', 'allday', array($calid)) > 0) ? true : false;
							$event_json[] = array(
								'type'			=> 'event',
								'eventid'		=> $calid,
								'editable'		=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->check_permission($calid)) ? true : false,
								'url'			=> (!is_utf8($str)) ? utf8_encode($this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($calid)), $calid)) : $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($calid)), $calid),
								'title'			=> $this->pdh->get('calendar_events', 'name', array($calid)),
								'start'			=> $this->time->date('c', $this->pdh->get('calendar_events', 'time_start', array($calid))),
								'end'			=> $this->time->date('c', $this->pdh->get('calendar_events', 'time_end', array($calid, $alldayevents))),
								'allDay'		=> $alldayevents,
								'note'			=> $this->pdh->get('calendar_events', 'notes', array($calid, true)),
								'backgroundColor'	=> $eventcolor.' !important',
								'borderColor'	=> $eventcolor.' !important',
								'textColor'		=> $eventcolor_txt.' !important',
								'isowner'		=> $this->pdh->get('calendar_events', 'is_owner', array($calid)) || $this->user->check_auth('a_cal_revent_conf', false),
								'isinvited'		=> $this->pdh->get('calendar_events', 'is_invited', array($calid)),
								'joinedevent'	=> $this->pdh->get('calendar_events', 'joined_invitation', array($calid)),
								'author'		=> $this->pdh->get('calendar_events', 'creator', array($calid)),
								'attendees'		=> $this->pdh->get('calendar_events', 'sharedevent_attendees', array($calid)),
								'className'		=> 'calendarevent_'.$calender_id.(($this->pdh->get('calendar_events', 'private', array($calid)) == 1) ? ' calendar_raid_private' : ''),
								'icon'			=> $this->pdh->get('calendar_events', 'event_icon', array($calid)),
							);
						}
					}
				}
			}
		}else{
			if($calender_id === -2 && $this->config->get('calendar_show_birthday') && $this->user->check_auth('u_userlist', false)){
				$birthday_y	= $this->time->date('Y', $range_end);
				$birthdays	= $this->pdh->get('user', 'birthday_list');
				if(is_array($birthdays)){
					foreach($birthdays as $birthday_uid=>$birthday_ts){
						$birthday_month	= $this->time->date('m', $birthday_ts);
						if($birthday_month >= $this->time->date('m', $range_start) && $birthday_month <= $this->time->date('m', $range_end)){
							$event_json[] = array(
								'type'					=> 'birthday',
								'className'				=> 'cal_birthday',
								'title'					=> $this->pdh->get('user', 'name', array($birthday_uid)),
								'start'					=> $birthday_y.'-'.$this->time->date('m-d', $birthday_ts),
								'end'					=> $birthday_y.'-'.$this->time->date('m-d', $birthday_ts),
								'allDay'				=> true,
								'textColor'				=> '#000000',
								'backgroundColor'		=> '#E8E8E8',
								'borderColor'			=> '#7F7F7F',
								'className'				=> 'calendarevent_'.$calender_id,
								'url'					=> $this->routing->build('user', $this->pdh->get('user',  'name', array($birthday_uid)), 'u'.$birthday_uid),
							);
						}
					}
				}
			}elseif($calender_id === -3){
				if ($this->hooks->isRegistered('calendar')){
					$arrHooksData = $this->hooks->process('calendar',array('start' => $range_start, 'end' => $range_end), false);
					if (count($arrHooksData) > 0){
						$event_json = array_merge($arrHooksData, $event_json);
					}
				}
			}
		}
		return $event_json;
	}

	public function get_json(){
		$tmp_calids		= explode('|', $this->in->get('calids', ''));
		$calendar_ids	= (is_array($tmp_calids) && count($tmp_calids) > 0) ? $tmp_calids : array();
		$filter			= ($this->in->exists('eventfilter', 'int')) ? $this->in->get('eventfilter', '') : 'all';

		$event_json		= array();
		foreach($calendar_ids as $id){
			$event_json = array_merge($event_json, $this->get_json_helper((int)$id, $filter));
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($event_json);exit;
	}

	// the main page display
	public function display(){
		// include the calendar js/css.. css is included in base template dir, but can be overwritten by adding to template
		$this->jquery->fullcalendar();
		$this->jquery->monthpicker();

		//RSS-Feed for next Raids
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - Calendar Raids', 'calendar_raids.xml', array('po_calendarevent'));
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - Calendar Events', 'calendar_events.xml', array('po_calendarevent'));
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - Calendar all Entries', 'calendar_all.xml', array('po_calendarevent'));
		
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
			'CALENDAR_LANG'			=> ($this->user->lang('XML_LANG') != '') ? $this->user->lang('XML_LANG') : 'en',
			'STARTDAY'				=> ($this->config->get('date_startday') == 'monday') ? '1' : '0',		//Sunday=0, Monday=1
			'JS_DATEFORMAT'			=> ($this->config->get('default_jsdate_nrml') != '') ? $this->config->get('default_jsdate_nrml') : $this->user->lang('style_jsdate_nrml'),
			'JS_DATEFORMAT2'		=> ($this->config->get('default_jsdate_short') != '') ? $this->config->get('default_jsdate_short') : $this->user->lang('style_jsdate_short'),
			'RAID_LIST'				=> $hptt->get_html_table($this->in->get('sort'), '', 0, 100),
			'DD_CHARS'				=> $memberrole[0],
			'DD_ROLES'				=> $memberrole[1],
			'DD_STATUS'				=> (new hdropdown('member_signupstatus', array('options' => $raidstatus)))->output(),
			'DD_MULTIDEL'			=> (new hdropdown('deleteall_selection', array('options' => $deleteall_drpdown)))->output(),
			'TXT_NOTE'				=> (new htext('member_note', array('size' => '20')))->output(),
			'ADD_RAID'				=> $this->user->check_auth('u_cal_event_add', false) && ($this->pdh->get('calendars', 'calendarids4userid', array($this->user->data['user_id'])) > 0),
			'CSRF_MOVE_TOKEN'		=> $this->CSRFGetToken('move'),
			'CSRF_RESIZE_TOKEN' 	=> $this->CSRFGetToken('resize'),
			'CSRF_DELETEID_TOKEN'	=> $this->CSRFGetToken('deleteid'),
			'U_CALENDAR'			=> $this->strPath.$this->SID,
			'U_CALENDAREVENT'		=> $this->routing->build('CalendarEvent'),
			'U_EDIT_CALENDAREVENT'	=> $this->routing->build('EditCalendarEvent'),
			'HPTT_ADMIN_LINK'		=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('calendar').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
		));

		//Calenderevent Statistics
		$hide_inactive	= false;
		$intRaidgroup	= 0;
		if ($this->in->get('from') && $this->in->get('to')){
			if(!$this->in->exists('timestamps')) {
				$date1 = $this->time->fromformat($this->in->get('from'));
				$date2 = $this->time->fromformat($this->in->get('to'));

				$date1 = (int)($date1 / 1000);
				$date1 = $date1 * 1000;

				$date2 = (int)($date2 / 1000);
				$date2 = $date2 * 1000;

				$date2 += 86400; // Includes raids/items ON that day
			} else {
				$date1 = $this->in->get('from');
				$date2 = $this->in->get('to');
			}
			$date_suffix	= '&amp;timestamps=1&amp;from='.$date1.'&amp;to='.$date2;
			$view_list		= $this->pdh->get('raid', 'raididsindateinterval', array($date1, $date2));
			$date2			-= 86400; // Shows THAT day

			//Create a Summary
			$arrRaidstatsSettings = array(
					'name'				=> 'hptt_viewmember_itemlist',
					'table_main_sub'	=> '%member_id%',
					'table_subs'		=> array('%member_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%', '%from%', '%to%'),
					'page_ref'			=> 'viewcharacter.php',
					'show_numbers'		=> false,
					'show_select_boxes'	=> false,
					'show_detail_twink'	=> false,
					'table_sort_col'	=> 0,
					'table_sort_dir'	=> 'asc',
					'table_presets'		=> array(
							array('name' => 'mlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'mraidgroups', 'sort' => true, 'th_add' => '', 'td_add' => ''),
							array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					),
				);

				if(in_array(0, $raidcal_status)) $arrRaidstatsSettings['table_presets'][] = array('name' => 'raidcalstats_raids_confirmed_fromto', 'sort' => true, 'th_add' => '', 'td_add' => '');
				if(in_array(1, $raidcal_status)) $arrRaidstatsSettings['table_presets'][] = array('name' => 'raidcalstats_raids_signedin_fromto', 'sort' => true, 'th_add' => '', 'td_add' => '');
				if(in_array(2, $raidcal_status)) $arrRaidstatsSettings['table_presets'][] = array('name' => 'raidcalstats_raids_signedoff_fromto', 'sort' => true, 'th_add' => '', 'td_add' => '');
				if(in_array(3, $raidcal_status)) $arrRaidstatsSettings['table_presets'][] = array('name' => 'raidcalstats_raids_backup_fromto', 'sort' => true, 'th_add' => '', 'td_add' => '');
				$arrRaidstatsSettings['table_presets'][] = array('name' => 'raidcalstats_raids_total_fromto', 'sort' => true, 'th_add' => '', 'td_add' => '');

				$show_twinks = false;
				$statsuffix = $date_suffix;
				if($this->in->exists('show_twinks')){
					$show_twinks = true;
					$statsuffix .= '&amp;show_twinks=1';
				}

				if($this->in->exists('hide_inactive')){
					$hide_inactive = true;
					$statsuffix .= '&amp;hide_inactive=1';
				}

				if($this->in->exists('raidgroup')){
					$intRaidgroup = $this->in->get('raidgroup', 0);
					$statsuffix .= '&amp;raidgroup='.$intRaidgroup;
				}

				$arrMemberlist	= $this->pdh->get('member', 'id_list', array(true, true, true, !($show_twinks)));

				//Filter for Raidgroup
				if($intRaidgroup){
					$arrMemberlist = $this->pdh->get('raid_groups_members', 'member_list', array($intRaidgroup));
				}

				//Filter
				if($hide_inactive){
					$arrMemberlistFiltered = array();
					foreach($arrMemberlist as $intMemberID){
						$total = $this->pdh->get('calendar_raids_attendees', 'calstat_raids_total_fromto', array($intMemberID, $date1, $date2, !$show_twinks));
						if($total > 0) $arrMemberlistFiltered[] = $intMemberID;
					}
					$arrMemberlist = $arrMemberlistFiltered;
				}

				$hptt= $this->get_hptt($arrRaidstatsSettings, $arrMemberlist, $arrMemberlist, array('%link_url%' => $this->routing->simpleBuild('character'), '%link_url_suffix%' => '', '%use_controller%' => true, '%from%'=> $date1, '%to%' => $date2, '%with_twink%' => !$show_twinks), md5($date1.'.'.$date2.'.'.($show_twinks)), 'statsort');
				$hptt->setPageRef($this->strPath);

				$sort = $this->in->get('statsort');

				$this->tpl->assign_vars(array (
					'RAIDSTATS_OUT' 		=> $hptt->get_html_table($sort, $statsuffix, null, null, null),
					'S_RAIDSTATS'			=> true,
				));
		} else {
			$date1			= $this->time->time-(30*86400);
			$date2			= $this->time->time;
			$show_twinks	= $this->config->get('show_twinks');
		}

		// build the dropdown for calendars
		$calendar_idlist		= $this->pdh->aget('calendars', 'name', 0, array($this->pdh->get('calendars', 'idlist')));
		$calendar_idlist[-2]	= $this->user->lang('user_sett_f_birthday');
		$calendar_idlist[-3]	= $this->user->lang('calendar_others');
		$todisable				= array();

		$arrRaidgroups = array_merge(array(0 => ' - '), $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list'))));

		$this->tpl->assign_vars(array (
			// Date Picker
			'DATEPICK_DATE_FROM'	=> (new hdatepicker('from', array('value' => $this->time->user_date($date1, false, false, false, function_exists('date_create_from_format')))))->output(),
			'DATEPICK_DATE_TO'		=> (new hdatepicker('to', array('value' => $this->time->user_date($date2, false, false, false, function_exists('date_create_from_format')))))->output(),
			'SHOW_TWINKS_CHECKED'	=> ($show_twinks)?'checked="checked"':'',
			'HIDE_INACTIVE_CHECKED'	=> ($hide_inactive)?'checked="checked"':'',
			'AMOUNT_CALENDARS'		=> count($calendar_idlist),
			'DD_RAIDGROUP'			=> (new hdropdown('raidgroup', array('options' => $arrRaidgroups, 'value' => $intRaidgroup)))->output(),
			'S_SHOW_STAT_RAIDGROUP' => (count($arrRaidgroups) > 1) ? true : false,
			'MS_CALENDAR_SELECT'	=> (new hmultiselect('calendarfilter', array('options' => $calendar_idlist, 'preview_num' => 3, 'todisable' => $todisable, 'value' => array(1,2), 'selectedtext'=>$this->user->lang('calendar_filter_bycalendar'), 'width' => 260)))->output(),
		));

		// template things
		$this->set_vars(array(
			'template_file'		=> 'calendar/calendar.html',
			'display'			=> true
		));
	}
}
