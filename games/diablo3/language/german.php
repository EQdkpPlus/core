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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$german_array = array(
	'classes' => array(
		0 => 'Unbekannt',
		1 => 'Barbar',
		2 => 'Dämonenjäger',
		3 => 'Mönch',
		4 => 'Hexendoktor',
		5 => 'Zauberer',	
	),
	'roles' => array(
		1 => array(4),
		2 => array(2,4,5),
		3 => array(1,3),
	),
	'lang' => array(
		'diablo3' => 'Diablo 3',
		
		// Roles
		'role1'						=> 'Heiler',
		'role2'						=> 'Fernkampf',
		'role3'						=> 'Nahkampf',
	),
);
?>