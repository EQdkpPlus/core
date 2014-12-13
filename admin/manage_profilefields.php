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
include_once ($eqdkp_root_path . 'common.php');

class ManageProfileFields extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array(
			'enable'	=> array('process' => 'enable', 'csrf'=>true),
			'disable'	=> array('process' => 'disable', 'csrf'=>true),
			'new'		=> array('process' => 'edit'),
			'reset'		=> array('process' => 'process_reset','csrf'=>true)
		);
		parent::__construct(false, $handler, array('profile_fields', 'lang'), null, 'del_ids[]');
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
	
	public function process_reset(){
		$this->game->AddProfileFields();
		$this->display();
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
		if($this->in->get('edit')) $field_data = $this->pdh->get('profile_fields', 'fields', array($this->in->get('edit')));
		else $field_data = array('lang' => '', 'options_language' => '', 'type' => '', 'category' => '', 'size' => '', 'image' => '', 'options' => array());
		$types = array(
			'text'		=> 'Text',
			'int'		=> 'Integer',
			'dropdown'	=> 'Dropdown',
			'link'		=> 'Link',
			'multiselect' => 'Multiselect',
		);

		$categories = array(
			'profiler'	=> ($this->game->glang('uc_cat_profiler')) ? $this->game->glang('uc_cat_profiler') : $this->user->lang('uc_cat_profiler'),
			'skills'	=> ($this->game->glang('uc_cat_skills')) ? $this->game->glang('uc_cat_skills') : $this->user->lang('uc_cat_skills'),
			'character'	=> ($this->game->glang('uc_cat_character')) ? $this->game->glang('uc_cat_character') : $this->user->lang('uc_cat_character'),
			'profession'=> ($this->game->glang('uc_cat_profession')) ? $this->game->glang('uc_cat_profession') : $this->user->lang('uc_cat_profession'),
		);

		$this->tpl->assign_vars(array (
			'L_IMAGE_NOTE'				=> sprintf($this->user->lang('profilefield_image_note'), $this->game->get_game()),
			'F_PAGE_MANAGER'			=> 'manage_profilefields.php'.$this->SID,
			'ID'						=> $this->in->get('edit'),
			'LANGUAGE'					=> $field_data['lang'],
			'OPTIONS_LANGUAGE'			=> $field_data['options_language'],
			'TYPE_DD'					=> new hdropdown('type', array('options' => $types, 'value' => $field_data['type'], 'id' => 'type_dd')),
			
			'CATEGORY_DD'				=> new hdropdown('category', array('options' => $categories, 'value' => $field_data['category'])),
			'SIZE'						=> $field_data['size'],
			'IMAGE'						=> $field_data['image'],
			'S_SHOW_OPTIONS'			=> ($field_data['type'] == 'dropdown' || $field_data['type'] == 'multiselect') ? '' : 'style="display:none;"',
		));

		if ($field_data['type'] == 'dropdown' || $field_data['type'] == 'multiselect'){
			foreach ($field_data['options'] as $key => $value){
				$this->tpl->assign_block_vars('options_row', array(
					'ID'		=> $key,
					'LANGUAGE'	=> $value,
				));
			}
		}
		
		$this->tpl->add_js('
$("#addopt_icon").click(function(){
	var fields = $("#new_options > span:last-child").clone(true);
	$("#addopt_icon").remove();
	$("#new_options").append(fields);
});
$("#type_dd").change(function(){
	if($("#type_dd").attr("value") == "dropdown" || $("#type_dd").attr("value") == "multiselect") {
		$("#options_row").show();
	} else {
		$("#options_row").hide();
	}
});', 'docready');

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('manage_profilefields'),
			'template_file'		=> 'admin/manage_profilefields_edit.html',
			'display'			=> true
		));
	}

	public function display($message = false){
		if($message){
			$this->pdh->process_hook_queue();
			$this->core->messages($message);
		}
		
		$this->jquery->Dialog('ResetProfileFields', '', array('custom_js'=> "window.location = 'manage_profilefields.php".$this->SID."&reset=true&link_hash=".$this->CSRFGetToken('reset')."';", 'message'=> $this->user->lang('reset_profilefieldstext')), 'confirm');
		
		
		$this->confirm_delete($this->user->lang('confirm_del_profilefields'));
		$fields = $this->pdh->get('profile_fields', 'fields');
		if (is_array($fields)) {
			foreach ($fields as $key=>$value){
				$this->tpl->assign_block_vars('profile_row', array (
					'ID'			=> $key,
					'TYPE'			=> $value['type'],
					'CATEGORY'		=> ($this->game->glang('uc_cat_'.$value['category'])) ?  $this->game->glang('uc_cat_'.$value['category']) : $this->user->lang('uc_cat_'.$value['category']),
					'SIZE'			=> $value['size'],
					'VISIBLE'		=> $value['visible'],
					'NAME'			=> $value['lang'],
					'ENABLED_ICON'	=> ($value['enabled'] == 1) ? 'eqdkp-icon-online' : 'eqdkp-icon-offline',
					'ENABLE'		=> ($value['enabled'] == 1) ? 'fa-eye-slash grey' : 'fa-eye',
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
registry::register('ManageProfileFields');
?>