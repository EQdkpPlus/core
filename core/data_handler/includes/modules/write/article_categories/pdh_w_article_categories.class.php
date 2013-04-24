<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
* -----------------------------------------------------------------------
* @author		$Author: wallenium $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 12937 $
*
* $Id: pdh_w_article_categories.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_article_categories')) {
	class pdh_w_article_categories extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'pfh',  'bbcode'=>'bbcode', 'embedly'=>'embedly');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

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
					$this->db->query("DELETE FROM __articles WHERE category='".$this->db->escape($intChildID)."'");
				}
			}
			$this->db->query("DELETE FROM __article_categories WHERE id = '".$this->db->escape($intCategoryID)."'");
			
			
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
			
			$blnResult = $this->db->query("INSERT INTO __article_categories :params", array(
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
			));
			
			$id = $this->db->insert_id();
			
			if ($blnResult){
				$arrAggregation[] = $id;
				$this->db->query("UPDATE __article_categories SET :params WHERE id=?", array(
					'aggregation' => serialize($arrAggregation),
				), $id);
				
				$this->pdh->enqueue_hook('article_categories_update');
				return $id;
			}
			
			return false;
		}
		
		public function update($id, $strName, $strDescription, $strAlias, $intPublished, $intPortalLayout, $intArticlePerPage, $intParentCategory, $intListType, $intShowChilds, $arrAggregation, $intFeaturedOnly, $intSocialButtons, $intArticlePublishedState, $arrPermissions, $intNotifyUnpublishedArticles,$intHideHeader, $intSortationType, $intFeaturedOntop, $intHideOnRSS){
			if ($strAlias == "" || $strAlias != $this->pdh->get('article_categories', 'alias', array($id))){
				$strAlias = $this->create_alias($strName);
			} else {
				$strAlias = $this->create_alias($strAlias);
			}
			
			//Check Alias
			$blnAliasResult = $this->check_alias($id, $strAlias);
			if (!$blnAliasResult) return false;
			
			$strDescription = $this->bbcode->replace_shorttags($strDescription);
			$strDescription = $this->embedly->parseString($strDescription);
			
			$blnResult = $this->db->query("UPDATE __article_categories SET :params WHERE id=?", array(
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
			), $id);
						
			if ($blnResult){
				$this->pdh->enqueue_hook('article_categories_update');
				return $id;
			}
			
			return false;
		}
		
		public function update_sortandpublished($id, $intSortID, $intPublished){
			$blnResult = $this->db->query("UPDATE __article_categories SET :params WHERE id=?", array(
				'sort_id'		=> $intSortID,
				'published'		=> $intPublished,
			), $id);
			
			if ($blnResult){
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
			$strAlias = preg_replace("/[^a-zA-Z0-9_-]/","",$strAlias);
			return $strAlias;
		}
		
	}
}
?>