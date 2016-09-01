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

class update_2306 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.6.0'; //new plus-version
	public $ext_version		= '2.3.0.6'; //new plus-version
	public $name			= 'Tmp-Update 2.3.0.6 (remove before release)';

	//ToDo TODO remove before release of 2.3, just a temp update
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2306'	=> 'EQdkp Plus 2.3.0.6 Update',
					1			=> 'Change user table',
					2			=> 'Change user table',
					3			=> 'Change user table',
				),
			'german' => array(
				'update_2306'	=> 'EQdkp Plus 2.3.0.6 Update',
					1			=> 'Ändere Benutzer-Tabelle',
					2			=> 'Ändere Benutzer-Tabelle',
					3			=> 'Ändere Benutzer-Tabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__users` CHANGE COLUMN `user_key` `user_email_confirmkey` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin';",
			2	=> "ALTER TABLE `__users` ADD COLUMN `user_email_confirmed` INT(2) NOT NULL DEFAULT '1';",
			3	=> "ALTER TABLE `__users` ADD COLUMN `user_temp_email` VARCHAR(255) NULL COLLATE 'utf8_bin'",
		);
	}

}


?>
