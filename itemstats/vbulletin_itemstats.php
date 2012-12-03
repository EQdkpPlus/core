<?php

    include_once(dirname(__FILE__) . '/itemstats.php');

    // START  --  Gestion en Hook BBCode
    function handle_bbcode_item(&$parser, $item_name, $link)
    {
      $item_html = itemstats_parse('[item]' . $item_name . '[/item]');
      return ($item_html);
    }

    function handle_bbcode_itemico(&$parser, $item_name, $link)
    {
      $item_html = itemstats_parse('[itemico]' . $item_name . '[/itemico]');
      return ($item_html);
    }
    // END  --  Gestion en Hook BBCode


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


            //===   Previous processing   =========================================
            // On transforme les entites HTML en vrai caract :)
            $item_name = html_entity_decode($item_name, ENT_QUOTES);
            //=====================================================================

            //===   GET HTML FOR DISPLAY   ========================================
            $item_html = $item_stats->getItemForDisplay($item_name, $item_type, $icon_lsize, automatic_search);
            //=====================================================================

            //===    Next processing   ============================================
            if (defined(path_itemstats))
                $item_html = str_replace("{PATH_ITEMSTATS}", path_itemstats, $item_html);
            else
                $item_html = str_replace("{PATH_ITEMSTATS}", "./itemstats", $item_html);
            //=====================================================================

		    // Finally, replace the bbcode with the html.
		    $message = str_replace($match[0], $item_html, $message);
	    }

        return $message;
    }
?>
