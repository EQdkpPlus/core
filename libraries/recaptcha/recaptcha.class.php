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

		private $type = 'v2';

		function __construct($type = 'v2'){
			$this->type = $type;
		}

		function check_answer ($privkey, $remoteip, $response)
		{
			if ($privkey == null || $privkey == '') {
				return false;
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
		public function get_html($pubkey, $type='v2')
		{
			if ($pubkey == null || $pubkey == '') {
				register('core')->message("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>", "Error", 'red');
				return "";
			}

			if($this->type == 'v2'){
				$out = $this->html_v2($pubkey);
			} elseif($this->type == 'invisible'){
				$out = $this->html_invisible($pubkey);
			}

			return $out;
		}

		private function html_v2($pubkey){
			$out = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
			$out .= '<div class="g-recaptcha" data-sitekey="'.$pubkey.'"></div>';

			return $out;
		}

		private function html_invisible($pubkey){
			$out = '<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=recaptchaCallback" async defer></script>';
			$out .= '<script>
			var mycaptcha = false;
			var mycaptcharesponse = false;

			function recaptchaCallback() {
				bucket = document.getElementById("recaptchaBucket");
				mycaptcha = grecaptcha.render(bucket, {
										sitekey: \''.$pubkey.'\',
										size: \'invisible\',
										badge: \'inline\',
										callback: function (recaptchaToken) {
											mycaptcharesponse = true;
											bucket = document.getElementById("recaptchaBucket");
											myform = $(bucket).closest("form");
											$(myform).trigger("submit");
                						},
               							\'expired-callback\' : function(){grecaptcha.reset(mycaptcha);}
									});

				myform = $(bucket).closest("form");

				//Give all Buttons an event listener
				var pressed = false;
				$(myform).find("button[type=\'submit\']").on("click", function(){
					pressed = this;
				})
				$(myform).find("input[type=\'submit\']").on("click", function(){
					pressed = this;
				})

				$(myform).on("submit", function(event){
					if(mycaptcharesponse) {
						//Add the Button into the form
						var buttonname = $(pressed).attr("name");
						if(buttonname) $(myform).append("<input type=\"hidden\" name=\""+buttonname+"\">");

						$(pressed).click();
						return true;
					} else {
						grecaptcha.execute(mycaptcha);
						return false;
					}
				});
			}
			</script>';
			$out .= '<input type="hidden" name="recaptcha-type" value="invisible">
					<div id="recaptchaBucket"></div>
					<noscript>
						<div style="width: 302px; height: 473px;">
							<div style="width: 302px; height: 422px; position: relative;">
								<div style="width: 302px; height: 422px; position: relative;">
									<iframe src="https://www.google.com/recaptcha/api/fallback?k='.$pubkey.'" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
								</div>
								<div style="width: 300px; height: 60px; position: relative; border-style: none; bottom: 12px; left: 0; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
									<textarea name="g-recaptcha-response" class="g-recaptcha-response" style="width: 290px; height: 50px; border: 1px solid #c1c1c1; margin: 5px; padding: 0px; resize: none;"></textarea>
								</div>
							</div>
						</div>
					</noscript>';

			return $out;

		}

	}
}
