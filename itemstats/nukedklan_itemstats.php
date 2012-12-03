<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core by Yahourt
|		modded by Corgan for EQdkp Plus
|		Add WoWHead & Armory Support
|
|		$Id:$
+---------------------------------------------------------------+
*/

include_once(dirname(__FILE__) . '/itemstats.php');

function itemstats_parse($message)
{
	$item_stats = new ItemStats();
	if ($item_stats->connected == false)
        return ($message);

	// Search for [item] tags, and replace them with HTML for the specified item.
	while (preg_match('#\[(item)(=[0-5])?\](.+?)\[/item\]#s', $message, $match) OR preg_match('#\[(itemico)(=[0-5])?\](.+?)\[/itemico\]#s', $message, $match))
	{
		// Grab the item name.
		$item_name = $match[3];
        $icon_lsize = $match[2];
        $item_type = $match[1];


        //====   Previous processing   ========================================
        // On transforme les entites HTML en vrai caract :)
        $item_name = html_entity_decode($item_name, ENT_QUOTES);
        //=====================================================================

        //===   GET HTML FOR DISPLAY   ========================================
        $item_html = $item_stats->getItemForDisplay($item_name, $item_type, $icon_lsize, automatic_search);
        //=====================================================================


        //===    Next processing   ============================================
        $item_html = str_replace("{PATH_ITEMSTATS}", path_itemstats, $item_html);
        $item_html = str_replace("8)", "8 )", $item_html);
        //=====================================================================


		// Finally, replace the bbcode with the html.
		$message = str_replace($match[0], $item_html, $message);
	}

	return $message;
}



?>