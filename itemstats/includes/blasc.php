<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core
|
|       Yahourt
|       http://itemstats.free.fr
|       itemstats@free.fr
|
|       Thorkal
|       EU Elune / Horde
|       www.elune-imperium.com
+---------------------------------------------------------------+
*/

include_once(dirname(__FILE__) . '/xmlhelper.php');
include_once(dirname(__FILE__) . '/urlreader.php');

// The main interface to the Blasc
class ParseBlasc
{
	var $xml_helper;

	// Constructor
	function ParseBlasc()
	{
		$this->xml_helper = new XmlHelper();
	}

	// Cleans up resources used by this object.
	function close()
	{
		$this->xml_helper->close();
	}

	// Attempts to retrieve data for the specified item from BLASC.
	function getItem($name)
	{
		// defines the css style for the various name colors
		$quality = array("greyname", "whitename", "greenname", "bluename", "purplename", "orangename", "redname");

		// Ignore blank names.
		$name = trim($name);
		if (empty($name))
		{
			return null;
		}

        $item = array('name' => $name);

		$data = itemstats_read_url('http://www.buffed.de/?f=' . urlencode($item['name']));

        $item_found = false;

		// Look for a name match in the search result.
        // if (preg_match_all('#i=(.*?)" onmouseover.*?<span class="q.*?">(.*?)</span>#s', $data, $matches))
        // Nouveau pour "buffed.de"
        #if (preg_match_all('#<div id="([0-9]*)" class="tooltip"><div><span class="item q.*?">(.*?)</span>#s', $data, $matches))
        if (preg_match_all('#<div id="([0-9]*)" class="tooltip"><div class="wowtooltip"><div class="itemtitle q.*?">(.*?)</div>#s', $data, $matches))
		{
			foreach ($matches[0] as $key => $match)
			{
				$item_name_tosearch = html_entity_decode($matches[2][$key]);
				if (strcasecmp($item_name_tosearch, $item['name']) == 0)
				{
					// Extract the item's ID from the match.
					$item_id = $matches[1][$key];

					// Retrieve the XML for this item from BLASC.
					$xml_data = itemstats_read_url('http://www.buffed.de/xml/i' . $item_id . '.xml');

                    // Parse out the name of this item from the XML and check if we have a match
					$xml_name = $this->xml_helper->parse($xml_data, 'InventoryName');
					if (strcasecmp($item['name'], $xml_name) == 0)
					{
						// If we have a match, grab additional information about this item and break out of this loop.
						$item['icon'] = $this->xml_helper->parse($xml_data, 'Icon');
						$item['link'] = 'http://www.buffed.de/?i=' . $item_id;
						$item_found = true;
						break;
					}
				}
			}
		}
		// If a match was found, retrieve additional info about it.
		if ($item_found)
		{
			$item['id'] = $item_id;
            $item['lang'] = 'de';
            $item['link'] = 'http://www.buffed.de/?i=' . $item_id;

			// $item['name'] = $item['name']; // Pas de htmlentities

			// Parse out the display html of this item from the XML
			$item['html'] = $this->xml_helper->parse($xml_data, 'display_html');

			// Fix up the html a bit.
			$item['html'] = preg_replace('/^<div>(.*)<\/div>$/', '<table cellpadding=\'0\' border=\'0\' class=\'wowitemt\'><tr><td colspan=\'2\'>\1</td></tr>{ITEMSTATS_LINK}</table>', $item['html']);
			$item['html'] = str_replace('item q5', 'orangename', $item['html']);
			$item['html'] = str_replace('item q4', 'purplename', $item['html']);
			$item['html'] = str_replace('item q3', 'bluename', $item['html']);
			$item['html'] = str_replace('item q2', 'greenname', $item['html']);
			$item['html'] = str_replace('item q1', 'whitename', $item['html']);
			$item['html'] = str_replace('itemspells', 'itemeffectlink', $item['html']);
			$item['html'] = str_replace('</span><br><br>', '</span><br />', $item['html']);
			$item['html'] = str_replace('<table width="100%">', '<table width=\'100%\' class=\'borderless\'>', $item['html']);
			$item['html'] = str_replace('<span class="itemdesc">', '<br /><span class=\'goldtext\'>', $item['html']);
			$item['html'] = str_replace('"', '\'', $item['html']);


			$item['html'] = str_replace('/images/wow/socket/SocketBlue.png', "./images/sockets/socket_blue.gif", $item['html']);
			$item['html'] = str_replace('/images/wow/socket/SocketRed.png',  "./images/sockets/socket_red.gif", $item['html']);
			$item['html'] = str_replace('/images/wow/socket/SocketYellow.png', "./images/sockets/socket_yellow.gif", $item['html']);
			$item['html'] = str_replace('/images/wow/socket/SocketMeta.png', "./images/sockets/socket_meta.gif", $item['html']);

			$item_set_id = $this->xml_helper->parse($xml_data, 'Item Set');
			if (!empty($item_set_id) && ($item_set_id != '0'))
			{
				// Read the item set page.
				$data = itemstats_read_url('http://www.buffed.de/?set=' . $item_set_id);

				// Extract the set bonus html from this page.
				preg_match('#<table class="liste"(.*?)</table>#s', $data, $match);
				$item_set_bonuses = '<table class="setlist" class="borderless" border="0"'.$match[1].'</table>';
				$item_set_bonuses = str_replace('"', "'", $item_set_bonuses);

				// Fix up the html a bit
				$item_set_bonuses = "<div class='setbonus'><span class='spacer'><br /></span>" . $item_set_bonuses . "</div>";
				$item['html'] = substr($item['html'],0,-6).$item_set_bonuses.'</div>';
			}

			// Extract the item color from the HTML.
			$item['color'] = $quality[$this->xml_helper->parse($xml_data, 'quality')];

            //$item['html'] = $item['html'] . '';
            //$item['html'] = "<div class='wowitem'>" . $item['html'] . "</div>";

			// Build the final HTML by merging the template and the data we just prepared.
			$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
			$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

            return ($item);
		}
        unset($item['link']);
        //echo "Aucun objet trouvé !<br/>";
        return ($item);
 	}

	// Attempts to retrieve data for the specified item from Blasc.
	function getItemId($item_id)
	{
        $quality = array("greyname", "whitename", "greenname", "bluename", "purplename", "orangename", "redname");

        $item = array('id' => $item_id);
        $item['lang'] = 'de';

		// Retrieve the XML for this item from Blasc.
		$xml_data = itemstats_read_url('http://www.buffed.de/xml/i' . $item_id . '.xml');

		// Parse out the name of this item from the XML and check if we have a match
		$xml_name = $this->xml_helper->parse($xml_data, 'InventoryName');
		if ($xml_name == "")
            return ($item);
        $item['name'] = $xml_name;

        $item['link'] = 'http://www.buffed.de/?i=' . $item_id;

        $xml_icon = $this->xml_helper->parse($xml_data, 'Icon');
        $item['icon'] = $xml_icon;

        $xml_quality = $this->xml_helper->parse($xml_data, 'Quality');
        if (is_numeric($xml_quality))
            $item['color'] = $quality[$xml_quality];

		// Parse out the display html of this item from the XML
		$item['html'] = $this->xml_helper->parse($xml_data, 'display_html');

		// Fix up the html a bit.
		$item['html'] = preg_replace('/^<div>(.*)<\/div>$/', '<table cellpadding=\'0\' border=\'0\' class=\'wowitemt\'><tr><td colspan=\'2\'>\1</td></tr>{ITEMSTATS_LINK}</table>', $item['html']);
		$item['html'] = str_replace('item q6', 'redname', $item['html']);
		$item['html'] = str_replace('item q5', 'orangename', $item['html']);
		$item['html'] = str_replace('item q4', 'purplename', $item['html']);
		$item['html'] = str_replace('item q3', 'bluename', $item['html']);
		$item['html'] = str_replace('item q2', 'greenname', $item['html']);
		$item['html'] = str_replace('item q1', 'whitename', $item['html']);
		$item['html'] = str_replace('itemspells', 'itemeffectlink', $item['html']);
		$item['html'] = str_replace('</span><br><br>', '</span><br />', $item['html']);
		$item['html'] = str_replace('<table width="100%">', '<table width=\'100%\' class=\'borderless\'>', $item['html']);
		$item['html'] = str_replace('<span class="itemdesc">', '<br /><span class=\'set\'>', $item['html']);
		$item['html'] = str_replace('"', '\'', $item['html']);

		$item_set_id = $this->xml_helper->parse($xml_data, 'Item Set');
		if (!empty($item_set_id) && ($item_set_id != '0'))
		{
			// Read the item set page.
			$data = itemstats_read_url('http://www.buffed.de/?set=' . $item_set_id);

			// Extract the set bonus html from this page.
			preg_match('#<table class="liste"(.*?)</table>#s', $data, $match);
			$item_set_bonuses = '<table class="setlist" class="borderless" border="0"'.$match[1].'</table>';
			$item_set_bonuses = str_replace('"', "'", $item_set_bonuses);

			// Fix up the html a bit
			$item_set_bonuses = "<div class='setbonus'><span class='spacer'><br /></span>" . $item_set_bonuses . "</div>";
			$item['html'] = substr($item['html'],0,-6).$item_set_bonuses.'</div>';
		}

        //$item['html'] = $item['html'] . '{ITEMSTATS_LINK}';
        //$item['html'] = "<div class='wowitem'>" . $item['html'] . "</div>";

		// Build the final HTML by merging the template and the data we just prepared.
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

		return ($item);
	}
}
?>