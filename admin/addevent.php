<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * addevent.php
 * Began: Mon December 30 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_Event extends EQdkp_Admin
{
    var $event     = array();           // Holds event data if URI_EVENT Is set             @var event
    var $old_event = array();           // Holds event data from before POST                @var old_event

    function add_event()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path;

        parent::eqdkp_admin();

        $this->event = array(
            'event_name'  => post_or_db('event_name'),
            'event_value' => post_or_db('event_value'),
            'event_icon' => post_or_db('event_icon')
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_event'],
            'uri_parameter' => URI_EVENT)
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
                'check'   => 'a_event_'))
        );

        $cur_hash = hash_filename("addevent.php");
        //print"HASH::$cur_hash::<br>";


        // Build the event array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT event_name, event_value, event_icon
                    FROM ' . EVENTS_TABLE . "
                    WHERE event_id = '" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_event_provided']);
            }
            $db->free_result($result);

            $this->event = array(
                'event_name'  => post_or_db('event_name',  $row),
                'event_value' => post_or_db('event_value', $row),
                'event_icon' => post_or_db('event_icon', $row)

            );
        }
    }

    function error_check()
    {
        global $user;

        $this->fv->is_number('event_value', $user->lang['fv_number_value']);

        $this->fv->is_filled(array(
            'event_name'  => $user->lang['fv_required_name'],
            'event_value' => $user->lang['fv_required_value'])
        );

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
        // Insert event
				// New for 1.3 - stripslashes out of event names.
        //

				$clean_event_name = str_replace("'","", $_POST['event_name']);
				$clean_event_name = str_replace("`","", $clean_event_name);
				$clean_event_name = stripslashes($clean_event_name);

        $query = $db->build_query('INSERT', array(
            'event_name'     => ($clean_event_name),
            'event_value'    => $_POST['event_value'],
            'event_icon'    => $_POST['event_icon'],
            'event_added_by' => $this->admin_user)
        );

        $db->query('INSERT INTO ' . EVENTS_TABLE . $query);
        $this_event_id = $db->insert_id();

        //
        // Call plugin update hooks
        //
        $pm->do_hooks('/admin/addevent.php?action=add');


        //
        // Logging
        //
        $log_action = array(
            'header'       => '{L_ACTION_EVENT_ADDED}',
            'id'           => $this_event_id,
            '{L_NAME}'     => ($clean_event_name),
            '{L_VALUE}'    => $_POST['event_value'],
            '{L_ADDED_BY}' => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_add_event_success'], $_POST['event_value'], $clean_event_name);
        $link_list = array(
            $user->lang['list_events'] => 'listevents.php' . $SID,
            $user->lang['add_event']   => 'addevent.php' . $SID,
            $user->lang['add_raid']    => 'addraid.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID, $conf_plus;


        //
        // Get the old data
        //
        $this->get_old_data();

			 	$clean_event_name = str_replace("'","", $_POST['event_name']);
				$clean_event_name = str_replace("`","", $clean_event_name);
				$clean_event_name = stripslashes($clean_event_name);

        //
        // Update any raids with the old name
        //
        if ( $this->old_event['event_name'] != $clean_event_name )
        {
            $sql = 'UPDATE ' . RAIDS_TABLE . "
                    SET raid_name='" . $clean_event_name . "'
                    WHERE raid_name='" . $this->old_event['event_name'] . "'";
            $db->query($sql);

            #Adjustments
            $sql = 'UPDATE ' . ADJUSTMENTS_TABLE . "
                    SET raid_name='" . $clean_event_name . "'
                    WHERE raid_name='" . $this->old_event['event_name'] . "'";
            $db->query($sql);


			if($conf_plus['pk_multidkp'] == 1)
			{
	            $sql = 'UPDATE ' . MULTIDKP2EVENTS_TABLE . "
	                    SET multidkp2event_eventname='" . $clean_event_name . "'
	                    WHERE multidkp2event_eventname='" . $this->old_event['event_name'] . "'";
	            $db->query($sql);

	            $sql = 'UPDATE ' . ADJUSTMENTS_TABLE . "
	                    SET raid_name='" . $clean_event_name . "'
	                    WHERE raid_name='" . $this->old_event['event_name'] . "'";
	            $db->query($sql);

			}

        }

        //
        // Update the event
        //
        $query = $db->build_query('UPDATE', array(
            'event_name'  => $clean_event_name,
            'event_icon'  => $_POST['event_icon'],
            'event_value' => $_POST['event_value'])
        );
        $sql = 'UPDATE ' . EVENTS_TABLE . ' SET ' . $query . " WHERE event_id='" . $this->url_id . "'";
        $db->query($sql);

        //
        // Call plugin update hooks
        //
        $pm->do_hooks('/admin/addevent.php?action=update');

        //
        // Logging
        //
        $log_action = array(
            'header'           => '{L_ACTION_EVENT_UPDATED}',
            'id'               => $this->url_id,
            '{L_NAME_BEFORE}'  => $this->old_event['event_name'],
            '{L_VALUE_BEFORE}' => $this->old_event['event_value'],
            '{L_NAME_AFTER}'   => $this->find_difference($this->old_event['event_name'],  $clean_event_name),
            '{L_VALUE_AFTER}'  => $this->find_difference($this->old_event['event_value'], $_POST['event_value']),
            '{L_UPDATED_BY}'   => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_event_success'], $_POST['event_value'], $clean_event_name);
        $link_list = array(
            $user->lang['list_events'] => 'listevents.php' . $SID,
            $user->lang['add_event']   => 'addevent.php' . $SID,
            $user->lang['add_raid']    => 'addraid.php' . $SID);
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
        // Delete the event
        //
        $sql = 'DELETE FROM ' . EVENTS_TABLE . "
                WHERE event_id = '" . $this->url_id . "'";
        $db->query($sql);

        //
        // Logging
        //

        $clean_event_name = str_replace("'","", $this->old_event['event_name']);

        $log_action = array(
            'header'    => '{L_ACTION_EVENT_DELETED}',
            'id'        => $this->url_id,
            '{L_NAME}'  => $clean_event_name,
            '{L_VALUE}' => $this->old_event['event_value']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_delete_event_success'], $this->old_event['event_value'], $this->old_event['event_name']);
        $link_list = array(
            $user->lang['list_events'] => 'listevents.php' . $SID,
            $user->lang['add_event']   => 'addevent.php' . $SID,
            $user->lang['add_raid']    => 'addraid.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function get_old_data()
    {
        global $db;

        $sql = 'SELECT event_name, event_value, event_icon
                FROM ' . EVENTS_TABLE . "
                WHERE event_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $this->old_event = array(
                'event_name'  => str_replace("'","",$row['event_name']),
                'event_icon'  => $row['event_icon'],
                'event_value' => addslashes($row['event_value'])
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
        global $eqdkp_root_path, $SID, $game_icons;



				if ($game_icons['event'])
				{
					$path = "../games/".$eqdkp->config['default_game']."/events";
					
				$allowed_types = "(jpg|jpeg|gif|png)";

				if($dir=opendir($path))
				{
					 while($file=readdir($dir))
					 {
					  if (!is_dir($file) && $file != "." && $file != ".." && preg_match("/\." . $allowed_types . "$/i",$file))
					  {

					   $files[]=$file;
					  }
					 }

					 closedir($dir);

					if ( @sizeof($files) > 0 )
		   		{
            $files = array_unique($files);
            sort($files);
            reset($files);
            $rows = ceil(sizeof($files) / $user->style['attendees_columns']);

            for ( $i = 0; $i < $rows; $i++ )
		        {
		          $tpl->assign_block_vars('files_row', array('ID'=>$i));
		          
				      for ( $j = 0; $j < $user->style['attendees_columns']; $j++ )
				      {
				        $offset = ($i + ($rows * $j));
				        $filename = ( isset($files[$offset]) ) ? $files[$offset] : '';
				        
				        // Fill the singe Fields, check if checkbox or not
				        $tpl->assign_block_vars('files_row.fields', array(
				                  'ROWCLASS'  => $eqdkp->switch_row_class(),
                          'NAME'      => $filename ,
                          'CHECKED'   => ( $filename == $this->event['event_icon'] ) ? 'checked="checked"' : '',
                          'IMAGE'     => ($filename) ? "<img height='50' width='50'  src='".$path.'/'.$filename."'>" : '',
                          'CHECKBOX'  => ($filename) ? true : false,
  				                )
                      );
				      }
				    }

				    $column_width = floor(100 / $user->style['attendees_columns']);
		   		} # if size
			 	}# if open dir
			}

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_EVENT' => 'addevent.php' . $SID,
            'EVENT_ID'    => $this->url_id,

            // Form values
            'EVENT_NAME'  => stripslashes($this->event['event_name']),
            'EVENT_VALUE' => stripslashes($this->event['event_value']),

            // Language
            'L_ADD_EVENT_TITLE' => $user->lang['addevent_title'],
            'L_NAME'            => $user->lang['name'],
            'L_DKP_VALUE'       => sprintf($user->lang['dkp_value'], $eqdkp->config['dkp_name']),
            'L_ADD_EVENT'       => $user->lang['add_event'],
            'L_RESET'           => $user->lang['reset'],
            'L_UPDATE_EVENT'    => $user->lang['update_event'],
            'L_DELETE_EVENT'    => $user->lang['delete_event'],

            'L_SELECT_ICON'    => $user->lang['event_icon_header'],

		        'COLUMN_WIDTH' => ( isset($column_width) ) ? $column_width : 0,
		        'COLSPAN'      => $user->style['attendees_columns'],

            // Form validation
            'FV_NAME'  => $this->fv->generate_error('event_name'),
            'FV_VALUE' => $this->fv->generate_error('event_value'),

            // Javascript messages
            'MSG_NAME_EMPTY'  => $user->lang['fv_required_name'],
            'MSG_VALUE_EMPTY' => $user->lang['fv_required_value'],

            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['addevent_title'],
            'template_file' => 'admin/addevent.html',
            'display'       => true)
        );
    }
}

$add_event = new Add_Event;
$add_event->process();
?>
