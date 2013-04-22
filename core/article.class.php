<?php


/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
* -----------------------------------------------------------------------
* @author		$Author: wallenium $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 12937 $
*
* $Id: page_generic.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('article')){
	class article extends page_generic {
		public static function __shortcuts() {
			$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core', 'html');
			return array_merge(parent::$shortcuts, $shortcuts);
		}
		
		private $arrVars = array();
		public $strPath = '';
		
		protected function set_vars($arrVars){
			if (!isset($this->arrVars['display']) || $this->arrVars['display'] = false) $this->arrVars = $arrVars;
		}
		
		public function get_vars(){
			return $this->arrVars;
		}
		
		public function __construct($pre_check, $handler=false, $pdh_call=array(), $params=null, $cb_name='', $url_id='') {
			parent::__construct($pre_check, $handler, $pdh_call, $params, $cb_name, $url_id);
			if (registry::isset_const('url_id')){
				$this->set_url_id(registry::get_const('url_id'));
			}
		}
		
		public function set_path($strPath){
		
		}
		
	}
}
?>