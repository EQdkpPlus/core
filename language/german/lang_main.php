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

$lang['ENCODING'] = 'iso-8859-1';
$lang['XML_LANG'] = 'de';

// Linknames
$lang['rp_link_name']   = "Raidplaner";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Gruppen-Korrekturliste';
$lang['listevents_title']     = 'Ereignis-Werte';
$lang['listiadj_title']       = 'Individuelle Korrekturliste';
$lang['listitems_title']      = 'Item-Werte';
$lang['listnews_title']       = 'News Einträge';
$lang['listmembers_title']    = 'Mitglieder Statistik';
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
$lang['menu_members'] = 'Charaktere';
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
$lang['administrative_options'] = 'Administrative Einstellungen';
$lang['admin_index'] = 'Admin Index';
$lang['attendance_by_event'] = 'Beteiligung bei Ereignis';
$lang['attended'] = 'Teilgenommen';
$lang['attendees'] = 'Teilnehmer';
$lang['average'] = 'Durchschnitt';
$lang['buyer'] = 'Käufer';
$lang['buyers'] = 'Käufer';
$lang['class'] = 'Klasse';
$lang['armor'] = 'Rüstung';
$lang['type'] = 'Rüstung';
$lang['class_distribution'] = 'Klassenverteilung';
$lang['class_summary'] = "Klassen-Zusammenfassung: %1\$s bis %2\$s";
$lang['configuration'] = 'Konfiguration';
$lang['config_plus']	= 'PLUS Einstellungen';
$lang['plus_vcheck']	= 'Update Check';
$lang['current'] = 'Jetzt';
$lang['date'] = 'Datum';
$lang['delete'] = 'Löschen';
$lang['delete_confirmation'] = 'Löschbestätigung';
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
$lang['get_new_password'] = 'Neues Passwort holen';
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
$lang['options'] = 'Einstellungen';
$lang['paste_log'] = 'Füge ein Log unten ein';
$lang['percent'] = 'Prozent';
$lang['permissions'] = 'Berechtigungen';
$lang['per_day'] = 'Pro Tag';
$lang['per_raid'] = 'Pro Raid';
$lang['pct_earned_lost_to'] = 'Verdientes verringert durch';
$lang['preferences'] = 'Voreinstellungen';
$lang['purchase_history_for'] = "Kauf-Historie für %1\$s";
$lang['quote'] = 'Zitat';
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
$lang['transfer_member_history'] = 'Mitglieder Historie';
$lang['turn_ins'] = 'Turn-ins';
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
$lang['listiadj_footcount']              = "... %1\$d individuelle Korrektur(en) gefunden / %2\$d pro Seite";
$lang['listitems_footcount']             = "... %1\$d einmalige(s) Item(s) gefunden / %2\$d pro Seite";
$lang['listmembers_active_footcount']    = "... %1\$d aktive(s) Mitglied(er) gefunden / %2\$sZeige alle</a>";
$lang['listmembers_compare_footcount']   = "... vergleiche %1\$d Mitglieder";
$lang['listmembers_footcount']           = "... %1\$d Mitglied(er) gefunden";
$lang['listnews_footcount']              = "... %1\$d Newseinträge gefunden / %2\$d pro Seite";
$lang['listpurchased_footcount']         = "... %1\$d Item(s) gefunden / %2\$d pro Seite";
$lang['listraids_footcount']             = "... %1\$d Raid(s) gefunden / %2\$d pro Seite";
$lang['stats_active_footcount']          = "... %1\$d Aktive(s) Mitglied(er) gefunden / %2\$sZeige alle</a>";
$lang['stats_footcount']                 = "... %1\$d Mitglied(er) gefunden";
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
$lang['create_news_summary'] = 'Erstelle Raidzusammenfassung';
$lang['login'] = 'Anmelden';
$lang['logout'] = 'Abmelden';
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
$lang['confirm_password'] = 'Passwort bestätigen';
$lang['confirm_password_note'] = 'Du musst Dein Passwort nur bestätigen, wenn Du es oben geändert hast.';
$lang['current_password'] = 'Aktuelles Passwort';
$lang['current_password_note'] = 'Du musst Dein jetziges Passwort nur bestätigen, wenn Du Benutzername oder Passwort ändern willst.';
$lang['email'] = 'E-Mail';
$lang['email_address'] = 'E-Mail-Adresse';
$lang['ending_date'] = 'Enddatum';
$lang['from'] = 'Von';
$lang['guild_tag'] = 'Gildenbezeichnung';
$lang['language'] = 'Sprache';
$lang['new_password'] = 'Neues Passwort';
$lang['new_password_note'] = 'Du brauchst nur ein neues Passwort eingeben, wenn Du es ändern willst.';
$lang['password'] = 'Passwort';
$lang['remember_password'] = 'Login merken (Cookie)';
$lang['starting_date'] = 'Startdatum';
$lang['style'] = 'Stil';
$lang['to'] = 'Zu';
$lang['username'] = 'Benutzername';
$lang['users'] = 'Benutzer';

// Pagination
$lang['next_page'] = 'Nächste Seite';
$lang['page'] = 'Seite';
$lang['previous_page'] = 'Vorherige Seite';

// Permission Messages
$lang['noauth_default_title'] = 'Zugriff verweigert';
$lang['noauth_u_event_list'] = 'Du hast keine Berechtigung Ereignisse aufzulisten.';
$lang['noauth_u_event_view'] = 'Du hast keine Berechtigung Ereignisse zu sehen.';
$lang['noauth_u_item_list'] = 'Du hast keine Berechtigung Items aufzulisten.';
$lang['noauth_u_item_view'] = 'Du hast keine Berechtigung Items zu sehen.';
$lang['noauth_u_member_list'] = 'Du hast keine Berechtigung Mitgliederstände zu sehen.';
$lang['noauth_u_member_view'] = 'Du hast keine Berechtigung Historien der Mitglieder zu sehen.';
$lang['noauth_u_raid_list'] = 'Du hast keine Berechtigung Raids aufzulisten.';
$lang['noauth_u_raid_view'] = 'Du hast keine Berechtigung Raids zu sehen.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde gespeichert.';
$lang['update_itemvote_success'] = 'Deine Abstimmung zu dem Item wurde aktualisiert.';
$lang['update_settings_success'] = 'Die Benutzereinstellungen wurden aktualisiert.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Char\' Namen in EverQuest beinhalten nur alphabetische Zeichen.';
$lang['fv_already_registered_email'] = 'Diese E-Mail-Adresse ist bereits registriert.';
$lang['fv_already_registered_username'] = 'Dieser Benutzername ist bereits registriert.';
$lang['fv_difference_transfer'] = 'Ein Historientransfer geht nur zwischen zwei unterschiedlichen Leuten.';
$lang['fv_difference_turnin'] = 'Ein Turn-in geht nur zwischen zwei unterschiedlichen Leuten.';
$lang['fv_invalid_email'] = ' Die E-Mail-Adresse scheint nicht gültig zu sein.';
$lang['fv_match_password'] = 'Die Passwortfelder müssen übereinstimmen.';
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
$lang['fv_required_inactivepd'] = 'Wenn inaktive Mitglieder verstecken auf Ja gesetzt ist, muss auch ein Wert für die inaktive Zeit angegeben werden.';
$lang['fv_required_item_name'] = 'Das Itemname-Feld muss ausgefüllt sein, oder ein vorhandenes Item muss ausgewählt sein.';
$lang['fv_required_member'] = 'Ein Mitglied muss ausgewählt sein.';
$lang['fv_required_members'] = 'Mindestens ein Mitglied muss ausgewählt sein.';
$lang['fv_required_message'] = 'Das Nachrichten-Feld ist notwendig.';
$lang['fv_required_name'] = 'Das Namens-Feld ist notwendig.';
$lang['fv_required_password'] = 'Das Passwort-Feld ist notwendig.';
$lang['fv_required_raidid'] = 'Ein Raid muss ausgewählt sein.';
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
$lang['hold_ctrl_note'] = 'STRG gedrückt halten, um mehrere Einträge auszuwählen';
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
$lang['required_field_note'] = 'Mit * gekennzeichnete Felder sind Pflichtfelder.';
$lang['select_1ofx_members'] = "Wähle 1 von %1\$d Mitgliedern...";
$lang['select_existing'] = 'Wähle Vorhandene';
$lang['select_version'] = 'Wähle die EQdkp-Version von der Du aktualisierst';
$lang['success'] = 'Erfolgreich';
$lang['s_admin_note'] = 'Diese Felder können nicht von den Benutzern verändert werden.';
$lang['transfer_member_history_description'] = 'Verschiebe die gesamte Historie (Raids, Items, Korrekturen) eines Mitglieds zu einem anderen Mitglied.';
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
$lang['error_email_send'] = 'Senden der Email fehlgeschlagen.';
$lang['error_invalid_email'] = 'Es wurde keine gültige E-Mail-Adresse angegeben.';
$lang['error_invalid_event_provided'] = 'Es wurde keine gültige Ereignis-ID angegeben.';
$lang['error_invalid_item_provided'] = 'Es wurde keine gültige Item-ID angegeben.';
$lang['error_invalid_key'] = 'Du hast einen ungültigen Aktivierungs-Key angegeben.';
$lang['error_invalid_name_provided'] = 'Es wurde kein gültiger Mitgliedername angegeben.';
$lang['error_invalid_news_provided'] = 'Es wurde keine gültige News-ID angegeben.';
$lang['error_invalid_raid_provided'] = 'Es wurde keine gültige Raid-ID angegeben.';
$lang['error_user_not_found'] = 'Es wurde kein gültiger Benutzername angegeben.';
$lang['error_invalid_password'] = 'Das Passwort darf keine " oder \' enthalten.';
$lang['incorrect_password'] = 'Falsches Passwort';
$lang['invalid_login'] = 'Du hast einen ungültigen Benutzernamen oder Passwort angegeben.';
$lang['not_admin'] = 'Du bist kein Administrator.';

// Registration
$lang['account_activated_admin']   = 'Der Account wurde aktiviert. Dem Benutzer wurde eine Informationsmail über die Änderungen geschickt.';
$lang['account_activated_user']    = "Dein Account wurde aktiviert und Du kannst Dich nun %1\$seinloggen%2\$s.";
$lang['password_sent'] = 'Dein neues Accountpasswort wurde Dir zugemailt.';
$lang['register_activation_self']  = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muss er von Dir aktiviert werden.<br /><br />Eine E-Mail wurde an %1\$s gesendet mit den Informationen zur Aktivierung.";
$lang['register_activation_admin'] = "Dein Account wurde erstellt. Bevor Du ihn nutzen kannst, muss er von einem Administrator aktiviert werden.<br /><br />Eine E-Mail mit mehr Informationen wurde an %1\$s gesendet.";
$lang['register_activation_none']  = "Dein Account wurde erstellt und Du kannst Dich nun %1\$seinloggen%2\$s.<br /><br />Eine E-Mail mit mehr Informationen wurde an %3\$s gesendet.";

//plus
$lang['news_submitter'] = 'geschrieben von';
$lang['news_submitat'] = 'um';
$lang['droprate_loottable'] = "Loot Tabelle für";
$lang['droprate_name'] = "Itemname";
$lang['droprate_count'] = "Anzahl";
$lang['droprate_drop'] = "Drop %";

$lang['Itemsearch_link'] = "Item-Suche";
$lang['Itemsearch_search'] = "Item Suche: ";
$lang['Itemsearch_searchby'] = "Suche nach: ";
$lang['Itemsearch_item'] = "Item";
$lang['Itemsearch_buyer'] = "Käufer";
$lang['Itemsearch_raid'] = "Raid";
$lang['Itemsearch_unique'] = "Einzigartig: ";
$lang['Itemsearch_no'] = "Nein";
$lang['Itemsearch_yes'] = "Ja";

$lang['bosscount_player'] = "Spieler: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "Items: ";
$lang['bosscount_dkptotal'] = "DKP insgesamt: ";

//MultiDKP
$lang['Plus_menuentry'] 		= "EQdkp Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Konto zufügen";
$lang['Multi_pageheader'] 		= "MultiDKP - Konten anzeigen";
$lang['Multi_events'] 			= "Ereignisse:";
$lang['Multi_eventname'] 		= "Ereignisname";
$lang['Multi_discnottolong'] 	= "(Spaltenname) - nicht zu lang wählen, da sonst die Tabellen zu breit werden. Wähle z.b. MC, BWL, AQ usw.!";
$lang['Multi_kontoname_short']	= "Kontoname";
$lang['Multi_discr'] 			= "Beschreibung";
$lang['Multi_events'] 			= "Dem Konto zugeordnete Ereignisse";

$lang['Multi_addkonto'] 		= "Konto zufügen";
$lang['Multi_updatekonto'] 		= "Konto aktualisieren";
$lang['Multi_deletekonto'] 		= "Konto löschen";
$lang['Multi_viewkonten']		= "Konten anzeigen";
$lang['Multi_chooseevents']		= "Ereignisse auswählen";
$lang['multi_footcount'] 		= "... %1\$d DKP Kont(o)en gefunden / %2\$d pro Seite";
$lang['multi_error_invalid']    = "Kein Konto gefunden....";
$lang['Multi_required_event']   = "Es muss mindestens ein Ereignis ausgewählt sein!";
$lang['Multi_required_name']    = "Es muss ein Name eingegeben werden!";
$lang['Multi_required_disc']    = "Es muss eine Beschreibung eingegeben werden!";
$lang['Multi_admin_add_multi_success'] 		= "Das Konto %1\$s ( %2\$s ) mit den Ereignissen %3\$s wurde der Datenbank zugefügt.";
$lang['Multi_admin_update_multi_success'] = "Das Konto %1\$s ( %2\$s ) mit den Ereignissen %3\$s wurde in der Datenbank geändert.";
$lang['Multi_admin_delete_success']       = "Das Konto %1\$s wurde aus der Datenbank gelöscht.";
$lang['Multi_confirm_delete']    					= 'Bist Du sicher, dass Du dieses Konto löschen willst?';

$lang['Multi_total_cost']   							= 'Gesamt Konto';
$lang['Multi_Accs']    										= 'MultiDKP Konten';

//update
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
$lang['upd_link_reinstall']    									= 'Neu installieren';
$lang['upd_admin_need_update']    							= 'Es wurde ein Datenbankfehler festgestellt. Das System ist nicht auf dem neuesten Stand und muss aktualisiert werden.';
$lang['upd_admin_link_update']									= 'Hier klicken, um das Problem zu beheben.';
$lang['upd_backto']    													= 'Zurück zur Übersicht';

// Event Icon
$lang['event_icon_header']    								  = 'Ereignis Icon auswählen';

//update Itemstats
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

$lang['fontcolor']    			  		= 'Schriftfarbe';
$lang['Warrior']    					= 'Krieger';
$lang['Rogue']    						= 'Schurke';
$lang['Hunter']    						= 'Jäger';
$lang['Paladin']    					= 'Paladin';
$lang['Priest']    						= 'Priester';
$lang['Druid']    						= 'Druide';
$lang['Shaman']    						= 'Schamane';
$lang['Warlock']    					= 'Hexenmeister';
$lang['Mage']    						= 'Magier';

# Reset DB Feature
$lang['reset_header']    			= 'EQdkp Daten löschen';
$lang['reset_infotext']  			= 'ACHTUNG, das Löschen der Daten kann nicht rückgängig gemacht werden! Alle eingetragenen Daten werden unwiderbringlich gelöscht. Legt Euch unbedingt ein Backup Eurer Daten an. <br> Um eine der Aktionen auszuführen, müsst Ihr das Löschen mit der Eingabe von "LÖSCHEN" in das Eingabefeld bestätigen.';
$lang['reset_type']    				= 'Datentyp';
$lang['reset_disc']    				= 'Beschreibung';
$lang['reset_sec']    				= 'Bestätigung';
$lang['reset_action']    			= 'Aktion';

$lang['reset_news']					 = 'News';
$lang['reset_news_disc']		  	= 'Löscht alle News aus der Datenbank.';
$lang['reset_dkp'] 					= 'Punkte';
$lang['reset_dkp_disc']			  	= 'Löscht alle Raids, alle eingetragenen Items und setzt alle DKP Punkte wieder auf null. Die angelegten Mitglieder haben wieder 0 DKP.';
$lang['reset_ALL']   				= 'Alles';
$lang['reset_ALL_DISC']				= 'Löscht die gesamte Datenbank. Es werden alle Mitglieder, Raids und Items gelöscht. Nur die Benutzer bleiben erhalten.';

$lang['reset_confirm_text']	  		= ' hier eingeben =>';
$lang['reset_confirm']			  	= 'LÖSCHEN';

// Armory Menu
$lang['lm_armorylink1']				= 'Armory';
$lang['lm_armorylink2']				= 'Skillung';
$lang['lm_armorylink3']				= 'Gilde';

$lang['updi_update_ready']			= 'Die Items wurden erfolgreich aktualisiert. Das Fenster kann <a href="#" onclick="javascript:parent.closeWindow()" >geschlossen</a> werden.';
$lang['updi_update_alternative']	= 'Alle Items löschen und aktualisieren - Alternatives Update, welches ein Timeout bzw. einen "Memory Size" Fehler verhindern soll.';
$lang['zero_sum']					= ' bei Nullsummen DKP';

//Hybride
$lang['Hybrid']		= 'Hybride';

$lang['Jump_to'] 				= 'Zum Video auf ';
$lang['News_vid_help'] 			= 'Zum Einbinden von Videos, den Link in die News schreiben ohne [Tags] zu benutzen. Unterstützte Videoseiten: google video, youtube, myvideo, clipfish, sevenload, metacafe und streetfire. ';

$lang['SubmitNews'] 		   = 'News berichten';
$lang['SubmitNews_help'] 	   = 'Ihr habt eine gute News, die auf Allvatar.com veröffentlicht werden sollte? Dann immer her damit!';

$lang['MM_User_Confirm']	   = 'Achtung, soll wirklich DEIN Admin Account ausgewählt werden?? Achtung, wenn Du dir selber die Rechte für das Ändern von User-Berechtigungen nimmst, kann das später nur in der Datenbank wieder rückgängig gemacht werden!! Im Zweifelsfall Bitte abbrechen!';

$lang['beta_warning']	   	   = 'ACHTUNG du nutzt eine Beta Version von Eqdkp-Plus! Diese Version ist NICHT für den produktiven Einsatz auf eurem Live-System gedacht. Diese Version wird mit dem Release einer Stable Version nur noch kurze Zeit laufen. Check <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> ob eine Stable Version verfügbar ist!';

$lang['news_comment']        			= 'Kommentar';
$lang['news_comments']       			= 'Kommentare';
$lang['comments_no_comments']	   	   	= 'Keine Kommentare vorhanden';
$lang['comments_comments_raid']	   		= 'Kommentare';
$lang['comments_write_comment']	   		= 'Kommentar schreiben';
$lang['comments_send_comment']	   		= 'Kommentar speichern';
$lang['comments_save_wait']	   	   		= 'Bitte warten, Kommentar wird gespeichert.';

$lang['news_nocomments'] 	 		    = 'Kommentare zu dieser News deaktivieren';
$lang['news_readmore_button']  			  	= 'News erweitern';
$lang['news_readmore_button_help']  			  	= 'Um den erweiterten Newstext einzugeben, klicke hier:';
$lang['news_message'] 				  	= 'Newstext welcher auf der Startseite erscheint.';
$lang['news_permissions']			  	= 'Berechtigung';

$lang['news_permissions_text']			= 'News nicht anzeigen für';
$lang['news_permissions_guest']			= 'Gäste';
$lang['news_permissions_member']		= 'Gäste & Member (Nur für Admins sichtbar)';
$lang['news_permissions_all']			= 'Für alle sichtbar';
$lang['news_readmore'] 				  	= 'Weiterlesen...';

$lang['recruitment_open']				= 'Wir suchen Member';
$lang['recruitment_contact']			= 'bewerben';

$lang['sig_conf']						= 'Klickt auf das Bild für den BB Code.';
$lang['sig_show']						= 'Zeige WoW-Signatur für eurer Forum';

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

//Last Raids
$lang['last_raids']					    = 'Letzte Raids';

$lang['voice_error']				    = 'Keine Verbindung zum Voice Server möglich.';

$lang['login_bridge_notice']		    = 'Login - Die CMS-Bridge ist aktiviert. Benutzt die Login-Daten eures Forums/CMS um euch anzumelden.';

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


$lang['talents'] = array(
'Paladin'   	=> array('Heilig','Schutz','Vergeltung'),
'Rogue'     	=> array('Meucheln','Kampf','Täuschung'),
'Warrior'   	=> array('Waffen','Furor','Schutz'),
'Hunter'    	=> array('Tierherrschaft','Treffsicherheit','Überleben'),
'Priest'    	=> array('Disziplin','Heilig','Schatten'),
'Warlock'   	=> array('Gebrechen','Dämonologie','Zerstörung'),
'Druid'     	=> array('Gleichgewicht','Wilder Kampf','Wiederherstellung'),
'Mage'      	=> array('Arkan','Feuer','Frost'),
'Shaman'    	=> array('Elementar','Verstärkung','Wiederherstellung'),
'Death Knight'   => array('Blut','Frost','Unheilig')
);

$lang['portalmanager'] = 'Portalmodule verwalten';

$lang['air_img_resize_warning'] = 'Das Bild wurde verkleinert. Auf das Bild klicken zum öffnen. (%1$sx%2$s)';

$lang['guild_shop'] = 'Shop';

// LibLoader Language String
$lang['libloader_notfound']     = 'Die Library Loader Klasse ist nicht verfügbar.
                                  Bitte überprüfe, ob der Ordner "eqdkp/libraries/" richtig hochgeladen wurde!<br/>
                                  Falls nicht, bitte neu herunterladen. Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']       = "Die Library ist nicht mehr aktuell. Es wird mindestens Version %1\$s oder höher benötigt.<br/>
                                  Download: <a href='%2\$s' target='blank'>Libraries Download</a><br/>
                                  Bitte laden und die aktuelle Version im 'eqdkp/libraries/' Ordner überschreiben!";
$lang['libloader_tooold_plug']  = "Das Library Modul '%1\$s' ist nicht mehr aktuell. Es wird mindestens Version %2\$s oder höher benötigt.
                                  Diese ist in den Libraries ab Version %4\$s (oder höher) enthalten. Deine aktuelle Libraries Version ist %5\$s<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Bitte laden und die aktuelle Version im 'eqdkp/libraries/' Ordner überschreiben!";

$lang['more_plugins']   = "Weitere Plugins findet ihr auf der Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "Weitere Module findet ihr auf der  Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "Weitere Templates findet ihr auf der Project Homepage unter <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Abbrechen';

// Update Available
$lang['upd_available_head']    = 'System Aktualisierungen verfügbar';
$lang['upd_available_txt']     = 'Das System ist nicht mehr auf dem neusten Stand. Es wurden Updates gefunden.';
$lang['upd_available_link']    = 'Hier klicken, um die Updates anzuzeigen.';

$lang['menu_roster'] = 'Roster';

//Userinfos

$lang['adduser_first_name'] = 'Vorname';
$lang['adduser_last_name'] = 'Nachname';
$lang['adduser_addinfos'] = 'Profilinformationen';
$lang['adduser_country'] = 'Land';
$lang['adduser_town'] = 'Stadt';
$lang['adduser_state'] = 'Bundesland';
$lang['adduser_ZIP_code'] = 'PLZ';
$lang['adduser_phone'] = 'Festnetz Telefon';
$lang['adduser_cellphone'] = 'Handynummer';
$lang['adduser_foneinfo'] = 'Die Telefonnummern werden anonym gespeichert und sind nur den Admins sichtbar. Über die Handynummer könnt ihr euch z.b. anonym eine SMS senden lassen, wenn neue Termine anstehen, oder ein Raid abgesagt wurde.';
$lang['adduser_address'] = 'Anschrift (Straße und Hausnummer)';
$lang['adduser_allvatar_nick'] = 'Allvatar Nick';
$lang['adduser_icq'] = 'ICQ Nummer';
$lang['adduser_skype'] = 'Skype Nummer';
$lang['adduser_msn'] = 'MSN Account';
$lang['adduser_irq'] = 'IRC Server&Channel';
$lang['adduser_gender'] = 'Geschlecht';
$lang['adduser_birthday'] = 'Geburtstag';
$lang['adduser_gender_m'] = 'Männlich';
$lang['adduser_gender_f'] = 'Weiblich';
$lang['fv_required'] = 'Das Feld ist notwendig.';
$lang['lib_cache_notwriteable'] = 'In den Ordner "eqdkp/data" kann nicht geschrieben werden. Bitte gib ihm chmod 777!';
$lang['pcache_safemode_error']  = 'Safe Mode aktiv. Es können keine Daten geschrieben werden. EQDKP-PLUS funktioniert nicht im Safe Mode.';

// Ajax Image Uploader
$lang['aiupload_wrong_format']  = "Die Abmessungen des Bildes überschreiten die maximal <br/>zulässigen Werte (%1\$spx x %2\$spx). Bitte das Bild verkleinern.";
$lang['aiupload_wrong_type']    = 'Unzulässiger Dateityp! Es sind nur Bilddateien (*.jpg, *.gif, *.png) zugelassen.';
$lang['aiupload_upload_again']  = 'Re-Upload';

//Sticky news
$lang['sticky_news_prefix'] = 'Sticky:';
$lang['news_sticky'] = 'Immer oben anzeigen?';

$lang['menu_eqdkp'] = 'Menu';
$lang['menu_user'] = 'User-Menu';

//Usersettings
$lang['user_list'] = 'Userliste';
$lang['user_priv'] = 'Privacy Einstellungen';
$lang['user_priv_set_global'] = 'Wer soll die Profildaten wie Name, Skype, ICQ usw. einsehen können? ';
$lang['user_priv_set'] = 'Sichtbar für ';
$lang['user_priv_all'] = 'Alle';
$lang['user_priv_user'] = 'Angemeldete User';
$lang['user_priv_admin'] = 'Nur Admins';
$lang['user_priv_rl'] = 'Raidplaner Admins';
$lang['user_priv_no'] = 'Keiner';
$lang['user_priv_tel_all'] = 'Sollen die Telefonnummern allen angemeldeten Usern anstatt nur den Admins angezeigt werden? ';
$lang['user_priv_tel_cript'] = 'Sollen die Telefonnummern komplett versteckt werden, so das auch Admins diese nicht mehr sehen könenn? (SMS Versand weiter möglich)';
$lang['user_priv_tel_sms'] = 'SMS Empfang durch Admins komplett verhindern. (Es können dann keine Raideinladungen per SMS empfangen werden)';

// Image & BBCode Handling
$lang['images_not_available']	= 'Das eingebettete Bild ist zur Zeit leider nicht verfügbar.';
$lang['images_not_available_admin']	= '<b>Das eingebettete Bild konnte nicht überprüft werden</b><br/>Das kann folgende Gründe haben, bitte prüfe ob:<br/>- Dynamische Bilder sind aus Sicherheitsgründen deaktiviert<br/>- externe Verbindungen gesperrt: Versuche es mit Pfaden anstatt von URLs<br>- Bild nicht länger verfügbar';
$lang['images_userposted']		= 'Hochgeladenes Bild';

//SMS
$lang['sms_perm']	= 'SMS Service';
$lang['sms_perm2']	= 'SMS senden';
$lang['sms_header'] = 'SMS Versenden';
$lang['sms_info'] = 'Sende SMS an die User, z.b. wenn ein Raid abgesagt wurde, oder ihr kurzfristig einen Ersatz-Spieler braucht.';
$lang['sms_info_account'] = 'Ihr habt noch keinen SMS Account? Dann besorgt euch jetzt ein SMS Kontingent.';
$lang['sms_info_account_link'] = '<a href=http://www.eqdkp-plus.com target=_blank> --> Link</a>';
$lang['sms_send_info'] = 'Um SMS versenden zu können, muss mindestens ein User mit gültiger Handynummer ausgewählt und ein Text eingegeben werden.';
$lang['sms_success'] = 'SMS erfolgreich an den SMS-Server übertragen. Es kann einige Zeit dauern, bis SMS verschickt werden.';
$lang['sms_error'] = 'Fehler beim Senden der SMS. Unbekannter Fehler, fehlender Text, oder keine Empfänger angegeben.';
$lang['sms_error_badpw'] = 'Fehler beim Senden. Der Benutzername oder das Passwort stimmen nicht.';
$lang['sms_error_bad'] = 'Fehler beim Senden. Es befindet sich kein Guthaben mehr auf dem Account.';
$lang['sms_error_fopen'] = 'Fehler beim Senden. Der Server konnte keine fopen Verbindung zum SMS-Releay aufbauen. Entweder der SMS-Server ist nicht erreichbar, oder eurer Server lässt keine fopen Verbindungen zu. In einem solchen Fall, wendet euch bitte an euren Hoster/Admin. (und nicht an das EQdkpPlus Team/Forum)!!';
$lang['sms_error_159'] = 'Fehler beim Senden. Dienste-ID unbekannt.';
$lang['sms_error_160'] = 'Fehler beim Senden. Nachricht nicht gefunden!';
$lang['sms_error_200'] = 'Fehler beim Senden. Ausnahmefehler / XML Script unvollstaendig';
$lang['sms_error_254'] = 'Fehler beim Senden. Nachricht wurde geloescht!';

// Libraries
$lang = array_merge($lang, array(
  'cl_shortlangtag'           => 'de',

  // Update Check
  'cl_ucpdate_box'            => 'Neue Version verfügbar',
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
  'cl_upd_wversion'           => "Die vorhandene Datenbank ( Version %1\$s ) passt nicht zur installierten Plugin Version %2\$s.
                                  Bitte benutzen Sie den Update-Button um die Datenbank automatisch zu aktualisieren",
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
  'cl_upd_prev_version'       => 'Vorherige Version',

  // HTML Class
  'cl_on'                     => 'Ein',
  'cl_off'                    => 'Aus',

  // ReCaptcha Library
	'lib_captcha_head'					=> 'Bestätigungscode',
	'lib_captcha_insertword'		=> 'Gib die oben stehenden Wörter ein',
	'lib_captcha_insertnumbers' => 'Gib die gehörten Nummern ein',
	'lib_captcha_send'					=> 'Bestätigungscode absenden',

	'lib_starrating_cancel'			=> 'Bewertung abbrechen',

	// RSS Feeder
	'lib_rss_readmore'          => 'weiterlesen',
	'lib_rss_loading'           => 'Feed lädt ...',
	'lib_rss_error'             => 'Fehler beim Seitenaufruf',
));

#$lang['']    								  = '';
?>