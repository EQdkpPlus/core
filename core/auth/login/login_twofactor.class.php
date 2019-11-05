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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class login_twofactor extends gen_class {

	public static $options = array(
		'connect_accounts'	=> true,
	);

	public static $functions = array(
		'account_button'	=> 'account_button',
		'get_account'		=> 'get_account',
		'after_login'		=> 'after_login',
		'display_account'	=> 'display_account',
	);

	public function __construct(){
	}

	public function account_button(){
		$this->jquery->dialog('twofactor_init', $this->user->lang('login_twofactor_connect'), array('url' => $this->server_path.'libraries/twofactor/init.php'.$this->SID, 'height'	=> 600, 'width' => 700));
		return '<button type="button" class="mainoption thirdpartylogin twofactor accountbtn" onclick="twofactor_init()">'.$this->user->lang('login_twofactor_connect').'</button>';
	}

	public function get_account(){
		$secret = register('encrypt')->decrypt(rawurldecode($this->in->get('secret')));
		$code = $this->in->get('code');
		if ($secret == "" || $code == "") return false;

		include_once $this->root_path.'libraries/twofactor/googleAuthenticator.class.php';
		$ga = new PHPGangsta_GoogleAuthenticator();
		$checkResult = $ga->verifyCode($secret, $code, 5);		// 2 = 2*30sec clock tolerance
		if ($checkResult) {
			return register('encrypt')->encrypt(serialize(array(
				'secret' => $secret,
				'emergency_token' => $ga->createSecret(16),
			)));
		}

		return false;
	}

	public function display_account($arrOptions){
		$data = unserialize(register('encrypt')->decrypt($arrOptions[0]));
		$out = '<div class="clickToReveal" title="'.$this->user->lang('click_to_reveal').'"><a>**********</a>
					<div><span style="font-weight:bold;">'.$this->user->lang("login_twofactor_key").'</span>: '.$data['secret'].'<br />';
		$out .= '<span style="font-weight:bold;">'.$this->user->lang("login_twofactor_emergency_token").'</span>: '.$data['emergency_token'].'</div></div>';
		return $out;
	}

	public function after_login($arrOptions){
		if((int)$this->config->get('pk_maintenance_mode') == 1) return false;

		if ($arrOptions[0] && $arrOptions[0]['user_id'] != ANONYMOUS && !$this->in->exists('lmethod')){
			//Get Auth Account
			$arrAuthAccounts = $this->pdh->get('user', 'auth_account', array($arrOptions[0]['user_id']));
			if ($arrAuthAccounts['twofactor'] != ""){
				$data = unserialize(register('encrypt')->decrypt($arrAuthAccounts['twofactor']));
				if ($data){
					$cookie = $this->in->getEQdkpCookie("twofactor");
					$cookie_secret = unserialize(register('encrypt')->decrypt($cookie));
					if (($cookie_secret['secret'] === hash("sha256", $data['secret'])) && (intval($cookie_secret['user_id'])===intval($arrOptions[0]['user_id']))) return false;

					$strEncryptedUser = register('encrypt')->encrypt(serialize($arrOptions[0]['user_id']));

					$this->tpl->assign_vars(array(
						'TWOFACTOR_DATA'		=>  $strEncryptedUser.':'.$this->time->time.':'.hash_hmac("sha256", $strEncryptedUser.'_'.$this->time->time.'.'.$arrOptions[0]['user_id'], hash("sha256", registry::get_const('encryptionKey'))),
						'TWOFACTOR_AUTOLOGIN'	=> ($arrOptions[4]) ? 'checked' : '',
					));

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
						if ($objQuery){
							$arrResult = $objQuery->fetchAssoc();
							if ((int)$arrResult['failed_logins'] >= $intFailedLoginCountForCaptcha){
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

					$this->core->set_vars(array(
							'page_title'		=> $this->user->lang("login_twofactor"),
							'template_file'		=> 'twofactor_login.html',
							'display'			=> true)
					);
				}
			}
		}
		return false;
	}

	/**
	* User-Login
	*
	* @param $strUsername
	* @param $strPassword
	* @return bool/array
	*/
	public function login($strUsername, $strPassword){
		list($serializedUser, $intTimestamp, $strHmac) = explode(':', $this->in->get('twofactor_data'));
		$user = unserialize(register('encrypt')->decrypt($serializedUser));
		$strCalcMac = hash_hmac("sha256", $serializedUser.'_'.$intTimestamp.'.'.$user, hash("sha256", registry::get_const('encryptionKey')));

		if($strCalcMac !== $strHmac) return false;
		if ($intTimestamp < ($this->time->time-5*60)) return false;

		$code = $this->in->get('twofactor_code');
		$blnLoginResult = false;

		if ($user == "" || $code == "") return false;

		if ($user && $user != ANONYMOUS){
			$arrAuthAccounts = $this->pdh->get('user', 'auth_account', array($user));
			if ($arrAuthAccounts['twofactor'] != ""){
				$data = unserialize(register('encrypt')->decrypt($arrAuthAccounts['twofactor']));
				if ($data){
					if ($code === $data['emergency_token']){
						$this->pdh->put('user', 'delete_authaccount', array($user, "twofactor"));
						$userdata = $this->pdh->get('user', 'data', array($user));
						if ($userdata){
							return array(
									'status'		=> 1,
									'user_id'		=> $userdata['user_id'],
									'password_hash'	=> $userdata['user_password'],
									'autologin'		=> true,
									'user_login_key' => $userdata['user_login_key'],
							);
						}
					}

					//Check Code
					if (!$blnLoginResult){
						include_once $this->root_path.'libraries/twofactor/googleAuthenticator.class.php';
						$ga = new PHPGangsta_GoogleAuthenticator();
						$checkResult = $ga->verifyCode($data['secret'], $code, 5);    // 2 = 2*30sec clock tolerance
						if ($checkResult) {
							$blnLoginResult = true;
							$userdata = $this->pdh->get('user', 'data', array($user));
							if ($userdata){
								if ($this->in->get('twofactor_cookie', 0)){
									set_cookie("twofactor", register('encrypt')->encrypt(serialize(array('secret' => hash("sha256", $data['secret']), 'user_id' => $userdata['user_id']))), time()+60*60*24*30);
								}

								return array(
									'status'			=> 1,
									'user_id'			=> $userdata['user_id'],
									'password_hash'		=> $userdata['user_password'],
									'autologin'			=> true,
									'user_login_key'	 => $userdata['user_login_key'],
								);
							}
						}
					}
				}
			}

		}

		return false;
	}

	/**
	* User-Logout
	*
	* @return bool
	*/
	public function logout(){
		return true;
	}

	/**
	* Autologin
	*
	* @param $arrCookieData The Data ot the Session-Cookies
	* @return bool
	*/
	public function autologin($arrCookieData){
		return false;
	}
}
