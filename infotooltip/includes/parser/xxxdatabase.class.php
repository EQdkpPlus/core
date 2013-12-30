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

if(!class_exists('xxxdatabase')) {
	class xxxdatabase extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('aion', 'allods');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();
		

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'aion' => array('icon_loc' => 'http://www.aiondatabase.com/res/icons/40/', 'icon_ext' => '.png', 'default_icon' => 'icon_item_box05', 'useitemlist' => 1),
				'allods' => array('icon_loc' => 'http://www.allodsdatabase.com/res/icons/40/', 'icon_ext' => '.png', 'default_icon' => 'not_yet_found', 'useitemlist' => 1)
			);
			$this->settings = array(
				'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
											'language' => 'pk_itt_icon_loc',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_loc'])) ? $g_settings[$this->config['game']]['icon_loc'] : ''),
				),
				'itt_icon_ext' => array(	'name' => 'itt_icon_ext',
											'language' => 'pk_itt_icon_ext',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_ext'])) ? $g_settings[$this->config['game']]['icon_ext'] : ''),
				),
				'itt_default_icon' => array('name' => 'itt_default_icon',
											'language' => 'pk_itt_default_icon',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['default_icon'])) ? $g_settings[$this->config['game']]['default_icon'] : ''),
				),
				'itt_useitemlist' => array('name' => 'itt_useitemlist',
											'language' => 'pk_itt_useitemlist',
											'fieldtype' => 'checkbox',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['useitemlist'])) ? $g_settings[$this->config['game']]['useitemlist'] : ''),
				)
			);
			$g_lang = array(
				'aion' => array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'jp' => 'ja_JP'),
				'allods' => array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}

		//initializes the item/recipelist. if it does not exists in the cache, get it from: http://www.aiondatabase.com/xml/en_US/items(recipes)/item(recipe)list.xml
		private function getItemlist($lang, $forceupdate=false, $type='item'){
			$this->{$type.'list'} = unserialize(file_get_contents($this->pfh->FilePath($this->config['game'].'_'.$lang.'_'.$type.'list.itt', 'itt_cache')));
			if(!$this->itemlist OR $forceupdate)
			{
				$urlitemlist = $this->puf->fetch('http://www.'.$this->config['game'].'database.com/xml/'.$this->av_langs[$lang].'/'.$type.'s/'.$type.'list.xml');
				$xml = simplexml_load_string($urlitemlist);
				foreach($xml->children() as $item) {
					$name = (string) $item['name'];
					$this->{$type.'list'}[(int)$item['id']][$lang] = $name;
				}
				$this->pfh->putContent($this->pfh->FilePath($this->config['game'].'_'.$lang.'_'.$type.'list.itt', 'itt_cache'), serialize($this->{$type.'list'}));
			}
			return true;
		}

		private function getItemIDfromItemlist($itemname, $lang, $forceupdate=false, $searchagain=0, $type='item'){
			$searchagain++;
			$this->getItemlist($lang,$forceupdate,$type);
			$item_id = array(0,0);

			//search in the itemlist for the name
			$loaded_item_langs = array();
			if($type == 'item') {
				foreach($this->itemlist as $itemID => $iteml){
					foreach($iteml as $slang => $name) {
						$loaded_item_langs[] = $slang;
						if($itemname == $name){
							$item_id[0] = $itemID;
							$item_id[1] = 'items';
							break 2;
						}
					}
				}
			}
			//search in the recipelist for the name
			$loaded_recipe_langs = array();
			if($type == 'recipe') {
				foreach($this->recipelist as $itemID => $iteml){
					foreach($iteml as $slang => $name) {
						$loaded_recipe_langs[] = $slang;
						if($itemname == $name)
						{
							$item_id[0] = $itemID;
							$item_id[1] = 'recipes';
							break 2;
						}
					}
				}
			}
			if(!$item_id[0] AND count($this->av_langs) > $searchagain) {
				$toload = array();
				foreach($this->av_langs as $c_lang => $langlong) {
					if(!in_array($c_lang,$loaded_item_langs)) {
						$toload[$c_lang][] = 'item';
					}
					if(!in_array($c_lang,$loaded_recipe_langs)) {
						$toload[$c_lang][] = 'recipe';
					}
				}
				foreach($toload as $lang => $load) {
					foreach($load as $type) {
						$item_id = $this->getItemIDfromItemlist($itemname, $lang, true, $searchagain, $type);
						if($item_id[0]) {
							break 2;
						}
					}
				}
			}
			return $item_id;
		}

		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$codedname = str_replace(' ', '%2B', $itemname);
	
			$data = $this->puf->fetch('http://'.(($lang == 'en') ? 'www' : $lang).'.'.$this->config['game'].'database.com/search?q='. $codedname);
			$this->searched_langs[] = $lang;
			if (preg_match_all('#\[\{\"tpl\":\"items\",\"n\":\"(.*?)\",\"id\":\"items\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
				if (preg_match_all('#\{\"id\":([0-9]*),\"n\":\"(.*?)\",\"l\":([0-9]*),\"p\":([0-9]*),\"r\":([0-9]*),\"i\":\"(.*?)\",\"cat\":\{(.*?)\}(.*?)\}#', $matchs[2][0], $matches)) {
					foreach ($matches[0] as $key => $match) {
						$item_name_tosearch = substr(html_entity_decode($matches[2][$key]),1);
						if (strcasecmp($item_name_tosearch, $itemname) == 0) {
							$item_id[0] = $matches[1][$key];
							$item_id[1] = 'items';
							break;
						}
					}
				}
			}
			if(!$item_id[0]) {
				if (preg_match_all('#\[\{\"tpl\":\"recipes\",\"n\":\"(.*?)\",\"id\":\"recipes\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
					if (preg_match_all('#\{\"id\":([0-9]*),\"sl\":([0-9]*),\"r\":([0-9]*),\"rn\":\"(.*?)\",\"p\":\{(.*?)},\"n\":\"(.*?)\",\"comp\":(.*?)\}#', $matchs[2][0], $matches)) {
						foreach ($matches[0] as $key => $match) {
							$item_name_tosearch = substr(html_entity_decode($matches[6][$key]),1);
							if (strcasecmp($item_name_tosearch, $itemname) == 0) {
								$item_id[0] = $matches[1][$key];
								$item_id[1] = 'recipes';
								break;
							}
						}
					}
				}
			}
			if(!$item_id AND count($this->av_langs) > $searchagain) {
				foreach($this->av_langs as $c_lang => $langlong) {
					if(!in_array($c_lang,$this->searched_langs)) {
						$item_id = $this->getItemIDfromUrl($itemname, $c_lang, $searchagain);
					}
					if($item_id[0]) {
						break;
					}
				}
			}
			return $item_id;
		}

		protected function searchItemID($itemname, $lang){
			if($this->config['useitemlist']) {
				return $this->getItemIDfromItemlist($itemname, $lang);
			} else {
				return $this->getItemIDfromUrl($itemname, $lang);
			}
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			settype($item_id, 'int');
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			$url = 'www.'.$this->config['game'].'database.com/xml/'.$this->av_langs[$lang];
			$item['link'] = $url."/".$type."/xmls/".$item['id'].".xml";
			//get the xml: http://www.aiondatabase.com/xml/$lang_code/items/xmls/$itemid.xml
			$itemxml = $this->puf->fetch($item['link'], array('Cookie: cookieLangId="'.$lang.'";'));
			$itemxml = simplexml_load_string($itemxml);

			$item['name'] = (!is_numeric($itemname) AND strlen($itemname) > 0) ? $itemname : trim($itemxml->name);

			//filter baditems
			if(!isset($itemxml->tooltip) OR strlen($itemxml->tooltip) < 5) {
				$item['baditem'] = true;
			}

			//build itemhtml
			$html = "<table class='db-tooltip' cellspacing='0'><tr><td class='normal'>";
			$html .= str_replace('"', "'", $itemxml->tooltip);
			$html .= "</td><td class='right'></td></tr><tr><td class='bottomleft'></td><td class='bottomright'></td></table>";
			$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/aion_popup.tpl'));
			$item['html'] = str_replace('{ITEM_HTML}', stripslashes($html), $template_html);
			$item['lang'] = $lang;
			$item['icon'] = (string)$itemxml->iconpath;
			$item['color'] = 'aion_q'.$itemxml->quality;
			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_xxxdatabase', xxxdatabase::$shortcuts);
?>