<?php
 /*
 * Project:		eqdkpPLUS Libraries: myHTML
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2014-07-20 17:17:39 +0200 (So, 20 Jul 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:myHTML
 * @version		$Rev: 14490 $
 * 
 * $Id: phpbb3.bridge.class.php 14490 2014-07-20 15:17:39Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class phpbb31_bridge extends bridge_generic {
	
	public static function __shortcuts() {
		$shortcuts = array('env', 'config', 'user', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public $name = "phpBB3.1";
	
	public $data = array(
		'user'	=> array( //User
			'table'	=> 'users',
			'id'	=> 'user_id',
			'name'	=> 'username',
			'where'	=> 'username',
			'password' => 'user_password',
			'email'	=> 'user_email',
			'salt'	=> '',
			'QUERY'	=> '',
		),
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'groups', //without prefix
			'id'	=> 'group_id',
			'name'	=> 'group_name',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'table'	=> 'user_group',
			'group'	=> 'group_id',
			'user'	=> 'user_id',
			'QUERY'	=> '',
		),
		
	);
		
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'phpbb3_callafter',
		),
		'logout' 	=> 'phpbb3_logout',
		'autologin' => 'phpbb3_autologin',	
		'sync'		=> 'phpbb3_sync',
	);
		
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'type'	=> 'radio',
		),
		'cmsbridge_disable_sync' => array(
			'type'	=> 'radio',
		),
	);
		
	public $sync_fields = array(
		'icq',
		'town',
		'interests',
		'birthday',
		'user_email',
		'username',
	);

	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash, $strUsername){
		if (strlen($hash) == 32){
			
			//plain md5
			$result = (md5($password) === $hash) ? true : false;
			if ($result) return true;
			
			//mybb md5
			$myhash = md5(md5($strSalt) . md5($password));
			if ($myhash === $hash) return true;
			
			//vb md5
			$myhash = md5(md5($password) . $strSalt);
			if ($myhash === $hash) return true;
			
			return false;
		}
		
		//Bcrypt
		if (strpos($hash, '$2a$') === 0 || strpos($hash, '$2x$') === 0 || strpos($hash, '$2y$') === 0){
			if (strpos($hash, '$2a$') === 0)
			{
				if (ord($password[strlen($password)-1]) & 128)
				{
					return false;
				}
			}
			
			$salt = substr($hash, 0, 29);
			if (strlen($salt) != 29)
			{
				return false;
			}
			
			$myhash = crypt($password, $salt);
			
			if ($hash === $myhash)
			{
				return true;
			}
			return false;
		} //end bcrypt
		
		//Bcrypt wcf2
		if (strpos($hash, '$wcf2$') === 0){
			
			$salt = substr($hash, 0, 29);
			
			if (strlen($salt) != 29)
			{
				return false;
			}
			
			$myhash = crypt($password, $salt);
			$myhash = crypt($myhash, $salt);
			
			if ($hash === $myhash)
			{
				return true;
			}
		}
		
		//PHPASS
		if (strpos($hash, '$P$') === 0){
			$wp_hasher = new PasswordHash(8, TRUE);
			$check = $wp_hasher->CheckPassword($password, $hash);
			return $check;
		}
		
		//phpbb 3.0
		if (strlen($hash) === 34){
			$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			return ($this->_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
		}
		
		//SHA1
		if(strlen($hash) === 40){
			//plain sha1
			$myhash = sha1($password);
			if ($myhash === $hash) return true;
			
			//smf sha1
			$myhash = sha1(strtolower($strUsername) . $password);
			if ($myhash === $hash) return true;
			
			//wcf sha1
			$myhash = sha1($strSalt . sha1($strSalt . sha1($password)));
			if ($myhash === $hash) return true;
		}
		
		return false;
	}
	
	private function get_random_salt(){
		$rand = random_string(false, 22);
		return base64_encode($rand);
	}
	
	public function phpbb3_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
		
			if ($arrUserdata['user_type'] == '1') {
				return false;
			}
			//Single Sign On
			if ($this->config->get('cmsbridge_disable_sso') != '1'){
				$this->phpbb3_sso($arrUserdata, $boolAutoLogin);
			}
		}
		return true;
	}
	
	public function phpbb3_sso($arrUserdata, $boolAutoLogin = false){
		$user_id = $arrUserdata['id'];
		$strSessionID = md5(generateRandomBytes(55));
		$this->db->prepare("DELETE FROM ".$this->prefix."sessions WHERE session_user_id=?")->execute($user_id);
		
		$query = $this->db->query("SELECT * FROM ".$this->prefix."config");
		if ($query){
			while($row = $query->fetchAssoc()){
				$arrConfig[$row['config_name']] = $row['config_value'];
			}
		} else return false;
		
		$ip = $this->get_ip();

		//PW is true, logg the user into our Forum
		$arrSet = array(
			'session_user_id'			=> (int) $user_id,
			'session_start'				=> (int) $this->time->time,
			'session_last_visit'		=> (int) $this->time->time,
			'session_time'				=> (int) $this->time->time,
			'session_browser'			=> (string) trim(substr($this->env->useragent, 0, 149)),
			'session_forwarded_for'		=> '',
			'session_ip'				=> $ip,
			'session_autologin'			=> ($boolAutoLogin) ? 1 : 0,
			'session_admin'				=> 0,
			'session_viewonline'		=> 1,
			'session_id'				=> $strSessionID,
			'session_page'				=> '',
			'session_forum_id'			=> 0,
		);
		
		$this->db->prepare("INSERT INTO ".$this->prefix."sessions :p")->set($arrSet)->execute();
				
		// Set cookie
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
		setcookie($arrConfig['cookie_name'].'_sid', $strSessionID, $expire, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		//User-Cookie
		setcookie($arrConfig['cookie_name'].'_u', $user_id, $expire, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		
		if ($boolAutoLogin){
			$strLoginKey = substr($this->user->generate_salt(), 4, 16);
			
			$this->db->prepare("INSERT INTO ".$this->prefix."sessions_keys :p")->set(array(
				'key_id'	=> md5($strLoginKey),
				'last_ip'	=> $ip,
				'last_login'=> (int)$this->time->time,
				'user_id'	=> (int) $user_id,
			))->execute();
		
			setcookie($arrConfig['cookie_name'].'_k', $strLoginKey, $expire, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		} else {
			setcookie($arrConfig['cookie_name'].'_k', '', $expire, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		}
		
		return true;
	}
	
	public function phpbb3_autologin(){
		$query = $this->db->query("SELECT * FROM ".$this->prefix."config");
		if ($query){
			while($row = $query->fetchAssoc()){
				$arrConfig[$row['config_name']] = $row['config_value'];
			}
		} else return false;
		
		$ip = $this->get_ip();
	
		$userID = (int)$_COOKIE[$arrConfig['cookie_name'].'_u'];
		$SID = $_COOKIE[$arrConfig['cookie_name'].'_sid'];
		
		if ($SID == NULL || $SID == "") return false;
	
		$result = $this->db->prepare("SELECT * FROM ".$this->prefix."sessions WHERE session_user_id = ? and session_id=?")->execute($userID, $SID);
		if ($result){
			$row = $result->fetchRow();
			if($row){
				if ($row['session_ip'] == $ip && $row['session_browser'] == (string) trim(substr($this->env->useragent, 0, 149))){
					$result2 = $this->db->prepare("SELECT * FROM ".$this->prefix."users WHERE user_id=?")->execute($userID);
					if ($result2){
						$row2 = $result2->fetchRow();
						if ($row2){
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
	
	private function get_ip(){
		$iip = (!empty($_SERVER['REMOTE_ADDR'])) ? (string) $_SERVER['REMOTE_ADDR'] : '';
		$iip = preg_replace('# {2,}#', ' ', str_replace(',', ' ', $iip));

		// split the list of IPs
		$ips = explode(' ', trim($iip));

		// Default IP if REMOTE_ADDR is invalid
		$iip = '127.0.0.1';

		foreach ($ips as $ip)
		{
			if (preg_match('#^(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$#', $ip))
			{
				$iip = $ip;
			}
			else if (preg_match('#^(?:(?:(?:[\dA-F]{1,4}:){6}(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:::(?:[\dA-F]{1,4}:){0,5}(?:[\dA-F]{1,4}(?::[\dA-F]{1,4})?|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:):(?:[\dA-F]{1,4}:){4}(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:){1,2}:(?:[\dA-F]{1,4}:){3}(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:){1,3}:(?:[\dA-F]{1,4}:){2}(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:){1,4}:(?:[\dA-F]{1,4}:)(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:){1,5}:(?:[\dA-F]{1,4}:[\dA-F]{1,4}|(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])))|(?:(?:[\dA-F]{1,4}:){1,6}:[\dA-F]{1,4})|(?:(?:[\dA-F]{1,4}:){1,7}:)|(?:::))$#i', $ip))
			{
				// Quick check for IPv4-mapped address in IPv6
				if (stripos($ip, '::ffff:') === 0)
				{
					$ipv4 = substr($ip, 7);

					if (preg_match('#^(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$#', $ipv4))
					{
						$ip = $ipv4;
					}
				}

				$iip = $ip;
			}
			else
			{
				// We want to use the last valid address in the chain
				// Leave foreach loop when address is invalid
				break;
			}
		}
		return $iip;
	}
	
	public function phpbb3_logout() {
		$arrUserdata = $this->get_userdata($this->user->data['username']);
		if (isset($arrUserdata['id'])){
			$this->db->prepare("DELETE FROM ".$this->prefix."sessions WHERE session_user_id=?")->execute($arrUserdata['id']);
		}
		
		$query = $this->db->query("SELECT * FROM ".$this->prefix."config");
		if ($query){
			while($row = $query->fetchAssoc()){
				$arrConfig[$row['config_name']] = $row['config_value'];
			}
		} else return;
				
		setcookie($arrConfig['cookie_name'].'_sid', '', 0, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		//User-Cookie
		setcookie($arrConfig['cookie_name'].'_u', '', 0, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
		setcookie($arrConfig['cookie_name'].'_k', '', 0, $arrConfig['cookie_path'], $arrConfig['cookie_domain'], $arrConfig['cookie_secure']);
	}
	
	public function phpbb3_sync($arrUserdata){
		if ($this->config->get('cmsbridge_disable_sync') == '1'){
			return false;
		}
		$sync_array = array(
			'icq' 			=> $arrUserdata['user_icq'],
			'town'			=> $arrUserdata['user_from'],
			'interests'		=> $arrUserdata['user_interests'],
			'birthday'		=> $this->_handle_birthday($arrUserdata['user_birthday']),
		);
		return $sync_array;
	}
	
	private function _handle_birthday($date){
		list($d, $m, $y) = explode('-', $date);
		if ($y != '' && $y != 0 && $m != '' && $m != 0 && $d != '' && $d != 0){
			return $this->time->mktime(0,0,0,$m,$d,$y);
		}
		return 0;
	}
	
	/**
	 * Hash Algorithm from phpBB 3.0.0
	 * used due some Major Werid Changes handling the password of phpBB 3.0.0
	 *
	 * @param string $password
	 * @param string $setting
	 * @param string_type $itoa64
	 * @return string
	 */
	function _hash_crypt_private($password, $setting, &$itoa64)
	{
		$password = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $password), ENT_COMPAT, 'UTF-8'));
	
		$output = '*';
	
		// Check for correct hash
		if (substr($setting, 0, 3) != '$H$')
		{
			return $output;
		}
	
		$count_log2 = strpos($itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30)
		{
			return $output;
		}
		$count = 1 << $count_log2;
		$salt = substr($setting, 4, 8);
		if (strlen($salt) != 8)
		{
			return $output;
		}
	
		/**
			* We're kind of forced to use MD5 here since it's the only
			* cryptographic primitive available in all versions of PHP
			* currently in use.  To implement our own low-level crypto
			* in PHP would result in much worse performance and
			* consequently in lower iteration counts and hashes that are
			* quicker to crack (by non-PHP code).
			*/
		if (PHP_VERSION >= 5)
		{
			$hash = md5($salt . $password, true);
			do
			{
				$hash = md5($hash . $password, true);
			}
			while (--$count);
		}
		else
		{
			$hash = pack('H*', md5($salt . $password));
			do
			{
				$hash = pack('H*', md5($hash . $password));
			}
			while (--$count);
		}
	
		$output = substr($setting, 0, 12);
		$output .= $this->_hash_encode64($hash, 16, $itoa64);
	
		return $output;
	}
	
	
	/**
	 * Encoding 64-Bit Hash Algorithm from phpBB 3.0.0
	 * used due some Major Werid Changes handling the password of phpBB 3.0.0
	 *
	 * @param string $input
	 * @param integer $count
	 * @param string $itoa64
	 * @return string
	 */
	function _hash_encode64($input, $count, &$itoa64)
	{
		$output = '';
		$i = 0;
		do
		{
			$value = ord($input[$i++]);
			$output .= $itoa64[$value & 0x3f];
	
			if ($i < $count)
			{
				$value |= ord($input[$i]) << 8;
			}
	
			$output .= $itoa64[($value >> 6) & 0x3f];
	
			if ($i++ >= $count)
			{
				break;
			}
	
			if ($i < $count)
			{
				$value |= ord($input[$i]) << 16;
			}
	
			$output .= $itoa64[($value >> 12) & 0x3f];
	
			if ($i++ >= $count)
			{
				break;
			}
	
			$output .= $itoa64[($value >> 18) & 0x3f];
		}
		while ($i < $count);
	
		return $output;
	}
}

if (!class_exists('PasswordHash')){
	#
	# Written by Solar Designer <solar at openwall.com> in 2004-2006 and placed in
	# the public domain.  Revised in subsequent years, still public domain.
	#
	# There's absolutely no warranty.
	#
	# Please be sure to update the Version line if you edit this file in any way.
	# It is suggested that you leave the main version number intact, but indicate
	# your project name (after the slash) and add your own revision information.
	#
	# Please do not change the "private" password hashing method implemented in
	# here, thereby making your hashes incompatible.  However, if you must, please
	# change the hash type identifier (the "$P$") to something different.
	#
	# Obviously, since this code is in the public domain, the above are not
	# requirements (there can be none), but merely suggestions.
	#
	
	/**
	 * Portable PHP password hashing framework.
	 *
	 * @package phpass
	 * @version 0.3 / WordPress
	 * @link http://www.openwall.com/phpass/
	 * @since 2.5
	 */
	class PasswordHash {
		var $itoa64;
		var $iteration_count_log2;
		var $portable_hashes;
		var $random_state;
	
		function PasswordHash($iteration_count_log2, $portable_hashes)
		{
			$this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	
			if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
				$iteration_count_log2 = 8;
			$this->iteration_count_log2 = $iteration_count_log2;
	
			$this->portable_hashes = $portable_hashes;
	
			$this->random_state = microtime() . uniqid(rand(), TRUE); // removed getmypid() for compatibility reasons
		}
	
		function get_random_bytes($count)
		{
			$output = '';
			if ( @is_readable('/dev/urandom') &&
					($fh = @fopen('/dev/urandom', 'rb'))) {
						$output = fread($fh, $count);
						fclose($fh);
					}
	
					if (strlen($output) < $count) {
						$output = '';
						for ($i = 0; $i < $count; $i += 16) {
							$this->random_state =
							md5(microtime() . $this->random_state);
							$output .=
							pack('H*', md5($this->random_state));
						}
						$output = substr($output, 0, $count);
					}
	
					return $output;
		}
	
		function encode64($input, $count)
		{
			$output = '';
			$i = 0;
			do {
				$value = ord($input[$i++]);
				$output .= $this->itoa64[$value & 0x3f];
				if ($i < $count)
					$value |= ord($input[$i]) << 8;
				$output .= $this->itoa64[($value >> 6) & 0x3f];
				if ($i++ >= $count)
					break;
				if ($i < $count)
					$value |= ord($input[$i]) << 16;
				$output .= $this->itoa64[($value >> 12) & 0x3f];
				if ($i++ >= $count)
					break;
				$output .= $this->itoa64[($value >> 18) & 0x3f];
			} while ($i < $count);
	
			return $output;
		}
	
		function gensalt_private($input)
		{
			$output = '$P$';
			$output .= $this->itoa64[min($this->iteration_count_log2 +
					((PHP_VERSION >= '5') ? 5 : 3), 30)];
			$output .= $this->encode64($input, 6);
	
			return $output;
		}
	
		function crypt_private($password, $setting)
		{
			$output = '*0';
			if (substr($setting, 0, 2) == $output)
				$output = '*1';
	
			$id = substr($setting, 0, 3);
			# We use "$P$", phpBB3 uses "$H$" for the same thing
			if ($id != '$P$' && $id != '$H$')
				return $output;
	
				$count_log2 = strpos($this->itoa64, $setting[3]);
				if ($count_log2 < 7 || $count_log2 > 30)
					return $output;
	
				$count = 1 << $count_log2;
	
				$salt = substr($setting, 4, 8);
				if (strlen($salt) != 8)
					return $output;
	
				# We're kind of forced to use MD5 here since it's the only
				# cryptographic primitive available in all versions of PHP
				# currently in use.  To implement our own low-level crypto
				# in PHP would result in much worse performance and
				# consequently in lower iteration counts and hashes that are
				# quicker to crack (by non-PHP code).
				if (PHP_VERSION >= '5') {
					$hash = md5($salt . $password, TRUE);
					do {
						$hash = md5($hash . $password, TRUE);
					} while (--$count);
				} else {
					$hash = pack('H*', md5($salt . $password));
					do {
						$hash = pack('H*', md5($hash . $password));
					} while (--$count);
				}
	
				$output = substr($setting, 0, 12);
				$output .= $this->encode64($hash, 16);
	
				return $output;
		}
	
		function gensalt_extended($input)
		{
			$count_log2 = min($this->iteration_count_log2 + 8, 24);
			# This should be odd to not reveal weak DES keys, and the
			# maximum valid value is (2**24 - 1) which is odd anyway.
			$count = (1 << $count_log2) - 1;
	
			$output = '_';
			$output .= $this->itoa64[$count & 0x3f];
			$output .= $this->itoa64[($count >> 6) & 0x3f];
			$output .= $this->itoa64[($count >> 12) & 0x3f];
			$output .= $this->itoa64[($count >> 18) & 0x3f];
	
			$output .= $this->encode64($input, 3);
	
			return $output;
		}
	
		function gensalt_blowfish($input)
		{
			# This one needs to use a different order of characters and a
			# different encoding scheme from the one in encode64() above.
			# We care because the last character in our encoded string will
			# only represent 2 bits.  While two known implementations of
			# bcrypt will happily accept and correct a salt string which
			# has the 4 unused bits set to non-zero, we do not want to take
			# chances and we also do not want to waste an additional byte
			# of entropy.
			$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
			$output = '$2a$';
			$output .= chr(ord('0') + $this->iteration_count_log2 / 10);
			$output .= chr(ord('0') + $this->iteration_count_log2 % 10);
			$output .= '$';
	
			$i = 0;
			do {
				$c1 = ord($input[$i++]);
				$output .= $itoa64[$c1 >> 2];
				$c1 = ($c1 & 0x03) << 4;
				if ($i >= 16) {
					$output .= $itoa64[$c1];
					break;
				}
	
				$c2 = ord($input[$i++]);
				$c1 |= $c2 >> 4;
				$output .= $itoa64[$c1];
				$c1 = ($c2 & 0x0f) << 2;
	
				$c2 = ord($input[$i++]);
				$c1 |= $c2 >> 6;
				$output .= $itoa64[$c1];
				$output .= $itoa64[$c2 & 0x3f];
			} while (1);
	
			return $output;
		}
	
		function HashPassword($password)
		{
			$random = '';
	
			if (CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
				$random = $this->get_random_bytes(16);
				$hash =
				crypt($password, $this->gensalt_blowfish($random));
				if (strlen($hash) == 60)
					return $hash;
			}
	
			if (CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
				if (strlen($random) < 3)
					$random = $this->get_random_bytes(3);
				$hash =
				crypt($password, $this->gensalt_extended($random));
				if (strlen($hash) == 20)
					return $hash;
			}
	
			if (strlen($random) < 6)
				$random = $this->get_random_bytes(6);
			$hash =
			$this->crypt_private($password,
					$this->gensalt_private($random));
			if (strlen($hash) == 34)
				return $hash;
	
			# Returning '*' on error is safe here, but would _not_ be safe
			# in a crypt(3)-like function used _both_ for generating new
			# hashes and for validating passwords against existing hashes.
			return '*';
		}
	
		function CheckPassword($password, $stored_hash)
		{
			$hash = $this->crypt_private($password, $stored_hash);
			if ($hash[0] == '*')
				$hash = crypt($password, $stored_hash);
	
			return $hash == $stored_hash;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_phpbb31_bridge',phpbb31_bridge::$shortcuts);
?>