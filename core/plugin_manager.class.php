<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class plugin_manager extends gen_class {
	public static $shortcuts = array('pdh', 'pdl', 'pgh');

	public $plugins 		= array();		// Store Plugin-Objects
	public $status			= array();		// Store Plugin-Status
	
	public static $valid_data_types = array(
		'id',
		'code',
		'name',
		'installed',
		'contact',
		'path',
		'template_path',
		'version',
		'description',
		'long_description',
		'manuallink',
		'homepage',
		'imageurl',
		'author',
		'plus_version',
		'icon'
	);

	public static $valid_dependency_types = array(
		'plus_version',
		'games',
		'php_functions'
	);
	
	public function __construct() {
		if(!$this->pdl->type_known('plugin_error')) $this->pdl->register_type('plugin_error', null, array($this, 'html_format'), array(2, 3, 4), true);
		if(!$this->pdl->type_known('plugin_installation')) $this->pdl->register_type('plugin_installation', null, array($this, 'html_inst_format'), array(0, 1, 2, 3, 4));
		include_once($this->root_path.'core/plugin_generic.class.php');
		$registered = $this->pdh->get('plugins', 'data');
		if(!is_array($registered)) return true;
		foreach($registered as $plugin_code => $data) {
			$this->status[$plugin_code] = PLUGIN_REGISTERED | $data['status'];
			if(($data['status'] & PLUGIN_INSTALLED) && !($data['status'] & PLUGIN_DISABLED)) {
				if(!$this->initialize($plugin_code)) {
					$this->pdl->log('plugin_error', $plugin_code, 'Initialisation failed.');
				} else {
					$this->register_pdh_modules($plugin_code);
					if(method_exists($this->get_plugin($plugin_code), 'autorun')){
						$this->get_plugin($plugin_code)->autorun();
					}
					$this->register_hooks($plugin_code);
				}
			}
		}
	}	
	
	public function html_format($log) {
		$text = 'Plugin "'.$log['args'][0].'": '.$log['args'][1];
		return $text;
	}
	
	public function html_inst_format($log) {
		$text = $log['args'][0];
		return $text;
	}
	
	public function check($plugin_code, $check='not_set', $strict=false) {
		if($check == 'not_set') $check = PLUGIN_ALL & ~PLUGIN_DISABLED;
		if(!is_string($plugin_code) OR !isset($this->status[$plugin_code])) return false;
		$bit_result = ($check & $this->status[$plugin_code]);
		return ($bit_result && (!$strict || $bit_result == $check)) ? true : false;
	}
	
	private function set_status($plugin_code) {
		return $this->pdh->put('plugins', 'set_status', array($plugin_code, ($this->status[$plugin_code] & ~PLUGIN_REGISTERED & ~PLUGIN_INITIALIZED)));
	}
	
	private function initialize($plugin_code, $search=false) {
		if($this->check($plugin_code, PLUGIN_INITIALIZED)) return true;
		$folder = $this->root_path.'plugins/'.$plugin_code;
		$file = $folder.'/'.$plugin_code.'_plugin_class.php';
		if(!is_dir($folder) OR !is_file($file)) {
			if($search) return false;
			$error = (is_dir($folder)) ? 'File '.$plugin_code.'_plugin_class.php not found in folder: "'.$folder.'".' : 'Folder "'.$folder.'" not found.';
			$this->pdl->log('plugin_error', $plugin_code, $error);
			$this->broken($plugin_code);
			return false;
		}
		include_once($file);
		if(!class_exists($plugin_code)) {
			$this->pdl->log('plugin_error', $plugin_code, 'Class "'.$plugin_code.'" not found.');
			$this->broken($plugin_code);
			return false;
		}
		$plugin_object = registry::register($plugin_code);
		if(!is_object($plugin_object)) {
			$this->pdl->log('plugin_error', $plugin_code, 'Class "'.$plugin_code.'" not instantiatable.');
			$this->broken($plugin_code);
			return false;
		}
		//files ok, remove broken status if set
		if($this->check($plugin_code, PLUGIN_BROKEN)) {
			$this->status[$plugin_code] = $this->status[$plugin_code] & ~PLUGIN_BROKEN;
			$this->set_status($plugin_code);
			$this->pdh->process_hook_queue();
		}
		unset($plugin_object);
		$this->status[$plugin_code] |= PLUGIN_INITIALIZED;
		return true;
	}
	
	private function broken($plugin_code) {
		if(!$this->check($plugin_code, PLUGIN_BROKEN) && isset($this->status[$plugin_code])) {
			$this->status[$plugin_code] |= PLUGIN_BROKEN;
			$this->set_status($plugin_code);
			$this->pdh->process_hook_queue();
		}
		if(!$this->check($plugin_code, PLUGIN_DISABLED) && isset($this->status[$plugin_code])) {
			$this->status[$plugin_code] |= PLUGIN_DISABLED;
			$this->set_status($plugin_code);
			$this->pdh->process_hook_queue();
		}
		return true;
	}
	
	public function enable($plugin_code) {
		if(!$this->check($plugin_code, PLUGIN_DISABLED)) return true;
		if(!$this->initialize($plugin_code)) return false;
		$this->status[$plugin_code] = $this->status[$plugin_code] & ~PLUGIN_DISABLED;
		$this->set_status($plugin_code);
		$this->pdh->process_hook_queue();
		return true;
	}
	
	public function install($plugin_code) {
		if($this->check($plugin_code, PLUGIN_INSTALLED)) return true;
		if(!$this->initialize($plugin_code)) return false;
		$this->register_pdh_modules($plugin_code);
		if(method_exists($this->get_plugin($plugin_code), 'pre_install')) {
			$this->get_plugin($plugin_code)->pre_install();
		}
		$install = $this->get_plugin($plugin_code)->install();
		if(is_array($install)) { //sql-errors
			$this->pdl->log('plugin_installation', $install);
			$this->unregister_pdh_modules($plugin_code);
			$this->uninstall($plugin_code);
			return false;
		}
		$this->status[$plugin_code] |= PLUGIN_INSTALLED;
		if(method_exists($this->get_plugin($plugin_code), 'post_install')) {
			$this->get_plugin($plugin_code)->post_install();
		}
		$this->set_status($plugin_code);
		$this->pdh->put('plugins', 'update_version', array($this->get_plugin($plugin_code)->version, $plugin_code));
		$this->pdh->process_hook_queue();
		return true;
	}
	
	public function uninstall($plugin_code) {
		if(!$this->check($plugin_code, PLUGIN_INSTALLED)) return true;
		if(!$this->initialize($plugin_code)) return false;
		$this->pdh->put('comment', 'uninstall', array($plugin_code));
		if(method_exists($this->get_plugin($plugin_code), 'pre_uninstall')) {
			$this->get_plugin($plugin_code)->pre_uninstall();
		}
		$uninstall = $this->get_plugin($plugin_code)->uninstall();
		if(is_array($uninstall)) { //sql-errors
			$this->pdl->log('plugin_installation', $uninstall);
			return false;
		}
		if(method_exists($this->get_plugin($plugin_code), 'post_uninstall')) {
			$this->get_plugin($plugin_code)->post_uninstall();
		}
		$this->status[$plugin_code] = $this->status[$plugin_code] & ~PLUGIN_INSTALLED;
		$this->set_status($plugin_code);
		$this->pdh->process_hook_queue();
		return true;
	}
	
	public function delete($plugin_code) {
		if(!$this->check($plugin_code, PLUGIN_DISABLED) && $this->check($plugin_code, PLUGIN_INSTALLED)) return false;
		if($this->pdh->put('plugins', 'delete_plugin', array($plugin_code))) {
			$this->pdh->process_hook_queue();
			unset($this->status[$plugin_code]);
			return true;
		}
		return false;
	}
	
	public function search($plugin_code = '') {
		if($plugin_code) {
			if(!$this->check($plugin_code, PLUGIN_REGISTERED)) return $this->initialize($plugin_code, true);
			return true;
		} 
		
		if(is_dir($this->root_path.'plugins') AND $dir = opendir($this->root_path.'plugins')) {
			while($plugin_code = readdir($dir)) {
				if(strpos($plugin_code, '.') === false) {
					if(!$this->check($plugin_code, PLUGIN_REGISTERED)) {
						$this->status[$plugin_code] = PLUGIN_REGISTERED;
						if($this->initialize($plugin_code)) $this->pdh->put('plugins', 'add_plugin', array($plugin_code, $this->get_plugin($plugin_code)->version));
					} else {
						$this->initialize($plugin_code);
					}
				}
			}
		}
	}
	
	public function plugin_update_check() {
		foreach($this->get_plugins() as $plugin_code) {
			$plugin_obj = $this->get_plugin($plugin_code);
			$plugin_db_version = $this->pdh->get('plugins', 'data', array($plugin_code, 'version'));
			if(compareVersion($plugin_obj->version, $plugin_db_version) == 1) {
				$found = false;
				if(!isset($tasks)) $tasks = registry::register('mmtaskmanager')->get_task_list(true);
				foreach($tasks as $task => $file) {
					if(strpos($task, 'update_'.$plugin_code) === 0) {
						include_once($file);
						if(compareVersion(registry::register($task)->version, $plugin_db_version) == 1) {
							$found = true;
							break;
						}
					}
				}
				if(!$found) {
					$this->pdh->put('plugins', 'update_version', array($plugin_obj->version, $plugin_code));
					$this->pdh->process_hook_queue();
				}
			}
		}
		return true;
	}
	
	public function get_plugin($plugin_code) {
		if(!$this->check($plugin_code, PLUGIN_REGISTERED)) {
			if(!$this->search($plugin_code)) return false;
		}
		$this->initialize($plugin_code);
		return registry::register($plugin_code);
	}
	
	public function get_plugins($check='not_set', $strict=false) {
		if($check == 'not_set') {
			$check = PLUGIN_INSTALLED | PLUGIN_INITIALIZED;
			$strict = true;
		}
		$retval = array();
		foreach($this->status as $plugin_code => $status) {
			if($this->check($plugin_code, $check, $strict)) $retval[] = $plugin_code;
		}
		return $retval;
	}
	
	/**
	* Add plugins' permission box arrays to an existing permissions array
	* Modifies the array by reference
	*
	* @param $cbox_array 	Array we're modifying
	* @return bool
	*/
	public function generate_permission_boxes(&$cbox_array){
		foreach ( $this->get_plugins() as $plugin_code ){
			if ( $this->get_plugin($plugin_code)->is_permissions() ){
				$cbox_array = array_merge($cbox_array, $this->get_plugin($plugin_code)->permission_boxes());
			}
		}
		return true;
	}

	private function register_hooks($plugin_code) {
		$hooks = $this->get_plugin($plugin_code)->get_hooks();
		foreach($hooks as $hook => $data) {
			$this->pgh->register($hook, $data['class'], $data['method'], 'plugins/'.$plugin_code.'/hooks');
		}
	}
	
	public function get_menus($menu_name='admin_menu') {
		$menu_array = array();
		foreach($this->get_plugins() as $plugin_code) {
			$plugin_menu = $this->get_plugin($plugin_code)->get_menu($menu_name);
			if(is_array($plugin_menu)) $menu_array = array_merge($menu_array, $plugin_menu);
		}
		return $menu_array;
	}
	
	public function add_data($plugin_code, $type, $data='') {
		if(!$this->check($plugin_code, (PLUGIN_INSTALLED | PLUGIN_INITIALIZED), true)) return false;
		if(!is_array($type)) $type = array($type => $data);
		foreach($type as $s_type => $data) {
			if(!in_array($s_type, self::valid_data_types)) {
				$this->pdl->log('plugin_error', $plugin_code, 'Invalid type of data: "'.$s_type.'".');
				continue;
			}
			$this->get_plugin($plugin_code)->add_data($s_type, $data);
		}
		return true;
	}
	
	public function get_data($plugin_code, $type) {
		if($this->check($plugin_code, PLUGIN_BROKEN) || !$this->initialize($plugin_code)) return false;
		return $this->get_plugin($plugin_code)->get_data($type);
	}
	
	private function register_pdh_modules($plugin_code){
		//register pdh read modules
		$rm = $this->get_plugin($plugin_code)->get_pdh_read_modules();
		foreach($rm as $module_name){
			$module_dir  = $this->root_path . 'plugins/' . $plugin_code . '/pdh/read/'.$module_name;
			$this->pdh->register_read_module($module_name, $module_dir);
		}
		//register pdh write modules
		$wm = $this->get_plugin($plugin_code)->get_pdh_write_modules();
		foreach($wm as $module_name){
			$module_dir  = $this->root_path . 'plugins/' . $plugin_code . '/pdh/write/'.$module_name;
			$this->pdh->register_write_module($module_name, $module_dir);
		}
	}

	private function unregister_pdh_modules($plugin_code){
		//unregister pdh read modules
		$rm = $this->get_plugin($plugin_code)->get_pdh_read_modules();
		foreach($rm as $module_name){
			$this->pdh->unregister_read_module($module_name);
		}
		//unregister pdh write modules
		$wm = $this->get_plugin($plugin_code)->get_pdh_write_modules();
		foreach($wm as $module_name){
			$this->pdh->unregister_write_module($module_name);
		}
	}
	
	/**
	* Check plugin dependencies for $plugin_code
	* @param $plugin_code
	* @param $dependency
	*/
	public function check_dependency($plugin_code, $dependency){
		if($this->check($plugin_code, PLUGIN_BROKEN) || !$this->initialize($plugin_code)) return false;
		if(!in_array($dependency, self::$valid_dependency_types)){
			$this->pdl->log('plugin_error', $plugin_code, 'Invalid dependency: "'.$dependency.'".');
			return false;
		}
		return $this->get_plugin($plugin_code)->check_dependency($dependency);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_plugin_manager', plugin_manager::$shortcuts);
?>