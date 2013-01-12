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
class encryptionkey extends install_generic {
	public static $shortcuts = array('pdl', 'in', 'pfh' => array('file_handler', array('installer')));
	public static $before 		= 'db_access';

	private $key			= '';

	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '
		<div class="ui-widget" align="left">
			<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
				<p>
				'.$this->lang['encryptkey_info'].'<br /></p>
			</div>
		</div>
		<br />
		<table width="100%" border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td align="right"><strong>'.$this->lang['encryptkey'].': </strong><div class="subname">'.$this->lang['encryptkey_help'].'</div></td>
				<td><input type="password" name="key1" size="25" value="" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['encryptkey_repeat'].': </strong></td>
				<td><input type="password" name="key2" size="25" value="" class="input" /></td>
			</tr>
		</table>';
		return $content;
	}
	public function get_filled_output() {
		return $this->get_output();
	}

	public function parse_input() {
		$key1		= $this->in->get('key1', '', 'raw');
		$key2		= $this->in->get('key2', '', 'raw');
		
		if($key1 != $key2) {
			$this->pdl->log('install_error', $this->lang['encryptkey_no_match']);
			return false;
		}
		
		if (strlen($key1) < 6){
			$this->pdl->log('install_error', $this->lang['encryptkey_too_short']);
			return false;
		}
		
		$this->key = $key1;
		
		$this->configfile_fill();
		return true;
	}

	private function configfile_fill() {
		$content = substr(file_get_contents($this->root_path.'config.php'), 0, -2); //discard last two symbols (? >)
		$content .= "\n\n".'$encryptionKey = \''.md5(md5(md5($this->key))).'\';'."\n";
		$content .= '?>';
		$this->pfh->putContent($this->root_path.'config.php', $content);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_encryptionkey', encryptionkey::$shortcuts);
?>