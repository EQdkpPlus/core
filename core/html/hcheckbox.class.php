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
 * name			(string) 	name of the textarea
 * value					key of the checkbox to be checked
 * class		(string)	class for the labels of the fields
 * options		(array)		list of all checkboxes: key (value) => option (label)
 * dependency	(array)		array containing IDs of other inputs fields to disable, format: array(opt1_key => array(id1,id2,...), opt2_key => array(id5,id6,...))
 */
class hcheckbox extends html {

	protected static $type = 'checkbox';

	public $name = '';
	public $disabled = false;
	public $class = '';

	public function output() {
		$out = '';

		foreach ($this->options as $key => $opt) {
			if(is_array($this->value)){
				$selected_choice = (in_array((string)$key, $this->value)) ? ' checked="checked"' : '';
			} else {
				$selected_choice = ((string)$key == (string)$this->value) ? ' checked="checked"' : '';
			}

			$disabled = ($this->disabled) ? ' disabled="disabled"' : '';
			$out .= '<label';
			$dep = '';
			if(!empty($this->dependency)) {
				$this->class .= ' form_change_checkbox';
				$data = (!empty($this->dependency[$key])) ? implode(',', $this->dependency[$key]) : '';
				$dep = ' data-form-change="'.$data.'"';
			}
			if(!empty($this->class)) $out .= ' class="'.$this->class.'"';
			$out .= ' id="'.$this->id.'"';
			if($this->tolang) $opt = ($this->user->lang($opt, false, false)) ? $this->user->lang($opt) : (($this->game->glang($opt)) ? $this->game->glang($opt) : $opt);
			if(empty($this->inputid)) $this->inputid = $this->cleanid($this->name);
			$out .= '><input type="'.self::$type.'" name="'.$this->name.((count($this->options) > 1) ? '[]' : '').'" value="'.$key.'"'.$selected_choice.$disabled.$dep.' id="'.$this->inputid.'" />'.$opt.'</label><br />';
		}
		return $out;
	}

	public function _inpval() {
		if(count($this->options) > 1){
			return $this->in->getArray($this->name, 'string');
		} else {
			return $this->in->get($this->name);
		}
	}
}
