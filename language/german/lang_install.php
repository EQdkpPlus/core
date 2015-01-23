<?php
/*	Project:	EQdkp-Plus
 *	Package:	Language File
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
	'page_title'			=> "EQDKP-PLUS %s Installation",
	'back'					=> 'Speichern und Zurück',
	'continue'				=> 'Fortfahren',
	'language'				=> 'Sprache',
	'inst_finish'			=> 'Installation abschließen',
	'error'					=> 'Fehler',
	'warning'				=> 'Warnung',
	'success'				=> 'Erfolg',
	'yes'					=> 'Ja',
	'no'					=> 'Nein',
	'retry'					=> 'Erneut versuchen',
	'skip'					=> 'Überspringen',
	'step_order_error'		=> 'Step-Order Fehler: Step nicht gefunden. Bitte überprüfe, ob alle Dateien richtig hochgeladen wurden. Für weitere Hilfe besuche bitte unser Forum unter <a href="'.EQDKP_BOARD_URL.'">'.EQDKP_BOARD_URL.'</a>.',
	
	//Step-Names
	'licence'				=> 'Lizenzbedingungen',
	'php_check'				=> 'Voraussetzungen',
	'ftp_access'			=> 'FTP-Einstellungen',
	'encryptionkey'			=> 'Verschlüsselungs-Key',
	'data_folder'			=> 'data-Ordner',
	'db_access'				=> 'Datenbank Zugang',
	'inst_settings'			=> 'Einstellungen',
	'admin_user'			=> 'Administrator Zugang',
	'end'					=> 'Abschließen der Installation',
	
	//Step: licence
	"welcome"				=> "Willkommen zur Installation von EQdkp Plus. Um dieses Gamer CMS & DKP-System zu installieren, lies dir bitte die Lizenzbedingungen durch und klicke anschließend auf 'Akzeptieren & Installation starten'",
	'accept'				=> 'Akzeptieren & Installation starten',
	'license_text'			=> '<b>EQdkp Plus ist unter der AGPL v3.0 Lizenz veröffentlicht.</b><br /><br /> Der vollständige Lizenztext kann unter <a href="http://opensource.org/licenses/AGPL-3.0" target="_blank">http://opensource.org/licenses/AGPL-3.0</a> abgerufen werden.<br /><br />
	
		Nachfolgend eine Zusammenfassung der wichtigsten Punkte der AGPL v3.0. Es besteht kein Anspruch auf Vollständigkeit, Korrektheit und richtige Übersetzung.<br /><br />
	<h3><strong>Du darfst:</strong></h3>
<ul>
<li>die Software für kommerzielle Zwecke einsetzen</li>
<li>die Software verbreiten</li>
<li>die Software verändern</li>
</ul>
<h3><strong>Du musst:</strong></h3>
<ul>
<li>den Quelltext deiner gesamten&nbsp;Anwendung, die EQdkp Plus verwendet, veröffentlichen&nbsp;wenn du diese weitergibst/verbreitest</li>
<li>den Quelltext deiner&nbsp;gesamten Anwendung, die EQdkp Plus verwendet, zur Verfügung stellen, auch wenn diese nicht weitergegeben wird, sondern über ein Netzwerk genutzt wird ("Hosting", "SaaS")</li>
<li>die sicht- und unsichtbaren Copyright-Hinweise bestehen lassen und eine Kopie der AGPL Lizenz deiner&nbsp;Anwendung beilegen</li>
<li>Änderungen am Quelltext kenntlich machen, wenn du deine Anwendung weitergibst/verbreitest</li>
</ul>
<h3><strong>Du darfst nicht:</strong></h3>
<ul>
<li>den Autor/die Autoren von EQdkp Plus&nbsp;verantwortlich für Schäden etc. machen. Die Software wird ohne Garantie ausgeliefert.</li>
<li>deine Software, die EQdkp Plus verwendet, unter eine andere Lizenz als die AGPL stellen</li>
</ul>
		
		
		',
	//Step: php_check
	'table_pcheck_name'		=> 'Name',
	'table_pcheck_required'	=> 'Benötigt',
	'table_pcheck_installed'=> 'Vorhanden',
	'table_pcheck_rec'		=> 'Empfohlen',
	'module_php'			=> 'PHP-Version',
	'module_mysql'			=> 'MySQL Datenbank',
	'module_zLib'			=> 'zLib PHP-Modul',
	'module_safemode'		=> 'PHP Safe Mode',
	'module_curl'			=> 'cURL PHP-Modul',
	'module_fopen'			=> 'fopen PHP-Funktion',
	'module_soap'			=> 'SOAP PHP-Modul',
	'module_autoload'		=> 'spl_autoload_register PHP-Funktion',
	'module_hash'			=> 'hash PHP-Funktion',
	'module_memory'			=> 'PHP Speicherlimit',
	'module_json'			=> 'JSON PHP-Modul',
	'safemode_warning'		=> '<strong>ACHTUNG</strong><br/>Da der  PHP Safe Mode aktiv ist, musst du im nächsten Schritt den FTP-Modus nutzen, ansonsten kann EQdkp Plus nicht verwendet werden!',
	'phpcheck_success'		=> 'Die Mindestanforderungen für die Installation von EQDKP-PLUS werden erfüllt. Die Installation kann fortgesetzt werden.',
	'phpcheck_failed'		=> 'Die Mindestanforderungen für die Installation von EQDKP-PLUS werden leider nicht erfüllt.<br />Eine Auswahl von geeigneten Hostern findest Du auf unserer <a href="'.EQDKP_PROJECT_URL.'" target="_blank">Website</a>',
	'do_match_opt_failed'	=> 'Es werden nicht alle Empfehlungen erfüllt. EQDKP-PLUS wird zwar auf diesem System funktionieren, jedoch eventuell mit Einschränkungen.',
	
	//Step: ftp access
	'ftphost'				=> 'FTP-Host',
	'ftpport'				=> 'FTP-Port',
	'ftpuser'				=> 'FTP-Benutzername',
	'ftppass'				=> 'FTP-Passwort',
	'ftproot'				=> 'Stammverzeichnis',
	'ftproot_sub'			=> '(Pfad zum Wurzelverzeichnis (root) des FTP-Benutzers)',
	'useftp'				=> 'FTP als Datei-Handler verwenden',
	'useftp_sub'			=> '(Kann nachträglich über die config.php de-/aktiviert werden)',
	'safemode_ftpmustbeon'	=> 'Da der PHP Safe Mode an ist, müssen die FTP Daten ausgefüllt werden um mit der Installation fortzufahren.',
	'ftp_connectionerror'	=> 'Die FTP-Verbindung konnte nicht hergestellt werden. Bitte überprüfe den FTP-Host und FTP-Port.',
	'ftp_loginerror'		=> 'Der FTP-Login war nicht erfolgreich. Bitte überprüfe den FTP-Benutzernamen und das FTP-Kennwort.',
	'plain_config_nofile'	=> 'Die Datei <b>config.php</b> ist nicht vorhanden und das automatische Anlegen ist fehlgeschlagen.<br />Bitte erstelle eine leere Textdatei mit dem namen <b>config.php</b> mit chmod 777 und lade sie hoch.',
	'plain_config_nwrite'	=> 'Die Datei <b>config.php</b> ist nicht beschreibbar.<br />Bitte die Berechtigung richtig setzen. <b>chmod 0777 config.php</b>.',
	'plain_dataf_na'		=> 'Der Ordner <b>./data/</b> ist nicht vorhanden.<br />Bitte erstelle ihn. <b>mkdir data</b>.',
	'plain_dataf_nwrite'	=> 'Der Ordner <b>./data/</b> ist nicht beschreibbar.<br />Bitte die Berechtigung richtig setzen. <b>chmod -R 0777 data</b>.',
	'ftp_datawriteerror'	=> 'Der Data Ordner konnte nicht beschrieben werden. Ist der FTP Root path richtig?',
	'ftp_info'				=> 'Anstatt bestimmten EQdkp Plus Dateiordnern Schreibrechte zu geben, kannst du einen FTP-Benutzer deines Servers benutzen, was die Sicherheit als auch die Funktionalität deines EQdkp Plus erhöht.',
	'ftp_tmpinstallwriteerror' => 'Der Ordner <b>./data/97384261b8bbf966df16e5ad509922db/tmp/</b> ist nicht beschreibbar.<br />Damit die Konfigurations-Datei geschrieben werden kann, ist CHMOD 777 notwendig. Dieser Ordner wird nach der Installation entfernt.',
	'ftp_tmpwriteerror' 	=> 'Der Ordner <b>./%stmp/</b> ist nicht beschreibbar.<br />Damit der FTP-Modus verwendet werden kann, ist CHMOD 777 für diesen Ordner notwendig. Dies ist der einzige Ordner, für den Schreibrechte benötigt werden.',
	
	
		
	//Step: db_access
	'dbtype'				=> 'Datenbanktyp',
	'dbhost'				=> 'Datenbankhost',
	'dbname'				=> 'Datenbankname',
	'dbuser'				=> 'Datenbank Benutzername',
	'dbpass'				=> 'Datenbank Passwort',
	'table_prefix'			=> 'Präfix für die EQdkp Tabellen',
	'test_db'				=> 'Datenbank testen',
	'prefix_error'			=> 'Kein oder ungültiges Datenbank Präfix angegeben! Bitte gib ein gültiges Präfix an.',
	'INST_ERR_PREFIX'		=> 'Eine EQdkp Installation mit diesem Präfix existiert bereits. Lösche alle Tabellen mit diesem Präfix und wiederhole diesen Schritt. Alternativ kannst du ein anderes Präfix wählen, wenn du z.B. mehrere EQDKP Plus-Installation in einer Datenbank nutzen willst.',
	'INST_ERR_DB_CONNECT'	=> 'Konnte keine Verbindung mit der Datenbank herstellen, siehe untenstehende Fehlermeldung.',
	'INST_ERR_DB_NO_ERROR'	=> 'Keine Fehlermeldung angegeben.',
	'INST_ERR_DB_NO_MYSQLI'	=> 'Die auf dieser Maschine installierte Version von MySQL ist nicht kompatibel mit der ausgewählten MySQL with MySQLi Extension Option. Bitte versuche stattdessen MySQL.',
	'INST_ERR_DB_NO_NAME'	=> 'Kein Datenbankname angegeben.',
	'INST_ERR_PREFIX_INVALID'	=> 'Der angegebene Datenbank-Prefix ist für diesen Datenbanktyp nicht gültig. Bitte versuche einen anderen, entferne alle Zeichen wie Bindestriche, Apostrophe, Slashes oder Backslashes.',
	'INST_ERR_PREFIX_TOO_LONG'	=> 'Der eingegebene Datenbankprefix ist zu lang. Die maximale Länge beträgt %d Zeichen.',
	'dbcheck_success'		=> 'Die Datenbank wurde überprüft. Es wurden keine Fehler oder Konflikte gefunden. Die Installation kann bedenkenlos fortgesetzt werden.',
	
	//Step: encryptionkey
	'encryptkey_info'		=> 'Der Verschlüsselungs-Key wird benötigt, um sensible Daten wie z.B. Email-Adressen verschlüsselt in der Datenbank abzulegen. Bitte bewahre eine Kopie des Schlüssels an einem sicheren Ort auf.',
	'encryptkey'			=> 'Verschlüsselungs-Key',
	'encryptkey_help'		=> '(der Key muss eine Mindestlänge von 6 Zeichen haben)',
	'encryptkey_repeat'		=> 'Bestätige Verschlüsselungs-Key',
	'encryptkey_no_match'	=> 'Die Verschlüsselungs-Keys stimmen nicht überein.',
	'encryptkey_too_short'	=> 'Der Verschlüsselungs-Key ist zu kurz. Die Mindestlänge beträgt 6 Zeichen.',
	
	//Step: inst_settings
	'inst_db'				=> 'Datenbank installieren',
	'lang_config'			=> 'Sprach-Einstellungen',
	'default_lang'			=> 'Standardsprache',
	'default_locale'		=> 'Standard Lokalisierung',
	'game_config'			=> 'Spiel-Einstellungen',
	'default_game'			=> 'Standard Spiel',
	'server_config'			=> 'Server-Einstellungen',	
	'server_path'			=> 'Scriptpfad',
	//Groups
	'grp_guest'				=> "Gäste",
	'grp_super_admins'		=> "Super-Administratoren",
	'grp_admins'			=> "Administratoren",
	'grp_officers'			=> "Offiziere",
	'grp_writers'			=> "Redakteure",
	'grp_member'			=> "Mitglieder",
	'grp_guest_desc'		=> "Gäste sind nicht eingeloggte Benutzer",
	'grp_super_admins_desc'	=> "Super-Administratoren haben sämtliche Rechte",
	'grp_admins_desc'		=> "Administratoren haben nicht alle Admin-Rechte",
	'grp_officers_desc'		=> "Offiziere dürfen Raids verwalten",
	'grp_writers_desc'		=> "Redakteure dürfen News schreiben und verwalten",
	'grp_member_desc'		=> "Mitglieder",
	"game_info"				=> "Weitere, nicht aufgeführte Spiele können nach der Installation über die Erweiterungsverwaltung heruntergeladen werden.",
	//Date/Time Settings
	'timezone'				=> "Zeitzone des Systems",
	'startday'				=> "erster Tag der Woche",
	'sunday'				=> "Sonntag",
	'monday'				=> "Montag",
	'time_format'			=> 'H:i',
	'date_long_format'		=> 'j. F Y',
	'date_short_format'		=> 'd.m.y',
	"style_jsdate_nrml"		=> "dd.MM.YYYY",
	"style_jsdate_short"	=> "d.M",
	"style_jstime"			=> "H:mm",
	//Welcome-News
	'welcome_news_title'	=> 'Willkommen bei EQdkp-Plus',
	'welcome_news'			=> '<p>Die Installation deines EQdkp-Plus wurde erfolgreich abgeschlossen - du kannst es nun nach deinen Wünschen einrichten.</p>
<p>Hilfestellungen zur Aministration und zur Allgemeinen Benutzung des EQdkps findest Du in unserer <a href="'.EQDKP_WIKI_URL.'" target="_blank">Wiki</a>.</p>
<p>Für weitere Hilfe steht dir unser <a href="'.EQDKP_BOARD_URL.'" target="_blank">Forum</a> zur Verfügung.</p>
<p>Viel Spaß mit dem EQdkp-Plus wünscht das gesamte EQdkp-Plus-Team!</p>',
	'feature_news_title'	=> 'Neue Funktionen von EQdkp Plus',
	'feature_news'			=> '&lt;p&gt;Das EQdkp Plus 2.0 stellt wesentliche neue Funktionen zur Verfügung. Dieser Artikel dient dazu, diese Funktionen etwas näher kennenzulernen.&lt;/p&gt;
&lt;h3&gt;Artikelsystem&lt;/h3&gt;
&lt;p&gt;Anstatt der früheren News und Infoseiten basiert nun alles auf einem Artikelsystem. Jede News und jede Seite ist dabei ein Artikel. Über Artikelkategorien können diese leicht gruppiert werden. Auch kann man dadurch Blogs ermöglichen.&lt;/p&gt;
&lt;p&gt;Die einzelnen Artikel können über einen Weiterlesen-Bereich und Seitenumbrüchen aufgeteilt werden. Auch besteht die Möglichkeit, über den Editor Bildergalerien, Items oder Raidloot in einen Artikel einzufügen.&lt;/p&gt;
&lt;h3&gt;Medienverwaltung&lt;/h3&gt;
&lt;p&gt;Durch die Medienverwaltung ist es nun ein leichtes, Medien in Artikel hinzuzufügen. Dateien können so zum Beispiel über Drag&amp;Drop einfach hochgeladen werden. Desweiteren können Bilder sogar im Medieneditor bearbeitet werden.&lt;/p&gt;
&lt;h3&gt;Menüverwaltung&lt;/h3&gt;
&lt;p&gt;Eine weitere Neuerung ist, dass es nur noch ein zentrales Menü gibt, dass frei angepasst werden kann. Die einzelnen Einträge lassen sich mittels Drag&amp;Drop positieren, und dass sogar in bis zu 3 Ebenen, so dass Untermenüs enstehen. Wie von bisherigen EQdkp Plus Versionen gewohnt, lassen sich auch Links zu externen Seiten anlegen, alles zentral über den &quot;Link hinzufügen&quot;-Button, wo man auch die Verlinkung zu Artikel und -Kategorien vornimmt.&lt;/p&gt;
&lt;h3&gt;Portalverwaltung&lt;/h3&gt;
&lt;p&gt;Früher gab es nur ein ein Portallayout, d.h. auf allen Seiten waren die Portalmodule gleich. Das hat sich nun geändert. So kann man Artikelkategorien andere Portallayouts zuweisen.&lt;/p&gt;
&lt;p&gt;Desweiteren besteht die Möglichkeit, eigene Portalblöcke anzulegen, so dass man diese im Template einbindet, um z.B. Links im Footer einfacher verwalten zu können.&lt;/p&gt;',
	
	//Categories
	'category1'	=> 'System',
	'category2'	=> 'News',
	'category3'	=> 'Events',
	'category4'	=> 'Items',
	'category5'	=> 'Raids',
	'category6'	=> 'Kalender',
	'category7'	=> 'Roster',
	'category8'	=> 'Punktestand',
	'category9'	=> 'Charakter',
		
	//Articles
	'article5' => 'Charakter',
	'article6' => 'Roster',
	'article7' => 'Events',
	'article8' => 'Items',
	'article9' => 'Punktestand',
	'article10' => 'Raids',
	'article12' => 'Kalenderevent',
	'article13' => 'Kalender',
	'article14' => 'Gildenregeln',
	'article15' => 'Datenschutz',
	'article16' => 'Impressum',
		
	//Fallback role-names
	'role_healer' 			=> 'Heiler',
	'role_tank' 			=> 'Tank',
	'role_range' 			=> 'Fernkämpfer',
	'role_melee' 			=> 'Nahkämpfer',
	
	//Step: admin_user
	'create_user'			=> 'Zugang anlegen',
	'username'				=> 'Administrator Benutzername',
	'user_password'			=> 'Administrator Passwort',
	'user_pw_confirm'		=> 'Bestätige Administrator Passwort',
	'user_email'			=> 'Administrator E-Mail-Adresse',
	'auto_login'			=> 'Angemeldet bleiben',
	'user_required'			=> 'Benutzername, E-mail und Passwort müssen ausgefüllt werden!',
	'no_pw_match'			=> 'Die Passwörter stimmen nicht überein.',
	
	//Step: end
	'install_end_text'		=> 'Die Installation kann nun erfolgreich abgeschlossen werden.',
	
	
);
?>