<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* lang_main.php
* begin: Wed December 18 2002
*
* $Id: lang_main.php 33 2006-05-08 18:00:40Z tsigo $
*
* german translation by bom
******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['ENCODING'] = 'iso-8859-1';
$lang['XML_LANG'] = 'de';

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Gruppen-Korrekturliste';
$lang['listevents_title']     = 'Ereignis-Werte';
$lang['listiadj_title']       = 'Individuelle Korrekturliste';
$lang['listitems_title']      = 'Item-Werte';
$lang['listnews_title']       = 'News Einträge';
$lang['listmembers_title']    = 'Mitglieder Stati';
$lang['listpurchased_title']  = 'Item-Historie';
$lang['listraids_title']      = 'Raidliste';
$lang['login_title']          = 'Login';
$lang['message_title']        = 'EQdkp: Nachricht';
$lang['register_title']       = 'Registrieren';
$lang['settings_title']       = 'Account Einstellungen';
$lang['stats_title']          = "%1\$s Statistiken";
$lang['summary_title']        = 'News Zusammenfassung';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Gespeicherte Raid Historie für %1\$s sehen";
$lang['viewitem_title']       = "Kauf-Historie für %1\$s sehen";
$lang['viewmember_title']     = "Historie für %1\$s";
$lang['viewraid_title']       = 'Raid Zusammenfassung';

// Main Menu
$lang['menu_admin_panel'] = 'Administrationsbereich';
$lang['menu_events'] = 'Ereignisse';
$lang['menu_itemhist'] = 'Item-Historie';
$lang['menu_itemval'] = 'Item-Preise';
$lang['menu_news'] = 'News';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'Registrieren';
$lang['menu_settings'] = 'Einstellungen';
$lang['menu_standings'] = 'Punktestand';
$lang['menu_stats'] = 'Statistik';
$lang['menu_summary'] = 'Zusammenfassung';

// Column Headers
$lang['account'] = 'Account';
$lang['action'] = 'Aktion';
$lang['active'] = 'Aktiv';
$lang['add'] = 'Zufügen';
$lang['added_by'] = 'Zugefügt von';
$lang['adjustment'] = 'Korrektur';
$lang['administration'] = 'Administration';
$lang['administrative_options'] = 'Administrative Optionen';
$lang['admin_index'] = 'Admin Index';
$lang['attendance_by_event'] = 'Beteiligung bei Ereignis';
$lang['attended'] = 'teilgenommen';
$lang['attendees'] = 'Teilnehmer';
$lang['average'] = 'Durchschnitt';
$lang['backup_database'] = 'Datenbank-Backup (nicht mit anderen Änderungen in dieser Sitzung kombinieren)';
$lang['buyer'] = 'Käufer';
$lang['buyers'] = 'Käufer';
$lang['class'] = 'Klasse';
$lang['armor'] = 'Rüstung';
$lang['type'] = 'Rüstung';
$lang['class_distribution'] = 'Klassen-Verteilung';
$lang['class_summary'] = "Klassen-Zusammenfassung: %1\$s to %2\$s";
$lang['configuration'] = 'Konfiguration';
$lang['plus_vcheck']	= 'Update Check';
$lang['config_plus']	= 'PLUS Einstellungen';
$lang['current'] = 'Jetzt';
$lang['date'] = 'Datum';
$lang['delete'] = 'Löschen';
$lang['delete_confirmation'] = 'Löschungsbestätigung';
$lang['dkp_value'] = "%1\$s Wert";
$lang['drops'] = 'Drops';
$lang['earned'] = 'Bekommen';
$lang['enter_dates'] = 'Daten eingeben';
$lang['eqdkp_index'] = 'EQdkp Index';
$lang['eqdkp_upgrade'] = 'EQdkp Upgrade';
$lang['event'] = 'Ereignis';
$lang['events'] = 'Ereignisse';
$lang['filter'] = 'Filter';
$lang['first'] = 'Erster';
$lang['rank'] = 'Rang';
$lang['general_admin'] = 'Allgemeine Administration';
$lang['get_new_password'] = 'Neues Paßwort holen';
$lang['group_adj'] = 'Gruppen-Kor.';
$lang['group_adjustments'] = 'Gruppen Korrekturen';
$lang['individual_adjustments'] = 'Individuelle Korrekturen';
$lang['individual_adjustment_history'] = 'Individuelle Korrektur-Historie';
$lang['indiv_adj'] = 'Indiv. Kor.';
$lang['ip_address'] = 'IP-Addresse';
$lang['item'] = 'Item';
$lang['items'] = 'Items';
$lang['item_purchase_history'] = 'Item-Kauf-Historie';
$lang['last'] = 'Letzter';
$lang['lastloot'] = 'Letzter Loot';
$lang['lastraid'] = 'Letzter Raid';
$lang['last_visit'] = 'Letzter Besuch';
$lang['level'] = 'Level';
$lang['log_date_time'] = 'Datum/Zeit von diesem Log';
$lang['loot_factor'] = 'Loot Faktor';
$lang['loots'] = 'Loots';
$lang['manage'] = 'Verwalten';
$lang['member'] = 'Mitglied';
$lang['members'] = 'Mitglieder';
$lang['members_present_at'] = "Mitglieder anwesend am %1\$s um %2\$s";
$lang['miscellaneous'] = 'Diverses';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['note'] = 'Notiz';
$lang['online'] = 'Online';
$lang['options'] = 'Optionen';
$lang['paste_log'] = 'Füge ein Log unten ein';
$lang['percent'] = 'Prozent';
$lang['permissions'] = 'Berechtigungen';
$lang['per_day'] = 'Pro Tag';
$lang['per_raid'] = 'Pro Raid';
$lang['pct_earned_lost_to'] = 'Verdientes verringert durch';
$lang['preferences'] = 'Voreinstellungen';
$lang['purchase_history_for'] = "Kauf-Historie für %1\$s";
$lang['quote'] = 'Angebot';
$lang['race'] = 'Rasse';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'Raid-Beteiligungs-Historie';
$lang['raids_lifetime'] = "Lebensdauer (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Letzte %1\$d Tage";
$lang['rank_distribution'] = 'Rang-Aufteilung';
$lang['recorded_raid_history'] = "Gespeicherte Raid-Historie für %1\$s";
$lang['reason'] = 'Grund';
$lang['registration_information'] = 'Registrations Information';
$lang['result'] = 'Ergebnis';
$lang['session_id'] = 'Session ID';
$lang['settings'] = 'Einstellungen';
$lang['spent'] = 'Ausgegeben';
$lang['summary_dates'] = "Raid Zusammenfassung: %1\$s bis %2\$s";
$lang['themes'] = 'Themes';
$lang['time'] = 'Zeit';
$lang['total'] = 'Gesamt';
$lang['total_earned'] = 'Gesamt verdient';
$lang['total_items'] = 'Gesamte Items';
$lang['total_raids'] = 'Gesamte Raids';
$lang['total_spent'] = 'Gesamt ausgegeben';
$lang['transfer_member_history'] = 'Transfer Member History';
$lang['turn_ins'] = 'Turn-ins';
$lang['type'] = 'Typ';
$lang['update'] = 'Aktualisieren';
$lang['updated_by'] = 'aktualisiert von';
$lang['user'] = 'User';
$lang['username'] = 'Username';
$lang['value'] = 'Wert';
$lang['view'] = 'Ansehen';
$lang['view_action'] = 'Aktion ansehen';
$lang['view_logs'] = 'Logs ansehen';

// Page Foot Counts
$lang['listadj_footcount']               = "... %1\$d Korrektur(en) gefunden / %2\$d pro Seite";
$lang['listevents_footcount']            = "... %1\$d Ereignis(se) gefunden / %2\$d pro Seite";
$lang['listiadj_footcount']              = "... %1\$d individuelle Korrektur(en) gefunden / %2\$d pro Seite";
$lang['listitems_footcount']             = "... %1\$d einmalige(s) Item(s) gefunden / %2\$d pro Seite";
$lang['listmembers_active_footcount']    = "... %1\$d aktive(s) Mitglied(er) gefunden / %2\$sZeige alle</a>";
$lang['listmembers_compare_footcount']   = "... vergleiche %1\$d Mitglieder";
$lang['listmembers_footcount']           = "... %1\$d Mitglied(er) gefunden";
$lang['listnews_footcount']              = "... %1\$d Newseinträge gefunden / %2\$d pro Seite";
$lang['listpurchased_footcount']         = "... %1\$d Item(s) gefunden / %2\$d pro Seite";
$lang['listraids_footcount']             = "... %1\$d Raid(s) gefunden / %2\$d pro Seite";
$lang['stats_active_footcount']          = "... %1\$d Aktive(s) Mitglied(er) / %2\$sZeige alle</a>";
$lang['stats_footcount']                 = "... %1\$d Mitglied(er)";
$lang['viewevent_footcount']             = "... %1\$d Raid(s) gefunden";
$lang['viewitem_footcount']              = "... %1\$d Item(s) gefunden";
$lang['viewmember_adjustment_footcount'] = "... %1\$d individuelle Korrektur(en) gefunden";
$lang['viewmember_item_footcount']       = "... %1\$d gekaufte Item(s) gefunden / %2\$d pro Seite";
$lang['viewmember_raid_footcount']       = "... %1\$d teilgenommene(n) Raid(s) gefunden / %2\$d pro Seite";
$lang['viewraid_attendees_footcount']    = "... %1\$d Teilnehmer gefunden";
$lang['viewraid_drops_footcount']        = "... %1\$d Drop(s) gefunden";

// Submit Buttons
$lang['close_window'] = 'Fenster schließen';
$lang['compare_members'] = 'Vergleiche Mitglieder';
$lang['create_news_summary'] = 'Erstelle Raid-Zusammenfassung';
$lang['login'] = 'Login';
$lang['logout'] = 'Logout';
$lang['log_add_data'] = 'Daten zum Formular zufügen';
$lang['lost_password'] = 'Passwort verloren';
$lang['no'] = 'Nein';
$lang['proceed'] = 'Fortfahren';
$lang['reset'] = 'Zurücksetzen';
$lang['set_admin_perms'] = 'Administrative Berechtigungen setzen';
$lang['submit'] = 'Abschicken';
$lang['upgrade'] = 'Upgrade';
$lang['yes'] = 'Ja';

// Form Element Descriptions
$lang['admin_login'] = 'Administrator Login';
$lang['confirm_password'] = 'Paßwort bestätigen';
$lang['confirm_password_note'] = 'Du mußt Dein Paßwort nur bestätigen, wenn Du es oben geändert hast';
$lang['current_password'] = 'Jetziges Password';
$lang['current_password_note'] = 'Du mußt Dein jetziges Paßwort nur bestätigen, wenn Du Benutzername oder Paßwort ändern willst';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email Addresse';
$lang['ending_date'] = 'End-Datum';
$lang['from'] = 'Von';
$lang['guild_tag'] = 'Gildenbezeichnung';
$lang['language'] = 'Sprache';
$lang['new_password'] = 'Neues Paßwort';
$lang['new_password_note'] = 'Du brauchst nur ein neues Paßwort eingeben, wenn Du es ändern willst';
$lang['password'] = 'Paßwort';
$lang['remember_password'] = 'Login merken (Cookie)';
$lang['starting_date'] = 'Startdatum';
$lang['style'] = 'Stil';
$lang['to'] = 'Zu';
$lang['username'] = 'Benutzername';
$lang['users'] = 'Benutzer';

// Pagination
$lang['next_page'] = 'Nächste Seite';
$lang['page'] = 'Seite';
$lang['previous_page'] = 'Vorige Seite';

// Permission Messages
$lang['noauth_default_title'] = 'Zugriff verweigert';
$lang['noauth_u_event_list'] = 'Du hast keine Berechtigung Ereignisse aufzulisten.';
$lang['noauth_u_event_view'] = 'Du hast keine Berechtigung Ereignisse zu sehen.';
$lang['noauth_u_item_list'] = 'Du hast keine Berechtigung Items aufzulisten.';
$lang['noauth_u_item_view'] = 'Du hast keine Berechtigung Items zu sehen.';
$lang['noauth_u_member_list'] = 'Du hast keine Berechtigung Mitgliederstände zu sehen.';
$lang['noauth_u_member_view'] = 'Du hast keine Berechtigung Historie der Mitglieder zu sehen.';
$lang['noauth_u_raid_list'] = 'Du hast keine Berechtigung Raids aufzulisten.';
$lang['noauth_u_raid_view'] = 'Du hast keine Berechtigung Raids zu sehen.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde gespeichert.';
$lang['update_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde aktualisiert.';
$lang['update_settings_success'] = 'Die Benutzereinstellungen wurden aktualisiert.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Char\' Namen in EverQuest beinhalten nur alphabetische Zeichen.';
$lang['fv_already_registered_email'] = 'Diese Mailadresse ist bereits registriert.';
$lang['fv_already_registered_username'] = 'Dieser Benutzername ist bereits registriert.';
$lang['fv_difference_transfer'] = 'Ein Historientransfer geht nur zwischen zwei unterschiedlichen Leuten.';
$lang['fv_difference_turnin'] = 'Ein turn-in geht nur zwischen zwei unterschiedlichen Leuten.';
$lang['fv_invalid_email'] = ' Die Mailadresse scheint nicht gültig zu sein.';
$lang['fv_match_password'] = 'Die Paßwortfelder müssen übereinstimmen.';
$lang['fv_member_associated']  = "%1\$s gehört bereits zu einem anderen Benutzeraccount.";
$lang['fv_number'] = 'Muß eine Zahl sein.';
$lang['fv_number_adjustment'] = 'Das Korrekturwert-Feld muß eine Zahl sein.';
$lang['fv_number_alimit'] = 'Das Korrekturgrenze-Feld muß eine Zahl sein.';
$lang['fv_number_ilimit'] = 'Das Itemgrenze-Feld muß eine Zahl sein.';
$lang['fv_number_inactivepd'] = 'Die inactive period  muß eine Zahl sein.';
$lang['fv_number_pilimit'] = 'Die gekaufte-Items-Grenze muß eine Zahl sein.';
$lang['fv_number_rlimit'] = 'Die Raidgrenze muß eine Zahl sein.';
$lang['fv_number_value'] = 'Das Werte-Feld muß eine Zahl sein.';
$lang['fv_number_vote'] = 'Das Abstimmungs-Feld muß eine Zahl sein.';
$lang['fv_range_day'] = 'Das Tag-Feld muß eine Zahl zwischen 1 und 31 sein.';
$lang['fv_range_hour'] = 'Das Stunden-Feld muß eine Zahl zwischen 0 und 23 sein.';
$lang['fv_range_minute'] = 'Das Minuten-Feld muß eine Zahl zwischen 0 und 59 sein.';
$lang['fv_range_month'] = 'Das Monats-Feld muß eine Zahl zwischen 1 und 12 sein.';
$lang['fv_range_second'] = 'Das zweite Feld muß eine Zahl zwischen 0 und 59 sein.';
$lang['fv_range_year'] = 'Das Jahr-Feld mindestens eine Zahl ab 1998 sein.';
$lang['fv_required'] = 'Muß-Feld';
$lang['fv_required_acro'] = 'Das Gildenkurtzwort-Feld ist notwendig.';
$lang['fv_required_adjustment'] = 'Das Korrekturwert-Feld ist notwendig.';
$lang['fv_required_attendees'] = 'Es muß mindestens ein Teilnehmer im Raid sein.';
$lang['fv_required_buyer'] = 'Ein Käufer muß ausgewählt sein.';
$lang['fv_required_buyers'] = 'Mindestens ein Käufer muß ausgewählt sein.';
$lang['fv_required_email'] = 'Das Mailadressen-Feld ist notwendig.';
$lang['fv_required_event_name'] = 'Ein Ereignis muß ausgewählt sein.';
$lang['fv_required_guildtag'] = 'Das Gildenbezeichnungs-Feld ist notwendig.';
$lang['fv_required_headline'] = 'Das Kopfzeilen-Feld ist notwendig.';
$lang['fv_required_inactivepd'] = 'Wenn inaktive Mitglieder verstecken auf Ja gesetzt ist, muß auch ein Wert für die inactive Zeit angegeben werden.';
$lang['fv_required_item_name'] = 'Das Itemname-Feld muß ausgefüllt sein, oder ein vorhandenes Item muß ausgewählt sein.';
$lang['fv_required_member'] = 'Ein Mitglied muß ausgewählt sein.';
$lang['fv_required_members'] = 'Mindestens ein Mitglied muß ausgewählt sein.';
$lang['fv_required_message'] = 'Das Nachrichten-Feld ist notwendig.';
$lang['fv_required_name'] = 'Das Namens-Feld ist notwendig.';
$lang['fv_required_password'] = 'Das Paßwort-Feld ist notwendig.';
$lang['fv_required_raidid'] = 'Ein Raid muß ausgewählt sein.';
$lang['fv_required_user'] = 'Das Benutzernamen-Feld ist notwendig.';
$lang['fv_required_value'] = 'Das Werte-Feld ist notwendig.';
$lang['fv_required_vote'] = 'Das Abstimmungs-Feld ist notwendig.';

// Miscellaneous
$lang['added'] = 'Zugefügt';
$lang['additem_raidid_note'] = "Nur Raids neuer als zwei Wochen werden angezeigt / %1\$sZeige alle</a>";
$lang['additem_raidid_showall_note'] = 'Alle Raids anzeigen';
$lang['addraid_datetime_note'] = 'Wenn Du ein Log analysierst, wird Datum und Zeit automatisch gefunden.';
$lang['addraid_value_note'] = 'für einen einmaligen Bonus; wenn es leer bleibt, wird der voreingestellte Wert für das Ereignis genommen';
$lang['add_items_from_raid'] = 'Füge Items von diesem Raid zu';
$lang['deleted'] = 'Gelöscht';
$lang['done'] = 'Fertig';
$lang['enter_new'] = 'Neu eingeben';
$lang['error'] = 'Fehler';
$lang['head_admin'] = 'Head Admin';
$lang['hold_ctrl_note'] = 'STRG gedrückt halten, um mehrere auszuwählen';
$lang['list'] = 'Anzeigen';
$lang['list_groupadj'] = 'Liste der Gruppenkorrekturen';
$lang['list_events'] = 'Liste der Ereignisse';
$lang['list_indivadj'] = 'Liste der individuellen Korrekturen';
$lang['list_items'] = 'Liste der Items';
$lang['list_members'] = 'Liste der Mitglieder';
$lang['list_news'] = 'Liste der News';
$lang['list_raids'] = 'Liste der Raids';
$lang['may_be_negative_note'] = 'kann negativ sein';
$lang['not_available'] = 'Nicht verfügbar';
$lang['no_news'] = 'Keine Newseinträge gefunden.';
$lang['of_raids'] = "%1\$d%% von Raids";
$lang['or'] = 'Oder';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Vorschau';
$lang['required_field_note'] = 'Items mit * gekennzeichnet sind Muß-Felder.';
$lang['select_1ofx_members'] = "Wähle 1 von %1\$d Mitgliedern...";
$lang['select_existing'] = 'Wähle Vorhandene';
$lang['select_version'] = 'Wähle die EQdkp-Version von der Du aktualisierst';
$lang['success'] = 'Erfolgreich';
$lang['s_admin_note'] = 'Diese Felder können nicht von den Benutzern verändert werden.';
$lang['transfer_member_history_description'] = 'Das verschiebt die ganze Historie (Raids, Items, Korrekturen) eines Mitglieds zu einem anderen.';
$lang['updated'] = 'Aktualisiert';
$lang['upgrade_complete'] = 'Deine EQdkp-Installation wurde erfolgreich aktualisiert.<br /><br /><b class="negative">Lösche diese Datei aus Sicherheitsgründen!</b>';

// Settings
$lang['account_settings'] = 'Account Einstellungen';
$lang['adjustments_per_page'] = 'Korrekturen pro Seite';
$lang['basic'] = 'Grundeinstellung';
$lang['events_per_page'] = 'Ereignisse pro Seite';
$lang['items_per_page'] = 'Items pro Seite';
$lang['news_per_page'] = 'Newseinträge pro Seite';
$lang['raids_per_page'] = 'Raids pro Seite';
$lang['associated_members'] = 'Verknüpfte Mitglieder';
$lang['guild_members'] = 'Gildenmitglied(er)';
$lang['default_locale'] = 'Standard Gebietsschema';


// Error messages
$lang['error_account_inactive'] = 'Dein Account ist inaktiv.';
$lang['error_already_activated'] = 'Der Account ist bereits aktiviert.';
$lang['error_invalid_email'] = 'Es wurde keine gültige Mailadresse angegeben.';
$lang['error_invalid_event_provided'] = 'Es wurde keine gültige Ereignis-ID angegeben.';
$lang['error_invalid_item_provided'] = 'Es wurde keine gültige Item-ID angegeben.';
$lang['error_invalid_key'] = 'Du hast einen ungültigen Aktivierungs-Key angegeben.';
$lang['error_invalid_name_provided'] = 'Es wurde kein gültiger Mitgliedername angegeben.';
$lang['error_invalid_news_provided'] = 'Es wurde keine gültige News-ID angegeben.';
$lang['error_invalid_raid_provided'] = 'Es wurde keine gültige Raid-ID angegeben.';
$lang['error_user_not_found'] = 'Es wurde kein gültiger Benutzername angegeben';
$lang['incorrect_password'] = 'Falsches Paßwort';
$lang['invalid_login'] = 'Du hast einen ungültigen Benutzernamen oder Paßwort angegeben';
$lang['not_admin'] = 'Du bist kein Administrator';

// Registration
$lang['account_activated_admin']   = 'Der Account wurde aktiviert. Dem Benutzer wurde eine Informationsmail über die Änderungen geschickt.';
$lang['account_activated_user']    = "Dein Account wurde aktiviert und Du kannst Dich nun %1\$seinloggen%2\$s.";
$lang['password_sent'] = 'Dein neues Accountpaßwort wurde Dir zugemailt.';
$lang['register_activation_self']  = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muß er von Dir aktiviert werden.<br /><br />Eine Mail wurde an %1\$s gesendet mit den Informationen zur Aktivierung.";
$lang['register_activation_admin'] = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muß er von einem Administrator aktiviert werden.<br /><br />Eine Mail mit mehr Informationen wurde an %1\$s gesendet.";
$lang['register_activation_none']  = "Dein Account wurde erstellt und Du kannst Dich nun %1\$seinloggen%2\$s.<br /><br />Eine Mail mit mehr Informationen wurde an %3\$s gesendet.";

//plus
$lang['news_submitter'] = 'geschrieben von';
$lang['news_submitat'] = 'um';
$lang['droprate_loottable'] = "Loot Tabelle für";
$lang['droprate_name'] = "Itemname";
$lang['droprate_count'] = "Anzahl";
$lang['droprate_drop'] = "Drop %";

$lang['Points_header'] = "Quick DKP";
$lang['Points_class'] = "Klasse:";
$lang['Points_Char'] = "Name:";
$lang['Points_DKP'] = "DKP:";
$lang['Points_CHAR'] = "Kein Member zugewiesen";

$lang['Itemsearch_link'] = "Item-Suche";
$lang['Itemsearch_search'] = "Item Suche: ";
$lang['Itemsearch_searchby'] = "Suche nach: ";
$lang['Itemsearch_item'] = "Item";
$lang['Itemsearch_buyer'] = "K&auml;ufer";
$lang['Itemsearch_raid'] = "Raid";
$lang['Itemsearch_unique'] = "Einzigartig: ";
$lang['Itemsearch_no'] = "Nein";
$lang['Itemsearch_yes'] = "Ja";

$lang['bosscount_player'] = "Spieler: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "Items: ";
$lang['bosscount_dkptotal'] = "DKP insgesamt: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP PLUS";
$lang['Multi_entryheader'] 		= "MultiDKP - Konto hinzufügen";
$lang['Multi_pageheader'] 		= "MultiDKP - Konten anzeigen";
$lang['Multi_events'] 				= "Events:";
$lang['Multi_eventname'] 				= "Eventname";
$lang['Multi_discnottolong'] 	= "(Spaltenname) - nicht zu lang wählen, da sonst die Tabellen zu breit werden. Wähle z.b. MC, BWL, AQ usw !";
$lang['Multi_kontoname_short']= "Kontoname:";
$lang['Multi_discr'] 					= "Beschreibung:";
$lang['Multi_events'] 				= "Dem Konto zugeordnete Events";

$lang['Multi_addkonto'] 			  = "Konto hinzufügen";
$lang['Multi_updatekonto'] 			= "Konto aktualisieren";
$lang['Multi_deletekonto'] 			= "Konto löschen";
$lang['Multi_viewkonten']			  = "Konten anzeigen";
$lang['Multi_chooseevents']			= "Events auswählen";
$lang['multi_footcount'] 				= "... %1\$d DKP Konten gefunden / %2\$d pro Seite";
$lang['multi_error_invalid']    = "Kein Konto gefunden....";
$lang['Multi_required_event']   = "Es muss mindestens ein Event ausgewählt sein !";
$lang['Multi_required_name']    = "Es muss ein Raidname eingegeben werden !";
$lang['Multi_required_disc']    = "Es muss eine Beschreibung eingegeben werden !";
$lang['Multi_admin_add_multi_success'] 		= "Das Konto %1\$s ( %2\$s ) mit den Events %3\$s wurde der Datenbank zugefügt.";
$lang['Multi_admin_update_multi_success'] = "Das Konto %1\$s ( %2\$s ) mit den Events %3\$s wurde der Datenbank geändert.";
$lang['Multi_admin_delete_success']       = "Das Konto %1\$s wurde aus der Datenbank gelöscht.";
$lang['Multi_confirm_delete']    					= 'Bist Du sicher, daß Du dieses Konto löschen willst?';

$lang['Multi_total_cost']   							= 'Gesamtpunkte für dieses Konto';
$lang['Multi_Accs']    										= 'MultiDKP Konten';

//update

$lang['upd_eqdkp_status']    										= 'EQDKP Update Status';
$lang['upd_system_status']    									= 'System Status';
$lang['upd_template_status']    								= 'Template Status';
$lang['upd_update_need']    										= 'Update notwendig!';
$lang['upd_update_need_link']    								= 'Alle benötigten Komponenten installiern';
$lang['upd_no_update']    											= 'Kein Update notwendig. Das System ist auf dem aktuellesten Stand.';
$lang['upd_status']    													= 'Status';
$lang['upd_state_error']    										= 'Fehler';
$lang['upd_sql_string']    											= 'SQL Anweisung';
$lang['upd_sql_status_done']    								= 'ausgeführt';
$lang['upd_sql_error']    											= 'Fehler';
$lang['upd_sql_footer']    											= " SQL Anweisungen ausgeführt";
$lang['upd_sql_file_error']    									= " Es ist ein Fehler aufgetreten! Das erforderliche SQL File %1\$s konnte nicht gefunden werden!";
$lang['upd_eqdkp_system_title']    							= 'EQDKP System Komponenten Update';
$lang['upd_plus_version']    										= 'EQDKP Plus Version';
$lang['upd_plus_feature']    										= 'Feature';
$lang['upd_plus_detail']    										= 'Details';
$lang['upd_update']    													= 'Update';
$lang['upd_eqdkp_template_title']    						= 'EQDKP Templates Update';
$lang['upd_template_name']    									= 'Template Name';
$lang['upd_template_state']    									= 'Template Status';
$lang['upd_template_filestate']    							= 'Template Ordner vorhanden';
$lang['upd_link_install']    										= 'Update';
$lang['upd_link_reinstall']    									= 'Neu installieren';
$lang['upd_admin_need_update']    							= 'Es wurde ein Datenbankfehler festgestellt. Das System ist nicht auf dem neustens Stand und muss aktualisiert werden.';
$lang['upd_admin_link_update']									= 'Hier klicken um das Problem zu beheben.';
$lang['upd_backto']    													= 'Zurück zur Übersicht';

// Event Icon
$lang['event_icon_header']    								  = 'Event Icon auswählen';

//Admin Helpline
$lang['it_help'] = 'Item einfügen: z.b. [item]Brustplatte des Richturteils[/item]';
$lang['ii_help'] = 'ItemIcon einfügen: z.b. [itemicon]Brustplatte des Richturteils[/itemicon]';

//update Itemstats
$lang['updi_header']    								    	= 'Itemstats Daten aktualisieren';
$lang['updi_header2']    								    	= 'Itemstats Informationen';
$lang['updi_action']    								    	= 'Aktion';
$lang['updi_notfound']    								    = 'Nicht gefunden';
$lang['updi_writeable_ok']    							  = 'Datei beschreibbar ';
$lang['updi_writeable_no']    								= 'Datei NICHT beschreibbar ';
$lang['updi_help']    								    		= 'Beschreibung';
$lang['updi_footcount']    								    = ' Items aktualisiert.';
$lang['updi_curl_bad']    								    = 'Die benötigte PHP Funktion cURL wurde nicht gefunden. Itemstats wird unter umständen nicht richtig funktionieren. Wendet euch bitte an euren Administrator.';
$lang['updi_curl_ok']    								    	= ' cURL gefunden.';
$lang['updi_fopen_bad']    								    = 'Die benötigte PHP Funktion fopen wurde nicht gefunden. Itemstats wird unter umständen nicht richtig funktionieren. Wendet euch bitte an euren Administrator.';
$lang['updi_fopen_ok']    								    = 'fopen gefunden.';
$lang['updi_nothing_found']						    		= ' Keine Items gefunden';
$lang['updi_itemscount']  						    		= ' Einträge im Itemcache:';
$lang['updi_baditemscount']						    		= ' Fehlerhafte Einträge:';
$lang['updi_items']										    		= ' Items in der Datenbank:';
$lang['updi_items_duplicate']					    		= ' (Mit doppelten Items)';
$lang['updi_show_all']    								    = 'Liste alle Items mit Itemstats';
$lang['updi_refresh_all']    								  = 'Alle Items löschen und aktualisieren.';
$lang['updi_refresh_bad']    								  = 'Nur defekte Items aktualisieren';
$lang['updi_refresh_raidbank']    						= 'Raidbanker Items aktualisieren';
$lang['updi_refresh_tradeskill']   						= 'Tradeskill Items aktualisieren';
$lang['updi_help_show_all']    								= 'Dabei werden alle Items mit ihren Stats angezeigt. Fehlende Stats werden dabei nachgeladen. (empfohlen)';
$lang['updi_help_refresh_all']  							= 'Löscht den kompletten Itemcache und versucht alle im EQDKP eingetragenen Item zu aktualisieren. ACHTUNG: Wenn ihr euer Itemcache mit einem Forum teilt, können die im Forum eingetragenen Items nicht aktualisiert werden. Je nach Geschwindigkeit eures Webservers und der Verfügbarkeit von www.buffed.de bzw Allakhazam.com kann diese Aktion mehrere Minuten dauern. Unter umständen verhindern die Einstellungen eures Webserver eine erfolgreiche Ausführung. Wendet euch dann bitte an euren Administrator.';
$lang['updi_help_refresh_bad']    						= 'Löscht alle fehlerhaften Items aus dem Cache und aktualisiert diese.';
$lang['updi_help_refresh_raidbank']    				= 'Wenn der Raidbanker installiert ist, werden die Itemstats von den im Banker eingetragenen Items aktualisiert.';
$lang['updi_help_refresh_Tradeskill']    			= 'Wenn Tradeskills installiert ist, werden die Itemstats von den in Tradeskill eingetragenen Items aktualisiert.';

$lang['updi_active'] 					   							= 'Aktiviert';
$lang['updi_inactive']    										= 'ausgeschaltet';

#$lang['']    								  = '';


?>
