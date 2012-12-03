<?php

include_once(dirname(__FILE__) . '/../config.php');
include_once(dirname(__FILE__) . '/sqlhelper.php');

// Interface to the item cache.
class ItemCache
{
	var $sql;
    var $connected = false;

	// Constructor
	function ItemCache($bNewConnection = false) 
	{
		$this->sql = new SqlHelper(dbhost, dbname, dbuser, dbpass, $bNewConnection);
        $this->connected = $this->sql->connected;
        if ($this->connected == false)
            return;

        $query = "SELECT item_id FROM " . item_cache_table;
        $returnValue = $this->sql->query($query);
        if ($returnValue == false)
        {
            $query = "ALTER TABLE " . item_cache_table . " ADD `item_id` VARCHAR(100) DEFAULT '0' AFTER `item_name`";
            $returnValue = $this->sql->query($query);
            $query = "ALTER TABLE " . item_cache_table . " ADD `item_lang` VARCHAR(2) DEFAULT '' AFTER `item_id`";
            $returnValue2 = $this->sql->query($query);            
            if ($returnValue == false || $returnValue2 == false)
            {
		        // Create the item cache table.		
		        $query = "CREATE TABLE IF NOT EXISTS `" . item_cache_table . "` (
                          `item_name` varchar(100) NOT NULL default '',
                          `item_id` varchar(100) default '0',
                          `item_lang` varchar(2) default '',
                          `item_link` varchar(100) default NULL,
                          `item_color` varchar(20) NOT NULL default '',
                          `item_icon` varchar(50) NOT NULL default '',
                          `item_html` text NOT NULL,
                          UNIQUE KEY `item_name` (`item_name`),
                          FULLTEXT KEY `item_html` (`item_html`))";
                $this->sql->query($query);
            }
        }
	}

	// Cleans up resources used by this object.
	function close()
	{        
		$this->sql->close();
	}

    // Returns the ID for the specified item.
	function getItemNameFromId($id, $lang)
	{                         
		return $this->sql->query_first("SELECT item_name FROM " . item_cache_table . " WHERE item_id = '" . $id . "' AND item_lang = '" . $lang . "'");
	}

    
	// Returns the proper name for the specified item.
	function getItemName($name)
	{
		return $this->sql->query_first("SELECT item_name FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
	}

	// Returns the link for the specified item.
	function getItemLinkFromId($id, $lang)
	{
		return $this->sql->query_first("SELECT item_link FROM " . item_cache_table . " WHERE item_id = '" . $id . "' AND item_lang = '" . $lang . "'");
	}

	// Returns the link for the specified item.
	function getItemLink($name)
	{
		return $this->sql->query_first("SELECT item_link FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
	}

	
    // Returns the color class for the specified item.
	function getItemColor($name)
	{
		return $this->sql->query_first("SELECT item_color FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
	}

	// Returns the icon for the specified item.
	function getItemIcon($name)
	{
		return $this->sql->query_first("SELECT item_icon FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
	}

	// Returns the html for the specified item.
	function getItemHtml($name)
	{
		return $this->sql->query_first("SELECT item_html FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
	}

	// Returns a list of all items in the database.
	function getItems()
	{
		$result = $this->sql->query("SELECT item_name FROM " . item_cache_table);
		$items = array();

		while ($item = $this->sql->fetch_record($result))
		{
			array_push($items, $item['item_name']);
		}
		return $items;
	}

	function saveItem($item)
	{
		// Delete any existing entry for this item.
        $this->sql->query("DELETE FROM " . item_cache_table . " WHERE item_name = '" . addslashes($item['name']) . "'");
        
		// Add the entry to the database.
		$result = $this->sql->query("INSERT INTO " . item_cache_table . " VALUES (" .
								 "'" . addslashes($item['name']) . "', " . 
                                 "'" . addslashes($item['id']) . "', " .
                                 "'" . addslashes($item['lang']) . "', " .
								 (empty($item['link']) ? 'NULL' : "'" . addslashes($item['link']) . "'") . ", " .
								 "'" . addslashes($item['color']) . "', " .
								 "'" . addslashes($item['icon']) . "', " .
								 "'" . addslashes($item['html']) . "')");
		if (!$result)
		{
			$error = $this->sql->error();
			$error = $error['message'];
			echo "Failed to update item: " . $item['name'] . " ($error)</br>";
            return false;
		}
		return $result;
	}
    
    function    getEmptyItem($name)
    {
        // Objet non trouvé, on renvoi un objet gris et une erreur :)
        $item['name'] = $name;
        $item['id'] = '0';
        $item['lang'] = 'na';
        $item['color'] = 'greyname';                                                                                         
        $item['icon'] = DEFAULT_ICON;
        $template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup-error.tpl'));
        $item['html'] = str_replace('{INFO_SITE}', 'Itemstats (http://itemstats.free.fr)', $template_html);
        return ($item);
    }
}
?>
