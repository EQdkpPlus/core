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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManageProfileFields extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array(
			'enable' => array('process' => 'enable', 'csrf'=>true),
			'disable' => array('process' => 'disable', 'csrf'=>true),
			'new'	=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('profile_fields', 'language'), null, 'del_ids[]');
		$this->process();
	}

	public function enable(){
		if ($this->in->get('enable') != ""){
			$result = $this->pdh->put('profile_fields', 'enable_field', array($this->in->get('enable')));
		}
		
		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => sprintf($this->user->lang('pf_enable_suc'), $this->in->get('enable')), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => sprintf($this->user->lang('pf_enable_nosuc'), $this->in->get('enable')), 'color' => 'red');
		}
		$this->display($message);
		
	} //close function

	public function disable(){
		if ($this->in->get('disable') != ""){
			$result = $this->pdh->put('profile_fields', 'disable_field', array($this->in->get('disable')));
		}

		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => sprintf($this->user->lang('pf_disable_suc'), $this->in->get('disable')), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => sprintf($this->user->lang('pf_disable_nosuc'), $this->in->get('disable')), 'color' => 'red');
		}
		$this->display($message);
	}

	public function delete(){
		$del_ids = $this->in->getArray('del_ids', 'string');
		if ($del_ids) {
			$result = $this->pdh->put('profile_fields', 'delete_fields', array($del_ids));
			$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('pf_delete_suc'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('pf_delete_nosuc'), 'color' => 'red');
		}
		$this->display($message);
	}
	
	public function add(){
		if ($this->in->get('id') != ""){
		//Update
			$result = $this->pdh->put('profile_fields', 'update_field', array($this->in->get('id')));
		} else {
		//Insert
			$result = $this->pdh->put('profile_fields', 'insert_field', array());
		}
		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('pf_save_suc'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('pf_save_nosuc'), 'color' => 'red');
		}
		$this->display($message);
	}

	public function edit(){
		$field_data = $this->pdh->get('profile_fields', 'fields', array($this->in->get('edit')));
		$types = array(
			'text'	=> 'Text',
			'int'		=> 'Integer',
			'dropdown' => 'Dropdown',
		);

		$categories = array(
			'profiler'	=> ($this->game->glang('uc_cat_profiler')) ? $this->game->glang('uc_cat_profiler') : $this->user->lang('uc_cat_profiler'),
			'skills'	=> ($this->game->glang('uc_cat_skills')) ? $this->game->glang('uc_cat_skills') : $this->user->lang('uc_cat_skills'),
			'character'	=> ($this->game->glang('uc_cat_character')) ? $this->game->glang('uc_cat_character') : $this->user->lang('uc_cat_character'),
			'profession'=> ($this->game->glang('uc_cat_profession')) ? $this->game->glang('uc_cat_profession') : $this->user->lang('uc_cat_profession'),
		);

		$this->tpl->assign_vars(array (
			'S_EDIT'					=> true,
			'L_IMAGE_NOTE'				=> sprintf($this->user->lang('profilefield_image_note'), $this->game->get_game()),
			'F_PAGE_MANAGER'			=> 'manage_profilefields.php'.$this->SID,
			'ID'						=> ($this->in->get('edit')) ? $this->in->get('edit') : '',
			'LANGUAGE'					=> (isset($field_data['language'])) ? $field_data['language'] : '',
			'TYPE_DD'					=> $this->html->DropDown('type', $types, ((isset($field_data['fieldtype'])) ? $field_data['fieldtype'] : ''), '', ' onchange="handle_fieldtypes(this.value);"'),
			'CATEGORY_DD'				=> $this->html->DropDown('category', $categories, ((isset($field_data['category'])) ? $field_data['category'] : '')),
			'SIZE'						=> (isset($field_data['size'])) ? $field_data['size'] : '',
			'IMAGE'						=> (isset($field_data['image'])) ? $field_data['image'] : '',
			'S_SHOW_OPTIONS'			=> (isset($field_data['fieldtype']) && $field_data['fieldtype'] == 'dropdown') ? '' : 'style="display:none;"',
		));

		if (isset($field_data['fieldtype']) && $field_data['fieldtype'] == 'dropdown'){
			foreach ($field_data['options'] as $key => $value){
				$this->tpl->assign_block_vars('options_row', array(
					'ID'		=> $key,
					'LANGUAGE'	=> $value,
				));
			}
		}

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('manage_profilefields'),
			'template_file'		=> 'admin/manage_profilefields.html',
			'display'			=> true
		));
	}

	public function display($message = false){
		if($message){
			$this->pdh->process_hook_queue();
			$this->core->messages($message);
		}
		$this->confirm_delete($this->user->lang('confirm_del_profilefields'));
		$fields = $this->pdh->get('profile_fields', 'fields');
		if (is_array($fields)) {
			foreach ($fields as $key=>$value){
				$this->tpl->assign_block_vars('profile_row', array (
					'ID'			=> $key,
					'TYPE'			=> $value['fieldtype'],
					'CATEGORY'		=> ($this->game->glang('uc_cat_'.$value['category'])) ?  $this->game->glang('uc_cat_'.$value['category']) : $this->user->lang('uc_cat_'.$value['category']),
					'SIZE'			=> $value['size'],
					'VISIBLE'		=> $value['visible'],
					'NAME'			=> $value['language'],
					'ENABLED_ICON'	=> ($value['enabled'] == 1) ? 'green' : 'red',
					'ENABLE'		=> ($value['enabled'] == 1) ? 'disable' : 'enable',
					'L_ENABLE'		=> ($value['enabled'] == 1) ? $this->user->lang('deactivate') : $this->user->lang('activate'),
					'U_EDIT'		=> 'manage_profilefields.php'.$this->SID.'&amp;edit='.$key,
					'U_ENABLE'		=> 'manage_profilefields.php'.$this->SID.'&amp;'.(($value['enabled'] == 1) ? 'disable' : 'enable').'='.$key.'&amp;link_hash='.(($value['enabled'] == 1) ? $this->CSRFGetToken('disable') : $this->CSRFGetToken('enable')),
					'S_UNDELETABLE'	=> $value['undeletable'],
				));
			}
		}

		$this->jquery->selectall_checkbox('selall_pfields', 'del_ids[]');
		$this->tpl->assign_vars(array (
			'FC_PROFILEFIELDS'			=> sprintf($this->user->lang('profilefields_footcount'), count($fields)),
		));

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('manage_profilefields'),
			'template_file'		=> 'admin/manage_profilefields.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageProfileFields', ManageProfileFields::__shortcuts());
registry::register('ManageProfileFields');
?>