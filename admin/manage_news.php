<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listnews.php
 * Began: Fri December 27 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('a_news_');

class Manage_News extends EQdkp_Admin
{


    function manage_news()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        // Vars used to confirm deletion
        $confirm_text = $user->lang['confirm_delete_news'];
        $usernames = array();
        if ( isset($_POST['delete']) )
        {
            if ( isset($_POST['news_id']) )
            {
                foreach ( $_POST['news_id'] as $news_id )
                {
                    $headline = $db->query_first("SELECT news_headline FROM __news WHERE news_id='" . $news_id . "'");
                    $headlines[] = $headline;
										$ids[] = $news_id;
                }

                $lines = implode(', ', $headlines);
 								$news_ids = implode(', ', $ids);
                $confirm_text .= '<br /><br />' . $lines;
            }
            
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
						'uri_parameter' => 'news_ids',
            'url_id'        => ( sizeof($headlines) > 0 ) ? $news_ids : '',
            'script_name'   => 'manage_news.php' . $SID)
        );

        $this->assoc_buttons(array(

            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_news_del'),
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_news_'))
        );


    }


		function process_confirm(){
			global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
      global $SID, $user_id, $jquery;
			if ( isset($_POST['news_ids']) )
        {
            $news_ids = explode(', ', $_POST['news_ids']);
            $sql = 'DELETE FROM __news
                    WHERE news_id IN (' . implode(', ', $news_ids) . ')';
            $db->query($sql);
				}
			$this->display_list();
		}

		function display_list(){		
			 global $db, $core, $user, $tpl, $pm, $in, $pdh, $acl;
       global $SID, $user_id, $jquery;
			 
			 $order = $in->get('o', '0.0');
			 $red = 'RED'.str_replace('.', '', $order);

				$sort_order = array(
						0 => array('news_date desc', 'news_date'),
						1 => array('news_headline', 'news_headline desc'),
						2 => array('user_id', 'user_id desc'),
						3 => array('news_category desc', 'news_category'),
						4 => array('news_start desc', 'news_start'),
						5 => array('news_stop desc', 'news_stop'),
				);
				
				$current_order = switch_order($sort_order);
				
				$total_news = $db->query_first('SELECT count(*) FROM __news');
				$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
				
				$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.news_category, n.user_id, nc.category_name, n.news_date, n.news_start, n.news_stop
								FROM __news n, __news_categories nc
								WHERE (n.news_category = nc.category_id)
								ORDER BY '.$current_order['sql'].'
								LIMIT '.$start.',50';
				
				if ( !($result = $db->query($sql)) )
				{
						message_die('Could not obtain news information', '', __FILE__, __LINE__, $sql);
				}
				while ( $news = $db->fetch_record($result) )
				{
						$tpl->assign_block_vars('news_row', array(
								'ROW_CLASS' 	=> $core->switch_row_class(),
								'DATE' 				=> date($user->style['date_time'], $news['news_date']),
								'USERNAME' 		=> $pdh->get('user', 'name', array($news['user_id'])),
								'ID'					=> $news['news_id'],
								'U_VIEW_NEWS' => 'addnews.php'.$SID.'&n='.$news['news_id'],
								'CATEGORY'	=> $news['category_name'],
								'START' => ($news['news_start']) ? date($user->style['date_time'], $news['news_start']) : '',
								'STOP' => ($news['news_stop']) ? date($user->style['date_time'], $news['news_stop']) : '',
								'HEADLINE' => stripslashes($news['news_headline']))
						);
				}
				
				$tpl->assign_vars(array(
						'L_DATE' => $user->lang['date'],
						'L_USERNAME' => $user->lang['username'],
						'L_HEADLINE' => $user->lang['headline'],
						'L_DELETE' => $user->lang['delete_selected'],
						 
						 $red 				=> '_red',
						'O_DATE' 			=> $current_order['uri'][0],
						'O_USERNAME' 	=> $current_order['uri'][2],
						'O_HEADLINE' 	=> $current_order['uri'][1],
						'O_CATEGORY' 	=> $current_order['uri'][3],
						'O_START' 		=> $current_order['uri'][4],
						'O_STOP' 			=> $current_order['uri'][5],
						
						'L_MANAGE_NEWS'	=> $user->lang['manage_news'],
						'L_CATEGORY'	=> $user->lang['pi_category'],
						'L_SORT_DESC'	=> $user->lang['sort_desc'],
						'L_SORT_ASC'	=> $user->lang['sort_asc'],
						'L_SHOW_START'	=> $user->lang['show_from'],
						'L_SHOW_STOP'	=> $user->lang['show_to'],
			
						'U_LIST_NEWS' => 'manage_news.php'.$SID,
						'L_ADD_NEWS'	=> $user->lang['add_news'],
						'L_MANAGE_NEWSCATEGORIES'	=> $user->lang['manage_newscategories'],
						'NEWS_CATEGEGORIES'	=> ($core->config['enable_newscategories'] == 1) ? true : false,
										
						'START' => $start,
						'LISTNEWS_FOOTCOUNT' => sprintf($user->lang['listnews_footcount'], $total_news, 50),
						'NEWS_PAGINATION' => generate_pagination('manage_news.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_news, 50, $start))
				);
				
				$core->set_vars(array(
						'page_title'    => $user->lang['listnews_title'],
						'template_file' => 'admin/manage_news.html',
						'display'       => true)
				);
		}

}
$manage_news = new Manage_News;
$manage_news->process();
?>