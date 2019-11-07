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

if ( !class_exists( "pdh_r_raid_groups_members" ) ){
	class pdh_r_raid_groups_members extends pdh_r_generic{

		public $default_lang = 'english';
		public $raid_groups_members;
		public $raid_memberships;
		public $raid_groups_charselection;

		public $hooks = array(
			'raid_groups_update',
		);

		public function reset(){
			$this->raid_groups_members					= NULL;
			$this->raid_memberships						= NULL;
			$this->raid_groups_charselection			= NULL;
		}

		public function init(){
			$this->raid_groups_members = array();

			$objQuery = $this->db->query("SELECT gm.* FROM __groups_raid_members gm, __members m WHERE m.member_id = gm.member_id ORDER BY m.member_name ASC");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->raid_groups_members[$row['group_id']][$row['member_id']] = $row['member_id'];
					//0 = regular member, 1 = group leader
					$this->raid_memberships[$row['member_id']][$row['group_id']] = (intval($row['grpleader'])) ? 1 : 0;
				}
			}
		}

		public function get_oneCharByUser($group_id){
			$tmp_raid_groups_members	= $this->raid_groups_charselection = array();
			if(is_array($this->raid_groups_members[$group_id]) && count($this->raid_groups_members[$group_id]) > 0){
				$tmp_raid_groups_members = $this->raid_groups_members[$group_id];
				foreach($tmp_raid_groups_members as $memberid){

					// if the mainchar is in the raid group, unset all other chars of this user
					$mainchar_id	= $this->pdh->get('member', 'mainid', array($memberid));
					if(array_search($mainchar_id, $tmp_raid_groups_members)){
						$this->raid_groups_charselection[$mainchar_id] = 'mainchar';
						$other_chars	= $this->pdh->get('member', 'other_members', array($mainchar_id));
						foreach($other_chars as $othercharIds){
							unset($tmp_raid_groups_members[$othercharIds]);
						}

					// if there is no mainchar, use the first char and unset the rest
					}else{
						$this->raid_groups_charselection[$mainchar_id] = 'attendance';
						$attending_char		= $this->pdh->get('calendar_raids_attendees', 'twinks_with_highest_attendance', array($memberid));
						$other_chars		= $this->pdh->get('member', 'other_members', array($attending_char));
						foreach($other_chars as $othercharIds){
							unset($tmp_raid_groups_members[$othercharIds]);
						}
					}
				}
			}
			return $tmp_raid_groups_members;
		}

		public function get_userOfGroups($group_id){
			if(is_array($group_id)){
				$users_of_group	= array();
				foreach($group_id as $rgroupid){
					$raid_groups_members	= $this->get_member_list($rgroupid, true);
					$users_of_group = array_merge($users_of_group, $this->pdh->get('member', 'userid', array($raid_groups_members)));
				}
			}else{
				$raid_groups_members	= $this->get_member_list($group_id, true);
				$users_of_group			= $this->pdh->get('member', 'userid', array($raid_groups_members));
			}
			return $users_of_group;
		}

		public function get_charSelectionMethod($memberid){
			return (isset($this->raid_groups_charselection[$memberid])) ? $this->raid_groups_charselection[$memberid] : '';
		}

		public function get_member_list($group_id, $onecharperuser=false){
			if(is_array($group_id)){
				$tmparray = array();
				foreach($group_id as $groupid){
					$arr_chars	= ($onecharperuser) ? $this->get_oneCharByUser($groupid) : $this->raid_groups_members[$groupid];
					if(is_array($arr_chars)){
						$tmparray = array_merge($tmparray, array_keys($arr_chars));
					}
				}
				return array_unique($tmparray);
			}else{
				$arr_chars	= ($onecharperuser) ? $this->get_oneCharByUser($group_id) : $this->raid_groups_members[$group_id];
				return (isset($arr_chars) && is_array($arr_chars)) ? array_keys($arr_chars) : array();
			}
		}

		public function get_is_grpleader($member_id, $group_id){
			if (isset($this->raid_memberships[$member_id][$group_id]) && $this->raid_memberships[$member_id][$group_id] == 1) return true;
			return false;
		}

		public function get_user_is_grpleader($user_id, $group_id){
			$arrMembers = $this->pdh->get('member', 'connection_id', array($user_id));
			if (is_array($arrMembers)){
				foreach($arrMembers as $member_id){
					if ($this->get_is_grpleader($member_id, $group_id)) return true;
				}
			}
			return false;
		}

		public function get_user_has_grpleaders($user_id){
			$arrMembers = $this->pdh->get('member', 'connection_id', array($user_id));
			if (is_array($arrMembers)){
				$arrRaidGroups = $this->pdh->get('raid_groups', 'id_list');

				foreach($arrRaidGroups as $raidgroup_id){
					foreach($arrMembers as $member_id){
						if ($this->get_is_grpleader($member_id, $raidgroup_id)) return true;
					}
				}
			}
			return false;
		}

		public function get_memberships($member_id){
			if (isset($this->raid_memberships[$member_id]) && is_array($this->raid_memberships[$member_id])){
				return array_keys($this->raid_memberships[$member_id]);
			} elseif ($member_id == ANONYMOUS) {
				return array(0 => -1);
			} else {
				return array();
			}
		}

		public function get_memberships_status($member_id){
			if (is_array($this->raid_memberships[$member_id])){
				return $this->raid_memberships[$member_id];
			} else {
				return array();
			}
		}

		public function get_membership_status($member_id, $group_id){
			if(is_array($group_id)){
				$group_id	= $group_id[0];
			}
			if (isset($this->raid_memberships[$member_id]) && isset($this->raid_memberships[$member_id][$group_id])){
				if(is_array($this->raid_memberships[$member_id][$group_id])){
					$statusout = array();
					foreach($group_id as $tmpgroupid){
						if($this->raid_memberships[$member_id][$tmpgroupid] == 1){
							return 1;
						}elseif($this->raid_memberships[$member_id][$tmpgroupid] == 0){
							return 0;
						}
					}
					return -1;
				}else{
					return (isset($this->raid_memberships[$member_id][$group_id])) ? $this->raid_memberships[$member_id][$group_id] : -1;
				}
			} else {
				return -1;
			}
		}
		public function get_check_user_is_in_groups($user_id, $group_id){
			if(is_array($group_id)){
				foreach($group_id as $groups){
					$output = $this->get_user_is_in_groups($user_id, $groups);
					if($output) { return true; }
				}
			}else{
				$output = $this->get_user_is_in_groups($user_id, $group_id);
			}
			return $output;
		}

		public function get_user_is_in_groups($user_id, $group_id){
			$arrMembers = $this->pdh->get('member', 'connection_id', array($user_id));
			if (is_array($arrMembers)){
				foreach($arrMembers as $member_id){
					if ($this->get_membership_status($member_id, $group_id) != -1){
						return true;
					}
				}
			}
			return false;
		}

		public function get_groupcount($group_id){
			return ((isset($this->raid_groups_members[$group_id])) ? count($this->raid_groups_members[$group_id]) : 0);
		}
	}//end class
}//end if
