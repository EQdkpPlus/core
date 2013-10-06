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

class hradio extends html {

	protected static $type = 'radio';
	
	public $name = '';
	public $disabled = false;
	public $default = 0;
	
	public function _toString() {
		$radiobox  = '';
		if(!is_array($this->list)){
			$this->list = array (
				'0'   => $this->user->lang('cl_off'),
				'1'   => $this->user->lang('cl_on')
			);
		}
		foreach ($this->list as $key => $value) {
			$selected_choice = ((string)$key == (string)$this->value) ? 'checked="checked"' : '';
			$radiobox .='<label><input type="'.self::$type.'" name="'.$this->name.'" value="'.$key.'" '.$selected_choice.' class="'.$this->class.'"/>'.$value.'</label>&nbsp;';
		}
		return $radiobox;
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>