<?php
/******************************
 * EQDKP PLUSkernel
 * (c) 2007 by Corgan [Stefan Knaak]
 * http://www.eqdkp-plus.com
 * ---------------------------
 * dkpplus.class.php
 * Start: 07.08.2007
 * $Id:$
   ******************************/

class dkpplus
{

	// return a Array with all MultiDKP Accounts of the given Member
	// Corgan 29.10.06
	// used for quickdkp
	function multiDkpMemberArray($membername)
	{
		global $db, $eqdkp, $user, $conf_plus;
		// EQDKP Plus MultiDKP Start
		// get all events

		// earned
		$pv_sql = "SELECT " . RAIDS_TABLE .".raid_name, SUM(raid_value)
				   FROM
				   ". RAID_ATTENDEES_TABLE ." LEFT JOIN " . RAIDS_TABLE ." ON ". RAID_ATTENDEES_TABLE .".raid_id=" . RAIDS_TABLE .".raid_id
				   WHERE ". RAID_ATTENDEES_TABLE .".member_name = '".$membername."' GROUP by " . RAIDS_TABLE .".raid_name";

    $pv_result = $db->query($pv_sql);
    while( $pv_row = $db->fetch_record($pv_result) )
    {
    	$event_data[$pv_row[0]]['earned'] = $pv_row[1] ;
    }
		# end earned
		###############################################################

		//
		// spend
		$ps_sql = "SELECT ". RAIDS_TABLE .".raid_name, SUM(". ITEMS_TABLE .".item_value)
		   FROM
		   ". ITEMS_TABLE ."  LEFT JOIN ". RAIDS_TABLE ." ON ". ITEMS_TABLE .".raid_id=". RAIDS_TABLE .".raid_id
		   WHERE
		   ". ITEMS_TABLE .".item_buyer = '".$membername."' GROUP by ". RAIDS_TABLE .".raid_name;";

		$ps_result = $db->query($ps_sql);
		while( $ps_row = $db->fetch_record($ps_result) )
    {
			$event_data[$ps_row[0]]['spend'] = $ps_row[1] ;
    }
		# end spend
		###############################################################

		//
		// Adjust
    $pa_sql = "SELECT adjustment_reason, adjustment_value, raid_name
    			FROM ". ADJUSTMENTS_TABLE . "
    			WHERE member_name = '".$membername."';";

		#echo $pa_sql.'<br>' ;
		$pa_result = $db->query($pa_sql);
		while( $pa_row = $db->fetch_record($pa_result) )
    {
    	$event_data[$pa_row['raid_name']]['adjust'] += $pa_row['adjustment_value'] ;
		}

		# end Adjust
		###############################################################

		//
		// get MultiDKP Data from eqdkp_multidkp
		$sql = 'SELECT multidkp_id, multidkp_name
						FROM ' . MULTIDKP_TABLE ;

		if ( !($multi_results = $db->query($sql)) )
		{
			message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
		}

		//
		// Konten
		$multicount = 0 ;
		while ( $a_multi = $db->fetch_record($multi_results) )
		{
			$multicount++;

			// namen speichern f�rs template
			$multi_name[$multicount]['name'] = $a_multi['multidkp_name'];
			$multi_name[$multicount]['id'] = $a_multi['multidkp_id'];

			$sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname
										 FROM ' . MULTIDKP2EVENTS_TABLE
										 .' WHERE multidkp2event_multi_id ='.$a_multi['multidkp_id'] ;

  		if ( !($multi2event_results = $db->query($sql_events)) )
			{
				message_die('Could not obtain MultiDKP -> Event information', '', __FILE__, __LINE__, $sql_events);
			}

			//Konten2Events
			//Konten verkn�pft mit Events	den Multikonten des Member zuweisen
			//

			// SmartTooltip
			if ($conf_plus['pk_multiSmarttip'] == 1)
			{
				$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=

				'<table cellpadding=2 cellspacing=10>
					<tr>
						<td>'.$user->lang['event'].'</td>
						<td>'.$user->lang['earned'].'</td>
						<td>'.$user->lang['spent'].'</td>
						<td>'.$user->lang['adjustment'].'</td>
						<td>'.$user->lang['current'].'</td>
					</tr>';
			}

		  while ( $a_multi = $db->fetch_record($multi2event_results) )
			{ // gehe alle Events durch, die einem Konto zugewiesen wurden

				 $current = 0 ;
				 // current wert berechnen
				 $current = $event_data[$a_multi['multidkp2event_eventname']]['earned'] -
				 $event_data[$a_multi['multidkp2event_eventname']]['spend'] +
				 $event_data[$a_multi['multidkp2event_eventname']]['adjust'] ;

				 //Generate DKP Tooltip
				if ($conf_plus['pk_multiTooltip'] == 1)  // Tooltip on/off
   			{
					if ($conf_plus['pk_multiSmarttip'] == 1) // SmartTooltip
   				{
					$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=

					'<tr>
							<td>'.$a_multi['multidkp2event_eventname']."</td>
							<td><span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['earned'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['earned'])."</span></td>
							<td><span class=negative> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['spend'])."</span></td>
							<td> <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."</span></td>
							<td><span class=".color_item($current)."> "
							.runden($current)."</span></td>
						</tr> ";

					}
					else if ($conf_plus['pk_multiSmarttip'] == 0)
					{
						$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=
							'<span class=itemdesc>'.$user->lang['event'].': '.$a_multi['multidkp2event_eventname']."</span><br>" ;

						$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['earned'].": <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['earned'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['earned'])."</span><br>";

						$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['spent'].":   <span class=negative> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['spend'])."</span><br>";

						$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['adjustment'].":  <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."</span><br>";

						$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['current'].": <span class=".color_item($current)."> "
							.runden($current)."</span><br><br>";
					}
				}

				//Array for the template
				//multicount = Account (konto)
				//Die Werte werden einfach aufaddiert

				$members_rows_multidkp[$membername][$multicount]['name'] = $multi_name[$multicount]['name'] ;
				$members_rows_multidkp[$membername][$multicount]['earned'] += runden($event_data[$a_multi['multidkp2event_eventname']]['earned']);
				$members_rows_multidkp[$membername][$multicount]['spend'] += runden($event_data[$a_multi['multidkp2event_eventname']]['spend']);
				$members_rows_multidkp[$membername][$multicount]['adjust'] += runden($event_data[$a_multi['multidkp2event_eventname']]['adjust']);
				$members_rows_multidkp[$membername][$multicount]['current'] += runden($current, 2);
			}
			if ($conf_plus['pk_multiSmarttip'] == 1 and $conf_plus['pk_multiTooltip'] == 1)
			{
				$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .= '</table>';
			}
		 }; // end while	konten

		 return $members_rows_multidkp ;
	} // end function

	##########################################
function multiDkpAllMemberArray($sortID)
	{
		global $db, $eqdkp, $user, $conf_plus;

	   $sql = 'SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current,
	   member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
     	c.class_name AS member_class,
     	c.class_armor_type AS armor_type,
	    c.class_min_level AS min_level,
	    c.class_max_level AS max_level
     	FROM ' . MEMBERS_TABLE . ' m, ' . MEMBER_RANKS_TABLE . ' r, ' . CLASS_TABLE . " c
	   	WHERE c.class_id = m.member_class_id ";

	   $sql .= ' AND (m.member_rank_id = r.rank_id)';


	   // Are we hiding inactive members?
		if ( $eqdkp->config['hide_inactive'] == '1' )
		{
			$sql .= " AND m.member_status <> '0'";
		}

		$sql .= ' group by m.member_name';

     if ( !($members_result = $db->query($sql)) )
     {
     	message_die('Could not obtain member information in multiDkpAllMemberArray() html.class.php', '', __FILE__, __LINE__, $sql);
     }

		unset($members_rows_multidkp);

		while ( $row = $db->fetch_record($members_result) )
    {
			// EQDKP Plus MultiDKP Start
			// get all events
			unset($event_data);
			$members_rows_multidkp[$row['member_name']]['class']=$row['member_class'];

			// earned
			$pv_sql = "SELECT " . RAIDS_TABLE .".raid_name, SUM(raid_value)
					   FROM
					   ". RAID_ATTENDEES_TABLE ." LEFT JOIN " . RAIDS_TABLE ." ON ". RAID_ATTENDEES_TABLE .".raid_id=" . RAIDS_TABLE .".raid_id
					   WHERE ". RAID_ATTENDEES_TABLE .".member_name = '".$row['member_name']."' GROUP by " . RAIDS_TABLE .".raid_name";

	    $pv_result = $db->query($pv_sql);
	    while( $pv_row = $db->fetch_record($pv_result) )
	    {
	    	$event_data[$pv_row[0]]['earned'] = $pv_row[1] ;
	    }
			# end earned
			###############################################################

			//
			// spend
			$ps_sql = "SELECT ". RAIDS_TABLE .".raid_name, SUM(". ITEMS_TABLE .".item_value)
			   FROM
			   ". ITEMS_TABLE ."  LEFT JOIN ". RAIDS_TABLE ." ON ". ITEMS_TABLE .".raid_id=". RAIDS_TABLE .".raid_id
			   WHERE
			   ". ITEMS_TABLE .".item_buyer = '".$row['member_name']."' GROUP by ". RAIDS_TABLE .".raid_name;";

			$ps_result = $db->query($ps_sql);
			while( $ps_row = $db->fetch_record($ps_result) )
	    {
				$event_data[$ps_row[0]]['spend'] = $ps_row[1] ;
	    }
			# end spend
			###############################################################

			//
			// Adjust
	    $pa_sql = "SELECT adjustment_reason, adjustment_value, raid_name
	    			FROM ". ADJUSTMENTS_TABLE . "
	    			WHERE member_name = '".$row['member_name']."';";

			#echo $pa_sql.'<br>' ;
			$pa_result = $db->query($pa_sql);
			while( $pa_row = $db->fetch_record($pa_result) )
	    {
	    	$event_data[$pa_row['raid_name']]['adjust'] += $pa_row['adjustment_value'] ;
			}

			# end Adjust
			###############################################################

			//
			// get MultiDKP Data from eqdkp_multidkp
			$sql = 'SELECT multidkp_id, multidkp_name
							FROM ' . MULTIDKP_TABLE ;

			if ( !($multi_results = $db->query($sql)) )
			{
				message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
			}

			$sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname
										 FROM ' . MULTIDKP2EVENTS_TABLE
									 .' WHERE multidkp2event_multi_id ='.$sortID ;
#											 .' WHERE multidkp2event_multi_id ='.$a_multi['multidkp_id'] ;

  		if ( !($multi2event_results = $db->query($sql_events)) )
			{
				message_die('Could not obtain MultiDKP -> Event information', '', __FILE__, __LINE__, $sql_events);
			}

			//Konten2Events
			//Konten verkn�pft mit Events	den Multikonten des Member zuweisen

			// SmartTooltip
			if ($conf_plus['pk_multiSmarttip'] == 1)
			{
				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=

				'<table cellpadding=2 cellspacing=10>
					<tr>
						<td>'.$user->lang['event'].'</td>
						<td>'.$user->lang['earned'].'</td>
						<td>'.$user->lang['spent'].'</td>
						<td>'.$user->lang['adjustment'].'</td>
						<td>'.$user->lang['current'].'</td>
					</tr>';
			}

		  while ( $a_multi = $db->fetch_record($multi2event_results) )
			{ // gehe alle Events durch, die einem Konto zugewiesen wurden
				 $current = 0 ;
				 // current wert berechnen
				 $current = $event_data[$a_multi['multidkp2event_eventname']]['earned'] -
				 $event_data[$a_multi['multidkp2event_eventname']]['spend'] +
				 $event_data[$a_multi['multidkp2event_eventname']]['adjust'] ;

					//Generate DKP Tooltip
					if($conf_plus['pk_multiTooltip'] == 1)
	   			{
	   				if ($conf_plus['pk_multiSmarttip'] == 1) // SmartTooltip
	   				{
						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=

						"<tr>
								<td>".$a_multi['multidkp2event_eventname']."</td>
								<td><span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['earned'])."> "
										.runden($event_data[$a_multi['multidkp2event_eventname']]['earned'])."</span></td>
								<td><span class=negative> "
										.runden($event_data[$a_multi['multidkp2event_eventname']]['spend'])."</span></td>
								<td><span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."> "
										.runden($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."</span></td>
								<td><span class=".color_item($current)."> "
										.runden($current)."</span></td>
							</tr> ";

						}
						else if ($conf_plus['pk_multiSmarttip'] == 0)
						{
						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
							'<span class=itemdesc>'.$user->lang['event'].': '.$a_multi['multidkp2event_eventname']."</span><br>" ;

						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['earned'].": <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['earned'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['earned'])."</span><br>";

						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['spent'].":   <span class=negative> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['spend'])."</span><br>";

						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['adjustment'].":  <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."> "
							.runden($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."</span><br>";

						$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
							" &nbsp;".$user->lang['current'].": <span class=".color_item($current)."> "
							.runden($current)."</span><br><br>";
						} // end normal tooltip
					} // end tooltip


				//Array for the template
				//multicount = Account (konto)
				//Die Werte werden einfach aufaddiert
				$members_rows_multidkp[$row['member_name']]['earned'] += runden($event_data[$a_multi['multidkp2event_eventname']]['earned']);
				$members_rows_multidkp[$row['member_name']]['spend'] += runden($event_data[$a_multi['multidkp2event_eventname']]['spend']);
				$members_rows_multidkp[$row['member_name']]['adjust'] += runden($event_data[$a_multi['multidkp2event_eventname']]['adjust']);
				$members_rows_multidkp[$row['member_name']]['current'] += runden($current, 2);
			}
			$members_rows_multidkp[$row['member_name']]['name']=$row['member_name'];

			#$sort_current[strtolower($tempmultiname)][$member_count] = $members_rows_multidkp[$member_count][$multicount]['current'];
			$sort_current[$row['member_name']] = $members_rows_multidkp[$row['member_name']]['current'];

			// Generate DKP Tooltip
			// ###############################################################
			if ($conf_plus['pk_multiTooltip'] == 1)  // Tooltip on/off
			{
				if ($conf_plus['pk_multiSmarttip'] == 1) // SmartTooltip
				{
					$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=

					"<tr>
					<td><span class=itemdesc>".$user->lang['Multi_total_cost'].": </span></td>
					<td><span class="
													.color_item($members_rows_multidkp[$row['member_name']]['earned']).">"
													.$members_rows_multidkp[$row['member_name']]['earned']."</span></td>
					<td> <span class=negative>"
													.$members_rows_multidkp[$row['member_name']]['spend']."</span></td>
					<td><span class="
													.color_item($members_rows_multidkp[$row['member_name']][$multicount]['adjust']).">"
													.$members_rows_multidkp[$row['member_name']]['adjust']."</span></td>
					<td><span class="
													.color_item($members_rows_multidkp[$row['member_name']]['current']).">"
													.$members_rows_multidkp[$row['member_name']]['current']."</span></td>
					</tr> ";
				}
				else if ($conf_plus['pk_multiSmarttip'] == 0 ) // normal Tooltip)
				{
				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .=
				"<span class=itemdesc><b>".$user->lang['Multi_total_cost'].":</b> </span><br>";

				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .= " &nbsp;".$user->lang['earned'].": <span class="
					.color_item($members_rows_multidkp[$row['member_name']]['earned']).">"
					.$members_rows_multidkp[$row['member_name']]['earned']."</span><br>";

				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .= " &nbsp;".$user->lang['spent'].": <span class=negative>"
					.$members_rows_multidkp[$row['member_name']]['spend']."</span><br>";

				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .= " &nbsp;".$user->lang['adjustment'].": <span class="
					.color_item($members_rows_multidkp[$row['member_name']][$multicount]['adjust']).">"
					.$members_rows_multidkp[$row['member_name']]['adjust']."</span><br>";

				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .= " &nbsp;".$user->lang['current'].": <span class="
					.color_item($members_rows_multidkp[$row['member_name']]['current']).">"
					.$members_rows_multidkp[$row['member_name']]['current']."</span><br>";
				}
		}
		} #end while row

  	$plus_sortorder = SORT_DESC;
		$members_rows_fsort = array();
	  $sort_value = 'current';
	  if(isset($sort_current))
	  {
			foreach($sort_current as $key => $row)
			{
				$members_rows_fsort[$key] = intval($row);
			}
		}
		if(isset($members_rows_multidkp) and isset($members_rows_fsort))
		{
			array_multisort($members_rows_fsort, $plus_sortorder, SORT_NUMERIC, $members_rows_multidkp);
		}

		return $members_rows_multidkp ;
	} // end function
	##########################################


}// end of class
?>