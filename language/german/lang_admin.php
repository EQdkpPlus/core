<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Titles
//$lang['addadj_title']         = 'Gruppenkorrektur zufügen';
$lang['addevent_title']       = 'Ereignis zufügen';
//$lang['addiadj_title']        = 'Individuelle Korrektur zufügen';
//$lang['additem_title']        = 'Itemkauf zufügen';
//$lang['addmember_title']      = 'Gildenmitglied zufügen';
$lang['addnews_title']        = 'Newsbeitrag zufügen';
$lang['addraid_title']        = 'Raid zufügen';
//$lang['addturnin_title']      = "Turn-in - Schritt %1\$d zufügen";
$lang['admin_index_title']    = 'EQdkp Administration';
$lang['config_title']         = 'Konfiguration';
$lang['manage_members_title'] = 'Gildencharaktere verwalten';
$lang['manage_users_title']   = 'Benutzer-Accounts und Berechtigungen';
$lang['manadjs_title']		  = 'Korrekturen verwalten';
$lang['manevents_title']	  = 'Ereignisse verwalten';
$lang['manitempools_title']	  = 'Item-Pools verwalten';
$lang['mantasks_title']			= 'Aufgaben verwalten';
$lang['manitems_title']		  = 'Items verwalten';
$lang['manmdkp_title']		  = 'Multidkp-Konten verwalten';
$lang['manraid_title']		  = 'Raids verwalten';
$lang['manrank_title']		  = 'Ränge verwalten';
$lang['uc_last_updated']			= 'Letzte Aktualisierung';
$lang['uc_never_updated']			= 'Nie aktualisiert';
$lang['uc_bttn_update']      		= 'Aktualisieren';
$lang['uc_bttn_import']      		= 'Importieren';
//$lang['parselog_title']       = 'Log File ansehen';
$lang['plugins_title']        = 'Plugins verwalten';
$lang['styles_title']         = 'Styles verwalten';
$lang['updraid_title'] 		  = 'Raid aktualisieren';
$lang['viewlogs_title']       = 'Log-Betrachter';
$lang['clear_all_logs']       = 'Alle Logs löschen';
$lang['clear_last_logs']      = 'Logs älter als';
$lang['filter_plugins']      = 'Nach Plugin filtern';
$lang['filter_type']      			= 'Nach Typ filtern';
$lang['days']       					= 'Tage';

// Page Foot Counts
$lang['listusers_footcount']			= "... %1\$d Benutzer / %2\$d pro Seite gefunden";
$lang['manage_members_footcount']		= "... %1\$d Charakter(e) gefunden";
$lang['online_footcount']				= "... %1\$d Benutzer sind online";
$lang['viewlogs_footcount']				= "... %1\$d Log(s) / %2\$d pro Seite gefunden";
$lang['footcount_entries']              = "... zeige %d Einträge";
$lang['footcount_entries_of']			= "... zeige %d von %d Einträgen";
$lang['itempools_footcount']			= "... %1\$d Item-Pool(s) gefunden";

// Submit Buttons
$lang['add_aadjustment']				= 'eine Korrektur hinzufügen';
$lang['add_adjustment']					= 'Korrektur zufügen';
//$lang['add_account'] = 'Account zufügen';
$lang['add_aitem'] = 'ein Item hinzufügen';
$lang['add_event'] = 'Ereignis zufügen';
$lang['add_item'] = 'Item zufügen';
$lang['add_itempool'] = 'Item-Pool hinzufügen';
$lang['add_member'] = 'Charakter zufügen';
$lang['add_raid'] = 'Raid zufügen';
$lang['add_style'] = 'Style zufügen';
//$lang['add_turnin'] = 'Turn-in zufügen';
$lang['delete'] = 'Löschen';
$lang['delete_adjustment'] = 'Korrektur löschen';
$lang['delete_event'] = 'Ereignis löschen';
$lang['delete_item'] = 'Item löschen';
$lang['delete_member'] = 'Charakter löschen';
$lang['delete_news'] = 'News löschen';
$lang['delete_raid'] = 'Raid löschen';
$lang['delete_selected'] = 'Ausgewählte Löschen';
$lang['delete_selected_adjs'] = 'Ausgewählte(s) Korrektur(en) löschen';
$lang['delete_selected_items'] = 'Ausgewählte(s) Item(s) löschen';
$lang['delete_selected_members'] = 'Ausgewählte Charaktere löschen';
$lang['delete_style'] = 'Style löschen';
$lang['doit'] = 'durchführen';
$lang['mass_delete'] = 'Massenlöschung';
$lang['mass_rank_change'] = 'Rang bei ausgewählten Charakteren ändern zu:';
$lang['mass_stat_change'] = 'Status bei ausgewählten Charakteren ändern';
$lang['mass_update'] = 'Massenupdate';
$lang['parse_log'] = 'Log ansehen';
$lang['save'] = 'Speichern';
$lang['search_existing'] = 'Durchsuchen';
$lang['select'] = 'Auswählen';
$lang['transfer_history'] = 'Transfer Historie';
$lang['update_adjustment'] = 'Korrektur aktualisieren';
$lang['update_event'] = 'Ereignis aktualisieren';
$lang['update_item'] = 'Item aktualisieren';
$lang['update_member'] = 'Charakter aktualisieren';
$lang['update_news'] = 'News aktualisieren';
$lang['update_raid'] = 'Raid aktualisieren';
$lang['update_style'] = 'Style aktualisieren';
$lang['delete_user'] = 'Benutzer löschen';
$lang['activate_user'] = 'Benutzer aktivieren';


// Misc
$lang['activate_list'] = 'Inaktive Benutzer';
$lang['activate_all'] = 'Alle Benutzer aktivieren';
$lang['activate_all_warning'] = 'Sollen wirklich alle Benutzer aktiviert werden?';
$lang['user_activate_success'] = 'Der Benutzer <i>%s</i> wurde erfolgreich aktiviert.';
$lang['user_deactivate_success'] = 'Der Benutzer <i>%s</i> wurde erfolgreich deaktiviert.';

$lang['admin_action']	= 'Admin-Aktion';
$lang['account_enabled'] = 'Account aktiviert';
$lang['member_active'] = 'Aktiv?';
$lang['adjitem_del'] = 'Markierte Korrekturen und Items löschen';
$lang['adjustment_value'] = 'Korrekturwert';
$lang['adjustment_value_note'] = 'kann negativ sein';
$lang['adjustments'] = 'Korrekturen';
$lang['code'] = 'Code';
$lang['contact'] = 'Kontakt';
//$lang['create'] = 'Erstellen';
$lang['del_nosuc'] = 'Löschen nicht erfolgreich';
$lang['del_raid_with_itemadj'] = 'Soll der Raid mit allen Items und Korrekturen gelöscht werden?';
$lang['del_suc'] = 'Löschen erfolgreich';
//$lang['enable']     = 'Enable';
$lang['found_members'] = "%1\$d Zeilen analysiert, %2\$d Charakter(e) gefunden";
$lang['headline'] = 'Überschrift';
$lang['hide'] = 'Verstecken?';
$lang['install'] = 'Installieren';
$lang['item_name'] = 'Itemname';
$lang['item_id'] = 'Game-Item ID';
//$lang['item_search'] = 'Gegenstandssuche';
$lang['list_prefix'] = 'Prefix anzeigen';
$lang['list_suffix'] = 'Suffix anzeigen';
$lang['logs'] = 'Logs';
//$lang['log_find_all'] = 'Finde alle (incl. Anonyme)';
$lang['manage_members'] = 'Charaktere verwalten';
$lang['manage_plugins'] = 'Plugins verwalten';
$lang['manage_raids'] = 'Raids verwalten';
$lang['manage_user'] = 'Benutzer "%s" verwalten';

$lang['mass_update_note'] = 'Wenn Du die Änderungen zu allen ausgewählten Accounts durchführen willst, benutze diese Einstellungen und klick auf "Massenupdate".
                            Um die ausgewählten Accounts zu löschen, klick auf "Massenlöschung".';
$lang['member_history'] = 'Charakter-Historie transferieren zu';
$lang['member_rank'] = 'Charakterrang';
//News
$lang['message_body'] = 'Nachrichtentext';
$lang['message_extended'] = 'Erweiterter Nachrichtentext';
$lang['message_show_loot_raid'] = 'Items von Raids hinzufügen';
$lang['message_select_raids'] = 'Raids auswählen';
//BB-Code
$lang['explanation'] = 'Erklärung';
$lang['bbcode'] = 'BB-Codes';
$lang['bbcode_note'] = 'Hier findest du zusätzliche BB-Codes. Bei Klick auf einen BB-Code wird er in das zuletzt bearbeitete Feld eingefügt.';
$lang['bbcode_item'] = '[item]NAME[/item]';
$lang['bbcode_item_note'] = 'Trage zwischen die Tags den Names eines Items ein, um es auf der Newsseite anzuzeigen.';
$lang['bbcode_video'] = '[video]URL[/video]';
$lang['bbcode_video_note'] = 'Trage zwischen die Video-Tags den Link zu einem Video einer gängigen Videoplattform ein, z.B. Youtube, Dailymotion, MyVideo...';


$lang['select_raid_draft'] = 'Raid als Vorlage auswählen';
$lang['member_raidcount'] = 'Raidzähler-Offset';
$lang['results'] = "%1\$d Ergebnisse (\"%2\$s\")";
$lang['search'] = 'Suche';
$lang['search_members'] = 'Suche Charaktere';
$lang['selected_ranks'] = 'Ausgewählte(n) Ränge/Rang';
$lang['del_selected_ranks'] = 'Ausgewählte Ränge löschen';
$lang['styles'] = 'Styles';
$lang['title'] = 'Titel';
$lang['browser'] = 'Browser';
$lang['uninstall'] = 'Deinstallieren';
$lang['update_date_to'] = "Auf aktuelles Datum setzen";
$lang['show_news_to'] = "News anzeigen bis:";
$lang['show_news_from'] = "News anzeigen ab:";
$lang['news_date'] = "Datum der News:";
$lang['show_news_from_help'] = "Lasse dieses Feld leer, um die News ab sofort anzuzeigen.";
$lang['show_news_to_help'] = "Lasse dieses Feld leer, um die News unbegrenzt anzuzeigen.";
$lang['show_from'] = "Angezeigt von";
$lang['show_to'] = "Angezeigt bis";

$lang['version'] = 'Version';
$lang['x_members_s'] = "%1\$d Charakter";
$lang['x_members_p'] = "%1\$d Charaktere";

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
//$lang['noauth_a_turnin_add']   = 'Du hast keine Berechtigung Turn-ins zuzufügen.';
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
$lang['admin_add_member_success']            = "%1\$s wurde als Charakter zugefügt.";
$lang['admin_add_news_success']              = 'Der Newsbeitrag wurde der Datenbank zugefügt.';
$lang['admin_add_raid_success']              = "Der %1\$d/%2\$d/%3\$d Raid auf %4\$s wurde der Datenbank zugefügt.";
$lang['admin_add_style_success']             = 'Der neue Style wurde erfolgreich zugefügt.';
//$lang['admin_add_turnin_success']            = "%1\$s wurde verschoben von %2\$s nach %3\$s.";
$lang['admin_delete_adj_success']            = "Die %1\$s Korrektur von %2\$.2f wurde aus der Datenbank gelöscht.";
$lang['admin_delete_admins_success']         = "Die ausgewählten Admins wurden gelöscht.";
$lang['admin_delete_event_success']          = "Der Standardwert von %1\$s für einen Raid auf %2\$s wurde aus der Datenbank gelöscht.";
$lang['admin_delete_iadj_success']           = "Die individuelle %1\$s Korrektur von %2\$.2f für %3\$s wurde aus der Datenbank gelöscht.";
$lang['admin_delete_item_success']           = "Der Itemkauf-Eintrag für %1\$s, gekauft von %2\$s für %3\$.2f wurde aus der Datenbank gelöscht.";
$lang['admin_delete_members_success']        = "%1\$s, inklusive seiner/ihrer Historie, wurde aus der Datenbank gelöscht.";
$lang['admin_delete_news_success']           = 'Der Newsbeitrag wurde aus der Datenbank gelöscht.';
$lang['admin_delete_raid_success']           = 'Der Raid und alle damit verknüpften Items wurden aus der Datenbank gelöscht.';
$lang['admin_delete_style_success']          = 'Der Style wurde erfolgreich gelöscht.';
$lang['admin_delete_style_error_defaultstyle'] = 'Du kannst den Standard-Style nicht löschen. Wähle in den Einstellungen einen neuen Standard-Style aus und wiederhole den Löschvorgang.';
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
$lang['admin_add_newscats_success']          = "Die Kategorien wurden erfolgreich bearbeitet.";
$lang['admin_raid_success_hideinactive']     = 'Aktualisiere aktiv/inaktiv Spielerstatus...';
$lang['admin_delete_infopages_success']       = "Die ausgewählten Seiten wurden erfolgreich gelöscht.";
$lang['admin_save_infopages_success']       	= "Die Seite <em>%s</em> wurde erfolgreich gespeichert";
$lang['admin_update_infopages_success']       = "Die Änderungen wurden erfolgreich gespeichert.";

$lang['mem_history_trans'] = "Charakter-Historie wurde von %1\$s nach %2\$s verschoben.";
$lang['mems_del'] = 'Folgende Charaktere wurden gelöscht: ';
$lang['mems_no_del'] = 'Folgende Charaktere konnten nicht gelöscht werden: ';
$lang['mems_no_rank_change'] = 'Der Rang folgender Charaktere konnte nicht geändert werden: ';
$lang['mems_no_status_change'] = 'Der Status folgender Charaktere konnte nicht geändert werden: ';
$lang['mems_rank_change'] = 'Der Rang folender Charaktere wurde erfolgreich geändert: ';
$lang['mems_status_change'] = 'Der Status folgender Charaktere wurde erfolgreich geändert: ';
$lang['missing_values'] = 'Es fehlen folgende Eingaben: ';
$lang['no_mem_history'] = 'Charakter-Historie konnte nicht verschoben werden.';
$lang['no_ranks_selected'] = 'Keine Ränge ausgewählt.';
$lang['no_groups_selected'] = 'Keine Benutzergruppen ausgewählt.';
$lang['no_del_default_itempool'] = 'Der Itempool mit der ID 1, darf nicht gelöscht werden, da Items, deren Itempool gelöscht wird, diesem zugeordnet werden.';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Bist Du sicher, dass Du diese Gruppenkorrektur löschen willst?';
$lang['confirm_delete_admins']  = 'Bist Du sicher, dass Du die ausgewählten Admins löschen willst?';
$lang['confirm_delete_event']   = '<b>Bist Du sicher, dass Du dieses Ereignis löschen willst?</b><br />%s<br /><br /><b><span class="negative">Achtung:</span> wenn du dieses Event löscht, werden alle Korrekturen und %d Raids die zu diesem Event zugeordnet sind ebenfalls gelöscht!</b>';


$lang['confirm_delete_iadj']    = 'Bist Du sicher, dass Du diese individuelle Korrektur löschen willst?';
$lang['confirm_delete_item']    = 'Bist Du sicher, dass Du dieses Item löschen willst?';
$lang['confirm_delete_members'] = 'Bist Du sicher, dass Du die folgenden Charaktere löschen willst?';
$lang['confirm_delete_news']    = 'Bist Du sicher, dass Du diesen Newsbeitrag löschen willst?';
$lang['confirm_delete_raid']    = 'Bist Du sicher, dass Du diesen Raid löschen willst?';
$lang['confirm_delete_style']   = '<b>Bist Du sicher, dass Du diesen Style löschen willst?</b><br/>Wenn die Template-Dateien dieses Styles gelöscht werden, werden alle anderen Styles, die diesen als Vorlage haben, nicht mehr funktionieren!';
$lang['confirm_delete_user']   = 'Bist Du sicher, dass Du diesen Benutzeraccount löschen willst? Alle verknüpften Mitglieder werden dabei auch aus deinem System gelöscht!';
$lang['confirm_delete_users']   = '<b>Bist Du sicher, dass Du die folgenden Benutzeraccounts löschen willst?</b>';
$lang['confirm_delete_ranks'] 	= 'Sollen folgende Ränge gelöscht werden?';
$lang['confirm_delete_multi']	= 'Bist Du sicher, dass Du dieses Konto löschen willst?';
$lang['confirm_delete_itempools'] = 'Bist Du sicher, dass Du diesen Item-Pool löschen willst?';
$lang['confirm_delete_logs']   = 'Möchstest du wirklich alle Logs löschen?';
$lang['confirm_delete_backup']   = 'Möchstest du dieses Backup wirklich löschen?';
$lang['confirm_restore_backup']   = 'Möchstest du dieses Backup wirklich einspielen? Dieser Vorgang überschreibt alle vorhandenen Daten! Beim Einspielen eines nicht kompatiblen Backups kann die Datenbank deines EQdkps zerstört werden!<br /><br /><label><input type="checkbox" onclick="restore_data(this.checked)" value="1" checked="checked"> Wichtige Einstellungen, die im data-Ordner gespeichert sind, wiederherstellen? (nur für zip-Archive)</label>';

// Log Actions
$lang['action_event_added']      = 'Ereignis zugefügt';
$lang['action_event_deleted']    = 'Ereignis gelöscht';
$lang['action_event_updated']    = 'Ereignis aktualisiert';
$lang['action_groupadj_added']   = 'Gruppenkorrektur zugefügt';
$lang['action_groupadj_deleted'] = 'Gruppenkorrektur gelöscht';
$lang['action_groupadj_updated'] = 'Gruppenkorrektur aktualisiert';
$lang['action_history_transfer'] = 'Charakter-Historie verschoben';
$lang['action_indivadj_added']   = 'Individuelle Korrektur zugefügt';
$lang['action_indivadj_deleted'] = 'Individuelle Korrektur gelöscht';
$lang['action_indivadj_updated'] = 'Individuelle Korrektur aktualisiert';
$lang['action_item_added']       = 'Item zugefügt';
$lang['action_item_deleted']     = 'Item gelöscht';
$lang['action_item_updated']     = 'Item aktualisiert';
$lang['action_member_added']     = 'Charakter zugefügt';
$lang['action_member_deleted']   = 'Charakter gelöscht';
$lang['action_member_updated']   = 'Charakter aktualisiert';
$lang['action_news_added']       = 'Newsbeitrag zugefügt';
$lang['action_news_deleted']     = 'Newsbeitrag gelöscht';
$lang['action_news_updated']     = 'Newsbeitrag aktualisiert';
$lang['action_raid_added']       = 'Raid zugefügt';
$lang['action_raid_deleted']     = 'Raid gelöscht';
$lang['action_raid_updated']     = 'Raid aktualisiert';
//$lang['action_turnin_added']     = 'Turn-in zugefügt';
$lang['action_old_logs_deleted'] = 'Ältere Logs gelöscht';
$lang['action_logs_deleted'] 		 = 'Logs gelehrt';

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
$lang['members_after']     = 'Charaktere nachher';
$lang['members_before']    = 'Charaktere vorher';
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
//$lang['general_settings'] = 'Allgemeine Einstellungen';
$lang['default_settings'] = 'Standard-/Gästeeinstellungen';
$lang['guildtag'] = 'Gilden- / Gruppenname';
$lang['guildtag_note'] = 'Wird im Titel fast jeder Seite angezeigt';
$lang['parsetags'] = 'Gildenname zum Analysieren';
$lang['parsetags_note'] = 'Das hier Aufgeführte ist als Option verfügbar, wenn Raidlogs analysiert werden.';
$lang['script_path'] = 'Skriptpfad';
$lang['script_path_note'] = 'Pfad zu EQdkp, relativ zum Domainname';
$lang['site_name'] = 'Seitenname';
$lang['site_description'] = 'Seitenbeschreibung';
$lang['point_name'] = 'Punktename';
$lang['point_name_note'] = 'Die Abkürzung des Punktenamens (z.B. DKP, RP, etc.)';
$lang['enable_account_activation'] = 'Accountaktivierung einschalten';
$lang['disable_account_registration'] = 'Benutzer-Registrierung deaktvieren';
$lang['disable_account_registration_note'] = 'Es können sich keine neuen Benutzer registrieren.';
$lang['user_settings'] = 'Benutzer-Einstellungen';
$lang['none'] = 'Kein';
$lang['admin'] = 'Administrator';
$lang['default_locale'] = 'Standardregion (nur Zeichensatz; beeinflusst nicht die Sprache)';
$lang['game_settings_head'] = 'Spieleinstellungen';
//$lang['default_game_warn'] = 'Änderung des Standardspiel kann andere Einstellungen dieser Sitzung ungültig machen.';
$lang['default_style'] = 'Standardstyle';
$lang['default_page'] = 'Standard Index Seite';
$lang['hide_inactive'] = 'Inaktive Charaktere verstecken';
$lang['hide_inactive_note'] = 'Charaktere verstecken, die an keinem Raid innerhalb von [inaktiver Zeitraum] Tagen teilgenommen haben?';
$lang['inactive_period'] = 'Inaktiver Zeitraum';
$lang['inactive_period_note'] = 'Anzahl von Tagen, die ein Charakter vom Raid fernbleiben kann und aktiv bleibt';
$lang['inactive_point_adj'] = 'Inaktive Punktekorrektur';
$lang['inactive_point_adj_note'] = 'Punktekorrektur eines Charakters wenn er inaktiv wird.';
$lang['active_point_adj'] = 'Aktive Punktekorrektur';
$lang['active_point_adj_note'] = 'Punktekorrektur eines Charakters wenn er aktiv wird.';
$lang['enable_gzip'] = 'Gzip-Kompression aktivieren';
//$lang['show_item_stats'] = 'Zeige Itemeigenschaften';
//$lang['show_item_stats_note'] = 'Versuche Itemeigenschaften aus dem Internet zu holen.  Kann die Geschwindigkeit des Seitenaufbaus beeinflussen.';
/*$lang['default_permissions'] = 'Standardberechtigungen';
$lang['default_permissions_note'] = 'Das sind die Berechtigungen für Benutzer, die nicht eingeloggt sind und wird neuen Benutzern bei der Registrierung gegeben. Punkte in <b>fett</b> sind administrative Berechtigungen,
                                    die man besser nicht als Standard setzen sollte. Punkte in <i>kursiv</i> sind nur für Plugins.  Du kannst später individuelle Benutzereinstellungen im Administrationsbereich vergeben.';*/
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'Das Pluginverzeichnis (./plugins/) ist leer.';
$lang['cookie_settings'] = 'Cookie Einstellungen';
$lang['cookie_domain'] = 'Cookie Domäne';
$lang['cookie_name'] = 'Cookie Name';
$lang['cookie_path'] = 'Cookie Pfad';
$lang['session_length'] = 'Sitzungslänge (Sekunden)';
$lang['admin_email'] = 'Administrator E-Mail-Adresse';
$lang['enable_newscategories'] = 'Newskategorien aktivieren';
$lang['enable_newscategories_help'] = 'Verwende Newskategorien um die News auf der Homepage kategoriesieren zu können.';
//$lang['newscategories'] = 'Newskategorien';
$lang['select_newscategories'] = 'Newskategorie auswählen';
$lang['manage_newscategories'] = 'Newskategorien verwalten';
$lang['add_newscategorie'] = 'Kategorie hinzufügen';
$lang['icon'] = 'Icon';
$lang['color'] = 'Schriftfarbe';
//Captcha
$lang['enable_captcha'] = 'Bestätigungscode für Registrierung aktivieren';
$lang['enable_captcha_help'] = 'Benutze einen CAPTCHA Code für die Registrierung.';
$lang['timezone'] = 'Standardzeitzone';
$lang['timezone_note'] = 'Wähle die Standardzeitzone, die für neue Registrationen und Gäste verwendet werden soll.';

// Admin Index
$lang['database_size'] = 'Datenbankgröße';
$lang['eqdkp_started'] = 'EQdkp Start';
$lang['ip_address'] = 'IP Addresse';
$lang['items_per_day'] = 'Gegenstände pro Tag';
$lang['last_update'] = 'Letztes Update';
$lang['location'] = 'Ort';
//$lang['new_version_notice'] = "EQdkp Version %1\$s ist <a href=\"http://www.eqdkp-plus.com\" target=\"_blank\">zum Download verfügbar</a>.";
$lang['number_of_items'] = 'Anzahl an Gegenständen';
$lang['number_of_logs'] = 'Anzahl an Logeinträgen';
$lang['number_of_members'] = 'Anzahl an Charakteren<br />(Aktiv / Inaktiv)';
$lang['number_of_raids'] = 'Anzahl an Raids';
$lang['raids_per_day'] = 'Raids pro Tag';
$lang['statistics'] = 'Statistiken';
$lang['totals'] = 'Gesamtsumme';
//$lang['version_update'] = 'Versionsupdate';
$lang['who_online'] = 'Wer ist online?';

// Style Management
$lang['style_settings'] = 'Style Einstellungen';
$lang['style_name'] = 'Style Name';
$lang['style_code'] = 'Style-Code';
$lang['template'] = 'Vorlage';
$lang['template_files'] = 'Template-Dateien:';
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
//$lang['date_notime_long'] = 'Datum ohne Zeit (lang)';
//$lang['date_notime_short'] = 'Datum ohne Zeit (kurz)';
//$lang['date_time'] = 'Datum mit Zeit';
$lang['logo_path'] = 'Logo Dateiname';
$lang['logo_path_note'] = 'Wählt entweder einen Dateinamen wenn sich das Bild in dem /templates/template/images/ Ordner befindet, oder gebt den gesamten Pfad zu einem Bild im Internet an, dann aber unbedingt mit http:// eingeben.)';
//$lang['logo_path_config'] = 'Wählt eine Datei von eurer Festplatte aus und ladet eurer neues Logo hier hoch welches für alle Templates gültig ist. (Überschreibt alle Template Einstellungen.) Nach dem Auswählen des Logos müsst ihr das Formular noch speichern!';
$lang['install_templates'] = 'Styles installieren';
$lang['delete_template_cache'] = 'Template-Cache leeren';
$lang['activate'] = 'Aktivieren';
$lang['deactivate'] = 'Deaktivieren';
$lang['edit_style'] = 'Style Bearbeiten';
$lang['download_style'] = 'Style herunterladen';
$lang['delete_style'] = 'Style löschen';
$lang['style_disabled_info'] = 'Style kann von Benutzern nicht verwendet werden';
$lang['style_enabled_info'] = 'Style kann von Benutzern verwendet werden';
$lang['style_default_info'] = 'Möchtest Du du den Style der Benutzer mit dem neuen Standardstyle überschreiben?';
$lang['color_settings'] = 'Farbeinstellungen';
$lang['edit_templates'] = 'Templates bearbeiten';
$lang['template_warning'] = 'Achtung: Fehlerhafte Änderungen können dazu führen, dass dein EQdkp-System nicht mehr läuft! Sollte dies der Fall sein, entferne die geänderten Template-Dateien aus dem Ordner %s und leere den Cache-Ordner im data-Ordner.';
$lang['make_default_style'] = 'Zum Standardstyle machen';
$lang['select_template'] = 'Template auswählen';
$lang['frontend'] = 'BENUTZER-BEREICH:';
$lang['backend'] = 'ADMIN-BEREICH:';
$lang['no_styles_to_install'] = 'Keine Styles zum Installieren gefunden. Um Styles zu installieren, kopiere das Verzeichniss  des Styles nach /templates/.';
$lang['template_not_exists_warning'] = 'Die Template-Dateien, auf denen dieser Style aufbaut, sind nicht mehr vorhanden. Lösche diesen Style oder wähle andere Template-Dateien aus.';

$lang['edit_template_suc']		= 'Die Template-Datei wurde erfolgreich bearbeitet.';
$lang['edit_template_nosuc']	= 'Die Template-Datei konnte nicht bearbeitet werden.';
$lang['enable_style_suc']			= 'Der Style %s wurde aktiviert.';
$lang['disable_style_suc']		= 'Der Style %s wurde deaktiviert.';
$lang['default_style_suc']		= 'Der Style %s wurde erfolgreich als neuer Standardstyle gespeichert.';
$lang['install_style_suc']		= 'Der Style %s wurde erfolgreich installiert.';
$lang['install_style_nosuc']	= 'Der Style %s konnte nicht installiert werden, da er schon existiert.';
$lang['create_style_nosuc']		= 'Ein Style mit diesem Namen existiert bereits. Bitte verwende einen anderen Namen.';
$lang['update_style_suc']		= 'Der Style %s wurde erfolgreich aktualisiert.';
$lang['eq_style_install'] 		= 'Neuen Style Erstellen';
$lang['style_update_warning'] 		= '<b>Folgende Styles benötigen ein Update:</b><br/>';
$lang['style_update_versions'] 		= '(Benötigt Update von %1$s auf %2$s)';
$lang['style_updates_available']	= '<img src="'.$eqdkp_root_path.'images/error.png" height="18"> Updates verfügbar!</b>';

$lang['style_updates_info']	= 'Für folgende Styles sind Updates verfügbar. Klicke einen Style an und wähle die entsprechenden Update-Optionen aus. Bitte beachte: Updates können nicht rückgängig gemacht werden, also lege vor einem Update ein Backup der Styles an (exportieren).';
$lang['style_update_selection1'] = 'Bitte wähle die gewünschten Update-Vorgehensweisen aus (vorausgewählte Optionen entsprechen dem besten Update-Vorgang):';
$lang['style_update_selection2'] = '<b>Überschreiben:</b> überschreibt die in der Datenbank vorhandenen Farbeinstellungen, gemachte Änderungen gehen verloren';
$lang['style_update_selection3'] = '<b>Nicht überschreiben:</b> Aktualisiert nur die Versionsnummer';
$lang['style_update_selection4'] = '<b>Template-Dateien:</b> du hast Änderungen an %1$d Dateien vorgenommen (<b><a href="#" onClick="%2$s">Dateien anzeigen</a></b>)';
$lang['style_update_selection5'] = '<b>Überschreiben:</b> überschreibe alle Template-Dateien, gemachte Änderungen gehen verloren';
$lang['style_update_selection6'] = '<b>Nicht Überschreiben:</b> gemachte Änderungen bleiben erhalten, allerdings sind die Dateien dann nicht auf dem aktuellsten Stand!';
$lang['style_update_selection7'] = '<b>Bearbeitete Template-Dateien:</b>';

// Errors
$lang['error_invalid_adjustment'] = 'Es wurde keine gültige Korrektur bereitgestellt.';
$lang['error_invalid_plugin']     = 'Es wurde kein gültiges Plugin bereitgestellt.';
$lang['error_invalid_style']      = 'Es wurde kein gültiger Style bereitgestellt.';

// Verbose log entry lines
$lang['new_actions']           = 'Neueste Adminaktionen';
$lang['view_all_actions']      = 'Alle Logs ansehen';
$lang['vlog_event_added']      = "%1\$s fügte das Ereignis '%2\$s' zu, mit Wert von %3\$.2f Punkten.";
$lang['vlog_event_updated']    = "%1\$s aktualisierte das Ereignis '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s löschte das Ereignis '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s fügte eine Gruppenkorrektur von %2\$.2f Punkten zu.";
$lang['vlog_groupadj_updated'] = "%1\$s aktualisierte eine Gruppenkorrektur von %2\$.2f Punkten.";
$lang['vlog_groupadj_deleted'] = "%1\$s löschte eine Gruppenkorrektur von %2\$.2f Punkten.";
$lang['vlog_history_transfer'] = "%1\$s übertrug %2\$s's Historie zu %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s fügte eine individuelle Korrektur von %2\$.2f zu %3\$d Charakter(en) zu.";
$lang['vlog_indivadj_updated'] = "%1\$s aktualisierte eine individuelle Korrektur von %2\$.2f zu %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s löschte eine individuelle Korrektur von %2\$.2f zu %3\$s.";
$lang['vlog_item_added']       = "%1\$s fügte das Item '%2\$s' zugeordnet zu %3\$d Charakter(en) für %4\$.2f Punkte ein.";
$lang['vlog_item_updated']     = "%1\$s aktualisierte das Item '%2\$s' zugeordnet zu %3\$d Charakter(en).";
$lang['vlog_item_deleted']     = "%1\$s löschte das Item '%2\$s' zugeordnet zu %3\$d Charakter(en).";
$lang['vlog_member_added']     = "%1\$s fügte den Charakter %2\$s zu.";
$lang['vlog_member_updated']   = "%1\$s aktualisierte den Charakter %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s löschte den Charakter %2\$s.";
$lang['vlog_news_added']       = "%1\$s fügte den Newsbeitrag '%2\$s' zu.";
$lang['vlog_news_updated']     = "%1\$s aktualisierte den Newsbeitrag '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s löschte den Newsbeitrag '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s fügte einen Raid auf '%2\$s' zu.";
$lang['vlog_raid_updated']     = "%1\$s aktualisierte einen Raid auf '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s löschte einen Raid auf '%2\$s'.";
//$lang['vlog_turnin_added']     = "%1\$s fügte ein Turn-in von %2\$s zu %3\$s für '%4\$s' ein.";
$lang['vlog_logs_deleted']     = "%1\$s leerte die Logs.";

// Location messages
$lang['adding_groupadj'] = 'Gruppenkorrektur zufügen';
$lang['adding_indivadj'] = 'Individuelle Korrektur zufügen';
$lang['adding_item'] = 'Item zufügen';
$lang['adding_news'] = 'Newsbeitrag zufügen';
$lang['adding_raid'] = 'Raid zufügen';
//$lang['adding_turnin'] = 'Turn-in zufügen';
$lang['editing_adj'] = 'Korrektur bearbeiten';
$lang['editing_groupadj'] = 'Gruppenkorrektur bearbeiten';
$lang['editing_indivadj'] = 'Individuelle Korrektur bearbeiten';
$lang['editing_item'] = 'Item bearbeiten';
//$lang['editing_news'] = 'Newsbeitrag bearbeiten';
$lang['editing_raid'] = 'Raid bearbeiten';
$lang['listing_events'] = 'Ereignisse anzeigen';
$lang['listing_groupadj'] = 'Gruppenkorrekturen anzeigen';
$lang['listing_indivadj'] = 'Individuelle Korrekturen anzeigen';
$lang['listing_itemhist'] = 'Item Historie anzeigen';
$lang['listing_itemvals'] = 'Itemwerte anzeigen';
$lang['listing_members'] = 'Charaktere anzeigen';
$lang['listing_raids'] = 'Raids anzeigen';
$lang['managing_config'] = 'EQdkp Einstellungen verwalten';
$lang['managing_members'] = 'Charaktere verwalten';
$lang['managing_plugins'] = 'Plugins verwalten';
$lang['managing_styles'] = 'Styles verwalten';
$lang['managing_users'] = 'Benutzer-Accounts verwalten';
$lang['parsing_log'] = 'Log auswerten';
$lang['viewing_admin_index'] = 'Admin Index anzeigen';
$lang['viewing_event'] = 'Ereignis anzeigen';
$lang['viewing_item'] = 'Item anzeigen';
$lang['viewing_logs'] = 'Logs anzeigen';
$lang['viewing_member'] = 'Charaktere anzeigen';
$lang['viewing_mysql_info'] = 'MySQL Informationen anzeigen';
$lang['viewing_news'] = 'News anzeigen';
$lang['viewing_raid'] = 'Raid anzeigen';
$lang['viewing_stats'] = 'Eigenschaften anzeigen';
//$lang['viewing_summary'] = 'Zusammenfassung anzeigen';
$lang['viewing_exchange'] = 'Externe Anwendung';

/* Help lines
$lang['b_help'] = 'fetter Text: [b]Text[/b] (shift+alt+b)';
$lang['i_help'] = 'kursiver Text: [i]Text[/i] (shift+alt+i)';
$lang['u_help'] = 'unterstrichener Text: [u]Text[/u] (shift+alt+u)';
$lang['q_help'] = 'Zitat: [quote]text[/quote] (shift+alt+q)';
$lang['c_help'] = 'zentrierter Text: [center]Text[/center] (shift+alt+c)';
$lang['p_help'] = 'Bild einfügen: [img]http://bild_url[/img] (shift+alt+p)';
$lang['w_help'] = 'URL einfügen: [url]http://URL[/url] oder [url=http://url]Text[/url] (shift+alt+w)';
$lang['it_help'] = 'Item einfügen: z.b. [item]Angelstuhl[/item] (shift+alt+t)';
$lang['ii_help'] = 'ItemIcon einfügen: z.b. [itemicon]Arkanitangelrute[/itemicon] (shift+alt+o)';*/

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Neuen Charakter zufügen';
//$lang['list_edit_del_member'] = 'Charaktere anzeigen, bearbeiten oder löschen';
$lang['edit_ranks'] = 'Charakterränge bearbeiten';
$lang['transfer_history'] = 'Charakter-Historie verschieben';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'Datenbank-Informationen';
$lang['eqdkp_tables'] = 'EQdkp Tabellen';
$lang['table_name'] = 'Tabellenname';
$lang['rows'] = 'Zeilen';
$lang['table_size'] = 'Tabellengröße';
$lang['index_size'] = 'Indexgröße';
$lang['num_tables'] = "%d Tabellen";
$lang['optimize'] = "Tabellen optimieren";
$lang['repair_tables'] = "Tabellen reparieren";
$lang['db_type'] = "Datenbank-Typ";
$lang['db_name'] = "Datenbank-Name";
$lang['db_prefix'] = "Tabellen-Prefix";
$lang['db_version'] = "Datenbank-Version";
$lang['db_engine'] = "Engine";
$lang['db_collation'] = "Collation";

//Backup
$lang['backup']            = 'Sicherung';
$lang['backup_database']   = 'Datenbank sichern';
$lang['backup_title']      = 'Erstelle eine Datenbanksicherung';
$lang['backup_type']       = 'Backup Format';
$lang['backup_system']     = 'Daten auswählen, die gesichert werden sollen';
$lang['backup_system_db']  = 'Datenbank';
$lang['backup_system_data']= 'data-Ordner';
$lang['recommended']= 'Empfohlen';
$lang['create_table']      = '\'CREATE TABLE\' Befehle hinzufügen?';
$lang['skip_nonessential'] = 'Überspringe unwichtige Daten?<br />Wird keine Inserts für die eqdkp_sessions anlegen.';
//$lang['gzip_content']      = 'GZIP Inhalt?<br />Wird eine kleinere Datei erzeugen, wenn GZIP eingeschaltet ist.';
$lang['select_tables']   = 'Tabellen auswählen';
$lang['tables']   		 = 'Tabellen';
$lang['table_prefix']    = 'Tabellen-Prefix';
$lang['backup_no_table_prefix']    = '<strong>ACHTUNG:</strong> Deine EQdkp-Installation hat kein Prefix für die Datenbank-Tabellen. Alle Tabellen für Plugins, die du installiert hast, können nicht mitgesichert werden.';
$lang['backup_action']    = 'Vorgang';
$lang['backup_action_download']    = 'Herunterladen';
$lang['backup_action_store']    = 'Auf Server speichern';
$lang['backup_action_both']    = 'Speichern & Herunterladen';
$lang['backup_restore']    = 'Sicherung wiederherstellen';
$lang['backup_restore_button']    = 'Wiederherstellung starten';
$lang['backup_restore_info']    = 'Hiermit wird eine vollständige Wiederherstellung aller EQdkp-Tabellen aus einer gespeicherten Datei durchgeführt. Sofern von deinem Server unterstützt, kann auch eine komprimierte gzip-Datei verwendet werden, die dann automatisch dekomprimiert wird. <br><strong>ACHTUNG:</strong> Dieser Vorgang überschreibt alle vorhandenen Daten. Die Wiederherstellung kann einige Zeit in Anspruch nehmen, bitte wechsle nicht auf eine andere Seite, bis der Vorgang abgeschlossen ist. EQdkp geht davon aus, dass die Backups im Backup-Ordner mit der EQdkp-eigenen Backup-Funktion erstellt wurden. Die Wiederherstellung anders erstellter Backups kann, muss aber nicht funktionieren.';
$lang['backup_uncomplete_info'] = '* = Mit * markierte Backups enthalten nur ausgewählte Tabellen und sind deshalb keine kompletten Sicherungen.';
$lang['backup_select']    = 'Backup auswählen';
$lang['backup_delete']    = 'Backup löschen';
$lang['backup_download']    = 'Backup herunterladen';
$lang['backup_no_files']    = '<em>Es sind keine Backups vorhanden</em>';
$lang['backup_delete_success']    = 'Das ausgewählte Backup wurde erfolgreich gelöscht.';
$lang['backup_store_success']    = 'Das Backup wurde erfolgreich auf dem Server gespeichert.';
$lang['backup_restore_success']    = 'Das Backup vom %s wurde erfolgreich wiederhergestellt.';
$lang['no_metadata']            = 'Es konnten keine Informationen für dieses Backup gefunden werden!';
$lang['metadata']            = 'Backup-Informationen';
//$lang['in_database']  = 'In Datenbank gespeichert';

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

$lang['default_style_overwrite']   = "Automatische Benutzung des Standard-Styles (Benutzereinstellung wird ignoriert)";
$lang['class_colors']              = "Klassenfarben";

#Plugins
$lang['description'] = 'Beschreibung';
$lang['manual'] = 'Anleitung';
$lang['homepage'] = 'Webseite';
//$lang['readme'] = 'Lies mich';
$lang['link'] = 'Link';
$lang['infos'] = 'Infos';

// Plugin Install / Uninstall
$lang['plugin_inst_success']  = 'Erfolgreich';
$lang['plugin_inst_error']  = 'Error';
$lang['plugin_inst_message']  = "Das Plugin <i>%1\$s</i> wurde erfolgreich %2\$s.";
$lang['plugin_inst_installed'] = 'installiert';
$lang['plugin_inst_uninstalled'] = 'deinstalliert';
$lang['plugin_inst_errormsg1'] = "Errors were detected during the %1\$s process: %2\$s";
$lang['plugin_inst_errormsg2']  = "%1\$s may not have %2\$s correctly.";

$lang['background_image'] = 'Hintergrund Bild (sollte mind. 1000x1000px groß sein) optional';
$lang['css_file'] = 'CSS Datei - optional da nicht von jedem Style verwendet. Ignoriert die eingestellten Werte auf dieser Seite!!!';

$lang['plugin_inst_sql_note'] = 'Ein SQL Fehler während der Plugininstallation bedeutet nicht zwingend, dass das Plugin defekt ist. Versuche das Plugin zu benutzen, sollten Fehler auftreten, so ist eine De- und Reinstallation meist hilfreich.';

/* Plugin Update Warn Class
$lang['puc_perform_intro']          = 'Folgende Plugins benötigen noch ein Update der Datenstruktur. Bitte klicke den "beheben" Link um die Datenbankupdates der jeweiligen Plugins zu installieren.<br/>Folgende Plugintabellen sind betroffen:';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Benötigt Datenbankupdate von %2\$s auf %3\$s)";
$lang['puc_solve_dbissues']         = 'beheben';
$lang['puc_unknown']                = '[Unbekannt]';*/

//Plus Data Cache
$lang['pdc_manager'] = 'Cache Manager';
$lang['pdc_status'] = 'Status';
$lang['pdc_entity'] = 'Datensatz';
$lang['pdc_entity_valid'] = 'Gültig';
$lang['pdc_entity_expired'] = 'Abgelaufen';
$lang['pdc_size'] = 'Größe';
$lang['pdc_clear'] = 'Leeren';
$lang['pdc_cleanup'] = 'Aufräumen';
$lang['pdc_save'] = "Speichern";
$lang['pdc_settings'] = "Cache Einstellungen";
$lang['pdc_table'] = "Cache Einträge";
$lang['pdc_cache_select_text'] = "Cache Auswahl";
$lang['pdc_cache_select_info'] = "Wählen sie das System aus, dass für das Caching verwendet werden soll.";
$lang['pdc_dttl_text'] = "Setzen der Standard-TTL [s]";
$lang['pdc_dttl_help'] = "Standard Zeit nach der Cache Einträge als abgelaufen gelten, insofern für den Eintrag keine eigene TTL gesetzt wurde.";
$lang['pdc_table_info'] = "Aktuell wird als Caching System \"%s\" verwendet und die folgenden Einträge sind vorhanden:";
$lang['pdc_cache_name_none'] = "Kein Cache";
$lang['pdc_cache_name_file'] = "Datei Cache";
$lang['pdc_cache_name_xcache'] = "XCache";
$lang['pdc_cache_name_apc'] = "Alternativer PHP Cache (APC)";
$lang['pdc_cache_name_memcache'] = "Memcache";
$lang['pdc_globalprefix'] = "Glob. Prefix";
$lang['pdc_globalprefix_help'] = "Glob. Prefix das automatisch vor alle Cache Einträge gesetzt wird.";
$lang['pdc_memcache_server_text'] = "Memcache Server Adresse";
$lang['pdc_memcache_server_help'] = "Adresse des Memcache Servers (z.B. 127.0.0.1 für den localhost)";
$lang['pdc_memcache_port_text'] = "Memcache Server Port";
$lang['pdc_memcache_port_help'] = "Port auf dem der Memcache Server erreichbar ist (Standard: 11211)";
$lang['pdc_cache_info_none'] = "Cache deaktiviert! Diese Einstellung ist nur für den Entwicklungsprozess empfohlen. Vom Einsatz im live-Betrieb ist aufgrund eines massiven Geschwindigkeitsverlustes abzuraten.";
$lang['pdc_cache_info_file'] = "eqDKP Plus eigener Datei Cache. Standard für alle Systeme, auf denen kein anderes Caching System verfügbar ist oder genutzt werden soll.";
$lang['pdc_cache_info_xcache'] = "XCache Caching System. Siehe <a href=\"http://xcache.lighttpd.net\">hier</a> für mehr Informationen.";
$lang['pdc_cache_info_apc'] = "Alternativer PHP Cache (APC). Siehe <a href=\"http://de.php.net/apc\">hier</a> für mehr Informationen.";
$lang['pdc_cache_info_memcache'] = "Memcache. Siehe <a href=\"http://de.php.net/memcache\">hier</a> für mehr Informationen.";


//Alt Manager
$lang['alt_manager'] = 'Twink Manager';
//$lang['alt_main_id'] = 'Main ID';
$lang['alt_message_title'] = 'Hinweis:';
$lang['alt_update_successful'] = 'Twinks erfolgreich aktualisiert.';
$lang['alt_update_unsuccessful'] = 'Twinks konnten nicht aktualisiert werden, siehe Fehler!';
$lang['alt_main_is_alt'] = 'Ausgewählter Main für %s ist selbst ein Twink, nur Mains können als Main für andere ausgewählt werden.';

//---- Main ----
$lang['pluskernel']          	= 'EQDKP-PLUS Core';
//$lang['pk_adminmenu']         	= 'PLUS Config';
//$lang['pk_settings']			= 'Einstellungen';
//$lang['pk_date_settings']		= 'd.m.y';

//---- Javascript stuff ----
//$lang['updates']				= 'Verfügbare Updates';
//$lang['loading']				= 'Lädt...';
//$lang['pk_config_header']		= 'EQDKP PLUS Einstellungen';
//$lang['pk_close_jswin1']     	= 'Schließe das';
//$lang['pk_close_jswin2']      	= 'Fenster bevor Du es erneut öffnest!';
//$lang['pk_help_header']		= 'Hilfe';
//$lang['pk_plus_comments']  	= 'Kommentare';

/* ---- Updater Stuff ---- NOT USED ANY LONGER
$lang['pk_alt_attention']			= 'Achtung';
$lang['pk_alt_ok']					= 'Alles OK!';
$lang['pk_updates_avail']			= 'Updates verfügbar';
$lang['pk_updates_navail']			= 'Keine Updates verfügbar';
$lang['pk_no_updates']				= 'Keine Updates verfügbar. Deine Installation ist auf dem neuesten Stand.';
$lang['pk_act_version']			= 'Aktuell';
$lang['pk_inst_version']			= 'Installiert';
$lang['pk_changelog']				= 'Changelog';
$lang['pk_download']				= 'Download';
$lang['pk_upd_information']		= 'Information';
$lang['pk_enabled']				= 'eingeschaltet';
$lang['pk_disabled']				= 'ausgeschaltet';
$lang['pk_auto_updates1']			= 'Die automatische Anzeige der Updates ist';
$lang['pk_auto_updates2']			= 'Falls dies beabsichtigt ist, überprüfe die Aktualität der Plugins und des EQDKP PLUS bitte von Zeit zu Zeit per Hand.';
$lang['pk_module_name']			= 'Modulname';
$lang['pk_plugin_level']			= 'Level';
$lang['pk_release_date']			= 'Release';
$lang['pk_alt_error']				= 'Fehler';
$lang['pk_no_conn_header']			= 'Verbindungsfehler';
$lang['pk_no_server_conn']			= 'Beim Versuch, den Updateserver zu kontaktieren, trat ein Fehler auf.
																 	Entweder erlaubt Dein Host keine ausgehenden Verbindungen, oder es
																 	bestehen Netzwerkprobleme. Bitte besuche das EQdkp Forum um
																 	sicherzustellen, dass Du die neueste Version am Laufen hast.';
$lang['pk_reset_warning']			= 'Warnung zurücksetzen';

//---- Update Levels ----
$lang['pk_level_other']				= 'andere';
$updatelevel = array (
	'Bugfix'								=> 'Bugfix',
	'Feature Release'						=> 'Zukünfiges Release',
	'Security Update'						=> 'Sicherheitsupdate',
	'New version'							=> 'Neue Version',
	'Release Candidate'						=> 'Release Candidate',
	'Public Beta'							=> 'Öffentliche Beta',
	'Closed Beta'							=> 'Geschlossene Beta',
	'Alpha'									=> 'Alpha',
);*/

//---- Settings ----
$lang['pk_save_title']					= 'Einstellungen gespeichert';
$lang['pk_succ_saved']					= 'Die Einstellungen wurden erfolgreich gespeichert';
 // Tabs
$lang['pk_tab_global']					= 'Global';
//$lang['pk_tab_multidkp']				= 'MultiDKP';
$lang['pk_tab_links']					= 'Links';
//$lang['pk_tab_bosscount']				= 'BossCounter';
//$lang['pk_tab_listmemb']				= 'Listmembers';
$lang['pk_tab_itemstats']				= 'Itemstats';
// Global
//$lang['pk_set_QuickDKP']				= 'Zeige QuickDKP';
//$lang['pk_set_Bossloot']				= 'Bossloot anzeigen (Nur aktivieren, wenn für jeden Boss ein einzelner Raid angetragen wird)';
$lang['pk_set_ClassColor']				= 'Farbige Klassennamen';
$lang['pk_set_Updatecheck']				= 'Automatische Warnung bei Updates anzeigen';
$lang['pk_hide_shop']					= 'Shop-Link für Gäste einblenden';
$lang['pk_hide_shop_note']				= 'Blendet den Shop für Gäste ein. Benutzer können diese Einstellung in ihrem Profil verändern.';
// MultiDKP
//$lang['pk_set_multidkp']				= 'MultiDKP einschalten';
// Listmembers /Showmembers
//$lang['pk_set_leaderboard']			= 'Zeige Leaderboard';
//$lang['pk_set_lb_solo']				= 'Zeige Leaderboard pro MultiDKP Konto';
//$lang['pk_set_rank']					= 'Zeige Rang';
//$lang['pk_set_rank_icon']				= 'Zeige Rang Icon';
//$lang['pk_set_level']					= 'Zeige Level';
//$lang['pk_set_lastloot']				= 'Zeige letzten Loot';
//$lang['pk_set_lastraid']				= 'Zeige letzten Raid';
//$lang['pk_set_attendance30']			= 'Zeige Raidbeteiligung der letzten 30 Tage';
//$lang['pk_set_attendance60']			= 'Zeige Raidbeteiligung der letzten 60 Tage';
//$lang['pk_set_attendance90']			= 'Zeige Raidbeteiligung der letzten 90 Tage';
//$lang['pk_set_attendanceAll']			= 'Zeige Raidbeteiligung seit Beginn';
// Links
$lang['pk_set_links']					= 'Linkmenü anzeigen';
$lang['pk_set_linkurl']				= 'URL';
$lang['pk_set_linkname']				= 'Name des Links';
//$lang['pk_set_newwindow']				= 'Neues Fenster?';
// BossCounter
//$lang['pk_set_bosscounter']			= 'Zeige BossCounter';
/*Itemstats
$lang['pk_set_itemstats']				= 'Itemstats einschalten';
$lang['pk_is_language']				= 'Itemstats Sprache';
$lang['pk_german']						=	'Deutsch';
$lang['pk_english']					= 'English';
$lang['pk_french']						= 'French';
$lang['pk_set_icon_ext']				= 'Dateierweiterung der Bilder.';
$lang['pk_set_icon_loc']				= 'URL zu den Itemstats Bildern.';*/
$lang['pk_itt_database_specific']		= 'Item-Datenbank spezifische Einstellungen';

$lang['pk_set_email_header'] = "E-Mail";
$lang['pk_set_recaptcha_header'] = "ReCaptcha";

$lang['lib_email_sender_name'] = 'Name des Absenders';
$lang['lib_email_sender_name_help'] = 'Name des Absenders bei E-Mails';
$lang['lib_email_sendmail_path'] = 'Sendmail-Pfad';
$lang['lib_email_sendmail_path_help'] = 'Der Pfad zu Sendmail auf dem Server. Zu erfahren aus den FAQ des Webhosters.';
$lang['lib_email_method'] = 'Mailer';
$lang['lib_email_method_help'] = 'Wähle den passenden Mailer aus. Dein Server muss die Mailmethode unterstützen.';
$lang['lib_email_mail'] = 'PHP-Mail-Funktion';
$lang['lib_email_sendmail'] = 'Sendmail';
$lang['lib_email_smtp'] = 'SMTP-Server';
$lang['lib_email_smtp_user'] = 'SMTP-Benutzer';
$lang['lib_email_smtp_user_help'] = 'Der Benutzername des SMTP Server';
$lang['lib_email_smtp_password'] = 'SMTP-Passwort';
$lang['lib_email_smtp_password_help'] = 'Das Passwort des SMTP Servers';
$lang['lib_email_smtp_host'] = 'SMTP-Host';
$lang['lib_email_smtp_host_help'] = 'Der Host des SMTP Servers';
$lang['lib_email_smtp_auth'] = 'SMTP-Authentifizierung';
$lang['lib_email_smtp_auth_help'] = 'Benötigt der SMTP Server eine Authentifizierung?';
$lang['lib_email_signature'] = 'Signatur anhängen';
$lang['lib_email_signature_help'] = 'Sollen den E-Mails eine Signatur angehängt werden?';
$lang['lib_email_signature_value'] = 'Signatur';
$lang['lib_email_signature_value_help'] = 'Die Signatur für die E-Mails die über das EQDKP-PLUS versendet werden.';

$lang['lib_recaptcha_okey'] = 'Öffentlicher Key von reCATPCHA';
$lang['lib_recaptcha_okey_help']	= 'Trage hier den öffentlichen Key deines Account auf reCAPTCHA.net ein.';
$lang['lib_recaptcha_pkey'] = 'Privater Key von reCATPCHA';
$lang['lib_recaptcha_pkey_help']	= 'Trage hier den privaten Key deines Account auf reCAPTCHA.net ein.';
$lang['pk_set_sms_tab']	= 'SMS';
$lang['pk_set_sms_header']			= 'SMS Einstellungen';
$lang['pk_set_sms_info_temp']		= 'Um SMS versenden zu können benötigt ihr Zugangsdaten. <br>Solltet ihr noch keine Logindaten haben, so können diese unter folgendem Link erworben werden:<br>' ;
$lang['pk_set_sms_username']		= 'Benutzername';
$lang['pk_set_sms_username_help']	= 'Der Benutzername des Allvatar SMS Accounts. Andere SMS Anbieter werden zur Zeit nicht unterstüzt';
$lang['pk_set_sms_pass']			= 'Passwort';
$lang['pk_set_sms_pass_help']		= 'Das Passwort des Allvatar SMS Accounts.';
//$lang['pk_set_sms_amount']			= 'Anzahl SMS die noch versendet werden können?';
$lang['pk_set_sms_deactivate']		= 'SMS Funktionen einschalten';
$lang['pk_set_sms_deactivate_help']		= 'Aktiviere die SMS Funktionen des EQDKP-PLUS. Es können dann Bestätigungen und andere Sachen per SMS an die Benutzer gesendet werden.';
$lang['sms_info_account_link']		= '<a href="http://www.allvatar.com/index.php?p=sms" target="_blank">http://www.allvatar.com/index.php?p=sms</a>';


################
# new sort
###############

//MultiDKP
//

//$lang['pk_set_multi_Tooltip']						= 'DKP Tooltip anzeigen';
//$lang['pk_set_multi_smartTooltip']			= 'Smart Tooltip';

//Help
$lang['pk_help_colorclassnames']				= "Wenn aktiviert, werden die Spieler in den Farben ihrer Klassen und mit ihrem Klassenicon dargestellt.";
//$lang['pk_help_quickdkp']						= "Zeigt dem eingeloggten Benutzer oberhalb des Menüs die Punkte aller Charaktere, die ihm zugeordnet sind.";
//$lang['pk_help_boosloot']						= "Wenn aktiviert, können die Bossnamen in den Raidnotizen und im BossCounter angeklickt werden, um zu einer detaillierten Übersicht der Drops eines Bosses zu gelangen. Wenn nicht aktiviert, wird auf Blasc.de verlinkt. (Nur aktivieren, wenn für jeden Boss ein einzelner Raid angetragen wird.)";
$lang['pk_help_autowarning']					= "Warnt den Administrator beim Einloggen, wenn Updates verfügbar sind.";
//$lang['pk_help_multidkp']						= "MultiDKP erlaubt die Verwaltung und Betrachtung von getrennten Punktekonten. Aktiviert die Berechnung und Anzeige der MultiDKP Konten.";
//$lang['pk_help_dkptooltip']					= "Wenn aktiviert, wird ein Tooltip mit detaillierten Informationen zur Punkteberechnung angezeigt, wenn der Mauszeiger über die Punkte fährt.";
//$lang['pk_help_smarttooltip']					= "Verkürzte Darstellung des Tooltips (Nur bei mehr als drei Ereignissen pro Konto aktivieren.)";
$lang['pk_help_links']							= "In diesem Menü können verschiedene Links definiert werden, die im Hauptmenu dargestellt werden.";
//$lang['pk_help_bosscounter']					= "Wenn aktiviert, wird unterhalb des Hauptmenüs eine Tabelle mit den Bosskills angezeigt. Die Administration erfolgt über das Plugin BossProgress.";
//$lang['pk_help_lm_leaderboard']				= "Wenn aktiviert, wird das Leaderboard oberhalb der Punktetabelle angezeigt. Mit Leaderboard ist eine Tabelle gemeint, in der pro Spalte eine Klasse nach DKP absteigend sortiert angezeigt wird";
//$lang['pk_help_lm_rank']						= "Es wird eine extra Spalte angezeigt, in der der Rang des Charakters dargestellt wird.";
//$lang['pk_help_lm_rankicon']					= "Anstatt des Rangnamens als Text, wird ein Icon angezeigt. Welche Icons verfügbar sind, seht Ihr in dem Ordner games\Spiel\ rank";
//$lang['pk_help_lm_level']						= "Es wird eine extra Spalte angezeigt, in der das Level des Charakters dargestellt wird.";
//$lang['pk_help_lm_lastloot']					= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages, an dem der Spieler zum letzten Mal ein Item bekommen hat.";
//$lang['pk_help_lm_lastraid']					= "Es wird eine extra Spalte angezeigt, mit dem Datum des Tages, an dem der Spieler zum letzten Mal an einem Raid teilgenommen hat.";
//$lang['pk_help_lm_atten30']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 30 Tagen (in Prozent) angezeigt wird.";
//$lang['pk_help_lm_atten60']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 60 Tagen (in Prozent) angezeigt wird.";
//$lang['pk_help_lm_atten90']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an den Raids der letzten 90 Tagen (in Prozent) angezeigt wird.";
//$lang['pk_help_lm_attenall']					= "Es wird eine extra Spalte angezeigt, in der die Beteiligung des Spielers, an allen Raids (in Prozent) angezeigt wird.";
$lang['pk_set_itemtooltip']						= 'ItemTooltip einschalten';
$lang['pk_help_itemtooltip']					= "ItemTooltip ruft bei Datenbanken (Blasc, Allakhazam, Thottbot, ...) Informationen zu den im EQDKP-PLUS eingetragenen Items ab. Diese werdem dann in einem Tooltip angezeigt, ähnlich denen der inGame Tooltips.";
//$lang['pk_help_itemstats_search']				= "In welcher Datenbank soll Itemstats zuerst nach Informationen suchen? Blasc oder Allakhazam?";
$lang['pk_help_itemstats_icon_ext']			= "Dateierweiterung der anzuzeigenden Bilder. Normalerweise .png oder .jpg.";
$lang['pk_help_itemstats_icon_url']			= "Tragt hier die URL ein, wo sich die Itemstats Bilder befinden. Deutsch: http://www.buffed.de/images/wow/32/ in 32x32 oder http://www.buffed.de/images/wow/64/ in 64x64 Pixel. Englisch bei Allakhazam: http://www.buffed.de/images/wow/32/";
//$lang['pk_help_itemstats_translate_deeng']		= "Wenn aktiviert, werden die Informationen des Tooltips in Deutsch abgerufen, auch wenn das Item in Englisch ist.";
//$lang['pk_help_itemstats_translate_engde']		= "Wenn aktiviert, werden die Informationen des Tooltips in Englisch abgerufen, auch wenn das Item in Deutsch ist.";

//$lang['pk_set_leaderboard_2row']		= 'Leaderboard in 2 Zeilen';
//$lang['pk_help_leaderboard_2row']		= 'Wenn aktiviert, wird das Leaderbaord in zwei Zeilen, mit je 4 bzw. 5 Klassen angezeigt.';

//$lang['pk_set_leaderboard_limit']		= 'Limit der Anzeige';
//$lang['pk_help_leaderboard_limit']		= 'Wenn ein numerischer Wert eingetragen wird, beschränkt das Leaderboard die Anzahl der angezeigten Charaktere. 0 steht dabei für keine Einschränkung.';

//$lang['pk_set_leaderboard_zero']		= 'Charaktere mit 0 DKP im Leaderboard ausblenden';
//$lang['pk_help_leaderboard_zero']		= 'Wenn aktiviert, werden Charaktere ohne DKP nicht im Leaderboard angezeigt.';


$lang['pk_set_newsloot_limit']		= 'Newsloot Limit';
$lang['pk_help_newsloot_limit']	= 'Wie viele Items sollen in den News angezeigt werden? Beschränkt die Anzeige der Items, die unter den News angezeigt werden. 0 auswählen für kein Limit.';

$lang['pk_set_itemtooltip_debug']	= 'Debug Modus';
$lang['pk_help_itemtooltip_debug']	= 'Wenn aktiviert, werden alle Schritte von ItemTooltip in die Datei /data/ Ordner geschrieben.';

//$lang['pk_set_showclasscolumn']	= 'Zeige Klassenspalte';
//$lang['pk_help_showclasscolumn']	= 'Wenn aktiviert, wird eine extra Spalte angezeigt, in der die Klasse des Spielers steht.';

//$lang['pk_set_show_skill']			= 'Zeige Skillungsspalte';
//$lang['pk_help_show_skill']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Skillung des Spielers angezeigt wird.';

//$lang['pk_set_show_arkan_resi']	= 'Zeige Arkan Resistenz Spalte';
//$lang['pk_help_show_arkan_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Arkan Resistenz des Spielers angezeigt wird.';

//$lang['pk_set_show_fire_resi']		= 'Zeige Feuer Resistenz Spalte';
//$lang['pk_help_show_fire_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Feuer Resistenz des Spielers angezeigt wird.';

//$lang['pk_set_show_nature_resi']	= 'Zeige Natur Resistenz Spalte';
//$lang['pk_help_show_nature_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Natur Resistenz des Spielers angezeigt wird.';

//$lang['pk_set_show_ice_resi']		= 'Zeige Eis Resistenz Spalte';
//$lang['pk_help_show_ice_resi']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Eis Resistenz des Spielers angezeigt wird.';

//$lang['pk_set_show_shadow_resi']		= 'Zeige Schatten Resistenz Spalte';
//$lang['pk_help_show_shadow_resi']	= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Schatten Resistenz des Spielers angezeigt wird.';

//$lang['pk_set_show_profils']		= 'Zeige Profil Links als Spalte';
//$lang['pk_help_show_profils']		= 'Wenn aktiviert, wird eine Spalte eingeblendet, in der die Links zu den eingegebenen Profilen angezeigt werden.';

//$lang['pk_set_servername']			= 'Realm Name';
//$lang['pk_help_servername']		= 'Gebt hier Euren Servernamen ein, um direkt zur Armory linken zu können.';

//$lang['pk_set_server_region']		= 'Region';
//$lang['pk_help_server_region']		= 'USA & Ozeanien oder Europa.';

//$lang['pk_help_default_multi']		= 'Wählt hier das Konto aus, welches IMMER im Leaderboard angezeigt werden soll. ACHTUNG: bei großen Datenbanken kann die Anzeige des Leaderboards sehr lange dauern und es kann zu Timeouts kommen. Bei Problemen auf der Seite, wählt hier bitte "none" aus.';
//$lang['pk_set_default_multi']		= 'Standard Konto für Leaderboard';

$lang['pk_set_round_activate']		= 'Runde DKP bei der Ausgabe, so dass keine Nachkommastellen angezeigt werden.';
$lang['pk_help_round_activate']	= 'Wenn aktivert, wird die Anzeige der DKP Punkte gerundet. Aus 125,55 DKP werden dann 126 DKP.';

$lang['pk_set_round_precision']	= 'Nachkommastelle auf die gerundet werden soll.';
$lang['pk_help_round_precision']	= 'Bestimmt, auf welche Nachkommastelle die DKP Anzeige bei der Ausgabe gerundet werden soll. Standard=0';

#Infotooltip-Tab
$lang['pk_is_set_prio']		= 'Priorität der Itemdatenbanken';
$lang['pk_itt_prio']            = '%d. Datenbank in der gesucht werden soll';
$lang['pk_is_help_prio']		= 'Legt die Priorität fest, in welcher Reihenfolge die Itemdatenbanken nach einem Item durchsucht werden sollen.';
$lang['pk_itt_set_langprio']    = 'Priorität der Sprachen in denen nach einem Item gesucht werden soll.';
$lang['pk_itt_langprio']        = '%d. Sprache';
$lang['pk_itt_help_langprio']   = 'Priorität der Sprachen in denen in der Datenbank nach einem gesucht werden soll, falls keine Item-ID zur Verfügung steht. Wähle die Sprache, in der du die Items einträgst.';
//$lang['pk_is_set_lang']		= 'Standardsprache der Item ID´s.';
//$lang['pk_is_help_lang']		= 'Standardsprache der Item ID´s. Beispiel: [item]17182[/item] wird in dieser Sprache ausgewählt.';
//$lang['pk_is_set_autosearch']	= 'Sofortige Suche';
//$lang['pk_is_help_autosearch']	= 'Aktiviert: Wenn das Item nicht im Cache ist, wird automatisch danach gesucht, ohne das es extra angeklickt werden muss.<br />Nicht Aktiviert: Das Item muss erst einmal angeklickt werden, damit die Daten abgeholt werden.';
$lang['pk_itt_default_icon']    = 'Standard-Icon';
$lang['pk_itt_default_icon_help']  = 'Standard-Icon, das angezeigt werden soll, falls kein Item gefunden werden konnte.';
$lang['pk_itt_useitemlist']      = 'Itemliste benutzen';
$lang['pk_itt_useitemlist_help'] = 'Das Benutzen der Itemliste verbessert die Suchzeit nach einzelnen Items, wenn die Itemliste sich im Cache befindet. Muss die Itemliste erst geladen werden dauert es länger. Solltest du Probleme haben mit Timeouts könnte das abschalten dieser Funktionalität das Problem beheben.';
$lang['pk_itt_icon_ext'] 		= "Datei-Endung der Icons.";
$lang['pk_itt_icon_ext_help']	= "Normalerweise .png oder .jpg";
$lang['pk_itt_icon_loc']		= "URL zu den Icons.";
$lang['pk_itt_icon_loc_help']	= "Falls die URL nicht bekannt ist, einmal den Parser wechseln und wieder zurück, um Standard-Wert zu laden.";
$lang['pk_itt_not_avail'] = 'Leider sind für dein ausgewähltes Spiel keine Itemstats vorhanden. Wenn du eine Datenbank-Seite mit XML-Schnittstelle kennst für dein Spiel, so melde dich doch bei uns im Forum unter <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a>.';
$lang['pk_itt_reset']			= 'Itemstatscache leeren';

#Global-Tab
//$lang['pk_set_dkp_info']		= 'DKP Info im Menü NICHT anzeigen.'; # JA negierte Abfrage!
//$lang['pk_help_dkp_info']		= 'Wenn aktiviert, dann wird im Hauptmenü die Tabelle DKP Info NICHT angezeigt.';

$lang['pk_set_debug']			= 'EQDKP-PLUS Debug Modus aktiviert';
//$lang['pk_set_debug_type']		= 'Modus';
$lang['pk_set_debug_type0']	= 'Debug aus (Debug=0)';
$lang['pk_set_debug_type1']	= 'Debug an einfach (Debug=1)';
$lang['pk_set_debug_type2']	= 'Debug an mit SQL Ausgaben (Debug=2)';
$lang['pk_set_debug_type3']	= 'Debug an erweitert (Debug=3)';
$lang['pk_help_debug']			= 'Wenn aktiviert, dann läuft EQDKP-PLUS im Debug Modus, welcher zusätzliche Informationen und Fehlermeldungen ausgibt. Deaktivieren, wenn Plugins mit SQL Fehlermeldungen abbrechen! 1=Renderzeit, Query count, 2=SQL Ausgaben, 3=Erweiterte Fehlermeldungen.';

$lang['special_members']			= 'Besondere Charaktere';
$lang['special_members_help']		= 'Besondere Charaktere sind z.B. \'bank\' oder \'disenchanted\'. Sie werden z.B. auf der Punktestandsliste nicht angezeigt.';

$lang['pk_show_twinks']				= 'Twinks anzeigen';
$lang['pk_help_show_twinks']		= 'Wenn Twinks angezeigt werden sollen, erhält jeder Charakter nur seine eigenen Punkte. Um die Punkte von Twinks und Mains zusammen rechnen zulassen, musst du die Anzeige von Twinks hier deaktivieren oder über Portal->Layout verwalten ein Layout mit detaillierter Aufschlüsselung über Punkte von Twinks wählen. Ist solch ein Layout gewählt, ist diese Option bzgl. Punkteberechnung bedeutungslos.';

$lang['pk_detail_twink']			= 'Detaillierte Informationen zu Twinks';
$lang['pk_help_detail_twink']		= 'Wenn aktiviert, kann auf der Punktestandsseite zu jedem einzelnen Charakter eine Liste ausgeklappt werden, in der seine Twinks aufgelistet stehen mit den zugehörigen Werten.';

/* RSS News --> Module
$lang['pk_set_Show_rss']			= 'RSS News deaktivieren';
$lang['pk_help_Show_rss']			= 'Wenn aktiviert, dann werden KEINE aktuellen Gamenews per RSS abgeholt und angezeigt.';

$lang['pk_set_Show_rss_style']		= 'Plazierung der Game-News';
$lang['pk_help_Show_rss_style']	= 'Wo sollen die RSS-Game News angezeigt werden? Oben horizontal, im Menü vertikal oder beides?';

$lang['pk_set_Show_rss_lang']		= 'Standardsprache der RSS-News';
$lang['pk_help_Show_rss_lang']		= 'In welcher Sprache sollen die RSS-News abgeholt werden? Verfügbar sind Deutsch und Englisch.';

$lang['pk_set_Show_rss_lang_de']	= 'Deutsch';
$lang['pk_set_Show_rss_lang_eng']	= 'Englisch';

$lang['pk_set_Show_rss_style_both'] = 'Beides' ;
$lang['pk_set_Show_rss_style_v']	 = 'Menü vertikal' ;
$lang['pk_set_Show_rss_style_h']	 = 'Oben horizontal' ;

$lang['pk_set_Show_rss_count']		= 'Anzahl der anzuzeigenden News (0 oder leer für alle)';
$lang['pk_help_Show_rss_count']	= 'Wieviele News sollen angezeigt werden?';*/

$lang['pk_set_itemhistory_dia']	= 'Zeige Diagramme';
$lang['pk_help_itemhistory_dia']	= 'Auf der Itemdetailseite wird ein Diagramm angezeigt, welches den Preisverlauf grafisch darstellt und die Klassenverteilung eines Raids wird in einer Grafik angezeigt.';

/*Bridge
$lang['pk_set_bridge_help']				= 'Auf dieser Seite kann das Zusammenspiel mit einem Content Management System (CMS) oder einem Forum eingestellt werden.
											   Wenn ihr eines der Systeme aus der Liste benutzt, dann können sich die registrierten Benutzer eures Forums/CMS mit denselben
											   Logindaten auch im Eqdkp anmelden.
											   Der Zugang ist allerdings auf eine Gruppe beschränkt. D.h. ihr müsst in eurem CMS/Forum eine Gruppe anlegen, und in dieser alle Benutzer
											   aufnehmen, die sich dann im Eqdkp anmelden dürfen.';

$lang['pk_set_bridge_activate']			= 'Bridge zu einem CMS aktivieren';
$lang['pk_help_bridge_activate']			= 'Wenn aktiviert, dann wird die Bridge zu einem CMS/Forum aktiviert. Dies ermöglicht den Users dieses CMS sich im EQdkp Plus mit den selben Login Daten anzumelden.';

$lang['pk_set_bridge_dectivate_eq_reg']	= 'Registrierung am Eqdkp Plus auf die oben eingegebene URL umlenken?';
$lang['pk_help_bridge_dectivate_eq_reg']	= 'Wenn aktiviert, können sich keine User mehr direkt am Eqdkp Plus registrieren, sondern werden weitergeleitet auf die angegebene URL';

$lang['pk_set_bridge_cms']					= 'Gewünschtes CMS/Forum';
$lang['pk_help_bridge_cms']				= 'Welches CMS/Forum soll unterstützt werden?';

$lang['pk_set_bridge_acess']				= 'Befindet sich das CMS/Forum in einer anderen Datenbank wie das Eqdkp?';
$lang['pk_help_bridge_acess']				= 'Nutzt das CMS eine andere Datenbank, müssen die folgenden Felder ausgefüllt werden.';

$lang['pk_set_bridge_host']				= 'Hostname bzw. Server';
$lang['pk_help_bridge_host']				= 'Der Hostname bzw. die Server IP wo sich die Datenbank des CMS Systems befindet.';

$lang['pk_set_bridge_username']			= 'Datenbank Username';
$lang['pk_help_bridge_username']			= 'Der Benutzername um auf die Datenbank verbinden zu können';

$lang['pk_set_bridge_password']			= 'Datenbank Passwort';
$lang['pk_help_bridge_password']			= 'Das Passwort um auf die Datenbank verbinden zu können';

$lang['pk_set_bridge_database']			= 'Datenbank Name';
$lang['pk_help_bridge_database']			= 'Name eurer CMS Datenbank';

$lang['pk_set_bridge_prefix']				= 'Prefix der CMS Installation';
$lang['pk_help_bridge_prefix']				= 'Gibt den Prefix eures CMS an, z.B. phpbb_ oder wbb_';

$lang['pk_set_bridge_group']				= 'CMS Gruppen ID die Zugriff haben soll';
$lang['pk_help_bridge_group']				= 'Tragt hier die ID der Gruppe eures CMS Systems ein, welche sich im Eqdkp anmelden darf.';

$lang['pk_set_bridge_inline']				= 'Integration eure Forums per Iframe';

$lang['pk_set_bridge_inline_url']			= 'URL zu eurem Forum';
$lang['pk_help_bridge_inline_url']			= 'URL zu eurem Forum welche innerhalb des Eqdkp dargestellt werden soll';*/

$lang['pk_set_link_type_header']			= 'Wie soll die Seite geöffnet werden';
//$lang['pk_set_link_type_help']				= 'Link im selben Browserfenster, in einem neuen Brwoserfenster oder innerhalb des EQDKP-PLUS in einem Iframe öffnen?';
$lang['pk_set_link_type_iframe_help']		= 'Wie soll der Link geöffnet werden? Bei eingebettet (dynamisch) können nur Seiten eingebunden werden, welche auf dem selben Server installier sind! Bei Problemen bitte die normale Einbettung benutzen.';
$lang['pk_set_link_type_self']				= 'normal';
$lang['pk_set_link_type_link']				= 'Neues Fenster';
$lang['pk_set_link_type_iframe']			= 'Eingebettet';
$lang['pk_set_game_tab']							= 'Spiel';
$lang['pk_set_user']							= 'Benutzer';
$lang['pk_set_contact']							= 'Kontakt';
$lang['pk_set_portal']							= 'Portal';
$lang['pk_set_portal_head']							= 'Portaleinstellungen';
$lang['pk_set_chars']							= 'Charaktere';
$lang['pk_set_chars_settings']					= 'Charaktereinstellungen';
$lang['pk_set_news_settings']					= 'Newseinstellungen';

#recruitment
//$lang['pk_set_recruitment_tab']			= 'Bewerbungen';
//$lang['pk_set_recruitment_header']			= 'Bewerbungen - Sucht ihr neue Member?';
//$lang['pk_set_recruitment']				= 'Bewerbungen aktivieren';
//$lang['pk_help_recruitment']				= 'Wenn die Bewerbungen aktiviert sind, wird in einem Portalmodul neben den News darauf hingewiesen, dass ihr Member sucht.';
//$lang['pk_recruitment_count']				= 'Anzahl';
//$lang['pk_set_recruitment_contact_type']	= 'URL auf die verlinkt werden soll.';
//$lang['pk_help_recruitment_contact_type']	= 'Wenn keine URL angegeben, dann wird auf die Kontakt Email-Adresse verlinkt.';
//$lang['ps_recruitment_spec']				= 'Skillung';

#comments
$lang['pk_set_comments_enable']			= 'Kommentare einschalten';
$lang['pk_hel_pcomments_enable']		= 'Aktiviert die Kommentarfunktion auf allen Seiten die Kommentare unterstützt. In einigen Plugins können diese gesondert deaktiviert werden. Die Newsseite zeigt unabhängig von dieser Einstellung die Kommentare an.';

#Contact
$lang['pk_contact']						= 'Kontaktinformationen';
$lang['pk_contact_name']				= 'Name der Kontaktperson';
$lang['pk_contact_name_help']			= 'Der Name der Kontaktperson der diese Webseite betreut. Für das Impressum wichtig.';
$lang['pk_contact_email']				= 'Email der Kontaktperson';
$lang['pk_contact_email_help']			= 'Email der Kontaktperson der diese Webseite betreut. Am besten eine gesonderte E-Mailadresse verwenden.';
$lang['pk_contact_website']				= 'Portal - Webseite';
$lang['pk_contact_website_help']		= 'Hier muss der Link des Header-Portal Buttons eingegeben werden. Die Eingabe muss als volle URL mit http:// erfolgen.';
$lang['pk_contact_irc']					= 'IRC Channel';
$lang['pk_contact_irc_help']			= 'Der IRC Channel eurer Gilde, zur weiteren Kontaktaufnahme';
$lang['pk_contact_admin_messenger']		= 'Messenger Name z.b. Skype, ICQ';
$lang['pk_contact_admin_messenger_help']= 'Name des Messengers, zum Beispiel  Skype, ICQ. Zur Kontaktaufnahme mit dem Administrator.';
$lang['pk_contact_custominfos']			= 'Weitere Infos';
$lang['pk_contact_custominfos_help']	= 'Weitere Informationen die im Disclaimer der Seite angezeigt werden sollen';
$lang['pk_contact_owner']					= 'Betreiber Infos:';

#Next_raids
//$lang['pk_set_nextraids_deactive']			= 'Nächste Raids nicht anzeigen';
//$lang['pk_help_nextraids_deactive']		= 'Wenn aktiviert, dann wird im Menü nicht die Tabellen mit den nächsten Raids angezeigt.';

//$lang['pk_set_nextraids_limit']			= 'Anzeige Limit der nächsten Raids';
//$lang['pk_help_nextraids_limit']			= 'Wieviele der kommenden Raids sollen angezeigt werden? Standard sind 3';

//$lang['pk_set_lastitems_deactive']			= 'Letzte Items nicht anzeigen';
//$lang['pk_help_lastitems_deactive']		= 'Wenn aktiviert, dann wird im Menü nicht die Tabelle mit den letzten Items angezeigt.';

//$lang['pk_set_lastitems_limit']			= 'Anzeige Limit der letzten Items';
//$lang['pk_help_lastitems_limit']			= 'Wieviele der letztem Items sollen angezeigt werden? Standard sind 5';

/*$lang['pk_is_help']						= 'Sollten nach einem Update eure Items nicht mehr richtig dargestellt werden, setzt die "Priorität der Itemdatenbanken" neu ("Armory & WoWHead" empfohlen), und ruft danach die Items neu ab. Benutzt dazu den "Itemstats aktualisieren"-Button unter diesem Text. <br>
											  Das beste Ergebnis wird mit der Einstellung "Armory & WoWHead" erziehlt, denn nur die Armory gibt erweiterte Informationen wie Droprate, Mob und Instanz pro Item aus.
											  <br> Wichtig: Wenn ihr die Webdatenbank geändert habt, müsst ihr den Itemstats-Cache leeren, da sonst die schon vorhandenen Tooltips nicht mehr richtig angezeigt werden!<br>
											  Zum aktualisieren des Itemcache dem Link folgen, danach die Buttons "Clear Cache" und danach "Update Itemtable" auswählen.<br><br>';*/
$lang['pk_is_info'] = 'Das EQDKP-PLUS enthält nach der Installation keine Items. Du kannste Items nur über zwei Wege in das EQDKP-PLUS bringen: händisches Eintragen und Raidlogimport. Ein Update von Itemstats ruft <b>NICHT</b> sämtliche Items ab, sondern aktualisiert ausschließlich bereits vorhandene Items. Weitere Information zum Thema "Wie kommen Items in das EQDKP-PLUS" findest du im <b><a href="http://wiki.eqdkp-plus.com/de/index.php/Wie_kommen_die_Items_ins_EQdkp" target="_blank">Wiki-Beitrag</a></b>.';
//$lang['pk_set_normal_leaderbaord']			= 'Zeige das Leaderboard dynamisch mit Slider an';
//$lang['pk_help_normal_leaderbaord']		= 'Wenn aktiviert, dann wird das Leaderboard dynamisch angezeigt. Die Klassenspalten vergrößern sich bei Mouseover. aktivieren, wenn die dritte Spalte des Portals angeschaltet ist.';
//$lang['pk_is_update'] = "Itemstats aktualisieren";

//$lang['pk_set_thirdColumn']				= 'Dritte Spalte nicht anzeigen';
//$lang['pk_help_thirdColumn']				= 'Dritte Spalte nicht anzeigen';
//$lang['pk_thirdColumn']					= 'Alle folgenden Features können nur in der dritten Spalte angezeigt werden!';

#GetDKP
//$lang['pk_getdkp_th']						= 'GetDKP Einstellungen';

//$lang['pk_set_getdkp_link']				= 'Zeige GetDKP Link im Hauptmenu';
//$lang['pk_help_getdkp_link']				= 'Wenn aktiviert, dann wird im EQdkp Hauptmenü der Link zur Getdkp.php angezeigt.';

//$lang['pk_set_recruit_embedded']			= 'Link eingebettet aufrufen?';
//$lang['pk_help_recruit_embedded']			= 'Wenn aktiviert, dann wird die anzuzeigende Seite innerhalb des EQdkps angezeigt.';

//$lang['pk_set_dis_3dmember']				= '3D Modelviewer für die Memberanzeige abschalten';
//$lang['pk_help_dis_3dmember']				= 'Wenn aktiviert, dann wird kein 3D Flash Model des WoW Chars angezeigt.';

$lang['pk_set_dis_3ditem']					= '3D Modelviewer für die Items';
$lang['pk_help_dis_3item']					= 'Wenn aktiviert, wird ein 3D Flash Item-Model angezeigt.';

//$lang['pk_set_disregister']				= 'Die Registrierung am EQdkp komplett deaktivieren (ignorierte die voherigen 2 Optionen).';
//$lang['pk_help_disregister']				= 'Wenn aktiviert, dann können sich keine Member mehr am EQdkp registrieren. (anmelden funktioniert weiterhin)';

//$lang['pk_count_listnews']	= 'Maximal angezeigte News (alle übrigen sind über das News-Archiv erreichbar)';
//$lang['pk_help_count_listnews']	= 'Lasse diese Feld leer, um alle News anzuzeigen.';
$lang['pk_enable_newsarchive']	= 'News-Archiv anzeigen';
$lang['pk_enable_newsarchive_help']	= 'Zeige das Newsarchiv auf der Webseite an.';
$lang['pk_newsarchive_position']= 'Position des News-Archivs';
$lang['pk_newsarchive_position_help']= 'Wähle die Position des News-Archives auf der Newsseite.';

# Portal Manager
$lang['portalplugin_name']         = 'Modul';
$lang['portalplugin_version']      = 'Version';
$lang['portalplugin_contact']      = 'Kontakt';
$lang['portalplugin_order']        = 'Sortierung';
$lang['portalplugin_orientation']  = 'Anordnung';
$lang['portalplugin_enabled']      = 'Aktiv';
//$lang['portalplugin_save']         = 'Änderungen übernehmen';
$lang['portalplugin_management']   = 'Portalmodule verwalten';
$lang['portalplugin_right']        = 'Rechts';
$lang['portalplugin_middle']       = 'Mitte oben';
$lang['portalplugin_bottom']       = 'Mitte unten';
$lang['portalplugin_left']        = 'Links';
$lang['portalplugin_left1']        = 'Links über Menü';
$lang['portalplugin_left2']        = 'Links unter Menü';

$lang['portalplugin_rights']       = 'Berechtigung';
$lang['portal_rights0']            = 'Alle';
$lang['portal_rights1']            = 'Gäste';
$lang['portal_rights2']            = 'Angemeldet';
$lang['portal_collapsable']        = 'Einklappbar';
$lang['portal_overview']        		= 'Übersicht';
$lang['portal_positioning']        = 'Positionierung';
$lang['portal_dragndrop_info']      = 'Hier kannst du die Portalmodule anordnen. Klicke dazu ein Portalmodul an und verschiebe es an die gewünschte Stelle. <br>Ein ausgewähltes Modul darf nur in grün markierte Bereiche positioniert werden.';
$lang['pk_set_link_type_D_iframe']			= 'Eingebettet (dyn. angepasste Höhe)';

//$lang['pk_set_modelviewer_default']	= 'Standard Modelviewer';

/* IMAGE RESIZE */
//$lang['pk_air_img_resize_options'] = 'Bilder Einstellungen';
$lang['pk_lightbox_enabled'] = 'Originalbilder in Lightbox anzeigen';
$lang['pk_lightbox_enabled_help'] = 'Zeige die Originalbilder beim Klick auf den Thumbnail in einer Lightbox an.';
$lang['pk_air_max_post_img_resize_width'] = 'Maximale Breite der Bilder';
$lang['pk_air_max_post_img_resize_width_help'] = 'Die maximale Breite der Bilder in den Newsbeiträgen und Kommentaren.';

//$lang['pk_set_rss_chekurl'] = 'URL vor dem Update überprüfen';
//$lang['pk_help_rss_chekurl'] = 'Wenn aktiviert, dann wird vorm dem Abrufen der RSS News überprüft ob der Server erreichbar ist. Deaktivieren wenn der RSS Feed über längere Zeit nicht angezeigt wird.';

//$lang['pk_set_features'] = 'DKP Funktionen';

$lang['pk_set_noDKP'] = 'Keine DKP Funktionen';
$lang['pk_help_noDKP'] = 'Wenn aktiviert, dann werden alle DKP Funktionen deaktiviert und es werden keine Hinweise auf DKP Punkte mehr angezeigt. Betrifft nicht die Raid und Ereignissliste.';

$lang['pk_set_noRoster'] = 'Kein Roster anzeigen';
$lang['pk_help_noRoster'] = 'Wenn aktiviert, dann wird die Roster Seite nicht im Menu angezeigt und das aufrufen der Seite unterbunden.';

$lang['pk_set_noDKP'] = 'Member Roster anstatt der Punkteliste anzeigen';
$lang['pk_help_noDKP'] = 'Wenn aktiviert, dann wird anstatt der Punkteliste das Member Roster angezeigt.';

$lang['pk_set_noRaids'] = 'Keine Raid Funktionen';
$lang['pk_help_noRaids'] = 'Wenn aktiviert, dann werden alle Raid Funktionen deaktiviert. Betrifft nicht die Ereignissliste.';

$lang['pk_set_noEvents'] = 'Keine Ereignisse';
$lang['pk_help_noEvents'] = 'Wenn aktiviert, dann werden alle Event Funktionen deaktiviert. ACHTUNG Ereigniss werden für den Raidplaner benötigt!!!';

$lang['pk_set_noItemPrices'] = 'Keine Item-Preise';
$lang['pk_help_noItemPrices'] = 'Wenn aktiviert, dann wird der Link zu den Item-Preisen deaktiviert und die Seite wird gesperrt.';

//$lang['pk_set_noItemHistoy'] = 'Keine Item-Historie';
//$lang['pk_help_noItemHistoy'] = 'Wenn aktiviert, dann wird der Link zu der Item-Historie deaktiviert und die Seite wird gesperrt.';

//$lang['pk_set_noStats'] = 'Keine Zusammenfassung und Statistik';
//$lang['pk_help_noStats'] = 'Wenn aktiviert, dann wird der Link zu der Zusammenfassung und Statistik deaktiviert und die Seiten werden gesperrt.';

//$lang['pk_set_cms_register_url'] = 'URL zur Registrierung an eurem CMS/Forums';
//$lang['pk_help_cms_register_url'] = 'Wenn die Bridge aktiviert ist, dann wird auf diese URL weitergeleitet, wenn jemand auf den Link "Registrieren" klickt.';

$lang['pk_set_link_type_menu']			= 'Menu';
$lang['pk_set_link_type_menuH']		= 'Reiter oben';

# Filter in portal modules
$lang['portalplugin_filter'] = 'Filtern';
$lang['portalplugin_filter1_all']	= 'Alle Modulpositionen';
$lang['portalplugin_filter2_all'] = 'Aktiv & Inaktiv';
$lang['portalplugin_filter3_all'] = 'Alle Rechte';
$lang['portalplugin_disabled'] = 'Inaktiv';

#new settings
$lang['pk_set_system'] = 'System';
//$lang['pk_set_mgame'] = 'Spiele verwalten';
//$lang['pk_set_rss_news'] = 'RSS-News';
//$lang['pk_set_rss_tabname'] = 'RSS';
//$lang['pk_set_multidktable'] = 'MultiDKP';
$lang['pk_set_globaltable'] = 'Global';
$lang['pk_set_linkstable'] = 'Links';
//$lang['pk_set_leaderbtable'] = 'Leaderboard';
//$lang['pk_set_listmemtable'] = 'Listmembers';
//$lang['pk_set_charmanatable'] = 'Charmanager Plugin';
$lang['pk_set_news_tab'] = 'News';
$lang['pk_set_itemtooltip_name'] = 'ItemTooltip';
//$lang['pk_set_cmsb_tab'] = 'CMS-Bridge';
//$lang['pk_set_updatestable'] = 'Update';
//$lang['pk_set_bridgectable'] = 'Bridge Config';

$lang['default_game_note'] = 'Das Spiel, das die Gilde die diese Webseite benutzt, spielt.';
$lang['game_language_note']= 'Die Sprache des gewählten Spieles';
$lang['default_locale_note'] = 'Der verwendete Zeichensatz, zB zum übersetzen der Monatsnamen';
//$lang['default_style_note'] = 'Der Standardstyle den Gäste und neue Benutzer sehen';
$lang['default_lang_note'] = 'Die Standardsprache für Gäste und neue Benutzer';
$lang['enable_gzip_note'] = 'Die Komprimierung verringert den Traffic, kann aber zu Problemen führen.';
//$lang['server_name_note'] = 'Die Domain des Servers, z.B. "domain.tld"';
$lang['default_style_overwrite_note'] = 'Die Benutzereinstellungen werden dann mit den hier eingestellten Werten überschrieben';
$lang['admin_email_note'] = 'Die (gültige!) Adresse die für Adminaktionen benutzt werden soll';
$lang['dkp_logoimg_note'] = 'Das Header Bild des EQDKP-PLUS';
$lang['sub_title_note'] = 'Die Beschreibung der Seite';
$lang['main_title_note'] = 'Der Name der Seite';
$lang['start_page_note'] = 'Die Startseite ist zugleich die Modulseite, nur hier werden mittel- und rechte Spalten angezeigt.';
$lang['default_alimit_note'] = 'Die Anzahl der Korrekturen pro Seite, dann wird ein Seitenumbruch erstellt';
$lang['default_elimit_note'] ='Die Anzahl der Ereignisse pro Seite, dann wird ein Seitenumbruch erstellt';
$lang['default_ilimit_note'] = 'Die Anzahl der Items pro Seite, dann wird ein Seitenumbruch erstellt';
$lang['default_nlimit_note'] = 'Die Anzahl der Newseinträge pro Seite, dann wird ein Seitenumbruch erstellt';
$lang['default_rlimit_note'] = 'Die Anzahl der Raids pro Seite, dann wird ein Seitenumbruch erstellt';
$lang['cookie_path_note'] = 'Der Cookie Pfad sollte nur von Leuten geändert werden, die wissen was sie tun. Kann zu Loginproblemen führen';
$lang['cookie_name_note'] = 'Der Name des Cookies. Dieser sollte nur von Leuten geändert werden, die wissen was sie tun. Kann zu Loginproblemen führen';
$lang['cookie_domain_note'] = 'Die Cookie Domain sollte nur von Leuten geändert werden, die wissen was sie tun. Kann zu Loginproblemen führen';
$lang['session_length_note'] = 'Die Länge der Seitzung (Session) in Sekunden. Sollte nicht zu lang sein (default: 3600)';
$lang['account_activation_note'] = 'Soll ein neu angelegter Account freigeschaltet werden, wenn ja wie?';

$lang['pk_set_gamelanguage'] = 'Sprache des Spieles';
$lang['pk_set_defaultgame'] = 'Spiel';
$lang['pk_set_layout'] = 'Layout';
$lang['pk_set_defaults'] = 'Allgemeine Einstellungen';
$lang['pk_deflanguage'] = 'Standardsprache';
$lang['pk_disable_username_change'] = 'Benutzer dürfen Benutzernamen nicht ändern';
$lang['pk_disable_username_change_help'] = 'Die Benutzer dürfen ihren Benutzernamen selbst nicht ändern';
$lang['pk_permanent_portal'] = 'Ausgewählte Portalspalte auf jeder Seite anzeigen';

$lang['page_manager'] = 'Layout verwalten';
#$lang['pk_help_locale']

//maintenance mode
$lang['pk_maintenance_mode'] = 'Wartungsmodus aktivieren.';
$lang['pk_help_maintenance'] = 'Der Wartungsmodus sorgt dafür, dass alle Nicht-Admins auf eine Wartungsseite weitergeleitet werden und erlaubt es dem Admin in Ruhe Wartungsarbeiten durchzuführen.';

// Plugin Update Warn Class
$lang['puc_solve_dbissues']         = 'beheben';

$lang['plus_cache_reset_done']      = 'Reset ausgeführt!';
$lang['plus_cache_reset_name']      = 'Plus Data Cache';

// Update Check PLUS
$lang['lib_pupd_intro']             = 'Es wurden folgende neuere Versionen auf www.eqdkp-plus.com gefunden:';
$lang['lib_pupd_updtxt']            = "<b>%1\$s</b>: neue Version: %2\$s, installierte Version: %3\$s, Update veröffentlicht am %4\$s";
$lang['lib_pupd_noupdates']         = 'Das EQDKP-PLUS und all seine Plugins sind aktuell! Es sind zur Zeit keine neueren Versionen auf www.eqdkp-plus.com verfügbar. Bitte regelmäßig überprüfen...';
$lang['lib_pupd_changelog']         = 'Changelog';
$lang['lib_pupd_nochangelog']       = 'Es sind keine Changelog-Informationen bekannt.';
$lang['lib_pupd_download']          = 'Download';
//$lang['lib_pupd_checknow']          = 'Jetzt nach Updates suchen';

// RSS News
$lang['rssadmin_head1']             = 'Benachrichtigungen';
$lang['rssadmin_head2']             = 'Eqdkp-Plus Twitter Feed';

// Admin Info Center
//$lang['adminc_information']         = 'Information';
$lang['adminc_news']                = 'News';
$lang['adminc_updtcheck']           = 'Aktualisierungen';
$lang['adminc_statistics']          = 'Statistiken';
$lang['adminc_server']              = 'PHP Infos';
$lang['adminc_support']             = 'Support';
$lang['adminc_phpvalue']            = 'Wert';
$lang['adminc_phpname']             = 'Name der PHP	Einstellung';
$lang['adminc_support_intro']       = "Wenn du Fragen und Anregungen zu EQDKP-PLUS hast, besuche doch einfach eine der folgenden Seiten:";
$lang['adminc_support_wiki']        = "Das WIKI ist ein Online Dokumentationssystem. Es gibt viele Artikel, FAQ und Antworten. Benutzer werden dazu ermuntert, eigene Artikel zu schreiben und somit der Community zu helfen.<br><b><a href='http://wiki.eqdkp-plus.com' target='blank'>Zum WIKI</a></b>";
$lang['adminc_support_bugtracker']  = "Du hast einen Bug gefunden? Dann hilf uns: Durchsuche zuerst den Bugtracker, und melde nicht vorhandene Fehler.<br><b><a href='http://bugtracker.eqdkp-plus.com' target='blank'>Zum Bugtracker</a></b>";
$lang['adminc_support_forums']      = "Du hast Fragen rund um das EQDKP-PLUS? Benötigst Hilfe und das WIKI hat dir nicht weitergeholfen? Besuche das offizielle Forum!<br>Damit dir möglichst schnell geholfen werden kann, halte dich bitte an die Forumsregeln und fülle dein Forums-Profil aus!<br /><b><a href='http://www.eqdkp-plus.com/forum' target='blank'>Zum Forum</a></b>";
$lang['adminc_support_tour']      = "Du bist neu bei EQDKP-PLUS? Du hast noch nie Raids eingetragen oder DKP-Punkte vergeben? Du bist einfach nur neugierig?<br /><b><a href='?tour=start'>Dann starte jetzt die Tour durch das EQdkp Plus</a></b>";

$lang['title_manageusers']	= 'Manage Users';
$lang['title_mysqlinfo']		= 'MySQL Info';
$lang['title_resetdkp']			= 'Reset EQDKP Plus';
$lang['portal']							= 'Portal';

//Plugin dependency additions
$lang['plug_dep_title'] = "Abhängigkeiten";
$lang['plug_dep_plusv'] = "Plus Version";
$lang['plug_dep_libsv'] = "Library Version";
$lang['plug_dep_games'] = "Spielunterstützung";
$lang['plug_dep_phpf'] = "PHP Funktionen";
$lang['plug_dep_broken_deps'] = "Abhängigkeiten nicht erfüllt!";
$lang['plug_tab_plugins']			= 'Plugins verwalten';
//$lang['plug_tab_install']			= 'Plugins installieren';
$lang['plug_tab_plugupdates']	= 'Updates nötig!';
$lang['plug_tab_noplugupdates'] = 'Keine Updates nötig';

// manage_tasks
$lang['uc_del_warning']				= 'Soll der Charakter wirklick gelöscht werden? Alle Punkte und Gegenstände gehen unweigerlich verloren.';
$lang['uc_del_msg_all']				= 'Sollen wirklich alle Charakter gelöscht werden?';
$lang['uc_confirm_msg_all']		= 'Sollen wirklich alle Charakter freigegeben werden?';
$lang['uc_delete_manager']		= 'Verwaltung von Aufgaben';
$lang['uc_rewoke_char']				= 'Charakter wiederherstellen';
$lang['uc_delete_char']				= 'Charakter löschen';
$lang['uc_delete_allchar']		= 'Alle löschen';
$lang['uc_confirm_all']				= 'Alle bestätigen';
$lang['uc_confirm_list']			= 'Charakter zum Bestätigen';
$lang['uc_confirm_char']			= 'Charakter bestätigen';
$lang['uc_delete_list']				= 'Charakter zum Löschen';
$lang['uc_no_tasks']					= 'Es sind keine Aufgaben vorhanden.';
$lang['uc_tasks_info']				= 'Hier kannst Du Aufgaben verwalten, wie z.B. Charaktere bestätigen oder löschen, oder inaktive Benutzer aktivieren. Bitte überprüfe diese Seite regelmäßig, ob Aufgaben vorhanden sind.';

$lang['raids']								= 'Raids';
$lang['maintenance']					= 'Wartungsbereich';

//User-Groups
$lang['manage_user_groups'] = 'Benutzergruppen verwalten';
$lang['manage_user_group']  = 'Benutzergruppe verwalten';
$lang['members']  = 'Mitglieder';
$lang['group_members']  = 'Gruppenmitglieder';
$lang['group_permissions']  = 'Gruppenberechtigungen';
$lang['manage']  = 'Verwalten';
$lang['delete_selected_group']  = 'Ausgewählte Gruppen löschen';
$lang['add_user_group']  = 'Benutzergruppe hinzufügen';
$lang['del_user_from_group_success']  = 'Die ausgewählten Mitglieder wurden erfolgreich aus der Gruppe entfernt.';
$lang['add_user_to_group_success']  = 'Die ausgewählten Mitglieder wurden erfolgreich zur Gruppe hinzugefügt.';
$lang['confirm_delete_groups'] = 'Bist Du sicher, dass Du diese Benutzergruppen löschen willst?';
$lang['user_groups'] = 'Benutzergruppen';
$lang['groups'] = 'Gruppen';
$lang['add_user_to_group'] = 'Benutzer zur Gruppe hinzufügen';
$lang['delete_selected_from_group'] = 'Markierte Benutzer entfernen';
$lang['add_selected_to_group'] = 'Markierte Benutzer hinzufügen';
$lang['admin_right_icon_title'] = 'Administrator-Recht';
$lang['s_admin_note'] = 'Dies symbolisert Administrator-Rechte.';
$lang['s_group_note'] = 'Deaktiviere Rechte bekommt der Benutzer bereits über Gruppenrechte.';
$lang['default_group'] = 'Standardgruppe';
$lang['delete_default_group_error'] = 'Du kannst die Standard-Gruppe nicht löschen. Wähle eine andere Standardgruppe aus und wiederhole den Vorgang.';
$lang['delete_associated members'] = 'Sollen die verknüpfen Mitglieder auch gelöscht werden?';
$lang['user'] = 'Benutzer';
$lang['no_auth_superadmins'] = 'Du hast keine Berechtigung, diese Benutzergruppe zu verwalten.';
$lang['user_group_permissions'] = 'Übersicht Benutzergruppen/Rechte';
$lang['add_usergroup_success'] = 'Die Benutzergruppe "%s" wurde erfolgreich hinzugefügt.';
$lang['save_usergroup_success'] = 'Die Benutzergruppen wurden erfolgreich aktualisiert.';

//Add new user
$lang['user_creation_success'] = 'Der Benutzer %s wurde erfolgreich erstellt. Eine Email mit den Zugangsdaten wurde an die angegebene Email-Adresse versendet.';
$lang['user_creation_password_note'] = 'Lasse die Passwort-Felder leer, um ein zufälliges Passwort generieren zu lassen.';
$lang['user_creation'] = 'Neuen Benutzer erstellen';

//Maintenance-User
$lang['maintenanceuser_info'] = 'Um Supportern einfacher einen temporären Zugang zu eurem EQdkp Plus zu geben, kannst du hier einen Wartungs-Benutzer anlegen. Beim Anlegen wird ein zufälliges Passwort generiert, und nach Ablauf der eingestellten Gültgkeit wird der Wartungs-Benutzer automatisch aus dem System gelöscht. Natürlich kannst Du den Wartungsbenutzer jederzeit vorher deaktivieren.';
$lang['maintenanceuser_warning'] = 'Achtung: der Wartungsbenutzer hat als Superadministrator sämtliche Rechte im EQDKP. Gib diese Daten deshalb nur an absolut vertrauenswürdige Personen weiter. Mitglieder des EQdkp/Allvatar-Teams sind im Forum auch als solche gekennzeichnet.<br><br>Sende keine unaufgeforderte Emails/Private Nachrichten an das EQdkp/Allvatar-Team. Unaufgeforderte Emails werden sofort gelöscht!';
$lang['maintenanceuser_user'] = 'Wartungsbenutzer';
$lang['maintenanceuser_create'] = 'Wartungsbenutzer anlegen';
$lang['maintenanceuser_delete'] = 'Wartungsbenutzer löschen';
$lang['maintenanceuser_valid'] = 'Gültigkeit';
$lang['maintenanceuser_valid_until'] = 'Gültig bis';
$lang['maintenanceuser_send'] = 'Versenden';
$lang['maintenanceuser_send_mail'] = 'Daten per Email versenden';
$lang['maintenanceuser_mail_subject'] = 'EQDKP-Wartung von %s';
$lang['maintenanceuser_mail_success'] = 'Die Daten des Wartungsbenutzers wurden erfolreich versandt.';
$lang['maintenanceuser_mail_error'] = 'Beim Senden der Email ist ein Fehler aufgetreten.';
$lang['maintenanceuser_mail_not_valid'] = 'Die eingegebenen Email-Adressen stimmen nicht überein.';

// PlugManager
$lang['pi_manualupload']             = 'Manueller Upload';
$lang['pi_manualupload_info']        = 'Hier kannst du heruntergeladene Erweiterungen wie z.B. Styles, Plugins oder Portalmodule hochladen. Um die Erweiterungen abschließend zu installieren, gehe auf die entsprechende Verwaltungsseite.<br /><b>Weitere Erweitungen kannst du von unserer Projektseite herunterladen: <a href="http://eqdkp-plus.com" target="_blank">http://eqdkp-plus.com</a>.</b>';

$lang['pi_choose_file']              = 'Bitte Datei zum Hochladen auswählen';
$lang['pi_upload_button']            = 'Hochladen';
$lang['plugin_error_prefix']				= 'FEHLER';
$lang['plugin_upload_error1']				= 'Bitte gib eine Datei an';
$lang['plugin_upload_error2']				= "Es dürfen nur Dateien vom Typ %s hochgeladen werden!";
$lang['plugin_upload_error3']				= 'Es dürfen nur Dateien mit einer Maximalgröße von 5 Megabyte hochgeladen werden!';
$lang['plugin_package_error1']			= 'Das hochgeladene Packet enthällt keine Kontrolldateien und ist somit nicht gültig!';
$lang['plugin_package_error2']			= 'Das hochgeladene Packet enthällt eine fehlerhafte Kontrolldatei. Die Kategorie kann nicht eindeutig bestimmt werden!!';
$lang['manage_extensions']					= 'Erweiterungen';
$lang['extensions_install']					= 'Erweiterungen installieren';

//Infopages
$lang['info_confirm_delete'] = "Bist Du sicher, dass du diese Infoseiten löschen willst?";
$lang['ID'] = "ID";
$lang['alias'] = "Alias";
$lang['editor_language']	= 'de';
$lang['info_alias'] = "Seiten-Alias";
$lang['info_delete_page'] = "Seite löschen";
$lang['info_manage_pages'] = "Seiten verwalten";
$lang['info_create_page'] = "Neue Seite erstellen";
$lang['info_error_alias'] = "Dieses Alias wird bereits verwendet oder besteht nur aus Zahlen!";
$lang['info_no_pages'] = 'Es sind keine Seiten vorhanden.';

$lang['info'] = "Info-Seiten";
$lang['pm_info_view'] = "zeige Seiten";
$lang['pm_info_man'] = "Seiten verwalten";

//$lang['pk_infopages_headtext'] = "Portalmodul Titel:";
$lang['info_opt_tsel'] = "Seitenauswahl:";
$lang['info_opt_title'] = "Seitentitel:";
$lang['info_opt_ml'] = "Anzeigen in:";
$lang['info_opt_content'] = "Inhalt";

$lang['info_opt_ml_0'] = "nirgends";
$lang['info_opt_ml_1'] = "Hauptmenü";
$lang['info_opt_ml_2'] = "User-Menü";
$lang['info_opt_ml_3'] = "Reiter oben";
$lang['info_opt_ml_99'] = "Info-Portalmodul";

$lang['info_opt_visibility'] = "Sichtbarkeit";
$lang['info_opt_vis_0'] = "Öffentlichkeit";
$lang['info_opt_vis_1'] = "Gäste";
$lang['info_opt_vis_2'] = "Angemeldete Benutzer";
$lang['info_opt_vis_3'] = "Administratoren";
$lang['info_comments'] 	= "Kommentare erlauben";
$lang['info_voting'] 		= "Bewertungen erlauben";
$lang['info_pageopt']			= "Seiteneinstellungen";
$lang['info_sort_short']	= "Sort.";
$lang['info_help_title']	= "Gebe hier den Titel der Seite an. Dieser wird z.B. im Menü angezeigt";
$lang['info_help_comments']	= "Benutzer dürfen den Artikel kommentieren";
$lang['info_help_voting']	= "Benutzer dürfen den Artikel bewerten";
$lang['info_reset_votings']	= "Bewertungen zurücksetzen";
$lang['info_delete_comments']	= "Kommentare löschen";
$lang['guildrules_info'] = 'Die Gildenregeln müssen von jedem Benutzer vor der Registrierung oder nach dem ersten Einloggen (wenn eine Bridge verwendet wird) bestätigt werden.';
$lang['licence_agreement'] = 'Nutzungsbedigungen';


$lang['info_help_alias']	= "Mit einem Alias ist die Seite z.B. unter domain.de/pages.php?page=ALIAS zu erreichen. Das Alias muss einmalig sein und darf nicht nur als Zahlen bestehen.";

$lang['info_help_ml']	= "Wähle aus, wo die Seite angezeigt werden soll.";
$lang['info_help_vis']	= "Wähle aus, wer diese Seite sehen darf.";
$lang['pi_repoblocked']							= 'Das Online Repo wurde für diesen Server gesperrt, da er im Verdacht steht gegen die Richtlinien von EQDKP-PLUS zu verstoßen. Dies betrifft v.a. unangemeldete EQDKP-PLUS Hostingdienste. Sollte dies nicht zutreffen, bitte im EQDKP-PLUS Forum melden!<br /><br /> <a href="extensions.php?mode=reset_blacklist">Nochmal prüfen!</a>';
// Online Repo
//$lang['pi_mode_plugins']             = 'Verfügbare Plugins';
//$lang['pi_mode_onlinerepo']          = 'Online Repository';
//$lang['pi_mode_manupload']           = 'Manueller Upload';
$lang['pi_onlinerepo']               = 'Online Repo';
$lang['pi_date']                     = 'Datum';
$lang['pi_title']                    = 'Plugin';
$lang['pi_description']              = 'Beschreibung';
$lang['pi_download']                 = 'Download';
$lang['pi_version']                  = 'Version';
$lang['pi_author']                   = 'Autor';
$lang['pi_shortdec']                 = 'Kurzbeschreibung';
$lang['pi_action']                   = 'Aktionen';
$lang['pi_category']                 = 'Kategorie';
$lang['pi_updnow']                   = 'Online aktualisieren';
$lang['pi_lastupdate']               = 'Letzte Aktualisierung';
$lang['pi_installed']                = 'Installiert';
$lang['pi_update']                   = 'Update verfügbar';
//$lang['pi_version_installed']        = 'Installiert';
//$lang['pi_version_online']           = 'Verfübar';
$lang['pi_installed']                = 'Installiert';
//$lang['pi_available']                = 'Verfügbar';
$lang['pi_category_1']               = 'Plugins';
$lang['pi_category_2']							= 'Templates';
$lang['pi_category_3']							= 'Portal Module';
$lang['pi_category_4']							= 'Libraries';
$lang['pi_category_7']							= 'Games';

//Manage Menus
$lang['dragndrop'] = "Klicken und an gewünschte Position verschieben";
$lang['manage_menus'] = "Menüs & Links verwalten";
$lang['inactive_entries'] = "Ausgeblendete Menüeinträge";
$lang['menu_entry'] = "Menüeintrag";
$lang['menus_info'] = "Hier kannst du die Menüeinträge individuell anordnen. Klicke dazu einen Eintrag an und verschiebe ihn an die gewünschte Position. Du kannst Menüeinträge auch ausblenden, in dem du sie unter 'Ausgeblendete Menüeinträge' verschiebst. <br><b>Beachte: </b> Auch wenn Menüeinträge ausgeblendet sind, können die Benutzer trotzdem auf die Seite zugreifen, wenn sie die entsprechende Berechtigung haben.";
$lang['no_inactive_entries'] = "Keine Ausgeblendeten Menüeinträge vorhanden. Ziehe Menü-Einträge in diesen Bereich, um sie auszublenden.";
$lang['favorits'] = "Favoriten";
$lang['favorits_admin_menu'] = "Favoriten Admin-Menü";
$lang['favorits_info'] = "Hier kannst Du Dir dein Favoriten-Menü für das Administrations-Bereich zusammenstellen. Wähle dazu einen Menüpunkt aus den Bereichen unten aus und verschiebe in an die gewünschte Stelle in den Favoriten-Bereich. Um einen Favoriten zu löschen, klicken auf den Lösch-Button. Der Favorit wird wieder in seinem Ursprungsbereich erscheinen.";
$lang['favorits_enable'] = "Favoriten-Menü aktivieren";
$lang['links_info'] = "Hier können die Einträge für das Links-Menü bearbeitet werden. Auch eine Sortierung ist möglich. Klicke dazu einen Eintrag an und verschiebe ihn an die gewünschte Stelle.";
$lang['no_favs_message'] = "Um Elemente zu deinen Favoriten hinzuzufügen, ziehe einfach den gewünschten Menü-Eintrag in dieses Feld.";
$lang['pk_color_items'] = 'Schwellwerte für Zahlen (negativ, neutral, positiv)';
$lang['pk_color_items_help'] = 'Lege hier die Schwellwerte für die Kolorierung von Zahlen fest, z.B. der Raidbeteiligung';

//Manage Profilefields
$lang['manage_pf_menue'] = "Profilfelder verwalten";
$lang['manage_profilefields'] = "Charakter-Profilfelder bearbeiten";
$lang['new_profilefield'] = "Neues Profilfeld anlegen";
$lang['profilefields_footcount'] = "... %d Profilfelder gefunden";
$lang['confirm_del_profilefields'] = "<b>Bist du sicher, dass du die folgenden Profilfelder löschen möchtest?</b>";
$lang['field_length'] = "Feld-Länge";
$lang['profilefield_image']	= "<b>Bild-Dateiname:</b> (Bild muss im Ordner games/%s/profiles liegen)";
$lang['profilefield_optionen']	= "Dropdown-Optionen";
$lang['pf_enable_suc']	= 'Profilfeld "%s" wurde erfolgreich aktiviert.';
$lang['pf_enable_nosuc']	= 'Profilfeld "%s" konnte nicht aktiviert werden.';
$lang['pf_disable_suc']	= 'Profilfeld "%s" wurde erfolgreich deaktiviert.';
$lang['pf_disable_nosuc']	= 'Profilfeld "%s" konnte nicht deaktiviert werden.';
$lang['pf_delete_suc']	= 'Die ausgewählten Profilfelder wurden erfolgreich gelöscht.';
$lang['pf_delete_nosuc']	= 'Es wurden keine Profilfelder zum Löschen ausgewählt.';
$lang['pf_save_suc']	= 'Die Profilfeld wurde erfolgreich gespeichert.';
$lang['pf_save_nosuc']	= 'Das Profilfeld konnte nicht gespeichert werden, weil ein gleichnamiges bereits existiert.';

//Manage Cronjobs
$lang['manage_cronjobs'] = "Zeitgesteuerte Aufgaben verwalten";
$lang['repeat_interval'] = "Wiederholungs-Intervall";
$lang['last_run'] 			= "Letzte Ausführung";
$lang['next_run'] 			= "Nächste Ausführung";
$lang['execute'] 				= "Ausführen";
$lang['repeat'] 				= "Wiederholen";
//$lang['params'] 				= "Parameter";
$lang['cron_start_time'] = "Start-Datum/-Zeit";
$lang['cron_run_success'] = 'Der Cronjob "%s" wurde erfolgreich ausgeführt.';
$lang['footcount_cronjobs'] = "... %1\$d aktive / insgesamt %2\$d Cronjobs gefunden";

$lang['hourly'] 		= "stündlich";
$lang['daily'] 			= "täglich";
$lang['minutely'] 	= "minütlich";
$lang['weekly'] 		= "wöchentlich";
$lang['monthly'] 		= "monatlich";
$lang['yearly'] 		= "jährlich";

$lang['repeat_note'] 	= "Bei den Wiederholungsintervallen wird der Stichtag oder -zeit aus dem Startzeitpunkt verwendet.<br />Soll der Cronjob am 1. eines Monats ausgeführt werden, so wähle als Startdatum den 1. eines Monats, wähle die Startzeit (um welche Uhrzeit die Aufgabe starten soll) und als Intervall '1-monatlich' aus.";

$lang['cron_prunebackups_days'] = 'Lösche Backups älter als x Tage';
$lang['cron_prunebackups_count'] = 'Lösche alle Backups bis auf die letzten x Backups';

//Simple Uploader
$lang['file_manager'] 	= "Datei-Manager";
$lang['upload_file'] 		= "Datei hochladen";
$lang['select_file'] 		= "Datei auswählen";
$lang['manage_files'] 	= "Dateien verwalten";
$lang['add_folder'] 		= "Ordner hinzufügen";
$lang['folder_name'] 		= "Ordner-Name";
$lang['selected_files'] = "Ausgewählte Dateien/Ordner";
$lang['move_files'] 		= "verschieben";
$lang['move_to'] 		= "nach";
$lang['go'] 		= "Los";
$lang['upload_success'] = "Die Datei %s wurde erfolgreich hochgeladen";
$lang['select_dest_folder'] 	= "Zielordner auswählen";
$lang['upload_extensions'] 	= "Erlaubte Upload-Dateiendungen";
$lang['upload_extensions_help'] 	= "Trage, durch Komma getrennt, die erlaubten Dateiendungen für den File-Manager ein";

//automatic point adjustments
$lang['manage_auto_points'] = "Autom. Punktkorrekturen";
$lang['apa_manager'] = 'Automatische Punktkorrekturen';
$lang['apa_pool'] = 'DKP Pool';
$lang['apa_event'] = 'Ereignis';
$lang['apa_value'] = 'Wert';
$lang['apa_reason'] = 'Begründung';
$lang['apa_type'] = 'Typ';
$lang['apa_type_startpoints'] = 'Startpunkte';
$lang['apa_type_timedecay'] = 'Zeitl. Verfall';
$lang['apa_type_pointcap'] = 'Punktemaximum';
$lang['apa_type_inactivity'] = 'Inaktivität';
$lang['apa_add'] = 'Autom. Punktkorrektur hinzufügen';
$lang['apa_edit'] = 'Autom. Punktkorrektur bearbeiten';
$lang['apa_new'] = 'Neue autom. Punktekorrektur hinzufügen';
$lang['apa_list'] = 'Korrekturen auflisten';
$lang['apa_recalculate'] = 'Korrekturen neu berechnen';
$lang['uc_import_guildb']			= 'Gilde importieren';
$lang['uc_import_adm_update']			= 'Benutzerprofile aktualisieren';
$lang['uc_import_guild_wh']				= 'Importiere Charactere einer Gilde';
?>