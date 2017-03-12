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
 * spinner		(boolean)	make a spinner out of the field?
 * disabled		(boolean)	disabled field
 * autocomplete	(array)		if not empty: array containing the elements on which to autocomplete (not to use together with spinner)
 * colorpicker	(boolean) 	apply a colorpicker to this field
 */
class htext extends html {

	protected static $type = 'text';

	public $name				= '';
	public $readonly			= false;
	public $spinner				= false;
	public $colorpicker			= false;
	public $placepicker			= false;
	public $placepicker_withmap	= false;
	public $required			= false;
	public $fvmessage			= false;
	public $returnJS			= false;
	public $autocomplete		= array();
	public $class				= 'input';
	public $inptype				= '';
	public $disabled			= false;

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
		} elseif($this->colorpicker) {
			$this->jquery->colorpicker($this->id,0,'',14,'',array(),$this->returnJS);
			if($this->returnJS){
				$jsout = '<script>'.$this->jquery->get_jscode('colorpicker', $this->id).'</script>';
			}
			$this->class = (empty($this->class)) ? 'colorpicker' : $this->class.' colorpicker';
		}elseif($this->placepicker){
			$this->jquery->placepicker($this->id, $this->placepicker_withmap, $this->returnJS);
			if($this->returnJS){
				$jsout = $this->jquery->get_jscode('placepicker', $this->id);
			}
		}elseif($this->spinner && $this->returnJS){
			$jsout = "<script>
						var self = $('#".$this->id."'), min = self.data('min'), max = self.data('max'), step = self.data('step');
						self.spinner({ min: min, max: max, step: step, });
					</script>";
		}

		// start the output
		$out	 = $jsout.'<input type="'.self::$type.'" name="'.$this->name.'" ';
		$out	.= 'id="'.$this->id.'" ';
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->pattern) && !empty($this->successmsg)) $this->class .= ' fv_success';
		if(!empty($this->equalto)) $this->class .= ' equalto';
		if($this->spinner) $this->class .= ' core-spinner';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->required) $out .= ' required="required" data-fv-message="'.(($this->fvmessage) ? $this->fvmessage : registry::fetch('user')->lang('fv_required')).'"';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(is_array($this->attrdata) && count($this->attrdata) > 0){
			foreach($this->attrdata as $attrdata_name=>$attrdata_value){
				$out .= 'data-'.$attrdata_name.'="'.$attrdata_value.'" ';
			}
		}
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
		if(!empty($this->pattern)) $out .= '<span class="fv_msg">'.registry::fetch('user')->lang('fv_sample_pattern').'</span>';
		elseif($this->required) $out .= '<i class="fa fa-asterisk required small"></i>';
		if(!empty($this->after_txt)) $out .= $this->after_txt;
		$this->out = $out;
		return $this->out;
	}

	public function _inpval() {
		return $this->in->get($this->name, '', $this->inptype);
	}
}
?>