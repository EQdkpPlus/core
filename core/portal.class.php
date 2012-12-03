<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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
class portal extends gen_class {
	public static $shortcuts = array('jquery', 'db', 'in', 'tpl', 'user', 'config', 'game', 'pdh', 'pm', 'env', 'acl', 'pgh');

	public $positions	= array('left1', 'left2', 'right', 'middle', 'bottom');
	
	private $output		= array('left1' => '', 'left2' => '', 'right' => '', 'middle' => '', 'bottom' => '');
	private $lang_inits	= array();
	private $objs		= array();
	private $enabled_modules = array();

	public function __construct() {
		// init the variables...
		$this->isAdmin		= $this->user->check_auth('a_config_man', false);
		$this->enabled_modules = $this->pdh->maget('portal', array('path', 'plugin', 'position'), 0, array($this->pdh->get('portal', 'id_list', array(array('enabled' => 1)))));
		if(is_array($this->enabled_modules)) {
			foreach($this->enabled_modules as $module_id => $data) {
				//Register pdh callbacks
				$this->register_pdh_callback($data['path'], $data['plugin'], $module_id, $data['position']);
				//Register global hooks
				$this->register_global_hooks($data['path'], $data['plugin'], $module_id, $data['position']);
			}
		}
	}
	
	public function module_output() {
		$this->output = array('left1' => '', 'left2' => '', 'right' => '', 'middle' => '', 'bottom' => '');
		$selected_portal_pos = (unserialize(stripslashes($this->config->get('pk_permanent_portal')))) ? unserialize(stripslashes($this->config->get('pk_permanent_portal'))) : array();
		$scriptname = explode('?', $this->config->get('start_page'));
		$is_start_page = (basename($_SERVER['PHP_SELF']) == basename($scriptname[0])) ? true : false;
		if(is_array($this->enabled_modules)){
			foreach($this->enabled_modules as $module_id => $data) {
				if($data['position'] == 'left1' || $data['position'] == 'left2' || $is_start_page || (in_array($data['position'], $selected_portal_pos) && !defined('IN_ADMIN'))) {
					$this->load_lang($data['path'], $data['plugin']);
					$this->output[$data['position']] .= $this->get_module_content($data['path'], $data['plugin'], $module_id, $data['position']);
				}
			}
		}
		
		if(strlen($this->output['right']) > 1) {
			$this->tpl->assign_var('THIRD_C', true);
		} else {
			$this->tpl->assign_var('THIRD_C', false);
		}
		$this->assign_to_tpl();
	}
	
	private function assign_to_tpl() {
		foreach($this->output as $posi => $out) {
			$this->tpl->assign_var('PORTAL_'.strtoupper($posi), $out);
		}
	}
	
	// To avoid reloading after saving on manage_portal
	public function reset() {
		$this->output = array('left1' => '', 'left2' => '', 'right' => '', 'middle' => '', 'bottom' => '');
		$this->assign_to_tpl();
		$this->module_output();
	}
	
	//to reset cache of module
	public function reset_portal($path, $plugin='') {
		$this->get_module($path, $plugin);
		if(method_exists($this->objs[$path], 'reset')) $this->objs[$path]->reset();
	}
	
	public function check_visibility($module_id){
		$vis = $this->pdh->get('portal', 'visibility', array($module_id));
		$perm = ($this->user->check_group($vis, false) || intval($vis[0]) == 0);
		return $perm;
	}
	
	private function register_pdh_callback($path, $plugin, $module_id, $posi) {
		$obj = $this->get_module($path, $plugin, $posi, $module_id);
		if ($obj){
			$arrHooks = $obj->get_reset_pdh_hooks();
			if (is_array($arrHooks) && count($arrHooks) > 0){
				$this->pdh->register_hook_callback(array($obj, "reset"), $arrHooks);
			}
		}
	}
	
	private function register_global_hooks($path, $plugin, $module_id, $posi) {
		$obj = $this->get_module($path, $plugin, $posi, $module_id);
		$cwd = ($plugin) ?'plugins/'.$plugin.'/portal/hooks' :'portal/' . $path .'/hooks';
		if ($obj){
			$arrHooks = $obj->get_hooks();
			if (is_array($arrHooks) && count($arrHooks) > 0){
				foreach($arrHooks as $arrHook){
					$this->pgh->register($arrHook[0], $arrHook[1], $arrHook[0].'_hook', $cwd);
				}
			}
		}
	}


	// The Module Style
	private function get_module_content($path, $plugin, $module_id, $posi) {
		$perm = $this->check_visibility($module_id);
		
		if(!$perm || !$obj = $this->get_module($path, $plugin, $posi, $module_id)) return '';
		if($this->pdh->get('portal', 'collapsable', array($module_id)) == '1'){
			$this->jquery->Collapse('#portalbox'.$module_id);
		}
		
		$editbutton = '';
		if($this->isAdmin && $this->pdh->get('portal', 'settings', array($module_id))) {
			$editbutton = '<span class="portal_fe_edit" onclick="fe_portalsettings(\''.$module_id.'\')"><img src="'.$this->root_path.'images/global/edit.png" alt="'.$this->user->lang('portalplugin_settings').'" /></span>';
			$this->init_portalsettings();
		}
		$obj->set_id($module_id);
		$out = $obj->output();
		return 
'				<div id="portalbox'.$module_id.'" class="portalbox '.get_class($obj).'">
					<div class="portalbox_head">'.(($this->pdh->get('portal', 'collapsable', array($module_id)) == '1') ? '<span class="toggle_button">&nbsp;</span>' : '').'
						
						'.$editbutton.'
						<span class="center" id="txt'.$module_id.'">'.$obj->get_header().'</span>
					</div>
					<div class="portalbox_content">
						<div class="toggle_container">'.$out.'</div>
					</div>
				</div>';
	}
	
	public function init_portalsettings() {
		if(!isset($this->portal_settings)) {
			$this->portal_settings = true;
			$this->jquery->Dialog('fe_portalsettings', $this->user->lang('portalplugin_winname'), array('url'=>$this->root_path."admin/manage_portal.php".$this->SID."&simple_head=true&id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid', 'onclosejs'=> 'location.reload(true);'));
		}
	}

	public function install($path, $plugin='', $child = false) {
		$obj = $this->get_module($path, $plugin);
		if(!$obj) return false;
		$settings = ($obj->get_settings()) ? true : false;
		$this->pdh->put('portal', 'install', array($path, $plugin, $obj->get_data('name'), $settings, $obj->install(), $child));
	}
	
	public function uninstall($path, $plugin='', $id=0) {
		$obj = $this->get_module($path, $plugin);
		if(!$obj) return false;
		if($id) {
			$child = $this->pdh->get('portal', 'child', array($id));
			$this->pdh->put('portal', 'delete', array($id));
		} else
			$this->pdh->put('portal', 'delete', array($path, 'path'));
			
		$obj->uninstall();
		if ($id && !$child) unset($this->objs[$path]);
	}
	
	// Check if the Portal-Module-File is still available
	// Checks plugin if plugin bundled module
	public function check_file($path, $plugin='', $nodelete=false){
		$cwd = ($plugin) ? $this->root_path . 'plugins/'.$plugin.'/portal/'.$path.'_portal.class.php' : $this->root_path . 'portal/' . $path .'/'.$path.'_portal.class.php';
		// File not there -> Delete it from DB!
		if(!is_file($cwd) || ($plugin && !$this->pm->check($plugin, PLUGIN_INSTALLED))){
			if(!$nodelete) {
				$this->pdh->put('portal', 'delete', array($path, 'path'));
				$this->pdh->process_hook_queue();
			}
			return 0;
		} else {
			return $cwd;
		}
	}

	public function get_module($path, $plugin='', $position='', $module_id = 0) {
		if(!isset($this->objs[$path])) {
			$cwd = $this->check_file($path, $plugin);
			if($cwd) {
				include_once($cwd);
				if(!class_exists($path.'_portal')) $this->objs[$path] = false;
				else {
					$this->load_lang($path, $plugin);
					$class_name = $path.'_portal';
					$this->objs[$path] = registry::register($class_name, array($position, $module_id));
				}
			} else $this->objs[$path] = false;
		}
		return $this->objs[$path];
	}
	
	private function check_update($id, $path) {
		if(version_compare($this->objs[$path]->get_data('version'), $this->pdh->get('portal', 'version', array($id))) <= 0) return true;
		//update settings, contact, autor, version
		$this->pdh->put('portal', 'update', array($id, array(
			'settings' 	=> ($this->objs[$path]->get_settings()) ? 1 : 0,
			'contact'	=> $this->objs[$path]->get_data('contact'),
			'autor'		=> $this->objs[$path]->get_data('author'),
			'version'	=> $this->objs[$path]->get_data('version'))
		));
		//maybe they have something else to do
		if(method_exists($this->objs[$path], 'update_function')) $this->objs[$path]->update_function($this->pdh->get('portal', 'version', array($id)));
	}

	public function get_all_modules() {
		$this->pdh->process_hook_queue();
		$modules = $this->pdh->aget('portal', 'path', 0, array($this->pdh->get('portal', 'id_list')));
		foreach($modules as $id => $path) {
			if(!$this->get_module($path, $this->pdh->get('portal', 'plugin', array($id)))) continue;
			$this->check_update($id, $path);
		}

		//EQDKP PORTAL MODULES
		// Search for portal-modules and make sure they are registered
		if ($dir = @opendir($this->root_path . 'portal/') ){
			while ($d_plugin_code = @readdir($dir) ) {
				$cwd = $this->root_path . 'portal/' . $d_plugin_code;
				if (valid_folder($cwd)){
					if (in_array($d_plugin_code, $modules)){
						continue;
					} else {
						$this->install($d_plugin_code);
					}
				}
				unset($d_plugin_code, $cwd);
			} // readdir
		}

		// EQDKP PLUGIN PORTAL MODULES
		foreach ( $this->pm->get_plugins() as $plugin_code ){
			$plug_modules = $this->pm->get_plugin($plugin_code)->get_portal_modules();
			foreach($plug_modules as $module_name){
				if(!in_array($module_name, $modules)){
					$this->install($module_name, $plugin_code);
				}
			}
		}
		$this->pdh->process_hook_queue();
		return $this->objs;
	}
	
	public function load_lang($portal, $plugin=''){
		if($plugin || isset($this->lang_inits[$portal])) return true;
		
		$file = $this->root_path.'portal/'.$portal.'/language/';
		if(is_file($file.$this->user->lang_name.'.php')) {
			include($file.$this->user->lang_name.'.php');
			$this->user->add_lang($this->user->lang_name, $lang);
		} else {
			if(is_file($file.$this->config->get('default_lang').'.php')){
				include($file.$this->config->get('default_lang').'.php');
				$this->user->add_lang($this->config->get('default_lang'), $lang);
			} else {
				include($file.'english.php');
				$this->user->add_lang('english', $lang);
			}
		}
		$this->lang_inits[$portal] = true;
		return true;
	}
}

abstract class portal_generic extends gen_class {
	public static $shortcuts = array('db');

	protected $path		= '';
	protected $plugin	= '';
	protected $data		= array(
			'name'			=> '',
			'version'		=> '0.0',
			'author'		=> '',
			'contact'		=> '',
			'description'	=> ''
		);
	protected $positions= array('left1', 'left2', 'right');
	protected $settings	= array();
	protected $install	= array(
			'autoenable'		=> '0',
			'defaultposition'	=> 'left1',
			'defaultnumber'		=> '0',
			'visibility'		=> array(0),
			'collapsable'		=> '1'
		);
	protected $tables	= array();
	protected $sqls		= array();
	
	protected $position	= '';
	protected $id = '';
	protected $multiple = false;
	protected $exchangeModules = array();
	protected $reset_pdh_hooks = array();
	protected $hooks = array();
	
	public $LoadSettingsOnchangeVisibility = false;
	
	public function __construct($position='', $module_id = 0) {
		$this->position = $position;
		$this->id = $module_id;
	}

	public function get_data($type='') {
		if(!$type) return $this->data;
		if(isset($this->data[$type])) return $this->data[$type];
		return false;
	}
	
	public function get_multiple(){
		if(isset($this->multiple)) return $this->multiple;
		return false;
	}
	
	public function get_positions() {
		return $this->positions;
	}

	public function get_settings() {
		return (empty($this->settings)) ? false : $this->settings;
	}
	
	public function get_exchangeModules(){
		return $this->exchangeModules;
	}
	
	public function get_reset_pdh_hooks(){
		return $this->reset_pdh_hooks;
	}
	
	public function get_hooks(){
		return $this->hooks;
	}
	
	public function install() {
		if(!empty($this->sqls)) {
			foreach($this->sqls as $sql){
				$this->db->query($sql);
			}
		}
		//set default settings
		if($settings = $this->get_settings()) {
			foreach($settings as $sett) {
				if(!empty($sett['default'])) $this->config->set($sett['name'], $sett['default']);
			}
		}
		return $this->install;
	}
	
	public function uninstall() {
		if(!empty($this->tables)) {
			foreach($this->tables as $table) {
				$this->db->query("DROP TABLE IF EXISTS __".$table.";");
			}
		}
		return true;
	}
	
	public function get_header() {
		return (isset($this->header)) ? $this->header : registry::fetch('user')->lang($this->path, true);
	}
	
	public function update_function($old_version) {
		return true;
	}

	public function config($value){
		$child = registry::register('plus_datahandler')->get('portal', 'child', array($this->id));
		if (!$child) return registry::register('config')->get($value);
		return registry::register('config')->get($value.'_'.$this->id);
	}
	
	public function set_id($id){
		$this->id = $id;
	}

	abstract public function output();
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_portal', portal::$shortcuts);
?>