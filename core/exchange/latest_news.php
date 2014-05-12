<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_latest_news')){
	class exchange_latest_news extends gen_class {
		public static $shortcuts = array('user', 'pdh', 'time', 'bbcode'=>'bbcode', 'pex'=>'plus_exchange');
		public $options		= array();

		public function get_latest_news($params, $body){

			if ($this->user->check_auth('u_news_view', false)){
				
				//Get Number; default: 10
				$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : 10;
				//Get sort direction; default: desc
				$sort = (isset($params['get']['sort']) && $params['get']['sort'] == 'asc') ? 'asc' : 'desc';
				
				$arrNews = $this->pdh->aget('news', 'news', 0, array($this->pdh->sort($this->pdh->get('news', 'id_list', array()), 'news', 'date', $sort)));
				if (is_array($arrNews) && count($arrNews) > 0){
					$arrNews = array_slice($arrNews, 0, $intNumber);
					foreach($arrNews as $id => $news){
						if(!$this->pdh->get('news', 'has_permission', array($news['news_id']))){
							continue;
						}
						
						if(!((!$news['news_start'] OR ($news['news_start'] AND $news['news_start'] < $this->time->time)) AND (!$news['news_stop'] OR ($news['news_stop'] AND $news['news_stop'] > $this->time->time)))) {
							continue;
						}
					
						$message = $news['news_message'];
						if (strlen($news['extended_message']) >1 ){
							$message .= '<br /><br />'.$news['extended_message'];
						}
						
						$message = $this->bbcode->remove_shorttags(html_entity_decode($message), true);
						$message = $this->bbcode->remove_embeddedMedia($message);
						
						$comments = $this->pdh->get('comment', 'filtered_list', array('news', $news['news_id']));
						$arrComments = array();
						if (is_array($comments)){
							foreach($comments as $key => $row){
								$avatarimg = $this->pdh->get('user', 'avatarimglink', array($row['userid']));
								
								$arrComments['comment:'.$key] = array(
									'username'			=> $row['username'],
									'user_avatar'		=> (($avatarimg) ? $avatarimg : 'images/no_pic.png'),
									'date'				=> $this->time->date('Y-m-d H:i', $row['date']),
									'date_timestamp'	=> $row['date'],
									'message'			=> $this->bbcode->toHTML($row['text']),
								);
							}
						}
						
						$arrCommentsOut = array(
							'count' 	=> count($arrComments),
							'page'		=> 'news',
							'attachid' => $news['news_id'],
							'comments' => $arrComments,
						);
						
						$response['entries']['entry:'.$id] = array(
						  'id'			=> $news['news_id'],
						  'headline'	=> unsanitize($news['news_headline']),
						  'message'		=> $message,
						  'date'		=> $this->time->date('Y-m-d H:i', $news['news_date']),
						  'date_timestamp'=> $news['news_date'],
						  'author'		=> $news['username'],
						  'category_id'	=> $news['news_category_id'],
						  'category'	=> $news['news_category'],
						  'comments'	=> $arrCommentsOut,
						);
					}
				}

				return $response;
			} else {
				return $this->pex->error('access denied');
			}

		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_exchange_latest_news', exchange_latest_news::$shortcuts);
?>