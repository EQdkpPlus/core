<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * charmanager.php
 * Changed: Thu December 28, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_charmanager_manage');

if (!$pm->check(PLUGIN_INSTALLED, 'charmanager')) { message_die($user->$lang['uc_not_installed']); }
if ($user->data['username']=="") { message_die($user->lang['uc_not_loggedin']); }
$raidplan = $pm->get_plugin('charmanager');

// Build member drop-down
        $sql = 'SELECT m.member_id, m.member_name, mu.user_id
                FROM ' . MEMBERS_TABLE . ' m
                LEFT JOIN ' . MEMBER_USER_TABLE . ' mu
                ON m.member_id = mu.member_id
                WHERE mu.user_id IS NULL
                OR mu.user_id = '.$user->data['user_id'].'
                GROUP BY m.member_name
                ORDER BY m.member_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('member_row', array(
                'VALUE'    => $row['member_id'],
                'SELECTED' => ( (isset($row['user_id'])) && ($row['user_id'] == $user->data['user_id']) ) ? ' selected="selected"' : '',
                'OPTION'   => $row['member_name'])
            );
        }
        $db->free_result($result);

$tpl->assign_vars(array(
    'F_UPDATE'                  => "index.php?mode=connection",
    
    'L_INFO_BOX'                => $user->lang['uc_info_box'],
    'L_SUBMIT'                  => $user->lang['submit'],
    'L_RESET'                   => $user->lang['reset'],
    'L_MANAGE_CHARS'            => $user->lang['uc_managechar'],
    'L_MEMBER_CHARS'            => $user->lang['uc_charmanager'],
    'L_SELECT_CHAR'             => $user->lang['uc_select_char'],
    'L_ASSOCIATED_MEMBERS'      => $user->lang['associated_members'],
    'L_MEMBERS'                 => $user->lang['members'],
    'L_VERSION'                 => $pm->get_data('charmanager', 'version')
));


$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ts'],
	'template_file' => 'connected.html',
	'template_path' => $pm->get_data('charmanager', 'template_path'),
	'display'       => true)
);
?>