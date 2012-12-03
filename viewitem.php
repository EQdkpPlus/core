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

    $u_view_stats = '';

    $item_id = $_GET[URI_ITEM];
    #search for the gameid
  	$sql = 'SELECT game_itemid
            FROM ' . ITEMS_TABLE . "
            WHERE item_id=".addslashes($item_id) ;

  	$game_id = $db->query_first($sql);

  	if ($game_id > 1)
  	{   	
    	$sql = 'SELECT i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, i.item_buyer, i.game_itemid, r.raid_name, c.class_name, c.class_id
				FROM (' . CLASS_TABLE . ' c RIGHT JOIN (' . MEMBERS_TABLE . ' m RIGHT JOIN ' . ITEMS_TABLE . ' i ON m.member_name = i.item_buyer) 
				ON c.class_id = m.member_class_id) INNER JOIN ' . RAIDS_TABLE .' r ON i.raid_id = r.raid_id
	            WHERE (r.raid_id = i.raid_id) AND (i.game_itemid='.addslashes($game_id).')
    	        ORDER BY '.$current_order['sql']
    			;
  	}else 
  	{
    	$sql = 'SELECT i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, i.item_buyer, i.game_itemid, r.raid_name, c.class_name, c.class_id
				FROM (' . CLASS_TABLE . ' c RIGHT JOIN (' . MEMBERS_TABLE . ' m RIGHT JOIN ' . ITEMS_TABLE . ' i ON m.member_name = i.item_buyer) 
				ON c.class_id = m.member_class_id) INNER JOIN ' . RAIDS_TABLE ." r ON i.raid_id = r.raid_id
	            WHERE (r.raid_id = i.raid_id) AND (i.item_name='".addslashes($item_name)."')
    	        ORDER BY ".$current_order['sql'] ;
  	}

    if ( !($items_result = $db->query($sql)) )
    {
        message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql);
    }
    $a_items = array();
    $counter = 0 ;
    while ( $item = $db->fetch_record($items_result) )
    {
    	$counter++;
		$event_icon = getEventIcon($item['raid_name']);

		$a_items[] = runden($item['item_value']) ;
        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS' => $eqdkp->switch_row_class(),
            'DATE' => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
            'BUYER' => ( !empty($item['item_buyer']) ) ? get_classNameImgViewmembers($item['item_buyer'],$item['class_name'],$item['class_id']) : '&nbsp;',
            'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$item['raid_id'],
            'RAID' => ( !empty($item['raid_name']) ) ? $event_icon.stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'VALUE' => runden($item['item_value']))
        );
    }

    
    //default now col
    $colspan = ($conf_plus['pk_itemstats']==1) ? 1 : 0 ;
    
    #Itemhistory Diagram
	if (!$conf_plus['pk_itemhistory_dia'])
	{
		require_once($eqdkp_root_path . 'pluskernel/include/GoogleGraph.class.php');
		$colspan++;
	}

  
	//3d Model
	if( (strtolower($eqdkp->config['default_game']) == 'wow') and (version_compare(phpversion(), "5.0.0", ">=") ) and ($game_id>1) and (!$conf_plus['pk_disable_3ditem']==1))	 
	{
		include $eqdkp_root_path.'/pluskernel/include/wow_modelviewer.class.php';	
		$wow_modelviewer = new wow_modelviewer();	
		$model3d = $wow_modelviewer->wow_itemviewer($game_id);
		
		$tpl->assign_vars(array('SHOW_MODELVIEWER'		=>  true ));
		$colspan++;
	}
	
	//Comments
	$comm_settings = array('attach_id'=>md5(stripslashes($item_name)), 'page'=>'items');
	$pcomments->SetVars($comm_settings);
	$COMMENT = ($conf_plus['pk_disable_comments'] == 1) ? '' : $pcomments->Show() ;
	

    $tpl->assign_vars(array(
		'ITEM_STATS' => $html->itemstats_itemHtml($item_name,$game_id) ,
		'ITEM_CHART' => $html->createGraph($a_items),
		'ITEM_MODEL' => $model3d,
		'COMMENT' 	 => $COMMENT ,
		
		'SHOW_ITEMSTATS'		=> ( !$conf_plus['pk_itemstats'] == 0 )? true : false,
		'SHOW_ITEMHISTORYA'		=> ( !$conf_plus['pk_itemhistory_dia'] == 1 )? true : false,
		'SHOW_COLSPAN' 	 		=> $colspan ,
		

        'L_PURCHASE_HISTORY_FOR' => sprintf($user->lang['purchase_history_for'], stripslashes($item_name)),
        'L_DATE' => $user->lang['date'],
        'L_BUYER' => $user->lang['buyer'],
        'L_RAID' => $user->lang['raid'],
        'L_VALUE' => $user->lang['value'],

        'O_DATE' => $current_order['uri'][0],
        'O_BUYER' => $current_order['uri'][1],
        'O_VALUE' => $current_order['uri'][2],

        'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.$_GET[URI_ITEM].'&amp;',

        'VIEWITEM_FOOTCOUNT' => sprintf($user->lang['viewitem_footcount'], $counter))
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
