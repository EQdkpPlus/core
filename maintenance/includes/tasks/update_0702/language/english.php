<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'update_0702' => 'eqDKP Plus 0.7.0.2 update package',
  'task00' => 'Alter Plus-Links Table',
  'task01' => 'Insert a_maintenance',
  'task02' => 'Insert a_reset',
  'task03' => 'Insert a_logs_del',
  'task04' => 'Insert u_userlist',
  'task05' => 'Delete a_turnin_add',
  'task06' => 'Create Table __auth_groups',
  'task07' => 'Create Table __groups_user',
  'task08' => 'Insert default groups',
  'task09' => 'Drop Table __groups_users',
  'task10' => 'Create Table __groups_users',
  'task11' => 'Set auto-Increment for __auth_options',
  'task12' => 'Put User 1 to the superadmin-group',
  'task13' => 'Create Table for the news-categories',
  'task14' => 'Insert default-category',
  'task15' => 'Alter News-Table',
	'task16' => 'Insert Setting for Captcha',
);
?>