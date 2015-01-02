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

class Manage_Raid_Groups extends page_generic {

	public function __construct(){
		$this->user->check_auths(array('a_raidgroups_man', 'a_raidgroups_grpleader'), 'or');
		
		$handler = array(
			'save'				=> array('process' => 'raid_group_save',			'csrf'=>true),
			'del_group_members'	=> array('process' => 'raid_group_members_del',		'csrf'=>true),
			'add_group_members'	=> array('process' => 'raid_group_members_save',	'csrf'=>true),
			'add_grpleader'		=> array('process' => 'process_add_grpleader',		'csrf'=>true),
			'remove_grpleader'	=> array('process' => 'process_remove_grpleader',	'csrf'=>true),
			'g'					=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('raid_groups', 'name'), null, 'raid_group_ids[]');
		$this->process();
	}
	
	//Delete User of a Group
	public function raid_group_members_del(){
		$intGroupID = $this->in->get('g', 0);
		
		if (!$this->user->check_auth('a_raidgroups_man', false) && !$this->pdh->get('raid_groups_members', 'user_is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_raidgroups_man');
		}
	
		$members = $this->in->getArray('group_raid', 'int');
		if (count($members) > 0){
			$this->pdh->put('raid_groups_members', 'delete_members_from_group', array($members, $intGroupID));
			$arrMemberNames = $this->pdh->aget('member', 'name', 0, array($members));
			$arrChanged["{L_MEMBER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_raidgroups_removed_char', $arrChanged, $intGroupID, $this->pdh->get('raid_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('del_user_from_group_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	//Add User to a Group
	public function raid_group_members_save(){
		$intGroupID = $this->in->get('g', 0);
		
		if (!$this->user->check_auth('a_raidgroups_man', false) && !$this->pdh->get('raid_groups_members', 'user_is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_raidgroups_man');
		}
		
		$members = $this->in->getArray('add_user', 'int');
		if ($members[0] == 0){unset($members[0]);};
		
		if (count($members) > 0){
			$this->pdh->put('raid_groups_members', 'add_members_to_group', array($members, $intGroupID));
			
			$arrMemberNames = $this->pdh->aget('member', 'name', 0, array($members));
			$arrChanged["{L_MEMBER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_raidgroups_added_char', $arrChanged, $intGroupID, $this->pdh->get('raid_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('add_user_to_group_success'), 'color' => 'green');
		$this->edit($message);
		
	}
	
	public function process_add_grpleader (){
		$this->user->check_auth('a_raidgroups_man');
		
		$members = $this->in->getArray('group_raid', 'int');

		if (count($members) > 0){
			$this->pdh->put('raid_groups_members', 'add_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	public function process_remove_grpleader (){
		$this->user->check_auth('a_raidgroups_man');
		
		$members = $this->in->getArray('group_raid', 'int');
		if (count($members) > 0){
			$this->pdh->put('raid_groups_members', 'remove_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	//Save the raid-Groups
	public function raid_group_save() {
		$this->user->check_auth('a_raidgroups_man');
		$retu = array();
		$group_post = $this->get_post();

		if($group_post) {
			$id_list = $this->pdh->get('raid_groups', 'id_list');
			foreach($group_post as $key=>$group) {
				$standard = ($this->in->get('raid_groups_standard') == $group['id']) ? 1 : 0;
				$func = (in_array($group['id'], $id_list)) ? 'update_grp' : 'add_grp';
				$retu[] = $this->pdh->put('raid_groups', $func, array($group['id'], $group['name'], $group['color'], $group['desc'], $standard, $key));
				$names[] = $group['name'];
				$add_name = (in_array($group['id'], $id_list)) ? '' : $group['name'];
			}

			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				if ($add_name != ""){
					$message = array('title' => $this->user->lang('save_suc'), 'text' => sprintf($this->user->lang('add_raidgroup_success'), $add_name), 'color' => 'green');
				} else {
					$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('save_raidgroup_success'), 'color' => 'green');
				}
			}
			
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_ranks_selected'), 'color' => 'grey');
		}

		$this->display($message);
	}
	
	//Delete user-groups
	public function delete() {
		$this->user->check_auth('a_raidgroups_man');
		
		$grpids = array();
		if(count($this->in->getArray('raid_group_ids', 'int')) > 0) {
			$grpids = $this->in->getArray('raid_group_ids', 'int');
		} else {
			$grpids[] = $this->in->get('raid_group_ids', 0);
		}

		if(is_array($grpids)) {
			foreach($grpids as $id) {
				
				$names[] = $this->pdh->get('raid_groups', 'name', ($id));
				$retu[] = $this->pdh->put('raid_groups', 'delete_grp', array($id));
			}

			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_nosuc'), 'text' => $this->user->lang('delete_default_group_error'), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		}
		$this->display($message);
	}
	
	//Display the Usergroup-list
	public function display($messages=false){
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		
		$grps = $this->pdh->aget('raid_groups', 'name', 0, array($this->pdh->get('raid_groups', 'id_list')));

		$key = 0;
		$new_id = 1;
		
		$this->tpl->add_js("
			$(\"#raid_groups_table tbody\").sortable({
				cancel: '.not-sortable, input, th',
				cursor: 'pointer',
			});
		", "docready");
		
		foreach($grps as $id => $name){
			$this->tpl->assign_block_vars('raid_groups', array(
				'KEY'				=> $key,
				'ID'				=> $id,
				'NAME'				=> $name,
				'DESC'				=> $this->pdh->get('raid_groups', 'desc', array($id)),
				'COLOR'				=> $this->jquery->colorpicker('raidgroup_'.$key, $this->pdh->get('raid_groups', 'color', array($id)), 'raid_groups['.$key.'][color]'),
				'USER_COUNT'		=> $this->pdh->get('raid_groups_members', 'groupcount', array($id)),
				'S_DELETABLE'		=> ($this->pdh->get('raid_groups', 'deletable', array($id))) ? true : false,
				'STANDARD'			=> ($this->pdh->get('raid_groups', 'standard', array($id))) ? 'checked="checked"' : '',
				'S_IS_GRPLEADER'	=> $this->pdh->get('raid_groups_members', 'user_is_grpleader', array($this->user->id, $id)),
			));
			$key++;
			$new_id = ($id >= $new_id) ? $id+1 : $new_id;
		}
		
		$this->confirm_delete($this->user->lang('confirm_delete_groups'));
		$this->confirm_delete($this->user->lang('confirm_delete_groups'), '', true, array('function' => 'delete_single_warning', 'force_ajax' => true));
		$this->jquery->selectall_checkbox('selall_groups', 'raid_group_ids[]');
		$this->tpl->assign_vars(array(
			'ID'		=> $new_id,
			'KEY'		=> $key,
			'S_USERGROUP_ADMIN' => $this->user->check_auth('a_raidgroups_man', false),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_raid_groups'),
			'template_file'		=> 'admin/manage_raid_groups.html',
			'display'			=> true)
		);
	}

	// ---------------------------------------------------------
	// Displays a single Group
	// ---------------------------------------------------------
	public function edit($messages=false, $group = false){
		$groupID  = ($group) ? $group : $this->in->get('g', 0);
		
		//Check Permissions
		if (!$this->user->check_auth('a_raidgroups_man', false) && !$this->pdh->get('raid_groups_members', 'user_is_grpleader', array($this->user->id, $groupID))){
			$this->user->check_auth('a_raidgroups_man');
		}
	
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$order = $this->in->get('o','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		
		//Get Users in Group
		$members = $this->pdh->get('raid_groups_members', 'member_list', array($groupID));
		
		//Get Group-name
		$group_name = $this->pdh->get('raid_groups', 'name', array($groupID));	
		
		//Get all chars
		$member_data = $this->pdh->get('member', 'id_list');
		$not_in = array();
		
		//Bring all members from Group to template
		foreach($member_data as $memberid) {
			if (in_array($memberid, $members)){
				$row = ($this->pdh->get('raid_groups_members', 'is_grpleader', array($memberid, $groupID))) ? '_grpleader' : '';
				$this->tpl->assign_block_vars('char_row'.$row, array(
					'ID'			=> $memberid,
					'NAME'			=> sanitize($this->pdh->get('member', 'name', array($memberid))),
					'CLASS'			=> $this->pdh->get('member', 'html_classname', array($memberid)),
					'LEVEL'			=> $this->pdh->get('member', 'level', array($memberid)),
					'RANK'			=> $this->pdh->get('member', 'html_rankname', array($memberid)),
					'ACTIVE'		=> ($this->pdh->get('member', 'active', array($memberid)) == '1') ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>',
				));
			} else {
				$not_in[$memberid] = $this->pdh->get('member', 'name', array($memberid));
			}
		}

		$arrMenuItems = array(
			0 => array(
				'name'	=> $this->user->lang('delete_selected_from_group'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-trash-o',
				'perm'	=> true,
				'link'	=> '#del_group_members',
			),
			1 => array(
				'name'	=> $this->user->lang('add_grpleader'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-check',
				'perm'	=> $this->user->check_auth('a_raidgroups_man', false),
				'link'	=> '#add_grpleader',
			),
			2 => array(
				'name'	=> $this->user->lang('remove_grpleader'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-times',
				'perm'	=> $this->user->check_auth('a_raidgroups_man', false),
				'link'	=> '#remove_grpleader',
			),
		
		);
		
		$this->tpl->assign_vars(array(
			'GROUP_NAME'			=> sanitize($group_name),
			$red 					=> '_red',
			'U_MANAGE_MEMBERS'		=> 'manage_raid_groups.php'.$this->SID.'&amp;g='.$groupID,
			'KEY'					=> $key,
			'ADD_USER_DROPDOWN'		=> $this->jquery->MultiSelect('add_user', $not_in, '', array('width' => 350, 'filter' => true)),
			'GRP_ID'				=> $groupID,
			'BUTTON_MENU'			=> $this->jquery->ButtonDropDownMenu('raid_groups_user_menu', $arrMenuItems, array(".usercheckbox"), '', $this->user->lang('selected_chars').'...', ''),
			'S_USERGROUP_ADMIN' 	=> $this->user->check_auth('a_raidgroups_man', false),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_raid_group').': '.sanitize($group_name),
			'template_file'		=> 'admin/manage_raid_groups_chars.html',
			'display'			=> true)
		);
	}

	private function get_post() {
		$grps = array();
		if(is_array($this->in->getArray('raid_groups', 'string'))) {
			foreach($this->in->getArray('raid_groups', 'string') as $key => $grp) {
				if(isset($grp['id']) AND $grp['id'] AND !empty($grp['name'])) {
					$grps[] = array(
						'id'		=> $this->in->get('raid_groups:'.$key.':id',0),
						'name'		=> $this->in->get('raid_groups:'.$key.':name',''),
						'desc'		=> $this->in->get('raid_groups:'.$key.':desc',''),
						'color'		=> $this->in->get('raid_groups:'.$key.':color',''),
						'deletable'	=> $this->in->get('raid_groups:'.$key.':deletable',false)
					);
				}
			}

			return $grps;
		}
		return false;
	}
	
	private function get_selected() {
		$grps = array();
		$selected = $this->in->getArray('raid_group_ids', 'int');
		if(is_array($this->in->getArray('raid_groups', 'string'))) {			
			foreach($this->in->getArray('raid_groups', 'string') as $key => $grp) {
				if(isset($grp['id']) AND in_array($grp['id'], $selected)) {
					$grps[] = array(
						'id'		=> $this->in->get('raid_groups:'.$key.':id',0),
						'name'		=> $this->in->get('raid_groups:'.$key.':name',''),
						'desc'		=> $this->in->get('raid_groups:'.$key.':desc',''),
						'color'		=> $this->in->get('raid_groups:'.$key.':color',''),
						'deletable'	=> $this->in->get('raid_groups:'.$key.':deletable',false)
					);
				}
			}
			return $grps;
		}
		return false;
	}
}
registry::register('Manage_Raid_Groups');
?>