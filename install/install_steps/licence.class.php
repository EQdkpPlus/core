<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2003
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
class licence extends install_generic {
	public $next_button = 'accept';
	
	public function get_output() {
		$strOut = $this->lang['welcome'];
		$strOut .= '<br /><br /><h1>'.$this->lang['licence'].'</h1>';
		$strOut .= '<div class="licence">'.$this->lang['license_text'].'</div>';
	
		return $strOut;
	}
	
	public function get_filled_output() {
		$strOut = $this->lang['welcome'];
		$strOut .= '<br /><br /><h1>'.$this->lang['licence'].'</h1>';
		$strOut .= '<div class="licence">'.$this->lang['license_text'].'</div>';
	
		return $strOut;
	}
	
	public function parse_input() {
		return true;
	}
}
?>