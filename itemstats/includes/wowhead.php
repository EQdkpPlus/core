<?php

/*
 * ParseWowhead
 * started: 31/05/2007
 *
 * author: Frank Matheron
 * email: fenuzz@gmail.com
 * description: create itemstats tooltips using wowhead
 *
 * version: 0.3.2
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
	// Attempts to retrieve data for the specified item from Wowhead 
	function getItem($name)
    	{
		// Ignore blank names.
		$name = trim($name);
		if (empty($name)) { return null; }
		
		$item = array('name' => $name);

		// remove extra spaces (vB is known to add them)
		$fixed_name = implode(' ', preg_split ("/[\s\+]+/", urldecode(urldecode($name))));
	
		// encode the name so it can be used to build the url
		$encoded_name = urlencode(utf8_encode($fixed_name));
		$encoded_name = str_replace('+' , '%20' , $encoded_name);

		// Perform the search, and retrieve the result
		unset($xml_parser); // unset $xml_parser to prevent warnings on PHP4x
		$xml_parser = new XmlToArray();
		$xml_search_data = itemstats_read_url('http://www.wowhead.com/?search=' . $encoded_name . '&xml');
		$result = $xml_parser->parse($xml_search_data);

		// find the ITEMS section in the xml file, if its not there we did not find _anything_
		$items_idx = -1;
		$i = 0;
		foreach($result[0]['child'] as $category) {
			if ($category['name'] == 'ITEMS') {
				$items_idx = $i;
				break;
			}
			$i++;
		}

		// our search found one or more items
		if ($items_idx != -1) {
			$found_items = $result[0]['child'][$items_idx]['child'];
			$item_id = -1;
			
			// loop the found items
			foreach($found_items as $found_item) {
				$found_name = '';
				// loop the item properties
				foreach($found_item['child'] as $property) {
					// check for the name property
					if ($property['name'] == 'NAME') {
						$found_name = $property['data'];
						// we only want the name property, break loop
						break;
					}
				}
				// does the found name match the item name we are looking for?
				if (strcasecmp($found_name, $fixed_name) == 0) {
					// exact match found
					$item_id = $found_item['attr']['ID'];
					// we found our item so we can stop looping the results
					break;
				}
			}
			
			if ($item_id != -1) {
				// we found the item in the results, retrieve the item data using its item id
				return $this->getItemId($item_id, $name);
			}
		}
		
		unset($item['link']);
		return $item;
	}
	
	// Attempts to retrieve data for the specified item from Wowhead by its wowhead itemid
	function getItemId($item_id, $name = '')
	{
		$item = array('id' => $item_id);

		// retrieve the item data
		unset($xml_parser); // unset $xml_parser to prevent warnings on PHP4x
		$xml_parser = new XmlToArray();
		$xml_item_data = itemstats_read_url('http://www.wowhead.com/?item=' . $item_id . '&xml');
		$item_data = $xml_parser->parse($xml_item_data);

		if ($item_data[0]['child'][0]['name'] == 'ERROR') {
			// error, probably an invalid item id
			unset($item['link']);
			return $item;
		}
		// apparantly weve got valid item data
		
		// create an array of item properties
		$properties = array();
		foreach($item_data[0]['child'][0]['child'] as $property) {
			$properties[$property['name']]['data'] = $property['data'];
			$properties[$property['name']]['attr'] = $property['attr'];
		}

		// set item data
		if ($name != '') {
			// The name used to query this item, using this name as the tooltip id will enable to store items with too many spaces in the name. This should fix issues with long itemnames in vB.
			$item['name'] = $name;
		} else {
			// query by item id only, use the proper name.
			$item['name'] = $properties['NAME']['data'];
		}
		$item['lang'] = 'en';
		$item['link'] = $properties['LINK']['data']; // wowhead url to the item
		$item['icon'] = $properties['ICON']['data']; // icon filename without an extension

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
			$item['html'] = '<table><tr><td>' . $properties['HTMLTOOLTIP']['data'] . '</td></tr></table>';
		} else {
			$item['html'] = $properties['HTMLTOOLTIP']['data'];
		}
		// remove the width attributes from the tooltips, they mess the tooltip up in IE
		$item['html'] = str_replace(' width="100%"', '', $item['html']);
		// tooltip title/item name links to its wowhead page
		$item['html'] = str_replace($item['name'], '<a href=\'' . $item['link'] . '\' target=\'_new\'>' . $properties['NAME']['data'] . '</a>', $item['html']);
		// add escape slashes
		$item['html'] = str_replace('"', '\'', $item['html']);
		// place the tooltip content html into the tooltip template
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/' . WOWHEAD_TEMPLATE));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
		

		return $item;	
	}

	// downloads an icon
	function downloadIcon($iconname) {
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
	function close() {}
}

?>