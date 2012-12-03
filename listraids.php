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

class listraids extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'config', 'core', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_raid_view');
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

		//redirect on member management
		if ( $this->in->exists('manage_b') && $this->in->get('manage_b') == $this->user->lang('manage_raids') ){
			$manage_link	= './admin/listraids.php';
			redirect($manage_link);
		}

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';

		//Output
		$hptt_page_settings	= $this->pdh->get_page_settings('listraids', 'hptt_listraids_raidlist');
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => ''));

		//footer
		$raid_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listraids_footcount'), $raid_count ,$this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'MANAGE_LINK'		=> ($this->user->check_auth('a_raid_', false)) ? '<a href="admin/manage_raids.php'.$this->SID.'" title="'.$this->user->lang('manage_raids').'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="'.$this->user->lang('manage_raids').'" /></a>' : '',
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix.$date_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'RAID_PAGINATION'	=> generate_pagination('listraids.php'.$this->SID.$sort_suffix.$date_suffix, $raid_count, $this->user->data['user_rlimit'], $start),

			// Date Picker
			'DATEPICK_DATE_FROM'		=> $this->jquery->Calendar('from', $this->time->user_date($date1, false, false, false, function_exists('date_create_from_format'))),
			'DATEPICK_DATE_TO'			=> $this->jquery->Calendar('to', $this->time->user_date($date2, false, false, false, function_exists('date_create_from_format')))
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('listraids_title'),
			'template_file'		=> 'listraids.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_listraids', listraids::__shortcuts());
registry::register('listraids');
?>