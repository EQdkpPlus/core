<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2006
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class ManageRaids extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'html', 'pm');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_raid_add', 'csrf'=>true),
			'itemadj_del' => array('process' => 'update', 'check' => 'a_raid_del', 'csrf'=>true),
			'refresh' => array('process' => 'update', 'check' => 'a_raid_'),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
		);
		parent::__construct('a_raid_', $handler, false, false, false, 'r');
		$this->process();
	}

	public function delete() {
		//delete everything connected to the raid
		//adjustments first
		$adj_ids = $this->pdh->get('adjustment', 'adjsofraid', array($this->url_id));
		$adj_del = array(true);
		foreach($adj_ids as $id) {
			$adj_del[] = $this->pdh->put('adjustment', 'delete_adjustment', array($id));
		}
		//raid itself now
		$raid_del = $this->pdh->put('raid', 'delete_raid', array($this->url_id));
		if($raid_del) {
			$messages[] = array('text' => $this->user->lang('raid').' '.$this->url_id, 'title' => $this->user->lang('del_suc'), 'color' => 'green');
		} else {
			$messages[] = array('text' => $this->user->lang('raid').' '.$this->url_id, 'title' => $this->user->lang('del_nosuc'), 'color' => 'red');
		}
		if(in_array(false, $adj_del)) {
			$messages[] = array('text' => $this->user->lang('adjustments'), 'title' => $this->user->lang('del_nosuc'), 'color' => 'red');
		} else {
			$messages[] = array('text' => $this->user->lang('adjustments'), 'title' => $this->user->lang('del_suc'), 'color' => 'green');
		}
		$this->display($messages);
	}

	public function save() {
		$data = $this->get_post();
		if(!$data['raid']['id']) {
			$raid_upd = $this->pdh->put('raid', 'add_raid', array($data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value']));
			$data['raid']['id'] = ($raid_upd) ? $raid_upd : false;
		} else {
			$raid_upd = $this->pdh->put('raid', 'update_raid', array($data['raid']['id'], $data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value']));
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
				foreach($data['items'] as $ikey => $item) {
					if($item['group_key'] == 'new' OR empty($item['group_key'])) {
						$item_upd[] = $this->pdh->put('item', 'add_item', array($item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']+$ikey));
					} else {
						$item_upd[] = $this->pdh->put('item', 'update_item', array($item['group_key'], $item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']+$ikey));
					}
				}
			}
			$messages[] = array('text' => $this->user->lang('raids'), 'title' => $this->user->lang('save_suc'), 'color' => 'green');
		} else {
			$messages[] = array('text' => $this->user->lang('raids'), 'title' => $this->user->lang('save_nosuc'), 'color' => 'red');
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
		$this->display($messages);
	}

	public function update($message=false, $force_refresh=false) {
		if($message) {
			$this->core->messages($message);
		}
		
		$data = array();
		if($this->in->exists('refresh') OR $this->in->exists('itemadj_del') OR $force_refresh){
			$data = $this->get_post(true);
			$this->pdh->process_hook_queue();
		}

		//fetch members
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));

		//fetch events
		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		asort($events);

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
		if($raid['id'])
		{ //we're updating a raid
			//fetch raid-data
			$raid = $this->get_raiddata($raid['id'], true);
			//fetch adjs
			$adjs = $this->get_adjsofraid($raid['id']);
			//fetch items
			$items = $this->get_itemsofraid($raid['id']);
		}
		//If we get a draft
		if ($this->in->get('draft', 0) > 0) {
			$raid = $this->get_raiddata($this->in->get('draft', 0), true);
			$raid['id']	 = 0;
			$raid['date']	 = $this->time->time;
		}
		//we're refreshing the view
		if($this->in->exists('refresh') OR $this->in->exists('itemadj_del') OR $force_refresh) {
			if(!empty($data['raid'])) $raid = $data['raid'];
			if(!empty($data['adjs'])) $adjs = $data['adjs'];
			if(!empty($data['items'])) $items = $data['items'];
			if($this->in->get('refresh') == $this->user->lang('add_aadjustment')) {
				$adjs[] = array('group_key' => 'new', 'event' => $raid['event'], 'members' => array(), 'reason' => '', 'value' => 0);
			}
			if($this->in->get('refresh') == $this->user->lang('add_aitem')) {
				$items[] = array('group_key' => 'new', 'name' => '', 'item_id' => '', 'value' => 0, 'members' => array(), 'itempool_id' => 1);
			}
		}
		if(isset($adjs) AND is_array($adjs)) {
			foreach($adjs as $key => $adj) {
				$this->tpl->assign_block_vars('adjs', array(
					'KEY'		=> $key,
					'GK'		=> $adj['group_key'],
					'MEMBER'	=> $this->jquery->MultiSelect('adjs['.$key.'][members]', $members, $adj['members'], array('width' => 250, 'id'=>'adjs_'.$key.'_members', 'filter' => true)),
					'REASON'	=> sanitize($adj['reason']),
					'EVENT'		=> $this->html->DropDown('adjs['.$key.'][event]', $events, $adj['event'], '', '', 'input', 'event_'.$key),
					'VALUE'		=> $adj['value'])
				);
				$adjs_ids[] = 'adjs_'.$key;
			}
			if(isset($adjs_ids) AND is_array($adjs_ids)){
				//fetch adjustment-reasons
				$adjustment_reasons = $this->pdh->aget('adjustment', 'reason', 0, array($this->pdh->get('adjustment', 'id_list')));
				$this->jquery->Autocomplete($adjs_ids, array_unique($adjustment_reasons));
			}
		}
		if(isset($items) AND is_array($items)) {
			foreach($items as $key => $item) {
				$this->tpl->assign_block_vars('items', array(
					'KEY'		=> $key,
					'GK'		=> $item['group_key'],
					'NAME'		=> stripslashes($item['name']),
					'ITEMID'	=> $item['item_id'],
					'MEMBER'	=> $this->jquery->MultiSelect('items['.$key.'][members]', $members, $item['members'], array('width' => 250, 'id'=>'items_'.$key.'_members', 'filter' => true)),
					'VALUE'		=> $item['value'],
					'ITEMPOOL'	=> $this->html->DropDown('items['.$key.'][itempool_id]', $itempools, $item['itempool_id'], '', '', 'input', 'itempool_id_'.$key))
				);
				$item_ids[] = 'items_'.$key;
			}
			if (isset($item_ids) AND is_array($item_ids)){
				$item_names = $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list')));
				$this->jquery->Autocomplete($item_ids, array_unique($item_names));
			}
		}

		if($raid['id'] AND $raid['id'] != 'new') $this->confirm_delete($this->user->lang('del_raid_with_itemadj')."<br />".$this->time->user_date($raid['date'])." ".$events[$raid['event']].": ".addslashes($raid['note']));
		$this->tpl->assign_vars(array(
			'DATE'				=> $this->jquery->Calendar('date', (($this->in->get('dataimport', '') == 'true') ? $this->in->get('date', '') : $this->time->user_date($raid['date'], true, false, false, function_exists('date_create_from_format'))), '', array('timepicker' => true)),
			'NOTE'				=> stripslashes((($this->in->get('dataimport', '') == 'true') ? $this->in->get('rnote', '') : $raid['note'])),
			'EVENT'				=> $this->html->DropDown('event', $events, (($this->in->get('dataimport', '') == 'true') ? $this->in->get('event', 0) : $raid['event'])),
			'RAID_ID'			=> ($raid['id']) ? $raid['id'] : 0,
			'VALUE'				=> (($this->in->get('dataimport', '') == 'true') ? $this->in->get('value', 0) : $raid['value']),
			'NEW_MEM_SEL'		=> $this->jquery->MultiSelect('raid_attendees', $members, (($this->in->get('dataimport', '') == 'true') ? $this->in->getArray('attendees', 'int') : $raid['attendees']), array('width' => 400, 'filter' => true)),
			'RAID_DROPDOWN'		=> $this->html->DropDown('draft', $raids, $this->in->get('draft', 0), '', 'onchange="window.location=\'manage_raids.php'.$this->SID.'&amp;upd=true&amp;draft=\'+this.value"'),
			//language vars
			'L_RAID_SAVE'		=> ($raid['id'] AND $raid['id'] != 'new') ? $this->user->lang('update_raid') : $this->user->lang('add_raid'),
			//other needed vars
			'S_RAID_UPD'		=> ($raid['id'] AND $raid['id'] != 'new') ? true : false,
		));

		$this->tpl->add_js("
	$('#r_add_mem').click(function() {
		r_add_mem_durl();
	});
	$('#r_add_event').click(function() {
		r_add_event_durl();
	});", 'docready');
		$this->jquery->dialog('r_add_mem_durl', $this->user->lang('add_member'), array('url' => $this->root_path.'addcharacter.php'.$this->SID.'&adminmode=1', 'width' =>'640', 'height' => '520', 'onclosejs' => 'document.getElementById("save_button").click();'));
		$this->jquery->dialog('r_add_event_durl', $this->user->lang('add_event'), array('url' => 'manage_events.php'.$this->SID.'&upd=true&simple_head=true', 'width' =>'700', 'height' =>'550', 'onclosejs' => 'document.getElementById("save_button").click();'));

		$this->core->set_vars(array(
			'page_title'    => $this->user->lang('manraid_title'),
			'template_file' => 'admin/manage_raids_edit.html',
			'display'       => true)
		);
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
		$footer_text = sprintf($this->user->lang('listraids_footcount'), $raid_count ,$this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array(
			'RAID_LIST' => $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_rlimit'], $footer_text),
			'PAGINATION' => generate_pagination('manage_raids.php'.$sort_suffix, $raid_count, $this->user->data['user_rlimit'], $this->in->get('start', 0)),
			'IMPORT_DKP' => ($this->pm->check('raidlogimport', PLUGIN_INSTALLED)) ? '<a href="'.$this->root_path.'plugins/raidlogimport/admin/dkp.php"><input type="button" class="mainoption" value="'.$this->user->lang('raidlogimport_dkp').'" /></a>' : '',
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manraid_title'),
			'template_file'		=> 'admin/manage_raids.html',
			'display'			=> true)
		);
	}

	private function get_raiddata($raid_id, $attendees=false) {
		$raid['id'] = $raid_id;
		$raid['date'] = $this->pdh->get('raid', 'date', array($raid_id));
		$raid['event'] = $this->pdh->get('raid', 'event', array($raid_id));
		$raid['note'] = $this->pdh->get('raid', 'note', array($raid_id));
		$raid['value'] = $this->pdh->get('raid', 'value', array($raid_id));
		if($attendees) {
			$raid['attendees'] = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
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

	private function get_post($norefresh=false) {
		$data = array();
		$data['raid']['id'] = $this->url_id;
		$data['raid']['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$data['raid']['note'] = $this->in->get('rnote','');
		$data['raid']['event'] = $this->in->get('event',0);
		$data['raid']['value'] = $this->in->get('value',0.0);
		$data['raid']['attendees'] = $this->in->getArray('raid_attendees','int');
		if(empty($data['raid']['attendees'])) {
			$data['false'][] = 'raids_members';
		}
		if(is_array($this->in->getArray('adjs', 'int'))) {
			foreach($this->in->getArray('adjs', 'int') as $key => $adj) {
				if(!isset($adj['delete']) OR !$this->in->exists('itemadj_del')) {
					if(!isset($adj['members'])) {
						$data['false'][] = 'adjustments_members';
					}
					$data['adjs'][$key]['group_key'] = $this->in->get('adjs:'.$key.':group_key','','hash');
					$data['adjs'][$key]['members'] = $this->in->getArray('adjs:'.$key.':members','int');
					$data['adjs'][$key]['reason'] = $this->in->get('adjs:'.$key.':reason','');
					$data['adjs'][$key]['event'] = $this->in->get('adjs:'.$key.':event',0);
					$data['adjs'][$key]['value'] = $this->in->get('adjs:'.$key.':value',0.0);
				} else {
					if(isset($adj['delete']) AND $adj['group_key'] != 'new' AND $this->in->exists('itemadj_del')) {
						$ids2del = $this->pdh->get('adjustment', 'ids_of_group_key', array($this->in->get('adjs:'.$key.':group_key','','hash')));
						foreach ($ids2del as $id) {
							$this->pdh->put('adjustment', 'delete_adjustment', array($id));
						}
					}
				}
			}
		}

		if(is_array($this->in->getArray('items', 'int'))) {
			foreach($this->in->getArray('items', 'int') as $key => $item) {
				if(!isset($item['delete']) OR !$this->in->exists('itemadj_del')) {
					if(!isset($item['members'])) {
						$data['false'][] = 'items_members';
					}
					$data['items'][$key]['group_key'] = $this->in->get('items:'.$key.':group_key','','hash');
					$data['items'][$key]['name'] = $this->in->get('items:'.$key.':name','');
					$data['items'][$key]['item_id'] = $this->in->get('items:'.$key.':itemid',0);
					$data['items'][$key]['members'] = $this->in->getArray('items:'.$key.':members','int');
					$data['items'][$key]['value'] = $this->in->get('items:'.$key.':value',0.0);
					$data['items'][$key]['itempool_id'] = $this->in->get('items:'.$key.':itempool_id',0);
				} else {
					if(isset($item['delete']) AND $item['group_key'] != 'new' AND $this->in->exists('itemadj_del')) {
						$ids2del = $this->pdh->get('item', 'ids_of_group_key', array($this->in->get('items:'.$key.':group_key','','hash')));
						foreach($ids2del as $id) {
							$this->pdh->put('item', 'delete_item', array($id));
						}
					}
				}
			}
		}
		if(isset($data['false']) AND !$norefresh) {
			foreach($data['false'] as $miss) {
				$params = explode('_', $miss);
				$missing .= $this->user->lang($params[0]).': '.$this->user->lang($params[1]).'<br />';
			}
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => $missing, 'color' => 'red'), true);
		}
		return $data;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageRaids', ManageRaids::__shortcuts());
registry::register('ManageRaids');
?>