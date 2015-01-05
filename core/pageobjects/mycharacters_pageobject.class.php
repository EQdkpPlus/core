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

class mycharacters_pageobject extends pageobject {

	public function __construct() {
		$handler = array(
			'connection_submit'	=> array('process' => 'update_connection', 'check' => 'u_member_conn', 'csrf'=>true),
			'delete_id'			=> array('process' => 'delete_char', 'check' => 'u_member_del', 'csrf'=>true),
			'maincharchange'	=> array('process' => 'ajax_mainchar', 'check' => 'u_member_man', 'csrf'=>true),
			'defrolechange'		=> array('process' => 'ajax_defaultrole', 'check' => 'u_member_man', 'csrf'=>true),
			'hide_info'			=> array('process' => 'hide_nochar_info', 'check' => 'u_member_'),
		);
		$this->user->check_auths(array('u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del'), 'OR');
	
		parent::__construct('u_member_', $handler, array());
		$this->process();
	}
	
	public function hide_nochar_info(){
		$this->pdh->put('user', 'hide_nochar_info', array($this->user->id));
	}

	public function update_connection(){
		$arrNewMembers = $this->in->getArray('member_id', 'int');
		$arrOldMembers = $this->pdh->get('member', 'connection_id', array($this->user->id));
		if(!is_array($arrOldMembers)) $arrOldMembers = array();
		$diff = array_diff($arrNewMembers, $arrOldMembers);
		$diff2 = array_diff($arrOldMembers, $arrNewMembers);
		if (count($diff) !== 0 || count($diff2) !== 0 ){
			$this->pdh->put('member', 'update_connection', array($this->in->getArray('member_id', 'int')));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}
	
	public function delete_char(){
		if($this->in->get('delete_id', 0) > 0){
			$this->pdh->put('member', 'suspend', array($this->in->get('delete_id', 0)));
			
			//Change Mainchar
			$arrOtherMembers	= $this->pdh->get('member', 'other_members', array($this->in->get('delete_id', 0)));
			$strMemID			= (count($arrOtherMembers) && isset($arrOtherMembers[0])) ? $arrOtherMembers[0] : $this->in->get('delete_id', 0);
			$this->pdh->put('member', 'change_mainid', array($this->pdh->get('member', 'connection_id', array($this->user->id)), $strMemID));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}
	
	public function ajax_mainchar(){
		$this->pdh->put('member', 'change_mainid', array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])), $this->in->get('maincharchange', 0)));
		$this->pdh->process_hook_queue();
		echo($this->user->lang('uc_savedmsg_main'));
		exit;
	}
	
	public function ajax_defaultrole(){
		$this->pdh->put('member', 'change_defaultrole', array($this->in->get('defrolechange_memberid', 0), $this->in->get('defrolechange', 0)));
		$this->pdh->process_hook_queue();
		echo($this->user->lang('uc_savedmsg_roles'));
		exit;
	}
	
	public function display(){
		// Build member drop-down
		$freemember_data = $this->pdh->get('member', 'freechars', array($this->user->data['user_id']));
		$mselect_list = $mselect_selected = array();
		foreach($freemember_data as $member_id => $member){
			$mselect_list[$member_id] = $member['name'];
			if($member['userid'] == $this->user->data['user_id']){
				$mselect_selected[] = $member_id;
			}
		}
		natcasesort($mselect_list);

		// Action Menu
		$cm_addmenu = array(
			0 => array(
				'name'		=> $this->user->lang('uc_add_char_plain'),
				'link'		=> "javascript:AddChar()",
				'icon'		=> 'fa-plus',
				'perm'		=> $this->user->check_auth('u_member_add', false),
			),
			1 => array(
				'name'		=> $this->user->lang('uc_add_char_armory'),
				'link'		=> "javascript:AddCharArmory()",
				'icon'		=> 'fa-download',
				'perm'		=> $this->game->get_importAuth('u_member_add', 'char_import') && !$this->game->get_require_apikey(),
			),
			2 => array(
				'name'		=> $this->user->lang('uc_add_massupdate'),
				'link'		=> "javascript:MassUpdateChars()",
				'icon'		=> 'fa-refresh',
				'perm'		=> $this->game->get_importAuth('a_members_man', 'char_mupdate') && !$this->game->get_require_apikey(),
			),
		);

		// Jquery stuff
		$this->jquery->Dialog('DeleteChar', '', array('message'=> $this->user->lang('uc_del_warning'), 'custom_js'=> "$('#delete_id').val(editid);$('#charactersform').submit();", 'withid'=>"editid"), 'confirm');
		$this->jquery->Dialog('AddChar', $this->user->lang('uc_add_char'), array('url'=> $this->controller_path.'AddCharacter/'.$this->SID, 'width'=>'640', 'height'=>'600', 'onclose'=> $this->env->link.$this->controller_path_plain.'MyCharacters/'.$this->SID));
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=> $this->controller_path.'AddCharacter/'.$this->SID."&editid='+editid+'", 'width'=>'640', 'height'=>'600', 'onclose'=>$this->env->link.$this->controller_path_plain.'MyCharacters/'.$this->SID));

		// The javascript for the mainchar change
		$this->tpl->add_js("
			$('.cmainradio').change( function(){
				$('#connection_submit').attr('disabled', 'disabled');
				
				$.post('".$this->SID."&link_hash=".$this->CSRFGetToken('maincharchange')."', { maincharchange: $( \"input:radio[name=mainchar]:checked\" ).val() },
					function(data){
						$('#connection_submit').removeAttr('disabled');
						$('#notify_container').notify('create', 'success', {text: data,title: '',custom: true,},{expires: 3000, speed: 1000});
					});
				});
			$('.cdefroledd').change( function(){
				$('#connection_submit').attr('disabled', 'disabled');
				$.post('".$this->SID."&link_hash=".$this->CSRFGetToken('defrolechange')."', { defrolechange: $(this).val(), defrolechange_memberid: $(this).attr('name').replace('defaultrole_', '') },
					function(data){
						$('#connection_submit').removeAttr('disabled');
						$('#notify_container').notify('create', 'success', {text: data,title: '',custom: true,},{expires: 3000, speed: 1000});
					});
			});
		", 'docready');

		// The Importer things..
		if($this->game->get_importAuth('u_member_add', 'char_import') && !$this->game->get_require_apikey()){
			$this->jquery->Dialog('AddCharArmory', $this->user->lang('uc_ext_import_sh'), array('url'=>$this->game->get_importers('char_import', true).$this->SID, 'width'=>'600', 'height'=>'300', 'onclose'=>$this->env->link.$this->controller_path_plain.'MyCharacters/'.$this->SID));
		}
		if($this->game->get_importAuth('u_', 'char_update') && !$this->game->get_require_apikey()){
			$this->jquery->Dialog('UpdateChar', $this->user->lang('uc_ext_import_sh'), array('url'=>$this->game->get_importers('char_update', true).$this->SID."&member_id='+memberid+'", 'width'=>'600', 'height'=>'300', 'onclose'=>$this->env->link.$this->controller_path_plain.'MyCharacters/'.$this->SID, 'withid'=>'memberid'));
		}

		if($this->game->get_importAuth('a_members_man', 'char_mupdate') && !$this->game->get_require_apikey()){
			$this->jquery->Dialog('MassUpdateChars', $this->user->lang('uc_cache_update'), array('url'=>$this->game->get_importers('char_mupdate', true).$this->SID, 'width'=>'600', 'height'=>'450', 'onclose'=>$this->env->link.$this->controller_path_plain.'MyCharacters/'.$this->SID));
		}

		$show_no_conn_info = false;
		if($this->pdh->get('member', 'connection_id', array($this->user->data['user_id'])) < 1 && ($this->user->is_signedin())){
			$show_no_conn_info = true;
		}

		$view_list			= $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
		$hptt_psettings		= $this->pdh->get_page_settings('manage_characters', 'hptt_manage_characters');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => register('routing')->simpleBuild('character'), '%link_url_suffix%' => '&ref=mc', '%use_controller%' => true), $this->user->id);
		$hptt->setPageRef($this->strPath);
		$footer_text		= sprintf($this->user->lang('listmembers_footcount'), ((is_array($view_list)) ? count($view_list) : 0));
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= '&amp;sort='.$this->in->get('sort');
		
		$this->tpl->assign_vars(array(
			'CHAR_LIST'				=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), $this->user->data['user_climit'], $footer_text),
			'CHAR_PAGINATION'		=> generate_pagination($this->SID.$sort_suffix, ((is_array($view_list)) ? count($view_list) : 0), $this->user->data['user_climit'], $this->in->get('start', 0)),
			'NEW_CHARS'				=> $this->user->check_auth('u_member_add', false),
			'CONNECT_CHARS'			=> $this->user->check_auth('u_member_conn', false),
			'DELETE_CHARS'			=> $this->user->check_auth('u_member_del', false),

			// JS Code
			'JS_CONNECTIONS'		=> $this->jquery->MultiSelect('member_id', $mselect_list, $mselect_selected, array('width' => 350, 'height' => 180, 'filter'=>true)),
			'ADD_MENU'				=> $this->jquery->DropDownMenu('colortab', $cm_addmenu, '<i class="fa fa-plus fa-lg"> </i> '.$this->user->lang('uc_add_char')),

			'S_SHOW_NO_CONN_INFO'	=> $show_no_conn_info,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_members_titl'),
			'template_file'		=> 'mycharacters.html',
			'display'			=> true)
		);	
	}
}
?>