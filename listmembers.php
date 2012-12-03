<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listmembers.php
 * begin: Wed December 18 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

$user->check_auth('u_member_list');

$sort_order = array(
    0 => array('member_name', 'member_name desc'),
    1 => array('member_earned desc', 'member_earned'),
    2 => array('member_spent desc', 'member_spent'),
    3 => array('member_adjustment desc', 'member_adjustment'),
    4 => array('member_current desc', 'member_current'),
    5 => array('member_lastraid desc', 'member_lastraid'),
    6 => array('member_level desc', 'member_level'),
    7 => array('member_class', 'member_class desc'),
    8 => array('rank_name', 'rank_name desc'),
    9 => array('class_armor_type', 'class_armor_type desc'),
    10 => array('last_loot desc', 'last_loot'),
    11 => array('frr desc', 'frr'),
    12 => array('ar desc', 'ar'),
    13 => array('sr desc', 'sr'),
    14 => array('nr desc', 'nr'),
    15 => array('fir desc', 'fir'),
    );

$current_order = switch_order($sort_order);
$cur_hash = hash_filename("listmembers.php");

if ($pm->check(PLUGIN_INSTALLED, 'gearscore')) {

	$GearScore		= new GearScore;
	$gear_score_conf	= $GearScore->getConfig();
	$gear_score	= $GearScore->retrieve();
	$tpl->assign_vars(array(
		'GS_SHOW_LISTMEMBERS' 	=> $gear_score_conf['gs_show_listmembers'],
		'L_GS_LIST_TITLE' 		=> $user->lang['gs_list_title']
	));

}

$_comp = $in->get('compare');

if($conf_plus['pk_noDKP'] == 1) { redirect('viewnews.php');}

if ( isset($_POST['submit']) && ($_POST['submit'] == $user->lang['compare_members']) && isset($_POST['compare_ids']) )
{
    // To compare members, we take the post checkboxes,
    // serialize them and pass them to the script again through _GET
    redirect('listmembers.php'.$SID.'&amp;compare=' . implode(',', $_POST['compare_ids']));

}
//
// Normal member display
//
else
{
    // Filtering
    $filter = ( isset($_GET['filter']) ) ? urldecode($in->get('filter')) : 'none';
    $filter = ( preg_match('#\-{1,}#', $filter) ) ? 'none' : $filter;

    // MultiDKP Filtering
    $multifilter = ( isset($_GET['multifilter']) ) ? urldecode($in->get('multifilter')) : 'none';
    $multifilter = ( preg_match('#\-{1,}#', $multifilter) ) ? 'none' : $multifilter;

    //Sortorder
    if(trim(strtolower($_GET['sortorder'])) == 'asc'){
    	$_GET['sortorder'] = 'desc';
    	$plus_sortorder = SORT_ASC;
    }else{
    	$_GET['sortorder'] = 'asc';
    	$plus_sortorder = SORT_DESC;
    }
    $_sort_value = trim(strtolower($_GET['sortby']));

    // Figure out what data we're comparing from member to member
    // in order to rank them
    $sort_index = explode('.', $current_order['uri']['current']);
    $previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);
    $show_all = ( (!empty($_GET['show'])) && ($_GET['show'] == 'all') ) ? true : false;

    // Grab class_id
    $temp_filter = '';
    if ( isset($_GET['filter']) )
    {
		$temp_filter = $in->get('filter');

       // Just because filter is set doesn't mean its valid - clear it if its set to none
       if ( preg_match('/ARMOR_/', $temp_filter) )
       {
			$temp_filter = preg_replace('/ARMOR_/', '', $temp_filter);
			$query_by_armor = 1;
        	$query_by_class = 0;
			$id = $temp_filter;
	 	}
		elseif( $temp_filter == "none" )
		{
            $temp_filter = "";
	    	$query_by_armor = 0;
            $query_by_class = 0;
		}
		else
		{
            $query_by_class = 1;
            $query_by_armor = 0;
            $id = $temp_filter;
       	}

       if(preg_match('/pool/', $temp_filter))
		{
			$query_by_armor = 0;
            $query_by_class = 0;
            $query_by_pool = 1;
		}
	}

		// Add in the cute ---- line, filter on None if some idiot selects it
    	$tpl->assign_block_vars('filter_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "--------")));

		// Grab generic armor information
		$sql = 'SELECT class_armor_type FROM ' . CLASS_TABLE .'';
		$sql .= ' GROUP BY class_armor_type';
		$result = $db->query($sql);

	    while ( $row = $db->fetch_record($result) )
	    {
				// fixed by WalleniuM: $filter must be $temp_filter, because of the ARMOR_ prefix
	      	  $tpl->assign_block_vars('filter_row', array(
	          'VALUE'    => "ARMOR_" . strtolower($row['class_armor_type']),
	          'SELECTED' => ( $temp_filter == strtolower($row['class_armor_type']) ) ? ' selected="selected"' : '',
	          'OPTION'   => str_replace('_', ' ', $row['class_armor_type']))
	      );

	    }

		// Add in the cute ---- line, filter on None if some idiot selects it
    $tpl->assign_block_vars('filter_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "--------")));

		// Moved the class/race/faction information to the database
	  $sql = 'SELECT class_name, class_id, class_min_level, class_max_level FROM ' . CLASS_TABLE .'';
	  $sql .= ' GROUP BY class_name';
	  $result = $db->query($sql);

	  while ( $row = $db->fetch_record($result) )
	  {
	  	// fixed by WalleniuM: $filter must be lower, because of the lower db entry!
	  	$tpl->assign_block_vars('filter_row', array(
      'VALUE' => $row['class_name'],
      'SELECTED' => ( strtolower($filter) == strtolower($row['class_name']) ) ? ' selected="selected"' : '',
      'OPTION'   => ( !empty($row['class_name']) ) ? stripslashes($row['class_name']) : '(None)' )
      );
	  }


	  //Add filter depand on the Game - /game/filter.php
	  //##############################
	 if($game_icons['filter'])
	 {
		 #DKP Pools
		 ###########

	    $file = $eqdkp_root_path.'games/'.$eqdkp->config['default_game'].'/filter.php';
	    if (file_exists($file))
	    {
		    $tpl->assign_block_vars('filter_row', array(
		    'VALUE'    => strtolower("None"),
		    'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
		    'OPTION'   => str_replace('_', ' ', "--------")));

		    include_once($file);
		 	$filter_class = new GameFilter();
		 	$filterlist = $filter_class->addFilter($filter);
	    }
	 }

	  $db->free_result($result);

	$cident = $filter.'.'.$_comp.'.'.$plus_sortorder.'.'.$_sort_value.'.'.$current_order['sql'].'.'.$show_all;
	$members_rows = $pdc->get('dkp.listmember.'.$cident,false,true);

	if (!$members_rows)
	{

	    $member_count = 0;
	    $previous_data = '';

	    // Find 30 days ago, then find how many raids occurred in those 30 days
	    // Do the same for 60 and 90 days
	    if($conf_plus['pk_attendance30']==1)
	    {
		    $thirty_days = mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
		    $raid_count_30 = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE . ' WHERE raid_date BETWEEN '.$thirty_days.' AND '.time());
	  	}

	    if($conf_plus['pk_attendance60']==1)
	    {
		    $sixty_days  = mktime(0, 0, 0, date('m'), date('d')-60, date('Y'));
		    $raid_count_60 = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE . ' WHERE raid_date BETWEEN '.$sixty_days.' AND '.time());
	  	}

	    if($conf_plus['pk_attendance90']==1)
	    {
		    $ninety_days = mktime(0, 0, 0, date('m'), date('d')-90, date('Y'));
		 		$raid_count_90 = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE . ' WHERE raid_date BETWEEN '.$ninety_days.' AND '.time());
	 		}

	    if($conf_plus['pk_attendanceAll']==1)
	    {

			$raid_count_all = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE) ;
	 	}

		// end database move of race/class/faction
		// Build SQL query based on GET options
		$sql = 'SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current,
			member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
			c.class_name AS member_class,
			c.class_armor_type AS armor_type,
			c.class_min_level AS min_level,
			c.class_max_level AS max_level
			FROM ' . MEMBERS_TABLE . ' m, ' . MEMBER_RANKS_TABLE . ' r, ' . CLASS_TABLE . ' c
			WHERE c.class_id = m.member_class_id
			AND (m.member_rank_id = r.rank_id)';


	    if ($pm->check(PLUGIN_INSTALLED, 'charmanager'))
		{
			if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }

			$sql = 'SELECT m.*, ma.*, c.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current, m.member_id AS member_id,
		           member_status, m.member_race_id,  r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
		           c.class_name AS member_class,
		           c.class_armor_type AS armor_type,
		           c.class_min_level AS min_level,
		           c.class_max_level AS max_level
		           FROM ' . MEMBER_RANKS_TABLE . ' r, ' . CLASS_TABLE . ' c, ' . MEMBERS_TABLE . ' m
		           LEFT JOIN ' . MEMBER_ADDITION_TABLE . ' ma ON (ma.member_id=m.member_id)
		           WHERE c.class_id = m.member_class_id
		           AND (m.member_rank_id = r.rank_id)';
		}


	    if ( !empty($_GET['rank']) and validateRank($_GET['rank']) )
	    {
	        $sql .= " AND r.rank_name='" . urldecode($_GET['rank']) . "'";
	    }


	    if ( isset($query_by_class) && $query_by_class == '1' )
	    {
	        $sql .= " AND c.class_name =  '$id'";
	    }

	    if ( isset($query_by_armor) && $query_by_armor == '1' )
	    {
	        $sql .= " AND c.class_armor_type =  '".$temp_filter."'";
	    }

	    //Add Filter depand on Game Filter
	    if (is_array($filterlist) AND array_key_exists($filter,$filterlist))
	    {
	    	$sql .= " AND (". $filterlist[$filter] .")";
	    }

	   $sql .= ' ORDER BY '.$current_order['sql']; #original


	    if ( !($members_result = $db->query($sql)) )
	    {
	        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
	    }

	        $rc_sql = 'SELECT ra.member_name as name, r.raid_date as date
	               FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
	               WHERE ra.raid_id = r.raid_id";

	    if ( !($rc_result = $db->query($rc_sql)) ) {
	        message_die('Could not obtain member/raid information', '', __FILE__, __LINE__, $sql);
	    }
	    $rc_30 = array();
	    $rc_60 = array();
	    $rc_90 = array();
	    $rc = array();

	    while ( $row = $db->fetch_record($rc_result) ) {
	        if (!$rc[$row['name']]) $rc[$row['name']] = 0;
	        $rc[$row['name']] += 1;
	        if ( $row['date'] > $ninety_days ) {
	            if (!$rc_90[$row['name']]) $rc_90[$row['name']] = 0;
	            $rc_90[$row['name']] += 1;
	            if ( $row['date'] > $sixty_days ) {
	                if (!$rc_60[$row['name']]) $rc_60[$row['name']] = 0;
	                $rc_60[$row['name']] += 1;
	                if ( $row['date'] > $thirty_days ) {
	                    if (!$rc_30[$row['name']]) $rc_30[$row['name']] = 0;
	                    $rc_30[$row['name']] += 1;
	                }
	            }
	        }
	    }
	    $compare_string = (isset($_GET['compare']))? $_GET['compare'] : '';
	    if($compare_string != ''){
	      $compare_members = explode(',', $compare_string);
	    }

			//
			// Member Schleife
			//
			while ( $row = $db->fetch_record($members_result) )
	    {
	        if(!empty($compare_members) && !in_array($row['member_id'], $compare_members)){
	          continue;
	        }
	        unset($event_data) ;
	        // Figure out the rank search URL based on show and filter
	        $u_rank_search  = 'listmembers.php' . $SID . '&amp;rank=' . urlencode($row['rank_name']);
	        $u_rank_search .= ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) ) ? '&amp;show=' : '&amp;show=all';
	        $u_rank_search .= ( $filter != 'none' ) ? '&amp;filter=' . $filter : '';

	        if($conf_plus['pk_attendance30']==1)
	        {
	            $individual_raid_count_30 = $rc_30[$row['member_name']];
	            $percent_of_raids_30 = ( $raid_count_30 > 0 ) ? round(($individual_raid_count_30 / $raid_count_30) * 100) : 0;
	        }

	        if($conf_plus['pk_attendance60']==1)
	        {
	            $individual_raid_count_60 = $rc_60[$row['member_name']];
	            $percent_of_raids_60 = ( $raid_count_60 > 0 ) ? round(($individual_raid_count_60 / $raid_count_60) * 100) : 0;
	        }

	        if($conf_plus['pk_attendance90']==1)
	        {
	            $individual_raid_count_90 = $rc_90[$row['member_name']];
	            $percent_of_raids_90 = ( $raid_count_90 > 0 ) ? round(($individual_raid_count_90 / $raid_count_90) * 100) : 0;
	        }

	        if($conf_plus['pk_attendanceAll']==1)
	        {
	            $individual_raid_count_all = $rc[$row['member_name']];
	            $percent_of_raids_all = ( $raid_count_all > 0 ) ? round(($individual_raid_count_all / $raid_count_all ) * 100) : 0;
	        }

	        if ( member_display($row) )
	        {


					$member_count++;
	       			$members_rows[$member_count] = $row;
	        		$members_rows[$member_count]['u_rank_search'] = $u_rank_search;
	        		$members_rows[$member_count]['member_count'] = $member_count;

	        		$members_rows[$member_count]['individual_raid_count_30'] = $individual_raid_count_30;
					$members_rows[$member_count]['individual_raid_count_60'] = $individual_raid_count_60;
					$members_rows[$member_count]['individual_raid_count_90'] = $individual_raid_count_90;
					$members_rows[$member_count]['individual_raid_count_all'] = $individual_raid_count_all;

	        		$members_rows[$member_count]['percent_of_raids_30'] = $percent_of_raids_30;
					$members_rows[$member_count]['percent_of_raids_60'] = $percent_of_raids_60;
					$members_rows[$member_count]['percent_of_raids_90'] = $percent_of_raids_90;
					$members_rows[$member_count]['percent_of_raids_all'] = $percent_of_raids_all;

					$members_rows[$member_count]['last_loot'] = $last_loot;
	        		$members_rows[$member_count]['member_earned'] = runden($row['member_earned']);
	        		$members_rows[$member_count]['member_spent'] = runden($row['member_spent']);
	        		$members_rows[$member_count]['member_adjustment'] = runden($row['member_adjustment']);
					$members_rows[$member_count]['member_current'] = runden($row['member_current']);

	        	// MultiDKP
	        	// #####################################
	        	//
				if($conf_plus['pk_multidkp'] == 1)
				{
					//
					// earned
					// ###############################################################
	        		$pv_sql = "SELECT " . RAIDS_TABLE .".raid_name, SUM(raid_value)
	        				   FROM
	        				   ". RAID_ATTENDEES_TABLE ." LEFT JOIN " . RAIDS_TABLE ." ON ". RAID_ATTENDEES_TABLE .".raid_id=" . RAIDS_TABLE .".raid_id
	        				   WHERE ". RAID_ATTENDEES_TABLE .".member_name = '".$row['member_name']."' GROUP by " . RAIDS_TABLE .".raid_name";

			        $pv_result = $db->query($pv_sql);
			        while( $pv_row = $db->fetch_record($pv_result, false) )
			        {
			        	$event_data[$pv_row[0]]['earned'] = $pv_row[1] ;
			        }
	        		# end earned
	        		###############################################################

					//
					// spend
					// ###############################################################
	        		$ps_sql = "SELECT ". RAIDS_TABLE .".raid_name, SUM(". ITEMS_TABLE .".item_value)
							   FROM
							   ". ITEMS_TABLE ."  LEFT JOIN ". RAIDS_TABLE ." ON ". ITEMS_TABLE .".raid_id=". RAIDS_TABLE .".raid_id
							   WHERE
							   ". ITEMS_TABLE .".item_buyer = '".$row['member_name']."' GROUP by ". RAIDS_TABLE .".raid_name;";

	        		$ps_result = $db->query($ps_sql);
	        		while( $ps_row = $db->fetch_record($ps_result, false) )
			        {
						$event_data[$ps_row[0]]['spend'] = $ps_row[1] ;
			        }
	        		# end spend
	        		###############################################################

					//
					// Adjust
					// ###############################################################
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
	        		// Konten
	        		// ###############################################################

	            $multicount = 0 ;
					    // get MultiDKP Data from eqdkp_multidkp
	            $multi_results = get_multi_pools();
	            reset($multi_results);
	            while ( list($unused_key, $a_multi) = each($multi_results) )
							{
								$multicount++;

								// namen speichern fürs template
								$multi_name[$multicount]['name'] = $a_multi['multidkp_name'];
								$multi_name[$multicount]['id'] = $a_multi['multidkp_id'];
								$tempmultiname = $a_multi['multidkp_name']; // for the sort array


								$members_rows[$member_count]['dkp_tooltip'] .= "Konto - ".$a_multi['multidkp_name']."<br>";

								//Konten2Events
								//Konten verknüpft mit Events den Multikonten des Member zuweisen
								//###############################################################

								// SmartTooltip
								if ($conf_plus['pk_multiSmarttip'] == 1)
								{
									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=

									'<table cellpadding=2 cellspacing=10>
									<tr>
										<td>'.$user->lang['event'].'</td>
										<td>'.$user->lang['earned'].'</td>
										<td>'.$user->lang['spent'].'</td>
										<td>'.$user->lang['adjustment'].'</td>
										<td>'.$user->lang['current'].'</td>
									</tr>';
								}

	              $multi2event_results = get_events_for_poolid($a_multi['multidkp_id']);
	              reset($multi2event_results);
	              while ( list($unused_key, $a_multi) = each($multi2event_results) )
								{ // gehe alle Events durch, die einem Konto zugewiesen wurden

									$current = 0 ;
									 // current wert berechnen
									 // ###############################################################
									 $current = $event_data[$a_multi['multidkp2event_eventname']]['earned'] -
									 $event_data[$a_multi['multidkp2event_eventname']]['spend'] +
									 $event_data[$a_multi['multidkp2event_eventname']]['adjust'] ;

									 //Generate DKP Tooltip
									 //###############################################################
									if ($conf_plus['pk_multiTooltip'] == 1)  // Tooltip on/off
									{
										if ($conf_plus['pk_multiSmarttip'] == 1) // SmartTooltip
										{
											$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=

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

									else if ($conf_plus['pk_multiSmarttip'] == 0) // normal Tooltip)
									{
										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
											'<span class=itemdesc>'.$user->lang['event'].': '.$a_multi['multidkp2event_eventname']."</span><br>" ;

										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
											" &nbsp;".$user->lang['earned'].": <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['earned'])."> "
											.runden($event_data[$a_multi['multidkp2event_eventname']]['earned'])."</span><br>";

										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
											" &nbsp;".$user->lang['spent'].":   <span class=negative> "
											.runden($event_data[$a_multi['multidkp2event_eventname']]['spend'])."</span><br>";

										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
											" &nbsp;".$user->lang['adjustment'].":  <span class=".color_item($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."> "
											.runden($event_data[$a_multi['multidkp2event_eventname']]['adjust'])."</span><br>";

										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
											" &nbsp;".$user->lang['current'].": <span class=".color_item($current)."> "
											.runden($current)."</span><br><br>";
									}
								}

									//Array for the template
									//$member_count = spielernummer
									//multicount = Account (konto)
									//Die Werte werden einfach aufaddiert
									//###############################################################
									$members_rows_multidkp[$member_count][$multicount]['earned'] += runden($event_data[$a_multi['multidkp2event_eventname']]['earned']);
									$members_rows_multidkp[$member_count][$multicount]['spend'] += runden($event_data[$a_multi['multidkp2event_eventname']]['spend']);
									$members_rows_multidkp[$member_count][$multicount]['adjust'] += runden($event_data[$a_multi['multidkp2event_eventname']]['adjust']);
									$members_rows_multidkp[$member_count][$multicount]['current'] += runden($current, 2);
								}

								 // Generate the Sort string
								$sort_current[strtolower($tempmultiname)][$member_count] = $members_rows_multidkp[$member_count][$multicount]['current'];

								// Generate DKP Tooltip
								// ###############################################################
								if ($conf_plus['pk_multiTooltip'] == 1)  // Tooltip on/off
								{
									if ($conf_plus['pk_multiSmarttip'] == 1) // SmartTooltip
									{
										$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=

											'<tr>
												<td><span class=itemdesc>'.$user->lang['Multi_total_cost'].": </span></td>
												<td><span class="
														.color_item($members_rows_multidkp[$member_count][$multicount]['earned']).">"
														.$members_rows_multidkp[$member_count][$multicount]['earned']."</span></td>
												<td><span class=negative>"
														 .$members_rows_multidkp[$member_count][$multicount]['spend']."</span></td>
												<td><span class="
														.color_item($members_rows_multidkp[$member_count][$multicount]['adjust']).">"
														.$members_rows_multidkp[$member_count][$multicount]['adjust']."</span></td>
												<td><span class="
														.color_item($members_rows_multidkp[$member_count][$multicount]['current']).">"
														.$members_rows_multidkp[$member_count][$multicount]['current']."</span></td>
											</tr> ";
								  }

								 	else if ($conf_plus['pk_multiSmarttip'] == 0) // normal Tooltip)
									{
									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .=
									"<span class=itemdesc><b>".$user->lang['Multi_total_cost'].":</b> </span><br>";

									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .= " &nbsp;".$user->lang['earned'].": <span class="
										.color_item($members_rows_multidkp[$member_count][$multicount]['earned']).">"
										.$members_rows_multidkp[$member_count][$multicount]['earned']."</span><br>";

									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .= " &nbsp;".$user->lang['spent'].": <span class=negative>"
										.$members_rows_multidkp[$member_count][$multicount]['spend']."</span><br>";

									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .= " &nbsp;".$user->lang['adjustment'].": <span class="
										.color_item($members_rows_multidkp[$member_count][$multicount]['adjust']).">"
										.$members_rows_multidkp[$member_count][$multicount]['adjust']."</span><br>";

									$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .= " &nbsp;".$user->lang['current'].": <span class="
										.color_item($members_rows_multidkp[$member_count][$multicount]['current']).">"
										.$members_rows_multidkp[$member_count][$multicount]['current']."</span><br>";
									} // end if smart tooltip
								} // end if tooltip

							if ($conf_plus['pk_multiSmarttip'] == 1 and $conf_plus['pk_multiTooltip'] == 1)
							{
								$members_rows_multidkp[$member_count][$multicount]['dkp_tooltip'] .= '</table>';
							}

	    				}; // end while konten
					} ; // End If MultiDKP
					###############################################################

					// If the member's spent is greater than 0, see how long ago they looted an item
					if ( $row['member_spent'] > 0 and $conf_plus['pk_lastloot']==1)
					{
						$ll_sql = 	'SELECT max(item_date) AS last_loot
								   			FROM ' . ITEMS_TABLE . "
								   			WHERE item_buyer='".$row['member_name']."'";
						$last_loot = $db->query_first($ll_sql);
						$members_rows[$member_count]['last_loot'] = $last_loot  ;
			    }

	            #$tpl->assign_block_vars('members_row', $line_array );
	            $u_rank_search = '';
	            unset($last_loot);

	            // So that we can compare this member to the next member,
	            // set the value of the previous data to the source
	            $previous_data = $row[$previous_source];
	        }
	    }

	    if (isset($_GET['sortby']))
	    {
	    	$members_rows_fsort = array();
	    	$sort_value = trim(strtolower($_GET['sortby']));

			if(isset($sort_current[$sort_value]))
			{
				foreach($sort_current[$sort_value] as $key => $row)
				{
					$members_rows_fsort[$key] = $row;//intval($row);
				}
			  array_multisort($members_rows_fsort, $plus_sortorder, SORT_NUMERIC, $members_rows);
		 	}
		}


	$pdc->put('dkp.listmember.'.$cident,$members_rows,86400,false,true);
	$pdc->put('dkp.listmember.multiname.'.$cident,$multi_name,86400,false,true);
	$pdc->put('dkp.listmember.members_rows_multidkp.'.$cident,$members_rows_multidkp,86400,false,true);

	}else #end if $members_rows from cache
	{
		$multi_name = $pdc->get('dkp.listmember.multiname.'.$cident,false,true);
		$members_rows_multidkp = $pdc->get('dkp.listmember.members_rows_multidkp.'.$cident,false,true);
	}

	if(isset($members_rows))
	{
		$memberArray2xml = array();
	    if(isset($conf_plus['pk_servername']) && isset($conf_plus['pk_server_region']) && $eqdkp->config['default_game'] == 'WoW')
	    {
        	include_once($eqdkp_root_path.'pluskernel/include/armory.class.details.php');
        	$armory_light = new ArmoryCharLoader('äüö');
	    }

	    $row_nr = 1;
	    foreach($members_rows as $row)
	    {
	       // class img
	       $mclass = renameClasstoenglish($row['member_class']) ;
		   $rankimg = get_RankIcon($row['rank_name']);
  		   $class_icon =  get_classImgListmembers($row['member_class'] , $row['member_class_id']) ;

  		   $race_icon = get_RaceIcon($row['member_race_id'],$row['member_name'] );
		   $rank_icon = get_RankIcon($row['rank_name']);
		   $class_icons_text = $race_icon.$class_icon." ".$row['member_class'];
		   $skill = "";

		   # Amory Link code  by Wallenium
	       	#####################################
	       	if(is_object($armory_light))
	       	{
        		// build the link: $armory_light->BuildLink($conf_plus['pk_server_region'], $row['member_name'], $conf_plus['pk_servername'])
				$menulink = array();
				$menulink[1] = $armory_light->BuildLink($conf_plus['pk_server_region'], $row['member_name'], $conf_plus['pk_servername']);
				$menulink[2] = $armory_light->BuildLink($conf_plus['pk_server_region'], $row['member_name'], $conf_plus['pk_servername'], 'talent');

				if(($pm->check(PLUGIN_INSTALLED, 'charmanager')) and ($row['member_id'] > 0))
				{
					$menulink[3] = ($row['guild']) ? $armory_light->BuildLink($conf_plus['pk_server_region'], $row['guild'], $conf_plus['pk_servername'], 'guild') : '';
					$last_update = date($user->lang['uc_changedate'],$row['last_update']);
					$skill1 = get_wow_talent_spec(ucwords(renameClasstoenglish($row['member_class'])), $row['skill_1'],$row['skill_2'] ,$row['skill_3'], $row['member_name'], $last_update, true);
					$skill2 = get_wow_talent_spec(ucwords(renameClasstoenglish($row['member_class'])), $row['skill2_1'],$row['skill2_2'] ,$row['skill2_3'], $row['member_name'], $last_update, true);
				  if($skill2['icon']){
				    $skill = $skill1['icon'].' '. $skill2['icon'];;
				  }else
				  {
            		$skill = $skill1['icon'];
          		  }
        		}
				$tpl->assign_vars(array('SHOW_AMORY' => TRUE));


			}# end if
			
			$memberArray2xml[$row['member_id']]['id'] = $row['member_id'];
			$memberArray2xml[$row['member_id']]['name'] = $row['member_name'];
			$memberArray2xml[$row['member_id']]['url'] = $pcache->BuildLink().'viewmember.php?name='.$row['member_name'];			
			$memberArray2xml[$row['member_id']]['class'] = $row['member_class'];
			$memberArray2xml[$row['member_id']]['class_eng'] = $mclass;
			$memberArray2xml[$row['member_id']]['race'] = $pcache->BuildLink().get_RaceIcon($row['member_race_id'], "",true);
			$memberArray2xml[$row['member_id']]['class_icon'] = $pcache->BuildLink().get_ClassIcon($row['member_class'] , $row['member_class_id'], false, true);			
			$memberArray2xml[$row['member_id']]['member_earned'] = $row['member_earned'];
			$memberArray2xml[$row['member_id']]['member_spent'] = $row['member_spent'];
			$memberArray2xml[$row['member_id']]['member_adjustment'] = $row['member_adjustment'];
			$memberArray2xml[$row['member_id']]['member_current'] = $row['member_current'];
			$memberArray2xml[$row['member_id']]['member_status'] = $row['member_status'];
			$memberArray2xml[$row['member_id']]['member_raidcount'] = $row['member_raidcount'];
			$memberArray2xml[$row['member_id']]['member_level'] = $row['member_level'];
			$memberArray2xml[$row['member_id']]['rank_name'] = $row['rank_name'];

				 $line_array = array(
	          'ROW_CLASS'     => $eqdkp->switch_row_class(),
	          'ID'            => $row['member_id'],
	          //'COUNT'       => ($row[$previous_source] == $previous_data) ? '&nbsp;' : $member_count,
	          'COUNT'         => $row_nr++,//$row['member_count'],
	          'NAME'          => $row['rank_prefix'] . (( $row['member_status'] == '0' ) ? '<i>' . $row['member_name'] . '</i>' : $row['member_name']) . $row['rank_suffix'],
	          'RANK'          => ( !empty($row['rank_name']) ) ? (( $row['rank_hide'] == '1' ) ? '<i>' . '<a href="'.$u_rank_search.'">' . '</a>' . '</i>'  : '<a href="'.$u_rank_search.'">' .$rank_icon.'</a>') : '&nbsp;',
	          'RAICON'        => ( !empty($row['rank_name']) ) ? $row['rank_name'] : '&nbsp;',
			  'LEVEL'         => ( $row['member_level'] > 0 ) ? $row['member_level'] : '&nbsp;',
	          'CLASS'         => ( !empty($row['member_class']) ) ? $class_icons_text : '&nbsp;',
	          'CLASS_ICONS'	  => $class_icon.$race_icon ,
	          'CLASSENG'	  => (!strpos(strtolower($eqdkp->config['default_game']),'wow') > 0 ) ? get_classColorChecked($mclass) : '',
	          #'CLASSENG'	  => $mclass,

	          'ARMOR'		  => ( !empty($row['armor_type']) ) ? $row['armor_type'] : '&nbsp;',
	          'EARNED'        => runden($row['member_earned']),
	          'SPENT'         => runden($row['member_spent']),
	          'ADJUSTMENT'    => runden($row['member_adjustment']),
	          'CURRENT'       => runden($row['member_current']),
			  'RAIDS_30_DAYS' => $row['percent_of_raids_30'],
	      	  'RAIDS_60_DAYS' =>  $row['percent_of_raids_60'],
	      	  'RAIDS_90_DAYS' =>  $row['percent_of_raids_90'],
	      	  'RAIDS_ALL'     =>  $row['percent_of_raids_all'],

	      	  'RAIDS_30_DAYS_C'   => ($row['individual_raid_count_30']) ? $row['individual_raid_count_30'] : 0,
	      	  'RAIDS_60_DAYS_C'   => ($row['individual_raid_count_60']) ? $row['individual_raid_count_60'] : 0,
	      	  'RAIDS_90_DAYS_C'   => ($row['individual_raid_count_90']) ? $row['individual_raid_count_90'] : 0,
	      	  'RAIDS_ALL_C'       =>  $row['individual_raid_count_all'],

	      	  'FIRE'			  => ( $row['fir'] ) ? $row['fir'] : 0,
    		  'ARCANE'			  => ( $row['ar'] ) ? $row['ar'] : 0,
	   		  'FROST'			  => ( $row['frr'] ) ? $row['frr'] : 0,
	   		  'NATURE'			  => ( $row['nr'] ) ? $row['nr'] : 0,
	   		  'SHADOW'			  => ( $row['sr'] ) ? $row['sr'] : 0,
	   		  'SKILL'			  => ( $skill ) ? $skill : "",

	       	  'BLASC_ID'		  => $row['blasc_id'],
			  'CTPROFILE_ID'	  => $row['ct_profile'],
			  'ALLA_ID'			  => $row['allakhazam'],
			  'CURSE_ID'		  => $row['curse_profiler'],
			  'TALENT_URL'		  => $row['talentplaner'],
			  'SHOW_BLASC'		  => ( $row['blasc_id'] ) ? true : false,
			  'SHOW_CTPROFILE'	  => ( $row['ct_profile'] ) ? true : false,
			  'SHOW_ALLA'		  => ( $row['allakhazam'] ) ? true : false,
			  'SHOW_CURSE'		  => ( $row['curse_profiler'] ) ? true : false,
			  'SHOW_TALENT'		  => ( $row['talentplaner'] ) ? true : false,

		      'ARMORY_LINK1' => $menulink[1],
			  'ARMORY_LINK2' => $menulink[2],
			  'ARMORY_LINK3' => $menulink[3],

	          'LASTRAID'        => ( !empty($row['member_lastraid']) ) ? date($user->style['date_notime_short'], $row['member_lastraid']) : '&nbsp;',
	          'LASTLOOT'        => ( isset($row['last_loot']) ) ? date($user->style['date_notime_short'], $row['last_loot']) : '&nbsp;',
	          'C_ADJUSTMENT'    => color_item($row['member_adjustment']),
	          'C_CURRENT'       => color_item($row['member_current']),
	          'C_LASTRAID'      => 'neutral',
	          'C_RAIDS_30_DAYS' => color_item($row['percent_of_raids_30'], true),
	      	  'C_RAIDS_60_DAYS' => color_item($row['percent_of_raids_60'], true),
	      	  'C_RAIDS_90_DAYS' => color_item($row['percent_of_raids_90'], true),
	      	  'C_RAIDS_ALL'     => color_item($row['percent_of_raids_all'], true),
	          'U_VIEW_MEMBER'   => 'viewmember.php'.$SID . '&amp;' . URI_NAME . '='.$row['member_name'] . '" class="' . $mclass
				);
			if($pm->check(PLUGIN_INSTALLED, 'gearscore')) {
				$line_array['GEARSCORE'] = '<a href="http://be.imba.hu/?zone='.strtoupper($conf_plus['pk_server_region']).'&realm='.ucfirst($conf_plus['pk_servername']).'&character='.$row['member_name'].'">'.$gear_score[$row['member_name']]['gearscore'].'</a>';
			}
		    $tpl->assign_block_vars('members_row', $line_array );

				// MultiDKP dynamic DKP Data
				// ############################
				//

				if(!empty($members_rows_multidkp[$row['member_count']]))
				{
					 foreach ($members_rows_multidkp[$row['member_count']] as $key)
					 {
					   // Build Data Array
					   //


					  $multidkp_array = array(
					  												'EARNED' => $key['earned'] ,
					  												'SPENT' => $key['spend'] ,
					  												'ADJUST' => $key['adjust'] ,
					  												'CURRENT' => $html->ToolTip($key['dkp_tooltip'],$key['current']) ,
					  												'C_EARNED' => color_item($key['earned']) ,
					  												'C_SPENT' => color_item($key['spend']) ,
					  												'C_ADJUST' => color_item($key['adjust']) ,
					  												'C_CURRENT' =>  color_item($key['current'])
					  												);

						$tpl->assign_block_vars('members_row.multidkp',$multidkp_array );
					 } # end for each
				 }# end if
	    } # end foreach member row
    } # end if isset($members)

    $uri_addon  = ''; // Added to the end of the sort links
    $uri_addon .= '&amp;filter=' . urlencode($filter);
    if ($compare_string != ''){
		  $uri_addon .= '&amp;compare='.$compare_string;
    }
    $uri_addon .= '&amp;multifilter=' . urlencode($multifilter);
    $uri_addon .= ( isset($_GET['show']) ) ? '&amp;show=' . htmlspecialchars(strip_tags($_GET['show']), ENT_QUOTES) : '';


    if ( ($eqdkp->config['hide_inactive'] == 1) && (!$show_all) )
    {
        $footcount_text = sprintf($user->lang['listmembers_active_footcount'], $member_count,
                                  '<a href="listmembers.php' . $SID . '&amp;'
                                  . URI_ORDER . '=' . $current_order['uri']['current'] . '&amp;show=all" class="rowfoot">');
    }
    else
    {
        $footcount_text = sprintf($user->lang['listmembers_footcount'], $member_count);
    }
    $db->free_result($members_result);

   //leaderboard
   if ($conf_plus['pk_leaderboard'] == 1)
   {
			// Load Default Leaderboard Pool from Plus Config
			if(($conf_plus['pk_default_multi'] <> 'none') and isset($conf_plus['pk_default_multi']) and  ($multifilter =='none')){
				$dkpplus->showDKPLeaderboard($conf_plus['pk_default_multi']);
			}
			else{
				$dkpplus->showDKPLeaderboard($multifilter);
			}
   }
}

$collspan = 20;
// Write Headerdata to template
// build dynamic collums
// set the Headertitle and Overlib !!
if(!empty($multi_name))
{
	foreach ($multi_name as $key)
	{
		$collspan++;

		$multi2event = $user->lang['Multi_menuentry'].' '.$user->lang['Multi_event'].' <br/> <br/> ' ;
    	$multi2event_results = get_events_for_poolid($key['id']);

    	reset($multi2event_results);
   		while ( list($unused_key, $a_multi) = each($multi2event_results) )
		{ // gehe alle Events durch, die einem Konto zugewiesen wurden
			$multi2event .= $a_multi['multidkp2event_eventname'].' <br/> ' ;
		}

		if(isset($multifilter))
		{
			$multiuri = '&multifilter='.$multifilter ;
		}

		if(isset($_GET['filter']) && $_GET['filter'] != 'none')
		{
			$sortlink_header = 'listmembers.php'.$SID.'&sortby='.urlencode($key['name']).'&sortorder='.$_GET['sortorder'].'&filter='.$_GET['filter'].$multiuri;
		}
		else if ($compare_string != '')
		{
		$sortlink_header = 'listmembers.php'.$SID.'&sortby='.urlencode($key['name']).'&sortorder='.$_GET['sortorder'].'&compare='.$compare_string.$multiuri;
    }
    else
		{
			$sortlink_header = 'listmembers.php'.$SID.'&sortby='.urlencode($key['name']).'&sortorder='.$_GET['sortorder'].$multiuri;
		}

		$tpl->assign_block_vars('custom_header', array('HEADER_DISC'    =>
		"<a href='".$sortlink_header."' onMouseOver=\"overlib('<b>".$user->lang['Multi_events']."</b>".$multi2event."');\" onMouseOut='nd();'>".$key['name']."</a> " ));
	}
}

$tpl->assign_vars(array(
    'F_MEMBERS' => 'listmembers.php'.$SID,
    'V_SID'     => str_replace('?' . URI_SESSION . '=', '', $SID),

    'SHOW_LEADERB'		=> ( $conf_plus['pk_leaderboard'] == 1 )? true : false,
    'SHOW_RANK'				=> ( $conf_plus['pk_rank'] == 1 )? true : false,
    'SHOW_LEVEL'			=> ( $conf_plus['pk_level'] == 1 )? true : false,
    'SHOW_ATTEND_ALL'	=> ( $conf_plus['pk_attendanceAll'] == 1 )? true : false,
    'SHOW_ATTEND_90'	=> ( $conf_plus['pk_attendance90'] == 1 )? true : false,
    'SHOW_ATTEND_60'	=> ( $conf_plus['pk_attendance60'] == 1 )? true : false,
    'SHOW_ATTEND_30'	=> ( $conf_plus['pk_attendance30'] == 1 )? true : false,
    'SHOW_LASTLOOT'		=> ( $conf_plus['pk_lastloot'] == 1 )? true : false,
    'SHOW_LASTRAID'		=> ( $conf_plus['pk_lastraid'] == 1 )? true : false,
    'SHOW_CLASS'		  => ( $conf_plus['pk_showclasscolumn'] == 1 )? true : false,

    'SHOW_CMC_SKILL'	=> ( $conf_plus['pk_show_skill'] == 1 )? true : false,
    'SHOW_CMC_ARKAN'	=> ( $conf_plus['pk_show_arkan_resi'] == 1 )? true : false,
    'SHOW_CMC_FIRE'		=> ( $conf_plus['pk_show_fire_resi'] == 1 )? true : false,
    'SHOW_CMC_NATURE'	=> ( $conf_plus['pk_show_nature_resi'] == 1 )? true : false,
    'SHOW_CMC_ICE'		=> ( $conf_plus['pk_show_ice_resi'] == 1 )? true : false,
    'SHOW_CMC_SHADOW'	=> ( $conf_plus['pk_show_shadow_resi'] == 1 )? true : false,
    'SHOW_CMC_PROFILES'	=> ( $conf_plus['pk_show_profiles'] == 1 )? true : false,

    'L_FILTER'        => $user->lang['filter'],
    'L_NAME'          => $user->lang['name'],
    'L_RANK'          => $user->lang['rank'],
    'L_LEVEL'         => $user->lang['level'],
    'L_CLASS'         => $user->lang['class'],
    'L_ARMOR'         => $user->lang['armor'],
    'L_EARNED'        => $user->lang['earned'],
    'L_SPENT'         => $user->lang['spent'],
    'L_ADJUSTMENT'    => $user->lang['adjustment'],
    'L_CURRENT'       => $user->lang['current'],
    'L_LASTRAID'      => $user->lang['lastraid'],
    'L_LASTLOOT'      => $user->lang['lastloot'],
    'L_RAIDS_30_DAYS' => sprintf($user->lang['raids_x_days'], 30),
    'L_RAIDS_60_DAYS' => sprintf($user->lang['raids_x_days'], 60),
    'L_RAIDS_90_DAYS' => sprintf($user->lang['raids_x_days'], 90),
    'L_RAIDS_ALL' => 'Lifetime',

    'L_SKILL'					=> $user->lang['uc_tab_skills'],
    'L_ARCANE'				=> $user->lang['uc_res_arcane'],
    'L_FIRE'					=> $user->lang['uc_res_fire'],
    'L_NATURE'				=> $user->lang['uc_res_nature'],
    'L_FROST'					=> $user->lang['uc_res_frost'],
    'L_SHADOW'				=> $user->lang['uc_res_shadow'],
    'L_PROFILE'				=> $user->lang['uc_ext_profile'],

    'L_BUFFED'												=> $user->lang['uc_buffed'],
    'L_ALLAKHAZAM'										=> $user->lang['uc_allakhazam'],
    'L_CTPROFILES'										=> $user->lang['uc_ctprofiles'],
    'L_CURSEPROFILES'									=> $user->lang['uc_curse_profiler'],
    'L_TALENTPLANER'									=> $user->lang['uc_talentplaner'],

	'L_ARMORY_LINK1' => $user->lang['lm_armorylink1'],
    'L_ARMORY_LINK2' => $user->lang['lm_armorylink2'],
    'L_ARMORY_LINK3' => $user->lang['lm_armorylink3'],

    'BUTTON_NAME'  => 'submit',
    'BUTTON_VALUE' => $user->lang['compare_members'],
    'O_NAME'       => $current_order['uri'][0],
    'O_RANK'       => $current_order['uri'][8],
    'O_LEVEL'      => $current_order['uri'][6],
    'O_CLASS'      => $current_order['uri'][7],
    'O_ARMOR'      => $current_order['uri'][9],
    'O_EARNED'     => $current_order['uri'][1],
    'O_SPENT'      => $current_order['uri'][2],
    'O_ADJUSTMENT' => $current_order['uri'][3],
    'O_CURRENT'    => $current_order['uri'][4],
    'O_LASTRAID'   => $current_order['uri'][5],
    'O_LASTLOOT'   => $current_order['uri'][10],

    'O_FROST'      => $current_order['uri'][11],
    'O_ARCANE'     => $current_order['uri'][12],
    'O_SHADOW'     => $current_order['uri'][13],
    'O_NATURE'     => $current_order['uri'][14],
    'O_FIRE'       => $current_order['uri'][15],
 		
    'URI_ADDON'    => $uri_addon,
    'PAGE_HASH'		 => $cur_hash,

    'GIVEN_MULTIFILTER' => $multifilter ,
    'GIVEN_FILTER' => $filter ,
    'U_LIST_MEMBERS' => 'listmembers.php' . $SID . '&amp;',

    'S_NOTMM'   => true,
    'COLSPAN' => $collspan ,
    'LEADERBOARD_2ROW' => ( $conf_plus['pk_leaderboard_2row'] == 1 )? true : false,

   'LISTMEMBERS_FOOTCOUNT' => ( isset($_GET['compare']) ) ? sprintf($footcount_text, sizeof(explode(',', $compare_ids))) : $footcount_text)
);

if(!$pm->check(PLUGIN_INSTALLED, 'charmanager'))
		{
				$tpl->assign_vars(array(
			'SHOW_CMC_PROFILES'	=> false,
	    'SHOW_CMC_SKILL'	=> false,
	    'SHOW_CMC_ARKAN'	=> false,
	    'SHOW_CMC_FIRE'		=> false,
	    'SHOW_CMC_NATURE'	=> false,
	    'SHOW_CMC_ICE'		=> false,
	    'SHOW_CMC_SHADOW'	=> false
	    ));
		}


$tpl->assign_vars(array(
        'U_COMPARE_MEMBERS' => 'listmembers.php' . $SID . '&amp;')
    );
    
    saveXML($memberArray2xml,$members_rows_multidkp,$multi_name);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listmembers_title'],
    'template_file' => 'listmembers.html',
    'display'       => true)
);

function saveXML($memberArray2xml,$members_rows_multidkp,$multi_name)
{
	global $pcache,$eqdkp,$eqdkp_root_path ;
	
	// RSS Feed
	include_once($eqdkp_root_path."libraries/UniversalFeedCreator/UniversalFeedCreator.class.php");
	$rss = new UniversalFeedCreator();
	
	$rss->title           = "EQdkp-Plus Members";
	$rss->description     = $eqdkp->config['main_title']." EQdkp-Plus - Members" ;
	$rss->link            = $pcache->BuildLink().'listmember.php';

	//Pool names
	if (is_array($multi_name)) 
	{
		$i=1;
		$ads = array();
		foreach ($multi_name as $val)		
		{
			$ads['pool_'.$i] = 	$val['name'];
			$i++;
		}
		$rss->additionalElements = $ads;
	}
		
	//member
	if (count($memberArray2xml)>0) 
	{
		foreach ($memberArray2xml as $value)
		{		
			$additionalElements = array();	
			$rssitem = new FeedItem();
			$rssitem->title        = $value['name'] ;
			$rssitem->link         = $value['url'];
			$rssitem->description  = $value['class'];	
			$rss->addItem($rssitem);
			
			//Member Infos additional Elements
			if (is_array($value)) 
			{
				$additionalElementsMember = array();
				foreach ($value as $val => $key)
				{
					$additionalElementsMember[$val] = $key; 
				}							
			}

			//Member DKP Pools additional Elements
			if (is_array($members_rows_multidkp[$value[id]])) 
			{
				$$additionalElementsPools = array();
				$i=1;
				foreach ($members_rows_multidkp[$value[id]] as $_val)
				{					
					$additionalElementsPools['pool_'.$i.'_earned'] = $_val[earned];
					$additionalElementsPools['pool_'.$i.'_spend'] = $_val[spend];
					$additionalElementsPools['pool_'.$i.'_adjust'] = $_val[adjust];
					$additionalElementsPools['pool_'.$i.'_current'] = $_val[current];
					$i++;						
				}
				
			}						
			$additionalElements = (is_array($additionalElementsPools)) ? array_merge($additionalElementsMember,$additionalElementsPools) : $additionalElementsMember ;
			$rssitem->additionalElements = $additionalElements;		
		}			
	}
			
	$rss->saveFeed("RSS2.0", $pcache->FilePath('member.xml', 'eqdkp'),false);	
}

function member_display(&$row)
{
    global $eqdkp;
    global $query_by_armor, $query_by_class, $filter, $filters, $show_all, $id, $query_by_pool;
		
    // Replace space with underscore (for array indices)
    // Damn you Shadow Knights!
    $d_filter = ucwords(str_replace('_', ' ', $filter));
    $d_filter = str_replace(' ', '_', $d_filter);

    $member_display = null;

    // We're filtering based on class

    if ( $filter != 'none'  )
    {

       if ( $query_by_class == 1  )
       {
					// Check for valid level ranges
         $member_display = ( ($row['member_class'] == $id && $row['member_status'] != '0') ) ? true : false;
       }
       elseif ( $query_by_armor == 1 )
       {
	 	     $rows = strtolower($row['armor_type']);
  	     // Check for valid level ranges
	       if ( $row['member_level'] > $row['min_level'] && $row['member_level'] <= $row['max_level'] && $row['member_status'] != '0' )
	       {
             $member_display = ( $rows == $id  ) ? true : false;

	   		 }
       }
       elseif ($query_by_pool == 1)
       {
       	$member_display = true ;
       }
     }
     else
     {
       // Are we showing all?
       if ( $show_all )
       {
           $member_display = true;
       }
       else
       {
          // Are we hiding inactive members?
          if ( $eqdkp->config['hide_inactive'] == '0' )
          {
        	   //Are we hiding their rank?
             $member_display = ( $row['rank_hide'] == '0' ) ? true : false;
          }
          else
          {
          	 // Are they active?
             if ( $row['member_status'] == '0' )
             {
             	$member_display = false;
             }
             else
             {
             	$member_display = ( $row['rank_hide'] == '0' ) ? true : false;
             } // Member inactive
           } // Not showing inactive members
       } // Not showing all
      } // Not filtering by class

    return $member_display;
}


function validateCompareInput($input)
{
    // Remove codes from the list, like "%20"
    $retval = urldecode($input);

    // Remove anything that's not a comma or alpha-numeric
    $retval = preg_replace('#[^A-Za-z0-9\,]#', '', $retval);

    // Remove any extra commas as a result of removing bogus entries above
    $retval = str_replace(',,', ',', $retval);

    // Remove a trailing blank entry
    $retval = preg_replace('#,$#', '', $retval);

    return $retval;
}

// Assure $_GET['rank'] is one of our ranks
function validateRank($rank)
{
	global $db;
	$retval = false;

	$sql = "SELECT rank_id, rank_name
			FROM " . MEMBER_RANKS_TABLE;
	$result = $db->query($sql);

	while ( $row = $db->fetch_record($result) )
	{
		if ( $row['rank_id'] == $rank || $row['rank_name'] == $rank )
		{
			$retval = true;
		}
	}
	$db->free_result($result);

	return $retval;
}

?>