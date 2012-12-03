<?php



include_once(dirname(__FILE__) . '/../config.php');

include_once(dirname(__FILE__) . '/sqlhelper.php');



// Interface to the item cache.

class ItemCache

{

	var $sql;



	// Constructor

	function ItemCache() 

	{

		$this->sql = new SqlHelper(dbhost, dbname, dbuser, dbpass);



		// Create the item cache table.		

		$query = "CREATE TABLE IF NOT EXISTS `" . item_cache_table . "` (

                  `item_name` varchar(100) NOT NULL default '',

                  `item_link` varchar(100) default NULL,

                  `item_color` varchar(20) NOT NULL default '',

                  `item_icon` varchar(50) NOT NULL default '',

                  `item_html` text NOT NULL,

                  UNIQUE KEY `item_name` (`item_name`),

                  FULLTEXT KEY `item_html` (`item_html`))";

		$this->sql->query($query);

	}



	// Cleans up resources used by this object.

	function close()

	{

		$this->sql->close();

	}

	

	// Returns the proper name for the specified item.

	function getItemName($name)

	{

		return $this->sql->query_first("SELECT item_name FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");

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

		if (!isset($item))

		{

			return false;

		}

		

		// Delete any existing entry for this item.

		$this->sql->query("DELETE FROM " . item_cache_table . " WHERE item_name = '" . addslashes($item['name']) . "'");



		// Add the entry to the database.	

		$result = $this->sql->query("INSERT INTO " . item_cache_table . " VALUES (" .

								 "'" . addslashes($item['name']) . "', " . 

								 (empty($item['link']) ? 'NULL' : "'" . addslashes($item['link']) . "'") . ", " .

								 "'" . addslashes($item['color']) . "', " .

								 "'" . addslashes($item['icon']) . "', " .

								 "'" . addslashes($item['html']) . "')");



		if (!$result)

		{

			$error = $this->sql->error();

			$error = $error['message'];

			echo "Failed to update item: $name ($error)</br>";

		}

		

		return $result;

	}

}

?>