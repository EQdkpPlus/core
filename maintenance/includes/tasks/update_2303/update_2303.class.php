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


//ToDo: This Update is the same as 2.2.3, therefore delete when release 2.3.0
class update_2303 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.3'; //new plus-version
	public $ext_version		= '2.3.0'; //new plus-version
	public $name			= 'Update 2.3.0';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2303'	=> 'EQdkp Plus 2.3.0 Update 3',
					1			=> 'Delete notifcation Type',
					2			=> 'Add Notification Type',
				),
			'german' => array(
				'update_2303'	=> 'EQdkp Plus 2.3.0 Update 3',
					1			=> 'Entferne Benachrichtigungstyp',
					2			=> 'Füge Benachrichtigungstyp hinzu',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "DELETE FROM `__notification_types` WHERE `id` = 'eqdkp_user_new_registered';",
			2	=> "INSERT INTO `__notification_types` (`id`, `name`, `category`, `prio`, `default`, `group`, `group_name`, `group_at`, `icon`) VALUES ('eqdkp_user_new_registered', 'notification_user_new_registered', 'user', 0, '0', 0, NULL, 0, 'fa-user-plus');",			
		);
	}

}


?>
