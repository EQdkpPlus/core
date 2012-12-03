<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file.php
 * Began: Day January 1 2003
 *
 * $Id$
 *
 ******************************/

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
'inst_header'               => 'Installation',

// ===========================================================
//	Per Language default settings
// ===========================================================
'game_language'             => 'german',
'default_lang'              => 'german',
'default_locale'            => 'de_DE',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation_message'      => 'Meldung',
'installation_messages'     => 'Meldungen',
'error'                     => 'Fehler',
'errors'                    => 'Fehler',
'lerror'                    => 'FEHLER',
'notice'                    => 'MITTEILUNG',
'install_error'             => 'Installationsfehler',
'inst_step'                 => 'Schritt',
'error_nostructure'         => 'Konnte die SQL Struktur/Daten nicht finden',
'error_template'            => "Konnte '%s' includes/template.class.php nicht finden.",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_0'                => 'Lizenzbedingungen',
'stepname_1'                => 'Vorraussetzungen',
'stepname_2'                => 'FTP-Zugang',
'stepname_3'                => 'FTP-Zugang prüfen',
'stepname_4'                => 'Datenbank',
'stepname_5'                => 'Datenbank prüfen',
'stepname_6'                => 'Einstellungen',
'stepname_7'                => 'Admin-Zugang',

// ===========================================================
//	Step 0: Licence-Things
// ===========================================================
'welcome'										=> 'Herzlich Willkommen bei der Installation von EQDKP Plus!<br>Dieser Installationsassistent leitet dich durch die Installation.',
'language_selector'         => 'Bitte wähle die gewünschte Sprache',
'install_language'          => 'Sprache des Installations-Assistenten',
'already_installed'         => 'EQdkp ist bereits installiert - entferne den <b>install/</b> Ordner in diesem Verzeichnis.',
'license'										=> 'Lizenzbedingungen',
'license_text'							=> 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.   

Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet,',
'accept'										=> 'Akzeptieren & Installation starten',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================

'conf_not_write'            => 'Die Datei <b>config.php</b> wurde nicht gefunden und konnte nicht im EQdkp\'s root Ordner erstellt werden.<br />
                                Erstelle händisch eine config.php im Eqdkp Root Ordner.',
'conf_written'              => 'Die Datei <b>config.php</b> wurde erstellt. Die Datei nicht löschen!',
'conf_chmod'                => 'Die Datei <b>config.php</b> ist nicht beschreibbar.
                                <br />Bitte die Berechtigung richtig setzen. <b>chmod 0666 config.php</b>.',
'conf_writable'             => '<b>config.php</b> muss beschreibbar sein, damit das Setup automatisch ablaufen kann.',
'templcache_created'        => "Das Verzeichnis '%1\$s' wurde erstellt. Ein Löschen dieses Verzeichnisses wird das Betreiben von EQDKP-PLUS unmöglich machen.",
'templcache_notcreated'     => "Das Verzeichnis '%1\$s' konnte nicht erstellt werden, bitte erstelle das Verzeichnis manuell.
                                <br />Du kannst dies auf Rootservern recht einfach, indem du in der Konsole folgendes eingibst: <b>mkdir -p %1\$s</b>",
'templcache_notwritable'     => "Das Verzeichnis '%1\$s' existiert, aber ist weder schreibbar noch konnte es automatisch schreibbar gemacht werden.
                                <br />Bitte ändere die Ordnerrechte manuell zu 0777, bei einem Rootserver hilft: <b>chmod 0777 %1\$s</b>.",
'templatecache_ok'          => "Der Ordner '%1\$s' konnte erfolgreich beschrieben werden.",
'connection_failed'         => 'Die Verbindung zu EQdkp-PLUS.com ist fehlgeschlagen.',
'curl_notavailable'         => 'cURL ist nicht verfügbar. Itemstats könnte Probleme beim Abrufen der Gegenstände haben.',
'soap_notavailable'         => 'Die PHP-Klasse "SOAP" ist nicht verfügbar. Einige Funktionalitäten des Eqdkp Plus stehen deshalb nicht zur Verfügung.',
'spl_autoload_register_notavailable'	=> 'Die PHP-Funktion "spl_autoload_register" konnte nicht gefunden werden. Dies liegt vermutlich daran, dass du bei einem Freehoster bist, der diese Funktion nicht zur Verfügung stellt.',
'fopen_notavailable'        => 'fopen ist nicht verfügbar. Itemstats könnte Probleme beim Abrufen der Gegenstände haben.',

'minimal_requ_notfilled'    => '<img src="'.$eqdkp_root_path.'images/false.png"> <span style="font-weight: bold; font-size: 14px;"> Entschuldigung, der Server entspricht nicht den Mindestanforderungen für EQdkp Plus!</span><br />Eine Auswahl von geeigneten Hostern findest Du auf unsere Website unter <a href="http://www.eqdkp-plus.com/page.php?29" target="_blank">http://www.eqdkp-plus.com/page.php?29</a>',
'minimal_requ_filled'       => 'EQdkp Plus hat den Server untersucht und festgestellt, dass er die Mindestanforderungen zur Installation und zum Betrieb von EQdkp Plus erfüllt.',
'safemode_on'             	=> 'Da der PHP Safe-Mode aktiviert ist, ist es zwingend erforderlich, im nächsten Schritt deine FTP Zugangsdaten einzugeben. Andernfalls ist eine Installation von EQdkp Plus nicht möglich.',
'attention'            			=> 'ACHTUNG',
'inst_unknown'              => 'Unbekannt',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'EQDKP Plus Version',
'inst_latest'               => 'Neueste Stabile Version',

'inst_php'                  => 'PHP-Voraussetzungen',
'inst_view'                 => 'phpinfo() ansehen',
'inst_version'              => 'PHP-Version',
'inst_required'             => 'Benötigt',
'inst_available'            => 'Verfügbar',
'inst_enabled'              => 'Aktiviert',
'inst_using'                => 'Benutzt',
'inst_yes'                  => 'Ja',
'inst_no'                   => 'Nein',

'inst_mysqlmodule'          => 'MySQL Modul',
'inst_zlibmodule'           => 'zLib Modul',
'inst_curlmodule'           => 'cURL Modul',
'inst_fopen'                => 'fopen',
'inst_safemode'             => 'Safe Mode',
'inst_soap'             		=> 'SOAP Modul',
'inst_other_functions'      => 'Benötigte PHP-Funktionen',

'inst_found'                => 'gefunden',
'inst_writable'             => 'schreibbar',
'inst_notfound'             => 'nicht gefunden',
'inst_unwritable'           => 'schreibgeschützt',

'inst_button1'              => 'Installation beginnen',
'inst_button_retry'         => 'Erneut prüfen',

// ===========================================================
//	Step 2: FTP
// ===========================================================
'inst_ftp_not_mandotary'    => 'Wenn du deine FTP-Daten eingibst und FTP als File-Handler benutzt, brauchst du dem data-Ordner sowie den Erweiterungen kein CHMOD 777 mehr geben, was die Sicherheit erhöht.',
'inst_ftp_mandotary'        => 'Um das EQdkp Plus benutzen zu können, musst du hier deine FTP-Zugangsdaten eingeben.',
'inst_ftp_conf'        			=> 'FTP-Zugang einrichten',
'inst_ftphost'              => 'FTP-Host',
'inst_ftpport'              => 'FTP-Port',
'inst_ftppath'              => '<b>Stammverzeichnis</b><br /><i>(Pfad zum Wurzelverzeichnis (root) des FTP-Benutzers)</i>',
'inst_ftpuser'              => 'FTP-Benutzername',
'inst_ftppass'              => 'FTP-Passwort',
'inst_useftp'               => '<b>FTP als Datei-Handler verwenden</b><br><i>(Kann nachträglich über die config.php de-/aktiviert werden)</i>',
'inst_button2'              => 'FTP-Zugang testen',
'inst_button_jump'          => 'Überspringen',
'inst_no_ftp'   						 => 'Ohne FTP-Zugang musst du dem data-Ordner CHMOD 777 geben, und wirst einige zukünftige Funktionen nicht verwenden können.<br/>Du kannst einen FTP-Zugang jederzeit in der config.php anlegen.',

// ===========================================================
//	Step 3: FTP Confirmation
// ===========================================================
'inst_ftp_connection_error' => '<img src="../templates/install/images/file_conflict.gif"> Die FTP-Verbindung konnte nicht hergestellt werden. Bitte überprüfe den FTP-Host und FTP-Port.',
'inst_ftp_connection_success'=> '<img src="../templates/install/images/file_up_to_date.gif"> Die FTP-Verbindung konnte erfolgreich hergestellt werden.',
'inst_ftp_login_error' 			=> '<img src="../templates/install/images/file_conflict.gif"> Der FTP-Login war nicht erfolgreich. Bitte überprüfe den FTP-Benutzernamen und das FTP-Kennwort.',
'inst_ftp_login_success' 		=> '<img src="../templates/install/images/file_up_to_date.gif"> Der FTP-Login war erfolgreich',
'inst_button3'              => 'Weiter',


// ===========================================================
//	Step 4: Database
// ===========================================================
'inst_database_conf'        => 'Datenbank Konfiguration',
'inst_dbtype'               => 'Datenbanktyp',
'inst_dbhost'               => 'Datenbankhost',
'inst_dbname'               => 'Datenbankname',
'inst_dbuser'               => 'Datenbank Benutzername',
'inst_dbpass'               => 'Datenbank Passwort',
'inst_table_prefix'         => 'Präfix für die EQdkp Tabellen',
'inst_button4'              => 'Datenbank testen',

// ===========================================================
//	Step 5: Database cofirmation
// ===========================================================

'inst_error_prefix'         => 'Kein Datenbank Prefix angegeben! Bitte gehe zurück und gib ein Prefix an.',
'inst_error_prefix_inval'   => 'Ungültiges Tabellen Prefix.',
//'inserror_dbconnect'        => 'Verbindung zur Datenbank ist fehlgeschlagen.',
//'insterror_no_mysql'        => 'Keine MySQL Datenbank gefunden!',
'inst_redoit'               => 'Installation wiederholen',
'db_warning'                => 'Warnung',
'db_information'            => 'Informationen',
'inst_sqlheaderbox'         => 'SQL Informationen',
'inst_mysqlinfo'            => "MySQL Client <b>und</b> Serverversion 4.0.4 oder höher und InnoDB Tabellenunterstützung werden für den Betrieb von EQdkp benötigt.<br>
                                <b><br>Auf dem Server läuft <ul>%s</ul> und Client Version <ul>%s.</ul></b><br>
                                MySQL Versionen unterhalb von 4.0.4 werden nicht mehr unterstützt. Versionen unterhalb von 4.0.4 werden zu Datenverlust führen. Das EQdkp Plus Support Team wird keine Anfragen bearbeiten, wenn Euer Server diese Anforderung nicht erfüllt.<br><br>",
'inst_button5'              => 'Weiter',
'inst_button_back'          => 'Zurück',
'inst_sql_error'            => "Fehler! Beim Ausrühren folgender SQL Anweisung ist ein Fehler aufgetreten: <br><br><ul>%1\$s</ul><br>Fehler: %2\$s [%3\$s]",
'insinfo_dbready'           => 'Die Datenbank wurde überprüft. Es wurden keine Fehler oder Konflikte gefunden. Die Installation kann bedenkenlos fortgesetzt werden.',

// Errors
//'INST_ERR'                  => 'Installationsfehler',
'INST_ERR_PREFIX'           => 'Eine EQdkp Installation mit diesem Präfix existiert bereits. Lösche alle Tabellen mit diesem Präfix und wiederhole diesen Schritt, in dem du den "Zurück-Button" benutzt. Alternativ kannst du ein anderes Präfix wählen, wenn du z.B. mehrere EQDKP Plus-Installation in einer Datenbank nutzen willst.',
'INST_ERR_DB_CONNECT'       => 'Konnte keine Verbindung mit der Datenbank herstellen, siehe untenstehende Fehlermeldung.',
'INST_ERR_DB_NO_ERROR'      => 'Keine Fehlermeldung angegeben.',
'INST_ERR_DB_NO_MYSQLI'     => 'Die auf dieser Maschine installierte Version von MySQL ist nicht kompatibel mit der ausgewählten MySQL with MySQLi Extension Option. Bitte versuche stattdessen MySQL.',
'INST_ERR_DB_NO_NAME'       => 'Kein Datenbankname angegeben.',
'INST_ERR_PREFIX_INVALID'   => 'Der angegebene Datenbank-Prefix ist für diesen Datenbanktyp nicht gültig. Bitte versuche einen anderen, entferne alle Zeichen wie Bindestriche, Apostrophe, Slashes oder Backslashes.',
'INST_ERR_PREFIX_TOO_LONG'  => 'Der eingegebene Datenbankprefix ist zu lang. Die maximale Länge beträgt %d Zeichen.',

// ===========================================================
//	Step 6: Server
// ===========================================================
'inst_language_config'      => 'Sprach-Einstellungen',
'inst_default_lang'         => 'Standardsprache',
'inst_default_locale'       => 'Standard Lokalisierung',

'inst_game_config'          => 'Spiel-Einstellungen',
'inst_default_game'         => 'Standard Spiel',

'inst_server_config'        => 'Server-Einstellungen',
//'inst_server_name'          => 'Domainname',
//'inst_server_port'          => 'Serverport',
'inst_server_path'          => 'Scriptpfad',

'inst_button6'              => 'Datenbank installieren',

// ===========================================================
//	Step 7: Accounts
// ===========================================================
'inst_administrator_config' => 'Administrator Account Erstellung',
'inst_username'             => 'Administrator Benutzername',
'inst_user_password'        => 'Administrator Passwort',
'inst_user_pw_confirm'      => 'Bestätige Administrator Passwort',
'inst_user_email'           => 'Administrator E-Mail-Adresse',

'inst_button7'              => 'Zugang anlegen',

'inst_writerr_confile'      => 'Die Datei <b>config.php</b> konnte nicht beschrieben werden.  Speichere eine leere Textdatei, benenne sie um in "config.php" und füge folgenden Inhalt ein:',
'inst_confwritten'          => 'Die Konfiguarationsdatei wurde erstellt. Erstelle nun einen Admin-Account.',
'inst_checkifdbexists'      => 'Datenbank vorhanden. Tabellen werden im nächsten Schritt angeleget.',
//'inst_wrong_dbtype'         => "Konnte die Datenbank Abstrakttionsschicht nicht finden <b>%s</b>, stelle sicher das %s existiert.",
'inst_failedconhost'        => "Konnte nicht auf die Datenbank <b>%s</b> als <b>%s@%s verbinden!</b>
                                <br /><br /><a href='index.php'>Installation wiederholen</a>",
'inst_failedversioninfo'    => "Konnte keine Versionsinformationen der Datenbank <b>%s</b> als <b>%s@%s abrufen.</b>
                                <br /><br /><a href='index.php'>Installation wiederholen</a>",

'grp_guest'    									=> "Gäste",																
'grp_super_admins'    					=> "Super-Administratoren",
'grp_admins'    								=> "Administratoren",
'grp_officers'    							=> "Offiziere",
'grp_writers'    								=> "Redakteure",
'grp_member'    								=> "Mitglieder",
'grp_guest_desc'    						=> "Gäste sind nicht eingeloggte Benutzer",																
'grp_super_admins_desc'    			=> "Super-Administratoren haben sämtliche Rechte",
'grp_admins_desc'    						=> "Administratoren haben nicht alle Admin-Rechte",
'grp_officers_desc'    					=> "Offiziere dürfen Raids verwalten",
'grp_writers_desc'    					=> "Redakteure dürfen News schreiben und verwalten",
'grp_member_desc'    						=> "Mitglieder",

'welcome_news_titel'						=> "Willkommen bei EQdkp-Plus",
'welcome_news'									=> "Die Installation deines EQdkp-Plus wurde erfolgreich abgeschlossen - du kannst es nun nach deinen Wünschen einrichten.[br]Hilfestellungen zur Aministration und zur Allgemeinen Benutzung des EQdkps findest Du in unserer [url=http://wiki.eqdkp-plus.com/]Wiki[/url].[br]
Quicklinks zu einigen Einträgen in unserer Wiki:[br]
- [url=http://wiki.eqdkp-plus.com/de/index.php/Ein_Beispielraid]Ein Beispielraid - Von der Raidvorbereitung bis zum Importieren des Raidlogs[/url]
- [url=http://wiki.eqdkp-plus.com/de/index.php/Wie_kommen_die_Items_ins_EQdkp]Wie kommen die Items ins EQdkp?[/url]
- [url=http://wiki.eqdkp-plus.com/de/index.php/Wie_kann_ich_meine_EQdkp_Daten_in_WoW_abrufen]Wie kann ich meine EQdkp Daten in WoW abrufen[/url]
- [url=http://wiki.eqdkp-plus.com/de/index.php/Multi-DKP]Multi-DKP[/url]
- [url=http://wiki.eqdkp-plus.com/de/index.php/CMS-Bridge_konfigurieren]CMS-Bridge konfigurieren[/url]
[br]Für weitere Hilfe steht dir unser [url=http://www.eqdkp-plus.com/forum/]Forum[/url] zur Verfügung, aber halte Dich bitte an die [url=http://www.eqdkp-plus.com/forum/eqdkp-plus-itemstats-f507/forenregeln-german-t798.html]Forums-Regeln[/url].[br]
Viel Spaß mit dem EQdkp-Plus wünscht das gesamte EQdkp-Plus-Team!
",


// ===========================================================
//	Step 8: Finish
// ===========================================================
'login'                     => 'Anmelden',
'username'                  => 'Benutzername',
'password'                  => 'Passwort',
'remember_password'         => 'Anmeldung speichern',

'login_button'              => 'Anmelden',

'inst_passwordnotmatch'     => 'Die beiden Passwörter stimmen nicht überein. Bitte gehe zurück und versuche es erneut.',
'inst_admin_empty'     			=> 'Bitte gehe zurück und fülle alle Felder aus, damit der Administrator-Account erstellt werden kann.',
'inst_admin_created'        => 'Das Administratorenkonto wurde erfolgreich angelegt. Als letzten Schritt bitte einloggen, um EQdkp Plus weiter einzurichten.',
);
?>
