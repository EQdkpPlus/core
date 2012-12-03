<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_User_Groups extends EQdkp_Admin
{
	function Manage_User_Groups()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_buttons(array(
			//Group-List
			'save' => array(
				'name' => 'save',
				'process' => 'user_group_save',
				'check' => 'a_users_man'),
			'delete' => array(
				'name' => 'del',
				'process' => 'user_group_del',
				'check' => 'a_users_man'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'user_group_del',
				'check' => 'a_users_man'),
			
			//Manage single User-Group
			'del_group_users' => array(
				'name' => 'del_group_users',
				'process' => 'user_group_users_del',
				'check' => 'a_users_man'),
			'add_group_users' => array(
				'name' => 'add_group_users',
				'process' => 'user_group_users_save',
				'check' => 'a_users_man'),
			'save_group_perms' => array(
				'name' => 'save_group_perms',
				'process' => 'save_group_permissions',
				'check' => 'a_users_man'),
			
			//Group-Permissions-List
			'group_permlist' => array(
				'name' => 'user_group_perms',
				'process' => 'display_grouppermlist',
				'check' => 'a_users_man'),
			
			//Display Usergroup-List
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_users_man')
			)
		);
		
		$this->assoc_params(array(
            'group' => array(
               'name'    => 'g',
               'process' => 'display_group',
               'check'   => 'a_users_man'),
						'delete' => array(
							'name'    => 'delete',
              'process' => 'user_group_del',
              'check'   => 'a_users_man'),						
						'group_permlist' => array(
							'name'    => 'grp_perms',
							'process' => 'display_grouppermlist',
							'check'   => 'a_users_man')				
        ));
	}
	
	//Delete User of a Group
	function user_group_users_del(){
		global $user, $pdh, $in;

		$members = $in->getArray('group_user', 'int');

		if (count($members) > 0){
			$pdh->put('user_groups_users', 'delete_users_from_group', array($members, $in->get('g')));
		}
		$message = array('title' => $user->lang['del_suc'], 'text' => $user->lang['del_user_from_group_success'], 'color' => 'green');
		$this->display_group($message);
		
	}
	
	//Add User to a Group
	function user_group_users_save(){
		global $user, $pdh, $in;

		$members = $in->getArray('add_user', 'int');

		if ($members[0] == 0){unset($members[0]);};
		if ($in->get('g') == 2){unset($members[$user->data['user_id']]);}
		
		if (count($members) > 0){
			$pdh->put('user_groups_users', 'add_users_to_group', array($members, $in->get('g')));

		}
		$message = array('title' => $user->lang['save_suc'], 'text' => $user->lang['add_user_to_group_success'], 'color' => 'green');
		$this->display_group($message);
		
	}
	
	//Save the user-Groups
	function user_group_save()
	{
		global $user, $pdh, $in;
		$retu = array();
		$group_post = $this->get_post();
			
		if($group_post)
		{
			$id_list = $pdh->get('user_groups', 'id_list');
			foreach($group_post as $group)
			{
				$standard = ($in->get('user_groups_standard') == $group['id']) ? 1 : 0;
				$func = (in_array($group['id'], $id_list)) ? 'update_grp' : 'add_grp';
				$retu[] = $pdh->put('user_groups', $func, array($group['id'], $group['name'], $group['desc'], $standard, $group['hide']));
				$names[] = $group['name'];
				$add_name = (in_array($group['id'], $id_list)) ? '' : $group['name'];
			}

			if(in_array(false, $retu))
			{
				$message = array('title' => $user->lang['save_no_suc'], 'text' => implode(', ', $names), 'color' => 'red');
			}
			elseif(in_array(true, $retu))
			{
				if ($add_name != ""){
					$message = array('title' => $user->lang['save_suc'], 'text' => sprintf($user->lang['add_usergroup_success'], $add_name), 'color' => 'green');
				} else {
					$message = array('title' => $user->lang['save_suc'], 'text' => $user->lang['save_usergroup_success'], 'color' => 'green');
				}
			}
			
		}
		else
		{
			$message = array('title' => '', 'text' => $user->lang['no_ranks_selected'], 'color' => 'grey');
		}

		$this->display_form($message);
	}
	
	//Delete user-groups
	function user_group_del()
	{
		global $user, $pdh, $in;
		
		//Input
		if($in->exists('user_groups', 'string')){
			$in_groups = $this->get_selected();
		} elseif ($in->get('delete') != ""){
			$in_groups[] = $pdh->get('user_groups', 'data', array($in->get('delete')));
		}
		
		
		//Confirm message
		if (!$in->exists('confirm')){
			if (count($in_groups) > 0){
				foreach ($in_groups as $groups){
					$ids[] = $groups['id'];
					$names[] = $groups['name'];
				}
				confirm_delete($user->lang['confirm_delete_groups'].'<br />'.implode(',<br>', $names), 'ids', base64_encode(serialize($ids)));

			} else {
				$message = array('title' => '', 'text' => $user->lang['no_groups_selected'], 'color' => 'grey');			
			}
		//Delete
		} else {
			if ($in->exists('ids')){
				$grpids = unserialize(base64_decode($in->get('ids')));
				foreach($grpids as $id)
				{
					$names[] = $pdh->get('user_groups', 'name', ($id));
					$retu[] = $pdh->put('user_groups', 'delete_grp', array($id));
				}
				
				if(in_array(false, $retu))
				{
					$message = array('title' => $user->lang['del_nosuc'], 'text' => $user->lang['delete_default_group_error'], 'color' => 'red');
				}
				else
				{
					$message = array('title' => $user->lang['del_suc'], 'text' => implode(', ', $names), 'color' => 'green');
				}
				
			}
		}
		
		$this->display_form($message);
	}
	
	//Display the Usergroup-list
	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $in;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}

		$new_id = 0;
		$order = $in->get('order','0.1');
		$red = 'RED'.str_replace('.', '', $order);

		$grps = $pdh->aget('user_groups', 'name', 0, array($pdh->get('user_groups', 'id_list')));

		if($order == '0.0')
		{
			arsort($grps, SORT_STRING);
		}
		else
		{
			asort($grps, SORT_STRING);
		}
		$key = 0;
		$new_id = 1;
		
		//ksort($grps); //otherwise our new_id is wrong!
		foreach($grps as $id => $name)
		{
			$tpl->assign_block_vars('user_groups', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'DESC'	=> $pdh->get('user_groups', 'desc', array($id)),
				'USER_COUNT'	=> $pdh->get('user_groups_users', 'groupcount', array($id)),
				'S_DELETABLE' => ($pdh->get('user_groups', 'deletable', array($id))) ? true : false,
				'S_NO_STANDARD' => ($id == 2 || $id == 3) ? true : false,
				'STANDARD'	=> ($pdh->get('user_groups', 'standard', array($id))) ? 'checked="checked"' : '',
				'HIDE'	=> ($pdh->get('user_groups', 'hide', array($id))) ? 'checked="checked"' : '',
				'ROW_CLASS' => $core->switch_row_class())
			);
			$key++;
			$new_id = ($id >= $new_id) ? $id+1 : $new_id;
		}

		$tpl->assign_vars(array(
			'ACTION' 	=> 'manage_user_groups.php'.$SID,
			$red 		=> '_red',
			'SID'		=> $SID,
			'ID'		=> $new_id,
			'KEY'		=> $key,
			'ROW_CLASS' => $core->switch_row_class(),
			//Language
			
			'L_USER_GROUPS'	=> $user->lang['manage_user_groups'],
			'L_NAME'	=> $user->lang['name'],
			'L_DESC'	=> $user->lang['description'],
			'L_MEMBERS'	=> $user->lang['members'],
			'L_STANDARD'	=> $user->lang['default_group'],
			'L_MANAGE'		=> $user->lang['manage'],
			'L_MANAGE_GROUP'	=> $user->lang['manage_user_group'],
			'L_SAVE'	=> $user->lang['save'],
			'L_DEL_SELECTED'		=> $user->lang['delete_selected_group'],
			'L_ADD_GROUP' => $user->lang['add_user_group'],
			'L_USER_GROUP_PERMS'	=> $user->lang['user_group_permissions'],
			'L_HIDE'	=> $user->lang['hide'],
			'L_ACTION'		=> $user->lang['action'],
			'L_DELETE'		=> $user->lang['delete'],
			'L_EDIT'		=> $user->lang['manage_user_group'],
			)
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manage_user_groups'],
            'template_file' => 'admin/manage_user_groups.html',
            'display'       => true)
        );
	}
	

	
	//Process: Save permissions of a group
	function save_group_permissions(){
		global $db, $in, $acl,$user;
		
		if ($in->get('g') != 2){
			$auth_defaults = $acl->get_auth_defaults(false);
			$group_permissions = $acl->get_group_permissions($in->get('g', 0), true);
			$superadm_only = $acl->get_superadmin_only_permissions();
			$memberships = $acl->get_user_group_memberships($user->data['user_id']);
			
			//If not Superadmin, unset the superadmin-permissions
			if (!isset($memberships[2])){
				foreach ($superadm_only as $superperm){
					unset($auth_defaults[$superperm]);
				}
			}
			
					foreach ( $auth_defaults as $auth_value => $auth_setting )
					{
							$r_auth_id    = $acl->get_auth_id($auth_value);
							$r_auth_value = $auth_value;
	
							$chk_auth_value = ( $group_permissions[$auth_value] == "Y") ? 'Y' : 'N';
							$db_auth_value  = ( $in->get($r_auth_value) == "Y" )                      ? 'Y' : 'N';
	
							if ( $chk_auth_value != $db_auth_value )
							{
								 $this->update_auth_groups($r_auth_id, $db_auth_value, $in->get('g', 0));
							}
					}
					$db->free_result($result);
		}
				$message = array('title' => $user->lang['save_suc'], 'text' => $user->lang['admin_set_perms_success'], 'color' => 'green');
				$this->display_group($message);
				
	}
	
	

	
	// ---------------------------------------------------------
  // Displays a single Group
  // ---------------------------------------------------------
	function display_group($messages=false){
		global $core, $user, $tpl, $pdh, $SID, $in, $db, $jquery, $acl, $pm, $acl;
		
		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}
		
		//Only a Super-Admin is allowed to manage the super-admin group
		$memberships = $pdh->get('user_groups_users', 'memberships_status', array($user->data['user_id']));
		if ($in->get('g', 0) == 2 && !isset($memberships[2])){message_die($user->lang['no_auth_superadmins']);}
		
		$order = $in->get('o','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		
		//Get Users in Group
		$members = $pdh->get('user_groups_users', 'user_list', array($in->get('g')));
		
		//Order
		if($order == '0.0')
		{
			arsort($members, SORT_STRING);
		}
		else
		{
			asort($members, SORT_STRING);
		}
		//Get Group-name
		$group_name = $pdh->get('user_groups', 'name', array($in->get('g')));
		
		//Get all Userdata
		$sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lastvisit, u.user_active, s.session_id
                FROM (__users u
                LEFT JOIN __sessions s
                ON u.user_id = s.session_user_id)
                GROUP BY u.username';
		
		$user_query = $db->query($sql);
		while($row = $db->fetch_record()){
			$user_data[$row['user_id']] = $row;
		}
		$member_in = array();
		
		//Bring all members from Group to template
		foreach($members as $key=>$elem)
		{	
			$user_online = ( !empty($user_data[$elem]['session_id']) ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";
            $user_active = ( $user_data[$elem]['user_active'] == '1' ) ? "<img src='../images/glyphs/status_green.gif'>" : "<img src='../images/glyphs/status_red.gif'>";
			
			$tpl->assign_block_vars('user_row', array(
				'ID'	=> $elem,
				'NAME'	=> sanitize($user_data[$elem]['username']),
				'ROW_CLASS' => $core->switch_row_class(),
				'EMAIL'   => ( !empty($user_data[$elem]['user_email']) ) ? '<a href="mailto:'.$user_data[$elem]['user_email'].'">'.$user_data[$elem]['user_email'].'</a>' : '',
                'LAST_VISIT'    => date($user->style['date_time'], $user_data[$elem]['user_lastvisit']),
                'ACTIVE'        => $user_active,
                'ONLINE'        => $user_online,
								'S_UNDELETABLE'	=> ($in->get('g') == 2 && $elem == $user->data['user_id']) ? true : false,
				
				));
			$member_in[$elem] = $elem; 

		}
		$not_in = array();
		
		foreach ($user_data as $key=>$elem){
			if (!$member_in[$key]){
				$not_in[$key] = $elem['username'];
			}
		}

		//Permissions
		$permission_boxes = $acl->get_permission_boxes();
		$pm->generate_permission_boxes($permission_boxes);
		$group_permissions = $acl->get_group_permissions($in->get('g'), true);
		$superadm_only_perms = $acl->get_superadmin_only_permissions();
		
		foreach ( $permission_boxes as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );
			
            foreach ( $checks as $data )
            {
							if (!($in->get('g') == 1 && substr($data['CBNAME'], 0, 2)== "a_")){
								$tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['CBNAME'],
										'DISABLED'	=> ($in->get('g') == 2) ? 'disabled' : '',
										'S_SUPERADMIN_PERM'	=> (isset($superadm_only_perms[$data['CBNAME']]) && !isset($memberships[2])) ? true : false,
                    'CBCHECKED' => ( $group_permissions[$data['CBNAME']] == "Y") ? ' checked="checked"' : '',
										'CLASS'			=> ( $group_permissions[$data['CBNAME']] == "Y") ? 'positive' : 'negative',
                    'TEXT'      => $data['TEXT'])
                );
							}
            }
        }
        unset($permission_boxes);
		
		
		$tpl->assign_vars(array(
			'GROUP_NAME' 	=> sanitize($group_name),	
			$red 		=> '_red',
			'U_MANAGE_USERS' 	=> 'manage_user_groups.php'.$SID.'&g='.$in->get('g'),
			'SID'		=> $SID,
			'KEY'		=> $key,
			'ROW_CLASS' => $core->switch_row_class(),
			'ADD_USER_DROPDOWN'	=> $jquery->MultiSelect('add_user', $not_in, '', 200, 350),
			'JS_TABS'		=> $jquery->Tab_header('groups_tabs'),
			//Language
			'L_USER_GROUPS'	=> $user->lang['manage_user_groups'],
			'L_MEMBERS'	=> $user->lang['group_members'],
			'L_PERMISSIONS'	=> $user->lang['group_permissions'],
			
			'L_ADD_USER'	=> $user->lang['add_user_to_group'],
			'L_DEL_SELECTED'	=> $user->lang['delete_selected_from_group'],
			'L_ADD_SELECTED'	=> $user->lang['add_selected_to_group'],
			
			'L_NAME'	=> $user->lang['name'],
			'L_HIDE'	=> $user->lang['hide'],
			'L_PREFIX'	=> $user->lang['list_prefix'],
			'L_SUFFIX'	=> $user->lang['list_suffix'],
			'L_SEL_RAN' => $user->lang['selected_ranks'],
			'L_SAVE'	=> $user->lang['save'],
			'L_DEL'		=> $user->lang['delete'],
			'L_USERNAME'         => $user->lang['username'],
      'L_EMAIL'            => $user->lang['email_address'],
      'L_LAST_VISIT'       => $user->lang['last_visit'],
      'L_ACTIVE'           => $user->lang['active'],
      'L_ONLINE'           => $user->lang['online'],
      'L_ACCOUNT_ENABLED'  => $user->lang['account_enabled'],
			 // Sorting
      'O_USERNAME'   => $current_order['uri'][0],
      'O_EMAIL'      => $current_order['uri'][1],
      'O_LAST_VISIT' => $current_order['uri'][2],
      'O_ACTIVE'     => $current_order['uri'][3],
      'O_ONLINE'     => $current_order['uri'][4],
			)
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manage_user_group'].': '.sanitize($group_name),
            'template_file' => 'admin/manage_user_groups_users.html',
            'display'       => true)
        );
	}
	
	function display_grouppermlist(){
		global $core, $user, $tpl, $pdh, $SID, $in, $db, $jquery, $acl, $pm, $acl;
		
		//Permissions
		$permission_boxes = $acl->get_permission_boxes();
		$pm->generate_permission_boxes($permission_boxes);
		$grps = $pdh->aget('user_groups', 'name', 0, array($pdh->get('user_groups', 'id_list')));
		
		foreach ( $permission_boxes as $group => $checks )
        {
            $tpl->assign_block_vars('permissions_row', array(
                'GROUP' => $group)
            );
						foreach($grps as $group_id => $group){
									$tpl->assign_block_vars('permissions_row.headline_row', array(
                    'GROUP'    => $group,
                	));
								}

            foreach ( $checks as $data )
            {
                $tpl->assign_block_vars('permissions_row.check_group', array(
                    'CBNAME'    => $data['TEXT'],
										'CLASS'			=> $core->switch_row_class(),
                ));
								
								foreach($grps as $group_id => $group){
									$group_permissions = $acl->get_group_permissions($group_id);

									$tpl->assign_block_vars('permissions_row.check_group.group_row', array(
                    'STATUS'    => ( $group_permissions[$data['CBNAME']] == "Y") ? ' <img src="../images/ok.png" height="14">' : '',
                	));
								}
            }
        }
        unset($permission_boxes);
		$tpl->assign_vars(array(
			'L_USER_GROUP_PERMS'	=> $user->lang['user_group_permissions'],
			'S_GROUP_PERM_LIST'		=> true,
			'ACTION'							=> 'manage_user_groups.php'.$SID,
			'L_MANAGE_USER_GROUPS'=> $user->lang['manage_user_groups'],
		));
		
		$core->set_vars(array(
				'page_title'    => $user->lang['user_group_permissions'],
				'template_file' => 'admin/manage_user_groups.html',
				'display'       => true)
		);
	
	}
	
	// ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------
    function update_auth_groups($auth_id,  $auth_setting = 'N', $group_id=0,$check_query_type = true)
    {
        global $db;

        $upd_ins = ( $check_query_type ) ? $this->switch_upd_ins($auth_id, $group_id) : 'upd';

        if ( (empty($auth_id)) || (empty($group_id)) )
        {
            return false;
        }

        if ( $upd_ins == 'upd' )
        {
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
			
        }
        else
        {
            $sql = "INSERT INTO __auth_groups
                    (group_id, auth_id, auth_setting)
                    VALUES ('".$group_id."','".$auth_id."','".$auth_setting."')";
        }

        if ( !($result = $db->query($sql)) )
        {
            return false;
        }
        return true;
    }

    function switch_upd_ins($auth_id, $group_id)
    {
        global $db;

        $sql = "SELECT o.auth_value
                FROM __auth_options o, __auth_groups u
                WHERE (u.auth_id = o.auth_id)
                AND (u.group_id='".$group_id."')
                AND u.auth_id='".$auth_id."'";
        if ( $db->num_rows($db->query($sql)) > 0 )
        {
            return 'upd';
        }
        return 'ins';
    }
    

	
	function get_post()
	{
		global $in;
		$grps = array();
		if($in->exists('user_groups', 'string'))
		{			
			foreach($in->getArray('user_groups', 'string') as $key => $grp)
			{
				if(isset($grp['id']) AND $grp['id'] AND !empty($grp['name']))				
				{
					$grps[] = array(
						'id'	=> $in->get('user_groups:'.$key.':id',0),
						'name'	=> $in->get('user_groups:'.$key.':name',''),
						'desc'	=> $in->get('user_groups:'.$key.':desc',''),
						'hide'	=> $in->get('user_groups:'.$key.':hide',0),
						'deletable' => $in->get('user_groups:'.$key.':deletable',false)
					);
				}
			}

			return $grps;
		}
		return false;
	}
	
	function get_selected()
	{
		global $in;
		$grps = array();
		if($in->exists('user_groups', 'string'))
		{			
			foreach($in->getArray('user_groups', 'string') as $key => $grp)
			{
				if(isset($grp['id']) AND $grp['id'] AND $grp['selected'])				
				{
					$grps[] = array(
						'id'	=> $in->get('user_groups:'.$key.':id',0),
						'name'	=> $in->get('user_groups:'.$key.':name',''),
						'desc'	=> $in->get('user_groups:'.$key.':desc',''),
						'hide'	=> $in->get('user_groups:'.$key.':hide',0),
						'deletable' => $in->get('user_groups:'.$key.':deletable',false)
					);
				}
			}
			return $grps;
		}
		return false;
	}
}
$user_groups = new Manage_User_Groups;
$user_groups->process();
?>