<?php
/*	Project:	EQdkp-Plus
 *	Package:	Star Wars - the old republic game package
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
 */

if(!class_exists('torhead')) {
	class torhead extends itt_parser {
		public static $shortcuts = array('puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));
		public $supported_games = array('swtor');
		public $av_langs = array();
		public $settings = array();
		public $itemlist = array();
		public $recipelist = array();
		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'swtor' => array('icon_loc' => 'http://tor.zamimg.com/torhead/images/icons/backgrounds/swtor/large/', 'icon_ext' => '.jpg', 'default_icon' => 'unknown'),
			);
			$this->settings = array(
				'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
											'language' => 'pk_itt_icon_loc',
											'type' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_loc'])) ? $g_settings[$this->config['game']]['icon_loc'] : ''),
				),
				'itt_icon_ext' => array(	'name' => 'itt_icon_ext',
											'language' => 'pk_itt_icon_ext',
											'type' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_ext'])) ? $g_settings[$this->config['game']]['icon_ext'] : ''),
				),
				'itt_default_icon' => array('name' => 'itt_default_icon',
											'language' => 'pk_itt_default_icon',
											'type' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['default_icon'])) ? $g_settings[$this->config['game']]['default_icon'] : ''),
				),
			);
			$g_lang = array(
				'swtor' => array('en' => 'en_US'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}

		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$encoded_name = str_replace(' ' , '+' , $itemname);
			$data = $this->puf->fetch('http://www.torhead.com/search/'. $encoded_name);
			$this->searched_langs[] = $lang;
			
			//Check for direct hit
			if (preg_match('#href=\"(.*)\/item\/(.*?)\/(.*?)\" rel="canonical" >#', $data, $matches)){
				$item_id[0] = $matches[2];
				$item_id[1] = 'items';
				return $item_id;
			}
			
			//Search page
			if (preg_match_all('#href=\"\/item\/(.*?)\/(.*?)\" class="(.*?)">(.*?)<\/a>#', $data, $matches)) {
				foreach ($matches[0] as $key => $match) {
					if (strcasecmp($matches[4][$key], $itemname) == 0) {
						$item_id[0] = $matches[1][$key];
						$item_id[1] = 'items';
						break;
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

		protected function searchItemID($itemname, $lang){ return $this->getItemIDfromUrl($itemname, $lang); }

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			$url = 'http://www.torhead.com/item/'.$item['id'].'/tooltips';
			$item['link'] = $url;
			$itemdata = $this->puf->fetch($item['link'], array('Cookie: cookieLangId="'.$lang.'";'));
			if (preg_match('#fhTooltip\.store\(\"(.*?)\", \"(.*?)\", \"(.*?)\", \"(.*?)\", \"(.*?)\", \"(.*?)\"#', $itemdata, $matches)){
				$quality = $matches[4];
				$pass1 = (str_replace('\n', '', $matches[3]));
				$pass2 = (str_replace('\r', '', $pass1));
				$pass3 = (str_replace('href=\"\/item\/', 'href=\"http:\/\/www.torhead.com\/item\/', $pass2));
				$content = stripslashes($pass3);
				if (preg_match('#\|small\|(.*?).jpg\)#',str_replace('\\/', '|', $matches[5]), $icon_matches)){
					$icon = $icon_matches[1];
				}
				$template_html = trim(file_get_contents($this->root_path.'games/swtor/infotooltip/templates/torhead_popup.tpl'));
				$template_html = str_replace('{ITEM_HTML}', $content, $template_html);
				$item['html'] = $template_html;
				$item['lang'] = $lang;
				$item['icon'] = $icon;
				$item['color'] = $quality;
				$item['name'] = $matches[6];
			} else { $item['baditem'] = true; }
			return $item;
		}
		
	}
}
?>