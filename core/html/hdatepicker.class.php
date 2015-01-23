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
 * value		(string)	formatted date
 * class		(string)	class for the field
 * size			(int)		size of the field
 * js			(string)	extra js which shall be injected into the field
 * disabled		(boolean)	disabled field
 * allow_empty	(boolean) 	parse empty field as empty and don't try to make a date out of it
 * any additional options jquery->Calendar might want
 */
class hdatepicker extends html {

	protected static $type = 'datepicker';
	
	public $name = '';
	public $disabled = false;
	public $allow_empty = false;
	public $readonly = false;
	
	private $out = '';
	private $php_format = false;
	private $php_timeformat = false;
	private $all_options = array('id', 'format', 'change_fields', 'cal_icons', 'show_buttons', 'number_months', 'year_range', 'other_months', 'timeformat', 'enablesecs', 'onselect', 'timepicker', 'return_function');
	
	protected function _construct() {
		if(!($this->allow_empty && (empty($this->value) || $this->value == '0')) && is_numeric($this->value)) {
			$this->value = $this->time->date($this->js_calendarformat(), $this->value);
		}
		$out = '<span class="input-icon-append"><input type="text" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(!empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		
		if(isset($this->format)) {
			$this->php_format = $this->format;
			$this->format = $this->time->translateformat2js($this->format);
		}
		
		if(isset($this->timeformat)) {
			$this->php_timeformat = $this->timeformat;
			$this->timeformat = $this->time->translateformat2js($this->timeformat);
		}
		
		//copy options
		$opts = array();
		foreach($this->all_options as $opt) {
			if(isset($this->$opt)) $opts[$opt] = $this->$opt;
		}
		
		if (!$this->readonly){
			$this->jquery->Calendar($this->name, $this->value, '', $opts);
		}
		
		$this->out = $out.' />'.((!$this->readonly) ? '<i class="fa fa-calendar" onclick="$( \'#'.$this->id.'\' ).datepicker( \'show\' );"></i>' : '<i class="fa fa-calendar"></i>').'</span>';
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		$input = $this->in->get($this->name, '');
		if($this->allow_empty && empty($input)) return null;
		return $this->time->fromformat($input, $this->php_calendarformat());
	}
			
	/**
	 * Output dateformat in Calendar format, according to options
	 *
	 * @array 	$options		Option-Array in HTML-Widget format
	 * @return 	dateformat
	 */
	public function php_calendarformat() {
		// Load default settings if no custom ones are defined..
		if($this->php_format === false) $this->php_format = $this->user->style['date_notime_short'];
		if($this->php_timeformat === false) $this->php_timeformat = $this->user->style['time'];
		$format = $this->php_format;
		if(isset($this->timepicker)) $format .= ' '.$this->php_timeformat;
		return $format;
	}
	
	public function js_calendarformat(){
		// Load default settings if no custom ones are defined..
		if(!isset($this->format)) $this->format = $this->user->style['date_notime_short'];
		if(!isset($this->timeformat)) $this->timeformat = $this->user->style['time'];
		$format = $this->format;
		if(isset($this->timepicker)) $format .= ' '.$this->timeformat;
		return $format;
	}
}
?>