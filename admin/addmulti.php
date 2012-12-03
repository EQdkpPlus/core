<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addmulti.php
 * Began: Sat Oktober 14 2006
 *
 * Corgan
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_MultiDKP extends EQdkp_Admin
{
    var $multidkp_data     = array();      // Holds MultiDKP data if URI_ADJUSTMENT is set  @var multidkp_data

    function Add_MultiDKP()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

        parent::eqdkp_admin();

        $this->multidkp_data = array(
            'multidkp_name'  => post_or_db('multidkp_name'),
            'multidkp_disc' => post_or_db('multidkp_disc')
           # 'events'      => post_or_db('events')
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['Multi_confirm_delete'],
            'uri_parameter' => URI_ADJUSTMENT)
        );


        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_event_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_event_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_event_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_event_add'))
        );


        $cur_hash = hash_filename("addmulti.php");
        #print"HASH::$cur_hash::<br>";


        // Build the adjustment aray
        // -----------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT multidkp_id, multidkp_name, multidkp_disc
                    FROM ' . MULTIDKP_TABLE . "
                    WHERE multidkp_id='" . $this->url_id . "'";

            $result = $db->query($sql);

            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['multi_error_invalid']);
            }
            $db->free_result($result);


            $this->multidkp_data = array(
                'multidkp_name'  => post_or_db('multidkp_name',  $row),
                'multidkp_disc' => post_or_db('multidkp_disc', $row)
            );

            $events = array();
            $sql = 'SELECT multidkp2event_multi_id, multidkp2event_eventname
                    FROM ' . MULTIDKP2EVENTS_TABLE . "
                    WHERE multidkp2event_multi_id='".$row['multidkp_id']."'";

            $result = $db->query($sql);

            while ( $row = $db->fetch_record($result) )
            {
                $events[] = $row['multidkp2event_eventname'];
            }
            $db->free_result($result);

            $this->multidkp_data['events'] = ( !empty($_POST['events']) ) ? $_POST['events'] : $events;
            unset($row, $events, $sql);
        }
    }

    function error_check()
    {
        global $user;

        if (!isset($_POST['multidkp_name']))
        {
        	  #echo "here name";
            $this->fv->errors['multidkp_name'] = $user->lang['fv_required_members'];
        }

        if (!isset($_POST['multidkp_disc']))
        {
        	  #echo "here disc";
            $this->fv->errors['multidkp_disc'] = $user->lang['fv_required_members'];
        }
        if (!isset($_POST['events']))
        {
        	  #echo "here events";
            $this->fv->errors['events'] = $user->lang['Multi_required_event'];
        }

        $this->fv->is_filled('multidkp_name',    $user->lang['Multi_required_name']);
        $this->fv->is_filled('multidkp_disc',    $user->lang['Multi_required_disc']);
        $this->fv->is_filled('events',    			 $user->lang['Multi_required_disc']);

      # message_die($this->fv->is_error()) ;
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
        // Insert MultiDKP data
        //

        $clean_multi_name = stripslashes($_POST['multidkp_name']);

        $query = $db->build_query('INSERT', array(
            'multidkp_name'     => $clean_multi_name ,
            'multidkp_disc'     => $_POST['multidkp_disc']
            )
        );
        $sql = 'INSERT INTO ' . MULTIDKP_TABLE . $query ;
        $db->query($sql);

        $this_multi_id = $db->insert_id();

        foreach ( $_POST['events'] as $events )
        {
            $this->add_multievents($this_multi_id, $events);
            $_events .= $events . ', ';
        }

        //
        // Logging
        //
        $log_action = array(
            'header'         	=> '{L_ACTION_MULTIDKP_ADDED}',
            '{L_MULTINAME}'  	=> $clean_multi_name,
            '{L_MULTIDISC}'  	=> $_POST['multidkp_disc'],
            '{L_MULTIEVENTS}'   => $_events);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['Multi_admin_add_multi_success'], $eqdkp->config['multidkp_name'], $_POST['multidkp_disc'], implode(', ', $_POST['events']));
        $link_list = array(
            $user->lang['Multi_pageheader'] => 'listmulti.php',
            $user->lang['Multi_entryheader'] => 'addmulti.php',
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    //
    // Add the events
    //
    function add_multievents($this_multi_id, $events)
    {
        global $db;

        //
        // Add the Eventnames to multi2event
        //

        $query = $db->build_query('INSERT', array(
            'multidkp2event_multi_id'     => $this_multi_id,
            'multidkp2event_eventname'    => $events)
        );
        $db->query('INSERT INTO ' . MULTIDKP2EVENTS_TABLE . $query);

    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

        //
        // update the MultiDKP Data
        //
        $query = $db->build_query('UPDATE', array(
            'multidkp_name' => stripslashes($_POST['multidkp_name']),
            'multidkp_disc' => stripslashes($_POST['multidkp_disc'])
            ));

        $db->query('UPDATE ' . MULTIDKP_TABLE . ' SET ' . $query . " WHERE multidkp_id='" . $this->url_id . "'");

        //
        // Remove the old Events
        //
        $sql = 'DELETE FROM ' . MULTIDKP2EVENTS_TABLE . '
                WHERE multidkp2event_multi_id = '.$this->url_id ;

        $db->query($sql);
				unset($sql);

				//
				//Add the Events again
				//
        foreach ( $_POST['events'] as $events )
        {
            $this->add_multievents($this->url_id, $events);
            $_events .= $events . ', ';
        }

        //
        // Logging
        //
        $log_action = array(
            'header'         	=> '{L_ACTION_MULTIDKP_UPDATED}',
            '{L_MULTINAME}'  	=> stripslashes($_POST['multidkp_name']),
            '{L_MULTIDISC}'  	=> stripslashes($_POST['multidkp_disc']),
            '{L_MULTIEVENTS}'   => $_events);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['Multi_admin_update_multi_success'], $eqdkp->config['multidkp_name'], $_POST['multidkp_disc'], implode(', ', $_POST['events']));
        $link_list = array(
            $user->lang['Multi_pageheader'] => 'listmulti.php',
            $user->lang['Multi_entryheader'] => 'addmulti.php',
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
        // Remove the old adjustment from members that received it
        // and then remove the adjustment itself
        //
        $this->remove_old_multi();

        //
        // Logging
        //
        $log_action = array(
            'header'         	=> '{L_ACTION_MULTIDKP_DELETED}',
            '{L_MULTINAME}'  	=> $this->multidkp_data['multidkp_name']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success messages
        //
        $success_message = sprintf($user->lang['Multi_admin_delete_success'], $this->multidkp_data['multidkp_name'] );
        $link_list = array(
            $user->lang['Multi_pageheader'] => 'listmulti.php',
            $user->lang['Multi_entryheader'] => 'addmulti.php',
            $user->lang['list_members']  => $eqdkp_root_path . 'listmembers.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function remove_old_multi()
    {
        global $db;
        //
        // Remove from MultiDKP Table
        //
				$sql = 'DELETE FROM ' . MULTIDKP_TABLE . '
                WHERE multidkp_id = '.$this->url_id ;
        $db->query($sql);

				$sql = 'DELETE FROM ' . MULTIDKP2EVENTS_TABLE . '
                WHERE multidkp2event_multi_id = '.$this->url_id ;
        $db->query($sql);

    }


    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

        //
        // Build Eventlist
        //
        $sql = 'SELECT event_id, event_name
        						FROM ' . EVENTS_TABLE ;

        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {

            if ( $this->url_id )
            {
                $selected = ( @in_array($row['event_name'], $this->multidkp_data['events']) ) ? ' selected="selected"' : '';
            }
            else
            {
            	if (is_array($_POST['events'])) {
            		$selected = ( @in_array($row['event_name'], $_POST['events']) ) ? ' selected="selected"' : '';
            	}

            }

            $tpl->assign_block_vars('events_row', array(
                'VALUE'    => $row['event_name'],
                'SELECTED' => $selected,
                'OPTION'   => $row['event_name'])
            );

        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_ADJUSTMENT' => 'addmulti.php' . $SID,
            'ADJUSTMENT_ID'    => $this->url_id,

            // Form values
            'MULTIDKP_NAME'  => stripslashes(htmlspecialchars($this->multidkp_data['multidkp_name'])),
            'MULTIDKP_DISC' => stripslashes(htmlspecialchars($this->multidkp_data['multidkp_disc'])),

            // Language
            'L_ADD_MULTI_TITLE'        => $user->lang['Multi_addkonto'],
            'L_EVENTS'               => $user->lang['Multi_chooseevents'],
            'L_HOLD_CTRL_NOTE'        => '(' . $user->lang['hold_ctrl_note'] . ')<br />',
            'L_KONTONAME'                => $user->lang['Multi_kontoname_short'],
            'L_KONTONAMENOTTOLONG'                => $user->lang['Multi_discnottolong'],
            'L_DISC'                 => $user->lang['Multi_discr'],

            'L_RESET'                 => $user->lang['reset'],
            'L_ADD_MULTI'        => $user->lang['Multi_addkonto'],
            'L_UPDATE_MULTI'     => $user->lang['Multi_updatekonto'],
            'L_DELETE_MULTI'     => $user->lang['Multi_deletekonto'],

			'L_MULTI_REQUIRED_EVENT' => $user->lang['Multi_required_event'],
			'L_MULTI_REQUIRED_NAME' => $user->lang['Multi_required_name'],
			'L_MULTI_REQUIRED_DISC' => $user->lang['Multi_required_disc'],

            // Form validation
            'FV_MEMBERS'    => $this->fv->generate_error('MULTIDKP_NAME'),
            'FV_ADJUSTMENT' => $this->fv->generate_error('MULTIDKP_DISC'),


            // Javascript messages
            'MSG_VALUE_EMPTY' => $user->lang['fv_required_adjustment'],

            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['Multi_addkonto'],
            'template_file' => 'admin/addmulti.html',
            'display'       => true)
        );
    }
}

$Add_MultiDKP = new Add_MultiDKP;
$Add_MultiDKP->process();
?>
