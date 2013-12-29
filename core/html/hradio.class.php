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
	
	public function _toString() {
		$radiobox  = '';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
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
			if($this->tolang) $opt = $this->user->lang($opt);
			$radiobox .= '><input type="'.self::$type.'" name="'.$this->name.'" value="'.$key.'"'.$selected_choice.$disabled.$dep.'/>'.$opt.'</label>&nbsp;';
		}
		return '<div id="'.$this->id.'">'.$radiobox.'</div>';
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>