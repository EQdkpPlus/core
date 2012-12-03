<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_User_Groups extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'db', 'pm', 'time', 'acl'	=> 'acl', 'crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_users_man');
		$handler = array(
			'save' => array('process' => 'user_group_save', 'csrf'=>true),
			'del_group_users' => array('process' => 'user_group_users_del', 'csrf'=>true),
			'add_group_users' => array('process' => 'user_group_users_save', 'csrf'=>true),
			'save_group_perms' => array('process' => 'save_group_permissions', 'csrf'=>true),
			'user_group_perms' => array('process' => 'display_grouppermlist'),
			'grp_perms' => array('process' => 'display_grouppermlist'),
			'g' => array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('user_groups', 'name'), null, 'user_group_ids[]');
		$this->process();
	}
	
	//Delete User of a Group
	public function user_group_users_del(){
		$members = $this->in->getArray('group_user', 'int');
		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'delete_users_from_group', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('del_user_from_group_success'), 'color' => 'green');
		$this->edit($message);
		
	}
	
	//Add User to a Group
	public function user_group_users_save(){
		$members = $this->in->getArray('add_user', 'int');
		if ($members[0] == 0){unset($members[0]);};
		if ($this->in->get('g') == 2){unset($members[$this->user->data['user_id']]);}
		
		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'add_users_to_group', array($members, $this->in->get('g')));

		}
		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('add_user_to_group_success'), 'color' => 'green');
		$this->edit($message);
		
	}
	
	//Save the user-Groups
	public function user_group_save() {
		$retu = array();
		$group_post = $this->get_post();
			
		if($group_post) {
			$id_list = $this->pdh->get('user_groups', 'id_list');
			foreach($group_post as $group) {
				$standard = ($this->in->get('user_groups_standard') == $group['id']) ? 1 : 0;
				$func = (in_array($group['id'], $id_list)) ? 'update_grp' : 'add_grp';
				$retu[] = $this->pdh->put('user_groups', $func, array($group['id'], $group['name'], $group['desc'], $standard, $group['hide']));
				$names[] = $group['name'];
				$add_name = (in_array($group['id'], $id_list)) ? '' : $group['name'];
			}

			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				if ($add_name != ""){
					$message = array('title' => $this->user->lang('save_suc'), 'text' => sprintf($this->user->lang('add_usergroup_success'), $add_name), 'color' => 'green');
				} else {
					$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('save_usergroup_success'), 'color' => 'green');
				}
			}
			
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_ranks_selected'), 'color' => 'grey');
		}

		$this->display($message);
	}
	
	//Delete user-groups
	public function delete() {
		$grpids = array();
		if(count($this->in->getArray('user_group_ids', 'int')) > 0) {
			$grpids = $this->in->getArray('user_group_ids', 'int');
		} else {
			$grpids[] = $this->in->get('user_group_ids', 0);
		}

		if(is_array($grpids)) {
			foreach($grpids as $id) {
				
				$names[] = $this->pdh->get('user_groups', 'name', ($id));
				$retu[] = $this->pdh->put('user_groups', 'delete_grp', array($id));
			}

			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_nosuc'), 'text' => $this->user->lang('delete_default_group_error'), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		}
		$this->display($message);
	}
	
	//Display the Usergroup-list
	public function display($messages=false){
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$new_id = 0;
		$order = $this->in->get('order','0.1');
		$red = 'RED'.str_replace('.', '', $order);

		$grps = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));

		if($order == '0.0')
		{
			arsort($grps, SORT_STRING);
		}
		else
		{
			asort($grps, SORT_STRING);
		}
		$key = 0;
		$new_id = 1;
		
		//ksort($grps); //otherwise our new_id is wrong!
		foreach($grps as $id => $name){
			$this->tpl->assign_block_vars('user_groups', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'DESC'	=> $this->pdh->get('user_groups', 'desc', array($id)),
				'USER_COUNT'	=> $this->pdh->get('user_groups_users', 'groupcount', array($id)),
				'S_DELETABLE' => ($this->pdh->get('user_groups', 'deletable', array($id))) ? true : false,
				'S_NO_STANDARD' => ($id == 2 || $id == 3) ? true : false,
				'STANDARD'	=> ($this->pdh->get('user_groups', 'standard', array($id))) ? 'checked="checked"' : '',
				'HIDE'	=> ($this->pdh->get('user_groups', 'hide', array($id))) ? 'checked="checked"' : '',
			));
			$key++;
			$new_id = ($id >= $new_id) ? $id+1 : $new_id;
		}
		
		$this->confirm_delete($this->user->lang('confirm_delete_groups'));
		$this->confirm_delete($this->user->lang('confirm_delete_groups'), '', true, array('function' => 'delete_single_warning', 'force_ajax' => true));
		$this->jquery->selectall_checkbox('selall_groups', 'user_group_ids[]');
		$this->tpl->assign_vars(array(
			$red 		=> '_red',
			'SID'		=> $this->SID,
			'ID'		=> $new_id,
			'KEY'		=> $key,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_user_groups'),
			'template_file'		=> 'admin/manage_user_groups.html',
			'display'			=> true)
		);
	}
	

	
	//Process: Save permissions of a group
	public function save_group_permissions(){
		if ($this->in->get('g') != 2){
			$auth_defaults = $this->acl->get_auth_defaults(false);
			$group_permissions = $this->acl->get_group_permissions($this->in->get('g', 0), true);
			$superadm_only = $this->acl->get_superadmin_only_permissions();
			$memberships = $this->acl->get_user_group_memberships($this->user->data['user_id']);
			
			//If not Superadmin, unset the superadmin-permissions
			if (!isset($memberships[2])){
				foreach ($superadm_only as $superperm){
					unset($auth_defaults[$superperm]);
				}
			}
			
			foreach ( $auth_defaults as $auth_value => $auth_setting ) {
				$r_auth_id    = $this->acl->get_auth_id($auth_value);
				$r_auth_value = $auth_value;
				$chk_auth_value = ( $group_permissions[$auth_value] == "Y") ? 'Y' : 'N';
				$db_auth_value  = ( $this->in->get($r_auth_value) == "Y" )                      ? 'Y' : 'N';

				if ( $chk_auth_value != $db_auth_value ) {
					$this->update_auth_groups($r_auth_id, $db_auth_value, $this->in->get('g', 0));
				}
			}
			$this->db->free_result($result);
		}
		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('admin_set_perms_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	

	
	// ---------------------------------------------------------
	// Displays a single Group
	// ---------------------------------------------------------
	public function edit($messages=false, $group = false){
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		
		$groupID  = ($group) ? $group : $this->in->get('g', 0);
		
		//Only a Super-Admin is allowed to manage the super-admin group
		$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));
		if ($groupID == 2 && !isset($memberships[2])){message_die($this->user->lang('no_auth_superadmins'), '', 'access_denied');}
		
		$order = $this->in->get('o','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		
		//Get Users in Group
		$members = $this->pdh->get('user_groups_users', 'user_list', array($groupID));
		
		//Get Group-name
		$group_name = $this->pdh->get('user_groups', 'name', array($groupID));	
		
		//Get all Userdata
		$sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active, s.session_id
				FROM (__users u
				LEFT JOIN __sessions s
				ON u.user_id = s.session_user_id)
				GROUP BY u.username 
				ORDER BY u.username '.(($order == '0.0') ? 'ASC' : 'DESC');
		
		$user_query = $this->db->query($sql);
		while($row = $this->db->fetch_record($user_query)){
			$user_data[$row['user_id']] = $row;
		}
		$not_in = array();
		
		//Bring all members from Group to template
		foreach($user_data as $key => $elem) {
			if (in_array($key, $members)){
			
			$user_online = ( !empty($elem['session_id']) ) ? "<img src='../images/glyphs/status_green.gif' alt='' />" : "<img src='../images/glyphs/status_red.gif' alt='' />";
			$user_active = ( $elem['user_active'] == '1' ) ? "<img src='../images/glyphs/status_green.gif' alt='' />" : "<img src='../images/glyphs/status_red.gif' alt='' />";
			
			$this->tpl->assign_block_vars('user_row', array(
				'ID'			=> $elem['user_id'],
				'NAME'			=> sanitize($elem['username']),
				'EMAIL'			=> ( !empty($elem['user_email']) ) ? '<a href="javascript:usermailer('.$elem['user_id'].');">'.$this->crypt->decrypt($elem['user_email']).'</a>' : '',
				'LAST_VISIT'	=> $this->time->user_date($elem['user_lastvisit'], true),
				'ACTIVE'		=> $user_active,
				'ONLINE'		=> $user_online,
				'S_UNDELETABLE'	=> ($groupID == 2 && $elem == $this->user->data['user_id']) ? true : false,
			));
			

			} else {
				$not_in[$key] = $elem['username'];
			}

		}
		


		//Permissions
		$permission_boxes = $this->acl->get_permission_boxes();
		$this->pm->generate_permission_boxes($permission_boxes);
		$group_permissions = $this->acl->get_group_permissions($this->in->get('g'), true);
		$superadm_only_perms = $this->acl->get_superadmin_only_permissions();

		foreach ( $permission_boxes as $group => $checks ){
						
			$a_set = $u_set = false;
			foreach ( $checks as $data ){
				//Guests won't get admin-permissions
				if (($groupID == 1 && substr($data['CBNAME'], 0, 2)== "a_")) continue;
				//Superadmin permission
				if (isset($superadm_only_perms[$data['CBNAME']]) && !isset($memberships[2])) continue;
				
				switch (substr($data['CBNAME'], 0, 2)){
					case 'a_': if (!$a_set){
									$this->tpl->assign_block_vars('a_permissions_row', array(
										'GROUP' => $group,
									));
									$a_set = true;
								}
					break;
					
					case 'u_': if (!$u_set){
									$this->tpl->assign_block_vars('u_permissions_row', array(
										'GROUP' => $group,
									));
									$u_set = true;
								}
					break;
					
				}

				$this->tpl->assign_block_vars(substr($data['CBNAME'], 0, 2).'permissions_row.check_group', array(
					'CBNAME'			=> $data['CBNAME'],
					'DISABLED'			=> ($groupID == 2) ? 'disabled' : '',
					'CBCHECKED'			=> (isset($group_permissions[$data['CBNAME']]) && $group_permissions[$data['CBNAME']] == "Y") ? ' checked="checked"' : '',
					'CLASS'				=> (isset($group_permissions[$data['CBNAME']]) && $group_permissions[$data['CBNAME']] == "Y") ? 'positive' : 'negative',
					'TEXT'				=> $data['TEXT'],
				));
			}

		}
		unset($permission_boxes);
		$this->jquery->Tab_header('groups_tabs');
		$this->jquery->Tab_header('permission_tabs');
		
		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));
		
		$this->tpl->assign_vars(array(
			'GROUP_NAME'			=> sanitize($group_name),
			$red 					=> '_red',
			'U_MANAGE_USERS'		=> 'manage_user_groups.php'.$this->SID.'&amp;g='.$groupID,
			'SID'					=> $this->SID,
			'KEY'					=> $key,
			'ADD_USER_DROPDOWN'		=> $this->jquery->MultiSelect('add_user', $not_in, '', array('width' => 350, 'filter' => true)),
			'GRP_ID'				=> $groupID,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_user_group').': '.sanitize($group_name),
			'template_file'		=> 'admin/manage_user_groups_users.html',
			'display'			=> true)
		);
	}
	
	public function display_grouppermlist(){
		//Permissions
		$permission_boxes = $this->acl->get_permission_boxes();
		$this->pm->generate_permission_boxes($permission_boxes);
		$grps = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		
		foreach ( $permission_boxes as $group => $checks ){
			$this->tpl->assign_block_vars('permissions_row', array(
				'GROUP' => $group)
			);
			foreach($grps as $group_id => $group){
				$this->tpl->assign_block_vars('permissions_row.headline_row', array(
					'GROUP'		=> $group,
				));
			}

			foreach ( $checks as $data ){
				$this->tpl->assign_block_vars('permissions_row.check_group', array(
					'CBNAME'		=> $data['TEXT'],
					'S_ADMIN'		=> (strpos($data['CBNAME'], 'a_') !== false) ? true : false
				));

				foreach($grps as $group_id => $group){
					$group_permissions = $this->acl->get_group_permissions($group_id);
					$this->tpl->assign_block_vars('permissions_row.check_group.group_row', array(
						'STATUS'	=> ( $group_permissions[$data['CBNAME']] == "Y") ? ' <img src="../images/global/ok.png" height="14" alt="" />' : '',
					));
				}
			}
		}
		unset($permission_boxes);
		$this->tpl->assign_vars(array(
			'S_GROUP_PERM_LIST'		=> true,
		));
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('user_group_permissions'),
				'template_file'		=> 'admin/manage_user_groups.html',
				'display'			=> true)
		);
	
	}
	
	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------
	public function update_auth_groups($auth_id,  $auth_setting = 'N', $group_id=0,$check_query_type = true){
		$upd_ins = ( $check_query_type ) ? $this->switch_upd_ins($auth_id, $group_id) : 'upd';

		if ( (empty($auth_id)) || (empty($group_id)) ){
			return false;
		}

		if ( $upd_ins == 'upd' ){
			if ($auth_setting == "N"){
				$sql = "DELETE FROM __auth_groups 
						WHERE auth_id='".$auth_id."'
						AND group_id='".$group_id."'";

			} else {
				$sql = "UPDATE __auth_groups
						SET auth_setting='".$auth_setting."'
						WHERE auth_id='".$auth_id."'
						AND group_id='".$group_id."'";
			}
			
		}else{
			$sql = "INSERT INTO __auth_groups
					(group_id, auth_id, auth_setting)
					VALUES ('".$group_id."','".$auth_id."','".$auth_setting."')";
		}

		if ( !($result = $this->db->query($sql)) ){
			return false;
		}
		return true;
	}

	private function switch_upd_ins($auth_id, $group_id){
		$sql = "SELECT o.auth_value
				FROM __auth_options o, __auth_groups u
				WHERE (u.auth_id = o.auth_id)
				AND (u.group_id='".$group_id."')
				AND u.auth_id='".$auth_id."'";
		if ( $this->db->num_rows($this->db->query($sql)) > 0 )
		{
			return 'upd';
		}
		return 'ins';
	}

	private function get_post() {
		$grps = array();
		if(is_array($this->in->getArray('user_groups', 'string'))) {			
			foreach($this->in->getArray('user_groups', 'string') as $key => $grp) {
				if(isset($grp['id']) AND $grp['id'] AND !empty($grp['name'])) {
					$grps[] = array(
						'id'	=> $this->in->get('user_groups:'.$key.':id',0),
						'name'	=> $this->in->get('user_groups:'.$key.':name',''),
						'desc'	=> $this->in->get('user_groups:'.$key.':desc',''),
						'hide'	=> $this->in->get('user_groups:'.$key.':hide',0),
						'deletable' => $this->in->get('user_groups:'.$key.':deletable',false)
					);
				}
			}

			return $grps;
		}
		return false;
	}
	
	private function get_selected() {
		$grps = array();
		$selected = $this->in->getArray('user_group_ids', 'int');
		if(is_array($this->in->getArray('user_groups', 'string'))) {			
			foreach($this->in->getArray('user_groups', 'string') as $key => $grp) {
				if(isset($grp['id']) AND in_array($grp['id'], $selected)) {
					$grps[] = array(
						'id'	=> $this->in->get('user_groups:'.$key.':id',0),
						'name'	=> $this->in->get('user_groups:'.$key.':name',''),
						'desc'	=> $this->in->get('user_groups:'.$key.':desc',''),
						'hide'	=> $this->in->get('user_groups:'.$key.':hide',0),
						'deletable' => $this->in->get('user_groups:'.$key.':deletable',false)
					);
				}
			}
			return $grps;
		}
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_User_Groups', Manage_User_Groups::__shortcuts());
registry::register('Manage_User_Groups');
?>