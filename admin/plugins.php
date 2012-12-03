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
                'NAME'      => $pm->get_data($plugin_code, 'name'),
                'CODE'      => $plugin_code,
                'VERSION'   => ( !empty($version) ) ? $version : '&nbsp;',
                'U_ACTION'  => 'plugins.php' . $SID . '&amp;mode=' . (( $installed ) ? 'uninstall' : 'install') . '&amp;code=' . $plugin_code,
                'ACTION'    => ( $installed ) ? $user->lang['uninstall'] : $user->lang['install'],
                'CONTACT'   => ( !is_null($contact) ) ? '<a href="mailto:' . $contact . '">' . $contact . '</a>' : '&nbsp;')
            );
            unset($contact, $installed, $version);
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
            'L_CODE'    => $user->lang['code'],
            'L_VERSION' => $user->lang['version'],
            'L_ACTION'  => $user->lang['action'],
            'L_CONTACT' => $user->lang['contact'])
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['plugins_title'],
            'template_file' => 'admin/plugins.html',
            'display'       => true)
        );
 
        break;
}
?>
