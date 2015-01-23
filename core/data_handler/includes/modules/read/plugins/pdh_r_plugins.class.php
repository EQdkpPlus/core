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

if ( !class_exists( "pdh_r_plugins" ) ) {
	class pdh_r_plugins extends pdh_r_generic{

		public $default_lang = 'english';
		public $plugins;

		public $hooks = array(
			'plugins_update'
		);

		public function reset(){
			$this->pdc->del('pdh_plugins_table');
			$this->plugins = NULL;
		}

		public function init(){
			$this->plugins	= $this->pdc->get('pdh_plugins_table');
			if($this->plugins !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __plugins ORDER BY code");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->plugins[$drow['code']] = array(
						'code'		=> $drow['code'],
						'version'	=> $drow['version'],
						'status'	=> $drow['status']
					);
				}
				
				$this->pdc->put('pdh_plugins_table', $this->plugins, null);
			}
		}

		public function get_id_list() {
			return array_keys($this->plugins);
		}

		public function get_data($plugin_code='', $field=''){
			return ($plugin_code) ? (($field) ? $this->plugins[$plugin_code][$field] : $this->plugins[$plugin_code]) : $this->plugins;
		}
	}//end class
}//end if
?>