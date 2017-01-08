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

class update_22100 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.2.10.0'; //new plus-version
	public $ext_version		= '2.2.10'; //new plus-version
	public $name			= 'Update 2.2.10';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_22100'	=> 'EQdkp Plus 2.2.10 Update',
					1			=> 'Change session table',
					2			=> 'Change session table',
					3			=> 'Change session table',
					4			=> 'Cleanup session table',
				),
			'german' => array(
				'update_22100'	=> 'EQdkp Plus 2.2.10 Update',
					1			=> 'Ändere Session-Tabelle',
					2			=> 'Ändere Session-Tabelle',
					3			=> 'Ändere Session-Tabelle',
					4			=> 'Räume Session-Tabelle auf',	
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__sessions` CHANGE COLUMN `session_user_id` `session_user_id` INT(11) NOT NULL DEFAULT '-1', CHANGE COLUMN `session_perm_id` `session_perm_id` INT(11) NULL DEFAULT '-1';",
			2	=> "ALTER TABLE `__sessions` ADD INDEX `session_start` (`session_start`), ADD INDEX `session_user_id` (`session_user_id`);",
			3	=> "ALTER TABLE `__sessions` ADD INDEX `session_current` (`session_current`);",
			4	=> "DELETE FROM `__sessions` WHERE `session_user_id` < 1;"
		);
	}

}


?>
