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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_User_Groups extends page_generic {

	public function __construct(){
		$this->user->check_auths(array('a_usergroups_man', 'a_usergroups_grpleader'), 'or');

		$handler = array(
			'saveorder' => array('process' => 'saveorder', 'csrf'=> true ),
			'del_group_users' => array('process' => 'user_group_users_del', 'csrf'=>true),
			'add_group_users' => array('process' => 'user_group_users_save', 'csrf'=>true),
			'save_group' 	=> array('process' => 'save_group', 'csrf'=>true),
			'add_grpleader' => array('process' => 'process_add_grpleader', 'csrf'=>true),
			'remove_grpleader' => array('process' => 'process_remove_grpleader', 'csrf'=>true),
			'user_group_perms' => array('process' => 'display_grouppermlist'),
			'grp_perms' => array('process' => 'display_grouppermlist'),
			'users' => array('process' => 'edit_users'),
			'g' => array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('user_groups', 'name'), null, 'user_group_ids[]');
		$this->process();
	}

	//Delete User of a Group
	public function user_group_users_del(){
		$intGroupID = $this->in->get('g', 0);

		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('user_groups_users', 'is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_usergroups_man');
		}

		$members = $this->in->getArray('group_user', 'int');
		if ($intGroupID == 2){
			$key = array_search($this->user->id, $members);
			if ($key !== false) unset($members[$key]);
		}

		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'delete_users_from_group', array($members, $intGroupID));
			$arrMemberNames = $this->pdh->aget('user', 'name', 0, array($members));
			$arrChanged["{L_USER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_usergroups_removed_user', $arrChanged, $intGroupID, $this->pdh->get('user_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('del_user_from_group_success'), 'color' => 'green');
		$this->edit_users($message);
	}

	//Add User to a Group
	public function user_group_users_save(){
		$intGroupID = $this->in->get('g', 0);

		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('user_groups_users', 'is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_usergroups_man');
		}

		$members = $this->in->getArray('add_user', 'int');
		if ($members[0] == 0){unset($members[0]);};

		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'add_users_to_group', array($members, $intGroupID));

			$arrMemberNames = $this->pdh->aget('user', 'name', 0, array($members));
			$arrChanged["{L_USER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_usergroups_added_user', $arrChanged, $intGroupID, $this->pdh->get('user_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('add_user_to_group_success'), 'color' => 'green');
		$this->edit_users($message);

	}

	public function process_add_grpleader (){
		$this->user->check_auth('a_usergroups_man');

		$members = $this->in->getArray('group_user', 'int');
		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'add_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit_users($message);
	}

	public function process_remove_grpleader (){
		$this->user->check_auth('a_usergroups_man');

		$members = $this->in->getArray('group_user', 'int');
		if (count($members) > 0){
			$this->pdh->put('user_groups_users', 'remove_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit_users($message);
	}

	//Save order
	public function saveorder(){
		$this->user->check_auth('a_usergroups_man');

		if(is_array($this->in->getArray('user_groups', 'string'))) {
			foreach($this->in->getArray('user_groups', 'string') as $key => $val){
				$this->pdh->put('user_groups', 'update_sortid', array(intval($val['id']), $key));
			}
		}

		//Standard
		$intDefault = $this->in->get('user_groups_standard', 0);
		$this->pdh->put('user_groups', 'update_default', array($intDefault));

		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('save_suc'), 'color' => 'green');
		$this->display($message);
	}


	//Delete user-groups
	public function delete() {
		$this->user->check_auth('a_usergroups_man');

		$grpids = array();
		if(count($this->in->getArray('user_group_ids', 'int')) > 0) {
			$grpids = $this->in->getArray('user_group_ids', 'int');
		} else {
			$grpids[] = $this->in->get('user_group_ids', 0);
		}

		if(is_array($grpids)) {
			foreach($grpids as $id) {

				$names[] = $this->pdh->get('user_groups', 'name', ($id));
				$retu[] = $this->pdh->put('user_groups', 'delete_grp', array($id));
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

		$grps = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));

		$this->tpl->add_js("
			$(\"#user_groups_table tbody\").sortable({
				cancel: '.not-sortable, input, th',
				cursor: 'pointer',
			});
		", "docready");

		foreach($grps as $id => $name){
			$this->tpl->assign_block_vars('user_groups', array(
				'ID'	=> $id,
				'NAME'	=> $name,
				'DESC'	=> $this->pdh->get('user_groups', 'desc', array($id)),
				'USER_COUNT'	=> $this->pdh->get('user_groups_users', 'groupcount', array($id)),
				'S_DELETABLE' => ($this->pdh->get('user_groups', 'deletable', array($id))) ? true : false,

				'S_NO_STANDARD' => ($id == 2 || $id == 3) ? true : false,
				'STANDARD'	=> ($this->pdh->get('user_groups', 'standard', array($id))) ? 'checked="checked"' : '',
				'HIDE'	=> ($this->pdh->get('user_groups', 'hide', array($id))) ? 'checked="checked"' : '',
				'S_IS_GRPLEADER' => $this->pdh->get('user_groups_users', 'is_grpleader', array($this->user->id, $id)),
			));
		}

		$this->confirm_delete($this->user->lang('confirm_delete_groups'));
		$this->confirm_delete($this->user->lang('confirm_delete_groups'), '', true, array('function' => 'delete_single_warning', 'force_ajax' => true));

		$this->jquery->selectall_checkbox('selall_groups', 'user_group_ids[]');

		$this->tpl->assign_vars(array(
			'S_USERGROUP_ADMIN' => $this->user->check_auth('a_usergroups_man', false),
			'GROUP_COUNT' 		=> count($grps),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_user_groups'),
			'template_file'		=> 'admin/manage_user_groups.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_user_groups'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}



	//Process: Save permissions of a group
	public function save_group(){
		$this->user->check_auth('a_usergroups_man');
		$intGroupID = $this->in->get('g', 0);

		//General settings
		$strName = $this->in->get('name');
		$strDesc = $this->in->get('desc');
		$intHidden = $this->in->get('hide', 0);
		$intTeam = $this->in->get('team', 0);

		if($intGroupID > 0){
			$retu = $this->pdh->put('user_groups', 'update_grp', array($intGroupID, $strName, $strDesc, $intHidden, $intTeam));
		} else {
			$retu = $this->pdh->put('user_groups', 'add_grp', array($strName, $strDesc, $intHidden, $intTeam));
			if($retu){
				$intGroupID = $retu;
			} else {
				$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('save_nosuc'), 'color' => 'red');
				$this->display($message);
				return;
			}
		}

		//Permissions
		if ($intGroupID != 2){
			$auth_defaults = $this->acl->get_auth_defaults(false);
			$group_permissions = $this->acl->get_group_permissions($intGroupID, true);
			$memberships = $this->acl->get_user_group_memberships($this->user->data['user_id']);

			$arrChanged = array();
			foreach ( $auth_defaults as $auth_value => $auth_setting ) {
				$r_auth_id    = $this->acl->get_auth_id($auth_value);
				$r_auth_value = $auth_value;
				$chk_auth_value = ( isset($group_permissions[$auth_value]) && $group_permissions[$auth_value] == "Y") ? 'Y' : 'N';
				$db_auth_value  = ( $this->in->get($r_auth_value) == "Y" )                      ? 'Y' : 'N';

				if ( $chk_auth_value != $db_auth_value ) {
					$this->update_auth_groups($r_auth_id, $db_auth_value, $intGroupID);
					$arrChanged[$r_auth_value] = array('old' => $chk_auth_value, 'new' => $db_auth_value);
				}
			}
		}

		//Articlecategory permissions
		if($this->user->check_auth("a_article_categories_man", false)){
			$arrArticlecategoryPerms = $this->in->getArray('perm');
			foreach($arrArticlecategoryPerms as $intCategoryId => $arrPerms){
				$this->pdh->put('article_categories', 'update_permission_for_group', array($intCategoryId, $intGroupID, $arrPerms));
			}
		}

		if(count($arrChanged)) $this->logs->add('action_usergroups_changed_permissions', $arrChanged, $intGroupID, $this->pdh->get('user_groups', 'name', array($intGroupID)));
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('save_suc'), 'color' => 'green');
		$this->edit($message, $intGroupID);
	}


	public function edit($messages=false, $group = false){
		$groupID  = ($group) ? $group : $this->in->get('g', 0);

		$this->user->check_auth('a_usergroups_man');

		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		//Get Group-name
		$group_name = $this->pdh->get('user_groups', 'name', array($groupID));

		//Permissions
		$permission_boxes = $this->acl->get_permission_boxes();
		$this->pm->generate_permission_boxes($permission_boxes);
		$group_permissions = $this->acl->get_group_permissions($this->in->get('g'), true);

		foreach ( $permission_boxes as $group => $checks ){
			$icon = (isset($checks['icon'])) ? $this->core->icon_font($checks['icon']) : '';
			$a_set = $u_set = false;
			foreach ( $checks as $data ){
				if (!is_array($data)) continue;

				//Guests won't get admin-permissions
				if (($groupID == 1 && substr($data['CBNAME'], 0, 2)== "a_")) continue;

				switch (substr($data['CBNAME'], 0, 2)){
					case 'a_': if (!$a_set){
						$this->tpl->assign_block_vars('a_permissions_row', array(
								'GROUP' => $group,
								'ICON'	=> $icon,
						));
						$a_set = true;
					}
					break;

					case 'u_': if (!$u_set){
						$this->tpl->assign_block_vars('u_permissions_row', array(
								'GROUP' => $group,
								'ICON'	=> $icon,
						));
						$u_set = true;
					}
					break;

				}

				$this->tpl->assign_block_vars(substr($data['CBNAME'], 0, 2).'permissions_row.check_group', array(
						'CBNAME'			=> $data['CBNAME'],
						'DISABLED'			=> ($groupID == 2) ? 'disabled' : '',
						'CBCHECKED'			=> (isset($group_permissions[$data['CBNAME']]) && $group_permissions[$data['CBNAME']] == "Y") ? ' checked="checked"' : '',
						'CLASS'				=> (isset($group_permissions[$data['CBNAME']]) && $group_permissions[$data['CBNAME']] == "Y") ? 'positive' : 'negative',
						'TEXT'				=> $data['TEXT'],
				));
			}

		}
		unset($permission_boxes);

		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $caid){
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}

		$this->tpl->assign_block_vars('articelcat_row', array(
				'GROUP' => $this->user->lang('article'),
				'ICON'	=> $this->core->icon_font('fa-file-text'))
				);

		$grps = array('rea', 'cre', 'upd', 'del', 'chs');

		$arrPermissionDropdown = array(
				-1 => $this->user->lang('inherited'),
				1 => $this->user->lang('allowed'),
				0 => $this->user->lang('disallowed')
		);

		foreach($grps as $group_id){
			$this->tpl->assign_block_vars('articelcat_row.headline_row', array(
					'GROUP'	=> $this->user->lang('perm_'.$group_id),
			));
		}

		foreach($arrCategories as $intCategoryID => $strCategoryName){
			$this->tpl->assign_block_vars('articelcat_row.check_group', array(
					'CBNAME'		=> $strCategoryName,
					'S_ADMIN'		=> false
			));

			$arrPermissions = $this->pdh->get('article_categories', 'permissions', array($intCategoryID));

			foreach($grps as $group_id){
				$perm = (isset($arrPermissions[$group_id][$groupID])) ? $arrPermissions[$group_id][$groupID] : -1;

				$this->tpl->assign_block_vars('articelcat_row.check_group.group_row', array(
						'STATUS'	=>  (new hdropdown('perm['.$intCategoryID.']['.$group_id.']', array('options' => $arrPermissionDropdown, 'value' => $perm)))->output(),
				));
			}
		}

		$this->jquery->Tab_header('groups_tabs');
		$this->jquery->Tab_header('permission_tabs');

		$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->id));
		$this->tpl->assign_vars(array(
				'S_IS_IN_GROUP'			=> (isset($memberships[$groupID])) ? true : false,
				'GRP_ID'				=> $groupID,
				'GRP_NAME'				=> sanitize($group_name),
				'GRP_DESC'				=> $this->pdh->get('user_groups', 'desc', array($groupID)),
				'GRP_HIDDEN'			=> (new hradio('hide', array('value' => $this->pdh->get('user_groups', 'hide', array($groupID)))))->output(),
				'GRP_TEAMLIST'			=> (new hradio('team', array('value' => $this->pdh->get('user_groups', 'team', array($groupID)))))->output(),
		));


		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manage_user_group').': '.sanitize($group_name),
				'template_file'		=> 'admin/manage_user_groups_edit.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('manage_user_groups'), 'url'=>$this->root_path.'admin/manage_user_groups.php'.$this->SID],
						['title'=> (!strlen($group_name)) ? $this->user->lang('add_user_group') : sanitize($group_name), 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	// ---------------------------------------------------------
	// Displays a single Group
	// ---------------------------------------------------------
	public function edit_users($messages=false, $group = false){
		$groupID  = ($group) ? $group : $this->in->get('g', 0);

		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('user_groups_users', 'is_grpleader', array($this->user->id, $groupID))){
			$this->user->check_auth('a_usergroups_man');
		}

		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}


		//Only a Super-Admin is allowed to manage the super-admin group
		$memberships = $this->pdh->get('user_groups_users', 'memberships_status', array($this->user->data['user_id']));
		if ($groupID == 2 && !isset($memberships[2])){message_die($this->user->lang('no_auth_superadmins'), '', 'access_denied');}

		$order = $this->in->get('o','0.0');
		$red = 'RED'.str_replace('.', '', $order);

		//Get Users in Group
		$members = $this->pdh->get('user_groups_users', 'user_list', array($groupID));

		//Get Group-name
		$group_name = $this->pdh->get('user_groups', 'name', array($groupID));

		//Get all Userdata
		$sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active FROM __users u
				ORDER BY u.username '.(($order == '0.0') ? 'ASC' : 'DESC');

		$user_query = $this->db->query($sql);
		$userNames = $user_data = array();
		if ($user_query){
			while($row = $user_query->fetchAssoc()){
				$user_data[$row['user_id']] = $row;
				$userNames[$row['user_id']] = $row['username'];
			}
		}

		natcasesort($userNames);

		$not_in = $addGrpLeaders = array();
		$count = array("" => 0, "_grpleader" => 0);
		//Bring all members from Group to template
		foreach($userNames as $key => $name) {
			if (in_array($key, $members)){
			$elem = $user_data[$key];
			$user_online = ( $this->pdh->get('user', 'is_online', array($elem['user_id'])) ) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';
			$user_active = ( $elem['user_active'] == '1' ) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';

			$row = ($this->pdh->get('user_groups_users', 'is_grpleader', array($elem['user_id'], $groupID))) ? '_grpleader' : '';

			$this->tpl->assign_block_vars('user_row'.$row, array(
				'ID'			=> $elem['user_id'],
				'NAME'			=> sanitize($elem['username']),
				'EMAIL'			=> ( !empty($elem['user_email']) ) ? '<a href="javascript:usermailer('.$elem['user_id'].');">'.$this->encrypt->decrypt($elem['user_email']).'</a>' : '',
				'LAST_VISIT'	=> $this->time->user_date($elem['user_lastvisit'], true),
				'ACTIVE'		=> $user_active,
				'ONLINE'		=> $user_online,
				'S_UNDELETABLE'	=> ($groupID == 2 && $elem == $this->user->data['user_id']) ? true : false,
			));

			if($row === "") $addGrpLeaders[$key] = $name;

			$count[$row]++;


			} else {
				$not_in[$key] = $addGrpLeaders[$key] = $name;
			}

		}

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'500', 'withid'=>'userid'));

		$arrMenuItems = array(
			0 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-trash-o',
				'text'	=> $this->user->lang('delete_selected_from_group'),
				'perm'	=> true,
				'name'	=> 'del_group_users',
			),
			1 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-check',
				'text'	=> $this->user->lang('add_grpleader'),
				'perm'	=> $this->user->check_auth('a_usergroups_man', false),
				'name'	=> 'add_grpleader',
			),
			2 => array(
				'type'	=> 'button',
				'icon'	=> 'fa-times',
				'text'	=> $this->user->lang('remove_grpleader'),
				'perm'	=> $this->user->check_auth('a_usergroups_man', false),
				'name'	=> 'remove_grpleader',
			),
		);

		$this->tpl->assign_vars(array(
			'GROUP_NAME'			=> sanitize($group_name),
			$red 					=> '_red',
			'U_MANAGE_USERS'		=> 'manage_user_groups.php'.$this->SID.'&amp;users=true&amp;g='.$groupID,
			'KEY'					=> $key,
			'ADD_USER_DROPDOWN'		=> (new hmultiselect('add_user', array('options' => $not_in, 'value' => '', 'width' => 350, 'filter' => true, 'appendTo' => '#dialog-add-users')))->output(),
			'ADD_GRPLEADER_DROPDOWN'=> (new hmultiselect('group_user', array('options' => $addGrpLeaders, 'value' => '', 'width' => 350, 'filter' => true, 'appendTo' => '#dialog-add-grpleaders')))->output(),
			'GRP_ID'				=> $groupID,
			'BUTTON_MENU'			=> $this->core->build_dropdown_menu($this->user->lang('selected_user').'...', $arrMenuItems, '', 'user_groups_user_menu', array(".usercheckbox"), false),
			'S_USERGROUP_ADMIN' 	=> $this->user->check_auth('a_usergroups_man', false),
			'S_IS_IN_GROUP'			=> (isset($memberships[$groupID])) ? true : false,
			'COUNT_MEMBERS'			=> $count[""],
			'COUNT_GROUPLEADERS'	=> $count["_grpleader"],
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_user_group').': '.sanitize($group_name),
			'template_file'		=> 'admin/manage_user_groups_users.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_user_groups'), 'url'=>$this->root_path.'admin/manage_user_groups.php'.$this->SID],
				['title'=>sanitize($group_name), 'url'=> $this->root_path.'admin/manage_user_groups.php'.$this->SID.'&g='.$groupID.'&edit=true'],
				['title'=>$this->user->lang('group_members'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display_grouppermlist(){
		$this->user->check_auth('a_usergroups_man');
		//Permissions
		$permission_boxes = $this->acl->get_permission_boxes();
		$this->pm->generate_permission_boxes($permission_boxes);

		if($this->in->get('grp', 0)){
			$grps = array($this->in->get('grp', 0) => $this->pdh->get('user_groups', 'name', array($this->in->get('grp', 0))));
		} else {
			$grps = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		}

		foreach ( $permission_boxes as $group => $checks ){
			$icon = (isset($checks['icon'])) ? $this->core->icon_font($checks['icon']) : '';

			$this->tpl->assign_block_vars('permissions_row', array(
				'GROUP' => $group,
				'ICON'	=> (isset($checks['icon'])) ? $this->core->icon_font($checks['icon']) : '',
				)
			);
			foreach($grps as $group_id => $group){
				$this->tpl->assign_block_vars('permissions_row.headline_row', array(
					'GROUP'		=> $group,
				));
			}

			foreach ( $checks as $data ){
				if(!is_array($data)) continue;

				$this->tpl->assign_block_vars('permissions_row.check_group', array(
					'CBNAME'		=> $data['TEXT'],
					'S_ADMIN'		=> (strpos($data['CBNAME'], 'a_') !== false) ? true : false
				));

				foreach($grps as $group_id => $group){
					$group_permissions = $this->acl->get_group_permissions($group_id);
					$this->tpl->assign_block_vars('permissions_row.check_group.group_row', array(
						'STATUS'	=> ( $group_permissions[$data['CBNAME']] == "Y") ? ' <i class="fa fa-check positive"></i>' : '',
					));
				}
			}
		}
		unset($permission_boxes);

		$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
		$arrCategories = array();
		foreach($arrCategoryIDs as $caid){
			$arrCategories[$caid] = $this->pdh->get('article_categories', 'name_prefix', array($caid)).$this->pdh->get('article_categories', 'name', array($caid));
		}

		$this->tpl->assign_block_vars('permissions_row', array(
				'GROUP' => $this->user->lang('article_categories'),
				'ICON'	=> $this->core->icon_font('fa-file-text'))
		);
		foreach($grps as $group_id => $group){
			$this->tpl->assign_block_vars('permissions_row.headline_row', array(
					'GROUP'		=> $group,
			));
		}

		foreach($arrCategories as $intCategoryID => $strCategoryName){
			$this->tpl->assign_block_vars('permissions_row.check_group', array(
					'CBNAME'		=> $strCategoryName,
					'S_ADMIN'		=> false
			));

			$arrPermissions = $this->pdh->get('article_categories', 'permissions', array($intCategoryID));
			$intParent = $this->pdh->get('article_categories', 'parent', array($intCategoryID));

			foreach($grps as $group_id => $group){
				$intCID = $intCategoryID;
				$intGroupID = $group_id;
				$intParentID = $intParent;

				$out = '';
				$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, 'cre', $intGroupID, isset($arrPermissions['cre'][$group_id]) ? $arrPermissions['cre'][$group_id] : -1, $intParentID));
				$out .= ($blnResult) ?  '<i class="fa fa-check positive" title="'.$this->user->lang('perm_cre').'"></i> / ' : ' - / ';
				$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, 'upd', $intGroupID, (isset($arrPermissions['upd'][$group_id]) ? $arrPermissions['upd'][$group_id] : -1), $intParentID));
				$out .= ($blnResult) ?  '<i class="fa fa-check positive" title="'.$this->user->lang('perm_upd').'"></i> / ' : ' - / ';
				$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, 'del', $intGroupID, isset($arrPermissions['del'][$group_id]) ? $arrPermissions['del'][$group_id] : -1, $intParentID));
				$out .= ($blnResult) ?  '<i class="fa fa-check positive" title="'.$this->user->lang('perm_del').'"></i> / ' : ' - / ';
				$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, 'rea', $intGroupID, isset($arrPermissions['rea'][$group_id]) ? $arrPermissions['rea'][$group_id] : -1, $intParentID));
				$out .= ($blnResult) ?  '<i class="fa fa-check positive" title="'.$this->user->lang('perm_rea').'"></i> / ' : ' - / ';
				$blnResult = $this->pdh->get('article_categories', 'calculated_permissions', array($intCID, 'chs', $intGroupID, isset($arrPermissions['chs'][$group_id]) ? $arrPermissions['chs'][$group_id] : -1, $intParentID));
				$out .= ($blnResult) ?  '<i class="fa fa-check positive" title="'.$this->user->lang('perm_chs').'"></i> / ' : ' - / ';
				$out = substr($out, 0, -2);

				$this->tpl->assign_block_vars('permissions_row.check_group.group_row', array(
						'STATUS'	=> $out,
				));
			}
		}


		$this->core->set_vars([
				'page_title'		=> $this->user->lang('user_group_permissions'),
				'template_file'		=> 'admin/manage_user_groups_permlist.html',
				'page_path'			=> [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manage_user_groups'), 'url'=>$this->root_path.'admin/manage_user_groups.php'.$this->SID],
					['title'=>$this->user->lang('user_group_permissions'), 'url'=>' '],
				],
				'display'			=> true
		]);

	}

	// ---------------------------------------------------------
	// Process helper methods
	// ---------------------------------------------------------
	private function update_auth_groups($auth_id,  $auth_setting = 'N', $group_id=0,$check_query_type = true){
		$upd_ins = ( $check_query_type ) ? $this->switch_upd_ins($auth_id, $group_id) : 'upd';

		if ( (empty($auth_id)) || (empty($group_id)) ){
			return false;
		}

		if ( $upd_ins == 'upd' ){
			if ($auth_setting == "N"){
				$sql = "DELETE FROM __auth_groups
						WHERE auth_id='".$auth_id."'
						AND group_id='".$group_id."'";

			} else {
				$sql = "UPDATE __auth_groups
						SET auth_setting='".$auth_setting."'
						WHERE auth_id='".$auth_id."'
						AND group_id='".$group_id."'";
			}

		}else{
			$sql = "INSERT INTO __auth_groups
					(group_id, auth_id, auth_setting)
					VALUES ('".$group_id."','".$auth_id."','".$auth_setting."')";
		}

		if ( !$this->db->query($sql) ){
			return false;
		}
		return true;
	}

	private function switch_upd_ins($auth_id, $group_id){
		$sql = "SELECT o.auth_value
				FROM __auth_options o, __auth_groups u
				WHERE (u.auth_id = o.auth_id)
				AND (u.group_id=?)
				AND u.auth_id=?";
		$objQuery = $this->db->prepare($sql)->execute($group_id, $auth_id);

		if ( $objQuery && $objQuery->numRows > 0 )
		{
			return 'upd';
		}
		return 'ins';
	}

	private function get_post() {
		$grps = array();
		if(is_array($this->in->getArray('user_groups', 'string'))) {
			foreach($this->in->getArray('user_groups', 'string') as $key => $grp) {
				if(isset($grp['id']) AND $grp['id'] AND !empty($grp['name'])) {
					$grps[] = array(
						'id'	=> $this->in->get('user_groups:'.$key.':id',0),
						'name'	=> $this->in->get('user_groups:'.$key.':name',''),
						'desc'	=> $this->in->get('user_groups:'.$key.':desc',''),
						'hide'	=> $this->in->get('user_groups:'.$key.':hide',0),
						'team'	=> $this->in->get('user_groups:'.$key.':team',0),
						'deletable' => $this->in->get('user_groups:'.$key.':deletable',false)
					);
				}
			}

			return $grps;
		}
		return false;
	}

}
registry::register('Manage_User_Groups');
