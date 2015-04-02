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

class e107_bridge extends bridge_generic {
		
	public static $name = "e107";
	
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
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		if (md5($password) == $hash){
			return true;
		}
		return false;
	}
	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['user_ban'] != '0') {
				return false;
			}
			
			return true;
		}
		return false;
	}
	
	public function e107_get_user_groups($intUserID){
		$objQuery = $this->bridgedb->prepare("SELECT user_class,user_admin FROM ".$this->prefix."user WHERE user_id=?")->execute($intUserID);
		if ($objQuery){
			$arrResult = $objQuery->fetchAssoc();
			$arrAditionalGroups = explode(',', $arrResult['user_class']);
			if (is_array($arrAditionalGroups)){
				return $arrAditionalGroups;
			}
		}
		
		return array();
	}
	
}
?>