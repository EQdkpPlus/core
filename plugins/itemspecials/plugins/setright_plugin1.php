<?php
/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * setright_plugin.php
 * Changed: Wed August 15, 2006
 * 
 ******************************/

/*
Variables to use:
$calc_data['raid_total'] : Total amount of raids
$calc_data['countperclass']['Classname'] : Members per Class (Count). Array! Classname p.e. "Hunter", have a look at the includes/data.php!
$calc_data['countperraid']['RaidID'] : Membercount/Raid (Count). Array! RaidID: Have a look at the raid_attendees sql table.!

$calc_data['itemcount'] : The total Count of the SETitems/User
$calc_data['itemsumm'] : The total Count of the all items/User
$calc_data['raidcount'] : The total Count of the Raids/User
$calc_data['membername'] : The Name of the current member
$calc_data['class'] : The Class of the current member
$calc_data['member_id'] : The Member-ID of the current member
$calc_data['member_rank'] : The Rank of the current member
$calc_data['member_level'] : The Level of the current member
$calc_data['dkp_total'] : Total DKP of Member
$calc_data['current_dkp'] : Current DKP of Member

$itemdata is a "array_merge_recursive"-Array of the tier1, tier2, tier3 Data

You can use the normal EQDKP Variables in the function.
*/

$itemspecial_plugin['setright_plugin1.php'] = array(
			'name'			    => 'Normal Calculation',
			'path'			    => 'setright_plugin1',
			'contact'		    => 'webmaster@wallenium.de',
			'version'		    => '1.0.0');

function CalculateSetRight($calc_data, $itemdata)
{
  // Check if the Setcount is nil, set to 1 if its 0
  ($calc_data['itemcount']== 0 ? $setcount = 1 : $setcount = $calc_data['itemcount']);
  
  // calculate the thing
  $output = round($calc_data['raidcount']/$setcount, 2);
  return $output;
}

?>
