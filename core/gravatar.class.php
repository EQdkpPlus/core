<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12937 $
 * 
 * $Id: hooks.class.php 12937 2013-01-29 16:35:08Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
} 

class gravatar extends gen_class {

	public static $shortcuts = array('puf' => 'urlfetcher', 'pfh');
	
	private $url = 'http://gravatar.com/avatar/%s?s=%d&r=g&d=%s';
	private $intCachingTime = 24; //hours
	
	public function getAvatar($strEmail, $intSize = 64){
		$strHash = $this->buildHash($strEmail);
		
		$strCachedImage = $this->getCachedImage($strHash, $intSize);
		if (!$strCachedImage){
			//Download
			$result = $this->cacheImage($strHash, $intSize);
			return $result;
		}
		return $strCachedImage;
	}
	
	public function getCachedImage($strHash, $intSize){
		$strImage = $strHash.'_'.$intSize.'.jpg';
		
		$strCacheFolder = $this->pfh->FolderPath('gravatar','eqdkp');
		$strRelativeFile = $strCacheFolder.$strImage;
		
		if (is_file($strRelativeFile)){
			//Check Cachetime
			if ((filemtime($strRelativeFile)+(3600*$this->intCachingTime)) > time()) return $strRelativeFile;
		}
		return false;
	}
	
	public function cacheImage($strHash, $intSize){
		$strImage = $strHash.'_'.$intSize.'.jpg';
		$strCacheFolder = $this->pfh->FolderPath('gravatar','eqdkp');
		$strRelativeFile = $strCacheFolder.$strImage;
		
		$strAvatarURL = sprintf($this->url, $strHash, $intSize, '404');
		$result = $this->puf->fetch($strAvatarURL);
		if($result &&  trim($result) != "404 Not Found"){
			$this->pfh->putContent($strRelativeFile, $result);
			if (is_file($strRelativeFile)) return $strRelativeFile;
		}
		return false;
	}
	
	private function buildHash($strEmail){
		return md5(strtolower(trim($strEmail)));
	}

}
?>