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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_calendar_raids_templates')){
	class pdh_r_calendar_raids_templates extends pdh_r_generic{

		private $rctemplates;
		public $hooks = array(
			'calendar_templates_update',
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_calendar_raids_table.templates');
			$this->pdc->del_prefix('plugin.guests');
			$this->rctemplates = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->guests		= $this->pdc->get('pdh_calendar_raids_table.templates');
			if($this->rctemplates !== NULL){
				return true;
			}

			// empty array as default
			$this->rctemplates	= array();
			
			$objQuery = $this->db->query('SELECT * FROM __calendar_raid_templates;');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$templatearray = json_decode($row['tpldata'], true);
					$this->rctemplates[$row['id']]['name'] = $row['name'];
					if(is_array($templatearray)){
						foreach($templatearray as $tplkey=>$tplvalue){
							$this->rctemplates[$row['id']][$tplkey] = $tplvalue;
						}
					}
				}
				$this->pdc->put('pdh_calendar_raids_table.templates', $this->rctemplates, NULL);
			}
	
			return true;
		}

		public function get_id_list(){
			return array_keys($this->rctemplates);
		}

		public function get_dropdowndata(){
			$out = array(''=>'----');
			if(is_array($this->rctemplates)){
				foreach($this->rctemplates as $tplid=>$data){
					$out[$tplid]	= $data['name'];
				}
			}
			return $out;
		}

		public function get_idbyname($name){
			if(is_array($this->rctemplates)){
				foreach($this->rctemplates as $tplid=>$data){
					if($data['name'] == $name){
						return $tplid;
					}
				}
			}
		}

		public function get_name($id){
			return $this->rctemplates[$id]['name'];
		}

		public function get_templates($id=''){
			return ($id) ? $this->rctemplates[$id] : $this->rctemplates;
		}

	} //end class
} //end if class not exists
?>