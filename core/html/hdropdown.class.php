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
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		(string)	selected option
 * class		(string)	class for the field
 * js			(string)	extra js which shall be injected into the field
 * options		(array)		dropdown-list
 * dependency	(array)		array containing IDs of other inputs fields to disable, format: array(opt1_key => array(id1,id2,...), opt2_key => array(id5,id6,...))
 * tolang		(boolean)	whether to put the vals of the list into language
 * disabled		(boolean)	disabled field
 * todisable	(array)		if not empty: array containing the elements which shall be disabled
 */
class hdropdown extends html {

	protected static $type = 'dropdown';
	
	public $name = '';
	public $disabled = false;
	public $tolang = false;
	public $class = 'input';
	
	public function _toString() {
		$dropdown = '<select size="1" name="'.$this->name.'"';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$dropdown .= ' id="'.$this->id.'"';
		if(!empty($this->dependency)) $this->class .= ' form_change';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if(!is_array($this->todisable)) {
			if (isset($this->todisable)) {
				$this->todisable = array($this->todisable);
			} else $this->todisable = array();
		}
		if(is_array($this->options) && count($this->options) > 0){
			foreach ($this->options as $key => $value) {
				if(is_array($value)){
					$dropdown .= "<optgroup label='".$key."'>";
					foreach ($value as $key2 => $value2) {
						$dep = (!empty($this->dependency[$key2])) ? ' data-form-change="'.implode(',', $this->dependency[$key2]).'"' : '';
						if($this->tolang) $value2 = ($this->user->lang($value2, false, false)) ? $this->user->lang($value2) : (($this->game->glang($value2)) ? $this->game->glang($value2) : $value2);
						$selected_choice = ($key2 == $this->value) ? ' selected="selected"' : '';
						$disabled = (isset($this->todisable[$key]) && is_array($this->todisable[$key]) && (($key2 === 0 && in_array($key2, $this->todisable[$key], true)) || ($key2 !== 0 && in_array($key2, $this->todisable[$key])))) ? ' disabled="disabled"' : '';
						$dropdown .= "<option value='".$key2."'".$selected_choice.$disabled.$dep.">".$value2."</option>";
					}
					$dropdown .= "</optgroup>";
				}else{
					$dep = (!empty($this->dependency[$key])) ? ' data-form-change="'.implode(',', $this->dependency[$key]).'"' : ' data-form-change=""';
					if($this->tolang) $value = ($this->user->lang($value, false, false)) ? $this->user->lang($value) : (($this->game->glang($value)) ? $this->game->glang($value) : $value);
					$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
					$selected_choice = (($key == $this->value)) ? 'selected="selected"' : '';
					$dropdown .= "<option value=\"".$key."\" ".$selected_choice.$disabled.$dep.">".$value."</option>";
				}
			}
		}else{
			$dropdown .= "<option value=''></option>";
		}
		$dropdown .= "</select>";
		return $dropdown;
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>