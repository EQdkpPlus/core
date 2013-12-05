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

if ( !class_exists( "pdh_r_raid_groups_users" ) ){
	class pdh_r_raid_groups_users extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $raid_groups_users;
		public $raid_memberships;

		public $hooks = array(
			'raid_groups_update',
		);

		public function reset(){
			$this->raid_groups_users	= NULL;
			$this->raid_memberships	= NULL;
		}

		public function init(){
			$this->raid_groups_users = array();

			$objQuery = $this->db->query("SELECT gu.* FROM __groups_raid_users gu, __users u WHERE u.user_id = gu.user_id ORDER BY u.username ASC");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->raid_groups_users[$row['group_id']][$row['user_id']] = $row['user_id'];
					//0 = regular member, 1 = group leader
					$this->raid_memberships[$row['user_id']][$row['group_id']] = (intval($row['grpleader'])) ? 1 : 0;
				}
			}
		}

		public function get_user_list($group_id){
			if(is_array($group_id)){
				$tmparray = array();
				foreach($group_id as $groupid){
					if(is_array($this->raid_groups_users[$groupid])){
						$tmparray = array_merge($tmparray, array_keys($this->raid_groups_users[$groupid]));
					}
				}
				return array_unique($tmparray);
			}else{
				return (isset($this->raid_groups_users[$group_id]) && is_array($this->raid_groups_users[$group_id])) ? array_keys($this->raid_groups_users[$group_id]) : array();
			}
		}
		
		public function get_is_grpleader($user_id, $group_id){
			if (isset($this->raid_memberships[$user_id][$group_id]) && $this->raid_memberships[$user_id][$group_id] == 1) return true;
			return false;
		}

		public function get_memberships($user_id){
			if (isset($this->raid_memberships[$user_id]) && is_array($this->raid_memberships[$user_id])){
				return array_keys($this->raid_memberships[$user_id]);
			} elseif ($user_id == ANONYMOUS) {
				return array(0 => 0);
			} else {
				return array();
			}
		}

		public function get_memberships_status($user_id){
			if (is_array($this->raid_memberships[$user_id])){
				return $this->raid_memberships[$user_id];
			} else {
				return array();
			}
		}

		public function get_groupcount($group_id){
			return ((isset($this->raid_groups_users[$group_id])) ? count($this->raid_groups_users[$group_id]) : 0);
		}
	}//end class
}//end if
?>