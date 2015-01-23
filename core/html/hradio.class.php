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
 * value					key of the radio to be checked
 * class		(string)	class for labels of the fields
 * options		(array)		list containing all the options, if empty it defaults to yes / no
 * dependency	(array)		array containing IDs of other inputs fields to disable, format: array(opt1_key => array(id1,id2,...), opt2_key => array(id5,id6,...))
 * disabled		(boolean)	disabled field
 */
class hradio extends html {

	protected static $type = 'radio';
	
	public $name = '';
	public $disabled = false;
	public $default = 0;
	public $class = '';
	public $tolang = false;
	public $nodiv = false;
	public $js = "";
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}
	
	public function _toString() {
		$radiobox  = '';
		if(empty($this->options)){
			$this->options = array (
				'0'   => $this->user->lang('cl_off'),
				'1'   => $this->user->lang('cl_on')
			);
		}
		if(!empty($this->dependency)) $this->class .= ' form_change_radio';
		foreach ($this->options as $key => $opt) {
			$selected_choice = ((string)$key == (string)$this->value) ? ' checked="checked"' : '';
			$disabled = ($this->disabled) ? ' disabled="disabled"' : '';
			$radiobox .= '<label';
			if(!empty($this->class)) $radiobox .= ' class="'.$this->class.'"';
			$data = (!empty($this->dependency[$key])) ? implode(',', $this->dependency[$key]) : '';
			$dep = (!empty($this->dependency)) ? ' data-form-change="'.$data.'"' : '';
			$js = (!empty($this->js)) ? ' '.$this->js.' ' : '';
			if($this->tolang) $opt = $this->user->lang($opt);
			$radiobox .= '><input type="'.self::$type.'" name="'.$this->name.'" value="'.$key.'"'.$selected_choice.$disabled.$dep.$js.'/> '.$opt.'</label>&nbsp;';
		}

		return ($this->nodiv) ? $radiobox: '<div id="'.$this->id.'">'.$radiobox.'</div>';
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>