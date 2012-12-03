<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * parse_Everquest2.php
 * Began: Sun February 04 2007
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
/*

[10:42:54]Anaryon: Level 9 Ranger - Leth Nurae
[10:42:54] Janaar: Level 8 Paladin - Leth Nurae
[10:42:54] Carathien: Level 8 Cleric - Leth Nurae
[10:42:54] Stonay: Level 11 Dread Knight (The Unholy Inquisition) - Leth Nurae
[10:42:54] Traxs: Level 14 Ranger - Leth Nurae
[10:42:54] Kellermann: Level 11 Cleric (The Unholy Inquisition) - Leth Nurae
[10:42:54] Aerwrynn: Level 6 Psionicist - Leth Nurae
[10:42:54] Nexiana: Level 8 Ranger - Leth Nurae
[10:42:54] Metis: Level 10 Psionicist - Leth Nurae
[10:42:54] Lancevent: Level 6 Psionicist (Alliance des Seigneurs) - Leth Nurae
[10:42:54] Arteah: Level 5 Blood Mage - Leth Nurae
[10:42:54] Ormung: Level 9 Shaman - Leth Nurae
[10:42:54] Schnabbo: Level 6 Bard - Leth Nurae
[10:42:54] Taung: Level 7 Cleric - Leth Nurae
[10:42:54] Marsosis: Level 8 Paladin - Leth Nurae
[10:42:54] Ariam: Level 7 Bard - Leth Nurae
[10:42:54] Nagyon: Level 6 Ranger - Leth Nurae
[10:42:54] Lillial: Level 7 Ranger - Leth Nurae
[10:42:54] Prook: Level 7 Sorcerer - Leth Nurae
[10:42:54] Maleek: Level 12 Paladin - Leth Nurae
[10:42:54] Kaeia: Level 15 Ranger - Leth Nurae
[10:42:54] Burro: Level 21 Druid (Knights of the Tribunal) - Leth Nurae
[10:42:54] Liadora: Level 6 Bard - Leth Nurae
[10:42:54] Maschelie: Level 4 Psionicist - Leth Nurae
[10:42:54] Tiltwait: Level 9 Blood Mage - Leth Nurae
[10:42:54] Mythera: Level 11 Psionicist - Leth Nurae
[10:42:54] Tulanthas: Level 7 Ranger - Leth Nurae
[10:42:54] Saelnir: Level 11 Sorcerer - Leth Nurae
[10:42:54] Wizor: Level 4 Sorcerer - Leth Nurae
[10:42:54] Tylara: Level 6 Sorcerer - Leth Nurae
[10:42:54] Silverana: Level 5 Psionicist - Leth Nurae
[10:42:54] Morowind: Level 11 Psionicist - Leth Nurae
[10:42:54] Gaddinger: Level 5 Ranger - Leth Nurae
[10:42:54] Domulus: Level 7 Blood Mage - Leth Nurae
[10:42:54] Emperios: Level 8 Cleric - Leth Nurae
[10:42:54] Silanis: Level 5 Ranger - Leth Nurae
[10:42:54] Carnon: Level 5 Paladin - Leth Nurae
[10:42:54] Edakor: Level 3 Sorcerer - Leth Nurae
[10:42:54] Arkanidall: Level 4 Sorcerer - Leth Nurae
[10:42:54] Sendaran: Level 9 Sorcerer - Leth Nurae
[10:42:54] 40 player(s) found.

*/
    function process_parse()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $channel_members  = '';
        $line             = '';
        $valid_date_found = false;
        
        $log_file = explode("\n", $_POST['log']);
        $log_file = str_replace('&lt;', '<', str_replace('&gt;', '>', $log_file));
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

			//Each member parse starts here
                $member_name = $this->line_parse($log_file[$i]);

                // Make sure that each member's name is properly capitalized
                $mname = strtolower(preg_replace('/[[:space:]]/i', ' ', $member_name));
                $member_name = ucwords($mname);

                if ( trim($member_name) != '')
                {
                    $member_names[] = $member_name;
                }
			//End member parse
            
        } // for ... log_file
        
        // If there were channel members, join the two arrays
        if ( !empty($channel_members) )
        {
            $channel_members = explode(', ', $channel_members);
            $member_names = array_merge($member_names, $channel_members);
        }
        
        $date['mo'] = date('M');
        $date['d']  = date('d');
        $date['y']  = date('Y');
        $date['h']  = date('h');
        $date['mi'] = date('i');
        $date['s']  = date('s');
        
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
            'template_file'     => 'admin/parse_Everquest2.html',
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
        
        //$log_line = stripslashes($log_line);

		// First off, we don't care about afk or lfg, so remove them
		$log_line = str_replace(' (AFK)', '', $log_line);
		$log_line = str_replace(' (LFG)', '', $log_line);

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

/* Sample SoH log file format ::

[10:42:54]Anaryon: Level 9 Ranger - Leth Nurae
[10:42:54] Janaar: Level 8 Paladin - Leth Nurae
[10:42:54] Carathien: Level 8 Cleric - Leth Nurae
[10:42:54] Stonay: Level 11 Dread Knight (The Unholy Inquisition) - Leth Nurae
[10:42:54] Traxs: Level 14 Ranger - Leth Nurae
[10:42:54] Kellermann: Level 11 Cleric (The Unholy Inquisition) - Leth Nurae
[10:42:54] Aerwrynn: Level 6 Psionicist - Leth Nurae
[10:42:54] Nexiana: Level 8 Ranger - Leth Nurae
[10:42:54] Metis: Level 10 Psionicist - Leth Nurae
[10:42:54] Lancevent: Level 6 Psionicist (Alliance des Seigneurs) - Leth Nurae
[10:42:54] Arteah: Level 5 Blood Mage - Leth Nurae

*/

		// Date / Time
        $pattern   = "/^(?:\[[0-9]{1,2}\:[0-9]{2}\:[0-9]{2}\])";

        // Name
        $pattern  .= "\s*?([A-Za-z]{1,}):";

        // Level / Class 
        $pattern  .= "\s*?Level ([0-9]{1,2}) ([^-\(]+)";

        // Guild Tag
		$pattern .= "\s*?(?:\((.*?)\))?";

        // Zone:
        $pattern  .= "\s*?- (.*)";

		// End pattern regexp
        $pattern .= '/';


		// unset a line match type identifier
		unset($type_line);
	
		// match things the hard way..one type of line at a time
		// crappy way to do it, but VERY easy to read and alter
	
		// Normal	
		if ( preg_match($pattern, $log_line, $log_parsed) ) 
		{ 
			$name = trim($log_parsed[1]);
			$level = trim($log_parsed[2]);
			$class = trim($log_parsed[3]);
			$guildtag = ( isset($log_parsed[5]) ? trim($log_parsed[4]) : '' );
			$zone = ( isset($log_parsed[5]) ? trim($log_parsed[5]) : trim($log_parsed[4]) );
			$type_line = 1;
		}
	

		if ($type_line == 1) {
			print"Name: $name , Level: $level , Class: $class , Guild: $guildtag , Zone: $zone<br>";
		} 


		if ( (isset($name)) && ($name != '') )
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
				'tag'   => $guildtag,
				'name'  => $name,
				'level' => $level,
				'class' => $class,
				'race'  => $race);
				
			return $name;
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
    
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $log_columns = ( preg_match("/Mozilla\/4\.[1-9]{1}.+/", $_SERVER['HTTP_USER_AGENT']) ) ? '50' : '90';

		$options = array();

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
            'F_PARSE_LOG'    => 'parse_Vanguard-SoH.php' . $SID,
            
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
            'template_file'     => 'admin/parse_Vanguard-SoH.html',
            'display'           => true)
        );
    }
}

$parse_log = new Parse_Log;
$parse_log->process();
?>
