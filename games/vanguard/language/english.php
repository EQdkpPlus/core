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
		1 => 'Artificer',
		2 => 'Bard',
		3 => 'Berserker',
		4 => 'Blacksmith',
		5 => 'Blood Mage',
		6 => 'Cleric',
		7 => 'Crafter',
		8 => 'Diplomat',
		9 => 'Disciple',
		10 => 'Dread Knight',
		11 => 'Druid',
		12 => 'Inquisitor',
		13 => 'Monk',
		14 => 'Necromancer',
		15 => 'Outfitter',
		16 => 'Paladin',
		17 => 'Psionicist',
		18 => 'Ranger',
		19 => 'Rogue',
		20 => 'Shaman',
		21 => 'Sorcerer',
		22 => 'Warrior',
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