<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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
 */
class htextareamultilang extends html {

	protected static $type = 'textarea';
	
	public $name = '';
	public $rows = 5;
	public $cols = 10;
	public $disabled = false;
	public $required = false;
	
	private $out = '';
	
	public function _construct() {
		$arrLanguages = $this->user->getAvailableLanguages();
		$strDefaultLanguage = $this->config->get('default_lang');
		$this->jquery->init_multilang();
		
		if(is_serialized($this->value)) {
			$this->value = @unserialize($this->value);
		} elseif(!is_array($this->value) && $this->value != ""){
			$strValue = $this->value;
			$this->value = array();
			$this->value[$strDefaultLanguage] = $strValue;
		}
		
		$this->out = '<div class="input-multilang textarea">
			<div class="multilang-switcher-container hand"><div class="multilang-switcher"><span>'.$arrLanguages[$strDefaultLanguage].'</span> <i class="fa fa-caret-down fa-lg"></i></div>
			<div class="multilang-dropdown"><ul>
		';
		foreach($arrLanguages as $strKey => $strLang){
			$this->out .= '<li data-lang="'.$strLang.'" data-key="'.$strKey.'" class="'.(($strKey == $strDefaultLanguage) ? 'active' : '').'">'.$strLang.'</li>';
		}
		$this->out .= '</ul></div><br />';
		
		foreach($arrLanguages as $strKey => $strLang){
			$out = '<textarea name="'.$this->name.'['.$strKey.']" rows="'.$this->rows.'" cols="'.$this->cols.'" ';
			if(empty($this->id)) $this->id = $this->cleanid($this->name);
			$out .= 'id="'.$this->id.'" ';
			$class = "taml_".$this->cleanid($this->name)." ".$strKey;
			if(!empty($this->class)) $class .= ' '.$this->class;
			$out .= 'class="'.$class.'" ';
			if($this->disabled) $out .= 'disabled="disabled" ';
			if(!empty($this->js)) $out.= $this->js.' ';
			if($this->required && $strKey == $strDefaultLanguage) $out .= 'required="required" ';
			if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
			if ($strKey != $strDefaultLanguage)  $out .= ' style="display:none;"';
			$out .= '>';
			if(isset($this->value) && isset($this->value[$strKey])) $out .= $this->value[$strKey];
			$out .= '</textarea>';
			
			$this->out .= $out;
		}
		
		$this->out .= '</div>';
		if($this->required) $this->out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		$arrInput = $this->in->getArray($this->name, $this->inptype);
		return serialize($arrInput);
	}
}
?>