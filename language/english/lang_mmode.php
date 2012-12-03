<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2002
 * -----------------------------------------------------------------------
 * @copyright   2006-2011 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * 
 */
 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English 0.7	
//Created by EQdkp Plus Translation Tool on  2011-09-03 18:07
//File: lang_mmode
//Source-Language: german07

$alang = array( 
"click2show" => "(click to show)",
"maintenance_mode" => "Maintenance Mode",
"task_manager" => "Task-Manager",
"admin_acp" => "Admin Control Panel",
"activate_info" => "<h1>Activate Maintenance Mode</h1><br />With this maintenance tool, you can easily update your EQdkp and import Data from an older Version.<br />An Update or Import is only possible, if the maintenance mode is enabled and denies the Login of users to prevent Errors and Problems.<br /><br />Reason, shown to the users (not required):<br />",
"activate_mmode" => "Activate Maintenance Mode",
"deactivate_mmode" => "Disable Maintenance Mode",
"leave_mmode" => "Leave Maintenance Mode",
"home" => "Home",
"no_leave" => "Deactivating of Maintenance Mode not possible, while there are necessary tasks.",
"no_leave_accept" => "Back to Task-List",
"maintenance_message" => "Your EQdkp plus System is currently in maintenance mode, only admin users are allowed to login!",
"reason" => "<br /><b>Reason:</b> ",
"admin_login" => "Admin-Login",
"login" => "Login",
"username" => "User",
"password" => "Password",
"remember_password" => "Remember password?",
"invalid_login_warning" => "Invalid login! Please verify your Username and Password. Only admin users are allowed to login!",
"is_necessary" => "Necessary?",
"is_applicable" => "Applicable?",
"name" => "Name",
"version" => "Version",
"author" => "Author",
"link" => "Link",
"description" => "Description",
"type" => "Task type",
"yes" => "Yes",
"no" => "No",
"click_me" => "Link to task",
"mmode_info" => "Welcome to the Maintenance-Tool of your EQdkp-Plus. Here you can update your Eqdkp easily, or import a former version of EQdkp Plus.<br />To prevent problems, you EQdkp Plus is disabled until to disable the maintenance mode.",
"mmode_pfh_error" => "Some errors occured. To deactivate the Maintenance Mode, you have to fix this errors.",
"necessary_tasks" => "Necessary Tasks",
"applicable_tasks" => "Applicable Tasks",
"not_applicable_tasks" => "not applicable tasks",
"no_nec_tasks" => "There are no Updates necessary.",
"nec_tasks" => "The following update-tasks are necessary. Please execute them, to update your system.",
"nec_tasks_available" => "Please execute the necessary update-tasks to update your system.",
"applicable_warning" => "This task is not necessary! Executing this task can cause data-loss. Please execute this task only, when you're shure!",
"executed_tasks" => "Following actions have been executed for action \"%s\"",
"stepend_info" => "The task is finished, but your system is still in maintenance mode so you can test eveything. User can login again when you diable the maintenance mode",
"fix" => "Fix",
"update" => "Update",
"import" => "Import",
"plugin_update" => "Plugin-Update",
"unknown_task_warning" => "Unknown task!",
"application_warning" => "Could not apply task, application check returned false!",
"dependency_warning" => "This task has dependencies, please apply them first!",
"start_here" => "Start here!",
"following_updates_necessary" => "The following SQL-Updates are necessary: ",
"start_update" => "Process all necessary Updates!",
"only_this_update" => "Only process this update: ",
"start_single_update" => "Process Update",
"splash_welcome" => "Welcome to the maintenance Area of your EQDKP-PLUS system!",
"splash_desc" => "You can update your EQdkp Plus or import older version of EQdkp Plus in this area.",
"splash_new" => "You are new to EQdkp Plus? You never added DKP or added raids?",
"start_tour" => "Start tour",
"jump_tour" => "Skip tour",
"06_import" => "Import old 0.6 data",
"guild_import" => "Import a guild from external Database",
"guild_import_info" => "when supported by your game",
 );
$lang = (is_array($lang))? $lang : array();
$lang = array_merge($lang, $alang);
?>