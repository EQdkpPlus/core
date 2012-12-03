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

// The main interface to the Allakhazam
class ParseJudgehype
{
	var $xml_helper;

	// Constructor
	function ParseJudgehype()
	{
		$this->xml_helper = new XmlHelper();
	}

	// Cleans up resources used by this object.
	function close()
	{
		$this->xml_helper->close();
	}

	// Attempts to retrieve data for the specified item from Judgehype.
	function getItem($name)
	{
		// Ignore blank names.
		$name = trim($name);

		if (empty($name))
		{
			return null;
		}

		$item = array('name' => $name);
        $data = itemstats_read_url('http://worldofwarcraft.judgehype.com/index.php?page=bc-result&Ckey=' . urlencode($name));

		// Look for a name match in the search result.
        //if (preg_match_all('#href="index.php\?page=cobjet&co=(.*?)" style="color: (.*?);" onmouseover="ddrivetip' . strtolower($name) . '#s', strtolower($data), $matches))
        if (preg_match_all('#href="index.php\?page=bc-obj&w=(.*?)" style="color:\#(.*?);" onmouseover="ddrivetip\(\'(.*?)\'\)" .*?' . cleanRegExp(strtolower($name)) . '#s', strtolower($data), $matches))
		{
            foreach ($matches[0] as $key => $match)
			{
                $name_lower = $name;

				// Extract the item's ID from the match.
				$item_id = $matches[1][$key];
				$full_item_id = $item_id;
                if (strpos($item_id, ",") != false)
                    $item_id = substr($item_id, 0, strpos($item_id, ","));

                $item_idcolor = $matches[2][$key];
                //$item_html = stripslashes($matches[3][$key]);
                $item_color = $item_idcolor;

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

                $object_data = itemstats_read_url('http://worldofwarcraft.judgehype.com/index.php?page=bc-obj&w=' . $full_item_id);

                // On colle les balises entre elles, sinon ca pose probleme.
                $object_data = eregi_replace(">[ \t\n\r\f\v]+<", "><", $object_data);

				//$count_icon = preg_match('#<img src="screenshots/databases/icones/(.*?).jpg" .*?<font size="3">' . cleanRegExp(strtolower($name_lower)) . '</font>#', strtolower($object_data), $icon_match);
                $count_icon = preg_match('#background: url\(/addon-v2/images/icones/(.*?).jpg\);".*?<font size="3">' . cleanRegExp(strtolower($name_lower)) . '</font>#', strtolower($object_data), $icon_match);
				if ($count_icon < 1)
                    continue;
                $item_icon = $icon_match[1];

                // On recupere le bon nom d'icone avec la aussi la bonne casse
                $position_object = strpos(strtolower($object_data), $item_icon);
                if ($position_object == false)
                    continue;
                $item_icon = substr($object_data, $position_object, strlen($item_icon));
                // On recupere le nom reel avec la bonne casse
                $position_object = strpos($icon_match[0], strtolower($name_lower));
                if ($position_object == false)
                    continue;
                $position_object2 = strpos(strtolower($object_data), $icon_match[0]);
                if ($position_object == false)
                    continue;
                $name_lower = substr($object_data, $position_object2 + $position_object, strlen($name_lower));

                $position_start_data_object = strpos($object_data, '<font color=\'#' . $item_idcolor . '\'><b>' . $name_lower);
                if ($position_start_data_object == false)
                    continue;
                $position_end_data_object = strpos($object_data, "</table>", $position_start_data_object);
                if ($position_end_data_object == false)
                    continue;

                // On ressere l'étaux autour de notre objet
                $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

                $object_data = str_replace('<font color=\'#' . $item_idcolor . '\'><b>', '<span class=\'iname\'><span class=\'' . $item_color . '\'>', $object_data);
                $object_data = str_replace('</b></font>', '</span></span>', $object_data);

                $count_icon = preg_match_all('#(<td align=\'right\'>(.*?))</td>#s', $object_data, $icon_match);
                for ($ct = 0; $ct < $count_icon; $ct++)
                    $object_data = str_replace($icon_match[1][$ct], '<span class=\'wowrttxt\'>' . $icon_match[2][$ct] . '</span>', $object_data);
                $object_data = str_replace('</td></tr><tr><td colspan=2>', '<br/>', $object_data);
                $object_data = str_replace('< /td></tr><tr><td>', '<br/>', $object_data);
				$object_data = str_replace('<font color=\'#00ff00\'>', '<span class=\'itemeffectlink\'>', $object_data);
				$object_data = str_replace('<font color=\'#00FF00\'>', '<span class=\'itemeffectlink\'>', $object_data);
                $object_data = str_replace('<font color=\'#1eff00\'>', '<span class=\'itemeffectlink\'>', $object_data);
                $object_data = str_replace('<font color=\'#FFA500\'>', '<span class=\'goldtext\'>', $object_data);
                $object_data = str_replace('<font color=\'#ffd517\'>', '<span class=\'goldtext\'>', $object_data);

                $object_data = str_replace('<font', '<span', $object_data);
                $object_data = str_replace('</font>', '</span>', $object_data);
                $object_data = str_replace('<tr><td>', '<br/>', $object_data);
                $object_data = str_replace('<tr><td colspan=\'2\'>', '<br/>', $object_data);
                $object_data = str_replace('</td></tr>', '', $object_data);
                $object_data = str_replace('</td>', '', $object_data);
                $object_data = str_replace('"', '', $object_data);

                // On rempli le tableau
                $item['name'] = $name_lower;
                $item['id'] = $item_id;
                $item['lang'] = 'fr';
                $item['color'] = $item_color;
                $item['icon'] = $item_icon;
                $item['link'] = 'http://worldofwarcraft.judgehype.com/index.php?page=bc-obj&w=' . $item_id;

                $object_data = $object_data . '{ITEMSTATS_LINK}';

                $item['html'] = "<div class='wowitem'>" . $object_data . "</div>";

                // Build the final HTML by merging the template and the data we just prepared.
                $template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
			    $item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

                //echo "Objet mis à jour, à partir du site internet !<br/>" . $item['html'] . '<br/>';
                // On renvoi le tout :)
                return ($item);
            }
        }
        unset($item['link']);
        //echo "Aucun objet trouvé !<br/>";
        return ($item);
   }

	// Attempts to retrieve data for the specified item from ID from Judgehype.
	function getItemId($item_id)
	{
        $item = array('id' => $item_id);

        $object_data = itemstats_read_url('http://worldofwarcraft.judgehype.com/index.php?page=bc-obj&w=' . $item_id);

        $item_data = preg_match("/<table width='100\%' border='0' cellpadding='0' cellspacing='0' class='contenu'><tr><td colspan='2'><font color='\#(.*?)'><b>(.*?)<\/b><\/font>/i", $object_data, $matches);
        if ($item_data == false)
            return ($item);

        $item_color = $matches[1];
        $item_idcolor = $item_color;
        $item_name = $matches[2];

        $name_lower = strtolower($item_name);

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

        // On colle les balises entre elles, sinon ca pose probleme.
        $object_data = eregi_replace(">[ \t\n\r\f\v]+<", "><", $object_data);

        $count_icon = preg_match('#<img src="screenshots/databases/icones/(.*?).jpg" .*?<font size="3">' . strtolower($name_lower) . '</font>#', strtolower($object_data), $icon_match);

        $item_icon = $icon_match[1];

        // On recupere le bon nom d'icone avec la aussi la bonne casse
        $position_object = strpos(strtolower($object_data), $item_icon);
        if ($position_object == false)
            return ($item);
        $item_icon = substr($object_data, $position_object, strlen($item_icon));
        // On recupere le nom reel avec la bonne casse
        $position_object = strpos($icon_match[0], strtolower($name_lower));
        if ($position_object == false)
            return ($item);
        $position_object2 = strpos(strtolower($object_data), $icon_match[0]);
        if ($position_object == false)
            return ($item);
        $name_lower = substr($object_data, $position_object2 + $position_object, strlen($name_lower));

        $position_start_data_object = strpos($object_data, '<font color=\'#' . $item_idcolor . '\'><b>' . $name_lower);
        if ($position_start_data_object == false)
            return ($item);
        $position_end_data_object = strpos($object_data, "</table>", $position_start_data_object);
        if ($position_end_data_object == false)
            return ($item);

        // On ressere l'étaux autour de notre objet
        $object_data = substr($object_data, $position_start_data_object, $position_end_data_object - $position_start_data_object);

        $object_data = str_replace('<font color=\'#' . $item_idcolor . '\'><b>', '<span class=\'iname\'><span class=\'' . $item_color . '\'>', $object_data);
        $object_data = str_replace('</b></font>', '</span></span>', $object_data);

        $count_icon = preg_match_all('#(<td align=\'right\'>(.*?))</td>#s', $object_data, $icon_match);
        for ($ct = 0; $ct < $count_icon; $ct++)
            $object_data = str_replace($icon_match[1][$ct], '<span class=\'wowrttxt\'>' . $icon_match[2][$ct] . '</span>', $object_data);
        $object_data = str_replace('</td></tr><tr><td colspan=2>', '<br/>', $object_data);
        $object_data = str_replace('< /td></tr><tr><td>', '<br/>', $object_data);
		$object_data = str_replace('<font color=\'#00ff00\'>', '<span class=\'itemeffectlink\'>', $object_data);
		$object_data = str_replace('<font color=\'#00FF00\'>', '<span class=\'itemeffectlink\'>', $object_data);
        $object_data = str_replace('<font color=\'#1eff00\'>', '<span class=\'itemeffectlink\'>', $object_data);
        $object_data = str_replace('<font color=\'#FFA500\'>', '<span class=\'goldtext\'>', $object_data);
        $object_data = str_replace('<font color=\'#ffd517\'>', '<span class=\'goldtext\'>', $object_data);

        $object_data = str_replace('<font', '<span', $object_data);
        $object_data = str_replace('</font>', '</span>', $object_data);
        $object_data = str_replace('<tr><td>', '<br/>', $object_data);
        $object_data = str_replace('<tr><td colspan=\'2\'>', '<br/>', $object_data);
        $object_data = str_replace('</td></tr>', '', $object_data);
        $object_data = str_replace('</td>', '', $object_data);
        $object_data = str_replace('"', '', $object_data);

        // On rempli le tableau
        $item['name'] = $name_lower;
        $item['id'] = $item_id;
        $item['lang'] = 'fr';
        $item['color'] = $item_color;
        $item['icon'] = $item_icon;
        $item['link'] = 'http://worldofwarcraft.judgehype.com/index.php?page=bc-obj&w=' . $item_id;

        $object_data = $object_data . '{ITEMSTATS_LINK}';

        $item['html'] = "<div class='wowitem'>" . $object_data . "</div>";

        // Build the final HTML by merging the template and the data we just prepared.
        $template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

        //echo "Objet mis à jour, à partir du site internet !<br/>" . $item['html'] . '<br/>';
        // On renvoi le tout :)
        return ($item);
   }

}

?>