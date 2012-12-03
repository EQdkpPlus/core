<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * mm_addmember.php
 * Began: Thu January 30 2003
 *
 * $Id$
 *
 ******************************/

// This script handles adding, updating or deleting a member.
// NOTE: This script will also process deleting multiple members through the
// mm_listmembers interface

if ( !defined('EQDKP_INC') )
{
    die('Hacking attempt');
}

class MM_Addmember extends EQdkp_Admin
{
    var $member     = array();          // Holds member data if URI_NAME is set             @var member
    var $old_member = array();          // Holds member data from before POST               @var old_member

    function mm_addmember()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $defaults = array(
            'member_earned'     => '0.00',
            'member_spent'      => '0.00',
            'member_adjustment' => '0.00');

        $this->member = array(
            'member_id'         => 0,
            'member_name'       => post_or_db('member_name'),
            'member_earned'     => post_or_db('member_earned', $defaults),
            'member_spent'      => post_or_db('member_spent', $defaults),
            'member_adjustment' => post_or_db('member_adjustment', $defaults),
            'member_current'    => '0.00',
            'member_race_id'    => post_or_db('member_race_id'),
            'member_class_id'   => post_or_db('member_class_id'),
            'member_level'      => post_or_db('member_level'),
            'member_rank_id'    => post_or_db('member_rank_id')
        );

        // Vars used to confirm deletion
        $confirm_text = $user->lang['confirm_delete_members'];
        $member_names = array();
        if ( isset($_POST['delete']) )
        {
            if ( isset($_POST['compare_ids']) )
            {
                foreach ( $_POST['compare_ids'] as $id )
                {
                    $member_name = $db->query_first('SELECT member_name FROM ' . MEMBERS_TABLE . " WHERE member_id='" . $id . "'");
                    $member_names[] = $member_name;
                }

                $names = implode(', ', $member_names);

                $confirm_text .= '<br /><br />' . $names;
            }
            else
            {
                message_die('No members were selected for deletion.');
            }
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
            'uri_parameter' => URI_NAME,
            'url_id'        => ( sizeof($member_names) > 0 ) ? $names : (( isset($_GET[URI_NAME]) ) ? $_GET[URI_NAME] : ''),
            'script_name'   => 'manage_members.php' . $SID . '&amp;mode=addmember')
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_members_man'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_members_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_members_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_members_man'))
        );

        // Build the member array
        // ---------------------------------------------------------
        if ( !empty($this->url_id) )
        {
            $sql = 'SELECT m.*, (m.member_earned - m.member_spent + m.member_adjustment) AS member_current,
			c.class_name AS member_class, r.race_name AS member_race
                    	FROM ' . MEMBERS_TABLE . ' m, ' . CLASS_TABLE . ' c, ' . RACE_TABLE . " r
			WHERE r.race_id = m.member_race_id
			AND c.class_id = m.member_class_id
			AND member_name='" . $this->url_id . "'";
            $result = $db->query($sql);
            $row = $db->fetch_record($result);
            $db->free_result($result);

            $this->member = array(
                'member_id'         => $row['member_id'],
                'member_name'       => post_or_db('member_name', $row),
                'member_earned'     => post_or_db('member_earned', $row),
                'member_spent'      => post_or_db('member_spent', $row),
                'member_adjustment' => post_or_db('member_adjustment', $row),
                'member_current'    => $row['member_current'],
                'member_race_id'    => post_or_db('member_race_id', $row),
                'member_race'       => $row['member_race'],
                'member_class_id'   => post_or_db('member_class_id', $row),
                'member_class'      => $row['member_class'],
                'member_level'      => post_or_db('member_level', $row),
                'member_rank_id'    => post_or_db('member_rank_id', $row)
            );
        }
    }

    function error_check()
    {
        global $user, $SID;

        if ( (isset($_POST['add'])) || (isset($_POST['update'])) )
        {
            $this->fv->is_filled('member_name', $user->lang['fv_required_name']);
            $this->fv->is_number(array(
                'member_earned'     => $user->lang['fv_number'],
                'member_spent'      => $user->lang['fv_number'],
                'member_adjustment' => $user->lang['fv_number'])
            );
        }

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        //
        // Insert the member
        //

        // Make sure that each member's name is properly capitalized
        $member_name = strtolower(preg_replace('/[[:space:]]/i', ' ', $_POST['member_name']));
        $member_name = ucwords($member_name);

	// Check for existing member name
	$sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
	$member_id = $db->query_first($sql);


	// Error out if member name exists
	if ( isset($member_id) && $member_id > 0 ) {

          $failure_message = "Failed to add $member_name; member exists as ID $member_id";
          $link_list = array(
              $user->lang['add_member']           => 'manage_members.php' . $SID . '&amp;mode=addmember',
              $user->lang['list_edit_del_member'] => 'manage_members.php' . $SID . '&amp;mode=list');

          message_die($failure_message, $link_list);

        }


        $query = $db->build_query('INSERT', array(
            'member_name'       => $member_name,
            'member_earned'     => $_POST['member_earned'],
            'member_spent'      => $_POST['member_spent'],
            'member_adjustment' => $_POST['member_adjustment'],
            'member_firstraid'  => 0,
            'member_lastraid'   => 0,
            'member_raidcount'  => 0,
            'member_level'      => $_POST['member_level'],
            'member_race_id'    => $_POST['member_race_id'],
            'member_class_id'   => $_POST['member_class_id'],
            'member_rank_id'    => $_POST['member_rank_id'])
        );
        $db->query('INSERT INTO ' . MEMBERS_TABLE . $query);

        //
        // Logging
        //
        $log_action = array(
            'header'         => '{L_ACTION_MEMBER_ADDED}',
            '{L_NAME}'       => $member_name,
            '{L_EARNED}'     => $_POST['member_earned'],
            '{L_SPENT}'      => $_POST['member_spent'],
            '{L_ADJUSTMENT}' => $_POST['member_adjustment'],
            '{L_LEVEL}'      => $_POST['member_level'],
            '{L_RACE}'       => $_POST['member_race_id'],
            '{L_CLASS}'      => $_POST['member_class_id'],
            '{L_ADDED_BY}'   => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_add_member_success'], $member_name);
        $link_list = array(
            $user->lang['add_member']           => 'manage_members.php' . $SID . '&amp;mode=addmember',
            $user->lang['list_edit_del_member'] => 'manage_members.php' . $SID . '&amp;mode=list');
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
        // Get old member data
        //
        $this->get_old_data($_POST[URI_NAME]);
        $member_id = $this->old_member['member_id'];
        $old_member_name = $this->old_member['member_name'];

        // Make sure that each member's name is properly capitalized
        $member_name = strtolower(preg_replace('/[[:space:]]/i', ' ', $_POST['member_name']));
        $member_name = ucwords($member_name);

        //
        // Update the member
        //
        $query = $db->build_query('UPDATE', array(
            'member_name'       => $member_name,
            'member_earned'     => $_POST['member_earned'],
            'member_spent'      => $_POST['member_spent'],
            'member_adjustment' => $_POST['member_adjustment'],
            'member_level'      => $_POST['member_level'],
            'member_race_id'    => $_POST['member_race_id'],
            'member_class_id'   => $_POST['member_class_id'],
            'member_rank_id'    => $_POST['member_rank_id'])
        );
        $db->query('UPDATE ' . MEMBERS_TABLE . ' SET ' . $query . " WHERE member_name='" . $old_member_name . "'");

	if ( !($member_name == $old_member_name) ) {

		$sql = "UPDATE " . RAID_ATTENDEES_TABLE . " SET member_name = '" . $member_name ."' WHERE member_name = '". $old_member_name . "'";
		$db->query_first($sql);

		$sql = "UPDATE " . ITEMS_TABLE . " SET item_buyer = '" . $member_name ."' WHERE item_buyer = '". $old_member_name . "'";
		$db->query_first($sql);

        $sql = "UPDATE " . ADJUSTMENT_TABLE . " SET member_name = '" . $member_name ."' WHERE member_name = '". $old_member_name . "'";
        $db->query_first($sql);

		$sql = "UPDATE " . ADJUSTMENTS_TABLE . " SET member_name = '" . $member_name ."' WHERE member_name = '". $old_member_name . "'";
        $db->query_first($sql);


	}



        //
        // Logging
        //
        $log_action = array(
            'header'                => '{L_ACTION_MEMBER_UPDATED}',
            '{L_NAME_BEFORE}'       => $this->old_member['member_name'],
            '{L_EARNED_BEFORE}'     => $this->old_member['member_earned'],
            '{L_SPENT_BEFORE}'      => $this->old_member['member_spent'],
            '{L_ADJUSTMENT_BEFORE}' => $this->old_member['member_adjustment'],
            '{L_LEVEL_BEFORE}'      => $this->old_member['member_level'],
            '{L_RACE_BEFORE}'       => $this->old_member['member_race_id'],
            '{L_CLASS_BEFORE}'      => $this->old_member['member_class_id'],
            '{L_NAME_AFTER}'        => $this->find_difference($this->old_member['member_name'],       $member_name),
            '{L_EARNED_AFTER}'      => $this->find_difference($this->old_member['member_earned'],     $_POST['member_earned']),
            '{L_SPENT_AFTER}'       => $this->find_difference($this->old_member['member_spent'],      $_POST['member_spent']),
            '{L_ADJUSTMENT_AFTER}'  => $this->find_difference($this->old_member['member_adjustment'], $_POST['member_adjustment']),
            '{L_LEVEL_AFTER}'       => $this->find_difference($this->old_member['member_level'],      $_POST['member_level']),
            '{L_RACE_AFTER}'        => $this->find_difference($this->old_member['member_race_id'],       $_POST['member_race_id']),
            '{L_CLASS_AFTER}'       => $this->find_difference($this->old_member['member_class_id'],   $_POST['member_class_id']),
            '{L_UPDATED_BY}'        => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_member_success'], $this->old_member['member_name']);
        $this->admin_die($success_message);
    }

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $success_message = '';
        $members = explode(', ', $_POST[URI_NAME]);
        foreach ( $members as $member_name )
        {
            if ( empty($member_name) )
            {
                continue;
            }

            //
            // Get old member data
            //
            $this->get_old_data($member_name);

            //
            // Delete attendance
            //
            $sql = 'DELETE FROM ' . RAID_ATTENDEES_TABLE . "
                    WHERE member_name='" . $member_name . "'";
            $db->query($sql);

            //
            // Delete items
            //
            $sql = 'DELETE FROM ' . ITEMS_TABLE . "
                    WHERE item_buyer='" . $member_name . "'";
            $db->query($sql);

            //
            // Delete adjustments
            //
            $sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . "
                    WHERE member_name='" . $member_name . "'";
            $db->query($sql);

            //
            // Delete member
            //
            $sql = 'DELETE FROM ' . MEMBERS_TABLE . "
                    WHERE member_name='" . $member_name . "'";
            $db->query($sql);

            //
            // Logging
            //
            $log_action = array(
                'header'         => '{L_ACTION_MEMBER_DELETED}',
                '{L_NAME}'       => $this->old_member['member_name'],
                '{L_EARNED}'     => $this->old_member['member_earned'],
                '{L_SPENT}'      => $this->old_member['member_spent'],
                '{L_ADJUSTMENT}' => $this->old_member['member_adjustment'],
                '{L_LEVEL}'      => $this->old_member['member_level'],
                '{L_RACE}'       => $this->old_member['member_race_id'],
                '{L_CLASS}'      => $this->old_member['member_class_id']);
            $this->log_insert(array(
                'log_type'   => $log_action['header'],
                'log_action' => $log_action)
            );

            //
            // Append success message
            //
            $success_message .= sprintf($user->lang['admin_delete_members_success'], $member_name) . '<br />';
        }

        //
        // Success message
        //
        $this->admin_die($success_message);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function get_old_data($member_name)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $sql = 'SELECT *
                FROM ' . MEMBERS_TABLE . "
                WHERE member_name='" . $member_name . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_member = array(
                'member_name'       => addslashes($row['member_name']),
		'member_id'	    => $row['member_id'],
                'member_earned'     => $row['member_earned'],
                'member_spent'      => $row['member_spent'],
                'member_adjustment' => $row['member_adjustment'],
                'member_level'      => $row['member_level'],
                'member_race_id'    => $row['member_race_id'],
                'member_class_id'   => $row['member_class_id']);
        }
        $db->free_result($result);
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        // New for 1.3 - get class and race information from the database
        // This section populates $eq_classes for the form. They are not
        // populated in a multidimensional array anymore.

        $eq_classes = array();

        $sql = 'SELECT class_id, class_name, class_min_level, class_max_level FROM ' . CLASS_TABLE .' GROUP BY class_id';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {

	   if ( $row['class_min_level'] == '0' ) {
             $option = ( !empty($row['class_name']) ) ? stripslashes($row['class_name'])." Level (".$row['class_min_level']." - ".$row['class_max_level'].")" : '(None)';
           } else {
             $option = ( !empty($row['class_name']) ) ? stripslashes($row['class_name'])." Level ".$row['class_min_level']."+" : '(None)';
	   }

            $tpl->assign_block_vars('class_row', array(
                'VALUE' => $row['class_id'],
                'SELECTED' => ( $this->member['member_class_id'] == $row['class_id'] ) ? ' selected="selected"' : '',
		'OPTION'   => $option )
		);

            $eq_classes[] = $row[0];
        }

        $db->free_result($result);

        // New for 1.3 - get race information from the database

        $eq_races = array();

        $sql = 'SELECT race_id, race_name FROM ' . RACE_TABLE .' GROUP BY race_name';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('race_row', array(
                'VALUE' => $row['race_id'],
                'SELECTED' => ( $this->member['member_race_id'] == $row['race_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => ( !empty($row['race_name']) ) ? stripslashes($row['race_name']) : '(None)')
		);

            $eq_races[] = $row[0];
        }

        $db->free_result($result);

	// end 1.3 changes

        if ( !empty($this->member['member_name']) )
        {
            // Get their correct earned/spent
            $correct_earned = $db->query_first('SELECT sum(r.raid_value)
						FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
						WHERE ra.raid_id = r.raid_id
						AND ra.member_name='" . addslashes($this->member['member_name']) . "'");

            $correct_spent  = $db->query_first('SELECT sum(item_value)
						FROM ' . ITEMS_TABLE . "
						WHERE item_buyer='" . addslashes($this->member['member_name']) . "'");
        }

        //
        // Build rank drop-down
        //
        $sql = 'SELECT rank_id, rank_name
                FROM ' . MEMBER_RANKS_TABLE . '
                ORDER BY rank_id';
        $result = $db->query($sql);

        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('rank_row', array(
                'VALUE'    => $row['rank_id'],
                'SELECTED' => ( $this->member['member_rank_id'] == $row['rank_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => ( !empty($row['rank_name']) ) ? stripslashes($row['rank_name']) : '(None)')
            );
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_MEMBER' => 'manage_members.php' . $SID . '&amp;mode=addmember',

            // Form values
            'MEMBER_NAME'           => $this->member['member_name'],
            'V_MEMBER_NAME'         => ( isset($_POST['add']) ) ? '' : $this->member['member_name'],
            'MEMBER_ID'             => $this->member['member_id'],
            'MEMBER_EARNED'         => $this->member['member_earned'],
            'MEMBER_SPENT'          => $this->member['member_spent'],
            'MEMBER_ADJUSTMENT'     => $this->member['member_adjustment'],
            'MEMBER_CURRENT'        => ( !empty($this->member['member_current']) ) ? $this->member['member_current'] : '0.00',
            'MEMBER_LEVEL'          => $this->member['member_level'],
            'CORRECT_MEMBER_EARNED' => ( !empty($correct_earned) ) ? $correct_earned : '0.00',
            'CORRECT_MEMBER_SPENT'  => ( !empty($correct_spent) ) ? $correct_spent : '0.00',
            'C_MEMBER_CURRENT'      => color_item($this->member['member_current']),

            // Language
            'L_ADD_MEMBER_TITLE' => $user->lang['addmember_title'],
            'L_NAME'             => $user->lang['name'],
            'L_RACE'             => $user->lang['race'],
            'L_CLASS'            => $user->lang['class'],
            'L_LEVEL'            => $user->lang['level'],
            'L_EARNED'           => $user->lang['earned'],
            'L_SPENT'            => $user->lang['spent'],
            'L_ADJUSTMENT'       => $user->lang['adjustment'],
            'L_CURRENT'          => $user->lang['current'],
            'L_SHOULD_BE'        => $user->lang['should_be'],
            'L_MEMBER_RANK'      => $user->lang['member_rank'],
            'L_ADD_MEMBER'       => $user->lang['add_member'],
            'L_RESET'            => $user->lang['reset'],
            'L_UPDATE_MEMBER'    => $user->lang['update_member'],
            'L_DELETE_MEMBER'    => $user->lang['delete_member'],

            // Form validation
            'FV_MEMBER_NAME'       => $this->fv->generate_error('member_name'),
            'FV_MEMBER_LEVEL'      => $this->fv->generate_error('member_level'),
            'FV_MEMBER_EARNED'     => $this->fv->generate_error('member_earned'),
            'FV_MEMBER_SPENT'      => $this->fv->generate_error('member_spent'),
            'FV_MEMBER_ADJUSTMENT' => $this->fv->generate_error('member_adjustment'),
            'FV_MEMBER_CURRENT'    => $this->fv->generate_error('member_current'),

            // Javascript messages
            'MSG_NAME_EMPTY' => $user->lang['fv_required_name'],

            // Buttons
            'S_ADD' => ( !empty($this->url_id) ) ? false : true)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['manage_members_title'],
            'template_file' => 'admin/mm_addmember.html',
            'display'       => true)
        );
    }
}
?>
