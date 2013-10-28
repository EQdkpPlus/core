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

include_once('itt_parser.aclass.php');

if(!class_exists('armory')) {
	class armory extends itt_parser {
	
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher');

	
		public $supported_games = array('wow');
		public $av_langs = array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU');#, 'jp' => 'ja_JP');

		public $settings = array(
			'itt_icon_loc' => array('name' => 'itt_icon_loc',
									'language' => 'pk_itt_icon_loc',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'http://eu.media.blizzard.com/wow/icons/56/'),
			'itt_icon_ext' => array('name' => 'itt_icon_ext',
									'language' => 'pk_itt_icon_ext',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => '.jpg'),
			'itt_default_icon' => array('name' => 'itt_default_icon',
										'language' => 'pk_itt_default_icon',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'inv_misc_questionmark')
								);

		private $url_prefix = '';
		private $searched_langs = array();
		private $armory_lang = array();
		private $enchants = array();
		
		private $reforged_stats = array(
			//   id			   from, to
				120		=> array(13, 6),
				121		=> array(13, 14),
				122		=> array(13, 31),
				123		=> array(13, 32),
				124		=> array(13, 36),
				125		=> array(13, 37),
				126		=> array(13, 49),
				127		=> array(14, 6),
				128		=> array(14, 13),
				129		=> array(14, 31),
				130		=> array(14, 32),
				131		=> array(14, 36),
				132		=> array(14, 37),
				133		=> array(14, 49),
				134		=> array(31, 6),
				135		=> array(31, 13),
				136		=> array(31, 14),
				137		=> array(31, 32),
				138		=> array(31, 36),
				139		=> array(31, 37),
				140		=> array(31, 49),
				141		=> array(32, 6),
				142		=> array(32, 13),
				143		=> array(32, 14),
				144		=> array(32, 31),
				145		=> array(32, 36),
				146		=> array(32, 37),
				147		=> array(32, 49),
				148		=> array(36, 6),
				149		=> array(36, 13),
				150		=> array(36, 14),
				151		=> array(36, 31),
				152		=> array(36, 32),
				153		=> array(36, 37),
				154		=> array(36, 49),
				155		=> array(37, 6),
				156		=> array(37, 13),
				157		=> array(37, 14),
				158		=> array(37, 31),
				159		=> array(37, 32),
				160		=> array(37, 36),
				161		=> array(37, 49),
				162		=> array(49, 6),
				163		=> array(49, 13),
				164		=> array(49, 14),
				165		=> array(49, 31),
				166		=> array(49, 32),
				167		=> array(49, 36),
				168		=> array(49, 37)
		);

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$this->url_prefix = ($this->config['armory_region']) ? $this->config['armory_region'] : 'eu';
			if($init) {
				// construct armory object
				include_once($this->root_path.'games/wow/objects/bnet_armory.class.php');
				$this->bnet = new bnet_armory($this->config['armory_region'], $this->av_langs[$this->config['game_language']]);
			}
		}

		public function __destruct(){
			unset($this->url_prefix);
			unset($this->searched_langs);
			unset($this->armory_lang);
			parent::__destruct();
		}

		protected function searchItemID($itemname, $lang, $searchagain=0){
			$this->pdl->log('infotooltip', 'armory->searchItemID called: itemname: '.$itemname.', lang: '.$lang.', searchagain: '.$searchagain);
			$searchagain++;
			//encode itemname for usage in url
			$encoded_name = str_replace(' ', '%20', str_replace('+', '%20', urlencode($itemname)));
			$url = 'http://'.$this->url_prefix.'.wowarmory.com/search.xml?searchType=items&amp;searchQuery='.$encoded_name;
			$this->pdl->log('infotooltip', 'Search for ItemID at '.$url);
			$xml_data = $this->puf->fetch($url);
			$xml = simplexml_load_string($xml_data);
			if((!is_object($xml) OR (int) $xml->armorySearch->tabs['count'] < 1)) {
				$this->pdl->log('infotooltip', 'No Items found.');
				if(count($this->config['lang_prio']) >= $searchagain) {
					$this->pdl->log('infotooltip', 'Search again in other language.');
					$this->searched_langs[] = $lang;
					foreach($this->config['lang_prio'] as $slang) {
						if(!in_array($slang, $this->searched_langs)) {
							return $this->searchItemID($itemname, $slang, $searchagain);
						}
					}
				}
			}
			$this->searched_langs = array();

			$xml = $xml->armorySearch->searchResults->items;
			if(!is_object($xml)) {
				$this->pdl->log('infotooltip', 'Invalid XML');
				return array(0, 'items');
			}

			$searchResults = array();
			foreach($xml->children() as $item) {
				$searchResults[(int) $item['id']]['name'] = (string) $item['name'];
				foreach($item->children() as $filter) {
					if($filter['name'] == 'itemLevel') {
						$searchResults[(int) $item['id']]['ilvl'] = (string) $filter['value'];
					} elseif($filter['name'] == 'relevance') {
						$searchResults[(int) $item['id']]['relevance'] = (string) $filter['value'];
					}
				}
			}
			if(!function_exists('sort_by_relevance_ilvl')) {
				function sort_by_relevance_ilvl($a, $b) {
					if($a['relevance'] == $b['relevance']) {
						if($a['ilvl'] == $b['ilvl']) {
							return 0;
						}
						return ($a['ilvl'] > $b['ilvl']) ? -1 : 1;
					}
					return ($a['relevance'] > $b['relevance']) ? -1 : 1;
				}
			}
			uasort($searchResults, "sort_by_relevance_ilvl");
			reset($searchResults);
			$item_id = key($searchResults);
			$debug_out = ($item_id > 0) ? 'Item-ID found: '.$item_id : 'No Item-ID found';
			$this->pdl->log('infotooltip', $debug_out);
			return array($item_id, 'items');
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items', $data=array()){
			$this->pdl->log('infotooltip', 'armory->getItemData called: item_id: '.$item_id.', lang: '.$lang.', itemname: '.$itemname.', data: '.implode(', ', $data));
			if($item_id <= 0) {
				$this->pdl->log('infotooltip', 'No Item-ID given. Set baditem to true.');
				$item['baditem'] = true;
				return $item;
			}
			$item_data = $this->bnet->item($item_id);
			$char_data = array();
			if(!empty($data)) {
				$char_data = $this->bnet->character($data[1], $data[0]);
			}
			//check for error
			if(isset($item_data['status']) && $item_data['status'] == 'error') {
				$this->pdl->log('infotooltip', 'Battle.net responded with an error. Reason: '.$item_data['reason']);
				$item['baditem'] = true;
				$item['name'] = $itemname;
				$item['id'] = $item_id;
				return $item;
			}
			$item['name'] = $item_data['name'];
			$item['id'] = $item_data['id'];
			$item['lang'] = $lang;
			$item['icon'] = $item_data['icon'];
			$item['color'] = 'q'.$item_data['quality'];
			//check if its a pattern
			if($item_data['itemClass'] == 9) {
				$item['html'] = $this->build_pattern($item_data, $lang, $item);
			} else {
				$item['html'] = $this->build_tooltip($item_data, $lang, $item, $char_data, $data[2]);
			}
			if(strlen($item['html']) < 10 || !$item['html']) {
				$this->pdl->log('infotooltip', 'No Tooltip could be created. Set baditem to true.');
				$item['baditem'] = true;
				return $item;
			}
			$this->pdl->log('infotooltip', 'Item succesfully fetched.');
			return $item;
		}
		
		private function build_pattern($data, $lang, $item) {
			$html = '';
			$this->load_armory_lang($lang);
			if(!isset($this->armory_lang[$lang]) OR sizeof($this->armory_lang[$lang]) < 1) {
				$this->pdl->log('infotooltip', 'No language from armory available.');
				return false;
			}
			$html .= "<table><tr><td><b class=\"".$item['color']."\">".$item['name']."</b><br/>";
			
			// required
			$html .= "<table><tr><td>".$this->armory_lang[$lang]['requiredSkill']." ".$this->armory_lang[$lang]['itemClass'][$data['itemClass']][$data['itemSubClass']]." (".$data['requiredSkillRank'].")</td></tr></table>";
			
			// ItemLevel
			if(!empty($data['itemLevel'])) $html .= $this->armory_lang[$lang]['itemLevel']." ".$data['itemLevel']."<br />";
			
			// spells
			$html .= "<span class=\"q2\">".$this->armory_lang[$lang]['trigger']['ON_USE']." ".$data['description']."</span><br />";
			
			// Sell-Price
			if(!empty($data['buyPrice'])) {
				$cop = $data['buyPrice']%100;
				$sil = (($data['buyPrice']-$cop)%10000);
				$gold = $data['buyPrice']-$cop-$sil;
				$html .= "<span class=\"moneygold\">".($gold/10000)."</span> <span  class=\"moneysilver\">".($sil/100)."</span> <span  class=\"moneycopper\">".$cop."</span><br />";
			}
			
			$html .= "</td></tr></table>";
			$tpl_html = file_get_contents($this->root_path.'infotooltip/includes/parser/templates/wow_popup.tpl');
			$html = str_replace('{ITEM_HTML}', $html, $tpl_html);
			return $html;
		}

		private function build_tooltip($data, $lang, $item, $char_data=array(), $slot=''){
			$html = '';
			$this->load_armory_lang($lang);

			if(!empty($char_data['items'])) {
				// Replace standard item info by data found on equipped and possibly altered item
				$char_data = $char_data['items'][$slot];
				$data['itemLevel'] = $char_data['itemLevel'];
				if(!empty($char_data['weaponInfo'])) $data['weaponInfo'] = $char_data['weaponInfo'];
				if(!empty($char_data['stats'])) $data['bonusStats'] = $char_data['stats'];
				
				$char_data_params = $char_data['tooltipParams'];
			}
			if(!isset($this->armory_lang[$lang]) OR sizeof($this->armory_lang[$lang]) < 1) {
				$this->pdl->log('infotooltip', 'No language from armory available.');
				return false;
			}
			$html .= "<table><tr><td><b class=\"".$item['color']."\">".$item['name']."</b><br/>";

			// Extralabels ("Heroic" etc...)
			$html .= (isset($data['nameDescription']) && $data['nameDescription'] != "") ? "<span class=\"q2\">".$data['nameDescription']."</span><br />" : "";
			if(!empty($data['itemLevel'])) $html .= "<span class=\"q\">" . $this->armory_lang[$lang]['itemLevel']." ".$data['itemLevel']."</span><br />";
			// Item upgrade information
			if(!empty($char_data_params['upgrade'])) $html .= "<span class=\"q\">" . $this->armory_lang[$lang]['upgraded'].": " . $char_data_params['upgrade']['current'] . "/" . $char_data_params['upgrade']['total']."</span><br />";
			$html .= (!empty($data['itemBind'])) ? $this->armory_lang[$lang]['itemBind'][$data['itemBind']]."<br />" : "";
			//if(!empty($data['maxCount'])) $html .= 'max-count?'; //($data['maxCount']) ? $this->armory_lang[$lang]['tooltip']['unique-equipped'] : 
			
			// item class information
			if(!empty($data['inventoryType']))
				$html .= "<table><tr><td>".$this->armory_lang[$lang]['inventoryType'][$data['inventoryType']]."</td><th>".$this->armory_lang[$lang]['itemClass'][$data['itemClass']][$data['itemSubClass']]."</th></tr></table>";

			//damage (weapon only)
			if(!empty($data['weaponInfo'])) {
				$html .= "<table><tr><td>".$data['weaponInfo']['damage']['min']." - ".$data['weaponInfo']['damage']['max']." ".$this->armory_lang[$lang]['damage'];
				// if($data['damage']['type']) $html .= "(".$this->armory_lang[$lang]['damage'][$data['damage']['type']].")";
				$html .= "</td><td>".$this->armory_lang[$lang]['weaponSpeed']." ".$data['weaponInfo']['weaponSpeed']."</td></tr></table>";
				$html .= "(".$data['weaponInfo']['dps']." ".$this->armory_lang[$lang]['dps'].")<br />";
			}
			
			//armor
			if(!empty($data['armor'])) $html .= $data['armor']." ".$this->armory_lang[$lang]['armor']."<br />";
			// (!empty($data['baseArmor'])) ? ((isset($tooltip_data['b_armor'])) ? "<span class=\"q2\">".$tooltip_data['armor'].' '.$this->armory_lang[$lang]['tooltip']['armor']."</span>" : $tooltip_data['armor'].$this->armory_lang[$lang]['tooltip']['armor'])."<br />" : "";
			
			// main stats
			if(!empty($data['bonusStats'])) {
				foreach($data['bonusStats'] as $stat) {
					// only main-attributes here
					if($stat['stat'] > 7)
						continue;
					$html .= "+".$stat['amount']." ".$this->armory_lang[$lang]['bonusStats'][$stat['stat']]."<br />";
				}
			}
			
			// secondary stats
			if(!empty($data['bonusStats'])) {
				// Check for reforge
				if(!empty($char_data_params['reforge'])) $reforge = $this->reforged_stats[$char_data_params['reforge']];
				foreach($data['bonusStats'] as $stat) {
					if($stat['stat'] <= 7)
						continue;
					$html .= "<span class=\"q2\">".sprintf($this->armory_lang[$lang]['secondary_stats'], $stat['amount'], $this->armory_lang[$lang]['bonusStats'][$stat['stat']]);
					// Add info about where this stat is reforged from
					if(!empty($reforge)) {
						if($stat['stat'] == $reforge[1]) {
							$html .= " (" . $this->armory_lang[$lang]['reforgedFrom'] . " " . $this->armory_lang[$lang]['bonusStats'][$reforge[0]] . ")";
						}
					}
					$html .= "</span><br />";
				}
			}
			
			$html .= "</td></tr></table><table class=\"tooltipGrouping\"><tr><td>";
			
			// Enchants
			if(isset($char_data_params['enchant'])) {
				$this->load_enchants($lang);
				$html .= "<span class=\"q2\">" . $this->armory_lang[$lang]['enchanted'] . ":  " .$this->enchants[$lang][$char_data_params['enchant']]."</span><br />";
			}			
			
			// Check for extra socket and if found, add prismatic socket
			if(!empty($char_data_params['extraSocket'])) $data['socketInfo']['sockets'][] = array('type' => "PRISMATIC");
			
			// Sockets
			$socket_bonus = false;
			if(!empty($data['socketInfo'])) {
				$socket_bonus = true;
				foreach($data['socketInfo']['sockets'] as $sockkey => $socket) {
					$html .= "<span class=\"socket socket-".strtolower($socket['type']);
					if(!empty($char_data_params['gem'.$sockkey])) {
						$sock_dat = $this->bnet->item($char_data_params['gem'.$sockkey]);
						$html .= "\"><img src=\"".$this->config['icon_path'].$sock_dat['icon'].$this->config['icon_ext']."\" /></span><span class=\"socket-info\">".$sock_dat['gemInfo']['bonus']['name']."</span><br />";
						if(!$this->socket_match($sock_dat['gemInfo']['type']['type'],$socket['type'])) $socket_bonus = false;
					} else {
						$socket_bonus = false;
						$html .= " socket-empty\"></span><span class=\"socket-info q0\">".$this->armory_lang[$lang]['socket'][strtolower($socket['type'])]."</span><br />";
					}
				}
			}
			// socket bonus
			if(!empty($data['socketInfo']['socketBonus'])) {
				$sock_class = ($socket_bonus) ? "q2" : "q0";
				$html .= "<span class=\"".$sock_class."\">".$this->armory_lang[$lang]['socketBonus'].": ".$data['socketInfo']['socketBonus']."</span>";
			}
			
			// set-bonus
			if(!empty($data['itemSet'])) {
				$html .= "</td></tr></table><table class=\"tooltipGrouping\"><tr><td>";
				$html .= "<span class=\"q\">".$data['itemSet']['name']."</span><br />";
				if(!empty($char_data_params['set'])) $set_count = count($char_data_params['set']);
				
				/* Marking and listing of set items disabled. 
				 * Set items of different iLvl may be combined, but how to find all other item sets to check for item id?
				 * 
				foreach($data['itemSet']['items'] as $sdata) {
					$sclass = 'q0';
					if(in_array($sdata, $char_data_params['set'])) {
						$sclass = 'q8';
					} 
					$itemName = $sdata;
					$html .= "<div class=\"".$sclass." indent\">". $itemName ."</div>";
				}*/
				foreach($data['itemSet']['setBonuses'] as $bonus) {
					$sclass = 'q0';
					if($bonus['threshold'] <= $set_count) {
						$sclass = 'q2';
					}
					$html .= "<span class=\"".$sclass."\">(".$bonus['threshold'].") ".$bonus['description']."</span><br />";
				}
			}
			
			// yellow description
			if(!empty($data['description'])) $html .= "<span class=\"q\">".$data['description']."</span><br />";
			
			// spells
			if(!empty($data['itemSpells'])) {
				foreach($data['itemSpells'] as $spell) {
					$html .= "</td></tr></table><table class=\"tooltipGrouping\"><tr><td>";
					$html .= "<span class=\"q2\">".$this->armory_lang[$lang]['trigger'][$spell['trigger']]." ".$spell['spell']['description']."</span><br />";
				}
			}			
			$html .= "</td></tr></table><table class=\"tooltipGrouping\"><tr><td>";
			
			// durability
			if(!empty($data['maxDurability'])) $html .= $this->armory_lang[$lang]['maxDurability'].": ".$data['maxDurability']." / ".$data['maxDurability']."<br />";
			// class restriction
			if(!empty($data['allowableClasses'])) {
				$html .= $this->armory_lang[$lang]['allowableClasses'].": ";
				$classes = array();
				foreach($data['allowableClasses'] as $class) {
					$classes[] = "<span class=\"class_".$this->bnet->ConvertID($class, 'int', 'classes')."\">".$this->class_name($class)."</span>";
				}
				$html .= implode(', ', $classes)."<br />";
			}
			// required things
			if(!empty($data['requiredSkill'])) {
				//$html .= (isset($tooltip_data['requires']['name']) AND $tooltip_data['requires']['name']) ? $this->armory_lang[$lang]['tooltip']['requires']." ".$tooltip_data['requires']['name']." (".$tooltip_data['requires']['rank'].")<br />" : "";
			}
			if(!empty($data['requiredLevel'])) $html .= $this->armory_lang[$lang]['requiredLevel']." ".$data['requiredLevel']."<br />";
			
			// Sell-Price
			if(!empty($data['sellPrice'])) {
				$cop = $data['sellPrice']%100;
				$sil = (($data['sellPrice']-$cop)%10000);
				$gold = $data['sellPrice']-$cop-$sil;
				$html .= "<span class=\"moneygold\">".($gold/10000)."</span> <span  class=\"moneysilver\">".($sil/100)."</span> <span  class=\"moneycopper\">".$cop."</span><br />";
			}
						
			// drop-source
			/*			
			$html .= "</td></tr></table><table class=\"tooltipGrouping\"><tr><td>";
			if(!empty($data['itemSource'])) {
				/* $lng_str = (isset($this->armory_lang[$lang]['source'][$tooltip_data['drop']['value']])) ? $this->armory_lang[$lang]['source'][$tooltip_data['drop']['value']] : $tooltip_data['drop']['value'];
				$html .= "<span class=\"q\">".$this->armory_lang[$lang]['source']['source']."</span>: ".$lng_str."<br />";
				if($tooltip_data['drop']['value'] == 'sourceType.creatureDrop') {
					$html .= ($tooltip_data['drop']['area']) ? "<span class=\"q\">".$this->armory_lang[$lang]['source']['dungeon']."</span> ".$tooltip_data['drop']['area']."<br />" : "";
					$html .= ($tooltip_data['drop']['boss']) ? "<span class=\"q\">".$this->armory_lang[$lang]['source']['boss']."</span> ".$tooltip_data['drop']['boss']."<br />" : "";
					$html .= "<span class=\"q\">".$this->armory_lang[$lang]['source']['droprate']."</span> ".$this->armory_lang[$lang]['drop'][$tooltip_data['drop']['drop']]."<br />";
				}
			}*/
			
			$html .= "</td></tr></table>";
			$tpl_html = file_get_contents($this->root_path.'infotooltip/includes/parser/templates/wow_popup.tpl');
			$html = str_replace('{ITEM_HTML}', $html, $tpl_html);
			return $html;
		}

		private function load_armory_lang($language) {
			if(empty($this->armory_lang[$language])) {
				$file = $this->root_path.'infotooltip/includes/parser/armory/lang_'.$language.'.php';
				if(file_exists($file)) {
					include_once($file);
				} else {
					$this->pdl->log('infotooltip', 'Language '.$language.' not available, fall back to english.');
					$file = $this->root_path.'infotooltip/includes/parser/armory/lang_en.php';
					if(!file_exists($file)) {
						$this->pdl->log('infotooltip', 'Language error, check your files.');
						return false;
					}
					include_once($file);
				}
				$this->armory_lang[$language] = $lang;
			}
		}
		
		private function load_enchants($language) {
			if(empty($this->enchants[$language])) {
				$file = $this->root_path.'infotooltip/includes/parser/armory/enchants_'.$language.'.php';
				if(file_exists($file)) {
					include_once($file);
				} else {
					$this->pdl->log('infotooltip', 'Language '.$language.' not available, fall back to english.');
					$file = $this->root_path.'infotooltip/includes/parser/armory/enchants_en.php';
					if(!file_exists($file)) {
						$this->pdl->log('infotooltip', 'Language error, check your files.');
						return false;
					}
					include_once($file);
				}
				$this->enchants[$language] = $enchants;
			}
		}
		
		private function socket_match($gem, $socket) {
			if($gem == $socket) return true;
			switch($socket) {
				case 'BLUE':
					if($gem == 'PURPLE' || $gem == 'GREEN') return true;
				case 'RED':
					if($gem == 'ORANGE' || $gem == 'PURPLE') return true;
				case 'YELLOW':
					if($gem == 'ORANGE' || $gem == 'GREEN') return true;
				case 'PRISMATIC':
					return true;
			}
			return false;
		}
		
		private function class_name($class_id) {
			if(empty($this->class_names)) {
				$class_names = $this->bnet->getdata('character', 'classes');
				$this->class_names = $class_names['classes'];
			}
			foreach($this->class_names as $class) {
				if($class['id'] === $class_id) return $class['name'];
			}
			return 'unknown';
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_armory', armory::$shortcuts);
?>