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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_itempool')){
	class pdh_r_itempool extends pdh_r_generic{

		public $default_lang	= 'english';
		public $itempools		= array();

		public $hooks = array(
			'itempool_update'
		);

		public function reset(){
			$this->pdc->del('pdh_itempools_table');
			$this->itempools = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->itempools = $this->pdc->get('pdh_itempools_table');
			if($this->itempools !== NULL){
				return true;
			}

			$this->itempools = array();
			
			$objQuery = $this->db->query("SELECT itempool_id, itempool_name, itempool_desc FROM __itempool;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->itempools[$row['itempool_id']]['name'] = $row['itempool_name'];
					$this->itempools[$row['itempool_id']]['desc'] = $row['itempool_desc'];
				}
				$this->pdc->put('pdh_itempools_table', $this->itempools, null);
			}
		}

		public function get_id_list(){
			return array_keys($this->itempools);
		}

		public function get_id($itempool_name){
			foreach($this->itempools as $id => $itempool){
				if($itempool['name'] == $name){
					return $id;
				}
			}
		}

		public function get_name($itempool_id){
			return (isset($this->itempools[$itempool_id]['name'])) ? $this->itempools[$itempool_id]['name'] : '';
		}

		public function get_desc($itempool_id){
			return (isset($this->itempools[$itempool_id]['desc'])) ? $this->itempools[$itempool_id]['desc'] : '';
		}
	}
}
?>