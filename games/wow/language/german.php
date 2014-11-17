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
$german_array = array(
	'classes' => array(
		0	=> 'Unbekannt',
		1	=> 'Todesritter',
		2	=> 'Druide',
		3	=> 'Jäger',
		4	=> 'Magier',
		5	=> 'Paladin',
		6	=> 'Priester',
		7	=> 'Schurke',
		8	=> 'Schamane',
		9	=> 'Hexenmeister',
		10	=> 'Krieger',
		11	=> 'Mönch'
	),
	'races' => array(
		'Unknown',
		'Gnom',
		'Mensch',
		'Zwerg',
		'Nachtelf',
		'Troll',
		'Untoter',
		'Ork',
		'Taure',
		'Draenei',
		'Blutelf',
		'Worg',
		'Goblin',
		'Pandaren'
	),
	'factions' => array(
		'alliance'	=> 'Allianz',
		'horde'		=> 'Horde'
	),
	'roles' => array(
		1 => array(2, 5, 6, 8, 11),
		2 => array(1, 2, 5, 10, 11),
		3 => array(2, 3, 4, 6, 8, 9),
		4 => array(1, 2, 5, 7, 8, 10, 11)
	),
	'professions' => array(
		'trade_alchemy'					=> 'Alchemie',
		'trade_blacksmithing'			=> 'Schmiedekunst',
		'trade_engraving'				=> 'Verzauberkunst',
		'trade_engineering'				=> 'Ingenieurskunst',
		'trade_herbalism'				=> 'Kräuterkunde',
		'inv_inscription_tradeskill01'	=> 'Inschriftenkunde',
		'inv_misc_gem_01'				=> 'Juwelenschleifen',
		'trade_leatherworking'			=> 'Lederverarbeitung',
		'inv_pick_02'					=> 'Bergbau',
		'inv_misc_pelt_wolf_01'			=> 'Kürschnerei',
		'trade_tailoring'				=> 'Schneiderei',
	),
	'lang' => array(
		'wow'			=> 'World of Warcraft',
		'plate'			=> 'Platte',
		'cloth'			=> 'Stoff',
		'leather'		=> 'Leder',
		'mail'			=> 'Schwere Rüstung',
		'tier_token'	=> 'Token: ',
		'talents_tt_1'	=> 'Primäres Talent',
		'talents_tt_2'	=> 'Sekundäres Talent',
		'talents'		=> array(
			1	=> array('Blut','Frost','Unheilig'),
			2	=> array('Gleichgewicht','Wildheit','Wächter','Wiederherstellung'),
			3	=> array('Tierherrschaft','Treffsicherheit','Überleben'),
			4	=> array('Arkan','Feuer','Frost'),
			5	=> array('Heilig','Schutz','Vergeltung'),
			6	=> array('Disziplin','Heilig','Schatten'),
			7	=> array('Meucheln','Kampf','Täuschung'),
			8	=> array('Elementar','Verstärkung','Wiederherstellung'),
			9	=> array('Gebrechen','Dämonologie','Zerstörung'),
			10	=> array('Waffen','Furor','Schutz'),
			11	=> array('Braumeister','Nebelwirker','Windläufer'),
		),
		'role1'							=> 'Heiler',
		'role2'							=> 'Tank',
		'role3'							=> 'DD Fernkampf',
		'role4'							=> 'DD Nahkampf',
		
		// Profile information
		'uc_prof_professions'			=> 'Berufe',
		'skills'						=> 'Talente',
		'corevalues'					=> 'Grundwerte',
		'values'						=> 'Werte',

		// Profile information
		'uc_achievements'				=> 'Erfolge',
		'uc_bosskills'					=> 'Boss Kills',
		'uc_bar_rage'					=> 'Wut',
		'uc_bar_energy'					=> 'Energie',
		'uc_bar_mana'					=> 'Mana',
		'uc_bar_focus'					=> 'Fokus',
		'uc_bar_runic-power'			=> 'Runenmacht',

		'uc_skill1'						=> 'Talente 1',
		'uc_skill2'						=> 'Talente 2',

		'pv_tab_profiles'				=> 'Externe Profile',
		'pv_tab_talents'				=> 'Skillung',

		'uc_guild'						=> 'Gilde',
		'uc_bar_health'					=> 'Gesundheit',
		'uc_bar_2value'					=> 'Wert der 2. Leiste',
		'uc_bar_2name'					=> 'Name der 2. Leiste',

		'uc_gender'						=> 'Geschlecht',
		'uc_male'						=> 'Männlich',
		'uc_female'						=> 'Weiblich',
		'uc_faction'					=> 'Fraktion',
		'uc_faction_help'				=> 'Die Fraktion im Spiel',
		'uc_fact_horde'					=> 'Horde',
		'uc_fact_alliance'				=> 'Allianz',

		'uc_prof1_value'				=> 'Level des Hauptberufes',
		'uc_prof1_name'					=> 'Name des Hauptberufes',
		'uc_prof2_value'				=> 'Level des Sekundärberufes',
		'uc_prof2_name'					=> 'Name des Sekundärberufs',

		'uc_achievement_tab_default'	=> 'Ungruppiert',
		'uc_achievement_tab_classic'	=> 'Classic',
		'uc_achievement_tab_bc'			=> 'Burning Crusade',
		'uc_achievement_tab_wotlk'		=> 'Wrath of the Lich King',
		'uc_achievement_tab_cataclysm'	=> 'Cataclysm',
		'uc_achievement_tab_mop'		=> 'Mists of Pandaria',
		'uc_achievement_tab_wod'		=> 'Warlords of Draenor',
		
		'challenge'						=> 'Herausforderungsmodus',
		'challenge_title'				=> 'Herausforderungsmodus: Ranglisten',
		'off_realm_toon'				=> 'Dieser Charakter scheint nicht in deiner Gilde zu sein. Da die Herausforderungen realmübergreifend sind, können auch fremde CHarakter in dieser Liste auftauchen.',

		// Profile Admin area
		'pk_tab_fs_wowsettings'			=> 'WoW Einstellungen',
		'importer_head_txt'				=> 'battle.net Importer',
		'uc_servername_help'			=> 'Servername des Spielservers (z.B. Mal\'Ganis)',
		'uc_update_all'					=> 'Von battle.net aktualisieren',
		'uc_update_all_help'			=> 'Alle Profilinformationen mit Profilerdaten von battle.net aktualisieren',
		'uc_importer_cache'				=> 'Leere Cache des Importers',
		'uc_importer_cache_help'		=> 'Löscht alle gecachten Daten aus der importer Class.',
		'uc_import_guild'				=> 'Gilde vom battle.net importieren',
		'uc_import_guild_help'			=> 'Importiere alle Mitglieder einer Gilde vom battle.net',
		'uc_server_loc'					=> 'Server Standort',
		'uc_server_loc_help'			=> 'Der Standort des WoW Game Servers',
		'uc_data_lang'					=> 'Sprache der Daten',
		'uc_data_lang_help'				=> 'In welcher Sprache sollen die Daten vom externen Anbieter geladen werden?',
		'uc_error_head'					=> 'FEHLER',
		'uc_error_noserver'				=> 'Es wurde kein Server in den globalen Einstellungen gefunden. Dieser wird für die Nutzung dieses Features jedoch benötigt. Bitte benachrichtige einen Administrator.',
	
		// Armory Import
		"uc_updat_armory" 				=> "Vom battle.net aktualisieren",
		'uc_charname'					=> 'Charaktername',
		'uc_servername'					=> 'Realm-Name',
		'uc_charfound'					=> "Der Charakter  <b>%1\$s</b> wurde im battle.net gefunden.",
		'uc_charfound2'					=> "Das letzte Update dieses Charakters war am <b>%1\$s</b>.",
		'uc_charfound3'					=> 'ACHTUNG: Beim Import werden bisher gespeicherte Daten überschrieben!',
		'uc_armory_imported'			=> 'Charakter erfolgreich importiert',
		'uc_armory_updated'				=> 'Charakter erfolgreich aktualisiert',
		'uc_armory_impfailed'			=> 'Charakter nicht importiert',
		'uc_armory_updfailed'			=> 'Charakter nicht aktualisiert',
		'uc_armory_impfail_reason'		=> 'Grund:',
		'uc_armory_impduplex'			=> 'Charakter ist bereits vorhanden',
		
		// guild importer
		'uc_class_filter'				=> 'Klasse',
		'uc_class_nofilter'				=> 'Nicht filtern',
		'uc_guild_name'					=> 'Name der Gilde',
		'uc_filter_name'				=> 'Filter',
		'uc_level_filter'				=> 'Level größer als',
		'uc_rank_filter1a'				=> 'höher als',
		'uc_rank_filter1b'				=> 'gleich',
		'uc_rank_filter'				=> 'Rang',
		'uc_imp_noguildname'			=> 'Es wurde kein Gildenname angegeben',
		'uc_gimp_loading'				=> 'Gildenmitglieder werden geladen, bitte warten...',
		'uc_massupd_loading'			=> 'Charaktere werden aktualisiert, bitte warten...',
		'uc_gimp_header_fnsh'			=> 'Der Import der Gildenmitglieder wurde beendet. Beim Gildenimport werden nur der Charktername, die Rasse, die Klasse und das Level importiert. Um die restlichen Daten zu importieren, einfach den battle.net Updater benutzen.',
		'uc_cupdt_header_fnsh'			=> 'Die Aktualisierung der Charaktere wurde beendet. Das Fenster kann nun geschlossen werden.',
		'uc_importcache_cleared'		=> 'Der Cache des Importers wurde erfolgreich geleert.',
		'uc_startdkp'					=> 'Start-DKP vergeben',
		'uc_startdkp_adjreason'			=> 'Start-DKP',
		'uc_delete_chars_onimport'		=> 'Charaktere im System löschen, die nicht mehr in der Gilde sind',

		'uc_noprofile_found'			=> 'Kein Profil gefunden',
		'uc_profiles_complete'			=> 'Profile erfolgreich aktualisiert',
		'uc_notyetupdated'				=> 'Keine neuen Daten (Inaktiver Charakter)',
		'uc_notactive'					=> 'Das Mitglied ist im EQDKP auf inaktiv gesetzt und wird daher übersprungen',
		'uc_error_with_id'				=> 'Fehler mit der Member ID, Charakter übersprungen',
		'uc_notyourchar'				=> 'ACHTUNG: Du versuchst gerade einen Charakter hinzuzufügen, der bereits in der Datenbank vorhanden ist und dir nicht zugewiesen ist. Aus Sicherheitsgründen ist diese Aktion nicht gestattet. Bitte kontaktiere einen Administrator zum Lösen dieses Problems oder versuche einen anderen Charakternamen einzugeben.',
		'uc_lastupdate'					=> 'Letzte Aktualisierung',

		'uc_prof_import'				=> 'importieren',
		'uc_import_forw'				=> 'Start',
		'uc_imp_succ'					=> 'Die Daten wurden erfolgreich importiert',
		'uc_upd_succ'					=> 'Die Daten wurden erfolgreich aktualisiert',
		'uc_imp_failed'					=> 'Beim Import der Daten trat ein Fehler auf. Bitte versuche es erneut.',

		'base'							=> 'Attribute',
		'strength'						=> 'Stärke',
		'agility'						=> 'Beweglichkeit',
		'stamina'						=> 'Ausdauer',
		'intellect'						=> 'Intelligenz',
		'spirit'						=> 'Willenskraft',

		'melee'							=> 'Nahkampf',
		'mainHandDamage'				=> 'Schaden',
		'mainHandDps'					=> 'DPS',
		'mainHandSpeed'					=> 'Geschwindigkeit',
		'power'							=> 'Angriffskraft',
		'hasteRating'					=> 'Tempowertung',
		'hitPercent'					=> 'Trefferwertung',
		'critChance'					=> 'Kritische Trefferwertung',
		'expertise'						=> 'Waffenkundewertung',
		'mastery'						=> 'Meisterschaftswertung',

		'range'							=> 'Distanzkampf',
		'damage'						=> 'Schaden',
		'rangedDps'						=> 'DPS',
		'rangedSpeed'					=> 'Geschwindigkeit',

		'spell'							=> 'Zauber',
		'spellpower'					=> 'Zaubermacht',
		'spellHit'						=> 'Trefferchance',
		'spellCrit'						=> 'Kritische Trefferchance',
		'spellPen'						=> 'Zauberdurchschlagskraft',
		'manaRegen'						=> 'Manaegeneration',
		'combatRegen'					=> 'Kampfregeneration',

		'defenses'						=> 'Verteidigung',
		'armor'							=> 'Rüstung',
		'dodge'							=> 'Ausweichen',
		'parry'							=> 'Parieren',
		'block'							=> 'Blocken',
		'pvpresil'						=> 'PVP-Abhärtung',
		'pvppower'						=> 'PVP-Macht',
		'all'							=> 'Alle Werte',

		'achievements'					=> 'Erfolge',
		'achievement_points'			=> 'Erfolgspunkte',
		'total'							=> 'Gesamt',
		'health'						=> 'Leben',
		'last5achievements'				=> 'Die letzten 5 Erfolge',

		'charnewsfeed'					=> 'Letzte Aktivitäten',
		'charnf_achievement'			=> 'Erfolg %s für %s Punkte errungen.',
		'charnf_achievement_hero'		=> 'Heldentat %s errungen.',
		'charnf_item'					=> 'Erhalten %s',
		'charnf_bosskill'				=> '%s %s',
		'charnf_criteria'				=> 'Schritt %s des Erfolgs %s abgeschlossen.',
		'avg_itemlevel'					=> 'Durchschnittliche Gegenstandsstufe',
		'avg_itemlevel_equiped'			=> 'ausgerüstet',

		// bossprogress
		'bossprogress_normalruns'		=> '%sx normal',
		'bossprogress_heroicruns'		=> '%sx heroisch',

		'mop'							=> 'Mists of Pandarian',
		'wotlk'							=> 'Wrath of the Lich King',
		'cataclysm'						=> 'Cataclysm',
		'burning_crusade'				=> 'Burning Crusade',
		'classic'						=> 'Classic',
		
		'mop_mogushan_10'				=> 'Mogu\'shangewölbe (10)',
		'mop_mogushan_25'				=> 'Mogu\'shangewölbe (25)',
		'mop_heartoffear_10'			=> 'Das Herz der Angst (10)',
		'mop_heartoffear_25'			=> 'Das Herz der Angst (25)',
		'mop_endlessspring_10'			=> 'Terrasse des Endlosen Frühlings (10)',
		'mop_endlessspring_25'			=> 'Terrasse des Endlosen Frühlings (25)',
		'mop_throneofthunder_10'		=> 'Thron des Donners (10)',
		'mop_throneofthunder_25'		=> 'Thron des Donners (25)',
		'mop_siegeoforgrimmar'			=> 'Schlacht um Orgrimmar',

		'char_news'						=> 'Char News',
		'no_armory'						=> 'Es konnten keine gültigen Daten für diesen Charakter geladen werden. Die battle.net API meldet folgenden Fehler: "%s".',
		'no_realm'						=> 'Um den vollen Funktionsumfang dieser Seite nutzen zu können, muss in den Administrator-Einstellungen ein gültiger World of Warcraft Server hinterlegt werden.',
		
		'guildachievs_total_completed'	=> 'Vollständig abgeschlossen',
		'latest_guildachievs'			=> 'Kürzlich erhalten',
		'guildnews'						=> 'Gildennews',
		'news_guildCreated'				=> 'Gilde wurde gegründet',
		'news_itemLoot'					=> '%1$s erhielt %2$s',
		'news_itemPurchase'				=> '%1$s erwarb den Gegenstand: %2$s',
		'news_guildLevel'				=> 'Die Gilde hat Level %s erreicht',
		'news_guildAchievement'			=> 'Die Gilde hat den Erfolg %1$s für %2$s Punkte errungen.',
		'news_playerAchievement'		=> '%1$s hat den Erfolg %2$s für %3$s Punkte errungen.',

		'not_assigned'					=> 'Nicht verteilt',
		'empty'							=> 'Leer',
		'major_glyphs'					=> 'Erhebliche Glyphen',
		'minor_glyphs'					=> 'Geringe Glyphen',
	),

	'realmlist' => array('Eldre\'Thalas','Spirestone','Shadow Council','Scarlet Crusade','Firetree','Frostmane','Gurubashi','Smolderthorn','Skywall','Windrunner','Nathrezim','Terenas','Arathor','Bonechewer','Dragonmaw','Shadowsong','Silvermoon','Crushridge','Stonemaul','Daggerspine','Stormscale','Dunemaul','Boulderfist','Suramar','Dragonblight','Draenor','Uldum','Bronzebeard','Feathermoon','Bloodscalp','Darkspear','Azjol-Nerub','Perenolde','Argent Dawn','Azgalor','Magtheridon','Trollbane','Gallywix','Madoran','Stormrage','Zul\'jin','Medivh','Durotan','Bloodhoof','Elune','Lothar','Arthas','Mannoroth','Warsong','Shattered Hand','Bleeding Hollow','Skullcrusher','Burning Blade','Gorefiend','Eredar','Shadowmoon','Lightning\'s Blade','Eonar','Gilneas','Kargath','Llane','Earthen Ring','Laughing Skull','Burning Legion','Thunderlord','Malygos','Drakkari','Aggramar','Thunderhorn','Ragnaros','Quel\'Thalas','Dreadmaul','Caelestrasz','Kilrogg','Proudmoore','Nagrand','Frostwolf','Ner\'zhul','Kil\'jaeden','Blackrock','Tichondrius','Silver Hand','Aman\'Thul','Barthilas','Thaurissan','Dath\'Remar','Frostmourne','Khaz\'goroth','Vek\'nilash','Sen\'jin','Aegwynn','Akama','Chromaggus','Draka','Drak\'thul','Garithos','Hakkar','Khaz Modan','Jubei\'Thos','Mug\'thol','Korgath','Kul Tiras','Malorne','Gundrak','Eitrigg','Rexxar','Muradin','Saurfang','Thorium Brotherhood','Runetotem','Garona','Alleria','Hellscream','Blackhand','Whisperwind','Cho\'gall','Illidan','Stormreaver','Gul\'dan','Kael\'thas','Alexstrasza','Kirin Tor','Ravencrest','Goldrinn','Nemesis','Balnazzar','Destromath','Gorgonnash','Dethecus','Spinebreaker','Moonrunner','Sargeras','Kalecgos','Ursin','Dark Iron','Greymane','Wildhammer','Detheroc','Staghelm','Emerald Dream','Maelstrom','Twisting Nether','Azshara','Agamaggan','Lightninghoof','Nazjatar','Malfurion','Baelgun','Azralon','Tol Barad','Duskwood','Zuluhed','Steamwheedle Cartel','Mal\'Ganis','Norgannon','Archimonde','Anetheron','Turalyon','Haomarush','Scilla','Ysondre','Thrall','Ysera','Dentarg','Khadgar','Dalaran','Dalvengyr','Black Dragonflight','Andorhal','Executus','Doomhammer','Icecrown','Deathwing','Kel\'Thuzad','Altar of Storms','Uldaman','Aerie Peak','Onyxia','Demon Soul','Gnomeregan','Anvilmar','The Venture Co','Sentinels','Jaedenar','Tanaris','Alterac Mountains','Undermine','Lethon','Blackwing Lair','Arygos','Lightbringer','Cenarius','Uther','Cenarion Circle','Echo Isles','Hyjal','The Forgotten Coast','Fenris','Anub\'arak','Blackwater Raiders','Vashj','Korialstrasz','Misha','Darrowmere','Ravenholdt','Bladefist','Shu\'halo','Winterhoof','Sisters of Elune','Maiev','Rivendare','Nordrassil','Tortheldrin','Cairne','Drak\'Tharon','Antonidas','Shandris','Moon Guard','Nazgrel','Hydraxis','Wyrmrest Accord','Farstriders','Borean Tundra','Quel\'dorei','Garrosh','Mok\'Nathal','Nesingwary','Drenden','Terokkar','Blade\'s Edge','Exodar','Area 52','Velen','Azuremyst','Auchindoun','The Scryers','Coilfang','Zangarmarsh','Shattered Halls','Blood Furnace','The Underbog','Fizzcrank','Ghostlands','Grizzly Hills','Galakrond','Dawnbringer','Aszune','Sunstrider','Twilight\'s Hammer','Zenedar','Aggra (Português)','Al\'Akir','Sinstralis','Madmortem','Nozdormu','Die Silberne Hand','Zirkel des Cenarius','Dun Morogh','Theradras','Genjuros','Wrathbringer','Nera\'thor','Kult der Verdammten','Das Syndikat','Terrordar','Krag\'jin','Der Rat von Dalaran','Neptulon','The Maelstrom','Sylvanas','Bloodfeather','Darksorrow','Frostwhisper','Defias Brotherhood','Drek\'Thar','Rashgarroth','Throk\'Feroth','Conseil des Ombres','Varimathras','Les Sentinelles','Moonglade','Mazrigos','Talnivarr','Emeriss','Ahn\'Qiraj','Nefarian','Blackmoore','Xavius','Die ewige Wacht','Die Todeskrallen','Scarshield Legion','Die Arguswacht','Outland','Grim Batol','Kazzak','Tarren Mill','Chamber of Aspects','Pozzo dell\'Eternità','Vek\'lor','Taerar','Rajaxx','Ulduar','Der abyssische Rat','Lordaeron','Tirion','Ambossar','Krasus','Die Nachtwache','Arathi','Culte de la Rive noire','Dun Modr','C\'Thun','Sanguino','Shen\'dralar','Tyrande','Minahonda','Los Errantes','Darkmoon Faire','Alonsus','Burning Steppes','Bronze Dragonflight','Anachronos','Colinas Pardas','Kor\'gall','Forscherliga','Un\'Goro','Todeswache','Teldrassil','Der Mithrilorden','Vol\'jin','Arak-arahm','La Croisade écarlate','Confrérie du Thorium','Hellfire','Azuregos','Ashenvale','Booty Bay','Eversong','Thermaplugg','The Sha\'tar','Karazhan','Grom','Blackscar','Gordunni','Lich King','Soulflayer','Deathguard','Sporeggar','Nethersturm','Shattrath','Festung der Stürme','Echsenkessel','Blutkessel','Deepholm','Howling Fjord','Razuvious','Deathweaver','Die Aldor','Das Konsortium','Chants éternels','Marécage de Zangar','Temple noir','Fordragon','Naxxramas','Les Clairvoyants'),
);
?>