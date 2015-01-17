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
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		
 * class		(string)	class for the field
 * readonly		(boolean)	field readonly?
 * size			(int)		size of the field
 * js			(string)	extra js which shall be injected into the field
 * disabled		(boolean)	disabled field
 */
class htextmultilang extends html {

	protected static $type = 'text';
	
	public $name = '';
	public $readonly = false;
	public $required = false;
	public $autocomplete = array();
	public $class = 'input';
	public $inptype = 'string';
	
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

		
		$this->out = '<div class="input-multilang">
			<div class="multilang-switcher-container hand"><div class="multilang-switcher"><span>'.$arrLanguages[$strDefaultLanguage].'</span> <i class="fa fa-caret-down fa-lg"></i></div>
			<div class="multilang-dropdown"><ul>
		';
		foreach($arrLanguages as $strKey => $strLang){
			$this->out .= '<li data-lang="'.$strLang.'" data-key="'.$strKey.'" class="'.(($strKey == $strDefaultLanguage) ? 'active' : '').'">'.$strLang.'</li>';
		}
		$this->out .= '</ul></div>';
		//The fields
		foreach($arrLanguages as $strKey => $strLang){
			$out = '<input type="'.self::$type.'" name="'.$this->name.'['.$strKey.']" ';
			if(empty($this->id)) $this->id = $this->cleanid($this->name);
			$out .= 'id="'.$this->id.'" ';
			if(isset($this->value) && isset($this->value[$strKey])) $out .= 'value="'.$this->value[$strKey].'" ';
			$class = "tml_".$this->cleanid($this->name)." ".$strKey;
			$class .= " ".$this->class;
			if(!empty($this->pattern) && !empty($this->successmsg)) $class .= ' fv_success';
			if(!empty($this->equalto)) $class .= ' equalto';
			if(!empty($this->class)) $out .= 'class="'.$class.'" ';
			if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
			if($this->readonly) $out .= 'readonly="readonly" ';
			if($this->required && $strKey == $strDefaultLanguage) $out .= 'required="required" ';
			if(!empty($this->pattern)) $out .= 'pattern="'.$this->pattern($this->pattern).'" ';
			if(!empty($this->euqalto)) $out .= 'data-equalto="'.$this->equalto.'" ';
			if(!empty($this->placeholder)) $out .= 'placeholder="'.$this->placeholder.'" ';
			if(!empty($this->js)) $out.= $this->js.' ';
			if ($strKey != $strDefaultLanguage)  $out .= ' style="display:none;"';
			
			$out .= ' />';
			$this->out .= $out;
		}
		
		$this->out .= '</div>';
		if(!empty($this->pattern)) $this->out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_sample_pattern').'"></span>';
		elseif($this->required) $this->out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
		if(!empty($this->equalto)) $this->out .= '<span class="errormessage error-message-red" style="display:none;"><i class="fa fa-exclamation-triangle fa-lg"></i>'.registry::fetch('user')->lang('fv_required_password_repeat').'</span>';
		if(!empty($this->after_txt)) $this->out .= $this->after_txt;
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