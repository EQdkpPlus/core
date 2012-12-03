<?php
/******************************
 * EQdkp Raid Planner
 * Copyright 2005 by A.Stranger
 * ------------------
 * addraid.php
 * Began: Fri August 26 2005
 * Changed: Fri September 30 2005
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../../';
include_once('../config.php');


// Check if plugin is installed
if (!$pm->check(PLUGIN_INSTALLED, 'raidplan')) { message_die('The Raid Planer plugin is not installed.'); }

// Check user permission
$user->check_auth('a_raidplan_');

// Get the plugin
$raidplan = $pm->get_plugin(PLUGIN);

class RPAddRaid extends EQdkp_Admin
{
	var $raid = array();
	var $classes = array();
	var $attendees = array();
	var $extra_css = "";
	
    // ---------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------
    function RPAddRaid()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();

		$this->set_vars(array(
			'confirm_text'  => $user->lang['confirm_delete_raid'],
            'uri_parameter' => URI_RAID)
        );

		//
		// Build class list
		//		
        $sql = 'SELECT class_name, class_min_level, class_max_level
				FROM (' . CLASS_TABLE . ')
				GROUP BY class_name ORDER BY class_name';
		if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
		while ($row = $db->fetch_record($result))
		{
			$this->classes[$row['class_name']] = array(
				'id'				=> $row['class_id'],
				'name'			=> $row['class_name'],
				'name_en'		=> convert_classname($row['class_name']),
				'count'			=> 0);
		}
		$db->free_result($class_result);
		
		if ((isset($_GET[URI_RAID])) && (intval($_GET[URI_RAID] > 0)))
		{
		    $sql = 'SELECT raid_id, raid_name, raid_date, raid_date_invite, raid_date_subscription, raid_note, raid_value, raid_attendees, raid_added_by, raid_updated_by
					FROM (' . RP_RAIDS_TABLE . ")
					WHERE raid_id='".$_GET[URI_RAID]."'";

			if (!($result = $db->query($sql))) { message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql); }
		
			// Check for valid raid
			if (!$raid = $db->fetch_record($result))
			{
				message_die($user->lang['error_invalid_raid_provided']);
			}
			$db->free_result($result);
			$this->raid = $raid;
			
			// Get class counts
			// $sql = 'SELECT class_index, count FROM (' . RP_CLASSES_TABLE . ") WHERE raid_id='".$_GET[URI_RAID]."'";
			$sql = 'SELECT class_name, class_count
					FROM (' . RP_CLASSES_TABLE . ")
					WHERE raid_id='".$_GET[URI_RAID]."'";

			if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
			while ($row = $db->fetch_record($result))
			{
				$this->classes[$row['class_name']]['count'] = $row['class_count'];
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
					'id'					=> $row['member_id'],
					'name'				=> (($row['member_status'] == '0') ? '<i>' . $row['member_name'] . '</i>' : $row['member_name']),
					'class_name'	=> $row['class_name'],
					'confirmed'		=> $row['confirmed'],
					'subscribed'	=> $row['attendees_subscribed'],
					'user_id'			=> $row['user_id'],
					'random'			=> $row['attendees_random'],
					'rank'				=> $row['rank_name'],
					'note'				=> $row['attendees_note'],
					'wildcard'		=> $row['wildcard'],
          'member_status' => $row['member_status']);
			}
			$db->free_result($result);
		}

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_raidplan_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_raidplan_update'),
            'allupdate' => array(
                'name'    => 'allupdate',
                'process' => 'process_allupdate',
                'check'   => 'a_raidplan_update'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_raidplan_delete'),
            'wildcard' => array(
                'name'    => 'wildcard',
                'process' => 'process_wildcard',
                'check'   => 'a_raidplan_update'),
            'distribution' => array(
                'name'    => 'distribution',
                'process' => 'process_distribution',
                'check'   => 'a_raidplan_update'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'u_raidplan_view')
			));
    }

    // ---------------------------------------------------------
    // Form error check
    // ---------------------------------------------------------
	function error_check()
    {
        global $user;
        
		setlocale(LC_ALL, 'de_DE');
        $this->fv->is_number('raid_attendees_count', $user->lang['fv_required_attendees']);
  
        if ( !empty($_POST['raid_value']) )
        {
            $this->fv->is_number('raid_value', $user->lang['fv_number_value']);
        }
    
        if ( (@empty($_POST['raid_name'])) || (@sizeof($_POST['raid_name']) == 0) )
        {
            $this->fv->errors['raid_name'] = $user->lang['fv_required_event_name'];
        }
        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $rp_email_members_raid;

		$success_message = "";
		
        //
        // Raid loop for multiple events
        //
        foreach ($_POST['raid_name'] as $raid_name)
		{
			$this_raid_id = 0;
			
      //
      // Get the raid value
      //
      $raid_value = $this->get_raid_value($raid_name);
			
			//
			// Build raid date
			//
			$raid_date 							= $_POST['raid_date_input'];
			$raid_date_invite 			= $_POST['raid_date_inv_input'];
			$raid_subscription_date = $_POST['raid_date_sub_input'];

      //
      // Insert the raid
      //
      $query = $db->build_query('INSERT', array(
                'raid_id'									=> 'NULL',
                'raid_name'								=> stripslashes($raid_name),
								'raid_date'								=> $raid_date,
								'raid_date_invite'				=> $raid_date_invite,
								'raid_date_subscription'	=> $raid_subscription_date,
                'raid_note'								=> stripslashes($_POST['raid_note']),
                'raid_value'							=> $raid_value,
                'raid_added_by'						=> $this->admin_user,
								'raid_attendees'					=> stripslashes($_POST['raid_attendees_count']),
			));
            $db->query('INSERT INTO ' . RP_RAIDS_TABLE . $query);
            $this_raid_id = $db->insert_id();

			// Insert class counts
			while (list ($key, $val) = each ($_POST)) {
				if (preg_match('/^(raid_class_.+_count)$/', $key, $match))
				{
					if ($val > 0)
					{
						$class_name = preg_split('/_/', $key);
						$query = $db->build_query('INSERT', array(
							'raid_id'		=> $this_raid_id,
							'class_name'	=> $class_name[2],
							'class_count'	=> $val)
						);
						$db->query('INSERT INTO ' . RP_CLASSES_TABLE . $query);
					}
				}
			}
		}
		
		// walle send Email Mod
       if ($rp_email_members_raid == true) {
         $sql = 'SELECT user_email, username FROM (' . USERS_TABLE . ')';
               if (!($result = $db->query($sql))) { message_die('Could not obtain user email information', '', __FILE__, __LINE__, $sql); }
                       while ($row = $db->fetch_record($result))
                       {
           // Build the server path
            $script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($eqdkp->config['server_path']));
            $script_name = ( $script_name != '' ) ? $script_name . '/' : '';
            $server_name = trim($eqdkp->config['server_name']);
            $server_port = ( intval($eqdkp->config['server_port']) != 80 ) ? ':' . trim($eqdkp->config['server_port']) . '/' : '/';
            $RP_server_url  = 'http://' . $server_name . $server_port . $script_name;
        // Email a notice
        //
        include_once('../includes/class_email_mod.php');
        $email = new EMail;
        
        $headers = "From: " . $eqdkp->config['admin_email'] . "\nReturn-Path: " . $eqdkp->config['admin_email'] . "\r\n";
        
        $email->set_template('new_raidplan_raid', $row['user_lang']);
        $email->address(stripslashes($row['user_email']));
        $email->subject(); // Grabbed from the template itself
        $email->extra_headers($headers);
        
        $email->assign_vars(array(
            'DKP_NAME'   => $eqdkp->config['dkp_name'],
            'USERNAME'   => $row['username'],
            'RAID_NAME'  => stripslashes($raid_name),
            'RAID_NOTE'  => stripslashes($_POST['raid_note']),
            'ADMIN_USER' => $this->admin_user,
            'RAID_LINK'  => $RP_server_url."plugins/raidplan/viewraid.php?s=&r=".$this_raid_id, 
            'DATE'       => date($user->style['date_notime_short'], $raid_date)
            )
        );
        $email->send();
        $email->reset();
                       }
                       $db->free_result($result);

       }
		// End Walle Send Email Mod

        //
        // Success message
        //
		$success_message .= sprintf($user->lang['rp_update_raid_success'], stripslashes($_POST['raid_date_input']), $raid_name) . '<br />';
        $link_list = array(
            $user->lang['add_raid']			=> 'addraid.php' . $SID,
            $user->lang['update_raid']	=> 'addraid.php' . $SID . '&amp;r=' . $this_raid_id,
            $user->lang['list_raids']		=> 'index.php' . $SID);
		$this->admin_die($success_message, $link_list);
    }

     // ---------------------------------------------------------
    // Process Update All Members
    // ---------------------------------------------------------

     function process_allupdate()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

		$success_message = "";

//add all signed members to the raid
		if (isset($this->url_id)){
		  
			 $sql = "SELECT attendees.confirmed, attendees.attendees_random, attendees.member_id as member_id, raid_id
				FROM " . RP_ATTENDEES_TABLE . " as attendees, " . MEMBERS_TABLE. " as members
				WHERE raid_id='" . $this->url_id . "'
				AND attendees.member_id=members.member_id";

			if (!($result = $db->query($sql))) { message_die('Could not obtain attendees information', '', __FILE__, __LINE__, $sql); }
			if (!$row = $db->fetch_record($result)) { message_die($user->lang['error_invalid_raid_provided']); }
			$db->free_result($result);

            //set all attendees confirmed
			$sql = "UPDATE " . RP_ATTENDEES_TABLE . "
						SET confirmed='1'
						WHERE raid_id='" . $row['raid_id']  . "'
						AND attendees_subscribed=1";
            $db->query($sql);
        }
		else
		{
			if (!isset($this->url_id))		{ message_die($user->lang['error_invalid_raid_provided']); }
		}
		redirect('plugins/'.PLUGIN.'/admin/addraid.php' . $SID . '&r=' . $this->url_id);
		
		}

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_message = "";

		//
		// Get the raid value
		//
		$raid_value = $this->get_raid_value($_POST['raid_name']);

		//
		// Build raid date
		//
			$raid_date 							= $_POST['raid_date_input'];
			$raid_date_invite 			= $_POST['raid_date_inv_input'];
			$raid_subscription_date = $_POST['raid_date_sub_input'];

		//
		// Update the raid
		//
		$query = $db->build_query('UPDATE', array(
			'raid_name'								=> stripslashes($_POST['raid_name']),
			'raid_date'								=> $raid_date,
			'raid_date_invite'				=> $raid_date_invite,
			'raid_date_subscription'	=> $raid_subscription_date,
			'raid_note'								=> stripslashes($_POST['raid_note']),
			'raid_value'							=> $raid_value,
			'raid_updated_by'					=> $this->admin_user,
			'raid_attendees'					=> stripslashes($_POST['raid_attendees_count']),
		));
		$db->query('UPDATE ' . RP_RAIDS_TABLE . ' SET ' . $query . " WHERE raid_id='" . $this->url_id . "'");

		// Delete class counts
		$db->query('DELETE FROM (' . RP_CLASSES_TABLE . ") WHERE raid_id='" . $this->url_id . "'");

		// Insert class counts
		while (list ($key, $val) = each ($_POST)) {
			if (preg_match('/^(raid_class_.+_count)$/', $key, $match))
			{
				if ($val > 0)
				{
					$class_name = preg_split('/_/', $key);
					$query = $db->build_query('INSERT', array(
						'raid_id'		=> $this->url_id,
						'class_name'	=> $class_name[2],
						'class_count'	=> $val)
					);
					$db->query('INSERT INTO ' . RP_CLASSES_TABLE . $query);
				}
			}
		}

        //
        // Success message
        //
		$success_message .= sprintf($user->lang['rp_update_raid_success'], stripslashes($_POST['raid_date_input']), $raid_name) . '<br />';
        $link_list = array(
            $user->lang['add_raid']		=> 'addraid.php' . $SID,
            $user->lang['update_raid']	=> 'addraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'index.php' . $SID);
		$this->admin_die($success_message, $link_list);
	}

    // ---------------------------------------------------------
    // Process Wildcard
    // ---------------------------------------------------------
    function process_wildcard()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

		$success_message = "";

		$sql = "SELECT users.username, attendees.confirmed, attendees.attendees_subscribed
				FROM (" . RP_ATTENDEES_TABLE . " as attendees, " . MEMBER_USER_TABLE . " as member_user, " . USERS_TABLE . " as users)
				WHERE raid_id='" . $this->url_id . "'
				AND attendees.member_id=member_user.member_id
				AND member_user.user_id=users.user_id
                AND attendees.attendees_subscribed=1";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->attendees[$row['username']] = array(
				'user_name'		=> $row['username'],
				'confirmed'		=> $row['confirmed'],
				'subscribed'	=> $row['attendees_subscribed']);
		}
		$db->free_result($result);

		foreach ($this->attendees as $attendee)
		{
			if ($attendee['confirmed'] == 1 && $attendee['subscribed'] == 1)
			{
				$sql = 'DELETE FROM (' . RP_WILDCARD_TABLE . ") WHERE user_name='" . $attendee['user_name'] . "'";
				$db->query($sql);
                $success_message .= $attendee['user_name'] . " looses his/her wildcards.<br />";
			}
			elseif ($attendee['subscribed'] == 1)
			{
				$query = $db->build_query('INSERT', array(
					'user_name'					=> $attendee['user_name'],
					'wildcard'					=> 1));
				$sql = 'INSERT INTO ' . RP_WILDCARD_TABLE . $query;
				$db->query($sql);
                $success_message .= $attendee['user_name'] . " get a wildcards.<br />";
			}
		}

        //
        // Success message
        //
		$success_message .= "Wildcards assigned.";
        $link_list = array(
            $user->lang['add_raid']		=> 'addraid.php' . $SID,
            $user->lang['update_raid']	=> 'addraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']	=> 'index.php' . $SID);
		$this->admin_die($success_message, $link_list);
	}

	// ---------------------------------------------------------
    // Process class distribution
    // ---------------------------------------------------------
    function process_distribution()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
		$success_message = "";
		
	if(is_array($_POST['raid_name'])){
    foreach ($_POST['raid_name'] as $raid_name)
		{
			// Delete old set
			$sql = 'DELETE FROM (' . RP_CLASS_DIST_TABLE . ") WHERE event_name='" . $_POST['raid_name'] . "'";
			$db->query($sql);
			
			// Add new set
			while (list ($key, $val) = each ($_POST)) {
				if (preg_match('/^(raid_class_.+_count)$/', $key, $match))
				{
					if ($val > 0)
					{
						$class_name = preg_split('/_/', $key);
						$query = $db->build_query('INSERT', array(
							'event_name'	=> stripslashes($raid_name),
							'class_name'	=> $class_name[2],
							'class_count'	=> $val)
						);
						$db->query('INSERT INTO ' . RP_CLASS_DIST_TABLE . $query);
					}
				}
			}
		}
		$success_message = $user->lang['rp_class_distribution_set'];
  }else{
    $success_message = $user->lang['rp_class_distribution_notset'];
  }

        //
        // Success message
        //
		
        $link_list = array(
            $user->lang['add_raid']				=> 'addraid.php' . $SID,
            $user->lang['update_raid']		=> 'addraid.php' . $SID . '&amp;r=' . $this->url_id,
            $user->lang['list_raids']			=> 'index.php' . $SID);
			$this->admin_die($success_message, $link_list);
	}

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        //
        // Get the old data
        //
        $this->get_old_data();

		//
		// Delete attendees
		//
		$db->query('DELETE FROM ' . RP_ATTENDEES_TABLE . " WHERE raid_id='" . $this->url_id . "'");

		//
		// Delete classes
		//
		$db->query('DELETE FROM ' . RP_CLASSES_TABLE . " WHERE raid_id='" . $this->url_id . "'");

		//
		// Delete raid
		//
		$db->query('DELETE FROM ' . RP_RAIDS_TABLE . " WHERE raid_id='" . $this->url_id . "'");

   	//
    // Success message
    //
    $success_message = $user->lang['admin_delete_raid_success'];

        $link_list = array(
            $user->lang['add_raid']   => 'addraid.php' . $SID,
            $user->lang['list_raids'] => 'index.php' . $SID);
        $this->admin_die($success_message, $link_list);
	}

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
		global $rp_use_plugin_css_file, $rp_show_ranks;

		//set local
		setlocale (LC_TIME, $user->lang['rp_local_format']);
		
		//
		// Build javascript for distribution sets
		//
        $sql = 'SELECT *
				FROM ' . RP_CLASS_DIST_TABLE;
		if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
		$class_distribution_set = array();
		while ($row = $db->fetch_record($result))
		{
			$class_distribution_set[$row['event_name']][$row['class_name']] = $row['class_count'];
		}
		$db->free_result($class_result);

		while (list ($key, $val) = each ($class_distribution_set))
		{
			$tpl->assign_block_vars('events', array(
						'NAME'		=> $key));		// Name of the event
			while (list ($key2, $val2) = each ($val))
			{
				$tpl->assign_block_vars('events.classes', array(
					'NAME'		=> $key2,			// Name of the class
					'COUNT'		=> $val2));			// Count of the class
			}
		}

		// print("<pre>"); print_r($tpl->_tpldata); print("</pre>"); die();
		// print_r($class_distribution_set);

		//
		// Build class list
		//
        $sql = 'SELECT class_name, class_id, class_min_level, class_max_level
				FROM (' . CLASS_TABLE . ')
				GROUP BY class_name
				ORDER BY class_name';
		if (!($result = $db->query($sql))) { message_die('Could not obtain class information', '', __FILE__, __LINE__, $sql); }
		while ($row = $db->fetch_record($result))
		{
			$tpl->assign_block_vars('raid_classes', array(
				'LABEL'			=> $row['class_name'],
				'NAME'			=> "raid_class_" . $row['class_name'] . "_count",
				'NAME_EN'		=> convert_classname($row['class_name']),
				'COUNT'			=> (isset($this->classes[$row['class_name']])) ? $this->classes[$row['class_name']]['count'] : 0,
			));
		}
		$db->free_result($class_result);

        //
        // Build event drop-down
        //
        $max_value = $db->query_first('SELECT max(event_value) FROM ' . EVENTS_TABLE);
        $float = @explode('.', $max_value);
        $format = '%0' . @strlen($float[0]) . '.2f';

        $sql = 'SELECT event_id, event_name, event_value
                FROM (' . EVENTS_TABLE . ')
                ORDER BY event_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $select_check = ( is_array($this->raid['raid_name']) ) ? in_array(stripslashes($row['event_name']), $this->raid['raid_name']) : stripslashes($row['event_name']) == $this->raid['raid_name'];

            $tpl->assign_block_vars('events_row', array(
                'VALUE'    => stripslashes($row['event_name']),
                'SELECTED' => ( $select_check ) ? ' selected="selected"' : '',
                'OPTION'   => '(' . sprintf($format, $row['event_value']) . ') - ' . stripslashes($row['event_name']))
            );
        }
        $db->free_result($result);

		//
		// Update Raid?
		//
		if ($this->url_id)
		{
			// For each class in the database
			foreach ($this->classes as $class)
			{
				// If this class should be in the raid
				if ($class['count'] > 0)
				{
					// Array for signed and confirmed attendees
					$confirmed_attendees = array();

					// Array for signed and NOT confirmed attendees
					$subscribed_attendees = array();
					$signin_attendees = array();
					
					$unsigned_attendees = array();

					// Count of confirmed attendees
					$confirmed_attendee = 0;

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
									'link'			=> $eqdkp_root_path . "viewmember.php" . $SID . "&amp;name=" . $attendee['name'],
									'name'			=> $attendee['name'],
									'random'		=> $attendee['random'],
									'rank'			=> $attendee['rank'],
									'member_status' => $attendee['member_status'],
									'note'			=> $attendee['note'],
									'wildcard'	=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
							}
							// Else if the attendee belongs to this class and has signed in
							elseif ($attendee['subscribed'] == 1)
							{
								// Put him to the "signin array"
								// $subscribed_attendees[] = array(
								$signin_attendees[] = array(
									'link'			=> $eqdkp_root_path . "viewmember.php" . $SID . "&amp;name=" . $attendee['name'],
									'name'			=> $attendee['name'],
									'random'		=> $attendee['random'],
									'rank'			=> $attendee['rank'],
									'member_status' => $attendee['member_status'],
									'note'			=> $attendee['note'],
									'wildcard'	=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
							}
							// Else if the attendee belongs to this class and has signed in
							elseif ($attendee['subscribed'] == 2)
							{
								// Put him to the "unsigned array"
								$unsigned_attendees[] = array(
									'link'			=> $eqdkp_root_path . "viewmember.php" . $SID . "&amp;name=" . $attendee['name'],
									'name'			=> $attendee['name'],
									'random'		=> $attendee['random'],
									'rank'			=> $attendee['rank'],
									'member_status' => $attendee['member_status'],
									'note'			=> $attendee['note'],
									'wildcard'	=> (!is_null($attendee['wildcard']) && ! $showNoWildcard) ? true : false);
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
						'SIGNIN'	=> count($signin_attendees),			// Count of the signed in attendees
						'UNSIGNED' => count($unsigned_attendees)
					));

					// For each signed in and confirmed attendee
					foreach ($confirmed_attendees as $confirmed_attendee)
					{
					$linkvero="";
          $nomevero="";
          if ($confirmed_attendee['member_status'] == "0"){
            $linkvero = str_replace("<i>", "", $confirmed_attendee['link']);
            $linkvero = str_replace("</i>", "", $linkvero);
            $nomevero = str_replace("<i>", "", $confirmed_attendee['name']);
            $nomevero = str_replace("</i>", "", $nomevero);
          }else{
            $nomevero = $confirmed_attendee['name'];
            $linkvero = $confirmed_attendee['link'];
          }
						// Write data to template
						$tpl->assign_block_vars('classes.confirmed_attendees', array(
							'LINK'        => $linkvero,
              'TOGGLE_LINK' => "updatemember.php" . $SID . "&amp;mode=confirm&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
              'UNLOCK_LINK' => "updatemember.php" . $SID . "&amp;mode=unlock&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
							'NAME'				=> $confirmed_attendee['name'],
							'RANDOM'			=> $confirmed_attendee['random'],
							'RANK'				=> $confirmed_attendee['rank'],
							'NOTE'				=> $confirmed_attendee['note'],
							'S_WILDCARD'	=> ($confirmed_attendee['wildcard']) ? true : false));
					}

					// For each signed in attendee
					foreach ($signin_attendees as $signin_attendee)
					{
					$linkvero ="";
          $nomevero="";
          if ($signin_attendee['member_status'] == "0"){
            $linkvero = str_replace("<i>", "", $signin_attendee['link']);
            $linkvero = str_replace("</i>", "", $linkvero);
            $nomevero = str_replace("<i>", "", $signin_attendee['name']);
            $nomevero = str_replace("</i>", "", $nomevero);
          }else{
            $nomevero = $signin_attendee['name'];
            $linkvero = $signin_attendee['link'];
          }
						$tpl->assign_block_vars('classes.signin_attendees', array(
							'LINK'            => $linkvero,
              'TOGGLE_LINK'    => "updatemember.php" . $SID . "&amp;mode=confirm&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
              'UNLOCK_LINK'    => "updatemember.php" . $SID . "&amp;mode=unlock&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
							'NAME'				=> $signin_attendee['name'],
							'RANDOM'			=> $signin_attendee['random'],
							'RANK'				=> $signin_attendee['rank'],
							'NOTE'				=> $signin_attendee['note'],
							'S_WILDCARD'	=> ( $signin_attendee['wildcard'] ) ? true : false));
					}
					// For each signed in attendee
					foreach ($unsigned_attendees as $unsigned_attendee)
					{
					$linkvero ="";
          $nomevero="";
          if ($signin_attendee['member_status'] == "0"){
            $linkvero = str_replace("<i>", "", $signin_attendee['link']);
            $linkvero = str_replace("</i>", "", $linkvero);
            $nomevero = str_replace("<i>", "", $signin_attendee['name']);
            $nomevero = str_replace("</i>", "", $nomevero);
          }else{
            $nomevero = $signin_attendee['name'];
            $linkvero = $signin_attendee['link'];
          }
						$tpl->assign_block_vars('classes.unsigned_attendees', array(
							'LINK'            => $linkvero,
              'TOGGLE_LINK'    => "updatemember.php" . $SID . "&amp;mode=confirm&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
              'UNLOCK_LINK'    => "updatemember.php" . $SID . "&amp;mode=unlock&amp;" . URI_RAID . "=" . $this->url_id . "&amp;name=" . $nomevero,
							'NAME'				=> $unsigned_attendee['name'],
							'RANDOM'			=> $unsigned_attendee['random'],
							'RANK'				=> $unsignedn_attendee['rank'],
							'NOTE'				=> $unsigned_attendee['note'],
							'S_WILDCARD'	=> ( $unsigned_attendee['wildcard'] ) ? true : false));
					}
					
				}
			}
		}

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_RAID'						=> 'addraid.php' . $SID,
            'F_RAID_DATE_INPUT'			=> (isset($this->raid['raid_date'])) ? $this->raid['raid_date'] :
            													 mktime(date("H"), 30, 0, date("m"), date("d")+2, date("Y")),
            'F_RAID_DATE'						=> (isset($this->raid['raid_date'])) ? strftime($user->lang['rp_time_format'], $this->raid['raid_date']) :
            													 strftime($user->lang['rp_time_format'], date(mktime(date("H"), 30, 0, date("m"), date("d")+2, date("Y")))),
            'F_RAID_DATE_INV_INPUT'	=> (isset($this->raid['raid_date_invite'])) ? $this->raid['raid_date_invite'] :
            													 mktime(date("H"), 00, 0, date("m"), date("d")+2, date("Y")),
            'F_RAID_DATE_INV'				=> (isset($this->raid['raid_date_invite'])) ? strftime($user->lang['rp_time_format'], $this->raid['raid_date_invite']) :
            													 strftime($user->lang['rp_time_format'], date(mktime(date("H"), 00, 0, date("m"), date("d")+2, date("Y")))),
            'F_RAID_DATE_SUB_INPUT'	=> (isset($this->raid['raid_date_subscription'])) ? $this->raid['raid_date_subscription']:
            													 mktime(date("H"), 00, 00, date("m"), date("d")+1, date("Y")),
            'F_RAID_DATE_SUB'				=> (isset($this->raid['raid_date_subscription'])) ? strftime($user->lang['rp_time_format'], $this->raid['raid_date_subscription']): 
            													 strftime($user->lang['rp_time_format'], date(mktime(date("H"), 00, 00, date("m"), date("d")+1, date("Y")))),
            
						'F_ATTENDEES_COUNT'	=> $this->raid['raid_attendees'],
            'RAID_ID'			=> $this->url_id,
            'ADDALL_LINK'		=> "updatemember.php" . $SID . "&amp;mode=addall&amp;" . URI_RAID . "=" . $this->url_id ,
            
            
            // 'RAID_VALUE'		=> stripslashes($raid_value),
            'RAID_NOTE'			=> stripslashes(htmlspecialchars($this->raid['raid_note'])),
			
			      'ATTENDEES_COLSPAN'	=> count($this->classes),
			      'COLUMN_WIDTH'		=> str_replace(',','.',100/count($this->classes)),
			      'U_ADD_EVENT'		=> $eqdkp_root_path . 'admin/addevent.php',

			     // Submit Buttons
			      'B_ADD_RAID'						=> $user->lang['add_raid'],
	          'B_DELETE_RAID'					=> $user->lang['delete_raid'],
			      'B_DISTRIBUTE'					=> $user->lang['rp_distribute_class_set'],
			      'B_RESET'								=> $user->lang['reset'],
		      	'B_UPDATE_RAID'					=> $user->lang['update_raid'],
		      	'B_WILDCARD_RAID'				=> $user->lang['rp_wildcard_raid'],

		      	// Switches
		       	'S_ADD'									=> (!$this->url_id) ? true : false,
		      	'S_CLASSVIEW'						=> (!$this->url_id) ? true : false,
		      	'S_EVENT_MULTIPLE'			=> (!$this->url_id) ? true : false,
		      	'S_SHOW_RANKS'					=> $rp_show_ranks,
		      	'S_WILDCARD'						=> ( $this->url_id && $this->raid['raid_date'] < time() ) ? true : false,

		      	// Lables
		      	'L_ADD_EVENT'						=> $user->lang['add_event'],
		      	'L_ADD_RAID_TITLE'			=> $user->lang['rp_raidplaner'] . ": " . $user->lang['addraid_title'],
		      	'L_ADDRAID_VALUE_NOTE'	=> $user->lang['addraid_value_note'],
		      	'L_ATTENDEES'						=> $user->lang['attendees'],
		      	'L_CLASSES'							=> $user->lang['class_distribution'],
			      'L_CONFIRMED'						=> $user->lang['rp_confirmed'],
			
		      	'L_START_TIME'					=> $user->lang['rp_start_time'],
		      	'L_INVITE_TIME'					=> $user->lang['rp_invite_time'],
			
		      	//Diese beiden Zeilen können nach erfolgreichem Einbau des Kalenders entfernt werden.	(evt.)		
		      	'L_TIME'								=> $user->lang['time'],
		      	'L_DATE'								=> $user->lang['date'],
			
		      	'L_EVENT'								=> $user->lang['event'],
		      	'L_NOTE'								=> $user->lang['note'],
		      	'L_ROLLED'							=> $user->lang['rp_rolled'],
		      	'L_SIGNED'							=> $user->lang['rp_signed'],
		      	'L_UNSIGNED'        		=> $user->lang['rp_unsigned'],
		      	'L_SIGNUP_DEADLINE'			=> $user->lang['rp_signup_deadline'],
			
		      	//Diese beiden Zeilen können nach erfolgreichem Einbau des Kalenders entfernt werden.
		      	'L_SIGNUP_DEADLINE_DATE'=> $user->lang['rp_signup_deadline_date'],
		      	'L_SIGNUP_DEADLINE_TIME'=> $user->lang['rp_signup_deadline_time'],
			
		      	'L_VALUE'								=> $user->lang['value'],
			      'L_WILDCARD'						=> $user->lang['rp_wildcard'],
		      	'L_ADDALL'							=> $user->lang['rp_add_all'],
		
		      	//Calendar Settings
		      	'L_CAL_LANG'						=> $user->lang['rp_calendar_lang'],

           // Form validation
           'FV_ATTENDEES'  					=> $this->fv->generate_error('raid_attendees'),
           'FV_EVENT_NAME' 					=> $this->fv->generate_error('raid_name'),
           'FV_VALUE'      					=> $this->fv->generate_error('raid_value'),

          // Javascript messages
           'MSG_ATTENDEES_EMPTY'		=> $user->lang['fv_required_attendees'],
           'MSG_NAME_EMPTY'					=> $user->lang['fv_required_event_name'],
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
			'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['rp_raidplaner'],
			'template_file' => 'admin/addraid.html',
			'template_path' => $pm->get_data('raidplan', 'template_path'),
			'extra_css'		=> $this->extra_css,
            'display'       => true)
        );
	}

	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------
	/**
	* Populate the old_raid array
	*/
	function get_old_data()
	{
		global $db, $eqdkp, $user, $tpl, $pm;
		global $SID;

		$sql = "SELECT raid_name, raid_value, raid_note, raid_date, raid_attendees
				FROM (" . RP_RAIDS_TABLE . ")
				WHERE raid_id=" . $this->url_id . "";
		$result = $db->query($sql);
		while ( $row = $db->fetch_record($result) )
		{
			$this->old_raid = array(
				'raid_name'				=> addslashes($row['raid_name']),
				'raid_value'			=> addslashes($row['raid_value']),
				'raid_note'				=> addslashes($row['raid_note']),
				'raid_date'				=> addslashes($row['raid_date']),
				'raid_attendees'	=> addslashes($row['raid_attendees'])
			);
		}
		$db->free_result($result);

		$sql = "SELECT t1.class_index, t2.class_name, t1.count
			FROM (" . RP_CLASSES_TABLE . " as t1, " . CLASS_TABLE . " as t2)
			WHERE t1.raid_id='" . $this->url_id . "'
			AND t1.class_index=t2.class_id";
		$result = $db->query($sql);
		while ($row = $db->fetch_record($result))
		{
			$this->old_classes[$row['class_index']]['id'] = $row['class_index'];
			$this->old_classes[$row['class_index']]['name'] = $row['class_name'];
			$this->old_classes[$row['class_index']]['count'] = $row['count'];
		}
	}

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
            $raid_value = $db->query_first('SELECT event_value FROM (' . EVENTS_TABLE . ") WHERE event_name='" . addslashes($raid_name) . "'");
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
}

function raid_count($start_date, $end_date, $member_name)
{
	global $db;
	$raid_count = $db->query_first('SELECT count(*) FROM (' . RAIDS_TABLE . ') WHERE (raid_date BETWEEN ' . $start_date . ' AND ' . $end_date . ')');

	$sql = 'SELECT count(*)
		FROM (' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra)
		WHERE (ra.raid_id = r.raid_id)
		AND (ra.member_name='" . $member_name . "')
		AND (r.raid_date BETWEEN " . $start_date . ' AND ' . $end_date . ')';
	$individual_raid_count = $db->query_first($sql);

	$percent_of_raids = ( $raid_count > 0 ) ? round(($individual_raid_count / $raid_count) * 100) : 0;
	
	$raid_count_stats = array(
		'percent'     => $percent_of_raids,
		'total_count' => $raid_count,
		'indiv_count' => $individual_raid_count);
	return $raid_count_stats['percent']; // Only thing needed ATM
}

function get_raid_counts($member_name, $event_name)
{
	global $db;
	
	$raid_counts = array();
	$event_ids = array();
	
	// Find the count for each for this member
	$sql = 'SELECT e.event_id, r.raid_name, count(ra.raid_id) AS raid_count
		FROM (' . EVENTS_TABLE . ' e, ' . RAID_ATTENDEES_TABLE . ' ra, ' . RAIDS_TABLE . " r)
		WHERE (e.event_name = r.raid_name)
		AND (r.raid_id = ra.raid_id)
		AND (ra.member_name = '" . $member_name . "')
		GROUP BY ra.member_name, r.raid_name";
	$result = $db->query($sql);
	while ($row = $db->fetch_record($result))
	{
		// The count now becomes the percent
		$raid_counts[$row['raid_name']] = $row['raid_count'];
		$event_ids[$row['raid_name']] = $row['event_id'];
	}
	$db->free_result($result);
	
	// Find the count for reach raid
	$sql = 'SELECT raid_name, count(raid_id) AS raid_count
		FROM (' . RAIDS_TABLE . ')
		GROUP BY raid_name';
	$result = $db->query($sql);
	while ($row = $db->fetch_record($result))
	{
		if ( isset($raid_counts[$row['raid_name']]) )
		{
			$raid_counts[$raidrow['raid_name']] = array('count' => $raid_counts[ $raidrow['raid_name'] ]);
		}
	}
	$db->free_result($result);
	
	$raid_count = 0;
	foreach ($raid_counts as $raid)
	{
		$raid_count += $raid['count'];
	}
	
	$result_count = array(
		'events'	=> (isset($raid_counts[$event_name])) ? $raid_counts[$event_name]['count'] : "0",
		'raids'		=> $raid_count);
	
	return $result_count;
}

$myRaid = new RPAddRaid;
$myRaid->process();
?>
