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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_latest_articles')){
	class exchange_latest_articles extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_latest_articles($params, $body){
				//Get Number; default: 10
				$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : 10;
				//Get sort direction; default: desc
				$sort = (isset($params['get']['sort']) && $params['get']['sort'] == 'asc') ? 'asc' : 'desc';
				
				$intCategoryID = (isset($params['get']['c'])) ? intval($params['get']['c']) : 0;
				
				$user_id = $this->user->id;
				
				$response = array();
								
				//Get latest Articles for a specific category
				if ($intCategoryID){
					$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, $user_id, true));
					$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
						
					switch($arrCategory['sortation_type']){
						case 4:
						case 3: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', $sort);
						break;
						case 2:
						case 1:
						default: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', $sort);
					}
				} else {
					//Get global latest articles
					$arrArticleCategoryIDs = $this->pdh->get('article_categories', 'id_list');
					$arrArticleIDs = array();
					foreach ($arrArticleCategoryIDs as $intCategoryID){
						$arrArticleIDs = array_merge($arrArticleIDs, $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, $user_id, true)));
					}
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', $sort);
				}
				
				if (count($arrSortedArticleIDs)){
					$arrSortedArticleIDs = $this->pdh->limit($arrSortedArticleIDs, 0, $intNumber);
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
						
						$category_id = $this->pdh->get('articles', 'category', array($intArticleID));
						
						
						$comments = $this->pdh->get('comment', 'filtered_list', array('articles', $intArticleID));
						$arrComments = array();
						if (is_array($comments)){
							foreach($comments as $key => $row){
								$avatarimg = $this->pdh->get('user', 'avatarimglink', array($row['userid']));
						
								$arrComments['comment:'.$key] = array(
										'username'			=> unsanitize($row['username']),
										'user_avatar'		=> $this->pfh->FileLink((($avatarimg != "") ? $avatarimg : 'images/global/avatar-default.svg'), false, 'absolute'),
										'date'				=> $this->time->date('Y-m-d H:i', $row['date']),
										'date_timestamp'	=> $row['date'],
										'message'			=> $this->bbcode->toHTML($row['text']),
								);
							}
						}
						
						$arrCommentsOut = array(
								'count'		=> count($arrComments),
								'page'		=> 'articles',
								'attachid'	=> $intArticleID,
								'comments'	=> $arrComments,
						);
						
						$arrTags = array();
						$arrArticleTags = $this->pdh->get('articles', 'tags', array($intArticleID));
						if(is_array($arrArticleTags) && count($arrArticleTags) && $arrArticleTags[0] != ""){
							foreach($arrArticleTags as $k => $strTag) {
								$arrTags['tag:'.$k] = $strTag;
							}
						}
						
						$response['entries']['entry:'.$intArticleID] = array(
								'id'			=> $intArticleID,
								'title'			=> unsanitize($this->pdh->get('articles', 'title', array($intArticleID))),
								'text'			=> $strText,
								'link'			=> $this->user->removeSIDfromString($this->env->link.$this->pdh->get('articles',  'path', array($intArticleID))),
								'permalink'		=> $this->env->link.'index.php?a='.$intArticleID,
								'date'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('articles', 'date', array($intArticleID))),
								'date_timestamp' => $this->pdh->get('articles', 'date', array($intArticleID)),
								'author'		=> unsanitize($this->pdh->geth('articles', 'user_id', array($intArticleID))),
								'category_id' 	=> $category_id,
								'category'		=> $this->pdh->get('article_categories', 'name', array($category_id)),
								'category_url'	=> $this->user->removeSIDfromString($this->env->link.$this->pdh->get('article_categories',  'path', array($category_id))),
								'tags'			=> $arrTags,
								'comments'		=> $arrCommentsOut,
						);

					}
				}

				return $response;
		}
	}
}

?>