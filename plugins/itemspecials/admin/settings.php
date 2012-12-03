<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * settings.php
 * Changed: November 10, 2006
 * 
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'itemspecials');
include_once('../include/functions.php');
include_once('../include/db.class.php');
include_once('../include/itemstatsadditions.class.php');

$isdb = new ItemSpecialsDB();

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

// this is the fix for the AJAX CrossDomainScript Problem.
$redirect_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(ereg("//", $redirect_url)){
	$redirURL = ereg_replace("//","/",$redirect_url);
	$settings_link = 'http://'.$redirURL;
	echo '<meta http-equiv="refresh" content="0; URL='.$settings_link.'">';
}

// Check user permission
$user->check_auth('a_itemspecials_conf');
$rb = $pm->get_plugin('itemspecials');

global $table_prefix;
if (!defined('IS_CONFIG_TABLE')) { define('IS_CONFIG_TABLE', $table_prefix . 'itemspecials_config'); }
if (!defined('IS_CUSTOM_TABLE')) { define('IS_CUSTOM_TABLE', $table_prefix . 'itemspecials_custom'); }

if ( !$pm->check(PLUGIN_INSTALLED, 'itemspecials') )
{
    message_die('The ItemSpecials plugin is not installed.');
}

// Save the item addition
if (isset($_POST['itemname'])){
    $sql = "INSERT INTO ".IS_CUSTOM_TABLE." (`custom_name`, `item_name`, `set`) VALUES ('".$isdb->stripName($_POST['itemname'])."', '".$_POST['itemname']."', 'itempool');";
    if($db->query($sql)){   
    	echo '<script LANGUAGE="JavaScript">
    top.location.href=\'./settings.php\'
</script>';
    }
}

if (isset($_POST['item_del'])){
  delete_customitem($_POST['item_del']);
  echo '<script LANGUAGE="JavaScript">
    top.location.href=\'./settings.php\'
</script>';
}

// Save this shit
if ($_POST['issavebu']){
  // global config
    $savearray = array(
    'is_exec_time'            => $_POST['is_exec_time'],
    'is_updatecheck'          => $_POST['is_updatecheck'],
		'locale'                  => $_POST['locale'],
		'race'                    => $_POST['race'],
		'imgwidth'                => $_POST['imgwidth'],
		'imgheight'               => $_POST['imgheight'],
		'hide_inactives'          => $_POST['hide_inactives'],
		'hidden_groups'           => $_POST['hidden_groups'],
		'colouredcls'             => $_POST['colouredcls'],
		'itemstats'               => $_POST['itemstats'],
		'is_replace'              => $_POST['is_replace'],
		'is_correctmode'          => $_POST['is_correctmode'],
		'nonset_set'              => $_POST['nonset_set'],
		'nonsettable'             => $_POST['nonsettable'],
		'settable'                => $_POST['settable'], 
		'header_images'           => $_POST['header_images'],
    'si_only_crosses'         => $_POST['si_only_crosses'],
    'si_itemstatus_show'      => $_POST['si_itemstatus_show'],  
    'si_rank'                 => $_POST['si_rank'], 
		'si_points'               => $_POST['si_points'],
		'si_total'                => $_POST['si_total'],
		'si_class'                => $_POST['si_class'],
		'si_cls_icon'             => $_POST['si_cls_icon'],
	  'si_bwltrinket'           => $_POST['si_bwltrinket'],
	  'si_aqmount'              => $_POST['si_aqmount'],
		'si_aqbook'               => $_POST['si_aqbook'],
		'si_atiesh'              	=> $_POST['si_atiesh'],
		'set_rank'                => $_POST['set_rank'],
		'set_points'              => $_POST['set_points'],
		'set_total'               => $_POST['set_total'],
		'set_class'               => $_POST['set_class'],
		'set_cls_icon'            => $_POST['set_cls_icon'],
		'set_show_t1'             => $_POST['set_show_t1'],
		'set_show_t2'             => $_POST['set_show_t2'],
		'set_show_tAQ'            => $_POST['set_show_tAQ'],
		'set_show_t3'             => $_POST['set_show_t3'],
		'set_show_index'          => $_POST['set_show_index'],
		'set_drpdwn_cls'          => $_POST['set_drpdwn_cls'],
		'set_op_rank'             => $_POST['set_op_rank'],
		'set_op_points'           => $_POST['set_op_points'],
		'set_op_total'            => $_POST['set_op_total'],
		'set_op_class'            => $_POST['set_op_class'],
		'set_op_cls_icon'         => $_POST['set_op_cls_icon'],
		'set_oldLink'         		=> $_POST['set_oldLink'],
		'sr_rank'                 => $_POST['sr_rank'],
		'sr_points'               => $_POST['sr_points'],
		'sr_class'                => $_POST['sr_class'],
		'sr_cls_icon'             => $_POST['sr_cls_icon'],
		);
	
		if ($isdb->UpdateConfig($savearray, $isdb->CheckDBFields('itemspecials_config', 'config_name'))){
      $info = true;
    }else{
      $info = false;
    }
} elseif ($_POST['del'] == "reset"){
  ResetIStoDefault();
} elseif (isset($_POST['langval'])){
  SetISdbLanguage($_POST['langval']);
}

$sql = 'SELECT * FROM ' . IS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $row[$roww['config_name']] = $roww['config_value'];
}

if( $row['is_updatecheck'] == 1 ){
// ##################################################### the update checker code
$vc_link        = "http://eqdkp.corgan-net.de/vcheck/version.php";
$vc_server      = 'eqdkp.corgan-net.de';
$pluginname_vc  = 'itemspecials';
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
					$getdata = @file_get_contents($vc_link.'?plugin='.$pluginname_vc);
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
        $vc_output = $user->lang['is_update_available_p1']." ".
          $user->lang['is_update_available_p2']." <b>".$pm->get_data($pluginname_vc, 'version')."</b> ".
          $user->lang['is_update_available_p3']." <b>".$versions['version'] ." (".$user->lang['is_updated_date'].": ".date($user->lang['is_timeformat'], $versions['release']).")</b><br>".
          "[".$user->lang['is_release_level'].": ".$versions['level']."] <a target='_blank' href='".$versions['download']."'>".$user->lang['is_update_url']."</a> | <a target='_blank' href='".$versions['changelog']."'>".$user->lang['is_changelog_url']."</a>";
        $vc_updatewindow = true;
    }
} else {
        $vc_output = $user->lang['is_noserver'];
        $vc_updatewindow =true;
} 
$tpl->assign_vars(array(
      'VC_SHOW'         => $vc_updatewindow,
      'VC_TEXT'         => $vc_output,
));
// ######################################################### End of Update Check
}

include_once('../include/data/'.$row['locale'].'/set.php');
include_once('../include/data/'.$row['locale'].'/special.php');

// Cache Part
if($_GET['cache'] == "reset" or $_POST['cache'] == "reset"){
// Load the itemspecials Data!
  include_once('../include/data.php');
    if ($row['itemstats'] == true){
      include_once($eqdkp_root_path.'itemstats/eqdkp_itemstats.php');
      $isaddition = new ItemstatsAddition();
      $is_version2 = ($isaddition->GetItemstatsVersion()) ? true : false;
      $sql = "SELECT * FROM ".IS_CUSTOM_TABLE;
      $custom = array();
      if (!($custom_result = $db->query($sql))) { message_die('Could not obtain custom item data', '', __FILE__, __LINE__, $sql); }
        while($customrow = $db->fetch_record($custom_result)) {
          $custom[] = $customrow['item_name'];
        }

      //get the Tier Infos which are not shown directly
      $array_name1 = "setitems_Tier3";
      $array_name2 = "setitems_TierAQ";
      $array_name3 = "trinket_items";
      $array_name4 = "aqbook_items";
      $array_name5 = "mount_items";
      $array_name6 = "atiesh_items";
      $merge1 = array_merge_recursive($$array_name1, $$array_name2, $$array_name4, $$array_name5, $$array_name3);
      $merge1 = array_values_recursive($merge1);
      $items = array_merge_recursive($merge1, $custom, $$array_name6);

     foreach($items AS $value){
      $isaddition->itemstats_decorate_Icon($value, '', $is_version2, true);
      }
      AddKeyToNaxxramas(true);
      $dataSet = array('download_cache'=> '1');
      $isdb->UpdateConfig($dataSet, $isdb->CheckDBFields('itemspecials_config', 'config_name'));
      }
}

// AJAX part
global $dbpass, $dbhost, $dbuser, $dbname;
mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname);
function parse_data($data)
{
  $containers = explode(":", $data);
  foreach($containers AS $container)
  {
      $container = str_replace(")", "", $container);
      $i = 0;
      $lastly = explode("(", $container);
      $values = explode(",", $lastly[1]);
      foreach($values AS $value)
      {
        if($value == '')
        {
            continue;
        }
        $final[$lastly[0]][] = $value;
        $i ++;
      }
  }
    return $final;
}

function update_db($data_array, $col_check)
{
global $table_prefix;

  foreach($data_array AS $set => $items)
  {
     $i = 0;
     foreach($items AS $item)
     {
       $item = mysql_escape_string($item);
       $set  = mysql_escape_string($set);
       
       mysql_query("UPDATE " . $table_prefix . "itemspecials_custom SET `set` = '$set', `order` = '$i'  WHERE `custom_name` = '$item' $col_check");
       $i ++;
     }
  }
}

// Lets setup Sajax
require_once('../include/Sajax.php');
sajax_init();
 $sajax_debug_mode = 0;

function sajax_update($data)
{
  $data = parse_data($data);
  update_db($data, "AND (`set` = 'itempool' OR `set` = 'itemshow')");
  return 'y';
}

sajax_export("sajax_update");
sajax_handle_client_request();


if(isset($_POST['order']))
{
  $data = parse_data($_POST['order']);
  update_db($data, "AND (`set` = 'left_col' OR `set` = 'right_col' OR `set` = 'center')");
  // redirect so refresh doesnt reset order to last save
  header("location: settings.php");
  exit;
}

$sql ="SELECT * FROM ".IS_CUSTOM_TABLE." WHERE `set` = 'itempool' ORDER BY `order` ASC";
$custom_result = $db->query($sql);
while($customrow = $db->fetch_record($custom_result)) {
  $tpl->assign_block_vars('itempool_row', array(
              'ID'        => ( get_magic_quotes_gpc()== 0 ) ? addslashes($customrow['custom_name']) : $customrow['custom_name'],
              'NAME'      => $customrow['item_name']
              )
          );
}

$sql ="SELECT * FROM ".IS_CUSTOM_TABLE." WHERE `set` = 'itemshow' ORDER BY `order` ASC";
$custom_result = $db->query($sql);
while($customrow = $db->fetch_record($custom_result)) {
  $tpl->assign_block_vars('itemshow_row', array(
              'ID'        => ( get_magic_quotes_gpc()== 0 ) ? addslashes($customrow['custom_name']) : $customrow['custom_name'],
              'NAME'      => $customrow['item_name']
              )
          );
}
// End of AJAX Part

// Output
$tpl->assign_vars(array(
      'F_CONFIG'        => 'settings.php' . $SID,
      'F_CUSTOM'        => 'customitems.php' . $SID,
      
      'CSS_AJAX'        => sajax_get_javascript(),
      
      'U_INFO_BOX'      => ( $_POST['issavebu'] ) ? true : false,
      'U_SAVED_SUCC'    => ( $_POST['issavebu'] && $info == true ) ? true : false,
      'U_SAVED_NOT'     => ( $_POST['issavebu'] && $info == false ) ? true : false,
      
      'RACE_SEL_AL'     => ( $row['race'] == "Al" ) ? ' selected="selected"' : '',
      'RACE_SEL_HO'     => ( $row['race'] == "Ho" ) ? ' selected="selected"' : '',
      'LOCAL_SEL_DE'    => ( $row['locale'] == "de" ) ? ' selected="selected"' : '',
      'LOCAL_SEL_EN'    => ( $row['locale'] == "en" ) ? ' selected="selected"' : '',
      'LOCAL_SEL_FR'    => ( $row['locale'] == "fr" ) ? ' selected="selected"' : '',
      'LOCAL_SEL_CH'    => ( $row['locale'] == "ch" ) ? ' selected="selected"' : '',
      'EXEC_TIME'       => ( $row['is_exec_time'] == 1 ) ? ' checked="checked"' : '',
      'UPDATE_CHECK'    => ( $row['is_updatecheck'] == 1 ) ? ' checked="checked"' : '',
      'IMGWIDTH'        => $row['imgwidth'],
      'IMGHEIGHT'       => $row['imgheight'],
      'ITEMSTATS'       => ( $row['itemstats'] == 1 ) ? ' checked="checked"' : '',
      'IS_NAME_CORRECT' => ( $row['is_correctmode'] == 1 ) ? ' checked="checked"' : '',
      'IS_REPLACE'      => $row['is_replace'],
      'COLOREDCLS'      => ( $row['colouredcls'] == 1 ) ? ' checked="checked"' : '',
      'HIDE_INACTIVE'   => ( $row['hide_inactives'] == 1 ) ? ' checked="checked"' : '',
      'HIDDEN_GRP'      => ( $row['hidden_groups'] == 1 ) ? ' checked="checked"' : '',
      'NONSET_SET'      => ( $row['nonset_set'] == 1 ) ? ' checked="checked"' : '',
      'NONSETTABLE'     => $row['nonsettable'],
      'SETTABLE'        => $row['settable'],
      
      // SpecialItems
      'HEADER_IMG'      => ( $row['header_images'] == 1 ) ? ' checked="checked"' : '',
      'SETITEM_TEMP'    => ( $row['download_cache'] == 0 ) ? '<font color=red>'.$user->lang['is_cache_notloaded'].'</font> (<a onclick="javascript:LoadCache()" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';" onmouseout="style.textDecoration=\'none\';">'.$user->lang['is_cache_load'].'</a>)' : '<font color=green>'.$user->lang['is_cache_loaded'].'</font> (<a onclick="javascript:LoadCache()" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';" onmouseout="style.textDecoration=\'none\';">'.$user->lang['is_cache_reload'].'</a>)',
      'SI_STATUSSHOW'   => ( $row['si_itemstatus_show'] == 1 ) ? ' checked="checked"' : '',
      'SI_CROSSES'      => ( $row['si_only_crosses'] == 1 ) ? ' checked="checked"' : '',
      'SI_RANK'         => ( $row['si_rank'] == 1 ) ? ' checked="checked"' : '',
      'SI_POINTS'       => ( $row['si_points'] == 1 ) ? ' checked="checked"' : '',
      'SI_TOTAL'        => ( $row['si_total'] == 1 ) ? ' checked="checked"' : '',
      'SI_CLASS'        => ( $row['si_class'] == 1 ) ? ' checked="checked"' : '',
      'SI_CLS_ICON'     => ( $row['si_cls_icon'] == 1 ) ? ' checked="checked"' : '',
      // enable/disable the rows
      'SI_BWLTRINKET'   => ( $row['si_bwltrinket'] == 1 ) ? ' checked="checked"' : '',
      'SI_AQMOUNT'      => ( $row['si_aqmount'] == 1 ) ? ' checked="checked"' : '',
      'SI_AQBOOK'       => ( $row['si_aqbook'] == 1 ) ? ' checked="checked"' : '',
      'SI_ATIESH'       => ( $row['si_atiesh'] == 1 ) ? ' checked="checked"' : '',
      
      //Setitems
      'SET_RANK'        => ( $row['set_rank'] == 1 ) ? ' checked="checked"' : '',
      'SET_POINTS'      => ( $row['set_points'] == 1 ) ? ' checked="checked"' : '',
      'SET_TOTAL'       => ( $row['set_total'] == 1 ) ? ' checked="checked"' : '',
      'SET_CLASS'       => ( $row['set_class'] == 1 ) ? ' checked="checked"' : '',
      'SET_CLS_ICON'    => ( $row['set_cls_icon'] == 1 ) ? ' checked="checked"' : '',
      'SET_TIER_1'      => ( $row['set_show_t1'] == 1 ) ? ' checked="checked"' : '',
      'SET_TIER_2'      => ( $row['set_show_t2'] == 1 ) ? ' checked="checked"' : '',
      'SET_TIER_3'      => ( $row['set_show_t3'] == 1 ) ? ' checked="checked"' : '',
      'SET_TIER_AQ'     => ( $row['set_show_tAQ'] == 1 ) ? ' checked="checked"' : '',
      'SET_INDEX'       => ( $row['set_show_index'] == 1 ) ? ' checked="checked"' : '',
      'SET_DROPDOWN'    => ( $row['set_drpdwn_cls'] == 1 ) ? ' checked="checked"' : '',
      'SET_OP_RANK'     => ( $row['set_op_rank'] == 1 ) ? ' checked="checked"' : '',
      'SET_OP_POINTS'   => ( $row['set_op_points'] == 1 ) ? ' checked="checked"' : '',
      'SET_OP_TOTAL'    => ( $row['set_op_total'] == 1 ) ? ' checked="checked"' : '',
      'SET_OP_CLASS'    => ( $row['set_op_class'] == 1 ) ? ' checked="checked"' : '',
      'SET_OP_CLS_ICON' => ( $row['set_op_cls_icon'] == 1 ) ? ' checked="checked"' : '',
      'SET_OLD_LINKSTY' => ( $row['set_oldLink'] == 1 ) ? ' checked="checked"' : '',
      
      // Setrights
      'SR_RANK'         => ( $row['sr_rank'] == 1 ) ? ' checked="checked"' : '',
      'SR_POINTS'       => ( $row['sr_points'] == 1 ) ? ' checked="checked"' : '',
      'SR_CLASS'        => ( $row['sr_class'] == 1 ) ? ' checked="checked"' : '',
      'SR_CLS_ICON'     => ( $row['sr_cls_icon'] == 1 ) ? ' checked="checked"' : '',
      
      // Language
      'L_INFO_BOX'      => $user->lang['is_info_box'],
      'L_SAVED_SUCC'    => $user->lang['is_setting_saved'],
      'L_SAVED_NOT'     => $user->lang['is_setting_failed'],
      
      'L_SUBMIT'        => $user->lang['submit'],
      'L_VERSION'       => $pm->get_data('itemspecials', 'version'),
      'L_SETDEFAULTS'   => $user->lang['is_set_default'],
      'L_RESET'         => $user->lang['reset'],
      'L_HEADER_HELP'		=> $user->lang['is_open_cat'],
      'L_ALLEXPAND'			=> $user->lang['is_expand_all'],
      'L_HEADER_DBS'		=> $user->lang['is_header_database'],
      'L_GENERAL'       => $user->lang['is_header_global'],
      'L_SPECIALIT'     => $user->lang['is_header_special'],
      'L_SETRIGHT'      => $user->lang['is_header_setright'],
      'L_SETITEM'       => $user->lang['is_header_setitem'],
      'L_SHOWONPAGE'    => $user->lang['is_header_page'],
      'L_ISTT_HEADER'		=> $user->lang['is_header_itemstats'],
      'L_SHOW_EXEC'     => $user->lang['is_conf_exec'],
      'L_UPDATECHECK'		=> $user->lang['is_updatecheck'],
      'L_HELP_UPDATECH'	=> $user->lang['is_help_updcheck'],
      'L_LANGUAGE'      => $user->lang['is_locale_name'],
      'L_ENGLISH'       => $user->lang['is_locale_en'],
      'L_GERMAN'        => $user->lang['is_locale_de'],
      'L_FRENCH'        => $user->lang['is_locale_fr'],
      'L_CHINESE'       => $user->lang['is_locale_ch'],
      'L_RACE'          => $user->lang['is_race_name'],
      'L_HORDE'         => $user->lang['is_race_ho'],
      'L_ALLIANCE'      => $user->lang['is_race_al'],
      'L_ICONS'         => $user->lang['is_icons'],
      'L_ICONS_HLP'     => $user->lang['is_icons_hlp'],
      'L_OLDSTYLE_LINKS'=> $user->lang['is_oldstyle_links'],
      'L_SHOW_CROSS'    => $user->lang['is_crosses'],
      'L_ITEMSTATS'     => $user->lang['is_itemstats'],
      'L_NAME_CORRECT'	=> $user->lang['is_name_correct'],
      'L_NAME_CORR_EX'	=> $user->lang['is_name_corr_expl'],
      'L_IS_REPLACE'    => $user->lang['is_itemst_replace'],
      'L_HELP_REPLACE' 	=> $user->lang['is_isreplace_help'],
      'L_HIDE_INACT'    => $user->lang['is_hide_inactive'],
      'L_HIDEN_GRP'     => $user->lang['is_hidden_groups'],
      'L_COLORD_CLS'    => $user->lang['is_colord_class'],
      'L_SHOW_RANK'     => $user->lang['is_show_rank'],
      'L_SHOW_POINTS'   => $user->lang['is_show_points'],
      'L_SHOW_TOTAL'    => $user->lang['is_show_total'],
      'L_SHOW_CLS'      => $user->lang['is_show_class'],
      'L_SHOW_DRPDWN'   => $user->lang['is_show_dropdown'],
      'L_SHOW_CLS_IMG'  => $user->lang['is_show_cls_img'],
      'L_IS_SET'        => $user->lang['is_settable'],
      'L_IS_NONSET'     => $user->lang['is_nonsettable'],
      'L_USE_SETDB'     => $user->lang['is_use_nonset'],
      'L_WARNING'       => $user->lang['is_warning'],
      'L_WARN_TXT'      => $user->lang['is_setdefaults'],
      
      'L_HELP_BOX_HEAD'	=> $user->lang['is_help_box_head'],      
      'L_HELP_NONSET'		=> $user->lang['is_help_nonset'],
      'L_HELP_CROSS'		=> $user->lang['is_help_crosses'],
      
      // AJAX Part
      'L_AX_ENABLED_I'  => $user->lang['is_enabled_items'], 
      'L_AX_UNUSED_I'   => $user->lang['is_unused_items'],
      'L_POPULATE'      => $user->lang['is_populate'],
      'L_DEL_WARNING'   => $user->lang['is_del_warning'],
      'L_DEL_NOTHING'   => $user->lang['is_del_nothing'],
      'L_BUTTON_CANCEL' => $user->lang['is_button_cancel'],
      'L_BUTTON_ACTION' => $user->lang['is_button_action'],
      'L_RESET_WARNING' => $user->lang['is_want_reset'],
      'L_BUTTON_I_ADD'  => $user->lang['is_button_add_item'],
      'L_BUTTON_I_DEL'  => $user->lang['is_button_del_item'],
      'L_AJAX_LOADING'  => $user->lang['is_loading'],
      'L_CLOSE'         => $user->lang['is_button_close'],
      'L_NO_ITEMSTATS'  => $user->lang['is_no_itemstats'],
      'L_IS_CACHE_REL'  => $user->lang['is_cache_succ_loaded'],
      'L_BUTTON_OK'     => $user->lang['is_button_ok'],
      'L_INFO_DRAGDROP'	=> $user->lang['is_info_dragdrop'],
      'L_ITEMLIST'			=> $user->lang['is_special_list'],
      
      // Setitems
      'L_SETITEM_OP'    => $user->lang['is_header_onepage'],
      'L_SHOW_TIER'     => $user->lang['is_show_tier'],
      'L_SHOW_INDEX'    => $user->lang['is_show_index'],
      'L_SHOW_ITEMSTA'  => $user->lang['is_itemstatus_show'],
      
      // Specialitems
      'L_HEADER_IMG'    => $user->lang['is_header_images'],
      'L_ITEMSTATUS'    => $user->lang['is_itemstatus_show'],
      'L_HELP_JJSTATUS'	=> $user->lang['is_itemstatus_help'],
      'L_TRINKET'       => $user->lang['is_show']." ".$user->lang['is_Trinket'],
      'L_AQMOUNT'       => $user->lang['is_show']." ".$user->lang['is_aqmount'],
      'L_AQ_BOOK'       => $user->lang['is_show']." ".$user->lang['is_aqbook'],
      'L_ATIESH'				=> $user->lang['is_show']." ".$user->lang['is_atiesh'],
        )
		);
    
    $eqdkp->set_vars(array(
	    'page_title'             => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_conf_pagetitle'],
			'template_path' 	       => $pm->get_data('itemspecials', 'template_path'),
			'template_file'          => 'admin/settings.html',
			'display'                => true)
    );
?>