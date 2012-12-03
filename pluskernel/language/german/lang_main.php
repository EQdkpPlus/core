<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.kompsoft.de
 * ------------------
 * lang_main.php (english)
 * Changed: October 31, 2006
 *
 ******************************/

//---- Main ----
$lang['pluskernel']          	= 'PLUS Config';
$lang['pk_adminmenu']         = 'PLUS Config';
$lang['pk_settings']					= 'Einstellungen';
$lang['pk_date_settings']			= 'd.m.y';

//---- Javascript stuff ----
$lang['pk_plus_about']				= 'Über EQDKP PLUS';
$lang['updates']							= 'Verfügbare Updates';
$lang['loading']							= 'Lädt...';
$lang['pk_config_header']			= 'EQDKP PLUS Einstellungen';
$lang['pk_close_jswin1']      = 'Schließe das';
$lang['pk_close_jswin2']      = 'Fenster bevor du es erneut öffnest!';
$lang['pk_help_header']				= 'Hilfe';

//---- Updater Stuff ----
$lang['pk_alt_attention']			= 'Achtung';
$lang['pk_alt_ok']						= 'Alles OK!';
$lang['pk_updates_avail']			= 'Updates verfügbar';
$lang['pk_updates_navail']		= 'Keine Updates verfügbar';
$lang['pk_no_updates']				= 'Keine Updates verfügbar. Deine Installation ist auf dem neusten Stand.';
$lang['pk_act_version']				= 'Aktuell';
$lang['pk_inst_version']			= 'Installiert';
$lang['pk_changelog']					= 'Changelog';
$lang['pk_download']					= 'Download';
$lang['pk_upd_information']		= 'Information';
$lang['pk_enabled']						= 'eingeschaltet';
$lang['pk_disabled']					= 'ausgeschaltet';
$lang['pk_auto_updates1']			= 'Die automatische Anzeige der Updates ist';
$lang['pk_auto_updates2']			= 'Falls dies beabsichtigt ist, überprüfe die Aktualität der Plugins und des EQDKP PLUS bitte von Zeit zu Zeit per Hand.';
$lang['pk_module_name']				= 'Modulname';
$lang['pk_plugin_level']			= 'Level';
$lang['pk_release_date']			= 'Release';
$lang['pk_alt_error']					= 'Fehler';
$lang['pk_no_conn_header']		= 'Verbindungsfehler';
$lang['pk_no_server_conn']		= 'Beim Versuch den Updateserver zu kontaktieren trat ein Fehler auf.
																 Entweder dein Host erlaubt keine ausgehenden Verbindungen, oder es
																 bestehen Netzwerkprobleme. Bitte besuche das EQDKP Forum um
																 sicherzustellen, dass du die neuste Version am laufen hast.';
$lang['pk_reset_warning']			= 'Warnung zurücksetzen';

//---- Update Levels ----
$lang['pk_level_other']				= 'andere';
$updatelevel = array (
	'Bugfix'										=> 'Bugfix',
	'Feature Release'						=> 'Zukünfiges Release',
	'Security Update'						=> 'Sicherheitsupdate',
	'New version'								=> 'Neue Version',
	'Release Candidate'					=> 'Release Candidate',
	'Public Beta'								=> 'Öffentliche Beta',
	'Closed Beta'								=> 'Geschlossene Beta',
	'Alpha'											=> 'Alpha',
);

//---- About ----
$lang['pk_version']						= 'Version';
$lang['pk_created by']				= 'geschrieben von';
$lang['web_url']							= 'Web';
$lang['personal_url']					= 'Privat';
$lang['pk_credits']						= 'Credits';
$lang['pk_sponsors']					= 'Spender';
$lang['pk_plugins']						= 'PlugIns';
$lang['pk_modifications']			= 'Mods';
$lang['pk_themes']						= 'Styles';
$lang['pk_additions']					= 'Code Additions';
$lang['pk_tab_stuff']					= 'EQDKP Team';
$lang['pk_tab_help']					= 'Hilfe';

//---- Settings ----
$lang['pk_save']							= 'Speichern';
$lang['pk_save_title']				= '';
$lang['pk_succ_saved']				= 'Die Einstellungen wurden erfolgreich gespeichert';
 // Tabs
$lang['pk_tab_global']				= 'Global';
$lang['pk_tab_multidkp']			= 'multiDKP';
$lang['pk_tab_links']					= 'Links';
$lang['pk_tab_bosscount']			= 'BossCounter';
$lang['pk_tab_listmemb']			= 'Listmembers';
$lang['pk_tab_itemstats']			= 'Itemstats';
// Global
$lang['pk_set_QuickDKP']			= 'Zeige QuickDKP';
$lang['pk_set_Bossloot']			= 'Bossloot anzeigen (Nur aktivieren wenn für jeden Boss ein einzelner Raid angetragen wird)';
$lang['pk_set_ClassColor']		= 'Farbige Klassennamen';
$lang['pk_set_Updatecheck']		= 'Automatische Warnung bei Updates anzeigen';
$lang['pk_window_time1']			= 'Zeige Updatewarnung alle';
$lang['pk_window_time2']			= 'Minuten';
// MultiDKP
$lang['pk_set_multidkp']			= 'MultiDKP einschalten';
// Listmembers /Showmembers
$lang['pk_set_leaderboard']		= 'Zeige Leaderboard';
$lang['pk_set_lb_solo']				= 'Zeige Leaderboard pro MultiDKP Konto';
$lang['pk_set_rank']					= 'Zeige Rang';
$lang['pk_set_rank_icon']			= 'Zeige Rang Icon';
$lang['pk_set_level']					= 'Zeige Level';
$lang['pk_set_lastloot']			= 'Zeige letzten Loot';
$lang['pk_set_lastraid']			= 'Zeige letzten Raid';
$lang['pk_set_attendance30']	= 'Zeige Raidbeteiligung letzte 30 Tage';
$lang['pk_set_attendance60']	= 'Zeige Raidbeteiligung letzte 60 Tage';
$lang['pk_set_attendance90']	= 'Zeige Raidbeteiligung letzte 90 Tage';
$lang['pk_set_attendanceAll']	= 'Zeige Raidbeteiligung seit Beginn';
// Links
$lang['pk_set_links']					= 'Links einschalten';
$lang['pk_set_linkurl']				= 'URL';
$lang['pk_set_linkname']			= 'Name des Links';
$lang['pk_set_newwindow']			= 'Neues Fenster?';
// BossCounter
$lang['pk_set_bosscounter']		= 'Zeige Bosscounter';
//Itemstats
$lang['pk_set_itemstats']			= 'Itemstats einschalten';
$lang['pk_is_language']				= 'Itemstats Sprache';
$lang['pk_german']						=	'Deutsch';
$lang['pk_english']						= 'English';
$lang['pk_french']						= 'French';
$lang['pk_set_icon_ext']			= 'Dateierweiterung der Bilder';
$lang['pk_set_icon_loc']			= 'URL zu den Itemstats Bildern';
$lang['pk_set_en_de']					= 'Übersetze die Gegenstände von Englisch ins Deutsche';
$lang['pk_set_de_en']					= 'Übersetze die Gegenstände von Deutsch ins Englische';

########### Alles ab hier muss noch übersetzt werden.
########### Danach bitte richtig einsortieren.

//MultiDKP
//

$lang['pk_set_multi_Tooltip']						= 'DKP Tooltip anzeigen';
$lang['pk_set_multi_smartTooltip']			= 'Smart Tooltip';

//Help
$lang['pk_help_colorclassnames']				= "Wenn aktiviert, dann werden die Spieler in den WoW Farben ihrer Klassen und mit ihrem Klassenicon dargestellt.";
$lang['pk_help_quickdkp']								= "Zeigt dem eingelogtem User oberhalb des Menus die Punkte, aller Member die ihm zugeordnet sind.";
$lang['pk_help_boosloot']								= "Wenn aktiviert, können die Bossnamen in den Raidnotizen und im Bosscounter angeklickt werden, um zu einer detailierten Übersicht der Drops eines Bosses zu gelangen. Wenn nicht aktiviert, wird auf Blasc.de verlinkt. (Nur aktivieren wenn für jeden Boss ein einzelner Raid angetragen wird)";
$lang['pk_help_autowarning']						= "Warnt den Administrator beim Einloggen, wenn Updates verfügbar sind.";
$lang['pk_help_warningtime']						= "Wie oft soll die Warnmeldung angezeigt werden ?";
$lang['pk_help_multidkp']								= "MultiDKP erlaub die verwaltung und betrachtung von getrennen Punktekonten. Aktiviert die Berechnung und Anzeige der MultiDKP Konten.";
$lang['pk_help_dkptooltip']							= "Wenn aktiviert, wird ein Tooltip mit detalierten Informationen zur Punkteberechnung angezeigt, wenn der Mauszeiger über die Punkte fährt.";
$lang['pk_help_smarttooltip']						= "Verkürzte Darstellung des Tooltips (aktivieren bei mehr als 3 Events pro Konto)";
$lang['pk_help_links']									= "In diesem Menu können verschiedene Links definiert werden, die im Hauptmenu zu darstellung kommen.";
$lang['pk_help_bosscounter']						= "Wenn aktiviert, wird unterhalb des Hauptmenus eine Tabelle mit den Bosskills angezeigt. Die Administration erfolgt über das Plugin Bossprogress.";
$lang['pk_help_lm_leaderboard']					= "Wenn aktiviert, wird das Leaderboard oberhalb der Punktetabelle angezeigt. Mit Leaderboard ist eine Tabelle gemeint, in der pro Spalte eine Klasse nach DKP absteigened sortiert angezeigt wird";
$lang['pk_help_lm_rank']								= "Es wird eine extra Spalte angezeigt, in der der Rang des Members dargestellt wird.";
$lang['pk_help_lm_rankicon']						= "Anstatt des Rangnamens als Text, wird ein Icon angezeigt. Welche Items verfügbar sind, seht ihr in dem Ordner \images\rank";
$lang['pk_help_lm_level']								= "Es wird eine extra Spalte angezeigt, in der das Level des Members dargestellt wird.";
$lang['pk_help_lm_lastloot']						= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages an dem der Spieler zum letzten mal ein Item bekommen hat.";
$lang['pk_help_lm_lastraid']						= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages an dem der Spieler zum letzten mal an einem Raid teilgenommen hat.";
$lang['pk_help_lm_atten30']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 30 Tagen (in Prozent) angezeigt wird.";
$lang['pk_help_lm_atten60']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 60 Tagen (in Prozent) angezeigt wird.";
$lang['pk_help_lm_atten90']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 90 Tagen (in Prozent) angezeigt wird.";
$lang['pk_help_lm_attenall']						= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an allen Raids (in Prozent) angezeigt wird.";
$lang['pk_help_itemstats_on']						= "Itemstats ruft bei WoW Datenbanken (Blasc, Allahkazm, Thottbot) Informationen zu den im EQDKP eingetragenen Items ab. Diese werden dann in der Farbe der Qualität der Items und mit dem von WoW bekannten Tooltip angezeigt. Wenn aktiviert, werden Items mit einem Mouseover Tooltip angezeigt, ähnlich dem von WoW.";
$lang['pk_help_itemstats_search']				= "In welcher Datenbank soll Itemstats zuerst nach Informationen suchen ? Blasc oder Allakazam.";
$lang['pk_help_itemstats_icon_ext']			= "Dateierweiterung der anzuzeigenden Bilder. Normalerweise .png oder .jpg.";
$lang['pk_help_itemstats_icon_url']						= "Tragt hier die URL ein, wo sich die Itemstats Bilder befinden. Deutsch: http://www.buffed.de/images/wow/32/ in 32x32 oder http://www.buffed.de/images/wow/64/ in 64x64 Pixel. English bei Allakzam: http://www.buffed.de/images/wow/32/";
$lang['pk_help_itemstats_translate_deeng']		= "Wenn aktivert, werden die Informationen des Tooltips in Deutsch abgerufen, auch wenn das Item in English ist.";
$lang['pk_help_itemstats_translate_engde']		= "Wenn aktivert, werden die Informationen des Tooltips in English abgerufen, auch wenn das Item in Deutsch ist.";

$lang['pk_set_leaderboard_2row']		= 'Leaderboard in 2 Zeilen';
$lang['pk_set_leaderboard_limit']		= 'Limit der Anzeige';

$lang['pk_help_leaderboard_2row']		= 'Wenn aktivert, wird das Leaderbaord in zwei Zeilen, mit je 4 bzw 5 Klassen angezeigt.';
$lang['pk_help_leaderboard_limit']		= 'Wenn ein numerischer Wert eingetragen wird, beschränkt das Leaderboard die Anzahl der angezeigten Member. 0 steht dabei für keine Einschränkung.';

$lang['pk_set_newsloot_limit']		= 'Newsloot Limit';
$lang['pk_help_newsloot_limit']	= 'Wie viele Items sollen in den News angezeigt werden ? Beschränkt die Anzeige der Items, die unter den News angezeigt werden. 0 auswählen für kein Limit.';

$lang['pk_set_itemstats_debug']		= 'Debug Modus';
$lang['pk_help_itemstats_debug']	= 'Wenn aktiviert, werden alle Schritte von Itemstats in die Datei /itemstats/includes_de/debug.txt geschrieben. Diese MUSS aber beschreibbar sein. CHMOD 777 !!!';

$lang['pk_set_showclasscolumn']		= 'Zeige Klassenspalte';
$lang['pk_help_showclasscolumn']	= 'Wenn aktiviert, wird eine extra Spalte angezeigt in der die Klasse des Spielers steht.';

$lang['pk_set_show_skill']		= 'Zeige Skillung Spalte';
$lang['pk_help_show_skill']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Skillung des Spielers angezeigt wird.';

$lang['pk_set_show_arkan_resi']		= 'Zeige Arkan Resistenzen Spalte';
$lang['pk_help_show_arkan_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Arkan Resistenzen des Spielers angezeigt wird.';

$lang['pk_set_show_fire_resi']		= 'Zeige Feuer Resistenzen Spalte';
$lang['pk_help_show_fire_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Feuer Resistenzen des Spielers angezeigt wird.';

$lang['pk_set_show_nature_resi']		= 'Zeige Natur Resistenzen Spalte';
$lang['pk_help_show_nature_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Natur Resistenzen des Spielers angezeigt wird.';

$lang['pk_set_show_ice_resi']		= 'Zeige Eis Resistenzen Spalte';
$lang['pk_help_show_ice_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Eis Resistenzen des Spielers angezeigt wird.';

$lang['pk_set_show_shadow_resi']		= 'Zeige Schatten Resistenzen Spalte';
$lang['pk_help_show_shadow_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Schatten Resistenzen des Spielers angezeigt wird.';

$lang['pk_set_show_profils']		= 'Zeige Profil Links als Spalte';
$lang['pk_help_show_profils']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Links zu den eingegebenen Profilen angezeigt werden.';


?>