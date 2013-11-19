<?php
 /*
 * Project:		eqdkpPLUS Libraries: MyMailer
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:MyMailer
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Include Needed files...
include_once('class.phpmailer.php');

/**************** OPTIONS HELP *****************
	$options = array(
		'sender_mail'		=> $conf['rp_sender_email'],
		'mail_type'			=> 'html',
		'template_type'		=> 'file',
	);
************************************************/

class MyMailer extends PHPMailer {
	
	private $myoptions = array();

	public static $shortcuts = array(
		'core', 'user', 'config',
		'crypt'		=> 'encrypt',
	);

	/**
	* Construct
	*
	* @param $options				Array with options (see above)
	* @param $path					root path to tmplate/language files folder
	* @return traue/false
	*/
	public function __construct($options='') {
		if(!is_array($options)){
			$this->myoptions['mail_type']		= 'html';
			$this->myoptions['template_type']	= 'file';
		}

		// Some usefull information
		$this->mydeflang	= $this->config->get('default_lang');
		$this->adminmail	= $this->crypt->decrypt($this->config->get('admin_email'));
		$this->dkpname		= ($this->config->get('main_title')) ? $this->config->get('main_title') : $this->config->get('guildtag').' '.$this->config->get('dkp_name');
		$this->sendmeth		= $this->config->get('lib_email_method');

		// Language Vars
		$this->nohtmlmssg	= $this->user->lang('error_nohtml');
	}

	/**
	* Set Options Array
	*
	* @param $options		Array with options (see above)
	* @return --
	*/
	public function SetOptions($options){
		$this->myoptions	= $options;
	}

	public function Set_Language($lang){
		$this->mydeflang = $lang;
	}

	public function generateSubject($input){
		return $this->config->get('guildtag').' '.$this->config->get('dkp_name').': '.$input;
	}

	/**
	* Set Path
	*
	* @param $path			root path to tmplate/language files folder
	* @return --
	*/
	public function SetPath($path){
		$this->root_path		= $path;
	}

	/**
	* Send the Mail with admin sender adress
	*
	* @param $adress				recipient email address
	* @param $subject				email subject
	* @param $templatename	Name of the Email template to use
	* @param $bodyvars			Body Vars
	* @param $method				Method to send the mails (smtp, sendmail, mail)
	* @return traue/false
	*/
	public function SendMailFromAdmin($adress, $subject, $templatename, $bodyvars = array()){
		$this->AddAddress(stripslashes($adress));
		$this->GenerateMail($subject, $templatename, $bodyvars, $this->adminmail);
		return $this->PerformSend();
	}
	
	public function SendMail($adress, $from, $subject, $templatename, $bodyvars = array()){
		$this->AddAddress(stripslashes($adress));
		$this->GenerateMail($subject, $templatename, $bodyvars, $from);
		return $this->PerformSend();
	}

	/****** PRIVATE FUNCTIONS *****/

	/**
	* Template
	*
	* @param $templatename	Name of the Email template to use
	* @param $inputs				Array with input variables to change in mail body
	* @return traue/false
	*/
	private function Template($templatename, $inputs){
		// Check if the template is from a file or not
		if($this->myoptions['template_type'] == 'input'){
			$body   = $templatename;
		}else{
			if (strpos($templatename, $this->root_path) === 0){
				$body   = $this->getFile($templatename);
			} else {
				$body   = $this->getFile($this->root_path.'language/'.$this->mydeflang.'/email/'.$templatename);
			}
		}
		$body   = str_replace("[\]",'',$body);
		if(is_array($inputs)){
			foreach($inputs as $name => $value){
				$body = str_replace("{".$name."}",$value,$body);
			}
		}
		return $body;
	}

	/**
	* Generate the Mail Body & rest
	*
	* @param $subject				Subject of the Mail
	* @param $templatename	Name of the Email template to use
	* @param $bodyvars			Array with input variables to change in mail body
	* @return traue/false
	*/
	private function GenerateMail($subject, $templatename, $bodyvars, $from){
		$this->From     = $from;
		$this->CharSet	= 'UTF-8';
		$this->FromName = $this->dkpname;
		$this->Subject  = $this->generateSubject($subject);
		$tmp_body		= $this->Template($templatename, $bodyvars);
		$signature 		= ($this->config->get('lib_email_signature')) ? "\n".$this->config->get('lib_email_signature_value') : '';

		if($this->myoptions['mail_type'] == 'text'){
			// Text Mail
			$this->Body	= $tmp_body.$signature;
		}else{
			// HTML Mail
			$this->MsgHTML($tmp_body.nl2br($signature));
			$this->AltBody = $this->nohtmlmssg;
		}
	}

	/**
	* Perform the message delivery
	*
	* @param $method Method to send the mails (smtp, sendmail, mail)
	* @return traue/false
	*/
	private function PerformSend(){
		if($this->sendmeth == 'smtp'){
			// set the smtp auth
			$this->Mailer		= 'smtp';
			$this->SMTPAuth		= ($this->config->get('lib_email_smtp_auth') == 1) ? true : false;
			$this->Host			= $this->config->get('lib_email_smtp_host');
			$this->Username		= $this->config->get('lib_email_smtp_user');
			$this->Password		= $this->config->get('lib_email_smtp_pw');
			$this->SMTPSecure	= (strlen($this->config->get('lib_email_smtp_connmethod'))) ? $this->config->get('lib_email_smtp_connmethod') : '';
			$this->Port			= (strlen($this->config->get('lib_email_smtp_port'))) ? $this->config->get('lib_email_smtp_port') : 25;
		}elseif($this->sendmeth == 'sendmail'){
			$this->Mailer		= 'sendmail';
			if($this->config->get('lib_email_sendmail_path')){
				$this->Sendmail	= $this->config->get('lib_email_sendmail_path');
			}
		}else{
			$this->Mailer   = 'mail';
		}
		$sendput = $this->Send();
		$this->ClearAddresses();
		return $sendput;
	}

		/**
		* Helper file for file Handling
		*
		* @param $filename  the filename of the template file
		* @return file/false
		*/
		function getFile($filename) {
		if( false == ($return = file_get_contents($filename))){
			return false;
		}else{
			return $return;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_MyMailer', MyMailer::$shortcuts);
?>