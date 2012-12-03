<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core by Yahourt
|		modded by Corgan for EQdkp Plus
|		Add WoWHead & Armory Support
|
|		$Id$
+---------------------------------------------------------------+
*/

include_once(dirname(__FILE__) . '/itemstats.php');

//generate itemstats instance
$item_stats = new ItemStats(true);

//we'll measure the time taken within itemstats to prevent timeouts and fatal errors
$item_stats_time = 0;

//itemstats timeout in seconds
$item_stats_max_time = (isset($conf_plus['pk_itemstats_max_execution_time']) && $conf_plus['pk_itemstats_max_execution_time'] > 0) ? $conf_plus['pk_itemstats_max_execution_time'] : (ini_get('max_execution_time')*0.8);

//set to 300s if php max_execution_time is not set
if($item_stats_max_time == 0){
  $item_stats_max_time = 300;
}

// This decorates the name of an item with icon, color and overlib popup.  This is used for the 'List Items',
// 'View Member', 'View Event' and 'View Raid' pages.
function itemstats_decorate_name($name,$id=0,$onlyIcon=false, $update=false)
{
	global $conf_plus , $eqdkp_root_path, $item_stats;
	global $item_stats_time, $item_stats_max_time;

	if(!$conf_plus['pk_itemstats'] == 1 )
 	{
 		return $name ;
 	}

 	//we bailout if itemstats requests take to long to prevent timeouts
 	if(round($item_stats_time) > $item_stats_max_time){
 	  return $name;
 	}
	if(!$update AND automatic_search) {
		$update = true;
	}

  $start_time = microtime(true);
	if ($item_stats->connected == false)
        return ($name);

	// Attempt the get the proper name of this item.
	$searching = ($id > 0) ? $id : $name;
	$name2use = $item_stats->getItemName($searching, $update, true);
	if(empty($name2use)) {
		$name2use = $name;
	}
	$decorated_name = $name2use;

	// Apply color to the name.
	$item_color = $item_stats->getItemColor($name2use);
	if (!empty($item_color))
	{
        $decorated_name = "<span class='" . $item_color . "'>" . $name2use . "</span>";
	}

	// Add the icon to the name.
	$item_icon_link = $item_stats->getItemIconLink($name2use);
	if (!empty($item_icon_link))
	{
		$decorated_name = "<img class='smallitemicon' src='" . $item_icon_link . "'> " . ($onlyIcon ? '' : $decorated_name);
	}

	// Wrap everything around tooltip code.
	$item_tooltip_html = $item_stats->getItemTooltipHtml($name2use,false,'',$id);
	if (!empty($item_tooltip_html))
	{
        if (defined('displayitemstatslink') && displayitemstatslink == true)
            $item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "<br/><p class=\'textitemstats\'>itemstats.free.fr</p>", $item_tooltip_html);
        else
            $item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "", $item_tooltip_html);

		$decorated_name = "<span " . $item_tooltip_html . ">" . $decorated_name . "</span>";
	}

	//prevent timeouts
  $item_stats_time += microtime(true)-$start_time;
	return $decorated_name;
}

// This function returns HTML for an item.  This is used for the 'View Item' page.
function itemstats_get_html($name,$id=0,$update=false)
{
	global $conf_plus , $eqdkp_root_path, $item_stats, $item_stats_time, $item_stats_max_time;
	if(!$update AND automatic_search) {
		$update = true;
	}

	if(!$conf_plus['pk_itemstats'] == 1 )
   	{
   		return $name ;
   	}

 	//we bailout if itemstats requests take to long to prevent timeouts
 	if(round($item_stats_time) > $item_stats_max_time){
 	  return $name;
 	}
  $start_time = microtime(true);

	if ($item_stats->connected == false)
        return ($name);

	// Attempt the get the proper name of this item.
	$searching = ($id > 0) ? $id : $name;
	$name2use = $item_stats->getItemName($searching, $update, true);

 	$item_html = $item_stats->getItemHtml($name2use,$update,$id);

	// Build a refresh link for this item.
	$update = ($id>0) ? $id.item_lang_default : $name;
	$refresh_link = $eqdkp_root_path . 'itemstats/updateitem.php?item=' . $update;

	// Build the final HTML of the item by merging the viewitem template and the data we just prepared.
	$template_html = trim(file_get_contents(dirname(__FILE__) . '/templates/viewitem.tpl'));

	$searches = Array('{ITEM_HTML}', '{REFRESH_LINK}');
	$replacements = Array($item_html, $refresh_link);
	$item_html = str_replace($searches, $replacements, $template_html);
    $item_html = str_replace("{ITEMSTATS_LINK}", "", $item_html);

  $item_stats_time += microtime(true)-$start_time;
	return $item_html;
}

function itemstats_parse($message)
{
	global $eqdkp_root_path;
	// Search for [item] tags, and replace them with HTML for the specified item.
	while (preg_match('#\[(item)(=[0-5])?\](.+?)\[/item\]#s', $message, $match) OR preg_match('#\[(itemicon)(=[0-5])?\](.+?)\[/itemicon\]#s', $message, $match))
	{
		// Grab the item name.
		$search = html_entity_decode($match[3], ENT_QUOTES);

		$onlyIcon = ($match[1] == 'itemicon') ? true : false;
		$item_html = itemstats_decorate_name($search, 0, $onlyIcon);

		// Build a refresh link for this item.
		$search = (is_numeric($search)) ? $search.item_lang_default : $search;
		$refresh_link = $eqdkp_root_path . 'itemstats/updateitem.php?item=' . urlencode($search);
		$item_html = "<a href='".$refresh_link."'>".$item_html."</a>";

		// Finally, replace the bbcode with the html.
		$message = str_replace($match[0], $item_html, $message);
	}

    return $message;
}
?>
