<?php
/******************************
 * EQdkp Tradeskills Plugin
 * ------------------
 * settings.php
 * Created: 12. Oct 2006
 * 
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'ts');
$eqdkp_root_path = './../../../';
include_once('../includes/functions.php');

// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'ts')) { message_die('The Tradeskill plugin is not installed.'); }

// Check user permission
$user->check_auth('a_ts_admin');

// Get the plugin
$raidplan = $pm->get_plugin('ts');


//---------
global $table_prefix;

if (!defined('TS_CONFIG_TABLE')) { define('TS_CONFIG_TABLE', $table_prefix . 'tradeskill_config'); }
if (!defined('RP_TRADESKILL_TABLE')) { define('RP_TRADESKILL_TABLE', $table_prefix . 'tradeskills');}

//--------------

// Save this shit
if ($_POST['issavebu']){
  // global config
		UpdateTSConfig('ts_restrict_professions', $_POST['ts_restrict_professions']); # Restrict Professions to 2?
		//UpdateTSConfig('ts_show_cooking', $_POST['ts_show_cooking']); # Show Cooking? -> other mechanism
		UpdateTSConfig('ts_use_infosite', $_POST['ts_use_infosite']); # What infosite should be used
		UpdateTSConfig('ts_single_show', $_POST['ts_single_show']); # Use Single Show only

    if ($lala){
    echo($user->lang['is_conf_saved']);
    }
} 
    	
if ($_POST['trade_id']) { $info = UpdateTSinuse($_POST['trade_id'] ); }

//----------------

$sql = 'SELECT * FROM ' . TS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data: Config-Table', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $row[$roww['config_name']] = $roww['config_value'];
}

// Output
$sql = 'SELECT trade_id, trade_name, inuse FROM ' . RP_TRADESKILL_TABLE .'';
if (!($settings_result1 = $db->query($sql))) { message_die('Could not obtain configuration data: Tradeskill-Table', '', __FILE__, __LINE__, $sql); }

while($zeile = $db->fetch_record($settings_result1)) {
 
	$tpl->assign_block_vars('trades', array(
	'ROW_CLASS' => $eqdkp->switch_row_class(),
	'TRADE_ID' => $zeile['trade_id'],
	'INUSE' => ( $zeile['inuse'] == 1 ) ? ' checked="checked"' : '',
	'L_TRADE' => $zeile['trade_name'],
	)
	);
}


$tpl->assign_vars(array(
      'F_CONFIG'        => 'settings.php' . $SID,
     
      'RESTRICT_PROFS'	=> ( $row['ts_restrict_professions'] == 1 ) ? ' checked="checked"' : '',
     // 'SHOW_COOKING'	=> ( $row['ts_show_cooking'] == 1 ) ? ' checked="checked"' : '', -> other mechanism
      'SINGLE_SHOW_ONLY' => ( $row['ts_single_show'] == 1 ) ? ' checked="checked"' : '',
      'INFOSITE_SEL_DE'	=> ( $row['ts_use_infosite'] == "buffed" ) ? ' selected="selected" ' : ' ',
      'INFOSITE_SEL_EN'	=> ( $row['ts_use_infosite'] == "allakhazam" ) ? ' selected="selected" ' : ' ',
     
	'L_SELECT_USED_TRADESKILLS' => $user->lang['ts_used_ts'],
      
      // Language
      'L_SUBMIT'        => $user->lang['submit'],
      'L_RESET'         => $user->lang['reset'],
      
      'L_GENERAL'       => $user->lang['ts_settings_header'],
      
      'L_RESTRICT_PROFS' => $user->lang['ts_restrict_profs'],
      //'L_SHOW_COOKING'  => $user->lang['ts_show_cooking'],-> other mechanism
      'L_SINGLE_SHOW_ONLY' => $user->lang['ts_single_show_only'],
      'L_SINGLE_SHOW_ONLY_NOTE' => $user->lang['ts_single_show_only_note'],
      'L_INFOSITE'	=> $user->lang['ts_infosite'],

      'L_SEL_DE'	=> "buffed.de",
      'L_SEL_EN'	=> "allakhazam.com",
      )
		);
    
    $eqdkp->set_vars(array(
		'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Settings',
		'template_path' 	       => $pm->get_data('ts', 'template_path'),
		'template_file'          => 'admin/settings.html',
		'display'                => true)
    );
    

