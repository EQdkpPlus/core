<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

class Manage_Ranks extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_members_man');
		$handler = array(
			'save' => array('process' => 'save','csrf'=>true),
			'r' => array('process' => 'edit'),
		);
		parent::__construct(false, $handler, array('rank', 'name'), null, 'rank_ids[]');
		$this->process();
	}
	
	//Save or add rank on edit page
	public function update(){
		$strName = $this->in->get('name');
		$strPrefix = $this->in->get('prefix');
		$strSuffix = $this->in->get('suffix');
		$intHidden = $this->in->get('hidden', 0);
		$strIcon = $this->in->get('icon');
		
		$intRankID = $this->in->get('r', -1);
		if ($intRankID >= 0){
			//Update rank
			$intSortID = $this->pdh->get('rank', 'sortid', array($intRankID));
			$intDefault = $this->pdh->get('rank', 'default_value', array($intRankID));
			
			$result = $this->pdh->put('rank', 'update_rank', array($intRankID, $strName, $intHidden, $strPrefix, $strSuffix, $intSortID, $intDefault, $strIcon));
			
		} else {
			//Add new rank
			$id_list = $this->pdh->get('rank', 'id_list');
			$intSortID = count($id_list) + 1;
			$intDefault = 0;
			$intRankID = max($id_list) + 1;
			$result = $this->pdh->put('rank', 'add_rank', array($intRankID, $strName, $intHidden, $strPrefix, $strSuffix, $intSortID, $intDefault, $strIcon));
		}
		
		if($result) {
			$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('pk_succ_saved'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $strName, 'color' => 'red');
		}
		$this->display($message);
	}

	//Save Sorting and Default Rank on display page
	public function save() {
		$noranks = false;
		$retu = array();
		$ranks = $this->get_post();
		if($ranks) {
			foreach($ranks as $rank) {
				$retu[] = $this->pdh->put('rank', 'set_standardAndSort', array($rank['id'], $rank['default'], $rank['sortid']));
				$names[] = $this->pdh->get('rank', 'name', array($rank['id']));
			}
			if(in_array(false, $retu)) {
				$message = array('title' => $this->user->lang('save_nosuc'), 'text' => implode(', ', $names), 'color' => 'red');
			} elseif(in_array(true, $retu)) {
				$message = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('pk_succ_saved'), 'color' => 'green');
			}
		} else {
			$message = array('title' => '', 'text' => $this->user->lang('no_ranks_selected'), 'color' => 'grey');
		}
		$this->display($message);
	}
	
	public function edit(){
		$intRankID = $this->in->get('r', -1);
		
		$arrRankImagesDD = array('' => '');
		$blnRankImages = $this->game->icon_exists('ranks');
		if($blnRankImages){
			$arrRankImages = sdir($this->root_path.'games/'.$this->game->get_game().'/icons/ranks');
			foreach($arrRankImages as $strRankImage){
				$arrRankImagesDD[$strRankImage] = $strRankImage;
			}
		}
		natcasesort($arrRankImagesDD);
		
		if($intRankID >= 0){
			
			
			$this->tpl->assign_vars(array(
					'NAME'				=> $this->pdh->get('rank', 'name', array($intRankID)),
					'RANK_ICON_DD' 		=> new hdropdown('icon', array('options' => $arrRankImagesDD, 'value' => $this->pdh->get('rank', 'icon', array($intRankID)))),
					'HIDE'				=> ($this->pdh->get('rank', 'is_hidden', array($intRankID))) ? 'checked="checked"' : "",
					'PREFIX' 			=> $this->pdh->get('rank', 'prefix', array($intRankID)),
					'SUFFIX' 			=> $this->pdh->get('rank', 'suffix', array($intRankID)),
			));
		} else {
			$this->tpl->assign_vars(array(
					'RANK_ICON_DD' 		=> new hdropdown('icon', array('options' => $arrRankImagesDD)),
			));
		}
		
		$this->tpl->assign_vars(array(
				'RANK_ID' 		=> $intRankID,
				'S_RANK_IMAGES' => $blnRankImages,
		));
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('manrank_title'),
				'template_file'		=> 'admin/manage_ranks_edit.html',
				'display'			=> true)
		);
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
				cancel: '.not-sortable, input, select, th',
				cursor: 'pointer',
			});
		", "docready");

		$ranks = $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));

		$key = 0;
		$new_id = 1;
		
		$default_rank = $this->pdh->get('rank', 'default');
		
		$arrRankImagesDD = array('' => '');
		$blnRankImages = $this->game->icon_exists('ranks');
		if($blnRankImages){
			$arrRankImages = sdir($this->root_path.'games/'.$this->game->get_game().'/ranks');
			foreach($arrRankImages as $strRankImage){
				$arrRankImagesDD[$strRankImage] = $strRankImage;
			}
		}
		natcasesort($arrRankImagesDD);
		
		foreach($ranks as $id => $name) {
			$this->tpl->assign_block_vars('ranks', array(
				'KEY'	=> $key,
				'ID'	=> $id,
				'NAME'	=> $name,
				'HIDE'	=> ($this->pdh->get('rank', 'is_hidden', array($id))) ? $this->user->lang('yes') : $this->user->lang('no'),
				'PREFIX' => $this->pdh->get('rank', 'prefix', array($id)),
				'SUFFIX' => $this->pdh->get('rank', 'suffix', array($id)),
				'DEFAULT' => ($id == $default_rank) ? 'checked="checked"' : '',
				'ICON' => $this->pdh->geth('rank', 'icon', array($id)),
			));
			$key++;

		}
		$this->confirm_delete($this->user->lang('confirm_delete_ranks'));
		$this->jquery->selectall_checkbox('selall_ranks', 'rank_ids[]');
		$this->tpl->assign_vars(array(
			'S_RANK_IMAGES' => $blnRankImages,
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
			$rank_default = $this->in->get('ranks_default', 0);
			foreach($this->in->getArray('ranks', 'string') as $key => $id) {			
				$ranks[] = array(
					'selected'	=> (in_array($id, $selected)) ? $id : false,
					'id'		=> $id,
					'sortid'	=> $sortid,
					'default'	=> ($rank_default == $id),
				);
				$sortid = $sortid + 1;
			}
			return $ranks;
		}
		return false;
	}
}
registry::register('Manage_Ranks');
?>