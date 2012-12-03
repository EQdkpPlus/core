<?php
//::///////////////////////////////////////////////
//::
//:: Plugin to EQDKP PLUGIN: Tradeskills
//:: © 2006 Achaz
//:: original Plugin by CNSDEV (http://cnsdev.dk)
//:: Contact: Cralex_NS - cns@cnsdev.dk
//::
//:://////////////////////////////////////////////
//::
//:: File: allakhazamReagents.php
//:: Created on: 01. Oct 2006
//::
//:: DEPENDENCIES:
//:: * Itemstats2 or higher
//::
//:://////////////////////////////////////////////
//:: VERSION: 0.15alpha
//::implements function get Reagents for cnsdev Tradeskill Addon 0.96beta only
//::
//::credits for debug output to Corgan!

$stats_debugReagents = false ;

function itemstats_debugReagents($debugReagentsmsg)
{
	global $stats_debugReagents ;

	if(!$stats_debugReagents){return;}

	$debugReagentsfile = dirname(__FILE__) . '/debugReagents.txt' ;


	if(file_exists($debugReagentsfile))
	{
	        $debugReagentsmsg .="\r\n";
	        $fp=fopen($debugReagentsfile,"a");
	        fwrite($fp,$debugReagentsmsg);
	        fclose($fp);
	}
}
	
function get_Reagents($trade_id, $new_recipe, &$wow_name)
{
		itemstats_debugReagents('trade_id: '.$trade_id);
		$xml_helper = new XmlHelper();
		
		switch($trade_id){
		    case 1: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/verzauberkunst.html"; break;
		    case 2: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/lederverarbeitung.html"; break;
		    case 3: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/schmiedekunst.html"; break;
		    case 4: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/ingenieurskunst.html"; break;
            case 5: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/schneiderei.html"; break;
		    case 6: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/alchimie.html"; break;
		    case 7: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/kochkunst.html"; break;
		    case 8: $filename = "http://www.buffed.de/world-of-warcraft/blasc/berufe/juwelenschleifen.html"; break;
		    default: $filename = "";
		}	
		
		itemstats_debugReagents('filename:'.$filename);
#                $ch = curl_init($filename);
#                $timeout = 15; // set to zero for no timeout
#                curl_setopt ($ch, CURLOPT_URL, $filename);
#                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
#                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
#                $fp = curl_exec($ch);
#                curl_close($ch);

		$fp = itemstats_read_url($filename);
		if (empty($fp))
			{
                itemstats_debugReagents('cannot open '.$filename);
				$itemstats .= "cannot open ".$filename;
			}

                $data = $fp;

		//itemstats_debugReagents($data); //VERY long output
		
		//buffed.de Inkonsistenz mit Verzauberung "Zweihandwaffe(n)" umgehen:

		if(strpos($new_recipe , 'Zweihandwaffe ') !== false )
		{
		   $new_recipe=substr_replace ( $new_recipe, 'Zweihandwaffen', 0 , 13 );
		   itemstats_debugReagents('Zweihandwaffenreplacement');
		}
		
		preg_match_all('#>'.$new_recipe.'</span>(.*?)</div><t(.*?)</td>#i', $data, $recipematch);
	        itemstats_debugReagents('Recipename '.$new_recipe);

		itemstats_debugReagents('1. Preg: '.$recipematch[0][0]); //long output

//-----blasc Namen mit Teil des evtl. vorhandenen Rezeptnamens überprüfen---------
		itemstats_debugReagents('findwowIDdata: '.$recipematch[2][0]);
    	if(preg_match('#href="\?i=([0-9]*?)" onmouseover#i', $recipematch[2][0], $findwowid)==1)
        {
        		$recipe_wowid = $findwowid[1];
          		itemstats_debugReagents('wowID: '.$recipe_wowid);
            	$xml_checkname_data = itemstats_read_url('http://www.buffed.de/xml/i' . $recipe_wowid.'.xml');
             	$xml_checkname_name = $xml_helper->parse($xml_checkname_data, 'InventoryName');
              	itemstats_debugReagents('wowFormel-Name: '.$xml_checkname_name);
           		if(strpos($xml_checkname_name, $new_recipe) !== false )
	         	{
                      itemstats_debugReagents('wow Formelname ist gleich blasc listen name');                           
                }
                else
                {
                     itemstats_debugReagents('Buffedlistenname: ' .$new_recipe. ' ist ungleich Rezeptname: '.$xml_checkname_name);
                     //preg_match('#Formel:(.*?)#',$xml_checkname_name, $cleanedxmlname);
                     $posnum=strpos($xml_checkname_name, ":");
                     $wow_name= substr($xml_checkname_name, $posnum+2);
                     //$wow_name = $cleanedxmlname[1];
                     itemstats_debugReagents('wow_name: ' .$wow_name);   		   
                }                       
        }
        else
        {
            itemstats_debugReagents('Kein Rezept angegeben');
        }
//-------------------------------------
		
		preg_match_all('#<div class="prof">(.*?)<a href="\?i=(.*?)"(.*?)</div>#i', $recipematch[0][0], $reagentmatch, PREG_SET_ORDER);
		//recipematch[0][0] //gesamter String
		//reagentmatch[x][1] //Anzahl
		//reagentmacht[x][2] //Reagenzid

		itemstats_debugReagents('Anzahl 1. Reagenz: '.$reagentmatch[0][1]);
		itemstats_debugReagents('ID 1. Reagenz: '.$reagentmatch[0][2]);
		
		if(empty($reagentmatch[0][1]))
        {
               	itemstats_debugReagents('Abbruch -------------');
                 return $reagents;
        }
		
		$i=0;
		foreach ($reagentmatch as $Wert) 
		{
		$i=$i+1;
		itemstats_debugReagents('Durchgang '.$i.' Wert1='.$Wert[1].' Wert2='.$Wert[2]);
		if($Wert[1]==""){break;}
		$xml_data = itemstats_read_url('http://www.buffed.de/xml/i' . $Wert[2].'.xml');
		$xml_name = $xml_helper->parse($xml_data, 'InventoryName');
		if($i==1){$reagents = $Wert[1].'x'.$xml_name;} //strings zusammenführen
		else{ $reagents = $reagents.','.$Wert[1].'x'.$xml_name;} //strings zusammenführen
		}	
      	itemstats_debugReagents('-------------');		

		return $reagents;	
}
?>
