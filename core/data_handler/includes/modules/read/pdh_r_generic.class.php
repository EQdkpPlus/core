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