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

class update_2309 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.0.9.0'; //new plus-version
	public $ext_version		= '2.3.0.9'; //new plus-version
	public $name			= 'Update 2.3.0.9';

	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2309'	=> 'EQdkp Plus 2.3.0.9 Update',
					1			=> 'Add permission',
					2			=> 'Add permission',
					3			=> 'Add permission',
					4			=> 'Add permission',
					5			=> 'Add permission',
					6			=> 'Add permission',
					7			=> 'Add permission',
					8			=> 'Add permission',
					9			=> 'Add permission',
				),
			'german' => array(
				'update_2309'	=> 'EQdkp Plus 2.3.0.9 Update',
					1			=> 'Füge Berechtigung hinzu',
					2			=> 'Füge Berechtigung hinzu',
					3			=> 'Füge Berechtigung hinzu',
					4			=> 'Füge Berechtigung hinzu',
					5			=> 'Füge Berechtigung hinzu',
					6			=> 'Füge Berechtigung hinzu',
					7			=> 'Füge Berechtigung hinzu',
					8			=> 'Füge Berechtigung hinzu',
					9			=> 'Füge Berechtigung hinzu',
			),
		);

		// init SQL querys
		$this->sqls = array(
			1	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_roles_man','N');",
			2	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_member_profilefields_man','N');",
			3	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_apa_man','N');",
			4	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_tables_man','N');",
			5	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_notifications_man','N');",
			6	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_menues_man','N');",
			7	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cronjobs_man','N');",
			8	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_bridge_man','N');",
			9	=> "INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cache_man','N');",
		);
	}

}


?>
