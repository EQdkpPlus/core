<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * mm_listmembers.php
 * Began: Thu January 30 2003
 *
 * $Id$
 *
 ******************************/

// Shows a list of members, basically just an admin-themed version of
// /listmembers.php

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

global $conf_plus;

$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    1 => array('member_earned desc', 'member_earned'),
    2 => array('member_spent desc', 'member_spent'),
    3 => array('member_adjustment desc', 'member_adjustment'),
    4 => array('member_current desc', 'member_current'),
    5 => array('member_lastraid desc', 'member_lastraid'),
    6 => array('member_level desc', 'member_level'),
    7 => array('member_class_id', 'member_class_id desc'),
    8 => array('member_rank_id', 'member_rank_id desc'),
    9 => array('class_armor_type', 'class_armor_type desc')

);

$current_order = switch_order($sort_order);

$member_count = 0;
$previous_data = '';

// Figure out what data we're comparing from member to member
// in order to rank them
$sort_index = explode('.', $current_order['uri']['current']);
$previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);

$show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == 'all') ) ? true : false;

$sql = 'SELECT *, member_earned - member_spent + member_adjustment AS member_current
        FROM __members
        ORDER BY ' . $current_order['sql'];

if ( !($members_result = $db->query($sql)) )
{
	d($sql) ;
	message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);

}
while ( $row = $db->fetch_record($members_result) )
{
    $member_count++;

   $class_name = get_classNamebyMemberName($row['member_name']) ;
   $rank_name = get_RankNamebyMemberName($row['member_name']);
   $text_class = $class_name;

   // class img
   $mclass = renameClasstoenglish($class_name) ;


    $tpl->assign_block_vars('members_row', array(
        'ROW_CLASS'     => $eqdkp->switch_row_class(),
        'ID'            => $row['member_id'],
        'COUNT'         => ($row[$previous_source] == $previous_data) ? '&nbsp;' : $member_count,
        'NAME'          => $row['member_name'] ,
        'RANK'          => stripslashes($rank_name),
        'LEVEL'         => ( $row['member_level'] > 0 ) ? $row['member_level'] : '&nbsp;',
        #'ARMOR'         => ( !empty($row['armor_type']) ) ? $row['armor_type'] : '&nbsp;',
        'CLASS'         => ( $class_name != 'NULL' ) ? $class_name : '&nbsp;',
        'CLASS_TEXT'    => ( $text_class != 'NULL' ) ? $text_class : '&nbsp;',
        'CLASSENG'		  => get_classColorChecked($class_name),
        'EARNED'        => $row['member_earned'],
        'SPENT'         => $row['member_spent'],
        'ADJUSTMENT'    => $row['member_adjustment'],
        'CURRENT'       => $row['member_current'],
        'LASTRAID'      => ( !empty($row['member_lastraid']) ) ? date($user->style['date_notime_short'], $row['member_lastraid']) : '&nbsp;',
        'C_ADJUSTMENT'  => color_item($row['member_adjustment']),
        'C_CURRENT'     => color_item($row['member_current']),
        'C_LASTRAID'    => 'neutral',
        'U_VIEW_MEMBER' => 'manage_members.php'.$SID . '&amp;mode=addmember&amp;' . URI_NAME . '='.$row['member_name'],
        'U_COMPARE_MEMBERS' => 'manage_members.php'.$SID.'&amp;mode=list&amp;')
    );

    // So that we can compare this member to the next member,
    // set the value of the previous data to the source
    $previous_data = $row[$previous_source];
}
$footcount_text = sprintf($user->lang['listmembers_footcount'], $db->num_rows($members_result));


$tpl->assign_vars(array(
    'F_MEMBERS' => 'manage_members.php' . $SID . '&amp;mode=addmember',

    'L_NAME' => $user->lang['name'],
    'L_RANK' => $user->lang['rank'],
    'L_LEVEL' => $user->lang['level'],
    'L_CLASS' => $user->lang['class'],
    'L_EARNED' => $user->lang['earned'],
    'L_SPENT' => $user->lang['spent'],
    'L_ARMOR'         => $user->lang['armor'],
    'L_ADJUSTMENT' => $user->lang['adjustment'],
    'L_CURRENT' => $user->lang['current'],
    'L_LASTRAID' => $user->lang['lastraid'],
    'BUTTON_NAME' => 'delete',
    'BUTTON_VALUE' => $user->lang['delete_selected_members'],

    'O_NAME' => $current_order['uri'][0],
    'O_RANK' => $current_order['uri'][8],
    'O_LEVEL' => $current_order['uri'][6],
    'O_CLASS' => $current_order['uri'][7],
    'O_ARMOR'      => $current_order['uri'][9],
    'O_EARNED' => $current_order['uri'][1],
    'O_SPENT' => $current_order['uri'][2],
    'O_ADJUSTMENT' => $current_order['uri'][3],
    'O_CURRENT' => $current_order['uri'][4],
    'O_LASTRAID' => $current_order['uri'][5],

    'U_LIST_MEMBERS' => 'manage_members.php'.$SID.'&amp;mode=list&amp;',
    'U_COMPARE_MEMBERS' => 'manage_members.php'.$SID.'&amp;mode=list&amp;',

    'S_COMPARE' => false,
    'S_NOTMM' => false,
    'S_FROM_EDIT_MEMBER' => true,

    'SHOW_CLASS' => true,
    'SHOW_RANK'	 => true,
    'SHOW_LEVEL' => true,

		'SHOW_LASTRAID'		=> ( $conf_plus['pk_lastraid'] == 1 )? true : false,

    'COLSPAN' => 15,

    'LISTMEMBERS_FOOTCOUNT' => $footcount_text)
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listmembers_title'],
    'template_file' => 'listmembers.html',
    'display'       => true)
);
?>
