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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_raids_attendees')){
	class pdh_w_calendar_raids_attendees extends pdh_w_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'time', 'user'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct(){
			parent::__construct();
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_raid_attendees;");
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
		}

		public function update_status($eventid, $memberid, $memberrole='', $signupstatus='', $raidgroup=0, $signed_memberid=0, $note='', $signedbyadmin=0){
			$memberrole = ($memberrole>0) ? $memberrole : 0;
			if($signed_memberid > 0){
				// fetch the old note
				$oldnote	= $this->pdh->get('calendar_raids_attendees', 'note', array($eventid, $signed_memberid));
				$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE member_id='".$this->db->escape($signed_memberid)."' AND calendar_events_id='".$this->db->escape($eventid)."'", array(
					'member_id'				=> $memberid,
					'note'					=> (trim($note) != '' && $oldnote != $note) ? $note : $oldnote,
					'timestamp_change'		=> $this->time->time,
					'raidgroup'				=> $raidgroup,
					'member_role'			=> $memberrole,
					'signup_status'			=> $signupstatus,
				));
			}else{
				srand((double)microtime()*1000000);
				$rand_value = rand(1,100);
				$this->db->query("INSERT INTO __calendar_raid_attendees :params", array(
					'note'					=> $note,
					'member_id'				=> $memberid,
					'calendar_events_id'	=> $eventid,
					'timestamp_signup'		=> $this->time->time,
					'raidgroup'				=> $raidgroup,
					'random_value'			=> $rand_value,
					'member_role'			=> $memberrole,
					'signup_status'			=> $signupstatus,
					'signedbyadmin'			=> $signedbyadmin
				));
			}
			// add log entry
			$log_action = array(
				'{L_ID}'							=> $eventid,
				'{L_DATE}'							=> '{D_'.$this->time->time.'}',
				'{L_NOTE}'							=> (($note) ? $note : $this->pdh->get('calendar_raids_attendees', 'note', array($signed_memberid))),
				'{L_calendar_log_charadd_name}'		=> $this->pdh->get('member', 'name', array($memberid)),
				'{L_calendar_log_charadd_status}'	=> '{LA_raidevent_raid_status['.$signupstatus.']}',
			);
			$this->log_insert('calendar_log_charchanged', $log_action, true, 'calendar');
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function moderate_status($eventid, $status, $memberids){
			if(is_array($memberids) && count($memberids) > 0){
				$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE calendar_events_id=? AND member_id IN(".implode(',', $memberids).");", 
				array('signup_status'	=> $status), $eventid);
				// add log entry
				$log_action = array(
					'{L_ID}'							=> $eventid,
					'{L_calendar_log_charadd_names}'	=> implode(', ', $this->pdh->aget('member', 'name', 0, array($memberids))),
					'{L_calendar_log_charadd_status}'	=> $status,
				);
				$this->log_insert('calendar_log_statuschanged', $log_action, true, 'calendar');
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
			$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE member_id='".$this->db->escape($old_memid)."' AND calendar_events_id=?", array(
				'member_id'		=> (int) $new_memid,
				'member_role'	=> (int) $role
			), $eventid);
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function update_group($eventid, $memberid, $tempgroup){;
			$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE member_id='".$this->db->escape($memberid)."' AND calendar_events_id=?", array(
				'raidgroup'		=> $tempgroup
			), $eventid);
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}

		public function update_note($eventid, $memberid, $note){;
			$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE member_id='".$this->db->escape($memberid)."' AND calendar_events_id=?", array(
				'note'		=> $note
			), $eventid);
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}

		public function confirm_all($eventid){;
			$this->db->query("UPDATE __calendar_raid_attendees SET :params WHERE calendar_events_id=? AND signup_status=1", array(
				'signup_status'		=> 0
			), $eventid);
			// add log entry
			$this->log_insert('calendar_log_confirmedall', array('{L_ID}' => $eventid), true, 'calendar');
			$this->pdh->enqueue_hook('calendar_raid_attendees_update', array($eventid));
		}
		
		public function delete_attendees($memberids){
			$memberids = (is_array($memberids)) ? $memberids : array($memberids);
			$this->db->query("DELETE FROM __calendar_raid_attendees WHERE member_id IN(".implode(',', $memberids).");");
			$this->pdh->enqueue_hook('calendar_raid_attendees_update');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_calendar_raids_attendees', pdh_w_calendar_raids_attendees::__shortcuts());
?>