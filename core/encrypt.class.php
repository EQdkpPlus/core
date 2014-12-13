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
	header('HTTP/1.0 404 Not Found'); exit;
}

class encrypt extends gen_class {
	private $resMycrypt;
	private $strKeyLength;
	private $strEncryptionKey;
		
	public function __construct($strEncryptionKey = ''){
		include_once($this->root_path.'libraries/aes/AES.class.php');
		
		if ($strEncryptionKey == '' && registry::get_const('encryptionKey') == ''){
			$this->core->message('Encryption Key is missing. Please take a look at our Wiki.', $this->user->lang('error'), 'red');
			$this->strEncryptionKey = '';
		} else {		
			$this->strEncryptionKey = ($strEncryptionKey != '') ? $strEncryptionKey : registry::get_const('encryptionKey');
			$this->strEncryptionKey = md5($this->strEncryptionKey);		
		}
	}
	
	public function encrypt($strValue){
		if ($strValue == '' || $this->strEncryptionKey == '') return '';
		
		$strEncrypted = AesCtr::encrypt($strValue, $this->strEncryptionKey, 256);
		return $strEncrypted;

	}
	
	public function decrypt($strValue){
		if ($strValue == '' || $this->strEncryptionKey == '') return '';
		
		$strDecrypted = AesCtr::decrypt($strValue, $this->strEncryptionKey, 256);
		return $strDecrypted;

	}

} //END mmocms_encrypt-class
?>