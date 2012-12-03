<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewnews.php
 * Began: Sat April 5 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

if ($conf_plus['pk_itemstats'] == 1){
	include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
}

	function preg_match_between($start, $end, $string){
	     $pattern='/'. $start .'(.*?)'. $end .'/';
	     preg_match_all($pattern, $string, $result);
	     return $result;
	}

	$total_news = $db->query_first('SELECT count(*) FROM ' . NEWS_TABLE);
	$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

	$newsid = intval(mysql_escape_string($_GET['id']));
	if ($newsid)
	{
		$sql = 'SELECT *
		        FROM ' . NEWS_TABLE . '
		        WHERE news_id='.$newsid;

		$result = $db->query($sql);
	}else
	{
		$previous_date = null;
		$sql = 'SELECT n.*, u.username
		        FROM ' . NEWS_TABLE . ' n, ' . USERS_TABLE . ' u
		        WHERE (n.user_id = u.user_id)
		        ORDER BY news_date DESC
		        LIMIT '.$start.','.$user->data['user_nlimit'];
		$result = $db->query($sql);

		if ( $db->num_rows($result) == 0 )
		{

			$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, u.username
		        FROM ' . NEWS_TABLE . ' n, ' . USERS_TABLE . ' u
		        WHERE (n.user_id = u.user_id)
		        ORDER BY news_date DESC
		        LIMIT '.$start.','.$user->data['user_nlimit'];
			$result = $db->query($sql);
			if ( $db->num_rows($result) == 0 )
			{
			    message_die($user->lang['no_news']);
			 }

		}
	}

	$cur_hash = hash_filename("viewnews.php");

	while ( $news = $db->fetch_record($result) )
	{

		// Show a new date row if it's not the same as the last
	    if ( date($user->style['date_notime_long'], $news['news_date']) != date($user->style['date_notime_long'], $previous_date) )
	    {

	        $tpl->assign_block_vars('date_row', array(
	            'DATE' => strftime($user->style['strtime_date'],$news['news_date']))
	        );

	        $previous_date = $news['news_date'];
	    }

	    $message = $news['news_message'];

	    //Details views
	    if ($newsid)
	    {
			$message .= $news['extended_message'];
	    }else
	    { //listview
	    	if (strlen($news['extended_message'])>1)
	    	{
	    		$message .= '<span style="float:right"> <a href="viewnews.php?id='.$news['news_id'].'">'.$user->lang['news_readmore']."</a></span>";
	    	}
	    }

	    $message = $html->EmbeddedVideo($message);
	    $message = nl2br($message);
	    news_parse($message);

	    //Newsloot
	    $message .= $html->newsloot($news['showRaids_id']);

		if ($conf_plus['pk_itemstats'] == 1){
			$message = itemstats_parse($message);
		}


		$SHOWCOMMENT = false;
  	   if (!$news['nocomments']==1)
  	   {
			// get the count of comments per news:
		    $comcount = $comments->Count($news['news_id'],'news');
  	   		$COMMENTS_COUNTER = ($comcount == 1 ) ? $comcount.' '.$user->lang['news_comment'] : $comcount.' '.$user->lang['news_comments'] ;
  	   		$COMMENT = $comments->Show($news['news_id'],'news') ;
  	   		$SHOWCOMMENT = true;
  	   }


		$shownews = true ;
		switch ($news['news_permissions'])
		{
			case 0: $shownews = true ; break ;
			case 1: if ($user->data['user_id'] == ANONYMOUS ){$shownews = false ; } break ;
			case 2: if (!$user->check_auth('a_', false) ) {$shownews = false ; } break ;
		}

		if ($shownews)
		{
		    $tpl->assign_block_vars('date_row.news_row', array(
		        'ROW_CLASS' => $eqdkp->switch_row_class(),
		        'HEADLINE' => stripslashes($news['news_headline']),
		        'AUTHOR' => $news['username'],
		        'TIME' => date($user->style['time'], $news['news_date']),
		        'SUBMITTER' => $user->lang['news_submitter'] ,
		        'SUBMITAT' => $user->lang['news_submitat'] ,
		        'ID' => $news['news_id'],
		        'DETAIL' => ($newsid > 0 ) ? true : false,
		        'SHOWCOMMENT' => $SHOWCOMMENT,
		        'COMMENTS_COUNTER' => $COMMENTS_COUNTER,
		        'COMMENT'  => $COMMENT,
		        'MESSAGE' => $message)
		    );
	    }


	}
	$db->free_result($result);

	if (!$newsid)
	{
		$tpl->assign_vars(array(
	    'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID, $total_news, $user->data['user_nlimit'], $start))
		);
	}


$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']),
    'template_file' => 'viewnews.html',
    'display'       => true)
);
?>
