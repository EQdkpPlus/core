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

$module_lang = array(
	'dattendance'		=> 'Tage (%d Tage)',
	'dlifetime'			=> 'Tage (Lebenszeit)',
	'dattendance_fromto' => 'Tagesteilnahme',
);

$preset_lang = array(
	'dattendance_30'	=> 'Tagesteilnahme (30 Tage)',
	'dattendance_60'	=> 'Tagesteilnahme (60 Tage)',
	'dattendance_90'	=> 'Tagesteilnahme (90 Tage)',
	'dattendance_lt'	=> 'Tagesteilnahme (Lebenszeit)',
	'dattendance_fromto_all' => 'Tagesteilnahme (def. Zeitraum)',
);
?>
