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

if (!class_exists('exchange_check_session')){
	class exchange_check_session extends gen_class {

		public function post_check_session($params, $body){
			$xml = simplexml_load_string($body);
			$status = 0;
			$data = array();
			if ($xml && $xml->sid){
				$result = $this->user->check_session($xml->sid);
				if ($result != ANONYMOUS){
					$status = 1;
					$arrUserdata = $this->pdh->get('user', 'data', array($result));
					$arrUserdata['email'] = $this->pdh->get('user', 'email', array($result));
					$hideArray = array('user_password', 'user_login_key', 'user_email','user_email_confirmkey', 'user_lastpage', 'privacy_settings', 'auth_account', 'notifications', 'user_temp_email', 'salt', 'password', 'exchange_key');
					foreach($hideArray as $entry){
						if(isset($arrUserdata[$entry])) unset($arrUserdata[$entry]);
					}
					$arrUserdata['custom_fields'] = unserialize($arrUserdata['custom_fields']);
					$arrUserdata['plugin_settings'] = unserialize($arrUserdata['plugin_settings']);
					$arrUserdata['usergroups'] = $this->pdh->get('user_groups_users', 'memberships', array($result));
					$data = $arrUserdata;
					
				} else {
					$status = 0;
				}
			}
			return array('valid' => $result, 'data' =>$data);
		}

	}
}
?>