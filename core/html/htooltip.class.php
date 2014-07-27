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

class htooltip extends html {
	protected static $type = 'none';
	
	public $name = '';
	public $content = '';
	public $label = '';
	public $usediv = false;
	
	private $contfunc = true;
	private $all_opts = array('contfunc', 'name', 'my', 'at', 'classes', 'width');
	private $out = '';

	public function _construct() {
		if(empty($this->name)) $this->name = unique_id();
		if(empty($this->id)) $this->id = unique_id();
		$options = array();
		foreach($this->all_opts as $opt) {
			$options[$opt] = $this->$opt;
		}
		$this->jquery->qtip('.'.$this->name, 'return $(".'.$this->name.'_c", this).html();', $options);
		if(isset($this->usediv) && $this->usediv){
			$this->out = '<div class="'.$this->name.'" id="'.$this->id.'"><div class="'.$this->name.'_c" style="display:none;">'.$this->content.'</div>'.$this->label.'</div>';
		}else{
			$this->out = '<span class="'.$this->name.'" id="'.$this->id.'"><span class="'.$this->name.'_c" style="display:none;">'.$this->content.'</span>'.$this->label.'</span>';
		}
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return '';
	}
}
?>