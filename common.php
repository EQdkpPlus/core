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
	die('Do not access this file directly.');
}

// Check for required PHP Version and quit exec if requirements are not reached
if (!version_compare(phpversion(), '5.3.0', ">=")){
	die('
		<b>PHP 4 detected!</b><br/><br/>
		You need PHP5 running on your server! <br />
		PHP4 is no longer supported! Dont ask in the <a href="http://www.eqdkp-plus.com">EQdkp-Plus Forum</a> for a PHP4 Release!<br />
		Ask your Admin or Hoster for a PHP5 update! If they do not update, you should leave your hoster!<br/><br/>
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
error_reporting (E_ALL);
ini_set("display_errors", 0);

include_once($eqdkp_root_path.'core/constants.php');
include_once($eqdkp_root_path.'core/super_registry.class.php');
include_once($eqdkp_root_path.'core/registry.class.php');
include_once($eqdkp_root_path.'core/gen_class.class.php');

if(!isset($lite)) $lite = false;
if(!isset($noinit)) $noinit = false;
if(!$noinit) registry::init($eqdkp_root_path, $lite);
?>