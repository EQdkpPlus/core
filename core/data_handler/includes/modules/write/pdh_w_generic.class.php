<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_generic')){
	class pdh_w_generic extends gen_class {
		public static $shortcuts = array('logs', 'time');
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

		public function log_insert($tag, $values, $admin_action=true, $plugin='', $userid=false){
			return $this->logs->add($tag, $values, $admin_action, $plugin, 1, $userid, 0);
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_generic', pdh_w_generic::$shortcuts);
?>