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

class addcharacter_pageobject extends pageobject {

	private $data = array();
	private $form_build = false;
	private $adminmode = false;

	public static $shortcuts = array('form' => array('form', array('addchar')));

	public function __construct() {
		$handler = array(
			'add'	=> array('process' => 'add', 'check' => 'u_member_add', 'csrf'=>true),
			'edit'	=> array('process' => 'edit', 'check' => 'u_member_man', 'csrf'=>true),
		);

		// Permissions
		$this->user->check_auths(array('u_member_man', 'u_member_add'), 'OR');

		// Check if the user is logged in
		if (!$this->user->is_signedin()) {
			message_die($this->user->lang('uc_not_loggedin'));
		}

		//Check if Adminmode
		$this->adminmode = ($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false)) ? true : false;

		//Default Rank
		$this->data['rank_id'] = $this->pdh->get('rank', 'default', array());

		parent::__construct('u_member_', $handler, array(), null, '', 'editid');
		$this->process();
	}

	//Add a new character
	public function add(){
		$this->build_form();
		$data = $this->form->return_values();
		$data['notes'] = $this->in->get('notes');
		
		$data['overtakechar'] = false;
		
		if(!$this->adminmode && ($this->user->check_auth('u_member_conn', false) || $this->user->check_auth('u_member_conn_free', false))){
			$data['overtakechar'] = true;
		}
		
		if (strlen($data['name'])){
			$mixResult = $this->pdh->put('member', 'addorupdate_member', array(0, $data, $data['overtakechar']));

			if($this->adminmode && $mixResult){
				if($data['userid']){
					$this->pdh->put('member', 'add_char_to_user', array($mixResult, (int)$data['userid']));
				}
			}

			$this->pdh->process_hook_queue();

			if ($mixResult){
				$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
				return true;
			} else {
				$this->core->message($this->user->lang('error_saving_char'), $this->user->lang('error'), 'red', true);
				$this->display($data);
			}
		} else {
			$this->core->message($this->user->lang('missing_values').$this->user->lang('name'), $this->user->lang('error'), 'red', true);
			$this->display($data);
		}
	}

	public function edit(){
		$this->build_form();
		$data = $this->form->return_values();
		$data['notes'] = $this->in->get('notes');

		// dont allow name-change if not in adminmode
		if(!$this->adminmode) {
			unset($data['name']);
			unset($data['mainid']);
			unset($data['rankid']);
		}
		
		$intOldUserID = $this->pdh->get('member', 'user', array($this->url_id));
		$id = $this->pdh->put('member', 'addorupdate_member', array($this->url_id, $data));

		if($id){
			if($this->adminmode && $data['userid'] != $intOldUserID){
				if($data['userid']){
					$this->pdh->put('member', 'add_char_to_user', array($this->url_id, (int)$data['userid']));
				} else {
					$this->pdh->put('member', 'delete_char_from_user', array($this->url_id, $intOldUserID));
				}
			}
		}

		//Transfer character history
		if ($this->adminmode && ($this->url_id != $this->in->get('history_receiver', 0)) && $this->in->get('history_receiver', 0) > 0){
			$this->pdh->put('member', 'trans_member', array($this->url_id, $this->in->get('history_receiver', 0)));
		}
		$this->pdh->process_hook_queue();

		if ($id){
			$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
			return true;
		} else {
			$this->core->message($this->user->lang('error_saving_char'), $this->user->lang('error'), 'red', true);
			$this->display($data);
		}
	}

	public function display($member_data=array()) {
		// Read the Data
		if(empty($member_data)) {
			$member_data = $this->pdh->get('member', 'array', array($this->url_id));
			if(!is_array($member_data)) $member_data = array();
			if($this->url_id > 0) $member_data['editid'] = $this->url_id;
			// get class data which is selected by admin /such as faction/
			$member_data = array_merge($member_data, $this->game->get_admin_classdata());
			// some modifications - TODO: solve this inconsistency
			$member_data['rankid'] = $member_data['rank_id'];
			$member_data['mainid'] = $member_data['main_id'];
			$member_data['userid'] = $this->pdh->get('member', 'user', array($this->url_id));
		}
		$userid_real	= ($this->url_id > 0) ? $this->pdh->get('member', 'userid', array($this->url_id)) : $this->user->data['user_id'];

		// test
		if($this->in->get('ajax', false)) {
			$requestID = ($this->in->exists('requestid') && strlen($this->in->get('requestid'))) ? $this->in->get('requestid') : $this->config->get($this->in->get('parent'));

			$arrParents = ($this->in->exists('parents')) ? $this->in->getArray('parents') : array();

			if (($key = array_search($this->in->get('parent'), $arrParents)) !== false) {
				unset($arrParents[$key]);
			}

			$data = $this->game->get_dep_classes($this->in->get('parent'), $this->in->get('child'), $requestID);

			if(count($arrParents)){
				$arrOut = array();
				foreach($arrParents as $parentClass){
					$arrAddData = $this->game->get_dep_classes($parentClass, $this->in->get('child'), $this->in->get($parentClass));
				}

				foreach($arrAddData as $key => $val){
					if(isset($data[$key])) $arrOut[$key] = $val;
				}
			} else {
				$arrOut = $data;
			}


			$options = array(
				'options_only'	=> true,
				'options' 		=> $arrOut,
			);
			if($this->url_id > 0) {
				$options['value'] = $this->pdh->get('member', 'profile_field', array($this->url_id, $this->in->get('child')));
			}
			header('content-type: text/html; charset=UTF-8');

			echo (new hdropdown('dummy', $options))->output();
			exit;
		}

		$this->build_form($member_data);

		// Fill fields with values
		$this->form->output($member_data);

		$arrHistoryReceivers = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
		asort($arrHistoryReceivers);
		$this->tpl->assign_vars(array(
			// Permissions
			'U_IS_EDIT'				=> ($this->url_id > 0) ? true : false,
			'ADMINMODE'				=> $this->adminmode,
				'S_NEW_CHAR_ADMIN_ASSOCIATE' => (!$this->user->check_auth('u_member_conn', false) && !$this->user->check_auth('u_member_conn_free', false) ),

			// Data
			'NOTES'					=> stripslashes(((isset($member_data['notes'])) ? $member_data['notes'] : '')),
			'DD_HISTORY_RECEIVER'	=> (new hdropdown('history_receiver', array('options' => $arrHistoryReceivers, 'value' => $this->url_id)))->output(),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('uc_add_char'),
			'description'		=> $this->user->lang('uc_add_char'),
			'template_file'		=> 'addcharacter.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}

	private function build_form($member_data=array()) {
		if($this->form_build) return true;
		$this->form_build = true;
		// initialize form class
		$this->form->lang_prefix = 'addchar_';
		$this->form->use_tabs = true;
		$this->form->ajax_url = html_entity_decode($this->action);
		$this->form->validate = true;

		// Static fields
		$static_fields = array(
			'name'	=> array(
				'type'		=> 'text',
				'lang'		=> 'name',
				'required'	=> true,
				'pattern'	=> '.{1,}',
				'readonly'	=> ($this->url_id > 0 && !$this->adminmode) ? true : false,
				'size'		=> 20
			),
			'picture' => array(
				'type'			=> 'imageuploader',
				'returnFormat'	=> 'relative',
				'imgup_type'	=> 'user',
			),
		);
		if($this->url_id > 0) {
			$static_fields['editid'] = array(
				'type'	=> 'hidden',
			);
			if($this->adminmode) {
				$static_fields['status'] = array(
					'type'	=> 'radio',
					'lang'	=> 'member_active',
				);
			}
		}

		if($this->adminmode) {
			$maincharsel = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list', array(false,false,true,true))));
			if (!$this->url_id){
				asort($maincharsel);
				$maincharsel[0] = $this->user->lang('mainchar');
			} else {
				$maincharsel[$this->url_id] = $this->pdh->get('member', 'name', array($this->url_id));
				asort($maincharsel);
			}
			$static_fields['mainid']	= array(
				'type'			=> 'dropdown',
				'options'		=> $maincharsel,
				'lang'			=> 'mainchar',
			);

			$arrUsersTmp = $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list')));
			$arrUsers = array(0 => '');
			foreach($arrUsersTmp as $usrid => $usernm){
				$arrUsers[$usrid] = $usernm;
			}

			$static_fields['userid']	= array(
					'type'			=> 'dropdown',
					'options'		=> $arrUsers,
					'lang'			=> 'user',
			);


			$tmpranks		= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));
			$static_fields['rankid']	= array(
				'type'			=> 'dropdown',
				'options'		=> $tmpranks,
				'lang'			=> 'rank',
				'default'		=> $this->pdh->get('rank', 'default', array()),
			);
			$static_fields['creation_date'] = array(
					'type'			=> 'datepicker',
					'lang'			=> 'creation_date',
					'timepicker'	=> true,
					'default'		=> $this->time->time,
			);
		}
		$this->form->add_tab(array('name' => 'general', 'lang' => $this->user->lang('uc_cat_general')));
		$this->form->add_fields($static_fields, '', 'general');
		$this->form->add_tab(array('name' => 'character', 'lang' => 'character'));

		// Dynamic Tabs
		$categorynames = $this->pdh->get('profile_fields', 'categories');
		foreach($categorynames as $catname) {
			$lang = 'uc_cat_'.$catname;
			$tabname = ($this->user->lang($lang, false, false)) ? $lang : (($this->game->glang($lang)) ? $lang : $catname);
			$this->form->add_tab(array('name' => $catname, 'lang' => $tabname));
		}

		$arrGameUniqueIDs = $this->game->get_char_unique_ids();
		if (!$arrGameUniqueIDs || !is_array($arrGameUniqueIDs)) $arrGameUniqueIDs = array();

		// Dynamic Fields
		$profilefields = $this->pdh->get('profile_fields', 'fields');
		if($this->hooks->isRegistered('addcharacter_profilefields')){
			$profilefields = $this->hooks->process('addcharacter_profilefields', array($profilefields, $this->url_id, $member_data), true);
		}

		foreach($profilefields as $fieldid => $fielddata) {
			$fieldname = $fielddata['name'];
			//Set Required for Unique Options
			if (in_array($fieldname, $arrGameUniqueIDs)) {
				$fielddata['required'] = true;
				$fielddata['default'] = $this->config->get($fieldname);
			}

			if($fielddata['type'] == 'imageuploader'){
				$fielddata['returnFormat'] = 'relative';
				$fielddata['imgup_type']	= 'user';
			}

			//Make Dropdowns etc. translatable
			if(count($fielddata['options']) > 0 && $fielddata['options_language'] != ""){
				if (strpos($fielddata['options_language'], 'lang:') === 0){
					$arrSplitted = explode(':', $fielddata['options_language']);
					$arrGlang = $this->game->glang($arrSplitted[1]);
					$arrLang = (isset($arrSplitted[2])) ? $arrGlang[$arrSplitted[2]] : $arrGlang;
				} else $arrLang = $this->game->get($fielddata['options_language']);

				foreach($fielddata['options'] as $key => $val){
					if(isset($arrLang[$key])){
						$fielddata['options'][$key] = $arrLang[$key];
					}
				}
			}

			$fielddata['type'] = ($fielddata['type'] == 'link') ? 'text' : $fielddata['type'];
			$tab = (!empty($fielddata['category']) && in_array($fielddata['category'], $categorynames)) ? $fielddata['category'] : 'character';
			$fielddata['type'] = ($fielddata['type'] === 'link') ? 'text' : $fielddata['type'];
			$this->form->add_field($fieldname, $fielddata, '', $tab);
		}

	}
}
