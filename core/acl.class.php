<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class acl_manager extends gen_class {
	private $auth_defaults		= array();
	private $auth_ids			= array();
	private $group_permissions	= array();

	//Permissions that are only for a superadmin
	private $superadmin_only_permissions = array(
		'a_maintenance'		=> 'a_maintenance',
		'a_logs_del'		=> 'a_logs_del',
		'a_backup'			=> 'a_backup',
		'a_reset'			=> 'a_reset',
		'a_files_man'		=> 'a_files_man',
	);

	//Returns the default permissions
	public function get_auth_defaults($force_requery = false){
		if(empty($this->auth_defaults) || $force_requery){
			$sql = 'SELECT auth_id, auth_value, auth_default
					FROM __auth_options
					ORDER BY auth_id';
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ) {
				$this->auth_defaults[ $row['auth_value'] ]	= $row['auth_default'];
				$this->auth_ids[$row['auth_value']]			= $row['auth_id'];
			}
			$this->db->free_result($result);
		}
		return $this->auth_defaults;
	}

	//Return the auth_value for an auth_id
	public function get_auth_id($auth_value){
		if(isset($this->auth_ids[$auth_value])){
			return $this->auth_ids[$auth_value];
		}else{
			$this->get_auth_defaults(true);
			if(isset($this->auth_ids[$auth_value])){
				return $this->auth_ids[$auth_value];
			}
		}
		return false;
	}

	//Return all permissions of a User
	public function get_user_permissions($user_id=0, $groups=true){
		if(!isset($this->user_permissions[$user_id])){
			$this->init_user_permissions($user_id);
		}
		$tmp_user_permissions = $this->user_permissions[$user_id];
		if ($groups == true){
			foreach($this->user_group_permissions[$user_id] as $key=>$elem){
				if ($elem == "Y"){
					$tmp_user_permissions[$key] = "Y";
				}
			}
		}
		return $tmp_user_permissions;
	}

	//Returns all permissions of a Group
	public function get_group_permissions($group_id=0, $force_requery=false){
		if(!isset($this->group_permissions[$group_id]) || $force_requery){
			$this->group_permissions[$group_id] = array();
			//Grant Super-Admins all rights
			if ($group_id == 2){
				$defaults = $this->get_auth_defaults();
				foreach ($defaults as $value => $elem){
					$this->group_permissions[$group_id][$value] = "Y";
				}

			} else {

				$sql = "SELECT ao.auth_value, ag.auth_setting
						FROM __auth_groups ag, __auth_options ao
						WHERE (ag.auth_id = ao.auth_id)
						AND (ag.group_id='".$group_id."')";
				$result = $this->db->query($sql);

				while ( $row = $this->db->fetch_record($result) ){
					if ($row['auth_setting'] == 'Y'){
						$this->group_permissions[$group_id][$row['auth_value']] = $row['auth_setting'];
					}
				}
				 $this->db->free_result($result);

			}
		}
		return $this->group_permissions[$group_id];
	}

	public function update_auth_option($auth_value, $auth_default){
		$auth_id = $this->get_auth_id($auth_value);
		if ( $auth_id ){
			$sql = "UPDATE __auth_users
					SET auth_setting='$auth_default'
					WHERE auth_id='".$this->db->escape($auth_id)."'";
		}else{
			$sql = "INSERT INTO __auth_options
					(auth_value, auth_default)
					VALUES ('".$this->db->escape($auth_value)."', '".$this->db->escape($auth_default)."')";
		}
		$this->db->query($sql);
	}

	public function del_auth_option($auth_value){
		$sql = "DELETE FROM __auth_options
				WHERE auth_value='".$auth_value."'";
		$this->db->query($sql);
	}

	public function update_user_permissions($permission_array, $user_id=0){
		if ($user_id == 0){$user_id = $user->data['user_id'];}

		$perm_ids = implode("', '", array_keys($permission_array));
		$sql = "DELETE FROM __auth_users WHERE user_id='".$user_id."' AND auth_id IN ('".$perm_ids."')";
		$this->db->query($sql);

		$boolExecute = false;

		$sql = "INSERT INTO __auth_users (user_id, auth_id, auth_setting) VALUES ";
		foreach ($permission_array as $auth_id => $permission) {
			if ($permission == 'Y'){
				$boolExecute = true;
				$sql .= "('{$user_id}','{$auth_id}','{$permission}'), ";
			}
		}
		$sql = preg_replace('/, $/', '', $sql);
		if ($boolExecute) $this->db->query($sql);
	}

	//Returns the permissions that are only for the superadmin
	public function get_superadmin_only_permissions(){
		return $this->superadmin_only_permissions;
	}

	public function get_permission_boxes(){
		$group_permissions = array(
			// Events
			$this->user->lang('events') => array(
				array('CBNAME' => 'a_event_add',  'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_event_upd',  'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_event_del',  'TEXT' => $this->user->lang('delete')),
				array('CBNAME' => 'u_event_view', 'TEXT' => $this->user->lang('view'))
			),
			// Individual adjustments
			$this->user->lang('individual_adjustments') => array(
				array('CBNAME' => 'a_indivadj_add', 'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_indivadj_upd', 'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_indivadj_del', 'TEXT' => $this->user->lang('delete'))
			),
			// Items
			$this->user->lang('items') => array(
				array('CBNAME' => 'a_item_add',  'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_item_upd',  'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_item_del',  'TEXT' => $this->user->lang('delete')),
				array('CBNAME' => 'u_item_view', 'TEXT' => $this->user->lang('view'))
			),
			// News
			$this->user->lang('news') => array(
				array('CBNAME' => 'a_news_add', 'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_news_upd', 'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_news_del', 'TEXT' => $this->user->lang('delete')),
				array('CBNAME' => 'u_news_view', 'TEXT' => $this->user->lang('view')),
			),
			// Raids
			$this->user->lang('raids') => array(
				array('CBNAME' => 'a_raid_add',  'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_raid_upd',  'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_raid_del',  'TEXT' => $this->user->lang('delete')),
				array('CBNAME' => 'u_raid_view', 'TEXT' => $this->user->lang('view'))
			),

			// Calendar
			$this->user->lang('calendars') => array(
				array('CBNAME' => 'a_calendars_man',  'TEXT' => $this->user->lang('manage_calendars')),
				array('CBNAME' => 'a_cal_event_man',  'TEXT' => $this->user->lang('manage_calevents')),
				array('CBNAME' => 'a_cal_revent_conf','TEXT' => $this->user->lang('manage_revent_man')),
				array('CBNAME' => 'u_cal_event_add', 'TEXT' => $this->user->lang('add_calevents')),
				array('CBNAME' => 'u_calendar_view', 'TEXT' => $this->user->lang('view_calendar')),
			),

			// Members
			$this->user->lang('chars') => array(
				array('CBNAME' => 'a_members_man', 'TEXT' => $this->user->lang('manage')),
				array('CBNAME' => 'u_roster_list', 'TEXT' => $this->user->lang('menu_roster')),
				array('CBNAME' => 'u_member_view', 'TEXT' => $this->user->lang('listing_members')),
				
				array('CBNAME' => 'u_member_add',  'TEXT' => $this->user->lang('charsadd')),
				array('CBNAME' => 'u_member_man',	'TEXT' => $this->user->lang('charsmanage')),
				array('CBNAME' => 'u_member_del',  'TEXT' => $this->user->lang('charsdelete')),
				array('CBNAME' => 'u_member_conn', 'TEXT' => $this->user->lang('charconnect')),
			),
			// Manage
			$this->user->lang('manage') => array(
				array('CBNAME' => 'a_config_man',  'TEXT' => $this->user->lang('configuration')),
				array('CBNAME' => 'a_extensions_man', 'TEXT' => $this->user->lang('extensions')),
				array('CBNAME' => 'a_reset',   		'TEXT' => $this->user->lang('reset')),
				array('CBNAME' => 'a_maintenance',   'TEXT' => $this->user->lang('maintenance')),
				array('CBNAME' => 'a_files_man',   'TEXT' => $this->user->lang('manage_files'))
			),
			//User
			$this->user->lang('user') => array(
				array('CBNAME' => 'a_users_man',   'TEXT' => $this->user->lang('manage')),
				array('CBNAME' => 'a_users_massmail',   'TEXT' => $this->user->lang('massmail_send')),
				array('CBNAME' => 'u_userlist',   'TEXT' => $this->user->lang('view')),
				array('CBNAME' => 'u_usermailer',   'TEXT' => $this->user->lang('adduser_send_mail')),
			),

			// Logs
			$this->user->lang('logs') => array(
				array('CBNAME' => 'a_logs_view', 'TEXT' => $this->user->lang('view')),
				array('CBNAME' => 'a_logs_del', 'TEXT' => $this->user->lang('delete'))
			),
			// Backup Database
			$this->user->lang('backup') => array(
				array('CBNAME' => 'a_backup', 'TEXT' => $this->user->lang('backup_database'))
			),
			// Pages
			$this->user->lang('info') => array(
				array('CBNAME' => 'a_pages_man', 'TEXT' => $this->user->lang('manage')),
			),
			 // SMS
			$this->user->lang('sms_perm') => array(
					array('CBNAME' => 'a_sms_send', 'TEXT' => $this->user->lang('sms_perm2')),
			),
			 // Search
			$this->user->lang('search') => array(
					array('CBNAME' => 'u_search', 'TEXT' => $this->user->lang('search')),
			),
		);
		return $group_permissions;
	}
}
class acl extends acl_manager {	
	public static $shortcuts = array('db', 'user');
	public $user_permissions = array();
	public $user_group_memberships = array();
	public $user_group_permissions = array();

	//Inits the userpermissions, group-memberships and group-permissions of a user
	public function init_user_permissions($user_id){
		if(!isset($this->user_permissions[$user_id])){
			$this->user_permissions[$user_id] = array();
			$this->user_group_memberships[$user_id] = array();
			$this->user_group_permissions[$user_id] = array();

			if ( $user_id != ANONYMOUS ){

				//First Step: get Group memberships
				$result =  $this->db->query("SELECT group_id FROM __groups_users WHERE user_id='".$user_id."'");
				while ( $row = $this->db->fetch_record($result) ){
					$this->user_group_memberships[$user_id][$row['group_id']] = 1;
				}
				$this->db->free_result($result);

				//If user is Superadmin, he has all permissions
				if (isset($this->user_group_memberships[$user_id][2])){
					foreach ($this->get_auth_defaults() as $value => $default){
						$this->user_group_permissions[$user_id][$value] = "Y";
					}
					//If not superadmin: get user- and grouppermissions
				} else {
					//User-Permissions
					$sql = "SELECT ao.auth_value, au.auth_setting
							FROM __auth_users au, __auth_options ao
							WHERE (au.auth_id = ao.auth_id)
							AND (au.user_id='".$user_id."')";

					$result = $this->db->query($sql);
					while ( $row = $this->db->fetch_record($result) ){
						$this->user_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
					}

					$this->db->free_result($result);

					//Group-Permissions
					$result =  $this->db->query("SELECT ga.auth_setting, ao.auth_value, gu.group_id FROM __groups_users gu, __auth_groups ga, __auth_options ao WHERE gu.user_id='".$user_id."' AND ga.group_id = gu.group_id AND ga.auth_id = ao.auth_id");

					while ( $row = $this->db->fetch_record($result) ){
						if ($row['auth_setting'] == "Y"){
							$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
							$this->user_group_memberships[$user_id][$row['group_id']] = 1;
						}
					}
					$this->db->free_result($result);
				}
			} else { //Permission for ANONYMOUS
				$result =  $this->db->query("SELECT ga.auth_setting, ao.auth_value FROM __auth_groups ga, __auth_options ao WHERE ga.auth_id = ao.auth_id AND ga.group_id = 1");
			while ( $row = $this->db->fetch_record($result) ){
				if ($row['auth_setting'] == "Y" && substr($row['auth_value'], 0, 2)!= "a_"){
						$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
				}
					$this->user_group_memberships[$user_id][1] = 1;
			}
			$this->db->free_result($result);
		}
	}
}

	//Checks if a user has the permission.
	public function check_auth($auth_value, $user_id, $groups = true){
		$this->init_user_permissions($user_id);
		$tmp_user_permissions = $this->user_permissions[$user_id];

		if ($groups == true){
			foreach($this->user_group_permissions[$user_id] as $key=>$elem){
				if ($elem == "Y"){
					$tmp_user_permissions[$key] = "Y";
				}
			}
		}

		// If auth_value ends with a '_' it's checking for any permissions of that type
		$exact = ( substr($auth_value, -1, 1) == '_' ) ? false : true;

		foreach ( $tmp_user_permissions as $value => $setting ){
			if ( $exact ){
				if ( ($value == $auth_value) && ($setting == 'Y') ){
					return true;
				}
			} else {
				if ( preg_match('/^('.$auth_value.'.+)$/', $value, $match) ){
					if ( $tmp_user_permissions[$match[1]] == 'Y' ){
						return true;
					}
				}
			}
		}
		return false;
	}

	//Returns all groups the user is in
	public function get_user_group_memberships($user_id=0){
		if(!isset($this->user_permissions[$user_id])){
			$this->init_user_permissions($user_id);
		}
		return $this->user_group_memberships[$user_id];
	}

	//Checks if a user is in a special group
	public function check_group($group_id, $user_id){
		if(!isset($this->user_permissions[$user_id])){
			$this->init_user_permissions($user_id);
		}
		if (isset($this->user_group_memberships[$user_id][$group_id])){
			return true;
		} else {
			return false;
		}
	}

} //Close class

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_acl', acl::$shortcuts);
?>
