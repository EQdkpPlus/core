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
	
	protected function _toString() {
		$out = '';
		foreach ($this->options as $key => $opt) {
			$selected_choice = ((string)$key == (string)$this->value) ? ' checked="checked"' : '';
			$disabled = ($this->disabled) ? ' disabled="disabled"' : '';
			$out .= '<label';
			$dep = '';
			if(!empty($this->dependency)) {
				$this->class .= ' form_change_checkbox';
				$data = (!empty($this->dependency[$key])) ? implode(',', $this->dependency[$key]) : '';
				$dep = ' data-form-change="'.$data.'"';
			}
			if(!empty($this->class)) $out .= ' class="'.$this->class.'"';
			$out .= '><input type="'.self::$type.'" name="'.$this->name.((count($this->options) > 1) ? '[]' : '').'" value="'.$key.'"'.$selected_choice.$disabled.$dep.'/>'.$opt.'</label>&nbsp;';
		}
		return $out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, 0);
	}
}
?>