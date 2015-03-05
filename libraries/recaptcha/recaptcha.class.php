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
/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
if (!class_exists("ReCaptchaResponse")) {
	class ReCaptchaResponse {
		var $is_valid;
		var $error;
	}
}

if (!class_exists("recaptcha")) {
	class recaptcha {
		
		function check_answer ($privkey, $remoteip, $response)
		{
			if ($privkey == null || $privkey == '') {
				register('core')->message("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>", "Error", 'red');
			}
			$recaptcha_response = new ReCaptchaResponse();
			if($remoteip == "" || $response == ""){
				$recaptcha_response->is_valid = false;
				$recaptcha_response->error = "Internal Error: Remoteip or Response is empty";
				return $recaptcha_response;
			}
			
			$urlfetcher = register('urlfetcher');
			$strResult = $urlfetcher->post("https://www.google.com/recaptcha/api/siteverify", array(
					'secret'	=> $privkey,
					'response'	=> $response,
					'remoteip'	=> $remoteip,
			), "application/x-www-form-urlencoded");

			if($strResult){
				$arrJSON = json_decode($strResult);

				if($arrJSON->success === true){
					$recaptcha_response->is_valid = true;
					return $recaptcha_response;
				} else {
					$recaptcha_response->is_valid = false;
					$recaptcha_response->error = $arrJSON->error_codes;
					return $recaptcha_response;
				}
			}
			
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = "Internal Error: No Result from Host";
			return $recaptcha_response;
		}
		
		
		/**
		 * Gets the challenge HTML (javascript and non-javascript version).
		 * This is called from the browser, and the resulting reCAPTCHA HTML widget
		 * is embedded within the HTML form it was called from.
		 * @param string $pubkey A public key for reCAPTCHA
		 * @param string $error The error given by reCAPTCHA (optional, default is null)
		
		 * @return string - The HTML to be embedded in the user's form.
		 */
		public function get_html($pubkey)
		{
			if ($pubkey == null || $pubkey == '') {
				register('core')->message("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>", "Error", 'red');
			}

			$out = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
			$out .= '<div class="g-recaptcha" data-sitekey="'.$pubkey.'"></div>';
			
			return $out;
		}
		
		// ReCaptcha Output
		public function get_html_for_form(){
			$out = '<dl>
						<dt>
							<label>'.register('user')->lang('lib_captcha_head').'</label>
						</dt>
						<dd>
							'.$this->recaptcha_get_html(register('config')->get('lib_recaptcha_okey')).'
						</dd>
					</dl>';
			return $out;
		}
	}
}
?>