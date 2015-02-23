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

class ManageAdjs extends page_generic {

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_indivadj_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
			'copy'		=> array('process' => 'copy', 'check' => 'a_raid_add'),
		);
		parent::__construct('a_indivadj_', $handler, array('adjustment', 'reason'), null, 'selected_ids[]', 'a');
		$this->process();
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
		
		$this->tpl->assign_vars(array(
			'GRP_KEY'		=> (isset($grp_key) && !$copy) ? $grp_key : '',
			'REASON'		=> (isset($adj['reason'])) ? $adj['reason'] : '',
			'RAID'			=> new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($adj['raid_id'])) ? $adj['raid_id'] : ''))),
			'MEMBERS'		=> $this->jquery->MultiSelect('members', $members, ((isset($adj['members'])) ? $adj['members'] : ''), array('width' => 350, 'filter' => true)),
			'DATE'			=> $this->jquery->Calendar('date', $this->time->user_date(((isset($adj['date'])) ? $adj['date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'VALUE'			=> (isset($adj['value'])) ? $adj['value'] : '',
			'S_COPY'		=> ($copy) ? true : false,
			'EVENT'			=> new hdropdown('event', array('options' => $events, 'value' => ((isset($adj['event'])) ? $adj['event'] : ''))),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manadjs_title'),
			'template_file'		=> 'admin/manage_adjustments_edit.html',
			'display'			=> true)
		);
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
		$footer_text = sprintf($this->user->lang('listadj_footcount'), $adj_count, $this->user->data['user_alimit']);
		
		$this->confirm_delete($this->user->lang('confirm_delete_adjustments'));
		
		$this->tpl->assign_vars(array(
			'SID'			=> $this->SID,
			'ADJ_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_alimit'], $footer_text),
			'PAGINATION' 	=> generate_pagination('manage_adjustments.php'.$sort_suffix, $adj_count, $this->user->data['user_alimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manadjs_title'),
			'template_file'		=> 'admin/manage_adjustments.html',
			'display'			=> true)
		);
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
?>