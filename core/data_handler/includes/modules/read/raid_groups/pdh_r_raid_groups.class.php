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

if ( !class_exists( "pdh_r_raid_groups" ) ){
	class pdh_r_raid_groups extends pdh_r_generic{

		public $default_lang = 'english';
		public $raid_groups;
		public $raid_standard_group;

		public $hooks = array(
			'raid_groups_update',
		);

		public function reset(){
			$this->raid_groups = NULL;
			$this->raid_standard_group = NULL;
		}

		public function init(){
			$this->raid_groups = array();
			
			$objQuery = $this->db->query("SELECT * FROM __groups_raid ORDER BY groups_raid_sortid ASC, groups_raid_id ASC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->raid_groups[$row['groups_raid_id']]['id']		= $row['groups_raid_id'];
					$this->raid_groups[$row['groups_raid_id']]['name']		= $row['groups_raid_name'];
					$this->raid_groups[$row['groups_raid_id']]['desc']		= $row['groups_raid_desc'];
					$this->raid_groups[$row['groups_raid_id']]['system']	= $row['groups_raid_system'];
					$this->raid_groups[$row['groups_raid_id']]['default']	= $row['groups_raid_default'];
					$this->raid_groups[$row['groups_raid_id']]['sortid']	= $row['groups_raid_sortid'];
					$this->raid_groups[$row['groups_raid_id']]['color']		= $row['groups_raid_color'];
					
					if ($row['groups_raid_default'] == 1){
						$this->raid_standard_group = $row['groups_raid_id'];
					}
				}
			}
		}

		public function get_id_list(){
			return array_keys($this->raid_groups);
		}

		public function get_data($groups_raid_id){
			return $this->raid_groups[$groups_raid_id];
		}

		public function get_name($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['name'];
		}

		public function get_color($groups_raid_id){
			return (isset($this->raid_groups[$groups_raid_id]['color'])) ? $this->raid_groups[$groups_raid_id]['color'] : '#000000';
		}

		public function get_desc($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['desc'];
		}

		public function get_deletable($groups_raid_id){
			return ($this->raid_groups[$groups_raid_id]['system'] > 0) ? false : true;
		}

		public function get_standard($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['default'];
		}

		public function get_sortid($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['sortid'];
		}

		public function get_system($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['system'];
		}

		public function get_standard_group(){
			if ($this->raid_standard_group){
				return $this->raid_standard_group;
			} else {
				return 1;
			}
		}

		public function get_groups_enabled(){
			return (is_array($this->raid_groups) && count($this->raid_groups) > 1) ? true : false;
		}
	}//end class
}//end if
?>