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

$previous_date = null;
$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.showRaids_id , u.username
        FROM ' . NEWS_TABLE . ' n, ' . USERS_TABLE . ' u
        WHERE (n.user_id = u.user_id)
        ORDER BY news_date DESC
        LIMIT '.$start.','.$user->data['user_nlimit'];
$result = $db->query($sql);

if ( $db->num_rows($result) == 0 )
{
#    message_die($user->lang['no_news']);

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

$cur_hash = hash_filename("viewnews.php");
// print"HASH::$cur_hash::<br>";

while ( $news = $db->fetch_record($result) )
{
    // Show a new date row if it's not the same as the last
    if ( date($user->style['date_notime_long'], $news['news_date']) != date($user->style['date_notime_long'], $previous_date) )
    {
        $tpl->assign_block_vars('date_row', array(
            'DATE' => date($user->style['date_notime_long'], $news['news_date']))
        );

        $previous_date = $news['news_date'];
    }


    $message = $news['news_message'];
    $message = $html->EmbeddedVideo($message);
    $message = nl2br($message);
    news_parse($message);

	#Corgan Newsloot start
	########################

	if(isset($news['showRaids_id']))
	{
		$raid_ids = explode(",",$news['showRaids_id']);

		foreach($raid_ids as $raid_ID)
		{
			$loot = "" ;
			$raid_info = "";

		/*Gets the looted items */
		#############################################
		if($raid_ID){
	    $sql2 = 'SELECT item_id, item_buyer, item_name, item_date, item_value
		         FROM ' . ITEMS_TABLE . "
	             WHERE raid_id = " . $raid_ID."
	             ORDER BY item_value DESC
            	 ";

      if ($conf_plus['pk_newsloot_limit'] > 0)
      {
      	$sql2 .= ' limit '. $conf_plus['pk_newsloot_limit'] ;
      }

		if (($results = $db->query($sql2)) )
		{
			#!message_die('Could not obtain item information', '', __FILE__, __LINE__, $sql2);
			while ( $item = $db->fetch_record($results) )
			{
				$loot .= '<a href=viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.$item['item_id'].'>' ;
				if ($conf_plus['pk_itemstats'] == 1){
					$loot .= $html->itemstats_item(stripslashes($item['item_name'])).'</a> -> ';
				}else{
					$loot .= stripslashes($item['item_name']).'</a> -> ';
				}
				$loot .= get_coloredLinkedName($item['item_buyer']) . ' ('.round($item['item_value']).' DKP)<br>';
				#$loot .= '<a href=viewmember.php'.$SID.'&amp;' . URI_NAME . '='.$item['item_buyer'].'>'. $item['item_buyer'].' </a> ('.round($item['item_value']).' DKP)<br>';
			}


			$db->free_result($results);

			//Raidname
			$sql2 = 'SELECT raid_name, raid_date, raid_note
							 FROM ' . RAIDS_TABLE . "
							 WHERE raid_id = " . $raid_ID."
							 ";

			if ( ($results = $db->query($sql2)) )
			{
				#!message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql2);
				$raid = $db->fetch_record($results);


				$event_icon = getEventIcon($raid['raid_name']);

				$raid_info .= $event_icon.'<a href=viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$raid_ID.'>'.$raid['raid_name'].'</a> &nbsp;' ;
				$raid_info .= '('.$raid['raid_note'].') &nbsp;' ;
				$raid_info .= ( !empty($raid['raid_date']) ) ? date($user->style['date_notime_short'], $raid['raid_date']) : '&nbsp;' ;
				if(strlen($loot) > 1)
				{
					$message .='<br><hr noshade>'.$raid_info.' Loot:<br><br>'.$loot ;
				}
			}
			$db->free_result($results);
		}
	}//end if
	 }	// forech
	}
	#Corgan Newsloot end
	########################

	if ($conf_plus['pk_itemstats'] == 1){
		$message = itemstats_parse($message);
	}

    $tpl->assign_block_vars('date_row.news_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'HEADLINE' => stripslashes($news['news_headline']),
        'AUTHOR' => $news['username'],
        'TIME' => date($user->style['time'], $news['news_date']),
        'SUBMITTER' => $user->lang['news_submitter'] ,
        'SUBMITAT' => $user->lang['news_submitat'] ,
        'MESSAGE' => $message)
    );
}
$db->free_result($result);


$tpl->assign_vars(array(
    'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID, $total_news, $user->data['user_nlimit'], $start))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']),
    'template_file' => 'viewnews.html',
    'display'       => true)
);
?>
