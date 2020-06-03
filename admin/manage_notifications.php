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

class ManageNotifications extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_notifications_man');

		$handler = array(
			'save'	=> array('process' => 'save', 'csrf'=>true),
			'id'	=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('notification_types', 'id'), null, 'selected_ids[]');
		$this->process();

	}


	public function save() {
		$strNotificationID = $this->in->get('id');
		$intPrio = $this->in->get('priority', 0);
		$strDefault = $this->in->get('default');
		$blnGroup = $this->in->get('group', 0);
		$intGroupAt = $this->in->get('group_at', 0);
		if($intGroupAt < 0) $intGroupAt = 0;
		$strIcon = $this->in->get('icon');

		$blnResult = $this->pdh->put('notification_types', 'update', array($strNotificationID, $intPrio, $strDefault, $blnGroup, $intGroupAt, $strIcon));

		$blnOverwrite = $this->in->get('overwrite', 0);
		
		if($blnOverwrite && $blnResult){
			//Overwrite
			$arrUsers = $this->pdh->get('user', 'id_list', array());
			foreach($arrUsers as $intUserID){
				$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));

				$arrNotificationSettings['ntfy_'.$strNotificationID] = $strDefault;
				
				$arrQuery['notifications'] = serialize($arrNotificationSettings);
				$this->pdh->put('user', 'update_user', array($intUserID, $arrQuery, false, false));
			}
		}

		if($blnResult){
			$message = array('title' => $this->user->lang('save_suc'), 'text' => $strNotificationID, 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $strNotificationID, 'color' => 'red');

		}
		$this->display($message);
	}

	public function edit() {
		$strNotificationID = $this->in->get('id');

		$arrData = $this->pdh->get('notification_types', 'data', array($strNotificationID));
		if(!$arrData) {
			$this->display();
			return;
		}

		$arrMethods = register('ntfy')->getAvailableNotificationMethods(true);
		array_unshift($arrMethods, register('user')->lang('notification_type_none'), register('user')->lang('notification_type_eqdkp'));

		$this->tpl->assign_vars(array(
			'NOTIFICATION_ID' 	=> $strNotificationID,
			'DD_PRIORITY'		=> (new hdropdown('priority', array('value' => $this->pdh->get('notification_types', 'prio', array($strNotificationID)), 'options' => array(0 => $this->user->lang('notification_prio_0'), 1 => $this->user->lang('notification_prio_1'), 2 => $this->user->lang('notification_prio_2')))))->output(),
			'ICON'				=> $this->pdh->get('notification_types', 'icon', array($strNotificationID)),
			'DD_DEFAULT'		=> (new hdropdown('default', array('value' => (string)$this->pdh->get('notification_types', 'default', array($strNotificationID)), 'options' => $arrMethods)))->output(),
			'GROUP_RADIO'		=> (new hradio('group', array('value' => $this->pdh->get('notification_types', 'group', array($strNotificationID)))))->output(),
			'GROUP_AT_SPINNER'	=> (new hspinner('group_at', array('value' => $this->pdh->get('notification_types', 'group_at', array($strNotificationID)), 'min' => 0)))->output(),
			'OVERWRITE_RADIO'	=> (new hradio('overwrite', array('value' => 0)))->output(),
		));

		$this->core->set_vars([
			'page_title'    => $this->user->lang('manage_notifications').': '.$strNotificationID,
			'template_file' => 'admin/manage_notifications_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_notifications'), 'url'=>$this->root_path.'admin/manage_notifications.php'.$this->SID],
				['title'=>$strNotificationID, 'url'=>' '],
			],
			'display'       => true
		]);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$view_list = $this->pdh->get('notification_types', 'id_list', array());

		$hptt_page_settings = array(
				'name' => 'hptt_admin_notificationstype_list',
				'table_main_sub' => '%intNotificationTypeID%',
				'table_subs' => array('%intNotificationTypeID%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'manage_notifications.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 1,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
						array('name' => 'notification_types_edit', 'sort' => false, 'th_add' => '', 'td_add' => ''),
						array('name' => 'notification_types_id', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'notification_types_default', 'sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'notification_types_category', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
						array('name' => 'notification_types_prio', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
				),
		);

		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_notifications.php', '%link_url_suffix%' => ''));

		$this->tpl->assign_vars(array(
			'NOTIFICATION_LIST' => $hptt->get_html_table($this->in->get('sort')),
			'NOTIFICATION_COUNT'=> count($view_list),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_notifications'),
			'template_file'		=> 'admin/manage_notifications.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_notifications'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

}
registry::register('ManageNotifications');
