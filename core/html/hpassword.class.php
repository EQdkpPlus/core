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

/*
 * available options
 * name			(string) 	name of the field
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		
 * class		(string)	class for the field
 * readonly		(boolean)	field readonly?
 * js			(string)	extra js which shall be injected into the field
 */
class hpassword extends html {

	protected static $type = 'password';
	
	public $name = '';
	public $set_value = false;
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}
	
	public function _toString() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" id="'.$this->id.'" ';
		if($this->set_value && !empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		return $out.' />';
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>