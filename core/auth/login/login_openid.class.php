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
		$this->tpl->add_js('
		function openid_login_selector(obj){
			$(obj).find(".openid_button_name").hide();
			$(obj).find(".openid_button_input").show();
			$(obj).find(".openid_button_input input").focus();

			var myvalue = $(obj).find(".openid_button_input input").val();

			if(myvalue != ""){
				window.location = "'.$this->env->buildLink().'index.php/Login/?login&lmethod=openid&openid="+myvalue;
			}
		}

		function submit_openid_login(obj){
			var myvalue = $(obj).find(".openid_button_input input").val();

			if(myvalue != ""){
				window.location = "'.$this->env->buildLink().'index.php/Login/?login&lmethod=openid&openid="+myvalue;
			}
			return false;
		}
		');

		return '<form onsubmit="return submit_openid_login(this)" style="display:inline;"><button type="button" class="mainoption thirdpartylogin openid loginbtn" onclick="openid_login_selector(this)"><i class="bi_openid"></i><div class="openid_button_name" style="display:inline;">OpenID</div><span class="openid_button_input" style="display:none;"><input type="text" placeholder="https://" size="20"></span></button></form>';
	}

	public function register_button(){
		$this->tpl->add_js('
		function openid_login_selector(obj){
			$(obj).find(".openid_button_name").hide();
			$(obj).find(".openid_button_input").show();
			$(obj).find(".openid_button_input input").focus();

			var myvalue = $(obj).find(".openid_button_input input").val();

			if(myvalue != ""){
				window.location = "'.$this->env->buildLink().'Register/?register&lmethod=openid&openid="+myvalue;
			}
		}

		function submit_openid_login(obj){
			var myvalue = $(obj).find(".openid_button_input input").val();

			if(myvalue != ""){
				window.location = "'.$this->env->buildLink().'Register/?register&lmethod=openid&openid="+myvalue;
			}
			return false;
		}
		');

		return '<form onsubmit="return submit_openid_login(this)" style="display:inline;"><button type="button" class="mainoption thirdpartylogin openid registerbtn" onclick="openid_login_selector(this)"><i class="bi_openid"></i><div class="openid_button_name" style="display:inline;">OpenID</div><span class="openid_button_input" style="display:none;"><input type="text" placeholder="https://" size="20"></span></button></form>';
	}

	public function account_button(){
		$link_hash = ((string)$this->user->csrfGetToken('settings_pageobjectmode'));

		$this->tpl->add_js('
		function openid_login_selector(obj){
			$(obj).find(".openid_button_name").hide();
			$(obj).find(".openid_button_input").show();
			$(obj).find(".openid_button_input input").focus();

			var myvalue = $(obj).find(".openid_button_input input").val();

			if(myvalue != ""){
				window.location = "'.$this->env->buildLink().'index.php/Settings/?mode=addauthacc&link_hash='.$link_hash.'&lmethod=openid&openid="+myvalue;
			}
		}
		');

		return '<button type="button" class="mainoption thirdpartylogin openid accountbtn" onclick="openid_login_selector(this)"><i class="bi_openid"></i><div class="openid_button_name" style="display:inline;">OpenID</div><span class="openid_button_input" style="display:none;"><input type="text" placeholder="https://" size="20"></span></button>';
	}

	public function get_account(){
		try {
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
		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'OpenID Error', 'red');
		}
		return false;
	}

	public function pre_register(){
		try {
			if ($this->in->get('openid') != ''){
				$this->init_openid();
				if(!$this->oid->mode) {
					$this->oid->required = array(
					'namePerson/friendly',
					'contact/email',
					'namePerson',
					//'person/gender',
					//'contact/country/home',
					);
					$this->oid->identity = $this->in->get('openid');

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
			}
		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'OpenID Error', 'red');
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
								return array(
									'status'		=> 1,
									'user_id'		=> $userdata['user_id'],
									'password_hash'	=> $userdata['user_password'],
									'autologin'		=> true,
									'user_login_key' => $userdata['user_login_key'],
								);
							}
						} elseif((int)$this->config->get('cmsbridge_active') != 1 && (int)$this->config->get('login_fastregister')){
							redirect($this->controller_path_plain.'Register/?register&lmethod=openid&openid='.sanitize($this->oid->identity));
						}
					}
				}
			}
		} catch (Exception $e) {
			$this->core->message($e->getMessage(), 'OpenID Error', 'red');
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
