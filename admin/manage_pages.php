<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

// TODO: use hptt?

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Info_Pages extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'time', 'editor'=>'tinyMCE');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_pages_man');
		if($this->in->get('check') == '1'){
			$id = $this->pdh->get('pages', 'alias_to_page', array($this->in->get('alias')));
			if (($id && $id != $this->in->get('page_id')) || is_numeric($this->in->get('alias'))){
				echo 'false';
				exit;
			}
			echo 'true';
			exit;
		}
		$handler = array(
			'cancel' => array('process' => 'display'),
			'save' => array('process' => 'save', 'csrf'=>true),
			'mode' => array(
				array('process' => 'reset_votings', 'value' => 'reset_votings', 'csrf'=>true),
				array('process' => 'delete_comments', 'value' => 'delete_comments', 'csrf'=>true)),
			'page_id' => array('process' => 'edit')
		);
		parent::__construct(false, $handler, array('pages', 'title'), null, 'page[]');
		$this->process();
	}

	public function delete() {
		if($this->in->exists('page')) {
			$retu = array();
			$page_ids = $this->in->getArray('page', 'int');
			foreach($page_ids as $id) {
				$retu[] = $this->pdh->put('pages', 'delete', array($id));
			}
			if(!in_array(false, $retu, true)) {
				$this->core->message($this->user->lang('admin_delete_pages_success'), $this->user->lang('del_suc'), 'green');
			}
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function reset_votings() {
		if ($this->in->get('page_id', 0) != "" AND $this->pdh->put('pages', 'reset_voting', array($this->in->get('page_id')))){
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf($this->user->lang('admin_reset_voting_success'), $this->pdh->get('pages', 'title', array($this->in->get('page_id')))), $this->user->lang('del_suc'), 'green');
		}
		$this->edit();
	}
		
	public function delete_comments() {
		if ($this->in->get('page_id', 0) != "" AND $this->pdh->put('pages', 'delete_comments', array($this->in->get('page_id')))) {
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf($this->user->lang('admin_delete_comments_success'), $this->pdh->get('pages', 'title', array($this->in->get('page_id')))), $this->user->lang('del_suc'), 'green');
		}
		$this->edit();
	}
		
	public function save() {
		if($this->in->get('page_id')) {

			if($this->pdh->put('pages', 'update', array(
				$this->in->get('page_id'),
				$this->in->get('title'),
				$this->in->get('page_content', '', 'htmlescape'),
				$this->in->get('alias'),
				$this->in->get('ml'),
				$this->in->getArray('vis', 'int'),
				$this->in->get('comments', 0),
				$this->in->get('voting', 0)))) {
				$this->core->message($this->user->lang('admin_update_pages_success'), $this->user->lang('save_suc'), 'green');
				$this->pdh->process_hook_queue();
			}
		} else {
			if($this->pdh->put('pages', 'add', array(
				$this->in->get('title'),
				$this->in->get('page_content', '', 'htmlescape'),
				$this->in->get('alias'),
				$this->in->get('ml'),
				$this->in->getArray('vis', 'int'),
				$this->in->get('comments', 0),
				$this->in->get('voting', 0)))) {
					$this->core->message(sprintf($this->user->lang('admin_save_pages_success'), sanitize($this->in->get('title'))), $this->user->lang('save_suc'), 'green');
					$this->pdh->process_hook_queue();
			}
		}
		$this->display();
	}

	public function edit() {		
		if ($this->in->get('page_id') != ""){
			$page_titel = $this->pdh->get('pages', 'title', array($this->in->get('page_id')));
			$this->page_data = array(
				'id'			=> $this->in->get('page_id'),
				'title'			=> $this->pdh->get('pages', 'title', array($this->in->get('page_id'))),
				'alias'			=> $this->pdh->get('pages', 'alias', array($this->in->get('page_id'))),
				'content'		=> $this->pdh->get('pages', 'content', array($this->in->get('page_id'))),
				'menu_link'		=> $this->pdh->get('pages', 'menu_link', array($this->in->get('page_id'))),
				'visibility'	=> $this->pdh->get('pages', 'visibility', array($this->in->get('page_id'))),
				'comments'		=> $this->pdh->get('pages', 'comments', array($this->in->get('page_id'))),
				'voting'		=> $this->pdh->get('pages', 'voting', array($this->in->get('page_id'))),
			);
		} else {
			$page_titel = $this->user->lang('info_create_page');
			unset($this->page_data);
		}

		//Menu-Link-Dropdown
		$mlvals[0] = $this->user->lang('info_opt_ml_0');
		$mlvals[1] = $this->user->lang('info_opt_ml_1');
		$mlvals[2] = $this->user->lang('info_opt_ml_2');
		$mlvals[3] = $this->user->lang('info_opt_ml_3');
		$mlvals[99] = $this->user->lang('info_opt_ml_99');

		if ($this->page_data['menu_link'] == 4 || count($this->pdh->get('pages', 'guildrule_page')) == 0){
			$mlvals[4] = $this->user->lang('guildrules');
		}

		//Visibility-Dropdown
		$this->user_groups = $this->pdh->get('user_groups', 'id_list');
		$visvals[0] = $this->user->lang('cl_all');
		foreach ($this->user_groups as $group){
			$visvals[$group] = $this->pdh->get('user_groups', 'name', array($group));
			$visvals_[] = $group;
		}

		if (!isset($this->page_data['visibility'])){
			$vis_selected = $visvals_;
		} else {
			$vis_selected = $this->page_data['visibility'];
		}
		$this->tpl->add_js('function info_check_form(){
					if(document.post.title.value == ""){
									show_fields_empty();
									return false;
					};
			}');
		$settings_array = array(
			'autoresize'	=> true,
		);
		$this->editor->editor_normal($settings_array);
		$this->tpl->add_js("function check_ml_dropdown(value){
					if (value == '4'){
						show_guildrules_info();
					}
				}"
		);
		
		$this->jquery->Validate('manage_pages', array(
			array('name' => 'title', 'value' => $this->user->lang('fv_required')),
			array('name' => 'page_content', 'value' => $this->user->lang('fv_required'))
		));

		$this->tpl->assign_vars(array(
			'S_NEW_PAGE'				=> ($this->in->get('page_id', 0)) ? false : true,
			'INFO_PAGE_CONTENT'			=> $this->page_data['content'],
			'INFO_PAGE_TITLE'			=> sanitize($this->page_data['title']),
			'INFO_PAGE_ALIAS'			=> sanitize($this->page_data['alias']),
			'INFO_PAGE_COMMENTS'		=> ($this->page_data['comments'] == '1') ? 'checked="checked"' : '',
			'INFO_PAGE_VOTING'			=> ($this->page_data['voting'] == '1') ? 'checked="checked"' : '',
			'PAGE_ID'					=> ($this->in->get('page_id')) ? $this->in->get('page_id') : 0,

			'JS_GUILDRULES_INFO'		=> $this->jquery->Dialog('guildrules_info', $this->user->lang('guildrules'), array('message' => $this->user->lang('guildrules_info'), 'width' => 300, 'height'=>200), 'alert'),

			'INFO_PAGE_ML_DROPDOWN'		=> $this->html->DropDown('ml', $mlvals,$this->page_data['menu_link'], '', "onchange='check_ml_dropdown(this.value);'"),
			'INFO_PAGE_VIS_DROPDOWN'	=> $this->jquery->MultiSelect('vis', $visvals, $vis_selected),

			'PAGE_HEADER'				=> $page_titel,
			'CSRF_MODE_TOKEN'			=> $this->CSRFGetToken('mode'),
			'F_ACTION'					=> 'manage_pages.php' . $this->SID. '&amp;page_id='.$this->in->get('page_id', 0))
		);
		
		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('info_manage_pages').': '.sanitize($this->page_data['title']),
			'template_file'		=> 'admin/manage_pages_add.html',
			'display'			=> true
		));
	}

	public function display(){
		$order = $this->in->get('o', '0.0');
		$red = 'RED'.str_replace('.', '', $order);
		$sort_order = array(
			0 => array('news_date desc', 'news_date'),
			1 => array('news_headline', 'news_headline desc'),
			2 => array('username', 'username desc')
		);
		$pagelist = $this->pdh->get('pages', 'id_list');
		$mlvals[0] = $this->user->lang('info_opt_ml_0');
		$mlvals[1] = $this->user->lang('info_opt_ml_1');
		$mlvals[2] = $this->user->lang('info_opt_ml_2');
		$mlvals[3] = $this->user->lang('info_opt_ml_3');
		$mlvals[4] = $this->user->lang('guildrules');
		$mlvals[99] = $this->user->lang('info_opt_ml_99');
		
		if (is_array($pagelist)){
			foreach ($pagelist as $id) {
				$visibility = array();
				if (is_array($this->pdh->get('pages', 'visibility', array($id)))){
					foreach ($this->pdh->get('pages', 'visibility', array($id)) as $group){
						if ($group!=0 && $this->pdh->get('user_groups', 'name', array($group)) AND !in_array($this->pdh->get('user_groups', 'name', array($group)), $visibility)) {
							$visibility[] = $this->pdh->get('user_groups', 'name', array($group));
						}
						if ($group == 0){
							$visibility[] = $this->user->lang('cl_all');
						}
					}
				}
				$pageslink = $this->pdh->get('pages', 'menu_link', array($id));
				$this->tpl->assign_block_vars('pages_row', array (
					'ID'			=> $id,
					'ALIAS'			=> $this->pdh->get('pages', 'alias', array($id)) ? sanitize($this->pdh->get('pages', 'alias', array($id))) : '',
					'EDIT_USER'		=> sanitize($this->pdh->get('pages', 'edit_user', array($id))),
					'EDIT_DATE'		=> sanitize($this->pdh->get('pages', 'edit_date', array($id))),
					'TITLE'			=> sanitize($this->pdh->get('pages', 'title', array($id))),
					'ML'			=> isset($mlvals[$pageslink]) ? $mlvals[$pageslink] : '',
					'VIS'			=> implode(', ', $visibility),
					'EDITED'		=> $this->time->user_date($this->pdh->get('pages', 'edit_date', array($id)), true).' ('.$this->pdh->get('user', 'name', array($this->pdh->get('pages', 'edit_user', array($id)))).')',
				));
			}
		}
		
		$this->confirm_delete($this->user->lang('info_confirm_delete'));
		$this->jquery->selectall_checkbox('selall_pages', 'page[]');
		$this->tpl->assign_vars(array (
			'S_NO_PAGES'			=> (count($pagelist) == 0 ) ? true : false,
		));
		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('info_manage_pages'),
			'template_file'		=> 'admin/manage_pages.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Info_Pages', Info_Pages::__shortcuts());
registry::register('Info_Pages');
?>