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

class wbb3_bridge extends bridge_generic {
	
	public static $name = 'WBB 3';
	
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
	
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'type'	=> 'radio',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		$settings = $this->get_encryption_settings();
		$strHashedPassword = $this->getDoubleSaltedHash($settings, $password, $strSalt);
		if ($hash === $strHashedPassword) return true;

		return false;
	}


	private function get_encryption_settings(){
		$config = array();
		$objQuery = $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'encryption_method' OR optionName = 'encryption_enable_salting' OR optionName = 'encryption_salt_position' OR optionName = 'encryption_encrypt_before_salting'");
		if ($objQuery){
			$result = $objQuery->fetchAllAssoc();
			if (is_array($result)){
				foreach ($result as $value){
					$config[$value['optionName']] = $value['optionValue'];
				}
			}
		}
		return $config;
	}
	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		if ($boolLoginResult){
			//Is user active?
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
	
	private function sso($arrUserdata, $boolAutoLogin){
		//Get wbb package ID
		$query = $this->bridgedb->query("SELECT packageID FROM ".$this->prefix."package WHERE package='com.woltlab.wbb'");
		if($query){
			$packageId = $query->fetchAssoc();
			if (isset($packageId['packageID'])){
				$user_id = intval($arrUserdata['id']);
				$strSessionID = substr(md5(generateRandomBytes(55)).md5(generateRandomBytes(55)), 0, 40);
				$this->bridgedb->prepare("DELETE FROM ".$this->prefix."session WHERE userID=?")->execute($user_id);
				//PW is true, logg the user into our Forum
				$arrSet = array(
					'sessionID'					=> $strSessionID,
					'packageID'					=> $packageId['packageID'],
					'userID'					=> (int) $user_id,
					'ipAddress'					=> $this->env->ip,
					'userAgent'					=> $this->env->useragent,
					'lastActivityTime'			=> (int) $this->time->time,
					'requestURI'				=> '',
					'requestMethod'				=> 'GET',
					'username'					=> $arrUserdata['username'],
				);
				$this->bridgedb->prepare("INSERT INTO ".$this->prefix."session :p")->set($arrSet)->execute();
				
				$config = array();
				$objQuery = $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix' OR optionName = 'cookie_path' OR optionName = 'cookie_domain'");
				if ($objQuery){
					$result = $objQuery->fetchAllAssoc();
					if (is_array($result)){
						foreach ($result as $value){
							if (isset($config[$value['optionName']]) && intval($packageId['packageID']) != intval($value['packageID'])) continue;
							$config[$value['optionName']] = $value['optionValue'];
						}
					}
				} else return;
				
				$expire = $this->time->time + 31536000;
				if($config['cookie_domain'] == '') {
					$arrDomains = explode('.', $this->env->server_name);
					$arrDomainsReversed = array_reverse($arrDomains);
					if (count($arrDomainsReversed) > 1){
						$config['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
					} else {
						$config['cookie_domain'] = $this->env->server_name;
					}
				}
				//SID Cookie
				setcookie($config['cookie_prefix'].'cookieHash', $strSessionID, $expire, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
				return true;
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
		$objQuery = $this->bridgedb->query("SELECT * FROM ".$this->prefix."option WHERE optionName = 'cookie_prefix' OR optionName = 'cookie_path' OR optionName = 'cookie_domain'");
		if ($objQuery){
			$result = $objQuery->fetchAllAssoc();
			if (is_array($result)){
				foreach ($result as $value){
					if (isset($config[$value['optionName']]) && intval($packageId['packageID']) != intval($value['packageID'])) continue;
					$config[$value['optionName']] = $value['optionValue'];
				}
			}
		} else return;
		
		if($config['cookie_domain'] == '') {
			$arrDomains = explode('.', $this->env->server_name);
			$arrDomainsReversed = array_reverse($arrDomains);
			if (count($arrDomainsReversed) > 1){
				$config['cookie_domain'] = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
			} else {
				$config['cookie_domain'] = $this->env->server_name;
			}
		}
		
		setcookie($config['cookie_prefix'].'cookieHash', '', 0, $config['cookie_path'], $config['cookie_domain'], $this->env->ssl);
	}
	
	/**
	 * Returns a double salted hash of the given value.
	 *
	 * @param 	string 		$value
	 * @param	string		$salt
	 * @return 	string 		$hash
	 */
	private function getDoubleSaltedHash($settings, $value, $salt) {
		return $this->encrypt($salt . $this->getSaltedHash($settings, $value, $salt), $settings['encryption_method']);
	}
	
	/**
	 * Returns a salted hash of the given value.
	 *
	 * @param 	string 		$value
	 * @param	string		$salt
	 * @return 	string 		$hash
	 */
	private function getSaltedHash($settings, $value, $salt) {
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
	private function encrypt($value, $encryption_method = 'sha1') {
		switch ($encryption_method) {
			case 'sha1': return sha1($value);
			case 'md5': return md5($value);
			case 'crc32': return crc32($value);
			case 'crypt': return crypt($value);
			default: return sha1($value);
		}
	}
}
?>