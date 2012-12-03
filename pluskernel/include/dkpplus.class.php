<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Knaak
 * http://www.eqdkp-plus.com
 * ---------------------------
 * dkpplus.class.php
 * Start: 07.08.2007
 * $Id$
   ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

/**
 * Eqdkp Plus DKP calculation class written by Corgan ^ Stefan Knaak
 *
 */
class dkpplus
{

	/**
	 * return an Array with all MultiDKP Accounts of the given Member
	 * used for quickdkp
	 *
	 * @param String $membername
	 * @return Array
	 */
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

			// namen speichern fï¿½rs template
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
			//Konten verknï¿½pft mit Events	den Multikonten des Member zuweisen
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
				$members_rows_multidkp[$membername][$multicount]['current'] += runden($current);
			}
			if ($conf_plus['pk_multiSmarttip'] == 1 and $conf_plus['pk_multiTooltip'] == 1)
			{
				$members_rows_multidkp[$membername][$multicount]['dkp_tooltip'] .= '</table>';
			}
		 }; // end while	konten

		 return $members_rows_multidkp ;
	} // end function

	##########################################

	/**
	 * Create MultiDKP Array
	 * used by Leaderboard
	 *
	 * @param  MultiDKPKonto-String $sortID
	 * @return Array
	 */
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

		$sql .= ' group by m.member_name ';
		$sql .= ' order by member_class ';

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
			//Konten verknï¿½pft mit Events	den Multikonten des Member zuweisen

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
				$members_rows_multidkp[$row['member_name']]['current'] += runden($current);
			}
			$members_rows_multidkp[$row['member_name']]['name']=$row['member_name'];
			$members_rows_multidkp[$row['member_name']]['rank_hide']=$row['rank_hide'];

			#create sepperate array to sort the shit
			#$sort_current[$row['member_name']] = $members_rows_multidkp[$row['member_name']]['current'];

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
			if ($conf_plus['pk_multiSmarttip'] == 1 and $conf_plus['pk_multiTooltip'] == 1)
			{
				$members_rows_multidkp[$row['member_name']]['dkp_tooltip'] .= '</table>';
			}

		} #end while row



		$dkp_sort = array();

		#Temp Array |Member | DKP
	  	if(isset($members_rows_multidkp))
	  	{
			foreach($members_rows_multidkp as $key => $row)
			{
				$dkp_sort[$key] = intval($row['current']);
			}
		}

		#Sort by DKP
		if(isset($members_rows_multidkp) and isset($dkp_sort))
		{
			array_multisort($dkp_sort, SORT_DESC, SORT_NUMERIC, $members_rows_multidkp);
		}

		return $members_rows_multidkp ;
	} // end function
	##########################################



	/**
	 * create a Table with the DKP of all members assigned to the active user
	 * the function defined the TPL Var {POINTSV}
	 * and returned the Array
	 *
	 * @return Array
	 */
	function quickdkp()
	{
		global $user, $db, $eqdkp, $dkpplus, $html,$conf_plus,$tpl;

		if ( $user->data['user_id'] != ANONYMOUS )
		{
			$quickdkp  ='<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>';
			//get member ID from UserID
			$sql3 = 'SELECT member_id
					FROM ' . MEMBER_USER_TABLE . '
					WHERE user_id = '. $user->data['user_id'] .'';

		 	$result3 = $db->query($sql3);
			while ( $row3 = $db->fetch_record($result3) )
			{
				$member_id = $row3[member_id];
				//get member info

				$sql	 = 'Select member_name, member_class_id
						   From '. MEMBERS_TABLE. ' where member_id = '.$member_id ;

				$result = $db->query($sql);
				$member_name = '' ;
				$member_classID = '';
				while ( $row = $db->fetch_record($result) )
				{
					$member_name = $row[member_name];
					$member_classID = $row[member_class_id];

					if($member_name != '')
					{
						$quickdkp  .= ' <tr class="'.$eqdkp->switch_row_class().'"><td colspan=2>'.
													get_classNameImgViewmembers($member_name). '</td></tr>';

						if($conf_plus['pk_multidkp'] == 1)
						{

							$member_multidkp = $this-> multiDkpMemberArray($row[member_name]) ; // create the multiDKP Table
							if(!empty($member_multidkp[$row[member_name]]))
							{
								 foreach ($member_multidkp[$row[member_name]] as $key)
								 {
									$quickdkp  .= '<tr class="'.$eqdkp->switch_row_class().'"><td>'.$key['name']." ".$user->lang['Points_DKP'].'</td>
																<td> <span class='.color_item($key['current']).'>
																  <b>'.$html->ToolTip($key['dkp_tooltip'],$key['current']). '</b> </span>
																</td></tr>';
								 } // end foreach
							}

						}
						else
						{
							//get DKP
							$sql2 = "SELECT member_earned + member_adjustment - member_spent as dkp
									FROM ".MEMBERS_TABLE." WHERE member_name = '".$member_name."'";
							$result2 = $db->query($sql2);
							$member_dkp = 0 ;
							while ( $row2 = $db->fetch_record($result2) )
							{
									$member_dkp = runden($row2[dkp]);

							}
							$db->free_result($result2);

								$quickdkp  .= '<tr class="'.$eqdkp->switch_row_class().'"><td>'.$user->lang['Points_DKP'].'</td><td><b>'.$member_dkp. '</b></td></tr>';

						} //end else config plus
					} // end if member
				} // end user2member while
				$db->free_result($result);
			} // end member while

			$db->free_result($result3);

			if(!$member_id > 0)
			{
				$quickdkp  ='<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>
							 <tr><td class="row1">'.$user->lang['Points_CHAR'].'</td></tr>';

			}


			$tpl->assign_var('POINTSV', $quickdkp);
			return $quickdkp;
		}
	} # end quickdkp

	/**
	 * DKP Info
	 * return the Raid, Items and Membercount
	 * Assign the TPL Var {DKP_INFO}
	 *
	 * @return Array
	 */
	function dkpinfo()
	{
		global $eqdkp , $user , $tpl, $db;

		$a_dkpinfo = array();
		// Get total raids
    	$sql ="SELECT count(*) as alle FROM ".RAIDS_TABLE.";";
		$a_dkpinfo['raids'] = $db->query_first($sql);

		// Get total players
		$sql = "SELECT count(member_id) FROM ".MEMBERS_TABLE ;
		$a_dkpinfo['member'] = $db->query_first($sql);

		// Get total items
		$sql = "SELECT COUNT(item_id) FROM ".ITEMS_TABLE ;
		$a_dkpinfo['items'] = $db->query_first($sql);

		$DKPInfo = '<tr><th colspan=2 class="smalltitle" align="center">DKP Infos</th></tr>
					<tr><td class="row1">'.$user->lang['bosscount_raids'].'</td><td class="row1">'. $a_dkpinfo['raids']. '</td></tr>
					<tr><td class="row2">'.$user->lang['bosscount_player'].'</td><td class="row2">'. $a_dkpinfo['member']. '</td></tr>
					<tr><td class="row1">'.$user->lang['bosscount_items'].'</td><td class="row1">'. $a_dkpinfo['items']. '</td></tr>
					';

		$tpl->assign_var('DKP_INFO',$DKPInfo);

		return $a_dkpinfo ;

	}#end dkpinfo


	/**
	 * Erzeugt
	 *
	 * @param unknown_type $multifilter
	 */
	function showDKPLeaderboard($multifilter)
	{
		// get needed global vars
		global $eqdkp, $db, $tpl, $SID, $conf_plus, $htmlPlus, $filter, $dkpplus, $html;

		// Max players listed per class (0 = all)
		define('MAXLIST', $conf_plus['pk_leaderboard_limit']);

	 	if ($conf_plus['pk_multidkp'] == 1)
		{

			$sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
		        	FROM ' . MULTIDKP_TABLE . '
		        	WHERE multidkp_name IS NOT NULL'
		        	;

			 if ( !($multi_result = $db->query($sql)) )
			 {
			 	message_die('Could not obtain Leaderboard MultiDKP information', '', __FILE__, __LINE__, $sql);
			 }


		 	$tpl->assign_block_vars('multi_row', array(
		    'VALUE'    => "0",
		    'SELECTED' => ( $filter == strtolower("None") ) ? ' selected="selected"' : '',
		    'OPTION'   => str_replace('_', ' ', "None"))
			);

		    // Add in the cute ---- line, filter on None if some idiot selects it
		    $tpl->assign_block_vars('multi_row', array(
		    'VALUE'    => "0",
		    'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
		    'OPTION'   => str_replace('_', ' ', "--------")));

		    $showmultifilter = '';
			while ( $row = $db->fetch_record($multi_result) )
			{
				  $tpl->assign_block_vars('multi_row', array(
			      'VALUE' => $row['multidkp_id'],
			      'SELECTED' => ( strtolower($multifilter) == strtolower($row['multidkp_id']) ) ? ' selected="selected"' : '',
			      'OPTION'   => ( !empty($row['multidkp_name']) ) ? stripslashes($row['multidkp_name']) : '(None)' )
			      );

			      if(strtolower($multifilter) == strtolower($row['multidkp_id']))
			      {
			      	$showmultifilter = " - ". stripslashes($row['multidkp_name']) ;
			      }

			}

			$tpl->assign_vars(array('SHOW_MULTI'    => true,
									'SHOW_MULTI_FILTER'    => $showmultifilter,));


			#Multifilter = Kontoname z.b. Tier4
			if(isset($multifilter) and ( strtoupper($multifilter ) <> 'NONE') and ($multifilter  <> '--------') and $multifilter > 0)
			{
				// create the multiDKP Array
				$member_multidkp = $this-> multiDkpAllMemberArray($multifilter) ;

				if(!empty($member_multidkp))
				{

					# create temp array mit den klassen dann den membern
					# damit wir später ein array haben -> array[klassen][member]
					$a_member_class = array();
					foreach($member_multidkp as $key => $value)
					{
						$a_member_class[$value['class']][] = $value;
					}
					$a_classes = array();

					#gehe durch die klassen und setze die header
					foreach ($a_member_class as $key => $value)
					{
						if (!array_search(strtolower($key),$a_classes)  and (strtolower($key) <> 'unknown'))
						{
							$a_classes[] = strtolower($key);
							$tpl->assign_block_vars('classheader_row', array(
									'ICON'		 		  	=> get_ClassIcon($key),
									'ROW_CLASS'	 		  	=> $eqdkp->switch_row_class(),
									'NAME_ENG'		 		=> get_classColorChecked($key),
									'NAME'          		=> $key));
						}

					  	#gehe durch die member in den klassen
						 foreach ($value as $data)
						 {
							 $skip = false;
						 	 $classcounte[$data['class']]++;

						 	 if(($conf_plus['pk_leaderboard_limit'] == '') or ($conf_plus['pk_leaderboard_limit'] == 0)) {
						 	 	$max = 999;
						 	 }
						 	 else
						 	 {
						 	 	$max = intval($conf_plus['pk_leaderboard_limit']);
						 	 }


							 if($classcounte[$data['class']] > $max )
							 {$skip=true;}

							 if(strtolower($data['class']) == 'unknown' )
							 {$skip=true;}

							 if($data['rank_hide']==1) {
							 	$skip=true;}

						 	 if($conf_plus['pk_leaderboard_hide_zero']==1)
							 {
								if($data['current'] == 0)
								{$skip = true ;}
							 }

							 if(!$skip)
							 {
								 $class = 'classheader_row.classlist_row' ;
								 $tpl->assign_block_vars($class, array(
									'NAME'          => $html->ToolTip($data['dkp_tooltip'],get_coloredLinkedName($data['name'])) ,
									'CURRENT' 		=> $html->ToolTip($data['dkp_tooltip'],$data['current']) ,
									'C_CURRENT'     => color_item(round($data['current'])),
									'U_VIEW_MEMBER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$data['name'])
													);
							 }
						 }# end for each member
					 }; # end for each classes
			  	}; # end if empty
			}; # end if set filter
		}
		else # kein multidkp -> normale ansicht
		{
	    	// select the classes (distinct: for only once)
			$sql= 'SELECT DISTINCT class_name FROM '. CLASS_TABLE;
			$classes = $db->query($sql);

			while ( $class_row = $db->fetch_record($classes) )
			{
				if(strtolower($class_row['class_name']) <> 'unknown')
				{
					// build sql string
					$sql = "SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current,
				 			member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
							c.class_name AS member_class, c.class_armor_type AS armor_type,
				 			c.class_min_level AS min_level, c.class_max_level AS max_level
							FROM " . MEMBERS_TABLE . " m, " . MEMBER_RANKS_TABLE . " r, " . CLASS_TABLE . " c
							WHERE c.class_id = m.member_class_id
							AND (m.member_rank_id = r.rank_id)
							AND c.class_name =  '".$class_row['class_name']."'
							AND rank_hide = '0'";

					// Are we hiding inactive members?
					if ( $eqdkp->config['hide_inactive'] == '1' )
						$sql .= " AND member_status <> '0'";

					$sql .= " ORDER BY member_current desc";

					// add limit if set
					if(MAXLIST > 0)
					{$sql .= " LIMIT 0,".MAXLIST;}

					if ($class_result = $db->query($sql))
					{
						#show only if membercount in a class is > 0
						if ($db->num_rows($class_result) > 0)
						{
							 // create the header row
							$tpl->assign_block_vars('classheader_row', array(
								'ICON'		 		  	=> get_ClassIcon($class_row['class_name']),
								'ROW_CLASS'	 		  	=> $eqdkp->switch_row_class(),
								'NAME_ENG'		 		=> renameClasstoenglish($class_row['class_name']),
								'NAME'          		=> $class_row['class_name']));

							// produce output
							while ( $row = $db->fetch_record($class_result) )
							{
								if((intval(round($row['member_current'])) == 0 ) and ($conf_plus['pk_leaderboard_hide_zero']==1))
								{
									//hide member with zero DKP
								}else
								{
									$tpl->assign_block_vars('classheader_row.classlist_row', array(
										'NAME'          => $row['rank_prefix'] . (( $row['member_status'] == '0' ) ? '<i>' . get_coloredLinkedName($row['member_name']) . '</i>' : get_coloredLinkedName($row['member_name'])) . $row['rank_suffix'],
										'CURRENT'       => runden($row['member_current']),
										'C_CURRENT'     => color_item(runden($row['member_current'])),
										'U_VIEW_MEMBER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$row['member_name']));
								}
							}
						}
					}
					$db->free_result($class_result);
				} # end if unknown
			}# end while
		} # end not multi
	} # end function

}// end of class
?>
