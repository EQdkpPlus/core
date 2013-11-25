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

/**
 * -------------------------------------------------------------------------
 * CORE VERSIONS
 * -------------------------------------------------------------------------
 */
define('VERSION_INT',		'1.0.17');				// the internal version number for updates & update check
define('VERSION_EXT',		'1.0.1');				// the external version number to be shown in the footer
define('VERSION_WIP',		false);					// work in progress or stable?
define('VERSION_PHP_RQ',	'5.2.0');				// required version of PHP
define('VERSION_PHP_REC', 	'5.3.0');				// recommended version of PHP


// Plugin states
define('PLUGIN_INSTALLED',		 1);
define('PLUGIN_BROKEN',			 2);
define('PLUGIN_DISABLED',		 4);
define('PLUGIN_REGISTERED',		 8);
define('PLUGIN_INITIALIZED',	16);
define('PLUGIN_ALL', PLUGIN_INITIALIZED
					| PLUGIN_REGISTERED 
					| PLUGIN_DISABLED
					| PLUGIN_BROKEN
					| PLUGIN_INSTALLED);

// SQL types
define('SQL_INSTALL',	1);
define('SQL_UNINSTALL',	2);

//URLs
//-------------------------------------------------------------------------
//Sourceforge
define('EQDKP_PROJECT_URL', "http://eqdkp-plus.eu");
define('EQDKP_ABOUT_URL', "http://eqdkp-plus.eu/about");
define('EQDKP_DOWNLOADS_URL', "https://sourceforge.net/projects/eqdkp-plus/files/");
define('EQDKP_REPO_URL', "http://eqdkp-plus.eu/repository/");
define('EQDKP_NOTIFICATIONS_URL', "http://eqdkp-plus.eu/rss/notifications.xml");
define('EQDKP_TWITTER_URL', "https://api.twitter.com/1/statuses/user_timeline/EQdkpPlus.json");
define('EQDKP_BOARD_URL', "http://eqdkp-plus.eu/forum");
define('EQDKP_CRL_URL', "https://eqdkp-plus.googlecode.com/files/crl.txt");
define('EQDKP_WIKI_URL', "http://eqdkp-plus.eu/wiki/");
define('EQDKP_BUGTRACKER_URL', "http://eqdkp-plus.eu/bugtracker/");
?>