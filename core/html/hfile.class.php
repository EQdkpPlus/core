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
 * spinner		(boolean)	make a spinner out of the field?
 * disabled		(boolean)	disabled field
 * autocomplete	(array)		if not empty: array containing the elements on which to autocomplete (not to use together with spinner)
 * colorpicker	(boolean) 	apply a colorpicker to this field
 */
class hfile extends html {

	protected static $type = 'file';
	
	public $name = '';
	public $readonly = false;
	public $class = 'input';
	public $inptype = '';
	public $required = false;
	public $preview = false;
	
	
	protected $mimetypes = false;
	protected $numerate = false;
	protected $extensions = array();
	private $out = '';
	
	public function _construct() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
				
		if(isset($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if(!empty($this->size)) $out .= 'size="'.$this->size.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		if($this->preview) $out .= 'onchange="previewImage_'.$this->name.'(this);"';
		$out .= ' />';
		if($this->required) $out .= '<span class="fv_msg" data-errormessage="'.registry::fetch('user')->lang('fv_required').'"></span>';
		if ($this->preview){
			$out = '<img src="'.((isset($this->value)) ? $this->value : registry::get_const('server_path').'images/global/default-image.svg').'" class="uploadPreview" style="max-height: 60px;"/>'.$out;
			
			register('tpl')->add_js('
			function previewImage_'.$this->name.'(object) {
				if (object.files[0].type == "image/jpeg" || object.files[0].type == "image/png" || object.files[0].type == "image/gif"){
					var oFReader = new FileReader();
					oFReader.readAsDataURL(object.files[0]);
			
					oFReader.onload = function (oFREvent) {
						$(object).parent().find(\'.uploadPreview\').attr(\'src\', oFREvent.target.result);
					};
				}
			};'
					
			);
		}
		$this->out = $out;
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		$tempname		= $_FILES[$this->name]['tmp_name'];
		$filename		= $_FILES[$this->name]['name'];
		$filetype		= $_FILES[$this->name]['type'];
		if ($tempname == '') return false;

		
		$fileEnding		= pathinfo($filename, PATHINFO_EXTENSION);
		if ($this->mimetypes){
			$mime = false;
			if(function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')){
				$finfo			= finfo_open(FILEINFO_MIME);
				$mime			= finfo_file($finfo, $tempname);
				finfo_close($finfo);
				
				$mime = array_shift(preg_split('/[; ]/', $mime));					
				if (!in_array($mime, $this->mimetypes)) return false;
			}elseif(function_exists('mime_content_type')){
				$mime			= mime_content_type( $tempname );
				$mime = array_shift(preg_split('/[; ]/', $mime));
				if (!in_array($mime, $this->mimetypes)) return false;
			}else{
				// try to get the extension... not really secure...			
				if (!in_array($fileEnding, $this->extensions)) return false;
			}			
			
		} else {
			
			if (!in_array($fileEnding, $this->extensions)) {
				return false;
			}
		}
		
		
		if($this->numerate){
			//Do no overwrite existing files
			$offset = 0;
			$files = array();
			$file = scandir($this->root_path.$this->folder);
			foreach($file as $this_file) {
				if( valid_folder($this_file) && !is_dir($this_file)) {
					$files[] = $this_file;
				}
			}
			
			$pathinfo = pathinfo($filename);
			$name = $pathinfo['filename'];
			
			$arrFiles = preg_grep('/^' . preg_quote($name, '/') . '.*\.' . preg_quote($fileEnding, '/') . '/', $files);
			
			foreach ($arrFiles as $strFile){
				if (preg_match('/_[0-9]+\.' . preg_quote($pathinfo['extension'], '/') . '$/', $strFile)){
					$strFile = str_replace('.' . $pathinfo['extension'], '', $strFile);
					$intValue = intval(substr($strFile, (strrpos($strFile, '_') + 1)));
					$offset = max($offset, $intValue);
				}
			}
			
			$filename = str_replace($name, $name . '_' . ++$offset, $filename);	
		}		

		if (isFilelinkInFolder(str_replace(registry::get_const('root_path'),"", $this->folder.$filename), str_replace(registry::get_const('root_path'),"", $this->folder))) {
			$this->pfh->FileMove($tempname, $this->folder.$filename, true);
		} else {
			unlink($tempname);
			return false;
		}

		return str_replace(registry::get_const('root_path'),"", $this->folder.$filename);		
	}
}
?>