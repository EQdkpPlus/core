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
	
	private $output		= array('left' => '', 'right' => '', 'middle' => '', 'bottom' => '');
	private $lang_inits	= array();
	private $objs		= array();
	private $enabled_modules = array();

	public function __construct() {
		// init the variables...
		$this->isAdmin			= $this->user->check_auth('a_config_man', false);
		$this->enabled_modules	= $this->pdh->maget('portal', array('path', 'plugin'), 0, array($this->pdh->get('portal', 'id_list', array())));
		if(is_array($this->enabled_modules)) {
			foreach($this->enabled_modules as $module_id => $data) {
				//Register pdh callbacks
				$this->register_pdh_callback($data['path'], $data['plugin'], $module_id);
				//Register global hooks
				$this->register_global_hooks($data['path'], $data['plugin'], $module_id);
			}
			$this->objs = array();
		}
	}
	
	public function get_module_external($intModuleID){
		$vis = $this->pdh->get('portal', 'visibility', array($intModuleID));
		if(in_array(999999999, $vis)){
			$data = $this->pdh->maget('portal', array('path', 'plugin'), 0, array(array($intModuleID)));
			if (isset($data[$intModuleID])) $data = $data[$intModuleID]; else return "";
			$position = $this->in->get('position', 'left');
			$wide = ($this->in->get('wide', 0));
			
			if(!$obj = $this->get_module($data['path'], $data['plugin'], $position, $intModuleID, $wide)) return '';

			$obj->set_id($intModuleID);
			$moduleout = $obj->output();

			$jsOut = '<div class="module_script"><script type="text/javascript">';
			
			$headerJS = $this->tpl->get_header_js();
			$arrSplitted = explode('"hex6"});', $headerJS);
			unset($arrSplitted[0]);
			$headerJS = "jQuery(document).ready(function($) {".str_replace("$", "jQuery",  implode('"hex6"});', $arrSplitted));
			
			$headerJS = str_replace("$", "jQuery", $headerJS);
			
			$jsOut .= $headerJS.'</script></div>';
			
			
			$cssOut = '<div class="external_module"><style>';
			$cssFile = $this->tpl->get_combined_css();
			
			$cssOut .= " ".file_get_contents($cssFile);
			$cssOut .= '</style>';
			
			return
			$jsOut.$cssOut.'<div id="portalbox'.$module_id.'" class="portalbox '.get_class($obj).'">'.(($this->in->get('header', 1)) ?
					'<div class="portalbox_head">
						<span class="center" id="txt'.$module_id.'">'.$obj->get_header().'</span>
					</div>' : ''
					).'<div class="portalbox_content">
						<div class="toggle_container">'.str_replace($this->server_path, $this->env->link, $moduleout).'</div>
					</div>
				</div></div>';

		} else {
			return "You don't have the required permission to view this module.";
			
		}
	}
	
	public function module_output($intPortalLayout) {
		//Get own Blocks
		$arrBlocks 		= $this->pdh->get('portal_layouts', 'blocks', array($intPortalLayout));
		$arrUsedModules = $this->pdh->get('portal_layouts', 'modules', array($intPortalLayout));
		$this->output = array('left' => '', 'right' => '', 'middle' => '', 'bottom' => '');
		foreach($arrBlocks as $strBlockID){
			$this->output[$strBlockID] = '';
		}
		
		$this->objs = array();
		
		//Don't show modules in Admin Area
		if (!defined('IN_ADMIN')){		
			foreach($arrUsedModules as $strBlockID => $arrModules){
				foreach($arrModules as $intModuleID){
					$data = $this->enabled_modules[$intModuleID];
					$blnWideContent = false;
					if (strpos($strBlockID, 'block') === 0) {
						$blnWideContent = $this->pdh->get('portal_blocks', 'wide_content', array(str_replace('block', '', $strBlockID)));
					} elseif($strBlockID == 'middle' || $strBlockID == 'bottom'){
						$blnWideContent = true;
					}
					$this->output[$strBlockID] .= $this->get_module_content($data['path'], $data['plugin'], $intModuleID, $strBlockID, $blnWideContent);
				}	
			}
		}
		$this->assign_to_tpl();
	}
	
	private function assign_to_tpl() {
		foreach($this->output as $posi => $out) {
			$this->tpl->assign_vars(array(
				'PORTAL_'.strtoupper($posi) => $out,
				'S_PORTAL_'.strtoupper($posi) => strlen($out),
			));
		}
	}
	
	// To avoid reloading after saving on manage_portal
	public function reset() {
		$this->output = array();
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
	
	private function register_pdh_callback($path, $plugin, $module_id) {
		$obj = $this->get_module($path, $plugin, 'left', $module_id);
		if ($obj){
			$arrHooks = $obj->get_reset_pdh_hooks();
			if (is_array($arrHooks) && count($arrHooks) > 0){
				$this->pdh->register_hook_callback(array($obj, "reset"), $arrHooks);
			}
		}
	}
	
	private function register_global_hooks($path, $plugin, $module_id) {
		$obj = $this->get_module($path, $plugin, 'left', $module_id);
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
	private function get_module_content($path, $plugin, $module_id, $posi, $wideContent = false) {
		$perm = $this->check_visibility($module_id);
		
		if(!$perm || !$obj = $this->get_module($path, $plugin, $posi, $module_id, $wideContent)) return '';
		if($this->pdh->get('portal', 'collapsable', array($module_id)) == '1'){
			$this->jquery->Collapse('#portalbox'.$module_id);
		}
		
		$editbutton = '';
		if($this->isAdmin) {
			$editbutton = '<span class="portal_fe_edit" onclick="fe_portalsettings(\''.$module_id.'\')"><img src="'.$this->server_path.'images/global/edit.png" alt="'.$this->user->lang('portalplugin_settings').'" /></span>';
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
			$this->jquery->Dialog('fe_portalsettings', $this->user->lang('portalplugin_winname'), array('url'=>$this->server_path."admin/manage_portal.php".$this->SID."&simple_head=true&id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid', 'onclosejs'=> 'location.reload(true);'));
		}
	}

	public function install($path, $plugin='', $child = false) {
		$obj = $this->get_module($path, $plugin);
		if(!$obj) return false;
		$this->pdh->put('portal', 'install', array($path, $plugin, $obj->get_data('name'), $obj->install(), $child));
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

	public function get_module($path, $plugin='', $position='', $module_id = 0, $wideContent = false) {
		if(!isset($this->objs[$path])) {
			$cwd = $this->check_file($path, $plugin);
			if($cwd) {
				include_once($cwd);
				if(!class_exists($path.'_portal')) $this->objs[$path] = false;
				else {
					$this->load_lang($path, $plugin);
					$class_name = $path.'_portal';
					$this->objs[$path] = registry::register($class_name, array($position, $module_id, $wideContent));
				}
			} else $this->objs[$path] = false;
		}
		return $this->objs[$path];
	}
	
	private function check_update($id, $path) {
		if(version_compare($this->objs[$path]->get_data('version'), $this->pdh->get('portal', 'version', array($id))) <= 0) return true;
		//update settings, contact, autor, version
		$this->pdh->put('portal', 'update', array($id, array(
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
	public static $shortcuts = array('db', 'config');

	protected $path		= '';
	protected $plugin	= '';
	protected $data		= array(
			'name'			=> '',
			'version'		=> '0.0',
			'author'		=> '',
			'contact'		=> '',
			'description'	=> ''
		);
	protected $settings	= array();
	protected $install	= array(
			'autoenable'		=> '0',
			'defaultposition'	=> 'left',
			'defaultnumber'		=> '0',
			'visibility'		=> array(0),
			'collapsable'		=> '1'
		);
	protected $tables	= array();
	protected $sqls		= array();
	
	protected $position	= '';
	protected $id = '';
	protected $wide_content = false;
	protected $multiple = false;
	protected $exchangeModules = array();
	protected $reset_pdh_hooks = array();
	protected $hooks = array();
	protected $css = array('files' => array(), 'content' => '');
	
	public $LoadSettingsOnchangeVisibility = false;
	
	public function __construct($position='', $module_id = 0, $wideContent = false) {
		$this->position = $position;
		$this->id = $module_id;
		$this->wide_content = $wideContent;
	}

	public function get_data($type='') {
		if(!$type) return $this->data;
		if(isset($this->data[$type])) return $this->data[$type];
		return false;
	}
	
	public function get_css(){
		return $this->css;
	}
	
	public function get_multiple(){
		if(isset($this->multiple)) return $this->multiple;
		return false;
	}

	public function get_settings($state) {
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
		if($settings = $this->get_settings('fetch_new')) {
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
	
	public function set_config($key, $value){
		$child = registry::register('plus_datahandler')->get('portal', 'child', array($this->id));
		if (!$child) return registry::register('config')->set($key, $value);
		return registry::register('config')->set($key.'_'.$this->id, $value);
	}
	
	public function del_config($key){
		$child = registry::register('plus_datahandler')->get('portal', 'child', array($this->id));
		if (!$child) return registry::register('config')->del($key);
		return registry::register('config')->del($key.'_'.$this->id);
	}
	
	public function set_id($id){
		$this->id = $id;
	}
	
	abstract public function output();
}
?>