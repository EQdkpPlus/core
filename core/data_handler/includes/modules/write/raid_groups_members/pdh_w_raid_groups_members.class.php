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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_raid_groups_members')) {
	class pdh_w_raid_groups_members extends pdh_w_generic {

		public function add_member_to_group($member_id, $group_id, $blnLogging = true) {
			$arrSet = array(
				'group_id' => $group_id,
				'member_id'  => $member_id,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __groups_raid_members :p")->set($arrSet)->execute();

			if(!$objQuery) {
				return false;
			}

			$this->pdh->enqueue_hook('raid_groups_update');
			return true;
		}

		public function add_member_to_groups($member_id, $group_array) {
			if (is_array($group_array)) {
				$memberships = $this->pdh->get('raid_groups_members', 'memberships_status', array($member_id));

				foreach($group_array as $key=>$group) {
					$group = intval($group);

					if(!$this->add_member_to_group($member_id, $group)) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}
		
		public function add_grpleader($arrMemberIDs, $group_id){
			if (!is_array($arrMemberIDs)){
				$arrMemberIDs = array($arrMemberIDs);
			}

			$arrNames = array();
			foreach($arrMemberIDs as $member_id){
				$objQuery = $this->db->prepare("UPDATE __groups_raid_members :p WHERE group_id=? AND member_id=?")->set(array('grpleader' => 1))->execute($group_id, $member_id);
				if(!$objQuery) {
					return false;
				}
				$arrNames[] = $this->pdh->get('member', 'name', array($member_id)); 
			}
			
			$log_action = array(
				'{L_MEMBER}' => implode(', ', $arrNames),	
			);
			
			$this->log_insert('action_membergroups_add_groupleader', $log_action, $group_id, $this->pdh->get('raid_groups', 'name', array($group_id)));
			
			$this->pdh->enqueue_hook('raid_groups_update');
			return true;
		}
		
		public function remove_grpleader($arrMemberIDs, $group_id){
			if (!is_array($arrMemberIDs)){
				$arrMemberIDs = array($arrMemberIDs);
			}
			
			$arrSet = array(
				'grpleader' => 0,
			);
			
			$arrNames = array();
			foreach($arrMemberIDs as $member_id){
				$objQuery = $this->db->prepare("UPDATE __groups_raid_members :p WHERE group_id=? AND member_id=?")->set($arrSet)->execute($group_id, $member_id);
				
				if(!$objQuery) {
					return false;
				}
				$arrNames[] = $this->pdh->get('member', 'name', array($member_id));
			}
			
			$log_action = array(
					'{L_USER}' => implode(', ', $arrNames),
			);
				
			$this->log_insert('action_membergroups_remove_groupleader', $log_action, $group_id, $this->pdh->get('raid_groups', 'name', array($group_id)));
			
			$this->pdh->enqueue_hook('raid_groups_update');
			return true;
		}

		public function add_members_to_group($member_array, $group_id) {
			if (is_array($member_array)) {
				foreach($member_array as $key=>$member){
					if(!$this->add_member_to_group($member, $group_id)) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}

		public function delete_member_from_group($member_id, $group_id) {
			$objQuery = $this->db->prepare("DELETE FROM __groups_raid_members WHERE group_id = ? AND member_id =?")->execute($group_id, $member_id);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('raid_groups_update');
				return true;
			}
			return false;
		}

		public function delete_members_from_group($member_array, $group_id) {
			if (is_array($member_array)) {
				$objQuery = $this->db->prepare("DELETE FROM __groups_raid_members WHERE group_id =? AND member_id :in")->in($member_array)->execute($group_id);
				$this->pdh->enqueue_hook('raid_groups_update');
			} else {
				return false;
			}
		}

		public function delete_all_member_from_group($group_id) {
			$objQuery = $this->db->prepare("DELETE FROM __groups_raid_members WHERE group_id =?")->execute($group_id);
			$this->pdh->enqueue_hook('raid_groups_update');
			return true;
		}
	}
}
?>