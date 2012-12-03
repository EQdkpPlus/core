<?php
	/*
	 * author: Hoofy
	 * version: 0.1
	 * began: 15-03-09 20:22:34
	 */


include_once(dirname(__FILE__).'/urlreader.php');
include_once(dirname(__FILE__).'/xmltoarray.inc.php');

class ParseBlasc
{
	var $itemlist = array();

	function close()
	{
		unset($this->itemlist);
	}

	//initializes the itemlist. if it does not exists in the cache, get it from: http://www.buffed.de/tooltiprom/items/xml/itemlist.xml
	function getItemlist($forceupdate=false)
	{
		global $pcache;

		$itemlist_file = $pcache->FilePath('rom_itemlist.itemcache', 'itemstats');
		$filesize = filesize($itemlist_file);
		if(!(!$forceupdate AND $pcache->FileExists('rom_itemlist.itemcache', 'itemstats') AND $filesize)) #upate itemlist once a week
		{
		  if(filemtime($itemlist_file) < (time()-(24*60*60)) OR !$filesize) //only update once a day, except we dont have an itemlist
		  {
			$urlitemlist = itemstats_read_url('http://www.buffed.de/tooltiprom/items/xml/itemlist.xml');
			$xmltoarray = new XmlToArray;
			$itemlist = $xmltoarray->parse($urlitemlist);
			$itemlist = $itemlist[0]['child'];
			foreach($itemlist as $key => $item)
			{
				$this->itemlist[$key]['id'] = $item['attr']['ID'];
				$this->itemlist[$key]['deDE'] = (isUTF8($item['attr']['NAME_DEDE'])) ? utf8_decode($item['attr']['NAME_DEDE']) : $item['attr']['NAME_DEDE'];
				$this->itemlist[$key]['enUS'] = (isUTF8($item['attr']['NAME_ENUS'])) ? utf8_decode($item['attr']['NAME_ENUS']) : $item['attr']['NAME_ENUS'];
			}
			$handle = fopen($itemlist_file, 'w');
			fwrite($handle, serialize($this->itemlist));
			fclose($handle);
			return;
		  }
		}
		$handle = fopen($itemlist_file, 'r');
		$this->itemlist = unserialize(fread($handle, $filesize));
		fclose($handle);
	}

	function searchitemid($itemname, $forceupdate=false, $searchagain=true, $url='romdata.buffed.de')
	{
		global $conf_plus;
		$item_id = 0;
		if($conf_plus['pk_is_useitemlist'])
		{
		  $this->getItemlist($forceupdate);

		  //search in the itemlist for the name
		  foreach($this->itemlist as $iteml)
		  {
			if($itemname == $iteml['deDE'] or $itemname == $iteml['enUS'])
			{
				$item_id = $iteml['id'];
				break;
			}
		  }
		}
		else
		{
			$codedname = str_replace(' ', '%2B', $itemname);
		  	$data = itemstats_read_url('http://'.$url.'/?f='. utf8_encode($codedname));
		  	//fixed by hoofy
        	if (preg_match_all('#new Btabs\(\[\{\"id\":\"items\",\"n\":\"(.*?)\",\"rows\":\[(.*?)\],\"tpl\":\"rom_itemlist\"\}#', $data, $matchs))
        	{
          	  if (preg_match_all('#\{\"id\":([0-9]*),\"n\":\"(.*?)\",\"q\":(.*?)\"\}#', $matchs[2][0], $matches))
		  	  {
				foreach ($matches[0] as $key => $match)
				{
					$item_name_tosearch = html_entity_decode($matches[2][$key]);
					//decode unicode
					$item_name_tosearch = str_replace('\u00df', 'ß', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00e4', 'ä', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00fc', 'ü', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00f6', 'ö', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00c4', 'Ä', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00dc', 'Ü', $item_name_tosearch);
					$item_name_tosearch = str_replace('\u00d6', 'Ö', $item_name_tosearch);

					if (strcasecmp($item_name_tosearch, $itemname) == 0)
					{
						// Extract the item's ID from the match.
						$item_id = $matches[1][$key];
						break;
					}
				}
			  }
			}
		}
		if($item_id)
		{
			return $item_id;
		}
		else
		{
			return (($searchagain) ? $this->searchitemid($itemname, true, false, 'romdata.getbuffed.com') : null);
		}
	}

	//search the item by his name in the itemlist
	function getItem($itemname, $lang='deDE')
	{
		global $eqdkp;
		$name = trim($itemname);
		if(empty($name)) return null;

		$item = array('name' => $name);

		$item_id = $this->searchitemid($itemname);
		$lang = ($eqdkp->config['game_language'] == 'de') ? 'deDE' : 'enUS';
		return $this->getItemData($item_id, $itemname, $lang);
	}

	function getItemData($item_id, $itemname, $lang='deDE')
	{
        settype($item_id, 'int');
		$item = array('id' => $item_id);
		if(!$item_id) return null;
		$url = ($lang == 'deDE') ? 'buffed.de' : 'getbuffed.com';
		$item['link'] = 'http://romdata.'.$url.'/tooltiprom/items/xml/'.$item_id.'.xml';
		//get the xml from blasc: http://www.buffed.de/tooltiprom/items/xml/$itemid.xml
		$itemxml = itemstats_read_url($item['link'], $lang);
		$xmltoarray = new XmlToArray;
		$itemdata = $xmltoarray->parse($itemxml);
		$itemdata = $itemdata[0]['child'];
		$item['name'] = $itemname;

		//build itemhtml
		$html = "<table class='db-tooltip' cellspacing='0'><tr><td class='normal'>";
		$html .= ((isUTF8($itemdata[1]['data'])) ? utf8_decode(str_replace('"', "'", $itemdata[1]['data'])) : str_replace('"', "'", $itemdata[1]['data']));
		$html .= "</td><td class='right'></td></tr><tr><td class='bottomleft'></td><td class='bottomright'></td></table>";

		$item['html'] = $html;
		$item['lang'] = $itemdata[7]['data'];
		$item['icon'] = $itemdata[3]['data'];
		$item['color'] = 'q'.$itemdata[4]['data'];
		return $item;
	}
}