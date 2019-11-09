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

class update_2320 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.3.2.0'; //new plus-version
	public $ext_version		= '2.3.2'; //new plus-version
	public $name			= '2.3.2 Update';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2320'	=> 'EQdkp Plus 2.3.2 Update',
				1	=> 'Create a temporary table for calendar raid attendees',
				2	=> 'Add unique poperty to calendar_events_id and member_id fields at calendar_raids_attendees table',
				3	=> 'Insert the non-duplicate entries into the temporary table',
				4	=> 'Rename temporary table to real one and real one to backup'
			),
			'german' => array(
				'update_2320'	=> 'EQdkp Plus 2.3.2 Update',
				1	=> 'Erstelle eine temporäre Tabelle für Kalender Raidteilnehmer',
				2	=> 'Füge Unique Eigenschaft zu den Feldern calendar_events_id und member_id bei der Tabele calendar_raids_attendees',
				3	=> 'Übertrage die duplikatfreien Einträge aus der calendar_raids_attendees in die temporäre Tabelle',
				4	=> 'Bennene die live Tabelle in Backup um und die temporäre calendar_raids_attendees in die live.',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "CREATE TABLE __tmp_calendar_raid_attendees LIKE __calendar_raid_attendees;",
			2	=> "ALTER TABLE __tmp_calendar_raid_attendees ADD UNIQUE(calendar_events_id, member_id);",
			3	=> "INSERT IGNORE INTO __tmp_calendar_raid_attendees SELECT * FROM __calendar_raid_attendees ORDER BY id DESC;",
			4	=> "RENAME TABLE __calendar_raid_attendees TO __backup_calendar_raid_attendees, __tmp_calendar_raid_attendees TO __calendar_raid_attendees;",
		);
	}

}
