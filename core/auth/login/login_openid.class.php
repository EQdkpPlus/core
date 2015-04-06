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

class login_openid extends gen_class {
	public $oid = false;
	public static $options = array(
		'connect_accounts'	=> true,
	);
	
	public static $functions = array(
		'login_button'		=> 'login_button',
		'register_button' 	=> 'register_button',
		'account_button'	=> 'account_button',
		'get_account'		=> 'get_account',
		'pre_register'		=> 'pre_register',
	);
	
	public function __construct(){		
	}
	
	public function init_openid(){
		if (!is_object($this->oid)){
			//Init Facebook Api
			require_once($this->root_path.'libraries/openid/openid.php');
			$openid = new LightOpenID($this->env->link);
			$this->oid = $openid;
		}
	}
	
	public function login_button(){
		$this->jquery->dialog('openid_login_selector', 'OpenID', array('url' => $this->server_path.'libraries/openid/selector/selector.html', 'height'	=> 400));
		
		return '<button type="button" class="mainoption thirdpartylogin openid loginbtn" onclick="openid_login_selector()"><i class="bi_openid"></i>OpenID</button>';
	}
	
	public function register_button(){
		$this->jquery->dialog('openid_reg_selector', 'OpenID', array('url' => $this->server_path.'libraries/openid/selector/reg_selector.html', 'height'	=> 400));
		
		return '<button type="button" class="mainoption thirdpartylogin openid registerbtn" onclick="openid_reg_selector()"><i class="bi_openid"></i>OpenID</button>';		
	}
	
	public function account_button(){
		$this->jquery->dialog('openid_acc_selector', 'OpenID', array('url' => $this->server_path.'libraries/openid/selector/acc_selector.html', 'height'	=> 400));
		
		return '<button type="button" class="mainoption thirdpartylogin openid accountbtn" onclick="openid_acc_selector()"><i class="bi_openid"></i>OpenID</button>';
	}
	
	public function get_account(){
		if ($this->in->get('openid') != ''){
			$this->init_openid();
			if(!$this->oid->mode) {
				$this->oid->identity = $this->in->get('openid');
				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {
			
			} else {
				if ($this->oid->validate() ){
					return $this->oid->identity;
				}
			}
		}
		
		return false;
	}
	
	public function pre_register(){
		if ($this->in->get('openid') != ''){
			$this->init_openid();
			if(!$this->oid->mode) {
				$this->oid->required = array(
				'namePerson/friendly',
				'contact/email',
				'namePerson',
				'person/gender',
				'contact/country/home',
				);
				$this->oid->identity = $this->in->get('openid');

				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {
			
			} else {
				if ($this->oid->validate() ){
					$me = $this->oid->getAttributes();

					switch($me['person/gender']){
						case 'M' : $gender = '1'; break;
						case 'F' : $gender = '2'; break;
						default: $gender = '0';
					}
					$bla = array(
						'username'			=> isset($me['namePerson/friendly']) ? $me['namePerson/friendly'] : '',
						'user_email'		=> isset($me['contact/email']) ? $me['contact/email'] : '',
						'user_email2'		=> isset($me['contact/email']) ? $me['contact/email'] : '',
						'first_name'		=> isset($me['namePerson']) ? $me['namePerson'] : '',
						'gender'			=> $gender,
						'country'			=> isset($me['contact/country/home']) ? $me['contact/country/home'] : '',
						'auth_account'		=> $this->oid->identity,
						'user_timezone'		=> $this->in->get('user_timezone', $this->config->get('timezone')),
					);

					return $bla;
					
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
	* @param $boolUseHash Use Hash for comparing
	* @return bool/array	
	*/	
	public function login($strUsername, $strPassword, $boolUseHash = false){
		$blnLoginResult = false;
		
			
		if ($this->in->get('openid') != ''){
			$this->init_openid();
			if(!$this->oid->mode) {
				$this->oid->identity = $this->in->get('openid');
				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {
			
			
			} else {
			
				if ($this->oid->validate() ){
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($this->oid->identity, 'openid'));
					if ($userid){
						$userdata = $this->pdh->get('user', 'data', array($userid));
						if ($userdata){
							list($strPwdHash, $strSalt) = explode(':', $userdata['user_password']);
							return array(
								'status'		=> 1,
								'user_id'		=> $userdata['user_id'],
								'password_hash'	=> $strPwdHash,
								'autologin'		=> true,
								'user_login_key' => $userdata['user_login_key'],
							);
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
?>