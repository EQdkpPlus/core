<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path.'common.php');

class Manage_Multidkp extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config');
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
		$mdkp_id = $this->in->get('mdkp_id',0);
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

	public function update($message=false) {
		$mdkp_id = $this->in->get('id',0);
		$mdkp = array('events' => array(), 'itempools' => array(), 'no_attendance' => array());
		if($message) {
			$this->core->messages($message);
			$this->pdh->process_hook_queue();
			$mdkp = $this->get_post(true);
		} else {
			$mdkp['name'] = $this->pdh->get('multidkp', 'name', array($mdkp_id));
			$mdkp['desc'] = $this->pdh->get('multidkp', 'desc', array($mdkp_id));
			$mdkp['events'] = $this->pdh->get('multidkp', 'event_ids', array($mdkp_id, true));
			$mdkp['itempools'] = $this->pdh->get('multidkp', 'itempool_ids', array($mdkp_id));
			$mdkp['no_attendance'] = $this->pdh->get('multidkp', 'no_attendance', array($mdkp_id));
		}

		//events
		$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));

		//itempools
		$itempools = $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));

		$this->confirm_delete($this->user->lang('confirm_delete_multi').'<br />'.$mdkp['name']);
		$this->tpl->assign_vars(array(
			'NAME'					=> $mdkp['name'],
			'DESC'					=> $mdkp['desc'],
			'EVENT_SEL'				=> $this->jquery->MultiSelect('events', $events, $mdkp['events'], array('width' => 300, 'filter' => true)),
			'ITEMPOOL_SEL'			=> $this->jquery->MultiSelect('itempools', $itempools, $mdkp['itempools'], array('width' => 300)),
			'NO_ATT_SEL'			=> $this->jquery->MultiSelect('no_atts', $events, $mdkp['no_attendance'], array('width' => 300, 'filter' => true)),
			'MDKP_ID'				=> $mdkp_id,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manmdkp_title'),
			'template_file'		=> 'admin/manage_multidkp_add.html',
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
		$mdkp_ids = $this->pdh->get('multidkp', 'id_list');
		$mdkp = array();
		foreach($mdkp_ids as $id)
		{
			$mdkp[$id]['name'] = $this->pdh->get('multidkp', 'name', $id);
			$mdkp[$id]['desc'] = $this->pdh->get('multidkp', 'desc', $id);
			$mdkp[$id]['events'] = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('multidkp', 'event_ids', $id)));
			$mdkp[$id]['no_atts'] = $this->pdh->get('multidkp', 'no_attendance', array($id));
			$ip_ids = $this->pdh->get('multidkp', 'itempool_ids', $id);
			$mdkp[$id]['itempools'] = $this->pdh->aget('itempool', 'name', 0, array(((is_array($ip_ids)) ? $ip_ids : array())));
		}

		$sort_ids = get_sortedids($mdkp, explode('.', $order), array('name', 'desc'));
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
			'LISTMULTI_FOOTCOUNT'	=> sprintf($this->user->lang('multi_footcount'), count($sort_ids)),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manmdkp_title'),
			'template_file'		=> 'admin/manage_multidkp.html',
			'display'			=> true)
		);
	}

	private function get_post($norefresh=false) {
		$mdkp['name'] = $this->in->get('name','');
		$mdkp['desc'] = $this->in->get('desc','');
		$mdkp['events'] = $this->in->getArray('events','int');
		if(!$mdkp['name']) {
			$missing[] = $this->user->lang('Multi_kontoname_short');
		}
		if(!$mdkp['desc']) {
			$missing[] = $this->user->lang('description');
		}
		if(!$mdkp['events']) {
			$missing[] = $this->user->lang('events');
		}
		if(isset($missing) AND !$norefresh) {
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => implode(', ', $missing), 'color' => 'red'));
		}
		$mdkp['itempools'] = $this->in->getArray('itempools','int');
		$mdkp['no_attendance'] = $this->in->getArray('no_atts', 'int');
		return $mdkp;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Multidkp', Manage_Multidkp::__shortcuts());
registry::register('Manage_Multidkp');
?>