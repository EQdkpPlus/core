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

if(!class_exists('eq_zam')) {
	class eq_zam extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('eq');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'eq' => array('icon_loc' => 'http://everquest.allakhazam.com/pgfx/', 'icon_ext' => '.png', 'default_icon' => 'unknown'),
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
				'eq' => array('en' => 'en_US'),
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
			$encoded_name = urlencode($itemname);
			$link = 'http://everquest.allakhazam.com/search.html?q='.$encoded_name;
			
			$data = $this->puf->fetch($link);

			$this->searched_langs[] = $lang;
			if (preg_match_all('#\<a href=\"\/db\/item\.html\?item=(.*?)\">(.*?)\<\/a\>#', $data, $matches))
			{
				foreach ($matches[0] as $key => $match)
				{
					// Extract the item's ID from the match.
					$item_id = $matches[1][$key];
					$found_name = $matches[2][$key];

					if(strcasecmp($itemname, $found_name) == 0) {
						return array($item_id, 'items');
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

			$url = 'http://everquest.allakhazam.com/cluster/ihtml.pl?zamtt=1&item='.$item['id'];
			$item['link'] = $url;
			$itemdata = $this->puf->fetch($item['link'], array('Cookie: cookieLangId="'.$lang.'";'));

			if (preg_match('#zamTooltip\.store\({\"icon\":\"(.*?)\",\"site\":\"(.*?)\",\"html\":\"(.*?)\",\"dataType\":\"(.*?)\",\"name\":\"(.*?)\",\"id\":\"(.*?)\"#', $itemdata, $matches)){

				$content = stripslashes(str_replace('\n', '', $matches[3]));
				if (preg_match('#pgfx\/(.*?)\.png\"#',stripslashes($matches[1]), $icon_matches)){
					$icon = $icon_matches[1];
				}

				$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/eq_popup.tpl'));
				$template_html = str_replace('{ITEM_HTML}', $content, $template_html);
				$item['html'] = $template_html;
				$item['lang'] = $lang;
				$item['icon'] = $icon;
				$item['name'] = $matches[5];
			} else {
				$item['baditem'] = true;
			}
			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eq_zam', eq_zam::$shortcuts);
?>