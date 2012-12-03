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
                return $this->getItem("","item_name",$id,$lang);
	}


	// Returns the proper name for the specified item.
	function getItemName($name)
	{
		return $this->getItem($name,"item_name");
	}

	// Returns the link for the specified item.
	function getItemLinkFromId($id, $lang)
	{
                return $this->getItem("","item_link",$id,$lang);
	}

	// Returns the link for the specified item.
	function getItemLink($name)
	{
		return $this->getItem($name,"item_link");
	}


    // Returns the color class for the specified item.
	function getItemColor($name)
	{
		return $this->getItem($name,"item_color");
	}

	// Returns the icon for the specified item.
	function getItemIcon($name)
	{
		return $this->getItem($name,"item_icon");
	}

	// Returns the html for the specified item.
	function getItemHtml($name)
	{
		return $this->getItem($name,"item_html");
	}

	// Returns the html for the specified item.
	function getItem($name,$param="",$id=0,$lang="")
	{
           static $results = array();
           if ($id != 0) {
              if (!isset($results[$id.$lang])) {
                $item = $this->getItemFromCache($name,$id,$lang);
                if (!$item) {
                  $this->sql->query("SELECT * FROM " . item_cache_table . " WHERE item_id = '" . $id . "' AND item_lang = '" . $lang . "'");
                  $results[$id.$lang] = $this->sql->fetch_record($this->sql->query_id);
                  $results[$results[$id.$lang]["item_name"]] = $results[$id.$lang];
                  $this->sql->free_result($this->query_id);
                  $this->cacheItem($results[$id.$lang]) ;
                  unset($this->sql->query_id);
                } else {
                  $results[$id.$lang]=$item;
                  $results[$results[$id.$lang]["item_name"]] = $results[$id.$lang];
                }
                }
                if ($param == "")
                    return $results[$id.$lang];
                else
                    return $results[$id.$lang][$param];
           }

          if (!isset($results[$name])) {
            $item = $this->getItemFromCache($name,$id,$lang);
            if (!$item) {
                $this->sql->query("SELECT * FROM " . item_cache_table . " WHERE item_name = '" . addslashes($name) . "'");
                $results[$name] = $this->sql->fetch_record($this->sql->query_id);
                $results[$results[$name]["item_id"].$results[$name]["item_lang"]] = $results[$name];
                $this->cacheItem($results[$name]);
                $this->sql->free_result($this->query_id);
                unset($this->sql->query_id);
            } else {
                  $results[$id.$lang]=$item;
                  $results[$results[$id.$lang]["item_name"]] = $results[$id.$lang];
            }
            }
          if ($param == "")
              return $results[$name];
          else
              return $results[$name][$param];
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
    	global $eqdkp;
    	$errorfile = '/../templates/popup-error.tpl';
    	if(strtolower($eqdkp->config['default_game']) == 'runesofmagic')
    	{
    		$errorfile = '/../templates/popup-error_rom.tpl';
    	}
    	elseif(in_array('wowhead', $GLOBALS['prio']))
    	{
    		$errorfile = '/../templates/popup-error_wowhead.tpl';
    	}
    	elseif(in_array('buffed', $GLOBALS['prio']))
    	{
    		$errorfile = '/../templates/popup-error_blasc.tpl';
    	}
        // Objet non trouvé, on renvoi un objet gris et une erreur :)
        $item['name'] = $name;
        $item['id'] = '0';
        $item['lang'] = 'na';
        $item['color'] = 'greyname';
        $item['icon'] = DEFAULT_ICON;
        $template_html = trim(file_get_contents(dirname(__FILE__) . $errorfile));
        $item['html'] = str_replace('{INFO_SITE}', 'Itemstats (http://itemstats.free.fr)', $template_html);
        return ($item);
    }

    function getItemFromCache($name,$id=0,$lang="") {
      if (isset($_GET["nocache"])) return false;
      //=============== DEBUT XML_CACHE =============================================================
        // On vérifie qu'il y a pas un fichier dans xml_cache
        // POUR LA RECHERCHE DE FICHIER CACHE, il faut encodé le nom en UTF8 sinon la recherche est mauvaise quand le nom comporte des accents.
        if ($id != 0)
        $search_name = utf8_encode($id.$lang);
        else
        $search_name = utf8_encode($name);

        // On fait attention aux failles de sécurité
        $search_name = str_replace("..", ".", $search_name);
        $search_name = str_replace("/", "", $search_name);
        $search_name = str_replace("\\", "", $search_name);

        if (debug_mode == true)
        {
            echo "Check on cache : <br/>";
            echo "search in :" . path_cache . $search_name . "<br/>";
        }

        // On vérifie si il y a pas un fichier cache pour cet objet, ca permet de créer les objets qu'on a envie.
        $file = dirname(__FILE__) . '/../'.path_cache . $search_name. ".itemcache";
        if (file_exists($file) && (time()-filemtime($file)<=86400)) {
            if (debug_mode == true)
                echo "Object found !<br/><br/>";

            //echo "Fichier cache trouvé !<br/>";
                   $item = file_get_contents($file);
                   if (!$item)
                      return false;
                   $item = unserialize($item);
                   if ($item['html'])
                      return $item;
            }
            return false;

        //=============== FIN XML_CACHE ===============================================================

    }

    function cacheItem($item)
    {
      if (isset($_GET["nocache"])) return false;

        $f=@fopen(dirname(__FILE__) . '/../'.path_cache . utf8_encode($item['item_name']). ".itemcache","w");
        if (!$f)
        {
          return false;
        } else
        {
          fwrite($f,serialize($item));
          fclose($f);
        }

        $f=@fopen(dirname(__FILE__) . '/../'.path_cache . utf8_encode($item['item_id'].$item['item_lang']). ".itemcache","w");
        if (!$f)
        {
          return false;
        } else
        {
          fwrite($f,serialize($item));
          fclose($f);
          return true;
        }
    }

    function deleteItemFromCache($item)
    {
    	// Delete any existing entry for this item.
    	$sql = "DELETE FROM " . item_cache_table . " WHERE item_name = '" . mysql_escape_string($item) . "'" ;
        $this->sql->query($sql);


        return;
    }
}
?>