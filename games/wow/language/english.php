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
		0	=> 'Unknown',
		1	=> 'Death Knight',
		2	=> 'Druid',
		3	=> 'Hunter',
		4	=> 'Mage',
		5	=> 'Paladin',
		6	=> 'Priest',
		7	=> 'Rogue',
		8	=> 'Shaman',
		9	=> 'Warlock',
		10	=> 'Warrior',
		11	=> 'Monk'
	),
	'races' => array(
		'Unknown',
		'Gnome',
		'Human',
		'Dwarf',
		'Night Elf',
		'Troll',
		'Undead',
		'Orc',
		'Tauren',
		'Draenei',
		'Blood Elf',
		'Worgen',
		'Goblin',
		'Pandaren'
	),
	'factions' => array(
		'alliance' => 'Alliance',
		'horde' => 'Horde'
	),
	'roles' => array(
		1 => array(2, 5, 6, 8, 11),
		2 => array(1, 2, 5, 10, 11),
		3 => array(2, 3, 4, 6, 8, 9),
		4 => array(1, 2, 5, 7, 8, 10, 11)
	),
	'professions' => array(
		'trade_alchemy'					=> 'Alchemy',
		'trade_blacksmithing'			=> 'Blacksmithing',
		'trade_engraving'				=> 'Enchanting',
		'trade_engineering'				=> 'Engineering',
		'trade_herbalism'				=> 'Herbalism',
		'inv_inscription_tradeskill01'	=> 'Inscription',
		'inv_misc_gem_01'				=> 'Jewelcrafting',
		'trade_leatherworking'			=> 'Leatherworking',
		'inv_pick_02'					=> 'Mining',
		'inv_misc_pelt_wolf_01'			=> 'Skinning',
		'trade_tailoring'				=> 'Tailoring',
	),
	'lang' => array(
		'wow'			=> 'World of Warcraft',
		'plate'			=> 'Plate',
		'cloth'			=> 'Cloth',
		'leather'		=> 'Leather',
		'mail'			=> 'Mail',
		'tier_token'	=> 'Token: ',
		'talents_tt_1'	=> 'Primary Talent',
		'talents_tt_2'	=> 'Secondary Talent',
		'talents'		=> array(
			1	=> array('Blood','Frost','Unholy'),
			2	=> array('Balance','Feral','Guardian','Restoration'),
			3	=> array('Beast Mastery','Marksmanship','Survival'),
			4	=> array('Arcane','Fire','Frost'),
			5	=> array('Holy','Protection','Retribution'),
			6	=> array('Discipline','Holy','Shadow'),
			7	=> array('Assassination','Combat','Subtlety'),
			8	=> array('Elemental','Enhancement','Restoration'),
			9	=> array('Affliction','Demonology','Destruction'),
			10	=> array('Arms','Fury','Protection'),
			11	=> array('Brewmaster','Mistweaver','Windwalker'),
		),
		'role1' => 'Healer',
		'role2' => 'Tank',
		'role3' => 'Range-DD',
		'role4' => 'Melee',
		
		// Profile information
		'uc_prof_professions'			=> 'Professions',
		'skills'						=> 'Specialization',
		'corevalues'					=> 'Core Attributes',
		'values'						=> 'Attributes',

		// Profile information
		'uc_achievements'				=> 'Achievements',
		'uc_bosskills'					=> 'Boss Kills',
		'uc_bar_rage'					=> 'Rage',
		'uc_bar_energy'					=> 'Energy',
		'uc_bar_mana'					=> 'Mana',
		'uc_bar_focus'					=> 'Focus',
		'uc_bar_runic-power'			=> 'Runic Power',

		'uc_skill1'						=> 'Talents 1',
		'uc_skill2'						=> 'Talents 2',

		'pv_tab_profiles'				=> 'External Profiles',
		'pv_tab_talents'				=> 'Specialization',

		'uc_guild'						=> 'Guild',
		'uc_bar_health'					=> 'Health',
		'uc_bar_2value'					=> 'Value of the second bar',
		'uc_bar_2name'					=> 'Name of the second bar',

		'uc_gender'						=> 'Gender',
		'uc_male'						=> 'Male',
		'uc_female'						=> 'Female',
		'uc_faction'					=> 'Faction',
		'uc_faction_help'				=> 'The ingame faction',
		'uc_fact_horde'					=> 'Horde',
		'uc_fact_alliance'				=> 'Alliance',

		'uc_prof1_value'				=> 'Level of the first profession',
		'uc_prof1_name'					=> 'Name of the first profession',
		'uc_prof2_value'				=> 'Level of the second profession',
		'uc_prof2_name'					=> 'Name of the second profession',

		'uc_achievement_tab_default'	=> 'Ungruppiert',
		'uc_achievement_tab_classic'	=> 'Classic',
		'uc_achievement_tab_bc'			=> 'Burning Crusade',
		'uc_achievement_tab_wotlk'		=> 'Wrath of the Lich King',
		'uc_achievement_tab_cataclysm'	=> 'Cataclysm',
		'uc_achievement_tab_mop'		=> 'Mists of Pandaria',
		'uc_achievement_tab_wod'		=> 'Warlords of Draenor',
		
		'challenge'						=> 'Challenge Mode',
		'challenge_title'				=> 'Challenge Mode Leaderboards',
		'off_realm_toon'				=> 'This character seems to be not in your guild. As the challenges are Battle-Realm based, could be foreign characters in this list.',
		

		// Profile Admin area
		'pk_tab_fs_wowsettings'			=> 'WoW Settings',
		'importer_head_txt'				=> 'battle.net Importer',
		'uc_servername_help'			=> 'Name of your realmserver (e.g. Mal\'Ganis)',
		'uc_update_all'					=> 'Update from battle.net',
		'uc_update_all_help'			=> 'Update all profile information with data from the battle.net\'s profiler',
		'uc_importer_cache'				=> 'Reset importer cache',
		'uc_importer_cache_help'		=> 'Delete all the cached data of the import class.',
		'uc_import_guild'				=> 'Import guild from battle.net',
		'uc_import_guild_help'			=> 'Import all members of a guild from battle.net',
		'uc_server_loc'					=> 'Server location',
		'uc_server_loc_help'			=> 'The location of the WoW Server',
		'uc_data_lang'					=> 'Language of the data',
		'uc_data_lang_help'				=> 'In which language should the data be fetched from external website?',
		'uc_error_head'					=> 'ERROR',
		'uc_error_noserver'				=> 'There is no server saved in the global settings. The server is required for fetching external data. Please report it to an administrator.',
		
		// Armory Import
		#'uc_armory_loc'					=> 'Realmserver\'s location',
		"uc_updat_armory" 				=> "Refresh from armory",
		'uc_charname'					=> 'Character\'s name',
		'uc_servername'					=> 'Realm\'s name',
		'uc_charfound'					=> "The character <b>%1\$s</b> has been found in the armory.",
		'uc_charfound2'					=> "This character was updated on <b>%1\$s</b>.",
		'uc_charfound3'					=> 'ATTENTION: Importing will overwrite the existing data!',
		'uc_armory_imported'			=> 'Charakter successfully imported',
		'uc_armory_updated'				=> 'Charakter successfully updated',
		'uc_armory_impfailed'			=> 'Charakter not imported',
		'uc_armory_updfailed'			=> 'Charakter not updated',
		'uc_armory_impfail_reason'		=> 'Reason:',
		'uc_armory_impduplex'			=> 'Charakter is already in the database',

		// guild importer
		'uc_class_filter'				=> 'Only member of the class',
		'uc_class_nofilter'				=> 'No filter',
		'uc_guild_name'					=> 'Name of the guild',
		'uc_filter_name'				=> 'Filter',
		'uc_level_filter'				=> 'All characters with a level higher than',
		'uc_rank_filter1a'				=> 'greater or equal',
		'uc_rank_filter1b'				=> 'equal',
		'uc_rank_filter'				=> 'Rank',
		'uc_imp_noguildname'			=> 'The name of the guild has not been given.',
		'uc_gimp_loading'				=> 'Importing guild members, please wait...',
		'uc_massupd_loading'			=> 'Characters are updated, please wait...',
		'uc_gimp_header_fnsh'			=> 'The import of the guild members was finished. Imported Data: Name of the character, race, class and level. To update the other data, please run the battle.net updater.',
		'uc_cupdt_header_fnsh'			=> 'The character was successfully updated. The window can now be closed.',
		'uc_importcache_cleared'		=> 'The cache of the importer was successfully cleared.',
		'uc_startdkp'					=> 'Set Start-DKP',
		'uc_startdkp_adjreason'			=> 'Start-DKP',
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

		'base'							=> 'Attributes',
		'strength'						=> 'Strength',
		'agility'						=> 'Agility',
		'stamina'						=> 'Stamina',
		'intellect'						=> 'Intellect',
		'spirit'						=> 'Spirit',

		'melee'							=> 'Melee',
		'mainHandDamage'				=> 'Main Hand Damage',
		'mainHandDps'					=> 'DPS',
		'mainHandSpeed'					=> 'Main Hand Speed',
		'power'							=> 'Attack Power',
		'hasteRating'					=> 'Haste rating',
		'hitPercent'					=> 'Hit rating',
		'critChance'					=> 'Crit rating',
		'expertise'						=> 'Expertise rating',
		'mastery'						=> 'Mastery rating',

		'range'							=> 'Ranged',
		'damage'						=> 'Damage',
		'rangedDps'						=> 'DPS',
		'rangedSpeed'					=> 'Speed',

		'spell'							=> 'Spell',
		'spellpower'					=> 'Spell Power',
		'spellHit'						=> 'Hit rating ',
		'spellCrit'						=> 'Crit rating',
		'spellPen'						=> 'Spell Penetration',
		'manaRegen'						=> 'Mana Regeneration',
		'combatRegen'					=> 'Combat Regeneration',

		'defenses'						=> 'Defense',
		'armor'							=> 'Armor',
		'dodge'							=> 'Dodge',
		'parry'							=> 'Parry',
		'block'							=> 'Block',
		'pvpresil'						=> 'PVP-Resilience',
		'pvppower'						=> 'PVP-Power',
		'all'							=> 'All Attributes',

		'achievements'					=> 'Achievements',
		'achievement_points'			=> 'Achievement points',
		'total'							=> 'Total',
		'health'						=> 'Health',
		'last5achievements'				=> '5 most recent achievements',

		'charnewsfeed'					=> 'Last activities',
		'charnf_achievement'			=> 'Earned the achievement %s for %s points.',
		'charnf_achievement_hero'		=> 'Earned the feat of strength %s.',
		'charnf_item'					=> 'Obtained %s',
		'charnf_bosskill'				=> '%s %s',
		'charnf_criteria'				=> 'Completed step %s of achievement %s.',
		'avg_itemlevel'					=> 'Average item level',
		'avg_itemlevel_equiped'			=> 'Equiped item level',

		// bossprogress
		'bossprogress_normalruns'		=> '%sx normal',
		'bossprogress_heroicruns'		=> '%sx heroic',

		'mop'							=> 'Mists of Pandarian',
		'wotlk'							=> 'Wrath of the Lich King',
		'cataclysm'						=> 'Cataclysm',
		'burning_crusade'				=> 'Burning Crusade',
		'classic'						=> 'Classic',
		
		'mop_mogushan_10'				=> 'Mogu\'shan Vaults (10)',
		'mop_mogushan_25'				=> 'Mogu\'shan Vaults (25)',
		'mop_heartoffear_10'			=> 'Heart of Fear (10)',
		'mop_heartoffear_25'			=> 'Heart of Fear (25)',
		'mop_endlessspring_10'			=> 'Terrace of Endless Spring (10)',
		'mop_endlessspring_25'			=> 'Terrace of Endless Spring (25)',
		'mop_throneofthunder_10'		=> 'Throne of Thunder (10)',
		'mop_throneofthunder_25'		=> 'Throne of Thunder (25)',
		'mop_siegeoforgrimmar'			=> 'Siege of Orgrimmar',

		'char_news'						=> 'Char News',
		'no_armory'						=> 'The data for this char could not be loaded. The battle.net API returned an error: "%s".',
		'no_realm'						=> 'To use the whole functionality please enter a valid World of Warcraft game server name in administrator settings.',

		'guildachievs_total_completed'	=> 'Total Completed',
		'latest_guildachievs'			=> 'Recently Earned',
		'guildnews'						=> 'Guildnews',
		'news_guildCreated'				=> 'The guild was founded.',
		'news_itemLoot'					=> '%1$s obtained %2$s',
		'news_itemPurchase'				=> '%1$s purchased item %2$s',
		'news_guildLevel'				=> 'The guild reached level %s.',
		'news_guildAchievement'			=> 'The guild earned the achievement %1$s for %2$s points.',
		'news_playerAchievement'		=> '%1$s earned the achievement %2$s for %3$s points.',

		'not_assigned'					=> 'Not assigned',
		'empty'							=> 'Empty',
		'major_glyphs'					=> 'Major Glyphs',
		'minor_glyphs'					=> 'Minor Glyphs',

	),

	'realmlist' => array('Eldre\'Thalas','Spirestone','Shadow Council','Scarlet Crusade','Firetree','Frostmane','Gurubashi','Smolderthorn','Skywall','Windrunner','Nathrezim','Terenas','Arathor','Bonechewer','Dragonmaw','Shadowsong','Silvermoon','Crushridge','Stonemaul','Daggerspine','Stormscale','Dunemaul','Boulderfist','Suramar','Dragonblight','Draenor','Uldum','Bronzebeard','Feathermoon','Bloodscalp','Darkspear','Azjol-Nerub','Perenolde','Argent Dawn','Azgalor','Magtheridon','Trollbane','Gallywix','Madoran','Stormrage','Zul\'jin','Medivh','Durotan','Bloodhoof','Elune','Lothar','Arthas','Mannoroth','Warsong','Shattered Hand','Bleeding Hollow','Skullcrusher','Burning Blade','Gorefiend','Eredar','Shadowmoon','Lightning\'s Blade','Eonar','Gilneas','Kargath','Llane','Earthen Ring','Laughing Skull','Burning Legion','Thunderlord','Malygos','Drakkari','Aggramar','Thunderhorn','Ragnaros','Quel\'Thalas','Dreadmaul','Caelestrasz','Kilrogg','Proudmoore','Nagrand','Frostwolf','Ner\'zhul','Kil\'jaeden','Blackrock','Tichondrius','Silver Hand','Aman\'Thul','Barthilas','Thaurissan','Dath\'Remar','Frostmourne','Khaz\'goroth','Vek\'nilash','Sen\'jin','Aegwynn','Akama','Chromaggus','Draka','Drak\'thul','Garithos','Hakkar','Khaz Modan','Jubei\'Thos','Mug\'thol','Korgath','Kul Tiras','Malorne','Gundrak','Eitrigg','Rexxar','Muradin','Saurfang','Thorium Brotherhood','Runetotem','Garona','Alleria','Hellscream','Blackhand','Whisperwind','Cho\'gall','Illidan','Stormreaver','Gul\'dan','Kael\'thas','Alexstrasza','Kirin Tor','Ravencrest','Goldrinn','Nemesis','Balnazzar','Destromath','Gorgonnash','Dethecus','Spinebreaker','Moonrunner','Sargeras','Kalecgos','Ursin','Dark Iron','Greymane','Wildhammer','Detheroc','Staghelm','Emerald Dream','Maelstrom','Twisting Nether','Azshara','Agamaggan','Lightninghoof','Nazjatar','Malfurion','Baelgun','Azralon','Tol Barad','Duskwood','Zuluhed','Steamwheedle Cartel','Mal\'Ganis','Norgannon','Archimonde','Anetheron','Turalyon','Haomarush','Scilla','Ysondre','Thrall','Ysera','Dentarg','Khadgar','Dalaran','Dalvengyr','Black Dragonflight','Andorhal','Executus','Doomhammer','Icecrown','Deathwing','Kel\'Thuzad','Altar of Storms','Uldaman','Aerie Peak','Onyxia','Demon Soul','Gnomeregan','Anvilmar','The Venture Co','Sentinels','Jaedenar','Tanaris','Alterac Mountains','Undermine','Lethon','Blackwing Lair','Arygos','Lightbringer','Cenarius','Uther','Cenarion Circle','Echo Isles','Hyjal','The Forgotten Coast','Fenris','Anub\'arak','Blackwater Raiders','Vashj','Korialstrasz','Misha','Darrowmere','Ravenholdt','Bladefist','Shu\'halo','Winterhoof','Sisters of Elune','Maiev','Rivendare','Nordrassil','Tortheldrin','Cairne','Drak\'Tharon','Antonidas','Shandris','Moon Guard','Nazgrel','Hydraxis','Wyrmrest Accord','Farstriders','Borean Tundra','Quel\'dorei','Garrosh','Mok\'Nathal','Nesingwary','Drenden','Terokkar','Blade\'s Edge','Exodar','Area 52','Velen','Azuremyst','Auchindoun','The Scryers','Coilfang','Zangarmarsh','Shattered Halls','Blood Furnace','The Underbog','Fizzcrank','Ghostlands','Grizzly Hills','Galakrond','Dawnbringer','Aszune','Sunstrider','Twilight\'s Hammer','Zenedar','Aggra (Português)','Al\'Akir','Sinstralis','Madmortem','Nozdormu','Die Silberne Hand','Zirkel des Cenarius','Dun Morogh','Theradras','Genjuros','Wrathbringer','Nera\'thor','Kult der Verdammten','Das Syndikat','Terrordar','Krag\'jin','Der Rat von Dalaran','Neptulon','The Maelstrom','Sylvanas','Bloodfeather','Darksorrow','Frostwhisper','Defias Brotherhood','Drek\'Thar','Rashgarroth','Throk\'Feroth','Conseil des Ombres','Varimathras','Les Sentinelles','Moonglade','Mazrigos','Talnivarr','Emeriss','Ahn\'Qiraj','Nefarian','Blackmoore','Xavius','Die ewige Wacht','Die Todeskrallen','Scarshield Legion','Die Arguswacht','Outland','Grim Batol','Kazzak','Tarren Mill','Chamber of Aspects','Pozzo dell\'Eternità','Vek\'lor','Taerar','Rajaxx','Ulduar','Der abyssische Rat','Lordaeron','Tirion','Ambossar','Krasus','Die Nachtwache','Arathi','Culte de la Rive noire','Dun Modr','C\'Thun','Sanguino','Shen\'dralar','Tyrande','Minahonda','Los Errantes','Darkmoon Faire','Alonsus','Burning Steppes','Bronze Dragonflight','Anachronos','Colinas Pardas','Kor\'gall','Forscherliga','Un\'Goro','Todeswache','Teldrassil','Der Mithrilorden','Vol\'jin','Arak-arahm','La Croisade écarlate','Confrérie du Thorium','Hellfire','Azuregos','Ashenvale','Booty Bay','Eversong','Thermaplugg','The Sha\'tar','Karazhan','Grom','Blackscar','Gordunni','Lich King','Soulflayer','Deathguard','Sporeggar','Nethersturm','Shattrath','Festung der Stürme','Echsenkessel','Blutkessel','Deepholm','Howling Fjord','Razuvious','Deathweaver','Die Aldor','Das Konsortium','Chants éternels','Marécage de Zangar','Temple noir','Fordragon','Naxxramas','Les Clairvoyants'),
);
?>