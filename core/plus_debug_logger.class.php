<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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
	die('Do not access this file directly.');
}

	class plus_debug_logger extends gen_class {
		public static $shortcuts = array('pfh');

		private $log = array();
		private $known_types = array();
		private $default_loglevels = array(3,4);
		private $debug_level = -1;
		private $php_error_reporting = E_ALL;
		private $eqdkp_cwd = './';
		private $logfile_folder = './';
		private $do_file_logging = true;

		private $logfile_info = array();
		private $logfile_info_changed = false;

		private $errorType = array(
			E_ERROR					=> 'ERROR',
			E_WARNING				=> 'WARNING',
			E_PARSE					=> 'PARSING ERROR',
			E_NOTICE				=> 'NOTICE',
			E_CORE_ERROR			=> 'CORE ERROR',
			E_CORE_WARNING			=> 'CORE WARNING',
			E_COMPILE_ERROR			=> 'COMPILE ERROR',
			E_COMPILE_WARNING		=> 'COMPILE WARNING',
			E_USER_ERROR			=> 'USER ERROR',
			E_USER_WARNING			=> 'USER WARNING',
			E_USER_NOTICE			=> 'USER NOTICE',
			E_STRICT				=> 'STRICT NOTICE',
			E_RECOVERABLE_ERROR		=> 'RECOVERABLE ERROR',
			E_DEPRECATED			=> 'DEPRECATED NOTICE',
			E_USER_DEPRECATED		=> 'USER DEPRECATED NOTICE',
		);

		private $debug2errorlevel = array();

		public $runtime_call_num = 0;

		public function __construct($root_path=false, $file_logging=true) {
			if($root_path) $this->root_path = $root_path;

			$this->do_file_logging = $file_logging;
			$this->register_type("unknown", null, null, array(0,1,2,3,4), true);
			$this->register_type("php_error", array($this, 'php_error_pt_formatter'), array($this, 'php_error_html_formatter'), array(3,4), true);
			$this->register_type("deprecated", null, array($this, 'deprecated_html_formatter'), array(3,4));

			$this->php_error_reporting = intval(ini_get("error_reporting"));
			$this->eqdkp_cwd = getcwd();
			if($this->do_file_logging) {
				$this->logfile_folder = $this->pfh->FolderPath('tmp', "");
				if(!is_writable($this->logfile_folder))
					$this->do_file_logging = false;
			}
				//register_shutdown_function(array($this, "catch_fatals"));
			if($this->do_file_logging) {
				$this->logfile_info = unserialize(@file_get_contents($this->logfile_folder.'info.data'));
				if (is_array($this->logfile_info)){
					foreach($this->logfile_info as $error_type => $data) {
						$file = $this->logfile_folder.$error_type.'.log';
						$max_key = max(array_keys($data));
						if(!is_file($file) OR (is_file($file) AND filesize($file) < $data[$max_key])) $this->delete_logfile($error_type);
					}
				}
			}
			$this->debug2errorlevel = array(
				0	=> E_ERROR,
				1	=> E_WARNING | E_ERROR,
				2	=> E_WARNING | E_ERROR,
				3	=> E_ALL & ~(E_NOTICE | E_USER_NOTICE),
				4 	=> E_ALL
			);
		}

		public function __destruct() {
			if($this->do_file_logging AND $this->logfile_info_changed) {
				file_put_contents($this->logfile_folder.'info.data', serialize($this->logfile_info));
			}
		}

		// PHP Error Handler
		public function myErrorHandler($errno, $errstr, $errfile, $errline){
			//don't show suppressed (@) errors
			if($this->php_error_reporting == 0)
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
			$this->php_error_reporting = isset($this->debug2errorlevel[$debug_level]) ? $this->debug2errorlevel[$debug_level] : E_ALL;
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
				'timestamp'		=> time(),
				'args'			=> array_slice(func_get_args(), 1),
				'script_time'	=> round(microtime(true) - $this->scriptstart, 5)
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

		// TODO: this does not work with data folder chmod 655! use pfh!
		// should work now, log-file folder now data/{hash}/tmp
		public function add_logfile_entry($type, $text){
			$msg = date('d.m.Y H:i:s') . "\t" . $text ."\n";
			clearstatcache();
			$size = @filesize($this->logfile_folder.$type.'.log');
			$this->logfile_info[$type][] = ($size) ? $size : 0;
			@error_log($msg, 3, $this->logfile_folder.$type.'.log');
			$this->logfile_info_changed = true;
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

		public function deprecated_html_formatter($log_entry) {
			$text = "<b>Use of deprecated function/method</b>:<br />
					<b>Name:</b> ".$log_entry['args'][0]."<br />
					<b>defined:</b> File ".$log_entry['args'][1].", line ".$log_entry['args'][2]."<br />
					<b>called:</b> File ".$log_entry['args'][3].", line ".$log_entry['args'][4]."<br />";
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

		//return logs from "behind", so most recent first
		public function get_file_log($error_type, $number=0, $start=0){
			$file = $this->logfile_folder.'/'.$error_type.'.log';
			$max_entries = (isset($this->logfile_info[$error_type])) ? count($this->logfile_info[$error_type]) : 0;
			if (($max_entries - $start) < 0) return array();
			if(file_exists($file)){
				$filearray['filesize'] = filesize($file);
				$length = $filearray['filesize'];
				//size where log start saved, so add one to get end of previous log
				if($start AND isset($this->logfile_info[$error_type][$max_entries - $start + 1])) $length = $this->logfile_info[$error_type][$max_entries - $start + 1];
				$offset = 0;
				if($number AND $number < ($max_entries - $start) AND isset($this->logfile_info[$error_type][$max_entries -$start +1 -$number])) {
					$offset = $this->logfile_info[$error_type][$max_entries - $start + 1 - $number];
					$length -= $offset;
				}
				
				$handle = fopen($file, "r+");
				if($offset) fseek($handle, $offset);
				$content = fread($handle, $length);
				fclose($handle);
				$regexp = '/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/';
				$filearray['entries'] = preg_split($regexp, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$filearray['entrycount'] = $max_entries;
				return $filearray;
			}else{
				return null;
			}
		}

		public function delete_logfile($type) {
			$this->pfh->Delete($this->logfile_folder.$type.'.log');
			unset($this->logfile_info[$type]);
			$this->logfile_info_changed = true;
			return true;
		}

		public function catch_fatals(){
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
						<div style="width:100%" align="center">
						<div class="error_table">
							<table width="400" border="0" cellspacing="1" cellpadding="2" style="background:#336c99 url(\''.$this->root_path.'templates/maintenance/images/header_back.png\') left no-repeat; font-size: 24px;
							font-weight:bold;
								height:110px;
								width: 100%;
								border: 0px;
								border-bottom:2px solid #333;
								text-transform: uppercase; color:#FFF;">
									<tr>
										<td width="100%">
											<center>
												<img src="'.$this->root_path.'templates/maintenance/images/logo.png" alt="EQdkp Plus" class="absmiddle" /> Fatal Error
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
						}elseif($key == 'message'){
							$slash = (strpos($value, '/') !== false) ? '/' : '\\';
							$real_directory = str_replace($slash.'core', '', realpath(dirname(__FILE__)));
							$msg = str_replace("\n", "<br />\n", $value);
							$msg = str_replace("Stack trace", "<b>Stack Trace</b>", $msg);
							$msg = str_replace($this->dbpass, '*******', $msg);
							$msg = str_replace($real_directory, '.....', $msg);
							$output .= '<b>'.ucfirst($key).'</b>: '.$msg.'<br />';
						}elseif($key == 'file') {
							$slash = (strpos($value, '/') !== false) ? '/' : '\\';
							$real_directory = str_replace($slash.'core', '', realpath(dirname(__FILE__)));
							$value = str_replace($real_directory, '.....', $value);
							$output .= '<b>File</b>: '.$value.'<br />';
						} else {
							$output .= '<b>'.ucfirst($key).'</b>: '.$value.'<br />';
						}
					}
					$output .= '
								<br /><b><a href="'.EQDKP_PROJECT_URL.'">Please visit the EQdkp Plus Homepage!</a></b>
								</div>
								</div>
								<div style="width:700px;">
								<a href="'.EQDKP_PROJECT_URL.'" target="_new">EQDKP Plus</a> &copy; 2003 - '.date('Y').' by EQDKP Plus Developer Team
								</div>
								</div>
								</body>
								</html>';
					echo $output;
				}
			}
		}

		/*
		 * Debug Helper Functions
		 */

		/*
		 * Init Tab in Debug-Tab, where output is shown
		 */
		private function init_debug() {
			if(!$this->type_known('debug_out')) {
				$this->register_type('debug_out', null, null, array(2,3,4));
			}
		}

		/*
		 * @mixed $variable		Outputs content of the variable into Debug-Tab, if == 'backtrace' post info of debug_backtrace
		 * @boolean $die		Directly output and die()
		 */
		public function debug($variable='backtrace', $die=false) {
			$this->init_debug();
			//first show script-runtime
			$out = '<i>'.$this->runtime('', true).'</i>: ';
			if($variable === 'backtrace') {
				$debug_data = debug_backtrace();
				foreach($debug_data as $ddata) {
					$out .=	'<br />File: '.$ddata['file'].', Line: '.$ddata['line'].', Function: '.$ddata['function'];
					if(isset($ddata['object'])) $out .= ', Object: '.get_class($ddata['object']);
				}
				unset($debug_data);
			} else {
				$out .= $this->format_var($variable);
			}
			if($die) die($out);
			$this->log('debug_out', $out);
		}

		/*
		 * @string/@int $info	What should be outputted after time, defaults to numbers
		 */
		public function runtime($info='', $return=false, $return_plain=false) {
			$this->runtime_call_num++;
			$time = round(microtime(true) - $this->scriptstart, 5);
			if($return_plain) return $time;
			$time = $this->format_runtime($time);
			$this->init_debug();
			if(!$info) $info = $this->runtime_call_num;
			if($return > 1) return $time;
			if($return > 0) return $time.' - cur: '.$this->memory_usage().' - peak: '.$this->memory_usage(true);
			$this->log('debug_out', '<i>'.$time.'</i> - <i>cur: '.$this->memory_usage().'</i> - <i>peak: '.$this->memory_usage(true).'</i>: '.$info);
		}

		public function format_runtime($time) {
			return (strpos($time, '0') === 0) ? ($time*1000).'ms' : '&nbsp;'.$time.'s';
		}

		public function memory_usage($peak=false) {
			$memory = $fmemory = ($peak) ? memory_get_peak_usage() : memory_get_usage();
			if($memory/1024 > 1) $fmemory = round($memory/1024, 4) .'kB';
			if($memory/(1024*1024) > 1) $fmemory = round($memory/(1024*1024), 4) .'MB';
			return $fmemory;
		}

		public function format_var($var) {
			$out = gettype($var);
			if(is_object($var)) return $this->format_object($var);
			if(is_array($var)) return $this->format_array($var);
			if(is_string($var)) $out .= '('.strlen($var).')';
			if(is_bool($var)) $var = ($var) ? 'true' : 'false';
			if(is_resource($var)) $out .= 'resource of type '.get_resource_type($var);
			if(is_null($var)) return 'NULL';
			return $out.' "'.htmlspecialchars($var).'"';
		}

		public function format_array($array, $offset='') {
			$out = 'array('.count($array).') {<br />';
			foreach($array as $key => $value) {
				$out .= $offset."&nbsp;&nbsp;&nbsp;&nbsp;[".$this->format_var($key).'] => ';
				if(is_array($value)) {
					$out .= $this->format_array($value, $offset.'&nbsp;&nbsp;&nbsp;&nbsp;').'<br />';
				} else {
					$out .= $this->format_var($value).'<br />';
				}
			}
			return $out.$offset.'}';
		}

		public function format_object($obj) {
			return 'object: '.get_class($obj);
		}

		public function deprecated($name) {
			$backtrace = debug_backtrace();
			$this->log('deprecated', $name, $backtrace[2]['file'], $backtrace[2]['line'], $backtrace[1]['file'], $backtrace[1]['line']);
		}
	}//end class
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_plus_debug_logger', plus_debug_logger::$shortcuts);
?>