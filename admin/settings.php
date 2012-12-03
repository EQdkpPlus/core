<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * settings.php
 * Began: Sat December 28 2002
 *
 * $Id: config.php 1287 2008-01-13 20:57:37Z wallenium $
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
require_once($eqdkp_root_path . 'common.php');

class EQdkp_Config extends EQdkp_Admin
{
    function eqdkp_config()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit',
                'check'   => 'a_config_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_config_man'))
        );
    }

    function error_check()
    {
        global $user;

        $this->fv->is_number(array(
            'default_alimit'     => $user->lang['fv_number'],
            'default_elimit'     => $user->lang['fv_number'],
            'default_ilimit'     => $user->lang['fv_number'],
            'default_nlimit'     => $user->lang['fv_number'],
            'default_rlimit'     => $user->lang['fv_number'],
            'active_point_adj'   => $user->lang['fv_number'],
            'inactive_point_adj' => $user->lang['fv_number'])
        );

        $this->fv->is_within_range('default_alimit', 1, 1000);
        $this->fv->is_within_range('default_elimit', 1, 1000);
        $this->fv->is_within_range('default_ilimit', 1, 1000);
        $this->fv->is_within_range('default_nlimit', 1, 1000);
        $this->fv->is_within_range('default_rlimit', 1, 1000);

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix,$pcache,$_HMODE;

        $_POST['parsetags'] = preg_replace('#<(.+)>([[:space:]])?#', "\\1", $_POST['parsetags']);
        $_POST = htmlspecialchars_array($_POST);

        // Update each config setting
        $eqdkp->config_set(array(
            'guildtag'           => $_POST['guildtag'],
            'parsetags'          => $_POST['parsetags'],
            'main_title'         => $_POST['main_title'],
            'sub_title'          => $_POST['sub_title'],
            'dkp_name'           => $_POST['dkp_name'],
            'default_game'       => $_POST['default_game'],
            'default_game_overwrite'       => $_POST['default_game_overwrite'],
            'game_language'      => $_POST['game_language'],

            'default_locale'     => $_POST['default_locale'],
            'account_activation' => ( isset($_POST['account_activation']) ) ? $_POST['account_activation'] : '0',
            'default_alimit'     => intval($_POST['default_alimit']),
            'default_elimit'     => intval($_POST['default_elimit']),
            'default_ilimit'     => intval($_POST['default_ilimit']),
            'default_nlimit'     => intval($_POST['default_nlimit']),
            'default_rlimit'     => intval($_POST['default_rlimit']),
            'default_lang'       => $_POST['default_lang'],
            'default_style'      => intval($_POST['default_style']),
            'hide_inactive'      => ( isset($_POST['hide_inactive']) ) ? $_POST['hide_inactive'] : '0',
            'inactive_period'    => intval($_POST['inactive_period']),
            'active_point_adj'   => $_POST['active_point_adj'],
            'inactive_point_adj' => $_POST['inactive_point_adj'],
            'session_length'     => intval($_POST['session_length']),
            'admin_email'        => $_POST['admin_email'],
            'start_page'         => $_POST['start_page'])
        );
        
        //Save new logo
        if ($_POST['logo_path'] <> $user->style['logo_path'] ) 
        {
        	$newpath = $pcache->BuildLink().$pcache->FileLink($_POST['logo_path'],'eqdkp','logo') ;    
        	$sql = "UPDATE __style_config SET logo_path = '".$newpath."' ";         	
        	$result = $db->query($sql);
        }
        
	   	if (!$_HMODE) 
        {
        	// Update each config setting
        	$eqdkp->config_set(array(
            'server_name'        => $_POST['server_name'],            
            'server_port'        => intval($_POST['server_port']),
            'server_path'        => $_POST['server_path'],
            'enable_gzip'        => ( isset($_POST['enable_gzip']) ) ? $_POST['enable_gzip'] : '0',
            'cookie_domain'      => $_POST['cookie_domain'],
            'cookie_name'        => $_POST['cookie_name'],
            'cookie_path'        => $_POST['cookie_path'] ));       
        }

        $current_game = $eqdkp->config['default_game'];
        $curr_gamelang = ($_POST['game_language'] && $_POST['game_language'] != $eqdkp->config['game_language']) ? $_POST['game_language'] : $eqdkp->config['game_language'];
       if (($_POST['default_game'] != $current_game || $_POST['game_language'] != $eqdkp->config['game_language']))
       {
		  $set_defaultgame = $eqdkp->root_path . 'games/'.$_POST['default_game'].'/index.php' ;

      if (file_exists($set_defaultgame))
      {
        include($set_defaultgame);
        $manage = new Manage_Game;
        include($eqdkp->root_path.'pluskernel/include/games.class.php');
        $manage->do_it(false, $curr_gamelang);
        redirect("admin/settings.php");
		  }else
		  {
	        d($set_defaultgame);
	      }
	   }

        // Permissions
        $sql = 'SELECT auth_id, auth_value
                FROM ' . AUTH_OPTIONS_TABLE . '
                ORDER BY auth_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->update_auth_default($row['auth_value'], ( isset($_POST[$row['auth_value']]) ) ? 'Y' : 'N');
        }

        header('Location: settings.php' . $SID);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function update_auth_default($auth_value, $auth_default='N')
    {
        global $db;

        if ( empty($auth_value) )
        {
            return false;
        }

        $sql = 'UPDATE ' . AUTH_OPTIONS_TABLE . "
                SET auth_default='" . strip_tags(htmlspecialchars($auth_default)) . "'
                WHERE auth_value='" . $auth_value . "'";
        if ( !($result = $db->query($sql)) )
        {
            return false;
        }

        return true;
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $auth_defaults, $eqdkp_root_path, $style;

	$cur_hash = hash_filename("settings.php");
	//print"HASH::$cur_hash::<br>";

        //
        // Find default auth settings
        //
        $sql = 'SELECT auth_id, auth_default
                FROM ' . AUTH_OPTIONS_TABLE . '
                ORDER BY auth_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $auth_defaults[$row['auth_id']] = $row['auth_default'];
        }
        $db->free_result($result);

        //
        // Build the config permissions
        //
        $config_permissions = array(
            // Events
            $user->lang['events'] => array(
                array('CBNAME' => 'a_event_add',  'CBCHECKED' => A_EVENT_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_event_upd',  'CBCHECKED' => A_EVENT_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_event_del',  'CBCHECKED' => A_EVENT_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_event_list', 'CBCHECKED' => U_EVENT_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_event_view', 'CBCHECKED' => U_EVENT_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Group adjustments
            $user->lang['group_adjustments'] => array(
                array('CBNAME' => 'a_groupadj_add', 'CBCHECKED' => A_GROUPADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_groupadj_upd', 'CBCHECKED' => A_GROUPADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_groupadj_del', 'CBCHECKED' => A_GROUPADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Individual adjustments
            $user->lang['individual_adjustments'] => array(
                array('CBNAME' => 'a_indivadj_add', 'CBCHECKED' => A_INDIVADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_indivadj_upd', 'CBCHECKED' => A_INDIVADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_indivadj_del', 'CBCHECKED' => A_INDIVADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Items
            $user->lang['items'] => array(
                array('CBNAME' => 'a_item_add',  'CBCHECKED' => A_ITEM_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_item_upd',  'CBCHECKED' => A_ITEM_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_item_del',  'CBCHECKED' => A_ITEM_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_item_list', 'CBCHECKED' => U_ITEM_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_item_view', 'CBCHECKED' => U_ITEM_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // News
            $user->lang['news'] => array(
                array('CBNAME' => 'a_news_add', 'CBCHECKED' => A_NEWS_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_news_upd', 'CBCHECKED' => A_NEWS_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_news_del', 'CBCHECKED' => A_NEWS_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Raids
            $user->lang['raids'] => array(
                array('CBNAME' => 'a_raid_add',  'CBCHECKED' => A_RAID_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_raid_upd',  'CBCHECKED' => A_RAID_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_raid_del',  'CBCHECKED' => A_RAID_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_raid_list', 'CBCHECKED' => U_RAID_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_raid_view', 'CBCHECKED' => U_RAID_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Turn-ins
            $user->lang['turn_ins'] => array(
                array('CBNAME' => 'a_turnin_add', 'CBCHECKED' => A_TURNIN_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>')
            ),
            // Members
            $user->lang['members'] => array(
                array('CBNAME' => 'a_members_man', 'CBCHECKED' => A_MEMBERS_MAN, 'TEXT' => '<b>' . $user->lang['manage'] . '</b>'),
                array('CBNAME' => 'u_member_list', 'CBCHECKED' => U_MEMBER_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_member_view', 'CBCHECKED' => U_MEMBER_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Manage
            $user->lang['manage'] => array(
                array('CBNAME' => 'a_config_man',  'CBCHECKED' => A_CONFIG_MAN,  'TEXT' => '<b>' . $user->lang['configuration'] . '</b>'),
                array('CBNAME' => 'a_plugins_man', 'CBCHECKED' => A_PLUGINS_MAN, 'TEXT' => '<b>' . $user->lang['plugins'] . '</b>'),
                array('CBNAME' => 'a_styles_man',  'CBCHECKED' => A_STYLES_MAN,  'TEXT' => '<b>' . $user->lang['styles'] . '</b>'),
                array('CBNAME' => 'a_users_man',   'CBCHECKED' => A_USERS_MAN,   'TEXT' => '<b>' . $user->lang['users'] . '</b>')
            ),
            // Logs
            $user->lang['logs'] => array(
                array('CBNAME' => 'a_logs_view', 'CBCHECKED' => A_LOGS_VIEW, 'TEXT' => '<b>' . $user->lang['view'] . '</b>')
            ),
						// Backup Database
						/*$user->lang['backup'] => array(
						    array('CBNAME' => 'a_backup', 'CBCHECKED' => A_BACKUP, 'TEXT' => '<b>' . $user->lang['backup_database'] . '</b>')
						)*/
        );

        // Add plugin checkboxes to our array
        $pm->generate_permission_boxes($config_permissions);

        foreach ( $config_permissions as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );

            foreach ( $checks as $data )
            {
                $tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],
                    'CBCHECKED' => option_checked($auth_defaults[$data['CBCHECKED']] == 'Y'),
                    'TEXT'      => $data['TEXT'])
                );
            }
        }
        unset($config_permissions);
        
                
        include_once($eqdkp_root_path.'libraries/AjaxImageUpload/AjaxImageUpload.class.php');
        $logo_upload = new AjaxImageUpload;
        
        $_auioptions = array(
          'filesize'  => '2048576',  // 1 MB
          'maxheight' => '600',
          'maxwidth'  => '600'
        );
        
		if($_GET['performupload'] == 'true')
		{
  			$logo_upload->PerformUpload('logo_path', 'eqdkp', 'logo',$_auioptions);
  			die();
		}

		if (strpos($user->style['logo_path'],'://') > 1) 
	    {
	    	$preview_image = $user->style['logo_path'];
	    		
	    }else 
	    {
	    	$preview_image = $eqdkp_root_path."templates/".$user->style['template_path']."/images/".$user->style['logo_path'] ;
	    }        
				
		
        $tpl->assign_vars(array(
            
        	'UCV_PICTURE'               => $logo_upload->Show('logo_path', 'settings.php?performupload=true', $preview_image,false), 
        	'STYLE_LOGO_PATH'			=> $user->style['logo_path'],
        
        	// Form vars
            'F_CONFIG'                  => 'settings.php' . $SID,

            // Form values
            'GUILDTAG'                  => $eqdkp->config['guildtag'],
            'PARSETAGS'                 => $eqdkp->config['parsetags'],
            'SERVER_NAME'               => $eqdkp->config['server_name'],
            'SERVER_PORT'               => $eqdkp->config['server_port'],
            'SERVER_PATH'               => $eqdkp->config['server_path'],
            'MAIN_TITLE'                => $eqdkp->config['main_title'],
            'SUB_TITLE'                 => $eqdkp->config['sub_title'],
            'DKP_NAME'                  => $eqdkp->config['dkp_name'],
            'ACTIVATION_NONE_CHECKED'   => ( $eqdkp->config['account_activation'] == USER_ACTIVATION_NONE )  ? ' checked="checked"' : '',
            'ACTIVATION_USER_CHECKED'   => ( $eqdkp->config['account_activation'] == USER_ACTIVATION_SELF )  ? ' checked="checked"' : '',
            'ACTIVATION_ADMIN_CHECKED'  => ( $eqdkp->config['account_activation'] == USER_ACTIVATION_ADMIN ) ? ' checked="checked"' : '',
            'DEFAULT_ALIMIT'            => $eqdkp->config['default_alimit'],
            'DEFAULT_ELIMIT'            => $eqdkp->config['default_elimit'],
            'DEFAULT_ILIMIT'            => $eqdkp->config['default_ilimit'],
            'DEFAULT_NLIMIT'            => $eqdkp->config['default_nlimit'],
            'DEFAULT_RLIMIT'            => $eqdkp->config['default_rlimit'],
            'HIDE_INACTIVE_YES_CHECKED' => ( $eqdkp->config['hide_inactive'] == '1' ) ? ' checked="checked"' : '',
            'HIDE_INACTIVE_NO_CHECKED'  => ( $eqdkp->config['hide_inactive'] == '0' ) ? ' checked="checked"' : '',
            'DEFAULT_GAME_OVERWRITE_YES_CHECKED'  => ( $eqdkp->config['default_game_overwrite'] == '1' ) ? ' checked="checked"' : '',
            'DEFAULT_GAME_OVERWRITE_NO_CHECKED'  => ( $eqdkp->config['default_game_overwrite'] == '0' ) ? ' checked="checked"' : '',
            'INACTIVE_PERIOD'           => $eqdkp->config['inactive_period'],
            'ACTIVE_POINT_ADJ'          => $eqdkp->config['active_point_adj'],
            'INACTIVE_POINT_ADJ'        => $eqdkp->config['inactive_point_adj'],
            'GZIP_YES_CHECKED'          => ( $eqdkp->config['enable_gzip'] == '1' ) ? ' checked="checked"' : '',
            'GZIP_NO_CHECKED'           => ( $eqdkp->config['enable_gzip'] == '0' ) ? ' checked="checked"' : '',
            'COOKIE_DOMAIN'             => $eqdkp->config['cookie_domain'],
            'COOKIE_NAME'               => $eqdkp->config['cookie_name'],
            'COOKIE_PATH'               => $eqdkp->config['cookie_path'],
            'SESSION_LENGTH'            => $eqdkp->config['session_length'],
            'ADMIN_EMAIL'               => $eqdkp->config['admin_email'],
            'DEFAULT_GAME'              => $eqdkp->config['default_game'],


            // Language (General Settings)
            'L_GENERAL_SETTINGS'          => $user->lang['general_settings'],
            'L_GUILDTAG'                  => $user->lang['guildtag'],
            'L_GUILDTAG_NOTE'             => $user->lang['guildtag_note'],
            'L_PARSETAGS'                 => $user->lang['parsetags'],
            'L_PARSETAGS_NOTE'            => $user->lang['parsetags_note'],
            'L_DOMAIN_NAME'               => $user->lang['domain_name'],
            'L_SERVER_PORT'               => $user->lang['server_port'],
            'L_SERVER_PORT_NOTE'          => $user->lang['server_port_note'],
            'L_SCRIPT_PATH'               => $user->lang['script_path'],
            'L_SCRIPT_PATH_NOTE'          => $user->lang['script_path_note'],
            'L_SITE_NAME'                 => $user->lang['site_name'],
            'L_SITE_DESCRIPTION'          => $user->lang['site_description'],
            'L_POINT_NAME'                => $user->lang['point_name'],
            'L_POINT_NAME_NOTE'           => $user->lang['point_name_note'],
            'L_ENABLE_ACCOUNT_ACTIVATION' => $user->lang['enable_account_activation'],
            'L_NONE'                      => $user->lang['none'],
            'L_USER'                      => $user->lang['user'],
            'L_ADMIN'                     => $user->lang['admin'],
            'L_ADJUSTMENTS_PER_PAGE'      => $user->lang['adjustments_per_page'],
            'L_EVENTS_PER_PAGE'           => $user->lang['events_per_page'],
            'L_ITEMS_PER_PAGE'            => $user->lang['items_per_page'],
            'L_NEWS_PER_PAGE'             => $user->lang['news_per_page'],
            'L_RAIDS_PER_PAGE'            => $user->lang['raids_per_page'],
            'L_DEFAULT_LANGUAGE'          => $user->lang['default_language'],
            'L_DEFAULT_GAME'              => $user->lang['default_game'],
            'L_DEFAULT_GAME_OVERWRITE'    => $user->lang['default_style_overwrite'],
            'L_DEFAULT_GAME_WARN'         => $user->lang['default_game_warn'],
            'L_DEFAULT_STYLE'             => $user->lang['default_style'],
            'L_DEFAULT_PAGE'              => $user->lang['default_page'],
            'L_DEFAULT_LOCALE'            => $user->lang['default_locale'],
            'L_PREVIEW'                   => $user->lang['preview'],
            'L_HIDE_INACTIVE'             => $user->lang['hide_inactive'],
            'L_HIDE_INACTIVE_NOTE'        => $user->lang['hide_inactive_note'],
            'L_INACTIVE_PERIOD'           => $user->lang['inactive_period'],
            'L_INACTIVE_PERIOD_NOTE'      => $user->lang['inactive_period_note'],
            'L_ACTIVE_POINT_ADJ'          => $user->lang['active_point_adj'],
            'L_ACTIVE_POINT_ADJ_NOTE'     => $user->lang['active_point_adj_note'],
            'L_INACTIVE_POINT_ADJ'        => $user->lang['inactive_point_adj'],
            'L_INACTIVE_POINT_ADJ_NOTE'   => $user->lang['inactive_point_adj_note'],
            'L_ENABLE_GZIP'               => $user->lang['enable_gzip'],
            'L_IN_DB'                     => $user->lang['in_database'],
            
            'L_LOGO_PATH'              => $user->lang['logo_path'],
            'L_LOGO_PATH_NOTE'         => $user->lang['logo_path_note'],
            'L_LOGO_PATH_CONFIG'         => $user->lang['logo_path_config'],
            

            // Language (Default Permissions)
            'L_DEFAULT_PERMISSIONS'      => $user->lang['default_permissions'],
            'L_DEFAULT_PERMISSIONS_NOTE' => $user->lang['default_permissions_note'],

            // Language (Cookie Settings)
            'L_COOKIE_SETTINGS' => $user->lang['cookie_settings'],
            'L_COOKIE_DOMAIN'   => $user->lang['cookie_domain'],
            'L_COOKIE_NAME'     => $user->lang['cookie_name'],
            'L_COOKIE_PATH'     => $user->lang['cookie_path'],
            'L_SESSION_LENGTH'  => $user->lang['session_length'],

            // Language (E-mail Settings)
            'L_EMAIL_SETTINGS' => $user->lang['email_settings'],
            'L_ADMIN_EMAIL'    => $user->lang['admin_email'],

            // Language
            'L_YES'    => $user->lang['yes'],
            'L_NO'     => $user->lang['no'],
            'L_SUBMIT' => $user->lang['submit'],
            'L_RESET'  => $user->lang['reset'])
        );

        //
        // Build language drop-down
        //
        if ( $dir = @opendir($eqdkp->root_path . 'language/') )
        {
            while ( $file = @readdir($dir) )
            {
                if ( (!is_file($eqdkp->root_path . 'language/' . $file)) && (!is_link($eqdkp->root_path . 'language/' . $file)) && valid_folder($file) )
                {
                    $tpl->assign_block_vars('lang_row', array(
                        'VALUE'    => $file,
                        'SELECTED' => ( $eqdkp->config['default_lang'] == $file ) ? ' selected="selected"' : '',
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
                'VALUE' => $row['style_id'],
                'SELECTED' => option_selected($eqdkp->config['default_style'] == $row['style_id'] ),
                'OPTION' => $row['style_name'])
            );
        }
        $db->free_result($result);


    //
    // Build game option drop-down
	// New for 1.3
	// Total hack job - I moved the class, race, and faction
	// info to the db, but I'm hardcoding what games I support
	// for the "button push" - what a tard I am :-)
	// To add a new game option, just copy the 4 lines below,
	// add them to the botton, increase the value of VALUE by 1,
	// and be sure to set OPTION to the EXACT SAME THING you put
	// in the == check in the SELECTED line and there must be
	// no spaces in the name, since the value below gets changed
	// to name.php and ran when you change it: for example,
	// if you change to WoW, this program will redirect you to
	// WoW.php and use that file to populate the database.
	//
	// Cheesy, but extensible and effective.


  // get the content of the games/ dir
  $games = array();
  if ( $dir = opendir($eqdkp->root_path . 'games/') )
  {
    while ( $d_plugin_code = @readdir($dir) )
    {
      $cwd = $eqdkp->root_path . 'games/'.$d_plugin_code.'/index.php'; // regenerate the link to the 'plugin'
      if((@is_file($cwd)) && valid_folder($d_plugin_code))
      {  // check if valid
        @array_push($games, $d_plugin_code);  // add to array
      }
    }
  }

  foreach ( $games as $game )
  {
    $tpl->assign_block_vars('game_row', array(
                'VALUE'    => $game,
                'SELECTED' => option_selected(strtolower($eqdkp->config['default_game']) == strtolower($game)),
                'OPTION'   => $game
    ));
  }
  unset($games);

  // build game language
  $glanguagearray = array(
                      'en'  => 'English',
                      'de'  => 'German',
                      'fr'  => 'French',
                      'es'  => 'Spanish',
                      'ru'  => 'Russian',
                      );
  foreach ( $glanguagearray as $gamelang=>$glangname){
    $tpl->assign_block_vars('gamelang_row', array(
                'VALUE'    => $gamelang,
                'SELECTED' => option_selected(strtolower($eqdkp->config['game_language']) == strtolower($gamelang)),
                'OPTION'   => $glangname
    ));
  }
  unset($glanguagearray);

    // Default locale drop-down
	// new for 1.3
	// Dont forget to change the install script -- maybe query the system for all supported
	// locales? that would break the "pretty" name of the locale (english, french, etc)
	// but would provide greater support

  $locale_array = array(
          'en_US'   => 'English',
          'de_DE'   => 'German',
          'fr_FR'   => 'French',
          'ru_RU'	=> 'Russian'
  );

  foreach ( $locale_array as $localss=>$langname )
  {
    $tpl->assign_block_vars('locale_row', array(
                'VALUE'    => $localss,
                'SELECTED' => option_selected(strtolower($eqdkp->config['default_locale']) == strtolower($localss)),
                'OPTION'   => $langname
    ));
  }
  unset($locale_array);

        //
        // Build start page drop-down
        //
        $menus = $eqdkp->gen_menus();
        $pages = array_merge($menus['menu1'], $menus['menu2']);
        unset($menus);

        foreach ( $pages as $page )
        {
            $link = preg_replace('#\?' . URI_SESSION . '\=([0-9A-Za-z]{1,32})?#', '', $page['link']);
            $link = preg_replace('#\.php&amp;#', '.php?', $link);

            $text = ( isset($user->data['username']) ) ? str_replace($user->data['username'], $user->lang['username'], $page['text']) : $page['text'];

            if($link != 'login.php?logout=true'){
              $tpl->assign_block_vars('page_row', array(
                  'VALUE'    => $link,
                  'SELECTED' => option_selected($eqdkp->config['start_page'] == $link),
                  'OPTION'   => $text)
              );
            }
            unset($link, $text);
        }

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['config_title'],
            'template_file' => 'admin/config.html',
            'display'       => true)
        );
    }
}

$eqdkp_config = new EQdkp_Config;
$eqdkp_config->process();
?>
