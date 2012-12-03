<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* lang_admin.php
* Began: Fri January 3 2003
*
* $Id$
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

// Titles
$lang['addadj_title']         = 'Gruppenkorrektur zufügen';
$lang['addevent_title']       = 'Ereignis zufügen';
$lang['addiadj_title']        = 'Individuelle Korrektur zufügen';
$lang['additem_title']        = 'Itemkauf zufügen';
$lang['addmember_title']      = 'Gildenmitglied zufügen';
$lang['addnews_title']        = 'Newsbeitrag zufügen';
$lang['addraid_title']        = 'Raid zufügen';
$lang['addturnin_title']      = "Turn-in - Schritt %1\$d zufügen";
$lang['admin_index_title']    = 'EQdkp Administration';
$lang['config_title']         = 'Scripteinstellungen';
$lang['manage_members_title'] = 'Gildenmitglieder verwalten';
$lang['manage_users_title']   = 'Benutzer-Accounts und Berechtigungen';
$lang['parselog_title']       = 'Log File ansehen';
$lang['plugins_title']        = 'Plugins verwalten';
$lang['styles_title']         = 'Styles verwalten';
$lang['viewlogs_title']       = 'Log-Betrachter';

// Page Foot Counts
$lang['listusers_footcount']             = "... %1\$d Benutzer / %2\$d pro Seite gefunden";
$lang['manage_members_footcount']        = "... %1\$d Mitglied(er) gefunden";
$lang['online_footcount']                = "... %1\$d Benutzer sind online";
$lang['viewlogs_footcount']              = "... %1\$d Log(s) / %2\$d pro Seite gefunden";

// Submit Buttons
$lang['add_adjustment'] = 'Korrektur zufügen';
$lang['add_account'] = 'Account zufügen';
$lang['add_event'] = 'Ereignis zufügen';
$lang['add_item'] = 'Item zufügen';
$lang['add_member'] = 'Mitglied zufügen';
$lang['add_news'] = 'News zufügen';
$lang['add_raid'] = 'Raid zufügen';
$lang['add_style'] = 'Style zufügen';
$lang['add_turnin'] = 'Turn-in zufügen';
$lang['delete_adjustment'] = 'Korrektur löschen';
$lang['delete_event'] = 'Ereignis löschen';
$lang['delete_item'] = 'Item löschen';
$lang['delete_member'] = 'Mitglied löschen';
$lang['delete_news'] = 'News löschen';
$lang['delete_raid'] = 'Raid löschen';
$lang['delete_selected_members'] = 'Ausgewählte(s) Mitglied(er) löschen';
$lang['delete_style'] = 'Style löschen';
$lang['mass_delete'] = 'Massenlöschung';
$lang['mass_update'] = 'Massenupdate';
$lang['parse_log'] = 'Log ansehen';
$lang['search_existing'] = 'Durchsuchen';
$lang['select'] = 'Auswählen';
$lang['transfer_history'] = 'Transfer Historie';
$lang['update_adjustment'] = 'Korrektur aktualisieren';
$lang['update_event'] = 'Ereignis aktualisieren';
$lang['update_item'] = 'Item aktualisieren';
$lang['update_member'] = 'Mitglied aktualisieren';
$lang['update_news'] = 'News aktualisieren';
$lang['update_raid'] = 'Raid aktualisieren';
$lang['update_style'] = 'Style aktualisieren';

// Misc
$lang['account_enabled'] = 'Account aktiviert';
$lang['adjustment_value'] = 'Korrekturwert';
$lang['adjustment_value_note'] = 'kann negativ sein';
$lang['code'] = 'Code';
$lang['contact'] = 'Kontakt';
$lang['create'] = 'Erstellen';
$lang['found_members'] = "%1\$d Zeilen analysiert, %2\$d Mitglied(er) gefunden";
$lang['headline'] = 'Kopfzeile';
$lang['hide'] = 'Verstecken?';
$lang['install'] = 'Installieren';
$lang['item_search'] = 'Gegenstandssuche';
$lang['list_prefix'] = 'Prefix anzeigen';
$lang['list_suffix'] = 'Suffix anzeigen';
$lang['logs'] = 'Logs';
$lang['log_find_all'] = 'Finde alle (incl. Anonyme)';
$lang['manage_members'] = 'Mitglieder verwalten';
$lang['manage_plugins'] = 'Plugins verwalten';
$lang['manage_users'] = 'Benutzer verwalten';
$lang['mass_update_note'] = 'Wenn Du die Änderungen zu allen ausgewählten Accounts durchführen willst, benutze diese Einstellungen und klick auf "Massenupdate".
                            Um die ausgewählten Accounts zu löschen, klick auf "Massenlöschung".';
$lang['members'] = 'Mitglieder';
$lang['member_rank'] = 'Mitgliedsrang';
$lang['message_body'] = 'Nachrichtentext';
$lang['message_show_loot_raid'] = 'Zeige die Items vom Raid:';
$lang['results'] = "%1\$d Ergebnisse (\"%2\$s\")";
$lang['search'] = 'Suche';
$lang['search_members'] = 'Suche Mitglieder';
$lang['should_be'] = 'Sollte sein';
$lang['styles'] = 'Styles';
$lang['title'] = 'Titel';
$lang['uninstall'] = 'Uninstall';
$lang['enable']		= 'Enable';
$lang['update_date_to'] = "Aktualisiere Datum zu<br />%1\$s?";
$lang['version'] = 'Version';
$lang['x_members_s'] = "%1\$d Mitglied";
$lang['x_members_p'] = "%1\$d Mitglieder";

// Permission Messages
$lang['noauth_a_event_add']    = 'Du hast keine Berechtigung Ereignisse zuzufügen.';
$lang['noauth_a_event_upd']    = 'Du hast keine Berechtigung Ereignisse zu aktualisieren.';
$lang['noauth_a_event_del']    = 'Du hast keine Berechtigung Ereignisse zu löschen.';
$lang['noauth_a_groupadj_add'] = 'Du hast keine Berechtigung Gruppenkorrekturen zuzufügen.';
$lang['noauth_a_groupadj_upd'] = 'Du hast keine Berechtigung Gruppenkorrekturen zu aktualisieren.';
$lang['noauth_a_groupadj_del'] = 'Du hast keine Berechtigung Gruppenkorrekturen zu löschen.';
$lang['noauth_a_indivadj_add'] = 'Du hast keine Berechtigung individuelle Korrekturen zuzufügen.';
$lang['noauth_a_indivadj_upd'] = 'Du hast keine Berechtigung individuelle Korrekturen zu aktualisieren.';
$lang['noauth_a_indivadj_del'] = 'Du hast keine Berechtigung individuelle Korrekturen zu löschen.';
$lang['noauth_a_item_add']     = 'Du hast keine Berechtigung Items zuzufügen.';
$lang['noauth_a_item_upd']     = 'Du hast keine Berechtigung Items zu aktualisieren.';
$lang['noauth_a_item_del']     = 'Du hast keine Berechtigung Items zu löschen.';
$lang['noauth_a_news_add']     = 'Du hast keine Berechtigung News zuzufügen.';
$lang['noauth_a_news_upd']     = 'Du hast keine Berechtigung News zu aktualisieren.';
$lang['noauth_a_news_del']     = 'Du hast keine Berechtigung News zu löschen.';
$lang['noauth_a_raid_add']     = 'Du hast keine Berechtigung Raids zuzufügen.';
$lang['noauth_a_raid_upd']     = 'Du hast keine Berechtigung Raids zu aktualisieren.';
$lang['noauth_a_raid_del']     = 'Du hast keine Berechtigung Raids zu löschen.';
$lang['noauth_a_turnin_add']   = 'Du hast keine Berechtigung Turn-ins zuzufügen.';
$lang['noauth_a_config_man']   = 'Du hast keine Berechtigung EQdkp Einstellungen zu verwalten.';
$lang['noauth_a_members_man']  = 'Du hast keine Berechtigung Gildenmitglieder zu verwalten.';
$lang['noauth_a_plugins_man']  = 'Du hast keine Berechtigung EQdkp Plugins zu verwalten.';
$lang['noauth_a_styles_man']   = 'Du hast keine Berechtigung EQdkp Styles zu verwalten.';
$lang['noauth_a_users_man']    = 'Du hast keine Berechtigung Benutzer Accounteinstellungen zu verwalten.';
$lang['noauth_a_logs_view']    = 'Du hast keine Berechtigung EQdkp Logs anzusehen.';

// Submission Success Messages
$lang['admin_add_adj_success']               = "Eine %1\$s Korrektur von %2\$.2f wurde der Datenbank zugefügt.";
$lang['admin_add_admin_success']             = "Eine Mail wurde an %1\$s mit administrativen Informationen gesendet.";
$lang['admin_add_event_success']             = "Ein Standardwert von %1\$s für einen Raid auf %2\$s wurde der Datenbank zugefügt.";
$lang['admin_add_iadj_success']              = "Eine individuelle %1\$s Korrektur von %2\$.2f für %3\$s wurde der Datenbank zugefügt.";
$lang['admin_add_item_success']              = "Ein Itemkauf-Eintrag für %1\$s, gekauft von %2\$s für %3\$.2f wurde der Datenbank zugefügt.";
$lang['admin_add_member_success']            = "%1\$s wurde als Mitglied zugefügt.";
$lang['admin_add_news_success']              = 'Der Newsbeitrag wurde der Datenbank zugefügt.';
$lang['admin_add_raid_success']              = "Der %1\$d/%2\$d/%3\$d Raid auf %4\$s wurde der Datenbank zugefügt.";
$lang['admin_add_style_success']             = 'Der neue Style wurde erfolgreich zugefügt.';
$lang['admin_add_turnin_success']            = "%1\$s wurde verschoben von %2\$s nach %3\$s.";
$lang['admin_delete_adj_success']            = "Die %1\$s Korrektur von %2\$.2f wurde aus der Datenbank gelöscht.";
$lang['admin_delete_admins_success']         = "Die ausgewählten Admins wurden gelöscht.";
$lang['admin_delete_event_success']          = "Der Standardwert von %1\$s für einen Raid auf %2\$s wurde aus der Datenbank gelöscht.";
$lang['admin_delete_iadj_success']           = "Die individuelle %1\$s Korrektur von %2\$.2f für %3\$s wurde aus der Datenbank gelöscht.";
$lang['admin_delete_item_success']           = "Der Itemkauf-Eintrag für %1\$s, gekauft von %2\$s für %3\$.2f wurde aus der Datenbank gelöscht.";
$lang['admin_delete_members_success']        = "%1\$s, inklusive seiner/ihrer Historie, wurde aus der Datenbank gelöscht.";
$lang['admin_delete_news_success']           = 'Der Newsbeitrag wurde aus der Datenbank gelöscht.';
$lang['admin_delete_raid_success']           = 'Der Raid und alle damit verknüpften Items wurden aus der Datenbank gelöscht.';
$lang['admin_delete_style_success']          = 'Der Style wurde erfolgreich gelöscht.';
$lang['admin_delete_user_success']           = "Der Account mit dem Benutzernamen %1\$s wurde gelöscht.";
$lang['admin_set_perms_success']             = "Alle administrativen Berechtigungen wurden aktualisiert.";
$lang['admin_transfer_history_success']      = "Die komplette Historie von %1\$s\ wurde auf %2\$s übertragen und %1\$s wurde in der Datenbank gelöscht.";
$lang['admin_update_account_success']        = "Deine Accounteinstellungen wurden in der Datenbank aktualisiert.";
$lang['admin_update_adj_success']            = "Die %1\$s Korrektur von %2\$.2f wurde in der Datenbank aktualisiert.";
$lang['admin_update_event_success']          = "Der Standardwert von %1\$s für einen Raid auf %2\$s wurde in der Datenbank aktualisiert.";
$lang['admin_update_iadj_success']           = "Die individuelle %1\$s Korrektur von %2\$.2f für %3\$s wurde in der Datenbank aktualisiert.";
$lang['admin_update_item_success']           = "Der Itemkauf-Eintrag für %1\$s, gekauft von %2\$s für %3\$.2f wurde in der Datenbank aktualisiert.";
$lang['admin_update_member_success']         = "Mitgliedschaftseinstellungen für %1\$s wurden aktualisiert.";
$lang['admin_update_news_success']           = 'Der Newsbeitrag wurde in der Datenbank aktualisiert.';
$lang['admin_update_raid_success']           = "Der %1\$d/%2\$d/%3\$d Raid auf %4\$s wurde in der Datenbank aktualisiert.";
$lang['admin_update_style_success']          = 'Der Style wurde erfolgreich aktualisiert.';

$lang['admin_raid_success_hideinactive']     = 'Aktualisiere aktiv/inaktiv Spielerstatus...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Bist Du sicher, dass Du diese Gruppenkorrektur löschen willst?';
$lang['confirm_delete_admins']  = 'Bist Du sicher, dass Du die ausgewählten Admins löschen willst?';
$lang['confirm_delete_event']   = 'Bist Du sicher, dass Du dieses Ereignis löschen willst?';
$lang['confirm_delete_iadj']    = 'Bist Du sicher, dass Du diese individuelle Korrektur löschen willst?';
$lang['confirm_delete_item']    = 'Bist Du sicher, dass Du dieses Item löschen willst?';
$lang['confirm_delete_members'] = 'Bist Du sicher, dass Du die folgenden Mitglieder löschen willst?';
$lang['confirm_delete_news']    = 'Bist Du sicher, dass Du diesen Newsbeitrag löschen willst?';
$lang['confirm_delete_raid']    = 'Bist Du sicher, dass Du diesen Raid löschen willst?';
$lang['confirm_delete_style']   = 'Bist Du sicher, dass Du diesen Style löschen willst?';
$lang['confirm_delete_users']   = 'Bist Du sicher, dass Du die folgenden Benutzeraccounts löschen willst?';

// Log Actions
$lang['action_event_added']      = 'Ereignis zugefügt';
$lang['action_event_deleted']    = 'Ereignis gelöscht';
$lang['action_event_updated']    = 'Ereignis aktualisiert';
$lang['action_groupadj_added']   = 'Gruppenkorrektur zugefügt';
$lang['action_groupadj_deleted'] = 'Gruppenkorrektur gelöscht';
$lang['action_groupadj_updated'] = 'Gruppenkorrektur aktualisiert';
$lang['action_history_transfer'] = 'Mitglieder Historie verschoben';
$lang['action_indivadj_added']   = 'Individuelle Korrektur zugefügt';
$lang['action_indivadj_deleted'] = 'Individuelle Korrektur gelöscht';
$lang['action_indivadj_updated'] = 'Individuelle Korrektur aktualisiert';
$lang['action_item_added']       = 'Item zugefügt';
$lang['action_item_deleted']     = 'Item gelöscht';
$lang['action_item_updated']     = 'Item aktualisiert';
$lang['action_member_added']     = 'Mitglied zugefügt';
$lang['action_member_deleted']   = 'Mitglied gelöscht';
$lang['action_member_updated']   = 'Mitglied aktualisiert';
$lang['action_news_added']       = 'Newsbeitrag zugefügt';
$lang['action_news_deleted']     = 'Newsbeitrag gelöscht';
$lang['action_news_updated']     = 'Newsbeitrag aktualisiert';
$lang['action_raid_added']       = 'Raid zugefügt';
$lang['action_raid_deleted']     = 'Raid gelöscht';
$lang['action_raid_updated']     = 'Raid aktualisiert';
$lang['action_turnin_added']     = 'Turn-in zugefügt';

// Before/After
$lang['adjustment_after']  = 'Korrektur nachher';
$lang['adjustment_before'] = 'Korrektur vorher';
$lang['attendees_after']   = 'Teilnehmer nachher';
$lang['attendees_before']  = 'Teilnehmer vorher';
$lang['buyers_after']      = 'Käufer nachher';
$lang['buyers_before']     = 'Käufer vorher';
$lang['class_after']       = 'Klasse nachher';
$lang['class_before']      = 'Klasse vorher';
$lang['earned_after']      = 'Verdient nachher';
$lang['earned_before']     = 'Verdient vorher';
$lang['event_after']       = 'Ereignis nachher';
$lang['event_before']      = 'Ereignis vorher';
$lang['headline_after']    = 'Kopfzeile nachher';
$lang['headline_before']   = 'Kopfzeile vorher';
$lang['level_after']       = 'Level nachher';
$lang['level_before']      = 'Level vorher';
$lang['members_after']     = 'Mitglieder nachher';
$lang['members_before']    = 'Mitglieder vorher';
$lang['message_after']     = 'Nachricht nachher';
$lang['message_before']    = 'Nachricht vorher';
$lang['name_after']        = 'Name nachher';
$lang['name_before']       = 'Name vorher';
$lang['note_after']        = 'Notiz nachher';
$lang['note_before']       = 'Notiz vorher';
$lang['race_after']        = 'Rasse nachher';
$lang['race_before']       = 'Rasse vorher';
$lang['raid_id_after']     = 'Raid ID nachher';
$lang['raid_id_before']    = 'Raid ID vorher';
$lang['reason_after']      = 'Grund nachher';
$lang['reason_before']     = 'Grund vorher';
$lang['spent_after']       = 'Ausgegeben nachher';
$lang['spent_before']      = 'Ausgegeben vorher';
$lang['value_after']       = 'Wert nachher';
$lang['value_before']      = 'Wert vorher';

// Configuration
$lang['general_settings'] = 'Allgemeine Einstellungen';
$lang['guildtag'] = 'Gilden- / Gruppenname';
$lang['guildtag_note'] = 'Wird im Titel fast jeder Seite angezeigt';
$lang['parsetags'] = 'Gildenname zum Analysieren';
$lang['parsetags_note'] = 'Das hier Aufgeführte ist als Option verfügbar, wenn Raidlogs analysiert werden.';
$lang['domain_name'] = 'Domainname';
$lang['server_port'] = 'Serverport';
$lang['server_port_note'] = 'Port des Webservers, meist 80';
$lang['script_path'] = 'Skriptpfad';
$lang['script_path_note'] = 'Pfad zu EQdkp, relativ zum Domainname';
$lang['site_name'] = 'Seitenname';
$lang['site_description'] = 'Seitenbeschreibung';
$lang['point_name'] = 'Punktename';
$lang['point_name_note'] = 'z.B.: DKP, RP, etc.';
$lang['enable_account_activation'] = 'Accountaktivierung einschalten';
$lang['none'] = 'Kein';
$lang['admin'] = 'Admin';
$lang['default_language'] = 'Standardsprache';
$lang['default_locale'] = 'Standardregion (nur Zeichensatz; beeinflusst nicht die Sprache)';
$lang['default_game'] = 'Standardspiel';
$lang['default_game_warn'] = 'Änderung des Standardspiel kann andere Einstellungen dieser Sitzung ungültig machen.';
$lang['default_style'] = 'Standardstyle';
$lang['default_page'] = 'Standard Index Seite';
$lang['hide_inactive'] = 'Inaktive Mitglieder verstecken';
$lang['hide_inactive_note'] = 'Mitglieder verstecken, die an keinem Raid innerhalb von [inactive period] Tagen teilgenommen haben?';
$lang['inactive_period'] = 'Inaktiver Zeitraum';
$lang['inactive_period_note'] = 'Anzahl von Tagen, die ein Mitglied vom Raid fernbleiben kann und aktiv bleibt';
$lang['inactive_point_adj'] = 'Inaktive Punktekorrektur';
$lang['inactive_point_adj_note'] = 'Punktekorrektur eines Mitglieds wenn er inaktiv wird.';
$lang['active_point_adj'] = 'Aktive Punktekorrektur';
$lang['active_point_adj_note'] = 'Punktekorrektur eines Mitglieds wenn er aktiv wird.';
$lang['enable_gzip'] = 'Gzip-Kompression aktivieren';
$lang['show_item_stats'] = 'Zeige Itemeigenschaften';
$lang['show_item_stats_note'] = 'Versuche Itemeigenschaften aus dem Internet zu holen.  Kann die Geschwindigkeit des Seitenaufbaus beeinflussen.';
$lang['default_permissions'] = 'Standardberechtigungen';
$lang['default_permissions_note'] = 'Das sind die Berechtigungen für Benutzer, die nicht eingeloggt sind und wird neuen Benutzern bei der Registrierung gegeben. Punkte in <b>fett</b> sind administrative Berechtigungen,
                                    die man besser nicht als Standard setzen sollte. Punkte in <i>kursiv</i> sind nur für Plugins.  Du kannst später individuelle Benutzereinstellungen im Administrationsbereich vergeben.';
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'Das Pluginverzeichnis (./plugins/) ist leer.';
$lang['cookie_settings'] = 'Cookie Einstellungen';
$lang['cookie_domain'] = 'Cookie Domäne';
$lang['cookie_name'] = 'Cookie Name';
$lang['cookie_path'] = 'Cookie Pfad';
$lang['session_length'] = 'Sitzungslänge (Sekunden)';
$lang['email_settings'] = 'E-Mail-Einstellungen';
$lang['admin_email'] = 'Administrator E-Mail-Adresse';

// Admin Index
$lang['anonymous'] = 'Anonym';
$lang['database_size'] = 'Datenbankgröße';
$lang['eqdkp_started'] = 'EQdkp Start';
$lang['ip_address'] = 'IP Addresse';
$lang['items_per_day'] = 'Gegenstände pro Tag';
$lang['last_update'] = 'Letztes Update';
$lang['location'] = 'Ort';
$lang['new_version_notice'] = "EQdkp Version %1\$s ist <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">zum Download verfügbar</a>.";
$lang['number_of_items'] = 'Anzahl an Gegenständen';
$lang['number_of_logs'] = 'Anzahl an Logeinträgen';
$lang['number_of_members'] = 'Anzahl an Mitgliedern<br />(Aktiv / Inaktiv)';
$lang['number_of_raids'] = 'Anzahl an Raids';
$lang['raids_per_day'] = 'Raids pro Tag';
$lang['statistics'] = 'Statistiken';
$lang['totals'] = 'Gesamtsumme';
$lang['version_update'] = 'Versionsupdate';
$lang['who_online'] = 'Wer ist online?';

// Style Management
$lang['style_settings'] = 'Style Einstellungen';
$lang['style_name'] = 'Style Name';
$lang['template'] = 'Vorlage';
$lang['element'] = 'Element';
$lang['background_color'] = 'Hintergrundfarbe';
$lang['fontface1'] = 'Schriftart 1';
$lang['fontface1_note'] = 'Standard Schriftart';
$lang['fontface2'] = 'Schriftart 2';
$lang['fontface2_note'] = 'Schriftart Eingabefeld';
$lang['fontface3'] = 'Schriftart 3';
$lang['fontface3_note'] = 'Momentan ungenutzt';
$lang['fontsize1'] = 'Schriftgröße 1';
$lang['fontsize1_note'] = 'Klein';
$lang['fontsize2'] = 'Schriftgröße 2';
$lang['fontsize2_note'] = 'Mittel';
$lang['fontsize3'] = 'Schriftgröße 3';
$lang['fontsize3_note'] = 'Groß';
$lang['fontcolor1'] = 'Schriftfarbe 1';
$lang['fontcolor1_note'] = 'Standardfarbe';
$lang['fontcolor2'] = 'Schriftfarbe 2';
$lang['fontcolor2_note'] = 'Farbe außerhalb der Tabellen (Menüs, Titel, Copyright)';
$lang['fontcolor3'] = 'Schriftfarbe 3';
$lang['fontcolor3_note'] = 'Schriftfarbe Eingabefeld';
$lang['fontcolor_neg'] = 'Negative Schriftfarbe';
$lang['fontcolor_neg_note'] = 'Farbe für negative/falsche Zahlen';
$lang['fontcolor_pos'] = 'Positive Schriftfarbe';
$lang['fontcolor_pos_note'] = 'Farbe für positive/gute Zahlen';
$lang['body_link'] = 'Link Farbe';
$lang['body_link_style'] = 'Link Style';
$lang['body_hlink'] = 'Hover Link Color';
$lang['body_hlink_style'] = 'Hover Link Style';
$lang['header_link'] = 'Header Link';
$lang['header_link_style'] = 'Header Link Style';
$lang['header_hlink'] = 'Hover Header Link';
$lang['header_hlink_style'] = 'Hover Header Link Style';
$lang['tr_color1'] = 'Farbe Tabellenzeile 1';
$lang['tr_color2'] = 'Farbe Tabellenzeile 2';
$lang['th_color1'] = 'Farbe Tabellenüberschrift';
$lang['table_border_width'] = 'Breite Tabellenrahmen';
$lang['table_border_color'] = 'Farbe Tabellenrahmen';
$lang['table_border_style'] = 'Style Tabellenrahmen';
$lang['input_color'] = 'Hintergrundfarbe Eingabefeld';
$lang['input_border_width'] = 'Breite Eingabefeldrahmen';
$lang['input_border_color'] = 'Farbe Eingabefeldrahmen';
$lang['input_border_style'] = 'Style Eingabefeldrahmen';
$lang['style_configuration'] = 'Style Einstellungen';
$lang['style_date_note'] = 'Für Datum-/Zeitfelder wird die Syntax der PHP-Funktion <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> benutzt.';
$lang['attendees_columns'] = 'Teilnehmerspalten';
$lang['attendees_columns_note'] = 'Anzahl der Spalten für Teilnehmer, wenn man einen Raid ansieht.';
$lang['date_notime_long'] = 'Datum ohne Zeit (lang)';
$lang['date_notime_short'] = 'Datum ohne Zeit (kurz)';
$lang['date_time'] = 'Datum mit Zeit';
$lang['logo_path'] = 'Logo Dateiname';

// Errors
$lang['error_invalid_adjustment'] = 'Es wurde keine gültige Korrektur bereitgestellt.';
$lang['error_invalid_plugin']     = 'Es wurde kein gültiges Plugin bereitgestellt.';
$lang['error_invalid_style']      = 'Es wurde kein gültiger Style bereitgestellt.';

// Verbose log entry lines
$lang['new_actions']           = 'Neueste Adminaktionen';
$lang['vlog_event_added']      = "%1\$s fügte das Ereignis '%2\$s' zu, mit Wert von %3\$.2f Punkten.";
$lang['vlog_event_updated']    = "%1\$s aktualisierte das Ereignis '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s löschte das Ereignis '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s fügte eine Gruppenkorrektur von %2\$.2f Punkten zu.";
$lang['vlog_groupadj_updated'] = "%1\$s aktualisierte eine Gruppenkorrektur von %2\$.2f Punkten.";
$lang['vlog_groupadj_deleted'] = "%1\$s löschte eine Gruppenkorrektur von %2\$.2f Punkten.";
$lang['vlog_history_transfer'] = "%1\$s übertrug %2\$s's Historie zu %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s fügte eine individuelle Korrektur von %2\$.2f zu %3\$d Mitglied(ern) zu.";
$lang['vlog_indivadj_updated'] = "%1\$s aktualisierte eine individuelle Korrektur von %2\$.2f zu %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s löschte eine individuelle Korrektur von %2\$.2f zu %3\$s.";
$lang['vlog_item_added']       = "%1\$s fügte das Item '%2\$s' zugeordnet zu %3\$d Mitglied(er) für %4\$.2f Punkte ein.";
$lang['vlog_item_updated']     = "%1\$s aktualisierte das Item '%2\$s' zugeordnet zu %3\$d Mitglied(er).";
$lang['vlog_item_deleted']     = "%1\$s löschte das Item '%2\$s' zugeordnet zu %3\$d Mitglied(er).";
$lang['vlog_member_added']     = "%1\$s fügte das Mitglied %2\$s zu.";
$lang['vlog_member_updated']   = "%1\$s aktualisierte das Mitglied %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s löschte das Mitglied %2\$s.";
$lang['vlog_news_added']       = "%1\$s fügte den Newsbeitrag '%2\$s' zu.";
$lang['vlog_news_updated']     = "%1\$s aktualisierte den Newsbeitrag '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s löschte den Newsbeitrag '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s fügte einen Raid auf '%2\$s' zu.";
$lang['vlog_raid_updated']     = "%1\$s aktualisierte einen Raid auf '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s löschte einen Raid auf '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s fügte ein Turn-in von %2\$s zu %3\$s für '%4\$s' ein.";

// Location messages
$lang['adding_groupadj'] = 'Gruppenkorrektur zufügen';
$lang['adding_indivadj'] = 'Individuelle Korrektur zufügen';
$lang['adding_item'] = 'Item zufügen';
$lang['adding_news'] = 'Newsbeitrag zufügen';
$lang['adding_raid'] = 'Raid zufügen';
$lang['adding_turnin'] = 'Turn-in zufügen';
$lang['editing_groupadj'] = 'Gruppenkorrektur bearbeiten';
$lang['editing_indivadj'] = 'Individuelle Korrektur bearbeiten';
$lang['editing_item'] = 'Item bearbeiten';
$lang['editing_news'] = 'Newsbeitrag bearbeiten';
$lang['editing_raid'] = 'Raid bearbeiten';
$lang['listing_events'] = 'Ereignisse anzeigen';
$lang['listing_groupadj'] = 'Gruppenkorrekturen anzeigen';
$lang['listing_indivadj'] = 'Individuelle Korrekturen anzeigen';
$lang['listing_itemhist'] = 'Item Historie anzeigen';
$lang['listing_itemvals'] = 'Itemwerte anzeigen';
$lang['listing_members'] = 'Mitglieder anzeigen';
$lang['listing_raids'] = 'Raids anzeigen';
$lang['managing_config'] = 'EQdkp Einstellungen verwalten';
$lang['managing_members'] = 'Mitglieder verwalten';
$lang['managing_plugins'] = 'Plugins verwalten';
$lang['managing_styles'] = 'Styles verwalten';
$lang['managing_users'] = 'Benutzer-Accounts verwalten';
$lang['parsing_log'] = 'Log auswerten';
$lang['viewing_admin_index'] = 'Admin Index anzeigen';
$lang['viewing_event'] = 'Ereignis anzeigen';
$lang['viewing_item'] = 'Item anzeigen';
$lang['viewing_logs'] = 'Logs anzeigen';
$lang['viewing_member'] = 'Mitglieder anzeigen';
$lang['viewing_mysql_info'] = 'MySQL Informationen anzeigen';
$lang['viewing_news'] = 'News anzeigen';
$lang['viewing_raid'] = 'Raid anzeigen';
$lang['viewing_stats'] = 'Eigenschaften anzeigen';
$lang['viewing_summary'] = 'Zusammenfassung anzeigen';

// Help lines
$lang['b_help'] = 'fetter Text: [b]Text[/b] (shift+alt+b)';
$lang['i_help'] = 'kursiver Text: [i]Text[/i] (shift+alt+i)';
$lang['u_help'] = 'unterstrichener Text: [u]Text[/u] (shift+alt+u)';
$lang['q_help'] = 'Zitat: [quote]text[/quote] (shift+alt+q)';
$lang['c_help'] = 'zentrierter Text: [center]Text[/center] (shift+alt+c)';
$lang['p_help'] = 'Bild einfügen: [img]http://bild_url[/img] (shift+alt+p)';
$lang['w_help'] = 'URL einfügen: [url]http://URL[/url] oder [url=http://url]Text[/url] (shift+alt+w)';
$lang['it_help'] = 'Item einfügen: z.b. [item]Angelstuhl[/item] (shift+alt+t)';
$lang['ii_help'] = 'ItemIcon einfügen: z.b. [itemicon]Arkanitangelrute[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Neues Mitglied zufügen';
$lang['list_edit_del_member'] = 'Mitglieder anzeigen, bearbeiten oder löschen';
$lang['edit_ranks'] = 'Mitgliederränge bearbeiten';
$lang['transfer_history'] = 'Mitglieder Historie verschieben';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'MySQL Informationen';
$lang['eqdkp_tables'] = 'EQdkp Tabellen';
$lang['table_name'] = 'Tabellen Name';
$lang['rows'] = 'Zeilen';
$lang['table_size'] = 'Tabellengröße';
$lang['index_size'] = 'Indexgröße';
$lang['num_tables'] = "%d Tabellen";

//Backup
$lang['backup'] = 'Sicherung';
$lang['backup_title'] = 'Erstelle eine Datenbanksicherung';
$lang['create_table'] = '\'CREATE TABLE\' zufügen?';
$lang['skip_nonessential'] = 'Überspringe unwichtige Daten?<br />Wird keine Inserts für die eqdkp_sessions anlegen.';
$lang['gzip_content'] = 'GZIP Inhalt?<br />Wird eine kleinere Datei erzeugen, wenn GZIP eingeschaltet ist.';
$lang['backup_database'] = 'Datenbank sichern';

// plus
$lang['in_database']  = 'In Datenbank gespeichert';

//Log Users Actions
$lang['action_user_added']     = 'Benutzer zugefügt';
$lang['action_user_deleted']   = 'Benutzer gelöscht';
$lang['action_user_updated']   = 'Benutzer aktualisiert';

$lang['vlog_user_added']     = "%1\$s fügte den Benutzer %2\$s zu.";
$lang['vlog_user_updated']   = "%1\$s aktualisierte Benutzer %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s löschte den Benutzer %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP Konto zugefügt";
$lang['action_multidkp_deleted']   = "MultiDKP Konto gelöscht";
$lang['action_multidkp_updated']   = "MultiDKP Konto aktualisiert";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s fügte das MultiDKP Konto %2\$s zu.";
$lang['vlog_multidkp_updated']   = "%1\$s aktualisierte das MultiDKP Konto %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s löschte MultiDKP Konto %2\$s.";

$lang['default_style_overwrite']   = "Usereinstellungen überschreiben (Alle User benutzen den Standardstyle)";
$lang['class_colors']              = "Klassenfarben";

#Plugin 
$lang['description'] = 'Beschreibung';
$lang['manual'] = 'Anleitung';
$lang['homepage'] = 'Webseite';
$lang['readme'] = 'Lies mich';
$lang['link'] = 'Link';
$lang['infos'] = 'Infos';


?>
