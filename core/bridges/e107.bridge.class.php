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

class e107_bridge extends bridge_generic {
		
	public $name = "e107";
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'userclass_classes', //without prefix
			'id'	=> 'userclass_id',
			'name'	=> 'userclass_name',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'QUERY'	=> '',
			'FUNCTION'	=> 'e107_get_user_groups',
		),
		'user'	=> array( //User
			'table'	=> 'user',
			'id'	=> 'user_id',
			'name'	=> 'user_loginname',
			'where'	=> 'user_loginname',
			'password' => 'user_password',
			'email'	=> 'user_email',
			'salt'	=> '',
			'QUERY'	=> '',
		),
	);
	
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'e107_callafter',
		),
		'logout' 	=> '',
		'autologin' => '',	
		'sync'		=> '',
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = ''){
		if (md5($password) == $hash){
			return true;
		}
		return false;
	}
	
	public function e107_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['user_ban'] != '0') {
				return false;
			}
		}
		return true;
	}
	
	public function e107_get_user_groups($intUserID, $arrGroups){
		$query = $this->db->query("SELECT user_class,user_admin FROM ".$this->prefix."user WHERE user_id='".$this->db->escape($intUserID)."'");
		$result = $this->db->fetch_row($query);
		if ((int)$result['user_admin'] == 1) return true;
		$arrAditionalGroups = explode(',', $result['user_class']);
		if (is_array($arrAditionalGroups)){
			foreach ($arrAditionalGroups as $group){
				if (in_array((int)$group, $arrGroups)) return true;
			}
		}
		
		return false;
	}
	
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_e107_bridge', e107_bridge::$shortcuts);
?>