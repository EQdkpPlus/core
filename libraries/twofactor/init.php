<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date: 2013-03-23 18:01:39 +0100 (Sa, 23 Mrz 2013) $
* -----------------------------------------------------------------------
* @author		$Author: godmod $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 13242 $
*
* $Id: Manage_Articles.php 13242 2013-03-23 17:01:39Z godmod $
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
			'TWOFACTOR_QR'			=> $ga->getQRCodeGoogleUrl('EQdkp Plus '.$this->config->get('guildtag'), $secret),
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