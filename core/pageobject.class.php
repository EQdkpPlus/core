<?php


/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
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

if(!class_exists('pageobject')){
	class pageobject extends page_generic {
		private $arrVars = array();
		public $strPath = '';
		public $strPathPlain = '';
		public $strPage = '';
		
		protected function set_vars($arrVars){
			if (!isset($this->arrVars['display']) || $this->arrVars['display'] = false) $this->arrVars = $arrVars;
		}
		
		public function get_vars(){
			return $this->arrVars;
		}
		
		public function __construct($pre_check=false, $handler=false, $pdh_call=array(), $params=null, $cb_name='', $url_id='') {
			parent::__construct($pre_check, $handler, $pdh_call, $params, $cb_name, $url_id);
			if (registry::isset_const('url_id')){
				$this->set_url_id((($url_id != "") ? $url_id  : registry::get_const('url_id')), registry::get_const('url_id'));
			}
			
			//Build Path
			$strPath = $this->server_path;
			if (!intval($this->config->get('seo_remove_index'))) $strPath .= 'index.php/';
			$strPagePath = (strlen($this->page_path))  ? $this->page_path : $this->env->path;
			if (strpos($strPagePath, "/") === 0) $strPagePath = substr($strPagePath, 1);
			
			$strPath .= $strPagePath;
			
			$this->strPage = $this->page;
			$this->strPath = $strPath;
			$this->strPathPlain = str_replace($this->server_path, "", $strPath);
			
			$this->action = $strPath.$this->SID.$this->simple_head_url.$this->url_id_ext;
			$this->tpl->assign_vars(array(
				'ACTION'	=> $this->action,
				'PATH'		=> $strPath,
			));
		}
				
	}
}
?>