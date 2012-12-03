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
 * Chinese Simp  converted by  Aoiete     aoiete@gmail.com    WWW.Replays.Net 
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}
 
// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['ENCODING'] = 'gb2312';
$lang['XML_LANG'] = 'cn';

// Titles
$lang['addadj_title']         = 'Ìí¼ÓÍÅ¶Óµ÷½Ú';
$lang['addevent_title']       = 'Ìí¼ÓÊÂ¼ş';
$lang['addiadj_title']        = 'Ìí¼Ó¸öÈËµ÷½Ú';
$lang['additem_title']        = 'Ìí¼ÓÎïÆ·¹ºÂò';
$lang['addmember_title']      = 'Ìí¼Ó¹«»á³ÉÔ±';
$lang['addnews_title']        = 'Ìí¼ÓĞÂÎÅÌõÄ¿';
$lang['addraid_title']        = 'Ìí¼ÓÒ»¸öRaid';
$lang['addturnin_title']      = "Ìí¼Ó Turn-in - Step %1\$d";
$lang['admin_index_title']    = 'WOWdkp ¹ÜÀí';
$lang['config_title']         = '½Å±¾ ÅäÖÃ';
$lang['manage_members_title'] = '¹ÜÀí¹«»á³ÉÔ±';
$lang['manage_users_title']   = 'ÓÃ»§ÕËºÅºÍÈ¨ÏŞ';
$lang['parselog_title']       = '½âÎöÈÕÖ¾ÎÄ¼ş';
$lang['plugins_title']        = '²å¼ş¹ÜÀí';
$lang['styles_title']         = '·ç¸ñ¹ÜÀí';
$lang['viewlogs_title']       = 'ÈÕÖ¾ÔÄ¶Á';

// Page Foot Counts
$lang['listusers_footcount']             = "... ÕÒµ½ %1\$d Î»ÓÃ»§ / %2\$d Î»Ã¿Ò³";
$lang['manage_members_footcount']        = "... ÕÒµ½ %1\$d Î»»áÔ±";
$lang['online_footcount']                = "... %1\$d Î»»áÔ±ÔÚÏß";
$lang['viewlogs_footcount']              = "... ÕÒµ½ %1\$d ¸öÈÕÖ¾ / %2\$d ¸öÃ¿Ò³";

// Submit Buttons
$lang['add_adjustment'] = 'Ìí¼Ó µ÷Õû¶î';
$lang['add_account'] = 'Ìí¼ÓÕËºÅ';
$lang['add_event'] = 'Add events';
$lang['add_item'] = 'Ìí¼ÓÎïÆ·';
$lang['add_member'] = 'Ìí¼Ó»áÔ±';
$lang['add_news'] = 'Ìí¼ÓĞÂÎÅ';
$lang['add_raid'] = 'Add Raid';
$lang['add_style'] = 'Ìí¼Ó·ç¸ñ';
$lang['add_turnin'] = 'Ìí¼ÓTurn-in';
$lang['delete_adjustment'] = 'É¾³ıµ÷Õû¶î';
$lang['delete_event'] = 'É¾³ıÊÂ¼ş';
$lang['delete_item'] = 'É¾³ıÎïÆ·';
$lang['delete_member'] = 'É¾³ı»áÔ±';
$lang['delete_news'] = 'É¾³ıĞÂÎÅ';
$lang['delete_raid'] = 'É¾³ıRaid';
$lang['delete_selected_members'] = 'É¾³ıÑ¡¶¨»áÔ±';
$lang['delete_style'] = 'É¾³ı·ç¸ñÑùÊ½';
$lang['mass_delete'] = 'ÈºÌåÉ¾³ı';
$lang['mass_update'] = 'ÈºÌå¸üĞÂ';
$lang['parse_log'] = '½âÎöÈÕÖ¾';
$lang['search_existing'] = 'ËÑË÷´æÔÚÊı¾İ';
$lang['select'] = 'Ñ¡Ôñ';
$lang['transfer_history'] = '×ªÒÆÀúÊ·¼ÇÂ¼';
$lang['update_adjustment'] = '¸üĞÂµ÷½Ú';
$lang['update_event'] = '¸üĞÂÊÂ¼ş';
$lang['update_item'] = '¸üĞÂÎïÆ·';
$lang['update_member'] = '¸üĞÂ»áÔ±';
$lang['update_news'] = '¸üĞÂĞÂÎÅ';
$lang['update_raid'] = '¸üĞÂRaid';
$lang['update_style'] = '¸üĞÂ·ç¸ñÑùÊ½';

// Misc
$lang['account_enabled'] = 'ÕËºÅ¼¤»î';
$lang['adjustment_value'] = 'µ÷½ÚµãÊıÖµ';
$lang['adjustment_value_note'] = 'You can use negative Value';
$lang['code'] = '¼ÓÂë';
$lang['contact'] = 'ÁªÏµÈË';
$lang['create'] = '´´½¨';
$lang['found_members'] = "½âÎö %1\$d ¼¸ĞĞ, ÕÒµ½ %2\$d Î»³ÉÔ±";
$lang['headline'] = '´ó±êÌâ';
$lang['hide'] = 'Òş²Ø?';
$lang['install'] = '°²×°';
$lang['item_search'] = 'ÎïÆ·ËÑË÷';
$lang['list_prefix'] = 'Ç°×ºÁĞ±í';
$lang['list_suffix'] = 'ºó×ºÁĞ±í';
$lang['logs'] = 'ÈÕÖ¾';
$lang['log_find_all'] = '²éÕÒËùÓĞ (°üÀ¨ÄäÃû)';
$lang['manage_members'] = '»áÔ±¹ÜÀí';
$lang['manage_plugins'] = '²å¼ş¹ÜÀí';
$lang['manage_users'] = 'ÓÃ»§¹ÜÀí';
$lang['mass_update_note'] = 'Èç¹ûÄãÏëÈÃËùÓĞÄãÑ¡ÔñÏîÄ¿µÃµ½Ó¦ÓÃºÍ¸üĞÂ,ÇëÓÃÕâĞ©¿ØÖÆÆ÷À´¸Ä±äÊôĞÔ. È»ºóÑ¡Ôñ"ÈºÌå¸üĞÂ".
                             É¾³ıËùÑ¡ÕËºÅ, °´ "ÈºÌåÉ¾³ı".';
$lang['members'] = '»áÔ±';
$lang['member_rank'] = '»áÔ±¼¶±ğ';
$lang['message_body'] = 'ÏûÏ¢Ö÷Ìå';
$lang['results'] = "%1\$d ½á¹û (\"%2\$s\")";
$lang['search'] = 'search';
$lang['search_members'] = '²éÕÒ»áÔ±';
$lang['should_be'] = 'Ó¦¸ÃÊÇ';
$lang['styles'] = '·ç¸ñÑùÊ½';
$lang['title'] = '±êÌâ';
$lang['uninstall'] = 'Ğ¶ÔØ';
$lang['update_date_to'] = "¸üĞÂÈÕÆÚÖÁ<br />%1\$s?";
$lang['version'] = '°æ±¾';
$lang['x_members_s'] = "%1\$d »áÔ±";
$lang['x_members_p'] = "%1\$d »áÔ±s";

// Permission Messages
$lang['noauth_a_event_add']    = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼ÓÊÂ¼ş.';
$lang['noauth_a_event_upd']    = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂÊÂ¼ş.';
$lang['noauth_a_event_del']    = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ıÊÂ¼ş.';
$lang['noauth_a_groupadj_add'] = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼ÓÍÅ¶Óµ÷½Ú.';
$lang['noauth_a_groupadj_upd'] = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂÍÅ¶Óµ÷½Ú.';
$lang['noauth_a_groupadj_del'] = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ıÍÅ¶Óµ÷½Ú.';
$lang['noauth_a_indivadj_add'] = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼Ó¸öÈËµ÷½Ú.';
$lang['noauth_a_indivadj_upd'] = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂ¸öÈËµ÷½Ú.';
$lang['noauth_a_indivadj_del'] = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ı¸öÈËµ÷½Ú.';
$lang['noauth_a_item_add']     = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼ÓÎïÆ·.';
$lang['noauth_a_item_upd']     = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂÎïÆ·.';
$lang['noauth_a_item_del']     = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ıÎïÆ·.';
$lang['noauth_a_news_add']     = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼ÓĞÂÎÅÌõÄ¿.';
$lang['noauth_a_news_upd']     = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂĞÂÎÅÌõÄ¿.';
$lang['noauth_a_news_del']     = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ıĞÂÎÅÌõÄ¿.';
$lang['noauth_a_raid_add']     = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼ÓRAIDs.';
$lang['noauth_a_raid_upd']     = 'ÄãÃ»ÓĞÈ¨ÏŞ¸üĞÂRAIDs.';
$lang['noauth_a_raid_del']     = 'ÄãÃ»ÓĞÈ¨ÏŞÉ¾³ıRAIDs.';
$lang['noauth_a_turnin_add']   = 'ÄãÃ»ÓĞÈ¨ÏŞÌí¼Ó turn-ins.';
$lang['noauth_a_config_man']   = 'ÄãÃ»ÓĞÈ¨ÏŞ¹ÜÀíDKPÏµÍ³ ³£¹æÉèÖÃã.';
$lang['noauth_a_members_man']  = 'ÄãÃ»ÓĞÈ¨ÏŞ¹ÜÀí¹«»á³ÉÔ±.';
$lang['noauth_a_plugins_man']  = 'ÄãÃ»ÓĞÈ¨ÏŞ¹ÜÀíÏµÍ³²å¼ş.';
$lang['noauth_a_styles_man']   = 'ÄãÃ»ÓĞÈ¨ÏŞ¹ÜÀíÏµÍ³·ç¸ñÑùÊ½.';
$lang['noauth_a_users_man']    = 'ÄãÃ»ÓĞÈ¨ÏŞ¹ÜÀíÓÃ»§ÕËºÅÉèÖÃ.';
$lang['noauth_a_logs_view']    = 'ÄãÃ»ÓĞÈ¨ÏŞ²é¿´ÏµÍ³ÈÕÖ¾.';

// Submission Success Messages
$lang['admin_add_adj_success']               = "A %1\$s adjustment of %2\$.2f has been added to the database for your guild.";
$lang['admin_add_admin_success']             = "An e-mail has been sent to %1\$s with their administrative information.";
$lang['admin_add_event_success']             = "A value preset of %1\$s for a raid on %2\$s has been added to the database for your guild.";
$lang['admin_add_iadj_success']              = "An individual %1\$s adjustment of %2\$.2f for %3\$s has been added to the database for your guild.";
$lang['admin_add_item_success']              = "An item purchase entry for %1\$s, purchased by %2\$s for %3\$.2f has been added to the database for your guild.";
$lang['admin_add_member_success']            = "%1\$s has been added as a member of your guild.";
$lang['admin_add_news_success']              = 'The news entry has been added to the database for your guild.';
$lang['admin_add_raid_success']              = "The %1\$d/%2\$d/%3\$d raid on %4\$s has been added to the database for your guild.";
$lang['admin_add_style_success']             = 'The new style has been added successfully.';
$lang['admin_add_turnin_success']            = "%1\$s has been transferred from %2\$s to %3\$s.";
$lang['admin_delete_adj_success']            = "The %1\$s adjustment of %2\$.2f has been deleted from the database for your guild.";
$lang['admin_delete_admins_success']         = "The selected administrators have been deleted.";
$lang['admin_delete_event_success']          = "The value preset of %1\$s for a raid on %2\$s has been deleted from the database for your guild.";
$lang['admin_delete_iadj_success']           = "The individual %1\$s adjustment of %2\$.2f for %3\$s has been deleted from the database for your guild.";
$lang['admin_delete_item_success']           = "The item purchase entry for %1\$s, purchased by %2\$s for %3\$.2f has been deleted from the database for your guild.";
$lang['admin_delete_members_success']        = "%1\$s, including all of his/her history, has been deleted from the database for your guild.";
$lang['admin_delete_news_success']           = 'The news entry has been deleted from the database for your guild.';
$lang['admin_delete_raid_success']           = 'The raid and any items associated with it have been deleted from the database for your guild.';
$lang['admin_delete_style_success']          = 'The style has been deleted successfully.';
$lang['admin_delete_user_success']           = "The account with a username of %1\$s has been deleted.";
$lang['admin_set_perms_success']             = "All administrative permissions have been updated.";
$lang['admin_transfer_history_success']      = "All of %1\$s\'s history has been transferred to %2\$s and %1\$s has been deleted from the database for your guild.";
$lang['admin_update_account_success']        = "Your account settings have been updated in the database.";
$lang['admin_update_adj_success']            = "The %1\$s adjustment of %2\$.2f has been updated in the database for your guild.";
$lang['admin_update_event_success']          = "The value preset of %1\$s for a raid on %2\$s has been updated in the database for your guild.";
$lang['admin_update_iadj_success']           = "The individual %1\$s adjustment of %2\$.2f for %3\$s has been updated in the database for your guild.";
$lang['admin_update_item_success']           = "The item purchase entry for %1\$s, purchased by %2\$s for %3\$.2f has been updated in the database for your guild.";
$lang['admin_update_member_success']         = "Membership settings for %1\$s have been updated.";
$lang['admin_update_news_success']           = 'The news entry has been updated in the database for your guild.';
$lang['admin_update_raid_success']           = "The %1\$d/%2\$d/%3\$d raid on %4\$s has been updated in the database for your guild.";
$lang['admin_update_style_success']          = '·ç¸ñÑùÊ½ÒÑ¾­³É¹¦¸üĞÂ.';

$lang['admin_raid_success_hideinactive']     = 'ÕıÔÚ¸üĞÂ ¼¤»î/·Ç¼¤»î Íæ¼ÒµÄ×´Ì¬...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'È·¶¨É¾³ıÍÅ¶Óµ÷½Ú?';
$lang['confirm_delete_admins']  = 'È·¶¨É¾³ıÑ¡¶¨¹ÜÀíÔ±?';
$lang['confirm_delete_event']   = 'È·¶¨É¾³ıÕâ¸öÊÂ¼ş?';
$lang['confirm_delete_iadj']    = 'È·¶¨É¾³ı¸öÈËµ÷½Ú?';
$lang['confirm_delete_item']    = 'È·¶¨É¾³ıÑ¡¶¨ÎïÆ·?';
$lang['confirm_delete_members'] = 'È·¶¨É¾³ıÒÔÏÂ»áÔ±?';
$lang['confirm_delete_news']    = 'È·¶¨É¾³ıÕâÌõĞÂÎÅ?';
$lang['confirm_delete_raid']    = 'È·¶¨É¾³ıÕâ¸öRaid?';
$lang['confirm_delete_style']   = 'È·¶¨É¾³ıÕâ¸ö·ç¸ñÑùÊ½?';
$lang['confirm_delete_users']   = 'È·¶¨É¾³ıÒÔÏÂÓÃ»§ÕËºÅ?';

// Log Actions
$lang['action_event_added']      = 'ÒÑ¾­Ìí¼ÓµÄÊÂ¼ş';
$lang['action_event_deleted']    = 'ÒÑ¾­É¾³ıµÄÊÂ¼ş';
$lang['action_event_updated']    = 'ÒÑ¾­¸üĞÂµÄÊÂ¼ş';
$lang['action_groupadj_added']   = 'ÒÑ¾­Ìí¼ÓµÄÍÅ¶Óµ÷½Ú';
$lang['action_groupadj_deleted'] = 'ÒÑ¾­É¾³ıµÄÍÅ¶Óµ÷½Ú';
$lang['action_groupadj_updated'] = 'ÒÑ¾­¸üĞÂµÄÍÅ¶Óµ÷½Ú';
$lang['action_history_transfer'] = '»áÔ±ÀúÊ·¼ÇÂ¼×ªÒÆ';
$lang['action_indivadj_added']   = 'ÒÑ¾­Ìí¼ÓµÄ¸öÈËµ÷½Ú';
$lang['action_indivadj_deleted'] = 'ÒÑ¾­É¾³ıµÄ¸öÈËµ÷½Ú';
$lang['action_indivadj_updated'] = 'ÒÑ¾­¸üĞÂµÄ¸öÈËµ÷½Ú';
$lang['action_item_added']       = 'ÒÑ¾­Ìí¼ÓµÄÎïÆ·';
$lang['action_item_deleted']     = 'ÒÑ¾­É¾³ıµÄÎïÆ·';
$lang['action_item_updated']     = 'ÒÑ¾­¸üĞÂµÄÎïÆ·';
$lang['action_member_added']     = 'ÒÑ¾­Ìí¼ÓµÄ»áÔ±';
$lang['action_member_deleted']   = 'ÒÑ¾­É¾³ıµÄ»áÔ±';
$lang['action_member_updated']   = 'ÒÑ¾­¸üĞÂµÄ»áÔ±';
$lang['action_news_added']       = 'ÒÑ¾­Ìí¼ÓµÄĞÂÎÅ';
$lang['action_news_deleted']     = 'ÒÑ¾­É¾³ıµÄĞÂÎÅ';
$lang['action_news_updated']     = 'ÒÑ¾­¸üĞÂµÄĞÂÎÅ';
$lang['action_raid_added']       = 'ÒÑ¾­Ìí¼ÓµÄRAID';
$lang['action_raid_deleted']     = 'ÒÑ¾­É¾³ıµÄRAID';
$lang['action_raid_updated']     = 'ÒÑ¾­¸üĞÂµÄRAID';
$lang['action_turnin_added']     = 'ÒÑ¾­Ìí¼ÓTurn-in';

// Before/After
$lang['adjustment_after']  = 'Adjustment After';
$lang['adjustment_before'] = 'Adjustment Before';
$lang['attendees_after']   = 'Attendees After';
$lang['attendees_before']  = 'Attendees Before';
$lang['buyers_after']      = 'Buyer After';
$lang['buyers_before']     = 'Buyer Before';
$lang['class_after']       = 'Class After';
$lang['class_before']      = 'Class Before';
$lang['earned_after']      = 'Earned After';
$lang['earned_before']     = 'Earned Before';
$lang['event_after']       = 'Event After';
$lang['event_before']      = 'Event Before';
$lang['headline_after']    = 'Headline After';
$lang['headline_before']   = 'Headline Before';
$lang['level_after']       = 'Level After';
$lang['level_before']      = 'Level Before';
$lang['members_after']     = 'Members After';
$lang['members_before']    = 'Members Before';
$lang['message_after']     = 'Message After';
$lang['message_before']    = 'Message Before';
$lang['name_after']        = 'Name After';
$lang['name_before']       = 'Name Before';
$lang['note_after']        = 'Note After';
$lang['note_before']       = 'Note Before';
$lang['race_after']        = 'Race After';
$lang['race_before']       = 'Race Before';
$lang['raid_id_after']     = 'Raid ID After';
$lang['raid_id_before']    = 'Raid ID Before';
$lang['reason_after']      = 'Reason After';
$lang['reason_before']     = 'Reason Before';
$lang['spent_after']       = 'Spent After';
$lang['spent_before']      = 'Spent Before';
$lang['value_after']       = 'Value After';
$lang['value_before']      = 'Value Before';

// Configuration
$lang['general_settings'] = '³£¹æÉèÖÃ';
$lang['guildtag'] = '¹«»áÃû³Æ/±êÇ©';
$lang['guildtag_note'] = 'ÔÚ×î½üµÄ¸÷Ò³ÖĞÊ¹ÓÃµÄ±êÌâ';
$lang['parsetags'] = '½âÎö¹«»á±êÇ©';
$lang['parsetags_note'] = 'µ±½âÎöRAID ÈÕÖ¾Ê±, ÒÔÏÂÁĞ³öµÄ½«ÒÔÑ¡ÏîĞÎÊ½±»Ó¦ÓÃ.';
$lang['domain_name'] = 'ÓòÃû';
$lang['server_port'] = '·şÎñÆ÷¶Ë¿Ú';
$lang['server_port_note'] = 'ÄãµÄÍøÒ³·şÎñÆ÷¶Ë¿Ú Í¨³£Îª 80';
$lang['script_path'] = '½Å±¾Â·¾¶';
$lang['script_path_note'] = 'dkpÏµÍ³µÄ·ÅÖÃÂ·¾¶, Ïà¶ÔÓÚÓòÃû';
$lang['site_name'] = 'Õ¾µãÃû³Æ';
$lang['site_description'] = 'Õ¾µãÃèÊö';
$lang['point_name'] = 'µãÊıÃû³Æ';
$lang['point_name_note'] = '¾ÙÀı: DKP, RP, µÈµÈ.';
$lang['enable_account_activation'] = 'ÆğÓÃÕËºÅ¼¤»î';
$lang['none'] = 'ÎŞ';
$lang['admin'] = '¹ÜÀíÔ±';
$lang['default_language'] = 'Ä¬ÈÏÓïÑÔ';
$lang['default_style'] = 'Ä¬ÈÏ·ç¸ñÑùÊ½';
$lang['default_page'] = 'Ä¬ÈÏÊ×Ò³';
$lang['hide_inactive'] = 'Ó®²ØÃ»ÓĞ¼¤»îµÄ»áÔ±';
$lang['hide_inactive_note'] = 'Ó®²Ø ÔÚÃ»ÓĞºÜ¶à»î¶¯Ê±ÆÚÃ»ÓĞ²Î¼ÓRaid µÄ»áÔ±?';
$lang['inactive_period'] = '·Ç»îÔ¾Ê±ÆÚ';
$lang['inactive_period_note'] = 'È·¶¨¶àÉÙÊ±¼ä»áÔ±²»²Î¼ÓRaid ÈÔÈ»ÊÓÎª»îÔ¾»áÔ±';
$lang['inactive_point_adj'] = '·Ç»îÔ¾µãÊıµ÷½Ú';
$lang['inactive_point_adj_note'] = 'µ±³ÉÎª·Ç»îÔ¾»áÔ±ĞèÒªµ÷½ÚµÄµãÊı.';
$lang['active_point_adj'] = '»îÔ¾µãÊıµ÷½Ú';
$lang['active_point_adj_note'] = 'µ±³ÉÎª»îÔ¾»áÔ±ĞèÒªµ÷½ÚµÄµãÊı.';
$lang['enable_gzip'] = 'ÔÊĞí Gzip Ñ¹ËõĞÎÊ½';
$lang['show_item_stats'] = 'ÏÔÊ¾ÎïÆ·×´Ì¬';
$lang['show_item_stats_note'] = 'ÊÔÍ¼´ÓÍøÉÏ»ñÈ¡ÎïÆ·×´Ì¬, ¿ÉÄÜÓ°Ïìµ½Ä³Ğ©Ò³ÃæµÄËÙ¶È';
$lang['default_permissions'] = 'Ä¬ÈÏÈ¨ÏŞ';
$lang['default_permissions_note'] = 'ÕâĞ©È¨ÏŞÊÇÈçÏÂÓÃ»§Ê¹ÓÃ1 Ã»ÓĞµÇÂ¼ÓÃ»§ 2 ĞÂ×¢²áÓÃ»§.  Ñ¡ÏîÒÔ<b>´ÖÌå</b>ÏÔÊ¾µÄÊÇ¹ÜÀíÔ±È¨ÏŞ
																		 ½¨Òé²»Òª°ÑÕâĞ©Ñ¡ÏîÉè³ÉÄ¬ÈÏÖµ, <i>Ğ±Ìå×Ö</i>ÊÇ¸ø×¨ÃÅ²å¼şÊ¹ÓÃ.  Äã¿ÉÒÔÔÚÎ´À´¸Ä±äÏàÓ¦µÄ¸öÈËÈ¨ÏŞÀ´¹ÜÀíÓÃ»§.';
$lang['plugins'] = '²å¼ş';
$lang['cookie_settings'] = 'Cookie ÉèÖÃ';
$lang['cookie_domain'] = 'Cookie ÓòÃû';
$lang['cookie_name'] = 'Cookie Ãû×Ö';
$lang['cookie_path'] = 'Cookie Â·¾¶';
$lang['session_length'] = 'Session ³¤¶È (Ãë)';
$lang['email_settings'] = 'E-Mail ÉèÖÃ';
$lang['admin_email'] = '¹ÜÀíÔ± E-Mail µØÖ·';

// Admin Index
$lang['anonymous'] = 'ÄäÃûÓÃ»§';
$lang['database_size'] = 'Êı¾İ¿â´óĞ¡';
$lang['eqdkp_started'] = 'EQdkpÆğµã';
$lang['ip_address'] = 'IPµØÖ·';
$lang['items_per_day'] = 'Æ½¾ùÃ¿ÌìµÄÎïÆ·';
$lang['last_update'] = '×îºóµÄ¸üĞÂ';
$lang['location'] = 'Î»ÖÃ';
$lang['new_version_notice'] = "EQdkp version %1\$s is <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">available for download</a>.";
$lang['number_of_items'] = 'ÎïÆ·µÄÊıÁ¿';
$lang['number_of_logs'] = 'ÈÕÖ¾µÄÊıÁ¿';
$lang['number_of_members'] = 'raid´ÎÊı (»îÔ¾µÄ / ·Ç»îÔ¾µÄ)';
$lang['number_of_raids'] = 'raid´ÎÊı';
$lang['raids_per_day'] = 'Æ½¾ùÃ¿ÌìµÄraid';
$lang['statistics'] = 'Í³¼Æ±í';
$lang['totals'] = '×ÜÊı';
$lang['version_update'] = '°æ±¾¸üĞÂ';
$lang['who_online'] = 'ÔÚÏÈÓÃ»§';

// Style Management
$lang['style_settings'] = '·ç¸ñÑùÊ½ÉèÖÃ';
$lang['style_name'] = '·ç¸ñÑùÊ½Ãû×Ö';
$lang['template'] = 'Ä£°å';
$lang['element'] = 'ÔªËØ';
$lang['background_color'] = '±³¾°ÑÕÉ«';
$lang['fontface1'] = '×ÖÌåÍâ¹Û1';
$lang['fontface1_note'] = 'Ä¬ÈÏ×ÖÌåÍâ¹Û';
$lang['fontface2'] = '×ÖÌåÍâ¹Û2';
$lang['fontface2_note'] = 'ÊäÈë¿òÄÚµÄ×ÖÌåÍâ¹Û';
$lang['fontface3'] = '×ÖÌåÍâ¹Û3';
$lang['fontface3_note'] = 'µ±Ç°Ã»±»Ê¹ÓÃµÄ';
$lang['fontsize1'] = '×ÖÌå´óĞ¡1';
$lang['fontsize1_note'] = 'Ğ¡×ÖÌål';
$lang['fontsize2'] = '×ÖÌå´óĞ¡2';
$lang['fontsize2_note'] = 'ÖĞ×ÖÌå2';
$lang['fontsize3'] = '×ÖÌå´óĞ¡3';
$lang['fontsize3_note'] = '´ó×ÖÌå3';
$lang['fontcolor1'] = '×ÖÌåÑÕÉ«1';
$lang['fontcolor1_note'] = 'Ä¬ÈÏÑÕÉ«';
$lang['fontcolor2'] = '×ÖÌåÑÕÉ«2';
$lang['fontcolor2_note'] = 'Íâ±í¸ñÊ¹ÓÃµÄÑÕÉ«(²Ëµ¥, ±êÌâ, °æÈ¨)';
$lang['fontcolor3'] = '×ÖÌåÑÕÉ«3';
$lang['fontcolor3_note'] = 'ÊäÈë¿òÇøÓòÄÚ²¿µÄ×ÖÌåÑÕÉ«';
$lang['fontcolor_neg'] = '¸ºÊıµÄ×ÖÌåÑÕÉ«';
$lang['fontcolor_neg_note'] = 'Ó¦ÓÃÓë¸ºÊıÊı×ÖµÄ×ÖÌåÑÕÉ«?';
$lang['fontcolor_pos'] = 'ÕıÊıµÄ×ÖÌåÑÕÉ«';
$lang['fontcolor_pos_note'] = 'Ó¦ÓÃÓëÕıÊıÊı×ÖµÄ×ÖÌåÑÕÉ«?';
$lang['body_link'] = 'Á´½ÓÑÕÉ«';
$lang['body_link_style'] = 'Á´½ÓÑùÊ½';
$lang['body_hlink'] = 'Ğü¸¡Á´½ÓÑÕÉ«';
$lang['body_hlink_style'] = 'Ğü¸¡Á´½ÓÑùÊ½';
$lang['header_link'] = '±êÌâÁ´½Ó';
$lang['header_link_style'] = '±êÌâÁ´½ÓÑùÊ½';
$lang['header_hlink'] = 'Ğü¸¡±êÌâÁ´½Ó';
$lang['header_hlink_style'] = 'Ğü¸¡µÄ±êÌâÁ´½ÓÑùÊ½';
$lang['tr_color1'] = '±í¸ñĞĞÑÕÉ«1';
$lang['tr_color2'] = '±í¸ñĞĞÑÕÉ«2';
$lang['th_color1'] = '±í¸ñ±êÌâÑÕÉ«';
$lang['table_border_width'] = '±í¸ñ±ß¿ò¿í¶È';
$lang['table_border_color'] = '±í¸ñ±ß¿òÑÕÉ«';
$lang['table_border_style'] = '±í¸ñ±ß¿òÑùÊ½';
$lang['input_color'] = 'ÊäÈë¿òµÄ±³¾°ÑÕÉ«';
$lang['input_border_width'] = 'ÊäÈë¿òµÄ±ß¿ò¿í¶È';
$lang['input_border_color'] = 'ÊäÈë¿òµÄ±ß¿òÑÕÉ«';
$lang['input_border_style'] = 'ÊäÈë¿òµÄ±ß¿òÑùÊ½';
$lang['style_configuration'] = '·ç¸ñÑùÊ½ÅäÖÃ';
$lang['style_date_note'] = 'For date/time fields, the syntax used is identical to the PHP <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> function.';
$lang['attendees_columns'] = '³öÏ¯Õß';
$lang['attendees_columns_note'] = '²é¿´raid³öÏ¯ÕßÊıÁ¿';
$lang['date_notime_long'] = 'ÈÕÆÚÃ»ÓĞÈ·¶¨Ê±¼ä (³¤ÆÚ)';
$lang['date_notime_short'] = 'ÈÕÆÚÃ»ÓĞÈ·¶¨Ê±¼ä (¶ÌÆÚ)';
$lang['date_time'] = 'ÈÕÆÚºÍÊ±¼ä';
$lang['logo_path'] = 'LogoÎÄ¼şÃû';

// Errors
$lang['error_invalid_adjustment'] = '²»ÊÇÒ»¸öÓĞĞ§µÄµ÷Õû.';
$lang['error_invalid_plugin']     = '²»ÊÇ¸öÓĞĞ§µÄ²å¼ş.';
$lang['error_invalid_style']      = '²»ÊÇ¸öÓĞĞ§µÄ·ç¸ñÀàĞÍ.';

// Verbose log entry lines
$lang['new_actions']           = '×î½ü¹ÜÀíÔ±²Ù×÷';
$lang['vlog_event_added']      = "%1\$s Ìí¼ÓÑ¡¶¨ÊÂ¼ş '%2\$s' ¼ÛÖµ %3\$.2f µãÊı.";
$lang['vlog_event_updated']    = "%1\$s ¸üĞÂÑ¡¶¨ÊÂ¼ş '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s É¾³ıÑ¡¶¨ÊÂ¼ş '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s Ìí¼ÓÕûÍÅ¶Óµ÷½Ú %2\$.2f µãÊı.";
$lang['vlog_groupadj_updated'] = "%1\$s ¸üĞÂÕûÍÅ¶Óµ÷½Ú %2\$.2f µãÊı.";
$lang['vlog_groupadj_deleted'] = "%1\$s É¾³ıÕûÍÅ¶Óµ÷½Ú %2\$.2f µãÊı.";
$lang['vlog_history_transfer'] = "%1\$s ×ªÒÆ %2\$s'µÄÀúÊ·¼ÇÂ¼o %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s ¶ÔÓÚ»áÔ±Ìí¼ÓÏàÓ¦µÄ¸öÈËµãÊıµ÷½Ú %2\$.2f  %3\$d .";
$lang['vlog_indivadj_updated'] = "%1\$s ¸üĞÂÏàÓ¦µÄ¸öÈËµãÊıµ÷½Ú %2\$.2f  %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s deleted an individual adjustment of %2\$.2f to %3\$s.";
$lang['vlog_item_added']       = "%1\$s Ìí¼ÓÑ¡¶¨ÎïÆ· '%2\$s' ¸øÓè %3\$d member(s) for %4\$.2f points.";
$lang['vlog_item_updated']     = "%1\$s ¸üĞÂÑ¡¶¨ÎïÆ· '%2\$s' ¸øÓè %3\$d »áÔ±.";
$lang['vlog_item_deleted']     = "%1\$s É¾³ıÑ¡¶¨ÎïÆ· '%2\$s' ¸øÓè %3\$d »áÔ±.";
$lang['vlog_member_added']     = "%1\$s Ìí¼ÓÑ¡¶¨»áÔ± %2\$s.";
$lang['vlog_member_updated']   = "%1\$s ¸üĞÂÑ¡¶¨»áÔ± %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s É¾³ıÑ¡¶¨»áÔ± %2\$s.";
$lang['vlog_news_added']       = "%1\$s Ìí¼ÓÑ¡¶¨Í·Ä¿ '%2\$s'.";
$lang['vlog_news_updated']     = "%1\$s ¸üĞÂÑ¡¶¨ÌõÄ¿ '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s É¾³ıÑ¡¶¨ÌõÄ¿ '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s Ìí¼ÓÒ»¸öraid '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s ¸üĞÂÒ»¸öraid '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s É¾³ıÒ»¸öraid '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s Ìí¼Ó turn-in µ½ %2\$s to %3\$s for '%4\$s'.";

// Location messages
$lang['adding_groupadj'] = 'Ìí¼ÓÍÅ¶Óµ÷½ÚÆ÷';
$lang['adding_indivadj'] = 'Ìí¼Ó¸öÈËµ÷½ÚÆ÷';
$lang['adding_item'] = 'Ìí¼ÓÎïÆ·';
$lang['adding_news'] = 'Ìí¼ÓĞÂÎÅÌõÄ¿';
$lang['adding_raid'] = 'Add Raid';
$lang['adding_turnin'] = 'Ìí¼ÓTurn-in';
$lang['editing_groupadj'] = '±à¼­µ÷½ÚÍÅ¶ÓĞÅÏ¢';
$lang['editing_indivadj'] = '±à¼­µ÷½Ú¸öÈËĞÅÏ¢';
$lang['editing_item'] = '±à¼­ÎïÆ·';
$lang['editing_news'] = '±à¼­ĞÂÎÅÌõÄ¿';
$lang['editing_raid'] = '±à¼­Raid';
$lang['listing_events'] = 'ÊÂ¼şÁĞ±í';
$lang['listing_groupadj'] = '³ÉÔ±µ÷½ÚÁĞ±í';
$lang['listing_indivadj'] = '¸öÈËµ÷½ÚÆ÷ÁĞ±í';
$lang['listing_itemhist'] = 'ÎïÆ·ÀúÊ·¼ÇÂ¼ÁĞ±í';
$lang['listing_itemvals'] = 'ÎïÆ·PTÖµÁĞ±í';
$lang['listing_members'] = '»áÔ±ÁĞ±í';
$lang['listing_raids'] = 'RaidÁĞ±í';
$lang['managing_config'] = '¹ÜÀíEQdkpÅäÖÃ';
$lang['managing_members'] = '¹ÜÀí¹¤»á³ÉÔ±';
$lang['managing_plugins'] = '²å¼ş¹ÜÀí';
$lang['managing_styles'] = '¹ÜÀí½çÃæ·ç¸ñ';
$lang['managing_users'] = '¹ÜÀíÓÃ»§ÕÊºÅ';
$lang['parsing_log'] = '½âÎöÈÕÖ¾';
$lang['viewing_admin_index'] = '²é¿´¹ÜÀíÔ±Ë÷Òı';
$lang['viewing_event'] = '²é¿´ÊÂ¼ş';
$lang['viewing_item'] = '²é¿´ÎïÆ·';
$lang['viewing_logs'] = '²é¿´¼ÇÂ¼';
$lang['viewing_member'] = '²é¿´»áÔ±';
$lang['viewing_mysql_info'] = '²é¿´MySQLĞÅÏ¢';
$lang['viewing_news'] = '²é¿´ĞÂÎÅ';
$lang['viewing_raid'] = '²é¿´Raid';
$lang['viewing_stats'] = '²é¿´×´Ì¬';
$lang['viewing_summary'] = '²é¿´×Ü¹²Çé¿ö';

// Help lines
$lang['b_help'] = '´ÖÌåÎÄ±¾: [b]text[/b] (alt+b)';
$lang['i_help'] = 'Ğ±ÌåÎÄ±¾: [i]text[/i] (alt+i)';
$lang['u_help'] = 'ÏÂ»®ÏßÎÄ±¾: [u]text[/u] (alt+u)';
$lang['q_help'] = 'ÒıÓÃÎÄ±¾: [quote]text[/quote] (alt+q)';
$lang['c_help'] = 'ÎÄ±¾¾ÓÖĞ: [center]text[/center] (alt+c)';
$lang['p_help'] = '²åÈëÍ¼Æ¬: [img]http://image_url[/img] (alt+p)';
$lang['w_help'] = '²åÈëURLÁ´½Ó: [url]http://url[/url] or [url=http://url]URL text[/url]  (alt+w)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Ìí¼Ó»áÔ±';
$lang['list_edit_del_member'] = 'ÁĞ±í/É¾³ı/±à¼­ »áÔ±';
$lang['edit_ranks'] = '±à¼­»áÔ±µÈ¼¶';
$lang['transfer_history'] = '×ªÒÆ»áÔ±ÀúÊ·';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'MySQLĞÅÏ¢';
$lang['eqdkp_tables'] = 'EQdkp±í¸ñ';
$lang['table_name'] = '±í¸ñÃû³Æ';
$lang['rows'] = 'ĞĞ';
$lang['table_size'] = '±í¸ñ´óĞ¡';
$lang['index_size'] = 'Ë÷Òı´óĞ¡';
$lang['num_tables'] = "%d ±í¸ñ";
?>
