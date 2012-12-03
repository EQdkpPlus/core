<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

	class logs extends gen_class {
		public static $shortcuts = array('pm', 'pdh', 'user', 'time');

		public $pluginname	= 'core';
		public $plugins		= array();

		public function __construct(){
			//Create Plugin List
			$this->plugins[] = 'core';
			$this->plugins[] = 'calendar';
			foreach ($this->pm->get_plugins() as $key){
				$this->plugins[] = $key;
			}
		}

		public function ChangePlugin($name){
			$this->pluginname 	= $name;
		}

		public function add($tag, $value, $admin_action=true, $plugin='', $result=1, $userid = false, $process_hooks=1){
			$plugin = ($plugin != '') ? $plugin : $this->pluginname;
			$this->pdh->put('logs', 'add_log', array($tag, $value, $admin_action, $plugin, $result, $userid));
			if($process_hooks) $this->pdh->process_hook_queue();
		}

		// Language Replacement
		public function lang_replace($variable){
			preg_match("/\{L_(.+)\}/", $variable, $to_replace);
			if ( (isset($to_replace[1])) && ($this->user->lang(strtolower($to_replace[1])))){
				$variable = str_replace('{L_'.$to_replace[1].'}', $this->user->lang(strtolower($to_replace[1])), $variable);
			}
			preg_match("/\{LA_(.+)\[(.+)\]\}/", $variable, $to_replace);
			if ( (isset($to_replace[1])) && ($this->user->lang(strtolower($to_replace[1])))){
				$variable = str_replace($to_replace[0], $this->user->lang(array(strtolower($to_replace[1]), strtolower($to_replace[2]))), $variable);
			}
			preg_match("/\{D_(.+)\}/", $variable, $to_replace);
			if ( (isset($to_replace[1]))){
				$variable = str_replace('{D_'.$to_replace[1].'}', $this->time->user_date($to_replace[1], true), $variable);
			}
			return $variable;
		}
	}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_logs', logs::$shortcuts);
?>