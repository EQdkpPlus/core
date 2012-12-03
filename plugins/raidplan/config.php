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
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . '/plugins/raidplan/includes/functions.php');

// The config is now saved in the database. Please go to the AdminPanel to edit
// the config. thank you :)

// Set table names
global $table_prefix;
if (!defined('RP_RAIDS_TABLE')) 			{ define('RP_RAIDS_TABLE', 			$table_prefix . 'raidplan_raids');}
if (!defined('RP_CLASSES_TABLE')) 		{ define('RP_CLASSES_TABLE', 		$table_prefix . 'raidplan_raid_classes');}
if (!defined('RP_ATTENDEES_TABLE')) 	{ define('RP_ATTENDEES_TABLE', 	$table_prefix . 'raidplan_raid_attendees');}
if (!defined('RP_WILDCARD_TABLE')) 		{ define('RP_WILDCARD_TABLE', 	$table_prefix . 'raidplan_wildcards');}
if (!defined('RP_CLASS_DIST_TABLE'))	{ define('RP_CLASS_DIST_TABLE', $table_prefix . 'raidplan_classes'); }
?>
