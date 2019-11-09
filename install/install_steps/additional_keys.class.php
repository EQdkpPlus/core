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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class additional_keys extends install_generic {
	public static $before 		= 'admin_user';

	public static function before() {
		return self::$before;
	}

	public $next_button		= 'skip';

	public function get_output() {
		$content = '
		<script> function change_next_button(){
			$(\'.buttonbar button[name="next"]\').html(\'<i class="fa fa-arrow-right"></i> '. html_entity_decode($this->lang['continue'], ENT_COMPAT, 'UTF-8').'\');
		}</script>
		<div class="infobox infobox-large infobox-blue clearfix">
			<i class="fa fa-info-circle fa-4x pull-left"></i>'.$this->lang['additional_keys_info'].'
		</div>
		<br />
					<h2>reCAPTCHA</h2>
					'.$this->lang['recaptcha_info'].'
					<table width="100%" border="0" cellspacing="1" cellpadding="2" class="no-borders">
						<tr>
							<td align="right"><strong>'.$this->lang['recaptcha_type'].'</strong></td>
							<td align="left"><div id="recaptcha_type" class="radioContainer"><label><input type="radio" name="recaptcha_type" value="v2" checked="checked"> V2 - Checkbox</label><label><input type="radio" name="recaptcha_type" value="invisible"> V2 - Invisible</label></div></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['recaptcha_okey'].'</strong></td>
							<td align="left"><input type="text" name="lib_recaptcha_okey" value="" class="input" size="30" onchange="change_next_button()"/></td>
						</tr>
						<tr>
							<td align="right"><strong>'.$this->lang['recaptcha_pkey'].'</strong></td>
							<td align="left"><input type="text" name="lib_recaptcha_pkey" value="" class="input" size="30"  onchange="change_next_button()"/></td>
						</tr>
					</table>
					<br />
			';
		return $content;
	}

	public function get_filled_output() {
		return $this->get_output();
	}

	public function parse_input() {
		$strRecaptchaPub = $this->in->get('lib_recaptcha_okey');
		$strRecaptchaPriv = $this->in->get('lib_recaptcha_pkey');
		$strRecaptchaType = $this->in->get('recaptcha_type');

		if($strRecaptchaPub != "" && $strRecaptchaPriv != ""){
			$this->config->set(array(
				'lib_recaptcha_okey' => $strRecaptchaPub,
				'lib_recaptcha_pkey' => $strRecaptchaPriv,
				'recaptcha_type'	=> $strRecaptchaType,
			));
		}

		return true;
	}
}
