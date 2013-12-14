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

class ipb3_bridge extends bridge_generic {
	
	public static $name = "IPB 3";
	
	public $data = array(
		'user'	=> array( //User
			'table'	=> 'members',
			'id'	=> 'member_id',
			'name'	=> 'name',
			'where'	=> 'name',
			'password' => 'members_pass_hash',
			'email'	=> 'email',
			'salt'	=> 'members_pass_salt',
			'QUERY'	=> '',
		),
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'groups',
			'id'	=> 'g_id',
			'name'	=> 'g_title',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'QUERY'	=> '',
			'FUNCTION'	=> 'ipb3_get_user_groups',
		),
		
	);
	
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'ipb3_callafter',
		),
		'logout' 	=> '',
		'autologin' => '',	
		'sync'		=> '',
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash){
		
		$password = md5( md5($strSalt) . md5( $password ) );
		
		return ($password === $hash) ? true : false;
	}
	
	public function ipb3_get_user_groups($intUserID){
		$query = $this->db->prepare("SELECT member_group_id, mgroup_others FROM ".$this->prefix."members WHERE member_id=?")->execute($intUserID);
		$arrReturn = array();
		if ($query){
			$result = $query->fetchAssoc();
			$arrReturn[] = (int)$result['member_group_id'];
			$arrAditionalGroups = explode(',', $result['mgroup_others']);
			if (is_array($arrAditionalGroups)){
				foreach ($arrAditionalGroups as $group){
					if ($group != '') $arrReturn[] = (int)$group;
				}
			}
		}		
		
		return $arrReturn;
	}
	
	public function ipb3_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['temp_ban'] != '0' || $arrUserdata['member_banned'] != '0') {
				return false;
			}
		}
		return true;
	}
	
}
?>