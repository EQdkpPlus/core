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

class eqdkp_bridge extends bridge_generic {
	
	public static $name = "EQdkp 2.0";
	
	public $data = array(
		'user'	=> array( //User
			'table'	=> 'users',
			'id'	=> 'user_id',
			'name'	=> 'username',
			'where'	=> 'username',
			'password' => 'user_password',
			'email'	=> 'user_email',
			'salt'	=> '',
			'QUERY'	=> '',
		),
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'groups_user', //without prefix
			'id'	=> 'groups_user_id',
			'name'	=> 'groups_user_name',
			'QUERY'	=> '',
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'table'	=> 'groups_users',
			'group'	=> 'group_id',
			'user'	=> 'user_id',
			'QUERY'	=> '',
		),
		
	);

	
	public $settings = array(
		'cmsbridge_disable_sync' => array(
			'type'	=> 'radio',
		),
	);
	
	public $blnSyncEmail = false;
		
	//Needed function
	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		$blnResult = $this->user->checkPassword($password, $hash, $boolUseHash);
		return $blnResult;
	}
	
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['active'] == '0') {
				return false;
			}
			
			return true;
		}
		return false;
	}
	
	public function sync($arrUserdata){
		if ($this->config->get('cmsbridge_disable_sync') == '1'){
			return false;
		}
		$sync_array = array();
		
		$custom_fields = $arrUserdata['custom_fields'];
		$arrFields = unserialize($custom_fields);
		foreach($arrFields as $key => $val){
			$key = str_replace('userprofile_', '', $key);
			$sync_array[$key] = $val;
		}
		
		return $sync_array;
	}
	
	public function sync_fields(){
		$query = $this->bridgedb->prepare("SELECT * FROM ".$this->prefix."user_profilefields WHERE enabled=1")->execute();
		$arrFields = array();
		if ($query){
			while($row = $query->fetchAssoc()){
				$arrFields[$row['id']] = $row['name'];
			}
		}
		
		return $arrFields;
	}
}
?>