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
	public static $shortcuts = array('email' => 'MyMailer', 'crypt' => 'encrypt', 'form' => array('form', array('user_edit_settings')));
	
	public function __construct(){
		$this->user->check_auth('a_users_man');
		$handler = array(
			'mode' => array(
				array('process' => 'activate', 'value' => 'activate', 'csrf' => true),
				array('process' => 'deactivate', 'value' => 'deactivate', 'csrf' => true),
				array('process' => 'overtake_permissions', 'value' => 'ovperms', 'csrf' => true),
				),
			'submit' => array('process' => 'submit', 'csrf' => true),
			'send_new_pw' => array('process' => 'send_new_pw', 'csrf' => true),
			'u' => array('process' => 'edit'),
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
			'USERNAME'		=> $this->pdh->get('user', 'name', array($this->in->get('u', 0))),
			'DATETIME'		=> $this->time->user_date(),
			'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'/Login/NewPassword/&amp;key=' . $pwkey,
		);

		if($this->email->SendMailFromAdmin($this->in->get('user_email'), $this->user->lang('email_subject_new_pw'), 'user_new_password.html', $bodyvars)) {
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
		
		$this->create_form($user_id);
		$values = $this->form->return_values();
		// Error-check the form
		$change_username = ( $values['username'] != $this->in->get('old_username') ) ? true : false;
		$change_password = ( $values['new_password'] != '' || $values['confirm_password'] != '') ? true : false;
		$change_email = ( $values['user_email'] != $this->pdh->get('user', 'email', array($user_id)) ) ? true : false;

		// Check username
		if ($change_username && $this->pdh->get('user', 'check_username', array($values['username'])) == 'false'){
			$this->core->message(str_replace('{0}', $values['username'], $this->user->lang('fv_username_alreadyuse')), $this->user->lang('error'), 'red');
			$this->edit();
			return;
		}

		// Check email
		if($change_email) {
			if ($this->pdh->get('user', 'check_email', array($values['user_email'])) == 'false'){
				$this->core->message(str_replace('{0}', $values['user_email'], $this->user->lang('fv_email_alreadyuse')), $this->user->lang('error'), 'red');
				$this->edit();
				return;
			} elseif ( !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $values['user_email']) ){
				$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
				$this->edit();
				return;
			}
		}
		
		//Check matching new passwords
		if($change_password) {
			if($values['new_password'] != $values['confirm_password']) {
				$this->core->message($this->user->lang('password_not_match'), $this->user->lang('error'), 'red');
				$this->edit();
				return;
			}
		}
		if ($change_password && strlen($values['new_password']) > 64) {
			$this->core->message($this->user->lang('password_too_long'), $this->user->lang('error'), 'red');
			$this->edit();
			return;
		}
		
		//The Group-Memberships of the admin who has submitted this form
		$adm_memberships   = $this->acl->get_user_group_memberships($this->user->id);

		if($user_id) {
			//Prevent edit of Superadmin
			if (!isset($adm_memberships[2]) && $this->user->check_group(2, false, $user_id)){
				$this->display();
				return;
			}
			$query_ary = array();
			if ( $change_username ) $query_ary['username'] = $values['username'];
			if ( $change_password ) {
				$new_salt = $this->user->generate_salt();
				$query_ary['user_password'] = $this->user->encrypt_password($values['new_password'], $new_salt).':'.$new_salt;
				$strApiKey = $this->user->generate_apikey($values['new_password'], $new_salt);
				$query_ary['api_key'] = $strApiKey;
				$query_ary['user_login_key'] = '';
			}

			$query_ary['user_email']	= $this->encrypt->encrypt($values['user_email']);
			
			$plugin_settings = array();
			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $options){
			
					foreach ($values as $key=>$setting){
						$plugin_settings[] = $key;
					}
				}
			}
			
			//copy all other values to appropriate array
			$ignore = array('username', 'user_email', 'current_password', 'new_password', 'confirm_password');
			$privArray = array();
			$customArray = array();
			$pluginArray = array();
			foreach($values as $name => $value) {
				if(in_array($name, $ignore)) continue;
				if(in_array($name, user_core::$privFields)) 
					$privArray[$name] = $value;
				elseif(in_array($name, user_core::$customFields)) 
					$customArray[$name] = $value;
				elseif(in_array($name, $plugin_settings))
					$pluginArray[$name] = $value;
				else 
					$query_ary[$name] = $value;
			}
			
			//Create Thumbnail for User Avatar
			if ($customArray['user_avatar'] != "" && $this->pdh->get('user', 'avatar', array($user_id)) != $customArray['user_avatar']){
				$image = $this->pfh->FolderPath('users/'.$user_id,'files').$customArray['user_avatar'];
				$this->pfh->thumbnail($image, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$user_id.'_68.'.pathinfo($image, PATHINFO_EXTENSION), 68);
			}
			
			$query_ary['privacy_settings']		= serialize($privArray);
			$query_ary['custom_fields']			= serialize($customArray);
			$query_ary['plugin_settings']		= serialize($pluginArray);
			unset($query_ary['send_new_pw']);
			
			$this->pdh->put('user', 'update_user', array($user_id, $query_ary));
			$this->pdh->put('user', 'activate', array($user_id, $this->in->get('user_active', 0)));
		} else {
			$password = ($values['new_password'] == "") ? random_string() : $values['new_password'];
			$new_salt = $this->user->generate_salt();
			$new_password = $this->user->encrypt_password($password, $new_salt).':'.$new_salt;
			
			$query_ar = array(
				'username'				=> $this->in->get('username'),
				'user_password'			=> $new_password,
				'user_email'			=> $this->crypt->encrypt($values['user_email'])
			);
			
			$plugin_settings = array();
			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $options){
			
					foreach ($values as $key=>$setting){
						$plugin_settings[] = $key;
					}
				}
			}
			
			$privArray = array();
			$customArray = array();
			$pluginArray = array();
			$ignore = array('username', 'user_email', 'current_password', 'new_password', 'confirm_password');
			$custom_fields = array('user_avatar', 'work', 'interests', 'hardware', 'facebook', 'twitter');
			foreach($values as $name => $value) {
				if(in_array($name, $ignore)) continue;
				if(in_array($name, user_core::$privFields)) 
					$privArray[$name] = $value;
				elseif(in_array($name, user_core::$customFields)) 
					$customArray[$name] = $value;
				elseif(in_array($name, $plugin_settings))
					$pluginArray[$name] = $value;
				else 
					$query_ar[$name] = $value;
			}
			
			//Create Thumbnail for User Avatar
			if ($customArray['user_avatar'] != "" && $this->pdh->get('user', 'avatar', array($user_id)) != $customArray['user_avatar']){
				$image = $this->pfh->FolderPath('users/'.$user_id,'files').$customArray['user_avatar'];
				$this->pfh->thumbnail($image, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$user_id.'_68.'.pathinfo($image, PATHINFO_EXTENSION), 68);
			}
			

			$query_ar['privacy_settings']	= serialize($privArray);
			$query_ar['custom_fields']		= serialize($customArray);
			$query_ar['plugin_settings']	= serialize($pluginArray);
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
		$auths_to_update = $arrChanged = array();
		foreach ( $auth_defaults as $auth_value => $auth_setting ){
			$r_auth_id    = $this->acl->get_auth_id($auth_value);
			$r_auth_value = $auth_value;

			$chk_auth_value = ( !$new_user AND $this->user->check_auth($r_auth_value, false, $user_id, false) ) ? 'Y' : 'N';
			$db_auth_value  = ( $this->in->exists($r_auth_value) ) ? 'Y' : 'N';

			if ( $chk_auth_value != $db_auth_value ){
				$auths_to_update[$r_auth_id] = $db_auth_value;
				$arrChanged[$r_auth_value] = array('old' => $chk_auth_value, 'new' => $db_auth_value);
			}
		}
		if(count($auths_to_update) > 0)	{
			$this->acl->update_user_permissions($auths_to_update, $user_id);
			$this->logs->add('action_user_changed_permissions', $arrChanged, $user_id, $this->pdh->get('user', 'name', array($user_id)));
		}

		$this->pdh->put('member', 'update_connection', array($this->in->getArray('member_id', 'int'), $user_id));

		// User-Groups
		$groups = $this->in->getArray('user_groups', 'int');
		$group_list = $this->pdh->get('user_groups', 'id_list', 0);
		$arrMemberships = array_keys($this->acl->get_user_group_memberships($user_id));
		$this->pdh->put('user_groups_users', 'delete_user_from_groups', array($user_id, $group_list));
		$this->pdh->put('user_groups_users', 'add_user_to_groups', array($user_id, $groups));
		$arrayRemoved = array_diff($arrMemberships, $groups);
		$arrayNew = array_diff($groups, $arrMemberships);
		if (count($arrayRemoved)) $this->logs->add("action_user_removed_group", array("{L_GROUPS}" => implode(", ", $this->pdh->aget('user_groups', 'name', 0, array($arrayRemoved)))), $user_id, $this->pdh->get('user', 'name', array($user_id)));
		if (count($arrayNew)) $this->logs->add("action_user_added_group", array("{L_GROUPS}" => implode(", ", $this->pdh->aget('user_groups', 'name', 0, array($arrayNew)))), $user_id, $this->pdh->get('user', 'name', array($user_id)));

		// E-mail the user if he/she was activated by the admin and admin activation was set in the config
		$email_success_message = '';
		if ($password OR ($this->config->get('account_activation') == 2 ) && ( $this->pdh->get('user', 'active', array($user_id)) < $this->in->get('user_active'))){

			// Email them their new password
			$this->email->Set_Language($values['user_lang']);

			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($user_id));
			if(!strlen($user_key)) {
				$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
			}
			$strPasswordLink = $this->env->link . 'login.php?mode=newpassword&key=' . $user_key;

			$bodyvars = array(
				'USERNAME'	=> $values['username'],
				'U_ACTIVATE'=> ($password) ? $this->user->lang('email_changepw').'<br /><br /><a href="'.$strPasswordLink.'">'.$strPasswordLink.'</a>' : '',
				'GUILDTAG'	=> $this->config->get('guildtag'),
			);
			if($this->email->SendMailFromAdmin($values['user_email'], $this->user->lang('email_subject_activation_none'), 'register_activation_none.html', $bodyvars)) {
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
		
		$objQuery = $this->db->query("SELECT session_user_id FROM __sessions;");
		if ($objQuery){
			while ( $row = $objQuery->fetchAssoc() ) {
				$online_users[] = $row['session_user_id'];
			}
		}

		$adm_memberships = $this->acl->get_user_group_memberships($this->user->data['user_id']);
		$k = 0;
		foreach($user_ids as $user_id) {
			if($k < $start) {
				$k++;
				continue;
			}
			if($k >= ($start+100)) break;
			$user_online = (in_array($user_id, $online_users)) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';
			if($this->pdh->get('user', 'active', array($user_id))) {
				$user_active = '<i class="eqdkp-icon-online"></i>';
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=deactivate&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('deactivate').'"><i class="fa fa-check-square-o fa-lg icon-color-green"></i></a>';
			} else {
				$user_active = '<i class="eqdkp-icon-offline"></i>';
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=activate&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('activate').'"><i class="fa fa-square-o fa-lg icon-color-red"></i></a>';
			}
			$user_memberships = $this->pdh->get('user_groups_users', 'memberships', array($user_id));
$a_members = $this->pdh->get('member', 'connection_id', array($user_id));
			$a_members = (is_array($a_members)) ? $this->pdh->maget('member', array('classid', 'name', 'rankname'), 0, array($a_members), null, false, true) : array();
			
			$this->tpl->assign_block_vars('users_row', array(
				'U_MANAGE_USER'		=> 'manage_users.php'.$this->SID.'&amp;' . 'u' . '='.$user_id,
				'U_OVERTAKE_PERMS'	=> 'manage_users.php'.$this->SID.'&amp;mode=ovperms&amp;' . 'u' . '='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'U_DELETE'			=> 'manage_users.php'.$this->SID.'&amp;del=single&amp;user_id='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('del'),
				'USER_ID'			=> $user_id,
				'NAME_STYLE'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? 'font-weight: bold' : 'font-weight: none',
				'ADMIN_ICON'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? '<span class="adminicon"></span> ' : '',
				'USERNAME'			=> $this->pdh->get('user', 'name', array($user_id)),
				'EMAIL'				=> $this->pdh->get('user', 'email', array($user_id)),
				'LAST_VISIT'		=> ($this->pdh->get('user', 'last_visit', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'last_visit', array($user_id)), true) : '',
				'REG_DATE'		=> ($this->pdh->get('user', 'regdate', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'regdate', array($user_id)), true) : '',
				'PROTECT_SUPERADMIN'=> ((is_array($user_memberships) && in_array(2, $user_memberships) && !isset($adm_memberships[2])) || ($user_id == $this->user->data['user_id'])) ? true : false,
				'ACTIVE'			=> $user_active,
				'ACTIVATE_ICON'		=> $activate_icon,
				'ONLINE'			=> $user_online,
				'MEMBER_COUNT'		=> count($a_members),
				)
			);
			
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
		$onclose_url = "if(event.originalEvent == undefined) { window.location.href = 'admin/manage_users.php".$this->SID."'; } else { window.location.href = 'manage_users.php".$this->SID."'; }";
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
				'GROUP' => $group,
			));
			
			$icon = (isset($checks['icon'])) ? $this->core->icon_font($checks['icon']) : '';

			$a_set = $u_set = false;
			foreach ( $checks as $data ){
				if (!is_array($data)) continue;
			
				//Superadmin-Permissions
				if (isset($superadm_only_perms[$data['CBNAME']]) && !isset($adm_memberships[2])) continue;

				switch (substr($data['CBNAME'], 0, 2)){
					case 'a_': if (!$a_set){
									$this->tpl->assign_block_vars('a_permissions_row', array(
										'GROUP' => $group,
										'ICON'	=> $icon,
									));
									$a_set = true;
								}
					break;

					case 'u_': if (!$u_set){
									$this->tpl->assign_block_vars('u_permissions_row', array(
										'GROUP' => $group,
										'ICON'	=> $icon,
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
		$this->create_form($user_id);
		// user field values
		$user_data = array();
		if($user_id > 0) {
			$user_data = $this->pdh->get('user', 'data', array($user_id));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'privacy_settings', array($user_id)));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'custom_fields', array($user_id)));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'plugin_settings', array($user_id)));
		}
		$this->confirm_delete($this->user->lang('confirm_delete_users').'<br />'.((isset($user_data['username'])) ? sanitize($user_data['username']) : '').'<br /><label><input type="checkbox" name="delete_associated_members" value="1"> '. $this->user->lang('delete_associated members').'</label>', '', true, array('height'	=> 300, 'custom_js' => "if($('input[name=delete_associated_members]').is(':checked')){ window.location='manage_users.php".$this->SID."&del=true&user_id='+selectedID+'&del_assocmem=1';}else{ window.location='manage_users.php".$this->SID."&del=true&user_id='+selectedID;}"));
		$this->jquery->Tab_header('usersettings_tabs');
		$this->jquery->Tab_header('permission_tabs');
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));

		$this->tpl->assign_vars(array(
			// Form vars
			'S_SETTING_ADMIN'			=> true,
			'S_MU_TABLE'				=> true,
			'JS_TAB_SELECT'				=> $this->jquery->Tab_Select('usersettings_tabs', (($user_id) ? 3 : 0)),
			'S_PROTECT_USER'			=> ($this->user->data['user_id'] == $user_id || (isset($memberships[2]) && !isset($adm_memberships[2]))) ? true : false,
			'USERID'					=> $user_id,
			'USERNAME'					=> $user_data['username'],

			'USER_GROUP_SELECT'			=> $this->jquery->MultiSelect('user_groups', $usergroups, array_keys($memberships), array('width' => 400, 'height' => 250, 'filter' => true)),
			'JS_CONNECTIONS'			=> $this->jquery->MultiSelect('member_id', $mselect_list, $mselect_selected, array('width' => 400, 'height' => 250, 'filter' => true)),
			'ACTIVE_RADIO'				=> new hradio('user_active', array('value' => (($user_id) ? $user_data['user_active'] : true))),

			// Validation
			'VALIDTAELNK_PREFIX'		=> '../',
		));
		if($user_id) {
			$this->tpl->assign_vars(array(
				//Validation
				'AJAXEXTENSION_USER'		=> '&olduser='.urlencode($user_data['username']),
				'AJAXEXTENSION_MAIL'		=> '&oldmail='.urlencode($user_data['user_email']),

				'L_SEND_MAIL2'				=> sprintf($this->user->lang('adduser_send_mail2'), $user_data['username']),
			));
		}

		// Output
		$this->form->output($user_data);


		$this->tpl->assign_var('JS_TAB_SELECT', $this->jquery->Tab_Select('usersettings_tabs', (($user_id) ? 3+count($this->pm->get_menus('settings')) : 0)));

		$this->core->set_vars(array(
			'page_title'		=> ($user_id) ? $this->user->lang('manage_users').': '.sanitize($user_data['username']) : $this->user->lang('user_creation'),
			'template_file'		=> 'settings.html',
			'display'			=> true)
		);
	}
	
	private function create_form($user_id) {
		// initialize form class
		$this->form->lang_prefix = 'user_sett_';
		$this->form->use_tabs = true;
		$this->form->use_fieldsets = true;
		
		$settingsdata = user_core::get_settingsdata($user_id);
		// get rid of current_password field
		unset($settingsdata['registration_info']['registration_info']['current_password']);
		// vary help messages for user creation
		if($user_id <= 0) {
			$settingsdata['registration_info']['registration_info']['new_password']['help'] = 'user_creation_password_note';
			$settingsdata['registration_info']['registration_info']['confirm_password']['help'] = 'user_creation_password_note';
		}
		// add deletelink for user-avatar
		$settingsdata['profile']['user_avatar']['user_avatar']['deletelink'] = 'manage_users.php'.$this->SID.'&u='.$user_id.'&mode=deleteavatar';
		
		$this->form->add_tabs($settingsdata);
		// add send-new-password-button (if editing user)
		if($user_id > 0) {
			$this->form->add_field('send_new_pw', array('type' => 'button', 'buttontype' => 'submit', 'class' => 'mainoption bi_mail', 'buttonvalue' => 'user_sett_f_send_new_pw', 'tolang' => true), 'registration_info', 'registration_info');
		}
		
		//Plugin Settings
		if (is_array($this->pm->get_menus('settings'))){
			$arrPluginSettings = array();
			foreach ($this->pm->get_menus('settings') as $plugin => $values){
				unset($values['name'], $values['icon']);
				foreach($values as $key => $setting){
					$arrPluginSettings[$plugin][$key] = $setting;
				}
		
			}
			$this->form->add_tabs($arrPluginSettings);
		}
	}
}
registry::register('Manage_users');
?>