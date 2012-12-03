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
		1 => 'Thaumaturge',
		2 => 'Marauder',
		3 => 'Lancer',
		4 => 'Pugilist',
		5 => 'Gladiator',
		6 => 'Archer',
		7 => 'Conjurer',
		8 => 'Botanist',
		9 => 'Fisher',
		10 => 'Miner',
		11 => 'Alchemist',
		12 => 'Armorer',
		13 => 'Blacksmith',
		14 => 'Carpenter',
		15 => 'Culinarian',
		16 => 'Leatherworker',
		17 => 'Goldsmith',
		18 => 'Weaver',
	),
	'races' => array(
		'Unknown',
		'Elezen M',
		'Elezen F',
		'Roegadyn',
		'Hyur M',
		'Hyur F',
		'Miqote',
		'Lalafell M',
		'Lalafell F',
	),
	'factions' => array(
		'Gridania',
		'Limsa Lominsa',
		'Uldah',
	),
	'lang' => array(
		'ffxiv' => 'Final Fantasy XIV',
		'tank' => 'Tank',
		'support' => 'Support',
		'damage_dealer' => 'Damage Dealer',
	),
);
?>