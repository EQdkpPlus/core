<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * common.php
 * Began: Tue December 17 2002
 *
 * $Id$
 *
 ******************************/

RunGlobalsFix();
if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

setcookie('herefirst', 'yes');

error_reporting (E_ALL ^ E_NOTICE);

// Disable magic quotes and add slashes to global arrays
set_magic_quotes_runtime(0);
if ( get_magic_quotes_gpc() == 0 )
{
    $_GET = slash_global_data($_GET);
    $_POST = slash_global_data($_POST);
    $_COOKIE = slash_global_data($_COOKIE);
}

// Default the site-wide variables
$gen_simple_header = false;
if ( !isset($eqdkp_root_path) )
{
    $eqdkp_root_path = './';
}

$conf_file = $eqdkp_root_path . 'config.php' ;
if(!is_file($conf_file) )
{
	fopen($conf_file,"w+");
	if(!is_file($conf_file) )
	{
		die('Error: could not locate configuration file config.php in the Root Folder. Create an empty config.php or set the install folder to chmod 777.');
	}
}
else
{
	require_once($eqdkp_root_path . 'config.php');
}

#Rev
$svn_rev_file = $eqdkp_root_path . 'svn.rev' ;
if(is_file($svn_rev_file))
{
	$svn_rev_handle = @fopen($svn_rev_file,"r");
	$svn_rev= "\$Rev:".@fread($svn_rev_handle, filesize($svn_rev_file));
}

if ( !class_exists('soapclient') ) {
	include_once($eqdkp_root_path . 'includes/nusoap.php');
}

if ( !defined('EQDKP_INSTALLED') )
{
    header('Location: ' . $eqdkp_root_path . 'install/index.php');
}

// Constants
define('EQDKP_VERSION', '1.3.2');

//Eqdkp PLUS CONSTANTS
require_once($eqdkp_root_path . '/pluskernel/plusversion.php');
define('EQDKPPLUS_AUTHOR', 'Corgan & Eqdkp Plus Project Team');
define('EQDKPPLUS_VCHECKURL', 'http://vcheck.eqdkp-plus.com');
$eqdkpplus_vcontrol = array('pluskernel','charmanager', 'raidplan', 'itemspecials', 'raidbanker');

define('NO_CACHE', true);
// Debug level [0 = Off / 1 = Render time, Query count / 2 = 1 + Show queries]
// Fixed in 1.3 so it works from config.php and obeys URL parsing of ?debug=2

if ( isset($debug) && $debug == 0 ) {

   $debug = ( isset($_GET['debug']) ) ? intval($_GET['debug']) : 0;
}

define('DEBUG', $debug);

// User Levels
define('ANONYMOUS', -1);
define('USER',       0);

// User activation
define('USER_ACTIVATION_NONE',  0);
define('USER_ACTIVATION_SELF',  1);
define('USER_ACTIVATION_ADMIN', 2);

// URI Parameters
define('URI_ADJUSTMENT', 'a');
define('URI_EVENT',      'e');
define('URI_ITEM',       'i');
define('URI_LOG',        'l');
define('URI_NAME',       'name');
define('URI_NEWS',       'n');
define('URI_ORDER',      'o');
define('URI_PAGE',       'p');
define('URI_RAID',       'r');
define('URI_SESSION',    's');

// Database Table names
define('ADJUSTMENTS_TABLE',    $table_prefix . 'adjustments');
define('ADMINS_TABLE',         $table_prefix . 'admins');
define('AUTH_OPTIONS_TABLE',   $table_prefix . 'auth_options');
define('AUTH_USERS_TABLE',     $table_prefix . 'auth_users');
define('CONFIG_TABLE',         $table_prefix . 'config');
define('EVENTS_TABLE',         $table_prefix . 'events');
define('ITEMS_TABLE',          $table_prefix . 'items');
define('LOGS_TABLE',           $table_prefix . 'logs');
define('MEMBERS_TABLE',        $table_prefix . 'members');
define('SOAP_TABLE',	       $table_prefix . 'soap_auth');
define('MEMBER_RANKS_TABLE',   $table_prefix . 'member_ranks');
define('MEMBER_USER_TABLE',    $table_prefix . 'member_user');
define('NEWS_TABLE',           $table_prefix . 'news');
define('PLUGINS_TABLE',        $table_prefix . 'plugins');
define('RAID_ATTENDEES_TABLE', $table_prefix . 'raid_attendees');
define('RAIDS_TABLE',          $table_prefix . 'raids');
define('SESSIONS_TABLE',       $table_prefix . 'sessions');
define('STYLES_CONFIG_TABLE',  $table_prefix . 'style_config');
define('STYLES_TABLE',         $table_prefix . 'styles');
define('USERS_TABLE',          $table_prefix . 'users');
define('CLASS_TABLE',	       $table_prefix . 'classes');
define('RACE_TABLE',	       $table_prefix . 'races');
define('FACTION_TABLE',	       $table_prefix . 'factions');

//MultiDKP
define('MULTIDKP_TABLE',	       $table_prefix . 'multidkp');
define('MULTIDKP2EVENTS_TABLE',	 $table_prefix . 'multidkp2event');

//Pluskernel
define('PLUS_CONFIG_TABLE',	       $table_prefix . 'plus_config');
define('PLUS_LINKS_TABLE',	       $table_prefix . 'plus_links');
define('PLUS_UPDATE_TABLE',	       $table_prefix . 'plus_update');
define('PLUS_RSS_TABLE',	       $table_prefix . 'plus_rss');
define('COMMENTS_TABLE',           $table_prefix . 'comments');

// Auth Options
define('A_EVENT_ADD',    1);
define('A_EVENT_UPD',    2);
define('A_EVENT_DEL',    3);
define('A_GROUPADJ_ADD', 4);
define('A_GROUPADJ_UPD', 5);
define('A_GROUPADJ_DEL', 6);
define('A_INDIVADJ_ADD', 7);
define('A_INDIVADJ_UPD', 8);
define('A_INDIVADJ_DEL', 9);
define('A_ITEM_ADD',    10);
define('A_ITEM_UPD',    11);
define('A_ITEM_DEL',    12);
define('A_NEWS_ADD',    13);
define('A_NEWS_UPD',    14);
define('A_NEWS_DEL',    15);
define('A_RAID_ADD',    16);
define('A_RAID_UPD',    17);
define('A_RAID_DEL',    18);
define('A_TURNIN_ADD',  19);
define('A_CONFIG_MAN',  20);
define('A_MEMBERS_MAN', 21);
define('A_USERS_MAN',   22);
define('A_LOGS_VIEW',   23);
define('U_EVENT_LIST',  24);
define('U_EVENT_VIEW',  25);
define('U_ITEM_LIST',   26);
define('U_ITEM_VIEW',   27);
define('U_MEMBER_LIST', 28);
define('U_MEMBER_VIEW', 29);
define('U_RAID_LIST',   30);
define('U_RAID_VIEW',   31);
define('A_PLUGINS_MAN', 32);
define('A_STYLES_MAN',  33);
define('A_SOAP_READ',   34);
define('A_SOAP_WRITE',  35);
define('A_BACKUP',      36);



include_once($eqdkp_root_path . 'includes/functions.php');
include_once($eqdkp_root_path . 'includes/db/dbal.php');

//EQDKP PLUS ADDITION
include_once($eqdkp_root_path . 'pluskernel/include/plus.functions.php');
include_once($eqdkp_root_path . 'pluskernel/include/db.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/html.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/dkpplus.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/jquery.class.php');
require_once($eqdkp_root_path . 'pluskernel/include/urlreader.class.php');
$jqueryp  	= new jQueryPLUS($eqdkp_root_path . 'pluskernel/include/');
$plusdb 	= new dbPlus();
$urlreader 	= new urlreader();
$conf_plus 	= $plusdb->InitConfig();
// END OF PLUS ADDITION

//Debug
if (isset($conf_plus['pk_debug']))
{
	$debug = intval($conf_plus['pk_debug']);
	define('DEBUG', $debug);
}

include_once($eqdkp_root_path . 'includes/eqdkp.php');
include_once($eqdkp_root_path . 'includes/session.php');

// Bridge/CMS Support
if ($conf_plus['pk_bridge_cms_active'] == 1)
{
	//Read the Config to get the selected CMS
	require_once($eqdkp_root_path . 'pluskernel/bridge/config.inc.php');

	//Try to get the correct bridge class
	$bridgefile = $eqdkp_root_path . 'pluskernel/bridge/'.$cms[$cms_sel];
	if (file_exists($bridgefile))
	{
		include_once($bridgefile);
	}
}

//If the User Class not exists extends from the default user class!
//@RedPepper: much cleaner fallback as the standard.class.php
if(! class_exists(User))
{
	class User extends UserSkel {}
}

include_once($eqdkp_root_path . 'includes/class_template.php');
include_once($eqdkp_root_path . 'includes/eqdkp_plugins.php');

$tpl   	 = new Template;
$eqdkp 	 = new EQdkp($eqdkp_root_path);
$user  	 = new User;
$html  	 = new htmlPlus(); 	// plus html class for tooltip and html stuff
$dkpplus = new dkpplus(); 	// calculation class

//Gameicons
$gameicofile = $eqdkp_root_path . 'games/'.$eqdkp->config['default_game'].'/icons.php';
if(@is_file($gameicofile)){
  include_once($gameicofile);
}

define('WEB_IMG_PATH',	      'http://'.$eqdkp->config['server_name'] . $eqdkp->config['server_path'].'images/');

// Style can come from $_GET['style']
$style = ( isset($_GET['style']) ) ? intval($_GET['style']) : false;

// Start up the user/session management
$user->start();
$user->setup(false, $style);

// Set the locale
$cur_locale = $eqdkp->config['default_locale'];
setlocale(LC_ALL, $cur_locale);

// Start plugin management
$pm = new EQdkp_Plugin_Manager(true, DEBUG);

// Populate the admin menu if we're in an admin page, they have admin permissions, and $gen_simple_header is false
if ( (defined('IN_ADMIN')) && (IN_ADMIN === true) )
{
    if ( $user->check_auth('a_', false) )
    {
        if ( !$gen_simple_header )
        {
            include($eqdkp_root_path . 'admin/index.php');
        }
    }
}
#Plus Features:
#==============

//Define IF MultiDKP
	$tpl->assign_vars(array(
	       'IS_MULTIDKP'			=> ( $conf_plus['pk_multidkp'] == 1 )? true : false,
	       'IS_NOT_MULTIDKP'		=> ( $conf_plus['pk_multidkp'] == 1 )? false : true,
	    								));

//RSS Parser
if (!$conf_plus['pk_showRss'] == 1){
	include_once($eqdkp_root_path . 'pluskernel/include/rss.class.php');
	$rss = new rss();
}

//Itemstats
if ($conf_plus['pk_itemstats'] == 1){
	define("path_itemstats", "itemstats");
    include_once($eqdkp_root_path . path_itemstats . '/eqdkp_itemstats.php');
}


//QuickDKP
if (isset($conf_plus['pk_quickdkp']) && $conf_plus['pk_quickdkp'] == 1)
{	$dkpplus->quickdkp();}

//DKP Info
if (!$conf_plus['pk_show_dkpinfo'])
{	$dkpplus->dkpinfo();}

//BossCounter
if ($pm->check(PLUGIN_INSTALLED, 'bosscounter'))
{
    include_once($eqdkp_root_path . 'plugins/bosscounter/bosscounter.php');
}
else if ( ($pm->check(PLUGIN_INSTALLED, 'bosssuite')) && ($eqdkp->config['bs_showBC']) )
{
    include_once($eqdkp_root_path . 'plugins/bosssuite/mods/bosscounter.php');
}

#Itemhistory Diagram
$tpl->assign_vars(array('ITEMHISTORY_DIA'		=> ( !$conf_plus['pk_itemhistory_dia'] == 1 )? true : false));
if (!$conf_plus['pk_itemhistory_dia'])
{
	require_once($eqdkp_root_path . 'pluskernel/include/GoogleGraph.class.php');
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

	/**
	* Converts all globals to superglobals if register_globals = on
	*/
	function RunGlobalsFix(){
	if( (bool)@ini_get('register_globals') ){
    $superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    if( isset($_SESSION) )
    {
        array_unshift($superglobals, $_SESSION);
    }
    $knownglobals = array(
        // Known PHP Reserved globals and superglobals:
        '_ENV',       'HTTP_ENV_VARS',
        '_GET',       'HTTP_GET_VARS',
        '_POST',    	'HTTP_POST_VARS',
        '_COOKIE',    'HTTP_COOKIE_VARS',
        '_FILES',    	'HTTP_FILES_VARS',
        '_SERVER',    'HTTP_SERVER_VARS',
        '_SESSION',   'HTTP_SESSION_VARS',
        '_REQUEST',

        // Global variables used by this code snippet:
        'superglobals',
        'knownglobals',
        'superglobal',
        'global',
        'void'
    );
    foreach( $superglobals as $superglobal ){
        foreach( $superglobal as $global => $void ){
            if( !in_array($global, $knownglobals) ){
                unset($GLOBALS[$global]);
            }
        }
    } // end forach
	} // end if register_globals = on


	// Fehlerbehandlungsfunktion
	function myErrorHandler($errno, $errstr, $errfile, $errline)
	{
	 global $debug;
	   if (($debug > 2) and ($errno <> ERROR) and ($errno <> WARNING) and ($errno <> FATAL))
	  {
	    // filter the mysql.php & itemstats_config errors:
	    if($_GET['show_all']){
	       print("<b>Unkown error type: </b> [$errno] $errstr<br /> <b>File:</b> $errfile <br>  <b>line: </b>$errline <br><br> \n");
  	  }else{
  	    if(substr($errfile, -9) != 'mysql.php' && substr($errfile, -20) != 'config_itemstats.php' && $errno != '2048' && $errno != '8' && (substr($errfile, -10) != 'config.php' && $errline != 275)){
  	  	  print("<b>Unkown error type: </b> [$errno] $errstr<br /> <b>File:</b> $errfile <br>  <b>line: </b>$errline <br><br> \n");
  	    }
	    }
    }

	  switch ($errno) {
	  case FATAL:
	    echo "<b>FATAL ERROR</b> <br><br>
	    	  <b>File:</b> $errfile <br>
	    	  <b>line: </b>$errline <br>
	    	  <b>Error Code: </b>[$errno] <br><br>
	    	  <b>Error String:</b> $errstr<br><br>
	    	  PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />
	    	  Aborting...<br />\n";
	    exit(1);
	    break;
	  case ERROR:
	    echo "<b>ERROR</b> [$errno] $errstr<br />\n";
	    break;
	  case WARNING:
	    echo "<b>WARNING</b> [$errno] $errstr<br />\n";
	    break;
	  } #end switch

	}

	// auf die benutzerdefinierte Fehlerbehandlung umstellen
	$old_error_handler = set_error_handler("myErrorHandler");
}
?>
