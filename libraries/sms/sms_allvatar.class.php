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

class sms_allvatar extends sms_service {

	private $username	= "";
	private $passwort	= "";
	private $error		= false;
	private $status		= false;
	private $statMessage= "";
	
	public static function __shortcuts() {
		$shortcuts = array('user', 'xmltools', 'puf' => 'urlfetcher');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public function __construct($strUsername = false, $strPassword = false){
		$this->username = $strUsername;
		$this->password = $strPassword;
	}
	public function send($strMessage, $arrReceiver){
		$strReceiver = implode("\n", $arrReceiver);
		
		$strPostdata = 'xml_daten=%3C%3Fxml+version%3D%221.0%22%3F%3E%0D%0A%3Cident+user%3D%22'
					.$this->username.'%22+pass%3D%22'
					.$this->password.'%22%3E%0D%0A%3Ccontent+id%3D%22100%22%3E%0D%0A%3Cmessage%3E'
					.$strMessage.'%3C%2Fmessage%3E%0D%0A%3Ctarget+structur%3D%22adresse%22%3E'
					.$strReceiver.'%3C%2Ftarget%3E%0D%0A%3C%2Fcontent%3E%0D%0A%3C%2Fident%3E'
					.'&senden=senden';
					
		$strReturnData = $this->puf->post("https://www.sms-news-media.de/evo2/schnittstelle/xml_versand_0905.cfm", $strPostdata, "application/x-www-form-urlencoded");
		
		if($strReturnData && $strReturnData != "") {
			
			$xml = simplexml_load_string(trim($strReturnData));

			$arrXml = $this->xmltools->simplexml2array($xml);

			if(is_array($arrXml)){
				$this->status 		= $arrXml['@attributes']['status'];
				$this->statMessage	= $arrXml[0]; 
			}

		} else {
			$this->status = '-2';
			$this->statMessage = $errno. " ". $errstr;
		}	
		
		if ($this->status == '100') return true;
		
		return false;		
	}
	
	public function getError(){
		switch($this->status){
				case '-2':	$notice = $this->user->lang('sms_error_fopen')." ".$this->statMessage;	
				break;
				case '100':	$notice = $this->user->lang('sms_success');
				break;
				case '150':	$notice = $this->user->lang('sms_error_badpw');
				break;
				case '159':	$notice = $this->user->lang('sms_error_159');
				break;
				case '160':	$notice = $this->user->lang('sms_error_160');
				break;
				case '200':	$notice = $this->user->lang('sms_error_200');
				break;
				case '254':	$notice = $this->user->lang('sms_error_254');
				break;
				default:	$notice = $this->user->lang('sms_error');
				break;
		}
		return $notice;
	}
	
	

}// end of class
?>