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
 * @copyright	2006-2011 EQdkp-Plus Developer Team
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
		You need PHP5 running on you server! <br />
		PHP4 is no longer supported! Dont ask in the <a href="http://www.eqdkp-plus.com">EQdkp-Plus Forum</a> for a PHP4 Release!<br />
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
error_reporting (E_ALL);
ini_set("display_errors", 0);

include_once($eqdkp_root_path.'core/constants.php');
include_once($eqdkp_root_path.'core/super_registry.class.php');
if(!version_compare(phpversion(), '5.3.0', ">=")) {
	include_once($eqdkp_root_path.'core/registry.class.5.2.php');
	include_once($eqdkp_root_path.'core/gen_class.class.5.2.php');
} else {
	include_once($eqdkp_root_path.'core/registry.class.php');
	include_once($eqdkp_root_path.'core/gen_class.class.php');
}
if(!isset($lite)) $lite = false;
if(!isset($noinit)) $noinit = false;
if(!$noinit) registry::init($eqdkp_root_path, $lite);
?>