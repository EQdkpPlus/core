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
	if (registry::register('input')->get('format') != 'json' && registry::register('input')->get('format') != 'lua'){
		header('Content-type: text/xml; charset=utf-8');
	} else {
		header('Content-type: application/json; charset=utf-8');
	}
	echo($myOut);
	exit;
}

$return	= register('plus_exchange')->execute();
header('Content-Length: '.strlen($return));
if (registry::register('input')->get('format') != 'json' && registry::register('input')->get('format') != 'lua'){
	header('Content-type: text/xml; charset=utf-8');
} else {
	header('Content-type: application/json; charset=utf-8');
}
			
echo($return);
exit;

?>