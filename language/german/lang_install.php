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
'game_language'             => 'de',
'default_lang'              => 'german',
'default_locale'            => 'de_DE',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation'              => 'Installation',
'note'                      => 'Notiz',
'notes'                     => 'Notizen',
'error'                     => 'Fehler',
'errors'                    => 'Fehler',
'lerror'                    => 'FEHLER',
'notice'                    => 'MITTEILUNG',
'install_error'             => 'Installationsfehler',
'inst_step'                 => 'Schritt',
'error_nostructure'         => 'Konnte die SQL Struktur/Daten nicht finden',
'error_template'            => "Konnte '%s' includes/class_template.php nicht finden.",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_1'                => 'Informationen',
'stepname_2'                => 'Datenbank',
'stepname_3'                => 'DB prüfen',
'stepname_4'                => 'Serverinfos',
'stepname_5'                => 'Zugänge',
'stepname_6'                => 'Fertigstellen',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================
'language_selector'         => 'Bitte wähle die gewünschte Sprache',
'install_language'          => 'Sprache der Installation',
'already_installed'         => 'EQdkp ist bereits installiert - entferne den <b>install/</b> Ordner in diesem Verzeichnis.',
'conf_not_write'            => 'Die Datei <b>config.php</b> wurde nicht gefunden und konnte nicht im EQdkp\'s root Ordner erstellt werden.<br />
                                Erstelle händisch eine config.php im Eqdkp Root Ordner.',
'conf_written'              => 'Die Datei <b>config.php</b> wurde erstellt. Die Datei nicht löschen!',
'conf_chmod'                => 'Die Datei <b>config.php</b> ist nicht beschreibbar.
                                <br />Bitte die Berechtigung richtig setzen. <b>chmod 0666 config.php</b>.',
'conf_writable'             => '<b>config.php</b> muss beschreibbar sein, damit das Setup automatisch ablaufen kann.',
'templcache_notcreated'     => 'Der Cache-Template Ordner konnte nicht angelegt werden, Bitte diesen händisch anlegen.
                                <br />Gebe dazu ein: <b>mkdir -p templates/cache/</b>',
'templatecache_created'     => 'Der Cache-Template Ordner wurde erfolgreich angelegt. Bitte diesen Ordner jetzt nicht löschen!',
'templatecache_chmod'       => 'Der Cache-Template Ordner existiert, kann aber nicht beschrieben werden. Der Ordner muss beschreibbar sein.
                                <br />Bitte setze die Berechtigung auf 0777 indem <b>chmod 0777 templates/cache</b> eingegeben wird.',
'templatecache_ok'          => 'Der Cache-Template Ordner wurde erfolgreich angelegt.',

'connection_failed'         => 'Die Verbindung zu EQdkp-PLUS.com ist fehlgeschlagen.',
'curl_notavailable'         => 'cURL ist nicht verfügbar. Itemstats könnte Probleme beim Abrufen der Gegenstände haben.',
'fopen_notavailable'        => 'fopen ist nicht verfügbar. Itemstats könnte Probleme beim Abrufen der Gegenstände haben.',

'minimal_requ_notfilled'    => 'Entschuldigung, der Server entspricht nicht den Mindestanforderungen für EQdkp Plus',
'minimal_requ_filled'       => 'EQdkp Plus hat den Server untersucht und festgestellt, dass er die Mindestanforderungen zur Installation und zum Betrieb von EQdkp Plus erfüllt.',

'inst_unknown'              => 'Unbekannt',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'EQDKP Plus Version',
'inst_latest'               => 'Neueste Stabile Version',

'inst_php'                  => 'PHP',
'inst_view'                 => 'phpinfo() ansehen',
'inst_version'              => 'Version',
'inst_required'             => 'Benötigt',
'inst_available'            => 'Verfügbar',
'inst_using'                => 'Benutzt',
'inst_yes'                  => 'Ja',
'inst_no'                   => 'Nein',

'inst_mysqlmodule'          => 'MySQL Modul',
'inst_zlibmodule'           => 'zLib Modul',
'inst_curlmodule'           => 'cURL Modul',
'inst_fopen'                => 'fopen',

'inst_php_modules'          => 'PHP Module',
'inst_Supported'            => 'Unterstützt',

'inst_button1'              => 'Installation beginnen',

// ===========================================================
//	Step 2: Database
// ===========================================================
'inst_database_conf'        => 'Datenbank Konfiguration',
'inst_dbtype'               => 'Datenbanktyp',
'inst_dbhost'               => 'Datenbankhost',
'inst_dbname'               => 'Datenbankname',
'inst_dbuser'               => 'Datenbank Benutzername',
'inst_dbpass'               => 'Datenbank Passwort',
'inst_table_prefix'         => 'Präfix für die EQdkp Tabellen',
'inst_button2'              => 'Datenbank testen',

// ===========================================================
//	Step 3: Database cofirmation
// ===========================================================
'inst_error_nodbname'       => 'Kein Datenbankname angegeben! Bitte gehe zurück und gib einen Tabellennamen an.',
'inst_error_prefix_inval'   => 'Ungültiges Tabellen Präfix.',
'inst_error_prefix_toolong' => 'Tabellen Präfix ist zu lang! Bitte gib ein kürzeres Präfix ein!',
'inserror_dbconnect'        => 'Verbindung zur Datenbank ist fehlgeschlagen.',
'insterror_no_mysql'        => 'Keine MySQL Datenbank gefunden!',
'db_warning'                => 'Warnung',
'db_information'            => 'Informationen',
'insterror_prefix'          => 'Eine EQdkp Installation mit diesem Präfix existiert bereits. Beim Fortsetzen der Installation werden alle vorhandenen Tabellen gelöscht, alle Daten gehen dabei unwiderruflich verloren! Benutze den Zurück-Button des Browsers um ein anderes Präfix anzugeben, oder klicke auf "Weiter", um mit der Installation fortzufahren.',
'insinfo_dbready'           => 'Die Datenbank wurde überprüft. Es wurden keine Fehler oder Konflikte gefunden. Die Installation kann bedenkenlos fortgesetzt werden.',
'inst_sqlheaderbox'         => 'SQL Informationen',
'inst_mysqlinfo'            => "MySQL Client <b>und</b> Serverversion 4.0.4 oder höher und InnoDB Tabellenunterstützung werden für den Betrieb von EQdkp benötigt.<br>
                                <b><br>Auf dem Server läuft <ul>%s</ul> und Client Version <ul>%s.</ul></b><br>
                                MySQL Versionen unterhalb von 4.0.4 werden nicht mehr unterstützt. Versionen unterhalb von 4.0.4<br>
                                werden zu Datenverlust führen. Das EQdkp Plus Support Team wird keine Anfragen bearbeiten, wenn Euer Server diese Anforderung nicht erfüllt.<br><br>",
'inst_button3'              => 'Weiter',

// ===========================================================
//	Step 4: Server
// ===========================================================
'inst_language_config'      => 'Spracheinstellungen',
'inst_default_lang'         => 'Standardsprache',
'inst_default_locale'       => 'Standard Lokalisierung',

'inst_game_config'          => 'Spieleinstellungen',
'inst_default_game'         => 'Standard Spiel',

'inst_server_config'        => 'Server Einstellungen',
'inst_server_name'          => 'Domainname',
'inst_server_port'          => 'Serverport',
'inst_server_path'          => 'Scriptpfad',

'inst_button4'              => 'Datenbank installieren',

// ===========================================================
//	Step 5: Accounts
// ===========================================================
'inst_administrator_config' => 'Administrator Account Erstellung',
'inst_username'             => 'Administrator Benutzername',
'inst_user_password'        => 'Administrator Passwort',
'inst_user_pw_confirm'      => 'Bestätige Administrator Passwort',
'inst_user_email'           => 'Administrator E-Mail-Adresse',

'inst_button5'              => 'Zugang anlegen',

'inst_writerr_confile'      => 'Die Datei <b>config.php</b> konnte nicht beschrieben werden.  Speichere die folgenden Daten als config.php ab:',
'inst_confwritten'          => 'Die Konfiguarationsdatei konnte nur mit Standardwerten geschrieben werden. Erstelle nun einen Admin-Account.',
'inst_checkifdbexists'      => 'Bestätige, dass es die Datenbank mit dem eingegebenen Namen schon gibt.',
'inst_wrong_dbtype'         => "Konnte die Datenbank Abstrakttionsschicht nicht finden <b>%s</b>, stelle sicher das %s existiert.",
'inst_failedconhost'        => "Konnte nicht auf die Datenbank <b>%s</b> als <b>%s@%s verbinden!</b>
                                <br /><br /><a href='index.php'>Installation wiederholen</a>",
'inst_failedversioninfo'    => "Konnte keine Versionsinformationen der Datenbank <b>%s</b> als <b>%s@%s abrufen.</b>
                                <br /><br /><a href='index.php'>Installation wiederholen</a>",

// ===========================================================
//	Step 5: Finish
// ===========================================================
'login'                     => 'Anmelden',
'username'                  => 'Benutzername',
'password'                  => 'Passwort',
'remember_password'         => 'Anmeldung speichern',

'login_button'              => 'Anmelden',

'inst_passwordnotmatch'     => 'Das Passwort passte nicht, so das es auf <b>admin</b> zurück gesetzt wurde.  Es kann aber jederzeit über Einstellungen geändert werden.',
'inst_admin_created'        => 'Das Administratorkonto wurde erfolgreich angelegt. Einloggen um EQdkp Plus weiter einzurichten.',
);
?>
