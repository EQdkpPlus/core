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

if (!class_exists('pdh_r_roles')){
	class pdh_r_roles extends pdh_r_generic{

		private $roles;
		private $roles_id;
		public $hooks = array(
			'roles_update',
		);

		public $presets = array(
			'roleid'		=> array('roleid',			array('%role_id%'),	array()),
			'rolename'		=> array('name',			array('%role_id%'),	array()),
			'roleclasses'	=> array('classes_list',	array('%role_id%'),	array()),
			'roleedit'		=> array('edit',			array('%role_id%'),	array()),
			'roledelete'	=> array('delete',			array('%role_id%'),	array()),
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_roles_table.roles_id');
			$this->pdc->del('pdh_roles_table.roles');
			$this->pdc->del_prefix('plugin.roles');
			$this->roles = NULL;
			$this->roles_id = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->roles		= $this->pdc->get('pdh_roles_table.roles');
			$this->roles_id		= $this->pdc->get('pdh_roles_table.roles_id');
			if($this->roles !== NULL && $this->roles_id !== NULL){
				return true;
			}

			// empty array as default
			$this->roles	= $this->roles_id = array();
			
			$objQuery = $this->db->query('SELECT * FROM __roles');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->roles[$row['role_id']]['id']			= $row['role_id'];
					$this->roles[$row['role_id']]['name']		= $row['role_name'];
					$this->roles[$row['role_id']]['classes']	= (substr_count($row['role_classes'], "|") > 0) ? explode("|", $row['role_classes']) : ((count($row['role_classes']) > 0) ? array($row['role_classes']) : array());
					$this->roles[$row['role_id']]['classes_r']	= $row['role_classes'];
					$this->roles_id[$row['role_id']]			= $row['role_name'];
				}
				
				$this->pdc->put('pdh_roles_table.roles', $this->roles, NULL);
				$this->pdc->put('pdh_roles_table.roles_id', $this->roles_id, NULL);
			}

			return true;
		}

		public function get_id_list(){
			return (isset($this->roles)) ? array_keys($this->roles) : '';
		}

		public function get_roles($id=''){
			return ($id) ? $this->roles[$id] : $this->roles;
		}

		public function get_roleid($id){
			return $id;
		}

		public function get_roles_dropdown(){
			return $this->roles_id;
		}

		public function get_maxid(){
			$maxarray = max($this->roles);
			return intval($maxarray['id']);
		}

		public function get_name($id){
			return $this->roles[$id]['name'];
		}

		public function get_classes_list($id){
			return (isset($this->roles[$id]['classes_r'])) ? $this->get_roleid2classid($this->roles[$id]['classes_r']) : '';
		}

		public function get_classes($id){
			return $this->roles[$id]['classes'];
		}

		public function get_classes_r($id){
			return $this->roles[$id]['classes_r'];
		}

		public function get_edit($id){
			return '<i class="fa fa-pencil fa-lg hand" title="'.$this->user->lang('edit_role').'" onclick="editRole(\''.$id.'\')"></i>';
		}

		public function get_memberroles($classid, $addfirstrow=false){
			$whatcanibe = ($addfirstrow) ? array(0=>'---') : array();
			foreach($this->roles as $rolearray){
				if(in_array($classid, $rolearray['classes'])){
					$whatcanibe[$rolearray['id']] = $this->roles[$rolearray['id']]['name'];
				}
			}
			return $whatcanibe;
		}

		public function get_roleid2classid($list){
			foreach(explode("|", $list) as $class_id){
				$output[$class_id] = $this->game->get_name('primary', $class_id);
			}
			$classnames = implode(", ", $output);
			return ($classnames) ? $classnames : $list;
		}
	} //end class
} //end if class not exists
?>