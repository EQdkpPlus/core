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

if(!class_exists('pdh_w_roles')){
	class pdh_w_roles extends pdh_w_generic{

		public function insert_role($role_id, $role_name, $role_classes=''){
			$objQuery = $this->db->prepare("INSERT INTO __roles :p")->set(array(
				'role_id'			=> $role_id,
				'role_name'			=> $role_name,
				'role_classes'		=> $role_classes
			))->execute();
			
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('roles_update');
			
			return true;
		}

		public function truncate_role(){
			$this->db->query('TRUNCATE TABLE __roles');
			$this->pdh->enqueue_hook('roles_update');
		}

		public function delete_roles($id){
			$field = (!is_array($id)) ? array($id) : $id;
			
			$objQuery = $this->db->prepare("DELETE FROM __roles WHERE role_id :in")->in($field)->execute();
			
			$this->pdh->enqueue_hook('roles_update');
		}

		public function update_role($role_id, $role_name='', $role_classes=''){
			$role_name		= ($role_name)		? $role_name		: $this->pdh->get('roles', 'name', array($role_id));
			$role_classes	= ($role_classes)	? $role_classes 	: $this->pdh->get('roles', 'classes_r', array($role_id));
			
			$objQuery = $this->db->prepare("UPDATE __roles :p WHERE role_id=?")->set(array(
				'role_name'			=> $role_name,
				'role_classes'		=> $role_classes
			))->execute($role_id);
				
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('roles_update');
				
			return true;
			$this->pdh->enqueue_hook('roles_update', array($role_id));
		}
	}
}
?>