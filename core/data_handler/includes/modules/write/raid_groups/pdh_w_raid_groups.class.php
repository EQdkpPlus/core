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

if(!class_exists('pdh_w_raid_groups')) {
	class pdh_w_raid_groups extends pdh_w_generic{

		public function add($name, $color, $desc='', $standard=0, $sortid=0, $system=0) {
				
			$arrSet = array(
					'groups_raid_name'		=> $name,
					'groups_raid_desc'		=> $desc,
					'groups_raid_system'	=> $system,
					'groups_raid_default'	=> $standard,
					'groups_raid_sortid'	=> $sortid,
					'groups_raid_color'		=> $color
			);
				
			$objQuery = $this->db->prepare("INSERT INTO __groups_raid :p")->set($arrSet)->execute();
				
			if(!$objQuery) {
				return false;
			}
			$this->pdh->enqueue_hook('raid_groups_update');
			return $objQuery->insertId;
		}
		
		
		public function add_grp($id, $name, $color, $desc='', $standard=0, $sortid=0,$system=0) {
			return $this->add($name, $color, $desc, $standard, $sortid, $system);
		}

		public function update_grp($id, $name='', $color=0, $desc='', $standard=0, $sortid=0) {
			$old = array();
			$old['name']		= $this->pdh->get('raid_groups', 'name', array($id));
			$old['color']		= $this->pdh->get('raid_groups', 'color', array($id));
			$old['desc']		= $this->pdh->get('raid_groups', 'desc', array($id));
			$old['standard']	= (int)$this->pdh->get('raid_groups', 'standard', array($id));
			$old['sortid']		= (int)$this->pdh->get('raid_groups', 'sortid', array($id));
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
					'groups_raid_name'		=> $name,
					'groups_raid_desc'		=> $desc,
					'groups_raid_default'	=> $standard,
					'groups_raid_sortid'	=> $sortid,
					'groups_raid_color'		=> $color
				);
				
				$objQuery = $this->db->prepare("UPDATE __groups_raid :p WHERE groups_raid_id=?")->set($arrSet)->execute($id);
				
				if(!$objQuery) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('raid_groups_update');
			return true;
		}

		public function delete_grp($id) {
			if ($id == $this->pdh->get('raid_groups', 'standard_group', array())) {
				return false;
			} else {
				$old['name'] = $this->pdh->get('raid_groups', 'name', array($id));
				
				$objQuery = $this->db->prepare("DELETE FROM __groups_raid WHERE (groups_raid_id = ? AND groups_raid_system != '1' AND groups_raid_default != '1');")->execute($id);

				if($objQuery) {
					$this->pdh->put('raid_groups_members', 'delete_all_member_from_group', $id);
					$this->pdh->enqueue_hook('raid_groups_update');
					$this->log_insert('action_raidgroups_deleted', array(), $id, $old['name']);
					return true;
				}
			}
			$this->pdh->enqueue_hook('raid_groups_update');
		}
		
		public function set_default($id){
			$objQuery = $this->db->prepare("UPDATE __groups_raid :p")->set(array('groups_raid_default' => 0))->execute($id);
			$objQuery = $this->db->prepare("UPDATE __groups_raid :p WHERE groups_raid_id=?")->set(array('groups_raid_default' => 1))->execute($id);
			$this->pdh->enqueue_hook('raid_groups_update');
		}
		
		public function reset(){
			$this->set_default(1);
			$id_list = $this->pdh->get('raid_groups', 'id_list', array());
			foreach($id_list as $intGroupID){
				if($this->pdh->get('raid_groups', 'system', array($intGroupID))>0) continue;
				
				$this->delete_grp($intGroupID);
			}
			$this->pdh->enqueue_hook('raid_groups_update');
		}
	}
}
?>