<?php
/******************************
 * GetDKP Plus
 *
 * © Corgan @ Server Antonidas PvE German -> www.seniorenraid.de
 * ------------------
 * getdkp.php
 *
 * Version 2.6
 * 06/25/06
 * 06/29/07 - Specialchar support
 * 06/18/11 - multidkp support
 * 07/03/01 - new item iterration
 * Support English / German -> http://www.seniorenraid.de/forum/forumdisplay.php?f=13
 * 07/05/13 support for dkp.exe 5.0
 ******************************/


############# you dont have to change anything below !!!!  ######################################
#################################################################################################

$version = "2.6";
$total_players = 0;
$total_items = 0;
$total_points = 0;
$eqdkp_version = "1.3.x";
$date_created = date("d.m.y G:i:s");
$startString = "--[START]\n";
$endString = "\n--[END]";

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

echo $startString ;
	if($conf_plus['pk_multidkp'] == 1)
	{
    $sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
            FROM ' . MULTIDKP_TABLE . '
            WHERE multidkp_name IS NOT NULL'
            ;

    $total_multi = $db->query_first('SELECT count(*) FROM ' . MULTIDKP_TABLE . ' WHERE multidkp_name IS NOT NULL');

		if ( !($multi_result = $db->query($sql)) )
		{
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
				while($row = mysql_fetch_array($member_results, MYSQL_ASSOC)) {
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
					while($row = mysql_fetch_array($member_results, MYSQL_ASSOC)) {
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

				echo "DKPInfo = {\n";
								echo "     [\"date\"] = \"$date_created\",\n";
								echo "     [\"process_dkp_ver\"] = \"$version\",\n";
								echo "     [\"total_players\"] = $total_players,\n";
								echo "     [\"total_items\"] = $total_items,\n";
								echo "     [\"total_points\"] = $total_points,\n";
	if (isset($table_prefix_NS)) {
								echo "     [\"set_items\"] = $setitems,\n";
								echo "     [\"nonset_items\"] = $Nonsetitems,\n";
								echo "     [\"total_points_set\"] = $total_pointsset,\n";
								echo "     [\"total_points_ns\"] = $total_pointsns\n";

						      }
				echo "}";


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

	define(TAB, "\t");
	define(CRLF, "\r\n");

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

		$html = new htmlPlus(); // plus html class
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

				$outputdkp .= sprintf($format_start, strto_wowutf($row['member_name']));
				$outputdkp .= sprintf($format_dkp,$row['current_dkp']);
				$member_multidkp = $html-> multiDkpMemberArray($row['member_name']) ; // create the multiDKP Table

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
			// echo $item_row['item_buyer'] .":". $item_row['item_name'] .":". $item_row['item_value'] ."\n";

			#$buyer = strtolower($item_row['item_buyer']);
			$buyer = $item_row['item_buyer'];
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

############# ######################################

	$item_results = mysql_query("SELECT distinct(game_itemid),item_name  FROM ".$table_prefix."items" ) or die(mysql_error());

	$itemsoutput = "getdkp_itemids = nil \n";
	IF(isset($item_results ))
	{
		$itemsoutput = "getdkp_itemids = {\n";
		while($item_row = mysql_fetch_array($item_results, MYSQL_ASSOC))
		{
			#echo strlen($item_row[game_itemid]) ;
			if(strlen($item_row[game_itemid]) > 0)
			{
				$itemsoutput .= "	\t   [\"".strto_wowutf($item_row[item_name])."\"]= ".$item_row[game_itemid]." , \n";
			}
		}
		$itemsoutput .= "}\n\n";
	echo $itemsoutput;
	}

echo $endString	;

	############# ######################################
	############# ######################################

function strto_wowutf($str)
{
	$find[] = 'à';
	$find[] = 'á';
	$find[] = 'â';
	$find[] = 'ã';
	$find[] = 'ä';
	$find[] = 'æ';
	$find[] = 'ç';
	$find[] = 'Ä';
	$find[] = 'Ö';
	$find[] = 'Ü';
	$find[] = 'ß';
	$find[] = 'è';
	$find[] = 'é';
	$find[] = 'ê';
	$find[] = 'ë';
	$find[] = 'ì';
	$find[] = 'í';
	$find[] = 'î';
	$find[] = 'ï';
	$find[] = 'ñ';
	$find[] = 'ò';
	$find[] = 'ó';
	$find[] = 'ô';
	$find[] = 'õ';
	$find[] = 'ö';
	$find[] = 'ø';
	$find[] = 'ù';
	$find[] = 'ú';
	$find[] = 'û';
	$find[] = 'ü';
	$find[] = 'Æ';

	$replace[]			= '\195\160';
	$replace[]			= '\195\161';
	$replace[]			= '\195\162';
	$replace[]			= '\195\163';
	$replace[]			= '\195\164';
	$replace[]			= '\195\166';
	$replace[]			= '\195\167';
	$replace[]			= '\195\132';
	$replace[]			= '\195\150';
	$replace[]			= '\195\156';
	$replace[]			= '\195\159';
	$replace[]			= '\195\168';
	$replace[]			= '\195\169';
	$replace[]			= '\195\170';
	$replace[]			= '\195\171';
	$replace[]			= '\195\172';
	$replace[]			= '\195\173';
	$replace[]			= '\195\174';
	$replace[]			= '\195\175';
	$replace[]			= '\195\177';
	$replace[]			= '\195\178';
	$replace[]			= '\195\179';
	$replace[]			= '\195\180';
	$replace[]			= '\195\181';
	$replace[]			= '\195\182';
	$replace[]			= '\195\184';
	$replace[]			= '\195\185';
	$replace[]			= '\195\186';
	$replace[]			= '\195\187';
	$replace[]			= '\195\188';
	$replace[] 			= '\195\134';


	$str_encoded = str_replace($find, $replace , $str);

	return $str_encoded;
}

function strto_wowutfItem($str)
{
	$str_encoded = utf8_encode($str);
	return $str_encoded;
}

// I find this name a little misleading because the result won't be valid UTF8 data
function cp1252_to_utf8($str) {
// map taken from http://de3.php.net/manual/de/function.utf8-encode.php#45226
$cp1252_map = array(
   "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
   "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
   "\xc2\x83" => "\xc6\x92",    /* LATIN SMALL LETTER F WITH HOOK */
   "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
   "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
   "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
   "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
   "\xc2\x88" => "\xcb\x86",    /* MODIFIER LETTER CIRCUMFLEX ACCENT */
   "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
   "\xc2\x8a" => "\xc5\xa0",    /* LATIN CAPITAL LETTER S WITH CARON */
   "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
   "\xc2\x8c" => "\xc5\x92",    /* LATIN CAPITAL LIGATURE OE */
   "\xc2\x8e" => "\xc5\xbd",    /* LATIN CAPITAL LETTER Z WITH CARON */
   "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
   "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
   "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
   "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
   "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
   "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
   "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */

   "\xc2\x98" => "\xcb\x9c",    /* SMALL TILDE */
   "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
   "\xc2\x9a" => "\xc5\xa1",    /* LATIN SMALL LETTER S WITH CARON */
   "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
   "\xc2\x9c" => "\xc5\x93",    /* LATIN SMALL LIGATURE OE */
   "\xc2\x9e" => "\xc5\xbe",    /* LATIN SMALL LETTER Z WITH CARON */
   "\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
);

   return  strtr(utf8_encode($str), $cp1252_map);
}

function cp1252_utf8_to_iso($str) { // the other way around...
  #global $cp1252_map;
  return  utf8_decode( strtr($str, array_flip($cp1252_map)) );
}

?>





