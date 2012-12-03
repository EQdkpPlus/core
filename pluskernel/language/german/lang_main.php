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
$plang['pluskernel']          	= 'PLUS Config';
$plang['pk_adminmenu']         	= 'PLUS Config';
$plang['pk_settings']						= 'Einstellungen';
$plang['pk_date_settings']			= 'd.m.y';

//---- Javascript stuff ----
$plang['pk_plus_about']					= 'Über EQDKP PLUS';
$plang['updates']								= 'Verfügbare Updates';
$plang['loading']								= 'Lädt...';
$plang['pk_config_header']			= 'EQDKP PLUS Einstellungen';
$plang['pk_close_jswin1']     	= 'Schließe das';
$plang['pk_close_jswin2']      	= 'Fenster bevor du es erneut öffnest!';
$plang['pk_help_header']				= 'Hilfe';

//---- Updater Stuff ----
$plang['pk_alt_attention']			= 'Achtung';
$plang['pk_alt_ok']							= 'Alles OK!';
$plang['pk_updates_avail']			= 'Updates verfügbar';
$plang['pk_updates_navail']			= 'Keine Updates verfügbar';
$plang['pk_no_updates']					= 'Keine Updates verfügbar. Deine Installation ist auf dem neusten Stand.';
$plang['pk_act_version']				= 'Aktuell';
$plang['pk_inst_version']				= 'Installiert';
$plang['pk_changelog']					= 'Changelog';
$plang['pk_download']						= 'Download';
$plang['pk_upd_information']		= 'Information';
$plang['pk_enabled']						= 'eingeschaltet';
$plang['pk_disabled']						= 'ausgeschaltet';
$plang['pk_auto_updates1']			= 'Die automatische Anzeige der Updates ist';
$plang['pk_auto_updates2']			= 'Falls dies beabsichtigt ist, überprüfe die Aktualität der Plugins und des EQDKP PLUS bitte von Zeit zu Zeit per Hand.';
$plang['pk_module_name']				= 'Modulname';
$plang['pk_plugin_level']				= 'Level';
$plang['pk_release_date']				= 'Release';
$plang['pk_alt_error']					= 'Fehler';
$plang['pk_no_conn_header']			= 'Verbindungsfehler';
$plang['pk_no_server_conn']			= 'Beim Versuch den Updateserver zu kontaktieren trat ein Fehler auf.
																 	Entweder dein Host erlaubt keine ausgehenden Verbindungen, oder es
																 	bestehen Netzwerkprobleme. Bitte besuche das EQDKP Forum um
																 	sicherzustellen, dass du die neuste Version am laufen hast.';
$plang['pk_reset_warning']			= 'Warnung zurücksetzen';

//---- Update Levels ----
$plang['pk_level_other']				= 'andere';
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
$plang['pk_version']						= 'Version';
$plang['pk_prodcutname']				= 'Produkt';
$plang['pk_modification']				= 'Mod';
$plang['pk_tname']							= 'Template';
$plang['pk_developer']					= 'Entwickler';
$plang['pk_plugin']							= 'Plug-In';
$plang['pk_weblink']						= 'Link';
$plang['pk_phpstring']					= 'PHP String';
$plang['pk_phpvalue']						= 'Wert';
$plang['pk_donation']						= 'Spende';
$plang['pk_job']								= 'Job';
$plang['pk_sitename']						= 'Seite';
$plang['pk_dona_name']					= 'Name';
$plang['pk_betateam1']					= 'Betatest Team (Deutschland)';
$plang['pk_betateam2']					= 'in chronologischer Reihenfolge';
$plang['pk_created by']					= 'geschrieben von';
$plang['web_url']								= 'Web';
$plang['personal_url']					= 'Privat';
$plang['pk_credits']						= 'Credits';
$plang['pk_sponsors']						= 'Spender';
$plang['pk_plugins']						= 'PlugIns';
$plang['pk_modifications']			= 'Mods';
$plang['pk_themes']							= 'Styles';
$plang['pk_additions']					= 'Code Additions';
$plang['pk_tab_stuff']					= 'EQDKP Team';
$plang['pk_tab_help']						= 'Hilfe';
$plang['pk_tab_tech']						= 'Tech';

//---- Settings ----
$plang['pk_save']								= 'Speichern';
$plang['pk_save_title']					= '';
$plang['pk_succ_saved']					= 'Die Einstellungen wurden erfolgreich gespeichert';
 // Tabs
$plang['pk_tab_global']					= 'Global';
$plang['pk_tab_multidkp']				= 'multiDKP';
$plang['pk_tab_links']					= 'Links';
$plang['pk_tab_bosscount']			= 'BossCounter';
$plang['pk_tab_listmemb']				= 'Listmembers';
$plang['pk_tab_itemstats']			= 'Itemstats';
// Global
$plang['pk_set_QuickDKP']				= 'Zeige QuickDKP';
$plang['pk_set_Bossloot']				= 'Bossloot anzeigen (Nur aktivieren wenn für jeden Boss ein einzelner Raid angetragen wird)';
$plang['pk_set_ClassColor']			= 'Farbige Klassennamen';
$plang['pk_set_Updatecheck']		= 'Automatische Warnung bei Updates anzeigen';
$plang['pk_window_time1']				= 'Zeige Updatewarnung alle';
$plang['pk_window_time2']				= 'Minuten';
// MultiDKP
$plang['pk_set_multidkp']				= 'MultiDKP einschalten';
// Listmembers /Showmembers
$plang['pk_set_leaderboard']		= 'Zeige Leaderboard';
$plang['pk_set_lb_solo']				= 'Zeige Leaderboard pro MultiDKP Konto';
$plang['pk_set_rank']						= 'Zeige Rang';
$plang['pk_set_rank_icon']			= 'Zeige Rang Icon';
$plang['pk_set_level']					= 'Zeige Level';
$plang['pk_set_lastloot']				= 'Zeige letzten Loot';
$plang['pk_set_lastraid']				= 'Zeige letzten Raid';
$plang['pk_set_attendance30']		= 'Zeige Raidbeteiligung letzte 30 Tage';
$plang['pk_set_attendance60']		= 'Zeige Raidbeteiligung letzte 60 Tage';
$plang['pk_set_attendance90']		= 'Zeige Raidbeteiligung letzte 90 Tage';
$plang['pk_set_attendanceAll']	= 'Zeige Raidbeteiligung seit Beginn';
// Links
$plang['pk_set_links']					= 'Links einschalten';
$plang['pk_set_linkurl']				= 'URL';
$plang['pk_set_linkname']				= 'Name des Links';
$plang['pk_set_newwindow']			= 'Neues Fenster?';
// BossCounter
$plang['pk_set_bosscounter']		= 'Zeige Bosscounter';
//Itemstats
$plang['pk_set_itemstats']			= 'Itemstats einschalten';
$plang['pk_is_language']				= 'Itemstats Sprache';
$plang['pk_german']							=	'Deutsch';
$plang['pk_english']						= 'English';
$plang['pk_french']							= 'French';
$plang['pk_set_icon_ext']				= 'Dateierweiterung der Bilder';
$plang['pk_set_icon_loc']				= 'URL zu den Itemstats Bildern';
$plang['pk_set_en_de']					= 'Übersetze die Gegenstände von Englisch ins Deutsche';
$plang['pk_set_de_en']					= 'Übersetze die Gegenstände von Deutsch ins Englische';

########### Alles ab hier muss noch übersetzt werden.
########### Danach bitte richtig einsortieren.

//MultiDKP
//

$plang['pk_set_multi_Tooltip']						= 'DKP Tooltip anzeigen';
$plang['pk_set_multi_smartTooltip']			= 'Smart Tooltip';

//Help
$plang['pk_help_colorclassnames']				= "Wenn aktiviert, dann werden die Spieler in den WoW Farben ihrer Klassen und mit ihrem Klassenicon dargestellt.";
$plang['pk_help_quickdkp']								= "Zeigt dem eingelogtem User oberhalb des Menus die Punkte, aller Member die ihm zugeordnet sind.";
$plang['pk_help_boosloot']								= "Wenn aktiviert, können die Bossnamen in den Raidnotizen und im Bosscounter angeklickt werden, um zu einer detailierten Übersicht der Drops eines Bosses zu gelangen. Wenn nicht aktiviert, wird auf Blasc.de verlinkt. (Nur aktivieren wenn für jeden Boss ein einzelner Raid angetragen wird)";
$plang['pk_help_autowarning']						= "Warnt den Administrator beim Einloggen, wenn Updates verfügbar sind.";
$plang['pk_help_warningtime']						= "Wie oft soll die Warnmeldung angezeigt werden ?";
$plang['pk_help_multidkp']								= "MultiDKP erlaub die verwaltung und betrachtung von getrennen Punktekonten. Aktiviert die Berechnung und Anzeige der MultiDKP Konten.";
$plang['pk_help_dkptooltip']							= "Wenn aktiviert, wird ein Tooltip mit detalierten Informationen zur Punkteberechnung angezeigt, wenn der Mauszeiger über die Punkte fährt.";
$plang['pk_help_smarttooltip']						= "Verkürzte Darstellung des Tooltips (aktivieren bei mehr als 3 Events pro Konto)";
$plang['pk_help_links']									= "In diesem Menu können verschiedene Links definiert werden, die im Hauptmenu zu darstellung kommen.";
$plang['pk_help_bosscounter']						= "Wenn aktiviert, wird unterhalb des Hauptmenus eine Tabelle mit den Bosskills angezeigt. Die Administration erfolgt über das Plugin Bossprogress.";
$plang['pk_help_lm_leaderboard']					= "Wenn aktiviert, wird das Leaderboard oberhalb der Punktetabelle angezeigt. Mit Leaderboard ist eine Tabelle gemeint, in der pro Spalte eine Klasse nach DKP absteigened sortiert angezeigt wird";
$plang['pk_help_lm_rank']								= "Es wird eine extra Spalte angezeigt, in der der Rang des Members dargestellt wird.";
$plang['pk_help_lm_rankicon']						= "Anstatt des Rangnamens als Text, wird ein Icon angezeigt. Welche Items verfügbar sind, seht ihr in dem Ordner \images\rank";
$plang['pk_help_lm_level']								= "Es wird eine extra Spalte angezeigt, in der das Level des Members dargestellt wird.";
$plang['pk_help_lm_lastloot']						= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages an dem der Spieler zum letzten mal ein Item bekommen hat.";
$plang['pk_help_lm_lastraid']						= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages an dem der Spieler zum letzten mal an einem Raid teilgenommen hat.";
$plang['pk_help_lm_atten30']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 30 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_atten60']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 60 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_atten90']							= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 90 Tagen (in Prozent) angezeigt wird.";
$plang['pk_help_lm_attenall']						= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an allen Raids (in Prozent) angezeigt wird.";
$plang['pk_help_itemstats_on']						= "Itemstats ruft bei WoW Datenbanken (Blasc, Allahkazm, Thottbot) Informationen zu den im EQDKP eingetragenen Items ab. Diese werden dann in der Farbe der Qualität der Items und mit dem von WoW bekannten Tooltip angezeigt. Wenn aktiviert, werden Items mit einem Mouseover Tooltip angezeigt, ähnlich dem von WoW.";
$plang['pk_help_itemstats_search']				= "In welcher Datenbank soll Itemstats zuerst nach Informationen suchen ? Blasc oder Allakazam.";
$plang['pk_help_itemstats_icon_ext']			= "Dateierweiterung der anzuzeigenden Bilder. Normalerweise .png oder .jpg.";
$plang['pk_help_itemstats_icon_url']						= "Tragt hier die URL ein, wo sich die Itemstats Bilder befinden. Deutsch: http://www.buffed.de/images/wow/32/ in 32x32 oder http://www.buffed.de/images/wow/64/ in 64x64 Pixel. English bei Allakzam: http://www.buffed.de/images/wow/32/";
$plang['pk_help_itemstats_translate_deeng']		= "Wenn aktivert, werden die Informationen des Tooltips in Deutsch abgerufen, auch wenn das Item in English ist.";
$plang['pk_help_itemstats_translate_engde']		= "Wenn aktivert, werden die Informationen des Tooltips in English abgerufen, auch wenn das Item in Deutsch ist.";

$plang['pk_set_leaderboard_2row']		= 'Leaderboard in 2 Zeilen';
$plang['pk_set_leaderboard_limit']		= 'Limit der Anzeige';

$plang['pk_help_leaderboard_2row']		= 'Wenn aktivert, wird das Leaderbaord in zwei Zeilen, mit je 4 bzw 5 Klassen angezeigt.';
$plang['pk_help_leaderboard_limit']		= 'Wenn ein numerischer Wert eingetragen wird, beschränkt das Leaderboard die Anzahl der angezeigten Member. 0 steht dabei für keine Einschränkung.';

$plang['pk_set_newsloot_limit']		= 'Newsloot Limit';
$plang['pk_help_newsloot_limit']	= 'Wie viele Items sollen in den News angezeigt werden ? Beschränkt die Anzeige der Items, die unter den News angezeigt werden. 0 auswählen für kein Limit.';

$plang['pk_set_itemstats_debug']		= 'Debug Modus';
$plang['pk_help_itemstats_debug']	= 'Wenn aktiviert, werden alle Schritte von Itemstats in die Datei /itemstats/includes_de/debug.txt geschrieben. Diese MUSS aber beschreibbar sein. CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']		= 'Zeige Klassenspalte';
$plang['pk_help_showclasscolumn']	= 'Wenn aktiviert, wird eine extra Spalte angezeigt in der die Klasse des Spielers steht.';

$plang['pk_set_show_skill']		= 'Zeige Skillung Spalte';
$plang['pk_help_show_skill']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Skillung des Spielers angezeigt wird.';

$plang['pk_set_show_arkan_resi']		= 'Zeige Arkan Resistenzen Spalte';
$plang['pk_help_show_arkan_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Arkan Resistenzen des Spielers angezeigt wird.';

$plang['pk_set_show_fire_resi']		= 'Zeige Feuer Resistenzen Spalte';
$plang['pk_help_show_fire_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Feuer Resistenzen des Spielers angezeigt wird.';

$plang['pk_set_show_nature_resi']		= 'Zeige Natur Resistenzen Spalte';
$plang['pk_help_show_nature_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Natur Resistenzen des Spielers angezeigt wird.';

$plang['pk_set_show_ice_resi']		= 'Zeige Eis Resistenzen Spalte';
$plang['pk_help_show_ice_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Eis Resistenzen des Spielers angezeigt wird.';

$plang['pk_set_show_shadow_resi']		= 'Zeige Schatten Resistenzen Spalte';
$plang['pk_help_show_shadow_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Schatten Resistenzen des Spielers angezeigt wird.';

$plang['pk_set_show_profils']		= 'Zeige Profil Links als Spalte';
$plang['pk_help_show_profils']	= 'Wenn aktiviert, wird eine Spalte eingeblendet in der die Links zu den eingegebenen Profilen angezeigt werden.';


?>