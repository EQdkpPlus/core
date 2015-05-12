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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class controller extends gen_class {
	public static $shortcuts = array('social' => 'socialplugins');
	
	public function __construct() {
		$blnCheckPost = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken()));
		$blnCheckPostOld = $this->user->checkCsrfPostToken($this->in->get($this->user->csrfPostToken(true)));

		if ($this->in->exists('delete') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'delete'))){
			$this->delete();
		}
		if ($this->in->exists('unpublish') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'unpublish'))){
			$this->unpublish();
		}
		if ($this->in->exists('publish') && ($blnCheckPost || $blnCheckPostOld || $this->user->checkCsrfGetToken($this->in->get('link_hash'), get_class($this).'publish'))){
			$this->publish();
		}
		if ($this->in->exists('savevote')){
			$this->saveRating();
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
	
	public function publish(){
		$intArticleID = $this->in->get('aid', 0);
		$intCategoryID = $this->pdh->get('articles','category', array($intArticleID));
		if ($intCategoryID){
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
			if ($arrPermissions && $arrPermissions['change_state']){
				$strArticleTitle = $this->pdh->get('articles', 'title', array($intArticleID));
				$this->pdh->put('articles', 'set_published', array(array($intArticleID)));
	
				$this->core->message($strArticleTitle, $this->user->lang('article_publish_success'), 'green');
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

	public function saveRating(){
		$this->pdh->put('articles', 'vote', array($this->in->get('name'), $this->in->get('score')));
		$this->pdh->process_hook_queue();
		die('done');
	}

	public function display(){
		$strPath = $this->env->path;
		$arrPath = array_filter(explode('/', $strPath));
		$arrPath = array_reverse($arrPath);
		$arrPath = $this->filterPathArray($arrPath);
		//Required, otherwise the Routing of Plugin URLS wont work.
		register('pm');
		
		if (count($arrPath) == 0 || str_ireplace('index.php', '', $strPath) === $this->config->get('server_path')){
			//Get Start Page
			if ($this->config->get('start_page') != ""){
				$strPath = $this->config->get('start_page');
			} else {
				$strPath = "news";
			}
			$arrPath = array_filter(explode('/', $strPath));
			$arrPath = array_reverse($arrPath);
		}
		registry::add_const('patharray', $arrPath);
		$intArticleID = $intCategoryID = $strSpecificID = 0;

		//Suche Alias in Artikeln
		$intArticleID = ($this->in->exists('a')) ? $this->in->get('a', 0) : $this->pdh->get('articles', 'resolve_alias', array($arrPath[0]));
		
		if (!$intArticleID){
			//Suche Alias in Kategorien
			$intCategoryID = ($this->in->exists('c')) ? $this->in->get('c', 0) : $this->pdh->get('article_categories', 'resolve_alias', array($arrPath[0]));
			//Is there an index-Article in this Category?
			if ($intCategoryID){
				$intIndexArticle = $this->pdh->get('article_categories', 'index_article', array($intCategoryID));
				if ($intIndexArticle) {
					$intArticleID = $intIndexArticle;
					$intCategoryID = false;
				}
			}
			
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
				} else {
					$intCategoryID = $this->pdh->get('article_categories', 'resolve_alias', array($arrPath[1]));
					if ($intCategoryID){
						$intIndexArticle = $this->pdh->get('article_categories', 'index_article', array($intCategoryID));
						if ($intIndexArticle) {
							$intArticleID = $intIndexArticle;
							$intCategoryID = false;
							
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
			if (($arrArticle['show_from'] != "" && $arrArticle['show_from'] > $this->time->time) || ($arrArticle['show_to'] != "" && $arrArticle['show_to'] < $this->time->time)) $intPublished = false;
			
			//Get Category Data
			$intCategoryID = $arrArticle['category'];
			registry::add_const('categoryid', $intCategoryID);
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			
			//Category Permissions
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($arrArticle['category'], $this->user->id));
			if (!$arrPermissions['read']) message_die($this->user->lang('article_noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);
			
			//Check Start to/start from
			if ((!$intPublished && (!$arrPermissions['update'] && !$arrPermissions['change_state'])) || !$arrCategory['published']) message_die($this->user->lang('article_unpublished'));
			
			registry::add_const('page_path', $strPath);
			$strPath = ucfirst($this->pdh->get('articles', 'path', array($intArticleID)));
			registry::add_const('page', $this->user->removeSIDfromString($strPath));
			
			//User Memberships
			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($this->user->id);
			

			//Page divisions
			$strText = xhtml_entity_decode($arrArticle['text']);
			$arrPagebreaks = array();
			preg_match_all('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText, $arrPagebreaks, PREG_PATTERN_ORDER);

			if (count($arrPagebreaks[0])){
				$arrTitles[1] = $arrArticle['title'];
				foreach($arrPagebreaks[2] as $key=>$val){
					$titleMatches		= array();
					$intMatches			= preg_match('#title="(.*)"#iU', $val, $titleMatches);
					$arrTitles[$key+2]	= ($intMatches && $titleMatches[1] != '' ) ? $titleMatches[1] : 'Page '.$key+2;
				}
				$arrContent = preg_split('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText);

				array_unshift($arrContent, "");
				
			} else {
				$arrContent[0]	= "";
				$arrContent[1]	= $strText;
				$arrTitles[1]	= $arrArticle['title'];
			}
			
			//Page
			$pageCount = count($arrContent) - 1;
			$intPageID = ($this->in->get('page', 0) && isset($arrContent[$this->in->get('page', 0)])) ? $this->in->get('page', 0) : 1;
			
			//Bring Page Sitemap to Template
			if ($pageCount > 1) {
				foreach($arrTitles as $key => $val){
					$this->tpl->assign_block_vars('articlesitemap_row', array(
						'LINK'		=> '<a href="'.$this->controller_path.$strPath.'&amp;page='.$key.'">'.$val.'</a>',
						'ACTIVE'	=> ($key == $intPageID),
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
					'U_NEXT_ARTICLE'	=> ($nextID) ? $this->controller_path.$this->pdh->get('articles', 'path', array($nextID)) : '',
					'U_PREV_ARTICLE'	=> ($prevID) ? $this->controller_path.$this->pdh->get('articles', 'path', array($prevID)) : '',
					'NEXT_TITLE'		=> ($nextID) ? $this->pdh->get('articles', 'title', array($nextID)) : '',
					'PREV_TITLE'		=> ($prevID) ? $this->pdh->get('articles', 'title', array($prevID)) : '',
				));
			}
		
			
			$userlink = '<a href="'.$this->routing->build('user', $this->pdh->geth('articles',  'user_id', array($intArticleID)), 'u'.$this->pdh->get('articles',  'user_id', array($intArticleID))).'">'.$this->pdh->geth('articles',  'user_id', array($intArticleID)).'</a>';
		
		$arrToolbarItems = array();
		if ($arrPermissions['create']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'fa-plus',
				'js'	=> 'onclick="editArticle(0)"',
				'title'	=> $this->user->lang('add_new_article'),
			); 
		}
		if ($arrPermissions['update']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'fa-pencil-square-o',
				'js'	=> 'onclick="editArticle('.$intArticleID.')"',
				'title'	=> $this->user->lang('edit_article'),
			);
			
			$editor = register('tinyMCE');
			
			//Init Inline Editor
			$editor->inline_editor_simple('.headline_inlineedit', array(
				'setup' => 'editor.on("blur", function(e) {
				            	save_inline_editor_simple(".headline_inlineedit", e);
				        	});',
				'autofocus' => true,
				'start_onload' => false,
			));
			
			
			$csrf = $this->user->csrfGetToken('editarticle_pageobjectsave_headline');
			$csrf_raw = $this->user->csrfGetToken('editarticle_pageobjectgetrawarticle');
			$csrf_article = $this->user->csrfGetToken('editarticle_pageobjectsave_article');
			
			$this->tpl->add_js('function save_inline_editor_simple(selector, e){
				if (e.target.editorManager.activeEditor.isDirty() == false) return true;	
					
				var newHeadline = e.target.editorManager.activeEditor.getContent();
					$.post( "'.$this->controller_path.'EditArticle/'.$this->SID.'&save_headline=1&aid='.$intArticleID.'&link_hash='.$csrf.'", 
					{ headline: newHeadline }, function( data ) {
						if (data.status != undefined && data.status == true){
							$("#notify_container").notify("create", "success", {text: '.$this->tpl->handleModifier($this->user->lang('success_create_article'), 'jsencode').',title: '.$this->tpl->handleModifier($this->user->lang('success'), 'jsencode').',custom: true,},{expires: 3000, speed: 1000});
						}
					}, "json");
			}

			var InlineLoaded = new Array();
			function focus_inline_editor(selector, e){
				if (InlineLoaded[selector] == undefined){	
					
				e.target.editorManager.activeEditor.setContent("<b>Loading... <i class=\"fa fa-spinner fa-spin fa-lg\"></i></b><br /><br />", {format: "raw"});
						
				$.get( "'.$this->controller_path.'EditArticle/'.$this->SID.'&get_raw_article=1&aid='.$intArticleID.'&link_hash='.$csrf_raw.'", 
					function( data ) {
						if (data.text != undefined && data.text != false){
							e.target.editorManager.activeEditor.setContent(data.text,  {format: "raw"});
							InlineLoaded[selector] = 1;
						}
					}, "json");
				}
			}		
			
			function save_inline_editor(selector, e){
				if (e.target.editorManager.activeEditor.isDirty() == false) return true;

				var newText = e.target.editorManager.activeEditor.getContent();
					$.post( "'.$this->controller_path.'EditArticle/'.$this->SID.'&save_article=1&aid='.$intArticleID.'&link_hash='.$csrf_article.'", 
					{ text: newText }, function( data ) {
						if (data.status != undefined && data.status == true){
							$("#notify_container").notify("create", "success", {text: '.$this->tpl->handleModifier($this->user->lang('success_create_article'), 'jsencode').',title: '.$this->tpl->handleModifier($this->user->lang('success'), 'jsencode').',custom: true,},{expires: 3000, speed: 1000});
						}
					}, "json");
			}
					


			$(".headline_inlineedit").on("dblclick", function(){
					tinyinlinesimple_21c3d11533bdc5e57418db4d323adbf5();
					$(".headline_inlineedit").off("dblclick");
			})
			$(".article-inlineedit").on("dblclick", function(){
					tinyinline_70f6da87ee4b4befde3c0e12de677bcc();
					$(".article-inlineedit").off("dblclick");
			})
			', 'docready');

			$editor->inline_editor('.article-inlineedit',array(
				'relative_urls'	=> false,
				'link_list'		=> true,
				'gallery'		=> true,
				'raidloot'		=> true,
				'setup'			=> 'editor.on("blur", function(e) {
							save_inline_editor(".article-inlineedit", e);
			        	});
					
					 	editor.on("focus", function(e) {
							focus_inline_editor(".article-inlineedit", e);
       					});',
				'start_onload'	=> false,
				'autofocus'		=> true,
				'autoresize'	=> true,
			),false);
			
			$this->tpl->assign_vars(array(
				'S_INLINE_EDIT' => true,
			));
		}
		if ($arrPermissions['delete']) {
			$arrToolbarItems[] = array(
				'icon'	=> 'fa-trash-o',
				'js'	=> 'onclick="deleteArticle('.$intArticleID.')"',
				'title'	=> $this->user->lang('delete_article'),
			);
		}
		if ($arrPermissions['change_state']) {
			if ($intPublished){
				$arrToolbarItems[] = array(
					'icon'	=> 'fa-eye-slash',
					'js'	=> 'onclick="window.location=\''.$this->env->link.$this->controller_path_plain.$this->page_path.$this->SID.'&unpublish&link_hash='.$this->CSRFGetToken('unpublish').'&aid='.$intArticleID.'\'"',
					'title'	=> $this->user->lang('article_unpublish'),
				);
			} else {
				$arrToolbarItems[] = array(
						'icon'	=> 'fa-eye',
						'js'	=> 'onclick="window.location=\''.$this->env->link.$this->controller_path_plain.$this->page_path.$this->SID.'&publish&link_hash='.$this->CSRFGetToken('publish').'&aid='.$intArticleID.'\'"',
						'title'	=> $this->user->lang('article_publish'),
				);
			}
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

		$this->comments->SetVars(array(
				'attach_id'	=> $intArticleID.(($strSpecificID) ? '_'.$strSpecificID : ''),
				'page'		=> 'articles',
				'auth'		=> 'a_articles_man',
				'ntfy_type' => 'comment_new_article',
				'ntfy_title'=> $arrArticle['title'],
				'ntfy_link' => $this->controller_path_plain.$this->page_path.$this->SID,
				'ntfy_category' => $intCategoryID,
		));
		
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
				if (!is_file($this->root_path.'core/pageobjects/'.$val.'_pageobject.class.php')) continue;
				include_once($this->root_path.'core/pageobjects/'.$val.'_pageobject.class.php');
				$objPage = registry::register($val.'_pageobject');
				$arrCoreVars = $objPage->get_vars();
				if ($arrCoreVars['template_file'] != '' ) {
					$strContent = str_replace($strHaystack, '<!-- INCLUDE '.$arrCoreVars['template_file'].' --><br />', $strContent);
				} else {
					$strContent = str_replace($strHaystack, '', $strContent);
				}
				if (isset($arrCoreVars['page_title']) && strlen($arrCoreVars['page_title'])) $strAdditionalTitles = ' - '.$arrCoreVars['page_title'];
			}
			$this->tpl->assign_var('S_INLINE_EDIT', false);
		}
		
		//Hook to replace content
		if ($this->hooks->isRegistered('article_parse')){
			$arrHooks = $this->hooks->process('article_parse', array('content' => $strContent, 'view' => 'article', 'article_id' => $intArticleID, 'specific_id' => $strSpecificID), true);
			if (isset($arrHooks['content'])) $strContent = $arrHooks['content'];
		}
		
		
		//Replace Image Gallery
		$arrGalleryObjects = array();
		preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strContent, $arrGalleryObjects, PREG_PATTERN_ORDER);
		if (count($arrGalleryObjects[0])){
			include_once($this->root_path.'core/gallery.class.php');
			foreach($arrGalleryObjects[4] as $key=>$val){
				$objGallery = registry::register('gallery');
				$strGalleryContent = $objGallery->create($val, (int)$arrGalleryObjects[3][$key], $this->controller_path.$strPath, $intPageID);
				$strContent = str_replace($arrGalleryObjects[0][$key], $strGalleryContent, $strContent);
			}
		}
		
		//Replace Raidloot
		$arrRaidlootObjects = array();

		preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strContent, $arrRaidlootObjects, PREG_PATTERN_ORDER);

		if (count($arrRaidlootObjects[0])){
			include_once($this->root_path.'core/gallery.class.php');
			foreach($arrRaidlootObjects[3] as $key=>$val){
				$objGallery = registry::register('gallery');
				$withChars = ($arrRaidlootObjects[5][$key] == "true") ? true : false;
				$strRaidlootContent = $objGallery->raidloot((int)$val, $withChars);
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
				'ARTICLE_ID'		=> $intArticleID,	
				'PAGINATION'		=> generate_pagination($this->controller_path.$strPath, $pageCount, 1, $intPageID-1, 'page', 1),
				'ARTICLE_CONTENT'	=> $strContent,
				'ARTICLE_TITLE'		=> $arrTitles[$intPageID],
				'ARTICLE_SUBMITTED'	=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($arrArticle['date'], false, true)),
				'ARTICLE_DATE'		=> $this->time->user_date($arrArticle['date'], false, false, true),
				'ARTICLE_TIMETAG'	=> $this->time->createTimeTag($arrArticle['date'], $this->time->user_date($arrArticle['date'], false, false, true).', '.$this->time->user_date($arrArticle['date'], false, true)),
				'ARTICLE_AUTHOR'	=> $userlink,
				'ARTICLE_PUBLISHED' => ($intPublished) ? true : false,
				'ARTICLE_TIME'		=> $this->time->user_date($arrArticle['date'], false, true),
				'ARTICLE_REAL_CATEGORY' => $this->pdh->get('articles',  'category', array($intArticleID)),
				'S_PAGINATION'		=> ($pageCount > 1) ? true : false,
				'ARTICLE_SOCIAL_BUTTONS'  => ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$this->controller_path_plain.$strPath, strip_tags($arrArticle['title'])) : '',
				'PERMALINK'			=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
				'BREADCRUMB'		=> $this->pdh->get('articles', 'breadcrumb', array($intArticleID, $strAdditionalTitles, registry::get_const('url_id'), $arrPath)),
				'ARTICLE_RATING'	=> ($arrArticle['votes']) ? $this->jquery->starrating($intArticleID, $this->controller_path.$strPath.'&savevote&link_hash='.$this->CSRFGetToken('savevote'), array('score' => (($arrArticle['votes_count']) ? round($arrArticle['votes_sum'] / $arrArticle['votes_count']): 0), 'number' => 10)) : '',
				//'ARTICLE_RATING'  => ($arrArticle['votes']) ? $this->jquery->StarRating('article_vote', $myRatings,$this->server_path.$strPath,(($arrArticle['votes_count']) ? round($arrArticle['votes_sum'] / $arrArticle['votes_count']): 0), $blnUserHasVoted) : '',
				'ARTICLE_TOOLBAR'	=> $jqToolbar['id'],
				'S_TOOLBAR'			=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
				'S_TAGS'			=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
				'COMMENTS_COUNTER'	=> ($intCommentsCount == 1 ) ? $intCommentsCount.' '.$this->user->lang('comment') : $intCommentsCount.' '.$this->user->lang('comments'),
				'S_COMMENTS'		=> ($arrArticle['comments']) ? true : false,
				'S_HIDE_HEADER'		=> ($arrArticle['hide_header']),
				'S_FEATURED'		=> ($this->pdh->get('articles',  'featured', array($intArticleID))),
			));
			
			$this->tpl->add_meta('<link rel="canonical" href="'.$this->pdh->get('articles', 'permalink', array($intArticleID)).'" />');
			$this->tpl->add_rssfeed($arrCategory['name'], $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''));

			//Comments
			if ($arrArticle['comments'] && $this->config->get('enable_comments') == 1){
				$this->comments->SetVars(array(
					'ntfy_title'	=> $arrArticle['title'].$strAdditionalTitles,
				));
				$this->tpl->assign_vars(array(
					'COMMENTS'		=> $this->comments->Show(),
				));
			};
			
			$strPreviewImage = ($this->pdh->get('articles',  'previewimage', array($intArticleID)) != "") ? $this->pdh->geth('articles', 'previewimage', array($intArticleID)) : '';
			if(!strlen($strPreviewImage)) $strPreviewImage = $this->social->getFirstImage($strContent);

			$this->core->set_vars(array(
				'page_title'		=> $arrArticle['title'].$strAdditionalTitles,
				'description'		=> truncate(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($arrContent[$intPageID])))), 600, '...', false, true),
				'image'				=> $strPreviewImage,
				'template_file'		=> 'article.html',
				'portal_layout'		=> $arrCategory['portal_layout'],
				'display'			=> true)
			);

		} elseif ($intCategoryID){
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			//Check if Published
			$intPublished = $arrCategory['published'];

			if (!$intPublished) message_die($this->user->lang('category_unpublished'));
			
			registry::add_const('categoryid', $intCategoryID);

			//User Memberships
			$arrUsergroupMemberships = $this->acl->get_user_group_memberships($this->user->id);
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));

			if (!$arrPermissions['read']) message_die($this->user->lang('category_noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);

			$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, false, false, NULL, (($arrPermissions['change_state']) ? true : false)));
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
				$blnPublished = $this->pdh->get('articles', 'published', array($intArticleID));
				
				//Hook to replace content
				if ($this->hooks->isRegistered('article_parse')){
					$arrHooks = $this->hooks->process('article_parse', array('content' => $strText, 'view' => 'category', 'article_id' => $intArticleID, 'specific_id' => $strSpecificID), true);
					if (isset($arrHooks['content'])) $strText = $arrHooks['content'];
				}
				
				//Replace Image Gallery
				$arrGalleryObjects = array();
				preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strText, $arrGalleryObjects, PREG_PATTERN_ORDER);

				if (count($arrGalleryObjects[0])){
					include_once($this->root_path.'core/gallery.class.php');
					foreach($arrGalleryObjects[4] as $key=>$val){
						$objGallery = registry::register('gallery');
						$strGalleryContent = $objGallery->create($val, (int)$arrGalleryObjects[3][$key], $this->controller_path.$strPath, 1);
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
				
				$arrToolbarItems = array();
				if ($arrPermissions['create']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'fa-plus',
						'js'	=> 'onclick="editArticle(0)"',
						'title'	=> $this->user->lang('add_new_article'),
					); 
				}
				if ($arrPermissions['update']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'fa fa-pencil-square-o',
						'js'	=> 'onclick="editArticle('.$intArticleID.')"',
						'title'	=> $this->user->lang('edit_article'),
					); 
				}				
				if ($arrPermissions['delete']) {
					$arrToolbarItems[] = array(
						'icon'	=> 'fa-trash-o',
						'js'	=> 'onclick="deleteArticle('.$intArticleID.')"',
						'title'	=> $this->user->lang('delete_article'),
					);
				}
				if ($arrPermissions['change_state']) {
					if ($blnPublished){
						$arrToolbarItems[] = array(
							'icon'	=> 'fa-eye-slash',
							'js'	=> 'onclick="window.location=\''.$this->env->link.$this->controller_path_plain.$this->page_path.$this->SID.'&unpublish&link_hash='.$this->CSRFGetToken('unpublish').'&aid='.$intArticleID.'\'"',
							'title'	=> $this->user->lang('article_unpublish'),
						);
					} else {
						$arrToolbarItems[] = array(
								'icon'	=> 'fa-eye',
								'js'	=> 'onclick="window.location=\''.$this->env->link.$this->controller_path_plain.$this->page_path.$this->SID.'&publish&link_hash='.$this->CSRFGetToken('publish').'&aid='.$intArticleID.'\'"',
								'title'	=> $this->user->lang('article_publish'),
						);
					}
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
					'ARTICLE_ID'			=> $intArticleID,
					'ARTICLE_CONTENT'		=> $strText,
					'ARTICLE_TITLE'			=> $this->pdh->get('articles',  'title', array($intArticleID)),
					'ARTICLE_SUBMITTED'		=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true)),
					'ARTICLE_DATE'			=> $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, false, true),	
					'ARTICLE_TS'			=> $this->pdh->get('articles', 'date', array($intArticleID)),
					'ARTICLE_TIMETAG'		=> $this->time->createTimeTag($this->pdh->get('articles', 'date', array($intArticleID)), $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, false, true).', '.$this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true)),
					'ARTICLE_AUTHOR'		=> $userlink,
					'ARTICLE_TIME'			=> $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true),
					'ARTICLE_PATH'			=> $this->controller_path.$this->pdh->get('articles',  'path', array($intArticleID)),
					'ARTICLE_SOCIAL_BUTTONS'=> ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$this->controller_path_plain.$this->pdh->get('articles',  'path', array($intArticleID)), strip_tags($this->pdh->get('articles',  'title', array($intArticleID)))) : '',
					'ARTICLE_TOOLBAR'		=> $jqToolbar['id'],
					'ARTICLE_PUBLISHED'		=> ($blnPublished) ? true : false,
					'ARTICLE_REAL_CATEGORY' => $this->pdh->get('articles',  'category', array($intArticleID)),
					'ARTICLE_PREVIEW_IMAGE' => ($this->pdh->get('articles',  'previewimage', array($intArticleID)) != "") ? $this->pdh->geth('articles', 'previewimage', array($intArticleID)) : '',
					'PERMALINK'				=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
					'S_TOOLBAR'				=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
					'S_TAGS'				=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
					'ARTICLE_CUTTED_CONTENT'=> truncate($strText, 600, '...', false, true),
					'S_READMORE'			=> (isset($arrContent[1])) ? true : false,
					'COMMENTS_COUNTER'		=> ($intCommentsCount == 1 ) ? $intCommentsCount.' '.$this->user->lang('comment') : $intCommentsCount.' '.$this->user->lang('comments'),
					'S_COMMENTS'			=> ($this->pdh->get('articles',  'comments', array($intArticleID))) ? true : false,
					'S_FEATURED'			=> ($this->pdh->get('articles',  'featured', array($intArticleID))),
					'S_HIDE_HEADER'			=> ($this->pdh->get('articles',  'hide_header', array($intArticleID))),
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
						'S_CHILDS'	=> true,
					));
					foreach($arrChilds as $intChildID){
						if(!$this->pdh->get('article_categories', 'published', array($intChildID))) continue;
						
						$this->tpl->assign_block_vars('child_row', array(
							'NAME'	=> $this->pdh->get('article_categories', 'name', array($intChildID)),
							'U_PATH'=> $this->controller_path.$this->pdh->get('article_categories', 'path', array($intChildID)),
							'COUNT'	=> count($this->pdh->get('article_categories', 'published_id_list', array($intChildID))),
							'DESC'	=> strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($this->pdh->get('article_categories', 'description', array($intChildID)))))),
						));
					}
				}
			}
			
			
			$arrToolbarItems = array();
			if ($arrPermissions['create']) {
				$arrToolbarItems[] = array(
					'icon'	=> 'fa-plus',
					'js'	=> 'onclick="editArticle(0)"',
					'title'	=> $this->user->lang('add_new_article'),
				); 
			}
			if ($this->user->check_auth('a_articles_man', false)) {
				$arrToolbarItems[] = array(
					'icon'	=> 'fa fa-pencil-square-o',
					'js'	=> 'onclick="window.location=\''.$this->server_path."admin/manage_article_categories.php".$this->SID.'&c='.$intCategoryID.'\';"',
					'title'	=> $this->user->lang('edit_article_category'),
				);
				$arrToolbarItems[] = array(
					'icon'	=> 'fa-list',
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
				'PAGINATION'			=> generate_pagination($this->controller_path.$strPath, count($arrSortedArticleIDs), $arrCategory['per_page'], $intStart, 'start'),
				'CATEGORY_DESCRIPTION'	=> $this->bbcode->parse_shorttags(xhtml_entity_decode($arrCategory['description'])),
				'CATEGORY_NAME'			=> $arrCategory['name'],
				'PERMALINK'				=> $this->pdh->get('article_categories', 'permalink', array($intCategoryID)),
				'RSSLINK'				=> $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''),
				'BREADCRUMB'			=> ($this->pdh->get('article_categories', 'parent', array($intCategoryID)) > 1) ? $this->pdh->get('article_categories', 'breadcrumb', array($intCategoryID)) : '',
				'ARTICLE_TOOLBAR'		=> $jqToolbar['id'],
				'S_TOOLBAR'				=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
				'LIST_TYPE'				=> $arrCategory['list_type'],
				'S_HIDE_HEADER'			=> $arrCategory['hide_header'],
				'CATEGORY_ID'			=> $intCategoryID,
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
		} else {
			$strPageObject = false;
			foreach($arrPath as $intPathPart => $strPathPart){	
				if (register('routing')->staticRoute($strPathPart)){
					if ($intPathPart == 0){

						$strPageObject = register('routing')->staticRoute($strPathPart);
						registry::add_const('page_path', $strPath);
						registry::add_const('page', $strPath);
					} else {
					
						//Static Page Object
						$strPageObject = register('routing')->staticRoute($arrPath[$intPathPart]);

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
							break;
						}
					}
				}
			}
			
			if ($strPageObject){
				$this->core->set_vars('portal_layout', 1);
				$objPage = $this->routing->getPageObject($strPageObject);
				if ($objPage){
					$arrVars = $objPage->get_vars();
					$this->core->set_vars(array(
							'page_title'		=> $arrVars['page_title'],
							'template_file'		=> $arrVars['template_file'],
							'portal_layout'		=> 1,
							'display'			=> true)
					);
				} else {
					redirect();
				}
			} else {
				if(!$this->env->is_ajax) message_die($this->user->lang('article_not_found'));
			}
		}

	}
	
	protected function CSRFGetToken($strProcess){
		$strAction = get_class($this).$strProcess;
		return $this->user->csrfGetToken($strAction);
	}
}
registry::register('controller');
?>