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

class login_facebook extends gen_class {
	private $fb_loaded = false;
	
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
				//Exceptions
				include_once($this->root_path.'libraries/facebook/FacebookSDKException.php');
				include_once($this->root_path.'libraries/facebook/FacebookRequestException.php');
				include_once($this->root_path.'libraries/facebook/FacebookAuthorizationException.php');
				include_once($this->root_path.'libraries/facebook/FacebookPermissionException.php');
				include_once($this->root_path.'libraries/facebook/FacebookServerException.php');
				include_once($this->root_path.'libraries/facebook/FacebookThrottleException.php');
				
				include_once($this->root_path.'libraries/facebook/FacebookHttpable.php');
				include_once($this->root_path.'libraries/facebook/FacebookCanvasLoginHelper.php');
				include_once($this->root_path.'libraries/facebook/FacebookClientException.php');
				include_once($this->root_path.'libraries/facebook/FacebookCurl.php');
				include_once($this->root_path.'libraries/facebook/FacebookCurlHttpClient.php');
				include_once($this->root_path.'libraries/facebook/FacebookJavaScriptLoginHelper.php');
				include_once($this->root_path.'libraries/facebook/FacebookOtherException.php');
				include_once($this->root_path.'libraries/facebook/FacebookPageTabHelper.php');
				include_once($this->root_path.'libraries/facebook/FacebookRedirectLoginHelper.php');
				include_once($this->root_path.'libraries/facebook/FacebookRequest.php');
				include_once($this->root_path.'libraries/facebook/FacebookResponse.php');
				include_once($this->root_path.'libraries/facebook/FacebookSession.php');
				
				include_once($this->root_path.'libraries/facebook/GraphObject.php');
				include_once($this->root_path.'libraries/facebook/GraphAlbum.php');
				include_once($this->root_path.'libraries/facebook/GraphLocation.php');
				include_once($this->root_path.'libraries/facebook/GraphSessionInfo.php');
				include_once($this->root_path.'libraries/facebook/GraphUser.php');
				
				session_start();
				Facebook\FacebookSession::setDefaultApplication($this->config->get('login_fb_appid'), $this->config->get('login_fb_appsecret'));
	
			} catch (Exception $e) {
				$this->core->message($e->getMessage(), "Facebook Exception", 'error');
			}
			
			$this->init_js();
			$this->fb_loaded = true;
		
		}
	}
	
	public function init_js(){
			
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
	}
	
	
	public function getMe($session){
		if (!$session) return false;
		
		// Request for user data
		try {
			$request = new Facebook\FacebookRequest( $session, 'GET', '/me' );
			$response = $request->execute();
			// Responce
			$data = $response->getGraphObject();
		
			if ($data){
				return array(
					'data'	=> $data,
					'uid'	=> $data->getProperty("id"),
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
			$helper = new Facebook\FacebookJavaScriptLoginHelper();
			$session = $helper->getSession();
			if ($session){
				$me = $this->getMe($session);
				if ($me){
					$uid = $me['uid'];
					return $me['data']->getProperty("name")	.' <button type="button" class="mainoption thirdpartylogin facebook accountbtn" onclick="window.location.href = \''.$this->controller_path.'Settings/'.$this->SID.'&mode=addauthacc&lmethod=facebook\';"><i class="fa fa-facebook fa-lg"></i> Facebook</button>'.new hhidden('auth_account', array('value' => $uid));
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
		
		try {
			$helper = new Facebook\FacebookJavaScriptLoginHelper();
			$session = $helper->getSession();
			if ($session){
				$me = $this->getMe($session);
				if ($me){
					$uid = $me['uid'];
					return $uid;
				}
			} elseif($this->in->get('act') != ""){
				$session = new Facebook\FacebookSession($this->in->get('act'));
				$me = $this->getMe($session);
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
		try {
			$session = new FacebookSession($this->in->get('act'));
			$me = $this->getMe($session);
	
			if ($me){
				
				switch($me['data']->getProperty('gender')){
					case 'male' : $gender = '1'; break;
					case 'female' : $gender = '2'; break;
					default: $gender = '0';
				}
				
				if ($me['data']->getProperty('locale')){
					list($locale1, $locale2) = explode('_', $me['data']->getProperty('locale'));
				}
				
				return array(
						'username'			=> $this->in->get('username', ($me['data']->getProperty('name') != null) ? $me['data']->getProperty('name') : ''),
						'user_email'		=> $this->in->get('user_email', ($me['data']->getProperty('email')  != null) ? $me['data']->getProperty('email') : ''),
						'user_email2'		=> $this->in->get('user_email2', ($me['data']->getProperty('email')  != null) ? $me['data']->getProperty('email') : ''),
						'first_name'		=> $this->in->get('first_name', ($me['data']->getProperty('first_name')  != null) ? $me['data']->getProperty('first_name') : ''),
	
						'country'			=> $this->in->get('country', $locale2),
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
			$helper = new Facebook\FacebookJavaScriptLoginHelper();
			$session = $helper->getSession();
			if ($session){
				$me = $this->getMe($session);
				if ($me){

					//Gender
					switch($me['data']->getProperty('gender')){
						case 'male' : $gender = '1'; break;
						case 'female' : $gender = '2'; break;
						default: $gender = '0';
					}
					
					//Check Email
					if ($this->in->get('user_email') == $me['data']->getProperty('email')){
						$out['user_active'] = 1;
					}
					
					//First Name
					if($me['data']->getProperty('first_name')  != null)  $out['first_name'] = $me['data']->getProperty('first_name');
					//Last Name
					if($me['data']->getProperty('last_name')  != null)  $out['last_name'] = $me['data']->getProperty('last_name');
					
					//Country
					if ($me['data']->getProperty('locale')){
						list($locale1, $locale2) = explode('_', $me['data']->getProperty('locale'));
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
			$session = new Facebook\FacebookSession($this->in->get('act'));
			
			$me = $this->getMe($session);

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
		try {
			$helper = new Facebook\FacebookJavaScriptLoginHelper();
			$session = $helper->getSession();
			if ($session){
				$me = $this->getMe($session);
				if ($me){
					$helper = new Facebook\FacebookRedirectLoginHelper( $this->env->link.$this->controller_path_plain );
					redirect($helper->getLogoutUrl($session, $this->env->link.$this->controller_path_plain.'Login/Logout'.$this->routing->getSeoExtension().$this->SID.'&amp;link_hash='.$this->user->csrfGetToken("login_pageobjectlogout")), false, true);	
				}
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
		$this->init_fb();
		try {
			$helper = new Facebook\FacebookJavaScriptLoginHelper();
			$session = $helper->getSession();
			
			if ($session){
				$me = $this->getMe($session);
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
			$this->core->message($e->getMessage(), "Facebook Exception autologin()", 'red');
		}
		
		return false;
	}
}
?>