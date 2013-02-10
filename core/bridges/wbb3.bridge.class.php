<?php
 /*
 * Project:		eqdkpPLUS Libraries: myHTML
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:myHTML
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class wbb3_bridge extends bridge_generic {
	
	public static function __shortcuts() {
		$shortcuts = array('env', 'config', 'user', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public $name = 'WBB 3';
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'group', //without prefix
			'id'	=> 'groupID',
			'name'	=> 'groupName',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'table'	=> 'user_to_groups',
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
			'salt'	=> 'salt',
			'QUERY'	=> '',
		),
	);
	
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'wbb3_callafter',
		),
		'logout' 	=> 'wbb3_logout',
		'autologin' => '',	
		'sync'		=> '',
	);
	
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'fieldtype'	=> 'checkbox',
			'name'		=> 'cmsbridge_disable_sso',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash){
		$settings = $this->get_encryption_settings();
		$strHashedPassword = $this->getDoubleSaltedHash($settings, $password, $strSalt);
		if ($hash === $strHashedPassword) return true;

		return false;
	}


	private function get_encryption_settings(){
		$config = array();
		$result = $this->db->fetch_array("SELECT * FROM ".$this->prefix."option WHERE optionName = 'encryption_method' OR optionName = 'encryption_enable_salting' OR optionName = 'encryption_salt_position' OR optionName = 'encryption_encrypt_before_salting'");
		if (is_array($result)){
			foreach ($result as $value){
				$config[$value['optionName']] = $value['optionValue'];
			}
		}
		return $config;
	}
	
	public function wbb3_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['banned'] != '0' || $arrUserdata['activationCode'] != '0') {
				return false;
			}
		}
		
		//Single Sign On
		if ($this->config->get('cmsbridge_disable_sso') != '1'){
			$this->wbb3_sso($arrUserdata, $boolAutoLogin);
		}
		return true;
	}
	
	public function wbb3_sso($arrUserdata, $boolAutoLogin){
		//Get wbb package ID
		$query = $this->db->query("SELECT packageID FROM ".$this->prefix."package WHERE package='com.woltlab.wbb'");
		$packageId = $this->db->fetch_row($query);
		if (isset($packageId['packageID'])){
			$user_id = intval($arrUserdata['id']);
			$strSessionID = md5(rand().rand()).'a7w8er45';
			$this->db->query("DELETE FROM ".$this->prefix."session WHERE userID='".$this->db->escape($user_id)."'");
			//PW is true, logg the user into our Forum
			$arrSet = array(
				'sessionID'					=> $strSessionID,
				'packageID'					=> $packageId,
				'userID'					=> (int) $user_id,
				'ipAddress'					=> $this->env->ip,
				'userAgent'					=> $this->env->useragent,
				'lastActivityTime'			=> (int) $this->time->time,
				'requestURI'				=> '',
				'requestMethod'				=> 'GET',
				'username'					=> $arrUserdata['username'],
			);
			
			$this->db->query("INSERT INTO ".$this->prefix."session :params", $arrSet);
			
			$config = array();
			$result = $this->db->fetch_array("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix' OR optionName = 'cookie_path' OR optionName = 'cookie_domain'");
			if (is_array($result)){
				foreach ($result as $value){
					$config[$value['optionName']] = $value['optionValue'];
				}
			}
			
			$expire = $this->time->time + 31536000;
			if($arrConfig['cookie_domain'] == '') {
				$arrDomains = explode('.', $this->env->server_name);
				$arrDomainsReversed = array_reverse($arrDomains);
				if (count($arrDomainsReversed) > 1){
					$arrConfig['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
				} else {
					$arrConfig['cookie_domain'] = $this->env->server_name;
				}
			}
			//SID Cookie
			setcookie($config['cookie_prefix'].'cookieHash', $strSessionID, $expire, $config['cookie_path'], $arrConfig['cookie_domain'], $this->env->ssl);
			return true;
		}
		
		return false;
	}
	
	public function wbb3_logout(){
		$arrUserdata = $this->get_userdata($this->user->data['username']);
		if (isset($arrUserdata['id'])){
			$this->db->query("DELETE FROM ".$this->prefix."session WHERE userID='".$this->db->escape($arrUserdata['id'])."'");
		}
		$config = array();
		$result = $this->db->fetch_array("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix' OR optionName = 'cookie_path' OR optionName = 'cookie_domain'");
		if (is_array($result)){
			foreach ($result as $value){
				$config[$value['optionName']] = $value['optionValue'];
			}
		}
		
		if($arrConfig['cookie_domain'] == '') {
			$arrDomains = explode('.', $this->env->server_name);
			$arrDomainsReversed = array_reverse($arrDomains);
			if (count($arrDomainsReversed) > 1){
				$arrConfig['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
			} else {
				$arrConfig['cookie_domain'] = $this->env->server_name;
			}
		}
		
		setcookie($config['cookie_prefix'].'cookieHash', '', 0, $config['cookie_path'], $arrConfig['cookie_domain'], $this->env->ssl);
	}
	
	/**
	 * Returns a double salted hash of the given value.
	 *
	 * @param 	string 		$value
	 * @param	string		$salt
	 * @return 	string 		$hash
	 */
	public function getDoubleSaltedHash($settings, $value, $salt) {
		return $this->encrypt($salt . $this->getSaltedHash($settings, $value, $salt), $settings['encryption_method']);
	}
	
	/**
	 * Returns a salted hash of the given value.
	 *
	 * @param 	string 		$value
	 * @param	string		$salt
	 * @return 	string 		$hash
	 */
	public function getSaltedHash($settings, $value, $salt) {
		if (!isset($settings['encryption_enable_salting']) || $settings['encryption_enable_salting'] == '1') {
			$hash = '';
			// salt
			if (!isset($settings['encryption_salt_position']) || $settings['encryption_salt_position'] == 'before') {
				$hash .= $salt;
			}
			
			// value
			if (!isset($settings['encryption_encrypt_before_salting']) || $settings['encryption_encrypt_before_salting'] == '1') {
				$hash .= $this->encrypt($value, $settings['encryption_method']);
			}
			else {
				$hash .= $value;
			}
			
			// salt
			if (!isset($settings['encryption_salt_position']) || $settings['encryption_salt_position'] == 'after') {
				$hash .= $salt;
			}
			
			return $this->encrypt($hash, $settings['encryption_method']);
		}
		else {
			return $this->encrypt($value, $settings['encryption_method']);
		}
	}
	
		
	/**
	 * encrypts the given value.
	 *
	 * @param 	string 		$value
	 * @return 	string 		$hash
	 */
	public function encrypt($value, $encryption_method = 'sha1') {
		switch ($encryption_method) {
			case 'sha1': return sha1($value);
			case 'md5': return md5($value);
			case 'crc32': return crc32($value);
			case 'crypt': return crypt($value);
			default: return sha1($value);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wbb3_bridge',wbb3_bridge::__shortcuts());
?>