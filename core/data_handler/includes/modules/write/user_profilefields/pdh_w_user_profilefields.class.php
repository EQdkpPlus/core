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

if(!class_exists('pdh_w_user_profilefields')) {
	class pdh_w_user_profilefields extends pdh_w_generic {
		
		public function insert_field($arrValues, $arrOptions){
		
			$arrSet = array(
					'name' 		=> $arrValues['name'],
					'lang_var' 	=> $arrValues['lang_var'],
					'type' 		=> $arrValues['type'],
					'length' 	=> $arrValues['length'],
					'minlength' => $arrValues['minlength'],
					'validation' => $arrValues['validation'],
					'required' => $arrValues['required'],
					'show_on_registration' => $arrValues['show_on_registration'],
					'enabled' => $arrValues['enabled'],
					'is_contact' => $arrValues['is_contact'],
					'contact_url' => $arrValues['contact_url'],
					'icon_or_image' => $arrValues['icon_or_image'],
					'bridge_field' => $arrValues['bridge_field'],
					'options' => serialize(array('options' => $arrOptions)),
					'editable' => 1,
					'sort_order' => 9999999,
			);
				
			$objQuery = $this->db->prepare("INSERT INTO __user_profilefields :p ")->set($arrSet)->execute();
				
			if($objQuery) {
				$this->pdh->enqueue_hook('user_profilefields_update');
				return $objQuery->insertId;
			}
				
			return false;
		}
		
		public function update_field($intFieldID, $arrValues, $arrOptions){

			$arrSet = array(
				'name' 		=> $arrValues['name'],
				'lang_var' 	=> $arrValues['lang_var'],
				'type' 		=> $arrValues['type'],
				'length' 	=> $arrValues['length'],
				'minlength' => $arrValues['minlength'],
				'validation' => $arrValues['validation'],
				'required' => $arrValues['required'],
				'show_on_registration' => $arrValues['show_on_registration'],
				'enabled' => $arrValues['enabled'],
				'is_contact' => $arrValues['is_contact'],
				'contact_url' => $arrValues['contact_url'],
				'icon_or_image' => $arrValues['icon_or_image'],
				'bridge_field' => $arrValues['bridge_field'],
				'options' => serialize(array('options' => $arrOptions)),
				'editable' => 1
			);
			
			$objQuery = $this->db->prepare("UPDATE __user_profilefields :p WHERE id=?")->set($arrSet)->execute($intFieldID);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
				return true;
			}
			
			return false;
		}
		
		public function enable_field($intFieldID){
			$objQuery = $this->db->prepare('UPDATE __user_profilefields :p WHERE id=?')->set(array(
				'enabled' => 1
			))->execute($intFieldID);
				
			if ($objQuery) {
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
				return true;
			} else {
				return false;
			}
		}
		
		public function disable_field($intFieldID) {
			$objQuery = $this->db->prepare('UPDATE __user_profilefields :p WHERE id=?')->set(array(
				'enabled' => 0
			))->execute($intFieldID);
				
			if ($objQuery) {
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
				return true;
			} else {
				return false;
			}
		}
		
		public function set_sortation($intFieldID, $intSortID){
			$old['sortid'] = $this->pdh->get('user_profilefields', 'sort_order', array($intFieldID));
			if ($old['sortid'] != $intSortID){
				$arrSet = array(
						'sort_order'	=> $intSortID,
				);
				$objQuery = $this->db->prepare("UPDATE __user_profilefields :p WHERE id=?")->set($arrSet)->execute($intFieldID);
				
				if(!$objQuery) {
					return false;
				}
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
			}
		}

		public function delete_field($intFieldID) {
			$objQuery = $this->db->prepare("DELETE FROM __user_profilefields WHERE id = ?;")->execute($intFieldID);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
				return true;
			}
			return false;
		}
		
		public function delete_fields($arrFields) {
			if (is_array($arrFields)) {
				foreach($arrFields as $intFieldID) {
					$this->delete_field($intFieldID);
				}
				$this->pdh->enqueue_hook('user_profilefields_update', array($intFieldID));
				return true;
			}
			return false;
		}
		
		public function truncate(){
			if($this->db->query("TRUNCATE __user_profilefields;")) {
				$this->pdh->enqueue_hook('user_profilefields_update');
				return true;
			}
			return false;
		}
	}
}
?>