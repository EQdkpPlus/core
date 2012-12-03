<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listraids.php
 * Began: Tue December 24 2002
 * 
 * $Id: listraids.php 4 2006-05-08 17:01:47Z tsigo $
 * 
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_raid_');

$sort_order = array(
    0 => array('raid_date desc', 'raid_date'),
    1 => array('raid_name', 'raid_name desc'),
    2 => array('raid_note', 'raid_note desc'),
    3 => array('raid_value desc', 'raid_value')
);
 
$current_order = switch_order($sort_order);

$total_raids = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE);

$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value 
        FROM ' . RAIDS_TABLE . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_rlimit'];
        
if ( !($raids_result = $db->query($sql)) )
{
    message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
}
while ( $row = $db->fetch_record($raids_result) )
{
    $tpl->assign_block_vars('raids_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
        'U_VIEW_RAID' => 'addraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
        'NAME' => stripslashes($row['raid_name']),
        'NOTE' => ( !empty($row['raid_note']) ) ? stripslashes($row['raid_note']) : '&nbsp;',
        'VALUE' => $row['raid_value'])
    );
}

$tpl->assign_vars(array(
    'L_DATE' => $user->lang['date'],
    'L_NAME' => $user->lang['name'],
    'L_NOTE' => $user->lang['note'],
    'L_VALUE' => $user->lang['value'],
    
    'O_DATE' => $current_order['uri'][0],
    'O_NAME' => $current_order['uri'][1],
    'O_NOTE' => $current_order['uri'][2],
    'O_VALUE' => $current_order['uri'][3],
    
    'U_LIST_RAIDS' => 'listraids.php'.$SID.'&amp;',
    
    'START' => $start,
    'LISTRAIDS_FOOTCOUNT' => sprintf($user->lang['listraids_footcount'], $total_raids, $user->data['user_rlimit']),
    'RAID_PAGINATION' => generate_pagination('listraids.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_raids, $user->data['user_rlimit'], $start))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listraids_title'],
    'template_file' => 'listraids.html',
    'display'       => true)
);
?>