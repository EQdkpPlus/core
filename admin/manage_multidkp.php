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

class Manage_Multidkp extends EQdkp_Admin
{
	function Manage_Multidkp()
	{
		global $core;

		parent::eqdkp_admin();

		$this->assoc_params(array(
			'update' => array(
				'name' => 'update',
				'process' => 'update_mdkp',
				'check' => 'a_event_add')
			)
		);

		$this->assoc_buttons(array(
			'save' => array(
				'name' => 'save',
				'process' => 'save_mdkp',
				'check' => 'a_event_add'),
			'del' => array(
				'name' => 'delete',
				'process' => 'delete_mdkp',
				'check' => 'a_event_del'),
			'confirm' => array(
				'name' => 'confirm',
				'process' => 'delete_mdkp',
				'check' => 'a_event_del'),
			'form' => array(
				'name' => '',
				'process' => 'display_form',
				'check' => 'a_event_upd')
			)
		);
	}

	function save_mdkp()
	{
		global $user, $pdh,$in;

		$mdkp_id = $in->get('mdkp_id',0);
		$mdkp = $this->get_post();
		if($mdkp)
		{
			if($mdkp_id)
			{
				$retu = $pdh->put('multidkp', 'update_multidkp', array($mdkp_id, $mdkp['name'], $mdkp['desc'], $mdkp['events'], $mdkp['itempools']));
			}
			else
			{
				$retu = $pdh->put('multidkp', 'add_multidkp', array($mdkp['name'], $mdkp['desc'], $mdkp['events'], $mdkp['itempools']));
			}
			if(!$retu)
			{
				$message = array('title' => $user->lang['save_nosuc'], 'text' => $mdkp['name'], 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['save_suc'], 'text' => $mdkp['name'], 'color' => 'green');
			}
		}
		$this->display_form($message);
	}

	function delete_mdkp()
	{
		global $user, $pdh, $in;

		$mdkp_id = $in->get('mdkp_id',0);
		if($mdkp_id AND !isset($_POST['confirm']))
		{
			confirm_delete($user->lang['confirm_delete_multi'].'<br />'.$in->get('name',''), 'mdkp_id', $mdkp_id);
		}
		elseif($mdkp_id AND $_POST['confirm'] == $user->lang['yes'])
		{
			$name = $pdh->get('multidkp', 'name', $mdkp_id);
			if(!$pdh->put('multidkp', 'delete_multidkp', array($mdkp_id)))
			{
				$message = array('title' => $user->lang['del_nosuc'], 'text' => $name, 'color' => 'red');
			}
			else
			{
				$message = array('title' => $user->lang['del_suc'], 'text' => $name, 'color' => 'green');
			}
		}
		$this->display_form($message);
	}

	function update_mdkp($message=false)
	{
		global $core, $user, $tpl, $pdh, $jquery, $in;

		$mdkp_id = $in->get('id',0);
		$mdkp = array('events' => array(), 'itempools' => array());
		if($message)
		{
			$core->messages($message);
			$pdh->process_hook_queue();
			$mdkp = $this->get_post(true);
		}
		else
		{
			$mdkp['name'] = $pdh->get('multidkp', 'name', array($mdkp_id));
			$mdkp['desc'] = $pdh->get('multidkp', 'desc', array($mdkp_id));
			$mdkp['events'] = $pdh->get('multidkp', 'event_ids', array($mdkp_id));
			$mdkp['itempools'] = $pdh->get('multidkp', 'itempool_ids', array($mdkp_id));
		}

		//events
		$events = $pdh->aget('event', 'name', 0, array($pdh->get('event', 'id_list')));

		//itempools
		$itempools = $pdh->aget('itempool', 'name', 0, array($pdh->get('itempool', 'id_list')));

		$tpl->assign_vars(array(
			'NAME'					=> $mdkp['name'],
			'DESC'					=> $mdkp['desc'],
			'EVENT_SEL'				=> $jquery->MultiSelect('events', $events, $mdkp['events'], 200, 500),
			'ITEMPOOL_SEL'			=> $jquery->MultiSelect('itempools', $itempools, $mdkp['itempools'], 200, 500),
			'MDKP_ID'				=> $mdkp_id,
			//language
            'L_ADD_MULTI_TITLE'		=> $user->lang['Multi_addkonto'],
            'L_EVENTS'				=> $user->lang['Multi_chooseevents'],
            'L_ITEMPOOLS'			=> $user->lang['Multi_chooseitempools'],
            'L_KONTONAME'			=> $user->lang['Multi_kontoname_short'],
            'L_KONTONAMENOTTOLONG'	=> $user->lang['Multi_discnottolong'],
            'L_DESC'				=> $user->lang['Multi_discr'],
            'L_SAVE'				=> $user->lang['save'],
						'L_CANCEL'				=> $user->lang['cancel'],
            'L_DELETE'				=> $user->lang['delete'])
        );

		$core->set_vars(array(
            'page_title'    => $user->lang['manmdkp_title'],
            'template_file' => 'admin/manage_multidkp_add.html',
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
		$mdkp_ids = $pdh->get('multidkp', 'id_list');
		foreach($mdkp_ids as $id)
		{
			$mdkp[$id]['name'] = $pdh->get('multidkp', 'name', $id);
			$mdkp[$id]['desc'] = $pdh->get('multidkp', 'desc', $id);
			$mdkp[$id]['events'] = $pdh->aget('event', 'name', 0, array($pdh->get('multidkp', 'event_ids', $id)));
			$ip_ids = $pdh->get('multidkp', 'itempool_ids', $id);
			$mdkp[$id]['itempools'] = $pdh->aget('itempool', 'name', 0, array(((is_array($ip_ids)) ? $ip_ids : array())));
		}

		$sort_ids = get_sortedids($mdkp, explode('.', $order), array('name', 'desc'));

		foreach($sort_ids as $id)
		{
			$tpl->assign_block_vars('multi_row', array(
				'ID'		=> $id,
				'NAME'		=> $mdkp[$id]['name'],
				'DESC'		=> $mdkp[$id]['desc'],
				'EVENTS'	=> implode(', ', $mdkp[$id]['events']),
				'ITEMPOOLS'	=> implode(', ', $mdkp[$id]['itempools']),
				'ROW_CLASS'	=> $core->switch_row_class())
			);
		}

		$tpl->assign_vars(array(
			'SID'	=> $SID,
			$red 	=> '_red',
			//language
			'L_NAME'	=> $user->lang['Multi_kontoname_short'],
			'L_DESC'	=> $user->lang['Multi_discr'],
			'L_EVENTS'	=> $user->lang['Multi_events'],
			'LISTMULTI_FOOTCOUNT'	=> sprintf($user->lang['multi_footcount'], count($sort_ids)),
			'L_MULTIDKPS' => $user->lang['manmdkp_title'],
			'L_ITEMPOOLS' => $user->lang['menu_itempools'],
			'L_ADD_MULTI' => $user->lang['Multi_addkonto'])
		);

		$core->set_vars(array(
            'page_title'    => $user->lang['manmdkp_title'],
            'template_file' => 'admin/manage_multidkp.html',
            'display'       => true)
        );
	}

	function get_post($norefresh=false)
	{
		global $user, $in;
        $mdkp['name'] = $in->get('name','');
        $mdkp['desc'] = $in->get('desc','');
        $mdkp['events'] = $in->getArray('events','int');
		if(!$mdkp['name'])
		{
			$missing[] = $user->lang['Multi_kontoname_short'];
		}
		if(!$mdkp['desc'])
		{
			$missing[] = $user->lang['Multi_discr'];
		}
		if(!$mdkp['events'])
		{
			$missing[] = $user->lang['events'];
		}
		if($missing AND !$norefresh)
		{
			$this->update_mdkp(array('title' => $user->lang['missing_values'], 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		$mdkp['itempools'] = $in->getArray('itempools','int');
		return $mdkp;
	}
}
$manmulti = new Manage_Multidkp;
$manmulti->process();
?>