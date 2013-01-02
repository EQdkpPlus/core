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
				'eq2' => array('icon_loc' => 'http://data.soe.com/img/eq2/icons/', 'icon_ext' => '/item/', 'default_icon' => 'unknown'),
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
			$itemInfo = urlencode('displayname=i/' . $itemname . '/');
			$link = 'http://data.soe.com/json/get/eq2/item/?' . $itemInfo;
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
			$url = 'http://data.soe.com/json/get/eq2/item/' . $item['id'];
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
		if (is_string($description)) {
		return "<div class='itemd_desc'>" . $description . "</div>\n";
		} else { return ""; }
	    }
			
		protected function GreenAdornMax($item)
		{
		if (array_key_exists('growth_table',$item))
		{
		    $content .= "<div style='width: 80px; float: left; color: white;'>Level</div>";
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
		$l1 = $growth->{'level1'};
		$l2 = $growth->{'level2'};
		$l3 = $growth->{'level3'};
		$l4 = $growth->{'level4'};
		$l5 = $growth->{'level5'};
		$l6 = $growth->{'level6'};
		$l7 = $growth->{'level7'};
		$l8 = $growth->{'level8'};
		$l9 = $growth->{'level9'};
		$l10 = $growth->{'level10'};
		$agi = (($l1->{'agi'}) + ($l2->{'agi'}) + ($l3->{'agi'}) + ($l4->{'agi'}) + ($l5->{'agi'}) + 
		($l6->{'agi'}) + ($l7->{'agi'}) + ($l8->{'agi'}) + ($l9->{'agi'}) + ($l10->{'agi'}));
		$intel = (($l1->{'int'}) + ($l2->{'int'}) + ($l3->{'int'}) + ($l4->{'int'}) + ($l5->{'int'}) + 
		($l6->{'int'}) + ($l7->{'int'}) + ($l8->{'int'}) + ($l9->{'int'}) + ($l10->{'int'}));
		$sta = (($l1->{'sta'}) + ($l2->{'sta'}) + ($l3->{'sta'}) + ($l4->{'sta'}) + ($l5->{'sta'}) + 
		($l6->{'sta'}) + ($l7->{'sta'}) + ($l8->{'sta'}) + ($l9->{'sta'}) + ($l10->{'sta'}));
		$str = (($l1->{'str'}) + ($l2->{'str'}) + ($l3->{'str'}) + ($l4->{'str'}) + ($l5->{'str'}) + 
		($l6->{'str'}) + ($l7->{'str'}) + ($l8->{'str'}) + ($l9->{'str'}) + ($l10->{'str'}));
		$wis = (($l1->{'wis'}) + ($l2->{'wis'}) + ($l3->{'wis'}) + ($l4->{'wis'}) + ($l5->{'wis'}) + 
		($l6->{'wis'}) + ($l7->{'wis'}) + ($l8->{'wis'}) + ($l9->{'wis'}) + ($l10->{'wis'}));
		"<div class='itemd_name'>" . $item->{'displayname'} . "</div>\n";
		$content .= "<div class='itemd_name'>Spirit Stone at Max Level</div>\n";
		$content .= "<div class='ui-helper-clearfix'></div>";
		$content .= "<div class='itemd_green'>";
		if ($intel != 0) { $content .= "  +" . $intel . " int"; }
		if ($wis != 0) { $content .= "  +" . $wis . " wis"; }
		if ($str != 0) { $content .= "  +" . $str . " str"; }
		if ($agi != 0) { $content .= "  +" . $agi . " agi"; }
		if ($sta != 0) { $content .= "  +" . $sta . " sta"; }
		$content .= "</div>\n";
		$content .= "<div class='itemd_blue'>";
		$attackspeed = (($l1->{'attackspeed'}) + ($l2->{'attackspeed'}) + ($l3->{'attackspeed'}) + ($l4->{'attackspeed'}) + ($l5->{'attackspeed'}) + 
		($l6->{'attackspeed'}) + ($l7->{'attackspeed'}) + ($l8->{'attackspeed'}) + ($l9->{'attackspeed'}) + ($l10->{'attackspeed'}));
		$dps = (($l1->{'dps'}) + ($l2->{'dps'}) + ($l3->{'dps'}) + ($l4->{'dps'}) + ($l5->{'dps'}) + 
		($l6->{'dps'}) + ($l7->{'dps'}) + ($l8->{'dps'}) + ($l9->{'dps'}) + ($l10->{'dps'}));
		$doubleattackchance = (($l1->{'doubleattackchance'}) + ($l2->{'doubleattackchance'}) + ($l3->{'doubleattackchance'}) + ($l4->{'doubleattackchance'}) + ($l5->{'doubleattackchance'}) + 
		($l6->{'doubleattackchance'}) + ($l7->{'doubleattackchance'}) + ($l8->{'doubleattackchance'}) + ($l9->{'doubleattackchance'}) + ($l10->{'doubleattackchance'}));
		$critbonus = (($l1->{'critbonus'}) + ($l2->{'critbonus'}) + ($l3->{'critbonus'}) + ($l4->{'critbonus'}) + ($l5->{'critbonus'}) + 
		($l6->{'critbonus'}) + ($l7->{'critbonus'}) + ($l8->{'critbonus'}) + ($l9->{'critbonus'}) + ($l10->{'critbonus'}));
		$spellweaponattackspeed = (($l1->{'spellweaponattackspeed'}) + ($l2->{'spellweaponattackspeed'}) + ($l3->{'spellweaponattackspeed'}) + ($l4->{'spellweaponattackspeed'}) + ($l5->{'spellweaponattackspeed'}) + 
		($l6->{'spellweaponattackspeed'}) + ($l7->{'spellweaponattackspeed'}) + ($l8->{'spellweaponattackspeed'}) + ($l9->{'spellweaponattackspeed'}) + ($l10->{'spellweaponattackspeed'}));
		$spellweapondps = (($l1->{'spellweapondps'}) + ($l2->{'spellweapondps'}) + ($l3->{'spellweapondps'}) + ($l4->{'spellweapondps'}) + ($l5->{'spellweapondps'}) + 
		($l6->{'spellweapondps'}) + ($l7->{'spellweapondps'}) + ($l8->{'spellweapondps'}) + ($l9->{'spellweapondps'}) + ($l10->{'spellweapondps'}));
		$spellweapondoubleattackchance = (($l1->{'spellweapondoubleattackchance'}) + ($l2->{'spellweapondoubleattackchance'}) + ($l3->{'spellweapondoubleattackchance'}) + ($l4->{'spellweapondoubleattackchance'}) + ($l5->{'spellweapondoubleattackchance'}) + 
		($l6->{'spellweapondoubleattackchance'}) + ($l7->{'spellweapondoubleattackchance'}) + ($l8->{'spellweapondoubleattackchance'}) + ($l9->{'spellweapondoubleattackchance'}) + ($l10->{'spellweapondoubleattackchance'}));
		$weapondamagebonus = (($l1->{'weapondamagebonus'}) + ($l2->{'weapondamagebonus'}) + ($l3->{'weapondamagebonus'}) + ($l4->{'weapondamagebonus'}) + ($l5->{'weapondamagebonus'}) + 
		($l6->{'weapondamagebonus'}) + ($l7->{'weapondamagebonus'}) + ($l8->{'weapondamagebonus'}) + ($l9->{'weapondamagebonus'}) + ($l10->{'weapondamagebonus'}));
		$basemodifier = (($l1->{'basemodifier'}) + ($l2->{'basemodifier'}) + ($l3->{'basemodifier'}) + ($l4->{'basemodifier'}) + ($l5->{'basemodifier'}) + 
		($l6->{'basemodifier'}) + ($l7->{'basemodifier'}) + ($l8->{'basemodifier'}) + ($l9->{'basemodifier'}) + ($l10->{'basemodifier'}));
		$maxhpperc = (($l1->{'maxhpperc'}) + ($l2->{'maxhpperc'}) + ($l3->{'maxhpperc'}) + ($l4->{'maxhpperc'}) + ($l5->{'maxhpperc'}) + 
		($l6->{'maxhpperc'}) + ($l7->{'maxhpperc'}) + ($l8->{'maxhpperc'}) + ($l9->{'maxhpperc'}) + ($l10->{'maxhpperc'}));
		$armormitigationincrease = (($l1->{'armormitigationincrease'}) + ($l2->{'armormitigationincrease'}) + ($l3->{'armormitigationincrease'}) + ($l4->{'armormitigationincrease'}) + ($l5->{'armormitigationincrease'}) + 
		($l6->{'armormitigationincrease'}) + ($l7->{'armormitigationincrease'}) + ($l8->{'armormitigationincrease'}) + ($l9->{'armormitigationincrease'}) + ($l10->{'armormitigationincrease'}));
		$strikethrough = (($l1->{'strikethrough'}) + ($l2->{'strikethrough'}) + ($l3->{'strikethrough'}) + ($l4->{'strikethrough'}) + ($l5->{'strikethrough'}) + 
		($l6->{'strikethrough'}) + ($l7->{'strikethrough'}) + ($l8->{'strikethrough'}) + ($l9->{'strikethrough'}) + ($l10->{'strikethrough'}));
		$spellcastpct = (($l1->{'spellcastpct'}) + ($l2->{'spellcastpct'}) + ($l3->{'spellcastpct'}) + ($l4->{'spellcastpct'}) + ($l5->{'spellcastpct'}) + 
		($l6->{'spellcastpct'}) + ($l7->{'spellcastpct'}) + ($l8->{'spellcastpct'}) + ($l9->{'spellcastpct'}) + ($l10->{'spellcastpct'}));
		$spelltimereusespellonly = (($l1->{'spelltimereusespellonly'}) + ($l2->{'spelltimereusespellonly'}) + ($l3->{'spelltimereusespellonly'}) + ($l4->{'spelltimereusespellonly'}) + ($l5->{'spelltimereusespellonly'}) + 
		($l6->{'spelltimereusespellonly'}) + ($l7->{'spelltimereusespellonly'}) + ($l8->{'spelltimereusespellonly'}) + ($l9->{'spelltimereusespellonly'}) + ($l10->{'spelltimereusespellonly'}));
		$all = (($l1->{'all'}) + ($l2->{'all'}) + ($l3->{'all'}) + ($l4->{'all'}) + ($l5->{'all'}) + 
		($l6->{'all'}) + ($l7->{'all'}) + ($l8->{'all'}) + ($l9->{'all'}) + ($l10->{'all'}));
		if ($attackspeed != 0) { $content .= "  +" . $attackspeed . "% Attack Speed<br>"; }
		if ($dps != 0) { $content .= "  +" . $dps . " Damage Per Second<br>"; }
		if ($doubleattackchance != 0) { $content .= "  +" . $doubleattackchance . "% Multi Attack Chance<br>"; }
		if ($critbonus != 0) { $content .= "  +" . $critbonus . "% Crit Bonus<br>"; }
		if ($spellweaponattackspeed != 0) { $content .= "  +" . $spellweaponattackspeed . "% Spell Weapon Attack Speed<br>"; }
		if ($spellweapondps != 0) {	$content .= "  +" . $spellweapondps . " Spell Weapon Damage Per Second<br>"; }
		if ($spellweapondoubleattackchance != 0) { $content .= "  +" . $spellweapondoubleattackchance . "% Spell Weapon Multi Attack Chance<br>"; }
		if ($weapondamagebonus != 0) { $content .= "  +" . $weapondamagebonus . " Weapon Damage Bonus<br>"; }
		if ($basemodifier != 0) { $content .= "  +" . $basemodifier . "% Potency<br>"; }
		if ($maxhpperc != 0) { $content .= "  +" . $maxhpperc . "% Max Health<br>"; }
		if ($armormitigationincrease != 0) { $content .= "  +" . $armormitigationincrease . "% Mitigation Increase<br>"; }
		if ($strikethrough != 0) { $content .= "  +" . $strikethrough . "% Strikethrough<br>"; }
		if ($spellcastpct != 0) { $content .= "  +" . $spellcastpct . "% Ability Casting Speed<br>"; }
		if ($spelltimereusespellonly != 0) { $content .= "  +" . $spelltimereusespellonly . "% Spell Reuse Speed<br>"; }
		if ($all !=0) { $content .= "  +" . $all . " Ability Modifier<br>"; }
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
			$content = "<div class='itemd_desc'>" . $growthinfo . "</div>"; }
			else { $content = ""; }
		return $content;
		}	
		
		protected function Adornments($item)
		{
			$typeInfo = $item->{'typeinfo'};
			$typecolor = $typeInfo->{'color'};
			$typename = $typeInfo->{'name'};
			if ($typeInfo->{'name'} == "adornment") {
			//if ($typename = "adornment") {
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
			return "<div class='itemd_icon'><img src='http://data.soe.com/img/eq2/icons/$iconId/item/'></div>";
		}
		
		protected function ItemTier($item) 
		{
			$tierName = $item->{'tier'};
			# default to COMMON
			$tierColor = "white";
			if ($tierName == "FABLED") {
				$tierColor = "#ff939d";
			}
			if ($tierName == "LEGENDARY") {
				$tierColor = "#ff939d";
			}
			if ($tierName == "TREASURED") {
				$tierColor = "#8accf0";
			}
			if ($tierName == "ETHEREAL") {
				$tierColor = "#ff8C00";
			}
			if ($tierName == "MYTHICAL") {
				$tierColor = "#d99fe9";
			}
			return "<div style='color: $tierColor;' class='itemd_tier'>$tierName</div>";
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
					$content .= strtoupper($key)." &nbsp;\n";
					$count++;
				}
			}
			if ($count > 0) {
				$content .= "</div>\n";
			}
			return $content;
		}
		
		protected function ItemAttributes($item)
		{
			$content = "";
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "attribute") {
					if ($count % 3 == 0) {
						$content .= "<br><div class='itemd_green'>";
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
		
		protected function ItemResists($item)
		{
			$content = "";
			$modifiers = $item->{'modifiers'};
			$count = 0;
			foreach($modifiers as $key => $value) {
				$type = $value->{'type'};
				if ($type == "ac") {
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
					$content .= $key->{'name'} . " <br/>";
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
			if ($priestCount == 6) {
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
				$classList .= $mageListList;
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

		protected function ItemType($item)
		{
			$typeInfo = $item->{'typeinfo'};
			if ($typeInfo->{'name'} == "weapon") {
				return $this->ItemTypeWeapon($item);
			}
			if ($typeInfo->{'name'} == "armor") {
				return $this->ItemTypeArmor($item);
			}
		}

		protected function ItemEffects($item) 
		{
			$content = "";
			$effects = $item->{'effect_list'};
			$count = 0;
			foreach($effects as $key) {
					$count++;
					if ($count == 1) {
						$content .= "<div class='itemd_effects'>Effects:</div>\n";
						$content .= "<div style='font-weight: normal; color:white;'>";
					}
					# format the value
					$description = $key->{'description'};
					if (strncmp("\\#FF0000",$description,8) == 0)
				{
						$description = substr($description,8);
						$description = str_replace("\\/c","",$description);
						$content .= "&nbsp;&nbsp;&bull;&nbsp;";
						$content .= "<span style='color:red;'>" . $description . "</span></br>";
					}
					else
					{
						$indent = $key->{'indentation'};
						if ($indent > 0) {
							for ($i=0;$i<$indent;$i++)
							{
								$content .= "&nbsp;&nbsp;";
							}
							$content .= "&bull;&nbsp;";
						}
						$content .= $description . " <br/>";
					}
			}
			if ($count > 0) {
				$content .= "</div>\n";
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
						$content .= "<div class='itemd_adornslots'>Adornment Slots:</div>";
						$content .= "<div style='font-weight: normal;'>";
					}
					$color = $key->{'color'};
					$content .= "<span style='color: $color;'>" . ucfirst($color) . "</span> ";
			}
			if ($count > 0) {
				#$content .= "</div></div>\n";
				$content .= "</div>\n";
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
			$content .= $this->GreenAdornMax($myItem);
			$content .= $this->Adornments($myItem);
			$content .= $this->ItemAttributes($myItem);
			$content .= $this->ItemResists($myItem);
			$content .= $this->ItemSkillMod($myItem);
			$content .= $this->ItemModifyProperties($myItem);
			$content .= $this->ItemAdornments($myItem);
			$content .= $this->ItemType($myItem);
			$content .= $this->ItemEffects($myItem);
			$content .= $this->ItemAdornmentSlots($myItem);
			$content .= "</div>\n";
			return $content;
		}
		
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eq2_sony', eq2_sony::$shortcuts);
?>