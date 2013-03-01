<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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
 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class login_persona extends gen_class {
	public static $shortcuts = array('user', 'jquery', 'db', 'in', 'config', 'env' => 'environment', 'pdh', 'tpl', 'puf'=>'urlfetcher');
	
	private $js_loaded = false;
	
	public function __construct(){
		
		$this->options = array(
			'connect_accounts'	=> true,
		);
		
		$this->functions = array(
			'login_button'		=> 'login_button',
			'account_button'	=> 'account_button',
			'get_account'		=> 'get_account',
		);
		
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
			window.location.href='".$this->root_path."login.php".$this->SID."&login=true&lmethod=persona&assertion='+assertion;
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
		return '<input type="button" class="mainoption bi_persona persona_login_button" value="Persona Login" />';
	}
	
	public function account_button(){
		$this->init_js();
		
		$this->tpl->add_js("
		var initAccountLogin = function () {
			'use strict';
			var login, connect, loginBtn;
			
			login = function (assertion) {
				if (assertion) {
					window.location.href='".$this->root_path."settings.php".$this->SID."&mode=addauthacc&lmethod=persona&assertion='+assertion;
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
		
		
		return '<input type="button" class="mainoption bi_persona persona_account_button" value="Persona '.$this->user->lang('auth_connect_account').'" />';
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
		)."&audience=".$_SERVER["HTTP_HOST"], "application/x-www-form-urlencoded");
		
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
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($verifyAssertion));
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

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_login_persona', login_persona::$shortcuts);
?>