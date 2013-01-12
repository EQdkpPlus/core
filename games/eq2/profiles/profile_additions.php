<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

	$this->game->new_object('eq2_soe', 'soe', array());
	$chardata = $this->game->obj['soe']->character($member['name'],$this->config->get('uc_servername'));
	if ($chardata && !isset($chardata['status'])){
		infotooltip_js();
	
		$this->tpl->add_css('
			.char_faction_1{
				color: #0060ff;
			}
			.char_faction_2{
				color: #90ead0;
			}
			.char_faction_3{
				color: #96ff66;
			}
			.char_faction_4{
				color: #dedede;
			}
			.char_faction_5{
				color: #ffe446;
			}
			.char_faction_6{
				color: #e0703a;
			}
			.char_faction_7{
				color: #d10000;
			}
			.xpbar {
    color: white;
    display: block;
    float: left;
    font-family: serif;
    font-weight: bold;
    margin-bottom: 3px;
    text-decoration: none;
    width: 170px;
}
.xpbar .bar {
    background-color: #7C4821;
    border: 1px solid #F0CAB7;
    border-bottom-left-radius: 5px;
    border-top-right-radius: 5px;
    display: block;
    margin-bottom: 4px;
    position: relative;
    text-shadow: 0 1px 0 #000000;
}
.xpbar .bar .value {
    background: url("./games/eq2/profiles/images/xpbar-overlay.png") repeat-x scroll 0 0 transparent;
    display: block;
    font-size: 12px;
    position: relative;
    text-align: center;
    z-index: 2;
}
.xpbar .bar .index {
    background-color: #B8E4F5;
    border-bottom-left-radius: 5px;
    display: block;
    height: 100%;
    left: 0;
    overflow: hidden;
    position: absolute;
    text-indent: -9999px;
    top: 0;
}
.xpbar .bar .xpbar_adv {
    background: none repeat scroll 0 0 #C89442;
}
.xpbar .bar .xpbar_aa {
    background: none repeat scroll 0 0 #26B011;
}
.xpbar .bar .xpbar_ts {
    background: none repeat scroll 0 0 #B010AF;
}
.xpbubble {
    background-color: #413122;
    border: 2px solid #EBDEAA;
    border-radius: 10px 10px 10px 10px;
    float: left;
    font-size: 11px;
    margin-left: 20px;
    padding: 0 2px;
    text-align: center;
    text-shadow: 0 1px 0 #000000;
    width: 14px;
}
		');
		
		
		$cdata 		= $chardata['character_list'][0];
		//d($cdata);
		$tradeskill_keys = array_keys($cdata['tradeskills']);
		$second_tradeskill = "";
		foreach($cdata['secondarytradeskills'] as $key=>$value){
			$second_tradeskill .= $this->jquery->ProgressBar('second_tradeskill'.$key, ((int)$value['totalvalue'] / (int)$value['maxvalue'])*100, ucfirst($key).' ('.$value['totalvalue'].')');
		}
		$this->tpl->assign_vars(array(
			'ARMORY' 		=> 1,
			'CHAR_IMGURL'		=> $this->game->obj['soe']->imgurl,
			'DATA_TRADESKILL'	=> ucfirst($tradeskill_keys[0]).' ('.$cdata['tradeskills'][$tradeskill_keys[0]]['level'].')',
			'DATA_TRADESKILL_LEVEL' => $cdata['tradeskills'][$tradeskill_keys[0]]['level'],
			'DATA_LEVEL_XP'		=> ((int)$cdata['experience']['currentadventureexp'] == 0) ? 0 : intval(((float)$cdata['experience']['currentadventureexp'] / (float)$cdata['experience']['adventureexpfornextlevel'])*100),
			'DATA_TRADESKILL_XP'=> ((int)$cdata['experience']['currenttradeskillexp'] == 0) ? 0 : intval(((float)$cdata['experience']['currenttradeskillexp'] / (float)$cdata['experience']['tradeskillexpfornextlevel'])*100),
			
			'DATA_SECOND_TRADESKILL' => $second_tradeskill,
			'DATA_AA'	=> ((int)$cdata['alternateadvancements']['spentpoints'] + (int)$cdata['alternateadvancements']['availablepoints']).' ('.$cdata['alternateadvancements']['availablepoints'].' unspent)',
			'DATA_BIRTHDATE'	=> $this->time->user_date($cdata['type']['birthdate_utc']),
			'DATA_LASTSEEN'		=> $this->time->user_date($cdata['last_update']),
			'DATA_PLAYEDTIME'	=> intval($cdata['playedtime']/60/60/24),
			'DATA_GUILDJOINED'	=> $this->time->user_date($cdata['guild']['joined']),
			'DATA_KILLDEATHRATIO'=> round((float)$cdata['statistics']['kills_deaths_ratio']['value'], 2),
			'DATA_BLOCKCHANCE'	=>  floor((float)$cdata['stats']['combat']['blockchance']),
			'DATA_CRITCHANCE'	=> floor((float)$cdata['stats']['combat']['critchance']),
			'DATA_CRITBONUS'	=> floor((float)$cdata['stats']['combat']['critbonus']),
			'DATA_POTENCY'	=> floor((float)$cdata['stats']['combat']['basemodifier']),
			'DATA_ABILITY'	=> floor((float)$cdata['stats']['combat']['abilitymod']),
			'DATA_HATEGAIN'	=> floor((float)$cdata['stats']['combat']['hategainmod']),
			'DATA_DPS'		=> floor((float)$cdata['stats']['combat']['dps']),
			'DATA_STRIKE'	=> floor((float)$cdata['stats']['combat']['strikethrough']),
			'DATA_ACCURACY'	=> floor((float)$cdata['stats']['combat']['accuracy']),
			'DATA_AEAUTO'	=> floor((float)$cdata['stats']['combat']['aeautoattackchance']),
			'DATA_ATTACKSPEED'	=> floor((float)$cdata['stats']['combat']['attackspeed']),
			'DATA_DOUBLEATTACK'	=> floor((float)$cdata['stats']['combat']['doubleattackchance']),
			'DATA_SP_DOUBLEATTACK' => floor((float)$cdata['stats']['combat']['spelldoubleattackchance']),
			'DATA_SP_WAA' => floor((float)$cdata['stats']['combat']['spellweaponautoattackchance']),
			'DATA_SP_WAS' => floor((float)$cdata['stats']['combat']['spellweaponattackspeed']),
			'DATA_SP_WDA' => floor((float)$cdata['stats']['combat']['spellweapondoubleattackchanc']),
			'DATA_SP_WDPS' => floor((float)$cdata['stats']['combat']['spellweapondps']),
			'DATA_SP_WF' => floor((float)$cdata['stats']['combat']['spellweaponflurry']),
		));
				
		foreach($cdata['resists'] as $key => $value){
			$this->tpl->assign_block_vars('defense_list', array(
				'NAME'	=> ucfirst($key),
				'VALUE' => $value['effective'],
			));
		}
		
		//Skills
		$arrTmpSkills = array();
		foreach ($cdata['skills'] as $key => $value){
			$arrTmpSkills[$key] = $value;
		}
		ksort($arrTmpSkills);
		foreach ($arrTmpSkills as $key => $value){
			$maxvalue = ((int)$value['maxvalue'] > (int)$value['totalvalue']) ? (int)$value['maxvalue'] : (int)$value['totalvalue'];
			
			$this->tpl->assign_block_vars('skill_list', array(
				'BAR' => $this->jquery->ProgressBar('skills_'.$key, ((int)$value['totalvalue'] / $maxvalue)*100, $value['totalvalue'].' / '.$maxvalue),
				'NAME'=> ucfirst($key),
			));
		}
		//Languages
		$arrTmpLangs = array();
		foreach ($cdata['language_list'] as $key => $value){
			$arrTmpLangs[$value['name']] = $value['id'];
		}
		ksort($arrTmpLangs);
		foreach ($arrTmpLangs as $key => $value){
			$this->tpl->assign_block_vars('lang_list', array(
				'NAME'=> ucfirst($key),
			));
		}
		//Factions
		$arrTmpFactions = array();
		foreach($cdata['faction_list'] as $value){
			$arrTmpFactions[$value['name']] = $value;
		}
		ksort($arrTmpFactions);
		foreach($arrTmpFactions as $value){
			$val = intval($value['value']);
			
			if ($val == 50000) {
				$class = 1;
			} elseif ($val > 29999) {
				$class = 2;
			} elseif ($val > 9999) {
				$class = 3;
			} elseif ($val > -10000) {
				$class = 4;
			} elseif ($val > -30000) {
				$class = 5;
			} elseif ($val > -49999) {
				$class = 6;
			} else {
				$class = 7;
			}
			$this->tpl->assign_block_vars('faction_list', array(
				'NAME'=> $value['name'],
				'CLASS'=> 'char_faction_'.$class,
				'VALUE'=> $val,
			));
		}
		
		//Places
		foreach($cdata['house_list'] as $value){
			$this->tpl->assign_block_vars('places_list', array(
				'NAME'=> ((int)$value['published'] == 1) ? '<b>'.$value['name'].'</b>' : $value['name'],
				'VALUE'=> $value['address'],
			));
		}
		foreach($cdata['dungeon_list'] as $value){
			$this->tpl->assign_block_vars('dungeon_list', array(
				'NAME'=> ((int)$value['published'] == 1) ? '<b>'.$value['name'].'</b>' : $value['name'],
				'VALUE'=> $value['type'],
				'S_DUNGEONS' => true,
			));
		}
		
		//Achievements
		$arrTmpAchievs = array();
		foreach($cdata['achievements']['achievement_list'] as $value){
			$arrTmpAchievs[$value['name']] = $value;
		}
		ksort($arrTmpAchievs);
		foreach($arrTmpAchievs as $value){
			$this->tpl->assign_block_vars('achievements_list', array(
				'NAME'=> $value['name'],
				'VALUE'=> ((int)$value['completed_timestamp'] == 0) ? '' : $this->time->user_date($value['completed_timestamp']),
			));
		}
		
		$this->tpl->assign_array('CHARDATA', $cdata);
		$this->jquery->Tab_header('char1_tabs');
		
		//Equipment
		$arrTmpEquip = array();
		foreach($cdata['equipmentslot_list'] as $value){
			$arrTmpEquip[] = $value;
			$arrSort[] = $value['displayname'];
		}
		array_multisort($arrSort, SORT_ASC, $arrTmpEquip);
		foreach($arrTmpEquip as $value){
			$this->tpl->assign_block_vars('equipment_list', array(
				'NAME'  => $value['displayname'],
				'VALUE' => (isset($value['item']['displayname'])) ? infotooltip($value['item']['displayname'], 0, false, 0, 0, 1) : '',
				'ICONID'=> (isset($value['item']['iconid'])) ? $value['item']['iconid'] : 0,
			));
		}
		
		//Appearance
		$arrTmpEquip = array();
		$arrSort = array();
		foreach($cdata['appearanceslot_list'] as $value){
			$arrTmpEquip[] = $value;
			$arrSort[] = $value['displayname'];
		}
		array_multisort($arrSort, SORT_ASC, $arrTmpEquip);
		foreach($arrTmpEquip as $value){
			$this->tpl->assign_block_vars('appearance_list', array(
				'NAME'  => $value['displayname'],
				'VALUE' => (isset($value['item']['displayname'])) ? $value['item']['displayname'] : '',
				'ICONID'=> (isset($value['item']['iconid'])) ? $value['item']['iconid'] : 0,
			));
		}
	}
?>