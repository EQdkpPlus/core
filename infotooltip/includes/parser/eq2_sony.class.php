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
			$myContent = "";
		/*
			if (array_key_exists('description',$item)) {
				#$description = $item->{'description'};
				$description = "test";
				if ($description) {
					$myContent = "<div class=\"itemd_desc\">" . $description . "</div>\n";
				}
			}
		*/
			return $myContent;
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
			$typeInfo = $item->{'typeinfo'};
			$content .= "<br/><div style='float: left; color: white;'>";
			$wieldStyle = $typeInfo->{'wieldstyle'};
			$skill = $typeInfo->{'skill'};
			$content .= $wieldStyle . " " . ucfirst($skill);
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
			$content .= "<div class='ui-helper-clearfix'</div><br/>";
			# Item Level
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
				$content .= "<div style='width: 80px; float: left;'>Slots</div>";
				$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= " " . $slot->{'name'};
				}
			} else {
				$content .= "<div style='width: 150px; float: left; color: white;'>";
				$knowledgeDesc = $typeInfo->{"knowledgedesc"};
				$content .= $knowledgeDesc . "( ";
				$slotList = $item->{'slot_list'};
				foreach ($slotList as $slot) {
					$content .= " " . $slot->{'name'};
				}
				$content .= ")";
				$content .= "</div>";
			}
			#print "<div class=\"ui-helper-clearfix\"></div>";
			# Mitigation
			if ($mitigation != $null) {
				$content .= "<br/><div style='width: 80px; float: left; color: white;'>Mitigation</div>";
				$content .= "<div style='width: 150px; float: left; color: white; '>$mitigation</div>";
			}
			# Item Level
			$content .= "<br/><div style='width: 80px; float: left; color: white;'>Level</div>";
			$itemLevel = $item->{'leveltouse'};
			$content .= "<div style='width: 150px; float: left;' class='itemd_green'>$itemLevel</div><br/>";
			#
			$content .= "<div class='itemd_green'>";
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
			$content .= $this->ItemDescription($myItem);
			$content .= $this->ItemIcon($myItem);
			$content .= $this->ItemTier($myItem);
			$content .= $this->ItemFlags($myItem);
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