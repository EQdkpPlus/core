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