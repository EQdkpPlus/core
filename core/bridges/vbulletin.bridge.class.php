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
 * @package		bridges
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class vbulletin_bridge extends bridge_generic {
	
	public $name = 'vBulletin';
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'usergroup', //without prefix
			'id'	=> 'usergroupid',
			'name'	=> 'title',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'FUNCTION'	=> 'vb_get_user_groups',
		),
		'user'	=> array( //User
			'table'	=> 'user',
			'id'	=> 'userid',
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
			'callafter'		=> '',
		),
		'logout' 	=> '',
		'autologin' => '',	
		'sync'		=> '',
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash){
		if ((md5(md5($password).$strSalt)) == $hash){
			return true;
		}

		return false;
	}
	
	public function vb_get_user_groups($intUserID, $arrGroups){
		$query = $this->db->query("SELECT usergroupid, membergroupids FROM ".$this->prefix."user WHERE userid='".$this->db->escape($intUserID)."'");
		$result = $this->db->fetch_row($query);
		if (in_array((int)$result['usergroupid'], $arrGroups)) return true;
		$arrAditionalGroups = explode(',', $result['membergroupids']);
		if (is_array($arrAditionalGroups)){
			foreach ($arrAditionalGroups as $group){
				if (in_array((int)$group, $arrGroups)) return true;
			}
		}
		
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_vbulletin_bridge',vbulletin_bridge::$shortcuts);
?>