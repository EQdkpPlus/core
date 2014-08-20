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

class smf2_bridge extends bridge_generic {
	
	public static function __shortcuts() {
		$shortcuts = array('time', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public $name = "SMF 2";
	
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
	
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'smf2_callafter',
		),
		'logout' 	=> 'smf2_logout',
		'autologin' => '',	
		'sync'		=> 'smf2_sync',
	);
	
	public $sync_fields = array(
		'icq',
		'birthday',
		'msn',
		'user_email',
		'username',
		'town',
		'gender',
	);
	
	public $settings = array(
		'cmsbridge_disable_sso'	=> array(
			'fieldtype'	=> 'checkbox',
			'name'		=> 'cmsbridge_disable_sso',
		),
		'cmsbridge_disable_sync' => array(
			'fieldtype'	=> 'checkbox',
			'name'			=> 'cmsbridge_disable_sync',
		),
		'cmsbridge_sso_cookiename'	=> array(
			'fieldtype'	=> 'text',
			'name'		=> 'cmsbridge_sso_cookiename',
		),
		'cmsbridge_sso_cookiedomain' => array(
				'fieldtype'	=> 'text',
				'name'		=> 'cmsbridge_sso_cookiedomain',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = ''){
		//Use normal strtolower and not utf8_strotolower, because SMF2 does the same...
		if (sha1(strtolower($strUsername).$password) == $hash){
			return true;
		}
		return false;
	}
	
	public function smf2_get_user_groups($intUserID, $arrGroups){
		$query = $this->db->query("SELECT id_group, id_post_group, additional_groups FROM ".$this->prefix."members WHERE id_member='".$this->db->escape($intUserID)."'");
		$result = $this->db->fetch_row($query);
		if (in_array((int)$result['id_group'], $arrGroups)) return true;
		if (in_array((int)$result['id_post_group'], $arrGroups)) return true;
		$arrAditionalGroups = explode(',', $result['additional_groups']);
		if (is_array($arrAditionalGroups)){
			foreach ($arrAditionalGroups as $group){
				if (in_array((int)$group, $arrGroups)) return true;
			}
		}
		
		return false;
	}
	
	public function smf2_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
			//Single Sign On
			if ($this->config->get('cmsbridge_disable_sso') != '1'){
				$this->smf2_sso($arrUserdata, $strPassword, $boolAutoLogin);
			}
		}
		return true;
	}
	
	public function smf2_sso($arrUserdata, $strPassword, $boolAutoLogin = false){
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
	
	public function smf2_logout() {
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
	
	public function smf2_sync($arrUserdata){
		if ($this->config->get('cmsbridge_disable_sync') == '1'){
			return false;
		}
		$sync_array = array(
			'icq' 			=> $arrUserdata['icq'],
			'town'			=> $arrUserdata['location'],
			'birthday'		=> $this->_handle_birthday($arrUserdata['birthdate']),
			'msn'			=> $arrUserdata['msn'],
			'gender'		=> $arrUserdata['gender'],
		);
		return $sync_array;
	}
	
	private function _handle_birthday($date){
		list($y, $m, $d) = explode('-', $date);
		if ($y != '' && $y != 0 && $m != '' && $m != 0 && $d != '' && $d != 0){
			return $this->time->mktime(0,0,0,$m,$d,$y);
		}
		return 0;
	}
	
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_smf2_bridge',smf2_bridge::__shortcuts());
?>