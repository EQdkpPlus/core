<?php
 use Facebook\FacebookSession;
/*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2014-06-10 21:36:25 +0200 (Di, 10 Jun 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14347 $
 * 
 * $Id: login_facebook.class.php 14347 2014-06-10 19:36:25Z godmod $
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
		
		
		return '<button type="button" class="mainoption" onclick="window.location=\''.$auth_url.'\'"><img src="'.$this->server_path.'images/global/bnet.png" class="absmiddle" /> Battle.net '.$this->user->lang('login_title').'</button>';
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
		
		
		return '<button type="button" class="mainoption" onclick="window.location=\''.$auth_url.'\'"><img src="'.$this->server_path.'images/global/bnet.png" class="absmiddle" /> '.$this->user->lang('auth_connect_account').'</button>';		
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