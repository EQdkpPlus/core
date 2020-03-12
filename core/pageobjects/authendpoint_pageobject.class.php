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

class authendpoint_pageobject extends pageobject {
	public static $shortcuts = array();

	public function __construct() {
		$handler = array(
			'lmethod' => array('process' => 'redirect', 'csrf' => true),
		);
		parent::__construct(false, $handler);

		$this->process();
	}

	public function redirect(){
		$strStatus = $this->in->get('status');
		if($strStatus == "") {
			$this->display();
			return;
		}

		$strMethod = $this->in->get('lmethod');

		set_cookie('auth_init_'.$strMethod, $strStatus, ($this->time->time + (5*60)));
		$this->user->setSessionVar('auth_init_'.$strMethod, $strStatus);

		$strRedirURL = $this->user->handle_login_functions('redirect', $strMethod, array('status' => $strStatus));

		if($strRedirURL){
			redirect($strRedirURL, false, true);
		}

		//echo "a"; die();

		redirect($this->controller_path_plain.'Login/'.$this->SID);
	}


	public function display() {
		$strMethod = $this->in->get('lmethod');

		$strCookie = $this->in->getEQdkpCookie('auth_init_'.$strMethod);
		$arrSessionVars = $this->user->data['session_vars'];
		$strSessionVar = $arrSessionVars['auth_init_'.$strMethod];

		if($strCookie === "" || $strCookie !== $strSessionVar) {
			//Error
			//echo "b"; die();
			redirect($this->controller_path_plain.'Login/'.$this->SID);
		}

		if($strCookie === 'account'){
			if (!$this->user->is_signedin()){
				//echo "c"; die();
				redirect($this->controller_path_plain.'Login/'.$this->SID);
			}

			$this->user->setSessionVar('auth_init_'.$strMethod, 'outdated');
			set_cookie('auth_init_'.$strMethod, 'outdated', $this->time->time);

			$strURL = $this->env->request_query;
			if($strURL) $strURL = substr($strURL, 1);
			//Redirect to Settings page
			$redir_url = $this->controller_path_plain.'Settings/'.$this->SID.'&'.$strURL.'&mode=addauthacc&link_hash='.$this->user->csrfGetToken('settings_pageobjectmode');
			redirect($redir_url);
		}

		if($strCookie === 'register'){
			$this->user->setSessionVar('auth_init_'.$strMethod, 'outdated');
			set_cookie('auth_init_'.$strMethod, 'outdated', $this->time->time);

			$strURL = $this->env->request_query;
			if($strURL) $strURL = substr($strURL, 1);
			//Redirect to Login page
			$redir_url = $this->controller_path_plain.'Register/'.$this->SID.'&'.$strURL.'&register';
			redirect($redir_url);
		}

		if($strCookie === 'login'){
			$this->user->setSessionVar('auth_init_'.$strMethod, 'outdated');
			set_cookie('auth_init_'.$strMethod, 'outdated', $this->time->time);

			$strURL = $this->env->request_query;
			if($strURL) $strURL = substr($strURL, 1);
			//Redirect to Login page
			$redir_url = $this->controller_path_plain.'Login/'.$this->SID.'&'.$strURL.'&login';
			redirect($redir_url);
		}
		//echo "d"; die();
		redirect($this->controller_path_plain.'Login/'.$this->SID);
	}

}
