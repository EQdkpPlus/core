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

if(!class_exists('routing')){
	class routing extends gen_class {
		private $_cache = array();
		
		private $arrStaticRoutes = array(
			'settings'		=> 'settings',
			'login'			=> 'login',
			'mycharacters'	=> 'mycharacters',
			'search'		=> 'search',
			'register'		=> 'register',
			'addcharacter'	=> 'addcharacter',
			'editarticle'	=> 'editarticle',
			'user'			=> 'user',
			'usergroup'		=> 'usergroup',
			'rss'			=> 'rss',
			'external'		=> 'wrapper',
			'tag'			=> 'tag',
				
			//Static Pages for Calendar
			'editcalendarevent' => 'editcalendarevent',
			'calendareventtransform' => 'calendareventtransform',
			'calendareventexport' => 	'calendareventexport',
			'calendareventguests'=> 'calendareventguests',
		);
		
		private $arrStaticLocations = array();
		
		public function addRoute($strRoutename, $strPageObject, $strPageObjectPath){
			$this->arrStaticRoutes[strtolower($strRoutename)] = strtolower($strPageObject);
			$this->arrStaticLocations[strtolower($strPageObject)] = $strPageObjectPath;
		}
		
		public function getRoutes(){
			return $this->arrStaticRoutes;
		}
		
		public function staticRoute($strPath, $blnWithAlias=false){
			$strPath = utf8_strtolower($strPath);
			if (isset($this->arrStaticRoutes[$strPath])){
				return (($blnWithAlias) ? $strPath : $this->arrStaticRoutes[$strPath]);
			}
			return false;
		}
		
		public function get($strPageObject, $blnWithAlias=false){
			$strPageObject = utf8_strtolower($strPageObject);
			if (isset($this->_cache[$strPageObject])) return $this->_cache[$strPageObject];
			
			//Check static route
			if ($this->staticRoute($strPageObject)){
				$this->_cache[$strPageObject] = $this->staticRoute($strPageObject, $blnWithAlias);
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
			$arrFiles = sdir( $this->root_path.'core/pageobjects/', '*_pageobject.class.php');
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
		
		public function build($arrPageObject, $strParamText=false, $strParam=false, $blnAddSID=true, $blnControllerPathPlain = false, $blnAddExtension=true){
			$strPath = ($blnControllerPathPlain) ? $this->controller_path_plain : $this->controller_path;
			if (is_array($arrPageObject)){
				foreach($arrPageObject as $key => $strPageObject){
					if ($key == 0){
						$strPath .= ucfirst($this->get($strPageObject, true));
					} else {
						$strPath .= '/'.$strPageObject;
					}
				}
			} else $strPath .= ucfirst($this->get($arrPageObject, true));
			
			if ($strParamText || $strParam) $strPath .= '/';
			if ($strParamText) $strPath .= $this->clean($strParamText);
			if ($strParam) $strPath .= '-'.$strParam;
			if(!$blnAddExtension) return $strPath;
			
			$lastChar = substr($strPath, -1, 1);
			if ($lastChar != "/"){
				$strPath .= $this->getSeoExtension();

				if ($blnAddSID) $strPath .= $this->SID;
			}
			return $strPath;
		}
		
		public function simpleBuild($strPageObject){
			$strPath = $this->controller_path;
			$strPath .= ucfirst($this->get($strPageObject, true));
			if (substr($strPath, -1, 1) != "/") $strPath .= '/';
			return $strPath;
		}
		
		public function getSeoExtension(){
			switch((int)$this->config->get('seo_extension')){
				case 1:
					return '.html';
					break;
				case 2:
					return '.php';
					break;
				default: return '/';
			}
		}
		
		public function clean($strText){
			$strText = utf8_strtolower($strText);
			$strText = str_replace(' ', '-', $strText);
			$a_satzzeichen = array("\"",",",";",".",":","!","?", "&", "=", "/", "|", "#", "*", "+", "(", ")", "%", "$", "´", "„", "“", "‚", "‘", "`", "^");
			$strText = str_replace($a_satzzeichen, "", $strText);
			return ucfirst($strText);
		}
		
		public function getPageObject($strObjectName){
			include_once($this->root_path.'core/pageobject.class.php');
			if (isset($this->arrStaticLocations[$strObjectName])){
				$strLocation = $this->arrStaticLocations[$strObjectName];
			} else {
				$strLocation = 'core/pageobjects';
			}
			if (is_file($this->root_path.$strLocation.'/'.$strObjectName.'_pageobject.class.php')){
				include_once($this->root_path.$strLocation.'/'.$strObjectName.'_pageobject.class.php');
				$objPage = registry::register($strObjectName.'_pageobject');
				return $objPage;
			}
			return false;
		}
		
	}
}
?>