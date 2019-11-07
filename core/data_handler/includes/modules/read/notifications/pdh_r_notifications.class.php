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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_notifications" ) ) {
	class pdh_r_notifications extends pdh_r_generic{
		public static function __shortcuts() {
			$shortcuts = array();
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public $default_lang = 'english';
		public $notifications = null;
		public $user_notifications = null;

		public $hooks = array(
			'notifications_update',
		);

		//Trunks
		private $index = array();
		private $objPagination = null;

		public $presets = array(
			'notifications_id' => array('id', array('%intNotificationID%'), array()),
			'notifications_type' => array('type', array('%intNotificationID%'), array()),
			'notifications_user_id' => array('user_id', array('%intNotificationID%'), array()),
			'notifications_time' => array('time', array('%intNotificationID%'), array()),
			'notifications_read' => array('read', array('%intNotificationID%'), array()),
			'notifications_dataset_id' => array('dataset_id', array('%intNotificationID%'), array()),
		);

		public function reset($affected_ids=array()){
				$this->objPagination = register("cachePagination", array("notifications", "id", "__notifications", array(), 100));
				return ($this->objPagination) ? $this->objPagination->reset($affected_ids) : false;
		}

		public function init(){
			$this->objPagination = register("cachePagination", array("notifications", "id", "__notifications", array(), 100));
			$this->objPagination->initIndex();
			$this->index = $this->objPagination->getIndex();

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			return $this->index;
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intNotificationID){
			return $this->objPagination->get($intNotificationID);
		}

		public function get_notifications_for_user($intUserID){
			$arrOut = array();
			$objQuery = $this->db->prepare("SELECT id FROM __notifications WHERE user_id=? AND `read`=0 ORDER BY `time` DESC")->execute($intUserID);

			if($objQuery){
				while ( $row = $objQuery->fetchAssoc() ) {
					$arrOut[] = $row['id'];
				}
			}

			return $arrOut;
		}

		public function get_all_notifications_for_user($intUserID){
			$arrOut = array();
			$objQuery = $this->db->prepare("SELECT id FROM __notifications WHERE user_id=? ORDER BY `time` DESC")->execute($intUserID);

			if($objQuery){
				while ( $row = $objQuery->fetchAssoc() ) {
					$arrOut[] = $row['id'];
				}
			}

			return $arrOut;
		}

		/**
		 * Returns id for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype id
		 */
		 public function get_id($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'id');
		}

		/**
		 * Returns type for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype type
		 */
		 public function get_type($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'type');
		}

		/**
		 * Returns user_id for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype user_id
		 */
		 public function get_user_id($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'user_id');
		}

		/**
		 * Returns time for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype time
		 */
		 public function get_time($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'time');
		}

		/**
		 * Returns read for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype read
		 */
		 public function get_read($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'read');
		}

		/**
		 * Returns username for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype username
		 */
		 public function get_username($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'username');
		}

		/**
		 * Returns dataset_id for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype dataset_id
		 */
		 public function get_dataset_id($intNotificationID){
		 	return $this->objPagination->get($intNotificationID, 'dataset_id');
		}

		/**
		 * Returns additional_data for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype dataset_id
		 */
		public function get_additional_data($intNotificationID){
			return $this->objPagination->get($intNotificationID, 'additional_data');
		}

		/**
		 * Returns link for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype link
		 */
		public function get_link($intNotificationID){
			$strLink = $this->objPagination->get($intNotificationID, 'link');

			if ($strLink && $strLink != ""){
				$strLink = str_replace(array('{SERVER_PATH}', '{CONTROLLER_PATH_PLAIN}', '{CONTROLLER_PATH}','{SID}', '{ROOT_PATH}'), array('', $this->controller_path_plain, $this->controller_path, $this->SID, ''), $strLink);
				return $strLink;
			}
			return false;
		}

	}//end class
}//end if
