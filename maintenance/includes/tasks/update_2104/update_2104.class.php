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

class update_2104 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.1.0.4'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update 5 Alpha 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2104'	=> 'EQdkp Plus 2.1.0 Update 5',
					1 => 'Add field for away mode to user table',
					2 => 'Add field for away mode to user table',
					3 => 'Add field for away mode to user table',
					4 => 'Add field for away mode to user table',
				),
			'german' => array(
				'update_2104'	=> 'EQdkp Plus 2.1.0 Update 5',
					1 => 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					2 => 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					3 => 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
					4 => 'Füge Feld für Abwesenheitsmodus in die Benutzertabelle ein',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_enabled` tinyint(1) NOT NULL DEFAULT 1;",
			2	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_startdate` BIGINT(10) NULL DEFAULT '0';",
			3	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_enddate` BIGINT(10) NULL DEFAULT '0';",
			4	=> "ALTER TABLE `__users` ADD COLUMN `awaymode_note` text COLLATE utf8_bin;",
		);
	}
}

?>