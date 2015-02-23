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
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Articles extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_articles_man');
		$handler = array(
			'save' 		=> array('process' => 'save', 'csrf' => true),
			'update'	=> array('process' => 'update', 'csrf' => true),
			'checkalias'=> array('process' => 'ajax_checkalias'),
			'del_votes' => array('process' => 'delete_votes', 'csrf' => true),
			'del_comments' => array('process' => 'delete_comments', 'csrf' => true),
			'change_category' => array('process' => 'change_category', 'csrf' => true),
			'set_published' => array('process' => 'set_published', 'csrf' => true),
			'set_unpublished' => array('process' => 'set_unpublished', 'csrf' => true),
			'delpreviewimage' => array('process' => 'delete_previewimage', 'csrf' => true),
			'a'			=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('articles', 'title'), null, 'selected_ids[]');
		$this->process();
	}
	
	public function delete_previewimage(){
		$id = $this->in->get('a', 0);
		if ($id) {
			$this->pdh->put('articles', 'delete_previewimage', array($id));
			$this->pdh->process_hook_queue();
		}
	}
	
	public function set_unpublished(){
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			$this->pdh->put('articles', 'set_unpublished', array($this->in->getArray('selected_ids', 'int')));
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		}
		
	}
	
	public function set_published(){
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			$this->pdh->put('articles', 'set_published', array($this->in->getArray('selected_ids', 'int')));
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		}
		
	}
	
	public function change_category(){
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			$intCategory = $this->in->get('new_category',0);
			$this->pdh->put('articles', 'change_category', array($this->in->getArray('selected_ids', 'int'), $intCategory));
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		}
	}
	
	public function delete_votes(){
		$id = $this->in->get('a', 0);
		if ($id) {
			$blnResult = $this->pdh->put('articles', 'reset_votes', array($id));
			if ($blnResult){
				$this->core->message(sprintf($this->user->lang('admin_reset_voting_success'), $this->pdh->get('articles', 'title', array($id))), $this->user->lang('success'), 'green');
				$this->pdh->process_hook_queue();
			}
		}
		$this->edit();
	}
	
	public function delete_comments(){
		$id = $this->in->get('a', 0);
		if ($id) {
			$this->pdh->put('comment', 'delete_attach_id', array('articles', $id));
			$this->pdh->process_hook_queue();
			$this->logs->add('action_article_reset_comments', array(), $id, $this->pdh->get('articles', 'title', array($id)), 1, 'article');
			$this->core->message(sprintf($this->user->lang('admin_delete_comments_success'), $this->pdh->get('articles', 'title', array($id))), $this->user->lang('success'), 'green');
		}
		$this->edit();
	}
	
	
	
	public function ajax_checkalias(){
		$strAlias = $this->in->get('alias');
		$intAID = $this->in->get('a', 0);
		
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
		$cid = $this->in->get('c', 0);
		$id = $this->in->get('a', 0);
		
		$strTitle = $this->in->get('title');
		$strText = $this->in->get('text', '', 'raw');
		$strTags = $this->in->get('tags');
		$strPreviewimage = $this->in->get('previewimage');
		if ($strPreviewimage != "") $strPreviewimage = str_replace(register('pfh')->FileLink('', 'files', 'absolute'), '', $strPreviewimage);
		$strAlias = $this->in->get('alias');
		$intPublished = $this->in->get('published', 0);
		$intFeatured = $this->in->get('featured', 0);
		$intCategory = $this->in->get('category', 0);
		$intUserID = $this->in->get('user_id', 0);
		$intComments = $this->in->get('comments', 0);
		$intVotes = $this->in->get('votes', 0);
		$intHideHeader = $this->in->get('hide_header', 0);
		
		$schluesselwoerter = preg_split("/[\s,]+/", $strTags);
		$arrTags = array();
		foreach($schluesselwoerter as $val){
			$arrTags[] = utf8_strtolower(str_replace("-", "", $val));
		}
		
		$intDate = $this->time->fromformat($this->in->get('date'), 1);
		$strShowFrom = $strShowTo = "";
		if($this->in->exists('show_from') AND strlen($this->in->get('show_from')) AND $this->in->get('show_from') != $this->user->lang('never')) $strShowFrom = $this->time->fromformat($this->in->get('show_from'), 1);
		if($this->in->exists('show_to') AND strlen($this->in->get('show_to')) AND $this->in->get('show_to') != $this->user->lang('never')) $strShowTo = $this->time->fromformat($this->in->get('show_to'), 1);
		
		
		if ($strTitle == "" ) {
			$this->core->message($this->user->lang('headline'), $this->user->lang('adduser_send_mail_error_fields'), 'red');
			$this->edit();
			return;
		}
		
		if ($id){
			$blnResult = $this->pdh->put('articles', 'update', array($id, $strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader));
		} else {
			$blnResult = $this->pdh->put('articles', 'add', array($strTitle, $strText, $arrTags, $strPreviewimage, $strAlias, $intPublished, $intFeatured, $intCategory, $intUserID, $intComments, $intVotes,$intDate, $strShowFrom, $strShowTo, $intHideHeader));
		}
		
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('success_create_article'), $this->user->lang('success'), 'green');
			$this->edit($blnResult);
		} else {
			$this->core->message($this->user->lang('error_create_article'), $this->user->lang('error'), 'red');
			$this->display();
		}
		
	}
	
	public function save(){
		$cid = $this->in->get('c', 0);
		$arrPublished = $this->in->getArray('published', 'int');
		$arrFeatured = $this->in->getArray('featured', 'int');
		foreach($arrPublished as $key => $val){
			$this->pdh->put('articles', 'update_featuredandpublished', array($key, $arrFeatured[$key], $val));
		}
		$this->pdh->put('articles', 'update_index', array($this->in->get('index', 0), $cid));
		$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
	}
	
	public function delete(){
		$retu = array();

		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
				
				$pos[] = stripslashes($this->pdh->get('article', 'name', array($id)));
				$retu[$id] = $this->pdh->put('articles', 'delete', array($id));
			}
		}

		if(!empty($pos)) {
			if(in_array(false, $retu)) {
				$messages[] = array('title' => $this->user->lang('del_nosuc'), 'text' => implode(', ', $pos), 'color' => 'red');
			} else {
				$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			}

			$this->core->messages($messages);
		}
		
		$this->pdh->process_hook_queue();
	}
	
	public function edit($aid=false){
		$id = ($aid === false) ? $this->in->get('a', 0) : $aid;
		$cid = $this->in->get('c', 0);
		
		$this->jquery->Tab_header('article_category-tabs');

		$editor = register('tinyMCE');
		$editor->editor_normal(array(
			'relative_urls'	=> false,
			'link_list'		=> true,
			'pageobjects'	=> true,
			'gallery'		=> true,
			'raidloot'		=> true,
			'autoresize'	=> true,
		));
		
		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'articles', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $caid){
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}
		
		
		if ($id){
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
				'DD_USER' 			=> new hdropdown('user_id', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'value' => $this->pdh->get('articles', 'user_id', array($id)))),
				'DATE_PICKER'		=> $this->jquery->Calendar('date', $this->time->user_date($this->pdh->get('articles', 'date', array($id)), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_TO_PICKER'	=> $this->jquery->Calendar('show_to', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_to', array($id)))) ? $this->pdh->get('articles', 'show_to', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_FROM_PICKER'	=> $this->jquery->Calendar('show_from', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_from', array($id)))) ? $this->pdh->get('articles', 'show_from', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'PREVIEW_IMAGE'		=> new himageuploader('previewimage', array(
						'imgpath'	=> $this->pfh->FolderPath('','files'),
						'value'		=> $this->pdh->get('articles', 'previewimage', array($id)),
						'noimgfile'	=> "images/global/default-image.svg",
						'deletelink'=> 'manage_articles.php'.$this->SID.'&a='.$id.'&c='.$cid.'&delpreviewimage=true&link_hash='.$this->CSRFGetToken('delpreviewimage'),
					)),
			));
			
		} else {
			
			$this->tpl->assign_vars(array(
				'DD_CATEGORY' => new hdropdown('category', array('options' => $arrCategories, 'value' => $cid)),
				'PUBLISHED_CHECKED'=> 'checked="checked"',
				'COMMENTS_CHECKED' => 'checked="checked"',
				'PUBLISHED_RADIO' => new hradio('published', array('value' => 1)),
				'FEATURED_RADIO' => new hradio('featured', array()),
				'COMMENTS_RADIO' => new hradio('comments', array('value' => 1)),
				'VOTES_RADIO' => new hradio('votes', array()),
				'HIDE_HEADER_RADIO' => new hradio('hide_header', array()),
				'DD_USER' 		   => new hdropdown('user_id', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'value' => $this->user->id)),
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
		
		$this->tpl->add_js(
			'var pageobjects = '.json_encode($arrPageObjects).';'
		);

		$this->tpl->assign_vars(array(
			'CID' => $cid,
			'AID' => $id,
			'CATEGORY_NAME' => $this->pdh->get('article_categories', 'name', array($cid)),
			'ARTICLE_NAME' => $this->pdh->get('articles', 'title', array($id)),
		));
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('manage_articles').': '.$this->pdh->get('articles', 'title', array($id)) : $this->user->lang('add_new_article')),
			'template_file'		=> 'admin/manage_articles_edit.html',
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display() {
		$cid = $this->in->get('c', 0);
		if(!$cid) redirect('admin/manage_article_categories.php'.$this->SID);
		
		$view_list = $this->pdh->get('articles', 'id_list', array($cid));

		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_articles', 'hptt_admin_manage_articles_list');

		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_raids.php', '%link_url_suffix%' => '&amp;upd=true'), $cid);
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');

		//footer
		$raid_count = count($view_list);
		$footer_text = sprintf($this->user->lang('article_footcount'), $raid_count ,$this->user->data['user_nlimit']);
		
		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $cid2){
			if ($cid == $cid2) continue;
			$arrCategories[$cid2] = $this->pdh->get('article_categories', 'name_prefix', array($cid2)).$this->pdh->get('article_categories', 'name', array($cid2));
		}
		
		$arrMenuItems = array(
			0 => array(
				'name'	=> $this->user->lang('delete'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-trash-o',
				'perm'	=> true,
				'link'	=> '#del_articles',
			),
			
			1 => array(
				'name'	=> $this->user->lang('mass_stat_change').': '.$this->user->lang('published'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-eye',
				'perm'	=> true,
				'link'	=> '#set_published',
			),
			2 => array(
				'name'	=> $this->user->lang('mass_stat_change').': '.$this->user->lang('not_published'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-eye-slash',
				'perm'	=> true,
				'link'	=> '#set_unpublished',
			),
			3 => array(
				'name'	=> $this->user->lang('move_to_other_category').':',
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-refresh',
				'perm'	=> true,
				'link'	=> '#change_category',
				'append' => new hdropdown('new_category', array('options' => $arrCategories)),
			),
		
		);
				
		$this->confirm_delete($this->user->lang('confirm_delete_articles'));

		$this->tpl->assign_vars(array(
			'ARTICLE_LIST' 		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), 25, $footer_text),
			'PAGINATION' 		=> generate_pagination('manage_articles.php'.$sort_suffix, $raid_count, 25, $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'CATEGORY_NAME' 	=> $this->pdh->get('article_categories', 'name', array($cid)),
			'CID'				=> $cid,
			'BUTTON_MENU'		=> $this->jquery->ButtonDropDownMenu('manage_members_menu', $arrMenuItems, array("input[name=\"selected_ids[]\"]"), '', $this->user->lang('selected_articles').'...', ''),		
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_articles'),
			'template_file'		=> 'admin/manage_articles.html',
			'display'			=> true)
		);
	}
	
}
registry::register('Manage_Articles');
?>