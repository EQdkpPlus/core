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
		1 => 'Bard',
		2 => 'Blood Mage',
		3 => 'Cleric',
		4 => 'Disciple',
		5 => 'Dread Knight',
		6 => 'Druid',
		7 => 'Inquisitor',
		8 => 'Monk',
		9 => 'Necromancer',
		10 => 'Paladin',
		11 => 'Psionicist',
		12 => 'Ranger',
		13 => 'Rogue',
		14 => 'Shaman',
		15 => 'Sorcerer',
		16 => 'Warrior',
	),
	'races' => array(
		'Unknown',
		'Dark Elf',
		'Dwarf',
		'Gnome',
		'Goblin',
		'Half Elf',
		'Halfling',
		'High Elf',
		'Kojani',
		'Kurashasa',
		'Lesser Giant',
		'Mordebi',
		'Orc',
		'Qaliathari',
		'Raki',
		'Thestran',
		'Varanjar',
		'Varanthari',
		'Vulmane',
		'Wood Elf'		
	),
	'roles' => array(
		1 => array(3,4,5,15),
		2 => array(6,11,17),
		3 => array(7,8,10,12,13,16),
		4 => array(1,2,6,9,11,14,17)
	),
	'lang' => array(
		'vanguard' => 'Vanguard: Saga of Heroes',
		'unknown' => 'Unknown',
		'cloth' => 'Cloth',
		'leather' => 'Leather',
		'chain' => 'Chain',
		'plate' => 'Plate',
		'role1' => 'Healer',
		'role2' => 'Tank',
		'role3' => 'Range-DD',
		'role4' => 'Melee',
	),
);
?>