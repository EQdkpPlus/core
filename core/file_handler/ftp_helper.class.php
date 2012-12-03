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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

class ftp_handler{
	private $link_id		= '';		//FTP hand
	private $is_login		= '';		//is login 
	private $root_dir		= '';		// FTP root directory
	private $tmp_dir		= '';		// Temporary files directory

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

	public function setTempDir($dir){
		$this->tmp_dir	= $dir;
	}

	public function setRootDir($dir){
		$this->root_dir	= $dir;
	}

	public function showerror(){
		return $this->error;
	}

	private function login(){
		if(!$this->link_id){
			$this->link_id = ftp_connect($this->host,$this->port);
			if(!$this->link_id){
				$this->error = sprintf("can not connect to host: %1$s:%2$s", $this->host, $this->port);return;
			}
		}
		if(!$this->is_login){
			$this->is_login = ftp_login($this->link_id, $this->user, $this->pass);
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
		$isok =  ftp_cdup($this->link_id);
		if($isok){
			$this->dir = $this->pwd();
		}
		return $isok;
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
		if(!$dir){
			$dir = ".";
		}
		$arr_dir = ftp_nlist($this->link_id,$this->root_dir.$dir);
		return $arr_dir;
	}

	public function rawlist($dir="/"){
		$this->login();
		$arr_dir = ftp_rawlist($this->link_id,$this->root_dir.$dir);
		return $arr_dir;
	}

	public function mkdir($dir){
		$this->login();
		return @ftp_mkdir($this->link_id,$this->root_dir.$dir);
	}
	
	public function mkdir_r($path, $mode=0755){
		$this->login();
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
					@ftp_chmod($this->link_id, $mode, $path);
				}
			}
		}
		return $ret;
	}

	public function file_size($file){
		$this->login();
		$size = ftp_size($this->link_id,$this->root_dir.$file);
		return $size;
	}

	public function chmod($file, $mode=0666){
		$this->login();
		return ftp_chmod($this->link_id,$mode,$this->root_dir.$file);
	}

	public function delete($remote_file){
		$this->login();
		return ftp_delete($this->link_id,$this->root_dir.$remote_file);
	}

	public function filedate($remote_file){
		$this->login();
		$date = ftp_mdtm($this->link_id, $this->root_dir.$remote_file);
		return ($date != -1) ? $date : 'No Date available';
	}

	public function rename($old_file, $new_file){
		$this->login();
		return ftp_rename($this->link_id, $this->root_dir.$old_file, $this->root_dir.$new_file);
	}

	public function is_dir($file){
		return (is_dir($this->root_dir.$file)) ? true : false;
	}
	
	public function ftp_rmdirr($path){
		$this->login();
		if (!(@ftp_rmdir($this->link_id, $this->root_dir.$path) || @ftp_delete($this->link_id, $this->root_dir.$path))){
			$list = ftp_nlist($this->link_id, $this->root_dir.$path);
			if (!empty($list))
			foreach($list as $value)
			$this->ftp_rmdirr($value);
		}
		@ftp_rmdir($this->link_id, $this->root_dir.$path);
	}

	public function get($local_file, $remote_file, $mode=FTP_BINARY, $startpos=0){
		$this->login();
		return ftp_get($this->link_id,$this->root_dir.$local_file,$this->root_dir.$remote_file,$mode, $startpos);
	}

	public function put($remote_file, $local_file, $mode=FTP_BINARY){
		$this->login();
		return ftp_put($this->link_id,$this->root_dir.$remote_file,$this->root_dir.$local_file,$mode);
	}

	private function put_upload($remote_file, $local_file, $mode=FTP_BINARY){
		$this->login();
		return ftp_put($this->link_id,$this->root_dir.$remote_file,$local_file,$mode);
	}

	public function put_string($remote_file, $data, $mode=FTP_BINARY, $startpos=0){
		$tmplfilename = $this->tmp_dir.md5(uniqid(mt_rand(), true));
		file_put_contents($tmplfilename, $data);
		$result = $this->put_upload($remote_file, $tmplfilename, $mode);
		@ftp_chmod($this->link_id, 0755, $this->root_dir.$remote_file);
		unlink($tmplfilename);
		return $result;
	}

	function ftp_copy($from,$to){
		$tmplfilename = $this->tmp_dir.md5(uniqid(mt_rand(), true));
		copy($this->root_dir.$from, $tmplfilename);
		$this->put_upload($to, $tmplfilename, FTP_BINARY);
		@ftp_chmod($this->link_id, 0755, $this->root_dir.$to);
		unlink($tmplfilename);
		return true;
	}

	public function close(){
		@ftp_quit($this->link_id);
	}
}
?>