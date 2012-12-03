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

if(!class_exists('lotro_allakhazam')) {
	class lotro_allakhazam extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher');

		public $supported_games = array('lotro');
		public $av_langs = array('en' => 'en_US');#, 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'jp' => 'ja_JP');

		public $settings = array(
			'itt_icon_loc' => array('name' => 'itt_icon_loc',
									'language' => 'pk_itt_icon_loc',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'http://lotro.allakhazam.com/images/icons/ItemIcons/'),
			'itt_icon_ext' => array('name' => 'itt_icon_ext',
									'language' => 'pk_itt_icon_ext',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => '.j'),
			'itt_default_icon' => array('name' => 'itt_default_icon',
										'language' => 'pk_itt_default_icon',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'unknown')
		);

		protected function u_construct() {}
		protected function u_destruct() {}

		protected function searchItemID($itemname, $lang){
			$encoded_name = urlencode($itemname);
			$link = 'http://lotro.allakhazam.com/search.html?q='.$encoded_name;
			$data = $this->puf->fetch($link);
			if (preg_match_all('#item\.html\?lotritem=(.*?)\" class=\"(.*?)\" id=\"(.*?)\">(.*?)\<\/a\>#', $data, $matches))
			{
				foreach ($matches[0] as $key => $match)
				{
					// Extract the item's ID from the match.
					$item_id = $matches[1][$key];
					$found_name = $matches[4][$key];

					if(strcasecmp($itemname, $found_name) == 0) {
						return array($item_id, 'items');
					}
				}
			}
			return false;
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){		
			if($item_id < 1) {
				return null;
			}
			$xml_link = 'http://lotro.allakhazam.com/cluster/item-xml.pl?lotritem='.$item_id;
			$xml_data = $this->puf->fetch($xml_link);
			$xml = simplexml_load_string($xml_data);

			//filter baditems
			if(!isset($xml->display_html) OR strlen($xml->display_html) < 5) {
				$item['baditem'] = true;
				return $item;
			}

			$item['link'] = 'http://lotro.allakhazam.com/db/item.html?lotritem='.$item_id;
			$item['id'] = 0; # dont store this id, since its an allkhazam internal id
			$item['name'] = (string) $xml->item_name;
			$item['icon'] = (string) $xml->icon;
			$item['lang'] = $lang;
			$item['color'] = 'item'.$xml->quality;
			$item['html'] = (string) $xml->display_html;
			$item['html'] = $item['html'];
			//reposition allakhazam-credit-stuff
			$alla_credit = '<br><span class="akznotice">Item display is courtesy <a href="http://lotro.allakhazam.com/">lotro.allakhazam.com</a>.</span>';
			$item['html'] = str_replace($alla_credit, "", $item['html']).$alla_credit;
			$item['html'] = str_replace('{ITEM_HTML}', $item['html'], file_get_contents($this->root_path.'infotooltip/includes/parser/templates/lotro_popup.tpl'));

			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_lotro_allakhazam', lotro_allakhazam::$shortcuts);
?>