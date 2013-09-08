<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class controller extends gen_class {
	public static $shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'config', 'core', 'html', 'time', 'env', 'acl', 'comments','social' => 'socialplugins', 'bbcode', 'pfh','pm', 'routing');
	
	public function __construct() {
		$blnCheckPost = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken()));
		$blnCheckPostOld = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken(true)));
		if ($this->in->exists('delete') && ($blnCheckPost || $blnCheckPostOld)){
			$this->delete();
		}
		if ($this->in->exists('unpublish') && ($blnCheckPost || $blnCheckPostOld)){
			$this->unpublish();
		}
	
		$this->display();
	}
	
	public function delete(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['delete']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'delete', array($intArticleID));
				$this->core->message($strArticleTitle, $this->user->lang('del_suc'), 'green');
				
				$this->pdh->process_hook_queue();
			}			
		}
	}
	
	public function unpublish(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['change_state']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'set_unpublished', array(array($intArticleID)));

				$this->core->message($strArticleTitle, $this->user->lang('article_unpublish_success'), 'green');
				
				$this->pdh->process_hook_queue();
			}			
		}
	}
	
	private function filterPathArray($arrPath){
		foreach($arrPath as $key => $val){
			$arrPath[$key] = str_replace(array(".html", ".php"), "", utf8_strtolower($arrPath[$key]));
		}
		
		return $arrPath;
	}
	
	public function display(){
		$strPath = $this->env->path;
		$arrPath = array_filter(explode('/', $strPath));
		$arrPath = array_reverse($arrPath);
		$arrPath = $this->filterPathArray($arrPath);
		register('pm');
		
		if (count($arrPath) == 0){
			//Get Start Page
			if ($this->config->get('start_page') != ""){
				$strPath = $this->config->get('start_page');
			} else {
				$strPath = "news";
			}
			$arrPath = array_filter(explode('/', $strPath));
			$arrPath = array_reverse($arrPath);
		}
		$intArticleID = $intCategoryID = $strSpecificID = 0;

		//Suche Alias in Artikeln
		$intArticleID = ($this->in->exists('a')) ? $this->in->get('a', 0) : $this->pdh->get('articles', 'resolve_alias', array($arrPath[0]));
		
		if (!$intArticleID){
			//Suche Alias in Kategorien
			$intCategoryID = ($this->in->exists('c')) ? $this->in->get('c', 0) : $this->pdh->get('article_categories', 'resolve_alias', array($arrPath[0]));
			
			//Suche in Artikeln mit nächstem Index, denn könnte ein dynamischer Systemartikel sein
			if (!$intCategoryID && isset($arrPath[1])) {
				
				$intArticleID = $this->pdh->get('articles', 'resolve_alias', array($arrPath[1]));
				if ($intArticleID){
					//Zerlege .html
					$strID = str_replace("-", "", strrchr($arrPath[0], "-"));
					$arrMatches = array();
					preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);
					if (isset($arrMatches[0]) && count($arrMatches[0])){
						if (count($arrMatches[0]) == 2){
							if(is_numeric($arrMatches[0][1])) $arrMatches[0][1] = intval($arrMatches[0][1]);
							$this->in->inject($arrMatches[0][0], $arrMatches[0][1]);
						}
					}
					if (strlen($strID)) {
						if(is_numeric($strID)) $strID = intval($strID);
						registry::add_const('url_id', $strID);
						$strSpecificID = $strID;
					} elseif (strlen($arrPath[0])){
						$this->in->inject(utf8_strtolower($arrPath[0]), 'injected');
						registry::add_const('url_id', $arrPath[0]);
						$strSpecificID = $arrPath[0];
					}
				}
			}
		}
		
		//Display Artikel
		if ($intArticleID){
			$arrArticle = $this->pdh->get('articles', 'data', array($intArticleID));
			
			//Perform Vote
			if ($this->in->exists('article_vote')){
				$arrVotedUsers = $this->pdh->get('articles', 'votes_users', array($intArticleID));
				$blnUserHasVoted = (is_array($arrVotedUsers) && in_array($this->user->id, $arrVotedUsers) && $this->user->id) ? true : false;
				if (!$blnUserHasVoted){
					$this->pdh->put('articles', 'vote', array($intArticleID, $this->in->get('article_vote', 0)));
					$this->pdh->process_hook_queue();
				}
				
				$intSum = $this->pdh->get('articles', 'votes_sum', array($intArticleID));
				$intCount = $this->pdh->get('articles', 'votes_count', array($intArticleID));
				$intRating = ($intCount) ? round($intSum/$intCount) : 0;

				die();
			}
			
			//Check if Published
			$intPublished = $arrArticle['published'];
			
			//Check Start to/start from
			if (($arrArticle['show_from'] != "" && $arrArticle['show_from'] < $this->time->time) || ($arrArticle['show_to'] != "" && $arrArticle['show_to'] > $this->time->time)) $intPublished = false;
			
			//Check Category Permission
			$intCategoryID = $arrArticle['category'];
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			
			//Check Start to/start from
			if (!$intPublished || !$arrCategory['published']) message_die('Dieser Artikel ist nicht veröffentlicht.');
			
			registry::add_const('page_path', $strPath);
			$strPath = ucfirst($this->pdh->get('articles', 'path', array($intArticleID)));
			registry::add_const('page', $this->user->removeSIDfromString($strPath));
			
			//User Memberships
			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($this->user->id);
			
			//Category Permissions
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($arrArticle['category'], $this->user->id));
			if (!$arrPermissions['read']) message_die('Keine Berechtigung, diesen Artikel anzusehen.', $this->user->lang('noauth_default_title'), 'access_denied', true);
			
			//Page divisions
			$strText = xhtml_entity_decode($arrArticle['text']);
			$arrPagebreaks = array();
			preg_match_all('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText, $arrPagebreaks, PREG_PATTERN_ORDER);

			if (count($arrPagebreaks[0])){
				$arrTitles[1] = $arrArticle['title'];
				foreach($arrPagebreaks[2] as $key=>$val){
					$titleMatches = array();
					$intMatches = preg_match('#title="(.*)"#iU', $val, $titleMatches);
					$arrTitles[$key+2] = ($intMatches && $titleMatches[1] != '' ) ? $titleMatches[1] : 'Page '.$key+2;
				}
				$arrContent = preg_split('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText);

				array_unshift($arrContent, "");
				
			} else {
				$arrContent[0] = "";
				$arrContent[1] = $strText;
				$arrTitles[1] = $arrArticle['title'];
			}
			
			//Page
			$pageCount = count($arrContent) - 1;
			$intPageID = ($this->in->get('page', 0) && isset($arrContent[$this->in->get('page', 0)])) ? $this->in->get('page', 0) : 1;
			
			//Bring Page Sitemap to Template
			if ($pageCount > 1) {
				foreach($arrTitles as $key => $val){
					$this->tpl->assign_block_vars('articlesitemap_row', array(
						'LINK' => '<a href="'.$this->server_path.$strPath.'&amp;page='.$key.'">'.$val.'</a>',
						'ACTIVE' => ($key == $intPageID),
					));
				}
			}
			

			//Next and Previous Article
			$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($arrArticle['category']));
			if (count($arrArticleIDs)){
				$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'asc');
				$arrFlippedArticles = array_flip($arrSortedArticleIDs);
				$intRecentArticlePosition = $arrFlippedArticles[$intArticleID];

				$prevID = (isset($arrSortedArticleIDs[$intRecentArticlePosition-1])) ? $arrSortedArticleIDs[$intRecentArticlePosition-1] : false;
				$nextID = (isset($arrSortedArticleIDs[$intRecentArticlePosition+1])) ? $arrSortedArticleIDs[$intRecentArticlePosition+1] : false;
				$this->tpl->assign_vars(array(
					'S_NEXT_ARTICLE'	=> ($nextID !== false) ? true : false,
					'S_PREV_ARTICLE'	=> ($prevID !== false) ? true : false,
					'U_NEXT_ARTICLE'	=> ($nextID) ? $this->server_path.$this->pdh->get('articles', 'path', array($nextID)) : '',
					'U_PREV_ARTICLE'	=> ($prevID) ? $this->server_path.$this->pdh->get('articles', 'path', array($prevID)) : '',
					'NEXT_TITLE'		=> ($nextID) ? $this->pdh->get('articles', 'title', array($nextID)) : '',
					'PREV_TITLE'		=> ($prevID) ? $this->pdh->get('articles', 'title', array($prevID)) : '',
				));
			}
		
			
			$userlink = '<a href="'.$this->routing->build('user', $this->pdh->geth('articles',  'user_id', array($intArticleID)), 'u'.$this->pdh->get('articles',  'user_id', array($intArticleID))).'">'.$this->pdh->geth('articles',  'user_id', array($intArticleID)).'</a>';
			$myRatings = array(
				'1'		=> '1',
				'2'		=> '2',
				'3'		=> '3',
				'4'		=> '4',
				'5'		=> '5',
				'6'		=> '6',
				'7'		=> '7',
				'8'		=> '8',
				'9'		=> '9',
				'10'	=> '10',
			);
		
		$arrToolbarItems = array();
		if ($arrPermissions['create']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'icon-plus',
				'js'	=> 'onclick="editArticle(0)"',
				'title'	=> $this->user->lang('add_new_article'),
			); 
		}
		if ($arrPermissions['update']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'icon-edit',
				'js'	=> 'onclick="editArticle('.$intArticleID.')"',
				'title'	=> $this->user->lang('edit_article'),
			); 
		}				
		if ($arrPermissions['delete']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'icon-trash',
				'js'	=> 'onclick="deleteArticle('.$intArticleID.')"',
				'title'	=> $this->user->lang('delete_article'),
			);
		}
		if ($arrPermissions['change_state']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'icon-eye-close',
				'js'	=> 'onclick="window.location=\''.$this->controller_path.$this->page_path.$this->SID.'&unpublish&link_hash='.$this->CSRFGetToken('unpublish').'&aid='.$intArticleID.'\'"',
				'title'	=> $this->user->lang('article_unpublish'),
			);
		}
						
		$jqToolbar = $this->jquery->toolbar('pages', $arrToolbarItems, array('position' => 'bottom'));
		
		$arrVotedUsers = $this->pdh->get('articles', 'votes_users', array($intArticleID));
		$blnUserHasVoted = (is_array($arrVotedUsers) && in_array($this->user->id, $arrVotedUsers) && $this->user->id) ? true : false;
				
		//Tags
		$arrTags = $this->pdh->get('articles', 'tags', array($intArticleID));

		if (count($arrTags) && $arrTags[0] != ""){
			foreach($arrTags as $tag){
				$this->tpl->assign_block_vars('tag_row', array(
					'TAG'	=> $tag,
					'U_TAG'	=> $this->routing->build('tag', $tag),
				));
			}
		}

		$this->comments->SetVars(array('attach_id'=> $intArticleID.(($strSpecificID) ? '|'.$strSpecificID : ''), 'page'=>'articles'));
		$intCommentsCount = $this->comments->Count();
		
		//Replace page objects from Content
		$strContent = $this->bbcode->parse_shorttags($arrContent[$intPageID]);
		$strAdditionalTitles = '';
		preg_match_all('#<p(.*)class="system-article"(.*) title="(.*)">(.*)</p>#iU', $strContent, $arrPageObjects, PREG_PATTERN_ORDER);
		if (count($arrPageObjects[0])){
			include_once($this->root_path.'core/pageobject.class.php');
			foreach($arrPageObjects[3] as $key=>$val){
				$strPageObject = $val;
				$strHaystack = $arrPageObjects[0][$key];
				if (!is_file($this->root_path.'core/page_objects/'.$val.'_pageobject.class.php')) continue;
				include_once($this->root_path.'core/page_objects/'.$val.'_pageobject.class.php');
				$objPage = registry::register($val.'_pageobject');
				$arrCoreVars = $objPage->get_vars();
				if ($arrCoreVars['template_file'] != '' ) {
					$strContent = str_replace($strHaystack, '<!-- INCLUDE '.$arrCoreVars['template_file'].' --><br />', $strContent);
				} else {
					$strContent = str_replace($strHaystack, '', $strContent);
				}
				if (isset($arrCoreVars['page_title']) && strlen($arrCoreVars['page_title'])) $strAdditionalTitles = ' - '.$arrCoreVars['page_title'];
			}
		}
		
		//Replace Image Gallery
		$arrGalleryObjects = array();
		preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strContent, $arrGalleryObjects, PREG_PATTERN_ORDER);
		if (count($arrGalleryObjects[0])){
			include_once($this->root_path.'core/gallery.class.php');
			foreach($arrGalleryObjects[4] as $key=>$val){
				$objGallery = registry::register('gallery');
				$strGalleryContent = $objGallery->create($val, (int)$arrGalleryObjects[3][$key], $this->server_path.$strPath, $intPageID);
				$strContent = str_replace($arrGalleryObjects[0][$key], $strGalleryContent, $strContent);
			}
		}
		
		//Replace Raidloot
		$arrRaidlootObjects = array();
		preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)">(.*)</p>#iU', $strContent, $arrRaidlootObjects, PREG_PATTERN_ORDER);
		if (count($arrRaidlootObjects[0])){
			include_once($this->root_path.'core/gallery.class.php');
			foreach($arrRaidlootObjects[3] as $key=>$val){
				$objGallery = registry::register('gallery');
				$strRaidlootContent = $objGallery->raidloot((int)$val);
				$strContent = str_replace($arrRaidlootObjects[0][$key], $strRaidlootContent, $strContent);
			}
		}
		
		if ($arrPermissions['create'] || $arrPermissions['update']) {
			$this->jquery->dialog('editArticle', $this->user->lang('edit_article'), array('url' => $this->controller_path."EditArticle/".$this->SID."&aid='+id+'&cid=".$intCategoryID, 'withid' => 'id', 'width' => 920, 'height' => 740, 'onclose'=> $this->env->link.$this->controller_path_plain.$this->page_path.$this->SID));
		}
		
		if ($arrPermissions['delete'] || $arrPermissions['change_state']){
			$this->jquery->dialog('deleteArticle', $this->user->lang('delete_article'), array('custom_js' => 'deleteArticleSubmit(aid);', 'confirm', 'withid' => 'aid', 'message' => $this->user->lang('delete_article_confirm')), 'confirm');
			$this->tpl->add_js(
				"function deleteArticleSubmit(aid){
					window.location='".$this->controller_path.$this->page_path.$this->SID.'&delete&link_hash='.$this->CSRFGetToken('delete')."&aid='+aid;
				}"
			);
		}

			$this->tpl->assign_vars(array(
				'PAGINATION' 	  => generate_pagination($this->server_path.$strPath, $pageCount, 1, $intPageID-1, 'page', 1),
				'ARTICLE_CONTENT' => $strContent,
				'ARTICLE_TITLE'	  => $arrTitles[$intPageID],
				'ARTICLE_SUBMITTED'=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($arrArticle['date'], false, true)),
				'ARTICLE_DATE'	  => $this->time->user_date($arrArticle['date'], false, false, true),
				'S_PAGINATION'	  => ($pageCount > 1) ? true : false,
				'ARTICLE_SOCIAL_BUTTONS'  => ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$strPath, strip_tags($arrArticle['title'])) : '',
				'PERMALINK'		  => $this->pdh->get('articles', 'permalink', array($intArticleID)),
				'BREADCRUMB'	  => $this->pdh->get('articles', 'breadcrumb', array($intArticleID, $strAdditionalTitles)),
				'ARTICLE_RATING'  => ($arrArticle['votes']) ? $this->jquery->StarRating('article_vote', $myRatings,$this->server_path.$strPath,(($arrArticle['votes_count']) ? round($arrArticle['votes_sum'] / $arrArticle['votes_count']): 0), $blnUserHasVoted) : '',
				'ARTICLE_TOOLBAR' => $jqToolbar['id'],
				'S_TOOLBAR'		=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
				'S_TAGS'		=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
				'COMMENTS_COUNTER'	=> ($intCommentsCount == 1 ) ? $intCommentsCount.' '.$this->user->lang('comment') : $intCommentsCount.' '.$this->user->lang('comments'),
				'S_COMMENTS'	=> ($arrArticle['comments']) ? true : false,
				'S_HIDE_HEADER' => ($arrArticle['hide_header']),
			));
			
			$this->tpl->add_meta('<link rel="canonical" href="'.$this->pdh->get('articles', 'permalink', array($intArticleID)).'" />');
			$this->tpl->add_rssfeed($arrCategory['name'], $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''));

			//Comments
			if ($arrArticle['comments'] && $this->config->get('pk_enable_comments') == 1){
				$this->comments->SetVars(array(
					'attach_id'	=> $intArticleID.(($strSpecificID) ? '_'.$strSpecificID : ''),
					'page'		=> 'articles',
					'auth'		=> 'a_articles_man',
				));
				$this->tpl->assign_vars(array(
					'COMMENTS'			=> $this->comments->Show(),
				));
			};
			
			$this->core->set_vars(array(
				'page_title'		=> $arrArticle['title'].$strAdditionalTitles,
				'description'		=> truncate(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($arrContent[$intPageID])))), 600, '...', false, true),
				'image'				=> ($this->pdh->get('articles', 'previewimage', array($intArticleID)) != "") ? $this->pfh->FileLink($this->pdh->get('articles', 'previewimage', array($intArticleID)),'files', 'absolute') : '',
				'template_file'		=> 'article.html',
				'portal_layout'		=> $arrCategory['portal_layout'],
				'display'			=> true)
			);
			
		} elseif ($intCategoryID){		
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			//Check if Published
			$intPublished = $arrCategory['published'];
							
			if (!$intPublished) message_die('Dieser Artikel ist nicht veröffentlicht.');
						
			//User Memberships
			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($this->user->id);
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
						
			if (!$arrPermissions['read']) message_die('Keine Berechtigung, diese Kategorie anzusehen.', $this->user->lang('noauth_default_title'), 'access_denied', true);
			
			$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID));
			switch($arrCategory['sortation_type']){
				case 2: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'asc');
				break;
				case 3: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', 'desc');
				break;
				case 4: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', 'asc');
				break;
				case 1:
				default: $arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'desc');
			}
			
			if ($arrCategory['featured_ontop']){
				$arrSortedArticleIDs = $this->pdh->get('articles', 'id_list_featured_ontop', array($arrSortedArticleIDs));
			}
			
			
			
			$intStart = $this->in->get('start', 0);
			$arrLimitedIDs = $this->pdh->limit($arrSortedArticleIDs, $intStart, $arrCategory['per_page']);
			$strPath = $this->pdh->get('article_categories', 'path', array($intCategoryID));
			registry::add_const('page_path', $this->user->removeSIDfromString($strPath));
			
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
				preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
				if (count($arrRaidlootObjects[0])){
					include_once($this->root_path.'core/gallery.class.php');
					foreach($arrRaidlootObjects[3] as $key=>$val){
						$objGallery = registry::register('gallery');
						$strRaidlootContent = $objGallery->raidloot((int)$val);
						$strText = str_replace($arrRaidlootObjects[0][$key], $strRaidlootContent, $strText);
					}
				}
				
				$arrToolbarItems = array();
				if ($arrPermissions['create']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'icon-plus',
						'js'	=> 'onclick="editArticle(0)"',
						'title'	=> $this->user->lang('add_new_article'),
					); 
				}
				if ($arrPermissions['update']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'icon-edit',
						'js'	=> 'onclick="editArticle('.$intArticleID.')"',
						'title'	=> $this->user->lang('edit_article'),
					); 
				}				
				if ($arrPermissions['delete']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'icon-trash',
						'js'	=> 'onclick="deleteArticle('.$intArticleID.')"',
						'title'	=> $this->user->lang('delete_article'),
					);
				}
				if ($arrPermissions['change_state']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'icon-eye-close',
						'js'	=> 'onclick="window.location=\''.$this->controller_path.$this->page_path.$this->SID.'&unpublish&link_hash='.$this->CSRFGetToken('unpublish').'&aid='.$intArticleID.'\'"',
						'title'	=> $this->user->lang('article_unpublish'),
					);
				}
				
				if ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']){
					$jqToolbar = $this->jquery->toolbar('article_'.$intArticleID, $arrToolbarItems, array('position' => 'bottom'));
				} else {
					$jqToolbar['id'] = '';
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
					'ARTICLE_PATH'		=> $this->server_path.$this->pdh->get('articles',  'path', array($intArticleID)),
					'ARTICLE_SOCIAL_BUTTONS'  => ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->server_path.$this->pdh->get('articles',  'path', array($intArticleID)), strip_tags($this->pdh->get('articles',  'title', array($intArticleID)))) : '',
					'ARTICLE_TOOLBAR' => $jqToolbar['id'],
					'PERMALINK'		=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
					'S_TOOLBAR'			=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
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
			
			
			//Childs
			if ($arrCategory['show_childs']){
				$arrChilds = $this->pdh->get('article_categories', 'childs', array($intCategoryID));
				if (count($arrChilds)){
					$this->tpl->assign_vars(array(
						'S_CHILDS' 	  => true,
					));
					foreach($arrChilds as $intChildID){
						$this->tpl->assign_block_vars('child_row', array(
							'NAME' 	=> $this->pdh->get('article_categories', 'name', array($intChildID)),
							'U_PATH'=> $this->server_path.$this->pdh->get('article_categories', 'path', array($intChildID)),
							'COUNT' => count($this->pdh->get('article_categories', 'published_id_list', array($intChildID))),
							'DESC'  => strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($this->pdh->get('article_categories', 'description', array($intChildID)))))),
						));
					}
				}
			}
			
			
			$arrToolbarItems = array();
			if ($arrPermissions['create']) {
				$arrToolbarItems[] = array(
					'icon'	=> 'icon-plus',
					'js'	=> 'onclick="editArticle(0)"',
					'title'	=> $this->user->lang('add_new_article'),
				); 
			}
			if ($this->user->check_auth('a_articles_man', false)) {
				$arrToolbarItems[] = array(
					'icon'	=> 'icon-edit',
					'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_article_categories.php".$this->SID.'&c='.$intCategoryID.'\';"',
					'title'	=> $this->user->lang('edit_article_category'),
				);
				$arrToolbarItems[] = array(
					'icon'	=> 'icon-list',
					'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_articles.php".$this->SID.'&c='.$intCategoryID.'\';"',
					'title'	=> $this->user->lang('list_articles'),
				);
				
			}

			$jqToolbar = $this->jquery->toolbar('pages', $arrToolbarItems, array('position' => 'bottom'));
			
			if ($arrPermissions['create'] || $arrPermissions['update']) {
				$this->jquery->dialog('editArticle', $this->user->lang('edit_article'), array('url' => $this->controller_path."EditArticle/".$this->SID."&aid='+id+'&cid=".$intCategoryID, 'withid' => 'id', 'width' => 920, 'height' => 740, 'onclose'=> $this->env->link.$this->controller_path_plain.$this->page_path));
			}
			if ($arrPermissions['delete'] || $arrPermissions['change_state']){
				$this->jquery->dialog('deleteArticle', $this->user->lang('delete_article'), array('custom_js' => 'deleteArticleSubmit(aid);', 'confirm', 'withid' => 'aid', 'message' => $this->user->lang('delete_article_confirm')), 'confirm');
				$this->tpl->add_js(
					"function deleteArticleSubmit(aid){
						window.location='".$this->controller_path.$this->page_path.$this->SID.'&delete&link_hash='.$this->CSRFGetToken('delete')."&aid='+aid;
					}"
				);
			}
			
			$this->tpl->assign_vars(array(
				'PAGINATION' 	  		=> generate_pagination($this->server_path.$strPath, count($arrSortedArticleIDs), $arrCategory['per_page'], $intStart, 'start'),
				'CATEGORY_DESCRIPTION' => $this->bbcode->parse_shorttags(xhtml_entity_decode($arrCategory['description'])),
				'CATEGORY_NAME'		   => $arrCategory['name'],
				'PERMALINK'		  	=> $this->pdh->get('article_categories', 'permalink', array($intCategoryID)),
				'RSSLINK'			=> $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''),
				'BREADCRUMB'	  	=> ($this->pdh->get('article_categories', 'parent', array($intCategoryID)) > 1) ? $this->pdh->get('article_categories', 'breadcrumb', array($intCategoryID)) : '',
				'ARTICLE_TOOLBAR' 	=> $jqToolbar['id'],
				'S_TOOLBAR'			=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
				'LIST_TYPE'		  	=> $arrCategory['list_type'],
				'S_HIDE_HEADER'		=> $arrCategory['hide_header'],
			));
			
			$this->tpl->add_rssfeed($arrCategory['name'], $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''));
			$this->tpl->add_meta('<link rel="canonical" href="'.$this->pdh->get('article_categories', 'permalink', array($intCategoryID)).'" />');
			
			$this->core->set_vars(array(
				'page_title'		=> $arrCategory['name'],
				'description'		=> truncate(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($arrCategory['description'])))), 600, '...', false, true),
				'image'				=> '',
				'template_file'		=> 'category.html',
				'portal_layout'		=> $arrCategory['portal_layout'],
				'display'			=> true)
			);
		} elseif (register('routing')->staticRoute($arrPath[0]) || register('routing')->staticRoute($arrPath[1])){
			//Static Page Object
			$strPageObject = register('routing')->staticRoute($arrPath[0]);
			if (!$strPageObject) {			
				$strPageObject = register('routing')->staticRoute($arrPath[1]);
				if ($strPageObject){
					//Zerlege .html
					$strID = str_replace("-", "", strrchr($arrPath[0], "-"));
					$arrMatches = array();
					preg_match_all('/[a-z]+|[0-9]+/', $strID, $arrMatches, PREG_PATTERN_ORDER);
					if (isset($arrMatches[0]) && count($arrMatches[0])){
						if (count($arrMatches[0]) == 2){
							if(is_numeric($arrMatches[0][1])) $arrMatches[0][1] = intval($arrMatches[0][1]);
							$this->in->inject($arrMatches[0][0], $arrMatches[0][1]);
						}
					}
					if (strlen($strID)) {
						if(is_numeric($strID)) $strID = intval($strID);
						registry::add_const('url_id', $strID);
					} elseif (strlen($arrPath[0])){
						registry::add_const('url_id', $arrPath[0]);
						$this->in->inject(utf8_strtolower($arrPath[0]), 'injected');
					}
					registry::add_const('page', str_replace('/'.$arrPath[0], '', $strPath));
					registry::add_const('page_path', $strPath);
					registry::add_const('speaking_name', str_replace('-'.$strID, '', $arrPath[0]));
				}
			} else {
				registry::add_const('page_path', $strPath);
				registry::add_const('page', $strPath);
			}
			if ($strPageObject){
				$objPage = $this->routing->getPageObject($strPageObject);
				if ($objPage){
					$arrVars = $objPage->get_vars();
					$this->core->set_vars(array(
						'page_title'		=> $arrVars['page_title'],
						'template_file'		=> $arrVars['template_file'],
						'display'			=> true)
					);					
				} else {
					redirect();
				}									
			}		 
		} else {
			message_die('Konnte Artikel bzw. Kategorie nicht finden.');
		}
	}
	
	protected function CSRFGetToken($strProcess){
		$strAction = get_class($this).$strProcess;
		return $this->user->csrfGetToken($strAction);
	}
}
registry::register('controller');
?>