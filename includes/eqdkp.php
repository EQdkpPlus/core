<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * eqdkp.php
 * begin: Sat December 21 2002
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

/**
* EQdkp foundation class
* Common page functionality
* Available to all pages as $eqdkp
*/

class EQdkp
{
    // General vars
    var $config     = array();                  // Config values            @var config
    var $row_class  = 'row1';                   // Alternating row class    @var row_class

    // Output vars
    var $root_path         = './';              // Path to EQdkps root     @var root_path
    var $gen_simple_header = false;             // Use a simple header?     @var gen_simple_header
    var $page_title        = '';                // Page title               @var page_title
    var $template_file     = '';                // Template file to parse   @var template_file
    var $template_path     = '';                // Path to template_file    @var template_path
    var $extra_css         = '';                // Extra CSS styles         @var extra_css

    // Timer vars
    var $timer_start = 0;                       // Page timer start         @var timer_start
    var $timer_end   = 0;                       // Page timer end           @var timer_end

    function eqdkp($eqdkp_root_path = './')
    {
        // Start a script timer if were debugging
        if ( DEBUG )
        {
            $mc_split = split(' ', microtime());
            $this->timer_start = $mc_split[0] + $mc_split[1];
            unset($mc_split);
        }

        $this->root_path = $eqdkp_root_path;

        $this->config();
    }

    function config()
    {
        global $db;

        if ( !is_object($db) )
        {
            trigger_error('Database object not instantiated', E_USER_ERROR);
        }

        $sql = 'SELECT config_name, config_value
                FROM ' . CONFIG_TABLE;

        if ( !($result = $db->query($sql)) )
        {
            trigger_error('Could not obtain configuration information', E_USER_ERROR);
        }
        while ( $row = $db->fetch_record($result) )
        {
            if ( !is_numeric($row['config_name']) )
            {
                $this->config[$row['config_name']] = $row['config_value'];
            }
        }

        return true;
    }

    function config_set($config_name, $config_value='')
    {
        global $db;

        if ( is_object($db) )
        {
            if ( is_array($config_name) )
            {
                foreach ( $config_name as $d_name => $d_value )
                {
                    $this->config_set($d_name, $d_value);
                }
            }
            else
            {
                $sql = 'UPDATE ' . CONFIG_TABLE . "
                        SET config_value='".strip_tags(htmlspecialchars($config_value))."'
                        WHERE config_name='".$config_name."'";
                $db->query($sql);

                return true;
            }
        }

        return false;
    }

    function switch_row_class($set_new = true)
    {
        $row_class = ( $this->row_class == 'row1' ) ? 'row2' : 'row1';

        if ( $set_new )
        {
            $this->row_class = $row_class;
        }

        return $row_class;
    }

    /**
    * Set object variables
    * NOTE: If the last var is 'display' and the val is TRUE, EQdkp::display() is called
    *   automatically
    *
    * @var $var Var to set
    * @var $val Value for Var
    * @return bool
    */
    function set_vars($var, $val = '', $append = false)
    {
        if ( is_array($var) )
        {
            foreach ( $var as $d_var => $d_val )
            {
                $this->set_vars($d_var, $d_val);
            }
        }
        else
        {
            if ( empty($val) )
            {
                return false;
            }
            if ( ($var == 'display') && ($val === true) )
            {
                $this->display();
            }
            else
            {
                if ( $append )
                {
                    if ( is_array($this->$var) )
                    {
                        $this->{$var}[] = $val;
                    }
                    elseif ( is_string($this->$var) )
                    {
                        $this->$var .= $val;
                    }
                    else
                    {
                        $this->$var = $val;
                    }
                }
                else
                {
                    $this->$var = $val;
                }
            }
        }

        return true;
    }

    function display()
    {
        $this->page_header();
        $this->page_tail();
    }

    function page_header()
    {
        global $db, $user, $tpl, $pm, $conf_plus, $table_prefix;
        global $SID, $eqdkpplus_vcontrol;

        // Define a variable so we know the header's been included
        define('HEADER_INC', true);

        // Use gzip if available
        if ( $this->config['enable_gzip'] == '1' )
        {
            if ( (extension_loaded('zlib')) && (!headers_sent()) )
            {
                @ob_start('ob_gzhandler');
            }
        }

        $SID = ( isset($SID) ) ? $SID : '?' . URI_SESSION . '=';

        // Send the HTTP headers
        $now = gmdate('D, d M Y H:i:s', time()) . ' GMT';
        if ( defined('NO_CACHE') )
        {
            @header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            @header('Last-Modified: ' . $now);
            @header('Cache-Control: no-store, no-cache, must-revalidate');
            @header('Cache-Control: post-check=0, pre-check=0', false);
            @header('Pragma: no-cache');
            @header('Content-Type: text/html; charset=iso-8859-1');
        }
        else
        {
            @header('Last-Modified: ' . $now);
            @header('Content-Type: text/html; charset=iso-8859-1');
        }

				// Additions for PLUS		
				include($this->root_path.'pluskernel/include/update.class.php');
				include($this->root_path.'pluskernel/include/init.class.php');
				$page 	= new InitPlus();
				$update = new UpdateCheck(
						'http://eqdkp.corgan-net.de/vcheck/version.php',
						'eqdkp.corgan-net.de',
						$eqdkpplus_vcontrol,
						array('Security Update' => 'red'),
						EQDKPPLUS_VERSION,
						EQDKPPLUS_AUTHOR,
						EQDKPPLUS_AUTHOR_URL
					);
				$onloadarray = $update->OnLoad();
				
				 $tpl->assign_vars(array(
            'PLUS_HEADER'			=> $page->Header($this->root_path),
            'PLUS_JS_WIN'			=> $page->generateWindows($this->root_path),
            'PLUS_ONLOAD'			=> $onloadarray['onload'],
            'PLUS_COOKIE'			=> $onloadarray['cookie'],
            'IS_PLUS_VERSION'	=> true,
         ));

        // Assign global template variables
        $tpl->assign_vars(array(
            'ENCODING'        => $user->lang['ENCODING'],
            'XML_LANG'        => $user->lang['XML_LANG'],
            'PAGE_TITLE'      => $this->page_title,
            'MAIN_TITLE'      => $this->config['main_title'],
            'SUB_TITLE'       => $this->config['sub_title'],
            'EQDKP_ROOT_PATH' => $this->root_path,
            'TEMPLATE_PATH'   => $this->root_path . 'templates/' . $user->style['template_path'])
        );

        $s_in_admin = ( defined('IN_ADMIN') ) ? IN_ADMIN : false;
        $s_in_admin = ( ($s_in_admin) && ($user->check_auth('a_', false)) ) ? true : false;

        $tpl->assign_vars(array(
            'S_NORMAL_HEADER' => false,
            'S_ADMIN'         => $user->check_auth('a_', false),
            'S_IN_ADMIN'      => $s_in_admin,

            'URI_ADJUSTMENT' => URI_ADJUSTMENT,
            'URI_EVENT'      => URI_EVENT,
            'URI_ITEM'       => URI_ITEM,
            'URI_LOG'        => URI_LOG,
            'URI_NAME'       => URI_NAME,
            'URI_NEWS'       => URI_NEWS,
            'URI_ORDER'      => URI_ORDER,
            'URI_PAGE'       => URI_PAGE,
            'URI_RAID'       => URI_RAID,
            'URI_SESSION'    => URI_SESSION,

            'SID' => $SID,

            // Theme Settings
            'T_FONTFACE1'          => $user->style['fontface1'],
            'T_FONTFACE2'          => $user->style['fontface2'],
            'T_FONTFACE3'          => $user->style['fontface3'],
            'T_FONTSIZE1'          => $user->style['fontsize1'],
            'T_FONTSIZE2'          => $user->style['fontsize2'],
            'T_FONTSIZE3'          => $user->style['fontsize3'],
            'T_FONTCOLOR1'         => $user->style['fontcolor1'],
            'T_FONTCOLOR2'         => $user->style['fontcolor2'],
            'T_FONTCOLOR3'         => $user->style['fontcolor3'],
            'T_FONTCOLOR_NEG'      => $user->style['fontcolor_neg'],
            'T_FONTCOLOR_POS'      => $user->style['fontcolor_pos'],
            'T_BODY_BACKGROUND'    => $user->style['body_background'],
            'T_TABLE_BORDER_WIDTH' => $user->style['table_border_width'],
            'T_TABLE_BORDER_COLOR' => $user->style['table_border_color'],
            'T_TABLE_BORDER_STYLE' => $user->style['table_border_style'],
            'T_BODY_LINK'          => $user->style['body_link'],
            'T_BODY_LINK_STYLE'    => $user->style['body_link_style'],
            'T_BODY_HLINK'         => $user->style['body_hlink'],
            'T_BODY_HLINK_STYLE'   => $user->style['body_hlink_style'],
            'T_HEADER_LINK'        => $user->style['header_link'],
            'T_HEADER_LINK_STYLE'  => $user->style['header_link_style'],
            'T_HEADER_HLINK'       => $user->style['header_hlink'],
            'T_HEADER_HLINK_STYLE' => $user->style['header_hlink_style'],
            'T_TH_COLOR1'          => $user->style['th_color1'],
            'T_TR_COLOR1'          => $user->style['tr_color1'],
            'T_TR_COLOR2'          => $user->style['tr_color2'],
            'T_INPUT_BACKGROUND'   => $user->style['input_color'],
            'T_INPUT_BORDER_WIDTH' => $user->style['input_border_width'],
            'T_INPUT_BORDER_COLOR' => $user->style['input_border_color'],
            'T_INPUT_BORDER_STYLE' => $user->style['input_border_style'],
						'EXTRA_CSS' => $this->extra_css,
						
            'T_CLASSFONTCOLOR_WARRIOR' 	=> $user->style['classfontcolor_Warrior'],
            'T_CLASSFONTCOLOR_ROGUE' 		=> $user->style['classfontcolor_Rogue'],
            'T_CLASSFONTCOLOR_HUNTER' 	=> $user->style['classfontcolor_Hunter'],
            'T_CLASSFONTCOLOR_PALADIN' 	=> $user->style['classfontcolor_Paladin'],
            'T_CLASSFONTCOLOR_PRIEST' 	=> $user->style['classfontcolor_Priest'],
            'T_CLASSFONTCOLOR_DRUID' 		=> $user->style['classfontcolor_Druid'],
            'T_CLASSFONTCOLOR_SHAMAN' 	=> $user->style['classfontcolor_Shaman'],
            'T_CLASSFONTCOLOR_WARLOCK' 	=> $user->style['classfontcolor_Warlock'],
            'T_CLASSFONTCOLOR_MAGE' 	  => $user->style['classfontcolor_Mage']            


            
            )
        );

        //
        // Menus
        //
        $menus = $this->gen_menus();
        $main_menu1 = '';
        $main_menu2 = '';
        $main_menu3 = '';


		#corgan
        #echo trim($user->style['template_path']) ;
		$bi = 1; #row counter

        foreach ( $menus as $number => $array )
        {
            foreach ( $array as $menu )
            {
                // Don't display the link if they don't have permission to view it
                if ( (empty($menu['check'])) || ($user->check_auth($menu['check'], false)) )
                {
                    $var = 'main_' . $number;

        			#corgan
                    if(trim($user->style['template_path']) == 'defaultV' or 
                     trim($user->style['template_path']) == 'wow_styleV' or
                     trim($user->style['template_path']) == 'WoWMaevahEmpireV' or
                     trim($user->style['template_path']) == 'WoWMoonclaw01V' or 
                     trim($user->style['template_path']) == 'wowV' or
                     trim($user->style['template_path']) == 'wow3theme' 
                     )
                    {
                    	#${$var} .= '<a href="' . $this->root_path . $menu['link'] . '" class="copy" target="_top">' . $menu['text'] . '</a> <br> ';
                    	${$var} .= '<tr nowrap>
						     		<td class="row'.($bi+1).'" nowrap>&nbsp;<img src="' .$this->root_path .'images/arrow.gif" alt="arrow"/> &nbsp;
							      	<a href="' . $this->root_path . $menu['link'] . '" class="copy" target="_top">' . $menu['text'] . '</a>
							      	</td></tr>';
						$bi = 1-$bi;
                    }
                    else
                    {
                    	${$var} .= '<a href="' . $this->root_path . $menu['link'] . '" class="copy" target="_top">' . $menu['text'] . '</a> | ';
                    }

                }
            }
        }

		// EQDKP PLUS Custom Links (corgan & wallenium)
		if ($conf_plus['pk_links'] == 1){
				$tpl->assign_var('MENU3NAME', 'Links');
				// load the links from db
				$linkssql = 	'SELECT link_name, link_url, link_window
         							FROM '.$table_prefix.'plus_links ORDER BY link_id';
  			$plinks_result = $db->query($linkssql);
   
   			//output links
				while ( $pluslinkrow = $db->fetch_record($plinks_result) )
   				{
   					// generate target
   					if ($pluslinkrow['link_window'] == 1){
   						$plus_target = 'target="_blank"';
   					}else{
   						$plus_target = '';
   					}
   					
   					// Output Link
						if(trim($user->style['template_path']) == 'defaultV' or 
						   trim($user->style['template_path']) == 'wow_styleV' or
						   trim($user->style['template_path']) == 'WoWMaevahEmpireV' or 
						   trim($user->style['template_path']) == 'WoWMoonclaw01V' or 
						   trim($user->style['template_path']) == 'wowV' or 
						   trim($user->style['template_path']) == 'wow3theme'  
						   )
						{
							$main_menu3 .= '<tr nowrap>
										<td class="row'.($bi+1).'" nowrap>&nbsp;<img src="' .$this->root_path .'images/arrow.gif" alt="arrow"/> &nbsp;
										<a href="' . $pluslinkrow['link_url'] . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a>
										</td></tr>';
							$bi = 1-$bi;
						}
						else
						{
							$main_menu3 .= '<a href="' . $pluslinkrow['link_url'] . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a> | ';
						}
					} // end while
	} // end on/off
// end of Links addition

        // Remove the trailing ' | ' from menus


	        $main_menu1 = preg_replace('# \| $#', '', $main_menu1);
	        $main_menu2 = preg_replace('# \| $#', '', $main_menu2);
	        $main_menu3 = preg_replace('# \| $#', '', $main_menu3);


        if ( !$this->gen_simple_header )
        {
            $tpl->assign_vars(array(
                'LOGO_PATH' => $user->style['logo_path'],

                'S_NORMAL_HEADER' => true,
                'S_LOGGED_IN' => ( $user->data['user_id'] != ANONYMOUS ) ? true : false,

                // Menu
                'MAIN_MENU1' => $main_menu1,
                'MAIN_MENU2' => $main_menu2,
                'MAIN_MENU3' => $main_menu3)
            );
        }
    }

    function gen_menus()
    {
        global $user, $pm, $SID;

        //
        // Menu 1
        //
        $main_menu1 = array(
            array('link' => 'viewnews.php' .    $SID,                                   'text' => $user->lang['menu_news'],      'check' => ''),
            array('link' => 'listmembers.php' . $SID,                                   'text' => $user->lang['menu_standings'], 'check' => 'u_member_list'),
            array('link' => 'listraids.php' .   $SID,                                   'text' => $user->lang['menu_raids'],     'check' => 'u_raid_list'),
            array('link' => 'listevents.php' .  $SID,                                   'text' => $user->lang['menu_events'],    'check' => 'u_event_list'),
            array('link' => 'listitems.php' .   $SID,                                   'text' => $user->lang['menu_itemval'],   'check' => 'u_item_list'),
            array('link' => 'listitems.php' .   $SID . '&amp;' . URI_PAGE . '=history', 'text' => $user->lang['menu_itemhist'],  'check' => 'u_item_list'),
            #array('link' => 'summary.php' .     $SID,                                   'text' => $user->lang['menu_summary'],   'check' => 'u_raid_list'),
            #array('link' => 'stats.php' .       $SID,                                   'text' => $user->lang['menu_stats'],     'check' => 'u_member_list')
        );

        #$main_menu1 = array_merge($main_menu1, $pm->get_menus('main_menu1'));
        $main_menu1 = (is_array($pm->get_menus('main_menu1'))) ? array_merge($main_menu1, $pm->get_menus('main_menu1')) : $main_menu1;

        //
        // Menu 2
        //
        $main_menu2 = array();
        if ( $user->data['user_id'] != ANONYMOUS )
        {
            $main_menu2[] = array('link' => 'settings.php' . $SID, 'text' => $user->lang['menu_settings']);
        }
        else
        {
            $main_menu2[] = array('link' => 'register.php' . $SID, 'text' => $user->lang['menu_register']);
        }

        if ( $user->check_auth('a_', false) )
        {
            $main_menu2[] = array('link' => 'admin/index.php' . $SID, 'text' => $user->lang['menu_admin_panel']);
        }

        // Switch login/logout link
        if ( $user->data['user_id'] != ANONYMOUS )
        {
            $main_menu2[] = array('link' => 'login.php' . $SID . '&amp;logout=true', 'text' => $user->lang['logout'] . ' [ ' . $user->data['username'] . ' ]');
        }
        else
        {
            $main_menu2[] = array('link' => 'login.php' . $SID, 'text' => $user->lang['login']);
        }

        #$main_menu2 = array_merge($main_menu2, $pm->get_menus('main_menu2'));
        $main_menu2 = (is_array($pm->get_menus('main_menu2'))) ? array_merge($main_menu2, $pm->get_menus('main_menu2')) : $main_menu2;

        $menus = array(
            'menu1' => $main_menu1,
            'menu2' => $main_menu2);

        return $menus;
    }

    function page_tail()
    {
        global $db, $user, $tpl, $pm, $eqdkpplus_vcontrol;
        global $SID;

        if ( !empty($this->template_path) )
        {
            $tpl->set_template($user->style['template_path'], $this->template_path);
        }

        if ( empty($this->template_file) )
        {
            trigger_error('Template file is empty.', E_USER_ERROR);
            return false;
        }

        $tpl->set_filenames(array(
            'body' => $this->template_file)
        );

        // Hiding the copyright/debug info if gen_simple_header is set
        if ( !$this->gen_simple_header )
        {
        	// Additions for PLUS
					$update = new UpdateCheck(
						'http://eqdkp.corgan-net.de/vcheck/version.php',
						'eqdkp.corgan-net.de',
						$eqdkpplus_vcontrol,
						array('Security Update' => 'red'),
						EQDKPPLUS_VERSION,
						EQDKPPLUS_AUTHOR,
						EQDKPPLUS_AUTHOR_URL
					);
				
            $tpl->assign_vars(array(
                'S_NORMAL_FOOTER' => true,
                'L_POWERED_BY' => $user->lang['powered_by'],
                'EQDKP_PLUS_COPYRIGHT' => $update->Copyright(),
                'EQDKP_VERSION' => EQDKP_VERSION)
            );

            if ( DEBUG )
            {
                $mc_split = split(' ', microtime());
                $this->timer_end = $mc_split[0] + $mc_split[1];
                unset($mc_split);

                $s_show_queries = ( DEBUG == 2 ) ? true : false;

                $tpl->assign_vars(array(
                    'S_SHOW_DEBUG' => true,
                    'S_SHOW_QUERIES' => $s_show_queries,
                    'EQDKP_RENDERTIME' => substr($this->timer_end - $this->timer_start, 0, 5),
                    'EQDKP_QUERYCOUNT' => $db->query_count)
                );

                if ( $s_show_queries )
                {
                    foreach ( $db->queries as $query )
                    {
                        $tpl->assign_block_vars('query_row', array(
                            'ROW_CLASS' => $this->switch_row_class(),
                            'QUERY' => sql_highlight($query))
                        );
                    }
                }
            }
            else
            {
                $tpl->assign_vars(array(
                    'S_SHOW_DEBUG' => false,
                    'S_SHOW_QUERIES' => false)
                );
            }
        }
        else
        {
            $tpl->assign_vars(array(
                'S_NORMAL_FOOTER' => false)
            );
        }

        // Close our DB connection.
        $db->close_db();

        // Get rid of our template data
        $tpl->display('body');
        $tpl->destroy();

        exit;
    }
}

/**
* EQdkp admin page foundation
* Extended by admin page classes only
*/

class EQdkp_Admin
{
    // General vars
    var $buttons      = array();          // Submit buttons and their associated actions      @var buttons
    var $params       = array();          // GET parameters and their associated actions      @var params
    var $last_process = '';               // Last-called process                              @var last_process
    var $err_process  = 'display_form';   // Process to call when errors occur                @var err_process
    var $url_id       = 0;                // ID from _GET                                     @var url_id
    var $fv           = NULL;             // Form Validation object (not reference)           @var fv
    var $time         = 0;                // Current time                                     @var time

    // Delete confirmation vars
    var $confirm_text  = '';              // Message to display for confirmation              @var confirm_text
    var $script_name   = '';              // e.g., eqdkp.php                                  @var script_name
    var $uri_parameter = '';              // URI parameter                                    @var uri_parameter

    // Logging vars
    var $log_fields = array('log_id', 'log_date', 'log_type', 'log_action', 'log_ipaddress', 'log_sid', 'log_result', 'admin_id');
    var $log_values = array();            // Holds default log values                         @var log_values
    var $admin_user = '';                 // Username of admin                                @var admin_user

    function eqdkp_admin()
    {
        global $user;

        // Store our Form Validation object
        $this->fv = new Form_Validate;

        // Determine the script name based on PHP_SELF
        $this->script_name = preg_replace('#.+/(.+\.php)$#', '\1', $_SERVER['PHP_SELF']);

        // Default our log values
        $this->log_values = array(
            'log_id'        => 'NULL',
            'log_date'      => time(),
            'log_type'      => NULL,
            'log_action'    => NULL,
            'log_ipaddress' => $user->ip_address,
            'log_sid'       => $user->sid,
            'log_result'    => '{L_SUCCESS}',
            'admin_id'      => $user->data['user_id']);

        $this->admin_user = ( $user->data['user_id'] != ANONYMOUS ) ? $user->data['username'] : '';
        $this->time = time();
    }

    /**
    * Build the $buttons array
    *
    * @param $buttons Array of button => name/process/auth_check values
    * @return bool
    */
    function assoc_buttons($buttons)
    {
        if ( !is_array($buttons) )
        {
            return false;
        }

        foreach ( $buttons as $code => $button )
        {
            $this->buttons[$code] = $button;
        }

        return true;
    }

    function assoc_params($params)
    {
        if ( !is_array($params) )
        {
            return false;
        }

        foreach ( $params as $code => $param )
        {
            $this->params[$code] = $param;
        }

        return true;
    }

    function process()
    {
        global $user;

        $errors_exist = false;
        $processed    = false;

        // Form has been submitted
        if ( @sizeof($_POST) > 0 )
        {
            // Sanitize our POST vars
            $_POST = sanitize_tags($_POST);

            // Confirm is an automatic button option if confirm_delete is called
            if ( isset($_POST['confirm']) )
            {
                if ( method_exists($this, 'process_confirm') )
                {
                    $processed = true;
                    if ( isset($this->buttons['delete']['check']) )
                    {
                        $user->check_auth($this->buttons['delete']['check']);
                    }
                    $this->last_process = 'process_confirm';
                    $this->process_confirm();
                }
            }
            // Cancel is an automatic button option if confirm_delete is called
            elseif ( isset($_POST['cancel']) )
            {
                $processed = true;
                $this->last_process = 'process_cancel';
                $this->process_cancel();
            }
            // Confirm/Delete weren't pressed, we're dealing with custom processes now
            else
            {
                // Check for errors
                $this->process_error_check();

                foreach ( $this->buttons as $code => $button )
                {
                    if ( isset($_POST[ $button['name'] ]) )
                    {
                        $processed = true;
                        if ( isset($button['check']) )
                        {
                            $user->check_auth($button['check']);
                        }
                        $this->last_process = $button['process'];
                        $this->$button['process']();
                    }
                }
            }
        }
        // No POST vars, check for GET vars and process as necessary
        foreach ( $this->params as $code => $param )
        {
            if ( isset($_GET[ $param['name'] ]) )
            {
                if ( isset($param['value']) )
                {
                    if ( $_GET[ $param['name'] ] == $param['value'] )
                    {
                        $this->process_error_check();
                        $processed = true;
                        if ( isset($param['check']) )
                        {
                            $user->check_auth($param['check']);
                        }
                        $this->last_process = $param['process'];
                        $this->$param['process']();
                    }
                }
                else
                {
                    $this->process_error_check();
                    $processed = true;
                    if ( isset($param['check']) )
                    {
                        $user->check_auth($param['check']);
                    }
                    $this->last_process = $param['process'];
                    $this->$param['process']();
                }
            }
        }

        // Nothing was processed
        if ( !$processed )
        {
            if ( (isset($this->buttons['form'])) && (is_array($this->buttons['form'])) )
            {
                if ( isset($this->buttons['form']['check']) )
                {
                    $user->check_auth($this->buttons['form']['check']);
                }
                $process = $this->buttons['form']['process'];
                $this->last_process = $process;
                $this->$process();
            }
            else
            {
                return false;
            }
        }
    }

    function process_error_check()
    {
        // Check for errors
        if ( method_exists($this, 'error_check') )
        {
            $errors_exist = $this->error_check();

            // Errors exist, redisplay the form
            if ( $errors_exist )
            {
                $process = $this->err_process;
                $this->last_process = $process;
                $this->$process();
            }
        }
    }

    // ---------------------------------------------------------
    // Default process methods
    // ---------------------------------------------------------

    function process_delete()
    {
        global $SID;

        $this->script_name = ( strpos($this->script_name, '?' . URI_SESSION . '=') ) ? $this->script_name : $this->script_name . $SID;

        confirm_delete($this->confirm_text, $this->uri_parameter, $this->url_id, $this->script_name);
    }

    function process_cancel()
    {
        global $SID;

        if ( empty($this->script_name) )
        {
            message_die('Cannot redirect to an empty script name.');
        }

        if ( defined('PLUGIN') )
        {
            $script_path = 'plugins/' . PLUGIN . '/';
			if ( defined('IN_ADMIN') ) { $script_path = 'plugins/' . PLUGIN . '/admin/'; }
        }
        elseif ( defined('IN_ADMIN') )
        {
            $script_path = 'admin/';
        }
        else
        {
            $script_path = '';
        }

        if ( $this->url_id )
        {
            $redirect = $script_path . $this->script_name . $SID . '&' . $this->uri_parameter . '=' . $this->url_id;
        }
        else
        {
            $redirect = $script_path . $this->script_name . $SID;
        }

        redirect($redirect);
    }

    /**
    * Set object variables
    *
    * @var $var Var to set
    * @var $val Value for Var
    * @return bool
    */
    function set_vars($var, $val = '')
    {
        if ( is_array($var) )
        {
            foreach ( $var as $d_var => $d_val )
            {
                $this->set_vars($d_var, $d_val);
            }
        }
        else
        {
            if ( empty($val) )
            {
                return false;
            }

            $this->$var = $val;
        }

        //
        // Set url_id if it hasn't already been set
        if ( !$this->url_id )
        {
            $this->url_id = ( !empty($_REQUEST[$this->uri_parameter]) ) ? $_REQUEST[$this->uri_parameter] : 0;
        }

        return true;
    }

    function make_log_action($action = array())
    {
        $str_action = "\$log_action = array(";
        foreach ( $action as $k => $v )
        {
            $str_action .= "'" . $k . "' => '" . addslashes($v) . "',";
        }
        $action = substr($str_action, 0, strlen($str_action)- 1) . ");";

        // Take the newlines and tabs (or spaces > 1) out of the action
        $action = preg_replace("/[[:space:]]{2,}/", '', $action);
        $action = str_replace("\t", '', $action);
        $action = str_replace("\n", '', $action);
        $action = preg_replace("#(\\\){1,}#", "\\", $action);

        return $action;
    }

    function log_insert($values = array())
    {
        global $db;

        if ( sizeof($values) > 0 )
        {
            // If they set the value, we use theirs, otherwise we use the default
            foreach ( $this->log_fields as $field )
            {
                $values[$field] = ( isset($values[$field]) ) ? $values[$field] : $this->log_values[$field];

                if ( $field == 'log_action' )
                {
                    $values[$field] = $this->make_log_action($values[$field]);
                }
            }

            $query = $db->build_query('INSERT', $values);
            $sql = 'INSERT INTO ' . LOGS_TABLE . $query;

            $db->query($sql);

            return true;
        }
        return false;
    }

    /**
    * Takes two variables of the same type and compares them, marking in red
    * any items that the two don't have in common
    *
    * @param $value1 The first, or 'old' value
    * @param $value2 The second, or 'new' value
    * @param $return_var Which of the two to return
    */
    function find_difference($value1, $value2, $return_var = 2)
    {
        if ( ($return_var != 1) && ($return_var != 2) )
        {
            $return_var = 2;
        }

        if ( (is_array($value1)) && (is_array($value2)) )
        {
            foreach ( $value1 as $k => $v )
            {
                $v = preg_replace("#(\\\){1,}\'#", "'", $v);

                if ( !in_array($v, $value2) )
                {
                    $value1[$k] = '<span class="negative">'.$v.'</span>';
                }
            }
            foreach ( $value2 as $k => $v )
            {
                $v = preg_replace("#(\\\){1,}\'#", "'", $v);

                if ( !in_array($v, $value1) )
                {
                    $value2[$k] = '<span class="negative">'.$v.'</span>';
                }
            }
        }
        elseif ( (!is_array($value1)) && (!is_array($value2)) )
        {
            $value1 = preg_replace("#(\\\){1,}\'#", "'", $value1);
            $value2 = preg_replace("#(\\\){1,}\'#", "'", $value2);

            if ( $value1 != $value2 )
            {
                $value2 = '<span class="negative">'.$value2.'</span>';
            }

            $value2 = addslashes($value2);
        }

        $valueX = 'value'.$return_var;

        return ${$valueX};
    }

    function admin_die(&$message, $link_list = array())
    {
        global $eqdkp, $user, $tpl, $pm;
        global $SID;

        $message = stripmultslashes($message);

        if ( (is_array($link_list)) && (sizeof($link_list) > 0) )
        {
            $message .= '<br /><br />' . $this->generate_link_list($link_list);
        }

        message_die($message);
    }

    /**
    * Returns a bulleted list of links to display after an admin event
    * has been completed
    *
    * @param $links Array of links
    * @return string Link list
    */
    function generate_link_list($links)
    {
        $link_list = '<ul>';

        if ( is_array($links) )
        {
            foreach ( $links as $k => $v )
            {
                $link_list .= '<li><a href="'.$v.'">'.$k.'</a></li>';
            }
        }
        $link_list .= '</ul>';

        return $link_list;
    }

    function gen_group_key($part1, $part2, $part3)
    {
        // Normalize data
        $part1 = htmlspecialchars(stripslashes($part1));
        $part2 = htmlspecialchars(stripslashes($part2));
        $part3 = htmlspecialchars(stripslashes($part3));

        // Get the first 10-11 digits of each md5 hash
        $part1 = substr(md5($part1), 0, 10);
        $part2 = substr(md5($part2), 0, 11);
        $part3 = substr(md5($part3), 0, 11);

        // Group the hashes together and create a new hash based on uniqid()
        $group_key = $part1 . $part2 . $part3;
        $group_key = md5(uniqid($group_key));

        return $group_key;
    }
}

/**
* Form Validate Class
* Validates various elements of a form and types of data
* Available through admin extensions as fv
*/
class Form_Validate
{
    var $errors = array();          // Error messages       @var errors

    /**
    * Constructor
    *
    * Initiates the error list
    */
    function form_validate()
    {
        $this->_reset_error_list();
    }

    /**
    * Resets the error list
    *
    * @access private
    */
    function _reset_error_list()
    {
        $this->errors = array();
    }

    /**
    * Returns the array of errors
    *
    * @return array Errors
    */
    function get_errors()
    {
        return $this->errors;
    }

    /**
    * Checks if errors exist
    *
    * @return bool
    */
    function is_error()
    {
        if ( @sizeof($this->errors) > 0 )
        {
            return true;
        }

        return false;
    }

    /**
    * Returns a string with the appropriate error message
    *
    * @param $field Field to generate an error for
    * @return string Error string
    */
    function generate_error($field)
    {
        global $eqdkp_root_path;

        if ( $field != '' )
        {
            if ( !empty($this->errors[$field]) )
            {
                $error = '<br /><img src="'.$eqdkp_root_path . 'images/error.gif"
                          align="middle" alt="Error" />&nbsp;<b>'.
                          $this->errors[$field].'</b>';
                return $error;
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }
    }

    /**
    * Returns the value of a variable in _POST or _GET
    *
    * @access private
    * @param $field_name Field name
    * @param $from post/get
    * @return mixed Value of the field_name
    */
    function _get_value($field_name, $from = 'post')
    {
        if ( $from == 'post' )
        {
            return ( isset($_POST[$field_name]) ) ? $_POST[$field_name] : false;
        }
        elseif ( $from == 'get' )
        {
            return ( isset($_GET[$field_name]) ) ? $_GET[$field_name] : false;
        }
    }

    // Begin validator methods
    // Note: The validation methods can accept arrays for the $field param
    // in this form: $field['fieldname'] = "Error message";
    // and the validation will be performed on each key/val pair.
    // If an array if used for validation, the method will always return true

    /**
    * Checks if a field is filled out
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_filled($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_filled($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( trim($value) == '' )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is numeric
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_number($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_number($k, $v);
            }
            return true;
        }
        else
        {
            $value = str_replace(' ','', $this->_get_value($field));
            if ( !is_numeric($value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is alphabetic
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_alpha($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_alpha($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            #if ( !preg_match("/^[[:alpha:][:space:]]+$/", $value) )
            # corgan
            if ( preg_match("/^[\"'-]+$/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is a valid hexadecimal color code (#FFFFFF)
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_hex_code($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_hex_code($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/(#)?[0-9A-Fa-f]{6}$/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if a field is within a minimum and maximum range
    * NOTE: Will NOT accept an array of fields
    *
    * @param $field Field name to check
    * @param $min Minimum value
    * @param $max Maximum value
    * @param $message Error message to insert
    * @return bool
    */
    function is_within_range($field, $min, $max, $message = '')
    {
        $value = $this->_get_value($field);
        if ( (!is_numeric($value)) || ($value < $min) || ($value > $max) )
        {
            $this->errors[$field] = $message;
            return false;
        }
        return true;
    }

    /**
    * Checks if a field has a valid e-mail address pattern
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_email_address($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_email_address($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $value) )
            {
                $this->errors[$field] = $message;
                return false;
            }
            return true;
        }
    }

    /**
    *  Checks if a field has a valid IP address pattern
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function is_ip_address($field, $message = '')
    {
        if ( is_array($field) )
        {
            foreach ( $field as $k => $v )
            {
                $this->is_ip_address($k, $v);
            }
            return true;
        }
        else
        {
            $value = $this->_get_value($field);
            if ( !preg_match("/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", $value) )
            {
                $this->errors[$field] = $v;
                return false;
            }
            return true;
        }
    }

    /**
    * Checks if two fields match eachother exactly
    * Used to verify the password/confirm password fields
    *
    * @param $field Field name to check
    * @param $message Error message to insert
    * @return bool
    */
    function matching_passwords($field1, $field2, $message = '')
    {
        $value1 = $this->_get_value($field1);
        $value2 = $this->_get_value($field2);

        if ( md5($value1) != md5($value2) )
        {
            $this->errors[$field1] = $message;
            return false;
        }
        return true;
    }
}
?>
