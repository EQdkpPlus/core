<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

class avatar extends gen_class {

	private $defaults = array(
		'chars' => 2,
		'fontSize' => 38,
		'foreground'   => '#FFFFFF',
	    	'backgrounds'   => [
			'#f44336',
			'#E91E63',
			'#9C27B0',
			'#673AB7',
			'#3F51B5',
			'#2196F3',
			'#03A9F4',
			'#00BCD4',
			'#009688',
			'#4CAF50',
			'#8BC34A',
			'#CDDC39',
			'#FFC107',
			'#FF9800',
			'#FF5722',
		    ],
	);

	public function getAvatar($intUserID, $strName, $intSize = 64){
		$strHash = $this->buildHash($intUserID, $strName);
		
		$strCachedImage = $this->getCachedImage($strHash, $intSize);
		if (!$strCachedImage){
			//Create
			$result = $this->cacheImage($strHash, $strName, $intSize);
			return $result;
		}
		return $strCachedImage;
	}

	private function buildHash($intUserID, $strName) {
		return md5($intUserID.'_'.utf8_strtoupper($strName));
	}

	public function deleteAvatar($intUserID, $strName, $intSize=64){
		$strHash = $this->buildHash($intUserID, $strName);
		$strCachedImage = $this->getCachedImage($strHash, $intSize);
		if($strCachedImage){
			$this->pfh->Delete($strCachedImage);
		}
	}

	public function getCachedImage($strHash, $intSize){
		$strImage = $strHash.'_'.$intSize.'.png';
		
		$strCacheFolder = $this->pfh->FolderPath('useravatar','eqdkp');
		$strRelativeFile = $strCacheFolder.$strImage;
		
		if (is_file($strRelativeFile)){
			return $strRelativeFile;
		}
		return false;
	}



	public function cacheImage($strHash, $strName, $intSize=64){
		$strInitials = $this->getInitials($strName);
		$strBackground = $this->getRandomBackground();
		$intFontSize = ($intSize/100) * $this->defaults['fontSize'];

		$image = imagecreatetruecolor ( $intSize , $intSize );	
		$arrColor = $this->hex2rgb($strBackground);
		$backgroundColor = imagecolorallocate($image, $arrColor[0], $arrColor[1], $arrColor[2]);
		imagefill($image, 0, 0, $backgroundColor);
		$fontColor = $this->hex2rgb($this->defaults['foreground']);
		$fontColorRes = ImageColorAllocate($image, $fontColor[0], $fontColor[1], $fontColor[2]);

		$fontfile = $this->root_path.'libraries/opensans/opensans-bold.ttf';
		
		$bbox = imagettfbbox($intFontSize, 0, $fontfile, $strInitials);
		$center1 = (imagesx($image) / 2) - (($bbox[2] - $bbox[0]) / 2)-$bbox[0];
		$y = ($intSize - ($bbox[1] - $bbox[7])) / 2; 
		$y -= $bbox[7]; 

		imagettftext($image, $intFontSize, 0, $center1, $y, $fontColorRes, $fontfile, $strInitials);
		
		$strImage = $strHash.'_'.$intSize.'.png';
		$strCacheFolder = $this->pfh->FolderPath('useravatar','eqdkp');
		$strRelativeFile = $strCacheFolder.$strImage;

		imagepng($image, $strRelativeFile, 0);

		if (is_file($strRelativeFile)) return $strRelativeFile;

		return false;
	}
	
	public function getInitials($strName){
		$strName = str_replace(array("\"",",",";",".",":","!","?", "&", "=", "/", "|", "#", "*", "+", "(", ")", "%", "$", "´", "„", "“", "‚", "‘", "`", "^", "[", "]", "-"), '', $strName);
		$arrWords = explode(' ', $strName);

		if(count($arrWords) > 1){
			$strInitial = "";
			foreach($arrWords as $strWord){
				$strInitial .= substr($strWord, 0, 1);
			}
			$strInitial = substr($strInitial, 0, $this->defaults['chars']);		
		} else {
			$strInitial = substr($strName, 0, $this->defaults['chars']);
		}

		return utf8_strtoupper($strInitial);
	}

	public function getRandomBackground(){
		$arrBackgrounds = $this->defaults['backgrounds'];
		$randKey = array_rand($arrBackgrounds);
		return $arrBackgrounds[$randKey];
	}

	public function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);
	
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb;
	}
}
?>