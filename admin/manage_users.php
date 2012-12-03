<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * manage_users.php
 * Began: Sun December 29 2002
 *
 * $Id: manage_users.php 4 2006-05-08 17:01:47Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Users extends EQdkp_Admin
{
    var $change_username = false;       // Was the username changed?                        @var change_username
    var $change_password = false;       // Was the password changed?                        @var change_password
    var $user_data       = array();     // Holds user data if URI_NAME is set               @var user_data

    function manage_users()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        // Vars used to confirm deletion
        $confirm_text = $user->lang['confirm_delete_users'];
        $usernames = array();
        if ( isset($_POST['delete']) )
        {
            if ( isset($_POST['user_id']) )
            {
                foreach ( $_POST['user_id'] as $user_id )
                {
                    $username = $db->query_first('SELECT username FROM ' . USERS_TABLE . " WHERE user_id='" . $user_id . "'");
                    $usernames[] = $username;
                }

                $names = implode(', ', $usernames);

                $confirm_text .= '<br /><br />' . $names;
            }
            else
            {
                message_die('No users were selected for deletion.');
            }
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
            'uri_parameter' => 'username',
            'url_id'        => ( sizeof($usernames) > 0 ) ? $names : (( isset($_GET['username']) ) ? $_GET['username'] : ''),
            'script_name'   => 'manage_users.php' . $SID)
        );

        $this->assoc_buttons(array(
            'submit' => array(
                'name'    => 'submit',
                'process' => 'process_submit',
                'check'   => 'a_users_man'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_users_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_users_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_users_man'))
        );

        $this->assoc_params(array(
            'name' => array(
                'name'    => URI_NAME,
                'process' => 'display_form',
                'check'   => 'a_users_man'))
        );
    }

    function error_check()
    {
        global $db, $user;

        // Singular Update
        if ( isset($_POST['submit']) )
        {
            // See if the user exists
            $sql = 'SELECT au.*, u.*
                    FROM ' . USERS_TABLE . ' u
                    LEFT JOIN ' . AUTH_USERS_TABLE . " au
                    ON (u.user_id = au.user_id)
                    WHERE u.username='" . $_POST[URI_NAME] . "'";
            $result = $db->query($sql);
            if ( !$this->user_data = $db->fetch_record($result) )
            {
                message_die($user->lang['error_user_not_found']);
            }
            $db->free_result($result);

            // Error-check the form
            $this->change_username = false;
            if ( $_POST['username'] != $_POST[URI_NAME] )
            {
                // They changed the username, see if it's already registered
                $sql = 'SELECT user_id
                        FROM ' . USERS_TABLE . "
                        WHERE username='".$_POST['username']."'";
                if ( $db->num_rows($db->query($sql)) > 0 )
                {
                    $this->fv->errors['username'] = $user->lang['fv_already_registered_username'];
                }
                $this->change_username = true;
            }
            $this->change_password = false;
            if ( (!empty($_POST['new_user_password1'])) || (!empty($_POST['new_user_password2'])) )
            {
                $this->fv->matching_passwords('new_user_password1', 'new_user_password2', $user->lang['fv_match_password']);
                $this->change_password = true;
            }
            $this->fv->is_number(array(
                'user_alimit' => $user->lang['fv_number'],
                'user_elimit' => $user->lang['fv_number'],
                'user_ilimit' => $user->lang['fv_number'],
                'user_nlimit' => $user->lang['fv_number'],
                'user_rlimit' => $user->lang['fv_number'])
            );

            // Make sure any members associated with this account aren't associated with another account
            if ( (isset($_POST['member_id'])) && (is_array($_POST['member_id'])) )
            {
                // Build array of member_id => member_name
                $member_names = array();
                $sql = 'SELECT member_id, member_name
                        FROM ' . MEMBERS_TABLE . '
                        ORDER BY member_name';
                $result = $db->query($sql);
                while ( $row = $db->fetch_record($result) )
                {
                    $member_names[ $row['member_id'] ] = $row['member_name'];
                }
                $db->free_result($result);

                $sql = 'SELECT member_id
                        FROM ' . MEMBER_USER_TABLE . '
                        WHERE member_id IN (' . implode(', ', $_POST['member_id']) . ')
                        AND user_id != ' . $this->user_data['user_id'];
                $result = $db->query($sql);

                $fv_member_id = '';
                while ( $row = $db->fetch_record($result) )
                {
                    // This member's associated with another account
                    $fv_member_id .= sprintf($user->lang['fv_member_associated'], $member_names[ $row['member_id'] ]) . '<br />';
                }
                $db->free_result($result);

                if ( $fv_member_id != '' )
                {
                    $this->fv->errors['member_id'] = $fv_member_id;
                }
            }
        }
        // Mass Update
        elseif ( isset($_POST['update']) )
        {
        }
        // Mass Delete
        elseif ( isset($_POST['delete']) )
        {
        }
        elseif ( isset($_GET[URI_NAME]) )
        {
            // See if the user exists
            $sql = 'SELECT au.*, u.*
                    FROM ' . USERS_TABLE . ' u
                    LEFT JOIN ' . AUTH_USERS_TABLE . " au
                    ON (u.user_id = au.user_id)
                    WHERE u.username='" . $_GET[URI_NAME] . "'";
            $result = $db->query($sql);
            if ( !$this->user_data = $db->fetch_record($result) )
            {
                message_die($user->lang['error_user_not_found']);
            }
            $db->free_result($result);
        }

        return $this->fv->is_error();
    }

    // ---------------------------------------------------------
    // Process Submit
    // ---------------------------------------------------------
    function process_submit()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $user_id;

        $user_id = $this->user_data['user_id'];

        //
        // Build the query
        //
        // User settings
        $sql = 'UPDATE ' . USERS_TABLE . "
                SET";
        if ( $this->change_username )
        {
            $sql .= " username='".$_POST['username']."', ";
        }
        if ( $this->change_password )
        {
            $sql .= " user_password='".md5($_POST['new_user_password1'])."', ";
        }
        $sql .= " user_email='".$_POST['user_email']."', ";

        $sql .= " user_alimit='".$_POST['user_alimit']."', user_elimit='".$_POST['user_elimit']."', user_ilimit='".$_POST['user_ilimit']."',
                  user_nlimit='".$_POST['user_nlimit']."', user_rlimit='".$_POST['user_rlimit']."', ";

        $sql .= " user_lang='".$_POST['user_lang']."', user_style='".$_POST['user_style']."',
                  user_active='".$_POST['user_active']."'";

        $sql .= " WHERE user_id='".$this->user_data['user_id']."'";

        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not update user information', '', __FILE__, __LINE__, $sql);
        }

        // Permissions
        $sql = 'SELECT auth_id, auth_value
                FROM ' . AUTH_OPTIONS_TABLE . '
                ORDER BY auth_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $r_auth_id    = $row['auth_id'];
            $r_auth_value = $row['auth_value'];

            $chk_auth_value = ( $user->check_auth($r_auth_value, false, $user_id) ) ? 'Y' : 'N';
            $db_auth_value  = ( isset($_POST[$r_auth_value]) )                      ? 'Y' : 'N';

            if ( $chk_auth_value != $db_auth_value )
            {
               $this->update_auth_users($r_auth_id, $db_auth_value);
            }
        }
        $db->free_result($result);

        // Users -> Members associations
        $sql = 'DELETE FROM ' . MEMBER_USER_TABLE . '
                WHERE user_id = ' . $this->user_data['user_id'];
        $db->query($sql);

        if ( (isset($_POST['member_id'])) && (is_array($_POST['member_id'])) )
        {
            $sql = 'INSERT INTO ' . MEMBER_USER_TABLE . '
                    (member_id, user_id)
                    VALUES ';

            $query = array();
            foreach ( $_POST['member_id'] as $member_id )
            {
                $query[] = '(' . $member_id . ', ' . $this->user_data['user_id'] . ')';
            }

            $sql .= implode(', ', $query);
            $db->query($sql);
        }

        // See if any plugins need to update the DB
        $pm->do_hooks('/admin/manage_users.php?action=update');

        $this->admin_die($user->lang['update_settings_success']);
    }

    // ---------------------------------------------------------
    // Process Mass Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        if ( isset($_POST['user_id']) )
        {
            $user_ids = $_POST['user_id'];

            // Delete existing permissions for these users
            $sql = 'DELETE FROM ' . AUTH_USERS_TABLE . '
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);

            // Permissions
            $sql = 'SELECT auth_id, auth_value
                    FROM ' . AUTH_OPTIONS_TABLE . '
                    ORDER BY auth_id';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $permissions[ $row['auth_id'] ] = $row['auth_value'];
            }
            $db->free_result($result);

            foreach ( $user_ids as $user_id )
            {
                $query = array();
                $sql = 'INSERT INTO ' . AUTH_USERS_TABLE . '
                        (user_id, auth_id, auth_setting)
                        VALUES ';
                foreach ( $permissions as $auth_id => $auth_value )
                {
                    $query[] = "('" . $user_id . "', '" . $auth_id . "', " . (( isset($_POST[$auth_value]) ) ? "'Y'" : "'N'") . ')';
                }
                $db->query($sql . implode(', ', $query));
            }

            $this->admin_die($user->lang['admin_set_perms_success']);
        }
        else
        {
            message_die('No users were selected for updating.');
        }
    }

    // ---------------------------------------------------------
    // Process (Mass) Delete
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        if ( isset($_POST['username']) )
        {
            $usernames = explode(', ', $_POST['username']);

            // Find user IDs
            $user_ids = array();
            $sql = 'SELECT user_id, username
                    FROM ' . USERS_TABLE . "
                    WHERE username IN ('" . implode("', '", $usernames) . "')";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $user_ids[ $row['username'] ] = $row['user_id'];
            }
            $db->free_result($result);

            // Delete from auth_user
            $sql = 'DELETE FROM ' . AUTH_USERS_TABLE . '
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);

            // Delete from users
            $sql = 'DELETE FROM ' . USERS_TABLE . '
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);

            // Delete from member users
            $sql = 'DELETE FROM ' . MEMBER_USER_TABLE . '
                    WHERE user_id IN (' . implode(', ', $user_ids) . ')';
            $db->query($sql);

            // Success message
            $success_message = '';
            foreach ( $usernames as $username )
            {
                $success_message .= sprintf($user->lang['admin_delete_user_success'], $username) . '<br />';
            }

            $link_list = array(
                $user->lang['manage_users'] => 'manage_users.php' . $SID);

            $this->admin_die($success_message, $link_list);
        }
        else
        {
            message_die('No users were selected for deleting.');
        }
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function update_auth_users($auth_id, $auth_setting = 'N', $check_query_type = true)
    {
        global $db, $user_id;

        $upd_ins = ( $check_query_type ) ? $this->switch_upd_ins($auth_id, $user_id) : 'upd';

        if ( (empty($auth_id)) || (empty($user_id)) )
        {
            return false;
        }

        if ( $upd_ins == 'upd' )
        {
            $sql = 'UPDATE ' . AUTH_USERS_TABLE . "
                    SET auth_setting='".$auth_setting."'
                    WHERE auth_id='".$auth_id."'
                    AND user_id='".$user_id."'";
        }
        else
        {
            $sql = 'INSERT INTO ' . AUTH_USERS_TABLE . "
                    (user_id, auth_id, auth_setting)
                    VALUES ('".$user_id."','".$auth_id."','".$auth_setting."')";
        }

        if ( !($result = $db->query($sql)) )
        {
            return false;
        }
        return true;
    }

function convert_user_classname($classname)
	{
		switch ($classname) {
			# Englische Namen sind OK und müssen nicht umgewandelt werden.

			case "Druid"		: break;
			case "Warlock"		: break;
			case "Hunter"		: break;
			case "Warrior"		: break;
			case "Mage"			: break;
			case "Paladin"		: break;
			case "Priest"		: break;
			case "Shaman"		: break;
			case "Rogue"		: break;
			case "Unknown"		: break;

			# Deutsche Klassennamen müssen umgewandelt werden.

			case "Druide"		: $classname = "Druid";		break;
			case "Hexenmeister"	: $classname = "Warlock";	break;
			case "Jäger"		: $classname = "Hunter";	break;
			case "Krieger"		: $classname = "Warrior";	break;
			case "Magier"		: $classname = "Mage";		break;
			case "Paladin"		: $classname = "Paladin";	break;
			case "Priester"		: $classname = "Priest";	break;
			case "Schurke"		: $classname = "Rogue";		break;
			case "Schamane"		: $classname = "Shaman";	break;
			case "Unbekannt"	: $classname = "Unknown";	break;
			}
		return $classname;
        return;
    }

    function switch_upd_ins($auth_id, $user_id)
    {
        global $db;

        $sql = 'SELECT o.auth_value
                FROM ' . AUTH_OPTIONS_TABLE . ' o, ' . AUTH_USERS_TABLE . " u
                WHERE (u.auth_id = o.auth_id)
                AND (u.user_id='".$user_id."')
                AND u.auth_id='".$auth_id."'";
        if ( $db->num_rows($db->query($sql)) > 0 )
        {
            return 'upd';
        }
        return 'ins';
    }

    // ---------------------------------------------------------
    // Display
    // ---------------------------------------------------------
    function display_list()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $sort_order = array(
            0 => array('u.username', 'u.username desc'),
            1 => array('u.user_email', 'u.user_email desc'),
            2 => array('u.user_lastvisit desc', 'u.user_lastvisit'),
            3 => array('u.user_active desc', 'u.user_active'),
            4 => array('s.session_id desc', 's.session_id')
        );

        $current_order = switch_order($sort_order);

        $total_users = $db->query_first('SELECT count(*) FROM ' . USERS_TABLE);
        $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
		// Build array of member_id => member_name
           $member_names = array();
		   $member_class_id = array();
		   $member_rank_id = array();

           $sql = 'SELECT member_id, member_name, member_rank_id, member_class_id
                  FROM ' . MEMBERS_TABLE . '
                  ORDER BY member_name';
           $result = $db->query($sql);
           while ( $row = $db->fetch_record($result) )
           {
				$member_names[ $row['member_id'] ] = $row['member_name'];		
			
			      $sql = 'SELECT rank_name
                  FROM ' . MEMBER_RANKS_TABLE . '
                  WHERE rank_id = '. $row['member_rank_id'] .'';
                  $resulttemp = $db->query($sql);
           		  while ( $rowtemp = $db->fetch_record($resulttemp) ) { $member_rank_id[ $row['member_id'] ] = $rowtemp['rank_name']; }
			      $sql = 'SELECT class_name
                  FROM ' . CLASS_TABLE . '
                  WHERE class_id = '. $row['member_class_id'] .'';
                  $resulttemp = $db->query($sql);
           		  while ( $rowtemp = $db->fetch_record($resulttemp) ) { $member_class_id[ $row['member_id'] ] = $rowtemp['class_name']; }  
           }
           $db->free_result($result);

        $sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active, s.session_id
                FROM (' . USERS_TABLE . ' u
                LEFT JOIN ' . SESSIONS_TABLE . ' s
                ON u.user_id = s.session_user_id)
                GROUP BY u.username
                ORDER BY ' . $current_order['sql'] . '
                LIMIT ' . $start . ',100';
        if ( !($result = $db->query($sql)) )
        {
            message_die('Could not obtain user information', '', __FILE__, __LINE__, $sql);
        }
        while ( $row = $db->fetch_record($result) )
        {
            $user_online = ( !empty($row['session_id']) ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";
            $user_active = ( $row['user_active'] == '1' ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";
			$memberuserid = $row['user_id'];

			$sql = 'SELECT member_id 
				   FROM ' . MEMBER_USER_TABLE . ' 
				   WHERE user_id = '. $memberuserid .''; 
            $result2 = $db->query($sql);

			$fv_member_id = '';
            while ( $row2 = $db->fetch_record($result2) )
                {
			switch ($this->convert_user_classname($member_class_id[ $row2['member_id']])) {
			case "Druid"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Druid.gif' alt=''>&nbsp;<font color=#FF7C0A>";break;
			case "Warlock"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Warlock.gif' alt=''>&nbsp;<font color=#9382C9>";break;
			case "Hunter"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Hunter.gif' alt=''>&nbsp;<font color=#AAD372>";break;
			case "Warrior"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Warrior.gif' alt=''>&nbsp;<font color=#C69B6D>";break;
			case "Mage"			: $fv_member_id .= "<img width=15 height=15 src='../images/class/Mage.gif' alt=''>&nbsp;<font color=#68CCEF>";break;
			case "Paladin"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Paladin.gif' alt=''>&nbsp;<font color=#F48CBA>";break;
			case "Priest"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Priest.gif' alt=''>&nbsp;<font color=#FFFFFF>";break;
			case "Rogue"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Rogue.gif' alt=''>&nbsp;<font color=#FFF468>";break;
			case "Shaman"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Shaman.gif' alt=''>&nbsp;<font color=#F48CBA>";break;
			case "Unknown"		: $fv_member_id .= "<img width=15 height=15 src='../images/class/Unknown.gif' alt=''>&nbsp;<font style=\"font-weight:bold\" color=#FF0000>";break;
			}
            $fv_member_id .= $member_names[$row2['member_id']]. "</font><br>";

                }
				
            $tpl->assign_block_vars('users_row', array(
                'ROW_CLASS'     => $eqdkp->switch_row_class(),
                'U_MANAGE_USER' => 'manage_users.php'.$SID.'&amp;' . URI_NAME . '='.$row['username'],
                'USER_ID'       => $row['user_id'],
                'CHARAKTER'		=> $fv_member_id,
                'NAME_STYLE'    => ( $user->check_auth('a_', false, $row['user_id']) ) ? 'font-weight: bold' : 'font-weight: none',
                'USERNAME'      => $row['username'],
                'U_MAIL_USER'   => ( !empty($row['user_email']) ) ? 'mailto:'.$row['user_email'] : '',
                'EMAIL'         => ( !empty($row['user_email']) ) ? $row['user_email'] : '&nbsp;',
                'LAST_VISIT'    => date($user->style['date_time'], $row['user_lastvisit']),
                'ACTIVE'        => $user_active,
                'ONLINE'        => $user_online)
            );
        }
        $db->free_result($result);

        //
        // Build the user permissions
        //
        $user_permissions = array(
            // Events
            $user->lang['events'] => array(
                array('CBNAME' => 'a_event_add',  'CBCHECKED' => A_EVENT_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_event_upd',  'CBCHECKED' => A_EVENT_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_event_del',  'CBCHECKED' => A_EVENT_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_event_list', 'CBCHECKED' => U_EVENT_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_event_view', 'CBCHECKED' => U_EVENT_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Group adjustments
            $user->lang['group_adjustments'] => array(
                array('CBNAME' => 'a_groupadj_add', 'CBCHECKED' => A_GROUPADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_groupadj_upd', 'CBCHECKED' => A_GROUPADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_groupadj_del', 'CBCHECKED' => A_GROUPADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Individual adjustments
            $user->lang['individual_adjustments'] => array(
                array('CBNAME' => 'a_indivadj_add', 'CBCHECKED' => A_INDIVADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_indivadj_upd', 'CBCHECKED' => A_INDIVADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_indivadj_del', 'CBCHECKED' => A_INDIVADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Items
            $user->lang['items'] => array(
                array('CBNAME' => 'a_item_add',  'CBCHECKED' => A_ITEM_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_item_upd',  'CBCHECKED' => A_ITEM_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_item_del',  'CBCHECKED' => A_ITEM_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_item_list', 'CBCHECKED' => U_ITEM_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_item_view', 'CBCHECKED' => U_ITEM_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // News
            $user->lang['news'] => array(
                array('CBNAME' => 'a_news_add', 'CBCHECKED' => A_NEWS_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_news_upd', 'CBCHECKED' => A_NEWS_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_news_del', 'CBCHECKED' => A_NEWS_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Raids
            $user->lang['raids'] => array(
                array('CBNAME' => 'a_raid_add',  'CBCHECKED' => A_RAID_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_raid_upd',  'CBCHECKED' => A_RAID_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_raid_del',  'CBCHECKED' => A_RAID_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_raid_list', 'CBCHECKED' => U_RAID_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_raid_view', 'CBCHECKED' => U_RAID_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Turn-ins
            $user->lang['turn_ins'] => array(
                array('CBNAME' => 'a_turnin_add', 'CBCHECKED' => A_TURNIN_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>')
            ),
            // Members
            $user->lang['members'] => array(
                array('CBNAME' => 'a_members_man', 'CBCHECKED' => A_MEMBERS_MAN, 'TEXT' => '<b>' . $user->lang['manage'] . '</b>'),
                array('CBNAME' => 'u_member_list', 'CBCHECKED' => U_MEMBER_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_member_view', 'CBCHECKED' => U_MEMBER_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Manage
            $user->lang['manage'] => array(
                array('CBNAME' => 'a_config_man',  'CBCHECKED' => A_CONFIG_MAN,  'TEXT' => '<b>' . $user->lang['configuration'] . '</b>'),
                array('CBNAME' => 'a_plugins_man', 'CBCHECKED' => A_PLUGINS_MAN, 'TEXT' => '<b>' . $user->lang['plugins'] . '</b>'),
                array('CBNAME' => 'a_styles_man',  'CBCHECKED' => A_STYLES_MAN,  'TEXT' => '<b>' . $user->lang['styles'] . '</b>'),
                array('CBNAME' => 'a_users_man',   'CBCHECKED' => A_USERS_MAN,   'TEXT' => '<b>' . $user->lang['users'] . '</b>')
            ),
            // Logs
            $user->lang['logs'] => array(
                array('CBNAME' => 'a_logs_view', 'CBCHECKED' => A_LOGS_VIEW, 'TEXT' => '<b>' . $user->lang['view'] . '</b>')
            )
        );

        // Add plugin checkboxes to our array
        $pm->generate_permission_boxes($user_permissions);

        // Find out our auth defaults
        $auth_defaults = array();
        $sql = 'SELECT auth_id, auth_value, auth_default
                FROM ' . AUTH_OPTIONS_TABLE . '
                ORDER BY auth_id';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $auth_defaults[ $row['auth_id'] ] = array(
                'auth_id'      => $row['auth_id'],
                'auth_value'   => $row['auth_value'],
                'auth_default' => $row['auth_default']);
        }
        $db->free_result($result);

        foreach ( $user_permissions as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );

            foreach ( $checks as $data )
            {
                $auth_setting = ( isset($auth_defaults[ $data['CBCHECKED'] ]) ) ? $auth_defaults[ $data['CBCHECKED'] ] : null;
                $tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],
                    'CBCHECKED' => ( (!is_null($auth_setting)) && ($auth_setting['auth_default'] == 'Y') ) ? ' checked="checked"' : '',
                    'TEXT'      => $data['TEXT'])
                );
            }
        }
        unset($user_permissions);

        $tpl->assign_vars(array(
            // Language
            'L_MANAGE_USERS'     => $user->lang['manage_users'],
			'L_ACTIVE_CHAR'		 => $user->lang['associated_members'],
            'L_USERNAME'         => $user->lang['username'],
            'L_EMAIL'            => $user->lang['email_address'],
            'L_LAST_VISIT'       => $user->lang['last_visit'],
            'L_ACTIVE'           => $user->lang['active'],
            'L_ONLINE'           => $user->lang['online'],
            'L_MASS_UPDATE'      => $user->lang['mass_update'],
            'L_MASS_UPDATE_NOTE' => $user->lang['mass_update_note'],
            'L_ACCOUNT_ENABLED'  => $user->lang['account_enabled'],
            'L_YES'              => $user->lang['yes'],
            'L_NO'               => $user->lang['no'],
            'L_MASS_DELETE'      => $user->lang['mass_delete'],

            // Sorting
            'O_USERNAME'   => $current_order['uri'][0],
            'O_EMAIL'      => $current_order['uri'][1],
            'O_LAST_VISIT' => $current_order['uri'][2],
            'O_ACTIVE'     => $current_order['uri'][3],
            'O_ONLINE'     => $current_order['uri'][4],

            // Page vars
            'U_MANAGE_USERS'      => 'manage_users.php' . $SID . '&amp;',
            'F_MASS_UPDATE'       => 'manage_users.php' . $SID,
            'START'               => $start,
            'LISTUSERS_FOOTCOUNT' => sprintf($user->lang['listusers_footcount'], $total_users, 100),
            'USER_PAGINATION'     => generate_pagination('manage_users.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_users, 100, $start))
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['manage_users_title'],
            'template_file' => 'admin/listusers.html',
            'display'       => true)
        );
    }

    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $user_id = $this->user_data['user_id'];

        //
        // Build the user permissions
        //
        $user_permissions = array(
            // Events
            $user->lang['events'] => array(
                array('CBNAME' => 'a_event_add',  'CBCHECKED' => A_EVENT_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_event_upd',  'CBCHECKED' => A_EVENT_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_event_del',  'CBCHECKED' => A_EVENT_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_event_list', 'CBCHECKED' => U_EVENT_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_event_view', 'CBCHECKED' => U_EVENT_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Group adjustments
            $user->lang['group_adjustments'] => array(
                array('CBNAME' => 'a_groupadj_add', 'CBCHECKED' => A_GROUPADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_groupadj_upd', 'CBCHECKED' => A_GROUPADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_groupadj_del', 'CBCHECKED' => A_GROUPADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Individual adjustments
            $user->lang['individual_adjustments'] => array(
                array('CBNAME' => 'a_indivadj_add', 'CBCHECKED' => A_INDIVADJ_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_indivadj_upd', 'CBCHECKED' => A_INDIVADJ_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_indivadj_del', 'CBCHECKED' => A_INDIVADJ_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Items
            $user->lang['items'] => array(
                array('CBNAME' => 'a_item_add',  'CBCHECKED' => A_ITEM_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_item_upd',  'CBCHECKED' => A_ITEM_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_item_del',  'CBCHECKED' => A_ITEM_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_item_list', 'CBCHECKED' => U_ITEM_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_item_view', 'CBCHECKED' => U_ITEM_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // News
            $user->lang['news'] => array(
                array('CBNAME' => 'a_news_add', 'CBCHECKED' => A_NEWS_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_news_upd', 'CBCHECKED' => A_NEWS_UPD, 'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_news_del', 'CBCHECKED' => A_NEWS_DEL, 'TEXT' => '<b>' . $user->lang['delete'] . '</b>')
            ),
            // Raids
            $user->lang['raids'] => array(
                array('CBNAME' => 'a_raid_add',  'CBCHECKED' => A_RAID_ADD,  'TEXT' => '<b>' . $user->lang['add'] . '</b>'),
                array('CBNAME' => 'a_raid_upd',  'CBCHECKED' => A_RAID_UPD,  'TEXT' => '<b>' . $user->lang['update'] . '</b>'),
                array('CBNAME' => 'a_raid_del',  'CBCHECKED' => A_RAID_DEL,  'TEXT' => '<b>' . $user->lang['delete'] . '</b>'),
                array('CBNAME' => 'u_raid_list', 'CBCHECKED' => U_RAID_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_raid_view', 'CBCHECKED' => U_RAID_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Turn-ins
            $user->lang['turn_ins'] => array(
                array('CBNAME' => 'a_turnin_add', 'CBCHECKED' => A_TURNIN_ADD, 'TEXT' => '<b>' . $user->lang['add'] . '</b>')
            ),
            // Members
            $user->lang['members'] => array(
                array('CBNAME' => 'a_members_man', 'CBCHECKED' => A_MEMBERS_MAN, 'TEXT' => '<b>' . $user->lang['manage'] . '</b>'),
                array('CBNAME' => 'u_member_list', 'CBCHECKED' => U_MEMBER_LIST, 'TEXT' => $user->lang['list']),
                array('CBNAME' => 'u_member_view', 'CBCHECKED' => U_MEMBER_VIEW, 'TEXT' => $user->lang['view'])
            ),
            // Manage
            $user->lang['manage'] => array(
                array('CBNAME' => 'a_config_man',  'CBCHECKED' => A_CONFIG_MAN,  'TEXT' => '<b>' . $user->lang['configuration'] . '</b>'),
                array('CBNAME' => 'a_plugins_man', 'CBCHECKED' => A_PLUGINS_MAN, 'TEXT' => '<b>' . $user->lang['plugins'] . '</b>'),
                array('CBNAME' => 'a_styles_man',  'CBCHECKED' => A_STYLES_MAN,  'TEXT' => '<b>' . $user->lang['styles'] . '</b>'),
                array('CBNAME' => 'a_users_man',   'CBCHECKED' => A_USERS_MAN,   'TEXT' => '<b>' . $user->lang['users'] . '</b>')
            ),
            // Logs
            $user->lang['logs'] => array(
                array('CBNAME' => 'a_logs_view', 'CBCHECKED' => A_LOGS_VIEW, 'TEXT' => '<b>' . $user->lang['view'] . '</b>')
            )
        );

        // Add plugin checkboxes to our array
        $pm->generate_permission_boxes($user_permissions);

        foreach ( $user_permissions as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );

            foreach ( $checks as $data )
            {
                $tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],
                    'CBCHECKED' => ( $user->check_auth($data['CBNAME'], false, $user_id) ) ? ' checked="checked"' : '',
                    'TEXT'      => $data['TEXT'])
                );
            }
        }
        unset($user_permissions);

        // Build member drop-down
        $sql = 'SELECT m.member_id, m.member_name, mu.user_id
                FROM ' . MEMBERS_TABLE . ' m
                LEFT JOIN ' . MEMBER_USER_TABLE . ' mu
                ON m.member_id = mu.member_id
                GROUP BY m.member_name
                ORDER BY m.member_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('member_row', array(
                'VALUE'    => $row['member_id'],
                'SELECTED' => ( (isset($row['user_id'])) && ($row['user_id'] == $this->user_data['user_id']) ) ? ' selected="selected"' : '',
                'OPTION'   => $row['member_name'])
            );
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            // Form vars
            'F_SETTINGS'         => 'manage_users.php' . $SID,
            'S_CURRENT_PASSWORD' => false,
            'S_NEW_PASSWORD'     => true,
            'S_SETTING_ADMIN'    => true,
            'S_MU_TABLE'         => true,

            // Form values
            'NAME'                    => stripslashes($_REQUEST[URI_NAME]),
            'USER_ID'                 => $this->user_data['user_id'],
            'USERNAME'                => $this->user_data['username'],
            'USER_EMAIL'              => $this->user_data['user_email'],
            'USER_ALIMIT'             => $this->user_data['user_alimit'],
            'USER_ELIMIT'             => $this->user_data['user_elimit'],
            'USER_ILIMIT'             => $this->user_data['user_ilimit'],
            'USER_NLIMIT'             => $this->user_data['user_nlimit'],
            'USER_RLIMIT'             => $this->user_data['user_rlimit'],
            'USER_ACTIVE_YES_CHECKED' => ( $this->user_data['user_active'] == '1' ) ? ' checked="checked"' : '',
            'USER_ACTIVE_NO_CHECKED'  => ( $this->user_data['user_active'] == '0' ) ? ' checked="checked"' : '',

            // Language
            'L_REGISTRATION_INFORMATION' => $user->lang['registration_information'],
            'L_REQUIRED_FIELD_NOTE'      => $user->lang['required_field_note'],
            'L_USERNAME'                 => $user->lang['username'],
            'L_EMAIL_ADDRESS'            => $user->lang['email_address'],
            'L_NEW_PASSWORD'             => $user->lang['new_password'],
            'L_NEW_PASSWORD_NOTE'        => $user->lang['new_password_note'],
            'L_CONFIRM_PASSWORD'         => $user->lang['confirm_password'],
            'L_CONFIRM_PASSWORD_NOTE'    => $user->lang['confirm_password_note'],
            'L_PREFERENCES'              => $user->lang['preferences'],
            'L_ADJUSTMENTS_PER_PAGE'     => $user->lang['adjustments_per_page'],
            'L_EVENTS_PER_PAGE'          => $user->lang['events_per_page'],
            'L_ITEMS_PER_PAGE'           => $user->lang['items_per_page'],
            'L_NEWS_PER_PAGE'            => $user->lang['news_per_page'],
            'L_RAIDS_PER_PAGE'           => $user->lang['raids_per_page'],
            'L_LANGUAGE'                 => $user->lang['language'],
            'L_STYLE'                    => $user->lang['style'],
            'L_PREVIEW'                  => $user->lang['preview'],
            'L_PERMISSIONS'              => $user->lang['permissions'],
            'L_S_ADMIN_NOTE'             => $user->lang['s_admin_note'],
            'L_ACCOUNT_ENABLED'          => $user->lang['account_enabled'],
            'L_YES'                      => $user->lang['yes'],
            'L_NO'                       => $user->lang['no'],
            'L_ASSOCIATED_MEMBERS'       => $user->lang['associated_members'],
            'L_MEMBERS'                  => $user->lang['members'],
            'L_SUBMIT'                   => $user->lang['submit'],
            'L_RESET'                    => $user->lang['reset'],

            // Form validation
            'FV_USERNAME'     => $this->fv->generate_error('username'),
            'FV_NEW_PASSWORD' => $this->fv->generate_error('new_user_password1'),
            'FV_USER_ALIMIT'  => $this->fv->generate_error('user_alimit'),
            'FV_USER_ELIMIT'  => $this->fv->generate_error('user_elimit'),
            'FV_USER_ILIMIT'  => $this->fv->generate_error('user_ilimit'),
            'FV_USER_NLIMIT'  => $this->fv->generate_error('user_nlimit'),
            'FV_USER_RLIMIT'  => $this->fv->generate_error('user_rlimit'),
            'FV_MEMBER_ID'    => $this->fv->generate_error('member_id'))
        );

        $pm->do_hooks('/admin/manage_users.php?action=settings');

        //
        // Build the language drop-down
        //
        if ( $dir = @opendir($eqdkp->root_path . 'language/') )
        {
            while ( $file = @readdir($dir) )
            {
                if ( (!is_file($eqdkp->root_path . 'language/' . $file)) && (!is_link($eqdkp->root_path . 'language/' . $file)) && ($file != '.') && ($file != '..') && ($file != 'CVS') )
                {
                    $tpl->assign_block_vars('lang_row', array(
                        'VALUE'    => $file,
                        'SELECTED' => ( $this->user_data['user_lang'] == $file ) ? ' selected="selected"' : '',
                        'OPTION'   => ucfirst($file))
                    );
                }
            }
        }

        //
        // Build the style drop-down
        //
        $sql = 'SELECT style_id, style_name
                FROM ' . STYLES_TABLE . '
                ORDER BY style_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('style_row', array(
                'VALUE'    => $row['style_id'],
                'SELECTED' => ( $this->user_data['user_style'] == $row['style_id'] ) ? ' selected="selected"' : '',
                'OPTION'   => $row['style_name'])
            );
        }
        $db->free_result($result);

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': Manage Users',
            'template_file' => 'settings.html',
            'display'       => true)
        );
    }
}

$manage_users = new Manage_users;
$manage_users->process();
?>
