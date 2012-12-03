<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * manage_users.php
 * Began: Sun December 29 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Users extends EQdkp_Admin
{
    var $change_username = false;       // Was the username changed?                        @var change_username
    var $change_password = false;       // Was the password changed?                        @var change_password
    var $user_data       = array();     // Holds user data if 'u' is set              		@var user_data

    function manage_users()
    {
        global $db, $core, $user, $tpl, $pm, $in, $pdh;
        global $SID, $AjaxImageUpload, $pcache;

        parent::eqdkp_admin();
				
				$logo_upload = new AjaxImageUpload;
				$_auioptions = array(
					'filesize'  => '2048576',  // 1 MB
					'maxheight' => '500',
					'maxwidth'  => '500'
				);
				//Logo-Upload
				if($in->get('performupload') != '')
				{
					$logo_upload->PerformUpload('user_avatar', 'eqdkp', 'user_avatars',$_auioptions);
					die();
				}
				//Delete Image
				if($in->get('deleteavatar') == 'true')
				{
					$result = $db->query_first("SELECT custom_fields FROM __users WHERE user_id = '".$db->escape($in->get('uid'))."'");
					$custom = unserialize($result);
					$pcache->Delete($pcache->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp'));
					unset($custom['user_avatar']);
					$db->query("UPDATE __users SET custom_fields = '".$db->escape(serialize($custom))."' WHERE user_id='".$db->escape($in->get('uid'))."'");
					redirect('admin/manage_users.php'.$SID.'&u='.$in->get('uid'));
				}
				
        // Vars used to confirm deletion
        $confirm_text = $user->lang['confirm_delete_users'];
        $usernames = array();
        if ( $in->get('delete') != "")
        {

						if ( $in->get('user_id') != "" || count($in->getArray('user_id', 'int')) > 0)
            {
                if (count($in->getArray('user_id', 'int')) < 1){
									$users[] = $in->get('user_id');
								} else {
									$users = $in->getArray('user_id', 'int');
								}

								foreach ( $users as $user_id )
								{
										$usernames[] = $pdh->get('user', 'name', array($user_id));
										$userid[] = $user_id;
								}
                $names = implode(', ', $usernames);

                $confirm_text .= '<br /><br />' . $names.'<br /><br />';
								$confirm_text .= '<label><input type="checkbox" name="delete_associated_members" value="1"> '. $user->lang['delete_associated members'].'</label>';
            }
            else
            {
                message_die('No users were selected for deletion.');
            }
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
            'uri_parameter' => 'users',
            'url_id'        => ( sizeof($usernames) > 0 ) ? base64_encode(serialize($userid)) : (( $in->get('username') != "" ) ? base64_encode(serialize($in->get('username'))) : ''),
            'script_name'   => 'manage_users.php' . $SID)
        );

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit',
                'check'   => 'a_users_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_users_man'),
						'send_mail' => array(
                'name'    => 'send_mail',
                'process' => 'process_send_mail',
                'check'   => 'a_users_man'),
						'send_new_pw' => array(
                'name'    => 'send_new_pw',
                'process' => 'process_send_new_pw',
                'check'   => 'a_users_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_users_man'))
        );

        $this->assoc_params(array(
            'activate' => array(
                'name'    => 'mode',
								'value'		=> 'activate',
                'process' => 'process_activate',
                'check'   => 'a_users_man'),
						'deactivate' => array(
                'name'    => 'mode',
								'value'		=> 'deactivate',
                'process' => 'process_deactivate',
                'check'   => 'a_users_man'),
						'delete' => array(
                'name'    => 'delete',
								'value'		=> 'single',
                'process' => 'process_delete',
                'check'   => 'a_users_man'),
						'name' => array(
                'name'    => 'u',
                'process' => 'display_form',
                'check'   => 'a_users_man'),
				));
    }

    function error_check()
    {
        global $db, $user;

        // Singular Update
        if ( isset($_POST['submit']) )
        {
            // See if the user exists
            $sql = "SELECT au.*, u.*
                    FROM __users u
                    LEFT JOIN __auth_users au
                    ON (u.user_id = au.user_id)
                    WHERE u.user_id ='" . $_POST['user_id'][0] . "'";
            $result = $db->query($sql);
            if ( !$this->user_data = $db->fetch_record($result) )
            {
                message_die($user->lang['error_user_not_found']);
            }
            $db->free_result($result);

            // Error-check the form
            $this->change_username = false;
            if ( $_POST['username'] != $_POST['old_username'] )
            {
                // They changed the username, see if it's already registered
                $sql = "SELECT user_id
                        FROM __users
                        WHERE username='".$_POST['username']."'";
                if ( $db->num_rows($db->query($sql)) > 0 )
                {
                    $this->fv->errors['username'] = $user->lang['fv_already_registered_username'];
                }
                $this->change_username = true;
            }
            $this->change_password = false;
            if ( (!empty($_POST['new_user_password1'])) || (!empty($_POST['new_user_password2'])) )
            {
                $this->fv->matching_passwords('new_user_password1', 'new_user_password2', $user->lang['fv_match_password']);
                $this->change_password = true;
 
            }
            $this->fv->is_number(array(
                'user_alimit' => $user->lang['fv_number'],
                'user_elimit' => $user->lang['fv_number'],
                'user_ilimit' => $user->lang['fv_number'],
                'user_nlimit' => $user->lang['fv_number'],
                'user_rlimit' => $user->lang['fv_number'])
            );


            // Make sure any members associated with this account aren't associated with another account
            if ( (isset($_POST['member_id'])) && (is_array($_POST['member_id'])) )
            {
                // Build array of member_id => member_name
                $member_names = array();
                $sql = 'SELECT member_id, member_name
                        FROM __members
                        ORDER BY member_name';
                $result = $db->query($sql);
                while ( $row = $db->fetch_record($result) )
                {
                    $member_names[ $row['member_id'] ] = $row['member_name'];
                }
                $db->free_result($result);

                $sql = 'SELECT member_id
                        FROM __member_user
                        WHERE member_id IN (' . implode(', ', $_POST['member_id']) . ')
                        AND user_id != ' . $this->user_data['user_id'];
                $result = $db->query($sql);

                $fv_member_id = '';
                while ( $row = $db->fetch_record($result) )
                {
                    // This member's associated with another account
                    $fv_member_id .= sprintf($user->lang['fv_member_associated'], $member_names[ $row['member_id'] ]) . '<br />';
                }
                $db->free_result($result);

                if ( $fv_member_id != '' )
                {
                    $this->fv->errors['member_id'] = $fv_member_id;
                }
            }
        }
        // Mass Update
        elseif ( isset($_POST['update']) )
        {
        }
        // Mass Delete
        elseif ( isset($_POST['delete']) )
        {
        }
        elseif ( isset($_GET['u']) )
        {
            // See if the user exists
            $sql = "SELECT au.*, u.*
                    FROM __users u
                    LEFT JOIN __auth_users au
                    ON (u.user_id = au.user_id)
                    WHERE u.user_id='" . $_GET['u'] . "'";
            $result = $db->query($sql);
            if ( !$this->user_data = $db->fetch_record($result) )
            {
                message_die($user->lang['error_user_not_found']);
            }
            $db->free_result($result);
        }

        return $this->fv->is_error();
    }
		
		function process_send_new_pw(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl, $CharTools;
      global $SID, $user_id, $logs;
			
			
			$username = sanitize($in->get('username'));

			// Create a new activation key
			$user_key = $core->random_string(true);
			$key_len = 54 - (strlen($this->server_url));
			$key_len = ($key_len > 6) ? $key_len : 6;

			$user_key = substr($user_key, 0, $key_len);
			$user_password = $core->random_string(false);
			$user_salt = $user->generate_salt();
			$user_id = $in->getArray('user_id', 'int');
			
			$sql = "UPDATE __users
							SET user_newpassword='" . $user->encrypt_password($user_password, $user_salt).':'.$user_salt. "', user_key='" . $user_key . "'
							WHERE user_id='" . $db->escape($user_id[0]) . "'";
			if ( !$db->query($sql) )
			{
					message_die('Could not update password information', '', __FILE__, __LINE__, $sql);
			}
			$server_url  = $core->BuildLink().'register.php';
			//
			// Email them their new password
			//
			$email = new MyMailer($eqdkp_root_path);
			$bodyvars = array(
					'USERNAME'   => $row['username'],
					'DATETIME'   => date('m/d/y h:ia T', time()),
					'IPADDRESS'  => $user->ip_address,
					'U_ACTIVATE' => $server_url . '?mode=activate&key=' . $user_key,
					'USERNAME'   => $username,
					'PASSWORD'   => $user_password
			);

			if($email->SendMailFromAdmin($in->get('user_email'), $user->lang['email_subject_new_pw'], 'user_new_password.html', $bodyvars)) {
				$message = $user->lang['password_sent'];
			} else {
				$message =$user->lang['error_email_send'];
			}
			$link_list = array(
						sprintf( $user->lang['manage_user'], sanitize($_POST['username']))  => 'manage_users.php' . $SID.'&u='.sanitize($user_id[0]),
							$user->lang['manage_users'] => 'manage_users.php' . $SID);
        	$this->admin_die($message, $link_list);
		}
		
		
		function process_send_mail(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl, $CharTools;
      global $SID, $user_id, $logs;
			
					
				if ($in->get('body') != ""){	
					$mailcl = new MyMailer();
		
					$options = array(
						'template_type'		=> 'input',
					);
		
					//Set E-Mail-Options
					$mailcl->SetOptions($options);
					
					$recipient_mail = $in->get('user_email');
					$body = $in->get('body');
					$user_id = $in->getArray('user_id', 'int');
					//Make a smile ;-)
					$bbclass = new bbcode();
					$bbclass->SetSmiliePath($core->BuildLink()."libraries/jquery/core/images/editor/icons");
					
					$body = $bbclass->MyEmoticons($body); //Emoticons
					$body = $bbclass->toHTML($body); //Make normal BB-Codes into HTML
					$status = $mailcl->SendMailFromAdmin($recipient_mail, $in->get('subject'), $body, '');
					if ($status){
						$message = $user->lang['adduser_send_mail_suc'];
					} else {
						$message = $user->lang['email_subject_send_error'];
					}
					$link_list = array(
						sprintf( $user->lang['manage_user'], sanitize($_POST['username']))  => 'manage_users.php' . $SID.'&u='.sanitize($user_id[0]),
							$user->lang['manage_users'] => 'manage_users.php' . $SID);
        	$this->admin_die($message, $link_list);
				} else {
				 $message = $user->lang['adduser_send_mail_error_fields'];
				 $link_list = array(
           sprintf( $user->lang['manage_user'], sanitize($_POST['username']))  => 'manage_users.php' . $SID.'&u='.sanitize($user_id[0]),
            $user->lang['manage_users'] => 'manage_users.php' . $SID);
        $this->admin_die($message, $link_list);
				}
			
		}
		
		function process_activate(){
			global $db, $core, $in, $pdh, $user;
			
				$db->query("UPDATE __users SET user_active = '1' WHERE user_id = '".$db->escape($in->get('u'))."'");
				$username = $pdh->get('user', 'name', array($in->get('u')));
				$core->message(sprintf($user->lang['user_activate_success'], sanitize($username)), $user->lang['success'], 'green');
				
				$this->display_list();
		}
		
		function process_deactivate(){
			global $db, $core, $in, $pdh, $user;
				
				if (($user->data['user_id'] == $in->get('u')) || (!$user->check_group(2, false) && $user->check_group(2, false, $in->get('u')))){
					$this->display_list();
				} else {
							
					$db->query("UPDATE __users SET user_active = '0' WHERE user_id = '".$db->escape($in->get('u'))."'");
					$username = $pdh->get('user', 'name', array($in->get('u')));
					$core->message(sprintf($user->lang['user_deactivate_success'], sanitize($username)), $user->lang['success'], 'green');
					
					$this->display_list();
				}
		}
		

    // ---------------------------------------------------------
    // Process Submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl, $CharTools;
        global $SID, $user_id, $logs, $html;

        $user_id = $this->user_data['user_id'];
        $_POST = htmlspecialchars_array($_POST);

        //
        // Build the query
        //
        // User settings
        $sql = "UPDATE __users
                SET";
        if ( $this->change_username )
        {
            $sql .= " username='".$_POST['username']."', ";
        }
        if ( $this->change_password )
        {
            $new_salt = $user->generate_salt();
						$sql .= " user_password='".$user->encrypt_password($_POST['new_user_password1'], $new_salt).':'.$new_salt."', ";
        }
        $sql .= " user_email='".$_POST['user_email']."', ";

        $sql .= " user_alimit='".$_POST['user_alimit']."', user_elimit='".$_POST['user_elimit']."', user_ilimit='".$_POST['user_ilimit']."',
                  user_nlimit='".$_POST['user_nlimit']."', user_rlimit='".$_POST['user_rlimit']."', ";

        $sql .= " user_lang='".$_POST['user_lang']."', user_style='".$_POST['user_style']."',
                  user_active='".$_POST['user_active']."'";

        $sql .= " WHERE user_id='".$this->user_data['user_id']."'";

        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not update user information', '', __FILE__, __LINE__, $sql);
        }

        //User Details
        $query_ary['first_name'] = $_POST['first_name'];
        $query_ary['last_name'] = $_POST['last_name'];
        $query_ary['country'] = $_POST['country'];
        $query_ary['town'] = $_POST['town'];
        $query_ary['state'] = $_POST['state'];
        $query_ary['ZIP_code'] = $in->get('ZIP_code', 0);
        $query_ary['phone'] = $_POST['phone'];
        $query_ary['cellphone'] = $_POST['cellphone'];
        $query_ary['address'] = $_POST['address'];
        $query_ary['allvatar_nick'] = $_POST['allvatar_nick'];
        $query_ary['icq'] = $_POST['icq'];
        $query_ary['skype'] = $_POST['skype'];
        $query_ary['msn'] = $_POST['msn'];
        $query_ary['irq'] = $_POST['irq'];
        $query_ary['gender'] = $_POST['gender'];
        $query_ary['birthday'] = $_POST['birthday'];
				$query_ary['user_date_time']		= $in->get('user_date_time', $user->lang['style_time']);
				$query_ary['user_date_short']		= $in->get('user_date_short', $user->lang['style_date_short']);
				$query_ary['user_date_long']		= $in->get('user_date_long', $user->lang['style_date_long']);
				$query_ary['app_key']						= $in->get('app_key', $core->random_string());
				$query_ary['app_use']						= $in->get('app_use', 0);
				
				$privArray = array();
        $privArray['priv_set'] = $_POST['priv_set'];
       	$privArray['priv_phone'] = $_POST['priv_phone'];
        $privArray['priv_nosms'] = $_POST['priv_nosms'];
				$privArray['priv_bday'] = $_POST['priv_bday'];
				$privArray['priv_gallery'] = $_POST['priv_gallery'];
        $query_ary['privacy_settings'] =  serialize($privArray);
				
				$customArray = array();
				$customArray['user_avatar'] = $in->get('user_avatar');
				$customArray['work']				= $in->get('work');
				$customArray['interests'] 	= $in->get('interests');
				$customArray['hardware'] 		= $in->get('hardware');
				$customArray['twitter'] 		= $in->get('twitter');	
				$customArray['facebook'] 		= $in->get('facebook');	
				$customArray['hide_shop'] 			= $in->get('hide_shop');	
				$customArray['hide_mini_games'] = $in->get('hide_mini_games');	
        $query_ary['custom_fields'] =  serialize($customArray);
				
				$plugin_settings = array();
				if (is_array($pm->get_menus('settings'))){
					foreach ($pm->get_menus('settings') as $plugin => $values){
						foreach ($values as $key=>$setting){
							$name = $setting['name'];
							$setting['name'] = $plugin.':'.$setting['name'];
							$plugin_settings[$plugin][$name] = $html->widget_return($setting);
						}		
					}
				}

				$query_ary['plugin_settings']	= serialize($plugin_settings);

        $query = $db->build_query('UPDATE', $query_ary);
        $sql = 'UPDATE __users SET ' . $query . " WHERE user_id = '" . $this->user_data['user_id'] . "'";
        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not update user Detail information', '', __FILE__, __LINE__, $sql);
        }
			
        // Permissions
        $auth_defaults = $acl->get_auth_defaults();
				$superadm_only = $acl->get_superadmin_only_permissions();
				//The Group-Memberships of the admin who has submitted this form
				$adm_memberships   = $acl->get_user_group_memberships($user->data['user_id']);			
				//If the admin is not Superadmin, unset the superadmin-permissions
				if (!isset($adm_memberships[2])){
					foreach ($superadm_only as $superperm){
						unset($auth_defaults[$superperm]);
					}
				}
				
        foreach ( $auth_defaults as $auth_value => $auth_setting )
        {
            $r_auth_id    = $acl->get_auth_id($auth_value);
            $r_auth_value = $auth_value;

            $chk_auth_value = ( $user->check_auth($r_auth_value, false, $user_id, false) ) ? 'Y' : 'N';
            $db_auth_value  = ( isset($_POST[$r_auth_value]) )                      ? 'Y' : 'N';

            if ( $chk_auth_value != $db_auth_value )
            {
               $this->update_auth_users($r_auth_id, $db_auth_value);
            }
        }
        $db->free_result($result);

        $CharTools->updateConnection($in->getArray('member_id', 'int'), $user_id);
				
				// User-Groups
				$groups = $in->getArray('user_groups', 'int');
				$group_list = $pdh->get('user_groups', 'id_list', 0);
				$pdh->put('user_groups_users', 'delete_user_from_groups', array($this->user_data['user_id'], $group_list));
				$pdh->put('user_groups_users', 'add_user_to_groups', array($this->user_data['user_id'], $groups));
			
        //
        // Logging
        //
        $log_action = array(
            'header'       => '{L_ACTION_USER_UPDATED}',
            '{L_USER}'   => $_POST['username']);

				$logs->add($log_action['header'], $log_action);
        // See if any plugins need to update the DB
        $pm->do_hooks('/admin/manage_users.php?action=update');

        // E-mail the user if he/she was activated by the admin and admin activation was set in the config
        if ( ( $core->config['account_activation'] == 2 ) && ( $this->user_data['user_active'] < $_POST['user_active'] ) )
        {
            
            //
            // Email them their new password
						//
						$email = new MyMailer($eqdkp_root_path);
						$email->Set_Language($_POST['user_lang']);
						$bodyvars = array(
							'USERNAME' => $_POST['username'],
							'PASSWORD' => $user->lang['email_encrypted']
							);
						if($email->SendMailFromAdmin($_POST['user_email'], $user->lang['email_subject_activation_none'], 'register_activation_none.html', $bodyvars)) {
						$email_success_message = $user->lang['account_activated_admin'];
					}
        }

        //create output message
        $message = $email_success_message . "\n" . $user->lang['update_settings_success'];

		 $link_list = array(
           sprintf( $user->lang['manage_user'], sanitize($_POST['username']))  => 'manage_users.php' . $SID.'&u='.sanitize($this->user_data['user_id']),
            $user->lang['manage_users'] => 'manage_users.php' . $SID);
        $this->admin_die($message, $link_list);
    }

    // ---------------------------------------------------------
    // Process (Mass) Delete
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $core, $user, $tpl, $pm, $in, $pdh;
        global $SID, $logs, $CharTools;

        if ( $in->get('users') != "")
        {
            $user_ids = unserialize(base64_decode($in->get('users')));
						
						$success_message = '';
						foreach ($user_ids as $usr){
							if (($user->data['user_id'] == $usr) || (!$user->check_group(2, false) && $user->check_group(2, false, $usr))){
								$this->display_list();
								return false;
							};
							$success_message .= sprintf($user->lang['admin_delete_user_success'], $pdh->get('user', 'name', array($usr))) . '<br />';
						}
					
            // Delete from auth_user
            $sql = 'DELETE FROM __auth_users
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);
   			
			// Delete from groups_users
            $sql = 'DELETE FROM __groups_users
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);
			
            // Delete from users
            $sql = 'DELETE FROM __users
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);

						//Delete associated Members
						if ($in->get('delete_associated_members')){
							foreach ($user_ids as $uid){
								$CharTools->updateConnection(array(), $uid);
								$members = $pdh->get('member_connection', 'connection', array($uid));
								foreach ($members as $member){
									$CharTools->DeleteChar($member);
								}
							}
						}

            foreach ( $user_ids as $usr )
            {
								$log_action = array(
								'header'       => '{L_ACTION_USER_DELETED}',
								'{L_USER}'   => $pdh->get('user', 'name', array($usr)));

								$logs->add($log_action['header'], $log_action);
								
            }

            $link_list = array(
                $user->lang['manage_users'] => 'manage_users.php' . $SID);

            $this->admin_die($success_message, $link_list);
        }
        else
        {
            message_die('No users were selected for deleting.');
        }
    }

    // ---------------------------------------------------------
    // Display
    // ---------------------------------------------------------
    function display_list()
    {
        global $db, $core, $user, $tpl, $pm, $game;
        global $SID, $eqdkp_root_path, $in, $acl, $pdh;
				$order = $in->get('o', '0.0');
				$red = 'RED'.str_replace('.', '', $order);

        $sort_order = array(
            0 => array('u.username ', 'u.username desc'),
            1 => array('u.user_email', 'u.user_email desc'),
            2 => array('u.user_lastvisit desc', 'u.user_lastvisit'),
            3 => array('u.user_active desc', 'u.user_active'),
            4 => array('s.session_id desc', 's.session_id')
        );

        $current_order = switch_order($sort_order);

        $total_users = $db->query_first('SELECT count(*) FROM __users');
        $start = ( isset($_GET['start']) ) ? $in->get('start', 0) : 0;

				// Build array of member_id => member_name
        $member_names = array();
				$member_class_id = array();
				$member_rank_id = array();


       $sql = 'SELECT m.member_id, m.member_name, m.member_class_id, r.rank_name, mu.user_id
				FROM ( __members m 
				INNER JOIN __member_ranks r ON m.member_rank_id = r.rank_id)
				LEFT JOIN __member_user mu ON m.member_id = mu.member_id
			';

       $result = $db->query($sql);
       $membercount = 1 ;
       while ( $row = $db->fetch_record($result) )
       {
		 		$a_members[$membercount]['member_id'] 	= $row['member_id'] ;
		 		$a_members[$membercount]['user_id'] = $row['user_id'] ;
		 		$a_members[$membercount]['member_name'] = $row['member_name'] ;
		 		$a_members[$membercount]['class_id'] = $row['member_class_id'] ;
				$a_members[$membercount]['class_name'] = $row['class_name'] ;
		 		$a_members[$membercount]['rank_name'] = $row['rank_name'] ;
		 		$membercount++;
       }

       $db->free_result($result);
        $sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active, s.session_id
                FROM (__users u
                LEFT JOIN __sessions s
                ON u.user_id = s.session_user_id)
                GROUP BY u.username
                ORDER BY ' . $current_order['sql'] . '
                LIMIT ' . $start . ' , 100';


        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not obtain user information', '', __FILE__, __LINE__, $sql);
        }


        while ( $row = $db->fetch_record($result) )
        {
            $user_online = ( !empty($row['session_id']) ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";
            $user_active = ( $row['user_active'] == '1' ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";

						if (is_array($a_members)){
							foreach ($a_members as $value)
							{
								if ($value['user_id'] == $row['user_id'])
								{
									$fv_member_id .= $game->decorate('classes', array($value['class_id'])).' <a href="'.$eqdkp_root_path.'admin/manage_members.php?s=&member_id='.$value['member_id'].'&mupd=true" style="color:'.$game->get_class_color($value['class_id']).'">'.$value['member_name']."</a> - ".$value['rank_name']."<br>";
								}
							}
						}
						$adm_memberships = $acl->get_user_group_memberships($user->data['user_id']);
						$user_memberships = $acl->get_user_group_memberships($row['user_id']);
						
            $tpl->assign_block_vars('users_row', array(
                'ROW_CLASS'     => $core->switch_row_class(),
                'U_MANAGE_USER' => 'manage_users.php'.$SID.'&amp;' . 'u' . '='.$row['user_id'],
								'U_DELETE' => 'manage_users.php'.$SID.'&amp;delete=single&amp;user_id='.$row['user_id'],
                'USER_ID'       => $row['user_id'],
                'CHARAKTER'			=> $fv_member_id,
                'NAME_STYLE'    => ( $user->check_auth('a_', false, $row['user_id']) ) ? 'font-weight: bold' : 'font-weight: none',
								'ADMIN_ICON'    => ( $user->check_auth('a_', false, $row['user_id']) ) ? '<img src="../images/admin/updates.png" title="'.$user->lang['admin'].'"> ' : '',
                'USERNAME'      => $row['username'],
                'U_MAIL_USER'   => ( !empty($row['user_email']) ) ? 'mailto:'.$row['user_email'] : '',
                'EMAIL'         => ( !empty($row['user_email']) ) ? $row['user_email'] : '&nbsp;',
                'LAST_VISIT'    => ($row['user_lastvisit']) ? date($user->style['date_time'], $row['user_lastvisit']) : '',
								'PROTECT_SUPERADMIN'	=> ((isset($user_memberships[2]) && !isset($adm_memberships[2])) || ($row['user_id'] == $user->data['user_id'])) ? true : false,
                'ACTIVE'        => $user_active,
								'ACTIVATE_ICON'	=> ( $row['user_active'] == '1' ) ? '<a href="manage_users.php'.$SID.'&mode=deactivate&u='.$row['user_id'].'" title="'.$user->lang['deactivate'].'"><img src="'.$eqdkp_root_path.'images/glyphs/disable.png"></a>' : '<a href="manage_users.php'.$SID.'&mode=activate&u='.$row['user_id'].'" title="'.$user->lang['activate'].'"><img src="'.$eqdkp_root_path.'images/glyphs/enable.png"></a>',
								
                'ONLINE'        => $user_online)
            );
            $fv_member_id = '';

        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Language
            'MY_OWN_ID' 		 		 => $user->data['user_id'],
            'L_MANAGE_USERS'     => $user->lang['manage_users'],
			 			'L_ACTIVE_CHAR'		 	 => $user->lang['associated_members'],
            'L_USERNAME'         => $user->lang['username'],
            'L_EMAIL'            => $user->lang['email_address'],
            'L_LAST_VISIT'       => $user->lang['last_visit'],
            'L_ACTIVE'           => $user->lang['active'],
            'L_ONLINE'           => $user->lang['online'],
						'L_ACTION'           => $user->lang['action'],
            'L_MASS_UPDATE'      => $user->lang['mass_update'],
            'L_MASS_UPDATE_NOTE' => $user->lang['mass_update_note'],
            'L_ACCOUNT_ENABLED'  => $user->lang['account_enabled'],
            'L_YES'              => $user->lang['yes'],
            'L_NO'               => $user->lang['no'],
						'L_EDIT'             => $user->lang['edit'],
						'L_DELETE'           => $user->lang['delete_user'],
            'L_MASS_DELETE'      => $user->lang['mass_delete'],
            'L_MM_User_Confirm'  => $user->lang['MM_User_Confirm'],
						'L_CREATE_USER'				=> $user->lang['user_creation'],

            // Sorting
            'O_USERNAME'   => $current_order['uri'][0],
            'O_EMAIL'      => $current_order['uri'][1],
            'O_LAST_VISIT' => $current_order['uri'][2],
            'O_ACTIVE'     => $current_order['uri'][3],
            'O_ONLINE'     => $current_order['uri'][4],

            // Page vars
            'U_MANAGE_USERS'      => 'manage_users.php' . $SID . '&amp;',
            'F_MASS_UPDATE'       => 'manage_users.php' . $SID,
            'START'               => $start,
            'LISTUSERS_FOOTCOUNT' => sprintf($user->lang['listusers_footcount'], $total_users, 100),
            'USER_PAGINATION'     => generate_pagination('manage_users.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_users, 100, $start))
        );

        $core->set_vars(array(
            'page_title'    => $user->lang['manage_users_title'],
            'template_file' => 'admin/manage_users.html',
            'display'       => true)
        );
    }
	// ---------------------------------------------------------
    // Process Display User
    // ----
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $eqdkp_root_path, $jquery, $acl;
        global $SID, $pdh, $html, $AjaxImageUpload, $pcache, $time, $pcomments, $in, $pm;

        $user_id = $this->user_data['user_id'];
				
				$logo_upload = new AjaxImageUpload;

        // Build the user permissions
        $user_permissions = $acl->get_permission_boxes();       
        // Add plugin checkboxes to our array
        $pm->generate_permission_boxes($user_permissions);
				//Get Superadmin-only-Permissions
				$superadm_only_perms = $acl->get_superadmin_only_permissions();
				//Get group-memberships of the user
				$memberships = $pdh->get('user_groups_users', 'memberships', array($this->user_data['user_id']));
				if (is_array($memberships)){
					foreach($memberships as $key=>$elem){
						$membersh[$elem] = $elem; 
					}
				}
				//Get group-permission of the admin
				$adm_memberships = $acl->get_user_group_memberships($user->data['user_id']);
				
        foreach ( $user_permissions as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );
			
            foreach ( $checks as $data )
            {
                if ($user->check_auth($data['CBNAME'], false, $user_id, false)){
									$perm = "user";
								}
								elseif (!$user->check_auth($data['CBNAME'], false, $user_id, false) && $user->check_auth($data['CBNAME'], false, $user_id) == true){
									$perm = "group";
								} else {
									$perm = false;
								}

								$tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],
                    'CBCHECKED' => ( $perm != false ) ? ' checked="checked"' : '',
										'S_IS_GROUP'	=> ( $perm == "group" ) ? true : false,			
										'S_SUPERADMIN_PERM'	=> (isset($superadm_only_perms[$data['CBNAME']]) && !isset($adm_memberships[2])) ? true : false,
										'CLASS'  => ( $perm != false ) ? 'positive' : 'negative',
                    'TEXT'      => $data['TEXT'])
                );
								
            }
        }

        unset($user_permissions);

		// Build member drop-down
		$freemember_data = $pdh->get('member_connection', 'freechars', array($this->user_data['user_id']));

		$mselect_list = $mselect_selected = array();
		foreach($freemember_data as $row){
			$mselect_list[$row['member_id']] = $row['member_name'];
			if($row['user_id'] == $this->user_data['user_id']){
				$mselect_selected[] = $row['member_id'];
			}
		}

			//Build Group-dropdown
			$groups = $pdh->aget('user_groups', 'name', 0, array($pdh->get('user_groups', 'id_list')));
			
			
			asort($groups);
			if (is_array($groups)){
				foreach ($groups as $key=>$elem){
	
					$usergroups[$key] = $elem;
				}
	
			}
		
				$privacy = unserialize($this->user_data['privacy_settings']) ;
				$custom = unserialize($this->user_data['custom_fields']) ;
				//Privacy - Phone numbers
        $priv_phone_array = array(
						'0'=>$user->lang['user_priv_all'],
						'1'=>$user->lang['user_priv_user'],
						'2'=>$user->lang['user_priv_admin'],
						'3'=>$user->lang['user_priv_no']
				);
				
				$priv_set_array = array(
						'0'=>$user->lang['user_priv_all'],
						'1'=>$user->lang['user_priv_user'],
						'2'=>$user->lang['user_priv_admin']
				);
				
				$image = ($custom['user_avatar'] != '') ? $pcache->FilePath('user_avatars/'.$custom['user_avatar'], 'eqdkp') : '';
				
				$tpl->add_js("
					function textbox_resize(id, pix)
					{
						var box			= document.getElementById(id);
						var new_height	= (parseInt(box.style.height) ? parseInt(box.style.height) : 300) + pix;
					
						if (new_height > 0)
						{
							box.style.height = new_height + 'px';
						}
					
						return false;
					}
				");
				
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
				
				$style_array = array();
        $result = $db->query("SELECT style_id, style_name FROM __styles WHERE enabled = '1' ORDER BY style_id DESC");
        while ($row = $db->fetch_record($result)){
        	$style_array[$row['style_id']] = $row['style_name'];
        }
        $db->free_result($result);
				
				
				//Comments
				$comm_settings = array(
					'attach_id' => $in->get('u'), 
					'page'      => 'userview',
					'auth'      => 'a_users_comment_w',
				);	  
				$pcomments->SetVars($comm_settings);
				
				$tpl->assign_vars(array(
					'COMMENTS'            => $pcomments->Show(),
					'ENABLE_COMMENTS'			=> true,
				));

				
        $tpl->assign_vars(array(
            // Form vars
            'F_SETTINGS'         => 'manage_users.php' . $SID,
            'S_CURRENT_PASSWORD' => false,
            'S_NEW_PASSWORD'     => true,
            'S_SETTING_ADMIN'    => true,
            'S_MU_TABLE'         => true,
						'S_CREATE_NEW_PW'			=> true,
						'JS_TABS'							=> $jquery->Tab_header('usersettings_tabs'),
						'JS_TAB_SELECT'				=> $jquery->Tab_Select('usersettings_tabs', 3),
						'USER_GROUP_SELECT'		=> $jquery->MultiSelect('user_groups', $usergroups, $membersh, 200, 300),
						'JS_CONNECTIONS'  => $jquery->MultiSelect('member_id', $mselect_list, $mselect_selected, '250'),
						'S_PROTECT_USER'			=> ($user->data['user_id'] == $this->user_data['user_id'] || (isset($membersh[2]) && !isset($adm_memberships[2]))) ? true : false,
						
						'PRIV_SET_DROPDOWN'	=> $html->DropDown('priv_set',  $priv_set_array, $privacy['priv_set']),
						'PRIV_PHONE_DROPDOWN'	=> $html->DropDown('priv_phone',  $priv_phone_array, $privacy['priv_phone']),
						'IMAGE_UPLOAD'	=> '<input type="hidden" name="user_avatar" id="user_avatar" value="'.$custom['user_avatar'].'">'.$logo_upload->Show('user_avatar', 'manage_users.php?performupload=true', $image, false),
						'S_IMAGE'	=> ($image != "") ? true: false,
						'IMAGE_DELETE'	=> 'manage_users.php'.$SID.'&uid='.stripslashes($_REQUEST['u']).'&deleteavatar=true',
			

            // Form values
            'MY_OWN_ID' 				  		=> " .. ".$user->data['user_id'],
            'USER_ID'                 => $this->user_data['user_id'],
            'USERNAME'                => $this->user_data['username'],
            'USER_EMAIL'              => $this->user_data['user_email'],
            'USER_ALIMIT'             => $this->user_data['user_alimit'],
            'USER_ELIMIT'             => $this->user_data['user_elimit'],
            'USER_ILIMIT'             => $this->user_data['user_ilimit'],
            'USER_NLIMIT'             => $this->user_data['user_nlimit'],
            'USER_RLIMIT'             => $this->user_data['user_rlimit'],
            'USER_ACTIVE_YES_CHECKED' => ( $this->user_data['user_active'] == '1' ) ? ' checked="checked"' : '',
            'USER_ACTIVE_NO_CHECKED'  => ( $this->user_data['user_active'] == '0' ) ? ' checked="checked"' : '',

            'FIRST_NAME' => stripslashes($this->user_data['first_name']),
            'LAST_NAME' => stripslashes($this->user_data['last_name']),
            'COUNTRY' => $this->user_data['country'],
            'TOWN' => stripslashes($this->user_data['town']),
            'STATE' => stripslashes($this->user_data['state']),
            'ZIP_CODE' => stripslashes($this->user_data['ZIP_code']),
            'PHONE' => stripslashes($this->user_data['phone']),
            'CELLPHONE' => stripslashes($this->user_data['cellphone']),
            'ADDRESS' => stripslashes($this->user_data['address']),
            'ALLVATAR_NICK' => stripslashes($this->user_data['allvatar_nick']),
            'ICQ' => stripslashes($this->user_data['icq']),
            'SKYPE' => stripslashes($this->user_data['skype']),
            'MSN' => stripslashes($this->user_data['msn']),
            'IRQ' => stripslashes($this->user_data['irq']),
            'GENDER' => stripslashes($this->user_data['gender']),
            'BIRTHDAY' => $jquery->Calendar('birthday', stripslashes($this->user_data['birthday']), '', array('change_fields'=>true, 'year_range'=>'-80:+0')),
						'USER_APP_KEY'								=> stripslashes($this->user_data['app_key']),
						'USER_APP_USE'								=> ($this->user_data['app_use'] == '1') ? 'checked' : '',
						'HARDWARE'	=> $custom['hardware'],
						'WORK'			=> $custom['work'],
						'INTERESTS'	=> $custom['interests'],
						'FACEBOOK'	=> $custom['facebook'],
						'TWITTER'	=> $custom['twitter'],
						'HIDE_SHOP'										=> ($custom['hide_shop']==1) ? 'checked' : '' ,
						'HIDE_MINI_GAMES'							=> ($custom['hide_mini_games']==1) ? 'checked' : '' ,
						'USER_DATE_TIME'							=> sanitize($user->data['user_date_time']),
						'USER_DATE_SHORT'							=> sanitize($user->data['user_date_short']),
						'USER_DATE_LONG'							=> sanitize($user->data['user_date_long']),
						
						
						'DD_LANGUAGE'									=> $html->DropDown('user_lang', $language_array, $user->data['user_lang']),
            'DD_STYLES'										=> $html->DropDown('user_style',  $style_array, $user->data['user_style']),
            'DD_TIMEZONES'								=> $html->DropDown('user_timezone', $time->timezones, $user->data['user_timezone']),
            'DD_COUNTRIES'								=> $html->DropDown('country',  $country_array, $user->data['country']),
            'DD_GENDER'										=> $html->DropDown('gender',  $gender_array, $user->data['gender']),

            // Language
            'L_REGISTRATION_INFORMATION' => $user->lang['registration_information'],
            'L_REQUIRED_FIELD_NOTE'      => $user->lang['required_field_note'],
            'L_USERNAME'                 => $user->lang['username'],
            'L_EMAIL_ADDRESS'            => $user->lang['email_address'],
            'L_NEW_PASSWORD'             => $user->lang['new_password'],
            'L_NEW_PASSWORD_NOTE'        => $user->lang['new_password_note'],
            'L_CONFIRM_PASSWORD'         => $user->lang['confirm_password'],
            'L_CONFIRM_PASSWORD_NOTE'    => $user->lang['confirm_password_note'],
            'L_PREFERENCES'              => $user->lang['view_options'],
            'L_ADJUSTMENTS_PER_PAGE'     => $user->lang['adjustments_per_page'],
            'L_EVENTS_PER_PAGE'          => $user->lang['events_per_page'],
            'L_ITEMS_PER_PAGE'           => $user->lang['items_per_page'],
            'L_NEWS_PER_PAGE'            => $user->lang['news_per_page'],
            'L_RAIDS_PER_PAGE'           => $user->lang['raids_per_page'],
            'L_LANGUAGE'                 => $user->lang['language'],
            'L_STYLE'                    => $user->lang['style'],
            'L_PREVIEW'                  => $user->lang['preview'],
            'L_PERMISSIONS'              => $user->lang['permissions'],
						'L_USER_GROUPS' 			 			 => $user->lang['user_groups'],
            'L_ADMIN_NOTE'             	 => $user->lang['s_admin_note'],
						'L_GROUP_NOTE'             	 => $user->lang['s_group_note'],
            'L_ACCOUNT_ENABLED'          => $user->lang['account_enabled'],
            'L_YES'                      => $user->lang['yes'],
            'L_NO'                       => $user->lang['no'],
            'L_ASSOCIATED_MEMBERS'       => $user->lang['associated_members'],
            'L_MEMBERS'                  => $user->lang['members'],
            'L_SUBMIT'                   => $user->lang['save'],
            'L_DELETE'                   => $user->lang['delete'],
            'L_RESET'                    => $user->lang['reset'],
						'L_BACK'                    => $user->lang['back'],
						'L_HARDWARE' 								=> $user->lang['user_hardware'],
						'L_WORK' 										=> $user->lang['user_work'],
						'L_INTERESTS' 							=> $user->lang['user_interests'],
            'L_USER_IMAGE' 							=> $user->lang['user_image'],
						'L_TIMEZONES'									=> $user->lang['user_timezones'],
						'L_COMMENTS'									=> $user->lang['news_comments'],
						
						'L_USER_PRIV' 							=> $user->lang['user_priv'],
            'L_USER_PRIV_SET' 					=> $user->lang['user_priv_set'],
            'L_USER_PRIV_SET_GLOBAL'		=> $user->lang['user_priv_set_global'],
						'L_USER_PRIV_BDAY' 					=> $user->lang['user_priv_bday'],
            'L_USER_PRIV_TEL_ALL' 			=> $user->lang['user_priv_tel_all'],
            'L_USER_PRIV_TEL_SMS' 			=> $user->lang['user_priv_tel_sms'],
						'L_USER_PRIV_GALLERY' 			=> $user->lang['user_priv_gallery'],
						'L_DATE_TIME'									=> $user->lang['adduser_date_time'],
						'L_DATE_SHORT'								=> $user->lang['adduser_date_short'],
						'L_DATE_LONG'									=> $user->lang['adduser_date_long'],
						'L_DATE_NOTE'									=> $user->lang['adduser_date_note'],

            'L_ADDUSER_FIRST_NAME' => $user->lang['adduser_first_name'],
            'L_ADDUSER_LAST_NAME' => $user->lang['adduser_last_name'],
            'L_ADDINFOS' => $user->lang['adduser_addinfos'],
            'L_ADDUSER_COUNTRY' => $user->lang['adduser_country'],
            'L_ADDUSER_TOWN' => $user->lang['adduser_town'],
            'L_ADDUSER_STATE' => $user->lang['adduser_state'],
            'L_ADDUSER_ZIP_CODE' => $user->lang['adduser_ZIP_code'],
            'L_ADDUSER_PHONE' => $user->lang['adduser_phone'],
            'L_ADDUSER_CELLPHONE' => $user->lang['adduser_cellphone'],
            'L_ADDUSER_FONEINFO' => $html->HelpTooltip($user->lang['adduser_foneinfo']),
						'L_ADDUSER_FONEINFO2' => $html->HelpTooltip($user->lang['adduser_cellinfo']),
            'L_ADDUSER_ADDRESS' => $user->lang['adduser_address'],
            'L_ADDUSER_ALLVATAR_NICK' => $user->lang['adduser_allvatar_nick'],
            'L_ADDUSER_ICQ' => $user->lang['adduser_icq'],
            'L_ADDUSER_SKYPE' => $user->lang['adduser_skype'],
            'L_ADDUSER_MSN' => $user->lang['adduser_msn'],
            'L_ADDUSER_IRQ' => $user->lang['adduser_irq'],
            'L_ADDUSER_GENDER' => $user->lang['adduser_gender'],
            'L_ADDUSER_GENDER_M' => $user->lang['adduser_gender'],
            'L_ADDUSER_GENDER_F' => $user->lang['adduser_gender'],
            'L_ADDUSER_BIRTHDAY' => $user->lang['adduser_birthday'],
						'L_ADDUSER_TWITTER'		=> $user->lang['adduser_twitter'],
						'L_ADDUSER_FACEBOOK'	=> $user->lang['adduser_facebook'],
						'L_SEND_NEW_PASSWORD'	=> $user->lang['adduser_send_new_pw'],
						'L_SEND_NEW_PASSWORD_NOTE'	=> $user->lang['adduser_send_new_pw_note'],
						'IRC_HELP'	=> $html->HelpTooltip($user->lang['register_help_irc']),
						'L_HIDE_SHOP'									=> $user->lang['adduser_hide_shop'],
						'L_HIDE_MINI_GAMES'						=> $user->lang['adduser_hide_mini_games'],
						'L_MISC'											=> $user->lang['adduser_misc'],
						
						'PRIV_TEL_ALL' => ($privacy['priv_tel_all'] ==1) ? 'checked' : '' ,
            'PRIV_TEL_CRIPT' => ($privacy['priv_tel_cript']==1) ? 'checked' : '' ,
            'PRIV_NOSMS' => ($privacy['priv_nosms']==1) ? 'checked' : '' ,
						'PRIV_GALLERY' => ($privacy['priv_gallery']==1) ? 'checked' : '' ,
            'PRIV_BDAY' => ($privacy['priv_bday']==1) ? 'checked' : '' ,
						
						
						'WYSIWYG_EDITOR' 		=> $jquery->wysiwyg('body'),
						'L_SEND_MAIL' => $user->lang['adduser_send_mail'],
						'L_BODY' => $user->lang['adduser_send_mail_body'],
						'L_SUBJECT' => $user->lang['adduser_send_mail_subject'],
						'L_SEND_MAIL2' => sprintf($user->lang['adduser_send_mail2'], $this->user_data['username']),
						'L_SEND'		=> $user->lang['maintenanceuser_send'],
						'S_SEND_MAIL'	=> true,
						
            // Form validation
						'REGISTRATION_ERROR_CLASS'		=> ($this->fv->generate_error('username') || $this->fv->generate_error('new_user_password1')) ? ' class="negative"' : '',
            'FV_USERNAME'     => $this->fv->generate_error('username'),
            'FV_NEW_PASSWORD' => $this->fv->generate_error('new_user_password1'),
            
						'PREFERENCES_ERROR_CLASS'			=> ($this->fv->generate_error('user_alimit') || $this->fv->generate_error('user_elimit') || $this->fv->generate_error('user_ilimit') || $this->fv->generate_error('user_nlimit') || $this->fv->generate_error('user_rlimit')) ? ' class="negative"' : '',
						'FV_USER_ALIMIT'  => $this->fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'  => $this->fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'  => $this->fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'  => $this->fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'  => $this->fv->generate_error('user_rlimit'),
						
						'MEMBER_ERROR_CLASS'			=> ($this->fv->generate_error('member_id')) ? ' class="negative"' : '',
            'FV_MEMBER_ID'    => $this->fv->generate_error('member_id')
            )
        );
				
				//Generate Plugin-Tabs
				if (is_array($pm->get_menus('settings'))){
					foreach ($pm->get_menus('settings') as $plugin => $values){
						$name = ($values['name']) ? $values['name'] : $user->lang[$plugin];
						$icon = ($values['icon']) ? $values['icon'] : $eqdkp_root_path.'images/admin/plugin.png';
						unset($values['name'], $values['icon']);
						
						$tpl->assign_block_vars('plugin_settings_row', array(
							'KEY'	=> $plugin,
							'PLUGIN'	=> $name,
							'ICON'	=> $icon,
						));
						$tpl->assign_block_vars('plugin_usersettings_div', array(
							'KEY'	=> $plugin,
							'PLUGIN'	=> $name,
						));

						foreach ($values as $key=>$setting){
							$helpstring =	($user->lang[$setting['help']]) ? $user->lang[$setting['help']] : $setting['help'];	    
							$help = (isset($setting['help'])) ? " ".$html->HelpTooltip($helpstring) : '';
							$setting['value']	= $setting['selected'] = $user->data['plugin_settings'][$plugin][$setting['name']];
							$setting['name'] = $plugin.'['.$setting['name'].']';
							
							$tpl->assign_block_vars('plugin_usersettings_div.plugin_usersettings', array(
								'NAME'	=> $user->lang[$setting['language']],
								'FIELD'	=> $html->widget($setting),
								'HELP'	=> $help,
								'S_TH'	=> ($setting['type'] == 'tablehead') ? true : false,
							));
						}			
					}
				}

        $pm->do_hooks('/admin/manage_users.php?action=settings');


        $core->set_vars(array(
            'page_title'    => $user->lang['manage_users'].': '.sanitize($this->user_data['username']),
            'template_file' => 'settings.html',
            'display'       => true)
        );
    }
		
		// ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function update_auth_users($auth_id, $auth_setting = 'N', $check_query_type = true)
    {
        global $db, $user_id;

        $upd_ins = ( $check_query_type ) ? $this->switch_upd_ins($auth_id, $user_id) : 'upd';

        if ( (empty($auth_id)) || (empty($user_id)) )
        {
            return false;
        }

        if ( $upd_ins == 'upd' )
        {
            if ($auth_setting == "N"){
				$sql = "DELETE FROM __auth_users 
						WHERE auth_id='".$auth_id."' 
						AND user_id='".$user_id."'";
					
			} else {
				$sql = "UPDATE __auth_users
						SET auth_setting='".$auth_setting."'
						WHERE auth_id='".$auth_id."'
						AND user_id='".$user_id."'";
			
			}
        }
        else
        {
            $sql = "INSERT INTO __auth_users
                    (user_id, auth_id, auth_setting)
                    VALUES ('".$user_id."','".$auth_id."','".$auth_setting."')";
        }

        if ( !($result = $db->query($sql)) )
        {
            return false;
        }
        return true;
    }

   function switch_upd_ins($auth_id, $user_id)
    {
        global $db;

        $sql = "SELECT o.auth_value
                FROM __auth_options o, __auth_users u
                WHERE (u.auth_id = o.auth_id)
                AND (u.user_id='".$user_id."')
                AND u.auth_id='".$auth_id."'";
        if ( $db->num_rows($db->query($sql)) > 0 )
        {
            return 'upd';
        }
        return 'ins';
    }
		
}

$manage_users = new Manage_users;
$manage_users->process();
?>