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

class update_2204 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.2.0.4'; //new plus-version
	public $ext_version		= '2.2.0'; //new plus-version
	public $name			= '2.2.0 Update RC2';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2204'	=> 'EQdkp Plus 2.2.0 Update RC2',
					1			=> 'Change approved field to status for calendar guests table',
					2			=> 'Change status number',
					3			=> 'Set empty raidgroup ids to 0 for guest table',
					4			=> 'Change raidgroup column to int for guest table'
				),
			'german' => array(
				'update_2204'	=> 'EQdkp Plus 2.2.0 Update RC2',
					1			=> 'Ändere den Namen des approved Feldes zu status',
					2			=> 'Setze die Status-Felder neu',
					3			=> 'Setze leere Raidgruppen auf 0 bei der Gästetabelle',
					4			=> 'Ändere die Raidrguppe in der Gästetabelle zu integer',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "ALTER TABLE `__calendar_raid_guests` CHANGE `approved` `status` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '1';",
			2	=> "UPDATE `__calendar_raid_guests` SET `status` = '0' WHERE status='1'",
			3	=> "UPDATE `__calendar_raid_guests` SET `raidgroup` = '0' WHERE `raidgroup` = '';",
			4	=> "ALTER TABLE `__calendar_raid_guests` CHANGE `raidgroup` `raidgroup` INT(10) UNSIGNED NOT NULL DEFAULT '0'",
		);
	}

}


?>
