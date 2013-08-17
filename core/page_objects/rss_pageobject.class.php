<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date: 2013-02-24 19:15:29 +0100 (So, 24 Feb 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13116 $
 * 
 * $Id: roster.php 13116 2013-02-24 18:15:29Z godmod $
 */

class rss_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core', 'html', 'bbcode' => 'bbcode');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array(
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}
	
	public function display(){
		$intCategoryID = $this->in->get('c', 0);
		$strExchangeKey = $this->in->get('key');
		$user_id = $this->user->getUserIDfromExchangeKey($strExchangeKey);
		
		//Get latest Articles for a specific category
		if ($intCategoryID){
			$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, $user_id, true));
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			
			switch($arrCategory['sortation_type']){
				case 4:
				case 3: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', 'desc');
				break;
				case 2:
				case 1:
				default: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'desc');
			}
		} else {
			//Get global latest articles
			$arrArticleCategoryIDs = $this->pdh->get('article_categories', 'id_list');
			$arrArticleIDs = array();
			foreach ($arrArticleCategoryIDs as $intCategoryID){
				$arrArticleIDs = array_merge($arrArticleIDs, $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, $user_id, true)));
			}
			$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'desc');
		}


		require_once($this->root_path.'core/feed.class.php');
		$feed				= registry::register('feed');
		$feed->feedfile		= $this->env->link.$this->strPathPlain.'?key='.$strExchangeKey;
		$feed->link			= $this->env->link;
		$feed->title		= $this->config->get('main_title').": ".$arrCategory['name'];
		$feed->description	= strip_tags(xhtml_entity_decode($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags($arrCategory['description']))));
		$feed->published	= time();
		$feed->language		= 'EN-EN';
		
		
		if (count($arrSortedArticleIDs)){
			$arrSortedArticleIDs = $this->pdh->limit($arrSortedArticleIDs, 0, 30);
			foreach($arrSortedArticleIDs as $intArticleID){
				$strText = $this->pdh->get('articles',  'text', array($intArticleID));
				$arrContent = preg_split('#<hr(.*)id="system-readmore"(.*)\/>#iU', xhtml_entity_decode($strText));
				
				$strText = $this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags($arrContent[0]));
				
				//Replace Image Gallery
				$arrGalleryObjects = array();
				preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strText, $arrGalleryObjects, PREG_PATTERN_ORDER);
				if (count($arrGalleryObjects[0])){
					include_once($this->root_path.'core/gallery.class.php');
					foreach($arrGalleryObjects[4] as $key=>$val){
						$strText = str_replace($arrGalleryObjects[0][$key], "", $strText);
					}
				}
				
				//Replace Raidloot
				$arrRaidlootObjects = array();
				preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
				if (count($arrRaidlootObjects[0])){
					include_once($this->root_path.'core/gallery.class.php');
					foreach($arrRaidlootObjects[3] as $key=>$val){
						$strText = str_replace($arrRaidlootObjects[0][$key], "", $strText);
					}
				}

				$rssitem = registry::register('feeditems', array($intArticleID));
				$rssitem->title			= sanitize($this->pdh->get('articles', 'title', array($intArticleID)));
				$rssitem->description	= $strText;
				$rssitem->link			= $this->user->removeSIDfromString($this->env->link.$this->pdh->get('articles',  'path', array($intArticleID)));
				$rssitem->published		= $this->pdh->get('articles', 'date', array($intArticleID));
				$rssitem->author		= $this->pdh->geth('articles', 'user_id', array($intArticleID));
				$rssitem->source		= $feed->link;
				$feed->addItem($rssitem);
			}	
		}
		header("Content-Type: application/xml; charset=utf-8");
		echo $feed->show();
		die();

	}
}
?>