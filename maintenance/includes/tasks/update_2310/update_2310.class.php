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

class update_2310 extends sql_update_task {
	public $author			= 'Wallenium';
	public $version			= '2.3.1.0'; //new plus-version
	public $ext_version		= '2.3.1'; //new plus-version
	public $name			= '2.3.1';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2310'	=> 'EQdkp Plus 2.3.1',
				1	=> 'Remove duplicate calendar raid attendees intriduced by a bug in 2.3.0',
			),
			'german' => array(
				'update_2310'	=> 'EQdkp Plus 2.3.1',
				1	=> 'Entferne Dupikate der Raidteilnehmer, die durch einen Fehler in 2.3.0 gespeichert wurden',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "DELETE att1 FROM __calendar_raid_attendees att1, __calendar_raid_attendees att2 WHERE att1.id < att2.id AND att1.calendar_events_id = att2.calendar_events_id AND att1.member_id=att2.member_id",
		);
	}

}

?>