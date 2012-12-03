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
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

// Titles
$lang['addadj_title']         = 'Add a Group Adjustment';
$lang['addevent_title']       = 'Add an Event';
$lang['addiadj_title']        = 'Add an Individual Adjustment';
$lang['additem_title']        = 'Add an Item Purchase';
$lang['addmember_title']      = 'Add a Guild Member';
$lang['addnews_title']        = 'Add a News Entry';
$lang['addraid_title']        = 'Add a Raid';
$lang['updraid_title']		  = 'Update a Raid';
$lang['addturnin_title']      = "Add a Turn-in - Step %1\$d";
$lang['admin_index_title']    = 'EQdkp Administration';
$lang['config_title']         = 'Script Configuration';
$lang['manage_members_title'] = 'Manage Guild Members';
$lang['manage_users_title']   = 'User Accounts and Permissions';
$lang['manraid_title']        = 'Manage Raids';
$lang['parselog_title']       = 'Parse a Log File';
$lang['plugins_title']        = 'Manage Plugins';
$lang['styles_title']         = 'Manage Styles';
$lang['viewlogs_title']       = 'Log Viewer';

// Page Foot Counts
$lang['listusers_footcount']             = "... found %1\$d user(s) / %2\$d per page";
$lang['manage_members_footcount']        = "... found %1\$d member(s)";
$lang['online_footcount']                = "... %1\$d users are online";
$lang['viewlogs_footcount']              = "... found %1\$d log(s) / %2\$d per page";

// Submit Buttons
$lang['add_aadjustment'] = 'Add an Adjustment';
$lang['add_adjustment'] = 'Add Adjustment';
$lang['add_account'] = 'Add Account';
$lang['add_aitem'] = 'Add an Item';
$lang['add_event'] = 'Add Event';
$lang['add_item'] = 'Add Item';
$lang['add_member'] = 'Add Member';
$lang['add_news'] = 'Add News';
$lang['add_raid'] = 'Add Raid';
$lang['add_style'] = 'Add Style';
$lang['add_turnin'] = 'Add Turn-in';
$lang['delete_adjustment'] = 'Delete Adjustment';
$lang['delete_event'] = 'Delete Event';
$lang['delete_item'] = 'Delete Item';
$lang['delete_member'] = 'Delete Member';
$lang['delete_news'] = 'Delete News';
$lang['delete_raid'] = 'Delete Raid';
$lang['delete_selected_members'] = 'Delete Selected Member(s)';
$lang['delete_style'] = 'Delete Style';
$lang['mass_delete'] = 'Mass Delete';
$lang['mass_update'] = 'Mass Update';
$lang['parse_log'] = 'Parse Log';
$lang['search_existing'] = 'Search Existing';
$lang['select'] = 'Select';
$lang['transfer_history'] = 'Transfer History';
$lang['update_adjustment'] = 'Update Adjustment';
$lang['update_event'] = 'Update Event';
$lang['update_item'] = 'Update Item';
$lang['update_member'] = 'Update Member';
$lang['update_news'] = 'Update News';
$lang['update_raid'] = 'Update Raid';
$lang['update_style'] = 'Update Style';

// Misc
$lang['account_enabled'] = 'Account Enabled';
$lang['member_active'] = 'Active?';
$lang['adjitem_del'] = 'Delete marked Adjustments and Items';
$lang['adjustment_value'] = 'Adjustment Value';
$lang['adjustment_value_note'] = 'May be negative';
$lang['adjustments'] = 'Adjustments';
$lang['code'] = 'Code';
$lang['contact'] = 'Contact';
$lang['create'] = 'Create';
$lang['del_nosuc'] = 'Deleting failed';
$lang['del_raid_with_itemadj'] = 'Should the Raid and all the items and adjustments be deleted?';
$lang['del_suc'] = 'Deleting successful';
$lang['found_members'] = "Parsed %1\$d lines, found %2\$d members";
$lang['headline'] = 'Headline';
$lang['hide'] = 'Hide?';
$lang['install'] = 'Install';
$lang['item_search'] = 'Item Search';
$lang['item_name'] = 'Itemname';
$lang['item_id'] = 'ItemID';
$lang['list_prefix'] = 'List Prefix';
$lang['list_suffix'] = 'List Suffix';
$lang['logs'] = 'Logs';
$lang['log_find_all'] = 'Find all (including anonymous)';
$lang['manage_members'] = 'Manage Members';
$lang['manage_plugins'] = 'Manage Plugins';
$lang['manage_raids'] = 'Manage Raids';
$lang['manage_users'] = 'Manage Users';
$lang['mass_update_note'] = 'If you wish to apply changes to all of the items selected above, use these controls to change their properties and click on "Mass Update".
                             To delete the selected accounts, just click on "Mass Delete".';
$lang['members'] = 'Members';
$lang['member_rank'] = 'Member Rank';
$lang['message_body'] = 'Message Body';
$lang['message_show_loot_raid'] = 'Show Loot from Raid:';
$lang['results'] = "%1\$d Results (\"%2\$s\")";
$lang['save_nosuc'] = 'Saving failed';
$lang['save_suc'] = 'Saving successful';
$lang['search'] = 'Search';
$lang['search_members'] = 'Search Members';
$lang['should_be'] = 'Should be';
$lang['styles'] = 'Styles';
$lang['title'] = 'Title';
$lang['uninstall'] = 'Uninstall';
$lang['enable']		= 'Enable';
$lang['update_date_to'] = "Update date to<br />%1\$s?";
$lang['version'] = 'Version';
$lang['x_members_s'] = "%1\$d member";
$lang['x_members_p'] = "%1\$d members";

// Permission Messages
$lang['noauth_a_event_add']    = 'You do not have permission to add events.';
$lang['noauth_a_event_upd']    = 'You do not have permission to update events.';
$lang['noauth_a_event_del']    = 'You do not have permission to delete events.';
$lang['noauth_a_groupadj_add'] = 'You do not have permission to add group adjustments.';
$lang['noauth_a_groupadj_upd'] = 'You do not have permission to update group adjustments.';
$lang['noauth_a_groupadj_del'] = 'You do not have permission to delete group adjustments.';
$lang['noauth_a_indivadj_add'] = 'You do not have permission to add individual adjustments.';
$lang['noauth_a_indivadj_upd'] = 'You do not have permission to update individual adjustments.';
$lang['noauth_a_indivadj_del'] = 'You do not have permission to delete individual adjustments.';
$lang['noauth_a_item_add']     = 'You do not have permission to add items.';
$lang['noauth_a_item_upd']     = 'You do not have permission to update items.';
$lang['noauth_a_item_del']     = 'You do not have permission to delete items.';
$lang['noauth_a_news_add']     = 'You do not have permission to add news entries.';
$lang['noauth_a_news_upd']     = 'You do not have permission to update news entries.';
$lang['noauth_a_news_del']     = 'You do not have permission to delete news entries.';
$lang['noauth_a_raid_add']     = 'You do not have permission to add raids.';
$lang['noauth_a_raid_upd']     = 'You do not have permission to update raids.';
$lang['noauth_a_raid_del']     = 'You do not have permission to delete raids.';
$lang['noauth_a_turnin_add']   = 'You do not have permission to add turn-ins.';
$lang['noauth_a_config_man']   = 'You do not have permission to manage EQdkp configuration settings.';
$lang['noauth_a_members_man']  = 'You do not have permission to manage guild members.';
$lang['noauth_a_plugins_man']  = 'You do not have permission to manage EQdkp plugins.';
$lang['noauth_a_styles_man']   = 'You do not have permission to manage EQdkp styles.';
$lang['noauth_a_users_man']    = 'You do not have permission to manage user account settings.';
$lang['noauth_a_logs_view']    = 'You do not have permission to view EQdkp logs.';

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
$lang['admin_update_style_success']          = 'The style has been updated successfully.';

$lang['admin_raid_success_hideinactive']     = 'Updating active/inactive player status...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Are you sure you want to delete this group adjustment?';
$lang['confirm_delete_admins']  = 'Are you sure you want to delete the selected administrator(s)?';
$lang['confirm_delete_event']   = 'Are you sure you want to delete this event?';
$lang['confirm_delete_iadj']    = 'Are you sure you want to delete this individual adjustment?';
$lang['confirm_delete_item']    = 'Are you sure you want to delete this item?';
$lang['confirm_delete_members'] = 'Are you sure you want to delete the following members?';
$lang['confirm_delete_news']    = 'Are you sure you want to delete this news entry?';
$lang['confirm_delete_raid']    = 'Are you sure you want to delete this raid?';
$lang['confirm_delete_style']   = 'Are you sure you want to delete this style?';
$lang['confirm_delete_users']   = 'Are you sure you want to delete the following user accounts?';

// Log Actions
$lang['action_event_added']      = 'Event Added';
$lang['action_event_deleted']    = 'Event Deleted';
$lang['action_event_updated']    = 'Event Updated';
$lang['action_groupadj_added']   = 'Group Adjustment Added';
$lang['action_groupadj_deleted'] = 'Group Adjustment Deleted';
$lang['action_groupadj_updated'] = 'Group Adjustment Updated';
$lang['action_history_transfer'] = 'Member History Transfer';
$lang['action_indivadj_added']   = 'Individual Adjustment Added';
$lang['action_indivadj_deleted'] = 'Individual Adjustment Deleted';
$lang['action_indivadj_updated'] = 'Individual Adjustment Updated';
$lang['action_item_added']       = 'Item Added';
$lang['action_item_deleted']     = 'Item Deleted';
$lang['action_item_updated']     = 'Item Updated';
$lang['action_member_added']     = 'Member Added';
$lang['action_member_deleted']   = 'Member Deleted';
$lang['action_member_updated']   = 'Member Updated';
$lang['action_news_added']       = 'News Entry Added';
$lang['action_news_deleted']     = 'News Entry Deleted';
$lang['action_news_updated']     = 'News Entry Updated';
$lang['action_raid_added']       = 'Raid Added';
$lang['action_raid_deleted']     = 'Raid Deleted';
$lang['action_raid_updated']     = 'Raid Updated';
$lang['action_turnin_added']     = 'Turn-in Added';

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
$lang['general_settings'] = 'General Settings';
$lang['guildtag'] = 'Guildtag / Alliance Name';
$lang['guildtag_note'] = 'Used in the title of nearly every page';
$lang['parsetags'] = 'Guildtags to Parse';
$lang['parsetags_note'] = 'Those listed will be available as options when parsing raid logs.';
$lang['domain_name'] = 'Domain Name';
$lang['server_port'] = 'Server Port';
$lang['server_port_note'] = 'Your webserver\'s port. Usually 80';
$lang['script_path'] = 'Script Path';
$lang['script_path_note'] = 'Path where EQdkp is located, relative to the domain name';
$lang['site_name'] = 'Site Name';
$lang['site_description'] = 'Site Description';
$lang['point_name'] = 'Point Name';
$lang['point_name_note'] = 'Ex: DKP, RP, etc.';
$lang['enable_account_activation'] = 'Enable Account Activation';
$lang['none'] = 'None';
$lang['admin'] = 'Admin';
$lang['default_language'] = 'Default Language';
$lang['default_locale'] = 'Default Locale (character set only; does not affect language)';
$lang['default_game'] = 'Default Game';
$lang['default_game_warn'] = 'Changing the default game may void other changes in this session.';
$lang['default_style'] = 'Default Style';
$lang['default_page'] = 'Default Index Page';
$lang['hide_inactive'] = 'Hide Inactive Members';
$lang['hide_inactive_note'] = 'Hide members that haven\'t attended a raid in [inactive period] days?';
$lang['inactive_period'] = 'Inactive Period';
$lang['inactive_period_note'] = 'Number of days a member can miss a raid and still be considered active';
$lang['inactive_point_adj'] = 'Inactive Point Adjustment';
$lang['inactive_point_adj_note'] = 'Point adjustment to make on a member when they become inactive.';
$lang['active_point_adj'] = 'Active Point Adjustment';
$lang['active_point_adj_note'] = 'Point Adjustment to make on a member when they become active.';
$lang['enable_gzip'] = 'Enable Gzip Compression';
$lang['show_item_stats'] = 'Show Item Stats';
$lang['show_item_stats_note'] = 'Tries to grab item stats from the Internet.  May impact speed of certain pages';
$lang['default_permissions'] = 'Default Permissions';
$lang['default_permissions_note'] = 'These are the permissions for users who are not logged in and are given to new users when they register. Items in <b>bold</b> are administrative permissions,
                                     it is highly recommended to not set any of those items as the default. Items in <i>italics</i> are used exclusively by plugins.  You can later change an individual user\'s permissions by going to Manage Users.';
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'The Plugin folder (./plugins/) is empty.';
$lang['cookie_settings'] = 'Cookie Settings';
$lang['cookie_domain'] = 'Cookie Domain';
$lang['cookie_name'] = 'Cookie Name';
$lang['cookie_path'] = 'Cookie Path';
$lang['session_length'] = 'Session Length (seconds)';
$lang['email_settings'] = 'E-Mail Settings';
$lang['admin_email'] = 'Administrator E-Mail Address';

// Admin Index
$lang['anonymous'] = 'Anonymous';
$lang['database_size'] = 'Database Size';
$lang['eqdkp_started'] = 'EQdkp Started';
$lang['ip_address'] = 'IP Address';
$lang['items_per_day'] = 'Items per Day';
$lang['last_update'] = 'Last Update';
$lang['location'] = 'Location';
$lang['new_version_notice'] = "EQdkp version %1\$s is <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">available for download</a>.";
$lang['number_of_items'] = 'Number of Items';
$lang['number_of_logs'] = 'Number of Log Entries';
$lang['number_of_members'] = 'Number of Members (Active / Inactive)';
$lang['number_of_raids'] = 'Number of Raids';
$lang['raids_per_day'] = 'Raids per Day';
$lang['statistics'] = 'Statistics';
$lang['totals'] = 'Totals';
$lang['version_update'] = 'Version Update';
$lang['who_online'] = 'Who\'s Online';

// Style Management
$lang['style_settings'] = 'Style Settings';
$lang['style_name'] = 'Style Name';
$lang['template'] = 'Template';
$lang['element'] = 'Element';
$lang['background_color'] = 'Background Color';
$lang['fontface1'] = 'Font Face 1';
$lang['fontface1_note'] = 'Default font face';
$lang['fontface2'] = 'Font Face 2';
$lang['fontface2_note'] = 'Input field font face';
$lang['fontface3'] = 'Font Face 3';
$lang['fontface3_note'] = 'Not currently used';
$lang['fontsize1'] = 'Font Size 1';
$lang['fontsize1_note'] = 'Small';
$lang['fontsize2'] = 'Font Size 2';
$lang['fontsize2_note'] = 'Medium';
$lang['fontsize3'] = 'Font Size 3';
$lang['fontsize3_note'] = 'Large';
$lang['fontcolor1'] = 'Font Color 1';
$lang['fontcolor1_note'] = 'Default color';
$lang['fontcolor2'] = 'Font Color 2';
$lang['fontcolor2_note'] = 'Color used outside tables (menus, titles, copyright)';
$lang['fontcolor3'] = 'Font Color 3';
$lang['fontcolor3_note'] = 'Input field font color';
$lang['fontcolor_neg'] = 'Negative Font Color';
$lang['fontcolor_neg_note'] = 'Color for negative/bad numbers';
$lang['fontcolor_pos'] = 'Positive Font Color';
$lang['fontcolor_pos_note'] = 'Color for positive/good numbers';
$lang['body_link'] = 'Link Color';
$lang['body_link_style'] = 'Link Style';
$lang['body_hlink'] = 'Hover Link Color';
$lang['body_hlink_style'] = 'Hover Link Style';
$lang['header_link'] = 'Header Link';
$lang['header_link_style'] = 'Header Link Style';
$lang['header_hlink'] = 'Hover Header Link';
$lang['header_hlink_style'] = 'Hover Header Link Style';
$lang['tr_color1'] = 'Table Row Color 1';
$lang['tr_color2'] = 'Table Row Color 2';
$lang['th_color1'] = 'Table Header Color';
$lang['table_border_width'] = 'Table Border Width';
$lang['table_border_color'] = 'Table Border Color';
$lang['table_border_style'] = 'Table Border Style';
$lang['input_color'] = 'Input Field Background Color';
$lang['input_border_width'] = 'Input Field Border Width';
$lang['input_border_color'] = 'Input Field Border Color';
$lang['input_border_style'] = 'Input Field Border Style';
$lang['style_configuration'] = 'Style Configuration';
$lang['style_date_note'] = 'For date/time fields, the syntax used is identical to the PHP <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> function.';
$lang['attendees_columns'] = 'Attendees Columns';
$lang['attendees_columns_note'] = 'Number of columns to use for attendees when viewing a raid';
$lang['date_notime_long'] = 'Date without Time (long)';
$lang['date_notime_short'] = 'Date without Time (short)';
$lang['date_time'] = 'Date with Time';
$lang['logo_path'] = 'Logo Filename';
$lang['logo_path_note'] = 'Choose an image from /templates/template/images/ or insert the complete URL from a image on the internet. Plz insert the URL starts with http:// !!)';
$lang['logo_path_config'] = 'Select a file from your hard drive and upload your new logo here';

// Errors
$lang['error_invalid_adjustment'] = 'A valid adjustment was not provided.';
$lang['error_invalid_plugin']     = 'A valid plugin was not provided.';
$lang['error_invalid_style']      = 'A valid style was not provided.';

// Verbose log entry lines
$lang['new_actions']           = 'Newest Admin Actions';
$lang['vlog_event_added']      = "%1\$s added the event '%2\$s' worth %3\$.2f points.";
$lang['vlog_event_updated']    = "%1\$s updated the event '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s deleted the event '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s added a group adjustment of %2\$.2f points.";
$lang['vlog_groupadj_updated'] = "%1\$s updated a group adjustment of %2\$.2f points.";
$lang['vlog_groupadj_deleted'] = "%1\$s deleted a group adjustment of %2\$.2f points.";
$lang['vlog_history_transfer'] = "%1\$s transferred %2\$s's history to %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s added an individual adjustment of %2\$.2f to %3\$d member(s).";
$lang['vlog_indivadj_updated'] = "%1\$s updated an individual adjustment of %2\$.2f to %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s deleted an individual adjustment of %2\$.2f to %3\$s.";
$lang['vlog_item_added']       = "%1\$s added the item '%2\$s' charged to %3\$d member(s) for %4\$.2f points.";
$lang['vlog_item_updated']     = "%1\$s updated the item '%2\$s' charged to %3\$d member(s).";
$lang['vlog_item_deleted']     = "%1\$s deleted the item '%2\$s' charged to %3\$d member(s).";
$lang['vlog_member_added']     = "%1\$s added the member %2\$s.";
$lang['vlog_member_updated']   = "%1\$s updated the member %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s deleted the member %2\$s.";
$lang['vlog_news_added']       = "%1\$s added the news entry '%2\$s'.";
$lang['vlog_news_updated']     = "%1\$s updated the news entry '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s deleted the news entry '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s added a raid on '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s updated a raid on '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s deleted a raid on '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s added a turn-in from %2\$s to %3\$s for '%4\$s'.";

// Location messages
$lang['adding_groupadj'] = 'Adding a Group Adjustment';
$lang['adding_indivadj'] = 'Adding an Individual Adjustment';
$lang['adding_item'] = 'Adding an Item';
$lang['adding_news'] = 'Adding a News Entry';
$lang['adding_raid'] = 'Adding a Raid';
$lang['adding_turnin'] = 'Adding a Turn-in';
$lang['editing_groupadj'] = 'Editing Group Adjustment';
$lang['editing_indivadj'] = 'Editing Individual Adjustment';
$lang['editing_item'] = 'Editing Item';
$lang['editing_news'] = 'Editing News Entry';
$lang['editing_raid'] = 'Editing Raid';
$lang['listing_events'] = 'Listing Events';
$lang['listing_groupadj'] = 'Listing Group Adjustments';
$lang['listing_indivadj'] = 'Listing Individual Adjustments';
$lang['listing_itemhist'] = 'Listing Item History';
$lang['listing_itemvals'] = 'Listing Item Values';
$lang['listing_members'] = 'Listing Members';
$lang['listing_raids'] = 'Listing Raids';
$lang['managing_config'] = 'Managing EQdkp Configuration';
$lang['managing_members'] = 'Managing Guild Members';
$lang['managing_plugins'] = 'Managing Plugins';
$lang['managing_styles'] = 'Managing Styles';
$lang['managing_users'] = 'Managing User Accounts';
$lang['parsing_log'] = 'Parsing a Log';
$lang['viewing_admin_index'] = 'Viewing Admin Index';
$lang['viewing_event'] = 'Viewing Event';
$lang['viewing_item'] = 'Viewing Item';
$lang['viewing_logs'] = 'Viewing Logs';
$lang['viewing_member'] = 'Viewing Member';
$lang['viewing_mysql_info'] = 'Viewing MySQL Information';
$lang['viewing_news'] = 'Viewing News';
$lang['viewing_raid'] = 'Viewing Raid';
$lang['viewing_stats'] = 'Viewing Stats';
$lang['viewing_summary'] = 'Viewing Summary';

// Help lines
$lang['b_help'] = 'Bold text: [b]text[/b] (shift+alt+b)';
$lang['i_help'] = 'Italic text: [i]text[/i] (shift+alt+i)';
$lang['u_help'] = 'Underlined text: [u]text[/u] (shift+alt+u)';
$lang['q_help'] = 'Quote text: [quote]text[/quote] (shift+alt+q)';
$lang['c_help'] = 'Center text: [center]text[/center] (shift+alt+c)';
$lang['p_help'] = 'Insert image: [img]http://image_url[/img] (shift+alt+p)';
$lang['w_help'] = 'Insert URL: [url]http://URL[/url] or [url=http://url]text[/url] (shift+alt+w)';
$lang['it_help'] = 'Insert Item: e.g. [item]Judgement Breastplate[/item] (shift+alt+t)';
$lang['ii_help'] = 'Insert ItemIcon: e.g. [itemicon]Judgement Breastplate[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Add New Member';
$lang['list_edit_del_member'] = 'List, Edit or Delete Members';
$lang['edit_ranks'] = 'Edit Membership Ranks';
$lang['transfer_history'] = 'Transfer Member History';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'MySQL Info';
$lang['eqdkp_tables'] = 'EQdkp Tables';
$lang['table_name'] = 'Table Name';
$lang['rows'] = 'Rows';
$lang['table_size'] = 'Table Size';
$lang['index_size'] = 'Index Size';
$lang['num_tables'] = "%d tables";

//Backup
$lang['backup']            = 'Backup';
$lang['backup_database']   = 'Backup Database';
$lang['backup_title']      = 'Create a database backup';
$lang['backup_type']       = 'Backup Format';
$lang['create_table']      = 'Add \'CREATE TABLE\' statements?';
$lang['skip_nonessential'] = 'Skip non essential data?<br />Will not produce insert rows for eqdkp_sessions.';
$lang['gzip_content']      = 'GZIP Content?<br />Will produce a smaller file if GZIP is enabled.';
$lang['backup_no_table_prefix']    = '<strong>WARNING:</strong> Your installation of EQdkp does not have a table prefix for its database tables. Any tables for plugins you may have will not be backed up.';

// plus
$lang['in_database']  = 'Saved in Database';

//Log Users Actions
$lang['action_user_added']     = 'User Added';
$lang['action_user_deleted']   = 'User Deleted';
$lang['action_user_updated']   = 'User Updated';

$lang['vlog_user_added']     = "%1\$s added the user %2\$s.";
$lang['vlog_user_updated']   = "%1\$s updated user %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s deleted the user %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP Pool Added";
$lang['action_multidkp_deleted']   = "MultiDKP Pool Deleted";
$lang['action_multidkp_updated']   = "MultiDKP Pool Updated";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s added the MultiDKP Pool %2\$s.";
$lang['vlog_multidkp_updated']   = "%1\$s updated the MultiDKP Pool %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s deleted the MultiDKP Pool %2\$s.";

$lang['default_style_overwrite']   = "Overwrite user style settings (every user use the default-style)";
$lang['class_colors']              = "Class colors";

#Plugins
$lang['description'] = 'Description';
$lang['manual'] = 'Manual';
$lang['homepage'] = 'Homepage';
$lang['readme'] = 'Read me';
$lang['link'] = 'Link';
$lang['infos'] = 'Infos';

// Plugin Install / Uninstall
$lang['plugin_inst_success']  = 'Success';
$lang['plugin_inst_error']  = 'Error';
$lang['plugin_inst_message']  = "The plugin <i>%1\$s</i> was successfully %2\$s.";
$lang['plugin_inst_installed'] = 'installed';
$lang['plugin_inst_uninstalled'] = 'uninstalled';
$lang['plugin_inst_errormsg1'] = "Errors were detected during the %1\$s process: %2\$s";
$lang['plugin_inst_errormsg2']  = "%1\$s may not have %2\$s correctly.";

$lang['background_image'] = 'Background image ( 1000x1000px) [optional]';
$lang['css_file'] = 'CSS File - ignored most of the color setting on this site. [optional]';

$lang['plugin_inst_sql_note'] = 'An SQL error during install does not necessary imply a broken plugin installation. Try using the plugin, if errors occur please un- and reinstall the plugin.';

// Plugin Update Warn Class
$lang['puc_perform_intro']          = 'The following Plugins need updates of their database structure. Please click on the "solve" Link to perform the database changes for each plugin.<br/>Following database tables are out of date:';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Requires database updates from %2\$s to %3\$s)";
$lang['puc_solve_dbissues']         = 'solve';
$lang['puc_unknown']                = '[unknown]';

//Plus Data Cache
$lang['pdc_manager'] = 'Cache Manager';
$lang['pdc_status'] = 'Status';
$lang['pdc_entity'] = 'Entity';
$lang['pdc_entity_valid'] = 'Valid';
$lang['pdc_entity_expired'] = 'Expired';
$lang['pdc_size'] = 'Size';
$lang['pdc_size_total'] = 'Total';
$lang['pdc_clear'] = 'Clear';
$lang['pdc_cleanup'] = 'Clean-Up';

//Alt Manager
$lang['alt_manager'] = 'Alt Manager';
$lang['alt_main_id'] = 'Main ID';
$lang['alt_message_title'] = 'Notification:';
$lang['alt_update_successful'] = 'Alt update successful.';
$lang['alt_update_unsuccessful'] = 'Alt update not successful, resolve errors above!';
$lang['alt_main_is_alt'] = 'Selected main for member %s is an alt, only mains can be selected as mains for other members.';

//---- Main ----
$lang['pluskernel']          	= 'PLUS Config';
$lang['pk_adminmenu']         	= 'PLUS Config';
$lang['pk_settings']						= 'Settings';
$lang['pk_date_settings']			= 'd.m.y';

//---- Javascript stuff ----
$lang['pk_plus_about']					= 'About EQDKP PLUS';
$lang['updates']								= 'Available Updates';
$lang['loading']								= 'Loading...';
$lang['pk_config_header']			= 'EQDKP PLUS Settings';
$lang['pk_close_jswin1']      	= 'Close the';
$lang['pk_close_jswin2']     	= 'window before opening it again!';
$lang['pk_help_header']				= 'Help';
$lang['pk_plus_comments']  	= 'Comments';

//---- Updater Stuff ----
$lang['pk_alt_attention']			= 'Attention';
$lang['pk_alt_ok']							= 'Everything OK!';
$lang['pk_updates_avail']			= 'Updates available';
$lang['pk_updates_navail']			= 'NO Updates available';
$lang['pk_no_updates']					= 'Your Versions are all up to date. There are no newer Versions available.';
$lang['pk_act_version']				= 'New Version';
$lang['pk_inst_version']				= 'Installed';
$lang['pk_changelog']					= 'Changelog';
$lang['pk_download']						= 'Download';
$lang['pk_upd_information']		= 'Information';
$lang['pk_enabled']						= 'disabled';
$lang['pk_disabled']						= 'enabled';
$lang['pk_auto_updates1']			= 'The automatic update warning is';
$lang['pk_auto_updates2']			= 'If you disabled this setting, please recheck regulary for updates to prevent hacks and stay up to date..';
$lang['pk_module_name']				= 'Module name';
$lang['pk_plugin_level']				= 'Level';
$lang['pk_release_date']				= 'Release';
$lang['pk_alt_error']					= 'Error';
$lang['pk_no_conn_header']			= 'Connection Error';
$lang['pk_no_server_conn']			= 'An error ocurred while trying to contact the update server, either
																 	your host do not allow outbound connections or the error was caused
																 	by a network problem. Please visit the eqdkp-forum to make
																 	sure you are running the latest version.';
$lang['pk_reset_warning']			= 'Reset Warning';

//---- Update Levels ----
$lang['pk_level_other']				= 'Other';
$updatelevel = array (
	'Bugfix'										=> 'Bugfix',
	'Feature Release'						=> 'Feature Release',
	'Security Update'						=> 'Security Update',
	'New version'								=> 'New version',
	'Release Candidate'					=> 'Release Candidate',
	'Public Beta'								=> 'Public Beta',
	'Closed Beta'								=> 'Closed Beta',
	'Alpha'											=> 'Alpha',
);

//---- Settings ----
$lang['pk_save']								= 'Save';
$lang['pk_save_title']					= 'Saved settings';
$lang['pk_succ_saved']					= 'The settings was successfully saved';
 // Tabs
$lang['pk_tab_global']					= 'Global';
$lang['pk_tab_multidkp']				= 'multiDKP';
$lang['pk_tab_links']					= 'Links';
$lang['pk_tab_bosscount']			= 'BossCounter';
$lang['pk_tab_listmemb']				= 'Listmembers';
$lang['pk_tab_itemstats']			= 'Itemstats';
// Global
$lang['pk_set_QuickDKP']				= 'Show QuickDKP';
$lang['pk_set_Bossloot']				= 'Show bossloot ?';
$lang['pk_set_ClassColor']			= 'Colored ClassClassnames';
$lang['pk_set_Updatecheck']		= 'Enable Updatecheck';
// MultiDKP
$lang['pk_set_multidkp']				= 'Enable MultiDKP';
// Listmembers
$lang['pk_set_leaderboard']		= 'Show Leaderboard';
$lang['pk_set_lb_solo']				= 'Show Leaderboard per account';
$lang['pk_set_rank']						= 'Show Rank';
$lang['pk_set_rank_icon']			= 'Show Rank Icon';
$lang['pk_set_level']					= 'Show Level';
$lang['pk_set_lastloot']				= 'Show Last loot';
$lang['pk_set_lastraid']				= 'Show Last raid';
$lang['pk_set_attendance30']		= 'Show Raid Attendance 30 Day';
$lang['pk_set_attendance60']		= 'Show Raid Attendance 60 Day';
$lang['pk_set_attendance90']		= 'Show Raid Attendance 90 Day';
$lang['pk_set_attendanceAll']	= 'Show Raid Attendance Lifetime';
// Links
$lang['pk_set_links']					= 'Enable Links';
$lang['pk_set_linkurl']				= 'Link URL';
$lang['pk_set_linkname']				= 'Link name';
$lang['pk_set_newwindow']			= 'open in new window ?';
// BossCounter
$lang['pk_set_bosscounter']		= 'Show Bosscounter';
//Itemstats
$lang['pk_set_itemstats']			= 'Enable Itemstats';
$lang['pk_is_language']				= 'Itemstats language';
$lang['pk_german']							=	'German';
$lang['pk_english']						= 'English';
$lang['pk_french']							= 'French';
$lang['pk_set_en_de']					= 'Translate Items from English to German';
$lang['pk_set_de_en']					= 'Translate Items from German to English';

################
# new sort
###############

//MultiDKP
//

$lang['pk_set_multi_Tooltip']						= 'Show DKP tooltip';
$lang['pk_set_multi_smartTooltip']			  = 'Smart tooltip';

//Help
$lang['pk_help_colorclassnames']				  = "If activated, the players will be shown with the WoW colors of their class and their class icon.";
$lang['pk_help_quickdkp']								= "Shows the logged-in user the points off all members that are assigned to him above the menu.";
$lang['pk_help_boosloot']								= "If active, you can click the boss names in the raid notes and the bosscounter to have a detailed overview of the dropped items. If inactive, it will be linked to Blasc.de (Only activate if you enter a raid for each single boss)";
$lang['pk_help_autowarning']             = "Warns the administrator when he logs in, if updates are available.";;
$lang['pk_help_multidkp']								= "MultiDKP allows the management and overview of seperate accounts. Activates the management and overview of the MultiDKP accounts.";
$lang['pk_help_dkptooltip']							= "If activated, a tooltip with detailed information about the calculation of the points will be shown, when the mouse hovers over the different points.";
$lang['pk_help_smarttooltip']						= "Shortened overview of the tooltips (activate if you got more than three events per account)";
$lang['pk_help_links']                   = "In this menu you are able to define different links, which will be displayed in the main menu.";
$lang['pk_help_bosscounter']             = "If activated, a table will be displayed below the main menu with the bosskills. The administration is being managed by the plugin Bossprogress";
$lang['pk_help_lm_leaderboard']					= "If activated, a leaderboard will be displayed above the scoretable. A leaderboard is a table, where the dkp of each class is displayed sorted in decending order.";
$lang['pk_help_lm_rank']                 = "An extra column is being displayed, which displays the rank of the member.";
$lang['pk_help_lm_rankicon']             = "Instead of the rank name, an icon is being displayed. Which items are available you can check in the folder \images\rank";
$lang['pk_help_lm_level']								= "An additional column is being displayed, which displays the level of the member. ";
$lang['pk_help_lm_lastloot']             = "An extra colums is being displayed, showing the date a member received his latest item.";
$lang['pk_help_lm_lastraid']             = "An extra column is being displayed, showing the date of the latest raid a member has been participated in.";
$lang['pk_help_lm_atten30']							= "An extra column is being displayed, showing a members participation in raid during the last 30 days (in percent).";
$lang['pk_help_lm_atten60']							= "An extra column is being displayed, showing a members participation in raid during the last 60 days (in percent). ";
$lang['pk_help_lm_atten90']							= "An extra column is being displayed, showing a members participation in raid during the last 90 days (in percent). ";
$lang['pk_help_lm_attenall']             = "An extra column is being displayed, showing a members overall raid participation (in percent).";
$lang['pk_help_itemstats_on']						= "Itemstats is requesting information about items entered in EQDKP in the WOW databases (Blasc, Allahkazm, Thottbot). These will be displayed in the color of the items quality including the known WOW tooltip. When active, items will be shown with a mouseover tooltip, similar to WOW.";
$lang['pk_help_itemstats_search']				= "Which database should Itemstats use first to lookup information? Blasc or Allakhazam?";

$lang['pk_set_leaderboard_2row']					= 'Leaderboard in 2 lines';
$lang['pk_help_leaderboard_2row']        = 'If active, the Leaderboard will be displayed in two lines with 4 or 5 classes each.';

$lang['pk_set_leaderboard_limit']        = 'limitation of the display';
$lang['pk_help_leaderboard_limit']				= 'If a numeric number is being entered, the Leaderboard will be restricted to the entered number of members. The number 0 represents no restrictions.';

$lang['pk_set_leaderboard_zero']         = 'Hide Player with zero DKP';
$lang['pk_help_leaderboard_zero']        = 'If activated, Players with zero DKP doesnt show in the leaderboard.';


$lang['pk_set_newsloot_limit']						= 'newsloot limit';
$lang['pk_help_newsloot_limit']          = 'How many items should be displayed in the news? This restricts the display of items, which will be displayed in the news. The number 0 represents no restrictions.';

$lang['pk_set_itemstats_debug']          = 'debug Modus';
$lang['pk_help_itemstats_debug']					= 'If activated, Itemstats will log all transactions to /itemstats/includes_de/debug.txt. This file has to be writable, CHMOD 777 !!!';

$lang['pk_set_showclasscolumn']          = 'show classes column';
$lang['pk_help_showclasscolumn']					= 'If activated, an extra column is being displayed showing the class of the player.' ;

$lang['pk_set_show_skill']								= 'show skill column';
$lang['pk_help_show_skill']              = 'If activated, an extra column is being displayed showing the skill of the player.';

$lang['pk_set_show_arkan_resi']          = 'show arcan resistance column';
$lang['pk_help_show_arkan_resi']					= 'If activated, an extra column is being displayed showing the arcane resistance of the player.';

$lang['pk_set_show_fire_resi']						= 'show fire resistance column';
$lang['pk_help_show_fire_resi']          = 'If activated, an extra column is being displayed showing the fire resistance of the player.';

$lang['pk_set_show_nature_resi']					= 'show nature resistance column';
$lang['pk_help_show_nature_resi']        = 'If activated, an extra column is being displayed showing the nature resistance of the player.';

$lang['pk_set_show_ice_resi']            = 'show ice resistance column';
$lang['pk_help_show_ice_resi']						= 'If activated, an extra column is being displayed showing the frost resistance of the player.';

$lang['pk_set_show_shadow_resi']					= 'show shadow resistance column';
$lang['pk_help_show_shadow_resi']        = 'If activated, an extra column is being displayed showing the shadow resistance of the player.';

$lang['pk_set_show_profils']							= 'show profile link column';
$lang['pk_help_show_profils']            = 'If activated, an extra column is being displayed showing the links to the profile.';

$lang['pk_set_servername']               = 'Realm name';
$lang['pk_help_servername']              = 'Insert your realm name here.';

$lang['pk_set_server_region']			  = 'region';
$lang['pk_help_server_region']			  = 'US or EU server.';


$lang['pk_help_default_multi']           = 'Choose the default DKP Acc for the leaderboard.';
$lang['pk_set_default_multi']            = 'Set default for leaderboard';

$lang['pk_set_round_activate']           = 'Round DKP.';
$lang['pk_help_round_activate']          = 'If activated, displayed DKP Point are rounded. 125.00 = 125DKP.';

$lang['pk_set_round_precision']          = 'Decimal place to round.';
$lang['pk_help_round_precision']         = 'Set the Decimal place to round the DKP. Default=0';

$lang['pk_is_set_prio']                  = 'Priority of Itemdatabase';
$lang['pk_itt_prio']                     = '%d. Database to search';
$lang['pk_is_help_prio']                 = 'Set the query order of item databases.';

$lang['pk_itt_set_langprio']             = 'Priority of Languages to search for an Item';
$lang['pk_itt_langprio']                 = '%d. Language to search';
$lang['pk_itt_help_langprio']            = 'Priority of Languages in which the Databases shall be searched, if there is no Item-ID given. Select language in which you enter the itemnames.';

$lang['pk_itt_icon_ext']				= 'File Extension of Itemstats-pictures';
$lang['pk_help_itt_icon_ext']			= "Filename extension of the pictures to be shown. Usually .png or .jpg.";

$lang['pk_itt_icon_loc']				= 'URL to Pictures of Itemstats.';
$lang['pk_help_itt_icon_url']			= "Please enter the URL where you Itemstats pictures are being located.";

$lang['pk_itt_default_icon']			= 'Default Icon';
$lang['pk_itt_help_default_icon']		= 'Default Icon which should be displayed if no item could be found.';

$lang['pk_itt_useitemlist']				= 'Use Itemlist';
$lang['pk_itt_help_useitemlist']		= 'Using Itemlist improves search time if the itemlist is cached, but it takes a longer time to fetch the itemlist. If you get timeouts with this option enabled disabling it, may help solving the problem.';

$lang['pk_itt_not_avail']                = 'We are sorry, but for the game, you have selected are no Itemstats available. If you know a Database with an XML-Interface, you can contact us at <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a>.';

$lang['pk_set_dkp_info']			  = 'Dont Show DKP Info in the main menu.';
$lang['pk_help_dkp_info']			  = 'If activated "DKP Info" will be hidden from the main menu.';

$lang['pk_set_debug']			= 'enable Eqdkp Debug Modus';
$lang['pk_set_debug_type']		= 'Mode';
$lang['pk_set_debug_type0']	= 'Debug off (Debug=0)';
$lang['pk_set_debug_type1']	= 'Debug on simple (Debug=1)';
$lang['pk_set_debug_type2']	= 'Debug on with SQL Queries (Debug=2)';
$lang['pk_set_debug_type3']	= 'Debug on extended (Debug=3)';
$lang['pk_help_debug']			= 'If activated, Eqdkp Plus will be running in debug mode, showing additional informations and error messages. Deaktivate if plugins abort with SQL error messages! 1=Rendering time, Query count, 2=SQL outputs, 3=Enhanced error messages.';

#RSS News
$lang['pk_set_Show_rss']			= 'deactivate RSS News';
$lang['pk_help_Show_rss']			= 'If activated, Eqdkp Plus will not show Game RSS News.';

$lang['pk_set_Show_rss_style']		= 'game-news positioning';
$lang['pk_help_Show_rss_style']	= 'RSS-Game News positioning. Top horizontal, in the menu vertical or both?';

$lang['pk_set_Show_rss_lang']		= 'RSS-News default language ';
$lang['pk_help_Show_rss_lang']		= 'Get the RSS-News in wich language? (atm german only). English news available within 2009.';

$lang['pk_set_Show_rss_lang_de']	= 'German';
$lang['pk_set_Show_rss_lang_eng']	= 'English';

$lang['pk_set_Show_rss_style_both'] = 'Both' ;
$lang['pk_set_Show_rss_style_v']	 = 'menu vertical' ;
$lang['pk_set_Show_rss_style_h']	 = 'top horizontal' ;

$lang['pk_set_Show_rss_count']		= 'News Count (0 or "" for all)';
$lang['pk_help_Show_rss_count']	= 'How many News should be displayed?';

$lang['pk_set_itemhistory_dia']	= 'Dont show diagrams'; # Ja negierte Abfrage
$lang['pk_help_itemhistory_dia']	= 'If activated, Eqdkp Plus dont show diagrams.';

#Bridge
$lang['pk_set_bridge_help']				= 'On This Tab you can tune up the settings to let an Content Management System (CMS) interact with Eqdkp Plus.
											   If you choose one of the Systems in the Drop Down Field , Registered Members of your Forum/CMS will be able to log in into Eqdkp Plus with the same credentials used in Forum/CMS.
											   The Access is only allowed for one Group, that Means that you must create a new group in your CMS/Forum which all Members belong who will be accessing Eqdkp.';

$lang['pk_set_bridge_activate']			= 'Activate bridging to an CMS';
$lang['pk_help_bridge_activate']			= 'When bridging is activated, Users of the Forum or CMS will be able to Log On in Eqdkp Plus with the same credentials as used in CMS/Forum';

$lang['pk_set_bridge_dectivate_eq_reg']	= 'deactivate registering in Eqdkp Plus';
$lang['pk_help_bridge_dectivate_eq_reg']	= 'When activated new Users are not able to register at Eqdkp Plus. The registering of new Users must be done at CMS/Forum';

$lang['pk_set_bridge_cms']					= 'supported CMS';
$lang['pk_help_bridge_cms']				= 'Which CMS shall be bridged ';

$lang['pk_set_bridge_acess']				= 'Is the CMS/Forum on another Database than Eqdkp ?';
$lang['pk_help_bridge_acess']				= 'If you use the CMS/Forum on another Database activate this and fill the Fields below';

$lang['pk_set_bridge_host']				= 'Hostname';
$lang['pk_help_bridge_host']				= 'The Hostname or IP on which the Database Server iss listening';

$lang['pk_set_bridge_username']			= 'Database User';
$lang['pk_help_bridge_username']			= 'Username used to connect to Database';

$lang['pk_set_bridge_password']			= 'Database Userpassword';
$lang['pk_help_bridge_password']			= 'Password of the User to connect with';

$lang['pk_set_bridge_database']			= 'Database Name';
$lang['pk_help_bridge_database']			= 'Name of the Database where the CMS Data is in';

$lang['pk_set_bridge_prefix']				= 'Tableprefix of your CMS Installation';
$lang['pk_help_bridge_prefix']				= 'Give your Prefix of your CMS . e.g.. phpbb_ or wcf1_';

$lang['pk_set_bridge_group']				= 'Group ID of the CMS Group';
$lang['pk_help_bridge_group']				= 'Enter here the ID of the Group in the CMS which is allowed to access Eqdkp.';

$lang['pk_set_bridge_inline']				= 'Forum Integration EQDKP - BETA !';
$lang['pk_help_bridge_inline']				= 'When you enter a URL here, an additional link will be displayed in the menu, which shows the given site inside the Eqdkp. This is done with a dynamic iframe. The Eqdkp Plus is not responsible for the appereance and the behaviour of the site included in the iframe';

$lang['pk_set_bridge_inline_url']			= 'Forum URL';
$lang['pk_help_bridge_inline_url']			= 'Forum URL';

$lang['pk_set_link_type_header']			= 'Display style';
$lang['pk_set_link_type_help']				= '';
$lang['pk_set_link_type_iframe_help']		= 'How should the link be open. Embedded dynamic only works with sites installed on the same server';
$lang['pk_set_link_type_self']				= 'normal';
$lang['pk_set_link_type_link']				= 'New window';
$lang['pk_set_link_type_iframe']			= 'Embedded';

#recruitment
$lang['pk_set_recruitment_tab']			= 'Recruitment';
$lang['pk_set_recruitment_header']			= 'Recruitment - Are you looking for new Members ?';
$lang['pk_set_recruitment']				= 'activate recruitment';
$lang['pk_help_recruitment']				= 'If active, a box with the needed classes will shown on top of the news.';
$lang['pk_recruitment_count']				= 'Count';
$lang['pk_set_recruitment_contact_type']	= 'Linkurl';
$lang['pk_help_recruitment_contact_type']	= 'If no URL is given, it will link to the contact email.';
$lang['ps_recruitment_spec']				= 'Spec';

#comments
$lang['pk_set_comments_disable']			= 'deactivate comments';
$lang['pk_hel_pcomments_disable']			= 'deactivate the comments on all pages';

#Contact
$lang['pk_contact']						= 'Contact infos';
$lang['pk_contact_name']					= 'Name';
$lang['pk_contact_email']					= 'Email';
$lang['pk_contact_website']				= 'Website';
$lang['pk_contact_irc']					= 'IRC Channel';
$lang['pk_contact_admin_messenger']		= 'Messenger name (Skype, ICQ)';
$lang['pk_contact_custominfos']			= 'additional infos';
$lang['pk_contact_owner']					= 'Owner infos:';

#Next_raids
$lang['pk_set_nextraids_deactive']			= 'Dont show next raids';
$lang['pk_help_nextraids_deactive']		= 'If active, the next raids doesnt shown in the Menu';

$lang['pk_set_nextraids_limit']			= 'Limit the shown next raids';
$lang['pk_help_nextraids_limit']			= '';

$lang['pk_set_lastitems_deactive']			= 'Dont show the last Items';
$lang['pk_help_lastitems_deactive']		= 'If active, the last items shown in the Menu';

$lang['pk_set_lastitems_limit']			= 'Limit the shown last items';
$lang['pk_help_lastitems_limit']			= 'Limit the shown last items';

$lang['pk_is_help']						= ' Important: Change of Itemstats mannerism with EQdkp-Plus 0.6.2.4<br>
												If your items should not be displayed correctly anymore after an update, set new the "priority of item database" (we recommend Armory & WoWHead)
												and retrieve items again.
												<br>Use the "Update Itemstat Link" below this message.<br>
												The best result will be achieved with the setting "Armory & WoWHead", since only Blizzards Armory delievers additional information like droprate,
												mob and dungeon per item dropped.
												In order to refresh the item cache follow the link, then choose "Clear Cache" and "Update Itemtable" after that.<br><br>
												Imortant: If you changed the web database you have to empty cache, if you don\'t existing item tooltips will  not be displayed properly.<br><br>';

$lang['pk_set_normal_leaderbaord']			= 'Show Leaderboard with Slider';
$lang['pk_help_normal_leaderbaord']		= 'If active, the Leaderboard use Sliders.';

$lang['pk_set_thirdColumn']				= 'Dont show the third column';
$lang['pk_help_thirdColumn']				= 'Dont show the third column';

#GetDKP
$lang['pk_getdkp_th']						= 'GetDKP Setting';

$lang['pk_set_getdkp_rp']					= 'activate raidplan';
$lang['pk_help_getdkp_rp']					= 'activate raidplan';

$lang['pk_set_getdkp_link']				= 'show getdkp link in the mainmenu';
$lang['pk_help_getdkp_link']				= 'show getdkp link in the mainmenu';

$lang['pk_set_getdkp_active']				= 'deactivate getdkp.php';
$lang['pk_help_getdkp_active']				= 'deactivate getdkp.php';

$lang['pk_set_getdkp_items']				= 'disable itemIDs';
$lang['pk_help_getdkp_items']				= 'disable itemIDs';

$lang['pk_set_recruit_embedded']			= 'open Link embedded';
$lang['pk_help_recruit_embedded']			= 'if activeted, the link opens embedded i an iframe';


$lang['pk_set_dis_3dmember']				= 'deactivate 3D Modelviewer for Members';
$lang['pk_help_dis_3dmember']				= 'deactivate 3D Modelviewer for Members';

$lang['pk_set_dis_3ditem']					= 'deactivate 3D Modelviewer for Items';
$lang['pk_help_dis_3item']					= 'deactivate 3D Modelviewer for Items';

$lang['pk_set_disregister']				= 'deactivate the user registration';
$lang['pk_help_disregister']				= 'deactivate the user registration';

# Portal Manager
$lang['portalplugin_name']         = 'Modul';
$lang['portalplugin_version']      = 'Verson';
$lang['portalplugin_contact']      = 'Contact';
$lang['portalplugin_order']        = 'Sorting';
$lang['portalplugin_orientation']  = 'Orientation';
$lang['portalplugin_enabled']      = 'Activ';
$lang['portalplugin_save']         = 'Save Changes';
$lang['portalplugin_management']   = 'Manage Portal Modules';
$lang['portalplugin_right']        = 'Right';
$lang['portalplugin_middle']       = 'Middle';
$lang['portalplugin_left1']        = 'Left on top of menu';
$lang['portalplugin_left2']        = 'Left below menu';
$lang['portalplugin_settings']     = 'Settings';
$lang['portalplugin_winname']      = 'Portal Module Settings';
$lang['portalplugin_edit']         = 'Edit';
$lang['portalplugin_save']         = 'Save';
$lang['portalplugin_rights']       = 'Visibility';
$lang['portal_rights0']            = 'All';
$lang['portal_rights1']            = 'Guests';
$lang['portal_rights2']            = 'Registered';
$lang['portal_collapsable']        = 'Collapsable';

$lang['pk_set_link_type_D_iframe']			= 'Embedded dynamic high';

$lang['pk_set_modelviewer_default']	= 'Default Modelviewer';


 /* IMAGE RESIZE */
 // Lytebox settings
 $lang['pk_air_img_resize_options'] = 'Lytebox Settings';
 $lang['pk_air_img_resize_enable'] = 'Enable image resizing';
 $lang['pk_air_max_post_img_resize_width'] = 'Maximum image width resize';
 $lang['pk_air_show_warning'] = 'Enable Warning, if the image was rezised';
 $lang['pk_air_lytebox_theme'] = 'Lytebox\'s theme';
 $lang['pk_air_lytebox_theme_explain'] = 'Themes: grey (default), red, green, blue, gold';
 $lang['pk_air_lytebox_auto_resize'] = 'Enable auto resize';
 $lang['pk_air_lytebox_auto_resize_explain'] = 'Controls whether or not images should be resized if larger than the browser window dimensions';
 $lang['pk_air_lytebox_animation'] = 'Enable animation';
 $lang['pk_air_lytebox_animation_explain'] = 'Controls whether or not "animate" Lytebox, i.e. resize transition between images, fade in/out effects, etc.';
 $lang['pk_air_lytebox_grey'] = 'Grey';
 $lang['pk_air_lytebox_red'] = 'Red';
 $lang['pk_air_lytebox_blue'] = 'Blue';
 $lang['pk_air_lytebox_green'] = 'Green';
 $lang['pk_air_lytebox_gold'] = 'Gold';

 $lang['pk_set_hide_shop'] = 'Hide Shop-Link';
 $lang['pk_help_hide_shop'] = 'Hide Shop-Link';

$lang['pk_set_rss_chekurl'] = 'check RSS-URL bevor update';
 $lang['pk_help_rss_chekurl'] = 'Controls whether or not the RSS-URL checked before update.';

$lang['pk_set_noDKP'] = 'Hide DKP function';
$lang['pk_help_noDKP'] = 'If activated all other DKP functions are disabled and no notice to dkp-points will be shown. Doesnt apply to raid and event list. ';

$lang['pk_set_noRoster'] = 'Hide Roster';
$lang['pk_help_noRoster'] = 'If activated the roster page will not be shown in the menu and access to this page will be disables';

$lang['pk_set_noDKP'] = 'Show member roster instead of dkp-point overview';
$lang['pk_help_noDKP'] = 'If activated, member-roster will be shown instead of DKP-point overview';

$lang['pk_set_noRaids'] = 'Hide raid functions';
$lang['pk_help_noRaids'] = 'If activated, all raid-functions are de-activated. Doesnt apply to the event-history';

$lang['pk_set_noEvents'] = 'Hide Events';
$lang['pk_help_noEvents'] = 'If activated, all event-functions are disabled. IMPORTANT: Events are needed for the raidplaner!';

$lang['pk_set_noItemPrices'] = 'Hide item prices';
$lang['pk_help_noItemPrices'] = 'If activated, the link to the item prices page is de-activated and blocked.';

$lang['pk_set_noItemHistoy'] = 'Hide item history';
$lang['pk_help_noItemHistoy'] = 'If activated, the link to the item history page is de-activated and blocked.';

$lang['pk_set_noStats'] = 'Hide summary and statictic';
$lang['pk_help_noStats'] = 'If activated, the link to the summary and statistics page is de-activated and blocked.';

$lang['pk_set_cms_register_url'] = 'CMS/Forums registration URL';
$lang['pk_help_cms_register_url'] = 'On activated bridge the eqDKP registration link will forward to this URL for registration purposes.';

$lang['pk_set_link_type_menu']			= 'Menu';
$lang['pk_set_link_type_menuH']		= 'Tab Menu';

# Filter in portal modules
$lang['portalplugin_filter'] = 'Filter';
$lang['portalplugin_filter1_all']	= 'All module positions';
$lang['portalplugin_filter2_all'] = 'Active & inactive';
$lang['portalplugin_filter3_all'] = 'All Permissions';
$lang['portalplugin_disabled'] = 'Inactive';

//Style install
$lang['eq_style_install'] = 'Install / Create';
$lang['eq_style_new'] = 'Install new Template from Filesystem';
$lang['eq_style_choose_newstyle'] = 'Choose Template:';
$lang['eq_style_choose_oldstyle'] = 'Or create a new Styles based on installed Styles';
$lang['eq_style_new_help'] = 'Copy a downloaded Template into your /template/ Folder to install. Must have an installer file /templatename/install/index.php.';

$lang['page_manager'] = 'Manage pages';

//maintenance mode
$lang['pk_maintenance_mode'] = 'Activate maintenance mode.';
$lang['pk_help_maintenance'] = 'Activating the maintenance mode will cause all non admin users to be redirected to a maintenance page and allows the admin to do maintenance on the eqdkp plus system.';

// Plugin Update Warn Class
$lang['puc_perform_intro']          = 'The following Plugins need updates of their database structure. Please click on the "solve" Link to perform the database changes for each plugin.<br/>Following database tables are out of date:';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Requires database updates from %2\$s to %3\$s)";
$lang['puc_solve_dbissues']         = 'solve';
$lang['puc_unknown']                = '[unknown]';

$lang['plus_cache_reset_done']      = 'Reset done!';
$lang['plus_cache_reset_name']      = 'Plus Data Cache';

// Update Check PLUS
$lang['lib_pupd_intro']             = 'There are new Versions available:';
$lang['lib_pupd_updtxt']            = "<b>%1\$s</b>: %2\$s (installed: %3\$s), released on %4\$s";
$lang['lib_pupd_noupdates']         = 'All EQDKP-PLUS plugins are up 2 date! There are currently no new Updates available. Please Re-Check later...';
$lang['lib_pupd_nodbupdates']       = 'All Database tables are up2date! There are currently no new Updates needed.';
$lang['lib_pupd_changelog']         = 'Changelog';
$lang['lib_pupd_nochangelog']       = 'No changelog information available';
$lang['lib_pupd_download']          = 'Download';
$lang['lib_pupd_checknow']          = 'Check now for updates again!';

// RSS News
$lang['rssadmin_head1']             = 'Notifications';
$lang['rssadmin_head2']             = 'Eqdkp-Plus News';

// Admin Info Center
$lang['adminc_information']         = 'Information';
$lang['adminc_news']                = 'News';
$lang['adminc_updtcheck']           = 'Updates';
$lang['adminc_statistics']          = 'Statistics';
$lang['adminc_server']              = 'PHP Info';
$lang['adminc_support']             = 'Support';
$lang['adminc_phpvalue']            = 'Value';
$lang['adminc_phpname']             = 'PHP Setting name';
$lang['adminc_info_dbupdates_av']   = 'Not all Database table of the installed plugins are up 2 date. Please visit the "Updates" Tab for further information.';
$lang['adminc_info_updates_av']     = 'There are new updates available. Please visit the "Updates" Tab for further information.';
$lang['adminc_support_intro']       = "If you've got Questions according to EQDKP-PLUS, please make sure you'd visited one of the following resources.";
$lang['adminc_support_wiki']        = "The WIKI is an online documentation System. There are several answers, faq and other. Users are encouraged to write own articles and help the community.<br><a href='http://wiki.eqdkp-plus.com' target='blank'>To the WIKI</a>";
$lang['adminc_support_bugtracker']  = "You encountered a bug? Help us by first searching in the bugtracker, open a new report if nessessary<br><a href='http://bugtracker.eqdkp-plus.com' target='blank'>To the Bugtracker</a>";
$lang['adminc_support_forums']      = "You've got Questions? Need Help? Searched the WIKI without any answer? Then visit the official Forums for help!<br><a href='http://www.eqdkp-plus.com/forum' target='blank'>To the Forums</a>";

//Plugin dependency additions
$lang['plug_dep_title'] = "Dependencies";
$lang['plug_dep_plusv'] = "Plus version";
$lang['plug_dep_libsv'] = "Library version";
$lang['plug_dep_games'] = "Game support";
$lang['plug_dep_broken_deps'] = "Broken dependencies!";

?>