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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_get_salt')){
	class exchange_get_salt extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');

		public function post_get_salt($params, $body){
			$xml = simplexml_load_string($body);
			if ($xml && $xml->user){

				$objQuery = $this->db->prepare("SELECT user_password FROM __users WHERE LOWER(username)=? AND user_active='1'")->limit(1)->execute(clean_username($xml->user));
				if ($objQuery && $objQuery->numRows){
					$row = $objQuery->fetchAssoc();
					
					if (strpos($row['user_password'], ':') !== false){
						list($user_password, $user_salt) = explode(':', $row['user_password']);
						$out = array(
							'salt'	=> base64_encode($user_salt),
						);
						return $out;
					}
				} 

				return $this->pex->error('user not found');
			}
		}
	}
}
?>