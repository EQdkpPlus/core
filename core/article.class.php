<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('article')){
	class article extends gen_class {
		public static $shortcuts = array('social' => 'socialplugins');

		private $_cache = array();

		public function showCategory($intCategoryID, $strSpecificID, $blnIsStartpage=false){
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			//Check if Published
			$intPublished = $arrCategory['published'];

			$intCurrentUserID = (isset($this->user->data['session_perm_id']) && $this->user->data['session_perm_id'] > 0) ? intval($this->user->data['session_perm_id']) : $this->user->id;

			if (!$intPublished) message_die($this->user->lang('category_unpublished'));

			registry::add_const('categoryid', $intCategoryID);

			//User Memberships
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $intCurrentUserID));

			if (!$arrPermissions['read']) message_die($this->user->lang('category_noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);

			$arrArticleIDs = $this->pdh->get('article_categories', 'published_id_list', array($intCategoryID, false, false, NULL, (($arrPermissions['change_state']) ? true : false)));
			switch($arrCategory['sortation_type']){
				case 2: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'asc');
					break;

				case 3: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', 'desc');
					break;

				case 4: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'last_edited', 'asc');
					break;

				case 5: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'alias', 'desc');
					break;

				case 6: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'alias', 'asc');
					break;

				case 7: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'title', 'desc');
					break;

				case 8: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'title', 'asc');
					break;

				case 1:
				default: 
					$arrSortedArticleIDs = $this->pdh->sort($arrArticleIDs, 'articles', 'date', 'desc');
			}

			if ($arrCategory['featured_ontop']){
				$arrSortedArticleIDs = $this->pdh->get('articles', 'id_list_featured_ontop', array($arrSortedArticleIDs));
			}

			$intStart = $this->in->get('start', 0);
			$arrLimitedIDs = $this->pdh->limit($arrSortedArticleIDs, $intStart, $arrCategory['per_page']);
			$strPath = $this->pdh->get('article_categories', 'path', array($intCategoryID));
			registry::add_const('page_path', $this->user->removeSIDfromString($strPath));
			registry::add_const('pageobject', 'articlecategory');

			$arrCategory['name'] = $this->user->multilangValue($arrCategory['name']);

			infotooltip_js();

			//Articles to template
			foreach($arrLimitedIDs as $intArticleID){
				$userlink = $this->pdh->geth('user', 'name', array($this->pdh->get('articles',  'user_id', array($intArticleID)), '', '', true));

				//Content dependet from list_type
				//1 = until readmore
				//2 = Headlines only
				//3 = only first 600 characters
				$strText = $this->pdh->get('articles',  'text', array($intArticleID));
				
				$arrContent = array();

				//Page divisions
				$strText = xhtml_entity_decode($strText);
				$arrPagebreaks = array();
				preg_match_all('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText, $arrPagebreaks, PREG_PATTERN_ORDER);

				if (count($arrPagebreaks[0])){
					$arrContent = preg_split('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText);
					array_unshift($arrContent, "");
				} else {
					$arrContent[0]	= "";
					$arrContent[1]	= $strText;
				}

				$strText = $arrContent[1];
				
				$arrReadmore = preg_split('#<hr(.*)id="system-readmore"(.*)\/>#iU', $strText);
				if(count($arrReadmore) > 1) $strText = $arrReadmore[0];

				$intTextLength = strlen($strText);			

				$blnPublished = $this->pdh->get('articles', 'published', array($intArticleID));

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

				$strPreviewImage = ($this->pdh->get('articles',  'previewimage', array($intArticleID)) != "") ? $this->pdh->geth('articles', 'previewimage', array($intArticleID)) : '';

				switch((int)$arrCategory['list_type']){
					case 3 : $strText = truncate($strText, 600, '...', false, true);
					break;

					case 4 : $strText = truncate(strip_tags($strText), 320, '...', false, true);
					break;
				}

				$this->tpl->assign_block_vars('article_row', array(
						'ARTICLE_ID'			=> $intArticleID,
						'ARTICLE_CONTENT'		=> $this->formatArticleOutput($strText, $intArticleID, $strSpecificID),
						'ARTICLE_TITLE'			=> $this->pdh->get('articles',  'title', array($intArticleID)),
						'ARTICLE_SUBMITTED'		=> sprintf($this->user->lang('news_submitter'), $userlink, $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true)),
						'ARTICLE_DATE'			=> $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, false, true),
						'ARTICLE_TS'			=> $this->pdh->get('articles', 'date', array($intArticleID)),
						'ARTICLE_TIMETAG'		=> $this->time->createTimeTag($this->pdh->get('articles', 'date', array($intArticleID)), $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, false, true).', '.$this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true)),
						'ARTICLE_AUTHOR'		=> $userlink,
						'ARTICLE_AUTHOR_AVATAR' => $this->pdh->geth('user', 'avatarimglink', array($this->pdh->get('articles',  'user_id', array($intArticleID)))),
						'ARTICLE_TIME'			=> $this->time->user_date($this->pdh->get('articles', 'date', array($intArticleID)), false, true),
						'ARTILE_DATE_DAY'		=> $this->time->date('d', $this->pdh->get('articles', 'date', array($intArticleID))),
						'ARTILE_DATE_MONTH'		=> $this->time->date('F', $this->pdh->get('articles', 'date', array($intArticleID))),
						'ARTILE_DATE_YEAR'		=> $this->time->date('Y', $this->pdh->get('articles', 'date', array($intArticleID))),
						'ARTICLE_PATH'			=> $this->controller_path.$this->pdh->get('articles',  'path', array($intArticleID)),
						'ARTICLE_SOCIAL_BUTTONS'=> ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$this->controller_path_plain.$this->pdh->get('articles',  'path', array($intArticleID)), strip_tags($this->pdh->get('articles',  'title', array($intArticleID)))) : '',
						'ARTICLE_TOOLBAR'		=> $jqToolbar['id'],
						'ARTICLE_PUBLISHED'		=> ($blnPublished) ? true : false,
						'ARTICLE_REAL_CATEGORY' => $this->pdh->get('articles',  'category', array($intArticleID)),
						'ARTICLE_REAL_CATEGORY_NAME' => $this->pdh->get('article_categories', 'name', array($this->pdh->get('articles',  'category', array($intArticleID)))),
						'ARTICLE_PREVIEW_IMAGE' => $strPreviewImage,
						'ARTICLE_PREVIEW_IMAGE_BIG' => ($this->pdh->get('articles',  'previewimage', array($intArticleID)) != "") ? $this->pdh->geth('articles', 'previewimage', array($intArticleID, 750)) : '',
						'PERMALINK'				=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
						'S_TOOLBAR'				=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
						'S_TAGS'				=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
						'ARTICLE_CONTENT_LENGTH'=> $intTextLength,
						'S_READMORE'			=> (isset($arrContent[2]) || count($arrReadmore) > 1) ? true : false,
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
					'CATEGORY_COUNT'		=> count($arrSortedArticleIDs),
					'PERMALINK'				=> $this->pdh->get('article_categories', 'permalink', array($intCategoryID)),
					'RSSLINK'				=> $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''),
					'ARTICLE_TOOLBAR'		=> $jqToolbar['id'],
					'S_TOOLBAR'				=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
					'LIST_TYPE'				=> $arrCategory['list_type'],
					'S_HIDE_HEADER'			=> $arrCategory['hide_header'],
					'CATEGORY_ID'			=> $intCategoryID,
			));

			$strPreviewImage = $this->social->getFirstImage(xhtml_entity_decode($arrCategory['description']));

			$this->social->callSocialPlugins($arrCategory['name'], strip_tags(xhtml_entity_decode($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(truncate($arrCategory['description'], 600, '...', false, true))))), $strPreviewImage);

			$this->tpl->add_rssfeed($arrCategory['name'], $this->controller_path.'RSS/'.$this->routing->clean($arrCategory['name']).'-c'.$intCategoryID.'/'.(($this->user->is_signedin()) ? '?key='.$this->user->data['exchange_key'] : ''));
			$this->tpl->add_meta('<link rel="canonical" href="'.$this->pdh->get('article_categories', 'permalink', array($intCategoryID)).'" />');

			$intPortallayout = ($blnIsStartpage) ? $this->pdh->get('portal_layouts', 'layout_for_route', array('startpage', true)) : $arrCategory['portal_layout'];
			if($intPortallayout === false) $intPortallayout = $arrCategory['portal_layout'];

			$this->core->set_vars(array(
					'page_title'		=> $arrCategory['name'],
					'description'		=> htmlspecialchars(truncate(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($arrCategory['description'])))), 600, '...', false, true), ENT_QUOTES),
					'page_path'			=> ($this->pdh->get('article_categories', 'parent', array($intCategoryID)) > 1) ? $this->pdh->get('article_categories', 'breadcrumb', array($intCategoryID)) : [],
					'image'				=> $strPreviewImage,
					'template_file'		=> 'category.html',
					'portal_layout'		=> $intPortallayout,
					'display'			=> true)
					);
		}

		public function showArticle($intArticleID, $strSpecificID, $blnIsStartpage, $strPath, $arrPath){
			$arrArticle = $this->pdh->get('articles', 'data', array($intArticleID));

			infotooltip_js();

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

			$intCurrentUserID = (isset($this->user->data['session_perm_id']) && $this->user->data['session_perm_id'] > 0) ? intval($this->user->data['session_perm_id']) : $this->user->id;

			//Check Start to/start from
			if (($arrArticle['show_from'] != "" && $arrArticle['show_from'] > $this->time->time) || ($arrArticle['show_to'] != "" && $arrArticle['show_to'] < $this->time->time)) $intPublished = false;

			//Get Category Data
			$intCategoryID = $arrArticle['category'];
			registry::add_const('categoryid', $intCategoryID);
			$arrCategory = $this->pdh->get('article_categories', 'data', array($intCategoryID));
			$arrCategory['name'] = $this->user->multilangValue($arrCategory['name']);

			//Category Permissions
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($arrArticle['category'], $intCurrentUserID));
			if (!$arrPermissions['read']) message_die($this->user->lang('article_noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);

			//Check Start to/start from
			if ((!$intPublished && (!$arrPermissions['update'] && !$arrPermissions['change_state'])) || !$arrCategory['published']) message_die($this->user->lang('article_unpublished'));

			registry::add_const('page_path', $strPath);
			$strPath = ucfirst($this->pdh->get('articles', 'path', array($intArticleID)));
			registry::add_const('page', $this->user->removeSIDfromString($strPath));
			registry::add_const('pageobject', 'article');


			//Page divisions
			$strText = xhtml_entity_decode($arrArticle['text']);
			$arrPagebreaks = array();
			preg_match_all('#<hr(.*)class="system-pagebreak"(.*)\/>#iU', $strText, $arrPagebreaks, PREG_PATTERN_ORDER);

			$arrArticle['title'] = $this->user->multilangValue($arrArticle['title']);

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


			$userlink = $this->pdh->geth('user', 'name', array($this->pdh->get('articles',  'user_id', array($intArticleID)), '', '', true));

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
							system_message("'.$this->tpl->handleModifier($this->user->lang('success_create_article'), 'jsencode').'", "success");
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
							system_message('.$this->tpl->handleModifier($this->user->lang('success_create_article'), 'jsencode').', "success");
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
						'image_upload'	=> true,
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
			$arrPageObjects = array();
			preg_match_all('#<p(.*)class="system-article"(.*) title="(.*)">(.*)</p>#iU', $strContent, $arrPageObjects, PREG_PATTERN_ORDER);
			if (count($arrPageObjects[0])){
				include_once($this->root_path.'core/pageobject.class.php');
				foreach($arrPageObjects[3] as $key=>$val){
					$strHaystack = $arrPageObjects[0][$key];
					if (!is_file($this->root_path.'core/pageobjects/'.$val.'_pageobject.class.php')) continue;
					include_once($this->root_path.'core/pageobjects/'.$val.'_pageobject.class.php');
					$objPage = registry::register($val.'_pageobject');

					//Reset pageobject var
					$strPageObjectVar = registry::get_const('pageobject');
					registry::add_const('pageobject', $strPageObjectVar.' pageobject-'.$val);

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

			$strContent = $this->formatArticleOutput($strContent, $intArticleID, $strSpecificID);

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
					'ARTICLE_DATE_ATOM'	=> date(DATE_ATOM, $arrArticle['date']),
					'ARTILE_DATE_DAY'	=> $this->time->date('d', $this->pdh->get('articles', 'date', array($intArticleID))),
					'ARTILE_DATE_MONTH'	=> $this->time->date('F', $this->pdh->get('articles', 'date', array($intArticleID))),
					'ARTILE_DATE_YEAR'	=> $this->time->date('Y', $this->pdh->get('articles', 'date', array($intArticleID))),
					'ARTICLE_TIMETAG'	=> $this->time->createTimeTag($arrArticle['date'], $this->time->user_date($arrArticle['date'], false, false, true).', '.$this->time->user_date($arrArticle['date'], false, true)),
					'ARTICLE_AUTHOR'	=> $userlink,
					'ARTICLE_AUTHOR_AVATAR' => $this->pdh->geth('user', 'avatarimglink', array($this->pdh->get('articles',  'user_id', array($intArticleID)))),
					'ARTICLE_PUBLISHED' => ($intPublished) ? true : false,
					'ARTICLE_TIME'		=> $this->time->user_date($arrArticle['date'], false, true),
					'ARTICLE_REAL_CATEGORY' => $this->pdh->get('articles',  'category', array($intArticleID)),
					'ARTICLE_REAL_CATEGORY_NAME' => $this->pdh->get('article_categories', 'name', array($this->pdh->get('articles',  'category', array($intArticleID)))),
					'S_PAGINATION'		=> ($pageCount > 1) ? true : false,
					'ARTICLE_SOCIAL_BUTTONS'  => ($arrCategory['social_share_buttons']) ? $this->social->createSocialButtons($this->env->link.$this->controller_path_plain.$strPath, strip_tags($arrArticle['title'])) : '',
					'PERMALINK'			=> $this->pdh->get('articles', 'permalink', array($intArticleID)),
					'ARTICLE_RATING'	=> ($arrArticle['votes']) ? $this->jquery->starrating($intArticleID, $this->controller_path.$strPath.'&savevote&link_hash='.$this->CSRFGetToken('savevote'), array('score' => (($arrArticle['votes_count']) ? round($arrArticle['votes_sum'] / $arrArticle['votes_count']): 0), 'number' => 10)) : '',
					//'ARTICLE_RATING'  => ($arrArticle['votes']) ? $this->jquery->StarRating('article_vote', $myRatings,$this->server_path.$strPath,(($arrArticle['votes_count']) ? round($arrArticle['votes_sum'] / $arrArticle['votes_count']): 0), $blnUserHasVoted) : '',
					'ARTICLE_TOOLBAR'	=> $jqToolbar['id'],
					'S_TOOLBAR'			=> ($arrPermissions['create'] || $arrPermissions['update'] || $arrPermissions['delete'] || $arrPermissions['change_state']),
					'S_TAGS'			=> (count($arrTags)  && $arrTags[0] != "") ? true : false,
					'COMMENTS_COUNTER'	=> ($intCommentsCount == 1 ) ? $intCommentsCount.' '.$this->user->lang('comment') : $intCommentsCount.' '.$this->user->lang('comments'),
					'S_COMMENTS'		=> ($arrArticle['comments']) ? true : false,
					'S_HIDE_HEADER'		=> ($arrArticle['hide_header']),
					'S_FEATURED'		=> ($this->pdh->get('articles',  'featured', array($intArticleID))),
					'ARTICLE_LINK'		=> $this->env->link.$this->controller_path_plain.$this->pdh->get('articles', 'path', array($intArticleID)),
			));

			$strPreviewImage = ($this->pdh->get('articles',  'previewimage', array($intArticleID)) != "") ? $this->pdh->geth('articles', 'previewimage', array($intArticleID)) : '';

			if(!strlen($strPreviewImage)) $strPreviewImage = $this->social->getFirstImage($strContent);
			$this->social->callSocialPlugins($arrTitles[$intPageID], strip_tags(xhtml_entity_decode($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(truncate($strContent, 600, '...', false, true))))), $strPreviewImage);


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

			$intPortallayout = ($blnIsStartpage) ? $this->pdh->get('portal_layouts', 'layout_for_route', array('startpage', true)) : $arrCategory['portal_layout'];
			if($intPortallayout === false) $intPortallayout = $arrCategory['portal_layout'];

			$this->core->set_vars(array(
					'page_title'		=> $arrArticle['title'].$strAdditionalTitles,
					'description'		=> htmlspecialchars((isset($arrCoreVars['description']) && $arrCoreVars['description']) ? $arrCoreVars['description'] : truncate(strip_tags($this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags(xhtml_entity_decode($arrContent[$intPageID])))), 600, '...', false, true), ENT_QUOTES),
					'page_path'			=> $this->pdh->get('articles', 'breadcrumb', array($intArticleID, $strAdditionalTitles, registry::get_const('url_id'), $arrPath)),
					'image'				=> $strPreviewImage,
					'template_file'		=> 'article.html',
					'portal_layout'		=> $intPortallayout,
					'display'			=> true)
			);
		}

		public function formatArticleOutput($strText, $intArticleID, $strSpecificID){
			$strText = $this->bbcode->parse_shorttags($strText);

			//Hook to replace content
			if ($this->hooks->isRegistered('article_parse')){
				$arrHooks = $this->hooks->process('article_parse', array('content' => $strText, 'view' => 'category', 'article_id' => $intArticleID, 'specific_id' => $strSpecificID), true);
				if (isset($arrHooks['content'])) $strText = $arrHooks['content'];
			}

			//Replace Image Gallery
			$arrGalleryObjects = array();
			preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strText, $arrGalleryObjects, PREG_PATTERN_ORDER);
			if (count($arrGalleryObjects[0])){
				foreach($arrGalleryObjects[4] as $key=>$val){
					$strGalleryContent = $this->gallery($val, (int)$arrGalleryObjects[3][$key], $this->controller_path.$strPath, $intPageID);
					$strText = str_replace($arrGalleryObjects[0][$key], $strGalleryContent, $strText);
				}
			}


			//Replace Raidloot
			$arrRaidlootObjects = array();
			preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
			if (count($arrRaidlootObjects[0])){
				foreach($arrRaidlootObjects[3] as $key=>$val){
					$withChars = ($arrRaidlootObjects[5][$key] == "true") ? true : false;
					$strRaidlootContent = $this->raidloot((int)$val, $withChars);
					$strText = str_replace($arrRaidlootObjects[0][$key], $strRaidlootContent, $strText);
				}
			}

			//Replace Embedds
			$strText = $this->embedly->parseString($strText);

			//Replace Smilies
			$strText = $this->bbcode->MyEmoticons($strText);

			return $strText;
		}

		public function gallery($strFolder, $intSortation, $strPath, $intPageNumber  = 0){
			$strFolder = str_replace("*+*+*", "/", $strFolder);
			$strOrigFolder = $strFolder;
			//Subfolder navigation
			if ($this->in->get('gf') != "" && $this->in->get('gsf') != ""){
				if (base64_decode($this->in->get('gf')) == $strOrigFolder) $strFolder = base64_decode($this->in->get('gsf'));
			}


			$contentFolder = $this->pfh->FolderPath($strFolder, 'files');
			$contentFolderSP = $this->pfh->FolderPath($strFolder, 'files', 'serverpath');

			$dataFolder = $this->pfh->FolderPath('system', 'files', 'plain');
			$blnIsSafe = isFilelinkInFolder($contentFolder, $dataFolder);
			if (!$blnIsSafe) return "";

			$arrFiles = sdir($contentFolder);
			$arrDirs = $arrImages = $arrImagesDate = $arrImageDimensions = array();

			foreach($arrFiles as $key => $val){
				if (is_dir($contentFolder.$val)){
					$arrDirs[] = $val;
				} else {
					$extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
					if (in_array($extension, array('jpg', 'png', 'gif', 'jpeg'))){
						$arrImages[$val] = pathinfo($val, PATHINFO_FILENAME);
						$arrImageDimensions[$val] = getimagesize($contentFolder.$val);
						if ($intSortation == 2 || $intSortation == 3) $arrImagesDate[$val] = filemtime($contentFolder.$val);
					}
				}
			}

			switch($intSortation){
				case 1: natcasesort($arrImages);
						$arrImages = array_reverse($arrImages);

				break;
				case 2: asort($arrImagesDate); $arrImages = $arrImagesDate;
				break;

				case 3: arsort($arrImagesDate); $arrImages = $arrImagesDate;
				break;

				default: natcasesort($arrImages);
			}

			$strOut = '<ul class="image-gallery">';
			$strLink = $strPath.(($intPageNumber > 1) ? '&page='.$intPageNumber : '');

			if($this->in->exists('gsf') && $this->in->get('gsf') != ''){
				$arrPath = array_filter(explode('/', $strFolder));
				array_pop($arrPath);
				$strFolderUp = implode('/', $arrPath);
				if ($strFolderUp == $strOrigFolder) {
					$strFolderUp = '';
				} else {
					$strFolderUp = base64_encode($strFolderUp);
				}
				$strOut .='<li class="folderup"><a href="'.$strLink.'&gf='.base64_encode($strOrigFolder).'&gsf='.$strFolderUp.'"><i class="fa fa-level-up fa-flip-horizontal"></i><br/>'.$this->user->lang('back').'</a></li>';
			}

			natcasesort($arrDirs);
			foreach($arrDirs as $foldername){
				$strOut .= '<li class="folder"><a href="'.$strLink.'&gf='.base64_encode($strOrigFolder).'&gsf='.base64_encode($strFolder.'/'.$foldername).'"><i class="fa fa-folder"></i><br/>'.sanitize($foldername).'</a></li>';
			}

			$strThumbFolder = $this->pfh->FolderPath('system/thumbs', 'files');
			$strThumbFolderSP = $this->pfh->FolderPath('system/thumbs', 'files', 'serverpath');

			foreach($arrImages as $key => $val){
				//Check for thumbnail
				$strThumbname = "thumb_".pathinfo($key, PATHINFO_FILENAME)."-150x150.".pathinfo($key, PATHINFO_EXTENSION);
				$strThumbnail = "";
				if (is_file($strThumbFolder.$strThumbname)){
					$strThumbnail = $strThumbFolderSP.$strThumbname;
				} else {
					//Create thumbnail
					$this->pfh->thumbnail($contentFolder.$key, $strThumbFolder, $strThumbname, 150);
					if (is_file($strThumbFolder.$strThumbname)){
						$strThumbnail = $strThumbFolderSP.$strThumbname;
					}
				}

				if($strThumbnail != ""){
					$strOut .= '<li class="image"><a href="'.$contentFolderSP.$key.'" class="lightbox_'.md5($strFolder).'" rel="'.md5($strFolder).'" title="'.sanitize($key).'; '.$arrImageDimensions[$key][0].'x'.$arrImageDimensions[$key][1].' px"><img src="'.$strThumbnail.'" alt="Image" /></a></li>';
				}

			}

			$strOut .= "</ul><div class=\"clear\"></div>";

			$this->jquery->lightbox(md5($strFolder), array('slideshow' => true, 'transition' => "elastic", 'slideshowSpeed' => 4500, 'slideshowAuto' => false));

			return $strOut;
		}

		public function raidloot($intRaidID, $blnWithChars=false){
			//Get Raid-Infos:
			$intEventID = $this->pdh->get('raid', 'event', array($intRaidID));
			if ($intEventID){
				if(isset($this->_cache['raidloot']) && isset($this->_cache['raidloot'][$intRaidID])){
					return $this->_cache['raidloot'][$intRaidID];
				}

				$strOut = '<div class="raidloot"><h3>'.$this->user->lang('loot').' '.$this->pdh->get('event', 'html_icon', array($intEventID)).$this->pdh->get('raid', 'html_raidlink', array($intRaidID, register('routing')->simpleBuild('raids'), '', true));
				$strRaidNote = $this->pdh->get('raid', 'html_note', array($intRaidID));
				if ($strRaidNote != "") $strOut .= ' ('.$strRaidNote.')';
				$strOut .= ', '.$this->pdh->get('raid', 'html_date', array($intRaidID)).'</h3>';

				//Get Items from the Raid
				$arrItemlist = $this->pdh->get('item', 'itemsofraid', array($intRaidID));
				infotooltip_js();

				if (count($arrItemlist)){
					foreach($arrItemlist as $item){
						$buyer = $this->pdh->get('item', 'buyer', array($item));
						$strOut .=  $this->pdh->get('item', 'link_itt', array($item, register('routing')->simpleBuild('items'), '', false, false, false, false, false, true)). ' - '.$this->pdh->geth('member', 'memberlink_decorated', array($buyer, register('routing')->simpleBuild('character'), '', true)).
						', '.round($this->pdh->get('item', 'value', array($item))).' '.$this->config->get('dkp_name').'<br />';
					}
				}

				if ($blnWithChars){
					$attendees_ids = $this->pdh->get('raid', 'raid_attendees', array($intRaidID));
					if (count($attendees_ids)){
						$strOut .= '<br /><h3>'.$this->user->lang('attendees').'</h3>';

						foreach($attendees_ids as $intAttendee){
							$strOut.= $this->pdh->get('member', 'memberlink_decorated', array($intAttendee, $this->routing->simpleBuild('character'), '', true)).'<br/>';
						}
					}
				}

				$strOut = $strOut.'</div>';

				if(!isset($this->_cache['raidloot'])) $this->_cache['raidloot'] = array();
				$this->_cache['raidloot'][$intRaidID] = $strOut;

				return $strOut;
			}
			return '';
		}

		public function buildCalendarevent($intEventID){
			if(isset($this->_cache['calendarevent']) && isset($this->_cache['calendarevent'][$intEventID])){
				return $this->_cache['calendarevent'][$intEventID];
			}

			$out = '<div class="articleCalendarEventBox table noMobileTransform">';

			$eventextension	= $this->pdh->get('calendar_events', 'extension', array($intEventID));
			if(!$eventextension) return false;

			$raidclosed		= ($this->pdh->get('calendar_events', 'raidstatus', array($intEventID)) == '1') ? true : false;

			if($eventextension['calendarmode'] == 'raid') {
				$eventdata	= $this->pdh->get('calendar_events', 'data', array($intEventID));

				// Build the Deadline
				$deadlinedate	= $eventdata['timestamp_start']-($eventdata['extension']['deadlinedate'] * 3600);
				if(date('j', $deadlinedate) == date('j', $eventdata['timestamp_start'])){
					$deadlinetime	= $this->time->user_date($deadlinedate, false, true);
				}else{
					$deadlinetime	= $this->time->user_date($deadlinedate, true);
				}

				$data = array(
						'NAME'				=> $this->pdh->get('event', 'name', array($eventdata['extension']['raid_eventid'])),
						'DATE_DAY'				=> $this->time->date('d', $eventdata['timestamp_start']),
						'DATE_MONTH'			=> $this->time->date('F', $eventdata['timestamp_start']),
						'DATE_YEAR'				=> $this->time->date('Y', $eventdata['timestamp_start']),
						'DATE_FULL'				=> $this->time->user_date($eventdata['timestamp_start']).', '.$this->time->user_date($eventdata['timestamp_start'], false, true).' - '.$this->time->user_date($eventdata['timestamp_end'], false, true),
						'RAIDTIME_START'		=> $this->time->user_date($eventdata['timestamp_start'], false, true),
						'RAIDTIME_END'			=> $this->time->user_date($eventdata['timestamp_end'], false, true),
						'RAIDTIME_DEADLINE'		=> $deadlinetime,
						'CALENDAR'				=> $this->pdh->get('calendars', 'name', array($eventdata['calendar_id'])),
						'RAIDICON'				=> $this->pdh->get('event', 'html_icon', array($eventdata['extension']['raid_eventid'], 32)),
						'RAIDNOTE'				=> ($eventdata['notes']) ? $this->bbcode->toHTML(nl2br($eventdata['notes'])) : '',
						'LINK'					=> $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intEventID)), $intEventID),
				);

				$out .= '<div class="tr raid '.(($raidclosed) ? 'closed' : 'open').'"><div class="bigDateContainer td">';
				$out .= $data['RAIDICON'];
				$out .= '<div class="middleDateTime">'.$data['DATE_DAY'].'</div>';
				$out .= '<div class="articleMonth">'.$data['DATE_MONTH'].'</div>';
				$out .= '<div class="middleDateTime">'.$data['RAIDTIME_START'].'</div>';
				$out .= '</div>';

				$out .= '<div class="articleCalendarEventBoxContent td">';
				$closedIcon = ($raidclosed) ? '<i class="fa fa-lg fa-lock"></i> ' : '';
				$out .= '<h2>'.$closedIcon.'<a href="'.$data['LINK'].'">'.$data['NAME'].'</a></h2>';
				$out .= '<div class="eventdata-details">
			<div class="eventdata-details-date"><i class="fa fa-lg fa-calendar-o"></i> '.$data['DATE_FULL'].'</div>';
				$out .= '<div class="eventdata-details-deadline"><i class="fa fa-calendar-times-o fa-lg" title="{L_raidevent_raidleader}"></i> '.$this->user->lang('calendar_deadline').' '.$data['RAIDTIME_DEADLINE'].' </div>';
				$out .='<div class="eventdata-details-calendar"><i class="fa fa-calendar fa-lg"></i> '.$data['CALENDAR'].'</div>';

				//Attendees
				// Build the Attendee Array
				$attendees = array();
				$attendees_raw = $this->pdh->get('calendar_raids_attendees', 'attendees', array($intEventID));
				if(is_array($attendees_raw)){
					foreach($attendees_raw as $attendeeid=>$attendeerow){
						if($attendeeid > 0){
							$attendees[$attendeerow['signup_status']][$attendeeid] = $attendeerow;
						}
					}
				}

				// Build the guest array
				$guests = array();
				if($this->config->get('calendar_raid_guests') > 0){
					$guestarray = $this->pdh->get('calendar_raids_guests', 'members', array($intEventID));
					if(is_array($guestarray)){
						foreach($guestarray as $guest_row){
							if(!isset($guests[$guest_row['status']])) $guests[$guest_row['status']] = array();
							$guests[(int)$guest_row['status']][] = $guest_row['name'];
						}
					}
				}
				// get the status counts
				$raidcal_status = $this->config->get('calendar_raid_status');
				$raidstatus = array();
				if(is_array($raidcal_status)){
					foreach($raidcal_status as $raidcalstat_id){
						if($raidcalstat_id != 4){
							$raidstatus[$raidcalstat_id]	= $this->user->lang(array('raidevent_raid_status', $raidcalstat_id));
						}
					}
				}

				$counts = '';
				foreach($raidstatus as $statusid=>$statusname){
					$actcount  = ((isset($attendees[$statusid])) ? count($attendees[$statusid]) : 0);
					$actcount += (is_array($guests[$statusid]) ? count($guests[$statusid]) : 0);
					
					$counts[$statusid]  = $actcount;			
				}
				
				$signinstatus = $this->pdh->get('calendar_raids_attendees', 'html_status', array($intEventID, $this->user->data['user_id']));

				if (is_array($counts)){
					foreach($counts as $countid=>$countdata){#
						$out .= '<span class="status'.$countid.' nextevent_statusrow coretip" data-coretip="'.$raidstatus[$countid].'">'.$this->pdh->get('calendar_raids_attendees', 'status_flag', array($countid)).' '.$countdata.'</span>';
					}
				}

				if($signinstatus && $signinstatus != ""){
					$out .= ' &bull; <i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i> '.$signinstatus;
				}

				$out .='</div>';
				$out .= '</div></div>';
			} else {
				$eventdata	= $this->pdh->get('calendar_events', 'data', array($intEventID));

				$blnIsPrivate = ($eventdata['private'] == 1) ? true : false;
				if($blnIsPrivate) return false;

				if($eventdata['allday'] == 1){
					$full_date = $this->time->user_date($eventdata['timestamp_start']).', '.$this->user->lang('calendar_allday');
				}elseif($this->time->date('d', $eventdata['timestamp_start']) == $this->time->date('d', $eventdata['timestamp_end'])){
					//Samstag, 31.12.2015, 15 - 17 Uhr
					$full_date = $this->time->user_date($eventdata['timestamp_start']).', '.$this->time->user_date($eventdata['timestamp_start'], false, true);
				}else{
					$full_date = $this->time->user_date($eventdata['timestamp_start'], true, false).' - '.$this->time->user_date($eventdata['timestamp_end'], true, false);
				}

				$data = array(
						'NAME'				=> $this->pdh->get('calendar_events', 'name', array($intEventID)),
						'DATE_DAY'			=> $this->time->date('d', $eventdata['timestamp_start']),
						'DATE_MONTH'		=> $this->time->date('F', $eventdata['timestamp_start']),
						'DATE_YEAR'			=> $this->time->date('Y', $eventdata['timestamp_start']),
						'DATE_TIME'			=> ($eventdata['allday'] == 1) ? '' : $this->time->user_date($eventdata['timestamp_start'], false, true),
						'DATE_FULL'			=> $full_date,
						'LOCATION'			=> (isset($eventdata['extension']['location'])) ? $eventdata['extension']['location'] : false,
						'CALENDAR'			=> $this->pdh->get('calendars', 'name', array($eventdata['calendar_id'])),
						'LINK'				=> $this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intEventID)), $intEventID),
				);

				$out .= '<div class="tr event"><div class="bigDateContainer td">';
				$out .= '<div class="bigDateNumber">'.$data['DATE_DAY'].'</div>';
				$out .= '<div class="articleMonth">'.$data['DATE_MONTH'].'</div>';
				$out .= '<div class="middleDateTime">'.$data['DATE_TIME'].'</div>';
				$out .= '</div>';

				$out .= '<div class="articleCalendarEventBoxContent td">';
				$out .= '<h2><a href="'.$data['LINK'].'">'.$data['NAME'].'</a></h2>';
				$out .= '<div class="eventdata-details">
			<div class="eventdata-details-date"><i class="fa fa-lg fa-calendar-o"></i> '.$data['DATE_FULL'].'</div>';
				if($data['LOCATION']){
					$out .= '<div class="eventdata-details-location"><i class="fa fa-lg fa-map-marker"></i> '.$data['LOCATION'].'</div>';
				}
				$out .='<div class="eventdata-details-calendar"><i class="fa fa-calendar fa-lg"></i> '.$data['CALENDAR'].'</div>';
				//Attendees
				$event_attendees		= (isset($eventdata['extension']['attendance']) && count($eventdata['extension']['attendance']) > 0) ? $eventdata['extension']['attendance'] : array();
				$userstatus	  = array('attendance' => 0, 'maybe' => 0, 'decline' => 0);
				$statusofuser = array();
				foreach($event_attendees as $attuserid=>$attstatus){
					switch($attstatus){
						case 1:		$attendancestatus = 'attendance'; break;
						case 2:		$attendancestatus = 'maybe'; break;
						case 3:		$attendancestatus = 'decline'; break;
					}
					$statusofuser[$attuserid] = $attstatus;
					if(!isset($userstatus[$attendancestatus])) $userstatus[$attendancestatus] = 0;
					$userstatus[$attendancestatus]++;
				}

				$out .='<div><i class="fa fa-lg fa-users green coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_confirmations').'"></i> '.$userstatus['attendance'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 1) ? ' <i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').'
					&bull; <i class="fa fa-lg fa-users orange coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_maybes').'"></i> '.$userstatus['maybe'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 2) ? '<i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').'
					&bull; <i class="fa fa-lg fa-users red coretip" data-coretip="'.$this->user->lang('calendar_eventdetails_declines').'"></i> '.$userstatus['decline'].((isset($statusofuser[$this->user->id]) && $statusofuser[$this->user->id] == 3) ? '<i class="fa fa-lg fa-user coretip" data-coretip="'.$this->user->data['username'].'"></i>' : '').
							'</div>';

				$out .='</div>';
				$out .= '</div></div>';

			}


			$out .= '</div>';

			if(!isset($this->_cache['calendarevent'])) $this->_cache['calendarevent'] = array();

			$this->_cache['calendarevent'][$intEventID] = $out;

			return $out;
		}


		protected function CSRFGetToken($strProcess){
			$strAction = 'controller'.$strProcess;
			return $this->user->csrfGetToken($strAction);
		}
	}

}
