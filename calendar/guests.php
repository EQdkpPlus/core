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
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

class calraids_guests extends page_generic {
	public static $shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'core', 'html');

	public function __construct() {
		$handler = array();
		$this->user->check_auth('u_cal_event_add');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function add(){
		if($this->in->get('membername')){
			if($this->in->get('guestid', 0) > 0){
				$blub = $this->pdh->put('calendar_raids_guests', 'update_guest', array(
					$this->in->get('guestid', 0), $this->in->get('class'), $this->in->get('group'), $this->in->get('note')
				));
			}else{
				$blub = $this->pdh->put('calendar_raids_guests', 'insert_guest', array(
					$this->in->get('eventid', 0), $this->in->get('membername'), $this->in->get('class'), $this->in->get('group'), $this->in->get('note')
				));
			}
		}
		$this->pdh->process_hook_queue();
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	public function display(){
		$guestdata = ($this->in->get('guestid', 0) > 0) ? $this->pdh->get('calendar_raids_guests', 'guest', array($this->in->get('guestid', 0))) : array();
		$this->tpl->assign_vars(array(
			'S_ADD'			=> true,
			'EVENT_ID'		=> $this->in->get('eventid', 0),
			'GUEST_ID'		=> $this->in->get('guestid', 0),
			'CLASS_DD'		=> $this->html->DropDown('class', $this->game->get('classes', array('id_0')), ((isset($guestdata['class'])) ? $guestdata['class'] : '')),
	
			// the edit input
			'MEMBER_NAME'	=> (isset($guestdata['name'])) ? sanitize($guestdata['name']) : '',
			'NOTE'			=> (isset($guestdata['note'])) ? sanitize($guestdata['note']) : '',
		));
	
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'calendar/guests.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_calraids_guests', calraids_guests::$shortcuts);
registry::register('calraids_guests');
?>