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

/**
 * Bridge Generic. Is abstract class, all Bridge have to extend this class.
 * @author GodMod
 */
abstract class bridge_generic extends gen_class {
	/**
	 * @var array Contains Information about Tables to get the information
	 */
	public $data					= array();

	/**
	 * @var array Contains Bridge-specific Settings, like cookie domain etc.
	 */
	public $settings				= array();
	
	/**
	 * @var boolean Set true if email adress should be synced from CMS
	 */
	public $blnSyncEmail		= true;
	/**
	 * @var boolean Set true if Birthdy should be synced from CMS
	 */
	public $blnSyncBirthday		= false;
	/**
	 * @var boolean Set true if Country should be synced from CMS
	 */
	public $blnSyncCountry		= false;
	
	/**
	 * @var object Database Connection to CMS
	 */
	protected  $bridgedb				= false;
	/**
	 * @var string Prefix of CMS
	 */
	protected  $prefix			= '';

	
	/**
	 * Constructor
	 * 
	 * @param object $objBridgeDB
	 * @param string $strPrefix
	 */
	public function __construct($objBridgeDB, $strPrefix) {
		$this->bridgedb = $objBridgeDB;
		$this->prefix = $strPrefix;
	}
	
	/**
	 * Check if Password matches with saved Hash
	 * 
	 * @param string $password	Password
	 * @param string $hash		Saved Passwordhash
	 * @param string $strSalt	Saved Salt
	 * @param boolean $boolUseHash	if $password is a hash or not
	 * @param string $strUsername	Username
	 * @return boolean true if password matches
	 */
	abstract public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array());
	
	/**
	 * Returns Array with fields that are available to sync
	 * Key = fieldid of CMS
	 * Value = Name of Field
	 * 
	 * @return array Available Fields for Synching
	 */
	public function sync_fields() {
		return array();
	}

	/**
	 * Returns Array with synched Userdata. 
	 * Please return all data, the Bridge Class handles the matching to the profilefields.
	 * If you want to sync birthday and country, add key "birthday" and "country"
	 * Key = fieldid of CMS
	 * Value = fieldvalue
	 * 
	 * @param array $arrUserdata Userdata of CMS
	 * @return boolean/array Return false if sync is disabled, otherwise return array with synced data
	 */
	public function sync($arrUserdata){
		return false;
	}
	
	/**
	 * Autologin. Log User into EQdkp Using Session Information from CMS
	 * 
	 * @param array $arrCookiedata Cookiedata of EQdkp Autologin Cookie
	 * @return boolean/array Return false if autologin failed, otherwise array with EQdkp Userdata
	 */
	public function autologin($arrCookiedata){
		return false;
	}
	
	/**
	 * Logout. Should be used to Logout User from CMS
	 * 
	 * @return boolean
	 */
	public function logout(){
		return true;
	}
	
	/**
	 * Fired before login. No return value, no influence to login.
	 * 
	 * @param string $strUsername
	 * @param string $strPassword
	 * @param boolean $boolSetAutologin
	 * @param boolean $boolUseHash
	 */
	public function before_login($strUsername, $strPassword, $boolSetAutologin=false, $boolUseHash=false){
		return;
	}
	
	/**
	 * Do some checks after User Passwordchecks. 
	 * Returning false prevents user from being logged into EQdkp.
	 * 
	 * @param string $strUsername
	 * @param string $strPassword
	 * @param boolean $boolSetAutoLogin
	 * @param array $arrUserdata
	 * @param boolean $boolLoginResult
	 * @param string $boolUseHash
	 * @return boolean True if user should be logged in, false if not
	 */
	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash=false){
		return $boolLoginResult;
	}
	
	/**
	 * Login method. Should only be overwritten if default login of Bridge is not enough for you.
	 * 
	 * @param string $strUsername
	 * @param string $strPassword
	 * @param boolean $boolSetAutologin
	 * @param boolean $boolUseHash
	 * @return boolean Return 0 is default login should be used. Otherwise return a boolean for the login status.
	 */
	public function login($strUsername, $strPassword, $boolSetAutologin=false, $boolUseHash=false){
		return 0;
	}
}
?>