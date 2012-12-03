<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * ------------------
 * index.php
 * Began: Fri August 19 2005
 * Changed: Thu September 26 2005
 * 
 ******************************/
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');

// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }

// Check user permission
$user->check_auth('a_raidplan_');

// Get the plugin
$raidplan = $pm->get_plugin('raidplan');

// Set table names
global $table_prefix;
if (!defined('RP_RAIDS_TABLE')) { define('RP_RAIDS_TABLE', $table_prefix . 'raidplan_raids'); }
if (!defined('RP_CLASSES_TABLE')) { define('RP_CLASSES_TABLE', $table_prefix . 'raidplan_raid_classes'); }
if (!defined('RP_ATTENDEES_TABLE')) { define('RP_ATTENDEES_TABLE', $table_prefix . 'raidplan_raid_attendees'); }

$sort_order = array(
    0 => array('raid_date desc', 'raid_date'),
    1 => array('raid_name', 'raid_name desc'),
    2 => array('raid_note', 'raid_note desc'),
    3 => array('raid_value desc', 'raid_value')
);
 
$current_order = switch_order($sort_order);
$raid_date = ($_GET['showall']) ? 0 : time();
$total_raids = $db->query_first('SELECT count(*) FROM ' . RP_RAIDS_TABLE . ' WHERE raid_date>' . $raid_date);
$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value 
        FROM ' . RP_RAIDS_TABLE . '
		WHERE raid_date >' . $raid_date . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_rlimit'];

if (!($raids_result = $db->query($sql))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }

while ( $row = $db->fetch_record($raids_result) )
{
    $tpl->assign_block_vars('raids_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
        'U_VIEW_RAID' => 'addraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
        'NAME' => ( !empty($row['raid_name']) ) ? stripslashes($row['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'NOTE' => ( !empty($row['raid_note']) ) ? stripslashes($row['raid_note']) : '&nbsp;',
        'VALUE' => ( !empty($row['raid_value']) ) ? stripslashes($row['raid_value']) : '-1.00',
		'EDIT' => ( $user->check_auth('u_raidplan_update', false) ) ? '(<a href="editraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'] . '">' . $user->lang['edit_raidplan'] . '</a>)' : '',
	));
}

$tpl->assign_vars(array(
	'EQDKP_ROOT_PATH'	=> $eqdkp_root_path,
    'L_DATE'			=> $user->lang['date'],
    'L_NAME'			=> $user->lang['name'],
    'L_NOTE'			=> $user->lang['note'],
    'L_VALUE'			=> $user->lang['value'],
    
    'O_DATE'			=> $current_order['uri'][0],
    'O_NAME'			=> $current_order['uri'][1],
    'O_NOTE'			=> $current_order['uri'][2],
    'O_VALUE'			=> $current_order['uri'][3],
    
    'U_LIST_RAIDS' => 'listraids.php'.$SID.'&amp;',
    
    'START' => $start,
    'LISTRAIDS_FOOTCOUNT' => sprintf($user->lang['rp_listraids_footcount'], $total_raids, $user->data['user_rlimit'],
		'<a href="index.php'.$SID.'&amp;showall=true">'),
    'RAID_PAGINATION' => generate_pagination('listraids.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_raids, $user->data['user_rlimit'], $start))
);

$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['raidplan_title'], $eqdkp->config['guildtag'], $member_fname),
	'template_file' => 'admin/listraids.html',
	'template_path' => $pm->get_data('raidplan', 'template_path'),
	'display'       => true)
);
?>
