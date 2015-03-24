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

class gravatar extends gen_class {

	public static $shortcuts = array('puf' => 'urlfetcher');
	
	private $url = 'https://secure.gravatar.com/avatar/%s?s=%d&r=g&d=%s';
	private $intCachingTime = 24; //hours
	
	public function getAvatar($strEmail, $intSize = 64, $blnIgnoreNotFound=false){
		$strHash = $this->buildHash($strEmail);
		
		$strCachedImage = $this->getCachedImage($strHash, $intSize);
		if (!$strCachedImage){
			//Download
			$result = $this->cacheImage($strHash, $intSize, $blnIgnoreNotFound);
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
	
	public function cacheImage($strHash, $intSize, $blnIgnoreNotFound=false){
		$strImage = $strHash.'_'.$intSize.'.jpg';
		$strCacheFolder = $this->pfh->FolderPath('gravatar','eqdkp');
		$strRelativeFile = $strCacheFolder.$strImage;
		
		if($blnIgnoreNotFound){
			$strAvatarURL = sprintf($this->url, $strHash, $intSize, 'identicon');
			$result = $this->puf->fetch($strAvatarURL);
			if($result){
				$this->pfh->putContent($strRelativeFile, $result);
				if (is_file($strRelativeFile)) return $strRelativeFile;
			}
		} else {
			$strAvatarURL = sprintf($this->url, $strHash, $intSize, '404');
			$result = $this->puf->fetch($strAvatarURL);
			if($result &&  trim($result) != "404 Not Found"){
				$this->pfh->putContent($strRelativeFile, $result);
				if (is_file($strRelativeFile)) return $strRelativeFile;
			}
		}
		
		return false;
	}
	
	private function buildHash($strEmail){
		return md5(strtolower(trim($strEmail)));
	}

}
?>