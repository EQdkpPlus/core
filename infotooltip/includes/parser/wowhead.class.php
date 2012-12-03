<?php
 /*
 * Project:     EQdkp-Plus Infotooltip
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2008-12-02 11:54:02 +0100 (Di, 02 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 3293 $
 *
 * $Id:   $
 */

include('itt_parser.aclass.php');

if(!class_exists('wowhead')) {
  class wowhead extends itt_parser {
	public $supported_games = array('wow');
    public $av_langs = array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'es' => 'es_ES');

    public $settings = array(
		'itt_icon_loc' => array('name' => 'itt_icon_loc',
								'language' => 'pk_itt_icon_loc',
								'fieldtype' => 'text',
								'size' => false,
								'options' => false,
								'default' => 'http://static.wowhead.com/images/wow/icons/large/'),
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

	private $searched_langs = array();

	protected function u_construct() {}

	protected function u_destruct()
	{
		unset($this->searched_langs);
	}

	protected function searchItemID($itemname, $lang, $searchagain=0) {
		$searchagain++;
		$item_id = 0;
		
		// Ignore blank names.
		$name = trim($itemname);
		if (empty($name)) { return null; }

		$item = array('name' => $name);

		$encoded_name = urlencode($item['name']);
		$encoded_name = str_replace('+' , '%20' , $encoded_name);
		$url = ($lang == 'en') ? 'www' : $lang;
		$item_data = $this->urlreader->GetURL('http://'.$url.'.wowhead.com/item='.$encoded_name.'&xml');
		$xml = simplexml_load_string($item_data);
		if(is_object($xml)) {
			$item_id = (int) $xml->item->attributes('id');
		}
		//search in other languages
		if(!$item_id AND $searchagain < count($this->av_langs)) {
			foreach($this->av_langs as $slang)
			{
				if(!in_array($slang, $this->av_langs)) {
					$item_id = $this->searchItemID($itemname, $slang, $searchagain);
				}
			}
		}
		return array($item_id, 'items');
	}

	protected function getItemData($item_id, $lang, $itemname='', $type='items')
	{
        settype($item_id, 'int');
		if(!$item_id) {
			$item['baditem'] = true;
			return $item;
		}
		$item = array('id' => $item_id);
		$url = ($lang == 'en') ? 'www' : $lang;
		$item['link'] = $url.'.wowhead.com/item='.$item['id'].'&xml';
		$itemxml = $this->urlreader->GetURL($item['link'], $lang);
		$itemxml = simplexml_load_string($itemxml);

		$item['name'] = (!is_numeric($itemname) AND strlen($itemname) > 0) ? $itemname : trim($itemxml->item->name);

		//filter baditems
		if(!isset($itemxml->item->htmlTooltip) OR strlen($itemxml->item->htmlTooltip) < 5) {
			$item['baditem'] = true;
			return $item;
		}

		//build itemhtml
		$html = str_replace('"', "'", $itemxml->item->htmlTooltip);
		$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/wow_popup.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', stripslashes($html), $template_html);
		$item['lang'] = $lang;
		$item['icon'] = (string) strtolower($itemxml->item->icon);
		$item['color'] = 'q'.$this->convert_color((string) $itemxml->item->quality);
		return $item;
	}
	
	/*
	 * Translate Old-Colors to new css-classes
	 */
	private function convert_color($color) {
		if(is_numeric($color)) return $color;
		$color_array = array(
			'Verbreitet' => 1,
			'Common' => 1,
			'Común' => 1,
			'Classique' => 1,
			'Обычный' => 1,
			'Selten' => 2,
			'Uncommon' => 2,
			'Bonne' => 2,
			'Poco Común' => 2,
			'Необычный' => 2,
			'Rar' => 3,
			'Rare' => 3,
			'Raro' => 3,
			'Редкий' => 3,
			'Episch' => 4,
			'Epic' => 4,
			'Épica' => 4,
			'Épique' => 4,
			'Эпический' => 4,
			'Legendär' => 5,
			'Legendary' => 5,
			'Légendaire' => 5,
			'Legendaria' => 5,
			'Легендарный' => 5
		);
		return $color_array[$color];
	}
  }
}