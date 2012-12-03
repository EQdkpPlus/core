<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * summary.php
 * Began: Sat December 21 2002
 * 
 * $Id$
 * 
 ******************************/
 
define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_raid_list');

//
// Build the from/to GET vars to pass back to the script
//
if ( (isset($_POST['submit'])) && ($_POST['submit'] == $user->lang['create_news_summary']) )
{
	$fv = new Form_Validate();
	$fv->is_valid_date('summary_date_from', $user->lang['fv_date']);
	$fv->is_valid_date('summary_date_to', $user->lang['fv_date']);
    
    // Kick 'em back to the start if there was an error from above
    if ( $fv->is_error() )
    {
        header('Location: summary.php'.$SID);
    }
    else
    {
        // Make the dates into mm-dd-yy and add them to the URI,
        // then redirect back to the script
        $date1 = substr($_POST['summary_date_from'],3,2) . '-' . substr($_POST['summary_date_from'],0,2) . '-' . substr($_POST['summary_date_from'],6);
        $date2 = substr($_POST['summary_date_to'],3,2) . '-' . substr($_POST['summary_date_to'],0,2) . '-' . substr($_POST['summary_date_to'],6);
        header('Location: summary.php'.$SID.'&from='.$date1.'&to='.$date2);
    }
}
//
// Display summary
//
elseif ( (isset($_GET['from'])) && (isset($_GET['to'])) )
{
    $s_step1 = false;
    
    $date1 = @explode('-', $_GET['from']);
    $mo1 = $date1[0];
    $d1  = $date1[1];
    $y1  = $date1[2];
    
    $date2 = @explode('-', $_GET['to']);
    $mo2 = $date2[0];
    $d2  = $date2[1] + 1; // Includes raids/items ON that day
    $y2  = $date2[2];
    
    // Make sure both make a valid timestamp    
    $date1 = @mktime(0, 0, 0, $mo1, $d1, $y1);
    $date2 = @mktime(0, 0, 0, $mo2, $d2, $y2);
    
    $date1_display = @mktime(0, 0, 0, $mo1, $d1, $y1);
    $date2_display = @mktime(0, 0, 0, $mo2, ($d2 - 1), $y2);
    
    // Make sure date1 is on or after the first raid entry
    $sql = 'SELECT MIN(raid_date) 
            FROM ' . RAIDS_TABLE . '
            WHERE raid_date > 0';
    $min_date = $db->query_first($sql);
    $date1 = ( $date1 < $min_date ) ? $min_date : $date1;
    
    if ( ($date1 > 0) && ($date2 > 0) )
    {
        // Get the current active members.  Used to find out the percentage of
        // active members present on each raid
        $active_members = $db->query_first('SELECT count(member_id) FROM ' . MEMBERS_TABLE . " WHERE member_firstraid BETWEEN " . $date1 . ' AND ' . $date2);
        
        // Build the raids
        $raids    = array();
        $drops    = array();
        $raid_ids = array();
        
        $sql = 'SELECT r.raid_id, r.raid_name, r.raid_date, r.raid_note,
                r.raid_value, count(ra.raid_id) AS attendee_count 
                FROM ' . RAIDS_TABLE .' r, ' . RAID_ATTENDEES_TABLE . ' ra
                WHERE (ra.raid_id = r.raid_id)
                AND (r.raid_date BETWEEN '.$date1.' AND '.$date2.')
                GROUP BY r.raid_id
                ORDER BY r.raid_date DESC';
        if ( !($raids_result = $db->query($sql)) )
        {
            message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
        }
        
        if ( !$db->num_rows($raids_result) )
        {
            message_die('No raids occurred between ' . date('n/j/y', $date1_display) . ' and ' . date('n/j/y', $date2_display));
        }
        
        while ( $row = $db->fetch_record($raids_result) )
        {
            $raids[ $row['raid_id'] ] = array(
                'raid_id' => $row['raid_id'],
                'raid_name' => $row['raid_name'],
                'raid_date' => $row['raid_date'],
                'raid_note' => $row['raid_note'],
                'raid_value' => $row['raid_value'],
                'attendee_count' => $row['attendee_count']);
                
            $raid_ids[] = $row['raid_id'];
        }
        $db->free_result($raids_result);
        
        // Find the item drops for each raid
        $sql = 'SELECT raid_id, count(item_id) AS count 
                FROM ' . ITEMS_TABLE . ' 
                WHERE raid_id IN (' . implode(', ', $raid_ids) . ')
                GROUP BY raid_id';
        $result = $db->query($sql);
        
        while ( $row = $db->fetch_record($result) )
        {
            $drops[ $row['raid_id'] ] = $row['count'];
        }
        $db->free_result($result);
        
        foreach ( $raids as $raid_id => $row )
        {
            $raid_drops = ( isset($drops[ $row['raid_id'] ]) ) ? $drops[ $row['raid_id'] ] : '0';
            
            $attendees = $row['attendee_count'];
            $attendees_percent = ( $active_members > 0 ) ? round(($attendees / $active_members) * 100) : '0';
            
            $tpl->assign_block_vars('raids_row', array(
                'ROW_CLASS' => $eqdkp->switch_row_class(),
                'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
                'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
                'NAME' => stripslashes($row['raid_name']),
                'NOTE' => stripslashes($row['raid_note']),
                'ATTENDEES' => $attendees,
                'ATTENDEES_PCT' => sprintf("%d%%", $attendees_percent),
                'ITEMS' => $raid_drops,
                'VALUE' => sprintf("%.2f", $row['raid_value']),
                'C_ATTENDEES_PCT' => color_item($attendees_percent, true))
            );
        }
        
        // Build the raid array. Contains total raids, total earned
        $sql = 'SELECT count(raid_id) AS total_raids, sum(raid_value) AS total_earned
                FROM ' . RAIDS_TABLE . '
                WHERE raid_date BETWEEN '.$date1.' AND '.$date2;
        $raid_total_result = $db->query($sql);
        $raids = $db->fetch_record($raid_total_result);
        $raids['total_earned'] = ( isset($raids['total_earned']) ) ? $raids['total_earned'] : '0.00';
        $db->free_result($raid_total_result);
        
        // Build the drops array. Contains total drops, total spent
        $sql = 'SELECT count(item_id) AS total_drops, sum(item_value) AS total_spent
                FROM ' . ITEMS_TABLE . '
                WHERE item_date BETWEEN '.$date1.' AND '.$date2 .'
                AND item_value != 0.00';
        $drop_total_result = $db->query($sql);
        $drops = $db->fetch_record($drop_total_result);
        $drops['total_spent'] = ( isset($drops['total_spent']) ) ? $drops['total_spent'] : '0.00';
        $db->free_result($drop_total_result);
        
        // Class Summary
        // Classes array - if an element is false, that class has gotten no
        // loot and won't show up from the SQL query
        // Otherwise it contains an array with the SQL data
	// New for 1.3 - grab class info from database

       $eq_classes = array();

        // Find the total members existing before this date to get overall class percentage
        $sql = 'SELECT count(member_id)
                FROM ' . MEMBERS_TABLE . '
                WHERE member_firstraid BETWEEN ' . $date1 . ' AND ' . $date2 . '
                AND member_class_id > 0';
        $total_members = $db->query_first($sql);
        
        // Find out how many members of each class exist
        $class_counts = array();
        $sql = 'SELECT c.class_name AS member_class, count(m.member_id) AS class_count
                FROM ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE . ' c
                WHERE (m.member_firstraid BETWEEN ' . $date1 . ' AND ' . $date2 . ')
		AND (c.class_id = m.member_class_id)
                GROUP BY member_class';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $class_counts[ $row['member_class'] ] = $row['class_count'];
        }
        $db->free_result($result);

        // Query finds all items purchased by each class between these dates
        // Will not find items that are unpriced, will not find members that don't have a class defined
        $sql = 'SELECT c.class_name AS member_class, count(i.item_id) AS class_drops
                FROM ' . ITEMS_TABLE . ' i, ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE . ' c
                WHERE (m.member_name = i.item_buyer)
                AND (i.item_value != 0.00)
                AND (m.member_class_id > 0)
		AND (c.class_id = m.member_class_id)
                AND (i.item_date BETWEEN ' . $date1 . ' AND ' . $date2 . ')
                GROUP BY m.member_class_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $class = $row['member_class'];
            $class_drops = $row['class_drops'];
            $class_drop_pct = ( $drops['total_drops'] > 0 ) ? round(($class_drops / $drops['total_drops']) * 100) : 0;
            
            $class_members = ( isset($class_counts[$class]) ) ? $class_counts[$class] : 0;
            
                $eq_classes[$class] = array(
                    'drops' => $class_drops,
                    'drop_pct' => $class_drop_pct,
                    'class_count' => $class_members,
                    'class_pct' => ( $total_members > 0 ) ? round(($class_members / $total_members) * 100) : 0,
                    'factor' => 0);
        }
        $db->free_result($result);
        
        foreach ( $eq_classes as $k => $v )
        {
            // If v's an array, we have data for this class
            // e.g., they looted something in this time period
            if ( !is_array($v) )
            {
                // We still need to find out how many of the class existed
                $sql = 'SELECT count(member_id) 
                        FROM ' . MEMBERS_TABLE . "
                        WHERE member_class='".$k."'
                        AND member_firstraid BETWEEN " . $date1 . ' AND ' . $date2;
                $class_members = $db->query_first($sql);
                $class_factor = 0;
                
                $v = array(
                    'drops' => 0,
                    'drop_pct' => 0,
                    'class_count' => $class_members,
                    'class_pct' => ( $total_members > 0 ) ? round(($class_members / $total_members) * 100) : 0,
                    'factor' => $class_factor);
            }
            
            $loot_factor = ( $v['class_pct'] > 0 ) ? round((($v['drop_pct'] / $v['class_pct']) - 1) * 100) : '0';
            
            $tpl->assign_block_vars('class_row', array(
                'ROW_CLASS' => $eqdkp->switch_row_class(),
                'U_LIST_MEMBERS' => 'listmembers.php' . $SID . '&amp;filter=' . $k,
                'CLASS' => $k,
                'LOOT_COUNT' => $v['drops'],
                'LOOT_PCT' => sprintf("%d%%", $v['drop_pct']),
                'CLASS_COUNT' => $v['class_count'],
                'CLASS_PCT' => sprintf("%d%%", $v['class_pct']),
                'LOOT_FACTOR' => sprintf("%d%%", $loot_factor),
                'C_LOOT_FACTOR' => color_item($loot_factor))
            );
        }

        $tpl->assign_vars(array(
            'L_SUMMARY_DATES' => sprintf($user->lang['summary_dates'], date('n/j/y', $date1_display), date('n/j/y', $date2_display)),
            'TOTAL_RAIDS' => $raids['total_raids'],
            'TOTAL_ITEMS' => $drops['total_drops'],
            'TOTAL_EARNED' => $raids['total_earned'],
            'TOTAL_SPENT' => $drops['total_spent'],
            
            'L_CLASS_SUMMARY' => sprintf($user->lang['class_summary'], date('n/j/y', $date1_display), date('n/j/y', $date2_display)),
            'L_LOOTS' => $user->lang['loots'],
            'L_MEMBERS' => $user->lang['members'],
            'L_LOOT_FACTOR' => $user->lang['loot_factor'])
        );
    }
    else
    {
        header('Location: summary.php');
    }
}
else
{
    $s_step1 = true;
}

$tpl->assign_vars(array(
    'S_STEP1' => $s_step1,

    'L_ENTER_DATES' => $user->lang['enter_dates'],
    'L_STARTING_DATE' => $user->lang['starting_date'],
    'L_ENDING_DATE' => $user->lang['ending_date'],
    'L_CREATE_NEWS_SUMMARY' => $user->lang['create_news_summary'],
    'L_TOTAL_RAIDS' => $user->lang['total_raids'],
    'L_TOTAL_ITEMS' => $user->lang['total_items'],
    'L_TOTAL_EARNED' => $user->lang['total_earned'],
    'L_TOTAL_SPENT' => $user->lang['total_spent'],
    'L_DATE' => $user->lang['date'],
    'L_EVENT' => $user->lang['event'],
    'L_NOTE' => $user->lang['note'],
    'L_ATTENDEES' => $user->lang['attendees'],
    'L_ITEMS' => $user->lang['items'],
    'L_VALUE' => $user->lang['value'],
    
	// Date Picker
    'DATEPICK_DATE_FROM'        => $jqueryp->Calendar('summary_date_from', date('d.m.Y', time())),
	'DATEPICK_DATE_TO'        => $jqueryp->Calendar('summary_date_to', date('d.m.Y', time())))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['summary_title'],
    'template_file' => 'summary.html',
    'display'       => true)
);
?>