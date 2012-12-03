<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listnews.php
 * Began: Fri December 27 2002
 * 
 * $Id: listnews.php 4 2006-05-08 17:01:47Z tsigo $
 * 
 ******************************/
 
define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_news_');

$sort_order = array(
    0 => array('news_date desc', 'news_date'),
    1 => array('news_headline', 'news_headline desc'),
    2 => array('username', 'username desc')
);

$current_order = switch_order($sort_order);

$total_news = $db->query_first('SELECT count(*) FROM ' . NEWS_TABLE);
$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, u.username
        FROM ' . NEWS_TABLE . ' n, ' . USERS_TABLE . ' u
        WHERE (n.user_id = u.user_id)
        ORDER BY '.$current_order['sql'].'
        LIMIT '.$start.',50';

if ( !($result = $db->query($sql)) )
{
    message_die('Could not obtain news information', '', __FILE__, __LINE__, $sql);
}
while ( $news = $db->fetch_record($result) )
{
    $tpl->assign_block_vars('news_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => date($user->style['date_time'], $news['news_date']),
        'USERNAME' => $news['username'],
        'U_VIEW_NEWS' => 'addnews.php'.$SID.'&amp;' . URI_NEWS . '='.$news['news_id'],
        'HEADLINE' => stripslashes($news['news_headline']))
    );
}

$tpl->assign_vars(array(
    'L_DATE' => $user->lang['date'],
    'L_USERNAME' => $user->lang['username'],
    'L_HEADLINE' => $user->lang['headline'],
    
    'O_DATE' => $current_order['uri'][0],
    'O_USERNAME' => $current_order['uri'][2],
    'O_HEADLINE' => $current_order['uri'][1],
    
    'U_LIST_NEWS' => 'listnews.php'.$SID,
    
    'START' => $start,
    'LISTNEWS_FOOTCOUNT' => sprintf($user->lang['listnews_footcount'], $total_news, 50),
    'NEWS_PAGINATION' => generate_pagination('listnews.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_news, 50, $start))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listnews_title'],
    'template_file' => 'admin/listnews.html',
    'display'       => true)
);
?>