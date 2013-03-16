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

class ManageItems extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'time', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_item_add', 'csrf'=>true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
		);
		parent::__construct('a_item_', $handler, array('item', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function save() {
		$item = $this->get_post();
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

	public function update($message=false) {
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

		$item_names = $this->pdh->aget('item', 'name', 0, array($this->pdh->get('item', 'id_list')));
		$this->jquery->Autocomplete('name', array_unique($item_names));
		$this->confirm_delete($this->user->lang('confirm_delete_item')."<br />".((isset($item['name'])) ? $item['name'] : ''), '', true);
		$this->tpl->assign_vars(array(
			'GRP_KEY'		=> (isset($grp_key)) ? $grp_key : '',
			'NAME'			=> (isset($item['name'])) ? $item['name'] : '',
			'RAID'			=> $this->html->DropDown('raid_id', $raids, ((isset($item['raid_id'])) ? $item['raid_id'] : '')),
			'BUYERS'		=> $this->jquery->MultiSelect('buyers', $members, ((isset($item['buyers'])) ? $item['buyers'] : ''), array('width' => 350, 'filter' => true)),
			'DATE'			=> $this->jquery->Calendar('date', $this->time->user_date($item['date'], true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'VALUE'			=> (isset($item['value'])) ? $item['value'] : '',
			'ITEM_ID'		=> (isset($item['item_id'])) ? $item['item_id'] : '',
			'ITEMPOOLS'		=> $this->html->DropDown('itempool_id', $itempools, ((isset($item['itempool_id'])) ? $item['itempool_id'] : ''))
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manitems_title'),
			'template_file'		=> 'admin/manage_item_edit.html',
			'display'			=> true)
		);
	}

	public function display($messages=false) {
		if($messages){
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
		$footer_text = sprintf($this->user->lang('listitems_footcount'), $item_count, $this->user->data['user_ilimit']);
		
		$this->confirm_delete($this->user->lang('confirm_delete_items'));

		$this->tpl->assign_vars(array(
			'SID'	=> $this->SID,
			'ITEM_LIST' => $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_ilimit'], $footer_text),
			'PAGINATION' => generate_pagination('manage_items.php'.$sort_suffix, $item_count, $this->user->data['user_ilimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manitems_title'),
			'template_file'		=> 'admin/manage_items.html',
			'display'			=> true)
		);
	}

	private function get_post($norefresh=false) {
		$item['name'] = $this->in->get('name','');
		foreach($this->in->getArray('buyers','int') as $buyer){
			$item['buyers'][] = $buyer;
		}
		$item['itempool_id'] = $this->in->get('itempool_id',0);
		if(!$item['name']){
			$missing[] = $this->user->lang('name');
		}
		if(!$item['buyers']){
			$missing[] = $this->user->lang('buyers');
		}
		if(!$item['itempool_id']){
			$missing[] = $this->user->lang('itempool');
		}
		if(!empty($missing) AND !$norefresh){
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		$item['value'] = $this->in->get('value',0.0);
		$item['date'] = $this->time->fromformat($this->in->get('date','1.1.1970 00:00'), 1);
		$item['raid_id'] = $this->in->get('raid_id',0);
		$item['item_id'] = $this->in->get('item_id','');
		return $item;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageItems', ManageItems::__shortcuts());
registry::register('ManageItems');
?>