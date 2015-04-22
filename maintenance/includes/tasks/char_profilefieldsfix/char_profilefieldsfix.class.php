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

class char_profilefieldsfix extends task {
	public $author = 'GodMod';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Character fix Profilefields';
	public $type = 'fix';

	public function is_applicable() {
		return true;
	}
	
	public function is_necessary() {
		return false;
	}
	
	public function get_form_content() {
		$objQuery = $this->db->query("SELECT profiledata, member_id FROM __members;");
		$profiledata = array();
		while($objQuery && $row = $objQuery->fetchAssoc()) {
			$profiledata			= json_decode($row['profiledata'], true);
			
			foreach($profiledata as $key => $val){
				if(!is_numeric($val) && stripos($val, '&#') === false) {
					$profiledata[$key] = filter_var($val, FILTER_SANITIZE_STRING);
				}
			}
			
			$this->db->prepare("UPDATE __members :p WHERE member_id = ?;")->set(array('profiledata' => json_encode($profiledata)))->execute($row['member_id']);
		}
		
		return $this->lang['char_profilefieldsfix_done'];
	}
}
?>