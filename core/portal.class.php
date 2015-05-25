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
class portal extends gen_class {

	private $output		= array('left' => '', 'right' => '', 'middle' => '', 'bottom' => '');
	private $lang_inits	= array();
	private $objs		= array();
	private $loaded 	= array();
	
	protected $apiLevel		= 20;	//API Level of Portal Class

	public function __construct() {
		$this->pdl->register_type('portal');
		
		// init the variables...
		$this->isAdmin			= $this->user->check_auth('a_config_man', false);
		// get a list of all potentially used modules
		$layouts = $this->pdh->get('portal_layouts', 'id_list');
		$module_ids = array();
		foreach($layouts as $layout_id) {
			$modules = $this->pdh->get('portal_layouts', 'modules', array($layout_id));
			foreach($modules as $position => $module) {
				foreach($module as $mod_id) {
					$module_ids[$mod_id] = $mod_id;
				}
			}
		}
		if(is_array($module_ids)) {
			foreach($module_ids as $module_id) {
				//Register pdh callbacks
				$this->register_pdh_callback($module_id);
				//Register global hooks
				$this->register_global_hooks($module_id);
			}
		}
		
	}
	
	public function get_module_external($intModuleID){

		if(in_array(PMOD_VIS_EXT, $this->config->get('visibility', 'pmod_'.$intModuleID)) || in_array(0, $this->config->get('visibility', 'pmod_'.$intModuleID))) {
			$position = $this->in->get('position', 'left');
			$wide = ($this->in->get('wide', 0));
			
			if(!$obj = $this->get_module($intModuleID, $position, $wide)) return '';

			$moduleout = $obj->output();

			$jsOut = '<div class="module_script"><script type="text/javascript">';
			
			$headerJS = $this->tpl->get_header_js();
		
			$headerJS = "jQuery(document).ready(function($) {".str_replace("$", "jQuery",  $headerJS).'});';
			
			$headerJS = str_replace("$", "jQuery", $headerJS);
			
			$jsOut .= $headerJS.'</script></div>';
			
			
			$cssOut = '<div class="external_module"><style>';
			$cssFile = $this->tpl->get_combined_css();
			
			$cssOut .= " ".file_get_contents($cssFile);
			$cssOut .= '</style>';
			
			$out = $jsOut.$cssOut.'<div id="portalbox'.$module_id.'" class="portalbox '.get_class($obj).'">'.(($this->in->get('header', 1)) ?
					'<div class="portalbox_head">
						<span class="center" id="txt'.$module_id.'">'.$obj->get_header().'</span>
					</div>' : ''
					).'<div class="portalbox_content">
						<div class="toggle_container">';

			$out .= preg_replace("/(\"|')(".preg_quote($this->server_path, "/").")/", "$1".$this->env->link, $moduleout); 
			
			$out .='</div>
					</div>
				</div></div>';
			
			
			return $out;

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
		
		// reset object-cache s.t. they get correctly initialised with position etc.
		$this->objs = array();
		
		//Don't show modules in Admin Area
		if (!defined('IN_ADMIN')){		
			foreach($arrUsedModules as $strBlockID => $arrModules){
				foreach($arrModules as $intModuleID){
					$blnWideContent = false;
					if (strpos($strBlockID, 'block') === 0) {
						$blnWideContent = $this->pdh->get('portal_blocks', 'wide_content', array(str_replace('block', '', $strBlockID)));
					} elseif($strBlockID == 'middle' || $strBlockID == 'bottom'){
						$blnWideContent = true;
					}
					$this->output[$strBlockID] .= $this->get_module_content($intModuleID, $strBlockID, $blnWideContent);
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
	
	// to reset cache of module
	public function reset_portal($path, $plugin='') {
		if(!$this->load_module($path, $plugin)) return;
		$portal = $path.'_portal';
		if(method_exists($portal, 'reset')) $portal::reset();
	}
	
	public function check_visibility($module_id){
		$vis = $this->config->get('visibility', 'pmod_'.$module_id);
		return ($this->user->check_group($vis, false) || (isset($vis[0]) && intval($vis[0]) == 0));
	}
	
	private function register_pdh_callback($module_id) {
		if ($obj = $this->get_module($module_id)){
			$arrHooks = $obj->get_reset_pdh_hooks();
			if (is_array($arrHooks) && count($arrHooks) > 0){
				$this->pdh->register_hook_callback(array($obj, "reset"), $arrHooks);
			}
		}
	}
	
	private function register_global_hooks($module_id) {
		$path = $this->pdh->get('portal', 'path', array($module_id));
		$plugin = $this->pdh->get('portal', 'plugin', array($module_id));
		$cwd = ($plugin) ? 'plugins/'.$plugin.'/portal/hooks' : 'portal/'.$path.'/hooks';
		if ($obj = $this->get_module($module_id)){
			$arrHooks = $obj->get_hooks();
			if (is_array($arrHooks) && count($arrHooks) > 0){
				foreach($arrHooks as $arrHook){
					$this->pgh->register($arrHook[0], $arrHook[1], $arrHook[0].'_hook', $cwd, array($module_id));
				}
			}
		}
	}


	// The Module Style
	private function get_module_content($module_id, $posi, $wideContent = false) {
		$perm = $this->check_visibility($module_id);
		
		if(!$perm || !$obj = $this->get_module($module_id, $posi, $wideContent)) return '';
		if($this->config->get('collapsable', 'pmod_'.$module_id) == '1'){
			$this->jquery->Collapse('#portalbox'.$module_id);
		}
		
		$editbutton = '';
		if($this->isAdmin) {
			$editbutton = '<span class="portal_fe_edit" onclick="fe_portalsettings(\''.$module_id.'\')"><i class="fa fa-wrench hand" title="'.$this->user->lang('portalplugin_settings').'"></i></span>';
			$this->init_portalsettings();
		}
		$out = $this->handle_output($obj);
		return 
'				<div id="portalbox'.$module_id.'" class="portalbox '.get_class($obj).'">
					<div class="portalbox_head">'.(($this->config->get('collapsable', 'pmod_'.$module_id) == '1') ? '<span class="toggle_button">&nbsp;</span>' : '').'
						
						'.$editbutton.'
						<span class="center" id="txt'.$module_id.'">'.$obj->get_header().'</span>
					</div>
					<div class="portalbox_content">
						<div class="toggle_container">'.$out.'</div>
					</div>
				</div>';
	}
	
	private function handle_output($obj){
		$out = "";
		if($obj->template_file != ""){
			$portalname = str_replace("_portal", "", get_class($obj));
			
			if(is_file($this->root_path.'templates/'.$this->user->style['template_path'].'/portal/'.$portalname.'/'.$obj->template_file)){
				$strContent = file_get_contents($this->root_path.'templates/'.$this->user->style['template_path'].'/portal/'.$portalname.'/'.$obj->template_file);
			} elseif(is_file($this->root_path.'portal/'.$portalname.'/templates/'.$obj->template_file)){
				$strContent = file_get_contents($this->root_path.'portal/'.$portalname.'/templates/'.$obj->template_file);
			} else {
				return "Error: Cannot load template file.";
			}
			
			if($strContent != ""){
				$obj->output();
				$out = $this->tpl->compileString($strContent);
			}
			
		} else {
			$out = $obj->output();
		}
		return $out;
	}
	
	public function init_portalsettings() {
		if(!isset($this->portal_settings)) {
			$this->portal_settings = true;
			$this->jquery->Dialog('fe_portalsettings', $this->user->lang('portalplugin_winname'), array('url'=>$this->server_path."admin/manage_portal.php".$this->SID."&simple_head=true&id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid', 'onclosejs'=> 'location.reload(true);'));
		}
	}
	
	public function get_module($module_id, $position='', $wideContent = false, $force=false) {
		if(!isset($this->objs[$module_id]) || $force) {
			$path = $this->pdh->get('portal', 'path', array($module_id));
			$plugin = $this->pdh->get('portal', 'plugin', array($module_id));
			if($this->load_module($path, $plugin)) {
				$this->load_lang($path, $plugin);
				$class_name = $path.'_portal';
				$this->objs[$module_id] = registry::register($class_name, array($module_id, $position, $wideContent));
			} else $this->objs[$module_id] = false;
		}
		return $this->objs[$module_id];
	}

	public function install($path, $plugin='', $child = false) {
		if(!$this->load_module($path, $plugin)) return false;
		$class_name = $path.'_portal';
		$inst = $class_name::install($child);
		if(!$id = $this->pdh->put('portal', 'install', array($path, $plugin, $class_name::get_data('name'), $class_name::get_data('version'), $child))) return false;
		
		// set defaults for collapsable and visibility
		if(!isset($inst['visibility'])) $inst['visibility'] = array(0);
		$this->config->set('visibility', $inst['visibility'], 'pmod_'.$id);
		if(!empty($inst['collapsable'])) $this->config->set('collapsable', $inst['collapsable'], 'pmod_'.$id);
		
		// set other default settings
		$obj = $this->get_module($id);
		if($obj && $settings = $obj->get_settings('fetch_new')) {
			foreach($settings as $name => $sett) {
				if(!empty($sett['default'])) $this->config->set($name, $sett['default'], 'pmod_'.$id);
			}
		}
	}
	
	public function uninstall($path, $plugin='') {
		if(!$this->load_module($path, $plugin)) return false;
		$this->pdh->put('portal', 'delete', array($path, 'path'));
		$class_name = $path.'_portal';
		$class_name::uninstall();
		unset($this->objs[$path]);
	}
	
	public function remove($path){
		$this->uninstall($path);
		
		$path = preg_replace("/[^a-zA-Z0-9-_]/", "", $path);
		if($path == "") return false;
		$this->pfh->Delete($this->root_path.'portal/'.$path.'/');
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

	public function load_module($path, $plugin) {
		if(!isset($this->loaded[$path])) {
			if($cwd = $this->check_file($path, $plugin)) {
				include_once($cwd);
				$this->loaded[$path] = class_exists($path.'_portal') ? true : false;
				
				//Check API Level
				$classname = $path.'_portal';
				$intAPILevel = $classname::getApiLevel();
				if (!$intAPILevel || $intAPILevel < $this->apiLevel-2){
					$this->pdl->log('portal', 'The Portal API Level of the Portal Module \''.$path.'\' is too old ('.$intAPILevel.' vs. '.$this->apiLevel.')');
					$this->loaded[$path] = false;
				} elseif ($intAPILevel < $this->apiLevel) {
					$this->pdl->log('portal', 'The Portal API Level of the Portal Module \''.$path.'\' should be updated ('.$intAPILevel.' vs. '.$this->apiLevel.')');
					$this->loaded[$path] = false;
				}
			} else $this->loaded[$path] = false;
		}
		return $this->loaded[$path];
	}
	
	private function check_update($id, $path) {
		$class_name = $path.'_portal';
		if(version_compare($class_name::get_data('version'), $this->pdh->get('portal', 'version', array($id))) <= 0) return false;
		//update settings, contact, autor, version
		$this->pdh->put('portal', 'update', array($id, array('version' => $class_name::get_data('version'))));
		//maybe they have something else to do
		if(method_exists($this->objs[$id], 'update_function')) $this->objs[$id]->update_function($this->pdh->get('portal', 'version', array($id)));
		$this->pdh->process_hook_queue();
		return true;
	}

	public function get_all_modules() {
		$this->pdh->process_hook_queue();
		$modules = $this->pdh->aget('portal', 'path', 0, array($this->pdh->get('portal', 'id_list')));

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
		// get a full clean list of all modules
		$this->objs = array();
		$this->pdh->process_hook_queue();
		$modules = $this->pdh->aget('portal', 'path', 0, array($this->pdh->get('portal', 'id_list')));
		foreach($modules as $id => $path) {
			if(!$this->get_module($id)) continue;
			$blnResult = $this->check_update($id, $path);
			if ($blnResult) $this->get_module($id, "", false, true);
		}
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
	
	public function get_module_config($moduleid, $value){
		return $this->config->get($value, 'pmod_'.$moduleid);
	}
}

abstract class portal_generic extends gen_class {
	protected static $path		= '';
	protected static $plugin	= '';
	protected static $data		= array(
		'name'			=> '',
		'version'		=> '0.0',
		'author'		=> '',
		'contact'		=> '',
		'description'	=> '',
		'icon'			=> '',
		'exchangeMod'	=> array(),
		'multiple'		=> false,
		'reload_on_vis'	=> false,
		'positions'		=> array('left'),
		'lang_prefix'	=> ''
	);
	// data required for installation of portal module
	protected static $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'left',
		'defaultnumber'		=> '0',
		'visibility'		=> array(0),
		'collapsable'		=> '1'
	);
	protected static $tables	= array();
	protected static $sqls		= array();
	
	protected $position			= '';
	protected $id 				= 0;
	protected $wide_content		= false;
	protected $settings			= array();
	protected $reset_pdh_hooks 	= array();
	protected $hooks 			= array();
	public $template_file	= '';
	
	final public function __construct($module_id, $position='', $wideContent = false) {
		$this->position = $position;
		$this->id = $module_id;
		$this->wide_content = $wideContent;
	}
	
	public static function getApiLevel(){
		return (isset(static::$apiLevel)) ? static::$apiLevel : 0;
	}

	final public static function get_data($type='') {
		// add default data to the data array
		$data = array_merge(self::$data, static::$data);
		if(!$type) return $data;
		if(isset($data[$type])) return $data[$type];
		return false;
	}

	public function get_settings($state) {
		return (empty($this->settings)) ? false : $this->settings;
	}
	
	public function get_reset_pdh_hooks(){
		return $this->reset_pdh_hooks;
	}
	
	public function get_hooks(){
		return $this->hooks;
	}
	
	public static function install($child=false) {
		if(!empty(static::$sqls) && !$child) {
			foreach(static::$sqls as $sql){
				register('db')->query($sql);
			}
		}
		return static::$install;
	}
	
	public static function uninstall() {
		if(!empty(static::$tables)) {
			foreach(static::$tables as $table) {
				register('db')->query("DROP TABLE IF EXISTS __".$table.";");
			}
		}
		return true;
	}
	
	public function get_header() {
		$customHeader = $this->config('custom_header');
		$header = ($customHeader && strlen($customHeader)) ? $customHeader : ((isset($this->header)) ? $this->header : $this->user->lang(static::$path, true, false));
		return $header;
	}
	
	public function update_function($old_version) {
		return true;
	}

	public function config($value){
		return $this->config->get($value, 'pmod_'.$this->id);
	}
	
	public function set_config($key, $value){
		return $this->config->set($key, $value, 'pmod_'.$this->id);
	}
	
	public function del_config($key){
		return $this->config->del($key, 'pmod_'.$this->id);
	}
	
	public static function reset() {
		register('pdc')->del_prefix('portal.module.'.static::$path);
	}
	
	abstract public function output();
}
?>