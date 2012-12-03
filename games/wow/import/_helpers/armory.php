<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = './../../../../';
include_once ($eqdkp_root_path . 'common.php');

// init the Armory Loader
$armory = new ArmoryChars();
if(!$in->get('step')){
	$myServer = ($core->config['uc_servername']) ? stripslashes($core->config['uc_servername']) : '';
	if($in->get('member_id', 0) > 0){
		$tmpmemname = $pdh->get('member', 'name', array($in->get('member_id', 0)));
	}

	// generate output
	$hmtlout .= ' <form name="settings" method="post" action="armory.php?step=1">';
	$hmtlout .= $game->glang('uc_charname').' <input name="charname" size="15" maxlength="50" value="'.(($tmpmemname) ? $tmpmemname : '').'" class="input" type="text">';
	if($core->config['uc_lockserver'] == 1){
		$hmtlout .= ' @'.stripslashes($core->config['uc_servername']).'<br/>';
		$hmtlout .= $armory->genHiddenInput('servername',stripslashes($core->config['uc_servername']));
	}else{
		$hmtlout .= '<br/>'.$game->glang('uc_servername').' <input name="servername" size="15" maxlength="50" value="'.$temp_svname.'" class="input" type="text">';
	}	
	if($core->config['uc_server_loc']){
		$hmtlout .= $armory->genHiddenInput('server_loc', $core->config['uc_server_loc']);
	}else{
		$hmtlout .= "<br/>".$game->glang('uc_server_loc').$html->DropDown('server_loc', $armory->GetLocs(), '', '', '', 'input');
	}
	$hmtlout .= '<br/><input type="submit" name="submiti" value="'.$game->glang('uc_import_forw').'" class="mainoption" />';
	$hmtlout .= '</form>';
}elseif($in->get('step', 0) == '1'){
	$isindatabase = '0';
	if($in->get('member_id', 0) > 0){
		// We'll update an existing one...
		$isindatabase	= $in->get('member_id', 0);
		$isMemberName	= $pdh->get('member', 'name', array($in->get('member_id', 0)));
		$isServerName	= stripslashes($core->config['uc_servername']);
		$isServerLoc	= $core->config['uc_server_loc'];
	}else{
		// Check for existing member name
		$isindatabase	= $pdh->get('member', 'id', array($in->get('charname')));
		$isMemberName	= $_POST['charname'];
		$isServerName	= $_POST['servername'];
		$isServerLoc	= $_POST['server_loc'];
	}
	
	// Check if its mine! -- Security thing to prevent highjacking of chardata
	$is_mine = false;echo $pdh->get('connection', 'userid', array($isindatabase));
	if($user->check_auth('a_charmanager_config', false)){
		$is_mine	= true;
	}else{
		$is_mine	= ($pdh->get('connection', 'userid', array($isindatabase)) == $user->data['user_id']) ? true : false;
	}

	if($is_mine){
		// Load the Armory Data
		$armlanguage = ($user->lang['ISO_LANG_SHORT']) ? $user->lang['ISO_LANG_SHORT'] : 'en_EN';
		$chardata = $armory->GetCharacterData($isMemberName,stripslashes($isServerName),$isServerLoc, $armlanguage);
		$arm_data = $armory->BuildMemberArray($chardata[0]);
		$hmtlout = '<style type="text/css">
									p.info { border:1px solid red; background-color:#E0E0E0; padding:4px; margin:0px; }
								</style>'; 
		$hmtlout .= '<form name="settings" method="post" action="armory.php?step=2">';

		// Basics
		$character_data = $arm_data['character']['@attributes'];
		$hmtlout .= $armory->genHiddenInput('member_id',		$isindatabase);
		$hmtlout .= $armory->genHiddenInput('member_name',		$isMemberName);
		$hmtlout .= $armory->genHiddenInput('member_level',		$character_data['level']);
		$hmtlout .= $armory->genHiddenInput('gender',			(($character_data['genderId'] == 1) ? 'Female' : 'Male'));
		$hmtlout .= $armory->genHiddenInput('faction',			(($character_data['factionId'] == 1) ? 'Horde' : 'Alliance'));
		$hmtlout .= $armory->genHiddenInput('member_race_id',	(($arm_data['race_eqdkp']) ? $arm_data['race_eqdkp'] : 0));
		$hmtlout .= $armory->genHiddenInput('member_class_id',	(($arm_data['class_eqdkp']) ? $arm_data['class_eqdkp'] : 0));
		$hmtlout .= $armory->genHiddenInput('guild',			$character_data['guildName']);
		$hmtlout .= $armory->genHiddenInput('last_update',		$armory->Date2Timestamp($character_data['lastModified']));

		// Resistances
		$chartab_data = $arm_data['characterTab'];
		$hmtlout .= $armory->genHiddenInput('fire',				$chartab_data['resistances']['fire']['@attributes']['value']);
		$hmtlout .= $armory->genHiddenInput('nature',			$chartab_data['resistances']['nature']['@attributes']['value']);
		$hmtlout .= $armory->genHiddenInput('shadow',			$chartab_data['resistances']['shadow']['@attributes']['value']);
		$hmtlout .= $armory->genHiddenInput('arcane',			$chartab_data['resistances']['arcane']['@attributes']['value']);
		$hmtlout .= $armory->genHiddenInput('ice',				$chartab_data['resistances']['frost']['@attributes']['value']);

		// Bars
		$hmtlout .= $armory->genHiddenInput('health_bar',		$chartab_data['characterBars']['health']['@attributes']['effective']);
		$hmtlout .= $armory->genHiddenInput('second_bar',		$chartab_data['characterBars']['secondBar']['@attributes']['effective']);
		$hmtlout .= $armory->genHiddenInput('second_name',		$chartab_data['characterBars']['secondBar']['@attributes']['type']);

		$profarry = array();
		if(is_array($arm_data)){
			$iii=1;
			foreach($chartab_data['professions']['skill'] as $professions){
				$hmtlout .= $armory->genHiddenInput('prof'.$iii.'_value', $professions['@attributes']['value']);
				$hmtlout .= $armory->genHiddenInput('prof'.$iii.'_name', $professions['@attributes']['name']);
				$iii++;
			}

			// Skills
			$talents = $chartab_data['talentSpecs']['talentSpec'];
			$hmtlout .= $armory->genHiddenInput('skill_1',	$talents[0]['@attributes']['treeOne']);
			$hmtlout .= $armory->genHiddenInput('skill_2',	$talents[0]['@attributes']['treeTwo']);
			$hmtlout .= $armory->genHiddenInput('skill_3',	$talents[0]['@attributes']['treeThree']);
			$hmtlout .= $armory->genHiddenInput('skill2_1',	$talents[1]['@attributes']['treeOne']);
			$hmtlout .= $armory->genHiddenInput('skill2_2',	$talents[1]['@attributes']['treeTwo']);
			$hmtlout .= $armory->genHiddenInput('skill2_3',	$talents[1]['@attributes']['treeThree']);
		}else{
			$hmtlout .= $armory->genHiddenInput('prof1_value', '');
			$hmtlout .= $armory->genHiddenInput('prof1_name', '');
			$hmtlout .= $armory->genHiddenInput('prof2_value', '');
			$hmtlout .= $armory->genHiddenInput('prof2_name', '');
		}
		$hmtlout .= $armory->genHiddenInput('debug', $arm_data['race_eqdkp']);

		// viewable Output
		if(is_array($arm_data)){
			$hmtlout .= sprintf($game->glang('uc_charfound'), $isMemberName).'<br>';
			$hmtlout .= sprintf($game->glang('uc_charfound2'), date('d.m.Y', $armory->Date2Timestamp($character_data['lastModified'])));
			$hmtlout .= '<br/><p class="info">'.$game->glang('uc_charfound3').'</p>';
			if(!$isindatabase){
				if($user->check_auth('u_member_conn', false)){
					$hmtlout .= '<br/><input type="checkbox" name="overtakeuser" value="1" checked="checked" />';
				}else{
					$hmtlout .= '<br/><input type="checkbox" name="overtakeuser" value="1" disabled="disabled" checked="checked" />';
					$hmtlout .= $armory->genHiddenInput('overtakeuser','1');
				}
				$hmtlout .= ' '.$game->glang('overtake_char');
			}
			$hmtlout .= '<center><input type="submit" name="submiti" value="'.$game->glang('uc_prof_import').'" class="mainoption"></center>';
		}elseif($arm_data == 'old_char'){
			$hmtlout .= $game->glang('uc_notyetupdated');
		}else{
			$hmtlout .= $game->glang('uc_noprofile_found');
		}
		$hmtlout .= '</form>';
	}else{
		$hmtlout = '<style type="text/css">
									p.info { border:1px solid red; background-color:#E0E0E0; padding:4px; margin:0px; }
								</style>';
		$hmtlout .= '<br/><p class="info">'.$game->glang('uc_notyourchar').'</p>';
	}
}elseif($in->get('step',0) == '2'){
	// insert the Data
	$info		= $CharTools->updateChar($_POST['member_id'], $_POST['member_name'], '', true);
	$hmtlout	= ($info[0] == true) ? $game->glang('uc_upd_succ').' (ID: '.$_POST['member_id'].')' : $game->glang('uc_imp_failed').' (ID: '.$_POST['member_id'].')';
}
echo $hmtlout;
?>