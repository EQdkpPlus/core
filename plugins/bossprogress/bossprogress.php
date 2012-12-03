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
 * bossprogress.php
 * 02.10.06 sz3
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'bossprogress');

$eqdkp_root_path = './../../';

include_once ('offsets.php');
include_once ($eqdkp_root_path . 'common.php');

include_once ('include/functions.php');
include ('include/data.php');

global $user;

define ('MAXDATE', mktime (0,0,0,1,1,2015));
define ('MINDATE', mktime (0,0,0,1,1,2000));

$user->check_auth('u_bossprogress_view');

# Get configuration data from the database
####################################################
if (!defined('BP_CONFIG_TABLE')) { define('BP_CONFIG_TABLE', $table_prefix . 'bp_config'); }

$sql = 'SELECT * FROM ' . BP_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }

while($roww = $db->fetch_record($settings_result)) {
	$conf[$roww['config_name']] = $roww['config_value'];
}

$delim = array (
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

fetch_bzi();


# Output
####################################################
foreach ($bzone as $location => $bosses) {
	$loc_killed = 0;
	foreach ($kill_counter as $myboss => $count) {
		if (in_array($myboss, $bosses) && ($count > 0)) {
			$loc_killed++;
		}
	}
	
	if ((!$conf['dynZone']) or ($loc_killed > 0)) {
		$loc_completed = round($loc_killed / count($bosses) * 100);
		$bpout .= html_get_zhi($conf['zhiType'], $location, $loc_completed);

		if($conf['showSB'])
			$bpout .= html_get_zsb($location, $loc_killed, $loc_completed, count($bosses),$zone_first_visit_date[$location],$zone_last_visit_date[$location],$zone_visit_counter[$location]);

		$bi = 1; //row number 1/2
		$printed = 0;
		foreach ($kill_counter as $kill => $count) {
			if (in_array($kill, $bosses)) {
				if ((!$conf['dynBoss']) or ($count > 0)) {
					if($conf['detailBoss']){
						$bpout .= html_get_bossinfo($bi, $kill, $first_kill_date[$kill], $last_kill_date[$kill], $count);
					}else{
						$bpout .= html_get_bossinfo_simple($bi, $kill, $first_kill_date[$kill], $last_kill_date[$kill], $count);
					}
					$bi = 1 - $bi;
					$printed++;
				}
			}
		}
		if (($printed > 0) && ($printed % 2) && ($conf['detailBoss'])){
			$bpout .= '<td ></td><td width=60px></td></tr>';
		} 
		$bpout .= '<tr height="5"><td colspan="4"></td></tr>';
	}
	
}


# Assign Vars
####################################################
$tpl->assign_vars(array (
	'BOSSKILLVV' => $bpout
));

$eqdkp->set_vars(array (
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['is_title_bossprogress'],
	'template_path' => $pm->get_data('bossprogress', 'template_path'),
	'template_file' => 'bossprogress.html', 'display' => true)
	);
?>
