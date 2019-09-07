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

class login_oauth extends gen_class {
	private $oauth_loaded = false;
	private $redirURL = "";
	
	private $appid, $appsecret, $scope, $name, $passToken, $userIDparam, $usernameparam, $useremailparam = false;
	
	private $AUTHORIZATION_ENDPOINT = '';
	private $TOKEN_ENDPOINT         = '';
	private $USER_ENDPOINT         = '';
	
	public static $functions = array(
			'login_button'		=> 'login_button',
			'account_button'	=> 'account_button',
			'get_account'		=> 'get_account',
			'register_button' 	=> 'register_button',
			'pre_register'		=> 'pre_register',
			'redirect'			=> 'redirect',
	);
	
	public static $options = array(
			'connect_accounts'	=> true,
	);
	
	public function __construct(){
		$this->redirURL = $this->env->buildLink().'index.php/auth-endpoint/?lmethod=oauth';
		
		$this->name						= $this->config->get('login_oauth_name');
		$this->AUTHORIZATION_ENDPOINT	= $this->config->get('login_oauth_authendpoint');
		$this->TOKEN_ENDPOINT 			= $this->config->get('login_oauth_tokenendpoint');
		$this->scope 					= $this->config->get('login_oauth_scope');
		$this->USER_ENDPOINT 			= $this->config->get('login_oauth_userendpoint');
		$this->passToken 				= $this->config->get('login_oauth_passtoken');
		$this->userIDparam 				= $this->config->get('login_oauth_useridparam');
		$this->usernameparam 			= $this->config->get('login_oauth_usernameparam');
		$this->useremailparam 			= $this->config->get('login_oauth_useremailparam');
	}
	
	public function settings(){
		$settings = array(
				'login_oauth_name' => array(
						'type'	=> 'text',
				),
				'login_oauth_clientid'	=> array(
						'type'	=> 'text',
				),
				'login_oauth_clientsecret' => array(
						'type'	=> 'password',
						'set_value' => true,
				),
				'login_oauth_scope' => array(
						'type'	=> 'text',
				),
				'login_oauth_authendpoint' => array(
						'type'	=> 'text',
				),
				'login_oauth_tokenendpoint' => array(
						'type'	=> 'text',
				),
				'login_oauth_userendpoint' => array(
						'type'	=> 'text',
				),
				'login_oauth_passtoken' => array(
						'type'	=> 'radio',
						'options' => array('bearer' => 'Bearer Header', 'param' => 'Param'),
				),
				'login_oauth_useridparam' => array(
						'type'	=> 'text',
						'default' => 'id',
				),
				'login_oauth_usernameparam' => array(
						'type'	=> 'text',
				),
				'login_oauth_useremailparam' => array(
						'type'	=> 'text',
				),
				
		);
		return $settings;
	}
		
	
	public function init_oauth(){
		if (!$this->oauth_loaded && !class_exists('OAuth2\\Client')){
			require($this->root_path.'libraries/oauth/Client.php');
			require($this->root_path.'libraries/oauth/GrantType/IGrantType.php');
			require($this->root_path.'libraries/oauth/GrantType/AuthorizationCode.php');
			$this->oauth_loaded = true;
		}
		
		$this->appid = $this->config->get('login_oauth_clientid');
		$this->appsecret = $this->config->get('login_oauth_clientsecret');
	}
	
	public function redirect($arrOptions=array()){
		$this->init_oauth();
		
		if(!strlen($this->appid) || !strlen($this->appsecret)){
			message_die('OAuth Client-ID or Client-Secret is missing. Please insert it into the fields at the EQdkp Plus settings, tab "User".');
		}
		
		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $this->redirURL, array('scope' => $this->scope));
		
		return $auth_url;
	}
	
	public function login_button(){
		$auth_url = $this->redirURL.'&status=login&link_hash='.$this->user->csrfGetToken('authendpoint_pageobjectlmethod');
		
		//Button color: #7289DA
		return '<button type="button" class="mainoption thirdpartylogin oauth loginbtn" onclick="window.location=\''.$auth_url.'\'">'.$this->name.'</button>';
	}
	
	
	public function account_button(){
		$auth_url = $this->redirURL.'&status=account&link_hash='.$this->user->csrfGetToken('authendpoint_pageobjectlmethod');
		
		return '<button type="button" class="mainoption thirdpartylogin oauth accountbtn" onclick="window.location=\''.$auth_url.'\'">'.$this->name.'</button>';
	}
	
	public function register_button(){
		$auth_url = $this->redirURL.'&status=register&link_hash='.$this->user->csrfGetToken('authendpoint_pageobjectlmethod');
		
		return '<button type="button" class="mainoption thirdpartylogin oauth registerbtn" onclick="window.location=\''.$auth_url.'\'">'.$this->name.'</button>';
	}
	
	public function pre_register(){
		$this->init_oauth();
		
		$blnLoginResult = false;
		
		if($this->in->exists('code')){
			
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			$params = array('code' => $this->in->get('code'), 'redirect_uri' => $this->redirURL, 'scope' => $this->scope);
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			
			if ($response && $response['result']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				
				if($arrAccountResult){
					$auth_account = $arrAccountResult[$this->userIDparam];
					
					$bla = array(
							'username'			=> isset($arrAccountResult[$this->usernameparam]) ? utf8_ucfirst($arrAccountResult[$this->usernameparam]) : '',
							'user_email'		=> isset($arrAccountResult[$this->useremailparam]) ? $arrAccountResult[$this->useremailparam] : '',
							'user_email2'		=> isset($arrAccountResult[$this->useremailparam]) ? $arrAccountResult[$this->useremailparam] : '',
							'auth_account'		=> $auth_account,
							'user_timezone'		=> $this->config->get('timezone'),
							'user_lang'			=> $this->user->lang_name,
							'avatar'			=> '',
					);
					
					$arrUserData = $this->user->registerUserFromAuthProvider($bla, 'oauth');
					if(isset($arrUserData['user_id'])){
						//Log the user in
						$auth_url = $this->controller_path_plain.'auth-endpoint/?lmethod=oauth&status=login&link_hash='.$this->user->csrfGetToken('authendpoint_pageobjectlmethod');
						redirect($auth_url);
					}
					
					return $arrUserData;
				}
			}
			
		}
		
		return false;
	}
	
	
	public function get_account(){
		$this->init_oauth();
		
		$code = $this->in->get('code');
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			$params = array('code' => $code, 'redirect_uri' => $this->redirURL, 'scope' => $this->scope);

			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result'] && $response['result']['access_token']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				
				if($arrAccountResult){
					//ID Param
					if(isset($arrAccountResult[$this->userIDparam])){
						return $arrAccountResult[$this->userIDparam];
					}
				}
			}
		}
		
		return false;
	}
	
	
	
	/**
	 * User-Login for facebook
	 *
	 * @param $strUsername
	 * @param $strPassword
	 * @param $boolUseHash Use Hash for comparing
	 * @return bool/array
	 */
	public function login($strUsername, $strPassword, $boolUseHash = false){
		$blnLoginResult = false;
		
		$this->init_oauth();
		
		$code = $_GET['code'];
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			$params = array('code' => $code, 'redirect_uri' => $this->redirURL, 'scope' => $this->scope);
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			
			if ($response && $response['result']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				if($arrAccountResult){
					if(isset($arrAccountResult[$this->userIDparam])){
						$userid = $this->pdh->get('user', 'userid_for_authaccount', array($arrAccountResult[$this->userIDparam], 'oauth'));
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
						} elseif((int)$this->config->get('cmsbridge_active') != 1 && (int)$this->config->get('login_fastregister')){
							//Try to register the user
							$auth_url = $this->controller_path_plain.'auth-endpoint/?lmethod=oauth&status=register&link_hash='.$this->user->csrfGetToken('authendpoint_pageobjectlmethod');
							
							redirect($auth_url);
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
	
	private function fetchUserData($strAccessToken){

		if($this->passToken == 'bearer'){
			$result = register('urlfetcher')->fetch($this->USER_ENDPOINT, array('Authorization: Bearer '.$strAccessToken));
		}elseif($this->passToken == 'param'){
			$url = "";
			
			if(strpos($this->USER_ENDPOINT, '?') === false){
				$url = $this->USER_ENDPOINT."?access_token=".rawurlencode($strAccessToken);
			} else {
				$url = $this->USER_ENDPOINT."&access_token=".rawurlencode($strAccessToken);
			}
			$url = str_replace('TOKEN', rawurlencode($strAccessToken), $url);
			$result = register('urlfetcher')->fetch($url);
			
		}

		
		if($result){
			$arrJSON = json_decode($result, true);
			if(!isset($arrJSON['error'])){
				return $arrJSON;
			} else {
				$this->core->message($arrJSON['errors'][0], 'Oauth Error', 'red');
			}
		}
		return false;
	}
	
}
?>