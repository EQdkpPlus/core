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

class Manage_Itempools extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Itempools', Manage_Itempools::__shortcuts());
registry::register('Manage_Itempools');
?>