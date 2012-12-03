<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewraid.php
 * Began: Thu December 19 2002
 *
 * $Id: viewraid.php 6 2006-05-08 17:11:35Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if ($conf_plus['pk_itemstats'] == 1){
	include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
}

$user->check_auth('u_raid_view');

if ( (isset($_GET[URI_RAID])) && (intval($_GET[URI_RAID] > 0)) )
{
    $sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value, raid_added_by, raid_updated_by
            FROM ' . RAIDS_TABLE . "
            WHERE raid_id='".$_GET[URI_RAID]."'";

    if ( !($raid_result = $db->query($sql)) )
    {
        message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
    }

    // Check for valid raid
    if ( !$raid = $db->fetch_record($raid_result) )
    {
        message_die($user->lang['error_invalid_raid_provided']);
    }
    $db->free_result($raid_result);

    //
    // Attendees
    //
    $sql = 'SELECT member_name
            FROM ' . RAID_ATTENDEES_TABLE . "
            WHERE raid_id='".$raid['raid_id']."'
            ORDER BY member_name";
    $result = $db->query($sql);

   // add a faliure check here

    while ( $arow = $db->fetch_record($result) )
    {
        $attendees[] = addslashes($arow['member_name']);
    }
    $db->free_result($result);

    // Get each attendee's rank
    $ranks = array();
    $sql = 'SELECT m.member_name, r.rank_prefix, r.rank_suffix
            FROM ( ' . MEMBERS_TABLE . ' m
            LEFT JOIN ' . MEMBER_RANKS_TABLE . " r
            ON m.member_rank_id = r.rank_id )
            WHERE m.member_name IN ('" . implode("', '", $attendees) . '\')';
    $result = $db->query($sql);
    while ( $row = $db->fetch_record($result) )
    {
        $ranks[ $row['member_name'] ] = array(
            'prefix' => (( !empty($row['rank_prefix']) ) ? $row['rank_prefix'] : ''),
            'suffix' => (( !empty($row['rank_suffix']) ) ? $row['rank_suffix'] : '')
        );
    }
    $db->free_result($result);

    if ( @sizeof($attendees) > 0 )
    {
        // First get rid of duplicates and resort them just in case,
        // so we're sure they're displayed correctly
        $attendees = array_unique($attendees);
        sort($attendees);
        reset($attendees);
        $rows = ceil(sizeof($attendees) / $user->style['attendees_columns']);

        // First loop: iterate through the rows
        // Second loop: iterate through the columns as defined in template_config,
        // then "add" an array to $block_vars that contains the column definitions,
        // then assign the block vars.
        // Prevents one column from being assigned and the rest of the columns for
        // that row being blank
        for ( $i = 0; $i < $rows; $i++ )
        {
            $block_vars = array();
            for ( $j = 0; $j < $user->style['attendees_columns']; $j++ )
            {
                $offset = ($i + ($rows * $j));
                $attendee = ( isset($attendees[$offset]) ) ? $attendees[$offset] : '';

                $html_prefix = ( isset($ranks[$attendee]) ) ? $ranks[$attendee]['prefix'] : '';
                $html_suffix = ( isset($ranks[$attendee]) ) ? $ranks[$attendee]['suffix'] : '';

                if ( $attendee != '' )
                {
#                    $block_vars += array(
#                        'COLUMN'.$j.'_NAME' => '<a href="viewmember.php' . $SID . '&amp;' . URI_NAME . '=' . $attendee . '">' . $html_prefix . $attendee . $html_suffix . '</a>'
#                    );
                    $block_vars += array(
                        'COLUMN'.$j.'_NAME' => get_coloredLinkedName($attendee)
                    );

                }
                else
                {
                    $block_vars += array(
                        'COLUMN'.$j.'_NAME' => ''
                    );
                }

                // Are we showing this column?
                $s_column = 's_column'.$j;
                ${$s_column} = true;
            }
            $tpl->assign_block_vars('attendees_row', $block_vars);
        }
        $column_width = floor(100 / $user->style['attendees_columns']);
    }
    else
    {
        message_die('Could not get raid attendee information.','Critical Error');
    }

    //
    // Drops
    //
    $sql = 'SELECT item_id, item_buyer, item_name, item_value
            FROM ' . ITEMS_TABLE . "
            WHERE raid_id='".$raid['raid_id']."'";
    if ( !($items_result = $db->query($sql)) )
    {
        message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql);
    }
    while ( $item = $db->fetch_record($items_result) )
    {
        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS'    => $eqdkp->switch_row_class(),
            #'BUYER'        => getClassImg(get_classNamebyMemberName($item['item_buyer'])).' '.$item['item_buyer'],
            'BUYER'        => get_classNameImgViewmembers($item['item_buyer']),
            
            #'BUYER'        => getClassHtmlColorLinkCode(get_classNamebyMemberName($item['item_buyer'])),
            'U_VIEW_BUYER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$item['item_buyer'],
            'NAME'         => ( $conf_plus['pk_itemstats'] == 1 ) ? itemstats_decorate_name(stripslashes($item['item_name'])): stripslashes($item['item_name']),
            'U_VIEW_ITEM'  => 'viewitem.php' . $SID . '&amp;' . URI_ITEM . '='.$item['item_id'],
            'VALUE'        => $item['item_value'])
        );
    }

    //
    // Class distribution
    //
    // If an element is false, that class didn't attend this raid
    // New for 1.3 - grab class information from the database

    $eq_classes = array();
    $total_attendees = sizeof($attendees);

    // Get each attendee's class
    $sql = 'SELECT m.member_name, c.class_name AS member_class
            FROM ' . MEMBERS_TABLE . ' m, ' .CLASS_TABLE ." c
	    WHERE m.member_class_id = c.class_id
            AND member_name IN ('" . implode("', '", $attendees) . '\')';
    $result = $db->query($sql);
    while ( $row = $db->fetch_record($result) )
    {
        $member_name = $row['member_name'];
	$member_class = $row['member_class'];

        if ( $member_name != '' )
        {
            $html_prefix = ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['prefix'] : '';
            $html_suffix = ( isset($ranks[$member_name]) ) ? $ranks[$member_name]['suffix'] : '';

            $eq_classes[ $row['member_class'] ] .= " " . $html_prefix . $member_name . $html_suffix .",";
	    $class_count[ $row['member_class'] ]++;
        }
    }
    $db->free_result($result);
    unset($ranks);


    // Now find out how many of each class there are
    foreach ( $eq_classes as $class => $members )
    {
	$percentage =  ( $total_attendees > 0 ) ? round(($class_count[$class] / $total_attendees) * 100) : 0;

        $tpl->assign_block_vars('class_row', array(
            'CLASS'     => get_classNameImgListmembers($class),
            'BAR'       => create_bar(($class_count[ $class ] * 10), $class_count[ $class ] . ' (' . $percentage . '%)'),
            'ATTENDEES' => $members)
        );
    }
    unset($eq_classes);
    
    $html = new htmlPlus(); // plus html class for tooltip
		$event_icon = $html->getEventIcon($raid['raid_name']);
			
		if(strlen($event_icon) > 0)
		{
			$event_icon = "<img src='./images/events/".$event_icon."'> " ;
		} 
		else
		{
			$event_icon = "";
		}


    $tpl->assign_vars(array(
        'L_MEMBERS_PRESENT_AT' => sprintf($user->lang['members_present_at'], stripslashes($raid['raid_name']),
                                  date($user->style['date_notime_long'], $raid['raid_date'])),
        'L_ADDED_BY'           => $user->lang['added_by'],
        'L_UPDATED_BY'         => $user->lang['updated_by'],
        'L_NOTE'               => $user->lang['note'],
        'L_VALUE'              => $user->lang['value'],
        'L_DROPS'              => $user->lang['drops'],
        'L_BUYER'              => $user->lang['buyer'],
        'L_ITEM'               => $user->lang['item'],
        'L_SPENT'              => $user->lang['spent'],
        'L_ATTENDEES'          => $user->lang['attendees'],
        'L_CLASS_DISTRIBUTION' => $user->lang['class_distribution'],
        'L_CLASS'              => $user->lang['class'],
        'L_PERCENT'            => $user->lang['percent'],
        'L_RANK_DISTRIBUTION'  => $user->lang['rank_distribution'],
        'L_RANK'               => $user->lang['rank'],
         
         'EVENT_ICON'          => $event_icon,
         'EVENT_NAME'					 => stripslashes($raid['raid_name']),		

        'S_COLUMN0' => ( isset($s_column0) ) ? true : false,
        'S_COLUMN1' => ( isset($s_column1) ) ? true : false,
        'S_COLUMN2' => ( isset($s_column2) ) ? true : false,
        'S_COLUMN3' => ( isset($s_column3) ) ? true : false,
        'S_COLUMN4' => ( isset($s_column4) ) ? true : false,
        'S_COLUMN5' => ( isset($s_column5) ) ? true : false,
        'S_COLUMN6' => ( isset($s_column6) ) ? true : false,
        'S_COLUMN7' => ( isset($s_column7) ) ? true : false,
        'S_COLUMN8' => ( isset($s_column8) ) ? true : false,
        'S_COLUMN9' => ( isset($s_column9) ) ? true : false,

        'COLUMN_WIDTH' => ( isset($column_width) ) ? $column_width : 0,
        'COLSPAN'      => $user->style['attendees_columns'],

        'RAID_ADDED_BY'       => ( !empty($raid['raid_added_by']) ) ? stripslashes($raid['raid_added_by']) : 'N/A',
        'RAID_UPDATED_BY'     => ( !empty($raid['raid_updated_by']) ) ? stripslashes($raid['raid_updated_by']) : 'N/A',
        'RAID_NOTE'           => ( !empty($raid['raid_note']) ) ? stripslashes($raid['raid_note']) : '&nbsp;',
        'DKP_NAME'            => $eqdkp->config['dkp_name'],
        'RAID_VALUE'          => $raid['raid_value'],
        'ATTENDEES_FOOTCOUNT' => sprintf($user->lang['viewraid_attendees_footcount'], sizeof($attendees)),
        'ITEM_FOOTCOUNT'      => sprintf($user->lang['viewraid_drops_footcount'], $db->num_rows($items_result)))
    );

    $eqdkp->set_vars(array(
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['viewraid_title'],
        'template_file' => 'viewraid.html',
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_raid_provided']);
}
?>
