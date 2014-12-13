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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class admin_user extends install_generic {
	public static $before 		= 'inst_settings';

	public $next_button		= 'create_user';
	
	//defaults
	private $username = '';
	private $useremail = '';
	
	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="no-borders">
						<tr>
							<td align="right"><strong>'.$this->lang['username'].':</strong></td>
							<td align="left"><input type="text" name="username" value="'.$this->username.'" class="input" /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['user_email'].':</strong></td>
							<td align="left"><input type="text" name="user_email" value="'.$this->useremail.'" class="input" size="30"  /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['user_password'].':</strong></td>
							<td align="left"><input type="password" name="user_password1" value="" class="input" /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['user_pw_confirm'].':</strong></td>
							<td align="left"><input type="password" name="user_password2" value="" class="input" /></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['auto_login'].':</strong></td>
							<td><input type="checkbox" name="auto_login" value="1" /></td>
						</tr>
					</table>';
		return $content;
	}
	
	public function get_filled_output() {
		return $this->get_output();
	}
	
	public function parse_input() {
		$this->username = $this->in->get('username');
		$this->useremail = $this->in->get('user_email');
		if($this->in->get('user_password1') == '' || empty($this->username) || empty($this->useremail)) {
			$this->pdl->log('install_error', $this->lang['user_required']);
			return false;
		}
		if($this->in->get('user_password1') != $this->in->get('user_password2')) {
			$this->pdl->log('install_error', $this->lang['no_pw_match']);
			return false;
		}

		$strEmail =  $this->encrypt->encrypt($this->useremail);
		$this->config->set('admin_email', $strEmail);
		$salt = $this->user->generate_salt();
		$password = $this->user->encrypt_password($this->in->get('user_password1'), $salt);
		$this->db->query("TRUNCATE __users;");
		
		$this->db->prepare("INSERT INTO __users :p")->set(array(
			'user_id'		=> 1,
			'username'		=> $this->username,
			'user_password'	=> $password.':'.$salt,
			'user_lang'		=> $this->config->get('default_lang'),
			'user_email'	=> $strEmail,
			'user_active'	=> '1',
			'rules'			=> 1,
			'user_style'	=> 1,
			'user_registered' => $this->time->time,
			'exchange_key'	=> md5(generateRandomBytes()),
			'user_timezone' => $this->config->get('timezone'),
			'user_date_time' => $this->config->get('default_date_time'),
			'user_date_short' => $this->config->get('default_date_short'),
			'user_date_long' => $this->config->get('default_date_long'),
		))->execute();

		$this->db->query("INSERT INTO __groups_users (group_id, user_id, grpleader) VALUES (2,1,1);");
		$this->user->login($this->username, $this->in->get('user_password1'), $this->in->exists('auto_login'));
		return true;
	}
}
?>