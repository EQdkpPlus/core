<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2012
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

	'advancedclass'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_advanced_class',
		'options'		=> array('-' => 'uc_ac_0','Acolyte - Monk' => 'uc_ac_1', 'Acolyte - Priest' => 'uc_ac_2', 'Archer - Beastmasters' => 'uc_ac_3', 'Archer - Ranger' => 'uc_ac_4', 'Magician - Sorcerer' => 'uc_ac_5', 'Magician - Wizard' => 'uc_ac_6', 'Swordman - Knight' => 'uc_ac_7', 'Swordman - Warrior' => 'uc_ac_8', 'Thief - Assassin' => 'uc_ac_9', 'Thief - Rogue' => 'uc_ac_10'),
		'undeletable'	=> true,
		'visible'		=> true,
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
	
	'prof1_value'	=> array(
		'type'			=> 'int',
		'category'		=> 'character',
		'name'			=> 'uc_prof1_value',
		'size'			=> 4,
		'undeletable'	=> true,
		'visible'		=> true
	),
	'prof1_name'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_prof1_name',
		'options'		=> array('-' => 'uc_job_0', 'Alchemy' => 'uc_job_1', 'Artisan' => 'uc_job_2', 'Blacksmith' => 'uc_job_3', 'Chef' => 'uc_job_4'),
		'undeletable'	=> true,
		'visible'		=> true
	),

);
?>