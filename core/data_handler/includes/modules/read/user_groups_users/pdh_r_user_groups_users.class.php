<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

if ( !class_exists( "pdh_r_user_groups_users" ) ){
	class pdh_r_user_groups_users extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
			$sql = "SELECT gu.* FROM __groups_users gu, __users u WHERE u.user_id = gu.user_id ORDER BY u.username ASC";
			$r_result = $this->db->query($sql);

			while( $row = $this->db->fetch_record($r_result) ){
				$this->user_groups_users[$row['group_id']][$row['user_id']] = $row['user_id'];
				$this->user_memberships[$row['user_id']][$row['group_id']] = 1;
			}
			$this->db->free_result($r_result);
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

		public function get_memberships($user_id){
			if (isset($this->user_memberships[$user_id]) && is_array($this->user_memberships[$user_id])){
				return array_keys($this->user_memberships[$user_id]);
			} elseif ($user_id == ANONYMOUS) {
				return array(0 => 1);
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_user_groups_users', pdh_r_user_groups_users::__shortcuts());
?>