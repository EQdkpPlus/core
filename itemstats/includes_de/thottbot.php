<?php

// ****************************************************************
// This is not fully functional yet.  Use at your own risk.
// ****************************************************************

include_once(dirname(__FILE__) . '/urlreader.php');

define('ICON_LINK_PLACEHOLDER', '{ITEM_ICON_LINK}');
define('DEFAULT_ICON', 'INV_Misc_QuestionMark');

// The main interface to the Thottbot.
class InfoSite
{
	// Constructor
	function InfoSite()
	{
	}
	
	// Cleans up resources used by this object.
	function close()
	{
	}

	// Retrieves the data for the specified item from Thottbot.
	function getItem($name)
	{
		// Ignore blank names.
		$name = trim($name);
		if (empty($name))
		{
			return null;
		}

		$item = array('name' => $name);

		// Perform a search on Thottbot for the specified item name.
		$data = itemstats_read_url('http://www.thottbot.com/?s=' . urlencode($name));

		// First check if thottbot redirected us to the actual item page.
		$item_found = preg_match('#\?i=(.*?);sort#', $data, $match);

		// If we were redirected, extract the item ID and move on.
		if ($item_found)
		{
			$item_id = $match[1];
		}
		// If a match was not found, we're probably on the search page.
		else
		{
			// Look for search result that matches the item name.
			if (preg_match_all("#\?i=(.*?)'><script>i(.*?)class=quality(.*?)>(.*?)</span>(.*?)Version&nbsp;(.*?)</font>#", $data, $matches))
			{
				// Look for a match with a version of 1.5.0
				foreach ($matches[0] as $key => $match)
				{
					if ((strcasecmp($item['name'], $matches[4][$key]) == 0) && ($matches[6][$key] == '1.5.0'))
					{
						// If we have a match, grab additional information about this item and break out of this loop.
						$item_id = $matches[1][$key];

						// Read the page specific to this item.
						$data = itemstats_read_url('http://www.thottbot.com/?i=' . $item_id);

						$item_found = true;
						break;
					}
				}
			}
		}
		
		// If the item was found, we got lots of work to do.  Start molding the HTML to the way we want it.
		if ($item_found)
		{
			// Grab the proper name of this item.
			preg_match("#<span class=quality[0-9]>(.*?)</span>#", $data, $match);
			$item['name'] = $match[1];

			// Grab the icon for this item.
			preg_match('#Icons/(.*?).jpg#', $data, $match);
			$item['icon'] = $match[1];

			// Create the link for this item.
			$item['link'] = 'http://www.thottbot.com/?i=' . $item_id;

			// Grab the display html of this item.
			if (preg_match("/<table(.*?)" . $item['name'] . "(.*?)<\/table>/", $data, $match))
			{
				$item['html'] = $match[0];

				// Extract the item color from the HTML.
				preg_match('/quality[1-9]/', $item['html'], $match);
				$item['color'] = $match[0];

				// Look for a set id for this item.
				if (preg_match("/href='\?set=(.*?)'/", $data, $match))
				{				
					// Create a link to the set page.
					$set_link = 'http://www.thottbot.com/?set=' . $match[1];
				
					// Extract the name of the set for this item.
					if (preg_match('/Set: (.*?) \((.*?)\)/', $item['html'], $match))
					{
						$set_name = $match[1];
					}

					// Extract the set bonuses.
					if (preg_match('/worn:<br>(.*?)<center>/s', $data, $match))
					{
						$set_bonuses = str_replace("<a", "<a class='setbonus'", $match[1]);
					}

					// Build the set bonus html.
					$set_html  = "<tr><td colspan=2><a class='set' href='" . $set_link . "'>" . $set_name . "</a><span class='setbonus'><br />";
					$set_html .= $set_bonuses;
					$set_html .= '</span></td></tr>';

					// Fix the "Set" part of the HTML.
					$item['html'] = preg_replace('/Set: (.*?) \((.*?)\)/', '&nbsp;</td></tr>' . $set_html, $item['html']);
				}

				// Remove the "Sells for" part of the HTML.
				$item['html'] = preg_replace('/<tr><td colspan=2>Sells for(.*?)<\/td><\/tr>/', '', $item['html']);
				
				// Remove the "Item Level" part of the HTML.
				$item['html'] = preg_replace('/<tr><td colspan=2>Item Level(.*?)<\/td><\/tr>/', '', $item['html']);
				
				// Remove the "Durability" part of the HTML.
				$item['html'] = preg_replace('/<tr><td colspan=2>Durability(.*?)<\/td><\/tr>/', '', $item['html']);
				
				// Remove the "Source" part of the HTML.
				$item['html'] = preg_replace('/<tr><td colspan=2><small><font color=(.*?)>Source(.*?)<\/table>/', '</table>', $item['html']);

				// Replace the 'ttd' table style with 'wowitemt'.
				$item['html'] = str_replace('class=ttb', "class='wowitemt'", $item['html']);

				// Replace the 'spell' style with 'itemeffectlink'.
				$item['html'] = str_replace("class='spell'", "class='itemeffectlink'", $item['html']);

				// Remove any underline tags
				$item['html'] = preg_replace('/<[ \/]*u[ \/]*>/', '', $item['html']);
				
				// Fix up some last bits of the HTML.
				$item['html'] = str_replace('"', '\'', $item['html']);
				$item['html'] = str_replace("href='?", "href='http://www.thottbot.com/?", $item['html']);
				$item['html'] = str_replace("<a", "<a target='_new'", $item['html']);

				// Build the final HTML by merging the template and the data we just prepared.
				$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
				$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
			}
		}
		else
		{
			// If Thottbot was busy or this item doesn't exist and this item isn't cached yet, create some error html.
			$item['color'] = 'quality1';
			$item['icon'] = DEFAULT_ICON;

			// Read the template html for an item.
			$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup-error.tpl'));
			$item['html'] = str_replace('{INFO_SITE}', 'Thottbot', $template_html);
		}
		
		return $item;
	}
}
?>