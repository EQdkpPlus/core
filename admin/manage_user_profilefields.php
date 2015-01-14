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

class ManageUserProfileFields extends page_generic {

	public function __construct(){
		
		$this->user->check_auth('a_users_profilefields');
		
		$handler = array(
			'savesort'	=> array('process' => 'save_sort', 'csrf'=>true),
			'enable'	=> array('process' => 'enable', 'csrf'=>true),
			'disable'	=> array('process' => 'disable', 'csrf'=>true),
			'new'		=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('user_profilefields', 'name'), null, 'del_ids[]');
		$this->process();
	}
	
	public function save_sort(){
		$arrSortOrder = $this->in->getArray('sort', 'int');
		foreach($arrSortOrder as $intSortID => $intFieldID){
			$this->pdh->put('user_profilefields', 'set_sortation', array($intFieldID, $intSortID));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('save_suc'), 'color' => 'green');
		$this->display($message);
	}

	public function enable(){
		$intFieldID = $this->in->get('enable', 0);
		
		if ($intFieldID){
			$result = $this->pdh->put('user_profilefields', 'enable_field', array($intFieldID));
		}
		
		$strName = $this->pdh->geth('user_profilefields', 'name', array($intFieldID));
		
		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => sprintf($this->user->lang('pf_enable_suc'), $strName), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => sprintf($this->user->lang('pf_enable_nosuc'), $strName), 'color' => 'red');
		}
		$this->display($message);
		
	} //close function

	public function disable(){
		$intFieldID = $this->in->get('disable', 0);
		
		if ($intFieldID){
			$result = $this->pdh->put('user_profilefields', 'disable_field', array($intFieldID));
		}

		$strName = $this->pdh->geth('user_profilefields', 'name', array($intFieldID));
		
		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => sprintf($this->user->lang('pf_disable_suc'), $strName), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => sprintf($this->user->lang('pf_disable_nosuc'), $strName), 'color' => 'red');
		}
		$this->display($message);
	}

	public function delete(){
		$del_ids = $this->in->getArray('del_ids', 'int');
		if ($del_ids) {
			$result = $this->pdh->put('user_profilefields', 'delete_fields', array($del_ids));
			$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('pf_delete_suc'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('pf_delete_nosuc'), 'color' => 'red');
		}
		$this->display($message);
	}
		
	public function add(){
		$intFieldID = $this->in->get('id', 0);
		
		$form = register('form', array('user_profilefield_edit'));
		$form->validate = true;
		$form->lang_prefix = 'userpf_sett_';
		
		$form->add_fields($this->edit_settings());
		
		$arrValues = $form->return_values();
		
		// Error-check the form
		if($form->error) {
			$this->edit($arrValues);
			return;
		}
		
		$options = array();
		if ($arrValues['type'] == 'dropdown' || $arrValues['type'] == 'multiselect'){
			$in_options_id = $this->in->getArray('option_id', 'string');
			$in_options_lang = $this->in->getArray('option_lang', 'string');
			foreach ($in_options_id as $key=>$value){
				if ($value != "" && $in_options_lang[$key] != ""){
					$options[$value] = $in_options_lang[$key];
				}
			}
		}
		
		if ($intFieldID){
			//Update
			$result = $this->pdh->put('user_profilefields', 'update_field', array($intFieldID, $arrValues, $options));
		} else {
			//Insert
			$result = $this->pdh->put('user_profilefields', 'insert_field', array($arrValues, $options));
		}
		//Handle Result
		if ($result){
			$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('pf_save_suc'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('pf_save_nosuc'), 'color' => 'red');
		}
		$this->display($message);
	}
	
	private function edit_settings(){
		$arrFields = array(
				'name' => array(
						'type' 		=> 'textmultilang',
						'required'	=> true,
						'size'		=> 30,
				),
				'lang_var' => array(
						'type' 		=> 'text',
						'size'		=> 30,
				),
				'type' => array(
						'type'		=> 'dropdown',
						'options'	=> array(
								'text'		=> 'Text',
								'int'		=> 'Integer',
								'dropdown'	=> 'Dropdown',
								'link'		=> 'Link',
								'multiselect' => 'Multiselect',
						),
						'required'	=> true
				),
				'length' => array(
						'type'	 => 'int',
						'length' =>	4,
						'default'=> 20,
				),
				'minlength' => array(
						'type'	 => 'int',
						'length' =>	4,
						'default'=> 6,
				),
				'validation' => array(
						'type' 		=> 'text',
						'size'		=> 30,
				),
				'required' => array(
						'type' => 'radio',
				),
				'show_on_registration' => array(
						'type' => 'radio',
				),
				'enabled' => array(
						'type' => 'radio',
				),
				'is_contact' => array(
						'type' => 'radio',
						'dependency' => array(1=>array('contact_url')),
				),
				'contact_url' => array(
						'type' => 'text',
						'size'	=> 30,
				),
				'icon_or_image' => array(
						'type' => 'text',
						'size'	=> 30,
				),
		);
		
		if ((int)$this->config->get('cmsbridge_active') == 1){
			$arrAvailableFields = $this->bridge->get_available_sync_fields();
			if (is_array($arrAvailableFields) && count($arrAvailableFields)){
				
				$arrAvailableFields[''] = '';
				
				$arrFields['bridge_field'] = array(
					'type'	  => 'dropdown',
					'options' => $arrAvailableFields
				);
			}
		}
		
		return $arrFields;
	}

	public function edit($arrValues=false){
		$intFieldID = $this->in->get('edit', 0);
		
		if ($arrValues !== false){
			$field_data = $arrValues;
		}elseif($intFieldID){
			$field_data = $this->pdh->get('user_profilefields', 'data', array($this->in->get('edit')));
		} else $field_data = array('type' => 'text');

		$form = register('form', array('user_profilefield_edit'));
		$form->validate = true;
		$form->lang_prefix = 'userpf_sett_';
		
		
		$form->add_fields($this->edit_settings());
		$form->output($field_data);
		

		$this->tpl->assign_vars(array (
			'ID'						=> $intFieldID,
			'LANGUAGE'					=> $this->pdh->geth('user_profilefields', 'name', array($intFieldID)),
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
	fields.find(".input").val("");
	$("#addopt_icon").remove();
	$("#new_options").append(fields);
});
$("#type").change(function(){
	if($("#type").attr("value") == "dropdown" || $("#type").attr("value") == "multiselect") {
		$("#options_row").show();
	} else {
		$("#options_row").hide();
	}
});', 'docready');

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('manage_userpf_edit'),
			'template_file'		=> 'admin/manage_user_profilefields_edit.html',
			'display'			=> true
		));
	}

	public function display($message = false){
		if($message){
			$this->pdh->process_hook_queue();
			$this->core->messages($message);
		}
		
		$this->confirm_delete($this->user->lang('confirm_del_profilefields'));
		
		$fields = $this->pdh->get('user_profilefields', 'fields');
		
		$arrAvailableBridgeFields = $this->bridge->get_available_sync_fields();
				
		if (is_array($fields)) {
			foreach ($fields as $key=>$value){
				$this->tpl->assign_block_vars('profile_row', array (
					'ID'			=> $value['id'],
					'TYPE'			=> ucfirst($value['type']),
					'NAME'			=> $this->pdh->geth('user_profilefields', 'name', array($value['id'])),
					'IS_CONTACT'	=> ($value['is_contact'] == 1) ? true : false,
					'IS_REQUIRED'	=> ($value['required'] == 1) ? true : false,
					'IS_SHOWN_ON_REG'	=> ($value['show_on_registration'] == 1) ? true : false,
					'BRIDGE_FIELD'	=> isset($arrAvailableBridgeFields[$value['bridge_field']]) ? $arrAvailableBridgeFields[$value['bridge_field']] : '',
					'ENABLED_ICON'	=> ($value['enabled'] == 1) ? 'eqdkp-icon-online' : 'eqdkp-icon-offline',
					'ENABLE'		=> ($value['enabled'] == 1) ? 'fa-eye-slash grey' : 'fa-eye',
					'L_ENABLE'		=> ($value['enabled'] == 1) ? $this->user->lang('deactivate') : $this->user->lang('activate'),
					'S_EDITABLE'	=> ($value['editable'] == 1) ? true : false,
					'U_EDIT'		=> 'manage_user_profilefields.php'.$this->SID.'&amp;edit='.$key,
					'U_ENABLE'		=> 'manage_user_profilefields.php'.$this->SID.'&amp;'.(($value['enabled'] == 1) ? 'disable' : 'enable').'='.$key.'&amp;link_hash='.(($value['enabled'] == 1) ? $this->CSRFGetToken('disable') : $this->CSRFGetToken('enable')),
				));
			}
		}

		$this->jquery->selectall_checkbox('selall_pfields', 'del_ids[]');
		$this->tpl->assign_vars(array (
			'FC_PROFILEFIELDS'		=> sprintf($this->user->lang('profilefields_footcount'), count($fields)),
			'S_BRIDGE_FIELD'		=> ((int)$this->config->get('cmsbridge_active') == 1) ? true : false,
		));
		
		$this->tpl->add_js("
			$(\"#userprofilefield_table tbody\").sortable({
				cancel: '.not-sortable, input, select, th',
				cursor: 'pointer',
			});
		", "docready");

		$this->core->set_vars(array (
			'page_title'		=> $this->user->lang('manage_userpf'),
			'template_file'		=> 'admin/manage_user_profilefields.html',
			'display'			=> true
		));
	}
}
registry::register('ManageUserProfileFields');
?>