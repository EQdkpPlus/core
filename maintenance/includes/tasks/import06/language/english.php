<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2009 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}
$lang['import06'] = 'Import Data from 0.6';
$lang['import_steps'] = 'Import Steps';
$lang['config_news_log'] = 'Config, News, Logs';
$lang['users_auths'] = 'Users, Permissions';
$lang['events'] = 'Events';
$lang['multidkp'] = 'Multidkp';
$lang['members'] = 'Members';
$lang['raids'] = 'Raids';
$lang['items'] = 'Items';
$lang['adjustments'] = 'Adjustments';
$lang['dkp_check'] = 'Check DKP';
$lang['infopages'] = 'Infopages';
$lang['plugins_portal'] = 'Plugins, Portalmodules';
$lang['item_cache'] = 'item_cache';

$lang['submit'] = 'Submit';
$lang['select_all'] = '(Select all)';
$lang['negate_selection'] = 'Negate selection of checkboxes';
$lang['no_problems'] = 'No problems detected. Just click the button below to proceed to next step.';
$lang['date_format'] = 'm/d/Y';
$lang['no_import'] = 'Don\'t import this';
$lang['dont_import'] = 'Don\'t import anything';
$lang['nothing_imported'] = 'Import canceled';

//first step
$lang['database_info'] = 'Information about the database from which shall be imported';
$lang['database_other'] = 'Old data in different database?';
$lang['only_other'] = ' (only if other database)';
$lang['host'] = 'Host of Database'.$lang['only_other'];
$lang['db_name'] = 'Name of the Database'.$lang['only_other'];
$lang['user'] = 'User of Database'.$lang['only_other'];
$lang['password'] = 'Password for database'.$lang['only_other'];
$lang['table_prefix'] = 'Table-Prefix of other Installation';

$lang['import'] = 'Import';
$lang['older_than'] = 'only older than';
$lang['config'] = 'Config-Values';
$lang['news'] = 'News';
$lang['log'] = 'Logs';
$lang['enter_date_format'] = "enter date in format 'DD.MM.YYYY'";  //do not change order of date

$lang['which_users'] = 'Which Users shall be imported?';
$lang['your_user'] = 'Select your own user (it won\'t be imported and the id of that user will be replaced with the id of your current user):';
$lang['no_user'] = 'No user';
$lang['admin'] = 'Admin';
$lang['notice_admin_perm'] = 'Only user with the permissions to manage users or to edit the config gain full admin-rights. All other admin-permissions are not imported. Please check those after completing the import.';

$lang['which_events'] = 'Which Events shall be imported?';

$lang['no_multi_found'] = 'No Multidkp-Pool found. Please enter a name and description to create a new pool. All events will be assigned to this pool. You can change it later if you want to.';
$lang['multi_name'] = 'Name of Multidkp-Pool';
$lang['multi_desc'] = 'Description of Multidkp-Pool';
$lang['which_multis'] = 'Which Multidkp-Pools shall be imported?';

$lang['which_members'] = 'Which Members shall be imported?';
$lang['import_ranks'] = 'Import ranks';
$lang['create_special_members'] = 'Create some special members, such as \'bank\' oder \'disenchanted\'. Leave blank to not create.';

$lang['change_checked_to'] = 'Change checked to';

$lang['raids_with_no_event'] = 'Some raids did not have an event assigned to them. Please select an event to proceed.';
$lang['event_name'] = 'Event-Name in Old-DB';
$lang['raid_id'] = 'Raid-ID';

$lang['items_without_raid'] = 'Some Items were assigned to a non-existent Raid. Please assign them to a new one.';
$lang['items_without_member'] = 'Some Items were assigned to a non-existend Member. Please select a new Member.';
$lang['item_buyer'] = 'Buyer of the item';
$lang['change_item_to'] = 'Change buyer of this items to';
$lang['item_buyer_2'] = 'Buyers with two or less items';

$lang['adjs_without_event'] = 'Some adjustments were assigned to a non-existent Event. Please select an event.';
$lang['adjs_without_member'] = 'Some adjustments were assigned to a non-existend Member. Please select a new Member.';

$lang['member_with_diff'] = 'Some members have a different amount of dkp, because 0.7 nolonger supports non-multidkp systems. Please choose what you want to do with the difference.';
$lang['mem_diff_create_adj'] = 'Create an adjustment, so the dkp will be the same as in 0.6';
$lang['mem_diff_ignore'] = 'Ignore this difference';
$lang['mem_diff_adj_reason'] = 'Difference because of Multidkp';

$lang['which_infopages'] = 'Welche Info-Seiten sollen importiert werden? Please check the visibility after finishing the import.';

$lang['which_plugins'] = 'Which of your old plugins shall be imported? You must install them to import the data.';
$lang['which_portals'] = 'Which of your old portal-moduls shall be imported?';
$lang['install'] = 'Install';
$lang['installed'] = 'Installation complete';
$lang['uninstall'] = 'Uninstall';

$lang['import_end'] = 'Import completed';

?>