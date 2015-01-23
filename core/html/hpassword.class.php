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
	public $required = false;
	public $pattern = 'password';
	public $autocomplete = false;
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}
	
	public function _toString() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" id="'.$this->id.'" ';
		if($this->set_value && !empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->pattern)) $this->class .= ' fv_success';
		if(!empty($this->equalto)) $this->class .= ' equalto';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->required) $out .= 'required="required" ';
		if(!$this->autocomplete) $out .= 'autocomplete="off" ';
		if(!empty($this->pattern)) $out .= 'pattern="'.$this->pattern($this->pattern).'" ';
		if(!empty($this->equalto)) $out .= 'data-equalto="'.$this->equalto.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$out .= ' />';
		if($this->required) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
		if(!empty($this->equalto)) $out .= '<span class="errormessage error-message-red" style="display:none;"><i class="fa fa-exclamation-triangle fa-lg"></i>'.registry::fetch('user')->lang('fv_required_password_repeat').'</span>';
		return $out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>