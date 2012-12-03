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

class Manage_Ranks extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_members_man');
		$handler = array(
			'save' => array('process' => 'save','csrf'=>true),
		);
		parent::__construct(false, $handler, array('rank', 'name'), null, 'rank_ids[]');
		$this->process();
	}

	public function save() {
		$noranks = false;
		$retu = array();
		$ranks = $this->get_post();
		if($ranks) {
			$id_list = $this->pdh->get('rank', 'id_list');
			foreach($ranks as $rank) {
				$func = (in_array($rank['id'], $id_list)) ? 'update_rank' : 'add_rank';
				$retu[] = $this->pdh->put('rank', $func, array($rank['id'], $rank['name'], $rank['hide'], $rank['prefix'], $rank['suffix'], $rank['sortid']));
				$names[] = $rank['name'];
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_ranks_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function delete() {
		$noranks = false;
		$rank_ids = $this->in->getArray('rank_ids', 'int');
		if($rank_ids) {
			foreach($rank_ids as $id) {
				$names[] = $this->pdh->get('rank', 'name', ($id));
				$retu[] = $this->pdh->put('rank', 'delete_rank', array($id));
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $names), 'color' => 'red');
			} else {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $names), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_ranks_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}

	public function display($messages=false) {
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		
		$this->tpl->add_js("
			$(\"#rank_table tbody\").sortable({
				cancel: '.not-sortable, input',
				cursor: 'pointer',
			});
		", "docready");

		$ranks = $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));

		$key = 0;
		$new_id = 1;

		
		foreach($ranks as $id => $name) {
			$this->tpl->assign_block_vars('ranks', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'HIDE'	=> ($this->pdh->get('rank', 'is_hidden', array($id))) ? 'checked="checked"' : '',
				'PREFIX' => $this->pdh->get('rank', 'prefix', array($id)),
				'SUFFIX' => $this->pdh->get('rank', 'suffix', array($id)),
			));
			$key++;
			$new_id = ($new_id == $id) ? $id+1 : $new_id;
		}
		$this->confirm_delete($this->user->lang('confirm_delete_ranks'));
		$this->jquery->selectall_checkbox('selall_ranks', 'rank_ids[]');
		$this->tpl->assign_vars(array(
			'SID'		=> $this->SID,
			'ID'		=> $new_id,
			'KEY'		=> $key,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manrank_title'),
			'template_file'		=> 'admin/manage_ranks.html',
			'display'			=> true)
		);
	}

	private function get_post() {
		$ranks = array();
		$selected = $this->in->getArray('rank_ids', 'int');
		if($this->in->exists('ranks', 'string')) {
			$sortid = 0;
			foreach($this->in->getArray('ranks', 'string') as $key => $rank) {			
				if( isset($rank['id']) && ((intval($rank['id']) == 0) && ($rank['name'] == '') || ($rank['name'] != '')) ) {
					$ranks[] = array(
						'selected'	=> (in_array($rank['id'], $selected)) ? $rank['id'] : false,
						'id'		=> $this->in->get('ranks:'.$key.':id',0),
						'name'		=> $this->in->get('ranks:'.$key.':name',''),
						'hide'		=> $this->in->get('ranks:'.$key.':hide',0),
						'prefix'	=> $this->in->get('ranks:'.$key.':prefix',''),
						'suffix'	=> $this->in->get('ranks:'.$key.':suffix',''),
						'sortid'	=> $sortid,
					);
					$sortid = $sortid + 1;
				}
			}
			return $ranks;
		}
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Ranks', Manage_Ranks::__shortcuts());
registry::register('Manage_Ranks');
?>