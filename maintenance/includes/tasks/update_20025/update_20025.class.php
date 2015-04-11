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

class update_20025 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.0.0.25'; //new plus-version
	public $ext_version		= '2.0.0'; //new plus-version
	public $name			= '2.0.0 Update 1';
		
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_20025'		=> 'EQdkp Plus 2.0 Update 1',
				'update_function'	=> 'Add Permission',
				),
			'german' => array(
				'update_20025'	=> 'EQdkp Plus 2.0 Update 1',
				'update_function'	=> 'Füge Berechtigung hinzu',
			),
		);
		
		// init SQL querys
		$this->sqls = array(
		);
	}
	
	public function update_function(){
		// hole mir alle events + user timezone setzen
		$objQuery = $this->db->query("SELECT * FROM __auth_options WHERE auth_value='u_articles_script'");
		if($objQuery){
			$intRows = $objQuery->numRows;
			if($intRows == 0){
				$this->db->query("INSERT INTO `__auth_options` (`auth_value`) VALUES ('u_articles_script');");
			}
		}
		return true;
	}
}


?>