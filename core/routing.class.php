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
		public static $shortcuts = array('core', 'config', 'pdh', 'user');
		
		private $_cache = array();
		
		private $arrStaticRoutes = array(
			'settings'		=> 'settings',
			'login'			=> 'login',
			'mycharacters'	=> 'mycharacters',
			'search'		=> 'search',
			'register'		=> 'register',
			'wrapper'		=> 'wrapper',
			'addcharacter'	=> 'addcharacter',
			'editarticle'	=> 'editarticle',
			'user'			=> 'user',
			'usergroup'		=> 'usergroup'
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
		
		public function get($strPageObject){
			$strPageObject = utf8_strtolower($strPageObject);
			if (isset($this->_cache[$strPageObject])) return $this->_cache[$strPageObject];
			
			//Check static route
			if ($this->staticRoute($strPageObject)){
				$this->_cache[$strPageObject] = $this->staticRoute($strPageObject);
				return $this->_cache[$strPageObject];
			}
			
			$arrArticleIDs = $this->pdh->get('articles', 'articles_for_pageobject', array($strPageObject));
			if ($arrArticleIDs && is_array($arrArticleIDs) && count($arrArticleIDs)){
				foreach($arrArticleIDs as $intArticleID){
					$intCategoryID = $this->pdh->get('articles', 'category', array($intArticleID));
					$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
					if($arrPermissions && $arrPermissions['read']) {
						$this->_cache[$strPageObject] = $this->pdh->get('articles', 'plain_path', array($intArticleID));
						return $this->_cache[$strPageObject];
					}
				}
				
				//No Permission, get first one
				$intArticleID = $arrArticleIDs[0];
				$intCategoryID = $this->pdh->get('articles', 'category', array($intArticleID));
				$this->_cache[$strPageObject] = $this->pdh->get('articles', 'plain_path', array($intArticleID));
				return $this->_cache[$strPageObject];
			}
			$this->_cache[$strPageObject] = 'NotFound';
			return 'NotFound';
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
		
		public function build($strPageObject, $strParamText=false, $strParam=false, $blnAddSID=true){
			$strPath = $this->controller_path;
			$strPath .= ucfirst($this->get($strPageObject)).'/';
			if ($strParam) $strPath .= $this->clean($strParamText).'-'.$strParam.((intval($this->config->get('seo_html_extension'))) ? '.html' : '/');
			if ($blnAddSID) $strPath .= $this->SID;
			return $strPath;
		}
		
		public function clean($strText){
			$strText = utf8_strtolower($strText);
			$strText = str_replace(' ', '-', $strText);
			$strText = preg_replace("/[^a-zA-Z0-9_-]/","",$strText);
			return ucfirst($strText);
		}
		
	}
}
?>