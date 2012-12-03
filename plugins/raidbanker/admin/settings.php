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
global $table_prefix;
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidbanker');
$eqdkp_root_path = './../../../';
include_once('../includes/functions.php');

// Check if plugin is installed
if ( !$pm->check(PLUGIN_INSTALLED, 'raidbanker') )
{message_die('The Raid Banker plugin is not installed.');}

// Check user permission
$user->check_auth('a_raidbanker_config');
$rb = $pm->get_plugin('raidbanker');

// ##################################################### the update checker code
$vc_link        = "http://eqdkp.corgan-net.de/vcheck/version.php";
$vc_server      = 'eqdkp.corgan-net.de';
$pluginname_vc  = 'raidbanker';
// static part

// ** RB LINK CHECK FUNCTION
function RBchecklink($url){
	global $vc_server;
  if($url) {
   		$dat = @fsockopen ($vc_server, 80, $errno, $errstr, 4);
    	//$dat = @fopen ($url, "r");
  }
  if($dat){
    return true;
    fclose($dat);
  } else {
    return false;
  } 
}
// ** The Rest...
if(RBchecklink($vc_link)){
    // get the version Data:
    		if (function_exists('file_get_contents')){
					$getdata = file_get_contents($vc_link.'?plugin='.$pluginname_vc);
				}else{
					$pparray = file ($vc_link.'?plugin='.$pluginname_vc);
					$getdata = $pparray[0];
				}
				$parse = explode('|', $getdata);
				$versions['version'] = $parse[0];
				$versions['level'] = $parse[1];
				$versions['changelog'] = $parse[2];
				$versions['download'] = $parse[3];
				$versions['release'] = $parse[5];
				$versions['name'] = $parse[6];
    if($versions['version'] == $pm->get_data($pluginname_vc, 'version') || $versions['version'] < $pm->get_data($pluginname_vc, 'version')){
        $vc_output = "";
        $vc_updatewindow = false;
    }else{
        $vc_output = $user->lang['rb_update_available_p1']." ".
          $user->lang['rb_update_available_p2']." <b>".$pm->get_data($pluginname_vc, 'version')."</b> ".
          $user->lang['rb_update_available_p3']." <b>".$versions['version'] ." (".$user->lang['rb_updated_date'].": ".date($user->lang['rb_timeformat'], $versions['release']).")</b><br>".
          "[".$user->lang['rb_release_level'].": ".$versions['level']."] <a target='_blank' href='".$versions['download']."'>".$user->lang['rb_update_url']."</a> | <a target='_blank' href='".$versions['changelog']."'>".$user->lang['rb_changelog_url']."</a>";
        $vc_updatewindow = true;
    }
} else {
        $vc_output = $user->lang['rb_noserver'];
        $vc_updatewindow =true;
} 
$tpl->assign_vars(array(
      'VC_SHOW'         => $vc_updatewindow,
      'VC_TEXT'         => $vc_output,
));
// ######################################################### End of Update Check

// Save this shit
if ($_POST['issavebu']){
  // global config
    UpdateRBConfig('rb_itemstats', $_POST['rb_itemstats']); # Show ranks in raid planner?
		UpdateRBConfig('rb_hide_banker', $_POST['rb_hide_banker']); # Show only short ranks?
		UpdateRBConfig('rb_no_bankers', $_POST['rb_no_bankers']); # Email raids to all users
		UpdateRBConfig('rb_is_cache', $_POST['rb_is_cache']); # Should we use the roll-system?
		UpdateRBConfig('rb_show_money', $_POST['rb_show_money']); # Should we use the roll-system?
		UpdateRBConfig('rb_list_lang', $_POST['rb_list_lang']); # The itemlist language
		UpdateRBConfig('rb_show_tooltip', $_POST['rb_show_tooltip']); # The Tooltip
		UpdateRBConfig('rb_oldstyle', $_POST['rb_oldstyle']); # OldStyle
		UpdateRBConfig('rb_auto_adjustment', $_POST['rb_auto_adjustment']); # Auto Adjustment
		$tinfo = UpdateRBConfig('rb_is_path', $_POST['rb_is_path']);   # Should we use the wildcard-system?
		if ($tinfo){
      $info = true;
    }else{
      $info = false;
    }
} 

// get the config
$sql = 'SELECT * FROM ' . RB_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

// Output
$tpl->assign_vars(array(
      'F_CONFIG'        => 'settings.php' . $SID,
      
      'USE_ITEMSTATS'     => ( $conf['rb_itemstats'] == 1 ) ? ' checked="checked"' : '',
      'HIDE_BANKER'       => ( $conf['rb_hide_banker'] == 1 ) ? ' checked="checked"' : '',
      'HIDE_MONEY'        => ( $conf['rb_show_money'] == 1 ) ? ' checked="checked"' : '',
      'NO_BANKER'         => ( $conf['rb_no_bankers'] == 1 ) ? ' checked="checked"' : '',
      'SHOW_TOOLTIP'      => ( $conf['rb_show_tooltip'] == 1 ) ? ' checked="checked"' : '',
      'AUTO_ADJUST'       => ( $conf['rb_auto_adjustment'] == 1 ) ? ' checked="checked"' : '',
      'OLDSTYLE_ON'				=> ( $conf['rb_oldstyle'] == 1 ) ? ' checked="checked"' : '',
      'IS_CACHE'          => $conf['rb_is_cache'],
      'IS_PATH'           => $conf['rb_is_path'],
      
      'U_INFO_BOX'        => ( $_POST['issavebu'] ) ? true : false,
      'U_SAVED_SUCC'      => ( $_POST['issavebu'] && $info == true ) ? true : false,
      'U_SAVED_NOT'       => ( $_POST['issavebu'] && $info == false ) ? true : false,
      
      'LOCAL_SEL_DE'    => ( $conf['rb_list_lang'] == "german" ) ? ' selected="selected"' : '',
      'LOCAL_SEL_EN'    => ( $conf['rb_list_lang'] == "english" ) ? ' selected="selected"' : '',
      
      // Language
      'L_SUBMIT'        => $user->lang['submit'],
      'L_RESET'         => $user->lang['reset'],
      
      'L_INFO_BOX'      => $user->lang['rb_info_box'],
      'L_SAVED_SUCC'    => $user->lang['rb_saved'],
      'L_UPDATED_SUCC'  => $user->lang['rb_failed'],
      
      'L_GENERAL'       => $user->lang['rb_header_global'],
      
      'L_USE_ITEMSTATS' => $user->lang['rb_use_itemstats'],
      'L_HIDE_BANKER'   => $user->lang['rb_hide_banker'],
      'L_HIDE_MONEY'    => $user->lang['rb_hide_money'],
      'L_NO_BANKER'     => $user->lang['rb_no_banker'],
      'L_LIST_LANG'     => $user->lang['rb_list_lang'],
      'L_OLDSTYLE'     	=> $user->lang['rb_is_oldstyle'],
      'L_SHOW_TOOLTIP'  => $user->lang['rb_show_tooltip'],
      'L_AUTO_ADJUST'   => $user->lang['rb_auto_adjust'],
      'L_GERMAN'        => $user->lang['rb_locale_de'],
      'L_ENGLISH'       => $user->lang['rb_locale_en'],
      'L_IS_CACHE'      => $user->lang['rb_is_cache'],
      'L_IS_PATH'       => $user->lang['rb_is_path'],
      )
		);
    
    $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Settings',
			'template_path' 	       => $pm->get_data('raidbanker', 'template_path'),
			'template_file'          => 'admin/settings.html',
			'display'                => true)
    );
    

