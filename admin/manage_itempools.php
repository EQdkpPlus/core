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

class Manage_Itempools extends EQdkp_Admin
{
	function Manage_Itempools()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_params(array(
			'update' => array(
				'name' => 'update',
				'process' => 'update_itempool',
				'check' => 'a_event_add')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name' => 'save',
				'process' => 'itempool_save',
				'check' => 'a_event_add'),
			'del' => array(
				'name' => 'delete',
				'process' => 'delete_itempool',
				'check' => 'a_event_del'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'delete_itempool',
				'check' => 'a_event_del'),
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_event_upd')
			)
		);
	}

	function itempool_save()
	{
		global $user, $pdh, $in;
		$itemp = $this->get_post();
		$ip_id = $in->get('ip_id',0);
		if($itemp)
		{
			if($ip_id)
			{
				$retu = $pdh->put('itempool', 'update_itempool', array($ip_id, $itemp['name'], $itemp['desc']));
			}
			else
			{
				$retu = $pdh->put('itempool', 'add_itempool', array($itemp['name'], $itemp['desc']));
			}
			if(!$retu)
			{
				$message = array('title' => $user->lang['save_nosuc'], 'text' => $itemp['name'], 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['save_suc'], 'text' => $itemp['name'], 'color' => 'green');
			}
		}
		$this->display_form($message);
	}

	function delete_itempool()
	{
		global $user, $pdh, $in;
		$ip_id = $in->get('ip_id',0);
		if($ip_id AND !isset($_POST['confirm']) AND $ip_id != 1)
		{
			confirm_delete($user->lang['confirm_delete_itempools'].'<br />'.$in->get('name',''), 'ip_id', $ip_id);
		}
		elseif($_POST['ip_id'] AND $_POST['confirm'] == $user->lang['yes'])
		{
			$name = $pdh->get('itempool', 'name', $ip_id);
			if(!$pdh->put('itempool', 'delete_itempool', $ip_id))
			{
				$message = array('title' => $user->lang['del_nosuc'], 'text' => $name, 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['del_suc'], 'text' => $name, 'color' => 'green');
			}
		}
		elseif($ip_id == 1)
		{
			$message = array('title' => $user->lang['del_nosuc'], 'text' => $user->lang['no_del_default_itempool'], 'color' => 'red');
		}
		$this->display_form($message);
	}

	function update_itempool($message=false)
	{
		global $core, $user, $tpl, $pdh, $in;

		$iid = $in->get('id',0);
		if($message)
		{
			$core->messages($message);
			$itemp = $this->get_post(true);
		}
		else
		{
			$itemp['name'] = $pdh->get('itempool', 'name', $iid);
			$itemp['desc'] = $pdh->get('itempool', 'desc', $iid);
		}

		$tpl->assign_vars(array(
			'ACTION'				=> 'manage_itempools.php',
			'NAME'					=> $itemp['name'],
			'DESC'					=> $itemp['desc'],
			'IID'					=> $iid,
			//language
            'L_ADD_ITEMPOOL_TITLE'	=> $user->lang['add_itempool'],
            'L_IPNAME'				=> $user->lang['name'],
            'L_DESC'				=> $user->lang['Multi_discr'],
            'L_SAVE'				=> $user->lang['save'],
						'L_CANCEL'				=> $user->lang['cancel'],
            'L_DELETE'				=> $user->lang['delete'])
        );

		$core->set_vars(array(
            'page_title'    => $user->lang['manitempool_title'],
            'template_file' => 'admin/manage_itempools_edit.html',
            'display'       => true)
        );
    }

	function display_form($messages=false)
	{
		global $core, $user, $tpl, $pdh, $SID, $in;

		if($messages)
		{
			$pdh->process_hook_queue();
			$core->messages($messages);
		}

		$order = $in->get('sort','0.0');
		$red = 'RED'.str_replace('.', '', $order);

		$itempool_ids = $pdh->get('itempool', 'id_list');
		foreach($itempool_ids as $id)
		{
			$itempools[$id]['name'] = $pdh->get('itempool', 'name', $id);
			$itempools[$id]['desc'] = $pdh->get('itempool', 'desc', $id);
		}
		$sortedids = get_sortedids($itempools, explode('.', $order), array('name', 'desc'));

		foreach($sortedids as $id)
		{
			$tpl->assign_block_vars('itempools', array(
				'ID'	=> $id,
				'NAME'	=> $itempools[$id]['name'],
				'DESC'	=> $itempools[$id]['desc'],
				'ROWC'	=> $core->switch_row_class())
			);
		}

		$tpl->assign_vars(array(
			'ACTION' 		=> 'manage_itempools.php'.$SID,
			$red 			=> '_red',
			'SID'			=> $SID,
			//Language
			'ITEMPOOLS_FOOTCOUNT' => sprintf($user->lang['itempools_footcount'], count($sortedids)),
			'L_ITEMPOOLS'	=> $user->lang['menu_itempools'],
			'L_NAME'		=> $user->lang['name'],
			'L_DESC'		=> $user->lang['Multi_discr'],
			'L_ADD_ITEMPOOL' => $user->lang['add_itempool'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manitempool_title'],
            'template_file' => 'admin/manage_itempools.html',
            'display'       => true)
        );
	}

	function get_post($norefresh=false)
	{
		global $user, $in;

		$ip_id = $in->get('ip_id',0);
		$itemp['name'] = $in->get('name','');
		$itemp['desc'] = $in->get('desc','');
		if(empty($itemp['name']))
		{
			$missing[] = $user->lang['name'];
		}
		if(empty($itemp['desc']))
		{
			$missing[] = $user->lang['Multi_discr'];
		}
		if($missing AND !$norefresh)
		{
			$this->update_itempool(array('title' => $user->lang['missing_values'], 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		return $itemp;
	}
}
$manitempools = new Manage_Itempools;
$manitempools->process();
?>