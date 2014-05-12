<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

if(!class_exists('infotooltip')) {
	class infotooltip extends gen_class {
		public static $shortcuts = array('pfh' => array('file_handler', array('infotooltips')), 'pdl', 'puf' => 'urlfetcher', 'settings' => 'config', 'db');

		private $avail_parser	= array();

		protected $parser		= false;
		protected $parser_info	= false;

		protected $cached		= false; //cached-files

		public $config			= array();

		/*
		 * constructor-function
		 * params:
		 *  $config = array(
		 *				'game' => gamename,
		 *				'game_language' => short form of language ('en', 'de', etc)
		 *				'debug' => 0 : 1,
		 *				'prio' => array(), //e.g.: armory, wowhead, buffed, aiondatabase, allakhazam
		 *				'lang_prio' => array(), //order of languages which shall be searched (short language names: e.g. en, de)
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
		public function __construct($config=false) {
			//pdl
			$this->pdl->register_type('infotooltip', null, array($this, 'html_format_debug'), array(2,3));

			//scan available source-reader
			if($srcs = opendir($this->root_path.'infotooltip/includes/parser')) {
				$ignore = array('.', '..', '.svn', 'itt_parser.aclass.php');
				while(false !== ($file = readdir($srcs))) {
					if(!in_array($file, $ignore) AND is_file($this->root_path.'infotooltip/includes/parser/'.$file) AND substr($file, -10) == '.class.php') {
						$this->avail_parser[] = substr($file, 0, strpos($file, '.')); //dont save .class.php
					}
				}
			}

			//set config
			$this->copy_config($config);
		}

		/*
		* destructor
		*/
		public function __destruct() {
			unset($this->parser);
			unset($this->avail_parser);
			parent::__destruct();
		}

		public function html_format_debug($log) {
			$text = '<i>'.$this->pdl->format_runtime($log['script_time']).'</i>: '.$log['args'][0];
			return $text;
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
			if(!is_array($config)) {
				$cconfig = $this->settings->get_config();
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
				$this->config[$name] = (isset($cconfig[$key])) ? $cconfig[$key] : '';
			}
			if(!isset($this->config['debug']) && $_GET['debug']) {
				$this->config['debug'] = 1;
			}
			$lang_conv = array('german' => 'de', 'english' => 'en', 'french' => 'fr', 'russian' => 'ru');
			$this->config['game_language'] = (isset($lang_conv[$cconfig['game_language']])) ? $lang_conv[$cconfig['game_language']] : 'en';
			$this->config['prio'][1] = $cconfig['itt_prio1'];
			$this->config['prio'][2] = $cconfig['itt_prio2'];
			$this->config['lang_prio'][1] = $cconfig['itt_langprio1'];
			$this->config['lang_prio'][2] = $cconfig['itt_langprio2'];
			$this->config['lang_prio'][3] = $cconfig['itt_langprio3'];
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
						$this->parser_info[$parse] = registry::register($parse, array(false, $this->config));
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
						$this->parser[$key] = registry::register($parse, array(true, $this->config, $this->root_path, $this->cache, $this->puf, $this->pdl));
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
			if(empty($this->cached)) {
				$this->cache_path = $this->pfh->FolderPath('', 'itt_cache');
				$this->cached = scandir($this->cache_path);
			}
		}

		/*
		 * saves item in cache
		 * @array $item
		 * return @bool
		 */
		private function cache_item($item, $game_id, $name2search, $ext='') {
			$data = serialize($item);

			//add color to item-table
			if(isset($item['color'])) {
				if($game_id > 0) {
					$sql = "UPDATE ".$this->table_prefix."items SET item_color = '".$item['color']."' WHERE game_itemid = '".$this->db->escape($game_id)."';";
				} else {
					$sql = "UPDATE ".$this->table_prefix."items SET item_color = '".$item['color']."' WHERE item_name = '".$this->db->escape($name2search)."';";
				}
				if($this->db->query($sql)) {
					$this->pdl->log('infotooltip', 'Item-color added to items_table.');
				} else {
					$this->pdl->log('infotooltip', 'Item-color not added to items_table.');
				}
			}
			$this->pdl->log('infotooltip', $item['name'].$ext.' added to cache in lang '.$item['lang'].'.');
			$this->pfh->putContent($this->pfh->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$item['name'].$ext).'.itt', 'itt_cache'), $data);
			$this->pfh->putContent($this->pfh->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$name2search.$ext).'.itt', 'itt_cache'), $data);
			if(!empty($item['id'])) {
				$this->pfh->putContent($this->pfh->FilePath(md5($this->config['game'].'_'.$item['lang'].'_'.$item['id'].$ext).'.itt', 'itt_cache'), $data);
			}
			return true;
		}

		/*
		 * deletes item from cache
		 * @string $item_name
		 * @string $lang
		 * return @bool
		 */
		private function delete_item($item_name, $lang, $game_id, $ext='') {
			$iddel = true;
			$namedel = true;
			$this->pdl->log('infotooltip', $this->config['game'].'_'.$lang.'_'.$item_name.$ext.' deleted from cache.');
			$filepath = $this->pfh->FilePath(md5($this->config['game'].'_'.$lang.'_'.$item_name.$ext).'.itt');
			if(is_file($filepath)) $namedel = $this->pfh->Delete($filepath);
			if($game_id) {
				$this->pdl->log('infotooltip', $this->config['game'].'_'.$lang.'_'.$game_id.$ext.' deleted from cache.');
				$filepath = $this->pfh->FilePath(md5($this->config['game'].'_'.$lang.'_'.$game_id.$ext).'.itt');
				if(is_file($filepath)) $iddel = $this->pfh->Delete($filepath);
			}
			return ($iddel && $namedel);
		}

		public function reset_cache() {
			$this->init_cache();
			$this->pdl->log('infotooltip', 'Delete whole cache.');
			return $this->pfh->Delete($this->cache_path, false);
		}

		/*
		 * updates item
		 * @string	$item_name
		 * @string	$lang
		 * @int		$game_id
		 * return @array
		 */
		protected function update($item_name, $lang=false, $game_id=false, $data=array()) {
			$this->pdl->log('infotooltip', 'update called: item_name: '.$item_name.', lang: '.$lang.', game_id: '.$game_id.', data: '.implode(', ', $data));
			$lang = (!$lang) ? $this->config['game_lang'] : $lang;
			$this->load_parser();
			$this->init_cache();
			$ext = '';
			foreach($this->parser as $parse) {
				if(!$parse->av_langs[$lang]) {
					$lang = $this->config['game_lang'];
					if(!$parse->av_langs[$lang]) {
						$lang = key($parse->av_langs);
					}
				}
				$this->pdl->log('infotooltip', 'Call getitem for parser: '.get_class($parse));
				if($item = $parse->getitem($item_name, $lang, $game_id, false, $data)) {
					if($data[0] && $data[1]) {
						$ext = '_'.$data[0].'_'.$data[1];
					}
					if(isset($item['baditem']) && $item['baditem']) {
						$item['name'] = $item_name;
						$item['icon'] = $this->config['default_icon'];
						$item['lang'] = $lang;
						continue; //item not fully fetched
					}
					break; //no errors occured, item fully fetched
				}
			}
			$this->cache_item($item, $game_id, $item_name, $ext);
			return $item;
		}

		/*
		 * return itemdata
		 * @string	$item_name
		 * @string	$lang
		 * @int	    $game_id
		 * @bool	$forceupdate
		 * return @array
		 */
		public function getitem($item_name, $lang=false, $game_id=false, $forceupdate=false, $data=array()) {
			$item_name = htmlspecialchars_decode($item_name, ENT_QUOTES);
			$this->pdl->log('infotooltip', 'getitem called: item_name: '.$item_name.', lang: '.$lang.', game_id: '.$game_id.', forceupdate: '.$forceupdate.', data: '.implode(', ', $data));
			$lang = (!$lang || $lang == '') ? $this->config['game_language'] : $lang;
			$this->init_cache();
			$ext = '';
			if($data[0] && $data[1]) {
				$ext = '_'.$data[0].'_'.$data[1];
			}

			if(!$forceupdate) {
				$cache_name = $this->config['game'].'_'.$lang.'_'.((is_numeric($game_id) AND $game_id > 0) ? $game_id : $item_name).$ext;
				$this->pdl->log('infotooltip', 'Search in cache: '.$cache_name);
				$cache_name = md5($cache_name).'.itt';
				if(in_array($cache_name, $this->cached)) {
					$item = unserialize(file_get_contents($this->pfh->FilePath($cache_name, 'itt_cache')));
					if(isset($item['baditem'])){
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
						if(!isset($this->parser_info[$parsing]->av_langs[$before_lang])) {
							$mid_lang = $this->config['game_language'];
							$new_lang_set = true;
							if(!isset($this->parser_info[$parsing]->av_langs[$mid_lang])) {
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
			if(!isset($item['html']) OR !$item['html'] OR !isset($item['name'])) {
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
if(!function_exists('itt_replace_bbcode')) {
	function itt_replace_bbcode($text, $lang='') {
		#[item game_id=0 lang=0 direct=0 onlyicon=0]name[/item]
		preg_match_all('+\[item(.*?)\](.*?)\[\/item\]+', $text, $matches);
		foreach($matches[1] as $k => $match) {
			$data = array(
				'name' => $matches[2][$k],
				'game_id' => 0,
				'lang' => '',
				'direct' => 0,
				'onlyicon' => false,
				'char_name' => '',
				'server' => '',
				'slotid' => 0,
			);
			$pre_options = explode(' ', $match);
			foreach($pre_options as $option) {
				if(strpos($option, '=') === false) continue;
				list($key, $val) = explode('=', $option);
				//check for invalid chars
				if(preg_match('#[^a-zA0-9_]#', $key) OR !isset($data[$key])) continue;
				$data[$key] = $val;
			}
			$direct = ($data['direct']) ? 1 : 0;
			unset($data['direct']);
			$id = uniqid();
			$data['lang'] = ($lang && !$data['lang']) ? $lang : $data['lang'];
			$insert = '<span class="infotooltip" id="bb_'.$id.'" title="'.$direct.urlencode(base64_encode(serialize($data))).'">'.$data['name'].'</span>';
			$text = str_replace($matches[0][$k], $insert, $text);
		}
		return $text;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_infotooltip', infotooltip::$shortcuts);
?>