<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_item_view');

if ($item_id = $in->get('i', 0))
{

    // We want to view items by name and not id, so get the name
    $item_name = $pdh->get('item', 'name', array($item_id));

    if ( empty($item_name) )
    {
        message_die($user->lang['error_invalid_item_provided']);
    }

    $sort_order = array(
        0 => array('i.item_date desc', 'i.item_date'),
        1 => array('i.item_buyer', 'i.item_buyer desc'),
        2 => array('i.item_value desc', 'i.item_value')
    );

    $current_order = switch_order($sort_order);

    #search for the gameid
  	$game_id = $pdh->get('item', 'game_itemid', array($item_id));

    $item_ids = array();
  	if ($game_id > 1){
  	  $item_ids = $pdh->get('item', 'ids_by_ingameid', array($game_id));
  	}else{
  	  $item_ids = $pdh->get('item', 'ids_by_name', array($item_name));
  	}

    $a_items = array();
    $counter = sizeof($item_ids);

    foreach($item_ids as $inner_item_id){
      $event_name = stripslashes($pdh->get('raid', 'event_name', array($pdh->get('item', 'raid_id', array($inner_item_id)))));
  		$event_icon = $game->decorate('events', array($pdh->get('event', 'icon', array($pdh->get('raid', 'event', array($pdh->get('item', 'raid_id', array($inner_item_id))))))));
  		$item_value = runden($pdh->get('item', 'value', array($inner_item_id)));
  		$a_items[] = array('name' => date('Y-m-d', $pdh->get('item', 'date', array($inner_item_id))), 'value' => $item_value);

      $tpl->assign_block_vars('items_row', array(
          'ROW_CLASS' => $core->switch_row_class(),
          'DATE' => ( $pdh->get('item', 'date', array($inner_item_id)) != '' ) ? date($user->style['date_notime_short'], $pdh->get('item', 'date', array($inner_item_id))) : '&nbsp;',
          'BUYER' => ( $pdh->get('member', 'name', array($pdh->get('item', 'buyer', array($inner_item_id)))) != '' ) ? $pdh->get('member', 'name', array($pdh->get('item', 'buyer', array($inner_item_id)))) : '&nbsp;',
          'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;r='.$pdh->get('item', 'raid_id', array($inner_item_id)),
          'RAID' => ( !empty($event_name) ) ? $event_icon.stripslashes($event_name) : '&lt;<i>Not Found</i>&gt;',
          'VALUE' => $item_value
          )
      );
    }

    //default now col
    $colspan = ($core->config['infotooltip_use']) ? 1 : 0 ;

    #Itemhistory Diagram
  	if ($core->config['pk_itemhistory_dia'])
  	{
  		$colspan++;
  	}

  	//3d Model
  	if( (strtolower($core->config['default_game']) == 'wow') and
  		(version_compare(phpversion(), "5.0.0", ">=") ) and
  		($game_id>1) and
  		($core->config['pk_enable_3ditem']==1))
  	{


  		$obj = $game->new_object('wow_modelviewer', 'wmv');
  		$model3d = $game->obj[$obj]->wow_itemviewer($game_id);

  		$tpl->assign_vars(array('SHOW_MODELVIEWER'		=>  true ));
  		$colspan++;
  	}

  	//Comments
  	$comm_settings = array('attach_id'=>md5(stripslashes($item_name)), 'page'=>'items');
  	$pcomments->SetVars($comm_settings);
  	$COMMENT = ($core->config['pk_enable_comments'] == 1) ? $pcomments->Show() : '';
	
	//init infotooltip
	infotooltip_js();

    $tpl->assign_vars(array(
  		'ITEM_STATS' => $pdh->get('item', 'itt_itemname', array($item_id, 0, 1)),
  		'ITEM_CHART' => $jquery->LineChart('item_chart', $a_items, '', 200, 500, '', false, true, 'date'),
  		'ITEM_MODEL' => $model3d,
  		'COMMENT' 	 => $COMMENT ,

  		'SHOW_ITEMSTATS'		=> ($core->config['infotooltip_use']) ? true : false,
  		'SHOW_ITEMHISTORYA'		=> ($core->config['pk_itemhistory_dia'] == 1 ) ? true : false,
  		'SHOW_COLSPAN' 	 		=> $colspan ,


      'L_PURCHASE_HISTORY_FOR' => sprintf($user->lang['purchase_history_for'], stripslashes($item_name)),
      'L_DATE' => $user->lang['date'],
      'L_BUYER' => $user->lang['buyer'],
      'L_RAID' => $user->lang['raid'],
      'L_VALUE' => $user->lang['value'],

      'O_DATE' => $current_order['uri'][0],
      'O_BUYER' => $current_order['uri'][1],
      'O_VALUE' => $current_order['uri'][2],

      'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;i='.$item_id.'&amp;',

      'VIEWITEM_FOOTCOUNT' => sprintf($user->lang['viewitem_footcount'], $counter))
    );

    $pm->do_hooks('/viewitem.php');
    $core->set_vars(array(
        'page_title'    => sprintf($user->lang['viewitem_title'], stripslashes($item_name)),
        'template_file' => 'viewitem.html',
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_item_provided']);
}
?>