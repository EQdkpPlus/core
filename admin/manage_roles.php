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
include_once($eqdkp_root_path . 'common.php');

class Manage_Roles extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_roles_man');
		$handler = array(
			'editid'		=> array('process' => 'display_edit'),
			'adddialog'		=> array('process' => 'display_edit'),
			'defaultroles'	=> array('process' => 'save_defaultrole'),
			'reset'			=> array('process' => 'process_reset','csrf'=>true)
		);
		parent::__construct(false, $handler, array('roles', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function delete(){
		$this->pdh->put('roles', 'delete_roles', array($this->in->getArray('selected_ids', 'int')));
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function process_reset(){
		$this->game->load_default_roles();
		$this->display();
	}

	public function save_defaultrole(){
		$roles = $this->in->getArray('defclassroles', 'int');
		$this->config->set('roles_defaultclasses', json_encode($roles));
		$this->display();
	}

	public function add(){
		$maxxID			= $this->pdh->get('roles', 'maxid');
		$maxxID++;
		$arole_name		= stripslashes($this->in->get('role_name'));
		$arole_classes	= implode("|",$this->in->getArray('role_classes', 'int'));
		$arole_icon		= $this->in->get('icon');
		if($arole_name == "" || $arole_classes == ""){
			$this->display_edit($arole_name);
		} else {
			$this->pdh->put('roles', 'insert_role', array($maxxID, $arole_name, $arole_classes, $arole_icon));
			$this->pdh->process_hook_queue();
			$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
		}
	}

	public function update(){
		// Select Max ID
		$maxxID = $this->pdh->get('roles', 'maxid');

		// Init Vars
		$newID  = $maxxID+1;

		// the variables
		$arole_id		= ($this->in->get('editid' ,0) > 0) ? $this->in->get('editid', 0) : $newID;
		$arole_name		= stripslashes($this->in->get('role_name'));
		$arole_classes	= implode("|",$this->in->getArray('role_classes', 'int'));
		$arole_icon		= $this->in->get('icon');

		if($arole_name == "" || $arole_classes == ""){
			$this->display_edit();
		} else {
			// Perform the action
			$this->pdh->put('roles', 'update_role', array($arole_id, $arole_name, $arole_classes, $arole_icon));
			$this->pdh->process_hook_queue();
			$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
		}
	}

	public function display_edit(){
	    $arrImages = array('png', 'jpg', 'jpeg', 'gif');
		$icons			= array();
		$row			= ($this->in->get('editid', 0) > 0) ? $this->pdh->get('roles', 'roles', array($this->in->get('editid', 0))) : array();

		// first, get the cutom uploaded role icons
		$roles_folder	= $this->pfh->FolderPath('role_icons', 'files');
		$files			= sdir($roles_folder);
		foreach($files as $file) {
			$strExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if(!in_array($strExtension, $arrImages)) continue;
			$icons[] = $roles_folder.'/'.$file;
		}

		// now, get the game file dependant icons
		$roles_folder	= $this->root_path.'games/'.$this->config->get('default_game').'/icons/roles';
		if (is_dir($roles_folder)){
			$files = sdir($roles_folder);
			foreach($files as $file) {
				$strExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				if(!in_array($strExtension, $arrImages)) continue;
				$icons[] = $roles_folder.'/'.$file;
			}
		}
		$num		= count($icons);
		$fields		= (ceil($num/6))*6;
		$i			= 0;

		while($i<$fields){
			$this->tpl->assign_block_vars('files_row', array());
			$this->tpl->assign_var('ICONS', true);
			$b = $i+6;
			for($i; $i<$b; $i++){
			$icon			= (isset($icons[$i])) ? $icons[$i] : '';
			$selected_icon	= isset($row['icon']) ? $row['icon'] : $row['id'].'.png';
			$this->tpl->assign_block_vars('files_row.fields', array(
					'NAME'		=> pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION),
					'CHECKED'	=> ($selected_icon && pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION) == $selected_icon) ? ' checked="checked"' : '',
					'IMAGE'		=> "<img src='".$icon."' alt='".$icon."' width='30px' style='eventicon' title='".pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION)."' />",
					'CHECKBOX'	=> ($i < $num) ? true : false)
				);
			}
		}

		// Load the roles
		$this->tpl->assign_vars(array(
			'S_ADD'			=> true,
			'EDITID'		=> $this->in->get('editid'),
			'MULTISELECT'	=> (new hmultiselect('role_classes', array('options' => $this->game->get_primary_classes(), 'value' => ((isset($row['classes'])) ? $row['classes'] : ''), 'width' => 350, 'height' => 70)))->output(),
			'REALNAME'		=> (isset($row['name'])) ? $row['name'] : '',
			'BUTTON_NAME'	=> ($this->in->get('editid')) ? 'upd': 'add',
		));

		$this->jquery->fileBrowser('all', 'image', $this->pfh->FolderPath('role_icons','files', 'absolute'), array('title' => $this->user->lang('upload_roleicon'), 'onclosejs' => '$(\'#roleSubmBtn\').click();'));
		$this->core->set_vars([
			'page_title'		=> $this->user->lang('rolemanager'),
			'template_file'		=> 'admin/manage_roles.html',
			'header_format'		=> 'simple',
			'display'			=> true
		]);
	}

	public function display(){
		// The jQuery stuff
		$this->confirm_delete($this->user->lang('delete_rolestext'));
		$this->jquery->Dialog('newRole', $this->user->lang('role_new'), array('url'=>"manage_roles.php".$this->SID."&adddialog=true", 'width'=>'800', 'height'=>'500', 'onclose'=> $this->env->link."admin/manage_roles.php".$this->SID));
		$this->jquery->Dialog('editRole', $this->user->lang('edit_role2'), array('url'=>"manage_roles.php".$this->SID."&editid='+editid+'", 'width'=>'800', 'height'=>'500', 'withid' => 'editid', 'onclose'=> $this->env->link."admin/manage_roles.php".$this->SID));
		$this->jquery->Dialog('ResetRoles', '', array('custom_js'=> "window.location = 'manage_roles.php".$this->SID."&reset=true&link_hash=".$this->CSRFGetToken('reset')."';", 'message'=> $this->user->lang('reset_rolestext')), 'confirm');

		// Build the HPTT Table
		$view_list			= $this->pdh->get('roles', 'id_list');
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_roles', 'hptt_manageroles_actions');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_roles.php'));
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= '?sort='.$this->in->get('sort');

		// build the class list
		$classes			= $this->game->get_primary_classes(array('id_0'));
		$roles				= $this->pdh->aget('roles', 'name', 0, array($this->pdh->get('roles', 'id_list')));
		$defautrole_config	= json_decode($this->config->get('roles_defaultclasses'), true);

		foreach($classes as $classid=>$classname){
			$this->tpl->assign_block_vars('defaultclasses', array(
				'NAME'		=> $this->game->decorate('primary', $classid).' '.$this->game->get_name('primary', $classid),
				'ID'		=> $classid,
				'ROLES'		=> (new hdropdown('defclassroles['.$classid.']', array('options' => $roles, 'value' => ((isset($defautrole_config[$classid])) ? $defautrole_config[$classid] : 1))))->output()
			));
		}

		$this->jquery->tab_header('roles_tabs');
		$this->tpl->assign_vars(array(
			'ROLES'				=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), 40, false),
			'ROLES_COUNT'		=> count($view_list),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_roles').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('rolemanager'),
			'template_file'		=> 'admin/manage_roles.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('rolemanager'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('Manage_Roles');
