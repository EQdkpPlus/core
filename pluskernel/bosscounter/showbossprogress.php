<?php
/******************************
 * EQdkp Bosscounter 2.1
 * Copyright 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * bosscounter.php
 * 28.05.06 Corgan
 * 31.05.06 Corgan 2.1
 * 07.11.06 Corgan change to fetch Data from Bossprogress
 *********************************************************/

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}

global $user , $eqdkp;
include_once(dirname(__FILE__).'/include/functions.php');
include_once(dirname(__FILE__).'/include/data.php');

#raid count:
	###################

		$sqlabfrage ="select count(*) as alle from ".RAIDS_TABLE.";";
		$result = mysql_query("$sqlabfrage");
		$data = mysql_fetch_object($result);
		$allraids = $data->alle;

		$member_results = mysql_query("SELECT * FROM ".MEMBERS_TABLE.";") or die(mysql_error());
		while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
		{
			$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
			$total_points+=$player_dkps;
		}

		$total_pointsset = $total_points ;

		// Get total items
		$item_results = mysql_query("SELECT * FROM ".ITEMS_TABLE.";") or die(mysql_error());
		$total_items = mysql_num_rows($item_results);
		$setitems = $total_items ;

		// Get total players
		$member_results = mysql_query("SELECT * FROM ".MEMBERS_TABLE.";") or die(mysql_error());
		$total_players = mysql_num_rows($member_results);

		if ($table_prefixNS <> "")
		{
			$member_results = mysql_query("SELECT * FROM ".$table_prefixNS."members") or die(mysql_error());
			while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
			{
				$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
				$total_pointsns+=$player_dkps;
			}

			$total_points = $total_pointsset + $total_pointsns ;

			// Get total items
			$item_results = mysql_query("SELECT * FROM ".$table_prefixNS."items") or die(mysql_error());
			$Nonsetitems = mysql_num_rows($item_results);
			$total_items = $setitems  + $Nonsetitems;
		}

###############


# Get configuration data from the database
####################################################
if (!defined('BP_CONFIG_TABLE')) 
{ 
	define('BP_CONFIG_TABLE', $table_prefix . 'bp_config'); 
}

$sql = 'SELECT * FROM ' . BP_CONFIG_TABLE;
if(!($settings_result = $db->query($sql))) 
{ 
	message_die('Could not Bossprogress configuration data', '', __FILE__, __LINE__, $sql); 
}

while($roww = $db->fetch_record($settings_result)) 
{
	$conf[$roww['config_name']] = $roww['config_value'];
}

$delim = array 
(
	'rnote' => '/'.$conf['noteDelim'].'/',
	'rname' => '/'.$conf['nameDelim'].'/'
);

$tables = array();
if($conf['tables'] != '')
	$tables = explode(", ", $conf['tables']);

$pzone = array();
$pboss = array();

foreach ($bzone as $zoneid => $bosslist){
	$pzone[$zoneid] = preg_split("/\', \'/", trim($conf['pz_'.$zoneid], "\' "));
	foreach ($bosslist as $bossid){
		$pboss[$bossid] = preg_split("/\', \'/", trim($conf['pb_'.$bossid], "\' "));
	}
}

# Create and populate the data arrays for zones/bosses
####################################################
foreach ($pboss as $boss => $parselist) {
	$kill_counter[$boss] = 0 + $bo_kc[$boss];
	(isset ($bo_fkd[$boss])) ? $first_kill_date[$boss] = $bo_fkd[$boss] : $first_kill_date[$boss] = MAXDATE;
	(isset ($bo_lkd[$boss])) ? $last_kill_date[$boss] = $bo_lkd[$boss] : $last_kill_date[$boss] = MINDATE;
}

foreach ($pzone as $zone => $parselist) {
    $zone_visit_counter[$zone] = 0 + $zo_vc[$zone];
    (isset ($zo_fvd[$zone])) ? $zone_first_visit_date[$zone] = $zo_fvd[$zone] : $zone_first_visit_date[$zone] = MAXDATE;
    (isset ($zo_lvd[$zone])) ? $zone_last_visit_date[$zone] = $zo_lvd[$zone] : $zone_last_visit_date[$zone] = MINDATE;
}

$bossInfo = $conf['bossInfo'];
$zoneInfo = $conf['zoneInfo'];

_fetch_bzi();


# Output
####################################################

	$_bpout = '<table width=100% class="borderless" cellspacing="0" cellpadding="2">
					<tr><th class="smalltitle" align="center">Bosscounter</th></tr>
					</table>'."\n";;
	
	$_bpout .= '<table width=100% class="borderless" cellspacing="0" cellpadding="2">'."\n";
	$_bpout .= '<tr><td class="row1">'.$user->lang['bosscount_raids'].'</td><td class="row1">'. $allraids. '</td></tr>'."\n";
	$_bpout .= '<tr><td class="row2">'.$user->lang['bosscount_player'].'</td><td class="row2">'. $total_players. '</td></tr>'."\n";

	if($Nonsetitems > 0)
	{
			$_bpout .= '<tr><td class="row1">Setitems:</td><td class="row1">'. $setitems. '</td></tr>'."\n";
			$_bpout .= '<tr><td class="row2">Nonsetitems:</td><td class="row2">'. $Nonsetitems. '</td></tr>'."\n";
	}
	else
	{
		$_bpout .= '<tr><td class="row1">'.$user->lang['bosscount_items'].'</td><td class="row1">'. $total_items. '</td></tr>'."\n";
	}
	$_bpout .= '</table>'."\n"; 


	$_bpout .= '<table width=100% cellpadding=2 cellspacing=0 border=0 align=center>'."\n";

foreach ($bzone as $location => $bosses) 
{
	$loc_killed = 0;
	foreach ($kill_counter as $myboss => $count) {
		if (in_array($myboss, $bosses) && ($count > 0)) {
			$loc_killed++;
		}
	}
	
	if ((!$conf['dynZone']) or ($loc_killed > 0)) 
	{
		$loc_completed = round($loc_killed / count($bosses) * 100);

		if($conf['showSB'])
			$_bpout .= _html_get_zsb($location, $loc_killed, $loc_completed, count($bosses),$zone_first_visit_date[$location],$zone_last_visit_date[$location],$zone_visit_counter[$location]);

		$bi = 1; //row number 1/2
		$printed = 0;
		foreach ($kill_counter as $kill => $count) 
		{
			if (in_array($kill, $bosses)) 
			{
				if ((!$conf['dynBoss']) or ($count > 0)) 
				{
					if($conf['detailBoss'])
					{
						$_bpout .= _html_get_bossinfo($bi, $kill, $first_kill_date[$kill], $last_kill_date[$kill], $count);
					}else{
						$_bpout .= _html_get_bossinfo_simple($bi, $kill, $first_kill_date[$kill], $last_kill_date[$kill], $count);
					}
					$bi = 1 - $bi;
					$printed++;
				}
			}
		} #end for each	
	}
}# end for each

$_bpout .= '</table>'."\n";
$tpl->assign_var('BOSSKILLV',$_bpout);
	
?>
