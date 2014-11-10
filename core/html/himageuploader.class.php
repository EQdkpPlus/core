<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'core/html/html.aclass.php');
include_once(registry::get_const('root_path').'core/html/hhidden.class.php');

// this class acts as an alias for easier usability
/*
 * see hhidden for all available options
 */
class himageuploader extends hhidden {
	
	public $imageuploader = true;
	public $returnFormat = '';
	
	public function _inpval() {
		switch($this->returnFormat){
			case 'relative': return str_replace($this->environment->link, registry::get_const('root_path'), urldecode($this->in->get($this->name, '')));
			
			case 'in_data': return str_replace($this->pfh->FileLink('', 'files', 'absolute'), '', urldecode($this->in->get($this->name, '')));
			
			case 'filename': return  pathinfo(urldecode($this->in->get($this->name, '')), PATHINFO_BASENAME);
			
			case 'absolute':
			default: return urldecode($this->in->get($this->name, ''));
		}
	}
}
?>