<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

// Check for required PHP Version and quit exec if requirements are not reached
if (!version_compare(phpversion(), '5.2.0', ">=")){
	die('
		<b>PHP 4 detected!</b><br/><br/>
		You need PHP5 running on you server! <br>
		PHP4 is no longer supported! Dont ask in the <a href="http://www.eqdkp-plus.com">EQdkp-Plus Forum</a> for a PHP4 Release!<br>
		Ask your Admin oder Hoster for a PHP5 update! If they do not update, you should leave your hoster!<br/><br/>
		<b>Resources:</b><br/>
			<a href="http://gophp5.org" target="_blank">goPHP5</a><br/>
			<a href="http://www.php.net" target="_blank">http://www.php.net</a>
	');
}

//eqdkp root path
if ( !isset($eqdkp_root_path) ){
	$eqdkp_root_path = './';
}

//set error options
$error_level = E_ALL & ~(E_NOTICE | E_STRICT);// | E_WARNING);
error_reporting ($error_level);
ini_set("display_errors", 0);

if(is_file($eqdkp_root_path . 'config.php') ){
	require_once($eqdkp_root_path . 'config.php');
}

// This check MUST be first. No class inits before that point.
if (!defined('EQDKP_INSTALLED') ){
	echo ("<script>window.location.href = '".$eqdkp_root_path."install/index.php';</script>");
	die();
}

// now, we have database information: init the file handler
require_once($eqdkp_root_path . 'core/file_handler/file_handler.class.php');
$ftp_or_file	= ($use_ftp) ? 'filehandler_ftp' : 'filehandler_php';
$pcache	= new file_handler(false, $ftp_or_file);

$svn_rev_file = $eqdkp_root_path . 'svn.rev' ;
if(is_file($svn_rev_file)){
	$svn_rev_handle = @fopen($svn_rev_file,"r");
	$svn_rev= "\$Rev:".@fread($svn_rev_handle, filesize($svn_rev_file));
}

// switch to userdefined error handler class
require_once($eqdkp_root_path.'core/plus_debug_logger.class.php');
$pdl = new plus_debug_logger();
set_error_handler(array($pdl,'myErrorHandler'), $error_level);
register_shutdown_function(array($pdl, "catch_fatals"));

// User Levels
define('ANONYMOUS',	-1);
define('USER',		0);

// Database Conncetors
$dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
require($eqdkp_root_path . 'gplcore/db/' . $dbms . '.php');
$db   = new $sql_db();
$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);

// Database Strict mode forcer... TO BE REMOVED
$db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");

// Require the statics
require_once($eqdkp_root_path . 'libraries/_statics/JSMin.php');
require_once($eqdkp_root_path . 'libraries/_statics/CSS.php');

// EQdkp class init...
include_once($eqdkp_root_path . 'core/config.class.php');
include_once($eqdkp_root_path . 'core/core.php');
include_once($eqdkp_root_path . 'gplcore/gplcore.php');
include_once($eqdkp_root_path . 'gplcore/functions.php');
include_once($eqdkp_root_path . 'core/core.functions.php');
require_once($eqdkp_root_path . 'core/time.class.php');
require_once($eqdkp_root_path . 'libraries/libraries.php');
include_once($eqdkp_root_path . 'core/timekeeper.class.php');
require_once($eqdkp_root_path . 'core/cache/cache.class.php');
require_once($eqdkp_root_path . 'core/urlreader.class.php');
require_once($eqdkp_root_path . 'core/comments.class.php');
require_once($eqdkp_root_path . 'core/language.class.php');
require_once($eqdkp_root_path . 'core/game.class.php');
require_once($eqdkp_root_path . 'core/plus.class.php');
include_once($eqdkp_root_path . 'gplcore/template.class.php');
include_once($eqdkp_root_path . 'core/chartools.class.php');
include_once($eqdkp_root_path . 'core/exchange.class.php');
include_once($eqdkp_root_path . 'gplcore/EQdkp_Plugin.class.php');
include_once($eqdkp_root_path . 'gplcore/eqdkp_plugins.php');
include_once($eqdkp_root_path . 'core/input.class.php');
include_once($eqdkp_root_path . 'gplcore/session.php');
require_once($eqdkp_root_path . 'core/portal.class.php');
require_once($eqdkp_root_path . 'core/acl.class.php');
require_once($eqdkp_root_path . 'core/logs.class.php');
require_once($eqdkp_root_path . 'core/backup.class.php');
require_once($eqdkp_root_path . 'core/upload.class.php');

// initiate the Core functionlity
RunGlobalsFix();
$settings		= new mmocms_config();
$core			= new mmocms_core($eqdkp_root_path);

// Fix the server_path if its required
if(!$core->config['server_path']){
	$core->config_set(
		array('server_path'	=> str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']))
	);
	redirect($_SERVER['PHP_SELF']);
}

// initiate the rest
$time			= new timehandler();
$libloader		= new libraries();
$timekeeper		= new timekeeper();
$cache			= (isset($core->config['pdc']['mode'])) ? 'cache_'.$core->config['pdc']['mode'] : 'cache_none';
$pdc			= new datacache($cache);
$xmltools		= new xmlTools();
$urlreader		= new urlreader();
$tpl			= new Template;
$bbcode			= new bbcode();
$CharTools		= new CharTools();
$pex			= new plus_exchange();
$uploader		= new mmocms_upload();
		
//If the User Class not exists extends from the default user class!
if(!class_exists('User')){
	class User extends UserSkel {}
}

$in = new Input();

if ($in->get('debug') > 0){
	define('DEBUG', $in->get('debug'));
}else if ( isset($debug) && $debug != 0 ){
	define('DEBUG', $debug);
}

$user			= new User;
$jquery			= new jquery();
$html			= new myHTML('pluskernel');
$plus			= new plus();
$backup			= new mmocms_backup;

// set the timezone
if($user->data['user_id'] > 0 && $user->data['user_timezone']){
	$time->setTimezone($user->data['user_timezone']);
}elseif($core->config['timezone']){
	$time->setTimezone($core->config['timezone']);
}else{
	$default_timezone = $time->get_serverTimezone();
	$time->setTimezone($default_timezone);
	if($default_timezone == 'GMT'){
		$core->message($user->lang['timezone_set_gmt']);
	}
}

//Debug
// second part of the Debug Part
// Only used if the debug mode didnt set in the config or via ?debug=
if (!defined('DEBUG') && isset($core->config['pk_debug'])){
	//No debug mode is set in the config File, so we check if in the eqdkpplus menu the debug mode isset
	$debug = intval($core->config['pk_debug']);
	define('DEBUG', $debug);
}
if (defined('DEBUG'))
{
	//only if the constant isset, we define the variable
	$debug = DEBUG;
}

$pdl->set_debug_level($debug);

// Bridge/CMS Support
if ($core->config['pk_bridge_cms_active'] == 1){
	// removed..
}

// Style can come from $_GET['style']
$style = ( isset($_GET['style']) ) ? sanitize($_GET['style']) : false;
// Language can come from $_GET['lang']
$get_lang = ( isset($_GET['lang']) ) ? sanitize($_GET['lang']) : false;

// ACL management
$acl = new acl();

// Start up the user/session management
$user->start();
$user->setup($get_lang, $style);

if ($user->data['user_id'] != ANONYMOUS && $user->data['rules'] != 1){
	if (preg_match("/register/i", $user->data['session_page']) == 0){
		redirect('register.php');
	}
}

//Maintenance mode redirect for non admins
if($core->config['pk_maintenance_mode'] && !$user->check_auth('a_', false)){
	redirect('maintenance/maintenance.php');
}

//Maintenance Modus for admins
require_once($eqdkp_root_path.'maintenance/includes/mmtaskmanager.class.php');
$task_manager	= new MMTaskManager();
$task_count		= $task_manager->get_task_count();

//redirect if there are new tasks
if(!isset($core->config['pk_known_task_count']) || $core->config['pk_known_task_count'] < $task_count){
	$core->config_set('pk_maintenance_mode', true);
	$core->config_set('pk_known_task_count', $task_count);
	redirect('maintenance/task_manager.php');
}
//redirect to maintenance if safe_mode is turned on or if there are any errors in pcache
if(($pcache->safe_mode AND $pcache->CheckWrite()) OR is_array($pcache->errors)) {
	$core->config_set('pk_maintenance_mode', true);
	redirect('maintenance/task_manager.php');
}

// set the custom UI for jquery.ui
$jquery->CustomUI($user->style['template_path']);
$pluslang	= new mmocms_language();
$game		= new Game();

//PLUS DATA HANDLER (PDH) & System
require_once($eqdkp_root_path.'core/data_handler/plus_datahandler.class.php');
$pdh = new plus_datahandler();
$cmapi		= new cmAPI();

/* Automatic Point Adjustments
include($eqdkp_root_path.'core/auto_point_adjustments.class.php');
$apa = new auto_point_adjustments();*/

// Start plugin management
$pm = new EQdkp_Plugin_Manager(true, DEBUG);
$pcomments = new comments();
$logs			= new mmocms_logs();

//now we've got the potential plugin pdh modules
//set eqdkp system type (default normal, possible values: epgp,..)
$eqdkp_layout = ( isset($core->config['eqdkp_layout']) ) ? $core->config['eqdkp_layout'] : 'normal';
$pdh->init_eqdkp_layout($eqdkp_layout);

// Set the locale
$cur_locale = $core->config['default_locale'];
setlocale(LC_ALL, $cur_locale);

// Populate the admin menu if we're in an admin page, they have admin permissions
if ( (defined('IN_ADMIN')) && (IN_ADMIN === true) ){
	if ( $user->check_auth('a_', false) ){
		include($eqdkp_root_path . 'admin/index.php');
	}
}

#initialize the Portal
$portal	  = new Portal();

// The third column on the start page...
if ($portal->THIRD_C == true){
	$tpl->assign_var('THIRD_C', true);
}

// System Message if user has no assigned members
if($pdh->get('member_connection', 'connection', array($user->data['user_id'])) < 1 && ($user->data['user_id'] != ANONYMOUS)){
	$core->message('<a href="'.$eqdkp_root_path.'characters.php">'.$user->lang['no_connected_char'].'</a>');
}

$timekeeper->handle_crons();

//EQdkp Tour
if ($user->check_auth('a_', false)){
	require_once($eqdkp_root_path . 'core/tour.class.php');
	$tour = new mmocms_tour();
	$tour->init();
}

$core->checkAdminTasks();
?>