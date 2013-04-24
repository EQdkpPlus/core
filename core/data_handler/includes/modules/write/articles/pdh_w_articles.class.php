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
* $Id: pdh_w_articles.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_articles')) {
	class pdh_w_articles extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'pfh', 'user', 'time',  'bbcode'=>'bbcode', 'embedly'=>'embedly');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function delete($id) {
			$this->db->query("DELETE FROM __articles WHERE id = '".$this->db->escape($id)."' AND system = ''");
			$this->pdh->enqueue_hook('articles_update');
		}
		
		public function delete_category($intCategoryID){
			$this->db->query("DELETE FROM __articles WHERE category = '".$this->db->escape($intCategoryID)."' AND system = '' ");
			$this->pdh->enqueue_hook('articles_update');
		}
		
		public function add($strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader){
			if ($strAlias == ""){
				$strAlias = $this->create_alias($strTitle);
			} else {
				$strAlias = $this->create_alias($strAlias);
			}
			
			//Check Alias
			$blnAliasResult = $this->check_alias(0, $strAlias);			
			
			$strText = $this->bbcode->replace_shorttags($strText);
			$strText = $this->embedly->parseString($strText);
			
			$arrPageObjects = array();
			preg_match_all('#<p(.*)class="system-article"(.*) title="(.*)">(.*)</p>#iU', xhtml_entity_decode($strText), $arrTmpPageObjects, PREG_PATTERN_ORDER);
			if (count($arrTmpPageObjects[0])){
				foreach($arrTmpPageObjects[3] as $key=>$val){
					$arrPageObjects[] = $val;
				}
			}
			
			$blnResult = $this->db->query("INSERT INTO __articles :params", array(
				'title' 			=> $strTitle,
				'text'				=> $strText,
				'category'			=> $intCategory,
				'featured'			=> $intFeatured,
				'comments'			=> $intComments,
				'votes'				=> $intVotes,
				'published'			=> $intPublished,
				'show_from'			=> $strShowFrom,
				'show_to'			=> $strShowTo,
				'user_id'			=> $intUserID,
				'date'				=> $intDate,
				'previewimage'		=> $strPreviewimage,
				'alias'				=> ($blnAliasResult) ? $strAlias : '',
				'hits'				=> 0,
				'sort_id'			=> 0,
				'tags'				=> serialize($arrTags),
				'votes_count'		=> 0,
				'votes_sum'			=> 0,
				'last_edited'		=> $this->time->time,
				'last_edited_user'	=> $this->user->id,
				'page_objects'		=> serialize($arrPageObjects),
				'hide_header'		=> $intHideHeader,
			));
			
			$id = $this->db->insert_id();
			
			if ($blnResult){
				if (!$blnAliasResult){
					$blnAliasResult = $this->check_alias(0, $strAlias.'-'.$id);
					if ($blnAliasResult){
						$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
							'alias' => $strAlias.'-'.$id,
						), $id);
					} else {
						$this->db->query("DELETE FROM __articles WHERE id=?", false, $id);
						return false;
					}
				}
				
				$this->pdh->enqueue_hook('articles_update');
				return $id;
			}
			
			return false;
		}
		
		public function update($id, $strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader){
			if ($strAlias == "" || $strAlias != $this->pdh->get('articles', 'alias', array($id))){
				$strAlias = $this->create_alias($strTitle);
			} else {
				$strAlias = $this->create_alias($strAlias);
			}
			
			//Check Alias
			$blnAliasResult = $this->check_alias($id, $strAlias);
			if (!$blnAliasResult) return false;
			
			$strText = $this->bbcode->replace_shorttags($strText);
			$strText = $this->embedly->parseString($strText);
			
			$arrPageObjects = array();
			preg_match_all('#<p(.*)class="system-article"(.*) title="(.*)">(.*)</p>#iU', xhtml_entity_decode($strText), $arrTmpPageObjects, PREG_PATTERN_ORDER);
			if (count($arrTmpPageObjects[0])){
				foreach($arrTmpPageObjects[3] as $key=>$val){
					$arrPageObjects[] = $val;
				}
			}
			
			$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
				'title' 			=> $strTitle,
				'text'				=> $strText,
				'category'			=> $intCategory,
				'featured'			=> $intFeatured,
				'comments'			=> $intComments,
				'votes'				=> $intVotes,
				'published'			=> $intPublished,
				'show_from'			=> $strShowFrom,
				'show_to'			=> $strShowTo,
				'user_id'			=> $intUserID,
				'date'				=> $intDate,
				'previewimage'		=> $strPreviewimage,
				'alias'				=> $strAlias,
				'tags'				=> serialize($arrTags),
				'last_edited'		=> $this->time->time,
				'last_edited_user'	=> $this->user->id,
				'page_objects'		=> serialize($arrPageObjects),
				'hide_header'		=> $intHideHeader,
			), $id);
						
			if ($blnResult){
				$this->pdh->enqueue_hook('articles_update');
				return $id;
			}
			
			return false;
		}
		
		public function reset_votes($id){
			$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
				'votes_count' 		=> 0,
				'votes_sum'			=> 0,
				'votes_users'		=> '',
			), $id);
			
			if ($blnResult) {
				$this->pdh->enqueue_hook('articles_update');
				return true;
			}
			
			return false;
		}
		
		public function vote($intArticleID, $intVoting){
			$intSum = $this->pdh->get('articles', 'votes_sum', array($intArticleID));
			$intCount = $this->pdh->get('articles', 'votes_count', array($intArticleID));
			$arrVotedUsers = $this->pdh->get('articles', 'votes_users', array($intArticleID));
			$arrVotedUsers[] = $this->user->id;
			$intSum += $intVoting;
			$intCount++;
			
			$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
				'votes_count' 		=> $intCount,
				'votes_sum'			=> $intSum,
				'votes_users'		=> serialize($arrVotedUsers),
			), $intArticleID);
			
			if ($blnResult) {
				$this->pdh->enqueue_hook('articles_update');
				return true;
			}
			
			return false;
		}
		
		public function delete_previewimage($id){
			$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
				'previewimage' 		=> '',
			), $id);
			
			if ($blnResult) {
				$this->pdh->enqueue_hook('articles_update');
				return true;
			}
			
			return false;
		}
		
		public function update_featuredandpublished($id, $intFeatured, $intPublished){
			$blnResult = $this->db->query("UPDATE __articles SET :params WHERE id=?", array(
				'featured'		=> $intFeatured,
				'published'		=> $intPublished,
			), $id);
			
			if ($blnResult){
				$this->pdh->enqueue_hook('articles_update');
				return $id;
			}
			return false;
		}
		
		public function set_published($arrIDs){
			$this->db->query('UPDATE __articles SET published=1 WHERE id IN ('.$this->db->escape(implode(',', $arrIDs)).')');
			$this->pdh->enqueue_hook('articles_update');
		}
		
		public function set_unpublished($arrIDs){
			$this->db->query('UPDATE __articles SET published=0 WHERE id IN ('.$this->db->escape(implode(',', $arrIDs)).')');
			$this->pdh->enqueue_hook('articles_update');
		}
		
		public function change_category($arrIDs, $intCategoryID){
			$this->db->query("UPDATE __articles SET category = ".$this->db->escape($intCategoryID).' WHERE id IN ('.$this->db->escape(implode(',', $arrIDs)).')');
			$this->pdh->enqueue_hook('articles_update');	
		}
		
		private function check_alias($id, $strAlias){
			if (is_numeric($strAlias)) return false;
			
			if ($id){
				$strMyAlias = $this->pdh->get('articles', 'alias', array($id));
				if ($strMyAlias == $strAlias) return true;		
				$blnResult = $this->pdh->get('articles', 'check_alias', array($strAlias));
				return $blnResult;
				
			} else {
				$blnResult = $this->pdh->get('articles', 'check_alias', array($strAlias));
				return $blnResult;
				
			}
			return false;
		}
		
		private function create_alias($strTitle){
			$strAlias = utf8_strtolower($strTitle);
			$strAlias = str_replace(' ', '-', $strAlias);
			$strAlias = preg_replace("/[^a-zA-Z0-9_-]/","",$strAlias);
			return $strAlias;
		}
		
		
	}
}
?>