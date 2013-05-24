<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date: 2013-03-23 18:01:39 +0100 (Sa, 23 Mrz 2013) $
* -----------------------------------------------------------------------
* @author		$Author: godmod $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13242 $
*
* $Id: Manage_Articles.php 13242 2013-03-23 17:01:39Z godmod $
*/

class editarticle_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'pfh');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	private $arrPermissions = array('read' => false, 'create' => false, 'update' => false, 'delete' => false, 'change_state' => false);

	public function __construct(){
		$handler = array(
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
		$intFeatured = $this->in->get('featured', 0);
		$intCategory = $cid;
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

		if ($id){
			$cid = $this->pdh->get('articles', 'category', array($id));
			$this->tpl->assign_vars(array(
				'TITLE'	=> $this->pdh->get('articles', 'title', array($id)),
				'TEXT'	=> $this->pdh->get('articles', 'text', array($id)),
				'ALIAS'	=> $this->pdh->get('articles', 'alias', array($id)),
				'TAGS'	=> implode(', ', $this->pdh->get('articles', 'tags', array($id))),
				'PUBLISHED_CHECKED' => ($this->pdh->get('articles', 'published', array($id))) ? 'checked="checked"' : '',
				'FEATURED_CHECKED' => ($this->pdh->get('articles', 'featured', array($id))) ? 'checked="checked"' : '',
				'COMMENTS_CHECKED' => ($this->pdh->get('articles', 'comments', array($id))) ? 'checked="checked"' : '',
				'VOTES_CHECKED' 	=> ($this->pdh->get('articles', 'votes', array($id))) ? 'checked="checked"' : '',
				'HIDE_HEADER_CHECKED' => ($this->pdh->get('articles', 'hide_header', array($id))) ? 'checked="checked"' : '',
				'DATE_PICKER'		=> $this->jquery->Calendar('date', $this->time->user_date($this->pdh->get('articles', 'date', array($id)), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_TO_PICKER'	=> $this->jquery->Calendar('show_to', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_to', array($id)))) ? $this->pdh->get('articles', 'show_to', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_FROM_PICKER'	=> $this->jquery->Calendar('show_from', $this->time->user_date(((strlen($this->pdh->get('articles', 'show_from', array($id)))) ? $this->pdh->get('articles', 'show_from', array($id)) : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'PREVIEW_IMAGE'		=> $this->html->widget(array(
						'fieldtype'	=> 'imageuploader',
						'name'		=> 'previewimage',
						'imgpath'	=> $this->pfh->FolderPath('','files'),
						'value'		=> $this->pdh->get('articles', 'previewimage', array($id)),
						'options'	=> array(
							'noimgfile'	=> "images/global/brokenimg.png",
							'deletelink'=> $this->SID.'&aid='.$id.'&cid='.$cid.'&delpreviewimage=true&link_hash='.$this->CSRFGetToken('delpreviewimage'),
						),
					)),
			));
			
		} else {
			
			$this->tpl->assign_vars(array(
				'PUBLISHED_CHECKED'=> 'checked="checked"',
				'COMMENTS_CHECKED' => 'checked="checked"',
				'DATE_PICKER'		=> $this->jquery->Calendar('date', $this->time->user_date($this->time->time, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_TO_PICKER'	=> $this->jquery->Calendar('show_to', $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'DATE_FROM_PICKER'	=> $this->jquery->Calendar('show_from', $this->time->user_date(0, true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
				'PREVIEW_IMAGE'		=> $this->html->widget(array(
						'fieldtype'	=> 'imageuploader',
						'name'		=> 'previewimage',
						'imgpath'	=> $this->pfh->FolderPath('logo','eqdkp'),
						'options'	=> array(
							'noimgfile'	=> "images/global/brokenimg.png"
						),
					)),
			));
		}
		
		$routing = register('routing');
		$arrPageObjects = $routing->getPageObjects();

		$this->tpl->assign_vars(array(
			'CID' => $cid,
			'AID' => $id,
			'CATEGORY_NAME' => $this->pdh->get('article_categories', 'name', array($cid)),
			'ARTICLE_NAME' => $this->pdh->get('articles', 'title', array($id)),
			'DD_PAGE_OBJECTS'	=> $this->html->Dropdown('page_objects',  $arrPageObjects),
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