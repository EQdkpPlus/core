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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class file_permissions extends install_generic {
	public static $shortcuts = array('pfh' => array('file_handler', array('installer')));
	public static $before		= 'php_check';

	//default settings
	private $ftphost	= '127.0.0.1';
	private $ftpport	= 21;
	private $ftpuser	= '';
	private $ftppass	= '';
	private $ftproot	= '';
	private $use_ftp	= 0;
	private $chmod		= false;

	public static function before() {
		return self::$before;
	}

	public function get_output() {
		if(!isset($this->data['file_step_count'])) $this->data['file_step_count'] = 0;
		$this->data['file_step_count']++;

		$content = "";

			$phpwriteerror = false;

			$content = '<table class="colorswitch" style="border-collapse: collapse;" width="100%">
			<tbody>';


			// check if the config.php is available


			$content .= '<tr><td width="54%">'.$this->lang['fp_config_file'].'</td><td  width="13%">';

			if(!file_exists($this->root_path.'config.php')){
				// try to create the file
				$this->pfh->CheckCreateFile($this->root_path.'config.php');
				if(!file_exists($this->root_path.'config.php')){
					$this->pdl->log('install_error', $this->lang['plain_config_nofile']);
					$phpwriteerror = true;
					$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
				} elseif(!$this->pfh->is_writable($this->root_path.'config.php', false)) {
					$this->pdl->log('install_error', $this->lang['plain_config_nwrite']);
					$phpwriteerror = true;
					$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
				} else {
					$content .= '<i class="fa fa-check-circle fa-2x positive"></i>';
				}

			} elseif(!$this->pfh->is_writable($this->root_path.'config.php', false)){
				// check if the config.php is writable, attempt to create it
				$this->pdl->log('install_error', $this->lang['plain_config_nwrite']);
				$phpwriteerror = true;
				$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
			} else {
				$content .= '<i class="fa fa-check-circle fa-2x positive"></i>';
			}

			if(file_exists($this->root_path.'config.php')){
				$filePermConfig = substr(sprintf('%o', fileperms($this->root_path.'config.php')), -4);
			} else $filePermConfig = "";

			$content .= '</td><td>'.$filePermConfig.'</td></tr>';


			// check if the data folder is available
			$content .= '<tr><td>'.$this->lang['fp_data_folder'].'</td><td  width="13%">';
			if(!$this->pfh->CheckCreateFolder($this->root_path.'data/')){
				$this->pdl->log('install_error', $this->lang['plain_dataf_na']);
				$phpwriteerror = true;
				$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
			} elseif(!$this->pfh->is_writable($this->root_path.'data/', true)){
				$this->pdl->log('install_error', $this->lang['plain_dataf_nwrite']);
				$phpwriteerror = true;
				$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
			} else {
				$content .= '<i class="fa fa-check-circle fa-2x positive"></i>';
			}

			if(is_dir($this->root_path.'data/')){
				$filePermConfig = substr(sprintf('%o', fileperms($this->root_path.'data/')), -4);
			} else $filePermConfig = "";

			$content .= '</td><td width="13%">'.$filePermConfig.'</td></tr>';

			//Check if file can be open in browser
			$content .= '<tr><td width="54%">'.$this->lang['fp_test_file'].'</td><td  width="13%">';
			$this->pfh->putContent($this->root_path.'data/'.md5('installer').'/tmp/test_file.php', 'test');
			$objUrlfetcher = registry::register('urlfetcher');
			$blnResult = $objUrlfetcher->fetch($this->get_my_url().'data/'.md5('installer').'/tmp/test_file.php');
			if(!$blnResult || $blnResult != "test"){
				$this->chmod = "0755";
				$content .= '<i class="fa fa-times-circle fa-2x negative"></i>';
			} else {
				$content .= '<i class="fa fa-check-circle fa-2x positive"></i>';
			}
			$this->pfh->Delete($this->root_path.'data/'.md5('installer').'/tmp/test_file.php', 'test');
			$content .= '</td><td></td></tr>';


			$content .='</tbody>
				</table>';

			if($this->data['file_step_count'] > 2){
				$content .= "<br />".$this->get_ftp_form();
			}

		return $content;
	}

	public function get_filled_output() {
		$this->ftphost	= registry::get_const('ftphost');
		$this->ftpport	= registry::get_const('ftpport');
		$this->ftpuser	= registry::get_const('ftpuser');
		$this->ftproot	= registry::get_const('ftproot');
		$this->use_ftp	= registry::get_const('use_ftp');
		return $this->get_output();
	}

	public function parse_input() {
		$this->ftphost	= $this->in->get('ftphost');
		$this->ftpport	= $this->in->get('ftpport');
		$this->ftpuser	= $this->in->get('ftpuser');
		$this->ftppass	= $this->in->get('ftppass');
		$this->ftproot	= $this->in->get('ftproot');
		$this->use_ftp	= $this->in->get('useftp', 0);

		// If the ftp mode is off, check if the data folder is readable
		if($this->use_ftp == 0){
			$phpwriteerror = false;
			// check if the config.php is available
			if(!file_exists($this->root_path.'config.php')){
				// try to create the file
				$this->pfh->CheckCreateFile($this->root_path.'config.php');
				if(!file_exists($this->root_path.'config.php')){
					$this->pdl->log('install_error', $this->lang['plain_config_nofile']);
					$phpwriteerror = true;
				} elseif(!$this->pfh->is_writable($this->root_path.'config.php', false)) {
					$this->pdl->log('install_error', $this->lang['plain_config_nwrite']);
					$phpwriteerror = true;
				}
			} elseif(!$this->pfh->is_writable($this->root_path.'config.php', false)){
				// check if the config.php is writable, attempt to create it
				$this->pdl->log('install_error', $this->lang['plain_config_nwrite']);
				$phpwriteerror = true;
			}

			// check if the data folder is available
			if(!$this->pfh->CheckCreateFolder($this->root_path.'data/')){
				$this->pdl->log('install_error', $this->lang['plain_dataf_na']);
				$phpwriteerror = true;
			} elseif(!$this->pfh->is_writable($this->root_path.'data/', true)){
				$this->pdl->log('install_error', $this->lang['plain_dataf_nwrite']);
				$phpwriteerror = true;
			}

			//Check if file can be open in browser
			$this->pfh->putContent($this->root_path.'data/'.md5('installer').'/tmp/test_file.php', 'test');
			$objUrlfetcher = registry::register('urlfetcher');
			$blnResult = $objUrlfetcher->fetch($this->get_my_url().'data/'.md5('installer').'/tmp/test_file.php');
			if(!$blnResult || $blnResult != "test"){
				$this->chmod = "0755";
			}
			$this->pfh->Delete($this->root_path.'data/'.md5('installer').'/tmp/test_file.php', 'test');

			// if one of this is not writeable, die, baby, die!
			if($phpwriteerror) return false;
		} else {
			// if the ftp handler is on, try to connect..
			$connect	= ftp_connect($this->ftphost, $this->ftpport, 5);
			$login		= ftp_login($connect, $this->ftpuser, $this->ftppass);

			// connection failed, jump out of the window
			if (!$connect){
				$this->pdl->log('install_error', $this->lang['ftp_connectionerror'].'<br />'.$connect);
				return false;
			}

			// try to login, hopefully it should work :)
			if(!$login) {
				$this->pdl->log('install_error', $this->lang['ftp_loginerror']);
				return false;
			}

			// test if the data folder is writable
			if ($this->ftproot == '/') $this->ftproot = '';
			if(strlen($this->ftproot) && substr($this->ftproot,-1) != "/") {
				$this->ftproot .= '/';
			}
			ftp_pasv($connect, true);

			//Go to data-Folder
			if (!ftp_chdir($connect, $this->ftproot.'data/')){
				//data-Folder is not available. Is there a core-Folder?
				if (!ftp_chdir($connect, $this->ftproot.'core/')){
					$this->pdl->log('install_error', $this->lang['ftp_datawriteerror']);
					return false;
				} else {
					ftp_chdir($connect, '../');
				}

				//Try to create data-Folder
				if (!ftp_mkdir($connect, $this->ftproot.'data/')){
					$this->pdl->log('install_error', $this->lang['plain_dataf_na']);
					return false;
				}
				ftp_chdir($connect, $this->ftproot.'data/');

			}

			if (!ftp_put($connect, 'test_file.php', $this->root_path.'install/index.php', FTP_BINARY)){
				$this->pdl->log('install_error', $this->lang['ftp_datawriteerror']);
				return false;
			}
			@ftp_delete($connect, 'test_file.php');
			ftp_mkdir($connect, md5('installer'));
			ftp_mkdir($connect, md5('installer').'/tmp/');
			ftp_chmod($connect, 0777, md5('installer').'/tmp/');

			//Check now tmp-Folder, because it needs CHMOD 777 for writing the config-file
			if (!file_put_contents ( $this->root_path.'data/'.md5('installer').'/tmp/test_file.php', 'test')){
				$this->pdl->log('install_error', $this->lang['ftp_tmpinstallwriteerror']);
				return false;
			}

			//Check if file can be open in browser
			$objUrlfetcher = registry::register('urlfetcher');
			$blnResult = $objUrlfetcher->fetch($this->get_my_url().'data/'.md5('installer').'/tmp/test_file.php');
			if(!$blnResult || $blnResult != "test"){
				$this->chmod = "0755";
			}

			@ftp_delete($connect, md5('installer').'/tmp/test_file.php');

			//Everything fine, reinitialise pfh, to use ftp
			registry::add_const('ftphost', $this->ftphost);
			registry::add_const('ftpport', (($this->ftpport) ? $this->ftpport : 21));
			registry::add_const('ftpuser', $this->ftpuser);
			registry::add_const('ftppass', $this->ftppass);
			registry::add_const('ftproot', $this->ftproot);
			registry::add_const('use_ftp', true);

			$this->pfh->__construct('installer', 'filehandler_ftp');

			if(!file_exists($this->root_path.'config.php'))	$this->pfh->CheckCreateFile($this->root_path.'config.php');
		}
		$this->configfile_content();
		return true;
	}

	private function get_ftp_form(){
		$content = '
		<div class="infobox infobox-large infobox-blue clearfix">
			<i class="fa fa-info-circle fa-4x pull-left"></i>'.$this->lang['ftp_info'].'
		</div>

		<br />
		<table width="100%" border="0" cellspacing="1" cellpadding="2" class="no-borders">
			<tr>
				<td align="right"><strong>'.$this->lang['ftphost'].': </strong></td>
				<td><input type="text" name="ftphost" size="25" value="'.$this->ftphost.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['ftpport'].': </strong></td>
				<td><input type="text" name="ftpport" size="25" value="'.$this->ftpport.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['ftpuser'].': </strong></td>
				<td><input type="text" name="ftpuser" size="25" value="'.$this->ftpuser.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['ftppass'].': </strong></td>
				<td><input type="password" name="ftppass" size="25" value="" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['ftproot'].': </strong><div class="subname">'.$this->lang['ftproot_sub'].'</div></td>
				<td><input type="text" name="ftproot" size="25" value="'.$this->ftproot.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['useftp'].': </strong><div class="subname">'.$this->lang['useftp_sub'].'</div></td>
				<td><input type="checkbox" value="1" name="useftp" '.(($this->use_ftp == 1) ? 'checked="checked"' : '').' /></td>
			</tr>
		</table>';
		return $content;
	}

	private function configfile_content() {
		$content = '<?php'."\n\n";
		$content .= '$ftphost = \''.$this->ftphost.'\';'."\n";
		$content .= '$ftpport = '.(($this->ftpport) ? $this->ftpport : 21).';'."\n";
		$content .= '$ftpuser = \''.$this->ftpuser.'\';'."\n";
		$content .= '$ftppass = \''.$this->ftppass.'\';'."\n";
		$content .= '$ftproot = \''.$this->ftproot.'\';'."\n";
		$content .= '$use_ftp = '.$this->use_ftp.';'."\n";
		if($this->chmod !== false){
			$content .= 'define("CHMOD", '.$this->chmod.');'."\n";
		}
		$content .= "\n".'?>';
		$this->pfh->putContent($this->root_path.'config.php', $content);

		//Reset Opcache, for PHP7
		if(function_exists('opcache_reset')){
			opcache_reset();
		}
	}

	private function get_my_url(){
		$strServerName = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		$strServerName = preg_replace('/[^A-Za-z0-9\.:-]/', '', $strServerName);
		$strServerName .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

		$blnIsSSL = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 || isset($_SERVER['SSL_SESSION_ID']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )) ? true : false;
		return (($blnIsSSL) ? 'https://' : 'http://'). str_replace('install/', '', $strServerName);
	}
}
