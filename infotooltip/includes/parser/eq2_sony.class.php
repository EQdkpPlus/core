<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

include_once('itt_parser.aclass.php');

if(!class_exists('eq2_sony')) {
	class eq2_sony extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher', 'pfh' => array('file_handler', array('infotooltips')));
		public $supported_games = array('eq2');
		public $av_langs = array();
		public $settings = array();
		public $itemlist = array();
		public $recipelist = array();
		private $searched_langs = array();

		public function __construct($init=false, $config=false, $root_path=false, $cache=false, $puf=false, $pdl=false){
			parent::__construct($init, $config, $root_path, $cache, $puf, $pdl);
			$g_settings = array(
				'eq2' => array('icon_loc' => 'http://census.soe.com/s:eqdkpplus/img/eq2/icons/', 'icon_ext' => '/item/', 'default_icon' => 'unknown'),
			);
			$this->settings = array(
				'itt_icon_loc' => array(	'name' => 'itt_icon_loc',
											'language' => 'pk_itt_icon_loc',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_loc'])) ? $g_settings[$this->config['game']]['icon_loc'] : ''),
				),
				'itt_icon_ext' => array(	'name' => 'itt_icon_ext',
											'language' => 'pk_itt_icon_ext',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['icon_ext'])) ? $g_settings[$this->config['game']]['icon_ext'] : ''),
				),
				'itt_default_icon' => array('name' => 'itt_default_icon',
											'language' => 'pk_itt_default_icon',
											'fieldtype' => 'text',
											'size' => false,
											'options' => false,
											'default' => ((isset($g_settings[$this->config['game']]['default_icon'])) ? $g_settings[$this->config['game']]['default_icon'] : ''),
				),
			);
			$g_lang = array(
				'eq2' => array('en' => 'en_US'),
			);
			$this->av_langs = ((isset($g_lang[$this->config['game']])) ? $g_lang[$this->config['game']] : '');
		}

		public function __destruct(){
			unset($this->itemlist);
			unset($this->recipelist);
			unset($this->searched_langs);
			parent::__destruct();
		}

		private function getItemIDfromUrl($itemname, $lang, $searchagain=0){
			$searchagain++;
			$itemInfo = urlencode($itemname);
			$link = 'http://census.soe.com/s:eqdkpplus/json/get/eq2/item/?displayname=' . $itemInfo;
			$data = $this->puf->fetch($link);
			$this->searched_langs[] = $lang;
			$itemData = json_decode($data);
			if ($itemData){
				$myItem = $itemData->{'item_list'}[0];
				$item_id = $myItem->id;
				return array($item_id, 'items');
			}
			return $item_id;
		}

		protected function searchItemID($itemname, $lang){
			return $this->getItemIDfromUrl($itemname, $lang);
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$item = array('id' => $item_id);
			if(!$item_id) return null;
			$url = 'http://census.soe.com/s:eqdkpplus/json/get/eq2/item/' . $item['id'];
			$item['link'] = $url;
			$data = $this->puf->fetch($item['link']);
			$itemdata = json_decode($data);
			if ($itemdata){
				$myItem = $itemdata->{'item_list'}[0];
				if ($myItem){
					$content = $this->GenerateItemStatsHTML($myItem);
					$template_html = trim(file_get_contents($this->root_path.'infotooltip/includes/parser/templates/eq2_sony_popup.tpl'));
					$template_html = str_replace('{ITEM_HTML}', $content, $template_html);
					$item['html'] = $template_html;
					$item['lang'] = $lang;
					$item['icon'] = $myItem->{'iconid'};
					$item['name'] = $myItem->{'displayname'};
					return $item;
				}
			}
			$item['baditem'] = true;
			return $item;
		}
		
		protected function OuterDiv($item) 
		{
			return "<div id='item_".$item->{'id'}."' class='itemd_surround itemd_hoverdiv'>\n";
		}
		
		protected function OuterDivNoHide($item) 
		{
			return "<div class='itemd_surround'>\n";
		}
		
		protected function DisplayName($item) 
		{
			return "<div class='itemd_name'>" . $item->{'displayname'} . "</div>\n";
		}
				
		protected function ItemDescription($item)
		{
		$description = $item->{'description'};
					if (substr($description, 0, 2) == '\#') { 
						$desccolor   = substr($description,1,7);
						$description = substr($description,8);
						$description = str_replace("\\/c","",$description);
						$description = "<span style='color:" . $desccolor . ";'>" . $description . "</span>";
					}
		if (is_string($description)) {
		return "<div class='itemd_desc'>" . $description . "</div>\n";
		} else { return ""; }
	    }
			
		protected function GreenAdornMax($item)
		{
		if (array_key_exists('growth_table',$item))
		{
		    $content .= "<br><div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div>";
			$content .= "<div class='ui-helper-clearfix'</div>";
			$typeInfo = $item->{'typeinfo'};
			$typecolor = $typeInfo->{'color'};
			$typename = $typeInfo->{'name'};
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Type</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>" . ucfirst($typecolor) . " " . ucfirst($typename) . "</div>";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
			$content .= "<div style='width: 120px; float: left; color: white;'>";
			$slotList = $typeInfo->{'slot_list'};
			foreach ($slotList as $slot) {
				$content .= " " . $slot->{'displayname'};
			}
		$content .= "</div><br>";
		$content .= "<div class='ui-helper-clearfix'></div>";
		$content .= "<br>";
		$growth = $item->{'growth_table'};
		$agi = 0; $intel = 0; $sta = 0; $str = 0; $wis = 0;
		$lcount = 0;
		for ($i = 1; $i <= 50; $i++) {
		(${"l{$i}"} = $growth->{'level'.$i});
		if ($growth->{'level'.$i} != "") {$lcount = $lcount +1;}
		$agi = $agi + (${"l{$i}"}->{'agi'});
		$intel = $intel + (${"l{$i}"}->{'int'});
		$sta = $sta + (${"l{$i}"}->{'sta'});
		$str = $str + (${"l{$i}"}->{'str'});
		$wis = $wis + (${"l{$i}"}->{'wis'});
		}
		$content .= "<div class='itemd_name'><small>Adds the following to an item at Level ".$lcount.":</small></div>\n";
		$content .= "<div class='ui-helper-clearfix'></div>";
		$content .= "<div class='itemd_green'>";
		if ($intel != 0) { $content .= "  +" . $intel . " int"; }
		if ($wis != 0) { $content .= "  +" . $wis . " wis"; }
		if ($str != 0) { $content .= "  +" . $str . " str"; }
		if ($agi != 0) { $content .= "  +" . $agi . " agi"; }
		if ($sta != 0) { $content .= "  +" . $sta . " sta"; }
		$content .= "</div>\n";
		$content .= "<div class='itemd_blue'>";
		$attackspeed = 0; $dps = 0; $doubleattackchance = 0; $critbonus = 0; $spellweaponattackspeed = 0;
		$spellweapondamagebonus = 0; $spelldoubleattackchance = 0;
		$spellweapondps = 0; $spellweapondoubleattackchance = 0; $weapondamagebonus = 0; $basemodifier = 0; $maxhpperc = 0;
		$armormitigationincrease = 0; $strikethrough = 0; $spellcastpct = 0; $spelltimereusespellonly = 0; $hategainmod = 0; $all = 0;
		for ($j = 1; $j <= 50; $j++) {
		(${"m{$j}"} = $growth->{'level'.$j});
		if (!empty(${"m{$j}"}->{'attackspeed'})) {
		$attackspeed = $attackspeed + (${"m{$j}"}->{'attackspeed'});
		}
		if (!empty(${"m{$j}"}->{'dps'})) {
		$dps = $dps + (${"m{$j}"}->{'dps'});
		}
		if (!empty(${"m{$j}"}->{'doubleattackchance'})) {
		$doubleattackchance = $doubleattackchance + (${"m{$j}"}->{'doubleattackchance'});
		}
		if (!empty(${"m{$j}"}->{'critbonus'})) {
		$critbonus = $critbonus + (${"m{$j}"}->{'critbonus'});
		}
		if (!empty(${"m{$j}"}->{'spellweaponattackspeed'})) {
		$spellweaponattackspeed = $spellweaponattackspeed + (${"m{$j}"}->{'spellweaponattackspeed'});
		}
		if (!empty(${"m{$j}"}->{'spellweapondps'})) {
		$spellweapondps = $spellweapondps + (${"m{$j}"}->{'spellweapondps'});
		}
		if (!empty(${"m{$j}"}->{'spellweapondoubleattackchance'})) {
		$spellweapondoubleattackchance = $spellweapondoubleattackchance + (${"m{$j}"}->{'spellweapondoubleattackchance'});
		}
		if (!empty(${"m{$j}"}->{'spelldoubleattackchance'})) {
		$spelldoubleattackchance = $spelldoubleattackchance + (${"m{$j}"}->{'spelldoubleattackchance'});
		}
		if (!empty(${"m{$j}"}->{'spellweapondamagebonus'})) {
		$spellweapondamagebonus = $spellweapondamagebonus + (${"m{$j}"}->{'spellweapondamagebonus'});
		}
		if (!empty(${"m{$j}"}->{'weapondamagebonus'})) {
		$weapondamagebonus = $weapondamagebonus + (${"m{$j}"}->{'weapondamagebonus'});
		}
		if (!empty(${"m{$j}"}->{'basemodifier'})) {
		$basemodifier = $basemodifier + (${"m{$j}"}->{'basemodifier'});
		}
		if (!empty(${"m{$j}"}->{'maxhpperc'})) {
		$maxhpperc = $maxhpperc + (${"m{$j}"}->{'maxhpperc'});
		}
		if (!empty(${"m{$j}"}->{'armormitigationincrease'})) {
		$armormitigationincrease = $armormitigationincrease + (${"m{$j}"}->{'armormitigationincrease'});
		}
		if (!empty(${"m{$j}"}->{'strikethrough'})) {
		$strikethrough = $strikethrough + (${"m{$j}"}->{'strikethrough'});
		}
		if (!empty(${"m{$j}"}->{'spellcastpct'})) {
		$spellcastpct = $spellcastpct + (${"m{$j}"}->{'spellcastpct'});
		}
		if (!empty(${"m{$j}"}->{'spelltimereusespellonly'})) {
		$spelltimereusespellonly = $spelltimereusespellonly + (${"m{$j}"}->{'spelltimereusespellonly'});
		}
		if (!empty(${"m{$j}"}->{'hategainmod'})) {
		$hategainmod = $hategainmod + (${"m{$j}"}->{'hategainmod'});
		}
		if (!empty(${"m{$j}"}->{'all'})) {
		$all = $all + (${"m{$j}"}->{'all'});
		}
		}
		/*
		if ($attackspeed != 0) { $content .= "  +" . $attackspeed . "% Attack Speed<br>"; }
		if ($dps != 0) { $content .= "  +" . $dps . " Damage Per Second<br>"; }
		if ($doubleattackchance != 0) { $content .= "  +" . $doubleattackchance . "% Multi Attack Chance<br>"; }
		if ($critbonus != 0) { $content .= "  +" . $critbonus . "% Crit Bonus<br>"; }
		if ($spellweaponattackspeed != 0) { $content .= "  +" . $spellweaponattackspeed . "% Spell Weapon Attack Speed<br>"; }
		if ($spellweapondps != 0) {	$content .= "  +" . $spellweapondps . " Spell Weapon Damage Per Second<br>"; }
		if ($spellweapondoubleattackchance != 0) { $content .= "  +" . $spellweapondoubleattackchance . "% Spell Weapon Multi Attack Chance<br>"; }
		if ($spellweapondamagebonus != 0) { $content .= "  +" . $spellweapondamagebonus . " Spell Weapon Damage Bonus<br>"; }
		if ($spelldoubleattackchance != 0) { $content .= "  +" . $spelldoubleattackchance . "% Doublecast Chance<br>"; }
		if ($weapondamagebonus != 0) { $content .= "  +" . $weapondamagebonus . " Weapon Damage Bonus<br>"; }
		if ($basemodifier != 0) { $content .= "  +" . $basemodifier . "% Potency<br>"; }
		if ($maxhpperc != 0) { $content .= "  +" . $maxhpperc . "% Max Health<br>"; }
		if ($armormitigationincrease != 0) { $content .= "  +" . $armormitigationincrease . "% Mitigation Increase<br>"; }
		if ($strikethrough != 0) { $content .= "  +" . $strikethrough . "% Strikethrough<br>"; }
		if ($spellcastpct != 0) { $content .= "  +" . $spellcastpct . "% Ability Casting Speed<br>"; }
		if ($spelltimereusespellonly != 0) { $content .= "  +" . $spelltimereusespellonly . "% Spell Reuse Speed<br>"; }
		if ($all !=0) { $content .= "  +" . $all . " Ability Modifier<br>"; }
		if ($hategainmod !=0) { $content .= "  +" . $hategainmod . "% Hate Gain<br>"; }
		*/
		if ($weapondamagebonus != 0) { $content .= $weapondamagebonus . " Weapon Damage Bonus<br>"; }
		if ($spellweapondamagebonus != 0) { $content .= $spellweapondamagebonus . " Spell Weapon Damage Bonus<br>"; }
		if ($basemodifier != 0) { $content .= $basemodifier . "% Potency<br>"; }
		if ($critbonus != 0) { $content .= $critbonus . "% Crit Bonus<br>"; }
		if ($spelldoubleattackchance != 0) { $content .= $spelldoubleattackchance . "% Doublecast Chance<br>"; }
		if ($attackspeed != 0) { $content .= $attackspeed . "% Attack Speed<br>"; }
		if ($dps != 0) { $content .= $dps . " Damage Per Second<br>"; }
		if ($doubleattackchance != 0) { $content .= $doubleattackchance . "% Multi Attack Chance<br>"; }
		if ($spellweaponattackspeed != 0) { $content .= $spellweaponattackspeed . "% Spell Weapon Attack Speed<br>"; }
		if ($spellweapondps != 0) {	$content .= $spellweapondps . " Spell Weapon Damage Per Second<br>"; }
		if ($spellweapondoubleattackchance != 0) { $content .= $spellweapondoubleattackchance . "% Spell Weapon Multi Attack Chance<br>"; }
		if ($maxhpperc != 0) { $content .= $maxhpperc . "% Max Health<br>"; }
		if ($armormitigationincrease != 0) { $content .= $armormitigationincrease . "% Mitigation Increase<br>"; }
		if ($strikethrough != 0) { $content .= $strikethrough . "% Strikethrough<br>"; }
		if ($spellcastpct != 0) { $content .= $spellcastpct . "% Ability Casting Speed<br>"; }
		if ($spelltimereusespellonly != 0) { $content .= $spelltimereusespellonly . "% Spell Reuse Speed<br>"; }
		if ($all !=0) { $content .= $all . " Ability Modifier<br>"; }
		if ($hategainmod !=0) { $content .= $hategainmod . "% Hate Gain<br>"; }
		return $content;
		}
		else { return ""; }
		}
				
		protected function GreenAdorn($item)
		{
			$content = "";
			$typeInfo = $item->{'typeinfo'};
			$growth = $typeInfo->{'growthdescription'};
			$growthinfo = $growth->{'growthdescription'};
			if (array_key_exists('growth_table',$item)) { 
			$content = "<div class='itemd_desc'><font color='yellow'>" . $growthinfo . "</font></div>"; }
			else { $content = ""; }
		return $content;
		}	
		
		protected function Adornments($item)
		{
			$typeInfo = $item->{'typeinfo'};
			$typecolor = $typeInfo->{'color'};
			$typename = $typeInfo->{'name'};
			if ($typeInfo->{'name'} == "adornment") {
				if (strncmp("\\#FF0000",$typename,8) == 0)
					{
						$description = substr($typename,8);
						$description = str_replace("\\/c","",$typename);
						$description = "<span style='color:red;'>" . $typename . "</span>";
					}
			# Item Level
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<br><div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div>";
			# Adornment Color
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Type</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_" . $typecolor . "'>" . ucfirst($typecolor) . " " . ucfirst($typename) . "</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
			$content .= "<div style='width: 120px; float: left; color: white;'>";
			$slotList = $typeInfo->{'slot_list'};
			foreach ($slotList as $slot) {
				$content .= " " . $slot->{'displayname'};
			}
			$content .= "</div><br>";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<br>";
			# usable by which classes
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div class='itemd_green'>";
			$usableByClasses = $this->GetUsableByClasses($typeInfo);
			$content .= $usableByClasses;
			$content .= "</div><br>";
			}
			else { $content = ""; }
			return $content;
		}
	
		protected function ItemIcon($item) 
		{
			$iconId = $item->{'iconid'};
			return "<div class='itemd_icon'><img src='http://census.soe.com/s:eqdkpplus/img/eq2/icons/$iconId/item/'></div>";
		}
		
		protected function ItemTier($item) 
		{
			$tierName = $item->{'tier'};
			# default to COMMON
			$tierColor = "white";
			if ($tierName == "FABLED") {
				$tierColor = "#ff939d";
				$tierShadow = "text-shadow: -1px 0px 0px rgb(0, 0, 0), 0px 1px 0px rgb(0, 0, 0), 1px 0px 0px rgb(0, 0, 0), 0px -1px 0px rgb(0, 0, 0), 0px 0px 4px rgb(223, 83, 95), 0px 0px 4px rgb(223, 83, 95);";
			}
			if ($tierName == "MASTERCRAFTED FABLED") {
				$tierColor = "#ff939d";
				$tierShadow = "text-shadow: -1px 0px 0px rgb(0, 0, 0), 0px 1px 0px rgb(0, 0, 0), 1px 0px 0px rgb(0, 0, 0), 0px -1px 0px rgb(0, 0, 0), 0px 0px 4px rgb(223, 83, 95), 0px 0px 4px rgb(223, 83, 95);";
			}
			if ($tierName == "LEGENDARY") {
				$tierColor = "#ffc993";
			}
			if ($tierName == "MASTERCRAFTED LEGENDARY") {
				$tierColor = "#ffc993";
			}
			if ($tierName == "TREASURED") {
				$tierColor = "#8accf0";
			}
			if ($tierName == "MASTERCRAFTED TREASURED") {
				$tierColor = "#8accf0";
			}
			if ($tierName == "ETHEREAL") {
				$tierColor = "#ff8C00";
				$tierShadow = "text-shadow: -1px 0px 0px rgb(0, 0, 0), 0px 1px 0px rgb(0, 0, 0), 1px 0px 0px rgb(0, 0, 0), 0px -1px 0px rgb(0, 0, 0), 0px 0px 4px rgb(213, 105, 0), 0px 0px 4px rgb(213, 105, 0);";
			}
			if ($tierName == "MYTHICAL") {
				$tierColor = "#d99fe9";
				$tierShadow = "text-shadow: -1px 0px 0px rgb(0, 0, 0), 0px 1px 0px rgb(0, 0, 0), 1px 0px 0px rgb(0, 0, 0), 0px -1px 0px rgb(0, 0, 0), 0px 0px 4px rgb(200, 89, 230), 0px 0px 4px rgb(200, 89, 230);";
			}
			return "<div style='color: $tierColor; $tierShadow' class='itemd_tier'>$tierName</div>";
		}
		
		protected function ItemFlags($item)
		{
			$content = "";
			#return $content;
			$count = 0;
			$flags = $item->{'flags'};
			foreach($flags as $key => $value) { 
				$enabled = $value->{'value'};
				if ($enabled == 1) { 
					if ($count == 0) {
						$content = "<div class='itemd_flags'>\n";
					}
				if ($key == 'notrasmute') {($key = 'no-transmute');}
				if ($key == 'nodestroy') {($key = 'no-destroy');}
				if ($key == 'novalue') {($key = 'no-value');}
				if ($key == 'notrade') {($key = 'no-trade');}
				if ($key == 'artiface') {($key = 'artifact');}
				if ($key == 'attunable') {($key = 'attuneable');}
				if ($key == 'indestructible') {($key = '');($indes = 1);}
					$content .= strtoupper($key)." &nbsp;\n";
					$count++;
				}
			}
			if ($count > 0) {
				$content .= "</div>\n";
			}
			if ($indes == 1) {($content .= "<div style='color: #7f00ff; font-size: 14px; font-weight: bold; margin-bottom: 3px;'>INDESTRUCTIBLE</div>");}
			return $content;
		}
		
		protected function ItemAttributes($item)
		{
			$content = "";
			$typeInfo = $item->{'typeinfo'};
			$growth = $typeInfo->{'growthdescription'};
			$growthinfo = $growth->{'growthdescription'};
			if (array_key_exists('growth_table',$item)) { $content = ""; } 
			else {
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "attribute") {
					if ($count % 5 == 0) {
						$content .= "<br><div class='itemd_green'>";
					}
					$content .= "+" . strtoupper($value->{'value'}) . " ";
					$content .= $value->{'displayname'} . " &nbsp;";
					$count++;
					if ($count % 5 == 0) { $content .= "</div>\n"; }
				}
			}
			}
			return $content;
		}
				
		protected function ItemResists($item)
		{
			$content = "";
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "ac") {
					if ($count % 5 == 0) {
						$content .= "<div class='itemd_green'>";
					}
					$content .= "+" . strtoupper($value->{'value'}) . " ";
					$content .= $value->{'displayname'} . " &nbsp;";
					$count++;
					if ($count % 5 == 0) {
						$content .= "</div>\n";
					}
				}
			}
			return $content;
		}
		
		protected function ItemSkillMod($item)
		{
			$content = "";
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "skill") {
					if ($count % 3 == 0) {
						$content .= "<div class='itemd_green'>";
					}
					$content .= "+" . strtoupper($value->{'value'}) . " ";
					$content .= $value->{'displayname'} . " &nbsp;";
					$count++;
					if ($count % 3 == 0) {
						$content .= "</div>\n";
					}
				}
			}
			return $content;
		}
		
		protected function ItemModifyProperties($item)
		{
			$content = "";
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "modifyproperty") {
					$count++;
					if ($count == 1) {
						$content .= "<div class='itemd_blue'>";
					}
					# format the value
					$content .= sprintf ("%01.1f", $value->{'value'});
					$content .= '% ' . $value->{'displayname'} . " <br/>";
				}
			}
			if ($count > 0) {
				$content .= "</div>\n";
			}
			return $content;
		}
		
		protected function ItemAdornments($item)
		{
			$content = "";
			$adornments = $item->{'adornment_list'};
			$count = 0;
			foreach($adornments as $key) {
					$count++;
					if ($count == 1) {
						$content .= "<div class='itemd_blue'>";
					}
					# format the value
					if (strncmp("\\#B000B0",$key->{'name'},8) == 0)
					{
						$an1 = substr($key->{'name'},8);
						$an2 = str_replace("\\/c","",$an1);
						$content .= "<span style='color:#B000B0;'>" . $an2 . "</span>";
					} else if (strncmp("\\#FF6600",$key->{'name'},8) == 0)
					{
						$an1 = substr($key->{'name'},8);
						$an2 = str_replace("\\/c","",$an1);
						$content .= "<span style='color:#FF6600;'>" . $an2 . "</span>";
					} else {
					$content .= $key->{'name'} . " <br/>";
					}
			}
			if ($count > 0) {
				$content .= "</div>\n";
			}
			return $content;
		}

		protected function GetUsableByClasses($typeInfo)
		{
			$classList = "";
			$classes = $typeInfo->{'classes'};
			$priestCount = 0;
			$fighterCount = 0;
			$scoutCount = 0;
			$mageCount = 0;
			$priestList = "";
			$fighterList = "";
			$scoutList = "";
			$mageList = "";
			foreach($classes as $class) 
			{
			   $className = $class->{'displayname'}; 
			   if ($className == "Defiler") { $priestCount++; $priestList .= "Defiler "; }
			   if ($className == "Mystic") { $priestCount++; $priestList .= "Mystic "; }
			   if ($className == "Templar") { $priestCount++; $priestList .= "Templar "; }
			   if ($className == "Inquisitor") { $priestCount++; $priestList .= "Inquisitor "; }
			   if ($className == "Fury") { $priestCount++; $priestList .= "Fury "; }
			   if ($className == "Warden") { $priestCount++; $priestList .= "Warden "; }
			   # channeler
			   if ($className == "Channeler") { $priestCount++; $priestList .= "Channeler "; }
			   # fighters
			   if ($className == "Berserker") { $fighterCount++; $fighterList .= "Berserker "; }
			   if ($className == "Guardian") { $fighterCount++; $fighterList .= "Guardian "; }
			   if ($className == "Monk") { $fighterCount++; $fighterList .= "Monk "; }
			   if ($className == "Bruiser") { $fighterCount++; $fighterList .= "Bruiser "; }
			   if ($className == "Paladin") { $fighterCount++; $fighterList .= "Paladin "; }
			   if ($className == "Shadowknight") { $fighterCount++; $fighterList .= "Shadowknight "; }
			   # scouts
			   if ($className == "Brigand") { $scoutCount++; $scoutList .= "Brigand "; }
			   if ($className == "Swashbuckler") { $scoutCount++; $scoutList .= "Swashbuckler "; }
			   if ($className == "Troubador") { $scoutCount++; $scoutList .= "Troubador "; }
			   if ($className == "Dirge") { $scoutCount++; $scoutList .= "Dirge "; }
			   if ($className == "Assassin") { $scoutCount++; $scoutList .= "Assassin "; }
			   if ($className == "Ranger") { $scoutCount++; $scoutList .= "Ranger "; }
			   # beastlord
			   if ($className == "Beastlord") { $scoutCount++; $scoutList .= "Beastlord "; }
			     # mages
			   if ($className == "Illusionist") { $mageCount++; $mageList .= "Illusionist "; }
			   if ($className == "Coercer") { $mageCount++; $mageList .= "Coercer "; }
			   if ($className == "Conjuror") { $mageCount++; $mageList .= "Conjuror "; }
			   if ($className == "Necromancer") { $mageCount++; $mageList .= "Necromancer "; }
			   if ($className == "Wizard") { $mageCount++; $mageList .= "Wizard "; }
			   if ($className == "Warlock") { $mageCount++; $mageList .= "Warlock "; }
			}
			if ($fighterCount == 6) {
				$classList .= "All Fighters ";
			}
			else {
				$classList .= $fighterList;
			}
			if ($priestCount >= 6) {
				$classList .= "All Priests ";
			}
			else {
				$classList .= $priestList;
			}
			if ($scoutCount >= 6) {
				$classList .= "All Scouts ";
			}
			else {
				$classList .= $scoutList;
			}
			if ($mageCount == 6) {
				$classList .= "All Mages ";
			}
			else {
				$classList .= $mageList;
			}
			return $classList;
		}

		protected function ItemTypeWeapon($item)
		{
			$content = "";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$typeInfo = $item->{'typeinfo'};
			$content .= "<br/><div style='float: left; color: white;'>";
			$wieldStyle = $typeInfo->{'wieldstyle'};
			$skill = $typeInfo->{'skill'};
			$content .= ucfirst($wieldStyle) . " " . ucfirst($skill);
			$content .= "</div>";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
			$content .= "<div style='width: 120px; float: left; color: white;'>";
			$slotList = $item->{'slot_list'};
			foreach ($slotList as $slot) {
				$content .= " " . $slot->{'name'};
			}
			$content .= "</div>";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Damage</div>";
			$minBaseDamage = $typeInfo->{'minbasedamage'};
			$maxMasteryDamage = $typeInfo->{'maxmasterydamage'};
			$content .= "<div style='width: 80px; float: left; color: white;'>";
			$content .= $minBaseDamage . " - " . $maxMasteryDamage;
			$content .= "</div>";
			# Weapon Rating
			$content .= "<div style='width: 100px; float: left; color: white;'>";
			$damageRating = $typeInfo->{'damagerating'};
			$content .= sprintf ("(%02.2f Rating)", $damageRating);
			$content .= "</div><div class='ui-helper-clearfix'></div>";
			# Delay
			$delay = $typeInfo->{'delay'};
			$content .= "<div style='width: 80px; float: left; color: white;'>Delay</div>";
			$content .= "<div style='width: 120px; float: left; color: white;'>";
			$content .= sprintf ("%02.1f",$delay);
			$content .= " seconds</div>";
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<br/>";
			# Item Level
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div>";
			# usable by which classes
			$content .= "<div class='ui-helper-clearfix'></div>";
			$content .= "<div class='itemd_green'>";
			$usableByClasses = $this->GetUsableByClasses($typeInfo);
			$content .= $usableByClasses;
			$content .= "</div>";
			return $content;
		}

		protected function ItemTypeArmor($item)
		{
			$typeInfo = $item->{'typeinfo'};
			$content .= "<div class='ui-helper-clearfix'></div>";
			$mitigation = $typeInfo->{'maxarmorclass'};
			if ($mitigation == $null) {
				$content .= "<br>";
				$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
				$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= "<div style='color: white;'> " . $slot->{'name'};
				}
			} else {
				$content .= "<br>";
				$content .= "<div style='width: 160px; float: left; color: white;'>";
				$knowledgeDesc = $typeInfo->{"knowledgedesc"};
				$content .= $knowledgeDesc . " (";
				$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= "" . $slot->{'name'};
				}
				$content .= ")";
				$content .= "</div>";
			}
			$content .= "<div class=\"ui-helper-clearfix\"></div>";
			# Mitigation
			if ($mitigation != $null) {
				$content .= "<div style='width: 80px; float: left; color: white;'>Mitigation</div>";
				$content .= "<div style='width: 150px; float: left; color: white; '>$mitigation</div>";
				$content .= "<div class='ui-helper-clearfix'</div>";
			}
			# Item Level
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div><br>";
			$content .= "<br><div class='itemd_green'>";
			$usableByClasses = $this->GetUsableByClasses($typeInfo);
			$content .= $usableByClasses;
			$content .= "</div>";
			return $content;
		}

		protected function ItemTypeMount($item)
		{
			$content .= "<br>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
			$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= "<div style='color: white;'> " . $slot->{'name'};
				}
			$content .= "<br><div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div><br>";
			$content .= "<br><div class='itemd_green'>";
			$usableByClasses = $this->GetUsableByClasses($typeInfo);
			$content .= $usableByClasses;
			$content .= "</div>";
			return $content;
		}
		
		protected function ItemTypeSpell($item)
		{
			$typeInfo = $item->{'typeinfo'};
			$content .= "<br>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div><br>";
			$content .= "<div class='itemd_green'>";
			$usableByClasses = $this->GetUsableByClasses($typeInfo);
			$content .= $usableByClasses;
			$content .= "</div>";
			return $content;
		}		
		
		protected function ItemTypeFood($item)
		{
			$typeInfo = $item->{'typeinfo'};
			$content .= "<br>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Slots</div>";
			$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= "<div style='color: white;'> " . $slot->{'name'};
				}
			$content .= "<br><div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'itemlevel'};
			$content .= "<div class='ui-helper-clearfix'</div>";
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div><br>";
			$content .= "<div style='width: 80px; float: left; color: white;'>Duration</div>";
			$duration = $typeInfo->{'duration'};
			$content .= "<div style='width: 150px; float: left; color: white;'>$duration</div><br>";
			$content .= "<div class='ui-helper-clearfix'</div>";
			return $content;
		}	
		
		protected function ItemType($item)
		{
			$typeInfo = $item->{'typeinfo'};
			if ($typeInfo->{'name'} == "weapon") {
				return $this->ItemTypeWeapon($item);
			}
			if ($typeInfo->{'name'} == "armor") {
				return $this->ItemTypeArmor($item);
			}
			if ($typeInfo->{'name'} == "expendable") {
			    return $this->ItemTypeMount($item);
			}
			if ($typeInfo->{'name'} == "spellscroll") {
			    return $this->ItemTypeSpell($item);
			}
			if ($typeInfo->{'name'} == "food") {
			    return $this->ItemTypeFood($item);
			}
		}

		protected function ItemEffects($item)
		{
			$content = "";
			$effects = $item->{'effect_list'};
			if ($effects == NULL) { return $content; } else {
			$content .= "<div class='itemd_effects'>Effects:</div>\n";
			$content .= "<div style='font-weight: normal; color:white;'>";
			foreach($effects as $key) {
					$description = htmlspecialchars_decode($key->{'description'});
					$indent = intval($key->{'indentation'});
					$indented = (6 + ($indent * 2));
					$padded = (8 + ($indent * 13));
					if (strncmp("\\#FF0000",$description,8) == 0)
					{
						$description = substr($description,8);
						$description = str_replace("\\/c","",$description);
						$description = "<span style='color:red;'>" . $description . "</span>";
					}
					if ($indent == 0)
					{
						$content .= $description . " <br/>";
					}
					else  {
					$content .= "<div style='text-indent: -".$indented."px; padding-left: ".$padded."px;'>&bull;&nbsp;" .$description . " <br/></div>";
					}
			}
			$content .= "</div>";
			return $content;
			}
		}
		
		protected function ItemSetBonus($item)
		{
			$content = "";
			if(isset($item->{'setbonus_info'})) {
			$setbonus = $item->{'setbonus_list'};
			$setinfo = $item->{'setbonus_info'};
			$setname = $setinfo->{'displayname'};
			$required = $setbonus->{'requireditems'};
			$content .= "<br><div style='width: 300px; float: left; color: yellow;'>".$setname."</div><br><br>";
			foreach ($setbonus as $set) {
					$content .= "<div style='color: white;'>(".$set->{'requireditems'}.")";
					$agi = 0; $int = 0; $sta = 0; $str = 0; $wis = 0; 
					$attackspeed = 0; $dps = 0; $doubleattackchance = 0; $critbonus = 0; $spellweaponattackspeed = 0; $flurry = 0; 
					$spellweapondps = 0; $spellweapondoubleattackchance = 0; $weapondamagebonus = 0; $basemodifier = 0; $maxhpperc = 0;
					$armormitigationincrease = 0; $strikethrough = 0; $spellcastpct = 0; $spelltimereusespellonly = 0; $hategainmod = 0; $all = 0;
					$spelldoubleattackchance = 0; $mana = 0; $effect = 0;
					if (!empty($set->{'agi'})) { $agi = ($set->{'agi'}); }
					if (!empty($set->{'int'})) { $int = ($set->{'int'}); }
					if (!empty($set->{'sta'})) { $sta = ($set->{'sta'}); }
					if (!empty($set->{'str'})) { $str = ($set->{'str'}); }
					if (!empty($set->{'wis'})) { $wis = ($set->{'wis'}); }
					if (!empty($set->{'attackspeed'})) { $attackspeed = ($set->{'attackspeed'}); }
					if (!empty($set->{'dps'})) { $dps = ($set->{'dps'}); }
					if (!empty($set->{'flurry'})) { $flurry = ($set->{'flurry'}); }
					if (!empty($set->{'spelldoubleattackchance'})) { $spelldoubleattackchance = ($set->{'spelldoubleattackchance'}); }
					if (!empty($set->{'doubleattackchance'})) { $doubleattackchance = ($set->{'doubleattackchance'}); }
					if (!empty($set->{'critbonus'})) { $critbonus = ($set->{'critbonus'}); }
					if (!empty($set->{'spellweaponattackspeed'})) { $spellweaponattackspeed = ($set->{'spellweaponattackspeed'}); }
					if (!empty($set->{'spellweapondps'})) { $spellweapondps = ($set->{'spellweapondps'}); }
					if (!empty($set->{'spellweapondoubleattackchance'})) { $spellweapondoubleattackchance = ($set->{'spellweapondoubleattackchance'}); }
					if (!empty($set->{'weapondamagebonus'})) { $weapondamagebonus = ($set->{'weapondamagebonus'}); }
					if (!empty($set->{'basemodifier'})) { $basemodifier = ($set->{'basemodifier'}); }
					if (!empty($set->{'maxhpperc'})) { $maxhpperc = ($set->{'maxhpperc'}); }
					if (!empty($set->{'armormitigationincrease'})) { $armormitigationincrease = ($set->{'armormitigationincrease'}); }
					if (!empty($set->{'strikethrough'})) { $strikethrough = ($set->{'strikethrough'}); }
					if (!empty($set->{'spellcastpct'})) { $spellcastpct = ($set->{'spellcastpct'}); }
					if (!empty($set->{'spelltimereusepct'})) { $spelltimereusepct = ($set->{'spelltimereusepct'}); }
					if (!empty($set->{'spelltimereusespellonly'})) { $spelltimereusespellonly = ($set->{'spelltimereusespellonly'}); }
					if (!empty($set->{'hategainmod'})) { $hategainmod = ($set->{'hategainmod'}); }
					if (!empty($set->{'all'})) { $all = ($set->{'all'}); }
					if (!empty($set->{'mana'})) { $mana = ($set->{'mana'}); }
					if ($int != 0) { $content .= "  +" . $int . " int&nbsp&nbsp"; }
					if ($wis != 0) { $content .= "  +" . $wis . " wis&nbsp&nbsp"; }
					if ($str != 0) { $content .= "  +" . $str . " str&nbsp&nbsp"; }
					if ($agi != 0) { $content .= "  +" . $agi . " agi&nbsp&nbsp"; }
					if ($sta != 0) { $content .= "  +" . $sta . " sta&nbsp&nbsp"; }
					if ($attackspeed != 0) { $content .= "  +" . $attackspeed . "%&nbspAttack&nbspSpeed&nbsp&nbsp"; }
					if ($dps != 0) { $content .= "  +" . $dps . "&nbspDamage&nbspPer&nbspSecond&nbsp&nbsp"; }
					if ($doubleattackchance != 0) { $content .= "  +" . $doubleattackchance . "%&nbspMulti&nbspAttack&nbspChance&nbsp&nbsp"; }
					if ($critbonus != 0) { $content .= "  +" . $critbonus . "%&nbspCrit&nbspBonus&nbsp&nbsp"; }
					if ($spellweaponattackspeed != 0) { $content .= "  +" . $spellweaponattackspeed . "%&nbspSpell&nbspWeapon&nbspAttack&nbspSpeed&nbsp&nbsp"; }
					if ($spellweapondps != 0) {	$content .= "  +" . $spellweapondps . "&nbspSpell&nbspWeapon&nbspDamage&nbspPer&nbspSecond&nbsp&nbsp"; }
					if ($spellweapondoubleattackchance != 0) { $content .= "  +" . $spellweapondoubleattackchance . "%&nbspSpell&nbspWeapon&nbspMulti&nbspAttack&nbspChance&nbsp&nbsp"; }
					if ($weapondamagebonus != 0) { $content .= "  +" . $weapondamagebonus . "&nbspWeapon&nbspDamage&nbspBonus&nbsp&nbsp"; }
					if ($basemodifier != 0) { $content .= "  +" . $basemodifier . "%&nbspPotency&nbsp&nbsp"; }
					if ($maxhpperc != 0) { $content .= "  +" . $maxhpperc . "%&nbspMax&nbspHealth&nbsp&nbsp"; }
					if ($armormitigationincrease != 0) { $content .= "  +" . $armormitigationincrease . "%&nbspMitigation&nbspIncrease&nbsp&nbsp"; }
					if ($strikethrough != 0) { $content .= "  +" . $strikethrough . "%&nbspStrikethrough&nbsp&nbsp"; }
					if ($spellcastpct != 0) { $content .= "  +" . $spellcastpct . "%&nbspAbility&nbspCasting&nbspSpeed&nbsp&nbsp"; }
					if ($spelltimereusepct != 0) { $content .= "  +" . $spelltimereusepct . "%&nbspAbility&nbspReuse&nbspSpeed&nbsp&nbsp"; }
					if ($spelltimereusespellonly != 0) { $content .= "  +" . $spelltimereusespellonly . "%&nbspSpell&nbspReuse&nbspSpeed&nbsp&nbsp"; }
					if ($all !=0) { $content .= "  +" . $all . "&nbspAbility&nbspModifier&nbsp&nbsp"; }
					if ($hategainmod !=0) { $content .= "  +" . $hategainmod . "%&nbspHate&nbspGain&nbsp&nbsp"; }
					if ($flurry !=0) { $content .= "  +" . $flurry . "%&nbspFlurry&nbsp&nbsp"; }
					if ($spelldoubleattackchance !=0) { $content .= "  +" . $spelldoubleattackchance . "%&nbspDoublecast Chance&nbsp&nbsp"; }
					if ($mana !=0) { $content .= "  +" . $mana . "&nbspPower&nbsp&nbsp"; }			
		if (!empty($set->{'effect'})) { $content .= "<br>&nbsp".($set->{'effect'}). " <br>"; }
	    for ($d = 1; $d <= 30; $d++) {
		if (!empty($set->{'descriptiontag_'.$d})) { $content .= "&nbsp".($set->{'descriptiontag_'.$d})."<br>"; }		
		}
			
			}
		
        }
		return $content;
		}
		
		protected function ItemAdornmentSlots($item) 
		{
			$content = "";
			$adornmentslots = $item->{'adornmentslot_list'};
			$count = 0;
			foreach($adornmentslots as $key) {
					$count++;
					if ($count == 1) {
						$content .= "<div class='adorncontainer'>";
					}
					$color = $key->{'color'};
					$content .= "<div class='itemd_adicon".$color."'> </div>";
			}
			if ($count > 0) {
				$content .= "</div>\n";
			}
			return $content;
		}
		
		protected function ItemPattern($item) 
		{
			$content = "";
			$typeInfo = $item->{'typeinfo'};
			if ($typeInfo->{'name'} == "itempattern") {
			$content .= "<br><div style='color: yellow;'>Creates:</div><br>";
			$patterns = $typeInfo->{'item_list'};
			if ($patterns == NULL) { return $content; } else {
			$content .= "<div style='font-weight: normal; color:white;'>";
				foreach($patterns as $key) {
					$display = $key->{'displayname'};
					$content .= "<div style='font-weight: normal; color:white;'>".$display."</div>";
				}
			}
			}
			return $content;
		}
		
		protected function GenerateItemStatsHTML($myItem) {
			$content = $this->OuterDivNoHide($myItem);
			$content .= $this->DisplayName($myItem);
			$content .= $this->ItemIcon($myItem);
			$content .= $this->ItemDescription($myItem);
			$content .= $this->GreenAdorn($myItem);
			$content .= $this->ItemTier($myItem);
			$content .= $this->ItemFlags($myItem);
			$content .= $this->ItemAdornmentSlots($myItem);
			$content .= $this->Adornments($myItem);
			$content .= $this->ItemAttributes($myItem);
			$content .= $this->ItemResists($myItem);
			$content .= $this->ItemSkillMod($myItem);
			$content .= $this->ItemModifyProperties($myItem);
			$content .= $this->ItemAdornments($myItem);
			$content .= $this->ItemType($myItem);
			$content .= $this->ItemEffects($myItem);
			$content .= $this->ItemSetBonus($myItem);
			$content .= $this->GreenAdornMax($myItem);
			$content .= $this->ItemPattern($myItem);
			$content .= "</div>\n";
			return $content;
		}
		
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eq2_sony', eq2_sony::$shortcuts);
?>