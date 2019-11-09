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
 * see htext class for available options
 */
// this class acts as an alias for easier usability
class hspinner extends html {

	protected static $type = 'spinner';

	public $default = 0;
	public $size = 5;
	public $name				= '';
	public $readonly			= false;
	public $required			= false;
	public $fvmessage			= false;
	public $returnJS			= false;
	public $class				= 'input';
	public $disabled			= false;
	public $attrdata			= array();
	public $after_txt			= '';
	public $js					= '';

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}

	public function output() {
		$this->out = "";
		$jsout	= '';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);

		if($this->spinner && $this->returnJS){
			$jsout = "<script>
						var self = $('#".$this->id."'), min = self.data('min'), max = self.data('max'), step = self.data('step');
						self.spinner({ min: min, max: max, step: step, });
					</script>";
		}

		// start the output
		$out	 = $jsout.'<input type="'.self::$type.'" name="'.$this->name.'" ';
		$out	.= 'id="'.$this->id.'" ';
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';

		$this->class .= ' core-spinner';
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

		$out .= (isset($this->min) && is_numeric($this->min)) ? 'data-min="'.$this->min.'"' : '';
		$out .= (isset($this->max) && is_numeric($this->max)) ? 'data-max="'.$this->max.'"' : '';
		$out .= (isset($this->step) && is_numeric($this->step)) ? 'data-step="'.$this->step.'"' : '';

		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$out .= ' />';
		if($this->required) $out .= '<i class="fa fa-asterisk required small"></i>';
		if(!empty($this->after_txt)) $out .= $this->after_txt;
		$this->out = $out;
		return $this->out;
	}

	public function _inpval() {
		if(is_float($this->step)) return trim($this->in->get($this->name, 0.0));
		return trim($this->in->get($this->name, 0));
	}
}
