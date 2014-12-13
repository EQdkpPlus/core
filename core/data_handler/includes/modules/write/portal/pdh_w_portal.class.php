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

if ( !class_exists( "pdh_w_portal" ) ) {
	class pdh_w_portal extends pdh_w_generic {
	
		public function install($path, $plugin='', $name='', $version='', $child = false) {
			$dbdata = array(
				'name'			=> $name,
				'path'			=> $path,
				'plugin'		=> $plugin,
				'version'		=> $version,
				'child'			=> ($child) ? 1 : 0,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __portal :p")->set($dbdata)->execute();
			
			if($objQuery) {
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('update_portal', array($id));
				return $id;
			}
			return false;
		}

		public function delete($id, $type='id') {
			if($type != 'id' && $type != 'path') $type = 'id';
			if($id){
				$objQuery = $this->db->prepare("DELETE FROM __portal WHERE ".$type." = ?")->execute($id);
				if ($objQuery){
					$this->pdh->enqueue_hook('update_portal', array($id));
					return true;
				}
			}
			return false;
		}

		public function update($id, $data) {
			if (!$id) return false;
			
			$objQuery = $this->db->prepare("UPDATE __portal :p WHERE id = ?;")->set($data)->execute($id);
			if($objQuery) {
				$this->pdh->enqueue_hook('update_portal', array($id));
				return true;
			}
			return false;
		}
		
	}//end class
}//end if
?>