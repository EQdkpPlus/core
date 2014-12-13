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

if( !class_exists( "apa_type_generic" ) ) {
	abstract class apa_type_generic extends gen_class {
		protected $options = array(
			'name'	=> array(
				'type'		=> 'text',
				'size'		=> 20,
				'required'	=> true,
			),
			'exectime'	=> array(
				'type'		=> 'timepicker',
				'default'	=> 14400,
				'hour'		=> 4,
				'min'		=> 0,
				'required'	=> true,
			),
			'pools'	=> array(
				'type'		=> 'multiselect',
			)
		);
		
		protected $required = array('name', 'exectime', 'pools');

		protected $ext_options = array();

		
		abstract public function modules_affected($apa_id);
		abstract public function get_decay_val($apa_id, $date, $module, $dkp_id, $data);
		abstract public function get_cache_date($date, $apa_id);
		
		public function pre_save_func($apa_id, $options) {
			return $options;
		}

		public function required() {
			return $this->required;
		}

		public function set_values($apa){
			$this->options = $this->get_options();
			foreach($this->options as $key => $value){
				if(isset($apa[$key])){
					$this->options[$key]['value'] = $apa[$key];
				}
			}
			return $this->options;
		}

		public function get_options(){
			return $this->options;
		}
		
		//default functions; if layout changes are necessary, implement functions in specific type
		public function add_layout_changes($apa_id) {
			return true;
		}
		
		public function update_layout_changes($apa_id) {
			return true;
		}
		
		public function delete_layout_changes($apa_id) {
			return true;
		}
		
		public function recalculate($apa_id){
			return true;
		}
	}
}
?>