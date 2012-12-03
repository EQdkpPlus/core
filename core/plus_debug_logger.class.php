<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

if ( !class_exists( "plus_debug_logger" ) ) {
  class plus_debug_logger{
    private $log = array();
    private $known_types = array();
    private $default_loglevels = array(3);
    private $debug_level = -1;
    private $php_error_reporting = E_ALL;
    private $eqdkp_cwd = './';
    private $logfile_folder = './';
    private $do_file_logging = true;
    
    private $errorType = array(
    	  E_ERROR               => 'ERROR',
    	  E_WARNING             => 'WARNING',
    	  E_PARSE               => 'PARSING ERROR',
    	  E_NOTICE              => 'NOTICE',
    	  E_CORE_ERROR          => 'CORE ERROR',
    	  E_CORE_WARNING        => 'CORE WARNING',
    	  E_COMPILE_ERROR       => 'COMPILE ERROR',
    	  E_COMPILE_WARNING     => 'COMPILE WARNING',
    	  E_USER_ERROR          => 'USER ERROR',
    	  E_USER_WARNING        => 'USER WARNING',
    	  E_USER_NOTICE         => 'USER NOTICE',
    	  E_STRICT              => 'STRICT NOTICE',
    	  E_RECOVERABLE_ERROR   => 'RECOVERABLE ERROR',
    	  E_DEPRECATED          => 'DEPRECATED NOTICE',
    	  E_USER_DEPRECATED     => 'USER DEPRECATED NOTICE',
    	);

    public function __construct(){
      global $eqdkp_root_path;
      $this->register_type("unknown", null, null, array(0,1,2,3), true);
      $this->register_type("php_error", array($this, 'php_error_pt_formatter'), array($this, 'php_error_html_formatter'), array(3), true);
      
      $this->php_error_reporting = intval(ini_get("error_reporting"));
      $this->eqdkp_cwd = getcwd();
      $this->logfile_folder = $eqdkp_root_path.'data/';
      if(!is_writable($this->logfile_folder))
        $this->do_file_logging = false;
      //register_shutdown_function(array($this, "catch_fatals"));
    }

    // PHP Error Handler
    public function myErrorHandler($errno, $errstr, $errfile, $errline){
      //don't show suppressed (@) errors
      if(intval(ini_get("error_reporting")) == 0)
        return false;
      
      // create error message
      if (array_key_exists($errno, $this->errorType)){
        $err = $this->errorType[$errno];
      }else{
        $err = 'UNKNOWN';
      }

      //we filter some errors, don't think thats the best way to do it
      //if(substr($errfile, -9) != 'mysql.php' && substr($errfile, -20) != 'config_itemstats.php' && $errno != E_STRICT && $errno != E_NOTICE && (substr($errfile, -10) != 'config.php' && $errline != 275)){
      
      //so lets do it better and use the set php error reporting level from common..
	    if( $errno & $this->php_error_reporting ){
        $this->log('php_error', $err, $errno, $errstr, $errfile, $errline);
	    }

	    //give it back to the original php error handler
	    return false;
    }
    
    public function set_debug_level($debug_level){
      $this->debug_level = $debug_level;
      foreach(array_keys($this->known_types) as $type){
        if(!in_array($debug_level, $this->known_types[$type]['loglevel'])){
          unset($this->log[$type]);
        }
      }
    }
    
    public function log($type){
      if(!array_key_exists($type, $this->known_types)){
        $type = 'unknown';
      }

      //create our loging entry
      $entry = array(
        'timestamp' => time(),
        'args' => array_slice(func_get_args(), 1),
      );
      
      //if either current loging level is unknown (pre db phase on initialisation) or appropriate for error type => save for output
      if( $this->debug_level == -1 || in_array($this->debug_level, $this->known_types[$type]['loglevel']) ){
        $this->log[$type][] = $entry;
      }			      
             
      //log to file if requested for the error type
      if($this->do_file_logging && $this->known_types[$type]['log_to_file']){
        $this->add_logfile_entry($type, $this->pt_format_log_entry($type, $entry));
      }
    }

    public function add_logfile_entry($type, $text){
      $msg = date('d.m.Y H:i:s') . "\t" . $text ."\n";
      @error_log($msg, 3, $this->logfile_folder.$type.'.log');
    }
    
    public function register_type($type, $plaintext_format_function = null, $html_format_function = null, $loglevels = null, $log_to_file = false){
      if(array_key_exists($type, $this->known_types)){
        message_die("$type already exists!");
      }

      if(isset($html_format_function)){
        $this->known_types[$type]['html'] = $html_format_function;
      }else{
        $this->known_types[$type]['html'] = array($this, 'default_html_formatter');
      }

      if(isset($plaintext_format_function)){
        $this->known_types[$type]['pt'] = $plaintext_format_function;
      }else{
        $this->known_types[$type]['pt'] = array($this, 'default_pt_formatter');
      }

      if(isset($loglevels)){
        $this->known_types[$type]['loglevel'] = $loglevels;
      }else{
        $this->known_types[$type]['loglevel'] = $this->default_loglevels;
      }
      
      $this->known_types[$type]['log_to_file'] = $log_to_file;
    }

    public function type_known($type){
      if(array_key_exists($type, $this->known_types)){
        return true;
      }else{
        return false;
      }
    }

    public function pt_format_log_entry($type, &$log_entry){
      return call_user_func($this->known_types[$type]['pt'], $log_entry);
    }


    public function html_format_log_entry($type, &$log_entry){
      return call_user_func($this->known_types[$type]['html'], $log_entry);
    }

    public function get_log(){
      return $this->log;
    }

    public function get_log_size($loglevel = -1){
      $count = 0;
      if($loglevel == -1){
        foreach($this->log as $type => $entries){
          foreach($entries as $entry){
            $count++;
          }
        }
      }else{
        foreach($this->log as $type => $entries){
          if(in_array($loglevel, $this->known_types[$type]['loglevel'])){
            foreach($entries as $entry){
              $count++;
            }
          }
        }
      }
      return $count;
    }

    public function default_pt_formatter($log_entry){
      $text = '';
      foreach($log_entry['args'] as $value){
        $text .= $value."\t";
      }
      return $text;
    }

    public function default_html_formatter($log_entry){
      $text = '';
      foreach($log_entry['args'] as $value){
        $text .= $value;
      }
      return $text;
    }

    public function php_error_pt_formatter($log_entry){
      $text = $log_entry['args'][0].": File: ".$log_entry['args'][3]." line: ".$log_entry['args'][4]." Error Code: ".$log_entry['args'][1]." Error String: ".$log_entry['args'][2];
  		return $text;
    }

    public function php_error_html_formatter($log_entry){
      $text = "<b>".$log_entry['args'][0]."</b>:<br />
			    	  <b>File:</b> ".$log_entry['args'][3]."<br />
			    	  <b>Line:</b> ".$log_entry['args'][4]."<br />
			    	  <b>Error Code:</b> ".$log_entry['args'][1]."<br />
			    	  <b>Error String:</b> ".$log_entry['args'][2]."<br />";
  		return $text;
    }

    public function get_pt_log($debug_level, $ltype=false){
      $text = "";
      foreach($this->log as $type => $entries){
	    if($type == $ltype OR !$ltype) {
          $text .= "$type:\n";
          if(in_array($debug_level, $this->known_types[$type]['loglevel'])){
            foreach($entries as $entry){
              $text .= call_user_func($this->known_types[$type]['pt'], $entry);
			}
          }
        }
      }
      return $text;
    }

    public function print_log($debug_level){
     echo($this->get_log($debug_level));
    }

    public function get_html_log($debug_level, $ltype=false){
      $text = "";
      foreach($this->log as $type => $entries){
	    if($type == $ltype OR !$ltype) {
          $text .= "$type:<br />";
          if(in_array($debug_level, $this->known_types[$type]['loglevel'])){
            foreach($entries as $entry){
              $text .= call_user_func($this->known_types[$type]['html'], $entry)."<br />";
            }
            $text.="<br />";
		  }
        }
      }
      return $text;
    }

    public function print_html_log($debug_level, $type=false){
      echo($this->get_html_log($debug_level, $type));
    }
    
    public function get_file_log($error_type){
      $file = $this->logfile_folder.'/'.$error_type.'.log';
      if(file_exists($file)){
        $handle = fopen($file, "r");
        $filearray['filesize'] = filesize($file);
        $content = fread($handle, $filearray['filesize']);
        fclose($handle);
        $regexp = '/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/';
        $filearray['entries'] = preg_split($regexp, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $filearray['entrycount'] = count($filearray['entries'])/2;
        return $filearray;
      }else{
        return null;
      }
    }
                                
    public function catch_fatals(){
			global $eqdkp_root_path;
      chdir($this->eqdkp_cwd);
      if ($error = error_get_last()) {
        if (isset($error['type']) && ($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR)) {
          while (ob_get_level()) {
            ob_end_clean();
          } 
          
          if (!headers_sent()){
            header('HTTP/1.1 500 Internal Server Error');
          }
          
          //log and output
          $this->myErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
					$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

  <title>EQdkp Plus - Fatal Error</title>
  <style type="text/css">
	body, html {
	padding:0;
  margin:0;
  font-family: \'Trebuchet MS\', Verdana, sans-serif;
  font-size: 13px;
}
	
	h1{
	font-size: 20px;
  padding: 0px;
  margin: 0px;
	text-decoration:underline;
}
.error_table{
	width: 700px;
	border:1px solid #333;
	background-color:#FFF;
}
  </style>
</head>

<body style="background-color:#efefef">
<div width="100%" align="center"> 
<div class="error_table">
  <table width="400" border="0" cellspacing="1" cellpadding="2" style="background:#336c99 url(\''.$eqdkp_root_path.'templates/maintenance/images/header_back.png\') left no-repeat; font-size: 24px;
  font-weight:bold;
  height:110px;
  width: 100%;
  border: 0px;
  border-bottom:2px solid #333;
  text-transform: uppercase; color:#FFF;">
    <tr>
      <td width="100%">
        <center>
  	      <img src="'.$eqdkp_root_path.'templates/maintenance/images/logo.png" alt="EQdkp Plus" align="absmiddle" /> Fatal Error
        </center>
      </td>
    </tr>
  </table>

 
<div id="cont" align="left" style="margin:15px;">
<h1>A Fatal error occured!</h1><br />
';

foreach ($error as $key=>$value){
  if($key == 'type'){
    $et = (isset($this->errorType[$value]))?$this->errorType[$value]:'unknown';
    $output .= '<b>'.ucfirst($key).'</b>: '.$et.'<br />';
  }else{
    $output .= '<b>'.ucfirst($key).'</b>: '.$value.'<br />';
  }
}

$output .= '
<br /><b><a href="http://www.eqdkp-plus.com">Please visit the EQdkp Plus Homepage!</a></b>
</div>
</div>

<div style="width:700px;">
  <a href="http://www.eqdkp-plus.com" target="_new">EQDKP Plus</a> &copy; 2003 - '.date('Y').' by EQDKP Plus Developer Team
</div>
</div>
</body>
</html>';
					
					echo $output;
        }
      }
    }
    
  }//end class
}//end if
?>