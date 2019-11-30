<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_23220 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.22.0'; //new plus-version
	public $ext_version		= '2.3.22'; //new plus-version
	public $name			= '2.3.22 Update';
	
	public function __construct(){
		parent::__construct();
		
		$this->langs = array(
				'english' => array(
						'update_23220'	=> 'EQdkp Plus 2.3.22 Update',
						1 => 'Alter Table',
						2 => 'Alter Table',
						3 => 'Alter Table',
						4 => 'Alter Table',
						5 => 'Alter Table',
						6 => 'Alter Table',
						7 => 'Alter Table',
						8 => 'Alter Table',
						9 => 'Alter Table',
						10 => 'Alter Table',
						11 => 'Alter Table',
						12 => 'Alter Table',
						13 => 'Alter Table',
						14 => 'Alter Table',
						15 => 'Alter Table',
				),
				'german' => array(
						'update_23220'	=> 'EQdkp Plus 2.3.22 Update',
						1 => 'Verändere Tabelle',
						2 => 'Verändere Tabelle',
						3 => 'Verändere Tabelle',
						4 => 'Verändere Tabelle',
						5 => 'Verändere Tabelle',
						6 => 'Verändere Tabelle',
						7 => 'Verändere Tabelle',
						8 => 'Verändere Tabelle',
						9 => 'Verändere Tabelle',
						10 => 'Verändere Tabelle',
						11 => 'Verändere Tabelle',
						12 => 'Verändere Tabelle',
						13 => 'Verändere Tabelle',
						14 => 'Verändere Tabelle',
						15 => 'Verändere Tabelle',
				),
		);
		
		$this->sqls = array(
				1	=> "ALTER TABLE `__auth_users` CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				2	=> "ALTER TABLE `__groups_user` CHANGE COLUMN `groups_user_sortid` `groups_user_sortid` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				3	=> "ALTER TABLE `__member_user`
	CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	CHANGE COLUMN `user_id` `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				4	=> "ALTER TABLE `__adjustments` CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				5	=> "ALTER TABLE `__events` CHANGE COLUMN `event_id` `event_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				6	=> "ALTER TABLE `__items` CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				7	=> "ALTER TABLE `__members`
	CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	CHANGE COLUMN `member_main_id` `member_main_id` INT(11) UNSIGNED NULL DEFAULT NULL;",
				8	=> "ALTER TABLE `__members`
	CHANGE COLUMN `member_rank_id` `member_rank_id` SMALLINT(5) NOT NULL DEFAULT '0';",
				9	=> "ALTER TABLE `__raids`
	CHANGE COLUMN `event_id` `event_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				10	=> "ALTER TABLE `__raid_attendees`
	CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				11	=> "ALTER TABLE `__groups_raid`
	CHANGE COLUMN `groups_raid_sortid` `groups_raid_sortid` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				12	=> "ALTER TABLE `__multidkp2event`
	CHANGE COLUMN `multidkp2event_event_id` `multidkp2event_event_id` INT(11) NOT NULL DEFAULT 0;",
				13	=> "ALTER TABLE `__logs`
	CHANGE COLUMN `user_id` `user_id` INT(11) NOT NULL DEFAULT 0;",
				14	=> "ALTER TABLE `__calendar_events`
	CHANGE COLUMN `creator` `creator` INT(11) UNSIGNED NOT NULL DEFAULT 0;",
				15	=> "ALTER TABLE `__events`
	ADD COLUMN `event_show_profile` TINYINT(1) UNSIGNED NULL DEFAULT '1';",
		);
	}
	
	
}

?>