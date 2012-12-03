<?php

include_once(dirname(__FILE__) . '/config.php');
include_once(dirname(__FILE__) . '/itemstats.php');

if (isset($_GET['item']))
{
	$item_stats = new ItemStats(false, 1);
	if ($item_stats->connected == false)
    {
        echo "Erreur de connection à la base de donnée";        
        return;
    }                

    // Grab the item name.
	$item_name = $_GET['item'];
    $item_type = $_GET['type'];
    $icon_lsize = $_GET['size'];
    
    if ($item_type != 'item' and $item_type != 'itemico')
        $item_type = 'item';
        
    $item_html = $item_stats->getItemForDisplay($item_name, $item_type, $icon_lsize, true, 1);
    
    $link_to_updatepage = str_replace('viewitem.php', '', $_SERVER['PHP_SELF']);
    
    $item_html = str_replace('{PATH_ITEMSTATS}', $link_to_updatepage, $item_html);
    
    $item_html = addslashes($item_html);
    echo 'document.write("' . $item_html . '");';
}
?>