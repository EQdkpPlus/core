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

if(!class_exists('wildstar_wildheap')) {
	class wildstar_wildheap extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));

		public $supported_games = array('wildstar');
		public $av_langs = array();

		public $settings = array();

		public $itemlist = array();
		public $recipelist = array();

		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'wildstar' => array('icon_loc' => '', 'icon_ext' => '', 'default_icon' => 'unknown.png'),
			);
			$this->settings = array(
				/*'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
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
				),*/
				'itt_default_icon' => array('name' => 'itt_default_icon',
											'language' => 'pk_itt_default_icon',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['default_icon'])) ? $g_settings[$this->config['game']]['default_icon'] : ''),
				),
			);
			$g_lang = array(
				'wildstar' => array('en' => 'en', 'de' => 'de', 'fr' => 'fr'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}


		private function getItemIDfromUrl($itemname, $lang){
			$encoded_name			= urlencode($itemname);
			$link					= 'http://wildheap.com/en/complete?q='.$encoded_name;
			$data					= json_decode($this->puf->fetch($link), true);
			$itemID					= $data[0]['id'];
			$this->searched_langs[]	= $lang;
			return array($itemID);
		}

		protected function searchItemID($itemname, $lang){
			return $this->getItemIDfromUrl($itemname, $lang);
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$item			= array('id' => $item_id);
			if(!$item_id) return null;

			$url			= 'http://wildheap.com/'.$lang.'/item/t/'.$item['id'];
			$item['link']	= $url;
			
			$data = json_decode($this->puf->fetch($url), true);
			if(isset($data['body'])){
				$content		= $data['body'];
				$template_html	= trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/wildstar_popup.tpl'));
				$template_html	= str_replace('{ITEM_HTML}', $content, $template_html);
				$item['html']	= $template_html;
				$item['lang']	= $lang;
				$item['icon']	= $data['icon'];
				$item['color']	= $data['quality'];
				$item['name']	= $data['name'];
			}else{
				$item['baditem'] = true;
			}
			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wildstar_wildheap', wildstar_wildheap::$shortcuts);
?>