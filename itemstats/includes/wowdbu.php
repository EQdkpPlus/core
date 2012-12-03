<?php
/*
+---------------------------------------------------------------+
|       Itemstats FR Core
|
|       Yahourt
|       http://itemstats.free.fr
|       itemstats@free.fr
|
|       Thorkal
|       EU Elune / Horde
|       www.elune-imperium.com
+---------------------------------------------------------------+
*/
                                                                                               
include_once(dirname(__FILE__) . '/xmlhelper.php');
include_once(dirname(__FILE__) . '/urlreader.php');
                   
// The main interface to the Wowdbu
class ParseWowdbu
{
	// Constructor
	function ParseWowdbu()
	{
	}

	// Cleans up resources used by this object.
	function close()
	{
	}

    function cleanString($text)
    {
        // On replace l'encodage, car une partie est codé en UTF8 (pour une raison inconnue)
        $text = utf8_decode($text);
        return ($text);
    }

    function buildObject($name_lower, $item_color, $item_idcolor, $html)
    {
        $object_data = $html;

        // On met le tout en forme
        $position_start_data_object = strpos($object_data, "<FONT COLOR='" . $item_idcolor . "'>" . $name_lower);
        if ($position_start_data_object === false)
            return (false);
        $position_end_data_object = strpos($object_data, "</FONT></TD></TR>", $position_start_data_object);
        if ($position_end_data_object === false)
            return (false);
        $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

        $object_data = str_replace("<FONT COLOR='" . $item_idcolor . "'>", "<span class='iname'><span class='" . $item_color . "'>", $object_data);
        $object_data = str_replace("<FONT COLOR=#00FF00>", "<span class='itemeffectlink'>", $object_data);
        // Le &count ne fonctionne pas en PHP4 :(, str_replace(..., ..., $count = 1);
        // Vu que ca ne fonctionne pas, ca remplace trop
        $object_data = str_replace("</FONT><BR>", "</span></span><br />", $object_data);
        $object_data = str_replace("<BR>", "<br />", $object_data);

        // On est obligé de remettre le "</span><br />" à la place du "</span></span><br />" apres les "itemeffectlink"
        $position_object = -1;
        while (($position_object = strpos($object_data, "<span class='itemeffectlink'>", $position_object + 1)) != false)
        {
            $position_start_data_object = strpos($object_data, "</span></span><br />", $position_object);
            if ($position_start_data_object === false)
                return (false);
            $position_end_data_object =  $position_start_data_object + 20;
            $object_data = substr_replace($object_data, "</span><br />", $position_start_data_object, $position_end_data_object - $position_start_data_object);
        }
        
        $temp_size_object_data = strlen($object_data);
        if (substr($object_data, $temp_size_object_data - 6, 6) == '<br />')
            $object_data = substr($object_data, 0, $temp_size_object_data - 6);
        return ($object_data);
    }

    function buildObjectFromTooltip($name_lower, $item_color, $item_idcolor, $html)
    {
        $object_data = $html;
        
        if (strstr(strtolower($object_data), "</font") == false)
            return (false);

        // On met le tout en forme
        $position_start_data_object = strpos($object_data, "<FONT COLOR=#" . $item_idcolor . "><B>" . addslashes(utf8_encode($name_lower)));
        if ($position_start_data_object === false)
            return (false);
        $object_data = str_replace('<B>', '', $object_data);
        $object_data = str_replace('</B>', '</span></span>', $object_data);
        $position_end_data_object = strlen($object_data);
        $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

        $object_data = str_replace("<FONT COLOR=#" . $item_idcolor . ">", "<span class='iname'><span class='" . $item_color . "'>", $object_data);
        $object_data = str_replace("<FONT COLOR=#00FF00>", "<span class='itemeffectlink'>", $object_data);
        // Le &count ne fonctionne pas en PHP4 :(, str_replace(..., ..., $count = 1);
        // Vu que ca ne fonctionne pas, ca remplace trop
        $object_data = str_replace("</FONT><BR>", "</span></span><br />", $object_data);
        $object_data = str_replace("<BR>", "<br />", $object_data);
        
        // On est obligé de remettre le "</span><br />" à la place du "</span></span><br />" apres les "itemeffectlink"
        $position_object = -1;
        while (($position_object = strpos($object_data, "<span class='itemeffectlink'>", $position_object + 1)) != false)
        {
            $position_start_data_object = strpos($object_data, "</span></span><br />", $position_object);
            if ($position_start_data_object === false)
                return (false);
            $position_end_data_object =  $position_start_data_object + 20;
            $object_data = substr_replace($object_data, "</span><br />", $position_start_data_object, $position_end_data_object - $position_start_data_object);
        }
        return ($object_data);
    }

	// Attempts to retrieve data for the specified item from Wowdbu.
	function getItem($name)
	{           
		// Ignore blank names.
		$name = trim($name);
		if (empty($name))
		{
			return null;
		}

		$item = array('name' => $name);

        // Nouvelle URL 7.html a la place de index.php
        // $data = itemstats_read_url('http://www.wowdbu.com/index.php?m=3&cat=7&pattern=' . urlencode($name));
		$data = itemstats_read_url('http://www.wowdbu.com/7.html?m=3&pattern=' . urlencode(utf8_encode($name)));

		// Look for a name match in the search result.        
        if (preg_match_all('#<A STYLE="color: \#(.*?);"(.*?)HREF=\'/(2,.*?,.*?.html)(.*?)doTooltip\(event,\'(.*?)\'\)".*?<IMG SRC=\'http://www.wowdbu.com/Interface/Icons/(.*?).png\'#s', $data, $matches))
		{ 
			foreach ($matches[0] as $key => $match)
			{   
                $name_lower = $name;
                
				// Extract the item's ID from the match.
				$item_id = $matches[3][$key];			
                $item_idcolor = $matches[1][$key];
                $item_color = $item_idcolor;
                $item_html = $matches[5][$key];
                $item_icon = $matches[6][$key];               

                // Traitement du nom de l'image (il n'est pas sous la même domination que Allakhazam & co)
                $item_icon = str_replace("inv", "INV", $item_icon);
                $item_icon = substr_replace($item_icon, strtoupper($item_icon[0]), 0, 1);
                for ($ct = 0; $ct + 1 < strlen($item_icon); $ct++)
                    if ($item_icon[$ct] == "_")
                        $item_icon = substr_replace($item_icon, strtoupper($item_icon[$ct + 1]), $ct + 1, 1);
                // Autre nom exotique, "Monsterscales" devient "MonsterScales"
                $item_icon = str_replace("Monsterscales", "MonsterScales", $item_icon);
                $item_icon = str_replace("Qirajidol", "QirajIdol", $item_icon);
                $item_icon = str_replace("Dispelmagic", "DispelMagic", $item_icon);

                // Choosing color :
                if ($item_color == 'ff8000')
                    $item_color = 'orangename';                                                         
                else if ($item_color == 'a335ee')
                    $item_color = 'purplename'; 
                else if ($item_color == '0070dd')
                    $item_color = 'bluename'; 
                else if ($item_color == '1eff00')
                    $item_color = 'greenname'; 
                else if ($item_color == 'ffffff')
                    $item_color = 'whitename'; 
                else if ($item_color == '9d9d9d')
                    $item_color = 'greyname'; 

                // On essaye de mettre en forme le code pris directement dans l'infobulle
                $object_data = $this->buildObjectFromTooltip($name_lower, $item_color, $item_idcolor, $item_html);
                if ($object_data == false)
                {                       
		            $object_data = itemstats_read_url('http://www.wowdbu.com/' . $item_id);

                    // On replace l'encodage, car une partie est codé en UTF8 (pour une raison inconnue)
                    $object_data = $this->cleanString($object_data);

                    $position_object = strpos(strtolower($object_data), strtolower($name_lower));
                    if ($position_object == false)
                        continue;
                    $name_lower = substr($object_data, $position_object, strlen($name_lower));

                    $position_start_data_object = strpos($object_data, "<TABLE ", $position_object);
                    if ($position_start_data_object == false)
                        continue;
                    $position_end_data_object = strpos($object_data, "</TABLE>", $position_start_data_object);
                    if ($position_end_data_object == false)
                        continue;
                    // On ressere l'étaux autour de notre objet
                    $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

                    // On met en forme l'objet
                    $object_data = $this->buildObject($name_lower, $item_color, $item_idcolor, $object_data);
                    if ($object_data == false)
                        continue;
                    //echo "MIS A JOUR par FICHE<br/>";
                }
                else
                {
                    $object_data = utf8_decode(stripcslashes($object_data));
                    //echo "MIS A JOUR par TOOLTIP<br/>";
                }

                // On rempli le tableau
                $item['name'] = $name;

                // A optimiser =)
                $myitemid = substr($item_id, strpos($item_id, ","), strlen($item_id));
                $myitemid = substr($myitemid, 0, strpos($item_id, ","));
                
                $item['id'] = $myitemid;
                $item['lang'] = 'fr';
                $item['color'] = $item_color;
                $item['icon'] = $item_icon;
                $item['link'] = 'http://www.wowdbu.com/' . $item_id;

                $object_data = $object_data . '{ITEMSTATS_LINK}';

                $item['html'] = "<div class='wowitem'>" . $object_data . "</div>";

                // Build the final HTML by merging the template and the data we just prepared.
                $template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
		        $item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);                

                //echo 'Objet mis à jour, à partir du site internet !<br/>' . $item['html'] . '<br/>';
                // On renvoi le tout :)
                return ($item);        
			}
		}
        unset($item['link']);
        //echo "Aucun objet trouvé !<br/>";
        return ($item);
     }

	// Attempts to retrieve data for the specified item with ID and from Wowdbu.
	function getItemId($item_id)
	{
        $item = array('id' => $item_id);

        $item_version_to_test = 0;
		
        while ($item_version_to_test <= 10)
        {
            $object_data = itemstats_read_url('http://www.wowdbu.com/2,' . $item_id . ',' . $item_version_to_test . '.html');
            if (strpos($object_data, "Item not found in database !") === false)
                break;
            $item_version_to_test++;    
        }
        if ($item_version_to_test == 11)
            return ($item);
            
        // On replace l'encodage, car une partie est codé en UTF8 (pour une raison inconnue)
        $object_data = $this->cleanString($object_data);

        $found = preg_match("/<IMG SRC='http:\/\/www.wowdbu.com\/Interface\/Icons\/(.*?)\.png'><\/TD><TD WIDTH='\*' class='name_mob_big'><FONT COLOR='(.*?)'>(.*?)<\/FONT>/i", $object_data, $matches);
        if ($found == false)
            return ($item);

        $item_icon = $matches[1];        
        $item_idcolor = $matches[2];
        $item_color = $item_idcolor;
        $name = $matches[3];
        $name_lower = strtolower($name);

        // Traitement du nom de l'image (il n'est pas sous la même domination que Allakhazam & co)
        $item_icon = str_replace("inv", "INV", $item_icon);
        $item_icon = substr_replace($item_icon, strtoupper($item_icon[0]), 0, 1);
        for ($ct = 0; $ct + 1 < strlen($item_icon); $ct++)
            if ($item_icon[$ct] == "_")
                $item_icon = substr_replace($item_icon, strtoupper($item_icon[$ct + 1]), $ct + 1, 1);
        // Autre nom exotique, "Monsterscales" devient "MonsterScales"
        $item_icon = str_replace("Monsterscales", "MonsterScales", $item_icon);
        $item_icon = str_replace("Qirajidol", "QirajIdol", $item_icon);
        $item_icon = str_replace("Dispelmagic", "DispelMagic", $item_icon);

        // Choosing color :
        if ($item_color == 'ff8000')
            $item_color = 'orangename';                                                         
        else if ($item_color == 'a335ee')
            $item_color = 'purplename'; 
        else if ($item_color == '0070dd')
            $item_color = 'bluename'; 
        else if ($item_color == '1eff00')
            $item_color = 'greenname'; 
        else if ($item_color == 'ffffff')
            $item_color = 'whitename'; 
        else if ($item_color == '9d9d9d')
            $item_color = 'greyname'; 


        $position_object = strpos(strtolower($object_data), strtolower($name_lower));
        $name_lower = substr($object_data, $position_object, strlen($name_lower));

        $position_start_data_object = strpos($object_data, "<TABLE ", $position_object);
        $position_end_data_object = strpos($object_data, "</TABLE>", $position_start_data_object);

        // On ressere l'étaux autour de notre objet
        $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

        // On met en forme l'objet
        $object_data = $this->buildObject($name_lower, $item_color, $item_idcolor, $object_data);
        //echo "MIS A JOUR par FICHE<br/>";

        // On rempli le tableau
        $item['name'] = $name;
        $item['id'] = $item_id;
        $item['lang'] = 'fr';
        $item['color'] = $item_color;
        $item['icon'] = $item_icon;
        $item['link'] = 'http://www.wowdbu.com/2,' . $item_id . ',' . $item_version_to_test . '.html';

        $object_data = $object_data . '{ITEMSTATS_LINK}';

        $item['html'] = "<div class='wowitem'>" . $object_data . "</div>";

        // Build the final HTML by merging the template and the data we just prepared.
        $template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);                

        //echo 'Objet mis à jour, à partir du site internet !<br/>' . $item['html'] . '<br/>';
        // On renvoi le tout :)
        return ($item);        
    }
}

?>