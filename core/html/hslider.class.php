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
 * value		
 * label		(string)	displayed text next to the slider
 * range		(boolean)	a double slider (range of two values) or a single slider (one value)?
 * min			(int)		minimum value of the field
 * max			(int)		maximum value of the field
 * width		(string)	width in (px) for the slider
 */
class hslider extends html {

	protected static $type = 'slider';
	
	public $name = '';
	public $range = true;
	private $options = array('min', 'max', 'value', 'width', 'label', 'name');
	private $out = '';
	
	protected function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$options = array();
		foreach($this->options as $opt) $options[$opt] = $this->$opt;
		$options['value'] = unserialize($options['value']);
		$this->out = $this->jquery->Slider($this->id, $options, ($this->range) ? 'range' : 'normal');
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function inpval() {
		return ($this->range) ? serialize($this->in->getArray($this->name, 'int')) : $this->in->get($this->name, 0);
	}
}
?>