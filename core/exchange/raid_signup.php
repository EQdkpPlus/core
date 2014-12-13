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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_raid_signup')){
	class exchange_raid_signup extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function post_raid_signup($params, $body){
			if ($this->user->check_auth('u_calendar_view', false)){
				$xml = simplexml_load_string($body);
				if ($xml && intval($xml->eventid) > 0){
					$eventid = intval($xml->eventid);
					$eventdata = $this->pdh->get('calendar_events', 'data', array($eventid));
					if ($eventdata && ((int)$this->pdh->get('calendar_events', 'calendartype', array($eventid)) == 1)){
					
						$mystatus = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($eventid, $this->user->data['user_id']));
						
						// Build the Deadline
						$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
						if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
							$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
						}else{
							$deadlinetime	= $this->time->user_date($deadlinedate, true);
						}
						$mysignedstatus	= $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $mystatus['member_id']));
						
						if (((int)$eventdata['closed'] == 1) || !($deadlinedate > $this->time->time || ($this->config->get('calendar_raid_allowstatuschange') == '1' && $mystatus['member_id'] > 0 && $mysignedstatus != 4 && $eventdata['timestamp_end'] > $this->time->time))){
							return $this->pex->error('statuschange not allowed');
						}

						$mychars = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
						$memberid = intval($xml->memberid);
						
						if (intval($memberid) > 0 && in_array($memberid, $mychars)){
							// auto confirm if enabled
							$usergroups		= $this->config->get('calendar_raid_autoconfirm');
							$signupstatus	= ($xml->status && intval($xml->status) < 5 && intval($xml->status) >0) ? intval($xml->status) : 4;
							if(is_array($usergroups) && count($usergroups) > 0 && $signupstatus == 1){
								if($this->user->check_group($usergroups, false)){
									$signupstatus = 0;
								}
							}
							$myrole = (intval($xml->role) > 0) ? intval($xml->role) : $this->pdh->get('member', 'defaultrole', array($memberid));
							if ($eventdata['extension']['raidmode'] == 'role' && (int)$myrole == 0){
								return $this->pex->error('no roleid given');
							}

							$this->pdh->put('calendar_raids_attendees', 'update_status', array(
								$eventid,
								$memberid,
								$myrole,
								$signupstatus,
								($xml->raidgroup) ? intval($xml->raidgroup) : 0,
								$mystatus['member_id'],
								($xml->note) ? filter_var((string)$xml->note, FILTER_SANITIZE_STRING) : '',
							));
							
							//Send Notification to Raidlead, Creator and Admins
							$raidleaders_chars	= ($eventdata['extension']['raidleader'] > 0) ? $eventdata['extension']['raidleader'] : array();
							$arrSendTo			= $this->pdh->get('member', 'userid', array($raidleaders_chars));
							$arrSendTo[] 		= $this->pdh->get('calendar_events', 'creatorid', array($eventid));
							$arrAdmins 			= $this->pdh->get('user', 'users_with_permission', array('a_cal_revent_conf'));
							$arrSendTo			= array_merge($arrSendTo, $arrAdmins);
							$arrSendTo			= array_unique($arrSendTo);
							$strEventTitle		= sprintf($this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])), $this->user->lang('raidevent_raid_show_title')).', '.$this->time->user_date($eventdata['timestamp_start']).' '.$this->time->user_date($eventdata['timestamp_start'], false, true);
							if (!in_array($this->user->id, $arrSendTo)) $this->ntfy->add('calendarevent_char_statuschange', $eventid.'_'.$memberid, $this->pdh->get('member', 'name', array($memberid)), $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventid)), $eventid, true, true), $arrSendTo, $strEventTitle);
								
							$this->pdh->process_hook_queue();
							
							return array('status'	=> 1);
						} else {
							return $this->pex->error('no memberid given');
						}
					} else {
						return $this->pex->error('unknown eventid');
					}
				}
				return $this->pex->error('no eventid given');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>