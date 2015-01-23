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
 * value		(string)	selected option
 * class		(string)	class for the field
 * js			(string)	extra js which shall be injected into the field
 * options		(array)		dropdown-list
 * dependency	(array)		array containing IDs of other inputs fields to disable, format: array(opt1_key => array(id1,id2,...), opt2_key => array(id5,id6,...))
 * tolang		(boolean)	whether to put the vals of the list into language
 * disabled		(boolean)	disabled field
 * todisable	(array)		if not empty: array containing the elements which shall be disabled
 */
class hdropdown extends html {

	protected static $type = 'dropdown';
	
	public $name = '';
	public $disabled = false;
	public $tolang = false;
	public $class = 'input';
	public $options_only = false;
	public $no_key = false;
	public $format = false;
	public $opt_extra = array();
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}
	
	public function _toString() {
		$dropdown = '<select size="1" name="'.$this->name.'"';
		$dropdown .= ' id="'.$this->id.'"';
		if(!empty($this->dependency)) $this->class .= ' form_change';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if($this->options_only) $dropdown = '';
		if(!is_array($this->todisable)) {
			if (isset($this->todisable)) {
				$this->todisable = array($this->todisable);
			} else $this->todisable = array();
		}

		if(is_array($this->options) && count($this->options) > 0){
			foreach ($this->options as $key => $value) {
				if(is_array($value)){
					$dropdown .= "<optgroup label='".$key."'>";
					foreach ($value as $key2 => $value2) {
						if($this->no_key) $key2 = $value2;
						$dep = $this->gen_form_change($this->dependency[$key2]);
						$extra = (isset($this->opt_extra[$key2])) ? $this->opt_extra[$key2] : '';
						if($this->tolang) $value2 = ($this->user->lang($value2, false, false)) ? $this->user->lang($value2) : (($this->game->glang($value2)) ? $this->game->glang($value2) : $value2);
						if($this->format && function_exists($this->format)) $value2 = call_user_func($this->format, $value2);
						$selected_choice = ($key2 == $this->value) ? ' selected="selected"' : '';
						$disabled = (isset($this->todisable[$key]) && is_array($this->todisable[$key]) && (($key2 === 0 && in_array($key2, $this->todisable[$key], true)) || ($key2 !== 0 && in_array($key2, $this->todisable[$key])))) ? ' disabled="disabled"' : '';
						$dropdown .= "<option value='".$key2."'".$selected_choice.$disabled.$dep.$extra.">".$value2."</option>";
					}
					$dropdown .= "</optgroup>";
				}else{
					if($this->no_key) $key = $value;
					$dep = $this->gen_form_change($this->dependency[$key]);
					$extra = (isset($this->opt_extra[$key])) ? $this->opt_extra[$key] : '';
					if($this->tolang) $value = ($this->user->lang($value, false, false)) ? $this->user->lang($value) : (($this->game->glang($value)) ? $this->game->glang($value) : $value);
					if($this->format && function_exists($this->format)) $value = call_user_func($this->format, $value);
					$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
					$selected_choice = (($key == $this->value)) ? 'selected="selected"' : '';
					$dropdown .= "<option value=\"".$key."\" ".$selected_choice.$disabled.$dep.$extra.">".$value."</option>";
				}
			}
		}else{
			$dropdown .= "<option value=''></option>";
		}
		if(!$this->options_only) $dropdown .= "</select>";
		return $dropdown;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>