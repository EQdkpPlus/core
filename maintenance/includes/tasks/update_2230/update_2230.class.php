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

class update_2230 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.2.3.0'; //new plus-version
	public $ext_version		= '2.2.3'; //new plus-version
	public $name			= 'Update 2.2.3';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2230'	=> 'EQdkp Plus 2.2.3 Update',
					1			=> 'Change article table',
					2			=> 'Change calendar_raid_attendees table',
					3			=> 'Change log table',
				),
			'german' => array(
				'update_2230'	=> 'EQdkp Plus 2.2.3 Update',
					1			=> 'Ändere Article-Tabelle',
					2			=> 'Ändere calendar_raid_attendees-Tabelle',
					3			=> 'Ändere Logs-Tabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__articles` CHANGE COLUMN `last_edited_user` `last_edited_user` INT(11) NOT NULL DEFAULT '0';",
			2	=> "ALTER TABLE `__calendar_raid_attendees` CHANGE COLUMN `status_changedby` `status_changedby` INT(11) NOT NULL DEFAULT '0';",
			3	=> "ALTER TABLE `__logs` CHANGE COLUMN `log_value` `log_value` MEDIUMTEXT NOT NULL COLLATE 'utf8_bin';",
		);
	}

}


?>
