<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listevents.php
 * Began: Fri December 27 2002
 * 
 * $Id: listevents.php 4 2006-05-08 17:01:47Z tsigo $
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_event_');

$sort_order = array(
    0 => array('event_name', 'event_name desc'),
    1 => array('event_value desc', 'event_value')
);
 
$current_order = switch_order($sort_order);

$total_events = $db->query_first('SELECT count(*) FROM ' . EVENTS_TABLE);

$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT event_id, event_name, event_value 
        FROM ' . EVENTS_TABLE . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_elimit'];
        
if ( !($events_result = $db->query($sql)) )
{
    message_die('Could not obtain event information', '', __FILE__, __LINE__, $sql);
}
while ( $event = $db->fetch_record($events_result) )
{
	
		$html = new htmlPlus(); // plus html class for tooltip
		$event_icon = $html->getEventIcon(stripslashes($event['event_name']));
		
		if(strlen($event_icon) > 0)
		{
			$event_icon = "<img height='16' width='16'  src='../images/events/".$event_icon."'> " ;
		} 
		else
		{
		$event_icon = "";
		}

	
    $tpl->assign_block_vars('events_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'U_VIEW_EVENT' => 'addevent.php'.$SID . '&amp;' . URI_EVENT . '='.$event['event_id'],
        'NAME' => $event_icon.stripslashes($event['event_name']),
        'VALUE' => $event['event_value'])
    );
}
$db->free_result($events_result);

$tpl->assign_vars(array(
    'L_NAME' => $user->lang['name'],
    'L_VALUE' => $user->lang['value'],
    'L_EVETNS' => $user->lang['menu_events'],
    
    'O_NAME' => $current_order['uri'][0],
    'O_VALUE' => $current_order['uri'][1],
    
    'U_LIST_EVENTS' => 'listevents.php'.$SID.'&amp;',
    
    'START' => $start,    
    'LISTEVENTS_FOOTCOUNT' => sprintf($user->lang['listevents_footcount'], $total_events, $user->data['user_elimit']),
    'EVENT_PAGINATION' => generate_pagination('listevents.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_events, $user->data['user_elimit'], $start))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listevents_title'],
    'template_file' => 'listevents.html',
    'display'       => true)
);
?>