<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* lang_main.php
* begin: Wed December 18 2002
*
* $Id$
*
******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['XML_LANG'] = 'de';
$lang['ISO_LANG_SHORT'] = 'de_DE';
$lang['ISO_LANG_NAME'] = 'Deutsch';

$lang['style_date_javascript']				= 'd.m.Y' ;		// DO NOT CHANGE THIS!!!
//Can be edited
$lang['style_time']							= 'H:i';
$lang['style_date_long']					= 'j. F Y';
$lang['style_date_short']					= 'd.m.y';

$lang['time_daynames']						= array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
$lang['time_monthnames']					= array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
//$lang['listadj_title']        = 'Gruppen-Korrekturliste';
//$lang['listevents_title']     = 'Ereignis-Werte';
//$lang['listiadj_title']       = 'Individuelle Korrekturliste';
$lang['listitems_title']      = 'Item-Werte';
$lang['listnews_title']       = 'News Einträge';
$lang['listmembers_title']    = 'Charakter Statistik';
//$lang['listpurchased_title']  = 'Item-Historie';
$lang['listraids_title']      = 'Raidliste';
$lang['login_title']          = 'Login';
$lang['message_title']        = 'EQdkp: Nachricht';
$lang['newsarchive_title']    = 'News-Archiv';
$lang['register_title']       = 'Registrieren';
$lang['settings_title']       = 'Account Einstellungen';
//$lang['stats_title']          = "%1\$s Statistiken";
//$lang['summary_title']        = 'News Zusammenfassung';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Gespeicherte Raid Historie für %1\$s sehen";
$lang['viewitem_title']       = "Kauf-Historie für %1\$s sehen";
$lang['viewmember_title']     = "Historie für %1\$s";
$lang['viewraid_title']       = 'Raid Zusammenfassung';

// Main Menu
$lang['menu_admin_panel'] = 'Administrationsbereich';
$lang['menu_events'] = 'Ereignisse';
$lang['menu_itemhist'] = 'Item-Historie';
$lang['menu_itempools'] = 'Item-Pools';
//$lang['menu_itemval'] = 'Item-Preise';
$lang['menu_news'] = 'News';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'Registrieren';
$lang['menu_settings'] = 'Einstellungen';
$lang['menu_members'] = 'Charaktere';
$lang['menu_standings'] = 'Punktestand';
//$lang['menu_stats'] = 'Statistik';
//$lang['menu_summary'] = 'Zusammenfassung';

// Column Headers
$lang['account'] = 'Account';
$lang['action'] = 'Aktion';
$lang['actions'] = 'Aktionen';
$lang['active'] = 'Aktiv';
$lang['add'] = 'Zufügen';
$lang['added_by'] = 'Zugefügt von';
$lang['adjustment'] = 'Korrektur';
$lang['administration'] = 'Administration';
//$lang['administrative_options'] = 'Administrative Einstellungen';
$lang['admin_index'] = 'Admin Index';
$lang['attendance_by_event'] = 'Beteiligung bei Ereignis';
$lang['attendance'] = 'Beteiligung';
$lang['attended'] = 'Teilgenommen';
$lang['attendees'] = 'Teilnehmer';
$lang['average'] = 'Durchschnitt';
$lang['buyer'] = 'Käufer';
$lang['buyers'] = 'Käufer';
$lang['class'] = 'Klasse';
//$lang['armor'] = 'Rüstung';
$lang['type'] = 'Rüstung';
$lang['class_distribution'] = 'Klassenverteilung';
//$lang['class_summary'] = "Klassen-Zusammenfassung: %1\$s bis %2\$s";
$lang['configuration'] = 'Konfiguration';
//$lang['config_plus']	= 'PLUS Einstellungen';
$lang['create_raid_summary']	= 'Raidzusammenfassung erstellen';
//$lang['plus_vcheck']	= 'Update Check';
$lang['current'] = 'Jetzt';
$lang['date'] = 'Datum';
$lang['delete'] = 'Löschen';
$lang['delete_confirmation'] = 'Löschbestätigung';
//$lang['dkp_value'] = "%1\$s Wert";
$lang['drops'] = 'Drops';
$lang['earned'] = 'Bekommen';
//$lang['enter_dates'] = 'Daten eingeben';
$lang['eqdkp_index'] = 'EQdkp Index';
//$lang['eqdkp_upgrade'] = 'EQdkp Upgrade';
$lang['event'] = 'Ereignis';
$lang['events'] = 'Ereignisse';
$lang['filter'] = 'Filter';
$lang['first'] = 'Erster';
$lang['rank'] = 'Rang';
$lang['general_admin'] = 'Allgemeine Administration';
$lang['get_new_password'] = 'Neues Passwort zusenden';
$lang['get_new_activation_mail'] = 'Bestätigungsmail erneut zusenden';
$lang['group_adj'] = 'Gruppen-Kor.';
$lang['group_adjustments'] = 'Gruppen Korrekturen';
$lang['individual_adjustments'] = 'Individuelle Korrekturen';
$lang['individual_adjustment_history'] = 'Individuelle Korrektur-Historie';
$lang['indiv_adj'] = 'Indiv. Kor.';
$lang['ip_address'] = 'IP-Adresse';
$lang['item'] = 'Item';
$lang['items'] = 'Items';
$lang['item_purchase_history'] = 'Item-Kauf-Historie';
$lang['last'] = 'Letzter';
$lang['lastloot'] = 'Letzter Loot';
$lang['lastraid'] = 'Letzter Raid';
$lang['last_visit'] = 'Letzter Besuch';
$lang['level'] = 'Level';
//$lang['log_date_time'] = 'Datum/Zeit von diesem Log';
//$lang['loot_factor'] = 'Loot Faktor';
//$lang['loots'] = 'Loots';
$lang['mainchar'] = 'Maincharakter';
$lang['mainchar_help'] = "Wenn der neue Charakter ein Maincharakter sein soll, wähle 'Maincharakter' aus, ansonsten ordne ihn den Hauptcharakter zu.";
$lang['manage'] = 'Verwalten';
$lang['member'] = 'Charakter';
$lang['members'] = 'Charaktere';
$lang['members_present_at'] = "Charaktere anwesend am %1\$s um %2\$s";
//$lang['miscellaneous'] = 'Diverses';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['categories'] = 'Kategorien';
$lang['note'] = 'Notiz';
$lang['online'] = 'Online';
$lang['options'] = 'Einstellungen';
$lang['paste_log'] = 'Füge ein Log unten ein';
$lang['percent'] = 'Prozent';
$lang['permissions'] = 'Berechtigungen';
$lang['per_day'] = 'Pro Tag';
$lang['per_raid'] = 'Pro Raid';
$lang['pct_earned_lost_to'] = 'Verdientes verringert durch';
$lang['preferences'] = 'Voreinstellungen';
$lang['view_options'] = 'Anzeige-Optionen';
$lang['purchase_history_for'] = "Kauf-Historie für %1\$s";
//$lang['quote'] = 'Zitat';
$lang['quote_of'] = 'Zitat von';
$lang['race'] = 'Rasse';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'Raidbeteiligungs-Historie';
$lang['raids_lifetime'] = "Lebensdauer (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Letzten %1\$d Tage";
$lang['rank_distribution'] = 'Rang-Aufteilung';
$lang['recorded_raid_history'] = "Gespeicherte Raid-Historie für %1\$s";
$lang['reason'] = 'Grund';
$lang['registration_information'] = 'Registrierungs-Details';
$lang['result'] = 'Ergebnis';
$lang['select_month'] = 'Monat auswählen';
$lang['session_id'] = 'Session ID';
$lang['settings'] = 'Einstellungen';
$lang['spent'] = 'Ausgegeben';
$lang['status'] = 'Status';
//$lang['summary_dates'] = "Raid Zusammenfassung: %1\$s bis %2\$s";
//$lang['themes'] = 'Themes';
$lang['time'] = 'Zeit';
$lang['total'] = 'Gesamt';
$lang['total_earned'] = 'Gesamt verdient';
$lang['total_items'] = 'Gesamte Items';
$lang['total_raids'] = 'Gesamte Raids';
$lang['total_spent'] = 'Gesamt ausgegeben';
$lang['transfer_member_history'] = 'Charakter-Historie';
//$lang['turn_ins'] = 'Turn-ins';
$lang['type'] = 'Typ';
$lang['update'] = 'Aktualisieren';
$lang['updated_by'] = 'aktualisiert von';
$lang['user'] = 'Benutzer';
$lang['username'] = 'Benutzername';
$lang['value'] = 'Wert';
$lang['view'] = 'Ansehen';
$lang['view_action'] = 'Aktion ansehen';
$lang['view_logs'] = 'Logs ansehen';

// Page Foot Counts
$lang['listadj_footcount']               = "... %1\$d Korrektur(en) gefunden / %2\$d pro Seite";
$lang['listevents_footcount']            = "... %1\$d Ereignis(se) gefunden / %2\$d pro Seite";
//$lang['listiadj_footcount']              = "... %1\$d individuelle Korrektur(en) gefunden / %2\$d pro Seite";
$lang['listitems_footcount']             = "... %1\$d einmalige(s) Item(s) gefunden / %2\$d pro Seite";
$lang['listmembers_active_footcount']    = "... %1\$d aktive(r) Charakter(e) gefunden / %2\$sZeige alle</a>";
$lang['listmembers_compare_footcount']   = "... vergleiche %1\$d Charaktere";
$lang['listmembers_footcount']           = "... %1\$d Charakter(e) gefunden";
$lang['listnews_footcount']              = "... %1\$d Newseinträge gefunden / %2\$d pro Seite";
//$lang['listpurchased_footcount']         = "... %1\$d Item(s) gefunden / %2\$d pro Seite";
$lang['listraids_footcount']             = "... %1\$d Raid(s) gefunden / %2\$d pro Seite";
//$lang['stats_active_footcount']          = "... %1\$d Aktive(r) Charakter(e) gefunden / %2\$sZeige alle</a>";
//$lang['stats_footcount']                 = "... %1\$d Charakter(e) gefunden";
//$lang['viewevent_footcount']             = "... %1\$d Raid(s) gefunden";
$lang['viewitem_footcount']              = "... %1\$d Item(s) gefunden";
$lang['viewmember_adjustment_footcount'] = "... %1\$d individuelle Korrektur(en) gefunden";
//$lang['viewmember_item_footcount']       = "... %1\$d gekaufte Item(s) gefunden / %2\$d pro Seite";
//$lang['viewmember_raid_footcount']       = "... %1\$d teilgenommene(n) Raid(s) gefunden / %2\$d pro Seite";
$lang['viewraid_attendees_footcount']    = "... %1\$d Teilnehmer gefunden";
$lang['viewraid_drops_footcount']        = "... %1\$d Drop(s) gefunden";
$lang['hptt_default_footcount'] = "... %1\$d Einträge gefunden";
$lang['hptt_default_part_footcount'] = "... %1\$d Einträge gefunden / %2\$d werden angezeigt";

// Submit Buttons
//$lang['close_window'] = 'Fenster schließen';
$lang['compare_members'] = 'Vergleiche Charaktere';
$lang['create_news_summary'] = 'Erstelle Raidzusammenfassung';
$lang['login'] = 'Anmelden';
$lang['logout'] = 'Abmelden';
$lang['logged_in'] = 'Angemeldet';
$lang['log_add_data'] = 'Daten zum Formular zufügen';
$lang['lost_password'] = 'Passwort vergessen';
$lang['lost_password_email_info'] = 'Du musst die E-Mail-Adresse angeben, die in deinem Benutzereinstellungen hinterlegt ist. Diese hast du bei der Registrierung angegeben oder nachträglich in deinen Einstellungen geändert.';
$lang['validation_email_info'] = 'Du musst die E-Mail-Adresse angeben, mit der du dich registriert hast. Solltest du diese nicht mehr wissen, frage deinen Administrator.';
$lang['no'] = 'Nein';
$lang['proceed'] = 'Fortfahren';
$lang['reset'] = 'Zurücksetzen';
$lang['set_admin_perms'] = 'Administrative Berechtigungen setzen';
$lang['submit'] = 'Abschicken';
//$lang['upgrade'] = 'Upgrade';
$lang['yes'] = 'Ja';
$lang['back'] = 'Zurück';

// Form Element Descriptions
$lang['admin_login'] = 'Administrator Login';
$lang['confirm_password'] = 'Passwort bestätigen';
$lang['confirm_password_note'] = 'Du musst Dein Passwort nur bestätigen, wenn Du es oben geändert hast.';
$lang['current_password'] = 'Aktuelles Passwort';
$lang['current_password_note'] = 'Du musst Dein jetziges Passwort nur bestätigen, wenn Du Benutzername oder Passwort ändern willst.';
$lang['email'] = 'E-Mail';
$lang['email_confirm'] = 'E-Mail Adresse bestätigen';
$lang['email_address'] = 'E-Mail-Adresse';
$lang['ending_date'] = 'Enddatum';
//$lang['from'] = 'Von';
//$lang['guild_tag'] = 'Gildenbezeichnung';
$lang['language'] = 'Sprache';
$lang['new_password'] = 'Neues Passwort';
$lang['new_password_note'] = 'Du brauchst nur ein neues Passwort eingeben, wenn Du es ändern willst.';
$lang['password'] = 'Passwort';
$lang['remember_password'] = 'Mich bei jedem Besuch automatisch anmelden';
$lang['starting_date'] = 'Startdatum';
$lang['style'] = 'Stil';
//$lang['to'] = 'Zu';
$lang['username'] = 'Benutzername';
$lang['users'] = 'Benutzer';

// Pagination
$lang['next_page'] = 'Nächste Seite';
$lang['page'] = 'Seite';
$lang['previous_page'] = 'Vorherige Seite';

// Permission Messages
$lang['noauth_default_title'] = 'Zugriff verweigert';
$lang['noauth_hostmode'] = 'Dieses Feature steht im vereinfachten Modus nicht zur Verfügung';
$lang['noauth_u_event_list'] = 'Du hast keine Berechtigung Ereignisse aufzulisten.';
$lang['noauth_u_event_view'] = 'Du hast keine Berechtigung Ereignisse zu sehen.';
$lang['noauth_u_item_list'] = 'Du hast keine Berechtigung Items aufzulisten.';
$lang['noauth_u_item_view'] = 'Du hast keine Berechtigung Items zu sehen.';
$lang['noauth_u_member_list'] = 'Du hast keine Berechtigung Punktestände zu sehen.';
$lang['noauth_u_member_view'] = 'Du hast keine Berechtigung Historien der Charaktere zu sehen.';
$lang['noauth_u_raid_list'] = 'Du hast keine Berechtigung Raids aufzulisten.';
$lang['noauth_u_raid_view'] = 'Du hast keine Berechtigung Raids zu sehen.';
$lang['noauth_u_information_view'] = 'Du hast keine Berechtigung diese Seite anzusehen.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde gespeichert.';
$lang['update_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde aktualisiert.';
$lang['update_settings_success'] = 'Die Benutzereinstellungen wurden aktualisiert.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Char\' Namen in EverQuest beinhalten nur alphabetische Zeichen.';
$lang['fv_already_registered_email'] = 'Diese E-Mail-Adresse ist bereits registriert.';
$lang['fv_already_registered_username'] = 'Dieser Benutzername ist bereits registriert.';
$lang['fv_difference_transfer'] = 'Ein Historientransfer geht nur zwischen zwei unterschiedlichen Leuten.';
//$lang['fv_difference_turnin'] = 'Ein Turn-in geht nur zwischen zwei unterschiedlichen Leuten.';
$lang['fv_invalid_email'] = ' Die E-Mail-Adresse scheint nicht gültig zu sein.';
$lang['fv_match_password'] = 'Die Passwortfelder müssen übereinstimmen.';
$lang['fv_match_email'] = 'Die Email-Felder müssen übereinstimmen.';
$lang['fv_member_associated']  = "%1\$s gehört bereits zu einem anderen Benutzeraccount.";
$lang['fv_number'] = 'Muss eine Zahl sein.';
$lang['fv_number_adjustment'] = 'Das Korrekturwert-Feld muss eine Zahl sein.';
$lang['fv_number_alimit'] = 'Das Korrekturgrenze-Feld muss eine Zahl sein.';
$lang['fv_number_ilimit'] = 'Das Itemgrenze-Feld muss eine Zahl sein.';
$lang['fv_number_inactivepd'] = 'Der inaktive Zeitraum muss eine Zahl sein.';
$lang['fv_number_pilimit'] = 'Die gekaufte-Items-Grenze muss eine Zahl sein.';
$lang['fv_number_rlimit'] = 'Die Raidgrenze muss eine Zahl sein.';
$lang['fv_number_value'] = 'Das Werte-Feld muss eine Zahl sein.';
$lang['fv_number_vote'] = 'Das Abstimmungs-Feld muss eine Zahl sein.';
$lang['fv_date'] = 'Bitte wähle ein gültiges Datum aus dem Kalender aus.';
$lang['fv_range_day'] = 'Das Tag-Feld muss eine Zahl zwischen 1 und 31 sein.';
$lang['fv_range_hour'] = 'Das Stunden-Feld muss eine Zahl zwischen 0 und 23 sein.';
$lang['fv_range_minute'] = 'Das Minuten-Feld muss eine Zahl zwischen 0 und 59 sein.';
$lang['fv_range_month'] = 'Das Monats-Feld muss eine Zahl zwischen 1 und 12 sein.';
$lang['fv_range_second'] = 'Das zweite Feld muss eine Zahl zwischen 0 und 59 sein.';
$lang['fv_range_year'] = 'Das Jahr-Feld muss mindestens eine Zahl ab 1998 sein.';
$lang['fv_required'] = 'Pflichtfeld';
$lang['fv_required_acro'] = 'Das Gildenkurzwort-Feld ist notwendig.';
$lang['fv_required_adjustment'] = 'Das Korrekturwert-Feld ist notwendig.';
$lang['fv_required_attendees'] = 'Es muss mindestens ein Teilnehmer im Raid sein.';
$lang['fv_required_buyer'] = 'Ein Käufer muss ausgewählt sein.';
$lang['fv_required_buyers'] = 'Mindestens ein Käufer muss ausgewählt sein.';
$lang['fv_required_email'] = 'Das E-Mail-Adressen-Feld ist notwendig.';
$lang['fv_required_event_name'] = 'Ein Ereignis muss ausgewählt sein.';
$lang['fv_required_guildtag'] = 'Das Gildenbezeichnungs-Feld ist notwendig.';
$lang['fv_required_headline'] = 'Das Kopfzeilen-Feld ist notwendig.';
$lang['fv_required_inactivepd'] = 'Wenn inaktive Charaktere verstecken auf Ja gesetzt ist, muss auch ein Wert für die inaktive Zeit angegeben werden.';
$lang['fv_required_item_name'] = 'Das Itemname-Feld muss ausgefüllt sein, oder ein vorhandenes Item muss ausgewählt sein.';
$lang['fv_required_member'] = 'Ein Charakter muss ausgewählt sein.';
$lang['fv_required_members'] = 'Mindestens ein Charakter muss ausgewählt sein.';
$lang['fv_required_message'] = 'Das Nachrichten-Feld ist notwendig.';
$lang['fv_required_name'] = 'Das Namens-Feld ist notwendig.';
$lang['fv_required_password'] = 'Das Passwort-Feld ist notwendig.';
$lang['fv_required_raidid'] = 'Ein Raid muss ausgewählt sein.';
$lang['fv_required_user'] = 'Das Benutzernamen-Feld ist notwendig.';
$lang['fv_required_value'] = 'Das Werte-Feld ist notwendig.';
$lang['fv_required_vote'] = 'Das Abstimmungs-Feld ist notwendig.';

//Jquery-Validation
$lang['jqfv_required_user'] = '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>Du musst einen Benutzernamen eingeben.';
$lang['jqfv_required_password'] = '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>Du musst ein Passwort eingeben.';
$lang['jqfv_required_email'] = '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>Du musst eine gültige Email-Adresse eingeben.';
$lang['jqfv_required_email2'] = '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>Wiederhole zur Sicherheit deine Email-Adresse.';
$lang['jqfv_recaptcha'] = '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>Du musst die beiden obigen Wörter eingeben.';

// Miscellaneous
$lang['anonymous'] = '<i>Gast</i>';
$lang['added'] = 'Zugefügt';
//$lang['additem_raidid_note'] = "Nur Raids neuer als zwei Wochen werden angezeigt / %1\$sZeige alle</a>";
$lang['additem_raidid_showall_note'] = 'Alle Raids anzeigen';
//$lang['addraid_datetime_note'] = 'Wenn Du ein Log analysierst, wird Datum und Zeit automatisch gefunden.';
//$lang['addraid_value_note'] = 'für einen einmaligen Bonus; wenn es leer bleibt, wird der voreingestellte Wert für das Ereignis genommen';
//$lang['add_items_from_raid'] = 'Füge Items von diesem Raid zu';
$lang['deleted'] = 'Gelöscht';
//$lang['done'] = 'Fertig';
$lang['edit'] = 'Bearbeiten';
//$lang['enter_new'] = 'Neu eingeben';
$lang['error'] = 'Fehler';
//$lang['head_admin'] = 'Head Admin';
//$lang['hold_ctrl_note'] = 'STRG gedrückt halten, um mehrere Einträge auszuwählen';
$lang['itempool'] = 'Item-Pool';
//$lang['itempools'] = 'Item-Pools';
$lang['list'] = 'Anzeigen';
$lang['chars']  = 'Charaktere';
$lang['charsmanage']	= 'Charaktere verwalten';
$lang['charconnect'] = 'Charaktere zuweisen';
$lang['charsdelete']= 'eigene Charaktere löschen';
$lang['cm_todo_txt'] = "Es warten %1\$s Aufgabe(n) auf Erledigung";
$lang['cm_todo_head'] = 'Administrationsaufgaben';
$lang['list_groupadj'] = 'Liste der Gruppenkorrekturen';
$lang['list_events'] = 'Liste der Ereignisse';
$lang['list_indivadj'] = 'Liste der individuellen Korrekturen';
$lang['list_items'] = 'Liste der Items';
$lang['list_members'] = 'Liste der Charaktere';
$lang['list_news'] = 'Liste der News';
$lang['list_raids'] = 'Liste der Raids';
//$lang['may_be_negative_note'] = 'kann negativ sein';
$lang['not_available'] = 'Nicht verfügbar';
$lang['no_news'] = 'Keine Newseinträge gefunden.';
$lang['of_raids'] = "%1\$d%% von Raids";
$lang['or'] = 'Oder';
//$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Vorschau';
$lang['registered_at'] = 'Registriert am';
$lang['required_field_note'] = 'Mit * gekennzeichnete Felder sind Pflichtfelder.';
$lang['valid_email_note'] = 'Bitte beachte, dass du eine gültige E-Mail-Adresse angeben musst, bevor dein Benutzerkonto aktiviert wird. Du erhältst eine E-Mail an die angegebene Adresse, in der ein Aktivierungs-Schlüssel enthalten ist.';
$lang['select_1ofx_members'] = "Wähle 1 von %1\$d Charakteren...";
$lang['select_existing'] = 'Wähle Vorhandene';
$lang['success'] = 'Erfolgreich';
$lang['transfer_member_history_description'] = 'Verschiebe die gesamte Historie (Raids, Items, Korrekturen) eines Charakters zu einem anderen Charakter.';
$lang['unknown'] = 'Unbekannt';
$lang['updated'] = 'Aktualisiert';
$lang['manage_news'] = 'News verwalten';
$lang['edit_news'] = 'News bearbeiten';
$lang['manage_users'] = 'Benutzer verwalten';
$lang['install_folder_warn'] = 'Bitte lösche das Installations-Verzeichnis <b>install/</b> von deinem Webspace.';
$lang['sort_desc'] = 'Spalte absteigend sortieren';
$lang['sort_asc'] = 'Spalte aufsteigend sortieren';
$lang['search'] = 'Suchen';


// Settings
//$lang['account_settings'] = 'Account Einstellungen';
$lang['adjustments_per_page'] = 'Korrekturen pro Seite';
//$lang['basic'] = 'Grundeinstellungen';
$lang['events_per_page'] = 'Ereignisse pro Seite';
$lang['items_per_page'] = 'Items pro Seite';
$lang['news_per_page'] = 'Newseinträge pro Seite';
$lang['raids_per_page'] = 'Raids pro Seite';
$lang['associated_members'] = 'Verknüpfte Charaktere';
//$lang['guild_members'] = 'Gildenmitglied(er)';
$lang['default_locale'] = 'Standard Gebietsschema';

// Error messages
$lang['error_account_inactive'] = 'Dein Account ist inaktiv.';
$lang['error_already_activated'] = 'Der Account ist bereits aktiviert.';
$lang['error_invalid_email'] = 'Es wurde keine gültige E-Mail-Adresse angegeben.';
$lang['error_invalid_event_provided'] = 'Es wurde keine gültige Ereignis-ID angegeben.';
$lang['error_invalid_item_provided'] = 'Es wurde keine gültige Item-ID angegeben.';
$lang['error_invalid_key'] = 'Du hast einen ungültigen Aktivierungs-Key angegeben.';
$lang['error_invalid_name_provided'] = 'Es wurde kein gültiger Charakterename angegeben.';
$lang['error_invalid_news_provided'] = 'Es wurde keine gültige News-ID angegeben.';
$lang['error_invalid_raid_provided'] = 'Es wurde keine gültige Raid-ID angegeben.';
$lang['error_user_not_found'] = 'Es wurde kein gültiger Benutzername angegeben.';
$lang['error_email_send'] = 'Senden der Email fehlgeschlagen.';
$lang['incorrect_password'] = 'Falsches Passwort';
$lang['invalid_login'] = 'Du hast einen ungültigen Benutzernamen oder Passwort angegeben.';
//$lang['not_admin'] = 'Du bist kein Administrator.';
$lang['error_invalid_user_or_mail'] = 'Es existiert kein Benutzer mit dieser Kombination aus Benutzernamen und E-Mail-Adresse.';

// Registration
$lang['account_activated_admin']   = 'Der Account wurde aktiviert. Dem Benutzer wurde eine Informationsmail über die Änderungen geschickt.';
$lang['account_activated_user']    = "Dein Account wurde aktiviert und Du kannst Dich nun %1\$seinloggen%2\$s.";
$lang['password_sent'] = 'Dein neues Accountpasswort wurde Dir zugemailt.';
$lang['register_activation_self']  = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muss er von Dir aktiviert werden.<br /><br />Eine E-Mail wurde an %1\$s gesendet mit den Informationen zur Aktivierung.";
$lang['register_activation_admin'] = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muss er von einem Administrator aktiviert werden.<br /><br />Eine E-Mail mit mehr Informationen wurde an %1\$s gesendet.";
$lang['register_activation_none']  = "Dein Account wurde erstellt und Du kannst Dich nun %1\$seinloggen%2\$s.<br /><br />Eine E-Mail mit mehr Informationen wurde an %3\$s gesendet.";
$lang['register_help_username']  = "Der Benutzername muss zwischen 1 und 30 Zeichen lang sein.";
$lang['register_help_email']  = "Gib hier Deine E-Mail-Adresse ein.";
$lang['register_help_email_confirm']  = "Wiederhole zur Sicherheit deine Email-Adresse.";
$lang['register_help_password']  = "Ein sicheres Passwort sollte aus mindestens 8 Zeichen bestehen";
$lang['register_help_password_repeat']  = "Wiederhole zur Sicherheit dein Passwort";
$lang['register_help_name']  = "Trage hier deinen Vornamen ein.";
$lang['register_help_gender']  = "Wähle hier dein Geschlecht aus.";
$lang['register_help_language']  = "Wähle die Sprache aus, in der das EQDKPlus für Dich angezeigt werden soll.";
$lang['register_help_style']  = "Wähle aus, in welchen Stil das EQDKPlus für dich angezeigt werden soll.";
$lang['register_help_country']  = "Wähle das Land aus, aus dem Du kommst.";
$lang['register_help_disabled_username']  = "Der Administrator hat das Ändern des Benutzernames nicht freigegeben. Wende dich an ihn, damit er deinen Benutzernamen für dich ändert.";
$lang['register_help_irc']  = "Gib hier deinen Channel und deinen IRC-Server an, z.B. #allvatar@quakenet";
//plus
$lang['news_submitter'] = 'geschrieben von %1$s um %2$s Uhr';
//$lang['droprate_loottable'] = "Loot Tabelle für";
//$lang['droprate_name'] = "Itemname";
//$lang['droprate_count'] = "Anzahl";
//$lang['droprate_drop'] = "Drop %";

//$lang['Itemsearch_link'] = "Item-Suche";
//$lang['Itemsearch_search'] = "Item Suche: ";
$lang['Itemsearch_searchby'] = "Suche nach: ";
$lang['Itemsearch_item'] = "Item";
$lang['Itemsearch_buyer'] = "Käufer";
$lang['Itemsearch_raid'] = "Raid";
//$lang['Itemsearch_unique'] = "Einzigartig: ";
//$lang['Itemsearch_no'] = "Nein";
//$lang['Itemsearch_yes'] = "Ja";

//$lang['bosscount_player'] = "Spieler: ";
//$lang['bosscount_raids'] = "Raids: ";
//$lang['bosscount_items'] = "Items: ";
//$lang['bosscount_dkptotal'] = "DKP insgesamt: ";

//MultiDKP
//$lang['Plus_menuentry'] 		= "EQdkp Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Konto zufügen";
$lang['Multi_pageheader'] 		= "MultiDKP - Konten anzeigen";
$lang['Multi_events'] 			= "Ereignisse:";
$lang['Multi_eventname'] 		= "Ereignisname";
$lang['Multi_discnottolong'] 	= "( = Spaltenname) - nicht zu lang wählen, da sonst die Tabellen zu breit werden. Wähle z.b. MC, BWL, AQ usw.!";
$lang['Multi_kontoname_short']	= "Kontoname";
$lang['Multi_discr'] 			= "Beschreibung";
$lang['Multi_events'] 			= "Dem Konto zugeordnete Ereignisse";

$lang['Multi_addkonto'] 		= "Konto zufügen";
$lang['Multi_updatekonto'] 		= "Konto aktualisieren";
$lang['Multi_deletekonto'] 		= "Konto löschen";
$lang['Multi_viewkonten']		= "Konten anzeigen";
$lang['Multi_chooseevents']		= "Ereignisse auswählen";
$lang['Multi_chooseitempools']	= "Item-Pools auswählen";
$lang['multi_footcount'] 		= "... %1\$d DKP Konten gefunden";
$lang['multi_error_invalid']    = "Kein Konto gefunden....";
$lang['Multi_required_event']   = "Es muss mindestens ein Ereignis ausgewählt sein!";
$lang['Multi_required_name']    = "Es muss ein Name eingegeben werden!";
$lang['Multi_required_disc']    = "Es muss eine Beschreibung eingegeben werden!";
$lang['Multi_admin_add_multi_success'] 		= "Das Konto %1\$s ( %2\$s ) mit den Ereignissen %3\$s wurde der Datenbank zugefügt.";
$lang['Multi_admin_update_multi_success'] = "Das Konto %1\$s ( %2\$s ) mit den Ereignissen %3\$s wurde in der Datenbank geändert.";
$lang['Multi_admin_delete_success']       = "Das Konto %1\$s wurde aus der Datenbank gelöscht.";

//$lang['Multi_total_cost']   							= 'Gesamt Konto';
$lang['Multi_Accs']    										= 'MultiDKP Konten';
$lang['event_icon_header']    								  = 'Ereignis Icon auswählen';

/*update -- OLD
$lang['upd_eqdkp_status']    										= 'EQdkp Update Status';
$lang['upd_system_status']    									= 'System Status';
$lang['upd_template_status']    								= 'Template Status';
$lang['upd_gamefile_status']                    = 'Spiel Status';
$lang['upd_update_need']    										= 'Update notwendig!';
$lang['upd_update_need_link']    								= 'Alle benötigten Komponenten installieren';
$lang['upd_no_update']    											= 'Kein Update notwendig. Das System ist auf dem aktuellsten Stand.';
$lang['upd_status']    													= 'Status';
$lang['upd_state_error']    										= 'Fehler';
$lang['upd_sql_string']    											= 'SQL Anweisung';
$lang['upd_sql_status_done']    								= 'ausgeführt';
$lang['upd_sql_error']    											= 'Fehler';
$lang['upd_sql_footer']    											= " SQL Anweisungen ausgeführt";
$lang['upd_sql_file_error']    									= " Es ist ein Fehler aufgetreten! Die erforderliche SQL Datei %1\$s konnte nicht gefunden werden!";
$lang['upd_eqdkp_system_title']    							= 'EQdkp System Komponenten Update';
$lang['upd_plus_version']    										= 'EQdkp Plus Version';
$lang['upd_plus_feature']    										= 'Feature';
$lang['upd_plus_detail']    										= 'Details';
$lang['upd_update']    													= 'Update';
$lang['upd_eqdkp_template_title']    						= 'EQdkp Templates Update';
$lang['upd_eqdkp_gamefile_title']               = 'EQDKP Spiel Update';
$lang['upd_gamefile_availversion']              = 'Verfügbare Version';
$lang['upd_gamefile_instversion']               = 'Installierte Version';
$lang['upd_template_name']    									= 'Template Name';
$lang['upd_template_state']    									= 'Template Status';
$lang['upd_template_filestate']    							= 'Template Ordner vorhanden';
$lang['upd_link_install']    										= 'Update';
$lang['updi_header']    							= 'Itemstats Daten aktualisieren';
$lang['updi_header2']    							= 'Itemstats Informationen';
$lang['updi_action']    							= 'Aktion';
$lang['updi_notfound']    							= 'Nicht gefunden';
$lang['updi_writeable_ok']    						= 'Datei beschreibbar ';
$lang['updi_writeable_no']    						= 'Datei NICHT beschreibbar ';
$lang['updi_help']    								= 'Beschreibung';
$lang['updi_footcount']    							= ' Item(s) aktualisiert.';
$lang['updi_curl_bad']    							= 'Die benötigte PHP Funktion cURL wurde nicht gefunden. Itemstats wird unter Umständen nicht richtig funktionieren. Wendet Euch bitte an Euren Administrator.';
$lang['updi_curl_ok']    							= ' cURL gefunden.';
$lang['updi_fopen_bad']    							= 'Die benötigte PHP Funktion fopen wurde nicht gefunden. Itemstats wird unter Umständen nicht richtig funktionieren. Wendet Euch bitte an Euren Administrator.';
$lang['updi_fopen_ok']    							= 'fopen gefunden.';
$lang['updi_nothing_found']						    = ' Keine Items gefunden';
$lang['updi_itemscount']  						    = ' Einträge im Itemcache:';
$lang['updi_baditemscount']						    = ' Fehlerhafte Einträge:';
$lang['updi_items']									= ' Items in der Datenbank:';
$lang['updi_items_duplicate']					    = ' (Mit doppelten Items)';
$lang['updi_show_all']    							= 'Liste alle Items mit Itemstats auf';
$lang['updi_refresh_all']    						= 'Alle Items löschen und aktualisieren.';
$lang['updi_refresh_bad']    						= 'Nur defekte Items aktualisieren';
$lang['updi_refresh_raidbank']    					= 'Raidbanker Items aktualisieren';
$lang['updi_refresh_tradeskill']   					= 'Tradeskill Items aktualisieren';
$lang['updi_help_show_all']    						= 'Dabei werden alle Items mit ihren Stats angezeigt. Fehlende Stats werden dabei nachgeladen. (empfohlen)';
$lang['updi_help_refresh_all']  					= 'Löscht den kompletten Itemcache und versucht alle im EQdkp eingetragenen Items zu aktualisieren. ACHTUNG: Wenn ihr Euer Itemcache mit einem Forum teilt, können die im Forum eingetragenen Items nicht aktualisiert werden. Je nach Geschwindigkeit Eures Webservers und der Verfügbarkeit von www.buffed.de bzw. Allakhazam.com kann diese Aktion mehrere Minuten dauern. Unter Umständen verhindern die Einstellungen Eures Webservers eine erfolgreiche Ausführung. Wendet Euch dann bitte an Euren Administrator.';
$lang['updi_help_refresh_bad']    					= 'Löscht alle fehlerhaften Items aus dem Cache und aktualisiert diese.';
$lang['updi_help_refresh_raidbank']    				= 'Wenn der Raidbanker installiert ist, werden die Itemstats, von denen im Banker eingetragenen Items, aktualisiert.';
$lang['updi_help_refresh_Tradeskill']    			= 'Wenn Tradeskills installiert ist, werden die Itemstats, von denen in Tradeskill eingetragenen Items, aktualisiert.';

$lang['updi_active'] 					   			= 'Aktiviert';
$lang['updi_inactive']    							= 'ausgeschaltet';

$lang['fontcolor']    			  		= 'Schriftfarbe';*/

# Reset DB Feature
$lang['reset_header']    			= 'EQdkp Daten löschen';
$lang['reset_infotext']  			= 'ACHTUNG, das Löschen der Daten kann nicht rückgängig gemacht werden! Alle eingetragenen Daten werden unwiderbringlich gelöscht!<br /> Lege unbedingt ein Backup des EQdkps an.';
$lang['reset_type']    				= 'Datentyp';
$lang['reset_disc']    				= 'Beschreibung';
$lang['reset_sec']    				= 'Bestätigung';
$lang['reset_action']    			= 'Aktion';


$lang['reset_raids_disc']			 = 'Löscht alle Raids aus der Datenbank.';
$lang['reset_events_disc']		 = 'Löscht alle Ereignisse aus der Datenbank.';
$lang['reset_items_disc']			 = 'Löscht alle Items aus der Datenbank.';
$lang['reset_itempools_disc']	 = 'Löscht alle Item-Pools aus der Datenbank.';
$lang['reset_adjustments_disc']= 'Löscht alle Korrekturen aus der Datenbank.';
$lang['reset_chars_disc']			 = 'Löscht alle Charaktere aus der Datenbank.';
$lang['reset_news_disc']			 = 'Löscht alle News aus der Datenbank.';
$lang['reset_plugins_disc']		 = 'Deinstalliert alle Plugins und löscht die damit verbundenen Daten aus der Datenbank.';
$lang['reset_user_disc']			 = 'Löscht alle Benutzer bis auf deinen aus der Datenbank.';
$lang['reset_multi_disc']			 = 'Löscht alle MultiDKP-Konten aus der Datenbank.';

$lang['reset_event_warning']		= 'Ereignisse können nicht gelöscht werden, ohne dass auch folgende Elemente gelöscht werden:<ul><li>Raids</li><li>Items</li><li>MultiDKP-Konten</li></ul> Klicke auf "OK", um auch alle genannten Elemente auszuwählen.';
$lang['reset_raids_warning']		= 'Raids können nicht gelöscht werden, ohne dass auch folgende Elemente gelöscht werden:<ul><li>Items</li></ul> Klicke auf "OK", um auch alle genannten Elemente auszuwählen.';
$lang['reset_chars_warning']		= 'Charaktere können nicht gelöscht werden, ohne dass auch folgende Elemente gelöscht werden:<ul><li>Raids</li><li>Items</li><li>Korrekturen</li></ul> Klicke auf "OK", um auch alle genannten Elemente auszuwählen.';
$lang['reset_itempools_warning']		= 'Itempools können nicht gelöscht werden, ohne dass auch folgende Elemente gelöscht werden:<ul><li>MultiDKP-Konten</li></ul> Klicke auf "OK", um auch alle genannten Elemente auszuwählen.';
$lang['reset_confirm']					= '<b>Bist du dir wirklich sicher, dass du die Folgenden Elemente zurücksetzen willst? Dies kann nicht rückgängig gemacht werden!!</b>';

$lang['reset_dependency_info']	= 'Vorgang konnte nicht ausgeführt werden: es fehlten zu einem Element abhängige Elemente';
$lang['reset_success']	= 'Die ausgewählten Elemente wurden erfolgreich zurückgesetzt!';

//$lang['updi_update_ready']			= 'Die Items wurden erfolgreich aktualisiert. Das Fenster kann <a href="#" onclick="javascript:parent.closeWindow()" >geschlossen</a> werden.';
//$lang['updi_update_alternative']	= 'Alle Items löschen und aktualisieren - Alternatives Update, welches ein Timeout bzw. einen "Memory Size" Fehler verhindern soll.';
//$lang['zero_sum']					= ' bei Nullsummen DKP';

$lang['Jump_to'] 				= 'Zum Video auf ';
//$lang['News_vid_help'] 			= 'Zum Einbinden von Videos, den Link in die News schreiben ohne [Tags] zu benutzen. Unterstützte Videoseiten: google video, youtube, myvideo, clipfish, sevenload, metacafe und streetfire. ';

$lang['SubmitNews'] 		   = 'News berichten';
$lang['SubmitNews_help'] 	   = 'Ihr habt eine gute News, die auf Allvatar.com veröffentlicht werden sollte? Dann immer her damit!';

$lang['MM_User_Confirm']	   = 'Achtung, soll wirklich DEIN Admin Account ausgewählt werden?? Achtung, wenn Du dir selber die Rechte für das Ändern von User-Berechtigungen nimmst, kann das später nur in der Datenbank wieder rückgängig gemacht werden!! Im Zweifelsfall Bitte abbrechen!';

$lang['beta_warning']	   	   = 'ACHTUNG du nutzt eine Beta Version von Eqdkp-Plus! Diese Version ist NICHT für den produktiven Einsatz auf eurem Live-System gedacht. Diese Version wird mit dem Release einer Stable Version nur noch kurze Zeit laufen. Check <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> ob eine Stable Version verfügbar ist!';

$lang['comments_read']       			= 'Kommentare lesen';
$lang['comments_write']       		= 'Kommenare schreiben';
$lang['add_news'] = 'News zufügen';
$lang['news_comment']        			= 'Kommentar';
$lang['news_comments']       			= 'Kommentare';
$lang['comments_no_comments']	   	   	= 'Keine Kommentare vorhanden';
$lang['comments_comments_raid']	   		= 'Kommentare';
$lang['comments_write_comment']	   		= 'Kommentar schreiben';
$lang['comments_send_comment']	   		= 'Kommentar speichern';
$lang['comments_save_wait']	   	   		= 'Bitte warten, Kommentar wird gespeichert.';

$lang['news_nocomments'] 	 		    = 'Kommentare deaktivieren';
$lang['news_readmore_button']  			  	= 'News erweitern';
$lang['news_readmore_button_help']  			  	= 'Um den erweiterten Newstext einzugeben, klicke hier:';
$lang['news_message'] 				  	= 'Newstext welcher auf der Startseite erscheint.';
$lang['news_permissions']			  	= 'News sichtbar für';


$lang['news_permissions_guest']			= 'Alle registrierte Benutzer';
$lang['news_permissions_member']		= 'Nur für Administratoren';
$lang['news_permissions_all']			= 'Für alle sichtbar';
$lang['news_readmore'] 				  	= 'Weiterlesen...';

$lang['sig_conf']						= 'Klickt auf das Bild für den BB Code.';
//$lang['sig_show']						= 'Zeige WoW-Signatur für eurer Forum';

//Shirtshop
$lang['service']					    = 'Service';
$lang['shirt_ad1']					    = 'Zum Gildenshop. <br><br> Hol Dir jetzt dein individuelles Shirt, <br>passend zu deinem Char.';
$lang['shirt_ad2']					    = 'Wähle einen Char';
$lang['shirt_ad3']					    = 'Willkommen in eurem Gildenshop. ';
$lang['shirt_ad4']					    = 'Wähle eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bedürfnissen anpassen und jeden Schriftzug verändern.<br>
										   Unter Motive findest Du alle zur Verfügung stehenden Motive!';
$lang['error_iframe']					= 'Dein Browser kann leider keine iFrames anzeigen.';
$lang['new_window']						= 'SHOP jetzt in einem neuen Fenster öffnen';
$lang['your_name']						= 'DEIN NAME';
$lang['your_guild']						= 'DEINE GILDE';
$lang['your_server']					= 'DEIN SERVER';
//$lang['last_raids']					    = 'Letzte Raids';

//$lang['voice_error']				    = 'Keine Verbindung zum Voice Server möglich.';

$lang['login_bridge_notice']		    = 'Da eine CMS-Bridge aktiv ist, musst Du dich mit den Login-Daten eures Forums/CMS anmelden.';

$lang['ads_remove']		    			= 'Support EQdkp-Plus';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'Das Eqdkp-Plus ist ein Hobby-Projekt welches hauptsächlich von 2 Privatpersonen entwickelt und vorangetrieben wird.
										  Am Anfang funktionierte das auch gut, doch nach knapp 3 Jahren wachsen uns die Kosten für das Projekt über
										  den Kopf. Alleine der Entwicklungs- und Update Server kostet uns 600 Euro im Jahr. Dazu kommen noch mal 1000 Euro Kosten für
										  einen Anwalt, da es vor einiger Zeit rechtliche Probleme gab.
										  In Zukunft soll es weitere Server-Basierende Features geben, die evtl. noch einen weiteren Server nötig machen.
										  Dazu kommen noch weitere Kosten wie die Lizenz für das neue Forum und der Designer unserer neuen Homepage.
										  Diese Kosten zusammen mit der Arbeitszeit können wir einfach nicht mehr aus eigener Tasche aufbringen.

										  Um das Projekt aber nicht sterben zu lassen, gibt es nun vereinzelt Werbebanner im Eqdkp-Plus. Diese Werbebanner unterliegen
										  einigen Einschränkungen. So wird es keine pornografische Werbung geben, ebenso wie ihr keine Gold/Item Verkäufer Werbung sehen werdet.
										  <br><br>
										  Ihr habt allerdings die Möglichkeit uns aktiv zu unterstürzen und die Werbung abzuschalten. Dazu habt ihr mehrere Möglichkeiten:<br><br>
										  <ol>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet einen Beitrag den ihr selber bestimmen könnt.
										  	  Denkt darüber nach, was euch das Eqdkp-Plus wert ist. Nach einer Spende (egal ob Amazon oder Paypal) bekommt ihr eine Email mit dem Freischaltcode
										  	  <br>Die Freischaltung gilt dann für die jeweilige Version bzw. Major-Version (z.B. 0.6 oder 0.7).<br><br></li>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet mehr als 50 Euro.
										  	   Ihr werdet damit Premium User und bekommt einen Livetime-Premium-Account mit dem ihr zu Updates auf neue
										  	   Major Versionen berechtigt seid.</li><br>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet mehr als 100 Euro.
										  	   Ihr werdet damit Gold-User und bekommt einen Livetime-Premium-Account mit dem ihr zu Updates auf neue
										  	   Major Versionen bereichtigt seid und zusätzlich persönlichen Support von den Entwicklern.<br><br></li>
										  </ul>
										  <li>Alle Entwickler und Übersetzer die einen Beitrag zum Eqdkp-Plus geleistet haben, bekommen ebenfalls einen Freischaltcode.<br><br></li>
										  <li>Besonders engagierte Betatester bekommen ebenfalls einen Freischaltcode.<br><br></li>
										  </ol>
										  Das Geld das wir mit der Werbung bzw. den Spenden einnehmen verwenden wir ausschließlich, um die Kosten des Projektes zu decken.<br>
										  Das Eqdkp-Plus ist und bleibt ein non-profit Projekt! Ihr habt kein Paypal oder Amazon Account? Dann schreibt mir einfach eine <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['portalmanager'] = 'Portalmodule verwalten';

//$lang['air_img_resize_warning'] = 'Das Bild wurde verkleinert.';

$lang['guild_shop'] = 'Shop';

/* LibLoader Language String
$lang['libloader_notfound']     = 'Die Library Loader Klasse ist nicht verfügbar.
                                  Bitte überprüfe, ob der Ordner "eqdkp/libraries/" richtig hochgeladen wurde!<br/>
                                  Falls nicht, bitte neu herunterladen. Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']       = "Die Library '%1\$s' ist nicht mehr aktuell. Es wird mindestens Version %2\$s oder höher benötigt.<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Bitte laden und die aktuelle Version im 'eqdkp/libraries/' Ordner überschreiben!";
$lang['libloader_tooold_plug']  = "Das Library Modul '%1\$s' ist nicht mehr aktuell. Es wird mindestens Version %2\$s oder höher benötigt.
                                  Diese ist in den Libraries ab Version %4\$s (oder höher) enthalten. Deine aktuelle Libraries Version ist %5\$s<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Bitte laden und die aktuelle Version im 'eqdkp/libraries/' Ordner überschreiben!";*/

$lang['more_plugins']   = "Weitere Plugins findet ihr auf der Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "Weitere Module findet ihr auf der  Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "Weitere Templates findet ihr auf der Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_ms_noneselected'] = 'Bitte wählen';
$lang['cl_ms_checkall'] ='Wähle alle';
$lang['cl_ms_uncheckall']='Auswahl aufheben';
$lang['cl_ms_selection']='# von # gewählt';

/* Update Available
$lang['upd_available_head']    = 'System Aktualisierungen verfügbar';
$lang['upd_available_txt']     = 'Das System ist nicht mehr auf dem neusten Stand. Es wurden Updates gefunden.';
$lang['upd_available_link']    = 'Hier klicken, um die Updates anzuzeigen.';*/

$lang['menu_roster'] = 'Roster';

//Userinfos
$lang['adduser_first_name'] = 'Vorname';
$lang['adduser_last_name'] = 'Nachname';
$lang['adduser_addinfos'] = 'Profilinformationen';
$lang['adduser_country'] = 'Land';
$lang['adduser_town'] = 'Ort';
$lang['adduser_state'] = 'Bundesland';
$lang['adduser_ZIP_code'] = 'PLZ';
$lang['adduser_phone'] = 'Festnetz-Nr.';
$lang['adduser_cellphone'] = 'Handynummer';
$lang['adduser_foneinfo'] = 'Du kannst unter &raquo;Privatssphäre&laquo; einstellen, wer Zugriff auf deine Telefonnummer haben darf.';
$lang['adduser_cellinfo'] = 'Du kannst unter &raquo;Privatsspähre&laquo; einstellen, wer Zugriff auf deine Handynunmmer haben darf. Über die Handynummer kannst Du Dir z.B. anonym eine SMS senden lassen, wenn neue Termine anstehen, oder ein Raid abgesagt wurde.';
$lang['adduser_address'] = 'Anschrift (Straße und Hausnummer)';
$lang['adduser_allvatar_nick'] = 'Allvatar Nick';
$lang['adduser_icq'] = 'ICQ Nummer';
$lang['adduser_skype'] = 'Skype Nummer';
$lang['adduser_msn'] = 'MSN Account';
$lang['adduser_twitter'] = 'Twitter Account';
$lang['adduser_facebook'] = 'Facebook-ID';
$lang['adduser_irq'] = 'IRC Server&Channel';
$lang['adduser_gender'] = 'Geschlecht';
$lang['adduser_birthday'] = 'Geburtstag';
$lang['adduser_gender_m'] = 'Männlich';
$lang['adduser_gender_f'] = 'Weiblich';
$lang['fv_required'] = 'Das Feld ist notwendig.';
$lang['adduser_send_mail'] = 'E-Mail senden';
$lang['adduser_send_mail2'] = 'E-Mail  an "%s" senden';
$lang['adduser_send_mail_subject'] = 'Betreff';
$lang['adduser_send_mail_body'] = 'Inhalt';
$lang['adduser_send_mail_suc'] = 'Die E-Mail wurde erfolgreich versendet.';
$lang['adduser_send_mail_error_fields'] = 'Fehler: Es wurden nicht alle Felder ausgefüllt.';
$lang['adduser_send_new_pw'] = 'Neues Passwort senden';
$lang['adduser_send_new_pw_note'] = 'Sende dem Benutzer ein neues, zufälliges Passwort';
$lang['adduser_misc'] = 'Sonstiges';
$lang['adduser_hide_shop'] = 'Shop-Link verbergen';
$lang['adduser_hide_mini_games'] = 'Mini-Games verbergen';
$lang['adduser_date_time'] = 'Uhrzeit-Format';
$lang['adduser_date_short'] = 'Datums-Format (kurz)';
$lang['adduser_date_long'] = 'Daturms-Format (lang)';
$lang['adduser_date_note'] = 'Die Syntax entspricht der der <a href="http://www.php.net/date" target="_blank">date()</a>-Funktion von PHP.';
$lang['adduser_date_note_nolink'] = 'Die Syntax entspricht der der date()-Funktion von PHP.';
$lang['adduser_passwordreset_note'] = 'Achtung: Bitte gebe das Passwort erneut ein, damit der Benutzer kein zufällig Generiertes bekommt.';

//Requirements
$lang['lib_cache_notwriteable'] = 'In den Ordner "data" kann nicht geschrieben werden. Bitte gib ihm CHMOD 777!';
$lang['pcache_safemode_error']  = 'PHP Safe Mode ist eingeschaltet. EQDKP-PLUS wird im Safe Mode nicht richtig funktionieren, da die Schreiboptionen nicht in vollem Umfang zur Verfügung stehen.';
$lang['spl_autoload_register_notavailable']	= 'Die PHP-Funktion "spl_autoload_register" konnte nicht gefunden werden. Dies liegt vermutlich daran, dass du bei einem Freehoster bist, der diese Funktion nicht zur Verfügung stellt.';
$lang['php_too_old']	= 'Deine PHP-Version %1$s ist zu alt. Benötigt wird mindestens die PHP-Version %2$s.';
$lang['requirements_notfilled']  = 'Dein Server erfüllt nicht die Voraussetzungen, um EQdkp Plus betreiben zu können.';

$lang['register_licence'] = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.   

Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.   

Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.   

Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer';
$lang['deny'] = 'Ablehnen';
$lang['accept'] = 'Akzeptieren';
$lang['guildrules'] = 'Gildenregeln';

// Ajax Image Uploader
$lang['aiupload_wrong_format']  = "Die Abmessungen des Bildes überschreiten die maximal <br/>zulässigen Werte (%1\$spx x %2\$spx). Bitte das Bild verkleinern.";
$lang['aiupload_wrong_type']    = 'Unzulässiger Dateityp! Es sind nur Bilddateien (*.jpg, *.gif, *.png) zugelassen.';
$lang['aiupload_upload_again']  = 'Re-Upload';

//Sticky news
$lang['sticky_news_prefix'] = '<img src="'.$eqdkp_root_path.'images/glyphs/sticky.png"> Sticky:';
$lang['news_sticky'] = 'News immer oben anzeigen?';

$lang['menu_main'] = 'Hauptmenü';
$lang['menu_eqdkp'] = 'Menü';
$lang['menu_user'] = 'User-Menü';
$lang['menu_links'] = 'Links-Menü';
$lang['menu_links_short'] = 'Links';
$lang['portal'] = 'Portal';
$lang['forum'] = 'Forum';
$lang['dkp_system'] = '%s-System';

//---- About ----
$lang['pk_plus_about']			= 'Über EQDKP PLUS';
$lang['pk_version']					= 'Version';
$lang['pk_prodcutname']				= 'Produkt';
$lang['pk_modification']				= 'Mod';
$lang['pk_tname']						= 'Template';
$lang['pk_developer']					= 'Entwickler';
$lang['pk_plugin']						= 'Plugin';
$lang['pk_weblink']					= 'Link';
//$lang['pk_phpstring']					= 'PHP String';
//$lang['pk_phpvalue']					= 'Wert';
$lang['pk_donation']					= 'Spende';
$lang['pk_job']						= 'Job';
$lang['pk_sitename']					= 'Seite';
$lang['pk_dona_name']					= 'Name';
//$lang['pk_betateam1']					= 'Betatest Team (Deutschland)';
//$lang['pk_betateam2']					= 'in chronologischer Reihenfolge';
$lang['pk_created_by']					= 'geschrieben von';
$lang['web_url']						= 'Web';
//$lang['personal_url']					= 'Privat';
$lang['pk_credits']					= 'Credits';
//$lang['pk_sponsors']					= 'Spender';
//$lang['pk_plugins']					= 'Plugins';
$lang['pk_modifications']				= 'Mods';
$lang['pk_themes']						= 'Styles';
//$lang['pk_additions']					= 'Code Additions';
$lang['pk_tab_stuff']					= 'EQDKP Team';
$lang['pk_tab_help']					= 'Hilfe';
//$lang['pk_tab_tech']					= 'Tech';
$lang['pk_disclaimer'] = 'Impressum';

// Image & BBCode Handling
$lang['images_not_available']	= 'Dieses Bild ist nicht mehr verfügbar';
$lang['images_userposted']		= 'Hochgeladenes Bild';

//pdh listmember
$lang['manage_members'] = "Charaktere verwalten";
$lang['show_hidden_ranks'] = "Zeige versteckte Charaktere";
$lang['show_inactive'] = "Zeige inaktive Charaktere";
$lang['show_twinks']				= 'Zeige Twinks';

// Image & BBCode Handling
$lang['images_not_available']	= 'Das eingebettete Bild ist zur Zeit leider nicht verfügbar.';
$lang['images_not_available_admin']	= '<b>Das eingebettete Bild konnte nicht überprüft werden</b><br/>Das kann folgende Gründe haben, bitte prüfe ob:<br/>- Dynamische Bilder sind aus Sicherheitsgründen deaktiviert<br/>- externe Verbindungen gesperrt: Versuche es mit Pfaden anstatt von URLs<br/>- Bild nicht länger verfügbar<br/>- PHP Safemode on: Safemode muss deaktiviert sein';
$lang['images_userposted']		= 'Hochgeladenes Bild';

$lang['loot_distribution'] = 'Loot-Verteilung';

// Libraries
$lang = array_merge($lang, array(
  
  // JS Short Language
  //'cl_shortlangtag'           => 'de',
    
  /* Update Check - now in the MM
  'cl_update_box'             => 'Neue Version verfügbar',
  'cl_changelog_url'          => 'Changelog',
  'cl_timeformat'             => 'd.m.Y',
  'cl_noserver'               => 'Beim Versuch den Updateserver zu kontaktieren trat ein Fehler auf. Entweder dein Host erlaubt keine ausgehenden
                                  Verbindungen, oder es bestehen Netzwerkprobleme. Bitte besuche das EQDKP Plugin Forum um sicherzustellen, dass du die neuste Version am laufen hast.',
  'cl_update_available'       => "Bitte aktualisiere das installierte <i>%1\$s</i> Plugin.
                                  Deine installierte Version ist <b>%2\$s</b> und die aktuellste Version ist <b>%3\$s (Veröffentlicht am: %4\$s)</b>.<br/><br/>
                                  [Releaseart: %5\$s]%6\$s%7\$s",
  'cl_update_url'             => 'Zur Downloadseite',
    
  // Plugin Updater
  'cl_update_box'             => 'Datenbankupdate notwendig',
  'cl_upd_wversion'           => "Datenbank Version: %1\$s, Plugin Version %2\$s",
  'cl_upd_woversion'          => 'Es wurde eine vorhandene Installation gefunden. Leider konnten die Versionsdaten nicht festgestellt werden. 
                                  Bitte wähle in der untenstehenden Liste die zuletzt installierte Version aus, damit alle nötigen Datenbankänderungen 
                                  durchgeführt werden können.',
  'cl_upd_bttn'               => 'Datenbank aktualisieren',
  'cl_upd_no_file'            => 'Es wurde keine Updatedatei gefunden',
  'cl_upd_glob_error'         => 'Beim update ist ein Fehler aufgetreten.',
  'cl_upd_ok'                 => 'Die Datenbank wurde erfolgreich aktualsiert',
  'cl_upd_step'               => 'Schritt',
  'cl_upd_step_ok'            => 'Erfolgreich',
  'cl_upd_step_false'         => 'Fehlgeschlagen',
  'cl_upd_reload_txt'         => 'Einstellungen werden neu geladen, bitte warten...',
  'cl_upd_pls_choose'         => 'Bitte auswählen...',
  'cl_upd_prev_version'       => 'Vorherige Version',*/
  
  // HTML Class
  'cl_on'                     => 'Ein',
  'cl_off'                    => 'Aus',
  'cl_all'										=> 'Alle',
  
    // ReCaptcha Library
	'lib_captcha_head'					=> 'Bestätigungscode',
	'lib_captcha_insertword'		=> 'Gib die beiden Wörter ein, getrennt durch ein Leerzeichen.',
	'lib_captcha_insertnumbers' => 'Gib die gehörten Nummern ein',
	'lib_captcha_send'					=> 'Bestätigungscode absenden',
	'lib_captcha_reload'				=> 'Neuen Bestätigungscode erzeugen',
	'lib_captcha_wrong'				=> 'Der eingegebene Bestätigungscode ist fehlerhaft',
	
	'lib_starrating_cancel'			=> 'Bewertung abbrechen',

	// RSS Feeder
	'lib_rss_readmore'          => 'weiterlesen',
	'lib_rss_loading'           => 'Feed lädt ...',
	'lib_loading'           		=> 'Laden...',
	'lib_rss_error'             => 'Fehler beim Seitenaufruf',
	'user_timezones'						=> 'Zeitzone',
));

$lang['cancel'] = 'Abbrechen';
$lang['email_encrypted'] = '(encrypted)';
$lang['email_subject_activation_none'] = 'Account aktiviert';
$lang['email_subject_new_pw'] = 'Neues Passwort Aktivierung benötigt';
$lang['email_subject_activation_self'] = 'Account Aktivierung benötigt';
$lang['email_subject_activation_admin'] = 'Account Aktivierung offen';
$lang['email_subject_activation_admin_act'] = 'Account Aktivierungsanfrage';
$lang['email_subject_send_error'] = 'Beim senden der E-Mail ist ein Fehler aufgetreten.';


//Layout manager: general
$lang['lm_title'] = "Layout Verwaltung";
$lang['lm_select_layout'] = "Layout Auswahl:";
$lang['lm_column_preset'] = "Spalte";
$lang['lm_column_sortable'] = "Sortierbar";
$lang['lm_column_default_sort'] = "Standardsortierung";
$lang['lm_column_th_add'] = "HTML &lt;th&gt; Zusatz";
$lang['lm_column_td_add'] = "HTML &lt;td&gt; Zusatz";
$lang['lm_add_row'] = 'Spalte hinzufügen';
$lang['lm_delete_row'] = 'Spalte entfernen';
$lang['lm_table_settings'] = 'Tabelleneinstellungen';
$lang['lm_table_columns'] = 'Tabellenspalten';
$lang['lm_save_btn'] = "Speichern als";
$lang['lm_system_description'] = " mit folgender Beschreibung:";
$lang['lm_default_layouts'] = "System-Layouts";
$lang['lm_user_layouts'] = "Benutzerdefinierte Layouts";
$lang['lm_make_current'] = "Ausgewähltes Layout verwenden";
$lang['lm_new_layout'] = "Neues Layout erstellen";
$lang['lm_source_layout'] = "Vorlage auswählen";
$lang['lm_create_layout'] = "Layout hinzufügen";
$lang['lm_manage_layouts'] = "Layouts verwalten";
$lang['lm_manage_advanced'] = "Benutzerdefinierte Spalten";

$lang['lm_del_suc'] = "Das ausgewählte Layout wurde erfolgreich gelöscht.";
$lang['lm_del_error'] = "Das ausgewählte Layout konnte nicht gelöscht werden."; 
$lang['lm_save_suc'] = "Das Layout wurde erfolgreich gespeichert.";
$lang['lm_layout_exists'] = "Ein gleichnamiges Layout existiert bereits. Bitte wähle einen anderen Namen für das neue Layout.";
$lang['lm_show_numbers'] = "Nummern anzeigen";
$lang['lm_sort_direction'] = "Sortier-Richtung";
$lang['lm_sort_asc'] = "Aufsteigend";
$lang['lm_sort_desc'] = "Absteigend";
$lang['lm_leaderbord_settings'] = "Leaderbord-Einstellungen";
$lang['lm_lb_maxperclass'] = "Maximale Charaktere pro Klasse";
$lang['lm_lb_maxperrow'] = "Maximale Klassen pro Reihe";
$lang['lm_lb_class_sort'] = "Klassen-Sortierung:";
$lang['lm_info'] = "Hier kannst du das Layout deines EQdkps und die DKP-Berechnung an deine Wünschen anpassen. So besteht die Möglichkeit, auf vielen Seiten auswählen zu können, welche Spalten angezeugt werden können. Du kannst zwischen System-Layouts wählen (die nicht verändert werden können), oder selbst Layouts anlegen, die u.a. auf System-Layouts basieren können.";
$lang['lm_warning'] = "Achtung: die Einstellmöglichkeiten auf dieser Seite sind nur etwas für sehr erfahrene Benutzer, die genau wissen, was sie tun. Mangeldes Wissen und fehlerhafte Eingaben können dazu führen, dass das EQdkp nicht mehr läuft!";
$lang['lm_add_preset'] = "Benutzerdef. Spalte hinzufügen";
$lang['lm_user_presets'] = "Benutzerdefinierte Spalten";
$lang['lm_module'] = "Modul";
$lang['lm_tag'] = "Tag";
$lang['lm_aparam'] = "Aufrufparameter";
$lang['lm_dparam'] = "Beschreibungs-Parameter";
$lang['lm_up_xml'] = "XML";

//Layout manager: layouts
$lang['lm_layout_normal'] = 'Normales EQdkp-Plus Layout.';
$lang['lm_layout_epgp'] = 'EPGP EQdkp-Plus Layout.';
$lang['lm_layout_sk'] = 'Suizide Kings EQdkp-Plus Layout.';
$lang['lm_layout_zs'] = 'ZeroSum EQdkp-Plus Layout.';
$lang['lm_layout_edkp'] = 'Effective-DKP EQdkp-Plus Layout.';

//Layout manager: pages
$lang['lm_page_listraids'] = 'Raid-Liste';
$lang['lm_page_listevents'] = 'Ereignisliste';
$lang['lm_page_listitems'] = 'Gegenstandsliste';
$lang['lm_page_listmembers'] = 'Punktestand';
$lang['lm_page_viewmember'] = 'Charakteransicht';
$lang['lm_page_viewevent'] = 'Ereignisansicht';
$lang['lm_page_admin_manage_members'] = '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="Admin-Seite"> Charakterverwaltung';
$lang['lm_page_admin_manage_items'] = '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="Admin-Seite"> Gegenstandsverwaltung';
$lang['lm_page_admin_manage_events'] = '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="Admin-Seite"> Ereignisverwaltung';
$lang['lm_page_admin_manage_adjustments'] = '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="Admin-Seite"> Korrekturverwaltung';
$lang['lm_page_admin_manage_raids'] = '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="Admin-Seite"> Raid-Verwaltung';

//Layout manager: tables
$lang['lm_hptt_listraids_raidlist'] = 'Raid-Tabelle';
$lang['lm_hptt_viewmember_memberlist'] = 'Punktetabelle';
$lang['lm_hptt_viewmember_raidlist'] = 'Raid-Tabelle';
$lang['lm_hptt_viewmember_adjlist'] = 'Anpassungstabelle';
$lang['lm_hptt_viewmember_itemlist'] = 'Gegenstandstabelle';
$lang['lm_hptt_listitems_itemlist'] = 'Gegenstandstabelle';
$lang['lm_hptt_listevents_eventlist'] = 'Ereignistabelle';
$lang['lm_hptt_listmembers_memberlist_overview'] = 'Punkteübersicht';
$lang['lm_hptt_listmembers_memberlist_detail'] = 'Detaillierte Punktliste';
$lang['lm_hptt_admin_manage_members_memberlist'] = 'Charaktertabelle';
$lang['lm_hptt_admin_manage_items_itemlist'] = 'Gegenstandstabelle';
$lang['lm_hptt_admin_manage_events_eventlist'] = 'Ereignistabelle';

//members.php
//$lang['uc_charmanager']       = 'Charakterverwaltung';
$lang['uc_change_pic']				= 'Bild ändern';
$lang['uc_add_pic']						= 'Bild hinzufügen';
$lang['uc_add_char']          = 'Charakter hinzufügen';
$lang['uc_add_char_plain']		= 'Neu erstellen';
$lang['uc_add_char_armory']		= 'Importieren';
$lang['uc_save_char']					= 'Charakter speichern';
$lang['overtake_char']        = 'Charakter zu deinem Account zuweisen';
$lang['uc_edit_char']         = 'Charakter bearbeiten';
$lang['uc_conn_members']			= 'Verknüpfte Charaktere';
$lang['uc_connections']				= 'Verknüpfungen';
//$lang['uc_tt_n1']							= 'Wähle den Charakter, den du<br/> bearbeiten möchtest';
$lang['uc_tt_n2']							= 'Verknüpfe Dein Benutzeraccount<br/> mit Deinen Charakteren,<br/>die schon im DKP-System <br/>vorhanden sind';
$lang['uc_tt_n3']							= 'Erstelle einen Charakter der <br/>noch nicht im DKP-System <br/>vorhanden ist';
//$lang['uc_prifler_expl']			= 'Die Profiler werden nur als Links angezeigt, es erfolgt kein Import!';
$lang['uc_ext_import_sh']			= 'Daten importieren';
$lang['uc_connectme']         = 'Speichern';
$lang['uc_updat_armory']			= 'Von Armory aktualisieren';
$lang['uc_add_massupdate']		= 'Alle aktualisieren';
$lang['uc_need_confirmation']	= '[Muss noch freigeben werden]';
$lang['uc_tab_Character']			= 'Charakter';
$lang['uc_guild']							= 'Gilde';
$lang['uc_tab_skills']				= 'Skillung';
$lang['uc_tab_notes']         = 'Notizen';
$lang['uc_notes']             = 'Notizen';
$lang['manage_members_titl']	= 'Charaktere verwalten';
$lang['uc_del_warning']				= 'Soll der Charakter wirklick gelöscht werden? Alle Punkte und Gegenstände gehen unweigerlich verloren.';

// Error Messages
//$lang['uc_faild_memberadd']   = "Der Benutzer mit dem Namen %1\$s existiert bereits als ID %2\$s. Bitte versuche es mit einem anderen Namen.";
//$lang['uc_saved_not']         = 'Beim speichern der Änderungen ist ein Fehler aufgetreten. Bitte versuche es erneut oder melde es einem Administrator';
//$lang['uc_error_memberinfos']	= 'Konnte die Charakterinformationen des CharManagers nicht abrufen.';
//$lang['uc_error_raidinfos']		= 'Konnte die Raidinformationen des CharManagers nicht abrufen.';
//$lang['uc_error_iteminfos']		= 'Konnte die Gegenstandsinformationen des CharManagers nicht abrufen.';
//$lang['uc_error_itemraidi']		= 'Konnte die Gegenstands-/Raidinformationen des CharManagers nicht abrufen.';
$lang['uc_not_loggedin']			= 'Du bist nicht angemeldet';
//$lang['uc_not_installed']			= 'Das Character Manager PlugIn ist nicht installiert';
$lang['uc_no_prmissions']			= 'Du besitzt keine Berechtigung diese Seite zu betrachten. Bitte frage einen Administrator';

$lang['save_nosuc'] = 'Speichern nicht erfolgreich';
$lang['save_suc'] = 'Speichern erfolgreich';

$lang['maintenance_mode_warn'] = 'Dein System befindet sich momentan im Wartungsmodus und verweigert normalen Benutzern den Zugriff. Bitte überprüfe das <a href="'.$eqdkp_root_path.'maintenance/task_manager.php">Wartungstool</a> und deaktiviere den Modus wenn alle Wartungen durchgeführt wurden.<ul><li><a href="'.$eqdkp_root_path.'maintenance/task_manager.php">Zum Wartungsbereich</a></li><li><a href="'.$eqdkp_root_path.'maintenance/task_manager.php?disable=true">Wartungsmodus beenden</a></li></ul>';
$lang['home_of_eqdkpplus']		= 'home of the EQDKP Plus Projekt';
$lang['manage_bridge']				= 'Bridge verwalten';

$lang['templates_error'] 			= "Template-Fehler";
$lang['templates_error_desc'] = "Fehler-Beschreibung";
$lang['templates_error_more'] = "Weitere Informationen:";
$lang['templates_error1'] 		= "Keine Template-Datei für den Handler '%s' angegeben.";
$lang['templates_error2'] 		= "Die Template-Datei '%s' existiert nicht oder ist leer.";
$lang['templates_error3'] 		= "Die Template-Datei '%s' konnte nicht geladen werden.";

$lang['tab_points']						= 'Punkte';
$lang['tab_raids']						= 'Raids';
$lang['tab_items']						= 'Gegenstände';
$lang['tab_adjustments']			= 'Korrekturen';
$lang['tab_attendance']				= 'Raidteilnahme';
$lang['tab_notes']						= 'Notizen';
$lang['uc_last_update']				= 'Letzte Aktualisierung';
$lang['uc_delete_char']				= 'Charakter löschen';
$lang['no_connected_char']		= 'Du hast noch keine zugewiesenen Charaktere. Klicken um einen Charakter anzulegen.';
$lang['no_connected_char_info']		= 'Willkommen in deinem Charakter-Bereich. Hier kannst du neue Charaktere erstellen/importieren und bestehende bearbeiten.<br /><br />Du hast noch keine zugewiesenen Charaktere.<ul>
			<li>Taucht dein Charakter nicht bei den Verküpften Charakteren auf, erstelle dir einen neuen Charakter oder importiere ihn</li>
			<li>wähle ansonsten deinen Charakter aus und verknüpfe ihn mit dir</li></ul>';

//Usersettings
$lang['user_list'] = 'Benutzerliste';
$lang['user_priv'] = 'Privatsphäre';

$lang['user_priv_set_global'] = 'Kontaktdaten wie Email, Skype, ICQ einsehbar für';
$lang['user_priv_all'] = 'Öffentlichkeit';
$lang['user_priv_user'] = 'Angemeldete Benutzer';
$lang['user_priv_admin'] = 'Nur Administratoren';
$lang['user_priv_no'] = 'Niemanden';
$lang['user_priv_set'] = 'Sichtbar für ';
$lang['user_priv_bday'] = 'Geburtsdatum zusätzlich zum Alter anzeigen';

//$lang['user_priv_rl'] = 'Raidplaner Admins';

$lang['user_priv_tel_all'] = 'Telenfonnummern einsehbar für';
$lang['user_priv_tel_sms'] = '<b>SMS Empfang durch Admins komplett verhindern.</b><br>(Es können dann keine Raideinladungen per SMS empfangen werden)';

$lang['user_priv_gallery'] = 'Eigene Gallery-Bilder nicht im Profil anzeigen?';

//Infopages
$lang['info_edit_user'] = "Letzte Änderung: ";
$lang['info_edit_date'] = " / ";
$lang['info_invalid_id_title'] = 'Seite nicht vorhanden';
$lang['info_invalid_id'] = 'Diese Seite ist nicht vorhanden.';
$lang['info_edit_page'] = "Seite bearbeiten";
$lang['infopages'] = "Extraseiten";

//Userlist
$lang['user_image'] = "Benutzer-Bild";
$lang['user_contact'] = "Kontaktinformationen";
$lang['age'] = "Alter";
$lang['user_work'] = "Tätigkeit";
$lang['user_hardware'] = "Hardware";
$lang['user_interests'] = "Interessen";
$lang['user_more_information'] = "Weitere Informationen über %s";
$lang['user_comments'] = "<img src=\"".$eqdkp_root_path."images/admin/updates.png\">Admin-Notizen zu diesem Benutzer:";
$lang['user_group_footcount']	= '%d Mitglied(er) in diseser Gruppe';

//SMS
$lang['sms_perm']	= 'SMS Service';
$lang['sms_perm2']	= 'SMS senden';
$lang['sms_header'] = 'SMS Versenden';
$lang['sms_chars']	= 'Zeichen';
$lang['sms_info'] = 'Sende SMS an die Benutzer, z.B. wenn ein Raid abgesagt wurde, oder ihr kurzfristig einen Ersatz-Spieler braucht.';
$lang['sms_info_account'] = 'Ihr habt noch keinen SMS Account? Dann besorgt euch jetzt ein SMS Kontingent unter folgendem Link: ';
$lang['sms_info_account_link'] = '<a href="http://www.allvatar.com/index.php?p=sms" target="_blank">http://www.allvatar.com/index.php?p=sms</a>';
$lang['sms_send_info'] = 'Wähle mindestens einen Benutzer mit gültiger Handynummer aus, gib Deinen Text unten ein und versende das Ganze!';
$lang['sms_success'] = 'SMS erfolgreich an den SMS-Server übertragen. Es kann einige Zeit dauern, bis SMS verschickt werden.';
$lang['sms_error'] = 'Fehler beim Senden der SMS. Unbekannter Fehler, fehlender Text, oder keine Empfänger angegeben.';
$lang['sms_error_badpw'] = 'Fehler beim Senden. Der Benutzername oder das Passwort stimmen nicht.';
//$lang['sms_error_bad'] = 'Fehler beim Senden. Es befindet sich kein Guthaben mehr auf dem Account.';
$lang['sms_error_fopen'] = 'Fehler beim Senden. Der Server konnte keine fopen Verbindung zum SMS-Releay aufbauen. Entweder der SMS-Server ist nicht erreichbar, oder eurer Server lässt keine fopen Verbindungen zu. In einem solchen Fall, wendet euch bitte an euren Hoster/Admin. (und nicht an das EQdkpPlus Team/Forum)!!';
$lang['sms_error_159'] = 'Fehler beim Senden. Dienste-ID unbekannt.';
$lang['sms_error_160'] = 'Fehler beim Senden. Nachricht nicht gefunden!';
$lang['sms_error_200'] = 'Fehler beim Senden. Ausnahmefehler / XML Script unvollstaendig';
$lang['sms_error_254'] = 'Fehler beim Senden. Nachricht wurde geloescht!';

// Portal
$lang['portalplugin_settings']     = 'Einstellungen';
$lang['portalplugin_winname']      = 'Portalmoduleinstellungen';

$lang['timezone_set_gmt']					= 'Da weder in der php.ini noch in den Settings eine default timezone gesetzt wurde wurde die Zeitzone vorrübergehend auf GMT gesetzt.';

//viewevent
$lang['event_name'] = 'Ereignis-Name';
$lang['belonging_mdkppools'] = 'Zugehörige MultiDKP-Pools';
$lang['belonging_itempools'] = 'Zugehörige Itempools';

$lang['information'] = 'Informationen';
$lang['uc_profile_updater']		= 'Lade Profilinformationen, bitte warten...';
?>