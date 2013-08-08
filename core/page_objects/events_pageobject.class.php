<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2013-03-25 17:40:09 +0100 (Mo, 25 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13247 $
 *
 * $Id: listevents.php 13247 2013-03-25 16:40:09Z godmod $
 */

class events_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'config', 'core');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
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
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->build("event",false,false,false), '%link_url_suffix%' => '', '%use_controller%' => true));
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