<?php

/*
 * ParseWowhead
 * started: 31/05/2007
 *
 * author: Frank Matheron
 * email: fenuzz@gmail.com
 * description: create itemstats tooltips using wowhead
 *
 * version: 0.3.9
 *
 */

include_once(dirname(__FILE__) . '/../config.php');
include_once(dirname(__FILE__) . '/../config_wowhead.php');
include_once(dirname(__FILE__) . '/xmltoarray.inc.php');
include_once(dirname(__FILE__) . '/urlreader.php');
include_once(dirname(__FILE__) . '/download_file.php');

// The main interface to the Wowhead parser
class ParseWowhead
{
	var $urlprefix = 'www';

	function ParseWowhead()
	{
		global $eqdkp;

        if($eqdkp->config['game_language'] != 'en')
        {
            $this->urlprefix = $eqdkp->config['game_language'];
        }
    }

	// Attempts to retrieve data for the specified item from Wowhead
	function getItem($name)
    {
		// Ignore blank names.
		$name = trim($name);
		if (empty($name)) { return null; }

		$item = array('name' => $name);

		// remove extra spaces (vB is known to add them)
		$fixed_name =  urldecode(urldecode($name));#));implode(' ', preg_split ("/[\s\+]+/",

		// encode the name so it can be used to build the url
		$encoded_name = urlencode(utf8_encode($fixed_name));
		$encoded_name = str_replace('+' , '%20' , $encoded_name);
		//search for the itemid in en, de, fr, es
		$langs = array('www', 'de', 'fr', 'es');
		foreach($langs as $lang)
		{
			if($item_data = $this->getItemData('http://'.$lang.'.wowhead.com/?item='.$encoded_name.'&xml'))
			{
				$item_id = $item_data[0]['child'][0]['attr']['ID'];
			}
		}
		if($item_id)
		{
			return $this->getItemId($item_id, $name);
		}
		else
		{
			return array();
		}
	}

	// Attempts to retrieve data for the specified item from Wowhead by its wowhead itemid
	function getItemId($item_id, $item_name='')
	{
		$item_url = 'http://'.$this->urlprefix.'.wowhead.com/?item=' . $item_id . '&xml';
		return $this->buildTooltip($this->getItemData($item_url), $item_name);
	}

	/**
	 * Parses the XML representation of the item data
	 *
	 * $item_url = the URL to the item data (XML).
	 */
	function getItemData($item_url)
	{
		// retrieve the item data
		unset($xml_parser); // unset $xml_parser to prevent warnings on PHP4x
		$xml_parser = new XmlToArray();
		$xml_item_data = itemstats_read_url($item_url);

		$item_data = $xml_parser->parse($xml_item_data);

		if ($item_data[0]['child'][0]['name'] == 'ERROR') {
			return false;
		}
		// apparantly weve got valid item data

		return $item_data;
	}

	/**
	 * Builds the tooltip using the parsed XML data.
	 *
	 * $item_data = parsed XML item data.
	 */
	function buildTooltip($item_data, $item_name='')
	{
		$item = array();

		if (!$item_data) {
			unset($item['link']);
			return $item;
		}

		// create an array of item properties
		$properties = array();
		foreach($item_data[0]['child'][0]['child'] as $property) {
			$properties[$property['name']]['data'] = $property['data'];
			$properties[$property['name']]['attr'] = $property['attr'];
		}

		// set item data
		$item['id'] = $item_data[0]['child'][0]['attr']['ID'];
		$item['name'] = ($item_name) ? $item_name : $properties['NAME']['data'];
		$item['lang'] = 'en';
		$item['link'] = $properties['LINK']['data']; // wowhead url to the item
		$item['icon'] = strtolower($properties['ICON']['data']); // icon filename without an extension

		// if download icons is enabled, download the icon
		if (DOWNLOAD_ICONS) {
			if (!$this->downloadIcon($item['icon'])) {
				// failed to download the icon, use default
				$item['icon'] = DEFAULT_ICON;
			}
		}

		// set the item color based on the item quality
		switch ($properties['QUALITY']['attr']['ID']) {
			case 0:
				$item['color'] = 'greyname';
				break;
			case 1:
				$item['color'] = 'whitename';
				break;
			case 2:
				$item['color'] = 'greenname';
				break;
			case 3:
				$item['color'] = 'bluename';
				break;
			case 4:
				$item['color'] = 'purplename';
				break;
			case 5:
				$item['color'] = 'orangename';
				break;
			case 6:
				$item['color'] = 'redname';
				break;
			default:
				$item['color'] = 'greyname';
				break;
		}

		// create the tooltip html
		if (substr($properties['HTMLTOOLTIP']['data'], 0, 7) != '<table>') {
			$item['html'] = '<table><tr><td>' . utf8_decode($properties['HTMLTOOLTIP']['data']) . '</td></tr></table>';
		} else {
			$item['html'] = utf8_decode($properties['HTMLTOOLTIP']['data']);
		}
		// remove tooltips from the tooltip -_-
		$item['html'] = preg_replace('/<span class="q2 tip" onmouseover=".+?".+?>(.+?)<\/span>/', '<span class="q2">\\1</span>', $item['html']);
		// remove the width attributes from the tooltips, they mess the tooltip up in IE
		$item['html'] = str_replace(' width="100%"', '', $item['html']);
		// tooltip title/item name links to its wowhead page
		$item['html'] = str_replace($item['name'], '<a href=\'' . $item['link'] . '\' target=\'_new\'>' . utf8_decode($properties['NAME']['data']) . '</a>', $item['html']);
		// add escape slashes
		$item['html'] = str_replace('"', '\'', $item['html']);
		// place the tooltip content html into the tooltip template
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/' . WOWHEAD_TEMPLATE));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

		return $item;
	}

	// downloads an icon
	function downloadIcon($iconname)
	{
		if (DOWNLOAD_ICONS) {
			if (file_exists(LOCAL_ICON_STORE_PATH . $iconname . ICON_EXTENSION)) {
				// file already exists, dont download
				return true; // return true, the icon is ready to use
			} else {
				// the icon is not available, attempt to download
				return download_file(REMOTE_ICON_STORE_PATH . $iconname . ICON_EXTENSION, LOCAL_ICON_STORE_PATH . $iconname . ICON_EXTENSION);
			}
		}
		return false;
	}

	// Cleans up resources used by this object.
	function close()
	{
		unset($this->urlprefix);
	}
}

?>
