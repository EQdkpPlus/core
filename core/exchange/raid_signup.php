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

if (!class_exists('exchange_raid_signup')){
	class exchange_raid_signup extends gen_class {
		public static $shortcuts = array('user', 'config', 'pex'=>'plus_exchange', 'pdh', 'time');
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
							$usergroups		= unserialize($this->config->get('calendar_raid_autoconfirm'));
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
								($xml->note) ? (string)$xml->note : '',
							));
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_raid_signup', exchange_raid_signup::$shortcuts);
?>