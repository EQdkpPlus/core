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
 * settings.php
 * 02.10.06 sz3
 ******************************/

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'bossprogress');
include_once ('../include/functions.php');
include_once ('../include/data.php');

$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_bossprogress_conf');
$rb = $pm->get_plugin('bossprogress');

if (!$pm->check(PLUGIN_INSTALLED, 'bossprogress')) {
	message_die('The Bossprogress plugin is not installed.');
}

global $table_prefix;
if (!defined('BP_CONFIG_TABLE')) {
	define('BP_CONFIG_TABLE', $table_prefix . 'bp_config');
}

// Saving
if ($_POST['bpsavebu']){
  	// global config
	UpdateConfig('zoneInfo', $_POST['zoneInfo']);
	UpdateConfig('bossInfo', $_POST['bossInfo']);
	UpdateConfig('dynZone', $_POST['dynloc']);
    	UpdateConfig('dynBoss', $_POST['dynboss']);
	UpdateConfig('zhiType', $_POST['zhiType']);
	UpdateConfig('detailBoss', $_POST['detailBoss']);
	UpdateConfig('showSB', $_POST['showSB']);
	UpdateConfig('noteDelim', $_POST['notedelim']);
	UpdateConfig('nameDelim', $_POST['namedelim']);
	UpdateConfig('tables', $_POST['tables']);
	foreach ($bzone as $zoneid => $bosslist){
		UpdateConfig('pz_'.$zoneid, $_POST['pz_'.$zoneid]);
		foreach ($bosslist as $bossid){
			UpdateConfig('pb_'.$bossid, $_POST['pb_'.$bossid]);
		}
	}
}

$sql = 'SELECT * FROM ' . BP_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) {
	message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql);
}

while ($roww = $db->fetch_record($settings_result)) {
	$row[$roww['config_name']] = $roww['config_value'];
}

$arrvals = array (
	'F_CONFIG' => 'settings.php' . $SID,
	'BP_DYNLOC' => ($row['dynZone'] == 1) ? ' checked="checked"' : '',
	'BP_DYNBOSS' => ($row['dynBoss'] == 1) ? ' checked="checked"' : '',
	'BP_DETAILBOSS' => ($row['detailBoss'] == 1) ? ' checked="checked"' : '',
	'BP_SHOWSB' => ($row['showSB'] == 1) ? ' checked="checked"' : '',
	'BP_NOTEDELIM' => $row['noteDelim'],
	'BP_NAMEDELIM' => $row['nameDelim'],
	'BP_TABLES' => $row['tables'],
	// Language
	'L_GENERAL' => $user->lang['opt_general'],
	'L_DYNLOC'      => $user->lang['opt_dynloc'],
	'L_DYNBOSS'    => $user->lang['opt_dynboss'],
	'L_ZHITYPE' => $user->lang['opt_zhiType'],
	'L_NOTEDELIM' => $user->lang['opt_delimRNO'],
    	'L_NAMEDELIM' => $user->lang['opt_delimRNA'],
    	'L_TABLES' => $user->lang['opt_tables'],
    	'L_PINFO' => $user->lang['opt_parseInfo'],
    	'L_SUBMIT' => "Save",
	'L_ZONEINFO' => $user->lang['opt_zoneInfo'],
	'L_BOSSINFO' => $user->lang['opt_bossInfo'],
	'L_DETAILBOSS' => $user->lang['opt_detailBoss'],
	'L_SHOWSB' => $user->lang['opt_showSB'],
	'L_RNAME' => $user->lang['rname'],
	'L_RNOTE' => $user->lang['rnote'],
	

	'L_JITTER' => $user->lang['zhi_jitter'],
	'L_BW' => $user->lang['zhi_bw'],
	'L_NONE' => $user->lang['zhi_none'],

	'ZHITYPE_SEL_JITTER'    => ( $row['zhiType'] == "0" ) ? ' selected="selected"' : '',
	'ZHITYPE_SEL_BW'    => ( $row['zhiType'] == "1" ) ? ' selected="selected"' : '',
	'ZHITYPE_SEL_NONE'    => ( $row['zhiType'] == "2" ) ? ' selected="selected"' : '',

	'ZONEINFO_SEL_RNAME'    => ( $row['zoneInfo'] == "rname" ) ? ' selected="selected"' : '',
	'ZONEINFO_SEL_RNOTE'    => ( $row['zoneInfo'] == "rnote" ) ? ' selected="selected"' : '',
	'BOSSINFO_SEL_RNAME'    => ( $row['bossInfo'] == "rname" ) ? ' selected="selected"' : '',
	'BOSSINFO_SEL_RNOTE'    => ( $row['bossInfo'] == "rnote" ) ? ' selected="selected"' : '',

	
);

foreach ($bzone as $zoneid => $bosslist){
	$arrvals['LZ_'.strtoupper($zoneid)] = $user->lang[$zoneid][long];
	$arrvals['PZ_'.strtoupper($zoneid)] = $row['pz_'.$zoneid];
	$arrvals['LPZ_'.strtoupper($zoneid)] = $user->lang['parse'].$user->lang[$zoneid][long];
	foreach ($bosslist as $bossid){
		$arrvals['LB_'.strtoupper($bossid)] = $user->lang['parse'].$user->lang[$bossid][long];
		$arrvals['PB_'.strtoupper($bossid)] = $row['pb_'.$bossid];
	}
}
//Output
$tpl->assign_vars($arrvals);


$eqdkp->set_vars(array (
	'page_title' => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['bp_conf_pagetitle'],
	'template_path' => $pm->get_data('bossprogress', 'template_path'),
	'template_file' => 'admin/settings.html', 'display' => true
	)
);
