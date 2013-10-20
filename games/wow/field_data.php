<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

// Category 'character' is a fix one! All others are created dynamically!

$xml_fields = array(
	'skill_1'	=> array(
		'type'			=> 'int',
		'category'		=> 'character',
		'name'			=> 'uc_skill1',
		'size'			=> 4,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'skill_2'	=> array(
		'type'			=> 'int',
		'category'		=> 'character',
		'name'			=> 'uc_skill2',
		'size'			=> 4,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'gender'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_gender',
		'options'		=> array('Male' => 'uc_male', 'Female' => 'uc_female'),
		'undeletable'	=> true,
		'visible'		=> true
	),
	'guild'	=> array(
		'type'			=> 'text',
		'category'		=> 'character',
		'name'			=> 'uc_guild',
		'size'			=> 40,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'servername'	=> array(
		'category'		=> 'character',
		'name'			=> 'uc_servername',
		'type'			=> 'autocomplete',
		'size'			=> '21',
		'edecode'		=> true,
		'options'		=> registry::register('game')->get('realmlist'),
	),
	'prof1_value'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'uc_prof1_value',
		'size'			=> 4,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'prof1_name'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'uc_prof1_name',
		'options'		=> array(),
		'undeletable'	=> true,
		'visible'		=> true
	),
	'prof2_value'	=> array(
		'type'			=> 'int',
		'category'		=> 'profession',
		'name'			=> 'uc_prof2_value',
		'size'			=> 4,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'prof2_name'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'profession',
		'name'			=> 'uc_prof2_name',
		'options'		=> array(),
		'undeletable'	=> true,
		'visible'		=> true
	),
	'health_bar'	=> array(
		'type'			=> 'int',
		'category'		=> 'character',
		'name'			=> 'uc_bar_health',
		'undeletable'	=> true,
		'size'			=> 4
	),
	'second_bar'	=> array(
		'type'			=> 'int',
		'category'		=> 'character',
		'name'			=> 'uc_bar_2value',
		'size'			=> 4,
		'undeletable'	=> true,
	),
	'second_name'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_bar_2name',
		'options'		=> array('rage' => 'uc_bar_rage', 'energy' => 'uc_bar_energy', 'mana' => 'uc_bar_mana', 'focus' => 'uc_bar_focus', 'runic-power' => 'uc_bar_runic-power'),
		'size'			=> 40,
		'undeletable'	=> true,
	),
);
?>