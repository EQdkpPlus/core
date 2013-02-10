<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Users extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'pm', 'time', 'db', 'pfh', 'html', 'env', 'acl'=>'acl', 'email'=>'MyMailer', 'crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_users_man');
		$handler = array(
			'mode' => array(
				array('process' => 'activate', 'value' => 'activate', 'csrf' => true),
				array('process' => 'deactivate', 'value' => 'deactivate', 'csrf' => true),
				array('process' => 'overtake_permissions', 'value' => 'ovperms', 'csrf' => true),
				),
			'u' => array('process' => 'edit'),
			'submit' => array('process' => 'submit', 'csrf' => true),
			'send_new_pw' => array('process' => 'send_new_pw', 'csrf' => true),
		);
		parent::__construct(false, $handler, array('user', 'name'), null, 'user_id[]');
		$this->process();
	}

	public function send_new_pw(){
		$pwkey = $this->pdh->put('user', 'create_new_activationkey', array($this->in->get('u')));
		if(!strlen($pwkey)) {
			$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
			$this->display();
		}

		// Email them their new password
		$bodyvars = array(
			'USERNAME'		=> $this->pdh->get('user', 'name', array($this->in->get('u'))),
			'DATETIME'		=> $this->time->date('m/d/y h:ia T'),
			'U_ACTIVATE'	=> $this->env->link.'login.php?mode=newpassword&amp;key=' . $pwkey,
		);

		if($this->email->SendMailFromAdmin($this->in->get('email_address'), $this->user->lang('email_subject_new_pw'), 'user_new_password.html', $bodyvars)) {
			$this->core->message($this->user->lang('password_sent'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('error_email_send'), $this->user->lang('error'), 'red');
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function activate() {
		$this->pdh->put('user', 'activate', array($this->in->get('u')));
		$username = $this->pdh->get('user', 'name', array($this->in->get('u')));
		$this->core->message(sprintf($this->user->lang('user_activate_success'), sanitize($username)), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function deactivate() {
		if (!(($this->user->data['user_id'] == $this->in->get('u')) || (!$this->user->check_group(2, false) && $this->user->check_group(2, false, $this->in->get('u'))))){
			$this->pdh->put('user', 'activate', array($this->in->get('u'), 0));
			$username = $this->pdh->get('user', 'name', array($this->in->get('u')));
			$this->core->message(sprintf($this->user->lang('user_deactivate_success'), sanitize($username)), $this->user->lang('success'), 'green');
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function overtake_permissions(){
		if ($this->user->check_group(2, false) || ($this->user->check_auth('a_users_man') && !$this->user->check_group(2, false, $this->in->get('u', 0)))){
			$this->user->overtake_permissions($this->in->get('u', 0));
			redirect('index.php'.$this->SID);
		}
	}

	// ---------------------------------------------------------
	// Process Submit
	// ---------------------------------------------------------
	public function submit() {
		$user_id = $this->in->getArray('user_id', 'int');
		$user_id = current($user_id);
		$new_user = ($user_id) ? false : true;
		$password = false;
		
		//The Group-Memberships of the admin who has submitted this form
		$adm_memberships   = $this->acl->get_user_group_memberships($this->user->id);

		if($user_id) {
			//Prevent edit of Superadmin
			if (!isset($adm_memberships[2]) && $this->user->check_group(2, false, $user_id)){
				$this->display();
				return;
			}
			
			$this->pdh->put('user', 'update_user_settings', array($user_id, $this->get_settingsdata()));
			
			$this->pdh->put('user', 'activate', array($user_id, $this->in->get('user_active', 0)));
		} else {
			$password = ($this->in->get('password') == "") ? random_string() : $this->in->get('password');
			$new_salt = $this->user->generate_salt();
			$new_password = $this->user->encrypt_password($password, $new_salt).':'.$new_salt;
			
			$query_ar = array(
				'username'				=> $this->in->get('username'),
				'user_password'			=> $new_password,
				'user_email'			=> $this->crypt->encrypt($this->in->get('email_address'))
			);
			
			$privArray = array();
			$customArray = array();
			$custom_fields = array('user_avatar', 'work', 'interests', 'hardware', 'facebook', 'twitter');
			foreach($settingsdata as $group => $fieldsets) {
				if($group == 'registration_information') continue;
				foreach($fieldsets as $tab => $fields) {
					foreach($fields as $name => $field) {
						if($tab == 'user_priv') 
							$privArray[$name] = $this->html->widget_return($field);
						elseif(in_array($name, $custom_fields)) 
							$customArray[$name] = $this->html->widget_return($field);
						else 
							$query_ary[$name] = $this->html->widget_return($field);
					}
				}
			}
			
			$plugin_settings = array();
			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $values){
					foreach ($values as $key=>$setting){
						$name								= $setting['name'];
						$setting['name']					= $plugin.':'.$setting['name'];
						$plugin_settings[$plugin][$name]	= $this->html->widget_return($setting);
					}
				}
			}
			
			$query_ar['privacy_settings']	= serialize($privArray);
			$query_ar['custom_fields']		= serialize($customArray);
			$query_ar['plugin_settings']	= serialize($plugin_settings);

			$user_id = $this->pdh->put('user', 'insert_user', array($query_ar, true, false));
			
		}

		// Permissions
		$auth_defaults = $this->acl->get_auth_defaults();
		$superadm_only = $this->acl->get_superadmin_only_permissions();
		//If the admin is not Superadmin, unset the superadmin-permissions
		if (!isset($adm_memberships[2])){
			foreach ($superadm_only as $superperm){
				unset($auth_defaults[$superperm]);
			}
		}
		$auths_to_update = array();
		foreach ( $auth_defaults as $auth_value => $auth_setting ){
			$r_auth_id    = $this->acl->get_auth_id($auth_value);
			$r_auth_value = $auth_value;

			$chk_auth_value = ( !$new_user AND $this->user->check_auth($r_auth_value, false, $user_id, false) ) ? 'Y' : 'N';
			$db_auth_value  = ( $this->in->exists($r_auth_value) ) ? 'Y' : 'N';

			if ( $chk_auth_value != $db_auth_value ){
				$auths_to_update[$r_auth_id] = $db_auth_value;
			}
		}
		if(count($auths_to_update) > 0)	$this->acl->update_user_permissions($auths_to_update, $user_id);

		$this->pdh->put('member', 'update_connection', array($this->in->getArray('member_id', 'int'), $user_id));

		// User-Groups
		$groups = $this->in->getArray('user_groups', 'int');
		$group_list = $this->pdh->get('user_groups', 'id_list', 0);
		$this->pdh->put('user_groups_users', 'delete_user_from_groups', array($user_id, $group_list));
		$this->pdh->put('user_groups_users', 'add_user_to_groups', array($user_id, $groups));

		// E-mail the user if he/she was activated by the admin and admin activation was set in the config
		$email_success_message = '';
		if ($password OR ($this->config->get('account_activation') == 2 ) && ( $this->pdh->get('user', 'active', array($user_id)) < $this->in->get('user_active'))){

			// Email them their new password
			$this->email->Set_Language($this->in->get('user_lang'));

			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($user_id));
			if(!strlen($user_key)) {
				$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
			}
			$strPasswordLink = $this->env->link . 'login.php?mode=newpassword&key=' . $user_key;

			$bodyvars = array(
				'USERNAME'	=> $this->in->get('username'),
				'U_ACTIVATE'=> ($password) ? $this->user->lang('email_changepw').'<br /><br /><a href="'.$strPasswordLink.'">'.$strPasswordLink.'</a>' : '',
				'GUILDTAG'	=> $this->config->get('guildtag'),
			);
			if($this->email->SendMailFromAdmin($this->in->get('email_address'), $this->user->lang('email_subject_activation_none'), 'register_activation_none.html', $bodyvars)) {
				$email_success_message = $this->user->lang('account_activated_admin')."\n";
			}
		}

		//create output message
		$this->core->message($email_success_message . $this->user->lang('update_settings_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	// ---------------------------------------------------------
	// Process (Mass) Delete
	// ---------------------------------------------------------
	function delete(){
		if ($this->in->exists('user_id')){
			if (count($this->in->getArray('user_id', 'int')) > 0){
				$user_ids = $this->in->getArray('user_id', 'int');
			} else {
				$user_ids[0] = $this->in->get('user_id');
			}

			foreach ($user_ids as $usr){
				if (($this->user->data['user_id'] == $usr) || (!$this->user->check_group(2, false) && $this->user->check_group(2, false, $usr))){
					$bad_users[] = $this->pdh->get('user', 'name', array($usr));
				} else {
					$good_users[] = $this->pdh->get('user', 'name', array($usr));
				}
				$this->pdh->put('user', 'delete_user', array($usr, (int)$this->in->get('del_assocmem', 0)));
			}
			if(!empty($bad_users)) $this->core->message(sprintf($this->user->lang('admin_delete_user_no'), implode(', ', $bad_users)), $this->user->lang('error'), 'red');
			if(!empty($good_users)) $this->core->message(sprintf($this->user->lang('admin_delete_user_success'), implode(', ', $good_users)), $this->user->lang('success'), 'green');
		}else{
			$this->core->message($this->user->lang('no_user_select'), $this->user->lang('error'), 'red');
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	// ---------------------------------------------------------
	// Display
	// ---------------------------------------------------------
	public function display() {
		$order = explode('.', $this->in->get('o', '0.0'));
		$sort = array(
			0 => array('name', array('asc', 'desc')),
			1 => array('email', array('desc', 'asc')),
			2 => array('last_visit', array('desc', 'asc')),
			3 => array('active', array('desc', 'asc')),
			4 => array('regdate', array('desc', 'asc')),
		);

		$user_ids = $this->pdh->sort($this->pdh->get('user', 'id_list'), 'user', $sort[$order[0]][0], $sort[$order[0]][1][$order[1]]);
		$total_users = count($user_ids);
		$start = $this->in->get('start', 0);

		$online_users = array();
		$result = $this->db->query("SELECT session_user_id FROM __sessions;");
		while ( $row = $this->db->fetch_record($result) ) {
			$online_users[] = $row['session_user_id'];
		}
		$this->db->free_result($result);

		$adm_memberships = $this->acl->get_user_group_memberships($this->user->data['user_id']);
		$k = 0;
		foreach($user_ids as $user_id) {
			if($k < $start) {
				$k++;
				continue;
			}
			if($k >= ($start+100)) break;
			$user_online = (in_array($user_id, $online_users)) ? "<img src='../images/glyphs/status_green.gif' alt='' />" : "<img src='../images/glyphs/status_red.gif' alt='' />";
			if($this->pdh->get('user', 'active', array($user_id))) {
				$user_active = "<img src='../images/glyphs/status_green.gif' alt='' />";
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=deactivate&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('deactivate').'"><img src="'.$this->root_path.'images/glyphs/disable.png" alt="" /></a>';
			} else {
				$user_active = "<img src='../images/glyphs/status_red.gif' alt='' />";
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=activate&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('activate').'"><img src="'.$this->root_path.'images/glyphs/enable.png" alt="" /></a>';
			}
			$user_memberships = $this->pdh->get('user_groups_users', 'memberships', array($user_id));

			$this->tpl->assign_block_vars('users_row', array(
				'U_MANAGE_USER'		=> 'manage_users.php'.$this->SID.'&amp;' . 'u' . '='.$user_id,
				'U_OVERTAKE_PERMS'	=> 'manage_users.php'.$this->SID.'&amp;mode=ovperms&amp;' . 'u' . '='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'U_DELETE'			=> 'manage_users.php'.$this->SID.'&amp;del=single&amp;user_id='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('del'),
				'USER_ID'			=> $user_id,
				'NAME_STYLE'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? 'font-weight: bold' : 'font-weight: none',
				'ADMIN_ICON'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? '<img src="../images/global/admin_flag.png" title="'.$this->user->lang('admin').'" alt="'.$this->user->lang('admin').'" /> ' : '',
				'USERNAME'			=> $this->pdh->get('user', 'name', array($user_id)),
				'EMAIL'				=> $this->pdh->get('user', 'email', array($user_id)),
				'LAST_VISIT'		=> ($this->pdh->get('user', 'last_visit', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'last_visit', array($user_id)), true) : '',
				'REG_DATE'		=> ($this->pdh->get('user', 'regdate', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'regdate', array($user_id)), true) : '',
				'PROTECT_SUPERADMIN'=> ((is_array($user_memberships) && in_array(2, $user_memberships) && !isset($adm_memberships[2])) || ($user_id == $this->user->data['user_id'])) ? true : false,
				'ACTIVE'			=> $user_active,
				'ACTIVATE_ICON'		=> $activate_icon,
				'ONLINE'			=> $user_online)
			);
			$a_members = $this->pdh->get('member', 'connection_id', array($user_id));
			$a_members = (is_array($a_members)) ? $this->pdh->maget('member', array('classid', 'name', 'rankname'), 0, array($a_members), null, false, true) : array();
			if (is_array($a_members)){
				foreach ($a_members as $member_id => $member) {
					$this->tpl->assign_block_vars('users_row.members_row', array(
						'MEMBER_ID'		=> $member_id,
						'CLASS'			=> $member['classid'],
						'NAME'			=> $member['name'],
						'RANK'			=> $member['rankname']
					));
				}
			}
			$members = '';
			$k++;
		}
		$onclose_url = "if(event.originalEvent == undefined) { window.location.href = 'admin/manage_users.php'; } else { window.location.href = 'manage_users.php'; }";
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=>"../addcharacter.php".$this->SID."&adminmode=1&editid='+editid+'", 'width'=>'640', 'height'=>'520', 'onclosejs'=>$onclose_url));
		$this->confirm_delete($this->user->lang('confirm_delete_users').'<br /><input type="checkbox" name="delete_associated_members" value="1" onchange="handle_assoc_members()" id="delete_associated_members" /><label for="delete_associated_members"> '. $this->user->lang('delete_associated members').'</label>', '', false, array('height'	=> 300));
		$this->confirm_delete($this->user->lang('confirm_delete_users').'<br /><input type="checkbox" name="delete_associated_members_single" value="1" id="delete_associated_members_single" /><label for="delete_associated_members_single"> '. $this->user->lang('delete_associated members').'</label>', '', true, array('height'	=> 300,'function' => 'delete_single_warning', 'force_ajax' => true, 'custom_js' => 'delete_single(selectedID);'));
		$this->jquery->selectall_checkbox('selall_user', 'user_id[]', $this->user->data['user_id']);

		$this->tpl->assign_vars(array(
			// Sorting
			'O_USERNAME'			=> ($this->in->get('o', '0.0') == '0.0') ? '0.1' : '0.0',
			'O_EMAIL'				=> ($this->in->get('o') == '1.0') ? '1.1' : '1.0',
			'O_LASTVISIT'			=> ($this->in->get('o') == '2.0') ? '2.1' : '2.0',
			'O_ACTIVE'				=> ($this->in->get('o') == '3.0') ? '3.1' : '3.0',
			'O_REG_DATE'			=> ($this->in->get('o') == '4.0') ? '4.1' : '4.0',
			'UPARROW'				=> $this->root_path.'images/arrows/up_arrow',
			'DOWNARROW'				=> $this->root_path.'images/arrows/down_arrow',
			'RED'.$order[0].$order[1] => '_red',

			// Page vars
			'U_MANAGE_USERS'		=> 'manage_users.php' . $this->SID . '&amp;start=' . $start . '&amp;',
			'LISTUSERS_FOOTCOUNT'	=> sprintf($this->user->lang('listusers_footcount'), $total_users, 100),
			'USER_PAGINATION'		=> generate_pagination('manage_users.php'.$this->SID.'&amp;o='.$this->in->get('o'), $total_users, 100, $start))
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_users_title'),
			'template_file'		=> 'admin/manage_users.html',
			'display'			=> true)
		);
	}
	// ---------------------------------------------------------
	// Process Display User
	// ---------------------------------------------------------
	public function edit(){
		$user_id = $this->in->get('u', 0);
		if($user_id != 0 AND !in_array($user_id, $this->pdh->get('user', 'id_list'))) $this->display();
		if ($user_id != 0 && $this->user->check_group(2, false, $user_id) && !$this->user->check_group(2, false)) message_die($this->user->lang('noauth'), '', 'access_denied');

		if ($user_id && $this->in->get('mode') == 'deleteavatar'){
			$this->pdh->put('user', 'delete_avatar', array($user_id));
			$this->pdh->process_hook_queue();
		}

		// Build the user permissions
		$user_permissions = $this->acl->get_permission_boxes();
		// Add plugin checkboxes to our array
		$this->pm->generate_permission_boxes($user_permissions);
		//Get Superadmin-only-Permissions
		$superadm_only_perms = $this->acl->get_superadmin_only_permissions();
		//Get group-memberships of the user
		$memberships = ($user_id) ? $this->acl->get_user_group_memberships($user_id) : array($this->pdh->get('user_groups', 'standard_group', array()));
		//Get group-permission of the admin
		$adm_memberships = $this->acl->get_user_group_memberships($this->user->data['user_id']);

		foreach ( $user_permissions as $group => $checks ){
			$this->tpl->assign_block_vars('permissions_row', array(
				'GROUP' => $group)
			);

			$a_set = $u_set = false;
			foreach ( $checks as $data ){
				//Superadmin-Permissions
				if (isset($superadm_only_perms[$data['CBNAME']]) && !isset($adm_memberships[2])) continue;

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


				if ($this->user->check_auth($data['CBNAME'], false, $user_id, false)){
					$perm = "user";
				}elseif (!$this->user->check_auth($data['CBNAME'], false, $user_id, false) && $this->user->check_auth($data['CBNAME'], false, $user_id) == true){
					$perm = "group";
				}else{
					$perm = false;
				}
				if(!$user_id) {
					$auth_defaults = $this->acl->get_auth_defaults();
					$perm = ($auth_defaults[$data['CBNAME']] == 'Y') ? 'user' : false;
				}

				$this->tpl->assign_block_vars(substr($data['CBNAME'], 0, 2).'permissions_row.check_group', array(
					'CBNAME'			=> $data['CBNAME'],
					'CBCHECKED'			=> ( $perm != false ) ? ' checked="checked"' : '',
					'S_IS_GROUP'		=> ( $perm == "group" ) ? true : false,
					'CLASS'				=> ( $perm != false ) ? 'positive' : 'negative',
					'TEXT'				=> $data['TEXT'],
				));
			}
		}

		unset($user_permissions);

		//Get all User-Permissions (without groups)
		$user_only_permissions = $this->acl->get_user_permissions($user_id, false);
		foreach ($user_only_permissions as $key=>$value){
			$this->tpl->assign_block_vars('user_permissions', array(
				'NAME' => $key)
			);
		}

		// Build member drop-down
		$freemember_data = $this->pdh->get('member', 'freechars', array($user_id));
		$mselect_list = $mselect_selected = array();
		foreach($freemember_data as $member_id => $member){
			$mselect_list[$member_id] = $member['name'];
			if($member['userid'] == $user_id && $user_id != 0){
				$mselect_selected[] = $member_id;
			}
		}

		//Build Group-dropdown
		$groups = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));

		asort($groups);
		if (is_array($groups)){
			foreach ($groups as $key=>$elem){
				$usergroups[$key] = $elem;
				$this->tpl->assign_block_vars('group_permissions', array(
					'KEY' => $key)
				);
				$group_permissions = $this->acl->get_group_permissions($key);
				foreach ($group_permissions as $name => $value){
					$this->tpl->assign_block_vars('group_permissions.group_permission_row', array(
						'NAME' => $name)
					);
				}
			}
		}
		
		// user field settings
		$settingsdata = $this->get_settingsdata($user_id);

		if($user_id) {
			$this->user_data = $this->pdh->get('user', 'data', array($user_id));
		} else {
			$this->user_data = array(
				'country'			=> '',
				'gender'			=> '',
				'user_alimit'		=> ($this->config->get('default_alimit')) ? $this->config->get('default_alimit') : 100,
				'user_climit'		=> ($this->config->get('default_climit')) ? $this->config->get('default_climit') : 100,
				'user_elimit'		=> ($this->config->get('default_elimit')) ? $this->config->get('default_elimit') : 100,
				'user_ilimit'		=> ($this->config->get('default_ilimit')) ? $this->config->get('default_ilimit') : 100,
				'user_nlimit'		=> ($this->config->get('default_nlimit')) ? $this->config->get('default_nlimit') : 10,
				'user_rlimit'		=> ($this->config->get('default_rlimit')) ? $this->config->get('default_rlimit') : 100,
				'user_lang'			=> $this->config->get('default_lang'),
				'user_style'		=> $this->config->get('default_style'),
				'user_timezone'		=> $this->config->get('timezone'),
				'user_date_long'	=> ($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->user->lang('style_date_long'),
				'user_date_short'	=> ($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->user->lang('style_date_short'),
				'user_date_time'	=> ($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->user->lang('style_date_time'),
				'user_timezone'		=> $this->config->get('timezone'),
			);
		}
		$privacy = ($user_id) ? $this->pdh->get('user', 'privacy_settings', array($user_id)) : array('priv_set' => 1, 'priv_phone' => 1);
		$custom = ($user_id) ? $this->pdh->get('user', 'custom_fields', array($user_id)) : array();
		$image = (isset($custom['user_avatar']) && $custom['user_avatar'] != '') ? $this->pfh->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp') : '';
		
		$this->user_data = array_merge($this->user_data, $privacy);
		$this->user_data = array_merge($this->user_data, $custom);

		$this->confirm_delete($this->user->lang('confirm_delete_users').'<br />'.((isset($this->user_data['username'])) ? sanitize($this->user_data['username']) : '').'<br /><label><input type="checkbox" name="delete_associated_members" value="1"> '. $this->user->lang('delete_associated members').'</label>', '', true, array('height'	=> 300, 'custom_js' => "if($('input[name=delete_associated_members]').is(':checked')){ window.location='manage_users.php".$this->SID."&del=true&user_id='+selectedID+'&del_assocmem=1';}else{ window.location='manage_users.php".$this->SID."&del=true&user_id='+selectedID;}"));
		$this->jquery->Tab_header('usersettings_tabs');
		$this->jquery->Tab_header('permission_tabs');
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));
		$this->jquery->spinner('#user_alimit, #user_climit, #user_ilimit, #user_rlimit, #user_elimit', array('step'=>10, 'multiselector'=>true));
		$this->jquery->spinner('user_nlimit');

		$this->tpl->assign_vars(array(
			// Form vars
			'F_SETTINGS'				=> 'manage_users.php' . $this->SID,
			'S_CURRENT_PASSWORD'		=> false,
			'S_SETTING_ADMIN'			=> true,
			'S_MU_TABLE'				=> true,
			'S_CREATE_NEW_PW'			=> ($user_id) ? true : false,
			'JS_TAB_SELECT'				=> $this->jquery->Tab_Select('usersettings_tabs', (($user_id) ? 3 : 0)),
			'S_PROTECT_USER'			=> ($this->user->data['user_id'] == $user_id || (isset($memberships[2]) && !isset($adm_memberships[2]))) ? true : false,
			'IMAGE_DELETE'				=> 'manage_users.php'.$this->SID.'&amp;uid='.stripslashes($_REQUEST['u']).'&amp;deleteavatar=true',
			'USER_ID'					=> $user_id,
			#'IMAGE_UPLOAD'				=> $logo_upload->Show('user_avatar', 'manage_users.php?performupload=true', $image, false),
			'S_IMAGE'					=> ($image != "") ? true: false,

			'USER_GROUP_SELECT'			=> $this->jquery->MultiSelect('user_groups', $usergroups, array_keys($memberships), array('width' => 400, 'height' => 250, 'filter' => true)),
			'JS_CONNECTIONS'			=> $this->jquery->MultiSelect('member_id', $mselect_list, $mselect_selected, array('width' => 400, 'height' => 250, 'filter' => true)),
			'ACTIVE_RADIO'				=> $this->html->RadioBox('user_active', array('1'	=> $this->user->lang('yes'), '0'	=> $this->user->lang('no')), ($user_id) ? $this->user_data['user_active'] : '1'),

			// Validation
			'VALIDTAELNK_PREFIX'		=> '../',
		));
		if($user_id) {
			$additional_fields = array(
				'registration_information'	=> array(
					'adduser_tab_registration_information' => array(
						'send_new_pw'	=> array(
							'name'	=> 'adduser_send_new_pw',
							'text'	=> $this->html->TextField('send_new_pw', '', $this->user->lang('adduser_send_new_pw'), 'submit', 'send_new_pw', 'mainoption bi_mail'),
							'help'	=> 'adduser_send_new_pw_note',
						),
					),
				),
			);
			$settingsdata = array_merge_recursive($settingsdata, $additional_fields);

			$this->tpl->assign_vars(array(
				//Validation
				'AJAXEXTENSION_USER'		=> '&olduser='.$this->user_data['username'],
				'AJAXEXTENSION_MAIL'		=> '&oldmail='.urlencode($this->user_data['user_email']),

				'L_SEND_MAIL2'				=> sprintf($this->user->lang('adduser_send_mail2'), $this->user_data['username']),
			));
		}

		// Output
		foreach($settingsdata as $tabname=>$fieldsetdata){
			$this->tpl->assign_block_vars('tabs', array(
				'NAME'	=> $this->user->lang('adduser_tab_'.$tabname),
				'ID'	=> $tabname,
			));

			foreach($fieldsetdata as $fieldsetname=>$fielddata){

				$this->tpl->assign_block_vars('tabs.fieldset', array(
					'NAME'		=> $this->user->lang($fieldsetname),
				));

				foreach($fielddata as $name=>$confvars){
					// continue if hmode == true
					if((isset($_HMODE) && $confvars['not4hmode'])){
						continue;
					}

					$no_lang = (isset($confvars['no_lang'])) ? true : false;
					$confvars['value'] = (isset($confvars['no_value'])) ? '' : (isset($this->user_data[$name]) ? $this->user_data[$name] : '');
					$this->tpl->assign_block_vars('tabs.fieldset.field', array(
						'NAME'		=> ((isset($confvars['required'])) ? '* ' : '').(($this->user->lang($confvars['name'])) ? $this->user->lang($confvars['name']) : $confvars['name']),
						'HELP'		=> ((isset($confvars['help'])) ? $this->user->lang($confvars['help']) : ''),
						'FIELD'		=> $this->html->widget($confvars),
						'TEXT'		=> isset($confvars['text']) ? $confvars['text'] : '',
					));
				}
			}
		}

		//Generate Plugin-Tabs
		if (is_array($this->pm->get_menus('settings'))){
			foreach ($this->pm->get_menus('settings') as $plugin => $values){
				$name	= ($values['name']) ? $values['name'] : $this->user->lang($plugin);
				$icon	= ($values['icon']) ? $values['icon'] : $this->root_path.'images/admin/plugin.png';
				unset($values['name'], $values['icon']);

				$this->tpl->assign_block_vars('plugin_settings_row', array(
					'KEY'		=> $plugin,
					'PLUGIN'	=> $name,
					'ICON'		=> $icon,
				));
				$this->tpl->assign_block_vars('plugin_usersettings_div', array(
					'KEY'		=> $plugin,
					'PLUGIN'	=> $name,
				));

				foreach ($values as $key=>$setting){
					$help				= (isset($setting['help']) AND $setting['help']) ? ( ($this->user->lang($setting['help'])) ? $this->user->lang($setting['help']) : $setting['help'] ) : '';
					$setting['value']	= $setting['selected'] = $this->user->data['plugin_settings'][$plugin][$setting['name']];
					$setting['name']	= $plugin.'['.$setting['name'].']';
					$setting['plugin']	= $plugin;

					$this->tpl->assign_block_vars('plugin_usersettings_div.plugin_usersettings', array(
						'NAME'	=> $this->user->lang($setting['language']),
						'FIELD'	=> $this->html->widget($setting),
						'HELP'	=> $help,
						'S_TH'	=> ($setting['type'] == 'tablehead') ? true : false,
					));
				}
			}
		}

		$this->tpl->assign_var('JS_TAB_SELECT', $this->jquery->Tab_Select('usersettings_tabs', (($user_id) ? 3+count($this->pm->get_menus('settings')) : 0)));

		$this->core->set_vars(array(
			'page_title'		=> ($user_id) ? $this->user->lang('manage_users').': '.sanitize($this->user_data['username']) : $this->user->lang('user_creation'),
			'template_file'		=> 'settings.html',
			'display'			=> true)
		);
	}
	
	private function get_settingsdata($user_id=-1) {
		//Privacy - Phone numbers
		$priv_phone_array = array(
			'0'=>$this->user->lang('user_priv_all'),
			'1'=>$this->user->lang('user_priv_user'),
			'2'=>$this->user->lang('user_priv_admin'),
			'3'=>$this->user->lang('user_priv_no')
		);

		$priv_set_array = array(
			'0'=>$this->user->lang('user_priv_all'),
			'1'=>$this->user->lang('user_priv_user'),
			'2'=>$this->user->lang('user_priv_admin')
		);

		$gender_array = array(
			'0'=> "---",
			'1'=> $this->user->lang('adduser_gender_m'),
			'2'=> $this->user->lang('adduser_gender_f')
		);

		$cfile = $this->root_path.'core/country_states.php';
		if (file_exists($cfile)){
			include($cfile);
		}

		// Build language array
		$language_array = array();
		if($dir = @opendir($this->core->root_path . 'language/')){
			while ( $file = @readdir($dir) ){
				if ((!is_file($this->core->root_path . 'language/' . $file)) && (!is_link($this->core->root_path . 'language/' . $file)) && valid_folder($file)){
					include($this->core->root_path.'language/'.$file.'/lang_main.php');
					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
					$language_array[$file]					= $lang_name_tp;
					$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
				}
			}
		}

		$style_array = array();
		foreach($this->pdh->get('styles', 'styles', array(0, false)) as $styleid=>$row){
			$style_array[$styleid] = $row['style_name'];
		}
		
		// hack the birthday format, to be sure there is a 4 digit year in it
		$birthday_format = $this->user->style['date_notime_short'];
		if(stripos($birthday_format, 'y') === false) $birthday_format .= 'Y';
		$birthday_format = str_replace('y', 'Y', $birthday_format);

		$settingsdata = array(
			'registration_information'	=> array(
				'adduser_tab_registration_information'	=> array(
					'username'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'username',
						'text'	=> '<img id="tick_username" src="'.$this->root_path.'images/register/tick.png" style="display:none;" width="16" height="16" alt="" />',
						'size'		=> 40,
						'required'	=> true,
						'id'	=> 'username',
					),
					'user_email' => array(
						'fieldtype'	=> 'text',
						'name'		=> 'email_address',
						'size'		=> 40,
						'required'	=> true,
						'id'		=> 'useremail',
					),
					'new_user_password1' => array(
						'fieldtype'	=> 'password',
						'name'		=> ($user_id) ?'new_password' : 'password',
						'size'		=> 40,
						'id'		=> 'password1',
						'help'		=> ($user_id) ? 'new_password_note' : 'user_creation_password_note',
					),
					'new_user_password2' => array(
						'fieldtype'	=> 'password',
						'name'		=> 'confirm_password',
						'size'		=> 40,
						'help'		=> ($user_id) ? 'confirm_password_note' : 'user_creation_password_note',
						'id'		=> 'password2',
					),

				),
			),
			'profile'	=> array(
				'adduser_tab_profile'	=> array(
					'first_name'	=> array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_first_name',
						'size'		=> 40,
						'id'		=> 'first_name',
					),
					'last_name'	=> array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_last_name',
						'size'		=> 40,
					),
					'gender' => array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'adduser_gender',
						'options'	=> $gender_array,
						'id'		=> 'gender',
					),
					'country' => array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'adduser_country',
						'options'	=> $country_array,
						'no_lang'	=> true,
						'id'		=> 'country',
					),
					'state' => array(
						'fieldtype'	=> 'text',
						'name'		=> 'adduser_state',
						'options'	=> $country_array,
						'no_lang'	=> true,
					),
					'ZIP_code'	=> array(
						'fieldtype'	=> 'int',
						'size'	=> 5,
						'name'	=> 'adduser_ZIP_code'
					),
					'town'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_town',
						'size'	=> 40,
					),
					'phone'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_phone',
						'size'	=> 40,
						'help'	=> 'adduser_foneinfo',
					),
					'cellphone'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_cellphone',
						'size'	=> 40,
						'help'	=> 'adduser_cellinfo',
					),
					'icq'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_icq',
						'size'	=> 40,
					),
					'skype'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_skype',
						'size'	=> 40,
					),
					'msn'=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_msn',
						'size'	=> 40,
					),
					'irq'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_irq',
						'size'	=> 40,
						'help'	=> 'register_help_irc',
					),
					'twitter'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_twitter',
						'size'	=> 40,
					),
					'facebook'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_facebook',
						'size'	=> 40,
					),
					'birthday'	=> array(
						'fieldtype'	=> 'datepicker',
						'name'	=> 'adduser_birthday',
						'allow_empty' => true,
						'options' => array(
							'year_range' => '-80:+0',
							'change_fields' => true,
							'format' => $birthday_format
						),
					),
					'user_avatar'	=> array(
						'fieldtype'	=> 'imageuploader',
						'name'		=> 'user_image',
						'imgpath'	=> $this->pfh->FolderPath('user_avatars','eqdkp'),
						'options'	=> array('deletelink'	=> 'manage_users.php'.$this->SID.'&u='.$user_id.'&mode=deleteavatar'),
					),
					'work'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_work',
						'size'	=> 40,
					),
					'interests'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_interests',
						'size'	=> 40,
					),
					'hardware'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'user_hardware',
						'size'	=> 40,
					),

				),
				'user_priv'	=> array(
					'priv_no_boardemails'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_no_boardemails',
					),
					'priv_set'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'user_priv_set_global',
						'options'	=> $priv_set_array,
					),
					'priv_phone'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'user_priv_tel_all',
						'options'	=> $priv_phone_array,
					),
					'priv_nosms'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_tel_sms',
					),
					'priv_bday'	=> array(
						'fieldtype'	=> 'checkbox',
						'default'	=> 0,
						'name'		=> 'user_priv_bday',
					),

				),
			),
			'view_options'		=> array(
				'adduser_tab_view_options'	=> array(
					'user_alimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'adjustments_per_page',
						'size'	=> 5,
						'step'	=> 10,
						'id'	=> 'user_alimit'
					),
					'user_climit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'characters_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_climit'
					),
					'user_elimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'events_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_elimit'
					),
					'user_ilimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'items_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_ilimit'
					),
					'user_rlimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'raids_per_page',
						'size'	=> 5,
						'step' => 10,
						'id'	=> 'user_rlimit'
					),
					'user_nlimit'	=> array(
						'fieldtype'	=> 'spinner',
						'name'	=> 'news_per_page',
						'size'	=> 5,
						'id'	=> 'user_nlimit'
					),
					'user_lang'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'	=> 'language',
						'options'	=> $language_array,
					),
					'user_timezone'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'user_timezones',
						'options'	=> $this->time->timezones,
					),

					'user_style'	=> array(
						'fieldtype'	=> 'dropdown',
						'name'		=> 'style',
						'options'	=> $style_array,
						'text'		=> ' (<a href="javascript:template_preview()">'.$this->user->lang('preview').'</a>)',
						'no_lang'	=> true,
					),
					'user_date_time'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_time',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
					'user_date_short'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_short',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
					'user_date_long'	=> array(
						'fieldtype'	=> 'text',
						'name'	=> 'adduser_date_long',
						'size'	=> 40,
						'help'	=> 'adduser_date_note',
					),
				),
			),
		);
		return $settingsdata;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Users', Manage_Users::__shortcuts());
registry::register('Manage_users');
?>