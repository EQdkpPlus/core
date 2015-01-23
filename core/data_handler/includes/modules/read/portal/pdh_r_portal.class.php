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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_portal" ) ) {
	class pdh_r_portal extends pdh_r_generic {

		public $default_lang = 'english';
		public $portal;

		public $hooks = array(
			'update_portal'
		);

		public function reset(){
			$this->pdc->del('pdh_portal_table');
			$this->portal = NULL;
		}

		public function init(){
			$this->portal	= $this->pdc->get('pdh_portal_table');
			if($this->portal !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __portal");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->portal[$drow['id']] = array(
						'name'			=> $drow['name'],
						'path'			=> $drow['path'],
						'version'		=> $drow['version'],
						'plugin'		=> $drow['plugin'],
						'child'			=> $drow['child'],
					);
				}
				
				$this->pdc->put('pdh_portal_table', $this->portal, null);
			}
		}
		
		public function get_portal($id=''){
			return ($id) ? $this->portal[$id] : $this->portal;
		}

		public function get_id_list() {
			$ids = (!empty($this->portal)) ? array_keys($this->portal) : array();
			return $ids;
		}

		public function get_path($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['path'] : false;
		}

		public function get_plugin($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['plugin'] : false;
		}

		public function get_name($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['name'] : false;
		}

		public function get_version($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['version'] : false;
		}

		public function get_child($id){
			return (isset($this->portal[$id])) ? (int)$this->portal[$id]['child'] : false;
		}
	}//end class
}//end if
?>