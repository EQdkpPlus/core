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

class login_persona extends gen_class {
	public static $shortcuts = array('puf'=>'urlfetcher');
	
	private $js_loaded = false;
	
	public static $options = array(
			'connect_accounts'	=> true,
	);
	
	public static $functions = array(
			'login_button'		=> 'login_button',
			'account_button'	=> 'account_button',
			'get_account'		=> 'get_account',
	);
	
	public function __construct(){		
	}
	
	public function init_js(){
		if (!$this->js_loaded){
			$this->tpl->js_file("https://browserid.org/include.js");
			
			$this->tpl->add_js("
var initLogin = function () {
	'use strict';
	var login, connect, loginBtn;
	
	login = function (assertion) {
		if (assertion) {
			window.location.href='".$this->controller_path."Login/".$this->SID."&login&lmethod=persona&assertion='+assertion;
		}
	};
	
	connect = function (e) {
		e.preventDefault();
		navigator.id.get(login);
		return false;
	};
	
	$('.persona_login_button').on('click', connect);
};

initLogin();
", "docready");
		}
	}
		
	public function login_button(){
		$this->init_js();		
		return '<button type="button" class="mainoption persona_login_button thirdpartylogin persona loginbtn"><i class="bi_persona"></i>Persona</button>';
	}
	
	public function account_button(){
		$this->init_js();
		
		$this->tpl->add_js("
		var initAccountLogin = function () {
			'use strict';
			var login, connect, loginBtn;
			
			login = function (assertion) {
				if (assertion) {
					window.location.href='".$this->controller_path."Settings/".$this->SID."&mode=addauthacc&lmethod=persona&assertion='+assertion;
				}
			};
			
			connect = function (e) {
				e.preventDefault();
				navigator.id.get(login);
				return false;
			};
			
			$('.persona_account_button').on('click', connect);
		};

		initAccountLogin();
		", "docready");
		
		
		return '<button type="button" class="mainoption persona_account_button thirdpartylogin persona accountbtn"><i class="bi_persona"></i>Persona</button>';
	}
	
	public function get_account(){
		if ($this->in->get('assertion') != ''){

			$verifyAssertion = $this->verify_assertion($this->in->get('assertion'));
			if ($verifyAssertion) {
				return $verifyAssertion;
			}
		}
		return false;
	}
	
	
	private function verify_assertion($assertion){
		$jsonRequest = $this->puf->post('https://browserid.org/verify', "assertion=".strval(
		   $assertion
		)."&audience=".$this->env->httpHost, "application/x-www-form-urlencoded");
		
		$arrResult = json_decode($jsonRequest);

		if ($arrResult->status==="okay") {
			return strval($arrResult->email);
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
		
		$verifyAssertion = $this->verify_assertion($this->in->get('assertion'));
			
		if ($this->in->get('assertion') != ''){
			$verifyAssertion = $this->verify_assertion($this->in->get('assertion'));
			
			if ($verifyAssertion) {
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($verifyAssertion, 'persona'));
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
		
		return false;
	}
	
	/**
	* User-Logout
	*
	* @return bool
	*/
	public function logout(){
		$this->tpl->add_js("navigator.id.logout();", "docready");
	
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