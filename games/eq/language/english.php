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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
$english_array = array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Bard',
		2 => 'Beastlord',
		3 => 'Berserker',
		4 => 'Enchanter',
		5 => 'Magician',
		6 => 'Monk',
		7 => 'Necromancer',
		8 => 'Paladin',
		9 => 'Ranger',
		10 => 'Rogue',
		11 => 'Shadow Knight',
		12 => 'Shaman',
		13 => 'Warrior',
		14 => 'Wizard',
		15 => 'Cleric',
		16 => 'Druid', //note: new classes need to be added as last spot, else the id => class assignment gets messed up in already existing systems
	),
	'races' => array(
		'Unknown',
		'Gnome',
		'Human',
		'Barbarian',
		'Dwarf',
		'High Elf',
		'Dark Elf',
		'Wood Elf',
		'Half Elf',
		'Vah Shir',
		'Troll',
		'Ogre',
		'Frog',
		'Iksar',
		'Erudite',
		'Halfling',
		'Drakkin' //note: see above
	),
	'factions' => array(
		'Good',
		'Evil'
	),
	'lang' => array(
		'eq' => 'EverQuest',
		'plate' => 'Plate',
		'silk' => 'Silk',
		'leather' => 'Leather',
		'chain' => 'Chain',
	),
);

?>