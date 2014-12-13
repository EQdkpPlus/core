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

if(!class_exists('pdh_w_user_groups')) {
	class pdh_w_user_groups extends pdh_w_generic{
	
		public function add_grp($id, $name, $desc='', $standard=0, $hide=0, $sortid=0,$deletable=1) {
			
			$arrSet = array(
				'groups_user_id' 		=> $id,
				'groups_user_name'		=> $name,
				'groups_user_desc'		=> $desc,
				'groups_user_deletable' => $deletable,
				'groups_user_default'	=> $standard,
				'groups_user_hide'		=> $hide,
				'groups_user_sortid'	=> $sortid,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __groups_user :p")->set($arrSet)->execute();
			
			if(!$objQuery) {
				return false;
			}
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function update_grp($id, $name='', $desc='', $standard=0, $hide=0, $team=0, $sortid=0) {
			$old = array();
			$old['name']		= $this->pdh->get('user_groups', 'name', array($id));
			$old['desc']		= $this->pdh->get('user_groups', 'desc', array($id));
			$old['standard']	= (int)$this->pdh->get('user_groups', 'standard', array($id));
			$old['hide']		= (int)$this->pdh->get('user_groups', 'hide', array($id));
			$old['team']		= (int)$this->pdh->get('user_groups', 'team', array($id));
			$old['sortid']		= (int)$this->pdh->get('user_groups', 'sortid', array($id));
			$changes = false;
			
			foreach($old as $varname => $value) {
				if(${$varname} === '') {
					${$varname} = $value;
				} else {
					if(${$varname} != $value) {
						$changes = true;
					}
				}
			}

			if ($changes) {
				$arrSet = array(
					'groups_user_name' => $name,
					'groups_user_desc' => $desc,
					'groups_user_default' => $standard,
					'groups_user_hide' => $hide,
					'groups_user_team' => $team,
					'groups_user_sortid' => $sortid,
				);
				
				$objQuery = $this->db->prepare("UPDATE __groups_user :p WHERE groups_user_id=?")->set($arrSet)->execute($id);
				
				if(!$objQuery) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function delete_grp($id) {
			if ($id == $this->pdh->get('user_groups', 'standard_group', array())) {
				return false;
			} else {
				$old['name'] = $this->pdh->get('user_groups', 'name', array($id));
				
				$objQuery = $this->db->prepare("DELETE FROM __groups_user WHERE (groups_user_id = ? AND groups_user_deletable != '0' AND groups_user_default != '1');")->execute($id);	
				if($objQuery) {
					$this->pdh->put('user_groups_users', 'delete_all_user_from_group', $id);
					$this->db->prepare("DELETE FROM __auth_groups WHERE group_id =?")->execute($id);
					$this->pdh->enqueue_hook('user_groups_update');
					$this->log_insert('action_usergroups_deleted', array(), $id, $old['name']);
					return true;
				}
			}
		}
	}
}
?>