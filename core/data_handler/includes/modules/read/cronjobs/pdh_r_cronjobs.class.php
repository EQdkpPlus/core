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

if ( !class_exists( "pdh_r_cronjobs" ) ) {
	class pdh_r_cronjobs extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $cronjobs = null;

	public $hooks = array(
		'cronjobs_update',
	);

	public $presets = array(
		'cronjobs_id' => array('id', array('%cronjobID%'), array()),
		'cronjobs_start_time' => array('start_time', array('%cronjobID%'), array()),
		'cronjobs_repeat' => array('repeat', array('%cronjobID%'), array()),
		'cronjobs_repeat_type' => array('repeat_type', array('%cronjobID%'), array()),
		'cronjobs_repeat_interval' => array('repeat_interval', array('%cronjobID%'), array()),
		'cronjobs_extern' => array('extern', array('%cronjobID%'), array()),
		'cronjobs_ajax' => array('ajax', array('%cronjobID%'), array()),
		'cronjobs_delay' => array('delay', array('%cronjobID%'), array()),
		'cronjobs_multiple' => array('multiple', array('%cronjobID%'), array()),
		'cronjobs_active' => array('active', array('%cronjobID%'), array()),
		'cronjobs_editable' => array('editable', array('%cronjobID%'), array()),
		'cronjobs_path' => array('path', array('%cronjobID%'), array()),
		'cronjobs_params' => array('params', array('%cronjobID%'), array()),
		'cronjobs_description' => array('description', array('%cronjobID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_cronjobs_table');

			$this->cronjobs = NULL;
	}

	public function init(){
			$this->cronjobs	= $this->pdc->get('pdh_cronjobs_table');

			if($this->cronjobs !== NULL && is_array($this->cronjobs) && count($this->cronjobs) > 0){
				return true;
			}

			$cronjobs = array();
			
			$objQuery = $this->db->query('SELECT * FROM __cronjobs ORDER BY id ASC');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$cronjobs[$drow['id']] = array(
						'id'				=> $drow['id'],
						'start_time'		=> (int)$drow['start_time'],
						'repeat'			=> (int)$drow['repeat'],
						'repeat_type'		=> $drow['repeat_type'],
						'repeat_interval'	=> (int)$drow['repeat_interval'],
						'extern'			=> (int)$drow['extern'],
						'ajax'				=> (int)$drow['ajax'],
						'delay'				=> (int)$drow['delay'],
						'multiple'			=> (int)$drow['multiple'],
						'active'			=> (int)$drow['active'],
						'editable'			=> (int)$drow['editable'],
						'path'				=> $drow['path'],
						'params'			=> ($drow['params'] != "") ? unserialize_noclasses($drow['params']) : array(),
						'description'		=> $drow['description'],
						'last_run'			=> (int)$drow['last_run'],
						'next_run'			=> (int)$drow['next_run'],

					);
				}

				$this->pdc->put('pdh_cronjobs_table', $cronjobs, null);
				$this->cronjobs = $cronjobs;
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->cronjobs === null) return array();
			return array_keys($this->cronjobs);
		}

		public function get_crontab(){
			return (is_array($this->cronjobs)) ? $this->cronjobs : array();
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID];
			}
			return false;
		}

		/**
		 * Returns id for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype id
		 */
		 public function get_id($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['id'];
			}
			return false;
		}

		/**
		 * Returns start_time for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype start_time
		 */
		 public function get_start_time($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['start_time'];
			}
			return false;
		}

		/**
		 * Returns repeat for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype repeat
		 */
		 public function get_repeat($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['repeat'];
			}
			return false;
		}

		/**
		 * Returns repeat_type for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype repeat_type
		 */
		 public function get_repeat_type($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['repeat_type'];
			}
			return false;
		}

		/**
		 * Returns repeat_interval for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype repeat_interval
		 */
		 public function get_repeat_interval($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['repeat_interval'];
			}
			return false;
		}

		/**
		 * Returns extern for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype extern
		 */
		 public function get_extern($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['extern'];
			}
			return false;
		}

		/**
		 * Returns ajax for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype ajax
		 */
		 public function get_ajax($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['ajax'];
			}
			return false;
		}

		/**
		 * Returns delay for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype delay
		 */
		 public function get_delay($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['delay'];
			}
			return false;
		}

		/**
		 * Returns multiple for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype multiple
		 */
		 public function get_multiple($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['multiple'];
			}
			return false;
		}

		/**
		 * Returns active for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype active
		 */
		 public function get_active($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['active'];
			}
			return false;
		}

		/**
		 * Returns editable for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype editable
		 */
		 public function get_editable($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['editable'];
			}
			return false;
		}

		/**
		 * Returns path for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype path
		 */
		 public function get_path($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['path'];
			}
			return false;
		}

		/**
		 * Returns params for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype params
		 */
		 public function get_params($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return  $this->cronjobs[$cronjobID]['params'];
			}
			return false;
		}

		/**
		 * Returns description for $cronjobID
		 * @param integer $cronjobID
		 * @return multitype description
		 */
		 public function get_description($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['description'];
			}
			return false;
		}

		public function get_next_run($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['next_run'];
			}
			return false;
		}

		public function get_last_run($cronjobID){
			if (isset($this->cronjobs[$cronjobID])){
				return $this->cronjobs[$cronjobID]['last_run'];
			}
			return false;
		}

	}//end class
}//end if
