<?php
 /******************************
 * EQdkp
 * Copyright 2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lua.php
 * Began: Mon Sept 19 2005
 *
 * $Id: lua.php 8 2006-05-08 17:15:20Z tsigo $
 *
 ******************************/

 // EQdkp required files/vars
 define('EQDKP_INC', true);
 define('IN_ADMIN', true);
 $eqdkp_root_path = './../';

 include_once($eqdkp_root_path . 'common.php');
 include_once($eqdkp_root_path . 'admin/lua_config.php');

 $lua_set_OnlySimulate = false;
 $user->check_auth('u_lua_import');

 if(!function_exists("html_entity_decode")) {

  function html_entity_decode($string) 
  {
     $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
     $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
     $trans_tbl = get_html_translation_table(HTML_ENTITIES);
     $trans_tbl = array_flip($trans_tbl);
     return strtr($string, $trans_tbl);
  }
 }

class LUA_Import extends EQdkp_Admin {

 function lua_import() 
 {
   global $db, $eqdkp, $user, $tpl, $pm;
   global $SID;
   parent::eqdkp_admin();
   $this->assoc_buttons(Array(
		'parse' => Array('name' => 'parse', 
				 'process' => 'process_parse', 
				 'check' => 'u_lua_import'), 
		'form' => Array('name' => '', 
				'process' => 'display_form', 
				'check' => 'u_lua_import'), 
		'insert' => Array('name' => 'insertraids', 
				  'process' => 'insert_log', 
				  'check' => 'u_lua_import'), ));
 }

 function xml2Array($xml) 
 {
   $xml = trim($xml);
   $xml = '<?
   xml version = "1 . 0" encoding = "ISO - 8859 - 1"?>
   
'.$xml;
        $xp = xml_parser_create();
        xml_parser_set_option($xp, XML_OPTION_CASE_FOLDING, false);
        xml_parser_set_option($xp, XML_OPTION_SKIP_WHITE, false);
        xml_parser_set_option($xp, XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
        xml_parse_into_struct($xp,trim($xml),$vals,$index);
        xml_parser_free($xp);
        $_data = NULL;
        $temp = $depth = array();
        $dc = array();
        
        foreach($vals as $value)
        {
            $p = join('::', $depth);
            $key = $value['tag'];
            switch ($value['type'])
            {
              case 'open':
                array_push($depth, $key);
                array_push($depth, (int)$dc[$p]++);
                break;
              case 'complete':
                array_pop($depth);
                array_push($depth, $key);
                $p = join('::',$depth);
                $temp[$p] = $value['value'];
                array_pop($depth);
                array_push($depth, (int)$dc[$p]);
                break;
              case 'close':
                array_pop($depth);
                array_pop($depth);
                break;
            }
        }

        foreach($temp as $key=>$value)
        {
            $levels = explode('::',$key);
            $num_levels = count($levels);
            if ($num_levels==1)
            {
                $_data[$levels[0]] = $value;
            }
            else
            {
                $pointer = &$_data;
                for ($i = 0; $i < $num_levels; $i++)
                {
                    if (!isset($pointer[$levels[$i]]))
                    {
                        $pointer[$levels[$i]] = array();
                    }
                    $pointer = &$pointer[$levels[$i]];
                }
                $pointer = $value;
            }
    
        }
        return ($_data);
    }

function process_parse()
{
  global $db, $eqdkp, $user, $tpl, $pm;
  global $SID;
  global $lua_set_MinItemQuality, $lua_set_IgnoredLooter, $lua_set_ConvertNames;
  global $lua_set_LootNoteEventTriggerCheck, $lua_set_PlayerAliases, $lua_set_AddLootDkpValuesCheckbox;
  global $lua_set_ignoritems, $lua_set_ownraids, $itemidtoname;

        $_POST['log'] = trim(str_replace("&", "and", html_entity_decode($_POST['log'])));

        if(!preg_match("/<RaidInfo><key>.*?<\/key>.*?<Join>.*?<\/Join><Leave>.*?<\/Leave><Loot>.*?<\/Loot><\/RaidInfo>/s", $_POST['log']))
        {
          message_die($user->lang['lua_step1_invalidstring_msg'], $user->lang['lua_step1_invalidstring_titel']);
        }

        $allraids = array();
        $allevents = array();
        $allraidattendees = array();
        $itemidtoname = array();

        $adata = $this->xml2array($_POST['log']);
        $globalevent = $this->GetRaidEventFromString($adata['RaidInfo']['note']);
        $globalraidnote = $this->GetRaidNoteFromString($adata['RaidInfo']['note']);

        if(!isset($lua_set_ignoritems)) { $lua_set_ignoritems = array(); }

        if($allforeachdata = @array_shift($adata['RaidInfo'][0]['Join'])) foreach($allforeachdata as $playerdata)
        {
          $playerdata['time'] = $this->ConvertTimestringToTimestamp($playerdata['time']);

          if(!empty($lua_set_PlayerAliases[$playerdata['player']]))
          {
            $playerdata['player'] = $lua_set_PlayerAliases[$playerdata['player']];
          }

          if($lua_set_ConvertNames)
          {
            $playerdata['player'] = $this->StripSpecialChars($playerdata['player']);
          }

          if(!isset($allattendees[$playerdata['player']]))
          {
            $allattendees[$playerdata['player']] = array("firstjoin" => 0, "lastjoin" => 0, "lastleave" => 0, "race" => 0, "class" => 0, "level" => 0);
          }

          if($playerdata['time'] < $allattendees[$playerdata['player']]['firstjoin'] || empty($allattendees[$playerdata['player']]['firstjoin'])) 
		{ 
			$allattendees[$playerdata['player']]['firstjoin'] = $playerdata['time']; 
		}

          if($playerdata['time'] > $allattendees[$playerdata['player']]['lastjoin'] || empty($allattendees[$playerdata['player']]['lastjoin'])) 
		{ 
			$allattendees[$playerdata['player']]['lastjoin'] = $playerdata['time']; 
		}

          if(!empty($playerdata['race']) && empty($allattendees[$playerdata['player']]['race'])) 
		{ 
			$allattendees[$playerdata['player']]['race'] = $this->GetRaceIdByRaceName($playerdata['race']); 
		}

          if(!empty($playerdata['class']) && empty($allattendees[$playerdata['player']]['class'])) 
		{ 
			$allattendees[$playerdata['player']]['class'] = $this->GetClassIdByClassNameLevel($playerdata['class'], $playerdata['level']); 
		}

          if(!empty($playerdata['level']) && $allattendees[$playerdata['player']]['level'] < $playerdata['level']) 
		{ 
			$allattendees[$playerdata['player']]['level'] = $playerdata['level']; 
		}

        }

        if($allforeachdata = @array_shift($adata['RaidInfo'][0]['Leave'])) foreach($allforeachdata as $playerdata)
        {
          $playerdata['time'] = $this->ConvertTimestringToTimestamp($playerdata['time']);
          if(!empty($lua_set_PlayerAliases[$playerdata['player']]))
          {
            $playerdata['player'] = $lua_set_PlayerAliases[$playerdata['player']];
          }

          if($lua_set_ConvertNames)
          {
            $playerdata['player'] = $this->StripSpecialChars($playerdata['player']);
          }

          if($playerdata['time'] > $allattendees[$playerdata['player']]['lastjoin']) 
		{ 
			$allattendees[$playerdata['player']]['lastleave'] = $playerdata['time']; 
		}
        }

        $i = 0;
        $allforeachdata = @array_shift($adata['RaidInfo'][0]['Loot']);
        
        if(!empty($allforeachdata)) foreach($allforeachdata as $lootdata)
        {
          if(!empty($lua_set_PlayerAliases[$playerdata['Player']]))
          {
            $playerdata['Player'] = $lua_set_PlayerAliases[$playerdata['Player']];
          }

          if($lua_set_ConvertNames)
          {
            $lootdata['Player'] = $this->StripSpecialChars($lootdata['Player']);
          }

          $itemid = $this->GetMainItemId($lootdata['ItemID']);
          $itemidtoname[$itemid] = str_replace("\\'", "'", $lootdata['ItemName']);
          $allloot[$i]['itemid'] = $itemid;
          $allloot[$i]['name'] = $this->GetItemName($itemid);
          $allloot[$i]['quality'] = $this->GetItemQualityByColor($lootdata['Color']);
          $allloot[$i]['looter'] = $lootdata['Player'];
          $allloot[$i]['time'] = $this->ConvertTimestringToTimestamp($lootdata['Time']);
          $lootraidnote = $this->GetRaidNoteFromString($lootdata['Note']);
          $lootraidevent = $this->GetRaidEventFromString($lootdata['Note']);

          if(!empty($lootraidnote) && $lootraidnote != "Unknown")
          {
            $alllootraidnote = $lootraidnote;
          } else {
            $alllootraidnote = $globalraidnote;
          }

          if(!empty($lootraidevent) && $lootraidevent != "Unknown Event" && $lua_set_LootNoteEventTriggerCheck)
          {
            $alllootraidevent = $lootraidevent;
          } else {
            $alllootraidevent = $globalevent;
          }

          if(!in_array($alllootraidevent, $allevents))
          {
            $allevents[] = $alllootraidevent;
          }

          if(in_array($alllootraidnote, $lua_set_ownraids)) 
          {
            $alllootraidnote = $alllootraidnote."-".$this->GenerateUniqId()."-";
          }

          $allloot[$i]['raidnote'] = $alllootraidnote;
          $allloot[$i]['raidevent'] = $alllootraidevent;

          if(!isset($allraids[$alllootraidnote]))
          {
            $allraids[$alllootraidnote] = array();
          }

          if(!isset($allraids[$alllootraidevent][$alllootraidnote])) 
          {
            $allraids[$alllootraidevent][$alllootraidnote] = $allloot[$i]['time'];
          }

          if(!empty($lootdata['Note']))
          {
            preg_match("/([\d\.]+) DKP/", $lootdata['Note'], $dkpinfo);
            if(!empty($dkpinfo[1]))
            {
              $allloot[$i]['dkp'] = $dkpinfo[1];
            }
          }

          foreach($allattendees as $player => $times)
          {
            if($allloot[$i]['time'] > $times['firstjoin'] && ($times['lastleave'] > $allloot[$i]['time'] || empty($times['lastleave'])))
            {
              $allloot[$i]['attendees'][] = $player;
              $allraidattendees[$alllootraidevent][$alllootraidnote][] = $player;
            }
          }
          $allraidattendees[$alllootraidevent][$alllootraidnote] = array_unique($allraidattendees[$alllootraidevent][$alllootraidnote]);
          sort($allraidattendees[$alllootraidevent][$alllootraidnote]);
          $i++;
        }

        if(empty($allforeachdata))
        {
          if(!in_array($globalevent, $allevents))
          {
            $allevents[] = $globalevent;
          }

          if(!isset($allraids[$globalraidnote]))
          {
            $allraids[$globalraidnote] = array();
          }

          foreach($allattendees as $player => $times)
          {
            $allraidattendees[$globalevent][$globalraidnote][] = $player;
          }

          $allraidattendees[$globalevent][$globalraidnote] = array_unique($allraidattendees[$globalevent][$globalraidnote]);
          sort($allraidattendees[$globalevent][$globalraidnote]);

          if(!isset($allraids[$globalevent][$globalraidnote])) 
          {
            $allraids[$globalevent][$globalraidnote] = $this->ConvertTimestringToTimestamp($adata['RaidInfo']['key']);
          }
        }
        #print_r($allraidattendees);
        #print_r($allattendees);
        #print_r($allevents);
        #print_r($allraids);
        #print_r($allloot);
                
        $tpl->assign_vars(array(
            'S_STEP1'         => false,
            'L_FOUND_RAIDS' => $user->lang['lua_step2_foundraids'],
            'L_RAIDS_DROPS_DETAILS'  => $user->lang['lua_step2_raidsdropsdetails'],
            'L_RAIDS_INSERT'  => $user->lang['lua_step2_insertraids'],
            'L_DKP_VALUE_TIP' => $user->lang['lua_step2_dkpvaluetip'],
            'S_ADDLOOTDKPVALUES' => (($lua_set_AddLootDkpValuesCheckbox) ? true : false),
            )
        );
        foreach($allattendees as $playername => $playerdata)
        {
          $tpl->assign_block_vars('allplayers_row', array(
            'ALLPLAYERNAME' => $playername,
            'ALLPLAYERRACE' => $playerdata['race'],
            'ALLPLAYERCLASS' => $playerdata['class'],
            'ALLPLAYERLEVEL' => $playerdata['level'],
          ));
        }
        
        foreach($allevents as $hevent)
        {
          $tpl->assign_block_vars('events_row', array(
            'HEVENT' => $hevent,
            'STRIPPEDHEVENT' => $this->StripUniqIdFromString($hevent),
          ));

          foreach($allraids[$hevent] as $hraidnote => $hraidtime)
          {
            $eventvalue = $db->query("SELECT `event_value` FROM ".EVENTS_TABLE." WHERE event_name = '".mysql_escape_string($hevent)."' LIMIT 1");
            $eventvalue = $db->fetch_record($eventvalue);
            $eventvalue = $eventvalue['event_value'];
            if($eventvalue <= 0)
            {
              $eventvalue = "";
            }
            $STRIPPEDHRAIDNOTE = $this->StripUniqIdFromString($hraidnote);
            $tpl->assign_block_vars('events_row.raids_row', array(
              'ROW_CLASS' => $eqdkp->switch_row_class(),
              'HRAIDNOTE' => $hraidnote,
              'HRAIDTIME' => $hraidtime,
              'HRAIDTIME_MO' => date("m", $hraidtime),
              'HRAIDTIME_D' => date("d", $hraidtime),
              'HRAIDTIME_Y' => date("Y", $hraidtime),
              'HRAIDTIME_H' => date("H", $hraidtime),
              'HRAIDTIME_MI' => date("i", $hraidtime),
              'HRAIDTIME_S' => date("s", $hraidtime),
              'STRIPPEDHRAIDNOTE' => (($STRIPPEDHRAIDNOTE == "Unknown") ? "" : $STRIPPEDHRAIDNOTE),
              'RAIDDKPVALUE' => $eventvalue,
              'ATTENDEES' => @implode("\n", $allraidattendees[$hevent][$hraidnote]),
              'ATTENDEESCOUNT' => @count($allraidattendees[$hevent][$hraidnote]),
            ));

            $i = 0;
            $hraidattendees = array();

            if(!isset($allloot)) 
		{ 
			$allloot = array(); 
		}

            foreach($allloot as $hlootid => $hlootdata)
            {
              if($hlootdata['raidnote'] == $hraidnote && $hlootdata['raidevent'] == $hevent)
              {
                if($hlootdata['quality'] >= $lua_set_MinItemQuality && !in_array($hlootdata['itemid'], $lua_set_ignoritems) && strtolower($hlootdata['looter']) != strtolower($lua_set_IgnoredLooter))
                {
                  $tpl->assign_block_vars('events_row.raids_row.loot_row', array(
                    'HNR' => $i,
                    'HNAME' => $hlootdata['name'],
                    'HID' => $hlootdata['itemid'],
                    'HLOOTER' => $hlootdata['looter'],
                    'HDKP' => ((!empty($hlootdata['dkp'])) ? $hlootdata['dkp'] : $this->GetDkpValue($hlootdata['name'])),
                    'STRIPPEDHRAIDNOTE' => $this->StripUniqIdFromString($hraidnote),
                  ));
                }
              }
              $i++;
            }
          }
        }
        
        $eqdkp->set_vars(array(
            'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['lua_step2_pagetitel'],
            'template_file'     => 'admin/lua.html',
            'display'           => true,
            )
        );
}
    
function insert_log()
{
  global $db, $eqdkp, $user, $tpl, $pm;
  global $SID;
  global $lua_set_MinItemQuality, $lua_set_OnlySimulate, $lua_set_NewMemberDefaultRank, $lua_set_ignoritems, $lua_set_ownraids;
  $a = 0;
  foreach($_POST['events'] as $ievent)
  {
    $inewevent = $_POST['newevents'][$ievent];
    foreach($_POST['raids'][$ievent] as $iraid => $iraidtime)
    {
      $inewraidtime = mktime($_POST['raidtimes'][$ievent][$iraid]['h'], $_POST['raidtimes'][$ievent][$iraid]['mi'], $_POST['raidtimes'][$ievent][$iraid]['s'], $_POST['raidtimes'][$ievent][$iraid]['mo'], $_POST['raidtimes'][$ievent][$iraid]['d'], $_POST['raidtimes'][$ievent][$iraid]['y']);
      $inewraidnote = $_POST['newraidnotes'][$ievent][$iraid];
      $inewraidattendees = trim($_POST['newraidattendees'][$ievent][$iraid]);
      $iallraids[$a]['event'] = $inewevent;
      $iallraids[$a]['raidnote'] = $inewraidnote;
      $iallraids[$a]['time'] = $inewraidtime;
      $iallraids[$a]['dkp'] = 0;
      $iallraids[$a]['attendees'] = explode("\n", $inewraidattendees);
      if(empty($inewraidattendees))
      {
        $iallraids[$a]['donotadd'] = true;
      }
      else
      {
        $iallraids[$a]['donotadd'] = false;
      }
      $iallraids[$a]['loots'] = array();
      $i = 0;
      if(!isset($_POST['loots'][$ievent][$iraid])) {  $_POST['loots'][$ievent][$iraid] = array();  }
      foreach($_POST['loots'][$ievent][$iraid] as $ilootdata)
      {
        if(!empty($ilootdata['dkp']) || $ilootdata['dkp'] == "0")
        {
          $iallraids[$a]['loots'][$i]['name'] = str_replace("\\'", "'", $ilootdata['name']);
          $iallraids[$a]['loots'][$i]['id'] = $ilootdata['id'];
          $iallraids[$a]['loots'][$i]['looter'] = $ilootdata['looter'];
          $iallraids[$a]['loots'][$i]['dkp'] = $ilootdata['dkp'];
          $iallraids[$a]['dkp'] += $ilootdata['dkp'];
        }
        $i++;
      }
      $iallraids[$a]['dkp'] = round($iallraids[$a]['dkp'] / count($iallraids[$a]['attendees']), 2);
      if($_POST['addlootdkpvalues'][$ievent][$iraid] == 1)
      {
        $iallraids[$a]['dkp'] += $_POST['raiddkpvalues'][$ievent][$iraid];
      }
      else
      {
        $iallraids[$a]['dkp'] = $_POST['raiddkpvalues'][$ievent][$iraid];
      }
      foreach($iallraids[$a]['attendees'] as $attendeeid => $attendeename)
      {
        $attendeename = trim($attendeename);
        if(!empty($attendeename))
        {
          $iallraids[$a]['attendees'][$attendeeid] = $attendeename;
        }
        else
        {
          unset($iallraids[$a]['attendees'][$attendeeid]);
        }
      }
      $a++;
    }
  }
  $text == "";
  foreach($iallraids as $iraid)
  {
    if(!$iraid['donotadd'])
    {
      $newraidid = $db->query("SELECT MAX(`raid_id`) as id FROM ".RAIDS_TABLE.";");
      $newraidid = $db->fetch_record($newraidid);
      $newraidid = $newraidid['id'] + 1;
      $alreadyexistscheck = $db->query("SELECT `raid_name`, `raid_date`, `raid_note`, `raid_value` FROM ".RAIDS_TABLE." WHERE raid_name = '".mysql_escape_string($iraid['event'])."' AND raid_date >= '".mysql_escape_string($iraid['time']-1800)."' AND raid_date <= '".mysql_escape_string($iraid['time']+1800)."' AND raid_note = '".mysql_escape_string($iraid['raidnote'])."' LIMIT 1");
      if($db->num_rows($alreadyexistscheck) == 1 && !$lua_set_OnlySimulate)
      {
        $text .= sprintf($user->lang['lua_step3_alreadyexist'], $iraid['raidnote'], $iraid['event'], $iraid['dkp'])."<br>\n"; 
      }
      elseif(!empty($iraid['event']))
      {
    if(!$lua_set_OnlySimulate) $db->query("INSERT INTO ".RAIDS_TABLE." (`raid_id`, `raid_name`, `raid_date`, `raid_note`, `raid_value`, `raid_added_by`) VALUES ('".mysql_escape_string($newraidid)."', '".mysql_escape_string($iraid['event'])."', '".mysql_escape_string($iraid['time'])."', '".mysql_escape_string($iraid['raidnote'])."', '".mysql_escape_string($iraid['dkp'])."', 'RaidTracker (by ".mysql_escape_string($user->data['username']).")');");
    $text .= sprintf($user->lang['lua_step3_raidadded'], $iraid['raidnote'], $iraid['event'], $iraid['dkp']); 
        $log_action = array(
          'header' => '{L_ACTION_RAID_ADDED}',
          'id'            => $newraidid,
          '{L_EVENT}'     => $iraid['event'],
          '{L_ATTENDEES}' => trim(implode(', ', $iraid['attendees']), ', '),
          '{L_NOTE}'      => $iraid['raidnote'],
          '{L_VALUE}'     => $iraid['dkp'],
          '{L_ADDED_BY}'  => 'RaidTracker (by '.$user->data['username'].')',
        );
        if(!$lua_set_OnlySimulate)
	{
		 $this->log_insert(array('log_type'   => $log_action['header'], 'log_action' => $log_action));
	}

        foreach($iraid['attendees'] as $iattendee)
        {
          $memberexistscheck = $db->query("SELECT `member_name` FROM ".MEMBERS_TABLE." WHERE member_name = '".mysql_escape_string($iattendee)."';");
          if($db->num_rows($memberexistscheck) == 0)
          {
            $iattendeerace = $this->GetRaceNameByRaceId($_POST['allplayers'][$iattendee]['race']);
            $iattendeeclass = $this->GetClassNameByClassId($_POST['allplayers'][$iattendee]['class']);
            $iattendeelevel = ((!empty($_POST['allplayers'][$iattendee]['level'])) ? $_POST['allplayers'][$iattendee]['level'] : "Unknown" ) ;
            $iattendeerank = $this->GetRankIdByRankName($lua_set_NewMemberDefaultRank) ;

            if(!$lua_set_OnlySimulate) 
	    {
		$db->query("INSERT INTO ".MEMBERS_TABLE." 
			    (`member_name`, `member_status`, `member_firstraid`, `member_level`, `member_rank_id`, `member_class_id`, `member_race_id`) 
			    VALUES ('".mysql_escape_string($iattendee)."', '1', '".mysql_escape_string($iraid['time'])."', 
			    '".mysql_escape_string($_POST['allplayers'][$iattendee]['level'])."', 
			    '".mysql_escape_string($iattendeerank)."', '".mysql_escape_string($_POST['allplayers'][$iattendee]['class'])."', 
			    '".mysql_escape_string($_POST['allplayers'][$iattendee]['race'])."');");
	    }

            $text .= sprintf($user->lang['lua_step3_memberadded'], $iattendee, $iattendeerace, $iattendeeclass, $iattendeelevel, $this->GetRankNameByRankId($iattendeerank)); 
            $log_action = array(
              'header'         => '{L_ACTION_MEMBER_ADDED}',
              '{L_NAME}'       => $iattendee,
              '{L_EARNED}'     => 0,
              '{L_SPENT}'      => 0,
              '{L_ADJUSTMENT}' => 0,
              '{L_LEVEL}'      => $iattendeelevel,
              '{L_RACE}'       => $iattendeerace,
              '{L_CLASS}'      => $iattendeeclass,
              '{L_ADDED_BY}'   => 'RaidTracker (by '.$user->data['username'].')',
            );
            if(!$lua_set_OnlySimulate) $this->log_insert(array('log_type' => $log_action['header'],'log_action' => $log_action));
          }
          if(!$lua_set_OnlySimulate) 
	  {
		$db->query("INSERT INTO ".RAID_ATTENDEES_TABLE." (`raid_id`, `member_name`) 
			    VALUES ('".mysql_escape_string($newraidid)."', '".mysql_escape_string($iattendee)."');");
	  }

          if(!$lua_set_OnlySimulate) 
	  {
		$db->query("UPDATE ".MEMBERS_TABLE." 
			    SET member_earned = member_earned + ".mysql_escape_string($iraid['dkp']).", 
			    member_status = '1', member_lastraid = '".mysql_escape_string($iraid['time'])."', 
			    member_raidcount = member_raidcount + 1 
			    WHERE member_name = '".mysql_escape_string($iattendee)."';");
        }

        $text .= sprintf($user->lang['lua_step3_attendeesadded'], count($iraid['attendees'])); 
        foreach($iraid['loots'] as $iloot)
        {
          if(!$lua_set_OnlySimulate) 
	  {
		$db->query("INSERT INTO ".ITEMS_TABLE." 
		            (`item_name`, `item_buyer`, `raid_id`, `item_value`, `item_date`, `item_added_by`, `item_group_key`) 
			    VALUES ('".mysql_escape_string($iloot['name'])."', '".mysql_escape_string($iloot['looter'])."', 
			    '".mysql_escape_string($newraidid)."', '".mysql_escape_string($iloot['dkp'])."', 
			    '".mysql_escape_string($iraid['time'])."', 'RaidTracker (by ".mysql_escape_string($user->data['username']).")', 
			    '".mysql_escape_string($this->gen_group_key($iloot['name'], $iraid['time'], $newraidid))."');");
	  }

          if(!$lua_set_OnlySimulate) 
	  {
		$db->query("UPDATE ".MEMBERS_TABLE." 
			    SET member_spent = member_spent + ".mysql_escape_string($iloot['dkp'])." 
			    WHERE member_name = '".mysql_escape_string($iloot['looter'])."';");
	  }

          $text .= sprintf($user->lang['lua_step3_lootadded'], $iloot['name'], $iloot['dkp'], $iloot['looter']); 
          $log_action = array(
            'header'       => '{L_ACTION_ITEM_ADDED}',
            '{L_NAME}'     => $iloot['name'],
            '{L_BUYERS}'   => $iloot['looter'],
            '{L_RAID_ID}'  => $newraidid,
            '{L_VALUE}'    => $iloot['dkp'],
            '{L_ADDED_BY}' => 'RaidTracker (by '.$user->data['username'].')',
          );
          if(!$lua_set_OnlySimulate) $this->log_insert(array('log_type' => $log_action['header'],'log_action' => $log_action));
        }
        $text .= "<br>\n";
      }
    }
  }
  message_die($text, $user->lang['lua_step3_titel']);
  
  $eqdkp->set_vars(array(
        'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['lua_step3_pagetitel'],
        'template_file'     => 'admin/lua.html',
        'display'           => true,
        )
    );
}
    
function display_form()
{
    global $db, $eqdkp, $user, $tpl, $pm;
    global $SID;
    
    $tpl->assign_vars(array(
        'F_PARSE_LOG'    => 'index.php' . $SID,
        'S_STEP1'        => true,
        'L_PASTE_LOG'    => $user->lang['lua_step1_th'],
        'L_PARSE_LOG'    => $user->lang['lua_step1_button_parselog'],
      )
    );
    
    $eqdkp->set_vars(array(
        'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['lua_step1_pagetitel'],
        'template_file'     => 'admin/lua.html',
        'display'           => true,
        )
    );
}
}

$LUA_Import = new LUA_Import;
$LUA_Import->process();
?> 
