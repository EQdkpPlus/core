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

class vbulletin51_bridge extends bridge_generic {
	
	public static $name = 'vBulletin 5.1';
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'usergroup', //without prefix
			'id'	=> 'usergroupid',
			'name'	=> 'title',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'FUNCTION'	=> 'vb_get_user_groups',
		),
		'user'	=> array( //User
			'table'	=> 'user',
			'id'	=> 'userid',
			'name'	=> 'username',
			'where'	=> 'username',
			'password' => 'token',
			'email'	=> 'email',
			'salt'	=> 'secret',
			'QUERY'	=> '',
		),
	);
	
	public $settings = array(
			'cmsbridge_disable_sso'	=> array(
					'type'	=> 'radio',
			),
			'cmsbridge_sso_cookieprefix' => array(
					'type'	=> 'text',
			),
			'cmsbridge_sso_cookiesalt' => array(
					'type'	=> 'text',
			),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		if($arrUserdata['scheme'] == 'legacy'){
			list($storedHash, $storedSalt) = explode(' ', $hash);
			return ($storedHash == md5(md5($password) . $storedSalt));
			
		} elseif(strpos($arrUserdata['scheme'], 'blowfish') !== false){
			return (crypt(md5($password), $hash) == $hash);
		}

		return false;
	}
	
	public function vb_get_user_groups($intUserID){
		$query = $this->bridgedb->prepare("SELECT usergroupid, membergroupids FROM ".$this->prefix."user WHERE userid=?")->execute($intUserID);
		$arrReturn = array();
		if ($query){
			$result = $query->fetchAssoc();
			
			$arrReturn[] = (int)$result['usergroupid'];
			
			$arrAditionalGroups = explode(',', $result['membergroupids']);
			if (is_array($arrAditionalGroups)){
				foreach ($arrAditionalGroups as $group){
					$arrReturn[] = (int)$group;
				}
			}
		}
		
		return $arrReturn;
	}
	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		//Is user active?
		if ($boolLoginResult){
			//Single Sign On
			if ($this->config->get('cmsbridge_disable_sso') != '1'){
				$this->sso($arrUserdata, $boolSetAutoLogin);
			}
				
			return true;
		}
	
		return false;
	}
	
	private function sso($arrUserdata, $boolAutoLogin){
		$user_id = intval($arrUserdata['id']);
		$strSessionID = substr(md5(generateRandomBytes(55)).md5(generateRandomBytes(55)), 0, 32);
		//$this->bridgedb->prepare("DELETE FROM ".$this->prefix."session WHERE userID=?")->execute($user_id);
		
		$config = array();
		$objQuery =  $this->bridgedb->query("SELECT data FROM ".$this->prefix."datastore WHERE title = 'options'");
		if($objQuery){
			$result = $objQuery->fetchAssoc();
			$config = unserialize($result['data']);
		}	
		
		//PW is true, logg the user into our Forum
		$arrSet = array(
				'sessionhash'				=> $strSessionID,
				'userid'					=> (int) $user_id,
				'host'						=> $this->env->ip,
				'idhash'					=> md5($this->env->useragent.implode('.', array_slice(explode('.', $this->fetchAltIp()), 0, 4 - $config['ipcheck']))),
				'useragent'					=> $this->env->useragent,
				'loggedin'					=> 1,
				'lastactivity'				=> time(),
		);
		$this->bridgedb->prepare("INSERT INTO ".$this->prefix."session :p")->set($arrSet)->execute();
			

			
		$expire = $this->time->time + 31536000;
		
		$strCookiedomain = $config['cookiedomain'];
		$strCookiepath = $config['cookiepath'];
		$strCookieprefix = $this->config->get('cmsbridge_sso_cookieprefix');
	
		//SID Cookie
		setcookie($strCookieprefix.'sessionhash', $strSessionID, $expire, $strCookiepath, $strCookiedomain, $this->env->ssl);
		setcookie($strCookieprefix.'userid', (int) $user_id, $expire, $strCookiepath, $strCookiedomain, $this->env->ssl);
		if ($boolAutoLogin && strlen($this->config->get('cmsbridge_sso_cookiesalt'))) setcookie(hash("sha224", $arrUserdata['token'].$this->config->get('cmsbridge_sso_cookiesalt')), $strCookieprefix.'password', $arrUserdata['password'], $expire, $strCookiepath, $strCookiedomain, $this->env->ssl);
		return true;
	}
	
	public function autologin($arrCookieData){
		$config = array();
		$objQuery =  $this->bridgedb->query("SELECT data FROM ".$this->prefix."datastore WHERE title = 'options'");
		if($objQuery){
			$result = $objQuery->fetchAssoc();
			$config = unserialize($result['data']);
		}
			
		$expire = $this->time->time + 31536000;
		
		$strCookiedomain = $config['cookiedomain'];
		$strCookiepath = $config['cookiepath'];
		$strCookieprefix = $this->config->get('cmsbridge_sso_cookieprefix');
	
		$userID = $_COOKIE[$strCookieprefix.'userid'];
		$cookieHash = $_COOKIE[$strCookieprefix.'sessionhash'];
	
		if ($cookieHash == NULL || $cookieHash == "") return false;
	
		$result = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."session WHERE userid = ? and sessionhash=?")->execute($userID, $cookieHash);
		if ($result){
			$row = $result->fetchRow();
			if ($row){
				if ($row['host'] == $this->env->ip && $row['useragent'] == $this->env->useragent){
					$result2 = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."user WHERE userid=?")->execute($userID);
					if ($result2){
						$row2 = $result2->fetchRow();
						if($row2){
							$strUsername = utf8_strtolower($row2['username']);
							$user_id = $this->pdh->get('user', 'userid', array($strUsername));
							$data = $this->pdh->get('user', 'data', array($user_id));
							return $data;
						}
					}
				}
			}
		}
	
		return false;
	}
	
	public function logout(){
		$arrUserdata = $this->bridge->get_userdata($this->user->data['username']);
		if (isset($arrUserdata['id'])){
			$this->bridgedb->prepare("DELETE FROM ".$this->prefix."session WHERE userid=?")->execute($arrUserdata['id']);
		}
	
		$config = array();
		$objQuery =  $this->bridgedb->query("SELECT data FROM ".$this->prefix."datastore WHERE title = 'options'");
		if($objQuery){
			$result = $objQuery->fetchAssoc();
			$config = unserialize($result['data']);
		}
			
		$expire = $this->time->time + 31536000;
		
		$strCookiedomain = $config['cookiedomain'];
		$strCookiepath = $config['cookiepath'];
		$strCookieprefix = $this->config->get('cmsbridge_sso_cookieprefix');

		setcookie($strCookieprefix.'sessionhash', '', 0, $strCookiepath, $strCookiedomain, $this->env->ssl);
		setcookie($strCookieprefix.'userid', '', 0, $strCookiepath, $strCookiedomain, $this->env->ssl);
		setcookie($strCookieprefix.'password', '', 0, $strCookiepath, $strCookiedomain, $this->env->ssl);
	}
	
	protected function fetchAltIp()
	{
		//These are set from the web page bot not from CLI
		$alt_ip = '';
		if (isset($_SERVER['REMOTE_ADDR']))
		{
			$alt_ip = $_SERVER['REMOTE_ADDR'];
		}
	
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
		{
			// try to avoid using an internal IP address, its probably a proxy
			$ranges = array(
					'10.0.0.0/8' => array(ip2long('10.0.0.0'), ip2long('10.255.255.255')),
					'127.0.0.0/8' => array(ip2long('127.0.0.0'), ip2long('127.255.255.255')),
					'169.254.0.0/16' => array(ip2long('169.254.0.0'), ip2long('169.254.255.255')),
					'172.16.0.0/12' => array(ip2long('172.16.0.0'), ip2long('172.31.255.255')),
					'192.168.0.0/16' => array(ip2long('192.168.0.0'), ip2long('192.168.255.255')),
			);
			foreach ($matches[0] AS $ip)
			{
				$ip_long = ip2long($ip);
				if ($ip_long === false)
				{
					continue;
				}
	
				$private_ip = false;
				foreach ($ranges AS $range)
				{
					if ($ip_long >= $range[0] AND $ip_long <= $range[1])
					{
						$private_ip = true;
						break;
					}
				}
	
				if (!$private_ip)
				{
					$alt_ip = $ip;
					break;
				}
			}
		}
		else if (isset($_SERVER['HTTP_FROM']))
		{
			$alt_ip = $_SERVER['HTTP_FROM'];
		}
	
		return $alt_ip;
	}
}
?>