<?php
/*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */


define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class ManageRaids extends EQdkp_Admin
{
	function ManageRaids ()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_params(array(
			'update' => array(
				'name'		=> 'update',
				'process'	=> 'edit_raid',
				'check'		=> 'a_raid_upd')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name'		=> 'save',
				'process'	=> 'save_raid',
				'check'		=> 'a_raid_add'),
			'del_r' => array(
				'name'		=> 'raid_del',
				'process'	=> 'delete_raid',
				'check'		=> 'a_raid_del'),
			'del_confirm' => array(
				'name'		=> 'confirm',
				'process'	=> 'delete_raid',
				'check'		=> 'a_raid_del'),
			'del_ia' => array(
				'name'		=> 'itemadj_del',
				'process'	=> 'edit_raid',
				'check'		=> 'a_raid_del'),
			'refresh' => array(
				'name'		=> 'refresh',
				'process'	=> 'edit_raid',
				'check'		=> 'a_raid_'),
            'form' => array(
                'name'      => '',
                'process'   => 'display_form',
                'check'     => 'a_raid_')
			)
		);
	}

	function delete_raid()
	{
		global $user, $pdh, $in;
		if($_POST['raid_id'] != 'new' AND !isset($_POST['confirm']))
		{
			$raid_id = $in->get('raid_id',0);
			confirm_delete($user->lang['del_raid_with_itemadj'], 'raid_id', $raid_id);
		}
		elseif(isset($_POST['confirm']) AND $_POST['confirm'] == $user->lang['yes'])
		{
			$raid_id = $in->get('raid_id',0);
			//delete everything connected to the raid
			//adjustments first
			$adj_ids = $pdh->get('adjustment', 'adjsofraid', array($raid_id));
			$adj_del = array(true);
			foreach($adj_ids as $id)
			{
				$adj_del[] = $pdh->put('adjustment', 'delete_adjustment', array($id));
			}
			//raid itself now
			$raid_del = $pdh->put('raid', 'delete_raid', array($raid_id));
			if($raid_del)
			{
				$messages[] = array('text' => $user->lang['raid'].' '.$raid_id, 'title' => $user->lang['del_suc'], 'color' => 'green');
			}
			else
			{
				$messages[] = array('text' => $user->lang['raid'].' '.$raid_id, 'title' => $user->lang['del_nosuc'], 'color' => 'red');
			}
			if(in_array(false, $adj_del))
			{
				$messages[] = array('text' => $user->lang['adjustments'], 'title' => $user->lang['del_nosuc'], 'color' => 'red');
			}
			else
			{
				$messages[] = array('text' => $user->lang['adjustments'], 'title' => $user->lang['del_suc'], 'color' => 'green');
			}
			$this->display_form($messages);
		}
		else
		{
			$this->display_form();
		}
	}

	function save_raid()
	{
		global $user, $pdh;
		$data = $this->get_post();
		if(!$data['raid']['id']) {
			$raid_upd = $pdh->put('raid', 'add_raid', array($data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value']));
			$data['raid']['id'] = ($raid_upd) ? $raid_upd : false;
		} else {
			$raid_upd = $pdh->put('raid', 'update_raid', array($data['raid']['id'], $data['raid']['date'], $data['raid']['attendees'], $data['raid']['event'], $data['raid']['note'], $data['raid']['value']));
		}
        if($raid_upd) {
			$adj_upd = array(true);
			if(is_array($data['adjs'])) {
			  foreach($data['adjs'] as $adj) {
			  	if($adj['group_key'] == 'new' OR empty($adj['group_key'])) {
			  		$adj_upd[] = $pdh->put('adjustment', 'add_adjustment', array($adj['value'], $adj['reason'], $adj['members'], $adj['event'], $data['raid']['id'], $data['raid']['date']));
			  	} else {
					$adj_upd[] = $pdh->put('adjustment', 'update_adjustment', array($adj['group_key'], $adj['value'], $adj['reason'], $adj['members'], $adj['event'], $data['raid']['id'], $data['raid']['date']));
				}
			  }
			}
			$item_upd = array(true);
			if(is_array($data['items'])) {
			  foreach($data['items'] as $item) {
			  	if($item['group_key'] == 'new' OR empty($item['group_key'])) {
			  		$item_upd[] = $pdh->put('item', 'add_item', array($item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']));
			  	} else {
					$item_upd[] = $pdh->put('item', 'update_item', array($item['group_key'], $item['name'], $item['members'], $data['raid']['id'], $item['item_id'], $item['value'], $item['itempool_id'], $data['raid']['date']));
				}
			  }
			}
			$messages[] = array('text' => $user->lang['raids'], 'title' => $user->lang['save_suc'], 'color' => 'green');
		} else {
			$messages[] = array('text' => $user->lang['raids'], 'title' => $user->lang['save_nosuc'], 'color' => 'red');
		}
		if(in_array(false, $adj_upd)) {
			$messages[] = array('text' => $user->lang['adjustments'], 'title' => $user->lang['save_nosuc'], 'color' => 'red');
		} else {
			$messages[] = array('text' => $user->lang['adjustments'], 'title' => $user->lang['save_suc'], 'color' => 'green');
		}
		if(in_array(false, $item_upd)) {
			$messages[] = array('text' => $user->lang['items'], 'title' => $user->lang['save_nosuc'], 'color' => 'red');
		} else {
			$messages[] = array('text' => $user->lang['items'], 'title' => $user->lang['save_suc'], 'color' => 'green');
		}
		$this->display_form($messages);
	}

	function edit_raid($message=false)
	{
		global $core, $tpl, $user, $pdh, $html, $jquery, $in, $time, $SID;

		if($message)
		{
			$core->messages($message);
			$norefresh = true;
		}

        //fetch members
        $members = $pdh->aget('member', 'name', 0, array($pdh->get('member', 'id_list', array(true,false,false))));
        asort($members);

        //fetch events
        $events = $pdh->aget('event', 'name', 0, array($pdh->get('event', 'id_list')));
        asort($events);

        //fetch itempools
        $itempools = $pdh->aget('itempool', 'name', 0, array($pdh->get('itempool', 'id_list')));
        asort($itempools);
				
				//fetch Raids			
				$raidlist = $pdh->get('raid', 'id_list');
				$raidlist = $pdh->sort($raidlist, 'raid', 'date', 'desc');
				$raids[] = '';
				foreach ($raidlist as $key => $row){
					$raids[$row] = $time->date($user->style['date_notime_short'], $pdh->get('raid', 'date', array($row))) . ' - ' . stripslashes($pdh->get('raid', 'event_name', array($row)));
				}
				
				//fetch notes
				$notes = $pdh->aget('raid', 'note', 0, array($pdh->get('raid', 'id_list')));
				asort($notes);
				$jquery->Autocomplete('event', array_unique($notes));


		$raid = array('id' => $in->get('r',0), 'date' => $time->time, 'note' => '', 'event' => 0, 'value' => 0.00, 'attendees' => array());
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
		if ($in->get('draft', 0) > 0){
			$raid = $this->get_raiddata($in->get('draft', 0), true);
			$raid['id']	 = 'new'; 
			$raid['date']	 = $time->time; 
		}		
		//we're refreshing the view
		if(isset($_POST['refresh']) OR isset($_POST['itemadj_del']))
		{
			$data = $this->get_post($norefresh);
			$raid = $data['raid'];
			$adjs = $data['adjs'];
			$items = $data['items'];
			if($_POST['refresh'] == $user->lang['add_aadjustment'])
			{
				$adjs[] = array('group_key' => 'new', 'event' => $raid['event']);
			}
			if($_POST['refresh'] == $user->lang['add_aitem'])
			{
				$items[] = array('group_key' => 'new');
			}
		}
		if(is_array($adjs))
		{
          
					
					foreach($adjs as $key => $adj)
          {
           $tpl->assign_block_vars('adjs', array(
                'KEY'       => $key,
                'GK'        => $adj['group_key'],
                'MEMBER'    => $jquery->MultiSelect('adjs['.$key.'][members]', $members, $adj['members'], 200, 200, array('id'=>'adjs_'.$key.'_members')),
                'REASON'    => sanitize($adj['reason']),
                'EVENT'     => $html->DropDown('adjs['.$key.'][event]', $events, $adj['event']),
                'VALUE'		=> $adj['value'],
                'RCLASS'    => $core->switch_row_class())
           );
					 $adjs_ids[] = 'adjs_'.$key;
					 
          }
					if (is_array($adjs_ids)){
						//fetch adjustment-reasons
						$adjustment_reasons = $pdh->aget('adjustment', 'reason', 0, array($pdh->get('adjustment', 'id_list')));
						$jquery->Autocomplete($adjs_ids, array_unique($adjustment_reasons));
					}
					
        }
        if(is_array($items))
        {
          foreach($items as $key => $item)
          {
        	$tpl->assign_block_vars('items', array(
                'KEY'       => $key,
                'GK'        => $item['group_key'],
                'NAME'      => stripslashes($item['name']),
                'ITEMID'    => $item['item_id'],
                'MEMBER'    => $jquery->MultiSelect('items['.$key.'][members]', $members, $item['members'], 200, 200, array('id'=>'items_'.$key.'_members')),
                'VALUE'     => $item['value'],
                'ITEMPOOL'	=> $html->DropDown('items['.$key.'][itempool_id]', $itempools, $item['itempool_id']),
                'RCLASS'    => $core->switch_row_class())
            );
						$item_ids[] = 'items_'.$key;
          }
					if (is_array($item_ids)){
						$item_names = $pdh->aget('item', 'name', 0, array($pdh->get('item', 'id_list')));
						$jquery->Autocomplete($item_ids, array_unique($item_names));
					}
        }
    	$tpl->assign_vars(array(
    		'DATE'				=> $jquery->Calendar('date', date('d.m.y', $raid['date'])),
    		'HOUR'				=> date('H', $raid['date']),
    		'MINUTE'			=> date('i', $raid['date']),
    		'SECOND'			=> date('s', $raid['date']),
    		'NOTE'				=> stripslashes($raid['note']),
    		'EVENT'				=> $html->DropDown('event', $events, $raid['event']),
    		'RAID_ID'			=> ($raid['id']) ? $raid['id'] : 0,
    		'VALUE'				=> $raid['value'],
    		'NEW_MEM_SEL'		=> $jquery->MultiSelect('raid_attendees', $members, $raid['attendees'], 200, 700),
				'RAID_DROPDOWN'	=> $html->DropDown('draft', $raids, $in->get('draft', 0), '', 'onChange="window.location=\'manage_raids.php'.$SID.'&update=true&draft=\'+this.value"'),			
				));

		//language vars
		$tpl->assign_vars(array(
			'L_DATE'			=> $user->lang['date'],
			'L_RAID_ADDUPD'		=> ($raid_id) ? $user->lang['updraid_title'] : $user->lang['addraid_title'],
			'L_ATTENDEES'		=> $user->lang['attendees'],
			'L_ADD_MEM'			=> $user->lang['add_member'],
			'L_SEARCH_MEMBERS'	=> $user->lang['search_members'],
			'L_TIME'			=> $user->lang['time'],
			'L_ADD_EVENT'		=> $user->lang['add_event'],
			'L_NOTE'			=> $user->lang['note'],
			'L_ADJUSTMENTS'		=> $user->lang['adjustments'],
			'L_ADD_ADJ'			=> $user->lang['add_aadjustment'],
			'L_REASON'			=> $user->lang['reason'],
			'L_EVENT'			=> $user->lang['event'],
			'L_ITEMS'			=> $user->lang['items'],
			'L_ADD_ITEM'		=> $user->lang['add_aitem'],
			'L_ITEM'			=> $user->lang['item_name'],
			'L_ITEMID'			=> $user->lang['item_id'],
			'L_ITEMPOOL'		=> $user->lang['itempool'],
			'L_MEMBER'			=> $user->lang['member'],
			'L_VALUE'			=> $user->lang['value'],
			'L_ADJITEM_DEL'		=> $user->lang['adjitem_del'],
			'L_RAID_DEL'		=> $user->lang['delete_raid'],
			'L_RESET'			=> $user->lang['reset'],
			'L_RAID_SAVE'		=> ($raid['id'] AND $raid['id'] != 'new') ? $user->lang['update_raid'] : $user->lang['add_raid'],
			//other needed vars
			'S_RAID_UPD'		=> ($raid['id'] AND $raid['id'] != 'new') ? true : false,
			'L_SELECT_RAID'	=> $user->lang['select_raid_draft'],
		));

		$tpl->add_js("$(document).ready(function() {
						$('#r_add_mem').click(function() {
							r_add_mem_durl();
						});
						$('#r_add_event').click(function() {
							r_add_event_durl();
						});
					  });");
		$jquery->dialog('r_add_mem_durl', $user->lang['add_member'], array('url' => 'manage_members.php?mupd=true&simple_head=true', 'width' =>'600', 'height' => '300'));
		$jquery->dialog('r_add_event_durl', $user->lang['add_event'], array('url' => 'manage_events.php?update=true&simple_head=true', 'width' =>'700', 'height' =>'550'));
						

		$core->set_vars(array(
            'page_title'    => $user->lang['manraid_title'],
            'template_file' => 'admin/manage_raids_edit.html',
            'display'       => true)
        );
	}

	function display_form($messages=false)
	{
		global $core, $pdh, $SID, $user, $tpl, $pm, $eqdkp_root_path, $in;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}

		include($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
		$view_list = $pdh->get('raid', 'id_list', array());
		
		$hptt_page_settings = $pdh->get_page_settings('admin_manage_raids', 'hptt_admin_manage_raids_raidlist');
		$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_raids.php', '%link_url_suffix%' => '&update=true'));
		$page_suffix = '&amp;start='.$in->get('start', 0);
		$sort_suffix = '?sort='.$in->get('sort', '0|desc');

		//footer
		$raid_count = count($view_list);
		$footer_text = sprintf($user->lang['listraids_footcount'], $raid_count ,$user->data['user_rlimit']);

		$tpl->assign_vars(array(
			'RAID_LIST' => $hptt->get_html_table($in->get('sort','0|desc'), $page_suffix, $in->get('start', 0), $user->data['user_rlimit'], $footer_text),
			'PAGINATION' => generate_pagination('manage_raids.php'.$sort_suffix, $raid_count, $user->data['user_rlimit'], $in->get('start', 0)),
      'IMPORT_DKP' => ($pm->check(PLUGIN_INSTALLED, 'raidlogimport')) ? '<a href="'.$eqdkp_root_path.'plugins/raidlogimport/admin/dkp.php"><input type="button" class="mainoption" value="'.$user->lang['raidlogimport_dkp'].'" /></a>' : '',
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
		//language vars
			'L_RAIDS'	=> $user->lang['manraid_title'],
			'L_DATE'	=> $user->lang['date'],
			'L_EVENT'	=> $user->lang['event'],
			'L_NOTE'	=> $user->lang['note'],
			'L_VALUE'	=> $user->lang['value'],
			'L_ADD_RAID' => $user->lang['add_raid'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manraid_title'],
            'template_file' => 'admin/manage_raids.html',
            'display'       => true)
        );
	}

    function get_raiddata($raid_id, $attendees=false)
    {
    	global $pdh;
    	$raid['id'] = $raid_id;
		$raid['date'] = $pdh->get('raid', 'date', array($raid_id));
		$raid['event'] = $pdh->get('raid', 'event', array($raid_id));
		$raid['note'] = $pdh->get('raid', 'note', array($raid_id));
		$raid['value'] = $pdh->get('raid', 'value', array($raid_id));
		if($attendees)
		{
			$raid['attendees'] = $pdh->get('raid', 'raid_attendees', array($raid_id));
		}
		return $raid;
	}

	function get_adjsofraid($raid_id)
	{
		global $pdh;
		if(!$raid_id) {
			return false;
		}
		$adj_ids = $pdh->get('adjustment', 'adjsofraid', array($raid_id));
		$ak = 0;
		$adjs = array();
		foreach($adj_ids as $id)
		{
			$group_key[$ak] = $pdh->get('adjustment', 'group_key', array($id));
			$adjs[$ak]['member'] = $pdh->get('adjustment', 'member', array($id));
			$adjs[$ak]['reason'] = $pdh->get('adjustment', 'reason', array($id));
			$adjs[$ak]['event'] = $pdh->get('adjustment', 'event', array($id));
			$adjs[$ak]['value'] = $pdh->get('adjustment', 'value', array($id));
			$ak++;
		}
		$ret_adjs = array();
		$ret_group_key = array();
		foreach($adjs as $key => $adj)
		{
			$ak = array_search($group_key[$key], $ret_group_key);
			if($ak !== false)
			{
				$ret_adjs[$ak]['members'][] = $adj['member'];
			}
			else
			{
				$ret_adjs[$key] = $adj;
				unset($ret_adjs[$key]['member']);
				$ret_adjs[$key]['members'][] = $adj['member'];
				$ret_adjs[$key]['group_key'] = $group_key[$key];
				$ret_group_key[$key] = $group_key[$key];
			}
		}
		return $ret_adjs;
	}

	function get_itemsofraid($raid_id)
	{
		global $pdh;
		$item_ids = $pdh->get('item', 'itemsofraid', array($raid_id));
		$ik = 0;
		$items = array();
		foreach($item_ids as $id)
		{
			$group_key[$ik] = $pdh->get('item', 'group_key', array($id));
			$items[$ik]['name'] = $pdh->get('item', 'name', array($id));
			$items[$ik]['item_id'] = $pdh->get('item', 'game_itemid', array($id));
			$items[$ik]['value'] = $pdh->get('item', 'value', array($id));
			$items[$ik]['member'] = $pdh->get('item', 'buyer', array($id));
			$items[$ik]['itempool'] = $pdh->get('item', 'itempool_id', array($id));
			$ik++;
		}
		$ret_items = array();
		$ret_group_key = array();
		foreach($items as $key => $item)
		{
			$ik = array_search($group_key[$key], $ret_group_key);
			if($ik !== false)
			{
				$ret_items[$ik]['members'][] = $item['member'];
			}
			else
			{
				$ret_items[$key] = $item;
				unset($ret_items[$key]['member']);
				$ret_items[$key]['members'][] = $item['member'];
				$ret_items[$key]['group_key'] = $group_key[$key];
				$ret_group_key[$key] = $group_key[$key];
			}
		}
		return $ret_items;
	}

	function get_post($norefresh=false)
	{
		global $pdh, $user, $in;
		$data = array();
		list($day, $mon, $yea) = explode('.', $in->get('date','1.1.1970'));
		$data['raid']['id'] = $in->get('raid_id',0);
		$data['raid']['date'] = mktime($in->get('rhour',0), $in->get('rminute',0), $in->get('rsecond',0), $mon, $day, $yea);
		$data['raid']['note'] = $in->get('rnote','');
		$data['raid']['event'] = $in->get('event',0);
		$data['raid']['value'] = $in->get('value',0.0);
		$data['raid']['attendees'] = $in->getArray('raid_attendees','int');
		if(empty($data['raid']['attendees']))
		{
			$data['false'][] = 'raids_members';
		}
		if(is_array($_POST['adjs']))
		{
			foreach($_POST['adjs'] as $key => $adj)
			{
			  if(!isset($adj['delete']) OR !isset($_POST['itemadj_del']))
			  {
			  	if(!isset($adj['members'])) {
			  		$data['false'][] = 'adjustments_members';
			  	}
			  	$data['adjs'][$key]['group_key'] = $in->get('adjs:'.$key.':group_key','','hash');
				$data['adjs'][$key]['members'] = $in->getArray('adjs:'.$key.':members','int');
				$data['adjs'][$key]['reason'] = $in->get('adjs:'.$key.':reason','');
				$data['adjs'][$key]['event'] = $in->get('adjs:'.$key.':event',0);
				$data['adjs'][$key]['value'] = $in->get('adjs:'.$key.':value',0.0);
			  }
			  else
			  {
			  	if($adj['delete'] AND $adj['group_key'] != 'new' AND isset($_POST['itemadj_del']))
			  	{
			  		$ids2del = $pdh->get('adjustment', 'ids_of_group_key', array($in->get('adjs:'.$key.':group_key','','hash')));
			  		foreach ($ids2del as $id)
			  		{
			  			$pdh->put('adjustment', 'delete_adjustment', array($id));
			  		}
			  	}
			  }
			}
		}
		if(is_array($_POST['items']))
		{
			foreach($_POST['items'] as $key => $item)
			{
			  if(!isset($item['delete']) OR !isset($_POST['itemadj_del']))
			  {
			  	if(!isset($item['members'])) {
			  		$data['false'][] = 'items_members';
			  	}
			  	$data['items'][$key]['group_key'] = $in->get('items:'.$key.':group_key','','hash');
				$data['items'][$key]['name'] = $in->get('items:'.$key.':name','');
				$data['items'][$key]['item_id'] = $in->get('items:'.$key.':itemid',0);
				$data['items'][$key]['members'] = $in->getArray('items:'.$key.':members','int');
				$data['items'][$key]['value'] = $in->get('items:'.$key.':value',0.0);
				$data['items'][$key]['itempool_id'] = $in->get('items:'.$key.':itempool_id',0);
			  }
			  else
			  {
			  	if($item['delete'] AND $item['group_key'] != 'new' AND isset($_POST['itemadj_del']))
			  	{
			  		$ids2del = $pdh->get('item', 'ids_of_group_key', array($in->get('items:'.$key.':group_key','','hash')));
			  		foreach($ids2del as $id)
			  		{
			  			$pdh->put('item', 'delete_item', array($id));
			  		}
			  	}
			  }
			}
		}
		if(isset($data['false']) AND !$norefresh)
		{
			$_POST['refresh'] = '';
			foreach($data['false'] as $miss)
			{
				$params = explode('_', $miss);
				$missing .= $user->lang[$params[0]].': '.$user->lang[$params[1]].'<br />';
			}
			$this->edit_raid(array('title' => $user->lang['missing_values'], 'text' => $missing, 'color' => 'red'));
		}
		return $data;
	}
}

$manraid = new ManageRaids;
$manraid->process();
?>