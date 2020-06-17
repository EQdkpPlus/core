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

class language extends gen_class {

	private $arrLang = array();
	private $strLangCode = 'english';
	private $loaded_plugs	= array();		// Not loaded plug-langs
	private $plugs_to_load	= array();		// Plugins, which have language to load
	private $unused			= array();		// Unused language keys

	public function __construct($strLanguage=''){
		if(!$this->pdl->type_known("language"))
			$this->pdl->register_type("language", false, array($this, 'pdl_html_format_language'), array(3, 4));
		if(!$this->pdl->type_known("unused_language"))
			$this->pdl->register_type("unused_language", array($this, 'pdl_pt_format_unused_language'), array($this, 'pdl_html_format_unused_language'), array(4));

		$this->init_lang($strLanguage);
	}

	/**
	 * Loads a specific core Language
	 *
	 * @param string $lang_name
	 * @return boolean
	 */
	public function init_lang($lang_name='') {
		if($lang_name == "") $lang_name = $this->config->get('default_lang');
		if(!$lang_name) return false;

		$this->strLangCode = $lang_name;

		if(isset($this->arrLang[$lang_name])) return true;

		$file_path = $this->root_path . 'language/' . $lang_name . '/';
		$tmp_lang = array();

		//If there is no main language file, abbort
		if(!file_exists($file_path . 'lang_main.php'))  {
			$this->arrLang[$lang_name] = "error";
			pd("Error loading language ".$lang_name);
			return false;
		}

		include($file_path . 'lang_main.php');
		if (is_array($lang)) {
			$tmp_lang = array_merge($tmp_lang, $lang);
			unset($lang);
		}

		if (defined('IN_ADMIN')) {
			include($file_path . 'lang_admin.php');
			if (is_array($lang)) {
				$tmp_lang = array_merge($tmp_lang, $lang);
				unset($lang);
			}
		}

		if (defined('MAINTENANCE_MODE')) {
			include($file_path . 'lang_mmode.php');
			if (is_array($lang)) {
				$tmp_lang = array_merge($tmp_lang, $lang);
				unset($lang);
			}
		}

		$this->arrLang[$lang_name] = &$tmp_lang;
	}

	private function init_plug_lang($lang_name) {
		$lang = array();
		foreach($this->plugs_to_load as $plug) {
			if(isset($this->loaded_plugs[$plug][$lang_name]) && $this->loaded_plugs[$plug][$lang_name]) continue;

			$file_path = $this->root_path.'plugins/'.$plug.'/language/'.$lang_name.'/lang_main.php';
			if(file_exists($file_path)) {
				include_once($file_path);
			}

			$this->loaded_plugs[$plug][$lang_name] = true;
		}
		if(count($this->plugs_to_load) > 0) $this->add_lang($lang_name, $lang);
	}

	private function missing_plug_lang($lang_name) {
		foreach($this->plugs_to_load as $code) {
			if(!isset($this->loaded_plugs[$code][$lang_name])) return true;
		}
		return false;
	}

	public function register_plug_language($plugin) {
		$this->plugs_to_load[] = $plugin;
	}

	public function add_lang($lang_name, $langtoadd) {
		if(!is_array($langtoadd)) return false;
		$this->arrLang[$lang_name] = (isset($this->arrLang[$lang_name]) && is_array($this->arrLang[$lang_name])) ? array_merge($this->arrLang[$lang_name], $langtoadd) : $langtoadd;
	}

	private function lang_error($key, $return_key, $warning=false, $error=true) {
		if($error && $this->pdl->should_log('language')) {
			$debug = debug_backtrace();
			if(!$warning) $this->pdl->log('language', $key, $debug[2]['file'], $debug[2]['line']);
			else $this->pdl->log('language', $key, $debug[2]['file'], $debug[2]['line'], $this->strLangCode);
			unset($debug);
		}
		return ($return_key) ? $key : false;
	}

	public function get($strLanguage, $key, $return_key=false, $error=true, $lang=false, $error_key='') {
		if(is_array($key)) {
			$keys = $key;
			$key = array_shift($keys);
		}
		if(!is_array($lang)) {
			$cur_lang_name = ($lang !== false) ? $lang : $strLanguage;
			if(!$cur_lang_name) $cur_lang_name = $this->config->get('default_lang');

			//Current Languages has not been loaded yet
			if(!isset($this->arrLang[$cur_lang_name])){
				$this->init_lang($cur_lang_name);
			}


			$blnCurrentLanguageLoadingError = ($this->arrLang[$cur_lang_name] == "error" ) ? true : false;
			//Switch to Default language
			if($blnCurrentLanguageLoadingError || !isset($this->arrLang[$cur_lang_name][$key])) {
				//check if plugin_lang initialized first
				if($this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
				if(!isset($this->arrLang[$cur_lang_name][$key]) && $cur_lang_name != $this->config->get('default_lang')) {
					$this->init_lang($this->config->get('default_lang'));
					$cur_lang_name = $this->config->get('default_lang');
					$default_chosen = true;
				}
				if(!isset($this->arrLang[$cur_lang_name][$key]) && $this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
				if(!isset($this->arrLang[$cur_lang_name][$key]) && $this->strLangCode != 'english' &&  $this->config->get('default_lang') != 'english') {
					$this->init_lang('english');
					$cur_lang_name = 'english';
					$default_chosen = true;
				}
				if(!isset($this->arrLang[$cur_lang_name][$key]) && $this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
			}
			$lang = $this->arrLang[$cur_lang_name];
		}

		//key not available at all languages
		if(!isset($lang[$key])) {
			return $this->lang_error($error_key.$key, $return_key, false, $error);
		}

		//key is available, but not in current language, but in default one
		if(isset($default_chosen) && $error && !$blnCurrentLanguageLoadingError) {
			$this->lang_error($key, false, true);
		}
		if(isset($keys) && count($keys) > 0) return $this->get($cur_lang_name, $keys, $return_key, $error, $lang[$key], $error_key.$key.' -> ');
		$this->lang_used($key);
		return $lang[$key];
	}

	/* Unused language addition start */
	public function pdl_html_format_language($log_entry) {
		return "Variable ".$log_entry['args'][0]." not found".((isset($log_entry['args'][3])) ? " in language ".$log_entry['args'][3] : "").".<br />File: ".$log_entry['args'][1]."<br />Line: ".$log_entry['args'][2];
	}

	public function pdl_pt_format_unused_language($log_entry) {
		return serialize($log_entry['args']);
	}

	public function pdl_html_format_unused_language($log_entry) {
		$text = 'array('.count($log_entry['args'][0]).') {<br />';
		foreach($log_entry['args'][0] as $key => $val) {
			if(is_array($val)) continue;
			$text .= '&nbsp;&nbsp;&nbsp;&nbsp;["'.$key.'"] => "'.htmlspecialchars($val).'",<br />';
		}
		return $text.'}';
	}

	private function init_unused_lang() {
		return true;

		if(count($this->unused) > 0) return true;
		$file = $this->pfh->FilePath('unused.lang', 'eqdkp');
		$data = unserialize_noclasses(file_get_contents($file));
		if(is_array($data) && count($data) > 0) {
			$this->unused = $data;
			//check for deleted keys
			foreach($this->unused as $key => $val) {
				if(!isset($this->arrLang[$this->strLangCode][$key])) unset($this->unused[$key]);
			}
		} elseif(DEBUG >= 4) {
			$this->unused = array();
			$this->read_folder($this->root_path);
		}
	}

	private function read_folder($folder){
		$folder = preg_replace('/\/$/', '', $folder);
		$ignore = array('.', '..', '.svn', '.htaccess', 'index.html', 'language');
		if ( $dir = opendir($folder) ){
			while ( $path = readdir($dir) ){
				if ( !in_array(basename($path), $ignore) && is_dir($folder . '/' . $path) ){
					$this->read_folder($folder . '/' . $path);
				}elseif ( !in_array(basename($path), $ignore) && is_file($folder . '/' . $path) ){
					$this->read_file_html($folder . '/' . $path);
					$this->read_file_php($folder . '/' . $path);
				}
			}
		}
	}

	private $used = array();
	private function read_file_html($path){
		// Check if its a php, tpl or html file
		if (!preg_match('/\.html$/', $path) || !preg_match('/\.tpl$/', $path)){ return; }
		$search = (count($this->unused) < 1) ? $this->arrLang[$this->strLangCode] : $this->unused;
		$file = file_get_contents($path);
		preg_match_all('#\{L_([a-zA-Z0-9_]+)\}#', $file, $matches, PREG_SET_ORDER);
		if (count($matches) > 0 ){
			foreach ( $matches as $match ){
				if(isset($search[$match[1]])) $this->used[$match[1]] = $search[$match[1]];
			}
			$this->unused = array_diff_key($search, $this->used);
		}
	}

	private function read_file_php($path){
		// Check if its a php, tpl or html file
		if (!preg_match('/\.php$/', $path)){ return; }
		$search = (count($this->unused) < 1) ? $this->arrLang[$this->strLangCode] : $this->unused;
		$file = file_get_contents($path);
		preg_match_all('/lang\(\'([\w]+)\'\)/', $file, $matches, PREG_SET_ORDER);
		if (count($matches) > 0 ){
			foreach ( $matches as $match ){
				if(isset($search[$match[1]])) $this->used[$match[1]] = $search[$match[1]];
			}
			$this->unused = array_diff_key($search, $this->used);
		}
	}

	private function lang_used($key) {
		if(isset($this->unused[$key])) unset($this->unused[$key]);
	}

	public function output_unused() {
		$this->init_unused_lang();
		$this->pdl->log('unused_language', $this->unused);
	}
	/* Unused language addition end */
}
