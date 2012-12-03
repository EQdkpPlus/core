<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
} 

class sms extends gen_class {

	private $username;
	private $password;
	private $service;
	
	public static $shortcuts = array('config');

	public function __construct($strUsername = false, $strPassword = false){
		$this->username = $strUsername;
		$this->password = $strPassword;
		
		//Load specific service;
		if ($this->config->get('sms_service') != ""){
			$strService = $this->config->get('sms_service');
		} else {
			$strService = 'sms_allvatar';
		}

		include_once($this->root_path.'libraries/sms/'.$strService.'.class.php');
		$this->service = new $strService($this->username, $this->password);
	}
	
	public function send($strMessage, $arrReceiver){
		return $this->service->send($strMessage, $arrReceiver);
	}
	
	public function getError(){
		return $this->service->getError();
	}
	
}

abstract class sms_service extends gen_class {
	public static $shortcuts = array();
	
	abstract public function __construct($strUsername = false, $strPassword = false);
	abstract public function send($strMessage, $arrReceiver);
	abstract public function getError();
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_sms', sms::$shortcuts);

?>