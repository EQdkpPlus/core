<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');

$pdc_cache_types = array('none', 'file', 'xcache', 'memcache', 'apc');

$usable_cache_types = array('none', 'file', 'xcache', 'memcache');
if(function_exists(apc_store) && function_exists(apc_fetch) && function_exists(apc_delete)){
  $usable_cache_types[] = 'apc';
}

if(function_exists(xcache_set) && function_exists(xcache_get) && function_exists(xcache_unset)){
  $usable_cache_types[] = 'xcache';
}

if ($in->get('cache_clear')){
  $pdc->flush();
}else if ($in->get('cache_cleanup')){
  $pdc->cleanup();
}else if ($in->get('cache_save')){
  $selected_cache = $in->get('cache_selection', '');
  if(!in_array($selected_cache, $pdc_cache_types)){
    message_die("Invalid Cache selected!");
  }else{
    $pdc_array['mode'] = $selected_cache;
    $pdc_array[$selected_cache] = $in->getArray('cache_'.$selected_cache, '');
    $pdc->flush();
    $core->config_set('pdc', $pdc_array);
  }
}

//install setup
//<GodMod|afk>sz3: config_data.php im install/schemas

//if(!isset($core->config['pk_known_task_count']) || $core->config['pk_known_task_count'] < $task_count){
//  $core->config_set('pk_maintenance_mode', true);
//  $core->config['cache_mode']

$current_cache = (isset($core->config['pdc']['mode'])) ? $core->config['pdc']['mode'] : 'none';

foreach($pdc_cache_types as $cache_type){
  if(in_array($cache_type, $usable_cache_types)){
    $tpl->assign_block_vars('cache_selection_row', array(
      'VALUE'    => $cache_type,
      'SELECTED' => ($cache_type == $current_cache) ? ' selected="selected"' : '',
      'OPTION'   => ($cache_type == $current_cache) ? '* '.$user->lang["pdc_cache_name_$cache_type"] : $user->lang["pdc_cache_name_$cache_type"],
    ));
  }    
  
  if($cache_type == $current_cache){
    $tpl->assign_var('DIV_CACHE_'.strtoupper($cache_type).'_VISIBLE', 'block');
  }else{
    $tpl->assign_var('DIV_CACHE_'.strtoupper($cache_type).'_VISIBLE', 'none');
  }
  
  if(isset($core->config['pdc'][$cache_type]['dttl'])){
    $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_DTTL', $core->config['pdc'][$cache_type]['dttl']);
  }else{
    $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_DTTL', 86400);
  }
  
  if(isset($core->config['pdc'][$cache_type]['prefix'])){
    $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PREFIX', $core->config['pdc'][$cache_type]['prefix']);
  }else{
    $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PREFIX', $table_prefix);
  }
  
  if($cache_type == 'memcache'){
    if(isset($core->config['pdc'][$cache_type]['server'])){
      $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_SERVER', $core->config['pdc'][$cache_type]['server']);
    }else{
      $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_SERVER', '127.0.0.1');
    }
    if(isset($core->config['pdc'][$cache_type]['port'])){
      $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PORT', $core->config['pdc'][$cache_type]['port']);
    }else{
      $tpl->assign_var('V_CACHE_'.strtoupper($cache_type).'_PORT', '11211');
    }
  }
}

$tpl->add_js('
function selectDiv(divId){
  var hiddenCacheDivs = document.getElementById("allCacheDivs");
  cacheDivArray = hiddenCacheDivs.getElementsByTagName("div");
  divId = "div_cache_"+divId;

  for(x=0; x<cacheDivArray.length; x++){
    if(cacheDivArray[x].id == divId){
      cacheDivArray[x].style.display = "block";
    }else{
      cacheDivArray[x].style.display = "none";
    }    
  }
}
');

$cache_list = $pdc->listing();
$total = 0;
$cache_table = '<tr><th>'.$user->lang['pdc_globalprefix'].'</th><th>'.$user->lang['pdc_entity'].'</th><th>'.$user->lang['pdc_status'].'</th></tr>';
$ctime = time();

foreach($cache_list as $global_prefix => $keys){
  	foreach($keys as $key => $expiry_date){
			$tpl->assign_block_vars('cache_entity_list_row', array (
					'ROW_CLASS'	=> $core->switch_row_class(),
					'GLOBAL_PREFIX' => $global_prefix,
					'KEY' => $key,
					'EXPIRED' => ($expiry_date < $ctime) ? '<span class="negative">'.$user->lang['pdc_entity_expired'].'</span>':'<span class="positive">'.$user->lang['pdc_entity_valid'].'</span>'
				)
			);
	}
}

$tpl->assign_vars(array (
	'F_CONFIG' => 'manage_cache.php' . $SID,
  'CACHE_TABLE' => $cache_table,
  'L_CLEANUP_CACHE' => $user->lang['pdc_cleanup'],
  'L_CLEAR_CACHE' => $user->lang['pdc_clear'],
  'L_SAVE_CACHE_SETTINGS' => $user->lang['pdc_save'],
//  'L_ITT_CACHE_CLEAR' => $user->lang['pk_itt_reset'],
  'L_HEAD_SETTINGS' => $user->lang['pdc_manager_settings'],
  'L_CACHE_NONE_INFO' => $user->lang['pdc_cache_info_none'],
  'L_CACHE_FILE_INFO' => $user->lang['pdc_cache_info_file'],
  'L_CACHE_XCACHE_INFO' => $user->lang['pdc_cache_info_xcache'],
  'L_CACHE_APC_INFO' => $user->lang['pdc_cache_info_apc'],
  'L_CACHE_MEMCACHE_INFO' => $user->lang['pdc_cache_info_memcache'],
  'L_CACHE_SELECT' => $user->lang['pdc_cache_select_text'],
  'I_CACHE_SELECT' => $user->lang['pdc_cache_select_info'],
  'L_CACHE_HEAD_SETTINGS' => $user->lang['pdc_settings'],
  'L_CACHE_HEAD_TABLE' => $user->lang['pdc_table'],
  'L_CACHE_TABLE_INFO' => sprintf($user->lang['pdc_table_info'], $user->lang["pdc_cache_name_$current_cache"]),
  'PDC_CACHE_ENABLED' => ($current_cache == 'none') ? false : true,  
  'L_CACHE_DTTL' => $user->lang['pdc_dttl_text'],
  'H_CACHE_DTTL' => $user->lang['pdc_dttl_help'],
  'L_CACHE_GPRE' => $user->lang['pdc_globalprefix'],
  'L_CACHE_KEY' => $user->lang['pdc_entity'],
  'L_CACHE_STATUS' => $user->lang['pdc_status'],
  'L_CACHE_PREFIX' => $user->lang['pdc_globalprefix'],
  'H_CACHE_PREFIX' => $user->lang['pdc_globalprefix_help'],
	'L_CACHE_MEMCACHE_SERVER' => $user->lang['pdc_memcache_server_text'],
	'L_CACHE_MEMCACHE_PORT' => $user->lang['pdc_memcache_port_text'],
	'H_CACHE_MEMCACHE_SERVER' => $user->lang['pdc_memcache_server_help'],
	'H_CACHE_MEMCACHE_PORT' => $user->lang['pdc_memcache_port_help'],
	
));


$core->set_vars(array (
	'page_title' => $user->lang['pdc_manager'],
	'template_file' => 'admin/manage_cache.html',
  'display' => true
	)
);

?>
