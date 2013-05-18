<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class maintenance_user extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'core', 'config', 'html', 'db', 'time', 'env', 'timekeeper'=>'timekeeper', 'email'=>'MyMailer', 'crypt'=>'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'create' => array('process' => 'submit', 'csrf'=>true),
			'email_submit' => array('process' => 'mail', 'csrf'=>true),
			'renew' => array('process' => 'renew', 'csrf'=>true),
		);
		$this->user->check_auth('a_');
		parent::__construct(false, $handler);
		$this->process();
	}

	public function submit() {
		$valid_until = $this->time->time + ($this->in->get('valid_until_days', 0) * 86400);
		$salt = $this->user->generate_salt();
		$password = random_string();
		$encr_password = $this->user->encrypt_password($password, $salt).':'.$salt;
		
		$arrUser = array(
			'username'	=> $this->user->lang('maintenanceuser_user'),
			'user_password' => $encr_password,
			'user_active'	=> 1,
			'rules'			=> 1,
			'user_email'	=> $this->crypt->encrypt('maintenance@local.com'),
		);
		
		$user_id = $this->pdh->put('user', 'insert_user', array($arrUser));
		
		$user_data = array(
			'user_id'	=> $user_id,
			'password'	=> $password,
			'valid_until' => $valid_until,
		);
		$this->pdh->put('user_groups_users', 'add_user_to_group', array($user_id, 2));
		$this->config->set('maintenance_user', $this->crypt->encrypt(serialize($user_data)));
		
		$special_users = unserialize(stripslashes($this->config->get('special_user')));
		$special_users[$user_id] = $user_id;
		$this->config->set('special_user', serialize($special_users));
		$this->timekeeper->add_cron('maintenanceuser', array('repeat_type' => 'daily', 'repeat_interval' => $this->in->get('valid_until_days', 0), 'active' => true), true);
	}
	
	public function renew(){
		$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));
		if ($muser_config != ''){
			$user_data = unserialize(stripslashes($muser_config));
			$user_data['valid_until'] = $user_data['valid_until'] + 7*86400;
			$days_to_end = ceil(($user_data['valid_until'] - $this->time->time) / 86400);
			$this->config->set('maintenance_user', $this->crypt->encrypt(serialize($user_data)));
			$this->timekeeper->add_cron('maintenanceuser', array('repeat_type' => 'daily', 'repeat_interval' => $days_to_end, 'active' => true), true);
			$this->core->message($this->user->lang('maintenanceuser_renew_suc'), $this->user->lang('success'), 'green');
		}
	}
	
	public function delete() {
		$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));
		if ($muser_config != ''){
			$muser = unserialize(stripslashes($muser_config));
			$this->db->query("DELETE FROM __users WHERE user_id = ".$muser['user_id']);

			$this->pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
			$this->config->set('maintenance_user', '');
		
			$special_users = unserialize(stripslashes($this->config->get('special_user')));
			unset($special_users[$muser['user_id']]);
			$this->config->set('special_user', serialize($special_users));
		}
		
		$this->timekeeper->del_cron('maintenanceuser');
		
		$this->display();
	}
	
	public function mail() {
		$user_active = ($this->config->get('maintenance_user') != "") ? true : false;
		
		if ($user_active){
			$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));
			$muser = unserialize(stripslashes($muser_config));
			$query = $this->db->query("SELECT * FROM __users WHERE user_id = ".$muser['user_id']);
			while ($row = $this->db->fetch_record($query)){
				$user_data = $row;
			}
		}

		$bodyvars = array(
					'USERNAME'		=> $user_data['username'],
					'PASSWORD'		=> $muser['password'],
					'VALID'			=> $this->time->user_date($muser['valid_until'], true),
					'EQDKP_URL'		=> $this->env->link,
					'GUILD'			=> $this->config->get('guildtag'),

		);
		
		if ($this->in->get('email') == $this->in->get('email_repeat')){
			if($this->email->SendMailFromAdmin($this->in->get('email'), sprintf($this->user->lang('maintenanceuser_mail_subject'), $this->config->get('guildtag')), 'maintenance_user.html', $bodyvars, $this->config->get('lib_email_method'))){
					$this->core->message( $this->user->lang('maintenanceuser_mail_success'), '','green');
				}else{
					$this->core->message( $this->user->lang('maintenanceuser_mail_error'), $this->user->lang('pk_alt_error'),'red');
				}
		} else {
			$this->core->message( $this->user->lang('maintenanceuser_mail_not_valid'), $this->user->lang('pk_alt_error'), 'red');
		}
		
		$this->display();
	}
	
	public function display() {
		$user_active = ($this->config->get('maintenance_user') != "") ? true : false;

		if ($user_active){
			$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));		
			$muser = unserialize(stripslashes($muser_config));
			$query = $this->db->query("SELECT * FROM __users WHERE user_id = ".$muser['user_id']);
			while ($row = $this->db->fetch_record($query)){
				$user_data = $row;
			}
		}

		$day_dropdown = array(
			1	=> '1',
			2	=> '2',
			3	=> '3',
			5	=> '5',
			7	=> '7',
			14	=> '14',
		);
				
		// Assign the rest of the variables.
		$this->tpl->assign_vars(array(
			'DAY_DROPDOWN'			=> $this->html->DropDown('valid_until_days', $day_dropdown, 3),
			'USER_ACTIVE'			=> $user_active,
			'USER_DATA'				=> $this->user->lang('username').": ".((isset($user_data['username'])) ? $user_data['username'] : '')."\n".$this->user->lang('password').": ".((isset($muser['password'])) ? $muser['password'] : '')."\n".$this->user->lang('maintenanceuser_valid_until').": ".((isset($muser['valid_until'])) ? date($this->user->style['date_time'], $muser['valid_until']) : '')."\n".$this->user->lang('pk_set_linkurl').": ".$this->env->link,
		));
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('maintenanceuser_user'),
			'template_file'		=> 'admin/manage_maintenance_user.html',
			'display'			=> true
		));
	}
} //close class
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_maintenance_user', maintenance_user::__shortcuts());
registry::register('maintenance_user');
?>