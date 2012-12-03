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

class add_user extends EQdkp_Admin
{
    var $user_data       = array();     // Holds user data if 'name' is set               @var user_data

    function add_user()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();


        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit',
                'check'   => 'a_users_man'),

						
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_users_man'))
        );
				
				// Data to be put into the form
        // If it's not in POST, we get it from config defaults
        $this->data = array(
            'username'    => post_or_db('username'),
            'user_email'  => post_or_db('user_email'),
						'first_name'  => post_or_db('first_name'),
						'last_name'	  => post_or_db('last_name'),
						'gender'	  	=> post_or_db('gender'),
						'country'	  	=> post_or_db('country'),
			
            'user_alimit' => post_or_db('user_alimit', $core->config, 'default_alimit'),
            'user_elimit' => post_or_db('user_elimit', $core->config, 'default_elimit'),
            'user_ilimit' => post_or_db('user_ilimit', $core->config, 'default_ilimit'),
            'user_nlimit' => post_or_db('user_nlimit', $core->config, 'default_nlimit'),
            'user_rlimit' => post_or_db('user_rlimit', $core->config, 'default_rlimit'),
            'user_lang'   => post_or_db('user_lang',   $core->config, 'default_lang'),
            'user_style'  => post_or_db('user_style',  $core->config, 'default_style')
        );

    }

    function error_check()
    {
        global $db, $user, $in;

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
						if ($in->get('user_password1') != "" && $in->get('user_password2') != "" ){
            	$this->fv->matching_passwords('user_password1', 'user_password2', $user->lang['fv_match_password']);
						}

            $this->fv->is_email_address('user_email', $user->lang['fv_invalid_email']);

            $this->fv->is_filled(array(
                'username'       => $user->lang['fv_required_user'],
                'user_email'     => $user->lang['fv_required_email'],
                'first_name'     => $user->lang['fv_required'],
                'gender'     	 		=> $user->lang['fv_required'],
                'country'     	 => $user->lang['fv_required'],
                )
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
                        WHERE member_id IN (' . implode(', ', $_POST['member_id']) . ')';
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

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
        global $SID, $user_id, $CharTools, $logs;
				
				$password = ($in->get('user_password1') == "") ? $core->random_string() : $in->get('user_password1');
				
       //Insert the user into the DB
				$user_id = $pdh->put('user', 'insert_user', array('1', '', $password));
			
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
				
				$pdh->put('user_groups_users', 'add_user_to_groups', array($user_id, $groups));
			
        //
        // Logging
        //
        $log_action = array(
            'header'       => '{L_ACTION_USER_UPDATED}',
            '{L_USER}'   => $_POST['username']);

        $logs->add($log_action['header'], $log_action);

        // See if any plugins need to update the DB
        $pm->do_hooks('/admin/add_user.php?action=submit');
				
				
				$email = new MyMailer($eqdkp_root_path);
				$email->Set_Language($_POST['user_lang']);
				$bodyvars = array(
							'USERNAME' => $_POST['username'].'<br>'.$user->lang['password'].':'.$password,
							'PASSWORD' => $password,
							'GUILDTAG' => $core->config['guildtag'],
				);
				
				$email->SendMailFromAdmin($_POST['user_email'], $user->lang['email_subject_activation_none'], 'register_activation_none.html', $bodyvars);
				
				$message = sprintf( $user->lang['user_creation_success'], sanitize($_POST['username']));
		 		$link_list = array(
           sprintf( $user->lang['manage_user'], sanitize($_POST['username']))  => 'manage_users.php' . $SID.'&name='.sanitize($_POST['username']),
            $user->lang['manage_users'] => 'manage_users.php' . $SID);
        $this->admin_die($message, $link_list);
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
		

		// ---------------------------------------------------------
    // Process Display User-List
    // ----
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm, $eqdkp_root_path, $jquery, $acl;
        global $SID, $pdh, $html, $time, $in;
				
        // Build the user permissions
        $user_permissions = $acl->get_permission_boxes();       
        // Add plugin checkboxes to our array
        $pm->generate_permission_boxes($user_permissions);
				//Get Superadmin-only-Permissions
				$superadm_only_perms = $acl->get_superadmin_only_permissions();
				
				//Get group-permission of the admin
				$adm_memberships = $acl->get_user_group_memberships($user->data['user_id']);
				
        foreach ( $user_permissions as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );
			
            foreach ( $checks as $data )
            {

								$tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],	
										'S_SUPERADMIN_PERM'	=> (isset($superadm_only_perms[$data['CBNAME']]) && !isset($adm_memberships[2])) ? true : false,
										'CLASS'  => 'negative',
                    'TEXT'   => $data['TEXT'])
                );
								
            }
        }

        unset($user_permissions);


				// Build member drop-down
				$freemember_data = $pdh->get('member_connection', 'freechars', array(ANONYMOUS));
				
				$mselect_list = array();
				foreach($freemember_data as $row){
					$mselect_list[$row['member_id']] = $row['member_name'];
					
				}

				//Build Group_dropdown
				$groups = $pdh->aget('user_groups', 'name', 0, array($pdh->get('user_groups', 'id_list')));

				asort($groups);
				if (is_array($groups)){
					foreach ($groups as $key=>$elem){
		
						$usergroups[$key] = $elem;
					}
		
				}
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
				
				if (strlen($in->get('user_password1'))){
					$core->message($user->lang['adduser_passwordreset_note'], '', 'red');
				}
				
        $tpl->assign_vars(array(
            // Form vars
            'F_SETTINGS'         => 'add_user.php' . $SID,
            'S_CURRENT_PASSWORD' => false,
            'S_NEW_PASSWORD'     => false,
            'S_SETTING_ADMIN'    => true,
            'S_MU_TABLE'         => true,
						'S_PROTECT_USER'		=> true,
						
						'JS_TABS'							=> $jquery->Tab_header('usersettings_tabs'),
						'JS_TAB_SELECT'				=> $jquery->Tab_Select('usersettings_tabs', 0),
						'USER_GROUP_SELECT'		=> $jquery->MultiSelect('user_groups', $usergroups, array($pdh->get('user_groups', 'standard_group', array())), 200, 300),
						'USER_ACTIVE_YES_CHECKED' => 'checked="checked"',
						'JS_CONNECTIONS'  => $jquery->MultiSelect('member_id', $mselect_list, '', '250'),
						'PRIV_SET_DROPDOWN'	=> $html->DropDown('priv_set',  $priv_set_array, $privacy['priv_set']),
						'PRIV_PHONE_DROPDOWN'	=> $html->DropDown('priv_phone',  $priv_phone_array, $privacy['priv_phone']),

            'BIRTHDAY' 						=> $jquery->Calendar('birthday', '', '', array('change_fields'=>true, 'year_range'=>'-80:0')),
						'DD_LANGUAGE'									=> $html->DropDown('user_lang', $language_array, $core->config['default_lang']),
            'DD_STYLES'										=> $html->DropDown('user_style',  $style_array, $core->config['default_style']),
            'DD_TIMEZONES'								=> $html->DropDown('user_timezone', $time->timezones, $core->config['timezone']),
            'DD_COUNTRIES'								=> $html->DropDown('country',  $country_array),
            'DD_GENDER'										=> $html->DropDown('gender',  $gender_array),

            // Language
            'L_REGISTRATION_INFORMATION' => $user->lang['registration_information'],
            'L_REQUIRED_FIELD_NOTE'      => $user->lang['required_field_note'],
            'L_USERNAME'                 => $user->lang['username'],
            'L_EMAIL_ADDRESS'            => $user->lang['email_address'],
            'L_PASSWORD'             			=> $user->lang['password'],

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
            'L_MEMBERS'                  => $user->lang['chars'],
            'L_SUBMIT'                   => $user->lang['save'],
            'L_DELETE'                   => $user->lang['delete'],
            'L_RESET'                    => $user->lang['reset'],
						'PASSWORD_NOTE'							=> '<br>'.$user->lang['user_creation_password_note'],
						'L_HARDWARE' 								=> $user->lang['user_hardware'],
						'L_WORK' 										=> $user->lang['user_work'],
						'L_INTERESTS'								=> $user->lang['user_interests'],
            'L_USER_IMAGE' 							=> $user->lang['user_image'],
						'L_ADDUSER_TWITTER'					=> $user->lang['adduser_twitter'],
						'L_ADDUSER_FACEBOOK'				=> $user->lang['adduser_facebook'],
						'L_TIMEZONES'								=> $user->lang['user_timezones'],
						'L_BACK'										=> $user->lang['back'],
						
						'L_USER_PRIV' 			=> $user->lang['user_priv'],
            'L_USER_PRIV_SET' 		=> $user->lang['user_priv_set'],
            'L_USER_PRIV_SET_GLOBAL'=> $user->lang['user_priv_set_global'],
						'L_USER_PRIV_BDAY' => $user->lang['user_priv_bday'],
            'L_USER_PRIV_TEL_ALL' => $user->lang['user_priv_tel_all'],
            'L_USER_PRIV_TEL_SMS' 	=> $user->lang['user_priv_tel_sms'],
						'L_USER_PRIV_GALLERY' 	=> $user->lang['user_priv_gallery'],
						
						'USERNAME'    => $this->data['username'],
            'USER_EMAIL'  => $this->data['user_email'],
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
						'L_HIDE_SHOP'									=> $user->lang['adduser_hide_shop'],
						'L_HIDE_MINI_GAMES'						=> $user->lang['adduser_hide_mini_games'],
						'L_MISC'											=> $user->lang['adduser_misc'],
						'L_DATE_TIME'									=> $user->lang['adduser_date_time'],
						'L_DATE_SHORT'								=> $user->lang['adduser_date_short'],
						'L_DATE_LONG'									=> $user->lang['adduser_date_long'],
						'L_DATE_NOTE'									=> $user->lang['adduser_date_note'],

            // Form validation
						'PREFERENCES_ERROR_CLASS'			=> ($this->fv->generate_error('user_alimit') || $this->fv->generate_error('user_elimit') || $this->fv->generate_error('user_ilimit') || $this->fv->generate_error('user_nlimit') || $this->fv->generate_error('user_rlimit')) ? ' class="negative"' : '',
            'FV_USERNAME'     => $this->fv->generate_error('username'),
            'FV_USER_ALIMIT'  => $this->fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'  => $this->fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'  => $this->fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'  => $this->fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'  => $this->fv->generate_error('user_rlimit'),
            
						'MEMBER_ERROR_CLASS'			=> ($this->fv->generate_error('member_id')) ? ' class="negative"' : '',
						'FV_MEMBER_ID'    => $this->fv->generate_error('member_id'),
						
						'REGISTRATION_ERROR_CLASS'		=> ($this->fv->generate_error('username') || $this->fv->generate_error('user_password') || $this->fv->generate_error('new_user_password1') || $this->fv->generate_error('user_email')) ? ' class="negative"' : '',
            'FV_USER_PASSWORD' => $this->fv->generate_error('user_password1'),
            'FV_USER_EMAIL'    => $this->fv->generate_error('user_email'),
						
						'ADDINFOS_ERROR_CLASS' => ($this->fv->generate_error('first_name') || $this->fv->generate_error('gender') || $this->fv->generate_error('country')) ? ' class="negative"' : '',
            'FV_FIRST_NAME' 	=> $this->fv->generate_error('first_name'),
            'FV_GENDER' 		=> $this->fv->generate_error('gender'),
            'FV_COUNTRY' 		=> $this->fv->generate_error('country'),
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


        $core->set_vars(array(
            'page_title'    => $user->lang['title_manageusers'],
            'template_file' => 'settings.html',
            'display'       => true)
        );
    }
}

$add_user = new add_user;
$add_user->process();
?>