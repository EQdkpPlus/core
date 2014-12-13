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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_generic')){
	class pdh_w_generic extends gen_class {
		public $admin_user			= '';		// Username of admin		@var admin_user
		public $current_time		= 0;		// Current time				@var time

		public function __construct(){
			$user = registry::fetch('user');
			$this->admin_user = (isset($user->data['user_id']) && $user->data['user_id'] != ANONYMOUS ) ? $user->data['username'] : '';
			$this->current_time = $this->time->time;
		}

		public function gen_group_key($time, $parts) {
			$time = htmlspecialchars(stripslashes($time));
			$time = substr(md5($time), 0, 10);
			foreach($parts as $key => $part){
				$parts[$key] = htmlspecialchars(stripslashes($part));
				$parts[$key] = substr(md5($parts[$key]), 0, 11);
			}
			$group_key = $time.implode('', $parts);
			$group_key = md5(uniqid($group_key));
			return $group_key;
		}

		public function log_insert($tag, $values, $record_id, $record='', $admin_action=true, $plugin='', $userid = false){
			return $this->logs->add($tag, $values, $record_id, $record, $admin_action, $plugin, 1, $userid, 0);
		}
	}//end class
}//end if
?>