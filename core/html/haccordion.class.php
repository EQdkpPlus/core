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
 * name			classname for the tooltip call
 * id			id for the div/span
 * content		content of the tooltip
 * label		text on which the tooltip shall be displayed
 * usediv		use a div or a span
 * additional options for qtip
 *		contfunc, name, my, mat, classes, width
 */

class haccordion extends html {
	protected static $type = 'accordion';
	
	public $name = '';
	public $content = '';
	public $label = '';
	public $usediv = false;
	
	private $contfunc = true;
	private $accordion_opts	= array('active', 'collapsible', 'disabled', 'event');
	private $out = '';

	public function _construct() {
		if(empty($this->name)) $this->name = unique_id();
		if(empty($this->id)) $this->id = unique_id();
		
		$acc_opts = array();
		foreach($this->accordion_opts as $opt) $acc_opts[$opt] = $this->$opt;

		$this->out = $this->jquery->Accordion($this->name, $this->options, $acc_opts);
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>