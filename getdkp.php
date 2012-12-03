<?php
/******************************
 * GetDKP Plus
 *
 * Corgan @ Server Antonidas PvE German -> www.eqdkp-plus.com
 * ------------------
 * getdkp.php
 *
 * Version 2.65
 *
 * 06/25/06
 * 06/29/07 - Specialchar support
 * 06/18/11 - multidkp support
 * 07/03/01 - new item iterration
 * Support English / German -> http://www.seniorenraid.de/forum/forumdisplay.php?f=13
 * 08/03/13 support for dkp.exe 8.0
 * 08/03/13 - Raidplaner support
 * $Id$
 ******************************/

############# you dont have to change anything below !!!!  ######################################
#################################################################################################

$version = "2.65";
$total_players = 0;
$total_items = 0;
$total_points = 0;
$eqdkp_version = "1.3.x";
$date_created = date("d.m.y G:i:s");
$startString = "\n--[START]\n";
$endString = "\n--[END]\n";

	
define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

if ($conf_plus['pk_getdkp_active'] == 1)
{
	echo "-- Getdkp deactive";
	die();
}

define(TAB, "\t");
define(CRLF, "\r\n");


echo $startString ;
if ( isset($_GET['debug']) )
	{ echo"--DEBUGMODE--\n";}
if ( !defined('EQDKP_INSTALLED') )
	{  
		ECHO $endString,
		message_die('EqDKP is not Install', '', __FILE__, __LINE__, $sql);
	}
echo "--------------------------------------------------------\n";
echo "----                   dkp_list.lua                 ----\n";
echo "---- dkp_list.lua is generated from getdkp.php ".$version." ----\n";
echo "---- created on ".$date_created."\n";
echo "----       support http://www.eqdkp-plus.com        ----\n";
echo "--------------------------------------------------------\n\n";
	if($conf_plus['pk_multidkp'] == 1)
	{
    $sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
            FROM ' . MULTIDKP_TABLE . '
            WHERE multidkp_name IS NOT NULL'
            ;

    $total_multi = $db->query_first('SELECT count(*) FROM ' . MULTIDKP_TABLE . ' WHERE multidkp_name IS NOT NULL');

		if ( !($multi_result = $db->query($sql)) )
		{
			ECHO $endString;
		    message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
		}

		$tableoutput = "multiTable = {\n";

		$count=0 ;
		while ( $multi = $db->fetch_record($multi_result) )
		{
			$count++;
			$sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname
		  							 FROM ' . MULTIDKP2EVENTS_TABLE
		  							 .' WHERE multidkp2event_multi_id ='.$multi['multidkp_id'] ;

				if ( !($multi2event_results = $db->query($sql_events)) )
				{
					ECHO $endString,
					message_die('Could not obta in MultiDKP -> Event information', '', __FILE__, __LINE__, $sql_events);
				}

				$tableoutput .= "	   [$count]= { [\"".strto_wowutf($multi['multidkp_name'])."\"] = { \n";
				$tableoutput .= " \t\t    [\"name\"] = \"".strto_wowutf($multi['multidkp_name'])."\",  \n";
				$tableoutput .= " \t\t    [\"disc\"] = \"".strto_wowutf($multi['multidkp_disc'])."\",  \n";

				$multi2event = '' ;
			  while ( $a_multi = $db->fetch_record($multi2event_results) )
				{ // gehe alle Events durch, die einem Konto zugewiesen wurden
					$multi2event .= strto_wowutf($a_multi['multidkp2event_eventname']).' , ' ;
				}

				# komma am ende entfernen
				$multi2event = preg_replace('# \, $#', '', $multi2event);

				$tableoutput .= " \t\t    [\"events\"] = \"$multi2event\" \n \t\t\t }, \n\t\t  },  \n";

		}
		$tableoutput .= "  }  \n";
			echo $tableoutput ;
	}
	else
	{
		$tableoutput = "multiTable = {\n";
		$tableoutput .= "	   [1]= { [\"dkp\"] = { \n";
	    $tableoutput .= " \t\t    [\"name\"] = \"dkp\",  \n";
		$tableoutput .= " \t\t    [\"disc\"] = \"Raid DKP\",  \n";
		$tableoutput .= " \t\t    [\"events\"] = \" \" \n \t\t\t }, \n\t\t  },  \n";
		$tableoutput .= "  }  \n";
		echo $tableoutput ;
	}



// Get raidpoints without spitting out the whole page

				$member_results = mysql_query("SELECT * FROM ".$table_prefix."members") or die(mysql_error());
				while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
				{
						$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
						$total_points+=$player_dkps;
				}
				$total_pointsset = $total_points ;

				// Get total items
				$item_results = mysql_query("SELECT * FROM ".$table_prefix."items") or die(mysql_error());
				$total_items = mysql_num_rows($item_results);
				$setitems = $total_items ;

				// Get total players
				$member_results = mysql_query("SELECT * FROM ".$table_prefix."members") or die(mysql_error());
				$total_players = mysql_num_rows($member_results);

				if (isset($table_prefix_NS))
				{

					$member_results = mysql_query("SELECT * FROM ".$table_prefix_NS."members") or die(mysql_error());
					while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
					{
						$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
						$total_pointsns+=$player_dkps;
					}
					$total_points = $total_pointsset + $total_pointsns ;

					// Get total items
					$item_results = mysql_query("SELECT * FROM ".$table_prefix_NS."items") or die(mysql_error());
					$Nonsetitems = mysql_num_rows($item_results);
					$total_items = $setitems  + $Nonsetitems;

				}

				if(!isset($total_pointsns))
				{
				$total_pointsns = 0 ;
				}

				$timestamp = time() ;
				echo "DKPInfo = {\n";
								echo "     [\"date\"] = \"$date_created\",\n";
								echo "     [\"timestamp\"] = \"$timestamp\",\n";
								echo "     [\"process_dkp_ver\"] = \"$version\",\n";
								echo "     [\"total_players\"] = $total_players,\n";
								echo "     [\"total_items\"] = $total_items,\n";
								echo "     [\"total_points\"] = "._delimeter($total_points).",\n";
	if (isset($table_prefix_NS)) {
								echo "     [\"set_items\"] = $setitems,\n";
								echo "     [\"nonset_items\"] = $Nonsetitems,\n";
								echo "     [\"total_points_set\"] = "._delimeter($total_pointsset).",\n";
								echo "     [\"total_points_ns\"] = "._delimeter($total_pointsns)."\n";

						      }
				echo "}\n";


########################
# Multi DKP / Normal DKP
########################
$sql = "SELECT *,
		m.member_name,
        c.class_name as member_class,
		m.member_earned + m.member_adjustment - m.member_spent as current_dkp
	FROM
		".$table_prefix."members m, ".$table_prefix."classes c
	WHERE
		m.member_class_id = c.class_id
	GROUP BY
		m.member_name
	ORDER BY
member_name ASC";


	if ( !($result = mysql_query($sql)) ) {
		print $sql ;
		message_die('Could not obtain member DKP information', '', __FILE__, __LINE__, $sql);
	}

if($conf_plus['pk_multidkp'] == 1)
{
	$format_start =
		TAB.TAB.'["%s"] = {'.CRLF;

	$format_dkp =
			TAB.TAB.TAB.'["DKP"] = %s,'.CRLF;

	$format_multidkp =
		TAB.TAB.TAB.'["%s_earned"] = %s,'.CRLF.
		TAB.TAB.TAB.'["%s_spend"] = %s,'.CRLF.
		TAB.TAB.TAB.'["%s_adjust"] = %s,'.CRLF.
		TAB.TAB.TAB.'["%s_current"] = %s,'.CRLF ;

	$format_end =
		TAB.TAB.TAB.'["class"] = "%s",'.CRLF.
		TAB.TAB.TAB.'["rcount"] = %s,'.CRLF.
		TAB.TAB.'},'.CRLF;


	if(mysql_num_rows($result) >= 1)
	{
		$outputdkp = 'gdkp = {'."\r\n";
		$outputdkp .=  TAB.'["players"] = {'.CRLF;

		while ($row = $db->fetch_record($result)) # member row
		{
			 $rc_sql = 'SELECT count(*)
       FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
       WHERE (ra.raid_id = r.raid_id)
       AND (ra.member_name='".$row['member_name']."')";

       if ( ($result_lifetime = mysql_query($rc_sql)) )
       {
       	 $individual_raid_count_all = $db->query_first($rc_sql);
				$row['raid_count'] = $individual_raid_count_all ;

			 }
			// search Alliases
			
			$al_found = 0;
			$al_sql = mysql_query("SELECT * FROM ".$table_prefix."ctrt_aliases" );
			if ( $al_sql)
				{
					while($row9 = mysql_fetch_array($al_sql, MYSQL_ASSOC))
					{
						if ($row9['alias_name'] == $row['member_name'])
						{
							$al_found = 1;
						}
					}
				}
			
			if ($al_found == 0) 
				{
					
					$outputdkp .= sprintf($format_start, strto_wowutf($row['member_name']));
					$outputdkp .= sprintf($format_dkp,$row['current_dkp']);
					$member_multidkp = $dkpplus-> multiDkpMemberArray($row['member_name']) ; // create the multiDKP Table
	
					if(!empty($member_multidkp[$row['member_name']]))
					{
						foreach ($member_multidkp[$row['member_name']] as $key)  # konten row
							{
									$outputdkp .= sprintf($format_multidkp, $key['name'] ,$key['earned'],
																										 $key['name'], $key['spend'],
																										 $key['name'], $key['adjust'],
																										 $key['name'], $key['current'] );
							}
					}
					$outputdkp .= sprintf($format_end, strto_wowutf($row['member_class']),$row['raid_count']);
				}
		}

	$outputdkp = substr($outputdkp, 0, strlen($outputdkp)-3);
	echo $outputdkp.CRLF.TAB.'}'.CRLF.'}'.CRLF;
	}
}
else
{
	$format =
		TAB.TAB.'["%s"] = {'.CRLF.

		TAB.TAB.TAB.'["dkp_earned"] = %s,'.CRLF.
		TAB.TAB.TAB.'["dkp_spend"] = %s,'.CRLF.
		TAB.TAB.TAB.'["dkp_adjust"] = %s,'.CRLF.
		TAB.TAB.TAB.'["dkp_current"] = %s,'.CRLF.

		TAB.TAB.TAB.'["class"] = "%s",'.CRLF.
		TAB.TAB.TAB.'["rcount"] = %s,'.CRLF.
		TAB.TAB.'},'.CRLF;

	if(mysql_num_rows($result) >= 1)
	{
		$outputdkp = 'gdkp = {'."\r\n";
		$outputdkp .=  TAB.'["players"] = {'.CRLF;

		while ($row = $db->fetch_record($result))
		{
			 $rc_sql = 'SELECT count(*)
       FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
       WHERE (ra.raid_id = r.raid_id)
       AND (ra.member_name='".$row['member_name']."')";

       if ( ($result_lifetime = mysql_query($rc_sql)) )
       {
        $individual_raid_count_all = $db->query_first($rc_sql);
				$row['raid_count'] = $individual_raid_count_all ;
			 }
			 //Search Allias
			$al_found = 0;
			$al_sql = mysql_query("SELECT * FROM ".$table_prefix."ctrt_aliases" );
			if ( $al_sql)
				{
					while($row9 = mysql_fetch_array($al_sql, MYSQL_ASSOC))
					{
						if ($row9['alias_name'] == $row['member_name'])
						{
							$al_found = 1;
						}
					}
				}
			
			if ($al_found == 0) 
				{
					$outputdkp .= sprintf($format,
					strto_wowutf($row['member_name']),
					runden($row['member_earned']),
					runden($row['member_spent']),
					runden($row['member_adjustment']),
					runden($row['current_dkp']),
					strto_wowutf($row['member_class']),
					$row['raid_count']
					);
				}
		}
		$outputdkp = substr($outputdkp, 0, strlen($outputdkp)-3);
		echo $outputdkp.CRLF.TAB.'}'.CRLF.'}'.CRLF;

	}
}
$db->free_result($result);

################################################################
# Nonset
################################################################

if (isset($table_prefix_NS))
{

#	$sql = "SELECT
#			ra.member_name,
#			c.class_name as member_class,
#			m.member_earned + m.member_adjustment - m.member_spent as current_dkp
#		FROM
#			".$table_prefix_NS."raids r, ".$table_prefix_NS."raid_attendees ra, ".$table_prefix_NS."members m, ".$table_prefix_NS."classes c
#		WHERE
#			ra.raid_id = r.raid_id
#			AND m.member_name = ra.member_name
#			AND m.member_class_id = c.class_id
#		GROUP BY
#			ra.member_name
#		ORDER BY
#	member_name ASC";

$sql = "SELECT
		m.member_name,
        c.class_name as member_class,
		m.member_earned + m.member_adjustment - m.member_spent as current_dkp
	FROM
		".$table_prefix_NS."members m, ".$table_prefix_NS."classes c
	WHERE
		m.member_class_id = c.class_id
	GROUP BY
		m.member_name
	ORDER BY
member_name ASC";



	if ( !($result = $db->query($sql)) ) {
		print $sql ;
		message_die('Could not obtain member DKP information', '', __FILE__, __LINE__, $sql);
	}

	define(TAB, "\t");
	define(CRLF, "\r\n");

	$format =
		TAB.TAB.'["%s"] = {'.CRLF.
		TAB.TAB.TAB.'["dkp"] = %s,'.CRLF.
		TAB.TAB.TAB.'["class"] = "%s",'.CRLF.
		TAB.TAB.'},'.CRLF;

if(mysql_num_rows($result) >= 1)
{
	$outputdkp = 'gdkp_NonSet = {'."\r\n";
	$outputdkp .=  TAB.'["players"] = {'.CRLF;

	while ($row = $db->fetch_record($result)) {
		$outputdkp .= sprintf($format,
			strto_wowutf($row['member_name']),
			$row['current_dkp'],
			strto_wowutf($row['member_class'])
		);
	}
	$outputdkp = substr($outputdkp, 0, strlen($outputdkp)-3);
	echo $outputdkp.CRLF.TAB.'}'.CRLF.'}'.CRLF;

}


	$db->free_result($result);
}
###############################


	//----------------------------------------------------------
	// DKP_ROLL_PLAYERS Output
	//----------------------------------------------------------

	$member_results = mysql_query("SELECT * FROM ".$table_prefix."members") or die(mysql_error());
	while($row = mysql_fetch_array($member_results, MYSQL_ASSOC))
	{

		$player = strtolower($row['member_name']);
		if ($eqdkp_version == "1.3.x") {
				// eqdkp 1.3.x support
				$player_class_id = $row['member_class_id'];

				$class_results = mysql_query("SELECT * FROM `".$table_prefix."classes` WHERE `class_id` =".$player_class_id) or die(mysql_error());
					$class_row = mysql_fetch_array($class_results, MYSQL_ASSOC);

					$player_class = $class_row['class_name'];
		} else {
				// eqdkp 1.2.x support
				$player_class = $row['member_class'];
		}

		$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];

	   # $player_data[$player]['RaidPoints'] = $player_dkps;
	   # $player_data[$player]['Class'] = $player_class;

		$total_points+=$player_dkps;
			$total_players++;
	} // end while

	if (isset($table_prefix_NS))
	{
		$item_results = mysql_query("SELECT * FROM ".$table_prefix."items UNION SELECT * FROM ".$table_prefix_NS."items " ) or die(mysql_error());
		#echo "--NS" ;
	}
	else
	{
		$item_results = mysql_query("SELECT * FROM ".$table_prefix."items" ) or die(mysql_error());
		#echo "--Setonly" ;
	}

	while($item_row = mysql_fetch_array($item_results, MYSQL_ASSOC))
	{
			# echo $item_row['item_buyer'] .":". $item_row['item_name'] .":". $item_row['item_value'] ."\n";

			$buyer = ucfirst($item_row['item_buyer']);

			#$buyer = $item_row['item_buyer'];
			$item = $item_row['item_name'];
			$item_value = $item_row['item_value'];

			$player_data[$buyer]['Items'][$item] = $item_value;

			$total_items++;
	}


	if($player_data)
	{
		echo "DKP_ITEMS = {\n" ;
		foreach ( $player_data as $player => $player_values )
		{
				echo "     [\"". strto_wowutf($player) ."\"] = {\n";

				foreach ($player_values as $value_name => $value)
				{
				  if (is_array($value))
				  {
					echo "          [\"$value_name\"] = {\n";
					$i = 0 ;
					foreach ($value as $item_name => $item_cost)
					 {
					 	$i++;
					 $item_name = str_replace("?", "'", $item_name);
					 $item_name = strto_wowutf($item_name);

					 echo "               [$i] = { [\"name\"] = \"$item_name\",\n";

					 echo "               	       [\"dkp\"] = $item_cost }, \n";
					 }
				   echo "      		 },\n";
				   }
				   else
				   {
					 if (preg_match("/-?[.0-9]{1,7}/", $value))
					 {
					   // Is a number
					   echo "          [\"$value_name\"] = $value,\n";
					   }
					   else
					   {// Is a string
						 echo "          [\"$value_name\"] = \"$value\",\n";
					   }
					 }
				}

				echo "     },\n";
		}
		echo "}\n\n"; // End of DKP_ROLL_PLAYERS
	}
	else
		{
			echo "-- No Items --\n\n";
		}
############# ######################################

	if ( $_GET['itemid'] != 1 and $conf_plus['pk_getdkp_itemids'] == 1) 
	{
		$item_array = array();
		#items Tabelle
		###############
		$item_results = mysql_query("SELECT distinct(game_itemid),item_name  FROM ".$table_prefix."items" );
	
		while($item_row = mysql_fetch_array($item_results, MYSQL_ASSOC))
		{
			if((strlen($item_row[game_itemid]) > 3) and (!isset($item_array[$item_row[game_itemid]]) ))
			{
			  $item_array[$item_row[game_itemid]] = strto_wowutf($item_row[item_name]) ;
			}
		}
	
		#Itemstats Tabelle
		##################
		$item_results = mysql_query("SELECT distinct(item_name),item_id  FROM item_cache" ) ;
		if( !($item_result))
			{
				echo "-- No Item IDs --\n\n";
				echo $endstring;
				
			}
		else
		{
			while($item_row = mysql_fetch_array($item_results, MYSQL_ASSOC))
			{
				if((strlen($item_row[item_id]) > 3) and (!isset($item_array[$item_row[item_id]])) )
				{
				  $item_array[$item_row[item_id]] = strto_wowutf($item_row[item_name]) ;
				}
			}
		
			$itemsoutput = "getdkp_itemids = nil \n";
			IF(isset($item_array))
			{
				$itemsoutput = "getdkp_itemids = {\n";
				foreach ($item_array as $key => $value)
				{
					if (is_numeric($key)) 
					{	
						$itemsoutput .= "	\t   [\"".$value."\"]= ".$key." , \n";
					}
				}
				$itemsoutput .= "}\n\n";
			}
			echo $itemsoutput;
		}
	}
	else
	{
		echo"-- ItemId deactive\n\n";
	}

	#### CTRT Alias
	#################################################
	if ( $_GET['allias'] != 1) 
	{
		$alias_results = mysql_query("SELECT alias_member_id, alias_name FROM ".$table_prefix."ctrt_aliases" );
		$alias = array() ;
		if ( !($alias_results))
			{
				echo "-- No Alliases --\n\n";
			}
		else
			{
				while($row = mysql_fetch_array($alias_results, MYSQL_ASSOC))
				{
					$alias_array[getMemberNamebyID($row['alias_member_id'])][] = $row['alias_name'] ;
				}
			
				$format_start =
					TAB.TAB.'["%s"] = {'.CRLF;
			
				$format_member =
					TAB.TAB.TAB.'[%s] = "%s",'.CRLF ;
			
				if (!isset($alias_array))
				{
					$aliasoutput = "gdkp_alliases = nil \n\n";
				}else
				{
					$aliasoutput = 'gdkp_alliases = {'."\r\n";
					foreach ($alias_array as $key => $value)
					{
						//membername
						$aliasoutput .= sprintf($format_start, strto_wowutf($key));
			
							//Aliasse
							foreach ($value as $_key => $_value)
							{
								$aliasoutput .= sprintf($format_member, $_key+1, strto_wowutf($_value));
							}
							$aliasoutput .= TAB.TAB.TAB.'},'.CRLF;
					}
					$aliasoutput .= TAB.TAB.'}'."\r\n".CRLF;
				}
				echo $aliasoutput;
			}
		}
		else
		{
		echo"-- Alliasses Data deactive\n\n";
		}
	
	function getMemberNamebyID($id)
	{
		$ret_val = "" ;
		global $db,$table_prefix  ;
		$sql = "SELECT member_name FROM ".$table_prefix."members WHERE member_id=".$id ;
		$member = $db->query_first($sql);
		if (strlen($member)>1){
			$ret_val = $member ;}
		return $ret_val;
	}
	function getMemberClassbyID($id)
	{
		$ret_val = "" ;
		global $db,$table_prefix  ;
		$sql = "SELECT member_class_id FROM ".$table_prefix."members WHERE member_id=".$id ;
		$class = $db->query_first($sql);
		$sql = "SELECT class_name FROM ".$table_prefix."classes WHERE class_id=".$class;
		$class = $db->query_first($sql);
			$ret_val = $class ;
		return $ret_val;
	}
	function getClassbyID($id)
	{
		$ret_val = "" ;
		global $db,$table_prefix  ;
		$sql = "SELECT class_name FROM ".$table_prefix."classes WHERE class_id=".$id ;
		$class = $db->query_first($sql);
		$ret_val = $class ;
		return $ret_val;
	}
	#End Alias
	################################


	####Raidplaner
	################################
	if ( $_GET['raidplaner'] != 1 and $conf_plus['pk_getdkp_rp'] == 1) 
	{
		$plugin_results = "SELECT plugin_installed FROM ".$table_prefix."plugins WHERE plugin_code = 'RaidPlan' or plugin_code = 'raidplan'";
		$result = $db->query_first($plugin_results);
		if ( $result == 0)
		{
			echo "-- The EQDKP Plugin RaidPlaner is not installed --\n\n";
			echo $endString	;
			die;
		}
		
		$eqdkp_classes_group_results = 'SELECT * FROM `' . $table_prefix . 'raidplan_config`';
		if (($settings_result = $db->query($eqdkp_classes_group_results)))
		{
			while($roww = $db->fetch_record($settings_result))
			{
				$eqdkp_getdkp_rpconf[$roww['config_name']] = $roww['config_value'];
			}
		}
		
			
		$timestamp = time() ;
		$riadplaner_raids_results = mysql_query("SELECT * FROM ".$table_prefix."raidplan_raids WHERE raid_date >".$timestamp);
	
		if (!($row = mysql_fetch_array($riadplaner_raids_results)))
		{
			echo "-- no Raids found --\n\n";
			echo $endString	;
			die;
		}
		$riadplaner_raids_results = mysql_query("SELECT * FROM ".$table_prefix."raidplan_raids WHERE raid_date >".$timestamp);
		$raidplaneroutput = 'GetDKPRaidPlaner = {'."\r\n";
		$raidplaneroutput .= TAB.TAB.TAB. '["raid"] = {'."\r\n";
		$format_raid = TAB.TAB.TAB.TAB.'["%s"] = {'.CRLF;
	
		while($row = mysql_fetch_array($riadplaner_raids_results))
		{
			$raidplaner_raids_icons = 'SELECT event_icon FROM '.$table_prefix.'events WHERE event_name = "'.$row['raid_name'].'"';
			$raidplaner_raids_icons = strto_eqdkp_icon($db->query_first($raidplaner_raids_icons));
			$raidplaneroutput .= sprintf($format_raid, $row['raid_date']);
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_name"] = "'.strto_wowutf($row['raid_name']).'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_icon"] = "'.$raidplaner_raids_icons.'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_added_by"] = "'.strto_wowutf($row['raid_added_by']).'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_date"] = '.$row['raid_date'].',--'.date('d.F.Y-G:H',$row['raid_date'])."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_date_invite"] = '.$row['raid_date_invite'].','."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_date_subscription"] = '.$row['raid_date_subscription'].','."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_date_added"] = '.$row['raid_date_added'].','."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_date_finish"] = '.$row['raid_date_finish'].','."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_value"] = "'.$row['raid_value'].'",'."\n";
			$note = strto_wowutf($row['raid_note']);
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_note"] = "'. strip_spezial($note) .'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_raidleader"] = "'.strto_wowutf(getMemberNamebyID($row['raid_leader'])).'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_attendees"] = "'.$row['raid_attendees'].'",'."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_distribution"] = '.$row['raid_distribution'].','."\n";
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_members"] = {'."\r\n";
	
	
			$eqdkp_members_results = mysql_query(" SELECT member_id,member_name FROM ".$table_prefix."members");
			while ($row3 = mysql_fetch_array($eqdkp_members_results))
			{
				$raidplaner_members_results = mysql_query("SELECT * FROM ".$table_prefix."raidplan_raid_attendees WHERE member_id = ".$row3['member_id']." AND raid_id = ".$row['raid_id']);
				$raidplaner_members_additions_result = mysql_query("SELECT skill_1,skill_2,skill_3 FROM ".$table_prefix."member_additions WHERE member_id = ".$row3['member_id']);
				if (!($row2 = mysql_fetch_array($raidplaner_members_results)))
				{
					$raidplaner_user_result = mysql_query("SELECT * FROM ".$table_prefix."member_user WHERE member_id = ".$row3['member_id']);
					if (($row4 = mysql_fetch_array($raidplaner_user_result)))
					{
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["player"] = "'.strto_wowutf(getMemberNamebyID($row3['member_id'])).'",'."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class"] = "'.strto_wowutf(getMemberClassbyID($row3['member_id'])).'",'."\n";
						if (($row5 = mysql_fetch_array($raidplaner_members_additions_result)))
						{
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_1"] = '.$row5['skill_1'].','."\n";
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_2"] = '.$row5['skill_2'].','."\n";
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_3"] = '.$row5['skill_3'].','."\n";
						}
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["subscribed"] = 5 ,'."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["note"] = "",'."\n";
						if ($row2['role'])
							{
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["role"] = "'.$row2['role'].'",'."\n";
							}
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
					}
				}
				else
				{
	
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["player"] = "'.strto_wowutf(getMemberNamebyID($row2['member_id'])).'",'."\n";
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class"] = "'.strto_wowutf(getMemberClassbyID($row2['member_id'])).'",'."\n";
					if (($row5 = mysql_fetch_array($raidplaner_members_additions_result)))
						{
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_1"] = '.$row5['skill_1'].','."\n";
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_2"] = '.$row5['skill_2'].','."\n";
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["skill_3"] = '.$row5['skill_3'].','."\n";
						}
					$subscribed = $row2['attendees_subscribed'] + 1;
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["subscribed"] = '.$subscribed.','."\n";
					$note = strto_wowutf($row2['attendees_note']);
					
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["note"] = "'.strip_spezial($note).'",'."\n";
					if ($row2['role'])
							{
							$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["role"] = "'.$row2['role'].'",'."\n";
							}
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
	
				}
			}
			$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
			if ($row['raid_distribution'] == 0 )
				{
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_classes"] = {'."\r\n";
					$eqdkp_classes_results = mysql_query(" SELECT * FROM ".$table_prefix."raidplan_raid_classes WHERE raid_id = ".$row['raid_id']);
					while ($row3 = mysql_fetch_array($eqdkp_classes_results))
					{
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf($row3['class_name']).'",'."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = '.strto_wowutf($row3['class_count']).','."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
					}
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				}
			elseif ($row['raid_distribution'] == 1)
			{
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_classes"] = {'."\r\n";
					$eqdkp_classes_results = mysql_query(" SELECT * FROM ".$table_prefix."raidplan_raid_classes WHERE raid_id = ".$row['raid_id']);
					while ($row3 = mysql_fetch_array($eqdkp_classes_results))
					{
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf($row3['class_name']).'",'."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = '.strto_wowutf($row3['class_count']).','."\n";
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
					}
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_classes_role"] = {'."\r\n";
					$eqdkp_getdkp_role_healer = explode("|", $eqdkp_getdkp_rpconf['rp_healer']);
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						
						for ($i=0; $i<count($eqdkp_getdkp_role_healer); $i++) 
							{
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf(getClassbyID($eqdkp_getdkp_role_healer[$i])).'",'."\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
							}
						
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.'},'."\r\n";
					$eqdkp_getdkp_role_tank = explode("|", $eqdkp_getdkp_rpconf['rp_tank']);
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_tank); $i++) 
							{
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf(getClassbyID($eqdkp_getdkp_role_tank[$i])).'",'."\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
							}
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.'},'."\r\n";
					$eqdkp_getdkp_role_dd_meele = explode("|", $eqdkp_getdkp_rpconf['rp_dd_meele']);
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_dd_meele); $i++) 
							{
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf(getClassbyID($eqdkp_getdkp_role_dd_meele[$i])).'",'."\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
							}
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.'},'."\r\n";
					$eqdkp_getdkp_role_dd_range = explode("|", $eqdkp_getdkp_rpconf['rp_dd_range']);
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_dd_range); $i++) 
							{
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "'.strto_wowutf(getClassbyID($eqdkp_getdkp_role_dd_range[$i])).'",'."\n";
								$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
							}
						$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.'},'."\r\n";
					$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.'},'."\r\n";
					
			}
			elseif ($row['raid_distribution'] == 2)
			{
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '["raid_classes"] = {'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Druid",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Warlock",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Hunter",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Warrior",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Mage",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Paladin",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Priest",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Shaman",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '{'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_name"] = "Rogue",'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB.TAB. '["class_count"] = 0 ,'."\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
				$raidplaneroutput .= TAB.TAB.TAB.TAB.TAB. '},'."\r\n";
			}
			
			$raidplaneroutput .= TAB.TAB.TAB.TAB. '},'."\r\n";
		}
	
		$raidplaneroutput .= TAB.TAB.TAB. '},'."\r\n";
		$raidplaneroutput .= '}'."\r\n\n";
		echo $raidplaneroutput;
	}
	else
	{
		echo "-- RaidPlaner Data deactive\n\n";
	}
	#End Raidplaner
	################################

echo $endString	;

############# ######################################
############# ######################################

function strto_wowutf($str)
{
    $find[] = 'À';
    $find[] = 'Á';
    $find[] = 'Â';
    $find[] = 'Ã';
    $find[] = 'Ä';
    $find[] = 'Å';
    $find[] = 'Æ';
    $find[] = 'Ç';
    $find[] = 'È';
    $find[] = 'É';
    $find[] = 'Ê';
    $find[] = 'Ë';
    $find[] = 'Ì';
    $find[] = 'Í';
    $find[] = 'Î';
    $find[] = 'Ï';
    $find[] = 'Ð';
    $find[] = 'Ñ';
    $find[] = 'Ò';
    $find[] = 'Ó';
    $find[] = 'Ô';
    $find[] = 'Õ';
    $find[] = 'Ö';
    $find[] = '×';
    $find[] = 'Ø';
    $find[] = 'Ù';
    $find[] = 'Ú';
    $find[] = 'Û';
    $find[] = 'Ü';
    $find[] = 'Ý';
    $find[] = 'Þ';
    $find[] = 'ß';
    $find[] = 'à';
    $find[] = 'á';
    $find[] = 'â';
    $find[] = 'ã';
    $find[] = 'ä';
    $find[] = 'å';
    $find[] = 'æ';
    $find[] = 'ç';
    $find[] = 'è';
    $find[] = 'é';
    $find[] = 'ê';
    $find[] = 'ë';
    $find[] = 'ì';
    $find[] = 'í';
    $find[] = 'î';
    $find[] = 'ï';
    $find[] = 'ð';
    $find[] = 'ñ';
    $find[] = 'ò';
    $find[] = 'ó';
    $find[] = 'ô';
    $find[] = 'õ';
    $find[] = 'ö';
    $find[] = '÷';
    $find[] = 'ø';
    $find[] = 'ù';
    $find[] = 'ú';
    $find[] = 'û';
    $find[] = 'ü';
    $find[] = 'ý';
    $find[] = 'þ';
    $find[] = 'ÿ';
	$find[] = '"';
	
    $replace[]            = '\195\128';
    $replace[]            = '\195\129';
    $replace[]            = '\195\130';
    $replace[]            = '\195\131';
    $replace[]            = '\195\132';
    $replace[]            = '\195\133';
    $replace[]            = '\195\134';
    $replace[]            = '\195\135';
    $replace[]            = '\195\136';
    $replace[]            = '\195\137';
    $replace[]            = '\195\138';
    $replace[]            = '\195\139';
    $replace[]            = '\195\140';
    $replace[]            = '\195\141';
    $replace[]            = '\195\142';
    $replace[]            = '\195\143';
    $replace[]            = '\195\144';
    $replace[]            = '\195\145';
    $replace[]            = '\195\146';
    $replace[]            = '\195\147';
    $replace[]            = '\195\148';
    $replace[]            = '\195\149';
    $replace[]            = '\195\150';
    $replace[]            = '\195\151';
    $replace[]            = '\195\152';
    $replace[]            = '\195\153';
    $replace[]            = '\195\154';
    $replace[]            = '\195\155';
    $replace[]            = '\195\156';
    $replace[]            = '\195\157';
    $replace[]            = '\195\158';
    $replace[]            = '\195\159';
    $replace[]            = '\195\160';
    $replace[]            = '\195\161';
    $replace[]            = '\195\162';
    $replace[]            = '\195\163';
    $replace[]            = '\195\164';
    $replace[]            = '\195\165';
    $replace[]            = '\195\166';
    $replace[]            = '\195\167';
    $replace[]            = '\195\168';
    $replace[]            = '\195\169';
    $replace[]            = '\195\170';
    $replace[]            = '\195\171';
    $replace[]            = '\195\172';
    $replace[]            = '\195\173';
    $replace[]            = '\195\174';
    $replace[]            = '\195\175';
    $replace[]            = '\195\176';
    $replace[]            = '\195\177';
    $replace[]            = '\195\178';
    $replace[]            = '\195\179';
    $replace[]            = '\195\180';
    $replace[]            = '\195\181';
    $replace[]            = '\195\182';
    $replace[]            = '\195\183';
    $replace[]            = '\195\184';
    $replace[]            = '\195\185';
    $replace[]            = '\195\186';
    $replace[]            = '\195\187';
    $replace[]            = '\195\188';
    $replace[]            = '\195\189';
    $replace[]            = '\195\190';
    $replace[]            = '\195\191';
	$replace[]			  = '';
	$str_encoded = str_replace($find, $replace , $str);

	return $str_encoded;
}

function strto_wowutfItem($str)
{
	$str_encoded = utf8_encode($str);
	return $str_encoded;
}

function _delimeter($val)
{
	return number_format ( $val, 2, '.', '' );
}

function strto_eqdkp_icon($str)
{
	$find[]		= '0_Spell_Nature_GuardianWard.png';
	$find[]		= '000_unknown.gif';
	$find[]		= '1_bwl.gif';
	$find[]		= '001_mc.gif';
	$find[]		= '1_ony.gif';
	$find[]		= '3_aq40.gif';
	$find[]		= 'Icon-AQRuins.gif';
	$find[]		= 'Icon-AQTemple.gif';
	$find[]		= 'Icon-BlackTemple.gif';
	$find[]		= 'Icon-BlackwingLair.gif';
	$find[]		= 'Icon-CavernsOfTime.gif';
	$find[]		= 'Icon-CoilFang.gif';
	$find[]		= 'Icon-Gruul-Mag.gif';
	$find[]		= 'Icon-GruulsLair.gif';
	$find[]		= 'Icon-Karazhan.gif';
	$find[]		= 'Icon-MagtheridonsLair.gif';
	$find[]		= 'Icon-MoltenCore.gif';
	$find[]		= 'Icon-Naxxramas.gif';
	$find[]		= 'Icon-Onyxia.gif';
	$find[]		= 'Icon-TempestKeep.gif';
	$find[]		= 'Icon-ZulGurub.gif';
	$find[]		= 'INV_Misc_TabardPVP_03.png';
	$find[]		= 'INV_Misc_TabardPVP_04.png';
	$find[]		= 'Mail_GMIcon.png';
	$find[]		= 'Icon-ZulAman.gif';

	$replace[] 		= 'Interface\\\\Icons\\\\Spell_Nature_GuardianWard';
	$replace[]		= 'Interface\\\\IventroyItems\\\\WoWUnknownItem01';
	$replace[] 		= 'Interface\\\\Icons\\\\INV_Misc_Head_Dragon_Black';
	$replace[] 		= 'Interface\\\\Icons\\\\INV_Hammer_Unique_Sulfuras';
	$replace[] 		= 'Interface\\\\Icons\\\\INV_Misc_Head_Dragon_01';
	$replace[] 		= 'Interface\\\\Icons\\\\Spell_Shadow_DetectInvisibility';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-AQRuins';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-AQTemple';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-BlackTemple';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-BlackwingLair';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-CavernsOfTime';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-CoilFang';
	$replace[] 		= 'Interface\\\\AddOns\\\\GetDKP\\\\Images\\\\Icon-Gruul-Mag.tga';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-GruulsLair';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-Karazhan';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-HellfireCitadelRaid';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-MoltenCore';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-Naxxramas';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-Raid';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-TempestKeep';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-ZulGurub';
	$replace[] 		= 'Interface\\\\Icons\\\\INV_Misc_TabardPVP_03';
	$replace[] 		= 'Interface\\\\Icons\\\\INV_Misc_TabardPVP_04';
	$replace[] 		= 'Interface\\\\Icons\\\\Mail_GMIcon.png';
	$replace[] 		= 'Interface\\\\LFGFrame\\\\LFGIcon-ZulAman';

	$str_encoded = str_replace($find, $replace , $str);

	return $str_encoded;
}

function getdkp_debug($str)
{
	echo "\n"."--debug : ".$str."\n";
}
function strip_spezial($str)
{
	for ($i = 1; $i <= 31; $i++)
		{
			$find[] = chr($i);
			if ($i == 13)
			{
				$replace[] = '/r/n';
			}
			else
			{
				$replace[] = '';
			}
		}
		
	$str_encoded = str_replace($find, $replace , $str);
	return $str_encoded;
}
?>





