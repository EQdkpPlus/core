<?php
/******************************
 * EQdkp
 * Copyright 2009
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * common_lite.php
 * begin: 2009
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

//Filter strict errors
//set error options
$error_level = E_ALL & ~(E_NOTICE | E_STRICT);
error_reporting ($error_level);
ini_set("display_errors", 0);
//error_reporting (E_ALL ^ E_NOTICE);

// Disable magic quotes and add slashes to global arrays
if ( get_magic_quotes_gpc() == 0 )
{
    $_GET = slash_global_data($_GET);
    $_POST = slash_global_data($_POST);
    $_COOKIE = slash_global_data($_COOKIE);
}

if ( !isset($eqdkp_root_path) )
{
    $eqdkp_root_path = './../';
}

$conf_file = $eqdkp_root_path . 'config.php' ;

if(!is_file($conf_file) ){
  die("There is no conf file, please use the installer instead!");
}
else{
	require_once($eqdkp_root_path . 'config.php');
}

//show all errors in maintenance mode
define('DEBUG', 3);
//template
require_once($eqdkp_root_path . 'core/file_handler/file_handler.class.php');
$pcache	= new file_handler;


// auf die benutzerdefinierte Fehlerbehandlung umstellen
require_once($eqdkp_root_path.'core/plus_debug_logger.class.php');
$pdl = new plus_debug_logger();
set_error_handler(array($pdl,'myErrorHandler'), $error_level);
register_shutdown_function(array($pdl, "catch_fatals"));

// User Levels
define('ANONYMOUS', -1);
define('USER',       0);

// Database Conncetors
$dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
require($eqdkp_root_path . 'gplcore/db/' . $dbms . '.php');
$db = new $sql_db();
$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);

// Config
require_once($eqdkp_root_path . 'core/config.class.php');
$settings		= new mmocms_config();

include_once($eqdkp_root_path.'gplcore/functions.php');
include_once($eqdkp_root_path.'core/core.functions.php');
RunGlobalsFix();

include_once($eqdkp_root_path . 'core/input.class.php');
$in	= new Input();

include_once('./includes/eqdkp_config_lite.class.php');
$core = new eqdkp_config_lite();

// Fix the server_path if its required
if(!$core->config['server_path']){
	$core->config_set(
		array('server_path'	=> str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']))
	);
	redirect($_SERVER['PHP_SELF']);
}

// ACL management
require_once($eqdkp_root_path.'core/acl.class.php');
$acl = new acl();

include_once('./includes/session_lite.class.php');

//If the User Class not exists extends from the default user class!
if(!class_exists(User)){
	class User extends UserSkel {}
}
$user = new User();
$user->start();
$user->setup();

//$user->check_auth('a_', false);

// Set the locale
$cur_locale = $core->config['default_locale'];
setlocale(LC_ALL, $cur_locale);//Maintenance Modus for admins

require_once($eqdkp_root_path.'maintenance/includes/mmtaskmanager.class.php');
$task_manager = new MMTaskManager();

$task_count = $task_manager->get_task_count();

//redirect if there are new tasks
if(!isset($core->config['pk_known_task_count']) || $core->config['pk_known_task_count'] < $task_count){
  $core->config_set('pk_maintenance_mode', true);
  $core->config_set('pk_known_task_count', $task_count);
}

#================================

/**
* Applies addslashes() to the provided data
*
* @param $data Array of data or a single string
* @return mixed Array or string of data
*/
function slash_global_data($data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = ( is_array($v) ) ? slash_global_data($v) : addslashes($v);
        }
    }
    return $data;
}
?>