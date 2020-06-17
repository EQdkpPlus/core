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
	die('Do not access this file directly.');
}

	class plus_debug_logger extends gen_class {

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

		private $date = false;

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
		private $fatals = array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);

		private $debug2errorlevel = array();

		public $runtime_call_num = 0;

		public function __construct($root_path=false, $file_logging=true) {
			if($root_path) $this->root_path = $root_path;

			$this->date = date('Y-m-d');
			$this->do_file_logging = $file_logging;
			$this->register_type("unknown", null, null, array(0,1,2,3,4), true);
			$this->register_type("php_error", array($this, 'php_error_pt_formatter'), array($this, 'php_error_html_formatter'), array(3,4), true, true);
			$this->register_type("deprecated", null, array($this, 'deprecated_html_formatter'), array(3,4));
			$this->register_type("fatal_error", array($this, 'fatal_error_pt_formatter'), array($this, 'fatal_error_html_formatter'), array(3,4), true, true);

			$this->php_error_reporting = intval(ini_get("error_reporting"));
			$this->eqdkp_cwd = getcwd();
			if($this->do_file_logging) {
				$this->logfile_folder = $this->pfh->FolderPath('tmp', "");
				if(!is_writable($this->logfile_folder))
					$this->do_file_logging = false;
			}

			//register_shutdown_function(array($this, "catch_fatals"));
			if($this->do_file_logging) {
				$this->logfile_info = unserialize(@file_get_contents($this->logfile_folder.'info.data'), array('allowed_classes' => false));
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
				1	=> E_ERROR,
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
				return true;

			// create error message
			if (array_key_exists($errno, $this->errorType)){
				$err = $this->errorType[$errno];
			}else{
				$err = 'UNKNOWN';
			}

			//we filter some errors, don't think thats the best way to do it
			if(substr($errfile, -16) == "config.class.php" && $errline == 154 && $errno == 8) return false;

			//so lets do it better and use the set php error reporting level from common..
			if( $errno & $this->php_error_reporting ){
				$this->log('php_error', $err, $errno, $errstr, $errfile, $errline);
			}
			//give it back to the original php error handler
			return (defined('NO_PHP_LOGGING')) ? true : false;
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

		public function add_logfile_entry($type, $text){
			$msg = date('d.m.Y H:i:s') . "\t" . $text ."\n";
			clearstatcache();
			$strLogfile = ($this->known_types[$type]['date_files']) ? $this->logfile_folder.$this->date.'-'.$type.'.log': $this->logfile_folder.$type.'.log';
			if(file_exists($strLogfile)){
				$size = @filesize($strLogfile);
			} else $size = 0;
			$this->logfile_info[$type][] = $size;
			@error_log($msg, 3, $strLogfile);
			$this->logfile_info_changed = true;
		}

		public function register_type($type, $plaintext_format_function = null, $html_format_function = null, $loglevels = null, $log_to_file = false, $blnDateFiles=false){
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
			$this->known_types[$type]['date_files'] = $blnDateFiles;
		}

		public function type_known($type){
			if(array_key_exists($type, $this->known_types)){
				return true;
			}else{
				return false;
			}
		}

		public function should_log($type){
			if(array_key_exists($type, $this->known_types)){
				if( $this->debug_level == -1 || in_array($this->debug_level, $this->known_types[$type]['loglevel']) ){
					return true;
				}
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

		// pdl html format function for sql errors
		public function fatal_error_html_formatter($log_entry) {
			$text =  '<b>Internal error code: </b>'		. $log_entry['args'][0] . '<br /><br />
			<b>Message:</b> '		. $log_entry['args'][2] . '<br /><br />
			<b>Code:</b>'			. $log_entry['args'][3] . '<br />
			<b>Backtrace:</b>'		. $log_entry['args'][4] . '<br />';
			return $text;
		}

		// pdl plaintext (logfile) format function for sql errors
		public function fatal_error_pt_formatter($log_entry) {
			$text =  '>>>> '.$log_entry['args'][0]." <<<<\t\n";
			$text .= "Type: "	. $log_entry['args'][1] . "\t\n";
			$text .= "Message: "	. $log_entry['args'][2] . "\t\n";
			$text .= "Code: "		. $log_entry['args'][3] . "\t\n";
			$text .= "Trace:\n";

			if (count($log_entry['args'][4])){
				$trace = $log_entry['args'][4];
			} else {
				$trace = $log_entry['args'][5];
			}
			if(is_array($trace)){
				foreach($trace as $ddata) {
					$text .=	'File: '.$ddata['file'].', Line: '.$ddata['line'].', Function: '.$ddata['function'];
					if(isset($ddata['object'])) $text .= ', Object: '.get_class($ddata['object']);
					$text .= "\n";
				}
			} else {
				$text .= $trace;
			}

			$text .= " <<<<\n";
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


		public function get_logfiles($blnRealFiles=false){
			$arrFiles = scandir($this->logfile_folder);
			$arrLogFiles = array();
			foreach($arrFiles as $file){
				if(valid_folder($file) && $file != "info.data" && $file != "unknown.log"){
					if(!$blnRealFiles && strpos($file, '20')===0) $file = substr($file, 11);
					$arrLogFiles[] = $file;
				}
			}
			return array_unique($arrLogFiles);
		}

		public function search_fatal_error_id($strErrorID){
			$arrLogFiles = $this->get_logfiles(true);
			$arrMatches = array();
			$exception = array();
			$blnRecord=false;

			foreach($arrLogFiles as $logfile){
				$handle = fopen($this->logfile_folder.'/'.$logfile, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);

						if(preg_match('/>>>> (.*?) <<<</', $buffer, $arrMatches)){
							if($arrMatches[1] === $strErrorID){
								$blnRecord = true;
							} else {
								$blnRecord = false;
							}
						}

						if($blnRecord){
							if(count($exception) && preg_match("/<<<</", $buffer)){
								unset($exception[0]);
								return array(
									'file'		=> $logfile,
									'error'		=> implode("", $exception),
								);

							}

							$exception[] = $buffer;
						}
					}
					fclose($handle);
				}
			}
			return false;
		}


		//return logs from "behind", so most recent first
		public function get_file_log($error_type, $number=25, $start=0){
			//if Datefile, use another method
			if(isset($this->known_types[$error_type]['date_files']) && $this->known_types[$error_type]['date_files']) return $this->get_datefile_log($error_type, $number, $start);

			$file = $this->logfile_folder.'/'.$error_type.'.log';

			$regexp = '/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/';
			$count = 0;
			$blnRecord = "false";
			$exceptions = array();

			if(file_exists($file)){
				//Read total number
				$handle = fopen($file, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);

						if(preg_match($regexp, $buffer)){
							$count++;
						}
					}
					fclose($handle);
				}

				//now get the entries
				$s = $count-$start-$number;
				if($s < 0) $s=0;
				$e = $count-$start;
				$i = 0;

				$handle = fopen($file, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle, 4096);

						if(preg_match($regexp, $buffer)){
							if($i >= $s && $i < $e){
								$blnRecord = true;
							} else {
								$blnRecord = false;
							}
							$i++;
						}

						if($blnRecord){
							$exceptions[] = $buffer;
						}
					}
					fclose($handle);
				}
			}

			$strException = implode("", $exceptions);

			$arrSplitted = preg_split($regexp, $strException, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

			$arrSplitted = array_reverse($arrSplitted);

			if(is_array($arrSplitted) && $count){
				return array('entries' => $arrSplitted, 'count' => $count);
			}
			return array('entries' => array(), 'count' => 0);
		}


		/**
		 * return logs from "behind", so most recent first
		 * for date logfiles
		 *
		 * @param unknown $error_type
		 * @param number $number
		 * @param number $start
		 * @return number[]|number[]
		 */
		public function get_datefile_log($error_type, $number=25, $start=0){
			$arrLogFiles = $this->get_logfiles(true);
			$arrTimes = $arrLogs = array();
			foreach($arrLogFiles as $strLogfile){
				if(strpos($strLogfile, '20') === 0 && strpos($strLogfile, $error_type) !== false){
					$arrTimes[] = strtotime(substr($strLogfile, 0, 10));
					$arrLogs[] = $strLogfile;
				} else continue;
			}

			array_multisort($arrTimes, SORT_NUMERIC, SORT_DESC, $arrLogs);


			$count = 0;
			$blnRecord = "false";
			$exceptions = $arrOut = array();
			$regexp = '/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/';

			foreach($arrLogs as $strLogfile){

				$file = $this->logfile_folder.'/'.$strLogfile;

				if(file_exists($file)){
					//Read total number
					$handle = fopen($file, "r");
					if ($handle) {
						while (!feof($handle)) {
							$buffer = fgets($handle, 4096);

							if(preg_match($regexp, $buffer)){
								$count++;
							}
						}
						fclose($handle);
					}

					//now get the entries
					$s = $count-$start-$number;
					if($s < 0) $s=0;
					$e = $count-$start;
					$i = 0;

					$handle = fopen($file, "r");
					if ($handle) {
						while (!feof($handle)) {
							$buffer = fgets($handle, 4096);

							if(preg_match($regexp, $buffer)){
								if($i >= $s && $i < $e){
									$blnRecord = true;
								} else {
									$blnRecord = false;
								}
								$i++;
							}

							if($blnRecord){
								$exceptions[] = $buffer;
							}
						}
						fclose($handle);
					}
				}

				$strException = implode("", $exceptions);

				$arrSplitted = preg_split($regexp, $strException, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

				$arrSplittedReversed = array_reverse($arrSplitted);
				$arrOut = array_merge($arrOut, $arrSplittedReversed);
			}


			if(is_array($arrOut) && $count){
				return array('entries' => $arrOut, 'count' => $count);
			}
			return array('entries' => array(), 'count' => 0);
		}

		public function delete_logfile($type) {
			$this->pfh->Delete($this->logfile_folder.$type.((strpos($type, '.log') !== false) ? '' : '.log'));
			unset($this->logfile_info[$type]);
			$this->logfile_info_changed = true;
			return true;
		}

		private function error_message_header($strErrorName = 'Fatal Error'){
			return '<!DOCTYPE html>
						<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

							<title>EQdkp Plus - '.$strErrorName.'</title>
							<style type="text/css">
							/* body */
							html {
								height: 100%;
							}

							body {
								background: #F5F5F5; /* Old browsers */
								font-size: 14px;
								font-family: \'Segoe UI\', \'Lucida Grande\', \'Helvetica Neue\', Helvetica, Arial, sans-serif;
								color: #000000;
								padding:0;
							  	margin:0;
								height: 100%;
								line-height: 20px;
							}

							.wrapper{

							}

							.header {
								padding-top: 20px;
								font-size: 45px;
								font-weight: 300;
								text-shadow: 1px 1px 2px #fff;
								filter: dropshadow(color=#fff, offx=1, offy=1);
								border: none;
								color:  #fff;
								text-align:center;
								vertical-align: middle;
								background-color: #2e78b0;
								height: 60px;
							}

							.header img {
								display: none;
							}

							.footer {
								margin-top: 10px;
								color: #000;
								text-align: center;
								font-size: 11px;
							}

							.footer a, .footer a:link, .footer a:visited {
								color: #000;
								text-decoration: none;
							}

							.footer a:hover {
								text-decoration: underline;
							}

							.innerWrapper {
								margin-right: auto;
								margin-left: auto;
								padding: 10px;
								margin-bottom: 20px;
								margin-top: 10px;
								width: 700px;
							}

							h1, h2, h3 {
								font-family: \'Trebuchet MS\',Arial,sans-serif;
							    font-weight: bold;
							    margin-bottom: 10px;
							    padding-bottom: 5px;
								border-bottom: 1px solid #CCCCCC;
								margin-top: 5px;
							}

							h1 {
							    font-size: 20px;
							}

							h2 {
								font-size: 18px;
							}

							h3 {
								font-size: 14px;
								border-bottom: none;
								margin-bottom: 5px;
							}

							/* Links */
							a,a:link,a:active,a:visited {
								color: #4E7FA8;
								text-decoration: none;
							}

							a:hover {
								color: #000;
								text-decoration: none;
							}

							.inlineCode {
							    background-color: #ffffff;
							    border: 1px solid #cccccc;
							    border-radius: 3px;
							    display: inline-block;
							    font-family: Consolas, \'Courier New\', monospace;
							    margin: 0 2px;
							    padding: 0 5px;
							    word-break: break-all;
							    word-wrap: break-word;
							}

						</style>
						</head>

						<body>

						<div class="wrapper">
							<div class="header">
								EQdkp Plus '.$strErrorName.'
							</div>

							<div class="innerWrapper">
								<h1>A '.$strErrorName.' has occured</h1><br />


';
		}

		private function error_message_footer($blnShowEQdkpLink = true){
			$out = '';

			if($this->debug_level > 2){
				$out .= '<br /><br /><h1>System information</h1>
					<ul><li>PHP Version: '.phpversion().'</li>
					<li>EQdkp Plus Version: '.VERSION_INT.'</li>
					</ul>';
			}

			$out .= '</div>

					</div>
					<div class="footer">
						<a href="'.EQDKP_PROJECT_URL.'" target="_new">EQDKP Plus</a> &copy; 2003 - '.date('Y').' by EQDKP Plus Developer Team
					</div>
					</body>
					</html>';

			return $out;
		}

		public function catch_fatals(){
			chdir($this->eqdkp_cwd);

			if ($error = error_get_last()) {
				if (isset($error['type']) && in_array($error['type'], $this->fatals)) {
					while (ob_get_level()) {
						ob_end_clean();
					}

					if (!headers_sent()){
						header('HTTP/1.1 500 Internal Server Error');
					}

					//log and output
					$this->myErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
					$output = $this->error_message_header();

					$strErrorID = str_replace('-', '', $this->date).':'.md5(randomID());

					//template errors
					if ($error['type'] == 4 && strpos($error['file'], 'template.class.php') && strpos($error['file'], ": eval()'d code")){
						$this->log('fatal_error', $strErrorID, 'Template Error', $error['message'], 'File: '.$error['file'].', Line: '.$error['line'], array(), register('tpl')->get_error_details());
					} else {
						$this->log('fatal_error', $strErrorID, ((isset($this->errorType[$error['type']]))?$this->errorType[$error['type']]:'unknown'), $error['message'], 'File: '.$error['file'].', Line: '.$error['line'], array(), debug_backtrace());
					}

					$output = $this->error_message_header('Fatal error');
					$output .= 'Internal error code: <span class="inlineCode">'.$strErrorID.'</span><br /><br />';
					if($this->debug_level > 2){

						//template errors
						if ($error['type'] == 4 && strpos($error['file'], 'template.class.php') && strpos($error['file'], ": eval()'d code")){

							echo register('tpl')->generate_error('
								You have a parsing error in a template file.<br /> Please see "Body-File" and "Path" for getting the files responsible for this error.<br />
								If the bugged template-file is located in data-folder, you can fix this error by deleting this file. Otherwise you should restore the original template file.
							');
							exit();
						}

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
					} else {
						$strLogFolder = ($this->table_prefix != "" && $this->dbname != "") ? ((defined('INSTALLED_VERSION')) ? '' : md5($this->table_prefix.$this->dbname)) : '<HASH>/';
						$output .= 'Please forward the above error code to the site administrator.<br /><br /> The error code can be used by an administrator to lookup the full error message in the Administration Control Panel via <span class="inlineCode">Logs >> Errors</span>.
								In addition, the error has been written to the log file located at <span class="inlineCode">data/'.$strLogFolder.'/tmp/'.$this->date.'-fatal_error.log</span> and can be accessed with a FTP program or similar.';
					}
					$output .= $this->error_message_footer();
					echo $output;
				}
			}
		}

		public function catch_dbal_exception($e){
			$strErrorID = str_replace('-', '', $this->date).':'.md5(time().'dbal_exception'.$e->getMessage());
			$this->log('fatal_error', $strErrorID, 'DBAL Exception', $e->getMessage(), $e->getCode(), $e->getTrace(), debug_backtrace());

			if (!headers_sent()){
				header('HTTP/1.1 500 Internal Server Error');
			}
			$output = $this->error_message_header('DBAL Exception');
			$output .= 'A fatal error with the Database occured.<br /><br />';
			$output .= 'Internal error code: <span class="inlineCode">'.$strErrorID.'</span><br /><br />';

			if($this->debug_level > 2){
				$strErrorMessage = '<b>Error Message:</b><br />'.$e->getMessage();
				$strErrorMessage = str_replace($this->dbpass, '*******', $strErrorMessage);
				if (strlen($this->dbuser) > 3){
					$strSuffix = substr($this->dbuser, 0, 3);
					$strUserReplace = str_pad($strSuffix, strlen($this->dbuser), '*');
				}
				$strErrorMessage = str_replace($this->dbuser, $strUserReplace, $strErrorMessage);
				if (strlen($this->dbhost) > 6){
					$strSuffix = substr($this->dbhost, 0, 6);
					$strHostReplace = str_pad($strSuffix, strlen($this->dbhost), '*');
				}
				$strErrorMessage = str_replace($this->dbhost, $strHostReplace, $strErrorMessage);

				$output .= $strErrorMessage.'<br /><br />';
			} else {
				$strLogFolder = ($this->table_prefix != "" && $this->dbname != "") ? ((defined('INSTALLED_VERSION')) ? '' : md5($this->table_prefix.$this->dbname)) : '<HASH>/';
				$output .= 'Please forward the above error code to the site administrator.<br /><br /> The error code can be used by an administrator to lookup the full error message in the Administration Control Panel via <span class="inlineCode">Logs >> Errors</span>.
								In addition, the error has been written to the log file located at <span class="inlineCode">data/'.$strLogFolder.'tmp/'.$this->date.'-fatal_error.log</span> and can be accessed with a FTP program or similar.<br />Notice: The error code was randomly generated and has no use beyond looking up the full message.';
			}

			$output .= $this->error_message_footer(false);
			echo $output;
			//Die, otherwise the next fatal error will occure
			die();
		}

		public function class_doesnt_exist($class) {
			if (!headers_sent()){
				header('HTTP/1.1 500 Internal Server Error');
			}
			if(is_object($class)) $class = get_class($class);
			
			$strErrorID = str_replace('-', '', $this->date).':'.md5(time().'class_404'.$class);
			$this->log('fatal_error', $strErrorID, 'Classloading Error', 'Class "'.$class.'" not found', 404, array(), debug_backtrace());
			$output = $this->error_message_header('Fatal error');
			$output .= 'A fatal error occured.<br /><br />';
			$output .= 'Internal error code: <span class="inlineCode">'.$strErrorID.'</span><br /><br />';
			if($this->debug_level > 2){
				$error_message = "Error while loading class <b>'".$class."'</b>: class not found!<br /><br /><b>Debug Backtrace:</b><br />";
				$data = debug_backtrace();
				$pos = strrpos($data[max(array_keys($data))]['file'], '/');
				if($pos === false) $pos = strrpos($data[max(array_keys($data))]['file'], '\\');
				foreach($data as $key => $call) {
					$file = substr($call['file'], $pos);
					$error_message .= $file.": ".$call['line']."<br />";
				}
				$output .= $error_message;
			} else {
				$strLogFolder = ($this->table_prefix != "" && $this->dbname != "") ? ((defined('INSTALLED_VERSION')) ? '' : md5($this->table_prefix.$this->dbname)) : '<HASH>/';
				$output .= 'Please forward the above error code to the site administrator.<br /><br /> The error code can be used by an administrator to lookup the full error message in the Administration Control Panel via <span class="inlineCode">Logs >> Errors</span>.
								In addition, the error has been written to the log file located at <span class="inlineCode">data/'.$strLogFolder.'tmp/'.$this->date.'-fatal_error.log</span> and can be accessed with a FTP program or similar.<br />Notice: The error code was randomly generated and has no use beyond looking up the full message.';

			}
			echo $output.$this->error_message_footer();
			//Die, otherwise the next fatal error will occure
			die();
		}

		public function file_not_found($path) {
			if (!headers_sent()){
				header('HTTP/1.1 500 Internal Server Error');
			}
			$strErrorID = str_replace('-', '', $this->date).':'.md5(time().'file_404'.$path);
			$this->log('fatal_error', $strErrorID, 'Fileloading Error', 'File "'.$path.'" not found', 404, array(), debug_backtrace());
			$output = $this->error_message_header('Fatal error');
			$output .= 'A fatal error occured.<br /><br />';
			$output .= 'Internal error code: <span class="inlineCode">'.$strErrorID.'</span><br /><br />';
			if($this->debug_level > 2){
				$error_message = "Error while loading file <b>'".$path."'</b>: File not found!<br />";
				$error_message .= "Please ensure that all files are uploaded correctly!";
				if($this->debug_level) {
					$error_message .= "<br /><br /><b>Debug Backtrace:</b><br />";
					$data = debug_backtrace();
					$pos = strrpos($data[max(array_keys($data))]['file'], '/');
					if($pos === false) $pos = strrpos($data[max(array_keys($data))]['file'], '\\');
					foreach($data as $key => $call) {
						$file = substr($call['file'], $pos);
						$error_message .= $file.": ".$call['line']."<br />";
					}
				}
				$output .= $error_message;
			} else {
				$strLogFolder = ($this->table_prefix != "" && $this->dbname != "") ? ((defined('INSTALLED_VERSION')) ? '' : md5($this->table_prefix.$this->dbname)) : '<HASH>/';
				$output .= 'Please forward the above error code to the site administrator.<br /><br /> The error code can be used by an administrator to lookup the full error message in the Administration Control Panel via <span class="inlineCode">Logs >> Errors</span>.
								In addition, the error has been written to the log file located at <span class="inlineCode">data/'.$strLogFolder.'tmp/'.$this->date.'-fatal_error.log</span> and can be accessed with a FTP program or similar.';
			}
			echo $output.$this->error_message_footer();
			//Die, otherwise the next fatal error will occure
			die();
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
