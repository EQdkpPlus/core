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

class RaidPlan_View_Member extends EQdkp_Admin
{
	var $classes = array();
	var $races = array();
	var $members = array();
	var $ranks = array();
	var $extra_css = "";
	var	$user_id = '';
	var	$username = '';

    // ---------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------
    function RaidPlan_View_Member()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();
		$user_id = $_GET['user_id'];

		//
		// Get classes
		//
		$sql = "SELECT class_id, class_name
			FROM " . CLASS_TABLE . " as classes
			ORDER BY class_id";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->classes[$row['class_id']] = array(
				'id'		=> $row['class_id'],
				'name'		=> $row['class_name'],
			);
		}
		$db->free_result($result);

		//
		// Get ranks
		//
		$sql = "SELECT rank_id, rank_name, rank_prefix, rank_suffix
			FROM " . MEMBER_RANKS_TABLE . " as ranks
			ORDER BY rank_id";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->ranks[$row['rank_id']] = array(
				'id'		=> $row['rank_id'],
				'name'		=> $row['rank_name'],
				'prefix'	=> $row['rank_prefix'],
				'suffix'	=> $row['rank_suffix'],
			);
		}
		$db->free_result($result);

		//
		// Get races
		//
		$sql = "SELECT race_id, race_name
			FROM " . RACE_TABLE . " as races
			ORDER BY race_id";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->races[$row['race_id']] = array(
				'id'		=> $row['race_id'],
				'name'		=> $row['race_name'],
			);
		}
		$db->free_result($result);

		
		//
		// Get Users Charakters
		//
		$sql = 'SELECT username
	   			FROM ' . USERS_TABLE . "
	   			WHERE user_id='" . $user_id . "'";
				$result = $db->query($sql);
		while($data = $db->fetch_record($result))
		{
			$username = $data['username'];
		}
		$db->free_result($result);

		$TEST1 = $username;

		$sql = 'SELECT member_id
	   			FROM ' . MEMBER_USER_TABLE . "
	   			WHERE user_id='" .$user_id."'";
	   	$result = $db->query($sql);
		while($data = $db->fetch_record($result))
		{
			$TEST2 .= $data['member_id'];
		  	$sql2 = "SELECT *
			 		 FROM ".MEMBERS_TABLE." as characters
			 		 WHERE member_id=".$data['member_id']."
					 ORDER BY member_id";
			$result2 = $db->query($sql2);
			while ($row = $db->fetch_record($result2))
			{
				$this->members[$row['member_id']] = array(
				'id'		=> $row['member_id'],
				'name'		=> $row['member_name'],
				'race_id'	=> $row['member_race_id'],
				'class_id'	=> $row['member_class_id'],
				'rank_id'	=> $row['member_rank_id'],
				'level'		=> $row['member_level'],
				);
			}
			$db->free_result($result2);
		}

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'u_raidplan_view'))
        );
		$tpl->assign_vars(array(
			'USERNAME'					=> $username,
		));
		
    }


    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
		global $rp_use_plugin_css_file, $rp_show_ranks, $rp_use_roll_system, $rp_use_wildcard_system, $rp_show_full;


		foreach ($this->classes as $class)
		{
			$tpl->assign_block_vars('classes', array(
				'ID'		=> $class['id'],					// ID of the class
				'NAME'		=> $class['name']					// Name of the class
				));	
		}
		foreach ($this->races as $race)
		{
			$tpl->assign_block_vars('races', array(
				'ID'		=> $race['id'],					// ID of the class
				'NAME'		=> $race['name']					// Name of the class
				));	
		}
		foreach ($this->ranks as $rank)
		{
			$tpl->assign_block_vars('ranks', array(
				'ID'		=> $rank['id'],					
				'NAME'		=> $rank['name'],	
				'PREFIX'	=> $rank['prefix'],	
				'SUFFIX'	=> $rank['suffix']				
				));	
		}
		foreach ($this->members as $member)
		{
			$tpl->assign_block_vars('members', array(
					'ID'		=> $member['id'],
					'NAME'		=> $member['name'],
					'RACE'		=> $this->races[$member['race_id']]['name'],
					'RACE_EN'	=> strtolower(convert_racesname($this->races[$member['race_id']]['name'])),
					'RANK'		=> $this->ranks[$member['rank_id']]['name'],
					'CLASS'		=> $this->classes[$member['class_id']]['name'],
					'CLASS_EN'	=> convert_classname($this->classes[$member['class_id']]['name']),
					'LEVEL'		=> $member['level']
				));	
		}




        $tpl->assign_vars(array(
			// Form
			'F_ADD_RAID'				=> "viewraid.php" . $SID,
            'RAID_ID'					=> $this->url_id,
			// Data
			'RAID_ADDED_BY'				=> $this->raid['raid_added_by'],
			'RAID_DATE'					=> strftime($user->lang['rp_time_format'],$this->raid['raid_date']),
			'RAID_NAME'					=> $this->raid['raid_name'],
			'RAID_NOTE'					=> $this->raid['raid_note'],
			'RAID_SIGNUP_DEADLINE'		=> date($user->style['date_time'], $this->raid['raid_date_subscription']),
			'RAID_VALUE'				=> $this->raid['raid_value'],
			'ATTENDEES_COLSPAN'			=> count($this->classes),
			
			// Switches
			'S_SHOW_RANKS'				=> $rp_show_ranks,
			'S_SHOW_ROLL'				=> $rp_use_roll_system,
			'S_SHOW_WILDCARD'			=> $rp_use_wildcard_system,

			// Submit Buttons
			'B_CHANGE'					=> $user->lang['update'],
			'B_SIGNOFF'					=> $user->lang['rp_signoff'],
			'B_SIGNUP'					=> $user->lang['rp_signup'],
			'B_NOTAVAIL'				=> $user->lang['rp_notavail'],
	
			// Language
			'L_VIEW_RAID_TITLE'			=> $user->lang['rp_raidplaner'],
			'L_Name'           => $user->lang['name'],
			'L_RANK'           => $user->lang['rp_rank'],
			'L_RACE'           => $user->lang['race'],
			'L_LEVEL'          => $user->lang['level'],
			'L_CLASS'          => $user->lang['rp_class'],
			'L_CHARS_OF'       => $user->lang['rp_chars_of'],
			'L_ADDED_BY'				=> $user->lang['added_by'],
			'L_ATTENDEES'				=> $user->lang['attendees'],
			'L_CONFIRMED'				=> $user->lang['rp_confirmed'],
			'L_DATE'					=> $user->lang['date'],
			'L_NOTE'					=> $user->lang['note'],
			'L_RAID'					=> $user->lang['raid'],
			'L_ROLLED'					=> $user->lang['rp_rolled'],
			'L_SIGNED'					=> $user->lang['rp_signed'],
			'L_SIGNEDOUT'				=> $user->lang['rp_signedout'],
			'L_SIGNUP_DEADLINE'			=> $user->lang['rp_signup_deadline'],
			'L_VALUE'					=> $user->lang['value'],
			'L_TIME'					=> $user->lang['time'],
			'L_NOT_LOGGED_IN'			=> $user->lang['rp_not_logged_in'],
			'L_NO_USER_ASSIGNED'		=> $user->lang['rp_no_user_assigned'],
			'L_WILDCARD'				=> $user->lang['rp_wildcard']
		));
		
		if ($rp_use_plugin_css_file)
		{
			$this->extra_css = "";
			$extra_css_file = $eqdkp_root_path . $pm->get_data('raidplan', 'template_path') . $user->style['template_path'] . "/stylesheet.css";
			
			if (file_exists($extra_css_file))
			{
				$filehandle = fopen($extra_css_file, "r");
				while (!feof($filehandle)) {
					$this->extra_css .= fgets($filehandle);
				}
				fclose ($filehandle);
			}
		}

		$eqdkp->set_vars(array(
			'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rp_raidplaner'],
			'template_file' => 'viewmember.html',
			'template_path' => $pm->get_data('raidplan', 'template_path'),
			'extra_css'		=> $this->extra_css,
            'display'       => true)
        );

	}

	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------
}

$myRaidList = new RaidPlan_View_Member;
$myRaidList->process();
?>
