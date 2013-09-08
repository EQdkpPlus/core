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

class hdropdown extends html {
	public static $shortcuts = array('in');

	protected static $type = 'dropdown';
	
	public $name = '';
	public $desc = '';
	public $disabled = false;
	
	public function __construct($name, $options=array()) {
		$this->name = $name;
		foreach($options as $key => $option) {
			$this->$key = $option;
		}
	}
	
	public function __toString() {
		$dropdown = $this->desc.": ";
		$dropdown .= '<select size="1" name="'.$name.'"';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$dropdown .= ' id="'.$this->id.'"';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if(!is_array($this->todisable)) $this->todisable = array($this->todisable);
		if(is_array($this->options) && count($this->options) > 0){
			foreach ($this->options as $key => $value) {
				if(is_array($value)){
					$dropdown .= "<optgroup label='".$key."'>";
					foreach ($value as $key2 => $value2) {
						$selected_choice = ($key2 == $this->selected) ? ' selected="selected"' : '';
						$disabled = (isset($this->todisable[$key]) && is_array($this->todisable[$key]) && ($key2 === 0 && in_array($key2, $this->todisable[$key], true)) || ($key2 !== 0 && in_array($key2, $this->todisable[$key]))) ? ' disabled="disabled"' : '';
						$dropdown .= "<option value='".$key2."'".$selected_choice.$disabled.">".$value2."</option>";
					}
					$dropdown .= "</optgroup>";
				}else{
					$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
					$selected_choice = (($key == $selected)) ? 'selected="selected"' : '';
					$dropdown .= "<option value='".$key."' ".$selected_choice.$disabled.">".$value."</option>";
				}
			}
		}else{
			$dropdown .= "<option value=''></option>";
		}
		$dropdown .= "</select>";
		return $dropdown;
	}
	
	public function inpval() {
		$value = $this->in->get($this->name, '', ($this->codeinput) ? 'raw' : ''));
		if($this->encrypt) $value = $this->crypt->encrypt($value);
		return $value;
	}
}
?>