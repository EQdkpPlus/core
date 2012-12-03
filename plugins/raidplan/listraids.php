<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * Continued 2006 by Urox and Wallenium 
 * ------------------
 * config.php
 * Began: Tue June 1, 2006
 * Changed: Tue June 1, 2006
 * 
 ******************************/
define('EQDKP_INC', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../';
include_once('config.php');


$user->check_auth('u_raidplan_list');

if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }
$raidplan = $pm->get_plugin('raidplan');

global $table_prefix;
global $db, $eqdkp, $user, $tpl, $pm;
global $SID, $rp_use_plugin_css_file;

$extra_css = "";

//set local
setlocale (LC_TIME, $user->lang['rp_local_format']);

$sort_order = array(
    0 => array('raid_date desc', 'raid_date'),
    1 => array('raid_name', 'raid_name desc'),
    2 => array('raid_note', 'raid_note desc'),
    3 => array('raid_value desc', 'raid_value'),
	4 => array('raid_date_subscription', 'raid_date_subscription desc')
);
 
$current_order = switch_order($sort_order);
$total_raids = $db->query_first('SELECT count(*) FROM ' . RP_RAIDS_TABLE . ' WHERE raid_date>' . time());
$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value, raid_attendees, raid_date_subscription, raid_date_invite
        FROM ' . RP_RAIDS_TABLE . '
		WHERE raid_date>' . time() . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_rlimit'];

if (!($raids_result = $db->query($sql))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }

while ( $row = $db->fetch_record($raids_result) )
{
	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=1 AND confirmed=0 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_signin = $db->fetch_record($count_result);

	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=1 AND confirmed=1 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_confirmed = $db->fetch_record($count_result);

	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=2 AND confirmed=0 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_signout = $db->fetch_record($count_result);

	if ($user->data['user_id'])
	{
			$confirmstatus = '';
			$sql1 = 'SELECT member_id
	   				FROM ' . MEMBER_USER_TABLE . "
	   				WHERE user_id='" . $user->data['user_id'] . "'";
	   		$result1 = $db->query($sql1);

			while($data = $db->fetch_record($result1))
			{
				//$user_subscribed = $data['member_id'];
				$sql2 = "SELECT attendees_subscribed, confirmed FROM ".RP_ATTENDEES_TABLE." WHERE member_id=".$data['member_id']." AND raid_id=" . $row['raid_id'] . "";
				$result2 = $db->query($sql2);
				while($data2 = $db->fetch_record($result2))
				{
					if($data2['attendees_subscribed'] == 1) {$confirmstatus = '<img src="./images/notconfirmed.gif"/>';}
					if($data2['confirmed'] == 1) 			{$confirmstatus = '<img src="./images/confirmed.gif"/>';}
					if($data2['attendees_subscribed'] == 2) {$confirmstatus = '<img src="./images/away.gif"/>';}
				}
			}
		if($confirmstatus == '') {$confirmstatus = '<img src="./images/unconfirmed.gif"/>';}
	}
    
    // check the status of raid
      // get the time of 24h later
      $actualtime = time();
      $timeh = $actualtime + (60*60*24);
      
      // time to signin
      $datediff_stat = rp_datediff($actualtime, $row['raid_date_subscription']);
      $status_diff = $datediff_stat['days']."<b>".$user->lang['rp_status_day']."</b> ".
                     $datediff_stat['hours']."<b>".$user->lang['rp_status_hours']."</b> ".
                     $datediff_stat['minutes']."<b>".$user->lang['rp_status_minutes']."</b> ";
      
    //check when the event is
    if ($row['raid_date_subscription'] > $timeh){
      $raidstatusicon = "<img src='images/status_green.gif'>";
      $overlibmaintxt =  $user->lang['rp_status_signintime']."<br>".$status_diff;
    } elseif ($row['raid_date_subscription'] < $timeh and $row['raid_date_subscription'] > $actualtime){
      $raidstatusicon = "<img src='images/status_yellow.gif'>";
      $overlibmaintxt =  $user->lang['rp_status_signintime']."<br>".$status_diff;
    }else{
      $raidstatusicon = "<img src='images/status_red.gif'>";
      $overlibmaintxt =  $user->lang['rp_status_closed'];
    }
    
	   $overlibheader = $user->lang['rp_status_header'];
	
    $tpl->assign_block_vars('raids_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
        'INVITE' => strftime($user->lang['rp_time_short'], $row['raid_date_invite']),
        'START'	 => strftime($user->lang['rp_time_short'], $row['raid_date']),
        'DAY'		 => strftime($user->lang['rp_day_format'], $row['raid_date']),
        'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
        'NAME' => ( !empty($row['raid_name']) ) ? stripslashes($row['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'COUNT' => '<font class="signedin">' . $count_signin[0] . '</font>/<font class="approved">' . $count_confirmed[0] . '</font>/<font class="signedout">' .$count_signout[0].'</font>/<font class="need">' .$row['raid_attendees']. '</font>',
        'NOTE' => ( !empty($row['raid_note']) ) ? stripslashes($row['raid_note']) : '&nbsp;',
        'VALUE' => ( !empty($row['raid_value']) ) ? stripslashes($row['raid_value']) : '-1.00',
        'CONFIRMED' => $confirmstatus,
        'RAIDSTATUS_ICON' => $raidstatusicon,
        'RAIDSTATUS_HEADER' => $overlibheader,
        'RAIDSTATUS_TXT' => $overlibmaintxt,
				'EDIT' => ( $user->check_auth('a_raid_upd', false) ) ? '<a href="admin/addraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'] . '"><img src="./../../images/glyphs/edit.gif" width="16" height="16" alt="Edit" /></a>' : '',
	));
}

// recent raids
$raidbtime = time() - (60*60*24*$rp_show_recent_days);
$total_recent_raids = $db->query_first('SELECT count(*) FROM ' . RP_RAIDS_TABLE . ' WHERE raid_date>' . $raidbtime . ' AND raid_date<' . time() . ' LIMIT '.$start.','.$user->data['user_rlimit']);
$sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value, raid_attendees
        FROM (' . RP_RAIDS_TABLE . ')
		    WHERE raid_date>' . $raidbtime . ' AND raid_date<' . time() . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_rlimit'];

if (!($raids_result = $db->query($sql))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }

while ( $row = $db->fetch_record($raids_result) )
{
	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=1 AND confirmed=0 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_signin = $db->fetch_record($count_result);

	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=1 AND confirmed=1 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_confirmed = $db->fetch_record($count_result);
	
	$sql_count = "SELECT count(*) FROM ". RP_ATTENDEES_TABLE ." WHERE attendees_subscribed=2 AND confirmed=0 AND raid_id=" . $row['raid_id'];
	if (!($count_result = $db->query($sql_count))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
	$count_signedout = $db->fetch_record($count_result);
	
	    $raidstatusicon_o = "<img src='images/status_red.gif'>";
      $overlibmaintxt_o =  $user->lang['rp_status_closed'];
	
    $tpl->assign_block_vars('recent_raids', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
        'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
        'NAME' => ( !empty($row['raid_name']) ) ? stripslashes($row['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'COUNT' => '<font class="signedin">' . $count_signin[0] . '</font>/<font class="approved">' . $count_confirmed[0] . '</font>/<font class="signedout">' .$count_signedout[0].'</font>/<font class="need">' .$row['raid_attendees']. '</font>',
        'NOTE' => ( !empty($row['raid_note']) ) ? stripslashes($row['raid_note']) : '&nbsp;',
        'VALUE' => ( !empty($row['raid_value']) ) ? stripslashes($row['raid_value']) : '-1.00',
        'RAIDSTATUS_ICON' => $raidstatusicon_o,
        'RAIDSTATUS_HEADER' => $overlibheader,
        'RAIDSTATUS_TXT' => $overlibmaintxt_o,
				'EDIT' => ( $user->check_auth('a_raid_upd', false) ) ? '<a href="admin/addraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'] . '"><img src="./../../images/glyphs/edit.gif" width="16" height="16" alt="Edit" /></a>' : '',
	));
}

if ($rp_use_plugin_css_file)
		{
			$extra_css = "";
			$extra_css_file = $eqdkp_root_path . $pm->get_data('raidplan', 'template_path') . $user->style['template_path'] . "/stylesheet.css";
			
			if (file_exists($extra_css_file))
			{
				$filehandle = fopen($extra_css_file, "r");
				while (!feof($filehandle)) {
					$extra_css .= fgets($filehandle);
				}
				fclose ($filehandle);
			}
		}

$tpl->assign_vars(array(
    'L_DATE' 		=> $user->lang['date'],
    'L_INVITE' 	=> $user->lang['rp_invite'],
    'L_START' 	=> $user->lang['rp_start'],
    'L_DAY' 		=> $user->lang['rp_day'],
    'L_NAME' 		=> $user->lang['name'],
    'L_NOTE' 		=> $user->lang['note'],
    'L_VALUE' 	=> $user->lang['value'],
    'L_STATUS' 	=> $user->lang['rp_status'],
	'L_APPROVED' 	=> $user->lang['rp_confirmed'],
	'L_SIGNIN' 	=> $user->lang['rp_signed'],
	'L_SIGNOUT' 	=> $user->lang['rp_unsigned'],
	'L_AWAY' 	=> $user->lang['rp_notavail'],
	'L_NEED' 	=> $user->lang['rp_needed'],
	'L_SIGNUP_OVER' => $user->lang['rp_signup_over'],
	'L_SIGNUP_POSS' => $user->lang['rp_signup_possible'],
	'L_SIGNUP_24H' => $user->lang['rp_signup_24h'],
	'L_VERSION'	=> $pm->get_data('raidplan', 'version'),
    'L_CURRENT_RAID' => $user->lang['rp_current_raid'],
    'L_RECENT_RAID' => $user->lang['rp_recent_raid'],
    
    'O_DATE' 		=> $current_order['uri'][0],
    'O_NAME' 		=> $current_order['uri'][1],
    'O_NOTE' 		=> $current_order['uri'][2],
    'O_VALUE' 	=> $current_order['uri'][3],
    
    'U_LIST_RAIDS' => 'listraids.php'.$SID.'&amp;',
    
    'START' => $start,
    'LISTRAIDS_FOOTCOUNT' => sprintf($user->lang['listraids_footcount'], $total_raids, $user->data['user_rlimit']),
    'LISTRECENTRAIDS_FOOTCOUNT' => sprintf($user->lang['rp_listrecentraids_footcount'], $total_recent_raids, strftime('%d',(time()-$raidbtime-86400))),
    'RAID_PAGINATION' => generate_pagination('listraids.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_raids, $user->data['user_rlimit'], $start))
);

if ($user->check_auth('u_raidplan_add', false)) {
	$tpl->assign_vars(array(
		'L_ADD_RAID' => $user->lang['add_raid'],
		'F_ADD_RAID' => "editraid.php",
		'S_ADD_RAID' => true));
}

$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rp_raidplaner'],
	'extra_css'		=> $extra_css,
	'template_file' => 'listraids.html',
	'template_path' => $pm->get_data('raidplan', 'template_path'),
	'display'       => true)
);
?>
