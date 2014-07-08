<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
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
	
	private $spinner_opts = array('step', 'max', 'min', 'value', 'numberformat', 'incremental', 'change', 'multiselector');
	private $out = '';
	
	public function _construct() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if($this->spinner) {
			$spin_options = array();
			foreach($this->spinner_opts as $opt) $spin_options[$opt] = $this->$opt;
			$this->jquery->Spinner($this->id, $spin_options);
		} elseif(!empty($this->autocomplete)) {
			$this->jquery->Autocomplete($this->id, $this->autocomplete);
		} elseif($this->colorpicker) {
			$this->jquery->colorpicker(0,0);
			$this->class = (empty($this->class)) ? 'colorpicker' : $this->class.' colorpicker';
		}
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->pattern)) $this->class .= ' fv_success';
		if(!empty($this->equalto)) $this->class .= ' equalto';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->required) $out .= 'required="required" ';
		if(!empty($this->pattern)) $out .= 'pattern="'.$this->pattern($this->pattern).'" ';
		if(!empty($this->euqalto)) $out .= 'data-equalto="'.$this->equalto.'" ';
		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$this->out = $out.' />';
		if(!empty($this->pattern)) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_sample_pattern').'"></span>';
		elseif($this->required) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function inpval() {
		return $this->in->get($this->name, '', $this->inptype);
	}
}
?>