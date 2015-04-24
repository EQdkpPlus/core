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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// Include Needed files...
include_once('class.phpmailer.php');
include_once('class.pop3.php');
include_once('class.smtp.php');

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
		$this->mydeflang	= $lang;
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
	* @param $templatename		Name of the Email template to use
	* @param $inputs			Array with input variables to change in mail body
	* @return traue/false
	*/
	private function Template($templatename, $inputs){
		// Check if the template is from a file or not
		if($this->myoptions['template_type'] == 'input'){
			$body	= $templatename.nl2br($this->Signature);
		}else{
			//Specific Email Template
			if (strpos($templatename, $this->root_path) === 0){
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
					$headerlogo	= register('environment')->buildlink().'templates/eqdkp_modern/images/logo.svg';
				}
				$this->AddEmbeddedImage($headerlogo, 'headerlogo');
				
				// load the images out of the template/images/email folder. If the image is a svg, also include png woth same name if available
				$images	= glob($this->root_path."templates/eqdkp_modern/images/emails/*.{jpg,png,svg}", GLOB_BRACE);
				$arrEmbedd	= array();
				foreach($images as $image){
					$imageinfo	= pathinfo($image);
					$arrEmbedd[str_replace('-','', $imageinfo["filename"])][] = array('filename' => $imageinfo["basename"], 'extension' => $imageinfo["extension"]);
				}
				foreach($arrEmbedd as $fileid=>$filedata){
					foreach($filedata as $image){
						$this->AddEmbeddedImage($this->root_path.'templates/eqdkp_modern/images/emails/'.$image['filename'], $fileid.'_'.$image['extension']);
					}
				}
				#d($arrEmbedd);die();
				#$this->AddEmbeddedImage($this->root_path.'templates/eqdkp_modern/images/background-head.svg', 'backgroundimage');
				#$this->AddEmbeddedImage($this->root_path.'templates/eqdkp_modern/images/background-head.png', 'backgroundimage_fallback');

				// replace the stuff
				$body	= $this->getFile($this->root_path.'templates/'.$strTemplatePath.'/email.tpl');
				$body	= str_replace('{CONTENT}', $content, $body);
				$body	= str_replace('{LOGO}', $headerlogo, $body);
				$body	= str_replace('{PLUSVERSION}', VERSION_EXT, $body);
				$body	= str_replace('{SUBJECT}', $this->Subject, $body);
				$body	= str_replace('{PLUSLINK}', register('environment')->buildlink(), $body);
				$body	= str_replace('{SIGNATURE}', nl2br($this->Signature), $body);
			} else $body = $content.nl2br($this->Signature);
		}
		$body	= str_replace("[\]",'',$body );
		if(is_array($inputs)){
			foreach($inputs as $name => $value){
				$body	= str_replace("{".$name."}",$value,$body );
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
		$this->From			= $from;
		$this->CharSet		= 'UTF-8';
		$this->FromName		= $this->dkpname;
		$this->Subject		= $this->generateSubject($subject);
		$this->Signature	= ($this->config->get('lib_email_signature')) ? "\n".$this->config->get('lib_email_signature_value') : '';
		$tmp_body			= $this->Template($templatename, $bodyvars);

		if($this->myoptions['mail_type'] == 'text'){
			// Text Mail
			$this->Body		= $tmp_body.$this->Signature;
		}else{
			// HTML Mail
			$this->MsgHTML($tmp_body);
			$this->AltBody	= $this->nohtmlmssg;
		}
		
		if (DEBUG == 4){
			pd($this->Body);
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
			$this->Mailer	= 'mail';
		}
		$sendput			= $this->Send();
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
?>