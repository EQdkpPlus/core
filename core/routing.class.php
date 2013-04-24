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

if(!class_exists('routing')){
	class routing extends gen_class {		
		public static $shortcuts = array('core', 'config');
		
		private $arrStaticRoutes = array(
			'settings'		=> 'settings',
			'login'			=> 'login',
			'mycharacters'	=> 'mycharacters',
			'search'		=> 'search',
			'register'		=> 'register',
			'wrapper'		=> 'wrapper',
			'addcharacter'	=> 'addcharacter',
		);
		
		public function addRoute($strRoutename, $strPageObject, $strPageObjectPath){
		
		}
		
		public function getRoutes(){
			return $this->arrStaticRoutes;
		}
		
		public function staticRoute($strPath){
			$strPath = utf8_strtolower($strPath);
			if (isset($this->arrStaticRoutes[$strPath])){
				return $this->arrStaticRoutes[$strPath];
			}
			return false;
		}
		//ToDo: Finish
		public function buildRoute($strPath, $strPageObject, $intID=false, $strIDParam=false, $strText=false){
			$strRoute = $this->server_path;
			if (!intval($this->config->get('seo_remove_index'))) $strRoute .= 'index.php/';
			$strRoute .= $strPath;
			
		}
		
		//ToDo: Finish
		public function buildRoutePrefix($intID, $strText, $intIDParam=false){
			
		}
		
		public function getPageObjects($blnIncludeStatic = false){
			$arrFiles = sdir( $this->root_path.'core/page_objects/', '*_pageobject.class.php');
			if (is_array($arrFiles) && count($arrFiles)){
				$arrOut = array();
				foreach($arrFiles as $strFilename) {
					$strObjectName = str_replace('_pageobject.class.php', '', $strFilename);
					if (!$blnIncludeStatic && in_array($strObjectName, $this->arrStaticRoutes)) continue;
					$arrOut[$strObjectName] = ucfirst($strObjectName);
				}
				return $arrOut;
			}
			return array();
		}
		
	}
}
?>