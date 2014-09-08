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
 * name			(string) 	name of the textarea
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		
 * class		(string)	class for the field
 * readonly		(boolean)	field readonly?
 * size			(int)		size of the field
 * js			(string)	extra js which shall be injected into the field
 * spinner		(boolean)	make a spinner out of the field?
 * disabled		(boolean)	disabled field
 * autocomplete	(array)		if not empty: array containing the elements on which to autocomplete (not to use together with spinner)
 * colorpicker	(boolean) 	apply a colorpicker to this field
 */
class hbutton extends html {

	protected static $type = 'button';
	
	public $name = '';
	public $class = 'input';
	public $buttontype = 'button';
	public $tolang = false;
	
	private $out = '';
	
	public function _construct() {
		$out = '<button type="'.$this->buttontype.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(empty($this->buttonvalue)) $this->buttonvalue = $this->name;
		$value = $this->buttonvalue;
		
		if($this->tolang) $value = ($this->user->lang($value, false, false)) ? $this->user->lang($value) : (($this->game->glang($value)) ? $this->game->glang($value) : $value);
		$out .= 'value="'.strip_tags($value).'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$this->out = $out.' >'.$value.'</button>';
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '', $this->inptype);
	}
}
?>