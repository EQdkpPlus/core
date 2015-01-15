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

class update_20017 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.17'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 9';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20017'	=> 'EQdkp Plus 2.0 Update 9',
					1			=> 'Add permission',
					2			=> 'Add permission',
				),
			'german' => array(
				'update_20017'	=> 'EQdkp Plus 2.0 Update 9',
					1			=> 'Add permission',
					2			=> 'Add permission',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
			1 => "INSERT INTO `__auth_options` (`auth_value`) VALUES ('a_users_perms');",
			2 => "INSERT INTO `__auth_options` (`auth_value`) VALUES ('a_users_profilefields');",
		);
	}
	
	public function update_function(){
		$this->config->set('enable_registration', !$this->config->get('disable_registration'));
		$this->config->set('enable_embedly', !$this->config->get('disable_embedly'));
		$this->config->set('enable_points', !$this->config->get('disable_points'));
		$this->config->set('enable_username_change', !$this->config->get('disable_username_change'));
		return true;
	}
}


?>