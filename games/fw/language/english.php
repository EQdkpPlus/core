<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
$english_array =  array(
	'classes' => array(
		0	=> 'Unknown',
		1	=> 'Warrior',
		2	=> 'Protector',
		3	=> 'Assassin',
		4	=> 'Marksman',
		5	=> 'Mage',
		6	=> 'Priest',
		7	=> 'Vampire',
		8	=> 'Bard',
	),

	'races' => array(
				'Unknown',
				'Dwarf',
				'Elf',
				'Human',
				'Kindred',
				'Stoneman',
				'Lycan',
	),
	'factions' => array(
		'Standard',
	),
	'roles' => array(
		1 => array(6),
		2 => array(1,2),
		3 => array(3,4,5,7),
		4 => array(8),
	),
	
	'lang' => array(
		'fw' => 'Forsaken World',

		'plate'	=> 'Plate',
		'heavy' => 'Heavy',
		'light' => 'Cloth',	
		'medium' => 'Leather',
	
		// Roles
		'role1' => 'Healer',
		'role2' => 'Tank',
		'role3' => 'Damage Dealer',
		'role4' => 'Supporter',
	
		// Profile information
		'uc_gender'						=> 'Gender',
		'uc_male'						=> 'Male',
		'uc_female'						=> 'Female',
		'uc_guild'						=> 'Guild',
		
	),
);

?>