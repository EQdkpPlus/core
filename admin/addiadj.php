<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addiadj.php
 * Began: Sun January 5 2003
 * 
 * $Id: addiadj.php 8 2006-05-08 17:15:20Z tsigo $
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_IndivAdj extends EQdkp_Admin
{
    var $adjustment     = array();      // Holds adjustment data if URI_ADJUSTMENT is set   @var adjustment
    var $old_adjustment = array();      // Holds adjustment data from before POST           @var old_adjustment
    
    function add_indivadj()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        
        parent::eqdkp_admin();
        
        $this->adjustment = array(
            'adjustment_value'  => post_or_db('adjustment_value'),
            'adjustment_reason' => post_or_db('adjustment_reason'),
            'member_names'      => post_or_db('member_names'),
		'raid_name'      => post_or_db('raid_name')
        );
        
        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_iadj'],
            'uri_parameter' => URI_ADJUSTMENT)
        );
        
        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_indivadj_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_indivadj_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_indivadj_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_indivadj_'))
        );

        $cur_hash = hash_filename("addiadj.php");
        //print"HASH::$cur_hash::<br>";

        
        // Build the adjustment aray
        // -----------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT adjustment_value, adjustment_date, adjustment_reason, member_name, raid_name, adjustment_group_key
                    FROM ' . ADJUSTMENTS_TABLE . "
                    WHERE adjustment_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_adjustment']);
            }
            $db->free_result($result);
        
            // If member name isn't set, it's a group adjustment - put them back on that script
            if ( !isset($row['member_name']) )
            {
                redirect('addadj.php' . $SID . '&' . URI_ADJUSTMENT . '='.$adjustment_id);
            }
            
            $this->time = $row['adjustment_date'];
            $this->adjustment = array(
                'adjustment_value'  => post_or_db('adjustment_value',  $row),
                'adjustment_reason' => post_or_db('adjustment_reason', $row),
		    'raid_name'         => post_or_db('raid_name', $row)
            );
            
            $members = array();
            $sql = 'SELECT member_name
                    FROM ' . ADJUSTMENTS_TABLE . "
                    WHERE adjustment_group_key='".$row['adjustment_group_key']."'";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $members[] = $row['member_name'];
            }
            $db->free_result($result);
            
            $this->adjustment['member_names'] = ( !empty($_POST['member_names']) ) ? $_POST['member_names'] : $members;
            unset($row, $members, $sql);
        }
    }
    
    function error_check()
    {
        global $user;
        
        if ( (!isset($_POST['member_names'])) || (!is_array($_POST['member_names'])) )
        {
            $this->fv->errors['member_names'] = $user->lang['fv_required_members'];
        }
        
        $this->fv->is_number('adjustment_value',    $user->lang['fv_number_adjustment']);
        $this->fv->is_filled('adjustment_value',    $user->lang['fv_required_adjustment']);
        $this->fv->is_within_range('mo', 1, 12,     $user->lang['fv_range_month']);
        $this->fv->is_within_range('d',  1, 31,     $user->lang['fv_range_day']);
        $this->fv->is_within_range('y', 1998, 2010, $user->lang['fv_range_year']);
        
if ( (@empty($_POST['raid_name'])) || (@sizeof($_POST['raid_name']) == 0) )
        {
            $this->fv->errors['raid_name'] = $user->lang['fv_required_event_name'];
        }


        $this->time = mktime(0, 0, 0, $_POST['mo'], $_POST['d'], $_POST['y']);
        
        return $this->fv->is_error();
    }
    
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
                  
        //
        // Generate our group key
        //
        $group_key = $this->gen_group_key($this->time, stripslashes($_POST['adjustment_reason']), $_POST['adjustment_value'], $_POST['raid_name']);
        
        //
        // Add adjustment to selected members
        //
        foreach ( $_POST['member_names'] as $member_name )
        {
            $this->add_new_adjustment($member_name, $group_key);
        }
        
        //
        // Logging
        //
        $log_action = array(
            'header'         => '{L_ACTION_INDIVADJ_ADDED}',
            '{L_ADJUSTMENT}' => $_POST['adjustment_value'],
            '{L_REASON}'     => stripslashes($_POST['adjustment_reason']),
            '{L_MEMBERS}'    => implode(', ', $_POST['member_names']),
		'{L_EVENT}'      => $_POST['raid_name'],
            '{L_ADDED_BY}'   => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_add_iadj_success'], $eqdkp->config['dkp_name'], $_POST['adjustment_value'], implode(', ', $_POST['member_names']), $raid_name);
 		$link_list = array(
            $user->lang['list_indivadj'] => 'listadj.php' . $SID . '&amp;' . URI_PAGE . '=individual',
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }
    
    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        
        //
        // Remove the old adjustment from members that received it
        // and then remove the adjustment itself
        //
        $this->remove_old_adjustment();
        
        //
        // Generate a new group key
        //
        $group_key = $this->gen_group_key($this->time, stripslashes($_POST['adjustment_reason']), $_POST['adjustment_value'], $_POST['raid_name']);
        
        //
        // Add the new adjustment to selected members
        //
        foreach ( $_POST['member_names'] as $member_name )
        {
            $this->add_new_adjustment($member_name, $group_key);
        }
        
        //
        // Logging
        //
        $log_action = array(
            'header'                => '{L_ACTION_INDIVADJ_UPDATED}',
            'id'                    => $this->url_id,
            '{L_ADJUSTMENT_BEFORE}' => $this->old_adjustment['adjustment_value'],
            '{L_REASON_BEFORE}'     => $this->old_adjustment['adjustment_reason'],
            '{L_MEMBERS_BEFORE}'    => implode(', ', $this->old_adjustment['member_names']),
					'{L_EVENT_BEFORE}'      => $this->old_adjustment['raid_name'],
            '{L_ADJUSTMENT_AFTER}'  => $this->find_difference($this->old_adjustment['adjustment_value'],  $_POST['adjustment_value']),
            '{L_REASON_AFTER}'      => $this->find_difference($this->old_adjustment['adjustment_reason'], $_POST['adjustment_reason']),
            '{L_MEMBERS_AFTER}'     => implode(', ', $this->find_difference($this->old_adjustment['member_names'], $_POST['member_names'])),
		'{L_EVENT_AFTER}'      => $this->find_difference($this->old_adjustment['raid_name'], $_POST['raid_name']),
            '{L_UPDATED_BY}'        => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_iadj_success'], $eqdkp->config['dkp_name'], $_POST['adjustment_value'], implode(', ', $_POST['member_names']), $_POST['raid_name']);
        $link_list = array(
            $user->lang['list_indivadj'] => 'listadj.php' . $SID . '&amp;' . URI_PAGE . '=individual',
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }
    
    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    // 
    
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        
        //
        // Remove the old adjustment from members that received it
        // and then remove the adjustment itself
        //
        $this->remove_old_adjustment();
        
        //
        // Logging
        //
        $log_action = array(
            'header'         => '{L_ACTION_INDIVADJ_DELETED}',
            'id'             => $this->url_id,
            '{L_ADJUSTMENT}' => $this->old_adjustment['adjustment_value'],
            '{L_REASON}'     => $this->old_adjustment['adjustment_reason'],
            '{L_MEMBERS}'    => implode(', ', $this->old_adjustment['member_names']),
						'{L_EVENT}'     => $this->old_adjustment['raid_name']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success messages
        //
        $success_message = sprintf($user->lang['admin_delete_iadj_success'], $eqdkp->config['dkp_name'], $this->old_adjustment['adjustment_value'], implode(', ', $this->old_adjustment['member_names']), $this->old_adjustment['raid_name']);
        $link_list = array(
            $user->lang['list_indivadj'] => 'listadj.php' . $SID . '&amp;' . URI_PAGE . '=individual',
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }
    
    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function remove_old_adjustment()
    {
        global $db;
    
        $adjustment_ids = array();
        $old_members    = array();
        
        $sql = 'SELECT a2.*
                FROM (' . ADJUSTMENTS_TABLE . ' a1
                LEFT JOIN ' . ADJUSTMENTS_TABLE . " a2
                ON a1.adjustment_group_key = a2.adjustment_group_key)
                WHERE a1.adjustment_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $adjustment_ids[] = $row['adjustment_id'];

            $old_members[] = addslashes($row['member_name']);
            $this->old_adjustment = array(
                'adjustment_value'  => addslashes($row['adjustment_value']),
                'adjustment_date'   => addslashes($row['adjustment_date']),
                'member_names'      => $old_members,
		    'raid_name'         => addslashes($row['raid_name']),
                'adjustment_reason' => addslashes($row['adjustment_reason'])
            );
        }
        
        //
        // Remove the adjustment value from adjustments table
        //
        $sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . '
                WHERE adjustment_id IN (' . implode(', ', $adjustment_ids) . ')';
        $db->query($sql);
        
        //
        // Remove the adjustment value from members
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment - ' . stripslashes($this->old_adjustment['adjustment_value']) . '
                WHERE member_name IN (\'' . implode("', '", $this->old_adjustment['member_names']) . '\')';
        $db->query($sql);
    }
    
    function add_new_adjustment($member_name, $group_key)
    {
        global $db;
        
        //
        // Add the adjustment to the member
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment + ' . $db->escape($_POST['adjustment_value']) . "
                WHERE member_name='" . $member_name . "'";
        $db->query($sql);
        unset($sql);
        
        //
        // Add the adjustment to the database
        //
        $query = $db->build_query('INSERT', array(
            'adjustment_value'     => $_POST['adjustment_value'],
            'adjustment_date'      => $this->time,
            'member_name'          => $member_name,
		'raid_name'            => $_POST['raid_name'],
            'adjustment_reason'    => $_POST['adjustment_reason'],
            'adjustment_group_key' => $group_key,
            'adjustment_added_by'  => $this->admin_user)
        );
        $db->query('INSERT INTO ' . ADJUSTMENTS_TABLE . $query);
    }
	
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        

        //
        // Find the value of the event, or use the one-time value from the form
        //
        $raid_name    = ( is_array($this->raid['raid_name']) ) ? (( isset($this->raid['raid_name'][0]) ) ? $this->raid['raid_name'][0] : '' ) : $this->raid['raid_name'];
        $preset_value = $db->query_first('SELECT event_value FROM ' . EVENTS_TABLE . " WHERE event_name='" . addslashes($raid_name) . "'");
        $adjustment_value = ( $this->raid['adjustment_value'] == 0 )             ? '' : $this->raid['adjustment_value'];
        $adjustment_value = ( $this->raid['adjustment_value'] == $preset_value ) ? '' : $this->raid['adjustment_value'];



        //
        // Build member drop-down
        //
        $sql = 'SELECT member_name
                FROM ' . MEMBERS_TABLE . '
                ORDER BY member_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            if ( $this->url_id )
            {
                $selected = ( @in_array($row['member_name'], $this->adjustment['member_names']) ) ? ' selected="selected"' : '';
            }
            else
            {
                $selected = ( @in_array($row['member_name'], $_POST['member_names']) ) ? ' selected="selected"' : '';
            }
            
            $tpl->assign_block_vars('members_row', array(
                'VALUE'    => $row['member_name'],
                'SELECTED' => $selected,
                'OPTION'   => $row['member_name'])
            );
        }
        $db->free_result($result);
        
 //
        // Build event drop-down
        //
        $max_value = $db->query_first('SELECT max(event_value) FROM ' . EVENTS_TABLE);
        $float = @explode('.', $max_value);
        $format = '%0' . @strlen($float[0]) . '.2f';
        
        $sql = 'SELECT event_id, event_name, event_value
                FROM ' . EVENTS_TABLE . '
                ORDER BY event_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $select_check = ( is_array($this->adjustment['raid_name']) ) ? in_array(stripslashes($row['event_name']), 
            									$this->raadjustmentid['raid_name']) : stripslashes($row['event_name']) == $this->adjustment['raid_name'];
            
            $tpl->assign_block_vars('events_row', array(
                'VALUE'    => stripslashes($row['event_name']),
                'SELECTED' => ( $select_check ) ? ' selected="selected"' : '',
                'OPTION'   => '(' . sprintf($format, $row['event_value']) . ') - ' . stripslashes($row['event_name'])
                )
            );
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_ADJUSTMENT' => 'addiadj.php' . $SID,
            'ADJUSTMENT_ID'    => $this->url_id,
		'U_ADD_EVENT'      => 'addevent.php'.$SID,
                        
            // Form values
            'ADJUSTMENT_VALUE'  => $this->adjustment['adjustment_value'],
            'ADJUSTMENT_REASON' => stripslashes(htmlspecialchars($this->adjustment['adjustment_reason'])),
            'MO'                => date('m', stripslashes($this->time)),
            'D'                 => date('d', stripslashes($this->time)),
            'Y'                 => date('Y', stripslashes($this->time)),
            'H'                 => date('h', stripslashes($this->time)),
            'MI'                => date('i', stripslashes($this->time)),
            'S'                 => date('s', stripslashes($this->time)),
            
            // Language
            'L_ADD_IADJ_TITLE'        => $user->lang['addiadj_title'],
            'L_MEMBERS'               => $user->lang['members'],
		'L_EVENT'                 => $user->lang['event'],
            'L_ADD_EVENT'             => strtolower($user->lang['add_event']),
            'L_HOLD_CTRL_NOTE'        => '(' . $user->lang['hold_ctrl_note'] . ')<br />',
            'L_REASON'                => $user->lang['reason'],
            'L_VALUE'                 => $user->lang['value'],
            'L_ADJUSTMENT_VALUE_NOTE' => strtolower($user->lang['adjustment_value_note']),
            'L_DATE'                  => $user->lang['date'],
            'L_ADD_ADJUSTMENT'        => $user->lang['add_adjustment'],
            'L_RESET'                 => $user->lang['reset'],
            'L_UPDATE_ADJUSTMENT'     => $user->lang['update_adjustment'],
            'L_DELETE_ADJUSTMENT'     => $user->lang['delete_adjustment'],
            
            // Form validation
            'FV_MEMBERS'    => $this->fv->generate_error('member_names'),
		'FV_EVENT_NAME' => $this->fv->generate_error('raid_name'),
            'FV_ADJUSTMENT' => $this->fv->generate_error('adjustment_value'),
            'FV_MO'         => $this->fv->generate_error('mo'),
            'FV_D'          => $this->fv->generate_error('d'),
            'FV_Y'          => $this->fv->generate_error('y'),
            
            // Javascript messages
            'MSG_VALUE_EMPTY' => $user->lang['fv_required_adjustment'],
		'MSG_NAME_EMPTY'      => $user->lang['fv_required_event_name'],
            
            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['addiadj_title'],
            'template_file' => 'admin/addiadj.html',
            'display'       => true)
        );
    }
}

$add_indivadj = new Add_IndivAdj;
$add_indivadj->process();
?>
