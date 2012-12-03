<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file_upload.php
 * begin: Sat December 21 2002
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

class fileupload{

  private $upload_tmp_dir = "/tmp/";  // leading and trailing slash required
  private $file_upload_flag = "off";
  private $upload_max_filesize = "100";
  private $allowable_upload_base_dirs = array("/tmp/", "/var/www/localhost/");
  private $allowable_upload_tmp_dirs = array( "/tmp/");
  private $upload_dir= "/tmp/";  // leading and trailing slash required
  private $upload_file_name;

  function __construct($name) {
     if( is_null($_FILES[$name]) )  {
         echo "Specified file <strong> ".$name." </strong> does not exist in the FILES array. Please check if it exists";
         echo "Exiting...";
         exit;
     }
     $this->getConfigurationSettings();
     if( $this->file_upload_flag == "off" ) {
       echo "File upload capability in the configuration file is turned <strong> off </strong> . Please update the php.ini file.";
       exit;
     }
     $this->upload_file_name = $name;
  }

  private function getConfigurationSettings() {
     $this->file_upload_flag = ini_get('file_uploads');
     $this->upload_tmp_dir = ini_get('upload_tmp_dir');
     $this->upload_max_filesize = ini_get('upload_max_filesize');
     $this->upload_max_filesize = preg_replace('/M/', '000000', $this->upload_max_filesize);
  }

  public function getErrors() {
     return $_FILES[$this->upload_file_name]['error'];
  }

  public function getFileSize() {
     return $_FILES[$this->upload_file_name]['size'];
  }

  public function getFileName() {
     return $_FILES[$this->upload_file_name]['name'];
  }

  public function getTmpName() {
     return $_FILES[$this->upload_file_name]['tmp_name'];
  }

  public function setUploadDir($upload_dir) {
   trim($upload_dir);
   if( $upload_dir[strlen($upload_dir)-1] != "/" ) $upload_dir .= "/"; // add trailing slash
   $can_upload = false;
   foreach( $this->allowable_upload_base_dirs as $dir ) {
       if( $dir == $upload_dir ) {
     $can_upload = true;
         break;
       }
   }
   if( !$can_upload ) {
       echo "Cannot upload to the dir ->".$upload_dir;
       return;
   }else{
       $this->upload_dir = $upload_dir;
       echo $this->upload_dir;
   }
  }

  public function setTmpUploadDir($upload_tmp_dir) {
   trim($upload_tmp_dir);
   if( $upload_tmp_dir[strlen($upload_tmp_dir)-1] != "/" ) $upload_tmp_dir .= "/"; // add trailing slash
   $can_upload = false;
   foreach( $this->allowable_upload_base_dirs as $dir ) {
       if( $dir == $upload_tmp_dir ) {
     $can_upload = true;
     return;
       }
   }
   if( !$can_upload ) {
       echo "Cannot upload to the dir ->".$uplaod_tmp_dir;
       return;
   }
   $this->upload_tmp_dir = $upload_dir;
  }

  public function uploadFile() {
   if( $this->checkMaxMemorySizeLimit() ) {
       echo "File size of ".$this->getFileSize()." greater than allowable limit of ".$this->upload_max_filesize."Please change the configuration setting.";
       return;
   }else{
     if( !move_uploaded_file($this->getTmpName(), $this->upload_dir.$this->getFileName()) ) {
         echo "Failed to upload file ".$this->getTmpName();
     }
   }
  }

  public function checkMaxMemorySizeLimit() {
   if( $this->getFileSize() >  $this->upload_max_filesize ) {
     return true;
   }else{
     return false;
   }
  }

}

?>
