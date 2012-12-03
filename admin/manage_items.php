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

class ManageItems extends EQdkp_Admin
{
	function ManageItems ()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_params(array(
			'update' => array(
				'name'		=> 'update',
				'process'	=> 'edit_item',
				'check'		=> 'a_item_upd')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name'		=> 'save',
				'process'	=> 'save_item',
				'check'		=> 'a_item_add'),
			'del' => array(
				'name'		=> 'delete',
				'process'	=> 'del_item',
				'check'		=> 'a_item_del'),
            'form' => array(
                'name'      => '',
                'process'   => 'display_form',
                'check'     => 'a_item_')
			)
		);
	}

	function save_item()
	{
		global $user, $pdh, $in;

		$item = $this->get_post();
		if($in->get('selected_ids','','hash'))
		{
			$retu = $pdh->put('item', 'update_item', array($in->get('selected_ids','','hash'), $item['name'], $item['buyers'], $item['raid_id'], $item['item_id'], $item['value'], $item['itempool_id'], $item['date']));
		}
		else
		{
			$retu = $pdh->put('item', 'add_item', array($item['name'], $item['buyers'], $item['raid_id'], $item['item_id'], $item['value'], $item['itempool_id'], $item['date']));
		}
		if($retu)
		{
			$message = array('title' => $user->lang['save_suc'], 'text' => $item['name'], 'color' => 'green');
		}
		else
		{
			$message = array('title' => $user->lang['save_no_suc'], 'text' => $item['name'], 'color' => 'red');
		}
		$this->display_form($message);
	}

	function del_item()
	{
		global $user, $pdh, $in;

		$ids = array();
		if(!is_array($_POST['selected_ids']))
		{
			$ids = $pdh->get('item', 'ids_of_group_key', array($in->get('selected_ids','','hash')));
		}
		else
		{
			foreach($in->getArray('selected_ids','int') as $s_id)
			{
				$new_ids = $pdh->get('item', 'ids_of_group_key', array($pdh->get('item', 'group_key', array($s_id))));
				$ids = array_merge($ids, $new_ids);
			}
		}
		$retu = array();
		foreach($ids as $id)
		{
			$retu[$id] = $pdh->put('item', 'delete_item', array($id));
		}
		foreach($retu as $id => $suc)
		{
			if($suc)
			{
				$pos[] = stripslashes($pdh->get('item', 'name', array($id)));
			}
			else
			{
				$neg[] = stripslashes($pdh->get('item', 'name', array($id)));
			}
		}
		if($pos)
		{
			$messages[] = array('title' => $user->lang['del_suc'], 'text' => implode(', ', $pos), 'color' => 'green');
		}
		if($neg)
		{
			$messages[] = array('title' => $user->lang['del_no_suc'], 'text' => implode(', ', $neg), 'color' => 'red');
		}
		$this->display_form($messages);
	}

	function edit_item($message=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $jquery, $html, $in,  $time;

        //fetch members for select
        $members = $pdh->aget('member', 'name', 0, array($pdh->get('member', 'id_list')));

        //fetch raids for select
        $raids = array();
        $raidids = $pdh->get('raid', 'id_list');
        foreach($raidids as $id)
        {
            $raids[$id] = '#ID:'.$id.' - '.$pdh->get('event', 'name', array($pdh->get('raid', 'event', array($id)))).' '.date('d.m.y', $pdh->get('raid', 'date', array($id)));
        }

        //fetch itempools for select
        $itempools = $pdh->aget('itempool', 'name', 0, array($pdh->get('itempool', 'id_list')));

		if($message)
		{
			$core->messages($message);
			$item = $this->get_post(true);
		}
        if(isset($_GET['i']))
        {
        	$grp_key = $pdh->get('item', 'group_key', array($in->get('i',0)));
        	$ids = $pdh->get('item', 'ids_of_group_key', array($grp_key));
        	foreach($ids as $id)
        	{
        		$item['buyers'][] = $pdh->get('item', 'buyer', array($id));
        	}
        	$item['name'] = $pdh->get('item', 'name', array($id));
        	$item['value'] = $pdh->get('item', 'value', array($id));
        	$item['date'] = $pdh->get('item', 'date', array($id));
        	$item['raid_id'] = $pdh->get('item', 'raid_id', array($id));
        	$item['item_id'] = $pdh->get('item', 'game_itemid', array($id));
        	$item['itempool_id'] = $pdh->get('item', 'itempool_id', array($id));
        } else {
					$item['date'] = $time->time;				
				}
				
				$item_names = $pdh->aget('item', 'name', 0, array($pdh->get('item', 'id_list')));
				$jquery->Autocomplete('name', array_unique($item_names));

        $tpl->assign_vars(array(
        	'GRP_KEY'	=> $grp_key,
        	'NAME'		=> $item['name'],
        	'RAID' 		=> $html->DropDown('raid_id', $raids, $item['raid_id']),
        	'BUYERS'	=> $jquery->MultiSelect('buyers', $members, $item['buyers'], 200, 350),
        	'DATE'		=> $jquery->calendar('date', date('d.m.Y', $item['date'])),
        	'VALUE'		=> $item['value'],
        	'HOUR'		=> date('H', $item['date']),
        	'MIN'		=> date('i', $item['date']),
        	'SEC'		=> date('s', $item['date']),
        	'ITEM_ID'	=> $item['item_id'],
        	'ITEMPOOLS' => $html->DropDown('itempool_id', $itempools, $item['itempool_id']),
        	//language
        	'L_NAME'	=> $user->lang['name'],
        	'L_DATE'	=> $user->lang['date'],
        	'L_BUYERS'	=> $user->lang['buyers'],
        	'L_VALUE'	=> $user->lang['value'],
        	'L_TIME'	=> $user->lang['time'],
        	'L_RAID'	=> $user->lang['raid'],
        	'L_EDIT_ITEM' => $user->lang['editing_item'],
        	'L_SAVE'	=> $user->lang['save'],
        	'L_DEL'		=> $user->lang['delete'],
        	'L_ITEMID'	=> $user->lang['item_id'],
        	'L_ITEMPOOL' => $user->lang['itempool'])
        );

        $core->set_vars(array(
            'page_title'    => $user->lang['manitem_title'],
            'template_file' => 'admin/manage_item_edit.html',
            'display'       => true)
        );
	}

	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $in, $eqdkp_root_path;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}
		include($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
		$view_list = $pdh->aget('item', 'group_key', 0, array($pdh->get('item', 'id_list', array())));
		$view_list = array_flip($view_list);
		$hptt_page_settings = $pdh->get_page_settings('admin_manage_items', 'hptt_admin_manage_items_itemlist');
		$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_items.php', '%link_url_suffix%' => '&update=true', '%raid_link_url%' => 'manage_raids.php', '%raid_link_url_suffix%' => '&update=true'));
		$page_suffix = '&amp;start='.$in->get('start', 0);
		$sort_suffix = '?sort='.$in->get('sort', '0|desc');

    $item_count = count($view_list);
		$footer_text = sprintf($user->lang['listitems_footcount'], $item_count ,$user->data['user_ilimit']);

		$tpl->assign_vars(array(
			'SID'	=> $SID,
			'ITEM_LIST' => $hptt->get_html_table($in->get('sort','0|desc'), $page_suffix, $in->get('start', 0), $user->data['user_ilimit'], $footer_text),
			'PAGINATION' => generate_pagination('manage_items.php'.$sort_suffix, $item_count, $user->data['user_ilimit'], $in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			//language
			'L_ITEMS'		=> $user->lang['manitems_title'],
			'L_DATE'		=> $user->lang['date'],
			'L_NAME'		=> $user->lang['name'],
			'L_RAID'		=> $user->lang['raid'],
			'L_MEMBERS'		=> $user->lang['buyers'],
			'L_VALUE'		=> $user->lang['value'],
			'L_MASS_DEL'	=> $user->lang['delete_selected_items'],
			'L_ITEMPOOL'	=> $user->lang['itempool'],
			
			'L_ADD_ITEM'	=> $user->lang['add_item'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manitems_title'],
            'template_file' => 'admin/manage_items.html',
            'display'       => true)
        );
    }

    function get_post($norefresh=false)
    {
    	global $user, $in;
        $item['name'] = $in->get('name','');
        foreach($in->getArray('buyers','int') as $buyer)
        {
            $item['buyers'][] = $buyer;
        }
        $item['itempool_id'] = $in->get('itempool_id',0);
    	if(!$item['name'])
    	{
    		$missing[] = $user->lang['name'];
    	}
    	if(!$item['buyers'])
    	{
    		$missing[] = $user->lang['buyers'];
    	}
    	if(!$item['itempool_id'])
    	{
    		$missing[] = $user->lang['itempool'];
    	}
    	if($missing AND !$norefresh)
    	{
    		$this->edit_item(array('title' => $user->lang['missing_values'], 'text' => implode(', ', $missing), 'color' => 'red'));
    	}
        $item['value'] = $in->get('value',0.0);
        list($day, $mon, $year) = explode('.', $in->get('date','1.1.1970'));
        $item['date'] = mktime($in->get('hour',0), $in->get('min',0), $in->get('sec',0), $mon, $day, $year);
        $item['raid_id'] = $in->get('raid_id',0);
        $item['item_id'] = $in->get('item_id',0);
        return $item;
    }
}

$manitems = new ManageItems;
$manitems->process();
?>