<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class ftp_access extends install_generic {
	public static $shortcuts = array('pdl', 'in', 'pfh' => array('file_handler', array('installer')));
	public static $before		= 'php_check';

	//default settings
	private $ftphost	= '127.0.0.1';
	private $ftpport	= 21;
	private $ftpuser	= '';
	private $ftppass	= '';
	private $ftproot	= '';
	private $use_ftp	= 0;
	
	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '
		<div class="ui-widget" align="left">
			<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
				<p>
				<strong>'.$this->lang['ftp_info'].'</strong><br /></p>
			</div>
		</div>
		<br />
		<table width="100%" border="0" cellspacing="1" cellpadding="2">
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

		// If the safe mode is on and the ftp mode is off, kill the cat
		if(ini_get('safe_mode') == '1' && $this->use_ftp != 1){ 
			$this->pdl->log('install_error', $this->lang['safemode_ftpmustbeon']);
			return false; 
		}

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

	private function configfile_content() {
		$content = '<?php'."\n\n";
		$content .= '$ftphost = \''.$this->ftphost.'\';'."\n";
		$content .= '$ftpport = '.(($this->ftpport) ? $this->ftpport : 21).';'."\n";
		$content .= '$ftpuser = \''.$this->ftpuser.'\';'."\n";
		$content .= '$ftppass = \''.$this->ftppass.'\';'."\n";
		$content .= '$ftproot = \''.$this->ftproot.'\';'."\n";
		$content .= '$use_ftp = '.$this->use_ftp.';'."\n";
		$content .= "\n".'?>';
		$this->pfh->putContent($this->root_path.'config.php', $content);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ftp_access', ftp_access::$shortcuts);
?>