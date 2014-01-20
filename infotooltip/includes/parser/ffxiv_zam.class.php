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

if(!class_exists('ffxiv_zam')) {
	class ffxiv_zam extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('ffxiv');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'ffxiv' => array('icon_loc' => 'http:', 'icon_ext' => '', 'default_icon' => 'unknown'),
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
			);
			$g_lang = array(
				'ffxiv' => array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}
		
		private function getLangID($strLang){
			$arrLang = array(
				'en' => 1,
				'de' => 2,
				'fr' => 3,
			);
			return $arrLang[$strLang];
		}


		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$encoded_name = urlencode($itemname);
			if (!$lang) $lang = "en";
			$link = "http://xivdb.com/modules/search/search.php?query=".$encoded_name."&page=1&pagearray=%7B%7D&language=".$this->getLangID($lang)."&filters=null&showview=0";			
			$data = $this->puf->fetch($link);
			$item_id = false;
			

			$this->searched_langs[] = $lang;
			if (preg_match_all('#href\=\"\?item\/(.*?)\/(.*?)\">(.*?)<\/a>#', $data, $matches))
			{				
				foreach ($matches[0] as $key => $match)
				{
					// Extract the item's ID from the match.
					$item_id = (int)$matches[1][$key];
					$found_name = strip_tags($matches[3][$key]);
					
					if(strcasecmp($itemname, $found_name) == 0) {
						return array($item_id, 'items');
					}
				}
			}
			
			//search in other languages
			if(!$item_id AND $searchagain < count($this->av_langs)) {
				$this->pdl->log('infotooltip', 'No Items found.');
				if(count($this->config['lang_prio']) >= $searchagain) {
					$this->pdl->log('infotooltip', 'Search again in other language.');
					$this->searched_langs[] = $lang;
					foreach($this->config['lang_prio'] as $slang) {
						if(!in_array($slang, $this->searched_langs)) {
							return $this->getItemIDfromUrl($itemname, $slang, $searchagain);
						}
					}
				}
			}
			
			return $item_id;
		}

		protected function searchItemID($itemname, $lang){
			return $this->getItemIDfromUrl($itemname, $lang);
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			
			$url = "http://xivdb.com/modules/fpop/fpop.php?callback=&lang=".$this->getLangID($lang)."&version=1.6&host=xivdb.com&type=item&id=".$item_id."&location=http%3A%2F%2Fxivdb.com%2F%3Ftooltip&convertQuotes=true&frameShadow=false&compact=false&statsOnly=false&replaceName=true&colorName=true&showIcon=true&_=1387608577170";
			$item['link'] = $url;
			$itemdata = $this->puf->fetch($item['link']);
			//$itemdata = substr(trim($itemdata), 1);
			//$itemdata = substr($itemdata, 0, -1);			
			$arrData = json_decode($itemdata);
			
			
			$strItemName = trim(strip_tags($arrData->name));

			if ($strItemName != ""){
				$item['icon'] = $arrData->icon;
				$item['color'] = $arrData->color;

				$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/ffxiv_popup.tpl'));
				$template_html = str_replace('{ITEM_HTML}', $arrData->html, $template_html);
				$item['html'] = $template_html;
				$item['lang'] = $lang;
				$item['name'] = $strItemName;
				
				
			} else {
				$item['baditem'] = true;
				
			}

			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ffxiv_zam', ffxiv_zam::$shortcuts);
?>