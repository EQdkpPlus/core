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

class update_2100 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.0'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update Alpha';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2100'	=> 'EQdkp Plus 2.1.0 Update',
				'update_function'=> 'Perform some updates on calendar events',
					1			=> 'Alter calendar events table add timezone',
					2			=> 'Rename "none" in repeating field to 0',
					3			=> 'Rename "day" in repeating field to 1',
					4			=> 'Rename "week" in repeating field to 7',
					5			=> 'Rename "twoweek" in repeating field to 14',
					6			=> 'Change repeating field in calendar events table to INT',
				),
			'german' => array(
				'update_2100'	=> 'EQdkp Plus 2.1.0 Update',
				'update_function'=> 'Führe einige Updates für Kalenderereignisse aus',
					1			=> 'Füge die Zeitzone zur Kalender-Events-Tabelle hinzu',
					2			=> 'Bennene den Inhalt des Wiederholungsfeldes von "none" in 0',
					3			=> 'Bennene den Inhalt des Wiederholungsfeldes von "day" in 1',
					4			=> 'Bennene den Inhalt des Wiederholungsfeldes von "week" in 7',
					5			=> 'Bennene den Inhalt des Wiederholungsfeldes von "twoweek" in 14',
					6			=> 'Ändere das Wiederholungsfeldes von VARCHAR zu INT',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__calendar_events` ADD COLUMN `timezone` VARCHAR(255) NULL;",
			2	=> "UPDATE `__calendar_events` SET `repeating` = '0' WHERE `repeating` = 'none';",
			3	=> "UPDATE `__calendar_events` SET `repeating` = '7' WHERE `repeating` = 'week';",
			4	=> "UPDATE `__calendar_events` SET `repeating` = '14' WHERE `repeating` = 'twoweeks';",
			5	=> "UPDATE `__calendar_events` SET `repeating` = '1' WHERE `repeating` = 'day';",
			6	=> "ALTER TABLE `__calendar_events` CHANGE `repeating` `repeating` INT(10) NULL DEFAULT 0;",
		);
	}
	
	public function update_function(){
		// hole mir alle events + user timezone setzen
		$caleventids	= $this->pdh->get('calendar_events', 'id_list');
		if(is_array($caleventids) && count($caleventids) > 0){
			foreach($caleventids as $calid){
				$creator	= $this->pdh->get('calendar_events', 'creatorid', array($calid));
				$creator_tz	= $this->pdh->get('user', 'timezone', array($creator));
				$this->pdh->put('calendar_events', 'update_timezone', array($calid, $creator_tz));
			}
		}
		
		return true;
	}
}


?>