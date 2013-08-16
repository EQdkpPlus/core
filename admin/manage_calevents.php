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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Calevents extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'env');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_cal_event_man');
		parent::__construct(false, array(), array('calendar_events', 'name'), null, 'selected_ids[]');
		$this->process();
	}

	public function delete(){
		$this->pdh->put('calendar_events', 'delete_cevent', array($this->in->getArray('selected_ids', 'int')));
		$this->pdh->process_hook_queue();
		$this->core->message($this->user->lang('del_suc'), $this->user->lang('success'), '');
		$this->display();
	}

	public function display(){
		// The jQuery stuff
		$this->confirm_delete($this->user->lang('confirm_delete_calevents'));
		$this->jquery->Dialog('newCalevent', $this->user->lang('calendar_win_add'), array('url'=>"../calendar/addevent.php".$this->SID."&simple_head=true", 'width'=>'900', 'height'=>'580', 'onclose' =>$this->env->link.'admin/manage_calevents.php'.$this->SID));
		$this->jquery->Dialog('editEvent', $this->user->lang('calendar_win_edit'), array('url'=>"../calendar/addevent.php".$this->SID."&eventid='+editid+'&simple_head=true", 'width'=>'900', 'height'=>'650', 'withid' => 'editid', 'onclose' => $this->env->link.'admin/manage_calevents.php'.$this->SID));

		// Build the HPTT Table
		$view_list			= $this->pdh->get('calendar_events', 'id_list');
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_calevents', 'hptt_managecalevents_actions');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_calevents.php'));
		$footer_text		= sprintf($this->user->lang('calevents_footcount'), count($view_list));
		$page_suffix		= '&amp;start='.$this->in->get('start', 0);
		$sort_suffix		= '?sort='.$this->in->get('sort');

		$this->tpl->assign_vars(array(
			'CALEVENTS'			=> $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), 40, $footer_text),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
			'PAGINATION' 		=> generate_pagination('manage_calevents.php'.$sort_suffix, count($view_list), 40, $this->in->get('start', 0)),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_calevents'),
			'template_file'		=> 'admin/manage_calevents.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Calevents', Manage_Calevents::__shortcuts());
registry::register('Manage_Calevents');
?>