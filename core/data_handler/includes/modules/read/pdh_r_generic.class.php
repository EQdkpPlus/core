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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_generic')){
	abstract class pdh_r_generic extends gen_class {

		public $hooks			= array();
		public $presets			= array();

		//Language stuff
		public $default_lang	= 'english';

		public $module_lang		= array();
		public $preset_lang		= array();

		//those functions need to be implemented in our childs
		abstract function reset();
		abstract function init();

		public function get_hooks(){
			return $this->hooks;
		}

		public function init_lang($module_path){
			$lang_inc = $module_path.'/language/'.registry::fetch('user')->data['user_lang'].'.php';
			if(!is_file($lang_inc)){
				$lang_inc = $module_path.'/language/'.$this->default_lang.'.php';
			}
			if(is_file($lang_inc)){
				include($lang_inc);
				$this->module_lang = isset($module_lang) ? $module_lang : array();
				$this->preset_lang = isset($preset_lang) ? $preset_lang : array();
			}
		}
	}//end class
}//end if
?>