<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addturnin.php
 * Began: Sat January 4 2003
 * 
 * $Id: addturnin.php 4 2006-05-08 17:01:47Z tsigo $
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_Turnin extends EQdkp_Admin
{
    var $turnin = array();              // Holds turnin data                    @var turnin
    
    function add_turnin()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        parent::eqdkp_admin();
        
        $this->turnin = array(
            'from' => post_or_db('turnin_from'),
            'to'   => post_or_db('turnin_to')
        );
        
        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_turnin_add'),
            'proceed' => array(
                'name'    => 'proceed',
                'process' => 'display_step2',
                'check'   => 'a_turnin_add'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_raid_'))
        );
    }
    
    function error_check()
    {
        global $user;
        
        if ( isset($_POST['turnin_from']) )
        {
            if ( ($_POST['turnin_from'] == $_POST['turnin_to']) || (empty($_POST['turnin_from'])) || (empty($_POST['turnin_to'])) )
            {
                $this->fv->errors['turnin_from'] = $user->lang['fv_difference_turnin'];
                $this->fv->errors['turnin_to']   = $user->lang['fv_difference_turnin'];
            }
            
            $this->turnin = array(
                'from' => post_or_db('turnin_from'),
                'to'   => post_or_db('turnin_to')
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
        // Get item information
        //
        $sql = 'SELECT item_value, item_name
                FROM ' . ITEMS_TABLE . "
                WHERE item_id='" . $_POST['item_id'] . "'";
        $result = $db->query($sql);
        $row = $db->fetch_record($result);
        
        $item_value = ( !empty($row['item_value']) ) ? $row['item_value'] : '0.00';
        
        //
        // Remove price from the 'From' member
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_spent = member_spent - ' . $item_value . "
                WHERE member_name='" . $_POST['from'] . "'";
        $db->query($sql);
        
        //
        // Add the price to the 'To' member
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_spent = member_spent + ' . $item_value . "
                WHERE member_name='" . $_POST['to'] . "'";
        $db->query($sql);
        
        //
        // Change the buyer
        //
        $sql = 'UPDATE ' . ITEMS_TABLE . "
                SET item_buyer='" . $_POST['to'] . "'
                WHERE item_id='" . $_POST['item_id'] . "'";
        $db->query($sql);
        
        //
        // Logging
        //
        $log_action = array(
            'header'       => '{L_ACTION_TURNIN_ADDED}',
            '{L_ITEM}'     => addslashes($row['item_name']),
            '{L_VALUE}'    => $item_value,
            '{L_FROM}'     => $_POST['from'],
            '{L_TO}'       => $_POST['to'],
            '{L_ADDED_BY}' => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        $success_message = sprintf($user->lang['admin_add_turnin_success'], $row['item_name'], $_POST['from'], $_POST['to']);
        $this->admin_die($success_message);
    }
    
    // ---------------------------------------------------------
    // Process Step 2
    // ---------------------------------------------------------
    function display_step2()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $max_value = $db->query_first('SELECT max(item_value) FROM ' . ITEMS_TABLE . " WHERE item_buyer='" . $_POST['turnin_from'] . "'");
        $float = @explode('.', $max_value);
        $format = '%0'.@strlen($float[0]).'.2f';
        
        $sql = 'SELECT item_id, item_name, item_value
                FROM ' . ITEMS_TABLE . " 
                WHERE item_buyer='" . $_POST['turnin_from'] . "'
                ORDER BY item_name";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('items_row', array(
                'VALUE'  => $row['item_id'],
                'OPTION' => '(' . sprintf($format, $row['item_value']) . ') - ' . stripslashes($row['item_name']))
            );
        }
        
        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_TURNIN' => 'addturnin.php' . $SID,
            'S_STEP1'      => false,
                        
            // Form values
            'FROM'        => $this->turnin['from'],
            'TO'          => $this->turnin['to'],
            'TURNIN_FROM' => $this->turnin['from'],
            'TURNIN_TO'   => $this->turnin['to'],
            
            // Language
            'L_ADD_TURNIN_TITLE' => sprintf($user->lang['addturnin_title'], '2'),
            'L_FROM'             => $user->lang['from'],
            'L_TO'               => $user->lang['to'],
            'L_ADD_TURNIN'       => $user->lang['add_turnin'],
            'L_ITEM'             => $user->lang['item'],
            
            // Form validation
            'FV_TURNIN_FROM' => $this->fv->generate_error('turnin_from'),
            'FV_TURNIN_TO'   => $this->fv->generate_error('turnin_to'),
            
            // Javascript messages
            'MSG_FROM_TO_SAME' => $user->lang['fv_difference_turnin'])
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['addturnin_title'], '2'),
            'template_file' => 'admin/addturnin.html',
            'display'       => true)
        );
    }
    
    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;
        
        $sql = 'SELECT member_name
                FROM ' . MEMBERS_TABLE . '
                ORDER BY member_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('turnin_from_row', array(
                'VALUE'    => $row['member_name'],
                'SELECTED' => ( $this->turnin['from'] == $row['member_name'] ) ? ' selected="selected"' : '',
                'OPTION'   => $row['member_name'])
            );
            
            $tpl->assign_block_vars('turnin_to_row', array(
                'VALUE'    => $row['member_name'],
                'SELECTED' => ( $this->turnin['to'] == $row['member_name'] ) ? ' selected="selected"' : '',
                'OPTION'   => $row['member_name'])
            );
        }
        
        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_TURNIN' => 'addturnin.php' . $SID,
            'S_STEP1'      => true,
            
            // Form values
            'FROM'    => $this->turnin['from'],
            'TO'      => $this->turnin['to'],
            
            // Language
            'L_ADD_TURNIN_TITLE' => sprintf($user->lang['addturnin_title'], '1'),
            'L_FROM'             => $user->lang['from'],
            'L_TO'               => $user->lang['to'],
            'L_PROCEED'          => $user->lang['proceed'],
            
            // Form validation
            'FV_TURNIN_FROM' => $this->fv->generate_error('turnin_from'),
            'FV_TURNIN_TO'   => $this->fv->generate_error('turnin_to'),
            
            // Javascript messages
            'MSG_FROM_TO_SAME' => $user->lang['fv_difference_turnin'])
        );
        
        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['addturnin_title'], '1'),
            'template_file' => 'admin/addturnin.html',
            'display'       => true)
        );
    }
}

$add_turnin = new Add_Turnin;
$add_turnin->process();
?>