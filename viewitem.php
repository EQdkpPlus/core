<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewitem.php
 * Began: Fri December 20 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if ($conf_plus['pk_itemstats'] == 1){
	include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
}

$user->check_auth('u_item_view');

if ( (isset($_GET[URI_ITEM])) && (intval($_GET[URI_ITEM] > 0)) )
{
    $sort_order = array(
        0 => array('i.item_date desc', 'i.item_date'),
        1 => array('i.item_buyer', 'i.item_buyer desc'),
        2 => array('i.item_value desc', 'i.item_value')
    );

    $current_order = switch_order($sort_order);

    // We want to view items by name and not id, so get the name
    $item_name = $db->query_first('SELECT item_name FROM ' . ITEMS_TABLE . " WHERE item_id='".$_GET[URI_ITEM]."'");

    if ( empty($item_name) )
    {
        message_die($user->lang['error_invalid_item_provided']);
    }

    // Item stats
    if ( $pm->check(PLUGIN_INSTALLED, 'stats') )
    {
        $show_stats = true;

        //  $sql = 'SELECT item_id
       // FROM ' . ITEM_STATS_TABLE . "
       //     WHERE item_name='" . addslashes($item_name) . "'";
       // $stat_id = $db->query_first($sql);

        $sql = 'SELECT id
                FROM ' . ITEM_STATS_TABLE . "
                WHERE name='" . addslashes($item_name) . "'";
        $stat_id = $db->query_first($sql);

        if ( !$stat_id )
        {
            $show_stats = false;
            $u_view_stats = '';
        }
        else
        {
            $u_view_stats = $eqdkp_root_path . 'plugins/' . $pm->get_data('stats', 'path') . '/itemshot.php' . $SID . '&amp;' . URI_ITEM . '=' . $stat_id . '&amp;iframe=true';
        }
    }
    else
    {
        $show_stats = false;
        $u_view_stats = '';
    }

    $sql = 'SELECT i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, i.item_buyer, r.raid_name
            FROM ' . ITEMS_TABLE . ' i, ' . RAIDS_TABLE . " r
            WHERE (r.raid_id = i.raid_id) AND (i.item_name='".addslashes($item_name)."')
            ORDER BY ".$current_order['sql'];
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
				$event_icon = "<img height='16' width='16'  src='images/wow_events/".$event_icon."'> " ;
			}
			else
			{
			$event_icon = "";
			}

        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS' => $eqdkp->switch_row_class(),
            'DATE' => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
            'BUYER' => ( !empty($item['item_buyer']) ) ? get_classNameImgViewmembers($item['item_buyer']) : '&nbsp;',
            'U_VIEW_BUYER' => 'viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'],
            'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$item['raid_id'],
            'RAID' => ( !empty($item['raid_name']) ) ? $event_icon.stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'VALUE' => runden($item['item_value']))
        );
    }

    $tpl->assign_vars(array(
        'S_STATS' => $show_stats,
				'ITEM_STATS' => ( $conf_plus['pk_itemstats'] == 1 )? itemstats_get_html($item_name) : '',
        'L_PURCHASE_HISTORY_FOR' => sprintf($user->lang['purchase_history_for'], stripslashes($item_name)),
        'L_DATE' => $user->lang['date'],
        'L_BUYER' => $user->lang['buyer'],
        'L_RAID' => $user->lang['raid'],
        'L_VALUE' => $user->lang['value'],

        'O_DATE' => $current_order['uri'][0],
        'O_BUYER' => $current_order['uri'][1],
        'O_VALUE' => $current_order['uri'][2],

        'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.$_GET[URI_ITEM].'&amp;',
        'U_VIEW_STATS' => $u_view_stats,

        'VIEWITEM_FOOTCOUNT' => sprintf($user->lang['viewitem_footcount'], $db->num_rows($items_result)))
    );

    $pm->do_hooks('/viewitem.php');

    $eqdkp->set_vars(array(
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['viewitem_title'], stripslashes($item_name)),
        'template_file' => 'viewitem.html',
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_item_provided']);
}
?>
