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

class wbb41_bridge extends bridge_generic {
	
	public static $name = 'WBB 4.1';
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'user_group', //without prefix
			'id'	=> 'groupID',
			'name'	=> 'groupName',
			'QUERY'	=> 'SELECT g.groupID as id, l.languageItemValue as name FROM ___user_group g, ___language_item l WHERE l.languageItem = g.groupName',
			'FUNCTION' => 'get_groups'
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'table'	=> 'user_to_group',
			'group'	=> 'groupID',
			'user'	=> 'userID',
			'QUERY'	=> '',
		),
		'user'	=> array( //User
			'table'	=> 'user',
			'id'	=> 'userID',
			'name'	=> 'username',
			'where'	=> 'username',
			'password' => 'password',
			'email'	=> 'email',
			'salt'	=> '',
			'QUERY'	=> '',
		),
	);
	
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'type'	=> 'radio',
		),
		'cmsbridge_sso_cookiedomain' => array(
			'type'	=> 'text',
		),
		'cmsbridge_sso_cookiepath' => array(
			'type'	=> 'text',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		$blnResult = $this->__checkPassword($strUsername, $password, $hash);

		if ($blnResult) return true;

		return false;
	}

	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['banned'] != '0' || $arrUserdata['activationCode'] != '0') {
				return false;
			}
			
			//Single Sign On
			if ($this->config->get('cmsbridge_disable_sso') != '1'){
				$this->sso($arrUserdata, $boolSetAutoLogin);
			}
			
			return true;
		}
		
		return false;
	}
	
	public function get_groups($blnWithID){
		$strQuery = "SELECT g.groupID as id, groupName as name, l.languageItemValue as lang FROM ".$this->prefix."user_group g LEFT JOIN ".$this->prefix."language_item l ON l.languageItem = g.groupName";
		$objQuery = $this->bridgedb->query($strQuery);
		$groups = false;
		
		if ($objQuery){
			$arrResult = $objQuery->fetchAllAssoc();
			$groups = false;
			
			if (is_array($arrResult) && count($arrResult) > 0) {
				foreach ($arrResult as $row){
					$name = (strpos($row['name'], 'wcf.acp.group') === 0 && $row['lang'] != "") ? $row['lang'] : $row['name'];
					$groups[$row['id']] = $name.(($blnWithID) ? ' (#'.$row['id'].')': '');
				}
			}
			
			return $groups;
		}

		return false;
	}
	
	private function sso($arrUserdata, $boolAutoLogin){
		$user_id = intval($arrUserdata['id']);
		$strSessionID = substr(md5(generateRandomBytes(55)).md5(generateRandomBytes(55)), 0, 40);
		//$this->bridgedb->prepare("DELETE FROM ".$this->prefix."session WHERE userID=?")->execute($user_id);
		
		//PW is true, logg the user into our Forum
		$arrSet = array(
			'sessionID'					=> $strSessionID,
			'userID'					=> (int) $user_id,
			'ipAddress'					=> self::getIpAddress(),
			'userAgent'					=> $this->env->useragent,
			'lastActivityTime'			=> (int) $this->time->time,
			'requestURI'				=> '',
			'requestMethod'				=> 'GET',
			'sessionVariables'			=> 'a:1:{s:16:"__SECURITY_TOKEN";s:40:".'.md5(generateRandomBytes()).'a7w8er45'.'.";}',
		);
		
		$objQuery = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."session WHERE userID =?")->execute((int) $user_id);
		if($objQuery){
			$intResult = $objQuery->numRows;
			$arrResult = $objQuery->fetchAssoc();
			if($intResult > 0){
				$strSessionID = $arrResult['sessionID'];
				
				//Check if there is an existing virtual Session
				$objQuery = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."session_virtual WHERE sessionID=? AND ipAddress=? AND userAgent=?")->execute($arrResult['sessionID'], self::getIpAddress(), $this->env->useragent);
				if($objQuery){
					$intResult = $objQuery->numRows;
					if($intResult > 0){
						//There is a existing virtual Session, use this Session ID
						$this->bridgedb->prepare("UPDATE ".$this->prefix."session_virtual :p WHERE sessionID=? AND ipAddress=? AND userAgent=?")->set(array('lastActivityTime' => time()))->execute($arrResult['sessionID'], self::getIpAddress(), $this->env->useragent);
					} else {
						//Create a new virtual Session for this IP and Useragent
						$arrVirtualSet = array(
							'sessionID'			=> $strSessionID,
							'ipAddress'			=> self::getIpAddress(),
							'userAgent'			=> $this->env->useragent,
							'lastActivityTime'	=> (int) $this->time->time,
						);
						
						//Is there an existing virtual Session?
						$this->bridgedb->prepare("INSERT INTO __session_virtual :p")->set($arrVirtualSet)->execute((int) $this->time->time);
					}
				}
				
			} else {
				$this->bridgedb->prepare("INSERT INTO ".$this->prefix."session :p")->set($arrSet)->execute();

				$arrVirtualSet = array(
					'sessionID'			=> $strSessionID,
					'ipAddress'			=> self::getIpAddress(),
					'userAgent'			=> $this->env->useragent,
					'lastActivityTime'	=> (int) $this->time->time,
				);
				$this->bridgedb->prepare("INSERT INTO ".$this->prefix."session_virtual :p")->set($arrVirtualSet)->execute();
			}
		}

		$config = array();
		$objQuery =  $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix'");
		if($objQuery){
			$result = $objQuery->fetchAllAssoc();
			if (is_array($result)){
				foreach ($result as $value){
					$config[$value['optionName']] = $value['optionValue'];
				}
			}		
		}
			
		$expire = $this->time->time + 31536000;
		$config['cookie_domain'] = "";
		if($this->config->get('cmsbridge_sso_cookiedomain') == '') {
			$arrDomains = explode('.', $this->env->server_name);
			$arrDomainsReversed = array_reverse($arrDomains);
			if (count($arrDomainsReversed) > 1){
				$config['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
			} else {
				$config['cookie_domain'] = $this->env->server_name;
			}
		} else $config['cookie_domain'] = $this->config->get('cmsbridge_sso_cookiedomain');
		
		$config['cookie_path'] = (strlen($this->config->get('cmsbridge_sso_cookiepath'))) ? $this->config->get('cmsbridge_sso_cookiepath') : '/';

		//SID Cookie
		setcookie($config['cookie_prefix'].'cookieHash', $strSessionID, $expire, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
		setcookie($config['cookie_prefix'].'userID', (int) $user_id, $expire, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
		if ($boolAutoLogin) setcookie($config['cookie_prefix'].'password', $arrUserdata['password'], $expire, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
		return true;
	}
	
	public function autologin($arrCookieData){
	$config = array();
		$objQuery =  $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix'");
		if($objQuery){
			$result = $objQuery->fetchAllAssoc();
			if (is_array($result)){
				foreach ($result as $value){
					$config[$value['optionName']] = $value['optionValue'];
				}
			}		
		}
		
		$userID = $_COOKIE[$config['cookie_prefix'].'userID'];
		$cookieHash = $_COOKIE[$config['cookie_prefix'].'cookieHash'];
		
		if ($cookieHash == NULL || $cookieHash == "") return false;
		
		$result = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."session WHERE userID = ? and sessionID=?")->execute($userID, $cookieHash);
		if ($result){
			$row = $result->fetchRow();
			if ($row){
				if ($row['ipAddress'] == self::getIpAddress() && $row['userAgent'] == $this->env->useragent){
					$result2 = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."user WHERE userID=?")->execute($userID);
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
			$this->bridgedb->prepare("DELETE FROM ".$this->prefix."session WHERE userID=?")->execute($arrUserdata['id']);
		}
		
		$config = array();
		$objQuery =  $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix'");
		if($objQuery){
			$result = $objQuery->fetchAllAssoc();
			if (is_array($result)){
				foreach ($result as $value){
					$config[$value['optionName']] = $value['optionValue'];
				}
			}		
		}
		
		if($this->config->get('cmsbridge_sso_cookiedomain') == '') {
			$arrDomains = explode('.', $this->env->server_name);
			$arrDomainsReversed = array_reverse($arrDomains);
			if (count($arrDomainsReversed) > 1){
				$config['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
			} else {
				$config['cookie_domain'] = $this->env->server_name;
			}
		} else $config['cookie_domain'] = $this->config->get('cmsbridge_sso_cookiedomain');
		
		$config['cookie_path'] = (strlen($this->config->get('cmsbridge_sso_cookiepath'))) ? $this->config->get('cmsbridge_sso_cookiepath') : '/';
		
		setcookie($config['cookie_prefix'].'cookieHash', '', 0, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
		setcookie($config['cookie_prefix'].'userID', '', 0, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
		setcookie($config['cookie_prefix'].'password', '', 0, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
	}
	
	private function __checkPassword($username, $password, $hash) {
		$isValid = false;
		
		// check if password is a valid bcrypt hash
		if (self::isBlowfish($hash)) {
			
			// password is correct
			if (self::secureCompare($hash, self::getDoubleSaltedHash($password, $hash))) {
				$isValid = true;
			}
		}
		else {
			// different encryption type
			if (self::checkPassword($username, $password, $hash)) {
				$isValid = true;
			}
		}
				
		return $isValid;
	}
	
	/**
	 * Returns the ipv6 address of the client.
	 * 
	 * @return	string
	 */
	private static function getIpAddress() {
		$REMOTE_ADDR = '';
		if (isset($_SERVER['REMOTE_ADDR'])) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		
		// darwin fix
		if ($REMOTE_ADDR == '::1' || $REMOTE_ADDR == 'fe80::1') {
			$REMOTE_ADDR = '127.0.0.1';
		}
		
		$REMOTE_ADDR = self::convertIPv4To6($REMOTE_ADDR);
		
		return $REMOTE_ADDR;
	}
	
	/**
	 * Converts given ipv4 to ipv6.
	 * 
	 * @param	string		$ip
	 * @return	string
	 */
	private static function convertIPv4To6($ip) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			// given ip is already ipv6
			return $ip;
		}
		
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
			// invalid ip given
			return '';
		}
		
		$ipArray = array_pad(explode('.', $ip), 4, 0);
		$part7 = base_convert(($ipArray[0] * 256) + $ipArray[1], 10, 16);
		$part8 = base_convert(($ipArray[2] * 256) + $ipArray[3], 10, 16);
		return '::ffff:'.$part7.':'.$part8;
	}
	

	/**
	 * Provides functions to compute password hashes.
	 * 
	 * @author	Alexander Ebert
	 * @copyright	2001-2013 WoltLab GmbH
	 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
	 * @package	com.woltlab.wcf
	 * @subpackage	util
	 * @category	Community Framework
	 */

	/**
	 * concated list of valid blowfish salt characters
	 * @var	string
	 */
	private static $blowfishCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
	
	/**
	 * list of supported encryption type by software identifier
	 * @var	array<string>
	 */
	private static $supportedEncryptionTypes = array(
		'ipb2',		// Invision Power Board 2.x
		'ipb3',		// Invision Power Board 3.x
		'mybb1',	// MyBB 1.x
		'smf1',		// Simple Machines Forum 1.x
		'smf2',		// Simple Machines Forum 2.x
		'vb3',		// vBulletin 3.x
		'vb4',		// vBulletin 4.x
		'vb5',		// vBulletin 5.x
		'wbb2',		// WoltLab Burning Board 2.x
		'wcf1',		// WoltLab Community Framework 1.x
		'wcf2',		// WoltLab Community Framework 2.x
		'xf1'		// XenForo 1.x
	);
	
	/**
	 * blowfish cost factor
	 * @var	string
	 */
	const BCRYPT_COST = '08';
	
	/**
	 * blowfish encryption type
	 * @var	string
	 */
	const BCRYPT_TYPE = '2a';
	
	/**
	 * Returns true if given encryption type is supported.
	 * 
	 * @param	string		$type
	 * @return	boolean
	 */
	public static function isSupported($type) {
		return in_array($type, self::$supportedEncryptionTypes);
	}
	
	/**
	 * Returns true if given hash looks like a valid bcrypt hash.
	 * 
	 * @param	string		$hash
	 * @return	boolean
	 */
	public static function isBlowfish($hash) {
		return (preg_match('#^\$2[afx]\$#', $hash) ? true : false);
	}
	
	/**
	 * Returns true if given bcrypt hash uses a different cost factor and should be re-computed.
	 * 
	 * @param	string		$hash
	 * @return	boolean
	 */
	public static function isDifferentBlowfish($hash) {
		$currentCost = intval(self::BCRYPT_COST);
		$hashCost = intval(substr($hash, 4, 2));
		
		if ($currentCost != $hashCost) {
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * Validates password against stored hash, encryption type is automatically resolved.
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	public static function checkPassword($username, $password, $dbHash) {
		$type = self::detectEncryption($dbHash);
		#var_dump($type);
		if ($type === 'unknown') {
			return false;
		}
		
		// drop type from hash
		$dbHash = substr($dbHash, strlen($type));
		
		// check for salt
		$salt = '';
		if (($pos = strrpos($dbHash, ':')) !== false) {
			$salt = substr(substr($dbHash, $pos), 1);
			$dbHash = substr($dbHash, 1, ($pos - 1));
		}
		
		// compare hash
		return call_user_func('self::'.$type, $username, $password, $salt, $dbHash);
	}
	
	/**
	 * Returns encryption type if possible.
	 * 
	 * @param	string		$hash
	 * @return	string
	 */
	public static function detectEncryption($hash) {
		if (($pos = strpos($hash, ':')) !== false) {
			$type = substr($hash, 0, $pos);
			if (in_array($type, self::$supportedEncryptionTypes)) {
				return $type;
			}
		}
		
		return 'unknown';
	}
	
	/**
	 * Returns a double salted bcrypt hash.
	 * 
	 * @param	string		$password
	 * @param	string		$salt
	 * @return	string
	 */
	public static function getDoubleSaltedHash($password, $salt = null) {
		if ($salt === null) {
			$salt = self::getRandomSalt();
		}
		
		return self::getSaltedHash(self::getSaltedHash($password, $salt), $salt);
	}
	
	/**
	 * Returns a simple salted bcrypt hash.
	 * 
	 * @param	string		$password
	 * @param	string		$salt
	 * @return	string
	 */
	public static function getSaltedHash($password, $salt = null) {
		if ($salt === null) {
			$salt = self::getRandomSalt();
		}
		
		return crypt($password, $salt);
	}
	
	/**
	 * Returns a random blowfish-compatible salt.
	 * 
	 * @return	string
	 */
	public static function getRandomSalt() {
		$salt = '';
		
		for ($i = 0, $maxIndex = (strlen(self::$blowfishCharacters) - 1); $i < 22; $i++) {
			$salt .= self::$blowfishCharacters[mt_rand(0, $maxIndex)];
		}
		
		return self::getSalt($salt);
	}
	
	/**
	 * Generates a random user password with the given character length.
	 * 
	 * @param	integer		$length
	 * @return	string
	 */
	public static function getRandomPassword($length = 8) {
		$availableCharacters = array(
			'abcdefghijklmnopqrstuvwxyz',
			'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'0123456789',
			'+#-.,;:?!'
		);
		
		$password = '';
		$type = 0;
		for ($i = 0; $i < $length; $i++) {
			$type = ($i % 4 == 0) ? 0 : ($type + 1);
			$password .= substr($availableCharacters[$type], MathUtil::getRandomValue(0, strlen($availableCharacters[$type]) - 1), 1);
		}
		
		return str_shuffle($password);
	}
	
	/**
	 * Compares two password hashes. This function is protected against timing attacks.
	 * 
	 * @see		http://codahale.com/a-lesson-in-timing-attacks/
	 * 
	 * @param	string		$hash1
	 * @param	string		$hash2
	 * @return	boolean
	 */
	public static function secureCompare($hash1, $hash2) {
		if (strlen($hash1) !== strlen($hash2)) {
			return false;
		}
	
		$result = 0;
		for ($i = 0, $length = strlen($hash1); $i < $length; $i++) {
			$result |= ord($hash1[$i]) ^ ord($hash2[$i]);
		}
	
		return ($result === 0);
	}
	
	/**
	 * Returns a blowfish salt, e.g. $2a$07$usesomesillystringforsalt$
	 * 
	 * @param	string		$salt
	 * @return	string
	 */
	protected static function getSalt($salt) {
		$salt = StringUtil::substring($salt, 0, 22);
		
		return '$' . self::BCRYPT_TYPE . '$' . self::BCRYPT_COST . '$' . $salt;
	}
	
	/**
	 * Validates the password hash for Invision Power Board 2.x (ipb2).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function ipb2($username, $password, $salt, $dbHash) {
		return self::vb3($username, $password, $salt, $dbHash);
	}
	
	/**
	 * Validates the password hash for Invision Power Board 3.x (ipb3).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function ipb3($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, md5(md5($salt) . md5($password)));
	}
	
	/**
	 * Validates the password hash for MyBB 1.x (mybb1).
	 *
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function mybb1($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, md5(md5($salt) . md5($password)));
	}
	
	/**
	 * Validates the password hash for Simple Machines Forums 1.x (smf1).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function smf1($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, sha1(StringUtil::toLowerCase($username) . $password));
	}
	
	/**
	 * Validates the password hash for Simple Machines Forums 2.x (smf2).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function smf2($username, $password, $salt, $dbHash) {
		return self::smf1($username, $password, $salt, $dbHash);
	}
	
	/**
	 * Validates the password hash for vBulletin 3 (vb3).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function vb3($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, md5(md5($password) . $salt));
	}
	
	/**
	 * Validates the password hash for vBulletin 4 (vb4).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function vb4($username, $password, $salt, $dbHash) {
		return self::vb3($username, $password, $salt, $dbHash);
	}
	
	/**
	 * Validates the password hash for vBulletin 5 (vb5).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function vb5($username, $password, $salt, $dbHash) {
		return self::vb3($username, $password, $salt, $dbHash);
	}
	
	/**
	 * Validates the password hash for WoltLab Burning Board 2 (wbb2).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function wbb2($username, $password, $salt, $dbHash) {
		if (self::secureCompare($dbHash, md5($password))) {
			return true;
		}
		else if (self::secureCompare($dbHash, sha1($password))) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Validates the password hash for WoltLab Community Framework 1.x (wcf1).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function wcf1($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, sha1($salt . sha1($salt . sha1($password))));
	}
	
	/**
	 * Validates the password hash for WoltLab Community Framework 2.x (wcf2).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function wcf2($username, $password, $salt, $dbHash) {
		return self::secureCompare($dbHash, self::getDoubleSaltedHash($password, $salt));
	}
	
	/**
	 * Validates the password hash for XenForo 1.x with (xf1).
	 * 
	 * @param	string		$username
	 * @param	string		$password
	 * @param	string		$salt
	 * @param	string		$dbHash
	 * @return	boolean
	 */
	protected static function xf1($username, $password, $salt, $dbHash) {
		if (self::secureCompare($dbHash, sha1(sha1($password) . $salt))) {
			return true;
		}
		else if (extension_loaded('hash')) {
			return self::secureCompare($dbHash, hash('sha256', hash('sha256', $password) . $salt));
		}
		
		return false;
	}

}
?>