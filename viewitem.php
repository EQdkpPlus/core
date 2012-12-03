<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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
include_once($eqdkp_root_path . 'common.php');

class viewitem extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'pm', 'time', 'comments'	=> 'comments');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_item_view');
		parent::__construct(false, $handler, array(), null, '', 'i');
		if(!$this->url_id){
			message_die($this->user->lang('error_invalid_item_provided'));
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

		//Comments
		$comm_settings = array('attach_id'=>md5(stripslashes($item_name)), 'page'=>'items');
		$this->comments->SetVars($comm_settings);
		$COMMENT = ($this->config->get('pk_enable_comments') == 1) ? $this->comments->Show() : '';

		//init infotooltip
		infotooltip_js();

		$hptt_page_settings		= $this->pdh->get_page_settings('viewitem', 'hptt_viewitem_buyerslist');
		$hptt					= $this->get_hptt($hptt_page_settings, $item_ids, $item_ids, array('%raid_link_url%' => 'viewraid.php', '%raid_link_url_suffix%' => ''), $this->url_id);

		//linechart data
		if($this->config->get('pk_itemhistory_dia')) {
			$a_items = array();
			foreach($item_ids as $item_id) {
				$a_items[] = array('name' => $this->time->date("Y-m-d H:i:s", $this->pdh->get('item', 'date', array($item_id))), 'value' => $this->pdh->get('item', 'value', array($item_id)));
			}
		}
		$this->tpl->assign_vars(array(
			'ITEM_STATS'				=> $this->pdh->get('item', 'itt_itemname', array($this->url_id, 0, 1)),
			'ITEM_CHART'				=> ($this->config->get('pk_itemhistory_dia') && count($a_items) > 1) ? $this->jquery->LineChart('item_chart', $a_items, '', 200, 500, '', false, true, 'date') : '',
			'ITEM_MODEL'				=> (isset($model3d)) ? $model3d : false,
			'COMMENT'					=> $COMMENT,

			'SHOW_ITEMSTATS'			=> ($this->config->get('infotooltip_use')) ? true : false,
			'SHOW_ITEMHISTORYA'			=> ($this->config->get('pk_itemhistory_dia') == 1 ) ? true : false,
			'SHOW_COLSPAN'				=> $colspan,
			'BUYERS_TABLE'				=> $hptt->get_html_table($sort, '&amp;i='.$this->url_id, 0, 100, sprintf($this->user->lang('viewitem_footcount'), $counter)),
			'L_PURCHASE_HISTORY_FOR'	=> sprintf($this->user->lang('purchase_history_for'), stripslashes($item_name)),
		));

		$this->core->set_vars(array(
			'page_title'		=> sprintf($this->user->lang('viewitem_title'), stripslashes($item_name)),
			'template_file'		=> 'viewitem.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_viewitem', viewitem::__shortcuts());
registry::register('viewitem');
?>