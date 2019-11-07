<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!class_exists('pdh_w_notifications')) {
	class pdh_w_notifications extends pdh_w_generic {

		public function add($strType, $intToUserID, $strFromUsername, $strRecordsetId, $strLink, $strAdditonalData=''){
			if($this->server_path != "/" && $this->server_path != ""){
				$strLink = str_replace($this->server_path, '{SERVER_PATH}', $strLink);
			} else {
				$strLink = '{SERVER_PATH}'.$strLink;
			}

			$strLink = str_replace('{SERVER_PATH}/', '{SERVER_PATH}', $strLink);
			$strLink = str_replace($this->root_path, '{SERVER_PATH}', $strLink);
			$strLink = str_replace($this->SID, '{SID}', $strLink);
			$strLink = str_replace('?', '{SID}&', $strLink);
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
				$this->pdh->enqueue_hook('notifications_update', array($objQuery->insertId));
				return $objQuery->insertId;
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

		public function mark_as_read_bytype($strType, $intUserId, $mixDatasetID){
			if(is_array($mixDatasetID)){
				if(count($mixDatasetID) === 0) return true;
				$objQuery = $this->db->prepare("UPDATE __notifications :p WHERE type=? AND user_id=? AND dataset_id :in;")->set(array(
						'`read`'	=> 1
				))->in($mixDatasetID)->execute($strType, $intUserId);

			} else {
				$objQuery = $this->db->prepare("UPDATE __notifications :p WHERE type=? AND user_id=? AND dataset_id=?;")->set(array(
						'`read`'	=> 1
				))->execute($strType, $intUserId, $mixDatasetID);
			}
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
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `type` = ?")->execute($strType);

			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}

		public function delete_by_type_and_recordset($strType, $strRecordsetID){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `type` = ? AND dataset_id=?")->execute($strType, $strRecordsetID);
			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}

		public function delete_by_type_and_recordset_for_user($strType, $strRecordsetID, $intUserID){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `type` = ? AND dataset_id=? AND user_id = ?")->execute($strType, $strRecordsetID, $intUserID);
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

		public function cleanup_read($intTime){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `read` = 1 AND `time` < ?")->execute($intTime);

			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}

		public function cleanup_unread($intTime){
			$objQuery = $this->db->prepare("DELETE FROM __notifications WHERE `time` < ?")->execute($intTime);

			if($objQuery) {
				$this->pdh->enqueue_hook('notifications_update');
				return true;
			}
			return false;
		}


		public function del_type($strType){
			$objQuery = $this->db->prepare("DELETE FROM __notification_types WHERE id=?")->execute($strType);

			if($objQuery) {
				$this->pdh->enqueue_hook('notification_types_update', array());
				return true;
			}
			return false;
		}

	}
}
