<?php
 /*
 * Project:     EQdkp-Plus Infotooltips
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2009-10-28 18:08:57 +0100 (Wed, 28 Oct 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 6294 $
 *
 * $Id: $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('infotooltip')) {
  class infotooltip {

  	private $avail_parser = array();
  	private $pcache = false;

  	protected $root_path = './../';
  	protected $db = false;
  	protected $table_prefix = '';
  	protected $pdl = false;
  	protected $urlreader = false;
  	protected $parser = false;
  	protected $parser_info = false;

  	protected $cache = false; //file_cache
	protected $cached = false; //cached-files

    public $config = array();

	/*
	 * constructor-function
	 * params:
	 *  $config = array(
	 *				'game' => gamename,
	 *				'game_language' => short form of language ('en', 'de', etc)
     *              'debug' => 0 : 1,
	 *				'prio' => array(), //e.g.: armory, wowhead, buffed, aiondatabase, allakhazam
     *              'lang_prio' => array(), //order of languages which shall be searched (short language names: e.g. en, de)
     *				-- some parser-specific configs --
	 *				'icon_path' => path2icon, //e.g. http://www.wowhead.com/irgendwas
	 *				'icon_ext' => extension, //e.g. '.png'
	 *				'default_icon' => default_icon_name,
	 *				'css_file' => cssfile_name,  -- currently not in use
	 *				'armory_region' => 'eu' : 'us', //only if armory in prio
	 *				'useitemlist' => 0 : 1,
	 *			  );
	 *			  OR false => values will be read from db
	 *  $use_pdl: object $pdl OR false (a new instance will be created)
	 *  $db_connection: 'new' or (only in eqdkp-plus) the db-object itself
	 *  $path2config: only needed if new db-connection initialized
	 *	$root_path: path to eqdkp-plus root folder
	 */
  	public function __construct($config=false, $pdl=false, $db_connection='new', $path2config=false, $root_path=false) {
  		if($root_path) {
  			$this->root_path = $root_path;
  		}
		
  		//init pdl
  		if(is_object($pdl)) {
  			$this->pdl = $pdl;
  		} else {
  			include_once($this->root_path.'core/plus_debug_logger.class.php');
  			$this->pdl = new plus_debug_logger();
  		}
  		$this->pdl->register_type('infotooltip', null, null, array(2,3));
        //register pdl format_functions
        // -- currently missing

  		//scan available source-reader
  		if($srcs = opendir($this->root_path.'infotooltip/includes/parser')) {
  			$ignore = array('.', '..', '.svn', 'itt_parser.aclass.php');
  			while(false !== ($file = readdir($srcs))) {
  				if(!in_array($file, $ignore) AND is_file($this->root_path.'infotooltip/includes/parser/'.$file) AND substr($file, -10) == '.class.php') {
  					$this->avail_parser[] = substr($file, 0, strpos($file, '.')); //dont save .class.php
  				}
  			}
  		}

  		//init our db-class
  		if(is_object($db_connection)) {
            $this->db = $db_connection;
            global $table_prefix;
            $this->table_prefix = $table_prefix;
			$this->dbname = $this->db->dbname;
        } else {
        	if($path2config) {
            	require_once($this->root_path.$path2config);
        	} else {
            	require_once($this->root_path.'config.php');
        	}
        	$this->table_prefix = $table_prefix;
			$this->dbname = $dbname;
	        $dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
			$this->config_file = array($dbhost, $dbname, $dbuser, $dbpass, $dbms);
	    }

	    //init urlreader class
	    include_once($this->root_path.'core/urlreader.class.php');
	    $this->urlreader = new urlreader();

	    //set config
	    $this->copy_config($config);
  	}

  	/*
  	 * destructor
  	 */
  	public function __destruct() {
  		unset($this->parser);
        unset($this->cache);
  		unset($this->config);
  		unset($this->avail_parser);
  		unset($this->root_path);
  	}

  	/*
  	 * returns list with parsers useable for selected game
  	 */
  	public function get_parserlist($game=false) {
		$game = ($game) ? $game : $this->config['game'];
  		$parserlist = array();
  		$this->load_parser(true);
  		foreach($this->parser_info as $parse) {
  			if(in_array($game, $parse->supported_games)) {
  				$parserlist[get_class($parse)] = get_class($parse);
  			}
  		}
  		return $parserlist;
  	}

  	/*
  	 * returns list with supported games (dynamically created)
  	 */
  	public function get_supported_games() {
  		$supp_games = array();
  		$this->load_parser(true);
  		foreach($this->parser_info as $parse) {
  			foreach($parse->supported_games as $game) {
  				$supp_games[] = $game;
  			}
  		}
  		return array_unique($supp_games);
  	}

  	/*
  	 * returns list with supported languages (dynamically created)
  	 */
  	public function get_supported_languages($game=false) {
		$game = ($game) ? $game : $this->config['game'];
  		$supp_langs = array();
  		$this->load_parser(true);
  		foreach($this->parser_info as $parse) {
			if(in_array($game, $parse->supported_games)) {
				foreach($parse->av_langs as $short => $long) {
					$supp_langs[$short] = $short;
				}
  			}
  		}
  		return $supp_langs;
  	}

  	/*
  	 * return needed settings for parsers
  	 */
  	public function get_extra_settings() {
  		$this->load_parser(true);
  		$setts = array();
  		foreach($this->get_parserlist() as $parse) {
  			foreach($this->parser_info[$parse]->settings as $key => $val) {
  				$setts[$key] = $val;
  			}
  		}
  		return $setts;
  	}

  	/*
  	 * execute on update of firstprio of parser (called by settings.php)
  	 */
  	public function changed_prio1($parser) {
  		$this->load_parser(true);
		$setts = array();
		foreach($this->parser_info[$parser]->settings as $skey => $sval) {
			$setts[$skey] = $sval['default'];
		}
  		return $setts;
  	}

  	/*
  	 * checks and copies $config to $this->config
  	 * @array $config (for info see __construct)
  	 */
  	private function copy_config($config) {
  		//fetch config from file
  		if($config === false) {
			include_once($this->root_path.'core/config.class.php');
			include($this->root_path.'core/file_handler/file_handler.class.php');
			$pcache_c = new file_handler($this->dbname);
			$config_class = new mmocms_config($pcache_c, $this->db);
			$cconfig = $config_class->get_config();
			unset($config_class);
			unset($pcache_c);
		} elseif (is_object($config)) {
			$cconfig = $config->get_config();
		} else {
			$cconfig = $config;
		}
  		$needed_configs = array(
  			'game'				=> 'default_game',
  			'icon_path'			=> 'itt_icon_loc',
  			'icon_ext'			=> 'itt_icon_ext',
  			'default_icon'		=> 'itt_default_icon',
			'useitemlist'		=> 'itt_useitemlist',
  			'armory_region'		=> 'uc_server_loc',
  			'debug'				=> 'itt_debug',
  		);
  		//only copy the "wanted" configs array('game', 'icon_path', 'icon_ext', 'default_icon', 'useitemlist', 'armory_region', 'debug', 'game_language', 'prio', 'lang_prio')
  		foreach($needed_configs as $name => $key) {
			$this->config[$name] = $cconfig[$key];
		}
		if(!$this->config['debug'] AND $_GET['debug']) {
			$this->config['debug'] = 1;
		}
  		$lang_conv = array('german' => 'de', 'english' => 'en', 'french' => 'fr', 'russian' => 'ru');
		$this->config['game_language'] = $lang_conv[$cconfig['game_language']];
		$this->config['prio'][1] = $cconfig['itt_prio1'];
		$this->config['prio'][2] = $cconfig['itt_prio2'];
		$this->config['lang_prio'][1] = $cconfig['itt_langprio1'];
		$this->config['lang_prio'][1] = $cconfig['itt_langprio1'];
		$this->config['lang_prio'][1] = $cconfig['itt_langprio1'];
  		//check prio-array for valid data
  		foreach($this->config['prio'] as $parse) {
  			if(!in_array($parse, $this->avail_parser) AND $parse) {
  				$this->pdl->log('infotooltip', 'Invalid element in prio-list!');
  			}
  		}
        if(in_array('armory', $this->config['prio']) AND !in_array($this->config['armory_region'], array('us', 'eu', 'kr', 'tw', 'cn'))) {
        	$this->pdl->log('infotooltip', 'Wrong or no region for armory given!');
        }
  	}

  	/*
  	 * loads parser
  	 * @string $parser
  	 */
  	private function load_parser($info=false) {
  		if($info) {
  			if(!$this->parser_info) {
  				foreach($this->avail_parser as $parse) {
  					include($this->root_path.'infotooltip/includes/parser/'.$parse.'.class.php');
  					$this->parser_info[$parse] = new $parse(false, $this->config);
  				}
  			}
  			return true;
  		}
  		if(!$this->parser) {
			$log = 'Load Parser in priority: ';
  			foreach($this->config['prio'] as $key => $parse) {
				$log .= $key.'. '.$parse.', ';
  				if(in_array($parse, $this->avail_parser)) {
	  				include($this->root_path.'infotooltip/includes/parser/'.$parse.'.class.php');
	  				$this->parser[$key] = new $parse(true, $this->config, $this->root_path, $this->cache, $this->urlreader, $this->pdl);
	  			}
	  		}
			$this->pdl->log('infotooltip', $log);
	  		ksort($this->parser);
	  	}
  	}

  	/*
  	 * inits cache
  	 */
  	private function init_cache() {
  		if(!$this->cache) {
  			include($this->root_path.'core/file_handler/file_handler.class.php');
  			$this->cache = new file_handler('infotooltips');
  			$this->cache_path = $this->cache->FolderPath('itt_cache');
			$this->cached = scandir($this->cache_path);
  		}
		if(!is_object($this->db)) {
	        $eqdkp_root_path = $this->root_path;
			$dbms = $this->config_file[4];
	        require($this->root_path.'gplcore/db/'.$dbms.'.php');
	        $this->db = new $sql_db($this->pdl, $this->root_path, $this->table_prefix);
	        $this->db->sql_connect($this->config_file[0], $this->config_file[1], $this->config_file[2], $this->config_file[3], false);
		}
  	}

  	/*
  	 * saves item in cache
  	 * @array $item
  	 * return @bool
  	 */
  	private function cache_item($item, $game_id, $ext='', $name2search) {
		$data = serialize($item);
		
		//add color to item-table
		if($item['color']) {
			if($game_id > 0) {
				$sql = "UPDATE ".$this->table_prefix."items SET item_color = '".$item['color']."' WHERE game_itemid = '".$game_id."';";
			} else {
				$sql = "UPDATE ".$this->table_prefix."items SET item_color = '".$item['color']."' WHERE item_name = '".$name2search."';";
			}
			if($this->db->query($sql)) {
				$this->pdl->log('infotooltip', 'Item-color added to items_table.');
			} else {
				$this->pdl->log('infotooltip', 'Item-color not added to items_table.');
			}
		}
		$this->pdl->log('infotooltip', $item['name'].$ext.' added to cache in lang '.$item['lang'].'.');
  		$this->cache->putContent($data, $this->cache->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$item['name'].$ext).'.itt', 'itt_cache'));
  		$this->cache->putContent($data, $this->cache->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$name2search.$ext).'.itt', 'itt_cache'));
		if($item['id'] > 0) {
			$this->cache->putContent($data, $this->cache->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$item['id'].$ext).'.itt', 'itt_cache'));
		}
		return true;
  	}

  	/*
  	 * deletes item from cache
  	 * @string $item_name
  	 * @string $lang
  	 * return @bool
  	 */
  	private function delete_item($item_name, $lang, $ext='') {
		$this->pdl->log('infotooltip', $this->config['game'].'_'.$lang.'_'.$item_name.$ext.' deleted from cache.');
		return unlink($this->cache->FilePath(md5($this->config['game'].'_'.$lang.'_'.$item_name.$ext).'.itt'));
  	}

  	public function reset_cache() {
  		$this->init_cache();
		$this->pdl->log('infotooltip', 'Delete whole cache.');
  		return $this->cache->Delete($this->cache_path, true);
  	}

  	/*
  	 * updates item
  	 * @string	$item_name OR game_id if $game_id true
  	 * @string	$lang
     * @bool    $game_id
  	 * return @array
  	 */
  	protected function update($item_name, $lang=false, $game_id=false, $data=array()) {
		$this->pdl->log('infotooltip', 'update called: item_name: '.$item_name.', lang: '.$lang.', game_id: '.$game_id.', data: '.implode(', ', $data));
  		$lang = (!$lang) ? $this->config['game_lang'] : $lang;
  		$this->load_parser();
  		$this->init_cache();
  		foreach($this->parser as $parse) {
			if(!$parse->av_langs[$lang]) {
				$lang = $this->config['game_lang'];
				if(!$parse->av_langs[$lang]) {
					$lang = key($parse->av_langs);
				}
			}
			$this->pdl->log('infotooltip', 'Call getitem for parser: '.get_class($parse));
  			if($item = $parse->getitem($item_name, $lang, $game_id, false, $data)) {
				$ext = '';
				if($data[0]) {
					$ext = '_'.$data[0].'_'.$data[1];
				}
				if($item['baditem'] AND !$item['name']) {
					$item['name'] = $item_name;
					$item['icon'] = $this->config['default_icon'];
					$item['lang'] = $lang;
					continue; //item not fully fetched
				}
  				break; //no errors occured, item fully fetched
  			}
  		}
  		$this->cache_item($item, $game_id, $ext, $item_name);
  		return $item;
  	}

  	/*
  	 * return itemdata
  	 * @string	$item_name OR game_id if $game_id true
  	 * @string	$lang
     * @bool    $game_id
  	 * @bool	$forceupdate
  	 * return @array
  	 */
  	public function getitem($item_name, $lang=false, $game_id=false, $forceupdate=false, $data=array()) {
		$this->pdl->log('infotooltip', 'getitem called: item_name: '.$item_name.', lang: '.$lang.', game_id: '.$game_id.', forceupdate: '.$forceupdate.', data: '.implode(', ', $data));
  		$lang = (!$lang) ? $this->config['game_language'] : $lang;
  		$this->init_cache();
		$ext = '';
		if($data[0]) {
			$ext = '_'.$data[0].'_'.$data[1];
		}
		if(!$forceupdate) {
			$cache_name = $this->config['game'].'_'.$lang.'_'.((is_numeric($game_id) AND $game_id > 0) ? $game_id : $item_name).$ext;
			$this->pdl->log('infotooltip', 'Search in cache: '.$cache_name);
			$cache_name = md5($cache_name).'.itt';
			if(in_array($cache_name, $this->cached)) {
				$item = unserialize(file_get_contents($this->cache->FilePath($cache_name, 'itt_cache')));
				if($item['baditem']) {
					$this->pdl->log('infotooltip', 'Item found, but item is baditem. forceupdate set to true.');
					$forceupdate = true;
				} else {
					$this->pdl->log('infotooltip', 'Item found.');
					return $this->item_return($item);
				}
			} else { //check for language
				$this->pdl->log('infotooltip', 'Item not found. Check if language '.$lang.' is available.');
				$this->load_parser(true);
				$new_lang_set = false;
				$before_lang = $lang;
				foreach($this->config['prio'] as $parsing) {
					if(!$this->parser_info[$parsing]->av_langs[$before_lang]) {
						$mid_lang = $this->config['game_lang'];
						$new_lang_set = true;
						if(!$this->parser_info[$parsing]->av_langs[$mid_lang]) {
							$mid_lang = key($this->parser_info[$parsing]->av_langs);
						}
					} else {
						$new_lang_set = false;
						break;
					}
				}
				if($new_lang_set) {
					$this->pdl->log('infotooltip', 'Language was not available. Changed language to '.$mid_lang.'. Search again.');
					$lang = $mid_lang;
					return $this->getitem($item_name, $lang, $game_id, $forceupdate, $data);
				}
				$this->pdl->log('infotooltip', 'Language is available.');
			}
  		}
		if($forceupdate) {
			$this->pdl->log('infotooltip', 'Force item-update.');
  			$this->delete_item($item_name, $lang, $game_id, $ext);
  		}
  		$item = $this->update($item_name, $lang, $game_id, $data);
  		return $this->item_return($item);
  	}
	
	private function item_return($item) {
		if(!$item['html']) {
			$item['html'] = file_get_contents($this->root_path.'infotooltip/includes/parser/templates/'.$this->config['game'].'_popup.tpl');
			$item['html'] = str_replace('{ITEM_HTML}', $item['name']."<br />Item not found.<br />", $item['html']);
		}
  		if($this->config['debug']) {
  			$item['html'] = str_replace('{DEBUG}', '<br />'.$this->pdl->get_html_log(3, 'infotooltip'), $item['html']);
  		} else {
			$item['html'] = str_replace('{DEBUG}', '', $item['html']);
		}
		return $item;
	}
  }#class
}

/**
 * Parse item-bbcode: [item game_id=0 lang=0 direct=0 onlyicon=0]name[/item]
 * @string $text
 * return @string
 */
if(!function_exists('replace_bbcode')) {
	function replace_bbcode($text) {
		infotooltip_js();
		preg_match_all('+\[item(.*?)\](.*?)\[\/item\]+', $text, $matches);
		foreach($matches[1] as $k => $match) {
			$insert = '<span id="'.uniqid().'" class="infotooltip" title="'.base64_encode(stripslashes($matches[2][$k])).'"';
			$pre_options = explode(' ', $match);
			$game_id = 0;
			foreach($pre_options as $option) {
				list($key, $val) = explode('=', $option);
				$insert .= ' '.$key.'="'.$val.'"';
				if($key == 'game_id' AND $val > 0) {
					$game_id = 1;
					$insert .= 'use_game_id="1"';
				}
			}
			$insert = ($game_id) ? $insert : $insert." game_id='0'";
			$insert .= '>'.$matches[2][$k].'</span>';
			$text = str_replace($matches[0][$k], $insert, $text);
		}
		return $text;
	}
}
?>