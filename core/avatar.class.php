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
	public static $shortcuts = array('puf'=>'urlfetcher');

	private $defaults = array(
		'chars' => 2,
		'fontSize' => 38,
	);

	public function getAvatar($intUserID, $strName, $intSize = 64){
		$strHash = $this->buildHash($intUserID, $strName);

		$strCachedImage = $this->getCachedImage($strHash, $intSize);
		if (!$strCachedImage){
			//Create
			$result = $this->cacheImage($intUserID, $strHash, $strName, $intSize);
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



	public function cacheImage($intUserID, $strHash, $strName, $intSize=64){
		$strInitials = $this->getInitials($strName);

		$strBackground = $this->getBackground($intUserID, $strName);

		$perceptiveLuminance = $this->getPerceptiveLuminance(
				hexdec($strBackground[0] . $strBackground[1]),
				hexdec($strBackground[2] . $strBackground[3]),
				hexdec($strBackground[4] . $strBackground[5])
				);

		$textColor = ($perceptiveLuminance < 0.3) ? '#000000' : '#FFFFFF';

		$intFontSize = ($intSize/100) * $this->defaults['fontSize'];

		$image = imagecreatetruecolor ( $intSize , $intSize );
		$arrColor = $this->hex2rgb($strBackground);
		$backgroundColor = imagecolorallocate($image, $arrColor[0], $arrColor[1], $arrColor[2]);
		imagefill($image, 0, 0, $backgroundColor);
		$fontColor = $this->hex2rgb($textColor);
		$fontColorRes = ImageColorAllocate($image, $fontColor[0], $fontColor[1], $fontColor[2]);
		$fontfile = realpath($this->root_path.'libraries/opensans/opensans-bold.ttf');

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
				$strInitial .= utf8_substr($strWord, 0, 1);
			}
			$strInitial = utf8_substr($strInitial, 0, $this->defaults['chars']);
		} else {
			$strInitial = utf8_substr($strName, 0, $this->defaults['chars']);
		}

		return utf8_strtoupper($strInitial);
	}

	public function getBackground($intUserID, $strUsername){
		$backgroundColor = substr(sha1($intUserID.'_'.$strUsername), 0, 6);
		return $backgroundColor;
	}

	public function downloadExternalAvatar($intUserID, $strURL){
		$arrImageExtensions = array('jpg', 'png', 'gif', 'jpeg');
		//If its an dynamic image...
		$path_parts = pathinfo($strURL);
		if (!in_array(strtolower($path_parts['extension']), $arrImageExtensions)){
			return false;
		}

		// Load it...
		$tmp_name = md5(generateRandomBytes());
		$strImageCacheFolder = $this->pfh->FolderPath('images', 'eqdkp');
		$this->pfh->CheckCreateFile($strImageCacheFolder.$tmp_name);

		$strImageContent = $this->puf->fetch($strURL);
		if(!$strImageContent) return false;

		$this->pfh->putContent($strImageCacheFolder.$tmp_name, $strImageContent);
		$i = getimagesize($strImageCacheFolder.$tmp_name);

		// Image is no image, lets remove it
		if (!$i) {
			$this->pfh->Delete($strImageCacheFolder.$tmp_name);
			return false;
		}

		$strNewFilename = md5(generateRandomBytes()).'.'.$path_parts['extension'];

		$myFileName = $this->pfh->FolderPath('users/'.$intUserID, 'files').$strNewFilename;

		$this->pfh->rename($strImageCacheFolder.$tmp_name, $myFileName);

		//Create Thumbnail
		$this->pfh->thumbnail($myFileName, $this->pfh->FolderPath('users/thumbs','files'), 'useravatar_'.$intUserID.'_68.'.pathinfo($strNewFilename, PATHINFO_EXTENSION), 68, 68);

		return $strNewFilename;
	}

	private function hex2rgb($hex) {
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

	private function getPerceptiveLuminance($r, $g, $b) {
		return 1 - (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
	}
}
