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

if(!class_exists('pdh_w_roles')){
	class pdh_w_roles extends pdh_w_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct(){
			parent::__construct();
		}

		public function insert_role($role_id, $role_name, $role_classes=''){
			$this->db->query("INSERT INTO __roles :params", array(
				'role_id'			=> $role_id,
				'role_name'			=> $role_name,
				'role_classes'		=> $role_classes
			));
			$this->pdh->enqueue_hook('roles_update');
		}

		public function truncate_role(){
			$this->db->query('TRUNCATE TABLE __roles');
			$this->pdh->enqueue_hook('roles_update');
		}

		public function delete_roles($id){
			$field = (is_array($id)) ? implode(', ', $id) : $id;
			$this->db->query("DELETE FROM __roles WHERE role_id IN (".$field.")");
			$this->pdh->enqueue_hook('roles_update');
		}

		public function update_role($role_id, $role_name='', $role_classes=''){
			$role_name		= ($role_name)		? $role_name		: $this->pdh->get('roles', 'name', array($role_id));
			$role_classes	= ($role_classes)	? $role_classes 	: $this->pdh->get('roles', 'classes_r', array($role_id));
			$this->db->query("UPDATE __roles SET :params WHERE role_id=?", array(
				'role_name'			=> $role_name,
				'role_classes'		=> $role_classes
			), $role_id);
			$this->pdh->enqueue_hook('roles_update', array($role_id));
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_roles', pdh_w_roles::__shortcuts());
?>