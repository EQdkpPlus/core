<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
* Date:			$Date: 2014-02-05 21:00:21 +0100 (Mi, 05 Feb 2014) $
* -----------------------------------------------------------------------
* @author		$Author: hoofy_leon $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13999 $
*
* $Id: pdh_w_user_profilefields.class.php 13999 2014-02-05 20:00:21Z hoofy_leon $
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