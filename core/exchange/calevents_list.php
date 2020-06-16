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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_calevents_list')){
	class exchange_calevents_list extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_calevents_list($params, $arrBody){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();

			if ($isAPITokenRequest || $this->user->check_auth('po_calendarevent', false)){

				//Input-Vars:
				// - raids_only 0/1(default: 1)
				// - number (default: 10)
				$blnRaidOnly = (isset($params['get']['raids_only']) && intval($params['get']['raids_only']) == 0) ? false : true;
				$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : 10;

				$arrRaidIDlist = $this->pdh->get('calendar_events', 'id_list', array($blnRaidOnly, $this->time->time));
				$arrRaidIDlist = $this->pdh->sort($arrRaidIDlist, 'calendar_events', 'date', 'asc');
				if (is_array($arrRaidIDlist)) {
					$arrRaidIDlist = array_slice($arrRaidIDlist, 0, $intNumber);
				}
				$out['events'] = array();
				if (is_array($arrRaidIDlist)){
					foreach ($arrRaidIDlist as $intRaidID){
						$eventextension	= $this->pdh->get('calendar_events', 'extension', array($intRaidID));
						$raidmode		= ((int)$this->pdh->get('calendar_events', 'calendartype', array($intRaidID)) == 1) ? true : false;
						$eventcolor		= $this->pdh->get('calendars', 'color', $this->pdh->get('calendar_events', 'calendar_id', array($intRaidID)));

						if($raidmode){
						
							// fetch the attendees
							$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($intRaidID));
							$attendees = array();
							if(is_array($attendees_raw)){
								foreach($attendees_raw as $attendeeid=>$attendeerow){
									$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
								}
							}

							// Build the guest array
							$guests = array(0 => 0, 1=>0, 2=>0, 3=>0);
							if(registry::register('config')->get('calendar_raid_guests') > 0){
								$guestarray = registry::register('plus_datahandler')->get('calendar_raids_guests', 'members', array($intRaidID));
								if(is_array($guestarray)){
									foreach($guestarray as $guest_row){
										$statusid = intval($guest_row['status']);
										$guests[$statusid] = $guests[$statusid] + 1;
									}
								}
							}
	
							// fetch per raid data
							$raidcal_status = $this->config->get('calendar_raid_status');
							$rstatusdata = array();
							if(is_array($raidcal_status)){
								foreach($raidcal_status as $raidcalstat_id){
									if($raidcalstat_id != 4){
										$actcount  = ((isset($attendees[$raidcalstat_id])) ? count($attendees[$raidcalstat_id]) : 0);
										$actcount += $guests[$raidcalstat_id];
										$rstatusdata['status'.$raidcalstat_id] = array(
											'id'	=> $raidcalstat_id,
											'name'	=> $this->user->lang(array('raidevent_raid_status', $raidcalstat_id)),
											'count'	=> $actcount,
										);
									}
								}
							}
							$rstatusdata['required'] = ((isset($eventextension['attendee_count'])) ? $eventextension['attendee_count'] : 0);
	
							$memberdata = $this->pdh->get('calendar_raids_attendees', 'myattendees', array($intRaidID, $this->user->id));
							if($memberdata['member_id'] > 0){
								$memberstatus = $this->pdh->get('calendar_raids_attendees', 'status', array($intRaidID, $memberdata['member_id']));
							} else {
								$memberstatus = -1;
							}
						
						} else {
							$eventextension	= $this->pdh->get('calendar_events', 'extension', array($intRaidID));
							
							// attending users
							$statusofuser = $userstatus = array();
							
							$event_attendees		= (isset($eventextension['attendance']) && count($eventextension['attendance']) > 0) ? $eventextension['attendance'] : array();
							if(count($event_attendees) > 0){
								foreach($event_attendees as $attuserid=>$attstatus){
									#$attendancestatus			= $this->statusID2status($attstatus);
									$statusofuser[$attuserid]	= $attstatus;
									$userstatus[$attstatus][] = array(
											'name'		=> $this->pdh->get('user', 'name', array($attuserid)),
											'icon'		=> $this->pdh->get('user', 'avatar_withtooltip', array($attuserid)),
											'joined'	=> false,
									);
								}
							}
							
							$memberstatus = isset($statusofuser[$this->user->id]) ? $statusofuser[$this->user->id] : -1;
							$rstatusdata = array();
							foreach(array(1,2,3) as $raidcalstat_id){
								$strStatus = $this->statusID2status($raidcalstat_id);
								$rstatusdata['status'.$raidcalstat_id] = array(
										'id'	=> $raidcalstat_id,
										'name'	=> $this->user->lang('calendar_eventdetails_'.$strStatus),
										'count'	=> count($userstatus[$raidcalstat_id]),
								);
							}

						}
						

						$arrRaids['event:'.$intRaidID] = array(
							'type'			=> ($raidmode) ? 'raid' : 'event',
							'title' 		=> unsanitize($this->pdh->get('calendar_events', 'name', array($intRaidID))),
							'start'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_start', array($intRaidID))),
							'start_timestamp'=> $this->pdh->get('calendar_events', 'time_start', array($intRaidID)),
							'end'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('calendar_events', 'time_end', array($intRaidID))),
							'end_timestamp'	=> $this->pdh->get('calendar_events', 'time_end', array($intRaidID)),
							'allDay'		=> ($this->pdh->get('calendar_events', 'allday', array($intRaidID)) > 0) ? 1 : 0,
							'closed'		=> ($this->pdh->get('calendar_events', 'raidstatus', array($intRaidID)) == 1) ? 1 : 0,
							'eventid'		=> $intRaidID,
							'url'			=> $this->env->buildlink(false).register('routing')->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intRaidID)), $intRaidID, false),
							'icon'			=> ($eventextension['raid_eventid']) ? $this->env->link.$this->pdh->get('event', 'icon', array($eventextension['raid_eventid'], true)) : '',
							'note'			=> $this->bbcode->remove_bbcode($this->pdh->get('calendar_events', 'notes', array($intRaidID))),
							'raidleader'	=> unsanitize(($eventextension['raidleader'] > 0) ? implode(', ', $this->pdh->aget('member', 'name', 0, array($eventextension['raidleader']))) : ''),
							'raidstatus'	=> $rstatusdata,
							'user_status'	=> $memberstatus,
							'color'			=> $eventcolor,
							'calendar'		=> $this->pdh->get('calendar_events', 'calendar_id', array($intRaidID)),
							'calendar_name'	=> unsanitize($this->pdh->get('calendar_events', 'calendar', array($intRaidID))),
							'icalfeed'		=> ($this->user->is_signedin()) ? $this->env->link.'exchange.php?out=icalfeed&module=calendar&key='.$this->pdh->get('user', 'exchange_key', array($this->user->id)) : '',
						);
					}
					$out['events'] = $arrRaids;
				}

				return $out;
			} else {
				return $this->pex->error('access denied');
			}

		}
		
		private function statusID2status($status){
			$attendancestatus = "unknown";
			switch($status){
				case 1:		$attendancestatus = 'confirmations'; break;
				case 2:		$attendancestatus = 'maybes'; break;
				case 3:		$attendancestatus = 'declines'; break;
			}
			return $attendancestatus;
		}
	}
}
