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
class hfiletree extends html {

	protected static $type = 'text';
	
	public $name = '';
	public $folder;
	public $extensions = array();
	public $inputBoxFormat = 'radio';
	public $dirsOnly = false;
	

	public $inptype = '';
	
	private $out = '';
	
	public function _construct() {
		
		
		
		$this->tpl->add_js('$(document).ready( function() {
		
			// Hide all sub$folders at startup
			$(".file-tree-'.$this->name.'").find("UL").hide();
		
			// Expand/collapse on click
			$(".file-tree-'.$this->name.' .pft-directory a").click( function() {
				$(this).parent().find("UL:first").slideToggle("medium");
				if( $(this).parent().attr(\'className\') == "pft-directory" ) return false;
			});
			$(".file-tree-'.$this->name.' .pft-root-directory a").click( function() {
				console.log($(this).parent());
				$(this).parent().parent().find("UL:first").slideToggle("medium");
			});
		});', 'docready');
		
		
		$this->tpl->add_css('				
			.pft-directory ul {
				margin-left: 10px;
			}
				
			.file-tree-'.$this->name.' > ul {
				margin-left: 10px;
			}
				
			.file-tree-'.$this->name.' li {
				padding: 3px;
			}
		');

		$this->out = "<ul class=\"file-tree-".$this->name."\"><li class=\"pft-root-directory\"><a href=\"javascript:void(0);\"><i class=\"fa fa-lg fa-folder\"></i> ".$this->folder.'</a></li>'.$this->file_tree($this->folder).'</ul>';
		if (!$this->out) $this->out = "";
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '', $this->inptype);
	}
	
	private function file_tree($folder, $first_call=true) {
		if (!is_array($this->value)) $this->value = array($this->value);
		// Get and sort directories/files
		$file = scandir($folder);
		natcasesort($file);
	
		// Make directories first
		$files = $dirs = array();
		foreach($file as $this_file) {
			if( is_dir("$folder/$this_file" ) ) {
				$dirs[] = $this_file;
			} elseif (!$this->dirsOnly && valid_folder($this_file)){
				$files[] = $this_file;
			}
		}
		$file = array_merge($dirs, $files);
	
		// Filter unwanted extensions
		if( !empty($this->extensions) ) {
			foreach( array_keys($file) as $key ) {
				if( !is_dir("$folder/$file[$key]") ) {
					$ext = substr($file[$key], strrpos($file[$key], ".") + 1);
					if( !in_array($ext, $this->extensions) ) unset($file[$key]);
				}
			}
		}
		$dd_data = array();
	
		if( count($file) > 2 ) { // Use 2 instead of 0 to account for . and .. "directories"
			$php_file_tree = "<ul";
			//if( $first_call ) { $php_file_tree .= " class=\"file-tree-".$this->name."\""; $first_call = false; }
			$php_file_tree .= ">";
				
			foreach( $file as $this_file ) {
				if( $this_file != "." && $this_file != ".." ) {
					if( is_dir("$folder/$this_file") ) {
						// Directory
						$php_file_tree .= "<li class=\"pft-directory\"><i class=\"fa fa-lg fa-folder-o\"></i> ".(($this->inputBoxFormat == "checkbox") ? "<input type=\"checkbox\" name=\"".$this->name."[]\" value=\"".str_replace("//", "/", $folder."/".$this_file)."\"> " : '')."<a href=\"javascript:void(0);\">" . sanitize($this_file) . "</a>";
						$php_file_tree .= $this->file_tree("$folder/$this_file", false);
						$php_file_tree .= "</li>";
						$dd_data["$folder/$this_file"] = $this_file;
						$bla = $this->file_tree(str_replace("//", "/", $folder."/".$this_file), false);
	
						if (is_array($bla)){
							foreach ($bla as $key => $value){
								$dd_data[$key] = '&nbsp;&nbsp;&nbsp;'. $value;
							}
						}
						$i++;
					} else {
						// File
						// Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
						$ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);
						$link = str_replace("[link]", "$folder/" . urlencode($this_file), $return_link);
						$php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\">".(($this->inputBoxFormat == "checkbox") ? '<input type="checkbox" name="'.$this->name.'[]" value="'.str_replace("//", "/", $folder."/".$this_file).'"> ': '').((true) ? "<input type=\"radio\" name=\"".$this->name."\" value=\"".str_replace("//", "/", $folder."/".$this_file)."\"".((in_array(str_replace("//", "/", $folder."/".$this_file), $this->value)) ? ' checked="checked"' : '')."> " : '')."<a>" . sanitize($this_file) . "</a></li>";
					}
				}
			}
			$php_file_tree .= "</ul>";
		}
		
		return $php_file_tree;
	}
}
?>