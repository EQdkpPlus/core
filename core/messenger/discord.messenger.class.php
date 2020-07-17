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

class discord_messenger extends generic_messenger {

	public function sendMessage($toUserID, $strSubject, $strMessage){
		if(!$this->isAvailable()) return false;

		$intUserID = $toUserID;
		$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));
		$channelid = $arrNotificationSettings['ntfy_discord_channelid'];
		if($channelid  == "") return false;

		$arrDiscordConfig = register('config')->get_config('discord');
		$token = $arrDiscordConfig['bot_token'];

		$discordChannelID = false;
		$blnIsGroupChannel = false;
		if(strpos($channelid, ':') !== false){
			//Its an direct channel
			$arrList = explode(':', $channelid);
			if($arrList[0] == 'channel' || $arrList[0] == 'chan'){
				$discordChannelID = $arrList[1];
				$blnIsGroupChannel = true;
			}
		} else {
			//Its an user, lets create the DM channel
			$arrJsonData = array(
					'recipient_id' => $channelid,
			);

			$strResult = register('urlfetcher')->post('https://discord.com/api/users/@me/channels', json_encode($arrJsonData), "application/json", array('Authorization: Bot '.$token));
			if($strResult){
				$arrResultJson = json_decode($strResult, true);
				if($arrResultJson && isset($arrResultJson['id'])){
					$discordChannelID = $arrResultJson['id'];
				}
			}
		}

		//Now post the real message
		if($discordChannelID){
			$strMessage = strip_tags($strMessage);
			$msg = '**'.$strSubject."**\r\n```".$strMessage."```";

			$arrJsonData = array('content' => $msg);
			$b = register('urlfetcher')->post('https://discord.com/api/channels/'.$discordChannelID.'/messages', json_encode($arrJsonData), "application/json", array('Authorization: Bot '.$token));
		}

		return true;
	}

	public function isAvailable(){
		$arrDiscordConfig = register('config')->get_config('discord');
		$token = $arrDiscordConfig['bot_token'];

		return ($this->pm->check('discord', PLUGIN_INSTALLED) && $token != "");
	}

	/*
	 * @see generic_notification::getUserSettings()
	 */
	public function getUserSettings(){
		return array(
				'ntfy_discord_channelid' => array(
						'type' => 'text',
						'size'	=> 40,
				),
		);
	}

	/*
	 * @see generic_notification::getAdminSettings()
	 */
	public function getAdminSettings(){
		return array();
	}
}
