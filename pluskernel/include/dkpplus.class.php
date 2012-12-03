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



		$tpl->assign_vars(array('NORMAL_LEADERBOARD'    => !$conf_plus['pk_leaderboard_normal']));


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
									'ROW'		 			=> $eqdkp->switch_row_class(),
									'PERCENT'		 		=> 0,
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
						$clas_count = $db->num_rows($class_result) ;
						$j = 1;
						if ($clas_count > 0)
						{
							 // create the header row
							$tpl->assign_block_vars('classheader_row', array(
								'ICON'		 		  	=> get_ClassIcon($class_row['class_name']),
								'ROW_CLASS'	 		  	=> $eqdkp->switch_row_class(),
								'NAME_ENG'		 		=> renameClasstoenglish($class_row['class_name']),
								'NAME'          		=> $class_row['class_name'],
								'PERCENT'          		=> 100/$clas_count,
								'ROW'          			=>$eqdkp->switch_row_class(),
								));
								$j++;

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
