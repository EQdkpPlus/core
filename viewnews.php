<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2003
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

	define('EQDKP_INC', true);
	define('USE_LIGHTBOX', true);
	$eqdkp_root_path = './';
	include_once($eqdkp_root_path . 'common.php');

	//Select the years and months for the archive
	if ($core->config['pk_show_newsarchive'] == 1) {
		$year_query = $db->query("SELECT news_id, news_date, news_category FROM __news ORDER BY news_date DESC");
		$date_array = array();
		while($row = $db->fetch_record($year_query)){
			$date_array[$time->date('Y', $row['news_date'])][$time->date('m', $row['news_date'])][] = $row['news_id'];
			$cat_array[$row['news_category']][] = $row['news_id'];
		}
		//The Side thing for the Months
		foreach($date_array as $year=>$month){
			
			$tpl->assign_block_vars('year_row', array(
				'YEAR'	=> $year,
			));
		
			foreach($month as $key=>$value){
				$month_dropdown['y='.$year.'&m='.$key] = $year.': '.$time->date('F', $time->mktime(0,0,0,$key,1,$year)).' ('.count($value).')';
				$tpl->assign_block_vars('year_row.month_row', array(
					'MONTH'	=> $time->date('F', $time->mktime(0,0,0,$key,1,$year)),
					'COUNT' => count($value),
					'ROW_CLASS'	=> $core->switch_row_class(),
					'U_MONTH_VIEW'	=> 'viewnews.php'.$SID.'&y='.$year.'&m='.$key,
				));
			}
		}
		if ($core->config['enable_newscategories']){
			//The News-Categorys
			$cat_query = $db->query("SELECT * FROM __news_categories");
			while ($row = $db->fetch_record($cat_query)){
				$newscategories[$row['category_id']] = $row;
				$cat_dropdown[$row['category_id']] = sanitize($row[category_name]);
				$tpl->assign_block_vars('cat_row', array(
					'CAT'	=> sanitize($row[category_name]),
					'U_CAT_VIEW'	=> 'viewnews.php'.$SID.'&c='.$row['category_id'],
					'COUNT'			=> count($cat_array[$row['category_id']]),
					'ROW_CLASS'		=> $core->switch_row_class(),
				));
				
			}
		}
		$tpl->assign_vars(array(
			'S_NEWSARCHIVE_'.strtoupper($core->config['pk_newsarchive_position'])	=> true,
			'L_CATEGORIES'	=> $user->lang['categories'],
			'L_SEARCH'		=> $user->lang['search'],
			'L_MONTHS'		=> $user->lang['select_month'],
			'S_CATS_ENABLED'	=> $core->config['enable_newscategories'],
			'MONTH_DD'		=> $html->DropDown('months', $month_dropdown, 'y='.$in->get('y').'&m='.$in->get('m'), false, 'onChange="window.location=\'viewnews.php'.$SID.'&\'+this.value"'),
			'CATEGORIES_DD'		=> $html->DropDown('months', $cat_dropdown, $in->get('c'), false, 'onChange="window.location=\'viewnews.php'.$SID.'&c=\'+this.value"'),
		));
	} //close if newsarchive is enabled
	
	
	$start = $in->get('start', 0);
	$newsid = $in->get('id', 0);
	
	if ($newsid >  0){
	//Single News
		$sql = 'SELECT n.*, u.username, c.*
								FROM __news n, __users u, __news_categories c
								WHERE n.user_id = u.user_id
								AND (n.news_category = c.category_id)
								AND news_id='.$db->escape($newsid);
		
		$result = $db->query($sql);
	
	
	} else if ($in->get('c') != ""){
	//Category

		$sql = 'SELECT n.*, u.username, c.*
								FROM __news n, __users u, __news_categories c
								WHERE n.user_id = u.user_id
								AND (n.news_category = c.category_id)
								AND n.news_category ='.$db->escape($in->get('c').' ORDER BY n.news_date DESC');
		
		$result = $db->query($sql);
		
		$total_news = $db->num_rows($result);
		$ignore_flags = true;
		
		$tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID.'&c='.sanitize($in->get('c',0)), $total_news, $user->data['user_nlimit'], $start))
		);
	
	
	} else if ($in->get('m') != ""){
	//Show a Month
		$start_date = $time->mktime(0,0,0,$in->get('m'),0,$in->get('y'));
		$days = $time->date('t', $time->mktime(0,0,0,$in->get('m'),0,$in->get('y')));
		$end_date = $time->mktime(0,0,0,$in->get('m'),$days,$in->get('y'));
			
		$sql = 'SELECT n.*, u.username, c.*
								FROM __news n, __users u, __news_categories c
								WHERE n.user_id = u.user_id
								AND (n.news_category = c.category_id)
								AND n.news_date > '.$start_date.' AND n.news_date < '.$end_date.' ORDER BY n.news_date DESC';
		
		$result = $db->query($sql);
		
		$total_news = $db->num_rows($result);
		$ignore_flags = true;
		
		$tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID.'&y='.sanitize($in->get('y',0)).'&m='.sanitize($in->get('m',0)), $total_news, $user->data['user_nlimit'], $start))
		);
		
		$title = ' '.$time->date('B', $time->mktime(0,0,0,$in->get('m'),1,$in->get('y'))).' '.$in->get('y');
	
	} else if ($in->get('sv') != ""){
	//Search
	
		$sql = 'SELECT n.*, u.username, c.*
								FROM __news n, __users u, __news_categories c
								WHERE n.user_id = u.user_id
	
								AND (n.news_category = c.category_id)
								AND (n.news_headline LIKE "%'.$db->escape($in->get('sv')).'%" OR n.news_message LIKE "%'.$db->escape($in->get('sv')).'%" OR n.extended_message LIKE "%'.$db->escape($in->get('sv')).'%") ORDER BY n.news_date DESC';
		
		$result = $db->query($sql);
		$total_news = $db->num_rows($result);
		$ignore_flags = true;
		
		$tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID.'&sv='.urldecode(sanitize($in->get('sv'))), $total_news, $user->data['user_nlimit'], $start))
		);
		$title = ' '.$user->lang['search'].': '.sanitize($in->get('sv'));
	} else {
	//Show the News - default view
	
		$recent_time = time();		
	
		
		$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.news_category, n.extended_message, n.news_flags, n.showRaids_id, u.username, c.*
					FROM __news n, __users u, __news_categories c
					WHERE (n.user_id = u.user_id) AND (n.news_category = c.category_id)
					AND (n.news_start = "" OR n.news_start < '.$recent_time.') AND (n.news_stop = "" OR n.news_stop > '.$recent_time.')
					ORDER BY news_flags DESC, news_date DESC';
		$result = $db->query($sql);
		
		$total_news = $db->num_rows($result);
		
		$tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $SID, $total_news, $user->data['user_nlimit'], $start))
		);
	
	}

	if($db->num_rows($result) == 0){
		$tpl->assign_vars(array(
			'S_NO_NEWS'	=> true,												
		));
	}


  $cur_news_number = 0;
  $sticky_news = 0;
  
	
	while ( $news = $db->fetch_record($result) ){
		$shownews = true ;
		switch ($news['news_permissions']){
			case 0: $shownews = true ; break ;
			case 1: if ($user->data['user_id'] == ANONYMOUS ){$shownews = false ; } break ;
			case 2: if (!$user->check_auth('a_', false) ) {$shownews = false ; } break ;
		}
		if(!$shownews){
			continue;
		}

    if(!$news['news_flags'] || $ignore_flags){
      if(($cur_news_number < $start)){
        $cur_news_number++;
        continue;
      }else if($cur_news_number >= $start+$user->data['user_nlimit']){
        break;
      }else{
        $cur_news_number++;
      }
    }else{
      $news['news_headline'] = $user->lang['sticky_news_prefix'].' '.$news['news_headline'];
      $sticky_news++;
    }

		$tpl->assign_block_vars('date_row', array(
				'DATE' => $time->date($user->style['date'],$news['news_date']))
			);
		$message = $news['news_message'];

		//Details views
		if(($newsid) and (strlen($news['extended_message'])>1)){
			$message .= "[br][br]".$news['extended_message'];
		}else{ //listview
			if (strlen($news['extended_message'])>1){
				$message .= '[right][b][url=viewnews.php?id='.$news['news_id'].']'.$user->lang['news_readmore']."[/url][/b][/right]";
			}
		}

		// Security: Do not allow html Tags and javascript
		$message = sanitize($message);
		
		
		// Perform BB Code
		$message = $bbcode->toHTML($message);

		// Emotion Code
		$bbcode->SetSmiliePath($eqdkp_root_path.'libraries/jquery/core/images/editor/icons');

		// Simple BB-Codes replacements
		$patterns = $replacements = array();
		$count = sizeof($patterns);
		if ( @is_object($pm) ){
			$plugin_news = $pm->do_hooks('news_parse');
			if(!empty($plugin_news)){
				foreach ( $plugin_news as $news_array ){
					if(!empty($news_array) && is_array($news_array)){
						foreach ( $news_array as $find_replace ){
							if ( (isset($find_replace['patterns'])) && (isset($find_replace['replacements'])) ){
								$count++;
								$patterns[$count]			= $find_replace['patterns'];
								$replacements[$count] = $find_replace['replacements'];
							}
						}
					}
				}
			}
		}
		$message = preg_replace($patterns, $replacements, $message);
		$message = $bbcode->MyEmoticons($message);

		//Do Hooks - Heavy BB-Code Replacements
		if (is_array($pm->hooks['news_bbcodes'])){
			foreach ($pm->hooks['news_bbcodes'] as $plugin_code => $function){
				$message = $pm->do_hook('news_bbcodes', $plugin_code, array('text'	=> $message));
			}
		}
				
		//Newsloot
		$message .= newsloot($news['showRaids_id']);

		$SHOWCOMMENT = false;
		if (!$news['nocomments']==1){
			// get the count of comments per news:
			$comm_settings = array('attach_id'=>$news['news_id'], 'page'=>'news');
			$pcomments->SetVars($comm_settings);
			$comcount = $pcomments->Count();
			$COMMENTS_COUNTER = ($comcount == 1 ) ? $comcount.' '.$user->lang['news_comment'] : $comcount.' '.$user->lang['news_comments'] ;
			if ($newsid){
				$COMMENT = $pcomments->Show();
			}
			$SHOWCOMMENT = true;
		}
		
		//News Categories
		$news_icon = ''; 
		$headline = '';
		if ($news['category_icon'] != "" && $core->config['enable_newscategories'] == 1){
			$news_icon = '<img src="'.$pcache->FilePath('newscat_icons/'.sanitize($news['category_icon']), 'eqdkp').'">';
		};
		if ($news['category_color'] != "" && $core->config['enable_newscategories'] == 1){
			$headline = '<span style="color:'.$news['category_color'].'">'.stripslashes($news['news_headline']).'</span>';
		} else {
			$headline = stripslashes($news['news_headline']);
		};
		$category = ($core->config['enable_newscategories'] == 1) ? ' | <a href="viewnews.php'.$SID.'&c='.$news['news_category'].'">'.$news['category_name'].'</a>' : '';
		$tpl->assign_block_vars('date_row.news_row', array(
	        'ROW_CLASS'					=> $core->switch_row_class(),
	        'HEADLINE'					=> $headline,
	        'SUBMITTED'					=> sprintf($user->lang['news_submitter'], sanitize($news['username']), $time->date($user->style['time'], $news['news_date'])),
	        'ID'						=> $news['news_id'],
	        'DETAIL'					=> ($newsid > 0 ) ? true : false,
	        'SHOWCOMMENT'				=> $SHOWCOMMENT,
	        'COMMENTS_COUNTER'			=> $COMMENTS_COUNTER,
	        'COMMENT'					=> $COMMENT,
			'ICON'						=> $news_icon,
			'CATEGORY'					=> $category,
	        'MESSAGE'					=> $message)
	    );
	}#end news while
	$db->free_result($result);
  
	$tpl->add_rssfeed($core->config['guildtag'].' - News', $core->BuildLink().$pcache->FileLink('last_news.xml', 'eqdkp'));
	
	// Windows
	$jquery->dialog('addNews', $user->lang['add_news'], array('url' => "admin/addnews.php".$SID.'&ref=fe', 'width' => 920, 'height' => 700));
	$jquery->dialog('editNews', $user->lang['manage_news'], array('url' => "admin/addnews.php".$SID."&n='+id+'&ref=fe", 'withid' => 'id', 'width' => 920, 'height' => 700));
	
 	$tpl->assign_vars(array(
		
		'S_NEWSADM_ADD'								=> $user->check_auth('a_news_add', false),
		'S_NEWSADM_UPD'								=> $user->check_auth('a_news_upd', false),
		'L_MANAGE_NEWS'								=> $user->lang['edit_news'],
		'L_ADD_NEWS'								=> $user->lang['add_news'],
		'L_NEWS_ARCHIVE'							=> $user->lang['newsarchive_title'],
		'S_NEWS_ARCHIVE_DISABLED'					=> ($core->config['pk_enable_newsarchive'] == 0) ? true : false,
		'L_NO_NEWS'									=> $user->lang['no_news'],
 		));

	$core->set_vars(array(
		'page_title'    => '',
		'template_file' => 'viewnews.html',
		'display'       => true)
	);

	/**
	 * GodMods Newsloot
	 *
	 * @param integer $showRaids_id
	 * @return String
	 */
	 	function newsloot($showRaids_id)
	{
		global $conf_plus,$db,$user,$eqdkp_root_path, $pdh, $game, $core;
		$raid_ids = explode(",",$showRaids_id);
		$message = "";

		foreach($raid_ids as $raid_ID)
		{
			$loot = "" ;
			$raid_info = "";

			if($raid_ID)
			{
		    	
					$event_id = $pdh->get('raid', 'event', array($raid_ID));
					//Get Raid-Infos:
					$raid_info = $pdh->get('event', 'html_icon', array($event_id)).$pdh->get('raid', 'html_raidlink', array($raid_ID, 'viewraid.php', ''));
					$raid_info .= ' ('.$pdh->get('raid', 'html_note', array($raid_ID)).') &nbsp;' ;
					$raid_info .= $pdh->get('raid', 'html_date', array($raid_ID));

					//Get Items from the Raid
					$itemlist = $pdh->get('item', 'itemsofraid', array($raid_ID));

					//Shorten the array
					if ($core->config['pk_newsloot_limit'] != 'all'){
						$itemlist = array_slice($itemlist, 0, $core->config['pk_newsloot_limit'], true);
					}
					infotooltip_js();
					foreach  ($itemlist as $item) {
					
							$loot .=  $pdh->get('item', 'link_itt', array($item, 'viewitem.php'));
							$buyer = $pdh->get('item', 'buyer', array($item));
							$loot .= ' &raquo; <a href="'.$eqdkp_root_path.'viewcharacter.php?member_id='.$buyer.'">'.$pdh->get('member', 'html_name', array($buyer))."</a> (".round($pdh->get('item', 'value', array($item)))." ".$core->config['dkp_name'].") <br>";
					}	
						
					if(strlen($loot) > 1)
					{
						$message .='<br><hr noshade>'.$raid_info.' Loot:<br><br>'.$loot ;
					}

			}//end if
	 	}// forech
		return $message ;
	}

?>