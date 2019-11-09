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

class login_steam extends gen_class {
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

	public $steamURL = "https://steamcommunity.com/openid";

	public function __construct(){
		$this->init_openid();
	}

	public function init_openid(){
		if (!is_object($this->oid)){
			//Init Facebook Api
			require_once($this->root_path.'libraries/openid/openid.php');
			$openid = new LightOpenID($this->env->link);
			$this->oid = $openid;
			$this->oid->identity = $this->steamURL;
		}
	}

	public function login_button(){
		$auth_url = $this->env->buildLink().'index.php/Login/?login&lmethod=steam';
		return '<button type="button" class="mainoption thirdpartylogin steam loginbtn" onclick="window.location=\''.$auth_url.'\'"><i class="fa fa-steam fa-lg"></i> Steam</button>';
	}

	public function register_button(){
		$auth_url = $this->env->buildLink().'index.php/Register/?register&lmethod=steam';
		return '<button type="button" class="mainoption thirdpartylogin steam registerbtn" onclick="window.location=\''.$auth_url.'\'"><i class="fa fa-steam fa-lg"></i> Steam</button>';
	}

	public function account_button(){
		$link_hash = ((string)$this->user->csrfGetToken('settings_pageobjectmode'));
		$auth_url = $this->env->buildLink().'index.php/Settings/?mode=addauthacc&lmethod=steam&link_hash='.$link_hash;
		return '<button type="button" class="mainoption thirdpartylogin steam accountbtn" onclick="window.location=\''.$auth_url.'\'"><i class="fa fa-steam fa-lg"></i> Steam</button>';
	}

	public function get_account(){
		try {
			if(!$this->oid->mode) {
				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {

			} else {
				if ($this->oid->validate() ){
					return $this->oid->identity;
				}
			}

		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'Steam Error', 'red');
		}
		return false;
	}

	public function pre_register(){
		try {
			$this->init_openid();

			if(!$this->oid->mode) {
				$this->oid->required = array(
				'namePerson/friendly',
				'contact/email',
				'namePerson',
				//'person/gender',
				//'contact/country/home',
				);

				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {

			} else {
				if ($this->oid->validate() ){
					$me = $this->oid->getAttributes();

					$bla = array(
						'username'			=> isset($me['namePerson/friendly']) ? $me['namePerson/friendly'] : '',
						'user_email'		=> isset($me['contact/email']) ? $me['contact/email'] : '',
						'user_email2'		=> isset($me['contact/email']) ? $me['contact/email'] : '',
						'auth_account'		=> $this->oid->identity,
						'user_timezone'		=> $this->in->get('user_timezone', $this->config->get('timezone')),
					);

					return $bla;

				}
			}

		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'Steam Error', 'red');
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
		try {
			$this->init_openid();
			if(!$this->oid->mode) {
				redirect($this->oid->authUrl(), false, true);
			} elseif($this->oid->mode == 'cancel') {


			} else {

				if ($this->oid->validate() ){
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($this->oid->identity, 'steam'));
					if ($userid){
						$userdata = $this->pdh->get('user', 'data', array($userid));
						if ($userdata){
							return array(
								'status'		=> 1,
								'user_id'		=> $userdata['user_id'],
								'password_hash'	=> $userdata['user_password'],
								'autologin'		=> true,
								'user_login_key' => $userdata['user_login_key'],
							);
						}
					} elseif((int)$this->config->get('cmsbridge_active') != 1 && (int)$this->config->get('login_fastregister')){
						redirect($this->env->buildLink().'index.php/Register/?register&lmethod=steam', false, true);
					}
				}
			}

		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'Steam Error', 'red');
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
