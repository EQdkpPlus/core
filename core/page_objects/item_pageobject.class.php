<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date: 2013-02-12 15:22:43 +0100 (Di, 12 Feb 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13064 $
 *
 * $Id: viewitem.php 13064 2013-02-12 14:22:43Z wallenium $
 */

class item_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'pm', 'time', 'comments'	=> 'comments');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array(), null, '', 'i');
		if(!$this->url_id){
			redirect($this->routing->build('items',false,false,true,true));
		}
		$this->process();
	}

	public function display(){
		// We want to view items by name and not id, so get the name
		$item_name = $this->pdh->get('item', 'name', array($this->url_id));

		if ( empty($item_name) ){
			message_die($this->user->lang('error_invalid_item_provided'));
		}

		#search for the gameid
		$game_id = $this->pdh->get('item', 'game_itemid', array($this->url_id));

		//Sort
		$sort			= $this->in->get('sort');

		$item_ids = array();
		if ($game_id > 1){
			$item_ids = $this->pdh->get('item', 'ids_by_ingameid', array($game_id));
		}else{
			$item_ids = $this->pdh->get('item', 'ids_by_name', array($item_name));
		}
		$counter = sizeof($item_ids);

		//default now col
		$colspan = ($this->config->get('infotooltip_use')) ? 1 : 0 ;

		#Itemhistory Diagram
		if ($this->config->get('pk_itemhistory_dia')){
			$colspan++;
		}

		//init infotooltip
		infotooltip_js();

		$hptt_page_settings		= $this->pdh->get_page_settings('viewitem', 'hptt_viewitem_buyerslist');
		$hptt					= $this->get_hptt($hptt_page_settings, $item_ids, $item_ids, array('%raid_link_url%' => $this->routing->build('raid', false, false, false), '%raid_link_url_suffix%' => '', '%use_controller%' => true), $this->url_id);
		$hptt->setPageRef($this->strPath);
		
		//linechart data
		if($this->config->get('pk_itemhistory_dia')) {
			$a_items = array();
			foreach($item_ids as $item_id) {
				$a_items[] = array('name' => $this->time->date("Y-m-d H:i:s", $this->pdh->get('item', 'date', array($item_id))), 'value' => $this->pdh->get('item', 'value', array($item_id)));
			}
		}

		$this->tpl->assign_vars(array(
			'ITEM_STATS'				=> $this->pdh->get('item', 'itt_itemname', array($this->url_id, 0, 1)),
			'ITEM_CHART'				=> ($this->config->get('pk_itemhistory_dia') && count($a_items) > 1) ? $this->jquery->charts('line', 'item_chart', $a_items, array('xrenderer' => 'date', 'autoscale_x' => false, 'autoscale_y' => true, 'height' => 200, 'width' => 500)) : '',

			'SHOW_ITEMSTATS'			=> ($this->config->get('infotooltip_use')) ? true : false,
			'SHOW_ITEMHISTORYA'			=> ($this->config->get('pk_itemhistory_dia') == 1 ) ? true : false,
			'SHOW_COLSPAN'				=> $colspan,
			'BUYERS_TABLE'				=> $hptt->get_html_table($sort, '', 0, 100, sprintf($this->user->lang('viewitem_footcount'), $counter)),
			'L_PURCHASE_HISTORY_FOR'	=> sprintf($this->user->lang('purchase_history_for'), stripslashes($item_name)),
		));

		$this->set_vars(array(
			'page_title'		=> $item_name,
			'template_file'		=> 'viewitem.html',
			'display'			=> true)
		);
	}
}

?>