<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listitems.php
 * Began: Fri December 27 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');

$user->check_auth('a_item_');

$sort_order = array(
    0 => array('item_date desc', 'item_date'),
    1 => array('item_buyer', 'item_buyer desc'),
    2 => array('item_name', 'item_name desc'),
    3 => array('raid_name', 'raid_name desc'),
    4 => array('item_value desc', 'item_value')
);

$current_order = switch_order($sort_order);

$total_items = $db->query_first('SELECT count(*) FROM ' . ITEMS_TABLE);
$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT i.item_id, i.item_name, i.item_buyer, i.item_date, i.raid_id, i.item_value, r.raid_name
        FROM ' . ITEMS_TABLE . ' i, ' . RAIDS_TABLE . ' r
        WHERE r.raid_id=i.raid_id
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_ilimit'];

$listitems_footcount = sprintf($user->lang['listpurchased_footcount'], $total_items, $user->data['user_ilimit']);
$pagination = generate_pagination('listitems.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_items, $user->data['user_ilimit'], $start);

if ( !($items_result = $db->query($sql)) )
{
    message_die('Could not obtain item information', 'Database error', __FILE__, __LINE__, $sql);
}

while ( $item = $db->fetch_record($items_result) )
{
    $tpl->assign_block_vars('items_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
        'BUYER' => ( !empty($item['item_buyer']) ) ? $item['item_buyer'] : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_BUYER' => ( !empty($item['item_buyer']) ) ? '../viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'] : '',
        'NAME' => $html->itemstats_item(stripslashes($item['item_name'])),
        'U_VIEW_ITEM' => 'additem.php'.$SID.'&amp;' . URI_ITEM . '='.$item['item_id'],
        'RAID' => ( !empty($item['raid_name']) ) ? stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_RAID' => ( !empty($item['raid_name']) ) ? 'addraid.php'.$SID.'&amp;' . URI_RAID . '='.$item['raid_id'] : '',
        'VALUE' => $item['item_value'])
    );
}
$db->free_result($items_result);

$tpl->assign_vars(array(
    'L_DATE' => $user->lang['date'],
    'L_BUYER' => $user->lang['buyer'],
    'L_ITEM' => $user->lang['item'],
    'L_RAID' => $user->lang['raid'],
    'L_VALUE' => $user->lang['value'],

    'O_DATE' => $current_order['uri'][0],
    'O_BUYER' => $current_order['uri'][1],
    'O_NAME' => $current_order['uri'][2],
    'O_RAID' => $current_order['uri'][3],
    'O_VALUE' => $current_order['uri'][4],

    'U_LIST_ITEMS' => 'listitems.php'.$SID.'&amp;',

    'START' => $start,
    'S_HISTORY' => true,
    'LISTITEMS_FOOTCOUNT' => $listitems_footcount,
    'ITEM_PAGINATION' => $pagination)
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listpurchased_title'],
    'template_file' => 'listitems.html',
    'display'       => true)
);
?>