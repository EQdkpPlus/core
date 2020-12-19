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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Users extends page_generic {
	public static $shortcuts = array('email' => 'MyMailer', 'crypt' => 'encrypt', 'form' => array('form', array('user_edit_settings')));

	public function __construct(){
		$this->user->check_auth('a_users_man');
		$handler = array(
			'maincharchange' => array('process' => 'maincharchange', 'csrf' => true),
			'mode' => array(
				array('process' => 'delete_authaccount', 'value' => 'delauthacc', 'csrf' => true),
				array('process' => 'activate', 'value' => 'activate', 'csrf' => true),
				array('process' => 'force_email_confirm', 'value' => 'forceemail', 'csrf' => true),
				array('process' => 'unlock', 'value' => 'unlock', 'csrf' => true),
				array('process' => 'lock', 'value' => 'lock', 'csrf' => true),
				array('process' => 'overtake_permissions', 'value' => 'ovperms', 'csrf' => true),
				array('process' => 'resolve_permissions', 'value' => 'resolveperms'),
			),
			'groupc' => array('process' => 'bulk_usergroups', 'csrf'=>true),
			'search' =>	array('process' => 'search', 'csrf' => false),
			'export_gdpr'  =>	array('process' => 'export_gdpr', 'csrf' => false),
			'submit_search' =>	array('process' => 'process_search', 'csrf' => true),
			'bulk_lock' =>	array('process' => 'bulk_lock', 'csrf' => true),
			'bulk_unlock' => array('process' => 'bulk_unlock', 'csrf' => true),
			'bulk_confirmemail' =>	array('process' => 'bulk_confirmemail', 'csrf' => true),
			'bulk_activate' => array('process' => 'bulk_activate', 'csrf' => true),
			'submit' => array('process' => 'submit', 'csrf' => true),
			'send_new_pw' => array('process' => 'send_new_pw', 'csrf' => true),
			'u' => array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('user', 'name'), null, 'user_id[]');
		$this->process();
	}

	public function bulk_unlock(){
		$user_ids = $this->in->getArray('user_id', 'int');

		foreach($user_ids as $intUserID){
			$this->pdh->put('user', 'activate', array($intUserID, 1));
		}
		$this->core->message($this->user->lang('bulk_user_unlock_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function bulk_lock(){
		$user_ids = $this->in->getArray('user_id', 'int');

		foreach($user_ids as $intUserID){
			$this->pdh->put('user', 'activate', array($intUserID, 0));
		}
		$this->core->message($this->user->lang('bulk_user_lock_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function bulk_activate(){
		$user_ids = $this->in->getArray('user_id', 'int');

		foreach($user_ids as $intUserID){
			$this->pdh->put('user', 'confirm_email', array($intUserID, 1));
			
			//Send Confirmation Email if Admin Activation is enabled
			if((int)$this->config->get('account_activation') == 2){
				//Send out notification email
				$bodyvars = array(
						'USERNAME'		=> $this->pdh->get('user', 'name', array($intUserID)),
						'GUILDTAG'		=> $this->config->get('guildtag'),
				);
				
				$this->email->Set_Language($this->pdh->get('user', 'lang', array($intUserID)));
				
				$this->email->SendMailFromAdmin($this->pdh->get('user', 'email', array($intUserID)), $this->user->lang('email_subject_activation_none'), 'register_account_activated.html', $bodyvars);
						
			}
		}
		$this->core->message($this->user->lang('bulk_user_activate_success'), $this->user->lang('success'), 'green');


		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function bulk_confirmemail(){
		$user_ids = $this->in->getArray('user_id', 'int');
		$email = registry::register('MyMailer');

		foreach($user_ids as $intUserID){
			$this->pdh->put('user', 'confirm_email', array($intUserID, 0));
			//Send the User an Email with activation link
			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($intUserID));
			$username = $this->pdh->get('user', 'name', array($intUserID));

			// Email them their new key
			$bodyvars = array(
					'USERNAME'		=> $username,
					'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'Register/Activate/?key=' . $user_key,
			);
			$email->SendMailFromAdmin($this->pdh->get('user', 'email', array($intUserID)), $this->user->lang('email_subject_email_confirm'), 'user_email_confirm.html', $bodyvars);
		}
		$this->core->message($this->user->lang('bulk_user_forceemailconfirm_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}
	
	public function bulk_usergroups(){		
		$intGroupID = $this->in->get('groups',0);
		
		$sucs = array();
		if($member_ids = $this->in->getArray('user_id','int')){
			foreach($member_ids as $id){
				$this->pdh->put('user_groups_users', 'add_user_to_group', array($id, $intGroupID));				
			}
		}
		$this->core->message($this->user->lang('bulk_user_usergroupsadded_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}
	


	public function search(){

		$this->tpl->assign_vars(array(
				'SPINNER_CHAR_COUNT' => (new hspinner('charcount'))->output(),
				'DATEPICKER_BEFORE'	=> (new hdatepicker('date_before', array('value' => false)))->output(),
				'DATEPICKER_AFTER'	=> (new hdatepicker('date_after', array('value' => false)))->output(),
		));


		$arrUsers = $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list', array(false))));

		$arrMembers = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false,true,false))));


		$this->jquery->Autocomplete('name', $arrUsers);
		$this->jquery->Autocomplete('charname', $arrMembers);

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manage_users_search'),
				'template_file'		=> 'admin/manage_users_search.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('manage_users'), 'url'=> $this->root_path.'admin/manage_users.php'.$this->SID],
						['title'=>$this->user->lang('manage_users_search'), 'url'=> ''],
				],
				'display'			=> true
		]);

	}

	public function export_gdpr(){
		$intUserID = $this->in->get('u', 0);

		//User data
		$arrUserdata = $this->pdh->get('user', 'data', array($intUserID, true));

		$arrUserdata['email'] = $this->pdh->get('user', 'email', array($intUserID));
		$hideArray = array('user_password', 'user_login_key', 'user_email','user_email_confirmkey', 'salt', 'password', 'exchange_key', 'user_email_clean');
		foreach($hideArray as $entry){
			if(isset($arrUserdata[$entry])) unset($arrUserdata[$entry]);
		}
		$arrUserdata['custom_fields'] = unserialize_noclasses($arrUserdata['custom_fields']);
		$arrUserdata['plugin_settings'] = unserialize_noclasses($arrUserdata['plugin_settings']);
		$arrUserdata['privacy_settings'] = unserialize_noclasses($arrUserdata['privacy_settings']);
		$arrUserdata['notifications'] = unserialize_noclasses($arrUserdata['notifications']);
		$arrUserdata['usergroups'] = $this->pdh->get('user_groups_users', 'memberships', array($this->user->id));
		$arrUserdata['avatar_big'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($this->user->id, true)));
		$arrUserdata['avatar_small'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($this->user->id, false)));

		//Session data
		$arrSession = array();
		$objQuery = $this->db->prepare("SELECT * FROM __sessions WHERE session_user_id=?")->execute($intUserID);
		if($objQuery){
			while($row = $objQuery->fetchAssoc()){
				unset($row['session_key']);
				$arrSession[] = $row;
			}
		}

		//Logs
		$arrLogs = array();
		$objQuery = $this->db->prepare("SELECT * FROM __logs WHERE user_id=?")->execute($intUserID);
		if($objQuery){
			while($row = $objQuery->fetchAssoc()){
				$anonIP = anonymize_ipaddress($row['log_ipaddress']);
				if($anonIP == $row['log_ipaddress']) continue;

				$arrLogs[] = array(
						'date'  => $row['log_date'],
						'ip'	=> $row['log_ipaddress'],
				);
			}
		}

		//Hooks
		$arrOutHooks = array();
		if($this->hooks->isRegistered('user_export_gdpr')){
			$arrHooks = $this->hooks->process('user_export_gdpr', array('user_id' => $intUserID));

			foreach($arrHooks as $key => $val){
				$strNewKey = str_replace('_user_export_gdpr_hook', '', $key);
				$arrOutHooks[$strNewKey] = $val;
			}
		}


		$arrOutdata = array('userobject' => $arrUserdata, 'sessions' => $arrSession, 'logs' => $arrLogs, 'extensions' => $arrOutHooks, 'info' => array(
				'created' => time(),
				'system' => 'EQdkp Plus',
				'version' => VERSION_EXT,
		));

		$strJson = json_encode($arrOutdata, JSON_PRETTY_PRINT);

		header('Content-Type: application/octet-stream');
		header('Content-Length: '.strlen($strJson));
		header('Content-Disposition: attachment; filename="export_gdpr_user'.$intUserID.'_'.time().'.json"');
		header('Content-Transfer-Encoding: binary');
		echo $strJson;

		die();
	}

	public function process_search(){
		//I will process each search, and merge the found user array later

		$arrUserIDs = $this->pdh->get('user', 'id_list', array(false));
		$arrResults = array(
				'name' => false,
				'email' => false,
				'date_before' => false,
				'date_after' => false,
				'charname' => false,
				'charcount' => false,
				'locked' => false,
				'not_confirmed' => false,
		);

		//Username
		$strSearchName = utf8_strtolower($this->in->get('name'));
		if($strSearchName != ""){
			$arrResults['name'] = array();
			foreach($arrUserIDs as $intUserID){
				$arrUserData = $this->pdh->get('user', 'data', array($intUserID));

				if(stripos($arrUserData['username'], $strSearchName) !== false OR stripos($arrUserData['username_clean'], $strSearchName) !== false) {
					$arrResults['name'][] = $intUserID;
				}

			}
		}

		//Useremail
		$strSearchEmail = utf8_strtolower($this->in->get('email'));
		if($strSearchEmail != ""){
			$arrResults['email'] = array();
			foreach($arrUserIDs as $intUserID){
				$arrUserData = $this->pdh->get('user', 'data', array($intUserID, true));

				if(stripos($arrUserData['user_email'], $strSearchEmail) !== false) {
					$arrResults['email'][] = $intUserID;
				}

			}
		}

		//Date before
		$strBeforeDate = $this->in->get('date_before');
		if($strBeforeDate){
			$arrResults['date_before'] = array();
			$intTime = $this->time->fromformat($strBeforeDate, 0);

			foreach($arrUserIDs as $intUserID){
				$intRegDate = $this->pdh->get('user', 'regdate', array($intUserID));

				if($intRegDate < $intTime) $arrResults['date_before'][] = $intUserID;
			}
		}


		//Date after
		$strAfterDate = $this->in->get('date_after');
		if($strAfterDate){
			$arrResults['date_after'] = array();
			$intTime = $this->time->fromformat($strAfterDate, 0);

			foreach($arrUserIDs as $intUserID){
				$intRegDate = $this->pdh->get('user', 'regdate', array($intUserID));

				if($intRegDate > $intTime) $arrResults['date_after'][] = $intUserID;
			}
		}

		//Charname
		$strCharname = utf8_strtolower($this->in->get('charname'));
		$arrChars = $this->pdh->get('member', 'id_list');
		if($strCharname != ""){
			$arrResults['charname'] = array();
			foreach($arrChars as $intCharID){
				$strMyCharname = $this->pdh->get('member', 'name', array($intCharID));

				if(stripos($strMyCharname, $strCharname) !== false) {
					//Find Owner
					$intOwner = $this->pdh->get('member', 'userid', array($intCharID));
					if($intOwner > 0) $arrResults['charname'][] = $intOwner;
				}
			}
		}



		//Charcount
		$charCountExist = $this->in->exists('charcount');
		$intCharcount = $this->in->get('charcount');
		if(strlen($intCharcount)){
			$intCharcount = intval($intCharcount);
			$arrResults['charcount'] = array();

			foreach($arrUserIDs as $intUserID){
				$a = $this->pdh->get('member', 'connection_id', array($intUserID));

				$count = (is_array($a)) ? count($a) : 0;

				if($intCharcount == $count){
					$arrResults['charcount'][] = $intUserID;
				}
			}
		}

		//Locked
		$arrStatus = $this->in->getArray('status');

		if(in_array('locked',$arrStatus ) ){
			$arrResults['locked'] = array();
			foreach($arrUserIDs as $intUserID){
				$intActive = $this->pdh->get('user', 'active', array($intUserID));

				if(!$intActive) $arrResults['locked'][] = $intUserID;
			}
		}

		if(in_array('notconfirmed',$arrStatus ) ){
			$arrResults['not_confirmed'] = array();
			foreach($arrUserIDs as $intUserID){
				$intActive = $this->pdh->get('user', 'email_confirmed', array($intUserID));

				if(!$intActive) $arrResults['not_confirmed'][] = $intUserID;
			}
		}

		//Now combine the search results
		$intFalseCount = 0;
		$arrOutResult = $arrUserIDs;
		foreach($arrResults as $key => $val){
			if($val === false) {
				$intFalseCount++;
			} else {
				$arrOutResult = array_intersect($arrOutResult, $val);
			}
		}

		$this->display($arrOutResult);
	}


	public function send_new_pw(){
		$pwkey = $this->pdh->put('user', 'create_new_activationkey', array($this->in->get('u')));
		if(!strlen($pwkey)) {
			$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
			$this->display();
		}

		//Destroy other sessions
		$this->user->destroyUserSessions($this->in->get('u'));

		//Set a random password, as this method should be used if an account is compromised.
		$user_password = random_string(32);
		$arrSet = array(
			'user_password' => $this->user->encrypt_password($user_password),
		);

		$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set($arrSet)->execute($this->in->get('u', 0));

		// Email them their new password
		$bodyvars = array(
			'USERNAME'		=> $this->pdh->get('user', 'name', array($this->in->get('u', 0))),
			'DATETIME'		=> $this->time->user_date($this->time->time),
			'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'/Login/NewPassword/?key=' . $pwkey,
		);

		if($this->email->SendMailFromAdmin($this->in->get('user_email'), $this->user->lang('email_subject_new_pw'), 'user_new_password.html', $bodyvars)) {
			$this->core->message($this->user->lang('password_sent'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('error_email_send'), $this->user->lang('error'), 'red');
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function unlock() {
		$this->pdh->put('user', 'activate', array($this->in->get('u')));
		$username = $this->pdh->get('user', 'name', array($this->in->get('u')));
		$this->core->message(sprintf($this->user->lang('user_unlock_success'), sanitize($username)), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function lock() {
		if (!(($this->user->data['user_id'] == $this->in->get('u')) || (!$this->user->check_group(2, false) && $this->user->check_group(2, false, $this->in->get('u'))))){
			$this->pdh->put('user', 'activate', array($this->in->get('u'), 0));
			$username = $this->pdh->get('user', 'name', array($this->in->get('u')));
			$this->core->message(sprintf($this->user->lang('user_lock_success'), sanitize($username)), $this->user->lang('success'), 'green');
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function activate(){
		$intUserID = $this->in->get('u', 0);
		
		$this->pdh->put('user', 'confirm_email', array($intUserID, 1));
		$username = $this->pdh->get('user', 'name', array($intUserID));
		
		//Send Confirmation Email if Admin Activation is enabled
		if((int)$this->config->get('account_activation') == 2){
			//Send out notification email
			$bodyvars = array(
					'USERNAME'		=> $username,
					'GUILDTAG'		=> $this->config->get('guildtag'),
			);
			
			$this->email->Set_Language($this->pdh->get('user', 'lang', array($intUserID)));
			
			$this->email->SendMailFromAdmin($this->pdh->get('user', 'email', array($intUserID)), $this->user->lang('email_subject_activation_none'), 'register_account_activated.html', $bodyvars);
			
		}
		
		$this->core->message(sprintf($this->user->lang('user_activate_success'), sanitize($username)), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function force_email_confirm(){
		$userid = $this->in->get('u', 0);
		$this->pdh->put('user', 'confirm_email', array($userid, 0));
		//Send the User an Email with activation link
		$user_key = $this->pdh->put('user', 'create_new_activationkey', array($userid));
		$username = $this->pdh->get('user', 'name', array($this->in->get('u')));

		// Email them their new key
		$email = registry::register('MyMailer');
		$bodyvars = array(
				'USERNAME'		=> $username,
				'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'Register/Activate/?key=' . $user_key,
		);
		$email->SendMailFromAdmin($this->pdh->get('user', 'email', array($userid)), $this->user->lang('email_subject_email_confirm'), 'user_email_confirm.html', $bodyvars);


		$this->core->message(sprintf($this->user->lang('user_force_emailconfirm_success'), sanitize($username)), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}


	public function resolve_permissions(){
		$intUserID = $this->in->get('u', 0);
		$strUsername = $this->pdh->get('user', 'name', array($intUserID));

		// Build the user permissions
		$user_permissions = $this->acl->get_permission_boxes();
		// Add plugin checkboxes to our array
		$this->pm->generate_permission_boxes($user_permissions);

		//Get group-memberships of the user
		$defaultGroup = $this->pdh->get('user_groups', 'standard_group', array());
		$memberships = $this->acl->get_user_group_memberships($intUserID);

		$arrPermTrace = $this->acl->trace_user_permissions($intUserID);
		
		foreach ( $user_permissions as $group => $checks ){

			$this->tpl->assign_block_vars('permissions_row', array(
					'GROUP' => $group,
			));

			$icon = (isset($checks['icon'])) ? $this->core->icon_font($checks['icon']) : '';

			$a_set = $u_set = false;
			foreach ( $checks as $data ){
				if (!is_array($data)) continue;

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

				$perm = false;
				$permString = "";
				
				if(isset($arrPermTrace[$data['CBNAME']]['personal'])){
					$perm = true;
					$permString .= '<i class="fa fa-user"></i>';
				}
				
				if(isset($arrPermTrace[$data['CBNAME']]['group'])){
					$perm = true;
					$arrGroupNames = array();
					
					foreach($arrPermTrace[$data['CBNAME']]['group'] as $groupID => $status){
						if($status !== "Y") continue;
						$arrGroupNames[] = $this->pdh->get('user_groups', 'name', array($groupID));
					}
					
					$permString .= ' <i class="fa fa-users coretip" data-coretip="'.$this->jquery->sanitize(implode(', ', $arrGroupNames)).'"></i>';
				}
				

				$this->tpl->assign_block_vars(substr($data['CBNAME'], 0, 2).'permissions_row.check_group', array(
						'CBNAME'			=> $data['CBNAME'],
						'STATUSICON'		=> ( $perm != false ) ? ' <i class="fa fa-check positive fa-lg"></i> ' : '<i class="fa fa-times negative fa-lg"></i> ',
						'PERMSTRING'		=> $permString,
						'CLASS'				=> ( $perm != false ) ? 'positive' : 'negative',
						'S_PERM'			=> ( $perm != false ) ? true : false,
						'TEXT'				=> $data['TEXT'],
				));
			}
		}


		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $caid){
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}

		$this->tpl->assign_block_vars('articelcat_row', array(
				'GROUP' => $this->user->lang('article'),
				'ICON'	=> $this->core->icon_font('fa-file-text'))
		);
		$grps = array('rea', 'cre', 'upd', 'del', 'chs');
		foreach($grps as $group_id){
			$this->tpl->assign_block_vars('articelcat_row.headline_row', array(
					'GROUP'	=> $this->user->lang('perm_'.$group_id),
			));
		}

		foreach($arrCategories as $intCategoryID => $strCategoryName){
			$this->tpl->assign_block_vars('articelcat_row.check_group', array(
					'CBNAME'		=> $strCategoryName,
					'S_ADMIN'		=> false
			));

			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($intUserID);
			
			$arrPermissions = array('read' => false, 'create' => false, 'update' => false, 'delete' => false, 'change_state' => false,);
			foreach($arrUsergroupMemberships as $intGroupID => $intStatus){
				$blnReadPerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'rea', $intGroupID));
				if ($blnReadPerm) $arrPermissions['read'][$intGroupID] = true;
				$blnCreatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'cre', $intGroupID));
				if ($blnCreatePerm) $arrPermissions['create'][$intGroupID] = true;
				$blnUpdatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'upd', $intGroupID));
				if ($blnUpdatePerm) $arrPermissions['update'][$intGroupID] = true;
				$blnDeletePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'del', $intGroupID));
				if ($blnDeletePerm) $arrPermissions['delete'][$intGroupID] = true;
				$blnChangeStatePerm = $this->pdh->get('article_categories', 'calculated_permissions', array($intCategoryID, 'chs', $intGroupID));
				if ($blnChangeStatePerm) $arrPermissions['change_state'][$intGroupID] = true;
			}
				
			foreach(array_keys($arrPermissions) as $group_id){
				$blnResult = (is_array($arrPermissions[$group_id]) && count($arrPermissions[$group_id]) > 0);
				if($blnResult){
					$arrGroupNames = array();
					foreach($arrPermissions[$group_id] as $userGroupID => $setting){
						$arrGroupNames[] = $this->pdh->get('user_groups', 'name', array($userGroupID));
					}
					$out = '<i class="fa fa-check positive fa-lg coretip" data-coretip="'.$this->jquery->sanitize(implode(', ', $arrGroupNames)).'"></i>';
					
				} else {
					$out = '<i class="fa fa-times negative fa-lg"></i>';
					
				}
				
				$this->tpl->assign_block_vars('articelcat_row.check_group.group_row', array(
						'STATUS'	=> $out,
				));
			}
		}
		
	

		$this->tpl->assign_vars(array(
			'THIS_USERNAME' => $strUsername,
		));

		$this->jquery->Tab_header('permission_tabs');

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('user_resolve_perms').': '.$strUsername,
			'template_file'		=> 'admin/manage_users_resolveperms.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_users'), 'url'=>$this->root_path.'admin/manage_users.php'.$this->SID],
				['title'=>$this->user->lang('user_resolve_perms').': '.$strUsername, 'url'=>' '],
			],
			'display'			=> true
		]);
	}



	public function overtake_permissions(){
		if ($this->user->check_group(2, false) || ($this->user->check_auth('a_users_perms') && !$this->user->check_group(2, false, $this->in->get('u', 0)))){
			$this->user->overtake_permissions($this->in->get('u', 0));
			redirect('index.php'.$this->SID);
		}
	}

	public function maincharchange(){
		$memberid = $this->in->get('maincharchange', 0);
		$userid = $this->in->get('user', 0);

		$this->pdh->put('member', 'change_mainid', array($this->pdh->get('member', 'connection_id', array($userid)), $memberid));
		$this->pdh->process_hook_queue();
		echo($this->user->lang('uc_savedmsg_main'));
		exit();
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
			} elseif ( !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_\-\+])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $values['user_email']) ){
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
		if ($change_password && strlen($values['new_password']) > 128) {
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
				$query_ary['user_password'] = $this->user->encrypt_password($values['new_password']);
				$query_ary['user_login_key'] = '';

				//Destroy other sessions
				$this->user->destroyUserSessions($user_id);
			}

			$query_ary['user_email']	= $this->encrypt->encrypt($values['user_email']);

			$plugin_settings = array();
			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $pvalues){
					unset($pvalues['name'], $pvalues['icon']);
					foreach($pvalues as $key => $settings){
						foreach($settings as $setkey => $setval){
							$plugin_settings[] = $setkey;
						}
					}
				}
			}

			//copy all other values to appropriate array
			$ignore = array('username', 'user_email', 'current_password', 'new_password', 'confirm_password');
			$values['ntfy_comment_new_article_categories'] = $this->in->getArray('ntfy_comment_new_article_categories', 'int');
			$privArray = array();
			$customArray = array();
			$pluginArray = array();
			$notificationArray = array();

			//Hook usersettings_update
			if($this->hooks->isRegistered('usersettings_update')){
				$values= $this->hooks->process('usersettings_update', array('settingsdata' => $values, 'admin' => true), true);
			}

			foreach($values as $name => $value) {
				if(in_array($name, $ignore)) continue;
				if (strpos($name, "auth_account_") === 0) continue;

				if(strpos($name, "priv_") === 0){
					$privArray[$name] = $value;
				}elseif(strpos($name, "ntfy_") === 0){
					$notificationArray[$name] = $value;
				} elseif(in_array($name, $plugin_settings)){
					$pluginArray[$name] = $value;
				} elseif(in_array($name, user::$normalUserTableFields)){
					$query_ary[$name] = $value;
				} else {
					$customArray[$name] = $value;
				}
			}

			//Create Thumbnail for User Avatar
			if ($customArray['user_avatar'] != "" && $this->pdh->get('user', 'avatar', array($user_id)) != $customArray['user_avatar']){
				$image = $this->pfh->FolderPath('users/'.$user_id,'files').$customArray['user_avatar'];
				$this->pfh->thumbnail($image, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$user_id.'_68.'.pathinfo($image, PATHINFO_EXTENSION), 68);
			}

			$query_ary['privacy_settings']		= serialize($privArray);
			$query_ary['custom_fields']			= serialize($customArray);
			$query_ary['plugin_settings']		= serialize($pluginArray);
			$query_ary['notifications']			= serialize($notificationArray);
			unset($query_ary['send_new_pw']);

			$this->pdh->put('user', 'update_user', array($user_id, $query_ary));
			$this->pdh->put('user', 'activate', array($user_id, $this->in->get('user_active', 0)));
		} else {
			$password = ($values['new_password'] == "") ? random_string(32) : $values['new_password'];
			$new_password = $this->user->encrypt_password($password);

			$query_ar = array(
				'username'				=> $this->in->get('username'),
				'user_password'			=> $new_password,
				'user_email'			=> $this->crypt->encrypt($values['user_email'])
			);

			$plugin_settings = array();

			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $pvalues){
					unset($pvalues['name'], $pvalues['icon']);
					foreach($pvalues as $key => $settings){
						foreach($settings as $setkey => $setval){
							$plugin_settings[] = $setkey;
						}
					}
				}
			}

			$ignore = array('username', 'user_email', 'current_password', 'new_password', 'confirm_password');
			$privArray = array();
			$customArray = array();
			$pluginArray = array();
			$notificationArray = array();

			foreach($values as $name => $value) {
				if(in_array($name, $ignore)) continue;
				if (strpos($name, "auth_account_") === 0) continue;

				if(strpos($name, "priv_") === 0){
					$privArray[$name] = $value;
				}elseif(strpos($name, "ntfy_") === 0){
					$notificationArray[$name] = $value;
				} elseif(in_array($name, $plugin_settings)){
					$pluginArray[$name] = $value;
				}elseif(in_array($name, user::$normalUserTableFields)){
					$query_ar[$name] = $value;
				} else {
					$customArray[$name] = $value;
				}
			}

			//Create Thumbnail for User Avatar
			if ($customArray['user_avatar'] != "" && $this->pdh->get('user', 'avatar', array($user_id)) != $customArray['user_avatar']){
				$image = $this->pfh->FolderPath('users/'.$user_id,'files').$customArray['user_avatar'];
				$this->pfh->thumbnail($image, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$user_id.'_68.'.pathinfo($image, PATHINFO_EXTENSION), 68);
			}


			$query_ar['privacy_settings']	= serialize($privArray);
			$query_ar['custom_fields']		= serialize($customArray);
			$query_ar['plugin_settings']	= serialize($pluginArray);
			$query_ar['notifications']		= serialize($notificationArray);
			$user_id = $this->pdh->put('user', 'insert_user', array($query_ar, true, false));
			if (!$user_id){
				$this->core->message($this->user->lang('save_nosuc'), $this->user->lang('error'), 'red');
				return;
			}

		}

		// Permissions
		if($this->user->check_auth('a_usergroups_man', false) || $this->user->check_auth('a_users_perms', false) || (isset($adm_memberships[2]) && $adm_memberships[2])){
			$auth_defaults = $this->acl->get_auth_defaults();

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
		}

		// Update Chars
		$this->pdh->put('member', 'update_connection', array($this->in->getArray('member_id', 'int'), $user_id));

		// User-Groups
		$arrOldMemberships = array_keys($this->acl->get_user_group_memberships($user_id));
		if ($this->user->check_auth('a_usergroups_man', false) || $this->user->check_auth('a_users_perms', false) || (isset($adm_memberships[2]) && $adm_memberships[2])){
			$group_list = $this->pdh->get('user_groups', 'id_list', 0);
		} else {
			$group_list = array_keys($adm_memberships);
		}

		$arrNewGroupsRaw = $this->in->getArray('user_groups', 'int');

		foreach($arrNewGroupsRaw as $intGroupID){
			if(in_array($intGroupID, $group_list)) $arrNewGroups[] = $intGroupID;
		}

		$arrayRemoved = array_diff($arrOldMemberships, $arrNewGroups);
		$arrayNew = array_diff($arrNewGroups, $arrOldMemberships);

		if (count($arrayRemoved)) {
			$this->pdh->put('user_groups_users', 'delete_user_from_groups', array($user_id, $arrayRemoved));
			$this->logs->add("action_user_removed_group", array("{L_GROUPS}" => implode(", ", $this->pdh->aget('user_groups', 'name', 0, array($arrayRemoved)))), $user_id, $this->pdh->get('user', 'name', array($user_id)));
		}
		if (count($arrayNew)) {
			$this->pdh->put('user_groups_users', 'add_user_to_groups', array($user_id, $arrayNew));
			$this->logs->add("action_user_added_group", array("{L_GROUPS}" => implode(", ", $this->pdh->aget('user_groups', 'name', 0, array($arrayNew)))), $user_id, $this->pdh->get('user', 'name', array($user_id)));
		}

		// E-mail the user if he/she was activated by the admin and admin activation was set in the config
		$email_success_message = '';
		if ($password OR ($this->config->get('account_activation') == 2 ) && ( $this->pdh->get('user', 'active', array($user_id)) < $this->in->get('user_active'))){

			// Email them their new password
			$this->email->Set_Language($values['user_lang']);

			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($user_id));
			if(!strlen($user_key)) {
				$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
			}
			$strPasswordLink = $this->env->link.$this->controller_path_plain.'/Login/NewPassword/?key=' . $user_key;

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
	public function display($arrUsers=false) {

		$order = explode('.', $this->in->get('o', '0.0'));
		$sort = array(
			0 => array('name', array('asc', 'desc')),
			1 => array('email', array('desc', 'asc')),
			2 => array('last_visit', array('desc', 'asc')),
			3 => array('active', array('desc', 'asc')),
			4 => array('regdate', array('desc', 'asc')),
			5 => array('awaymode', array('desc', 'asc')),
		);

		if($arrUsers !== false){
			$user_ids = $this->pdh->sort($arrUsers, 'user', $sort[$order[0]][0], $sort[$order[0]][1][$order[1]]);
			$blnIsSearch = true;
		} else {
			$user_ids = $this->pdh->sort($this->pdh->get('user', 'id_list'), 'user', $sort[$order[0]][0], $sort[$order[0]][1][$order[1]]);
			$blnIsSearch = false;
		}

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

		$intUsersPerPage = ($blnIsSearch) ? PHP_INT_MAX : 100;

		$k = 0;
		foreach($user_ids as $user_id) {
			if($k < $start) {
				$k++;
				continue;
			}
			if($k >= ($start+$intUsersPerPage)) break;

			$user_avatar = $this->pdh->geth('user', 'avatarimglink', array($user_id));
			if($this->pdh->get('user', 'active', array($user_id))) {
				$user_active = '<i class="eqdkp-icon-online"></i>';
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=lock&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('lock').'"><i class="fa fa-unlock fa-lg icon-color-green"></i></a>';
			} else {
				$user_active = '<i class="eqdkp-icon-offline"></i>';
				$activate_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=unlock&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('unlock').'"><i class="fa fa-lock fa-lg icon-color-red"></i></a>';
			}

			if($this->pdh->get('user', 'email_confirmed', array($user_id)) > 0) {
				$user_mail_confirmed_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=forceemail&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('confirm_email').'"><i class="fa fa-check-square-o fa-lg icon-color-green"></i></a>';
			} else {

				$user_mail_confirmed_icon = '<a href="manage_users.php'.$this->SID.'&amp;mode=activate&amp;u='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('activate').'"><i class="fa fa-square-o fa-lg icon-color-red"></i></a>';
			}
			$user_memberships = $this->pdh->get('user_groups_users', 'memberships', array($user_id));
			$a_members = $this->pdh->get('member', 'connection_id', array($user_id));
			$a_members = (is_array($a_members)) ? $this->pdh->maget('member', array('classid', 'name', 'rankname'), 0, array($a_members), null, false, true) : array();

			$this->tpl->assign_block_vars('users_row', array(
				'U_MANAGE_USER'		=> 'manage_users.php'.$this->SID.'&amp;' . 'u' . '='.$user_id,
				'U_OVERTAKE_PERMS'	=> 'manage_users.php'.$this->SID.'&amp;mode=ovperms&amp;' . 'u' . '='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'U_DELETE'			=> 'manage_users.php'.$this->SID.'&amp;del=single&amp;user_id='.$user_id.'&amp;link_hash='.$this->CSRFGetToken('del'),
				'U_RESOLVE_PERMS'	=> 'manage_users.php'.$this->SID.'&amp;mode=resolveperms&amp;' . 'u' . '='.$user_id,
				'U_DOWNLOAD_GDPR'	=> 'manage_users.php'.$this->SID.'&amp;export_gdpr&amp;' . 'u' . '='.$user_id,
				'USER_ID'			=> $user_id,
				'NAME_STYLE'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? 'font-weight: bold' : 'font-weight: none',
				'ADMIN_ICON'		=> ( $this->user->check_auth('a_', false, $user_id) ) ? '<span class="adminicon"></span> ' : '',
				'USERNAME'			=> $user_avatar.' '.$this->pdh->get('user', 'name', array($user_id)),
				'EMAIL'				=> $this->pdh->get('user', 'email', array($user_id)),
				'LAST_VISIT'		=> ($this->pdh->get('user', 'last_visit', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'last_visit', array($user_id)), true) : '',
				'REG_DATE'			=> ($this->pdh->get('user', 'regdate', array($user_id))) ? $this->time->user_date($this->pdh->get('user', 'regdate', array($user_id)), true) : '',
				'PROTECT_SUPERADMIN'=> ((is_array($user_memberships) && in_array(2, $user_memberships) && !isset($adm_memberships[2])) || ($user_id == $this->user->data['user_id'])) ? true : false,
				'ACTIVE'			=> $user_active,
				'ACTIVATE_ICON'		=> $activate_icon,
				'EMAIL_CONFIRM'		=> $user_mail_confirmed_icon,
				'MEMBER_COUNT'		=> count($a_members),
				'AWAY'				=> $this->pdh->get('user', 'html_is_away', array($user_id)),
			));

			if (is_array($a_members)){
				foreach ($a_members as $member_id => $member) {
					$this->tpl->assign_block_vars('users_row.members_row', array(
						'MEMBER_ID'		=> $member_id,
						'CLASS'			=> $this->jquery->sanitize($member['classid']),
						'NAME'			=> $this->jquery->sanitize($member['name']),
						'RANK'			=> $this->jquery->sanitize($member['rankname']),
						'RADIO'			=> $this->jquery->sanitize((new hradio('mainchar_'.$user_id, array('options' => array($member_id=>''), 'value' => $this->pdh->get('member', 'mainid', array($member_id)), 'class' => 'cmainradio', 'nodiv' => true, 'js' => 'onchange="change_mainchar('.$user_id.', '.$member_id.')"')))->output()),
					));
				}
			}
			$members = '';
			$k++;
		}
		$onclose_url = "if(event.originalEvent == undefined) { window.location.href = '".$this->server_path."admin/manage_users.php".$this->SID."'; } else { window.location.href = 'manage_users.php".$this->SID."'; }";
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=> $this->controller_path.'AddCharacter/'.$this->SID."&adminmode=1&editid='+editid+'", 'width'=>'640', 'height'=>'520', 'onclosejs'=>$onclose_url));
		
		$strConfirmDelete = $this->user->lang('confirm_delete_users');
		if(!$this->config->get('disable_guild_features')){
			$strConfirmDeleteSingle = $strConfirmDelete.'<br /><br /><input type="checkbox" name="delete_associated_members" value="1" onchange="handle_assoc_members()" id="delete_associated_members" /><label for="delete_associated_members"> '. $this->user->lang('delete_associated members').'</label>';
			$strConfirmDelete .= '<br /><br /><input type="checkbox" name="delete_associated_members_single" value="1" id="delete_associated_members_single" /><label for="delete_associated_members_single"> '. $this->user->lang('delete_associated members').'</label>';
		}
		
		$this->confirm_delete($strConfirmDelete, '', false, array('height'	=> 300));
		$this->confirm_delete($strConfirmDeleteSingle, '', true, array('height'	=> 300,'function' => 'delete_single_warning', 'force_ajax' => true, 'custom_js' => 'delete_single(selectedID);'));
		$this->jquery->selectall_checkbox('selall_user', 'user_id[]', $this->user->data['user_id']);

		$arrUsergroups = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		
		$arrMenuItems = array(
				0 => array(
						'type'	=> 'javascript',
						'icon'	=> 'fa-trash-o',
						'text'	=> $this->user->lang('delete'),
						'perm'	=> true,
						'name'	=> 'mdel',
						'js'	=> "$('#del_members').click();",
						'append'=> '<input name="mdel" onclick="delete_warning();" id="del_members" class="mainoption bi_delete" type="button" style="display:none;" />',
				),
				1 => array(
						'type'	=> 'button',
						'icon'	=> 'fa-lock',
						'text'	=> $this->user->lang('lock'),
						'perm'	=> true,
						'name'	=> 'bulk_lock',
				),
				2 => array(
						'type'	=> 'button',
						'icon'	=> 'fa-unlock',
						'text'	=> $this->user->lang('unlock'),
						'perm'	=> true,
						'name'	=> 'bulk_unlock',
				),
				3 => array(
						'type'	=> 'button',
						'icon'	=> 'fa-check-square-o',
						'text'	=> $this->user->lang('activate'),
						'perm'	=> true,
						'name'	=> 'bulk_activate',
				),
				4 => array(
						'type'	=> 'button',
						'icon'	=> 'fa-square-o',
						'text'	=> $this->user->lang('confirm_email'),
						'perm'	=> true,
						'name'	=> 'bulk_confirmemail',
				),
				5 => array(
						'type'	=> 'select',
						'icon'	=> 'fa-users',
						'text'	=> $this->user->lang('mass_usergroup_change'),
						'perm'	=> true,
						'name'	=> 'groupc',
						'options' => array('groups', $arrUsergroups),
				),
		);

		$this->tpl->assign_vars(array(
			// Sorting
			'O_USERNAME'			=> ($this->in->get('o', '0.0') == '0.0') ? '0.1' : '0.0',
			'O_EMAIL'				=> ($this->in->get('o') == '1.0') ? '1.1' : '1.0',
			'O_LAST_VISIT'			=> ($this->in->get('o') == '2.0') ? '2.1' : '2.0',
			'O_ACTIVE'				=> ($this->in->get('o') == '3.0') ? '3.1' : '3.0',
			'O_REG_DATE'			=> ($this->in->get('o') == '4.0') ? '4.1' : '4.0',
			'O_AWAY'				=> ($this->in->get('o') == '5.0') ? '5.1' : '5.0',
			'UPARROW'				=> $this->root_path.'images/arrows/up_arrow',
			'DOWNARROW'				=> $this->root_path.'images/arrows/down_arrow',
			'RED'.$order[0].$order[1] => '_red',
			'CSRF_MAINCHARCHANGE' => $this->CSRFGetToken('maincharchange'),
			'S_PERM_PERMISSION'		=> $this->user->check_auth('a_users_perms', false),
			'S_IS_SEARCH'			=> $blnIsSearch,

			// Page vars
			'U_MANAGE_USERS'	=> 'manage_users.php' . $this->SID . '&amp;start=' . $start . '&amp;',
			'LISTUSERS_COUNT'	=> $total_users,
			'BUTTON_MENU'		=> $this->core->build_dropdown_menu($this->user->lang('selected_user').'...', $arrMenuItems, '', 'manage_users_menu', array("input[name=\"user_id[]\"]")),

			'USER_PAGINATION'		=> generate_pagination('manage_users.php'.$this->SID.'&amp;o='.$this->in->get('o'), $total_users, $intUsersPerPage, $start))
		);

		if($blnIsSearch){
			$arrPagePath = [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manage_users'), 'url'=> $this->root_path.'admin/manage_users.php'.$this->SID],
					['title'=>$this->user->lang('manage_users_search'), 'url'=> ' '],
			];
		} else {
			$arrPagePath = [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manage_users'), 'url'=>' '],
			];
		}

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_users_title'),
			'template_file'		=> 'admin/manage_users.html',
			'page_path'			=> $arrPagePath,
			'display'			=> true
		]);
	}

	public function delete_authaccount() {
		$strMethod = $this->in->get('lmethod');
		$this->pdh->put('user', 'delete_authaccount', array($this->in->get('u'), $strMethod));
		$this->pdh->process_hook_queue();
		$this->edit();
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

		//Get group-memberships of the user
		$defaultGroup = $this->pdh->get('user_groups', 'standard_group', array());
		$memberships = ($user_id) ? $this->acl->get_user_group_memberships($user_id) : array( $defaultGroup => $defaultGroup);
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
		$usergroups = $todisable = array();
		if (is_array($groups)){
			foreach ($groups as $key=>$elem){
				if($this->user->check_auth('a_usergroups_man', false) || $this->user->check_auth('a_users_perms', false) || (isset($memberships[2]) && $memberships[2])){
					$usergroups[$key] = $elem;
				} elseif(isset($memberships[$key]) && $memberships[$key]) {
					$usergroups[$key] = $elem;
				} else {
					$todisable[$key] = $key;
					$usergroups[$key] = $elem;
				}

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
			$user_data = $this->pdh->get('user', 'data', array($user_id, true));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'privacy_settings', array($user_id)));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'custom_fields', array($user_id)));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'plugin_settings', array($user_id)));
			$user_data = array_merge($user_data, $this->pdh->get('user', 'notification_settings', array($user_id)));
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
			'S_PERM_PERMISSION'			=> $this->user->check_auth("a_users_perms", false),

			'USER_GROUP_SELECT'			=> (new hmultiselect('user_groups', array('options' => $usergroups, 'value' => array_keys($memberships), 'width' => 400, 'filter' => true, 'height' => 250, 'todisable' => $todisable)))->output(),
			'JS_CONNECTIONS'			=> (new hmultiselect('member_id', array('options' => $mselect_list, 'value' => $mselect_selected, 'width' => 400, 'height' => 250, 'filter' => true)))->output(),
			'ACTIVE_RADIO'				=> (new hradio('user_active', array('value' => (($user_id) ? $user_data['user_active'] : true))))->output(),

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


		$this->tpl->assign_var('JS_TAB_SELECT', $this->jquery->Tab_Select('usersettings_tabs', (($user_id) ? 6+count($this->pm->get_menus('settings')) : 0)));

		$this->core->set_vars([
			'page_title'		=> ($user_id) ? $this->user->lang('manage_users').': '.sanitize($user_data['username']) : $this->user->lang('user_creation'),
			'template_file'		=> 'settings.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_users'), 'url'=>$this->root_path.'admin/manage_users.php'.$this->SID],
				['title'=>sanitize($user_data['username']), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function create_form($user_id) {
		// initialize form class
		$this->form->lang_prefix = 'user_sett_';
		$this->form->use_tabs = true;
		$this->form->use_fieldsets = true;
		$this->form->validate = true;

		$settingsdata = user::get_settingsdata($user_id);

		// get rid of current_password field
		unset($settingsdata['registration_info']['registration_info']['current_password']);
		// vary help messages for user creation
		if($user_id <= 0) {
			$settingsdata['registration_info']['registration_info']['new_password']['help'] = 'user_creation_password_note';
			$settingsdata['registration_info']['registration_info']['confirm_password']['help'] = 'user_creation_password_note';
		}
		// add deletelink for user-avatar
		$settingsdata['profile']['user_avatar']['user_avatar']['deletelink'] = 'manage_users.php'.$this->SID.'&u='.$user_id.'&mode=deleteavatar';

		//Hook usersettings_display
		if($this->hooks->isRegistered('usersettings_display')){
			$settingsdata = $this->hooks->process('usersettings_display', array('settingsdata' => $settingsdata, 'admin' => true), true);
		}

		$this->form->add_tabs($settingsdata);
		// add send-new-password-button (if editing user)
		if($user_id > 0) {
			$this->form->add_field('send_new_pw', array('type' => 'button', 'buttontype' => 'submit', 'class' => 'mainoption bi_mail', 'buttonvalue' => 'user_sett_f_send_new_pw', 'tolang' => true), 'registration_info', 'registration_info');
		}

		// add various auth-accounts
		$auth_options = $this->user->get_loginmethod_options();
		$auth_array = array();

		$user_data = array();
		if($user_id > 0) {
			$user_data = $this->pdh->get('user', 'data', array($user_id, true));
		}

		foreach($auth_options as $method => $options){
			if (isset($options['connect_accounts']) && $options['connect_accounts']){
				if (isset($user_data['auth_account'][$method]) && strlen($user_data['auth_account'][$method])){
					$display = $this->user->handle_login_functions('display_account', $method, array($this->user->data['auth_account'][$method]));
					if (is_array($display) || $display == "") {
						$display = $this->user->data['auth_account'][$method];
					}
					$field_opts = array(
							'dir_lang'	=> ($this->user->lang('login_'.$method)) ? $this->user->lang('login_'.$method) : ucfirst($method),
							'text'		=> '<a href="manage_users.php'.$this->SID.'&amp;u='.$user_id.'&amp;mode=delauthacc&amp;lmethod='.$method.'&amp;link_hash='.$this->CSRFGetToken('mode').'"><i class="fa fa-trash-o fa-lg" title="'.$this->user->lang('delete').'"></i></a>',
							'help'		=> 'auth_accounts_help',
					);

					$this->form->add_field('auth_account_'.$method, $field_opts, 'auth_accounts', 'registration_info');
				}
			}
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
