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

class smf2_bridge extends bridge_generic {

	public static $name = "SMF 2";
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'membergroups', //without prefix
			'id'	=> 'id_group',
			'name'	=> 'group_name',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'QUERY'	=> '',
			'FUNCTION'	=> 'smf2_get_user_groups',
		),
		'user'	=> array( //User
			'table'	=> 'members',
			'id'	=> 'id_member',
			'name'	=> 'member_name',
			'where'	=> 'member_name',
			'password' => 'passwd',
			'email'	=> 'email_address',
			'salt'	=> 'password_salt',
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
		'cmsbridge_sso_cookiename'	=> array(
			'type'	=> 'text',
		),
		'cmsbridge_sso_cookiedomain' => array(
			'type'	=> 'text',
		),
	);
	
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		//Use normal strtolower and not utf8_strotolower, because SMF2 does the same...
		if (sha1(strtolower($strUsername).$password) == $hash){
			return true;
		}
		return false;
	}
	
	public function smf2_get_user_groups($intUserID){
		$query = $this->bridgedb->prepare("SELECT id_group, id_post_group, additional_groups FROM ".$this->prefix."members WHERE id_member=?")->execute($intUserID);
		$arrReturn = array();
		if ($query){
			$result = $query->fetchAssoc();
			$arrReturn[] = (int)$result['id_group'];
			$arrReturn[] = (int)$result['id_post_group'];
			
			$arrAditionalGroups = explode(',', $result['additional_groups']);
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
				$this->sso($arrUserdata, $strPassword, $boolSetAutoLogin);
			}
			
			return true;
		}
		return false;
	}
	
	private function sso($arrUserdata, $strPassword, $boolAutoLogin = false){
		if (!strlen($this->config->get('cmsbridge_sso_cookiename')) || !strlen($this->config->get('cmsbridge_url'))) return false;
		
		$cookie_length = 31536000;
		$cookie_state = 2;
		$password = sha1($arrUserdata['passwd'].$arrUserdata['password_salt']);
		$data = serialize(array($arrUserdata['id'], $password, time() + $cookie_length, $cookie_state));
		
		$strBoardPath = parse_url($this->config->get('cmsbridge_url'), PHP_URL_PATH);
		if($this->config->get('cmsbridge_sso_cookiedomain') == '') {
				$strBoardURL = parse_url($this->config->get('cmsbridge_url'), PHP_URL_HOST);
				
				$arrDomains = explode('.', $strBoardURL);
				$arrDomainsReversed = array_reverse($arrDomains);
				if (count($arrDomainsReversed) > 1){
					$cookieDomain = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
				} else {
					$cookieDomain = ($strBoardURL == 'localhost') ? '' : $strBoardURL;
				}
		} else $cookieDomain = $this->config->get('cmsbridge_sso_cookiedomain');
		
		$res = setcookie($this->config->get('cmsbridge_sso_cookiename'), $data, time() + $cookie_length, $strBoardPath, $cookieDomain, $this->env->ssl);
	
		return true;
	}
	
	public function logout() {
		$strBoardURL = parse_url($this->config->get('cmsbridge_url'), PHP_URL_HOST);
		$strBoardPath = parse_url($this->config->get('cmsbridge_url'), PHP_URL_PATH);
		$arrDomains = explode('.', $strBoardURL);
		$arrDomainsReversed = array_reverse($arrDomains);
		if (count($arrDomainsReversed) > 1){
			$cookieDomain = '.'.$arrDomainsReversed[1].'.'.$arrDomainsReversed[0];
		} else {
			$cookieDomain = ($strBoardURL == 'localhost') ? '' : $strBoardURL;
		}
		
		setcookie($this->config->get('cmsbridge_sso_cookiename'), '', 0, $strBoardPath, $cookieDomain, $this->env->ssl);
	}
	
	public function sync($arrUserdata){
		if ($this->config->get('cmsbridge_disable_sync') == '1'){
			return false;
		}
		$sync_array = array(
			'icq' 			=> $arrUserdata['icq'],
			'town'			=> $arrUserdata['location'],
			'birthday'		=> $this->_handle_birthday($arrUserdata['birthdate']),
			'gender'		=> $arrUserdata['gender'],
		);
		return $sync_array;
	}
	
	public function sync_fields(){
		return array(
			'icq'		=> 'ICQ',
			'birthday'	=> 'Birthday',
			'town'		=> 'Town',
			'gender'	=> 'Gender',
		);
	}
	
	private function _handle_birthday($date){
		list($y, $m, $d) = explode('-', $date);
		if ($y != '' && $y != 0 && $m != '' && $m != 0 && $d != '' && $d != 0){
			return $this->time->mktime(0,0,0,$m,$d,$y);
		}
		return 0;
	}
	
}
?>