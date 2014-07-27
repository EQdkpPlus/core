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
	public $required = false;
	
	private $out = '';
	
	public function _construct() {
		$out = '<textarea name="'.$this->name.'" rows="'.$this->rows.'" cols="'.$this->cols.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if($this->bbcodeeditor) {
			$this->class = (empty($this->class)) ? 'mceEditor_bbcode' : $this->class.' mceEditor_bbcode';
			$this->tinyMCE->editor_bbcode();
		}
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		if($this->required) $out .= 'required="required" ';
		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		$out .= '>'.$this->value.'</textarea>';
		if($this->required) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
		$this->out = $out;
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		$value = $this->in->get($this->name, '', ($this->codeinput) ? 'raw' : '');
		return $value;
	}
}
?>