<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class viewnews extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'time', 'pfh', 'env', 'bbcode'	=> 'bbcode', 'comments'	=> 'comments', 'social' => 'socialplugins');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_news_view');
		parent::__construct(false, $handler, array(), null, '', 'id');
		$this->process();
	}

	public function display(){
		$start = $this->in->get('start', 0);
		$news_array = array();
		if ($this->url_id > 0){			//Single News
			$news_array[$this->url_id] = $this->pdh->get('news', 'news', array($this->url_id));
			if(!$news_array[$this->url_id]) redirect('viewnews.php'.$this->SID);
		}elseif ($this->in->exists('c')){		//Category
			$news_array = $this->pdh->aget('news', 'news', 0, array($this->pdh->get('news', 'news_ids4cat', array($this->in->get('c')))));
			$total_news = count($news_array);
			$ignore_flags = true;
			$this->tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $this->SID.'&amp;c='.sanitize($this->in->get('c',0)), $total_news, $this->user->data['user_nlimit'], $start))
			);
		}elseif ($this->in->exists('m')){		//Show a Month
			$start_date = $this->time->mktime(0,0,0,$this->in->get('m'),0,$this->in->get('y'));
			$days = $this->time->date('t', $this->time->mktime(0,0,0,$this->in->get('m'),0,$this->in->get('y')));
			$end_date = $this->time->mktime(0,0,0,$this->in->get('m'),$days,$this->in->get('y'));
			$allnews = $this->pdh->aget('news', 'date', 0, array($this->pdh->get('news', 'id_list')));
			asort($allnews);
			foreach($allnews as $nid => $date) {
				if($date > $start_date AND $date < $end_date) {
					$news_array[$nid] = $this->pdh->get('news', 'news', array($nid));
				}
			}
			unset($allnews);
			$total_news = count($news_array);
			$ignore_flags = true;
			$this->tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $this->SID.'&amp;y='.sanitize($this->in->get('y',0)).'&amp;m='.sanitize($this->in->get('m',0)), $total_news, $this->user->data['user_nlimit'], $start))
			);
			$title = ' '.$this->time->date('B', $this->time->mktime(0,0,0,$this->in->get('m'),1,$this->in->get('y'))).' '.$this->in->get('y');
		} else {
			//Show the News - default view
			$allnews = $this->pdh->aget('news', 'news', 0, array($this->pdh->get('news', 'id_list')));
			foreach($allnews as $nid => $new) {
				if((!$new['news_start'] OR ($new['news_start'] AND $new['news_start'] < $this->time->time)) AND (!$new['news_stop'] OR ($new['news_stop'] AND $new['news_stop'] > $this->time->time))) {
					$news_array[$nid] = $new;
				}
			}
			unset($allnews);
			$total_news = count($news_array);
			$this->tpl->assign_vars(array(
				'NEWS_PAGINATION' => generate_pagination('viewnews.php' . $this->SID, $total_news, $this->user->data['user_nlimit'], $start))
			);
		}

		$cur_news_number = 0;
		$sticky_news = 0;
		$first_news = false;
		$no_news = true;

		if (is_array($news_array)){
			foreach($news_array as $news_id => $news) {
				if(!$this->pdh->get('news', 'has_permission', array($news_id))){
					continue;
				}

				if(!@$news['news_flags'] || isset($ignore_flags)){
					if(($cur_news_number < $start)){
						$cur_news_number++;
						continue;
					}else if($cur_news_number >= $start+$this->user->data['user_nlimit']){
						break;
					}else{
						$cur_news_number++;
					}
				}else{
					$news['news_headline'] = $this->user->lang('sticky_news_prefix').' '.$news['news_headline'];
					$sticky_news++;
				}
				$this->tpl->assign_block_vars('date_row', array(
					'DATE' => $this->time->user_date($news['news_date'], false, false, true))
				);
				if (!$first_news) $first_news = $news;
				$message = $news['news_message'];
				//Extended News
				if(($this->url_id) and (strlen($news['extended_message']) >1)){
					$message .= "<br/><br/>".$news['extended_message'];
				}else{ //listview
					if (strlen($news['extended_message'])>1){
						$message .= '<br /><a class="button news_rmlink" href="viewnews.php'.$this->SID.'&amp;id='.$news['news_id'].'">'.$this->user->lang('news_readmore').'</a></p>';
					}
				}

				$message = $this->bbcode->parse_shorttags(xhtml_entity_decode($message));
				$message .= $this->newsloot($news['showRaids_id']);

				$show_comment = $comments_counter = $COMMENT = false;
				$nocomments = (isset($news['nocomments'])) ? $news['nocomments'] : 0;
				if ($nocomments != 1){
					// get the count of comments per news:
					$this->comments->SetVars(array('attach_id'=>$news['news_id'], 'page'=>'news'));
					$comcount = $this->comments->Count();
					$comments_counter = ($comcount == 1 ) ? $comcount.' '.$this->user->lang('comment') : $comcount.' '.$this->user->lang('comments') ;
					if ($this->url_id){
						$COMMENT = $this->comments->Show();
					}
					$show_comment = true;
				}

				//News Categories
				$news_icon = '';
				$headline = '';
				if ($this->config->get('enable_newscategories') == 1 && $icon = $this->pdh->get('news_categories', 'icon', $news['news_category_id'])){
					$news_icon = '<img src="'.$this->pfh->FilePath('newscat_icons/'.sanitize($icon), 'eqdkp').'" class="absmiddle" alt="Category Icon" />&nbsp;';
				};
				if ($this->config->get('enable_newscategories') == 1 && $color = $this->pdh->get('news_categories', 'color', $news['news_category_id'])){
					$headline = '<span style="color:#'.$color.'">'.stripslashes($news['news_headline']).'</span>';
				} else {
					$headline = stripslashes($news['news_headline']);
				}
				$userlink = '<a href="'.$this->root_path.'listusers.php'.$this->SID.'&amp;u='.$news['user_id'].'">'.sanitize($news['username']).'</a>';
				$this->tpl->assign_block_vars('date_row.news_row', array(
					'HEADLINE'			=> $headline,
					'SOCIAL_BUTTONS'	=> $this->social->createSocialButtons($this->env->link.'viewnews.php?id='.$news_id, strip_tags($headline)),
					'URL_RAW'			=> rawurlencode($this->env->link.'viewnews.php?id='.$news_id),
					'SUBMITTED'			=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($news['news_date'], false, true)),
					'ID'				=> $news_id,
					'DETAIL'			=> ($this->url_id > 0 ) ? true : false, //newsid without _ correct here!
					'SHOWCOMMENT'		=> $show_comment,
					'COMMENTS_COUNTER'	=> $comments_counter,
					'COMMENT'			=> $COMMENT,
					'ICON'				=> $news_icon,
					'CATEGORY_LINK'		=> 'viewnews.php'.$this->SID.'&amp;c='.$news['news_category_id'],
					'CATEGORY_ID'		=> $news['news_category_id'],
					'CATEGORY_NAME'		=> $news['news_category'],
					'MESSAGE'			=> $message,
				));
				$no_news = false;
			}
		}
		$this->tpl->add_rssfeed($this->config->get('guildtag').' - News', 'last_news.xml', array('u_news_view'));

		$this->jquery->dialog('addNews', $this->user->lang('add_news'), array('url' => "admin/manage_news.php".$this->SID.'&simple_head=true&n=0', 'width' => 920, 'height' => 740, 'onclose'=> $this->env->link.'viewnews.php'.$this->SID));
		$this->jquery->dialog('editNews', $this->user->lang('manage_news'), array('url' => "admin/manage_news.php".$this->SID."&n='+id+'&simple_head=true", 'withid' => 'id', 'width' => 920, 'height' => 740, 'onclose'=> $this->env->link.'viewnews.php'.$this->SID));

		$this->tpl->assign_vars(array(
			'S_NEWSADM_ADD'					=> $this->user->check_auth('a_news_add', false),
			'S_NEWSADM_UPD'					=> $this->user->check_auth('a_news_upd', false),
			'S_NEWS_ARCHIVE_DISABLED'		=> ($this->config->get('pk_show_newsarchive') == 0) ? true : false,
			'S_NO_NEWS'						=> $no_news,
			'S_SOCIAL_BUTTONS'				=> ($this->config->get('enable_social_sharing') == 1) ? true : false,
			'S_NEWSDETAILS'					=> ($this->url_id > 0 ) ? true : false,
			'S_CATS_ENABLED'				=> $this->config->get('enable_newscategories'),
		));
		
		if($no_news) $news['extended_message'] = '';
		$this->core->set_vars(array(
			'page_title'		=> ($this->url_id) ? stripslashes($news['news_headline']) : '',
			'description'		=> strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($first_news['news_message'])))),
			'image'				=> $this->social->getFirstImage(xhtml_entity_decode($first_news['news_message'].$news['extended_message'])),
			'template_file'		=> 'viewnews.html',
			'display'			=> true)
		);
	}

	private function newsloot($showRaids_id) {
		$raid_ids = explode(",",$showRaids_id);
		$message = "";
		foreach($raid_ids as $raid_ID) {
			$loot = "" ;
			$raid_info = "";
			if($raid_ID) {
				$event_id = $this->pdh->get('raid', 'event', array($raid_ID));

				//Get Raid-Infos:
				$raid_info = $this->pdh->get('event', 'html_icon', array($event_id)).$this->pdh->get('raid', 'html_raidlink', array($raid_ID, 'viewraid.php', ''));
				$raid_info .= ' ('.$this->pdh->get('raid', 'html_note', array($raid_ID)).') &nbsp;' ;
				$raid_info .= $this->pdh->get('raid', 'html_date', array($raid_ID));

				//Get Items from the Raid
				$itemlist = $this->pdh->get('item', 'itemsofraid', array($raid_ID));

				//Shorten the array
				if ($this->config->get('pk_newsloot_limit') > 0){
					$itemlist = array_slice($itemlist, 0, $this->config->get('pk_newsloot_limit'), true);
				}

				infotooltip_js();
				foreach ($itemlist as $item) {
					$loot .= $this->pdh->get('item', 'link_itt', array($item, 'viewitem.php'));
					$buyer = $this->pdh->get('item', 'buyer', array($item));
					$loot .= ' &raquo; <a href="'.$this->root_path.'viewcharacter.php'.$this->SID.'&amp;member_id='.$buyer.'">';
					$loot .= $this->pdh->get('member', 'html_name', array($buyer))."</a> (".round($this->pdh->get('item', 'value', array($item)))." ".$this->config->get('dkp_name').") <br />";
				}
				if(strlen($loot) > 1){
					$message .= '<br /><hr />'.$raid_info.' '.$this->user->lang('loot').':<br /><br />'.$loot ;
				}
			}//end if
		}// forech
		return $message ;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_viewnews', viewnews::__shortcuts());
registry::register('viewnews');
?>