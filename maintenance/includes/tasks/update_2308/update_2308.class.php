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

class update_2308 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.8.0'; //new plus-version
	public $ext_version		= '2.3.0.8'; //new plus-version
	public $name			= 'Update 2.3.0.8';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2308'	=> 'EQdkp Plus 2.3.0.8 Update',
					1			=> 'Alter table raids',
					2			=> 'Alter table items',
					3			=> 'Alter table adjustments',
					4			=> 'Alter table members',
					5			=> 'Alter table members',
					6			=> 'Create table member_points'
				),
			'german' => array(
				'update_2308'	=> 'EQdkp Plus 2.3.0.8 Update',
					1			=> 'Erweitere Tabelle raids',
					2			=> 'Erweitere Tabelle items',
					3			=> 'Erweitere Tabelle adjustments',
					4			=> 'Erweitere Tabelle members',
					5			=> 'Erweitere Tabelle members',
					6			=> 'Erstelle Tabelle member_points',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__raids` ADD COLUMN `raid_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			2	=> "ALTER TABLE `__items` ADD COLUMN `item_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			3	=> "ALTER TABLE `__adjustments` ADD COLUMN `adjustment_apa_value` TEXT NULL COLLATE 'utf8_bin'",
			4	=> "ALTER TABLE `__members` ADD COLUMN `points` TEXT NULL COLLATE 'utf8_bin'",
			5	=> "ALTER TABLE `__members` ADD COLUMN `points_apa` TEXT NULL COLLATE 'utf8_bin'",
			6	=> "DROP TABLE IF EXISTS __member_points;",
			6 => "CREATE TABLE `__member_points` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`member_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`mdkp_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`current` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`earned` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`spent` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`adjustments` FLOAT(11,2) NOT NULL DEFAULT '0.00',
	`misc` TEXT NULL COLLATE 'utf8_bin',
	`type` VARCHAR(10) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
		);
	}

}


?>
