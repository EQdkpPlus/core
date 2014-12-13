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

class Manage_Itempools extends page_generic {

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_event_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
		);
		parent::__construct('a_event_', $handler);
		$this->process();
	}

	public function save() {
		$itemp = $this->get_post();
		$ip_id = $this->in->get('ip_id',0);
		if($itemp) {
			if($ip_id) {
				$retu = $this->pdh->put('itempool', 'update_itempool', array($ip_id, $itemp['name'], $itemp['desc']));
			} else {
				$retu = $this->pdh->put('itempool', 'add_itempool', array($itemp['name'], $itemp['desc']));
			}
			if(!$retu) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $itemp['name'], 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => $itemp['name'], 'color' => 'green');
			}
		}
		$this->display($message);
	}

	public function delete() {
		$ip_id = $this->in->get('id',0);
		if($ip_id == 1) {
			$message = array('title' => $this->user->lang('del_nosuc'), 'text' => $this->user->lang('no_del_default_itempool'), 'color' => 'red');
		} elseif($ip_id) {
			$name = $this->pdh->get('itempool', 'name', $ip_id);
			if(!$this->pdh->put('itempool', 'delete_itempool', $ip_id)) {
				$message = array('title' => $this->user->lang('del_nosuc'), 'text' => $name, 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => $name, 'color' => 'green');
			}
		}
		$this->display($message);
	}

	public function update($message=false) {
		$iid = $this->in->get('id',0);
		if($message) {
			$this->core->messages($message);
			$itemp = $this->get_post(true);
		} else {
			$itemp['name'] = $this->pdh->get('itempool', 'name', $iid);
			$itemp['desc'] = $this->pdh->get('itempool', 'desc', $iid);
		}
		$this->confirm_delete($this->user->lang('confirm_delete_itempools')."<br />".$itemp['name']);
		$this->tpl->assign_vars(array(
			'ACTION'				=> 'manage_itempools.php',
			'NAME'					=> $itemp['name'],
			'DESC'					=> $itemp['desc'],
			'IID'					=> $iid,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manitempool_title'),
			'template_file'		=> 'admin/manage_itempools_edit.html',
			'display'			=> true)
		);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$order = $this->in->get('sort','0.0');
		$red = 'RED'.str_replace('.', '', $order);

		$itempool_ids = $this->pdh->get('itempool', 'id_list');
		foreach($itempool_ids as $id) {
			$itempools[$id]['name'] = $this->pdh->get('itempool', 'name', $id);
			$itempools[$id]['desc'] = $this->pdh->get('itempool', 'desc', $id);
		}
		$sortedids = get_sortedids($itempools, explode('.', $order), array('name', 'desc'));

		foreach($sortedids as $id) {
			$this->tpl->assign_block_vars('itempools', array(
				'ID'	=> $id,
				'NAME'	=> $itempools[$id]['name'],
				'DESC'	=> $itempools[$id]['desc'])
			);
		}

		$this->tpl->assign_vars(array(
			$red 			=> '_red',
			'SID'			=> $this->SID,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manitempool_title'),
			'template_file'		=> 'admin/manage_itempools.html',
			'display'			=> true)
		);
	}

	private function get_post($norefresh=false) {
		$ip_id = $this->in->get('ip_id',0);
		$itemp['name'] = $this->in->get('name','');
		$itemp['desc'] = $this->in->get('desc','');
		if(empty($itemp['name'])){
			$missing[] = $this->user->lang('name');
		}
		if(empty($itemp['desc'])){
			$missing[] = $this->user->lang('description');
		}
		if($missing AND !$norefresh){
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		return $itemp;
	}
}
registry::register('Manage_Itempools');
?>