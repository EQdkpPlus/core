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

class update_20023 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.23'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update Beta6';
		
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20023'	=> 'EQdkp Plus 2.0 Update Beta6',
					1			=> 'Add Permission',
					2			=> 'Add Column in raids-table',
					3			=> 'Add Permission for calendar raidnotes',
				),
			'german' => array(
				'update_20023'	=> 'EQdkp Plus 2.0 Update Beta6',
					1			=> 'Füge Berechtigung für Artikel-Skript hinzu',
					2			=> 'Füge Zelle in Raids-Tabelle hinzu',
					3			=> 'Füge Berechtigung für Kalender-Raidnotizen hinzu',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "INSERT INTO `__auth_options` (`auth_value`) VALUES ('u_articles_script');",
			2 => "ALTER TABLE `__raids` ADD COLUMN `raid_additional_data` TEXT NULL DEFAULT NULL COLLATE 'utf8_bin';",
			3 => "INSERT INTO `__auth_options` (`auth_value`) VALUES ('u_calendar_raidnotes');",
		);
	}
	
}


?>