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
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value
 * class		(string)	class for the field
 * readonly		(boolean)	field readonly?
 * size			(int)		size of the field
 * js			(string)	extra js which shall be injected into the field
 * disabled		(boolean)	disabled field
 * autocomplete	(array)		if not empty: array containing the elements on which to autocomplete (not to use together with spinner)
 */
class htext extends html {

	protected static $type = 'text';

	public $name				= '';
	public $readonly			= false;
	public $required			= false;
	public $fvmessage			= false;
	public $returnJS			= false;
	public $autocomplete		= array();
	public $class				= 'input';
	public $inptype				= '';
	public $disabled			= false;
	public $trim_input			= true;
	public $pattern				= '';
	public $eqalto				= '';
	public $attrdata			= array();
	public $after_txt			= '';
	public $size				= '';
	public $js					= '';


	private $out = '';

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}

	public function output() {
		$this->out = "";
		$jsout	= '';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		if(!empty($this->autocomplete)) {
			$this->jquery->Autocomplete($this->id, $this->autocomplete, $this->returnJS);
			if($this->returnJS){
				$jsout = '<script>'.$this->jquery->get_jscode('autocomplete', $this->id).'</script>';
			}
		}

		// start the output
		$out	 = $jsout.'<input type="'.static::$type.'" name="'.$this->name.'" ';
		$out	.= 'id="'.$this->id.'" ';
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->pattern) && !empty($this->successmsg)) $this->class .= ' fv_success';
		if(!empty($this->equalto)) $this->class .= ' equalto';

		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->required) $out .= ' required="required" data-fv-message="'.(($this->fvmessage) ? $this->fvmessage : registry::fetch('user')->lang('fv_required')).'"';
		if(!$this->required && !empty($this->pattern)) $out .= 'data-fv-message="'.registry::fetch('user')->lang('fv_sample_pattern').'"';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(is_array($this->attrdata) && count($this->attrdata) > 0){
			foreach($this->attrdata as $attrdata_name=>$attrdata_value){
				$out .= 'data-'.$attrdata_name.'="'.$attrdata_value.'" ';
			}
		}
		if(!empty($this->pattern)) $out .= 'pattern="'.$this->pattern($this->pattern).'" ';
		if(!empty($this->euqalto)) $out .= 'data-equalto="'.$this->equalto.'" ';
		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$out .= ' />';
		if($this->required) $out .= '<i class="fa fa-asterisk required small"></i>';
		if(!empty($this->after_txt)) $out .= $this->after_txt;
		$this->out = $out;
		return $this->out;
	}

	public function _inpval() {
		$strInput = $this->in->get($this->name, '', $this->inptype);
		return ($this->trim_input) ? trim($strInput) : $strInput;
	}
}
