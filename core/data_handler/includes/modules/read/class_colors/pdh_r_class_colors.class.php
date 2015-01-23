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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_class_colors')){
	class pdh_r_class_colors extends pdh_r_generic{

		public $default_lang = 'english';

		public $class_colors = array();

		public $hooks = array(
			'classcolors_update'
		);

		public $presets = array();

		public function reset(){
			$this->pdc->del('pdh_classcolors_table');
			$this->class_colors = NULL;
		}

		public function init(){
			$this->class_colors	= $this->pdc->get('pdh_classcolors_table');
			if($this->class_colors !== NULL){
				return true;
			}

			$this->class_colors = array();
			
			$objQuery = $this->db->query("SELECT * FROM __classcolors");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->class_colors[$row['template']][$row['class_id']]	= $row['color'];
				}
				$this->pdc->put('pdh_classcolors_table', $this->class_colors, null);
			}

		}

		public function get_class_colors($templateid){
			$colors = ($templateid) ? ((isset($this->class_colors[$templateid])) ? $this->class_colors[$templateid] : array()) : $this->class_colors;			
			return $colors;
		}
	}
}
?>
