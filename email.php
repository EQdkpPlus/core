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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class email extends page_generic {
	public static $shortcuts = array('mail' => 'MyMailer');

	public function __construct() {
		$handler = array(
			'send'	=> array('process'	=> 'send', 'csrf' => true),
		);
		parent::__construct(false, $handler, array());
		
		$this->user->check_auth('u_usermailer');
		
		if (!$this->user->is_signedin()){
			message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied');
		}		
		
		$this->process();
	}

	public function send(){
		$user_id = $this->in->get('user', 0);
		if ($user_id < 1){
			$this->core->message($this->user->lang('error_user_not_found'), $this->user->lang('error'), 'red');
		} elseif ($this->in->get('body') == '' || $this->in->get('subject') == '') {
			$this->core->message($this->user->lang('adduser_send_mail_error_fields'), $this->user->lang('error'), 'red');
		} else {
			$priv = $this->pdh->get('user', 'privacy_settings', array($user_id));
			$perm = false;
			
			$priv['priv_set'] = (isset($priv['priv_set'])) ? $priv['priv_set'] : 1;
			switch ((int)$privacy['priv_set']){
				case 0: // all
					$perm = true;
					break;
				case 1: // only user
					if($this->user->is_signedin()){
						$perm = true;
					}
					break;
				case 2: // only admins
					if($this->user->check_group(2, false) || $this->user->check_group(3, false)){
						$perm = true;
					}
				break;
			}
			
			//Permission to send
			if ($perm) {
				if (strlen($this->pdh->get('user', 'email', array($user_id))) && strlen($this->user->data['user_email'])){
					$options = array(
						'template_type'		=> 'input',
					);

					//Set E-Mail-Options
					$this->mail->SetOptions($options);
					
					$status = $this->mail->SendMail($this->pdh->get('user', 'email', array($user_id)), $this->user->data['user_email'], $this->in->get('subject'), $this->in->get('body'));
					if ($status){
						$this->core->message($this->user->lang('adduser_send_mail_suc'), $this->user->lang('success'), 'green');
						$this->tpl->add_js("jQuery.FrameDialog.closeDialog();");
					} else {
						$this->core->message($this->user->lang('error_email_send'), $this->user->lang('error'), 'red');
					}

				} else {
					$this->core->message($this->user->lang('fv_invalid_email'), $this->user->lang('error'), 'red');
				}			
			
			} else {
				message_die($this->user->lang('noauth'), $this->user->lang('noauth_default_title'), 'access_denied');
			}
			
		}
		
	}

	public function display(){
		$user_id = $this->in->get('user', 0);
		if ($user_id < 1){
			$this->core->message($this->user->lang('error_user_not_found'), $this->user->lang('error'), 'red');
		} else {
			$this->tpl->assign_vars(array(
				'TO_USER_ID' => $user_id,
				'USERNAME'=> $this->pdh->get('user', 'name', array($user_id)),
				'BODY'	=> $this->in->get('body', ''),
				'SUBJECT' => $this->in->get('subject', ''),
			));
		}
	
	
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('adduser_send_mail'),
			'template_file'		=> 'email.html',
			'header_format'		=> 'simple',
			'display'			=> true,
			)
		);
	}

}
registry::register('email');
?>