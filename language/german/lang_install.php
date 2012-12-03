<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* lang_install.php
* Began: Day January 1 2003
*
* $Id: lang_install.php 33 2006-05-08 18:00:40Z tsigo $
*
******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

$lang['inst_header'] = 'EQdkp Installation';

// ===========================================================
//    Step 1: PHP / Mysql Umgebung
// ===========================================================

$lang['inst_eqdkp'] = 'EQdkp';
$lang['inst_version'] = 'Version';
$lang['inst_using'] = 'aktuell';
$lang['inst_latest'] = 'letzte';

$lang['inst_php'] = 'PHP';
$lang['inst_view'] = 'phpinfo() anschauen';
$lang['inst_required'] = 'erfordert';
$lang['inst_major_version'] = 'Hauptversion';
$lang['inst_minor_version'] = 'Testversion';
$lang['inst_version_classification'] = 'Version Klassifikation';
$lang['inst_yes'] = 'Ja';
$lang['inst_no'] = 'Nein';

$lang['inst_php_modules'] = 'PHP Module';
$lang['inst_Supported'] = 'unterstüzte';

$lang['inst_step1'] = 'Installation: Schritt 1';
$lang['inst_note1'] = 'EQdkp hat Ihr System überprüft und festgestellt das es die minimalen Installationsanforderungen erfüllt.';
$lang['inst_note1_error'] = '<B><FONT SIZE="+1" COLOR="red">ACHTUNG</font></B><BR>EQdkp hat Ihr System überprüft und festgestellt das es die minimalen Installationsanforderungen nicht erfüllt..<BR>Bitte auf die minimalen Anforderungen aufrüsten.';
$lang['inst_button1'] = 'Installation beginnen';

// ===========================================================
//    Step 2: Server / Datenbank
// ===========================================================

$lang['inst_language_configuration'] = 'Sprachkonfiguration';
$lang['inst_default_lang'] = 'Standardsprache';

$lang['inst_database_configuration'] = 'Datenbank-Konfiguration';
$lang['inst_dbtype'] = 'Datenbankart';
$lang['inst_dbhost'] = 'Datenbank-Host';
$lang['inst_default_dbhost'] = 'localhost';
$lang['inst_dbname'] = 'Datenbankname';
$lang['inst_dbuser'] = 'Datenbankbenutzername';
$lang['inst_dbpass'] = 'Datenbankkenwort';
$lang['inst_table_prefix'] = 'Präfix für EQdkp Tabellen';
$lang['inst_default_table_prefix'] = 'eqdkp_';

$lang['inst_server_configuration'] = 'Server-Konfiguration';
$lang['inst_server_name'] = 'Domain-Name';
$lang['inst_server_port'] = 'Webserver-Port';
$lang['inst_server_path'] = 'Script-Pfad';

$lang['inst_step2'] = 'Installation: Schritt 2';
$lang['inst_note2'] = 'Bevor Sie fortfahren stellen Sie sicher dass, die Datenbak vorhanden ist und der Datenbankbenutzer denn Sie erhalten haben, das Recht, für das erstellen von Tabellen in dieser Datenbank, haben';
$lang['inst_button2'] = 'Datenbank erstellen';


// ===========================================================
//    Step 3: Konten
// ===========================================================

$lang['inst_administrator_configuration'] = 'Administrator-Konfiguration';
$lang['inst_username'] = 'Administrator-Benutzername';
$lang['inst_user_password'] = 'Administrator-Passwort';
$lang['inst_user_password_confirm'] = 'Administrator-Passwort bestätigen';
$lang['inst_user_email'] = 'Administrator Email Addresse';

$lang['inst_initial_accounts'] = 'Mitgliederkonten';
$lang['inst_guild_members'] = 'Gildenmitglieder';

$lang['inst_step3'] = 'Installation: Schritt 3';
$lang['inst_note3'] = 'Anmerkung: Alle erstellten Mitgliederkonten erhalten beim erstellen ein Kennwort das gleich dem Kontoname ist. Bitte raten Sie Ihren Mitgliedern das Passwort zu ändern.';
$lang['inst_button3'] = 'Konto erstellen';


// ===========================================================
//    Step 4: EQdkp Einstellungen
// ===========================================================

$lang['inst_general_settings'] = 'Allgemeiner Einstellungen';
$lang['inst_guildtag'] = 'Gildentag / Bündnisname';
$lang['inst_guildtag_note'] = 'Wird in fast allen Seiten genutzt';
$lang['inst_parsetags'] = 'Gildentags zum Parsen';
$lang['inst_parsetags_note'] = 'Die hier Aufgeführten sind beim parsen der Raidlogs als Option vorhanden.';
$lang['inst_domain_name'] = 'Domainname';
$lang['inst_server_port'] = 'Server Port';
$lang['inst_server_port_note'] = 'Port Ihres Webservers. Normalerweise 80';
$lang['inst_script_path'] = 'Script Pfad';
$lang['inst_script_path_note'] = 'Pfad wo sich EQdkp befindet, relative zu dem Domainname';
$lang['inst_site_name'] = 'Seitenname';
$lang['inst_site_description'] = 'Seitenbeschreibung';
$lang['inst_point_name'] = 'Punktename';
$lang['inst_point_name_note'] = 'z.B.: DKP, RP, etc.';
$lang['inst_enable_account_activation'] = 'Kontenfreischaltung aktivieren';
$lang['inst_none'] = 'keine';
$lang['inst_user'] = 'Benutzer';
$lang['inst_admin'] = 'Admin';
$lang['inst_default_language'] = 'Standardsprache';
$lang['inst_default_style'] = 'Standardstyle';
$lang['inst_default_page'] = 'Standardindexseite';
$lang['inst_hide_inactive'] = 'Verstecke nicht aktive Mitglieder';
$lang['inst_hide_inactive_note'] = 'Verstecke Mitglieder die länger als [Zeitraum nicht aktiv] Tage nicht an einem Raid teilgenommen haben?';
$lang['inst_inactive_period'] = 'Zeitraum nicht aktiv';
$lang['inst_inactive_period_note'] = 'Anzhal von Tagen die ein Mitglied nicht an einem Raid teilnehmen kann ohne als nicht aktiv zu gelten';
$lang['inst_inactive_point_adj'] = 'Inaktive Punkte Korrektur';
$lang['inst_inactive_point_adj_note'] = 'Punktekorrektur die auf ein Mitglied angewandt wird, wenn es in den Inaktiv-Status übergeht';
$lang['inst_active_point_adj'] = 'Aktive Punkte Korrektur';
$lang['inst_active_point_adj_note'] = 'Punktekorrektur die auf ein Mitglied angewandt wird, wenn es in den Aktiv-Status übergeht';
$lang['inst_enable_gzip'] = 'Gzip-Kompression ermöglichen';
$lang['inst_preview'] = 'Vorschau';
$lang['inst_account_settings'] = 'Kontoeinstellung';
$lang['inst_adjustments_per_page'] = 'Korrektur pro Seite';
$lang['inst_basic'] = 'Allgemein';
$lang['inst_events_per_page'] = 'Ereignisse pro Seite';
$lang['inst_items_per_page'] = 'Items pro Seite';
$lang['inst_news_per_page'] = 'Neue Einträge pro Seite';
$lang['inst_raids_per_page'] = 'Raids pro Seite';

$lang['inst_step4'] = 'Installation: Schritt 4';
$lang['inst_note4'] = 'Anmerkung: Alle diese Einstellungen sind auch innerhalb vom System konfigurierbar. Gehen Sie einfach zu Administration Pannel > Konfiguration.';
$lang['inst_button4'] = 'Einstellungen spreichern';


// ===========================================================
//    Step 5: Ende
// ===========================================================

$lang['inst_step5'] = 'Fertig';
$lang['inst_note5'] = 'Die Installation ist nun beendet. Sie können sich nun unten einloggen';

$lang['login'] = 'LOGON';
$lang['username'] = 'Benutzername';
$lang['password'] = 'Kennwort';
$lang['remember_password'] = 'an mich erinnern (Cookie)';

$lang['lost_password'] = 'Passwort vergessen';

?>
