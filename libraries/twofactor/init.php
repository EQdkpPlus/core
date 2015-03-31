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
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');

class TwofactorInit extends page_generic {

	public function __construct(){
		if (!$this->user->is_signedin()) return;
		
		$handler = array(
			'save' 		=> array('process' => 'save', 'csrf' => true),
		);
		parent::__construct(false, $handler);
		$this->process();
	}
	

	public function display() {
		include_once $this->root_path.'libraries/twofactor/googleAuthenticator.class.php';
		$ga = new PHPGangsta_GoogleAuthenticator();
		
		$secret = $ga->createSecret();

		$this->tpl->assign_vars(array(
			'TWOFACTOR_KEY' 		=> $secret,
			'TWOFACTOR_QR'			=> $ga->getQRCodeGoogleUrl(str_replace(' ' , '_', 'EQdkpPlus '.$this->config->get('guildtag')), $secret),
			'TWOFACTOR_KEY_ENCR'	=> rawurlencode(register('encrypt')->encrypt($secret)),
		));

		$this->core->set_vars(array(
			'page_title'		=> "",
			'header_format'		=> "simple",
			'template_file'		=> 'twofactor_init.html',
			'display'			=> true)
		);
	}
	
}
registry::register('TwofactorInit');
?>