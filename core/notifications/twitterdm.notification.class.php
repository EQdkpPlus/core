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

class twitterdm_notification extends generic_notification {

	public function sendNotification($arrNotificationData){
		if(!$this->isAvailable()) return false;

		$intUserID = $arrNotificationData['to_userid'];
		$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));
		$twitterAccount = str_replace("@", "", $arrNotificationSettings['ntfy_twitter_user']);
		if($twitterAccount  == "") return false;

		$strMessage = substr($arrNotificationData['name'], 0, 110).'... '.$arrNotificationData['link'];
		$this->messenger->sendMessage('twitterdm', $intUserID, $strSubject, $strMessage);
	}

	public function isAvailable(){
		return $this->messenger->isAvailable('twitterdm');
	}

	/*
	 * @see generic_notification::getUserSettings()
	 */
	public function getUserSettings(){
		return $this->messenger->getMethodUserSettings('twitterdm');
	}

	/*
	 * @see generic_notification::getAdminSettings()
	 */
	public function getAdminSettings(){
		return $this->messenger->getMethodAdminSettings('twitterdm');
	}
}
