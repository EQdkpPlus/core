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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_calendar_raids_guests')){
	class pdh_r_calendar_raids_guests extends pdh_r_generic{

		private $guests;
		public $hooks = array(
			'guests_update',
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_calendar_raids_table.guests');
			$this->pdc->del_prefix('plugin.guests');
			$this->guests = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->guests		= $this->pdc->get('pdh_calendar_raids_table.guests');
			if($this->guests !== NULL){
				return true;
			}

			// empty array as default
			$this->guests	= array();
			
			$objQuery = $this->db->query('SELECT * FROM __calendar_raid_guests;');
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->guests[$row['calendar_events_id']][$row['id']] = array(
						'name'				=> $row['name'],
						'note'				=> $row['note'],
						'timestamp_signup'	=> $row['timestamp_signup'],
						'raidgroup'			=> $row['raidgroup'],
						'class'				=> $row['class'],
					);
				}
				$this->pdc->put('pdh_calendar_raids_table.guests', $this->guests, NULL);
			}
	
			return true;
		}

		public function get_members($id=''){
			$output = ($id) ? ((isset($this->guests[$id])) ? $this->guests[$id] : '') : $this->guests;
			return (is_array($output)) ? $output : array();
		}

		public function get_guest($id){
			foreach($this->guests as $gdata){
				if(is_array($gdata[$id])){
						return $gdata[$id];
				}
			}
		}

		public function get_class($id){
			return $this->guests[$id]['class'];
		}

		public function get_note($id){
			return $this->guests[$id]['note'];
		}

		public function get_group($id){
			return $this->guests[$id]['group'];
		}

		// not working. must have a look another day
		public function get_count($raidid){
			if(isset($this->guests[$id]) && is_array($this->guests[$id])){
				/*foreach($this->guests[$id]){

				}*/
				return count($this->guests[$id]);
			}else{
				return 0;
			}
		}

	} //end class
} //end if class not exists
?>