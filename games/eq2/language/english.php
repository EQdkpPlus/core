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
		1 => 'Assassin',
		2 => 'Berserker',
		3 => 'Brigand',
		4 => 'Bruiser',
		5 => 'Coercer',
		6 => 'Conjuror',
		7 => 'Defiler',
		8 => 'Dirge',
		9 => 'Fury',
		10 => 'Guardian',
		11 => 'Illusionist',
		12 => 'Inquisitor',
		13 => 'Monk',
		14 => 'Mystic',
		15 => 'Necromancer',
		16 => 'Paladin',
		17 => 'Ranger',
		18 => 'Shadowknight',
		19 => 'Swashbuckler',
		20 => 'Templar',
		21 => 'Troubador',
		22 => 'Warden',
		23 => 'Warlock',
		24 => 'Wizard',
		25 => 'Beastlord',
	),
	'races' => array(
		'Unknown',
		'Sarnak',
		'Gnome',
		'Human',
		'Barbarian',
		'Dwarf',
		'High Elf',
		'Dark Elf',
		'Wood Elf',
		'Half Elf',
		'Kerran',
		'Troll',
		'Ogre',
		'Froglok',
		'Erudite',
		'Iksar',
		'Ratonga',
		'Halfling',
		'Arasai',
		'Fae',
		'Freeblood'
	),
	'factions' => array(
		'Good',
		'Evil',
		'Neutral'
	),
	'lang' => array(
		'eq2'			=> 'EverQuest II',
		'very_light'	=> 'Cloth',
		'light'			=> 'Leather',
		'medium'		=> 'Chain',
		'heavy'			=> 'Plate',
		'healer'        => 'Healer',
		'fighter'       => 'Fighter',
		'mage'          => 'Mage',
		'scout'         => 'Scout',
		
		'pk_tab_fs_eq2settings'			=> 'EQ2 Settings',
		'uc_import_guild'				=> 'Import Guild',
		'uc_import_guild_help'			=> 'Import all characters of a guild',
		'uc_servername'					=> 'Name of your realmserver',
		'uc_lockserver'				=> 'Lock the realm name for users',
		'uc_faction'					=> 'Faction',
		'uc_update_all'					=> 'Update all characters',
		'uc_importer_cache'				=> 'Reset importer cache',
		'uc_importer_cache_help'		=> 'Delete all the cached data of the import class.',
		
		'uc_class_filter'				=> 'Only character of the class',
		'uc_class_nofilter'				=> 'No filter',
		'uc_guild_name'					=> 'Guild-Name',
		'uc_filter_name'				=> 'Filter',
		'uc_level_filter'				=> 'All characters with a level higher than',
		'uc_imp_novariables'			=> 'You first have to set a realmserver and it\'s location in the settings.',
		'uc_imp_noguildname'			=> 'The name of the guild has not been given.',
		'uc_gimp_loading'				=> 'Loading guild characters, please wait...',
		'uc_gimp_header_fnsh'			=> 'Guild import finished',
		'uc_importcache_cleared'		=> 'The cache of the importer was successfully cleared.',
		'uc_delete_chars_onimport'		=> 'Delete Chars that have left the guild',
		
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
		"uc_updat_armory" 				=> "Refresh from SOE",
		
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