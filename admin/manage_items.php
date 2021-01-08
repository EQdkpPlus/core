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

class ManageItems extends page_generic {

	public function __construct(){
		$handler = array(
			'bulk_upd'	=> array('process' => 'bulk_update', 'csrf'=> true, 'check' => 'a_item_upd'),
			'save' => array('process' => 'save', 'check' => 'a_item_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
			'bulk'	=> array('process' => 'display_bulkedit', 'csrf'=>false, 'check' => 'a_item_upd'),
			'copy' => array('process' => 'copy', 'check' => 'a_item_add'),
		);
		parent::__construct('a_item_', $handler, array('item', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function copy(){
		$this->core->message($this->user->lang('copy_info'), $this->user->lang('copy'));
		$this->update(false, true);
	}

	public function save() {
		$item = $this->get_post();
		if($this->config->get('dkp_easymode')){
			$raid = $item['raid_id'];
			$event = $this->pdh->get('raid', 'event', array($raid));
			$arrPools = $this->pdh->get('event', 'multidkppools', array($event));
			if(count($arrPools) === 0){
			     //No Itempool Found
			    $message = array('title' => $this->user->lang('save_nosuc'), 'text' => $this->user->lang('event_pool_connection_missing'), 'color' => 'red');
			    $this->display($message);
			    return;
			}
			$arrItempools = $this->pdh->get('multidkp', 'itempool_ids', array($arrPools[0]));
			$item['itempool_id'] = $arrItempools[0];
		}

		if($this->in->get('selected_ids','','hash')) {
			$retu = $this->pdh->put('item', 'update_item', array($this->in->get('selected_ids','','hash'), $item['name'], $item['buyers'], $item['raid_id'], $item['item_id'], $item['value'], $item['itempool_id'], $item['date']));
		} else {
			$retu = $this->pdh->put('item', 'add_item', array($item['name'], $item['buyers'], $item['raid_id'], $item['item_id'], $item['value'], $item['itempool_id'], $item['date']));
		}
		if($retu) {
			$message = array('title' => $this->user->lang('save_suc'), 'text' => $item['name'], 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $item['name'], 'color' => 'red');
		}
		$this->display($message);
	}

	public function bulk_update(){
		$strSelectedIDs = $this->in->get('selected_ids');
		$arrSelected = explode('|', $strSelectedIDs);
		$arrBulk = $this->in->getArray('bulk', 'int');

		//Get new item information
		$arrNewValues['buyer'] = $this->in->get('buyer', 0);
		$arrNewValues['name'] = $this->in->get('name','');
		$arrNewValues['value'] = $this->in->get('value',0.0);
		$arrNewValues['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$arrNewValues['raid_id'] = $this->in->get('raid_id',0);
		$arrNewValues['item_id'] = $this->in->get('item_id','');
		$arrNewValues['itempool_id'] = $this->in->get('itempool_id',0);

		$arrCheckboxes = array('name', 'item_id', 'raid_id', 'date', 'value', 'itempool_id', 'buyer');
		//Für jedes Item
		$messages = array();
		foreach($arrSelected as $itemID){

			//Hole alte daten
			$item = array();
			$item['buyer'] = $this->pdh->get('item', 'buyer', array($itemID));
			$item['name'] = $this->pdh->get('item', 'name', array($itemID));
			$item['value'] = $this->pdh->get('item', 'value', array($itemID));
			$item['date'] = $this->pdh->get('item', 'date', array($itemID));
			$item['raid_id'] = $this->pdh->get('item', 'raid_id', array($itemID));
			$item['item_id'] = $this->pdh->get('item', 'game_itemid', array($itemID));
			$item['itempool_id'] = $intOldItempoolID = $this->pdh->get('item', 'itempool_id', array($itemID));

			foreach($arrBulk as $key => $val){
				if(!in_array($key, $arrCheckboxes) || !$val) continue;

				$item[$key] = $arrNewValues[$key];
			}

			if($this->config->get('dkp_easymode')){
				if($intOldItempoolID != $item['raid_id']){
					$event = $this->pdh->get('raid', 'event', array($item['raid_id']));
					$arrPools = $this->pdh->get('event', 'multidkppools', array($event));
					$arrItempools = $this->pdh->get('multidkp', 'itempool_ids', array($arrPools[0]));
					$item['itempool_id'] = $arrItempools[0];
				}
			}

			$retu = $this->pdh->put('item', 'update_item', array($itemID, $item['name'], array($item['buyer']), $item['raid_id'], $item['item_id'], $item['value'], $item['itempool_id'], $item['date'], true));

			if(!$retu){
				$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $item['name'], 'color' => 'red');
			}

		}
		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('bulkedit'), 'color' => 'green');
		$this->pdh->process_hook_queue();
		$this->display($messages);
	}

	public function delete() {
		$ids = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $s_id) {
					$new_ids = $this->pdh->get('item', 'ids_of_group_key', array($this->pdh->get('item', 'group_key', array($s_id))));
					$ids = array_merge($ids, $new_ids);
			}
		} else {
			$ids = $this->pdh->get('item', 'ids_of_group_key', array($this->in->get('selected_ids','','hash')));
		}

		$retu = array();
		foreach($ids as $id) {
			$retu[$id] = $this->pdh->put('item', 'delete_item', array($id));
		}
		foreach($retu as $id => $suc) {
			if($suc) {
				$pos[] = stripslashes($this->pdh->get('item', 'name', array($id)));
			} else {
				$neg[] = stripslashes($this->pdh->get('item', 'name', array($id)));
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
		$raids = array();
		$raidids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
		foreach($raidids as $id)
		{
			$raids[$id] = '#ID:'.$id.' - '.$this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($id)))).' '.$this->time->user_date($this->pdh->get('raid', 'date', array($id)));
		}

		//fetch itempools for select
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));

		if($message){
			$this->core->messages($message);
			$item = $this->get_post(true);
		}
		if($this->in->exists('i')){
			$grp_key = $this->pdh->get('item', 'group_key', array($this->in->get('i',0)));
			$ids = $this->pdh->get('item', 'ids_of_group_key', array($grp_key));
			foreach($ids as $id)
			{
				$item['buyers'][] = $this->pdh->get('item', 'buyer', array($id));
			}
			$item['name'] = $this->pdh->get('item', 'name', array($id));
			$item['value'] = $this->pdh->get('item', 'value', array($id));
			$item['date'] = $this->pdh->get('item', 'date', array($id));
			$item['raid_id'] = $this->pdh->get('item', 'raid_id', array($id));
			$item['item_id'] = $this->pdh->get('item', 'game_itemid', array($id));
			$item['itempool_id'] = $this->pdh->get('item', 'itempool_id', array($id));

			//Add additional members
			if (count($item['buyers']) > 0){
				$arrIDList = array_keys($members);
				$blnResort = false;
				foreach($item['buyers'] as $member_id){
					if (!isset($members[$member_id])) {
						$arrIDList[] = $member_id;
						$blnResort = true;
					}
				}
				if ($blnResort) $members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($arrIDList, 'member', 'name', 'asc')));
			}
		} else {
			$item['date'] = $this->time->time;
		}

		$arrAutocomplete = array();
		foreach($this->pdh->get('item', 'id_list') as $intItemID){
			$arrAutocomplete[] = array(
				'label'		=> 	$this->pdh->get('item', 'name', array($intItemID)).', '.$this->pdh->geth('item', 'date', array($intItemID)).(($this->config->get('enable_points')) ? ', '.runden($this->pdh->get('item', 'value', array($intItemID))).' '.$this->config->get('dkp_name') : '').' (#'.$intItemID.')',
				'ivalue'		=>  runden($this->pdh->get('item', 'value', array($intItemID))),
				'game_id'	=>	$this->pdh->get('item', 'game_itemid', array($intItemID)),
				'iname'		=>	$this->pdh->get('item', 'name', array($intItemID)),
				'itempool'	=>	$this->pdh->get('item', 'itempool_id', array($intItemID)),
			);
		}

		$this->jquery->AutocompleteMultiple("name", $arrAutocomplete, '
			$("#item_value").val(ui.item.ivalue);
			$("#item_id").val(ui.item.game_id);
			$("#name").val(ui.item.iname);
			$("#itempool_id").val(ui.item.itempool);
			event.preventDefault();
		');

		$item_names = $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list')));

		if(!isset($item['name'])) $item['name'] = '';
		$this->confirm_delete($this->user->lang('confirm_delete_item')."<br />".$item['name'], '', true);
		$this->tpl->assign_vars(array(
			'GRP_KEY'		=> (isset($grp_key) && !$copy) ? $grp_key : '',
			'NAME'			=> $item['name'],
			'RAID'			=> (new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($item['raid_id'])) ? $item['raid_id'] : ''))))->output(),
			'BUYERS'		=> (new hmultiselect('buyers', array('options' => $members, 'value' => ((isset($item['buyers'])) ? $item['buyers'] : ''), 'width' => 350, 'filter' => true)))->output(),
			'DATE'			=> (new hdatepicker('date', array('value' => $this->time->user_date($item['date'], true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
			'VALUE'			=> (isset($item['value'])) ? $item['value'] : '',
			'ITEM_ID'		=> (isset($item['item_id'])) ? $item['item_id'] : '',
			'ITEMPOOLS'		=> (new hdropdown('itempool_id', array('options' => $itempools, 'value' => ((isset($item['itempool_id'])) ? $item['itempool_id'] : ''))))->output(),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manitems_title'),
			'template_file'		=> 'admin/manage_items_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manitems_title'), 'url'=>$this->root_path.'admin/manage_items.php'.$this->SID],
				['title'=>(($item['name'] == '')?$this->user->lang('add_item'):$item['name']), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display($messages=false) {
		if($messages && is_array($messages) && count($messages)){
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		//init infotooltip
		infotooltip_js();

		$view_list = $this->pdh->aget('item', 'group_key', 0, array($this->pdh->get('item', 'id_list', array())));
		$view_list = array_flip($view_list);
		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_items', 'hptt_admin_manage_items_itemlist');
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_items.php', '%link_url_suffix%' => '&amp;upd=true', '%raid_link_url%' => 'manage_raids.php', '%raid_link_url_suffix%' => '&amp;upd=true', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');

		$item_count = count($view_list);

		$this->confirm_delete($this->user->lang('confirm_delete_items'));

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
			'ITEM_LIST' 		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_rlimit'], false),
			'PAGINATION' 		=> generate_pagination('manage_items.php'.$sort_suffix, $item_count, $this->user->data['user_rlimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'ITEM_COUNT' 		=> $item_count,
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_items').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
			'BUTTON_MENU'		=> $this->core->build_dropdown_menu($this->user->lang('selected_items').'...', $arrMenuItems, '', 'manage_members_menu', array("input[name=\"selected_ids[]\"]")),

		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manitems_title'),
			'template_file'		=> 'admin/manage_items.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manitems_title'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display_bulkedit($messages=false) {
		$arrItems = $this->in->getArray('selected_ids', 'int');
		if(count($arrItems) === 0) $this->display();

		//fetch members for select
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));

		//fetch raids for select
		$raids = array();
		$raidids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
		foreach($raidids as $id)
		{
			$raids[$id] = '#ID:'.$id.' - '.$this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($id)))).' '.$this->time->user_date($this->pdh->get('raid', 'date', array($id)));
		}

		//fetch itempools for select
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));

		if($message){
			$this->core->messages($message);
			$item = $this->get_post(true);
			$item['buyer'] = $this->in->get('buyer', 0);
		}

		$item['date'] = $this->time->time;

		$arrAutocomplete = array();
		foreach($this->pdh->get('item', 'id_list') as $intItemID){
			$arrAutocomplete[] = array(
					'label'		=> 	$this->pdh->get('item', 'name', array($intItemID)).', '.$this->pdh->geth('item', 'date', array($intItemID)).(($this->config->get('enable_points')) ? ', '.runden($this->pdh->get('item', 'value', array($intItemID))).' '.$this->config->get('dkp_name') : '').' (#'.$intItemID.')',
					'ivalue'		=>  runden($this->pdh->get('item', 'value', array($intItemID))),
					'game_id'	=>	$this->pdh->get('item', 'game_itemid', array($intItemID)),
					'iname'		=>	$this->pdh->get('item', 'name', array($intItemID)),
					'itempool'	=>	$this->pdh->get('item', 'itempool_id', array($intItemID)),
			);
		}

		$this->jquery->AutocompleteMultiple("name", $arrAutocomplete, '
			$("#item_value").val(ui.item.ivalue);
			$("#item_id").val(ui.item.game_id);
			$("#name").val(ui.item.iname);
			$("#itempool_id").val(ui.item.itempool);
			event.preventDefault();
		');

		$item_names = $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list')));

		$this->tpl->assign_vars(array(
				'GRP_KEY'		=> (isset($grp_key) && !$copy) ? $grp_key : '',
				'NAME'			=> (isset($item['name'])) ? $item['name'] : '',
				'RAID'			=> (new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($item['raid_id'])) ? $item['raid_id'] : ''))))->output(),
				'BUYERS'		=> (new hsingleselect('buyer', array('options' => $members, 'value' => ((isset($item['buyer'])) ? $item['buyer'] : ''), 'width' => 350, 'filter' => true)))->output(),
				'DATE'			=> (new hdatepicker('date', array('value' => $this->time->user_date($item['date'], true, false, false, function_exists('date_create_from_format')), 'timepicker' => true)))->output(),
				'VALUE'			=> (isset($item['value'])) ? $item['value'] : '',
				'ITEM_ID'		=> (isset($item['item_id'])) ? $item['item_id'] : '',
				'ITEMPOOLS'		=> (new hdropdown('itempool_id', array('options' => $itempools, 'value' => ((isset($item['itempool_id'])) ? $item['itempool_id'] : ''))))->output(),
				'BULK_ITEMS'	=> implode('|', $arrItems),
		));

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('manitems_title').' - '.$this->user->lang('bulkedit'),
				'template_file'		=> 'admin/manage_items_bulkedit.html',
				'page_path'			=> [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manitems_title'), 'url'=>$this->root_path.'admin/manage_items.php'.$this->SID],
					['title'=>$this->user->lang('bulkedit'), 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	private function get_post($norefresh=false) {
		$item['name'] = $this->in->get('name','');
		if(!$item['name']){
		    $missing[] = $this->user->lang('name');
		}
		
		foreach($this->in->getArray('buyers','int') as $buyer){
			$item['buyers'][] = $buyer;
		}
		if(!$item['buyers']){
			$missing[] = $this->user->lang('buyers');
		}
		$item['raid_id'] = $this->in->get('raid_id', 0);
		if(!$item['raid_id'] && $this->config->get('dkp_easymode')){
		    $missing[] = $this->user->lang('raid');
		}
		$item['itempool_id'] = $this->in->get('itempool_id',0);
		if(!$item['itempool_id'] && !$this->config->get('dkp_easymode')){
			$missing[] = $this->user->lang('itempool');
		}
		if(!empty($missing) AND !$norefresh){
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		$item['value'] = $this->in->get('value',0.0);
		$item['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$item['item_id'] = $this->in->get('item_id','');
		return $item;
	}
}
registry::register('ManageItems');
