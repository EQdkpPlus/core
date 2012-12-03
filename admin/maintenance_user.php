<?php 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_');

class maintenance_user extends EQdkp_Admin
{

  function maintenance_user()
  {
    global $db, $core, $user, $tpl, $pm, $eqdkp_root_path, $SID, $a_system, $a_styles;
    parent::eqdkp_admin();

    $this->assoc_buttons(array(
      'form' => array(
      	'name'    => '',
      	'process' => 'display_form',
      	'check'   => 'a_maintenance'),
			'submit' => array(
      	'name'    => 'create',
      	'process' => 'process_submit',
      	'check'   => 'a_maintenance'),
			'send_mail' => array(
      	'name'    => 'email_submit',
      	'process' => 'process_mail',
      	'check'   => 'a_maintenance'),
			'delete' => array(
      	'name'    => 'delete',
      	'process' => 'process_delete',
      	'check'   => 'a_maintenance')
    ));
		
	}
	
	function process_submit(){
		global $core, $user, $tpl, $table_prefix, $pcache, $SID, $html, $pdh, $acl, $db, $in, $timekeeper;
		
		$valid_until = time() + ($in->get('valid_until_days', 0) * 86400);
		
		$salt = $user->generate_salt();
		$password = $core->random_string();
		$encr_password = $user->encrypt_password($password, $salt).':'.$salt;
		$db->query("INSERT INTO __users (username, user_password, user_active, user_lang, user_newpassword, user_style) VALUES ('".$user->lang['maintenanceuser_user']."', '".$encr_password."', '1', '".$core->config['default_lang']."', '', ".$core->config['default_style'].")");
		$user_id = $db->sql_lastid();
		$user_data = array(
			'user_id'	=> $user_id,
			'password'	=> $password,
			'valid_until'		=> $valid_until,
		);
		$pdh->put('user_groups_users', 'add_user_to_group', array($user_id, 2));
		$core->config_set('maintenance_user', serialize($user_data));
		
		$special_users = unserialize(stripslashes($core->config['special_user']));
		$special_users[$user_id] = $user_id;
		$core->config_set('special_user', serialize($special_users));
		
		$timekeeper->add_cron('maintenanceuser', array('run_time' => ($in->get('valid_until_days', 0) * 86400), 'active' => true), true);
		$this->display_form();
	}
	
	function process_delete(){
		global $core, $user, $tpl, $table_prefix, $pcache, $SID, $html, $pdh, $acl, $db, $in, $timekeeper;
		$muser = unserialize(stripslashes($core->config['maintenance_user']));
		$db->query("DELETE FROM __users WHERE user_id = ".$muser['user_id']);

		$pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
		$core->config_set('maintenance_user', '');
		
		$special_users = unserialize(stripslashes($core->config['special_user']));
		unset($special_users[$muser['user_id']]);
		$core->config_set('special_user', serialize($special_users));
		
		$timekeeper->del_cron('maintenanceuser');
		
		$this->display_form();
	}
	
	function process_mail(){
		global $core, $user, $tpl, $table_prefix, $pcache, $SID, $html, $pdh, $acl, $db, $in;
		$user_active = ($core->config['maintenance_user'] != "") ? true : false;
		
		if ($user_active){
			$muser = unserialize(stripslashes($core->config['maintenance_user']));
			$query = $db->query("SELECT * FROM __users WHERE user_id = ".$muser['user_id']);
			while ($row = $db->fetch_record($query)){
				$user_data = $row;
			}
		}
		
		$email = new MyMailer($eqdkp_root_path);

		$bodyvars = array(
					'USERNAME'		=> $user_data['username'],
					'PASSWORD'		=> $muser['password'],
					'VALID'				=> date($user->style['date_time'], $muser['valid_until']),
					'EQDKP_URL'		=> $core->BuildLink(),
					'GUILD'				=> $core->config['guildtag'],

		);
		
		
		if ($in->get('email') == $in->get('email_repeat')){
			if($email->SendMailFromAdmin($in->get('email'), sprintf($user->lang['maintenanceuser_mail_subject'], $core->config['guildtag']), 'maintenance_user.html', $bodyvars, $core->config['lib_email_method'])){
							$core->message( $user->lang['maintenanceuser_mail_success'], '','green');
				}else{
							$core->message( $user->lang['maintenanceuser_mail_error'], $user->lang['pk_alt_error'],'red');
				}
				
		} else {
			
			$core->message( $user->lang['maintenanceuser_mail_not_valid'], $user->lang['pk_alt_error'], 'red');
		}
				
		
		$this->display_form();
	}
	
	function display_form(){
		global $core, $user, $tpl, $table_prefix, $pcache, $SID, $html, $db;
		
		$user_active = ($core->config['maintenance_user'] != "") ? true : false;
		
		if ($user_active){
			$muser = unserialize(stripslashes($core->config['maintenance_user']));
			$query = $db->query("SELECT * FROM __users WHERE user_id = ".$muser['user_id']);
			while ($row = $db->fetch_record($query)){
				$user_data = $row;
			}
		}
	
		$day_dropdown = array(
		1 => '1',
		2	=> '2',
		3 => '3',
		5 => '5',
		7	=> '7',
		14	=> '14',
		);
				
		// Assign the rest of the variables.
    $tpl->assign_vars(array(
			'DAY_DROPDOWN' => $html->DropDown('valid_until_days', $day_dropdown, 3),
			'USER_ACTIVE'	=> $user_active,
			'USER_DATA'		=> $user->lang['username'].": ".$user_data['username']."\n".$user->lang['password'].": ".$muser['password']."\n".$user->lang['maintenanceuser_valid_until'].": ".date($user->style['date_time'], $muser['valid_until'])."\n".$user->lang['pk_set_linkurl'].": ".$core->BuildLink(),
			'L_INFO'	=> $user->lang['maintenanceuser_info'],
			'L_WARNING'	=> $user->lang['maintenanceuser_warning'],
			'L_MAINTENANCE_USER' => $user->lang['maintenanceuser_user'],
			'L_CREATE' => $user->lang['maintenanceuser_create'],
			'L_DELETE' => $user->lang['maintenanceuser_delete'],
			'L_VALID' => $user->lang['maintenanceuser_valid'],
			'L_DAYS' => $user->lang['days'],
			'L_INFOS' => $user->lang['infos'],
			'L_EMAIL'	=> $user->lang['email'],
			'L_EMAIL_CONFIRM'	=> $user->lang['email_confirm'],
			'L_SEND'	=> $user->lang['maintenanceuser_send'],
			'L_SEND_MAIL'	=> $user->lang['maintenanceuser_send_mail'],
    ));
			
			
	  $core->set_vars(array(
    	'page_title'    	=> $user->lang['maintenanceuser_user'],
    	'template_file' 	=> 'admin/maintenance_user.html',
     	'display'       	=> true
    ));
	}
	
	
} //close class

$m_user = new maintenance_user;
$m_user->process();
?>