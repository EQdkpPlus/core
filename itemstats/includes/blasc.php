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
	var $searchurl = 'www.buffed.de';

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
	function getItem($name, $nosearchagain=false)
	{
		global $conf_plus;
		$url = ($nosearchagain) ? $nosearchagain : $this->searchurl;
		
		// defines the css style for the various name colors
		$quality = array("greyname", "whitename", "greenname", "bluename", "purplename", "orangename", "redname");

		// Ignore blank names.
		$name = trim($name);
		if (empty($name))
		{
			return null;
		}

        $item = array('name' => $name);

		$data = itemstats_read_url('http://'.$url.'/?f=' . urlencode($item['name']));

        $item_found = false;

		// Look for a name match in the search result.
        // if (preg_match_all('#i=(.*?)" onmouseover.*?<span class="q.*?">(.*?)</span>#s', $data, $matches))
        // Nouveau pour "buffed.de"
        #if (preg_match_all('#<div id="([0-9]*)" class="tooltip"><div><span class="item q.*?">(.*?)</span>#s', $data, $matches))
        #if (preg_match_all('#<div id="([0-9]*)" class="tooltip"><div class="wowtooltip"><div class="itemtitle q.*?">(.*?)</div>#s', $data, $matches))

		//fixed by hoofy
        if (preg_match_all('#new Btabs\(\[\{\"id\":\"items\",\"n\":\"(.*?)\",\"rows\":\[(.*?)\],\"tpl\":\"itemlist\"\}#', $data, $matchs))
        {
          if (preg_match_all('#\{\"id\":\"(.*?)\",\"n\":\"([0-9])(.*?)\",\"level\":\"([0-9]*)\",\"cl\":\"(.*?)\",\"on\":\"(.*?)\"\}#', $matchs[2][0], $matches))
		  {
			foreach ($matches[0] as $key => $match)
			{
				$item_name_tosearch = html_entity_decode($matches[3][$key]);
				//decode unicode
				$item_name_tosearch = str_replace('\u00df', 'ß', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00e4', 'ä', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00fc', 'ü', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00f6', 'ö', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00c4', 'Ä', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00dc', 'Ü', $item_name_tosearch);
				$item_name_tosearch = str_replace('\u00d6', 'Ö', $item_name_tosearch);

				if (strcasecmp($item_name_tosearch, $item['name']) == 0)
				{
					// Extract the item's ID from the match.
					$item_id = $matches[1][$key];

					// Retrieve the XML for this item from BLASC.
					$xml_data = itemstats_read_url('http://www.buffed.de/xml/i' . $item_id . '.xml');

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
			$icon_path = $conf_plus['pk_is_icon_loc'].$item['icon'].$conf_plus['pk_is_icon_ext'];
			$item['html'] = "<table class='wowbuffed' cellspacing='0'><tr><td class='normal'>";
			$item['html'] .= $this->xml_helper->parse($xml_data, 'display_html');

			#UTF8 Check
			$item['html'] = isUTF8($item['html']) ? utf8_decode($item['html']) : $item['html'] ;

			// Fix up the html a bit.
			$item['html'] = preg_replace('/^<div>(.*)<\/div>$/', '<table cellpadding=\'0\' border=\'0\' class=\'wowitemt\'><tr><td colspan=\'2\'>\1</td></tr>{ITEMSTATS_LINK}</table>', $item['html']);
			$item['html'] = str_replace('q5', 'orangename', $item['html']);
			$item['html'] = str_replace('q4', 'purplename', $item['html']);
			$item['html'] = str_replace('q3', 'bluename', $item['html']);
			$item['html'] = str_replace('q2', 'greenname', $item['html']);
			$item['html'] = str_replace('q1', 'whitename', $item['html']);
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
			$item['html'] .= "</td><td class='right'></tr><tr><td class='bottomleft'></td><td class='bottomright'></td></tr></table>";
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
        return (($nosearchagain) ? $item : $this->getItem($name, 'www.getbuffed.com'));
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