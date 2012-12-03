<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * logs.php
 * Began: Tues December 24 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$sort_order = array(
    0 => array('log_date desc', 'log_date'),
    1 => array('log_type', 'log_type desc'),
    2 => array('username', 'username dsec'),
    3 => array('log_ipaddress', 'log_ipaddress desc'),
    4 => array('log_result', 'log_result desc')
);

$current_order = switch_order($sort_order);

// Obtain var settings
$log_id = ( !empty($_GET[URI_LOG]) ) ? intval($_REQUEST[URI_LOG]) : false;
$search = ( !empty($_GET['search']) ) ? true : false;

if ( $log_id )
{
    $action = 'view';
}
else
{
    $action = 'list';
}

$user->check_auth('a_logs_view');

//
// Processing
//
$valid_action_types = array(
    '{L_ACTION_EVENT_ADDED}'      => $user->lang['action_event_added'],
    '{L_ACTION_EVENT_UPDATED}'    => $user->lang['action_event_updated'],
    '{L_ACTION_EVENT_DELETED}'    => $user->lang['action_event_deleted'],
    '{L_ACTION_GROUPADJ_ADDED}'   => $user->lang['action_groupadj_added'],
    '{L_ACTION_GROUPADJ_UPDATED}' => $user->lang['action_groupadj_updated'],
    '{L_ACTION_GROUPADJ_DELETED}' => $user->lang['action_groupadj_deleted'],
    '{L_ACTION_INDIVADJ_ADDED}'   => $user->lang['action_indivadj_added'],
    '{L_ACTION_INDIVADJ_UPDATED}' => $user->lang['action_indivadj_updated'],
    '{L_ACTION_INDIVADJ_DELETED}' => $user->lang['action_indivadj_deleted'],
    '{L_ACTION_ITEM_ADDED}'       => $user->lang['action_item_added'],
    '{L_ACTION_ITEM_UPDATED}'     => $user->lang['action_item_updated'],
    '{L_ACTION_ITEM_DELETED}'     => $user->lang['action_item_deleted'],
    '{L_ACTION_MEMBER_ADDED}'     => $user->lang['action_member_added'],
    '{L_ACTION_MEMBER_UPDATED}'   => $user->lang['action_member_updated'],
    '{L_ACTION_MEMBER_DELETED}'   => $user->lang['action_member_deleted'],
    '{L_ACTION_NEWS_ADDED}'       => $user->lang['action_news_added'],
    '{L_ACTION_NEWS_UPDATED}'     => $user->lang['action_news_updated'],
    '{L_ACTION_NEWS_DELETED}'     => $user->lang['action_news_deleted'],
    '{L_ACTION_RAID_ADDED}'       => $user->lang['action_raid_added'],
    '{L_ACTION_RAID_UPDATED}'     => $user->lang['action_raid_updated'],
    '{L_ACTION_RAID_DELETED}'     => $user->lang['action_raid_deleted'],
    '{L_ACTION_TURNIN_ADDED}'     => $user->lang['action_turnin_added'],

    '{L_ACTION_MULTIDKP_ADDED}'     => $user->lang['action_multidkp_added'],
    '{L_ACTION_MULTIDKP_UPDATED}'   => $user->lang['action_multidkp_updated'],
    '{L_ACTION_MULTIDKP_DELETED}'   => $user->lang['action_multidkp_deleted'],

    '{L_ACTION_USER_ADDED}'     => $user->lang['action_user_added'],
    '{L_ACTION_USER_UPDATED}'   => $user->lang['action_user_updated'],
    '{L_ACTION_USER_DELETED}'   => $user->lang['action_user_deleted']


    );


$valid_action_types = array_merge($valid_action_types, $pm->get_log_actions());

switch ( $action )
{
    case 'view':
        // Get log info
        $sql = 'SELECT l.*, u.username FROM (' . LOGS_TABLE . ' l
                LEFT JOIN ' . USERS_TABLE . " u
                ON u.user_id=l.admin_id )
                WHERE log_id='".$log_id."'";
        $result = $db->query($sql);
        $log = $db->fetch_record($result);
        $db->free_result($result);

        eval($log['log_action']);

        if ( !empty($log_action['header']) )
        {
            $log_header = lang_replace($log_action['header']);
        }

        $eqdkp->switch_row_class();

        foreach ( $log_action as $k => $v )
        {
            if ( $k != 'header' )
            {
                $k = lang_replace($k);
                $v = lang_replace($v);

                $tpl->assign_block_vars('log_row', array(
                    'ROW_CLASS' => $eqdkp->switch_row_class(),
                    'KEY' => stripslashes($k).':',
                    'VALUE' => stripslashes($v))
                );
            }
        }


        $tpl->assign_vars(array(
            'S_LIST' => false,

            'L_LOG_VIEWER' => $user->lang['viewlogs_title'],
            'L_DATE'       => $user->lang['date'],
            'L_USERNAME'   => $user->lang['username'],
            'L_IP_ADDRESS' => $user->lang['ip_address'],
            'L_SESSION_ID' => $user->lang['session_id'],

            'LOG_DATE'       => ( !empty($log['log_date']) ) ? date($user->style['date_time'], $log['log_date']) : '&nbsp;',
            'LOG_USERNAME'   => ( !empty($log['username']) ) ? $log['username'] : '&nbsp;',
            'LOG_IP_ADDRESS' => $log['log_ipaddress'],
            'LOG_SESSION_ID' => $log['log_sid'],
            'LOG_ACTION'     => ( !empty($log_header) ) ? $log_header : '&nbsp;')
        );

        break;
    case 'list':
        $sql = 'SELECT l.*, u.username FROM (' . LOGS_TABLE . ' l
                LEFT JOIN ' . USERS_TABLE . ' u
                ON u.user_id=l.admin_id )';

        $addon_sql = '';
        $search_term = '';

        // If they're looking for something specific, we have to
        // figure out what that is
        if ( $search )
        {
            $search_term = urldecode($_GET['search']);

            // Check if it's an action
            if ( in_array($search_term, $valid_action_types) )
            {
                foreach ( $valid_action_types as $k => $v )
                {
                    if ( $v == $search_term )
                    {
                        $addon_sql = " WHERE l.log_type='".$k."'";
                    }
                }
            }
            // Check it's an IP
            elseif ( preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $search_term) )
            {
                $addon_sql = " WHERE l.log_ipaddress='".$search_term."'";
            }
            // Still going? It's a username
            else
            {
                $addon_sql = " WHERE u.username='".$search_term."'";
            }
        }

        $total_sql = 'SELECT count(*)
                      FROM ( ' . LOGS_TABLE . ' l
                      LEFT JOIN ' . USERS_TABLE . ' u
                      ON u.user_id=l.admin_id )';
        $total_logs = $db->query_first($total_sql . $addon_sql);

        $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

        $result = $db->query($sql . $addon_sql . ' ORDER BY ' . $current_order['sql'] . ' LIMIT '.$start.',100');
        while ( $log = $db->fetch_record($result) )
        {
            $log['log_type'] = lang_replace($log['log_type']);
            $log['log_result'] = lang_replace($log['log_result']);

            $tpl->assign_block_vars('logs_row', array(
                'ROW_CLASS'    => $eqdkp->switch_row_class(),
                'DATE'         => ( !empty($log['log_date']) ) ? date($user->style['date_time'], $log['log_date']) : '&nbsp;',
                'TYPE'         => ( !empty($log['log_type']) ) ? $log['log_type'] : '&nbsp;',
                'U_VIEW_LOG'   => 'logs.php?' . URI_LOG . '='.$log['log_id'],
                'USER'         => $log['username'],
                'IP'           => $log['log_ipaddress'],
                'RESULT'       => $log['log_result'],
                'C_RESULT'     => ( $log['log_result'] == $user->lang['success'] ) ? 'positive' : 'negative',
                'ENCODED_TYPE' => urlencode($log['log_type']),
                'ENCODED_USER' => urlencode($log['username']),
                'ENCODED_IP'   => urlencode($log['log_ipaddress']))
            );
        }

        $tpl->assign_vars(array(
            'S_LIST' => true,

            'L_DATE'        => $user->lang['date'],
            'L_TYPE'        => $user->lang['type'],
            'L_VIEW_ACTION' => $user->lang['view_action'],
            'L_USER'        => $user->lang['user'],
            'L_IP_ADDRESS'  => $user->lang['ip_address'],
            'L_RESULT'      => $user->lang['result'],
            'L_VIEW'        => $user->lang['view'],

            'O_DATE'        => $current_order['uri'][0],
            'O_TYPE'        => $current_order['uri'][1],
            'O_USER'        => $current_order['uri'][2],
            'O_IP'          => $current_order['uri'][3],
            'O_RESULT'      => $current_order['uri'][4],

            'U_LOGS'        => 'logs.php'.$SID.'&amp;search='.$search_term.'&amp;start='.$start.'&amp;',
            'U_LOGS_SEARCH' => 'logs.php'.$SID.'&amp;',

            'CURRENT_ORDER'       => $current_order['uri']['current'],
            'START'               => $start,
            'VIEWLOGS_FOOTCOUNT'  => sprintf($user->lang['viewlogs_footcount'], $total_logs, 100),
            'VIEWLOGS_PAGINATION' => generate_pagination('logs.php'.$SID.'&amp;search='.$search_term.'&amp;o='.$current_order['uri']['current'],
                                     $total_logs, '100', $start))
        );
        break;
}

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['viewlogs_title'],
    'template_file' => 'admin/logs.html',
    'display'       => true)
);

/**
* Returns the language value of a variable in the format "{L_<LANG_KEY>}"
* Allows use of template-style variables outside of templates
*
* @param $variable Variable to replace language keys for
* @return string Translated variable
*/
function lang_replace($variable)
{
    global $user;

    preg_match("/\{L_(.+)\}/", $variable, $to_replace);
    if ( (isset($to_replace[1])) && (isset($user->lang[strtolower($to_replace[1])])) )
    {
        $variable = str_replace('{L_'.$to_replace[1].'}', $user->lang[strtolower($to_replace[1])], $variable);
    }

    return $variable;
}
?>