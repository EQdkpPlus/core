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

if(!class_exists('pdh_w_user_groups_users')) {
	class pdh_w_user_groups_users extends pdh_w_generic {
	
		public function add_user_to_group($user_id, $group_id, $blnLogging = true) {
			$arrSet = array(
				'group_id' => $group_id,
				'user_id'  => $user_id,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __groups_users :p")->set($arrSet)->execute();

			if(!$objQuery) {
				return false;
			}

			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function add_user_to_groups($user_id, $group_array) {
			if (is_array($group_array)) {
				$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));

				foreach($group_array as $key=>$group) {
					$group = intval($group);
					if (!(($group == 2 && !isset($memberships[2])) || $group == 0)) {					
						if(!$this->add_user_to_group($user_id, $group)) {
							return false;
						}
					}
				}
				return true;
			} else {
				return false;
			}
		}
		
		public function add_grpleader($arrUserIDs, $group_id){
			if (!is_array($arrUserIDs)){
				$arrUserIDs = array($arrUserIDs);
			}
			
			$arrSet = array(
				'grpleader' => 1,
			);
			
			$arrNames = array();
			foreach($arrUserIDs as $user_id){
				$objQuery = $this->db->prepare("UPDATE __groups_users :p WHERE group_id=? AND user_id=?")->set($arrSet)->execute($group_id, $user_id);
				
				if(!$objQuery) {
					return false;
				}
				$arrNames[] = $this->pdh->get('user', 'name', array($user_id)); 
			}
			
			$log_action = array(
				'{L_USER}' => implode(', ', $arrNames),	
			);
			
			$this->log_insert('action_usergroups_add_groupleader', $log_action, $group_id, $this->pdh->get('user_groups', 'name', array($group_id)));
			
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}
		
		public function remove_grpleader($arrUserIDs, $group_id){
			if (!is_array($arrUserIDs)){
				$arrUserIDs = array($arrUserIDs);
			}
			
			$arrSet = array(
				'grpleader' => 0,
			);
			
			$arrNames = array();
			foreach($arrUserIDs as $user_id){
				$objQuery = $this->db->prepare("UPDATE __groups_users :p WHERE group_id=? AND user_id=?")->set($arrSet)->execute($group_id, $user_id);
				
				if(!$objQuery) {
					return false;
				}
				$arrNames[] = $this->pdh->get('user', 'name', array($user_id));
			}
			
			$log_action = array(
					'{L_USER}' => implode(', ', $arrNames),
			);
				
			$this->log_insert('action_usergroups_remove_groupleader', $log_action, $group_id, $this->pdh->get('user_groups', 'name', array($group_id)));
			
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function add_users_to_group($user_array, $group_id) {
			if (is_array($user_array)) {
				foreach($user_array as $key=>$user){
					if(!$this->add_user_to_group($user, $group_id)) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}

		public function delete_user_from_group($user_id, $group_id) {
			$objQuery = $this->db->prepare("DELETE FROM __groups_users WHERE group_id = ? AND user_id =?")->execute($group_id, $user_id);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('user_groups_update');
				return true;
			}
			return false;
		}

		public function delete_users_from_group($user_array, $group_id) {
			if (is_array($user_array)) {
				$objQuery = $this->db->prepare("DELETE FROM __groups_users WHERE group_id =? AND user_id :in")->in($user_array)->execute($group_id);
			} else {
				return false;
			}
		}

		public function delete_all_user_from_group($group_id) {
			$objQuery = $this->db->prepare("DELETE FROM __groups_users WHERE group_id =?")->execute($group_id);		
			return true;
		}

		public function delete_user_from_groups($user_id, $group_array) {
			$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));
			if (is_array($group_array)) {
				foreach($group_array as $key=>$group) {
					if (!($group == 2 && (!isset($memberships[2]) || $this->user->data['user_id'] == $user_id))) {
						$objQuery = $this->db->prepare("DELETE FROM __groups_users WHERE group_id = ? AND user_id =?")->execute($group, $user_id);
					}
					
				}
			} else {
				return false;
			}
		}
	}
}
?>