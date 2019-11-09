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
			'duplicate'	=> array('process' => 'copy'),
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

	public function copy(){
		$this->core->message($this->user->lang('copy_info'), $this->user->lang('copy'));
		$this->edit($this->in->get('duplicate', 0), true);
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
			if($intCategory === 0) return false;

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

		$arrTitle = $this->in->getArray('title', 'string');
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

		//Check Name
		$strDefaultLanguage = $this->config->get('default_lang');
		if(!isset($arrTitle[$strDefaultLanguage]) || $arrTitle[$strDefaultLanguage] == ""){
			$this->core->message($this->user->lang('headline'), $this->user->lang('adduser_send_mail_error_fields'), 'red');
			$this->edit();
			return;
		}

		$strTitle = serialize($arrTitle);

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
		$retu = $pos = $messages = array();

		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
				$pos[] = stripslashes($this->pdh->get('articles', 'title', array($id)));
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

	public function edit($aid=false, $copy=false){
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
			'image_upload'	=> true,
		));

		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $caid){
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}


		if ($id){
			$this->tpl->assign_vars(array(
				'TITLE'				=> $this->pdh->get('articles', 'title', array($id)),
				'TEXT'				=> $this->pdh->get('articles', 'text', array($id)),
				'ALIAS'				=> ($copy) ? '' : $this->pdh->get('articles', 'alias', array($id)),
				'TAGS'				=> implode(', ', $this->pdh->get('articles', 'tags', array($id))),
				'ML_TITLE'			=> (new htextmultilang('title', array('value' => $this->pdh->get('articles', 'title', array($id, true)), 'required' => true, 'size' => 50)))->output(),
				'DD_CATEGORY'		=> (new hsingleselect('category', array('options' => $arrCategories, 'filter' => true, 'value' => $this->pdh->get('articles', 'category', array($id)))))->output(),
				'PUBLISHED_RADIO'	=> (new hradio('published', array('value' => ($this->pdh->get('articles', 'published', array($id))))))->output(),
				'FEATURED_RADIO'	=> (new hradio('featured', array('value' => ($this->pdh->get('articles', 'featured', array($id))))))->output(),
				'COMMENTS_RADIO'	=> (new hradio('comments', array('value' => ($this->pdh->get('articles', 'comments', array($id))))))->output(),
				'VOTES_RADIO'		=> (new hradio('votes', array('value' => ($this->pdh->get('articles', 'votes', array($id))))))->output(),
				'HIDE_HEADER_RADIO' => (new hradio('hide_header', array('value' => ($this->pdh->get('articles', 'hide_header', array($id))))))->output(),
				'DD_USER' 			=> (new hsingleselect('user_id', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'filter' => true, 'value' => $this->pdh->get('articles', 'user_id', array($id)))))->output(),
				'DATE_PICKER'		=> ($copy) ? (new hdatepicker('date', array('value' => $this->time->user_date($this->time->time, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output() : (new hdatepicker('date', array('value' => $this->time->user_date($this->pdh->get('articles', 'date', array($id)), true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'DATE_TO_PICKER'	=> ($copy) ? (new hdatepicker('show_to', array('value' => $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output() : (new hdatepicker('show_to', array('value' => $this->time->user_date(((strlen($this->pdh->get('articles', 'show_to', array($id)))) ? $this->pdh->get('articles', 'show_to', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'DATE_FROM_PICKER'	=> ($copy) ? (new hdatepicker('show_from', array('value' => $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(): (new hdatepicker('show_from', array('value' => $this->time->user_date(((strlen($this->pdh->get('articles', 'show_from', array($id)))) ? $this->pdh->get('articles', 'show_from', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'PREVIEW_IMAGE'		=> (new himageuploader('previewimage', array(
						'imgpath'	=> $this->pfh->FolderPath('','files'),
						'value'		=> $this->pdh->get('articles', 'previewimage', array($id)),
						'noimgfile'	=> "images/global/default-image.svg",
						'deletelink'=> 'manage_articles.php'.$this->SID.'&a='.$id.'&c='.$cid.'&delpreviewimage=true&link_hash='.$this->CSRFGetToken('delpreviewimage'),
					)))->output(),
			));

		} else {

			$this->tpl->assign_vars(array(
				'DD_CATEGORY'		=> (new hsingleselect('category', array('options' => $arrCategories, 'value' => $cid, 'filter' => true)))->output(),
				'ML_TITLE'			=> (new htextmultilang('title', array('value' => '', 'required' => true, 'size' => 50)))->output(),
				'PUBLISHED_CHECKED'	=> 'checked="checked"',
				'COMMENTS_CHECKED'	=> 'checked="checked"',
				'PUBLISHED_RADIO'	=> (new hradio('published', array('value' => 1)))->output(),
				'FEATURED_RADIO'	=> (new hradio('featured', array()))->output(),
				'COMMENTS_RADIO'	=> (new hradio('comments', array('value' => 1)))->output(),
				'VOTES_RADIO'		=> (new hradio('votes', array()))->output(),
				'HIDE_HEADER_RADIO'	=> (new hradio('hide_header', array()))->output(),
				'DD_USER'			=> (new hsingleselect('user_id', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'value' => $this->user->id, 'filter' => true)))->output(),
				'DATE_PICKER'		=> (new hdatepicker('date', array('value' => $this->time->user_date($this->time->time, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'DATE_TO_PICKER'	=> (new hdatepicker('show_to', array('value' => $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'DATE_FROM_PICKER'	=> (new hdatepicker('show_from', array('value' => $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'PREVIEW_IMAGE'		=> (new himageuploader('previewimage', array(
						'imgpath'	=> $this->pfh->FolderPath('logo','eqdkp'),
						'noimgfile'	=> "images/global/default-image.svg"
					)))->output(),
			));
		}

		$routing = register('routing');
		$arrPageObjects = $routing->getPageObjects();

		$this->tpl->add_js(
			'var pageobjects = '.json_encode($arrPageObjects).';'
		);

		$strArticleName		= $this->pdh->get('articles', 'title', array($id));
		$strCategoryName	= $this->pdh->get('article_categories', 'name', array($cid));
		$this->tpl->assign_vars(array(
			'CID' => $cid,
			'AID' => ($copy) ? 0 : $id,
			'CATEGORY_NAME' => $strCategoryName,
			'ARTICLE_NAME' => $strArticleName,
		));
		$this->core->set_vars([
			'page_title'		=> (($id) ? $this->user->lang('manage_articles').': '.$this->pdh->get('articles', 'title', array($id)) : $this->user->lang('add_new_article')),
			'template_file'		=> 'admin/manage_articles_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_article_categories'), 'url'=>$this->root_path.'admin/manage_article_categories.php'.$this->SID],
				['title'=>$strCategoryName, 'url'=>$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$cid],
				['title'=>$this->user->lang('manage_articles'), 'url'=>$this->root_path.'admin/manage_articles.php'.$this->SID.'&c='.$cid],
				['title'=>$strArticleName, 'url'=>' '],
			],
			'display'			=> true
		]);
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
		$page_suffix = '&amp;start='.$this->in->get('start', 0).'&c='.$cid;
		$sort_suffix = '?sort='.$this->in->get('sort').'&c='.$cid;


		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $cid2){
			if ($cid == $cid2) continue;
			$arrCategories[$cid2] = $this->pdh->get('article_categories', 'name_prefix', array($cid2)).$this->pdh->get('article_categories', 'name', array($cid2));
		}

		$arrMenuItems = array(
			0 => array(
				'type'	=> 'javascript',
				'icon'	=> 'fa-trash-o',
				'text'	=> $this->user->lang('delete'),
				'perm'	=> true,
				'js'	=> "$('#del_articles').click();",
				'append'=> '<button name="del" onclick="delete_warning();" id="del_articles" class="mainoption" type="button" style="display:none;" />',
			),
			1 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-eye',
				'text'	=> $this->user->lang('mass_stat_change').': '.$this->user->lang('published'),
				'perm'	=> true,
				'name'	=> 'set_published',
			),
			2 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-eye-slash',
				'text'	=> $this->user->lang('mass_stat_change').': '.$this->user->lang('not_published'),
				'perm'	=> true,
				'name'	=> 'set_unpublished',
			),
			3 => array(
				'type'	=> 'select',
				'icon'	=> 'fa-share',
				'text'	=> $this->user->lang('move_to_other_category').':',
				'perm'	=> true,
				'name'	=> 'change_category',
				'options' => array('new_category', $arrCategories),
			),
		);

		$this->confirm_delete($this->user->lang('confirm_delete_articles'));

		$strName = $this->pdh->get('article_categories', 'name', array($cid));
		$this->tpl->assign_vars(array(
			'ARTICLE_LIST' 		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), 25, false),
				'PAGINATION' 		=> generate_pagination('manage_articles.php'.$sort_suffix, count($view_list), 25, $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'ARTICLE_COUNT'		=> count($view_list),
			'CATEGORY_NAME' 	=> $strName,
			'S_CATEGORY_PUBLISHED' => $this->pdh->get('article_categories', 'published', array($cid)) ? true : false,
			'CID'				=> $cid,
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_articles').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
			'BUTTON_MENU'		=> $this->core->build_dropdown_menu($this->user->lang('selected_articles').'...', $arrMenuItems, '', 'manage_members_menu', array("input[name=\"selected_ids[]\"]")),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_articles'),
			'template_file'		=> 'admin/manage_articles.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_article_categories'), 'url'=>$this->root_path.'admin/manage_article_categories.php'.$this->SID],
				['title'=>$strName, 'url'=>' '],
			],
			'display'			=> true
		]);
	}

}
registry::register('Manage_Articles');
