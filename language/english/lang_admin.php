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
$lang['addturnin_title']      = "Add a Turn-in - Step %1\$d";
$lang['admin_index_title']    = 'EQdkp Administration';
$lang['config_title']         = 'Script Configuration';
$lang['manage_members_title'] = 'Manage Guild Members';
$lang['manage_users_title']   = 'User Accounts and Permissions';
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
$lang['add_adjustment'] = 'Add Adjustment';
$lang['add_account'] = 'Add Account';
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
$lang['adjustment_value'] = 'Adjustment Value';
$lang['adjustment_value_note'] = 'May be negative';
$lang['code'] = 'Code';
$lang['contact'] = 'Contact';
$lang['create'] = 'Create';
$lang['found_members'] = "Parsed %1\$d lines, found %2\$d members";
$lang['headline'] = 'Headline';
$lang['hide'] = 'Hide?';
$lang['install'] = 'Install';
$lang['item_search'] = 'Item Search';
$lang['list_prefix'] = 'List Prefix';
$lang['list_suffix'] = 'List Suffix';
$lang['logs'] = 'Logs';
$lang['log_find_all'] = 'Find all (including anonymous)';
$lang['manage_members'] = 'Manage Members';
$lang['manage_plugins'] = 'Manage Plugins';
$lang['manage_users'] = 'Manage Users';
$lang['mass_update_note'] = 'If you wish to apply changes to all of the items selected above, use these controls to change their properties and click on "Mass Update".
                             To delete the selected accounts, just click on "Mass Delete".';
$lang['members'] = 'Members';
$lang['member_rank'] = 'Member Rank';
$lang['message_body'] = 'Message Body';
$lang['message_show_loot_raid'] = 'Show Loot from Raid:';
$lang['results'] = "%1\$d Results (\"%2\$s\")";
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
$lang['backup'] = 'Backup';
$lang['backup_title'] = 'Create a database backup';
$lang['create_table'] = 'Add \'CREATE TABLE\' statements?';
$lang['skip_nonessential'] = 'Skip non essential data?<br />Will not produce insert rows for eqdkp_sessions.';
$lang['gzip_content'] = 'GZIP Content?<br />Will produce a smaller file if GZIP is enabled.';
$lang['backup_database'] = 'Backup Database';

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

$lang['vlog_multidkp_added']     = "%1\$s added the MultiDKP Pool %2\$s zu.";
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


?>
