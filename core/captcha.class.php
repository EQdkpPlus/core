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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}


class captcha extends gen_class {

	private $arrAvailableMethods = array();

	public function __construct(){


	}

	public function get(){
		if (!(int)$this->config->get('enable_captcha')) return "";

		//Load default recaptcha
		if ($this->config->get('lib_recaptcha_pkey') && strlen($this->config->get('lib_recaptcha_pkey'))){
			$type = ($this->config->get('recaptcha_type')) ? $this->config->get('recaptcha_type') : 'v2';
			require_once($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$recaptcha = new recaptcha($type);

			$strOut ='<dl>
			<dt>
				<label>'.$this->user->lang('lib_captcha_head').'</label>
			</dt>
			<dd>';

			$strOut .= $recaptcha->get_html($this->config->get('lib_recaptcha_okey'));

			$strOut .= '</dd>
			</dl>';
		}

		//Add additional Captchas
		if($this->hooks->isRegistered('captcha_get')){
			$arrHooks = $this->hooks->process('captcha_get', array());
			foreach($arrHooks as $arrHook){
				if(isset($arrHook['label'])){
					$strOut .='<dl>
					<dt>
						<label>'.$arrHook['label'].'</label>
					</dt>
					<dd>';
							$strOut .= $arrHook['captcha'];
							$strOut .= '</dd>
					</dl>';
				} else {
					$strOut .= $arrHook['captcha'];
				}

			}

		}

		return $strOut;
	}

	public function verify(){
		if (!(int)$this->config->get('enable_captcha')) return true;

		$blnResult = false;

		//Load default recaptcha
		if ($this->config->get('lib_recaptcha_pkey') && strlen($this->config->get('lib_recaptcha_pkey'))){
			$type = ($this->config->get('recaptcha_type')) ? $this->config->get('recaptcha_type') : 'v2';
			require_once($this->root_path.'libraries/recaptcha/recaptcha.class.php');
			$recaptcha = new recaptcha($type);

			$response = $recaptcha->check_answer($this->config->get('lib_recaptcha_pkey'), $this->env->ip, $this->in->get('g-recaptcha-response'));
			if ($response->is_valid) {
				$blnResult = true;
			}
		}

		//Add additional Captchas
		if($this->hooks->isRegistered('captcha_verify')){
			$arrReturn = $this->hooks->process('captcha_verify', array('result' => $blnResult), true);
			$blnResult = (isset($arrReturn['result'])) ? $arrReturn['result'] : false;
		}

		return $blnResult;
	}


}
