<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

class event_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'config', 'core');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();

		parent::__construct(false, $handler, array(), null, '', 'event_id');

		if(!in_array($this->url_id, $this->pdh->get('event', 'id_list', array()))) {
			redirect($this->routing->build('events',false,false,true,true));
		}
		$this->process();
	}

	public function display(){
		$isort				= $this->in->get('isort');
		$rsort				= $this->in->get('rsort');
		$ipools				= $this->pdh->get('event', 'itempools', array($this->url_id));
		$raid_hptt_settings	= $this->pdh->get_page_settings('viewevent', 'hptt_viewevent_raidlist');
		$item_hptt_settings	= $this->pdh->get_page_settings('viewevent', 'hptt_viewevent_itemlist');
		$raid_ids			= $this->pdh->get('raid', 'raidids4eventid', array($this->url_id));
		$raid_hptt			= $this->get_hptt($raid_hptt_settings, $raid_ids, $raid_ids, array('%link_url%' => $this->routing->simpleBuild('raid'), '%link_url_suffix%' => '', '%use_controller%' => true), $this->url_id, 'rsort');
		$raid_hptt->setPageRef($this->strPath);
		$item_ids			= $this->pdh->get('item', 'itemids4eventid', array($this->url_id));
		$item_hptt			= $this->get_hptt($item_hptt_settings, $item_ids, $item_ids, array('%link_url%' => $this->routing->simpleBuild('item'), '%link_url_suffix%' => '', '%raid_link_url%' => $this->routing->simpleBuild('raid'), '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%use_controller%' => true), $this->url_id, 'isort');
		$item_hptt->setPageRef($this->strPath);
		
		infotooltip_js();
		$this->tpl->assign_vars(array(
			'RAID_LIST'		=> $raid_hptt->get_html_table($rsort, ''),
			'ITEM_LIST'		=> $item_hptt->get_html_table($isort, ''),
			'EVENT_ICON'	=> $this->game->decorate('events', array($this->url_id, 64)),
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
}
?>