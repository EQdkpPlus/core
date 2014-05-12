<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_calevents_details')){
	class exchange_calevents_details extends gen_class {
		public static $shortcuts = array('user', 'config', 'pdh', 'time', 'game', 'bbcode'=>'bbcode', 'pex'=>'plus_exchange');

		public function get_calevents_details($params, $body){
			if ($this->user->check_auth('u_calendar_view', false)){
				if ( intval($params['get']['eventid']) > 0){
					$event_id = intval($params['get']['eventid']);
					$eventdata	= $this->pdh->get('calendar_events', 'data', array($event_id));
					$comments = $this->pdh->get('comment', 'filtered_list', array('viewcalraid', $event_id));
					if (is_array($comments)){
						foreach($comments as $key => $row){
							$arrComments['comment:'.$key] = array(
								'username'			=> $row['username'],
								'date'				=> $this->time->date('Y-m-d H:i', $row['date']),
								'date_timestamp'	=> $row['date'],
								'message'			=> $this->bbcode->toHTML($row['text']),
							);
						}
					}

					$raidmode		= ((int)$this->pdh->get('calendar_events', 'calendartype', array($event_id)) == 1) ? true : false;
					if ($raidmode) {

						// get the memners
						$notsigned_filter		= unserialize($this->config->get('calendar_raid_nsfilter'));
						$this->members			= $this->pdh->maget('member', array('userid', 'name', 'classid'), 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(
														((in_array('inactive', $notsigned_filter)) ? false : true),
														((in_array('hidden', $notsigned_filter)) ? false : true),
														((in_array('special', $notsigned_filter)) ? false : true),
														((in_array('twinks', $notsigned_filter)) ? false : true),
													)), 'member', 'classname')));

						// get all attendees
						$this->attendees_raw	= $this->pdh->get('calendar_raids_attendees', 'attendees', array($event_id));
						$attendeeids = (is_array($this->attendees_raw)) ? array_keys($this->attendees_raw) : array();
						$this->unsigned = $this->members;
						foreach($attendeeids as $mattid){
							$att_userid = $this->pdh->get('member', 'userid', array($mattid));
							$filter_attuserids = $this->pdh->get('member', 'connection_id', array($att_userid));
							if(is_array($filter_attuserids)){
								foreach($filter_attuserids as $attmemid){
									if($this->pdh->get('calendar_raids_attendees', 'status', array($event_id, $attmemid)) != 4){
										unset($this->unsigned[$attmemid]);
									}
								}
							}
						}

						// Guests / rest
						$this->guests			= $this->pdh->get('calendar_raids_guests', 'members', array($event_id));
						$this->raidcategories	= ($eventdata['extension']['raidmode'] == 'role') ? $this->pdh->aget('roles', 'name', 0, array($this->pdh->get('roles', 'id_list'))) : $this->game->get('classes', 'id_0');
						$this->mystatus			= $this->pdh->get('calendar_raids_attendees', 'myattendees', array($event_id, $this->user->data['user_id']));

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
						$arrStatus = array();
						foreach($this->raidstatus as $statuskey=>$statusname){

							$arrClasses = array();
							foreach ($this->raidcategories as $classid=>$classname){
								// The characters
								$arrChars = array();

								if(isset($this->attendees[$statuskey][$classid]) && is_array($this->attendees[$statuskey][$classid])){
									foreach($this->attendees[$statuskey][$classid] as $memberid=>$memberdata){
										//$shownotes_ugroups = unserialize($this->config->get('calendar_raid_shownotes'));

										$arrChars['char:'.$memberid] = array(
											'id'			=> $memberid,
											'name'			=> unsanitize($this->pdh->get('member', 'name', array($memberid))),
											'classid'		=> $this->pdh->get('member', 'classid', array($memberid)),
											'signedbyadmin'	=> ($memberdata['signedbyadmin']) ? 1 : 0,
											'note'			=> ((trim($memberdata['note']) && $this->user->check_group($shownotes_ugroups, false)) ? $memberdata['note'] : ''),
											'rank'			=> $this->pdh->get('member', 'rankname', array($memberid)),
										);

									}
								}

								$arrClasses["category".$classid] = array(
									'id'		=> $classid,
									'name'		=> $classname,
									'color'		=> ($eventdata['extension']['raidmode'] != 'role') ? $this->game->get_class_color($classid) : '',
									'count'		=> (isset($this->attendees[$statuskey][$classid])) ? count($this->attendees[$statuskey][$classid]) : 0,
									'maxcount'	=> ($eventdata['extension']['raidmode'] == 'none' && $eventdata['extension']['distribution'][$classid] == 0) ? '' : $eventdata['extension']['distribution'][$classid],
									'chars'		=> $arrChars,
								);
							}

							$arrStatus['status'.$statuskey] = array(
								'id'		=> $statuskey,
								'name'		=> $statusname,
								'count'		=> (isset($this->attendees_count[$statuskey])) ? count($this->attendees_count[$statuskey]) : 0,
								'maxcount'	=> $eventdata['extension']['attendee_count'],
								'categories'=> $arrClasses,
							);

						}

						// raid guests
						if(is_array($this->guests) && count($this->guests) > 0){
							foreach($this->guests as $guestid=>$guestsdata){
								$arrGuests['guest:'.$guestid] = array(
									'id'		=> $guestid,
									'name'		=> unsanitize($guestsdata['name']),
									'classid'	=> $guestsdata['class'],
									'class'		=> $this->game->get_name('classes', $guestsdata['class']),
								);
							}
						}

						//UserChars
						$user_chars = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
						$mainchar = $this->pdh->get('user', 'mainchar', array($this->user->data['user_id']));
						$arrRoles = array();
						if (is_array($user_chars)){
							foreach ($user_chars as $key=>$charname){
								$roles = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($key))));
								if (is_array($roles)){
									$arrRoles = array();
									foreach ($roles as $roleid => $rolename){
										$arrRoles['role:'.$roleid] = array(
											'id'	=> $roleid,
											'name'	=> $rolename,
											'signed_in'	=> ($this->mystatus['member_role'] == $roleid) ? 1 : 0,
										);
									}
								}

								$arrUserChars['char:'.$key] = array(
									'id'		=> $key,
									'name'		=> unsanitize($charname),
									'signed_in'	=> ($this->mystatus['member_id'] == $key) ? 1 : 0,
									'main'		=> ($key == $mainchar) ? 1 : 0,
									'class'		=> $this->pdh->get('member', 'classid', array($key)),
									'roles'		=> $arrRoles,
								);
							}
						}

						$userstatus['status'] = (!strlen($this->mystatus['signup_status'])) ? -1 : $this->mystatus['signup_status'];
						$userstatus['status_name'] = ($this->mystatus['signup_status'] >= 0) ? $this->raidstatus[$this->mystatus['signup_status']] : '';

						if ($userstatus['status'] > -1){
							$userstatus['char_id'] = $this->mystatus['member_id'];
							$userstatus['char_class'] = $this->pdh->get('member', 'classid', array($this->mystatus['member_id']));
							$userstatus['char_name'] = $this->pdh->get('member', 'name', array($this->mystatus['member_id']));
							if ($this->mystatus['member_role'] > 0 ) $userstatus['char_roleid'] = $this->mystatus['member_role'];
							if ($this->mystatus['member_role'] > 0 ) $userstatus['char_role'] = $this->pdh->get('roles', 'name', array($this->mystatus['member_role']));
						}
						
						$arrCommentsOut = array(
							'count' => count($arrComments),
							'page'	=> 'viewcalraid',
							'attachid' => $event_id,
							'comments' => $arrComments,
						);
						
						
						$out = array(
							'type'			=> ($raidmode == 'raid') ? 'raid' : 'event',
							'categories'	=> ($eventdata['extension']['raidmode'] == 'role') ? 'roles' : 'classes',
							'title' 		=> unsanitize($this->pdh->get('calendar_events', 'name', array($event_id))),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($event_id))),
							'start_timestamp'=> $this->pdh->get('calendar_events', 'time_start', array($event_id)),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($event_id))),
							'end_timestamp'	=> $this->pdh->get('calendar_events', 'time_end', array($event_id)),
							'deadline'		=> $this->time->date('Y-m-d H:i', $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600)),
							'deadline_timestamp'=> $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600),
							'allDay'		=> ($this->pdh->get('calendar_events', 'allday', array($event_id)) > 0) ? 1 : 0,
							'closed'		=> ($this->pdh->get('calendar_events', 'raidstatus', array($event_id)) == 1) ? 1 : 0,
							'icon'			=> ($eventdata['extension']['raid_eventid']) ? $this->pdh->get('event', 'icon', array($eventdata['extension']['raid_eventid'], true, true)) : '',
							'note'			=> unsanitize($this->pdh->get('calendar_events', 'notes', array($event_id))),
							'raidleader'	=> unsanitize(($eventdata['extension']['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventdata['extension']['raidleader']))) : ''),
							'raidstatus'	=> $arrStatus,
							'user_status'	=> $userstatus,
							'user_chars'	=> $arrUserChars,
							'comments'		=> $arrCommentsOut,
							'calendar'		=> $eventdata['calendar_id'],
							'calendar_name'	=> $this->pdh->get('calendar_events', 'calendar', array($event_id)),
						);
					} else {
						$out = array(
							'type'			=> ($raidmode == 'raid') ? 'raid' : 'event',
							'title' 		=> unsanitize($this->pdh->get('calendar_events', 'name', array($event_id))),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($event_id))),
							'start_timestamp'=> $this->pdh->get('calendar_events', 'time_start', array($event_id)),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($event_id))),
							'end_timestamp'	=> $this->pdh->get('calendar_events', 'time_end', array($event_id)),
							'allDay'		=> ($this->pdh->get('calendar_events', 'allday', array($event_id)) > 0) ? 1 : 0,
							'note'			=> unsanitize($this->pdh->get('calendar_events', 'notes', array($event_id))),
							'calendar'		=> $eventdata['calendar_id'],
							'calendar_name'	=> $this->pdh->get('calendar_events', 'calendar', array($event_id)),
						);
					}
					return $out;
				} else {
					return $this->pex->error('no event_id given');
				}
			} else {
				return $this->pex->error('access denied');
			}

		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_calevents_details', exchange_calevents_details::$shortcuts);
?>