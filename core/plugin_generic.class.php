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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class plugin_generic extends gen_class {

	protected $code 				= '';
	protected $pm 					= false;
	
	protected $data					= array();
	protected $permissions			= array();
	protected $dependency			= array();
	protected $hooks				= array();
	protected $menus				= array();

	protected $sql_queries			= array();

	protected $portal_modules		= array();
	protected $exchange_modules		= array();
	protected $exchange_feeds		= array();
	protected $pdh_read_modules		= array();
	protected $pdh_write_modules	= array();
	
	public function __construct() {
		$this->code = get_class($this);
		$this->user->register_plug_language($this->code);
	}
	
	public static function getApiLevel(){
		return (isset(static::$apiLevel)) ? static::$apiLevel : 0;
	}
	
	protected function add_sql($type, $sql, $order='') {
		$type = ($type == SQL_INSTALL) ? SQL_INSTALL : SQL_UNINSTALL;
		$order = ($order) ? $order : ((isset($this->sql_queries[$type])) ? max(array_keys($this->sql_queries[$type]))+1 : 0);
		$this->sql_queries[$type][$order] = $sql;
	}
	
	public function install() {
		// Check if we need to add permissions
		$permissions = $this->get_permissions();
		ksort($permissions);
		if ( count($permissions) > 0 ){
			foreach ( $permissions as $auth_value => $permission ){
				$this->acl->update_auth_option($auth_value, $permission['auth_default']);
			}
			//Grant permissions to installing user
			if ( $this->user->is_signedin() ){
				$permission_array = array();
				foreach ($permissions as $auth_value => $permission) {
					$permission_array[$this->acl->get_auth_id($auth_value)] = "Y";
				}
				$this->acl->update_user_permissions($permission_array, $this->user->data['user_id']);
			}
			//Grant permissions to groups
			foreach ($permissions as $auth_value => $permission) {
				if ($permission['groups']){
					foreach($permission['groups'] as $key=>$group_id){
						$this->db->prepare("DELETE FROM __auth_groups WHERE group_id = ? AND auth_id = ?")->execute($group_id, $this->acl->get_auth_id($auth_value));
						$this->db->prepare("INSERT INTO __auth_groups :p")->set(array(
								'group_id' => $group_id,
								'auth_id'	=> $this->acl->get_auth_id($auth_value),
								'auth_setting' => 'Y',
						))->execute();
					}
				}
			}
		}

		ksort($this->sql_queries[SQL_INSTALL]);
		foreach($this->sql_queries[SQL_INSTALL] as $sql) {
			if(!$this->db->query($sql)) return $this->db->error;
		}
		return true;
	}
	
	public function uninstall() {
		// Check if we need to remove permissions
		$permissions = $this->get_permissions();
		ksort($permissions);
		if ( count($permissions) > 0 ){
			$auth_ids = array();
			foreach ( $permissions as $auth_value => $permission ){
				$auth_ids[] = $this->acl->get_auth_id($auth_value);
				$this->acl->del_auth_option($auth_value);
			}
			
			$this->db->prepare("DELETE FROM __auth_users WHERE `auth_id` :in")->in($auth_ids)->execute();
			$this->db->prepare("DELETE FROM __auth_groups WHERE `auth_id` :in")->in($auth_ids)->execute();
		}
		
		ksort($this->sql_queries[SQL_UNINSTALL]);
		foreach($this->sql_queries[SQL_UNINSTALL] as $sql) {
			if(!$this->db->query($sql)) return $this->db->error;
		}
		return true;
	}
	
	protected function add_dependency($type, $dependency = '') {
		if(!is_array($type)) $type = array($type => $dependency);
		foreach($type as $i_dep => $dependency) {
			if(!in_array($i_dep, plugin_manager::$valid_dependency_types)) {
				$this->pdl->log('plugin_error', $this->code, 'Invalid type of dependency: "'.$i_dep.'".');
				continue;
			}
			$this->dependency[$i_dep] = $dependency;
		}
		return true;
	}
	
	public function get_dependency($dependency) {
		if(in_array($dependency, plugin_manager::$valid_dependency_types) AND isset($this->dependency[$dependency])) return $this->dependency[$dependency];
		return false;
	}
	
	/*
	 * Default Dependency-Check-Function, may be redefined in specific plugin-class
	 */
	public function check_dependency($dependency) {
		$deps = $this->get_dependency($dependency);
		//dependency not set
		if( $deps == false ){
			return true;
		}
		switch ($dependency){
			case 'plus_version':  
				$check_result = compareVersion($this->config->get('plus_version'), $deps, '>=');
				$check_result = ( $check_result >= 0 ) ? true : false;
				break;
			case 'games':
				$check_result = in_array($this->config->get('default_game'), $deps);
				break;
			case 'php_functions':
				foreach($deps as $function){
					$check_result = function_exists($function);
					if($check_result == false)
						break;
				}
				break;
			default:
				$check_result = true;
		}
		return $check_result;
	}
	
	protected function add_portal_module($module) {
		$this->portal_modules[] = $module;
	}
	
	public function get_portal_modules() {
		return $this->portal_modules;
	}
	
	protected function add_exchange_module($module_name, $feed = false, $feed_url=''){
		if (!$feed){
			$this->exchange_modules[] = $module_name;
		} else {
			$this->exchange_feeds[] = array('name'	=> $module_name, 'url' => $feed_url);
		}
	}	

	public function get_exchange_modules($feeds = false){
		if ($feeds) {
			return $this->exchange_feeds;
		} else {
			return $this->exchange_modules;
		}
	}

	protected function add_pdh_read_module($module_name){
		$this->pdh_read_modules[] = $module_name;
	}

	public function get_pdh_read_modules(){
		return $this->pdh_read_modules;
	}
	
	protected function add_pdh_write_module($module_name){
		$this->pdh_write_modules[] = $module_name;
	}

	public function get_pdh_write_modules(){
		return $this->pdh_write_modules;
	}
	
	protected function add_permission($auth_type, $auth_value, $auth_default, $text, $groups='') {
		$auth_value = $auth_type.'_'.$this->get_data('code').'_'.$auth_value;
		$this->permissions[$auth_value] = array(
			'auth_value'	=> $auth_value,
			'auth_default'	=> $auth_default,
			'text'			=> $text,
			'groups'		=> $groups,
		);
	}

	public function is_permissions(){
		return ( count($this->permissions) ) ? true : false;
	}

	public function get_permissions(){
		return $this->permissions;
	}
	
	public function permission_boxes(){
		$cbox_array = array();
		// Look for $this->user->lang('<code>_plugin') - otherwise just use $this->user->lang('<code>')
		$code = $this->get_data('code');
		$cbox_group = ( $this->user->lang($code.'_plugin') ) ? $this->user->lang($code . '_plugin') : $this->user->lang($code);	
		$plugin_icon = (strlen($this->get_data('icon'))) ? $this->get_data('icon') : "fa-puzzle-piece";

		$cbox_group = $this->core->icon_font($plugin_icon, 'fa-lg fa-fw').' '.$cbox_group ;

		foreach ( $this->permissions as $auth_id => $permissions ){
			$cbox_array[$cbox_group][] = array(
				'CBNAME'		=> $permissions['auth_value'],
				'TEXT'			=> $permissions['text'],
			);
		}
		return $cbox_array;
	}
	
	protected function add_menu($menu_name, $menu_array) {
		$this->menus[$menu_name] = $menu_array;
	}
	
	public function get_menu($menu_name) {
		return (isset($this->menus[$menu_name])) ? $this->menus[$menu_name] : false;
	}
	
	protected function add_hook($hook, $class, $function) {
		$this->hooks[$hook] = array('class' => $class, 'method'=>$function);
	}
	
	public function get_hooks() {
		return $this->hooks;
	}
	
	protected function add_data($type, $data='') {
		if(!is_array($type)) $type = array($type => $data);
		$this->data = array_merge($this->data, $type);
	}
	
	public function get_data($type) {
		if(isset($this->data[$type]) AND in_array($type, plugin_manager::$valid_data_types)) return $this->data[$type];
		return false;
	}
}
?>