<?php

include_once(dirname(__FILE__) . '/xmlhelper.php');
include_once(dirname(__FILE__) . '/urlreader.php');

// The main interface to the Allakhazam
class ParseAllakhazam
{
	var $xml_helper;

	// Constructor
	function ParseAllakhazam()
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

		for ($ct = 0; isset($GLOBALS["allakhazam_lang"][$ct]); $ct++)
		{
			$current_lang = $GLOBALS["allakhazam_lang"][$ct];

			// Perform the search.
			$data = itemstats_read_url('http://lotro.allakhazam.com/search.html?q=' . urlencode(utf8_encode($name)));
	        $item_found = false;

			// Look for a name match in the search result.
			//regex fixed by hoofy
			  if (preg_match_all('#item\.html\?lotritem=(.*?)\"#', $data, $matches))
			{
				foreach ($matches[0] as $key => $match)
				{
					// Extract the item's ID from the match.
					$item_id = $matches[1][$key];
					
					// Retrieve the XML for this item from Allakhazam.
					$xml_data = itemstats_read_url('http://lotro.allakhazam.com/cluster/item-xml.pl?lotritem=' . $item_id);
					
					// Parse out the name of this item from the XML and check if we have a match
					//$xml_name = utf8_decode($this->xml_helper->parse($xml_data, 'name'));
					$xml_name = utf8_decode($this->xml_helper->parse($xml_data, 'item_name'));
					$xml_icon = $this->xml_helper->parse($xml_data, 'icon');

					if ($xml_icon == "")
					{
					$xml_icon = "unknown.png";
					}

				    if (strcasecmp($item['name'], $xml_name) == 0)
					{
						// If we have a match, grab additional information about this item and break out of this loop.
						$item['name'] = $xml_name;
	                    $item['id'] = $item_id;
	                    $item['lang'] = substr($current_lang, 0, 2);
						//$item['icon'] = $matches[1][$key];
						$item['icon'] = $xml_icon;
						$item['link'] = 'http://lotro.allakhazam.com/db/item.html?lotritem=' . $item_id;
					    $item_found = true;
						break;
					}
				}
			}

			// If a match was found, retrieve additional info about it.
	        if ($item_found)
			{
		        $html_data = utf8_decode(itemstats_read_url('http://lotro.allakhazam.com/ihtml?' . $item_id));
		        $intPositionBody = strpos($html_data, '<body>', 0);
                $intPositionEndHTML = strpos($html_data, '<div class="credit">', 0);
                if ($intPositionEndHTML === false)
                {
                    $intPositionEndHTML = strpos($html_data, '</html>', 0);
                }
		        $item['html'] = substr($html_data, $intPositionBody, $intPositionEndHTML - $intPositionBody);

				// Parse out the display html of this item from the XML
			    //$item['html'] = utf8_decode($this->xml_helper->parse($xml_data, 'display_html'));
	            $item['html'] = str_replace("</div>", "{ITEMSTATS_LINK}</div>", $item['html']);

	            $offset_akznotice = strpos($item['html'], '<br/><br/><span class="akznotice">');
	            if ($offset_akznotice !== false)
	            {
	                $offset_itemstats_link = strpos($item['html'], '{ITEMSTATS_LINK}</div>', $offset_akznotice);
	                if ($offset_itemstats_link !== false)
	                    $item_suppl = substr($item['html'], $offset_itemstats_link, 22);
	                else
	                    $item_suppl = '';
	                $item['html'] = substr($item['html'], 0, $offset_akznotice) . $item_suppl;
	            }

				// Replace Image socket
				if (defined(path_sockets_image) == true)
					$path_images_sockets = path_sockets_image;
				else
					$path_images_sockets = "http://lotro.allakhazam.com/images/";
				if (preg_match_all('#<img class="esock" src="/images/(.*?)\.png".*?/>#s', $item['html'], $matches_sockets))
				{
					foreach ($matches_sockets[0] as $key_socket => $match_socket)
						$item['html'] = str_replace($matches_sockets[0][$key_socket], '<img src="' . $path_images_sockets . $matches_sockets[1][$key_socket] . '.png" alt="' . $matches_sockets[1][$key_socket] . '"/>', $item['html']);
				}

	            // Fix up the html a bit.
				$item['html'] = str_replace("</span></span><br /><span class='wowrttxt'>", "</span></span>\n<br /><span class='wowrttxt'>", $item['html']);
				$item['html'] = str_replace('"', '\'', $item['html']);
				$item['html'] = preg_replace('#(<[ /]*br[ /]*>)<[ /]*br[ /]*>#', '$1', $item['html']);
				$item['html'] = str_replace("<a href='http://wow.allakhazam.com/db/itemset", "<br><a class='set' href='http://lotro.allakhazam.com/db/itemset", $item['html']);
				$item['html'] = str_replace("<a", "<a target='_new'", $item['html']);
				$item['html'] = preg_replace("#<a ([^>]*) class='itemcreatelink'>#", "<br><span class='itemeffectlink'>Creates: </span>\\0", $item['html']);
				$item['html'] = str_replace('(', "", $item['html']);
				$item['html'] = str_replace(')', "", $item['html']);

				// Extract the item color from the HTML.
				preg_match_all("#<span class='(.*?)'>#s", $item['html'], $matches);
				foreach ($matches[1] as $match)
	            {
					if ($match!="iname" && $match!="")
	                {
						$item['color'] = $match;
						break;
					}
				}

				// If this is a set, grab the set bonuses and add it to the html.
				$item_set_id = $this->xml_helper->parse($xml_data, 'setid');
				if (!empty($item_set_id) && ($item_set_id != '0'))
				{
					// Read the item set page.
					$data = utf8_decode(itemstats_read_url('http://lotro.allakhazam.com/db/itemset.html?lotritemset=' . $item_set_id));

					// Extract the set bonus html from this page.
					preg_match('#Set Bonuses:</div>(.*?)<br/><div class#s', $data, $match);
					$item_set_bonuses = $match[1];

					// Fix up the html a bit /* COMMENT - MAY NEED TO CHANGE spell.html to skill.html ~Sokol */
					$item_set_bonuses = str_replace('/db/skill.html', 'http://lotro.allakhazam.com/db/skill.html', $item_set_bonuses);
					$item_set_bonuses = str_replace("<a", "<a class='setbonus' target='_new'", $item_set_bonuses);
					$item_set_bonuses = str_replace('"', '\'', $item_set_bonuses);
					$item_set_bonuses = preg_replace('#<[ /]*br[ /]*>$#','',$item_set_bonuses);
					$item_set_bonuses = "<span class='setbonus'>" . $item_set_bonuses . "</span>";

					// Insert the set bonus text into the display html;
					$item['html'] = preg_replace('#setid=(.*?)</span></a>#s', '\\0' . $item_set_bonuses, $item['html']);
				}

	            /*
			            $temp_size_object_data = strlen($object_data);
			            if (substr($item['html'], $temp_size_object_data - 5, 5) == '<br/>')
			                $item['html'] = substr($item['html'], 0, $temp_size_object_data - 5);
			            */

				// Build the final HTML by merging the template and the data we just prepared.
				$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
				$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
	            return $item;
			}
		}

        unset($item['link']);
        //echo "Aucun objet trouvé !<br/>";
        return ($item);
	}

	// Attempts to retrieve data for the specified item with ID from Allakhazam.
	function getItemId($item_id, $locale_lang)
    {
		// Retrieve the XML for this item from Allakhazam.
		$xml_data = itemstats_read_url('http://lotro.allakhazam.com/cluster/item-xml.pl?lotritem=' . $item_id);

		// Parse out the name of this item from the XML and check if we have a match
		$xml_name = $this->xml_helper->parse($xml_data, 'name1');
        $item['name'] = utf8_decode($xml_name);

		$xml_icon = $this->xml_helper->parse($xml_data, 'icon');
        $found = preg_match("/\/images\/icons\/(.*?)\.png/i", $xml_data, $matches);
        if ($found == false)
            return ($item);

		// If we have a match, grab additional information about this item and break out of this loop.
        $item['id'] = $item_id;
        $item['lang'] = 'en';
		$item['icon'] = $matches[1];
		$item['link'] = 'http://lotro.allakhazam.com/db/item.html?lotritem=' . $item_id;

		// Parse out the display html of this item from the XML
		$html_data = utf8_decode(itemstats_read_url('http://lotro.allakhazam.com/ihtml?' . $item_id));
		$intPositionBody = strpos($html_data, '<body>', 0);
        $intPositionEndHTML = strpos($html_data, '<div class="credit">', 0);
        if ($intPositionEndHTML === false)
        {
            $intPositionEndHTML = strpos($html_data, '</html>', 0);
        }
		$item['html'] = substr($html_data, $intPositionBody, $intPositionEndHTML - $intPositionBody);

		//$item['html'] = utf8_decode($this->xml_helper->parse($xml_data, 'display_html'));
        $item['html'] = str_replace("</div>", "{ITEMSTATS_LINK}</div>", $item['html']);

        $offset_akznotice = strpos($item['html'], '<br/><br/><span class="akznotice">');
        if ($offset_akznotice !== false)
        {
            $offset_itemstats_link = strpos($item['html'], '{ITEMSTATS_LINK}</div>', $offset_akznotice);
            if ($offset_itemstats_link !== false)
                $item_suppl = substr($item['html'], $offset_itemstats_link, 22);
            else
                $item_suppl = '';
            $item['html'] = substr($item['html'], 0, $offset_akznotice) . $item_suppl;
        }

        // Replace Image socket
	    if (defined(path_sockets_image) == true)
		    $path_images_sockets = path_sockets_image;
	    else
		    $path_images_sockets = "http://lotro.allakhazam.com/images/";
	    if (preg_match_all('#<img class="esock" src="/images/(.*?)\.png".*?/>#s', $item['html'], $matches_sockets))
	    {
		    foreach ($matches_sockets[0] as $key_socket => $match_socket)
			    $item['html'] = str_replace($matches_sockets[0][$key_socket], '<img src="' . $path_images_sockets . $matches_sockets[1][$key_socket] . '.png" alt="' . $matches_sockets[1][$key_socket] . '"/>', $item['html']);
	    }

		// Fix up the html a bit.
		$item['html'] = str_replace("</span></span><br /><span class='wowrttxt'>", "</span></span>\n<br /><span class='wowrttxt'>", $item['html']);
		$item['html'] = str_replace('"', '\'', $item['html']);
		$item['html'] = preg_replace('#(<[ /]*br[ /]*>)<[ /]*br[ /]*>#', '$1', $item['html']);
		$item['html'] = str_replace("<a href='http://wow.allakhazam.com/db/itemset", "<br><a class='set' href='http://lotro.allakhazam.com/db/itemset", $item['html']);
		$item['html'] = str_replace("<a", "<a target='_new'", $item['html']);
		$item['html'] = preg_replace("#<a ([^>]*) class='itemcreatelink'>#", "<br><span class='itemeffectlink'>Creates: </span>\\0", $item['html']);
		$item['html'] = str_replace('(', "", $item['html']);
		$item['html'] = str_replace(')', "", $item['html']);

		// Extract the item color from the HTML.
		preg_match_all("#<span class='(.*?)'>#s", $item['html'], $matches);
		foreach ($matches[1] as $match)
        {
			if ($match!="iname" && $match!="")
            {
				$item['color'] = $match;
				break;
			}

		}

		// If this is a set, grab the set bonuses and add it to the html.
		$item_set_id = $this->xml_helper->parse($xml_data, 'setid');
		if (!empty($item_set_id) && ($item_set_id != '0'))
		{
			// Read the item set page.
			$data = utf8_decode(itemstats_read_url('http://lotro.allakhazam.com/db/itemset.html?lotritemset=' . $item_set_id));

			// Extract the set bonus html from this page.
			preg_match('#Set Bonuses:</div>(.*?)<br/><div class#s', $data, $match);
			$item_set_bonuses = $match[1];

			// Fix up the html a bit
			$item_set_bonuses = str_replace('/db/spell.html', 'http://lotro.allakhazam.com/db/skill.html', $item_set_bonuses);
			$item_set_bonuses = str_replace("<a", "<a class='setbonus' target='_new'", $item_set_bonuses);
			$item_set_bonuses = str_replace('"', '\'', $item_set_bonuses);
			$item_set_bonuses = preg_replace('#<[ /]*br[ /]*>$#','',$item_set_bonuses);
			$item_set_bonuses = "<span class='setbonus'>" . $item_set_bonuses . "</span>";

			// Insert the set bonus text into the display html;
			$item['html'] = preg_replace('#setid=(.*?)</span></a>#s', '\\0' . $item_set_bonuses, $item['html']);
		}

        /*
	        $temp_size_object_data = strlen($item['html']);
	        if (substr($item['html'], $temp_size_object_data - 5, 5) == '<br/>')
	            $item['html'] = substr($item['html'], 0, $temp_size_object_data - 5);
	        */

		// Build the final HTML by merging the template and the data we just prepared.
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
        return $item;
	}
}
?>