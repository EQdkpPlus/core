<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

//---- Main ----
$plang['pluskernel']          	= 'PLUS Config';
$plang['pk_adminmenu']         	= 'PLUS Config';
$plang['pk_settings']			= 'Einstellungen';
$plang['pk_date_settings']		= 'd.m.y';

//---- Javascript stuff ----
$plang['pk_plus_about']			= 'Über EQDKP PLUS';
$plang['updates']				= 'Verfügbare Updates';
$plang['loading']				= 'Lädt...';
$plang['pk_config_header']		= 'EQDKP PLUS Einstellungen';
$plang['pk_close_jswin1']     	= 'Schließe das';
$plang['pk_close_jswin2']      	= 'Fenster bevor Du es erneut öffnest!';
$plang['pk_help_header']		= 'Hilfe';
$plang['pk_plus_comments']  	= 'Kommentare';

//---- Updater Stuff ----
$plang['pk_alt_attention']			= 'Achtung';
$plang['pk_alt_ok']					= 'Alles OK!';
$plang['pk_updates_avail']			= 'Updates verfügbar';
$plang['pk_updates_navail']			= 'Keine Updates verfügbar';
$plang['pk_no_updates']				= 'Keine Updates verfügbar. Deine Installation ist auf dem neuesten Stand.';
$plang['pk_act_version']			= 'Aktuell';
$plang['pk_inst_version']			= 'Installiert';
$plang['pk_changelog']				= 'Changelog';
$plang['pk_download']				= 'Download';
$plang['pk_upd_information']		= 'Information';
$plang['pk_enabled']				= 'eingeschaltet';
$plang['pk_disabled']				= 'ausgeschaltet';
$plang['pk_auto_updates1']			= 'Die automatische Anzeige der Updates ist';
$plang['pk_auto_updates2']			= 'Falls dies beabsichtigt ist, überprüfe die Aktualität der Plugins und des EQDKP PLUS bitte von Zeit zu Zeit per Hand.';
$plang['pk_module_name']			= 'Modulname';
$plang['pk_plugin_level']			= 'Level';
$plang['pk_release_date']			= 'Release';
$plang['pk_alt_error']				= 'Fehler';
$plang['pk_no_conn_header']			= 'Verbindungsfehler';
$plang['pk_no_server_conn']			= 'Beim Versuch, den Updateserver zu kontaktieren, trat ein Fehler auf.
																 	Entweder erlaubt Dein Host keine ausgehenden Verbindungen, oder es
																 	bestehen Netzwerkprobleme. Bitte besuche das EQDKP Forum um
																 	sicherzustellen, dass Du die neueste Version am Laufen hast.';
$plang['pk_reset_warning']			= 'Warnung zurücksetzen';

//---- Update Levels ----
$plang['pk_level_other']				= 'andere';
$updatelevel = array (
	'Bugfix'								=> 'Bugfix',
	'Feature Release'						=> 'Zukünfiges Release',
	'Security Update'						=> 'Sicherheitsupdate',
	'New version'							=> 'Neue Version',
	'Release Candidate'						=> 'Release Candidate',
	'Public Beta'							=> 'Öffentliche Beta',
	'Closed Beta'							=> 'Geschlossene Beta',
	'Alpha'									=> 'Alpha',
);

//---- About ----
$plang['pk_version']					= 'Version';
$plang['pk_prodcutname']				= 'Produkt';
$plang['pk_modification']				= 'Mod';
$plang['pk_tname']						= 'Template';
$plang['pk_developer']					= 'Entwickler';
$plang['pk_plugin']						= 'Plugin';
$plang['pk_weblink']					= 'Link';
$plang['pk_phpstring']					= 'PHP String';
$plang['pk_phpvalue']					= 'Wert';
$plang['pk_donation']					= 'Spende';
$plang['pk_job']						= 'Job';
$plang['pk_sitename']					= 'Seite';
$plang['pk_dona_name']					= 'Name';
$plang['pk_betateam1']					= 'Betatest Team (Deutschland)';
$plang['pk_betateam2']					= 'in chronologischer Reihenfolge';
$plang['pk_created by']					= 'geschrieben von';
$plang['web_url']						= 'Web';
$plang['personal_url']					= 'Privat';
$plang['pk_credits']					= 'Credits';
$plang['pk_sponsors']					= 'Spender';
$plang['pk_plugins']					= 'Plugins';
$plang['pk_modifications']				= 'Mods';
$plang['pk_themes']						= 'Styles';
$plang['pk_additions']					= 'Code Additions';
$plang['pk_tab_stuff']					= 'EQDKP Team';
$plang['pk_tab_help']					= 'Hilfe';
$plang['pk_tab_tech']					= 'Tech';

//---- Settings ----
$plang['pk_save']						= 'Speichern';
$plang['pk_save_title']					= 'Einstellungen gespeichert';
$plang['pk_succ_saved']					= 'Die Einstellungen wurden erfolgreich gespeichert';
 // Tabs
$plang['pk_tab_global']					= 'Global';
$plang['pk_tab_multidkp']				= 'MultiDKP';
$plang['pk_tab_links']					= 'Links';
$plang['pk_tab_bosscount']				= 'BossCounter';
$plang['pk_tab_listmemb']				= 'Listmembers';
$plang['pk_tab_itemstats']				= 'Itemstats';
// Global
$plang['pk_set_QuickDKP']				= 'Zeige QuickDKP';
$plang['pk_set_Bossloot']				= 'Bossloot anzeigen (Nur aktivieren, wenn für jeden Boss ein einzelner Raid angetragen wird)';
$plang['pk_set_ClassColor']				= 'Farbige Klassennamen';
$plang['pk_set_Updatecheck']			= 'Automatische Warnung bei Updates anzeigen';
$plang['pk_window_time1']				= 'Zeige Updatewarnung alle';
$plang['pk_window_time2']				= 'Minuten';
// MultiDKP
$plang['pk_set_multidkp']				= 'MultiDKP einschalten';
// Listmembers /Showmembers
$plang['pk_set_leaderboard']			= 'Zeige Leaderboard';
$plang['pk_set_lb_solo']				= 'Zeige Leaderboard pro MultiDKP Konto';
$plang['pk_set_rank']					= 'Zeige Rang';
$plang['pk_set_rank_icon']				= 'Zeige Rang Icon';
$plang['pk_set_level']					= 'Zeige Level';
$plang['pk_set_lastloot']				= 'Zeige letzten Loot';
$plang['pk_set_lastraid']				= 'Zeige letzten Raid';
$plang['pk_set_attendance30']			= 'Zeige Raidbeteiligung der letzten 30 Tage';
$plang['pk_set_attendance60']			= 'Zeige Raidbeteiligung der letzten 60 Tage';
$plang['pk_set_attendance90']			= 'Zeige Raidbeteiligung der letzten 90 Tage';
$plang['pk_set_attendanceAll']			= 'Zeige Raidbeteiligung seit Beginn';
// Links
$plang['pk_set_links']					= 'Links einschalten';
$plang['pk_set_linkurl']				= 'URL';
$plang['pk_set_linkname']				= 'Name des Links';
$plang['pk_set_newwindow']				= 'Neues Fenster?';
// BossCounter
$plang['pk_set_bosscounter']			= 'Zeige BossCounter';
//Itemstats
$plang['pk_set_itemstats']				= 'Itemstats einschalten';
$plang['pk_is_language']				= 'Itemstats Sprache';
$plang['pk_german']						=	'Deutsch';
$plang['pk_english']					= 'English';
$plang['pk_french']						= 'French';
$plang['pk_set_icon_ext']				= 'Dateierweiterung der Bilder (nur bei buffed)';
$plang['pk_set_icon_loc']				= 'URL zu den Itemstats Bildern (nur bei buffed)';
$plang['pk_set_en_de']					= 'Übersetze die Gegenstände von Englisch ins Deutsche';
$plang['pk_set_de_en']					= 'Übersetze die Gegenstände von Deutsch ins Englische';

################
# new sort
###############

//MultiDKP
//

$plang['pk_set_multi_Tooltip']						= 'DKP Tooltip anzeigen';
$plang['pk_set_multi_smartTooltip']			= 'Smart Tooltip';

//Help
$plang['pk_help_colorclassnames']				= "Wenn aktiviert, werden die Spieler in den Farben ihrer Klassen und mit ihrem Klassenicon dargestellt.";
$plang['pk_help_quickdkp']						= "Zeigt dem eingeloggten Benutzer oberhalb des Menüs die Punkte aller Mitglieder, die ihm zugeordnet sind.";
$plang['pk_help_boosloot']						= "Wenn aktiviert, können die Bossnamen in den Raidnotizen und im BossCounter angeklickt werden, um zu einer detaillierten Übersicht der Drops eines Bosses zu gelangen. Wenn nicht aktiviert, wird auf Blasc.de verlinkt. (Nur aktivieren, wenn für jeden Boss ein einzelner Raid angetragen wird.)";
$plang['pk_help_autowarning']					= "Warnt den Administrator beim Einloggen, wenn Updates verfügbar sind.";
$plang['pk_help_warningtime']					= "Wie oft soll die Warnmeldung angezeigt werden?";
$plang['pk_help_multidkp']						= "MultiDKP erlaubt die Verwaltung und Betrachtung von getrennten Punktekonten. Aktiviert die Berechnung und Anzeige der MultiDKP Konten.";
$plang['pk_help_dkptooltip']					= "Wenn aktiviert, wird ein Tooltip mit detaillierten Informationen zur Punkteberechnung angezeigt, wenn der Mauszeiger über die Punkte fährt.";
$plang['pk_help_smarttooltip']					= "Verkürzte Darstellung des Tooltips (Nur bei mehr als drei Ereignissen pro Konto aktivieren.)";
$plang['pk_help_links']							= "In diesem Menü können verschiedene Links definiert werden, die im Hauptmenu dargestellt werden.";
$plang['pk_help_bosscounter']					= "Wenn aktiviert, wird unterhalb des Hauptmenüs eine Tabelle mit den Bosskills angezeigt. Die Administration erfolgt über das Plugin BossProgress.";
$plang['pk_help_lm_leaderboard']				= "Wenn aktiviert, wird das Leaderboard oberhalb der Punktetabelle angezeigt. Mit Leaderboard ist eine Tabelle gemeint, in der pro Spalte eine Klasse nach DKP absteigend sortiert angezeigt wird";
$plang['pk_help_lm_rank']						= "Es wird eine extra Spalte angezeigt, in der der Rang des Mitglieds dargestellt wird.";
$plang['pk_help_lm_rankicon']					= "Anstatt des Rangnamens als Text, wird ein Icon angezeigt. Welche Icons verfügbar sind, seht Ihr in dem Ordner games\Spiel\ rank";
$plang['pk_help_lm_level']						= "Es wird eine extra Spalte angezeigt, in der das Level des Mitglieds dargestellt wird.";
$plang['pk_help_lm_lastloot']					= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages, an dem der Spieler zum letzten Mal ein Item bekommen hat.";
$plang['pk_help_lm_lastraid']					= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages, an dem der Spieler zum letzten Mal an einem Raid teilgenommen hat.";
$plang['pk_help_lm_atten30']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 30 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_atten60']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 60 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_atten90']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 90 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_attenall']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an allen Raids (in Prozent) angezeigt wird.";
$plang['pk_help_itemstats_on']					= "Itemstats ruft bei WoW Datenbanken (Blasc, Allakhazam, Thottbot) Informationen zu den im EQDKP eingetragenen Items ab. Diese werden dann in der Farbe der Qualität der Items und mit dem von WoW bekannten Tooltip angezeigt. Wenn aktiviert, werden Items mit einem Mouseover Tooltip angezeigt, ähnlich dem von WoW.";
$plang['pk_help_itemstats_search']				= "In welcher Datenbank soll Itemstats zuerst nach Informationen suchen? Blasc oder Allakhazam?";
$plang['pk_help_itemstats_icon_ext']			= "Dateierweiterung der anzuzeigenden Bilder. Normalerweise .png oder .jpg.";
$plang['pk_help_itemstats_icon_url']			= "Tragt hier die URL ein, wo sich die Itemstats Bilder befinden. Deutsch: http://www.buffed.de/images/wow/32/ in 32x32 oder http://www.buffed.de/images/wow/64/ in 64x64 Pixel. Englisch bei Allakhazam: http://www.buffed.de/images/wow/32/";
$plang['pk_help_itemstats_translate_deeng']		= "Wenn aktiviert, werden die Informationen des Tooltips in Deutsch abgerufen, auch wenn das Item in Englisch ist.";
$plang['pk_help_itemstats_translate_engde']		= "Wenn aktiviert, werden die Informationen des Tooltips in Englisch abgerufen, auch wenn das Item in Deutsch ist.";

$plang['pk_set_leaderboard_2row']		= 'Leaderboard in 2 Zeilen';
$plang['pk_help_leaderboard_2row']		= 'Wenn aktiviert, wird das Leaderbaord in zwei Zeilen, mit je 4 bzw. 5 Klassen angezeigt.';

$plang['pk_set_leaderboard_limit']		= 'Limit der Anzeige';
$plang['pk_help_leaderboard_limit']		= 'Wenn ein numerischer Wert eingetragen wird, beschränkt das Leaderboard die Anzahl der angezeigten Mitglieder. 0 steht dabei für keine Einschränkung.';

$plang['pk_set_leaderboard_zero']		= 'Mitglieder mit 0 DKP im Leaderboard ausblenden';
$plang['pk_help_leaderboard_zero']		= 'Wenn aktiviert, werden Mitglieder ohne DKP nicht im Leaderboard angezeigt.';


$plang['pk_set_newsloot_limit']		= 'Newsloot Limit';
$plang['pk_help_newsloot_limit']	= 'Wie viele Items sollen in den News angezeigt werden? Beschränkt die Anzeige der Items, die unter den News angezeigt werden. 0 auswählen für kein Limit.';

$plang['pk_set_itemstats_debug']	= 'Debug Modus';
$plang['pk_help_itemstats_debug']	= 'Wenn aktiviert, werden alle Schritte von Itemstats in die Datei /itemstats/includes_de/debug.txt geschrieben. Diese MUSS aber beschreibbar sein. CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']	= 'Zeige Klassenspalte';
$plang['pk_help_showclasscolumn']	= 'Wenn aktiviert, wird eine extra Spalte angezeigt, in der die Klasse des Spielers steht.';

$plang['pk_set_show_skill']			= 'Zeige Skillungsspalte';
$plang['pk_help_show_skill']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Skillung des Spielers angezeigt wird.';

$plang['pk_set_show_arkan_resi']	= 'Zeige Arkan Resistenz Spalte';
$plang['pk_help_show_arkan_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Arkan Resistenz des Spielers angezeigt wird.';

$plang['pk_set_show_fire_resi']		= 'Zeige Feuer Resistenz Spalte';
$plang['pk_help_show_fire_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Feuer Resistenz des Spielers angezeigt wird.';

$plang['pk_set_show_nature_resi']	= 'Zeige Natur Resistenz Spalte';
$plang['pk_help_show_nature_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Natur Resistenz des Spielers angezeigt wird.';

$plang['pk_set_show_ice_resi']		= 'Zeige Eis Resistenz Spalte';
$plang['pk_help_show_ice_resi']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Eis Resistenz des Spielers angezeigt wird.';

$plang['pk_set_show_shadow_resi']		= 'Zeige Schatten Resistenz Spalte';
$plang['pk_help_show_shadow_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Schatten Resistenz des Spielers angezeigt wird.';

$plang['pk_set_show_profils']		= 'Zeige Profil Links als Spalte';
$plang['pk_help_show_profils']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Links zu den eingegebenen Profilen angezeigt werden.';

$plang['pk_set_servername']			= 'Realm Name';
$plang['pk_help_servername']		= 'Gebt hier Euren Servernamen ein, um direkt zur Armory linken zu können.';

$plang['pk_set_server_region']		= 'Region';
$plang['pk_help_server_region']		= 'USA & Ozeanien oder Europa.';

$plang['pk_help_default_multi']		= 'Wählt hier das Konto aus, welches IMMER im Leaderboard angezeigt werden soll. ACHTUNG: bei großen Datenbanken kann die Anzeige des Leaderboards sehr lange dauern und es kann zu Timeouts kommen. Bei Problemen auf der Seite, wählt hier bitte "none" aus.';
$plang['pk_set_default_multi']		= 'Standard Konto für Leaderboard';

$plang['pk_set_round_activate']		= 'Runde DKP bei der Ausgabe, so dass keine Nachkommastellen angezeigt werden.';
$plang['pk_help_round_activate']	= 'Wenn aktivert, wird die Anzeige der DKP Punkte gerundet. Aus 125,55 DKP werden dann 126 DKP.';

$plang['pk_set_round_precision']	= 'Nachkommastelle auf die gerundet werden soll.';
$plang['pk_help_round_precision']	= 'Bestimmt, auf welche Nachkommastelle die DKP Anzeige bei der Ausgabe gerundet werden soll. Standard=0';

$plang['pk_is_set_prio']		= 'Priorität der Itemdatenbanken';
$plang['pk_is_help_prio']		= 'Legt die Priorität fest, in welcher Reihenfolge die Itemdatenbanken nach einem Item durchsucht werden sollen.';

$plang['pk_is_set_alla_lang']	= 'Sprache der Itemnamen bei Allakhazam.';
$plang['pk_is_help_alla_lang']	= 'Legt fest, in welchen Spracheinstellungen die Items bei Allakhazam gesucht werden sollen.';

$plang['pk_is_set_lang']		= 'Standardsprache der Item ID´s.';
$plang['pk_is_help_lang']		= 'Standardsprache der Item ID´s. Beispiel: [item]17182[/item] wird in dieser Sprache ausgewählt.';

$plang['pk_is_set_autosearch']	= 'Sofortige Suche';
$plang['pk_is_help_autosearch']	= 'Aktiviert: Wenn das Item nicht im Cache ist, wird automatisch danach gesucht, ohne das es extra angeklickt werden muss.<br />Nicht Aktiviert: Das Item muss erst einmal angeklickt werden, damit die Daten abgeholt werden.';

$plang['pk_is_set_integration_mode']	= 'Integrations Modus';
$plang['pk_is_help_integration_mode']	= 'Normal: Scannt den Text und setzt den Tooltip direkt in den HTML Code.<br />Script: Scannt den Text und setzt <script> Tags.';

$plang['pk_is_set_tooltip_js']		= 'Tooltip Aussehen';
$plang['pk_is_help_tooltip_js']		= 'Overlib: Der normale Tooltip.<br />Light: Abgespeckter Tooltip mit weniger Speicherplatzbedarf.';

$plang['pk_is_set_patch_cache']		= 'Cache Pfad';
$plang['pk_is_help_patch_cache']	= 'Pfad für den Cache der eigenen Items, ausgehend von /itemstats/. Standard=./xml_cache/';

$plang['pk_is_set_patch_sockets']	= 'Sockelbilder Pfad';
$plang['pk_is_help_patch_sockets']	= 'Pfad zu den Sockelbildern.';

$plang['pk_set_dkp_info']		= 'DKP Info im Menü NICHT anzeigen.'; # JA negierte Abfrage!
$plang['pk_help_dkp_info']		= 'Wenn aktiviert, dann wird im Hauptmenü die Tabelle DKP Info NICHT angezeigt.';

$plang['pk_set_debug']			= 'EQdkp Debug Modus aktiviert';
$plang['pk_set_debug_type']		= 'Modus';
$plang['pk_set_debug_type0']	= 'Debug aus (Debug=0)';
$plang['pk_set_debug_type1']	= 'Debug an einfach (Debug=1)';
$plang['pk_set_debug_type2']	= 'Debug an mit SQL Ausgaben (Debug=2)';
$plang['pk_set_debug_type3']	= 'Debug an erweitert (Debug=3)';
$plang['pk_help_debug']			= 'Wenn aktiviert, dann läuft EQdkp Plus im Debug Modus, welcher zusätzliche Informationen und Fehlermeldungen ausgibt. Deaktivieren, wenn Plugins mit SQL Fehlermeldungen abbrechen! 1=Renderzeit, Query count, 2=SQL Ausgaben, 3=Erweiterte Fehlermeldungen.';

#RSS News
$plang['pk_set_Show_rss']			= 'RSS News deaktivieren';
$plang['pk_help_Show_rss']			= 'Wenn aktiviert, dann werden KEINE aktuellen Gamenews per RSS abgeholt und angezeigt.';

$plang['pk_set_Show_rss_style']		= 'Plazierung der Game-News';
$plang['pk_help_Show_rss_style']	= 'Wo sollen die RSS-Game News angezeigt werden? Oben horizontal, im Menü vertikal oder beides?';

$plang['pk_set_Show_rss_lang']		= 'Standardsprache der RSS-News';
$plang['pk_help_Show_rss_lang']		= 'In welcher Sprache sollen die RSS-News abgeholt werden? Verfügbar sind Deutsch und Englisch.';

$plang['pk_set_Show_rss_lang_de']	= 'Deutsch';
$plang['pk_set_Show_rss_lang_eng']	= 'Englisch';

$plang['pk_set_Show_rss_style_both'] = 'Beides' ;
$plang['pk_set_Show_rss_style_v']	 = 'Menü vertikal' ;
$plang['pk_set_Show_rss_style_h']	 = 'Oben horizontal' ;

$plang['pk_set_Show_rss_count']		= 'Anzahl der anzuzeigenden News (0 oder leer für alle)';
$plang['pk_help_Show_rss_count']	= 'Wieviele News sollen angezeigt werden?';

$plang['pk_set_itemhistory_dia']	= 'Verberge Diagramme'; # Ja negierte Abfrage
$plang['pk_help_itemhistory_dia']	= 'Wenn aktivert, wird auf der Itemdetailseite ein Diagramm angezeigt, welches den Preisverlauf grafisch darstellt und die Klassenverteilung eines Raids wird in einer Grafik angezeigt.';

#Bridge
$plang['pk_set_bridge_help']				= 'Auf dieser Seite kann das Zusammenspiel mit einem Content Management System (CMS) oder einem Forum eingestellt werden.
											   Wenn ihr eines der Systeme aus der Liste benutzt, dann können sich die registrierten Mitglieder eures Forums/CMS mit denselben
											   Logindaten auch im Eqdkp anmelden.
											   Der Zugang ist allerdings auf eine Gruppe beschränkt. D.h. ihr müsst in eurem CMS/Forum eine Gruppe anlegen, und in dieser alle Mitglieder
											   aufnehmen, die sich dann im Eqdkp anmelden dürfen.';

$plang['pk_set_bridge_activate']			= 'Bridge zu einem CMS aktivieren';
$plang['pk_help_bridge_activate']			= 'Wenn aktiviert, dann wird die Bridge zu einem CMS/Forum aktiviert. Dies ermöglicht den Users dieses CMS sich im EQDKP Plus mit den selben Login Daten anzumelden.';

$plang['pk_set_bridge_dectivate_eq_reg']	= 'Registrierung am Eqdkp Plus auf die oben eingegebene URL umlenken?';
$plang['pk_help_bridge_dectivate_eq_reg']	= 'Wenn aktiviert, können sich keine User mehr direkt am Eqdkp Plus registrieren, sondern werden weitergeleitet auf die angegebene URL';

$plang['pk_set_bridge_cms']					= 'Gewünschtes CMS/Forum';
$plang['pk_help_bridge_cms']				= 'Welches CMS/Forum soll unterstützt werden?';

$plang['pk_set_bridge_acess']				= 'Befindet sich das CMS/Forum in einer anderen Datenbank wie das Eqdkp?';
$plang['pk_help_bridge_acess']				= 'Nutzt das CMS eine andere Datenbank, müssen die folgenden Felder ausgefüllt werden.';

$plang['pk_set_bridge_host']				= 'Hostname bzw. Server';
$plang['pk_help_bridge_host']				= 'Der Hostname bzw. die Server IP wo sich die Datenbank des CMS Systems befindet.';

$plang['pk_set_bridge_username']			= 'Datenbank Username';
$plang['pk_help_bridge_username']			= 'Der Benutzername um auf die Datenbank verbinden zu können';

$plang['pk_set_bridge_password']			= 'Datenbank Passwort';
$plang['pk_help_bridge_password']			= 'Das Passwort um auf die Datenbank verbinden zu können';

$plang['pk_set_bridge_database']			= 'Datenbank Name';
$plang['pk_help_bridge_database']			= 'Name eurer CMS Datenbank';

$plang['pk_set_bridge_prefix']				= 'Prefix der CMS Installation';
$plang['pk_help_bridge_prefix']				= 'Gibt den Prefix eures CMS an, z.B. phpbb_ oder wbb_';

$plang['pk_set_bridge_group']				= 'CMS Gruppen ID die Zugriff haben soll';
$plang['pk_help_bridge_group']				= 'Tragt hier die ID der Gruppe eures CMS Systems ein, welche sich im Eqdkp anmelden darf.';

$plang['pk_set_bridge_inline']				= 'Integration eure Forums per Iframe';

$plang['pk_set_bridge_inline_url']			= 'URL zu eurem Forum';
$plang['pk_help_bridge_inline_url']			= 'URL zu eurem Forum welche innerhalb des Eqdkp dargestellt werden soll';

$plang['pk_set_link_type_header']			= 'Wie soll die Seite geöffnet werden';
$plang['pk_set_link_type_help']				= 'Link im selben Browserfenster, in einem neuen Brwoserfenster oder innerhalb des Eqdkps in einem Iframe öffnen?';
$plang['pk_set_link_type_iframe_help']		= 'Wie soll der Link geöffnet werden? Bei eingebettet (dynamisch) können nur Seiten eingebunden werden, welche auf dem selben Server installier sind! Bei Problemen bitte die normale Einbettung benutzen.';
$plang['pk_set_link_type_self']				= 'normal';
$plang['pk_set_link_type_link']				= 'Neues Fenster';
$plang['pk_set_link_type_iframe']			= 'Eingebettet';

#recruitment
$plang['pk_set_recruitment_tab']			= 'Bewerbungen';
$plang['pk_set_recruitment_header']			= 'Bewerbungen - Sucht ihr neue Member ?';
$plang['pk_set_recruitment']				= 'Bewerbungen aktivieren';
$plang['pk_help_recruitment']				= 'Wenn die Bewerbungen aktiviert sind, wird über den News eine Box eingeblendet, in der darauf hingewiesen wird, dass ihr Member sucht.';
$plang['pk_recruitment_count']				= 'Anzahl';
$plang['pk_set_recruitment_contact_type']	= 'URL auf die verlinkt werden soll.';
$plang['pk_help_recruitment_contact_type']	= 'Wenn keine URL angegeben , dann wird auf die Kontakt Email Adresse verlinkt.';
$plang['ps_recruitment_spec']				= 'Skillung';

#comments
$plang['pk_set_comments_disable']			= 'Kommentare deaktivieren';
$plang['pk_hel_pcomments_disable']			= 'Deaktiviert die Kommentar Funktion auf allen Seiten das Eqdkp. (Allerdings nicht im Raidplaner Plugin)';

#Contact
$plang['pk_contact']						= 'Kontaktinformationen';
$plang['pk_contact_name']					= 'Name der Kontaktperson';
$plang['pk_contact_email']					= 'Email der Kontaktperson';
$plang['pk_contact_website']				= 'Portal - Webseite, Link des Header-Portal Buttons (mit http:// eingeben)';
$plang['pk_contact_irc']					= 'IRC Channel';
$plang['pk_contact_admin_messenger']		= 'Messenger Name z.b. Skype, ICQ';
$plang['pk_contact_custominfos']			= 'Weitere Infos';
$plang['pk_contact_owner']					= 'Betreiber Infos:';

#Next_raids
$plang['pk_set_nextraids_deactive']			= 'Nächste Raids nicht anzeigen';
$plang['pk_help_nextraids_deactive']		= 'Wenn aktiviert, dann wird im Menü nicht die Tabellen mit den nächsten Raids angezeigt.';

$plang['pk_set_nextraids_limit']			= 'Anzeige Limit der nächsten Raids';
$plang['pk_help_nextraids_limit']			= 'Wieviele der kommenden Raids sollen angezeigt werden? Standard sind 3';

$plang['pk_set_lastitems_deactive']			= 'Letzte Items nicht anzeigen';
$plang['pk_help_lastitems_deactive']		= 'Wenn aktiviert, dann wird im Menü nicht die Tabelle mit den letzten Items angezeigt.';

$plang['pk_set_lastitems_limit']			= 'Anzeige Limit der letzten Items';
$plang['pk_help_lastitems_limit']			= 'Wieviele der letztem Items sollen angezeigt werden? Standard sind 5';

$plang['pk_is_help']						= '<b>ACHTUNG Änderungen am Verhalten von Itemstats mit Eqdkp Plus 0.6.2.4!</b><br><br>
											  Sollten nach einem Update eure Items nicht mehr richtig dargestellt werden, setzt die "Priorität der Itemdatenbanken" neu ("Armory & WoWHead empfohlen), und ruft danach die Items neu ab. Benutzt dazu den "Update Itemstat Link" unter diesem Text. <br>
											  Das beste Ergebnis wird mit der Einstellung "Armory & WoWHead" erziehlt, denn nur die Armory gibt erweiterte Informationen wie Droprate, Mob und Instanz pro Item aus.
											  <br> Wichtig: Wenn ihr die Webdatenbank geändert habt, müsst ihr den Cache leeren, da sonst die schon vorhandenen Tooltips nicht mehr richtig angezeigt werden!!!!<br>
											  Zum aktualisieren des Itemcache dem Link folgen, danach die Buttons "Clear Cache" und danach "Update Itemtable" auswählen.<br><br>';

$plang['pk_set_normal_leaderbaord']			= 'Zeige das Leaderboard dynamisch mit Slider an';
$plang['pk_help_normal_leaderbaord']		= 'Wenn aktiviert, dann wird das Leaderboard dynamisch angezeigt. Die Klassenspalten vergrößern sich bei Mouseover. aktivieren, wenn die dritte Spalte des Portals angeschaltet ist.';

$plang['pk_set_thirdColumn']				= 'Dritte Spalte nicht anzeigen';
$plang['pk_help_thirdColumn']				= 'Dritte Spalte nicht anzeigen';
$plang['pk_thirdColumn']					= 'Alle folgenden Features können nur in der dritten Spalte angezeigt werden!';

#GetDKP
$plang['pk_getdkp_th']						= 'GetDKP Einstellungen';

$plang['pk_set_getdkp_rp']					= 'Raidplanerdaten mit ausgeben';
$plang['pk_help_getdkp_rp']					= 'Wenn aktiviert, dann werden die Daten des Raidpalaners mit ausgegeben.';

$plang['pk_set_getdkp_link']				= 'Zeige GetDKP Link im Hauptmenu';
$plang['pk_help_getdkp_link']				= 'Wenn aktiviert, dann wir im EQDKP Hauptmenu der Link zur Getdkp.php angezeigt.';

$plang['pk_set_getdkp_active']				= 'Getdk.php deaktivieren';
$plang['pk_help_getdkp_active']				= 'Wenn aktiviert, dann gibt die getdkp.php keine Daten aus.';

$plang['pk_set_getdkp_items']				= 'ItemIDs nicht mit ausgeben.';
$plang['pk_help_getdkp_items']				= 'Wenn deaktiviert, dann werden in der Getdkp.php keine ITemsIDs gespeichert. Die kann gerade bei Eqdkps mit sehr vielen Items zu einer deutlichen verringerung der DAteigröße führen.';

$plang['pk_set_recruit_embedded']			= 'Link embedded aufrufen?';
$plang['pk_help_recruit_embedded']			= 'Wenn aktiviert, dann wird die anzuzeigende Seite innerhalb des Eqdkps angezeigt.';

$plang['pk_set_dis_3dmember']				= '3D Modelviewer für die Memberanzeige abschalten';
$plang['pk_help_dis_3dmember']				= 'Wenn aktiviert, dann wird kein 3D Flash Model des WoW Chars angezeigt.';

$plang['pk_set_dis_3ditem']					= '3D Modelviewer für die Items abschalten';
$plang['pk_help_dis_3item']					= 'Wenn aktiviert, dann wird kein 3D Flash Item-Model.';

$plang['pk_set_disregister']				= 'Die Registrierung am EQdkp komplett deaktivieren (ignorierte die voherigen 2 Optionen).';
$plang['pk_help_disregister']				= 'Wenn aktiviert, dann können sich keine Member mehr am EQdkp registrieren. (anmelden funktioniert weiterhin)';


# Portal Manager
$plang['portalplugin_name']         = 'Modul';
$plang['portalplugin_version']      = 'Version';
$plang['portalplugin_contact']      = 'Kontakt';
$plang['portalplugin_order']        = 'Sortierung';
$plang['portalplugin_orientation']  = 'Anordnung';
$plang['portalplugin_enabled']      = 'Aktiv';
$plang['portalplugin_save']         = 'Änderungen übernehmen';
$plang['portalplugin_management']   = 'Portalmodule verwalten';
$plang['portalplugin_right']        = 'Rechts';
$plang['portalplugin_middle']       = 'Mitte';
$plang['portalplugin_left1']        = 'Links über Menü';
$plang['portalplugin_left2']        = 'Links unter Menü';
$plang['portalplugin_settings']     = 'Einstellungen';
$plang['portalplugin_winname']      = 'Portalmoduleinstellungen';
$plang['portalplugin_edit']         = 'Bearbeiten';
$plang['portalplugin_save']         = 'Speichern';
$plang['portalplugin_rights']       = 'Berechtigung';
$plang['portal_rights0']            = 'Alle';
$plang['portal_rights1']            = 'Gäste';
$plang['portal_rights2']            = 'Angemeldet';
$plang['portal_collapsable']        = 'Einklappbar';

$plang['pk_set_link_type_D_iframe']			= 'Eingebettet dynamisch angepasste Höhe';

$plang['pk_set_modelviewer_default']	= 'Standard Modelviewer';

 /* IMAGE RESIZE */
 // Lytebox settings
 $plang['pk_air_img_resize_options'] = 'Lytebox Einstellungen';
 $plang['pk_air_img_resize_enable'] = 'aktiviere Anpassung der Newsbilder';
 $plang['pk_air_max_post_img_resize_width'] = 'Maximale Breite der Bilder';
 $plang['pk_air_show_warning'] = 'Aktiviere die Anzeige einer Warnung, wenn das Bild verkleinert wurde.';
 $plang['pk_air_lytebox_theme'] = 'Lytebox\'s Design';
 $plang['pk_air_lytebox_theme_explain'] = 'Designs: grau (Standard), rot, grün, blau, gold';
 $plang['pk_air_lytebox_auto_resize'] = 'Aktiviere automatische Anpassung';
 $plang['pk_air_lytebox_auto_resize_explain'] = 'Bestimmt ob die Newsbilder verkleinert werden sollen, wenn diese zu groß sind.';
 $plang['pk_air_lytebox_animation'] = 'Aktiviere Animation beim Laden des Bildes';
 $plang['pk_air_lytebox_animation_explain'] = 'Wenn aktiviert, dann öffnet sich das Bild mit einer Animation.';
 $plang['pk_air_lytebox_grey'] = 'Grau';
 $plang['pk_air_lytebox_red'] = 'Rot';
 $plang['pk_air_lytebox_blue'] = 'Blau';
 $plang['pk_air_lytebox_green'] = 'Grün';
 $plang['pk_air_lytebox_gold'] = 'Gold';
 
 
$plang['pk_set_hide_shop'] = 'Shop-Link verbergen';
$plang['pk_help_hide_shop'] = 'Wenn aktivert, dann wird der Link zum Shirt Shop nicht mehr angezeigt.';
 
$plang['pk_set_rss_chekurl'] = 'URL vor dem Update überprüfen';
$plang['pk_help_rss_chekurl'] = 'Wenn aktiviert, dann wird vorm dem Abrufen der RSS News überprüft ob der Server erreichbar ist. Deaktivieren wenn der RSS Feed über längere Zeit nicht angezeigt wird.';

$plang['pk_set_features'] = 'DKP Funktionen';

$plang['pk_set_noDKP'] = 'Keine DKP Funktionen';
$plang['pk_help_noDKP'] = 'Wenn aktiviert, dann werden alle DKP Funktionen deaktiviert und es werden keine Hinweise auf DKP Punkte mehr angezeigt. Betrifft nicht die Raid und Ereignissliste.';

$plang['pk_set_noRoster'] = 'Kein Roster anzeigen';
$plang['pk_help_noRoster'] = 'Wenn aktiviert, dann wird die Roster Seite nicht im Menu angezeigt und das aufrufen der Seite unterbunden.';

$plang['pk_set_noDKP'] = 'DKP Funktionen komplett deaktivieren. Es wir ein Member Roster anstatt der Punkteliste angezeigt.';
$plang['pk_help_noDKP'] = 'Wenn aktiviert, dann wird anstatt der Punkteliste das Member Roster angezeigt.';

$plang['pk_set_noRaids'] = 'Keine Raid Funktionen';
$plang['pk_help_noRaids'] = 'Wenn aktiviert, dann werden alle Raid Funktionen deaktiviert. Betrifft nicht die Ereignissliste.';

$plang['pk_set_noEvents'] = 'Keine Ereignisse';
$plang['pk_help_noEvents'] = 'Wenn aktiviert, dann werden alle Event Funktionen deaktiviert. ACHTUNG Ereigniss werden für den Raidplaner benötigt!!!';

$plang['pk_set_noItemPrices'] = 'Keine Item-Preise';
$plang['pk_help_noItemPrices'] = 'Wenn aktiviert, dann wird der Link zu den Item-Preisen deaktiviert und die Seite wird gesperrt.';

$plang['pk_set_noItemHistoy'] = 'Keine Item-Historie';
$plang['pk_help_noItemHistoy'] = 'Wenn aktiviert, dann wird der Link zu der Item-Historie deaktiviert und die Seite wird gesperrt.';

$plang['pk_set_noStats'] = 'Keine Zusammenfassung und Statistik';
$plang['pk_help_noStats'] = 'Wenn aktiviert, dann wird der Link zu der Zusammenfassung und Statistik deaktiviert und die Seiten werden gesperrt.';

$plang['pk_set_cms_register_url'] = 'URL zur Registrierung an eurem CMS/Forums';
$plang['pk_help_cms_register_url'] = 'Wenn die Bridge aktiviert ist, dann wird auf diese URL weitergeleitet, wenn jemand auf den Link "Registrieren" klickt.';

$plang['pk_disclaimer'] = 'Impressum';

$plang['pk_set_link_type_menu']			= 'Menu';
$plang['pk_set_link_type_menuH']		= 'Reiter oben rechts';

//SMS gedöns
$plang['pk_set_sms_header']			= 'SMS Einstellungen';
$plang['pk_set_sms_info']			= 'Es dürfen nur Admins SMS versenden';
$plang['pk_set_sms_info_temp']		= 'Um SMS versenden zu können benötigt ihr Zugangsdaten. <br>Solltet ihr noch keine Logindaten haben, so können diese unter folgendem Link erworben werden:<br>' ;
$plang['pk_set_sms_username']		= 'Benutzername';
$plang['pk_set_sms_pass']			= 'Passwort';
$plang['pk_set_sms_amount']			= 'Anzahl SMS die noch versendet werden können?';
$plang['pk_set_sms_deactivate']		= 'SMS Funktionen komplett abschalten';

$plang['pk_faction']		= 'Fraktion';



 /*
$plang['pk_set_']	= '';
$plang['pk_help_']	= '';
*/
?>
