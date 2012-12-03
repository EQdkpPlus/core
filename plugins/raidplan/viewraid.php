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

// Get the plugin
$raidplan = $pm->get_plugin(PLUGIN);

//set local
setlocale (LC_TIME, $user->lang['rp_local_format']);

class RaidPlan_View_Raid extends EQdkp_Admin
{
	var $raid = array();
	var $classes = array();
	var $attendees = array();
	var $members = array();
	var $extra_css = "";
	
    // ---------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------
    function RaidPlan_View_Raid()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();

		$this->set_vars(array(
			'confirm_text'			=> $user->lang['rp_confirm_delete_subscription'],
      'uri_parameter'			=> URI_RAID,
			'subscribed_member_id'	=> 'subscribed_member_id',
		));
		
    if (!$this->url_id) { message_die($user->lang['error_invalid_raid_provided']); }
		
		$sql = 'SELECT raid_id, raid_name, raid_date, raid_date_subscription, raid_date_invite, raid_note, raid_value, raid_added_by
				FROM (' . RP_RAIDS_TABLE . ")
				WHERE raid_id='" . $this->url_id . "'";
		$result = $db->query($sql);
		if (!$row = $db->fetch_record($result)) { message_die($user->lang['error_invalid_raid_provided']); }
		$db->free_result($result);

		$this->raid = array(
			'raid_added_by'						=> post_or_db('raid_added_by', $row),
			'raid_name'								=> post_or_db('raid_name', $row),
			'raid_date'								=> post_or_db('raid_date', $row),
			'raid_date_subscription'	=> post_or_db('raid_date_subscription', $row),
			'raid_date_invite'				=> post_or_db('raid_date_invite', $row),
			'raid_note'								=> post_or_db('raid_note', $row),
			'raid_value'							=> post_or_db('raid_value', $row),
			'raid_subscribed'					=> false,
		);

		if (!$this->raid['raid_value']) { $this->raid['raid_value'] = $this->get_raid_value($this->raid['raid_name']); }

		//
		// Get classes
		//
		$sql = "SELECT class_name, class_count
			FROM (" . RP_CLASSES_TABLE . " as classes)
			WHERE raid_id='" . $this->url_id . "'
			ORDER BY class_name";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->classes[$row['class_name']] = array(
				'name'		=> $row['class_name'],
				'name_en'	=> convert_classname($row['class_name']),
				'count'		=> $row['class_count'],
			);
		}
		$db->free_result($result);

		//
		// Get attendees
		//
		$sql = "SELECT attendees.member_id, members.member_name, class_name, confirmed, members.member_status, member_user.user_id, attendees.attendees_subscribed, attendees.attendees_random, attendees.attendees_note, attendees.attendees_signup_time, wildcards.wildcard, ranks.rank_name, ranks.rank_prefix, ranks.rank_suffix
			FROM (" . RP_ATTENDEES_TABLE . " as attendees, " . MEMBERS_TABLE . " as members, " . MEMBER_USER_TABLE . " as member_user, " . CLASS_TABLE . " as classes, " . USERS_TABLE . " as users, " . MEMBER_RANKS_TABLE . " as ranks)
			LEFT OUTER JOIN " . RP_WILDCARD_TABLE . " as wildcards ON (users.username=wildcards.user_name)
			WHERE raid_id='" . $this->url_id . "'
			AND attendees.member_id=members.member_id
			AND members.member_id=member_user.member_id
			AND classes.class_id=members.member_class_id
			AND member_user.user_id=users.user_id
			AND members.member_rank_id = ranks.rank_id";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			    $this->attendees[$row['member_id']] = array(
        'id'            => $row['member_id'],
        'name'            => (($row['member_status'] == '0') ? '<i>' . $row['member_name'] . '</i>' : $row['member_name']),
				'class_name'	=> $row['class_name'],
				'confirmed'		=> $row['confirmed'],
				'subscribed'	=> $row['attendees_subscribed'],
				'user_id'		=> $row['user_id'],
				'random'		=> $row['attendees_random'],
				'rank'			=> $row['rank_name'],
				'rank_prefix'	=> $row['rank_prefix'],
				'rank_suffix'	=> $row['rank_suffix'],
				'note'			=> $row['attendees_note'],
				'signup_time'  	=> strftime($user->lang['rp_time_format'],$row['attendees_signup_time']),
				'wildcard'		=> $row['wildcard'],
        'member_status' => $row['member_status']);
		}
        $db->free_result($result);
		
		//
		// Get members
		//
		if ($user->data['user_id'])
		{
			$sql = "SELECT users.user_id, members.member_id, members.member_name, classes.class_name, attendees.raid_id, attendees.confirmed, attendees.attendees_subscribed, attendees.attendees_random, attendees.attendees_note
				FROM (" . MEMBERS_TABLE . " as members, " . MEMBER_USER_TABLE . " as users, " . CLASS_TABLE . " as classes) 
				LEFT JOIN " . RP_ATTENDEES_TABLE . " as attendees
				ON (members.member_id=attendees.member_id AND attendees.raid_id=" . $this->url_id . ")
				WHERE classes.class_name IN ('" . $this->join_array("','", $this->classes, "name") . "')
				AND members.member_class_id=classes.class_id
				AND members.member_id=users.member_id
				AND users.user_id=" . $user->data['user_id'];
			$result = $db->query($sql);
			while ($row = $db->fetch_record($result))
			{
				$this->members[$row['member_id']] = array(
					'id'			=> $row['member_id'],
					'name'			=> $row['member_name'],
					'class_name'	=> $row['class_name'],
					'subscribed'	=> $row['attendees_subscribed'],
					'confirmed'		=> $row['confirmed'],
					'random'		=> $row['attendees_random'],
					'note'			=> $row['attendees_note']);

				// if($row['attendees_subscribed'] == 1 && $row['attendees_random']>0) { $this->raid['raid_subscribed'] = true; }
				if($row['attendees_subscribed'] == 1) { $this->raid['raid_subscribed'] = true; }
			}
			$db->free_result($result);
		}

        $this->assoc_buttons(array(
            'signup' => array(
                'name'    => 'signup',
                'process' => 'process_signup',
                'check'   => 'u_raidplan_view'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'u_raidplan_view'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'u_raidplan_view'),
            'unsign' => array(
                'name'    => 'unsign',
                'process' => 'process_unsign',
                'check'   => 'u_raidplan_view'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'u_raidplan_view'))
        );
    }

// walle testmod
    // ---------------------------------------------------------
    // Process Unsign
    // ---------------------------------------------------------
    function process_unsign()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_msg = "";
		$link_list = array(
            $user->lang['view'] . " " . $user->lang['raid']	=> 'viewraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'listraids.php' . $SID,
		);

		if ($_POST[URI_RAID]=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$link_list[$user->lang['view_raid']] = "";
			$this->admin_die($success_msg, $link_list);
		}

		if ($_POST['member_id']=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$this->admin_die($success_msg, $link_list);			
		}

		//
		// Check if the member subscribed earlier
		//
		$sql = "SELECT  raid_id, member_id, attendees_subscribed, confirmed, attendees_random, attendees_signup_time
				FROM (" . RP_ATTENDEES_TABLE . ")
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'";
		$result = $db->query($sql);
		if ($row = $db->fetch_record($result))
		{
			// Member subscribed earlier
			// Change subscription status
			$query = $db->build_query('UPDATE', array(
				'attendees_subscribed'	=> 2,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'");
		}
		else
		{
			// This is the first subscription 
			srand((double)microtime()*1000000);
			$query = $db->build_query('INSERT', array(
				'raid_id'				=> stripslashes($_POST[URI_RAID]),
				'member_id'     		=> stripslashes($_POST['member_id']),
				'attendees_subscribed'	=> 2,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_random'		=> rand(1,100),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('INSERT INTO ' . RP_ATTENDEES_TABLE . $query);
		}

		$success_msg = sprintf($user->lang['rp_raid_signed'], "", "");
			
		$this->admin_die($success_msg, $link_list);
    }
//end walle testmod



    // ---------------------------------------------------------
    // Process Sign-Up
    // ---------------------------------------------------------
    function process_signup()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_msg = "";
		$link_list = array(
            $user->lang['view'] . " " . $user->lang['raid']	=> 'viewraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'listraids.php' . $SID,
		);

		if ($_POST[URI_RAID]=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$link_list[$user->lang['view_raid']] = "";
			$this->admin_die($success_msg, $link_list);
		}

		if ($_POST['member_id']=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$this->admin_die($success_msg, $link_list);			
		}

		//
		// Check if the member subscribed earlier
		//
		$sql = "SELECT  raid_id, member_id, attendees_subscribed, confirmed, attendees_random, attendees_signup_time
				FROM (" . RP_ATTENDEES_TABLE . ")
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'";
		$result = $db->query($sql);
		if ($row = $db->fetch_record($result))
		{
			// Member subscribed earlier
			// Change subscription status
			$query = $db->build_query('UPDATE', array(
				'attendees_subscribed'	=> 1,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'");
		}
		else
		{
			// This is the first subscription 
			srand((double)microtime()*1000000);
			$query = $db->build_query('INSERT', array(
				'raid_id'				=> stripslashes($_POST[URI_RAID]),
				'member_id'     		=> stripslashes($_POST['member_id']),
				'attendees_subscribed'	=> 1,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_random'		=> rand(1,100),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('INSERT INTO ' . RP_ATTENDEES_TABLE . $query);
		}

		$success_msg = sprintf($user->lang['rp_raid_signed'], "", "");
			
		$this->admin_die($success_msg, $link_list);
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_msg = "";
		$link_list = array(
            $user->lang['view'] . " " . $user->lang['raid']	=> 'viewraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'listraids.php' . $SID,
		);

		if ($_POST[URI_RAID]=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$link_list[$user->lang['view_raid']] = "";
			$this->admin_die($success_msg, $link_list);			
		}

		if ($_POST['member_id']=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$this->admin_die($success_msg, $link_list);			
		}

		if ($_POST['subscribed_member_id'] == $_POST['member_id'])
		{
          	$query = $db->build_query('UPDATE', array(
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'");
			$success_msg = $user->lang['rp_member_allready_subscribed'].'Note updated!';
			$this->admin_die($success_msg, $link_list);
		}

		//
		// Check if the member subscribed earlier
		//
		$sql = "SELECT  raid_id, member_id, attendees_subscribed, confirmed, attendees_random, attendees_signup_time
				FROM (" . RP_ATTENDEES_TABLE . ")
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'";
		$result = $db->query($sql);
		if ($row = $db->fetch_record($result))
		{
			// Member subscribed earlier
			// Change subscription status           
			$query = $db->build_query('UPDATE', array(
				'attendees_subscribed'	=> 1,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
				WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
				AND member_id='" . stripslashes($_POST['member_id']) . "'");
		}
		else
		{
            // die("Not found");
			// This is the first subscription with this member
            srand((double)microtime()*1000000);
			$query = $db->build_query('INSERT', array(
				'raid_id'				=> stripslashes($_POST[URI_RAID]),
				'member_id'     		=> stripslashes($_POST['member_id']),
				'attendees_subscribed'	=> 1,
				'confirmed'				=> 0,
				'attendees_signup_time' => time(),
				'attendees_random'		=> rand(1,100),
				'attendees_note'		=> stripslashes($_POST['signupnote'])));
			$db->query('INSERT INTO ' . RP_ATTENDEES_TABLE . $query);
		}

		//
		// Deactivate old subscription
		//
        $query = $db->build_query('UPDATE', array(
            'attendees_subscribed'	=> 0,
            'confirmed'				=> 0));
		$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
			WHERE raid_id='" . stripslashes($_POST[URI_RAID]) . "'
			AND member_id='" . stripslashes($_POST['subscribed_member_id']) . "'");

		$success_msg = sprintf($user->lang['rp_raid_signed'], "", "");
		$this->admin_die($success_msg, $link_list);
	}

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_msg = "";
		$link_list = array(
            $user->lang['view'] . " " . $user->lang['raid']	=> 'viewraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'listraids.php' . $SID,
		);

		if ($_POST[URI_RAID]=="")
		{
			$success_msg = $user->lang['error_invalid_name_provided'];
			$link_list[$user->lang['view_raid']] = "";
			$this->admin_die($success_msg, $link_list);
		}

		foreach ($this->members as $member)
		{
			if ($member['subscribed']==1)
			{
				if ($this->raid['raid_date_subscription']>time())
				{
					$query = $db->build_query('UPDATE', array(
							'attendees_subscribed'	=> 2));
					$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
						WHERE raid_id='" . $this->url_id . "'
						AND member_id='" . $member['id'] . "'");
				} else {
					$sql = "SELECT  attendees_random
						FROM " . RP_ATTENDEES_TABLE . "
						WHERE raid_id='" . $this->url_id . "'
						AND member_id='" . $member['id'] . "'";
					$result = $db->query($sql);
					if (!$row = $db->fetch_record($result)) { message_die($user->lang['error_invalid_raid_provided']); }
					$db->free_result($result);

					$query = $db->build_query('UPDATE', array(
							'attendees_random'	=> $row['attendees_random'] * -1));
					$db->query('UPDATE ' . RP_ATTENDEES_TABLE . ' SET ' . $query . "
						WHERE raid_id='" . $this->url_id . "'
						AND member_id='" . $member['id'] . "'");
				}
				$success_msg .= sprintf($user->lang['rp_raid_signup_deleted'], $member['name']);
			}
		}

		$this->admin_die($success_msg, $link_list);
	}

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
		global $rp_use_plugin_css_file, $rp_show_ranks, $rp_use_roll_system, $rp_use_wildcard_system, $rp_show_full, $rp_short_ranks;

		// For each class in the database
		foreach ($this->classes as $class)
		{
			// If this class should be in the raid
			if ($class['count'] > 0)
			{
				// Array for signed and confirmed attendees
				$confirmed_attendees = array();

				// Array for signed and NOT confirmed attendees
				$signed_attendees = array();
				
				// Array for unsigned attendees
				$unsigned_attendees = array();

				// For each attendee of this raid
				foreach ($this->attendees as $attendee)
				{
					// If attendee belongs to this class
					if ($attendee['class_name'] == $class['name'])
					{
						// Look if user has signed an earlier raid
						$sql = "SELECT count(*)
								FROM (" . RP_RAIDS_TABLE . " as raids, " . RP_ATTENDEES_TABLE . " as attendees, " .
								MEMBER_USER_TABLE . " as member_user)
								WHERE raids.raid_id = attendees.raid_id
								AND attendees.member_id = member_user.member_id
								AND member_user.user_id = " . $attendee['user_id'] . "
								AND raids.raid_date > " . time() . "
								AND raids.raid_date < " . $this->raid['raid_date'] . "
								AND attendees.attendees_subscribed = 1";
						$result = $db->query($sql);
						if (!$row = $db->fetch_record($result)) { message_die($user->lang['error_invalid_raid_provided']); }

						// If the user has signed a earlier raid than this, do not show the wildcard
						$showNoWildcard = ($row[0] > 0) ? true : false;

						// If the attendee belongs to this class and has signed in and has been confirmed
						if ($attendee['confirmed'] == 1 && $attendee['subscribed'] == 1)
						{
							// Put him to the "confirmed array"
							$confirmed_attendees[] = array(
								'link'			=> "viewmember.php" . $SID . "&amp;user_id=" . $attendee['user_id'],
								'name'			=> $attendee['name'],
								'random'		=> $attendee['random'],
								'note'			=> $attendee['note'],
								'rank'			=> $attendee['rank'],
								'member_status' => $attendee['member_status'],
								'rank_prefix'	=> $attendee['rank_prefix'],
								'rank_suffix'	=> $attendee['rank_suffix'],
								'time'			=> $attendee['signup_time'],
								'wildcard'		=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
						}
						// Else if the attendee belongs to this class and has signed in
						elseif ($attendee['subscribed'] == 1)
						{
							// Put him to the "signed array"
							$signed_attendees[] = array(
								'link'			=> "viewmember.php" . $SID . "&amp;user_id=" . $attendee['user_id'],
								'name'			=> $attendee['name'],
								'random'		=> $attendee['random'],
								'note'			=> $attendee['note'],
								'rank'			=> $attendee['rank'],
								'member_status' => $attendee['member_status'],
								'rank_prefix'	=> $attendee['rank_prefix'],
								'rank_suffix'	=> $attendee['rank_suffix'],
								'time'			=> $attendee['signup_time'],
								'wildcard'		=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
						}
						elseif ($attendee['subscribed'] == 2)
						{
							// Put him to the "unsigned array"
							$unsigned_attendees[] = array(
								'link'			=> "viewmember.php" . $SID . "&amp;user_id=" . $attendee['user_id'],
								'name'			=> $attendee['name'],
								'random'		=> $attendee['random'],
								'note'			=> $attendee['note'],
								'rank'			=> $attendee['rank'],
								'member_status' => $attendee['member_status'],
								'rank_prefix'	=> $attendee['rank_prefix'],
								'rank_suffix'	=> $attendee['rank_suffix'],
								'time'			=> $attendee['signup_time'],
								'wildcard'		=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
						}
					}
				}

				// Write data to template
				$tpl->assign_block_vars('classes', array(
					'ID'		=> $class['id'],					// ID of the class
					'NAME'		=> $class['name'],					// Name of the class
					'NAME_EN'	=> $class['name_en'],
					'COUNT'		=> $class['count'],					// Count of the class
					'CONFIRMED'	=> count($confirmed_attendees),		// Count of the signed in and confirmed attendees
					'SIGNED'	=> count($signed_attendees),			// Count of the signed in attendees
					'UNSIGNED'	=> count($unsigned_attendees)			// Count of the signed in attendees
				));

				// For each signed in and confirmed attendee
				foreach ($confirmed_attendees as $confirmed_attendee)
				{
				$linkvero="";
        if ($confirmed_attendee['member_status'] == "0"){
          $linkvero = str_replace("<i>", "", $confirmed_attendee['link']);
          $linkvero = str_replace("</i>", "", $linkvero);
        }else{
          $linkvero = $confirmed_attendee['link'];
        }
					// Write data to template
					$tpl->assign_block_vars('classes.confirmed_attendees', array(
						'LINK'        => $linkvero,
						'NAME'			  => $confirmed_attendee['name'],
						'RANDOM'		  => $confirmed_attendee['random'],
						'NOTE'			  => $confirmed_attendee['note'],
						'RANK'			  => $confirmed_attendee['rank'],
						'RANK_SUFFIX'	=> $confirmed_attendee['rank_suffix'],
						'RANK_PREFIX'	=> $confirmed_attendee['rank_prefix'],
						'TIME'			  => $confirmed_attendee['time'],
						'S_WILDCARD'	=> ($confirmed_attendee['wildcard']) ? true : false));
				}

				// For each signed in attendee
				foreach ($signed_attendees as $signed_attendee)
				{
				$linkvero="";
        if ($signed_attendee['member_status'] == "0"){
          $linkvero = str_replace("<i>", "", $signed_attendee['link']);
          $linkvero = str_replace("</i>", "", $linkvero);
        }else{
          $linkvero = $signed_attendee['link'];
        }
					$tpl->assign_block_vars('classes.signin_attendees', array(
						'LINK'            => $linkvero,
						'NAME'			=> $signed_attendee['name'],
						'RANDOM'		=> $signed_attendee['random'],
						'NOTE'			=> $signed_attendee['note'],
						'RANK'			=> $signed_attendee['rank'],
						'RANK_SUFFIX'	=> $signed_attendee['rank_suffix'],
						'RANK_PREFIX'	=> $signed_attendee['rank_prefix'],
						'TIME'			=> $signed_attendee['time'],
						'S_WILDCARD'	=> ($signed_attendee['wildcard']) ? true : false));
				}
				
					// For each unsigned in attendee
				foreach ($unsigned_attendees as $unsigned_attendee)
				{
				$linkvero="";
        if ($confirmed_attendee['member_status'] == "0"){
          $linkvero = str_replace("<i>", "", $confirmed_attendee['link']);
          $linkvero = str_replace("</i>", "", $linkvero);
        }else{
          $linkvero = $confirmed_attendee['link'];
        }
					$tpl->assign_block_vars('classes.unsigned_attendees', array(
						'LINK'            => $linkvero,
						'NAME'			=> $unsigned_attendee['name'],
						'RANDOM'		=> $unsigned_attendee['random'],
						'NOTE'			=> $unsigned_attendee['note'],
						'RANK'			=> $unsigned_attendee['rank'],
						'RANK_SUFFIX'	=> $unsigned_attendee['rank_suffix'],
						'RANK_PREFIX'	=> $unsigned_attendee['rank_prefix'],
						'TIME'			=> $unsigned_attendee['time'],
						'S_WILDCARD'	=> ($unsigned_attendee['wildcard']) ? true : false));
				}
			}
		}

		// If user is allowed to sign in and has members assigned
		if ($user->check_auth('u_raidplan_view', false) && count($this->members)>0)
		{
			$subscribed_member_id = '';
			$signin_member_confirmed = false;
			$signin_member_random = 0;
			
			foreach ($this->members as $member)
			{
				$tpl->assign_block_vars('members', array(
					'VALUE'		=> $member['id'],
					'NAME'		=> $member['name'],
					'CLASS'		=> $member['class_name'],
          'SELECTED'	=> ($member['subscribed']==1 or $member['subscribed']==2) ? ' selected ' : '',
				));
				if ($member['subscribed']==1)
				{
					$tpl->assign_vars(array(
						'SUBSCRIBED_MEMBER_ID'	=> $member['id']));
					$signin_member_confirmed = $member['confirmed'];
					$signin_member_random = $member['random'];
				}

			}

			// If sign in date is NOT expired
			if ($this->raid['raid_date_subscription']>time())
			{
				// If the user already signed in
				if ($this->raid['raid_subscribed'])
				{
					if ($signin_member_random > 0)
					{
						$tpl->assign_vars(array(
							'S_SUBSCRIPTION'	=> false,
							'S_UPDATE'			=> true,
							'S_DELETE'			=> true
						));
					} else {
						$tpl->assign_vars(array(
							'S_SUBSCRIPTION'	=> false,
							'S_UPDATE'			=> false,
							'S_DELETE'			=> false
						));
					}
				}
				else
				{
					$tpl->assign_vars(array(
						'S_SUBSCRIPTION'	=> true,
						'S_UPDATE'			=> false,
						'S_DELETE'			=> false
					));
				}
			} else {	// If sign in date is expired
				// If the user already signed in
				if ($this->raid['raid_subscribed'])
				{
					// User is allowed to cancel a confirmed subscription but only one
					if ($signin_member_confirmed && $signin_member_random>0)
					{
						$tpl->assign_vars(array(
							'S_SUBSCRIPTION'	=> false,
							'S_UPDATE'			=> false,
							'S_DELETE'			=> true
						));
					}
				}
			}
		}else{ $tpl->assign_vars(array( 'S_NO_USER_ASSIGN' => true)); }

		If (count($this->classes)>0)
		{ $tpl->assign_vars(array( 'COLUMN_WIDTH' => str_replace(',','.',100/count($this->classes)),
								   'L_NO_USER_ASSIGNED'=> $user->lang['rp_no_user_assigned'],
		)); }else
		{ $tpl->assign_vars(array( 'COLUMN_WIDTH' => str_replace(',','.',100),
								   'L_NO_USER_ASSIGNED'=> $user->lang['rp_class_distribution_not_set'],
			));}

				
        $tpl->assign_vars(array(
			// Form
			'F_ADD_RAID'					=> "viewraid.php" . $SID,
            'RAID_ID'						=> $this->url_id,
			// Data
			'RAID_ADDED_BY'					=> $this->raid['raid_added_by'],
			'RAID_DATE'						=> strftime($user->lang['rp_time_format'], $this->raid['raid_date']),
			'RAID_DATE_INVITE'				=> strftime($user->lang['rp_time_format'], $this->raid['raid_date_invite']),
			'RAID_NAME'						=> $this->raid['raid_name'],
			'RAID_NOTE'						=> $this->raid['raid_note'],
			'RAID_SIGNUP_DEADLINE'			=> strftime($user->lang['rp_time_format'], $this->raid['raid_date_subscription']),
			'RAID_VALUE'					=> $this->raid['raid_value'],
			'ATTENDEES_COLSPAN'				=> count($this->classes),

			// Switches
			'S_SHOW_RANKS'					=> $rp_show_ranks,
			'S_SHORT_RANKS'					=> $rp_short_ranks,
			'S_SHOW_ROLL'					=> $rp_use_roll_system,
			'S_SHOW_WILDCARD'				=> $rp_use_wildcard_system,

			// Submit Buttons
			'B_CHANGE'						=> $user->lang['update'],
			'B_SIGNOFF'						=> $user->lang['rp_signoff'],
			'B_SIGNUP'						=> $user->lang['rp_signup'],
			'B_UNSIGN'          			=> $user->lang['rp_bunsign'],

			// Language
			'L_VIEW_RAID_TITLE'				=> $user->lang['rp_raidplaner'],
			'L_ADDED_BY'					=> $user->lang['added_by'],
			'L_ATTENDEES'					=> $user->lang['attendees'],
			'L_CONFIRMED'					=> $user->lang['rp_confirmed'],
			'L_DATE'						=> $user->lang['date'],
			'L_INVITE_TIME'					=> $user->lang['rp_invite_time'],
			'L_START_TIME'					=> $user->lang['rp_start_time'],
			'L_NOTE'						=> $user->lang['note'],
			'L_RAID'						=> $user->lang['raid'],
			'L_CHAR'         				=> $user->lang['rp_char'],
			'L_ROLLED'						=> $user->lang['rp_rolled'],
			'L_VERSION'						=> $pm->get_data('raidplan', 'version'),
			'L_SIGNED'						=> $user->lang['rp_signed'],
			'L_UNSIGNED'       				=> $user->lang['rp_unsigned'],
			'L_SIGNUP_DEADLINE'				=> $user->lang['rp_signup_deadline'],
			'L_VALUE'						=> $user->lang['value'],
			'L_TIME'						=> $user->lang['time'],
			'L_NOT_LOGGED_IN'				=> $user->lang['rp_not_logged_in'],
			'L_WILDCARD'					=> $user->lang['rp_wildcard'],
			'L_NOTE_HEADER'    				=> $user->lang['rp_note_header'],
			'L_TIME_HEADER'    				=> $user->lang['rp_time_header'],
			'L_AUTOJOIN_LINKN' 				=> $user->lang['rp_autonv_link']
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
			'template_file' => 'viewraid.html',
			'template_path' => $pm->get_data('raidplan', 'template_path'),
			'extra_css'		=> $this->extra_css,
      'display'       => true)
        );

	}

	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------
	/**
	* Determine this raid's value
	* 
	* @param $raid_name
	* @return string Raid value
	*/
    function get_raid_value($raid_name)
    {
        global $db;
        
        // Check if they entered a one-time value; Get the preset value of the raid(s) if not
        if ( empty($_POST['raid_value']) )
        {
            $raid_value = $db->query_first('SELECT event_value FROM ' . EVENTS_TABLE . " WHERE event_name='" . addslashes($raid_name) . "'");
        }
        else
        {
            $raid_value = $_POST['raid_value'];
        }
    
        // Still no post value?
        if ( empty($raid_value) )
        {
            $raid_value = '0.00';
        }
        
        return $raid_value;
    }

	function join_array($glue, $pieces, $dimension = 0)
	{
		// print ("DEBUG: \$dimension=" . $dimension . "<br />");
		$rtn = array();
		foreach($pieces as $key => $value)
		{
			// print ("DEBUG: \$key=" . $key . "<br />");
			// print ("DEBUG: \$value=" . $value . "<br />");
			if(isset($value[$dimension]))
			{
				$rtn[] = $value[$dimension];
			}
		}
		return join($glue, $rtn);
	} 
}

$myRaidList = new RaidPlan_View_Raid;
$myRaidList->process();
?>
