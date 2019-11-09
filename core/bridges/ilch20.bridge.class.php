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

class ilch20_bridge extends bridge_generic {

	public static $name = "ilch 2.0";

	public $data = array(
			'user'	=> array(
				'table'	=> 'users',
				'id'	=> 'id',
				'name'	=> 'name',
				'where'	=> 'name',
				'password' => 'password',
				'email'	=> 'email',
				'salt'	=> '',
				'QUERY'	=> '',
			),
			'groups' => array(
				'table'	=> 'groups', //without prefix
				'id'	=> 'id',
				'name'	=> 'name',
				'QUERY'	=> '',
			),
			'user_group' => array(
				'table'	=> 'users_groups',
				'group'	=> 'group_id',
				'user'	=> 'user_id',
				'QUERY'	=> '',
			),
	);

	//Needed function
	public function check_password($password, $hash, $strSalt = '', $strUsername = "", $arrUserdata=array()){
		$blnResult = crypt($password, $hash) === $hash;

		return $blnResult;
	}

}
