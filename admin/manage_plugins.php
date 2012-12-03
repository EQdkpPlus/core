<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * manage_plugins.php
 * Began: Mon January 13 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_plugins_man');

$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : 'list';
$code = ( isset($_GET['code']) ) ? $_GET['code'] : '';

if ( (!empty($code)) && (!is_dir($eqdkp_root_path . 'plugins/' . $code)) ){
	message_die($user->lang['error_invalid_plugin']);
}

// Shiny new Progress Messages...
if($in->get('progressChange')){
  $core->message($in->get('prog_message'), $user->lang['plugin_inst_success'], 'green');
}

// Not used untill now...
if($in->get('download')){
  $pmanager->DownloadPackage($in->get('download'));
}

$show_list = false;

switch ( $mode )
{
		case 'enable':
       	$pm->enable($code);
       	$pm->do_hooks('/admin/manage_plugins.php?mode=enable');
       	$show_list = true;
        break;
        
    case 'disable':
       	$pm->disable($code);
       	$pm->do_hooks('/admin/manage_plugins.php?mode=disable');
       	$show_list = true;
        break;
        
    case 'install':
        $pm->install($code);
        $pm->do_hooks('/admin/manage_plugins.php?mode=install');

        $plugin_object = $pm->get_plugin($code);
        $plugin_object->message(SQL_INSTALL);
        break;
        
    case 'uninstall':
        $pm->uninstall($code);
        $pcomments->Uninstall($code);
        $pm->do_hooks('/admin/manage_plugins.php?mode=uninstall');

        $plugin_object = $pm->get_plugin($code);
        $plugin_object->message(SQL_UNINSTALL);
        break;
        
    case 'list':
        $show_list = true;
        break;
}

if($show_list){
// Register any new plugins before we list the available ones
        $pm->register();

        $unset_array = array();
        $plugins_array = $pm->get_plugins(PLUGIN_ALL);
        $plugin_count = count($plugins_array);
        foreach ( $plugins_array as $plugin_code => $plugin_object )
        {
            $installed 	= $pm->check(PLUGIN_INSTALLED, $plugin_code);

            // Initialize the object if we need to
            if ( !$pm->check(PLUGIN_INITIALIZED, $plugin_code) )
            {
                if ( $pm->initialize($plugin_code) )
                {
                    $unset_array[] = $plugin_code;
                }
            }

            $contact = $pm->get_data($plugin_code, 'contact');
            $version = $pm->get_data($plugin_code, 'version');
            $description = $pm->get_data($plugin_code, 'description');
            $long_description = $pm->get_data($plugin_code, 'long_description');
            $manuallink = $pm->get_data($plugin_code, 'manuallink');
            $homepagelink = $pm->get_data($plugin_code, 'homepage');
            $author = $pm->get_data($plugin_code, 'author');
            $status = $pm->get_db_info($plugin_code, 'status');
            
            //glyph
            
            
            //dependencies
            $dep_plus = $pm->check_dependency($plugin_code, 'plus_version');
            $dep_libs = $pm->check_dependency($plugin_code, 'lib_version');
            $dep_game = $pm->check_dependency($plugin_code, 'games');
            $dep_phpf = $pm->check_dependency($plugin_code, 'php_functions');

            //show missing functions
            $tt_phpf = $user->lang['plug_dep_phpf'];

            $needed_functions = $plugin_object->get_dependency('php_functions');
            
            if( is_array($needed_functions) && (count($needed_functions) > 0) ){
              $tt_phpf .= ':<br>';
              foreach($needed_functions as $function){
                $tt_phpf .= (function_exists($function)) ? '<span class="positive">'.$function.'</span><br>' : '<span class="negative">'.$function.'</span><br>';
              }
            }
            $dep_all = $dep_plus & $dep_libs & $dep_game & $dep_phpf;
            
            
            if($installed){
              $link = ( $dep_all ) ? '<a href="manage_plugins.php' . $SID . '&amp;mode=uninstall&amp;code=' . $plugin_code. '">' . $user->lang['uninstall'] . '</a>' : $user->lang['plug_dep_broken_deps'];
            }else{
              $link = ( $dep_all ) ? '<a href="manage_plugins.php' . $SID . '&amp;mode=install&amp;code=' . $plugin_code. '">' . $user->lang['install'] . '</a>' : $user->lang['plug_dep_broken_deps'];
            }

            $tpl->assign_block_vars('plugins_row', array(
                'ROW_CLASS' => $core->switch_row_class(),
                'glyph' => ($installed) ? 'plugin.gif' : 'plugin_off.png' ,
                'NAME'      => $pm->get_data($plugin_code, 'name'),
                'DEPENDENCY_STATUS' =>  $html->ToolTip($user->lang['plug_dep_plusv'],$html->toggleIcons($dep_plus,'status_green.gif','status_red.gif','images/glyphs/')).'&nbsp;&nbsp;'.
                                        $html->ToolTip($user->lang['plug_dep_libsv'],$html->toggleIcons($dep_libs,'status_green.gif','status_red.gif','images/glyphs/')).'&nbsp;&nbsp;'.
                                        $html->ToolTip($user->lang['plug_dep_games'],$html->toggleIcons($dep_game,'status_green.gif','status_red.gif','images/glyphs/')).'&nbsp;&nbsp;'.
                                        $html->ToolTip($tt_phpf,$html->toggleIcons($dep_phpf,'status_green.gif','status_red.gif','images/glyphs/')),

                'VERSION'   => ( !empty($version) ) ? $version : '&nbsp;',
                'CODE'      => $plugin_code,
                'CONTACT'   => ( !empty($contact) ) ? ( !empty($author) ) ? '<a href="mailto:' . $contact . '">' . $author . '</a>' : '<a href="mailto:' . $contact . '">' . $contact . '</a>'  : $author,
                'DESCRIPTION' => ( !empty($description) ) ? $description : '&nbsp;',

                'LONG_DESCRIPTION' => $html->ToolTip($long_description,$html->toggleIcons($long_description,'help.png','help_off.png','images/glyphs/',$user->lang['description'],false)),
				        'HOMEPAGE'  => $html->ToolTip($user->lang['homepage'],$html->toggleIcons($homepagelink,'browser.png','browser_off.png','images/glyphs/',$user->lang['homepage'],$homepagelink)),
                'MANUAL'    => $html->ToolTip($user->lang['manual'],$html->toggleIcons($manuallink,'acroread.png','acroread_off.png','images/glyphs/',$user->lang['Manual'],$manuallink)),
                'ACTION_LINK' => $link,
                )
            );
            unset($contact, $installed, $version, $description, $manuallink, $homepagelink, $author);
        }

        // Return uninitialized objects to their previous state
        foreach ( $unset_array as $plugin_code )
        {
            unset($pm->plugins[$plugin_code]);
        }
				
        $tpl->assign_vars(array(
            'NO_PLUGS'  					=> ($plugin_count == 0) ? true : false,
            'MANUPLOAD'						=> true,
            
            'L_TAB_PLUGINS'				=> $user->lang['plug_tab_plugins'],
            'L_TAB_PLUGUPDATES'		=> ($plugupfatesavailable) ? $user->lang['plug_tab_plugupdates'] : $user->lang['plug_tab_noplugupdates'],
            'L_NO_PLUG'						=> $user->lang['no_plugins'],
            'L_NAME'							=> $user->lang['name'],
            'L_DESCRIPTION'				=> $user->lang['description'],
            'L_MANUAL'						=> $user->lang['manual'],
            'L_INFOS'							=> $user->lang['infos'],
            'L_HOMEPAGE'					=> $user->lang['homepage'],
            'L_CODE'							=> $user->lang['code'],
            'L_VERSION'						=> $user->lang['version'],
            'L_ACTION' 						=> $user->lang['action'],
            'L_CONTACT'						=> $user->lang['contact'],
            'L_MORE'							=> $user->lang['more_plugins'],
            'L_DEPENDENCY_STATUS' => $user->lang['plug_dep_title'],
            )
        );

        $core->set_vars(array(
            'page_title'    => $user->lang['plugins_title'],
            'template_file' => 'admin/plugins.html',
			'header_format' => ($in->get('simple_head', false)) ? 'simple' : 'full',
            'display'       => true)
        );
}
?>