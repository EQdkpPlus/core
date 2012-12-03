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
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_News extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $news = array();
	public $old_news = array();
	
	public function __construct(){
		$handler = array(
			'n' => array('process' => 'edit', 'check' => 'a_news_')
		);
		parent::__construct('a_news_', $handler, array('news', 'headline'), null, 'selected_ids[]', 'n');
		
		if($this->url_id) $this->news	= $this->pdh->get('news', 'news', array($this->url_id));
		$this->process();
	}

	public function delete(){
		if($this->in->exists('selected_ids') OR $this->url_id) {
			if($this->url_id) {
				$this->pdh->put('news', 'delete_news', array($this->url_id));
			} else {
				$this->pdh->put('news', 'delete_news', array($this->in->getArray('selected_ids', 'int'), true));
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('admin_delete_mnews_success'), $this->user->lang('success'), 'green');
		}
		$this->display();
	}

	// ---------------------------------------------------------
	// Process Update
	// ---------------------------------------------------------
	public function update(){
		// Update the news table
		if(is_array($this->in->getArray('raid_id', 'int'))){
			$raids_id	= implode(",", $this->in->getArray('raid_id', 'int'));
		}

		$date = $this->time->time;
		if($this->in->exists('news_date')) $date = $this->time->fromformat($this->in->get('news_date'), 1);
		$news_from	= '';
		if($this->in->exists('news_start') AND strlen($this->in->get('news_start')) AND $this->in->get('news_start') != $this->user->lang('never')) $news_from = $this->time->fromformat($this->in->get('news_start'), 1);
		$news_to	= '';
		if($this->in->exists('news_stop') AND strlen($this->in->get('news_stop')) AND $this->in->get('news_stop') != $this->user->lang('never')) $news_to = $this->time->fromformat($this->in->get('news_stop'), 1);

		$data = array(
			$this->in->get('news_headline'),
			$this->in->get('news_message', '', 'raw'),
			$this->in->get('user', $this->user->id),
			$raids_id,
			$this->in->get('nocomments', 0),
			$this->in->get('news_permissions', 0),
			$this->in->get('news_flags', 0),
			$this->in->get('category', 1),
			$date,
			$news_from,
			$news_to,
		);
		$insert_upd = 'insert_news';
		if($this->url_id) {
			$insert_upd = 'update_news';
			$data = array_merge(array($this->url_id), $data);
		}
		$this_news_id = $this->pdh->put('news', $insert_upd, $data);
		$this->pdh->process_hook_queue();

		$add_upd = ($this->url_id) ? 'update' : 'add';
		if($this_news_id) {
			$this->core->message($this->user->lang('admin_'.$add_upd.'_news_success'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('no_news_save_success'), $this->user->lang('error'), 'red');
		}
		if ($this->core->header_format == 'simple'){
			$this->tpl->add_js("jQuery.FrameDialog.closeDialog();", 'docready');
			//$this->tpl->add_js("jQuery.FrameDialog.cancelDialog();", 'docready');
		}
		$this->display();
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function edit(){
		$this->jquery->Validate('addnews', array(
			array('name' => 'news_headline', 'value' => '<br/>'.$this->user->lang('fv_required_headline')), 
			array('name' => 'news_message', 'value' => $this->user->lang('fv_required_message'))
		));
		$this->jquery->ResetValidate('addnews');

		// Build raid drop-down
		$raidlist	= $this->pdh->get('raid','id_list', array());
		$raidlist		= array_reverse($raidlist);
		$raid_ids		= (isset($this->news['showRaids_id'])) ? explode(",",$this->news['showRaids_id']) : '';

		$raids	= $raid_selected = array();
		foreach($raidlist as $key=>$row){
			$raids[$row]			= $this->time->user_date($this->pdh->get('raid', 'date', array($row))) . ' - ' . stripslashes($this->pdh->get('raid', 'event_name', array($row)));
			if(@in_array($row,$raid_ids)){
				$raid_selected[]	= $row;
			}
		}

		// Build category drop-down
		foreach($this->pdh->get('news_categories', 'category', array()) as $row){
			$categories[$row['category_id']] = $row['category_name'];
		}

		$permission_dropdown = array(
			0	=> $this->user->lang('news_permissions_all'),
			1	=> $this->user->lang('news_permissions_guest'),
			2	=> $this->user->lang('news_permissions_member'),
		);

		$this->confirm_delete($this->user->lang('confirm_delete_news').'<br />'.((isset($this->news['news_headline'])) ? sanitize($this->news['news_headline']) : ''), '', true);

		$this->tpl->add_js("$('#readmorebutton').click(function(){ $('#news_message').tinymce().execCommand('mceInsertContent',false,'{{readmore}}'); $('#readmorebutton').attr('disabled', true) });", 'docready');

		$this->jquery->Tab_header('news_tabs');
		$editor = register('tinyMCE');
		$editor->editor_normal(array(
			'autoresize'	=> true,
			'relative_urls'	=> false,
		));
		
		// Build Message text (Preview + Extended Message)
		$extendnewsbutton = true;
		if(isset($this->news['extended_message']) && strlen($this->news['extended_message'])){
			$newsmessage	= (isset($this->news['news_message']) ? $this->news['news_message'] : '').'{{readmore}}'.$this->news['extended_message'];
			$extendnewsbutton = false;
		}else{
			$newsmessage	= isset($this->news['news_message']) ? $this->news['news_message'] : '';
		}
		
		$this->jquery->tab_header('manage_news_tabs');

		$this->tpl->assign_vars(array(
			// Form vars
			'NEWS_ID'					=> $this->url_id,
			'REF'						=> $this->in->get('ref'),
			'RAID_DROPDOWN'				=> $this->jquery->MultiSelect('raid_id', $raids, $raid_selected, array('width' => 350)),

			// Form values
			'HEADLINE'					=> isset($this->news['news_headline']) ? sanitize($this->news['news_headline']): '',
			'MESSAGE'					=> $newsmessage,
			'EXTENDNEWSBUTTON_ENABLED'	=> ($extendnewsbutton) ? '' : 'disabled="disabled" ',

			'NOCOMMENTS_CHECKED'		=> (isset($this->news['nocomments']) && isset($this->news['nocomments']) && $this->news['nocomments'] == 1) ? 'checked' : '' ,
			'PERMISSION_DROPDOWN'		=> $this->html->DropDown('news_permissions', $permission_dropdown, ((isset($this->news['news_permissions'])) ? $this->news['news_permissions'] : '')),
			'STICKY_CHECKED' 			=> (isset($this->news['news_flags']) && $this->news['news_flags'] == 1) ? 'checked' : '',
			'DATE_PICKER'				=> $this->jquery->Calendar('news_date', $this->time->user_date(((isset($this->news['news_date'])) ? $this->news['news_date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'DATE_TO_PICKER'			=> $this->jquery->Calendar('news_stop', $this->time->user_date(((strlen($this->news['news_stop'])) ? $this->news['news_stop'] : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'DATE_FROM_PICKER'			=> $this->jquery->Calendar('news_start', $this->time->user_date(((strlen($this->news['news_start'])) ? $this->news['news_start'] : 0), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),

			// Language (General)
			'L_ADD_NEWS'				=> ($this->url_id) ? $this->user->lang('manage_news') : $this->user->lang('add_news'),
			'L_DATE_TO_HELP'			=> $this->html->ToolTip($this->user->lang('show_news_to_help'), '<img src="'.$this->root_path.'images/global/info.png" alt="" />'),
			'L_DATE_FROM_HELP'			=> $this->html->ToolTip($this->user->lang('show_news_from_help'), '<img src="'.$this->root_path.'images/global/info.png" alt="" />'),

			'S_NEWS_CATEGORIES'			=> ($this->config->get('enable_newscategories') == 1) ? true : false,

			'NEWS_CATEGORY_DROPDOWN'	=> $this->html->DropDown('category', $categories, ((isset($this->news['news_category_id'])) ? $this->news['news_category_id'] : ''), '', '', 'input'),
			'NEWS_USER_DROPDOWN'		=> $this->html->DropDown('user', $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), ((isset($this->news['user_id'])) ? $this->news['user_id'] : $this->user->id), '', '', 'input'),
			// Buttons
			'S_ADD'						=> ( !$this->url_id ) ? true : false)
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('addnews_title'),
			'template_file'		=> 'admin/manage_news_add.html',
			'header_format'		=> ($this->in->get('simple_head')) ? 'simple' : 'full',
			'display'			=> true,
		));
	}

	public function display(){
		$view_list			= $this->pdh->get('news', 'id_list');
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_news', 'hptt_admin_manage_news');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_news.php'.$this->SID, '%edit_url%' => 'manage_news.php'.$this->SID));
		$character_count	= count($view_list);
		$footer_text		= sprintf($this->user->lang('listnews_footcount'), $character_count);
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= ($this->in->get('sort') != '') ? '&amp;sort='.$this->in->get('sort') : '';
		
		$this->confirm_delete($this->user->lang('confirm_delete_news_multi'));

		$this->tpl->assign_vars(array(
			'NEWS_LIST'					=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), $this->user->data['user_climit'], $footer_text),
			'PAGINATION'				=> generate_pagination('manage_news.php'.$this->SID.$sort_suffix, $character_count, $this->user->data['user_climit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'			=> $hptt->get_column_count(),
			'NEWS_CATEGEGORIES'			=> ($this->config->get('enable_newscategories') == 1) ? true : false,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('listnews_title'),
			'template_file'		=> 'admin/manage_news.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_News', Manage_News::__shortcuts());
registry::register('Manage_News');
?>