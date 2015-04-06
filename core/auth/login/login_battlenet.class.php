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

class login_battlenet extends gen_class {
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
			'login_bnet_appid'	=> array(
				'type'	=> 'text',
			),
			'login_bnet_appsecret' => array(
				'type'	=> 'text',
			),
			'login_bnet_eqdkp_relay' => array(
				'type'	=> 'radio',
				'dependency' => array(1=>array('login_bnet_eqdkp_appid', 'login_bnet_eqdkp_appsecret')),
			),
			'login_bnet_eqdkp_appid'	=> array(
				'type'	=> 'text',
			),
			'login_bnet_eqdkp_appsecret' => array(
				'type'	=> 'text',
			),
		);
		return $settings;
	}
	
	private $appid, $appsecret, $relay, $eqdkp_appid, $eqdkp_appsecret = false;
	
	private $AUTHORIZATION_ENDPOINT = 'https://eu.battle.net/oauth/authorize';
	private $TOKEN_ENDPOINT         = 'https://eu.battle.net/oauth/token';
	
	private $RELAY_URL = 'https://eqdkp-plus.eu/repository/relay.php';
	
	public function init_oauth(){
		if (!$this->oauth_loaded){
			require($this->root_path.'libraries/oauth/Client.php');
			require($this->root_path.'libraries/oauth/GrantType/IGrantType.php');
			require($this->root_path.'libraries/oauth/GrantType/AuthorizationCode.php');
			
			$this->appid = $this->config->get('login_bnet_appid');
			$this->appsecret = $this->config->get('login_bnet_appsecret');
			$this->relay = ((int)$this->config->get('login_bnet_eqdkp_relay')) ? true : false;
			$this->eqdkp_appid = $this->config->get('login_bnet_eqdkp_appid');
			$this->eqdkp_appsecret = $this->config->get('login_bnet_eqdkp_appsecret');
			
			$this->oauth_loaded = true;
		
		}
	}
	
	public function login_button(){
		$this->init_oauth();
		
		if ($this->relay){
			$time = time();
			$hmac = hash('sha1', $time.'_'.$this->eqdkp_appid.'_'.$this->eqdkp_appsecret);
			$redir_url = $this->RELAY_URL.'?_t='.$time.'&_id='.$this->eqdkp_appid.'&_hmac='.$hmac;
		} else {
			$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=battlenet&norelay=1';
		}
		
		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'wow.profile'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin battlenet loginbtn" onclick="window.location=\''.$auth_url.'\'"><i class="bi_battlenet"></i> Battle.net</button>';
	}
	
	
	public function account_button(){
		$this->init_oauth();
		
		if ($this->relay){
			$time = time();
			$hmac = hash('sha1', $time.'_'.$this->eqdkp_appid.'_'.$this->eqdkp_appsecret);
			$redir_url = $this->RELAY_URL.'?_t='.$time.'&_id='.$this->eqdkp_appid.'&_hmac='.$hmac.'&_s=ac';
	
		} else {
			$redir_url = $this->env->buildLink().'index.php/Settings/?mode=addauthacc&lmethod=battlenet&norelay=1';
		}

		$client = new OAuth2\Client($this->appid, $this->appsecret);
		$auth_url = $client->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $redir_url, array('scope' => 'wow.profile'));
		
		
		return '<button type="button" class="mainoption thirdpartylogin battlenet accountbtn" onclick="window.location=\''.$auth_url.'\'"><i class="bi_battlenet"></i> Battle.net</button>';		
	}
	
	public function get_account(){
		$this->init_oauth();
		
		if ($this->in->get('norelay', 0)){
			$code = $this->in->get('code');
		} else {
			$encrypt = register('encrypt', array($this->eqdkp_appsecret));
			$code = $encrypt->decrypt(rawurldecode($_GET['code']));
		}
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
			
			if ($this->relay){
				$time = $this->in->get('t');
				$hmac = $this->in->get('h');
				$s = ($this->in->get('_s')) ? '&_s='.$this->in->get('_s') : '';
				$redir_url = $this->RELAY_URL.'?_t='.$time.'&_id='.$this->eqdkp_appid.'&_hmac='.$hmac.$s;
			} else {
				$redir_url = $this->env->buildLink().'index.php/Settings/?mode=addauthacc&lmethod=battlenet&norelay=1';
			}
			
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'wow.profile');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			if ($response && $response['result']){
				if (isset($response['result']['accountId'])){
					return $response['result']['accountId'];	
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
		
		if ($this->in->get('norelay', 0)){
			$code = $_GET['code'];
		} else {
			$encrypt = register('encrypt', array($this->eqdkp_appsecret));
			$code = $encrypt->decrypt(rawurldecode($_GET['code']));
		}
		
		if ($code){
			$client = new OAuth2\Client($this->appid, $this->appsecret);
				
			if ($this->relay){
				$time = $this->in->get('t');
				$hmac = $this->in->get('h');
				$s = ($this->in->get('_s')) ? '&_s='.$this->in->get('_s') : '';
				$redir_url = $this->RELAY_URL.'?_t='.$time.'&_id='.$this->eqdkp_appid.'&_hmac='.$hmac.$s;
			} else {
				$redir_url = $this->env->buildLink().'index.php/Login/?login&lmethod=battlenet&norelay=1';
			}
				
			$params = array('code' => $code, 'redirect_uri' => $redir_url, 'scope' => 'wow.profile');
			$response = $client->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
			if ($response && $response['result']){
				if (isset($response['result']['accountId'])){
					$userid = $this->pdh->get('user', 'userid_for_authaccount', array($response['result']['accountId'], 'battlenet'));
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
}
?>