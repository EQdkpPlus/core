<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_user')){
	class exchange_user extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();


		public function get_user($params, $arrBody){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();
			if ($isAPITokenRequest){
				$intUserID = (int)$params['get']['user_id'];

				$arrUserIDs = $this->pdh->get('user', 'id_list', array(false));
				if(!in_array($intUserID, $arrUserIDs)) return $this->pex->error('user not found');


				$arrUserdata = $this->pdh->get('user', 'data', array($intUserID));
				$arrUserdata['email'] = $this->pdh->get('user', 'email', array($intUserID));
				$hideArray = array('user_password', 'user_login_key', 'user_email','user_email_confirmkey', 'user_lastpage', 'privacy_settings', 'auth_account', 'notifications', 'user_temp_email', 'salt', 'password', 'exchange_key', 'failed_login_attempts');
				foreach($hideArray as $entry){
					if(isset($arrUserdata[$entry])) unset($arrUserdata[$entry]);
				}
				$arrUserdata['custom_fields'] = unserialize_noclasses($arrUserdata['custom_fields']);
				$arrUserdata['plugin_settings'] = unserialize_noclasses($arrUserdata['plugin_settings']);
				$arrUserdata['usergroups'] = $this->pdh->get('user_groups_users', 'memberships', array($intUserID));
				$arrUserdata['avatar_big'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($intUserID, true)));
				$arrUserdata['avatar_small'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($intUserID, false)));

				return array('data' => $arrUserdata);
			} else {
				return $this->pex->error('access denied');
			}



		}

		public function post_user($params, $arrBody){
			$isAPITokenRequest = $this->pex->isApiWriteTokenRequest();
			if ($isAPITokenRequest){

				if (!isset($arrBody['username']) || !strlen($arrBody['username'])) return $this->pex->error('required data missing', 'username');
				if (!isset($arrBody['password']) || !strlen($arrBody['password'])) return $this->pex->error('required data missing', 'password');
				if (!isset($arrBody['email']) || !strlen($arrBody['email'])) return $this->pex->error('required data missing', 'email');

				$strUsername = $arrBody['username'];
				$strPassword = $arrBody['password'];
				$strEmail = $arrBody['email'];
				$intRules = (isset($arrBody['rules'])) ? (int)$arrBody['rules'] : 0;

				$intUserId = $this->pdh->put('user', 'insert_user_bridge', array($strUsername, $strPassword, $strEmail, $intRules));

				$this->pdh->process_hook_queue();

				if($intUserId){
					return array('user_id' => $intUserId);
				} else {
					return $this->pex->error('an error occured');
				}

			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
