<?php

/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 *
 * viewboss.php by Micromancer
 * modded by corgan, to work with bossprogress und bosscounter
 ******************************/


define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');


$specialchars = array("/&/i", "/'/i", "/´/i", "/;/i");

if(isset($_GET['boss']))
{
	$boss = strtolower(stripslashes(preg_replace($specialchars, "", $_GET['boss']))); 	// Sonderzeichen und Leerzeichen entfernen

	// schauen ob boss bekannt ist
//   if(in_array($_GET['boss'], $boss_raid_note))
//    {
    		$realboss = preg_replace($specialchars, "", $_GET['boss']) ;
    		$name2 = preg_replace($specialchars, "", $_GET['name2']) ;
    		$name3 = preg_replace($specialchars, "", $_GET['name3']) ;
    		$searchstring = preg_replace($specialchars, "", $_GET['search']) ;
    		$searchstring = str_replace("\\","",$searchstring); 

        $sql = "Select raid_id from ". RAIDS_TABLE 
        ." where (raid_note like '%".$realboss."%')" 
        ." or (raid_name like '%".$realboss."%')"
        ." or (raid_name like '%".$name2."%')"
        ." or (raid_name like '%".$name3."%')"  
        ." or (raid_note like '%".$name2."%')"
        ." or (raid_note like '%".$name3."%')";
        
        $searchnamesarray = explode(",",$searchstring) ;			
				foreach($searchnamesarray as $wert)
				{
					if(strlen($wert) > 1)
					{
						$sql .= " or (raid_note like '%".trim($wert)."%')";
						$sql .= " or (raid_name like '%".trim($wert)."%')";
					}
				}	
        
        $result = $db->query($sql);
        $total_kills = mysql_num_rows($result);
        if($total_kills > 0)
        {
        	$killed_once = true;

        	while($row = $db->fetch_record($result))
        	{
           		$sql2 = "Select item_name from ". ITEMS_TABLE ." where raid_id = '".$row['raid_id']."' order by item_name";
            	$result2 = $db->query($sql2);

            	while($row2 = $db->fetch_record($result2)){
            		$tempitems[$row2['item_name']]++;
            	}
        	}
        	$db->free_result($result);
        	$db->free_result($result2);

        	// Items nach dropcount ordnen
	    	if($tempitems)
	    	{
				foreach ( $tempitems as $item => $count )
				{
					$items[$count][] = $item;
				}
			}

        	// absteigend nach itemcount(index) sortieren
        	if($items)
        	{
				krsort($items);
				foreach ( $items as $count => $array_items)
				{
				// alphabetisch sortieren
					sort($array_items);
					foreach ($array_items as $item_name)
					{
						$tpl->assign_block_vars('items_row', array(
						'ROW_CLASS'       => $eqdkp->switch_row_class(),
						'ITEM_NAME' => itemstats_decorate_name(stripslashes($item_name)),
						'DROP_COUNT' => $count,
						'DROP_PERCENTAGE' => round($count * 100 / $total_kills, 2) . "%",
						'U_VIEW_ITEM' => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '='.get_item_id($item_name))
						);

					}
				}
			}
        }
        else
        {
        	$killed_once=false;
        }

        $bossx = './images/bosses/'.strtolower($boss).".gif" ;
        $bossx2 = './images/bosses/'.strtolower($name2).".gif" ;
        $bossx3 = './images/bosses/'.strtolower($name3).".gif" ;
        
        if(file_exists($bossx))
        {
        $boss = '<img src="'.$bossx.'" alt="'.$realboss.'">' ;
        }
        elseif(file_exists($bossx2))
        {
        $boss = '<img src="'.$bossx2.'" alt="'.$realboss.'">' ;
        }
        elseif(file_exists($bossx3))
        {
        $boss = '<img src="'.$bossx3.'" alt="'.$realboss.'">' ;
        }       
        else
        {
        $boss = '' ;
        }

        $tpl->assign_vars(array(
        	'TOTAL_KILLS' => $total_kills,
            'BOSS_IMAGE' => $boss,
            'BOSS_NAME' => $realboss,
            'KILLED' => $killed_once,
            'ITEMNAME' => $user->lang['droprate_name'] ,
            'COUNT' => $user->lang['droprate_count'] ,
            'DROPCHANCE' => $user->lang['droprate_drop']
            )
            );
        $eqdkp->set_vars(array(
   	 		'page_title'    => $user->lang['droprate_loottable'] .' '. $realboss,
    		'template_file' => 'viewboss.html',
    		'display'       => true)
			);
//    }
//    else
//    {
//    	#message_die("Boss not found.");
//
//    	$tpl->assign_vars(array(
//		        	'TOTAL_KILLS' => 0,
//		            'BOSS_IMAGE' => 'No Boss found',
//		            'BOSS_NAME' => '',
//		            'KILLED' => '',
//		            'ITEMNAME' => $user->lang['droprate_name'] ,
//		            'COUNT' => $user->lang['droprate_count'] ,
//		            'DROPCHANCE' => $user->lang['droprate_drop']
//		            )
//		            );
//		        $eqdkp->set_vars(array(
//		   	 		'page_title'    => $user->lang['droprate_loottable'] .' '. $realboss,
//		    		'template_file' => 'viewboss.html',
//		    		'display'       => true));
//    }
}
else
{
	#message_die("No Boss was provided.");
	    	$tpl->assign_vars(array(
			        	'TOTAL_KILLS' => 0,
			            'BOSS_IMAGE' => 'No Boss found',
			            'BOSS_NAME' => '',
			            'KILLED' => '',
			            'ITEMNAME' => $user->lang['droprate_name'] ,
			            'COUNT' => $user->lang['droprate_count'] ,
			            'DROPCHANCE' => $user->lang['droprate_drop']
			            )
			            );
			        $eqdkp->set_vars(array(
			   	 		'page_title'    => $user->lang['droprate_loottable'] .' '. $realboss,
			    		'template_file' => 'viewboss.html',
		    		'display'       => true));
}

function get_item_id($name)
{
    global $db;
    $sql = "Select item_id from ". ITEMS_TABLE ." where item_name=\"".stripslashes($name)."\"";
    return $db->query_first($sql);
}