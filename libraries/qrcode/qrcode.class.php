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

class qrcode extends gen_class {
		
	public function image($strData, $intSize=140){
		$strImage = $this->url($strData, $intSize);
		
		return '<img src="'.$strImage.'" height="'.$intSize.'" />';
	}
	
	public function url($strData, $intSize=140){
		if (version_compare(PHP_VERSION, '7.1.0') >= 0) {
			$objQRCode = $this->includeQRLib();
			
			$strImage = $objQRCode->render($strData);
		} else {
			$strImage =  "https://chart.googleapis.com/chart?chs=".$intSize."x".$intSize."&chld=S|0&cht=qr&chl=".$strData;
		}
		
		return $strImage;
	}
	
	private function includeQRLib(){
		include_once('Settings/SettingsContainerInterface.php');
		include_once('Settings/SettingsContainerAbstract.php');
		include_once('QRCode/QRCode.php');
		include_once('QRCode/QROptions.php');
		include_once('QRCode/QROptions.php');

		return new \chillerlan\QRCode\QRCode;
	}
}