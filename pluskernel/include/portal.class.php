<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
  * http://www.eqdkp-plus.com
 * ----------------------------
 * portal.class.php
 * Start: 2008
 * $Id: portal.class.php 1797 2008-03-24 22:44:56Z osr-corgan $
  ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');exit;
}
class portal
{

	function portal()
	{

		global $conf_plus, $pm , $eqdkp_root_path, $tpl,$eqdkp;

		//QuickDKP
		if (isset($conf_plus['pk_quickdkp']) && $conf_plus['pk_quickdkp'] == 1)
		{	$this->quickdkp();}

		//DKP Info
		if (!$conf_plus['pk_show_dkpinfo'])
		{	$this->dkpinfo();}

		//BossCounter
		if ( ($pm->check(PLUGIN_INSTALLED, 'bosssuite')) && ($eqdkp->config['bs_showBC']) )
		{
		   include_once($eqdkp_root_path . 'plugins/bosssuite/mods/bosscounter.php');
		}

		#Recruitment
		if ($conf_plus['pk_recruitment_active']==1)
		{
			$this->createRecruitmentTable();
		}

		//NextRaids und lastitems deactivate until the release of Eqdkp Plus 0.6!
		//next Raids
		if(!$conf_plus['pk_nextraids_deactive']==1)
		{
			$this->getNextRaids();
		}

		//last items
		if (!$conf_plus['pk_last_items_deactive']==1)
		{
			$this->createLastItems();
		}

		//last Raids
		if (!$conf_plus['pk_last_raid_deactive']==1)
		{
			$this->createLastRaids();
		}

		//Teamspeak
		if ($conf_plus['pk_ts_active']==1)
		{
			$this->createTSViewer();
		}

		//Teamspeak
		if ($conf_plus['pk_ts_ranking']==1)
		{
			$this->createRankIMG();
		}

		require_once($eqdkp_root_path . 'pluskernel/include/siteDisplay.class.php');
		$siteDisplay = new siteDisplay();



	}

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
			$quickdkp  = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">';
			$quickdkp  .='<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>';
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

							$member_multidkp = $dkpplus-> multiDkpMemberArray($row[member_name]) ; // create the multiDKP Table
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
				$quickdkp  = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">';
				$quickdkp  .='<tr><th class="smalltitle" align="center" colspan="2">'.$user->lang['Points_header'].'</th></tr>
							 <tr><td class="row1">'.$user->lang['Points_CHAR'].'</td></tr>';
			}

			$quickdkp  .='</table>';


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

		$DKPInfo = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
					<tr><th colspan=2 class="smalltitle" align="center">DKP Infos</th></tr>
					<tr><td class="row1">'.$user->lang['bosscount_raids'].'</td><td class="row1">'. $a_dkpinfo['raids']. '</td></tr>
					<tr><td class="row2">'.$user->lang['bosscount_player'].'</td><td class="row2">'. $a_dkpinfo['member']. '</td></tr>
					<tr><td class="row1">'.$user->lang['bosscount_items'].'</td><td class="row1">'. $a_dkpinfo['items']. '</td></tr>
					</table>
					';

		$tpl->assign_var('DKP_INFO',$DKPInfo);

		return $a_dkpinfo ;

	}#end dkpinfo


	/**
	 * Create the TPL Var {NEXT_RAIDS}
	 *
	 * @return void
	 */
	function getNextRaids()
	{
		global $db, $eqdkp_root_path, $user, $tpl, $pm, $table_prefix, $eqdkp,$user,$conf_plus;

		$total_recent_raids = $total_raids = 0;

		if(!$pm->check(PLUGIN_INSTALLED, 'raidplan'))
		{
			return nil;
		}

		//looking for next Raids
		$sql = "SELECT * FROM `".$table_prefix."raidplan_raids` WHERE raid_date > ".time()."  ORDER BY `raid_date`";

		if ($conf_plus['pk_nextraids_limit'] > 0) {
		 $sql .= ' LIMIT '.$conf_plus['pk_nextraids_limit']	;
		}elseif ($conf_plus['pk_nextraids_limit'] == 0 ){
		 $sql .= ' LIMIT 3'	;
		}

		$result = $db->query($sql);
		$raidcount = $db->num_rows($result);

		//Do the rest only if we have at least one next raid
		if ($raidcount > 0)
		{
			//Get all MemberIDs from the active User
			if ( $user->data['user_id'] != ANONYMOUS )
			{
				$sql2 = 'SELECT member_id
						FROM ' . MEMBER_USER_TABLE . '
						WHERE user_id = '. $user->data['user_id'] .'';

		 		$result2 = $db->query($sql2);
		 		$member_ids = array();

		 		//get all memberIDs
				while ( $row2 = $db->fetch_record($result2) )
				{
					$member_ids[] = $row2[member_id]  ;
				}
			}

			//Table Header
		  	$out = '<table width="100%" border="0" cellspacing="0" cellpadding="5" class="forumline">
  					<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['next_raids_head'].'</th></tr>';
		}else
		{
			return nil ;
		}

		//go though all given next raids
	   	while ( $row = $db->fetch_record($result) )
	   	{
			$own_status = false;

		  	// count the signed in members
		    $sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=1 AND raid_id=" . $row['raid_id'];
	        $count_signin = $db->query_first($sql);

	  		// count the confirmed members
	    	$sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=0 AND raid_id=" . $row['raid_id'];
	      	$count_confirmed = $db->query_first($sql);

	  		// count the signedout members
	    	$sql = "SELECT count(*) FROM ".$table_prefix."raidplan_raid_attendees WHERE attendees_subscribed=2 AND raid_id=" . $row['raid_id'];
	    	$count_signedout = $db->query_first($sql);

	  		// count the total sum
	    	$sql = "SELECT raid_attendees FROM `".$table_prefix."raidplan_raids` WHERE raid_id='".$row['raid_id']."'";
	    	$count_total = $db->query_first($sql);

	    	//Raid-Event-Icon
	    	$sql = "SELECT event_icon FROM `".$table_prefix."events` WHERE event_name='".$row['raid_name']."'";
			$icon = $db->query_first($sql);

			//looking for the attend of the signed on user
			if(is_array($member_ids))
			{
				//Beteiligung an RaidID des Members suchen
				$sql2 = "SELECT attendees_subscribed,attendees_note,attendees_signup_time FROM ".$table_prefix."raidplan_raid_attendees
							  WHERE raid_id=".$row['raid_id']."
							  AND member_id in ('".join_array("', '", $member_ids)."')";

				$result2 = $db->query($sql2);

				//found some raids
				$row2 = $db->fetch_record($result2);

				if($row2)
				{
					//only if the user has allready signed on
					if ($row2['attendees_signup_time'])
					{
						$own_status['status'] 	= $row2['attendees_subscribed'];
						$own_status['note'] 	= $row2['attendees_note'];
					}
				}
			}

			//calc
			$count_summ = $count_confirmed + $count_signin;
			$diffangemeldet = $count_total - $count_summ ;
			$diffgest = $count_total - $count_confirmed ;

			if(($count_affirmed > $count_summ) and  ($count_total - $count_affirmed >= 1))
    		{$diffangemeldet = $count_total - $count_affirmed ;}

    		$confirmstatus = '';

    		//Flags only for registered user
    		if (is_array($own_status))
    		{
	    		switch ($own_status['status'])
	    		{
		            case 0: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status0.gif />";break;
		            case 1: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status1.gif />";break;
		            case 2: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status2.gif />";break;
		            case 3: $confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status3.gif />";break;
	          		}
    		}elseif($user->data['user_id'] != ANONYMOUS)
    		{
    			#$confirmstatus = ' <img src='.$eqdkp_root_path."plugins/raidplan/images/status/status2.gif />";
    		}


			$out .= "<tr class=row1><td colspan=2><b>".strftime($user->style['strtime_date_short'], $row['raid_date']).$confirmstatus.
												  "</b></td></tr>";

				$out .= '<tr class="row2" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\'row2\';">'.
							"<td valign=top>
			  		   		<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=". stripslashes($row['raid_id'])."'>
			  		   		<img src=".$eqdkp_root_path."/games/".$eqdkp->config['default_game']."/events/".$icon."></a>
			  		   	</td>
			  			<td>
			  				<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=".
    								   stripslashes($row['raid_id'])."'>".
    								   stripslashes($row['raid_name']) ." (".$count_total.") </a>
			  				<br>";

    		if (is_array($own_status))
    		{
			  $out .= "<font class='positive'>".$user->lang['next_raids_signon'].": ".$count_summ."</font><br>" ;
			  $out .= "<font class='neutral'>".$user->lang['next_raids_confirmed'].": ".$count_confirmed."</font><br>" ;
			  $out .= "<font class='negative'>".$user->lang['next_raids_signoff'].": ".$count_signedout."</font><br>" ;
 			  $out .=  ($diffangemeldet > 0) ? "<font class='negative'><b>".$user->lang['next_raids_missing'].": ".$diffangemeldet."</b></font>" : '' ;
    		}elseif ($user->data['user_id'] != ANONYMOUS)
    		{
    			$out .= "<a href='".$eqdkp_root_path."plugins/raidplan/viewraid.php?r=". $row['raid_id']."'>".$user->lang['next_raids_notsigned']."</a>" ;
    		}

			  $out .= "</td></tr>";

			  $show = true ;

	   	}#end while

	   	if ($show) {
	   		$out .= "</table>" ;
	   	}else {
	   		$out = "";
	   	}

		$tpl->assign_var('NEXT_RAIDS',$out);

	}# end function

	function createLastItems()
	{
		global $eqdkp_root_path , $user, $eqdkp,$tpl,$conf_plus,$html;

		include_once($eqdkp_root_path.'pluskernel/bridge/bridge_class.php');
		$br = new eqdkp_bridge();
		$limit = ($conf_plus['pk_last_items_limit'] > 0) ? $conf_plus['pk_last_items_limit'] : '5' ;
		$lastitems = $br->get_last_items($limit);

		if (is_array($lastitems))
		{
			$out = '<table width="100%" border="0" cellspacing="0" cellpadding="5" class="forumline">
  						<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['last_items'].'</th></tr>';

			foreach ($lastitems as $item)
			{
				$class = $eqdkp->switch_row_class();
				$out .= '<tr class="'.$class.'" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$class.'\';">'.
							"<td>
								<a href='".$eqdkp_root_path."viewitem.php?i=". $item['id']."'>".$html->itemstats_item(stripslashes($item['name']),false,false)."</a> <br>".
								get_coloredLinkedName($item['looter']).' ('.$item['value'].' DKP)
							</td>
						</tr>';
			}
			$out .= '</table>';

			$tpl->assign_var('LAST_ITEMS',$out);
		}

	}

	function createLastRaids()
	{
		global $eqdkp_root_path , $user, $eqdkp,$tpl,$conf_plus,$html;

		include_once($eqdkp_root_path.'pluskernel/bridge/bridge_class.php');
		$br = new eqdkp_bridge();

		$limit = ($conf_plus['pk_last_raids_limit'] > 0)  ? $conf_plus['pk_last_raids_limit'] : 5 ;
		$lastraids= $br->get_last_Group_Raids($limit);

		if (is_array($lastraids))
		{
			$out = '<table width="100%" border="0" cellspacing="0" cellpadding="5" class="forumline">
  						<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['last_raids'].'</th></tr>';

			foreach ($lastraids as $raid)
			{
				//Items
				if (!$conf_plus['pk_set_lastraids_showloot'])
				{
					$item_icons = "";
					$loot_limit = ($conf_plus['pk_set_lastraids_lootLimit'] > 0) ? $conf_plus['pk_set_lastraids_lootLimit'] : 7 ;
					$raid_items = $br->get_last_items($loot_limit,$raid['raid_id']);
					if (is_array($raid_items))
					{
						foreach($raid_items as $item)
						{
							$item_icons .= $html->itemstats_item(stripslashes($item['name']),false,true);
						}
					}
				}

				$img = $eqdkp_root_path."games/".$eqdkp->config['default_game']."/events/".$raid['raid_icon'];
				$img = (file_exists($img)) ? "<img src=".$img.">" : "" ;

				$class = $eqdkp->switch_row_class();
				$out .= '<tr class="'.$class.'" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$class.'\';">'.
							"<td>
								<table>
									<tr>
										<td valign=top>
											<a href='".$eqdkp_root_path."viewraid.php?r=". $raid['raid_id']."'>
											".$img."</a>
											</a>
										</td>
										<td valign=top>
											<a href='".$eqdkp_root_path."viewraid.php?r=". $raid['raid_id']."'>
											".$raid['raid_name']."</a><br>".
											strftime($user->style['strtime_date_short'], $raid['raid_date']).
											"<p><span class=small> ".$raid['raid_note']."</span><br>".
											$item_icons.
										"</td>
									</tr>
								</table>
							</td>
						</tr>";
			}
			$out .= '</table>';

		}

		$tpl->assign_var('LAST_RAIDSV',$out);
	}

	function createItemsFromRaid($raidID, $showtype)
	{
		#global $itemstats
		#getItemForDisplay
	}


	/**
	 * Create a TPL VAR the shows the recruitment Table
	 *
	 */
	function createRecruitmentTable()
	{
		global $conf_plus,$db,$user,$tpl,$eqdkp,$user,$eqdkp_root_path,$html;

 	$sql = 'SELECT class_name , class_id
         	FROM '.CLASS_TABLE.' group by class_name ORDER BY class_name';

  		$result = $db->query($sql);

  		$recruit = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
  					<tr><th colspan=2 class="smalltitle" align="center">'.$user->lang['recruitment_open'].'</th></tr>';

	   	while ( $row = $db->fetch_record($result) )
	   	{


			if($eqdkp->config['default_game'] == 'WoW' and ($row['class_name'] <> 'Unknown' ))
			{
				$i = 0 ;
				$specs = $user->lang['talents'][renameClasstoenglish($row['class_name'])] ;
		   		foreach ($specs as $specname)
		   		{
		   			$i++;
		   			$classCount = $conf_plus['pk_recruitment_class['.$row['class_id'].']['.$i.']'] ;
			   		if ($classCount > 0)
			   	  	{
			   	  		$rowcolor = $eqdkp->switch_row_class();
			   	  		$c_color = renameClasstoenglish($row['class_name']);
  		 	   		    $img = $eqdkp_root_path."games/WoW/talents/".strtolower(renameClasstoenglish($row['class_name'])).($i-1).".png" ;
  		 	   		    $icon= "<img src='".$img."'>" ;
  		 	   		    $showntext = $html->ToolTip($specname.' - '.$row['class_name'],$icon.get_ClassIcon($row['class_name']).' '.$row['class_name'],$icon) ;
			   	  		$recruit .=
			   	  					'<tr class="'.$rowcolor.'" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$rowcolor.'\';">'.
			   	  		 			'<td class="'.$c_color.'">'.$showntext.'</td>
			   	  						 					   <td>'. $classCount. '</td>
			   	  					</tr>';
			   	  		$show =true ;
			   	  	}
		   		}
			}else
			{

		   		if ($conf_plus['pk_recruitment_class['.$row['class_id'].']'] > 0)
		   	  	{
		   	  		$rowcolor = $eqdkp->switch_row_class();
		   	  		$c_color = renameClasstoenglish($row['class_name']);
		   	  		$recruit .= '<tr class="'.$rowcolor.'"><td class="'.$c_color.'">'.get_ClassIcon($row['class_name']).' '.$row['class_name'].'</td>
		   	  						 					   <td>'. $conf_plus['pk_recruitment_class['.$row['class_id'].']']. '</td>
		   	  					</tr>';
		   	  		$show =true ;
		   	  	}
			}
	   	}

	   	if (strlen($conf_plus['pk_recruitment_url']) > 1) {
	   		$url = '<a href="'.$conf_plus['pk_recruitment_url'].'">' ;
	   	}else
	   	{
	   		$url = '<a href="mailto:'.$conf_plus['pk_contact_email'].'">';
	   	}

	   	$recruit .= '<tr class="'.$rowcolor.'"><td colspan=2 class="smalltitle" align="center">'.$url.$user->lang['recruitment_contact'].' </a></td></tr>';
	   	$recruit .= ' </table>';
	   	if ($show) {
			$tpl->assign_var('RECRUITMENT',$recruit);
	   	}

	}

	/**
	 * EQDKP PLUS Custom Links
	 * create the menu with the additional links
	 *
	 * @param boolean $activate
	 * @param String-Eqdkü Styöe $style
	 * @param String $root_path
	 * @return String
	 */
	function createLinkMenu($activate,$root_path)
	{
		global $tpl, $db, $eqdkp,$eqdkp_root_path;

		$main_menu3 = array();
		if ($activate == 1)
		{
			$tpl->assign_var('MENU3NAME', 'Links');

			// load the links from db
			$linkssql = 'SELECT link_id, link_name, link_url, link_window
     		 			 FROM '.PLUS_LINKS_TABLE.' ORDER BY link_id';
  			$plinks_result = $db->query($linkssql);

   			//output links
   			$b = 0;
			while ( $pluslinkrow = $db->fetch_record($plinks_result) )
   			{

   				$link = $pluslinkrow['link_url'];
   				$plus_target = '';

				// generate target
				if ($pluslinkrow['link_window'] == 1)
				{
					$plus_target = 'target="_blank"';
				}elseif ($pluslinkrow['link_window'] == 2) {
					$link = $eqdkp_root_path.'wrapper.php?id='.$pluslinkrow['link_id'];
				}


                $class = "row".($bi+1) ;
				$main_menu3['V'] .= '<tr class="'.$class.'" nowrap onmouseover="this.className=\'rowHover\';" onmouseout="this.className=\''.$class.'\';">
										<td nowrap>&nbsp;<img src="' .$root_path .'images/arrow.gif" alt="arrow"/> &nbsp;
											<a href="' . $link . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a>
										</td></tr>';


				$main_menu3['H'] .= '<a href="' . $pluslinkrow['link_url'] . '" class="copy" '.$plus_target.'>' . $pluslinkrow['link_name'] . '</a> | ';

			    $bi = 1-$bi;

			} // end while
		} // end on/off
		return $main_menu3;
	}

	function createTSViewer()
	{
		global $tpl, $eqdkp,$eqdkp_root_path,$conf_plus ,$user;

		include_once($eqdkp_root_path . 'pluskernel/include/TeamSpeakViewer/TS_Viewer.class.php');


		$tss2info 	= new tss2info;
		$tss2info->sitetitle = $conf_plus['pk_ts_title'];
		$tss2info->serverAddress = $conf_plus['pk_ts_serverAddress'];
		$tss2info->serverQueryPort = $conf_plus['pk_ts_serverQueryPort'];
		$tss2info->serverUDPPort = $conf_plus['pk_ts_serverUDPPort'];
		$tss2info->serverPasswort = $conf_plus['pk_ts_serverPasswort'];
		$tss2info->rpath = $eqdkp_root_path;

		$tss2info->TS_channelflags_ausgabe   = (isset($conf_plus['pk_ts_channelflags'])) ? $conf_plus['pk_ts_channelflags'] : 0 ;
		$tss2info->TS_userstatus_ausgabe     = (isset($conf_plus['pk_ts_userstatus'])) ? $conf_plus['pk_ts_userstatus'] : 0 ;
		$tss2info->TS_channel_anzeigen       = (isset($conf_plus['pk_ts_showchannel'])) ? $conf_plus['pk_ts_showchannel'] : 1 ;
		$tss2info->TS_leerchannel_anzeigen   = (isset($conf_plus['pk_ts_showEmptychannel'])) ? $conf_plus['pk_ts_showEmptychannel'] : 0 ;
		$tss2info->TS_overlib_mouseover      = (isset($conf_plus['pk_ts_overlib_mouseover'])) ? $conf_plus['pk_ts_overlib_mouseover'] : 0 ;

		if ( $user->data['user_id'] != ANONYMOUS )
		{
			$tss2info->alternativer_nick     = $user->data['username'];
			$tss2info->joinable				 = (isset($conf_plus['pk_ts_joinable'])) ? $conf_plus['pk_ts_joinable'] : 0 ;
		}else
		{
			if (!$conf_plus['pk_ts_joinableMember'] ==1)
			{
				$tss2info->joinable			= (isset($conf_plus['pk_ts_joinable'])) ? $conf_plus['pk_ts_joinable'] : 0 ;
			}
		}

		$htmlout 	= @$tss2info->getInfo();

		if (!isset($htmlout)) {
			$htmlout = $user->lang['voice_error'].
			"<br>".$conf_plus['pk_ts_serverAddress'].":".$conf_plus['pk_ts_serverQueryPort'];
		}
		$out .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">';
		$out .= '<tr><th class="smalltitle" align="center">Voice Server</th></tr>';
		$out .= '<tr class=row1><td>';
		$out .= $htmlout ;
		$out .= '</td></tr>';
		$out .= '</table>';

		$tpl->assign_var('TS_VIEWER',$out);

	}

	function createRankIMG()
	{
		global $tpl, $eqdkp,$eqdkp_root_path,$conf_plus ,$user;

		if (isset($conf_plus['pk_ts_ranking_url']) && isset($conf_plus['pk_ts_ranking_link']))
		{
			$out .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">';
			$out .= '<tr ><td align=center>';

			if(strlen($conf_plus['pk_ts_ranking_link'] > 0))
			{
				$out .=  '<a href="'.$conf_plus['pk_ts_ranking_link'].'" target=_blank> <img src="'.$conf_plus['pk_ts_ranking_url'].'"> </a>';
			}else
			{
				$out .=  '<img src="'.$conf_plus['pk_ts_ranking_url'].'">';
			}


			$out .= '</td></tr>';
			$out .= '</table>';
			$tpl->assign_var('RANK_IMG',$out);
		}


	}

}