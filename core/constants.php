<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

/**
 * -------------------------------------------------------------------------
 * CORE VERSIONS
 * -------------------------------------------------------------------------
 */

define('VERSION_INT',		'2.3.30.0');				// the internal version number for updates & update check
define('VERSION_EXT',		'2.3.30');				// the external version number to be shown in the footer
define('VERSION_WIP',		false);					// work in progress or stable?
define('VERSION_PHP_RQ',	'5.6.0');				// required version of PHP
define('VERSION_PHP_REC',	'7.2.0');				// recommended version of PHP
define('REQ_PHP_MEMORY',	'64M');					// required PHP Memory
define('REQ_PHP_MEMORY_REC','128M');				// required PHP Memory

// Plugin states
define('PLUGIN_INSTALLED',		 1);
define('PLUGIN_BROKEN',			 2);
define('PLUGIN_DISABLED',		 4);
define('PLUGIN_REGISTERED',		 8);
define('PLUGIN_INITIALIZED',	16);
define('PLUGIN_BROKEN_API',		32);
define('PLUGIN_ALL', PLUGIN_INITIALIZED
					| PLUGIN_REGISTERED
					| PLUGIN_DISABLED
					| PLUGIN_BROKEN
					| PLUGIN_INSTALLED);

// Portal modules
define('PMOD_VIS_EXT', -1);

// SQL types
define('SQL_INSTALL',	1);
define('SQL_UNINSTALL',	2);

//-------------------------------------------------------------------------
//Things user can modify at config.php

//Max Images in Useravatar Folder
if(!defined('MAX_FILES_USERFOLDER')) define('MAX_FILES_USERFOLDER', 30);

//Max concurrent sessions from one IP with the same Browser
if(!defined('MAX_CONCURRENT_SESSIONS')) define('MAX_CONCURRENT_SESSIONS', 50);

//Snapshot time - default: three weeks
if(!defined('POINTS_SNAPSHOT_TIME')) define('POINTS_SNAPSHOT_TIME', 1814400);

if(!defined('STOPFORUMSPAM_CONFIDENCE')) define('STOPFORUMSPAM_CONFIDENCE', 70);
if(!defined('STOPFORUMSPAM_FREQUENCY')) define('STOPFORUMSPAM_FREQUENCY', 2);

//URLs
//-------------------------------------------------------------------------
define('EQDKP_PROJECT_URL',			"https://eqdkp-plus.eu");
define('EQDKP_ABOUT_URL',			"https://eqdkp-plus.eu/about");
define('EQDKP_DOWNLOADS_URL',		"https://download.eqdkp-plus.eu/");
define('EQDKP_REPO_URL',			"https://repo.eqdkp-plus.eu/");
define('EQDKP_NOTIFICATIONS_URL',	"https://eqdkp-plus.eu/rss/notifications.xml");
define('EQDKP_TWITTER_SCREENNAME',	"EQdkpPlus");
define('EQDKP_BOARD_URL',			"https://forum.eqdkp-plus.eu");
define('EQDKP_CRL_URL',				"https://raw.githubusercontent.com/EQdkpPlus/misc-crl/master/crl.txt");
define('EQDKP_WIKI_URL',			"https://wiki.eqdkp-plus.eu/wiki/");
define('EQDKP_BUGTRACKER_URL',		"https://forum.eqdkp-plus.eu/bugtracker/");
define('EQDKP_CONNECTION_CHECK_URL', 'https://raw.githubusercontent.com/EQdkpPlus/misc-connectioncheck/master/status.txt');

//Tag Blacklist for filtering article content
$TAG_BLACKLIST = array(
	'applet',
	'body',
	'bgsound',
	'base',
	'basefont',
	'canvas',
	'embed',
	'frame',
	'frameset',
	'head',
	'html',
	'id',
	'iframe',
	'ilayer',
	'layer',
	'link',
	'meta',
	'name',
	'object',
	'script',
	'style',
	'title',
	'xml',
);

//Attribute Blacklist for filtering article content
$ATTR_BLACKLIST = array(
		'action',
		'background',
		'codebase',
		'dynsrc',
		'formaction',
		'lowsrc'
);
