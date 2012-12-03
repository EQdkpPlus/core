<?php
/******************************
 * EQdkp
 * Copyright 2009
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * template_wrap.class.php
 * begin: 2009
 *
 * $Id$
 *
 ******************************/

// ---------------------------------------------------------
// Template Wrap class
// ---------------------------------------------------------
if ( !include_once($eqdkp_root_path . 'gplcore/template.class.php') )
{
    die(sprintf($lang['error_template'],$eqdkp_root_path));
}

class Template_Wrap extends Template
{
    var $error_message       = array();           // Array of errors      @var $error_message
    var $install_message     = array();           // Array of messages    @var $install_message
    var $header_inc          = false;             // Printed header?      @var $header_inc
    var $tail_inc            = false;             // Printed footer?      @var $tail_inc
    var $template_file       = '';                // Template filename    @var $template_file
    var $error_template_file = '';                // Error Tp filename    @var $template_file
    var $default_game 	     = '';				  // Defaultgame		  @var $default_game
    var $game_language 	     = '';				  // Defaultgame		  @var $default_game
    var $icon_error          = '<img src="../templates/install/images/file_conflict.gif">&nbsp;';
    var $icon_ok             = '<img src="../templates/install/images/file_up_to_date.gif">&nbsp;';

    function StatusIcon($mystat= 'ok')
    {
      return ($mystat=='ok') ? $this->icon_ok : $this->icon_error;
    }

    function template_wrap($template, $template_file, $error_template_file)
    {
      global $lang;
        $this->template_file = $template_file;
        $this->error_template_file = $error_template_file;

        $this->set_template($template);

        $this->assign_vars(array(
            'TYEAR'         => date('Y', time()),
            'MSG_TITLE'     => '',
            'MSG_TEXT'      => '',
            'L_BUTTON_BACK' => $lang['inst_button_back'])
        );

        $this->set_filenames(array(
            'body' => $this->template_file)
        );
    }

    function message_die($text = '', $title = '')
    {
      global $lang;
        $this->set_filenames(array(
            'body' => $this->error_template_file
          )
        );

        $this->assign_vars(array(
            'MSG_TITLE' => ( $title != '' ) ? $title : '&nbsp;',
            'MSG_TEXT'  => ( $text  != '' ) ? $text  : '&nbsp;',
            )
        );

        if ( !$this->header_inc )
        {
            $this->page_header();
        }

        $this->page_tail();
    }

    function message_append($message)
    {
        $this->install_message[ sizeof($this->install_message) + 1 ] = $message;
    }

    function message_out($die = false)
    {
      global $lang;
        sort($this->install_message);
        reset($this->install_message);

        $install_message = implode('<br /><br />', $this->install_message);

        if ( $die )
        {
            $this->message_die($install_message, (( sizeof($this->install_message) == 1 ) ? $lang['installation_message'] : $lang['installation_messages']));
        }
        else
        {
            $this->assign_vars(array(
                'MSG_TITLE' => (( sizeof($this->install_message) == 1 ) ? $lang['installation_message'] : $lang['installation_messages']),
                'MSG_TEXT'  => $install_message)
            );
        }
    }

    function error_append($error)
    {
      global $pdl;
        $pdl->log("install_error", $error);
    }

    function error_out($die = false)
    {
      global $lang, $pdl, $user, $core;
        $error_message = $pdl->get_html_log(3);
        $error_count = $pdl->get_log_size(3);

        if ( $die )
        {
            $this->message_die($error_message, (( $error_count == 1 ) ? $lang['error'] : $lang['errors']));
        }
        else
        {
        	$log = $pdl->get_log();
        	$cc = 0;
        	foreach($log as $type => $entries) {
                $cc++;
        		$this->assign_block_vars('debug_types', array(
        			'ID' => $cc,
        			'TYPE' => $type)
        		);
        		foreach($entries as $entry) {
        			$this->assign_block_vars('debug_types.debug_messages', array('MESSAGE' => $pdl->html_format_log_entry($type, $entry), 'ROW_CLASS' => $core->switch_row_class()));
        		}
        	}
            $this->assign_vars(array(
                'MAX_ID'  => $cc,
                'L_CLICK' => $user->lang['click2show'])
            );
        }
    }

    function page_header()
    {
        global $STEP, $lang, $user, $eqdkp_root_path, $core;
        $this->header_inc = true;
		$this->assign_var('L_ADMIN_PANEL', $user->lang['admin_acp']);
		$this->assign_var('U_ACP', $eqdkp_root_path.'admin/index.php');
		$this->assign_var('L_ADMIN_PANEL', $user->lang['admin_acp']);
		$this->assign_var('L_MMODE', $user->lang['maintenance_mode']);
		$this->assign_var('L_TASK_MANAGER', $user->lang['task_manager']);
		$this->assign_var('U_MMODE', $eqdkp_root_path.'maintenance/task_manager.php');
		
		$this->assign_var('L_ACTIVATE_INFO', $user->lang['activate_info']);
		$this->assign_var('L_ACTIVATE_MMODE', $user->lang['activate_mmode']);
		$this->assign_var('L_LEAVE_MMODE', $user->lang['leave_mmode']);
		$this->assign_var('L_DEACTIVATE_MMODE', $user->lang['deactivate_mmode']);
		$this->assign_var('S_MMODE_ACTIVE', ($core->config['pk_maintenance_mode'] == 1) ?  true : false);
		$this->assign_var('MAINTENANCE_MESSAGE', $core->config['pk_maintenance_message']);
		$this->assign_var('S_SPLASH', ($_GET['splash'] == 'true') ? true : false);
		if ($_GET['splash'] == 'true') {
			$this->assign_var('L_SPLASH_WELCOME', $user->lang['splash_welcome']);
			$this->assign_var('L_SPLASH_DESC', $user->lang['splash_desc']);
			$this->assign_var('L_SPLASH_NEW', $user->lang['splash_new']);
			$this->assign_var('L_START_TOUR', $user->lang['start_tour']);
			$this->assign_var('L_JUMP_TOUR', $user->lang['jump_tour']);
			$this->assign_var('L_06_IMPORT', $user->lang['06_import']);
			$this->assign_var('L_GUILD_IMPORT', $user->lang['guild_import']);
		}
        /*
        $now = gmdate('D, d M Y H:i:s', time()) . ' GMT';
        @header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        @header('Last-Modified: ' . $now);
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Content-Type: text/html; charset=iso-8859-1');
        */
    }

    function page_tail()
    {
        global $DEFAULTS, $db, $lang, $pdl, $core;

        $this->assign_var('S_SHOW_BUTTON', true);

        if ( sizeof($this->install_message) > 0 )
        {
            $this->message_out(false);
        }

        if($pdl->get_log_size(3) > 0)//if ( sizeof($this->error_message) > 0 )
        {
            $this->assign_var('S_SHOW_BUTTON', false);
            $this->error_out(false);
        }

        $this->assign_var('EQDKP_VERSION', $core->config['plus_version']);

        if ( is_object($db) )
        {
            $db->close_db();
        }
				
				$this->display('header');
        $this->display('body');
        $this->display('footer');
        $this->destroy();

        exit;
    }
}
?>