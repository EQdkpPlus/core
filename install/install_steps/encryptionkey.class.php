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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class encryptionkey extends install_generic {
	public static $shortcuts = array('pfh' => array('file_handler', array('installer')));
	public static $before 		= 'db_access';

	private $key			= '';

	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '
		<div class="infobox infobox-large infobox-blue clearfix">
			<i class="fa fa-info-circle fa-4x pull-left"></i>'.$this->lang['encryptkey_info'].'
		</div>
		<br />
		<table width="100%" border="0" cellspacing="1" cellpadding="2" class="no-borders">
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
?>