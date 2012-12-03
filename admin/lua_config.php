<?php
/******************************
 * EQdkp
 * Copyright 2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lua_config.php
 * Began: Mon Sept 19 2005
 *
 * $Id$
 ******************************/
 
// Sets the minimum Item Quality of Items to get parsed (4 = Epic, 3 = Rare, 2 = uncommon)
$lua_set_MinItemQuality = 4;

// Set a "Looter" which should be ignored
$lua_set_IgnoredLooter = "disenchanted";

// Here you can set the default status of the "Add Item value/attendees" Checkbox
$lua_set_AddLootDkpValuesCheckbox = true;

// Setting it to true will convert e.g. "Âvâtâr" to "Avatar".
$lua_set_ConvertNames = false;	

// Will Check for Event Triggers in the Loot Notes 
// (e.g. if you have events called "MC (Lucifron), MC (Magmadar), ..." and only want to log one raid.
$lua_set_LootNoteEventTriggerCheck = false;	

// Here you can set the default Rank of new Members (e.g. Member).
$lua_set_NewMemberDefaultRank = "";
		
// Here you can set a list of item IDs which should be ignored
// Mature Blue Dragon Sinew (From Azuregos)
#$lua_set_ignoritems[] = 18704;

// Here You can Set all the Raid notes which should be handled as a own raid everytime
// e.g. Random Drops are normaly added to an own Raid (Not all Random Drops to one Raid)
$lua_set_ownraids[] = "Random Drop";

// Here you can set the triggers for the eqDKP Event 
// (CT_RaidRracker Raid note will be parsed (Loot Notes only when $lua_set_LootNoteEventTriggerCheck is set))
$lua_set_EventTriggers["Molten Core"]	= "Molten Core";
$lua_set_EventTriggers["MC"] 		= "Molten Core";
$lua_set_EventTriggers["Azuregos"] 	= "Azuregos";
$lua_set_EventTriggers["Kazzak"] 	= "Kazzak";
$lua_set_EventTriggers["Onyxia"] 	= "Onyxia";
$lua_set_EventTriggers["Blackwing Lair"]	= "Blackwing Lair";
$lua_set_EventTriggers["Blackwinglair"] 	= "Blackwing Lair";
$lua_set_EventTriggers["BWL"] 			= "Blackwing Lair";

// Here you can set the triggers for the eqDKP Raid Note 
// (CT_RaidRracker Raid note and the Loots Notes will be parsed (Loot Notes will override the Raid Note))
$lua_set_RaidNoteTriggers["Azuregos"]	= "Azuregos";
$lua_set_RaidNoteTriggers["Kazzak"] 	= "Kazzak";
$lua_set_RaidNoteTriggers["Onyxia"] 	= "Onyxia";
$lua_set_RaidNoteTriggers["Lucifron"]	= "Lucifron";
$lua_set_RaidNoteTriggers["Magmadar"]	= "Magmadar";
$lua_set_RaidNoteTriggers["Gehennas"] 	= "Gehennas";
$lua_set_RaidNoteTriggers["Garr"] 	= "Garr";
$lua_set_RaidNoteTriggers["Geddon"] 	= "Baron Geddon";
$lua_set_RaidNoteTriggers["Shazzrah"] 	= "Shazzrah";
$lua_set_RaidNoteTriggers["Sulfuron"] 	= "Sulfuron";
$lua_set_RaidNoteTriggers["Golemagg"] 	= "Golemagg";
$lua_set_RaidNoteTriggers["Majordomo"]	= "Majordomo";
$lua_set_RaidNoteTriggers["Ragnaros"] 	= "Ragnaros";
$lua_set_RaidNoteTriggers["Razorgore"]	= "Razorgore";
$lua_set_RaidNoteTriggers["Vaelastrasz"] = "Vaelastrasz";
$lua_set_RaidNoteTriggers["Broodlord"]	= "Broodlord";
$lua_set_RaidNoteTriggers["Firemaw"] 	= "Firemaw";
$lua_set_RaidNoteTriggers["Ebonroc"] 	= "Ebonroc";
$lua_set_RaidNoteTriggers["Flamegor"] 	= "Flamegor";
$lua_set_RaidNoteTriggers["Chromaggus"]	= "Chromaggus";
$lua_set_RaidNoteTriggers["Nefarius"]	= "Lord Nefarius";
$lua_set_RaidNoteTriggers["Random"] 	= "Random Drop";

// Here you can set Player aliases, if one of the players is in the attende List it will be replaced 
// (e.g. if a Twink of the Mainchar helps out, but the Mainchar should get the DKP Points)

#$lua_set_PlayerAliases["Twink1ofMainChar1"]	= "MainChar1";
#$lua_set_PlayerAliases["Twink2ofMainChar1"]	= "MainChar1";

   function GetRaidNoteFromString($string) {
     global $lua_set_RaidNoteTriggers;
     foreach($lua_set_RaidNoteTriggers as $trigger => $value) {
       if($this->in_string($trigger, $string, true)) {
         return $value;
       }
     }
     return "Unknown";
   }

   function GetRaidEventFromString($string) {
     global $lua_set_EventTriggers;
     foreach($lua_set_EventTriggers as $trigger => $value) {
       if($this->in_string($trigger, $string, true)) {
         return $value;
       }
     }
     return "Unknown Event";
   }

   function GetItemName($itemid) {
     global $gitemlist, $itemidtoname;
     if(empty($gitemlist) && file_exists("itemlist.xml")) {
       $itemlisthandle = fopen("itemlist.xml", "r");
       while(!feof($itemlisthandle)) {
         $itemlistbuffer = fgets($itemlisthandle, 1024);
         preg_match_all("/<wowitem name=\"(.+?)\" id=\"(\d+)\" \/>/s", $itemlistbuffer, $itemlista, PREG_SET_ORDER);
         foreach($itemlista as $itemlistdata) {
           $gitemlist[$itemlistdata[2]] = $itemlistdata[1];
         }
       }
       fclose($itemlisthandle);
     }
     if(function_exists("GetItemName"))// Just ignore this, it's my part to get the itemname from my database
     { $altitemname = @GetItemName($itemid, $lang = "en");
   }
   if(!empty($altitemname)) {
     return $altitemname;
   } elseif(!empty($gitemlist[$itemid])) {
     return $gitemlist[$itemid];
   } elseif(!empty($itemidtoname[$itemid])) {
     return $itemidtoname[$itemid];
   } else {
     return false;
   }
 }

 function ConvertTimestringToTimestamp($timestring) {
   $parts = preg_split('/[\/ :]/', $timestring);
   return mktime($parts[3], $parts[4], $parts[5], $parts[0], $parts[1], $parts[2]);
 }

 function in_string($needle, $haystack, $insensitive = false) {
   if($insensitive) {
     $haystack = strtolower($haystack);
     $needle = strtolower($needle);
   }
   return(false !== strpos($haystack, $needle)) ? true : false;
 }

    function GetMainItemId($itemid)
    {
      $itemid = trim($itemid, "item:");
      $itemid = preg_split("/:/", $itemid);
      return $itemid[0];
    }

    function GetItemQualityByColor($color)
    {
      $color = strtolower($color);
      if($color == "ffa335ee") { return 4; }
      elseif($color == "ff0070dd") { return 3; }
      elseif($color == "ff1eff00") { return 2; }
      elseif($color == "ffffffff") { return 1; }
      elseif($color == "ff9d9d9d") { return 0; }
      else { return -1; }
    }

    function GetDkpValue($item)
    {
      global $db;
      $value = $db->query("SELECT MIN(`item_value`) as minval FROM ".ITEMS_TABLE." WHERE `item_name` = '".mysql_escape_string($item)."';");
      $value = $db->fetch_record($value);
      return $value['minval'];
    }

    function GetClassIdByClassNameLevel($classname, $level)
    {
      global $db;
      $value = $db->query("SELECT `class_id` FROM ".CLASS_TABLE." WHERE `class_name` = '".mysql_escape_string($classname)."' AND `class_min_level` <= '".mysql_escape_string($level)."' AND `class_max_level` >= '".mysql_escape_string($level)."' ORDER by class_min_level DESC;");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        return $value['class_id'];
      }
      else
      {
        return 0;
      }
    }


    function GetRaceIdByRaceName($racename)
    {
      global $db;
      if($racename == "Scourge")
      {
        $racename = "Undead";
      }
      elseif($racename == "NightElf")
      {
        $racename = "Night Elf";
      }
      $value = $db->query("SELECT `race_id` FROM ".RACE_TABLE." WHERE `race_name` = '".mysql_escape_string($racename)."';");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        return $value['race_id'];
      }
      else
      {
        return 0;
      }
    }

    function GetRankIdByRankName($rankname)
    {
      global $db;
      $value = $db->query("SELECT `rank_id` FROM ".MEMBER_RANKS_TABLE." WHERE `rank_name` = '".mysql_escape_string($rankname)."';");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        return $value['rank_id'];
      }
      else
      {
        return 0;
      }
    }




    function GetClassNameByClassId($classid)
    {
      global $db;
      $value = $db->query("SELECT `class_name` FROM ".CLASS_TABLE." WHERE `class_id` = '".mysql_escape_string($classid)."';");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        return $value['class_name'];
      }
      else
      {
        return "Unknown";
      }
    }

    function GetRaceNameByRaceId($raceid)
    {
      global $db;
      $value = $db->query("SELECT `race_name` FROM ".RACE_TABLE." WHERE `race_id` = '".mysql_escape_string($raceid)."';");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        return $value['race_name'];
      }
      else
      {
        return "Unknown";
      }
    }

    function GetRankNameByRankId($rankid)
    {
      global $db;
      $value = $db->query("SELECT `rank_name` FROM ".MEMBER_RANKS_TABLE." WHERE `rank_id` = '".mysql_escape_string($rankid)."';");
      if($db->num_rows($value) >= 1)
      {
        $value = $db->fetch_record($value);
        if(!empty($value['rank_name']))
        {
          return $value['rank_name'];
        }
        else
        {
          return "None";
        }
      }
      else
      {
        return "None";
      }
    }


    function GenerateUniqId()
    {
      return md5(uniqid(rand(), true));
    }

    function StripUniqIdFromString($string)
    {
      return preg_replace("/(.*?)-[a-z0-9]{32}-(.*?)/", "\\1\\2", $string);
    }

    function StripSpecialChars($string)
    {
      $string = strtr($string, "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF", "!ao?AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyy");
      $string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
      return($string);
    }

?>
