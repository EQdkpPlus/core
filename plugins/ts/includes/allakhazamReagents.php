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

$stats_debugReagents = true ;

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
		$recipe_name = $new_recipe;
		
		//**************************************
		// MODDED by ROKI: Added the boolean to remove 'Formula: ' from the front of the enchanting items. This requires the users to input Enchanting skills like "Formula: Enchant ..." 
		// 		Something similar needs to be done for the Alchemy Transmutes
		//**************************************	
		if ($trade_id == 1){
			itemstats_debugReagents('Inside Enchanting '.$new_recipe);
			$recipe_name = substr($new_recipe,9);
			
		}
		
		itemstats_debugReagents('trade_id'.$trade_id);
		
		switch($trade_id){
		    case 1: $filename = "http://wow.allakhazam.com/db/skill.html?line=333"; break;
		    case 2: $filename = "http://wow.allakhazam.com/db/skill.html?line=165"; break;
		    case 3: $filename = "http://wow.allakhazam.com/db/skill.html?line=164"; break;
		    case 4: $filename = "http://wow.allakhazam.com/db/skill.html?line=202"; break;
		    case 5: $filename = "http://wow.allakhazam.com/db/skill.html?line=197"; break;
		    case 6: $filename = "http://wow.allakhazam.com/db/skill.html?line=171"; break;
		    case 7: $filename = "http://wow.allakhazam.com/db/skill.html?line=185"; break;
		    case 8: $filename = "http://wow.allakhazam.com/db/skill.html?line=755"; break;
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
		
		preg_match_all('#>'.$recipe_name.'</a(.*?)</tr>#is', $data, $recipematch);
        itemstats_debugReagents('Recipename '.$recipe_name);
		
        itemstats_debugReagents('1. Preg: '.$recipematch[0][0]); //long output
		
		//**************************************
		// MODDED by ROKI: changed the [1-99] to [0-9]+ for a more robust regular expression
		//**************************************
        preg_match_all('#(<td>|,).?([0-9]+)x&nbsp;<a(.*?)<font color=(.*?)>(.*?)</font></a>#is', $recipematch[0][0], $reagentmatch, PREG_SET_ORDER);
		//recipematch[0][0] //whole String
		//reagentmatch[x][1] //quantity
		//reagentmacht[x][4] //reagentname

		itemstats_debugReagents('Quantaty 1. Reagent: '.$reagentmatch[0][2]);
		itemstats_debugReagents('Name 1. Reagent: '.$reagentmatch[0][5]);
			
		if(empty($reagentmatch[0][2])){
                                       itemstats_debugReagents('Cancel-------------');	
                                       return $reagents;}		
		$i=0;
		foreach ($reagentmatch as $Wert){
			$i=$i+1;		
			if($Wert[1]==""){break;}
			itemstats_debugReagents("Wert[5] = ".$Wert[5]);
			if(strcasecmp($Wert[5], $new_recipe) != 0){
				if($i==1){
					$reagents = $Wert[2].'x'.$Wert[5];
				//strings zusammenführen
				}else{ 
					$reagents = $reagents.','.$Wert[2].'x'.$Wert[5];
				} //strings zusammenführen
			}
		}	
		itemstats_debugReagents('-------------');	
		return $reagents;
	
}
?>
