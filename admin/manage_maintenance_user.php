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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class maintenance_user extends page_generic {
	public static $shortcuts = array('email'=>'MyMailer', 'crypt'=>'encrypt');

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
			'username'		=> $this->user->lang('maintenanceuser_user'),
			'user_password' => $encr_password,
			'user_active'	=> 1,
			'rules'			=> 1,
			'user_email'	=> $this->crypt->encrypt('maintenance@local.com'),
		);
		
		$user_id = $this->pdh->put('user', 'insert_user', array($arrUser, false));
		
		$user_data = array(
			'user_id'	=> $user_id,
			'password'	=> $password,
			'valid_until' => $valid_until,
		);
		$this->pdh->put('user_groups_users', 'add_user_to_group', array($user_id, 2));
		$this->config->set('maintenance_user', $this->crypt->encrypt(serialize($user_data)));
		
		$this->pdh->put('user', 'add_special_user', array($user_id));
		$this->timekeeper->add_cron('maintenanceuser', array('repeat_type' => 'daily', 'repeat_interval' => $this->in->get('valid_until_days', 0), 'active' => true), true);
		$log_action = array(
			'{L_maintenanceuser_valid}'	=> "{D_".$valid_until."}",
		);
		$this->logs->add('action_maintenanceuser_added', $log_action, $user_id, $this->user->lang('maintenanceuser_user'));
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
			$log_action = array(
					'{L_maintenanceuser_valid}'	=> "{D_".$user_data['valid_until']."}",
			);
			$this->logs->add('action_maintenanceuser_renewed', $log_action, $user_data['user_id'], $this->user->lang('maintenanceuser_user'));
		}
	}
	
	public function delete() {
		$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));
		if ($muser_config != ''){
			$muser = unserialize(stripslashes($muser_config));
			$this->db->prepare("DELETE FROM __users WHERE user_id = ?")->execute($muser['user_id']);

			$this->pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
			$this->config->set('maintenance_user', '');
		
			$this->pdh->put('user', 'delete_special_user', array($user_id));
			$this->logs->add('action_maintenanceuser_deleted', array(), $muser['user_id'], $this->user->lang('maintenanceuser_user'));
		}
		
		$this->timekeeper->del_cron('maintenanceuser');
		
		$this->display();
	}
	
	public function mail() {
		$user_active = ($this->config->get('maintenance_user') != "") ? true : false;
		
		if ($user_active){
			$muser_config = $this->crypt->decrypt($this->config->get('maintenance_user'));
			$muser = unserialize(stripslashes($muser_config));
			
			$objQuery = $this->db->prepare("SELECT * FROM __users WHERE user_id = ?")->limit(1)->execute($muser['user_id']);
			if ($objQuery && $objQuery->numRows){
				$user_data = $objQuery->fetchAssoc();
			} else {
				$this->display();
				return;
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
			
			$objQuery = $this->db->prepare("SELECT * FROM __users WHERE user_id = ?")->limit(1)->execute($muser['user_id']);
			if ($objQuery && $objQuery->numRows){
				$user_data = $objQuery->fetchAssoc();
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
			'DAY_DROPDOWN'			=> new hdropdown('valid_until_days', array('options' => $day_dropdown, 'value' => 3)),
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
registry::register('maintenance_user');
?>