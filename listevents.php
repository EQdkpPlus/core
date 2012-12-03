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
include_once($eqdkp_root_path . 'common.php');


class listevents extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'config', 'core');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_event_view');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		//redirect on event management
		if ( $this->in->exists('manage_b') && $this->in->get('manage_b') == $this->user->lang('manage_events') ){
			$manage_link	= './admin/listevents.php';
			redirect($manage_link);
		}

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
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewevent.php', '%link_url_suffix%' => ''));

		$this->tpl->assign_vars(array (
			'EVENT_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_elimit'], $footer_text),
			'MANAGE_LINK'		=> ($this->user->check_auth('a_event_', false)) ? '<a href="admin/manage_events.php'.$this->SID.'" title="'.$this->user->lang('manevents_title').'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="'.$this->user->lang('manevents_title').'" /></a>' : '',
			'EVENT_PAGINATION'	=> generate_pagination('listevents.php'.$this->SID.$sort_suffix, $event_count, $this->user->data['user_elimit'], $start),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('events'),
			'template_file'		=> 'listevents.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_listevents', listevents::__shortcuts());
registry::register('listevents');
?>