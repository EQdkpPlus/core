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

// update_22100 added, update_22130 ignored because it is included in 2.3 as well.
class update_2200 extends sql_update_task {
	public $author			= 'GodMod';
	public $version		= '2.2.0.0'; //new plus-version
	public $ext_version	= '2.2.0'; //new plus-version
	public $name			= '2.2.0 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2200'	=> 'EQdkp Plus 2.2.0 Update',
					1			=> 'Alter notification_types table',
					2			=> 'Alter member profilefields table',
					3			=> 'Alter notification_types table',
					4			=> 'Alter raid guests table and add creator column',
					5			=> 'Alter raid guests table and add colum for approval status',
					6			=> 'Alter raid guests table and add colum for email address',
					7			=> 'Set guests approved status to 1',
					8			=> 'Set guests creator to 1',
					9			=> 'Add new notifictaion for guests',
					10			=> 'Change approved field to status for calendar guests table',
					11			=> 'Change status number',
					12			=> 'Set empty raidgroup ids to 0 for guest table',
					13			=> 'Change raidgroup column to int for guest table',
					14			=> 'Change article table',
					15			=> 'Change calendar_raid_attendees table',
					16			=> 'Change log table',
					17			=> 'Change session table',
					18			=> 'Change session table',
					19			=> 'Change session table',
					20			=> 'Cleanup session table',
				),
			'german' => array(
				'update_2200'	=> 'EQdkp Plus 2.2.0 Update',
					1			=> 'Ändere die Benachrichtigungstypen-Tabelle',
					2			=> 'Ändere die Charakter Profilfeld-Tabelle',
					3			=> 'Ändere die Benachrichtigungstypen-Tabelle',
					4			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für Ersteller hinzu',
					5			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für Freigabestatus hinzu',
					6			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für E-Mails hinzu',
					7			=> 'Setze den Bestätigt-STatus für bisherige Gäste auf 1',
					8			=> 'Setze den Ersteller für bisherige Gäste auf 1',
					9			=> 'Füge neue Benachrichtigung für Gäste hinzu',
					10			=> 'Ändere den Namen des approved Feldes zu status',
					11			=> 'Setze die Status-Felder neu',
					12			=> 'Setze leere Raidgruppen auf 0 bei der Gästetabelle',
					13			=> 'Ändere die Raidrguppe in der Gästetabelle zu integer',
					14			=> 'Ändere Article-Tabelle',
					15			=> 'Ändere calendar_raid_attendees-Tabelle',
					16			=> 'Ändere Logs-Tabelle',
					17			=> 'Ändere Session-Tabelle',
					18			=> 'Ändere Session-Tabelle',
					19			=> 'Ändere Session-Tabelle',
					20			=> 'Räume Session-Tabelle auf',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__notification_types` CHANGE COLUMN `default` `default` VARCHAR(50) NOT NULL DEFAULT '0'",
			2	=> "ALTER TABLE `__member_profilefields` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`id`);",
			3	=> "ALTER TABLE `__notification_types` CHANGE COLUMN `default` `default` VARCHAR(50) NOT NULL DEFAULT '0'",
			4	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `creator` int(10) UNSIGNED NOT NULL DEFAULT '0'",
			5	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'",
			6	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `email` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL",
			7	=> "UPDATE `__calendar_raid_guests` SET `status` = '0'",
			8	=> "UPDATE `__calendar_raid_guests` SET `creator` = '1'",
			9	=> "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_calendar_guest_application', 'notification_calendar_guestapplication', 'calendarevent', 1, 1, 0, NULL, 0, 'fa-user-plus');",
			10	=> "ALTER TABLE `__calendar_raid_guests` CHANGE `approved` `status` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '1';",
			11	=> "UPDATE `__calendar_raid_guests` SET `status` = '0' WHERE status='1'",
			12	=> "UPDATE `__calendar_raid_guests` SET `raidgroup` = '0' WHERE `raidgroup` = '';",
			13	=> "ALTER TABLE `__calendar_raid_guests` CHANGE `raidgroup` `raidgroup` INT(10) UNSIGNED NOT NULL DEFAULT '0'",
			14	=> "ALTER TABLE `__articles` CHANGE COLUMN `last_edited_user` `last_edited_user` INT(11) NOT NULL DEFAULT '0';",
			15	=> "ALTER TABLE `__calendar_raid_attendees` CHANGE COLUMN `status_changedby` `status_changedby` INT(11) NOT NULL DEFAULT '0';",
			16	=> "ALTER TABLE `__logs` CHANGE COLUMN `log_value` `log_value` MEDIUMTEXT NOT NULL COLLATE 'utf8_bin';",
			17	=> "ALTER TABLE `__sessions` CHANGE COLUMN `session_user_id` `session_user_id` INT(11) NOT NULL DEFAULT '-1', CHANGE COLUMN `session_perm_id` `session_perm_id` INT(11) NULL DEFAULT '-1';",
			18	=> "ALTER TABLE `__sessions` ADD INDEX `session_start` (`session_start`), ADD INDEX `session_user_id` (`session_user_id`);",
			19	=> "ALTER TABLE `__sessions` ADD INDEX `session_current` (`session_current`);",
			20	=> "DELETE FROM `__sessions` WHERE `session_user_id` < 1;",
		);
	}
}
