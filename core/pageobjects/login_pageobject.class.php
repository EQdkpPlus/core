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

class login_pageobject extends pageobject {
	public static $shortcuts = array('email'=>'MyMailer', 'crypt' => 'encrypt');

	public function __construct() {
		$handler = array(
			//Process
			'login' 				=> array('process' => 'process_login'),
			'logout' 				=> array('process' => 'process_logout','csrf' => true),
			'lost_password' 		=> array('process' => 'process_lost_password', 'csrf' => true),
			'lostpassword'			=> array('process' => 'display_lost_password'),
			'new_password' 			=> array('process' => 'process_new_password', 'csrf' => true),
			'newpassword'			=> array('process' => 'display_new_password'),
			'resendactivation'		=> array('process' => 'redirect_resend_activation'),
		);
		parent::__construct(false, $handler);

		$this->process();
	}

	public function process_login(){
		if (!$this->user->is_signedin()){

			//Check Password Length
			if (strlen($this->in->get('password')) > 128) {
				$this->core->message($this->user->lang('password_too_long'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}

			//Check Honeypot
			if (strlen($this->in->get($this->user->csrfGetToken("honeypot")))){
				$this->core->message($this->user->lang('invalid_login'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}


			//Check Captcha
			$blnShowCaptcha = false;
			if (((int)$this->config->get('failed_logins_inactivity') - 2) > 0){
				if ($this->user->data['session_failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
					$blnShowCaptcha = true;
				}
				if (!$blnShowCaptcha){
					$objQuery = $this->db->prepare("SELECT SUM(session_failed_logins) as failed_logins FROM __sessions WHERE session_ip =?")->execute($this->env->ip);
					if($objQuery && $objQuery->numRows){
						$arrResult = $objQuery->fetchAssoc();
						if ($arrResult['failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
							$blnShowCaptcha = true;
						}
					}
				}
			}

			if ($blnShowCaptcha){
				$captcha = register('captcha');
				$response = $captcha->verify();
				if (!$response) {
					$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
					$this->display();
					return;
				}
			}

			$blnAutoLogin = ( $this->in->exists('auto_login') ) ? true : false;
			//Login
			if ( !$this->user->login($this->in->get('username'), $this->in->get('password'), $blnAutoLogin) ){
				//error
				$strErrorCode = $this->user->error;
				switch($strErrorCode){
					case 'user_inactive': $strErrorMessage = $this->user->lang('error_account_inactive');
					break;
					case 'user_locked': $strErrorMessage = $this->user->lang('error_account_locked');
					break;
					case 'user_inactive_failed_logins': $strErrorMessage = $this->user->lang('error_account_inactive_failed_logins');
					break;
					case 'wrong_password':
					case 'wrong_username': $strErrorMessage = $this->user->lang('invalid_login');
					break;
					default: $strErrorMessage = $strErrorCode;
				}

				$strInvalidLogin = '';
				if ($strErrorCode != 'user_locked')
				{
					$strInvalidLogin = $this->user->lang('invalid_login_goto_admin');
				}

				$this->core->global_warning($strErrorMessage.$strInvalidLogin, 'fa-exclamation-circle');

				$this->display();

			} else {
				$strContent = "";

				//success
				if($this->hooks->isRegistered('login_pageobject_successfull_login')){
					if($this->in->exists('redirect')){
						$redirect_url = preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $this->SID . '&\\2', base64_decode($this->in->get('redirect')));
						$redirect_url = $this->user->removeSIDfromString($redirect_url);
						if (strpos($redirect_url, '?') === false) {
							$redirect_url = $redirect_url.$this->SID;
						} else {
							$redirect_url = str_replace("?&", $this->SID.'&', $redirect_url);
						}
					} else {
						$redirect_url = $this->controller_path_plain.$this->SID;
					}

					$arrHookData = $this->hooks->process('login_pageobject_successfull_login', array('user_id' => $this->user->id, 'redirect_url' => $redirect_url, 'content' => ''), true);
					$redirect_url = $arrHookData['redirect_url'];
					$strContent = $arrHookData['content'];
				} elseif ($this->in->exists('redirect')){
					$redirect_url = preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $this->SID . '&\\2', base64_decode($this->in->get('redirect')));
					$redirect_url = $this->user->removeSIDfromString($redirect_url);
					if (strpos($redirect_url, '?') === false) {
						$redirect_url = $redirect_url.$this->SID;
					} else {
						$redirect_url = str_replace("?&", $this->SID.'&', $redirect_url);
					}

				} else {
					$redirect_url = $this->controller_path_plain.$this->SID;
				}
				
				if($this->config->get('check_password_leak')){
					if(strlen($this->in->get('password'))){
						$blnLeaked = register('password')->checkIfLeaked($this->in->get('password'));
						if($blnLeaked){
							$redirect_url = $this->controller_path_plain.'Settings/'.$this->SID.'&leaked=true';
						}
					}
				}

				redirect($redirect_url, false, false, true, $strContent);
			}
		} elseif($this->in->exists('lmethod')) {
			redirect($this->controller_path_plain.'Settings/?mode=addauthacc&lmethod='.$this->in->get('lmethod').'&code='.$this->in->get('code').'&token='.$this->in->get('token').'&error='.$this->in->get('error'));
		} else {
			redirect($this->controller_path_plain.$this->SID);
		}

	}

	public function process_logout(){
		if ($this->user->is_signedin()){
			$this->user->logout();
		}
		redirect($this->controller_path_plain.$this->SID);
	}

	public function redirect_resend_activation(){
		redirect($this->controller_path_plain.'Register/ResendActivation/'.$this->SID);
	}

	//Save new password
	public function process_new_password(){

		if((int)$this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_reg_url'))) {
			redirect($this->config->get('cmsbridge_reg_url'),false,true);
		}

		//Check if passwords are the same
		if (strlen($this->in->get('password1', '')) && ($this->in->get('password1', '') === $this->in->get('password2'))){
			if (!strlen($this->in->get('key', ''))){
				message_die($this->user->lang('error_invalid_key'));
			}

			$objQuery = $this->db->prepare("SELECT user_id, user_active, username, user_email
				FROM __users
				WHERE user_email_confirmkey =?")->limit(1)->execute($this->in->get('key', ''));

			if ($objQuery && $objQuery->numRows){
				$row = $objQuery->fetchAssoc();

				// Account's inactive, can't give them their password
				if ( !(int)$row['user_active'] ) {
					message_die($this->user->lang('error_account_inactive'));
				}

				$user_password = $this->in->get('password1');

				$arrSet = array(
						'user_password' => $this->user->encrypt_password($user_password),
						'user_email_confirmkey' => '',
				);

				$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set($arrSet)->execute($row['user_id']);
				if ($objQuery){
					$this->core->message($this->user->lang('password_reset_success'), $this->user->lang('success'), 'green');
					//Send Mail to the user
					$bodyvars = array(
							'USERNAME' => $row['username'],
							'DATETIME'	=> $this->time->user_date($this->time->time, true)
					);
					$this->email->SendMailFromAdmin($this->crypt->decrypt($row['user_email']), $this->user->lang('email_subject_password_changed'), 'user_password_changed.html', $bodyvars);

					$this->display();

					//Destroy all user sessions of this user
					$this->user->destroyUserSessions($row['user_id']);
				} else {
					$this->core->message($this->user->lang('error'),'', 'red');
					$this->display_new_password();
				}

			} else {
				message_die($this->user->lang('error_invalid_key'));
			}

		} else {
			$this->display_new_password();
		}

	}


	//Send email with Key for changing password
	public function process_lost_password(){
		if((int)$this->config->get('cmsbridge_active') == 1 && strlen($this->config->get('cmsbridge_reg_url'))) {
			redirect($this->config->get('cmsbridge_reg_url'),false,true);
		}

		$username	= ( $this->in->exists('username') )	? trim(strip_tags($this->in->get('username'))) : '';

		// Look up record based on the username
		$objQuery = $this->db->prepare("SELECT user_id, username, user_email, user_active, user_lang
				FROM __users
				WHERE LOWER(username)=?")->execute(clean_username($username));
		if ($objQuery){
			$row = $objQuery->fetchAssoc();

			//Check if email
			if(!$row){
				$userid = $this->pdh->get('user', 'userid_for_email', array($username));
				if ($userid) $row = $this->pdh->get('user', 'data', array($userid));
			} else {
				$row['user_email'] = $this->crypt->decrypt($row['user_email']);
			}

			//We have an hit
			if ($row) {
				// Account's inactive, can't give them their password
				if ( !$row['user_active'] ) {
					message_die($this->user->lang('error_account_inactive'));
				}
				$username = $row['username'];

				// Create a new activation key
				$user_key = $this->pdh->put('user', 'create_new_activationkey', array($row['user_id']));
				if(!strlen($user_key)) {
					$this->core->message($this->user->lang('error_set_new_pw'), $this->user->lang('error'), 'red');
					$this->display();
				}

				// Email them their new password

				$bodyvars = array(
						'USERNAME'		=> $row['username'],
						'DATETIME'		=> $this->time->user_date($this->time->time, true),
						'U_ACTIVATE'	=> $this->env->link . $this->controller_path_plain. 'Login/NewPassword/?key=' . $user_key,
				);

				if($this->email->SendMailFromAdmin($row['user_email'], $this->user->lang('email_subject_new_pw'), 'user_new_password.html', $bodyvars)) {
					message_die($this->user->lang('password_sent'), $this->user->lang('get_new_password'));
				} else {
					message_die($this->user->lang('error_email_send'), $this->user->lang('get_new_password'));
				}
			} else {
				message_die($this->user->lang('error_invalid_user_or_mail'), $this->user->lang('get_new_password'));
			}


		} else {
			message_die($this->user->lang('error_invalid_user_or_mail'), $this->user->lang('get_new_password'));
		}

	}

	public function display_new_password(){
		$this->tpl->add_js('document.new_password.password1.focus();', 'docready');

		$this->tpl->add_js("
			$('[data-equalto]').bind('input', function() {
    var to_confirm = $(this);
    var to_equal = $('#' + to_confirm.data('equalto'));

    if(to_confirm.val() != to_equal.val()){
        this.setCustomValidity(\"".$this->jquery->sanitize(registry::fetch('user')->lang('fv_required_password_repeat'))."\");
		$(this).attr('data-fv-message', \"".$this->jquery->sanitize(registry::fetch('user')->lang('fv_required_password_repeat'))."\");
    } else {
        this.setCustomValidity('');
	}
});");

		$this->tpl->assign_vars(array(
			'KEY'	=> sanitize($this->in->get('key', '')),
			'PW_PATTERN' => '.{'.($this->config->get('password_length') ? (int)$this->config->get('password_length') : 8).',}',
			'PASSWORD_LENGTH' => 	($this->config->get('password_length') ? (int)$this->config->get('password_length') : 8),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('create_new_password'),
			'template_file'		=> 'new_password.html',
			'page_path'			=> false,
			'display'			=> true,
		]);
	}

	public function display_lost_password(){
		$this->tpl->add_js('document.lost_password.username.focus();', 'docready');
		$this->tpl->assign_vars(array(
			'BUTTON_NAME'			=> 'lost_password',
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('get_new_password'),
			'template_file'		=> 'lost_password.html',
			'page_path'			=> false,
			'display'			=> true,
		]);
	}

	public function display(){
		if ($this->user->is_signedin()){
			redirect($this->controller_path_plain.'Settings/'. $this->SID);
		}
		$blnShowCaptcha = false;

		if((int)$this->config->get('failed_logins_inactivity') > 0){
			$intFailedLoginCountForCaptcha = (((int)$this->config->get('failed_logins_inactivity') - 2) > 0) ? (int)$this->config->get('failed_logins_inactivity') - 2 : 1;
		} else {
			$intFailedLoginCountForCaptcha = 4;
		}

		if ($this->user->data['session_failed_logins'] >= $intFailedLoginCountForCaptcha){
			$blnShowCaptcha = true;
		}
		if (!$blnShowCaptcha){
			$objQuery = $this->db->prepare("SELECT SUM(session_failed_logins) as failed_logins FROM __sessions WHERE session_ip =?")->execute($this->env->ip);
			if($objQuery && $objQuery->numRows){
				$arrResult = $objQuery->fetchAssoc();
				if ($arrResult['failed_logins'] >= $intFailedLoginCountForCaptcha){
					$blnShowCaptcha = true;
				}
			}
		}

		//Captcha
		if ($blnShowCaptcha){
			$captcha = register('captcha');
			$this->tpl->assign_vars(array(
				'CAPTCHA'				=> $captcha->get(),
				'S_DISPLAY_CATPCHA'		=> true,
			));
		}

		$arrPWresetLink = $this->core->handle_link($this->config->get('cmsbridge_pwreset_url'),$this->user->lang('lost_password'),$this->config->get('cmsbridge_embedded'),'pwreset');

		$this->tpl->add_js('$("#username").focus();', 'docready');
		$this->tpl->assign_vars(array(
			'S_USER_ACTIVATION'		=> ($this->config->get('account_activation') == 1) ? true : false,
			'REDIRECT'				=> ( isset($redirect) ) ? '<input type="hidden" name="redirect" value="'.base64_decode($redirect).'" />' : '',
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('login'),
			'description'		=> $this->user->lang('login'),
			'template_file'		=> 'login.html',
			'page_path'			=> false,
			'display'			=> true,
		]);

	}

}
