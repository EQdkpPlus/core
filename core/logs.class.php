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

	class logs extends gen_class {

		public $pluginname	= 'core';
		public $plugins		= array();

		public function __construct(){
			//Create Plugin List
			$this->plugins[] = 'core';
			$this->plugins[] = 'calendar';
			$this->plugins[] = 'article';
			$this->plugins[] = 'article_category';
			foreach ($this->pm->get_plugins() as $key){
				$this->plugins[] = $key;
			}
		}

		public function ChangePlugin($name){
			$this->pluginname 	= $name;
		}

		public function add($tag, $value, $record_id = '', $record = '',  $admin_action=true, $plugin='', $result=1, $userid = false, $process_hooks=1){
			$plugin = ($plugin != '') ? $plugin : $this->pluginname;
			$this->pdh->put('logs', 'add_log', array($tag, $value, $record_id, $record, $admin_action, $plugin, $result, $userid));
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
		
		public function option_lang($option){
			return ($option == 1) ? '{L_OPTION_TRUE}' : '{L_OPTION_FALSE}';
		}
		
		/*
		 * $arrOld = array(1,2,3)
		 * $arrNew = array(4,5,6)
		 * $arrLang = array("1", "2", "3")
		 * $arrFlags = array(0,0,1)
		 */
		public function diff($arrOld, $arrNew, $arrLang, $arrFlags=array(), $blnOnlyNewKeys=false){
			$arrChanged = array();
			if ($arrOld && !$blnOnlyNewKeys){
				foreach($arrOld as $key => $val){
					if ($arrNew[$key] != $val){
						$arrChanged[$arrLang[$key]] = array('old' => $val, 'new' => $arrNew[$key], 'flag' => ((isset($arrFlags[$key])) ? $arrFlags[$key] : 0)); 
					}
				}
			} elseif($arrOld && $blnOnlyNewKeys){
				foreach($arrNew as $key => $val){
					if ($arrOld[$key] != $val){
						$arrChanged[$arrLang[$key]] = array('old' => $arrOld[$key], 'new' => $val, 'flag' => ((isset($arrFlags[$key])) ? $arrFlags[$key] : 0));
					}
				}
			} else {
				foreach($arrNew as $key => $val){
					if (isset($arrLang[$key])) $arrChanged[$arrLang[$key]] = $arrNew[$key];
				}	
			}
			
			return (count($arrChanged)) ? $arrChanged : false;
		}
		
	}
?>