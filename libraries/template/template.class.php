<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2001
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 *
 * This class was initally written for the phpbb. Most code of this file does not match
 * the old code any longer, as it was rewritten for the needs of eqdkp-plus. Because of that
 * it is used under the GPL as part of the libraries of eqdkp plus.
 * Parts of this class may contain code of the smarty project
 *
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class template extends gen_class {
	public static $shortcuts = array('pfh', 'user', 'game', 'pdh', 'config',
		'encrypt' 		=> 'encrypt',
		'timekeeper' 	=> 'timekeeper',
	);

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

	// Various counters and storage arrays
	protected $block_names			= array();
	protected $block_else_level		= array();
	protected $include_counter		= 1;
	protected $block_nesting_level	= 0;
	protected $tplout_set				= array(
										'js_code'		=> false,
										'js_file'		=> false,
										'css_code'		=> false,
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
		require_once($this->root_path . 'libraries/_statics/CSS.php');
		require_once($this->root_path . 'libraries/_statics/JSMin.php');
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
	public function js_file($varval){
		$this->tpl_output['js_file'][] = array('file' => $varval);
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
	public function css_file($varval, $media='screen'){
		$this->tpl_output['css_file'][]			= array('file'=>$varval, 'type'=>'text/css', 'media'=>$media);
	}

	/**
	* Assign custom CSS Code to the Header
	*/
	public function add_css($varval){
		$this->tpl_output['css_code'][] = $varval;
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
			$url = $this->root_path.'exchange.php?out=xml&amp;data='.$this->encrypt->encrypt(serialize($arrData)).'&amp;key='.((isset($this->user->data['exchange_key'])) ? $this->user->data['exchange_key'] : '');
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
	private function fullpath_filename($filename, $basefile=false){

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

	// Load the JS / CSS / RSS files to the header
	private function perform_header_tasks(){
		$debug = true;
		if(!$this->get_templateout('js_code')){
			// JS in header...
			if(is_array($this->get_templatedata('js_code'))){
				$imploded_jscode = implode("\n", $this->get_templatedata('js_code'));
				if(is_array($this->get_templatedata('js_code_docready'))){
					$imploded_jscode .= "$(document).ready(function(){";
					$imploded_jscode .= implode("\n", $this->get_templatedata('js_code_docready'));
					$imploded_jscode .= "});";
				}
				$this->assign_var('JS_CODE', (($debug) ? $imploded_jscode : JSMin::minify($imploded_jscode)));
				$this->set_templateout('js_code', true);
			}

			// JS on end of page
			if(is_array($this->get_templatedata('js_code_eop'))){
				$imploded_jscodeeop = implode("\n", $this->get_templatedata('js_code_eop'));
				$this->assign_var('JS_CODE_EOP', (($debug) ? $imploded_jscodeeop : JSMin::minify($imploded_jscodeeop)));
				$this->set_templateout('js_code', true);
			}
			// JS on end of page
			if(is_array($this->get_templatedata('js_code_eop2'))){
				$imploded_jscodeeop2 = implode("\n", $this->get_templatedata('js_code_eop2'));
				$this->assign_var('JS_CODE_EOP2', (($debug) ? $imploded_jscodeeop2 : JSMin::minify($imploded_jscodeeop2)));
				$this->set_templateout('js_code', true);
			}
		}

		// Pass CSS Code to template..
		if(!$this->get_templateout('css_code')){
			if(is_array($this->get_templatedata('css_code'))){
				$imploded_css = implode("\n", $this->get_templatedata('css_code'));
				$this->assign_var('CSS_CODE', (($debug) ? $imploded_css : Minify_CSS::minify($imploded_css)));
				$this->set_templateout('css_code', true);
			}
		}

		// Load the CSS Files..
		if(!$this->get_templateout('css_file')){
			if(is_array($this->get_templatedata('css_file'))){
				$this->assign_var('CSS_FILES', $this->implode_cssjsfiles("<link rel='stylesheet' href='", " />", "\n", $this->get_templatedata('css_file')));
				$this->set_templateout('css_file', true);
			}
		}

		// Load the JS Files..
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
			$filetime	= (substr($item['file'],0,4) == "http") ? rand(1,100000000) : @filemtime($item['file']);
			$type		= (is_array($item) && isset($item['type'])) ? "' type='".$item['type']."'" : '';
			$media		= (is_array($item) && isset($item['media'])) ? " media='".$item['media']."'" : '';
			$output .= $before . ((is_array($item)) ? $item['file'] : $item) . "?timestamp=".$filetime.$type.$media.$after.$glue;
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
				@eval($this->compiled_code[$handle]);
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
			$str			= '$this->_data';
			for($i = 0; $i < $blockcount; $i++){
				$str		.= '[\'' . $blocks[$i] . '.\']';
				eval('$lastiteration = sizeof(' . $str . ') - 1;');
				$str		.= '[' . $lastiteration . ']';
			}

			// Now we add the block that we're actually assigning to.
			// We're adding a new iteration to this block with the given
			// variable assignments.
			$str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';

			// Now we evaluate this assignment we've built up.
			$str						= eval($str);
			$s_row_count				= isset($str[$blocks[$blockcount]]) ? sizeof($str[$blocks[$blockcount]]) : 0;
			$vararray['S_ROW_COUNT']	= $s_row_count;

			// Assign S_FIRST_ROW
			if(!$s_row_count){
				$vararray['S_FIRST_ROW']	= true;
			}

			// Now the tricky part, we always assign S_LAST_ROW and alter the entry before
			// This is much more clever than going through the complete template data on display (phew)
			$vararray['S_LAST_ROW'] = true;
			if($s_row_count > 0){
				unset($this->_data[$blocks[$blockcount]][($s_row_count - 1)]['S_LAST_ROW']);
			}
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

	//Compiles the given string of code, and returns the result in a string.
	private function compile($code, $do_not_echo = false, $retvar = ''){
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
					default:
						$this->compile_var_tags($blocks[0][$curr_tb]);
						$trim_check = trim($blocks[0][$curr_tb]);
						$compile_blocks[] = (!$do_not_echo) ? ((!empty($trim_check)) ? 'echo \'' . $blocks[0][$curr_tb] . '\';' : '') : ((!empty($trim_check)) ? $blocks[0][$curr_tb] : '');
						break;
				}	// switch
			}	// isset
		}	// for

		$template_php = '';
		for ($i = 0; $i < count($text_blocks); $i++){
			$trim_check_text		= ( isset($text_blocks[$i]) ) ? trim($text_blocks[$i]) : '';
			$trim_check_block		= ( isset($compile_blocks[$i]) ) ? trim($compile_blocks[$i]) : '';
			$template_php			.= (!$do_not_echo) ? ((!empty($trim_check_text)) ? 'echo \'' . $text_blocks[$i] . '\';' : '') . ((!empty($compile_blocks[$i])) ? $compile_blocks[$i] : '') : ((!empty($trim_check_text)) ? $text_blocks[$i] . "\n" : '') . ((!empty($compile_blocks[$i])) ? $compile_blocks[$i] . "\n" : '');
		}
		return (!$do_not_echo) ? $template_php : '$' . $retvar . '.= \'' . $template_php . '\'';
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
		//normal language
		$text_blocks	= preg_replace('#\{L_([a-z0-9\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['L_\\1'])) ? \$this->_data['.'][0]['L_\\1'] : ((\$this->lang('\\1')) ? \$this->lang('\\1') : '{ ' . ucfirst(strtolower(str_replace('_', ' ', '\\1'))) . '         }')) . '", $text_blocks);
		//game language
		if(strpos($text_blocks, '{GL_') !== false) $text_blocks = preg_replace('#\{GL_([a-z0-9\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['L_\\1'])) ? \$this->_data['.'][0]['L_\\1'] : ((\$this->glang('\\1', false, true)) ? \$this->glang('\\1') : '{ ' . ucfirst(strtolower(str_replace('_', ' ', '\\1'))) . '         }')) . '", $text_blocks);
		$text_blocks	= preg_replace('#\{([a-z0-9\:\@\-_]*?)\}#is', "' . ((isset(\$this->_data['.'][0]['\\1'])) ? \$this->_data['.'][0]['\\1'] : '') . '", $text_blocks);
		return;
	}

	private function compile_tag_block($tag_args){
		$tag_template_php = '';
		array_push($this->block_names, $tag_args);

		if (sizeof($this->block_names) < 2){
			// Block is not nested.
			$tag_template_php	= '$_' . $tag_args . '_count = (isset($this->_data[\'' . $tag_args . '.\'])) ?  sizeof($this->_data[\'' . $tag_args . '.\']) : 0;' . "\n";
			$tag_template_php	.= 'if ($_' . $tag_args . '_count) {' . "\n";
			$tag_template_php	.= 'for ($_' . $tag_args . '_i = 0; $_' . $tag_args . '_i < $_' . $tag_args . '_count; $_' . $tag_args . '_i++)';

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
			$tag_template_php	.= 'for ($_' . $tag_args . '_i = 0; $_' . $tag_args . '_i < $_' . $tag_args . '_count; $_' . $tag_args . '_i++)';
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
					$token = '==';
				break;

				case '!=':
				case '<>':
				case 'ne':
				case 'neq':
					$token = '!=';
				break;

				case '<':
				case 'lt':
					$token = '<';
				break;

				case '<=':
				case 'le':
				case 'lte':
					$token = '<=';
				break;

				case '>':
				case 'gt':
					$token = '>';
				break;

				case '>=':
				case 'ge':
				case 'gte':
					$token = '>=';
				break;

				case '&&':
				case 'and':
					$token = '&&';
				break;

				case '||':
				case 'or':
					$token = '||';
				break;

				case '!':
				case 'not':
					$token = '!';
				break;

				case '%':
				case 'mod':
					$token = '%';
				break;

				case '(':
					array_push($is_arg_stack, $i);
				break;

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

	public function parse_cssfile($stylepath = false, $data = false) {
		$style = ($data) ? $data : $this->user->style;
		$stylepath = ($stylepath) ? $stylepath : $this->style_code;
		$root_path = '../../../../../';

		$storage_folder = $this->pfh->FolderPath('templates/'.$stylepath, 'eqdkp');
		if (file_exists($storage_folder.$stylepath.'.css')){
			$file = $storage_folder.$stylepath.'.css';
		} elseif (file_exists($this->root_path . 'templates/'.$stylepath.'/'.$stylepath.'.css')) {
			$file = $this->root_path . 'templates/'.$stylepath.'/'.$stylepath.'.css';
		}


		if (file_exists($this->root_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg')){
			$template_background_file = $root_path . 'games/' .$this->config->get('default_game') . '/template_background.jpg' ;
		} else {
			$template_background_file	= $root_path . 'templates/' . $this->user->style['template_path'] . '/images/template_background.jpg';
		}
		if ($this->user->style['background_img'] != ''){
			if (strpos($this->user->style['background_img'],'://') > 1){
				$template_background_file = $this->user->style['background_img'];
			} else {
				$template_background_file = $root_path.$this->user->style['background_img'];
			}
		}

		$in = array(
				"/T_FONTFACE1/",
				"/T_FONTFACE2/",
				"/T_FONTFACE3/",
				"/T_FONTSIZE1/",
				"/T_FONTSIZE2/",
				"/T_FONTSIZE3/",
				"/T_FONTCOLOR1/",
				"/T_FONTCOLOR2/",
				"/T_FONTCOLOR3/",
				"/T_FONTCOLOR_NEG/",
				"/T_FONTCOLOR_POS/",
				"/T_BODY_BACKGROUND/",
				"/T_TABLE_BORDER_WIDTH/",
				"/T_TABLE_BORDER_COLOR/",
				"/T_TABLE_BORDER_STYLE/",
				"/T_BODY_LINK_STYLE/",
				"/T_BODY_LINK/",
				"/T_BODY_HLINK_STYLE/",
				"/T_BODY_HLINK/",
				"/T_HEADER_LINK_STYLE/",
				"/T_HEADER_LINK/",
				"/T_HEADER_HLINK_STYLE/",
				"/T_HEADER_HLINK/",

				"/T_TH_COLOR1/",
				"/T_TR_COLOR1/",
				"/T_TR_COLOR2/",
				"/T_INPUT_BACKGROUND/",
				"/T_INPUT_BORDER_WIDTH/",
				"/T_INPUT_BORDER_COLOR/",
				"/T_INPUT_BORDER_STYLE/",
				"/T_PORTAL_WIDTH_WITHOUT_BOTH_COLUMNS/",
				"/T_PORTAL_WIDTH_WITHOUT_LEFT_COLUMN/",
				"/T_PORTAL_WIDTH/",
				"/T_COLUMN_LEFT_WIDTH/",
				"/T_COLUMN_RIGHT_WIDTH/",

				"/\.\.\/\.\.\//",
				"/\.\.\//",
				"/\(images/",
				"/\('images/",
				"/\(\"images/",
				"/EQDKP_ROOT_PATH/",
				"/EQDKP_IMAGE_PATH/",
				"/TEMPLATE_IMAGE_PATH/",
				"/TEMPLATE_BACKGROUND/",
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


				$root_path,
				$root_path,
				'('.$root_path.'templates/'.$stylepath.'/images',
				'(\''.$root_path.'templates/'.$stylepath.'/images',
				'("'.$root_path.'templates/'.$stylepath.'/images',
				$root_path,
				$root_path.'images/',
				$root_path.'templates/'.$stylepath.'/images',
				$template_background_file,
		);

		if (strlen($file)){
			//The global css file
			$content = file_get_contents($this->root_path.'templates/eqdkpplus.css');
			$data = preg_replace($in, $out, $content);


			$content = file_get_contents($file);
			//Replace everything
			$data .= preg_replace($in, $out, $content);

			//Now the class colors
			$gameclasses = $this->game->get('classes');
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
			}
			
			//User additions
			$strUserFile = $this->root_path . 'templates/'.$stylepath.'/user_additions.css';
			if (file_exists($strUserFile)){
				$content = file_get_contents($strUserFile);
				$data .= preg_replace($in, $out, $content);
			}

			$minify = new Minify_CSS();
			$data = $minify->minify($data);

			$this->pfh->putContent($storage_folder.'main.css', $data);
			$this->timekeeper->put('tpl_cache_'.$stylepath, 'main.css');
		}

	}

	private function generate_error($content, $handle, $sprintf = '', $function = ''){
		if(!$this->error_message){
			$title			= $this->lang('templates_error');
			$content		= (!$this->lang($content)) ? $content : $this->lang($content);

			if ($sprintf !=""){
				$content	= sprintf($content, $sprintf);
			}

			$message		 = '<h2>'.$this->lang('templates_error_desc').':</h2>';
			$message		.= $content;
			$message		.= '<br /><h2>'.$this->lang('templates_error_more').'</h2>';
			$message		.= '<b>File:</b> '.$this->filename[$handle].'<br />';
			$message		.= '<b>Path:</b> '.$this->files[$handle].'<br />';
			$message		.= ($function != "") ? '<b>Function:</b> '.$function.'<br />' : '';
			$message		.= '<b>Style-Code:</b> '.$this->style_code.'<br />';
			$message		.= '<b>Template:</b> '.$this->template.'<br />';
			$message		.= '<b>Handler:</b> '.$handle.'<br />';

			$this->display_error($title, $message);
			$this->error_message	= true;
		}
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_template', template::$shortcuts);
?>