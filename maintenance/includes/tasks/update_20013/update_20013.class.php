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

class update_20013 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.0.0.13'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 5';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20013'	=> 'EQdkp Plus 2.0 Update 5',
					1			=> 'Add affiliation field to calendar table',
					2			=> 'Set affiliation to core for core calendars',
					3			=> 'Set affiliation to user for rest',
					4			=> 'Add systems table to raid groups table',
					5			=> 'Set system to 1 for undeletable fields',
					6			=> 'Remove now unused deletable field',
				),
			'german' => array(
				'update_20013'	=> 'EQdkp Plus 2.0 Update 5',
					1			=> 'Add affiliation field to calendar table',
					2			=> 'Set affiliation to core for core calendars',
					3			=> 'Set affiliation to user for rest',
					4			=> 'Add systems table to raid groups table',
					5			=> 'Set system to 1 for undeletable fields',
					6			=> 'Remove now unused deletable field',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "ALTER TABLE __calendars ADD affiliation VARCHAR(30) NOT NULL DEFAULT 'user' AFTER `restricted`;",
			2 => "UPDATE __calendars SET `affiliation` = 'core' WHERE `system` = '1';",
			3 => "UPDATE __calendars SET `affiliation` = 'user' WHERE `system` = '';",
			4 => "ALTER TABLE __groups_raid ADD groups_raid_system tinyint(1) NOT NULL DEFAULT '0' AFTER `groups_raid_sortid`;",
			5 => "UPDATE __groups_raid SET `groups_raid_system` = '1' WHERE `groups_raid_deletable` = '0';",
			6 => "ALTER TABLE __groups_raid DROP groups_raid_deletable;"
		);
	}
}


?>