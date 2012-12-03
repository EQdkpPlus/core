<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * fix_memberstats.php
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_');

echo("Check first/last raid and member active status<br>");

echo("Get current values from member table...<br>");
$sql = "SELECT member_name, member_firstraid, member_lastraid, member_status FROM __members ORDER BY member_name ASC";
$result = $db->query($sql);

while($row = $db->fetch_record($result)){
  $old_stats[$row['member_name']]['firstraid'] = $row['member_firstraid'];
  $old_stats[$row['member_name']]['lastraid'] = $row['member_lastraid'];
  $old_stats[$row['member_name']]['status'] = $row['member_status'];
}

echo("Recalculate stats from raid table and compare to saved...<br>");
foreach($old_stats as $member_name => $stats){
  $sql = "SELECT max(raid_date) as last_raid, min(raid_date) as first_raid
            FROM __raids
            JOIN __raid_attendees ON __raids.raid_id = __raid_attendees.raid_id
          WHERE __raid_attendees.member_name = '$member_name'";
  $result = $db->query($sql);
  $row = $db->fetch_record($result);
  $new_stats[$member_name]['lastraid'] = (isset($row['last_raid'])) ? $row['last_raid'] : 0;
  $new_stats[$member_name]['firstraid'] = (isset($row['first_raid'])) ? $row['first_raid'] : 0;
}

if($eqdkp->config['inactive_period'] > 0){
  $min_active_raid_date = mktime(0, 0, 0, date('m'), (date('d')-$eqdkp->config['inactive_period']), date('Y'));
}else{
  $min_active_raid_date = 0;
}
foreach($old_stats as $member_name => $stats){
  $new_stats[$member_name]['status'] = ( ($min_active_raid_date == 0) || ($new_stats[$member_name]['lastraid'] > $min_active_raid_date)) ? 1 : 0;
}

echo("<table border=\"1\"><tr><th>Name</th><th>First Raid</th><th>Last Raid</th><th>Active</th><th>Updated</th></tr>");

foreach($old_stats as $member_name => $stats){
  echo("<tr><td>$member_name</td>");
  $upd_need = false;
  if($stats['firstraid'] == $new_stats[$member_name]['firstraid']){
    echo("<td>".$stats['firstraid']."</td>");
  }else{
    echo("<td>".$stats['firstraid']."//".$new_stats[$member_name]['firstraid']."</td>");
    $upd_need = true;
  }
  if($stats['lastraid'] == $new_stats[$member_name]['lastraid']){
    echo("<td>".$stats['lastraid']."</td>");
  }else{
    echo("<td>".$stats['lastraid']."//".$new_stats[$member_name]['lastraid']."</td>");
    $upd_need = true;
  }
  if($stats['status'] == $new_stats[$member_name]['status']){
    echo("<td>".$stats['status']."</td>");
  }else{
    echo("<td>".$stats['status']."//".$new_stats[$member_name]['status']."</td>");
    $upd_need = true;
  }
  if($upd_need){
    $sql = "UPDATE __members SET
              member_firstraid = '".$new_stats[$member_name]['firstraid']."',
              member_lastraid ='".$new_stats[$member_name]['lastraid']."',
              member_status = '".$new_stats[$member_name]['status']."'
            WHERE member_name = '".$member_name."'";
    $db->query($sql);
    echo("<td>yes</td>");
  }else{
    echo("<td>no</td>");
  }
  
  echo("</tr>");
}
echo("</table>");
die("Done!");

?>
