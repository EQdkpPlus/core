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
 * name			(string) 	name of the input
 * id			(string)	id of the input
 * value		(int) 		timestamp
 * class		(string)	class for the input
 * enablesecs	(boolean) 	wether seconds shall be used
 * hourf		(int) 		24 or 12 hour format
 */
class htimepicker extends html {

	protected static $type = 'timepicker';
	
	public $name = '';
	public $enablesecs = false;
	public $hourf = 24;
	public $value = 0;
	
	private $out = '';
	
	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out = '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'"';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		$this->jquery->timePicker($this->id, $this->name, $this->value, $this->enablesecs, $this->hourf);
		$this->out = $out.' />';
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function inpval() {
		return $this->in->get($this->name, '');
	}
}
?>