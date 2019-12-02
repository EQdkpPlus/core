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

class update_23240 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.24.0'; //new plus-version
	public $ext_version		= '2.3.24'; //new plus-version
	public $name			= '2.3.24 Update';
	
	public function __construct(){
		parent::__construct();
		
		$this->langs = array(
				'english' => array(
						'update_23240'	=> 'EQdkp Plus 2.3.24 Update',
						'update_function' => 'Add Permission',
				),
				'german' => array(
						'update_23240'	=> 'EQdkp Plus 2.3.24 Update',
						'update_function' => 'Füge Berechtigung hinzu'
				),
		);
	}
	
	public function update_function(){
		$objInsertQuery = $this->db->prepare("REPLACE INTO __auth_options (`auth_value`, `auth_default`) VALUES ('u_member_conn_free','Y');")->execute();
		if($objInsertQuery){
			$intInsertID = $objInsertQuery->insertId;
		}
		$objQuery = $this->db->prepare('SELECT * FROM __auth_options WHERE auth_value=?')->execute('u_member_conn');
		if($objQuery){
			$arrRow = $objQuery->fetchAssoc();
			$intAuthID = intval($arrRow['auth_id']);
			$objQuery2 = $this->db->prepare('SELECT * FROM __auth_groups WHERE group_id=4 AND auth_id=?')->execute($intAuthID);
			if($objQuery2){
				$arrRow2 = $objQuery2->fetchAssoc();
				if($arrRow2 && count($arrRow2)){
					$objQuery = $this->db->prepare('INSERT INTO __auth_groups (`group_id`, `auth_id`, `auth_setting`) VALUES (?, ?, ?);')->execute(4, $intInsertID, 'Y');
				}
			}
			
			
		}
		
		return true;
	}
	
}

?>