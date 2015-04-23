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
 *
 * This class was initally written for the phpbb. Most code of this file does not match
 * the old code any longer, as it was rewritten for the needs of eqdkp-plus. Because of that
 * it is used under the GPL as part of the libraries of eqdkp plus.
 * Parts of this class may contain code of the smarty project
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class template extends gen_class {
	
	private $handle					= '';
	protected $_data				= array();
	protected $caching				= false;
	protected $root					= '';
	protected $data_root			= '';
	protected $style_code			= '';
	protected $root_dir				= '';
	protected $template				= '';
	protected $files				= array();
	protected $filename				= '';
	protected $error_message		= false;
	protected $compiled_code		= array();
	protected $uncompiled_code		= array();
	protected $statcontent_file		= array('main'=>'index.tpl');
	protected $body_filename		= '';
	protected $is_install			= false;
	protected $intErrorCount		= 0;

	// Various counters and storage arrays
	protected $block_names			= array();
	protected $block_else_level		= array();
	protected $include_counter		= 1;
	protected $block_nesting_level	= 0;
	protected $tplout_set				= array(
										'js_code'		=> false,
										'js_file'		=> false,
										'css_code'		=> false,
										'css_code_direct'=> false,
										'css_file'		=> false,
										'rss_feeds'		=> false,
										'statichtml'	=> false,
										'meta'			=> false,
									);
	protected $tpl_output			= array();
	
	// Save array for states
	private $states = array();

	public function __construct($install=false) {
		$this->is_install = $install;
		require_once($this->root_path . 'libraries/_statics/CSS/CSS.php');
		require_once($this->root_path . 'libraries/_statics/JS/JShrink.php');
	}
	
	/*
	 *  State-handling functions
	 *    save and load output states of template for i.e. showing different output in case of errors
	 */
	public function save_state($name) {
		$this->states[$name]['tpl_output'] = $this->tpl_output;
		$this->states[$name]['data'] = $this->_data;
	}
	
	public function load_state($name) {
		if(!isset($this->states[$name])) return false;
		$this->_data = $this->states[$name]['data'];
		$this->tpl_output = $this->states[$name]['tpl_output'];
		return true;
	}

	private function lang($key) {
		if($this->is_install) return '';
		return $this->user->lang($key);
	}

	private function glang($key, $lang=false, $exists=false) {
		if($this->is_install) return '';
		return $this->game->glang($key, $lang, $exists);
	}

	public function get_templatedata($key){
		return isset($this->tpl_output[$key]) ? $this->tpl_output[$key] : array();
	}

	public function set_templateout($key, $value=false){
		$this->tplout_set[$key] = $value;
	}

	public function get_templateout($key){
		return $this->tplout_set[$key];
	}

	public function set_template($style_code = '', $template = '', $root_dir = 'templates/'){
		$this->style_code	= $style_code;
		$this->template		= $template;

		if( substr($root_dir, strlen($root_dir)-1) != '/' ){
			$root_dir .= '/';
		}

		// set the template dir & the fallback base template
		$this->root			= $this->root_path.$root_dir.$style_code;
		$this->base_template= $this->root_path.$root_dir.'base_template';
		$this->data_root	= $this->pfh->FolderPath('templates/'.$style_code, 'eqdkp');
		$this->root_dir		= $root_dir;

		// Cache part...
		$myPlugin			= (defined('PLUGIN')) ? PLUGIN : '';

		$arrCacheDir = array('template', ((strlen($this->style_code)) ? $this->style_code : 'base_template'));
		if (strlen($myPlugin)) array_push($arrCacheDir, $myPlugin);

		$this->cachedir		= $this->pfh->FolderPath($arrCacheDir,			'cache');

		// create the admin & calendar folder & set folders to chnmod 777
		$copiedarrCacheDir = $arrCacheDir;
		array_push($copiedarrCacheDir, 'admin');
		$this->pfh->FolderPath($copiedarrCacheDir,	'cache');
		array_push($arrCacheDir, 'calendar');
		$this->pfh->FolderPath($arrCacheDir, 'cache');

		return true;
	}

	/**
	* Assign custom JS Code to the Header
	*/
	public function add_js($varval, $eop=false){
		switch($eop){
			case 'eop':			$identifier = 'js_code_eop';		break;
			case 'eop2':		$identifier = 'js_code_eop2';		break;
			case 'docready':	$identifier = 'js_code_docready';	break;
			default:			$identifier = 'js_code';
		}
		$this->tpl_output[$identifier][] = $varval;
	}

	/**
	* Assign custom JS File to the Header
	*/
	public function js_file($varval, $key=false){
		if($key !== false){
			$this->tpl_output['js_file'][$key] = array('file' => $varval);
		} else $this->tpl_output['js_file'][] = array('file' => $varval);
	}

	public function staticHTML($varval){
		$this->tpl_output['statichtml'][] = $varval;
	}
	
	public function add_meta($strMetatag){
		$this->tpl_output['meta'][] = $strMetatag;
	}

	/**
	* Assign custom CSS File to the Header
	*/
	public function css_file($varval, $media='screen', $first=false){
		if($first) $this->tpl_output['css_file'] 	= array_merge(array(array('file'=>$varval, 'type'=>'text/css', 'media'=> $media)), $this->tpl_output['css_file']);
		else $this->tpl_output['css_file'][] 		= array('file'=>$varval, 'type'=>'text/css', 'media'=> $media);
	}

	/**
	* Assign custom CSS Code to the Header
	*/
	public function add_css($varval, $blnDirect=false){
		if($blnDirect){
			$this->tpl_output['css_code_direct'][] = $varval;
		} else $this->tpl_output['css_code'][] = $varval;
	}
	
	
	public function combine_css(){
		$strInlineCSS = "";
		$arrHash = $data = $arrFiles = $arrOrigFiles = array();
		
		if (is_array($this->tpl_output['css_code'])){
			foreach($this->tpl_output['css_code'] as $key => $strInlineCode){
				$arrHash[] = md5($strInlineCode);
				$strInlineCSS .= " ".$strInlineCode;
				unset($this->tpl_output['css_code'][$key]);
			}
		}
		$data[] = array('content' => $strInlineCSS, 'path' => false);
			
		$storage_folder = $this->pfh->FolderPath('templates', 'eqdkp');
		
		if (is_array($this->tpl_output['css_file'])){
			foreach($this->tpl_output['css_file'] as $key => $val){
				$val['file'] = $this->env->server_to_rootpath($val['file']);

				//Resolve file
				$val['orig_file'] = $val['file'];
				$val['file'] = $this->resolve_css_file($val['file']);
				if(!$val['file']) continue;
				
				if ($val['media'] == 'screen' && is_file($val['file'])){
					if (strpos('combined_', $val['file']) !== false) continue;
					$arrHash[] = md5_file($val['file']);
					$arrFiles[] = $val['file'];
					$arrOrigFiles[] = $val['orig_file'];
					unset($this->tpl_output['css_file'][$key]);		
				}
			}
		}
		
		//Check if there is an file for this hash
		asort($arrHash);
		$strHash = md5(implode(";", $arrHash));
		$combinedFile = $storage_folder.$this->style_code.'/combined_'.$strHash.'.css';

		if (!is_array($this->tpl_output['css_file'])) $this->tpl_output['css_file'] = array();
		
		if (is_file($combinedFile)){
			array_unshift($this->tpl_output['css_file'], array('file' => $combinedFile, 'media' => 'screen', 'type' => 'text/css'));
			return $combinedFile;
		} else {
			//Generate it
			$strCSS = "";
			foreach($arrFiles as $key => $strFile){
				$strOrigFile = $arrOrigFiles[$key];
				$strContent = file_get_contents($strFile);
				$strPathDir = pathinfo($strOrigFile, PATHINFO_DIRNAME).'/';
				
				if (strpos($strPathDir, "./") === 0){
					$strPathDir = str_replace($this->root_path, "", $strPathDir);
				}

				$data[] = array('content' => "\r\n/*!\r\n* From File: ".$strFile."\r\n*/ \r\n".$strContent, 'path' => $strPathDir);
			}

			foreach($data as $val){
				$strCSS .= $this->replace_paths_css($val['content'], false, false, $val['path']);
			}

			if(strlen($strCSS)){
				if(!defined('DISABLE_CSS_MINIFY')){
					$minify = new Minify_CSS();
					$strCSS = $minify->minify($strCSS);
				}
				
				$this->pfh->putContent($combinedFile, $strCSS);
				$this->timekeeper->put('tpl_cache_'.$this->style_code, 'combined.css');
				if (!is_array($this->tpl_output['css_file'])) $this->tpl_output['css_file'] = array();
				array_unshift($this->tpl_output['css_file'], array('file' => $combinedFile, 'media' => 'screen', 'type' => 'text/css'));
			}
		}
		return $combinedFile;
	}
	
	public function debug_css_files(){
		$strInlineCSS = "";
		$arrHash = $data = $arrFiles = array();
				
		$storage_folder = $this->pfh->FolderPath('templates', 'eqdkp');
	
		if (is_array($this->tpl_output['css_file'])){
			foreach($this->tpl_output['css_file'] as $key => $val){
				$val['file'] = $this->env->server_to_rootpath($val['file']);

				//Resolve file
				$origFile = $val['file'];
				$val['file'] = $this->resolve_css_file($val['file']);
				if(!$val['file']) continue;
				
				if ($val['media'] == 'screen' && is_file($val['file'])){
					if (strpos('combined_', $val['file']) !== false) continue;
					
					$strFile = $val['file'];
					$strContent = file_get_contents($strFile);
					
					$strPathDir = pathinfo($origFile, PATHINFO_DIRNAME).'/';
					$strFilename = pathinfo($origFile, PATHINFO_FILENAME);
					if (strpos($strPathDir, "./") === 0){
						$strPathDir = str_replace($this->root_path, "", $strPathDir);
					}

					$strContent = $this->replace_paths_css($strContent, false, false, $strPathDir);
					
					$combinedFile = $storage_folder.$this->style_code.'/dev_'.$strFilename.'.css';
					$this->pfh->putContent($combinedFile, "/* ".$strFile."*/ \r\n\n\n".$strContent);
					$this->tpl_output['css_file'][$key]['file'] = $combinedFile;
				}
			}
		}
	}
	
	public function resolve_css_file($cssfile, $stylecode =false){
		if(!$stylecode) $stylecode = $this->style_code;
		
		//Check data dir for exact match
		$strWithoutRoot = str_replace($this->root_path, '', $cssfile);
		$strCleaned = str_replace('templates/base_template/', '', $strWithoutRoot);
		$strCleaned = str_replace('templates/'.$stylecode, '', $strCleaned);
		$data_root = $this->pfh->FolderPath('templates/'.$stylecode, 'eqdkp');

		if(file_exists($data_root.$strWithoutRoot)){
			return $data_root.$strWithoutRoot;
		}elseif(file_exists($data_root.$strCleaned)){
			return $data_root.$strCleaned;
		}elseif(strpos($cssfile, '/base_template/')){
			$strSpecificTemplate = str_replace('/base_template/', '/'.$stylecode.'/', $cssfile);
			if (file_exists($strSpecificTemplate)){
				return $strSpecificTemplate;
			}
		}

		//if it contains base_template, check first specific template folder, then use base_template
		return file_exists($cssfile) ? $cssfile : false;
	}
	
	//Combining JS Files
	public function combine_js(){
		$arrHash = $data = $arrFiles = array();
		$storage_folder = $this->pfh->FolderPath('templates', 'eqdkp');
		
		if (!is_array($this->tpl_output['js_file'])) $this->tpl_output['js_file'] = array();

		foreach($this->tpl_output['js_file'] as $key => $val){
			$val['file'] = $this->env->server_to_rootpath($val['file']);
			
			//Put the jquery lang file at the end of all other JS files
			if (pathinfo($val['file'], PATHINFO_FILENAME) == 'lang_jquery'){
				$nkey = 99999999;
			} else $nkey = $key;
			
			if (is_file($val['file'])){
				if (strpos($val['file'], $storage_folder) === 0 || strpos('combined_', $val['file']) !== false) continue;
				$arrHash[] = md5_file($val['file']);
				unset($this->tpl_output['js_file'][$key]);
				$arrFiles[$nkey] = $val['file'];
			}
		}
		
		ksort($arrFiles);
		
		//Check if there is an file for this hash
		asort($arrHash);
		$strHash = md5(implode(";", $arrHash));
		$combinedFile = $storage_folder.$this->style_code.'/combined_'.$strHash.'.js';
		
		if (is_file($combinedFile)){
			array_unshift($this->tpl_output['js_file'], array('file' => $combinedFile));
			
			return file_get_contents($combinedFile);
		} else {
			//Generate it
			$strJS = "";
			foreach($arrFiles as $strFile){
				$strContent = file_get_contents($strFile);
				$arrHash[] = md5($strContent);
				$strPathDir = pathinfo($strFile, PATHINFO_DIRNAME).'/';
				if (strpos($strPathDir, "./") === 0){
					$strPathDir = "EQDKP_ROOT_PATH".substr($strPathDir, 2);
				}
				$strContent = str_replace(array('(./', '("./', "('./"), array('('.$strPathDir, '("'.$strPathDir, "('".$strPathDir),$strContent);
				$data[] = array('content' => "\r\n/* ".$strFile."*/ \r\n".$strContent, 'path' => $strPathDir);
			}
			
			foreach($data as $val){
				$strJS .= ' '.$val['content'];
			}
			
			$this->pfh->putContent($combinedFile, $strJS);
			$this->timekeeper->put('tpl_cache_'.$this->style_code, 'combined.js');
			array_unshift($this->tpl_output['js_file'], array('file' => $combinedFile));
			return $strJS;
		}
		return "";
	}
	
	public function cleanup_combined(){
		$intCleanUpTime = 24*3600; //24 hours
		$intCSS = $this->timekeeper->get('tpl_cache_'.$this->style_code, 'combined.css');
		$intJS = $this->timekeeper->get('tpl_cache_'.$this->style_code, 'combined.js');
		
		if (($intCSS+$intCleanUpTime) < time() || (($intJS+$intCleanUpTime) < time())) {
			$arrDir = sdir($storage_folder = $this->pfh->FolderPath('templates', 'eqdkp').$this->style_code, 'combined_*');
			foreach($arrDir as $file){
				$this->pfh->Delete('templates/'.$this->style_code.'/'.$file, 'eqdkp');
			}
		}
	}
	

	/**
	* Assign RSS-Feeds to the Header
	* $id = uniqueid of feed, e.g. latest_news
	* $title = e.g. My Guild DKP - Shoutbox
	* $url = when using permissions, just add the filename and store the RSS-Feed in Folder $pfh->FolderPath('rss', 'eqdkp'), otherwise use relative path to the folder of the RSS-Feed
	*/
	public function add_rssfeed($title, $url, $arrPermissions = array(), $type='application/rss+xml'){
		if (is_array($arrPermissions) && count($arrPermissions) > 0){
			$arrData = array(
				'url'	=> $url,
				'perms'	=> $arrPermissions,
			);
			$url = $this->server_path.'exchange.php?out=xml&amp;data='.rawurlencode($this->encrypt->encrypt(serialize($arrData))).'&amp;key='.((isset($this->user->data['exchange_key'])) ? $this->user->data['exchange_key'] : '');
		}

		$this->tpl_output['rss_feeds'][] = array(
			'name'	=> $title,
			'url'	=> $url,
			'type'	=> $type,
		);
	}

	/**
	 * Sets the template filenames for handles. $filename_array
	 * should be a hash of handle => filename pairs.
	 */
	public function set_filenames($filename_array){
		if(is_array($filename_array)){
			$new_files	= array_merge($this->statcontent_file, $filename_array);
			foreach ($new_files as $handle => $filename){
				if (empty($filename)){
					$this->generate_error('templates_error1', $handle, $handle, 'set_filenames()');
				}
				$this->filename[$handle]	= $filename;
				$this->files[$handle]		= $this->fullpath_filename($filename, (($handle == 'main') ? true : false));
			}
			return true;
		}else{
			return false;
		}
	}

	 // Generate a full path & the filename for a given filename (absolute or relative)
	public function fullpath_filename($filename, $basefile=false){

		if(substr($filename, 0, 1) == '/' || substr($filename, 0, 2) == './'){
			$myfile			= $filename;
		}else{
			if ($basefile){
				$data_file = $this->data_root.$filename;
				$tmp_root_file = $this->root_path.'templates/'.$this->style_code.'/'.$filename;
			} else {
				$data_file = $this->data_root.str_replace('templates/', '', $this->root_dir).$filename;
				$tmp_root_file = $this->root.'/'.$filename;
			}
			if (file_exists($data_file)){
				$myfile = $data_file;
			} elseif(file_exists($tmp_root_file)){
				$myfile = $tmp_root_file;
			} else {
				$myfile = $this->base_template.'/'.$filename;
			}
		}
		return $myfile;
	}
	
	public function resolve_templatefile($filename, $stylecode=false){
		if(!$stylecode) $stylecode = $this->style_code;
		
		$data_root = $this->pfh->FolderPath('templates/'.$stylecode, 'eqdkp');
		$data_file = $data_root.$filename;
		$tmp_root_file = $this->root_path.'templates/'.$stylecode.'/'.$filename;
		$base_template = $this->root_path.'templates/base_template';
		$myfile = false;
		
		if (file_exists($data_file)){
			$myfile = $data_file;
		} elseif(file_exists($tmp_root_file)){
			$myfile = $tmp_root_file;
		} elseif(file_exists($myfile)) {
			$myfile = $base_template.'/'.$filename;
		}
		return $myfile;
	}

	/**
	 * If not already done, load the file for the given handle and populate
	 * the uncompiled_code[] hash with its code. Do not compile.
	 */
	private function loadfile($handle){
		// If the file for this handle is already loaded and compiled, do nothing.
		if (!empty($this->uncompiled_code[$handle])){
			return true;
		}
		// If we don't have a file assigned to this handle, die.
		if (!isset($this->files[$handle])){
			$this->generate_error('templates_error1', $handle, $handle, 'loadfile()');
		}
		if(is_file($this->files[$handle])){
			$str = file_get_contents($this->files[$handle]);
		}else{
			$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'loadfile()');
		}
		if($handle == 'main'){
			$str = str_replace('{GBL_CONTENT_BODY}', file_get_contents($this->files['body']), $str);
			if(function_exists('gen_htmlhead_copyright')){
				$str = gen_htmlhead_copyright($str);
			}
		}
		$this->uncompiled_code[$handle] = trim($str);
		return true;
	}

	private function security(){
		return true;
	}
	
	public function get_combined_css(){
		return $this->combine_css();
	}
	
	public function get_header_js(){
		$imploded_jscode = "";
		if(is_array($this->get_templatedata('js_code'))){
			$imploded_jscode = implode("\n", $this->get_templatedata('js_code'));
			if(is_array($this->get_templatedata('js_code_docready'))){
				$imploded_jscode .= "jQuery(document).ready(function(){";
				$imploded_jscode .= implode("\n", $this->get_templatedata('js_code_docready'));
				$imploded_jscode .= "});";
			}
		}
		return $imploded_jscode;
	}

	// Load the JS / CSS / RSS files to the header
	private function perform_header_tasks(){
		$debug = (DEBUG == 4);
		$this->cleanup_combined();
		if(!$this->get_templateout('js_code')){
			// JS in header...
			if(is_array($this->get_templatedata('js_code'))){
				$imploded_jscode = implode("\n", $this->get_templatedata('js_code'));
				if(is_array($this->get_templatedata('js_code_docready'))){
					$imploded_jscode .= "$(document).ready(function(){";
					$imploded_jscode .= implode("\n", $this->get_templatedata('js_code_docready'));
					$imploded_jscode .= "});";
				}
				$this->assign_var('JS_CODE', $imploded_jscode);
				$this->set_templateout('js_code', true);
			}

			// JS on end of page
			if(is_array($this->get_templatedata('js_code_eop'))){
				$imploded_jscodeeop = implode("\n", $this->get_templatedata('js_code_eop'));
				$this->assign_var('JS_CODE_EOP', $imploded_jscodeeop);
				$this->set_templateout('js_code', true);
			}
			// JS on end of page
			if(is_array($this->get_templatedata('js_code_eop2'))){
				$imploded_jscodeeop2 = implode("\n", $this->get_templatedata('js_code_eop2'));
				$this->assign_var('JS_CODE_EOP2', $imploded_jscodeeop2);
				$this->set_templateout('js_code', true);
			}
		}

		//Combine CSS Files and Inline CSS
		if ($debug) {
			$this->debug_css_files(); 
		} else $this->combine_css();
		
		// Load the CSS Files..
		if(!$this->get_templateout('css_file')){
			if(is_array($this->get_templatedata('css_file'))){
				$this->assign_var('CSS_FILES', $this->implode_cssjsfiles("<link rel='stylesheet' href='", " />", "\n", $this->get_templatedata('css_file')));
				$this->set_templateout('css_file', true);
			}
		}
		
		// Pass CSS Code to template..
		if(!$this->get_templateout('css_code') || !$this->get_templateout('css_code_direct')){
			$imploded_css = "";
			if(is_array($this->get_templatedata('css_code'))){
				$imploded_css .= implode("\n", $this->get_templatedata('css_code'));
			}
			if(is_array($this->get_templatedata('css_code_direct'))){
				$imploded_css .= implode("\n", $this->get_templatedata('css_code_direct'));
			}
			if($imploded_css != ""){
				$this->assign_var('CSS_CODE', (($debug || defined('DISABLE_JS_MINIFY')) ? $imploded_css : Minify_CSS::minify($imploded_css)));
			}
			$this->set_templateout('css_code', true);
			$this->set_templateout('css_code_direct', true);
		}

		// Load the JS Files..
		if(!$debug) $this->combine_js();
		if(!$this->get_templateout('js_file')){
			if(is_array($this->get_templatedata('js_file'))){
				$this->assign_var('JS_FILES', $this->implode_cssjsfiles("<script type='text/javascript' src='", "'></script>", "\n", $this->get_templatedata('js_file')));
			}
			$this->set_templateout('js_file', true);
		}

		// Pass RSS-Feeds to template..
		if(!$this->get_templateout('rss_feeds')){
			if(is_array($this->get_templatedata('rss_feeds'))){
				foreach($this->get_templatedata('rss_feeds') as $feed){
					$feeds[] = '<link rel="alternate" type="'.$feed['type'].'" title="'.$feed['name'].'" href="'.$feed['url'].'" />';
				}
			}

			if(isset($feeds) && is_array($feeds)){
				$imploded_feeds = implode("\n", $feeds);
				$this->assign_var('RSS_FEEDS', $imploded_feeds);
				$this->set_templateout('rss_feeds', true);
				
				$this->assign_var('S_GLOBAL_RSSFEEDS', true);
				foreach($this->get_templatedata('rss_feeds') as $feed){
					$this->tpl->assign_block_vars('global_rss_row', array(
						'LINK' => $feed['url'],
						'NAME' => $feed['name'],
					));
				}
			}
		}

		// Static HTML Code
		if(!$this->get_templateout('statichtml')){
			if(is_array($this->get_templatedata('statichtml'))){
				$this->assign_var('STATIC_HTMLCODE', implode("\n", $this->get_templatedata('statichtml')));
			}
			$this->set_templateout('statichtml', true);
		}
		
		// Metatags
		if(!$this->get_templateout('meta')){
			if(is_array($this->get_templatedata('meta'))){
				$this->assign_var('META', implode("\n", $this->get_templatedata('meta')));
			}
			$this->set_templateout('meta', true);
		}
	}

	// implode an array with JS or CSS files and add a timestamp for caching identifier
	private function implode_cssjsfiles($before, $after, $glue, $array, $type=''){
		$output = '';

		foreach($array as $item){
			$relative_file = $this->env->server_to_rootpath($item['file']);
			$filetime	= (substr($item['file'],0,4) == "http") ? rand(1,100000000) : @filemtime($relative_file);
			$type		= (is_array($item) && isset($item['type'])) ? "' type='".$item['type']."'" : '';
			$media		= (is_array($item) && isset($item['media'])) ? " media='".$item['media']."'" : '';
			$file 		= ((is_array($item)) ? $item['file'] : $item);
			$output .= $before . str_replace($this->root_path, $this->server_path, $file) . "?timestamp=".$filetime.$type.$media.$after.$glue;
		}
		return substr($output, 0, -strlen($glue));
	}

	/**
	 * Load the file for the handle, compile the file,
	 * and run the compiled code. This will print out
	 * the results of executing the template.
	 */
	public function display($handle='main'){
		$this->perform_header_tasks();
		$this->body_filename = $this->filename['body'];
		$_str = '';
		if(!$this->compile_load($_str, $handle, true)){
			if(!$this->loadfile($handle)){
				$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'display()');
			}

			// Actually compile the code now.
			$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
			if($this->compiled_code[$handle] == ""){
				$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'display()');
			}else{
				$this->compile_write($handle, $this->compiled_code[$handle]);
				$this->handle = $handle;
				@eval($this->compiled_code[$handle]);
				@flush();
			}
		}
		exit;
	}

	/**
	 * Inserts the uncompiled code for $handle as the
	 * value of $varname in the root-level. This can be used
	 * to effectively include a template in the middle of another
	 * template.
	 * Note that all desired assignments to the variables in $handle should be done
	 * BEFORE calling this function.
	 */
	private function assign_var_from_handle($varname, $handle){
		$_str = '';
		if(!($this->compile_load($_str, $handle, false))){
			if(!$this->loadfile($handle)){
				$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'assign_var_from_handle()');
			}

			$code = $this->compile($this->uncompiled_code[$handle], true, '_str');
			$this->compile_write($handle, $code);

			// evaluate the variable assignment.
			@eval($code);
		}

		// assign the value of the generated variable to the given varname.
		$this->assign_var($varname, $_str);
		return true;
	}

	private function assign_from_include($filename){
		$handle						= 'include_' . $this->include_counter++;
		$this->filename[$handle]	= $filename;
		$this->files[$handle]		= $this->fullpath_filename($filename);
		$_str						= '';

		if(!($this->compile_load($_str, $handle, false))){
			if(!$this->loadfile($handle)){
				$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'assign_from_include()');
			}

			$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
			if($this->compiled_code[$handle] == ""){
				$this->generate_error('templates_error2', $handle, $this->filename[$handle], 'assign_from_include()');
			}else{
				$this->compile_write($handle, $this->compiled_code[$handle]);
				eval($this->compiled_code[$handle]);
			}
		}
	}

	// Multilevel variable assignment. Adds to current assignments, overriding
	// any existing variable assignment with the same name.
	public function assign_array($name, $array, $morekeys='') {
		foreach($array as $key => $val){
			if(is_array($val)){
				$keys = (!empty($morekeys)) ? $morekeys.':'.$key : ':'.$key;
				$this->assign_array($name, $val, $keys);
				continue;
			}else{
				$keys = ':'.$key;
			}
			if((!empty($morekeys))){
				$this->_data['.'][0][$name.$morekeys.$keys] = $val;
			}else{
				$this->_data['.'][0][$name.$keys] = $val;
			}
		}
	}

	// Root-level variable assignment. Adds to current assignments, overriding
	// any existing variable assignment with the same name.
	public function assign_vars($vararray){
		foreach ($vararray as $key => $val){
			$this->_data['.'][0][$key] = $val;
		}
		return true;
	}

	// Root-level variable assignment. Adds to current assignments, overriding
	// any existing variable assignment with the same name.
	public function assign_var($varname, $varval){
		$this->_data['.'][0][$varname] = $varval;
		return true;
	}

	// Block-level variable assignment. Adds a new block iteration with the given
	// variable assignments. Note that this should only be called once per block iteration.
	public function assign_block_vars($blockname, $vararray){
		if(strpos($blockname, '.') !== false){
			// Nested block.
			$blocks			= explode('.', $blockname);
			$blockcount		= sizeof($blocks) - 1;
			$str				= &$this->_data;
			for($i = 0; $i < $blockcount; $i++){
				$str			= &$str[$blocks[$i] . '.'];
				$str			= &$str[sizeof($str) - 1];
			}

			// Use an additional reference to keep following code a bit
			// more compact (and less error prone because of the extra '.'
			$existing					= &$str[$blocks[$blockcount] . '.'];
			$s_row_count				= isset($existing) ? sizeof($existing) : 0;
			$vararray['S_ROW_COUNT']	= $s_row_count;

			// Assign S_FIRST_ROW
			if(!$s_row_count){
				$vararray['S_FIRST_ROW']	= true;
			}

			// Now the tricky part, we always assign S_LAST_ROW and alter the entry before
			// This is much more clever than going through the complete template data on display (phew)
			$vararray['S_LAST_ROW'] = true;
			if($s_row_count > 0){
				unset($existing[($s_row_count - 1)]['S_LAST_ROW']);
			}
			// Now we add the block that we're actually assigning to.
			// We're adding a new iteration to this block with the given
			// variable assignments.
			$existing[] = $vararray;
		}else{
			// Top-level block.
			$s_row_count = (isset($this->_data[$blockname . '.'])) ? sizeof($this->_data[$blockname . '.']) : 0;
			$vararray['S_ROW_COUNT'] = $s_row_count;

			// Assign S_FIRST_ROW
			if(!$s_row_count){
				$vararray['S_FIRST_ROW'] = true;
			}

			// We always assign S_LAST_ROW and remove the entry before
			$vararray['S_LAST_ROW'] = true;
			if($s_row_count > 0){
				unset($this->_data[$blockname . '.'][($s_row_count - 1)]['S_LAST_ROW']);
			}

			// Add a new iteration to this block with the variable assignments we were given.
			$this->_data[$blockname . '.'][] = $vararray;
		}
		return true;
	}
	
	public function compileString($strString, $arrVars=array()){
		$this->core->addCommonTemplateVars();
		$this->assign_vars($arrVars);
		$strCompiled = $this->compile($strString, true, 'return');
		@eval($strCompiled);

		return $return;
	}

	//Compiles the given string of code, and returns the result in a string.
	private function compile($code, $do_not_echo = false, $retvar = 'return'){
		$this->strip_tags_php($code);

		// match the template tags
		preg_match_all('#<!-- (.*?) (.*?)?[ ]?-->#s', $code, $blocks);
		$text_blocks = preg_split('#<!-- (.*?) (.*?)?[ ]?-->#s', $code);
		for($i = 0; $i < count($text_blocks); $i++){
			$this->compile_var_tags($text_blocks[$i]);
		}

		$compile_blocks = array();
		for ($curr_tb = 0; $curr_tb < count($text_blocks); $curr_tb++){
			if ( isset($blocks[1][$curr_tb]) ){
				switch ($blocks[1][$curr_tb]){
					case 'BEGIN':
						$this->block_else_level[] = false;
						$compile_blocks[] = '// BEGIN ' . $blocks[2][$curr_tb] . "\n" . $this->compile_tag_block($blocks[2][$curr_tb]);
						break;
					case 'BEGINELSE':
						$this->block_else_level[sizeof($this->block_else_level) - 1] = true;
						$compile_blocks[] = "// BEGINELSE\n}} else {\n";
						break;
					case 'END':
						$compile_blocks[] = ((array_pop($this->block_else_level)) ? "}\n" : "}}\n") . '// END ' . array_pop($this->block_names) . "\n";
						break;
					case 'IF':
						$compile_blocks[] = '// IF ' . $blocks[2][$curr_tb] . "\n" . $this->compile_tag_if($blocks[2][$curr_tb], false);
						break;
					case 'ELSE':
						$compile_blocks[] = "// ELSE\n} else {\n";
						break;
					case 'ELSEIF':
						$compile_blocks[] = '// ELSEIF ' . $blocks[2][$curr_tb] . "\n" . $this->compile_tag_if($blocks[2][$curr_tb], true);
						break;
					case 'ENDIF':
						$compile_blocks[] = "// ENDIF\n}\n";
						break;
					case 'INCLUDE':
						$compile_blocks[] = '// INCLUDE ' . $blocks[2][$curr_tb] . "\n" . $this->compile_tag_include($blocks[2][$curr_tb]);
						break;
					case 'PRE':
						$precompiled = $this->pre_compile($blocks[2][$curr_tb]);
						$compile_blocks[] = $precompiled;
						break;
					default:
						$this->compile_var_tags($blocks[0][$curr_tb]);
						$trim_check = trim($blocks[0][$curr_tb]);
						$compile_blocks[] = ((!empty($trim_check)) ? 'echo \'' . $blocks[0][$curr_tb] . '\';' : '');
						break;
				}	// switch
			}	// isset
		}	// for

		$template_php = '';
		for ($i = 0; $i < count($text_blocks); $i++){
			$trim_check_text		= ( isset($text_blocks[$i]) ) ? trim($text_blocks[$i]) : '';
			$trim_check_block		= ( isset($compile_blocks[$i]) ) ? trim($compile_blocks[$i]) : '';
			$template_php			.= ((!empty($trim_check_text)) ? 'echo \'' . $text_blocks[$i] . '\';' : '') . ((!empty($compile_blocks[$i])) ? $compile_blocks[$i] : '') ;
		}
		
		if($do_not_echo){
			$template_php = '$' . $retvar . ' = "";'."\n".$template_php;
			$template_php = str_replace("echo '", '$' . $retvar . ' .= \'', $template_php);
			return $template_php;
		}
		
		return $template_php;
	}

	private function compile_var_tags(&$text_blocks){
		// change template varrefs into PHP varrefs
		$varrefs		= array();
		$text_blocks	= str_replace('\\', '\\\\', $text_blocks);
		$text_blocks	= str_replace('\'', '\\\'', $text_blocks);

		// This one will handle varrefs WITH namespaces
		preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $text_blocks, $varrefs);
		for ($j = 0; $j < sizeof($varrefs[1]); $j++){
			$namespace		= $varrefs[1][$j];
			$varname		= $varrefs[3][$j];
			$new			= $this->generate_block_varref($namespace, $varname);
			$text_blocks	= str_replace($varrefs[0][$j], $new, $text_blocks);
		}

		// This will handle the remaining root-level varrefs
		//Check modifier on language Vars
		$text_blocks = preg_replace("/\{(L|GL)_([a-z0-9\-_]*?)\|([a-z0-9\-_]+?)\}/is", "'.\$this->handleModifier('{"."$1_$2"."}', '$3').'", $text_blocks);
		
		
		//normal language
		$text_blocks	= preg_replace('#\{L_([a-z0-9\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['L_\\1'])) ? \$this->_data['.'][0]['L_\\1'] : ((\$this->lang('\\1')) ? \$this->lang('\\1') : '{ ' . ucfirst(strtolower(str_replace('_', ' ', '\\1'))) . '         }')) . '", $text_blocks);
		//game language
		if(strpos($text_blocks, '{GL_') !== false) $text_blocks = preg_replace('#\{GL_([a-z0-9\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['L_\\1'])) ? \$this->_data['.'][0]['L_\\1'] : ((\$this->glang('\\1', false, true)) ? \$this->glang('\\1') : '{ ' . ucfirst(strtolower(str_replace('_', ' ', '\\1'))) . '         }')) . '", $text_blocks);
		$text_blocks	= preg_replace('#\{([a-z0-9\:\@\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['\\1'])) ? \$this->_data['.'][0]['\\1'] : '') . '", $text_blocks);
		return;
	}
	
	public function handleModifier($strLangString, $strModifier){
		switch($strModifier){
			case 'jsencode':
					return "'".str_replace("'", "\'", $strLangString)."'";
				break;
			
			default: return $strLangString;
		}
	}
	
	
	private function pre_compile($tag_args){
		$var = $this->_data['.'][0][$tag_args];
		if ($var) return $this->compile($var);
		return '';
	}

	private function compile_tag_block($tag_args){
		$tag_template_php = '';
		array_push($this->block_names, $tag_args);
		
		// Allow for control of looping (indexes start from zero):
		// foo(2)    : Will start the loop on the 3rd entry
		// foo(-2)   : Will start the loop two entries from the end
		// foo(3,4)  : Will start the loop on the fourth entry and end it on the fifth
		// foo(3,-4) : Will start the loop on the fourth entry and end it four from last
		if (preg_match('#^([^()]*)\(([\-\d]+)(?:,([\-\d]+))?\)$#', $tag_args, $match))
		{
			$tag_args = $match[1];
			if ($match[2] < 0)
			{
				$loop_start = '($_' . $tag_args . '_count ' . $match[2] . ' < 0 ? 0 : $_' . $tag_args . '_count ' . $match[2] . ')';
			}
			else
			{
				$loop_start = '($_' . $tag_args . '_count < ' . $match[2] . ' ? $_' . $tag_args . '_count : ' . $match[2] . ')';
			}
			if (strlen($match[3]) < 1 || $match[3] == -1)
			{
				$loop_end = '$_' . $tag_args . '_count';
			}
			else if ($match[3] >= 0)
			{
				$loop_end = '(' . ($match[3] + 1) . ' > $_' . $tag_args . '_count ? $_' . $tag_args . '_count : ' . ($match[3] + 1) . ')';
			}
			else //if ($match[3] < -1)
			{
				$loop_end = '$_' . $tag_args . '_count' . ($match[3] + 1);
			}
		}
		else
		{
			$loop_start = 0;
			$loop_end = '$_' . $tag_args . '_count';
		}

		if (sizeof($this->block_names) < 2){
			// Block is not nested.
			$tag_template_php	= '$_' . $tag_args . '_count = (isset($this->_data[\'' . $tag_args . '.\'])) ?  sizeof($this->_data[\'' . $tag_args . '.\']) : 0;' . "\n";
			$tag_template_php	.= 'if ($_' . $tag_args . '_count) {' . "\n";
			$tag_template_php	.= 'for ($_' . $tag_args . '_i = '.$loop_start.'; $_' . $tag_args . '_i < '.$loop_end.'; $_' . $tag_args . '_i++)';

		// This block is nested.
		}else{
			// Generate a namespace string for this block.
			$namespace			= implode('.', $this->block_names);

			// Get a reference to the data array for this block that depends on the
			// current indices of all parent blocks.
			$varref				= $this->generate_block_data_ref($namespace, false);

			// Create the for loop code to iterate over this block.
			$tag_template_php	= '$_' . $tag_args . '_count = (isset(' . $varref . ')) ? sizeof(' . $varref . ') : 0;' . "\n";
			$tag_template_php	.= 'if ($_' . $tag_args . '_count) {' . "\n";
			$tag_template_php	.= 'for ($_' . $tag_args . '_i = '.$loop_start.'; $_' . $tag_args . '_i < ' . $loop_end . '; $_' . $tag_args . '_i++)';
		}
		$tag_template_php		.= "\n{\n";
		return $tag_template_php;
	}

	// compile the IF tags
	private function compile_tag_if($tag_args, $elseif){
		/* Tokenize args for 'if' tag. */
		preg_match_all('/(?:
			"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"		|
			\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'	|
			[(),]								|
			[^\s(),]+)/x', $tag_args, $match
		);

		$tokens = $match[0];
		$is_arg_stack = array();
		for($i = 0, $size = count($tokens); $i < $size; $i++){
			$token = &$tokens[$i];
			switch ($token){
				case '!==':
				case '===':
				case '<<':
				case '>>':
				case '|':
				case '^':
				case '&':
				case '~':
				case ')':
				case ',':
				case '+':
				case '-':
				case '*':
				case '/':
				case '@':
				break;

				case '==':
				case 'eq':
				case 'EQ':
					$token = '==';
				break;

				case '!=':
				case '<>':
				case 'ne':
				case 'neq':
				case 'NEQ':
					$token = '!=';
				break;

				case '<':
				case 'lt':
				case 'LT':
					$token = '<';
				break;

				case '<=':
				case 'le':
				case 'lte':
				case 'LE':
					$token = '<=';
				break;

				case '>':
				case 'gt':
				case 'GET':
					$token = '>';
				break;

				case '>=':
				case 'ge':
				case 'gte':
					$token = '>=';
				break;

				case '&&':
				case 'and':
				case 'AND':
					$token = '&&';
				break;

				case '||':
				case 'or':
				case 'OR':
					$token = '||';
				break;

				case '!':
				case 'not':
				case 'NOT':
					$token = '!';
				break;

				case '%':
				case 'mod':
				case 'MOD':
					$token = '%';
				break;

				case '(':
					array_push($is_arg_stack, $i);
				break;

				case 'IS':
				case 'is':	$is_arg_start	= ($tokens[$i-1] == ')') ? array_pop($is_arg_stack) : $i-1;
							$is_arg			= implode('    ', array_slice($tokens, $is_arg_start, $i - $is_arg_start));
							$new_tokens		= $this->_parse_is_expr($is_arg, array_slice($tokens, $i+1));
							array_splice($tokens, $is_arg_start, count($tokens), $new_tokens);
							$i				= $is_arg_start;
							// no break

				default:	if (preg_match('#^(([a-z0-9\-_]+?\.)+?)?([A-Z]+[A-Z0-9\-_]+?)$#s', $token, $varrefs)){
								$token = (!empty($varrefs[1])) ? $this->generate_block_data_ref(substr($varrefs[1], 0, -1), true) . '[\'' . $varrefs[3] . '\']' : '$this->_data[\'.\'][0][\'' . $varrefs[3] . '\']';
							}
							break;
			}	// end switch
		}	// end for

		// If there are no valid tokens left or only control/compare characters left, we do skip this statement
		if (!sizeof($tokens) || str_replace(array(' ', '=', '!', '<', '>', '&', '|', '%', '(', ')'), '', implode('', $tokens)) == ''){
			$tokens = array('false');
		}
		return (($elseif) ? '} else if (' : 'if (') . (implode(' ', $tokens) . ') { ' . "\n");
	}

	// compile the include tag
	private function compile_tag_include($tag_args){
		preg_match_all('#\{([A-Z0-9\-_]+)\}#is', $tag_args, $mymatch);
		if(count($mymatch) > 0){
			foreach($mymatch as $var_val){
				if(is_array($var_val) && count($var_val) > 0){
					$tag_args	= str_replace(@$var_val[0], $this->_data['.'][0][@$mymatch[1][0]], $tag_args);
				}
			}
		}
		return "\$this->assign_from_include('$tag_args');\n";
	}

	// This is from Smarty
	private function _parse_is_expr($is_arg, $tokens){
		$expr_end			=	0;
		$negate_expr		= false;

		if(($first_token = array_shift($tokens)) == 'not'){
			$negate_expr	= true;
			$expr_type		= array_shift($tokens);
		}else{
			$expr_type		= $first_token;
		}

		switch ($expr_type){
			case 'even':
				if(@$tokens[$expr_end] == 'by'){
					$expr_end++;
					$expr_arg	=	$tokens[$expr_end++];
					$expr		=	"!(($is_arg	/ $expr_arg) % $expr_arg)";
				}else{
					$expr		=	"!($is_arg % 2)";
				}
				break;

			case 'odd':
				if (@$tokens[$expr_end] == 'by'){
					$expr_end++;
					$expr_arg	=	$tokens[$expr_end++];
					$expr		=	"(($is_arg / $expr_arg)	% $expr_arg)";
				}else{
					$expr		=	"($is_arg %	2)";
				}
				break;

			case 'div':
				if (@$tokens[$expr_end] == 'by'){
					$expr_end++;
					$expr_arg	=	$tokens[$expr_end++];
					$expr		=	"!($is_arg % $expr_arg)";
				}
				break;

			default:
				break;
		}

		if ($negate_expr){
			$expr				=	"!($expr)";
		}
		array_splice($tokens, 0, $expr_end,	$expr);
		return $tokens;
	}

	/**
	 * Generates a reference to the given variable inside the given (possibly nested)
	 * block namespace. This is a string of the form:
	 * ' . $this->_data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['varname'] . '
	 * It's ready to be inserted into an "echo" line in one of the templates.
	 * NOTE: expects a trailing "." on the namespace.
	 */
	private function generate_block_varref($namespace, $varname){
		$namespace = substr($namespace, 0, -1);							// Strip the trailing period.
		$varref = $this->generate_block_data_ref($namespace, true);		// Get a reference to the data block for this namespace.
		$varref .= '[\'' . $varname . '\']';							// Append the variable reference.
		return '\' . ((isset(' . $varref . ')) ? ' . $varref . ' : \'\') . \'';
	}

	/**
	 * Generates a reference to the array of data values for the given
	 * (possibly nested) block namespace. This is a string of the form:
	 * $this->_data['parent'][$_parent_i]['$child1'][$_child1_i]['$child2'][$_child2_i]...['$childN']
	 *
	 * If $include_last_iterator is true, then [$_childN_i] will be appended to the form shown above.
	 * NOTE: does not expect a trailing "." on the blockname.
	 */
	private function generate_block_data_ref($blockname, $include_last_iterator){
		// Get an array of the blocks involved.
		$blocks			= explode('.', $blockname);
		$blockcount		= sizeof($blocks) - 1;
		$varref			= '$this->_data';

		// Build up the string with everything but the last child.
		for($i = 0; $i < $blockcount; $i++){
			$varref		.= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
		}

		// Add the block reference for the last child.
		$varref			.= '[\'' . $blocks[$blockcount] . '.\']';

		// Add the iterator for the last child if requried.
		if($include_last_iterator){
			$varref		.= '[$_' . $blocks[$blockcount] . '_i]';
		}
		return $varref;
	}

	private function compile_load(&$_str, &$handle, $do_echo){
		$filename	= ($handle == 'main') ? $this->body_filename : $this->files[$handle];
		$file = $this->cachedir . $filename . '.php';

		// Recompile page if the original template is newer, otherwise load the compiled version
		if($this->caching && file_exists($file) && $this->timekeeper->get('tpl_cache_'.$this->template, $filename) >= @filemtime($this->files['body'])){
			$_str	= '';
			include($file);
			if($do_echo && $_str != ''){
				echo $_str;
			}
			return true;
		}

		return false;
	}

	private function compile_write(&$handle, $data){
		$handle_filename	= ($handle == 'main') ? $this->body_filename : $this->files[$handle];
		if($m = preg_match("/^\.{1,2}\/(\.{1,2}\/)*/", $handle_filename)){
			$handle_filename_array	= explode("/", $handle_filename);
			$handle_filename		= $handle_filename_array[ count($handle_filename_array) - 1 ];
		}
		$filename		= $this->cachedir . $handle_filename . '.php';
		$data			= '<?php' . "\nif (\$this->security()) {\n" . $data . "\n}\n?".">";

		// save the file data
		$this->pfh->putContent($filename, $data);
		$this->timekeeper->put('tpl_cache_'.$this->template, $handle_filename);
		return;
	}

	public function delete_cache($template = ''){
		$this->pfh->Delete($this->pfh->FolderPath('template/'.$template,'cache'));
		if (strlen($template)){
			$this->timekeeper->del('tpl_cache_'.$template);
		} else {
			$arrStyles = $this->pdh->aget('styles', 'templatepath', 0, array($this->pdh->get('styles', 'id_list')));
			foreach($arrStyles as $value){
				$this->timekeeper->del('tpl_cache_'.$value);
			}
		}

	}
	
	
	public function add_common_cssfiles(){
		//Global CSS
		$global_css		= $this->root_path.'templates/eqdkpplus.css';
		$this->tpl->css_file($global_css, 'screen');
		
		//Font Awesome
		$this->tpl->css_file($this->root_path.'libraries/FontAwesome/font-awesome.min.css', 'screen');
		
		//Template CSS
		$css_theme		= $this->root_path.'templates/'.$this->style_code.'/'.$this->style_code.'.css';
		$this->tpl->css_file($css_theme, 'screen');
		
		//Now the class colors
		$gameclasses = $this->game->get_primary_classes();
		$data = "";
		if(isset($gameclasses) && is_array($gameclasses)){
			foreach($gameclasses as $class_id => $class_name) {
				$data .= '
						.class_'.$class_id.', .class_'.$class_id.':link, .class_'.$class_id.':visited, .class_'.$class_id.':active,
						.class_'.$class_id.':link:hover, td.class_'.$class_id.' a:hover, td.class_'.$class_id.' a:active,
						td.class_'.$class_id.', td.class_'.$class_id.' a:link, td.class_'.$class_id.' a:visited{
							text-decoration: none;
							color: '.$this->game->get_class_color($class_id).' !important;
						}
					';
			}
			$this->add_css($data);
		}
	}
	
	
	private function replace_paths_css($strCSS, $stylepath = false, $data = false, $path=false){
		$style = ($data) ? $data : $this->user->style;
		$stylepath = ($stylepath) ? $stylepath : $this->style_code;
		$root_path = '../../../../../';

		//Background Image
		$template_background_file = "";
		switch($style['background_type']){
			//Game
			case 1: $template_background_file = $root_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;
			break;
				
			//Own
			case 2:
				if ($style['background_img'] != ''){
					if (strpos($style['background_img'],'://') > 1){
						$template_background_file = $style['background_img'];
					} else {
						$template_background_file = $root_path.$style['background_img'];
					}
				}
				break;
		
			//Style
			default:
				if(is_file($this->root_path . 'templates/' . $style['template_path'] . '/images/template_background.png')){
					$template_background_file	= $root_path . 'templates/' . $style['template_path'] . '/images/template_background.png';
				} else {
					$template_background_file	= $root_path . 'templates/' . $style['template_path'] . '/images/template_background.jpg';
				}
		}
		if($template_background_file == ""){
			//Cannot find a background file, let's take the game specific
			$template_background_file = $root_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;
		}
		
		$in = array(
				"T_FONTFACE1",
				"T_FONTFACE2",
				"T_FONTFACE3",
				"T_FONTSIZE1",
				"T_FONTSIZE2",
				"T_FONTSIZE3",
				"T_FONTCOLOR1",
				"T_FONTCOLOR2",
				"T_FONTCOLOR3",
				"T_FONTCOLOR_NEG",
				"T_FONTCOLOR_POS",
				"T_BODY_BACKGROUND",
				"T_TABLE_BORDER_WIDTH",
				"T_TABLE_BORDER_COLOR",
				"T_TABLE_BORDER_STYLE",
				"T_BODY_LINK_STYLE",
				"T_BODY_LINK",
				"T_BODY_HLINK_STYLE",
				"T_BODY_HLINK",
				"T_HEADER_LINK_STYLE",
				"T_HEADER_LINK",
				"T_HEADER_HLINK_STYLE",
				"T_HEADER_HLINK",
		
				"T_TH_COLOR1",
				"T_TR_COLOR1",
				"T_TR_COLOR2",
				"T_INPUT_BACKGROUND",
				"T_INPUT_BORDER_WIDTH",
				"T_INPUT_BORDER_COLOR",
				"T_INPUT_BORDER_STYLE",
				"T_PORTAL_WIDTH_WITHOUT_BOTH_COLUMNS",
				"T_PORTAL_WIDTH_WITHOUT_LEFT_COLUMN",
				"T_PORTAL_WIDTH",
				"T_COLUMN_LEFT_WIDTH",
				"T_COLUMN_RIGHT_WIDTH",
				"T_BACKGROUND_POSITION",
		
				"EQDKP_ROOT_PATH",
				"EQDKP_IMAGE_PATH",
				"TEMPLATE_IMAGE_PATH",
				"TEMPLATE_BACKGROUND",
		);
		
		$out = array(
				$style['fontface1'],
				$style['fontface2'],
				$style['fontface3'],
				$style['fontsize1'],
				$style['fontsize2'],
				$style['fontsize3'],
				$style['fontcolor1'],
				$style['fontcolor2'],
				$style['fontcolor3'],
				$style['fontcolor_neg'],
				$style['fontcolor_pos'],
				$style['body_background'],
				$style['table_border_width'],
				$style['table_border_color'],
				$style['table_border_style'],
				$style['body_link_style'],
				$style['body_link'],
				$style['body_hlink_style'],
				$style['body_hlink'],
				$style['header_link_style'],
				$style['header_link'],
				$style['header_hlink_style'],
				$style['header_hlink'],
				$style['th_color1'],
				$style['tr_color1'],
				$style['tr_color2'],
				$style['input_color'],
				$style['input_border_width'],
				$style['input_border_color'],
				$style['input_border_style'],
				(intval($style['portal_width']) - intval($style['column_left_width']) - intval($style['column_right_width'])).((strpos($style['portal_width'], '%') !== false) ? '%' : 'px'),
				(intval($style['portal_width']) - intval($style['column_left_width'])).((strpos($style['portal_width'], '%') !== false) ? '%' : 'px'),
				$style['portal_width'],
				$style['column_left_width'],
				$style['column_right_width'],
				(($style['background_pos'] == 'normal') ? 'scroll' : 'fixed'),
		
				$root_path,
				$root_path.'images/',
				$root_path.'templates/'.$stylepath.'/images',
				$template_background_file,
		);
		
		$data = str_replace($in, $out, $strCSS);
		
		/**
		 * Contao Open Source CMS
		 * Copyright (c) 2005-2014 Leo Feyer
		 * @link    https://contao.org
		 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
		 */
		
		$content = $data;
		$strDirname = $path;
		$strGlue = ($strDirname != '.') ? $strDirname  : '';
		
		$strBuffer = '';
		$chunks = preg_split('/url\(["\']??(.+)["\']??\)/U', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		// Check the URLs
		for ($i=0, $c=count($chunks); $i<$c; $i=$i+2)
		{
			$strBuffer .= $chunks[$i];
		
			if (!isset($chunks[$i+1]))
			{
				break;
			}
		
			$strData = $chunks[$i+1];
		
			// Skip absolute links and embedded images (see #5082)
			if (strncmp($strData, 'data:', 5) !== 0 && strncmp($strData, 'http://', 7) !== 0 && strncmp($strData, 'https://', 8) !== 0 && strncmp($strData, '/', 1) !== 0)
			{
				// Make the paths relative to the root (see #4161)
				if (strncmp($strData, '../', 3) !== 0)
				{
					$strData = $root_path . $strGlue . $strData;
				}
				else
				{
					$dir = $strDirname;
		
					// Remove relative paths
					while (strncmp($strData, '../', 3) === 0)
					{
						$dir = dirname($dir);
						$strData = substr($strData, 3);
					}
		
					$glue = ($dir != '.') ? $dir . '/' : '';
					$strData = $root_path . $glue . $strData;
				}
				
				$strData = str_replace("//", "/", $strData);
			}
			
			$strBuffer .= 'url("' . $strData . '")';
		}
		$data = $strBuffer;
		return $data;
	}

	public function generate_error($content, $handle = false, $sprintf = '', $function = ''){
		if ($handle === false) $handle = $this->handle;
		
		$this->intErrorCount++;
		if($this->intErrorCount > 3){
			throw new Exception("Infinite Template Error. Generating Error aborted.");
		}
		
		if(!$this->error_message){		
			// fix for upgrade from 1.0 to 2.0 with deleted old template folder. This fix redirects directly to the maintenance mode
			if($this->files[$handle] && strpos($this->files[$handle], '/templates/base_template/index.tpl') !== false && $function == 'loadfile()' && ($this->config->get('plus_version') === false || version_compare('2.0', $this->config->get('plus_version')) > 0)){			
				redirect('maintenance/index.php', false, false, false);
			}

			$title			= $this->lang('templates_error');
			$content		= (!$this->lang($content)) ? $content : $this->lang($content);

			if ($sprintf !=""){
				$content	= sprintf($content, $sprintf);
			}

			$message		 = '<h2>'.$this->lang('templates_error_desc').':</h2>';
			$message		.= $content;
			$message		.= '<br /><h2>'.$this->lang('templates_error_more').'</h2>';
			$message		.= '<b>File:</b> '.$this->filename[$handle].'<br />';
			if($handle != 'body') $message		.= '<b>Body-File:</b> '.$this->files['body'].'<br />';
			$message		.= '<b>Path:</b> '.$this->files[$handle].'<br />';
			$message		.= ($function != "") ? '<b>Function:</b> '.$function.'<br />' : '';
			$message		.= '<b>Style-Code:</b> '.$this->style_code.'<br />';
			$message		.= '<b>Template:</b> '.$this->template.'<br />';
			$message		.= '<b>Handler:</b> '.$handle.'<br />';
			$this->display_error($title, $message);
			$this->error_message	= true;
		}
		
	}
	
	public function get_error_details(){
		$handle = $this->handle;
		$message = "";
		if(!$this->error_message){
			$message		.= 'File: '.$this->filename[$handle]."\n";
			if($handle != 'body') $message		.= 'Body-File: '.$this->files['body']."\n";
			$message		.= 'Path: '.$this->files[$handle]."\n";
			$message		.= ($function != "") ? 'Function: '.$function."\n" : '';
			$message		.= 'Style-Code: '.$this->style_code."\n";
			$message		.= 'Template: '.$this->template."\n";
			$message		.= 'Handler: '.$handle."\n";
		}
		return $message;
	}

	private function display_error($title, $message) {
		$this->set_template('maintenance', 'maintenance');
		$this->set_filenames(array(
			'body' => 'template_error.html')
		);

		$this->assign_vars(array(
			'ERROR_TITLE'		=> $title,
			'ERROR_MESSAGE'		=> $message,
			'TYEAR'				=> date('Y'),
		));
		$this->display('body');
		exit;
	}

	// strip all php tags
	private function strip_tags_php(&$code){
		$code = preg_replace(array("#<([\?%])=?.*?\1>#s", "#<\?php(?:\r\n?|[ \n\t]).*?\?>#s"), '', $code);
	}

	public function __destruct() {
		$this->_data = array();
		$this->states = array();
		parent::__destruct();
	}
}
?>
