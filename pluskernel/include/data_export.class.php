<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       18 October 2009
 * Date:        $Date: 2009-11-10 14:44:38 +0100 (Tue, 10 Nov 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 6390 $
 *
 * $Id: exchange.php 6390 2009-11-10 13:44:38Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class content_export
{
	var $version				= "2.65";
	var $startString		= "\n--[START]\n";
	var $endString			= "\n--[END]\n";
	var $total_points		= 0;
	var $total_items		= 0;
	var $total_players	= 0;
	var $param_tab			= "\t";
	var $param_crlf			= "\r\n";
	var $data_out				= "";
	
	public function __construct(){
		$this->timestamp			= time();
		$this->date_created		= date("d.m.y G:i:s");
	}
	
	public function export(){
		global $db, $pm, $conf_plus, $dkpplus, $eqdkp_root_path;
	
		if ($conf_plus['pk_getdkp_active'] == 1){
			$this->data_out .= "-- Getdkp deactive";
			return $this->data_out;
		}
		
		$this->data_out .= $this->startString ;
		if ( isset($_GET['debug']) ){
			$this->data_out .= "--DEBUGMODE--\n";
		}
		if ( !defined('EQDKP_INSTALLED') ){  
			$this->data_out .= $this->endString;
			message_die('EqDKP is not Install', '', __FILE__, __LINE__, $sql);
		}
		$this->data_out .= "--------------------------------------------------------\n";
		$this->data_out .= "----                   dkp_list.lua                 ----\n";
		$this->data_out .= "---- dkp_list.lua is generated from getdkp.php ".$this->version." ----\n";
		$this->data_out .= "---- created on ".$this->date_created."\n";
		$this->data_out .= "----       support http://www.eqdkp-plus.com        ----\n";
		$this->data_out .= "--------------------------------------------------------\n\n";
		if($conf_plus['pk_multidkp'] == 1){
		    $sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
		            FROM __multidkp
		            WHERE multidkp_name IS NOT NULL';
		    $total_multi = $db->query_first('SELECT count(*) FROM __multidkp WHERE multidkp_name IS NOT NULL');
		
				if ( !($multi_result = $db->query($sql)) ){
					$this->data_out .= $this->endString;
				  message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
				}
		
				$tableoutput = "multiTable = {\n";
		
				$count=0 ;
				while ( $multi = $db->fetch_record($multi_result) ){
					$count++;
					$sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname
				  							 FROM __multidkp2event WHERE multidkp2event_multi_id ='.$multi['multidkp_id'] ;
		
					if ( !($multi2event_results = $db->query($sql_events)) ){
						$this->data_out .= $this->endString;
						message_die('Could not obta in MultiDKP -> Event information', '', __FILE__, __LINE__, $sql_events);
					}
		
					$tableoutput .= "	   [$count]= { [\"".$this->strto_wowutf($multi['multidkp_name'])."\"] = { \n";
					$tableoutput .= " \t\t    [\"name\"] = \"".$this->strto_wowutf($multi['multidkp_name'])."\",  \n";
					$tableoutput .= " \t\t    [\"disc\"] = \"".$this->strto_wowutf($multi['multidkp_disc'])."\",  \n";
		
					$multi2event = '' ;
					// go through all events of an account
					while ( $a_multi = $db->fetch_record($multi2event_results) ){ 
						$multi2event .= $this->strto_wowutf($a_multi['multidkp2event_eventname']).' , ' ;
					}
		
					# remove comma on end of string
					$multi2event  = preg_replace('# \, $#', '', $multi2event);
					$tableoutput .= " \t\t    [\"events\"] = \"$multi2event\" \n \t\t\t }, \n\t\t  },  \n";
		
				}
				$tableoutput .= "  }  \n";
				$this->data_out .= $tableoutput ;
			}else{
				$tableoutput = "multiTable = {\n";
				$tableoutput .= "	   [1]= { [\"dkp\"] = { \n";
			 	$tableoutput .= " \t\t    [\"name\"] = \"dkp\",  \n";
				$tableoutput .= " \t\t    [\"disc\"] = \"Raid DKP\",  \n";
				$tableoutput .= " \t\t    [\"events\"] = \" \" \n \t\t\t }, \n\t\t  },  \n";
				$tableoutput .= "  }  \n";
				$this->data_out .= $tableoutput ;
			}
		
		// Get raidpoints without spitting out the whole page
			if ( !($members_results = $db->query("SELECT * FROM __members"))){
				$this->data_out .= $this->endString;
				message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
			}
			while($row = $db->fetch_record($members_results)){
				$player_dkps = ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
				$this->total_points+=$player_dkps;
			}
			
			// Get total players
			$this->total_players		= $db->num_rows($members_results);
		
			// Get total items
			$items_results			= $db->query("SELECT * FROM __items");
			$this->total_items	= $db->num_rows($items_results);
		
			$this->data_out .= "DKPInfo = {\n";
			$this->data_out .= "     [\"date\"] = \"$this->date_created\",\n";
			$this->data_out .= "     [\"timestamp\"] = \"$this->timestamp\",\n";
			$this->data_out .= "     [\"process_dkp_ver\"] = \"$this->version\",\n";
			$this->data_out .= "     [\"total_players\"] = $this->total_players,\n";
			$this->data_out .= "     [\"total_items\"] = $this->total_items,\n";
			$this->data_out .= "     [\"total_points\"] = ".$this->_delimeter($this->total_points).",\n";
			$this->data_out .= "}\n";
		
			########################
			# Multi DKP / Normal DKP
			########################
			$sql = "SELECT *, m.member_name, c.class_name as member_class,
							m.member_earned + m.member_adjustment - m.member_spent as current_dkp
							FROM __members m, __classes c
							WHERE m.member_class_id = c.class_id
							GROUP BY m.member_name
							ORDER BY member_name ASC";
			
			if (!($result = $db->query($sql))){
				print $sql ;
				message_die('Could not obtain member DKP information', '', __FILE__, __LINE__, $sql);
			}
		
		if($conf_plus['pk_multidkp'] == 1){
			$format_start			= $this->param_tab.$this->param_tab.'["%s"] = {'.$this->param_crlf;
			$format_dkp				= $this->param_tab.$this->param_tab.$this->param_tab.'["DKP"] = %s,'.$this->param_crlf;
			$format_multidkp	= $this->param_tab.$this->param_tab.$this->param_tab.'["%s_earned"] = %s,'.$this->param_crlf.
													$this->param_tab.$this->param_tab.$this->param_tab.'["%s_spend"] = %s,'.$this->param_crlf.
													$this->param_tab.$this->param_tab.$this->param_tab.'["%s_adjust"] = %s,'.$this->param_crlf.
													$this->param_tab.$this->param_tab.$this->param_tab.'["%s_current"] = %s,'.$this->param_crlf ;
			$format_end				= $this->param_tab.$this->param_tab.$this->param_tab.'["class"] = "%s",'.$this->param_crlf.
													$this->param_tab.$this->param_tab.$this->param_tab.'["rcount"] = %s,'.$this->param_crlf.
													$this->param_tab.$this->param_tab.'},'.$this->param_crlf;
		
			if($db->num_rows($result) >= 1){
				$outputdkp  = 'gdkp = {'."\r\n";
				$outputdkp .=  $this->param_tab.'["players"] = {'.$this->param_crlf;
				
				//member row
				while ($row = $db->fetch_record($result)){
					$rc_sql = "SELECT count(*)
											FROM __raids r, __raid_attendees ra
											WHERE (ra.raid_id = r.raid_id)
											AND (ra.member_name='".$row['member_name']."')";
					if ( ($result_lifetime = $db->query($rc_sql)) ){
						$individual_raid_count_all = $db->query_first($rc_sql);
						$row['raid_count'] = $individual_raid_count_all ;
					}
		
					// search Alliases
					$al_found = 0;
					$al_sql = $db->query("SELECT * FROM __raidlogimport_aliases");
					if ( $al_sql){
						while($row9 = $db->fetch_record($al_sql)){
							if ($row9['alias_name'] == $row['member_name']){
								$al_found = 1;
							}
						}
					}
					if ($al_found == 0) {
						$outputdkp .= sprintf($format_start, $this->strto_wowutf($row['member_name']));
						$outputdkp .= sprintf($format_dkp,$row['current_dkp']);
						$member_multidkp = $dkpplus-> multiDkpMemberArray($row['member_name']) ; // create the multiDKP Table
			
						if(!empty($member_multidkp[$row['member_name']])){
							foreach ($member_multidkp[$row['member_name']] as $key){
								$outputdkp .= sprintf($format_multidkp, $key['name'] ,$key['earned'],
																			$key['name'], $key['spend'],
																			$key['name'], $key['adjust'],
																			$key['name'], $key['current'] );
							}
						}
						$outputdkp .= sprintf($format_end, $this->strto_wowutf($row['member_class']),$row['raid_count']);
					}
				}
				$outputdkp = substr($outputdkp, 0, strlen($outputdkp)-3);
				$this->data_out .= $outputdkp.$this->param_crlf.$this->param_tab.'}'.$this->param_crlf.'}'.$this->param_crlf;
			}
		}else{
			$format =
				$this->param_tab.$this->param_tab.'["%s"] = {'.$this->param_crlf.
		
				$this->param_tab.$this->param_tab.$this->param_tab.'["dkp_earned"] = %s,'.$this->param_crlf.
				$this->param_tab.$this->param_tab.$this->param_tab.'["dkp_spend"] = %s,'.$this->param_crlf.
				$this->param_tab.$this->param_tab.$this->param_tab.'["dkp_adjust"] = %s,'.$this->param_crlf.
				$this->param_tab.$this->param_tab.$this->param_tab.'["dkp_current"] = %s,'.$this->param_crlf.
		
				$this->param_tab.$this->param_tab.$this->param_tab.'["class"] = "%s",'.$this->param_crlf.
				$this->param_tab.$this->param_tab.$this->param_tab.'["rcount"] = %s,'.$this->param_crlf.
				$this->param_tab.$this->param_tab.'},'.$this->param_crlf;
		
			if($db->num_rows($result) >= 1){
				$outputdkp = 'gdkp = {'."\r\n";
				$outputdkp .=  $this->param_tab.'["players"] = {'.$this->param_crlf;
		
				while ($row = $db->fetch_record($result)){
					$rc_sql =	"SELECT count(*)
								     FROM __raids r, __raid_attendees ra
								     WHERE (ra.raid_id = r.raid_id)
								     AND (ra.member_name='".$row['member_name']."')";
		
					if ( ($result_lifetime = $db->query($rc_sql)) ){
		        $individual_raid_count_all = $db->query_first($rc_sql);
						$row['raid_count'] = $individual_raid_count_all ;
					}
					
					if ($al_found == 0){
						$outputdkp .= sprintf($format,
																	$this->strto_wowutf($row['member_name']),
																	runden($row['member_earned']),
																	runden($row['member_spent']),
																	runden($row['member_adjustment']),
																	runden($row['current_dkp']),
																	$this->strto_wowutf($row['member_class']),
																	$row['raid_count']
						);
					}
				}
				$outputdkp = substr($outputdkp, 0, strlen($outputdkp)-3);
				$this->data_out .= $outputdkp.$this->param_crlf.$this->param_tab.'}'.$this->param_crlf.'}'.$this->param_crlf;
			}
		}
		$db->free_result($result);
		
			//----------------------------------------------------------
			// DKP_ROLL_PLAYERS Output
			//----------------------------------------------------------
		
			$member_results = $db->query("SELECT * FROM __members");
			while($row = $db->fetch_record($member_results)){
				$player = strtolower($row['member_name']);
				$player_class_id = $row['member_class_id'];
				
				$class_results	= $db->query("SELECT * FROM __classes WHERE `class_id` =".$player_class_id);
				$class_row			= $db->fetch_record($class_results);
				$player_class		= $class_row['class_name'];
				
				$player_dkps		= ($row['member_earned'] - $row['member_spent']) + $row['member_adjustment'];
				$this->total_points		+= $player_dkps;
				$this->total_players++;
			}
			$item_results = $db->query("SELECT * FROM __items");
		
			while($item_row = $db->fetch_record($item_results)){
				$buyer			= ucfirst($item_row['item_buyer']);
				$item				= $item_row['item_name'];
				$item_value	= $item_row['item_value'];
				$player_data[$buyer]['Items'][$item] = $item_value;
				$this->total_items++;
			}
		
			if($player_data){
				$this->data_out .= "DKP_ITEMS = {\n" ;
				foreach ( $player_data as $player => $player_values ){
					$this->data_out .= "     [\"". $this->strto_wowutf($player) ."\"] = {\n";
					foreach ($player_values as $value_name => $value){
						if (is_array($value)){
							$this->data_out .= "          [\"$value_name\"] = {\n";
							$i = 0 ;
							foreach ($value as $item_name => $item_cost){
								$i++;
								$item_name = str_replace("?", "'", $item_name);
								$item_name = $this->strto_wowutf($item_name);
			
								$this->data_out .= "               [$i] = { [\"name\"] = \"$item_name\",\n";
			
								$this->data_out .= "               	       [\"dkp\"] = $item_cost }, \n";
							}
							$this->data_out .= "      		 },\n";
						}else{
							if (preg_match("/-?[.0-9]{1,7}/", $value)){
								$this->data_out .= "          [\"$value_name\"] = $value,\n";	// Is a number
							}else{
								$this->data_out .= "          [\"$value_name\"] = \"$value\",\n"; // Is a string
							}
						}
					}
					$this->data_out .= "     },\n";
				}
				$this->data_out .= "}\n\n"; // End of DKP_ROLL_PLAYERS
			}else{
				$this->data_out .= "-- No Items --\n\n";
			}
	
			###################################################
			#Items
			##################
			if ( $_GET['itemid'] != 1 and $conf_plus['pk_getdkp_itemids'] == 1) {
				$item_array = array();
				
				// Item Tables
				$item_results = $db->query("SELECT distinct(game_itemid),item_name FROM __items");
				while($item_row = $db->fetch_record($item_results)){
					if((strlen($item_row[game_itemid]) > 3) and (!isset($item_array[$item_row[game_itemid]]) )){
					  $item_array[$item_row[game_itemid]] = $this->strto_wowutf($item_row[item_name]) ;
					}
				}
				
				// Itemstats Tables
				$item_results = $db->query("SELECT distinct(item_name),item_id  FROM item_cache");
				if( !($item_results)){
					$this->data_out .= "-- No Item IDs --\n\n";
					$this->data_out .= $this->endString;
				}else{
					while($item_row = $db->fetch_record($item_results)){
						if((strlen($item_row[item_id]) > 3) and (!isset($item_array[$item_row[item_id]])) ){
						  $item_array[$item_row[item_id]] = $this->strto_wowutf($item_row[item_name]) ;
						}
					}
				
					$itemsoutput = "getdkp_itemids = nil \n";
					if(isset($item_array)){
						$itemsoutput = "getdkp_itemids = {\n";
						foreach ($item_array as $key => $value){
							if (is_numeric($key)){	
								$itemsoutput .= "	\t   [\"".$value."\"]= ".$key." , \n";
							}
						}
						$itemsoutput .= "}\n\n";
					}
					$this->data_out .= $itemsoutput;
				}
			}else{
				$this->data_out .="-- ItemId deactive\n\n";
			}
		
			#### RaidLogImport Alias
			#################################################
			if ($pm->check(PLUGIN_INSTALLED, 'raidlogimport')){
				$rlipfile = $eqdkp_root_path . 'plugins/raidlogimport/includes/functions.php';
				if (file_exists($rlipfile)) {
					include_once($rlipfile);			
					$rp_alias_array = array();
					foreach(rli_get_aliases() as $alias => $memname){
		 				$rp_alias_array[$memname][] = $alias;
		 			}		
				}
		
				if (count($rp_alias_array) >0){
					$alias_array = $rp_alias_array;
				}
					
				if (!count($alias_array) > 0  ) {
					$this->data_out .= "-- No Alliases --\n\n";
				}else {
					$format_start 	= $this->param_tab.$this->param_tab.'["%s"] = {'.$this->param_crlf;
					$format_member	= $this->param_tab.$this->param_tab.$this->param_tab.'[%s] = "%s",'.$this->param_crlf ;
				
					if (!isset($alias_array)){
						$aliasoutput = "gdkp_alliases = nil \n\n";
					}else{
						$aliasoutput = 'gdkp_alliases = {'."\r\n";
						foreach ($alias_array as $key => $value){
							//membername
							$aliasoutput .= sprintf($format_start, $this->strto_wowutf($key));
				
								//Aliasse
								foreach ($value as $_key => $_value){
									$aliasoutput .= sprintf($format_member, $_key+1, $this->strto_wowutf($_value));
								}
								$aliasoutput .= $this->param_tab.$this->param_tab.$this->param_tab.'},'.$this->param_crlf;
						}
						$aliasoutput .= $this->param_tab.$this->param_tab.'}'."\r\n".$this->param_crlf;
					}
					$this->data_out .= $aliasoutput;
				}
			}else{
				$this->data_out .="-- Alliasses Data deactive\n\n";
			}
			#End RaidLogImport
			################################
			
			####Raidplaner
			################################
			if ( $_GET['raidplaner'] != 1 and $conf_plus['pk_getdkp_rp'] == 1) {
				if(!$pm->check(PLUGIN_INSTALLED, 'raidplan')){
					$this->data_out .= "-- The EQDKP Plugin RaidPlaner is not installed --\n\n";
					$this->data_out .= $this->endString	;
					return $this->data_out;
				}
				
				$settings_result = $db->query('SELECT * FROM __raidplan_config');
				while($roww = $db->fetch_record($settings_result)){
					$eqdkp_getdkp_rpconf[$roww['config_name']] = $roww['config_value'];
				}
				
				// event icons..
				$myeventicons = array();
				$event_results = $db->query('SELECT event_icon, event_name FROM __events WHERE event_name = "'.$row['raid_name'].'"');
				while($row_events = $db->fetch_record($riadplaner_raids_results)){
					$myeventicons[$row_events['event_name']] = $row_events['event_icon'];
				}
				$riadplaner_raids_results = $db->query("SELECT * FROM __raidplan_raids WHERE raid_date >".$this->timestamp);
			
				if (!($row = $db->fetch_record($riadplaner_raids_results))){
					$this->data_out .= "-- no Raids found --\n\n";
					$this->data_out .= $this->endString	;
					return $this->data_out;
				}
				$riadplaner_raids_results = $db->query("SELECT * FROM __raidplan_raids WHERE raid_date >".$this->timestamp);
				$raidplaneroutput = 'GetDKPRaidPlaner = {'."\r\n";
				$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab. '["raid"] = {'."\r\n";
				$format_raid = $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'["%s"] = {'.$this->param_crlf;
			
				while($row = $db->fetch_record($riadplaner_raids_results)){
					$raidplaner_raids_icons = $this->strto_eqdkp_icon($myeventicons[$row['raid_name']]);
					$raidplaneroutput .= sprintf($format_raid, $row['raid_date']);
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_name"] = "'.$this->strto_wowutf($row['raid_name']).'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_icon"] = "'.$raidplaner_raids_icons.'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_added_by"] = "'.$this->strto_wowutf($row['raid_added_by']).'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_date"] = '.$row['raid_date'].',--'.date('d.F.Y-G:H',$row['raid_date'])."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_date_invite"] = '.$row['raid_date_invite'].','."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_date_subscription"] = '.$row['raid_date_subscription'].','."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_date_added"] = '.$row['raid_date_added'].','."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_date_finish"] = '.$row['raid_date_finish'].','."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_value"] = "'.$row['raid_value'].'",'."\n";
					$note = $this->strto_wowutf($row['raid_note']);
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_note"] = "'. $this->strip_spezial($note) .'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_raidleader"] = "'.$this->strto_wowutf($this->getMemberNamebyID($row['raid_leader'])).'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_attendees"] = "'.$row['raid_attendees'].'",'."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_distribution"] = '.$row['raid_distribution'].','."\n";
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_members"] = {'."\r\n";
			
			
					$eqdkp_members_results = $db->query("SELECT member_id,member_name FROM __members");
					while ($row3 = $db->fetch_record($eqdkp_members_results)){
						$raidplaner_members_results = $db->query("SELECT * FROM __raidplan_raid_attendees WHERE member_id = ".$row3['member_id']." AND raid_id = ".$row['raid_id']);
						$raidplaner_members_additions_result = $db->query("SELECT skill_1,skill_2,skill_3 FROM __member_additions WHERE member_id = ".$row3['member_id']);
						if (!($row2 = $db->fetch_record($raidplaner_members_results))){
							$raidplaner_user_result = $db->query("SELECT * FROM __member_user WHERE member_id = ".$row3['member_id']);
							if (($row4 = $db->fetch_record($raidplaner_user_result))){
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["player"] = "'.$this->strto_wowutf($this->getMemberNamebyID($row3['member_id'])).'",'."\n";
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class"] = "'.$this->strto_wowutf($this->getMemberClassbyID($row3['member_id'])).'",'."\n";
								if (($row5 = $db->fetch_record($raidplaner_members_additions_result))){
									$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_1"] = '.$row5['skill_1'].','."\n";
									$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_2"] = '.$row5['skill_2'].','."\n";
									$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_3"] = '.$row5['skill_3'].','."\n";
								}
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["subscribed"] = 5 ,'."\n";
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["note"] = "",'."\n";
								if ($row2['role']){
									$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["role"] = "'.$row2['role'].'",'."\n";
								}
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
							}
						}else{
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["player"] = "'.$this->strto_wowutf($this->getMemberNamebyID($row2['member_id'])).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class"] = "'.$this->strto_wowutf($this->getMemberClassbyID($row2['member_id'])).'",'."\n";
							if (($row5 = $db->fetch_record($raidplaner_members_additions_result))){
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_1"] = '.$row5['skill_1'].','."\n";
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_2"] = '.$row5['skill_2'].','."\n";
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["skill_3"] = '.$row5['skill_3'].','."\n";
							}
							$subscribed = $row2['attendees_subscribed'] + 1;
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["subscribed"] = '.$subscribed.','."\n";
							$note = $this->strto_wowutf($row2['attendees_note']);
							
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["note"] = "'.$this->strip_spezial($note).'",'."\n";
							if ($row2['role']){
								$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["role"] = "'.$row2['role'].'",'."\n";
							}
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
					}
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
					if ($row['raid_distribution'] == 0 ){
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_classes"] = {'."\r\n";
						$eqdkp_classes_results = $db->query(" SELECT * FROM __raidplan_raid_classes WHERE raid_id = ".$row['raid_id']);
						while ($row3 = $db->fetch_record($eqdkp_classes_results)){
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($row3['class_name']).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = '.$this->strto_wowutf($row3['class_count']).','."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
					}elseif ($row['raid_distribution'] == 1){
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_classes"] = {'."\r\n";
						$eqdkp_classes_results = $db->query(" SELECT * FROM __raidplan_raid_classes WHERE raid_id = ".$row['raid_id']);
						while ($row3 = $db->fetch_record($eqdkp_classes_results)){
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($row3['class_name']).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = '.$this->strto_wowutf($row3['class_count']).','."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_classes_role"] = {'."\r\n";
						$eqdkp_getdkp_role_healer = explode("|", $eqdkp_getdkp_rpconf['rp_healer']);
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
								
						for ($i=0; $i<count($eqdkp_getdkp_role_healer); $i++) {
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($this->getClassbyID($eqdkp_getdkp_role_healer[$i])).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
								
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'},'."\r\n";
						$eqdkp_getdkp_role_tank = explode("|", $eqdkp_getdkp_rpconf['rp_tank']);
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_tank); $i++) {
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($this->getClassbyID($eqdkp_getdkp_role_tank[$i])).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'},'."\r\n";
						$eqdkp_getdkp_role_dd_meele = explode("|", $eqdkp_getdkp_rpconf['rp_dd_meele']);
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_dd_meele); $i++) {
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($this->getClassbyID($eqdkp_getdkp_role_dd_meele[$i])).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'},'."\r\n";
						$eqdkp_getdkp_role_dd_range = explode("|", $eqdkp_getdkp_rpconf['rp_dd_range']);
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						for ($i=0; $i<count($eqdkp_getdkp_role_dd_range); $i++) {
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "'.$this->strto_wowutf($this->getClassbyID($eqdkp_getdkp_role_dd_range[$i])).'",'."\n";
							$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						}
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.'},'."\r\n";
					}elseif ($row['raid_distribution'] == 2){
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["raid_classes"] = {'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Druid",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Warlock",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Hunter",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Warrior",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Mage",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Paladin",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Priest",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Shaman",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '{'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_name"] = "Rogue",'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '["class_count"] = 0 ,'."\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
						$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
					}
					$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
				}
			
				$raidplaneroutput .= $this->param_tab.$this->param_tab.$this->param_tab. '},'."\r\n";
				$raidplaneroutput .= '}'."\r\n\n";
				$this->data_out .= $raidplaneroutput;
			}else{
				$this->data_out .= "-- RaidPlaner Data deactive\n\n";
			}
			#End Raidplaner
			################################
		
		$this->data_out .= $this->endString;
		return $this->data_out;
	}
	
	############# ######################################
	############# ######################################
	private function getMemberNamebyID($id){
		global $db;
		$member = $db->query_first("SELECT member_name FROM __members WHERE member_id=".$id);
		return (strlen($member)>1) ? $member : "";
	}
		
	private function getMemberClassbyID($id){
		global $db;
		$class = $db->query_first("SELECT member_class_id FROM __members WHERE member_id=".$id);
		return $db->query_first("SELECT class_name FROM __classes WHERE class_id=".$class);
	}
	
	private function getClassbyID($id){
		global $db;
		return $db->query_first("SELECT class_name FROM __classes WHERE class_id=".$id);
	}
	
	private function strto_wowutf($str){
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
		$replace[]						= '';
		return str_replace($find, $replace , $str);
	}
	
	private function _delimeter($val){
		return number_format ( $val, 2, '.', '' );
	}
	
	private function strto_eqdkp_icon($str){
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
		return str_replace($find, $replace , $str);
	}
	
	private function strip_spezial($str){
		for ($i = 1; $i <= 31; $i++){
			$find[] = chr($i);
			if ($i == 13){
				$replace[] = '/r/n';
			}else{
				$replace[] = '';
			}
		}
		return str_replace($find, $replace , $str);
	}
}
?>