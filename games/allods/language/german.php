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
		1 => 'Krieger',
		2 => 'Paladin',
		3 => 'Heiler',
		4 => 'Beschwörer',
		5 => 'Magier',
		6 => 'Behüter',
		7 => 'Psioniker',
		8 => 'Späher',
		9 => 'Barde',
	),
	'races' => array(
		//Liga
		0=> array(
			'Kanians',
			'Elfen',
			'Gibberlings',
		),
		//Imperium
		1=> array(
			'Xadaganians',
			'Orks',
			'Arisen'
		),
	),
	'factions' => array(
		'Die Liga',
		'Das Imperium'
	),
	'lang' => array(
		'allods' => 'Allods Online',
		'plate' => 'Platte',
		'cloth' => 'Stoff',
		'leather' => 'Leder',
		'mail' => 'Schwere Rüstung',
		
		'pk_tab_fs_allodssettings'	=> 'Allods Online Einstellungen',
		'allods_faction'			=> 'Fraktion',
	),
);
?>