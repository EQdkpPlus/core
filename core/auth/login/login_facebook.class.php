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

class login_facebook extends gen_class {
	public static $shortcuts = array('config', 'tpl', 'html', 'user', 'db', 'in', 'pdh');
	public $fb = false;
	private $js_loaded = false;
	
	public function __construct(){
		
		$this->options = array(
			'connect_accounts'	=> true,	
		);
		
		$this->functions = array(
			'login_button'		=> 'login_button',
			'register_button' 	=> 'register_button',
			'account_button'	=> 'account_button',
			'get_account'		=> 'get_account',
			'pre_register'		=> 'pre_register',
			'after_register'	=> 'after_register',
		);
	}
	
	public function init_fb(){
		if (!is_object($this->fb)){
			//Init Facebook Api
			require_once($this->root_path.'libraries/facebook/facebook.php');

			$facebook = new Facebook(array(
				'appId'  => $this->config->get('login_fb_appid'),
				'secret' => $this->config->get('login_fb_appsecret'),
				'cookie' => true,
			));

			$this->fb = $facebook;
		}
	}
	
	public function init_js(){
		if (!$this->js_loaded){
			$this->init_fb();
			$this->tpl->staticHTML('<div id="fb-root"></div>');
			$this->tpl->add_js("
				window.fbAsyncInit = function() {
					FB.init({
					  appId   : '".$this->fb->getAppId()."',
					  status  : true, // check login status
					  cookie  : true, // enable cookies to allow the server to access the session
					  oauth	  : true,		  
					  xfbml   : true // parse XFBML
					});
				}
				
				  $(document).ready(function(){
					var e = document.createElement('script');
					e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
					e.async = true;
					$('body').append(e);	  
				  });				  
			");
			$this->js_loaded = true;
		}
	}
	
	public function get_me(){
		$this->init_fb();
		
		$user = $this->fb->getUser();
		if ($user){
			 try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $this->fb->api('/me');
			  } catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			  }
		}
		
		if ($user && $user_profile){
			return array('me' => $user_profile, 'uid' => $user);
		}
		return false;
	}
	
	public function settings(){
		$settings = array(
			'login_fb_appid'	=> array(
				'type'	=> 'text',
				'name'	=> 'login_fb_appid'
				),
			'login_fb_appsecret' => array(
				'type'	=> 'text',
				'name'	=> 'login_fb_appsecret'
				),		
		);
		return $settings;
	}
	
	public function login_button(){
		$this->init_js();
		$this->tpl->add_js("
			function facebook_login(){
				FB.login(function(response) {
				   if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					 window.location.href='".$this->root_path."login.php".$this->SID."&login=true&lmethod=facebook';
				   } else {
					 console.log('User cancelled login or did not fully authorize.');
				   }
				 });
			}
		");
		
		return '<input type="button" class="mainoption bi_facebook" onclick="facebook_login()" value="'.$this->user->lang('login_title').'" />';	
	}
	
	public function register_button(){
		$this->init_js();
		$this->tpl->add_js("		
			function facebook_register(){
				FB.login(function(response) {
				   if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					 window.location.href='".$this->root_path."register.php".$this->SID."&register=true&lmethod=facebook';
				   } else {
					 console.log('User cancelled login or did not fully authorize.');
				   }
				 }, {scope: 'email,user_birthday,user_location'});
			}	  
	  ");
	  		
		return '<input type="button" class="mainoption bi_facebook" onclick="facebook_register()" value="'.$this->user->lang('register_title').'" />';
	}
	
	public function account_button(){
		$this->init_fb();
		
		if ($this->get_me()){
			$me = $this->get_me();
			$uid = $me['uid'];
			$me = $me['me'];
			return $me['name'].' <input type="button" class="mainoption bi_facebook" value="'.$this->user->lang('auth_connect_account').'" onclick="window.location.href = \'settings.php'.$this->SID.'&mode=addauthacc&lmethod=facebook\';" />'.$this->html->TextField('auth_account', '', $uid, 'hidden');
		} else {
			$this->init_js();
			
			$this->tpl->add_js("		
			function facebook_connect_acc(){
				FB.login(function(response) {
				   if (response.authResponse) {
					 console.log('Welcome!  Fetching your information.... ');
					 window.location.href='".$this->root_path."settings.php".$this->SID."&mode=addauthacc&lmethod=facebook';
				   } else {
					 console.log('User cancelled login or did not fully authorize.');
				   }
				 });
			}	  
			");
			return '<input type="button" class="mainoption bi_facebook" onclick="facebook_connect_acc()" value="'.$this->user->lang('auth_connect_account').'" />';		
		}
	}
	
	public function get_account(){
		if ($this->get_me()){
			$me = $this->get_me();
			$uid = $me['uid'];
			return $uid;
		}
		return false;
	}
	
	public function pre_register(){
		$this->init_fb();
	  		
		if ($this->get_me()){
			$me = $this->get_me();
			$uid = $me['uid'];
			$me = $me['me'];
			
			switch($me['gender']){
				case 'male' : $gender = '1'; break;
				case 'female' : $gender = '2'; break;
				default: $gender = '0';
			}
			
			if ($me['locale']){
				list($locale1, $locale2) = explode('_', $me['locale']);
			}

			return array(
				'username'			=> $this->in->get('username', isset($me['name']) ? $me['name'] : ''),
				'user_email'		=> $this->in->get('user_email', isset($me['email']) ? $me['email'] : ''),
				'user_email2'		=> $this->in->get('user_email2', isset($me['email']) ? $me['email'] : ''),
				'first_name'		=> $this->in->get('first_name', isset($me['first_name']) ? $me['first_name'] : ''),
				'gender'			=> $this->in->get('gender', $gender),
				'country'			=> $this->in->get('country', $locale2),
				'user_lang'			=> $this->in->get('user_lang',	$this->config->get('default_lang')),
				'user_style'		=> $this->in->get('user_style', $this->config->get('default_style')),
				'user_timezone'		=> $this->in->get('user_timezone',	$this->config->get('timezone')),
				'user_password1'	=> $this->in->get('new_user_password1'),
				'user_password2'	=> $this->in->get('new_user_password2'),
				'auth_account'		=> $uid
			);
		}
		return false;
	}
	
	public function after_register(){
		if ($this->get_me()){
			$me = $this->get_me();
			$_uid = $me['uid'];
			$uid = '';
			$me = $me['me'];
			$out = false;
			if ($me['birthday']){
				list ($m, $d, $y) = explode('/', $me['birthday']);
				$out['birthday'] = $d.'.'.$m.'.'.$y;
			}
						
			if ($this->in->get('user_email') == $me['email']){
				$out['user_active'] = 1;
			}
	
			return $out;
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
		$blnLoginResult = false;
		
			
		if ($this->get_me() && $strPassword == ''){
			$me = $this->get_me();
			$uid = $me['uid'];
			$me = $me['me'];
			
			$userid = $this->pdh->get('user', 'userid_for_authaccount', array($uid));
			if ($userid){
				$userdata = $this->pdh->get('user', 'data', array($userid));
				if ($userdata){
					list($strPwdHash, $strSalt) = explode(':', $userdata['user_password']);
					return array(
						'status'		=> 1,
						'user_id'		=> $userdata['user_id'],
						'password_hash'	=> $strPwdHash,
						'autologin'		=> true,
					);
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
		$this->init_fb();
		if ($this->get_me()){
			$me = $this->get_me();
			$uid = $me['uid'];
			$me = $me['me'];
			redirect($this->fb->getLogoutUrl(), false, true);
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
		if ($this->get_me()){
			$me = $this->get_me();
			$uid = $me['uid'];
			$me = $me['me'];
			
			$userid = $this->pdh->get('user', 'userid_for_authaccount', array($uid));
			if ($userid){
				$userdata = $this->pdh->get('user', 'data', array($userid));
				return ($userdata) ? $userdata : false;
			}	
		}
		
		return false;
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_login_facebook', login_facebook::$shortcuts);
?>