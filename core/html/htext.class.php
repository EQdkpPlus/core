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
class htext extends html {

	protected static $type = 'text';
	
	public $name = '';
	public $readonly = false;
	public $spinner = false;
	public $colorpicker = false;
	public $required = false;
	public $autocomplete = array();
	public $class = 'input';
	public $inptype = '';

	private $out = '';
	
	public function _construct() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(!empty($this->autocomplete)) {
			$this->jquery->Autocomplete($this->id, $this->autocomplete);
		} elseif($this->colorpicker) {
			$this->jquery->colorpicker(0,0);
			$this->class = (empty($this->class)) ? 'colorpicker' : $this->class.' colorpicker';
		}
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->pattern) && !empty($this->successmsg)) $this->class .= ' fv_success';
		if(!empty($this->equalto)) $this->class .= ' equalto';
		if($this->spinner) $this->class .= ' core-spinner';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->required) $out .= 'required="required" ';
		if(!empty($this->pattern)) $out .= 'pattern="'.$this->pattern($this->pattern).'" ';
		if(!empty($this->euqalto)) $out .= 'data-equalto="'.$this->equalto.'" ';
		if($this->spinner){
			$out .= (isset($this->min) && is_numeric($this->min)) ? 'data-min="'.$this->min.'"' : '';
			$out .= (isset($this->max) && is_numeric($this->max)) ? 'data-max="'.$this->max.'"' : '';
			$out .= (isset($this->step) && is_numeric($this->step)) ? 'data-step="'.$this->step.'"' : '';
		}
		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$out .= ' />';
		if(!empty($this->pattern)) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_sample_pattern').'"></span>';
		elseif($this->required) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
		if(!empty($this->equalto)) $out .= '<span class="errormessage error-message-red" style="display:none;"><i class="fa fa-exclamation-triangle fa-lg"></i>'.registry::fetch('user')->lang('fv_required_password_repeat').'</span>';
		if(!empty($this->after_txt)) $out .= $this->after_txt;
		$this->out = $out;
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '', $this->inptype);
	}
}
?>