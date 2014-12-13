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

class events_pageobject extends pageobject {
	public function __construct() {
		$handler = array('e' => array('process' => 'display_event'));
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display_event(){
		$this->url_id = $this->in->get('e', 0);
		
		$isort				= $this->in->get('isort');
		$rsort				= $this->in->get('rsort');
		$ipools				= $this->pdh->get('event', 'itempools', array($this->url_id));
		$raid_hptt_settings	= $this->pdh->get_page_settings('viewevent', 'hptt_viewevent_raidlist');
		$item_hptt_settings	= $this->pdh->get_page_settings('viewevent', 'hptt_viewevent_itemlist');
		$raid_ids			= $this->pdh->get('raid', 'raidids4eventid', array($this->url_id));
		$raid_hptt			= $this->get_hptt($raid_hptt_settings, $raid_ids, $raid_ids, array('%link_url%' => $this->routing->simpleBuild('raids'), '%link_url_suffix%' => '', '%use_controller%' => true), $this->url_id, 'rsort');
		$raid_hptt->setPageRef($this->strPath);
		$item_ids			= $this->pdh->get('item', 'itemids4eventid', array($this->url_id));
		$item_hptt			= $this->get_hptt($item_hptt_settings, $item_ids, $item_ids, array('%link_url%' => $this->routing->simpleBuild('items'), '%link_url_suffix%' => '', '%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%use_controller%' => true), $this->url_id, 'isort');
		$item_hptt->setPageRef($this->strPath);
	
		infotooltip_js();
		$this->tpl->assign_vars(array(
				'RAID_LIST'		=> $raid_hptt->get_html_table($rsort, ''),
				'ITEM_LIST'		=> $item_hptt->get_html_table($isort, ''),
				'EVENT_ICON'	=> $this->game->decorate('events', $this->url_id, array(), 64),
				'EVENT_NAME'	=> $this->pdh->get('event', 'name', array($this->url_id)),
				'MDKPPOOLS'		=> $this->pdh->geth('event', 'multidkppools', array($this->url_id)),
				'ITEMPOOLS'		=> $this->pdh->geth('event', 'itempools', array($this->url_id)),
		));
	
		$this->set_vars(array(
				'page_title'	=> $this->pdh->get('event', 'name', array($this->url_id)),
				'template_file'	=> 'viewevent.html',
				'display'		=> true)
		);
	}
	
	
	public function display(){
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start = 0;
		$pagination_suffix = '';
		if($this->in->exists('start')){
			$start = $this->in->get('start', 0);
			$pagination_suffix	= '&amp;start='.$start;
		}

		//Output
		$view_list			= $this->pdh->get('event', 'id_list');

		//footer
		$event_count		= count($view_list);
		$footer_text		= sprintf($this->user->lang('listevents_footcount'), $event_count ,$this->user->data['user_elimit']);

		$hptt_page_settings	= $this->pdh->get_page_settings('listevents', 'hptt_listevents_eventlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('events'), '%link_url_suffix%' => '', '%use_controller%' => true));
		$hptt->setPageRef($this->strPath);
		
		$this->tpl->assign_vars(array (
			'EVENT_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_elimit'], $footer_text),
			'EVENT_PAGINATION'	=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $event_count, $this->user->data['user_elimit'], $start),
		));

		$this->set_vars(array(
			'template_file'		=> 'listevents.html',
			'display'			=> true
		));
	}
}
?>