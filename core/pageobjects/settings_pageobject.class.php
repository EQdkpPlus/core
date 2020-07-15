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


//AJAX
if(register('in')->get('ajax', 0) === 1){
	if($_POST['username']){
		if(register('in')->exists('olduser') && urldecode(register('in')->get('olduser')) === sanitize($_POST['username'])){
			echo 'true';
		}else{
			echo register('pdh')->get('user', 'check_username', array(register('in')->get('username')));
		}
	}
	if($_POST['email_address']){
		if(register('in')->exists('oldmail') && urldecode(register('in')->get('oldmail')) === sanitize($_POST['email_address'])){
			echo 'true';
		}else{
			echo register('pdh')->get('user', 'check_email', array(register('in')->get('email_address')));
		}
	}
	exit;
}


class settings_pageobject extends pageobject {
	public static $shortcuts = array('form' => array('form', array('user_settings')), 'email'=>'MyMailer');
	private $logo_upload = false;

	public function __construct() {

		if (!$this->user->is_signedin()){
			redirect($this->controller_path_plain.'Login/'.$this->SID);
		}

		$handler = array(
			'newexchangekey' => array('process' => 'renew_exchangekey', 'csrf' => true),
			'submit' => array('process' => 'update', 'csrf' => true),
			'delete_account'  => array('process' => 'delete_account', 'csrf' => true),
			'mode' => array(
				array('process' => 'delete_authaccount', 'value' => 'delauthacc', 'csrf' => true),
				array('process' => 'add_authaccount', 'value' => 'addauthacc', 'csrf' => true),
				array('process' => 'delete_avatar', 'value' => 'deleteavatar',  'csrf' => true),
			),

		);
		parent::__construct(false, $handler);

		$this->process();
	}

	public function delete_account(){
		$strPassword = $this->in->get('password');

		if (!$this->user->checkPassword($strPassword, $this->user->data['user_password'])){
			$this->core->message($this->user->lang('incorrect_password'), $this->user->lang('error'), 'red');
			$this->display();
			return;
		}

		//Password matches, so delete user
		$this->pdh->put('user', 'delete_user', array($this->user->id, 0));

		//Logout
		$this->user->logout();
		redirect('', false, false, false);
	}

	public function renew_exchangekey(){
		$app_key = $this->pdh->put('user', 'create_new_exchangekey', array($this->user->id));
		$this->user->data['exchange_key'] = $app_key;
		if ($app_key) $this->core->message($this->user->lang('user_create_new_appkey_success'), $this->user->lang('success'), 'green');
	}

	public function delete_avatar() {
		$this->pdh->put('user', 'delete_avatar', array($this->user->data['user_id']));
		$this->pdh->process_hook_queue();
		unset($this->user->data['custom_fields']['user_avatar']);
	}

	public function delete_authaccount() {
		$strMethod = $this->in->get('lmethod');
		$this->pdh->put('user', 'delete_authaccount', array($this->user->id, $strMethod));
		$this->pdh->process_hook_queue();
		unset($this->user->data['auth_account'][$strMethod]);
	}

	public function add_authaccount() {
		$strMethod = $this->in->get('lmethod');
		$account = $this->user->handle_login_functions('get_account', $strMethod);
		if ($strMethod && !is_array($account) && strlen($account) && $this->pdh->get('user', 'check_auth_account', array($account, $strMethod))){
			$this->pdh->put('user', 'add_authaccount', array($this->user->id, $account, $strMethod));
			$this->pdh->process_hook_queue();
			$this->user->data['auth_account'][$strMethod] = $account;
			$this->core->message(ucfirst($strMethod), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('auth_connect_account_error'), $this->user->lang('error'), 'red');
		}
	}

	public function update() {
		$this->create_form();
		$values = $this->form->return_values();

		// Error-check the form
		if($this->form->error) {
			$this->display($values);
			return;
		}
		$change_username = ( $values['username'] != $this->user->data['username'] ) ? true : false;
		$change_password = ( $values['new_password'] != '' || $values['confirm_password'] != '') ? true : false;
		$change_email = ( $values['user_email'] != $this->user->data['user_email']) ? true : false;

		//Check username
		if ($change_username && $this->pdh->get('user', 'check_username', array($values['username'])) == 'false'){
			$this->core->message(str_replace('{0}', $values['username'], $this->user->lang('fv_username_alreadyuse')), $this->user->lang('error'), 'red');
			$this->display($values);
			return;
		}

		//Check email
		if ($change_email){
			if ($this->pdh->get('user', 'check_email', array($values['user_email'])) == 'false'){
				$this->core->message(str_replace('{0}', $values['user_email'], $this->user->lang('fv_email_alreadyuse')), $this->user->lang('error'), 'red');
				$this->display($values);
				return;
			}
		}

		//Check matching new passwords
		if($change_password) {
			if($values['new_password'] != $values['confirm_password']) {
				$this->tpl->add_js('$("#oldpassword").focus();');
				$this->core->message($this->user->lang('password_not_match'), $this->user->lang('error'), 'red');
				$this->display($values);
				return;
			}
		}
		if ($change_password && strlen($values['new_password']) > 128) {
			$this->tpl->add_js('$("#oldpassword").focus();');
			$this->core->message($this->user->lang('password_too_long'), $this->user->lang('error'), 'red');
			$this->display($values);

			return;

		}

		//Check Password length
		$intPWLength = ($this->config->get('password_length') ? (int)$this->config->get('password_length') : 8);
		if($change_password && strlen($values['new_password']) < $intPWLength){
			$this->core->message(sprintf($this->user->lang('password_too_short'), $intPWLength), $this->user->lang('error'), 'red');
			$this->tpl->add_js('$("#oldpassword").focus();');
			$this->display($values);
			return;
		}
		
		//Check Password Leak
		if($change_password && $this->config->get('check_password_leak')){
			$blnLeaked = register('password')->checkIfLeaked($values['new_password']);
			if($blnLeaked){
				$this->core->message($this->user->lang('password_leaked'), $this->user->lang('error'), 'red');
				$this->display($values);
				$this->tpl->add_js('$("#oldpassword").focus();');
				return;
			}
		}

		// If they changed their username or password, we have to confirm their current password
		if ( ($change_username) || ($change_password) || ($change_email)){
			if (!$this->user->checkPassword($values['current_password'], $this->user->data['user_password'])){
				$this->core->message($this->user->lang('incorrect_password'), $this->user->lang('error'), 'red');
				$this->tpl->add_js('$("#oldpassword").focus();');
				$this->display($values);
				return;
			}
		}

		// Errors have been checked at this point, build the query
		$query_ary = array();
		if ( $change_username ) {
			//Delete default avatar
			include_once $this->root_path.'core/avatar.class.php';
			$avatar = registry::register('avatar');
			$avatar->deleteAvatar($this->user->id,  $this->pdh->get('user', 'name', array($this->user->id)), 68);
			$avatar->deleteAvatar($this->user->id,  $this->pdh->get('user', 'name', array($this->user->id)), 400);
			$query_ary['username'] = $values['username'];
		}
		if ( $change_password ) {
			$query_ary['user_password'] = $this->user->encrypt_password($values['new_password']);
			$query_ary['user_login_key'] = '';

			//Send Mail to the recent user's email address
			$bodyvars = array(
					'USERNAME' => $this->pdh->get('user', 'name', array($this->user->id)),
					'DATETIME'	=> $this->time->user_date($this->time->time, true),
			);
			$this->email->SendMailFromAdmin($this->pdh->get('user', 'email', array($this->user->id)), $this->user->lang('email_subject_password_changed'), 'user_password_changed.html', $bodyvars);

			//Destroy other sessions
			$this->user->destroyOtherSessions();
		}

		//Send email to confirm new email address
		if($change_email){
			if($this->user->data['user_email_confirmed'] > 0){
				$this->pdh->put('user', 'confirm_email', array($this->user->id, 2));
			}
			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($this->user->id, $values['user_email']));

			// Email them their new key
			$email = registry::register('MyMailer');
			$bodyvars = array(
					'USERNAME'		=> $this->pdh->get('user', 'name', array($this->user->id)),
					'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'Register/Activate/?key=' . $user_key,
			);
			$email->SendMailFromAdmin($values['user_email'], $this->user->lang('email_subject_email_confirm'), 'user_email_confirm.html', $bodyvars);

			$this->core->message($this->user->lang('email_confirm_note'), '', 'hint');
		} elseif($this->user->data['user_email_confirmed'] < 1) {
			//Just send new message

			$user_key = $this->pdh->put('user', 'create_new_activationkey', array($this->user->id, $values['user_email']));

			// Email them their new key
			$email = registry::register('MyMailer');
			$bodyvars = array(
					'USERNAME'		=> $this->pdh->get('user', 'name', array($this->user->id)),
					'U_ACTIVATE'	=> $this->env->link.$this->controller_path_plain.'Register/Activate/?key=' . $user_key,
			);
			$email->SendMailFromAdmin($values['user_email'], $this->user->lang('email_subject_email_confirm'), 'user_email_confirm.html', $bodyvars);

			$this->core->message($this->user->lang('email_confirm_note'), '', 'hint');
		}

		//Do not use new email here, because it just have to be confirmed
		$query_ary['user_email']	= $this->encrypt->encrypt($this->user->data['user_email']);
		$query_ary['exchange_key']	= $this->pdh->get('user', 'exchange_key', array($this->user->id));
		
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
		$ignore = array('username', 'user_email', 'current_password', 'new_password', 'confirm_password', 'regenerate_keys', 'api_key', 'exchange_key');
		$privArray = array();
		$customArray = array();
		$pluginArray = array();
		$notificationArray = array();
		$values['ntfy_comment_new_article_categories'] = $this->in->getArray('ntfy_comment_new_article_categories', 'int');

		//Hook usersettings_update
		if($this->hooks->isRegistered('usersettings_update')){
			$values= $this->hooks->process('usersettings_update', array('settingsdata' => $values), true);
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
			}elseif(in_array($name, user::$normalUserTableFields)){
				$query_ary[$name] = $value;
			} else {
				$customArray[$name] = $value;
			}
		}
		
		// the logging thing for calendar away mode
		$arrOld = array(
			'awaymode_enabled'		=> $this->pdh->get('user', 'awaymode_enabled', array($this->user->id)),
			'awaymode_startdate'	=> "{D_".$this->pdh->get('user', 'awaymode_startdate', array($this->user->id))."}",
			'awaymode_enddate'		=> "{D_".$this->pdh->get('user', 'awaymode_enddate', array($this->user->id))."}",
			'awaymode_note'			=> $this->pdh->get('user', 'awaymode_note', array($this->user->id))
		);
		$arrNew = array(
			'awaymode_enabled'		=> $values['awaymode_enabled'],
			'awaymode_startdate'	=> "{D_".$this->time->removetimefromtimestamp($values['awaymode_startdate'])."}",
			'awaymode_enddate'		=> "{D_".$this->time->removetimefromtimestamp($values['awaymode_enddate'])."}",
			'awaymode_note'			=> $values['awaymode_note']
		);
		$arrLang = array(
			'awaymode_enabled'		=> '{L_CALENDAR_AWAYMODE_ENABLED}',
			'awaymode_startdate'	=> '{L_CALENDAR_AWAYMODE_STARTDATE}',
			'awaymode_enddate'		=> '{L_CALENDAR_AWAYMODE_ENDDATE}',
			'awaymode_note'			=> '{L_NOTE}',
		);

		$log_action = $this->logs->diff($arrOld, $arrNew, $arrLang);
		if($log_action) {
			$log_action["{L_USER}"] = $this->pdh->get('user', 'name', array($this->user->id));
			$this->logs->add('calendar_log_awaymode', $log_action, $this->user->id, $this->pdh->get('user', 'name', array($this->user->id)), false, 'calendar');
		}

		//Create Thumbnail for User Avatar
		if ($customArray['user_avatar'] != "" && $this->pdh->get('user', 'avatar', array($this->user->id)) != $customArray['user_avatar']){
			$image = $this->pfh->FolderPath('users/'.$this->user->id,'files').$customArray['user_avatar'];
			$this->pfh->thumbnail($image, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$this->user->id.'_68.'.pathinfo($image, PATHINFO_EXTENSION), 68, 68);
		}

		$query_ary['privacy_settings']		= serialize($privArray);
		$query_ary['custom_fields']			= serialize($customArray);
		$query_ary['plugin_settings']		= serialize($pluginArray);
		$query_ary['notifications']			= serialize($notificationArray);

		$blnResult = $this->pdh->put('user', 'update_user', array($this->user->id, $query_ary));
		$this->pdh->process_hook_queue();

		//Only redirect if saving was successfull so we can grad an error message
		if ($blnResult) redirect($this->controller_path_plain.'Settings/'.$this->SID.'&amp;save'.(($change_email) ? '=mailconfirm' : ''));
		return;
	}

	public function display($userdata=array()) {

		if ($this->in->exists('save')){
			if($this->in->get('save') == 'mailconfirm'){
				$this->core->message($this->user->lang('email_confirm_note'), '', 'hint');
			}
			$this->core->message( $this->user->lang('update_settings_success'),$this->user->lang('save_suc'), 'green');
		}
		
		if($this->in->exists('leaked')){
			$this->core->message($this->user->lang('password_leaked'), $this->user->lang('error'), 'red');
			$this->tpl->add_js('$("#oldpassword").focus();');
		}
		
		if(empty($userdata)) {
			$this->create_form();
			$userdata = array_merge($this->user->data, $this->user->data['privacy_settings'], $this->user->data['custom_fields'], $this->user->data['plugin_settings'], $this->user->data['notification_settings']);
			if(is_array($userdata['ntfy_comment_new_article']) && count($userdata['ntfy_comment_new_article']) == 0){
				$userdata['ntfy_comment_new_article'] = array('-1' => -1);
			}
		}

		// Output
		$this->form->validate = true;
		$this->form->output($userdata);

		$this->jquery->Tab_header('usersettings_tabs', true);
		$this->jquery->Dialog('template_preview', $this->user->lang('template_preview'), array('url'=>$this->controller_path.$this->SID."&style='+ $(\"select[name='user_style'] option:selected\").val()+'", 'width'=>'750', 'height'=>'520', 'modal'=>true));
		$this->tpl->assign_vars(array(
			'S_CURRENT_PASSWORD'			=> true,
			'S_NEW_PASSWORD'				=> true,
			'S_SETTING_ADMIN'				=> false,
			'S_MU_TABLE'					=> false,
			'USERNAME'						=> $this->user->data['username'],
			'PASSWORD_LENGTH'				=> ($this->config->get('password_length') ? (int)$this->config->get('password_length') : 8),


			// Validation
			'AJAXEXTENSION_USER'			=> '&olduser='.urlencode($this->user->data['username']),
			'AJAXEXTENSION_MAIL'			=> '&oldmail='.urlencode($this->user->data['user_email']),
		));

		$this->set_vars(array(
			'page_title'	=> $this->user->lang('settings_title'),
			'template_file'	=> 'settings.html',
			'display'		=> true)
		);
	}

	public function create_form() {
		// initialize form class
		$this->form->lang_prefix = 'user_sett_';
		$this->form->use_tabs = true;
		$this->form->use_fieldsets = true;
		$this->form->validate = true;

		$settingsdata = user::get_settingsdata($this->user->id);
		// set username readonly
		if(!$this->config->get('enable_username_change')) {
			$settingsdata['registration_info']['registration_info']['username']['help'] =  'register_help_disabled_username';
			$settingsdata['registration_info']['registration_info']['username']['readonly'] = true;
		}
		// add delete-avatar link and set upload-type to user
		$settingsdata['profile']['user_avatar']['user_avatar']['imgup_type'] = 'user';
		$settingsdata['profile']['user_avatar']['user_avatar']['deletelink'] = $this->server_path.$this->controller_path_plain.'Settings/'. $this->SID.'&mode=deleteavatar&link_hash='.$this->CSRFGetToken('mode');
		//Deactivate Profilefields synced by Bridge

		if ($this->config->get('cmsbridge_active') == 1){
			$synced_fields = array('username', 'new_password', 'confirm_password');

			if($this->bridge->get_sync_email()) $synced_fields[] = 'user_email';

			if((int)$this->config->get('cmsbridge_disable_sync') != 1){
				//Key: Bridge ID, Value: EQdkp Profilefield ID
				$arrMapping = $this->pdh->get('user_profilefields', 'bridge_mapping');
				foreach($arrMapping as $intBridgeFieldID => $strEQdkpFieldID){
					$synced_fields[] = 'userprofile_'.$strEQdkpFieldID;
				}

			}

			foreach($synced_fields as $sync_field) {
				foreach($settingsdata as &$fieldsets) {
					foreach($fieldsets as &$fields) {
						if(isset($fields[$sync_field])) {
							$fields[$sync_field]['readonly'] = true;
							$fields[$sync_field]['help'] = 'user_sett_bridge_note';
						}
					}
				}
			}
		}

		//Hook usersettings_display
		if($this->hooks->isRegistered('usersettings_display')){
			$settingsdata = $this->hooks->process('usersettings_display', array('settingsdata' => $settingsdata), true);
		}

		$this->form->add_tabs($settingsdata);

		// add user-app-key
		$this->form->add_field('exchange_key', array('lang' => 'user_app_key', 'help' => 'user_help_app_key', 'text' => '<div class="clickToReveal" title="'.$this->user->lang('click_to_reveal').'"><a>**********</a><div>'.$this->user->data['exchange_key'].'</div></div>'), 'private_keys', 'registration_info');

		
		$objQRCode = register('qrcode');

		$this->form->add_field('api_key', array('lang' => 'user_api_key', 'help' => 'user_help_api_key', 'text' =>
				'<div class="clickToReveal" title="'.$this->user->lang('click_to_reveal').'"><a>**********</a><div>'.$this->user->deriveKeyFromExchangekey($this->user->id, 'pex_api').'</a><div class="qr_api_code">'.$objQRCode->image($this->user->deriveKeyFromExchangekey($this->user->id, 'pex_api')).'</div>'
		), 'private_keys', 'registration_info');
		$this->form->add_field('regenerate_keys', array('lang' => 'user_create_new_appkey', 'text' => '<button class="" type="submit" name="newexchangekey"><i class="fa fa-refresh"></i>'.$this->user->lang('user_create_new_appkey').'</button>'), 'private_keys', 'registration_info');
		// add various auth-accounts
		$auth_options = $this->user->get_loginmethod_options();
		$auth_array = array();
		foreach($auth_options as $method => $options){
			if (isset($options['connect_accounts']) && $options['connect_accounts']){
				if (isset($this->user->data['auth_account'][$method]) && strlen($this->user->data['auth_account'][$method])){
					$display = $this->user->handle_login_functions('display_account', $method, array($this->user->data['auth_account'][$method]));
					if (is_array($display) || $display == "") {
						$display = $this->user->data['auth_account'][$method];
					}
					$field_opts = array(
						'dir_lang'	=> ($this->user->lang('login_'.$method)) ? $this->user->lang('login_'.$method) : ucfirst($method),
						'text'		=> $display.' <a href="'.$this->server_path.$this->controller_path_plain.'Settings/'.$this->SID.'&amp;mode=delauthacc&amp;lmethod='.$method.'&amp;link_hash='.$this->CSRFGetToken('mode').'"><i class="fa fa-trash-o fa-lg" title="'.$this->user->lang('delete').'"></i></a>',
						'help'		=> 'auth_accounts_help',
					);
				} else {
					$field_opts = array(
						'dir_lang'	=> ($this->user->lang('login_'.$method)) ? $this->user->lang('login_'.$method) : ucfirst($method),
						'text'		=> $this->user->handle_login_functions('account_button', $method),
						'help'		=> 'auth_accounts_help',
					);
				}
				$this->form->add_field('auth_account_'.$method, $field_opts, 'auth_accounts', 'registration_info');
			}
		}

		//Delete Account
		$this->form->add_field('delete_account', array('lang' => 'user_delete_account', 'text' => '<button class="" type="button" name="newexchangekey" onclick="confirm_account_deletion()"><i class="fa fa-trash-o"></i>'.$this->user->lang('user_sett_fs_delete_account').'</button>'), 'delete_account', 'registration_info');


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
