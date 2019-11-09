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

class Manage_Calendars extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_calendars_man');
		$handler = array(
			'save' => array('process' => 'save', 'csrf'=>true),
			'c'=> array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('calendars', 'name'), null, 'calendar_ids[]');
		$this->process();
	}

	public function save() {
		$calendarID = $this->in->get('c', 0);

		$arrValues = $this->get_post();

		if($calendarID > 0){
			$retu = $this->pdh->put('calendars', 'update_calendar', array($arrValues['id'], $arrValues['name'], $arrValues['color'], $arrValues['feed'], $arrValues['private'], $arrValues['type'], $arrValues['restricted'], 0, $arrValues['permissions']));
		} else {
			$retu = $this->pdh->put('calendars', 'add_calendar', array($arrValues['id'], $arrValues['name'], $arrValues['color'], $arrValues['feed'], $arrValues['private'], $arrValues['type'], $arrValues['restricted'], 0, $arrValues['permissions']));
		}

		if(!$retu) {
			$message = array('title' => $this->user->lang('error'), 'text' => $this->user->lang('save_nosuc'), 'color' => 'red');
		} else {
			$message = array('title' => $this->user->lang('success'), 'text' => $this->user->lang('save_suc'), 'color' => 'green');
		}

		$this->display($message);
	}

	public function delete() {
		$calendar_ids = $this->in->getArray('calendar_ids', 'int');
		if($calendar_ids) {
			foreach($calendar_ids as $id) {
				$names[] = $this->pdh->get('calendars', 'name', ($id));
				$retu[] = $this->pdh->put('calendars', 'delete_calendar', array($id));
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $names), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_calendars_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function edit(){
		$intCalendarID = $this->in->get('c', 0);

		$types = array(
				1	=> $this->user->lang(array('calendars_types', 1)),
				2	=> $this->user->lang(array('calendars_types', 2)),
				3	=> $this->user->lang(array('calendars_types', 3)),
		);

		$usergroups = $this->pdh->get('user_groups', 'id_list');
		if(($usergrpkey = array_search(1, $usergroups)) !== false) {
			unset($usergroups[$usergrpkey]);
		}

		if($intCalendarID > 0){
			$this->tpl->assign_vars(array(
					'DELETABLE'		=> $this->pdh->get('calendars', 'is_deletable', array($intCalendarID)),
					'ID'			=> $intCalendarID,
					'NAME'			=> $this->pdh->get('calendars', 'name', array($intCalendarID)),
					'TYPE'			=> (new hdropdown('type', array('options' => $types, 'value' => $this->pdh->get('calendars', 'type', array($intCalendarID)))))->output(),
					'COLOR'			=> (new hcolorpicker('color', array('value' =>  $this->pdh->get('calendars', 'color', array($intCalendarID)))))->output(),
					'FEED'			=> $this->pdh->get('calendars', 'feed', array($intCalendarID)),
					'RESTRICTED'	=> (new hradio('restricted', array('value' => !$this->pdh->get('calendars', 'restricted', array($intCalendarID)))))->output(),
					'PERMISSIONS'	=> (new hmultiselect('permissions', array('options' => $this->pdh->aget('user_groups', 'name', 0, array($usergroups)), 'value' => $this->pdh->get('calendars', 'permissions', array($intCalendarID)))))->output(),
			));
		} else {
			$this->tpl->assign_vars(array(
					'DELETABLE'		=> 1,
					'ID'			=> -1,
					'NAME'			=> '',
					'TYPE'			=> (new hdropdown('type', array('options' => $types, 'value' => 0)))->output(),
					'COLOR'			=> (new hcolorpicker('color', array('value' =>  '')))->output(),
					'FEED'			=> '',
					'RESTRICTED'	=> (new hradio('restricted', array('value' => !1)))->output(),
					'PERMISSIONS'	=> (new hmultiselect('permissions', array('options' => $this->pdh->aget('user_groups', 'name', 0, array($usergroups)), 'value' => array())))->output(),
			));
		}

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manage_calendars'),
				'template_file'		=> 'admin/manage_calendars_edit.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('manage_calendars'), 'url'=>$this->root_path.'admin/manage_calendars.php'.$this->SID],
						['title'=>(($intCalendarID > 0) ? $this->pdh->get('calendars', 'name', [$intCalendarID]) : $this->user->lang('add_calendar')), 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$todisable = array();
		if($this->config->get('disable_guild_features')){
			$todisable[1] = 1;
		}
		$types = array(
			1	=> $this->user->lang(array('calendars_types', 1)),
			2	=> $this->user->lang(array('calendars_types', 2)),
			3	=> $this->user->lang(array('calendars_types', 3)),
		);

		// ranks
		$new_id = 0;
		$order = $this->in->get('order','0.0');
		$ranks = $this->pdh->aget('calendars', 'name', 0, array($this->pdh->get('calendars', 'idlist')));
		unset($ranks[0]);
		if($order == '0.0') {
			arsort($ranks);
		} else {
			asort($ranks);
		}
		$key = 0;
		$new_id = 1;
		ksort($ranks);

		$usergroups = $this->pdh->get('user_groups', 'id_list');
		if(($usergrpkey = array_search(1, $usergroups)) !== false) {
			unset($usergroups[$usergrpkey]);
		}

		foreach($ranks as $id => $name) {
			$this->tpl->assign_block_vars('calendars', array(
				'DELETABLE'		=> $this->pdh->get('calendars', 'is_deletable', array($id)),
				'ID'			=> $id,
				'NAME'			=> $name,
				'TYPE'			=> $types[$this->pdh->get('calendars', 'type', array($id))],
				'COLOR'			=> '<div style="background-color:'.$this->pdh->get('calendars', 'color', array($id)).'; height:16px; width:16px;display:inline-block"></div>',
				'PRIVATE'		=> $this->pdh->get('calendars', 'private', array($id)),
			));
			$key++;
			$new_id = ($new_id == $id) ? $id+1 : $new_id;
		}
		$this->confirm_delete($this->user->lang('confirm_delete_calendars'));

		$this->tpl->assign_vars(array(
				'CALENDAR_COUNT'	=> count($ranks),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manage_calendars'),
			'template_file'		=> 'admin/manage_calendars.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manage_calendars'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function get_post() {
		$calendars = array(
				'id'			=> $this->in->get('c', 0),
				'name'			=> $this->in->get('name',''),
				'feed'			=> $this->in->get('feed','', 'raw'),
				'color'			=> $this->in->get('color',''),
				'private'		=> $this->in->get('suffix',0),
				'type'			=> $this->in->get('type',0),
				'restricted'	=> !$this->in->get('restricted',0),
				'permissions'	=> $this->in->getArray('permissions', 'int')
		);

		return $calendars;

	}
}
registry::register('Manage_Calendars');
