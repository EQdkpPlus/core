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

class ipb4_bridge extends bridge_generic {

	public static $name = "IPB 4";

	public $data = array(
		'user'	=> array( //User
			'table'	=> 'members',
			'id'	=> 'member_id',
			'name'	=> 'name',
			'where'	=> 'name',
			'password' => 'members_pass_hash',
			'email'	=> 'email',
			'salt'	=> 'members_pass_salt',
			'QUERY'	=> '',
		),
		'groups' => array( //Where I find the Usergroup
			'table'	=> 'groups',
			'id'	=> 'g_id',
			'name'	=> 'g_title',
			'QUERY'	=> '',
			'FUNCTION' => 'ipb4_get_groups'
		),
		'user_group' => array( //Zuordnung User zu Gruppe
			'QUERY'	=> '',
			'FUNCTION'	=> 'ipb4_get_user_groups',
		),

	);

	//Needed function
	public function check_password($password, $hash, $strSalt = '', $strUsername = "", $arrUserdata=array()){
		if ($strSalt == NULL) {
			/* IPB 4.4+ */
			return password_verify($password, $hash);

		} elseif ( strlen( $strSalt ) === 22 )
		/* New password style introduced in IPS4 using Blowfish */
		{
			$strUserPassword = crypt( $password, '$2a$13$' . $strSalt );
			return ($strUserPassword === $hash) ? true : false;
		}
		/* Old encryption style using md5 */
		else
		{
			$password = md5( md5($strSalt) . md5( $password ) );
			return ($password === $hash) ? true : false;
		}

		return;
	}

	public function ipb4_get_groups($blnWithID){
		$arrGroups = array();
		$query = $this->bridgedb->query("SELECT * FROM ".$this->prefix."groups");
		if ($query){
			$objGroupNameQuery = $this->bridgedb->query("SELECT * FROM ".$this->prefix."sys_lang_words WHERE word_key LIKE 'core_group_%'");
			if($objGroupNameQuery){
				while($row = $objGroupNameQuery->fetchAssoc()){
					$arrGroupNames[$row['word_key']] = ($row['word_custom'] != "") ? $row['word_custom'] : $row['word_default'];
				}
			}

			while($row = $query->fetchAssoc()){
				$arrGroups[$row['g_id']] = $arrGroupNames['core_group_'.$row['g_id']].(($blnWithID) ? ' (#'.$row['g_id'].')': '');
			}

		}

		return $arrGroups;
	}

	public function ipb4_get_user_groups($intUserID){
		$query = $this->bridgedb->prepare("SELECT member_group_id, mgroup_others FROM ".$this->prefix."members WHERE member_id=?")->execute($intUserID);
		$arrReturn = array();
		if ($query){
			$result = $query->fetchAssoc();
			$arrReturn[] = (int)$result['member_group_id'];
			$arrAditionalGroups = explode(',', $result['mgroup_others']);
			if (is_array($arrAditionalGroups)){
				foreach ($arrAditionalGroups as $group){
					if ($group != '') $arrReturn[] = (int)$group;
				}
			}
		}

		return $arrReturn;
	}

	public function after_login($strUsername, $strPassword, $boolSetAutoLogin, $arrUserdata, $boolLoginResult){
		//Is user active?
		if ($boolLoginResult){
			if ($arrUserdata['temp_ban'] != '0') {
				return false;
			}

			return true;
		}
		return false;
	}

}
