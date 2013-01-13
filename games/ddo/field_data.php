<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2012
 * Date:		$Date: 2013-01-12 22:52:22 +0100 (Sa, 12 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12817 $
 * 
 * $Id: field_data.php 12817 2013-01-12 21:52:22Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Category 'character' is a fix one! All others are created dynamically!

$xml_fields = array(

	'classpath'	=> array(
		'type'			=> 'dropdown',
		'category'		=> 'character',
		'name'			=> 'uc_class_path',
		'options'		=> array('Angel of Vengeance' => 'uc_path_1', 'Arcane Archer' => 'uc_path_2', 'Arcane Cannon' => 'uc_path_3', 'Bastion of the Outlands' => 'uc_path_4', 'Beacon of Hope' => 'uc_path_5', 'Deepwood Sniper' => 'uc_path_6', 'Divine Avenger' => 'uc_path_7', 'Elementalist' => 'uc_path_8', 'Henshin Mystic' => 'uc_path_9', 'Master Mechanic' => 'uc_path_10', 'Mastermaker' => 'uc_path_11', 'Necromancer' => 'uc_path_12', 'Ninja Spy' => 'uc_path_13', 'Runic Champion' => 'uc_path_14', 'Savage of the Wild' => 'uc_path_15', 'Scourge of the Undead' => 'uc_path_16', 'Shintao Monk' => 'uc_path_17', 'Spellsinger' => 'uc_path_18', 'Stalwart Soldier' => 'uc_path_19', 'Storm of Kargon' => 'uc_path_20', 'Tempest' => 'uc_path_21', 'The Dark Blade' => 'uc_path_22', 'The Dynamic Hand' => 'uc_path_23', 'The Flame of Justice' => 'uc_path_24', 'The Font of Healing' => 'uc_path_25', 'The Ingenious Sage' => 'uc_path_26', 'The Mighty Protector' => 'uc_path_27', 'The Path of Light' => 'uc_path_28', 'The Path of Shadow' => 'uc_path_29', 'The Truthbringer' => 'uc_path_30', 'The Voice of Power' => 'uc_path_31', 'Thief Acrobat' => 'uc_path_32', 'Two-headed Heron' => 'uc_path_33', 'Vanguard Warrior' => 'uc_path_34', 'Virtuoso of the Sword' => 'uc_path_35', 'War Chanter' => 'uc_path_36', 'Warpriest of Siberys' => 'uc_path_37', 'Whirlwind Fighter' => 'uc_path_38'),
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

);
?>