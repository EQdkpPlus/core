<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * register.php
 * Began: Sat January 4 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
      	

class Register extends EQdkp_Admin
{
    var $server_url  = '';
    var $data        = array();

    function register()
    {
        global $db, $eqdkp, $user, $tpl, $pm,$conf_plus;
        global $SID;

        //
        // If they're trying access this page while logged in, redirect to settings.php
        //
        if ( ($user->data['user_id'] != ANONYMOUS) && (!isset($_GET['key'])) )
        {
            header('Location: settings.php' . $SID);
        }

        parent::eqdkp_admin();
        
        if ($conf_plus['pk_disable_reg'] ==1 ) 
		{
			redirect('index.php');	 	
		}
        
        if ($conf_plus['pk_bridge_cms_deac_reg'] ==1 ) 
        {  
    		if (strlen($conf_plus['pk_bridge_cms_InlineUrl']) > 1) 
    		{
    			redirect($conf_plus['pk_bridge_cms_InlineUrl'],false,true);		    			
    		}else 
    		{
    			redirect('index.php');	
    		}	
        	
        }
        

        // Data to be put into the form
        // If it's not in POST, we get it from config defaults
        $this->data = array(
            'username'    => post_or_db('username'),
            'user_email'  => post_or_db('user_email'),
            'user_alimit' => post_or_db('user_alimit', $eqdkp->config, 'default_alimit'),
            'user_elimit' => post_or_db('user_elimit', $eqdkp->config, 'default_elimit'),
            'user_ilimit' => post_or_db('user_ilimit', $eqdkp->config, 'default_ilimit'),
            'user_nlimit' => post_or_db('user_nlimit', $eqdkp->config, 'default_nlimit'),
            'user_rlimit' => post_or_db('user_rlimit', $eqdkp->config, 'default_rlimit'),
            'user_lang'   => post_or_db('user_lang',   $eqdkp->config, 'default_lang'),
            'user_style'  => post_or_db('user_style',  $eqdkp->config, 'default_style')
        );

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form'))
        );

        $this->assoc_params(array(
            'lostpassword' => array(
                'name'    => 'mode',
                'value'   => 'lostpassword',
                'process' => 'process_lostpassword'),
            'activate' => array(
                'name'    => 'mode',
                'value'   => 'activate',
                'process' => 'process_activate'))
        );

        // Build the server URL
        // ---------------------------------------------------------
        $script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($eqdkp->config['server_path']));
        $script_name = ( $script_name != '' ) ? $script_name . '/register.php' : 'register.php';
        $server_name = trim($eqdkp->config['server_name']);
        $server_port = ( intval($eqdkp->config['server_port']) != 80 ) ? ':' . trim($eqdkp->config['server_port']) . '/' : '/';
        $this->server_url  = 'http://' . $server_name . $server_port . $script_name;
    }

    function error_check()
    {
        global $db, $user;

        if ( isset($_POST['submit']) )
        {
            $sql = 'SELECT user_id
                    FROM ' . USERS_TABLE . "
                    WHERE username='" . $_POST['username'] . "'";
            if ( $db->num_rows($db->query($sql)) > 0 )
            {
                $this->fv->errors['username'] = $user->lang['fv_already_registered_username'];
            }

            $sql = 'SELECT user_id
                    FROM ' . USERS_TABLE . "
                    WHERE user_email='" . $_POST['user_email'] . "'";
            if ( $db->num_rows($db->query($sql)) > 0 )
            {
                $this->fv->errors['user_email'] = $user->lang['fv_already_registered_email'];
            }

            $this->fv->matching_passwords('user_password1', 'user_password2', $user->lang['fv_match_password']);

            $this->fv->is_number(array(
                'user_alimit' => $user->lang['fv_number'],
                'user_elimit' => $user->lang['fv_number'],
                'user_ilimit' => $user->lang['fv_number'],
                'user_nlimit' => $user->lang['fv_number'],
                'user_rlimit' => $user->lang['fv_number'])
            );

            $this->fv->is_email_address('user_email', $user->lang['fv_invalid_email']);

            $this->fv->is_filled(array(
                'username'       => $user->lang['fv_required_user'],
                'user_email'     => $user->lang['fv_required_email'],
                'user_password1' => $user->lang['fv_required_password'],
                'user_password2' => '')
            );
        }

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        // If the config requires account activation, generate a random key for validation
        if ( ($eqdkp->config['account_activation'] == USER_ACTIVATION_SELF) || ($eqdkp->config['account_activation'] == USER_ACTIVATION_ADMIN) )
        {
            $user_key = $this->random_string(true);
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

        // Insert them into the users table
        $query = $db->build_query('INSERT', array(
            'username'       => $_POST['username'],
            'user_password'  => md5($_POST['user_password1']),
            'user_email'     => $_POST['user_email'],
            'user_alimit'    => $_POST['user_alimit'],
            'user_elimit'    => $_POST['user_elimit'],
            'user_ilimit'    => $_POST['user_ilimit'],
            'user_nlimit'    => $_POST['user_nlimit'],
            'user_rlimit'    => $_POST['user_rlimit'],
            'user_style'     => $_POST['user_style'],
            'user_lang'      => $_POST['user_lang'],
            'user_key'       => $user_key,
            'user_active'    => $user_active,
            'user_lastvisit' => $this->time)
        );
        $sql = 'INSERT INTO ' . USERS_TABLE . $query;

        if ( !($db->query($sql)) )
        {
            message_die('Could not add user information', '', __FILE__, __LINE__, $sql);
        }
        $user_id = $db->insert_id();

        // Insert their permissions into the table
        $sql = 'SELECT auth_id, auth_default
                FROM ' . AUTH_OPTIONS_TABLE . '
                ORDER BY auth_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $au_sql = 'INSERT INTO ' . AUTH_USERS_TABLE . "
                       (user_id, auth_id, auth_setting)
                       VALUES ('" . $user_id . "','" . $row['auth_id'] . "','" . $row['auth_default'] . "')";
            $db->query($au_sql);
        }

        if ($eqdkp->config['account_activation'] == USER_ACTIVATION_SELF)
        {
            $success_message = sprintf($user->lang['register_activation_self'], stripslashes($_POST['user_email']));
            $email_template = 'register_activation_self';
        }
        elseif ($eqdkp->config['account_activation'] == USER_ACTIVATION_ADMIN)
        {
            $success_message = sprintf($user->lang['register_activation_admin'], stripslashes($_POST['user_email']));
            $email_template = 'register_activation_admin';
        }
        else
        {
            $success_message = sprintf($user->lang['register_activation_none'], '<a href="login.php'.$SID.'">', '</a>', stripslashes($_POST['user_email']));
            $email_template = 'register_activation_none';
        }

        //
        // Email a notice
        //
        include_once($eqdkp->root_path . 'includes/class_email.php');
        $email = new EMail;

        $headers = "From: " . $eqdkp->config['admin_email'] . "\nReturn-Path: " . $eqdkp->config['admin_email'] . "\r\n";

        $email->set_template($email_template, stripslashes($_POST['user_lang']));
        $email->address(stripslashes($_POST['user_email']));
        $email->subject(); // Grabbed from the template itself
        $email->extra_headers($headers);

        $email->assign_vars(array(
            'GUILDTAG'   => $eqdkp->config['guildtag'],
            'DKP_NAME'   => $eqdkp->config['dkp_name'],
            'USERNAME'   => stripslashes($_POST['username']),
            'PASSWORD'   => stripslashes($_POST['user_password1']),
            'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key)
        );
        $email->send();
        $email->reset();

        // Now email the admin if we need to
        if ( $eqdkp->config['account_activation'] == USER_ACTIVATION_ADMIN )
        {
            $email->set_template('register_activation_admin_activate', $eqdkp->config['default_lang']);
            $email->address($eqdkp->config['admin_email']);
            $email->subject();
            $email->extra_headers($headers);

            $email->assign_vars(array(
                'GUILDTAG'   => $eqdkp->config['guildtag'],
                'DKP_NAME'   => $eqdkp->config['dkp_name'],
                'USERNAME'   => stripslashes($_POST['username']),
                'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key)
            );
            $email->send();
            $email->reset();
        }

        message_die($success_message);
    }

    // ---------------------------------------------------------
    // Process Lost Password
    // ---------------------------------------------------------
    function process_lostpassword()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $username   = ( !empty($_POST['username']) )   ? trim(strip_tags($_POST['username'])) : '';
        $user_email = ( !empty($_POST['user_email']) ) ? trim(strip_tags($_POST['user_email'])) : '';

        //
        // Look up record based on the username and e-mail
        //
        $sql = 'SELECT user_id, username, user_email, user_active, user_lang
                FROM ' . USERS_TABLE . "
                WHERE user_email='" . $user_email . "'
                AND username='" . $username . "'";
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
                $user_key = $this->random_string(true);
                $key_len = 54 - (strlen($this->server_url));
                $key_len = ($key_len > 6) ? $key_len : 6;

                $user_key = substr($user_key, 0, $key_len);
                $user_password = $this->random_string(false);

                $sql = 'UPDATE ' . USERS_TABLE . "
                        SET user_newpassword='" . md5($user_password) . "', user_key='" . $user_key . "'
                        WHERE user_id='" . $row['user_id'] . "'";
                if ( !$db->query($sql) )
                {
                    message_die('Could not update password information', '', __FILE__, __LINE__, $sql);
                }

                //
                // Email them their new password
                //
                include_once($eqdkp->root_path . 'includes/class_email.php');
                $email = new EMail;

                $headers = "From: " . $eqdkp->config['admin_email'] . "\nReturn-Path: " . $eqdkp->config['admin_email'] . "\r\n";

                $email->set_template('user_new_password', $row['user_lang']);
                $email->address($row['user_email']);
                $email->subject();
                $email->extra_headers($headers);

                $email->assign_vars(array(
                    'GUILDTAG'   => $eqdkp->config['guildtag'],
                    'DKP_NAME'   => $eqdkp->config['dkp_name'],
                    'USERNAME'   => $row['username'],
                    'DATETIME'   => date('m/d/y h:ia T', time()),
                    'IPADDRESS'  => $user->ip_address,
                    'U_ACTIVATE' => $this->server_url . '?mode=activate&key=' . $user_key,
                    'USERNAME'   => $row['username'],
                    'PASSWORD'   => $user_password)
                );
                $email->send();
                $email->reset();

                message_die($user->lang['password_sent']);
            }
            else
            {
                message_die($user->lang['error_invalid_email']);
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
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $sql = 'SELECT user_id, username, user_active, user_email, user_newpassword, user_lang, user_key
                FROM ' . USERS_TABLE . "
                WHERE user_key='" . $_GET['key'] . "'";
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

                $sql = 'UPDATE ' . USERS_TABLE . "
                        SET user_active='1', user_key=''" . $sql_password . "
                        WHERE user_id='" . $row['user_id'] . "'";
                $db->query($sql);

                // E-mail the user if this was activated by the admin
                if ( $eqdkp->config['account_activation'] == USER_ACTIVATION_ADMIN )
                {
                    include_once($eqdkp->root_path . 'includes/class_email.php');
                    $email = new EMail;

                    $headers = "From: " . $eqdkp->config['admin_email'] . "\nReturn-Path: " . $eqdkp->config['admin_email'] . "\r\n";

                    $email->set_template('register_activation_none', $row['user_lang']);
                    $email->address($row['user_email']);
                    $email->subject();
                    $email->extra_headers($headers);

                    $email->assign_vars(array(
                        'GUILDTAG' => $eqdkp->config['guildtag'],
                        'DKP_NAME' => $eqdkp->config['dkp_name'],
                        'USERNAME' => $row['username'],
                        'PASSWORD' => '(encrypted)')
                    );
                    $email->send();
                    $email->reset();

                    $success_message = $user->lang['account_activated_admin'];
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
    function random_string($hash = false)
    {
    	$chars = array('a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H','i','I','j','J',
                       'k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T',
                       'u','U','v','V','w','W','x','X','y','Y','z','Z','1','2','3','4','5','6','7','8',
                       '9','0');

    	$max_chars = count($chars) - 1;
    	srand( (double) microtime()*1000000);

    	$rand_str = '';
    	for($i = 0; $i < 8; $i++)
    	{
    		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
    	}

    	return ( $hash ) ? md5($rand_str) : $rand_str;
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $tpl->assign_vars(array(
            'F_SETTINGS' => 'register.php' . $SID,

            'S_CURRENT_PASSWORD' => false,
            'S_NEW_PASSWORD'     => false,
            'S_SETTING_ADMIN'    => false,
            'S_MU_TABLE'         => false,

            'L_REGISTRATION_INFORMATION' => $user->lang['registration_information'],
            'L_REQUIRED_FIELD_NOTE'      => $user->lang['required_field_note'],
            'L_USERNAME'                 => $user->lang['username'],
            'L_EMAIL_ADDRESS'            => $user->lang['email_address'],
            'L_PASSWORD'                 => $user->lang['password'],
            'L_CONFIRM_PASSWORD'         => $user->lang['confirm_password'],
            'L_PREFERENCES'              => $user->lang['preferences'],
            'L_ADJUSTMENTS_PER_PAGE'     => $user->lang['adjustments_per_page'],
            'L_EVENTS_PER_PAGE'          => $user->lang['events_per_page'],
            'L_ITEMS_PER_PAGE'           => $user->lang['items_per_page'],
            'L_NEWS_PER_PAGE'            => $user->lang['news_per_page'],
            'L_RAIDS_PER_PAGE'           => $user->lang['raids_per_page'],
            'L_LANGUAGE'                 => $user->lang['language'],
            'L_STYLE'                    => $user->lang['style'],
            'L_PREVIEW'                  => $user->lang['preview'],
            'L_SUBMIT'                   => $user->lang['submit'],
            'L_RESET'                    => $user->lang['reset'],
            'REGISTER'					 => true ,

            'USERNAME'    => $this->data['username'],
            'USER_EMAIL'  => $this->data['user_email'],
            'USER_ALIMIT' => $this->data['user_alimit'],
            'USER_ELIMIT' => $this->data['user_elimit'],
            'USER_ILIMIT' => $this->data['user_ilimit'],
            'USER_NLIMIT' => $this->data['user_nlimit'],
            'USER_RLIMIT' => $this->data['user_rlimit'],

            'FV_USERNAME'      => $this->fv->generate_error('username'),
            'FV_USER_PASSWORD' => $this->fv->generate_error('user_password1'),
            'FV_USER_EMAIL'    => $this->fv->generate_error('user_email'),
            'FV_USER_ALIMIT'   => $this->fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'   => $this->fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'   => $this->fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'   => $this->fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'   => $this->fv->generate_error('user_rlimit'))
        );

        //
        // Build language drop-down
        //
        if ( $dir = @opendir($eqdkp->root_path . 'language/') )
        {
            while ( $file = @readdir($dir) )
            {
                if ( (!is_file($eqdkp->root_path . 'language/' . $file)) && valid_folder($file) )
                {
                    $tpl->assign_block_vars('lang_row', array(
                        'VALUE'    => $file,
                        'SELECTED' => ( $this->data['user_lang'] == $file ) ? ' selected="selected"' : '',
                        'OPTION'   => ucfirst($file))
                    );
                }
            }
        }

        //
        // Build style drop-down
        //
        $sql = 'SELECT style_id, style_name
                FROM ' . STYLES_TABLE . '
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

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': ' . $user->lang['register_title'],
            'template_file' => 'settings.html',
            'display'       => true)
        );
    }
}

$register = new Register;
$register->process();
?>
