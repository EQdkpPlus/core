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

class vbulletin51_bridge extends bridge_generic {
	
	public static $name = 'vBulletin 5.1';
	
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
			'password' => 'token',
			'email'	=> 'email',
			'salt'	=> 'secret',
			'QUERY'	=> '',
		),
	);
	
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		if($arrUserdata['scheme'] == 'legacy'){
			list($storedHash, $storedSalt) = explode(' ', $hash);
			return ($storedHash == md5(md5($password) . $storedSalt));
			
		} elseif(strpos($arrUserdata['scheme'], 'blowfish') !== false){
			return (crypt(md5($password), $hash) == $hash);
		}

		return false;
	}
	
	public function vb_get_user_groups($intUserID){
		$query = $this->bridgedb->prepare("SELECT usergroupid, membergroupids FROM ".$this->prefix."user WHERE userid=?")->execute($intUserID);
		$arrReturn = array();
		if ($query){
			$result = $query->fetchAssoc();
			
			$arrReturn[] = (int)$result['usergroupid'];
			
			$arrAditionalGroups = explode(',', $result['membergroupids']);
			if (is_array($arrAditionalGroups)){
				foreach ($arrAditionalGroups as $group){
					$arrReturn[] = (int)$group;
				}
			}
		}
		
		return $arrReturn;
	}
}
?>