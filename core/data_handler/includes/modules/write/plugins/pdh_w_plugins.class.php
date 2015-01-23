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

if(!class_exists('pdh_w_plugins')) {
	class pdh_w_plugins extends pdh_w_generic {

		public function update_version($version, $plugin_code) {
			$objQuery = $this->db->prepare("UPDATE __plugins :p WHERE code=?;")->set(array(
					'version'	=> $version
			))->execute($plugin_code);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function delete_plugin($plugin_code) {
			$objQuery = $this->db->prepare("DELETE FROM __plugins WHERE `code` = ?")->execute($plugin_code);
				
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function add_plugin($code, $version) {
			$objQuery = $this->db->prepare("INSERT INTO __plugins :p")->set(array(
				'code'		=> $code,
				'status'	=> '0',
				'version'	=> $version
			))->execute();
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
		
		public function set_status($code, $status) {
			$objQuery = $this->db->prepare("UPDATE __plugins :p WHERE code=?;")->set(array('status' => $status))->execute($code);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
	}
}
?>