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

class update_2107 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.7'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update 8 Alpha 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2107'		=> 'EQdkp Plus 2.1.0 Update 8',
				'update_function'	=> 'Update for notifications & enable new setting for raidleader auoinvite',
			),
			'german' => array(
				'update_2107'		=> 'EQdkp Plus 2.1.0 Update 8',
				'update_function'	=> 'Aktualisisierung für Benachrichtigungen & Einstellungen für Auto-anmeldung von Raidleitern aktiviert',
			),
		);
		
		// init SQL querys
		$this->sqls = array();
	}
		
	public function update_function(){
		$this->ntfy->addNotificationType('calendarevent_invitation','notification_calendarevent_invitation', 'calendarevent', 0, 1, 0, '', 0, 'fa-envelope');
		$this->config->set('calendar_raidleader_autoinvite', 1);
		return true;
	}
}

?>