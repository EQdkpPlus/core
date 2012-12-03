<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * additem.php
 * Began: Fri December 27 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Add_Item extends EQdkp_Admin
{
    var $item     = array();            // Holds item data if URI_ITEM is set               @var item
    var $old_item = array();            // Holds item data from before POST                 @var old_item

    function add_item()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $eqdkp_root_path, $SID;

        parent::eqdkp_admin();

        $this->item = array(
            'select_item_name' => post_or_db('select_item_name'),
            'item_name'        => post_or_db('item_name'),
            'item_buyers'      => post_or_db('item_buyers'),
            'raid_id'          => post_or_db('raid_id'),
            'game_itemid'      => post_or_db('game_itemid'),
            'item_value'       => post_or_db('item_value')
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_item'],
            'uri_parameter' => URI_ITEM)
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_item_add'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_item_upd'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_item_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_item_'))
        );

        // Build the item array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT item_name, item_buyer, raid_id, item_value, item_date, item_group_key, game_itemid
                    FROM ' . ITEMS_TABLE . "
                    WHERE item_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_item_provided']);
            }
            $db->free_result($result);

            $this->time = $row['item_date'];
            $this->item = array(
                'select_item_name' => post_or_db('select_item_name'),
                'item_name'        => post_or_db('item_name',  $row),
                'game_itemid'      => post_or_db('game_itemid',  $row),
                'raid_id'          => post_or_db('raid_id',    $row),
                'item_value'       => post_or_db('item_value', $row)
            );

            $buyers = array();
            $sql = 'SELECT item_buyer
                    FROM ' . ITEMS_TABLE . "
                    WHERE item_group_key='" . $row['item_group_key'] . "'";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $buyers[] = $row['item_buyer'];
            }
            $this->item['item_buyers'] = ( !empty($_POST['item_buyers']) ) ? $_POST['item_buyers'] : $buyers;
            unset($buyers);
        }
    }

    function error_check()
    {
        global $user;

        if ( (!isset($_POST['item_buyers'])) || (!is_array($_POST['item_buyers'])) )
        {
            $this->fv->errors['item_buyers'] = $user->lang['fv_required_buyers'];
        }
        $this->fv->is_number('item_value', $user->lang['fv_number_value']);
        $this->fv->is_filled(array(
            'raid_id'    => $user->lang['fv_required_raidid'],
            'item_value' => $user->lang['fv_required_value'])
        );

        if ( !empty($_POST['item_name']) )
        {
            $this->item['item_name'] = $_POST['item_name'];
        }
        elseif ( !empty($_POST['select_item_name']) )
        {
            $this->item['item_name'] = $_POST['select_item_name'];
        }
        else
        {
            $this->fv->errors['item_name'] = $user->lang['fv_required_item_name'];
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

        // Get item name from the appropriate field
        $this->item['item_name'] = ( !empty($_POST['item_name']) ) ? $_POST['item_name'] : $_POST['select_item_name'];

        // Find out the item's date based on the raid it's associated with
        $this->time = $db->query_first('SELECT raid_date FROM ' . RAIDS_TABLE . " WHERE raid_id='" . $_POST['raid_id'] . "'");

        //
        // Generate our group key
        //
        $group_key = $this->gen_group_key($this->item['item_name'], $this->time, $_POST['raid_id']);

        //
        // Add item to selected members
        //
        $this->add_new_item($group_key);

        //
        // Logging
        //
        $item_buyers = implode(', ', $_POST['item_buyers']);
        $log_action = array(
            'header'       => '{L_ACTION_ITEM_ADDED}',
            '{L_NAME}'     => $this->item['item_name'],
            '{L_BUYERS}'   => $item_buyers,
            '{L_RAID_ID}'  => $_POST['raid_id'],
            '{L_VALUE}'    => $_POST['item_value'],
            '{L_ADDED_BY}' => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );
        
        //reset cache
        $pdc->del_suffix('dkp');        

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_add_item_success'], $this->item['item_name'], $item_buyers, $_POST['item_value']);
        $link_list = array(
            $user->lang['add_item']   => 'additem.php' . $SID,
            $user->lang['list_items'] => 'listitems.php' . $SID);
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
        // Remove the old item
        //
        $this->remove_old_item();

        // Get item name from the appropriate field
        $this->item['item_name'] = ( !empty($_POST['item_name']) ) ? $_POST['item_name'] : $_POST['select_item_name'];

        // Find out the item's date based on the raid it's associated with
        $this->time = $db->query_first('SELECT raid_date FROM ' . RAIDS_TABLE . " WHERE raid_id='" . $_POST['raid_id'] . "'");

        //
        // Generate our group key
        //
        $group_key = $this->gen_group_key($this->item['item_name'], $this->time, $_POST['raid_id']);

        //
        // Add item to selected members
        //
        $this->add_new_item($group_key);

        //reset cache
        $pdc->del_suffix('dkp');       
        
        //
        // Logging
        //
        $item_buyers = implode(', ', $_POST['item_buyers']);
        $log_action = array(
            'header'             => '{L_ACTION_ITEM_UPDATED}',
            '{L_NAME_BEFORE}'    => $this->old_item['item_name'],
            '{L_BUYERS_BEFORE}'  => implode(', ', $this->old_item['item_buyers']),
            '{L_RAID_ID_BEFORE}' => $this->old_item['raid_id'],
            '{L_VALUE_BEFORE}'   => $this->old_item['item_value'],
            '{L_NAME_AFTER}'     => $this->find_difference($this->old_item['item_name'], $this->item['item_name']),
            '{L_BUYERS_AFTER}'   => implode(', ', $this->find_difference($this->old_item['item_buyers'], $_POST['item_buyers'])),
            '{L_RAID_ID_AFTER}'  => $this->find_difference($this->old_item['raid_id'], $_POST['raid_id']),
            '{L_VALUE_AFTER}'    => $this->find_difference($this->old_item['item_value'], $_POST['item_value']),
            '{L_UPDATED_BY}'     => $this->admin_user);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_update_item_success'], $this->old_item['item_name'], implode(', ', $this->old_item['item_buyers']), $this->old_item['item_value']);
        $link_list = array(
            $user->lang['add_item']   => 'additem.php' . $SID,
            $user->lang['list_items'] => 'listitems.php' . $SID);
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
        // Remove the old item
        //
        $this->remove_old_item();

        //
        // Logging
        //
        $item_buyers = implode(', ', $this->old_item['item_buyers']);
        $log_action = array(
            'header'      => '{L_ACTION_ITEM_DELETED}',
            '{L_NAME}'    => $this->old_item['item_name'],
            '{L_BUYERS}'  => $item_buyers,
            '{L_RAID_ID}' => $this->old_item['raid_id'],
            '{L_VALUE}'   => $this->old_item['item_value']);
        $this->log_insert(array(
            'log_type'   => $log_action['header'],
            'log_action' => $log_action)
        );

        //
        // Success message
        //
        $success_message = sprintf($user->lang['admin_delete_item_success'], $this->old_item['item_name'], $item_buyers, $this->old_item['item_value']);
        $link_list = array(
            $user->lang['add_item']   => 'additem.php' . $SID,
            $user->lang['list_items'] => 'listitems.php' . $SID);
        $this->admin_die($success_message, $link_list);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function remove_old_item()
    {
        global $db;

        $item_ids      = array();
        $old_buyers    = array();

        //
        // Build the item_ids, old_buyers and old_item arrays
        //
        $sql = 'SELECT i2.*
                FROM (' . ITEMS_TABLE . ' i1
                LEFT JOIN ' . ITEMS_TABLE . " i2
                ON i1.item_group_key = i2.item_group_key)
                WHERE i1.item_id='" . $this->url_id . "'";
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $item_ids[] = $row['item_id'];

            $old_buyers[] = addslashes($row['item_buyer']);
            $this->old_item = array(
                'item_name'   => addslashes($row['item_name']),
                'item_buyers' => $old_buyers,
                'raid_id'     => addslashes($row['raid_id']),
                'item_date'   => addslashes($row['item_date']),
                'item_value'  => addslashes($row['item_value']),
                'game_itemid'  => addslashes($row['game_itemid'])

            );
        }

        //
        // Remove the item purchase from the items table
        //
        $sql = 'DELETE FROM ' . ITEMS_TABLE . '
                WHERE item_id IN (' . implode(', ', $item_ids) . ')';
        $db->query($sql);

        //
        // Remove the purchase value from members
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_spent = member_spent - ' . stripslashes($this->old_item['item_value']) . '
                WHERE member_name IN (\'' . implode("', '", $this->old_item['item_buyers']) . '\')';
        $db->query($sql);
    }

    function add_new_item($group_key)
    {
        global $db;

        $query = array();

        foreach ( $_POST['item_buyers'] as $member_name )
        {
            $query[] = $db->build_query('INSERT', array(
                'item_name'      => stripslashes($this->item['item_name']),
                'item_buyer'     => $member_name,
                'raid_id'        => $_POST['raid_id'],
                'item_value'     => $_POST['item_value'],
                'item_date'      => $this->time,
                'item_group_key' => $group_key,
                'item_added_by'  => $this->admin_user,
                'game_itemid'  => $_POST['game_itemid']


                )
            );
        }

        //
        // Add charge to members
        //
        $sql = 'UPDATE ' . MEMBERS_TABLE . '
                SET member_spent = member_spent + ' . $_POST['item_value'] . '
                WHERE member_name IN (\'' . implode("', '", $_POST['item_buyers']) . '\')';
        $db->query($sql);

        //
        // Add purchase(s) to items table
        //
        // Remove the field names from our built queries
        foreach ( $query as $key => $sql )
        {
            $query[$key] = preg_replace('#^.+\) VALUES (\(.+\))#', '\1', $sql);
        }

        $sql = 'INSERT INTO ' . ITEMS_TABLE . '
                (item_name, item_buyer, raid_id, item_value, item_date, item_group_key, item_added_by, game_itemid)
                VALUES ' . implode(', ', $query);
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
        // Build member and buyer drop-downs
        //
        $buyer_source = ( $this->url_id ) ? $this->item['item_buyers'] : (( isset($_POST['item_buyers']) ) ? $_POST['item_buyers'] : '');

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

            if ($buyer_source)
            {
	            if ( @in_array($row['member_name'], $buyer_source) )
	            {
	                $tpl->assign_block_vars('buyers_row', array(
	                    'VALUE'  => $row['member_name'],
	                    'OPTION' => $row['member_name'])
	                );
	            }

            }
        }
        $db->free_result($result);

        //
        // Build raid drop-down
        //
        // Show all raids?
        $show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == 'all') ) ? true : false;

        // Make two_weeks two weeks before the date the item was purchased
        $two_weeks = mktime(0, 0, 0, date('m', $this->time), date('d', $this->time)-14, date('y', $this->time));

        $sql_where_clause = ( $show_all ) ? '' : ' WHERE (raid_date >= ' . $two_weeks . ')';
        $sql = 'SELECT raid_id, raid_name, raid_date
                FROM ' . RAIDS_TABLE .
                $sql_where_clause . '
                ORDER BY raid_date DESC';
        $result = $db->query($sql);
        $raid_id = ( isset($_GET['raid_id']) ) ? $_GET['raid_id'] : '0'; // 'Add items from this raid' link
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('raids_row', array(
                'VALUE'    => $row['raid_id'],
                'SELECTED' => ( ($raid_id == $row['raid_id']) || ($this->item['raid_id'] == $row['raid_id']) ) ? ' selected="selected"' : '',
                // Start Changed By Teclador
                //'OPTION'   => date($user->style['date_notime_short'], $row['raid_date']) . ' - ' . stripslashes($row['raid_name']))
                'OPTION'   => date($user->style['date_notime_short'], $row['raid_date']) . ' - ' . stripslashes($row['raid_name']) . ' - RaidID #' . $row['raid_id'])
                // End Changed By Teclador
            );
        }
        $db->free_result($result);

        //
        // Build item drop-down
        //
        $max_value = $db->query_first('SELECT max(item_value) FROM ' . ITEMS_TABLE);
        $float = @explode('.', $max_value);
        $floatlen = @strlen($float[0]);
        $format = '%0' . $floatlen . '.2f';

        $previous_item = '';
        $sql = 'SELECT item_value, item_name, game_itemid
                FROM ' . ITEMS_TABLE . '
                ORDER BY item_name, item_date DESC';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $item_select_name = stripslashes(trim($row['item_name']));
            $item_name        = stripslashes(trim($this->item['item_name']));
		    $item_value       = $row['item_value'];

            if ( $previous_item != $item_select_name )
            {
                $tpl->assign_block_vars('items_row', array(
                    'VALUE'    => $item_select_name,
                    'SELECTED' => ( ($item_select_name == $item_name) || ($item_select_name == $this->item['select_item_name']) ) ? ' selected="selected"' : '',
		             'OPTION'   => $item_select_name . ' - ' . sprintf($format, $row['item_value']) . ' - '.$row['game_itemid'] )


                );

                $previous_item = $item_select_name;
            }
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_ITEM'        => 'additem.php' . $SID,
            'ITEM_ID'           => $this->url_id,
            'U_ADD_RAID'        => 'addraid.php'.$SID,
            'S_MULTIPLE_BUYERS' => ( !$this->url_id ) ? true : false,

            // Form values
            'ITEM_NAME'  => stripslashes($this->item['item_name']),
            'ITEM_VALUE' => $this->item['item_value'],
            'GAME_ITEMID' => $this->item['game_itemid'],

            // Language
            'L_ADD_ITEM_TITLE'      => $user->lang['additem_title'],
            'L_BUYERS'              => $user->lang['buyers'],
            'L_HOLD_CTRL_NOTE'      => '('.$user->lang['hold_ctrl_note'].')<br />',
            'L_SEARCH_MEMBERS'      => $user->lang['search_members'],
            'L_RAID'                => $user->lang['raid'],
            'L_ADD_RAID'            => strtolower($user->lang['add_raid']),
            'L_NOTE'                => $user->lang['note'],
            'L_ADDITEM_RAIDID_NOTE' => ( !$show_all ) ? sprintf($user->lang['additem_raidid_note'], '<a href="additem.php'.$SID.'&amp;show=all'.(( $this->url_id ) ? '&amp;' . URI_ITEM . '=' . $this->url_id : '').'">')
                                       : $user->lang['additem_raidid_showall_note'],
            'L_ITEM'                => $user->lang['item'],
            'L_SEARCH'              => strtolower($user->lang['search']),
            'L_SEARCH_EXISTING'     => $user->lang['search_existing'],
            'L_SELECT_EXISTING'     => $user->lang['select_existing'],
            'L_OR'                  => strtolower($user->lang['or']),
            'L_ENTER_NEW'           => $user->lang['enter_new'],
            'L_VALUE'               => $user->lang['value'],
            'L_ADD_ITEM'            => $user->lang['add_item'],
            'L_RESET'               => $user->lang['reset'],
            'L_UPDATE_ITEM'         => $user->lang['update_item'],
            'L_DELETE_ITEM'         => $user->lang['delete_item'],

            // Form validation
            'FV_ITEM_BUYERS' => $this->fv->generate_error('item_buyers'),
            'FV_RAID_ID'     => $this->fv->generate_error('raid_id'),
            'FV_ITEM_NAME'   => $this->fv->generate_error('item_name'),
            'FV_ITEM_VALUE'  => $this->fv->generate_error('item_value'),

            // Javascript messages
            'MSG_NAME_EMPTY'    => $user->lang['fv_required_item_name'],
            'MSG_RAID_ID_EMPTY' => $user->lang['fv_required_raidid'],
            'MSG_VALUE_EMPTY'   => $user->lang['fv_required_value'],
            'ITEM_VALUE_LENGTH' => ($floatlen + 3), // The first three digits plus '.00';

            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['additem_title'],
            'template_file' => 'admin/additem.html',
            'display'       => true)
        );
    }
}

class Item_Search extends EQdkp_Admin
{
    function item_search()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'search' => array(
                'name'    => 'search',
                'process' => 'process_search',
                'check'   => 'a_item_'),
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_item_'))
        );
    }

    function error_check()
    {
        $this->fv->is_filled('query');
        if ( strlen($_POST['query']) < 2 )
        {
            $this->fv->errors['query'] = '';
        }

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process item search
    // ---------------------------------------------------------
    function process_search()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $items_array = array();
        if ( !empty($_POST['query']) )
        {
            $eqdkp_items      = array();
            $eqdkp_item_stats = array();

            //
            // Get item names from our standard items table
            //
            $sql = 'SELECT item_name
                    FROM ' . ITEMS_TABLE . "
                    WHERE item_name LIKE '%" . $_POST['query'] . "%'
                    ORDER BY item_name";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $eqdkp_items[] = $row['item_name'];
            }
            $db->free_result($result);

            //
            // Get item names from our stats table if the stats plugin is installed
            //

            // #######################################
            // Alteration sumbitted by Tam
            // #######################################
            if ( $pm->check(PLUGIN_INSTALLED, 'stats') )
                  {
                      $sql = 'SELECT name
                              FROM ' . ITEM_STATS_TABLE . "
                              WHERE name LIKE '%" . $_POST['query'] . "%'
                              ORDER BY name";
                      $result = $db->query($sql);
                      while ( $row = $db->fetch_record($result) )
                      {
                          // Add the item if we don't have it already
                          if ( !in_array($row['item_name'], $eqdkp_items) )
                          {
                              $eqdkp_item_stats[] = $row['name'];
                          }
                      }
                      $db->free_result($result);
                  }
            //#######################################

            //
            // Build the drop-down
            //
            $items_array = array_merge($eqdkp_items, $eqdkp_item_stats);
            $items_array = array_unique($items_array);
            sort($items_array);
            reset($items_array);

            foreach ( $items_array as $item_name )
            {
                $tpl->assign_block_vars('items_row', array(
                    'VALUE'  => stripslashes($item_name),
                    'OPTION' => stripslashes($item_name))
                );
            }
        }

        $tpl->assign_vars(array(
            'S_STEP1' => false,

            'L_RESULTS' => sprintf($user->lang['results'], sizeof($items_array), stripslashes($_POST['query'])),
            'L_SELECT'  => $user->lang['select'])
        );

        $eqdkp->set_vars(array(
            'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['additem_title'],
            'gen_simple_header' => true,
            'template_file'     => 'admin/additem_search.html',
            'display'           => true)
        );
    }

    // ---------------------------------------------------------
    // Display item search
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $tpl->assign_vars(array(
            'F_SEARCH_ITEM' => 'additem.php' . $SID . '&amp;mode=search',
            'S_STEP1'       => true,
            'ONLOAD'        => ' onload="javascript:document.post.query.focus()"',

            'L_ITEM_SEARCH'  => $user->lang['item_search'],
            'L_SEARCH'       => $user->lang['search'],
            'L_CLOSE_WINDOW' => $user->lang['close_window'])
        );

        $eqdkp->set_vars(array(
            'page_title'        => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['additem_title'],
            'gen_simple_header' => true,
            'template_file'     => 'admin/additem_search.html',
            'display'           => true)
        );
    }
}

$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : 'additem';
switch ( $mode )
{
    case 'additem':
        $add_item = new Add_Item;
        $add_item->process();
        break;

    case 'search':
        $item_search = new Item_Search;
        $item_search->process();
        break;
}
?>
