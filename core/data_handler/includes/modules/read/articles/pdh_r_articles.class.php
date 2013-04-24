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
* $Id: pdh_r_articles.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_articles" ) ) {
	class pdh_r_articles extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user', 'pdh', 'time', 'env', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $articles = NULL;
		public $categories = NULL;
		public $alias = NULL;

		public $hooks = array(
			'articles_update'
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
			'article_last_edited' => array('last_edited', array('%article_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_articles_table');
			$this->articles = NULL;
			$this->categories = NULL;
			$this->alias = NULL;
		}

		public function init(){
			$this->articles	= $this->pdc->get('pdh_articles_table');
			$this->categories = $this->pdc->get('pdh_articles_categories');
			$this->alias = $this->pdc->get('pdh_articles_alias');
			if($this->articles !== NULL){
				return true;
			}

			$pff_result = $this->db->query("SELECT * FROM __articles ORDER BY category ASC, sort_id ASC");
			while($drow = $this->db->fetch_record($pff_result) ){
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
					'alias'			=> $drow['alias'],
					'hits'			=> (int)$drow['hits'],
					'sort_id'		=> (int)$drow['sort_id'],
					'tags'			=> $drow['tags'],
					'votes_count'	=> (int)$drow['votes_count'],
					'votes_sum'		=> (int)$drow['votes_sum'],
					'votes_users'	=> $drow['votes_users'],
					'last_edited'	=> (int)$drow['last_edited'],
					'last_edited_user'	=> (int)$drow['last_edited_user'],
					'page_objects'		=> $drow['page_objects'],
				);
				
				if (!isset($this->categories[(int)$drow['category']])) $this->categories[(int)$drow['category']] = array();
				$this->categories[(int)$drow['category']][] = (int)$drow['id'];
				
				$this->alias[$drow['alias']] = intval($drow['id']);
			}
			$this->db->free_result($pff_result);
			
			$this->pdc->put('pdh_articles_table', $this->articles, null);
			$this->pdc->put('pdh_articles_categories', $this->categories, null);
			$this->pdc->put('pdh_articles_alias', $this->alias, null);
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
		
		public function get_html_last_edited_user($intArticleID){
			return $this->pdh->get('user', 'name', array($this->get_last_edited_user($intArticleID)));
		}
		
		public function get_editicon($intArticleID){
			return '<a href="'.$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$this->get_category($intArticleID).'&a='.$intArticleID.'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="edit"/></a>';
		}
		
		public function get_check_alias($strAlias, $blnCheckCategory=false){
			foreach ($this->articles as $key => $val){
				if ($this->get_alias($key) == $strAlias) return false;
			}
			if ($blnCheckCategory){
				//No Articel uses this alias, check categories
				$blnResult = $this->pdh->get('article_categories', 'check_alias', array($strAlias));
				return $blnResult;
			}
			return true;
		}
		
		public function get_resolve_alias($strAlias){
			if (isset($this->alias[$strAlias])){
				return $this->alias[$strAlias];
			}
			return false;
		}
		
		public function get_path($intArticleID){
			if (!intval($this->config->get('enable_seo'))) return 'index.php'.$this->SID.'&a='.(int)$intArticleID;
		
			$strPath = "";
			$strPath = $this->add_path($this->get_category($intArticleID));
			if (!intval($this->config->get('seo_remove_index'))) $strPath .= 'index.php/';
			$strPath = $strPath . $this->get_alias($intArticleID).'.html';
			return $strPath.$this->SID;
		}
		
		public function get_permalink($intArticleID){
			return $this->env->link.'index.php?a='.(int)$intArticleID;
		}
		
		private function add_path($intCategoryID, $strPath=''){
			$strAlias = $this->pdh->get('article_categories', 'alias', array($intCategoryID));
			if ($strAlias != '' && $strAlias != 'system'){
				$strPath = $strAlias.'/'.$strPath;
			}
			if ($this->pdh->get('article_categories', 'parent', array($intCategoryID))){
				$strPath = $this->add_path($this->pdh->get('article_categories', 'parent', array($intCategoryID)), $strPath);
			}
			
			return $strPath;
		}
		
		public function get_breadcrumb($intArticleID){
			$strBreadcrumb = $this->add_breadcrumb($this->get_category($intArticleID));

			$strBreadcrumb .=  '<a href="'.$this->server_path.$this->get_path($intArticleID).'">'.$this->get_title($intArticleID).'</a>';
			return $strBreadcrumb;
		}
		
		private function add_breadcrumb($intCategoryID, $strBreadcrumb=''){
			if ($intCategoryID == 1) return $strBreadcrumb;
			$strName = $this->pdh->get('article_categories', 'name', array($intCategoryID));
			$strPath = $this->pdh->get('article_categories', 'path', array($intCategoryID));
			$strBreadcrumb = '<a href="'.$this->server_path.$strPath.'">'.$strName.'</a> &raquo; '.$strBreadcrumb;
			
			if ($this->pdh->get('article_categories', 'parent', array($intCategoryID))){
				$strBreadcrumb = $this->add_breadcrumb($this->pdh->get('article_categories', 'parent', array($intCategoryID)), $strBreadcrumb);
			}
			
			return $strBreadcrumb;
		}

	}//end class
}//end if
?>