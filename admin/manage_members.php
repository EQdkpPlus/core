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
include_once($eqdkp_root_path.'common.php');

class Manage_Members extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_members_man');
		$handler = array(
			'del' => array('process' => 'member_del', 'csrf'=>true),
			'mstatus' => array('process' => 'member_status', 'csrf'=>true),
			'rankc' => array('process' => 'member_ranks', 'csrf'=>true),
			'defrolechange'	=> array('process' => 'ajax_defaultrole', 'csrf'=>true),
		);
		parent::__construct(false, $handler, array('member', 'name'), null, 'selected_ids[]');
		$this->process();
	}
	
	public function ajax_defaultrole(){
		$this->pdh->put('member', 'change_defaultrole', array($this->in->get('defrolechange_memberid', 0), $this->in->get('defrolechange', 0)));
		$this->pdh->process_hook_queue();
		echo($this->user->lang('uc_savedmsg_roles'));
		exit;
	}

	public function member_del(){
		$member_ids = $this->in->getArray('selected_ids', 'int');
		if($member_id = $this->in->get('member_id', 0)){
			$member_ids[] = $member_id;
		}

		$pos = $neg = '';
		foreach($member_ids as $id){
			//delete member
			$membername = $this->pdh->get('member', 'name', array(intval($id)));
			if($this->pdh->put('member', 'delete_member', array(intval($id)))){
				$pos[] = $membername;
			}else{
				$neg[] = $membername;
			}
		}
		if($neg){
			$messages[]	= array('title' => $this->user->lang('del_nosuc'), 'text' => $this->user->lang('mems_no_del').implode(', ', $neg), 'color' => 'red');
		}
		if($pos){
			$messages[]	= array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('mems_del').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function member_ranks(){
		$sucs = array();
		if($member_ids = $this->in->getArray('selected_ids','int')){
			foreach($member_ids as $id){
				$sucs[$id] = $this->pdh->put('member', 'change_rank', array($id, $this->in->get('rank',0)));
			}
		}
		foreach($sucs as $id => $suc){
			if($suc){
				$pos[] = $this->pdh->get('member', 'name', array($id));
			}else{
				$neg[] = $this->pdh->get('member', 'name', array($id));
			}
		}
		if($neg){
			$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $this->user->lang('mems_no_rank_change').implode(', ', $neg), 'color' => 'red');
		}
		if($pos){
			$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_rank_change').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function member_status(){
		$sucs = array();
		if($member_ids = $this->in->getArray('selected_ids','int')){
			foreach($member_ids as $id){
				$status = ($this->pdh->get('member', 'active', array($id))) ? 0 : 1;
				$sucs[$id] = $this->pdh->put('member', 'change_status', array($id, $status));
			}
		}
		foreach($sucs as $id => $suc){
			if($suc){
				$pos[] = $this->pdh->get('member', 'name', array($id));
			}else{
				$neg[] = $this->pdh->get('member', 'name', array($id));
			}
		}
		if($neg){
			$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $this->user->lang('mems_no_status_change').implode(', ', $neg), 'color' => 'red');
		}
		if($pos){
			$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('mems_status_change').implode(', ', $pos), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function display($messages=false){
		if($messages){
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		
		$view_list		= $this->pdh->get('member', 'id_list', array(false, false, false));
		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_members', 'hptt_admin_manage_members_memberlist');
		$hptt			= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_members.php', '%link_url_suffix%' => '&mupd=true'));
		$ranks			= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list', array())));
		asort($ranks);
		$page_suffix	= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix	= '?sort='.$this->in->get('sort');

		//footer
		$character_count	= count($view_list);
		$footer_text		= sprintf($this->user->lang('listmembers_footcount'), $character_count);

		$onclose_url = "window.location.href = '".$this->server_path."admin/manage_members.php".$this->SID."';";
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=> $this->controller_path.'AddCharacter/'.$this->SID."&adminmode=1&editid='+editid+'", 'width'=>'650', 'height'=>'520', 'onclosejs'=>$onclose_url));
		$this->jquery->Dialog('AddChar', $this->user->lang('uc_add_char'), array('url'=> $this->controller_path.'AddCharacter/'.$this->SID.'&adminmode=1', 'width'=>'650', 'height'=>'520', 'onclosejs'=>$onclose_url));
		$this->confirm_delete($this->user->lang('confirm_delete_members'));

		$this->tpl->add_js("
			$('.cdefroledd').change( function(){
				$.post('manage_members.php".$this->SID."&link_hash=".$this->CSRFGetToken('defrolechange')."', { defrolechange: $(this).val(), defrolechange_memberid: $(this).attr('name').replace('defaultrole_', '') },
					function(data){
						$('#notify_container').notify('create', 'success', {text: data,title: '',custom: true,},{expires: 3000, speed: 1000});
					});
			});
		", 'docready');
		
		$arrMenuItems = array(
			0 => array(
				'name'	=> $this->user->lang('delete'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-trash-o',
				'perm'	=> true,
				'link'	=> '#del_members',
			),
			1 => array(
				'name'	=> $this->user->lang('mass_stat_change'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-level-down',
				'perm'	=> true,
				'link'	=> '#member_statchange',
			),
			2 => array(
				'name'	=> $this->user->lang('mass_rank_change'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-level-down',
				'perm'	=> true,
				'link'	=> '#member_rankchange',
				'append' => new hdropdown('rank', array('options' => $ranks)),
			),
		
		);
		
		$this->tpl->assign_vars(array(
			'SID'				=> $this->SID,
			'MEMBER_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_climit'], $footer_text),
			'PAGINATION'		=> generate_pagination('manage_members.php'.$sort_suffix, $character_count, $this->user->data['user_climit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'BUTTON_MENU'		=> $this->jquery->ButtonDropDownMenu('manage_members_menu', $arrMenuItems, array("input[name=\"selected_ids[]\"]"), '', $this->user->lang('selected_chars').'...', ''),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_members_title'),
			'template_file'		=> 'admin/manage_members.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true
		));
	}
}
registry::register('Manage_Members');
?>