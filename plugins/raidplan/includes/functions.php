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

// get the config
global $table_prefix;
if (!defined('RP_CONFIG_TABLE')) { define('RP_CONFIG_TABLE', $table_prefix . 'raidplan_config'); }
$sql = 'SELECT * FROM ' . RP_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

// Load the kewl Variables:
$rp_show_ranks			    = ( $conf['rp_show_ranks'] == 1) ? true : false;
$rp_short_ranks			    = ( $conf['rp_short_rank'] == 1) ? true : false;
$rp_email_members_raid  = ( $conf['rp_send_email'] == 1) ? true : false;
$rp_show_recent_days    = $conf['rp_last_days'];
$rp_use_roll_system		  = ( $conf['rp_roll_systm'] == 1) ? true : false;
$rp_use_wildcard_system	= ( $conf['rp_wildcard'] == 1) ? true : false;
$rp_use_plugin_css_file	= ( $conf['rp_use_css'] == 1) ? true : false;
$rp_autojoin_secretword = $conf['rp_auto_hash'];
$rp_autojoin_path       = $conf['rp_auto_path'];

// the "Save my Data to the Database" :D
  function UpdateRPConfig($fieldname,$insertvalue)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix, $db;
        $sql = "UPDATE `".$table_prefix."raidplan_config` SET config_value='".strip_tags(htmlspecialchars($insertvalue))."' WHERE config_name='".$fieldname."';";
		    if ($db->query($sql)){
          return true;
        } else {
          return false;
        }
      }

function rp_stripGetDate($getdate)
{
  // format must be: 01012006 ddmmyyyy
  if (strlen($getdate) == 8){
    // Datum aufbereiten
        $getdat['day']    = substr($getdate,0,2); //|17|052006  -> 0,2
        $getdat['month']  = substr($getdate,2,2);  //17|05|2006 -> 2,2
        $getdat['year']   = substr($getdate,4,4); //1705|2006| -> 4,4
  } //end of strlen
    return $getdat;
}

// date difference
function rp_datediff($fromtime, $totime=''){
       if($totime=='')        $totime = time();
      
       if($fromtime>$totime){
           $tmp = $totime;
           $totime = $fromtime;
           $fromtime = $tmp;
       }
      
       $timediff = $totime-$fromtime;
       //check for leap years in the middle
       for($i=date('Y',$fromtime); $i<=date('Y',$totime); $i++){
           if ((($i%4 == 0) && ($i%100 != 0)) || ($i%400 == 0)) {
               $timediff -= 24*60*60;
           }
       }
       $remain = $timediff;
       $ret['years']    = intval($remain/(365*24*60*60));
       $remain            = $remain%(365*24*60*60);
       $ret['days']    = intval($remain/(24*60*60));
       $remain            = $remain%(24*60*60);


       $ret['hours']    = intval($remain/(60*60));
       $remain            = $remain%(60*60);
       $ret['minutes']    = intval($remain/60);
       $ret['seconds']    = $remain%60;
       return $ret;
   }
function convert_classname($classname)
	{
		switch ($classname) {
			# Englische Namen sind OK und müssen nicht umgewandelt werden.

			case "Druid"		: break;
			case "Warlock"		: break;
			case "Hunter"		: break;
			case "Warrior"		: break;
			case "Mage"			: break;
			case "Paladin"		: break;
			case "Priest"		: break;
			case "Shaman"		: break;
			case "Rogue"		: break;

			# Deutsche Klassennamen müssen umgewandelt werden.

			case "Druide"		: $classname = "Druid";		break;
			case "Hexenmeister"	: $classname = "Warlock";	break;
			case "Jäger"		: $classname = "Hunter";	break;
			case "Krieger"		: $classname = "Warrior";	break;
			case "Magier"		: $classname = "Mage";		break;
			case "Paladin"		: $classname = "Paladin";	break;
			case "Priester"		: $classname = "Priest";	break;
			case "Schurke"		: $classname = "Rogue";		break;
			case "Schamane"		: $classname = "Shaman";	break;
			case "Default"		: $classname = "UNKNOWN";	break;
			}
		return $classname;
        return;
    }

function convert_racesname($racesname)
	{
		switch ($racesname) {
			# Englische Namen sind OK und müssen nicht umgewandelt werden.

			case "Unknown"		: break;
			case "Gnome"		: break;
			case "Human"		: break;
			case "Dwarf"		: break;
			case "Night Elf"	: break;
			case "Troll"		: break;
			case "Undead"		: break;
			case "Orc"			: break;
			case "Tauren"		: break;

			# Deutsche Rassennamen müssen umgewandelt werden.

			case "Unbekannt"	: $racesname =  "Unknown";		break;
			case "Gnom"			: $racesname =  "Gnome";		break;
			case "Mensch"		: $racesname =  "Human";		break;
			case "Zwerg"		: $racesname =  "Dwarf";		break;
			case "Nachtelf"		: $racesname =  "Night Elf";	break;
			case "Troll"		: $racesname =  "Troll";		break;
			case "Untoter"		: $racesname =  "Undead";		break;
			case "Ork"			: $racesname =  "Orc";			break;
			case "Taure"		: $racesname =  "Tauren";		break;
			}
		return $racesname;
        return;
    } 
?>
