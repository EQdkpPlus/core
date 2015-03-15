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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_raids_attendees')){
	class pdh_w_calendar_raids_attendees extends pdh_w_generic{

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_raid_attendees;");
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
		}

		public function update_status($eventid, $memberid, $memberrole='', $signupstatus='', $raidgroup=0, $signed_memberid=0, $note='', $signedbyadmin=0){		
			$memberrole = ($memberrole>0) ? $memberrole : 0;
			if($signed_memberid > 0){
				// fetch the old note
				$oldnote	= $this->pdh->get('calendar_raids_attendees', 'note', array($eventid, $signed_memberid));
				$oldstatus	= $this->pdh->get('calendar_raids_attendees', 'status', array($eventid, $signed_memberid));
				$arrOld = array(
					'note'		=> $oldnote,
					'member'	=> $this->pdh->get('member', 'name', array($signed_memberid)),
					'status'	=> '{LA_raidevent_raid_status['.$oldstatus.']}',
				);
				$arrNew = array(
					'note'	=> (trim($note) != '' && $oldnote != $note) ? $note : $oldnote,
					'member'=> $this->pdh->get('member', 'name', array($memberid)),
					'status'=> '{LA_raidevent_raid_status['.$signupstatus.']}',
				);
				$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE member_id=? AND calendar_events_id=?")->set(array(
					'member_id'				=> $memberid,
					'note'					=> (trim($note) != '' && $oldnote != $note) ? $note : $oldnote,
					'timestamp_change'		=> $this->time->time,
					'raidgroup'				=> $raidgroup,
					'member_role'			=> $memberrole,
					'signup_status'			=> $signupstatus,
				))->execute($signed_memberid, $eventid);
			}else{
				$rand_value = rand(1,100);
				$objQuery = $this->db->prepare("INSERT INTO __calendar_raid_attendees :p")->set(array(
					'note'					=> $note,
					'member_id'				=> $memberid,
					'calendar_events_id'	=> $eventid,
					'timestamp_signup'		=> $this->time->time,
					'raidgroup'				=> $raidgroup,
					'random_value'			=> $rand_value,
					'member_role'			=> $memberrole,
					'signup_status'			=> $signupstatus,
					'signedbyadmin'			=> $signedbyadmin
				))->execute();

				$arrNew = array(
					'note'	=> $note,
					'member'=> $this->pdh->get('member', 'name', array($memberid)),
					'status'=> '{LA_raidevent_raid_status['.$signupstatus.']}',
				);
				
				$arrOld = false;
			}
			
			$arrLang = array(
				'note'	=> '{L_NOTE}',
				'member'=> '{L_MEMBER}',
				'status'=> '{L_calendar_log_charadd_status}',
			);
			
			// add log entry
			$log_action = $this->logs->diff($arrOld, $arrNew, $arrLang);
			if ($arrOld['member'] == $arrNew['member']) $log_action['{L_MEMBER}'] = $this->pdh->get('member', 'name', array($memberid));
			
			$this->log_insert('calendar_log_charchanged', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function moderate_status($eventid, $status, $memberids){
			if(is_array($memberids) && count($memberids) > 0){
				$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE calendar_events_id=? AND member_id :in")->set(array(
						'signup_status'	=> $status
						
				))->in($memberids)->execute($eventid);
				// add log entry
				$log_action = array(
					'{L_calendar_log_charadd_names}'	=> implode(', ', $this->pdh->aget('member', 'name', 0, array($memberids))),
					'{L_calendar_log_charadd_status}'	=> '{LA_raidevent_raid_status['.$status.']}',
				);
				$this->log_insert('calendar_log_statuschanged', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
				$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
			}
		}
		
		public function moderate_group($eventid, $group, $memberids){
			if(is_array($memberids) && count($memberids) > 0){
				$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE calendar_events_id=? AND member_id :in")->set(array(
						'raidgroup'	=> $group
						
				))->in($memberids)->execute($eventid);
				// add log entry
				$log_action = array(
					'{L_calendar_log_charadd_names}'	=> implode(', ', $this->pdh->aget('member', 'name', 0, array($memberids))),
					'{L_calendar_log_charadd_group}'	=> '{LA_raidevent_raid_status['.$status.']}',
				);
				$this->log_insert('calendar_log_groupchanged', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
				$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
			}
		}
		
		public function add_notsigned($eventid, $memberids, $status, $roles=false){
			if(is_array($memberids)){
				foreach($memberids as $realmemids){
					// check if the attendee is already in the database
					$oldmemberid	= ($this->pdh->get('calendar_raids_attendees', 'in_db', array($eventid, $realmemids))) ? $realmemids : 0;

					// do the update
					$role = (isset($roles[$realmemids])) ? $roles[$realmemids] : '';
					$this->update_status($eventid, $realmemids, $role, $status, 0, $oldmemberid, $this->user->lang('raidevent_raid_adminnote'), 1);
				}
			}
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function toggle_twinks($eventid, $new_memid, $old_memid, $role){;
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE member_id=? AND calendar_events_id=?")->set(array(
				'member_id'		=> (int) $new_memid,
				'member_role'	=> (int) $role
			))->execute($old_memid, $eventid);
			
			$arrOld = array(
				'member' => $this->pdh->get('member', 'name', array($old_memid)),	
			);
			$arrNew = array(
				'member' => $this->pdh->get('member', 'name', array($new_memid)),
				'role'	 => $role,
			);
			$arrLang = array(
				'member' => "{L_member}",
				'role'	=> "{L_role}",	
			);
			
			//Logging
			$log_action = $this->logs->diff($arrOld, $arrNew, $arrLang);
			$this->log_insert('calendar_log_twinkchanged', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
			
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function update_group($eventid, $memberid, $tempgroup){
			$old_raidgroup = $this->pdh->get('calendar_raids_attendees', 'raidgroup', array($eventid, $memberid));
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE member_id=? AND calendar_events_id=?")->set(array(
				'raidgroup'		=> $tempgroup
			))->execute($memberid, $eventid);
			
			$log_action = $this->logs->diff(array($old_raidgroup), array($tempgroup), array("{L_CALENDAR_RAIDGROUP}"));
			if($log_action) {
				$log_action["{L_MEMBER}"] = $this->pdh->get('member', 'name', array($memberid));
				$this->log_insert('calendar_log_updatedgroup', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
			}
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}

		public function update_note($eventid, $memberid, $note){
			$old_note = $this->pdh->get('calendar_raids_attendees', 'note', array($eventid, $memberid));
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE member_id=? AND calendar_events_id=?")->set(array(
				'note'		=> $note
			))->execute($memberid, $eventid);
			
			$log_action = $this->logs->diff(array($old_note), array($note), array("{L_NOTE}"));
			if($log_action) {
				$log_action["{L_MEMBER}"] = $this->pdh->get('member', 'name', array($memberid));
				$this->log_insert('calendar_log_updatednote', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)), true, 'calendar');
			}
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}

		public function update_role($eventid, $memberid, $role){
			$old_role = $this->pdh->get('calendar_raids_attendees', 'role', array($eventid, $memberid));
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE member_id=? AND calendar_events_id=?")->set(array(
				'member_role'		=> $role
			))->execute($memberid, $eventid);
			
			$log_action = $this->logs->diff(array($this->pdh->get('roles', 'name', array($old_role))), array($this->pdh->get('roles', 'name', array($role))), array("{L_ROLE}"));
			if($log_action) {
				$log_action["{L_MEMBER}"] = $this->pdh->get('member', 'name', array($memberid));
				$this->log_insert('calendar_log_updatedrole', $log_action, $eventid, $role, true, 'calendar');
			}
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}

		public function confirm_all($eventid){
			$arrMembers = $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($eventid, 1));
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_attendees :p WHERE calendar_events_id=? AND signup_status=1")->set(array(
				'signup_status'		=> 0
			))->execute($eventid);
			
			// add log entry
			$log_action = array(
				'{L_calendar_log_charadd_names}'	=> implode(', ', $this->pdh->aget('member', 'name', 0, array($arrMembers))),
				'{L_calendar_log_charadd_status}'	=> '{LA_raidevent_raid_status[1]}',
			);
			if(count($arrMembers)) $this->log_insert('calendar_log_confirmedall', $log_action, $eventid, $this->pdh->get('calendar_events', 'name', array($eventid)),true, 'calendar');
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function delete_attendees($memberids){
			$memberids = (is_array($memberids)) ? $memberids : array($memberids);
			$objQuery = $this->db->prepare("DELETE FROM __calendar_raid_attendees WHERE member_id :in")->in($memberids)->execute();
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
		}
	}
}
?>