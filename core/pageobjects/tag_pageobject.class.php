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

class tag_pageobject extends pageobject {
	public static $shortcuts = array('social' => 'socialplugins');
	
	public function __construct() {
		$handler = array(
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}
	
	public function display(){
		$strTag = utf8_strtolower($this->patharray[0]);
		if (!strlen($strTag)) redirect($this->controller_path_plain.$this->SID);
		
		$arrArticleIDs = $this->pdh->get('articles', 'articles_for_tag', array($strTag));
		$arrArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'desc');
		
		$intStart = $this->in->get('start', 0);
		$arrLimitedIDs = $this->pdh->limit($arrArticleIDs, $intStart, $this->user->data['user_nlimit']);
		
		//Articles to template
		foreach($arrLimitedIDs as $intArticleID){
			$userlink = '<a href="'.$this->routing->build('user', $this->pdh->geth('articles',  'user_id', array($intArticleID)), 'u'.$this->pdh->get('articles',  'user_id', array($intArticleID))).'">'.$this->pdh->geth('articles',  'user_id', array($intArticleID)).'</a>';
		
			//Content dependet from list_type
			//1 = until readmore
			//2 = Headlines only
			//3 = only first 600 characters
			$strText = $this->pdh->get('articles',  'text', array($intArticleID));
			$arrContent = preg_split('#<hr(.*)id="system-readmore"(.*)\/>#iU', xhtml_entity_decode($strText));
		
			$strText = $this->bbcode->parse_shorttags($arrContent[0]);
			$strPath = $this->pdh->get('articles',  'path', array($intArticleID));
			$intCategoryID = $this->pdh->get('articles',  'category', array($intArticleID));
		
			//Replace Image Gallery
			$arrGalleryObjects = array();
			preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strText, $arrGalleryObjects, PREG_PATTERN_ORDER);
			if (count($arrGalleryObjects[0])){
				include_once($this->root_path.'core/gallery.class.php');
				foreach($arrGalleryObjects[4] as $key=>$val){
					$objGallery = registry::register('gallery');
					$strGalleryContent = $objGallery->create($val, (int)$arrGalleryObjects[3][$key], $this->server_path.$strPath, 1);
					$strText = str_replace($arrGalleryObjects[0][$key], $strGalleryContent, $strText);
				}
			}
		
			//Replace Raidloot
			$arrRaidlootObjects = array();
			preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
			if (count($arrRaidlootObjects[0])){
				include_once($this->root_path.'core/gallery.class.php');
				foreach($arrRaidlootObjects[3] as $key=>$val){
					$objGallery = registry::register('gallery');
					$withChars = ($arrRaidlootObjects[5][$key] == "true") ? true : false;
					$strRaidlootContent = $objGallery->raidloot((int)$val, $withChars);
					$strText = str_replace($arrRaidlootObjects[0][$key], $strRaidlootContent, $strText);
				}
			}

				
			$this->comments->SetVars(array('attach_id'=> $intArticleID, 'page'=>'articles'));
			$intCommentsCount = $this->comments->Count();
			//Tags
			$arrTags = $this->pdh->get('articles', 'tags', array($intArticleID));
		
			$this->tpl->assign_block_vars('article_row', array(
					'ARTICLE_CONTENT' => $strText,
					'ARTICLE_TITLE'	  => $this->pdh->get('articles',  'title', array($intArticleID)),
					'ARTICLE_SUBMITTED'=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true)),
					'ARTICLE_DATE'	  => $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, false, true),
					'ARTICLE_PATH'			=> $this->controller_path.$this->pdh->get('articles',  'path', array($intArticleID)),
					'ARTICLE_SOCIAL_BUTTONS'=> ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$this->pdh->get('articles',  'path', array($intArticleID)), strip_tags($this->pdh->get('articles',  'title', array($intArticleID)))) : '',
					'PERMALINK'		=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
					'S_TAGS'		=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
					'ARTICLE_CUTTED_CONTENT' => truncate($strText, 600, '...', false, true),
					'S_READMORE'	=> (isset($arrContent[1])) ? true : false,
					'COMMENTS_COUNTER'	=> ($intCommentsCount == 1 ) ? $intCommentsCount.' '.$this->user->lang('comment') : $intCommentsCount.' '.$this->user->lang('comments'),
					'S_COMMENTS'	=> ($this->pdh->get('articles',  'comments', array($intArticleID))) ? true : false,
					'S_FEATURED'	=> ($this->pdh->get('articles',  'featured', array($intArticleID))),
			));
		
		
		
			if (count($arrTags) && $arrTags[0] != ""){
				foreach($arrTags as $tag){
					$this->tpl->assign_block_vars('article_row.tag_row', array(
							'TAG'	=> $tag,
							'U_TAG'	=> $this->routing->build('tag', $tag),
					));
				}
			}
		}
		
		
		$this->tpl->assign_vars(array(
				'TAG'	=> sanitize($strTag),
				'PAGINATION' => generate_pagination($this->strPath.$this->SID, count($arrArticleIDs), $this->user->data['user_nlimit'], $intStart, 'start'),
		));
		
		$this->tpl->add_meta('<link rel="canonical" href="'.$this->env->link.$this->routing->build('tag', $tag, false, false, true).'" />');
		
		$this->core->set_vars(array(
				'page_title' 		=> $this->user->lang("tag").': '.sanitize($strTag),
				'template_file'		=> 'tag.html',
				'display'			=> true)
		);
	}
}
?>