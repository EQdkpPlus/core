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

class update_23028 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.28'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= '2.3.0 RC9';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_23028'	=> 'EQdkp Plus 2.3.0 RC9',
				1	=> 'Add Indexes for Raids',
				2	=> 'Add Indexes for Items',
				3	=> 'Add Indexes for Adjustments',
				4	=> 'Extend Log Table',
			),
			'german' => array(
				'update_23028'	=> 'EQdkp Plus 2.3.0 RC9',
					1	=> 'Füge Indexes für Raids hinzu',
					2	=> 'Füge Indexes für Items hinzu',
					3	=> 'Füge Indexes für Adjustments hinzu',
					4	=> 'Erweitere Log-Tabelle',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__adjustments`
	ADD INDEX `adjustment_value` (`adjustment_value`),
	ADD INDEX `event_id` (`event_id`),
	ADD INDEX `member_id` (`member_id`);",
			2	=> "ALTER TABLE `__raids`
	ADD INDEX `raid_value` (`raid_value`),
	ADD INDEX `event_id` (`event_id`);",
			3	=> "ALTER TABLE `__items`
	ADD INDEX `member_id` (`member_id`),
	ADD INDEX `item_value` (`item_value`),
	ADD INDEX `itempool_id` (`itempool_id`);",
			4 => "ALTER TABLE `__logs`
	ADD COLUMN `trace` TEXT NULL COLLATE 'utf8_bin' AFTER `log_record_id`;",
		);
	}

}

?>