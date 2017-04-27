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

class discord_bridge extends bridge_generic {

	public static $name = 'Discord';

	public $blnSyncEmail = false;

	private $arrGroups = array();
	private $arrUsers = array();
	private $guild = "";
	private $token = "";

	public $data = array(
			//Data
			'groups' => array( //Where I find the Usergroup
					'FUNCTION' => 'get_groups'
			),
			'user_group' => array( //Zuordnung User zu Gruppe
					'FUNCTION' => 'get_user_groups'
			),
			'user'	=> array( //User
					'FUNCTION' => 'get_user',
			),
			'users'	=> array( //User
					'FUNCTION' => 'get_users',
			),
			/*
			 'check_user_group'	=> array( //User
			 		'FUNCTION' => 'get_check_user_group',
			 ),
	*/
	);	
	
	public function __construct($objBridgeDB, $strPrefix){
		parent::__construct($objBridgeDB, $strPrefix);
		
		$arrDiscordConfig = $this->config->get_config('discord');
		$this->guild = $arrDiscordConfig['guild_id'];
		$this->token = $arrDiscordConfig['bot_token'];
	}



	public $settings = array(
	);

	public function get_groups($blnWithID){
		if (count($this->arrGroups)) return $this->arrGroups;

		$arrOut = array('0' => 'Everyone');
		$result = register('urlfetcher')->fetch('https://discordapp.com/api/guilds/'.$this->guild.'/roles', array('Authorization: Bot '.$this->token));
		if($result){
			$arrJSON = json_decode($result, true);
			if($arrJSON && is_array($arrJSON)){
				foreach($arrJSON as $val){
					if( $val['name'] == '@everyone') continue;
					$arrOut[$val['id']] = $val['name'];
				}
			}
		}
		$this->arrGroups = $arrOut;
		return $arrOut;
	}

	public function get_user_groups($intUserID){
		$arrOut = array('0');
		$result = register('urlfetcher')->fetch('https://discordapp.com/api/guilds/'.$this->guild.'/members', array('Authorization: Bot '.$this->token));
		if($result){
			$arrJSON = json_decode($result, true);
			if($arrJSON && is_array($arrJSON)){
				foreach($arrJSON as $val){
					if ($val['user']['id'] == $intUserID){
						$arrOut = $val['roles'];
					}
				}
			}
		}

		return $arrOut;
	}

	public function get_user($strUsername){
		$arrUsers = $this->get_users();
		foreach($arrUsers as $userID => $username){
			if($strUsername == $username){
				return array(
						'id'		=> $userID,
						'name'		=> $strUsername,
						'password' 	=> md5(rand()),
						'email'		=> md5(rand()),
						'salt'		=> '',
				);
			}
		}
		return false;
	}

	public function get_users(){
		if (count($this->arrUsers)) return $this->arrUsers;
		$arrOut = array();
		$result = register('urlfetcher')->fetch('https://discordapp.com/api/guilds/'.$this->guild.'/members', array('Authorization: Bot '.$this->token));
		if($result){
			$arrJSON = json_decode($result, true);
			if($arrJSON && is_array($arrJSON)){
				foreach($arrJSON as $val){
					$user = $val['user'];
					$arrOut[$user['id']] = $user['username'];
				}
			}
		}
		$this->arrUsers = $arrOut;
		return $arrOut;
	}

	public function check_password($password, $hash, $strSalt = '', $boolUseHash = false, $strUsername = "", $arrUserdata=array()){
		return false;
	}

	public function login($strUsername, $strPassword, $boolSetAutologin=false, $boolUseHash=false){
		//$strUsername = email address of user
		$arrData = array('email' => $strUsername, 'password' => $strPassword);

		$result = register('urlfetcher')->post('https://discordapp.com/api/auth/login', json_encode($arrData), 'application/json; charset=utf-8');
		if($result){
			$arrJSON = json_decode($result, true);
			if($arrJSON && is_array($arrJSON)){
				$token = $arrJSON['token'];

				$result = register('urlfetcher')->fetch('https://discordapp.com/api/users/@me', array('Authorization: '.$token));
				if($result){
					$arrJSON = json_decode($result, true);
					if($arrJSON['id']){
						if(!$arrJSON['username'] || $arrJSON['username'] == "") {
							$arrMailParts = explode("@", $strUsername);
							$arrJSON['username'] = $arrMailParts[0];
							if($arrJSON['username'] == ""){						
								return array(
									'status' => false,
								);
							}
						}
						return array(
								'status' 	=> true,
								'id'		=> $arrJSON['id'],
								'name'		=> $arrJSON['username'],
								'password' 	=> $strPassword,
								'email'		=> $strUsername,
								'salt'		=> $strSalt,
						);
					}
				}
			}
		}


		return array(
				'status' => false,
		);
	}

}

?>
