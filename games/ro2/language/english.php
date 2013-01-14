<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-12 18:27:09 +0100 (Sa, 12 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12812 $
 * 
 * $Id: english.php 12812 2013-01-12 17:27:09Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}
$english_array = array(
	'classes' => array(
		0 => 'Unknown',
		1 => 'Acolyte',
		2 => 'Archer',
		3 => 'Magician',
		4 => 'Swordman',
		5 => 'Thief',

	),

	'races' => array(
		'Unknown',
		'Norman',
		'Ellr',
		'Dimago ',

	),

	'lang' => array(
		'ro2' => 'Ragnarok Online 2',
		'tank' => 'Tank',
		'damage_dealer' => 'Damage Dealer',
		'healer' => 'Healer',
		
		// Profile information
		'uc_gender'						=> 'Gender',
		'uc_male'						=> 'Male',
		'uc_female'						=> 'Female',
		'uc_guild'						=> 'Guild',
		'uc_advanced_class'				=> 'Advanced Class',
		'uc_prof1_name'					=> 'Job',
		'uc_prof1_value'				=> 'Job-Level',
		
		
		// Advanced Class Information
		
		'uc_ac_0' => '-',
		'uc_ac_1' => 'Acolyte - Monk',
		'uc_ac_2' => 'Acolyte - Priest',
		'uc_ac_3' => 'Archer - Beastmasters',
		'uc_ac_4' => 'Archer - Ranger',
		'uc_ac_5' => 'Magician - Sorcerer',
		'uc_ac_6' => 'Magician - Wizard',
		'uc_ac_7' => 'Swordman - Knight',
		'uc_ac_8' => 'Swordman - Warrior',
		'uc_ac_9' => 'Thief - Assassin',
		'uc_ac_10' => 'Thief - Rogue',
		
		// Jobs
		
		'uc_job_0'	=> '-',
		'uc_job_1'	=> 'Alchemy',
		'uc_job_2'	=> 'Artisan',
		'uc_job_3'	=> 'Blacksmith',
		'uc_job_4'	=> 'Chef',
		
	),
);
?>