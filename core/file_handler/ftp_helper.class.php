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

class ftp_handler{
	private $link_id		= '';		// FTP handler
	private $is_login		= '';		// The ftp login handle
	private $root_dir		= '';		// FTP root directory
	private $tmp_dir		= '';		// Temporary files directory
	private $tmp_dir_plain	= '';		// Temporary files directory without root path
	private $error			= '';		// Error Messages
	private $host, $port, $user, $pass, $dir, $rootdir, $eqdkp_root = '';

	public function __construct($user="Anonymous", $pass="Email", $host="localhost", $port="21", $dologin=true){
		if($host){
			$this->host = $host;
		}
		if($port){
			$this->port = $port;
		}
		if($user){
			$this->user = $user;
		}
		if($pass){
			$this->pass = $pass;
		}

		if($dologin){
			$this->login();
			if(!$this->error){
				$this->rootdir		= $this->pwd();
				$this->dir			= $this->rootdir;
			}else{
				return $this->error;
			}
		}
	}

	public function setTempDir($dir, $dir_plain){
		$this->tmp_dir			= $dir;
		$this->tmp_dir_plain	= $dir_plain;
	}

	public function setRootDir($dir, $eqdkp_root){
		$this->root_dir	= $dir;
		$this->eqdkp_root = $eqdkp_root;
	}

	public function showerror(){
		return $this->error;
	}

	private function login(){
		if(!$this->link_id){
			$this->link_id = ftp_connect($this->host,$this->port,3);
			if(!$this->link_id){
				$this->error = sprintf("can not connect to host: %s:%s", $this->host, $this->port);return;
			}
		}
		if(!$this->is_login){
			$this->is_login = ftp_login($this->link_id, $this->user, $this->pass);
			ftp_pasv($this->link_id, true);
			if(!$this->is_login){
				$this->error = 'FTP login faild. Invaid user or password';return;
			}
		}
	}

	public function systype(){
		return ftp_systype($this->link_id);
	}

	public function pwd(){
		$this->login();
		$dir = ftp_pwd($this->link_id);
		$this->dir = $dir;
		return $dir;
	}

	public function cdup(){
		$this->login();
		$this->cdToHome();
		$isok =  ftp_cdup($this->link_id);
		if($isok){
			$this->dir = $this->pwd();
		}
		return $isok;
	}

	public function cdToHome(){
		$aPath = explode('/',$this->pwd());
		$sHomeDir = str_repeat('../', count($aPath) );

		$this->login();
		$isok = ftp_chdir($this->link_id,$sHomeDir);
	}

	public function cd($dir){
		$this->login();
		$isok = ftp_chdir($this->link_id,$this->root_dir.$dir);
		if($isok){
			$this->dir = $dir;
		}
		return $isok;
	}

	public function nlist($dir=''){
		$this->login();
		$this->cdToHome();
		if(!$dir){
			$dir = ".";
		}
		$arr_dir = ftp_nlist($this->link_id,$this->root_dir.$dir);
		return $arr_dir;
	}

	public function rawlist($dir="/"){
		$this->login();
		$this->cdToHome();
		$arr_dir = ftp_rawlist($this->link_id,$this->root_dir.$dir);
		return $arr_dir;
	}

	public function mkdir($dir){
		$this->login();
		$this->cdToHome();
		return @ftp_mkdir($this->link_id,$this->root_dir.$dir);
	}

	public function mkdir_r($path, $mode=0775){
		$this->login();
		$this->cdToHome();
		$dir	= explode("/", $this->root_dir.$path);
		$path	= "";
		$ret	= true;

		for($i=0; $i<count($dir); $i++){
			$path	.= "/".$dir[$i];
			if(!@ftp_chdir($this->link_id, $path)){
				@ftp_chdir($this->link_id, "/");
				if(!@ftp_mkdir($this->link_id, $path)){
					$ret=false;break;
				}else{
					if(!$this->on_iis()) @ftp_chmod($this->link_id, $mode, $path);
				}
			}
		}
		return $ret;
	}

	public function file_size($file){
		$this->login();
		$this->cdToHome();
		$size = ftp_size($this->link_id,$this->root_dir.$file);
		return $size;
	}

	public function chmod($file, $mode=0666){
		if($this->on_iis()) return true;

		$this->login();
		$this->cdToHome();
		return @ftp_chmod($this->link_id,$mode,$this->root_dir.$file);
	}

	public function delete($remote_file){
		$this->login();
		$this->cdToHome();
		return @ftp_delete($this->link_id,$this->root_dir.$remote_file);
	}

	public function filedate($remote_file){
		$this->login();
		$this->cdToHome();
		$date = ftp_mdtm($this->link_id, $this->root_dir.$remote_file);
		return ($date != -1) ? $date : 'No Date available';
	}

	public function rename($old_file, $new_file, $tmpmove=false){
		if($old_file == $new_file) return true;

		$this->login();
		$this->cdToHome();

		if(defined('PHP_WINDOWS_VERSION_BUILD') && file_exists($this->eqdkp_root.$new_file)){
			$this->delete($new_file);
		}

		if($tmpmove){
			return $this->moveuploadedfile($old_file, $new_file, FTP_BINARY);
		}else{
			if(is_dir($this->eqdkp_root.$old_file)){
				$blnResult = @ftp_rename($this->link_id, $this->root_dir.$old_file, $this->root_dir.$new_file);
			} else {
				$blnResult = $this->ftp_copy($old_file, $new_file);
				$this->delete($old_file);
			}

			return $blnResult;
		}
	}

	public function is_dir($file){
		return (is_dir($this->root_dir.$file)) ? true : false;
	}

	public function ftp_rmdirr($path){
		$this->login();
		$this->cdToHome();
		$ar_files = ftp_nlist($this->link_id, $this->root_dir.$path);
		if (is_array($ar_files)){ // makes sure there are files
			for ($i=0;$i<sizeof($ar_files);$i++){ // for each file
				$st_file = basename($ar_files[$i]);
				if($st_file == '.' || $st_file == '..') continue;

				if (ftp_size($this->link_id, $this->root_dir.$path.'/'.$st_file) == -1){ // check if it is a directory
					$this->ftp_rmdirr($path.'/'.$st_file); // if so, use recursion
				}else ftp_delete($this->link_id, $this->root_dir.$path.'/'.$st_file); // if not, delete the file
			}
		}
		$flag = ftp_rmdir($this->link_id, $this->root_dir.$path); // delete empty directories
		return $flag;
	}

	public function get($local_file, $remote_file, $mode=FTP_BINARY, $startpos=0){
		$this->login();
		$this->cdToHome();
		return ftp_get($this->link_id,$local_file,$this->root_dir.$remote_file,$mode, $startpos);
	}

	private function put_upload($remote_file, $local_file, $mode=FTP_BINARY){
		$this->login();
		$this->cdToHome();
		return ftp_put($this->link_id,$this->root_dir.$remote_file,$local_file,$mode);
	}

	public function moveuploadedfile($tmpfile, $newfile, $mode=FTP_BINARY){
		$result = ftp_put($this->link_id,$this->root_dir.$newfile, $tmpfile, $mode);
		@ftp_delete($this->link_id, $tmpfile);
		if(!$this->on_iis()) @ftp_chmod($this->link_id, 0755, $this->root_dir.$newfile);
		return $result;
	}

	public function put_string($remote_file, $data, $mode=FTP_BINARY, $startpos=0){
		$tmplfilename = $this->tmp_dir.md5($this->generateRandomBytes());
		file_put_contents($tmplfilename, $data);
		$result = $this->put_upload($remote_file, $tmplfilename, $mode);
		if(!$this->on_iis()) @ftp_chmod($this->link_id, 0755, $this->root_dir.$remote_file);
		unlink($tmplfilename);
		return $result;
	}

	public function add_string($remote_file, $data, $mode=FTP_BINARY, $startpos=0){
		$strFilename = md5($this->generateRandomBytes());

		$tmplfilename 		= $this->tmp_dir.$strFilename;
		$tmplfilename_plain = $this->tmp_dir_plain.$strFilename;

		$a = $this->ftp_copy($remote_file, $tmplfilename_plain);

		$tmpHandle = fopen($tmplfilename, 'a');
		if ($tmpHandle){
			$intBits = fwrite($tmpHandle, $data);
			fclose($tmpHandle);
		}

		$result = $this->put_upload($remote_file, $tmplfilename, $mode);
		if(!$this->on_iis()) @ftp_chmod($this->link_id, 0755, $this->root_dir.$remote_file);
		unlink($tmplfilename);
		return $result;
	}

	public function ftp_copy($from , $to){
		$result = ftp_put($this->link_id, $this->root_dir.$to, $this->root_dir.$from);
		if(!$this->on_iis()) @ftp_chmod($this->link_id, 0755, $this->root_dir.$to);

		return $result;
	}

	public function close(){
		@ftp_close($this->link_id);
	}

	/**
	 * Generate random bytes.
	 *
	 * @param   integer  $length  Length of the random data to generate
	 * @return  string  Random binary data
	 *
	 */
	private function generateRandomBytes($length = 16)
	{
		try {
			if (function_exists('random_bytes')) {
				$bytes = random_bytes($length);
				if ($bytes === false) throw new Exception('Cannot generate a secure stream of bytes.');

				return $bytes;
			}

			$bytes = openssl_random_pseudo_bytes($length, $s);
			if (!$s) throw new Exception('Cannot generate a secure stream of bytes.');

			return $bytes;
		} catch (\Exception $e) {
			throw new Exception('Cannot generate a secure stream of bytes.', $e);
		}

	}

	//These methods here have been defined somewhere else. But the pfh is called so early in super registry, that they are not available when pfh needs it.
	//Therefore they have been redeclared here.

	private function on_iis() {
		$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
		if ( strpos($sSoftware, "microsoft-iis") !== false )
			return true;
		else
			return false;
	}

}
