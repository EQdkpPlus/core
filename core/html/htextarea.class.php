<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

	public $name				= '';
	public $rows				= 5;
	public $cols				= 60;
	public $disabled			= false;
	public $codeinput			= false;
	public $bbcodeeditor		= false;
	public $htmleditor			= false;
	public $required			= false;
	public $fvmessage			= false;
	public $readonly			= false;
	public $style				= "";
	public $textarea_height		= "350px";

	private $out				= '';

	public function _construct() {
	}

	public function output() {
		$out = '<textarea name="'.$this->name.'" rows="'.$this->rows.'" cols="'.$this->cols.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if($this->bbcodeeditor) {
			$this->class = (empty($this->class)) ? 'mceEditor_bbcode' : $this->class.' mceEditor_bbcode';
			$this->tinyMCE->editor_bbcode();
		}
		if($this->htmleditor){
			$this->class = (empty($this->class)) ? $this->id : $this->class.' '.$this->id;
			$this->jquery->CodeEditor($this->id, "", "html", array('textarea_height' => $this->textarea_height));
		}
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->style)) $out .= 'style="'.$this->style.'" ';
		if($this->disabled) $out .= 'disabled="disabled" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		if($this->required) $out .= ' required="required" data-fv-message="'.(($this->fvmessage) ? $this->fvmessage : registry::fetch('user')->lang('fv_required')).'"';
		if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
		$out .= '>'.$this->value.'</textarea>';
		if($this->required) $out .= '<i class="fa fa-asterisk required small"></i>';
		return $out;
	}

	public function _inpval() {
		$value = $this->in->get($this->name, '', ($this->codeinput) ? 'raw' : '');
		return $value;
	}
}
