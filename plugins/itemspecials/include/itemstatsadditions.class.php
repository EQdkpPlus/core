<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * itemstatsadditions.class.php
 * Changed: November 10, 2006
 * 
 ******************************/


// This class extends the original itemstats functions to fit the
// needs of itemspecials. Could be used free in other Applications,
// I release it under the GPL Licence.
class ItemstatsAddition
{
// This is for Itemstats 1.5 and higher
function GetItemstatsVersion(){
	global $eqdkp_root_path;
	$file = $eqdkp_root_path.'itemstats/version.php';
	if (is_file($file)){
		require_once($file);
		return $itemstats_core_version;
	}else{
		return false;
	}
}

// Itemstats Additions: Returns the properly capitalized name for the specified item.  If the update flag is set and the item is
// not in the cache, item data item will be fetched from an info site
function itemstats_format_name($name, $update = false)
{
  global $eqdkp_root_path;
	$item_stats = new ItemStats();
	$realname = $item_stats->getItemName($name, $update);
	return $realname;
}

// decorate own icons
function itemstats_decorate_Icon($name, $size, $version = false, $download=false)
{
  global $eqdkp_root_path;
	$item_stats = new ItemStats();
  
   // dowload if not there
  	if($download == true){
      $item_stats->getItemName($name,true);
  	}
  
	// Apply color to the name.
	$item_color = $item_stats->getItemColor($name);
	if (!empty($item_color))
	{
        $decorated_name = "<span class='" . $item_color . "'>" . $decorated_name . "</span>";
	}
  
  //item size
  if ($size == "large"){
    $itemsize = "itemicon";
  }elseif ($size == "middle"){
    $itemsize = "middleitemicon";
  }else {
    $itemsize = "smallitemicon";
  }
  
	// Add the icon to the name.
	$item_icon_link = $item_stats->getItemIconLink($name);
	if (!empty($item_icon_link))
	{
		$decorated_name = "<img class='".$itemsize."' src='" . $item_icon_link . "'> " . $decorated_name;
	}else{
		$decorated_name = "<img class='".$itemsize."' src='images/no_itemcache.png'> " . $decorated_name;
}

	// Wrap everything around tooltip code.
	$item_tooltip_html = $item_stats->getItemTooltipHtml($name);
	if (!empty($item_tooltip_html))
	{
		if($version){
			if (defined('displayitemstatslink') && displayitemstatslink == true){
      	$item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "<br/><p class=\'textitemstats\'>itemstats.free.fr</p>", $item_tooltip_html);
      }else{
       	$item_tooltip_html = str_replace("{ITEMSTATS_LINK}", "", $item_tooltip_html);
      }
		}
		$decorated_name = "<span " . $item_tooltip_html . ">" . $decorated_name . "</span>";
	}


	return $decorated_name;
}

// write Header Image
function itemstats_get_header_Icon($name, $size, $alt, $link='')
{
  global $eqdkp_root_path, $conf, $db;
	$item_stats = new ItemStats();
  
  //item size
  if ($size == "large"){
    $itemsize = "itemicon";
  }elseif ($size == "middle"){
    $itemsize = "middleitemicon";
  }else {
    $itemsize = "smallitemicon";
  }
  
	// Add the icon to the name.
	$item_icon_link = $item_stats->getItemIconLink($name);
	if(!$link){
    $link = 'JavaScript:void(0)';
  }
	if (!empty($item_icon_link))
	{
		$decorated_name = "<a href='$link' onMouseOver=\"overlib('".$alt."', HAUTO, VAUTO);\" onMouseOut='nd();'>
    <img class='".$itemsize."' src='" . $item_icon_link . "'></a> ";
	}else{
		$decorated_name = "<a href='$link' onMouseOver=\"overlib('".$alt."', HAUTO, VAUTO);\" onMouseOut='nd();'>
    <img class='".$itemsize."' src='images/no_itemcache.png'></a> ";
}

	return $decorated_name;
}

} // end of class
?>