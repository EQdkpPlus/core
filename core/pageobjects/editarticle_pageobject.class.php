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

class editarticle_pageobject extends pageobject {
	
	private $arrPermissions = array('read' => false, 'create' => false, 'update' => false, 'delete' => false, 'change_state' => false);

	public function __construct(){
		$handler = array(
			'save_headline'		=> array('process' => 'ajax_saveheadline', 'csrf' => true),
			'save_article'		=> array('process' => 'ajax_savearticle', 'csrf' => true),
			'get_raw_article'	=> array('process' => 'ajax_getrawarticle'),
			'update'			=> array('process' => 'update', 'csrf' => true),
			'checkalias'		=> array('process' => 'ajax_checkalias'),
			'delpreviewimage'	=> array('process' => 'delete_previewimage', 'csrf' => true),
			'aid'				=> array('process' => 'edit'),
		);
		
		//Permission Check
		$blnPermission = false;
		if ($this->in->get('aid', 0)){
			//Get Category
			$intCategoryID = $this->pdh->get('articles', 'category', array($this->in->get('aid', 0)));
			
			if ($intCategoryID){
				$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));	
			}
			
			//Check Category permission
		} elseif($this->in->get('cid', 0)) {
			//Get Category Permissions
			$intCategoryID = $this->in->get('cid', 0);
			$arrPermissions = $this->pdh->get('article_categories', 'user_permissions', array($intCategoryID, $this->user->id));
		}
		
		if (is_array($arrPermissions)) $this->arrPermissions = $arrPermissions;
		
		if (!in_array(true, $this->arrPermissions)) message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);
		
		parent::__construct(false, $handler, array('articles', 'title'), false, 'selected_ids[]');
		$this->process();
	}
	
	public function delete_previewimage(){
		if ($this->arrPermissions['update'] || $this->arrPermissions['create']){
			$id = $this->in->get('aid', 0);
			if ($id) $this->pdh->put('articles', 'delete_previewimage', array($id));
		}
	}
	
	public function ajax_saveheadline(){
		if ($this->arrPermissions['update']){
			$strTitle = $this->in->get('headline');
			$id = $this->in->get('aid', 0);
			
			if ($strTitle != "") {
				$this->pdh->put('articles', 'update_headline', array($id, $strTitle));
				$this->pdh->process_hook_queue();
			}

			header('Content-type: application/json; charset=utf-8');
			echo json_encode(array('status' => true));
			exit();
			
		}
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode(array('status' => false));
		exit();
	}
	
	public function ajax_savearticle(){
		if ($this->arrPermissions['update']){
			$strText = $this->in->get('text', '', 'raw');
			$id = $this->in->get('aid', 0);
		
			if ($strText != "") {
				$this->pdh->put('articles', 'update_article', array($id, $strText));
				$this->pdh->process_hook_queue();
			}
		
			header('Content-type: application/json; charset=utf-8');
			echo json_encode(array('status' => true));
			exit();
		}
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode(array('status' => false));
		exit();
	}
	
	public function ajax_getrawarticle(){
		$id = $this->in->get('aid', 0);
		$strArticle = $this->pdh->get('articles', 'text', array($id));
		
		header('Content-type: application/json; charset=utf-8');
		echo json_encode(array('text' => unsanitize($strArticle)));
		exit();
	}
		
	public function ajax_checkalias(){
		$strAlias = $this->in->get('alias');
		$intAID = $this->in->get('aid', 0);
		
		$blnResult = $this->pdh->get('articles', 'check_alias', array($strAlias, true));
		if (!$blnResult && $this->pdh->get('articles', 'alias', array($intAID)) === $strAlias) $blnResult = true;
		if (is_numeric($strAlias)) $blnResult = false;
		
		header('content-type: text/html; charset=UTF-8');
		if ($blnResult){
			echo 'true';
		} else {
			echo 'false';
		}
		exit;
	}
		
	public function update(){
		$cid = $this->in->get('cid', 0);
		$id = $this->in->get('aid', 0);

		$strTitle = $this->in->get('title');
		$strText = $this->in->get('text', '', 'raw');
		$strTags = $this->in->get('tags');
		$strPreviewimage = $this->in->get('previewimage');
		if ($strPreviewimage != "") $strPreviewimage = str_replace(register('pfh')->FileLink('', 'files', 'absolute'), '', $strPreviewimage);
		$strAlias = $this->in->get('alias');
		$intPublished = $this->in->get('published', 0);
		
		//Get default published state
		$intDefaultPublished = $this->pdh->get("article_categories", "article_published_state", array($cid));
		if ($intPublished && !$intDefaultPublished) $intPublished = 0;
		
		$intFeatured = $this->in->get('featured', 0);
		$intCategory = ($this->in->exists('category')) ? $this->in->get('category', 0) : $cid;
		$intUserID = $this->user->id;
		$intComments = $this->in->get('comments', 0);
		$intVotes = $this->in->get('votes', 0);
		$intHideHeader = $this->in->get('hide_header', 0);
		
		$schluesselwoerter = preg_split("/[\s,]+/", $strTags);
		$arrTags = array();
		foreach($schluesselwoerter as $val){
			$arrTags[] = utf8_strtolower($val);
		}
		
		$intDate = $this->time->fromformat($this->in->get('date'), 1);
		$strShowFrom = $strShowTo = "";
		if($this->in->exists('show_from') AND strlen($this->in->get('show_from')) AND $this->in->get('show_from') != $this->user->lang('never')) $strShowFrom = $this->time->fromformat($this->in->get('show_from'), 1);
		if($this->in->exists('show_to') AND strlen($this->in->get('show_to')) AND $this->in->get('show_to') != $this->user->lang('never')) $strShowTo = $this->time->fromformat($this->in->get('show_to'), 1);
		
		
		if ($strTitle == "" ) {
			$this->core->message('', '', 'red');
			$this->edit();
			return;
		}
		
		if ($id){
			//Update
			if (!$this->arrPermissions['update']) message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);
			$blnResult = $this->pdh->put('articles', 'update', array($id, $strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader));
		} else {
			if (!$this->arrPermissions['create']) message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied', true);
			$blnResult = $this->pdh->put('articles', 'add', array($strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader));
		}
		
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('success_create_article'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('error_create_article'), $this->user->lang('error'), 'red');
		}
		
		$this->pdh->process_hook_queue();
	}
	
	public function edit(){
		$id = $this->in->get('aid', 0);
		$cid = $this->in->get('cid', 0);
		
		$this->jquery->Tab_header('article_category-tabs');

		$editor = register('tinyMCE');
		$editor->editor_normal(array(
			'relative_urls'	=> false,
			'link_list'		=> true,
			'gallery'		=> true,
			'raidloot'		=> true,
		));
		
		$arrSubcategories = $this->pdh->get('article_categories', 'all_childs', array($cid));
		$arrCategories = array();
		foreach ($arrSubcategories as $caid){
			$arrPerms = $this->pdh->get('article_categories', 'user_permissions', array($caid, $this->user->id));
			if (!$arrPerms['update'] || !$arrPerms['create']) continue;
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}

		if ($id){
			$cid = $this->pdh->get('articles', 'category', array($id));
			$this->tpl->assign_vars(array(
				'TITLE'	=> $this->pdh->get('articles', 'title', array($id)),
				'TEXT'	=> $this->pdh->get('articles', 'text', array($id)),
				'ALIAS'	=> $this->pdh->get('articles', 'alias', array($id)),
				'TAGS'	=> implode(', ', $this->pdh->get('articles', 'tags', array($id))),
				'DD_CATEGORY' => new hdropdown('category', array('options' => $arrCategories, 'value' => $this->pdh->get('articles', 'category', array($id)))),
				
				'PUBLISHED_RADIO' => new hradio('published', array('value' => ($this->pdh->get('articles', 'published', array($id))))),
				'FEATURED_RADIO' => new hradio('featured', array('value' => ($this->pdh->get('articles', 'featured', array($id))))),
				'COMMENTS_RADIO' => new hradio('comments', array('value' => ($this->pdh->get('articles', 'comments', array($id))))),
				'VOTES_RADIO' => new hradio('votes', array('value' => ($this->pdh->get('articles', 'votes', array($id))))),
				'HIDE_HEADER_RADIO' => new hradio('hide_header', array('value' => ($this->pdh->get('articles', 'hide_header', array($id))))),
				
				
				'DATE_PICKER'		=> $this->jquery->Calendar('date', $this->time->user_date($this->pdh->get('articles', 'date', array($id)), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_TO_PICKER'	=> $this->jquery->Calendar('show_to', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_to', array($id)))) ? $this->pdh->get('articles', 'show_to', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_FROM_PICKER'	=> $this->jquery->Calendar('show_from', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_from', array($id)))) ? $this->pdh->get('articles', 'show_from', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'PREVIEW_IMAGE'		=> new himageuploader('previewimage', array(
						'imgpath'	=> $this->pfh->FolderPath('','files'),
						'value'		=> $this->pdh->get('articles', 'previewimage', array($id)),
						'noimgfile'	=> "images/global/default-image.svg",
						'deletelink'=> $this->SID.'&aid='.$id.'&cid='.$cid.'&delpreviewimage=true&link_hash='.$this->CSRFGetToken('delpreviewimage'),
					)),
			));
			
		} else {
			
			$this->tpl->assign_vars(array(
				'DD_CATEGORY' => new hdropdown('category', array('options' => $arrCategories, 'value' => $cid)),
				'PUBLISHED_RADIO' => new hradio('published', array('value' => 1)),
				'FEATURED_RADIO' => new hradio('featured', array()),
				'COMMENTS_RADIO' => new hradio('comments', array('value' => 1)),
				'VOTES_RADIO' => new hradio('votes', array()),
				'HIDE_HEADER_RADIO' => new hradio('hide_header', array()),
				'DATE_PICKER'		=> $this->jquery->Calendar('date', $this->time->user_date($this->time->time, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_TO_PICKER'	=> $this->jquery->Calendar('show_to', $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_FROM_PICKER'	=> $this->jquery->Calendar('show_from', $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'PREVIEW_IMAGE'		=> new himageuploader('previewimage', array(
						'imgpath'	=> $this->pfh->FolderPath('logo','eqdkp'),
						'noimgfile'	=> "images/global/default-image.svg"
					)),
			));
		}
		
		$routing = register('routing');
		$arrPageObjects = $routing->getPageObjects();
		
		$this->tpl->assign_vars(array(
			'CID' 				=> $cid,
			'AID' 				=> $id,
			'CATEGORY_NAME' 	=> $this->pdh->get('article_categories', 'name', array($cid)),
			'ARTICLE_NAME' 		=> $this->pdh->get('articles', 'title', array($id)),
			'DD_PAGE_OBJECTS'	=> new hdropdown('page_objects', array('options' => $arrPageObjects)),
			'S_SHOW_CATEGORIES' => (count($arrCategories) > 0) ? true : false,
		));
		
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('manage_articles').': '.$this->pdh->get('articles', 'title', array($id)) : $this->user->lang('add_new_article')),
			'template_file'		=> 'article_edit.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
	
	public function  display(){
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('manage_articles').': '.$this->pdh->get('articles', 'title', array($id)) : $this->user->lang('add_new_article')),
			'template_file'		=> 'article_edit.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}

}
?>