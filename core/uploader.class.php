<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class uploader extends gen_class {
	public static $shortcuts = array('core', 'pfh', 'in', 'tpl', 'config');

	private $added_js = false;
	
	public function upload_mime($strFieldname, $strFolder, $arrMimetypes, $arrExtensions){
		$tempname		= $_FILES[$strFieldname]['tmp_name'];
		$filename		= $_FILES[$strFieldname]['name'];
		$filetype		= $_FILES[$strFieldname]['type'];
		if ($tempname == '') return false;
		$filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
		
		// get the mine....
		$fileEnding		= pathinfo($filename, PATHINFO_EXTENSION);
		$mime = false;
		if(function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')){
			$finfo			= finfo_open(FILEINFO_MIME);
			$mime			= finfo_file($finfo, $tempname);
			finfo_close($finfo);
		}elseif(function_exists('mime_content_type')){
			$mime			= mime_content_type( $tempname );
		}else{
			// try to get the extension... not really secure...
			
			if (in_array($fileEnding, $arrExtensions)) {
				$mime			= $arrMimetypes[0];
			}
		}
		
		$mime = array_shift(preg_split('/[; ]/', $mime));

		if (in_array($mime, $arrMimetypes)){
			//Do no overwrite existing files
			$offset = 0;
			$files = array();
			$file = scandir($this->pfh->FolderPath('files/'.$strFolder, 'eqdkp'));
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
			$strFolder = ($strFolder == '/') ? '' : $strFolder;
				
			if (isFilelinkInFolder($this->pfh->FolderPath('files/'.$strFolder, 'eqdkp', true), $this->pfh->FolderPath('files','eqdkp', true))) {
				$this->pfh->FileMove($tempname, $this->pfh->FolderPath('files/'.$strFolder, 'eqdkp').$filename, true);
			} else {
				unlink($tempname);
			}

			return $filename;
		}
		
		return false;
	}

	public function upload($fieldname, $folder) {
		$filename = $_FILES[$fieldname]['name'];
		if ($filename) {
			$filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
			$extension = pathinfo($filename, PATHINFO_EXTENSION);

			//Extension-Check
			$allowed_extensions = preg_split('/, */', strtolower($this->config->get('upload_allowed_extensions')));
			if (in_array(strtolower($extension), $allowed_extensions)) {

				//Do no overwrite existing files
				$offset = 1;
				$files = array();
				$file = scandir($this->pfh->FolderPath('files'.$folder, 'eqdkp'));

				foreach($file as $this_file) {
					if( valid_folder($this_file) && !is_dir($this_file)) {
						$files[] = $this_file;
					}
				}

				$pathinfo = pathinfo($filename);
				$name = $pathinfo['filename'];
				

				$arrFiles = preg_grep('/^' . preg_quote($name, '/') . '.*\.' . preg_quote($extension, '/') . '/', $files);

				foreach ($arrFiles as $strFile){
					if (preg_match('/_[0-9]+\.' . preg_quote($pathinfo['extension'], '/') . '$/', $strFile)){
						$strFile = str_replace('.' . $pathinfo['extension'], '', $strFile);
						$intValue = intval(substr($strFile, (strrpos($strFile, '_') + 1)));
						$offset = max($offset, $intValue);
					}
				}

				$filename = str_replace($name, $name . '_' . ++$offset, $filename);
				$folder = ($folder == '/') ? '' : $folder;
				
				if (isFilelinkInFolder($this->pfh->FolderPath('files/'.$folder, 'eqdkp', true), $this->pfh->FolderPath('files','eqdkp', true))) {
					$this->pfh->FileMove($_FILES[$fieldname]['tmp_name'], $this->pfh->FolderPath('files/'.$folder, 'eqdkp').$filename, true);
				} else {
					unlink($_FILES[$fieldname]['tmp_name']);
				}

				return $filename;

			};
		}
		return false;
	}

	public function delete(){
		if ($this->in->getArray('files', 'string')){
			foreach($this->in->getArray('files', 'string') as $key=>$value){
				if (isFilelinkInFolder($value, $this->pfh->FolderPath('files','eqdkp', true))) {
					$this->pfh->Delete($value);
				}
			}
		}
	}

	public function move(){
		if ($this->in->getArray('files', 'string')){
			$folder = ($this->in->get('dest_folder') == '/') ? '' : $this->in->get('dest_folder');
			$destination = $this->pfh->FolderPath('files','eqdkp').$folder;
			foreach($this->in->getArray('files', 'string') as $key=>$value){
				if (isFilelinkInFolder($value, $this->pfh->FolderPath('files','eqdkp', true))) {
					$info = pathinfo($value);
					$this->pfh->FileMove($value, $destination.'/'.$info['basename']);
				}
			}
		}
	}

	public function create_folder(){
		if ($this->in->get('name') != ""){
			$folder = ($this->in->get('src_folder') == '/') ? '' : $this->in->get('src_folder');
			if (isFilelinkInFolder($this->pfh->FolderPath('files','eqdkp').$folder.'/'.strtolower($this->in->get('name')), $this->pfh->FolderPath('files','eqdkp', true))) {
				$this->pfh->CheckCreateFolder($this->pfh->FolderPath('files','eqdkp').$folder.'/'.strtolower($this->in->get('name')));
			}
		}
	}

	public function file_tree($directory, $return_link = '', $extensions = array(), $first_call = true, $only_dir = false, $dd = false, $checkboxes = false, $radiobox = false, $selected = array()) {
		if (!is_array($selected)) $selected = array($selected);
		// Get and sort directories/files
		$file = scandir($directory);
		natcasesort($file);
		
		// Make directories first
		$files = $dirs = array();
		foreach($file as $this_file) {
			if( is_dir("$directory/$this_file" ) ) {
				$dirs[] = $this_file; 
			} elseif (!$only_dir && valid_folder($this_file)){
				$files[] = $this_file;
			}
		}
		$file = array_merge($dirs, $files);

		// Filter unwanted extensions
		if( !empty($extensions) ) {
			foreach( array_keys($file) as $key ) {
				if( !is_dir("$directory/$file[$key]") ) {
					$ext = substr($file[$key], strrpos($file[$key], ".") + 1); 
					if( !in_array($ext, $extensions) ) unset($file[$key]);
				}
			}
		}
		$dd_data = array();
		
		if( count($file) > 2 ) { // Use 2 instead of 0 to account for . and .. "directories"
			$php_file_tree = "<ul";
			if( $first_call ) { $php_file_tree .= " class=\"php-file-tree\""; $first_call = false; }
			$php_file_tree .= ">";
			
			foreach( $file as $this_file ) {
				if( $this_file != "." && $this_file != ".." ) {
					if( is_dir("$directory/$this_file") ) {
						// Directory
						$php_file_tree .= "<li class=\"pft-directory\">".(($checkboxes) ? "<input type=\"checkbox\" name=\"files[]\" value=\"".str_replace("//", "/", $directory."/".$this_file)."\"> " : '')."<a href=\"javascript:void();\">" . sanitize($this_file) . "</a>";
						$php_file_tree .= $this->file_tree("$directory/$this_file", $return_link ,$extensions, false, $only_dir, $dd, $checkboxes, $radiobox, $selected);
						$php_file_tree .= "</li>";
						$dd_data["$directory/$this_file"] = $this_file;
						$bla = $this->file_tree(str_replace("//", "/", $directory."/".$this_file), $return_link ,$extensions, false, $only_dir, $dd, $checkboxes, $radiobox, $selected);

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
						$link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
						$php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\">".(($checkboxes) ? '<input type="checkbox" name="files[]" value="'.str_replace("//", "/", $directory."/".$this_file).'"> ': '').(($radiobox) ? "<input type=\"radio\" name=\"".$radiobox."\" value=\"".str_replace("//", "/", $directory."/".$this_file)."\"".((in_array(str_replace("//", "/", $directory."/".$this_file), $selected)) ? ' checked="checked"' : '')."> " : '')."<a href=\"$link\">" . sanitize($this_file) . "</a></li>";
					}
				}
			}
			$php_file_tree .= "</ul>";
		}

		if (!$this->added_js){
			$this->tpl->add_js($this->add_js());
			$this->tpl->add_css($this->add_css());
			$this->added_js = true;
		}

		if ($dd){
			return $dd_data;
		}
		return $php_file_tree;
	}

	public function add_css(){
		$output = "

			.php-file-tree .open {
				font-style: italic;
			}

			.php-file-tree .closed {
				font-style: normal;
			}

			.php-file-tree .pft-directory {
				list-style-image: url(".$this->root_path."images/glyphs/extensions/directory.png);
			}

			/* Default file */
			.php-file-tree LI.pft-file { list-style-image: url(".$this->root_path."images/glyphs/extensions/file.png); }
			/* Additional file types */
			.php-file-tree LI.ext-3gp { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-afp { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-afpa { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-asp { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-aspx { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-avi { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-bat { list-style-image: url(".$this->root_path."images/glyphs/extensions/application.png); }
			.php-file-tree LI.ext-bmp { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-c { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-cfm { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-cgi { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-com { list-style-image: url(".$this->root_path."images/glyphs/extensions/application.png); }
			.php-file-tree LI.ext-cpp { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-css { list-style-image: url(".$this->root_path."images/glyphs/extensions/css.png); }
			.php-file-tree LI.ext-doc { list-style-image: url(".$this->root_path."images/glyphs/extensions/doc.png); }
			.php-file-tree LI.ext-exe { list-style-image: url(".$this->root_path."images/glyphs/extensions/application.png); }
			.php-file-tree LI.ext-gif { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-fla { list-style-image: url(".$this->root_path."images/glyphs/extensions/flash.png); }
			.php-file-tree LI.ext-h { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-htm { list-style-image: url(".$this->root_path."images/glyphs/extensions/html.png); }
			.php-file-tree LI.ext-html { list-style-image: url(".$this->root_path."images/glyphs/extensions/html.png); }
			.php-file-tree LI.ext-jar { list-style-image: url(".$this->root_path."images/glyphs/extensions/java.png); }
			.php-file-tree LI.ext-jpg { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-jpeg { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-js { list-style-image: url(".$this->root_path."images/glyphs/extensions/script.png); }
			.php-file-tree LI.ext-lasso { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-log { list-style-image: url(".$this->root_path."images/glyphs/extensions/txt.png); }
			.php-file-tree LI.ext-m4p { list-style-image: url(".$this->root_path."images/glyphs/extensions/music.png); }
			.php-file-tree LI.ext-mov { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-mp3 { list-style-image: url(".$this->root_path."images/glyphs/extensions/music.png); }
			.php-file-tree LI.ext-mp4 { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-mpg { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-mpeg { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-ogg { list-style-image: url(".$this->root_path."images/glyphs/extensions/music.png); }
			.php-file-tree LI.ext-pcx { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-pdf { list-style-image: url(".$this->root_path."images/glyphs/extensions/pdf.png); }
			.php-file-tree LI.ext-php { list-style-image: url(".$this->root_path."images/glyphs/extensions/php.png); }
			.php-file-tree LI.ext-png { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-ppt { list-style-image: url(".$this->root_path."images/glyphs/extensions/ppt.png); }
			.php-file-tree LI.ext-psd { list-style-image: url(".$this->root_path."images/glyphs/extensions/psd.png); }
			.php-file-tree LI.ext-pl { list-style-image: url(".$this->root_path."images/glyphs/extensions/script.png); }
			.php-file-tree LI.ext-py { list-style-image: url(".$this->root_path."images/glyphs/extensions/script.png); }
			.php-file-tree LI.ext-rb { list-style-image: url(".$this->root_path."images/glyphs/extensions/ruby.png); }
			.php-file-tree LI.ext-rbx { list-style-image: url(".$this->root_path."images/glyphs/extensions/ruby.png); }
			.php-file-tree LI.ext-rhtml { list-style-image: url(".$this->root_path."images/glyphs/extensions/ruby.png); }
			.php-file-tree LI.ext-rpm { list-style-image: url(".$this->root_path."images/glyphs/extensions/linux.png); }
			.php-file-tree LI.ext-ruby { list-style-image: url(".$this->root_path."images/glyphs/extensions/ruby.png); }
			.php-file-tree LI.ext-sql { list-style-image: url(".$this->root_path."images/glyphs/extensions/db.png); }
			.php-file-tree LI.ext-swf { list-style-image: url(".$this->root_path."images/glyphs/extensions/flash.png); }
			.php-file-tree LI.ext-tif { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-tiff { list-style-image: url(".$this->root_path."images/glyphs/extensions/picture.png); }
			.php-file-tree LI.ext-txt { list-style-image: url(".$this->root_path."images/glyphs/extensions/txt.png); }
			.php-file-tree LI.ext-vb { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-wav { list-style-image: url(".$this->root_path."images/glyphs/extensions/music.png); }
			.php-file-tree LI.ext-wmv { list-style-image: url(".$this->root_path."images/glyphs/extensions/film.png); }
			.php-file-tree LI.ext-xls { list-style-image: url(".$this->root_path."images/glyphs/extensions/xls.png); }
			.php-file-tree LI.ext-xml { list-style-image: url(".$this->root_path."images/glyphs/extensions/code.png); }
			.php-file-tree LI.ext-zip { list-style-image: url(".$this->root_path."images/glyphs/extensions/zip.png); }
			/* You can add millions of these... */
		";
		return $output;
	}

	public function add_js(){
		$output = '$(document).ready( function() {

			// Hide all subfolders at startup
			$(".php-file-tree").find("UL").hide();

			// Expand/collapse on click
			$(".pft-directory a").click( function() {
				$(this).parent().find("UL:first").slideToggle("medium");
				if( $(this).parent().attr(\'className\') == "pft-directory" ) return false;
			});	
		});';
		return $output;
	}
}	//close class
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_uploader', uploader::$shortcuts);
?>