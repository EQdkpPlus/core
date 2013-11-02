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
 * name			(string) 	name of the textarea
 * id			(string)	id of the textarea
 * value		(int) 		text
 * class		(string)	class for the textarea
 * rows			(int) 		rows of the textarea
 * cols			(int) 		cols of the textarea
 * disabled		(boolean)	disabled field
 * codeinput	(boolean)	allow html-tags being used
 * bbcodeeditor	(boolean) 	apply a bbcodeeditor to this textarea
 */
class htextarea extends html {

	protected static $type = 'textarea';
	
	public $name = '';
	public $rows = 5;
	public $cols = 10;
	public $disabled = false;
	public $codeinput = false;
	public $bbcodeeditor = false;
	
	public function _toString() {
		$out = '<textarea name="'.$this->name.'" rows="'.$this->rows.'" cols="'.$this->cols.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if($this->bbcodeeditor) $this->class = (empty($this->class)) ? 'mceEditor_bbcode' : $this->class.' mceEditor_bbcode';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		return $out.'>'.$this->value.'</textarea>';
	}
	
	public function inpval() {
		$value = $this->in->get($this->name, '', ($this->codeinput) ? 'raw' : '');
		return $value;
	}
}
?>