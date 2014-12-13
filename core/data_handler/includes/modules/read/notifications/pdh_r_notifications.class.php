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
			
	public $presets = array(
		'notifications_id' => array('id', array('%intNotificationID%'), array()),
		'notifications_type' => array('type', array('%intNotificationID%'), array()),
		'notifications_user_id' => array('user_id', array('%intNotificationID%'), array()),
		'notifications_time' => array('time', array('%intNotificationID%'), array()),
		'notifications_read' => array('read', array('%intNotificationID%'), array()),
		'notifications_dataset_id' => array('dataset_id', array('%intNotificationID%'), array()),
	);
				
	public function reset(){
			$this->pdc->del('pdh_notifications_table');
			$this->user_notifications = NULL;
			$this->notifications = NULL;
	}
					
	public function init(){
			$this->notifications		= $this->pdc->get('pdh_notifications_table');
			$this->user_notifications	= $this->pdc->get('pdh_user_notifications_table');
					
			if($this->notifications !== NULL){
				return true;
			}		

			$this->notifications = $this->user_notifications = array();
			
			$objQuery = $this->db->query('SELECT * FROM __notifications ORDER BY time DESC');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->notifications[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'type'				=> $drow['type'],
						'user_id'			=> (int)$drow['user_id'],
						'time'				=> (int)$drow['time'],
						'read'				=> (int)$drow['read'],
						'username'			=> $drow['username'],
						'dataset_id'		=> $drow['dataset_id'],
						'link'				=> $drow['link'],
						'additional_data'	=> $drow['additional_data'],
					);
					if (!isset($this->user_notifications[(int)$drow['user_id']])) $this->user_notifications[(int)$drow['user_id']] = array();
					$this->user_notifications[(int)$drow['user_id']][] = (int)$drow['id'];
				}
				
				$this->pdc->put('pdh_notifications_table', $this->notifications, null);
				$this->pdc->put('pdh_user_notifications_table', $this->user_notifications, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if ($this->notifications === null) return array();
			return array_keys($this->notifications);
		}
		
		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */				
		public function get_data($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID];
			}
			return false;
		}
		
		public function get_notifications_for_user($intUserID){
			$arrOut = array();
			if (isset($this->user_notifications[$intUserID])){
				foreach($this->user_notifications[$intUserID] as $intNotificationID){
					if (!$this->get_read($intNotificationID)) $arrOut[] = $intNotificationID;
				}
			}
			
			return $arrOut;
		}
		
		public function get_all_notifications_for_user($intUserID){
			if (isset($this->user_notifications[$intUserID])){
				return $this->user_notifications[$intUserID];
			}
			
			return array();
		}
				
		/**
		 * Returns id for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype id
		 */
		 public function get_id($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['id'];
			}
			return false;
		}

		/**
		 * Returns type for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype type
		 */
		 public function get_type($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['type'];
			}
			return false;
		}

		/**
		 * Returns user_id for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype user_id
		 */
		 public function get_user_id($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['user_id'];
			}
			return false;
		}

		/**
		 * Returns time for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype time
		 */
		 public function get_time($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['time'];
			}
			return false;
		}

		/**
		 * Returns read for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype read
		 */
		 public function get_read($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['read'];
			}
			return false;
		}

		/**
		 * Returns username for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype username
		 */
		 public function get_username($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['username'];
			}
			return false;
		}

		/**
		 * Returns dataset_id for $intNotificationID				
		 * @param integer $intNotificationID
		 * @return multitype dataset_id
		 */
		 public function get_dataset_id($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['dataset_id'];
			}
			return false;
		}
		
		/**
		 * Returns additional_data for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype dataset_id
		 */
		public function get_additional_data($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				return $this->notifications[$intNotificationID]['additional_data'];
			}
			return false;
		}
		
		/**
		 * Returns link for $intNotificationID
		 * @param integer $intNotificationID
		 * @return multitype link
		 */
		public function get_link($intNotificationID){
			if (isset($this->notifications[$intNotificationID])){
				$strLink = $this->notifications[$intNotificationID]['link'];
				$strLink = str_replace(array('{SERVER_PATH}', '{CONTROLLER_PATH_PLAIN}', '{CONTROLLER_PATH}','{SID}'), array($this->server_path, $this->controller_path_plain, $this->controller_path, $this->SID), $strLink);
				
				return $strLink;
			}
			return false;
		}

	}//end class
}//end if
?>