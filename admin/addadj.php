<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addadj.php
 * Began: Sat January 4 2003
 * 
 * $Id$
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_GroupAdj extends EQdkp_Admin
{
    var $adjustment     = array();      // Holds adjustment data if URI_ADJUSTMENT is set   @var adjustment
    var $old_adjustment = array();      // Holds adjustment data from before POST           @var old_adjustment
    
    function add_groupadj()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path;
        
        parent::eqdkp_admin();
        
        $this->adjustment = array(
            'adjustment_value' => post_or_db('adjustment_value')
        );
        
        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_adj'],
            'uri_parameter' => URI_ADJUSTMENT)
        );
        
        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_groupadj_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_groupadj_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_groupadj_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_groupadj_'))
        );
        
        // Build the adjustment aray
        // -----------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT adjustment_value, member_name
                    FROM ' . ADJUSTMENTS_TABLE . "
                    WHERE adjustment_id='".$this->url_id."'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_adjustment']);
            }
            $db->free_result($result);
        
            // If member name is set, it's an individual adjustment - put them back on that script
            if ( isset($row['member_name']) )
            {
                redirect('addiadj.php' . $SID . '&' . URI_ADJUSTMENT . '='.$this->url_id);
            }
        
            $this->adjustment = array(
                'adjustment_value' => post_or_db('adjustment_value', $row)
            );
        }
    }
    
    function error_check()
    {
        global $user;
        
        if ( sizeof($_POST) > 0 )
        {
            $this->fv->is_number('adjustment_value', $user->lang['fv_number_adjustment']);
            $this->fv->is_filled('adjustment_value', $user->lang['fv_required_adjustment']);
        }
        
        return $this->fv->is_error();
    }
    
    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID, $pdc;
        
        //
        // Change member's adjustment column
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment + ' . $db->escape($_POST['adjustment_value']);
        $db->query($sql);
        
        //
        // Insert adjustment
        //
        $query = $db->build_query('INSERT', array(
            'adjustment_value'    => $_POST['adjustment_value'],
            'adjustment_date'     => $this->time,
            'adjustment_added_by' => $this->admin_user)
        );
        $db->query('INSERT INTO ' . ADJUSTMENTS_TABLE . $query);
        $this_adjustment_id = $db->insert_id();
        
        //
        // Logging
        //
        $log_action = array(
            'header'         => '{L_ACTION_GROUPADJ_ADDED}',
            'id'             => $this_adjustment_id,
            '{L_ADJUSTMENT}' => $_POST['adjustment_value'],
            '{L_ADDED_BY}'   => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //reset cache
        $pdc->del_suffix('dkp');
        
        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_add_adj_success'], $eqdkp->config['dkp_name'], $_POST['adjustment_value']);
        $link_list = array(
            $user->lang['list_groupadj'] => 'listadj.php' . $SID,
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }
    
    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID, $pdc;
        
        //
        // Get the old data
        //
        $this->get_old_data();
        
        //
        // Remove the old adjustment from members that received it
        // If their first raid was before/on the adjustment date, then they
        // would have received a group adjustment
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment - ' . $this->old_adjustment['adjustment_value'] . '
                WHERE member_firstraid <= ' . $this->old_adjustment['adjustment_date'];
        $db->query($sql);
        
        //
        // Add the new adjustment
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment + ' . $db->escape($_POST['adjustment_value']);
        $db->query($sql);
        
        //
        // Update the adjustment table
        //
        $query = $db->build_query('UPDATE', array(
            'adjustment_value'      => $_POST['adjustment_value'],
            'adjustment_updated_by' => $this->admin_user)
        );
        $sql = 'UPDATE ' . ADJUSTMENTS_TABLE . ' SET ' . $query . " WHERE adjustment_id='" . $this->url_id . "'";
        $db->query($sql);
        
        //reset cache
        $pdc->del_suffix('dkp');
        
        
        //
        // Logging
        //
        $log_action = array(
            'header'                => '{L_ACTION_GROUPADJ_UPDATED}',
            'id'                    => $this->url_id,
            '{L_ADJUSTMENT_BEFORE}' => $this->old_adjustment['adjustment_value'],
            '{L_ADJUSTMENT_AFTER}'  => $this->find_difference($this->old_adjustment['adjustment_value'], $_POST['adjustment_value']),
            '{L_UPDATED_BY}'        => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_adj_success'], $eqdkp->config['dkp_name'], $_POST['adjustment_value']);
        $link_list = array(
            $user->lang['list_groupadj'] => 'listadj.php' . $SID,
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }
    
    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        
        //
        // Get the old data
        //
        $this->get_old_data();
        
        //
        // Remove the old adjustment from members that received it
        // If their first raid was before/on the adjustment date, then they
        // would have received a group adjustment
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_adjustment = member_adjustment - ' . $this->old_adjustment['adjustment_value'] . '
                WHERE member_firstraid <= ' . $this->old_adjustment['adjustment_date'];
        $db->query($sql);
        
        //
        // Remove the adjustment from members
        //
        $sql = 'DELETE FROM ' . ADJUSTMENTS_TABLE . "
                WHERE adjustment_id = '" . $this->url_id . "'";
        $db->query($sql);
        
        //
        // Logging
        //    
        $log_action = array(
            'header'         => '{L_ACTION_GROUPADJ_DELETED}',
            'id'             => $this->url_id,
            '{L_ADJUSTMENT}' => $this->old_adjustment['adjustment_value']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //
        // Success message
        //    
        $success_message = sprintf($user->lang['admin_delete_adj_success'], $eqdkp->config['dkp_name'], $this->old_adjustment['adjustment_value']);
        $link_list = array(
            $user->lang['list_groupadj'] => 'listadj.php' . $SID,
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);    
    }
    
    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function get_old_data()
    {
        global $db;
        
        $sql = 'SELECT adjustment_value, adjustment_date
                FROM ' . ADJUSTMENTS_TABLE . "
                WHERE adjustment_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_adjustment = array(
                'adjustment_value' => addslashes($row['adjustment_value']),
                'adjustment_date'  => addslashes($row['adjustment_date'])
            );
        }
        $db->free_result($result);
    }
    
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;
        
        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_ADJUSTMENT' => 'addadj.php' . $SID,
            'ADJUSTMENT_ID'    => $this->url_id,
            
            // Form values
            'ADJUSTMENT' => $this->adjustment['adjustment_value'],
            
            // Language
            'L_ADD_ADJ_TITLE'         => $user->lang['addadj_title'],
            'L_ADJUSTMENT_VALUE'      => $user->lang['adjustment_value'],
            'L_ADJUSTMENT_VALUE_NOTE' => '(' . strtolower($user->lang['adjustment_value_note']) . ')',
            'L_ADD_ADJUSTMENT'        => $user->lang['add_adjustment'],
            'L_RESET'                 => $user->lang['reset'],
            'L_UPDATE_ADJUSTMENT'     => $user->lang['update_adjustment'],
            'L_DELETE_ADJUSTMENT'     => $user->lang['delete_adjustment'],

            // Form validation
            'FV_ADJUSTMENT' => $this->fv->generate_error('adjustment_value'),

            // Javascript messages
            'MSG_VALUE_EMPTY' => $user->lang['fv_required_adjustment'],
            
            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['addadj_title'],
            'template_file' => 'admin/addadj.html',
            'display'       => true)
        );
    }
}

$add_groupadj = new Add_GroupAdj;
$add_groupadj->process();
?>
