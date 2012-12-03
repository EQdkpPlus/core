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
* Chinese - Converted by zoof@263.net using http://pt.chinaeq.com
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
$lang['admin_title_prefix']   = "%1\$s %2\$s ¹ÜÀíÔ±";
$lang['listadj_title']        = 'ÍÅ¶Óµ÷½ÚÁĞ±í';
$lang['listevents_title']     = '»î¶¯Öµ';
$lang['listiadj_title']       = '¸öÈËµ÷½ÚÁĞ±í';
$lang['listitems_title']      = 'ÎïÆ·PTÖµ';
$lang['listnews_title']       = 'ĞÂÎÅÌõÄ¿';
$lang['listmembers_title']    = '±ê×¼»áÔ±';
$lang['listpurchased_title']  = 'ÎïÆ·Àú·';
$lang['listraids_title']      = 'raidsÁĞ±í';
$lang['login_title']          = 'µÇÂ¼';
$lang['message_title']        = 'EQdkp£ºĞÅÏ¢';
$lang['register_title']       = '×¢²á';
$lang['settings_title']       = 'ÕËºÅÉèÖÃ';
$lang['stats_title']          = "%1\$s ×´Ì¬";
$lang['summary_title']        = 'ĞÂÎÅÕªÒª';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "²é¿´raidÀú·¼ÍÂ¼ for %1\$s";
$lang['viewitem_title']       = "²é¿´½»Ò×Àú·¼ÍÂ¼ for %1\$s";
$lang['viewmember_title']     = "Àú· for %1\$s";
$lang['viewraid_title']       = 'raid¸ÅÒª';

// Main Menu
$lang['menu_admin_panel'] = '¹ÜÀíÃæ°å';
$lang['menu_events'] = '»î¶¯';
$lang['menu_itemhist'] = 'ÎïÆ·Àú·';
$lang['menu_itemval'] = 'ÎïÆ·PTÖµ';
$lang['menu_news'] = 'ĞÂÎÅ';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = '×¢²á';
$lang['menu_settings'] = 'ÉèÖÃ';
$lang['menu_standings'] = '»ù±¾ĞÅÏ¢';
$lang['menu_stats'] = '×´Ì¬';
$lang['menu_summary'] = 'ÕªÒª';

// Column Headers
$lang['account'] = 'ÕË»§';
$lang['action'] = '¶¯×÷';
$lang['active'] = 'ĞĞÎª';
$lang['add'] = 'Ìí¼Ó';
$lang['added_by'] = 'Ìí¼Ó By';
$lang['adjustment'] = 'µ÷½Ú';
$lang['administration'] = '¹ÜÀí';
$lang['administrative_options'] = '¹ÜÀíÉèÖÃ';
$lang['admin_index'] = '¹ÜÀíÔ±×Ò³';
$lang['attendance_by_event'] = '³öÏ¯»î¶¯';
$lang['attended'] = '³öÏ¯';
$lang['attendees'] = '³öÏ¯Õß';
$lang['average'] = 'Æ½¾ù';
$lang['backup_database'] = '±¸·İı¾İ¿â';
$lang['buyer'] = 'Âò·½';
$lang['buyers'] = '¹Ë¿ÍÃÇ';
$lang['class'] = 'Ö°Òµ';
$lang['class_distribution'] = 'Ö°Òµ·Ö²¼';
$lang['class_summary'] = "Ö°Òµ¸ÅÒª: %1\$s to %2\$s";
$lang['configuration'] = 'ÉèÖÃ';
$lang['current'] = 'µ±Ç°';
$lang['date'] = 'ÈÕÆÚ';
$lang['delete'] = 'É¾³ı';
$lang['delete_confirmation'] = 'È·ÈÏÉ¾³ı';
$lang['dkp_value'] = "%1\$s Öµ";
$lang['drops'] = 'µôÂä';
$lang['earned'] = 'ÕÈë';
$lang['enter_dates'] = '½øÈëÈÕÆÚ';
$lang['eqdkp_index'] = 'EQdkp ×Ò³';
$lang['eqdkp_upgrade'] = 'EQdkp ¸üĞÂ';
$lang['event'] = '»î¶¯';
$lang['events'] = '»î¶¯';
$lang['filter'] = '¹ıÂËÆ÷';
$lang['first'] = '×ÏÈ';
$lang['rank'] = 'Í·ÏÎ';
$lang['general_admin'] = 'ÆÕÍ¨¹ÜÀíÔ±';
$lang['get_new_password'] = 'µÃµ½Ò»¸öĞÂÃÜÂë';
$lang['group_adj'] = 'ÍÅ¶Óµ÷½Ú';
$lang['group_adjustments'] = 'ÍÅ¶Óµ÷½Ú';
$lang['individual_adjustments'] = '¸öÈËµ÷½Ú';
$lang['individual_adjustment_history'] = '¸öÈËµ÷½Ú¼ÍÂ¼';
$lang['indiv_adj'] = '¸öÈËµ÷½Ú';
$lang['ip_address'] = 'IPµØÖ·';
$lang['item'] = 'ÎïÆ·';
$lang['items'] = 'ÎïÆ·';
$lang['item_purchase_history'] = 'ÎïÆ·½»Ò×¼ÍÂ¼';
$lang['last'] = '×îºó';
$lang['lastloot'] = '×îºóµÄloot';
$lang['lastraid'] = '×îºóµÄraid';
$lang['last_visit'] = '×îºóµÄ·ÃÎ';
$lang['level'] = 'µÈ¼¶';
$lang['log_date_time'] = '¼ÍÂ¼µÄÈÕÆÚ/±¼ä';
$lang['loot_factor'] = 'LootÕß';
$lang['loots'] = 'Loots';
$lang['manage'] = '´¦Àí';
$lang['member'] = '»áÔ±';
$lang['members'] = '»áÔ±ÃÇ';
$lang['members_present_at'] = "µ±Ç°»áÔ± at %1\$s on %2\$s";
$lang['miscellaneous'] = '»ìÔÓ';
$lang['name'] = 'Ãû×Ö';
$lang['news'] = 'ÏûÏ¢';
$lang['note'] = '×¢Í';
$lang['online'] = 'ÔÚÏß';
$lang['options'] = 'ÉèÖÃ';
$lang['paste_log'] = 'Õ³ÌùÒ»¸öÒ³µ×¼ÍÂ¼';
$lang['percent'] = 'µ±Ç°µÄ';
$lang['permissions'] = 'Ğí¿É';
$lang['per_day'] = 'Ã¿Ìì';
$lang['per_raid'] = 'Ã¿´Îraid';
$lang['pct_earned_lost_to'] = '% »ñµÃµãı¿Û·Ö';
$lang['preferences'] = '²Îı';
$lang['purchase_history_for'] = "½»Ò×Àú· for %1\$s";
$lang['quote'] = 'ÒıÓÃ';
$lang['race'] = 'ÖÖ×å';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'raid³öÏ¯¼ÍÂ¼';
$lang['raids_lifetime'] = "ÓĞĞ§±¼ä (%1\$s - %2\$s)";
$lang['raids_x_days'] = "³ÖĞø %1\$d Ìì";
$lang['rank_distribution'] = 'Í·ÏÎ·ÖÅä';
$lang['recorded_raid_history'] = "raidÀú·¼ÍÂ¼ for %1\$s";
$lang['reason'] = 'Ô­Òò';
$lang['registration_information'] = '×¢²áĞÅÏ¢';
$lang['result'] = '½á¹û';
$lang['session_id'] = 'Session ID';
$lang['settings'] = 'ÉèÖÃ';
$lang['spent'] = '»¨·Ñ';
$lang['summary_dates'] = "Raid ¸ÅÒª: %1\$s to %2\$s";
$lang['themes'] = 'Ö÷Ìâ';
$lang['time'] = '±¼ä';
$lang['total'] = '×Ü¹²';
$lang['total_earned'] = 'ÕÈëºÏ¼Æ';
$lang['total_items'] = 'ÎïÆ·×Üı';
$lang['total_raids'] = 'raid×Üı';
$lang['total_spent'] = '»¨·Ñ×Üı';
$lang['transfer_member_history'] = '»áÔ±Àú·×ªÒÆ';
$lang['turn_ins'] = 'ÎïÆ·×ªÒÆ';
$lang['type'] = 'ÖÖÀà';
$lang['update'] = '¸üĞÂ';
$lang['updated_by'] = '¸üĞÂ By';
$lang['user'] = 'ÓÃ»§';
$lang['username'] = 'ÓÃ»§Ãû';
$lang['value'] = 'PTÖµ';
$lang['view'] = '²é¿´';
$lang['view_action'] = '²é¿´ĞĞÎª';
$lang['view_logs'] = '¹Û²ìÈÕÖ¾';

// Page Foot Counts
$lang['listadj_footcount']               = "... ´´½¨ %1\$d µ÷½ÚÆ÷ / %2\$d Ã¿Ò³";
$lang['listevents_footcount']            = "... ´´½¨ %1\$d »î¶¯ / %2\$d Ã¿Ò³";
$lang['listiadj_footcount']              = "... ´´½¨ %1\$d ¸öÈËµ÷½Ú / %2\$d Ã¿Ò³";
$lang['listitems_footcount']             = "... ´´½¨ %1\$d ¶ÀÌØÎïÆ· / %2\$d Ã¿Ò³";
$lang['listmembers_active_footcount']    = "... ´´½¨ %1\$d »îÔ¾»áÔ± / %2\$sShow All</a>";
$lang['listmembers_compare_footcount']   = "... ±È½Ï %1\$d »áÔ±";
$lang['listmembers_footcount']           = "... ´´½¨ %1\$d »áÔ±";
$lang['listnews_footcount']              = "... ´´½¨ %1\$d ÏûÏ¢Èë¿Ú / %2\$d Ã¿Ò³";
$lang['listpurchased_footcount']         = "... ´´½¨ %1\$d ÎïÆ· / %2\$d Ã¿Ò³";
$lang['listraids_footcount']             = "... ´´½¨ %1\$d raid(s) / %2\$d Ã¿Ò³";
$lang['stats_active_footcount']          = "... ´´½¨ %1\$d »îÔ¾»áÔ± / %2\$sÏÔ¾ËùÓĞ</a>";
$lang['stats_footcount']                 = "... ´´½¨ %1\$d »áÔ±";
$lang['viewevent_footcount']             = "... ´´½¨ %1\$d raid(s)";
$lang['viewitem_footcount']              = "... ´´½¨ %1\$d ÎïÆ·";
$lang['viewmember_adjustment_footcount'] = "... ´´½¨ %1\$d ¸öÈËµ÷½Ú";
$lang['viewmember_item_footcount']       = "... ´´½¨ %1\$d ¹ºÂòÎïÆ· / %2\$d Ã¿Ò³";
$lang['viewmember_raid_footcount']       = "... ´´½¨ %1\$d ³öÏ¯ raid(s) / %2\$d Ã¿Ò³";
$lang['viewraid_attendees_footcount']    = "... ´´½¨ %1\$d ³öÏ¯Õß";
$lang['viewraid_drops_footcount']        = "... ´´½¨ %1\$d µôÂä";

// Submit Buttons
$lang['close_window'] = '¹Ø±Õ´°¿Ú';
$lang['compare_members'] = '»áÔ±¶Ô±È';
$lang['create_news_summary'] = 'ĞÂ½¨ÏûÏ¢¸ÅÒª';
$lang['login'] = 'µÇÈë';
$lang['logout'] = 'µÇ³ö';
$lang['log_add_data'] = '±í¸ñÖĞÌí¼Óı¾İ';
$lang['lost_password'] = '¶ª§ÃÜÂë';
$lang['no'] = '·ñ';
$lang['proceed'] = '¼ÌĞø';
$lang['reset'] = 'ÖØÖÃ';
$lang['set_admin_perms'] = 'ÉèÖÃ¹ÜÀíÔ±Ğí¿É';
$lang['submit'] = 'Ìá½»';
$lang['upgrade'] = '¸üĞÂ';
$lang['yes'] = 'Ç';

// Form Element Descriptions
$lang['admin_login'] = '¹ÜÀíÔ±µÇÈë';
$lang['confirm_password'] = 'È·ÈÏÃÜÂë';
$lang['confirm_password_note'] = 'Èç¹ûÉÏÃæÒÑ¸ü¸Ä£¬Ö»ĞëÈ·ÈÏĞÂµÄÃÜÂë';
$lang['current_password'] = 'µ±Ç°ÃÜÂë';
$lang['current_password_note'] = 'Èç¹ûÏëÒª¸ü¸ÄÓÃ»§Ãû/ÃÜÂë£¬ÇëÈ·ÈÏµ±Ç°ÃÜÂë';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email µØÖ·';
$lang['ending_date'] = 'ÖÕÖ¹ÈÕÆÚ';
$lang['from'] = '´Ó';
$lang['guild_tag'] = '¹«»á±ê¾';
$lang['language'] = 'ÓïÑÔ';
$lang['new_password'] = 'ĞÂÃÜÂë';
$lang['new_password_note'] = 'Èç¹ûÏëÒª¸ü¸ÄÃÜÂë£¬Ö»ĞëÌá¹»Ò»¸öĞÂµÄÃÜÂë';
$lang['password'] = 'ÃÜÂë';
$lang['remember_password'] = '¼Ç×¡ÎÒ (cookie)';
$lang['starting_date'] = 'Æğ¼ÈÕÆÚ';
$lang['style'] = '·ç¸ñ';
$lang['to'] = 'µ½';
$lang['username'] = 'ÓÃ»§Ãû';
$lang['users'] = 'ÓÃ»§';

// Pagination
$lang['next_page'] = 'ÏÂÒ³';
$lang['page'] = 'Ò³';
$lang['previous_page'] = 'ÉÏÒ³';

// Permission Messages
$lang['noauth_default_title'] = 'È¨ÏŞ¾Ü¾ø';
$lang['noauth_u_event_list'] = 'ÄãÃ»ÓĞÁĞ³ö»î¶¯µÄÈ¨ÏŞ.';
$lang['noauth_u_event_view'] = 'ÄãÃ»ÓĞ²é¿´»î¶¯µÄÈ¨ÏŞ.';
$lang['noauth_u_item_list'] = 'ÄãÃ»ÓĞÁĞ³öÎïÆ·µÄÈ¨ÏŞ.';
$lang['noauth_u_item_view'] = 'ÄãÃ»ÓĞ²é¿´ÎïÆ·µÄÈ¨ÏŞ.';
$lang['noauth_u_member_list'] = 'ÄãÃ»ÓĞ²é¿´»áÔ±×´Ì¬µÄÈ¨ÏŞ.';
$lang['noauth_u_member_view'] = 'ÄãÃ»ÓĞ²é¿´»áÔ±Àú·µÄÈ¨ÏŞ.';
$lang['noauth_u_raid_list'] = 'ÄãÃ»ÓĞÁĞ³öraidsµÄÈ¨ÏŞ.';
$lang['noauth_u_raid_view'] = 'ÄãÃ»ÓĞ²é¿´raidsµÄÈ¨ÏŞ.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Äã¹ØÓÚÕâ¼şÎïÆ·µÄÍ¶Æ±ÒÑ¾­¼ÇÂ¼.';
$lang['update_itemvote_success'] = 'Äã¹ØÓÚÕâ¼şÎïÆ·µÄÍ¶Æ±ÒÑ¾­¸üĞÂ.';
$lang['update_settings_success'] = 'ÓÃ»§ÉèÖÃÒÑ¾­¸üĞÂ.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = '½ÇÉ«\' EQÖĞµÄĞÕÃû£¨Ö»°üº¬×ÖÄ¸£©.';
$lang['fv_already_registered_email'] = 'e-mailµØÖ·ÒÑ¾­×¢²á.';
$lang['fv_already_registered_username'] = 'ÓÃ»§ÃûÒÑ¾­×¢²á.';
$lang['fv_difference_transfer'] = 'Àú·¼ÍÂ¼×ªÒÆ±ØĞëÔÚÁ½²»Í¬ÈËÖ®¼ä.';
$lang['fv_difference_turnin'] = 'turn-in±ØĞëÔÚÁ½²»Í¬ÈËÖ®¼ä.';
$lang['fv_invalid_email'] = 'e-mailµØÖ·ÎŞĞ§.';
$lang['fv_match_password'] = 'ÃÜÂë±ØĞëÆ¥Åä.';
$lang['fv_member_associated']  = "%1\$s ÒÑ¾­¹ØÁªÆäËûÓÃ»§ÕËºÅ.";
$lang['fv_number'] = '±ØĞëÇı×Ö.';
$lang['fv_number_adjustment'] = 'µ÷½ÚÖµ±ØĞëÇı×Ö.';
$lang['fv_number_alimit'] = 'µ÷½ÚÏŞÖÆ±ØĞëÇı×Ö.';
$lang['fv_number_ilimit'] = 'ÎïÆ·ÏŞÖÆ±ØĞëÇı×Ö.';
$lang['fv_number_inactivepd'] = 'Î´¼¤»î±ÆÚ±ØĞëÇı×Ö.';
$lang['fv_number_pilimit'] = '¹ºÂòÎïÆ·ÏŞÖÆ±ØĞëÇı×Ö.';
$lang['fv_number_rlimit'] = 'raidsÏŞÖÆ±ØĞëÇı×Ö.';
$lang['fv_number_value'] = 'Öµ±ØĞëÇı×Ö.';
$lang['fv_number_vote'] = 'Í¶Æ±±ØĞëÇı×Ö.';
$lang['fv_date'] = 'Please choose a valid date from the calendar.';
$lang['fv_range_day'] = 'ÈÕ ±ØĞëÇ1-31µÄÕûı.';
$lang['fv_range_hour'] = '± ±ØĞëÇ1-23µÄÕûı.';
$lang['fv_range_minute'] = '·ÖÖÓ ±ØĞëÇ1-59µÄÕûı.';
$lang['fv_range_month'] = 'ÔÂ ±ØĞëÇ1-12µÄÕûı.';
$lang['fv_range_second'] = 'Ãë ±ØĞëÇ0-59µÄÕûı.';
$lang['fv_range_year'] = 'Äê ±ØĞëÇ´óÓÚ1998µÄÕûı.';
$lang['fv_required'] = 'Required Field';
$lang['fv_required_acro'] = '¹«»á××ÖÄ¸ËõĞ´²»ÄÜÎª¿Õ.';
$lang['fv_required_adjustment'] = 'µ÷½ÚÖµ²»ÄÜÎª¿Õ.';
$lang['fv_required_attendees'] = 'raidÖÁÉÙÒªÓĞÒ»¸ö²Î¼ÓÕß.';
$lang['fv_required_buyer'] = '±ØĞëÑ¡ÔñÒ»¸ö¹Ë¿Í.';
$lang['fv_required_buyers'] = '±ØĞëÑ¡ÔñÖÁÉÙÒ»¸ö¹Ë¿Í.';
$lang['fv_required_email'] = 'e-mailµØÖ·²»ÄÜÎª¿Õ.';
$lang['fv_required_event_name'] = 'Ò»Ïî»î¶¯±ØĞëÑ¡Ôñ.';
$lang['fv_required_guildtag'] = '¹«»á±ê¾²»ÄÜÎª¿Õ.';
$lang['fv_required_headline'] = '±êÌâ²»ÄÜÎª¿Õ.';
$lang['fv_required_inactivepd'] = 'If the hide inactive members field is set to Yes, a value for the inactive period must also be set.';
$lang['fv_required_item_name'] = 'ÎïÆ·Ãû³ÆÏî±ØĞëÌîĞ´£¬»òÕßÑ¡ÔñÒ»¸öÒÑ´æÔÚµÄÎïÆ·.';
$lang['fv_required_member'] = 'Ò»¸ö»áÔ±±ØĞë±»Ñ¡ÖĞ.';
$lang['fv_required_members'] = 'ÖÁÉÙÒ»¸ö»áÔ±±ØĞë±»Ñ¡ÖĞ.';
$lang['fv_required_message'] = 'ÏûÏ¢ ²»ÄÜÎª¿Õ.';
$lang['fv_required_name'] = 'Ãû×Ö ²»ÄÜÎª¿Õ.';
$lang['fv_required_password'] = 'ÃÜÂë ²»ÄÜÎª¿Õ.';
$lang['fv_required_raidid'] = 'Ò»´Îraid±ØĞë±»Ñ¡ÖĞ.';
$lang['fv_required_user'] = 'ÓÃ»§Ãû ²»ÄÜÎª¿Õ.';
$lang['fv_required_value'] = 'Öµ ²»ÄÜÎª¿Õ.';
$lang['fv_required_vote'] = 'Í¶Æ± ²»ÄÜÎª¿Õ.';

// Miscellaneous
$lang['added'] = 'Ôö¼Ó';
$lang['additem_raidid_note'] = "Ö»ÓĞÁ½ÖÜÒÔÄÚµÄRAID»á±»ÏÔ¾ / %1\$sÏÔ¾ËùÓĞ</a>";
$lang['additem_raidid_showall_note'] = 'ÏÔ¾ËùÓĞraids';
$lang['addraid_datetime_note'] = '½âÎölog±£¬ÈÕÆÚºÍ±¼ä»á×Ô¶¯.';
$lang['addraid_value_note'] = 'µ¥´Î½±Àø£¬Èç¹ûÒÅÁô¿Õ¸ñ£¬»á¹ÓÃÑ¡¶¨»î¶¯µÄÄ¬ÈÏÖµ';
$lang['add_items_from_raid'] = '±¾´ÎRaidÔö¼ÓµÄÎïÆ·';
$lang['deleted'] = 'ÒÑÉ¾³ı';
$lang['done'] = 'Íê³É';
$lang['enter_new'] = 'ĞÂµÇÂ¼';
$lang['error'] = '´íÎó';
$lang['head_admin'] = '×Ï¯¹ÜÀíÔ±';
$lang['hold_ctrl_note'] = '°´×¡CTRL½øĞĞ¶àÑ¡';
$lang['list'] = 'ÁĞ±í';
$lang['list_groupadj'] = 'ÁĞ³öÍÅ¶Óµ÷½Ú';
$lang['list_events'] = 'ÁĞ³ö»î¶¯';
$lang['list_indivadj'] = 'ÁĞ³ö¸öÈËµ÷½Ú';
$lang['list_items'] = 'ÁĞ³öÎïÆ·';
$lang['list_members'] = 'ÁĞ³ö»áÔ±';
$lang['list_news'] = 'ÁĞ³öÏûÏ¢';
$lang['list_raids'] = 'ÁĞ³öRaids';
$lang['may_be_negative_note'] = '¿ÉÄÜ±»¾Ü¾ø';
$lang['not_available'] = '²»¿É¼û';
$lang['no_news'] = 'No news entries found.';
$lang['of_raids'] = "%1\$d%% of raids";
$lang['or'] = '»òÕß';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Ô¤ÀÀ';
$lang['required_field_note'] = '±ê×¢*µÄ±ØĞëÌîĞ´.';
$lang['select_1ofx_members'] = "Ñ¡Ôñ 1 of %1\$d »áÔ±...";
$lang['select_existing'] = 'Ñ¡ÔñÍË³ö';
$lang['select_version'] = 'Ñ¡ÔñÏëÉı¼¶µÄEQdkp°æ±¾';
$lang['success'] = '³É¹¦';
$lang['s_admin_note'] = 'ÕâĞ©Ïî²»ÄÜ±»ÓÃ»§¸Ä±ä.';
$lang['transfer_member_history_description'] = 'Õâ½«×ªÒÆÒ»¸ö»áÔ±ËùÓĞµÄÀú· (raids, ÎïÆ·, adjustments) µ½ÁíÒ»¸ö»áÔ±.';
$lang['updated'] = 'ÒÑ¸üĞÂ';
$lang['upgrade_complete'] = 'ÄãµÄEQdkp°²×°³ÌĞòÒÑ¾­³É¹¦Éı¼¶.<br /><br /><b class="negative">Îª°²È«Æğ¼û£¬ÇëÒÆ³ı´ËÎÄ¼ş£¡</b>';

// Settings
$lang['account_settings'] = 'ÕËºÅÉèÖÃ';
$lang['adjustments_per_page'] = 'µ÷½ÚÃ¿Ò³';
$lang['basic'] = '»ù´¡';
$lang['events_per_page'] = 'Â¼şÃ¿Ò³';
$lang['items_per_page'] = 'ÎïÆ·Ã¿Ò³';
$lang['news_per_page'] = 'ÏûÏ¢Èë¿ÚÃ¿Ò³';
$lang['raids_per_page'] = 'RaidsÃ¿Ò³';
$lang['associated_members'] = 'Associated Members';
$lang['guild_members'] = '¹«»á»áÔ±';

// Error messages
$lang['error_account_inactive'] = 'ÕËºÅÃ»ÓĞ¼¤»î.';
$lang['error_already_activated'] = 'ÕËºÅÒÑ¾­´æÔÚ.';
$lang['error_invalid_email'] = 'ÎŞĞ§e-mailµØÖ·.';
$lang['error_invalid_event_provided'] = 'ÎŞĞ§ event id.';
$lang['error_invalid_item_provided'] = 'ÎŞĞ§ item id.';
$lang['error_invalid_key'] = 'ÎŞĞ§¼¤»î¹Ø¼ü×Ö.';
$lang['error_invalid_name_provided'] = 'ÎŞĞ§»áÔ±Ãû³Æ.';
$lang['error_invalid_news_provided'] = 'ÎŞĞ§ÏûÏ¢ id.';
$lang['error_invalid_raid_provided'] = 'ÎŞĞ§raid id.';
$lang['error_user_not_found'] = 'ÎŞĞ§ÓÃ»§Ãû³Æ';
$lang['incorrect_password'] = 'ÃÜÂë´íÎó';
$lang['invalid_login'] = 'ÓÃ»§Ãû/ÃÜÂë ´íÎó»òÕß²»´æÔÚ';
$lang['not_admin'] = 'Äã²»Ç¹ÜÀíÔ±';

// Registration
$lang['account_activated_admin']   = 'ÕËºÅÒÑ¼¤»î.e-mailÒÑ·¢ËÍ.';
$lang['account_activated_user']    = "ÕËºÅÒÑ¾­¼¤»î£¬ÏÖÔÚ¿ÉÒÔ %1\$sµÇÈë%2\$s.";
$lang['password_sent'] = 'ĞÂµÄÃÜÂëÒÑ¾­Í¨¹ıe_mail·¢¸øÄãÁË.';
$lang['register_activation_self']  = "ÕËºÅÒÑ¾­´´½¨, µ«Ç¹ÓÃÖ®Ç°±ØĞë¼¤»î.<br /><br />e-mailÒÑ¾­·¢ËÍµ½ %1\$s ÀïÃæ°üº¬ÁËÈçºÎ¼¤»îÕËºÅµÄĞÅÏ¢.";
$lang['register_activation_admin'] = "ÕËºÅÒÑ¾­´´½¨, µ«Ç¹ÓÃÖ®Ç°¹ÜÀíÔ±±ØĞë¼¤»îËü.<br /><br />e-mailÒÑ¾­·¢ËÍµ½ %1\$s ÀïÃæ°üº¬¸ü¶àĞÅÏ¢.";
$lang['register_activation_none']  = "ÕËºÅÒÑ¾­´´½¨£¬ÄãÏÖÔÚ¿ÉÒÔ %1\$sµÇÈë%2\$s.<br /><br />e-mailÒÑ¾­·¢ËÍµ½ %3\$s ÀïÃæ°üº¬¸ü¶àĞÅÏ¢.";
?>
