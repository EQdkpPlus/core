<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * plugins.php
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

if ( (!empty($code)) && (!is_dir($eqdkp_root_path . 'plugins/' . $code)) )
{
    message_die($user->lang['error_invalid_plugin']);
}

// Shiny new Progress Messages...
if($_POST['progressChange']){
  $tpl->assign_vars(array(
      'JS_GROWL_MSSG'   => message_growl($_POST['prog_message'], $user->lang['plugin_inst_success']),
  ));
}

switch ( $mode )
{
		case 'enable':
       	$pm->enable($code);
       	$pm->do_hooks('/admin/plugins.php?mode=enable');
        break;
    case 'install':
        $pm->install($code);
        $pm->do_hooks('/admin/plugins.php?mode=install');
        
        $plugin_object = $pm->get_plugin($code);
        $plugin_object->message(SQL_INSTALL);
        
        break;
    case 'uninstall':
        $pm->uninstall($code);
        $pm->do_hooks('/admin/plugins.php?mode=uninstall');
        
        $plugin_object = $pm->get_plugin($code);
        $plugin_object->message(SQL_UNINSTALL);
        
        break;
    case 'list':
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
            
            $description = $pm->get_additional_data($plugin_code, 'description');
            $long_description = $pm->get_additional_data($plugin_code, 'long_description');
            $manuallink = $pm->get_additional_data($plugin_code, 'manuallink');
            $homepagelink = $pm->get_additional_data($plugin_code, 'homepage');
            $author = $pm->get_additional_data($plugin_code, 'author');
            
            if($installed){
            	$linkname = '<a href="plugins.php' . $SID . '&amp;mode=uninstall&amp;code='.$plugin_code.'">'.$user->lang['uninstall'].'</a>';
            /*}elseif(!$installed && $pm->check(PLUGIN_DISABLED, $plugin_code)){
            	$linkname  = '<a href="plugins.php' . $SID . '&amp;mode=enable&amp;code='.$plugin_code.'">'.$user->lang['enable'].'</a> | ';
            	$linkname .= '<a href="plugins.php' . $SID . '&amp;mode=uninstall&amp;code='.$plugin_code.'">'.$user->lang['uninstall'].'</a>';*/
            }else{
            	$linkname = '<a href="plugins.php' . $SID . '&amp;mode=install&amp;code='.$plugin_code.'">'.$user->lang['install'].'</a>';
            }
            
            $tpl->assign_block_vars('plugins_row', array(
                'ROW_CLASS' => $eqdkp->switch_row_class(),
                'glyph' => ($installed) ? 'plugin.gif' : 'plugin_off.png' ,
                'NAME'      => $pm->get_data($plugin_code, 'name'),
                'VERSION'   => ( !empty($version) ) ? $version : '&nbsp;',
                'CODE'      => $plugin_code,
                'CONTACT'   => ( !is_null($contact) ) ? ( !empty($author) ) ? '<a href="mailto:' . $contact . '">' . $author . '</a>' : '<a href="mailto:' . $contact . '">' . $contact . '</a>'  : '&nbsp;',
                'DESCRIPTION' => ( !empty($description) ) ? $description : '&nbsp;',                          

                'LONG_DESCRIPTION' => $html->ToolTip($long_description,$html->toggleIcons($long_description,'help.png','help_off.png','images/glyphs/',$user->lang['description'],false)), 
				'HOMEPAGE'  => $html->ToolTip($user->lang['homepage'],$html->toggleIcons($homepagelink,'browser.png','browser_off.png','images/glyphs/',$user->lang['homepage'],$homepagelink)), 
                'MANUAL'    => $html->ToolTip($user->lang['manual'],$html->toggleIcons($manuallink,'acroread.png','acroread_off.png','images/glyphs/',$user->lang['Manual'],$manuallink)), 
                
                'U_ACTION'  => 'plugins.php' . $SID . '&amp;mode=' . (( $installed ) ? 'uninstall' : 'install') . '&amp;code=' . $plugin_code,
                'ACTION'    => ( $installed ) ? $user->lang['uninstall'] : $user->lang['install'],
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
            'NO_PLUGS'  => ($plugin_count == 0) ? true : false,
            'L_NO_PLUG' => $user->lang['no_plugins'],
            'L_NAME'    => $user->lang['name'],
            'L_DESCRIPTION' => $user->lang['description'],
            'L_MANUAL'    => $user->lang['manual'],
            'L_INFOS'    => $user->lang['infos'],
            'L_HOMEPAGE'  => $user->lang['homepage'],
            'L_CODE'    => $user->lang['code'],
            'L_VERSION' => $user->lang['version'],
            'L_ACTION'  => $user->lang['action'],
            'L_CONTACT' => $user->lang['contact'],
            'L_MORE' => $user->lang['more_plugins']
            )
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['plugins_title'],
            'template_file' => 'admin/plugins.html',
            'display'       => true)
        );
 
        break;
}



?>
