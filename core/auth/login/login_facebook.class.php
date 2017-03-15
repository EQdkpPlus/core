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

class login_facebook extends gen_class {
	private $fb_loaded = false;
	private $js_loaded = false;
	private $objFacebook = false;

	public static $functions = array(
		'login_button'		=> 'login_button',
		'register_button' 	=> 'register_button',
		'account_button'	=> 'account_button',
		'get_account'		=> 'get_account',
		'pre_register'		=> 'pre_register',
		'after_register'	=> 'after_register',
	);
	public static $options = array(
		'connect_accounts'	=> true,
	);

	public function __construct(){
	}

	public function settings(){
		$settings = array(
			'login_fb_appid'	=> array(
				'type'	=> 'text',
			),
			'login_fb_appsecret' => array(
				'type'	=> 'text',
			),
		);
		return $settings;
	}

	public function init_fb(){
		if (!$this->fb_loaded){

			//Init Facebook Api
			try {
				$fb = new Facebook\Facebook([
						'app_id' => $this->config->get('login_fb_appid'),
						'app_secret' => $this->config->get('login_fb_appsecret'),
						'default_graph_version' => 'v2.5',
				]);

				$this->objFacebook = $fb;

			} catch (Exception $e) {
				$this->core->message($e->getMessage(), "Facebook Exception", 'error');
			}

			$this->init_js();
			$this->fb_loaded = true;

		}
	}

	public function init_js(){
		if(!$this->js_loaded && $this->config->get('login_fb_appid') != ""){
			$this->tpl->staticHTML('<div id="fb-root"></div>');
			$this->tpl->add_js("

				window.fbAsyncInit = function() {
					FB.init({
					  appId   : '".$this->config->get('login_fb_appid')."',
					  status  : true, // check login status
					  cookie  : true, // enable cookies to allow the server to access the session
					  xfbml   : true,
					  version : 'v2.0'
					});
				}

				  $(document).ready(function(){
					var e = document.createElement('script');
					e.src = document.location.protocol + '//connect.facebook.net/en_US/sdk.js';
					e.async = true;
					$('body').append(e);
				  });
			");

			$this->js_loaded = true;
		}
	}


	public function getMe($access_token){
		if (!$access_token) return false;

		if(!is_object($this->objFacebook)) return false;

		// Request for user data
		try {
			$response = $this->objFacebook->get('/me?fields=id,name,birthday,email,first_name,last_name,locale', $access_token);
			if($response){
				$arrBody = $response->getDecodedBody();
				return array(
						'data'	=> $arrBody,
						'uid'	=> $arrBody['id'],
				);
			}

		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception getMe()", 'red');
		}

		return false;
	}

	public function login_button(){
		$this->init_js();

		$this->tpl->add_js("
			function facebook_login(){
				FB.login(function(response) {
				   if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					if (response.status == 'connected') window.location.href='".$this->controller_path."Login/".$this->SID."&login&lmethod=facebook&act='+response.authResponse.accessToken;
				   } else {
					 console.log('User cancelled login or did not fully authorize.');
				   }
				 }, {scope: 'email,public_profile'});
			}
		");

		return '<button type="button" class="mainoption thirdpartylogin facebook loginbtn" onclick="facebook_login()"><i class="fa fa-facebook fa-lg"></i> Facebook</button>';
	}

	public function register_button(){
		$this->init_js();

		$this->tpl->add_js("
			function facebook_register(){
				FB.login(function(response) {
				   if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					 if (response.status == 'connected') window.location.href='".$this->controller_path."Register/".$this->SID."&register&lmethod=facebook&act='+response.authResponse.accessToken;
				   } else {
					 console.log('User cancelled login or did not fully authorize.');
				   }
				 }, {scope: 'email,public_profile'});
			}
	  ");

		return '<button type="button" class="mainoption thirdpartylogin facebook registerbtn" onclick="facebook_register()"><i class="fa fa-facebook fa-lg"></i> Facebook</button>';
	}

	public function account_button(){
		$this->init_fb();

		try {
			if(is_object($this->objFacebook)){
				$helper = $this->objFacebook->getJavaScriptHelper();
				$token = $helper->getAccessToken();
			} else $token = false;

			if ($token){
				$me = $this->getMe($token);
				if ($me){
					$uid = $me['uid'];
					return $me['data']['name']	.' <button type="button" class="mainoption thirdpartylogin facebook accountbtn" onclick="window.location.href = \''.$this->controller_path.'Settings/'.$this->SID.'&mode=addauthacc&lmethod=facebook\';"><i class="fa fa-facebook fa-lg"></i> Facebook</button>'.(new hhidden('auth_account', array('value' => $uid)))->output();
				}
			} else {
				$this->tpl->add_js("
				function facebook_connect_acc(){
					FB.login(function(response) {
					   if (response.authResponse) {
						 console.log('Welcome!  Fetching your information.... ');
						  if (response.status == 'connected') window.location.href='".$this->controller_path."Settings/".$this->SID."&mode=addauthacc&lmethod=facebook&act='+response.authResponse.accessToken;
					   } else {
						 console.log('User cancelled login or did not fully authorize.');
					   }
					 }, {scope: 'email,public_profile'});
				}
				");
				return '<button type="button" class="mainoption thirdpartylogin facebook accountbtn" onclick="facebook_connect_acc()"><i class="fa fa-facebook fa-lg"></i> Facebook</button>';

			}
		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception get_account()", 'error');
		}
	}

	public function get_account(){
		$this->init_fb();

		if(!is_object($this->objFacebook)) return false;

		try {
			$helper = $this->objFacebook->getJavaScriptHelper();

			$token = $helper->getAccessToken();
			if ($token){
				$me = $this->getMe($token);
				if ($me){
					$uid = $me['uid'];
					return $uid;
				}
			} elseif($this->in->get('act') != ""){
				$me = $this->getMe($this->in->get('act'));
				if ($me){
					$uid = $me['uid'];
					return $uid;
				}
			}
		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception get_account()", 'red');
		}

		return false;
	}

	public function pre_register(){
		$this->init_fb();
		if(!is_object($this->objFacebook)) return false;
		try {
			$token = $this->get_longterm_token($this->in->get('act'));
			if(!$token) {
				$token = $this->in->get('act');
			} else {
				$this->user->setSessionVar('fb_token', $token);
			}

			$me = $this->getMe($token);

			if ($me){

				if ($me['data']['locale']){
					list($locale1, $locale2) = explode('_', $me['data']['locale']);
				}

				return array(
						'username'			=> $this->in->get('username', ($me['data']['name'] != null) ? $me['data']['name'] : ''),
						'user_email'		=> $this->in->get('user_email', ($me['data']['email']  != null) ? $me['data']['email'] : ''),
						'user_email2'		=> $this->in->get('user_email2', ($me['data']['email']  != null) ? $me['data']['email'] : ''),
						'first_name'		=> $this->in->get('first_name', ($me['data']['first_name']  != null) ? $me['data']['first_name'] : ''),
						'user_lang'			=> $this->in->get('user_lang',	$this->config->get('default_lang')),
						'user_timezone'		=> $this->in->get('user_timezone',	$this->config->get('timezone')),
						'user_password1'	=> $this->in->get('new_user_password1'),
						'user_password2'	=> $this->in->get('new_user_password2'),
						'auth_account'		=> $me['uid'],
				);
			}
		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception preRegister()", 'red');
		}

		return false;
	}

	public function after_register(){
		$this->init_fb();

		$out = false;
		try {
			if($this->user->data['session_vars']['fb_token']){
				$token = $this->user->data['session_vars']['fb_token'];
			} else {
				if(is_object($this->objFacebook)){
					$helper = $this->objFacebook->getJavaScriptHelper();
					$token = $helper->getAccessToken();
				}
			}

			if ($token){
				$me = $this->getMe($token);
				if ($me){
					//Check Email
					if ($this->in->get('user_email') == $me['data']['email']){
						$out['user_active'] = 1;
					}

					//First Name
					//if($me['data']->getProperty('first_name')  != null)  $out['name'] = $me['data']->getProperty('first_name');
					//Last Name
					//if($me['data']->getProperty('last_name')  != null)  $out['last_name'] = $me['data']->getProperty('last_name');

					//Country
					if ($me['data']['locale']){
						list($locale1, $locale2) = explode('_', $me['data']['locale']);
						$out['country'] = $locale2;
					}

					return $out;
				}
			}

		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception afterRegister()", 'red');
		}

		return false;
	}

	/**
	* User-Login for Facebook
	*
	* @param $strUsername
	* @param $strPassword
	* @param $boolUseHash Use Hash for comparing
	* @return bool/array
	*/
	public function login($strUsername, $strPassword, $boolUseHash = false){
		$this->init_fb();

		$blnLoginResult = false;
		try {
			$access_token = $this->in->get('act');
			$me = $this->getMe($access_token);

			if ($me && $strPassword == ''){
				$userid = $this->pdh->get('user', 'userid_for_authaccount', array($me['uid'], 'facebook'));
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

		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception login()", 'red');
		}

		return false;
	}

	/**
	* User-Logout
	*
	* @return bool
	*/
	public function logout(){
		$this->init_fb();

		if(!is_object($this->objFacebook)) return true;

		try {
			$helper = $this->objFacebook->getRedirectLoginHelper();

			$js_helper = $this->objFacebook->getJavaScriptHelper();

			$token = $js_helper->getAccessToken();
			if($token){
				$strLogoutURL = $helper->getLogoutUrl($token, $this->env->link.$this->controller_path_plain.'Login/Logout'.$this->routing->getSeoExtension().$this->SID.'&amp;link_hash='.$this->user->csrfGetToken("login_pageobjectlogout"));
				redirect($strLogoutURL, false, true);
			}

		} catch(Exception $e){
			$this->core->message($e->getMessage(), "Facebook Exception logout()", 'red');
		}

		return true;
	}

	/**
	* Autologin
	*
	* @param $arrCookieData The Data ot the Session-Cookies
	* @return bool
	*/
	public function autologin($arrCookieData){
		if(!$this->config->get('login_fb_appid') || $this->config->get('login_fb_appid') == "") return false;
		
		
		$this->init_fb();
		if(!is_object($this->objFacebook)) return false;

		try {
			$helper = $this->objFacebook->getJavaScriptHelper();

			$accessToken = $helper->getAccessToken();
			if($accessToken){
				$me = $this->getMe($accessToken);
				if ($me){
					$uid = $me['uid'];
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($uid, 'facebook'));
					if ($userid){
						$userdata = $this->pdh->get('user', 'data', array($userid));
						return ($userdata) ? $userdata : false;
					}

				}
			}

		} catch(Exception $e){
			//$this->core->message($e->getMessage(), "Facebook Exception autologin()", 'red');
		}

		return false;
	}

	private function get_longterm_token($strAccessToken){
		$params = array(
				'client_id' 		=> $this->config->get('login_fb_appid'),
				'client_secret'		=> $this->config->get('login_fb_appsecret'),
				'grant_type'		=> 'fb_exchange_token',
				'fb_exchange_token' => $strAccessToken
		);

		$url = 'https://graph.facebook.com/oauth/access_token?'.http_build_query($params);

		$mixResult = register('urlfetcher')->fetch($url);
		if($mixResult){
			$keyArray = array();
			$arrValues = explode("&", $mixResult);
			foreach($arrValues as $val){
				$arrKeys = explode("=", $val);
				$keyArray[$arrKeys[0]] = $arrKeys[1];
			}

			if(isset($keyArray['access_token'])){
				return $keyArray['access_token'];
			}
		}

		return false;
	}
}
?>