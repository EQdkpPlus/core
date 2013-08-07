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
 * $Id: listraids.php 13247 2013-03-25 16:40:09Z godmod $
 */

class raids_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function display(){
		if ($this->in->get('from') && $this->in->get('to')){
			if(!$this->in->exists('timestamps')) {
				$date1 = $this->time->fromformat($this->in->get('from'));
				$date2 = $this->time->fromformat($this->in->get('to'));
				$date2 += 86400; // Includes raids/items ON that day
			} else {
				$date1 = $this->in->get('from');
				$date2 = $this->in->get('to');
			}
			$date_suffix	= '&amp;timestamps=1&amp;from='.$date1.'&amp;to='.$date2;
			$view_list		= $this->pdh->get('raid', 'raididsindateinterval', array($date1, $date2));
			$date2			-= 86400; // Shows THAT day
		}else{
			$view_list		= $this->pdh->get('raid', 'id_list');
			$date1			= $date2 = time();
			$date_suffix	= '';
		}

		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

		//Output
		$hptt_page_settings	= $this->pdh->get_page_settings('listraids', 'hptt_listraids_raidlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->build("raid",false,false,false), '%link_url_suffix%' => '', '%use_controller%' => true));
		$hptt->setPageRef($this->strPath);

		//footer
		$raid_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listraids_footcount'), $raid_count ,$this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix.$date_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'RAID_PAGINATION'	=> generate_pagination($this->strPath.$this->SID.$sort_suffix.$date_suffix, $raid_count, $this->user->data['user_rlimit'], $start),

			// Date Picker
			'DATEPICK_DATE_FROM'		=> $this->jquery->Calendar('from', $this->time->user_date($date1, false, false, false, function_exists('date_create_from_format'))),
			'DATEPICK_DATE_TO'			=> $this->jquery->Calendar('to', $this->time->user_date($date2, false, false, false, function_exists('date_create_from_format')))
		));
		
		$this->jquery->Collapse('#toggleRaidsummary', true);

		$this->set_vars(array(
			'page_title'		=> $this->user->lang('listraids_title'),
			'template_file'		=> 'listraids.html',
			'display'			=> true
		));
	}
}
?>