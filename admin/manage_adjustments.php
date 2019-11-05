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

class ManageAdjs extends page_generic {

	public function __construct(){
		$handler = array(
			'bulk_upd'	=> array('process' => 'bulk_update', 'csrf'=> true, 'check' => 'a_indivadj_upd'),
			'save' => array('process' => 'save', 'check' => 'a_indivadj_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
			'copy'	=> array('process' => 'copy', 'check' => 'a_indivadj_add'),
			'bulk'	=> array('process' => 'display_bulkedit', 'csrf'=> false, 'check' => 'a_indivadj_upd'),
		);
		parent::__construct('a_indivadj_', $handler, array('adjustment', 'reason'), null, 'selected_ids[]', 'a');
		$this->process();
	}

	public function bulk_update(){
		$strSelectedIDs = $this->in->get('selected_ids');
		$arrSelected = explode('|', $strSelectedIDs);
		$arrBulk = $this->in->getArray('bulk', 'int');

		//Get new item information
		$arrNewValues['member'] = $this->in->get('member', 0);
		$arrNewValues['reason'] = $this->in->get('reason','');
		$arrNewValues['value'] = $this->in->get('value',0.0);
		$arrNewValues['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$arrNewValues['raid_id'] = $this->in->get('raid_id',0);
		$arrNewValues['event'] = $this->in->get('event',0);

		$arrCheckboxes = array('member', 'reason', 'value', 'date', 'raid_id', 'event');
		//Für jedes Item
		$messages = array();
		foreach($arrSelected as $adjID){
			//Hole alte daten
			$adj['member']	= $this->pdh->get('adjustment', 'member', array($adjID));
			$adj['reason'] = $this->pdh->get('adjustment', 'reason', array($adjID));
			$adj['value']	= $this->pdh->get('adjustment', 'value', array($adjID));
			$adj['date']	= $this->pdh->get('adjustment', 'date', array($adjID));
			$adj['raid_id'] = $this->pdh->get('adjustment', 'raid_id', array($adjID));
			$adj['event']	= $this->pdh->get('adjustment', 'event', array($adjID));

			foreach($arrBulk as $key => $val){
				if(!in_array($key, $arrCheckboxes) || !$val) continue;

				$adj[$key] = $arrNewValues[$key];
			}

			$retu = $this->pdh->put('adjustment', 'update_adjustment', array($adjID, $adj['value'], $adj['reason'], array($adj['member']), $adj['event'], $adj['raid_id'], $adj['date'], true));

			if(!$retu){
				$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $adj['name'], 'color' => 'red');
			}
		}
		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('bulkedit'), 'color' => 'green');
		$this->pdh->process_hook_queue();
		$this->display($messages);
	}

	public function copy(){
		$this->core->message($this->user->lang('copy_info'), $this->user->lang('copy'));
		$this->update(false, true);
	}

	public function save() {
		$adj = $this->get_post();
		if($this->in->get('a',0) && !$this->in->exists('copy')) {
			$retu = $this->pdh->put('adjustment', 'update_adjustment', array($this->in->get('a',0), $adj['value'], $adj['reason'], $adj['members'], $adj['event'], $adj['raid_id'], $adj['date'], true));
		} else {
			$retu = $this->pdh->put('adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], $adj['members'], $adj['event'], $adj['raid_id'], $adj['date']));
		}
		if(!$retu) {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $adj['reason'], 'color' => 'red');
		} else {
			$message = array('title' => $this->user->lang('save_suc'), 'text' => $adj['reason'], 'color' => 'green');
		}
		$this->display($message);
	}

	public function delete() {
		$ids = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $s_id)
			{
				$new_ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($this->pdh->get('adjustment', 'group_key', array($s_id))));
				$ids = array_merge($ids, $new_ids);
			}
		} else {
			$ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($this->in->get('selected_ids','')));
		}

		$retu = array();
		foreach($ids as $id) {
			$retu[$id] = $this->pdh->put('adjustment', 'delete_adjustment', array($id));
		}
		foreach($retu as $id => $suc) {
			if($suc) {
				$pos[] = stripslashes($this->pdh->get('adjustment', 'reason', array($id)));
			} else {
				$neg[] = stripslashes($this->pdh->get('adjustment', 'reason', array($id)));
			}
		}
		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
		}
		if(!empty($neg)) {
			$messages[] = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $neg), 'color' => 'red');
		}
		$this->display($messages);
	}

	public function update($message=false, $copy=false) {
		//fetch members for select
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));

		//fetch raids for select
		$raids = array(0 => '');
		$raidids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
		foreach($raidids as $id) {
			$raids[$id] = '#ID:'.$id.' - '.$this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($id)))).' '.date('d.m.y', $this->pdh->get('raid', 'date', array($id)));
		}

		//fetch events for select
		$events = array();
		$event_ids = $this->pdh->get('event', 'id_list');
		foreach($event_ids as $id) {
			$events[$id] = $this->pdh->get('event', 'name', array($id));
		}
		if($message) {
			$this->core->messages($message);
			$adj = $this->get_post(true);
		} elseif($this->in->get('a',0)) {
			$grp_key = $this->pdh->get('adjustment', 'group_key', array($this->in->get('a',0)));
			$ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($grp_key));
			foreach($ids as $id)
			{
				$adj['members'][] = $this->pdh->get('adjustment', 'member', array($id));
			}
			$adj['reason'] = $this->pdh->get('adjustment', 'reason', array($id));
			$adj['value'] = $this->pdh->get('adjustment', 'value', array($id));
			$adj['date'] = $this->pdh->get('adjustment', 'date', array($id));
			$adj['raid_id'] = $this->pdh->get('adjustment', 'raid_id', array($id));
			$adj['event'] = $this->pdh->get('adjustment', 'event', array($id));

			//Add additional members
			if (count($adj['members']) > 0){
				$arrIDList = array_keys($members);
				$blnResort = false;
				foreach($adj['members'] as $member_id){
					if (!isset($members[$member_id])) {
						$arrIDList[] = $member_id;
						$blnResort = true;
					}
				}
				if ($blnResort) $members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($arrIDList, 'member', 'name', 'asc')));
			}
		}

		//fetch adjustment-reasons
		$adjustment_reasons = $this->pdh->aget('adjustment', 'reason', 0, array($this->pdh->get('adjustment', 'id_list')));
		$this->jquery->Autocomplete('reason', array_unique($adjustment_reasons));
		$this->confirm_delete($this->user->lang('confirm_delete_adjustment')."<br />".((isset($adj['reason'])) ? $adj['reason'] : ''), '', true);

		if(!isset($adj['reason'])) $adj['reason'] = '';
		$this->tpl->assign_vars(array(
			'GRP_KEY'		=> (isset($grp_key) && !$copy) ? $grp_key : '',
			'REASON'		=> $adj['reason'],
			'RAID'			=> (new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($adj['raid_id'])) ? $adj['raid_id'] : ''))))->output(),
			'MEMBERS'		=> (new hmultiselect('members', array('options' => $members, 'value' => ((isset($adj['members'])) ? $adj['members'] : ''), 'width' => 350, 'filter' => true)))->output(),
			'DATE'			=> (new hdatepicker('date', array('value' => $this->time->user_date(((isset($adj['date'])) ? $adj['date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
			'VALUE'			=> (isset($adj['value'])) ? $adj['value'] : '',
			'S_COPY'		=> ($copy) ? true : false,
			'EVENT'			=> (new hdropdown('event', array('options' => $events, 'value' => ((isset($adj['event'])) ? $adj['event'] : ''))))->output(),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manadjs_title'),
			'template_file'		=> 'admin/manage_adjustments_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manadjs_title'), 'url'=>$this->root_path.'admin/manage_adjustments.php'.$this->SID],
				['title'=>$this->user->lang((($adj['reason'] == '')?'add_adjustment':'editing_adj')), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display_bulkedit($messages=false) {
		$arrItems = $this->in->getArray('selected_ids', 'int');
		//if(count($arrItems) === 0) $this->display();

		//fetch members for select
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));

		//fetch raids for select
		$raids = array(0 => '');
		$raidids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
		foreach($raidids as $id) {
			$raids[$id] = '#ID:'.$id.' - '.$this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($id)))).' '.date('d.m.y', $this->pdh->get('raid', 'date', array($id)));
		}

		//fetch events for select
		$events = array();
		$event_ids = $this->pdh->get('event', 'id_list');
		foreach($event_ids as $id) {
			$events[$id] = $this->pdh->get('event', 'name', array($id));
		}

		if($message) {
			$this->core->messages($message);
			$adj = $this->get_post(true);
			$adj['member'] = $this->in->get('member', 0);
		}

		//fetch adjustment-reasons
		$adjustment_reasons = $this->pdh->aget('adjustment', 'reason', 0, array($this->pdh->get('adjustment', 'id_list')));
		$this->jquery->Autocomplete('reason', array_unique($adjustment_reasons));
		$this->confirm_delete($this->user->lang('confirm_delete_adjustment')."<br />".((isset($adj['reason'])) ? $adj['reason'] : ''), '', true);

		$this->tpl->assign_vars(array(
				'GRP_KEY'		=> (isset($grp_key) && !$copy) ? $grp_key : '',
				'REASON'		=> (isset($adj['reason'])) ? $adj['reason'] : '',
				'RAID'			=> (new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($adj['raid_id'])) ? $adj['raid_id'] : ''))))->output(),
				'MEMBERS'		=> (new hsingleselect('member', array('options' => $members, 'value' => ((isset($adj['member'])) ? $adj['member'] : ''), 'width' => 350, 'filter' => true)))->output(),
				'DATE'			=> (new hdatepicker('date', array('value' => $this->time->user_date(((isset($adj['date'])) ? $adj['date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'VALUE'			=> (isset($adj['value'])) ? $adj['value'] : '',
				'S_COPY'		=> ($copy) ? true : false,
				'EVENT'			=> (new hdropdown('event', array('options' => $events, 'value' => ((isset($adj['event'])) ? $adj['event'] : ''))))->output(),
				'BULK_ITEMS'	=> implode('|', $arrItems),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manadjs_title').' - '.$this->user->lang('bulkedit'),
			'template_file'		=> 'admin/manage_adjustments_bulkedit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manadjs_title'), 'url'=>$this->root_path.'admin/manage_adjustments.php'.$this->SID],
				['title'=>$this->user->lang('bulkedit'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		$view_list = $this->pdh->aget('adjustment', 'group_key', 0, array($this->pdh->get('adjustment', 'id_list', array())));
		$view_list = array_flip($view_list);
		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_adjustments', 'hptt_admin_manage_adjustments_adjlist');
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_adjustments.php', '%link_url_suffix%' => '&amp;upd=true', '%raid_link_url%' => 'manage_raids.php', '%raid_link_url_suffix%' => '&amp;upd=true'));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');

		//footer
		$adj_count = count($view_list);

		$this->confirm_delete($this->user->lang('confirm_delete_adjustments'));

		$arrMenuItems = array(
				0 => array(
						'type'	=> 'javascript',
						'icon'	=> 'fa-trash-o',
						'text'	=> $this->user->lang('delete'),
						'perm'	=> true,
						'name'	=> 'mdel',
						'js'	=> "$('#del_members').click();",
						'append'=> '<input name="mdel" onclick="delete_warning();" id="del_members" class="mainoption bi_delete" type="button" style="display:none;" />',
				),
				1 => array(
						'type'	=> 'button',
						'icon'	=> 'fa-pencil',
						'text'	=> $this->user->lang('edit'),
						'perm'	=> true,
						'name'	=> 'bulk',
				),
		);

		$this->tpl->assign_vars(array(
			'ADJ_LIST'			=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_rlimit'], false),
			'PAGINATION' 		=> generate_pagination('manage_adjustments.php'.$sort_suffix, $adj_count, $this->user->data['user_rlimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'ADJ_COUNT'			=> $adj_count,
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_adjustments').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
			'BUTTON_MENU'		=> $this->core->build_dropdown_menu($this->user->lang('selected_elements').'...', $arrMenuItems, '', 'manage_members_menu', array("input[name=\"selected_ids[]\"]")),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manadjs_title'),
			'template_file'		=> 'admin/manage_adjustments.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manadjs_title'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function get_post($norefresh=false) {
		$adj['reason'] = $this->in->get('reason','');
		foreach($this->in->getArray('members','int') as $member) {
			$adj['members'][] = (int) $member;
		}
		if(!$adj['reason']) {
			$missing[] = $this->user->lang('reason');
		}
		if(empty($adj['members'])) {
			$missing[] = $this->user->lang('members');
		}
		if(!empty($missing) AND !$norefresh) {
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ',$missing), 'color' => 'red'));
		}
		$adj['value'] = $this->in->get('value',0.0);
		$adj['date'] = $this->time->fromformat($this->in->get('date', '1.1.1970'), 1);
		$adj['raid_id'] = $this->in->get('raid_id',0);
		$adj['event'] = $this->in->get('event',0);
		return $adj;
	}
}
registry::register('ManageAdjs');
