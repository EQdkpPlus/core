<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2013
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
include_once($eqdkp_root_path.'common.php');

class Manage_Raid_Groups extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'db', 'pm', 'time', 'acl'=> 'acl', 'crypt' => 'encrypt','logs');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auths(array('a_usergroups_man', 'a_usergroups_grpleader'), 'or');
		
		$handler = array(
			'save'				=> array('process' => 'user_group_save',			'csrf'=>true),
			'del_group_users'	=> array('process' => 'user_group_users_del',		'csrf'=>true),
			'add_group_users'	=> array('process' => 'user_group_users_save',		'csrf'=>true),
			'add_grpleader'		=> array('process' => 'process_add_grpleader',		'csrf'=>true),
			'remove_grpleader'	=> array('process' => 'process_remove_grpleader',	'csrf'=>true),
			'g'					=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('raid_groups', 'name'), null, 'raid_group_ids[]');
		$this->process();
	}
	
	//Delete User of a Group
	public function user_group_users_del(){
		$intGroupID = $this->in->get('g', 0);
		
		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('raid_groups_users', 'is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_usergroups_man');
		}
	
		$members = $this->in->getArray('group_raid', 'int');
		if ($intGroupID == 2){unset($members[$this->user->id]);}
		
		if (count($members) > 0){
			$this->pdh->put('raid_groups_users', 'delete_users_from_group', array($members, $intGroupID));
			$arrMemberNames = $this->pdh->aget('user', 'name', 0, array($members));
			$arrChanged["{L_USER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_raidgroups_removed_user', $arrChanged, $intGroupID, $this->pdh->get('raid_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('del_user_from_group_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	//Add User to a Group
	public function user_group_users_save(){
		$intGroupID = $this->in->get('g', 0);
		
		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('raid_groups_users', 'is_grpleader', array($this->user->id, $intGroupID))){
			$this->user->check_auth('a_usergroups_man');
		}
		
		$members = $this->in->getArray('add_user', 'int');
		if ($members[0] == 0){unset($members[0]);};
		
		if (count($members) > 0){
			$this->pdh->put('raid_groups_users', 'add_users_to_group', array($members, $intGroupID));
			
			$arrMemberNames = $this->pdh->aget('user', 'name', 0, array($members));
			$arrChanged["{L_USER}"] = implode(', ', $arrMemberNames);
			$this->logs->add('action_raidgroups_added_user', $arrChanged, $intGroupID, $this->pdh->get('raid_groups', 'name', array($intGroupID)));
		}
		$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('add_user_to_group_success'), 'color' => 'green');
		$this->edit($message);
		
	}
	
	public function process_add_grpleader (){
		$this->user->check_auth('a_usergroups_man');
		
		$members = $this->in->getArray('group_raid', 'int');
		if (count($members) > 0){
			$this->pdh->put('raid_groups_users', 'add_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	public function process_remove_grpleader (){
		$this->user->check_auth('a_usergroups_man');
		
		$members = $this->in->getArray('group_raid', 'int');
		if (count($members) > 0){
			$this->pdh->put('raid_groups_users', 'remove_grpleader', array($members, $this->in->get('g')));
		}
		$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('add_grpleader_success'), 'color' => 'green');
		$this->edit($message);
	}
	
	//Save the user-Groups
	public function user_group_save() {
		$this->user->check_auth('a_usergroups_man');
		$retu = array();
		$group_post = $this->get_post();

		if($group_post) {
			$id_list = $this->pdh->get('raid_groups', 'id_list');
			foreach($group_post as $key=>$group) {
				$standard = ($this->in->get('raid_groups_standard') == $group['id']) ? 1 : 0;
				$func = (in_array($group['id'], $id_list)) ? 'update_grp' : 'add_grp';
				$retu[] = $this->pdh->put('raid_groups', $func, array($group['id'], $group['name'], $group['desc'], $standard, $group['hide'], $key));
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
		$this->user->check_auth('a_usergroups_man');
		
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
				cancel: '.not-sortable, input',
				cursor: 'pointer',
			});
		", "docready");
		
		foreach($grps as $id => $name){
			$this->tpl->assign_block_vars('raid_groups', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'DESC'	=> $this->pdh->get('raid_groups', 'desc', array($id)),
				'USER_COUNT'	=> $this->pdh->get('raid_groups_users', 'groupcount', array($id)),
				'S_DELETABLE' => ($this->pdh->get('raid_groups', 'deletable', array($id))) ? true : false,
				'S_NO_STANDARD' => ($id == 2 || $id == 3) ? true : false,
				'STANDARD'	=> ($this->pdh->get('raid_groups', 'standard', array($id))) ? 'checked="checked"' : '',
				'HIDE'	=> ($this->pdh->get('raid_groups', 'hide', array($id))) ? 'checked="checked"' : '',
				'S_IS_GRPLEADER' => $this->pdh->get('raid_groups_users', 'is_grpleader', array($this->user->id, $id)),
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
			'S_USERGROUP_ADMIN' => $this->user->check_auth('a_usergroups_man', false),
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
		
		if (!$this->user->check_auth('a_usergroups_man', false) && !$this->pdh->get('raid_groups_users', 'is_grpleader', array($this->user->id, $groupID))){
			$this->user->check_auth('a_usergroups_man');
		}
	
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$order = $this->in->get('o','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		
		//Get Users in Group
		$members = $this->pdh->get('raid_groups_users', 'user_list', array($groupID));
		
		//Get Group-name
		$group_name = $this->pdh->get('raid_groups', 'name', array($groupID));	
		
		//Get all Userdata
		$sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active, s.session_id
				FROM (__users u
				LEFT JOIN __sessions s
				ON u.user_id = s.session_user_id)
				GROUP BY u.username 
				ORDER BY u.username '.(($order == '0.0') ? 'ASC' : 'DESC');
		
		$user_query = $this->db->query($sql);
		if ($user_query){
			while($row = $user_query->fetchAssoc()){
				$user_data[$row['user_id']] = $row;
			}
		}
		
		$not_in = array();
		
		//Bring all members from Group to template
		foreach($user_data as $key => $elem) {
			if (in_array($key, $members)){
				$user_online = ( !empty($elem['session_id']) ) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';
				$user_active = ( $elem['user_active'] == '1' ) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';

				$row = ($this->pdh->get('raid_groups_users', 'is_grpleader', array($elem['user_id'], $groupID))) ? '_grpleader' : '';

				$this->tpl->assign_block_vars('user_row'.$row, array(
					'ID'			=> $elem['user_id'],
					'NAME'			=> sanitize($elem['username']),
					'EMAIL'			=> ( !empty($elem['user_email']) ) ? '<a href="javascript:usermailer('.$elem['user_id'].');">'.$this->crypt->decrypt($elem['user_email']).'</a>' : '',
					'LAST_VISIT'	=> $this->time->user_date($elem['user_lastvisit'], true),
					'ACTIVE'		=> $user_active,
					'ONLINE'		=> $user_online,
					'S_UNDELETABLE'	=> ($groupID == 2 && $elem == $this->user->data['user_id']) ? true : false,
				));
			} else {
				$not_in[$key] = $elem['username'];
			}
		}
		
		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));
		
		$arrMenuItems = array(
			0 => array(
				'name'	=> $this->user->lang('delete_selected_from_group'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-trash-o',
				'perm'	=> true,
				'link'	=> '#del_group_users',
			),
			1 => array(
				'name'	=> $this->user->lang('add_grpleader'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-check',
				'perm'	=> $this->user->check_auth('a_usergroups_man', false),
				'link'	=> '#add_grpleader',
			),
			2 => array(
				'name'	=> $this->user->lang('remove_grpleader'),
				'type'	=> 'button', //link, button, javascript
				'icon'	=> 'fa-times',
				'perm'	=> $this->user->check_auth('a_usergroups_man', false),
				'link'	=> '#remove_grpleader',
			),
		
		);
		
		$this->tpl->assign_vars(array(
			'GROUP_NAME'			=> sanitize($group_name),
			$red 					=> '_red',
			'U_MANAGE_USERS'		=> 'manage_raid_groups.php'.$this->SID.'&amp;g='.$groupID,
			'KEY'					=> $key,
			'ADD_USER_DROPDOWN'		=> $this->jquery->MultiSelect('add_user', $not_in, '', array('width' => 350, 'filter' => true)),
			'GRP_ID'				=> $groupID,
			'BUTTON_MENU'			=> $this->jquery->ButtonDropDownMenu('raid_groups_user_menu', $arrMenuItems, array(".usercheckbox"), '', $this->user->lang('selected_user').'...', ''),
			'S_USERGROUP_ADMIN' 	=> $this->user->check_auth('a_usergroups_man', false),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_user_group').': '.sanitize($group_name),
			'template_file'		=> 'admin/manage_raid_groups_users.html',
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
						'hide'		=> $this->in->get('raid_groups:'.$key.':hide',0),
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
						'hide'		=> $this->in->get('raid_groups:'.$key.':hide',0),
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