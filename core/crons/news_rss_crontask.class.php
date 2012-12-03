<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "news_rss_crontask" ) ) {
  class news_rss_crontask extends crontask{
		
	public function __construct(){
			$this->defaults['active'] = true;
			$this->defaults['repeat'] = true;
			$this->defaults['repeat_type'] = 'hourly';
			$this->defaults['description'] = 'Creating News RSS-Feed';
    }
		
		
    public function run(){
      global $core, $db, $UniversalFeedCreator, $pcache, $bbcode;
  	
  		$rss = new UniversalFeedCreator();
  	
  		$rss->title           = "Last News";
  		$rss->description     = $core->config['main_title']." EQdkp-Plus - Last News" ;
  		$rss->link            = $core->BuildLink();
  		$rss->syndicationURL  = $core->BuildLink().$_SERVER['PHP_SELF'];
  		$time = time();
  		
  		$previous_date = null;
  		$sql = 'SELECT n.*, u.username
  				FROM __news n, __users u
  				WHERE (n.user_id = u.user_id)
  				ORDER BY news_date DESC LIMIT 10';
  		$result = $db->query($sql);
  	
  		if ( $db->num_rows($result) == 0 ){
  			$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.news_flags, u.username
  				FROM __news n, __users u
  				WHERE (n.user_id = u.user_id)
  				ORDER BY news_date DESC LIMIT 10';
  			$result = $db->query($sql);
  			
			if ( $db->num_rows($result) == 0 ){
  				return;
  			}
  		}
  		$i = 0;
  		while ( $news = $db->fetch_record($result) ){
  		//Create RSS
  		if (($i < 10)){
  				$rssitem = new FeedItem();
  				$rssitem->title        = stripslashes(sanitize($news['news_headline'])) ;
  				$rssitem->link         = $core->BuildLink().'viewnews.php?id='.$news['news_id'];
  				$rssitem->description  = $bbcode->toHTML($news['news_message'], true);
  				$rssitem->date         = $news['news_date'] ;
  				$rssitem->source       = $rss->link;
  				$rssitem->author       = $news['username']  ;
  				$additionals = array(
  						'comments_counter'	=> $comcount,
  						'comments_active'		=> $SHOWCOMMENT,
  				);
  				$rssitem->additionalElements = $additionals;
  				$rss->addItem($rssitem);
  				$i++;
  		}
  	  }
  	$rss->saveFeed("RSS2.0", $pcache->FilePath('last_news.xml', 'eqdkp'),false);  	
    }	
  }
}
?>