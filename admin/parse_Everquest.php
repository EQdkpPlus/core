<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * parse_Everquest.php
 * Began: Sat March 05 2005
 * 
 * $Id$
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Parse_Log extends EQdkp_Admin
{
    function parse_log()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();
        
        $this->assoc_buttons(array(
            'parse' => array(
                'name'    => 'parse',
                'process' => 'process_parse',
                'check'   => 'a_raid_'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_raid_'))
        );
    }
    
    // ---------------------------------------------------------
    // Process Parse
    // ---------------------------------------------------------
    function process_parse()
    {


	/* New EQ format for /who

[Fri Apr 29 20:45:08 2005] Logging to 'eqlog.txt' is now *ON*.
[Fri Apr 29 20:45:09 2005] Players on EverQuest:
[Fri Apr 29 20:45:09 2005] ---------------------------
[Fri Apr 29 20:45:09 2005] [63 Warlock (Necromancer)] Volac (Dark Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Maestro (Bard)] Willowwhisper (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Premus  <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [65 Storm Warden (Druid)] Whispering (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [65 Prophet (Shaman)] Cuzak (Troll) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [64 Virtuoso (Bard)] Case (Half Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Aspenelder  <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [67 Forest Stalker (Ranger)] Hatteras (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [67 Archon (Cleric)] Kelee (High Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Storm Warden (Druid)] Branson (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Overlord (Warrior)] Damilex (Human) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Lord Protector (Paladin)] Hafgun (Human) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [54 Primalist (Beastlord)] Sistakitty (Vah Shir) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Arch Convoker (Magician)] Torgen (High Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [63 Phantasmist (Enchanter)] Merlinycus (Erudite) <Aurora Spirits>
[Fri Apr 29 20:45:09 2005] [66 Forest Stalker (Ranger)] Xlifex (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Misser  <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [70 Scourge Knight (Shadow Knight)] Grosbras (Ogre) <Shadows of Doom>
[Fri Apr 29 20:45:09 2005] [70 Herald (Bard)] Anjagi (Fire Elemental) <Guild>


*/
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

	$usable_date = "/([a-zA-Z]{3}) ([a-zA-Z]{3}) ([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2}) ([0-9]{4})/";
        
        $channel_members  = '';
        $line             = '';
        $valid_date_found = false;
        
        $log_file = explode("\n", $_POST['log']);
        $log_file = str_replace('&lt;', '<', str_replace('&gt;', '>', $log_file));
        $log_file = str_replace('Shadow Knight', 'Shadow_Knight', $log_file);
        $line_count = sizeof($log_file);
        
        // Go through each line and
        //      - Check for/get a valid member in the /who
        //      - Check if there's a valid date we can use
        //      - Check for/get valid members from /list <channel>

        $log_date = array();
        session_start(); // Hold our array of name => class/level/race
        for ( $i = 0; $i < $line_count; $i++ )
        {
            $line = '';
            if ( (isset($_POST['findall'])) || (strpos($log_file[$i], '<')) )
            {
                $member_name = $this->line_parse($log_file[$i]);

                // Make sure that each member's name is properly capitalized
                $mname = strtolower(preg_replace('/[[:space:]]/i', ' ', $member_name));
                $member_name = ucwords($mname);

                if ( trim($member_name) != '')
                {
                    $member_names[] = $member_name;
                }
            }
            
            // Check if there's a usable date/time in this string
            if ( preg_match( $usable_date , $log_file[$i], $pre_log_date) )
            {
                if ( isset($pre_log_date[0]) )
                {
                    $log_date = $pre_log_date;
                }
                $valid_date_found = true;
            }
             
            // Check if the log contains channel information we can use
            if (preg_match("/Channel (.+)\(([0-9]{1,5})\) members\:/", $log_file[$i], $num_members))
            {
                $first_chan_line = ($i+1);
                $channel_lines = (($num_members[2] % 10) == 0) ? $num_members[2] / 10 : floor($num_members[2] / 10) + 1;
                $last_chan_line = ($i + $channel_lines + 1);
                for ( $j = $first_chan_line; $j < $last_chan_line; $j++ )
                {
                    $line = preg_replace("/\[[A-Za-z]{3} [A-Za-z]{3} [0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9]{4}\]/", '', $log_file[$j]);
                    $line = preg_replace("/[^A-Za-z\,[:space:]]/", '', $line);
                    if ($j != $channel_lines)
                    {
                        $line = str_replace("\n", ', ', $line);
                    }
                    $channel_members .= $line;
                }
            }

	if (isset ($log_file[$i]) ) {
		$line_to_read = str_replace("\t", ' ', $log_file[$i]);
	}

	if (preg_match("/^[0-9]{1,2} (.+) ([0-9]{1,2}) ([a-z A-Z]+)/", $line_to_read, $raid_line))
	  {
	   $memberName  = $raid_line[1];
	   $memberLevel = $raid_line[2];
	   $memberClass = $raid_line[3];
           $line = preg_replace("/ Group Leader/", '', $memberClass);
           $line = preg_replace("/ Raid Leader/", '', $line);
	   $memberClass = $line;

	   if ( $memberClass == 'Shadow' ) {
		$memberClass = 'Shadow Knight';
	   }

	   if ( !($this->original_class($memberClass) == "")) {
		$memberClass = $this->original_class($memberClass);
	  }
           
	   $_SESSION[$memberName] = array(
	    'name'  => $memberName,
	    'race'  => 'Unknown',
	    'level' => $memberLevel,
	    'class' => $memberClass,
	   );

	   // add member's name to the array
	   $member_names[] = $memberName;


 	 }


      } // for ... log_file

        
        // If there were channel members, join the two arrays
        if ( !empty($channel_members) )
        {
            $channel_members = explode(', ', $channel_members);
            $member_names = array_merge($member_names, $channel_members);
        }
        
        if ( $valid_date_found )
        {
            $date['mo'] = $log_date[2];
            $date['d']  = $log_date[3];
            $date['y']  = $log_date[7];
            $date['h']  = $log_date[4];
            $date['mi'] = $log_date[5];
            $date['s']  = $log_date[6];
        }
        else
        {
            $date['mo'] = date('M');
            $date['d']  = date('d');
            $date['y']  = date('Y');
            $date['h']  = date('h');
            $date['mi'] = date('i');
            $date['s']  = date('s');
        }
        
        // Process the member_names array: replaces spaces, make it unique, sort it and reset it
        if ( (isset($member_names)) && (is_array($member_names)) )
        {
            $name_count = sizeof($member_names);
        }
        else
        {
            $name_count = 0;
            $member_names = array();
        }
        
        for ( $i = 0; $i < $name_count; $i++ )
        {
            $member_names[$i] = str_replace(' ', '', $member_names[$i]);
        }
        $member_names = array_unique($member_names);
        sort($member_names);
        reset($member_names);
        
        $tpl->assign_vars(array(
            'S_STEP1'         => false,
            'L_FOUND_MEMBERS' => sprintf($user->lang['found_members'], $line_count, sizeof($member_names)),
            'L_LOG_DATE_TIME' => $user->lang['log_date_time'],
            'L_LOG_ADD_DATA'  => $user->lang['log_add_data'],
            
            'FOUND_MEMBERS' => implode("\n", $member_names),
            'MO'            => $this->M_to_n($date['mo']),
            'D'             => $date['d'],
            'Y'             => $date['y'],
            'H'             => $date['h'],
            'MI'            => $date['mi'],
            'S'             => $date['s'])
        );
        
        $eqdkp->set_vars(array(
            'page_title'        => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['parselog_title'],
            'gen_simple_header' => true,
            'template_file'     => 'admin/parse_Everquest.html',
            'display'           => true)
        );
    }
    
    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function line_parse($log_line)
    {
        global $db, $eqdkp, $user;
        static $member_ranks = array();
        
        $log_line = stripslashes($log_line);
        
        // Build a clean array of guildtags we might be looking for
        $parsetags = explode("\n", $eqdkp->config['parsetags']);
        foreach ( $parsetags as $k => $v )
        {
            $parsetags[$k] = trim(stripslashes($v));
        }
        
        // Cache the member name / member rank info
        if ( @sizeof($member_ranks) == 0 )
        {
            $sql = 'SELECT r.rank_name, m.member_name
                    FROM ' . MEMBER_RANKS_TABLE . ' r, ' . MEMBERS_TABLE . ' m
                    WHERE (r.rank_id = m.member_rank_id)
                    ORDER BY m.member_name';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $member_ranks[ $row['member_name'] ] = 'r_' . str_replace(' ', '_', trim($row['rank_name']));
            }
            $db->free_result($result);
        }
        
        $name_check = false;
        $role_check = true;
        $rank_check = true;

	/* New EQ /who format
[Fri Apr 29 20:45:08 2005] Logging to 'eqlog.txt' is now *ON*.
[Fri Apr 29 20:45:09 2005] Players on EverQuest:
[Fri Apr 29 20:45:09 2005] ---------------------------
[Fri Apr 29 20:45:09 2005] [63 Warlock (Necromancer)] Volac (Dark Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Maestro (Bard)] Willowwhisper (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Premus  <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [65 Storm Warden (Druid)] Whispering (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [65 Prophet (Shaman)] Cuzak (Troll) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [64 Virtuoso (Bard)] Case (Half Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Aspenelder  <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [67 Forest Stalker (Ranger)] Hatteras (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [67 Archon (Cleric)] Kelee (High Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Storm Warden (Druid)] Branson (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Overlord (Warrior)] Damilex (Human) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Lord Protector (Paladin)] Hafgun (Human) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [54 Primalist (Beastlord)] Sistakitty (Vah Shir) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [66 Arch Convoker (Magician)] Torgen (High Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [63 Phantasmist (Enchanter)] Merlinycus (Erudite) <Aurora Spirits>
[Fri Apr 29 20:45:09 2005] [66 Forest Stalker (Ranger)] Xlifex (Wood Elf) <Storm Rider Rebellion>
[Fri Apr 29 20:45:09 2005] [ANONYMOUS] Misser  <Storm Rider Rebellion>
	*/
        
        // Date
        $pattern  = "/\[[a-zA-Z]{3} [a-zA-Z]{3} [0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9]{4}\]";
        // AFK
        $pattern .= ".*(AFK )?";
        // Level / Class (if findall or findrole is set, we can check for ANONYMOUS people, too)
        // $pattern .= ( (isset($_POST['findall'])) || (isset($_POST['findrole'])) ) ? "\[(ANONYMOUS|([0-9]{1,2})(.+))\]" : "\[([0-9]{1,2})(.+)\]";
        $pattern .= ( (isset($_POST['findall'])) || (isset($_POST['findrole'])) ) ? "\[(ANONYMOUS|([0-9]{1,2})(.+) (.+))\]" : "\[([0-9]{1,2})(.+) (.+)\]";
        // Name
        $pattern .= " ([A-Za-z]{1,})";
        // Race
        $pattern .= "( \(.*\))?";
        // Guild (ignored if we're finding EVERYONE in the log, regardless of tag)
        if ( !isset($_POST['findall']) )
        {
            $guildtag_sep = '';
            $pattern .= ".*\<(";
            foreach ( $parsetags as $guildtag )
            {
                if ( isset($_POST[str_replace(' ', '_', $guildtag)]) )
                {
                    $pattern .= $guildtag_sep . $guildtag;
                    $guildtag_sep = '|';
                }
            }
            $pattern .= ")\>";
        }
        $pattern .= '/';
        
        if ( preg_match($pattern, $log_line, $log_parsed) )
        {
            // 0 = full string 
            // 1 = AFK?
            // 2 = ANONYMOUS | 'XX Class (archtype)'
            // 3 = Level
            // 4 = Class
            // 5 = (Archtype)
	    // 6 = Name
            // 7 = (Race)

	    $log_parsed[5] = str_replace('_', ' ', $log_parsed[5]);
            $name  = trim($log_parsed[6]);
            $level = trim($log_parsed[3]);
            $class = trim($log_parsed[4]);
            $race  = ( isset($log_parsed[7]) ) ? trim(str_replace(')', '', str_replace('(', '', $log_parsed[7]))) : '';
            $archtype  = ( isset($log_parsed[5]) ) ? trim(str_replace(')', '', str_replace('(', '', $log_parsed[5]))) : '';

	    if ( $archtype == 'Shadow' ) {
	 	 $archtype = 'Shadow Knight';
	    }
	
	    if ( isset($log_parsed[2]) && ($log_parsed[2] == 'ANONYMOUS')) {

		$class = "Unknown";
		$archtype = "Unknown";
		$race = "Unknown";
		$level = '1';
	        
		$log_parsed[2] = 'Unknown' ;

	    }
		
	 
            if ( !isset($_POST['findrole']) )
            {
                if ( (isset($log_parsed[2])) && ($log_parsed[2] == 'ANONYMOUS') )
                {
                    $role_check = false;
                }
            }
         
            if ( (isset($log_parsed[6])) && ($log_parsed[6] != '') )
            {
                $name_check = true;
            }
            
            // Check if we're including this member's rank
            if ( isset($member_ranks[$name]) )
            {
                // If POST[r_<rank_name>] isn't set, we're ignoring this member
                if ( !isset($_POST[ $member_ranks[$name] ]) )
                {
                    $rank_check = false;
                }
            }
            
            if ( ($name_check) && ($role_check) && ($rank_check) )
            {
                $_SESSION[$name] = array(
                    'name'  => $name,
                    'level' => $level,
                    'class' => $archtype,
                    'race'  => $race);
                    
                return $log_parsed[6];
            }
        }
        return false;
    }
    
    function M_to_n($m)
    {
        switch($m)
        {
            case 'Jan':
                return '01';
                break;
            case 'Feb':
                return '02'; 
                break;
            case 'Mar':
                return '03'; 
                break;
            case 'Apr':
                return '04'; 
                break;
             case 'May': 
                return '05';
                break;
             case 'Jun':
                return '06';
                break;
             case 'Jul':
                return '07'; 
                break;
             case 'Aug': 
                return '08';
                break;
             case 'Sep':
                return '09'; 
                break;
             case 'Oct': 
                return '10';
                break;
             case 'Nov':
                return '11'; 
                break;
             case 'Dec':
                return '12';
                break;
        }
    }
    
    function original_class($class)
    {
        $classes = array(
            'Bard'          => array('Bard','Minstrel','Troubadour','Virtuoso','Maestro'),
            'Beastlord'     => array('Beastlord','Primalist','Animist','Savage Lord','Feral Lord'),
            'Berserker'     => array('Berserker','Brawler','Vehement','Rager','Fury'),
            'Cleric'        => array('Cleric','Vicar','Templar','High Priest','Archon'),
            'Druid'         => array('Druid','Wanderer','Preserver','Hierophant','Storm Warden'),
            'Enchanter'     => array('Enchanter','Illusionist','Beguiler','Phantasmist','Coercer'),
            'Magician'      => array('Magician','Elementalist','Conjurer','Arch Mage','Arch Convoker'),
            'Monk'          => array('Monk','Disciple','Master','Grandmaster','Transcendent'),
            'Necromancer'   => array('Necromancer','Heretic','Defiler','Warlock','Arch Lich'),
            'Paladin'       => array('Paladin','Cavalier','Knight','Crusader','Lord Protector'),
            'Ranger'        => array('Ranger','Pathfinder','Outrider','Warder','Hunter','Forest Stalker'),
            'Rogue'         => array('Rogue','Rake','Blackguard','Assassin','Deceiver'),
            'Shadow Knight' => array('Scourge Knight','Shadow Knight','Reaver','Revenant','Grave Lord','Dread Lord'),
            'Shaman'        => array('Shaman','Mystic','Luminary','Oracle','Prophet'),
            'Warrior'       => array('Warrior','Champion','Myrmidon','Warlord','Overlord'),
            'Wizard'        => array('Wizard','Channeler','Evoker','Sorcerer','Arcanist')
        );
        
        foreach ( $classes as $k => $v)
        {
            if ( in_array($class, $v) )
            {
                return $k;
            }
        }
        
        return false;
    }
    
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $log_columns = ( preg_match("/Mozilla\/4\.[1-9]{1}.+/", $_SERVER['HTTP_USER_AGENT']) ) ? '50' : '90';
        
        // Options to parse
        $options = array(
            0 => array(
                'CBNAME'    => 'findall',
                'CBVALUE'   => '1',
                'CBCHECKED' => '',
                'OPTION'    => $user->lang['log_find_all']),
            1 => array(
                'CBNAME'    => 'findrole',
                'CBVALUE'   => '1',
                'CBCHECKED' => ' checked="checked"',
                'OPTION'    => 'Include Roleplay')
        );
        
        // Guildtags to parse
        if ( !empty($eqdkp->config['parsetags']) )
        {
            $parsetags = explode("\n", $eqdkp->config['parsetags']);
            foreach ( $parsetags as $index => $guildtag )
            {
                $tagoptions[] = array(
                    'CBNAME'    => str_replace(' ', '_', trim($guildtag)),
                    'CBVALUE'   => '1',
                    'CBCHECKED' => ' checked="checked"',
                    'OPTION'    => '&lt;' . trim($guildtag) . '&gt;');
            }
            $options = array_merge($options, $tagoptions);
        }
        
        foreach ( $options as $row )
        {
            $tpl->assign_block_vars('options_row', $row);
        }
        
        // Member tags to parse
        // Find out how many members have each rank
        $rank_counts = array();
        $sql = 'SELECT member_rank_id, count(member_rank_id) as count
                FROM ' . MEMBERS_TABLE . '
                GROUP BY member_rank_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $rank_counts[ $row['member_rank_id'] ] = $row['count'];
        }
        $db->free_result($result);
        
        $ranks = array();
        $sql = 'SELECT rank_id, rank_name, rank_prefix, rank_suffix
                FROM ' . MEMBER_RANKS_TABLE . '
                ORDER BY rank_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            // Make sure there's not a guildtag with the same name as the rank
            if ( !in_array($row['rank_name'], $options) )
            {
                $rank_count = ( isset($rank_counts[ $row['rank_id'] ]) ) ? $rank_counts[ $row['rank_id'] ] : 0;
                $format = ( $rank_count == 1 ) ? $user->lang['x_members_s'] : $user->lang['x_members_p'];
                
                $ranks[] = array(
                    'CBNAME'    => 'r_' . str_replace(' ', '_', trim($row['rank_name'])),
                    'CBVALUE'   => intval($row['rank_id']),
                    'CBCHECKED' => ' checked="checked"',
                    'OPTION'    => $user->lang['rank'] . ': ' . (( empty($row['rank_name']) ) ? '(None)' : $row['rank_prefix'] . $row['rank_name'] . $row['rank_suffix'])
                                   . ' <span class="small">(' . sprintf($format, $rank_count) . ')</span>');
            }
        }
        $db->free_result($result);
        
        foreach ( $ranks as $row )
        {
            $tpl->assign_block_vars('ranks_row', $row);
        }
        
        $tpl->assign_vars(array(
            'F_PARSE_LOG'    => 'parse_Everquest.php' . $SID,
            
            'S_STEP1'        => true,
            'L_PASTE_LOG'    => $user->lang['paste_log'],
            'L_OPTIONS'      => $user->lang['options'],
            'L_PARSE_LOG'    => $user->lang['parse_log'],
            'L_CLOSE_WINDOW' => $user->lang['close_window'],
            
            'LOG_COLS' => $log_columns)
        );
        
        $eqdkp->set_vars(array(
            'page_title'        => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['parselog_title'],
            'gen_simple_header' => true,
            'template_file'     => 'admin/parse_Everquest.html',
            'display'           => true)
        );
    }
}

$parse_log = new Parse_Log;
$parse_log->process();
?>
