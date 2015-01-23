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

if ( !class_exists( "pdh_r_user_groups_users" ) ){
	class pdh_r_user_groups_users extends pdh_r_generic{

		public $default_lang = 'english';
		public $user_groups_users;
		public $user_memberships;

		public $hooks = array(
			'user_groups_update',
		);

		public function reset(){
			$this->user_groups_users = NULL;
			$this->user_memberships = NULL;
		}

		public function init(){
			$this->user_groups_users = array();
			
			$objQuery = $this->db->query("SELECT gu.* FROM __groups_users gu, __users u WHERE u.user_id = gu.user_id ORDER BY u.username ASC");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->user_groups_users[$row['group_id']][$row['user_id']] = $row['user_id'];
					//0 = regular member, 1 = group leader
					$this->user_memberships[$row['user_id']][$row['group_id']] = (intval($row['grpleader'])) ? 1 : 0;
				}
			}
		}

		public function get_user_list($group_id){
			if(is_array($group_id)){
				$tmparray = array();
				foreach($group_id as $groupid){
					if(is_array($this->user_groups_users[$groupid])){
						$tmparray = array_merge($tmparray, array_keys($this->user_groups_users[$groupid]));
					}
				}
				return array_unique($tmparray);
			}else{
				return (isset($this->user_groups_users[$group_id]) && is_array($this->user_groups_users[$group_id])) ? array_keys($this->user_groups_users[$group_id]) : array();
			}
		}
		
		public function get_is_grpleader($user_id, $group_id){
			if (isset($this->user_memberships[$user_id][$group_id]) && $this->user_memberships[$user_id][$group_id] == 1) return true;
			return false;
		}

		public function get_memberships($user_id){
			if (isset($this->user_memberships[$user_id]) && is_array($this->user_memberships[$user_id])){
				return array_keys($this->user_memberships[$user_id]);
			} elseif ($user_id == ANONYMOUS) {
				return array(0 => 0);
			} else {
				return array();
			}
		}

		public function get_memberships_status($user_id){
			if (is_array($this->user_memberships[$user_id])){
				return $this->user_memberships[$user_id];
			} else {
				return array();
			}
		}

		public function get_groupcount($group_id){
			return ((isset($this->user_groups_users[$group_id])) ? count($this->user_groups_users[$group_id]) : 0);
		}
	}//end class
}//end if
?>