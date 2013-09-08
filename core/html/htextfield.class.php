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

class htextfield extends html {
	public static $shortcuts = array('in');

	protected static $type = 'text';
	
	public $name = '';
	public $readonly = false;
	
	public function __construct($name, $options=array()) {
		$this->name = $name;
		foreach($options as $key => $option) {
			$this->$key = $option;
		}
	}
	
	public function __toString() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(!empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		return $out.' />';
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>