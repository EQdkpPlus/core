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
 * value		(array)		array containing the selected values
 * class		(string)	class for the field
 * disabled		(boolean)	disabled field
 * js			(string)	extra js which shall be injected into the field
 * options		(array)		list of all available options
 * todisable	(array)		list of all options which shall not be selectable
 * tolang		(boolean)	apply language function on values of option-array
 * 
 * additional options for jquery->multiselect
 * height 		(int)		height of the dropdown in px
 * width 		(int)		width of the dropdown in px
 * preview_num	(int)		number of selected options to be displayed in a comma seperated list in collapsed state
 * no_animation (boolean)	disable collapse animation?
 * header
 * filter
 */
class hmultiselect extends html {

	protected static $type = 'dropdown';
	
	public $name = '';
	public $disabled = false;
	
	public $multiple = true;
	public $width = 200;
	public $height = 200;
	public $preview_num = 5;
	public $datatype = 'string';
	public $tolang = false;
	
	private $jq_options = array('id', 'height', 'width', 'preview_num', 'multiple', 'no_animation', 'header', 'filter');
	private $out = '';
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$dropdown = '<select name="'.$this->name.'[]" id="'.$this->id.'" multiple="multiple"';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if(!is_array($this->todisable)) $this->todisable = array($this->todisable);
		if(is_array($this->options) && count($this->options) > 0){
			foreach ($this->options as $key => $value) {
				if($this->tolang) $value = ($this->user->lang($value, false, false)) ? $this->user->lang($value) : (($this->game->glang($value)) ? $this->game->glang($value) : $value);
				$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
				$selected_choice = (!empty($this->value) && in_array($key, $this->value)) ? 'selected="selected"' : '';
				$dropdown .= "<option value='".$key."' ".$selected_choice.$disabled.">".$value."</option>";
			}
		} else {
			$dropdown .= "<option value=''></option>";
		}
		$dropdown .= "</select>";
		$options = array();
		foreach($this->jq_options as $opt) $options[$opt] = $this->$opt;
		$this->jquery->MultiSelect('', array(), array(), $options);
		$this->out = $dropdown;
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->getArray($this->name, $this->datatype);
	}
}
?>