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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class login extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'db', 'time', 'env', 'email'=>'MyMailer', 'crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			//Process
			'login' 				=> array('process' => 'process_login'),
			'logout' 				=> array('process' => 'process_logout','csrf' => true),
			'mode' => array(
				array('process' => 'display_lost_password', 'value' => 'lostpassword'),
				array('process' => 'display_new_password', 'value' => 'newpassword'),
			),
			'new_password' 			=> array('process' => 'process_new_password', 'csrf' => true),
			'lost_password' 		=> array('process' => 'process_lost_password', 'csrf' => true),
			'resend_activation_mail' => array('process' => 'redirect_resend_activation'),

		);
		parent::__construct(false, $handler);

		$this->process();
	}

	public function process_login(){
		if (!$this->user->is_signedin()){
			//Check Captcha
			if (((int)$this->config->get('failed_logins_inactivity') - 2) > 0){
				if ($this->user->data['session_failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
					$blnShowCaptcha = true;
				}
				if (!$blnShowCaptcha){
					$resQuery = $this->db->query("SELECT SUM(session_failed_logins) as failed_logins FROM __sessions WHERE session_ip = '".$this->env->ip."'");
					$arrResult = $this->db->fetch_row($resQuery);
					if ($arrResult['failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
						$blnShowCaptcha = true;
					}
				}
			}
		
			if ($blnShowCaptcha){
				require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
				$captcha = new recaptcha;
				$response = $captcha->recaptcha_check_answer ($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('recaptcha_challenge_field'), $this->in->get('recaptcha_response_field'));
				if (!$response->is_valid) {
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
					case 'user_inactive_failed_logins': $strErrorMessage = $this->user->lang('error_account_inactive_failed_logins');
					break;
					case 'wrong_password':
					case 'wrong_username': $strErrorMessage = $this->user->lang('invalid_login');
					break;
					default: $strErrorMessage = $strErrorCode;
				}
				
				$this->core->global_warning($strErrorMessage.$this->user->lang('invalid_login_goto_admin'), 'icon_stop');
				
				$this->display();
				
			} else {
				//success
				if ($this->in->exists('redirect')){
					$redirect_url = preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $this->SID . '&\\2', base64_decode($this->in->get('redirect')));
					if (strpos($redirect_url, '?') === false) {
						$redirect_url = $redirect_url.$this->SID;
					} else {
						$redirect_url = str_replace("?&", $this->SID.'&', $redirect_url);
					}
					
				} else {
					$redirect_url = 'index.php'.$this->SID;
				}
				
				redirect($redirect_url);
			}
		} else {
			redirect('index.php'.$this->SID);
		}

	}

	public function process_logout(){
		if ($this->user->is_signedin()){
			$this->user->logout();
		}
		redirect('index.php'.$this->SID);
	}

	public function redirect_resend_activation(){
		redirect('register.php'.$this->SID.'&mode=resend_activation');
	}

	//Save new password
	public function process_new_password(){
		if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
			if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
				redirect($this->config->get('cmsbridge_reg_url'),false,true);
			}else{
				redirect('index.php'.$this->SID);
			}
		}

		//Check if passwords are the same
		if (strlen($this->in->get('password1', '')) && ($this->in->get('password1', '') === $this->in->get('password2'))){
			if (!strlen($this->in->get('key', ''))){
				message_die($this->user->lang('error_invalid_key'));
			}

			$sql = "SELECT user_id, user_active
				FROM __users
				WHERE user_key ='" .$this->db->escape($this->in->get('key', ''))."'";
			$query = $this->db->query($sql);
			if ( $row = $this->db->fetch_record($query) ) {
				// Account's inactive, can't give them their password
				if ( !(int)$row['user_active'] ) {
					message_die($this->user->lang('error_account_inactive'));
				}

				$user_salt = $this->user->generate_salt();
				$user_password = $this->in->get('password1');

				$arrSet = array(
					'user_password' => $this->user->encrypt_password($user_password, $user_salt).':'.$user_salt,
					'user_key' => '',
				);
				if ($this->db->query("UPDATE __users SET :params WHERE user_id=?", $arrSet, $row['user_id'])){
					$this->core->message($this->user->lang('password_reset_success'), $this->user->lang('success'), 'green');
					$this->display();
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
		if((int)$this->config->get('cmsbridge_reg_redirect') == 1 && (int)$this->config->get('cmsbridge_active') == 1) {
			if(strlen($this->config->get('cmsbridge_reg_url')) > 1){
				redirect($this->config->get('cmsbridge_reg_url'),false,true);
			}else{
				redirect('index.php'.$this->SID);
			}
		}

		$username	= ( $this->in->exists('username') )	? trim(strip_tags($this->in->get('username'))) : '';

		// Look up record based on the username
		$sql = "SELECT user_id, username, user_email, user_active, user_lang
				FROM __users
				WHERE LOWER(username)='".$this->db->escape(clean_username($username))."'";
		$result = $this->db->query($sql);
		$row = $this->db->fetch_record($result);
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
				'DATETIME'		=> $this->time->user_date(false, true),
				'U_ACTIVATE'	=> $this->env->link . 'login.php?mode=newpassword&key=' . $user_key,
			);

			if($this->email->SendMailFromAdmin($row['user_email'], $this->user->lang('email_subject_new_pw'), 'user_new_password.html', $bodyvars)) {
				message_die($this->user->lang('password_sent'), $this->user->lang('get_new_password'));
			} else {
				message_die($this->user->lang('error_email_send'), $this->user->lang('get_new_password'));
			}
		} else {
			message_die($this->user->lang('error_invalid_user_or_mail'), $this->user->lang('get_new_password'));
		}
	}

	public function display_new_password(){
		$this->jquery->Validate('new_password', array(
			array('name' => 'password1', 'value' => $this->user->lang('fv_required_password')),
			array('name' => 'password2', 'value' => $this->user->lang('fv_required_password_repeat'))
		));

		$this->jquery->ResetValidate('new_password');

		$this->tpl->add_js('document.new_password.password1.focus();', 'docready');

		$this->tpl->assign_vars(array(
			'KEY'	=> sanitize($this->in->get('key', '')),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('create_new_password'),
			'template_file'		=> 'new_password.html',
			'display'			=> true,
		));
	}

	public function display_lost_password(){
		$this->jquery->Validate('lost_password', array(
			array('name' => 'username', 'value' => $this->user->lang('fv_required_user')),
			array('name' => 'user_email', 'value' => $this->user->lang('fv_required_email'))
		));
		$this->jquery->ResetValidate('lost_password');

		$this->tpl->add_js('document.lost_password.username.focus();', 'docready');
		$this->tpl->assign_vars(array(
			'F_ACTION'				=> $this->root_path.'login.php'.$this->SID.'&amp;lost_password=true',
			'BUTTON_NAME'			=> 'lost_password',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('get_new_password'),
			'template_file'		=> 'lost_password.html',
			'display'			=> true,
		));
	}

	public function display(){
		if ($this->user->is_signedin()){
			redirect('settings.php'.$this->SID);
		}

		if (((int)$this->config->get('failed_logins_inactivity') - 2) > 0){
			if ($this->user->data['session_failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
				$blnShowCaptcha = true;
			}
			if (!$blnShowCaptcha){
				$resQuery = $this->db->query("SELECT SUM(session_failed_logins) as failed_logins FROM __sessions WHERE session_ip = '".$this->env->ip."'");
				$arrResult = $this->db->fetch_row($resQuery);
				if ($arrResult['failed_logins'] >= ((int)$this->config->get('failed_logins_inactivity') - 2)){
					$blnShowCaptcha = true;
				}
			}
		}
		
		
		//Captcha
		if ($blnShowCaptcha){
			require($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$captcha = new recaptcha;
			$this->tpl->assign_vars(array(
				'CAPTCHA'				=> $captcha->recaptcha_get_html($this->config->get('lib_recaptcha_okey')),
				'S_DISPLAY_CATPCHA'		=> true,
			));
		}


		$this->jquery->Validate('login', array(
			array('name' => 'username', 'value'=> $this->user->lang('fv_required_user')),
			array('name'=>'password', 'value'=>$this->user->lang('fv_required_password'))
		));

		$this->tpl->add_js('document.login.username.focus();', 'docready');
		$this->tpl->assign_vars(array(
			'S_BRIDGE_INFO'			=> ($this->config->get('cmsbridge_active') ==1) ? true : false,
			'S_USER_ACTIVATION'		=> ($this->config->get('account_activation') == 1) ? true : false,
			'REDIRECT'				=> ( isset($redirect) ) ? '<input type="hidden" name="redirect" value="'.base64_decode($redirect).'" />' : '',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('login'),
			'template_file'		=> 'login.html',
			'display'			=> true,
		));

	}

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_login', login::__shortcuts());
registry::register('login');
?>