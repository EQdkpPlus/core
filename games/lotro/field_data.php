<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2011-10-31 16:05:28 +0100 (Mo, 31. Okt 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11412 $
 * 
 * $Id: field_data.php 11412 2011-10-31 15:05:28Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Category 'character' is a fix one! All others are created dynamically!

$xml_fields = array(
	'vocation'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'vocation',
		'options'		=> array('armourer' => 'Armourer', 'armsman' => 'Armsman', 'explorer' => 'Explorer', 'historian' => 'Historian', 'tinker' => 'Tinker', 'woodsman' => 'Woodsman', 'yeoman' => 'Yeoman'),
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	
	'profession1'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'profession1',
		'options'		=> array('farmer' => 'Farmer', 'forester' => 'Forester', 'prospector' => 'Prospector', 'cook' => 'Cook', 'jeweller' => 'Jeweller', 'metalsmith' => 'Metalsmith', 'scholar' => 'Scholar', 'tailor' => 'Tailor', 'weaponsmith' => 'Weaponsmith', 'woodworker' => 'Woodworker'),
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession2'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'profession2',
		'options'		=> array('farmer' => 'Farmer', 'forester' => 'Forester', 'prospector' => 'Prospector', 'cook' => 'Cook', 'jeweller' => 'Jeweller', 'metalsmith' => 'Metalsmith', 'scholar' => 'Scholar', 'tailor' => 'Tailor', 'weaponsmith' => 'Weaponsmith', 'woodworker' => 'Woodworker'),
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession3'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'profession3',
		'options'		=> array('farmer' => 'Farmer', 'forester' => 'Forester', 'prospector' => 'Prospector', 'cook' => 'Cook', 'jeweller' => 'Jeweller', 'metalsmith' => 'Metalsmith', 'scholar' => 'Scholar', 'tailor' => 'Tailor', 'weaponsmith' => 'Weaponsmith', 'woodworker' => 'Woodworker'),
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	
	'profession1_mastery'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession1_mastery',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession2_mastery'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession2_mastery',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession3_mastery'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession3_mastery',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession1_proficiency'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession1_proficiency',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession2_proficiency'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession2_proficiency',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
	'profession3_proficiency'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'profession3_proficiency',
		'size'			=> 3,
		'undeletable'	=> true,
		'enabled'		=> true,
	),
);
?>