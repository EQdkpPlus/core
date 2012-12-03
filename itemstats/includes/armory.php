<?php

/*
 * ParseArmory
 * started: 13/06/2007
 *
 * author: Olivier Garbé
 * email: ogarbe@gmail.com
 * description: create itemstats tooltips using armory
 *
 * version: 0.4
 *
* Copyright (c) 1998, Regents of the University of California
* All rights reserved.
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the University of California, Berkeley nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE REGENTS AND CONTRIBUTORS BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

 */

include_once(dirname(__FILE__) . '/../config.php');
include_once(dirname(__FILE__) . '/../config_armory.php');
include_once(dirname(__FILE__) . '/xmltoarray.inc.php');
include_once(dirname(__FILE__) . '/urlreader.php');
include_once(dirname(__FILE__) . '/download_file.php');

// The main interface to the Armory parser
class ParseArmory
{
        function arrayToHtml ($array) {
          $str = "<div class=\"armory_".$array["name"]."\">";
          if (sizeof($array["attr"])>0) {
            foreach ($array["attr"] as $key => $val) {
              $str .="<div class=\"armory_".$array["name"]."_".$key."\">".$val."</div>";
            }
          }
          $str.=$array["data"];
          if (sizeof($array["child"])>0) {
            foreach ($array["child"] as $child)
               $str.=$this->arrayToHtml($child);
          }
          $str.="</div>";
          return $str;
        }
	// Attempts to retrieve data for the specified item from Wowhead
	function getItem($name,$language='fr')
    	{
		

		// Ignore blank names.
		$name = trim($name);
		if (empty($name)) { return null; }
		
		$item = array('name' => $name);

		// remove extra spaces (vB is known to add them)
		$fixed_name = implode(' ', preg_split ("/[\s\+]+/", urldecode($name)));
	
		// encode the name so it can be used to build the url
		$encoded_name = utf8_encode($fixed_name);//rawurlencode($fixed_name);
		$encoded_name = str_replace('+' , '%20' , $encoded_name);
               $encoded_name = str_replace(' ' , '%20' , $encoded_name);

		// Perform the search, and retrieve the result
		unset($xml_parser); // unset $xml_parser to prevent warnings on PHP4x
		$xml_parser = new XmlToArray();
		$xml_search_data = itemstats_read_url('http://armory.worldofwarcraft.com/search.xml?searchType=items&searchQuery=' . $encoded_name, $language);
		if (debug_mode == true) {
                    echo "Search on the Armory site : " . $name." in the ".$language." language : http://armory.worldofwarcraft.com/search.xml?searchType=items&searchQuery=" . $encoded_name."<br/>";
                    var_dump($xml_search_data);
                }
                if (strpos($xml_search_data,'<items>') === false) return $item;
		$xml_search_data=substr($xml_search_data,strpos($xml_search_data,'<items>'));
                $xml_search_data=substr($xml_search_data,0,strpos($xml_search_data,'</items>')+8);
                $result = $xml_parser->parse($xml_search_data);


		// find the ITEMS section in the xml file, if its not there we did not find _anything_
		$items_idx = -1;
		$i = 0;
		foreach($result[0]['child'] as $category) {
			if ($category['name'] == 'ITEM') {
				$items_idx = $i;
				break;
			}
			$i++;
		}

		// our search found one or more items
		if ($items_idx != -1) {
			$found_item = $result[0]['child'][$items_idx];
			$item_id = -1;
			
                        $item_id = $found_item['attr']['ID'];
			$found_name = utf8_decode($found_item['attr']['NAME']);

			if ($item_id != -1) {
				// we found the item in the results, retrieve the item data using its item id
				return $this->getItemId($item_id, $found_name,$language);
			}
		}
		
		unset($item['link']);
		return $item;
	}

	// Attempts to retrieve data for the specified item from Wowhead by its wowhead itemid
	function getItemId($item_id, $name = '',$language='fr')
	{
		$item = array('id' => $item_id);

                // retrieve the item data
		unset($xml_parser); // unset $xml_parser to prevent warnings on PHP4x
		$xml_parser = new XmlToArray();
		$xml_item_data = itemstats_read_url('http://armory.worldofwarcraft.com/item-tooltip.xml?i=' . $item_id, $language);
		$item_data = $xml_parser->parse($xml_item_data);
		
		if (debug_mode == true) {
                    echo "Search on the Armory site : " . $item_id." in the ".$language." language<br/>";
                }

		if (sizeof($item_data) == 0) {
			// error, probably an invalid item id
			unset($item['link']);
			return $item;
		}
		// apparantly weve got valid item data
		
		$lang = $item_data[0]['attr']['LANG'];
		
		$xml_lang_data = itemstats_read_url('http://armory.worldofwarcraft.com/strings/'.$lang.'/strings.xml', $language);
		$lang_data = $xml_parser->parse($xml_lang_data);
		$tooltip = array();
		$slot = array();
		$lang_data=$lang_data[0]["child"];
		
		foreach ($lang_data as $child) {
			if (strpos($child['attr']['ID'],'armory.item-tooltip.') !== false) {
				$tooltip[substr($child['attr']['ID'],strlen('armory.item-tooltip.'))] = $child['data'];
			}
			if (strpos($child['attr']['ID'],'armory.labels.slot.') !== false) {
				$slot[substr($child['attr']['ID'],strlen('armory.labels.slot.'))] = $child['data'];
			}
			
		}
		if (debug_mode == true) {
                    echo "Search on the Armory site : language ".$lang."<br/>";
		}

		// create an array of item properties
		$properties = array();
		foreach($item_data[0]['child'][0]['child'][0]['child'] as $property) {
			$properties[$property['name']]['data'] = $property['data'];
			$properties[$property['name']]['attr'] = $property['attr'];
			if (sizeof($property['child'])) {
                           foreach($property['child'] as $k=>$p) {
                             $properties[$property['name']."_".$p['name'].$k]['data'] = $p['data'];
			     $properties[$property['name']."_".$p['name'].$k]['attr'] = $p['attr'];
			     if (sizeof($p['child'])) {
                                 foreach($p['child'] as $q) {
                                   $properties[$property['name']."_".$p['name'].$k."_".$q['name']]['data'] = $q['data'];
      			           $properties[$property['name']."_".$p['name'].$k."_".$q['name']]['attr'] = $q['attr'];
                                 }
                              }
                           }
                        }
		}
		
		if (debug_mode == true) var_dump($properties);


		// set item data
		if ($name != '') {
			$item['name'] = $name;
		} else {
			$item['name'] = $properties['NAME']['data'];
		}
		$item['lang'] = $language;
		$item['link'] = 'http://armory.worldofwarcraft.com/item-info.xml?i='.$item_id; // wowhead url to the item
		$item['icon'] = $properties['ICON']['data']; // icon filename without an extension

		// if download icons is enabled, download the icon
		if (DOWNLOAD_ICONS) {
			if (!$this->downloadIcon($item['icon'])) {
				// failed to download the icon, use default
				$item['icon'] = DEFAULT_ICON;
			}
		}
		// set the item color based on the item quality
		switch ($properties['OVERALLQUALITYID']['data']) {
			case 0:
				$item['color'] = 'greyname';
				break;
			case 1:
				$item['color'] = 'whitename';
				break;
			case 2:
				$item['color'] = 'greenname';
				break;
			case 3:
				$item['color'] = 'bluename';
				break;
			case 4:
				$item['color'] = 'purplename';
				break;
			case 5:
				$item['color'] = 'orangename';
				break;
			case 6:
				$item['color'] = 'redname';
				break;
			default:
				$item['color'] = 'greyname';
				break;
		}
		
		$properties['HTMLTOOLTIP']['data'] = "<table><tr><td><b class=\"q".$properties['OVERALLQUALITYID']['data']."\">".$item['name']."</b><br/>";
		switch($properties['BONDING']['data']) {
                   case '1':  $properties['HTMLTOOLTIP']['data'] .= $tooltip["binds-pickup"]."<br/>";break;
                   case '4': $properties['HTMLTOOLTIP']['data'] .= $tooltip["quest-item"]."<br/>";break;
                   default : $properties['HTMLTOOLTIP']['data'] .= $tooltip["binds-equipped"]."<br/>";
                }
		if ($properties['MAXCOUNT']['data']) {
                   $properties['HTMLTOOLTIP']['data'] .= $tooltip["unique"]."<br/>";
                }

                $slots = array (
                	1 => $slot["head"],
                	2 => $slot["neck"],
                	3 => $slot["shoulders"],
                	4 => "4 ?",
                	5 => $slot["chest"],
                	6 => $slot["waist"],
                	7 => $slot["legs"],
                	8 => $slot["feet"],
                	9 => $slot["wrist"],
                	10 => $slot["hands"],
                	11 => $slot["finger"],
                	12 => $slot["trinket"],
                	13 => "Une main",
                	14 => $slot["offHand"],
                	15 => "Arc",
                	16 => $slot["back"],
                	17 => "Deux mains",
                	18 => "Sac",
                	19 => "19 ?",
                	20 => "Torse",
                	21 => $slot["mainHand"],
                	22 => $slot["offHand"],
                	23 => $slot["offHand"],
                	24 => "24 ?",
                	25 => "Arme de jet",
                	26 => $slot["ranged"],
                	27 => "27 ?",
                	28 => $slot["relic"]
                );
		if ((int)$properties['EQUIPDATA_INVENTORYTYPE0']['data'] > 0)
                                $properties['HTMLTOOLTIP']['data'].= "<table><tbody><tr><td>".$slots[$properties['EQUIPDATA_INVENTORYTYPE0']['data']]."</td><th>".$properties['EQUIPDATA_SUBCLASSNAME1']['data']."</th></tr></tbody></table>";
                                
                if ($properties['DAMAGEDATA']['data']) {
                    $damage=array(0=>"", 1=>"(Sacré)", 2=>"(Feu)", 3=>"(Nature)", 4=>"Givre", 5=>"(Ombre)", 6=>"(Arcane)");
                    $properties['HTMLTOOLTIP']['data'].= "<table><tbody><tr><td>".$properties['DAMAGEDATA_DAMAGE0_MIN']['data']." - ".$properties['DAMAGEDATA_DAMAGE0_MAX']['data']." ".$tooltip["damage"]." ".$damage[$properties['DAMAGEDATA_DAMAGE0_TYPE']['data']]."</td>";
                    $properties['HTMLTOOLTIP']['data'].= "<th>".$tooltip["speed"]." ".$properties['DAMAGEDATA_SPEED1']['data']."</th></tr></tbody></table>";
                    $properties['HTMLTOOLTIP']['data'].= "(".$properties['DAMAGEDATA_DPS2']['data'].$tooltip["dps"]."<br/>";
                }

                if ($properties['REQUIREDSKILL'])
                   $properties['HTMLTOOLTIP']['data'].= $tooltip["requires"]." ".$properties['REQUIREDSKILL']['attr']['NAME']." (".$properties['REQUIREDSKILL']['attr']['RANK'].")<br/>";
                if ($properties['ARMOR'])
                   $properties['HTMLTOOLTIP']['data'].= $tooltip["armor"]." ".$properties['ARMOR']['data']."<br/>";
		   
		$caracs = array("BLOCKVALUE" => "block", "BONUSSTRENGTH" => "strength", "BONUSAGILITY"=>"agility",
		"BONUSSTAMINA" => "stamina","BONUSINTELLECT"=>"intellect","BONUSSPIRIT"=>"spirit",
		"FIRERESIST"=>"fire-resistance","NATURERESIST"=>"nature-resistance","FROSTRESIST"=>"frost-resistance","SHADOWRESIST"=>"shadow-resistance","ARCANERESIST"=>"arcane-resistance");
		foreach ($caracs as $k => $c) {
			if ($properties[$k]['data']) {
			   $properties['HTMLTOOLTIP']['data'].= (((int)$properties[$k]['data'])>0)?"+":"";
			   $properties['HTMLTOOLTIP']['data'].= (int)$properties[$k]['data'];
			   $properties['HTMLTOOLTIP']['data'].= " ".$tooltip[$c]."<br/>";
			}
		}
		
                if ($properties['SOCKETDATA']) {
                   for ($i=0;$i<3;$i++) {
                       if ($properties['SOCKETDATA_SOCKET'.$i]) {
                         $properties['HTMLTOOLTIP']['data'].= "<span class=\"socket-";
			 $color = strtolower($properties['SOCKETDATA_SOCKET'.$i]['attr']["COLOR"]);
			 $properties['HTMLTOOLTIP']['data'].= $color." q0\">".$tooltip[$color."-socket"];
                         $properties['HTMLTOOLTIP']['data'].= "</span><br/>";
                       } else break;
                   }
                   $properties['HTMLTOOLTIP']['data'].="<span class=\"q0\">".$tooltip["socket-bonus"]." : ".$properties['SOCKETDATA_SOCKETMATCHENCHANT'.$i]['data']."</span><br/>";
                }
                
                if ($properties['DURABILITY']) {
                  $properties['HTMLTOOLTIP']['data'] .= $tooltip["durability"]." : ".$properties['DURABILITY']['attr']['CURRENT']." / ".$properties['DURABILITY']['attr']['MAX']."<br/>";
                }
                if ($properties['ALLOWABLECLASSES']) {
                    $properties['HTMLTOOLTIP']['data'] .= $tooltip["classes"].": ";

                    for ($i=0;$i<10;$i++) {
                         if ($properties['ALLOWABLECLASSES_CLASS'.$i]) {
                             if ($i>0) $properties['HTMLTOOLTIP']['data'] .= ", ";
                             $properties['HTMLTOOLTIP']['data'] .= $properties['ALLOWABLECLASSES_CLASS'.$i]['data'];
                         } else {
                            $properties['HTMLTOOLTIP']['data'] .= "<br/>";
                            break;
                         }
                    }
                }
                if ($properties['STARTQUESTID']) {
                      $properties['HTMLTOOLTIP']['data'] .= "<a href=\"http://www.wowhead.com/?quest=".$properties['STARTQUESTID']['data']."\">".$tooltip["begins-quest"]."</a><br/>";
                }


                if ($properties['REQUIREDLEVEL']) {
                    $properties['HTMLTOOLTIP']['data'] .= $tooltip["requires-level"]." ".$properties['REQUIREDLEVEL']['data']."<br/>";
                }
                if ($properties['REQUIREDABILITY']) {
                    $properties['HTMLTOOLTIP']['data'] .=  $tooltip["requires"]." ".$properties['REQUIREDABILITY']['data']."<br/>";
                }
		if ($properties['GEMPROPERTIES']) {
                    $properties['HTMLTOOLTIP']['data'] .=  $properties['GEMPROPERTIES']['data']."<br/>";
                }
		
                $properties['HTMLTOOLTIP']['data'] .="</td></tr></table>";
                $properties['HTMLTOOLTIP']['data'] .="<table><tr><td>";
		
		$caracs = array("BONUSDEFENSESKILLRATING" => "increase-defense", 
		"INCREASEDODGE"=>"increase-dodge","BONUSPARRYRATING"=>"bonusParryRating",
		"BONUSBLOCKRATING" => "bonusBlockRating", "BONUSCRITSPELLRATING"=>"improve-spell-crit",
		"BONUSHITSPELLRATING" => "improve-spell","BONUSCRITRATING"=>"improve-crit-strike","BONUSHITRATING"=>"improve-hit-rating", 
		"BONUSHITTAKENRATING"=>"bonusHitTakenRating","BONUSCRITTAKENRATING"=>"bonusCritTakenRating",
		"BONUSRESILIENCE"=>"improve-resilience","BONUSHASTERATING"=>"bonusHasteRating");
		foreach ($caracs as $k => $c) {
			$k = strtoupper($k);
			if ($properties[$k]) {
			   $properties['HTMLTOOLTIP']['data'] .= "<span class=\"q2\">".$tooltip[$c]." ".$properties[$k]['data']."</span><br/>";
                }
		}
                
                if ($properties['SPELLDATA']) {
                  for ($i=0;$i<10;$i++) {
                         if ($properties['SPELLDATA_SPELL'.$i]) {
                             $properties['HTMLTOOLTIP']['data'] .= "<span class=\"q2\">";
                             switch ($properties['SPELLDATA_SPELL'.$i.'_TRIGGER']['data']) {
                               case "1" : $properties['HTMLTOOLTIP']['data'] .= $tooltip["equip"].": "; break;
                               case "2" : $properties['HTMLTOOLTIP']['data'] .= $tooltip["chance-on-hit"].": "; break;
                               default :  $properties['HTMLTOOLTIP']['data'] .= $tooltip["use"].": ";
                             }
                             $properties['HTMLTOOLTIP']['data'] .= $properties['SPELLDATA_SPELL'.$i.'_DESC']['data']."</span><br/>";
                         } else break;
                    }
                }
                
                if ($properties['SETDATA']) {
                  $properties['HTMLTOOLTIP']['data'] .= "<br/><span class=\"q\">".$properties['SETDATA_NAME0']['data']."</span>";
                  for ($i=1;$i<15;$i++) {
                         if ($properties['SETDATA_ITEM'.$i]) {
                             $properties['HTMLTOOLTIP']['data'] .= "<div class=\"q0 indent\">".$properties['SETDATA_ITEM'.$i]['attr']['NAME']."</div>";
                         } elseif ($properties['SETDATA_SETBONUS'.$i]) {
                           $properties['HTMLTOOLTIP']['data'] .= "<br/><span class=\"q0\">(".$properties['SETDATA_SETBONUS'.$i]['attr']['THRESHOLD'].") : ".$properties['SETDATA_SETBONUS'.$i]['attr']['DESC']."</span>";
                         } else break;
                    }
                }

                if ($properties['DESC']['data']) {
                   $properties['HTMLTOOLTIP']['data'] .= "<span class=\"q\">\"".$properties['DESC']['data']."\"</span><br/>";
                }

                $properties['HTMLTOOLTIP']['data'] .="</td></tr></table>";

		if (debug_mode) {
                  var_dump(strpos($xml_item_data,"<itemTooltip>"+13));
	//	   var_dump($item);
		   var_dump($properties);
		}
		// create the tooltip html
		/*if (substr($properties['HTMLTOOLTIP']['data'], 0, 7) != '<table>') {
			$item['html'] = '*' . $properties['HTMLTOOLTIP']['data'] . '</td></tr></table>';
		} else {
		*/	$item['html'] = $properties['HTMLTOOLTIP']['data'];
		//}
		// remove the width attributes from the tooltips, they mess the tooltip up in IE
		$item['html'] = str_replace(' width="100%"', '', $item['html']);
		// tooltip title/item name links to its wowhead page
		$item['html'] = str_replace($item['name'], '<a href=\'' . $item['link'] . '\' target=\'_new\'>' . $properties['NAME']['data'] . '</a>', $item['html']);
		// add escape slashes
		$item['html'] = str_replace('"', '\'', $item['html']);

                /*$item['html'] = preg_replace('/<a(.*?)>/', '', $item['html']);
                $item['html'] = str_replace('</a>', '', $item['html']);
		*/// place the tooltip content html into the tooltip template
		$template_html = trim(file_get_contents(dirname(__FILE__) . '/../templates/' . WOWHEAD_TEMPLATE));
		$item['html'] = str_replace('{ITEM_HTML}', $item['html'], $template_html);

                foreach (array_keys($item) as $key) {
                  if (debug_mode) var_dump($item[$key]);
	          $item[$key] = utf8_decode($item[$key]);
	          if (debug_mode) var_dump($item[$key]);
	          
                }
		return $item;	
	}

	// downloads an icon
	function downloadIcon($iconname) {
		if (DOWNLOAD_ICONS) {
			if (file_exists(LOCAL_ICON_STORE_PATH . $iconname . ICON_EXTENSION)) {
				// file already exists, dont download
				return true; // return true, the icon is ready to use
			} else {
				// the icon is not available, attempt to download
				return download_file(REMOTE_ICON_STORE_PATH . $iconname . ICON_EXTENSION, LOCAL_ICON_STORE_PATH . $iconname . ICON_EXTENSION);
			}
		}
		return false;
	}

	// Cleans up resources used by this object.
	function close() {}
}

?>