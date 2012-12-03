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
 **************************************************************************
 *                              lua_output.php
 *                            -------------------
 *   begin                : Saturday, Jan 16, 2005
 *   copyright            : (C) 2005 MR
 *   email                : maverick@gmx.com
 *
 *   $Id: mysql.php,v 1.16 2002/03/19 01:07:36 psotfx Exp $
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
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

// Variable Get
if ($_GET['raid_id'])
{
  $raid_id = $_GET['raid_id'];
}
elseif ($_GET['date'])
{
  // serach for raids on the given date. if no, try next raid!
  $time2bkewl = rp_stripGetDate($_GET['date']);
}else {
  echo $user->lang['rp_no_raidid'];
}

if ($_GET['automate'] == "true" && $_GET['auth'] == $rp_autojoin_secretword && isset($time2bkewl) )
{
  // here's the automate script!
  $time2fly   = mktime(0, 0, 0, $time2bkewl['month'], $time2bkewl['day'], $time2bkewl['year']);
  $time3fly   = mktime(23, 59, 59, $time2bkewl['month'], $time2bkewl['day'], $time2bkewl['year']);
  $sql = "SELECT raid_id FROM " . RP_RAIDS_TABLE . " WHERE raid_date >'" . $time2fly . "' AND raid_date < '".$time3fly."'";
	   $result = $db->query($sql);
	   $araid_id = $db->fetch_record($result);
	   //$raid_id = $araid_id['raid_id'];
	   CreateLuaFile($araid_id['raid_id'], false, true); //input/printlua/downloader
}

if ($_GET['show'] == "true" && isset($raid_id))
{
  MacroListing($raid_id); // Show the Macro List
  CreateLuaFile($raid_id, false, false); // input/printlua/downloader
}

// macro output
// non-queued first
function MacroListing($raid_id)
{
    global $db, $eqdkp_root_path, $user, $SID;
  // Set table names
  $text = $user->lang['rp_raid_id'].": ".$raid_id;
  $text .= "<br><b>".$user->lang['rp_Macro_output_Listing']."<br>";
  $text .= $user->lang['rp_nonqued_user']."</b><br><br>";

  $sql = 'SELECT raid_id, member_id
	   FROM ' . RP_ATTENDEES_TABLE . "
	   WHERE raid_id='" . $raid_id . "' AND confirmed ='1'";
	   $result = $db->query($sql);

  while($data = $db->fetch_record($result))
  {
	   $sql2 = "SELECT member_name, member_race_id, member_class_id FROM ".MEMBERS_TABLE." WHERE member_id=".$data['member_id']."";
	   $result2 = $db->query($sql2);
	   while($data2 = $db->fetch_record($result2))
	  {
		  $text .= "/invite ".$data2['member_name']."<br>";
	  }
  }

  $text .= "<b><br>".$user->lang['rp_queued_users']."</b><br><br>";
  $sql = 'SELECT raid_id, member_id
	   FROM ' . RP_ATTENDEES_TABLE . "
	   WHERE raid_id='" . $raid_id . "' AND confirmed ='0'";
	   $result = $db->query($sql);

  while($data = $db->fetch_record($result))
  {
	 $sql2 = "SELECT member_name, member_race_id, member_class_id FROM ".MEMBERS_TABLE." WHERE member_id=".$data['member_id']."";
	 $result2 = $db->query($sql2);
	 while($data2 = $db->fetch_record($result2))
	 {
		$text .= "/invite ".$data2['member_name']."<br>";
	 }
  }

  $text .= "<b><br>".$user->lang['rp_MacroListingComplete']."<br>";
  $text .= $user->lang['rp_copypaste_ig']."</b>";
  echo $text;
} // end MacroList Function

function CreateLuaFile($raid_id, $printlua, $downloader)
{
  global $db, $eqdkp_root_path, $user, $SID, $rp_autojoin_path;
  
if($printlua == false && $downloader == false){
  $text = '<br>'.$user->lang['rp_lua_created'].'. <br><br> <a href="'.$rp_autojoin_path.'AutoInvite.lua"><b> '.$user->lang['rp_download'].' </b></a> '.$user->lang['rp_dl_autoinv_add'];
  $file = fopen($rp_autojoin_path.'AutoInvite.lua','w');
}
if($downloader == false && $printlua == true)
{
  $text .= "<br><br><b>".$user->lang['rp_lua_output']."</b><br>";
}

$output = "

AutoInviteCompleteList = {";

$player_counter = 1;

$sql = 'SELECT raid_id, member_id, attendees_random, attendees_note
	   FROM ' . RP_ATTENDEES_TABLE . "
	   WHERE raid_id='" . $raid_id . "' AND confirmed ='1'";
	   $result = $db->query($sql);

while($data = $db->fetch_record($result))
{
	$sql2 = "SELECT member_name, member_race_id, member_class_id, member_level FROM ".MEMBERS_TABLE." WHERE member_id=".$data['member_id']."";
	$result2 = $db->query($sql2);
	while($data2 = $db->fetch_record($result2))
	{
		
		$output .= "\n\t\t[".$player_counter."] = {\n";
		$output .= "\t\t\t[\"name\"] = \"{$data2['member_name']}\",\n";
		$output .= "\t\t\t[\"inGroup\"] = true,\n";
		$output .= "\t\t\t[\"level\"] = {$data2['member_level']},\n";

		$output .= "\t\t\t[\"comment\"] = \"";
		$output .= 'W\195\188rfelergebnis: |CFFB700B7';
		$output .= "{$data['attendees_random']} |CFFFF50FF {$data['attendees_note']}\",\n";

		$sql3 = "SELECT class_name FROM ".CLASS_TABLE." WHERE class_id=".$data2['member_class_id']."";
		$result3 = $db->query($sql3);
		while($data3 = $db->fetch_record($result3))
		{
				$tempclass = strtoupper(convert_classname($data3['class_name']));
				$output .= "\t\t\t[\"eClass\"] = \"{$tempclass}\",\n";
		}
		$output .= "\t\t},\n";
		$player_counter = $player_counter + 1;
	}
}
$sql = 'SELECT raid_id, member_id, attendees_random, attendees_note
	   FROM ' . RP_ATTENDEES_TABLE . "
	   WHERE raid_id='" . $raid_id . "' AND confirmed ='0'";
	   $result = $db->query($sql);

while($data = $db->fetch_record($result))
{
	$sql2 = "SELECT member_name, member_race_id, member_class_id, member_level FROM ".MEMBERS_TABLE." WHERE member_id=".$data['member_id']."";
	$result2 = $db->query($sql2);
	while($data2 = $db->fetch_record($result2))
	{
		
		$output .= "\n\t\t[".$player_counter."] = {\n";
		$output .= "\t\t\t[\"name\"] = \"{$data2['member_name']}\",\n";
		$output .= "\t\t\t[\"inGroup\"] = false,\n";
		$output .= "\t\t\t[\"status\"] = \"unknown\",\n"; 
		$output .= "\t\t\t[\"group\"] = \"-\",\n";
		$output .= "\t\t\t[\"level\"] = {$data2['member_level']},\n";

		$output .= "\t\t\t[\"comment\"] = \"";
		$output .= 'W\195\188rfelergebnis: |CFFB700B7';
		$output .= "{$data['attendees_random']} |CFFFF50FF {$data['attendees_note']}\",\n";

		$sql3 = "SELECT class_name FROM ".CLASS_TABLE." WHERE class_id=".$data2['member_class_id']."";
		$result3 = $db->query($sql3);
		while($data3 = $db->fetch_record($result3))
		{
				$tempclass = strtoupper(convert_classname($data3['class_name']));
				$output .= "\t\t\t[\"eClass\"] = \"{$tempclass}\",\n";
		}
		$output .= "\t\t},";
		$player_counter = $player_counter + 1;
	}
}


$output .= "}";

$output .='
AutoInviteConfig = {
	["ifWisper"] = 1,
	["modActive"] = 0,
	}
AutoInviteSavedList = {}';
if($printlua == false && $downloader == false)
{
  fwrite($file,$output);//write to file
}

if($printlua == false && $downloader == false){
  echo $text;
}

if($printlua == true && $downloader == false){
  $output = str_replace("\n","<br>",$output);
  $output = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$output);
  echo $text;
  echo $output;
}
if($printlua == false && $downloader == true){
  //$output = str_replace("\n","<br>",$output);
  //$output = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$output);
  echo $output;
}

} // End of Lua Function
?>
