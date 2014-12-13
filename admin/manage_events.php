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

class Manage_Events extends page_generic {

	public function __construct(){
		$handler = array(
			'save' => array('process' => 'save', 'check' => 'a_event_add', 'csrf' => true),
			'upd'	=> array('process' => 'update', 'csrf'=>false),
		);
		parent::__construct('a_event_', $handler);
		$this->process();
	}

	public function save() {
		$event = $this->get_post();
		if($event) {
			if($event['id']) {
				$retu = $this->pdh->put('event', 'update_event', array($event['id'], $event['name'], $event['value'], $event['icon']));
				if($retu) $this->pdh->put('multidkp', 'add_multidkp2event', array($event['id'], $this->in->getArray('mdkp2event', 'int')));
			} else {
				$retu = $this->pdh->put('event', 'add_event', array($event['name'], $event['value'], $event['icon']));
				if($retu > 0) $this->pdh->put('multidkp', 'add_multidkp2event', array($retu, $this->in->getArray('mdkp2event', 'int')));
			}

			// a quick & dirty hack for the calendar implemention
			if($this->in->get('calendar', 0) > 0){
				$this->pdh->process_hook_queue();
				$this->tpl->add_js("parent.$('body').data('raidevent_id', '".$retu."');", 'docready');
				$this->tpl->add_js('$.FrameDialog.closeDialog();', 'docready');
				$this->update();
			}else{
				// the message
				if($retu) {				
					$message = array('title' => $this->user->lang('save_suc'), 'text' => $event['name'], 'color' => 'green');
				} else {
					$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $event['name'], 'color' => 'red');
				}
				$this->display($message);
			}
		}
	}

	public function delete() {
		$event_id = $this->in->get('event_id',0);
		if($event_id) {
			if($this->pdh->put('event', 'delete_event', array($event_id))) {
				$message = array('title' => $this->user->lang('del_suc'), 'text' => $this->pdh->get('event', 'name', $event_id), 'color' => 'green');
			} else {
				$message = array('title' => $this->user->lang('del_no_suc'), 'text' => $this->pdh->get('event', 'name', $event_id), 'color' => 'red');
			}
		}
		$this->display($message);
	}

	public function update($message=false) {
		$event = array('id' => $this->in->get('event_id',0), 'value' => '0.00', 'mdkp2event' => array());
		if($message) {
			$this->core->messages($message);
			$event = $this->get_post(true);
		} elseif($event['id']) {
			$event['name'] = $this->pdh->get('event', 'name', array($event['id']));
			$event['icon'] = $this->pdh->get('event', 'icon', array($event['id']));
			$event['value'] = $this->pdh->get('event', 'value', array($event['id']));
			$event['mdkp2event'] = $this->pdh->get('event', 'multidkppools', array($event['id']));
		}

		//Get Icons
			$events_folder = $this->pfh->FolderPath('event_icons', 'files');
			$files = scandir($events_folder);
			$ignorefiles = array('.', '..', '.svn', 'index.html', '.tmb');
			
			$icons = array();
			foreach($files as $file) {
				if(!in_array($file, $ignorefiles)) $icons[] = $events_folder.'/'.$file;
			}
			
			$events_folder = $this->root_path.'games/'.$this->config->get('default_game').'/icons/events';
			if (is_dir($events_folder)){
				$files = scandir($events_folder);
				foreach($files as $file) {
					if(!in_array($file, $ignorefiles)) $icons[] = $events_folder.'/'.$file;
				}
			}
			$num = count($icons);
			$fields = (ceil($num/6))*6;
			$i = 0;

			while($i<$fields)
			{
				$this->tpl->assign_block_vars('files_row', array());
				$this->tpl->assign_var('ICONS', true);
				$b = $i+6;
				for($i; $i<$b; $i++)
				{
				$icon = (isset($icons[$i])) ? $icons[$i] : '';
				$this->tpl->assign_block_vars('files_row.fields', array(
						'NAME'		=> pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION),
						'CHECKED'	=> (isset($event['icon']) AND pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION) == $event['icon']) ? ' checked="checked"' : '',
						'IMAGE'		=> "<img src='".$icon."' alt='".$icon."' width='48px' style='eventicon' />",
						'CHECKBOX'	=> ($i < $num) ? true : false)
					);
				}
			}
		
		//Image Uploader
		$this->jquery->fileBrowser('all', 'image', $this->pfh->FolderPath('event_icons','files', 'absolute'), array('title' => $this->user->lang('upload_eventicon'), 'onclosejs' => '$(\'#eventSubmBtn\').click();'));

		$this->confirm_delete(sprintf($this->user->lang('confirm_delete_event'), ((isset($event['name'])) ? $event['name'] : ''), count($this->pdh->get('raid', 'raidids4eventid', array($event['id'])))), 'manage_events.php'.$this->SID.'&event_id='.$event['id'], false, array('height' => 220));

		$this->tpl->assign_vars(array(
			'S_UPD'			=> ($event['id']) ? TRUE : FALSE,
			'EVENT_ID'		=> $event['id'],
			'NAME'			=> (isset($event['name'])) ? $event['name'] : '',
			'VALUE'			=> $this->pdh->geth('event', 'value', array($event['id'])),
			'MDKP2EVENT' 	=> $this->jquery->Multiselect('mdkp2event', $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), $event['mdkp2event']),
			'CALENDAR'		=> ($this->in->get('calendar') == 'true') ? '1' : '0'
		));
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('addevent_title'),
			'template_file'		=> 'admin/manage_events_add.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true)
		);
	}

	public function display($messages=false) {
		$pagination_suffix = '';
		if($messages) {
			$this->pdh->process_hook_queue();
			$this->core->messages($messages);
		}
		$event_ids = $this->pdh->get('event', 'id_list');

		//Sort
		$sort = $this->in->get('sort');
		$sort_suffix = '&amp;sort='.$sort;

		$start = 0;
		if($this->in->exists('start')){
		$start = $this->in->get('start', 0);
		$pagination_suffix = '&amp;start='.$start;
		}

		$hptt_page_settings = $this->pdh->get_page_settings('admin_manage_events', 'hptt_admin_manage_events_eventlist');
		$hptt = $this->get_hptt($hptt_page_settings, $event_ids, $event_ids, array('%link_url%' => 'manage_events.php', '%link_url_suffix%' => '&amp;upd=true'));

		//footer
		$event_count = count($event_ids);
		$footer_text = sprintf($this->user->lang('listevents_footcount'), $event_count ,$this->user->data['user_elimit']);

		$this->tpl->assign_vars(array(
			'ACTION' 	=> 'manage_events.php'.$this->SID,
			'EVENTS_LIST' => $hptt->get_html_table($this->in->get('sort',''), $pagination_suffix, $start, $this->user->data['user_elimit'], $footer_text),
			'EVENT_PAGINATION' => generate_pagination('manage_events.php'.$this->SID.$sort_suffix, $event_count, $this->user->data['user_elimit'], $start),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			)
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manevents_title'),
			'template_file'		=> 'admin/manage_events.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true)
		);
	}

	private function get_post($norefresh=false) {
		$event['name'] = $this->in->get('name','');
		if(empty($event['name']) AND !$norefresh) {
			$this->update(array('title' => $this->user->lang('missing_values'), 'text' => $this->user->lang('name'), 'color' => 'red'));
		}
		$event['value'] = $this->in->get('value', 0.0);
		$event['icon'] = $this->in->get('icon','');
		$event['id'] = $this->in->get('event_id',0);
		$event['mdkp2event'] = $this->in->getArray('mdkp2event', 'int');
		return $event;
	}
}
registry::register('Manage_Events');
?>