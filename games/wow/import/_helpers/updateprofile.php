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
$eqdkp_root_path = './../../../../';
include_once ($eqdkp_root_path . 'common.php');

if(!$in->get('cron') == 'true'){
	$user->check_auth('a_members_man');
}
$armory = new ArmoryChars();

$members = $pdh->get('member', 'names', array());
if($in->get('actual',0) < $in->get('count',0)){
	$nextcount 		= $in->get('actual',0)+1;
	$actualcount	= $in->get('actual',0);
	//Load the Armory Data
	$armlanguage = ($user->lang['ISO_LANG_SHORT']) ? $user->lang['ISO_LANG_SHORT'] : 'en_EN';
	$xml_data = $armory->GetCharacterData($members[$actualcount],stripslashes($core->config['uc_servername']),$core->config['uc_server_loc'], $armlanguage);
	$arm_data = $armory->BuildMemberArray($xml_data[0]);
	
	// Show the Char icon
	$myOutput = '';
	if(is_array($arm_data)){
		echo "<script type='text/javascript'>
		function replace_img(){
			if (load_img.complete) {
				document['char_icon'].src=load_img.src;
				clearInterval(timerid);
			} 
		} 
		load_img = new Image();
		load_img.src = '{$arm_data['ac_charicon']}';
		timerid = setInterval('replace_img()', 500);
		</script>";

		$myOutput .= '<img src="'.$arm_data['ac_charicon'].'" name="char_icon" alt="icon" width="44px" height="44px" align="middle"/> ';
	}
	
	$myOutput .= '('.($actualcount+1).'/'.$in->get('count',0).') ';
	if(is_array($arm_data)){
		$myOutput  .= $members[$actualcount].' ['.$game->glang('uc_lastupdate').': '.date('d.m.Y', $armory->Date2Timestamp($arm_data['lastmodified'])).']';
		$skipme			= false;
	}elseif($arm_data == 'old_char'){
		$myOutput  .= $members[$actualcount].': <span style="color:orange">'.$game->glang('uc_notyetupdated').'</span>';
		$skipme 		= true;
	}else{
		$myOutput  .= $members[$actualcount].': <span style="color:red">'.$game->glang('uc_noprofile_found').'</span>';
		$skipme 		= true;
	}
	
	if($skipme){
		echo $myOutput;
	}else{
		// Check for existing member name
		$tmp_membeid = $db->query_first("SELECT member_id FROM __members WHERE member_name = '".$members[$actualcount]."'");
		$isindatabase = ($tmp_membeid > 0) ? $tmp_membeid : '0';

		if(!$isindatabase or $isindatabase == 0 or $isindatabase == '0'){
			$myOutput  .= $members[$actualcount].': <span style="color:red">'.$game->glang('uc_error_with_id').'</span>';
		}else{
			echo $myOutput; $iii=1; $profarry = array();
			if(is_array($arm_data)){
				foreach($arm_data['professions']->children() as $professions){
					$profarry[$iii]['name'] 	= $professions['key'];
					$profarry[$iii]['value'] 	= $professions['value'];
					$iii++;
				}
			}

			$dataarray = array(
				'member_name'		=> $members[$actualcount],
				'member_id'			=> $isindatabase,
				'member_race_id'	=> $armory->ValueorNull($arm_data['race_eqdkp']),
				'member_class_id'	=> $armory->ValueorNull($arm_data['class_eqdkp']),
				'member_level'		=> $armory->ValueOrNull($arm_data['level']),
				'gender'			=> ($arm_data['genderid'] == 1) ? 'Female' : 'Male',
				'faction'			=> ($arm_data['factionid'] == 1) ? 'Horde' : 'Alliance',
				'guild'				=> $arm_data['guildname'],
				'last_update'		=> $armory->Date2Timestamp($arm_data['lastmodified']),

				'skill_1'			=> $armory->ValueOrNull($arm_data['spec1']['treeOne']),
				'skill_2'			=> $armory->ValueOrNull($arm_data['spec1']['treeTwo']),
				'skill_3'			=> $armory->ValueOrNull($arm_data['spec1']['treeThree']),

				'health_bar'		=> $arm_data['characterbars']->health['effective'],
				'second_bar'		=> $arm_data['characterbars']->secondBar['effective'],
				'second_name'		=> $arm_data['characterbars']->secondBar['type'],
				'prof1_value'		=> ($profarry[1]['value']) ? $profarry[1]['value'] : '',
				'prof1_name'		=> ($profarry[1]['name']) ? $profarry[1]['name'] : '',
				'prof2_value'		=> (count($profarry) == 2) ? $profarry[2]['value'] : '',
				'prof2_name'		=> (count($profarry) == 2) ? $profarry[2]['name'] : '',

				'fire'				=> $armory->ValueOrNull($arm_data['resistances']->fire['value']),
				'nature'			=> $armory->ValueOrNull($arm_data['resistances']->nature['value']),
				'shadow'			=> $armory->ValueOrNull($arm_data['resistances']->shadow['value']),
				'arcane'			=> $armory->ValueOrNull($arm_data['resistances']->arcane['value']),
				'frost'				=> $armory->ValueOrNull($arm_data['resistances']->frost['value']),
			);

			// Skill tree 2 / DualSpec
			$dataarray['skill2_1'] = ($arm_data['dualspec']) ? $armory->ValueOrNull($arm_data['spec2']['treeOne']) : '0';
			$dataarray['skill2_2'] = ($arm_data['dualspec']) ? $armory->ValueOrNull($arm_data['spec2']['treeTwo']): '0';
			$dataarray['skill2_3'] = ($arm_data['dualspec']) ? $armory->ValueOrNull($arm_data['spec2']['treeThree']): '0';

			if(is_array($arm_data)){
				$CharTools->updateChar($isindatabase, '', $dataarray, true);
			}
		} // end of null member id check
	} // end of skipme

	// Wait 2 second.. to prevent armory blacklisting..
	flush();
	sleep(2);

	// end of import into DB
	if($in->get('cron')){
		redirect('updateprofile.php?cron=true&count='.$in->get('count',0).'&actual='.$nextcount);
	}else{
		redirect('updateprofile.php?count='.$in->get('count',0).'&actual='.$nextcount);
	}
}

if($in->get('actual',0) == $in->get('count',0)){
	if($in->get('cron')){
		$core->config_set(array('uc_profileimported'=> time()));
		die('fertig');
	}else{
		$core->config_set(array('uc_profileimported'=> time()));
		$output = '<table><tr><td width="48px"><img src="../../../images/ok.png" alt="update" \></td><td>'. $game->glang('uc_profile_ready').'</td></tr></table>';
		echo "<script>parent.document.getElementById('loadingtext').innerHTML = '".$output." ';</script>";
	}
}
?>