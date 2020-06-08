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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

class MyMailer extends gen_class {

	private $myoptions = array('mail_type' => 'html', 'template_type' => 'file');
	protected $adminmail;
	protected $mydeflang = "english";
	protected $dkpname;
	protected $sendmeth = 'php';
	protected $nohtmlmssg;
	private $objMailer = false;
	private $sendStatus = false;
	private $sendError = "";
	private $debug = array();

	public static $shortcuts = array(
		'crypt'		=> 'encrypt',
	);

	/**
	* Construct
	*
	* @param $options				Array with options (see above)
	* @param $path					root path to tmplate/language files folder
	* @return true/false
	*/
	public function __construct($options='') {
		$this->objMailer = new PHPMailer;

		// Some usefull information
		$this->mydeflang	= $this->config->get('default_lang');
		$this->adminmail	= $this->crypt->decrypt($this->config->get('admin_email'));
		$this->dkpname		= ($this->config->get('main_title')) ? $this->config->get('main_title') : $this->config->get('guildtag').' '.$this->config->get('dkp_name');
		$this->sendmeth		= $this->config->get('lib_email_method');
		$this->Signature	= ($this->config->get('lib_email_signature')) ? "\n".$this->config->get('lib_email_signature_value') : '';

		// Language Vars
		$this->nohtmlmssg	= $this->user->lang('error_nohtml');

		if(!$this->pdl->type_known('mail')) $this->pdl->register_type('mail', null, null, array(2,3,4), ((DEBUG > 2) ? true : false));
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

	/**
	 * Set Language for Email Templates
	 *
	 * @param string $lang
	 */
	public function Set_Language($lang){
		$this->mydeflang	= $lang;
	}

	public function generateSubject($input){
		$strMaintitle = $this->config->get('main_title');
		$strTitle = ($strMaintitle && strlen($strMaintitle)) ? $strMaintitle : ($this->config->get('guildtag').' '.$this->config->get('dkp_name'));
		return $strTitle.': '.$input;
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
	 * Send a Mail from the admin sender address
	 *
	 * @param string $adress Receiver
	 * @param string $subject
	 * @param string $templatename
	 * @param array $bodyvars
	 * @return true/false
	 */
	public function SendMailFromAdmin($adress, $subject, $templatename, $bodyvars = array()){
		return $this->GenerateMail($adress, $subject, $templatename, $bodyvars, $this->adminmail);
	}

	/**
	 * Send a Mail from a given sender
	 *
	 * @param string $adress Receiver
	 * @param string $from	Sender
	 * @param string $subject
	 * @param string $templatename
	 * @param array $bodyvars
	 * @return true/false
	 */
	public function SendMail($adress, $from, $subject, $templatename, $bodyvars = array()){
		return $this->GenerateMail($adress, $subject, $templatename, $bodyvars, $from);
	}

	/****** PRIVATE FUNCTIONS *****/

	/**
	* Template
	*
	* @param $templatename		Name of the Email template to use
	* @param $inputs			Array with input variables to change in mail body
	* @return traue/false
	*/
	private function Template($templatename, $inputs){

		//Specific Email Template
		if($this->myoptions['template_type'] == 'input'){
			$content	= $templatename;
		} elseif (strpos($templatename, $this->root_path) === 0){
			$content	= $this->getFile($templatename);
		} else {
			$content	= $this->getFile($this->root_path.'language/'.$this->mydeflang.'/email/'.$templatename);
		}

		//General Body Email Template
		$intDefaultTemplate	= register('config')->get('default_style');
		$strTemplatePath	= register('pdh')->get('styles', 'templatepath', array($intDefaultTemplate));

		if(is_file($this->root_path.'templates/'.$strTemplatePath.'/email.tpl')){
			// get the logo
			if(is_file(register('file_handler')->FolderPath('','files').register('config')->get('custom_logo'))){
				$headerlogo	= register('file_handler')->FolderPath('','files').register('config')->get('custom_logo');
			}else{
				$headerlogo	= $this->root_path.'templates/eqdkp_modern/images/logo.svg';
			}

			$this->objMailer->addEmbeddedImage($headerlogo, 'headerlogo');

			// load the images out of the template/images/email folder. If the image is a svg, also include png woth same name if available
			$images	= glob($this->root_path."templates/eqdkp_modern/images/emails/*.{jpg,png,svg}", GLOB_BRACE);
			$arrEmbedd	= array();
			foreach($images as $image){
				$imageinfo	= pathinfo($image);
				$arrEmbedd[str_replace('-','', $imageinfo["filename"])][] = array('filename' => $imageinfo["basename"], 'extension' => $imageinfo["extension"]);
			}
			foreach($arrEmbedd as $fileid=>$filedata){
				foreach($filedata as $image){
					$this->objMailer->addEmbeddedImage($this->root_path.'templates/eqdkp_modern/images/emails/'.$image['filename'], $fileid.'_'.$image['extension']);
				}
			}

			// replace the stuff
			$strMaintitle = $this->config->get('main_title');
			$strTitle = ($strMaintitle && strlen($strMaintitle)) ? $strMaintitle : ($this->config->get('guildtag').' '.$this->config->get('dkp_name'));
			$body	= $this->getFile($this->root_path.'templates/'.$strTemplatePath.'/email.tpl');
			$body	= str_replace('{CONTENT}', $content, $body);
			$body	= str_replace('{LOGO}', $headerlogo, $body);
			$body	= str_replace('{PLUSVERSION}', VERSION_EXT, $body);
			$body	= str_replace('{SUBJECT}', $this->Subject, $body);
			$body	= str_replace('{PLUSLINK}', register('environment')->buildlink(), $body);
			$body	= str_replace('{SIGNATURE}', nl2br($this->Signature), $body);
			$body	= str_replace('{GUILDTAG}', $strTitle, $body);
			$body	= str_replace('{EQDKP_ABOUT_URL}', EQDKP_ABOUT_URL, $body);

		} else $body = $content.nl2br($this->Signature);

		$body	= str_replace("[\]",'',$body );
		if(is_array($inputs)){
			foreach($inputs as $name => $value){
				$body	= str_replace("{".$name."}",$value,$body );
			}
		}
		return $body;
	}
	
	public function smtpDebug($str, $level) {	
		$this->debug[] = $str;
	}

	/**
	* Generate the Mail Body & rest
	*
	* @param $to				Receiver of the  Mail
	* @param $subject				Subject of the Mail
	* @param $templatename	Name of the Email template to use
	* @param $bodyvars			Array with input variables to change in mail body
	* @return traue/false
	*/
	private function GenerateMail($to, $subject, $templatename, $bodyvars, $from){
		try {
			$strFromName		= ($this->config->get('lib_email_sender_name') && strlen($this->config->get('lib_email_sender_name'))) ? $this->config->get('lib_email_sender_name') : $from;

			$this->objMailer->setFrom($this->adminmail, $strFromName);
			$this->objMailer->clearReplyTos();
			$this->objMailer->addReplyTo($from, $from);
			$this->objMailer->CharSet = 'UTF-8';
			$this->objMailer->addAddress($to);
			$this->objMailer->Subject = $this->generateSubject($subject);
			$this->objMailer->XMailer = "EQdkp Plus";
			
			

			$tmp_body		= $this->Template($templatename, $bodyvars);

			if($this->myoptions['mail_type'] == 'text'){
				// Text Mail
				$this->objMailer->Body		= $tmp_body;
			}else{
				// HTML Mail
				$this->objMailer->msgHTML($tmp_body, $this->root_path);
				$this->objMailer->isHTML(true);
				//$this->objMailer->AltBody = $this->nohtmlmssg; Disable because Autogenerated
			}

			if (DEBUG == 4){
				pd($this->objMailer->Body);
			}

			if($this->sendmeth == 'smtp'){
				$this->objMailer->SMTPDebug = 0;
				if(DEBUG > 2){
					$this->debug = array();
					$this->objMailer->SMTPDebug = 3;
					$this->objMailer->Debugoutput = array($this, 'smtpDebug');
				}
				// Enable verbose debug output
				$this->objMailer->isSMTP();                                   // Set mailer to use SMTP
				$this->objMailer->Host = $this->config->get('lib_email_smtp_host');  // Specify main and backup SMTP servers
				$this->objMailer->SMTPAuth = ($this->config->get('lib_email_smtp_auth') == 1) ? true : false;// Enable SMTP authentication
				$this->objMailer->Username = $this->config->get('lib_email_smtp_user');                 // SMTP username
				$this->objMailer->Password = $this->config->get('lib_email_smtp_pw');                           // SMTP password
				$this->objMailer->SMTPSecure = (strlen($this->config->get('lib_email_smtp_connmethod'))) ? $this->config->get('lib_email_smtp_connmethod') : '';                            // Enable TLS encryption, `ssl` also accepted
				$this->objMailer->Port = (strlen($this->config->get('lib_email_smtp_port'))) ? (int)$this->config->get('lib_email_smtp_port') : 587;                                    // TCP port to connect to
			} elseif($this->sendmeth == 'sendmail'){
				$this->objMailer->isSendmail();
				$this->objMailer->Sendmail = ($this->config->get('lib_email_sendmail_path') && strlen($this->config->get('lib_email_sendmail_path'))) ? $this->config->get('lib_email_sendmail_path') : '/usr/sbin/sendmail';

			}else{
				$this->objMailer->isMail();
			}

			$dbgBody = $this->objMailer->AltBody;

			$blnSendresult = $this->objMailer->send();

			//Debugging
			$this->pdl->log("mail", "\nFrom: ".$from."
To: ".print_r($this->objMailer->getAllRecipientAddresses(), true)."
Subject: ".$this->objMailer->Subject."
Body: ".$dbgBody."
Method: ".$this->objMailer->Mailer."
Result: ".print_r($blnSendresult, true)."
Error: ".$this->objMailer->ErrorInfo."
=================================");

			$this->objMailer->clearAddresses();

			//Reset Status
			$this->sendStatus = false;
			$this->sendError = "";

			if (!$blnSendresult) {
				$this->core->message(nl2br(sanitize($this->objMailer->ErrorInfo)), 'Mail error', 'red');
				$this->sendStatus = false;
				$this->sendError = $this->objMailer->ErrorInfo;
				return false;
			} else {
				$this->sendStatus = true;
				return true;
			}

		} catch (Exception $e) {
			$this->core->message("Message could not be sent. Mailer Error: ".nl2br(sanitize($this->objMailer->ErrorInfo)), 'Mail error', 'red');
		}

	}

	function getLatestSendStatus(){
		return $this->sendStatus;
	}

	function getLatestErrorMessage(){
		$str = $this->sendError;
		
		if(DEBUG > 2 && count($this->debug)){
			$str .= '<br /><br />Debug:<ul>';
			foreach($this->debug as $k => $msg){
				$str .= '<li>- '.$msg.'</li>';
			}
			$str .= '</ul>';
		}
		
		return $str;
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
