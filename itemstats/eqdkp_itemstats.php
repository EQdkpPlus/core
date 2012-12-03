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
|
|       Based on Psyker7 work
|       http://forums.eqdkp.com/index.php?showtopic=4435
|
+---------------------------------------------------------------+
*/

include_once(dirname(__FILE__) . '/itemstats.php');
include_once(dirname(__FILE__) . '/phpbb_itemstats.php');

if (!defined('path_itemstats'))
	define(path_itemstats, './itemstats');

// This decorates the name of an item with icon, color and overlib popup.  This is used for the 'List Items',
// 'View Member', 'View Event' and 'View Raid' pages.
function itemstats_decorate_name($name)
{
	$item_stats = new ItemStats(true);
	if ($item_stats->connected == false)
        return ($name);

	// Attempt the get the proper name of this item.
	$decorated_name = $item_stats->getItemName($name, automatic_search);

	// Apply color to the name.
	$item_color = $item_stats->getItemColor($name);
	if (!empty($item_color))
	{
        $decorated_name = "<span class='" . $item_color . "'>" . $decorated_name . "</span>";
	}

	// Add the icon to the name.
	$item_icon_link = $item_stats->getItemIconLink($name);
	if (!empty($item_icon_link))
	{
		$decorated_name = "<img class='smallitemicon' src='" . $item_icon_link . "'> " . $decorated_name;
	}

	// Wrap everything around tooltip code.
	$item_tooltip_html = $item_stats->getItemTooltipHtml($name);
	if (!empty($item_tooltip_html))
	{
        if (defined('displayitemstatslink') && displayitemstatslink == true)
            $item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "<br/><p class=\'textitemstats\'>itemstats.free.fr</p>", $item_tooltip_html);
        else
            $item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "", $item_tooltip_html);

		$decorated_name = "<span " . $item_tooltip_html . ">" . $decorated_name . "</span>";
	}

	return $decorated_name;
}

// This function returns HTML for an item.  This is used for the 'View Item' page.
function itemstats_get_html($name)
{
	$item_stats = new ItemStats();
	if ($item_stats->connected == false)
        return ($name);

	// Get the HTML for this item.
	$item_html = $item_stats->getItemHtml($name, true);

	// If this item has a link to the info site, add this link to the HTML.
	$item_link = $item_stats->getItemLink($name);
	if (!empty($item_link))
	{
		// Get the proper name and color of this item.
		$item_name = $item_stats->getItemName($name);
		$item_color = $item_stats->getItemColor($name);

		// Add the link to the HTML.
		$pattern = "#<span class=[ ']*" . $item_color . "[ ']*>" . $item_name . "</span>#";
		$replacement = "<a target='_blank' class='itemlink' href='" . $item_link . "'>\\0</a>";

		$item_html = preg_replace($pattern, $replacement, $item_html);
	}

	// Build a refresh link for this item.
	$refresh_link = path_itemstats . '/updateitem.php?item=' . urlencode(urlencode($name));

	// Build the final HTML of the item by merging the viewitem template and the data we just prepared.
	$template_html = trim(file_get_contents(dirname(__FILE__) . '/templates/viewitem.tpl'));

	$searches = Array('{ITEM_HTML}', '{REFRESH_LINK}');
	$replacements = Array($item_html, $refresh_link);

	$item_html = str_replace($searches, $replacements, $template_html);

    /*if (defined('displayitemstatslink') && displayitemstatslink == 'true')
        $item_html = str_replace("{ITEMSTATS_LINK}", "<br/><p class=\'textitemstats\'>itemstats.free.fr</p>", $item_html);
    else*/
        $item_html = str_replace("{ITEMSTATS_LINK}", "", $item_html);


	return $item_html;
}

?>
