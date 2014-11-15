<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

class items_pageobject extends pageobject {

	public function __construct() {
		$handler = array(	
			'i' => array('process' => 'display_item'),
		);
		parent::__construct(false, $handler, array(), null, '', 'i');
		$this->process();
	}
	
	public function display_item(){
		$this->url_id = $this->in->get('i', 0);
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
		if ($this->config->get('itemhistory_dia')){
			$colspan++;
		}
	
		//init infotooltip
		infotooltip_js();

		$hptt_page_settings		= $this->pdh->get_page_settings('viewitem', 'hptt_viewitem_buyerslist');
		$hptt					= $this->get_hptt($hptt_page_settings, $item_ids, $item_ids, array('%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true), $this->url_id);
		$hptt->setPageRef($this->strPath);

		//linechart data
		if($this->config->get('itemhistory_dia') && !$this->config->get('disable_points')) {
			$a_items = array();

			foreach($item_ids as $item_id) {
				$a_items[] = array('name' => $this->time->date("Y-m-d h:i:s", $this->pdh->get('item', 'date', array($item_id))), 'value' => $this->pdh->get('item', 'value', array($item_id)));
			}
		}

		$this->tpl->assign_vars(array(
				'ITEM_STATS'				=> $this->pdh->get('item', 'itt_itemname', array($this->url_id, 0, 1)),
				'ITEM_CHART'				=> ($this->config->get('itemhistory_dia')  && !$this->config->get('disable_points') && count($a_items) > 1) ? $this->jquery->charts('line', 'item_chart', $a_items, array('xrenderer' => 'date', 'autoscale_x' => false, 'autoscale_y' => true, 'height' => 200, 'width' => 500)) : '',
				'SHOW_ITEMSTATS'			=> ($this->config->get('infotooltip_use')) ? true : false,
				'SHOW_ITEMHISTORYA'			=> ($this->config->get('itemhistory_dia')  && !$this->config->get('disable_points') == 1 ) ? true : false,
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

	public function display(){
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

		//Output
		$view_list = $filtered_list	= $this->pdh->get('item', 'id_list');
		if($this->in->exists('search')){
			$mySearch		= $this->in->get('search');
			$searchType		= ($this->in->get('search_type')) ? $this->in->get('search_type') : 'itemname';
			$filtered_list	= $this->filter($view_list, $searchType, $mySearch);
		}

		$item_count		= ((count($filtered_list) > 0) ? count($filtered_list) : count($view_list));
		$footer_text	= sprintf($this->user->lang('listitems_footcount'), $item_count ,$this->user->data['user_ilimit']);

		//init infotooltip
		infotooltip_js();

		$hptt_page_settings		= $this->pdh->get_page_settings('listitems', 'hptt_listitems_itemlist');
		$hptt					= $this->get_hptt($hptt_page_settings, $view_list, $filtered_list, array('%link_url%' => $this->routing->simpleBuild('items') , '%link_url_suffix%' => '', '%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%use_controller%'=>true), md5($searchType.$mySearch));
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array(
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_ilimit'], $footer_text),
			'ITEM_PAGINATION'	=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $item_count, $this->user->data['user_ilimit'], $start),
		));
		
		$this->jquery->Collapse('#toggleItemsearch', true);

		$this->set_vars(array(
			'template_file'		=> 'listitems.html',
			'display'			=> true
		));
	}

	// Search Helper
	function filter($view_list, $searchType, $mySearch ){		
		if(!$mySearch){
			return $view_list;
		}

		$filtered_list	= array();
		$filter_type	= '';
		switch($searchType){
			case 'itemname':	$filter_type = 'name';			break;
			case 'buyer':		$filter_type = 'buyer_name';	break;
			case 'raidname':	$filter_type = 'raid_name';		break;
		}

		// Set the search array
		if($filter_type){
			foreach($view_list as $item_id){
				if(preg_match("/".$mySearch."/i", $this->pdh->get('item', $filter_type, array($item_id)))){
					$filtered_list[] = $item_id;
				}
			}
		}

		return $filtered_list;
	}
}
?>