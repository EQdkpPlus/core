<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */
if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class admin_user extends install_generic {
	public static $shortcuts = array('pdl', 'in', 'user', 'db', 'time', 'config', 'crypt' => 'encrypt');
	public static $before 		= 'inst_settings';

	public $next_button		= 'create_user';
	
	//defaults
	private $username = '';
	private $useremail = '';
	
	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '<table width="100%" border="0" cellspacing="1" cellpadding="2">
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
		$this->useremail = $this->crypt->encrypt($this->useremail);
		$this->config->set('admin_email', $this->useremail);
		$salt = $this->user->generate_salt();
		$password = $this->user->encrypt_password($this->in->get('user_password1'), $salt);
		$this->db->query("TRUNCATE __users;");

		$this->db->query("INSERT INTO __users :params", array(
			'user_id'		=> 1,
			'username'		=> $this->username,
			'user_password'	=> $password.':'.$salt,
			'user_lang'		=> $this->config->get('default_lang'),
			'user_email'	=> $this->useremail,
			'user_active'	=> '1',
			'rules'			=> 1,
			'user_style'	=> 1,
			'user_registered' => $this->time->time,
			'exchange_key'	=> md5(rand().rand()),
		));
		$this->db->query("INSERT INTO __groups_users (group_id, user_id) VALUES (2,1);");
		$this->user->login($this->username, $this->in->get('user_password1'), $this->in->exists('auto_login'));
		return true;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_admin_user', admin_user::$shortcuts);
?>