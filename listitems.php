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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once ($eqdkp_root_path . 'common.php');

class listitems extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'config', 'core');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_item_view');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		//redirect on member management
		if($this->in->exists('manage_b') && $this->in->get('manage_b') == $this->user->lang('manage_items')){
			$manage_link = './admin/listitems.php';
			redirect($manage_link);
		}

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
		$hptt					= $this->get_hptt($hptt_page_settings, $view_list, $filtered_list, array('%link_url%' => 'viewitem.php', '%link_url_suffix%' => '', '%raid_link_url%' => 'viewraid.php', '%raid_link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0));
		$this->tpl->assign_vars(array(
			'SEARCH_LINK'		=> '<input type="image" src="'.$this->root_path.'images/glyphs/view.png" name="search_b" value="1" alt="'.$this->user->lang('Itemsearch_searchby').'" title="'.$this->user->lang('Itemsearch_searchby').'" class="absmiddle" />',
			'MANAGE_LINK'		=> ($this->user->check_auth('a_item_', false)) ? '<a href="admin/manage_items.php'.$this->SID.'" title="'.$this->user->lang('manage_items').'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="'.$this->user->lang('manage_items').'" /></a>' : '',
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_ilimit'], $footer_text),
			'ITEM_PAGINATION'	=> generate_pagination('listitems.php'.$this->SID.$sort_suffix, $item_count, $this->user->data['user_ilimit'], $start),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('listitems_title'),
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_listitems', listitems::__shortcuts());
registry::register('listitems');
?>