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

	$this->game->new_object('lotro_data', 'ldata', array());
	$chardata = $this->game->obj['ldata']->character($member['name'],$this->config->get('uc_servername'));
	if ($chardata){
		
		$this->tpl->add_css('.charsheet {
	background-color: #0E0E0E;
}

.header_color {
    background-color: #002B6B;
}

.char_panel {
    margin: 10px auto;
}
.equip_cell {
    margin: 0;
    padding: 0;
    position: relative;
}
.char_equip {
    background: url("./games/lotro/profiles/images/equipment_bg.png") repeat scroll 0 0 transparent;
    display: block;
    height: 343px;
    margin: 0;
    padding: 0;
    position: relative;
    width: 444px;
}
.char_name {
    background-image: url("./games/lotro/profiles/images/character_bar_name.png");
    font-size: 1.2em;
    font-weight: bold;
    height: 25px;
    margin-left: 2px;
    padding-top: 3px;
    position: relative;
    text-align: center;
    top: 0;
    width: 440px;
}
.char_core {
    height: 219px;
    left: 136px;
    position: absolute;
    text-align: center;
    top: 28px;
    width: 172px;
}
.class_banner {
    height: 122px;
    left: 0;
    position: absolute;
    top: 24px;
    width: 172px;
}
.char_race, .char_nat, .char_class, .char_level {
    font-weight: bold;
    height: 21px;
    padding-top: 3px;
    position: absolute;
    text-align: center;
    width: 172px;
}
.char_class {
    background-image: url("./games/lotro/profiles/images/character_bar_class.png");
    left: 0;
    top: 0;
}
.char_nat {
    background-image: url("./games/lotro/profiles/images/character_bar_class.png");
    left: 0;
    top: 146px;
}
.char_race {
    background-image: url("./games/lotro/profiles/images/character_bar_class.png");
    left: 0;
    top: 170px;
}
.char_level {
    background-image: url("./games/lotro/profiles/images/character_bar_level.png");
    height: 22px;
    left: 56px;
    top: 194px;
    width: 60px;
}
.currency_bar {
    background: url("./games/lotro/profiles/images/coin_bg.png") repeat scroll 0 0 transparent;
    height: 24px;
    left: 91px;
    position: absolute;
    top: 293px;
    width: 259px;
}
.gold, .silver, .copper, .morale, .power, .armour {
    font-family: serif;
    font-variant: small-caps;
    font-weight: bold;
    position: absolute;
}
.gold, .silver, .copper {
    font-size: 10pt;
    position: absolute;
    top: 4px;
}
.gold {
    right: 226px;
}
.silver {
    right: 173px;
}
.copper {
    right: 120px;
}
.core_stats_en {
    background-image: url("./games/lotro/profiles/images/character_bar_mpa_en.png");
    height: 21px;
    left: 4px;
    padding-top: 3px;
    position: absolute;
    top: 317px;
    width: 436px;
}
.core_stats_de {
    background-image: url("./games/lotro/profiles/images/character_bar_mpa_de.png");
    height: 21px;
    left: 4px;
    padding-top: 3px;
    position: absolute;
    top: 317px;
    width: 436px;
}
.morale, .power, .armour {
    font-size: 8pt;
    font-weight: bold;
    top: 4px;
}
.morale {
    right: 301px;
}
.power {
    right: 156px;
}
.armour {
    right: 10px;
}
.stats {
    vertical-align: top;
}
.col1 {
    width: 145px;
}
.col2 {
    width: 145px;
}
.col3 {
    width: 146px;
}
.stat_list {
    width: 100%;
}
.stat_list th {
    font-family: serif;
    font-size: 10pt;
    font-variant: small-caps;
    font-weight: normal;
    text-align: left;
    width: 60%;
	background: none;
}
.stat_list td {
    font-size: 9pt;
    font-variant: small-caps;
    font-weight: normal;
    text-align: center;
    width: 40%;
}
.equip_icon {
    border: medium none;
}
.slot_2, .slot_3, .slot_4, .slot_5, .slot_6, .slot_7, .slot_8, .slot_9, .slot_10, .slot_11, .slot_12, .slot_13, .slot_14, .slot_15, .slot_16, .slot_17, .slot_18, .slot_19, .slot_20, .slot_21 {
    position: absolute;
}
.slot_14, .slot_15, .slot_2, .slot_7 {
    top: 40px;
}
.slot_11, .slot_16, .slot_3, .slot_8 {
    top: 85px;
}
.slot_9, .slot_10, .slot_5 {
    top: 130px;
}
.slot_12, .slot_13, .slot_4, .slot_6 {
    top: 175px;
}
.slot_17, .slot_18, .slot_19, .slot_20, .slot_21 {
    top: 254px;
}
.slot_14, .slot_11, .slot_9, .slot_12 {
    left: 27px;
}
.slot_15, .slot_16, .slot_10, .slot_13 {
    left: 83px;
}
.slot_2, .slot_3, .slot_5, .slot_4 {
    left: 321px;
}
.slot_7, .slot_8, .slot_6 {
    left: 382px;
}
.slot_17 {
    left: 119px;
}
.slot_18 {
    left: 163px;
}
.slot_19 {
    left: 207px;
}
.slot_20 {
    left: 249px;
}
.slot_21 {
    left: 293px;
}
.creep .charsheet_error {
    border: 1px solid #723636;
}
.creep .char_equip {
    background: url("./games/lotro/profiles/images/creep_equipment_bg.png") repeat scroll 0 0 transparent;
    height: 272px;
}
.creep .char_core {
    left: 13px;
    width: 166px;
}
.creep .char_race, .creep .char_nat, .creep .char_class {
    background-image: url("./games/lotro/profiles/images/bar_monster_class.png");
    width: 166px;
}
.creep .class_banner {
    width: 166px;
}
.creep .core_stats {
    bottom: 0;
    top: auto;
}
.creep .pvmp_rank {
    left: 227px;
    position: absolute;
    top: 45px;
}
.creep .pvmp_stars {
    left: 234px;
    position: absolute;
    top: 90px;
}
.creep .creep_stats {
    left: 285px;
    position: absolute;
    top: 45px;
    width: 151px;
}
.creep .stat_list, .creep .stat_list th, .creep .stat_list td {
    border-collapse: collapse;
    margin: 0;
    padding: 0;
}
.layout-two-col #field_1 .char_panel .currency_bar, .layout-two-col #field_1 .char_panel .stats.col1, .layout-two-col #field_1 .char_panel .stats.col2, .layout-two-col #field_1 .char_panel .stats.col3, .layout-two-col #field_1 .char_panel .equipment_icon, .layout-two-col #field_1 .char_panel .pvmp_rank, .layout-two-col #field_1 .char_panel .pvmp_stars, .layout-two-col #field_1 .char_panel .creep_stats {
    display: none;
}
.layout-two-col #field_1 .char_name {
    background-image: url("./games/lotro/profiles/images/character_bar_name_narrow.png");
    font-size: 1em;
    width: 100%;
}
.layout-two-col #field_1 .char_core {
    left: 0;
    margin: auto;
    position: relative;
    top: 0;
}
.layout-two-col #field_1 .char_panel .char_equip {
    background: none repeat scroll 0 0 transparent;
    height: auto;
    width: 220px;
}
.layout-two-col #field_1 .char_panel .morale, .layout-two-col #field_1 .char_panel .power, .layout-two-col #field_1 .char_panel .armour {
    margin-right: 10px;
    position: static;
    text-align: right;
}
.layout-two-col #field_1 .char_panel .morale {
    padding-top: 6px;
}
.layout-two-col #field_1 .char_panel .power {
    padding-top: 9px;
}
.layout-two-col #field_1 .char_panel .armour {
    padding-top: 10px;
}
.layout-two-col_en #field_1_en .core_stats_en {
    background-image: url("./games/lotro/profiles/images/stat_core_bg_en.png");
    height: 72px;
    left: 0;
    margin: auto;
    padding: 0;
    position: relative;
    top: 0;
    width: 145px;
}

.layout-two-col_de #field_1_de .core_stats_de {
    background-image: url("./games/lotro/profiles/images/stat_core_bg_en.png");
    height: 72px;
    left: 0;
    margin: auto;
    padding: 0;
    position: relative;
    top: 0;
    width: 145px;
}

.vocation .char_name{
	font-variant: small-caps;
}

.vocation .stat_list th {
	width: 25%;
}
.vocation .stat_list td {
	width: auto;
}

.vocation .start_list .td_small {
	width: 20px;
}
.vocation .start_list .td_med {
	width: 25%;
}

.rulingring-tooltip th {
	background: none;
}
');
		//$this->tpl->js_file('http://content.turbine.com/sites/lorebook.lotro.com/js/onering.js');
		
		$this->tpl->assign_vars(array(
			'DATA_CLASSID'	=> $member['class_id'],
			'ARMORY' 		=> 1,
		)) ;
		$this->tpl->assign_array('CHARDATA', $chardata['character']['@attributes']);
		
		$arrStats = array();
		foreach ($chardata['character']['stats']['stat'] as $value){
			$arrStats[$value['@attributes']['name']] = $value['@attributes']['value'];
		}
		$this->tpl->assign_array('CHARSTATS', $arrStats);
		
		$arrEquip = array();
		$arrSlots = $this->game->obj['ldata']->slots;
		
		infotooltip_js();

		foreach ($chardata['character']['equipment']['item'] as $value){
			$slot_id = $arrSlots[$value['@attributes']['slot']];
			if (!$slot_id) {echo "Unknown slot: ".$value['@attributes']['slot'];}
			$this->tpl->assign_block_vars('equipment', array(
				'SLOT_ID' 	=> $slot_id,
				'LINK'	 	=> $value['@attributes']['lorebookEntry'],
				'ITEM_ID'	=> $value['@attributes']['item_id'],
				'ITEM_NAME'	=> $value['@attributes']['name'],
				'ICON'		=> infotooltip($value['@attributes']['name'], intval($value['@attributes']['item_id']), false, 0, 32),
			));
		}
		
		if (isset($chardata['character']['vocation'])){
			$this->tpl->assign_vars(array(
				'DATA_VOCATION'	=> $chardata['character']['vocation']['@attributes']['name'],
				'S_VOCATION'	=> true,
			));
			
			$this->tpl->assign_array('DATA_PROF1', $chardata['character']['vocation']['professions']['profession'][0]['@attributes']);
			$this->tpl->assign_array('DATA_PROF2', $chardata['character']['vocation']['professions']['profession'][1]['@attributes']);
			$this->tpl->assign_array('DATA_PROF3', $chardata['character']['vocation']['professions']['profession'][2]['@attributes']);
		}
		
	}
?>