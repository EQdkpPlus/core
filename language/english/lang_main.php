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
$lang['XML_LANG'] = 'en';

// Linknames
$lang['rp_link_name']   = "Raidplanner";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Group Adjustment Listing';
$lang['listevents_title']     = 'Event Values';
$lang['listiadj_title']       = 'Individual Adjustment Listing';
$lang['listitems_title']      = 'Item Values';
$lang['listnews_title']       = 'News Entries';
$lang['listmembers_title']    = 'Member Standings';
$lang['listpurchased_title']  = 'Item History';
$lang['listraids_title']      = 'Raids Listing';
$lang['login_title']          = 'Login';
$lang['message_title']        = 'EQdkp: Message';
$lang['register_title']       = 'Register';
$lang['settings_title']       = 'Account Settings';
$lang['stats_title']          = "%1\$s Stats";
$lang['summary_title']        = 'News Summary';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Viewing Recorded Raid History for %1\$s";
$lang['viewitem_title']       = "Viewing Purchase History for %1\$s";
$lang['viewmember_title']     = "History for %1\$s";
$lang['viewraid_title']       = 'Raid Summary';

// Main Menu
$lang['menu_admin_panel'] = 'Administration Panel';
$lang['menu_events'] = 'Events';
$lang['menu_itemhist'] = 'Item History';
$lang['menu_itemval'] = 'Item Values';
$lang['menu_news'] = 'News';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'Register';
$lang['menu_settings'] = 'Settings';
$lang['menu_members'] = 'Characters';
$lang['menu_standings'] = 'Standings';
$lang['menu_stats'] = 'Stats';
$lang['menu_summary'] = 'Summary';

// Column Headers
$lang['account'] = 'Account';
$lang['action'] = 'Action';
$lang['active'] = 'Active';
$lang['add'] = 'Add';
$lang['added_by'] = 'Added By';
$lang['adjustment'] = 'Adjustment';
$lang['administration'] = 'Administration';
$lang['administrative_options'] = 'Administrative Options';
$lang['admin_index'] = 'Admin Index';
$lang['attendance_by_event'] = 'Attendance by Event';
$lang['attended'] = 'Attended';
$lang['attendees'] = 'Attendees';
$lang['average'] = 'Average';
$lang['buyer'] = 'Buyer';
$lang['buyers'] = 'Buyers';
$lang['class'] = 'Class';
$lang['armor'] = 'Armor';
$lang['type'] = 'Armor';
$lang['class_distribution'] = 'Class Distribution';
$lang['class_summary'] = "Class Summary: %1\$s to %2\$s";
$lang['configuration'] = 'Configuration';
$lang['config_plus']	= 'PLUS Settings';
$lang['plus_vcheck']	= 'Update Check';
$lang['current'] = 'Current';
$lang['date'] = 'Date';
$lang['delete'] = 'Delete';
$lang['delete_confirmation'] = 'Delete Confirmation';
$lang['dkp_value'] = "%1\$s Value";
$lang['drops'] = 'Drops';
$lang['earned'] = 'Earned';
$lang['enter_dates'] = 'Enter Dates';
$lang['eqdkp_index'] = 'EQdkp Index';
$lang['eqdkp_upgrade'] = 'EQdkp Upgrade';
$lang['event'] = 'Event';
$lang['events'] = 'Events';
$lang['filter'] = 'Filter';
$lang['first'] = 'First';
$lang['rank'] = 'Rank';
$lang['general_admin'] = 'General Admin';
$lang['get_new_password'] = 'Get a New Password';
$lang['group_adj'] = 'Group Adj.';
$lang['group_adjustments'] = 'Group Adjustments';
$lang['individual_adjustments'] = 'Individual Adjustments';
$lang['individual_adjustment_history'] = 'Individual Adjustment History';
$lang['indiv_adj'] = 'Indiv. Adj.';
$lang['ip_address'] = 'IP Address';
$lang['item'] = 'Item';
$lang['items'] = 'Items';
$lang['item_purchase_history'] = 'Item Purchase History';
$lang['last'] = 'Last';
$lang['lastloot'] = 'Last Loot';
$lang['lastraid'] = 'Last Raid';
$lang['last_visit'] = 'Last Visit';
$lang['level'] = 'Level';
$lang['log_date_time'] = 'Date/Time of this Log';
$lang['loot_factor'] = 'Loot Factor';
$lang['loots'] = 'Loots';
$lang['manage'] = 'Manage';
$lang['member'] = 'Member';
$lang['members'] = 'Members';
$lang['members_present_at'] = "Members Present at %1\$s on %2\$s";
$lang['miscellaneous'] = 'Miscellaneous';
$lang['name'] = 'Name';
$lang['news'] = 'News';
$lang['note'] = 'Note';
$lang['online'] = 'Online';
$lang['options'] = 'Options';
$lang['paste_log'] = 'Paste a Log Below';
$lang['percent'] = 'Percent';
$lang['permissions'] = 'Permissions';
$lang['per_day'] = 'Per Day';
$lang['per_raid'] = 'Per Raid';
$lang['pct_earned_lost_to'] = '% Earned Lost to';
$lang['preferences'] = 'Preferences';
$lang['purchase_history_for'] = "Purchase History for %1\$s";
$lang['quote'] = 'Quote';
$lang['race'] = 'Race';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'Raid Attendance History';
$lang['raids_lifetime'] = "Lifetime (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Last %1\$d Days";
$lang['rank_distribution'] = 'Rank Distribution';
$lang['recorded_raid_history'] = "Recorded Raid History for %1\$s";
$lang['reason'] = 'Reason';
$lang['registration_information'] = 'Registration Information';
$lang['result'] = 'Result';
$lang['session_id'] = 'Session ID';
$lang['settings'] = 'Settings';
$lang['spent'] = 'Spent';
$lang['summary_dates'] = "Raid Summary: %1\$s to %2\$s";
$lang['themes'] = 'Themes';
$lang['time'] = 'Time';
$lang['total'] = 'Total';
$lang['total_earned'] = 'Total Earned';
$lang['total_items'] = 'Total Items';
$lang['total_raids'] = 'Total Raids';
$lang['total_spent'] = 'Total Spent';
$lang['transfer_member_history'] = 'Transfer Member History';
$lang['turn_ins'] = 'Turn-ins';
$lang['type'] = 'Type';
$lang['update'] = 'Update';
$lang['updated_by'] = 'Updated By';
$lang['user'] = 'User';
$lang['username'] = 'Username';
$lang['value'] = 'Value';
$lang['view'] = 'View';
$lang['view_action'] = 'View Action';
$lang['view_logs'] = 'View Logs';

// Page Foot Counts
$lang['listadj_footcount']               = "... found %1\$d adjustment(s) / %2\$d per page";
$lang['listevents_footcount']            = "... found %1\$d events / %2\$d per page";
$lang['listiadj_footcount']              = "... found %1\$d individual adjustment(s) / %2\$d per page";
$lang['listitems_footcount']             = "... found %1\$d unique items / %2\$d per page";
$lang['listmembers_active_footcount']    = "... found %1\$d active members / %2\$sshow all</a>";
$lang['listmembers_compare_footcount']   = "... comparing %1\$d members";
$lang['listmembers_footcount']           = "... found %1\$d members";
$lang['listnews_footcount']              = "... found %1\$d news entries / %2\$d per page";
$lang['listpurchased_footcount']         = "... found %1\$d item(s) / %2\$d per page";
$lang['listraids_footcount']             = "... found %1\$d raid(s) / %2\$d per page";
$lang['stats_active_footcount']          = "... found %1\$d active member(s) / %2\$sshow all</a>";
$lang['stats_footcount']                 = "... found %1\$d member(s)";
$lang['viewevent_footcount']             = "... found %1\$d raid(s)";
$lang['viewitem_footcount']              = "... found %1\$d item(s)";
$lang['viewmember_adjustment_footcount'] = "... found %1\$d individual adjustment(s)";
$lang['viewmember_item_footcount']       = "... found %1\$d purchased item(s) / %2\$d per page";
$lang['viewmember_raid_footcount']       = "... found %1\$d attended raid(s) / %2\$d per page";
$lang['viewraid_attendees_footcount']    = "... found %1\$d attendee(s)";
$lang['viewraid_drops_footcount']        = "... found %1\$d drop(s)";

// Submit Buttons
$lang['close_window'] = 'Close Window';
$lang['compare_members'] = 'Compare Members';
$lang['create_news_summary'] = 'Create News Summary';
$lang['login'] = 'Login';
$lang['logout'] = 'Logout';
$lang['log_add_data'] = 'Add Data to Form';
$lang['lost_password'] = 'Lost Password';
$lang['no'] = 'No';
$lang['proceed'] = 'Proceed';
$lang['reset'] = 'Reset';
$lang['set_admin_perms'] = 'Set Administrative Permissions';
$lang['submit'] = 'Submit';
$lang['upgrade'] = 'Upgrade';
$lang['yes'] = 'Yes';

// Form Element Descriptions
$lang['admin_login'] = 'Administrator Login';
$lang['confirm_password'] = 'Confirm Password';
$lang['confirm_password_note'] = 'You only need to confirm your new password if you changed it above';
$lang['current_password'] = 'Current Password';
$lang['current_password_note'] = 'You must confirm your current password if you wish to change your username or password';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email Address';
$lang['ending_date'] = 'Ending Date';
$lang['from'] = 'From';
$lang['guild_tag'] = 'Guild Tag';
$lang['language'] = 'Language';
$lang['new_password'] = 'New Password';
$lang['new_password_note'] = 'You only need to supply a new password if you want to change it';
$lang['password'] = 'Password';
$lang['remember_password'] = 'Remember me (cookie)';
$lang['starting_date'] = 'Starting Date';
$lang['style'] = 'Style';
$lang['to'] = 'To';
$lang['username'] = 'Username';
$lang['users'] = 'Users';

// Pagination
$lang['next_page'] = 'Next Page';
$lang['page'] = 'Page';
$lang['previous_page'] = 'Previous Page';

// Permission Messages
$lang['noauth_default_title'] = 'Permission Denied';
$lang['noauth_u_event_list'] = 'You do not have permission to list events.';
$lang['noauth_u_event_view'] = 'You do not have permission to view events.';
$lang['noauth_u_item_list'] = 'You do not have permission to list items.';
$lang['noauth_u_item_view'] = 'You do not have permission to view items.';
$lang['noauth_u_member_list'] = 'You do not have permission to view member standings.';
$lang['noauth_u_member_view'] = 'You do not have permission to view member history.';
$lang['noauth_u_raid_list'] = 'You do not have permission to list raids.';
$lang['noauth_u_raid_view'] = 'You do not have permission to view raids.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Your vote on the item has been recorded.';
$lang['update_itemvote_success'] = 'Your vote on the item has been updated.';
$lang['update_settings_success'] = 'The user settings have been updated.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Characters\' names in EverQuest contain only alphabetic characters.';
$lang['fv_already_registered_email'] = 'That e-mail address is already registered.';
$lang['fv_already_registered_username'] = 'That username is already registered.';
$lang['fv_difference_transfer'] = 'A history transfer must be made between two different people.';
$lang['fv_difference_turnin'] = 'A turn-in must be made between two different people.';
$lang['fv_invalid_email'] = 'The e-mail address does not appear to be valid.';
$lang['fv_match_password'] = 'The password fields must match.';
$lang['fv_member_associated']  = "%1\$s is already associated with another user account.";
$lang['fv_number'] = 'Must be a number.';
$lang['fv_number_adjustment'] = 'The adjustment value field must be a number.';
$lang['fv_number_alimit'] = 'The adjustments limit field must be a number.';
$lang['fv_number_ilimit'] = 'The items limit field must be a number.';
$lang['fv_number_inactivepd'] = 'The inactive period must be a number.';
$lang['fv_number_pilimit'] = 'The purchased items limit must be a number.';
$lang['fv_number_rlimit'] = 'The raids limit must be a number.';
$lang['fv_number_value'] = 'The value field must be a number.';
$lang['fv_number_vote'] = 'The vote field must be a number.';
$lang['fv_date'] = 'Please choose a valid date from the calendar.';
$lang['fv_range_day'] = 'The day field must be an integer between 1 and 31.';
$lang['fv_range_hour'] = 'The hour field must be an integer between 0 and 23.';
$lang['fv_range_minute'] = 'The minute field must be an integer between 0 and 59.';
$lang['fv_range_month'] = 'The month field must be an integer between 1 and 12.';
$lang['fv_range_second'] = 'The second field must be an integer between 0 and 59.';
$lang['fv_range_year'] = 'The year field must be an integer with a value of at least 1998.';
$lang['fv_required'] = 'Required Field';
$lang['fv_required_acro'] = 'The guild acronym field is required.';
$lang['fv_required_adjustment'] = 'The adjustment value field is required.';
$lang['fv_required_attendees'] = 'There must be at least one attendee on this raid.';
$lang['fv_required_buyer'] = 'A buyer must be selected.';
$lang['fv_required_buyers'] = 'At least one buyer must be selected.';
$lang['fv_required_email'] = 'The e-mail address field is required.';
$lang['fv_required_event_name'] = 'An event must be selected.';
$lang['fv_required_guildtag'] = 'The guildtag field is required.';
$lang['fv_required_headline'] = 'The headline field is required.';
$lang['fv_required_inactivepd'] = 'If the hide inactive members field is set to Yes, a value for the inactive period must also be set.';
$lang['fv_required_item_name'] = 'The item name field must be filled out or an existing item must be selected.';
$lang['fv_required_member'] = 'A member must be selected.';
$lang['fv_required_members'] = 'At least one member must be selected.';
$lang['fv_required_message'] = 'The message field is required.';
$lang['fv_required_name'] = 'The name field is required.';
$lang['fv_required_password'] = 'The password field is required.';
$lang['fv_required_raidid'] = 'A raid must be selected.';
$lang['fv_required_user'] = 'The username field is required.';
$lang['fv_required_value'] = 'The value field is required.';
$lang['fv_required_vote'] = 'The vote field is required.';

// Miscellaneous
$lang['added'] = 'Added';
$lang['additem_raidid_note'] = "Only raids less than two weeks old are shown / %1\$sshow all</a>";
$lang['additem_raidid_showall_note'] = 'Showing all raids';
$lang['addraid_datetime_note'] = 'If you parse a log, the date and time will be found automatically.';
$lang['addraid_value_note'] = 'for a one-time bonus; preset value for the event selected is used if left blank';
$lang['add_items_from_raid'] = 'Add Items from this Raid';
$lang['deleted'] = 'Deleted';
$lang['done'] = 'Done';
$lang['enter_new'] = 'Enter New';
$lang['error'] = 'Error';
$lang['head_admin'] = 'Head Admin';
$lang['hold_ctrl_note'] = 'Hold CTRL to select multiple';
$lang['list'] = 'List';
$lang['list_groupadj'] = 'List Group Adjustments';
$lang['list_events'] = 'List Events';
$lang['list_indivadj'] = 'List Individual Adjustments';
$lang['list_items'] = 'List Items';
$lang['list_members'] = 'List Members';
$lang['list_news'] = 'List News';
$lang['list_raids'] = 'List Raids';
$lang['may_be_negative_note'] = 'may be negative';
$lang['not_available'] = 'Not Available';
$lang['no_news'] = 'No news entries found.';
$lang['of_raids'] = "%1\$d%% of raids";
$lang['or'] = 'Or';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Preview';
$lang['required_field_note'] = 'Items marked with a * are required fields.';
$lang['select_1ofx_members'] = "Select 1 of %1\$d members...";
$lang['select_existing'] = 'Select Existing';
$lang['select_version'] = 'Select the EQdkp version that you are upgrading from';
$lang['success'] = 'Success';
$lang['s_admin_note'] = 'These fields cannot be modified by the users.';
$lang['transfer_member_history_description'] = 'This transfers all of a member\'s history (raids, items, adjustments) to another member.';
$lang['updated'] = 'Updated';
$lang['upgrade_complete'] = 'Your EQdkp installation has been successfully upgraded.<br /><br /><b class="negative">For extra security, remove this file!</b>';

// Settings
$lang['account_settings'] = 'Account Settings';
$lang['adjustments_per_page'] = 'Adjustments per Page';
$lang['basic'] = 'Basic';
$lang['events_per_page'] = 'Events per Page';
$lang['items_per_page'] = 'Items per Page';
$lang['news_per_page'] = 'News Entries per Page';
$lang['raids_per_page'] = 'Raids per Page';
$lang['associated_members'] = 'Associated Members';
$lang['guild_members'] = 'Guild Member(s)';
$lang['default_locale'] = 'Default Locale';


// Error messages
$lang['error_account_inactive'] = 'Your account is inactive.';
$lang['error_already_activated'] = 'That account has already been activated.';
$lang['error_email_send'] = 'Failed sending email.';
$lang['error_invalid_email'] = 'A valid e-mail address was not provided.';
$lang['error_invalid_event_provided'] = 'A valid event id was not provided.';
$lang['error_invalid_item_provided'] = 'A valid item id was not provided.';
$lang['error_invalid_key'] = 'You have provided an invalid activation key.';
$lang['error_invalid_name_provided'] = 'A valid member name was not provided.';
$lang['error_invalid_news_provided'] = 'A valid news id was not provided.';
$lang['error_invalid_raid_provided'] = 'A valid raid id was not provided.';
$lang['error_user_not_found'] = 'A valid username was not provided';
$lang['error_invalid_password'] = 'The password must not contain " or \'.';
$lang['incorrect_password'] = 'Incorrect password';
$lang['invalid_login'] = 'You have provided an incorrect or invalid username or password';
$lang['not_admin'] = 'You are not an administrator';

// Registration
$lang['account_activated_admin']   = 'The account has been activated. An e-mail has been sent to the user informing them of this change.';
$lang['account_activated_user']    = "Your account has been activated and you can now %1\$slog in%2\$s.";
$lang['password_sent'] = 'Your new account password has been e-mailed to you.';
$lang['register_activation_self']  = "Your account has been created, but before you can use it you need to activate it.<br /><br />An e-mail has been sent to %1\$s with information on how to activate your account.";
$lang['register_activation_admin'] = "Your account has been created, but before you can use it an administrator needs to activate it.<br /><br />An e-mail has been sent to %1\$s with more information.";
$lang['register_activation_none']  = "Your account has been created and you can now %1\$slog in%2\$s.<br /><br />An e-mail has been sent to %3\$s with more information.";

//plus
$lang['news_submitter'] = 'submitted by';
$lang['news_submitat'] = 'at';
$lang['droprate_loottable'] = "Loot Table for";
$lang['droprate_name'] = "Item Name";
$lang['droprate_count'] = "Count";
$lang['droprate_drop'] = "Drop %";

$lang['Itemsearch_link'] = "Item-Search";
$lang['Itemsearch_search'] = "Item Search :";
$lang['Itemsearch_searchby'] = "Search by :";
$lang['Itemsearch_item'] = "Item ";
$lang['Itemsearch_buyer'] = "Buyer ";
$lang['Itemsearch_raid'] = "Raid ";
$lang['Itemsearch_unique'] = "Unique item results :";
$lang['Itemsearch_no'] = "No";
$lang['Itemsearch_yes'] = "Yes";

$lang['bosscount_player'] = "Player: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "Items: ";
$lang['bosscount_dkptotal'] = "total DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Add Pool";
$lang['Multi_pageheader'] 		= "MultiDKP - Show Pools";
$lang['Multi_events'] 				= "Events:";
$lang['Multi_eventname'] 				= "Eventname";
$lang['Multi_discnottolong'] 	= "(Name of row) - this one should not be too long, the table will get large,. Choose p.e MC, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Accountname:";
$lang['Multi_discr'] 					= "Description:";
$lang['Multi_events'] 				= "Events of this Pool";

$lang['Multi_addkonto'] 			  = "Add MultiDKP Pool";
$lang['Multi_updatekonto'] 			= "Change Pool";
$lang['Multi_deletekonto'] 			= "Delete Pool";
$lang['Multi_viewkonten']			  = "Show MultiDKP Pools";
$lang['Multi_chooseevents']			= "Choose Events";
$lang['multi_footcount'] 				= "... %1\$d DKP Pools / %2\$d per page";
$lang['multi_error_invalid']    = "No Pools assigned....";
$lang['Multi_required_event']   = "You must choose at least one event!";
$lang['Multi_required_name']    = "You must insert a name!";
$lang['Multi_required_disc']    = "You must insert a description!";
$lang['Multi_admin_add_multi_success'] = "The Pool %1\$s ( %2\$s ) with the events %3\$s was added to the database.";
$lang['Multi_admin_update_multi_success'] = "The Pool %1\$s ( %2\$s ) with the events %3\$s was changed in the database.";
$lang['Multi_admin_delete_success']           = "The Pool %1\$s was deleted in the database.";
$lang['Multi_confirm_delete']    = 'Are you really sure you want to delete that Pool?';

$lang['Multi_total_cost']   										= 'Total points for this Pool';
$lang['Multi_Accs']    													= 'MultiDKP Pool';

//update
$lang['upd_eqdkp_status']    										= 'EQDKP update status';
$lang['upd_system_status']    									= 'System status';
$lang['upd_template_status']    								= 'Template status';
$lang['upd_gamefile_status']                    = 'Game Status';
$lang['upd_update_need']    										= 'Update necessary!';
$lang['upd_update_need_link']    								= 'Install all required components';
$lang['upd_no_update']    											= 'No update necessary. The system is up to date.';
$lang['upd_status']    													= 'Status';
$lang['upd_state_error']    										= 'Error';
$lang['upd_sql_string']    											= 'SQL command';
$lang['upd_sql_status_done']    								= 'done';
$lang['upd_sql_error']    											= 'Error';
$lang['upd_sql_footer']    											= 'SQL command executed';
$lang['upd_sql_file_error']    									= 'Error: The required SQL File %1\$s could not be found!';
$lang['upd_eqdkp_system_title']    							= 'EQDKP System component update';
$lang['upd_plus_version']    										= 'EQDKP Plus version';
$lang['upd_plus_feature']    										= 'Feature';
$lang['upd_plus_detail']    										= 'Details';
$lang['upd_update']    													= 'Update';
$lang['upd_eqdkp_template_title']    						= 'EQDKP template update';
$lang['upd_eqdkp_gamefile_title']               = 'EQDKP game update';
$lang['upd_gamefile_availversion']              = 'Available Version';
$lang['upd_gamefile_instversion']               = 'Installed Version';
$lang['upd_template_name']    									= 'Template name';
$lang['upd_template_state']    									= 'Template status';
$lang['upd_template_filestate']    							= 'Template folder available';
$lang['upd_link_install']    										= 'Update';
$lang['upd_link_reinstall']    									= 're-install';
$lang['upd_admin_need_update']    							= 'A database error has been detected. The system is not up to date and needs to be updated.';
$lang['upd_admin_link_update']									= 'Click here to solve the problem.';
$lang['upd_backto']    													= 'Back to overview';

// Event Icon
$lang['event_icon_header']    								  = 'Select event icon';

//update Itemstats
$lang['updi_header']    								    	= 'Refresh Itemstats data';
$lang['updi_header2']    								    	= 'Itemstats information';
$lang['updi_action']    								    	= 'action';
$lang['updi_notfound']    								    = 'Not found';
$lang['updi_writeable_ok']    							  = 'File is writable';
$lang['updi_writeable_no']    								= 'File is NOT writable';
$lang['updi_help']    								    		= 'Description';
$lang['updi_footcount']    								    = 'Item refreshed';
$lang['updi_curl_bad']    								    = 'The required PHP function cURL could not be found. Maybe Itemstats will not work properly. Please contact your administrator.';
$lang['updi_curl_ok']    								    	= 'cURL found.';
$lang['updi_fopen_bad']    								    = 'The required PHP function fopen could not be found. Maybe Itemstats will not work properly. Please contact your administrator.';
$lang['updi_fopen_ok']    								    = 'fopen found.';
$lang['updi_nothing_found']						    		= 'No items found';
$lang['updi_itemscount']  						    		= 'Itemcache entries:';
$lang['updi_baditemscount']						    		= 'Bad entries:';
$lang['updi_items']										    		= 'Items in database:';
$lang['updi_items_duplicate']					    		= '{With double items}';
$lang['updi_show_all']    								    = 'List all items with Itemstats';
$lang['updi_refresh_all']    								  = 'Delete all Items and refresh them.';
$lang['updi_refresh_bad']    								  = 'Refresh only bad items';
$lang['updi_refresh_raidbank']    						= 'Refresh Raidbanker items';
$lang['updi_refresh_tradeskill']   						= 'Refresh Tradeskill items';
$lang['updi_help_show_all']    								= 'Therby all items will be shown with their stats. Bad stats will be refreshed. (recommended)';
$lang['updi_help_refresh_all']  							= 'Deletes the current Itemcache and tries to refresh all items that are listed in EQDKP. WARNING: If you share your Itemcache with a forum, the items from the forum cannot be refreshed. Depending on your webservers speed and the availability of Allakhazam.com this action could take several minutes. Possibly your webserver settings forbid a successful execution. In this case please contact your administrator.';
$lang['updi_help_refresh_bad']    						= 'Deletes all bad items from the cache and refreshes them.';
$lang['updi_help_refresh_raidbank']    				= 'If Raidbanker is installed, Itemstats uses the entered items of the banker.';
$lang['updi_help_refresh_Tradeskill']    			= 'When Tradeskill is installed, the entered items will be updated by Itemstats.';

$lang['updi_active'] 					   							= 'activated';
$lang['updi_inactive']    										= 'deactivated';

$lang['fontcolor']    			  = 'Fontcolor';
$lang['Warrior']    					= 'Warrior';
$lang['Rogue']    						= 'Rogue';
$lang['Hunter']    						= 'Hunter';
$lang['Paladin']    					= 'Paladin';
$lang['Priest']    						= 'Priest';
$lang['Druid']    						= 'Druid';
$lang['Shaman']    						= 'Shaman';
$lang['Warlock']    					= 'Warlock';
$lang['Mage']    							= 'Mage';

# Reset DB Feature
$lang['reset_header']    			= 'Reset EQDKP Data';
$lang['reset_infotext']  			= 'DANGER!!! Deleting the data can’t be reverted!!! Make a backup first. To confirm the action, type DELETE into the editbox.';
$lang['reset_type']    				= 'Data type';
$lang['reset_disc']    				= 'Description';
$lang['reset_sec']    				= 'Certificate';
$lang['reset_action']    			= 'Action';

$lang['reset_news']					  = 'News';
$lang['reset_news_disc']		  = 'Delete all news from the database.';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Delete all raids and items from the database and reset all DKP points to zero.';
$lang['reset_ALL']   					= 'ALL';
$lang['reset_ALL_DISC']				= 'Delete every raid, item an members. Complete data reset. (Do not delete the users).';

$lang['reset_confirm_text']	  = ' insert here =>';
$lang['reset_confirm']			  = 'DELETE';

// Armory Menu
$lang['lm_armorylink1']				= 'Armory';
$lang['lm_armorylink2']				= 'Talents';
$lang['lm_armorylink3']				= 'Guild';

$lang['updi_update_ready']			= 'The items were successfully updated. You can <a href="#" onclick="javascript:parent.closeWindow()" >close</a> this window.';
$lang['updi_update_alternative']= 'Alternative update method to avoid timeouts.';
$lang['zero_sum']				= ' on Zero SUM DKP';

//Hybrid
$lang['Hybrid']				= 'Hybrid';

$lang['Jump_to'] 				= 'watch the video on ';
$lang['News_vid_help'] 			= 'To embed videos just post the link to the video without [tags]. supported videosites: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire. ';

$lang['SubmitNews'] 		   = 'Submit News';
$lang['SubmitNews_help'] 	   = 'You have a good News? Submit the News and share with all Eqdkp Plus Users.';

$lang['MM_User_Confirm']	   = 'Select your Admin Account? If you take of you Admin Permission, this can only be restored in the Database';

$lang['beta_warning']	   	   = 'Warning this EQDKP-Plus Beta Version must not be used on a live system! This Version stop working if a stable version is available. Check <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> for updates!';

$lang['news_comment']        = 'Comment';
$lang['news_comments']       = 'Comments';
$lang['comments_no_comments']	   = 'No entries';
$lang['comments_comments_raid']	   = 'Comments';
$lang['comments_write_comment']	   = 'write a comment';
$lang['comments_send_comment']	   = 'save comment';
$lang['comments_save_wait']	   	   = 'Please wait, comment is saving...';

$lang['news_nocomments'] 	 		    = 'Disallow Comments';
$lang['news_readmore_button']  			  	= 'Extend News';
$lang['news_readmore_button_help']  			  	= 'To use the extended Newstext, click here:';
$lang['news_message'] 				  	= 'Newstext';
$lang['news_permissions']			  	= 'Permissions';

$lang['news_permissions_text']			= 'Dont show news for';
$lang['news_permissions_guest']			= 'Guest';
$lang['news_permissions_member']		= 'Guest and Members (only Admin can see)';
$lang['news_permissions_all']			= 'Free for all';
$lang['news_readmore'] 				  	= 'Read more...';

$lang['recruitment_open']				= 'Recruitment open';
$lang['recruitment_contact']			= 'contact';

$lang['sig_conf']						= 'click the image to get the BB Code';
$lang['sig_show']						= 'show WoW signatur for your forum';

//Shirtshop
$lang['service']					    = 'service';
$lang['shirt_ad1']					    = 'Go to the Shirt-shop. <br> get your own shirt now!';
$lang['shirt_ad2']					    = 'Choose your Char';
$lang['shirt_ad3']					    = 'welcome to your guild shop ';
$lang['shirt_ad4']					    = 'Choose one of the ready-made products or make your own shirt with our Creator.<br>
										   You can customize every shirt and change every lettering.<br>
										   In the tab "Motive" you will find all available motifs!';
$lang['error_iframe']					= "Your browser doesn't support Frames!";
$lang['new_window']						= 'Open shop in a new windows';
$lang['your_name']						= 'YOUR NAME';
$lang['your_guild']						= 'YOUR GUILD';
$lang['your_server']					= 'YOUR SERVER';

//Last Raids
$lang['last_raids']					    = 'Last Raids';

$lang['voice_error']				    = 'No connection to the server.';

$lang['login_bridge_notice']		    = 'Login - CMS-Bridge is active. Use your CSM/Board Data to login.';

$lang['ads_remove']		    			= 'support EQdkp-Plus';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'EQDKP-Plus is a hobby-project which was mainly developed and is kept updated by two private persons.
											At the beginning this wasn’t a problem but after three years of constant programming and updating,
											the costs for this are unfortunately growing too quick for us to handle. Just for the developer and the update-server we
											have to spend 600€ per year now and there are also another 1000€ of costs for an attorney, since there have been
											some legal problems not so long ago. For the future we have also planned many more server-based features which will
											result in another needed server. Costs for our new forum and the designer of our new homepage add to this.
											All these named costs plus our more and more invested working time cannot be paid by ourselves anymore.
											For this reason and not wanting the project to die you will now sparely see ad-banners in EQDKP-Plus.
											These banners are very limited for content, so you will not see any pornographic banners or gold/item-selling vendors.

											You do have options to turn these banners off:
										  <ol>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount you want.
										  		Please think about how much EQDKP-Plus is worth for you.
										  		After a donation (Amazon or Paypal) you will get an eMail with a serial-key for the
										  		respective version or major-version (e.g. 0.6 or 0.7).<br><br></li>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 50€.
										  		You will earn premium status and get a livetime-premium-account, making you eligible for
										  		free upgrades to new major-versions. </li><br><br>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 100€.
										  		You will earn gold status and get a livetime-premium-account,
										  		making you eligible for free upgrades to new major-versions + free personal
										  		support from the EQDKP-Plus developers.<br><br></li>
										  <li> All developers and translators who ever contributed to EQDKP-Plus also get a free serial-key.<br><br></li>
										  <li> Deeply committed beta-testers also get a free serial-key. <br><br></li>
										  </ol>
										 All money generated with ad-banners and donations is solely spent to pay the costs coming up with the EQDKP-Plus project.
										 EQDKP-Plus is still a non-profit project! You dont have a Paypal or Amazon Account or have trouble with you key? Write me an <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['talents'] = array(
'Paladin'   	=> array('Holy','Protection','Retribution'),
'Rogue'     	=> array('Assassination','Combat','Subtlety'),
'Warrior'   	=> array('Arms','Fury','Protection'),
'Hunter'    	=> array('Beast Mastery','Marksmanship','Survival'),
'Priest'    	=> array('Discipline','Holy','Shadow'),
'Warlock'  		=> array('Affliction','Demonology','Destruction'),
'Druid'     	=> array('Balance','Feral Combat','Restoration'),
'Mage'      	=> array('Arcane','Fire','Frost'),
'Shaman'    	=> array('Elemental','Enhancement','Restoration'),
'Death Knight'   => array('Blood','Frost','Unholy')
);

$lang['portalmanager'] = 'Manage Portal Modules';

$lang['air_img_resize_warning'] = 'Click this bar to view the full image. The original is %1$sx%2$s.';
$lang['air_img_resize_warning_new'] = 'Click this bar to view the full image.';

$lang['guild_shop'] = 'Shop';

// LibLoader Language String
$lang['libloader_notfound']     = 'The Library Loader Class is not available.
                                  Please check if the folder  "eqdkp/libraries/" is propperly uploaded!<br/>
                                  Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']       = "The Library is outdated. You have to upload Version %1\$s or higher.<br/>
                                  Download: <a href='%2\$s' target='blank'>Libraries Download</a><br/>
                                  Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";
$lang['libloader_tooold_plug']  = "The Library Module '%1\$s' is outdated. Version %2\$s or higher is required.
                                  This is included in the Libraries %4\$s or higher. Your current Libraries Version is %5\$s<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";

$lang['more_plugins']   = "For more Plugins visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "For more Modules visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "For more Styles visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Cancel';

// Update Available
$lang['upd_available_head']    = 'System Updated available';
$lang['upd_available_txt']     = 'The System is not up to date. There are updates available.';
$lang['upd_available_link']    = 'Click to show updates.';

$lang['menu_roster'] = 'Roster';

//Userinfos

$lang['adduser_first_name'] = 'First name';
$lang['adduser_last_name'] = 'Last name';
$lang['adduser_addinfos'] = 'Profile information';
$lang['adduser_country'] = 'Country';
$lang['adduser_town'] = 'Town';
$lang['adduser_state'] = 'State';
$lang['adduser_ZIP_code'] = 'ZIP Code';
$lang['adduser_phone'] = 'Phone number';
$lang['adduser_cellphone'] = 'Cell phone number';
$lang['adduser_foneinfo'] = 'Phone numbers will be saved anonymously and only admins are able to see them. With the cell phone number given you can send each other text messages anonymously, e.g. in case of new raid events or cancelled raids.';
$lang['adduser_address'] = 'Street';
$lang['adduser_allvatar_nick'] = 'allvatar nick';
$lang['adduser_icq'] = 'ICQ number';
$lang['adduser_skype'] = 'Skype';
$lang['adduser_msn'] = 'MSN';
$lang['adduser_irq'] = 'IRC Server&Channel';
$lang['adduser_gender'] = 'Gender';
$lang['adduser_birthday'] = 'Birthday';
$lang['adduser_gender_m'] = 'Male';
$lang['adduser_gender_f'] = 'Female';
$lang['fv_required'] = 'Required field!';
$lang['lib_cache_notwriteable'] = 'The folder "eqdkp/data" is not writable. Please chmod 777!';
$lang['pcache_safemode_error']  = 'Safe Mode active. EQDKP-PLUS will not work wile it xould not write data to the cache folders in safe mode.';

// Ajax Image Uploader
$lang['aiupload_wrong_format']  = "The picture dimensions are out of bounds (max values: %1\$spx x %2\$spx).<br/>Please resize the image.";
$lang['aiupload_wrong_type']    = 'Invalid file type! Only image files (*.jpg, *.gif, *.png) are permitted.';
$lang['aiupload_upload_again']  = 'Reupload';

//Sticky news
$lang['sticky_news_prefix'] = 'Sticky:';
$lang['news_sticky'] = 'Make it sticky?';

$lang['menu_eqdkp'] = 'Menu';
$lang['menu_user'] = 'User-menu';

//Usersettings
$lang['user_list'] = 'Userlist';
$lang['user_priv'] = 'Privacy settings';
$lang['user_priv_set_global'] = 'Who should be allowed to see profile data like name, Skype-Account, ICQ… ?';
$lang['user_priv_set'] = 'Visible for ';
$lang['user_priv_all'] = 'all';
$lang['user_priv_user'] = 'Registered users';
$lang['user_priv_admin'] = 'Admin only';
$lang['user_priv_rl'] = 'Raidplaner admins';
$lang['user_priv_no'] = 'Nobody';
$lang['user_priv_tel_all'] = 'Should phone numbers be visible to all registered users instead of being visible only for admins? ';
$lang['user_priv_tel_cript'] = 'Should phone numbers be completely invisible, even for admins? (SMS/Text message sending still possible) ';
$lang['user_priv_tel_sms'] = 'Disable receiving SMS/Text messages from admins. (Receiving of raid-invitations via SMS/Text message not possible)';

// Image & BBCode Handling
$lang['images_not_available']	= 'The embedded image is not available at the moment';
$lang['images_not_available_admin']	= '<b>The embedded image could not be checked</b><br/>There are several reasons why this could happen:<br/>- Dynamic generated images are disabled for security reasons<br/>- blocked external connections: Try paths instead of URL<br/>- PHP safe mode on: must be disabled!';
$lang['images_userposted']		= 'User Posted Image';


//SMS
$lang['sms_perm']	= 'SMS Service';
$lang['sms_perm2']	= 'send SMS';
$lang['sms_header'] = 'Send text message/SMS';
$lang['sms_info'] = 'Send text message/SMS to users, e.g. when a raid was cancelled or you need extra players on short notice.';
$lang['sms_info_account'] = "You don't have a text message/SMS account, yet? Then get your text message/contingent now.";
$lang['sms_info_account_link'] = '<a href=http://www.eqdkp-plus.com target=_blank> --> Link</a>';
$lang['sms_send_info'] = 'In order to be able to send text messages/SMS, at least one user with a valid cell phone number has to be selected and a text has to be entered.';
$lang['sms_success'] = 'Text message successfully forwarded to SMS-Server. It may take a few minutes till messages will be sent.';
$lang['sms_error'] = 'An error occurred while sending text message.';
$lang['sms_error_badpw'] = 'An error occurred while sending text message. Username oder password incorrect.';
$lang['sms_error_bad'] = 'An error occurred while sending text message. No more text message credit on your account.';
$lang['sms_error_fopen'] = "An error occurred while sending text message. Server couldn't establish a fopen connection to the sms-relay. Either the sms-server is not available at this moment or your server doesn't accept fopen-connections. In such a case please contact your hoster/administrator. (don't contact the EQdkpPlus Team/Forum!!)";
$lang['sms_error_159'] = 'An error occurred while sending text message. Service-ID unknown.';
$lang['sms_error_160'] = 'An error occurred while sending text message. Message not found!';
$lang['sms_error_200'] = 'An error occurred while sending text message. Fatal eception error. / XML Script incomplete';
$lang['sms_error_254'] = 'An error occurred while sending text message. Message was deleted!';

// Libraries
$lang = array_merge($lang, array(
  'cl_shortlangtag'           => 'en',

  // Update Check
  'cl_ucpdate_box'            => 'New Version available',
  'cl_changelog_url'          => 'Changelog',
  'cl_timeformat'             => 'm/d/Y',
  'cl_noserver'               => 'An error occurred while trying to contact the update server, either your host does not allow outbound connections
                                  or the error was caused by a network problem.
                                  Please visit the eqdkp-plugin-forum to make sure you are running the latest plugin version.',
  'cl_update_available'       => "Please update the installed <i>%1\$s</i> Plugin.
                                  Your current version is <b>%2\$s</b> and the latest version is <b>%3\$s (Released at: %4\$s)</b>.<br/><br/>
                                  [release: %5\$s]%6\$s%7\$s",
  'cl_update_url'             => 'To the Download Page',

  // Plugin Updater
  'cl_update_box'             => 'Database update required',
  'cl_upd_wversion'           => "The actual Database ( Version %1\$s ) does not fit to the installed Plugin Version %2\$s.
                                  Please use the update button to perform the required updates automatically.",
  'cl_upd_woversion'          => 'A previous installation was found. The version Data is missing.
                                  Please choose the previous installed version in the drop Down list, to perform all Database changes.',
  'cl_upd_bttn'               => 'Update Database',
  'cl_upd_no_file'            => 'Update file is missing',
  'cl_upd_glob_error'         => 'An error occured during the update process.',
  'cl_upd_ok'                 => 'The update of the Database was successful',
  'cl_upd_step'               => 'Step',
  'cl_upd_step_ok'            => 'Successfull',
  'cl_upd_step_false'         => 'Failed',
  'cl_upd_reload_txt'         => 'Settings are reloading, please wait...',
  'cl_upd_pls_choose'         => 'Please choose...',
  'cl_upd_prev_version'       => 'Previous Version',

  // HTML Class
  'cl_on'                     => 'On',
  'cl_off'                    => 'Off',

  // ReCaptcha Library
	'lib_captcha_head'					=> 'confirmation Code',
	'lib_captcha_insertword'		=> 'Enter the words written below',
	'lib_captcha_insertnumbers' => 'Enter the spoken Numbers',
	'lib_captcha_send'					=> 'Send confirmation Code',

	'lib_starrating_cancel'			=> 'Cancel voting',

	// RSS Feeder
	'lib_rss_readmore'          => 'Read more',
	'lib_rss_loading'           => 'Feed is loading ...',
	'lib_rss_error'             => 'Error requesting page',
));

//date/time style
$lang['style_date_notime_long']		= 'F j, Y' ;
$lang['style_date_notime_short']	= 'm/d/y' ;
$lang['style_date_time']			= 'd.m.y h:ia T' ;
$lang['style_time']					= 'h:ia';
$lang['style_strtime_date']			= '%A %B %d %Y';
$lang['style_strtime_date_short']  	= '%a %m.%d %I:%M %p';

$lang['mini_games'] 		 	= 'Mini Games';
$lang['sf_text'] 		 		= '<b>Shakes & Fidget - The Game</b><br><br>
								   The funny role playing game to the legendary comic! Create your own hero and immerse into a dangerous world full of monster rabbits and hell brides. 
									<br><br>
									Master exciting adventures or mash other players in the arena. Gain experience and honor, win gold and found a guild with your friends! 
									<br><br>
									Squander your gold for new, wicked equipment - from a real sword to a fake mustache! ';
$lang['popmog_text'] 		 		= '<b>POPMOG Browsergames</b><br><br>On POPMOG you are playing great browsergames. Whether you are playing as a privateer, space invader or a dragon breeder - on POPMOG you will always play in a team!<br><br>
										<ul><li>permanently new games</li>
										<li>play with other cool gamer</li>
										<li>collect valuable honor points</li>
										<li>move up in the highscores!</li></ul>
										';
$lang['pm_games'] 		 	= 'Games';
$lang['pm_player'] 		 	= 'Top player';
$lang['pm_activity'] 	 	= 'Activities';
$lang['sf_image'] 	 		= 'sf_en.png';
$lang['pm_image'] 	 		= 'pm_en.png';


?>