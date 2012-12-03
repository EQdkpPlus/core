<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

// todo:
// - start points!
// - case insensitive in_array

	define('EQDKP_INC', true);
	$eqdkp_root_path = './../../../';
	include_once ($eqdkp_root_path . 'common.php');
	$user->check_auth('a_members_man');

	if(!$core->config['uc_servername'] or !$core->config['uc_server_loc']){
		echo $game->glang('uc_imp_novariables');
		die();
	}
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			</head>
			<body>';
	
	if(!$in->get('step')){
		// CSS
		echo "<style>
					.uc_infotext{
						margin:4px;
						padding: 4px;
						color: grey;
						border: 1px dotted grey;
						font-size: 13px;
					}
					.uc_headerload{
						font-size: 13px;
						text-align:center;
					}
					.uc_headerfinish{
						font-size: 14px;
						vertical-align: middle;
					}
					.uc_notetext{
						vertical-align: top;
						font-size: 14px;
						color: red;
						border: 1px dotted red;
						background: #FFEFEF;
						margin:4px;
						padding: 4px;
					}
					.uc_headtxt2{
						margin:4px;
						margin-bottom: 10px;
					}
					</style>";
		$output .= '<div id="uc_load_message">
							</div>
							<div id="uc_load_notice">
							</div>
								<iframe src="a_guild_import.php?step=1" width="100%" height="200px" name="uc_guildimport" frameborder=0 border=0 framespacing=0 scrolling="auto"></iframe>';
		echo $output;
	}elseif($in->get('step',0) == '1'){
		// Build the Class Array
		$output = ' <form name="settings" method="post" action="a_guild_import.php?step=2">';
		$output .= $game->glang('uc_guild_name').': <input name="guildname" size="30" maxlength="50" value="" class="input" type="text"><br/>';
		$output .= $game->glang('uc_class_filter').': '.$html->DropDown('uc_classid', $game->get('classes'), '', '', '', 'input').'<br/>';
		$output .= $game->glang('uc_level_filter').': <input name="level" size="2" maxlength="3" value="0" class="input" type="text"><br/>';
		$ranksortarry = array(1=>$game->glang('uc_rank_filter1a'), 2=>$game->glang('uc_rank_filter1b'));
		$output .= $game->glang('uc_rank_filter2a').' '.$html->DropDown('rank_sort', $ranksortarry, '1', '', '', 'input').' '.$game->glang('uc_rank_filter2b').': <input name="uc_rank" size="2" maxlength="3" value="0" class="input" type="text"><br/>';
		//$output .= $game->glang('uc_startdkp').': <input name="startdkp" size="5" maxlength="5" value="0" class="input" type="text"><br/>';
		$output .= '<input type="submit" name="submiti" value="'.$game->glang('uc_import_forw').'" class="mainoption" />';
		$output .= '</form>';
		echo $output;
	}elseif($in->get('step',0) == '2'){

		// set the import-start message
		$load_mssg		= '<div class="uc_headerload"><img src="../../../images/global/loading.gif" alt="loading..." /><div class="uc_headtxt2">'.$game->glang('uc_gimp_header_load').'</div></div>';
		$load_notice	= '<div class="uc_infotext">'.$game->glang('uc_gimp_infotxt').'</div>';
		echo "<script>parent.document.getElementById('uc_load_message').innerHTML='".$load_mssg."';</script>";
		echo "<script>parent.document.getElementById('uc_load_notice').innerHTML='".$load_notice."';</script>";

		include_once('classes/ArmoryChars.class.php');
		$armory = new ArmoryChars();

		// Read Member kist from Armory
		$myClassId	= ($in->get('uc_classid',0)) ? $armory->ConvertID($in->get('uc_classid',0), 'int', 'classes', true) : '';

		// Limit by Rank
		$myRankFilter = array('value' => $in->get('uc_rank',0), 'sort' => $in->get('rank_sort',0));

		// Error Reporting..
		if(!$_POST['guildname']){
			die($game->glang('uc_imp_noguildname'));
		}

		// Fetch the Data
		$armlanguage = ($user->lang['ISO_LANG_SHORT']) ? $user->lang['ISO_LANG_SHORT'] : 'en_EN';
		$xml = $armory->GetGuildMembers($_POST['guildname'],stripslashes($core->config['uc_servername']),$core->config['uc_server_loc'], $_POST['level'], $myClassId, $myRankFilter, $armlanguage);
		$myheadout = '<table width="400">';
		echo $myheadout;

		if(is_array($xml)){
			foreach($xml as $chars){
				if(in_array($chars['name'],$pdh->get('member', 'names', array()))){
					// member is in Database! Do not import again!
					$setstatus = '<span>'.$game->glang('uc_armory_impduplex').'</span>';
				}else{
					$dataarry = array(
						'member_level'		=> $armory->ValueOrNull($chars['level']),
						'member_class_id'	=> $chars['eqdkp_classid'],
						'member_race_id'	=> $chars['eqdkp_raceid'],
					);
					$myStatus = $CharTools->updateChar('', $chars['name'], $dataarry, true);
					if($myStatus[0]){
						$setstatus = '<span syle="color:green">'.$game->glang('uc_armory_imported').'</span>';
					}else{
						$setstatus = '<span style="color:red">'.$game->glang('uc_armory_impfailed').'</span>';
					}
					
					// Adjustment
					// we have to add a f... event first.. add a dropdown, and add a default event if no one is selected..
					//$pdh->put('adjustment', 'add_adjustment', array($_POST['startdkp'], $adj['reason'], $adj['members'], $adj['event']));
				}
				$output  = '<tr>';
				$output .= '<td width="200">'.$chars['name'].'</td>';
				$output .= '<td width="50">'.$chars['level'].'</td>';
				$output .= '<td width="150">'.$setstatus.'</td>';
				$output .= "</tr>";
				echo $output;
			}
		}
		echo "</table>";
		
		// Set the finish message...
		$load_mssg		= '<div class="uc_headerfinish"><img src="../../../images/ok.png" alt="finished" align="middle" />'.$game->glang('uc_gimp_header_fnsh').'</div>';
		$load_notice	= '<div class="uc_notetext" id="import_finished"><img src="../../../images/false.png" alt="finished" align="left" style="padding-right:3px;" />'.$game->glang('uc_gimp_finish_note').'</div>';
		echo "<script>parent.document.getElementById('uc_load_message').innerHTML='".$load_mssg."';</script>";
		echo "<script>parent.document.getElementById('uc_load_notice').innerHTML='".$load_notice."';</script>";
	}
	echo '</body></html>';
?>