<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class calendareventguests_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->process();
	}

	private function is_raidleader(){
		$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($this->in->get('eventid', 0)));
		$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
		$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
		return (in_array($this->user->data['user_id'], $raidleaders_users)) ? true : false;
	}

	public function add(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()){
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
	}

	public function display(){
		$guestdata = ($this->in->get('guestid', 0) > 0) ? $this->pdh->get('calendar_raids_guests', 'guest', array($this->in->get('guestid', 0))) : array();
		$this->tpl->assign_vars(array(
			'S_ADD'		=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()) ? true : false,
			'EVENT_ID'		=> $this->in->get('eventid', 0),
			'GUEST_ID'		=> $this->in->get('guestid', 0),
			'CLASS_DD'		=> new hdropdown('class', array('options' => $this->game->get_primary_classes(array('id_0')), 'value' => ((isset($guestdata['class'])) ? $guestdata['class'] : ''))),
	
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
?>