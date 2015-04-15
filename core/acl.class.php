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
	header('HTTP/1.0 404 Not Found');exit;
}

class acl_manager extends gen_class {	
	private $auth_defaults		= array();
	private $auth_ids			= array();
	private $group_permissions	= array();

	//Returns the default permissions
	public function get_auth_defaults($force_requery = false){
		if(empty($this->auth_defaults) || $force_requery){
			$sql = 'SELECT auth_id, auth_value, auth_default
					FROM __auth_options
					ORDER BY auth_id';
			$result = $this->db->query($sql);
			if ($result){
				while ( $row = $result->fetchAssoc() ) {
					$this->auth_defaults[ $row['auth_value'] ]	= $row['auth_default'];
					$this->auth_ids[$row['auth_value']]			= $row['auth_id'];
				}
			}

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
						AND (ag.group_id=?)";
				
				
				$result = $this->db->prepare($sql)->execute($group_id);
				
				if ($result){
					while ( $row = $result->fetchAssoc() ){
						if ($row['auth_setting'] == 'Y'){
							$this->group_permissions[$group_id][$row['auth_value']] = $row['auth_setting'];
						}
					}
				}

			}
		}
		return $this->group_permissions[$group_id];
	}

	public function get_groups_with_active_auth($auth_value){
		$groups = $this->pdh->get('user_groups', 'id_list');
		$output	= array();
		foreach($groups as $group_id){
			$active_auths	= $this->get_group_permissions($group_id);
			if(array_key_exists($auth_value, $active_auths)){
				$output[] = $group_id;
			}
		}
		return $output;
	}

	public function update_auth_option($auth_value, $auth_default){
		$auth_id = $this->get_auth_id($auth_value);
		if ( $auth_id ){			
			$objQuery = $this->db->prepare("UPDATE __auth_users :p WHERE auth_id=?")->set(array(
					'auth_setting' => $auth_default,
			))->execute($auth_id);
		}else{
			$objQuery = $this->db->prepare("INSERT INTO __auth_options :p")->set(array(
					'auth_value'	=> $auth_value,
					'auth_default'	=> $auth_default,
			))->execute();
		}
		return $objQuery;
	}

	public function del_auth_option($auth_value){
		$objQuery = $this->db->prepare("DELETE FROM __auth_options WHERE auth_value=?")->execute($auth_value);
		return $objQuery;
	}

	public function update_user_permissions($permission_array, $user_id=0){
		if ($user_id == 0){$user_id = $user->data['user_id'];}
		
		$this->db->prepare("DELETE FROM __auth_users WHERE user_id=? AND auth_id :in")->in(array_keys($permission_array))->execute($user_id);

		$boolExecute = false;

		$sql = "INSERT INTO __auth_users (user_id, auth_id, auth_setting) VALUES ";
		foreach ($permission_array as $auth_id => $permission) {
			if ($permission == 'Y'){
				$boolExecute = true;
				$arrData[] = array(
						'user_id'		=> $user_id,
						'auth_id'		=> $auth_id,
						'auth_setting'	=> $permission,
				);
			}
		}

		if ($boolExecute) $this->db->prepare("INSERT INTO __auth_users :p")->set($arrData)->execute();
		
		
		
	}

	//Returns the permissions that are only for the superadmin
	public function get_superadmin_only_permissions(){
		return $this->superadmin_only_permissions;
	}

	public function get_permission_boxes(){
		$group_permissions = array(
			// Events
			$this->user->lang('events') => array(
				'icon' => 'fa fa-key la-lg',
				array('CBNAME' => 'a_event_add',			'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_event_upd',			'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_event_del',			'TEXT' => $this->user->lang('delete')),
			),
			// Individual adjustments
			$this->user->lang('individual_adjustments') => array(
				'icon' => 'fa fa-tag la-lg',
				array('CBNAME' => 'a_indivadj_add',			'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_indivadj_upd',			'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_indivadj_del',			'TEXT' => $this->user->lang('delete'))
			),
			// Items
			$this->user->lang('items') => array(
				'icon' => 'fa fa-gift la-lg',
				array('CBNAME' => 'a_item_add',				'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_item_upd',				'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_item_del',				'TEXT' => $this->user->lang('delete')),
			),
			// Article
			$this->user->lang('articles') => array(
				'icon' => 'fa fa-file-text la-lg',
				array('CBNAME' => 'a_articles_man',			'TEXT' => $this->user->lang('manage')),
				array('CBNAME' => 'a_article_categories_man','TEXT' => $this->user->lang('manage_article_categories')),
				array('CBNAME' => 'u_files_man',			'TEXT' => $this->user->lang('perm_u_files_man')),
				array('CBNAME' => 'u_articles_script',		'TEXT' => $this->user->lang('perm_u_articles_script')),
			),
			// Raids
			$this->user->lang('raids') => array(
				'icon' => 'fa fa-trophy la-lg',
				array('CBNAME' => 'a_raid_add',				'TEXT' => $this->user->lang('add')),
				array('CBNAME' => 'a_raid_upd',				'TEXT' => $this->user->lang('update')),
				array('CBNAME' => 'a_raid_del',				'TEXT' => $this->user->lang('delete')),
			),

			// Calendar
			$this->user->lang('calendars') => array(
				'icon' => 'fa fa-calendar la-lg',
				array('CBNAME' => 'a_calendars_man',		'TEXT' => $this->user->lang('manage_calendars')),
				array('CBNAME' => 'a_cal_event_man',		'TEXT' => $this->user->lang('manage_calevents')),
				array('CBNAME' => 'a_cal_revent_conf',		'TEXT' => $this->user->lang('manage_revent_man')),
				array('CBNAME' => 'a_cal_addrestricted',	'TEXT' => $this->user->lang('add_restricted_calevent')),
				array('CBNAME' => 'u_cal_event_add',		'TEXT' => $this->user->lang('add_calevents')),
				array('CBNAME' => 'u_calendar_view',		'TEXT' => $this->user->lang('view_calendar')),
				array('CBNAME' => 'u_calendar_raidnotes',	'TEXT' => $this->user->lang('calendar_acl_raidnotes')),
			),

			// Members
			$this->user->lang('chars') => array(
				'icon' => 'fa fa-user la-lg',
				array('CBNAME' => 'a_members_man',			'TEXT' => $this->user->lang('manage')),
				array('CBNAME' => 'a_raidgroups_man',		'TEXT' => $this->user->lang('manage_raid_groups')),
				array('CBNAME' => 'u_member_add',			'TEXT' => $this->user->lang('charsadd')),
				array('CBNAME' => 'u_member_man',			'TEXT' => $this->user->lang('charsmanage')),
				array('CBNAME' => 'u_member_del',			'TEXT' => $this->user->lang('charsdelete')),
				array('CBNAME' => 'u_member_conn',			'TEXT' => $this->user->lang('charconnect')),
			),
			// Manage
			$this->user->lang('manage') => array(
				'icon' => 'fa fa-wrench la-lg',
				array('CBNAME' => 'a_config_man',			'TEXT' => $this->user->lang('configuration')),
				array('CBNAME' => 'a_extensions_man',		'TEXT' => $this->user->lang('extensions')),
				array('CBNAME' => 'a_reset',				'TEXT' => $this->user->lang('reset')),
				array('CBNAME' => 'a_maintenance',			'TEXT' => $this->user->lang('maintenance')),
				array('CBNAME' => 'a_files_man',			'TEXT' => $this->user->lang('manage_files')),
			),
			//User
			$this->user->lang('user') => array(
				'icon' => 'fa fa-group la-lg',
				array('CBNAME' => 'a_users_man',			'TEXT' => $this->user->lang('manage')),
				array('CBNAME' => 'a_users_perms',			'TEXT' => $this->user->lang('permissions')),
				array('CBNAME' => 'a_usergroups_man',		'TEXT' => $this->user->lang('manage_user_groups')),
				array('CBNAME' => 'a_users_profilefields',	'TEXT' => $this->user->lang('manage_userpf')),
				array('CBNAME' => 'a_users_massmail',		'TEXT' => $this->user->lang('massmail_send')),
				array('CBNAME' => 'u_userlist',				'TEXT' => $this->user->lang('view')),
				array('CBNAME' => 'u_usermailer',			'TEXT' => $this->user->lang('adduser_send_mail')),
			),

			// Logs
			$this->user->lang('logs') => array(
				'icon' => 'fa fa-book la-lg',
				array('CBNAME' => 'a_logs_view',			'TEXT' => $this->user->lang('view')),
				array('CBNAME' => 'a_logs_del',				'TEXT' => $this->user->lang('delete'))
			),
			// Backup Database
			$this->user->lang('backup') => array(
				'icon' => 'fa fa-floppy-o la-lg',
				array('CBNAME' => 'a_backup',				'TEXT' => $this->user->lang('backup_database'))
			),
			 // Search
			$this->user->lang('search') => array(
				'icon' => 'fa fa-search la-lg',
				array('CBNAME' => 'u_search',				'TEXT' => $this->user->lang('search')),
			),
		);
		return $group_permissions;
	}
}
class acl extends acl_manager {
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
				$objQuery = $this->db->prepare("SELECT * FROM __groups_users WHERE user_id=?")->execute($user_id);
				if ($objQuery){
					while ( $row = $objQuery->fetchAssoc() ){
						if (intval($row['grpleader']) && !isset($this->user_group_permissions[$user_id]['a_usergroups_grpleader'])) $this->user_group_permissions[$user_id]['a_usergroups_grpleader'] = "Y";
						$this->user_group_memberships[$user_id][$row['group_id']] = 1;
					}
				}

				//If user is Superadmin, he has all permissions
				if (isset($this->user_group_memberships[$user_id][2])){
					foreach ($this->get_auth_defaults() as $value => $default){
						$this->user_group_permissions[$user_id][$value] = "Y";
					}
					//If not superadmin: get user- and grouppermissions
				} else {
					//User-Permissions					
					$objQuery = $this->db->prepare("SELECT ao.auth_value, au.auth_setting
							FROM __auth_users au, __auth_options ao
							WHERE (au.auth_id = ao.auth_id)
							AND (au.user_id=?)")->execute($user_id);
					
					if($objQuery){
						while ( $row = $objQuery->fetchAssoc() ){
							$this->user_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
						}
					}
					
					//Group-Permissions
					$objQuery = $this->db->prepare("SELECT ga.auth_setting, ao.auth_value, gu.group_id FROM __groups_users gu, __auth_groups ga, __auth_options ao WHERE gu.user_id=? AND ga.group_id = gu.group_id AND ga.auth_id = ao.auth_id")->execute($user_id);
					
					if($objQuery){
						while ( $row = $objQuery->fetchAssoc() ){
							if ($row['auth_setting'] == "Y"){
								$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
								$this->user_group_memberships[$user_id][$row['group_id']] = 1;
							}
						}
					}
				}
				
				//Check if he has chars that are grpleader of raidgroups
				if ($this->pdh->get('raid_groups_members', 'user_has_grpleaders', array($user_id))) $this->user_group_permissions[$user_id]['a_raidgroups_grpleader'] = "Y";
				
			} else { //Permission for ANONYMOUS
				$result =  $this->db->query("SELECT ga.auth_setting, ao.auth_value FROM __auth_groups ga, __auth_options ao WHERE ga.auth_id = ao.auth_id AND ga.group_id = 1");
				if($result){
					while ( $row = $result->fetchAssoc() ){
						if ($row['auth_setting'] == "Y" && substr($row['auth_value'], 0, 2)!= "a_"){
								$this->user_group_permissions[$user_id][$row['auth_value']] = $row['auth_setting'];
						}		
					}
				}
				$this->user_group_memberships[$user_id][1] = 1;
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

?>
