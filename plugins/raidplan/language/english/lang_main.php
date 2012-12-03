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

$lang['raidplan'] = "Raid Planner";
$lang['rp_raidplaner'] = "Raid Planner";

// User Menu
$lang['rp_usermenu_raidplaner']				= "Raid Planner";
$lang['rp_raid_id']                   = "Raid ID";

// Submit Buttons
$lang['rp_wildcard_raid']					    = "Wildcard Raid";

// Delete Confirmation Texts
$lang['rp_confirm_delete_subscription'] 	= "Are you sure you want to sign off this raid?";

// Page Foot Counts
$lang['rp_listraids_footcount']				= "... found %1\$d raid(s) / %2\$d per page / %3\$sshow all</a>";
$lang['rp_listrecentraids_footcount']       = "... found %1\$d Raid(s) / last %2\$d Days";

// Buttons
$lang['rp_signup']							      = "Sign up";
$lang['rp_bunsign']                   = "Unsign";
$lang['rp_signoff']							      = "Sign off";
$lang['rp_distribute_class_set']			= "Set class distribution";
$lang['rp_class_distribution_notset'] = "To create a Classlist please take a new Raid, not an old one.";
$lang['rp_add_all']							      = "Add all members";

// Misc
$lang['rp_confirmed']						      = "Confirmed";
$lang['rp_signed']							      = "Signed";
$lang['rp_unsigned']							    = "Unsigned";
$lang['rp_notavail']						      = "Not Available";
$lang['rp_needed']							      = "Needed";
$lang['rp_start_time']				 		 		= "Start - Time";
$lang['rp_invite_time']				 		 		= "Invite - Time";
$lang['rp_signup_deadline']					  = "Sign-Up deadline";
$lang['rp_signup_deadline_date']			= "Sign-Up deadline date";
$lang['rp_signup_deadline_time']			= "Sign-Up deadline time";
$lang['rp_current_raid']              = "Current Raid";
$lang['rp_recent_raid']               = "Recent Raid";

$lang['rp_signup_over']            				= "Signin is closed";
$lang['rp_signup_possible']            			= "Signin possible";
$lang['rp_signup_24h']            				= "Signin close in next 24h";

// viewmember
$lang['rp_rank']                      = "Rank";
$lang['rp_class']                     = "Class";
$lang['rp_chars_of']                  = "Chars of Player:";
$lang['rp_char']                   		= "Charakter";

//overlib windows
$lang['rp_status_header']             = "Raid Status";
$lang['rp_status_signintime']         = "Time to sign in:";
$lang['rp_status_closed']             = "Signin is closed";
$lang['rp_status_day']                = "d";
$lang['rp_status_hours']              = "h";
$lang['rp_status_minutes']            = "m";
$lang['rp_note_header']               = "Note";
$lang['rp_time_header']               = "Signup Time";
$lang['rp_status']           		  		= "Status";

//time translations
$lang['rp_time_format']               = "%A, %d-%m-%Y, %H:%M";
$lang['rp_day_format']               	= "%A";
$lang['rp_time_short']               	= "%H:%M";
$lang['rp_local_format']              = "en_US";
$lang['rp_calendar_lang']             = "en";

$lang['rp_start']											= "Go";
$lang['rp_day']												= "Day";
$lang['rp_invite']										= "Invite";

// Image alternates
$lang['rp_rolled']							      = "Rolled";
$lang['rp_wildcard']						      = "Wildcard";

// Submission Success Messages
$lang['rp_raid_signed']						    = "Member %1\$s successfully signed up raid %2\$s.";
$lang['rp_admin_update_confimation_status']	= "Member %1\$s successfully unlocked.";
$lang['rp_admin_unlock_member']           	= "Confirmation for member %1\$s successfully updated.";
$lang['rp_raid_signup_deleted']           	= "Sign-Up for member %1\$s deleted.";
$lang['rp_class_distribution_set']			= "Class distribution set successfully set.";

// Submission Error Messages
$lang['rp_member_allready_subscribed']		= "Member already subscribed. Update aborted.";

// AutoInvite
$lang['rp_Macro_output_Listing']             = "Macro Output Listing...";
$lang['rp_nonqued_user']                     = "Non-queued users first";
$lang['rp_queued_users']                     = "Queued users";
$lang['rp_MacroListingComplete']             = "Macro output listing complete.";
$lang['rp_copypaste_ig']                     = "Copy and paste the above to a macro and run in-game";
$lang['rp_lua_created']                      = "LUA file created";
$lang['rp_download']                         = "Download";
$lang['rp_dl_autoinv_add']                   = "(right-click, choose save as, name it AutoInvite.lua)";
$lang['rp_lua_output']                       = "Beginning LUA output";
$lang['rp_no_raidid']                        = "Error: No RaidID";
$lang['rp_autonv_link']                      = "Autoinvite: LUA / Macro Generator";

// Error Messages
$lang['rp_error_invalid_mode_provided']		= "A valid mode was not provided.";
$lang['rp_not_logged_in']					= "You must be logged in to join a raid!";
$lang['rp_no_user_assigned']				= "The Admin didn't set you a char!";
$lang['rp_class_distribution_not_set']		= "Class distribution is not set correctly!";

// config things
$lang['config']           = "settings";
$lang['rp_header_global'] = "General Raidplan config";
$lang['rp_show_ranks']    = "Show ranks in raid planner";
$lang['rp_short_rank']    = "Show only short ranks";
$lang['rp_send_email']    = "Email new raids to all users";
$lang['rp_roll_system']   = "Use the roll-system?";
$lang['rp_wildcard_sys']  = "Use the wildcard-system?";
$lang['rp_use_css']       = "Add the .css file in the plugin's template folder";
$lang['rp_last_x_days']   = "show recent raids: last x days";
$lang['rp_aj_secret_hash']= "Autojoin: Secret Hash";
$lang['rp_aj_path']       = "Autojoin: Path";


?>
