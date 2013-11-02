<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date: 2013-04-24 10:23:19 +0200 (Mi, 24 Apr 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13337 $
 * 
 * $Id: super_registry.class.php 13337 2013-04-24 08:23:19Z godmod $
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
class hdatepicker extends html {

	protected static $type = 'datepicker';
	
	public $name = '';
	public $disabled = false;
	public $allow_empty = false;
	
	protected function _toString() {	
		if(!($this->allow_empty && (empty($this->value) || $this->value == '0')) && is_numeric($this->value)) {
			$this->value = $this->time->date($this->calendarformat(), $this->value);
		}
		$out = '<input type="text" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(!empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->checked)) $out .= 'checked="checked" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		
		$this->options['id'] = $this->id;
		if(isset($this->options['format'])) $this->options['format'] = $this->time->translateformat2js($this->options['format']);
		if(isset($this->options['timeformat'])) $this->options['timeformat'] = $this->time->translateformat2js($this->options['timeformat']);
		$this->jquery->Calendar($this->name, $this->value, '', $this->options);
		
		return $out.' />';
	}
	
	public function inpval() {
		$input = $this->in->get($this->name, 0);
		if($this->allow_empty && empty($input)) return $input;
		return $this->time->fromformat($input, $this->calendarformat());
	}
			
	/**
	 * Output dateformat in Calendar format, according to options
	 *
	 * @array 	$options		Option-Array in HTML-Widget format
	 * @return 	dateformat
	 */
	public function calendarformat() {
		// Load default settings if no custom ones are defined..
		if(!isset($this->options['format'])) $this->options['format'] = $this->user->style['date_notime_short'];
		if(!isset($this->options['timeformat'])) $this->options['timeformat'] = $this->user->style['time'];
		$format = $this->options['format'];
		if(isset($this->options['timepicker'])) $format .= ' '.$this->options['timeformat'];
		return $format;
	}
}
?>