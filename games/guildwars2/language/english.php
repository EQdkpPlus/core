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
$english_array = array(
		'classes' => array(
		0 => 'Unknown',
		1 => 'Elementalist',
		2 => 'Warrior',
		3 => 'Ranger',
		4 => 'Necromancer',
		5 => 'Guardian',
		6 => 'Thief',
		7 => 'Mesmer',
		8 => 'Engineer',
	),
	'races' => array(
		'Unknown',
		'Sylvari',
		'Norn',
		'Charr',
		'Asura',
		'Human',
	),
	'roles' => array(
		1 => array(8,7,4,3),
		2 => array(1,2,3,4,5,6,7,8),
		3 => array(1,8,5,7,4,2,6,3),
		4 => array(1,8,5,7,4,2,6,3)
	),
	'lang' => array(
		'guildwars2' => 'Guildwars 2',
		
		// Roles
		'role1'						=> 'Healer',
		'role2'						=> 'Tank',
		'role3'						=> 'Range-DD',
		'role4'						=> 'Melee',
	),
);
?>