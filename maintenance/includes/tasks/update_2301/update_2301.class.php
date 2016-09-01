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

class update_2301 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.3.0.1'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= '2.3.0 Update 1';

	public function __construct(){
		parent::__construct();

// combined update 2.3.0.1 to 2.3.0.7
		$this->langs = array(
			'english' => array(
				'update_2300'	=> 'EQdkp Plus 2.3.0 Update 1',
					1			=> 'Add icons field to roles table',
					2			=> 'Change article table',
					3			=> 'Change calendar_raid_attendees table',
					4			=> 'Change log table',
					5			=> 'Delete notifcation Type',
					6			=> 'Add Notification Type',
					7			=> 'Alter Style Table',
					8			=> 'Alter Style Table',
					9			=> 'Alter Style Table',
					10			=> 'Change user table',
					11			=> 'Change user table',
					12			=> 'Change user table',
					13			=> 'Add roles field to raid guests table',
				),
			'german' => array(
				'update_2300'	=> 'EQdkp Plus 2.3.0 Update 1',
					1			=> 'Füge Icons-Feld zur Rollentabelle hinzu',
					2			=> 'Ändere Article-Tabelle',
					3			=> 'Ändere calendar_raid_attendees-Tabelle',
					4			=> 'Ändere Logs-Tabelle',
					5			=> 'Entferne Benachrichtigungstyp',
					6			=> 'Füge Benachrichtigungstyp hinzu',
					7			=> 'Erweitere Style Tabelle',
					8			=> 'Erweitere Style Tabelle',
					9			=> 'Erweitere Style Tabelle',
					10			=> 'Ändere Benutzer-Tabelle',
					11			=> 'Ändere Benutzer-Tabelle',
					12			=> 'Ändere Benutzer-Tabelle',
					13			=> 'Füge Rollenfeld zur Raid-Gästetabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__roles` ADD COLUMN `role_icon` varchar(255) COLLATE utf8_bin DEFAULT NULL;",
			2	=> "ALTER TABLE `__articles` CHANGE COLUMN `last_edited_user` `last_edited_user` INT(11) NOT NULL DEFAULT '0';",
			3	=> "ALTER TABLE `__calendar_raid_attendees` CHANGE COLUMN `status_changedby` `status_changedby` INT(11) NOT NULL DEFAULT '0';",
			4	=> "ALTER TABLE `__logs` CHANGE COLUMN `log_value` `log_value` MEDIUMTEXT NOT NULL COLLATE 'utf8_bin';",
			5	=> "DELETE FROM `__notification_types` WHERE `id` = 'eqdkp_user_new_registered';",
			6	=> "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_user_new_registered', 'notification_user_new_registered', 'user', 0, '0', 0, NULL, 0, 'fa-user-plus');",
			7	=> "ALTER TABLE `__styles` ADD COLUMN `additional_fields` TEXT NULL COLLATE 'utf8_bin';",
			8   => "ALTER TABLE `__styles` ADD `favicon_img` VARCHAR(255) NULL DEFAULT NULL AFTER `logo_position`;",
			9   => "ALTER TABLE `__styles` ADD `banner_img` VARCHAR(255) NULL DEFAULT NULL AFTER `favicon_img`;",
			10	=> "ALTER TABLE `__users` CHANGE COLUMN `user_key` `user_email_confirmkey` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin';",
			11	=> "ALTER TABLE `__users` ADD COLUMN `user_email_confirmed` INT(2) NOT NULL DEFAULT '1';",
			12	=> "ALTER TABLE `__users` ADD COLUMN `user_temp_email` VARCHAR(255) NULL COLLATE 'utf8_bin'",
			13	=> "ALTER TABLE `__calendar_raid_guests` ADD `role` int(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`;",
		);
	}
}


?>
