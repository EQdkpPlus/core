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

include_once(dirname(__FILE__) . '/config.php');
include_once(dirname(__FILE__) . '/includes/itemcache.php');
include_once(dirname(__FILE__) . '/config_itemstats.php');


global $eqdkp;

if(strtolower($eqdkp->config['default_game']) == 'wow')
{
	include_once(dirname(__FILE__) . '/includes/allakhazam.php');
	include_once(dirname(__FILE__) . '/includes/thottbot.php');
	include_once(dirname(__FILE__) . '/includes/wowdbu.php');
	include_once(dirname(__FILE__) . '/includes/judgehype.php');
	include_once(dirname(__FILE__) . '/includes/blasc.php');
	include_once(dirname(__FILE__) . '/includes/wowhead.php');
	include_once(dirname(__FILE__) . '/includes/armory.php');
}
elseif (strtolower($eqdkp->config['default_game']) == 'lotro')
{
	include_once(dirname(__FILE__) . '/includes/lotro_allakhazam.php');
}
elseif (strtolower($eqdkp->config['default_game']) == 'runesofmagic')
{
	include_once(dirname(__FILE__) . '/includes/rom_blasc.php');
}
elseif (strtolower($eqdkp->config['default_game']) == 'aion');
{
	include_once(dirname(__FILE__) . '/includes/aiondatabase.php');
}

function getStrCssStyle()
{
    if (defined('tooltip_css'))
    {
        return ("/templates/" . tooltip_css);
    }
    else
        return ("/templates/itemstats.css");
}

function getStrTooltipStyle($eqdkpplus='')
{
  if($eqdkpplus){
    if (defined('tooltip_js')){
      if (tooltip_js == 'overlib'){
        return ("/overlib/overlib.js");
      }
    }
  }else{
    if (defined('tooltip_js'))
    {
      if (tooltip_js == 'overlib')
      {
        return ("/overlib/overlib.js");
      } else {
        return ("/tooltips_light/tooltips_light.js");
      }
    }else{
      return ("/overlib/overlib.js");
    }
  }
}

function getViewitemLink($item_name, $type, $icon_lsize, $path_itemstats)
{
    if ($type == 'item')
        $add_type = '&type=item';
    else if ($type == 'itemico')
        $add_type = '&type=itemico';
    else
        $add_type = '';

    if ($icon_lsize != '')
        $add_icon_lsize = "&size=" . $icon_lsize;
    else
        $add_icon_lsize = '';

    $html = '<script src="' . $path_itemstats . '/viewitem.php?item=' . $item_name . $add_type . $add_icon_lsize . '" type="text/javascript"></script>';

    return ($html);
}

	/**
 * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
 *
 * Example of use. If you want to know if a file is saved in UTF8 format :
 * <code> $array = file('one file.txt');
 * $isUTF8 = isUTF8($array);
 * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
 * else --> we are in UTF8 :)
 * @param mixed A string, or an array from a file() function.
 * @return boolean
 */
if (!function_exists('isUTF8'))
{
	function isUTF8($str)
	{
	    $c=0; $b=0;
	    $bits=0;
	    $len=strlen($str);
	    for($i=0; $i<$len; $i++)
	    {
	        $c=ord($str[$i]);
	        if($c > 128)
	        {
	            if(($c >= 254)) return false;
	            elseif($c >= 252) $bits=6;
	            elseif($c >= 248) $bits=5;
	            elseif($c >= 240) $bits=4;
	            elseif($c >= 224) $bits=3;
	            elseif($c >= 192) $bits=2;
	            else return false;
	            if(($i+$bits) > $len) return false;
	            while($bits > 1)
	            {
	                $i++;
	                $b=ord($str[$i]);
	                if($b < 128 || $b > 191) return false;
	                $bits--;
	            }
	        }
	    }
	    return true;
	}
}

/*
function convert_utf8_to_Iso($in)
{
   if (is_array($in))
   {
        foreach ($in as $key => $value)
        {
            $out[convert_utf8_to_Iso($key)] = convert_utf8_to_Iso($value);
        }
        $in = $out ;
    }else
    {
		$c = 0;
    	while (isUTF8($in) )
   		{
		     $in = utf8_decode($in);
		     $c++;
		    if($c>10)
		    {
		       break;
		    }
   		}
     }

  return $in;

} // fixEncoding
*/


function convert_utf8_to_Iso($in)
{
 	for($i=0; $i<=10; $i++)
 	{
 		if (is_array($in))
 		{
            foreach ($in as $key => $value)
            {
                $out[convert_utf8_to_Iso($key)] = convert_utf8_to_Iso($value);
            }
            $in = $out ;
        }else
        {
        	if (!function_exists('mb_detect_encoding'))
        	{
		 		if (isUTF8($in) )
				{
					$in = utf8_decode($in);
				}
        	}else
        	{
		 		if (mb_detect_encoding($in) == 'UTF-8' )
				{
					$in = utf8_decode($in);
				}
        	}
        }
	 }

	 return $in;

} // fixEncoding


// The main interface to the ItemStats module.
class ItemStats
{
	var $item_cache;
    var $info_site;
    var $connected;

    var $info_site_allakhazam;
    var $info_site_thottbot;
    var $info_site_judgehype;
    var $info_site_wowdbu;
    var $info_site_blasc;
    var $info_site_wowhead;

    // Constructor
	function ItemStats($bNewConnection = false, $openConnection = 0)
	{

    	global $eqdkp ;
		if (debug_mode == true)
        {
            echo "<br/><br/>Itemstats class initialized<br/>";
            echo "Preferences : ==============================================<br/>";
            echo "item_lang_default : " . item_lang_default . "<br/>";
            if (displayitemstatslink == true)
            	echo "displayitemstatslink : true<br/>";
            else
            	echo "displayitemstatslink : false<br/>";
            echo "path_cache : " . path_cache . "<br/>";
            if (automatic_search == true)
                echo "automatic_search : true<br/>";
            else
                echo "automatic_search : false<br/>";
            echo "Integration_mode : " . integration_mode . "<br/>";
			echo "openConnection : " . $openConnection . "<br/>";
            echo "Tooltip_css : " . tooltip_css . "<br/>";
            echo "Tooltip_js : " . tooltip_js . "<br/>";
            if (debug_mode == true)
                echo "debug_mode : true<br/>";
            else
                echo "debug_mode : false<br/>";
            if (defined('path_itemstats'))
                echo "path_itemstats : " . path_itemstats . "<br/>";
            echo "priority list : <br/>";
            print_r($GLOBALS["prio"]);
            echo "<br/>============================================================<br/><br/>";
        }

        if ($openConnection == 2 || ($openConnection == 0 && integration_mode == 'script'))
        {
            $this->connected = true;
            return;
        }

		$this->item_cache = new ItemCache($bNewConnection);
        $this->connected = $this->item_cache->connected;
        if ($this->connected == false)
            return;

        if (debug_mode == true)
        {
            if ($bNewConnection == true)
                echo "Itemstats connected to database with NEW connection activated.<br/><br/>";
            else
                echo "Itemstats connected to database WITHOUT new connection activated.<br/><br/>";
        }

        if(strtolower($eqdkp->config['default_game']) == 'wow')
		{
			$this->info_site_allakhazam = new ParseAllakhazam();
      $this->info_site_thottbot = new ParseThottbot();
      $this->info_site_judgehype = new ParseJudgehype();
      $this->info_site_wowdbu = new ParseWowdbu();
      $this->info_site_blasc = new ParseBlasc();
			$this->info_site_wowhead = new ParseWowhead();
			$this->info_site_armory = new ParseArmory();
		}
		elseif (strtolower($eqdkp->config['default_game']) == 'lotro')
		{
			$this->info_site_allakhazam = new ParseAllakhazam();
		}
		elseif (strtolower($eqdkp->config['default_game']) == 'runesofmagic')
		{
			$this->info_site_blasc = new ParseBlasc();
		}
		elseif (strtolower($eqdkp->config['default_game']) == 'aion')
		{
			$this->info_site_aion = new AionDatabase();
		}

        //========================================================================================================

		// Setup a ghetto destructor.
		register_shutdown_function(array(&$this, '_ItemStats'));
	}

	// Ghetto Destructor
	function _ItemStats()
	{
		if (isset($this->item_cache))
		{
			$this->item_cache->close();

			if(strtolower($eqdkp->config['default_game']) == 'wow')
			{
		        $this->info_site_allakhazam->close();
		        $this->info_site_thottbot->close();
		        $this->info_site_judgehype->close();
		        $this->info_site_wowdbu->close();
		        $this->info_site_blasc->close();
				$this->info_site_wowhead->close();
				$this->info_site_armory->close();
			}
			elseif (strtolower($eqdkp->config['default_game']) == 'lotro')
			{
				$this->info_site_allakhazam->close();
			}
			elseif (strtolower($eqdkp->config['default_game']) == 'runesofmagic')
			{
				$this->info_site_blasc->close();
			}
			elseif (strtolower($eqdkp->config['default_game']) == 'aion')
			{
				$this->info_site_aion->close();
			}
		}
	}

    function getItemForDisplay($item_name, $type, $icon_lsize, $search_objects, $force_integration_mode = 0, $item_id = 0)
    {
    	global $eqdkp_root_path;
       	if (debug_mode == true)
        {
            echo "New getItemForDisplay : <br/>";
            echo "item_name : " . $item_name . "<br/>";
            echo "item_id : ". $item_id . "<br />";
            echo "type : " . $type . "<br/>";
            echo "icon_lsize : " . $icon_lsize . "<br/>";
            echo "search_objects : " . $search_objects . "<br/>";
			echo "force_integration_mode : " . $force_integration_mode . "<br/>";
        }
        $item_name = cleanHTML($item_name);

        if ($force_integration_mode == 2 || ($force_integration_mode == 0 && integration_mode == 'script'))
        {
            $html = getViewitemLink($item_name, $type, $icon_lsize, "{PATH_ITEMSTATS}");
            return ($html);
        }

		if (!isset($this->item_cache))
		{
	        if (debug_mode == true)
	            echo "==> GROSSE ERREUR : Utilisation du Itemcache sans qu'il soit chargï¿½, ni connectï¿½<br/>" ;
		}
        $params = explode('/',$item_name);
        $item_name=array_shift($params);
        // Get the proper name of this item.
        $old_item_name = $item_name;
        $item_search = ($item_id) ? $item_id : $item_name;
		$item_name = $this->getItemName($item_name, $search_objects);

        if (debug_mode == true)
            echo "=> getItemName (real case name) : " . $item_name . "<br/>" ;

        // On regle la taille
        if ($icon_lsize == '')
            $icon_size = '40';
        else if ($icon_lsize == '=0')
            $icon_size = '10';
        else if ($icon_lsize == '=1')
            $icon_size = '20';
        else if ($icon_lsize == '=2')
            $icon_size = '30';
        else if ($icon_lsize == '=3')
            $icon_size = '40';
        else if ($icon_lsize == '=4')
            $icon_size = '50';
        else if ($icon_lsize == '=5')
            $icon_size = '60';
        else
            $icon_size = '40';

		$item_tooltip_html = $this->getItemTooltipHtml($item_name,false,$old_item_name);

		// If this item has a link to the info site, add this link to the HTML.  If it doesn't have a link, it
		// means the item hasn't been found yet, so put up a link to the update page instead.
		$item_link = $this->getItemLink($item_name);
		if (empty($item_link))
			$item_link = $eqdkp_root_path.'itemstats/updateitem.php?item=' . urlencode(urlencode($item_name));

        if ($type == 'item')
        {
		    // Initialize the html.
		    $item_html = '[' . $item_name . ']';

		    // Get the color of this item and apply it to the html.
		    $item_color = $this->getItemColor($item_name);
		    if (!empty($item_color))
		    {
			    $item_html = "<a " . $item_tooltip_html . " class=\"forumitemlink\"  target=\"_blank\" href=\"" . $item_link . "\"><span class=\"" . $item_color . "\">" . $item_html . "</span></a>";
			}
        }
        else // Balise Itemico
        {
            // Recuperation du lien de l'image
            $item_html = $this->getItemIconLink($item_name);

            $item_html = "<a id=\"".$item_name."\" ". $item_tooltip_html ." href=\"" . $item_link . "\"  class=\"forumitemlink\"><div class=\"iconmedium\" style=\"float:left;background-image: url(".$item_html.");\" ><div class=\"tile\"></div></div></a>";
	    	//<img src='" . $item_html . "' width='" . $icon_size . "' height='" . $icon_size . "' border='0' />";
        }



		if (defined('displayitemstatslink') && displayitemstatslink == true){
            $item_html = str_replace("{ITEMSTATS_LINK}", "<br/><p class=\"textitemstats\">itemstats.free.fr</p>", $item_html);}
        else{
            $item_html = str_replace("{ITEMSTATS_LINK}", "", $item_html);}

        // For Guild Heberg :
        // $item_html = str_replace("''", "\\'", $item_html);
        if (sizeof($params) >0)
        {
          foreach ($params as $param)
          {
            if ($param == 'c')
            {
                $item = $this->item_cache->getItem($item_name);
                $item_tooltip_html = $this->getItemTooltipHtml($item['item_id'].'_reagent'.$item['item_lang'],false,$item['item_id'].'_reagent',false,"",$item['item_id'].'_reagent'.$item['item_lang'],$item['item_lang']);
                $item_html .= " <a " . $item_tooltip_html . " class=\"forumitemlink\"><span class=\"" . $item_color . "\">Craft</span></a>";
            }
          }
        }

        if (debug_mode == true)
            echo "====== END getItemForDisplay ==================================================" ;

        return ($item_html);
    }

    // Returns the properly capitalized name for the specified item.  If the update flag is set and the item is
	// not in the cache, item data item will be fetched from an info site
	function getItemName($name, $update = false, $empty = false)
	{
        // Check if it is an id
        $id_object = substr($name, 0, strlen($name) - 2);
        $lang_object = substr($name, strlen($name) - 2, 2);

        if ($lang_object != 'fr' && $lang_object != 'en' && $lang_object != 'de' && $lang_object != 'es' && $lang_object != 'zh' && $lang_object != 'ko')
        {
            $id_object = $id_object . $lang_object;
            $lang_object = item_lang_default;
        }

        if (is_numeric($id_object))
        {
            if (debug_mode == true)
            {
                echo "We find a Blizzard Item Id, getItemNameFromId : <br/>";
                echo "id_object :" . $id_object . "<br/>";
                echo "lang_object :" . $lang_object . "<br/>";
                echo "update :" . $update . "<br/><br/>";
            }

            $proper_name = $this->getItemNameFromId($id_object, $lang_object, $update, true);
            if (!empty($proper_name))
                return ($proper_name);
        } else {
        	if (!isset($_GET["forcedb"])) $proper_name = $this->item_cache->getItemName($name);
        	if (empty($proper_name) && $update) {
            	$this->updateItemFromName($name);
            	$proper_name = $this->item_cache->getItemName($name, true);
            }
        }

		return empty($proper_name) ? (!$empty ? $name : '') : $proper_name;
	}

	// Returns the properly capitalized name for the specified item.  If the update flag is set and the item is
	// not in the cache, item data item will be fetched from an info site
	function  getItemNameFromId($id, $lang_object = '', $update = false, $empty = false)
	{
        if ($lang_object == '')
        {
        	$lang_object = item_lang_default;
        }

        if (!isset($_GET["forcedb"]))
        {
        	$proper_name = $this->item_cache->getItemNameFromId($id, $lang_object);
        }

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($proper_name) && $update)
		{
			$this->updateItemFromId($id, $lang_object);
			$proper_name = $this->item_cache->getItemNameFromId($id, $lang_object, true);
		}

		return empty($proper_name) ? (!$empty ? $id . $lang_object : '') : $proper_name;
    }


	// Returns the link to the info site for the specified item.  If the update flag is set and the item is not in
	// the cache, item data will be fetched from an info site
	function    getItemLinkFromId($id, $lang_object, $update = false)
	{
        if ($lang_object == '')
            $lang_object = item_lang_default;

		$link = $this->item_cache->getItemLinkFromId($id, $lang_object);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($link) && $update)
		{
			$this->updateItem($name);
			$link = $this->item_cache->getItemLinkFromId($name, true);
		}

		return $link;
	}

	// Returns the link to the info site for the specified item.  If the update flag is set and the item is not in
	// the cache, item data will be fetched from an info site
	function    getItemLink($name, $update = false)
	{
		$link = $this->item_cache->getItemLink($name);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($link) && $update)
		{
			$this->updateItem($name);
			$link = $this->item_cache->getItemLink($name, true);
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
			$color = $this->item_cache->getItemColor($name, true);
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
			$icon = $this->item_cache->getItemIcon($name, true);
		}

		// If the icon was found, create a link by merging it with the icon path and extension.
		if (!empty($icon))
		{
			if (($GLOBALS["prio"][0] == 'armory' ) or ($GLOBALS["prio"][0] == 'wowhead' ) )
			{
				$icon = strtolower($icon) ;
			}

			$icon_link = ICON_STORE_LOCATION . htmlspecialchars($icon, ENT_QUOTES) . ICON_EXTENSION;
		}
        else
            $icon_link = ICON_STORE_LOCATION . DEFAULT_ICON . ICON_EXTENSION;

		return $icon_link;
	}

	// Returns the html for the specified item.  If the update flag is set and the item is not in the cache, the
	// item will be fetched from an info site
	function getItemHtml($name, $update = false, $id=0, $lang='')
	{
        if($lang == "") {
            $lang = item_lang_default;
        }
		$html = $this->item_cache->getItem($name,'item_html',$id,$lang);

		// If this item was not found and the update flag is set, try to fetch the item data from an info site.
		if (empty($html) && $update)
		{
			if($id > 0) {
				$search = $id.$lang;
			} else {
				$search = $name;
			}
			$this->updateItem($search);
			$html = $this->item_cache->getItem($name,'item_html',$id,$lang,true);
		}

		// If the item was found, update the icon path in the HTML.
		if (!empty($html))
		{
			if($id > 0) {
				$nam = $this->getItemNameFromId($id,'',false,true);
				$name = ($nam) ? $nam : $name;
			}
			$html = str_replace(ICON_LINK_PLACEHOLDER, $this->getItemIconLink($name), $html);
		}

		return $html;
	}

	// Returns the overlib tooltip html for the specified item.  If the update flag is set and the item is not in
	// the cache, the item will be fetched from an info site
	function getItemTooltipHtml($name, $update = false,$oldname="",$id=0,$lang="")
	{
          if (debug_mode == true) {
              echo "getItemTooltipHtml : <br/>";
                echo "name :" . $name . "<br/>";
                echo "update :" . $update . "<br/>";
                echo "oldname :" . $oldname . "<br/>";
                echo "id : ".$id."<br /><br/>";

          }
		// Retrieve the item data from the cache.
		if (integration_mode == 'ajax' && defined('tooltip_js') && tooltip_js == 'light')
		{
                  if ($id == 0)
                     $item = $this->item_cache->getItem($name);
                  else
                  $item = $this->item_cache->getItem("","",$id,$lang);
                  $html = 'onmouseover="return doAjaxTooltip(event,\''.$item["item_id"].$item["item_lang"].'\')" onmouseout="hideTip()"';
                  return  $html;
        }
      		$html = $this->getItemHtml($name, $update, $id, $lang);
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

            if (defined('tooltip_js') && tooltip_js == 'light') {
               $html = 'onmouseover="return doTooltip(event,\'' . $html . '\')" onmouseout="hideTip()"';
            } else
                $html = 'onmouseover="return overlib(' . "'" . $html . "'" . ',VAUTO,HAUTO,FULLHTML,WRAP);" onmouseout="return nd();"';
		}

		return $html;
	}



    function updateItem($object_str)
    {
        // Check if it is an id
        $id_object = substr($object_str, 0, strlen($object_str) - 2);
        $lang_object = substr($object_str, strlen($object_str) - 2, 2);

        if ((int)$id_object == 0 || ($lang_object != 'fr' && $lang_object != 'en' && $lang_object != 'de' && $lang_object != 'es' && $lang_object != 'zh' && $lang_object != 'ko'))
        {
            $id_object = $id_object . $lang_object;
            $lang_object = item_lang_default;
        }

        $objectid_to_check = $id_object;
        if (debug_mode == true)
        {
            echo "id_object : ".$id_object."<br/>";
            echo "lang_object :" . $lang_object . "<br/>";
        }
        if (strstr($id_object, ',') !== false)
        	$objectid_to_check = str_replace(",", ".", $objectid_to_check);
        if (is_numeric($objectid_to_check)) {
            $result = $this->updateItemFromId($id_object, $lang_object); }
        else
            $result = $this->updateItemFromName($object_str, $lang_object);

        return ($result);
    }

	// Retrieves the data for the specified item from an info site and caches it.
	function updateItemFromName($name,$language='de')
	{
        if ($name == '')
            return ($name);
        // Retrives the data from an information site.
        // On init la chose :)
        $item['html'] = '';

        if (debug_mode == true)
        {
            echo "updateItemFromName : <br/>";
            echo "name :" . $name . "<br/>";
        }


        //=============== DEBUT XML_CACHE =============================================================
        // On vérifie qu'il y a pas un fichier dans xml_cache
        // POUR LA RECHERCHE DE FICHIER CACHE, il faut encodé le nom en UTF8 sinon la recherche est mauvaise quand le nom comporte des accents.
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
        if (file_exists(path_cache . $search_name))
        {
            if (debug_mode == true)
                echo "Object found !<br/><br/>";

            //echo "Fichier cache trouvé !<br/>";
	        if ($fp = fopen(dirname(__FILE__) . '/' . path_cache . '/' . $search_name, "r"))
            {
                $data = "";
                while (!feof($fp))
                    $data .= fread($fp, 4096);
                $value = explode("|", $data);
                if (count($value) > 3)
                {
					$item['name'] = $name;
                    $item['id'] = 0;
                    $item['lang'] = 'fr';
                    $item['link'] = $value[0];
                    $item['color'] = $value[1];
                    $item['icon'] = $value[2];
                    $item['html'] = $value[3];

                    $item['html'] = trim($item['html']);
                    if (substr($item['html'], strlen($item['html']) - 6, 6) == '</div>')
                        $item['html'] = substr($item['html'], 0, strlen($item['html']) - 6) . '{ITEMSTATS_LINK}</div>';

                    // Build the final HTML by merging the template and the data we just prepared.
                    $template_html = trim(file_get_contents(dirname(__FILE__) . '/templates/popup.tpl'));
			        $item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
                }
                //echo "--> Erreur dans le fichier <br/>";
            }
	    }
        if (debug_mode == true)
        {
            if ($item['html'] == '')
                echo "Cache Object not found !<br/><br/>";
        }
        //=============== FIN XML_CACHE ===============================================================



        //===  SEARCH OBJECT IN DATABASE  ========================================================================
        if ($item['html'] == '')
        {
            for ($ct = 0; isset($GLOBALS["prio"][$ct]); $ct++)
            {
                if (debug_mode == true)
                    echo "Search on the site : " . $GLOBALS["prio"][$ct] . "<br/>";

                if ($GLOBALS["prio"][$ct] == 'allakhazam')
                    $item = $this->info_site_allakhazam->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'wowdbu')
                    $item = $this->info_site_wowdbu->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'judgehype')
                    $item = $this->info_site_judgehype->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'thottbot')
                    $item = $this->info_site_thottbot->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'blasc')
                    $item = $this->info_site_blasc->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'buffed')
                    $item = $this->info_site_blasc->getItem($name);
        		else if ($GLOBALS["prio"][$ct] == 'wowhead')
        			$item = $this->info_site_wowhead->getItem($name);
        		else if ($GLOBALS["prio"][$ct] == 'aion')
        			$item = $this->info_site_aion->getItem($name);
                else if ($GLOBALS["prio"][$ct] == 'armory')
                {
	                for ($lg = 0; isset($GLOBALS["armory_lang"][$lg]); $lg++)
	                {
	                    $item = $this->info_site_armory->getItem($name,$GLOBALS["armory_lang"][$lg]);
	                    if (!empty($item['link']))
	                      break;
	                  }

                }

                if (!empty($item['link']))
                    break;
            }
        }
        //========================================================================================================

        if (empty($item['link']))
        {
            if (debug_mode == true)
                echo "Item not found, getEmptyItem on " . $name . "<br/>";
            $item = $this->item_cache->getEmptyItem($name);
        }

		// If the item wasn't found, and we have something cached already, don't overwrite with lesser data.
		$cached_link = $this->getItemLink($name);
		if (!empty($item['link']) || empty($cached_link))
		{
            if (debug_mode == true)
                echo "Save item data on the Item_cache<br/>";
			// If the data was loaded succesfully, save it to the cache.
			$result = $this->item_cache->saveItem($item);
            if ($result == false)
            {
                $item = $this->item_cache->getEmptyItem($name);
                $result = $this->item_cache->saveItem($item);
            }
		}
        else
            $result = 0;

		return $result;
	}

    // Retrieves the data for the specified item from an info site and caches it.
	function updateItemFromId($id, $lang)
	{
        // Retrives the data from an information site.
        // On init la chose :)
        $item['html'] = '';

        if (debug_mode == true)
        {
            echo "updateItemFromId : <br/>";
            echo "id :" . $id . "<br/>";
            echo "lang :" . $lang . "<br/>";
        }

        for ($ct = 0; isset($GLOBALS["prio"][$ct]); $ct++)
        {
            if (debug_mode == true)
                echo "Search on the site : " . $GLOBALS["prio"][$ct] . "<br/>";

            if ($GLOBALS["prio"][$ct] == 'allakhazam')
			{
				for ($ct2 = 0; isset($GLOBALS["allakhazam_lang"][$ct2]); $ct2++)
				{
					if (substr($GLOBALS["allakhazam_lang"][$ct2], 0, 2) == $lang)
					{
						$item = $this->info_site_allakhazam->getItemId($id, $GLOBALS["allakhazam_lang"][$ct2]);
						break;
					}
				}
			}
            else if ($GLOBALS["prio"][$ct] == 'wowdbu' && $lang == 'fr')
                $item = $this->info_site_wowdbu->getItemId($id);
            else if ($GLOBALS["prio"][$ct] == 'judgehype' && $lang == 'fr')
                $item = $this->info_site_judgehype->getItemId($id);
            else if ($GLOBALS["prio"][$ct] == 'thottbot' && $lang == 'en')
                $item = $this->info_site_thottbot->getItemId($id);
            else if ($GLOBALS["prio"][$ct] == 'blasc')
                $item = $this->info_site_blasc->getItemId($id,$lang);
            else if ($GLOBALS["prio"][$ct] == 'buffed')
                $item = $this->info_site_blasc->getItemId($id,$lang);
	    	else if ($GLOBALS["prio"][$ct] == 'wowhead')
                $item = $this->info_site_wowhead->getItemId($id);
            else if ($GLOBALS["prio"][$ct] == 'aion') {
            	$item = $this->info_site_aion->getItemId($id); }
            else if ($GLOBALS["prio"][$ct] == 'armory')
                $item = $this->info_site_armory->getItemId($id,$lang);
            if (!empty($item['link']))
                break;
        }
        //========================================================================================================

        if (empty($item['link']))
        {
            if (debug_mode == true)
                echo "Item not found, getEmptyItem on " . $id . $lang . "<br/>";
            $item = $this->item_cache->getEmptyItem($id . $lang);
        }

		// If the item wasn't found, and we have something cached already, don't overwrite with lesser data.
		$cached_link = $this->getItemLinkFromId($id, $lang);
		if (!empty($item['link']) || empty($cached_link))
		{
            if (debug_mode == true)
                echo "Save item data on the Item_cache<br/>";
			// If the data was loaded succesfully, save it to the cache.
			$result = $this->item_cache->saveItem($item);
			if ($item['reagent']) {
                          $item['id'] = $item['id'].'_reagent';
                          $item['name'] = $item['id'].$item['lang'];
                          $item['html'] = $item['reagent'] ;
                          $result2 = $this->item_cache->saveItem($item);
                        }
            if ($result == false)
            {
                $item = $this->item_cache->getEmptyItem($name);
                $result = $this->item_cache->saveItem($item);
            }

            //if (isset($item))
			// If the data was loaded succesfully, save it to the cache.
		}
        else
            $result = 0;
		return $result;
    }

}//itemstats class

function cleanHTML($string)
{
    if (function_exists("mb_convert_encoding"))
        $string = mb_convert_encoding($string, "ISO-8859-1", "HTML-ENTITIES");
    else
    {
       $conv_table = get_html_translation_table(HTML_ENTITIES);
       $conv_table = array_flip($conv_table);
       $string = strtr ($string, $conv_table);
       //$string = preg_replace('/\&\#([0-9]+)\;/me', "chr('\\1')", $string);
       $string = preg_replace('/&#(\d+);/me', "chr('\\1')", $string);
    }
    return ($string);
}

function cleanRegExp($string)
{
    $string = quotemeta($string);
    $string = str_replace("#", "\#", $string);
    return ($string);
}

?>