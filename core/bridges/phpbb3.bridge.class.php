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

class phpbb3_bridge extends bridge_generic {
	
	public static $name = "phpBB3";
	
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
				
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'type'	=> 'radio',
		),
		'cmsbridge_disable_sync' => array(
			'type'	=> 'radio',
		),
	);
	
	public $blnSyncBirthday = true;
		

	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		if (strlen($hash) == 34)
		{
			return ($this->_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
		}
		return (md5($password) === $hash) ? true : false;
	}
	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		//Is user active?
		if ($boolLoginResult){
		
			if ($arrUserdata['user_type'] == '1') {
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
	
	public function sso($arrUserdata, $boolAutoLogin = false){
		$user_id = $arrUserdata['id'];
		$strSessionID = md5(generateRandomBytes(55));
		//$this->bridgedb->prepare("DELETE FROM ".$this->prefix."sessions WHERE session_user_id=?")->execute($user_id);
		
		$query = $this->bridgedb->query("SELECT * FROM ".$this->prefix."config");
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
		
		$this->bridgedb->prepare("INSERT INTO ".$this->prefix."sessions :p")->set($arrSet)->execute();
				
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
			
			$this->bridgedb->prepare("INSERT INTO ".$this->prefix."sessions_keys :p")->set(array(
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
	
	public function autologin($arrCookieData){
		$query = $this->bridgedb->query("SELECT * FROM ".$this->prefix."config");
		if ($query){
			while($row = $query->fetchAssoc()){
				$arrConfig[$row['config_name']] = $row['config_value'];
			}
		} else return false;
		
		$ip = $this->get_ip();
	
		$userID = (int)$_COOKIE[$arrConfig['cookie_name'].'_u'];
		$SID = $_COOKIE[$arrConfig['cookie_name'].'_sid'];
		
		if ($SID == NULL || $SID == "") return false;
	
		$result = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."sessions WHERE session_user_id = ? and session_id=?")->execute($userID, $SID);
		if ($result){
			$row = $result->fetchRow();
			if($row){
				if ($row['session_ip'] == $ip && $row['session_browser'] == (string) trim(substr($this->env->useragent, 0, 149))){
					$result2 = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."users WHERE user_id=?")->execute($userID);
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
	
	public function logout() {
		$arrUserdata = $this->bridge->get_userdata($this->user->data['username']);
		if (isset($arrUserdata['id'])){
			$this->bridgedb->prepare("DELETE FROM ".$this->prefix."sessions WHERE session_user_id=?")->execute($arrUserdata['id']);
		}
		
		$query = $this->bridgedb->query("SELECT * FROM ".$this->prefix."config");
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
	
	public function sync($arrUserdata){
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
	
	public function sync_fields(){
		return array(
			'icq'		=> 'ICQ',
			'town'		=> 'Town',
			'interests' => 'Interests',
			'birthday'	=> 'Birthday',
		);
	}
	
	private function _handle_birthday($date){
		list($d, $m, $y) = explode('-', $date);
		if ($y != '' && $y != 0 && $m != '' && $m != 0 && $d != '' && $d != 0){
			return $this->time->mktime(0,1,0,$m,$d,$y);
		}
		return 0;
	}
	
	//---------------------------------------------------------
	//Functions from phpbb 3
	
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
?>