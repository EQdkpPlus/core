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

class ipb3_bridge extends bridge {
	
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