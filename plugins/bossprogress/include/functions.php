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
 ******************************/


// the "Save my Data to the Database" :D (Wallenium)
function UpdateConfig($fieldname,$insertvalue) {
global $eqdkp_root_path, $user, $SID, $table_prefix, $db;
    	$sql = "UPDATE `".$table_prefix."bp_config` SET config_value='".strip_tags(htmlspecialchars($insertvalue))."' WHERE config_name='".$fieldname."';";
	if ($db->query($sql)){
    		return true;
    	} else {
    		return false;
    	}
}

function get_sql_data_string(){
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

function fetch_bzi() {
global $pboss, $pzone, $tables, $bossInfo, $zoneInfo, $delim, $kill_counter, $first_kill_date, $last_kill_date, $zone_visit_counter, $zone_first_visit_date, $zone_last_visit_date;
	#Get data from the raids tables
	##################################################
	$sql = get_sql_data_string();	
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

// 0 = jitter, 1 = sw, 2 = none
function html_get_zhi($zhi_type, $location, $loc_completed){
global $user;
	switch ($zhi_type) {
	case 0:
		$eimg = 'images/zones/sz3/' . $user->lang['lang'] . '/normal/' . $location . '.jpg';
		$simg = 'images/zones/sz3/' . $user->lang['lang'] . '/photo/' . $location . '.jpg';
		return '<tr width="800"><td colspan="4" class="row1"><dl><dd style="background: url(' . $simg . ')"><div style="width:' . $loc_completed . '%; background: url(' . $eimg . ');"></div></dd></dl></td></tr>';
		break;
	case 1:
  		$eimg = 'images/zones/sz3/' . $user->lang['lang'] . '/normal/' . $location . '.jpg';
		$simg = 'images/zones/sz3/' . $user->lang['lang'] . '/sw/' . $location . '.jpg';
		return '<tr width="800"><td colspan="4" class="row1"><dl><dd style="background: url(' . $simg . ')"><div style="width:' . $loc_completed . '%; background: url(' . $eimg . ');"></div></dd></dl></td></tr>';
		break;
	case 2:
		$eimg = 'images/zones/sz3/' . $user->lang['lang'] . '/normal/' . $location . '.jpg';
 		return '<tr width="800"><td colspan="4" class="row1"><img src="' .$eimg.'" alt="' . $location . '" /></td></tr>';
	 	break;
	default:
		$eimg = 'images/zones/sz3/' . $user->lang['lang'] . '/normal/' . $location . '.jpg';
		$simg = 'images/zones/sz3/' . $user->lang['lang'] . '/photo/' . $location . '.jpg';
		return '<tr width="800"><td colspan="4" class="row1"><dl><dd style="background: url(' . $simg . ')"><div style="width:' . $loc_completed . '%; background: url(' . $eimg . ');"></div></dd></dl></td></tr>';
		break;
	}

}

/*function html_get_zhi($zhi_type, $location, $loc_completed){
global $user;
	$eimg = 'images/zones/sz3/' . $user->lang['lang'] . '/normal/' . $location . '.jpg';
	$simg = 'images/zones/sz3/' . $user->lang['lang'] . '/sw/' . $location . '.jpg';
	return '<tr width="800"><td colspan="4" class="row1"><dl><dd style="background: url(' . $simg . ')"><div style="width:' . $loc_completed . '%; background: url(' . $eimg . ');"></div></dd></dl></td></tr>';
}
*/
function html_get_zsb($location, $loc_killed, $loc_completed, $totalbosscount, $zfvd, $zlvd, $zvc){
global $user;
	($loc_completed == '100') ? $bar_class = "positive" : $bar_class = "negative";
	//return '<tr><td align="center" colspan="4" class="row2"><span class="' . $bar_class . '">' . $user->lang[$location] . ' -- ' . $user->lang['firstvisit'] . datetx($zfvd) . ' -- ' . $user->lang['lastvisit'] . datetx($zlvd) . ' -- ' . $user->lang['zonevisitcount'] . $zvc . ' -- ' . $user->lang['status'] . $loc_killed . '/' . $totalbosscount. ' (' . $loc_completed . '%)</span></td></tr>';
	return '<tr><td align="center" colspan="4" class="row2"><span class="' . $bar_class . '">' . $user->lang[$location]['long'] . ' -- ' . $user->lang['firstvisit'] . datetx($zfvd) . ' -- ' . $user->lang['lastvisit'] . datetx($zlvd) . ' -- ' . $user->lang['status'] . $loc_killed . '/' . $totalbosscount. ' (' . $loc_completed . '%)</span></td></tr>';
}


function html_get_boss_image_td($bossname, $bosscount) {
	if ($bosscount > 0) {
		if (file_exists("images/bosses/" . $bossname . ".gif")) {
			return '<td width="60" height="60" align="left"><img src="' . "images/bosses/" . $bossname . '.gif" height="60" border="0" alt="' . $bossname . '" /></td>';
		} else {
			return '<td width="60" height="60" align="left"><img src="' . "images/bosses/" . 'checkmark.gif height="60" border="0" alt= "' . $bossname . '" /></td>';
		}
	} else {
		if (file_exists("images/bosses/" . $bossname . ".gif")) {
			return '<td width="60" height="60" align="left"><img src="' . "images/bosses/" . $bossname . '_b.gif" height="60" border="0" alt="' . $bossname . '" /></td>';
		} else {
			return '<td width="60" height="60" align="left"><img src="' . "images/bosses/" . 'checkmark.gif" height="60" border="0" alt="' . $bossname . '" /></td>';
		}
	}
}

function html_get_bossinfo($rowid, $bossname, $firstkill, $lastkill, $count) {
global $eboss, $dateFormat, $bosslink, $user;
	$firstkill_date = datetx($firstkill);
	$lastkill_date = datetx($lastkill);
	if (($rowid % 2)) {
		$bossinfo = '<tr class="row' . ($rowid +1) . '">';
		$bossinfo .= html_get_boss_image_td($bossname, $count);
		$bossinfo .= '<td align="left">Name: <a href="' . $user->lang['baseurl'] . $user->lang['iboss'][$bossname] . '" target="bossinfo">' . $user->lang[$bossname]['long'] . '</a><br />';
		$bossinfo .= $user->lang['firstkill'] . $firstkill_date . '<br />';
		$bossinfo .= $user->lang['lastkill'] . $lastkill_date . '<br />';
		$bossinfo .= $user->lang['bosskillcount'] . $count;
		$bossinfo .= '</td>' . "\n";
	} else {
		$bossinfo .= '<td align="right">Name: <a href="' . $user->lang['baseurl'] . $user->lang['iboss'][$bossname] . '" target="bossinfo">' . $user->lang[$bossname]['long'] . '</a><br />';
		$bossinfo .= $user->lang['firstkill'] . $firstkill_date . '<br />';
		$bossinfo .= $user->lang['lastkill'] . $lastkill_date . '<br />';
		$bossinfo .= $user->lang['bosskillcount'] . $count . '</td>';
		$bossinfo .= html_get_boss_image_td($bossname, $count);
		$bossinfo .= '</tr>' . "\n";
	}

	return $bossinfo;
}

function html_get_bossinfo_simple($rowid, $bossname, $firstkill, $lastkill, $count) {
global $eboss, $iboss, $dateFormat, $bosslink, $user;
	$firstkill_date = datetx($firstkill);
	$lastkill_date = datetx($lastkill);
	$bossinfo = '<tr class="row' . ($rowid +1) . '">';
	$bossinfo .= '<td align="left">Name: <a href="' . $user->lang['baseurl'] . $user->lang['iboss'][$bossname] . '" target="bossinfo">' . $user->lang[$bossname] . '</a></td>';
	$bossinfo .= '<td align="left">' . $user->lang['firstkill'] . $firstkill_date . '</td>';
	$bossinfo .= '<td align="left">' . $user->lang['lastkill'] . $lastkill_date . '</td>';
	$bossinfo .= '<td align="left">' . $user->lang['bosskillcount'] . $count . '</td></tr>';
	return $bossinfo;
}


function datetx($date) {
global $user;
	if (($date == MAXDATE) or ($date == MINDATE)) {
		return $user->lang['never'];
	} else {
		return strftime($user->lang['dateFormat'], $date);
	}
}
?>
