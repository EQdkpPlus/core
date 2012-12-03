<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_News_Categories extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'pfh');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_news_');
		$handler = array(
			'mode'		=> array('process' => 'delete_icon', 'value' => 'delicon', 'csrf'=>true),
			'deleteid'	=> array('process' => 'delete', 'csrf'=>true),
		);
		parent::__construct(false, $handler);
		$this->process();
	}

	public function delete(){
		if (is_numeric($this->in->get('deleteid'))){
			$this->pdh->put('news_categories', 'delete_category', array($this->in->get('deleteid')));
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function delete_icon() {
		if (is_numeric($this->in->get('id'))){
			$this->pdh->put('news_categories', 'delete_icon', array($this->in->get('id')));
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	// ---------------------------------------------------------
	// Process Add
	// ---------------------------------------------------------
	public function add() {
		// Insert the new Category
		if ($this->in->get('new_cat_name') != ""){
			$this_news_id = $this->pdh->put('news_categories', 'add_category', array($this->in->get('new_cat_name'), $this->jquery->MoveUploadedImage($this->in->get('new_cat'), $this->pfh->FolderPath('newscat_icons','eqdkp')), $this->in->get('new_color')));
		}

		//Update the whole others...
		$old_data = $this->in->getArray('news_categories', 'string');
		foreach ($old_data as $key=>$elem){
			if ($elem['name'] == "" && $key != 1){
				$this->pdh->put('news_categories', 'delete_category', array($key));		//Delete
			}else{
				$this->pdh->put('news_categories', 'update_category', array(
					$key, $elem['name'], $this->jquery->MoveUploadedImage($elem['icon'], $this->pfh->FolderPath('newscat_icons','eqdkp')), $this->in->get('user_color_'.$key)
				));		//Update
			}
		}

		// Success message
		$this->core->message($this->user->lang('admin_add_newscats_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		$this->display();
	}

	// ---------------------------------------------------------
	// Display form
	// ---------------------------------------------------------
	public function display() {
		$order			= $this->in->get('o', '0.0');
		list($tag, $direction) = explode('.', $order);
		$red			= 'RED'.str_replace('.', '', $order);

		$sort_order = array(	
			0 => array('name', array('asc', 'desc')),
			1 => array('icon', array('asc', 'desc')),
			2 => array('color', array('asc', 'desc'))
		);
		$ncategories	= $this->pdh->sort($this->pdh->get('news_categories', 'id_list'), 'news_categories', $sort_order[$tag][0], $sort_order[$tag][1][$direction]);
		foreach($ncategories as $id){
			$this->tpl->assign_block_vars('news_categories', array(
				'ID'			=> $id,
				'NAME'			=> $this->pdh->get('news_categories', 'name', array($id)),
				'COLORPICKER'	=> $this->jquery->colorpicker('user_color_'.$id, $this->pdh->get('news_categories', 'color', array($id))),
				'ICON'			=> $this->html->widget(array('type' => 'imageuploader', 'name' => 'news_categories['.$id.'][icon]', 'imgpath'	=> $this->pfh->FolderPath('newscat_icons','eqdkp'), 'value' => $this->pdh->get('news_categories', 'icon', array($id)), 'options' => array('prevheight'=>'48', 'resize'=>true, 'deletelink'=>'manage_news_categories.php'.$this->SID.'&mode=delicon&id='.$id.'&link_hash='.$this->CSRFGetToken('mode')))),
			));
		}

		$this->confirm_delete($this->user->lang('confirm_delete_newscat'), '', true, array('custom_js' => "window.location = 'manage_news_categories.php".$this->SID."&link_hash=".$this->CSRFGetToken('deleteid')."&deleteid='+selectedID"));
		$this->tpl->assign_vars(array(
			'ICON_UPLOADER'		=> $this->html->widget(array('type' => 'imageuploader', 'name' => 'new_cat', 'imgpath'	=> $this->pfh->FolderPath('newscat_icons','eqdkp'), 'options' => array('prevheight'=>'48'))),
			'NEW_COLORPICKER'	=> $this->jquery->colorpicker('new_color', ''),
			$red				=> '_red',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_newscategories'),
			'template_file'		=> 'admin/manage_news_categories.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_News_Categories', Manage_News_Categories::__shortcuts());
registry::register('Manage_News_Categories');
?>