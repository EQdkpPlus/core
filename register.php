<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2002
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkp-plus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

// the member & email check functionality. POST == security. Not use in-get!!!
if(registry::register('input')->get('ajax', 0) == '1'){
	if($_POST['username']){
		if(registry::register('input')->exists('olduser') && registry::register('input')->get('olduser') === $_POST['username']){
			echo 'true';
		}else{
			echo registry::register('plus_datahandler')->get('user', 'check_username', array(registry::register('input')->get('username')));
		}
	}
	if($_POST['user_email']){
		if(registry::register('input')->exists('oldmail') && urldecode(registry::register('input')->get('oldmail')) === $_POST['user_email']){
			echo 'true';
		}else{
			echo registry::register('plus_datahandler')->get('user', 'check_email', array(registry::register('input')->get('user_email')));
		}
	}
	if($_POST['oldpassword']){
		echo registry::register('plus_datahandler')->get('user', 'check_password', array(registry::register('input')->get('oldpassword')));
	}
	exit;
}

class Register extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'config', 'core', 'html', 'jquery', 'db', 'time', 'env', 'email'=>'MyMailer','crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $server_url	= '';
	public $data		= array();

	public function __construct() {
		$handler = array(
			'submit' => array('process' => 'submit',  'csrf' => true),
			'register' => array('process' => 'display_form'),
			'guildrules' => array('process' => 'display_guildrules'),
			'deny' => array('process' => 'process_deny'),
			'confirmed' => array('process' => 'process_confirmed'),
			'mode' => array(
				array('process' => 'display_resend_activation_mail', 'value' => 'resend_activation'),
				array('process' => 'process_activate', 'value' => 'activate')),
			'resend_activation'=> array('process' => 'process_resend_activation'),
		);
		parent::__construct(false, $handler);
		if ($this->user->data['rules'] == 1){
			// If they're trying access this page while logged in, redirect to settings.php
			if( $this->user->is_signedin() && !$this->in->exists('key')) {
				redirect('settings.php'. $this->SID);
			}
			if((int)$this->config->get('disable_registration') == 1){
				redirect('index.php');
			}
			if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
				if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
					redirect($this->config->get('cmsbridge_reg_url'),false,true);
				} else {
					redirect('index.php');
				}
			}
		}
		// Data to be put into the form
		$strMethod = $this->in->get('lmethod');
		if ($strMethod != ""){
			$pre_register_data = $this->user->handle_login_functions('pre_register', $strMethod);
			if ($pre_register_data) $this->data = $pre_register_data;
		} else {
			// If it's not in POST, we get it from config defaults
			$this->data = array(
				'username'			=> $this->in->get('username'),
				'user_email'		=> $this->in->get('user_email'),
				'user_email2'		=> $this->in->get('user_email2'),
				'user_lang'			=> $this->in->get('user_lang', $this->config->get('default_lang')),
				'user_timezone'		=> $this->in->get('user_timezone', $this->config->get('timezone')),
				'user_password1'	=> $this->in->get('new_user_password1'),
				'user_password2'	=> $this->in->get('new_user_password2'),
			);
		}

		// Build the server URL
		// ---------------------------------------------------------
		$this->server_url  = $this->env->link.'register.php';
		$this->process();
	}

	// ---------------------------------------------------------
	// Process Submit
	// ---------------------------------------------------------
	public function submit() {
		if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
			if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
				redirect($this->config->get('cmsbridge_reg_url'),false,true);
			}else{
				redirect('index.php');
			}
		}

		//Check CAPTCHA
		if ($this->config->get('pk_enable_captcha') == 1){
			require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$captcha = new recaptcha;
			$response = $captcha->recaptcha_check_answer ($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('recaptcha_challenge_field'), $this->in->get('recaptcha_response_field'));
			if (!$response->is_valid) {
				$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
				$this->display_form();
				return;
			}
		}

		//Check Email
		if ($this->pdh->get('user', 'check_email', array($this->in->get('user_email'))) == 'false'){
			$this->core->message($this->user->lang('fv_email_alreadyuse'), $this->user->lang('error'), 'red');
			$this->display_form();
			return;
		} elseif (!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$this->in->get('user_email'))){
			$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
			$this->display_form();
			return;
		}

		//Check Username
		if ($this->pdh->get('user', 'check_username', array($this->in->get('username'))) == 'false'){
			$this->core->message($this->user->lang('fv_username_alreadyuse'), $this->user->lang('error'), 'red');
			$this->display_form();
			return;
		}

		// If the config requires account activation, generate a random key for validation
		if ( ((int)$this->config->get('account_activation') == 1) || ((int)$this->config->get('account_activation') == 2) ) {
			$user_key = random_string(true);
			$key_len = 54 - (strlen($this->server_url));
			$key_len = ($key_len > 6) ? $key_len : 6;

			$user_key = substr($user_key, 0, $key_len);
			$user_active = '0';

			if ($this->user->is_signedin()) {
				$this->user->destroy();
			}
		} else {
			$user_key = '';
			$user_active = '1';
		}

		//Insert the user into the DB
		$user_id = $this->pdh->put('user', 'register_user', array($this->data, $user_active, $user_key, true, $this->in->get('lmethod')));

		//Add auth-account
		if ($this->in->exists('auth_account')){
			$auth_account = $this->crypt->decrypt($this->in->get('auth_account'));
			if ($this->pdh->get('user', 'check_auth_account', array($auth_account))){
				$this->pdh->put('user', 'add_authaccount', array($user_id, $auth_account, $this->in->get('lmethod')));
			}
		}

		//Give permissions if there is no default group
		$default_group = $this->pdh->get('user_groups', 'standard_group', array());
		if (!$default_group) {
			$sql = 'SELECT auth_id, auth_default
					FROM __auth_options
					ORDER BY auth_id';
			$result = $this->db->query($sql);
			while ( $row = $this->db->fetch_record($result) ) {
				$arrSet = array(
					'user_id' => $user_id,
					'auth_id' => $row['auth_id'],
					'auth_setting' => $row['auth_default'],
				);
				$this->db->query("INSERT INTO __auth_users :params", $arrSet);
			}
		}

		if ($this->config->get('account_activation') == 1) {
			$success_message = sprintf($this->user->lang('register_activation_self'), $this->in->get('user_email'));
			$email_template = 'register_activation_self';
			$email_subject	= $this->user->lang('email_subject_activation_self');
		} elseif ($this->config->get('account_activation') == 2) {
			$success_message = sprintf($this->user->lang('register_activation_admin'), $this->in->get('user_email'));
			$email_template = 'register_activation_admin';
			$email_subject	= $this->user->lang('email_subject_activation_admin');
		} else {
			$success_message = sprintf($this->user->lang('register_activation_none'), '<a href="login.php'.$this->SID.'">', '</a>', $this->in->get('user_email'));
			$email_template = 'register_activation_none';
			$email_subject	= $this->user->lang('email_subject_activation_none');
		}

		// Email a notice
		$this->email->Set_Language($this->in->get('user_lang'));
		$bodyvars = array(
			'USERNAME'		=> stripslashes($this->in->get('username')),
			'PASSWORD'		=> stripslashes($this->in->get('user_password1')),
			'U_ACTIVATE' 	=> $this->server_url . '?mode=activate&key=' . $user_key,
			'GUILDTAG'		=> $this->config->get('guildtag'),
		);
		if(!$this->email->SendMailFromAdmin($this->in->get('user_email'), $email_subject, $email_template.'.html', $bodyvars)){
			$success_message = $this->user->lang('email_subject_send_error');
		}

		// Now email the admin if we need to
		if ( $this->config->get('account_activation') == 2 ) {
			$this->email->Set_Language($this->config->get('default_lang'));
			$bodyvars = array(
				'USERNAME'   => $this->in->get('username'),
				'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key
			);
			if(!$this->email->SendMailFromAdmin(register('encrypt')->decrypt($this->config->get('admin_email')), $this->user->lang('email_subject_activation_admin_act'), 'register_activation_admin_activate.html', $bodyvars)){
				$success_message = $this->user->lang('email_subject_send_error');
			}
		}
		message_die($success_message);
	}

	public function display_resend_activation_mail(){
		$this->jquery->Validate('lost_password', array(
			array('name' => 'username', 'value'=> $this->user->lang('fv_required_user')),
			array('name'=>'user_email', 'value'=>$this->user->lang('fv_required_email'))
		));
		$this->jquery->ResetValidate('lost_password');

		$this->tpl->add_js('document.lost_password.username.focus();', 'docready');
		$this->tpl->assign_vars(array(
			'BUTTON_NAME'			=> 'resend_activation',
			'S_RESEND_ACTIVATION'	=> true,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('get_new_activation_mail'),
			'template_file'		=> 'lost_password.html',
			'display'			=> true,
		));

	}

	// ---------------------------------------------------------
	// Process Resend Validation E-Mail
	// ---------------------------------------------------------
	public function process_resend_activation() {
		if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
			if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
				redirect($this->config->get('cmsbridge_reg_url'),false,true);
			}else{
				redirect('index.php');
			}
		}

		$username   = ( $this->in->exists('username') )   ? trim(strip_tags($this->in->get('username'))) : '';

		// Look up record based on the username and e-mail
		$sql = "SELECT user_id, username, user_email, user_active, user_lang
				FROM __users
				WHERE LOWER(user_email) = '" .$this->db->escape(utf8_strtolower($username))."'
				OR LOWER(username)='".$this->db->escape(clean_username($username))."'";
		if ( $result = $this->db->query($sql) ) {
			if ( $row = $this->db->fetch_record($result) ) {
				// Account's inactive, can't give them their password
				if ( $row['user_active'] || $this->config->get('account_activation') != 1) {
					message_die($this->user->lang('error_already_activated'));
				}

				$username = $row['username'];

				// Create a new activation key
				$user_key = $this->pdh->put('user','create_new_activationkey',array($row['user_id']));

				// Email them their new password
				$bodyvars = array(
					'USERNAME'		=> $row['username'],
					'DATETIME'		=> $this->time->user_date(false, true),
					'U_ACTIVATE'	=> $this->server_url . '?mode=activate&key=' . $user_key,
				);

				if($this->email->SendMailFromAdmin($row['user_email'], $this->user->lang('email_subject_activation_self'), 'register_activation_self.html', $bodyvars)) {
					message_die(sprintf($this->user->lang('register_activation_self'), $this->in->get('user_email')), $this->user->lang('get_new_password'));
				} else {
					message_die($this->user->lang('error_email_send'), $this->user->lang('get_new_password'));
				}
			} else {
				message_die($this->user->lang('error_invalid_user_or_mail'), $this->user->lang('get_new_activation_mail'), '', '', '', array('value' => $this->user->lang('back'), 'onclick' => 'javascript:history.back()'));
			}
		} else {
			message_die('Could not obtain user information', '', 'error', false,__FILE__, __LINE__, $sql);
		}
	}


	// ---------------------------------------------------------
	// Process Activate
	// ---------------------------------------------------------
	public function process_activate() {
		$sql = "SELECT user_id, username, user_active, user_email, user_lang, user_key
				FROM __users
				WHERE user_key='" . $this->db->escape($this->in->get('key')) . "'";
		if ( !($result = $this->db->query($sql)) ) {
			message_die('Could not obtain user information', '', 'error', false, __FILE__, __LINE__, $sql);
		}
		if ( $row = $this->db->fetch_record($result) ) {
			// If they're already active, just bump them back
			if ( ($row['user_active'] == '1') && ($row['user_key'] == '') ) {
				message_die($this->user->lang('error_already_activated'));
			} else {
				$this->pdh->put('user', 'activate', array($row['user_id']));

				// E-mail the user if this was activated by the admin
				if ( $this->config->get('account_activation') == 2 ) {
					$this->email->Set_Language($row['user_lang']);
					$bodyvars = array(
						'USERNAME' => $row['username'],
					);
					if($this->email->SendMailFromAdmin($row['user_email'], $this->user->lang('email_subject_activation_none'), 'register_activation_none.html', $bodyvars)) {
						$success_message = $this->user->lang('account_activated_admin');
					}else{
						$success_message = $this->user->lang('email_subject_send_error');
					}
				} else {
					$this->tpl->add_meta('<meta http-equiv="refresh" content="3;login.php' . $this->SID . '">');
					$success_message = sprintf($this->user->lang('account_activated_user'), '<a href="login.php' . $this->SID . '">', '</a>');
				}
				message_die($success_message);
			}
		} else {
			message_die($this->user->lang('error_invalid_key'));
		}
	}

	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------

	public function display() {
		$count = count($this->pdh->get('pages', 'guildrule_page'));
		$button = ($this->user->is_signedin()) ? 'confirmed' : 'register';
		$intSocialPlugins = count(register('socialplugins')->getSocialPlugins(true));

		$this->tpl->assign_vars(array(
			'SUBMIT_BUTTON'	=> ($count > 0) ? 'guildrules' : $button,
			'FORM_ACTION'	=> 'register.php'.$this->SID,
			'HEADER'		=> $this->user->lang('register_title').' - '.$this->user->lang('licence_agreement'),
			'TEXT'			=> $this->user->lang('register_licence').(($intSocialPlugins) ? $this->user->lang('social_privacy_statement') : ''),
			'S_LICENCE'		=> true,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('register_title'),
			'template_file'		=> 'register.html',
			'display'			=> true)
		);
	}

	public function display_guildrules() {
		$button = ($this->user->is_signedin()) ? 'confirmed' : 'register';
		$page = $this->pdh->get('pages', 'guildrule_page');
		$data = $this->pdh->get('pages', 'content', array($page[0]));

		$this->tpl->assign_vars(array(
			'SUBMIT_BUTTON'	=> $button,
			'FORM_ACTION'	=> 'register.php'.$this->SID,
			'HEADER'		=> $this->user->lang('guildrules'),
			'TEXT'			=> html_entity_decode($data),
			'S_LICENCE'		=> true,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('register_title'),
			'template_file'		=> 'register.html',
			'display'			=> true)
		);
	}

	public function process_deny() {
		if ($this->user->is_signedin()){
			redirect('login.php'.$this->SID.'&logout=true');
		} else {
			redirect('index.php');
		}
	}

	public function process_confirmed() {
		if ($this->user->is_signedin()){
			$this->db->query("UPDATE __users SET rules = 1 WHERE user_id=?", false, $this->user->id);
		}
		redirect('index.php');
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display_form() {
		if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
			if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
				redirect($this->config->get('cmsbridge_reg_url'),false,true);
			}else{
				redirect('index.php');
			}
		}

		//Captcha
		if ($this->config->get('pk_enable_captcha') == 1){
			require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$captcha = new recaptcha;
			$this->tpl->assign_vars(array(
				'CAPTCHA'				=> $captcha->recaptcha_get_html($this->config->get('lib_recaptcha_okey')),
				'S_DISPLAY_CATPCHA'		=> true,
			));
		}

		$language_array = array();
		if($dir = @opendir($this->root_path . 'language/')){
			while($file = @readdir($dir)){
				if((!is_file($this->root_path . 'language/' . $file)) && (!is_link($this->root_path . 'language/' . $file)) && valid_folder($file)){
					$language_array[$file] = ucfirst($file);
				}
			}
		}

		$this->tpl->assign_vars(array(
			'S_CURRENT_PASSWORD'			=> false,
			'S_NEW_PASSWORD'				=> false,
			'S_SETTING_ADMIN'				=> false,
			'S_MU_TABLE'					=> false,

			'VALID_EMAIL_INFO'				=> ($this->config->get('account_activation') == 1) ? '<br />'.$this->user->lang('valid_email_note') : '',
			'AUTH_REGISTER_BUTTON'			=> ($arrRegisterButtons = $this->user->handle_login_functions('register_button')) ? implode(' ', $arrRegisterButtons) : '',

			'REGISTER'						=> true,

			'DD_LANGUAGE'					=> $this->html->DropDown('user_lang', $language_array, $this->data['user_lang']),
			'DD_TIMEZONES'					=> $this->html->DropDown('user_timezone', $this->time->timezones, $this->data['user_timezone']),
			'HIDDEN_FIELDS'					=> (isset($this->data['auth_account'])) ? $this->html->TextField('lmethod', '', $this->in->get('lmethod'), 'hidden').$this->html->TextField('auth_account', '', $this->crypt->encrypt($this->data['auth_account']), 'hidden') : '',

			'USERNAME'						=> $this->data['username'],
			'USER_EMAIL'					=> $this->data['user_email'],
			'USER_EMAIL2'					=> $this->data['user_email2'],
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('register_title'),
			'template_file'		=> 'register.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Register', Register::__shortcuts());
registry::register('Register');
?>