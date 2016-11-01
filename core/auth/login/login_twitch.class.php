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

class login_twitch extends gen_class {
	private $oauth_loaded = false;
	
	public static $functions = array(
		'login_button'		=> 'login_button',
		'account_button'	=> 'account_button',
		'get_account'		=> 'get_account',
	);
	
	public static $options = array(
		'connect_accounts'	=> true,	
	);
	
	public function __construct(){
	}
	
	public function settings(){
		$settings = array(
			'login_twitch_appid'	=> array(
				'type'	=> 'text',
			),
			'login_twitch_appsecret' => array(
				'type'	=> 'text',
			),
		);
		return $settings;
	}
	
	private $appid, $appsecret = false;
	
	private $AUTHORIZATION_ENDPOINT = 'https://api.twitch.tv/kraken/oauth2/authorize';
	private $TOKEN_ENDPOINT         = 'https://api.twitch.tv/kraken/oauth2/token';

	
	public function init_oauth(){
		if (!$this->oauth_loaded && !class_exists('OAuth2\\Client')){
			require($this->root_path.'libraries/oauth/Client.php');
			require($this->root_path.'libraries/oauth/GrantType/IGrantType.php');
			require($this->root_path.'libraries/oauth/GrantType/AuthorizationCode.php');
			$this->oauth_loaded = true;
		}
		
		$this->appid = $this->config->get('login_twitch_appid');
		$this->appsecret = $this->config->get('login_twitch_appsecret');
	}
	
	public function login_button(){
		$this->init_oauth();
		
		$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=twitch';
		
		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'user_read'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin twitch loginbtn" onclick="window.location=\''.$auth_url.'\'"><i class="fa fa-twitch fa-lg"></i> Twitch</button>';
	}
	
	
	public function account_button(){
		$this->init_oauth();
		
		$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=twitch';

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'user_read'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin twitch accountbtn" onclick="window.location=\''.$auth_url.'\'"><i class="fa fa-twitch fa-lg"></i> Twitch</button>';		
	}
	
	public function get_account(){
		$this->init_oauth();
		
		$code = $this->in->get('code');
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			$redir_url =  $this->env->buildLink().'index.php/Login/?login&lmethod=twitch';
			
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'user_read');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result'] && $response['result']['access_token']){
				
				$accountResponse = register('urlfetcher')->fetch("https://api.twitch.tv/kraken/user?oauth_token=".$response['result']['access_token']);
				if($accountResponse){
					
					$arrAccountResult = json_decode($accountResponse, true);
					if(isset($arrAccountResult['_id'])){
						return $arrAccountResult['_id'];
					}
				}
			}
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
		
		$this->init_oauth();
		
		$code = $_GET['code'];
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
				
			$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=twitch';
				
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'user_read');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result']){
				
				$accountResponse = register('urlfetcher')->fetch("https://api.twitch.tv/kraken/user?oauth_token=".$response['result']['access_token']);
				if($accountResponse){
					$arrAccountResult = json_decode($accountResponse, true);
					if(isset($arrAccountResult['_id'])){
						$userid = $this->pdh->get('user', 'userid_for_authaccount', array($arrAccountResult['_id'], 'twitch'));
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