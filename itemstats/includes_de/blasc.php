<?php
// edit by Corgan:
// search for the item ID on blasc
//
// edit:
// 01.11.2005 by Corgan
// 22.07.2006 by Corgan
// 25.08.2006 by Corgan (update auf buffed.de)
// 17.09.2006 by Corgan fixed some bugs with some craft items
// last changes:
// 27.09.2003 by Corgan itemnames now can be in lower case

// visit: http://www.seniorenraid.de
//
// version 2.3

$stats_debug = false ;

include_once(dirname(__FILE__) . '/xmlhelper.php');
include_once(dirname(__FILE__) . '/urlreader.php');
define('ICON_LINK_PLACEHOLDER', '{ITEM_ICON_LINK}');
define('DEFAULT_ICON', 'INV_Misc_QuestionMark');

function itemstats_debug($debugmsg)
{
	global $stats_debug , $conf_plus;
	
if(!$conf_plus['pk_itemstats_debug']==1)
{
	return;
}	
	
$debugfile = dirname(__FILE__) . '/debug.txt' ;
if(file_exists($debugfile) and is_writeable($debugfile))
{

	$debugmsg .="\r\n";
	$fp=fopen($debugfile,"a");
	fwrite($fp,$debugmsg);
	 fclose($fp);
 }
 else
 {
	// do nothing :D
 }
}

// The main interface to the Blasc Site
class InfoSite
{
	var $xml_helper;
	var $colornames;

	// Constructor
	function InfoSite()
	{
		$this->xml_helper = new XmlHelper();
		$this->colornames['q0'] = "greyname";
		$this->colornames['q1'] = "whitename";
		$this->colornames['q2'] = "greenname";
		$this->colornames['q3'] = "bluename";
		$this->colornames['q4'] = "purplename";
		$this->colornames['q5'] = "orangename";
		$this->colornames['q6'] = "redname";
	}

	// Cleans up resources used by this object.
	function close()
	{
		$this->xml_helper->close();
	}

	// Attempts to retrieve data for the specified item from Allakhazam.
	function getItem($name)
	{
		itemstats_debug(' ');
		itemstats_debug('##############');
		itemstats_debug('New Item: '.$name);

		// Ignore blank names.
		$name = trim($name);
		if (empty($name))
		{
			return null;
		}
		$org_name = $name;
		$item = array('name' => $name);
		$item_found = false;

		//suche englischen Namen in englischer Itemlist

		if (file_exists(dirname(__FILE__) . '/../itemlist.xml'))
		{
			itemstats_debug('Allakhazam Itemlist.xml found. search for ID');

			$itemlist = file(dirname(__FILE__) . '/../itemlist.xml');
			for ($i=0;$i<count($itemlist);$i++)
			{
				if (strpos($itemlist[$i], 'name="'.$name.'"') !== false)
				{
					preg_match('#<wowitem name="(.*?)" id="(.*?)" />#', $itemlist[$i], $itemmatches);
					$item_id = $itemmatches[2];
					$item_xml_found = true;
					itemstats_debug('Item found in allakhazam Itemlist.xml ItemID: '.$item_id);
					break;
				} // stristr($itemlist, 'name="'.$name.'"') !== false
			} // for ($i=0;$i<count($itemlist);$i++)
		}

		//-------------------------------------------------------------- edit by corgan begin
		if (empty($item_id))
		{

			$searchname = str_replace("%2B", "+", urlencode($name));
			$searchname = str_replace("&uuml;", "ü", urlencode($name));
			$searchname = str_replace("&Uuml;", "Ü", urlencode($name));
			$searchname = str_replace("&auml;", "ä", urlencode($name));
			$searchname = str_replace("&Auml;", "Ä", urlencode($name));
			$searchname = str_replace("&ouml;", "ö", urlencode($name));
			$searchname = str_replace("&Ouml;", "Ö", urlencode($name));
			$searchname = str_replace("&szlig;", "ß", urlencode($name));
			
			itemstats_debug('No ID in allakhazam itemlist.xml found. Try to search for Item.');
			itemstats_debug('Searchname: '.$searchname);
			$data = itemstats_read_url('http://www.buffed.de/?f='.$searchname);
			itemstats_debug('search on: http://www.buffed.de/?f='.$searchname);
			#preg_match_all("#(i=[a-z0-9]*)(.*?)</span>#", $data, $matches);
			preg_match_all('#(id="[0-9]*)(.*?)class="tooltip"(.*?)</span>#', $data, $matches);
			
			
			if (count($matches[0]) > 1)
			{
				itemstats_debug('found '.count($matches[0]).' search results on blasc. Now pass through the Results.');
				itemstats_debug(' ');		
				for ($i=0;$i<count($matches[0]);$i++) 
				{
					itemstats_debug(($i+1).'. orginal lockupstring: '.$matches[0][$i]);

					$lockupstring = $matches[0][$i] ;
					$lockupstring = str_replace("%2B", "+", $lockupstring);
					$lockupstring = str_replace("&uuml;", "ü", $lockupstring);
					$lockupstring = str_replace("&Uuml;", "Ü", $lockupstring);
					$lockupstring = str_replace("&auml;", "ä", $lockupstring);
					$lockupstring = str_replace("&Auml;", "Ä", $lockupstring);
					$lockupstring = str_replace("&ouml;", "ö", $lockupstring);
					$lockupstring = str_replace("&Ouml;", "Ö", $lockupstring);
					$lockupstring = str_replace("&szlig;", "ß", $lockupstring);
									
					itemstats_debug(($i+1).'. htmlcode cleaned lockupstring: '.$lockupstring);				
					itemstats_debug(($i+1).'. search in above string for: >'.urldecode($searchname).'<');							
					itemstats_debug(' ');							
					
					if (strpos($lockupstring, ">".urldecode(urldecode($searchname))."<") !== false or 
						 strpos($lockupstring, ">".$searchname."<") !== false or
						 preg_match('#>'.$searchname.'<#i', $lockupstring) !== false)
					{
						itemstats_debug('!!! Gotcha !!! @ pass '.($i+1));						 
						itemstats_debug('take this one - item urldecode: '.urldecode(urldecode($searchname)));						
						itemstats_debug('take this one - ID: '.trim(substr($matches[1][$i],2)));
						break;

					}
				}
			} else {
				itemstats_debug('only found one entry on blasc');
				$i = 0;
			}
			$item_id = trim(substr($matches[1][$i],4));
			#echo $item_id ;
			itemstats_debug('Final trimmed Itemid: '.$item_id);


		}
		//-------------------------------------------------------------- edit by corgan END

		$blascurl = 'http://www.buffed.de/xml/i' . $item_id.'.xml' ;
		if($item_id and $item_id >0)
		{
			$item['item_id'] = $item_id;
			itemstats_debug('ok, now get the itemstats from blascurl: '.$blascurl);
			$xml_data = itemstats_read_url($blascurl);
	
			// Parse out the name of this item from the XML and check if we have a match
			$xml_name = $this->xml_helper->parse($xml_data, 'InventoryName');
			itemstats_debug('Search for Itemname in XML Data: '.$xml_name);
			if (!empty($xml_name))
			{
			#echo $xml_name ;
				// If we have a match, grab additional information about this item and break out of this loop.
				//$item['name'] = $itemmatches[1];
				$item['name'] = $name;           // -------->>> edit by corgan
				$item['icon'] = $this->xml_helper->parse($xml_data, 'Icon');
				$item['link'] = 'http://www.buffed.de/?i='. $item_id;
				$item_found = true;
			} // if (strcasecmp($item['name'], $xml_name) == 0)
		}
		// If a match was found, retrieve additional info about it.
		if ($item_found)
		{
			itemstats_debug('get XML Data from http://www.buffed.de/?i='. $item_id);
			// Parse out the display html of this item from the XML
			$item['html'] = $this->xml_helper->parse($xml_data, 'display_html');

			// Extract the item color from the HTML.
			preg_match('#<span class="item (.*?)">#s', $item['html'], $match);
			$item['color'] = $this->colornames[$match[1]];
			$item['html'] = preg_replace('#<span class="item (.*?)">#s', '<span class="'.$item['color'].'">', $item['html']);

			// Fix up the html a bit.
			$item['html'] = str_replace('<table width="100%">', '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="borderless">', $item['html']);
			$item['html'] = str_replace("<div>", "<div class='wowitemt' style='display:block'><div>", $item['html']);
			$item['html'] = str_replace('"', "'", $item['html']);
			// If this is a set, grab the set bonuses and add it to the html.
			$item_set_id = $this->xml_helper->parse($xml_data, 'ItemSet');
			if (!empty($item_set_id) && ($item_set_id != '0'))
			{
				// Read the item set page.
				$data = itemstats_read_url('http://www.buffed.de/world-of-warcraft/blasc/gegenstaende/sets/set.html?tx_blasc_pi1[set_id]=' . $item_set_id);

				// Extract the set bonus html from this page.
				//preg_match('#<table class="liste"(.*?)</table>#s', $data, $match);
				preg_match('#<table class="liste" width="100%"(.*?)</table>#s', $data, $match);
				$item_set_bonuses = '<table class="setlist" class="borderless" border="0"'.$match[1].'</table>';
				$item_set_bonuses = str_replace('"', "'", $item_set_bonuses);

				// Fix up the html a bit
				$item_set_bonuses = "<div class='setbonus'><span class='spacer'><br /></span>" . $item_set_bonuses . "</div>";
				$item['html'] = substr($item['html'],0,-6).$item_set_bonuses.'</div>';

			}
			else {
			      $item['html'] .= '</ div>';
			}


			// Build the final HTML by merging the template and the data we just prepared.
			$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup.tpl'));
			$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);
		}
		else
		{
			itemstats_debug('No Item found on Blasc with that Name !!!! -> search on allakhazam. Good luck');
			include_once(dirname(__FILE__) . '/allakhazam.php');
			$allaitems = new InfoSite2;
			$item = $allaitems->getItem($name);
		}
		$item['html'] = str_replace("<br>", "<br />", $item['html']);
		$item['html'] = preg_replace('#>\s*<#s', '><', $item['html']);
		return $item;
	}
}
?>
