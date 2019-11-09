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

class twitterdm_messenger extends generic_messenger {

	public function sendMessage($toUserID, $strSubject, $strMessage){
		$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($toUserID));
		$twitterAccount = str_replace("@", "", $arrNotificationSettings['ntfy_twitter_user']);
		if($twitterAccount  == "") return false;

		include_once($this->root_path.'libraries/twitter/codebird.class.php');
		Codebird::setConsumerKey($this->config->get('twitter_consumer_key'), $this->config->get('twitter_consumer_secret')); // static, see 'Using multiple Codebird instances'

		$cb = Codebird::getInstance();
		$cb->setToken($this->config->get('twitter_access_token'), $this->config->get('twitter_access_token_secret'));

		$strMessage = strip_tags($strMessage);

		//$strMessage = substr($strMessage, 0, 160);

		$params = array(
			'screen_name' => $twitterAccount,
			'text'		  => $strMessage,
		);

		$reply = $cb->directMessages_new($params);

		return true;
	}

	public function isAvailable(){
		if($this->config->get('twitter_consumer_key') != ""
				&& $this->config->get('twitter_consumer_secret') != ""
				&& $this->config->get('twitter_access_token') != ""
				&& $this->config->get('twitter_screen_name') != ""
				&& $this->config->get('twitter_access_token_secret') != ""){
			return true;
		}

		return false;
	}

	/*
	 * @see generic_notification::getUserSettings()
	 */
	public function getUserSettings(){
		return array(
			'ntfy_twitter_user' => array(
					'type' => 'text',
					'size'	=> 40,
					'dir_help'	=> str_replace("{TWITTER}", $this->config->get('twitter_screen_name'), $this->user->lang('user_sett_f_help_ntfy_twitter_user')),
			),
		);
	}

	/*
	 * @see generic_notification::getAdminSettings()
	 */
	public function getAdminSettings(){
		return array(
				'twitter_screen_name' => array(
						'type' => 'text',
						'size'	=> 40
				),
				'twitter_consumer_key' => array(
						'type' => 'text',
						'size'	=> 40
				),
				'twitter_consumer_secret' => array(
						'type' => 'text',
						'size'	=> 40
				),
				'twitter_access_token' => array(
						'type' => 'text',
						'size'	=> 40
				),
				'twitter_access_token_secret' => array(
						'type' => 'text',
						'size'	=> 40
				),
		);
	}
}
