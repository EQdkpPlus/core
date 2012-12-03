<?php



include_once(dirname(__FILE__) . '/xmlhelper.php');

include_once(dirname(__FILE__) . '/urlreader.php');



define('ICON_LINK_PLACEHOLDER', '{ITEM_ICON_LINK}');

define('DEFAULT_ICON', 'INV_Misc_QuestionMark');



// The main interface to the Allakhazam

class InfoSite2

{

	var $xml_helper;



	// Constructor

	function InfoSite2()

	{

		$this->xml_helper = new XmlHelper();

	}



	// Cleans up resources used by this object.

	function close()

	{

		$this->xml_helper->close();

	}



	// Attempts to retrieve data for the specified item from Allakhazam.

	function getItem($name)

	{

		// Ignore blank names.

		$name = trim($name);

		if (empty($name))

		{

			return null;

		}



		$item = array('name' => $name);



		// Perform the search.

		$data = itemstats_read_url('http://wow.allakhazam.com/search.html?q=' . urlencode($name));



		// Look for a name match in the search result.

		if (preg_match_all('#images/icons/(.*?)\.png(.*?)witem=(.*?)">#s', $data, $matches))

		{

			foreach ($matches[0] as $key => $match)

			{

				// Extract the item's ID from the match.

				$item_id = $matches[3][$key];



				// Retrieve the XML for this item from Allakhazam.

				$xml_data = itemstats_read_url('http://wow.allakhazam.com/dev/wow/item-xml.pl?witem=' . $item_id);



				// Parse out the name of this item from the XML and check if we have a match

				$xml_name = $this->xml_helper->parse($xml_data, 'name1');

				if (strcasecmp($item['name'], $xml_name) == 0)

				{

					// If we have a match, grab additional information about this item and break out of this loop.

					$item['name'] = $xml_name;
					$item['item_id'] = $item_id;

					$item['icon'] = $matches[1][$key];

					$item['link'] = 'http://wow.allakhazam.com/db/item.html?witem=' . $item_id;



					$item_found = true;

					break;

				}

			}

		}



		// If a match was found, retrieve additional info about it.

		if ($item_found)

		{

			// Parse out the display html of this item from the XML

			$item['html'] = $this->xml_helper->parse($xml_data, 'display_html');



			// Fix up the html a bit.

			$item['html'] = str_replace("</span></span><br /><span class='wowrttxt'>", "</span></span>\n<br /><span class='wowrttxt'>", $item['html']);

			$item['html'] = str_replace('"', '\'', $item['html']);

			$item['html'] = preg_replace('#(<[ /]*br[ /]*>)<[ /]*br[ /]*>#', '$1', $item['html']);

			$item['html'] = str_replace("<a href='http://wow.allakhazam.com/db/itemset", "<br><a class='set' href='http://wow.allakhazam.com/db/itemset", $item['html']);

			$item['html'] = str_replace("<a", "<a target='_new'", $item['html']);

			$item['html'] = preg_replace("#<a ([^>]*) class='itemcreatelink'>#", "<br><span class='itemeffectlink'>Creates: </span>\\0", $item['html']);

			$item['html'] = str_replace('/images/bluesocket.png', "http://wow.allakhazam.com/images/bluesocket.png", $item['html']);
            $item['html'] = str_replace('/images/redsocket.png',  "http://wow.allakhazam.com/images/redsocket.png", $item['html']);
            $item['html'] = str_replace('/images/yellowsocket.png', "http://wow.allakhazam.com/images/yellowsocket.png", $item['html']);
            $item['html'] = str_replace('/images/metasocket.png', "http://wow.allakhazam.com/images/metasocket.png", $item['html']);

			$item['html'] = str_replace('(', "", $item['html']);
			$item['html'] = str_replace(')', "", $item['html']);


			// Extract the item color from the HTML.

			preg_match_all("#<span class='(.*?)'>#s", $item['html'], $matches);

			foreach ($matches[1] as $match) {

				if ($match!="iname" && $match!="") {

					$item['color'] = $match;

					break;

				}

			}

			// If this is a set, grab the set bonuses and add it to the html.

			$item_set_id = $this->xml_helper->parse($xml_data, 'setid');

			if (!empty($item_set_id) && ($item_set_id != '0'))

			{

				// Read the item set page.

				$data = itemstats_read_url('http://wow.allakhazam.com/db/itemset.html?setid=' . $item_set_id);



				// Extract the set bonus html from this page.

				preg_match('#Set Bonuses:</div>(.*?)<br/><div class#s', $data, $match);

				$item_set_bonuses = $match[1];



				// Fix up the html a bit

				$item_set_bonuses = str_replace('/db/spell.html', 'http://wow.allakhazam.com/db/spell.html', $item_set_bonuses);

				$item_set_bonuses = str_replace("<a", "<a class='setbonus' target='_new'", $item_set_bonuses);

				$item_set_bonuses = str_replace('"', '\'', $item_set_bonuses);

				$item_set_bonuses = preg_replace('#<[ /]*br[ /]*>$#','',$item_set_bonuses);

				$item_set_bonuses = "<span class='setbonus'>" . $item_set_bonuses . "</span>";



				// Insert the set bonus text into the display html;

				$item['html'] = preg_replace('#setid=(.*?)</span></a>#s', '\\0' . $item_set_bonuses, $item['html']);

			}



			// Build the final HTML by merging the template and the data we just prepared.

			$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));

			$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

		}

		else

		{

			// If Allakhazam was busy or this item doesn't exist and this item isn't cached yet, create some error html.

			$item['color'] = 'greyname';

			$item['icon'] = DEFAULT_ICON;



			// Read the template html for an item.

			$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup-error.tpl'));

			$item['html'] = str_replace('{INFO_SITE}', 'Allakhazam', $template_html);

		}



		return $item;

	}

}

?>