<?php
 /*
 * Project:     EQdkp TwinkIt (v0.7 eqdkp plus sandbox test)
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2008-11-08 00:37:24 +0100 (Sa, 08 Nov 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: sz3 $
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     twinkit
 * @version     $Rev: 3021 $
 * 
 * $Id: settings.php 3021 2008-11-07 23:37:24Z sz3 $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');

    if ($_POST['cache_clear']){
      $pdc->flush_cache();
    }else if ($_POST['cache_cleanup']){
      $pdc->cleanup();
    }
    
    //get cache data
    $sql = "SELECT entity, LENGTH(data) AS size, ttl, date FROM __data_cache";
    $result = $db->query($sql);
    $total = 0;
    
    //build table
    $cache_table = '<tr><th>'.$user->lang['pdc_entity'].'</th><th>'.$user->lang['pdc_size'].'</th><th>'.$user->lang['pdc_status'].'</th></tr>';
    $ctime = time();
    while ($row = $db->fetch_record($result)){
      $rc = $eqdkp->switch_row_class();
      $size = round($row['size']/1024, 1);
      $expiry_date = (($row['date']+$row['ttl'])<$ctime)?'<span class="negative">'.$user->lang['pdc_entity_expired'].'</span>':'<span class="positive">'.$user->lang['pdc_entity_valid'].'</span>';
      $total += $size;
      $cache_table .= '<tr class="'.$rc.'" onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$rc.'\';">';
      $cache_table .= '<td>'.$row['entity'].'</td>';
      $cache_table .= '<td>'.$size.'kb</td>';
      $cache_table .= '<td>'.$expiry_date.'</td></tr>';
    }
    $rc = $eqdkp->switch_row_class();
    $cache_table .= '<tr>';
    $cache_table .= '<th>'.$user->lang['pdc_size_total'].'</th><th>'.$total.'kb</th><th>&nbsp;</th></tr>';
    
    $tpl->assign_vars(array (
    	'F_CONFIG' => 'cache.php' . $SID,
      'CACHE_TABLE' => $cache_table,
      'L_CLEANUP_CACHE' => $user->lang['pdc_cleanup'],
      'L_CLEAR_CACHE' => $user->lang['pdc_clear'],
    ));
    
    
    $eqdkp->set_vars(array (
    	'page_title' => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['pdc_manager'],
    	'template_file' => 'admin/manage_cache.html',
      'display' => true
    	)
    );

?>
