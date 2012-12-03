<?php
/******************************
 * EQdkp Bossprogress2
 * by sz3
 * 
 * Additional Credits should go to 
 * Corgan's bosscounter mod
 * Wallenium's ItemSpecials plugin
 * Magnus' raidprogress plugin
 * 
 * which all lend inspiration and/or code bits 
 *  
 * Copyright 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * functions.php
 * 02.10.06 sz3
 * modded by Corgan 07.11.06
 ******************************/

function _get_sql_data_string(){
global $tables;
	$sql = "";
	if (count($tables) == 0) {
		$sql = "SELECT  raid_name AS rname, raid_date AS rdate, raid_note AS rnote FROM " . RAIDS_TABLE . ";";
	} else {
		$bpinc = 0;
		foreach ($tables as $raidtable) {
			if ($bpinc == 0) {
				$sql .= "SELECT raid_name AS rname, raid_date AS rdate, raid_note AS rnote FROM " . $raidtable . "_raids";
				$bpinc++;
			} else {
				$sql .= " UNION ALL SELECT raid_name, raid_date, raid_note FROM " . $raidtable . "_raids";
			}

		}
		$sql .= ";";
	}
	return $sql;
}

function _fetch_bzi() {
global $pboss, $pzone, $tables, $bossInfo, $zoneInfo, $delim, $kill_counter, $first_kill_date, $last_kill_date, $zone_visit_counter, $zone_first_visit_date, $zone_last_visit_date;
	#Get data from the raids tables
	##################################################
	$sql = _get_sql_data_string();	
	$result = mysql_query($sql) or die(mysql_error());

	while ($row = mysql_fetch_assoc($result)) {
		# Zoneinfo
		#########################################
		if ($delim[$zoneInfo] != "//"){
			$zone_element = preg_split($delim[$zoneInfo], $row[$zoneInfo], -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$zone_element = array($row[$zoneInfo]);
		}

		foreach ($zone_element as $raid) {
			foreach ($pzone as $zone => $parseList) {
				if (in_array(trim($raid), $parseList)) {
					//$zone_visit_counter[$zone]++;
					if ($zone_first_visit_date[$zone] > $row["rdate"]) {
						$zone_first_visit_date[$zone] = $row["rdate"];
					}
					if ($zone_last_visit_date[$zone] < $row["rdate"]) {
						$zone_last_visit_date[$zone] = $row["rdate"];
						//if (!strcmp(strftime("%D", $zone_last_visit_date[$zone]), strftime("%D", $row["rdate"]))){
						//	$zone_visit_counter[$zone]++;
						//}
					}
				}
			}
		}

		# Bossinfo
		####################################
		if ($delim[$bossInfo] != "//"){
			$boss_element = preg_split($delim[$bossInfo], $row[$bossInfo], -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$boss_element = array($row[$bossInfo]);
		}
		foreach ($boss_element as $raid) {
			foreach ($pboss as $boss => $parseList) {
				if (in_array(trim($raid), $parseList)) {
					$kill_counter[$boss]++;
					if ($first_kill_date[$boss] > $row["rdate"]) {
						$first_kill_date[$boss] = $row["rdate"];
					}
					if ($last_kill_date[$boss] < $row["rdate"]) {
						$last_kill_date[$boss] = $row["rdate"];
					}
				}
			}
		}
	}
	mysql_free_result($result);
}

// header zeile
function _html_get_zsb($location, $loc_killed, $loc_completed, $totalbosscount, $zfvd, $zlvd, $zvc){
global $user;
return '<tr><th colspan=2>'.$user->lang[$location]['long'].'</th></tr>'."\n";
}


function _html_get_bossinfo($rowid, $bossname, $firstkill, $lastkill, $count) {
global $eboss, $dateFormat, $bosslink, $user,$conf_plus;

	$bossinfo  = '<tr class="row' . ($rowid +1) . '">';
	$bossinfo .= '<td align="left">';
	if ($conf_plus['pk_bossloot'] == 1)
  {
		$bossinfo .= get_linked_raid_note(stripslashes($user->lang[$bossname]['short']), stripslashes($user->lang[$bossname]['long']) , $bossname);
		
	}
	else
	{
		$bossinfo .= '<a href="' . $user->lang['baseurl'] . $user->lang['iboss'][$bossname] . '" target="bossinfo">' . $user->lang[$bossname]['short'] . '</a>';
	}
	$bossinfo .='</td><td>';	
	$bossinfo .=  $count;
	$bossinfo .= '</td></tr>' . "\n";
	
	return $bossinfo;
}


?>
