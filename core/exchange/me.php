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

if (!class_exists('exchange_me')){
	class exchange_me extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');

		public function get_me($params, $arrBody){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();

			if($isAPITokenRequest) {
				return $this->pex->error('api token request');
			}

			$status = 0;
			$data = array();
			if($this->user->is_signedin() && $this->user->id > 0){
				$strUsername = $this->pdh->get('user', 'name', array($this->user->id));
				if(!$strUsername || $strUsername == ''){
					return $this->pex->error('access denied');
				}

				$arrUserdata = $this->pdh->get('user', 'data', array($this->user->id));
				$arrUserdata['email'] = $this->pdh->get('user', 'email', array($this->user->id));
				$hideArray = array('user_password', 'user_login_key', 'user_email','user_email_confirmkey', 'user_lastpage', 'privacy_settings', 'auth_account', 'notifications', 'user_temp_email', 'salt', 'password', 'exchange_key', 'failed_login_attempts');
				foreach($hideArray as $entry){
					if(isset($arrUserdata[$entry])) unset($arrUserdata[$entry]);
				}
				$arrUserdata['custom_fields'] = unserialize_noclasses($arrUserdata['custom_fields']);
				$arrUserdata['plugin_settings'] = unserialize_noclasses($arrUserdata['plugin_settings']);
				$arrUserdata['usergroups'] = $this->pdh->get('user_groups_users', 'memberships', array($this->user->id));
				$arrUserdata['avatar_big'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($this->user->id, true)));
				$arrUserdata['avatar_small'] = $this->env->httpHost.$this->env->root_to_serverpath($this->pdh->get('user', 'avatarimglink', array($this->user->id, false)));


				return array('data' => $arrUserdata);
			} else {
				return $this->pex->error('access denied');
			}
		}

	}
}
