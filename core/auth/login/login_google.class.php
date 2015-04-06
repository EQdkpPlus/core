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

class login_google extends gen_class {
	
	private $publicKey = "";
	private $privateKey = "";
	private $authURL = "";
	private $callbackURLs = "";
	
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
		$this->publicKey = $this->config->get("login_google_publickey");
		$this->privateKey = $this->config->get("login_google_privatekey");
		$token = $this->user->csrfGetToken('google');
		$this->authURL = "https://accounts.google.com/o/oauth2/auth?client_id=".rawurlencode(trim($this->publicKey)). "&state=".$token."&scope=profile+email&response_type=code&redirect_uri=";
		$this->callbackURLs = array(
			'login' => $this->env->link.$this->controller_path_plain."Login/?login&lmethod=google",
			'register' => $this->env->link.$this->controller_path_plain."Register/?register&lmethod=google",
			'account' => $this->env->link.$this->controller_path_plain."Settings/?mode=addauthacc&lmethod=google",
		);
	}

	
	public function settings(){
		$settings = array(
				'login_google_publickey'	=> array(
					'type'	=> 'text',
				),
				'login_google_privatekey' => array(
					'type'	=> 'text',
				),
		);
		return $settings;
	}
	
	public function login_button(){
		return '<button type="button" class="mainoption thirdpartylogin google loginbtn" onclick="window.location=\''.$this->controller_path."Login/".$this->SID.'&login&lmethod=google&status=start\'"><i class="fa fa-google-plus fa-lg"></i> Google</button>';
	}
	
	public function register_button(){
		return '<button type="button" class="mainoption thirdpartylogin google registerbtn" onclick="window.location=\''.$this->controller_path."Register/".$this->SID.'&register&lmethod=google&status=start\'"><i class="fa fa-google-plus fa-lg"></i> Google</button>';		
	}
	
	public function account_button(){
		return '<button type="button" class="mainoption thirdpartylogin google accountbtn" onclick="window.location=\''.$this->controller_path."Settings/".$this->SID.'&mode=addauthacc&lmethod=google&status=start\'"><i class="fa fa-google-plus fa-lg"></i> Google</button>';
	}
	
	public function get_account(){
		$blnLoginResult = false;
		
		if(!$this->in->exists('code')){
			redirect($this->authURL.rawurlencode($this->callbackURLs['account']), false, true);
		} else {
			$accessToken = $this->fetchAccessToken($this->in->get('code'), $this->callbackURLs['account']);
			if($accessToken){
				$arrGoogleUser = $this->fetchUserData($accessToken);
				if($arrGoogleUser){
					$myGoogleID = $arrGoogleUser['id'];
					return $myGoogleID;
				}
			}			
		}
		
		return false;
	}
	
	public function pre_register(){
		$blnLoginResult = false;
		
		if(!$this->in->exists('code')){
			redirect($this->authURL.rawurlencode($this->callbackURLs['register']), false, true);
		} else {
			$accessToken = $this->fetchAccessToken($this->in->get('code'), $this->callbackURLs['register']);
			if($accessToken){
				$arrGoogleUser = $this->fetchUserData($accessToken);
			
				if($arrGoogleUser){
					$myGoogleID = $arrGoogleUser['id'];
						
					switch($arrGoogleUser['gender']){
						case 'male' : $gender = '1'; break;
						case 'female' : $gender = '2'; break;
						default: $gender = '0';
					}
					$bla = array(
							'username'			=> isset($arrGoogleUser['displayName']) ? $arrGoogleUser['displayName'] : '',
							'user_email'		=> isset($arrGoogleUser['emails'][0]) ? $arrGoogleUser['emails'][0]['value'] : '',
							'user_email2'		=> isset($arrGoogleUser['emails'][0]) ? $arrGoogleUser['emails'][0]['value'] : '',
							'first_name'		=> isset($arrGoogleUser['name']['givenName']) ? $arrGoogleUser['name']['givenName'] : '',
							'gender'			=> $gender,
							//'country'			=> isset($me['contact/country/home']) ? $me['contact/country/home'] : '',
							'auth_account'		=> $myGoogleID,
							'user_timezone'		=> $this->in->get('user_timezone', $this->config->get('timezone')),
					);
					
					return $bla;
				}

			}			
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
		
		if(!$this->in->exists('code')){
			redirect($this->authURL.rawurlencode($this->callbackURLs['login']), false, true);
		} else {
			$accessToken = $this->fetchAccessToken($this->in->get('code'), $this->callbackURLs['login']);
			if($accessToken){
				$arrGoogleUser = $this->fetchUserData($accessToken);
				
				if($arrGoogleUser){
					$myGoogleID = $arrGoogleUser['id'];
					
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($myGoogleID, 'google'));
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
	
	private function fetchAccessToken($strCode, $callbackURL){
		$settings = array(
				'code' => $strCode,
				'client_id' => trim($this->publicKey),
				'client_secret' => trim($this->privateKey),
				'redirect_uri' => $callbackURL,
				'grant_type' => 'authorization_code',
		);
		
		$result = register('urlfetcher')->post('https://accounts.google.com/o/oauth2/token', $settings, 'application/x-www-form-urlencoded');
		
		if($result){
			$arrJSON = json_decode($result, true);
			if(isset($arrJSON['access_token'])){
				return $arrJSON['access_token'];
			} elseif(isset($arrJSON['error'])){
				$this->core->message($arrJSON['error'], 'Google Error', 'red');
			}
		}
		return false;
	}
	
	private function fetchUserData($strAccessToken){
		$result = register('urlfetcher')->fetch('https://www.googleapis.com/plus/v1/people/me', array('Authorization: Bearer '.$strAccessToken));
		if($result){
			$arrJSON = json_decode($result, true);

			if(!isset($arrJSON['error'])){
				return $arrJSON;
			} else {
				$this->core->message($arrJSON['errors'][0], 'Google Error', 'red');
			}
		}
		d($arrJSON);
		return false;
	}
}
?>