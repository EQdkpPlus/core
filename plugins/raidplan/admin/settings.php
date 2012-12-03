<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * settings.php
 * Changed: Thu July 13, 2006
 * 
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . '/plugins/raidplan/config.php');

// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }

// Check user permission
$user->check_auth('a_raidplan_');

// Get the plugin
$raidplan = $pm->get_plugin('raidplan');

// Save this shit
if ($_POST['issavebu']){
  // global config
    UpdateRPConfig('rp_show_ranks', $_POST['rp_show_ranks']); # Show ranks in raid planner?
		UpdateRPConfig('rp_short_rank', $_POST['rp_short_rank']); # Show only short ranks?
		UpdateRPConfig('rp_send_email', $_POST['rp_send_email']); # Email raids to all users
		UpdateRPConfig('rp_roll_systm', $_POST['rp_roll_systm']); # Should we use the roll-system?
		UpdateRPConfig('rp_wildcard', $_POST['rp_wildcard']);   # Should we use the wildcard-system?
		UpdateRPConfig('rp_use_css', $_POST['rp_use_css']);    # Should we add the .css file in the plugin's template folder?
		UpdateRPConfig('rp_last_days', $_POST['rp_last_days']);  # show recent raids: last x days
		UpdateRPConfig('rp_auto_hash', $_POST['rp_auto_hash']);  # Autojoin Secret Hash
		UpdateRPConfig('rp_auto_path', $_POST['rp_auto_path']);  # Autojoin Secret Path
    if ($lala){
    echo($user->lang['is_conf_saved']);
    }
} 

global $table_prefix;
if (!defined('RP_CONFIG_TABLE')) { define('RP_CONFIG_TABLE', $table_prefix . 'raidplan_config'); }

$sql = 'SELECT * FROM ' . RP_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $row[$roww['config_name']] = $roww['config_value'];
}

// Output
$tpl->assign_vars(array(
      'F_CONFIG'        => 'settings.php' . $SID,
      
      'SHOW_RANKS'     => ( $row['rp_show_ranks'] == 1 ) ? ' checked="checked"' : '',
      'SHORT_RANKS'     => ( $row['rp_short_rank'] == 1 ) ? ' checked="checked"' : '',
      'SEND_EMAIL'    => ( $row['rp_send_email'] == 1 ) ? ' checked="checked"' : '',
      'ROLL_SYSTEM'    => ( $row['rp_roll_systm'] == 1 ) ? ' checked="checked"' : '',
      'WILDCARD'        => ( $row['rp_wildcard'] == 1) ? ' checked="checked"' : '',
      'USE_CSS'       => ( $row['rp_use_css'] == 1) ? ' checked="checked"' : '',
      'LAST_X_DAYS'      => $row['rp_last_days'],
      'AUTOHASH'      => $row['rp_auto_hash'],
      'AUTOPATH'      => $row['rp_auto_path'],
      
      // Language
      'L_SUBMIT'        => $user->lang['submit'],
      'L_RESET'         => $user->lang['reset'],
      
      'L_GENERAL'       => $user->lang['rp_header_global'],
      
      'L_SHOW_RANKS'    => $user->lang['rp_show_ranks'],
      'L_SHORT_RANK'    => $user->lang['rp_short_rank'],
      'L_SEND_EMAIL'    => $user->lang['rp_send_email'],
      'L_ROLL_SYSTEM'   => $user->lang['rp_roll_system'],
      'L_WILDCARD_SYS'  => $user->lang['rp_wildcard_sys'],
      'L_USE_CSS'       => $user->lang['rp_use_css'],
      'L_LAST_X_DAYS'   => $user->lang['rp_last_x_days'],
      'L_SECRET_HASH'   => $user->lang['rp_aj_secret_hash'],
      'L_RP_PATH'       => $user->lang['rp_aj_path'],
      
      )
		);
    
    $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Settings',
			'template_path' 	       => $pm->get_data('raidplan', 'template_path'),
			'template_file'          => 'admin/settings.html',
			'display'                => true)
    );
    

