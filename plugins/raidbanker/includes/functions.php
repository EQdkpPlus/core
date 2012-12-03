<?php
/******************************
 * EQdkp Raid Banker
 * Copyright 2005 by WalleniuM
 * ------------------
 * config.php
 * Began: Tue June 1, 2006
 * Changed: Tue June 1, 2006
 * 
 ******************************/
 
include_once($eqdkp_root_path . 'common.php');
global $table_prefix;

//Define the tables
if (!defined('RB_BANK_TABLE')) { define('RB_BANK_TABLE', $table_prefix . 'raidbanker_bank'); }
if (!defined('RB_CHARS_TABLE')) { define('RB_CHARS_TABLE', $table_prefix . 'raidbanker_chars'); }
if (!defined('RB_CONFIG_TABLE')) { define('RB_CONFIG_TABLE', $table_prefix . 'raidbanker_config'); }
if (!defined('RB_ACTIONS_TABLE')) { define('RB_ACTIONS_TABLE', $table_prefix . 'raidbanker_actions'); }
if (!defined('RB_BANK_REL_TABLE')) { define('RB_BANK_REL_TABLE', $table_prefix . 'raidbanker_bank_rel'); }


// get the config
$sql = 'SELECT * FROM ' . RB_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

function GetFilterValues($language)
{
	$language = trim(strtolower($language));
  if ($language == "german"){
    $filter_value['type'] = array(
      'quest'     => "Quest",
      'weapon'    => "Waffe",
      'reagent'   => "Reagenz",
      'builder'   => "Handwerkswaren",
      'armor'     => "Rüstung",
      'key'       => "Schlüssel",
      'useable'   => "Verbrauchbar",
      'misc'      => "Verschiedenes"
    );

    $filter_value['quality'] = array(
      '5'       => "Legendär",
      '4'       => "Episch",
      '3'       => "Rar",
      '2'       => "Normal",
      '1'       => "Rest"
    );
  }else{
    // filter array
    $filter_value['type'] = array(
      'quest'     => "Quest",
      'weapon'    => "Weapon",
      'reagent'   => "Reagent",
      'builder'   => "Crafted",
      'armor'     => "Armor",
      'key'       => "Key",
      'useable'   => "Usable",
      'misc'      => "Miscellaneous"
    );

  $filter_value['quality'] = array(
      '5'       => "Legendary",
      '4'       => "Epic",
      '3'       => "Rare",
      '2'       => "Normal",
      '1'       => "Rest"
  );
  }
  return $filter_value;
}

// the "Save my Data to the Database" :D
  function UpdateRBConfig($fieldname,$insertvalue)
      {
        global $eqdkp_root_path, $user, $SID, $table_prefix, $db;
        $sql = "UPDATE `" . $table_prefix . "raidbanker_config` SET config_value='".strip_tags(htmlspecialchars($insertvalue))."' WHERE config_name='".$fieldname."';";
        if ($db->query($sql)){
          return true;
        } else {
          return false;
        }
      }
      
// get the item name (out of xml file?)
function GetItemName($itemid, $itemname)
		{
		global $eqdkp_root_path;
			if(file_exists($eqdkp_root_path . "plugins/raidbanker/includes/itemlist.xml"))
			{
				$itemlisthandle = fopen($eqdkp_root_path . "plugins/raidbanker/includes/itemlist.xml", "r");
				while(!feof($itemlisthandle))
				{
					$itemlistbuffer = fgets($itemlisthandle, 1024);
					preg_match_all("/<wowitem name=\"(.+?)\" id=\"(\d+)\" \/>/s", $itemlistbuffer, $itemlista, PREG_SET_ORDER);
					foreach($itemlista as $itemlistdata)
					{
						$gitemlist[$itemlistdata[2]] = $itemlistdata[1];
					}
				}
				fclose($itemlisthandle);
			}else{
        $gitemlist[$itemlistdata[2]] = $itemname;
      }
			if(!empty($altitemname))
			{
				return $altitemname;
			}
			elseif(!empty($gitemlist[$itemid]))
			{
				return $gitemlist[$itemid];
			}
			else
			{
				return false;
			}
		}
?>
