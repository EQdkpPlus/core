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
$global_error_text = array();
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



/* #Rev CU again in 0.7
$svn_rev_file = $eqdkp_root_path . 'svn.rev' ;
if(is_file($svn_rev_file))
{
	$svn_rev_handle = @fopen($svn_rev_file,"r");
	$svn_rev= "\$Rev:".@fread($svn_rev_handle, filesize($svn_rev_file));
}
*/

if ( !defined('EQDKP_INSTALLED') )
{
  echo "<script>window.location.href = '".$eqdkp_root_path."install/index.php';</script>";
}

// Constants
define('EQDKP_VERSION', '1.3.2');

//Eqdkp PLUS CONSTANTS
require_once($eqdkp_root_path . 'pluskernel/plusversion.php');
define('EQDKPPLUS_AUTHOR', 'Corgan & Eqdkp Plus Project Team');

define('NO_CACHE', true);
// Debug level [0 = Off / 1 = Render time, Query count / 2 = 1 + Show queries], 3 = report all errors
// Fixed in 1.3 so it works from config.php and obeys URL parsing of ?debug=2
if ( isset($_GET['debug']) )
{
    define('DEBUG', intval($_GET['debug']));
}
else if ( isset($debug) && $debug != 0 )
{
    define('DEBUG', $debug);
}

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
define('CLASSCOLOR_TABLE',         $table_prefix . 'classcolors');
define('ITEMID_TABLE',         	   $table_prefix . 'itemIDs');

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
define('U_USERLIST',   	34);
define('A_SMS_SEND',   	35);
define('A_BACKUP',      36);

// PHP5 Warning..
if (version_compare(phpversion(), "5.0.0", "<")){
  die(sprintf("EQDKP-PLUS requires PHP %1\$s or higher. Your server runs PHP %2\$s", "5.0.0", phpversion()));
}

// Database Conncetors
$dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
require($eqdkp_root_path . 'includes/db/' . $dbms . '.php');
$db   = new $sql_db();
$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);

//Plus Data Cache | handler for APC, Xcache, MemCache, Mysql caching
$param_array = array();
$param_array['prefix'] = $dbname;

if (function_exists('apc_store') & function_exists('apc_fetch')){
	$_cachetype = 'apc';
}
#$_cachetype = 'memcache';
#$_cachetype = 'xcache';
#$_cachetype = 'mysql_sq';

$cache = ( isset($_cachetype) ) ? 'cache_'.$_cachetype : 'cache_none';
require($eqdkp_root_path . 'pluskernel/cache/'.$cache.'.class.php');
$pdc = new $cache();
$pdc->init($param_array);

include_once($eqdkp_root_path . 'includes/functions.php');


//EQDKP PLUS ADDITION
include_once($eqdkp_root_path . 'pluskernel/include/plus.functions.php');
include_once($eqdkp_root_path . 'pluskernel/include/db.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/html.class.php');
include_once($eqdkp_root_path . 'pluskernel/include/dkpplus.class.php');
include_once($eqdkp_root_path . 'libraries/jquery/jquery.class.php');
include_once($eqdkp_root_path . 'libraries/fileHandler/fileHandler.class.php');
include_once($eqdkp_root_path . 'libraries/xmlTools/xmlTools.class.php');
require_once($eqdkp_root_path . 'pluskernel/include/urlreader.class.php');
require_once($eqdkp_root_path . 'pluskernel/include/comments.class.php');
require_once($eqdkp_root_path . 'pluskernel/include/bbcode.class.php');
require_once($eqdkp_root_path . 'pluskernel/include/language.class.php');

$jqueryp      = new jquery();
$plusdb       = new dbPlus();
$urlreader    = new urlreader();
$conf_plus    = $plusdb->InitConfig();
$xmltools     = new xmlTools();

// END OF PLUS ADDITION

//Debug
// second part of the Debug Part
// Only used if the debug mode didnt set in the config or via ?debug=
if (!defined('DEBUG') && isset($conf_plus['pk_debug']))
{
	//No debug mode is set in the config File, so we check if in the eqdkpplus menu the debug mode isset
	$debug = intval($conf_plus['pk_debug']);
  	define('DEBUG', $debug);
}
if (defined('DEBUG'))
{
	//only if the constant isset, we define the variable
	$debug = DEBUG;
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
if(! class_exists(User))
{
	class User extends UserSkel {}
}

include_once($eqdkp_root_path . 'includes/class_template.php');
include_once($eqdkp_root_path . 'includes/input.class.php');
include_once($eqdkp_root_path . 'includes/eqdkp_plugins.php');
require_once($eqdkp_root_path . 'pluskernel/include/convertion.class.php');

$tpl      = new Template;
$in		  	= new Input();
$eqdkp    = new EQdkp($eqdkp_root_path);
$user     = new User;
$html     = new htmlPlus(); 	// plus html class for tooltip and html stuff
$dkpplus  = new dkpplus(); 	// calculation class
$pcache   = new fileHandler;
$bbcode   = new BBcode();

// Style can come from $_GET['style']
$style = ( isset($_GET['style']) ) ? intval($_GET['style']) : false;

// Start up the user/session management
$user->start();
$user->setup(false, $style);
$jqueryp->GenerateHeader($user->style['template_path']);

// Load things depends on $user class
$pluslang = new PlusLanguage();
$pconvertion  = new PlusConvertions();

//Gameicons
$gameicofile = $eqdkp_root_path . 'games/'.$eqdkp->config['default_game'].'/icons.php';
if(@is_file($gameicofile)){
  include_once($gameicofile);
}



// Start plugin management
$pm = new EQdkp_Plugin_Manager(true, DEBUG);
$pcomments = new plusComments();

// Set the locale
$cur_locale = $eqdkp->config['default_locale'];
setlocale(LC_ALL, $cur_locale);

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
//Itemstats
if ($conf_plus['pk_itemstats'] == 1)
{
	define("path_itemstats", "itemstats");
    include_once($eqdkp_root_path . path_itemstats . '/eqdkp_itemstats.php');
}

//Define IF MultiDKP
$tpl->assign_vars(array(
       'IS_MULTIDKP'			=> ( $conf_plus['pk_multidkp'] == 1 )? true : false,
       'IS_NOT_MULTIDKP'		=> ( $conf_plus['pk_multidkp'] == 1 )? false : true,));


#initialize the Portal
require_once($eqdkp_root_path . 'pluskernel/include/portal.class.php');
$portal	  = new Portal();

// The third collumn on the start page...
if ($portal->THIRD_C == true){
	$tpl->assign_var('THIRD_C', true);
}
//Avril Insert
if (date("d.m.y") == "01.04.11" && $eqdkp->config['avril_11'] != '1' && $eqdkp->config['avril_11'] != '2'){
	$avril_classes = array(
		'Esel',
		'Fisch',
		'Affe',
		'Hund',
		'Katze',
		'Tiger',
		'Maus',
		'Frosch',
		'Schlange',
		'Vogel',
		'Wolf',
		'Hamster',
		'Eisbaer',
		'Fuchs',
		'Pinguin',
		'Nilpferd',
		'Fledermaus',
		'Turtle',
		'Elefant',
	);
	$result = $db->query("SELECT * FROM __classes ORDER BY class_id");
	$i = 0;
	while ( $row = $db->fetch_record($result) )
	{
	$backup[$row['class_id']] = $row['class_name'];
	$db->query("UPDATE __classes SET class_name='".$avril_classes[$i]."' WHERE class_id='".$row['class_id']."'");
	$i++;
	}
	$db->query("INSERT INTO `__config` (`config_name`, `config_value`) VALUES ('avril_11', '1');");
	$db->query("INSERT INTO `__config` (`config_name`, `config_value`) VALUES ('avril_11_backup', '".serialize($backup)."');");
}

//Avril Reset:
if (($eqdkp->config['avril_11'] == '1') || $eqdkp->config['avril_11_reset'] == '1' || $_GET['avril'] == 'reset'){
	$old = unserialize($eqdkp->config['avril_11_backup']);
	if (is_array($old)){
		foreach ($old as $key => $value){
			$db->query("UPDATE __classes SET class_name='".$value."' WHERE class_id='".$key."'");
		}
	}
	$db->query("DELETE FROM `__config` WHERE config_name = 'avril_11'");
	$db->query("INSERT INTO `__config` (`config_name`, `config_value`) VALUES ('avril_11', '2');");
	$db->query("DELETE FROM `__config` WHERE config_name = 'avril_11_backup'");
	$pdc->del_suffix('');
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
function RunGlobalsFix()
{
	if( (bool)@ini_get('register_globals') )
	{
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
	    foreach( $superglobals as $superglobal )
	    {
	        foreach( $superglobal as $global => $void )
	        {
	            if( !in_array($global, $knownglobals) )
	            {
	                unset($GLOBALS[$global]);
	            }
	        }
	    } // end forach
	} // end if register_globals = on


	// Error Handler
	function myErrorHandler($errno, $errstr, $errfile, $errline)
	{

		global $global_error_text ;
		if ((DEBUG > 2) and ($errno <> ERROR) and ($errno <> WARNING) and ($errno <> FATAL))
	  	{
	    	// filter the mysql.php & itemstats_config errors:
	    	if($_GET['show_all'])
	    	{
	    		$global_error_text[] = "<b>Unkown error type: </b> [$errno] $errstr<br /> <b>File:</b> $errfile <br>  <b>line: </b>$errline <br><br> \n";
		  	}else
		  	{
		    	if(substr($errfile, -9) != 'mysql.php' && substr($errfile, -20) != 'config_itemstats.php' && $errno != '2048' && $errno != '8' && (substr($errfile, -10) != 'config.php' && $errline != 275))
		    	{
		  	  		$global_error_text[] = "<b>Unkown error type: </b> [$errno] $errstr<br /> <b>File:</b> $errfile <br>  <b>line: </b>$errline <br><br> \n";
		    	}
	    	}
		}

		switch ($errno)
		{
			case FATAL:
		    $global_error_text[]= "<b>FATAL ERROR</b> <br><br>
			    	  	 <b>File:</b> $errfile <br>
			    	  	 <b>line: </b>$errline <br>
			    	     <b>Error Code: </b>[$errno] <br><br>
			    	     <b>Error String:</b> $errstr<br><br>
			    	  	 PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />
			    	  	 Aborting...<br />\n";
			    exit(1);
		    	break;
			case ERROR:
				$global_error_text[]= "<b>ERROR</b> [$errno] $errstr<br />\n";
				break;
			case WARNING:
				$global_error_text[]= "<b>WARNING</b> [$errno] $errstr<br />\n";
				break;
		} #end switch
	}

	// auf die benutzerdefinierte Fehlerbehandlung umstellen
	$old_error_handler = set_error_handler("myErrorHandler");
}

?>
