<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-30 16:51:43 +0100 (Mi, 30 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12953 $
 * 
 * $Id: rift_magelo.class.php 12953 2013-01-30 15:51:43Z wallenium $
 */

if(!class_exists('rift_magelo')) {
	class rift_magelo extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('rift');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'rift' => array('icon_loc' => 'http://www.magelocdn.com/images/rift/icons/32/', 'icon_ext' => '.jpg', 'default_icon' => 'unknown'),
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
				'rift' => array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}

/*
	http://rift.magelo.com/de/tooltip.json?callback=jsonp_item_8CCEC3BB0201010101_de&item=8CCEC3BB0201010101
	http://www.magelocdn.com/pack/rift/en/magelo-bar.js
	http://rift.magelo.com/de/item/ADF7B6AB0701010E28
	http://eqdkp-plus.eu/bugtracker/index.php?page=BugView&bugID=488

*/
		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$codedname = urlencode($itemname);
			$data = $this->puf->fetch('http://rift.magelo.com/'.$lang.'/items?q='. $codedname);
			$this->searched_langs[] = $lang;

			 if (preg_match_all('#return\[\[\"(.*?)\",\"rift:item:(.*?)\",\"\/de\/item\/(.*?)\/(.*?)\",\"(.*?)\"#', $data, $matches))
            {
				foreach ($matches[0] as $key => $match) {
					if (strcasecmp($matches[1][$key], $itemname) == 0) {
						$item_id[0] = $matches[2][$key];
						$item_id[1] = 'items';
						break;
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
			if ($type == 'items') $type = 'item';
			
			
			$url = 'http://www.rift_magelo.com/res/tooltip/'.$this->av_langs[$lang].'/20111212/'.$type.'/js/'.$item['id'].'.js';
			$url = 'http://rift.magelo.com/'.$lang.'/tooltip.json?callback=jsonp_item_'.$item_id.'_'.$lang.'&item='.$item_id;
			$item['link'] = $url;

			$itemdata = $this->puf->fetch($item['link'], array('Cookie: cookieLangId="'.$lang.'";'));

			if (preg_match('#name:\"(.*?)\", icon:\"(.*?)\", rarity:(.*?), slots:(.*?), stats:(.*?), tooltip:\"(.*?)\" \}#', $itemdata, $matches)){			
				$quality = $matches[3];
				$content = str_replace("\\r\\n", "", $matches[6]);
				$content = str_replace("\\t", "", $content);
				$content = str_replace('\\"', '"', $content);
				$content = str_replace('\\>', '>', $content);
				
				$icon = $matches[2];
				
				$template_html = trim(file_get_contents($this->root_path.'games/rift/infotooltip/templates/rift_magelo.tpl'));
				$template_html = str_replace('{ITEM_HTML}', $content, $template_html);
				$template_html = str_replace('{ITEM_ICON}', 'http://www.magelocdn.com/images/rift/icons/48/'.$icon.'.jpg', $template_html);
				$item['html'] = $template_html;
				$item['lang'] = $lang;
				$item['icon'] = $icon;
				$item['color'] = 'rarity'.$quality;
				$item['name'] = $matches[1];
			} else {
				$item['baditem'] = true;
			}
			return $item;
		}
	}
}
?>