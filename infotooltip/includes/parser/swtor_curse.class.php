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

if(!class_exists('swtor_curse')) {
	class swtor_curse extends itt_parser {

		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher');
		
		public $supported_games = array('swtor');
		public $av_langs = array('en' => 'en_US');#, 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'es' => 'es_ES', 'pl' => 'pl_PL');

		public $settings = array(
			'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
										'language' => 'pk_itt_icon_loc',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'http://db.darthhater.com/icons/l/',
			),
			'itt_icon_ext' => array(	'name' => 'itt_icon_ext',
										'language' => 'pk_itt_icon_ext',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => '.gif',
			),
			'itt_default_icon' => array('name' => 'itt_default_icon',
										'language' => 'pk_itt_default_icon',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'not_yet_found',
			)
		);

		private $searched_langs = array();
		
		public function u_construct() {}

		public function __destruct() {
			unset($this->searched_langs);
			parent::__destruct();
		}

		protected function searchItemID($itemname, $lang, $searchagain=0) {
			$searchagain++;
			$this->pdl->log('infotooltip', 'swtor_curse->searchItemID called: itemname: '.$itemname.', lang: '.$lang.', searchagain: '.$searchagain);

			$name = trim($itemname);
			if(empty($name)) return null;
			$name = urlencode($name);
			$name = str_replace('+', '%20', $name);
			$url = "http://db.darthhater.com/search.aspx?search_text=".$name;
			$this->pdl->log('infotooltip', 'Search for Item-ID at '.$url);
			$result = $this->puf->fetch($url);
			$item_id = 0;

			preg_match_all('~<script type="text\/javascript">(.*?)cg_items\.addData\((.*?)\);(.*?)<\/script>~s', $result, $matches);
			if(!empty($matches[2][0])) {
				preg_match_all('~id:([0-9]{4,5}),~', $matches[2][0], $ids);
				foreach($ids[1] as $key => $id) {
					$url = 'http://db.darthhater.com/ExTooltips.aspx?id='.$id.'&type=1';
					$content = $this->convert_utf16($this->puf->fetch($url));
					$name = $this->get_name_from_tt($content);
					if($name == $itemname) {
						$item_id = $id;
						break;
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
							return $this->searchItemID($itemname, $slang, $searchagain);
						}
					}
				}
			}
			$debug_out = $item_id > 0 ? 'Item-ID found: '.$item_id : 'No Item-ID found';
			$this->pdl->log('infotooltip', $debug_out);
			return array($item_id, 'items');
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			//look up url: http://db.darthhater.com/ExTooltips.aspx?id={id}&type=1
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			$url = 'http://db.darthhater.com/ExTooltips.aspx?id='.$item_id.'&type=1';
			$this->pdl->log('infotooltip', 'fetch item-data from '.$url);
			$content = $this->convert_utf16($this->puf->fetch($url));
			$content = substr($content, 31,-2);
			preg_match_all("~tooltip:\'(.*?)\',rarity:([0-9]+),icon:([0-9]+)\}$~", $content, $matches);

			$item['name'] = ($itemname) ? $itemname : $this->get_name_from_tt($matches[1][0]);
			$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/swtor_popup.tpl'));
			$item['html'] = str_replace('{ITEM_HTML}', stripslashes($matches[1][0]), $template_html);
			$item['link'] = "http://db.darthhater.com/items/".$item_id."/";
			$item['icon'] = $matches[3][0];
			$item['color'] = $matches[2][0];
			$item['lang'] = $lang;

			return $item;
		}
		
		private function get_name_from_tt($string) {
			preg_match_all('~<span class=\\\"r[0-9] item\-name\\\">(.*?)<\/span>~', $string, $name_match);
			return stripslashes($name_match[1][0]);
		}
		
		private function convert_utf16($string) {
			if(function_exists('mb_convert_encoding')) {
				$string = mb_convert_encoding($string, 'UTF-8', 'UTF-16LE');
			} else {
				$string = $this->utf16_to_utf8($string);
			}
			return $string;
		}
		
		private function utf16_to_utf8($str) {
			$c0 = ord($str[0]);
			$c1 = ord($str[1]);
		 
			if ($c0 == 0xFE && $c1 == 0xFF) {
				$be = true;
			} else if ($c0 == 0xFF && $c1 == 0xFE) {
				$be = false;
			} else {
				return $str;
			}
		 
			$str = substr($str, 2);
			$len = strlen($str);
			$dec = '';
			for ($i = 0; $i < $len; $i += 2) {
				$c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) : 
						ord($str[$i + 1]) << 8 | ord($str[$i]);
				if ($c >= 0x0001 && $c <= 0x007F) {
					$dec .= chr($c);
				} else if ($c > 0x07FF) {
					$dec .= chr(0xE0 | (($c >> 12) & 0x0F));
					$dec .= chr(0x80 | (($c >>  6) & 0x3F));
					$dec .= chr(0x80 | (($c >>  0) & 0x3F));
				} else {
					$dec .= chr(0xC0 | (($c >>  6) & 0x1F));
					$dec .= chr(0x80 | (($c >>  0) & 0x3F));
				}
			}
			return $dec;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_swtor_curse', swtor_curse::$shortcuts);
?>