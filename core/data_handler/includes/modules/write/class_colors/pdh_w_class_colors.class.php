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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_class_colors')) {
	class pdh_w_class_colors extends pdh_w_generic {
	
		public function add_classcolor($template, $clsid='', $color=''){
			$color = (substr($color, 0, 1) == '#') ? $color : ((strlen($color)) ? '#'.$color : '');
			
			$objQuery = $this->db->prepare('INSERT INTO __classcolors :p')->set(array(
				'template'		=> $template,
				'class_id'		=> $clsid,
				'color'			=> $color,
			))->execute();
			if($objQuery){
				$this->pdh->enqueue_hook('classcolors_update');
				return $objQuery->insertId;
			}
			return false;
		}

		public function truncate_classcolor() {
			$this->pdh->enqueue_hook('classcolors_update');
			return $this->db->query("TRUNCATE __classcolors");
		}

		public function delete_classcolor($template) {
			$objQuery = $this->db->prepare("DELETE FROM __classcolors WHERE template=?")->execute($template);
			$this->pdh->enqueue_hook('classcolors_update');
		}
		
	}
}
?>