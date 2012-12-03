<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_user_groups_users')) {
	class pdh_w_user_groups_users extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'user'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_user_to_group($user_id, $group_id) {
			$arrSet = array(
				'group_id' => $group_id,
				'user_id'  => $user_id,
			);

			if(!$this->db->query("INSERT INTO __groups_users :params", $arrSet)) {
				return false;
			}

			$this->pdh->enqueue_hook('user_groups_update');
			return true;
		}

		public function add_user_to_groups($user_id, $group_array) {
			if (is_array($group_array)) {
				$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));

				foreach($group_array as $key=>$group) {
					$group = intval($group);
					if (!(($group == 2 && !isset($memberships[2])) || $group == 0)) {					
						if(!$this->add_user_to_group($user_id, $group)) {
							return false;
						}
					}
				}
				return true;
			} else {
				return false;
			}
		}

		public function add_users_to_group($user_array, $group_id) {
			if (is_array($user_array)) {
				foreach($user_array as $key=>$user){
					if(!$this->add_user_to_group($user, $group_id)) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}

		public function delete_user_from_group($user_id, $group_id) {
			if($this->db->query("DELETE FROM __groups_users WHERE group_id = ".$this->db->escape($group_id)." AND user_id = ".$this->db->escape($user_id).";")) {
				$this->pdh->enqueue_hook('user_groups_update');
				return true;
			}
			return false;
		}

		public function delete_users_from_group($user_array, $group_id) {
			if (is_array($user_array)) {
				$user = implode(",", $user_array);
				$this->db->query("DELETE FROM __groups_users WHERE group_id = '".$this->db->escape($group_id)."' AND user_id IN (".$this->db->escape($user).");");
			} else {
				return false;
			}
		}

		public function delete_all_user_from_group($group_id) {
			$this->db->query("DELETE FROM __groups_users WHERE group_id = '".$this->db->escape($group_id)."';");			
			return true;
		}

		public function delete_user_from_groups($user_id, $group_array) {
			$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));
			if (is_array($group_array)) {
				foreach($group_array as $key=>$group) {
					if (!($group == 2 && (!isset($memberships[2]) || $this->user->data['user_id'] == $user_id))) {
						$this->db->query("DELETE FROM __groups_users WHERE group_id = '".$this->db->escape($group)."' AND user_id = '".$this->db->escape($user_id)."';");
					}
					
				}
			} else {
				return false;
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_user_groups_users', pdh_w_user_groups_users::__shortcuts());
?>