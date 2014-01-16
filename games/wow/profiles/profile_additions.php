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

	$this->jquery->Tab_header('char1_tabs');

	// init infotooltip
	infotooltip_js();

	// Add css & JS Code
	$this->tpl->add_css("
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
		
		ul#wow_icons_left img, ul#wow_icons_right img, ul#wow_icons_bottom img{
			box-shadow: 0 0 8px black;
			-webkit-border-radius: 4px;
			border-radius: 4px;
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
		#bar_92, #bar_96, #bar_97, #bar_95, #bar_168, #bar_169, #bar_15165, #bar_201, #bar_155, #bar_15117, #bar_81{
			width: 31%;
			float: left;
			padding: 5px;
			cursor: pointer;
		}
		
		#profile {
			padding: 6px;
			position: relative;
		}		
		
		#profile_charicon {
			height:100px; 
			float: left;
		}
		
		#profile_charname {
			font-size: 56px;
			font-weight: bold;
			letter-spacing: -0.05em;
			line-height: 1.1em;
			margin-left: 8px;
			vertical-align: top;
			height:60px; 
			float: left;
		}
		
		#profile_titel_guild {
			height: 60px; 
			float: left;
			padding-left: 16px;
			padding-top: 10px;
			font-size: 14px;
			line-height: 25px;
		}
		
		.profile_guild {
			font-size: 20px;
			color: #FFB100;
			line-height: 15px;
		}
		
		#profile_charinfos {
			float:left;
			font-size: 15px;
			margin-left: 8px;
			margin-top: -4px;
		}
		
		.profile_charname{
			top: 0;
			right: 0;
		}
		.profile_chartitle{
			font-size: 17px;
			font-weight: bolder;
			color: #FFCC33;
		}
		.profile_guildname{
			font-size: 16px;
			font-weight: bolder;
			color: #FFCC33;
		}
		.profile_charpoints{
			font-size: 17px;
			font-weight: bolder;
			color: #white;
			position:relative;
			top:0px;
			left:0px;
		}
		.profile_itemlevel{
			position: absolute;
			bottom: 0;
			right: 6px;
		}
		.profile_itemlevel_avg{
			font-size: 36px;
			font-weight: bold;
			letter-spacing: -0.05em;
			line-height: 0.8em;
			margin-right: 4px;
			vertical-align: top;
			height: 60px; 
			float: left;
		}
		.profile_itemlevel_txt {
			display: inline-block;
		}
		
		.profile_itemlevel_avgtxt, .profile_itemlevel_eq{
			margin-left: 6px;
		}
		.raideventicon { background: url('games/wow/profiles/events/raid-icons.jpg') no-repeat; width: 59px; height: 59px;}
		.raideventicon.id2717 { background-position:        0 0; }
		.raideventicon.id2677 { background-position:    -61px 0; }
		.raideventicon.id3429 { background-position:   -122px 0; }
		.raideventicon.id3428 { background-position:   -183px 0; }
		.raideventicon.id3457 { background-position:   -244px 0; }
		.raideventicon.id3923 { background-position:   -305px 0; }
		.raideventicon.id3836 { background-position:   -366px 0; }
		.raideventicon.id3607 { background-position:   -488px 0; }
		.raideventicon.id3845 { background-position:   -549px 0; }
		.raideventicon.id3606 { background-position:   -610px 0; }
		.raideventicon.id3959 { background-position:   -671px 0; }
		.raideventicon.id4075 { background-position:   -732px 0; }
		.raideventicon.id3456 { background-position:   -793px 0; }
		.raideventicon.id4493 { background-position:   -854px 0; }
		.raideventicon.id4603 { background-position:   -915px 0; }
		.raideventicon.id4500 { background-position:   -976px 0; }
		.raideventicon.id4273 { background-position:  -1037px 0; }
		.raideventicon.id4722 { background-position:  -1098px 0; }
		.raideventicon.id2159 { background-position:  -1159px 0; }
		.raideventicon.id4812 { background-position:  -1220px 0; }
		.raideventicon.id4987 { background-position:  -1281px 0; }
		.raideventicon.id5600 { background-position:  -1342px 0; }
		.raideventicon.id5334 { background-position:  -1403px 0; }
		.raideventicon.id5094 { background-position:  -1464px 0; }
		.raideventicon.id5638 { background-position:  -1525px 0; }
		.raideventicon.id5723 { background-position:  -1586px 0; }
		.raideventicon.id5892 { background-position:  -1647px 0; }
		.raideventicon.id6125 { background-position:  -1708px 0; }
		.raideventicon.id6297 { background-position:  -1769px 0; }
		.raideventicon.id6067 { background-position:  -1830px 0; }
		.raideventicon.id6622 { background-position:  -1891px 0; }
		.raideventicon.id6738 { background-position:  -1952px 0; }
		
		#wow_icons_left .q img, #wow_icons_right .q img, #wow_icons_bottom .q img {
			border: 1px solid #ffd100;
		}
		#wow_icons_left .q0 img, #wow_icons_right .q0 img, #wow_icons_bottom .q0 img {
			border: 1px solid #9d9d9d;
		}
		#wow_icons_left .q1 img, #wow_icons_right .q1 img, #wow_icons_bottom .q1 img {
			border: 1px solid #ffffff;
		}
		#wow_icons_left .q2 img, #wow_icons_right .q2 img, #wow_icons_bottom .q2 img {
			border: 1px solid #1eff00;
		}
		#wow_icons_left .q3 img, #wow_icons_right .q3 img, #wow_icons_bottom .q3 img {
			border: 1px solid #0070dd;
		}
		#wow_icons_left .q4 img, #wow_icons_right .q4 img, #wow_icons_bottom .q4 img {
			border: 1px solid #a335ee;
		}
		#wow_icons_left .q5 img, #wow_icons_right .q5 img, #wow_icons_bottom .q5 img {
			border: 1px solid #ff8000;
		}
		#wow_icons_left .q6 img, #wow_icons_right .q6 img, #wow_icons_bottom .q6 img {
			border: 1px solid #ff0000;
		}
		#wow_icons_left .q7 img, #wow_icons_right .q7 img, #wow_icons_bottom .q7 img {
			border: 1px solid #E5CC80;
		}
		#wow_icons_left .q8 img, #wow_icons_right .q8 img, #wow_icons_bottom .q8 img {
			border: 1px solid #ffff98;
		}
		
		.accountwide { color: #00AEFF !important; }

		.icon-frame {
			-moz-border-bottom-colors: none;
			-moz-border-left-colors: none;
			-moz-border-right-colors: none;
			-moz-border-top-colors: none;
			background-color: #000000;
			background-position: 1px 1px;
			background-repeat: no-repeat;
			border-color: #B1B2B4 #434445 #2F3032;
			border-image: none;
			border-left: 1px solid #434445;
			border-radius: 3px 3px 3px 3px;
			border-right: 1px solid #434445;
			border-style: solid;
			border-width: 1px;
			padding: 1px;
			-moz-border-radius:4px; /* Firefox */
			-webkit-border-radius:4px; /* Safari, Chrome */
			-khtml-border-radius:4px; /* Konqueror */
			border-radius:4px; /* CSS3 */
		}
		.icon-frame.frame-14 { height: 14px; width: 14px; }
		.icon-frame.frame-18 { height: 18px; width: 18px; }
		.icon-frame.empty {
			border-color: black;
			border-radius: 2px 2px 2px 2px;
			box-shadow: 0 0 0 0 transparent;
			color: #572E1B;
			font-family: Arial,sans-serif;
			font-size: 12px;
			font-weight: bold;
			line-height: 18px;
			text-align: center;
			vertical-align: top;
		}

		.talenttab_select{ margin-left: 8px; }
		.talenttab_name{ margin-left: 4px; }

		.profession-name { padding-left: 4px; }
		.profession-icon { margin-left: 4px; }
		.profession-row { height: 30px; }

		.healthpowerbar {
			color: white;
			float: left;
			margin: 0 0 0px;
		}
		.healthpowerbar li {
			background: url('games/wow/profiles/bars/health_power_bars.png') repeat-x scroll 0 0 transparent;
			border-radius: 3px 3px 3px 3px;
			display: inline-block;
			margin-right: 11px;
			padding-left: 11px;
			text-shadow: 1px 1px 1px #000000;
			width: 140px;
		}
		.healthpowerbar li, .healthpowerbar span {
			height: 23px;
			line-height: 23px;
		}
		.healthpowerbar .name {
			font-size: 11px;
		}
		.healthpowerbar .value {
			font-family: 'Arial Black',Arial,sans-serif;
			font-size: 12px;
			font-weight: 900;
			padding-left: 5px;
		}
		.healthpowerbar .health {
			background-color: #248000;
			background-position: 0 0;
			margin-bottom: 7px;
		}
		.healthpowerbar .mana {
			background-color: #1C8AFF;
			background-position: 0 -23px;
		}
		.healthpowerbar .rage {
			background-color: #AB0000;
			background-position: 0 -69px;
		}
		.healthpowerbar .focus {
			background-color: #964414;
			background-position: 0 -115px;
		}
		.healthpowerbar .energy {
			background-color: #CB9501;
			background-position: 0 -46px;
		}
		.healthpowerbar .runic-power {
			background-color: #00ACCB;
			background-position: 0 -92px;
		}
	");

	// Armory based information
	$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
	$member_servername	= $this->pdh->get('member', 'profiledata', array($this->url_id, 'servername'));
	$servername			= ($member_servername != '') ? $member_servername : $this->config->get('uc_servername');
	$chardata			= $this->game->obj['armory']->character($member['name'], $servername);
	if($this->config->get('uc_servername') != '' && !isset($chardata['status'])){
		$this->jquery->Tab_header('talent_tabs');
		$this->jquery->Tab_header('achievement_tabs');
		$this->tpl->add_js("
			$('#base').hide();
			$('#melee').hide();
			$('#range').hide();
			$('#spell').hide();
			$('#defenses').hide();
			$('#char_infos').change(function(){
			if(this.value == 'all'){
				$('#boxes').children().show();
			}else{
				$('#' + this.value).show().siblings().hide();}
			});
			$('#char_infos').change();
		", 'docready');
		$items = $this->game->callFunc('getItemArray', array($chardata['items'], $member['name']));

		// talents & professions
		$this->tpl->assign_array('bnetlinks',	$this->game->obj['armory']->a_bnlinks($member['name'],$this->config->get('uc_servername'), $chardata['guild']['name']));
		$this->tpl->assign_array('items',		$items);

		// talents
		$a_talents = $this->game->callFunc('talents', array($chardata));
		foreach ($a_talents as $id_talents => $v_talents){
			$this->tpl->assign_block_vars('talents', array(
				'ID'			=> $id_talents,
				'SELECTED'		=> ($v_talents['selected'] == '1') ? true : false,
				'ICON'			=> $v_talents['icon'],
				'NAME'			=> $v_talents['name'],
				'ROLE'			=> strtolower($v_talents['role']),
				'DESCRIPTION'	=> $v_talents['desc'],
			));

			// talent specialization
			for ($i_ts = 0; $i_ts < 6; $i_ts ++) {
				$this->tpl->assign_block_vars('talents.special', array(
					'NAME'			=> (isset($v_talents['talents'][$i_ts]) && $v_talents['talents'][$i_ts]['name']) ? $v_talents['talents'][$i_ts]['name'] : $this->game->glang('empty'),
					'ICON'			=> (isset($v_talents['talents'][$i_ts]) && $v_talents['talents'][$i_ts]['icon']) ? '<div class="icon-frame frame-18" style="background-image: url('.$v_talents['talents'][$i_ts]['icon'].');"></div>' : '<div class="icon-frame frame-18 empty"></div>',
					'DESCRIPTION'	=> (isset($v_talents['talents'][$i_ts]) && $v_talents['talents'][$i_ts]['description']) ? $v_talents['talents'][$i_ts]['description'] : false,
				));
			}

			// talent glyphs
			for ($i_tgs = 0; $i_tgs < 3; $i_tgs ++) {
				$this->tpl->assign_block_vars('talents.glyphs_major', array(
					'NAME'			=> (isset($v_talents['glyphs']['minor'][$i_tgs]) && $v_talents['glyphs']['major'][$i_tgs]['name']) ? $v_talents['glyphs']['major'][$i_tgs]['name'] : false,
					'ICON'			=> (isset($v_talents['glyphs']['minor'][$i_tgs]) && $v_talents['glyphs']['major'][$i_tgs]['icon']) ? '<div class="icon-frame frame-18" style="background-image: url('.$v_talents['glyphs']['major'][$i_tgs]['icon'].');"></div>' : '<div class="icon-frame frame-18 empty"></div>',
				));

				$this->tpl->assign_block_vars('talents.glyphs_minor', array(
					'NAME'			=> (isset($v_talents['glyphs']['minor'][$i_tgs]) && $v_talents['glyphs']['minor'][$i_tgs]['name']) ? $v_talents['glyphs']['minor'][$i_tgs]['name'] : false,
					'ICON'			=> (isset($v_talents['glyphs']['minor'][$i_tgs]) && $v_talents['glyphs']['minor'][$i_tgs]['icon']) ? '<div class="icon-frame frame-18" style="background-image: url('.$v_talents['glyphs']['minor'][$i_tgs]['icon'].');"></div>' : '<div class="icon-frame frame-18 empty"></div>',
				));
			}

		}

		// professions
		$a_professions = $this->game->callFunc('professions', array($chardata));
		foreach ($a_professions as $v_professions){
			$this->tpl->assign_block_vars('professions', array(
				'ICON'			=> $v_professions['icon'],
				'NAME'			=> $v_professions['name'],
				'BAR'			=> $v_professions['progressbar'],
			));
		}

		// RIGHT SIDE PANEL - Char attributes
		$charattributes = array(
			'base' => array(
				$this->game->glang('strength')		=> $chardata['stats']['str'],
				$this->game->glang('agility')		=> $chardata['stats']['agi'],
				$this->game->glang('stamina')		=> $chardata['stats']['sta'],
				$this->game->glang('intellect')		=> $chardata['stats']['int'],
				$this->game->glang('spirit')		=> $chardata['stats']['spr'],
				$this->game->glang('mastery')		=> round($chardata['stats']['mastery'], 2).'%',
			),
			'melee' => array(
				$this->game->glang('mainHandDamage')=> $chardata['stats']['mainHandDmgMin']." - ".$chardata['stats']['mainHandDmgMax'],
				$this->game->glang('mainHandDps')	=> round($chardata['stats']['mainHandDps'], 1),
				$this->game->glang('power')			=> $chardata['stats']['attackPower'],
				$this->game->glang('hasteRating')	=> $chardata['stats']['hasteRating'],
				$this->game->glang('mainHandSpeed')	=> round($chardata['stats']['mainHandSpeed'], 2),
				$this->game->glang('hitPercent')	=> '+'.round($chardata['stats']['hitPercent'], 2).'%',
				$this->game->glang('critChance')	=> round($chardata['stats']['crit'], 2).'%',
				$this->game->glang('expertise')		=> round($chardata['stats']['mainHandExpertise'], 2).'%',
			),
			'range' => array(
				$this->game->glang('damage')		=> (($chardata['stats']['rangedDmgMin'] > 0) ? $chardata['stats']['rangedDmgMin'] : '')." - ". (($chardata['stats']['rangedDmgMax'] > 0) ? $chardata['stats']['rangedDmgMax'] : ''),
				$this->game->glang('rangedDps')		=> ($chardata['stats']['rangedDps'] > 0) ? $chardata['stats']['rangedDps'] : '-',
				$this->game->glang('power')			=> $chardata['stats']['rangedAttackPower'],
				$this->game->glang('rangedSpeed')	=> ($chardata['stats']['rangedSpeed'] > 0) ? $chardata['stats']['rangedSpeed'] : '-',
				$this->game->glang('hitPercent')	=> '+'.round($chardata['stats']['hitPercent'], 2).'%',
				$this->game->glang('critChance')	=> round($chardata['stats']['rangedCrit'], 2).'%',
			),
			'spell' => array(
				$this->game->glang('spellpower')	=> $chardata['stats']['spellPower'],
				$this->game->glang('spellHit')		=> round($chardata['stats']['spellHitPercent'], 2).'%',
				$this->game->glang('spellCrit')		=> round($chardata['stats']['spellCrit'], 2).'%',
				$this->game->glang('spellPen')		=> $chardata['stats']['spellPen'],
				$this->game->glang('manaRegen')		=> $chardata['stats']['mana5'],
				$this->game->glang('combatRegen')	=> $chardata['stats']['mana5Combat']
			),
			'defenses' => array(
				$this->game->glang('armor')			=> $chardata['stats']['armor'],
				$this->game->glang('dodge')			=> round($chardata['stats']['dodge'], 2).'%',
				$this->game->glang('parry')			=> round($chardata['stats']['parry'], 2).'%',
				$this->game->glang('block')			=> round($chardata['stats']['block'], 2).'%',
				$this->game->glang('pvpresil')		=> round($chardata['stats']['pvpResilience'], 2).'%',
				$this->game->glang('pvppower')		=> round($chardata['stats']['pvpPower'], 2).'%',
			)
		);

		// character attributes
		foreach ($charattributes as $info_grp_name => $info_grp){
			$this->tpl->assign_block_vars('charattribute_group', array(
					'ID'	=> $info_grp_name,
					'NAME'	=> $this->game->glang($info_grp_name)
				));
			foreach ($info_grp as $info_name => $info){
				$this->tpl->assign_block_vars('charattribute_group.charattributes', array(
					'NAME'		=> $info_name,
					'VALUE'		=> $info,
				));
			}
		}

		// Character News Feed
		$d_charfeed = $this->game->callFunc('ParseCharNews', array($chardata));
		$cnf_output = '';
		if (is_array($d_charfeed)) {
			$arrCharacterAchievements = $this->game->obj['armory']->getdata();
			foreach ($d_charfeed as $v_charfeed){
				switch ($v_charfeed['type']){
						case 'achievement':
							$achievCat = $this->game->obj['armory']->getCategoryForAchievement((int)$v_charfeed['achievementID'], $arrCharacterAchievements);
							$bnetLink = $this->game->obj['armory']->bnlink($chardata['name'], $this->config->get('uc_servername'), 'achievements', $this->config->get('guildtag')).'#'.$achievCat.':a'.$v_charfeed['achievementID'];
							$class='';
							if ($v_charfeed['accountWide']) $class = 'accountwide';
						
							$cnf_output = ($v_charfeed['hero']) ? sprintf($this->game->glang('charnf_achievement_hero'), '<a href="'.$bnetLink.'" class="'.$class.'">'.$v_charfeed['title'].'</a>') : sprintf($this->game->glang('charnf_achievement'), '<a href="'.$bnetLink.'" class="'.$class.'">'.$v_charfeed['title'].'</a>', $v_charfeed['points']);

						break;
						case 'bosskill':
							$cnf_output = sprintf($this->game->glang('charnf_bosskill'), $v_charfeed['quantity'], $v_charfeed['title']);
						break;
						case 'criteria':
							$achievCat = $this->game->obj['armory']->getCategoryForAchievement((int)$v_charfeed['achievementID'], $arrCharacterAchievements);
							$bnetLink = $this->game->obj['armory']->bnlink($chardata['name'], $this->config->get('uc_servername'), 'achievements', $this->config->get('guildtag')).'#'.$achievCat.':a'.$v_charfeed['achievementID'];
							
							$cnf_output = sprintf($this->game->glang('charnf_criteria'), '<b>'.$v_charfeed['criteria'].'</b>', '<a href="'.$bnetLink.'">'.$v_charfeed['title'].'</a>');
						break;
						case 'item':
							$itemData = $this->game->obj['armory']->item($v_charfeed['itemid']);
							$item = infotooltip($itemData['name'], $v_charfeed['itemid'], false, false, false, true, $chardata['name'], $this->config->get('uc_servername'));
							$cnf_output = sprintf($this->game->glang('charnf_item'), $item);
							$v_charfeed['icon'] = 'http://eu.media.blizzard.com/wow/icons/18/'.$itemData['icon'].'.jpg';
						break;
				}
				$this->tpl->assign_block_vars('charfeed', array(
					'TEXT'	=> $cnf_output,
					'ICON'	=> $v_charfeed['icon'],
					'DATE'	=> $this->time->nice_date($v_charfeed['timestamp'], 60*60*24*7),
				));
			}
		}

		// item icons
		foreach ($items as $items_pos=>$v_items){
			foreach ($v_items as $slots){
				$this->tpl->assign_block_vars('itemicons_'.$items_pos, array('SLOTS'	=> $slots));
			}
		}
		$this->tpl->assign_array('itemlevel',		$items['itemlevel']);

		// boss progress
		$d_bossprogress		= $this->game->callFunc('ParseRaidProgression', array($chardata));
		if(is_array($d_bossprogress)){
			foreach($d_bossprogress as $v_progresscat=>$a_bossprogress){

				$this->tpl->assign_block_vars('bossprogress_cat', array(
					'NAME'	=> $this->game->glang('uc_achievement_tab_'.$v_progresscat),
					'ID'	=> $v_progresscat
				));

				$a_bossprogress =  array_reverse($a_bossprogress);
				foreach($a_bossprogress as $v_bossprogress){

					// build the tooltip
					$tt_bossprogress = '<table border="0" width="100%" cellspacing="0" cellpadding="2" >
											<tr>
												<td></td>
												<td>normal</td>
												<td>heroic</td>
											</tr>';
					foreach($v_bossprogress['bosses'] as $bosses){
						$tt_bossprogress .= '<tr>
												<td>'.$bosses['name'].'</td>
												<td>'.$bosses['normalKills'].'</td>
												<td>'.$bosses['heroicKills'].'</td>
											</tr>';
					}
					$tt_bossprogress .= '</table>';

					// normal
					$percent_bc_normal	= ($v_bossprogress['bosses_normal'] != 0) ? intval(($v_bossprogress['bosses_normal'] / $v_bossprogress['bosses_max']) * 100) : 0;
					$bar_bc_normal		= $this->jquery->ProgressBar('bcnormal_'.$v_bossprogress['id'], $percent_bc_normal, $v_bossprogress['bosses_normal'] .' / ' . $v_bossprogress['bosses_max'].' ('.$percent_bc_normal.'%)');

					//Heroic
					$percent_bc_heroic	= ($v_bossprogress['bosses_heroic'] != 0) ? intval(($v_bossprogress['bosses_heroic'] / $v_bossprogress['bosses_max']) * 100) : 0;
					$bar_bc_heroic		= $this->jquery->ProgressBar('bcheroic_'.$v_bossprogress['id'], $percent_bc_heroic, 'heroic '.$v_bossprogress['bosses_heroic'] .' / ' . $v_bossprogress['bosses_max'].' ('.$percent_bc_heroic.'%)');

					$this->tpl->assign_block_vars('bossprogress_cat.bossprogress_val', array(
						'ID'	=> $v_bossprogress['id'],
						'NAME'	=> $v_bossprogress['name'],
						'BARS'	=> $this->html->ToolTip($tt_bossprogress , $bar_bc_normal.$bar_bc_heroic, '', array('usediv'=>true, 'name'=>'tt_bossprogress')),
						'RUNS'	=> sprintf($this->game->glang('bossprogress_normalruns'), $v_bossprogress['runs_normal']).' '.sprintf($this->game->glang('bossprogress_heroicruns'), $v_bossprogress['runs_heroic'])
					));
				}
			}
		}

		// achievements
		$a_achievements = $this->game->callFunc('parseCharAchievementOverview', array($chardata));
		foreach ($a_achievements as $id_achievements => $v_achievements){
			$percent_achievements = ($v_achievements['completed'] != 0) ? intval(($v_achievements['completed'] / $v_achievements['total']) * 100) : 0;
			$this->tpl->assign_block_vars('achievements', array(
				'NAME'	=> $v_achievements['name'],
				'BAR'	=> $this->jquery->ProgressBar('guildachievs_'.$id_achievements, $percent_achievements, $v_achievements['completed'] .' / ' . $v_achievements['total'].' ('.$percent_achievements.'%)'),
				'ID'	=> $id_achievements,
				'LINK'	=> ($id_achievements != 'total') ? $this->game->obj['armory']->bnlink($chardata['name'], register('config')->get('uc_servername'), 'achievements').'#achievement#'.$id_achievements : '',
			));
		}

		// latest achievements
		$a_latestAchievements = $this->game->callFunc('parseLatestCharAchievements', array($chardata,$chardata['name']));
		foreach ($a_latestAchievements as $v_latestAchievements){
			$this->tpl->assign_block_vars('latestachievements', array(
					'NAME'	=> $v_latestAchievements['name'],
					'ICON'	=> $v_latestAchievements['icon'],
					'DESC'	=> $v_latestAchievements['desc'],
					'POINTS'=> $v_latestAchievements['points'],
					'DATE'	=> $this->time->nice_date($v_latestAchievements['date'], 60*60*24*7),
			));
		}

		$this->tpl->assign_vars(array(
			'ARMORY'				=> 1,
			'CHARDATA_ICON'			=> $this->game->obj['armory']->characterIcon($chardata),
			'CHARACTER_IMG'			=> $this->game->obj['armory']->characterImage($chardata, 'big'),
			'CHARDATA_NAME'			=> $chardata['name'],
			'CHARDATA_GUILDNAME'	=> $chardata['guild']['name'],
			'CHARDATA_GUILDREALM'	=> $chardata['guild']['realm'],
			'CHARDATA_POINTS'		=> $chardata['achievementPoints'],
			'CHARDATA_TITLE'		=> $this->game->obj['armory']->selectedTitle($chardata['titles'], true),

			// Bars
			'HEALTH_VALUE'			=> $chardata['stats']['health'],
			'POWER_VALUE'			=> $chardata['stats']['power'],
			'POWER_TYPE'			=> $chardata['stats']['powerType'],
			'POWER_NAME'			=> $this->game->glang('uc_bar_'.$chardata['stats']['powerType']),
		));

	// the non armory charview
	}else{
		$a_lang_profession = $this->game->get('professions');
		$a_professions = array(
			0	=> array(
				'icon'			=> "games/wow/profiles/professions/".(($member['prof1_name']) ? $member['prof1_name'] : '0').".jpg",
				'name'			=> $a_lang_profession[$member['prof1_name']],
				'progressbar'	=> $this->html->bar((($member['prof1_value']) ? $member['prof1_value'] : 0), 600, 110, "")
			),
			1	=> array(
				'icon'			=> "games/wow/profiles/professions/".(($member['prof2_name']) ? $member['prof2_name'] : '0').".jpg",
				'name'			=> $a_lang_profession[$member['prof2_name']],
				'progressbar'	=> $this->html->bar((($member['prof2_value']) ? $member['prof2_value'] : 0), 600, 110, "")
			)
		);
		foreach ($a_professions as $v_professions){
			$this->tpl->assign_block_vars('professions', array(
				'ICON'			=> $v_professions['icon'],
				'NAME'			=> $v_professions['name'],
				'BAR'			=> $v_professions['progressbar'],
			));
		}

		$this->tpl->assign_vars(array(
			'ARMORY'				=> 0,
			'CHARDATA_GUILDREALM'	=> $this->config->get('uc_servername'),
			'NO_SERVER_SET'			=> ($this->config->get('uc_servername') != '') ? false : true,
			'CHARACTER_IMG'			=> $this->game->obj['armory']->characterIconSimple($this->game->obj['armory']->ConvertID($member['race_id'], 'int', 'races', true), (($member['gender'] == 'Female') ? '1' : '0')),
			'POWER_BAR_NAME'		=> ($this->game->glang('uc_bar_'.$member['second_name'])) ? $this->game->glang('uc_bar_'.$member['second_name']) : $member['second_name'],
			'ERRORMSG_BNET'			=> sprintf($this->game->glang('no_armory'), $chardata['reason']),
		));
	}
?>