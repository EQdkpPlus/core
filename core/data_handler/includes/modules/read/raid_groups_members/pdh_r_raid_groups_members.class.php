<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2013
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_raid_groups_members" ) ){
	class pdh_r_raid_groups_members extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $raid_groups_members;
		public $raid_memberships;

		public $hooks = array(
			'raid_groups_update',
		);

		public function reset(){
			$this->raid_groups_members	= NULL;
			$this->raid_memberships		= NULL;
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

		public function get_member_list($group_id){
			if(is_array($group_id)){
				$tmparray = array();
				foreach($group_id as $groupid){
					if(is_array($this->raid_groups_members[$groupid])){
						$tmparray = array_merge($tmparray, array_keys($this->raid_groups_members[$groupid]));
					}
				}
				return array_unique($tmparray);
			}else{
				return (isset($this->raid_groups_members[$group_id]) && is_array($this->raid_groups_members[$group_id])) ? array_keys($this->raid_groups_members[$group_id]) : array();
			}
		}
		
		public function get_is_grpleader($member_id, $group_id){
			if (isset($this->raid_memberships[$member_id][$group_id]) && $this->raid_memberships[$member_id][$group_id] == 1) return true;
			return false;
		}

		public function get_memberships($member_id){
			if (isset($this->raid_memberships[$member_id]) && is_array($this->raid_memberships[$member_id])){
				return array_keys($this->raid_memberships[$member_id]);
			} elseif ($member_id == ANONYMOUS) {
				return array(0 => 0);
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

		public function get_groupcount($group_id){
			return ((isset($this->raid_groups_members[$group_id])) ? count($this->raid_groups_members[$group_id]) : 0);
		}
	}//end class
}//end if
?>