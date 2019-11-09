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

class Manage_Multidkp extends page_generic {

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_event_add', 'csrf'=>true),
			'save_sort' => array('process' => 'save_sort', 'check' => 'a_event_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
			'info'	=> array('process' => 'display_info', 'check' => 'a_event_add', 'csrf'=>false),
		);
		parent::__construct('a_event_', $handler);
		$this->process();
	}

	public function save() {
		$mdkp_id = $this->in->get('id',0);
		$mdkp = $this->get_post();
		if($mdkp) {
			if($mdkp_id) {
				$retu = $this->pdh->put('multidkp', 'update_multidkp', array($mdkp_id, $mdkp['name'], $mdkp['desc'], $mdkp['events'], $mdkp['itempools'], $mdkp['no_attendance']));
			} else {
				$retu = $this->pdh->put('multidkp', 'add_multidkp', array($mdkp['name'], $mdkp['desc'], $mdkp['events'], $mdkp['itempools'], $mdkp['no_attendance']));
			}
			if(!$retu) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $mdkp['name'], 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => $mdkp['name'], 'color' => 'green');
			}
		}
		$this->display($message);
	}

	public function save_sort(){
		$arrSort = $this->in->getArray("sort", "int");

		$this->pdh->put('multidkp', 'save_sort', array($arrSort));


		$message = array('title' => $this->user->lang('success'),'text' => $this->user->lang('save_suc'), 'color' => 'green');
		$this->display($message);
	}

	public function delete() {
		$mdkp_id = $this->in->get('id',0);
		if($mdkp_id) {
			$name = $this->pdh->get('multidkp', 'name', $mdkp_id);
			if(!$this->pdh->put('multidkp', 'delete_multidkp', array($mdkp_id))) {
				$message = array('title' => $this->user->lang('del_nosuc'), 'text' => $name, 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => $name, 'color' => 'green');
			}
		}
		$this->display($message);
	}

	public function update($message=false, $id=0) {
		$mdkp_id = ($id !== 0) ? $id : $this->in->get('id',0);

		$mdkp = array('events' => array(), 'itempools' => array(), 'no_attendance' => array());
		if($message) {
			$this->core->messages($message);
			$this->pdh->process_hook_queue();
			$mdkp = $this->get_post(true);
		} else {
			$mdkp['name'] = $this->pdh->get('multidkp', 'name', array($mdkp_id));
			$mdkp['desc'] = $this->pdh->get('multidkp', 'desc', array($mdkp_id));
			$mdkp['events'] = array_merge($this->pdh->get('multidkp', 'event_ids', array($mdkp_id, true)), $this->pdh->get('multidkp', 'event_ids', array($mdkp_id)));
			$mdkp['itempools'] = $this->pdh->get('multidkp', 'itempool_ids', array($mdkp_id));
			$mdkp['no_attendance'] = array_diff($this->pdh->get('multidkp', 'event_ids', array($mdkp_id)), $this->pdh->get('multidkp', 'event_ids', array($mdkp_id, true)));
		}

		//events
		if($this->config->get('dkp_easymode')){
			//1 event can only be associated to 1 multidkp pool = easy mode
			$eventList = $this->pdh->sort($this->pdh->get('event', 'id_list'), 'event', 'name');
			foreach($eventList as $eventID){
				$pools = $this->pdh->get('multidkp', 'mdkpids4eventid', array($eventID));
				if(count($pools) > 0 && $mdkp_id === 0) continue;

				if(count($pools) > 0 && $mdkp_id !==0 && !in_array($mdkp_id, $pools)) continue;

				$events[$eventID] = $this->pdh->get('event', 'name', array($eventID));
			}


		} else {
			$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->sort($this->pdh->get('event', 'id_list'), 'event', 'name')));
		}


		$sel_events = $this->pdh->aget('event', 'name', 0, array($this->pdh->sort($mdkp['events'], 'event', 'name')));

		//itempools
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->sort($this->pdh->get('itempool', 'id_list'), 'itempool', 'name')));

		$this->confirm_delete($this->user->lang('confirm_delete_multi').'<br />'.$mdkp['name']);
		$this->tpl->assign_vars(array(
			'NAME'					=> $mdkp['name'],
			'DESC'					=> $mdkp['desc'],
			'EVENT_SEL'				=> (new hmultiselect('events', array('options' => $events, 'value' => $mdkp['events'], 'width' => 300, 'filter' => true)))->output(),
			'ITEMPOOL_SEL'			=> (new hmultiselect('itempools', array('options' => $itempools, 'value' => $mdkp['itempools'], 'width' => 300)))->output(),
			'NO_ATT_SEL'			=> (new hmultiselect('no_atts', array('options' => $sel_events, 'value' => $mdkp['no_attendance'], 'width' => 300, 'filter' => true)))->output(),
			'MDKP_ID'				=> $mdkp_id,
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manmdkp_title'),
			'template_file'		=> 'admin/manage_multidkp_add.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manmdkp_title'), 'url'=>$this->root_path.'admin/manage_multidkp.php'.$this->SID],
				['title'=>(($mdkp_id)?$mdkp['name']:$this->user->lang('Multi_addkonto')), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display_info(){
		$mdkpid = $this->in->get('info', 0);

		//Events -
		$arrEvents = $this->pdh->get('multidkp', 'event_ids', array($mdkpid));
		foreach($arrEvents as $eventID){
			$this->tpl->assign_block_vars('event_row', array(
				'NAME' => $this->pdh->get('event', 'name', array($eventID)),
				'LINK' => 'manage_events.php'.$this->SID.'&event_id='.$eventID.'&upd=true',
				'ADDITION' => '',
			));
		}

		//Itempools
		$arrItempools = $this->pdh->get('multidkp', 'itempool_ids', array($mdkpid));
		foreach($arrItempools as $eventID){
			$this->tpl->assign_block_vars('itempool_row', array(
					'NAME' => $this->pdh->get('itempool', 'name', array($eventID)),
					'LINK' => 'manage_itempools.php'.$this->SID.'&id='.$eventID.'&upd=true',
					'ADDITION' => '',
			));
		}

		//APAs
		$apas = register('auto_point_adjustments')->list_apas();
		//Entweder Pool ist angegeben, oder das Event ist im Pool
		$intApaCount = 0;
		foreach($apas as $apaid => $arrAPA){
			if(isset($arrAPA['event'])){
				if(in_array($arrAPA['event'], $arrEvents)){
					#d("APA ".$arrAPA['name']);

					$this->tpl->assign_block_vars('apa_row', array(
							'NAME' => $arrAPA['name'],
							'LINK' => 'manage_auto_points.php'.$this->SID,
							'ADDITION' => $this->user->lang('apa_type_'.$arrAPA['type']),
					));

					$intApaCount++;
				}

			} elseif(isset($arrAPA['pools'])){
				if(in_array($mdkpid, $arrAPA['pools'])){
					#d("APA ".$arrAPA['name']);

					$this->tpl->assign_block_vars('apa_row', array(
							'NAME' => $arrAPA['name'],
							'LINK' => 'manage_auto_points.php'.$this->SID,
							'ADDITION' => $this->user->lang('apa_type_'.$arrAPA['type']),
					));

					$intApaCount++;
				}
			}
		}





		$this->tpl->assign_vars(array(
				'MDKP_NAME' => $this->pdh->get('multidkp', 'name', array($mdkpid)),
				'S_MDKP_EVENTS' => (count($arrEvents)),
				'S_MDKP_ITEMPOOLS' => (count($arrItempools)),
				'S_MDKP_APA' => $intApaCount,
				'S_MDKP_TWINKS' => $this->config->get('show_twinks'),
				'MDKP_LAYOUT' => $this->config->get('eqdkp_layout'),
				'MDKP_LAYOUT_INFO' => $this->user->lang('lm_layout_'.str_replace('user_', '', $this->config->get('eqdkp_layout'))),
		));

		$presets = array(
				array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'spent', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'adjustment', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'current', 'sort' => true, 'th_add' => '', 'td_add' => ''),
		);
		$arrPresets = array();
		foreach ($presets as $preset){
			$pre = $this->pdh->pre_process_preset($preset['name'], $preset);
			if(empty($pre))
				continue;

				$arrPresets[$pre[0]['name']] = $pre[0];
		}

		foreach($arrPresets as $strName => $arrPreset){
			$this->tpl->assign_block_vars('preset_row', array(
					'NAME' => $strName,
					'LINK' => '',
					'ADDITION' => 'Module <i style="font-style:italic">'.$arrPreset[0].'</i>, Function <i style="font-style:italic">'.$arrPreset[1].'</i>',
			));
		}

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manmdkp_info'),
				'template_file'		=> 'admin/manage_multidkp_info.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('manmdkp_title'), 'url'=>$this->root_path.'admin/manage_multidkp.php'.$this->SID],
						['title'=>$this->user->lang('manmdkp_info').': '.$this->pdh->get('multidkp', 'name', array($mdkpid)), 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$order = $this->in->get('sort','0.0');
		$red = 'RED'.str_replace('.', '', $order);
		$mdkp_ids = $this->pdh->get('multidkp', 'id_list');
		$mdkp = array();
		foreach($mdkp_ids as $id)
		{
			$mdkp[$id]['sortid'] = $this->pdh->get('multidkp', 'sortid', $id);
			$mdkp[$id]['name'] = $this->pdh->get('multidkp', 'name', $id);
			$mdkp[$id]['desc'] = $this->pdh->get('multidkp', 'desc', $id);
			$mdkp[$id]['events'] = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('multidkp', 'event_ids', $id)));
			$mdkp[$id]['no_atts'] = $this->pdh->get('multidkp', 'no_attendance', array($id));
			$ip_ids = $this->pdh->get('multidkp', 'itempool_ids', $id);
			$mdkp[$id]['itempools'] = $this->pdh->aget('itempool', 'name', 0, array(((is_array($ip_ids)) ? $ip_ids : array())));
		}

		$sort_ids = get_sortedids($mdkp, explode('.', $order), array('sortid', 'name', 'desc'));
		foreach($sort_ids as $id) {
			$event_string = array();
			foreach($mdkp[$id]['events'] as $eid => $event) {
				$event_string[] = "<span class='".((in_array($eid, $mdkp[$id]['no_atts'])) ? 'negative' : 'positive')."'>".$event."</span>";
			}
			$this->tpl->assign_block_vars('multi_row', array(
				'ID'		=> $id,
				'NAME'		=> $mdkp[$id]['name'],
				'DESC'		=> $mdkp[$id]['desc'],
				'EVENTS'	=> implode(', ', $event_string),
				'ITEMPOOLS'	=> implode(', ', $mdkp[$id]['itempools']),
			));
		}
		$this->tpl->assign_vars(array(
			'SID'	=> $this->SID,
			$red 	=> '_red',
			'LISTMULTI_COUNT'	=> count($sort_ids),
		));

		$this->tpl->add_js("
			$(\"#multidkpsort tbody\").sortable({
				cancel: '.not-sortable, input, tr th.footer, th',
				cursor: 'pointer',
			});
		", "docready");

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manmdkp_title'),
			'template_file'		=> 'admin/manage_multidkp.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manmdkp_title'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function get_post($norefresh=false) {
		$mdkp = array();
		$mdkp['name'] = $this->in->get('name','');
		$mdkp['desc'] = $this->in->get('desc','');
		$mdkp['events'] = $this->in->getArray('events','int');
		$missing = array();
		if(!$mdkp['name']) {
			$missing[] = $this->user->lang('Multi_kontoname_short');
		}
		/*
		if(!$mdkp['desc']) {
			$missing[] = $this->user->lang('description');
		}
		*/
		if((!$mdkp['events'] || (in_array("", $mdkp['events']))) && !$this->config->get('dkp_easymode')) {
			$missing[] = $this->user->lang('events');
		}
		if(count($missing) AND !$norefresh) {
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ', $missing), 'color' => 'red'), $this->in->get('id', 0));
		}
		$mdkp['itempools'] = $this->in->getArray('itempools','int');
		$mdkp['no_attendance'] = $this->in->getArray('no_atts', 'int');
		return $mdkp;
	}
}
registry::register('Manage_Multidkp');
