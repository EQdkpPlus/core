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
				
if ( !class_exists( "pdh_r_notification_types" ) ) {
	class pdh_r_notification_types extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}				
	
	public $default_lang = 'english';
	public $notification_types = null;

	public $hooks = array(
		'notification_types_update',
	);		
			
	public $presets = array(
		'notification_types_id' => array('id', array('%intNotificationTypeID%'), array()),
		'notification_types_name' => array('name', array('%intNotificationTypeID%'), array()),
		'notification_types_category' => array('category', array('%intNotificationTypeID%'), array()),
		'notification_types_link' => array('link', array('%intNotificationTypeID%'), array()),
		'notification_types_params' => array('params', array('%intNotificationTypeID%'), array()),
		'notification_types_prio' => array('prio', array('%intNotificationTypeID%'), array()),
		'notification_types_default' => array('default', array('%intNotificationTypeID%'), array()),
		'notification_types_group' => array('group', array('%intNotificationTypeID%'), array()),
		'notification_types_group_name' => array('group_name', array('%intNotificationTypeID%'), array()),
		'notification_types_group_at' => array('group_at', array('%intNotificationTypeID%'), array()),
	);
				
	public function reset(){
			$this->pdc->del('pdh_notification_types_table');
			
			$this->notification_types = NULL;
	}
					
	public function init(){
			$this->notification_types	= $this->pdc->get('pdh_notification_types_table');				
					
			if($this->notification_types !== NULL){
				return true;
			}		

			$objQuery = $this->db->query('SELECT * FROM __notification_types');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->notification_types[$drow['id']] = array(
						'id'				=> $drow['id'],
						'name'				=> $drow['name'],
						'category'			=> $drow['category'],
						'prio'				=> (int)$drow['prio'],
						'default'			=> (int)$drow['default'],
						'group'				=> (int)$drow['group'],
						'group_name'		=> $drow['group_name'],
						'group_at'			=> (int)$drow['group_at'],
						'icon'				=> $drow['icon'],
					);
				}
				
				$this->pdc->put('pdh_notification_types_table', $this->notification_types, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */				
		public function get_id_list(){
			if ($this->notification_types === null) return array();
			return array_keys($this->notification_types);
		}
		
		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */				
		public function get_data($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID];
			}
			return false;
		}
				
		/**
		 * Returns id for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype id
		 */
		 public function get_id($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['id'];
			}
			return false;
		}

		/**
		 * Returns name for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype name
		 */
		 public function get_name($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['name'];
			}
			return false;
		}

		/**
		 * Returns category for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype category
		 */
		 public function get_category($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['category'];
			}
			return false;
		}

		/**
		 * Returns prio for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype prio
		 */
		 public function get_prio($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['prio'];
			}
			return false;
		}

		/**
		 * Returns default for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype default
		 */
		 public function get_default($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['default'];
			}
			return false;
		}

		/**
		 * Returns group for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype group
		 */
		 public function get_group($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['group'];
			}
			return false;
		}

		/**
		 * Returns group_name for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype group_name
		 */
		 public function get_group_name($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['group_name'];
			}
			return false;
		}

		/**
		 * Returns group_at for $strNotificationTypeID				
		 * @param integer $strNotificationTypeID
		 * @return multitype group_at
		 */
		 public function get_group_at($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['group_at'];
			}
			return false;
		}
		
		/**
		 * Returns icon for $strNotificationTypeID
		 * @param integer $strNotificationTypeID
		 * @return multitype icon
		 */
		public function get_icon($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return $this->notification_types[$strNotificationTypeID]['icon'];
			}
			return false;
		}
		
		/**
		 * Checks wheater a notification type exists or not
		 * 
		 * @param string $strNotificationTypeID
		 * @return boolean true if type exists, false if type not exists
		 */
		public function get_check_existing_type($strNotificationTypeID){
			if (isset($this->notification_types[$strNotificationTypeID])){
				return true;
			}
			return false;
		}

	}//end class
}//end if
?>