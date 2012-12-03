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
		1 => 'Minstrel',
		2 => 'Captain',
		3 => 'Hunter',
		4 => 'Lore-master',
		5 => 'Burglar',
		6 => 'Guardian',
		7 => 'Champion',
		8 => 'Runekeeper',
		9 => 'Warden',
	),
	'races' => array(
		'Unknown',
		'Man',
		'Hobbit',
		'Elf',
		'Dwarf'
	),
	'factions' => array(
		'Free People',
		'MonsterPlay'
	),
	'roles' => array(
		1 => array(1,8),
		2 => array(6,7,9),
		3 => array(4,5),
		4 => array(3,7,8,9),
		5 => array(2)
	),
	'lang' => array(
		'lotro' => 'The Lord of the Rings Online',
		'heavy' => 'Heavy Armour',
		'medium' => 'Medium Armour',
		'light' => 'Light Armour',
		'role1' => 'Healer',
		'role2' => 'Tank',
		'role3' => 'Crowd Control',
		'role4' => 'Damage Dealer',
		'role5' => 'Supporter',

		// Profile Admin area
		'pk_tab_fs_lotrosettings'					=> 'LOTRO Settings',
		'uc_faction'							=> 'Faction',
		'uc_faction_help'					=> 'Free People / MonsterPlay',
		'uc_fact_pvp'							=> 'MonsterPlay',
		'uc_fact_pve'							=> 'Free People',
		'uc_server_loc'						=> 'Server location',
		'uc_server_loc_help'			=> 'Location of your LOTRO-server',
		'uc_servername'						=> 'Server name',
		'uc_servername_help'			=> 'Name of your LOTRO-server (p.e. Bullroarer)',
		'uc_lockserver'						=> 'Lock the server name for users',
		'uc_lockserver_help'			=> '',
		
		'uc_import_guild'				=> 'Import Guild',
		'uc_import_guild_help'			=> 'Import all characters of a guild',
		'uc_update_all'					=> 'Update all profile information with data from MyLotro',
		'uc_bttn_update'				=> 'Update',
		
		'uc_class_filter'				=> 'Only character of the class',
		'uc_class_nofilter'				=> 'No filter',
		'uc_guild_name'					=> 'Name of the guild',
		'uc_filter_name'				=> 'Filter',
		'uc_level_filter'				=> 'All characters with a level higher than',
		'uc_imp_novariables'			=> 'You first have to set a realmserver and it\'s location in the settings.',
		'uc_imp_noguildname'			=> 'The name of the guild has not been given.',
		'uc_gimp_loading'				=> 'Loading guild characters, please wait...',
		'uc_gimp_header_fnsh'			=> 'Guild import finished',
		"uc_updat_armory" 				=> "Refresh from armory",
		
		'uc_noprofile_found'			=> 'No profile found',
		'uc_profiles_complete'			=> 'Profiles updated successfully',
		'uc_notyetupdated'				=> 'No new data (inactive character)',
		'uc_notactive'					=> 'This character will be skipped because it is set to inactive',
		'uc_error_with_id'				=> 'Error with this character\'s id, it has been left out',
		'uc_notyourchar'				=> 'ATTENTION: You currently try to import a character that already exists in the database but is not owned by you. For security reasons, this action is not permitted. Please contact an administrator for solving this problem or try to use another character name.',
		'uc_lastupdate'					=> 'Last Update',
		
		'uc_prof_import'				=> 'import',
		'uc_import_forw'				=> 'continue',
		'uc_imp_succ'					=> 'The data has been imported successfully',
		'uc_upd_succ'					=> 'The data has been updated successfully',
		'uc_imp_failed'					=> 'An error occured while updating the data. Please try again.',
		
		'uc_charname'					=> 'Character\'s name',
		'uc_servername'					=> 'Server\'s name',
		'uc_charfound'					=> "The character <b>%1\$s</b> has been found in the armory.",
		'uc_charfound2'					=> "This character was updated on <b>%1\$s</b>.",
		'uc_charfound3'					=> 'ATTENTION: Importing will overwrite the existing data!',
		'uc_armory_confail'				=> 'No connection to the armory. Data could not be transmitted.',
		'uc_armory_imported'			=> 'Imported',
		'uc_armory_impfailed'			=> 'Failed',
		'uc_armory_impduplex'			=> 'already existing',
	),
);
?>