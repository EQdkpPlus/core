<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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
		0	=> 'Unknown',
		1	=> 'Tank',
		2	=> 'Healer',
		3	=> 'Supporter',
		4	=> 'Melee',
		5	=> 'Ranged',

	),

	'races' => array(
		'Unknown',
		'Nuian',
		'Elf',
		'Hariharan',
		'Ferre',

	),

	'lang' => array(
		'archeage' => 'Archeage',
		
		// Profile information
		'uc_gender'						=> 'Gender',
		'uc_male'						=> 'Male',
		'uc_female'						=> 'Female',
		'uc_guild'						=> 'Guild',
		'uc_ability_1'				=> '1. Ability',
		'uc_ability_2'				=> '2. Ability',
		'uc_ability_3'				=> '3. Ability',
		
		
		// Advanced Class Information
		
		'uc_ab_0' => '-',
		'uc_ab_1' => 'Combat',
		'uc_ab_2' => 'Conjury',
		'uc_ab_3' => 'Fortification',
		'uc_ab_4' => 'Will',
		'uc_ab_5' => 'Death',
		'uc_ab_6' => 'Wild',
		'uc_ab_7' => 'Magic',
		'uc_ab_8' => 'Assassination',
		'uc_ab_9' => 'Artistry',
		'uc_ab_10' => 'Devotion',

	),
);
?>