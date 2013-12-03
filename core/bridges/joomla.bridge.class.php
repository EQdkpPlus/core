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

class joomla_bridge extends bridge_generic {
	
	public $name = "Joomla";
	
	public $data = array(
		//Data
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'usergroups', //without prefix
			'id'	=> 'id',
			'name'	=> 'title',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'table'	=> 'user_usergroup_map',
			'group'	=> 'group_id',
			'user'	=> 'user_id',
			'QUERY'	=> '',
		),
		'user'	=> array( //User
			'table'	=> 'users',
			'id'	=> 'id',
			'name'	=> 'username',
			'where'	=> 'username',
			'password' => 'password',
			'email'	=> 'email',
			'salt'	=> '',
			'QUERY'	=> '',
		),
	);
	
	public $functions = array(
		'login'	=> array(
			'callbefore'	=> '',
			'function' 		=> '',
			'callafter'		=> 'joomla_callafter',
		),
		'logout' 	=> '',
		'autologin' => '',	
		'sync'		=> '',
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash){
		
		//Bcrypt
		if (substr($hash, 0, 4) == '$2a$' || substr($hash, 0, 4) == '$2y$')
		{
			if (!function_exists("crypt")) return false;
			
			return (crypt($password, $hash) === $hash);
		}
	
		// Check if the hash is an MD5 hash.
		if (substr($hash, 0, 3) == '$1$')
		{
			if (!function_exists("crypt")) return false;
			
			return (crypt($password, $hash) === $hash);
		}

		// Check if the hash is a Joomla hash.
		if (preg_match('#[a-z0-9]{32}:[A-Za-z0-9]{32}#', $hash) === 1)
		{
			return md5($password . substr($hash, 33)) == substr($hash, 0, 32);
		}

		return false;

	}
	
	public function joomla_callafter($strUsername, $strPassword, $boolAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['block'] != '0') {
				return false;
			}
		}
		return true;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_joomla_bridge', joomla_bridge::$shortcuts);
?>