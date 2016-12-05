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

class update_23011 extends sql_update_task {
	public $author			= 'WalleniuM';
	public $version			= '2.3.0.11.0'; //new plus-version
	public $ext_version		= '2.3.0.11'; //new plus-version
	public $name			= 'Update 2.3.0.11';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_23011'	=> 'EQdkp Plus 2.3.0.11 Update',
					1			=> 'Add permissions field to calendar table',
					2			=> 'Set permissions of existing calendars to all',
				),
			'german' => array(
				'update_23011'	=> 'EQdkp Plus 2.3.0.11 Update',
					1			=> 'Füge Berechtigungen Feld zu Kalendertabelle hinzu',
					2			=> 'Setze Berechtigungen bei existierenden Raids auf Alle',
			),
		);
		$this->sqls = array(
			1 => "ALTER TABLE `__calendars` ADD `permissions` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT 'all' AFTER `affiliation`;",
			2 => "UPDATE __calendars SET `permissions` = 'all';",
		);
	}
}
?>
