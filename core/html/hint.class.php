<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date: 2013-11-13 22:11:01 +0100 (Mi, 13 Nov 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13719 $
 * 
 * $Id: hspinner.class.php 13719 2013-11-13 21:11:01Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'core/html/htext.class.php');

/*
 * see htext class for available options
 */
// this class acts as an alias for easier usability
class hint extends htext {
	
	public $default = 0;
	public $size = 5;
	
	public function inpval() {
		return $this->in->get($this->name, 0);
	}
}
?>