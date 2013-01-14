<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2012
 * Date:		$Date: 2013-01-14 15:10:51 +0100 (Mo, 14 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: sionaa $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12841 $
 * 
 * $Id: field_data.php 12841 2013-01-14 14:10:51Z sionaa $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Category 'character' is a fix one! All others are created dynamically!

$xml_fields = array(

	'ability_1'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_ability_1',
		'options'		=> array('-' => 'uc_ab_0','Combat' => 'uc_ab_1', 'Conjury' => 'uc_ab_2', 'Fortification' => 'uc_ab_3', 'Will' => 'uc_ab_4', 'Death' => 'uc_ab_5', 'Wild' => 'uc_ab_6', 'Magic' => 'uc_ab_7', 'Assassination' => 'uc_ab_8', 'Artistry' => 'uc_ab_9', 'Thief - Rogue' => 'Devotion'),
		'undeletable'	=> true,
		'visible'		=> true,
	),

	'ability_2'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_ability_2',
		'options'		=> array('-' => 'uc_ab_0','Combat' => 'uc_ab_1', 'Conjury' => 'uc_ab_2', 'Fortification' => 'uc_ab_3', 'Will' => 'uc_ab_4', 'Death' => 'uc_ab_5', 'Wild' => 'uc_ab_6', 'Magic' => 'uc_ab_7', 'Assassination' => 'uc_ab_8', 'Artistry' => 'uc_ab_9', 'Thief - Rogue' => 'Devotion'),
		'undeletable'	=> true,
		'visible'		=> true
	),
	'ability_3'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_ability_3',
		'options'		=> array('-' => 'uc_ab_0','Combat' => 'uc_ab_1', 'Conjury' => 'uc_ab_2', 'Fortification' => 'uc_ab_3', 'Will' => 'uc_ab_4', 'Death' => 'uc_ab_5', 'Wild' => 'uc_ab_6', 'Magic' => 'uc_ab_7', 'Assassination' => 'uc_ab_8', 'Artistry' => 'uc_ab_9', 'Thief - Rogue' => 'Devotion'),
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

);
?>