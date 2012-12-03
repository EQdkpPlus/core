<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewmember.php
 * Began: Thu December 19 2002
 *
 * $Id: viewmember.php 4 2006-05-08 17:01:47Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if ($conf_plus['pk_itemstats'] == 1){
	include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
}

$user->check_auth('u_member_view');

if ( (isset($_GET[URI_NAME])) && (strval($_GET[URI_NAME] != '')) )
{
    $sort_order = array(
        0 => array('raid_name', 'raid_name desc'),
        1 => array('raid_count desc', 'raid_count')
    );

    $current_order = switch_order($sort_order);

$sql = 'SELECT member_id, member_name, member_earned, member_spent, member_adjustment, (member_earned-member_spent+member_adjustment) AS member_current,
              member_firstraid, member_lastraid, member_class_id, member_race_id
            FROM ' . MEMBERS_TABLE . "
            WHERE member_name='".$_GET[URI_NAME]."'";

    if ( !($member_result = $db->query($sql)) )
    {
        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
    }

    // Make sure they provided a valid member name
    if ( !$member = $db->fetch_record($member_result) )
    {
        message_die($user->lang['error_invalid_name_provided']);
    }

    // Find the percent of raids they've attended in the last 30, 60 and 90 days
    $percent_of_raids = array(
        '30'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-30, date('Y')), time(), $member['member_name']),
        '60'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-60, date('Y')), time(), $member['member_name']),
        '90'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-90, date('Y')), time(), $member['member_name']),
        'lifetime' => raid_count($member['member_firstraid'], $member['member_lastraid'], $member['member_name'])
    );


        //===========================
		// Find the race name

		$sql = 'SELECT race_name
				FROM ' . RACE_TABLE . '
				WHERE race_id = ' . $member['member_race_id'] ;

		$result = $db->query($sql);
		$race_name = $db->fetch_record($result) ;


	$db->free_result($result);
    //===========================


        //===========================
		// Find the class name

		$sql = 'SELECT class_name
				FROM ' . CLASS_TABLE . '
				WHERE class_id = ' . $member['member_class_id'] ;

		$result = $db->query($sql);
		$class_name = $db->fetch_record($result) ;


	$db->free_result($result);
    //===========================


    //
    // Raid Attendance
    //
    $rstart = ( isset($_GET['rstart']) ) ? $_GET['rstart'] : 0;

    // Find $current_earned based on the page.  This prevents us having to pass the
    // current earned as a GET variable which could result in user error
    if ( (!isset($_GET['rstart'])) || ($_GET['rstart'] == '0') )
    {
        $current_earned = $member['member_earned'];
    }
    else
    {
        $current_earned = $member['member_earned'];
        $sql = 'SELECT raid_value
                FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                WHERE (ra.raid_id = r.raid_id)
                AND (ra.member_name='" . $member['member_name']."')
                ORDER BY r.raid_date DESC
                LIMIT " . $rstart;
        if ( !($earned_result = $db->query($sql)) )
        {
            message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
        }
        while ( $ce_row = $db->fetch_record($earned_result) )
        {
            $current_earned -= $ce_row['raid_value'];
        }
        $db->free_result($earned_result);
    }

    $sql = 'SELECT r.raid_id, r.raid_name, r.raid_date, r.raid_note, r.raid_value
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name='" . $member['member_name'] . "')
            ORDER BY r.raid_date DESC
            LIMIT " . $rstart . ',' . $user->data['user_rlimit'];
    if ( !($raids_result = $db->query($sql)) )
    {
        message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
    }
    while ( $raid = $db->fetch_record($raids_result) )
    {
			$html = new htmlPlus(); // plus html class for tooltip
			$event_icon = $html->getEventIcon($raid['raid_name']);
			
			if(strlen($event_icon) > 0)
			{
				$event_icon = "<img height='16' width='16'  src='images/events/".$event_icon."'> " ;
			} 
			else
			{
				$event_icon = "";
			}
	
        $tpl->assign_block_vars('raids_row', array(
            'ROW_CLASS'      => $eqdkp->switch_row_class(),
            'DATE'           => ( !empty($raid['raid_date']) ) ? date($user->style['date_notime_short'], $raid['raid_date']) : '&nbsp;',
            'U_VIEW_RAID'    => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$raid['raid_id'],
            'NAME'           => ( !empty($raid['raid_name']) ) ? $event_icon.stripslashes($raid['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'NOTE'           => ( !empty($raid['raid_note']) ) ? stripslashes($raid['raid_note']) : '&nbsp;',
            'EARNED'         => $raid['raid_value'],
            'CURRENT_EARNED' => sprintf("%.2f", $current_earned))
        );
        $current_earned -= $raid['raid_value'];
    }

    $db->free_result($raids_result);

    $sql = 'SELECT count(*)
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name='" . addslashes($member['member_name']) . "')";
    $total_attended_raids = $db->query_first($sql);

    //
    // Item Purchase History
    //
    $istart = ( isset($_GET['istart']) ) ? $_GET['istart'] : 0;

    if ( (!isset($_GET['istart'])) || ($_GET['istart'] == '0') )
    {
        $current_spent = $member['member_spent'];
    }
    else
    {
        $current_spent = $member['member_spent'];
        $sql = 'SELECT item_value
                FROM ' . ITEMS_TABLE . "
                WHERE (item_buyer='" . $member['member_name'] . "')
                ORDER BY item_date DESC
                LIMIT " . $istart;
        if ( !($spent_result = $db->query($sql)) )
        {
            message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql);
        }
        while ( $cs_row = $db->fetch_record($spent_result) )
        {
            $current_spent -= $cs_row['item_value'];
        }
        $db->free_result($spent_result);
    }

    $sql = 'SELECT i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, r.raid_name
            FROM ( ' . ITEMS_TABLE . ' i
            LEFT JOIN ' . RAIDS_TABLE . " r
            ON r.raid_id = i.raid_id )
            WHERE (i.item_buyer='" . $member['member_name'] . "')
            ORDER BY i.item_date DESC
            LIMIT " . $istart . ',' . $user->data['user_ilimit'];
    if ( !($items_result = $db->query($sql)) )
    {
        message_die('Could not obtain item information', 'Database error', __FILE__, __LINE__, $sql);
    }
    while ( $item = $db->fetch_record($items_result) )
    {
    	
    			$html = new htmlPlus(); // plus html class for tooltip
					$event_icon = $html->getEventIcon($item['raid_name']);
					
					if(strlen($event_icon) > 0)
					{
						$event_icon = "<img height='16' width='16'  src='images/events/".$event_icon."'> " ;
					} 
					else
					{
					$event_icon = "";
					}
    	
        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS'     => $eqdkp->switch_row_class(),
            'DATE'          => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
            'U_VIEW_ITEM'   => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '=' . $item['item_id'],
            'U_VIEW_RAID'   => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '=' . $item['raid_id'],
            'NAME'          => ( $conf_plus['pk_itemstats'] == 1 ) ? itemstats_decorate_name(stripslashes($item['item_name'])) : stripslashes($item['item_name']),
            'RAID'          => ( !empty($item['raid_name']) ) ? $event_icon.stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'SPENT'         => $item['item_value'],
            'CURRENT_SPENT' => sprintf("%.2f", $current_spent))
        );
        $current_spent -= $item['item_value'];
    }
    $db->free_result($items_result);

    $total_purchased_items = $db->query_first('SELECT count(*) FROM ' . ITEMS_TABLE . " WHERE item_buyer='" . $member['member_name'] . "' ORDER BY item_date DESC");

    //
    // Individual Adjustment History
    //
    $sql = 'SELECT adjustment_value, adjustment_date, adjustment_reason, raid_name
            FROM ' . ADJUSTMENTS_TABLE . "
            WHERE member_name='" . $member['member_name'] . "'
            ORDER BY adjustment_date DESC";
    if ( !($adjustments_result = $db->query($sql)) )
    {
        message_die('Could not obtain adjustment information', '', __FILE__, __LINE__, $sql);
    }
    while ( $adjustment = $db->fetch_record($adjustments_result) )
    {
    			$html = new htmlPlus(); // plus html class for tooltip
					$event_icon = $html->getEventIcon($adjustment['raid_name']);
					
					if(strlen($event_icon) > 0)
					{
						$event_icon = "<img height='16' width='16'  src='images/events/".$event_icon."'> " ;
					} 
					else
					{
					$event_icon = "";
					}
					    	
        $tpl->assign_block_vars('adjustments_row', array(
            'ROW_CLASS'               => $eqdkp->switch_row_class(),
            'DATE'                    => ( !empty($adjustment['adjustment_date']) ) ? date($user->style['date_notime_short'], $adjustment['adjustment_date']) : '&nbsp;',
            'REASON'                  => ( !empty($adjustment['adjustment_reason']) ) ? stripslashes($adjustment['adjustment_reason']) : '&nbsp;',
            'RAIDNAME'                  => ( !empty($adjustment['raid_name']) ) ? $event_icon.stripslashes($adjustment['raid_name']) : '&nbsp;',
            'C_INDIVIDUAL_ADJUSTMENT' => color_item($adjustment['adjustment_value']),
            'INDIVIDUAL_ADJUSTMENT'   => $adjustment['adjustment_value'])
        );
    }

    //
    // Attendance by Event
    //
    $raid_counts = array();

    // Find the count for each for for this member
    $sql = 'SELECT e.event_id, r.raid_name, count(ra.raid_id) AS raid_count
            FROM ' . EVENTS_TABLE . ' e, ' . RAID_ATTENDEES_TABLE . ' ra, ' . RAIDS_TABLE . " r
            WHERE (e.event_name = r.raid_name)
            AND (r.raid_id = ra.raid_id)
            AND (ra.member_name = '" . $member['member_name'] . "')
            AND (r.raid_date >= " . $member['member_firstraid'] . ")
            GROUP BY ra.member_name, r.raid_name";
    $result = $db->query($sql);
    while ( $row = $db->fetch_record($result) )
    {
        // The count now becomes the percent
        $raid_counts[ $row['raid_name'] ] = $row['raid_count'];

        $event_ids[ $row['raid_name'] ] = $row['event_id'];
    }
    $db->free_result($result);

    // Find the count for reach raid
    $sql = 'SELECT raid_name, count(raid_id) AS raid_count
            FROM ' . RAIDS_TABLE . '
            WHERE raid_date >= ' . $member['member_firstraid'] . '
            GROUP BY raid_name';
    $result = $db->query($sql);
    while ( $row = $db->fetch_record($result) )
    {
        if ( isset($raid_counts[$row['raid_name']]) )
        {
            $percent = round(($raid_counts[ $row['raid_name'] ] / $row['raid_count']) * 100);
            $raid_counts[$row['raid_name']] = array('percent' => $percent, 'count' => $raid_counts[ $row['raid_name'] ]);

            unset($percent);
        }
    }
    $db->free_result($result);

    // Since we can't sort in SQL for this case, we have to sort
    // by the array
    switch ( $current_order['sql'] )
    {
        // Sort by key
        case 'raid_name':
            ksort($raid_counts);
            break;
        case 'raid_name desc':
            krsort($raid_counts);
            break;

        // Sort by value (keeping relational keys in-tact)
        case 'raid_count':
            asort($raid_counts);
            break;
        case 'raid_count desc':
            arsort($raid_counts);
            break;
    }
    reset($raid_counts);
    foreach ( $raid_counts as $event => $data )
    {
    			$html = new htmlPlus(); // plus html class for tooltip
					$event_icon = $html->getEventIcon($event);
					
					if(strlen($event_icon) > 0)
					{
						$event_icon = "<img height='12' width='12'  src='images/events/".$event_icon."'> " ;
					} 
					else
					{
					$event_icon = "";
					}
					    
					    	
        $tpl->assign_block_vars('event_row', array(
            'EVENT'        => $event_icon.stripslashes($event),
            'U_VIEW_EVENT' => 'viewevent.php' . $SID . '&' . URI_EVENT . '=' . $event_ids[$event],
            'BAR'          => create_bar($data['percent'] . '%', $data['count'] . ' (' . $data['percent'] . '%)'))
        );
    }
    unset($raid_counts, $event_ids);


	// 3D Model IMGs
	#######################

	$t1img = './images/3dmodel/'.$member['member_class_id'].$member['member_race_id'].'.gif' ;
	$t2img = './images/3dmodel/'.$member['member_class_id'].$member['member_race_id'].'m.gif' ;
	$t3img = './images/3dmodel/'.$member['member_class_id'].$member['member_race_id'].'f.gif' ;

	if(file_exists($t1img))
	{
		$class_race = '<img src='.$t1img.'> &nbsp;&nbsp;' ;
	}

	if(file_exists($t2img))
	{
		$class_race .= '<img src='.$t2img.'> &nbsp;&nbsp;' ;
	}

	if(file_exists($t3img))
	{
		$class_race .= '<img src='.$t3img.'>' ;
	}

	#######################
	# MultiDKP:
	
	if($conf_plus['pk_multidkp'] == 1)
	{  
		$html = new htmlPlus(); // plus html class 
		$member_multidkp = $html-> multiDkpMemberArray($member['member_name']) ; // create the multiDKP Table 
						
		if(!empty($member_multidkp[$member['member_name']]))
		{
			 foreach ($member_multidkp[$member['member_name']] as $key) 
			 { 
			
			  $multidkp_array = array(
																'ROW_CLASS'     => $eqdkp->switch_row_class(),
																'NNAME' 				=> $html->ToolTip($key['dkp_tooltip'],$key['name']) ,
																'EARNED' 				=> $html->ToolTip($key['dkp_tooltip'],$key['earned']) ,
																'SPENT' 				=> $html->ToolTip($key['dkp_tooltip'],$key['spend']),
																'ADJUST' 				=> $html->ToolTip($key['dkp_tooltip'],$key['adjust']), 
																'CURRENT' 			=> $html->ToolTip($key['dkp_tooltip'],$key['current']) ,
																'C_NNAME' 			=> $key['name'] ,
																'C_EARNED' 			=> color_item($key['earned']) ,
																'C_SPENT' 			=> color_item($key['spend']) ,
																'C_ADJUST' 			=> color_item($key['adjust']) ,
																'C_CURRENT' 		=> color_item($key['current']),
				);
				$tpl->assign_block_vars('multidkp',$multidkp_array );	
			 } // end foreach 											  
		 } // end if
	}	 
	#######################

    $tpl->assign_vars(array(
	        'IS_MULTIDKP'			=> ( $conf_plus['pk_multidkp'] == 1 )? true : false,
	        'GUILDTAG' => $eqdkp->config['guildtag'],
	        'NAME'     => $member['member_name'],
					'CLASS_RACE_IMG'     => $class_race  ,
		    'RACENAME'     => $race_name['race_name'],
		    'CLASSNAME'     => $class_name['class_name'],


        'L_EARNED'                        => $user->lang['earned'],
        'L_SPENT'                         => $user->lang['spent'],
        'L_ADJUSTMENT'                    => $user->lang['adjustment'],
        'L_CURRENT'                       => $user->lang['current'],
        'L_RAIDS_30_DAYS'                 => sprintf($user->lang['raids_x_days'], 30),
        'L_RAIDS_60_DAYS'                 => sprintf($user->lang['raids_x_days'], 60),
        'L_RAIDS_90_DAYS'                 => sprintf($user->lang['raids_x_days'], 90),
        'L_RAIDS_LIFETIME'                => sprintf($user->lang['raids_lifetime'],
                                                date($user->style['date_notime_short'], $member['member_firstraid']),
                                                date($user->style['date_notime_short'], $member['member_lastraid'])),
        'L_RAID_ATTENDANCE_HISTORY'       => $user->lang['raid_attendance_history'],
        'L_DATE'                          => $user->lang['date'],
        'L_NAME'                          => $user->lang['name'],
        'L_NOTE'                          => $user->lang['note'],
        'L_EARNED'                        => $user->lang['earned'],
        'L_CURRENT'                       => $user->lang['current'],
        'L_ITEM_PURCHASE_HISTORY'         => $user->lang['item_purchase_history'],
        'L_RAID'                          => $user->lang['raid'],
        'L_INDIVIDUAL_ADJUSTMENT_HISTORY' => $user->lang['individual_adjustment_history'],
        'L_REASON'                        => $user->lang['reason'],
        'L_ADJUSTMENT'                    => $user->lang['adjustment'],
        'L_ATTENDANCE_BY_EVENT'           => $user->lang['attendance_by_event'],
        'L_EVENT'                         => $user->lang['event'],
        'L_PERCENT'                       => $user->lang['percent'],
        'L_MULTI_KONTONAME_SHORT'           => $user->lang['Multi_kontoname_short'],
        
        

        'O_EVENT'   => $current_order['uri'][0],
        'O_PERCENT' => $current_order['uri'][1],

        'EARNED'         => $member['member_earned'],
        'SPENT'          => $member['member_spent'],
        'ADJUSTMENT'     => $member['member_adjustment'],
        'CURRENT'        => $member['member_current'],
        'RAIDS_30_DAYS'  => sprintf($user->lang['of_raids'], $percent_of_raids['30']),
        'RAIDS_60_DAYS'  => sprintf($user->lang['of_raids'], $percent_of_raids['60']),
        'RAIDS_90_DAYS'  => sprintf($user->lang['of_raids'], $percent_of_raids['90']),
        'RAIDS_LIFETIME' => sprintf($user->lang['of_raids'], $percent_of_raids['lifetime']),

        'C_ADJUSTMENT'     => color_item($member['member_adjustment']),
        'C_CURRENT'        => color_item($member['member_current']),
        'C_RAIDS_30_DAYS'  => color_item($percent_of_raids['30'], true),
        'C_RAIDS_60_DAYS'  => color_item($percent_of_raids['60'], true),
        'C_RAIDS_90_DAYS'  => color_item($percent_of_raids['90'], true),
        'C_RAIDS_LIFETIME' => color_item($percent_of_raids['lifetime'], true),

        'RAID_FOOTCOUNT'       => sprintf($user->lang['viewmember_raid_footcount'], $total_attended_raids, $user->data['user_rlimit']),
        'RAID_PAGINATION'      => generate_pagination('viewmember.php'.$SID.'&amp;name='.$member['member_name'].'&amp;istart='.$istart, $total_attended_raids, $user->data['user_rlimit'], $rstart, 'rstart'),
        'ITEM_FOOTCOUNT'       => sprintf($user->lang['viewmember_item_footcount'], $total_purchased_items, $user->data['user_ilimit']),
        'ITEM_PAGINATION'      => generate_pagination('viewmember.php'.$SID.'&amp;name='.$member['member_name'].'&amp;rstart='.$rstart, $total_purchased_items, $user->data['user_ilimit'], $istart, 'istart'),
        'ADJUSTMENT_FOOTCOUNT' => sprintf($user->lang['viewmember_adjustment_footcount'], $db->num_rows($adjustments_result)),

        'U_VIEW_MEMBER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '=' . $member['member_name'] . '&amp;')
    );

    $db->free_result($adjustments_result);

    $pm->do_hooks('/viewmember.php');

    $eqdkp->set_vars(array(
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['viewmember_title'], $member['member_name']),
        'template_file' => 'viewmember.html',
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_name_provided']);
}

// ---------------------------------------------------------
// Page-specific functions
// ---------------------------------------------------------
function raid_count($start_date, $end_date, $member_name)
{
    global $db;

    $raid_count = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE . ' WHERE (raid_date BETWEEN ' . $start_date . ' AND ' . $end_date . ')');

    $sql = 'SELECT count(*)
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name='" . $member_name . "')
            AND (r.raid_date BETWEEN " . $start_date . ' AND ' . $end_date . ')';
    $individual_raid_count = $db->query_first($sql);

    $percent_of_raids = ( $raid_count > 0 ) ? round(($individual_raid_count / $raid_count) * 100) : 0;

    $raid_count_stats = array(
        'percent'     => $percent_of_raids,
        'total_count' => $raid_count,
        'indiv_count' => $individual_raid_count);

    return $raid_count_stats['percent']; // Only thing needed ATM
}
?>
