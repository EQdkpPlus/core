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

class login_discord extends gen_class {
	private $oauth_loaded = false;

	public static $functions = array(
			'login_button'		=> 'login_button',
			'account_button'	=> 'account_button',
			'get_account'		=> 'get_account',
			'register_button' 	=> 'register_button',
			'pre_register'		=> 'pre_register',
	);

	public static $options = array(
			'connect_accounts'	=> true,
	);

	public function __construct(){
	}

	public function settings(){
		$settings = array(
				'login_discord_appid'	=> array(
						'type'	=> 'text',
				),
				'login_discord_appsecret' => array(
						'type'	=> 'password',
						'set_value' => true,
				),
		);
		return $settings;
	}

	private $appid, $appsecret = false;

	private $AUTHORIZATION_ENDPOINT = 'https://discordapp.com/api/oauth2/authorize';
	private $TOKEN_ENDPOINT         = 'https://discordapp.com/api/oauth2/token';


	public function init_oauth(){
		if (!$this->oauth_loaded && !class_exists('OAuth2\\Client')){
			require($this->root_path.'libraries/oauth/Client.php');
			require($this->root_path.'libraries/oauth/GrantType/IGrantType.php');
			require($this->root_path.'libraries/oauth/GrantType/AuthorizationCode.php');
			$this->oauth_loaded = true;
		}

		$this->appid = $this->config->get('login_discord_appid');
		$this->appsecret = $this->config->get('login_discord_appsecret');
	}

	public function login_button(){
		$this->init_oauth();

		$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=discord';

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'identify'));

		//Button color: #7289DA
		return '<button type="button" class="mainoption thirdpartylogin discord loginbtn" onclick="window.location=\''.$auth_url.'\'">Discord</button>';
	}


	public function account_button(){
		$this->init_oauth();

		$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=discord';

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'identify'));


		return '<button type="button" class="mainoption thirdpartylogin discord accountbtn" onclick="window.location=\''.$auth_url.'\'">Discord</button>';
	}
	
	public function register_button(){
		$this->init_oauth();
		
		$redir_url = $this->env->buildLink().'index.php/Register/?register&lmethod=discord';
		
		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'email'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin discord accountbtn" onclick="window.location=\''.$auth_url.'\'">Discord</button>';
	}
	
	public function pre_register(){
		$this->init_oauth();
		
		$blnLoginResult = false;
		
		if($this->in->exists('code')){
			$redir_url = $this->env->buildLink().'index.php/Register/?register&lmethod=discord';
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			$params = array('code' => $this->in->get('code'), 'redirect_uri' => $redir_url, 'scope' => 'email');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			
			if ($response && $response['result']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				
				if($arrAccountResult){
					$auth_account = $arrAccountResult['id'];
					
					$bla = array(
							'username'			=> isset($arrAccountResult['username']) ? utf8_ucfirst($arrAccountResult['username']) : '',
							'user_email'		=> isset($arrAccountResult['email']) ? $arrAccountResult['email'] : '',
							'user_email2'		=> isset($arrAccountResult['email']) ? $arrAccountResult['email'] : '',
							'auth_account'		=> $arrAccountResult['id'],
							'user_timezone'		=> $this->config->get('timezone'),
							'user_lang'			=> $this->user->lang_name,
					);
					
					//Admin activation
					if ($this->config->get('account_activation') == 2){
						return $bla;
					}
					
					//Check Auth Account
					if (!$this->pdh->get('user', 'check_auth_account', array($auth_account, 'discord'))){
						return $bla;
					}
					
					//Check Email address
					if($this->pdh->get('user', 'check_email', array($bla['user_email'])) == 'false'){
						return $bla;
					}
					
					//Create Username
					$strUsername = ($bla['username'] != "") ? $bla['username'] : 'DiscordUser'.rand(100, 999);
					
					//Check Username and create a new one
					if ($this->pdh->get('user', 'check_username', array($strUsername)) == 'false'){
						$strUsername = $strUsername.rand(100, 999);
					}
					if ($this->pdh->get('user', 'check_username', array($strUsername)) == 'false'){
						return $bla;
					}
					
					//Register User (random credentials)
					$salt = $this->user->generate_salt();
					$strPwdHash = $this->user->encrypt_password(random_string(false, 16), $salt);

					$intUserID = $this->pdh->put('user', 'insert_user_bridge', array(
							$strUsername, $strPwdHash, $bla['user_email']
					));
					
					//Add the auth account
					$this->pdh->put('user', 'add_authaccount', array($intUserID, $auth_account, 'discord'));

					
					//Send Email with username
					$email_template		= 'register_activation_none';
					$email_subject		= $this->user->lang('email_subject_activation_none');
					
					$objMailer = register('MyMailer');
					
					$objMailer->Set_Language($this->user->lang_name);

					$bodyvars = array(
							'USERNAME'		=> stripslashes($strUsername),
							'GUILDTAG'		=> $this->config->get('guildtag'),
					);
					
					if(!$objMailer->SendMailFromAdmin($bla['user_email'], $email_subject, $email_template.'.html', $bodyvars)){
						$success_message = $this->user->lang('email_subject_send_error');
					}
					
					//Log the user in
					$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=discord';
					
					$client = new OAuth2\Client($this->appid, $this->appsecret);
					$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'identify'));
					redirect($auth_url, false, true);
					
					return $bla;
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
				
			$redir_url =  $this->env->buildLink().'index.php/Login/?login&lmethod=discord';
				
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'identify');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result'] && $response['result']['access_token']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				if($arrAccountResult){
					if(isset($arrAccountResult['id'])){
						return $arrAccountResult['id'];
					}
				}
			}
		}

		return false;
	}



	/**
	 * User-Login for Discord
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

			$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=discord';

			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'identify');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);

			if ($response && $response['result']){
				$arrAccountResult = $this->fetchUserData($response['result']['access_token']);
				if($arrAccountResult){
					if(isset($arrAccountResult['id'])){
						$userid = $this->pdh->get('user', 'userid_for_authaccount', array($arrAccountResult['id'], 'discord'));
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
						} else {
							$redir_url = $this->env->buildLink().'index.php/Register/?register&lmethod=discord';
							
							$client = new OAuth2\Client($this->appid, $this->appsecret);
							$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'email'));
							
							redirect($auth_url, false, true);
							
							return '<button type="button" class="mainoption thirdpartylogin discord accountbtn" onclick="window.location=\''.$auth_url.'\'">Discord</button>';
							
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
		$result = register('urlfetcher')->fetch('https://discordapp.com/api/users/@me', array('Authorization: Bearer '.$strAccessToken));
		if($result){
			$arrJSON = json_decode($result, true);
			if(!isset($arrJSON['error'])){
				return $arrJSON;
			} else {
				$this->core->message($arrJSON['errors'][0], 'Discord Error', 'red');
			}
		}
		return false;
	}

}
?>