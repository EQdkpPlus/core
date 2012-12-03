<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * stats.php
 * Began: Sat December 21 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_member_list');

$sort_order = array(
     0 => array('member_name', 'member_name desc'),
     1 => array('member_firstraid', 'member_firstraid desc'),
     2 => array('member_lastraid', 'member_lastraid desc'),
     3 => array('member_raidcount desc', 'member_raidcount'),
     4 => array('member_earned desc', 'member_earned'),
     5 => array('earned_per_day desc', 'earned_per_day'),
     6 => array('earned_per_raid desc', 'earned_per_raid'),
     7 => array('member_spent desc', 'member_spent'),
     8 => array('spent_per_day desc', 'spent_per_day'),
     9 => array('spent_per_raid desc', 'spent_per_raid'),
    10 => array('lost_to_adjustment desc', 'lost_to_adjustment'),
    11 => array('lost_to_spent desc', 'lost_to_spent'),
    12 => array('member_current desc', 'member_current')
);

$current_order = switch_order($sort_order);

$total_raids = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE);
$show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == "all") ) ? true : false;

// No idea if this massive query will work outside MySQL...if not, we'll have
// to use a switch and get the values another way




$sql = 'SELECT member_name, member_earned, member_spent, member_adjustment,
        (member_earned-member_spent+member_adjustment) AS member_current,
        ((member_spent/member_earned)*100) AS lost_to_spent,
        ((member_adjustment-(member_adjustment*2))/member_earned)*100 AS lost_to_adjustment,
        r.rank_prefix, r.rank_suffix
        FROM ' . MEMBERS_TABLE . ' m
        LEFT JOIN ' . MEMBER_RANKS_TABLE . ' r
        ON (m.member_rank_id = r.rank_id)';

if ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) )
{
    $sql .= " WHERE member_status='1'";
}
$sql .= "ORDER BY ".$current_order['sql'].";";

if ( !($members_result = $db->query($sql)) )
{
    message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
}

//raidcount, firstraid, lastraid are often not correctly filled, so get the data live
$raid_sql = "SELECT r.raid_date, r.raid_id, rm.member_name FROM __raids r, __raid_attendees rm WHERE r.raid_id = rm.raid_id ORDER BY r.raid_date ASC;";
if ( !($raid_result = $db->query($raid_sql)) )
{
	message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
}
$raids = array();
while ( $rrow = $db->fetch_record($raid_result) )
{
	$raids[$rrow['member_name']][] = $rrow['raid_date'];
}

while ( $row = $db->fetch_record($members_result) )
{
	//fill raidcount, firstraid, lastraid etc.
	$row['member_raidcount'] = count($raids[$row['member_name']]);
	if($row['member_raidcount']) {
		$row['member_firstraid'] = $raids[$row['member_name']][0];
		$row['member_lastraid'] = $raids[$row['member_name']][$row['member_raidcount']-1];
		$row['earned_per_day'] = $row['member_earned'] / (((time() - $row['member_firstraid'])+86400) / 86400);
		$row['zero_check'] = (time() - $row['member_firstraid']) / 86400;
		$row['spent_per_day'] = $row['member_spent'] / (((time() - $row['member_firstraid'])+86400) / 86400);
		$row['earned_per_raid'] = $row['member_earned']/$row['member_raidcount'];
		$row['spent_per_raid'] = $row['member_spent']/$row['member_raidcount'];
	}

    // Default the values of these in case they have no earned or spent or
    // adjustment
    $row['earned_per_day'] = ( ( (!empty($row['earned_per_day']) ) && ( $row['zero_check'] > '0.01') )) ? $row['earned_per_day'] : '0.00';
    $row['earned_per_raid'] = (!empty($row['earned_per_raid'])) ? $row['earned_per_raid'] : '0.00';
    $row['spent_per_day'] = ( ( (!empty($row['spent_per_day']) ) && ($row['zero_check'] > '0.01') )) ? $row['spent_per_day'] : '0.00';
    $row['spent_per_raid'] = (!empty($row['spent_per_raid'])) ? $row['spent_per_raid'] : '0';
    $row['lost_to_adjustment'] = (!empty($row['lost_to_adjustment'])) ? $row['lost_to_adjustment'] : '0.00';
    $row['lost_to_spent'] = (!empty($row['lost_to_spent'])) ? $row['lost_to_spent'] : '0.00';

    // Find out how many days it's been since their first raid
    $days_since_start = 0;
    $days_since_start = round((time() - $row['member_firstraid']) / 86400);

    // Find the percentage of raids they've been on
    $attended_percent = ( $total_raids > 0 ) ? round(($row['member_raidcount'] / $total_raids) * 100) : 0;

    $tpl->assign_block_vars('stats_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'U_VIEW_MEMBER' => 'viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$row['member_name'],
        'NAME' => $row['rank_prefix'] . $row['member_name'] . $row['rank_suffix'],
        'FIRST_RAID' => ( !empty($row['member_firstraid']) ) ? date($user->style['date_notime_short'], $row['member_firstraid']) : '&nbsp;',
        'LAST_RAID' => ( !empty($row['member_lastraid']) ) ? date($user->style['date_notime_short'], $row['member_lastraid']) : '&nbsp;',
        'ATTENDED_COUNT' => $row['member_raidcount'],
        'C_ATTENDED_PERCENT' => color_item($attended_percent, true),
        'ATTENDED_PERCENT' => $attended_percent,
        'EARNED_TOTAL' =>  runden($row['member_earned']),
        'EARNED_PER_DAY' => sprintf("%.2f", $row['earned_per_day']),
        'EARNED_PER_RAID' => sprintf("%.2f", $row['earned_per_raid']),
        'SPENT_TOTAL' =>  runden($row['member_spent']),
        'SPENT_PER_DAY' => sprintf("%.2f", $row['spent_per_day']),
        'SPENT_PER_RAID' => sprintf("%.2f", $row['spent_per_raid']),
        'LOST_TO_ADJUSTMENT' => sprintf("%.2f", $row['lost_to_adjustment']),
        'LOST_TO_SPENT' => sprintf("%.2f", $row['lost_to_spent']),
        'C_CURRENT' => color_item($row['member_current']),
        'CURRENT' =>  runden($row['member_current']))
    );
}

if ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) )
{
    $footcount_text = sprintf($user->lang['stats_active_footcount'], $db->num_rows($members_result),
                              '<a href="stats.php'.$SID.'&amp;o='.$current_order['uri']['current'].'&amp;show=all" class="rowfoot">');
}
else
{
    $footcount_text = sprintf($user->lang['stats_footcount'], $db->num_rows($members_result));
}

// Class Statistics
// Class Summary
// Classes array - if an element is false, that class has gotten no
// loot and won't show up from the SQL query
// Otherwise it contains an array with the SQL data
// New for 1.3 - grab class info from database

    $eq_classes = array();

// Find the total members existing with a class
$sql = 'SELECT count(member_id)
        FROM ' . MEMBERS_TABLE ;
$total_members = $db->query_first($sql);

// Find the total priced items
$sql = 'SELECT count(item_id)
        FROM ' . ITEMS_TABLE . '
        WHERE item_value != 0.00';
$total_drops = $db->query_first($sql);

// Find out how many members of each class exist
$class_counts = array();
$sql = 'SELECT member_class_id, count(member_id) AS class_count
        FROM ' . MEMBERS_TABLE . '
        GROUP BY member_class_id';
$result = $db->query($sql);

while ( $row = $db->fetch_record($result) )
{
    $class_counts[ $row['member_class_id'] ] = $row['class_count'];
}
$db->free_result($result);


// Query finds all items purchased by each class
// Will not find items that are unpriced
$sql = 'SELECT c.class_name, c.class_id, count(i.item_id) AS class_drops
        FROM ' . ITEMS_TABLE . ' i, ' . CLASS_TABLE . ' c, ' . MEMBERS_TABLE . " m
        WHERE (m.member_name = i.item_buyer)
        AND (i.item_value != 0.00)
        AND (m.member_class_id = c.class_id)
        GROUP BY c.class_name";

$result = $db->query($sql);

while ( $row = $db->fetch_record($result) )
{
    $class = $row['class_name'];
    $class_id = $row['class_id'];

    $class_drops = $row['class_drops'];
    $class_drop_pct = ( $total_drops > 0 ) ? round(($class_drops / $total_drops) * 100) : 0;

    $class_members = ( isset($class_counts[$class_id]) ) ? $class_counts[$class_id] : 0;

    $class_factor = ( $class_members > 0 ) ? round(($class_drops / $class_members) * 100) : 0;

    $eq_classes[$class] = array(
         'drops' => $class_drops,
         'drop_pct' => $class_drop_pct,
         'class_count' => $class_members,
         'class_pct' => ( $total_members > 0 ) ? round(($class_members / $total_members) * 100) : 0,
         'factor' => $class_factor);

}
$db->free_result($result);


// Query finds all items purchased by each armor type
// Will not find items that are unpriced
// Check out them longass var names! :-)
$sql = 'SELECT c.class_armor_type, count(i.item_id) AS armor_type_drops
        FROM ' . ITEMS_TABLE . ' i, ' . CLASS_TABLE . ' c, ' . MEMBERS_TABLE . " m
        WHERE (m.member_name = i.item_buyer)
        AND (i.item_value != 0.00)
        AND (m.member_class_id = c.class_id)
        GROUP BY c.class_armor_type";

$result = $db->query($sql);

while ( $row = $db->fetch_record($result) )
{
    $armor = $row['class_armor_type'];

    $number_of_armor_type_members = $db->query_first('SELECT count(*)
				      FROM '. CLASS_TABLE .' c, ' . MEMBERS_TABLE . " m
				      WHERE c.class_armor_type = '".$armor."'
				      AND m.member_class_id = c.class_id");

    $number_of_armor_type_drops = $row['armor_type_drops'];
    $pct_of_armor_type_to_all_members = ( $total_members > 0 ) ? round(($number_of_armor_type_members / $total_members) * 100) : 0;
    $type_of_armor_drop_pct = ( $total_drops > 0 ) ? round(($number_of_armor_type_drops / $total_drops) * 100) : 0;
    $pct_drops_per_armor_type = ( $number_of_armor_type_members > 0 ) ? round(($number_of_armor_type_drops / $number_of_armor_type_members) * 100) : 0;
    $row_class = $eqdkp->switch_row_class();
    $loot_factor = ( $number_of_armor_type_members > 0 ) ? round((($number_of_armor_type_members / $type_of_armor_drop_pct) - 1) * 100) : '0';

    $tpl->assign_block_vars('type_row', array(
        'ROW_TYPE' => $row_class,
        'LINK_TYPE' => ( $row_class == 'rowhead' ) ? 'header' : '',
        'U_LIST_MEMBERS' => 'listmembers.php' . $SID . '&amp;filter=ARMOR_' .strtolower($armor),
        //'U_LIST_MEMBERS' => 'listmembers.php' . $SID . '&amp;filter=' . strtolower($armor),
        'TYPE' => $armor,
        'LOOT_COUNT' => $number_of_armor_type_drops,
        'LOOT_PCT' => sprintf("%d%%", $type_of_armor_drop_pct),
        'TYPE_COUNT' => $number_of_armor_type_members,
        'TYPE_PCT' => sprintf("%d%%", $pct_of_armor_type_to_all_members),
        'LOOT_FACTOR' => sprintf("%d%%", $loot_factor),
        'T_LOOT_FACTOR' => color_item($loot_factor))
    );



}
$db->free_result($result);






// We still need to find out how many of the class exist
$sql = 'SELECT c.class_name, count(m.member_id) as class_count
        FROM ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE .' c
	WHERE m.member_class_id = c.class_id
        GROUP BY m.member_class_id';
$result = $db->query($sql);

while ( $row = $db->fetch_record($result) )
{
    $class = $row['class_name'];
    $class_count = $row['class_count'];

    if( (empty($class)) || ($class == 'NULL') )
    {
        continue;
    }

    // if this isn't an array, define blank values
    if ( !is_array($eq_classes[$class]) )
    {
        $v = array(
            'drops' => 0,
            'drop_pct' => 0,
            'class_count' => $class_count,
            'class_pct' => ( $total_members > 0 ) ? round(($class_count / $total_members) * 100) : 0,
            'factor' => 0
        );
    }
    else
    {
        $v = $eq_classes[$class];
    }

    $row_class = ( (!empty($_GET['class'])) && ($_GET['class'] == $k) ) ? 'rowhead' : $eqdkp->switch_row_class();

    $loot_factor = ( $v['class_pct'] > 0 ) ? round((($v['drop_pct'] / $v['class_pct']) - 1) * 100) : '0';

    $tpl->assign_block_vars('class_row', array(
        'ROW_CLASS' => $row_class,
        'LINK_CLASS' => ( $row_class == 'rowhead' ) ? 'header' : '',
        'U_LIST_MEMBERS' => 'listmembers.php' . $SID . '&amp;filter=' . $class,
        'CLASS' => $class,
        'LOOT_COUNT' => $v['drops'],
        'LOOT_PCT' => sprintf("%d%%", $v['drop_pct']),
        'CLASS_COUNT' => $v['class_count'],
        'CLASS_PCT' => sprintf("%d%%", $v['class_pct']),
        'LOOT_FACTOR' => sprintf("%d%%", $loot_factor),
        'C_LOOT_FACTOR' => color_item($loot_factor))
    );
}

$tpl->assign_vars(array(
    'L_NAME' => $user->lang['name'],
    'L_RAIDS' => $user->lang['raids'],
    'L_EARNED' => $user->lang['earned'],
    'L_SPENT' => $user->lang['spent'],
    'L_PCT_EARNED_LOST_TO' => $user->lang['pct_earned_lost_to'],
    'L_CURRENT' => $user->lang['current'],
    'L_FIRST' => $user->lang['first'],
    'L_LAST' => $user->lang['last'],
    'L_ATTENDED' => $user->lang['attended'],
    'L_TOTAL' => $user->lang['total'],
    'L_PER_DAY' => $user->lang['per_day'],
    'L_PER_RAID' => $user->lang['per_raid'],
    'L_ADJUSTMENT' => $user->lang['adjustment'],

    'L_CLASS' => $user->lang['class'],
    'L_LOOTS' => $user->lang['loots'],
    'L_MEMBERS' => $user->lang['members'],
    'L_LOOT_FACTOR' => $user->lang['loot_factor'],

    'O_NAME' => $current_order['uri'][0],
    'O_FIRSTRAID' => $current_order['uri'][1],
    'O_LASTRAID' => $current_order['uri'][2],
    'O_RAIDCOUNT' => $current_order['uri'][3],
    'O_EARNED' => $current_order['uri'][4],
    'O_EARNED_PER_DAY' => ($in->get('o')) ? $in->get('o') :  '0.0',
    'O_EARNED_PER_RAID' => ($in->get('o')) ? $in->get('o') :  '0.0',
    'O_SPENT' => $current_order['uri'][7],
    'O_SPENT_PER_DAY' => ($in->get('o')) ? $in->get('o') :  '0.0',
    'O_SPENT_PER_RAID' => ($in->get('o')) ? $in->get('o') :  '0.0',
    'O_LOST_TO_ADJUSTMENT' => $current_order['uri'][10],
    'O_LOST_TO_SPENT' => $current_order['uri'][11],
    'O_CURRENT' => $current_order['uri'][12],

    'U_STATS' => 'stats.php'.$SID.'&amp;',

    'SHOW' => $in->get('show'),

    'STATS_FOOTCOUNT' => $footcount_text)
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['stats_title'], $eqdkp->config['dkp_name']),
    'template_file' => 'stats.html',
    'display'       => true)
);
?>