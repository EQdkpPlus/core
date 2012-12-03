<?php
/******************************
 * itemsearch.php
 * A rewrite of listitems.php to include a search functionality
 * Began: Mon July 3 2006
 *
 * itemsearch.php, v 1.0 July 21 2006
 * Zeaky, Horde, Azgalor.
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
$user->check_auth('u_item_list');

// Begin

    $sort_order = array(
        0 => array('item_date desc', 'item_date'),
        1 => array('item_buyer', 'item_buyer desc'),
        2 => array('item_name', 'item_name desc'),
        3 => array('raid_name', 'raid_name desc'),
        4 => array('item_value desc', 'item_value')
    );

    $current_order = switch_order($sort_order);
    
    $mySearch = $in->get('search');
    
    $u_list_items = 'itemsearch.php'.$SID.'&amp;search=' . $mySearch . '&amp;search_type=' . $in->get('search_type') . '&amp;unique_result=' . $in->get('unique_result') . '&amp;';

    $page_title = sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Item Search';

	$start = $in->get('start', 0);


if (isset($mySearch))
{
	if ($_GET['search_type'] == 'itemname')
		{
		$searchString = " AND i.item_name LIKE '%" . $db->sql_escape($mySearch) . "%' ";
		$search_type = 'item_name';
		$search_table = ITEMS_TABLE;
	}elseif ($_GET['search_type'] == 'buyer'){
		$searchString = " AND i.item_buyer LIKE '%" . $db->sql_escape($mySearch) . "%' ";
		$search_type = "item_buyer";
		$search_table = ITEMS_TABLE;
	}elseif ($_GET['search_type'] == 'raidname'){
		$searchString = " AND r.raid_name LIKE '%" . $db->sql_escape($mySearch) . "%' ";
		$search_type = ITEMS_TABLE . '.raid_id = ' . RAIDS_TABLE . '.raid_id AND raid_name';
		$search_table = ITEMS_TABLE . ', ' .RAIDS_TABLE;
	}
	$search_set = true;
	if ($_GET['unique_result'] == 'yes')
		$group_unique = 'GROUP BY item_name desc';
	else
		$group_unique = '';
}
    else
	{
        $searchString = '';
		$search_set = false;
		$group_unique = '';
	}

	$total_items = $db->query_first('SELECT count(*) FROM ' . $search_table . ' WHERE ' . $search_type . " LIKE '%" . $db->sql_escape($mySearch) . "%' ");// . $group_unique);

    $sql = 'SELECT i.item_id, i.item_name, i.item_buyer, i.item_date, i.raid_id, i.item_value, r.raid_name
            FROM ' . ITEMS_TABLE . ' i, ' . RAIDS_TABLE . ' r
            WHERE i.raid_id = r.raid_id ' . $searchString . $group_unique . '
            ORDER BY '.$current_order['sql']. '
            LIMIT '.$start.','.$user->data['user_ilimit'];


    $listitems_footcount = sprintf($user->lang['listpurchased_footcount'], $total_items, $user->data['user_ilimit']);
    $pagination = generate_pagination('itemsearch.php'.$SID.'&amp;search=' . $mySearch . '&amp;search_type=' . $in->get('search_type') . '&amp;unique_result=' . $in->get('unique_result') . '&amp;o='.$current_order['uri']['current'],
                                       $total_items, $user->data['user_ilimit'], $start);


if ( !($items_result = $db->query($sql)) )
{
    message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql);
}

while ( $item = $db->fetch_record($items_result) )
{
    $tpl->assign_block_vars('items_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
        'BUYER' => ( !empty($item['item_buyer']) ) ? $item['item_buyer'] : '&lt;<i>Not Found</i>&gt;',
        'U_VIEW_BUYER' => 'viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'],
        'NAME' => $html->itemstats_item(stripslashes($item['item_name'])),
        'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.$item['item_id'],
        'RAID' => ( !empty($item['raid_name']) ) ? stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
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
	'SEARCH_SET' => $search_set,
    'LISTITEMS_FOOTCOUNT' => $listitems_footcount,
    'ITEM_PAGINATION' => $pagination)
);

$eqdkp->set_vars(array(
    'page_title'    => $page_title,
    'template_file' => 'itemsearch.html',
    'display'       => true)
);
?>