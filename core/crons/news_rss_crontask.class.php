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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "news_rss_crontask" ) ) {
	class news_rss_crontask extends crontask {
		public static $shortcuts = array('config', 'pfh', 'db', 'bbcode'=>'bbcode', 'env' => 'environment');

		public function __construct(){
			$this->defaults['active']		= true;
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'hourly';
			$this->defaults['description']	= 'Creating News RSS-Feed';
			$this->defaults['ajax']	 = false;
		}

		public function run(){

			require_once($this->root_path.'core/feed.class.php');
			$this->pfh->secure_folder('rss', 'eqdkp');
			$rssfile			= $this->pfh->FilePath('rss/last_news.xml', 'eqdkp', 'relative');
			$feed				= registry::register('feed');
			$feed->feedfile		= $this->pfh->FileLink('rss/last_news.xml', 'eqdkp', 'absolute');
			$feed->link			= $this->env->link;
			$feed->title		= "Last News";
			$feed->description	= $this->config->get('main_title')." EQdkp-Plus - Last News";
			$feed->published	= time();
			$feed->language		= 'EN-EN';

			$previous_date = null;
			$sql = 'SELECT n.*, u.username
					FROM __news n, __users u
					WHERE (n.user_id = u.user_id)
					ORDER BY news_date DESC LIMIT 10';
			$result = $this->db->query($sql);

			if ( $this->db->num_rows($result) == 0 ){
				$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.news_flags, u.username
						FROM __news n, __users u
						WHERE (n.user_id = u.user_id)
						ORDER BY news_date DESC LIMIT 10';
				$result = $this->db->query($sql);

				if ( $this->db->num_rows($result) == 0 ){
					return;
				}
			}
			$i = 0;
			while ( $news = $this->db->fetch_record($result) ){
				//Create RSS
				if(($i < 10)){
					$rssitem = registry::register('feeditems', array($i));
					$rssitem->title			= stripslashes(sanitize($news['news_headline']));
					$rssitem->description	= $this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(html_entity_decode($news['news_message'])));
					$rssitem->link			= $this->env->link.'viewnews.php?id='.$news['news_id'];
					$rssitem->published		= $news['news_date'];
					$rssitem->author		= $news['username'];
					$rssitem->source		= $feed->link;
					$feed->addItem($rssitem);
					$i++;
				}
			}
			$feed->save($rssfile);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_news_rss_crontask', news_rss_crontask::$shortcuts);
?>