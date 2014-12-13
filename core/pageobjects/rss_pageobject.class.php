<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class rss_pageobject extends pageobject {

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
				preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
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