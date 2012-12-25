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
		$shortcuts = array('time');
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
			'callafter'		=> '',
		),
		'logout' 	=> '',
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
		'cmsbridge_disable_sync' => array(
			'fieldtype'	=> 'checkbox',
			'name'			=> 'cmsbridge_disable_sync',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = ''){
		if (sha1(utf8_encode(strtolower(utf8_decode($strUsername))).$password) == $hash){
			return true;
		}
		return false;
	}
	
	public function smf2_get_user_groups($intUserID, $arrGroups){
		$query = $this->db->query("SELECT id_group, additional_groups FROM ".$this->prefix."members WHERE id_member='".$this->db->escape($intUserID)."'");
		$result = $this->db->fetch_row($query);
		if (in_array((int)$result['id_group'], $arrGroups)) return true;
		$arrAditionalGroups = explode(',', $result['additional_groups']);
		if (is_array($arrAditionalGroups)){
			foreach ($arrAditionalGroups as $group){
				if (in_array((int)$group, $arrGroups)) return true;
			}
		}
		
		return false;
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
		return '';
	}
	
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_smf2_bridge',smf2_bridge::__shortcuts());
?>