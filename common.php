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
	die('Do not access this file directly.');
}

// Check for required PHP Version and quit exec if requirements are not reached
if (!version_compare(phpversion(), '7.0.0', ">=")){
	die('
		<b>Your PHP Version is outdated!</b><br/><br/>
		Your PHP Version is outdated and not longer maintained by the PHP project. The Version is end of life. If you are using these releases, you are strongly<br />
		urged to upgrade to a current version, as using older versions may expose you to security vulnerabilities and bugs that have been<br />
		fixed in more recent versions of PHP.<br />
		You are able to check which versions are End of life on the <a href="http://php.net/eol.php" target="_blank">homepage of the PHP Project</a>.<br />
		As Eqdkp-Plus is a modern Web Application and uses current technology, future versions will not be tested on unsupported PHP Versions and might <br />
		not work on these PHP Versions. Please do not ask in the official <a href="http://eqdkp-plus.eu">EQdkp-Plus Forum</a> for a comparible release.<br />
		Please Ask your Hoster or Admin to Upgrade to a recent Version of PHP. If the Hoster refuses to update, you should consider to change Hoster.<br />
		Security should be worth the effort of a hoster change.<br /><br />
		You need at least PHP 7.0.x running on your server! <br />
	');
}

//eqdkp root path
if ( !isset($eqdkp_root_path) ){
	$eqdkp_root_path = './';
}

//set error options
@ini_set("display_errors", 0);
@ini_set("default_charset", "UTF-8");

include_once($eqdkp_root_path.'core/constants.php');
include_once($eqdkp_root_path.'core/super_registry.class.php');
include_once($eqdkp_root_path.'core/registry.class.php');
include_once($eqdkp_root_path.'core/gen_class.class.php');

if(!isset($lite)) $lite = false;
if(!isset($noinit)) $noinit = false;
if(!$noinit) registry::init($eqdkp_root_path, $lite);
?>