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


// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }

// Check user permission
$user->check_auth('u_raidplan_view');

//if (!$this->url_id) { message_die($user->lang['error_invalid_raid_provided']); }

// Get the plugin
$raidplan = $pm->get_plugin(PLUGIN);




	function create_raidplan_tables($step)
	{
		global $table_prefix;
		$sql = "";
		switch ($step)
		{
			case "step1a":
				$sql = "DROP TABLE IF EXISTS `" . $table_prefix . "raidplan_raids`";
				break;
			case "step1b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raids (
						raid_id mediumint(8) unsigned NOT NULL auto_increment,
						raid_name varchar(255) default NULL,
						raid_date int(11) NOT NULL default '0',
						raid_date_invite int(11) NOT NULL default '0',
						raid_date_subscription int(11) NOT NULL default '0',
						raid_note text default NULL,
						raid_value float(6,2) default NULL,
						raid_attendees mediumint(8) NOT NULL default '0',
						raid_added_by varchar(30) NOT NULL default '',
						raid_updated_by varchar(30) default NULL,
						PRIMARY KEY  (raid_id)
						) ";
				break;
			case "step2a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_raid_classes";
				break;
			case "step2b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raid_classes (
						raid_id mediumint(8) unsigned NOT NULL default '0',
						class_name varchar(50) default NULL,
						class_count smallint(3) unsigned NOT NULL default '0',
						KEY raid_id (raid_id)
						)";
				break;
			case "step3a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_raid_attendees";
				break;
			case "step3b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_raid_attendees (
						raid_id mediumint(8) unsigned NOT NULL default '0',
						member_id mediumint(5) NOT NULL default '0',
						attendees_subscribed tinyint(1) NOT NULL default '0',
						attendees_note text default NULL,
						attendees_signup_time int(11) NOT NULL default '0',
						confirmed tinyint(1) NOT NULL default '0',
						attendees_random mediumint(4) NOT NULL default '0',
						KEY raid_id (raid_id),
						KEY member_name (member_id))";
				break;
			case "step4a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_wildcards";
				break;
			case "step4b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_wildcards (
						user_name varchar(25) default NULL,
						wildcard tinyint(1) NOT NULL default '0',
						KEY user_name (user_name))";
				break;
			case "steb5a":
				$sql = "DROP TABLE IF EXISTS " . $table_prefix . "raidplan_classes";
				break;
			case "step5b":
				$sql = "CREATE TABLE IF NOT EXISTS " . $table_prefix . "raidplan_classes (
						event_name varchar(50) NOT NULL default '',
						class_name varchar(50) NOT NULL default '',
						class_count smallint(3) NOT NULL default '0')";
				break;
		}
		return $sql;
	}


        // Define installation
        // -----------------------------------------------------
		$steps=5;
		for ($i = 1; $i <= $steps; $i++)
		{
			add_sql(SQL_INSTALL, $this->create_raidplan_tables("step".$i."a"));
			//$this->add_sql(SQL_INSTALL, $this->create_raidplan_tables("step".$i."b"));
		}	

?>
