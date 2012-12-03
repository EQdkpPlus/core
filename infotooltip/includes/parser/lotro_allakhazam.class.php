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

if(!class_exists('lotro_allakhazam')) {
  class lotro_allakhazam extends itt_parser {
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
								'default' => ''),
		'itt_default_icon' => array('name' => 'itt_default_icon',
									'language' => 'pk_itt_default_icon',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'unknown')
	);

	protected function u_construct() {}
	protected function u_destruct() {}

	protected function searchItemID($itemname, $lang)
	{
		$encoded_name = urlencode($itemname);
		$link = 'http://lotro.allakhazam.com/search.html?q='.$encoded_name;
		$data = $this->urlreader->GetURL($link);
		if (preg_match_all('#item\.html\?lotritem=(.*?)\" class=\"(.*?)\" id=\"(.*?)\" \>\<img (.*?)\/\>(.*?)\<\/a\>#', $data, $matches))
		{
			foreach ($matches[0] as $key => $match)
			{
				// Extract the item's ID from the match.
				$item_id = $matches[1][$key];
				$found_name = $matches[5][$key];

				if(strcasecmp($itemname, $found_name) == 0) {
					return array($item_id, 'items');
				}
			}
		}
		return false;
	}

	protected function getItemData($item_id, $lang, $itemname='', $type='items')
	{
		if($item_id < 1) {
			return $itemname;
		}
        $xml_link = 'http://lotro.allakhazam.com/cluster/item-xml.pl?lotritem='.$item_id;
        $xml_data = $this->urlreader->GetURL($xml_link);
        $xml = simplexml_load_string($xml_data);
        
        //filter baditems
		if(!isset($xml->display_html) OR strlen($xml->display_html) < 5) {
			$item['baditem'] = true;
		}

        $item['link'] = 'http://lotro.allakhazam.com/db/item.html?lotritem='.$item_id;
		$item['id'] = (int) $xml->item_id;
		$item['name'] = (string) $xml->item_name;
		$item['icon'] = (string) $xml->icon;
		$item['lang'] = $lang;
		$item['color'] = 'item'.$xml->quality;
		$item['html'] = (string) $xml->display_html;
		$item['html'] = $item['html'];
		//reposition allakhazam-credit-stuff
		$alla_credit = '<br/><span class="akznotice">Item display is courtesy <a href="http://lotro.allakhazam.com/">lotro.allakhazam.com</a>.</span>';
		$item['html'] = str_replace($alla_credit, "", $item['html']).$alla_credit;
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], file_get_contents($this->root_path.'infotooltip/includes/parser/templates/lotro_popup.tpl'));

		return $item;
	}
  }
}