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

class update_23230 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.3.23.0'; //new plus-version
	public $ext_version		= '2.3.23'; //new plus-version
	public $name			= '2.3.23 Update';
	
	public function __construct(){
		parent::__construct();
		
		$this->langs = array(
				'english' => array(
						'update_23230'	=> 'EQdkp Plus 2.3.23 Update',
						'update_function' => 'Repair Tables',
				),
				'german' => array(
						'update_23230'	=> 'EQdkp Plus 2.3.23 Update',
						'update_function' => 'Repariere Tabellen',
				),
		);
		
	}
	
	public function update_function(){
		#members und events
		
		$objQuery = $this->db->prepare("SELECT max(member_id) as max FROM __members")->execute();
		if($objQuery){
			$arrRow = $objQuery->fetchAssoc();
			
			$intMax = intval($arrRow['max']);
			
			$this->db->prepare("UPDATE __members SET member_id=? WHERE member_id=0")->execute($intMax+1);
			
			$this->db->prepare("ALTER TABLE `__members` AUTO_INCREMENT = ".($intMax+2).", CHANGE COLUMN `member_id` `member_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT")->execute();

		}
		
		$objQuery = $this->db->prepare("SELECT max(event_id) as max FROM __events")->execute();
		if($objQuery){
			$arrRow = $objQuery->fetchAssoc();
			
			$intMax = intval($arrRow['max']);
			
			$this->db->prepare("UPDATE __events SET event_id=? WHERE event_id=0")->execute($intMax+1);
			
			$this->db->prepare("ALTER TABLE `__events` AUTO_INCREMENT = ".($intMax+2).", CHANGE COLUMN `event_id` `event_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT")->execute();
			
		}
		
		return true;
	}
	
}

?>