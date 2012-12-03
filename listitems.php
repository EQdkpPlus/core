<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listitems.php
 * Began: Sat December 21 2002
 *
 * $Id: listitems.php 6 2006-05-08 17:11:35Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if ($conf_plus['pk_itemstats'] == 1){
	include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
}

$user->check_auth('u_item_list');

//
// Item Values (unique items)
//

if ( (!isset($_GET[URI_PAGE])) || ($_GET[URI_PAGE] == 'values') )
{
     $sort_order = array(
	0 => array('item_date desc', 'item_date'),
	1 => array('item_buyer', 'item_buyer desc'),
	2 => array('item_name', 'item_name desc'),
	3 => array('raid_name', 'raid_name desc'),
	4 => array('item_value desc', 'item_value')
     );

    $current_order = switch_order($sort_order);

    $u_list_items = 'listitems.php'.$SID.'&amp;';

    $page_title = sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listitems_title'];

    $total_items = $db->num_rows($db->query('SELECT item_id FROM ' . ITEMS_TABLE . ' GROUP BY item_name'));
    $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

    // We don't care about history; ignore making the items unique
    $s_history = false;

    $sql = 'SELECT i.item_id, i.item_name, i.item_buyer, i.item_date, i.raid_id, min(i.item_value) AS item_value, r.raid_name
            FROM ' . ITEMS_TABLE . ' i, ' . RAIDS_TABLE . ' r
	    WHERE i.raid_id = r.raid_id
	    GROUP BY item_name
            ORDER BY '.$current_order['sql']. '
            LIMIT '.$start.','.$user->data['user_ilimit'];

    $listitems_footcount = sprintf($user->lang['listitems_footcount'], $total_items, $user->data['user_ilimit']);
    $pagination = generate_pagination('listitems.php'.$SID.'&amp;o='.$current_order['uri']['current'],
                                       $total_items, $user->data['user_ilimit'], $start);
}


//
// Item Purchase History (all items)
//
elseif ( $_GET[URI_PAGE] == 'history' )
{
    $sort_order = array(
        0 => array('item_date desc', 'item_date'),
        1 => array('item_buyer', 'item_buyer desc'),
        2 => array('item_name', 'item_name desc'),
        3 => array('raid_name', 'raid_name desc'),
        4 => array('item_value desc', 'item_value')
    );

    $current_order = switch_order($sort_order);

    $u_list_items = 'listitems.php'.$SID.'&amp;' . URI_PAGE . '=history&amp;';

    $page_title = sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listpurchased_title'];

    $total_items = $db->query_first('SELECT count(*) FROM ' . ITEMS_TABLE);
    $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

    $s_history = true;

    $sql = 'SELECT i.item_id, i.item_name, i.item_buyer, i.item_date, i.raid_id, i.item_value, r.raid_name
            FROM ' . ITEMS_TABLE . ' i, ' . RAIDS_TABLE . ' r
            WHERE r.raid_id=i.raid_id
            ORDER BY '.$current_order['sql']. '
            LIMIT '.$start.','.$user->data['user_ilimit'];

    $listitems_footcount = sprintf($user->lang['listpurchased_footcount'], $total_items, $user->data['user_ilimit']);
    $pagination = generate_pagination('listitems.php'.$SID.'&amp;' . URI_PAGE . '=history&amp;o='.$current_order['uri']['current'],
                                       $total_items, $user->data['user_ilimit'], $start);
}

// Regardless of which listitem page they're on, we're essentially
// outputting the same stuff. Purchase History just has a buyer column.
if ( !($items_result = $db->query($sql)) )
{
    message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql);
}

while ( $item = $db->fetch_record($items_result) )
{
	    			$html = new htmlPlus(); // plus html class for tooltip
					$event_icon = $html->getEventIcon($item['raid_name']);
					
					if(strlen($event_icon) > 0)
					{
						$event_icon = "<img height='20' width='20'  src='images/events/".$event_icon."'> " ;
					} 
					else
					{
					$event_icon = "";
					}
					  
    $tpl->assign_block_vars('items_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
        'BUYER' => ( !empty($item['item_buyer']) ) ? getClassImg(get_classNamebyMemberName($item['item_buyer'])).get_coloredLinkedName($item['item_buyer']) : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_BUYER' => 'viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'],
        'NAME' => ( $conf_plus['pk_itemstats'] == 1 ) ? itemstats_decorate_name(stripslashes($item['item_name'])) : stripslashes($item['item_name']),
        'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.$item['item_id'],
        'RAID' => ( !empty($item['raid_name']) ) ? $event_icon.stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$item['raid_id'],
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

	'SEARCH' => $user->lang['Itemsearch_search'] ,
	'SEARCHBY' => $user->lang['Itemsearch_searchby'] ,
	'ITEMM' => $user->lang['Itemsearch_item'] ,
	'BUYERR' => $user->lang['Itemsearch_buyer'] ,
	'RAIDD' => $user->lang['Itemsearch_raid'] ,
	'UNIQUE' => $user->lang['Itemsearch_unique'] ,
	'NO' => $user->lang['Itemsearch_no'] ,
	'YES' => $user->lang['Itemsearch_yes'] ,

    'O_DATE' => $current_order['uri'][0],
    'O_BUYER' => $current_order['uri'][1],
    'O_NAME' => $current_order['uri'][2],
    'O_RAID' => $current_order['uri'][3],
    'O_VALUE' => $current_order['uri'][4],

    'U_LIST_ITEMS' => $u_list_items,

    'START' => $start,
    'S_HISTORY' => $s_history,
    'LISTITEMS_FOOTCOUNT' => $listitems_footcount,
    'ITEM_PAGINATION' => $pagination)
);

$eqdkp->set_vars(array(
    'page_title'    => $page_title,
    'template_file' => 'listitems.html',
    'display'       => true)
);
?>
