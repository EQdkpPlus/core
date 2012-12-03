<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2010-07-25 16:35:42 +0200 (So, 25 Jul 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: Godmod $
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev: 8466 $
 *
 * $Id: english.php 8466 2010-07-25 14:35:42Z Godmod $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  //Global
  'click2show' => '(click to show)',
	'maintenance_mode' => 'Maintenance Mode',
	'task_manager' => 'Task-Manager',
	'admin_acp' => 'Admin Control Panel',
	'activate_info'	=> '<h1>Activate Maintenance Mode</h1><br />With this maintenance tool, you can easily update your EQdkp and import Data from an older Version.<br />An Update or Import is only possible, if the maintenance mode is enabled and denies the Login of users to prevent Errors and Problems.<br /><br />Reason, shown to the users (not required):<br />',
	'activate_mmode'	=> 'Activate Maintenance Mode',
	'deactivate_mmode'	=> 'Disable Maintenance Mode',
	'leave_mmode'	=> 'Leave Maintenance Mode',
	'no_leave' => 'Deactivating of Maintenance Mode not possible, while there are necessary tasks.',
	'no_leave_accept' => 'Back to Task-List',

  //Maintenance page
  'maintenance_message' => 'Your EQdkp plus System is currently in maintenance mode, only admin users are allowed to login!',
	'reason'	=> '<br /><b>Reason:</b> ',
	'admin_login'		=> 'Admin-Login',
  'username' => 'User',
  'password' => 'Password',
  'login' => 'Login',
  'remember_password' => 'Remember password?',
  'invalid_login_warning' => 'Invalid login! Please verify your Username and Password. Only admin users are allowed to login!',

  //Task manager
  'is_necessary' => 'Necessary?',
  'is_applicable' => 'Applicable?',
  'name' => 'Name',
  'version' => 'Version',
  'author' => 'Author',
  'link' => 'Link',
  'description' => 'Description',
  'type' => 'Task type',
  'yes' => 'Yes',
  'no' => 'No',
  'click_me' => 'Link to task',
  'mmode_info' => 'Welcome to the Maintenance-Tool of your EQdkp-Plus. Here you can update your Eqdkp easily, or import a former version of EQdkp Plus.<br />To prevent problems, you EQdkp Plus is disabled until to disable the maintenance mode.',
  'mmode_pcache_error' => 'Some errors occured. You have to solve these errors in order to deactivate the Maintenance-Mode',
  'necessary_tasks' => 'Necessary Tasks',
  'applicable_tasks' => 'Applicable Tasks',
  'not_applicable_tasks' => 'not applicable tasks',
  'no_nec_tasks' => 'There are no Updates necessary.',
  'nec_tasks' => 'The following update-tasks are necessary. Please execute them, to update your system.',
	'nec_tasks_available' => 'Please execute the necessary update-tasks to update your system.',
	'applicable_warning' => 'This task is not necessary! Executing this task can cause data-loss. Please execute this task only, when you\'re shure!',
	'executed_tasks'	=> 'Following actions have been executed for action "%s"',
	'stepend_info'		=> 'The task is finished, but your system is still in maintenance mode so you can test eveything. User can login again when you diable the maintenance mode',

  //Task types
  'fix' => 'Fix',
  'update' => 'Update',
  'import' => 'Import',
  'plugin_update' => 'Plugin-Update',

  //Task page
  'unknown_task_warning' => 'Unknown task!',
  'application_warning' => 'Could not apply task, application check returned false!',
  'dependency_warning' => 'This task has dependencies, please apply them first!',
  'start_here' => 'Start here!',

  //Sql-Updates
  'following_updates_necessary' => 'The following SQL-Updates are necessary: ',
  'start_update' => 'Process all necessary Updates!',
  'only_this_update' => 'Only process this update: ',
	
		//Splash
	'splash'	=> '<p><strong>Welcome to the Maintenance-Tool of your EQdkp-Plus!</strong></p>
	<p>Here you can update your Eqdkp easily, or import a former version of EQdkp Plus.</p>
	<p><table><tr>
	<td><img src="../images/support_tour.png" border="0" /></td>
	<td>You are new to EQdkp Plus? You\'ve never given DKP away or import Raids?
	<br />
	<strong><a href="../admin/?tour=start">Start now a Tour through your EQdkp Plus!</a></strong></td></tr></table></p>
	<input type="button" value="Start Tour" class="mainoption" onClick="window.location=\'../admin/?tour=start\'"> <input type="button" value="Skip Tour" class="mainoption" onClick="window.location=\'../admin/settings.php\'"> <input type="button" value="Import" class="mainoption" onClick="window.location=\'?type=import\'">',
);
?>