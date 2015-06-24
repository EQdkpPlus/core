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

/**
 * -------------------------------------------------------------------------
 * CORE VERSIONS
 * -------------------------------------------------------------------------
 */
define('VERSION_INT',		'2.0.4.0');				// the internal version number for updates & update check
define('VERSION_EXT',		'2.0.4');				// the external version number to be shown in the footer
define('VERSION_WIP',		false);					// work in progress or stable?
define('VERSION_PHP_RQ',	'5.4.0');				// required version of PHP
define('VERSION_PHP_REC',	'5.5.0');				// recommended version of PHP
define('REQ_PHP_MEMORY',	'60M');					// required PHP Memory
define('REQ_PHP_MEMORY_REC','128M');				// required PHP Memory

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

// Portal modules
define('PMOD_VIS_EXT', -1);

// SQL types
define('SQL_INSTALL',	1);
define('SQL_UNINSTALL',	2);

//Max Images in Useravatar Folder
define('MAX_FILES_USERFOLDER', 20);

//URLs
//-------------------------------------------------------------------------
define('EQDKP_PROJECT_URL',			"http://eqdkp-plus.eu");
define('EQDKP_ABOUT_URL',			"http://eqdkp-plus.eu/about");
define('EQDKP_DOWNLOADS_URL',		"http://eqdkp-plus.eu/repository/");
define('EQDKP_REPO_URL',			"http://eqdkp-plus.eu/repository/");
define('EQDKP_NOTIFICATIONS_URL',	"http://eqdkp-plus.eu/rss/notifications.xml");
define('EQDKP_TWITTER_SCREENNAME',	"EQdkpPlus");
define('EQDKP_BOARD_URL',			"http://eqdkp-plus.eu/forum");
define('EQDKP_CRL_URL',				"https://raw.githubusercontent.com/EQdkpPlus/misc-crl/master/crl.txt");
define('EQDKP_WIKI_URL',			"http://eqdkp-plus.eu/wiki/");
define('EQDKP_BUGTRACKER_URL',		"http://eqdkp-plus.eu/bugtracker/");

//Tag Blacklist for filtering article content
$TAG_BLACKLIST = array(
	'applet',
	'body',
	'bgsound',
	'base',
	'basefont',
	'frame',
	'frameset',
	'head',
	'html',
	'id',
	'ilayer',
	'layer',
	'link',
	'meta',
	'name',
	'script',
	'title',
	'xml',
	'iframe',
);

//Attribute Blacklist for filtering article content
$ATTR_BLACKLIST = array(
	'onclick',
	"ondblclick",
	"onkeydown",
	"onkeypress",
	"onkeyup",
	"onmousedown",
	"onmousemove",
	"onmouseout",
	"onmouseover",
	"onmouseup",
	"onchange",
	'action',
	'background',
	'codebase',
	'dynsrc',
	'lowsrc',
);

?>
