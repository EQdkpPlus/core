<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12937 $
 * 
 * $Id: bridge_usersync_crontask.class.php 12937 2013-01-29 16:35:08Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "bridge_usersync_crontask" ) ) {
	class bridge_usersync_crontask extends crontask {
		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['delay']		= false;
			$this->defaults['ajax']			= true;
			$this->defaults['description']	= 'Bridge User Sync';
		}

		public function run(){
			$a = $this->bridge->get_users();
			$arrUser = array();
			foreach($a as $val){
				$id = intval($val['id']);
				if ($this->bridge->check_user_group($id)){
					$arrUser[] = $val;
				}
			}
		
			foreach($arrUser as $arrUserdata){
				if ($this->pdh->get('user', 'check_username', array($arrUserdata['name'])) != 'false'){
					//Neu anlegen
					$salt = $this->user->generate_salt();
					$strPassword = random_string(false, 32);
					$strPwdHash = $this->user->encrypt_password($strPassword, $salt);
					$strApiKey = $this->user->generate_apikey($strPassword, $salt);
					
					$user_id = $this->pdh->put('user', 'insert_user_bridge', array($arrUserdata['name'], $strPwdHash.':'.$salt, $arrUserdata['email'], false, $strApiKey));
					$this->pdh->process_hook_queue();
					//Sync Usergroups
					$this->bridge->sync_usergroups((int)$arrUserdata['id'], $user_id);
				}
			}
		}
	}
}
?>