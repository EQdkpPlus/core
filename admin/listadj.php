<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listadj.php
 * Began: Fri December 27 2002
 * 
 * $Id: listadj.php 4 2006-05-08 17:01:47Z tsigo $
 * 
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$sort_order = array(
    0 => array('adjustment_date desc', 'adjustment_date'),
    1 => array('member_name', 'member_name desc'),
    2 => array('adjustment_reason', 'adjustment_reason desc'),
    3 => array('adjustment_value desc', 'adjustment_value'),
    4 => array('adjustment_added_by', 'adjustment_added_by desc'),
    5 => array('raid_name', 'raid_name desc')
);

$current_order = switch_order($sort_order);

//
// Group Adjustments
//
if ( (!isset($_GET[URI_PAGE])) || ($_GET[URI_PAGE] == 'group') )
{
    $user->check_auth('a_groupadj_');
    
    $u_list_adjustments = 'listadj.php'.$SID.'&amp;';
    
    $page_title = sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listadj_title'];
    
    $total_adjustments = $db->query_first('SELECT count(*) FROM ' . ADJUSTMENTS_TABLE . ' WHERE member_name IS NULL');
    $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
    
    $s_group_adj = true;
    
    $sql = 'SELECT adjustment_id, adjustment_value, adjustment_date, adjustment_added_by, raid_name
            FROM ' . ADJUSTMENTS_TABLE . '
            WHERE member_name IS NULL
            ORDER BY '.$current_order['sql'].'
            LIMIT '.$start.','.$user->data['user_alimit'];
    
    $listadj_footcount = sprintf($user->lang['listadj_footcount'], $total_adjustments, $user->data['user_alimit']);
    $pagination = generate_pagination('listadj.php'.$SID.'&amp;o='.$current_order['uri']['current'],
                                       $total_adjustments, $user->data['user_alimit'], $start);
}

//
// Individual Adjustments
//
elseif ( $_GET[URI_PAGE] == 'individual' )
{
    $user->check_auth('a_indivadj_');
    
    $u_list_adjustments = 'listadj.php'.$SID.'&amp;' . URI_PAGE . '=individual&amp;';
    
    $page_title = sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listiadj_title'];
    
    $total_adjustments = $db->query_first('SELECT count(*) FROM ' . ADJUSTMENTS_TABLE . ' WHERE member_name IS NOT NULL');
    $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
    
    $s_group_adj = false;
    
    $sql = 'SELECT adjustment_id, adjustment_value, member_name, adjustment_reason, adjustment_date, adjustment_added_by, raid_name
            FROM ' . ADJUSTMENTS_TABLE . '
            WHERE member_name IS NOT NULL
            ORDER BY '.$current_order['sql'].'
            LIMIT '.$start.','.$user->data['user_alimit'];
    
    $listadj_footcount = sprintf($user->lang['listiadj_footcount'], $total_adjustments, $user->data['user_alimit']);
    $pagination = generate_pagination('listadj.php'.$SID.'&amp;' . URI_PAGE . '=individual&amp;o='.$current_order['uri']['current'],
                                       $total_adjustments, $user->data['user_alimit'], $start);
}

if ( !($adj_result = $db->query($sql)) )
{
    message_die('Could not obtain adjustment information', '', __FILE__, __LINE__, $sql);
}

while ( $adj = $db->fetch_record($adj_result) )
{
    $tpl->assign_block_vars('adjustments_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'U_ADD_ADJUSTMENT' => (( $s_group_adj ) ? 'addadj.php' : 'addiadj.php') . $SID.'&amp;' . URI_ADJUSTMENT . '='.$adj['adjustment_id'],
        'DATE' => date($user->style['date_notime_short'], $adj['adjustment_date']),
        'U_VIEW_MEMBER' => ( isset($adj['member_name']) ) ? $eqdkp_root_path.'viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$adj['member_name'] : '',
        'MEMBER' => ( isset($adj['member_name']) ) ? $adj['member_name'] : '',
        'REASON' => ( isset($adj['adjustment_reason']) ) ? stripslashes($adj['adjustment_reason'])  : '',
        'ADJUSTMENT' => $adj['adjustment_value'],
        'C_ADJUSTMENT' => color_item($adj['adjustment_value']),
        'RAID_NAME' => $adj['raid_name'],
        'ADDED_BY' => ( isset($adj['adjustment_added_by']) ) ? $adj['adjustment_added_by'] : '')
    );
}
$db->free_result($adj_result);

$tpl->assign_vars(array(
    'L_DATE' => $user->lang['date'],
    'L_MEMBER' => $user->lang['member'],
    'L_REASON' => $user->lang['reason'],
    'L_ADJUSTMENT' => $user->lang['adjustment'],
    'L_ADDED_BY' => $user->lang['added_by'],
    'L_MULTI_EVENTNAME' => $user->lang['Multi_eventname'],
    
    
    
    'O_DATE' => $current_order['uri'][0],
    'O_MEMBER' => $current_order['uri'][1],
    'O_RAID' => $current_order['uri'][5],
    'O_REASON' => $current_order['uri'][2],
    'O_ADJUSTMENT' => $current_order['uri'][3],
    'O_ADDED_BY' => $current_order['uri'][4],
    
    'U_LIST_ADJUSTMENTS' => $u_list_adjustments,
    
    'START' => $start,
    'S_GROUP_ADJ' => $s_group_adj,
    'LISTADJ_FOOTCOUNT' => $listadj_footcount,
    'ADJUSTMENT_PAGINATION' => $pagination)
);

$eqdkp->set_vars(array(
    'page_title'    => $page_title,
    'template_file' => 'admin/listadj.html',
    'display'       => true)
);
?>