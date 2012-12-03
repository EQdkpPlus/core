<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date: 2008-12-02 11:54:02 +0100 (Di, 02 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     itemstats
 * @version     $Rev: 3293 $
 *
 * $Id:   $
 */

include_once(dirname(__FILE__).'/urlreader.php');
include_once(dirname(__FILE__).'/xmltoarray.inc.php');

class AionDatabase
{
	public $itemlist = array();
	public $recipelist = array();
	public $av_langs = array('en' => 'en_US', 'de' => 'de_DE');#, 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'jp' => 'ja_JP'); currently not supported by our aion-game-folder

	private $searched_langs = array();

	function close()
	{
		unset($this->itemlist);
		unset($this->searched_langs);
	}

	//initializes the item/recipelist. if it does not exists in the cache, get it from: http://www.aiondatabase.com/xml/en_US/items(recipes)/item(recipe)list.xml
	function getItemlist($forceupdate=false, $lang=false, $type='item')
	{
		global $pcache, $eqdkp;

		$lang = ($lang) ? $lang : $eqdkp->config['game_language'];

		$itemlist_file = $pcache->FilePath($lang.'.aion_'.$type.'list.itemcache', 'itemstats');
		$filesize = filesize($itemlist_file);
		if(!(!$forceupdate AND $pcache->FileExists($lang.'.aion_'.$type.'list.itemcache', 'itemstats') AND $filesize)) #upate itemlist once a week
		{
		  if(filemtime($itemlist_file) < (time()-(24*60*60)) OR !$filesize) //only update once a day, except we dont have an itemlist
		  {
			$urlitemlist = itemstats_read_url('http://www.aiondatabase.com/xml/'.$this->av_langs[$lang].'/'.$type.'s/'.$type.'list.xml');
			$xmltoarray = new XmlToArray;
			$itemlist = $xmltoarray->parse($urlitemlist);
			$itemlist = $itemlist[0]['child'];
			foreach($itemlist as $key => $item)
			{
				$this->{$type.'list'}[$key]['id'] = $item['attr']['ID'];
				$this->{$type.'list'}[$key]['name'][$lang] = (isUTF8($item['attr']['NAME'])) ? utf8_decode($item['attr']['NAME']) : $item['attr']['NAME'];
			}
			$handle = fopen($itemlist_file, 'w');
			fwrite($handle, serialize($this->{$type.'list'}));
			fclose($handle);
			return;
		  }
		}
		$handle = fopen($itemlist_file, 'r');
		$this->{$type.'list'} = unserialize(fread($handle, $filesize));
		fclose($handle);
	}

	function getItemIDfromItemlist($itemname, $forceupdate=false, $searchagain=0, $lang=false, $type='item')
	{
		$searchagain++;
        $lang = ($lang) ? $lang : $eqdkp->config['game_language'];
		$this->getItemlist($forceupdate,$lang,$type);
		$item_id = array(0,0);

		//search in the itemlist for the name
		$loaded_item_langs = array();
		if($type == 'item') {
		  foreach($this->itemlist as $iteml)
		  {
			foreach($iteml['name'] as $slang => $name) {
		  		$loaded_item_langs[] = $slang;
				if($itemname == $name)
				{
					$item_id[0] = $iteml['id'];
					$item_id[1] = 'items';
					break 2;
				}
			}
		  }
		}
		//search in the recipelist for the name
		$loaded_recipe_langs = array();
		if($type == 'recipe') {
		  foreach($this->recipelist as $iteml)
		  {
			foreach($iteml['name'] as $slang => $name) {
		  		$loaded_recipe_langs[] = $slang;
				if($itemname == $name)
				{
					$item_id[0] = $iteml['id'];
					$item_id[1] = 'recipes';
					break 2;
				}
			}
		  }
		}
		if(!$item_id[0] AND count($this->av_langs) > $searchagain) {
			$toload = array();
		  	foreach($this->av_langs as $c_lang => $langlong) {
		  		if(!in_array($c_lang,$loaded_item_langs)) {
		  			$toload[$c_lang][] = 'item';
		  		}
		  		if(!in_array($c_lang,$loaded_recipe_langs)) {
		  			$toload[$c_lang][] = 'recipe';
		  		}
		  	}
		  	foreach($toload as $lang => $load) {
		  	  foreach($load as $type) {
		  		$item_id = $this->getItemIDfromItemlist($itemname, true, $searchagain, $lang, $type);
		  		if($item_id[0]) {
		  			break 2;
		  		}
		  	  }
		  	}
		}
		return $item_id;
	}

	function getItemIDfromUrl($itemname, $searchagain=0, $lang=false)
	{
		global $eqdkp;
		$searchagain++;
		$lang = ($lang) ? $lang : $eqdkp->config['game_language'];
		$codedname = str_replace(' ', '%2B', $itemname);

		$data = itemstats_read_url('http://'.(($lang == 'en') ? 'www' : $lang).'.aiondatabase.com/search?q='. utf8_encode($codedname));
		$this->searched_langs[] = $lang;
		if (preg_match_all('#\[\{\"tpl\":\"items\",\"n\":\"(.*?)\",\"id\":\"items\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
			if (preg_match_all('#\{\"id\":([0-9]*),\"n\":\"(.*?)\",\"l\":([0-9]*),\"p\":([0-9]*),\"r\":([0-9]*),\"i\":\"(.*?)\",\"cat\":\{(.*?)\}(.*?)\}#', $matchs[2][0], $matches)) {
				foreach ($matches[0] as $key => $match) {
					$item_name_tosearch = substr(html_entity_decode($matches[2][$key]),1);
					//decode unicode
					$item_name_tosearch = decode_unicode($item_name_tosearch);
					if (strcasecmp($item_name_tosearch, $itemname) == 0) {
						$item_id[0] = $matches[1][$key];
						$item_id[1] = 'items';
						break;
					}
				}
			}
		}
		if(!$item_id[0]) {
		  if (preg_match_all('#\[\{\"tpl\":\"recipes\",\"n\":\"(.*?)\",\"id\":\"recipes\",\"rows\":\[(.*?)\]}\]#', $data, $matchs)) {
			if (preg_match_all('#\{\"id\":([0-9]*),\"sl\":([0-9]*),\"r\":([0-9]*),\"rn\":\"(.*?)\",\"p\":\{(.*?)},\"n\":\"(.*?)\",\"comp\":(.*?)\}#', $matchs[2][0], $matches)) {
				foreach ($matches[0] as $key => $match) {
					$item_name_tosearch = substr(html_entity_decode($matches[6][$key]),1);
					//decode unicode
					$item_name_tosearch = decode_unicode($item_name_tosearch);
					if (strcasecmp($item_name_tosearch, $itemname) == 0) {
						$item_id[0] = $matches[1][$key];
						$item_id[1] = 'recipes';
						break;
					}
				}
			}
		  }
		}
		if(!$item_id AND count($this->av_langs) > $searchagain) {
			$toload = array();
		  	foreach($this->av_langs as $c_lang => $langlong) {
		  		if(!in_array($c_lang,$this->searched_langs)) {
		  			$item_id = $this->getItemIDfromUrl($itemname, $searchagain, $c_lang);
		  		}
		  		if($item_id[0]) {
		  			break;
		  		}
		  	}
		}
		return $item_id;
	}

	function searchitemid($itemname)
	{
		global $conf_plus;
		if($conf_plus['pk_is_useitemlist']) {
			return $this->getItemIDfromItemlist($itemname);
		} else {
			return $this->getItemIDfromUrl($itemname);
		}
	}

	public function getItemId($item_id, $itemname='')
	{
		global $eqdkp;
		return $this->getItemData($item_id, $itemname, $eqdkp->config['game_language']);
	}

	//search the item by his name in the itemlist
	public function getItem($itemname, $lang=false)
	{
		global $eqdkp;
		$name = trim($itemname);
		if(empty($name)) return null;

		$item = array('name' => $name);

		$item_id = $this->searchitemid($itemname);
		$lang = ($lang) ? $lang : $eqdkp->config['game_language'];
		return $this->getItemData($item_id[0], $itemname, $lang, $item_id[1]);
	}

	private function getItemData($item_id, $itemname, $lang=false, $type='items')
	{
        settype($item_id, 'int');
		$lang = ($lang) ? $lang : $eqdkp->config['game_language'];
		$item = array('id' => $item_id);
		if(!$item_id) return null;
		$url = 'www.aiondatabase.com/xml/'.$this->av_langs[$lang];
		$item['link'] = $url."/".$type."/xmls/".$item['id'].".xml";
		//get the xml from blasc: http://www.aiondatabase.com/xml/$lang_code/items/xmls/$itemid.xml
		$itemxml = itemstats_read_url($item['link'], $lang);
		$xmltoarray = new XmlToArray;
		$itemdata = $xmltoarray->parse($itemxml);
		$itemdata = $itemdata[0]['child'];

		//get indexes of tooltip, name, quality, icon
		$index = array('t' => false, 'n' => false, 'q' => false, 'i' => false);
		foreach($itemdata as $ind => $i_dat) {
			switch($i_dat['name']) {
				case 'NAME': $index['n'] = $ind;
					break;
				case 'ICONPATH': $index['i'] = $ind;
					break;
				case 'QUALITY': $index['q'] = $ind;
					break;
				case 'TOOLTIP': $index['t'] = $ind;
					break;
			}
		}

		$item['name'] = (!is_numeric($itemname) AND strlen($itemname) > 0) ? $itemname : $itemdata[$index['n']]['data'];

		//build itemhtml
		$html = "<table class='db-tooltip' cellspacing='0'><tr><td class='normal'>";
		$html .= ((isUTF8($itemdata[$index['t']]['data'])) ? utf8_decode(str_replace('"', "'", $itemdata[$index['t']]['data'])) : str_replace('"', "'", $itemdata[$index['t']]['data']));
		$html .= "</td><td class='right'></td></tr><tr><td class='bottomleft'></td><td class='bottomright'></td></table>";
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/popup_aion.tpl'));
		$item['html'] = str_replace('{ITEM_HTML}', stripslashes($html), $template_html);
		$item['lang'] = $lang;
		$item['icon'] = $itemdata[$index['i']]['data'];
		$item['color'] = 'aion_q'.$itemdata[$index['q']]['data'];
		return $item;
	}
}