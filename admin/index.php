<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * index.php
 * Began: Tue December 24 2002
 *
 * $Id$
 *
 ******************************/

// IN_ADMIN not yet defined, display the main admin page
if ( !defined('IN_ADMIN') )
{
    // ---------------------------------------------------------
    // Function definitions
    // ---------------------------------------------------------
    /**
    * 'Pretty-up' the Location field of the Who's Online list
    * If we recognize the script they're on, return a more friendly
    * location (like most modern forums do), otherwise just return the path
    *
    * @param    string  $page Page URL
    * @return   string
    */

    function resolve_eqdkp_page($page)
    {
        global $db, $eqdkp, $user, $SID;

        $matches = explode('&', $page);

        if ( !empty($matches[0]) )
        {
            // See if we recognize the page/script we're on
            switch ( $matches[0] )
            {
                // ---------------------------------------------------------
                // Admin
                // ---------------------------------------------------------
                case 'addadj':
                    $page = $user->lang['adding_groupadj'];
                    if ( (!empty($matches[1])) && (preg_match('/^' . URI_ADJUSTMENT . '=([0-9]{1,})/', $matches[1], $adjustment_id)) )
                    {
                        $page  = $user->lang['editing_groupadj'] . ': ';
                        $page .= '<a href="addadj.php' . $SID . '&amp;' . URI_ADJUSTMENT . '=' . $adjustment_id[1] . '">' . $adjustment_id[1] . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'addiadj':
                    $page = $user->lang['adding_indivadj'];
                    if ( (!empty($matches[1])) && (preg_match('/^' . URI_ADJUSTMENT . '=([0-9]{1,})/', $matches[1], $adjustment_id)) )
                    {
                        $page  = $user->lang['editing_indivadj'] . ': ';
                        $page .= '<a href="addiadj.php' . $SID . '&amp;' . URI_ADJUSTMENT . '=' . $adjustment_id[1] . '">' . $adjustment_id[1] . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'additem':
                    $page = $user->lang['adding_item'];
                    if ( (!empty($matches[1])) && (preg_match('/^' . URI_ITEM . '=([0-9]{1,})/', $matches[1], $item_id)) )
                    {
                        $item_name = get_item_name($item_id[1]);
                        $page  = $user->lang['editing_item'] . ': ';
                        $page .= '<a href="additem.php' . $SID . '&amp;' . URI_ITEM . '=' . $item_id[1] . '">' . $item_name . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'addnews':
                    $page = $user->lang['adding_news'];
                    if ( (!empty($matches[1])) && (preg_match('/^' . URI_NEWS . '=([0-9]{1,})/', $matches[1], $news_id)) )
                    {
                        $news_name = get_news_name($news_id[1]);
                        $page  = $user->lang['editing_item'] . ': ';
                        $page .= '<a href="addnews.php' . $SID . '&amp;' . URI_NEWS . '=' . $news_id[1] . '">' . $news_name . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'addraid':
                    $page = $user->lang['adding_raid'];

                    if ( (!empty($matches[1])) && (preg_match('/^' . URI_RAID . '=([0-9]{1,})/', $matches[1], $raid_id)) )
                    {
                        $raid_name = get_raid_name($raid_id[1]);
                        $page  = $user->lang['editing_raid'] . ': ';
                        $page .= '<a href="addraid.php' . $SID . '&amp;' . URI_RAID . '=' . $raid_id[1] . '">' . $raid_name . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'addturnin':
                    $page = $user->lang['adding_turnin'];
                    break;
                // ---------------------------------------------------------
                case 'config':
                    $page = $user->lang['managing_config'];
                    break;
                // ---------------------------------------------------------
                case 'index':
                   $page = $user->lang['viewing_admin_index'];
                    break;
                // ---------------------------------------------------------
                case 'logs':
                    $page = $user->lang['viewing_logs'];
                    break;
                // ---------------------------------------------------------
                case 'manage_members':
                    $page = $user->lang['managing_members'];
                    break;
                // ---------------------------------------------------------
                case 'manage_users':
                    $page = $user->lang['managing_users'];
                    break;
                // ---------------------------------------------------------
                case 'mysql_info':
                    $page = $user->lang['viewing_mysql_info'];
                    break;
                // ---------------------------------------------------------
                case 'plugins':
                    $page = $user->lang['managing_plugins'];
                    break;
                // ---------------------------------------------------------
                case 'styles':
                    $page = $user->lang['managing_styles'];
                    break;

                // ---------------------------------------------------------
                // Listing
                // ---------------------------------------------------------
                case 'listadj':
                    if ( (empty($matches[1])) || ($matches[1] == 'group') )
                    {
                        $page = $user->lang['listing_groupadj'];
                    }
                    else
                    {
                        $page = $user->lang['listing_indivadj'];
                    }
                    break;
                // ---------------------------------------------------------
                case 'listevents':
                    $page = $user->lang['listing_events'];
                    break;
                // ---------------------------------------------------------
                case 'listitems':
                    if ( (empty($matches[1])) || ($matches[1] == 'values') )
                    {
                        $page = $user->lang['listing_itemvals'];
                    }
                    else
                    {
                        $page = $user->lang['listing_itemhist'];
                    }
                    break;
                // ---------------------------------------------------------
                case 'listmembers':
                    $page = $user->lang['listing_members'];
                    break;
                // ---------------------------------------------------------
                case 'listraids':
                    $page = $user->lang['listing_raids'];
                    break;

                // ---------------------------------------------------------
                // Misc
                // ---------------------------------------------------------
                case 'parse_log':
                    $page = $user->lang['parsing_log'];
                    break;

                case 'stats':
                    $page = $user->lang['viewing_stats'];
                    break;

                case 'summary':
                    $page = $user->lang['viewing_summary'];
                    break;

                // ---------------------------------------------------------
                // Viewing
                // ---------------------------------------------------------
                case 'viewevent':
                    $page = $user->lang['viewing_event'] . ': ';
                    if ( !empty($matches[1]) )
                    {
                        preg_match('/^' . URI_EVENT . '=([0-9]{1,})/', $matches[1], $event_id);
                        $event_name = get_event_name($event_id[1]);
                        $page .= '<a href="../viewevent.php' . $SID . '&amp;' . URI_EVENT . '=' . $event_id[1] . '" target="_top">' . $event_name . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'viewitem':
                    $page = $user->lang['viewing_item'] . ': ';
                    if ( !empty($matches[1]) )
                    {
                        preg_match('/^' . URI_ITEM . '=([0-9]{1,})/', $matches[1], $item_id);
                        $item_name = get_item_name($item_id[1]);
                        $page .= '<a href="../viewitem.php' . $SID . '&amp;' . URI_ITEM . '=' . $item_id[1] . '" target="_top">' . $item_name . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'viewnews':
                    $page = $user->lang['viewing_news'];
                    break;
                // ---------------------------------------------------------
                case 'viewmember':
                    $page = $user->lang['viewing_member'] . ': ';
                    if ( !empty($matches[1]) )
                    {
                        preg_match('/^' . URI_NAME . '=([A-Za-z]{1,})/', $matches[1], $member_name);
                        $page .= '<a href="../viewmember.php' . $SID . '&amp;' . URI_NAME . '=' . $member_name[1] . '" target="_top">' . $member_name[1] . '</a>';
                    }
                    break;
                // ---------------------------------------------------------
                case 'viewraid':
                    $page = $user->lang['viewing_raid'] . ': ';
                    if ( !empty($matches[1]) )
                    {
                        preg_match('/^' . URI_RAID . '=([0-9]{1,})/', $matches[1], $raid_id);
                        $raid_name = get_raid_name($raid_id[1]);
                        $page .= '<a href="../viewraid.php' . $SID . '&amp;' . URI_RAID . '=' . $raid_id[1] . '" target="_top">' . $raid_name . '</a>';
                    }
                    break;
            }
        }

        return $page;
    }

    function get_event_name($event_id)
    {
        global $db;

        $event_id = intval($event_id);

        $sql = 'SELECT event_name FROM ' . EVENTS_TABLE . " WHERE event_id='" . $event_id . "'";
        $event_name = $db->query_first($sql);

        return ( !empty($event_name) ) ? $event_name : 'Unknown';
    }

    function get_item_name($item_id)
    {
        global $db;

        $item_id = intval($item_id);

        $sql = 'SELECT item_name FROM ' . ITEMS_TABLE . " WHERE item_id='" . $item_id . "'";
        $item_name = $db->query_first($sql);

        return ( !empty($item_name) ) ? $item_name : 'Unknown';
    }

    function get_news_name($news_id)
    {
        global $db;

        $news_id = intval($news_id);

        $sql = 'SELECT news_headline FROM ' . NEWS_TABLE . " WHERE news_id='" . $news_id . "'";
        $news_name = $db->query_first($sql);

        return ( !empty($news_name) ) ? $news_name : 'Unknown';
    }

    function get_raid_name($raid_id)
    {
        global $db;

        $raid_id = intval($raid_id);

        $sql = 'SELECT raid_name FROM ' . RAIDS_TABLE . " WHERE raid_id='" . $raid_id . "'";
        $raid_name = $db->query_first($sql);

        return ( !empty($raid_name) ) ? $raid_name : 'Unknown';
    }

    function get_eqdkp_version()
    {
		/*
    	// Try and get the latest EQdkp version from EQdkp.com
        $sh = @fsockopen('eqdkp.com', 80, $errno, $error, 5);
        if ( !$sh )
        {
            return EQDKP_VERSION;
        }
        else
        {
            @fputs($sh, "GET /version.php HTTP/1.1\r\nHost: eqdkp.com\r\nConnection: close\r\n\r\n");
            while ( !feof($sh) )
            {
                $content = @fgets($sh, 512);
                if ( preg_match('/<version>(.*)<\/version>/', $content, $version) )
                {
                    return $version[1];
                }
            }
        }
        @fclose($sh);

        return EQDKP_VERSION;
        */
    }

    // ---------------------------------------------------------
    // Display the main admin page
    // ---------------------------------------------------------

    define('EQDKP_INC', true);
    define('IN_ADMIN', true);
    $eqdkp_root_path = './../';
    include_once($eqdkp_root_path . 'common.php');

    $user->check_auth('a_');

    $days = ((time() - $eqdkp->config['eqdkp_start']) / 86400);

    $total_members_inactive = $db->query_first('SELECT count(*) FROM ' . MEMBERS_TABLE . " where member_status='0'");
    $total_members_active = $db->query_first('SELECT count(*) FROM ' . MEMBERS_TABLE . " where member_status='1'");
    $total_members = $total_members_active . ' / ' . $total_members_inactive;

    $total_raids = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE);
    $raids_per_day = sprintf("%.2f", ($total_raids / $days));

    $total_items = $db->query_first('SELECT count(*) FROM ' . ITEMS_TABLE);
    $items_per_day = sprintf("%.2f", ($total_items / $days));

    $total_logs = $db->query_first('SELECT count(*) FROM ' . LOGS_TABLE);

    if ( $raids_per_day > $total_raids )
    {
        $raids_per_day = $total_raids;
    }
    if ( $items_per_day > $total_items )
    {
        $items_per_day = $total_items;
    }

    // DB Size - MySQL Only
    if ( DBTYPE == 'mysql' )
    {
        $result = $db->query('SELECT VERSION() AS mysql_version');

        if ( $row = $db->fetch_record($result) )
        {
            $version = $row['mysql_version'];

            if ( preg_match('/^(3\.23|4\.)/', $version) )
            {
                $db_name = ( preg_match('/^(3\.23\.[6-9])|(3\.23\.[1-9][1-9])|(4\.)/', $version) ) ? "`$dbname`" : $dbname;

                $sql = 'SHOW TABLE STATUS
                        FROM ' . $db_name;
                $result = $db->query($sql);

                $dbsize = 0;
                while ( $row = $db->fetch_record($result) )
                {
                    if ( $row['Type'] != 'MRG_MyISAM' )
                    {
                        if ( $table_prefix != '' )
                        {
                            if ( strstr($row['Name'], $table_prefix) )
                            {
                                $dbsize += $row['Data_length'] + $row['Index_length'];
                            }
                        }
                        else
                        {
                            $dbsize += $row['Data_length'] + $row['Index_length'];
                        }
                    }
                }
            }
            else
            {
                $dbsize = $user->lang['not_available'];
            }
        }
        else
        {
            $dbsize = $user->lang['not_available'];
        }
    }
    else
    {
        $dbsize = $user->lang['not_available'];
    }

    if ( is_int($dbsize) )
    {
        $dbsize = ( $dbsize >= 1048576 ) ? sprintf('%.2f MB', ($dbsize / 1048576)) : (($dbsize >= 1024) ? sprintf('%.2f KB', ($dbsize / 1024)) : sprintf('%.2f Bytes', $dbsize));
    }

    //
    // Who's Online
    //
    $sql = 'SELECT s.*, u.username
            FROM ( ' . SESSIONS_TABLE . ' s
            LEFT JOIN ' . USERS_TABLE . ' u
            ON u.user_id = s.session_user_id )
            GROUP BY u.username, s.session_ip
            ORDER BY u.username, s.session_current DESC';
    $result = $db->query($sql);
    while ( $row = $db->fetch_record($result) )
    {
        $session_page = resolve_eqdkp_page($row['session_page']);

        $tpl->assign_block_vars('online_row', array(
            'ROW_CLASS'   => $eqdkp->switch_row_class(),
            'USERNAME'    => ( !empty($row['username']) ) ? $row['username'] : $user->lang['anonymous'],
            'LOGIN'       => date($user->style['date_time'], $row['session_start']),
            'LAST_UPDATE' => date($user->style['date_time'], $row['session_current']),
            'LOCATION'    => $session_page,
            'IP_ADDRESS'  => $row['session_ip'])
        );
    }
    $online_count = $db->num_rows($result);

    // Log Actions
    $s_logs = false;
    if ( $user->check_auth('a_logs_view', false) )
    {
        if ( $total_logs > 0 )
        {
            $sql = 'SELECT l.*, u.username
                    FROM ' . LOGS_TABLE . ' l, ' . USERS_TABLE . ' u
                    WHERE u.user_id=l.admin_id
                    ORDER BY l.log_date DESC
                    LIMIT 10';
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                eval($row['log_action']);

                switch ( $row['log_type'] )
                {
                    case '{L_ACTION_EVENT_ADDED}':
                        $logline = sprintf($user->lang['vlog_event_added'],      $row['username'], $log_action['{L_NAME}'], $log_action['{L_VALUE}']);
                        break;
                    case '{L_ACTION_EVENT_UPDATED}':
                        $logline = sprintf($user->lang['vlog_event_updated'],    $row['username'], $log_action['{L_NAME_BEFORE}']);
                        break;
                    case '{L_ACTION_EVENT_DELETED}':
                        $logline = sprintf($user->lang['vlog_event_deleted'],    $row['username'], $log_action['{L_NAME}']);
                        break;
                    case '{L_ACTION_GROUPADJ_ADDED}':
                        $logline = sprintf($user->lang['vlog_groupadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}']);
                        break;
                    case '{L_ACTION_GROUPADJ_UPDATED}':
                        $logline = sprintf($user->lang['vlog_groupadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}']);
                        break;
                    case '{L_ACTION_GROUPADJ_DELETED}':
                        $logline = sprintf($user->lang['vlog_groupadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}']);
                        break;
                    case '{L_ACTION_HISTORY_TRANSFER}':
                        $logline = sprintf($user->lang['vlog_history_transfer'], $row['username'], $log_action['{L_FROM}'], $log_action['{L_TO}']);
                        break;
                    case '{L_ACTION_INDIVADJ_ADDED}':
                        $logline = sprintf($user->lang['vlog_indivadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}'], count(explode(', ', $log_action['{L_MEMBERS}'])));
                        break;
                    case '{L_ACTION_INDIVADJ_UPDATED}':
                        $logline = sprintf($user->lang['vlog_indivadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}'], $log_action['{L_MEMBERS_BEFORE}']);
                        break;
                    case '{L_ACTION_INDIVADJ_DELETED}':
                        $logline = sprintf($user->lang['vlog_indivadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}'], $log_action['{L_MEMBERS}']);
                        break;
                    case '{L_ACTION_ITEM_ADDED}':
                        $logline = sprintf($user->lang['vlog_item_added'],       $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])), $log_action['{L_VALUE}']);
                        break;
                    case '{L_ACTION_ITEM_UPDATED}':
                        $logline = sprintf($user->lang['vlog_item_updated'],     $row['username'], $log_action['{L_NAME_BEFORE}'], count(explode(', ', $log_action['{L_BUYERS_BEFORE}'])));
                        break;
                    case '{L_ACTION_ITEM_DELETED}':
                        $logline = sprintf($user->lang['vlog_item_deleted'],     $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])));
                        break;
                    case '{L_ACTION_MEMBER_ADDED}':
                        $logline = sprintf($user->lang['vlog_member_added'],     $row['username'], $log_action['{L_NAME}']);
                        break;
                    case '{L_ACTION_MEMBER_UPDATED}':
                        $logline = sprintf($user->lang['vlog_member_updated'],   $row['username'], $log_action['{L_NAME_BEFORE}']);
                        break;
                    case '{L_ACTION_MEMBER_DELETED}':
                        $logline = sprintf($user->lang['vlog_member_deleted'],   $row['username'], $log_action['{L_NAME}']);
                        break;
                    case '{L_ACTION_NEWS_ADDED}':
                        $logline = sprintf($user->lang['vlog_news_added'],       $row['username'], $log_action['{L_HEADLINE}']);
                        break;
                    case '{L_ACTION_NEWS_UPDATED}':
                        $logline = sprintf($user->lang['vlog_news_updated'],     $row['username'], $log_action['{L_HEADLINE_BEFORE}']);
                        break;
                    case '{L_ACTION_NEWS_DELETED}':
                        $logline = sprintf($user->lang['vlog_news_deleted'],     $row['username'], $log_action['{L_HEADLINE}']);
                        break;
                    case '{L_ACTION_RAID_ADDED}':
                        $logline = sprintf($user->lang['vlog_raid_added'],       $row['username'], $log_action['{L_EVENT}']);
                        break;
                    case '{L_ACTION_RAID_UPDATED}':
                        $logline = sprintf($user->lang['vlog_raid_updated'],     $row['username'], $log_action['{L_EVENT_BEFORE}']);
                        break;
                    case '{L_ACTION_RAID_DELETED}':
                        $logline = sprintf($user->lang['vlog_raid_deleted'],     $row['username'], $log_action['{L_EVENT}']);
                        break;
                    case '{L_ACTION_TURNIN_ADDED}':
                        $logline = sprintf($user->lang['vlog_turnin_added'],     $row['username'], $log_action['{L_FROM}'], $log_action['{L_TO}'], $log_action['{L_ITEM}']);
                        break;

                    case '{L_ACTION_USER_ADDED}':
                        $logline = sprintf($user->lang['vlog_user_added'],     $row['username'], $log_action['{L_USER}']);
                        break;
                    case '{L_ACTION_USER_UPDATED}':
                        $logline = sprintf($user->lang['vlog_user_updated'],   $row['username'], $log_action['{L_USER}']);
                        break;
                    case '{L_ACTION_USER_DELETED}':
                        $logline = sprintf($user->lang['vlog_user_deleted'],   $row['username'], $log_action['{L_USER}']);
                        break;

                    case '{L_ACTION_MULTIDKP_ADDED}':
                        $logline = sprintf($user->lang['vlog_multidkp_added'],     $row['username'], $log_action['{L_MULTINAME}']);
                        break;
                    case '{L_ACTION_MULTIDKP_UPDATED}':
                        $logline = sprintf($user->lang['vlog_multidkp_updated'],   $row['username'], $log_action['{L_MULTINAME}']);
                        break;
                    case '{L_ACTION_MULTIDKP_DELETED}':
                        $logline = sprintf($user->lang['vlog_multidkp_deleted'],   $row['username'], $log_action['{L_MULTINAME}']);
                        break;

                }

                #if (!isset($logline) )
                #{
                #$logline = $logline = sprintf($row['log_type'],   $row['username'], $log_action['{L_NAME}']);
              	#}

                unset($log_action);

                // Show the log if we have a valid line for it
                if ( isset($logline) )
                {
                    $tpl->assign_block_vars('actions_row', array(
                        'ROW_CLASS' => $eqdkp->switch_row_class(),
                        'U_VIEW_LOG' => 'logs.php?' . URI_LOG . '='.$row['log_id'],
                        'ACTION' => stripslashes($logline))
                    );
                }
                unset($logline);
            }
            $db->free_result($result);

            $s_logs = true;
        }
    }

    if(file_exists('sqlupdate/backup_data.php'))
    {
			include_once('sqlupdate/backup_data.php');
			$show_update_warning = false ;
			if(isset($a_system))
			{
				foreach($a_system as $key => $value)
				{
					if($value['state']==0)
					{
						$show_update_warning = true ;
					}
				}
			}
		}

    $eqdkp_com_version = EQDKP_VERSION;

    $tpl->assign_vars(array(
        #'S_NEW_VERSION' => ( $eqdkp_com_version != EQDKP_VERSION ) ? true : false,
        'S_LOGS'        => $s_logs,

        'L_VERSION_UPDATE'     => $user->lang['version_update'],
        'L_NEW_VERSION_NOTICE' => $user->lang['upd_admin_need_update']."<br><br> <a href=update.php>".$user->lang['upd_admin_link_update']."</a>" ,

        'L_STATISTICS'         => $user->lang['statistics'],
        'L_NUMBER_OF_MEMBERS'  => $user->lang['number_of_members'],
        'L_NUMBER_OF_RAIDS'    => $user->lang['number_of_raids'],
        'L_NUMBER_OF_ITEMS'    => $user->lang['number_of_items'],
        'L_DATABASE_SIZE'      => $user->lang['database_size'],
        'L_NUMBER_OF_LOGS'     => $user->lang['number_of_logs'],
        'L_RAIDS_PER_DAY'      => $user->lang['raids_per_day'],
        'L_ITEMS_PER_DAY'      => $user->lang['items_per_day'],
        'L_EQDKP_STARTED'      => $user->lang['eqdkp_started'],

        'NUMBER_OF_MEMBERS' => $total_members,
        'NUMBER_OF_RAIDS'   => $total_raids,
        'NUMBER_OF_ITEMS'   => $total_items,
        'DATABASE_SIZE'     => $dbsize,
        'NUMBER_OF_LOGS'    => $total_logs,
        'RAIDS_PER_DAY'     => $raids_per_day,
        'ITEMS_PER_DAY'     => $items_per_day,
        'EQDKP_STARTED'     => date($user->style['date_time'], $eqdkp->config['eqdkp_start']),

        'L_WHO_ONLINE'  => $user->lang['who_online'],
        'L_USERNAME'    => $user->lang['username'],
        'L_LOGIN'       => $user->lang['login'],
        'L_LAST_UPDATE' => $user->lang['last_update'],
        'L_LOCATION'    => $user->lang['location'],
        'L_IP_ADDRESS'  => $user->lang['ip_address'],

        'L_NEW_ACTIONS' => $user->lang['new_actions'],

        'SHOW_UPDATE_WARNING' 	=> $show_update_warning,
        'SHOW_BETA_WARNING' 	=> EQDKPPLUS_VERSION_BETA,
        'BETA_WARNING' 			=> $user->lang['beta_warning'],

        'L_NEW_VERSION_NOTICE_IMAGE' => "<img src='../images/false.png'>",

        'ONLINE_FOOTCOUNT' => sprintf($user->lang['online_footcount'], $online_count))
    );

    $eqdkp->set_vars(array(
        'page_title'    => $user->lang['admin_index_title'],
        'template_file' => 'admin/admin_index.html',
        'display'       => true)
    );
}
// IN_ADMIN already defined, just output the menu
else
{
    // Build a dynamic admin menu
    // Credit to draelon for the idea and original implementation
    // 0 = header
    // 1 - n = array(link, text, auth_check)

    $itscheck = "a_dont_show";
    if($conf_plus['pk_itemstats'] == 1 )
    {$itscheck = "a_item_" ;}

    $admin_menu = array(
        'events' => array(
           99 => 'time.png',
            0 => $user->lang['events'],
            1 => array('link' => 'admin/addevent.php' . $SID,   'text' => $user->lang['add'],  'check' => 'a_event_add'),
            2 => array('link' => 'admin/listevents.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_event_')
        ),
				'gmultidkp' => array(
           99 => 'wand.png',
            0 => $user->lang['Plus_menuentry'],
            1 => array('link' => 'pluskernel/settings.php',         'text' => $user->lang['config_plus'],  'check' => 'a_config_man'),
            2 => array('link' => '',         'text' => $user->lang['plus_vcheck'],  'check' => 'a_config_man', 'spezial' => 'javascript:Updates();'),
            3 => array('link' => 'admin/' . 'addmulti.php' . $SID,   'text' => $user->lang['Multi_addkonto'],  'check' => 'a_config_man'),
            4 => array('link' => 'admin/' .'listmulti.php' . $SID, 'text' => $user->lang['Multi_viewkonten'], 'check' => 'a_config_man')
        ),
        'groupadj' => array(
        	 99 => 'group_add.png',
            0 => $user->lang['group_adjustments'],
            1 => array('link' => 'admin/addadj.php' . $SID,  'text' => $user->lang['add'],  'check' => 'a_groupadj_add'),
            2 => array('link' => 'admin/listadj.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_groupadj_')
        ),
        'indivadj' => array(
        	 99 => 'user_add.png',
            0 => $user->lang['individual_adjustments'],
            1 => array('link' => 'admin/addiadj.php' . $SID,                                      'text' => $user->lang['add'],  'check' => 'a_indivadj_add'),
            2 => array('link' => 'admin/listadj.php' . $SID . '&amp;' . URI_PAGE . '=individual', 'text' => $user->lang['list'], 'check' => 'a_indivadj_')
        ),
        'items' => array(
           99 => 'cart_add.png',
            0 => $user->lang['items'],
            1 => array('link' => 'admin/additem.php' . $SID,   'text' => $user->lang['add'],  'check' => 'a_item_add'),
            2 => array('link' => 'admin/listitems.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_item_'),
            3 => array('link' => 'admin/' .'updateitemstats.php' . $SID, 'text' => 'Update Itemstats', 'check' => $itscheck)
        ),
        'mysql' => array(
           99 => 'coins.png',
            0 => $user->lang['mysql'],
            1 => array('link' => 'admin/mysql_info.php' . $SID, 'text' => $user->lang['mysql_info'], 'check' => 'a_config_man'),
            2 => array('link' => 'admin/backup.php' . $SID, 'text' => $user->lang['backup'], 'check' => 'a_backup')
        ),
        'news' => array(
           99 => 'script.png',
            0 => $user->lang['news'],
            1 => array('link' => 'admin/addnews.php' . $SID,  'text' => $user->lang['add'],  'check' => 'a_news_add'),
            2 => array('link' => 'admin/listnews.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_news_')
        ),
        'raids' => array(
           99 => 'calendar.png',
            0 => $user->lang['raids'],
            1 => array('link' => 'admin/addraid.php' . $SID,   'text' => $user->lang['add'],  'check' => 'a_raid_add'),
            2 => array('link' => 'admin/listraids.php' . $SID, 'text' => $user->lang['list'], 'check' => 'a_raid_'),
            #3 => array('link' => 'admin/' .'lua.php' . $SID,   'text' => $user->lang['lua_parse'],  'check' => 'a_lua_import')
        ),
        'turnin' => array(
           99 => 'note.png',
            0 => $user->lang['turn_ins'],
            1 => array('link' => 'admin/addturnin.php' . $SID, 'text' => $user->lang['add'], 'check' => 'a_turnin_add')
        ),
        'general' => array(
           99 => 'wrench.png',
            0 => $user->lang['general_admin'],
            1 => array('link' => 'admin/settings.php' . $SID,       'text' => $user->lang['configuration'],  'check' => 'a_config_man'),
            2 => array('link' => 'admin/manage_members.php' . $SID, 'text' => $user->lang['manage_members'], 'check' => 'a_members_man'),
            3 => array('link' => 'admin/plugins.php' . $SID,        'text' => $user->lang['manage_plugins'], 'check' => 'a_plugins_man'),
            4 => array('link' => 'admin/manage_users.php' . $SID,   'text' => $user->lang['manage_users'],   'check' => 'a_users_man'),
            5 => array('link' => 'admin/logs.php' . $SID,           'text' => $user->lang['view_logs'],      'check' => 'a_logs_view')
        ),
        'styles' => array(
           99 => 'color_swatch.png',
            0 => $user->lang['styles'],
            1 => array('link' => 'admin/styles.php' . $SID . '&amp;mode=create', 'text' => $user->lang['create'], 'check' => 'a_styles_man'),
            2 => array('link' => 'admin/styles.php' . $SID,                      'text' => $user->lang['manage'], 'check' => 'a_styles_man')
        ),
        'update' => array(
           99 => 'shield.png',
            0 => 'EQDKP Update',
            1 => array('link' => 'admin/' .'update.php', 'text' => 'Update', 'check' => 'a_config_man'),
            2 => array('link' => 'admin/' .'reset.php', 'text'  => 'Reset', 'check' => 'a_config_man'),
       )

    );

    // Sort the array by the keys to make it alphabetical by header (essentially)
    // Note: I considered using the header as the key itself, but this could
    //      possibly break PHP if non-standard characters were used when another language
    //      was in use.
    #ksort($admin_menu);
    if(is_array($admin_menu)) ksort($admin_menu);

    #SK Sort First, the Insert Plugins Link!
    #SK

    // Now get plugin hooks for the menu
    $admin_menu = (is_array($pm->get_menus('admin_menu'))) ? array_merge($admin_menu, $pm->get_menus('admin_menu')) : $admin_menu;

    #reset($admin_menu);
    if(is_array($admin_menu)) reset($admin_menu);

    foreach ( $admin_menu as $k => $v )
    {

        // Restart next loop if the element isn't an array we can use
        if ( !is_array($v) )
        {
            continue;
        }

				if(!isset($v[99]))
				{
				 $header_img = 'plugin.png';
				}
				else
				{
				 $header_img = $v[99] ;
				}

        // Set the header with the first element
        $tpl->assign_block_vars('header_row', array(
            'HEADER' => $v[0],
            'HEADER_IMG' => $header_img,
            )
        );

        foreach ( $v as $k2 => $row )
        {
            // Ignore the first element (header)
            if ( ($k2 == 0) or ($k2 ==99) )
            {
                continue;
            }

            // Show the link if they have permission to use it
            if ( ($row['check'] == '') || ($user->check_auth($row['check'], false)) )
            {
            		if(isset($row['spezial']))
            		{
            			$adm_url = '<a onclick="'.$row['spezial'].'" style="cursor:pointer;" onmouseover="style.textDecoration=\'underline\';" onmouseout="style.textDecoration=\'none\';">' . $row['text'] . '</a>';
            		}else
            		{
            			$adm_url = '<a href="' . $eqdkp_root_path . $row['link'] . '">' . $row['text'] . '</a>';

            		}
                $tpl->assign_block_vars('header_row.menu_row', array(
                    'ROW_CLASS' => $eqdkp->switch_row_class(),
                    'LINK'      => $adm_url)
                );
            }
        }
    }

    $tpl->assign_vars(array(
        'L_ADMINISTRATION' => $user->lang['administration'],
        'L_ADMIN_INDEX'    => $user->lang['admin_index'],
        'L_EQDKP_INDEX'    => $user->lang['eqdkp_index'])
    );
}
?>
