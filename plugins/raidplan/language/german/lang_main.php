<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * ------------------
 * lang_main.php
 * Began: Fri August 19 2005
 * Changed: Fri September 30 2005
 *
 ******************************/

$lang['raidplan'] 												= "Raid Planner";
$lang['rp_raidplaner'] 										= "Raid Planner";

// User Menu
$lang['rp_usermenu_raidplaner']						= "Raid Planner";
$lang['rp_raid_id']                   		= "Raid ID";

// Submit Buttons
$lang['rp_wildcard_raid']									= "Freilose";

// Delete Confirmation Texts
$lang['rp_confirm_delete_subscription'] 	= "Bist Du sicher, daß Du Deine Anmeldung löschen willst?";

// Page Foot Counts
$lang['rp_listraids_footcount']						= "... %1\$d Raid(s) gefunden / %2\$d pro Seite / %3\$sZeige alle</a>";
$lang['rp_listrecentraids_footcount']               = "... %1\$d Raid(s) gefunden / letzten %2\$d Tage";

// Buttons
$lang['rp_signup']							  				= "Anmelden";
$lang['rp_bunsign']               				= "Abmelden";
$lang['rp_signoff']							  				= "Abmelden";
$lang['rp_distribute_class_set']  				= "Klassen-Verteilung speichern";
$lang['rp_class_distribution_notset']     		= "Um eine Klassenliste speichern zu können darf kein angelgter Raid verwendet werden, nimm einen NEUEN Raid ...";
$lang['rp_add_all']							  				= "Alle Mitglieder hinzufügen";

// Misc
$lang['rp_confirmed']						   	= "Bestätigt";
$lang['rp_signed']							   	= "Angemeldet";
$lang['rp_unsigned']							= "Abgemeldet";
$lang['rp_notavail']						   	= "Nicht verfügbar";
$lang['rp_needed']							   	= "Benötigt";
$lang['rp_start_time']				 		 	= "Startzeitpunkt";
$lang['rp_invite_time']				 		 	= "Einladezeitpunkt";
$lang['rp_signup_deadline']				 		= "Anmeldefrist";
$lang['rp_signup_deadline_date']	 			= "Anmeldefrist-Datum";
$lang['rp_signup_deadline_time']	 			= "Anmeldefrist-Uhrzeit";
$lang['rp_current_raid']           				= "Aktuelle Raids";
$lang['rp_recent_raid']            				= "Vergangene Raids";

$lang['rp_signup_over']            				= "Anmeldefrist abgelaufen";
$lang['rp_signup_possible']            			= "Anmeldung möglich";
$lang['rp_signup_24h']            				= "Anmeldefrist läuft in 24h ab";

// viewmember
$lang['rp_rank']                   				= "Rang";
$lang['rp_class']                  				= "Klasse";
$lang['rp_chars_of']               				= "Charakter des Spielers:";
$lang['rp_char']                  	 			= "Charakter";

//overlib windows
$lang['rp_status_header']           			= "Raid Status";
$lang['rp_status_signintime']       			= "Verbleibende Anmeldezeit:";
$lang['rp_status_closed']           			= "Anmeldung ist nun geschlossen";
$lang['rp_status_day']              			= "d";
$lang['rp_status_hours']            			= "h";
$lang['rp_status_minutes']          			= "m";
$lang['rp_note_header']             			= "Notiz";
$lang['rp_time_header']             			= "Anmeldezeit";
$lang['rp_status']           							= "Status";

//time translations
$lang['rp_time_format']             			= "%A, %d.%m.%Y, %H:%M";
$lang['rp_day_format']               			= "%A";
$lang['rp_time_short']               			= "%H:%M";
$lang['rp_local_format']                  = "German";
$lang['rp_calendar_lang']             		= "de";

$lang['rp_start']													= "Start";
$lang['rp_day']														= "Tag";
$lang['rp_invite']												= "Einladen";

// Image alternates
$lang['rp_rolled']							    			= "Gewürfelt";
$lang['rp_wildcard']						    			= "Freilos";

// Submission Success Messages
$lang['rp_update_raid_success']          	= "Der %1\$d Raid auf %2\$s wurde in der Datenbank aktualisiert.";
$lang['rp_raid_signed']										= "Mitglied %1\$s hat sich für den Raid %2\$s angemeldet.";
$lang['rp_admin_update_confimation_status']= "Anmeldung für Mitglied %1\$s wurde bestätigt.";
$lang['rp_admin_unlock_member']           = "Mitglied %1\$s wurde entsperrt.";
$lang['rp_raid_signup_deleted']           = "Anmeldung für Mitglied %1\$s wurde gelöscht.";
$lang['rp_class_distribution_set']				= "Klassen-Verteilung wurde gespeichert.";

// Submission Error Messages
$lang['rp_member_allready_subscribed']		= "Mitglied hat sich schon angemeldet. Die Änderung wurde abgebrochen.";

// AutoInvite
$lang['rp_Macro_output_Listing']          = "Makroausgabe Auflistung...";
$lang['rp_nonqued_user']                  = "Benutzer ohne Warteschlange zuerst";
$lang['rp_queued_users']                  = "Benutzer mit Warteschlange";
$lang['rp_MacroListingComplete']          = "Makroausgabe Auflistung komplett";
$lang['rp_copypaste_ig']                  = "Kopiere den obenstehenden Text in die Zwischenablage und füge in Ingame als Makro ein.";
$lang['rp_lua_created']                   = "LUA-Datei wurde hergestellt";
$lang['rp_download']                      = "Download";
$lang['rp_dl_autoinv_add']                = "(Rechtsklick, Speichern unter auswählen, Dateiname: AutoInvite.lua)";
$lang['rp_lua_output']                    = "Starte die LUA Ausgabe";
$lang['rp_no_raidid']                     = "Fehler: Es wurde keine Raid-ID angegeben";
$lang['rp_autonv_link']                   = "Autoinvite: LUA / Makro Generator";

// Error Messages
$lang['rp_error_invalid_mode_provided']		= "Es wurde kein gültiger Modus angegeben.";
$lang['rp_not_logged_in']									= "Um dich für einen Raid anzumelden musst du eingeloggt sein!";
$lang['rp_no_user_assigned']							= "Es wurden dir vom Administrator noch keine Charaktere zugewiesen!";
$lang['rp_class_distribution_not_set']				= "Klassen-Verteilung wurde nicht richtig erstellt!.";

// config things
$lang['config']           = "Einstellungen";
$lang['rp_header_global'] = "Allgemeine Raidplan Einstellungen";
$lang['rp_show_ranks']    = "Zeige Ränge im Raidplaner";
$lang['rp_short_rank']    = "Zeige nur kurze Rangbezeichnungen";
$lang['rp_send_email']    = "Email neue Raids an die Benutzer";
$lang['rp_roll_system']   = "Benutze das Würfelsystem?";
$lang['rp_wildcard_sys']  = "Benutze das Wildcardsystem?";
$lang['rp_use_css']       = "Add the .css file in the plugin's template folder";
$lang['rp_last_x_days']   = "Zeige vergangene Raids: letze x Tage";
$lang['rp_aj_secret_hash']= "Autojoin: Secret Hash";
$lang['rp_aj_path']       = "Autojoin: Pfad";

?>
