<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Roles extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_members_man');
		$handler = array(
			'editid'	=> array('process' => 'display_edit'),
			'adddialog'	=> array('process' => 'display_edit'),
			'reset'		=> array('process' => 'process_reset','csrf'=>true)
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
	
	public function add(){
		$maxxID			= $this->pdh->get('roles', 'maxid');
		$maxxID++;
		$arole_name		= stripslashes($this->in->get('role_name'));
		$arole_classes	= implode("|",$this->in->getArray('role_classes', 'int'));
		$this->pdh->put('roles', 'insert_role', array($maxxID, $arole_name, $arole_classes));
		$this->pdh->process_hook_queue();
		echo "<script>parent.window.location.href = 'manage_roles.php';</script>";
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
	
		// Perform the action
		$this->pdh->put('roles', 'update_role', array($arole_id, $arole_name, $arole_classes));
		$this->pdh->process_hook_queue();
		echo "<script>parent.window.location.href = 'manage_roles.php';</script>";
	}

	public function display_edit(){
		// Load the roles
		$row = ($this->in->get('editid', 0) > 0) ? $this->pdh->get('roles', 'roles', array($this->in->get('editid', 0))) : array();
		$this->tpl->assign_vars(array(
			'S_ADD'			=> true,
			'EDITID'		=> $this->in->get('editid'),
			'MULTISELECT'	=> $this->jquery->MultiSelect('role_classes', $this->game->get('classes'), ((isset($row['classes'])) ? $row['classes'] : ''), array('width' => 350, 'height' => 70)),
			'REALNAME'		=> (isset($row['name'])) ? $row['name'] : '',
			'BUTTON_NAME'	=> ($this->in->get('editid')) ? 'upd': 'add',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('rolemanager'),
			'template_file'		=> 'admin/manage_roles.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}

	public function display(){
		// The jQuery stuff
		$this->confirm_delete($this->user->lang('delete_rolestext'));
		$this->jquery->Dialog('newRole', $this->user->lang('role_new'), array('url'=>"manage_roles.php".$this->SID."&adddialog=true", 'width'=>'600', 'height'=>'260'));
		$this->jquery->Dialog('editRole', $this->user->lang('edit_role2'), array('url'=>"manage_roles.php".$this->SID."&editid='+editid+'", 'width'=>'600', 'height'=>'260', 'withid' => 'editid'));
		$this->jquery->Dialog('ResetRoles', '', array('custom_js'=> "window.location = 'manage_roles.php".$this->SID."&reset=true&link_hash=".$this->CSRFGetToken('reset')."';", 'message'=> $this->user->lang('reset_rolestext')), 'confirm');

		// Build the HPTT Table
		$view_list			= $this->pdh->get('roles', 'id_list');
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_roles', 'hptt_manageroles_actions');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_roles.php'));
		$footer_text		= sprintf($this->user->lang('rolemanager_footcount'), count($view_list));
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= '?sort='.$this->in->get('sort');

		$this->tpl->assign_vars(array(
			'ROLES'				=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), 40, $footer_text),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
		));
		
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('rolemanager'),
			'template_file'		=> 'admin/manage_roles.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Roles', Manage_Roles::__shortcuts());
registry::register('Manage_Roles');
?>