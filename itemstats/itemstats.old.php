<?php

include_once(dirname(__FILE__) . '/config.php');
include_once(dirname(__FILE__) . '/includes/itemcache.php');
include_once(dirname(__FILE__) . '/includes/allakhazam.php');

// The main interface to the ItemStats module.
class ItemStats
{
	var $item_cache;
	var $info_site;
	
	// Constructor
	function ItemStats()
	{
		$this->item_cache = new ItemCache();
		$this->info_site = new InfoSite();

		// Setup a ghetto destructor.
		register_shutdown_function(array(&$this, '_ItemStats'));
	}
	
	// Ghetto Destructor
	function _ItemStats()
	{
		$this->item_cache->close();
		$this->info_site->close();
	}
	
	// Returns the properly capitalized name for the specified item.  If the update flag is set and the item is
	// not in the cache, item data item will be fetched from an info site
	function getItemName($name, $update = false)
	{
		$proper_name = $this->item_cache->getItemName($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($proper_name) && $update)
		{
			$this->updateItem($name);
			$proper_name = $this->item_cache->getItemName($name);
		}
		
		return empty($proper_name) ? $name : $proper_name;
	}
	
	// Returns the link to the info site for the specified item.  If the update flag is set and the item is not in
	// the cache, item data will be fetched from an info site
	function getItemLink($name, $update = false)
	{
		$link = $this->item_cache->getItemLink($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($link) && $update)
		{
			$this->updateItem($name);
			$link = $this->item_cache->getItemLink($name);
		}

		return $link;
	}
	
	// Returns the color class for the specified item.  If the update flag is set and the item is not in the cache, item
	// data will be fetched from an info site
	function getItemColor($name, $update = false)
	{
		$color = $this->item_cache->getItemColor($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($color) && $update)
		{
			$this->updateItem($name);
			$color = $this->item_cache->getItemColor($name);
		}

		return $color;
	}
	
	// Returns the icon link for the specified item.  If the update flag is set and the item is not in the cache, item
	// data will be fetched from an info site
	function getItemIconLink($name, $update = false)
	{
		$icon = $this->item_cache->getItemIcon($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($icon) && $update)
		{
			$this->updateItem($name);
			$icon = $this->item_cache->getItemIcon($name);
		}
		
		// If the icon was found, create a link by merging it with the icon path and extension.
		if (!empty($icon))
		{
			$icon_link = ICON_STORE_LOCATION . $icon . ICON_EXTENSION;
		}

		return $icon_link;
	}
	
	// Returns the html for the specified item.  If the update flag is set and the item is not in the cache, the
	// item will be fetched from an info site
	function getItemHtml($name, $update = false)
	{
		$html = $this->item_cache->getItemHtml($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($html) && $update)
		{
			$this->updateItem($name);
			$html = $this->item_cache->getItemHtml($name);
		}
		
		// If the item was found, update the icon path in the HTML.
		if (!empty($html))
		{
			$html = str_replace(ICON_LINK_PLACEHOLDER, $this->getItemIconLink($name), $html);
		}

		return $html;
	}
	
	// Returns the overlib tooltip html for the specified item.  If the update flag is set and the item is not in
	// the cache, the item will be fetched from an info site
	function getItemTooltipHtml($name, $update = false)
	{
		// Retrieve the item data from the cache.
		$html = $this->getItemHtml($name, $update);
		if (empty($html))
		{
			return null;
		}

		// Warp the data around the HTML data that invokes the tooltip.
		if (!empty($html))
		{
			// Format the HTML to be compatible with Overlib.
			$html = str_replace(array("\n", "\r"), '', $html);
			$html = addslashes($html);

			$html = 'onmouseover="return overlib(' . "'" . $html . "'" . ',VAUTO,HAUTO,FULLHTML);" onmouseout="return nd();"';
		}

		return $html;
	}

	// Retrieves the data for the specified item from an info site and caches it.
	function updateItem($name)
	{
		// Retrives the data from an information site.
		$item = $this->info_site->getItem($name);

		// If the item wasn't found, and we have something cached already, don't overwrite with lesser data.
		$cached_link = $this->getItemLink($name);
		if (!empty($item['link']) || empty($cached_link))
		{
			// If the data was loaded succesfully, save it to the cache.
			$result = $this->item_cache->saveItem($item);
		}

		return $result;
	}
}
?>