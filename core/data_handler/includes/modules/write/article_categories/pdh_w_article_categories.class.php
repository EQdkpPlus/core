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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_article_categories')) {
	class pdh_w_article_categories extends pdh_w_generic {
		
		private $arrLogLang = array(
				'id'				=> "{L_ID}",
				'name'				=> "{L_NAME}",
				'alias'				=> "{L_ALIAS}",
				'description'		=> "{L_DESCRIPTION}",
				'per_page'			=> "{L_ARTICLE_PER_PAGE}",
				'permissions'		=> "{L_PERMISSIONS}",
				'published'			=> "{L_PUBLISHED}",
				'parent'			=> "{L_PARENT_CATEGORY}",
				'sort_id'			=> "{L_SORTATION}",
				'list_type'			=> "{L_LIST_TYPE}",
				'aggregation'		=> "{L_AGGREGATION}",
				'featured_only'		=> "{L_FEATURED_ONLY}",
				'social_share_buttons'=> "{L_SOCIAL_SHARE_BUTTONS}",
				'portal_layout'		=> "{L_PORTAL_LAYOUT}",
				'show_childs'		=> "{L_SHOW_CHILD_CATEGORIES}",
				'article_published_state' => "{L_ARTICLE_PUBLISHED_STATE}",
				'notify_on_onpublished_articles' => "{L_NOTIFY_ON_UNPUBLISHED_ARTICLES}",
				'hide_header'		=> "{L_HIDE_HEADER}",
				'sortation_type'	=> "{L_SORTATION_TYPE}",
				'featured_ontop'	=> "{L_FEATURED_ONTOP}",
				'hide_on_rss'		=> "{L_HIDE_ON_RSS}",
		);

		public function delete($id) {
			$this->delete_recursiv(intval($id));
			
			$this->pdh->enqueue_hook('articles_update');
			$this->pdh->enqueue_hook('article_categories_update');
			return true;
		}
		
		private function delete_recursiv($intCategoryID){
			if ($this->pdh->get('article_categories', 'childs', array($intCategoryID))){
				foreach($this->pdh->get('article_categories', 'childs', array($intCategoryID)) as $intChildID){
					$this->delete_recursiv($intChildID);
					$arrOldData = $this->pdh->get('article_categories', 'data', array($intChildID));

					$this->db->prepare("DELETE FROM __articles WHERE category=?")->execute($intChildID);
					
					$log_action = $this->logs->diff(false, $arrOldData, $this->arrLogLang);
					$this->log_insert("action_articlecategory_deleted", $log_action, $intChildID, $arrOldData['title']);
				}
			}
			$arrOldData = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			$this->db->prepare("DELETE FROM __article_categories WHERE id =?")->execute($intCategoryID);
			$log_action = $this->logs->diff(false, $arrOldData, $this->arrLogLang);
			$this->log_insert("action_articlecategory_deleted", $log_action, $intCategoryID, $arrOldData["name"]);
			
			return true;
		}
		
		
		
		public function add($strName, $strDescription, $strAlias, $intPublished, $intPortalLayout, $intArticlePerPage, $intParentCategory, $intListType, $intShowChilds, $arrAggregation, $intFeaturedOnly, $intSocialButtons, $intArticlePublishedState, $arrPermissions, $intNotifyUnpublishedArticles,$intHideHeader, $intSortationType, $intFeaturedOntop, $intHideOnRSS){
			if ($strAlias == ""){
				$strAlias = $this->create_alias($strName);
			} else {
				$strAlias = $this->create_alias($strAlias);
			}
			
			//Check Alias
			$blnAliasResult = $this->check_alias(0, $strAlias);
			if (!$blnAliasResult) return false;
			
			$strDescription = $this->bbcode->replace_shorttags($strDescription);
			$strDescription = $this->embedly->parseString($strDescription);
			
			if(!$this->user->check_auth('u_articles_script', false)){
				include_once($this->root_path."libraries/inputfilter/input.class.php");
				$filter = new FilterInput(get_tag_blacklist(), get_attr_blacklist(), 1,1);
				$strDescription = htmlspecialchars($filter->clean($strDescription));
			}			
			
			$arrQuery  = array(
				'name' 			=> $strName,
				'alias' 		=> $strAlias,
				'portal_layout' => $intPortalLayout,
				'description'	=> $strDescription,
				'per_page'		=> $intArticlePerPage,
				'permissions'	=> serialize($arrPermissions),
				'published'		=> $intPublished,
				'parent'		=> $intParentCategory,
				'sort_id'		=> 99999999,
				'list_type'		=> $intListType,
				'aggregation'	=> serialize($arrAggregation),
				'featured_only' => $intFeaturedOnly,
				'social_share_buttons' => $intSocialButtons,
				'show_childs'	=> $intShowChilds,
				'article_published_state' => $intArticlePublishedState,
				'notify_on_onpublished_articles' => $intNotifyUnpublishedArticles,
				'hide_header'	=> $intHideHeader,
				'sortation_type' => $intSortationType,
				'featured_ontop' => $intFeaturedOntop,
				'hide_on_rss'	=> $intHideOnRSS,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __article_categories :p")->set($arrQuery)->execute();
			
			
			
			if ($objQuery){
				$id = $objQuery->insertId;
				$arrAggregation[] = $id;
				
				$objQuery = $this->db->prepare("UPDATE __article_categories :p WHERE id=?")->set(array(
					'aggregation' => serialize($arrAggregation),
				))->execute($id);
				
				$log_action = $this->logs->diff(false, $arrQuery, $this->arrLogLang);
				$this->log_insert("action_articlecategory_added", $log_action, $id, $arrQuery["name"], 1, 'article');
				
				$this->pdh->enqueue_hook('article_categories_update');
				return $id;
			}
			
			return false;
		}
		
		public function update($id, $strName, $strDescription, $strAlias, $intPublished, $intPortalLayout, $intArticlePerPage, $intParentCategory, $intListType, $intShowChilds, $arrAggregation, $intFeaturedOnly, $intSocialButtons, $intArticlePublishedState, $arrPermissions, $intNotifyUnpublishedArticles,$intHideHeader, $intSortationType, $intFeaturedOntop, $intHideOnRSS){
			if ($strAlias == ""){
				$strAlias = $this->create_alias($strName);
			} elseif($strAlias != $this->pdh->get('article_categories', 'alias', array($id))) {
				$strAlias = $this->create_alias($strAlias);
			}
			
			//Check Alias
			$blnAliasResult = $this->check_alias($id, $strAlias);
			if (!$blnAliasResult) return false;
			
			$strDescription = $this->bbcode->replace_shorttags($strDescription);
			$strDescription = $this->embedly->parseString($strDescription);
			
			if(!$this->user->check_auth('u_articles_script', false)){
				include_once($this->root_path."libraries/inputfilter/input.class.php");
				$filter = new FilterInput(get_tag_blacklist(), get_attr_blacklist(), 1,1);
				$strDescription = htmlspecialchars($filter->clean($strDescription));
			}
			
			$arrQuery = array(
				'name' 			=> $strName,
				'alias' 		=> $strAlias,
				'portal_layout' => $intPortalLayout,
				'description'	=> $strDescription,
				'per_page'		=> $intArticlePerPage,
				'permissions'	=> serialize($arrPermissions),
				'published'		=> $intPublished,
				'parent'		=> $intParentCategory,
				'list_type'		=> $intListType,
				'aggregation'	=> serialize($arrAggregation),
				'featured_only' => $intFeaturedOnly,
				'social_share_buttons' => $intSocialButtons,
				'show_childs'	=> $intShowChilds,
				'article_published_state' => $intArticlePublishedState,
				'notify_on_onpublished_articles' => $intNotifyUnpublishedArticles,
				'hide_header'	=> $intHideHeader,
				'sortation_type' => $intSortationType,
				'featured_ontop' => $intFeaturedOntop,
				'hide_on_rss'	=> $intHideOnRSS,
			);
			
			$arrOldData = $this->pdh->get('article_categories', 'data', array($id));
			
			$objQuery = $this->db->prepare("UPDATE __article_categories :p WHERE id=?")->set($arrQuery)->execute($id);
						
			if ($objQuery){
				$this->pdh->enqueue_hook('article_categories_update');
				
				$log_action = $this->logs->diff($arrOldData, $arrQuery, $this->arrLogLang, array('description' => 1), true);
				$this->log_insert("action_articlecategory_updated", $log_action, $id, $arrOldData["name"], 1, 'article');
				
				return $id;
			}
			
			return false;
		}
		
		public function update_sortandpublished($id, $intSortID, $intPublished){
			$arrOldData = array(
				'published' => $this->pdh->get('article_categories', 'published', array($id)),
			);
			
			$objQuery = $this->db->prepare("UPDATE __article_categories :p WHERE id=?")->set(array(
				'sort_id'		=> $intSortID,
				'published'		=> $intPublished,
			))->execute($id);
			
			if ($objQuery){
				$arrNewData = array(
					'published' => $intPublished,	
				);
				$log_action = $this->logs->diff($arrOldData, $arrNewData, $this->arrLogLang, array());
				if ($log_action) $this->log_insert("action_articlecategory_updated", $log_action, $id, $this->pdh->get('article_categories', 'name', array($id)), 1, 'article');
				
				$this->pdh->enqueue_hook('article_categories_update');
				return $id;
			}
			return false;
		}
		
		private function check_alias($id, $strAlias){
			if (is_numeric($strAlias)) return false;
			
			if ($id){
				$strMyAlias = $this->pdh->get('article_categories', 'alias', array($id));
				if ($strMyAlias == $strAlias) return true;		
				$blnResult = $this->pdh->get('article_categories', 'check_alias', array($strAlias));
				return $blnResult;
				
			} else {
				$blnResult = $this->pdh->get('article_categories', 'check_alias', array($strAlias));
				return $blnResult;
				
			}
			return false;
		}
		
		private function create_alias($strName){
			$strAlias = utf8_strtolower($strName);
			$strAlias = str_replace(' ', '-', $strAlias);
			$a_satzzeichen = array("\"",",",";",".",":","!","?", "&", "=", "/", "|", "#", "*", "+", "(", ")", "%", "$");
			$strAlias = str_replace($a_satzzeichen, "", $strAlias);
			return $strAlias;
		}
		
	}
}
?>