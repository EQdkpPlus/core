<?php
/******************************
 * EQdkp RaidBanker Plugin
 * Copyright 2005 - 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 ******************************/

// General Shit
$lang['raidbanker'] 						      = "Raid Banker";
$lang['raidbanker_title'] 			      = "Raid Bankier";
$lang['rb_date_format']               = "%A, %d.%m.%Y, %H:%M";
$lang['rb_local_format']              = "German";

// Buttons
$lang['rb_import']							      = "Importieren";
$lang['rb_add']                       = "Zufügen";
$lang['rb_edit']							        = "Bearbeiten";
$lang['rb_delete']							      = "Löschen";
$lang['rb_view']                      = "Ansehen";
$lang['rb_config']                    = "Einstellungen";
$lang['lang_couldnt_info']            = "Konnte Item-Infos nicht nicht parsen";
$lang['lang_couldnt_char']						= "Konnte die Charakterinformationen nicht abrufen";
$lang['rb_close']                     = "Schliessen";

// User Menu
$lang['rb_usermenu_raidbanker']	      = "Raid Banker";

// Admin Menu
$lang['rb_adminmenu_raidbanker']			= "Raid Banker";
$lang["rb_step1_pagetitle"]					  = "Raid Banker - Importiere Logdatei";
$lang["rb_step1_th"]						      = "Füge Raid Banker Log unten ein";
$lang["rb_step1_button_parselog"]			= "Verarbeite Log";
$lang["rb_step2_pagetitle"]					  = "Raid Banker - Verarbeitete Logdatei";
$lang["rb_edit_pagetitle"]					  = "Raid Banker - Bearbeite Bankier";

//output
$lang['rb_Bank_Items']                = "Gegenstände auf der Bank";
$lang['rb_Banker']                    = "Bankier";
$lang['rb_all_Banker']                = "Alle Bankiers";
$lang['rb_not_avail']                 = "n.v.";
$lang['rb_Item_Name']                 = "Gegenstand";
$lang['rb_Bank_Type']                 = "Art";
$lang['rb_Bank_QTY']                  = "Menge";
$lang['rb_Bank_Quality']              = "Qualität";
$lang['rb_Update']                    = "Aktualisierung";
$lang['rb_AllBankers']                = "Alle Banken";
$lang['rb_TotBankers']                = "Vermögen aller Banken";
$lang['rb_mainchar_out']              = "Uaptcharakter";
$lang['rb_note_out']                  = "Notiz";

//import
$lang['Character_Data']               = "Characterdaten";
$lang['lang_with']                    = "mit";
$lang['lang_g']                       = "g";
$lang['lang_s']                       = "s";
$lang['lang_c']                       = "k";
$lang['lang_gold']                    = "Gold";
$lang['lang_silver']                  = "Silber";
$lang['lang_copper']                  = "Kupfer";
$lang['lang_amount']                  = "Anzahl";
$lang['lang_name']                    = "Name";
$lang['lang_itemid']                  = "Gegenstands ID";
$lang['lang_quality']                 = "Qualität";
$lang['lang_skip']                    = "Überspringen";
$lang['lang_update_data']             = "Bank Daten aktualisieren";
$lang['lang_found_log']               = "Gegenstände im Log gefunden";
$lang['lang_skipped_items']           = "<b>Übersprungene</b> Gegenstände";
$lang['lang_cleared_data']            = "Alle Charakterdaten gelöscht für";
$lang['lang_added_data']              = "Charakterdaten hinzugefügt für";
$lang['lang_adding_item']             = "Gegenstand hinzugefügt: ";
$lang['lang_deleting_item']           = "Gegenstand entfernt";
$lang['rb_add_item']                  = "Gegenstand hinzufügen";
$lang['rb_insert']                    = "Gegenstand speichern";
$lang['rb_insert_banker']             = "Bankier hinzufügen";
$lang['rb_add_banker_l']              = "Bankier hinzufügen";
$lang['rb_money_val']                 = "Kosten in Gold";
$lang['rb_dkp_val']                   = "Kosten in DKP";
$lang['rb_mainchar']                  = "Name des Hauptcharakters";
$lang['rb_note']                      = "Notiz zu diesem Bankier";

// Result page
$lang['rb_user_link']                 = "Zurück zur vorherigen Seite";
$lang['Lang_actions_performed']       = "Aktionen ausgeführt";

// acl shit
$lang['rb_add_acl']                   = "Buchung hinzufügen";
$lang['rb_acl_action']                = "Buchungsart";
$lang['rb_ac_spent']                  = "Gespendet";
$lang['rb_ac_got']                    = "Erhalten";
$lang['rb_item_name']                 = "Name des Gegenstands";
$lang['rb_acl_save']                  = "Buchung speichern";
$lang['rb_list_acl']                  = "Alle Buchungen auflisten";
$lang['rb_char_name']                 = "Mitgliedsname";
$lang['rb_id']                        = "ID";
$lang['rb_acl']                       = "Gegenstandskonten";
$lang['rb_banker']                    = "Bankier";
$lang['rb_char_data']                 = "Charakterdaten";
$lang['itemcost_money']               = "Itemkosten (Gold)";
$lang['itemcost_dkp']                 = "Itemkosten (DKP)";
$lang['rb_adjust_reason']             = "von RaidBank erhalten";

// Log things
$lang['action_rbacl_added']           = "Buchung hinzugefügt";
$lang['action_rbacl_del']             = "Buchung(en) gelöscht";
$lang['action_rb_imported']           = "RaidBanker Log importiert";
$lang['action_rbbank_del']            = "Bankier gelöscht";

// Proprity
$lang['rb_priority']                  = "Priorität";
$lang['rb_prio_4']                    = "sehr hoch";
$lang['rb_prio_3']                    = "hoch";
$lang['rb_prio_2']                    = "mittel";
$lang['rb_prio_1']                    = "niedrig";
$lang['rb_prio_0']                    = "keine";

//edit
$lang['admin_delete_bank_success']    = "Bankier erfolgreich gelöscht.";

// configuration
$lang['rb_header_global']             = "RaidBanker Einstellungen";
$lang['rb_use_itemstats']             = "Benutze Itemstats";
$lang['rb_hide_banker']               = "Andere Banker nach Auswahl eines Bankers verstecken";
$lang['rb_hide_money']                = "Zeige Bankvermögen (wenn aus: Keine Goldanzeige)";
$lang['rb_no_banker']                 = "Alle Banken zusammenfassen";
$lang['rb_is_cache']                  = "Itemstats Cache: wenn 'true' werden die Gegenstandsinfos durch einen Klick auf den Gegenstand geladen.";
$lang['rb_is_path']                   = "Pfad zu Itemstats";
$lang['rb_saved']                     = "Die Einstellungen wurden erfolgreich gespeichert";
$lang['rb_failed']                    = "Beim Speichern der Einstellungen ist ein Fehler aufgetreten. Bitte versuche es erneut. Sollte dieser Fehler nochmals auftreten, kontaktiere Bitte einen Administrator.";
$lang['rb_info_box']                  = "Information";
$lang['rb_list_lang']                 = "Gegenstandssprache";
$lang['rb_locale_de']                 = "Deutsch";
$lang['rb_locale_en']                 = "Englisch";
$lang['rb_show_tooltip']              = "Zeige Info-Tooltips<br />(ACHTUNG: Ladzeiten können länger sein!)";
$lang['rb_auto_adjust']               = "Automatische DKP Korrektur bei Itemvergabe";
$lang['rb_is_oldstyle']								= "OldStyle Layout: Zeige die Gegenstände jedes Bankiers (nicht nach Gegenständen Gruppieren): Beim Einschalten dieser Funktion werden Megrfachnennungen von Gegegnständen vor kommen.";

//filter translations
$lang['rb_filter_banker']             = "Bankier auswählen";
$lang['rb_filter_type']               = "Gegenstandsart auswählen";
$lang['rb_filter_prio']               = "Priorität auswählen";

// View Item PopUP
$lang['rb_char_got']                  = "Gegenstand gekauft von";
$lang['rb_char_spent']                = "Gegenstand gespendet von";
$lang['rb_gold_value']                = "Kosten in Gold";
$lang['rb_dkp_value']                 = "Kosten in DKP";
$lang['rb_total_amount']              = "Gesamte Anzahl";
$lang['rb_dkp']                       = "DKP";

// About dialog
$lang['rb_created by']                = "geschrieben von";
$lang['rb_contact_info']              = "Kontaktinformationen";
$lang['rb_url_personal']              = "Privat";
$lang['rb_url_web']                   = "Web";
$lang['rb_sponsors']                  = "Spender";
$lang['rb_dialog_header']						  = "Über RaidBanker";
$lang['rb_additions']                 = "Beiträge zum PlugIn";
$lang['rb_loading']                   = "Seite wird geladen";

// Update Checker Part
$lang['rb_changelog_url']							= 'Changelog';
$lang['rb_updated_date']							= 'Veröffentlicht am';
$lang['rb_timeformat']								= 'd.m.Y';
$lang['rb_release_level']							= 'Releaseart';
$lang['rb_noserver']                  = 'Beim Versuch den Updateserver zu kontaktieren trat ein Fehler auf. Entweder dein Host erlaubt keine ausgehenden
                                        Verbindungen, oder es bestehen Netzwerkprobleme. Bitte besuche das EQDKP Plugin Forum um sicherzustellen, dass du die neuste Version am laufen hast.';
$lang['rb_update_available_p1']       = 'Bitte aktualisiere dein Raidbanker Plugin.';
$lang['rb_update_available_p2']       = 'Deine installierte Version ist';
$lang['rb_update_available_p3']       = 'und die aktuellste Version ist';
$lang['rb_update_url']                = 'Zur Downloadseite';
?>