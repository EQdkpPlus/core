<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addraid.php
 * Began: Mon December 23 2002
 * 
 * $Id$
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_Raid extends EQdkp_Admin
{
    var $raid     = array();            // Holds raid data if URI_RAID is set               @var raid
    var $old_raid = array();            // Holds raid data from before POST                 @var old_raid
    
    function add_raid()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();
        
        $this->raid = array(
            'raid_date'      => ( !$this->url_id ) ? $this->time : '',
            'raid_attendees' => post_or_db('raid_attendees'),
            'raid_name'      => post_or_db('raid_name'),
            'raid_note'      => post_or_db('raid_note'),
            'raid_value'     => post_or_db('raid_value')
        );
        
        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_raid'],
            'uri_parameter' => URI_RAID)
        );
        
        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_raid_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_raid_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_raid_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_raid_'))
        );
        
        // Build the raid array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value
                    FROM ' . RAIDS_TABLE . "
                    WHERE raid_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_raid_provided']);
            }
            $db->free_result($result);
        
            $this->time = $row['raid_date'];
            $this->raid = array(
                'raid_name'  => post_or_db('raid_name', $row),
                'raid_note'  => post_or_db('raid_note', $row),
                'raid_value' => post_or_db('raid_value', $row)
            );
        
            $attendees = array();
            $sql = 'SELECT member_name
                    FROM ' . RAID_ATTENDEES_TABLE . "
                    WHERE raid_id='" . $this->url_id . "'
                    ORDER BY member_name";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $attendees[] = $row['member_name'];
            }
            $this->raid['raid_attendees'] = ( !empty($_POST['raid_attendees']) ) ? $_POST['raid_attendees'] : implode(',', $attendees);
            unset($attendees);
        }
    }
    
    function error_check()
    {
        global $user;
        
	setlocale(LC_ALL, 'de_DE');
        $this->fv->is_alpha('raid_attendees',  $user->lang['fv_alpha_attendees']);
        $this->fv->is_filled('raid_attendees', $user->lang['fv_required_attendees']);
    
        $this->fv->is_within_range('mo', 1, 12,      $user->lang['fv_range_month']);
        $this->fv->is_within_range('d',  1, 31,      $user->lang['fv_range_day']);
        $this->fv->is_within_range('y',  1998, 2010, $user->lang['fv_range_year']); // How ambitious
        $this->fv->is_within_range('h',  0, 23,      $user->lang['fv_range_hour']);
        $this->fv->is_within_range('mi', 0, 59,      $user->lang['fv_range_minute']);
        $this->fv->is_within_range('s',  0, 59,      $user->lang['fv_range_second']);
        
        if ( !empty($_POST['raid_value']) )
        {
            $this->fv->is_number('raid_value', $user->lang['fv_number_value']);
        }
    
        if ( (@empty($_POST['raid_name'])) || (@sizeof($_POST['raid_name']) == 0) )
        {
            $this->fv->errors['raid_name'] = $user->lang['fv_required_event_name'];
        }
    
        $this->time = mktime($_POST['h'], $_POST['mi'], $_POST['s'], $_POST['mo'], $_POST['d'], $_POST['y']);
        
        return $this->fv->is_error();
    }
    
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $success_message = '';
        
        //
        // Raid loop for multiple events
        //
        foreach ( $_POST['raid_name'] as $raid_name )
        {
            //
            // Get the raid value
            //
            $raid_value = $this->get_raid_value($raid_name);
            
            //
            // Insert the raid
            //
            $query = $db->build_query('INSERT', array(
                'raid_name'     => stripslashes($raid_name),
                'raid_date'     => $this->time,
                'raid_note'     => stripslashes($_POST['raid_note']),
                'raid_value'    => $raid_value,
                'raid_added_by' => $this->admin_user)
            );
            $db->query('INSERT INTO ' . RAIDS_TABLE . $query);
            $this_raid_id = $db->insert_id();
            
            //
            // Attendee handling
            //
            // Make sure that each member's name is properly capitalized
            $raid_attendees = strtolower(preg_replace('/[[:space:]]/i', ' ', $_POST['raid_attendees']));
            $raid_attendees = ucwords($raid_attendees);
            
            // Make the array unique and sort it by name
            $members_array = explode(' ', $raid_attendees);
            $members_array = array_unique($members_array);
            sort($members_array);
            reset($members_array);
            
            //
            // Handle members
            //
            $this->handle_members($members_array, $raid_value, 'process_add');
           
            //
            // Insert the attendees
            //
            // Get rid of the 'blank' member bug
            $raid_attendees = implode(',', $members_array);
            $raid_attendees = preg_replace('/^\,(.+)/', '\1', $raid_attendees);
            $members_array  = explode(',', $raid_attendees);
            $this->add_attendees($members_array, $this_raid_id);
            
            //
            // Call plugin add hooks
            //
            $pm->do_hooks('/admin/addraid.php?action=add');
            
            //
            // Logging
            //
            $log_action = array(
                'header'        => '{L_ACTION_RAID_ADDED}',
                'id'            => $this_raid_id,
                '{L_EVENT}'     => $raid_name,
                '{L_ATTENDEES}' => implode(', ', $members_array),
                '{L_NOTE}'      => $_POST['raid_note'],
                '{L_VALUE}'     => $raid_value,
                '{L_ADDED_BY}'  => $this->admin_user);
            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );
            
            //
            // Append success message
            //
            $success_message .= sprintf($user->lang['admin_add_raid_success'], $_POST['mo'], $_POST['d'], $_POST['y'], $raid_name) . '<br />';
            
            unset($raid_value, $raid_name);
        } // Raid loop
        
        //
        // Update player status if needed
        //
        if ( $eqdkp->config['hide_inactive'] == 1 )
        {
            $success_message .= '<br /><br />' . $user->lang['admin_raid_success_hideinactive'];
            $success_message .= ' ' . (( $this->update_player_status() ) ? strtolower($user->lang['done']) : strtolower($user->lang['error']));
        }
        
        //
        // Success message
        //
        $link_list = array(
            $user->lang['add_items_from_raid'] => 'additem.php' . $SID . '&amp;raid_id=' . $this_raid_id,
            $user->lang['add_raid']            => 'addraid.php' . $SID,
            $user->lang['list_raids']          => 'listraids.php' . $SID);
        $this->admin_die($success_message, $link_list);

    }
    
    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        //
        // Get the old data
        //
        $this->get_old_data();
        
        //
        // Remove the attendees from the old raid
        //
        $db->query('DELETE FROM ' . RAID_ATTENDEES_TABLE . " WHERE raid_id='" . $this->url_id . "'");
        
        //
        // Get the raid value
        //
        $raid_value = $this->get_raid_value($_POST['raid_name']);
        
        //
        // Remove the value of the old raid from the attendees' earned
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . "
                SET member_earned = member_earned - " . $this->old_raid['raid_value'] . ",
                    member_raidcount = member_raidcount - 1
                WHERE member_name IN ('" . str_replace(',', "', '", $this->old_raid['raid_attendees']) . '\')';
        $db->query($sql);
        
        //
        // Update the raid
        //
        $query = $db->build_query('UPDATE', array(
            'raid_date'       => $this->time,
            'raid_note'       => stripslashes($_POST['raid_note']),
            'raid_value'      => $raid_value,
            'raid_name'       => stripslashes($_POST['raid_name']),
            'raid_updated_by' => $this->admin_user)
        );
        $db->query('UPDATE ' . RAIDS_TABLE . ' SET ' . $query . " WHERE raid_id='" . $this->url_id . "'");
        
        //
        // Add the new, updated raid to attendees' earned
        //
        $raid_attendees = strtolower(preg_replace('/[[:space:]]/i', ' ', $_POST['raid_attendees']));
        $raid_attendees = ucwords($raid_attendees);

        $n_members_array = explode(' ', $raid_attendees);
        $n_members_array = array_unique($n_members_array);
        sort($n_members_array);
        reset($n_members_array);
        
        //
        // Handle members
        //
        $this->handle_members($n_members_array, $raid_value, 'process_update');
        
        //
        // Insert the attendees
        //
        // Get rid of the 'blank' member bug
        $raid_attendees   = implode(',', $n_members_array);
        $raid_attendees   = preg_replace('/^\,(.+)/', '\1', $raid_attendees);
        $n_members_array  = explode(',', $raid_attendees);
        $this->add_attendees($n_members_array, $this->url_id);
        
        //
        // Update firstraid / lastraid [ #749201 ]
        //
        $update_firstraid = array(); // Members who need their firstraid updated
        $update_lastraid  = array(); // Members who need their lastraid updated
        
        $sql = 'SELECT member_name, member_firstraid, member_lastraid, member_raidcount
                FROM ' . MEMBERS_TABLE . "
                WHERE member_name IN ('" . str_replace(',', "', '", $this->old_raid['raid_attendees']) . '\')';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            // If the raid's date changed...
            if ( $this->time != $this->old_raid['raid_date'] )
            {
                // If the raid's old date is their firstraid, update their firstraid
                if ( $row['member_firstraid'] == $this->old_raid['raid_date'] )
                {
                    $update_firstraid[] = $row['member_name'];
                }
                
                // If the raid's old date is their lastraid, update their lastraid
                if ( $row['member_lastraid'] == $this->old_raid['raid_date'] )
                {
                    $update_lastraid[] = $row['member_name'];
                }
            }
        }
        $db->free_result($result);
        
        // Find members who were deleted from this raid and revert their first/last
        $old_attendees = explode(',', $this->old_raid['raid_attendees']);
        foreach ( $old_attendees as $member_name )
        {
            if ( !in_array($member_name, $n_members_array) )
            {
                $update_firstraid[] = $member_name;
                $update_lastraid[]  = $member_name;
            }
        }
        $update_firstraid = array_unique($update_firstraid);
        $update_lastraid  = array_unique($update_lastraid);
        
        sort($update_firstraid);
        sort($update_lastraid);
        
        reset($update_firstraid);
        reset($update_lastraid);
        
        $queries = array();
        // Update selected firstraids if needed
        if ( sizeof($update_firstraid) > 0 )
        {
            $sql = 'SELECT MIN(r.raid_date) AS member_firstraid, ra.member_name
                    FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                    WHERE ra.raid_id = r.raid_id
                    AND ra.member_name IN ('" . implode("', '", $update_firstraid) . '\')
                    AND r.raid_date > 0
                    GROUP BY ra.member_name';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $queries[] = 'UPDATE ' . MEMBERS_TABLE . "
                              SET member_firstraid = '" . $row['member_firstraid'] . "'
                              WHERE member_name = '" . $row['member_name'] . "'";
            }
            $db->free_result($result);
        }
        // Updated selected lastraids if needed
        if ( sizeof($update_lastraid) > 0 )
        {
            $sql = 'SELECT MAX(r.raid_date) AS member_lastraid, ra.member_name
                    FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                    WHERE ra.raid_id = r.raid_id
                    AND ra.member_name IN ('" . implode("', '", $update_lastraid) . '\')
                    AND r.raid_date > 0
                    GROUP BY ra.member_name';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $queries[] = 'UPDATE ' . MEMBERS_TABLE . "
                              SET member_lastraid = '" . $row['member_lastraid'] . "'
                              WHERE member_name = '" . $row['member_name'] . "'";
            }
            $db->free_result($result);
        }
        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }
        unset($queries, $sql);
        
        //
        // Call plugin update hooks
        //
        $pm->do_hooks('/admin/addraid.php?action=update');
        
        //
        // Logging
        //
        $old_attendees_array = explode(',', $this->old_raid['raid_attendees']);
        $new_attendees_array = $n_members_array;
        
        $log_action = array(
            'header'               => '{L_ACTION_RAID_UPDATED}',
            'id'                   => $this->url_id,
            '{L_EVENT_BEFORE}'     => $this->old_raid['raid_name'],
            '{L_ATTENDEES_BEFORE}' => implode(', ', $this->find_difference($new_attendees_array, $old_attendees_array)),
            '{L_NOTE_BEFORE}'      => $this->old_raid['raid_note'],
            '{L_VALUE_BEFORE}'     => $this->old_raid['raid_value'],
            '{L_EVENT_AFTER}'      => $this->find_difference($this->old_raid['raid_name'], $_POST['raid_name']),
            '{L_ATTENDEES_AFTER}'  => implode(', ', $this->find_difference($old_attendees_array, $new_attendees_array)),
            '{L_NOTE_AFTER}'       => $this->find_difference($this->old_raid['raid_note'], $_POST['raid_note']),
            '{L_VALUE_AFTER}'      => $this->find_difference($this->old_raid['raid_value'], $raid_value),
            '{L_UPDATED_BY}'       => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_raid_success'], $_POST['mo'], $_POST['d'], $_POST['y'], $_POST['raid_name']);
        
        // Update player status if needed
        if ( $eqdkp->config['hide_inactive'] == 1 )
        {
            $success_message .= '<br /><br />' . $user->lang['admin_raid_success_hideinactive'];
            $success_message .= ' ' . (( $this->update_player_status() ) ? strtolower($user->lang['done']) : strtolower($user->lang['error']));
        }
        
        $link_list = array(
            $user->lang['add_items_from_raid'] => 'additem.php' . $SID . '&amp;raid_id=' . $this->url_id,
            $user->lang['add_raid']            => 'addraid.php' . $SID,
            $user->lang['list_raids']          => 'listraids.php' . $SID);
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
        // Take the value away from the attendees
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . "
                SET member_earned = member_earned - " . $this->old_raid['raid_value'] . ",
                    member_raidcount = member_raidcount - 1
                WHERE member_name IN ('" . str_replace(',', "', '", $this->old_raid['raid_attendees']) . '\')';
        $db->query($sql);
        
        //
        // Remove cost of items from this raid from buyers
        //
        $sql = 'SELECT item_id, item_buyer, item_value
                FROM ' . ITEMS_TABLE . "
                WHERE raid_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $item_value = ( !empty($row['item_value']) ) ? $row['item_value'] : '0.00';
            $sql = 'UPDATE ' . MEMBERS_TABLE . "
                    SET member_spent = member_spent - " . $item_value . " 
                    WHERE member_name='" . $row['item_buyer'] . "'";
            $db->query($sql);
        }
        $db->free_result($result);
        
        //
        // Delete associated items
        //
        $db->query('DELETE FROM ' . ITEMS_TABLE . " WHERE raid_id='" . $this->url_id . "'");
        
        //
        // Delete attendees
        //
        $db->query('DELETE FROM ' . RAID_ATTENDEES_TABLE . " WHERE raid_id='" . $this->url_id . "'");
        
        //
        // Remove the raid itself
        //
        $db->query('DELETE FROM ' . RAIDS_TABLE . " WHERE raid_id='" . $this->url_id . "'");
        
        //
        // Update firstraid / lastraid [ #749201 ]
        //
        $update_firstraid = array(); // Members who need their firstraid updated
        $update_lastraid  = array(); // Members who need their lastraid updated
        $zero_firstlast   = array(); // Members who only attended one raid: this one - reset their first/last raid
        
        $sql = 'SELECT member_name, member_firstraid, member_lastraid, member_raidcount
                FROM ' . MEMBERS_TABLE . "
                WHERE member_name IN ('" . str_replace(',', "', '", $this->old_raid['raid_attendees']) . '\')';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            // We already updated their raidcount to reflect this deleted raid; so if it's 0, this was their only raid
            if ( $row['member_raidcount'] == '0' )
            {
                $zero_firstlast[] = $row['member_name'];
            }
            else
            {
                // If the raid's old date is their firstraid, update their firstraid
                if ( $row['member_firstraid'] == $this->old_raid['raid_date'] )
                {
                    $update_firstraid[] = $row['member_name'];
                }
                
                // If the raid's old date is their lastraid, update their lastraid
                if ( $row['member_lastraid'] == $this->old_raid['raid_date'] )
                {
                    $update_lastraid[] = $row['member_name'];
                }
            }
        }
        $db->free_result($result);
        
        // Zero the first/last raids if this was their only raid
        if ( sizeof($zero_firstlast) > 0 )
        {
            $sql = 'UPDATE ' . MEMBERS_TABLE . "
                    SET member_firstraid = 0,
                      member_lastraid = 0
                    WHERE member_name IN ('" . implode("', '", $zero_firstlast) . '\')';
            $db->query($sql);
        }
        
        $queries = array();
        // Update selected firstraids if needed
        if ( sizeof($update_firstraid) > 0 )
        {
            $sql = 'SELECT MIN(r.raid_date) AS member_firstraid, ra.member_name
                    FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                    WHERE ra.raid_id = r.raid_id
                    AND ra.member_name IN ('" . implode("', '", $update_firstraid) . '\')
                    AND r.raid_date > 0
                    GROUP BY ra.member_name';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $queries[] = 'UPDATE ' . MEMBERS_TABLE . "
                              SET member_firstraid = '" . $row['member_firstraid'] . "'
                              WHERE member_name = '" . $row['member_name'] . "'";
            }
            $db->free_result($result);
        }
        // Updated selected lastraids if needed
        if ( sizeof($update_lastraid) > 0 )
        {
            $sql = 'SELECT MAX(r.raid_date) AS member_lastraid, ra.member_name
                    FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                    WHERE ra.raid_id = r.raid_id
                    AND ra.member_name IN ('" . implode("', '", $update_lastraid) . '\')
                    AND r.raid_date > 0
                    GROUP BY ra.member_name';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $queries[] = 'UPDATE ' . MEMBERS_TABLE . "
                              SET member_lastraid = '" . $row['member_lastraid'] . "'
                              WHERE member_name = '" . $row['member_name'] . "'";
            }
            $db->free_result($result);
        }
        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }
        unset($queries, $sql);
        
        //
        // Call plugin delete hooks
        //
        $pm->do_hooks('/admin/addraid.php?action=delete');
        
        //
        // Logging
        //
        $log_action = array(
            'header'        => '{L_ACTION_RAID_DELETED}',
            'id'            => $this->url_id,
            '{L_EVENT}'     => $this->old_raid['raid_name'],
            '{L_ATTENDEES}' => str_replace(',', ', ', $this->old_raid['raid_attendees']),
            '{L_NOTE}'      => $this->old_raid['raid_note'],
            '{L_VALUE}'     => $this->old_raid['raid_value']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //
        $success_message = $user->lang['admin_delete_raid_success'];
        
        // Update player status if needed
        if ( $eqdkp->config['hide_inactive'] == 1 )
        {
            $success_message .= '<br /><br />' . $user->lang['admin_raid_success_hideinactive'];
            $success_message .= ' ' . (( $this->update_player_status() ) ? strtolower($user->lang['done']) : strtolower($user->lang['error']));
        }
        
        $link_list = array(
            $user->lang['add_raid']   => 'addraid.php' . $SID,
            $user->lang['list_raids'] => 'listraids.php' . $SID);
        $this->admin_die($success_message, $link_list);
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
        
        $sql = 'SELECT raid_name, raid_value, raid_note, raid_date
                FROM ' . RAIDS_TABLE . "
                WHERE raid_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_raid = array(
                'raid_name'  => addslashes($row['raid_name']),
                'raid_value' => addslashes($row['raid_value']),
                'raid_note'  => addslashes($row['raid_note']),
                'raid_date'  => addslashes($row['raid_date'])
            );
        }
        $db->free_result($result);
        
        $sql = 'SELECT r.member_name
                FROM ' . RAID_ATTENDEES_TABLE . " r, " . MEMBERS_TABLE . " m
                WHERE m.member_name = r.member_name AND raid_id='" . $this->url_id . "'
                ORDER BY member_name";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $attendees[] = $row['member_name'];
        }
        $this->old_raid['raid_attendees'] = @implode(',', $attendees);
        unset($attendees);
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
            $raid_value = $db->query_first('SELECT event_value FROM ' . EVENTS_TABLE . " WHERE event_name='" . addslashes($raid_name) . "'");

	    if (!isset($raid_value)) {
		$raid_value = 0;
	    }

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
    
    /**
    * Get member level/race/class from database or SESSION data
    * 
    * @param $member_name
    * @return Array
    */
    function get_member_info($member_name)
    {
	global $db, $eqdkp;

	if ($_SESSION[$member_name]['class'] == "") {
		unset($_SESSION[$member_name]['class']);
	}

	if ($_SESSION[$member_name]['level'] == "") {
		unset($_SESSION[$member_name]['level']);
	}

	if ($_SESSION[$member_name]['race'] == "") {
		unset($_SESSION[$member_name]['race']);
	}

	if ( isset($_SESSION[$member_name]['level']) && isset($_SESSION[$member_name]['class']) ) {

		$sql = "SELECT race_name FROM " . RACE_TABLE . " WHERE race_name = '" . $_SESSION[$member_name]['race'] . "'";
		$race_name = $db->query_first($sql);

		if (!isset($race_name)) {
			$race_name = "Unknown";
		}

        	$retval = array(
			'name'  => $_SESSION[$member_name],
	            	'level' => ( isset($_SESSION[$member_name]['level']) ) ? $_SESSION[$member_name]['level'] : false,
    	        	'race'  => $race_name,
  	          	'class' => ( isset($_SESSION[$member_name]['class']) ) ? $_SESSION[$member_name]['class'] : false);

      	  	unset($_SESSION[$member_name]);


	} else {


		$sql = "SELECT member_name, member_race_id, member_class_id, member_level FROM " . MEMBERS_TABLE . " 
			WHERE member_name = '" . $member_name . "'";
		$result = $db->query($sql);
		$info = $db->fetch_record($result);

		if (!isset($info['member_level'])) {
			$member_level = "1";
		}

		$sql = "SELECT race_name FROM " . RACE_TABLE . " WHERE race_id = '" . $info['member_race_id'] . "'";
		$race_name = $db->query_first($sql);

		if (!isset($race_name)) {
			$race_name = "Unknown";
		}
	
		$sql = "SELECT class_name FROM " . CLASS_TABLE . " WHERE class_id = '" . $info['member_class_id'] . "'";
		$class_name = $db->query_first($sql);

		if (!isset($class_name)) {
			$class_name = "Unknown";
		}

      	 	$retval = array(
			    'name'  => $member_name,
       			    'race'  => $race_name,
       			    'level' => $member_level,
       			    'class' => $class_name);
	}

        return $retval;
    }
    
    /**
    * Insert members into raid attendees table
    * 
    * @param $members_array Array of members
    * @param $raid_id
    */
    function add_attendees(&$members_array, $raid_id)
    {
        global $db;
        
        $query = array();
        foreach ( $members_array as $member_name )
        {
            $query[] = "($raid_id, '" . $member_name . "')";
        }
        
        $sql = 'INSERT INTO ' . RAID_ATTENDEES_TABLE . ' (raid_id, member_name) 
                VALUES ' . implode(', ', $query);
        $db->query($sql);
    }
    
    /**
    * Update existing members / add new members
    * 
    * @param $members_array
    * @param $raid_value
    * @param $time_check
    */
    function handle_members(&$members_array, $raid_value, $process)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        // Grab our array of name => class/level/race
        session_start();
        
        //
        // Handle existing members
        //
        $update_sql_members = array();
        $updated_members    = array();
        $raid_attendees     = array();
	
        $sql = 'SELECT m.member_name, m.member_firstraid, m.member_lastraid, m.member_level, r.race_name AS member_race, 
		c.class_name AS member_class, m.member_raidcount 
                FROM ' . MEMBERS_TABLE .' m, '. CLASS_TABLE .' c, '.RACE_TABLE.' r
		WHERE r.race_id = m.member_race_id 
		AND c.class_id = m.member_class_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $member_name = trim(str_replace(' ', '', $row['member_name']));
            
            // Make sure the member is in the attendees list before proceeding
            if ( (!in_array($member_name, $members_array)) || (empty($member_name)) )
            {
                continue;
            }
            
            $raid_attendees[] = $member_name;
            
            // raidcount and/or firstraid is 0 - they exist but we need to set their firstraid to this date [ #705206 ]
            if ( ($row['member_raidcount'] == '0') || ($row['member_firstraid'] == '0') )
            {
                $sql = 'UPDATE ' . MEMBERS_TABLE . '
                        SET member_earned = member_earned + ' . $raid_value . ",
                            member_firstraid = '" . $this->time . "',
                            member_lastraid = '" . $this->time . "',
                            member_raidcount = member_raidcount + 1
                        WHERE member_name = '" . $member_name . "'";
                $db->query($sql);

                $updated_members[] = $member_name;
                continue;
            }
            else
            {
                $updated_members[] = $member_name;
            }
            
            // Check for race/class/level data for this member
            $member_data = $this->get_member_info($member_name);

            if ( (!(isset($member_data['race']) )) ||  $member_data['race'] == 'Unknown'  )
            {
                $member_data['race'] = $row['member_race'];
            }

            if ( (!(isset($member_data['class']) )) ||  $member_data['class'] == 'Unknown' )
            {
                $member_data['class'] = $row['member_class'];
            }

            $member_level = ( is_numeric($member_data['level']) ) ? trim($member_data['level']) : 'member_level';
            $member_race  = ( is_string($member_data['race']) )   ? trim($member_data['race'])  : 'member_race';
            $member_class = ( is_string($member_data['class']) )  ? trim($member_data['class']) : 'member_class';
            unset($member_data);
            
            // Update this member's race/class/level if they changed
            $time_check  = ( $process == 'process_add' ) ? ($this->time > $row['member_lastraid']) : ($this->time <= $row['member_lastraid']);
            $level_check = ( ($member_level != $row['member_level']) && ($member_level != 'member_level') ) ? true : false;
            $race_check  = ( ($member_race  != $row['member_race'])  && ($member_race  != 'member_race') )  ? true : false;
            $class_check = ( ($member_class != $row['member_class']) && ($member_class != 'member_class') ) ? true : false;
            
            if ( ($time_check) && ($level_check || $race_check || $class_check) )
            {
                // For comparison, quotes need to be added after the if statement above
                $member_level = ( $member_level != 'member_level' ) ? '\'' . $member_level . '\'' : $member_level;
                $member_race  = ( $member_race  != 'member_race'  ) ? '\'' . $member_race  . '\'' : $member_race;
                $member_class = ( $member_class != 'member_class' ) ? '\'' . $member_class . '\'' : $member_class;

                
                // Process the update
                $sql  = 'UPDATE ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE . ' c, ' . RACE_TABLE . ' r
                         SET m.member_earned = m.member_earned + ' . $raid_value . ',';
                         
                // Do not update their lastraid if it's greater than this raid's date [ #749201 ]
                if ( $row['member_lastraid'] < $this->time )
                {
                    $sql .= "m.member_lastraid = '" . $this->time . "',";
                }
                
                $sql .= '  m.member_raidcount = m.member_raidcount + 1,
                           m.member_level = ' . $member_level . ',
                           m.member_race_id = r.race_id, 
                           m.member_class_id = c.class_id
			 WHERE r.race_name = '.$member_race.'
			 AND c.class_name = '.$member_class.'
                         AND m.member_name = "' . $member_name . '"';
                $db->query($sql);
            }
            // If they didn't, their update is lumped into $update_sql (below)
            else
            {
                $update_sql_members[] = $member_name;
            }
        }
        $db->free_result($result);
        session_destroy();
        
        // Run the lump update if we need to
        if ( sizeof($update_sql_members) > 0 )
        {
            $sql = 'UPDATE ' . MEMBERS_TABLE . '
                    SET member_raidcount = member_raidcount + 1,
                        member_earned = member_earned + ' . $raid_value . "
                    WHERE member_name IN ('" . implode("', '", $update_sql_members) . '\')';
            $db->query($sql);
        }
        
        //
        // Update firstraid / lastraid [ #749201 ]
        //
        $this->update_member_firstraid($raid_attendees, $this->time);
        $this->update_member_lastraid($raid_attendees,  $this->time);
               
        //
        // Handle new members
        //
        $new_members = array_diff($members_array, $updated_members);
        foreach ( $new_members as $member_name )
        {
            $member_name = trim($member_name);
            if ( $member_name != '' )
            {
                $member_data2 = $this->get_member_info($member_name);

		$class = $member_data2['class'];
		$race = $member_data2['race'];
	
		if ( ! ( isset($class) ) || ($class == "") ) {
			$class = "Unknown";
		}
		

                $class_id_number = $db->query_first('SELECT class_id FROM ' . CLASS_TABLE . ' WHERE class_name  = "'.$class.'"');
                $race_id_number = $db->query_first('SELECT race_id FROM ' . RACE_TABLE . ' WHERE race_name  = "'.$race.'"');
		
		if (!isset($race_id_number)) {
			$race_id_number = 0;
		}

		if (!isset($class_id_number)) {
			$class_id_number = 0;
		}
	
                $query = $db->build_query('INSERT', array(
                    'member_name'      => $member_name,
                    'member_earned'    => $raid_value,
                    'member_status'    => '1',
                    'member_firstraid' => $this->time,
                    'member_lastraid'  => $this->time,
                    'member_raidcount' => '1',
                    'member_level'     => $member_data2['level'],
                    'member_race_id'   => $race_id_number,
                    'member_class_id'  => $class_id_number,
                    'member_rank_id'   => '0')
                );
                $db->query('INSERT INTO ' . MEMBERS_TABLE . $query);
            }
        }
        
        // For any member who has a 0 raidcount, reset their first/last raid to 0
        $sql = 'UPDATE ' . MEMBERS_TABLE . "
                SET member_firstraid = '0', member_lastraid='0'
                WHERE member_raidcount='0'";
        $db->query($sql);
    }
    
    /**
    * Update members' firstraid
    * 
    * @param    array   $members_array
    * @param    string  $time_check     Time to check
    */
    function update_member_firstraid(&$members_array, $time_check)
    {
        global $db;
        
        if ( sizeof($members_array) > 0 )
        {
            $sql = 'UPDATE ' . MEMBERS_TABLE . "
                    SET member_firstraid = '" . $time_check . "'
                    WHERE member_name IN ('" . implode("', '", $members_array) . '\')
                    AND member_firstraid > ' . $time_check;
            $db->query($sql);
        }
    }

    /**
    * Update members' lastraid
    * 
    * @param    array   $members_array
    * @param    string  $time_check     Time to check
    */    
    function update_member_lastraid(&$members_array, $time_check)
    {
        global $db;
        
        if ( sizeof($members_array) > 0 )
        {
            $sql = 'UPDATE ' . MEMBERS_TABLE . "
                    SET member_lastraid = '" . $time_check . "'
                    WHERE member_name IN ('" . implode("', '", $members_array) . '\')
                    AND member_lastraid < ' . $time_check;
            $db->query($sql);
        }
    }


	
    
    /**
    * Update active/inactive player status
    * 
    * @return bool
    */
    function update_player_status()
    {
        global $db, $eqdkp, $user;
        
        $inactive_time = mktime(0, 0, 0, date('m'), date('d')-$eqdkp->config['inactive_period'], date('Y'));

        $active_members   = array();
        $inactive_members = array();
        
        // Don't go through this whole thing of active/inactive adjustments if we don't need to.
        if ( ($eqdkp->config['active_point_adj'] != '0.00') || ($eqdkp->config['inactive_point_adj'] != '0.00') )
        {
            $time = time();
            $sql = 'SELECT member_name, member_status, member_lastraid
                    FROM ' . MEMBERS_TABLE;
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                unset($adj_value);
                unset($adj_reason);
                
                // Active -> Inactive
                if ( ($eqdkp->config['inactive_point_adj'] != '0.00') && ($row['member_status'] == '1') && ($row['member_lastraid'] < $inactive_time) )
                {
                    $adj_value  = $eqdkp->config['inactive_point_adj'];
                    $adj_reason = 'Inactive adjustment';
                    
                    $inactive_members[] = $row['member_name'];
                }
                // Inactive -> Active
                elseif ( ($eqdkp->config['active_point_adj'] != '0.00') && ($row['member_status'] == '0') && ($row['member_lastraid'] >= $inactive_time) )
                {
                    $adj_value  = $eqdkp->config['active_point_adj'];
                    $adj_reason = 'Active adjustment';
                    
                    $active_members[] = $row['member_name'];
                }
                
                //
                // Insert individual adjustment
                //
                if ( (isset($adj_value)) && (isset($adj_reason)) )
                {
                    $group_key = $this->gen_group_key($time, $adj_reason, $adj_value);

                    $query = $db->build_query('INSERT', array(
                        'adjustment_value'     => $adj_value,
                        'adjustment_date'      => $time,
                        'member_name'          => $row['member_name'],
                        'adjustment_reason'    => $adj_reason,
                        'adjustment_group_key' => $group_key,
                        'adjustment_added_by'  => $user->data['username'])
                    );
                    $db->query('INSERT INTO ' . ADJUSTMENTS_TABLE . $query);
                }
            }
            
            // Update inactive members' adjustment
            if ( sizeof($inactive_members) > 0 )
            {
                $adj_value  = $eqdkp->config['inactive_point_adj'];
                $adj_reason = 'Inactive adjustment';
                
                $sql = 'UPDATE ' . MEMBERS_TABLE . "
                        SET member_status='0', member_adjustment = member_adjustment + " . $eqdkp->config['inactive_point_adj'] . "
                        WHERE member_name IN ('" . implode("', '", $inactive_members) . '\')';
                        
                $log_action = array(
                    'header'         => '{L_ACTION_INDIVADJ_ADDED}',
                    '{L_ADJUSTMENT}' => $eqdkp->config['inactive_point_adj'],
                    '{L_MEMBERS}'    => implode(', ', $inactive_members),
                    '{L_REASON}'     => 'Inactive adjustment',
                    '{L_ADDED_BY}'   => $user->data['username']);
                $this->log_insert(array(
                    'log_type'   => $log_action['header'],
                    'log_action' => $log_action)
                );
            }
            
            // Update active members' adjustment
            if ( sizeof($active_members) > 0 )
            {
                $sql = 'UPDATE ' . MEMBERS_TABLE . "
                        SET member_status='1', member_adjustment = member_adjustment + " . $eqdkp->config['active_point_adj'] . "
                        WHERE member_name IN ('" . implode("', '", $active_members) . '\')';
                $db->query($sql);
                
                $log_action = array(
                    'header'         => '{L_ACTION_INDIVADJ_ADDED}',
                    '{L_ADJUSTMENT}' => $eqdkp->config['active_point_adj'],
                    '{L_MEMBERS}'    => implode(', ', $active_members),
                    '{L_REASON}'     => 'Active adjustment',
                    '{L_ADDED_BY}'   => $user->data['username']);
                $this->log_insert(array(
                    'log_type'   => $log_action['header'],
                    'log_action' => $log_action)
                );
            }
        }
        else
        {
            // Active -> Inactive
            $db->query('UPDATE ' . MEMBERS_TABLE . " SET member_status='0' WHERE (member_lastraid < " .  $inactive_time . ") AND (member_status='1')");
        
            // Inactive -> Active
            $db->query('UPDATE ' . MEMBERS_TABLE . " SET member_status='1' WHERE (member_lastraid >= " . $inactive_time . ") AND (member_status='0')");
        }


        // If your class_id doesn't match your level, update your class ID to the one that has
        // the same class_name, but the correct min and max level.
        $sql = "SELECT m.member_name, m.member_level, c.class_name, c.class_id, c.class_min_level, c.class_max_level
                FROM " . MEMBERS_TABLE ." m, " . CLASS_TABLE . " c
                WHERE m.member_class_id = c.class_id";
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) ) {

                if ( isset($row['member_level']) && ($row['member_level'] > $row['class_max_level'] || $row['member_level'] < $row['class_min_level'])) {

                  $sql = "SELECT class_id
                          FROM " . CLASS_TABLE . "
                          WHERE class_name = '" . $row['class_name'] ."'
                          AND class_min_level < '" . $row['member_level'] ."'
                          AND class_max_level >= '" . $row['member_level'] ."'";
                  $new_class_id = $db->query_first($sql);

		  if (!isset($new_class_id)) {
			$new_class_id = 0;
		  }

                  $sql = "UPDATE " . MEMBERS_TABLE . "
                          SET member_class_id = '" . $new_class_id . "'
                          WHERE member_name = '" . $row['member_name'] . "'";
                  $db->query($sql);

                }
        }

        
        return true;
    }
    
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        //
        // Find the value of the event, or use the one-time value from the form
        //
        $raid_name    = ( is_array($this->raid['raid_name']) ) ? (( isset($this->raid['raid_name'][0]) ) ? $this->raid['raid_name'][0] : '' ) : $this->raid['raid_name'];
        $preset_value = $db->query_first('SELECT event_value FROM ' . EVENTS_TABLE . " WHERE event_name='" . addslashes($raid_name) . "'");
        $raid_value = ( $this->raid['raid_value'] == 0 )             ? '' : $this->raid['raid_value'];
        $raid_value = ( $this->raid['raid_value'] == $preset_value ) ? '' : $this->raid['raid_value'];
        
        //
        // Build member drop-down
        //
        $sql = 'SELECT member_name
                FROM ' . MEMBERS_TABLE . '
                ORDER BY member_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('members_row', array(
                'VALUE'  => $row['member_name'],
                'OPTION' => $row['member_name'])
            );
        }
        $db->free_result($result);
        
        //
        // Build event drop-down
        //
        $max_value = $db->query_first('SELECT max(event_value) FROM ' . EVENTS_TABLE);

	if (!isset($max_value)) {
		$max_value = 0;
	}

        $float = @explode('.', $max_value);
        $format = '%0' . @strlen($float[0]) . '.2f';
        
        $sql = 'SELECT event_id, event_name, event_value
                FROM ' . EVENTS_TABLE . '
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
        
        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_RAID'       => 'addraid.php' . $SID,
            'RAID_ID'          => $this->url_id,
            'U_ADD_EVENT'      => 'addevent.php'.$SID,
            'S_EVENT_MULTIPLE' => ( !$this->url_id ) ? true : false,
            
            // Form values
            'RAID_ATTENDEES' => str_replace(',', "\n", $this->raid['raid_attendees']),
            'RAID_VALUE'     => stripslashes($raid_value),
            'RAID_NOTE'      => stripslashes(htmlspecialchars($this->raid['raid_note'])),
            'MO'             => date('m', $this->time),
            'D'              => date('d', $this->time),
            'Y'              => date('Y', $this->time),
            'H'              => date('H', $this->time),
            'MI'             => date('i', $this->time),
            'S'              => date('s', $this->time),
            
            // Language
            'L_ADD_RAID_TITLE'        => $user->lang['addraid_title'],
            'L_ATTENDEES'             => $user->lang['attendees'],
            'L_PARSE_LOG'             => $user->lang['parse_log'],
            'L_SEARCH_MEMBERS'        => $user->lang['search_members'],
            'L_EVENT'                 => $user->lang['event'],
            'L_ADD_EVENT'             => strtolower($user->lang['add_event']),
            'L_VALUE'                 => $user->lang['value'],
            'L_ADDRAID_VALUE_NOTE'    => $user->lang['addraid_value_note'],
            'L_DATE'                  => $user->lang['date'],
            'L_TIME'                  => $user->lang['time'],
            'L_ADDRAID_DATETIME_NOTE' => $user->lang['addraid_datetime_note'],
            'L_NOTE'                  => $user->lang['note'],
            'L_ADD_RAID'              => $user->lang['add_raid'],
            'L_RESET'                 => $user->lang['reset'],
            'L_UPDATE_RAID'           => $user->lang['update_raid'],
            'L_DELETE_RAID'           => $user->lang['delete_raid'],
            
            // Form validation
            'FV_ATTENDEES'  => $this->fv->generate_error('raid_attendees'),
            'FV_EVENT_NAME' => $this->fv->generate_error('raid_name'),
            'FV_VALUE'      => $this->fv->generate_error('raid_value'),
            'FV_MO'         => $this->fv->generate_error('mo'),
            'FV_D'          => $this->fv->generate_error('d'),
            'FV_Y'          => $this->fv->generate_error('y'),
            'FV_H'          => $this->fv->generate_error('h'),
            'FV_MI'         => $this->fv->generate_error('mi'),
            'FV_S'          => $this->fv->generate_error('s'),
            
            // Javascript messages
            'MSG_ATTENDEES_EMPTY' => $user->lang['fv_required_attendees'],
            'MSG_NAME_EMPTY'      => $user->lang['fv_required_event_name'],
	    'MSG_GAME_NAME'	  => $eqdkp->config['default_game'],
            
            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['addraid_title'],
            'template_file' => 'admin/addraid.html',
            'display'       => true)
        );
    }
}

$add_raid = new Add_Raid;
$add_raid->process();
?>
