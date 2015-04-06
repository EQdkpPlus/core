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

abstract class super_registry {
	public static $aliases = array(
		'pfh' 		=> 'file_handler',
		'pdl' 		=> 'plus_debug_logger',
		'config' 	=> 'config',
		'user'		=> '_user_',
		'db'		=> '_dbal_',
		'in'		=> 'input',
		'pdh'		=> 'plus_datahandler',
		'tpl'		=> 'template',
		'jquery'	=> 'jquery',
		'game'		=> 'game',
		'time'		=> 'timehandler',
		'pm'		=> 'plugin_manager',
		'pdc'		=> 'datacache',
		'env'		=> 'environment',
		'pw'		=> 'password',
		'pgh'		=> 'hooks', //Plus global hook
		'ntfy'		=> 'notifications',
	);
	//all classes in here must have their file named after its name, e.g. admin_index.class.php (keep them in alphabetical order, results in easy searching)
	protected static $locs = array(
		'admin_index'			=> 'admin/',
		'admin_tasks'			=> 'core/',
		'auth_db'				=> 'core/auth/',
		'datacache'				=> 'core/cache/',
		'plus_datahandler'		=> 'core/data_handler/',
		'file_handler'			=> 'core/file_handler/',
		// core-classes
		'acl'					=> 'core/',
		'auto_point_adjustments'=> 'core/',
		'article'				=> 'core/',
		'backup'				=> 'core/',
		'bridge'				=> 'core/',
		'comments'				=> 'core/',
		'config'				=> 'core/',
		'core'					=> 'core/',
		'embedly'				=> 'core/',
		'environment'			=> 'core/',
		'encrypt'				=> 'core/',
		'form'					=> 'core/',
		'game'					=> 'core/',
		'hooks'					=> 'core/',
		'html_leaderboard'		=> 'core/',
		'html_pdh_tag_table'	=> 'core/',
		'input'					=> 'core/',
		'logs'					=> 'core/',
		'notifications'			=> 'core/',
		'password'				=> 'core/',
		'plugin_manager'		=> 'core/',
		'plus_debug_logger'		=> 'core/',
		'plus_exchange'			=> 'core/',
		'portal'				=> 'core/',
		'repository'			=> 'core/',
		'routing'				=> 'core/',
		'socialplugins'			=> 'core/',
		'styles'				=> 'core/',
		'timehandler'			=> 'core/',
		'timekeeper'			=> 'core/',
		'tour'					=> 'core/',
		'uploader'				=> 'core/',
		'urlfetcher'			=> 'core/',
		'user'					=> 'core/',
		'xmltools'				=> 'core/',
		'zip'					=> 'core/',
		'infotooltip'			=> 'infotooltip/',
		'bbcode'				=> 'libraries/bbcode/',
		'MyMailer'				=> 'libraries/MyMailer/',
		'jquery'				=> 'libraries/jquery/',
		'template'				=> 'libraries/template/',
		'tinyMCE'				=> 'libraries/tinyMCE/',
		'mmtaskmanager'			=> 'maintenance/includes/',
	);
	public static $lite_igno = array('bridge');

	protected static $const = array();
	
	public static function init($root_path, $lite=false) {
		try{
			self::$const['scriptstart'] = microtime(true);
			self::$const['root_path'] = $root_path;
			self::$const['lite_mode'] = $lite;
			self::load_config();

			// switch to userdefined error handler class
			set_error_handler(array(registry::register('plus_debug_logger'),'myErrorHandler'), intval(ini_get("error_reporting")));
			register_shutdown_function(array(registry::register('plus_debug_logger'), "catch_fatals"));

			// User Levels
			define('ANONYMOUS',	-1);
			define('USER',		0);
			define('CRONJOB', -2);
			
			//DBAL
			if (self::$const['dbtype'] == "mysql") self::$const['dbtype'] = "mysqli";
			include_once(self::get_const('root_path') .'libraries/dbal/dbal.class.php');
			require_once(self::get_const('root_path') . 'libraries/dbal/' . self::$const['dbtype'] . '.dbal.class.php');
			self::$aliases['db'] = array('dbal_'.self::$const['dbtype'], array(array('open' => true)));
			
			registry::register('input');
			registry::register('config');
			self::set_debug_level();
			
			include_once(self::$const['root_path'].'core/core.functions.php');
			registry::register('environment');
			if(!registry::register('config')->get('server_path')) self::fix_server_path();
			self::$const['server_path'] = registry::register('config')->get('server_path');
			self::$const['controller_path'] = self::$const['server_path'].((!intval(registry::register('config')->get('seo_remove_index'))) ? 'index.php/' : '');
			self::$const['controller_path_plain'] = ((!intval(registry::register('config')->get('seo_remove_index'))) ? 'index.php/' : '');
						
			//Auth/User
			require(self::$const['root_path'] . 'core/auth.class.php');
			$auth_method = 'auth_'.((registry::register('config')->get('auth_method') != '') ? registry::register('config')->get('auth_method') : 'db');
			require_once(self::get_const('root_path') . 'core/auth/'. $auth_method . '.class.php');
			self::$aliases['user'] = $auth_method;
			
			registry::fetch('user')->start();
			
			registry::fetch('user')->setup(registry::register('input')->get('lang', ''), registry::register('input')->get('style', 0));
			self::set_timezone();
			define('USER_INITIALIZED', true);
			
			if (!defined('MAINTENANCE_MODE')){
				//Maintenance mode redirect for non admins
				if(registry::register('config')->get('pk_maintenance_mode') && !registry::fetch('user')->check_auth('a_', false) && !defined('NO_MMODE_REDIRECT')){
					redirect('maintenance/maintenance.php'.self::get_const('SID'));
				}

				//Maintenance Modus for admins
				$task_hash = registry::register('mmtaskmanager')->get_task_hash();
				
				//redirect if there are necessary tasks or errors from pfh				
				if(!registry::register('config')->get('pk_known_task_hash') || registry::register('config')->get('pk_known_task_hash') !== $task_hash){
					registry::register('config')->set('pk_known_task_hash', $task_hash);
					registry::register('mmtaskmanager')->init_tasks();
					if(registry::register('mmtaskmanager')->status['necessary_tasks']) {
						registry::register('config')->set('pk_maintenance_mode', true);
						if (!defined('NO_MMODE_REDIRECT')){
							redirect('maintenance/index.php'.self::get_const('SID'));
						}
					}
				}
				
				//activate and redirect to mmode if pfh-errors exist|
				if(count(registry::register('file_handler')->get_errors()) > 0) {
					registry::register('config')->set('pk_maintenance_mode', true);
					if (!defined('NO_MMODE_REDIRECT')){
						redirect('maintenance/index.php'.self::get_const('SID'));
					}
				}
				
				//check if version in config needs update (no db update existing, since no task necessary at this point)
				if(!defined('NO_MMODE_REDIRECT') && !registry::register('config')->get('pk_maintenance_mode')){
					if(version_compare(registry::register('config')->get('plus_version'), VERSION_INT, '<')) {
						registry::register('config')->set('plus_version', VERSION_INT);
					}
				}
			}
			
			// Set the locale
			setlocale(LC_ALL, registry::register('config')->get('default_locale'));
			setlocale(LC_NUMERIC, "en_EN", "en", "eng");

			// Populate the admin menu if we're in an admin page, they have admin permissions
			if(defined('IN_ADMIN') && IN_ADMIN === true){
				if(registry::fetch('user')->check_auth('a_', false)){
					registry::register('admin_index');
				}
			}
			
			if(!$lite) {
				//Get Info about mobile/desktop
				if(registry::register('input')->exists('toggleResponsive')){
					$arrAllowed = array('mobile', 'desktop');
					$strView = registry::register('input')->get('toggleResponsive');
					if(in_array($strView, $arrAllowed)){
						set_cookie('resp', $strView, time()+3600*24*30);
						registry::add_const('mobile_view', (($strView == 'mobile') ? true : false));
					}
				} elseif(registry::register('input')->getEQdkpCookie('resp', '') == 'desktop'){
					registry::add_const('mobile_view', false);
				} else registry::add_const('mobile_view', true);
				
				//initiate PDH
				$strPageLayout = ((registry::register('config')->get('eqdkp_layout') ) ? registry::register('config')->get('eqdkp_layout') : 'normal');
				$strPageLayout = (registry::register('config')->get('mobile_pagelayout') && strlen(registry::register('config')->get('mobile_pagelayout')) && registry::register('environment')->agent->mobile && registry::get_const('mobile_view')) ? registry::register('config')->get('mobile_pagelayout') : $strPageLayout;
				
				registry::register('plus_datahandler')->init_eqdkp_layout($strPageLayout);

				registry::register('portal');
			
				registry::register('timekeeper')->handle_crons();
			
				//EQdkp Tour
				if (registry::fetch('user')->check_auth('a_', false)){
					registry::register('tour')->init();
				}
				include_once(self::$const['root_path'].'core/page_generic.class.php');
				registry::register('jquery');
			}

			//Restore Permissions
			if (registry::register('input')->get('mode', '') == 'rstperms'){
				registry::fetch('user')->restore_permissions();
				redirect('', false, false, false);
			}
		} catch (DBALException $e){
			registry::register('plus_debug_logger')->catch_dbal_exception($e);
		}
		// maybe find a more elegant solution later on
		self::load_html_fields();
	}
	
	public static function class_exists($name) {
		return (isset(registry::$locs[$name]) || class_exists($name));
	}
	
	public static function get_const($name) {
		if(isset(self::$const[$name])) return self::$const[$name];
		return null;
	}
	
	public static function isset_const($name) {
		return isset(self::$const[$name]);
	}
	
	public static function add_const($name, $val) {
		self::$const[$name] = $val;
		return true;
	}
	
	public static function load_config($install=false) {
		if(is_file(self::$const['root_path'] . 'config.php')) require_once(self::$const['root_path'] . 'config.php');
		if (!defined('EQDKP_INSTALLED') && !$install ){
			$script_name = $_SERVER['SCRIPT_NAME'];
			
			if (substr($script_name, -9) == "index.php" && substr($script_name, -15) != "admin/index.php"){
				echo ("<script>window.location.href = '".str_replace("index.php", "", $script_name)."install/index.php';</script>");
			} else {
				echo ("<script>window.location.href = '".self::$const['root_path']."install/index.php';</script>");
			}
			die();
		}
		//copy config-data into our array
		$configs = array('ftphost', 'ftpport', 'ftpuser', 'ftppass', 'ftproot', 'use_ftp', 'encryptionKey', 'dbtype', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'table_prefix', 'debug');
		foreach($configs as $config) {
			if(isset($$config)) self::$const[$config] = $$config;
		}
	}
	
	public static function load_html_fields() {
		$path = self::$const['root_path'].'core/html/';
		$classes = sdir($path, '*.class.php');
		foreach($classes as $file) {
			include_once($path.$file);
		}
	}
	
	private static function set_debug_level() {
		if(!defined('DEBUG')) {
			if ( isset(self::$const['debug']) && self::$const['debug'] != 0 ){
				define('DEBUG', self::$const['debug']);
			}
		}
		if (!defined('DEBUG') && registry::register('config')->get('debug')){
			//No debug mode is set in the config File, so we check if in the eqdkpsettings menu the debug mode isset
			define('DEBUG', intval(registry::register('config')->get('debug')));
		} elseif (!defined('DEBUG')) define('DEBUG', 0);
		registry::register('plus_debug_logger')->set_debug_level(DEBUG);
	}
	
	private static function fix_server_path() {
		registry::register('config')->set('server_path', registry::register('environment')->server_path);
		redirect(registry::register('environment')->phpself);
	}
	
	private static function set_timezone() {
		if(!empty(registry::fetch('user')->data['user_id']) && registry::fetch('user')->data['user_timezone']) {
			registry::register('timehandler')->setTimezone(registry::fetch('user')->data['user_timezone']);
		} elseif(registry::register('config')->get('timezone')) {
			registry::register('timehandler')->setTimezone(registry::register('config')->get('timezone'));
		} else {
			$default_timezone = registry::register('timehandler')->get_serverTimezone();
			registry::register('timehandler')->setTimezone($default_timezone);
			if($default_timezone == 'GMT'){
				registry::register('config')->message(registry::fetch('user')->lang('timezone_set_gmt'));
			}
		}
	}
	
}

?>