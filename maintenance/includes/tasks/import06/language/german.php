<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2009 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}
$lang['import06'] = 'Importiere Daten vom 0.6';
$lang['import_steps'] = 'Durchzuführere Importschritte auswählen';
$lang['config_news_log'] = 'Einstellungen, News, Logs';
$lang['users_auths'] = 'Benutzer, Berechtigungen';
$lang['events'] = 'Events';
$lang['multidkp'] = 'Multidkp';
$lang['members'] = 'Charaktere';
$lang['raids'] = 'Raids';
$lang['items'] = 'Items';
$lang['adjustments'] = 'Korrekturen';
$lang['dkp_check'] = 'DKP Überprüfen';
$lang['infopages'] = 'Infopages';
$lang['plugins_portal'] = 'Plugins, Portalmodule';
$lang['item_cache'] = 'item_cache';

$lang['submit'] = 'Abschicken';
$lang['select_all'] = '(alles auswählen)';
$lang['negate_selection'] = 'Checkbox Auswahl umkehren';
$lang['no_problems'] = 'Es wurden keine Probleme gefunden. Klicke auf Absenden, um mit den nächsten Schritt fortzufahren.';
$lang['date_format'] = 'm/d/Y';
$lang['no_import'] = 'nicht importieren';
$lang['dont_import'] = 'Abbrechen';
$lang['nothing_imported'] = 'Import wurde abgebrochen.';
$lang['page'] = 'Seite';

//first step
$lang['database_info'] = 'Informationen über das zu importierende EQdkp Plus';
$lang['database_other'] = 'Daten liegen in einer anderen Datenbank?';
$lang['only_other'] = ' (nur auszufüllen wenn Daten in andere Datenbank)';
$lang['host'] = 'Datenbank-Server'.$lang['only_other'];
$lang['db_name'] = 'Datenbank-Name'.$lang['only_other'];
$lang['user'] = 'Datenbank-Benutzer'.$lang['only_other'];
$lang['password'] = 'Datenbank-Passwort'.$lang['only_other'];
$lang['table_prefix'] = 'Tabellen-Prefix des zu importierenden EQdkp Plus';

$lang['import'] = 'Import';
$lang['older_than'] = 'älter als';
$lang['config'] = 'Einstellungen';
$lang['news'] = 'News';
$lang['log'] = 'Logs';
$lang['styles'] = 'Styles';
$lang['enter_date_format'] = "Trage das Datum im Format 'DD.MM.YYYY' ein";  //do not change order of date

$lang['which_users'] = 'Welche Benutzer sollen importiert werden?';
$lang['your_user'] = 'Wähle deinen eigenen Benutzer aus (Dieser Benutzer wird nicht importiert und die ID dieses Benutzers wird durch die ID deines aktuellen Benutzers ersetzt):';
$lang['no_user'] = 'Kein Benutzer vorhanden';
$lang['admin'] = 'Administrator';
$lang['notice_admin_perm'] = 'Nur Benutzer, die das Recht zur Änderung der Einstellungen hatten, werden in die Admin-Benutzergruppe übernommen. Alle anderen Administrator-Berechtigungen werden nicht importiert!! Bitte überprüfe die Berechtigungen aller Benutzer nach dem Import-Vorgang!';

$lang['which_events'] = 'Welche Events sollen importiert werden?';

$lang['no_multi_found'] = 'Es konnte kein Multidkp-Pool gefunden werden. Bitte trage einen Namen und eine Beschreibung ein, um einen Multidkp-Pool zu erstellen. Alle Events werden diesem Pool zugeordnert. Dies kann später bei Bedarf wieder geändert werden.';
$lang['multi_name'] = 'Name des Multidkp-Pools';
$lang['multi_desc'] = 'Beschreibung des Multidkp-Pools';
$lang['which_multis'] = 'Welche Multidkp-Pools sollen importiert werden?';

$lang['which_members'] = 'Welche Charaktere sollen importiert werden';
$lang['import_ranks'] = 'Importiere Ränge';
$lang['create_special_members'] = 'Erzeuge spezielle Charaktere, wie z.B. "Bank" oder "Disenchanted". Lasse die Felder leer, wenn keine speziellen Charaktere benötigt werden.';

$lang['change_checked_to'] = 'Ändere ausgewählte zu';

$lang['raids_with_no_event'] = 'Einigen Raids ist kein Event zugeordnert. Bitte ordne den Raids on Event zu, um fortfahren zu können.';
$lang['event_name'] = 'Event-Name in der alten Datenbank';
$lang['raid_id'] = 'Raid-ID';

$lang['items_without_raid'] = 'Einige Items wurden einem nicht existieren Raid zugeordnert. Bitte ordne sie einem Raid zu.';
$lang['items_without_member'] = 'Einige Items wurden einem nicht existierenden Charakter zugeordnert. Bitte ordne sie einem Mitglied zu.';
$lang['item_buyer'] = 'Käufer der Items';
$lang['change_item_to'] = 'Käufer für diese Items ändern zu';
$lang['item_buyer_2'] = 'Käufer mit zwei oder weniger Items';

$lang['adjs_without_event'] = 'Einige Korrekturen wurden einem nicht exitierenden Event zugeordnert. Bitte ordne sie einem Event zu.';
$lang['adjs_without_member'] = 'Einige Korrekturen wurden einem nicht existierenden Charakter zugeordnert. Bitte ordne sie einem Mitglied zu.';

$lang['member_with_diff'] = 'Einige Mitglieder haben abweichende DKP-Punkte, da diese Version von EQdkp Plus nur noch MultiDKP-Systeme unterstützt. Bitte wähle aus, wie mit den Punktständen weiter verfahren werden soll.';
$lang['mem_diff_create_adj'] = 'Erstelle eine Korrektur, damit der Punktestand dem Alten entspricht.';
$lang['mem_diff_ignore'] = 'Ignoriere die Punktedifferenz';
$lang['mem_diff_adj_reason'] = 'Punktedifferenz aufgrund MultiDKP';

$lang['which_infopages'] = 'Welche Info-Seiten sollen importiert werden? Bitte überprüfe die Sichtbarkeit nach Abschluss des Imports.';

$lang['which_plugins'] = 'Welche Plugins sollen importiert werden? Plugins, die importiert werden sollen, müssen in diesem EQdkp Plus-System installiert sein/werden. Für alle installierten Plugins werden Daten importiert.';
$lang['which_portals'] = 'Welche Portal-Module sollen importiert werden?';
$lang['install'] = 'Installieren';
$lang['installed'] = 'Installation erfolgreich';
$lang['uninstall'] = 'Deinstallieren';

$lang['import_end'] = 'Import abgeschlossen.';

?>