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
 * name			(string) 	name of the field
 * value					key of the radio to be checked
 * class		(string)	class for labels of the fields
 * options		(array)		list containing all the options, if empty it defaults to yes / no
 * disabled		(boolean)	disabled field
 */
class hradio extends html {

	protected static $type = 'radio';
	
	public $name = '';
	public $disabled = false;
	public $default = 0;
	
	public function _toString() {
		$radiobox  = '';
		if(empty($this->options)){
			$this->options = array (
				'0'   => $this->user->lang('cl_off'),
				'1'   => $this->user->lang('cl_on')
			);
		}
		foreach ($this->options as $key => $opt) {
			$selected_choice = ((string)$key == (string)$this->value) ? ' checked="checked"' : '';
			$disabled = ($this->disabled) ? ' disabled="disabled"' : '';
			$radiobox .= '<label';
			if(!empty($this->class)) $raidobox .= ' class="'.$this->class.'"';
			$raidobox .= '><input type="'.self::$type.'" name="'.$this->name.'" value="'.$key.'"'.$selected_choice.$disabled.'/>'.$opt.'</label>&nbsp;';
		}
		return $radiobox;
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>