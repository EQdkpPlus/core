<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
	'license_text'			=> '<b>EQdkp Plus ist unter Creative Commons Lizenz "Namensnennung-Nicht-kommerziell-Weitergabe unter gleichen Bedingungen 3.0 Deutschland (CC BY-NC-SA 3.0)" veröffentlicht.</b><br /><br /> Der vollständige Lizenztext kann unter https://creativecommons.org/licenses/by-nc-sa/3.0/de/ abgerufen werden.<br /><br />
	Nachfolgend eine vereinfachte Zusammenfassung des rechtsverbindlichen Lizenzvertrages in allgemeinverständlicher Sprache:<br /><br />
	<b>Sie dürfen:</b><ul><li>das Werk bzw. den Inhalt vervielfältigen, verbreiten und öffentlich zugänglich machen</li><li>Abwandlungen und Bearbeitungen des Werkes bzw. Inhaltes anfertigen</li></ul>
	<b>Zu den folgenden Bedingungen:</b><ul><li><b>Namensnennung</b> — Sie müssen den Namen des Autors/Rechteinhabers in der von ihm festgelegten Weise nennen. </li><li><b>Keine kommerzielle Nutzung</b> — Dieses Werk bzw. dieser Inhalt darf nicht für kommerzielle Zwecke verwendet werden. </li><li><b>Weitergabe unter gleichen Bedingungen</b> — Wenn Sie das lizenzierte Werk bzw. den lizenzierten Inhalt bearbeiten oder in anderer Weise erkennbar als Grundlage für eigenes Schaffen verwenden, dürfen Sie die daraufhin neu entstandenen Werke bzw. Inhalte nur unter Verwendung von Lizenzbedingungen weitergeben, die mit denen dieses Lizenzvertrages identisch oder vergleichbar sind. </li></ul>
	<b>Wobei gilt:</b><ul><li><b>Verzichtserklärung</b> — Jede der vorgenannten Bedingungen kann aufgehoben werden, sofern Sie die ausdrückliche Einwilligung des Rechteinhabers dazu erhalten.</li><li><b>Public Domain (gemeinfreie oder nicht-schützbare Inhalte)</b> — Soweit das Werk, der Inhalt oder irgendein Teil davon zur Public Domain der jeweiligen Rechtsordnung gehört, wird dieser Status von der Lizenz in keiner Weise berührt.</li><li><b>Sonstige Rechte</b> — Die Lizenz hat keinerlei Einfluss auf die folgenden Rechte:<ul><li>Die Rechte, die jedermann wegen der Schranken des Urheberrechts oder aufgrund gesetzlicher Erlaubnisse zustehen (in einigen Ländern als grundsätzliche Doktrin des fair use etabliert); </li><li>Das Urheberpersönlichkeitsrecht des Rechteinhabers; </li><li>Rechte anderer Personen, entweder am Lizenzgegenstand selber oder bezüglich seiner Verwendung, zum Beispiel Persönlichkeitsrechte abgebildeter Personen. </li></ul> </li><li><b>Hinweis</b> — Im Falle einer Verbreitung müssen Sie anderen alle Lizenzbedingungen mitteilen, die für dieses Werk gelten. Am einfachsten ist es, an entsprechender Stelle einen Link auf diese Seite (https://creativecommons.org/licenses/by-nc-sa/3.0/de/) einzubinden. </li></ul>
	',
	//Step: php_check
	'table_pcheck_name'		=> 'Name',
	'table_pcheck_required'	=> 'Benötigt',
	'table_pcheck_installed'=> 'Vorhanden',
	'module_php'			=> 'PHP-Version',
	'module_mysql'			=> 'MySQL Datenbank',
	'module_zLib'			=> 'zLib PHP-Modul',
	'module_safemode'		=> 'PHP Safe Mode',
	'module_curl'			=> 'cURL PHP-Modul',
	'module_fopen'			=> 'fopen PHP-Funktion',
	'module_soap'			=> 'SOAP PHP-Modul',
	'module_autoload'		=> 'spl_autoload_register PHP-Funktion',
	'module_hash'			=> 'hash PHP-Funktion',
	'safemode_warning'		=> '<strong>ACHTUNG</strong><br/>Der PHP Safe Mode ist aktiv, es muss den FTP Modus verwenden, an Sonsten funktioniert EQDKP-PLUS nicht!',
	'phpcheck_success'		=> 'Die Mindestanforderungen für die Installation von EQDKP-PLUS werden erfüllt. Die Installation kann fortgesetzt werden.',
	'phpcheck_failed'		=> 'Die Mindestanforderungen für die Installation von EQDKP-PLUS werden leider nicht erfüllt.<br />Eine Auswahl von geeigneten Hostern findest Du auf unserer <a href="'.EQDKP_PROJECT_URL.'" target="_blank">Website</a>',
	'do_match_opt_failed'	=> 'Es werden nicht alle Bedingungen erfüllt. EQDKP-PLUS wird auf diesem System funktionieren, jedoch können nicht alle Features verwendet werden.',
	
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
	'ftp_tmpwriteerror' 	=> 'Der Ordner <b>./data/%s/tmp/</b> ist nicht beschreibbar.<br />Damit der FTP-Modus verwendet werden kann, ist CHMOD 777 für diesen Ordner notwendig. Dies ist der einzige Ordner, für den Schreibrechte benötigt werden.',
	
	
		
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
	//Date/Time Settings
	'timezone'				=> "Zeitzone des Systems",
	'startday'				=> "erster Tag der Woche",
	'sunday'				=> "Sonntag",
	'monday'				=> "Montag",
	'time_format'			=> 'H:i',
	'date_long_format'		=> 'j. F Y',
	'date_short_format'		=> 'd.m.y',
	//Welcome-News
	'welcome_news_title'	=> 'Willkommen bei EQdkp-Plus',
	'welcome_news'			=> '<p>Die Installation deines EQdkp-Plus wurde erfolgreich abgeschlossen - du kannst es nun nach deinen Wünschen einrichten.</p>
<p>Hilfestellungen zur Aministration und zur Allgemeinen Benutzung des EQdkps findest Du in unserer <a href="'.EQDKP_WIKI_URL.'" target="_blank">Wiki</a>.</p>
<p>Für weitere Hilfe steht dir unser <a href="'.EQDKP_BOARD_URL.'" target="_blank">Forum</a> zur Verfügung.</p>
<p>Viel Spaß mit dem EQdkp-Plus wünscht das gesamte EQdkp-Plus-Team!</p>',
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