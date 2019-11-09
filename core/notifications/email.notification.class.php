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

class email_notification extends generic_notification {
	public static $shortcuts = array('email'=>'MyMailer');

	public function sendNotification($arrNotificationData){
		$strEmailAdress = $this->pdh->get('user', 'email', array($arrNotificationData['to_userid'], true));
		$strMailSubject = $this->user->lang('new_notification', false, false, $this->pdh->get('user', 'lang', array($arrNotificationData['to_userid']))).': '.$arrNotificationData['type_lang'];
		$arrBodyvars = array(
			'USERNAME' 		=> $arrNotificationData['to_username'],
			'LINK'			=> $arrNotificationData['link'],
			'SETTINGS_LINK'	=> $this->env->buildlink().$this->routing->build('Settings', false, false, false, true).'#fragment-notifications',
			'MESSAGE'		=> $arrNotificationData['name'],
		);
		$this->email->Set_Language($this->pdh->get('user', 'lang', array($arrNotificationData['to_userid'])));
		$this->email->SendMailFromAdmin($strEmailAdress, $strMailSubject, 'notification.html', $arrBodyvars, $this->config->get('lib_email_method'));
	}

	public function isAvailable(){
		return true;
	}
}
