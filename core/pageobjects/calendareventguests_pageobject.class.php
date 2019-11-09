<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
		$handler = array(
			'ajax'	=> array(
				array('process' => 'role_ajax',	'value' => 'role'),
			),
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}

	private function is_raidleader(){
		$ev_ext				= $this->pdh->get('calendar_events', 'extension', array($this->in->get('eventid', 0)));
		$raidleaders_chars	= ($ev_ext['raidleader'] > 0) ? $ev_ext['raidleader'] : array();
		$raidleaders_users	= $this->pdh->get('member', 'userid', array($raidleaders_chars));
		return (is_array($raidleaders_users) && in_array($this->user->data['user_id'], $raidleaders_users)) ? true : false;
	}

	// the role dropdown, attached to the character selection
	public function role_ajax(){
		$ddroles		= $this->pdh->get('roles', 'memberroles', array($this->in->get('requestid')));

		// hide the role if null and such stuff
		if($this->game->get_game_settings('calendar_hide_emptyroles')){
			$eventdata = $this->pdh->get('calendar_events', 'data', array($this->in->get('eventid', 0)));
			if($eventdata['extension']['raidmode'] == 'role'){
				$raidcategories = $this->pdh->aget('roles', 'name', 0, array($this->pdh->get('roles', 'id_list')));
				foreach ($raidcategories as $classid=>$classname){
					if($eventdata['extension']['distribution'][$classid] == 0){
						unset($ddroles[$classid]);
					}
				}
			}
		}

		// start the output
		header('content-type: text/html; charset=UTF-8');
		echo $this->jquery->dd_create_ajax($ddroles, array('selected'=>$this->pdh->get('member', 'defaultrole', array($this->in->get('requestid')))));exit;
	}

	public function add(){
		if($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()){
			if($this->in->get('membername')){
				if($this->in->get('guestid', 0) > 0){
					$blub = $this->pdh->put('calendar_raids_guests', 'update_guest', array(
						$this->in->get('guestid', 0), $this->in->get('class'), $this->in->get('group'), $this->in->get('note'), $this->in->get('role', 0)
					));
				}else{
					$blub = $this->pdh->put('calendar_raids_guests', 'insert_guest', array(
						$this->in->get('eventid', 0), $this->in->get('membername'), $this->in->get('class'), $this->in->get('group'), $this->in->get('note'), '' ,$this->in->get('role', 0)
					));
				}
			}

		}else{
			if (!$this->user->is_signedin() && $this->config->get('enable_captcha') == 1){
				$captcha = register('captcha');
				$response = $captcha->verify();
				if (!$response) {
					$this->core->message($this->user->lang('lib_captcha_wrong'), $this->user->lang('error'), 'red');
					return;
				}
			}

			// check if the email is validate
			if(!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_\-\+])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $this->in->get('email'))){
				$this->display( $this->user->lang('fv_invalid_email') );
				return;
			}

			// check if name is empty
			if($this->in->get('membername') == ''){
				$this->display( $this->user->lang('fv_invalid_name') );
				return;
			}

			// check if email already joined
			if($this->pdh->get('calendar_raids_guests', 'check_email', array($this->in->get('eventid', 0), $this->in->get('email'))) == 'true'){
				$this->display( str_replace("{0}", $this->in->get('email'), $this->user->lang('fv_email_alreadyuse')) );
				return;
			}
			$blub = $this->pdh->put('calendar_raids_guests', 'insert_guest', array(
				$this->in->get('eventid', 0), $this->in->get('membername'), $this->in->get('class'), 0, $this->in->get('note'), $this->in->get('email'), $this->in->get('role', 0)
			));
		}
		$this->pdh->process_hook_queue();
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	public function display($error=false){
		if($error) {
			$this->core->message($error, $this->user->lang('error'), 'red');
		}
		$guestdata = ($this->in->get('guestid', 0) > 0) ? $this->pdh->get('calendar_raids_guests', 'guest', array($this->in->get('guestid', 0))) : array();

		$display_captcha = false;
		if (!$this->user->is_signedin() && $this->config->get('enable_captcha') == 1){
			$captcha = register('captcha');
			$display_captcha = $captcha->get();
		}

		$is_roleraid	= false;
		$eventdata		= $this->pdh->get('calendar_events', 'data', array($this->in->get('eventid', 0)));
		if($eventdata['extension']['raidmode'] == 'role'){
			$is_roleraid = true;
			$classrole = $this->jquery->dd_ajax_request('class', 'role', $this->game->get_primary_classes(array('id_0')), array(), ((isset($guestdata['class'])) ? $guestdata['class'] : ''), $this->strPath.$this->SID.'&eventid='.$this->in->get('eventid', 0).'&guestid='.$this->in->get('guestid', 0).'&ajax=role');
		}

		$this->tpl->assign_vars(array(
			'PERM_ADD'				=> ($this->user->check_auth('a_cal_revent_conf', false) || $this->is_raidleader()) ? true : false,
			'PERM_GUESTAPPLICATION'	=> ($this->config->get('calendar_raid_guests') == 2) ? true : false,
			'ROLERAID'				=> $is_roleraid,
			'EVENT_ID'				=> $this->in->get('eventid', 0),
			'GUEST_ID'				=> $this->in->get('guestid', 0),
			'ROLE_DD'				=> ($eventdata['extension']['raidmode'] == 'role') ? $classrole[1] : '',
			'CLASS_DD'				=> ($eventdata['extension']['raidmode'] == 'role') ? $classrole[0] : (new hdropdown('class', array('options' => $this->game->get_primary_classes(array('id_0')), 'value' => ((isset($guestdata['class'])) ? $guestdata['class'] : ''))))->output(),

			// captcha
			'CEG_CAPTCHA'			=> $display_captcha,
			'S_CEG_DISPLAY_CATPCHA' => (($this->user->is_signedin()) ? false : true),

			// the edit input
			'MEMBER_NAME'			=> (isset($guestdata['name'])) ? sanitize($guestdata['name']) : '',
			'NOTE'					=> (isset($guestdata['note'])) ? sanitize($guestdata['note']) : '',
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'calendar/guests.html',
			'display'			=> true
		));
	}
}
