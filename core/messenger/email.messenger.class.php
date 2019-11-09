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

class email_messenger extends generic_messenger {
	public static $shortcuts = array('email'=>'MyMailer');

	public function sendMessage($toUserID, $strSubject, $strMessage){
		$blnResult = false;
		$strEmailAdress = $this->pdh->get('user', 'email', array($toUserID, true));
		if (preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$strEmailAdress)){
			$this->email->Set_Language($this->pdh->get('user', 'lang', array($toUserID)));
			$options = array(
					'template_type'		=> 'input',
			);
			//Set E-Mail-Options
			$this->email->SetOptions($options);
			$blnResult = $this->email->SendMailFromAdmin($strEmailAdress, $strSubject, $strMessage);

		}
		return $blnResult;
	}

	public function isAvailable(){
		return true;
	}
}
