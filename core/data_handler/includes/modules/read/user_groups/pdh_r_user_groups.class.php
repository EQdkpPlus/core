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

if ( !class_exists( "pdh_r_user_groups" ) ){
	class pdh_r_user_groups extends pdh_r_generic{

		public $default_lang = 'english';
		public $user_groups;
		public $user_standard_group;

		public $hooks = array(
			'user_groups_update',
		);

		public function reset(){
			$this->user_groups = NULL;
			$this->user_standard_group = NULL;
		}

		public function init(){
			$this->user_groups = array();
			
			$objQuery = $this->db->query("SELECT * FROM __groups_user ORDER BY groups_user_sortid ASC, groups_user_id ASC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->user_groups[$row['groups_user_id']]['id']		= (int)$row['groups_user_id'];
					$this->user_groups[$row['groups_user_id']]['name']		= $row['groups_user_name'];
					$this->user_groups[$row['groups_user_id']]['desc']		= $row['groups_user_desc'];
					$this->user_groups[$row['groups_user_id']]['deletable']	= (int)$row['groups_user_deletable'];
					$this->user_groups[$row['groups_user_id']]['default']	= (int)$row['groups_user_default'];
					$this->user_groups[$row['groups_user_id']]['hide']		= (int)$row['groups_user_hide'];
					$this->user_groups[$row['groups_user_id']]['sortid']	= (int)$row['groups_user_sortid'];
					$this->user_groups[$row['groups_user_id']]['team']		= (int)$row['groups_user_team'];
					if ($row['groups_user_default'] == 1){
						$this->user_standard_group = $row['groups_user_id'];
					}
				}
			}
		}

		public function get_id_list($hide = false){
			if (!$hide){
				return array_keys($this->user_groups);
			} else {
				foreach ($this->user_groups as $key=>$value){
					if ($value['hide'] != 1){
						$out[$key] = $key;
					}
				}
				return $out;
			}
		}

		public function get_data($groups_user_id){
			return $this->user_groups[$groups_user_id];
		}

		public function get_name($groups_user_id){
			return $this->user_groups[$groups_user_id]['name'];
		}

		public function get_desc($groups_user_id){
			return $this->user_groups[$groups_user_id]['desc'];
		}

		public function get_deletable($groups_user_id){
			return $this->user_groups[$groups_user_id]['deletable'];
		}

		public function get_standard($groups_user_id){
			return $this->user_groups[$groups_user_id]['default'];
		}

		public function get_hide($groups_user_id){
			return $this->user_groups[$groups_user_id]['hide'];
		}
		
		public function get_team($groups_user_id){
			return $this->user_groups[$groups_user_id]['team'];
		}
		
		public function get_sortid($groups_user_id){
			return $this->user_groups[$groups_user_id]['sortid'];
		}

		public function get_standard_group(){
			if ($this->user_standard_group){
				return $this->user_standard_group;
			} else {
				return 4;
			}
		}
		
		public function get_team_groups(){
			$out = array();
			foreach ($this->user_groups as $key=>$value){
				if ($value['team'] == 1){
					$out[$key] = $key;
				}
			}
			return $out;
		}
	}//end class
}//end if
?>