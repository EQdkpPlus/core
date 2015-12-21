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

class update_2203 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.2.0.3'; //new plus-version
	public $ext_version		= '2.2.0'; //new plus-version
	public $name			= '2.2.0 Update 3';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2203'	=> 'EQdkp Plus 2.2.0 Update 3',
					1			=> 'Alter raid guests table and add creator column',
					2			=> 'Alter raid guests table and add colum for approval status',
					3			=> 'Alter raid guests table and add colum for email address',
					4			=> 'Set '
				),
			'german' => array(
				'update_2203'	=> 'EQdkp Plus 2.2.0 Update 3',
					1			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für Ersteller hinzu',
					2			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für Freigabestatus hinzu',
					3			=> 'Ändere die Raidgäste-Tabelle und füge Spalte für E-Mails hinzu',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `creator` int(10) UNSIGNED NOT NULL DEFAULT '0'",
			2	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
			3	=> "ALTER TABLE `__calendar_raid_guests` ADD COLUMN `email` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL",
			4	=> "UPDATE `__calendar_raid_guests` SET `approved` = '1'",
			5	=> "UPDATE `__calendar_raid_guests` SET `creator` = '1'",
		);
	}

}


?>
