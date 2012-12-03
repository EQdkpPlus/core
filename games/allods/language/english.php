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
$english_array =  array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Warrior',
		2 => 'Paladin',
		3 => 'Priest',
		4 => 'Necromancer',
		5 => 'Mage',
		6 => 'Druid',
		7 => 'Psionic',
		8 => 'Stalker',
		9 => 'Bard',
	),
	'races' => array(
		//The League
		0=> array(
			'Kanians',
			'Elves',
			'Gibberlings',
		),
		//The Empire
		1=> array(
			'Xadaganians',
			'Orcs',
			'Arisen'
		),
	),
	'factions' => array(
		'The League',
		'The Empire'
	),
	'lang' => array(
		'allods' => 'Allods Online',
		'plate' => 'Plate',
		'cloth' => 'Cloth',
		'leather' => 'Leather',
		'mail' => 'Mail',
		
		'pk_tab_fs_allodssettings'	=> 'Allods Online Settings',
		'allods_faction'			=> 'Fraction',
	)
);
?>