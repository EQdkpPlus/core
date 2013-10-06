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

class hslider extends html {

	protected static $type = 'slider';
	
	public $name = '';
	public $range = true;
	private $options = array('min', 'max', 'value', 'width', 'label', 'name');
	
	protected function _toString() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$options = array();
		foreach($this->options as $opt) $options[$opt] = $this->$opt;
		return $this->jquery->Slider($this->id, $options, ($this->range) ? 'range' : 'normal');
	}
	
	public function inpval() {
		return ($this->range) ? $this->in->getArray($this->name, 'int') : $this->in->get($this->name, 0);
	}
}
?>