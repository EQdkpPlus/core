<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
 * text_after	(string)	Text added after the Multiselect
 * text_before	(string)	Text added before the Multiselect
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

	public $name				= '';
	public $disabled			= false;

	public $multiple			= true;
	public $width				= 200;
	public $height				= 200;
	public $preview_num			= 5;
	public $datatype			= 'string';
	public $tolang				= false;
	public $text_after			= "";
	public $text_before			= "";
	public $returnJS			= false;
	private $origID				= false;

	private $jq_options = array('height', 'width', 'preview_num', 'multiple', 'no_animation', 'header', 'filter', 'clickfunc', 'selectedtext', 'withmax', 'minselectvalue', 'appendTo');
	private $out				= '';

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}

	public function output() {
		$dropdown = "";
		$this->out = '';

		if(strlen($this->text_before)) $dropdown = $this->text_before;
		$dropdown .= '<select name="'.$this->name.'[]" id="'.$this->id.'" multiple="multiple"';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if(!is_array($this->todisable)) $this->todisable = array($this->todisable);
		if(is_array($this->options) && count($this->options) > 0){
			foreach ($this->options as $key => $value) {
				if(is_array($value)){
					$label = $key;
					if($this->tolang) $label = ($this->user->lang($key, false, false)) ? $this->user->lang($key) : (($this->game->glang($key)) ? $this->game->glang($key) : $key);
					$dropdown .= '<optgroup label="'.$label.'">';
					foreach($value as $key2 => $value2){
						if($this->tolang) $value2 = ($this->user->lang($value2, false, false)) ? $this->user->lang($value2) : (($this->game->glang($value2)) ? $this->game->glang($value2) : $value2);
						$disabled = (($key2 === 0 && in_array($key2, $this->todisable, true)) || ($key2 !== 0 && in_array($key2, $this->todisable))) ? ' disabled="disabled"' : '';
						$selected_choice = (!empty($this->value) && ($this->value == 'all' || (is_array($this->value) && in_array($key2, $this->value)))) ? 'selected="selected"' : '';
						$dropdown .= "<option value='".$key2."' ".$selected_choice.$disabled.">".$value2."</option>";
					}
					$dropdown .= '</optgroup>';

				} else {
					if($this->tolang) $value = ($this->user->lang($value, false, false)) ? $this->user->lang($value) : (($this->game->glang($value)) ? $this->game->glang($value) : $value);
					$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
					$selected_choice = (!empty($this->value) && ($this->value == 'all' || (is_array($this->value) && in_array($key, $this->value)))) ? 'selected="selected"' : '';
					$dropdown .= "<option value='".$key."' ".$selected_choice.$disabled.">".$value."</option>";

				}

			}
		} else {
			$dropdown .= "<option value=''></option>";
		}
		$dropdown .= "</select>";
		$options = array('id' => $this->id);
		foreach($this->jq_options as $opt) $options[$opt] = $this->$opt;

		$this->jquery->MultiSelect($this->name, array(), array(), $options, $this->returnJS);
		$jsout = ($this->returnJS) ? '<script>'.$this->jquery->get_jscode('multiselect', $this->id).'</script>' : '';
		if(strlen($this->text_after)) $dropdown .= $this->text_after;
		$this->out = $jsout.$dropdown;

		return $this->out;
	}

	public function _inpval() {
		return $this->in->getArray($this->name, $this->datatype);
	}
}
