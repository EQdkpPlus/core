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
include_once($eqdkp_root_path . 'common.php');

class Manage_Calevents extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_cal_event_man');
		parent::__construct(false, array(), array('calendar_events', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function delete(){
		$this->pdh->put('calendar_events', 'delete_cevent', array($this->in->getArray('selected_ids', 'int')));
		$this->pdh->process_hook_queue();
		$this->core->message($this->user->lang('del_suc'), $this->user->lang('success'), '');
		$this->display();
	}

	public function display(){
		// The jQuery stuff
		$this->confirm_delete($this->user->lang('confirm_delete_calevents'));
		$this->jquery->Dialog('newCalevent', $this->user->lang('calendar_win_add'), array('url'=> $this->routing->build('editcalendarevent')."&simple_head=true", 'width'=>'900', 'height'=>'580', 'onclose' =>$this->env->link.'admin/manage_calevents.php'.$this->SID));
		$this->jquery->Dialog('editEvent', $this->user->lang('calendar_win_edit'), array('url'=> $this->routing->build('editcalendarevent')."&eventid='+editid+'&simple_head=true", 'width'=>'900', 'height'=>'650', 'withid' => 'editid', 'onclose' => $this->env->link.'admin/manage_calevents.php'.$this->SID));

		// Build the HPTT Table
		$view_list			= $this->pdh->get('calendar_events', 'id_list');
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_calevents', 'hptt_managecalevents_actions');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_calevents.php'));
		$footer_text		= sprintf($this->user->lang('calevents_footcount'), count($view_list));
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= '?sort='.$this->in->get('sort');

		$this->tpl->assign_vars(array(
			'CALEVENTS'			=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), 40, $footer_text),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'PAGINATION' 		=> generate_pagination('manage_calevents.php'.$sort_suffix, count($view_list), 40, $this->in->get('start', 0)),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_calevents'),
			'template_file'		=> 'admin/manage_calevents.html',
			'display'			=> true
		));
	}
}
registry::register('Manage_Calevents');
?>