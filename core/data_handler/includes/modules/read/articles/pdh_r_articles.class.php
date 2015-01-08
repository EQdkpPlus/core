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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_articles" ) ) {
	class pdh_r_articles extends pdh_r_generic{

		public $default_lang = 'english';
		public $articles = NULL;
		public $categories = NULL;
		public $alias = NULL;
		public $pageobjects = NULL;
		public $tags = NULL;

		public $hooks = array(
			'articles_update',
			'article_categories_update',
		);
		
		public $presets = array(
			//'article_sortable' => array('sort_id', array('%article_id%'), array()),
			'article_editicon' => array('editicon', array('%article_id%'), array()),
			'article_published' => array('published', array('%article_id%'), array()),
			'article_featured' => array('featured', array('%article_id%'), array()),
			'article_title' => array('title', array('%article_id%'), array()),
			'article_alias' => array('alias', array('%article_id%'), array()),
			'article_user' => array('user_id', array('%article_id%'), array()),
			'article_date' => array('date', array('%article_id%'), array()),
			'article_index' => array('index', array('%article_id%'), array()),
			'article_index_cb' => array('index_cb', array('%article_id%'), array()),
			'article_last_edited' => array('last_edited', array('%article_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_articles_table');
			$this->pdc->del('pdh_articles_categories');
			$this->pdc->del('pdh_articles_alias');
			$this->pdc->del('pdh_articles_pageobjects');
			$this->pdc->del('pdh_articles_tags');
			
			$this->articles = NULL;
			$this->categories = NULL;
			$this->alias = NULL;
			$this->pageobjects = NULL;
			$this->tags = NULL;
		}

		public function init(){
			$this->articles	= $this->pdc->get('pdh_articles_table');
			$this->categories = $this->pdc->get('pdh_articles_categories');
			$this->alias = $this->pdc->get('pdh_articles_alias');
			$this->pageobjects = $this->pdc->get('pdh_articles_pageobjects');
			$this->tags = $this->pdc->get('pdh_articles_tags');
			
			if($this->articles !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __articles ORDER BY category ASC, sort_id ASC");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->articles[(int)$drow['id']] = array(
						'id'			=> (int)$drow['id'],
						'title'			=> $drow['title'],
						'text'			=> $drow['text'],
						'category'		=> (int)$drow['category'],
						'featured'		=> (int)$drow['featured'],
						'comments'		=> (int)$drow['comments'],
						'votes'			=> (int)$drow['votes'],
						'published'		=> (int)$drow['published'],
						'show_from'		=> $drow['show_from'],
						'show_to'		=> $drow['show_to'],
						'user_id'		=> (int)$drow['user_id'],
						'date'			=> (int)$drow['date'],
						'previewimage'	=> $drow['previewimage'],
						'alias'			=> utf8_strtolower($drow['alias']),
						'hits'			=> (int)$drow['hits'],
						'sort_id'		=> (int)$drow['sort_id'],
						'tags'			=> $drow['tags'],
						'votes_count'	=> (int)$drow['votes_count'],
						'votes_sum'		=> (int)$drow['votes_sum'],
						'votes_users'	=> $drow['votes_users'],
						'last_edited'	=> (int)$drow['last_edited'],
						'last_edited_user'	=> (int)$drow['last_edited_user'],
						'page_objects'		=> $drow['page_objects'],
						'hide_header'		=> (int)$drow['hide_header'],
						'index'			=> (int)$drow['index'],
						'undeletable'			=> (int)$drow['undeletable'],
					);
					
					if (!isset($this->categories[(int)$drow['category']])) $this->categories[(int)$drow['category']] = array();
					$this->categories[(int)$drow['category']][] = (int)$drow['id'];
					
					$this->alias[utf8_strtolower($drow['alias'])] = intval($drow['id']);
					
					if ($drow['page_objects'] != ''){
						$arrObjects = unserialize($drow['page_objects']);
						foreach($arrObjects as $elem){
							$this->pageobjects[$elem][] = (int)$drow['id'];
						}
					}
					
					if ($drow['tags'] != ''){
						$arrTags = unserialize($drow['tags']);
						foreach($arrTags as $elem){
							$this->tags[$elem][] = (int)$drow['id'];
						}
					}
				}
				
				$this->pdc->put('pdh_articles_table', $this->articles, null);
				$this->pdc->put('pdh_articles_categories', $this->categories, null);
				$this->pdc->put('pdh_articles_alias', $this->alias, null);
				$this->pdc->put('pdh_articles_pageobjects', $this->pageobjects, null);
				$this->pdc->put('pdh_articles_tags', $this->tags, null);
			}
			

		}
		
		public function get_id_list($intCategoryID = false){
			if ($intCategoryID == false){
				return array_keys($this->articles);
			} else {
				if (isset($this->categories[$intCategoryID])) return $this->categories[$intCategoryID];
			}
			return array();
		}
		
		public function get_id_list_featured_ontop($arrViewList){
			if (!is_array($arrViewList) || count($arrViewList) == 0) return array();
			
			$arrOut = array();
			foreach($arrViewList as $key => $articleID){
				if ($this->get_featured($articleID)){
					$arrOut[] = $articleID;
					unset($arrViewList[$key]);
				}
			}
			if (count($arrViewList)) $arrOut = array_merge($arrOut, $arrViewList);
			return $arrOut;
		}
		
		public function get_data($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID];
			}
			return false;
		}
		
		public function get_title($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['title'];
			}
			return false;
		}
		
		public function get_html_title($intArticleID){
			return '<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$this->get_category($intArticleID).'&a='.$intArticleID.'">'.$this->get_title($intArticleID).'</a>';
		}
		
		public function get_text($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['text'];
			}
			return false;
		}
		
		public function get_category($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['category'];
			}
			return false;
		}
		
		public function get_featured($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['featured'];
			}
			return false;
		}
		
		public function get_html_featured($intArticleID){
			if ($this->get_featured($intArticleID)){
				$strImage = '<div><div class="featured featuredToggleTrigger"></div><input type="hidden" class="featured_cb" name="featured['.$intArticleID.']" value="1"/></div>';
			} else {
				$strImage = '<div><div class="not-featured featuredToggleTrigger"></div><input type="hidden" class="featured_cb" name="featured['.$intArticleID.']" value="0"/></div>';
			}
			return $strImage;
		}
		
		public function get_comments($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['comments'];
			}
			return false;
		}
		
		public function get_votes($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['votes'];
			}
			return false;
		}
		
		public function get_published($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['published'];
			}
			return false;
		}
		
		public function get_index($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['index'];
			}
			return false;
		}
		
		public function get_index_cb($intArticleID){
			$intCategoryIndex = $this->pdh->get('article_categories', 'index_article', array($this->get_category($intArticleID)));
			if($intCategoryIndex){
				if($intCategoryIndex == $intArticleID){
					return '<input type="checkbox" class="index_cb" value="'.$intArticleID.'" name="index" checked="checked"/>';
				} else {
					return '<input type="checkbox" class="index_cb" value="'.$intArticleID.'" name="index" disabled="disabled"/>';
				}
			} else {
				return '<input type="checkbox" class="index_cb" value="'.$intArticleID.'" name="index" />';
			}
		}
		
		public function get_html_published($intArticleID){
			if ($this->get_published($intArticleID)){
				$strImage = '<div><div class="eye eyeToggleTrigger"></div><input type="hidden" class="published_cb" name="published['.$intArticleID.']" value="1"/></div>';
			} else {
				$strImage = '<div><div class="eye-gray eyeToggleTrigger"></div><input type="hidden" class="published_cb" name="published['.$intArticleID.']" value="0"/></div>';
			}
			return $strImage;
		}
					
		public function get_show_from($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['show_from'];
			}
			return false;
		}
		
		public function get_show_to($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['show_to'];
			}
			return false;
		}
		
		public function get_user_id($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['user_id'];
			}
			return false;
		}
		
		public function get_html_user_id($intArticleID){
			return $this->pdh->get('user', 'name', array($this->get_user_id($intArticleID)));
		}
		
		public function get_date($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['date'];
			}
			return false;
		}
		
		public function get_html_date($intArticleID){
			return $this->time->user_date($this->get_date($intArticleID), true);
		}
		
		
		
		public function get_previewimage($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['previewimage'];
			}
			return false;
		}
		
		public function get_html_previewimage($intArticleID, $intSize=120){
			$strPreviewImage = $this->get_previewimage($intArticleID);
			if ($strPreviewImage == "") return "";
			
			$extension = pathinfo($strPreviewImage, PATHINFO_EXTENSION);
			$thumbnailname = "thumb_articlepreview_".md5($intArticleID.'.'.$strPreviewImage).'_'.$intSize.'.'.$extension;
			$thumbnailfolder = $this->pfh->FolderPath('thumbnails', '');
			
			if (!is_file($thumbnailfolder.$thumbnailname)){
				$origImage = $this->pfh->FilePath($strPreviewImage,'files');
				$this->pfh->thumbnail($origImage, $thumbnailfolder, $thumbnailname, $intSize);
			}
			return $this->pfh->FolderPath('thumbnails', '', 'absolute').$thumbnailname;
		}
		
		public function get_alias($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['alias'];
			}
			return false;
		}
		
		public function get_hits($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['hits'];
			}
			return false;
		}
		
		public function get_sort_id($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['sort_id'];
			}
			return false;
		}
		
		public function get_html_sort_id($intArticleID){
			return '<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'"></span><input type="hidden" name="sortArticles[]" value="'.$intArticleID.'"/>';
		}
		
		public function get_tags($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return unserialize($this->articles[$intArticleID]['tags']);
			}
			return false;
		}
		
		public function get_votes_count($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['votes_count'];
			}
			return false;
		}
		
		public function get_votes_sum($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['votes_sum'];
			}
			return false;
		}
		
		public function get_votes_users($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return unserialize($this->articles[$intArticleID]['votes_users']);
			}
			return false;
		}
		
		public function get_last_edited($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['last_edited'];
			}
			return false;
		}
		
		public function get_html_last_edited($intArticleID){
			return $this->time->user_date($this->get_last_edited($intArticleID), true);
		}
		
		public function get_last_edited_user($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['last_edited_user'];
			}
			return false;
		}
		
		public function get_page_objects($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return unserialize($this->articles[$intArticleID]['page_objects']);
			}
			return false;
		}
		
		public function get_hide_header($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['hide_header'];
			}
			return false;
		}
		
		public function get_undeletable($intArticleID){
			if (isset($this->articles[$intArticleID])){
				return $this->articles[$intArticleID]['undeletable'];
			}
			return false;
		}
		
		public function get_html_last_edited_user($intArticleID){
			return $this->pdh->get('user', 'name', array($this->get_last_edited_user($intArticleID)));
		}
		
		public function get_editicon($intArticleID){
			return '<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$this->get_category($intArticleID).'&a='.$intArticleID.'"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
		
		public function get_check_alias($strAlias, $blnCheckCategory=false){
			$strAlias = utf8_strtolower($strAlias);
		
			foreach ($this->articles as $key => $val){
				if ($this->get_alias($key) == $strAlias) return false;
			}
			//Check static routes
			$arrRoutes = register('routing')->getRoutes();
			if (isset($arrRoutes[$strAlias])) return false;
			
			if ($blnCheckCategory){
				//No Articel uses this alias, check categories
				$blnResult = $this->pdh->get('article_categories', 'check_alias', array($strAlias));
				return $blnResult;
			}
			return true;
		}
		
		public function get_resolve_alias($strAlias){
			$strAlias = utf8_strtolower($strAlias);
			
			if (isset($this->alias[$strAlias])){
				return $this->alias[$strAlias];
			}
			return false;
		}
		
		public function get_permalink($intArticleID){
			return $this->env->link.'index.php?a='.(int)$intArticleID;
		}
		
		public function get_plain_path($intArticleID){
			$strPath = "";
			$strPath = $this->add_path($this->get_category($intArticleID));
			$strAlias = $this->get_alias($intArticleID);
			if ($this->get_index($intArticleID)){
				return $strPath;
			}
			
			$strPath .= ucfirst($this->get_alias($intArticleID));
			return $strPath;
		}
		
		public function get_path($intArticleID,$url_id=false,$arrPath=array()){		
			$strPath = "";
			$strPath = $this->add_path($this->get_category($intArticleID));
			
			$strAlias = ucfirst($this->get_alias($intArticleID));
			if ($this->get_index($intArticleID) && !$url_id){
				$strPath .= ($this->SID == "?s=") ? '?' : $this->SID;
				return $strPath;
			} elseif($url_id){
				$strPath .= ucfirst($arrPath[0]);
			} else {
				$strPath .= ucfirst($this->get_alias($intArticleID));
			}
			
			if(substr($strPath, -1) == "/") $strPath = substr($strPath, 0, -1);
			$strPath .= $this->routing->getSeoExtension();

			return $strPath.(($this->SID == "?s=") ? '?' : $this->SID);
		}
		
		private function add_path($intCategoryID, $strPath=''){
			$strAlias = ucfirst($this->pdh->get('article_categories', 'alias', array($intCategoryID)));
			if ($strAlias != '' && $strAlias != 'system' && $strAlias != 'System'){
				$strPath = $strAlias.'/'.$strPath;
			}
			if ($this->pdh->get('article_categories', 'parent', array($intCategoryID))){
				$strPath = $this->add_path($this->pdh->get('article_categories', 'parent', array($intCategoryID)), $strPath);
			}
			
			return $strPath;
		}
		
		public function get_breadcrumb($intArticleID, $strAdditionalString='', $url_id=false, $arrPath=array()){
			$strAlias = ucfirst($this->get_alias($intArticleID));
			if ($this->get_index($intArticleID) && !$url_id){
				$intCategoryID = $this->get_category($intArticleID);
				return $this->pdh->get('article_categories', 'breadcrumb', array($intCategoryID));
			}

			$strBreadcrumb = $this->add_breadcrumb($this->get_category($intArticleID));
			$strBreadcrumb .=  '<li class="current"><a href="'.$this->controller_path.$this->get_path($intArticleID, $url_id, $arrPath).'">'.$this->get_title($intArticleID).$strAdditionalString.'</a></li>';
			return $strBreadcrumb;
		}
		
		private function add_breadcrumb($intCategoryID, $strBreadcrumb=''){
			if ($intCategoryID == 1) return $strBreadcrumb;
			$strName = $this->pdh->get('article_categories', 'name', array($intCategoryID));
			$strPath = $this->pdh->get('article_categories', 'path', array($intCategoryID));
			$strBreadcrumb = '<li><a href="'.$this->controller_path.$strPath.'">'.$strName.'</a></li>'.$strBreadcrumb;
			
			if ($this->pdh->get('article_categories', 'parent', array($intCategoryID))){
				$strBreadcrumb = $this->add_breadcrumb($this->pdh->get('article_categories', 'parent', array($intCategoryID)), $strBreadcrumb);
			}
			
			return $strBreadcrumb;
		}
		
		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->articles)){
				foreach($this->articles as $id => $value) {
					if (!$this->get_published($id)) continue;			
					$intCategoryID = $this->get_category($id);
					if(!$this->pdh->get('article_categories', 'published', array($intCategoryID))) continue;
					
					$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
					if(!$arrPermissions['read']) continue;
					
					$arrTags = $this->get_tags($id);
					$blnTagSearch = false;
					foreach($arrTags as $tag){
						if (stripos($tag, $search_value) !== false){
							$blnTagSearch = true;
							break;
						}
					}
					
					if($blnTagSearch OR stripos($this->get_title($id), $search_value) !== false OR stripos($this->get_text($id), $search_value) !== false) {

						$arrSearchResults[] = array(
							'id'	=> $this->get_html_date($id),
							'name'	=> $this->get_title($id),
							'link'	=> $this->controller_path.$this->get_path($id),
						);
					}
				}
			}
			return $arrSearchResults;
		}
		
		public function get_articles_for_tag($strTag){
			$arrOut = array();
			if(isset($this->tags[$strTag])){
				foreach($this->tags[$strTag] as $id){
					if (!$this->get_published($id)) continue;
					$intCategoryID = $this->get_category($id);
					if(!$this->pdh->get('article_categories', 'published', array($intCategoryID))) continue;
					
					$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
					if(!$arrPermissions['read']) continue;
					
					$arrOut[] = $id;
				}
			}
			return $arrOut;
		}
		
		public function get_articles_for_pageobject($strPageObject){
			if (isset($this->pageobjects[$strPageObject])){
				return $this->pageobjects[$strPageObject];
			}
			return false;
		}
		
		public function get_check_pageobject_permission($strPageObject, $user_id=false){
			if(!$user_id) $user_id = $this->user->id;
			$arrArticles = $this->get_articles_for_pageobject($strPageObject);
			if($arrArticles){
				foreach($arrArticles as $id){
					if (!$this->get_published($id)) continue;
					$intCategoryID = $this->get_category($id);
					if(!$this->pdh->get('article_categories', 'published', array($intCategoryID))) continue;
						
					$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $user_id));
					if($arrPermissions['read']) return true;
				}
			}
			return false;
		}
		
		public function get_tags_array(){
			return (is_array($this->tags)) ? $this->tags : array();
		}
		
		public function get_user_permissions($intArticleID, $intUserID){
			$intCategoryID = $this->get_category($intArticleID);
			$arrCategoryPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $intUserID));
			return $arrCategoryPermissions;
		}
		

	}//end class
}//end if
?>