<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2010-02-25 17:41:21 +0100 (Do, 25. Feb 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: Godmod $
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev: 7339 $
 *
 * $Id: english.php 7339 2010-02-25 16:41:21Z Godmod $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  //Global
  'click2show' => '(klicken, um anzuzeigen)',
	'maintenance_mode' => 'Wartungsbereich',
	'task_manager' => 'Task-Manager',
	'admin_acp' => 'Administrationsbereich',
	'activate_info'	=> '<h1>Wartungsmodus aktivieren</h1><br />Im Wartungsbereich Deines EQdkps kannst zu z.B. dein System aktualisieren und Daten von einer älteren Version des EQdkps importieren. <br />Ein Update oder Import ist nur möglich, wenn sich dein System im Wartungsmodus befindet und anderen Benutzern die Anmeldung verweigert, um Probleme zu verhindern.<br /><br />Grund, der den Benutzern angezeigt werden soll (optional):<br/>',
	'activate_mmode'	=> 'Wartungsmodus aktivieren',
	'deactivate_mmode'	=> 'Wartungsmodus beenden',
	'leave_mmode'	=> 'Abbrechen',
	'home' => 'Home',
	'no_leave' => 'Deaktivieren des Wartungsmodus nicht möglich, solang notwendige Aufgaben ausgeführt werden müssen.',
	'no_leave_accept' => 'Zurück zur Aufgabenübersicht',

  //Maintenance page
  'maintenance_message' => '<b>Das EQdkp Plus-System befindet sich gerade im Wartungsmodus.</b> Eine Anmeldung ist zur Zeit nicht möglich.',
	'reason'	=> '<br /><b>Grund:</b> ',
	'admin_login'		=> 'Administrator-Login',
	'login'		=> 'Anmelden',
  'username' => 'Benutzer',
  'password' => 'Passwort',
  'remember_password' => 'Password merken?',
  'invalid_login_warning' => 'Fehlerhafte Anmeldung! Bitte überprüfe deinen Benutzernamen und Dein Passwort. Nur Administratoren ist es erlaubt, sich anzumelden.',

  //Task manager
  'is_necessary' => 'Notwendig?',
  'is_applicable' => 'Anwendbar?',
  'name' => 'Name',
  'version' => 'Version',
  'author' => 'Autor',
  'link' => 'Aufgabe ausführen',
  'description' => 'Beschreibung',
  'type' => 'Aufgaben-Typ',
  'yes' => 'Ja',
  'no' => 'Nein',
  'click_me' => 'Aufgabe ausführen',
  'mmode_info' => 'Willkommen im Wartungsbereich deines EQdkp Plus-Systems. Hier kannst du dein EQdkp aktualisieren, und ältere Versionen von EQdkp Plus importieren.<br />Um Probleme zu verhindern, können sich Benutzer solange nicht anmelden, bis du den Wartungsmodus beendet hast.',
  'mmode_pcache_error' => 'Es sind einige Fehler aufgetreten. Du musst die Fehler beheben, um den Maintenance-Mode zu deaktivieren.',
  'necessary_tasks' => 'Notwendige Aufgaben',
  'applicable_tasks' => 'Nicht notwendige/bereits ausgeführte Aufgaben',
  'not_applicable_tasks' => 'Nicht-Ausführbare Aufgaben',
  'no_nec_tasks' => 'Keine Aufgaben notwendig.',
  'nec_tasks' => 'Folgende Aufgaben sind notwendig, bitte führe sie aus, um das System auf den aktuellsten Stand zu bringen.',
	'nec_tasks_available' => 'Bitte führe die notwendigen Tasks aus, um das System auf den aktuellesten Stand zu bringen',
	'applicable_warning' => 'Diese Aufgabe ist nicht notwendig! Ein Ausführen kann Datenverlust zur Folge haben! Führe diese Aufgabe nur aus, wenn du dir absolut sicher bist!',
	'executed_tasks'	=> 'Folgende Aktionen wurden für die Aufgabe "%s" ausgeführt',
	'stepend_info'		=> 'Die Aufgabe wurde beendet. Das EQdkp Plus befindet sich aber noch im Wartungsmodus, damit du alles durchtesten kannst. Erst nachdem der Wartungsmodus beendet ist, können sich Benutzer wieder anmelden.',
	

  //Task types
  'fix' => 'Fix',
  'update' => 'Update',
  'import' => 'Import',
  'plugin_update' => 'Plugin-Update',

  //Task page
  'unknown_task_warning' => 'Unbekannter Task!',
  'application_warning' => 'Konnte Aufgabe aufgrund eines Applikations-Fehlers nicht ausführen!',
  'dependency_warning' => 'Dieser Task ist von anderen abhängig. Führe diese zuerst aus!',
  'start_here' => 'Beginne hier!',

  //Sql-Updates
  'following_updates_necessary' => 'Die folgenden Aktualisierungen sind notwendig: ',
  'start_update' => 'Führe alle notwendigen Updates aus!',
  'only_this_update' => 'Führe nur dieses Update aus: ',
	'start_single_update' => 'Update ausführen',
	
	//Splash
	'splash_welcome'	=> 'Willkommen im Wartungsbereich deines EQdkp Plus-Systems!',
	'splash_desc' 		=> 'Hier kannst du dein EQdkp aktualisieren, und ältere Versionen von EQdkp Plus importieren.',
	'splash_new'		=> 'Dir ist EQdkp Plus neu? Du hast noch nie DKP-Punkte vergeben oder Raids eingetragen?',
	'start_tour'		=> 'Tour starten',
	'jump_tour'			=> 'Tour überspringen',
	'06_import'			=> 'alte 0.6 Daten importieren',
	'guild_import'		=> 'Gilde von externer Datenbank importieren (z.B. Armory; nicht von allen Spielen unterstützt!)',
);
?>