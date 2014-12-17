<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus Language File
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 23:17
//File: maintenance/includes/tasks/import06/language/english.php
//Source-Language: german

$lang = array( 
	"import06" => 'Import Data from 0.6',
	"import_steps" => 'Import Steps',
	"config_news_log" => 'Config, News, Logs',
	"users_auths" => 'Users, Permissions',
	"events" => 'Events',
	"multidkp" => 'Multidkp',
	"members" => 'Members',
	"raids" => 'Raids',
	"items" => 'Items',
	"adjustments" => 'Adjustments',
	"dkp_check" => 'Check DKP',
	"infopages" => 'Infopages',
	"plugins_portal" => 'Plugins, Portalmodules',
	"item_cache" => 'item_cache',
	"submit" => 'Submit',
	"select_all" => '(Select all)',
	"negate_selection" => 'Negate selection of checkboxes',
	"no_problems" => 'No problems detected. Just click the button below to proceed to next step.',
	"date_format" => 'm/d/Y',
	"no_import" => 'Don\'t import this',
	"dont_import" => 'Don\'t import anything',
	"nothing_imported" => 'Import canceled',
	"page" => 'Page',
	"database_info" => 'Information about the database from which shall be imported',
	"database_other" => 'Old data in different database?',
	"only_other" => ' (only if other database)',
	"host" => 'Host of Database (only if other database)',
	"db_name" => 'Name of the Database (only if other database)',
	"user" => 'User of Database (only if other database)',
	"password" => 'Password for database (only if other database)',
	"table_prefix" => 'Table-Prefix of other Installation',
	"import" => 'Import',
	"older_than" => 'only older than',
	"config" => 'Config-Values',
	"news" => 'News',
	"log" => 'Logs',
	"styles" => 'Styles',
	"enter_date_format" => 'enter date in format \'DD.MM.YYYY\'',
	"which_users" => 'Which Users shall be imported?',
	"your_user" => 'Select your own user (it won\'t be imported and the id of that user will be replaced with the id of your current user):',
	"no_user" => 'No user',
	"admin" => 'Admin',
	"notice_admin_perm" => 'Only user with the permissions to manage users or to edit the config gain full admin-rights. All other admin-permissions are not imported. Please check those after completing the import.',
	"which_events" => 'Which Events shall be imported?',
	"no_multi_found" => 'No Multidkp-Pool found. Please enter a name and description to create a new pool. All events will be assigned to this pool. You can change it later if you want to.',
	"multi_name" => 'Name of Multidkp-Pool',
	"multi_desc" => 'Description of Multidkp-Pool',
	"which_multis" => 'Which Multidkp-Pools shall be imported?',
	"which_members" => 'Which Members shall be imported?',
	"import_ranks" => 'Import ranks',
	"create_special_members" => 'Create some special members, such as \'bank\' oder \'disenchanted\'. Leave blank to not create.',
	"change_checked_to" => 'Change checked to',
	"raids_with_no_event" => 'Some raids did not have an event assigned to them. Please select an event to proceed.',
	"event_name" => 'Event-Name in Old-DB',
	"raid_id" => 'Raid-ID',
	"items_without_raid" => 'Some Items were assigned to a non-existent Raid. Please assign them to a new one.',
	"items_without_member" => 'Some Items were assigned to a non-existend Member. Please select a new Member.',
	"item_buyer" => 'Buyer of the item',
	"change_item_to" => 'Change buyer of this items to',
	"item_buyer_2" => 'Buyers with two or less items',
	"adjs_without_event" => 'Some adjustments were assigned to a non-existent Event. Please select an event.',
	"adjs_without_member" => 'Some adjustments were assigned to a non-existend Member. Please select a new Member.',
	"member_with_diff" => 'Some members have a different amount of dkp, because 0.7 nolonger supports non-multidkp systems. Please choose what you want to do with the difference.',
	"mem_diff_create_adj" => 'Create an adjustment, so the dkp will be the same as in 0.6',
	"mem_diff_ignore" => 'Ignore this difference',
	"mem_diff_adj_reason" => 'Difference because of Multidkp',
	"which_infopages" => 'Which infopages should be imported? Please check the visibility after finishing the import.',
	"which_plugins" => 'Which of your old plugins shall be imported? You must install them to import the data.',
	"which_portals" => 'Which of your old portal-moduls shall be imported?',
	"install" => 'Install',
	"installed" => 'Installation complete',
	"uninstall" => 'Uninstall',
	"import_end" => 'Import completed',
	
);

?>