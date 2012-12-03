<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
      	

class Register extends EQdkp_Admin
{
    var $server_url  = '';
    var $data        = array();

    function register()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID;
				
				parent::eqdkp_admin();

				if ($user->data['rules'] == 1){
					//
					// If they're trying access this page while logged in, redirect to settings.php
					//
					if ( ($user->data['user_id'] != ANONYMOUS) && (!isset($_GET['key'])))
					{
							redirect('settings.php'. $SID);
					}			
					
					if ($core->config['disable_registration'] ==1 ) 
					{
						redirect('index.php');	 	
					}
					
					if ($core->config['pk_bridge_cms_deac_reg'] ==1 ) 
					{  
					if (strlen($core->config['pk_bridge_cms_InlineUrl']) > 1) 
					{
						redirect($core->config['pk_bridge_cms_InlineUrl'],false,true);		    			
					}else 
					{
						redirect('index.php');	
					}	
						
					}
				}

        // Data to be put into the form
        // If it's not in POST, we get it from config defaults
        $this->data = array(
            'username'    => post_or_db('username'),
            'user_email'  => post_or_db('user_email'),
						'user_email2' => post_or_db('user_email2'),
						'first_name'  => post_or_db('first_name'),
						'last_name'	  => post_or_db('last_name'),
						'gender'	  	=> post_or_db('gender'),
						'country'	  	=> post_or_db('country'),
			      'user_lang'   => post_or_db('user_lang',   $core->config, 'default_lang'),
            'user_style'  => post_or_db('user_style',  $core->config, 'default_style'),
						'user_timezone' => post_or_db('user_timezone',  $core->config, 'timezone'),
        );

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit'),
            'form' => array(
                'name'    => '',
                'process' => 'display_licence'),					
						'register' => array(
                'name'    => 'register',
                'process' => 'display_form'),
						'guildrules' => array(
                'name'    => 'guildrules',
                'process' => 'display_guildrules'),
						'deny' => array(
                'name'    => 'deny',
                'process' => 'process_deny'),
						'confirmed' => array(
                'name'    => 'confirmed',
                'process' => 'process_confirmed'),
						)
        );

        $this->assoc_params(array(
            'lostpassword' => array(
                'name'    => 'mode',
                'value'   => 'lostpassword',
                'process' => 'process_lostpassword'),
						'resend_validation' => array(
                'name'    => 'mode',
                'value'   => 'resend_validation',
                'process' => 'process_resend_validation'),
            'activate' => array(
                'name'    => 'mode',
                'value'   => 'activate',
                'process' => 'process_activate'))
        );

        // Build the server URL
        // ---------------------------------------------------------
        $this->server_url  = $core->BuildLink().'register.php';
    }

    function error_check()
    {
        global $db, $user, $core, $in;

        if ( isset($_POST['submit']) )
        {
            $sql = "SELECT user_id
                    FROM __users
                    WHERE username='" . $_POST['username'] . "'";
            if ( $db->num_rows($db->query($sql)) > 0 )
            {
                $this->fv->errors['username'] = $user->lang['fv_already_registered_username'];
            }

            $sql = "SELECT user_id
                    FROM __users
                    WHERE user_email='" . $_POST['user_email'] . "'";
            if ( $db->num_rows($db->query($sql)) > 0 )
            {
                $this->fv->errors['user_email'] = $user->lang['fv_already_registered_email'];
            }

            $this->fv->matching_passwords('user_password1', 'user_password2', $user->lang['fv_match_password']);
						
						$this->fv->matching_emails('user_email', 'user_email2', $user->lang['fv_match_email']);

            $this->fv->is_email_address('user_email', $user->lang['fv_invalid_email']);

            $this->fv->is_filled(array(
                'username'       => $user->lang['fv_required_user'],
                'user_email'     => $user->lang['fv_required_email'],
								'user_email2'			=> '',
                'user_password1' => $user->lang['fv_required_password'],
                'user_password2' => '',
                'first_name'     => $user->lang['fv_required'],
                'gender'     	 		=> $user->lang['fv_required'],
                'country'     	 => $user->lang['fv_required'],
                )
            );
						
						if ($core->config['pk_enable_captcha'] == 1){

							$captcha = new recaptcha;
							$response = $captcha -> recaptcha_check_answer ($core->config['lib_recaptcha_pkey'], $_SERVER["REMOTE_ADDR"], $in->get('recaptcha_challenge_field'), $in->get('recaptcha_response_field'));

							if ($response->is_valid) {

							} else {
								$this->fv->is_email_address('captcha', $user->lang['lib_captcha_wrong']);
							}
						}
       
				}
			 return $this->fv->is_error();
        
    }

    // ---------------------------------------------------------
    // Process Submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $core, $user, $tpl, $pm, $pdh;
        global $SID;

        // If the config requires account activation, generate a random key for validation
        if ( ($core->config['account_activation'] == 1) || ($core->config['account_activation'] == 2) )
        {
            $user_key = $core->random_string(true);
            $key_len = 54 - (strlen($this->server_url));
            $key_len = ($key_len > 6) ? $key_len : 6;

            $user_key = substr($user_key, 0, $key_len);
            $user_active = '0';

            if ($user->data['user_id'] != ANONYMOUS)
            {
                $user->destroy();
            }
        }
        else
        {
            $user_key = '';
            $user_active = '1';
        }
				
				//Insert the user into the DB
				$user_id = $pdh->put('user', 'register_user', array($user_active, $user_key, true));

				
				//Put the user into the default class
				$default_group = $pdh->get('user_groups', 'standard_group', array());
				if ($default_group){
					$pdh->put('user_groups_users', 'add_user_to_group', array($user_id, $default_group));
				} else {
					$sql = 'SELECT auth_id, auth_default
									FROM __auth_options
									ORDER BY auth_id';
					$result = $db->query($sql);
					while ( $row = $db->fetch_record($result) )
					{
							$au_sql = "INSERT INTO __auth_users
												 (user_id, auth_id, auth_setting)
												 VALUES ('" . $user_id . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
							$db->query($au_sql);
					}
				}

        if ($core->config['account_activation'] == 1)
        {
            $success_message = sprintf($user->lang['register_activation_self'], stripslashes($_POST['user_email']));
            $email_template = 'register_activation_self';
            $email_subject	= $user->lang['email_subject_activation_self'];
        }
        elseif ($core->config['account_activation'] == 2)
        {
            $success_message = sprintf($user->lang['register_activation_admin'], stripslashes($_POST['user_email']));
            $email_template = 'register_activation_admin';
            $email_subject	= $user->lang['email_subject_activation_admin'];
        }
        else
        {
            $success_message = sprintf($user->lang['register_activation_none'], '<a href="login.php'.$SID.'">', '</a>', stripslashes($_POST['user_email']));
            $email_template = 'register_activation_none';
            $email_subject	= $user->lang['email_subject_activation_none'];
        }

        //
        // Email a notice
        //
        //
				$email = new MyMailer($options, $eqdkp_root_path);
				$email->Set_Language($_POST['user_lang']);
				$bodyvars = array(
					'USERNAME'		=> stripslashes($_POST['username']),
          'PASSWORD'   	=> stripslashes($_POST['user_password1']),
          'U_ACTIVATE' 	=> $this->server_url . '?mode=activate&key=' . $user_key,
					'GUILDTAG'		=> $core->config['guildtag'],
				);
				if($email->SendMailFromAdmin($_POST['user_email'], $email_subject, $email_template.'.html', $bodyvars)){
						//$success_message = $user->lang['account_activated_admin'];
					}else{
            $success_message = $user->lang['email_subject_send_error'];
					}

        // Now email the admin if we need to
        if ( $core->config['account_activation'] == 2 )
        {
					$email->Set_Language($core->config['default_lang']);
					$bodyvars = array(
						'USERNAME'   => stripslashes($_POST['username']),
          	'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key
					);
					if($email->SendMailFromAdmin($core->config['admin_email'], $user->lang['email_subject_activation_admin_act'], 'register_activation_admin_activate.html', $bodyvars)){
						//$success_message = $user->lang['account_activated_admin'];
					}else{
            $success_message = $user->lang['email_subject_send_error'];
					}
        }

        message_die($success_message);
    }
    
		// ---------------------------------------------------------
    // Process Resend Validation E-Mail
    // ---------------------------------------------------------
    function process_resend_validation()
    {
        global $db, $core, $user, $tpl, $pm, $eqdkp_root_path;
        global $SID;

        $username   = ( !empty($_POST['username']) )   ? trim(strip_tags($_POST['username'])) : '';
        $user_email = ( !empty($_POST['user_email']) ) ? trim(strip_tags($_POST['user_email'])) : '';

        //
        // Look up record based on the username and e-mail
        //
        $sql = "SELECT user_id, username, user_email, user_active, user_lang
                FROM __users
                WHERE user_email='" .$db->sql_escape($user_email)."'
                AND username='".$db->sql_escape($username)."'";
        if ( $result = $db->query($sql) )
        {
            if ( $row = $db->fetch_record($result) )
            {
                // Account's inactive, can't give them their password
                if ( $row['user_active'] || $core->config['account_activation'] != 1)
                {
                    message_die($user->lang['error_already_activated']);
                }

                $username = $row['username'];

                // Create a new activation key
                $user_key = $core->random_string(true);
                $key_len = 54 - (strlen($this->server_url));
                $key_len = ($key_len > 6) ? $key_len : 6;

                $user_key = substr($user_key, 0, $key_len);
								
                $sql = "UPDATE __users
                        SET user_key='" . $user_key . "'
                        WHERE user_id='" . $row['user_id'] . "'";
                if ( !$db->query($sql) )
                {
                    message_die('Could not update password information', '', __FILE__, __LINE__, $sql);
                }

                //
                // Email them their new password
                //
                $email = new MyMailer($eqdkp_root_path);
                $bodyvars = array(
                    'USERNAME'   => $row['username'],
                    'DATETIME'   => date('m/d/y h:ia T', time()),
                    'IPADDRESS'  => $user->ip_address,
                    'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key,
                    'USERNAME'   => $row['username'],
                );
						
                if($email->SendMailFromAdmin($row['user_email'], $user->lang['email_subject_activation_self'], 'register_activation_self.html', $bodyvars)) {
                	message_die(sprintf($user->lang['register_activation_self'], stripslashes($_POST['user_email'])), $user->lang['get_new_password']);
                } else {
                	message_die($user->lang['error_email_send'], $user->lang['get_new_password']);
                }
            }
            else
            {
                message_die($user->lang['error_invalid_user_or_mail'], $user->lang['get_new_activation_mail'], '', '', '', array('value' => $user->lang['back'], 'onclick' => 'javascript:history.back()'));
            }
        }
        else
        {
            message_die('Could not obtain user information', '', __FILE__, __LINE__, $sql);
        }
    }
		
    // ---------------------------------------------------------
    // Process Lost Password
    // ---------------------------------------------------------
    function process_lostpassword()
    {
        global $db, $core, $user, $tpl, $pm, $eqdkp_root_path;
        global $SID;

        $username   = ( !empty($_POST['username']) )   ? trim(strip_tags($_POST['username'])) : '';
        $user_email = ( !empty($_POST['user_email']) ) ? trim(strip_tags($_POST['user_email'])) : '';

        //
        // Look up record based on the username and e-mail
        //
        $sql = "SELECT user_id, username, user_email, user_active, user_lang
                FROM __users
                WHERE user_email='" .$db->sql_escape($user_email)."'
                AND username='".$db->sql_escape($username)."'";
        if ( $result = $db->query($sql) )
        {
            if ( $row = $db->fetch_record($result) )
            {
                // Account's inactive, can't give them their password
                if ( !$row['user_active'] )
                {
                    message_die($user->lang['error_account_inactive']);
                }

                $username = $row['username'];

                // Create a new activation key
                $user_key = $core->random_string(true);
                $key_len = 54 - (strlen($this->server_url));
                $key_len = ($key_len > 6) ? $key_len : 6;

                $user_key = substr($user_key, 0, $key_len);
                $user_password = $core->random_string(false);
								$user_salt = $user->generate_salt();
								
                $sql = "UPDATE __users
                        SET user_newpassword='" . $user->encrypt_password($user_password, $user_salt).':'.$user_salt. "', user_key='" . $user_key . "'
                        WHERE user_id='" . $row['user_id'] . "'";
                if ( !$db->query($sql) )
                {
                    message_die('Could not update password information', '', __FILE__, __LINE__, $sql);
                }

                //
                // Email them their new password
                //
                $email = new MyMailer($eqdkp_root_path);
                $bodyvars = array(
                    'USERNAME'   => $row['username'],
                    'DATETIME'   => date('m/d/y h:ia T', time()),
                    'IPADDRESS'  => $user->ip_address,
                    'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key,
                    'USERNAME'   => $row['username'],
                    'PASSWORD'   => $user_password
                );

                if($email->SendMailFromAdmin($row['user_email'], $user->lang['email_subject_new_pw'], 'user_new_password.html', $bodyvars)) {
                	message_die($user->lang['password_sent'], $user->lang['get_new_password']);
                } else {
                	message_die($user->lang['error_email_send'], $user->lang['get_new_password']);
                }
            }
            else
            {
                message_die($user->lang['error_invalid_user_or_mail'], $user->lang['get_new_password'], '', '', '', array('value' => $user->lang['back'], 'onclick' => 'javascript:history.back()'));
            }
        }
        else
        {
            message_die('Could not obtain user information', '', __FILE__, __LINE__, $sql);
        }
    }

    // ---------------------------------------------------------
    // Process Activate
    // ---------------------------------------------------------
    function process_activate()
    {
        global $db, $core, $user, $tpl, $pm, $in;
        global $SID;

        $sql = "SELECT user_id, username, user_active, user_email, user_newpassword, user_lang, user_key
                FROM __users
                WHERE user_key='" . $in->get('key') . "'";
        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not obtain user information', '', __FILE__, __LINE__, $sql);
        }
        if ( $row = $db->fetch_record($result) )
        {
            // If they're already active, just bump them back
            if ( ($row['user_active'] == '1') && ($row['user_key'] == '') )
            {
                message_die($user->lang['error_already_activated']);
            }
            else
            {
                // Update the password if we need to
                $sql_password = ( !empty($row['user_newpassword']) ) ? ", user_password='" . $row['user_newpassword'] . "', user_newpassword=''" : '';

                $sql = "UPDATE __users
                        SET user_active='1', user_key=''" . $sql_password . "
                        WHERE user_id='" . $row['user_id'] . "'";
                $db->query($sql);

                // E-mail the user if this was activated by the admin
                if ( $core->config['account_activation'] == 2 )
                {
									$email = new MyMailer($eqdkp_root_path);
									$email->Set_Language($row['user_lang']);
									$bodyvars = array(
										'USERNAME' => $row['username'],
										'PASSWORD' => $user->lang['email_encrypted']
									);
									if($email->SendMailFromAdmin($row['user_email'], $user->lang['email_subject_activation_none'], 'register_activation_none.html', $bodyvars)) {
                    $success_message = $user->lang['account_activated_admin'];
                  }else{
                  	$success_message = $user->lang['email_subject_send_error'];
                  }
                }
                else
                {
                    $tpl->assign_vars(array(
                        'META' => '<meta http-equiv="refresh" content="3;login.php' . $SID . '">')
                    );

                    $success_message = sprintf($user->lang['account_activated_user'], '<a href="login.php' . $SID . '">', '</a>');
                }

                message_die($success_message);
            }
        }
        else
        {
            message_die($user->lang['error_invalid_key']);
        }
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    
		function display_licence(){
			  global $db, $core, $user, $tpl, $pm, $jquery, $html;
        global $SID, $pdh;
				
				$count = count($pdh->get('infopages', 'guildrule_page'));
				$button = ($user->data['user_id'] != ANONYMOUS) ? 'confirmed' : 'register';
				
				$tpl->assign_vars(array(
					'SUBMIT_BUTTON'	=> ($count > 0) ? 'guildrules' : $button,
					'FORM_ACTION'	=> 'register.php'.$SID,
					'L_HEADER'		=> $user->lang['licence_agreement'],
					'L_TEXT'			=> $user->lang['register_licence'],
					'L_ACCEPT'		=> $user->lang['accept'],
					'L_DENY'			=> $user->lang['deny'],
				));
				
				$core->set_vars(array(
            'page_title'    => $user->lang['register_title'],
            'template_file' => 'register.html',
            'display'       => true)
        );
		}	
		
		function display_guildrules(){
			  global $db, $core, $user, $tpl, $pm, $jquery, $html;
        global $SID, $pdh;
				
				$button = ($user->data['user_id'] != ANONYMOUS) ? 'confirmed' : 'register';
				$page = $pdh->get('infopages', 'guildrule_page');
				$data = $pdh->get('infopages', 'content', array($page[0]));
				
				$tpl->assign_vars(array(
					'SUBMIT_BUTTON'	=> $button,
					'FORM_ACTION'	=> 'register.php'.$SID,
					'L_HEADER'		=> $user->lang['guildrules'],
					'L_TEXT'			=> html_entity_decode($data),
					'L_ACCEPT'		=> $user->lang['accept'],
					'L_DENY'			=> $user->lang['deny'],
				));
				
				$core->set_vars(array(
            'page_title'    => $user->lang['register_title'],
            'template_file' => 'register.html',
            'display'       => true)
        );
		}	

		function process_deny(){
				global $db, $core, $user, $tpl, $pm, $jquery, $html;
        global $SID;
				if ($user->data['user_id'] != ANONYMOUS){
					redirect('login.php'.$SID.'&logout=true');
				} else {
					redirect('index.php');
				}
		}
		
		function process_confirmed(){
				global $db, $core, $user, $tpl, $pm, $jquery, $html;
        global $SID;
				if ($user->data['user_id'] != ANONYMOUS){
					$db->query("UPDATE __users SET rules = 1 WHERE user_id='".$db->escape($user->data['user_id'])."'");
				}
				redirect('index.php');
		}
		
		
		
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $jquery, $html, $time;
        global $SID;

				//Captcha
				if ($core->config['pk_enable_captcha'] == 1){
					$captcha = new recaptcha;
					 
					$tpl->assign_vars(array(								
						'CAPTCHA' => $captcha->recaptcha_get_html($core->config['lib_recaptcha_okey']),	
						'S_DISPLAY_CATPCHA'		=> true,
						'L_CONFIRM'	=> $user->lang['lib_captcha_head'],
						'L_CONFIRM_TEXT'	=> $user->lang['lib_captcha_head'],
						'L_CONFIRM_INFO'	=> $user->lang['lib_captcha_insertword'],
						'L_RELOAD_CAPTCHA'	=> $user->lang['lib_captcha_reload'],
					));
					
				}

				$jquery->Validate('register', array(					
					array('name' => 'username', 'value'=> '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['fv_required_user']), 
					array('name'=>'user_email', 'value'=>$user->lang['jqfv_required_email']), 
					array('name'=>'user_email2', 'value'=>$user->lang['jqfv_required_email2']),  
					array('name'=>'user_password1', 'value'=>'<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['fv_required_password']),  
					array('name'=>'user_password2', 'value'=>'<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['register_help_password_repeat']), 
					array('name' => 'first_name', 'value'=> '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['fv_required']), 
					array('name' => 'country', 'value'=> '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['fv_required']),
					array('name' => 'gender', 'value'=> '<br><img src=\''.$eqdkp_root_path.'images/error.png\' height=\'20\'>'.$user->lang['fv_required']),
					array('name'=>'recaptcha_response_field', 'value'=>$user->lang['jqfv_recaptcha']),
				));
				
				$jquery->ResetValidate('register');
				       
				$gender_array = array(
						'0'=> "---",
						'1'=> $user->lang['adduser_gender_m'],
						'2'=> $user->lang['adduser_gender_f']
				);
			 
        $cfile = $eqdkp_root_path.'core/country_states.php';
        if (file_exists($cfile)){
					include_once($cfile);
        }
        
        $language_array = array();
        if($dir = @opendir($eqdkp_root_path . 'language/')){
					while($file = @readdir($dir)){
						if((!is_file($eqdkp_root_path . 'language/' . $file)) && (!is_link($eqdkp_root_path . 'language/' . $file)) && valid_folder($file)){
							$language_array[$file] = ucfirst($file);
						}
					}
        }
				
        $tpl->assign_vars(array(
            'F_SETTINGS' => 'register.php' . $SID,

            'S_CURRENT_PASSWORD' => false,
            'S_NEW_PASSWORD'     => false,
            'S_SETTING_ADMIN'    => false,
            'S_MU_TABLE'         => false,

            'L_REGISTRATION_INFORMATION' => $user->lang['registration_information'],
						'VALID_EMAIL_INFO'				 => ($core->config['account_activation'] == 1) ? '<br>'.$user->lang['valid_email_note'] : '',
            'L_REQUIRED_FIELD_NOTE'      => $user->lang['required_field_note'],
            'L_USERNAME'                 => $user->lang['username'],
            'L_EMAIL_ADDRESS'            => $user->lang['email_address'],
						'L_CONFIRM_EMAIL_ADDRESS'			=> $user->lang['email_confirm'],
            'L_PASSWORD'                 => $user->lang['password'],
            'L_CONFIRM_PASSWORD'         => $user->lang['confirm_password'],
            'L_PREFERENCES'              => $user->lang['view_options'],
            'L_TIMEZONES'									=> $user->lang['user_timezones'],			
            'L_LANGUAGE'                 => $user->lang['language'],
            'L_STYLE'                    => $user->lang['style'],
            'L_PREVIEW'                  => $user->lang['preview'],
            'L_SUBMIT'                   => $user->lang['submit'],
            'L_RESET'                    => $user->lang['reset'],
            'REGISTER'					 					=> true ,
            'BIRTHDAY' 										=> $jquery->Calendar('birthday', '', '', array('change_fields'=>true, 'year_range'=>'-80:0')),
						
						'HELP_USERNAME'	=> $html->HelpTooltip($user->lang['register_help_username']),
						'HELP_EMAIL'	=> $html->HelpTooltip($user->lang['register_help_email']),
						'HELP_EMAIL_CONFIRM'	=> $html->HelpTooltip($user->lang['register_help_email_confirm']),
						'HELP_PASSWORD'	=> $html->HelpTooltip($user->lang['register_help_password']),
						'HELP_PASSWORD_REPEAT'	=> $html->HelpTooltip($user->lang['register_help_password_repeat']),
						'HELP_NAME'	=> $html->HelpTooltip($user->lang['register_help_name']),
						'HELP_GENDER'	=> $html->HelpTooltip($user->lang['register_help_gender']),
						'HELP_LANGUAGE'	=> $html->HelpTooltip($user->lang['register_help_language']),
						'HELP_STYLE'	=> $html->HelpTooltip($user->lang['register_help_style']),
						'HELP_COUNTRY'	=> $html->HelpTooltip($user->lang['register_help_country']),
						
						'DD_LANGUAGE'									=> $html->DropDown('user_lang', $language_array, $this->data['user_lang']),
            'DD_STYLES'										=> $html->DropDown('user_style',  $style_array, $this->data['user_style']),
            'DD_TIMEZONES'								=> $html->DropDown('user_timezone', $time->timezones, $this->data['user_timezone']),
            'DD_COUNTRIES'								=> $html->DropDown('country',  $country_array, $this->data['country']),
            'DD_GENDER'										=> $html->DropDown('gender',  $gender_array, $this->data['gender']),

            'USERNAME'    => $this->data['username'],
            'USER_EMAIL'  => $this->data['user_email'],
						'USER_EMAIL2'  => $this->data['user_email2'],
            'USER_ALIMIT' => $this->data['user_alimit'],
            'USER_ELIMIT' => $this->data['user_elimit'],
            'USER_ILIMIT' => $this->data['user_ilimit'],
            'USER_NLIMIT' => $this->data['user_nlimit'],
            'USER_RLIMIT' => $this->data['user_rlimit'],
						'FIRST_NAME'  => $this->data['first_name'],
						'LAST_NAME'	  => $this->data['last_name'],
			
            
            'L_ADDUSER_FIRST_NAME' => $user->lang['adduser_first_name'],
            'L_ADDUSER_LAST_NAME' => $user->lang['adduser_last_name'],
            'L_ADDINFOS' => $user->lang['adduser_addinfos'],
            'L_ADDUSER_COUNTRY' => $user->lang['adduser_country'],
            'L_ADDUSER_STATE' => $user->lang['adduser_state'],
            'L_ADDUSER_GENDER' => $user->lang['adduser_gender'],          
            'L_ADDUSER_GENDER_M' => $user->lang['adduser_gender'],
            'L_ADDUSER_GENDER_F' => $user->lang['adduser_gender'],            
      

            'FV_USERNAME'      => $this->fv->generate_error('username'),
            'FV_USER_PASSWORD' => $this->fv->generate_error('user_password1'),
            'FV_USER_EMAIL'    => $this->fv->generate_error('user_email'),
            'FV_USER_ALIMIT'   => $this->fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'   => $this->fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'   => $this->fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'   => $this->fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'   => $this->fv->generate_error('user_rlimit'),

            'FV_FIRST_NAME' 	=> $this->fv->generate_error('first_name'),
            'FV_GENDER' 		=> $this->fv->generate_error('gender'),
            'FV_COUNTRY' 		=> $this->fv->generate_error('country'),
            'FV_CAPTCHA' 		=> $this->fv->generate_error('captcha'),
            
            )
        );


        
        //
        // Build style drop-down
        //
        $sql = 'SELECT style_id, style_name
                FROM __styles
                ORDER BY style_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('style_row', array(
                'VALUE'    => $row['style_id'],
                'SELECTED' => ( $this->data['user_style'] == $row['style_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => $row['style_name'])
            );
        }
        $db->free_result($result);

        $core->set_vars(array(
            'page_title'    => $user->lang['register_title'],
            'template_file' => 'settings.html',
            'display'       => true)
        );
    }
}

$register = new Register;
$register->process();
?>
