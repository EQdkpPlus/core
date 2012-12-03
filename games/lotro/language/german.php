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
		0 => 'Unbekannt',
		1 => 'Barde',
		2 => 'Hauptmann',
		3 => 'Jäger',
		4 => 'Kundiger',
		5 => 'Schurke',
		6 => 'Wächter',
		7 => 'Waffenmeister',
		8 => 'Runenbewahrer',
		9 => 'Hüter',
	),
	'races' => array(
		'Unknown',
		'Mensch',
		'Hobbit',
		'Elb',
		'Zwerg'
	),
	'factions' => array(
		'Freie Völker',
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
		'lotro' => 'Der Herr der Ringe Online',
		'heavy' => 'Schwere Rüstung',
		'medium' => 'Mittlere Rüstung',
		'light' => 'Leichte Rüstung',
		'role1' => 'Heiler',
		'role2' => 'Tank',
		'role3' => 'Crowd Control',
		'role4' => 'Damage Dealer',
		'role5' => 'Supporter',

		// Profile Admin area
		'pk_tab_fs_lotrosettings'		=> 'LOTRO Einstellungen',
		'uc_faction'					=> 'Fraktion',
		'uc_faction_help'				=> 'Die Fraktion im Spiel',
		'uc_fact_pvp'					=> 'MonsterPlay',
		'uc_fact_pve'					=> 'Freie Völker',
		'uc_server_loc'					=> 'Server Standort',
		'uc_server_loc_help'			=> 'Der Standort des LOTRO-Servers',
		'uc_servername'					=> 'Servername',
		'uc_servername_help'			=> 'Servername des Spielservers (z.B. Bullroarer)',		
		'uc_lockserver'					=> 'Servername unveränderbar machen',
		'uc_lockserver_help'			=> 'Der Servername für den Benutzer unveränderbar machen',
		'uc_importer_cache'				=> 'Leere Cache des Importers',
		'uc_importer_cache_help'		=> 'Löscht alle gecachten Daten aus der importer Class.',
		
		'uc_import_guild'				=> 'Gilde importieren',
		'uc_import_guild_help'			=> 'Importiere alle Mitglieder einer Gilde',
		'uc_importer_cache'				=> 'Leere Cache des Importers',
		'uc_importer_cache_help'		=> 'Löscht alle gecachten Daten aus der importer Class.',
		'uc_update_all'					=> 'Von MyLotro aktualisieren',
		'uc_update_all_help'			=> 'Alle Profilinformationen mit Profilerdaten von MyLotro aktualisieren',
		
		'uc_class_filter'				=> 'Klasse',
		'uc_class_nofilter'				=> 'Nicht filtern',
		'uc_guild_name'					=> 'Name der Gilde',
		'uc_filter_name'				=> 'Filter',
		'uc_level_filter'				=> 'Level größer als',
		'uc_imp_noguildname'			=> 'Es wurde kein Gildenname angegeben',
		'uc_gimp_loading'				=> 'Gildenmitglieder werden geladen, bitte warten...',
		'uc_gimp_header_fnsh'			=> 'Der Import der Gildenmitglieder wurde beendet. Beim Gildenimport werden nur der Charktername, die Rasse, die Klasse und das Level importiert. Um die restlichen Daten zu importieren, einfach den Updater benutzen.',
		'uc_importcache_cleared'		=> 'Der Cache des Importers wurde erfolgreich geleert.',
		
		'uc_noprofile_found'			=> 'Kein Profil gefunden',
		'uc_profiles_complete'			=> 'Profile erfolgreich aktualisiert',
		'uc_notyetupdated'				=> 'Keine neuen Daten (Inaktiver Charakter)',
		'uc_notactive'					=> 'Das Mitglied ist im EQDKP auf inaktiv gesetzt und wird daher übersprungen',
		'uc_error_with_id'				=> 'Fehler mit der Charakter ID, Charakter übersprungen',
		'uc_notyourchar'				=> 'ACHTUNG: Du versuchst gerade einen Charakter hinzuzufügen, der bereits in der Datenbank vorhanden ist und dir nicht zugewiesen ist. Aus Sicherheitsgründen ist diese Aktion nicht gestattet. Bitte kontaktiere einen Administrator zum Lösen dieses Problems oder versuche einen anderen Charakternamen einzugeben.',
		'uc_lastupdate'					=> 'Letzte Aktualisierung',
		"uc_updat_armory" 				=> "Von MyLotro aktualisieren",

		'uc_prof_import'				=> 'importieren',
		'uc_import_forw'				=> 'Start',
		'uc_imp_succ'					=> 'Die Daten wurden erfolgreich importiert',
		'uc_upd_succ'					=> 'Die Daten wurden erfolgreich aktualisiert',
		'uc_imp_failed'					=> 'Beim Import der Daten trat ein Fehler auf. Bitte versuche es erneut.',
		'uc_charname'					=> 'Charaktername',
		'uc_servername'					=> 'Servername',
		'uc_charfound'					=> "Der Charakter  <b>%1\$s</b> wurde gefunden.",
		'uc_charfound2'					=> "Das letzte Update dieses Charakters war am <b>%1\$s</b>.",
		'uc_charfound3'					=> 'ACHTUNG: Beim Import werden bisher gespeicherte Daten überschrieben!',
		'uc_armory_imported'			=> 'Charakter erfolgreich importiert',
		'uc_armory_updated'				=> 'Charakter erfolgreich aktualisiert',
		'uc_armory_impfailed'			=> 'Charakter nicht importiert',
		'uc_armory_updfailed'			=> 'Charakter nicht aktualisiert',
		'uc_armory_impfail_reason'		=> 'Grund:',
		'uc_armory_impduplex'			=> 'Charakter ist bereits vorhanden',
		
		'no_data'						=> 'Zu diesem Char konnten keine Informationen abgerufen werden. Bitte überprüfe ob der richtige Server in den Einstellungen eingestellt ist.',
		
		'vocation'						=> 'Berufung',
		'profession1'					=> 'Erster Beruf',
		'profession2'					=> 'Zweiter Beruf',
		'profession3'					=> 'Dritter Beruf',
		'profession1_proficiency'		=> 'Fachmann-Stufe erster Beruf',
		'profession2_proficiency'		=> 'Fachmann-Stufe zweiter Beruf',
		'profession3_proficiency'		=> 'Fachmann-Stufe dritter Beruf',
		'profession1_mastery'			=> 'Meister-Stufe erster Beruf',
		'profession2_mastery'			=> 'Meister-Stufe zweiter Beruf',
		'profession3_mastery'			=> 'Meister-Stufe dritter Beruf',
	),
);
?>