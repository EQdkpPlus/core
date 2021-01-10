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

class ManageRaids extends page_generic {

	public function __construct(){
		$handler = array(
			'bulk_upd'	=> array('process' => 'bulk_update', 'csrf'=> true, 'check' => 'a_raid_upd'),
			'raidvalue' => array('process' => 'ajax_raidvalue', 'check' => false),
			'defitempool' => array('process' => 'ajax_defitempool', 'check' => false),
			'save' => array('process' => 'save', 'check' => 'a_raid_add', 'csrf'=>true),
			'itemadj_del' => array('process' => 'update', 'check' => 'a_raid_del', 'csrf'=>true),
			'copy'		=> array('process' => 'copy', 'check' => 'a_raid_add'),
			'refresh'	=> array('process' => 'refresh', 'check' => 'a_raid_add', 'csrf' => true),
			'upd'		=> array('process' => 'update', 'csrf'=>false),
			'bulk'		=> array('process' => 'display_bulkedit', 'csrf'=>false, 'check' => 'a_raid_upd'),
		);
		parent::__construct('a_raid_', $handler, array('raid', 'event_name'), null, 'selected_ids[]', 'r');
		$this->process();

	}

	public function ajax_defitempool(){
		header('content-type: text/html; charset=UTF-8');

		$event_id = $this->in->get('event', 0);
		$defItempool = $this->pdh->get("event", "def_itempool", array($event_id));
		echo $defItempool;
		die();
	}

	public function ajax_raidvalue(){
		header('content-type: text/html; charset=UTF-8');

		$event_id = $this->in->get('event', 0);
		$event_value = $this->pdh->geth("event", "value", array($event_id));
		echo runden($event_value);
		die();
	}

	public function copy(){
		$this->core->message($this->user->lang('copy_info'), $this->user->lang('copy'));
		$this->update(false, false, true);
	}

	public function bulk_update(){
		$strSelectedIDs = $this->in->get('selected_ids');
		$arrSelected = explode('|', $strSelectedIDs);
		$arrBulk = $this->in->getArray('bulk', 'int');

		$arrNewValues['note'] = $this->in->get('rnote','');
		$arrNewValues['additonal_data'] = $this->in->get('additional_data','');
		$arrNewValues['event'] = $this->in->get('event',0);
		$arrNewValues['value'] = $this->in->get('value',0.0);
		$arrNewValues['attendees'] = array_unique($this->in->getArray('raid_attendees','int'));
		$arrNewValues['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$arrNewValues['itempool'] = $this->in->get('itempool_id',0);

		$arrCheckboxes = array('date', 'note', 'event', 'value', 'attendees');

		//Für jedes Item
		$messages = array();
		foreach($arrSelected as $raidID){
			//Hole alte daten
			$raid['note'] 			= $this->pdh->get('raid', 'note', array($raidID));
			$raid['additonal_data'] = $this->pdh->get('raid', 'additional_data', array($raidID));
			$raid['value'] 			= $this->pdh->get('raid', 'value', array($raidID));
			$raid['date'] 			= $this->pdh->get('raid', 'date', array($raidID));
			$raid['event'] 			= $intOldEvent = $this->pdh->get('raid', 'event', array($raidID));
			$raid['attendees'] 		= $this->pdh->get('raid', 'raid_attendees', array($raidID, false));

			foreach($arrBulk as $key => $val){
				if(!in_array($key, $arrCheckboxes) && $key != "itempool" || !$val) continue;

				if($key == 'itempool'){
					//Get Items of Raid

					//Change Itempool of Items
					$arrItemsOfRaid = $this->pdh->get('item', 'itemsofraid', array($raidID));
					foreach($arrItemsOfRaid as $intItemID){
						$this->pdh->put('item', 'update_itempool', array($intItemID, $arrNewValues['itempool']));
					}

				}

				$raid[$key] = $arrNewValues[$key];
				if($key == 'note'){
					$raid['additonal_data'] = $arrNewValues['additonal_data'];
				}
			}

			if($this->config->get('dkp_easymode') && $raid['event'] != $intOldEvent){
				$itemPool = $this->pdh->get('event', 'def_itempool', array($raid['event']));
				if(!$itemPool){
					$arrItempools = $this->pdh->get('event', 'itempools', array($raid['event']));
					$itemPool = $arrItempools[0];
				}

				//Change Itempool of Items
				$arrItemsOfRaid = $this->pdh->get('item', 'itemsofraid', array($raidID));
				foreach($arrItemsOfRaid as $intItemID){
					$this->pdh->put('item', 'update_itempool', array($intItemID, $itemPool));
				}
			}

			//update_raid($raid_id, $raid_date, $raid_attendees, $event_id, $raid_note, $raid_value, $additional_data='')
			$retu = $this->pdh->put('raid', 'update_raid', array($raidID, $raid['date'], $raid['attendees'], $raid['event'], $raid['note'], $raid['value'], $raid['additonal_data']));

			if(!$retu){
				$messages[] = array('title' => $this->user->lang('save_nosuc'), 'text' => $raid['name'], 'color' => 'red');
			}

		}
		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('bulkedit'), 'color' => 'green');
		$this->pdh->process_hook_queue();
		$this->display($messages);
	}



	public function delete() {
		$ids = $pos = $neg = $messages = array();

		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $s_id) {
					$ids[] = $s_id;
			}
		} elseif ($this->url_id > 0) {
			$ids[] = $this->url_id;
		}


		if (count($ids)){
			foreach ($ids as $raidid){
				//delete everything connected to the raid
				//adjustments first
				$adj_ids = $this->pdh->get('adjustment', 'adjsofraid', array($raidid));
				$adj_del = array(true);
				foreach($adj_ids as $id) {
					$adj_del[] = $this->pdh->put('adjustment', 'delete_adjustment', array($id));
				}
				//raid itself now
				$raid_del = $this->pdh->put('raid', 'delete_raid', array($raidid));
				if($raid_del) {
					$pos[] = $this->user->lang('raid').' '.$raidid;
				} else {
					$neg[] = $this->user->lang('raid').' '.$raidid;
				}
				if(in_array(false, $adj_del)) {
					$messages[] = array('text' => $this->user->lang('adjustments').' '.$this->user->lang('raid').' '.$raidid, 'title' => $this->user->lang('del_nosuc'), 'color' => 'red');
				}
			}
		}

		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
		}
		if(!empty($neg)) {
			$messages[] = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $neg), 'color' => 'red');
		}

		if($this->in->get('simple_head') != ""){
			$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
		}

		$this->display($messages);
	}

	public function save() {
		$data = $this->get_post();
		
		if(!$data['raid']['id']) {
			$raid_upd = $this->pdh->put('raid', 'add_raid', array($data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value'], $data['raid']['additonal_data']));
			$data['raid']['id'] = ($raid_upd) ? $raid_upd : false;

			if($raid_upd && $data['raid']['caleventid'] > 0){
				$this->pdh->put('calendar_events', 'raid_transformed', array($data['raid']['caleventid'], $data['raid']['id']));
			}
		} else {
			$raid_upd = $this->pdh->put('raid', 'update_raid', array($data['raid']['id'], $data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value'], $data['raid']['additonal_data'], $data['raid']['connected_raids']));
		}
		if($raid_upd) {
			$adj_upd = array(true);
			if(!empty($data['adjs']) && is_array($data['adjs'])) {
				foreach($data['adjs'] as $adj) {
					if($adj['group_key'] == 'new' OR empty($adj['group_key'])) {
						$adj_upd[] = $this->pdh->put('adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], $adj['members'], $adj['event'], $data['raid']['id'], $data['raid']['date']));
					} else {
						$adj_upd[] = $this->pdh->put('adjustment', 'update_adjustment', array($adj['group_key'], $adj['value'], $adj['reason'], $adj['members'], $adj['event'], $data['raid']['id'], $data['raid']['date']));
					}
				}
			}
			$item_upd = array(true);
			if(!empty($data['items']) && is_array($data['items'])) {
				$intEventID = $data['raid']['event'];
				$itemPool = $this->pdh->get('event', 'def_itempool', array($intEventID));
				if(!$itemPool){
					$arrItempools = $this->pdh->get('event', 'itempools', array($intEventID));
					$itemPool = $arrItempools[0];
				}

				foreach($data['items'] as $ik => $item) {
					if($this->config->get('dkp_easymode')){
						$item['itempool_id'] = $itemPool;
					}

					if($item['group_key'] == 'new' OR empty($item['group_key'])) {
						$intAmount = (int)$item['amount'];
						if($intAmount == 0 && $data['raid']['id']) $intAmount = 1;

						if($intAmount > 0){
							for($i=0; $i<$intAmount; $i++){
								$item_upd[] = $this->pdh->put('item', 'add_item', array($item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']+$ik));
							}
						}

					} else {
						$item_upd[] = $this->pdh->put('item', 'update_item', array($item['group_key'], $item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']+$ik));
					}
				}
			}
			
			if(!$data['raid']['id'] && $this->hooks->isRegistered('manageraids_raid_added')){
				$this->hooks->process('manageraids_raid_added', array('id' => $raid_upd));
			} elseif($this->hooks->isRegistered('manageraids_raid_updated')){
				$this->hooks->process('manageraids_raid_updated', array('id' => $raid_upd));
			}
			

			if(in_array(false, $adj_upd)) {
				$messages[] = array('text' => $this->user->lang('adjustments'), 'title' => $this->user->lang('save_nosuc'), 'color' => 'red');
			} else {
				$messages[] = array('text' => $this->user->lang('adjustments'), 'title' => $this->user->lang('save_suc'), 'color' => 'green');
			}
			if(in_array(false, $item_upd)) {
				$messages[] = array('text' => $this->user->lang('items'), 'title' => $this->user->lang('save_nosuc'), 'color' => 'red');
			} else {
				$messages[] = array('text' => $this->user->lang('items'), 'title' => $this->user->lang('save_suc'), 'color' => 'green');
			}

			$messages[] = array('text' => $this->user->lang('raids'), 'title' => $this->user->lang('save_suc'), 'color' => 'green');
		} else {
			$messages[] = array('text' => $this->user->lang('raids'), 'title' => $this->user->lang('save_nosuc'), 'color' => 'red');
		}

		if($this->in->get('simple_head') != ""){
			$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
		}
		$this->display($messages);
	}

	public function refresh(){
		$this->update(false, true);
	}

	public function update($message=false, $force_refresh=false, $copy=false) {
		if($message) {
			$this->core->messages($message);
		}

		$data = array();
		if($force_refresh){
			$data = $this->get_post(true);
			$this->pdh->process_hook_queue();
		}

		//fetch members
		$members_active = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(true,true,true)), 'member', 'name', 'asc')));
		$members_hidden = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_hidden', array()), 'member', 'name', 'asc')));
		$members_special = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_special', array()), 'member', 'name', 'asc')));
		$members_inactive = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_inactive', array()), 'member', 'name', 'asc')));

		$members = array(
				$this->user->lang('attendees') => array(),
				$this->user->lang('active') => $members_active,
				$this->user->lang('inactive') => $members_inactive,
				$this->user->lang('hidden') => $members_hidden,
				$this->user->lang('core_sett_f_special_members') => $members_special,
		);

		//fetch events
		$events = $eventsOrig = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		asort($events);

		if($this->config->get('dkp_easymode')){
			foreach($events as $eventID => $strEventname){
				$arrPools = $this->pdh->get('multidkp', 'mdkpids4eventid', array($eventID));
				$strPoolname = $this->pdh->get('multidkp', 'name', array($arrPools[0]));
				$tmpEvents[$strPoolname][$eventID] = $strEventname;
			}
			$events = $tmpEvents;
		}

		//Event Itempool Mapping
		$arrEventItempoolMapping = array();
		foreach($this->pdh->get('event', 'id_list') as $eventID){
			$arrEventItempools = $this->pdh->get('event', 'itempools', array($eventID));
			$arrEventItempoolMapping[$eventID] = (isset($arrEventItempools[0])) ? $arrEventItempools[0] : 0;
		}

		//fetch itempools
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));
		asort($itempools);

		//fetch Raids
		$raidlist = $this->pdh->get('raid', 'id_list');
		$raidlist = $this->pdh->sort($raidlist, 'raid', 'date', 'desc');
		$raids[] = '';
		foreach ($raidlist as $key => $row){
			$raids[$row] = $this->time->user_date($this->pdh->get('raid', 'date', array($row))) . ' - ' . stripslashes($this->pdh->get('raid', 'event_name', array($row)));
		}

		//fetch notes
		$notes = $this->pdh->aget('raid', 'note', 0, array($this->pdh->get('raid', 'id_list')));
		asort($notes);
		$this->jquery->Autocomplete('note', array_unique($notes));


		//Autocompletes
		$adjustment_reasons = $this->pdh->aget('adjustment', 'reason', 0, array($this->pdh->get('adjustment', 'id_list')));
		$item_names = $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list')));
		if($this->hooks->isRegistered('admin_manage_raids_items_autocomplete')){
			$item_names = $this->hooks->process('admin_manage_raids_items_autocomplete', array('item_names' => $item_names), true);
		}

		$raid = array('id' => $this->url_id, 'date' => $this->time->time, 'note' => '', 'event' => 0, 'value' => 0.00, 'attendees' => array(), 'connected_raids' => array());
		
		$membersForAdjsAndItems = $members;
		unset($members[$this->user->lang('attendees')]);
		
		if($raid['id'])
		{ //we're updating a raid
			//fetch raid-data
			$raid = $this->get_raiddata($raid['id'], true);
			//fetch adjs
			$adjs = $this->get_adjsofraid($raid['id']);
			//fetch items
			$items = $this->get_itemsofraid($raid['id']);
			
			$arrAttendees = $raid['attendees'];
			foreach($arrAttendees as $val){
				$membersForAdjsAndItems[$this->user->lang('attendees')][$val] = $this->pdh->get('member', 'name', array($val));
			}
			//Now remove the remaining chars 
			foreach($membersForAdjsAndItems as $group => $arrMembers){
				if($group === $this->user->lang('attendees')) continue;
				
				foreach($arrMembers as $key => $val){
					
					if(isset($membersForAdjsAndItems[$this->user->lang('attendees')][$key])){
						unset($membersForAdjsAndItems[$group][$key]);
					}
				}
 			}
		}


		//If we get a draft
		if ($this->in->get('draft', 0) > 0) {
			$raid = $this->get_raiddata($this->in->get('draft', 0), true);
			$raid['id']	 = 0;
			$raid['date']	 = $this->time->time;
		}

		//we're refreshing the view
		if($force_refresh) {
			if(!empty($data['raid'])) $raid = $data['raid'];
			if(!empty($data['adjs'])) $adjs = $data['adjs'];
			if(!empty($data['items'])) $items = $data['items'];
		}
		$intAdjKey = 0;
		if(isset($adjs) AND is_array($adjs)) {
			foreach($adjs as $key => $adj) {
				$this->tpl->assign_block_vars('adjs', array(
					'KEY'		=> $key,
					'GK'		=> ($copy) ? 'new' : $adj['group_key'],
						'MEMBER'	=> (new hmultiselect('adjs['.$key.'][members]', array('options' => $membersForAdjsAndItems, 'value' => $adj['members'], 'width' => 250, 'filter' => true, 'id'=>'adjs_'.$key.'_members', 'class' => 'input adj_members')))->output(),
					'REASON'	=> sanitize($adj['reason']),
					'EVENT'		=> (new hdropdown('adjs['.$key.'][event]', array('options' => $events, 'value' => $adj['event'], 'id' => 'event_'.$key, 'class' => 'input adj_event')))->output(),
					'VALUE'		=> $adj['value'])
				);
				$adjs_ids[] = 'adjs_'.$key;
				if($key > $intAdjKey) $intAdjKey = $key;
			}
			if(isset($adjs_ids) AND is_array($adjs_ids)){
				$this->jquery->Autocomplete($adjs_ids, array_unique($adjustment_reasons));
			}
		}
		$intItemKey = 0;
		if(isset($items) AND is_array($items)) {
			foreach($items as $key => $item) {
				$this->tpl->assign_block_vars('items', array(
					'KEY'		=> $key,
					'GK'		=> ($copy) ? 'new' : $item['group_key'],
					'NAME'		=> stripslashes($item['name']),
					'ITEMID'	=> $item['item_id'],
						'MEMBER'	=> (new hmultiselect('items['.$key.'][members]', array('options' => $membersForAdjsAndItems, 'value' => $item['members'], 'width' => 250, 'filter' => true, 'id'=>'items_'.$key.'_members', 'class' => 'input item_members')))->output(),
					'VALUE'		=> $item['value'],
					'ITEMPOOL'	=> (new hdropdown('items['.$key.'][itempool_id]', array('options' => $itempools, 'value' => $item['itempool_id'], 'id' => 'itempool_id_'.$key, 'class' => 'input item_itempool')))->output(),
				));
				$item_ids[] = 'items_'.$key;
				if($key > $intItemKey) $intItemKey = $key;
			}
			if (isset($item_ids) AND is_array($item_ids)){
				$this->jquery->Autocomplete($item_ids, array_unique($item_names));
			}
		}

		$blnRaidUpdate		= ($raid['id'] AND $raid['id'] != 'new' && !$copy);
		$strRaidEvent		= $this->pdh->get('event', 'name', array($raid['event']));
		$strRaidUserDate	= $this->time->user_date($raid['date']);

		if($raid['id'] AND $raid['id'] != 'new') $this->confirm_delete($this->user->lang('del_raid_with_itemadj')."<br />".$strRaidUserDate." ".$events[$raid['event']].": ".addslashes($raid['note']));

		
		$arrConnected = array();
		if($raid['id']){
			$arrAllRaidsForEvent = $this->pdh->get('raid', 'raidids4eventid', array($raid['event']));
			$arrAllRaidsForEvent = $this->pdh->sort($arrAllRaidsForEvent, 'raid', 'date', 'desc');
			foreach($arrAllRaidsForEvent as $connRaidId){
				if($connRaidId == $raid['id']) continue;
				
				$date = $this->pdh->get('raid', 'date', array($connRaidId));
				if($date < ($raid['date']-7*3610*24)) continue;
				if($date > ($raid['date']+7*3610*24)) continue;
				
				$arrConnected[$connRaidId] = $this->time->user_date($date) . ' - ' . stripslashes($this->pdh->get('raid', 'event_name', array($connRaidId)));
			}
		}

		$arrEventKeys = array_keys($eventsOrig);

		$this->tpl->assign_vars(array(
			'DATE'				=> (new hdatepicker('date', array('value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('date', '') : $this->time->user_date($raid['date'], true, false, false, function_exists('date_create_from_format'))), 'timepicker' => true)))->output(),
			'NOTE'				=> stripslashes((($this->in->get('dataimport', '') == 'true') ? $this->in->get('rnote', '') : $raid['note'])),
			'EVENT'				=> (new hdropdown('event', array('options' => $events, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('event', 0) : $raid['event']), 'js' => 'onchange="loadEventValue($(this).val())"')))->output(),
			'RAID_EVENT'		=> $strRaidEvent,
			'RAID_DATE'			=> $strRaidUserDate,
			'RAID_ID'			=> ($raid['id'] && !$copy) ? $raid['id'] : 0,
			'VALUE'				=> runden((($this->in->get('dataimport', '') == 'true') ? $this->in->get('value', 0) : $raid['value'])),
			'NEW_MEM_SEL'		=> (new hmultiselect('raid_attendees', array('options' => $members, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->getArray('attendees', 'int') : $raid['attendees']), 'width' => 400, 'filter' => true)))->output(),
			'RAID_DROPDOWN'		=> (new hdropdown('draft', array('options' => $raids, 'value' => $this->in->get('draft', 0), 'js' => 'onchange="window.location=\'manage_raids.php'.$this->SID.'&amp;upd=true&amp;draft=\'+this.value"')))->output(),
			'CONNECTED_RAIDS'	=> count($arrConnected) ? (new hmultiselect('connected', array('options' => $arrConnected, 'value' => $raid['connected_raids'], 'width' => 250, 'filter' => true)))->output() : '',
			'ADJ_KEY'			=> $intAdjKey+1,
				'MEMBER_DROPDOWN'	=> (new hmultiselect('adjs[KEY][members]', array('options' => $membersForAdjsAndItems, 'value' => '', 'width' => 250, 'filter' => true, 'id'=>'adjs_KEY_members', 'class' => 'input adj_members')))->output(),
				'MEMBER_ITEM_DROPDOWN'	=> (new hmultiselect('items[KEY][members]', array('options' => $membersForAdjsAndItems, 'value' => '', 'width' => 250, 'filter' => true, 'id'=>'items_KEY_members', 'class' => 'input item_members')))->output(),
			'EVENT_DROPDOWN'	=> (new hdropdown('adjs[KEY][event]', array('options' => $events, 'value' => $adj['event'], 'id' => 'event_KEY', 'class' => 'input adj_event')))->output(),
			'ADJ_REASON_AUTOCOMPLETE' => $this->jquery->Autocomplete('adjs_KEY', array_unique($adjustment_reasons)),
			'ITEM_KEY'			=> $intItemKey+1,
			'ITEMPOOL_DROPDOWN' => (new hdropdown('items[KEY][itempool_id]', array('options' => $itempools, 'value' => $item['itempool_id'], 'id' => 'itempool_id_KEY', 'class' => 'input item_itempool')))->output(),
			'ITEM_AUTOCOMPLETE' => $this->jquery->Autocomplete('item_KEY', array_unique($item_names)),
			'EVENT_ITEMPOOL_MAPPING' => json_encode($arrEventItempoolMapping),
			'FIRST_EVENT_ID'	=> (isset($arrEventKeys[0]) && strlen($arrEventKeys[0])) ? $arrEventKeys[0] : 0,
			'S_COPY'			=> ($copy),

			//language vars
			'L_RAID_SAVE'		=> ($blnRaidUpdate) ? $this->user->lang('update_raid') : $this->user->lang('add_raid'),
			//other needed vars
			'S_RAID_UPD'		=> $blnRaidUpdate,
			'S_EVENTVAL_ONLOAD' => ($raid['id'] == 'new' && !$force_refresh && $this->in->get('dataimport', '') != 'true') ? true : false,
			'S_CALDATAIMPORT'	=> ($this->in->get('dataimport', '') == 'true') ? $this->in->get('calevent_id', 0) : 0,
			'ADDITIONAL_INFOS_EDITOR' => (new hbbcodeeditor('additional_data', array('rows' => 10, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('additional_data') : $raid['additional_data']))))->output(),
			'ADDITIONAL_INFOS'	=> ((isset($raid['additional_data']) AND strlen($raid['additional_data'])) || (($this->in->get('dataimport', '') == 'true') && strlen($this->in->get('additional_data')))) ? 'true' : 'false',
		));

		$this->tpl->add_js("
	$('#r_add_mem').click(function() {
		r_add_mem_durl();
	});
	$('#r_add_event').click(function() {
		r_add_event_durl();
	});", 'docready');
		$this->jquery->dialog('r_add_mem_durl', $this->user->lang('add_member'), array('url' => $this->controller_path.'AddCharacter/'.$this->SID.'&adminmode=1', 'width' =>'640', 'height' => '520', 'onclosejs' => 'document.getElementById("refresh_button").click();'));
		$this->jquery->dialog('r_add_event_durl', $this->user->lang('add_event'), array('url' => 'manage_events.php'.$this->SID.'&upd=true&simple_head=true', 'width' =>'700', 'height' =>'550', 'onclosejs' => 'document.getElementById("refresh_button").click();'));
		$this->jquery->Collapse('#toggleAdjustments');
		$this->jquery->Collapse('#toggleItems');
		$this->jquery->Collapse('#toggleRaidInfos');

		$this->core->set_vars([
			'page_title'    => $this->user->lang('manraid_title'),
			'template_file' => 'admin/manage_raids_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manraid_title'), 'url'=>$this->root_path.'admin/manage_raids.php'.$this->SID],
				['title'=>(($blnRaidUpdate)?$strRaidEvent.', '.$strRaidUserDate:$this->user->lang('addraid_title')), 'url'=>' '],
			],
			'display'       => true
		]);
	}

	public function display_bulkedit($messages=false) {
		$arrItems = $this->in->getArray('selected_ids', 'int');
		if(count($arrItems) === 0) $this->display();


		if($messages) {
			$this->core->messages($messages);
		}

		$data = array();

		//fetch members
		$members_active = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(true,true,true)), 'member', 'name', 'asc')));
		$members_hidden = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_hidden', array()), 'member', 'name', 'asc')));
		$members_special = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_special', array()), 'member', 'name', 'asc')));
		$members_inactive = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list_inactive', array()), 'member', 'name', 'asc')));

		$members = array(
				$this->user->lang('active') => $members_active,
				$this->user->lang('inactive') => $members_inactive,
				$this->user->lang('hidden') => $members_hidden,
				$this->user->lang('core_sett_f_special_members') => $members_special,
		);

		//fetch events
		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		asort($events);

		//Event Itempool Mapping
		$arrEventItempoolMapping = array();
		foreach($this->pdh->get('event', 'id_list') as $eventID){
			$arrEventItempools = $this->pdh->get('event', 'itempools', array($eventID));
			$arrEventItempoolMapping[$eventID] = (isset($arrEventItempools[0])) ? $arrEventItempools[0] : 0;
		}

		//fetch itempools
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));
		asort($itempools);

		//fetch Raids
		$raidlist = $this->pdh->get('raid', 'id_list');
		$raidlist = $this->pdh->sort($raidlist, 'raid', 'date', 'desc');
		$raids[] = '';
		foreach ($raidlist as $key => $row){
			$raids[$row] = $this->time->user_date($this->pdh->get('raid', 'date', array($row))) . ' - ' . stripslashes($this->pdh->get('raid', 'event_name', array($row)));
		}

		//fetch notes
		$notes = $this->pdh->aget('raid', 'note', 0, array($this->pdh->get('raid', 'id_list')));
		asort($notes);
		$this->jquery->Autocomplete('note', array_unique($notes));

		$raid = array('id' => $this->url_id, 'date' => $this->time->time, 'note' => '', 'event' => 0, 'value' => 0.00, 'attendees' => array());

		$arrEventKeys = array_keys($events);
		$this->tpl->assign_vars(array(
				'DATE'				=> (new hdatepicker('date', array('value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('date', '') : $this->time->user_date($raid['date'], true, false, false, function_exists('date_create_from_format'))), 'timepicker' => true)))->output(),
				'NOTE'				=> stripslashes((($this->in->get('dataimport', '') == 'true') ? $this->in->get('rnote', '') : $raid['note'])),
				'EVENT'				=> (new hdropdown('event', array('options' => $events, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('event', 0) : $raid['event']), 'js' => 'onchange="loadEventValue($(this).val())"')))->output(),
				'RAID_EVENT'		=> $this->pdh->get('event', 'name', array($raid['event'])),
				'RAID_DATE'			=> $this->time->user_date($raid['date']),
				'RAID_ID'			=> ($raid['id'] && !$copy) ? $raid['id'] : 0,
				'ITEMPOOL_DD'		=> (new hdropdown('itempool_id', array('options' => $itempools)))->output(),
				'VALUE'				=> runden((($this->in->get('dataimport', '') == 'true') ? $this->in->get('value', 0) : $raid['value'])),
				'NEW_MEM_SEL'		=> (new hmultiselect('raid_attendees', array('options' => $members, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->getArray('attendees', 'int') : $raid['attendees']), 'width' => 400, 'filter' => true)))->output(),
				'RAID_DROPDOWN'		=> (new hdropdown('draft', array('options' => $raids, 'value' => $this->in->get('draft', 0), 'js' => 'onchange="window.location=\'manage_raids.php'.$this->SID.'&amp;upd=true&amp;draft=\'+this.value"')))->output(),
				//language vars
				'L_RAID_SAVE'		=> ($raid['id'] AND $raid['id'] != 'new' && !$copy) ? $this->user->lang('update_raid') : $this->user->lang('add_raid'),
				//other needed vars
				'S_RAID_UPD'		=> ($raid['id'] AND $raid['id'] != 'new' && !$copy) ? true : false,
				'S_EVENTVAL_ONLOAD' => ($raid['id'] == 'new' && $this->in->get('dataimport', '') != 'true') ? true : false,
				'S_CALDATAIMPORT'	=> ($this->in->get('dataimport', '') == 'true') ? $this->in->get('calevent_id', 0) : 0,
				'ADDITIONAL_INFOS_EDITOR' => (new hbbcodeeditor('additional_data', array('rows' => 10, 'value' => (($this->in->get('dataimport', '') == 'true') ? $this->in->get('additional_data') : $raid['additional_data']))))->output(),
				'ADDITIONAL_INFOS'	=> ((isset($raid['additional_data']) AND strlen($raid['additional_data'])) || (($this->in->get('dataimport', '') == 'true') && strlen($this->in->get('additional_data')))) ? 'true' : 'false',
				'BULK_ITEMS'		=> implode('|', $arrItems),
		));

		$this->core->set_vars([
				'page_title'    => $this->user->lang('manraid_title').' - '.$this->user->lang('bulkedit'),
				'template_file' => 'admin/manage_raids_bulkedit.html',
				'page_path'			=> [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('manraid_title'), 'url'=>$this->root_path.'admin/manage_raids.php'.$this->SID],
					['title'=>$this->user->lang('bulkedit'), 'url'=>' '],
				],
				'display'       => true
		]);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}

		$view_list = $this->pdh->get('raid', 'id_list', array());

		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_raids', 'hptt_admin_manage_raids_raidlist');

		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_raids.php', '%link_url_suffix%' => '&amp;upd=true'));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');

		//footer
		$raid_count = count($view_list);

		$this->confirm_delete($this->user->lang('confirm_delete'));

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
			'RAID_LIST' => $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_rlimit'], false),
			'PAGINATION' => generate_pagination('manage_raids.php'.$sort_suffix, $raid_count, $this->user->data['user_rlimit'], $this->in->get('start', 0)),
			'IMPORT_DKP' => ($this->pm->check('raidlogimport', PLUGIN_INSTALLED)) ? '<button onclick="window.location=\''.$this->root_path.'plugins/raidlogimport/admin/dkp.php'.$this->SID.'\'" type="button" class="mainoption"><i class="fa fa-upload"></i>'.$this->user->lang('raidlogimport_dkp').'</button>' : '',
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'RAID_COUNT' => $raid_count,
			'HPTT_ADMIN_LINK'	=> ($this->user->check_auth('a_tables_man', false)) ? '<a href="'.$this->server_path.'admin/manage_pagelayouts.php'.$this->SID.'&edit=true&layout='.$this->config->get('eqdkp_layout').'#page-'.md5('admin_manage_raids').'" title="'.$this->user->lang('edit_table').'"><i class="fa fa-pencil floatRight"></i></a>' : false,
			'BUTTON_MENU'=> $this->core->build_dropdown_menu($this->user->lang('selected_elements').'...', $arrMenuItems, '', 'manage_members_menu', array("input[name=\"selected_ids[]\"]")),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('manraid_title'),
			'template_file'		=> 'admin/manage_raids.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('manraid_title'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function get_raiddata($raid_id, $attendees=false) {
		$raid['id'] = $raid_id;
		$raid['date'] = $this->pdh->get('raid', 'date', array($raid_id));
		$raid['event'] = $this->pdh->get('raid', 'event', array($raid_id));
		$raid['note'] = $this->pdh->get('raid', 'note', array($raid_id));
		$raid['value'] = $this->pdh->get('raid', 'value', array($raid_id));
		$raid['additional_data'] = $this->pdh->get('raid', 'additional_data', array($raid_id));
		$raid['connected_raids'] = json_decode($this->pdh->get('raid', 'connected_attendance', array($raid_id)));
		if($attendees) {
			$raid['attendees'] = $this->pdh->get('raid', 'raid_attendees', array($raid_id, false));
		}
		return $raid;
	}

	private function get_adjsofraid($raid_id) {
		if(!$raid_id) {
			return false;
		}
		$adj_ids = $this->pdh->get('adjustment', 'adjsofraid', array($raid_id));
		$ak = 0;
		$adjs = array();
		foreach($adj_ids as $id) {
			$group_key[$ak] = $this->pdh->get('adjustment', 'group_key', array($id));
			$adjs[$ak]['member'] = $this->pdh->get('adjustment', 'member', array($id));
			$adjs[$ak]['reason'] = $this->pdh->get('adjustment', 'reason', array($id));
			$adjs[$ak]['event'] = $this->pdh->get('adjustment', 'event', array($id));
			$adjs[$ak]['value'] = $this->pdh->get('adjustment', 'value', array($id));
			$ak++;
		}
		$ret_adjs = array();
		$ret_group_key = array();
		foreach($adjs as $key => $adj) {
			$ak = array_search($group_key[$key], $ret_group_key);
			if($ak !== false) {
				$ret_adjs[$ak]['members'][] = $adj['member'];
			} else {
				$ret_adjs[$key] = $adj;
				unset($ret_adjs[$key]['member']);
				$ret_adjs[$key]['members'][] = $adj['member'];
				$ret_adjs[$key]['group_key'] = $group_key[$key];
				$ret_group_key[$key] = $group_key[$key];
			}
		}
		return $ret_adjs;
	}

	private function get_itemsofraid($raid_id) {
		$item_ids = $this->pdh->get('item', 'itemsofraid', array($raid_id));
		sort($item_ids, SORT_NUMERIC);
		$ik = 0;
		$items = array();
		foreach($item_ids as $id) {
			$group_key[$ik] = $this->pdh->get('item', 'group_key', array($id));
			$items[$ik]['name'] = $this->pdh->get('item', 'name', array($id));
			$items[$ik]['item_id'] = $this->pdh->get('item', 'game_itemid', array($id));
			$items[$ik]['value'] = $this->pdh->get('item', 'value', array($id));
			$items[$ik]['member'] = $this->pdh->get('item', 'buyer', array($id));
			$items[$ik]['itempool_id'] = $this->pdh->get('item', 'itempool_id', array($id));
			$ik++;
		}
		$ret_items = array();
		$ret_group_key = array();
		foreach($items as $key => $item) {
			$ik = array_search($group_key[$key], $ret_group_key);
			if($ik !== false) {
				$ret_items[$ik]['members'][] = $item['member'];
			} else {
				$ret_items[$key] = $item;
				unset($ret_items[$key]['member']);
				$ret_items[$key]['members'][] = $item['member'];
				$ret_items[$key]['group_key'] = $group_key[$key];
				$ret_group_key[$key] = $group_key[$key];
			}
		}
		return $ret_items;
	}

	private function get_post($refresh=false) {
		$data = array();
		$data['raid']['id'] = ($this->in->get('copy',0)) ? 0 : $this->url_id;
		$data['raid']['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$data['raid']['note'] = $this->in->get('rnote','');
		$data['raid']['additonal_data'] = $this->in->get('additional_data','');
		$data['raid']['event'] = $this->in->get('event',0);
		$data['raid']['caleventid'] = $this->in->get('caldata_import',0);
		$data['raid']['value'] = $this->in->get('value',0.0);

		$data['raid']['attendees'] = array_unique($this->in->getArray('raid_attendees','int'));
		$data['raid']['connected_raids'] = array_unique($this->in->getArray('connected','int'));

		if(empty($data['raid']['attendees'])) {
			$data['false'][] = 'raids_members';
		}
		if(is_array($this->in->getArray('adjs', 'int'))) {
			foreach($this->in->getArray('adjs', 'int') as $key => $adj) {
				if(!isset($adj['delete'])){
					if(!isset($adj['members'])) {
						$data['false'][] = 'adjustments_members';
					}
					$data['adjs'][$key]['group_key'] = $this->in->get('adjs:'.$key.':group_key','','hash');
					$data['adjs'][$key]['members'] = array_unique($this->in->getArray('adjs:'.$key.':members','int'));
					$data['adjs'][$key]['reason'] = $this->in->get('adjs:'.$key.':reason','');
					$data['adjs'][$key]['event'] = $this->in->get('adjs:'.$key.':event',0);
					$data['adjs'][$key]['value'] = $this->in->get('adjs:'.$key.':value',0.0);
				} else {
					$ids2del = $this->pdh->get('adjustment', 'ids_of_group_key', array($this->in->get('adjs:'.$key.':group_key','','hash')));
					foreach ($ids2del as $id) {
						$this->pdh->put('adjustment', 'delete_adjustment', array($id));
					}
				}
			}
		}

		if(is_array($this->in->getArray('items', 'int'))) {
			foreach($this->in->getArray('items', 'int') as $key => $item) {
				if(!isset($item['delete'])){
					if(!isset($item['members'])) {
						$data['false'][] = 'items_members';
					}
					if($this->in->get('items:'.$key.':name','') == "" && $this->in->get('items:'.$key.':itemid','') == ""){
						$data['false'][] = 'item';
					}

					$data['items'][$key]['group_key'] = $this->in->get('items:'.$key.':group_key','','hash');
					$data['items'][$key]['name'] = $this->in->get('items:'.$key.':name','');
					$data['items'][$key]['item_id'] = $this->in->get('items:'.$key.':itemid','');
					$data['items'][$key]['members'] = array_unique($this->in->getArray('items:'.$key.':members','int'));
					$data['items'][$key]['value'] = $this->in->get('items:'.$key.':value',0.0);
					$data['items'][$key]['itempool_id'] = $this->in->get('items:'.$key.':itempool_id',0);
					$data['items'][$key]['amount'] = $this->in->get('items:'.$key.':amount',0);
				} else {
					$ids2del = $this->pdh->get('item', 'ids_of_group_key', array($this->in->get('items:'.$key.':group_key','','hash')));
					foreach($ids2del as $id) {
						$this->pdh->put('item', 'delete_item', array($id));
					}
				}
			}
		}

		if(isset($data['false']) && !$refresh) {
			$missing = '';
			foreach($data['false'] as $miss) {
				$params = explode('_', $miss);
				$missing .= $this->user->lang($params[0]).': '.$this->user->lang($params[1]).'<br />';
			}
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => $missing, 'color' => 'red'), true);
		}

		return $data;
	}
}
registry::register('ManageRaids');
