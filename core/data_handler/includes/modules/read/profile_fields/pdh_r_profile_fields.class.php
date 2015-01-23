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

if ( !class_exists( "pdh_r_profile_fields" ) ) {
	class pdh_r_profile_fields extends pdh_r_generic{

		public $default_lang = 'english';
		public $profile_fields;
		public $profile_categories;

		public $hooks = array(
			'game_update'
		);

		public function reset(){
			$this->pdc->del('pdh_profile_fields_table');
			$this->pdc->del('pdh_profile_categories_table');
			$this->profile_fields = NULL;
			$this->profile_categories = NULL;
		}

		public function init(){
			$this->profile_fields			= $this->pdc->get('pdh_profile_fields_table');
			$this->profile_categories		= $this->pdc->get('pdh_profile_categories_table');
			if($this->profile_fields !== NULL && $this->profile_categories !== NULL){
				return true;
			}
			$this->profile_fields = array();
			$objQuery = $this->db->query("SELECT * FROM __member_profilefields ORDER BY sort ASC,category,name");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()) {
					// build categories array
					if(!is_array($this->profile_categories) || !in_array($drow['category'], $this->profile_categories)){
						if($drow['category'] != 'character'){
							$this->profile_categories[] = $drow['category'];
						}
					}
	
					$this->profile_fields[$drow['name']] = array(
						'type'			=> $drow['type'],
						'category'		=> $drow['category'],
						'size'			=> $drow['size'],
						'sort'			=> $drow['sort'],
						'lang'			=> $drow['lang'],
						'options_language' => $drow['options_language'],
						'image'			=> $drow['image'],
						'enabled'		=> $drow['enabled'],
						'data'			=> unserialize($drow['data']),
						'undeletable'	=> $drow['undeletable'],
						'custom'		=> $drow['custom'],
					);
					foreach($this->profile_fields[$drow['name']]['data'] as $key => $dat) {
						$this->profile_fields[$drow['name']][$key] = $dat;
					}
				}
				
				// check if the character tab is in the categories list, if not add it
				if (!is_array($this->profile_categories)) $this->profile_categories = array();
				if(!in_array('character', $this->profile_categories)){
					$this->profile_categories[] = 'character';
				}
	
				// save all the stuff to the cache
				$this->profile_categories = array_unique($this->profile_categories);
				$this->pdc->put('pdh_profile_fields_table', $this->profile_fields, null);
				$this->pdc->put('pdh_profile_categories_table', $this->profile_categories, null);
			}
		}

		public function get_categories(){
			return $this->profile_categories;
		}

		public function get_fields($name=''){
			return ($name) ? (isset($this->profile_fields[$name]) ? $this->profile_fields[$name] : null) : $this->profile_fields;
		}

		public function get_fieldlist(){
			return array_keys($this->profile_fields);
		}

		public function get_lang($name) {
			return $this->profile_fields[$name]['lang'];
		}
		
		public function get_options_language($name) {
			return $this->profile_fields[$name]['options_language'];
		}

	}//end class
}//end if
?>