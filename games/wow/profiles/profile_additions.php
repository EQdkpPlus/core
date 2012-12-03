<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       30.06.2009
 * Date:        $Date: 2009-12-05 16:16:23 +0100 (Sat, 05 Dec 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2006-2009 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 6576 $
 *
 * $Id: template.class.php 6576 2009-12-05 15:16:23Z wallenium $
 */

	$jquery->Tab_header('char1_tabs');
	$myMaxProffession = 450;
	
	// Add css code:
	$tpl->add_css("
		.uc_logo_Alliance { 
			background:url('games/wow/profiles/factions/alliance-icon.png');
		}
		.uc_logo_Horde { 
			background:url('games/wow/profiles/factions/horde-icon.png');
		}
		.uc_name { 
			text-align: left;
			color: white;
			font-weight: bold;
			font-size: 20px;
			padding-bottom: 0px;
			padding-left: 6px;
			z-index: 9;
		}
		.uc_subname {
			text-align: left;
			color: white;
			font-size: 11px;
			margin-top:2px;
			margin-left:0px;
			padding-left: 6px;
			z-index: 10;
		}		
		.uc_nametd {
			border-bottom: 1px solid white;
		}
		
		 ul#wow_icons_left {
		    margin: 0; padding: 0;
		  }
		  ul#wow_icons_left li {
		    list-style: none;
		    padding: 1; margin-bottom: 3px;
		  }

		  ul#wow_icons_right {
		    margin: 0; padding: 0;
		  }		  
		  ul#wow_icons_right li {
		    list-style: none;
		    padding: 1; margin-bottom: 3px;
		  }		

		  ul#wow_icons_bottom {
		    margin: 0; padding: 0;
		  }		  		  
		  ul#wow_icons_bottom li {
		    list-style: none;
		    display: inline;		    
		    padding: 1; margin-left: 3px;
		  }
	");
	
	
	// Energy Bars..
	$sendondbar = '';
	if($member['second_name'] == 'r'){
		$sendondbar['name'] = $game->glang('uc_bar_rage');
		$sendondbar['css']	= 'red';
	}elseif($member['second_name'] == 'e'){
		$sendondbar['name'] = $game->glang('uc_bar_energy');
		$sendondbar['css']	= 'yellow';
	}else{
		$sendondbar['name'] = $game->glang('uc_bar_mana');
		$sendondbar['css']	= 'blue';
	}
	
	$armory = new ArmoryChars();
	infotooltip_js();
	$chardata 			= $armory->GetCharacterData($member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'],$core->config['default_locale']);
	$armory_data 		= $armory->BuildMemberArray($chardata[0]);
	
	if ($armory_data <> 'no_char' && $armory_data <> 'old_char') 
	{
		$a_charRSSdata		= $armory->GetCharRSSData($member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'],$core->config['default_locale'],true,true);
		$a_achievement_list	= $armory->GetAchievementData(0,$member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'],$core->config['default_locale'],true,true);
		$a_bosskillData 	= $armory->GetBossKillData(14807, $member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'],$core->config['default_locale'], true, true);
		
		$items 				= $game->callFunc('ShowItem', array($armory_data['characterTab']['items']['item'], $member['name'], $armory_data['characterTab']['glyphs']['glyph']));
		$spec				= $game->callFunc('spec', array($armory_data['characterTab']['talentSpecs']['talentSpec'])) ;
		$profs 				= $game->callFunc('profs', array($armory_data['characterTab']['professions']['skill']));	
		$charInfos 			= $game->callFunc('charInfos', array($armory_data['characterTab'])) ; 
		$achievements 		= $game->callFunc('achievements', array($armory_data['summary'],$armory_links)) ; 
		$achievement_list 	= $game->callFunc('achievement_list', array($a_achievement_list)) ; 
		$basicBars 			= $game->callFunc('basicBars', array($armory_data['characterTab'],$sendondbar['css'])) ;	
		$armory_links 		= $game->callFunc('armoryLinks', array($member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'],$core->config['default_locale'])) ;	 
		$charRSSdata 		= $game->callFunc('CharRssInfos', array($a_charRSSdata)) ;	 
		$bosskillData 		= $game->callFunc('bosskills', array($a_bosskillData)) ;	 
	
		$tpl->assign_array($armory_data, 'armory');
		$tpl->assign_array($spec, 'spec');
		$tpl->assign_array($profs, 'profs');
		$tpl->assign_array($charInfos, 'char_infos');
		$tpl->assign_array($items, 'items');
		$tpl->assign_array($basicBars, 'basicBars');
		$tpl->assign_array($armory_links, 'armory_links');
		$tpl->assign_array($bosskillData, 'bosskillData');		
		$tpl->assign_vars(array('ARMORY' => 1));		
	}else{
		$tpl->assign_vars(array(
			'L_NO_ARMORY_INFO'								=> $game->glang('no_armory'),
			'L_NO_REALM_INFO'								=> (isset($core->config['uc_servername'])) ? "" : $game->glang('no_realm'),
			'ARMORY_LINK'									=> (isset($core->config['uc_servername'])) ? "" : $game->glang('no_realm')
		)) ;		
	}
	

	/*
	if ($pm->check(PLUGIN_INSTALLED, 'gearscore')) 
	{
	}
	*/
	
	$tpl->assign_vars(array(
		'PROF_FACTION'										=> $core->config['uc_faction'],
		'SECONDBAR_NAME'									=> $sendondbar['name'],			
		'L_TAB_CHAR'										=> $game->glang('uc_cat_character'),
		'L_PROFESSIONS'										=> $game->glang('uc_prof_professions'),
		'L_SKILLS'											=> $game->glang('uc_cat_skills'),
		'L_EXT_PROFILE'										=> $game->glang('uc_ext_profiles'),
		'L_TALENTS'											=> $game->glang('uc_pro_talents'),
		'L_corevalues'										=> $game->glang('corevalues'),
		'L_values'											=> $game->glang('values'),
		'L_achievements'									=> $game->glang('uc_achievements'),
		'L_bosskills'										=> $game->glang('uc_bosskills'),
		'L_health'											=> $game->glang('health'),	
	    "L_gearscoreHeader"									=> $game->glang('gearscoreHeader'),
	    "L_gearscore"										=> $game->glang('gearscore'),
	    "L_itemlevel"										=> $game->glang('itemlevel'),
	    "L_min_itemlevel"									=> $game->glang('min_itemlevel'),
	    "L_max_itemlevel"									=> $game->glang('max_itemlevel'),
	    "L_char_news"										=> $game->glang('char_news'),
	
		'ARMORY_3D'											=> $game->callFunc('armory_charviewer', array($member['name'],$core->config['uc_servername'],$core->config['uc_server_loc'])),
		'achievements'										=> $achievements,
		'achievement_list'									=> $achievement_list,
		'charRSSdata'										=> $charRSSdata,
	));
?>