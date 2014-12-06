<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2014-02-05 21:00:21 +0100 (Mi, 05 Feb 2014) $
* -----------------------------------------------------------------------
* @author		$Author: hoofy_leon $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13999 $
*
* $Id: pdh_w_notifications.class.php 13999 2014-02-05 20:00:21Z hoofy_leon $
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_notifications')) {
	class pdh_w_notifications extends pdh_w_generic {
		
		public function add($strType, $intToUserID, $strFromUsername, $strRecordsetId, $strLink, $strAdditonalData=''){

			$strLink = str_replace($this->server_path, '{SERVER_PATH}', $strLink);
			$strLink = str_replace($this->SID, '{SID}', $strLink);
			$strLink = str_replace('?', '{SID}', $strLink);
			$strLink = str_replace('//', '/', $strLink);
			
			$objQuery = $this->db->prepare("INSERT INTO __notifications :p")->set(array(
					'type'				=> $strType,
					'user_id'			=> $intToUserID,
					'dataset_id'		=> $strRecordsetId,
					'username'			=> $strFromUsername,
					'link'				=> $strLink,
					'time'				=> $this->time->time,
					'additional_data'	=> $strAdditonalData,
			))->execute();

			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update', array());
				return true;
			}
			return false;			
		}
		
		public function mark_as_read($intNotificationID){
			$objQuery = $this->db->prepare("UPDATE __notifications :p WHERE id=?;")->set(array(
					'`read`'	=> 1
			))->execute($intNotificationID);
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update', array($intNotificationID));
				return true;
			}
			return false;
		}
		
		public function mark_all_as_read($intUserID){
			$objQuery = $this->db->prepare("UPDATE __notifications :p WHERE user_id=?;")->set(array(
					'`read`'	=> 1
			))->execute($intUserID);
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
			
		}
		
		public function delete($intNotificationID){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE id = ?")->execute($intNotificationID);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update', array($intNotificationID));
				return true;
			}
			return false;
		}
		
		public function delete_by_type($strType){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE type = ?")->execute($strType);
				
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}
		
		public function delete_by_type_and_recordset($strType, $strRecordsetID){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE type = ? AND dataset_id=?")->execute($strType, $strRecordsetID);
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}
		
		public function delete_by_user($intUserID){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE user_id = ?")->execute($intUserID);
		
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}
		
		public function cleanup($intTime){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `read` = 1 AND time < ?")->execute($intTime);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}
		
		public function add_type($strType, $strName, $strCategory, $intPrio=0, $blnDefault=0, $blnGroup=0, $strGroupName='', $intGroupAt=3, $strIcon=""){
			
			$objQuery = $this->db->prepare("INSERT INTO __notification_types :p")->set(array(
					'id'			=> $strType,
					'name'			=> $strName,
					'category'		=> $strCategory,
					'prio'			=> $intPrio,
					'default'		=> ($blnDefault) ? 1 : 0,
					'group'			=> ($blnGroup) ? 1 : 0,
					'group_name'	=> $strGroupName,
					'group_at'		=> $intGroupAt,
					'icon'			=> $strIcon,
			))->execute();
		
			if($objQuery) {
				$this->pdh->enqueue_hook('notification_types_update', array());
				return true;
			}
			return false;
		}

	}
}
?>