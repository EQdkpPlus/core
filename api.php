<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
define('NO_MMODE_REDIRECT', true);
define('SESSION_TYPE', 'exchange');

include_once($eqdkp_root_path . 'common.php');

if (registry::register('config')->get('pk_maintenance_mode')){
	if (registry::register('input')->get('format') == 'json'){
		$myOut = json_encode(array('status' => 0, 'error' => 'maintenance'));
	} else {
		$myOut = '<?xml version="1.0" encoding="UTF-8"?><response><status>0</status><error>maintenance</error></response>';
	}
	header('Content-Length: '.strlen($myOut));
	if (registry::register('input')->get('format') != 'json'){
		header('Content-type: text/xml');
	} else {
		header('Content-type: application/json');
	}
	echo($myOut);
	exit;
}

$return	= register('plus_exchange')->execute();
header('Content-Length: '.strlen($return));
if (registry::register('input')->get('format') != 'json'){
	header('Content-type: text/xml');
} else {
	header('Content-type: application/json');
}
			
echo($return);
exit;

?>