<?php
 /*
 * Project:     EQdkp-Plus Infotooltip
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2008-12-02 11:54:02 +0100 (Di, 02 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 3293 $
 *
 * $Id:   $
 */

include('itt_parser.aclass.php');

if(!class_exists('armory')) {
  class armory extends itt_parser {
	public $supported_games = array('wow');
    public $av_langs = array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU');#, 'jp' => 'ja_JP');

    public $settings = array(
		'itt_icon_loc' => array('name' => 'itt_icon_loc',
								'language' => 'pk_itt_icon_loc',
								'fieldtype' => 'text',
								'size' => false,
								'options' => false,
								'default' => 'http://eu.wowarmory.com/wow-icons/_images/64x64/'),
		'itt_icon_ext' => array('name' => 'itt_icon_ext',
								'language' => 'pk_itt_icon_ext',
								'fieldtype' => 'text',
								'size' => false,
								'options' => false,
								'default' => '.jpg'),
		'itt_default_icon' => array('name' => 'itt_default_icon',
									'language' => 'pk_itt_default_icon',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'inv_misc_questionmark')
							);

    private $url_prefix = '';
    private $searched_langs = array();
    private $armory_lang = array();

	public function __construct($init=false, $config=false, $root_path=false, $cache=false, $urlreader=false, $pdl=false)
	{
		parent::__construct($init, $config, $root_path, $cache, $urlreader, $pdl);
		$this->url_prefix = $this->config['armory_region'];
	}

	public function __destruct()
	{
		unset($this->url_prefix);
		unset($this->searched_langs);
		unset($this->armory_lang);
		parent::__destruct();
	}

    protected function searchItemID($itemname, $lang, $searchagain=0)
	{
		$searchagain++;
		$this->pdl->log('infotooltip', 'armory->searchItemID called: itemname: '.$itemname.', lang: '.$lang.', searchagain: '.$searchagain);
		//encode itemname for usage in url
		$encoded_name = str_replace(' ', '%20', str_replace('+', '%20', urlencode($itemname)));
		$url = 'http://'.$this->url_prefix.'.wowarmory.com/search.xml?searchType=items&searchQuery='.$encoded_name;
		$this->pdl->log('infotooltip', 'Search for ItemID at '.$url);
		$xml_data = $this->urlreader->GetURL($url, $lang);
        $xml = simplexml_load_string($xml_data);
		if((!is_object($xml) OR (int) $xml->armorySearch->tabs['count'] < 1)) {
		  $this->pdl->log('infotooltip', 'No Items found.');
		  if(count($this->config['lang_prio']) >= $searchagain) {
			$this->pdl->log('infotooltip', 'Search again in other language.');
			$this->searched_langs[] = $lang;
			foreach($this->config['lang_prio'] as $slang) {
				if(!in_array($slang, $this->searched_langs)) {
					return $this->searchItemID($itemname, $slang, $searchagain);
				}
			}
		  }
		}
		$this->searched_langs = array();

		$xml = $xml->armorySearch->searchResults->items;
		if(!is_object($xml)) {
			$this->pdl->log('infotooltip', 'Invalid XML');
			return array(0, 'items');
		}
		
		$searchResults = array();
		foreach($xml->children() as $item) {
			$searchResults[(int) $item['id']]['name'] = (string) $item['name'];
			foreach($item->children() as $filter) {
				if($filter['name'] == 'itemLevel') {
					$searchResults[(int) $item['id']]['ilvl'] = (string) $filter['value'];
				} elseif($filter['name'] == 'relevance') {
					$searchResults[(int) $item['id']]['relevance'] = (string) $filter['value'];
				}
			}
		}
		if(!function_exists('sort_by_relevance_ilvl')) {
			function sort_by_relevance_ilvl($a, $b) {
				if($a['relevance'] == $b['relevance']) {
					if($a['ilvl'] == $b['ilvl']) {
						return 0;
					}
					return ($a['ilvl'] > $b['ilvl']) ? -1 : 1;
				}
				return ($a['relevance'] > $b['relevance']) ? -1 : 1;
			}
		}
		uasort($searchResults, "sort_by_relevance_ilvl");
		reset($searchResults);
		$item_id = key($searchResults);
		$debug_out = ($item_id > 0) ? 'Item-ID found: '.$item_id : 'No Item-ID found';
		$this->pdl->log('infotooltip', $debug_out);
		return array($item_id, 'items');
	}

	protected function getItemData($item_id, $lang, $itemname='', $type='items', $data=array())
	{
		$this->pdl->log('infotooltip', 'armory->getItemData called: item_id: '.$item_id.', lang: '.$lang.', itemname: '.$itemname.', data: '.implode(', ', $data));
		
		if($item_id <= 0) {
			$this->pdl->log('infotooltip', 'No Item-ID given. Set baditem to true.');
			$item['baditem'] = true;
			return $item;
		}
		$ext = '';
		if($data) {
			$ext = '&r='.urlencode($data[0]).'&cn='.urlencode($data[1]).'&s='.((int) $data[2]);
		}
		$item['link'] = 'http://'.$this->url_prefix.'.wowarmory.com/item-tooltip.xml?i='.$item_id.$ext;
		$this->pdl->log('infotooltip', 'Fetch Item-Info from '.$item['link']);
		$xml = $this->urlreader->GetURL($item['link'], $lang);
		$xml = simplexml_load_string($xml);
		$xml = $xml->itemTooltips->itemTooltip;

		$item['id'] = (int) $xml->id;
		$item['name'] = (string) $xml->name;
		$item['icon'] = (string) $xml->icon;
		$item['lang'] = $lang;
		$item['color'] = 'q'.$xml->overallQualityId;
		$item['html'] = $this->build_tooltip($xml, $lang, $item);
		if(!$item['html']) {
			$this->pdl->log('infotooltip', 'No Tooltip could be created. Set baditem to true.');
			$item['baditem'] = true;
			return $item;
		}
		$this->pdl->log('infotooltip', 'Item succesfully fetched.');
        return $item;
	}

	private function build_tooltip($xml, $lang, $item)
	{
		if(!is_object($xml)) {
			$this->pdl->log('infotooltip', 'Invalid XML');
			return false;
		}
		$tooltip_data = array();
		$ignore = array('id', 'name', 'icon', 'overallQualityId', 'itemSource', 'equipData', 'spellData', 'damageData', 'socketData', 'durability', 'setData', 'requiredSkill', 'allowableClasses');
		foreach($xml->children() as $node) {
			if(!in_array($node->getName(), $ignore)) {
				$tooltip_data[$node->getName()] = (string) $node;
			}
		}
		$key = 0;
		foreach(@$xml->socketData->children() as $socket) {
			$tooltip_data['sockets'][$key]['color'] = (string) strtolower($socket['color']);
			if($socket['enchant']) {
				$tooltip_data['sockets'][$key]['enchant'] = (string) $socket['enchant'];
				$tooltip_data['sockets'][$key]['icon'] = (string) $socket['icon'];
			}
			if($socket->getName() == 'socketMatchEnchant') {
				unset($tooltip_data['sockets'][$key]);
				$tooltip_data['socket_match'] = (string) $socket;
			}
			$key++;
		}
		$tooltip_data['inv_type'] = (string) $xml->equipData->inventoryType;
		$tooltip_data['inv_sub'] = (string) $xml->equipData->subclassName;
		foreach(@$xml->spellData->children() as $spell) {
			$tooltip_data['spells'][] = array('trigger' => (int) $spell->trigger, 'desc' => (string) $spell->desc);
		}
		$tooltip_data['durability'] = (int) $xml->durability['max'];
		$tooltip_data['b_armor'] = (int) $xml->armor['armorBonus'];
		$tooltip_data['damage'] = array(
			'type' => (int) $xml->damageData->damage->type,
			'min' => (int) $xml->damageData->damage->min,
			'max' => (int) $xml->damageData->damage->max,
			'speed' => (string) $xml->damageData->speed,
			'dps' => (string) $xml->damageData->dps
		);
		$tooltip_data['drop'] = array(
			'area' => (string) $xml->itemSource['areaName'],
			'boss' => (string) $xml->itemSource['creatureName'],
			'diff' => (string) $xml->itemSource['difficulty'],
			'drop' => (string) $xml->itemSource['dropRate'],
			'value' => (string) $xml->itemSource['value']
		);
		$tooltip_data['set'] = array();
		foreach(@$xml->setData->children() as $sitem) {
			if($sitem->getName() == 'name') {
				$tooltip_data['set']['name'] = (string) $sitem;
			} elseif($sitem->getName() == 'item') {
				$tooltip_data['set']['item'][] = array('equipped' => (int) $sitem['equipped'], 'name' => (string) $sitem['name']);
			} elseif($sitem->getName() == 'setBonus') {
				$tooltip_data['set']['bonus'][] = array('desc' => (string) $sitem['desc'], 'threshold' => (string) $sitem['threshold']);
			}
		}
		$tooltip_data['requires']['name'] = (string) $xml->requiredSkill['name'];
		$tooltip_data['requires']['rank'] = (string) $xml->requiredSkill['rank'];
		foreach(@$xml->allowableClasses->children() as $class) {
			$tooltip_data['classes'] = (string) $class;
		}
		$html = '';
		$this->load_armory_lang($lang);
		$html .= "<table><tr><td><b class=\"".$item['color']."\">".$item['name']."</b><br/>";
		$html .= ($tooltip_data['heroic']) ? "<span class=\"q2\">".$this->armory_lang[$lang]['info']['heroic']."</span><br />" : "";
		$html .= ($tooltip_data['bonding']) ? $this->armory_lang[$lang]['tooltip']['bind'][$tooltip_data['bonding']]."<br />" : "";
		$html .= ($tooltip_data['maxCount']) ? (($xml->maxCount['uniqueEquippable']) ? $this->armory_lang[$lang]['tooltip']['unique-equipped'] : $this->armory_lang[$lang]['tooltip']['unique'])."<br />" : "";
		if($tooltip_data['inv_type'])
			$html .= "<table><tr><td>".$this->armory_lang[$lang]['slot'][$tooltip_data['inv_type']]."</td><th>".$tooltip_data['inv_sub']."</th></tr></table>";
		
		//damage (weapon only)
		if($tooltip_data['damage']['max']) {
			$html .= "<table><tr><td>".$tooltip_data['damage']['min']." - ".$tooltip_data['damage']['max']." ".$this->armory_lang[$lang]['tooltip']['damage'];
			$html .= ($tooltip_data['damage']['type']) ? "(".$this->armory_lang[$lang]['damage'][$tooltip_data['damage']['type']].")" : "";
			$html .= "</td><td>".$this->armory_lang[$lang]['tooltip']['speed']." ".$tooltip_data['damage']['speed']."</td></tr></table>";
			$html .= "(".$tooltip_data['damage']['dps']." ".$this->armory_lang[$lang]['tooltip']['dps'].")<br />";
		}
		
		//armor
		$html .= ($tooltip_data['requires']['name']) ? $this->armory_lang[$lang]['tooltip']['requires']." ".$tooltip_data['requires']['name']." (".$tooltip_data['requires']['rank'].")<br />" : "";
		$html .= ($tooltip_data['armor']) ? (($tooltip_data['b_armor']) ? "<span class=\"q2\">".$tooltip_data['armor']."</span>" : $tooltip_data['armor']).'  '.$this->armory_lang[$lang]['tooltip']['armor']."<br />" : "";
		
		//attributes
		foreach(array('bonusStrength', 'bonusAgility', 'bonusStamina', 'bonusIntellect', 'bonusSpirit', 'arcane-resistance', 'fire-resistance', 'nature-resistance', 'frost-resistance', 'shadow-resistance') as $val) {
			$html .= ($tooltip_data[$val]) ? "+".$tooltip_data[$val]." ".$this->armory_lang[$lang]['tooltip'][$val]."<br />" : "";
		}
		
		if($tooltip_data['enchant']) {
			$html .= "<span class=\"q2\">".$tooltip_data['enchant']."</span><br />";
		}
		
		//sockets
		foreach($tooltip_data['sockets'] as $socket) {
			if($socket['enchant']) {
				$html .= "<img src=\"http://eu.wowarmory.com/wow-icons/_images/21x21/".$socket['icon'].".png\" width=\"15\" height=\"15\" style=\"margin-bottom: -3px;\" />".$socket['enchant']."<br />";
			} else {
				$html .= "<span class=\"socket-".$socket['color']." q0\">".$this->armory_lang[$lang]['tooltip'][$socket['color'].'-socket']."</span><br />";
			}
		}
		
		//socket_match
		if($tooltip_data['socket_match']) {
			$html .= "<span class=\"q2\">".$this->armory_lang[$lang]['tooltip']['socket-bonus'].$tooltip_data['socket_match']."</span><br />";
		}
		
		//durability, level
		$html .= ($tooltip_data['durability']) ? $this->armory_lang[$lang]['tooltip']['durability'].": ".$tooltip_data['durability']." / ".$tooltip_data['durability']."<br />" : "";
		$html .= ($tooltip_data['classes'] > 0) ? $this->armory_lang[$lang]['tooltip']['classes'].": ".implode(', ', $tooltip_data['classes'])."<br />" : "";
		$html .= ($tooltip_data['requiredLevel'] > 0) ? $this->armory_lang[$lang]['tooltip']['requires-level'].": ".$tooltip_data['requiredLevel']."<br />" : "";
		$html .= ($tooltip_data['itemLevel']) ? $this->armory_lang[$lang]['info']['itemlevel']." ".$tooltip_data['itemLevel']."<br />" : "";
		$html .= "</td></tr></table><table><tr><td>";
		
		//special values
		foreach(array("increase-defense","increase-dodge", "bonusParryRating", "increase-dodge", "bonusBlockRating", "improve-crit-strike", "improve-hit-rating", "bonusHitTakenRating", "bonusCritTakenRating", "improve-resilience", "bonusHasteRating", "bonusSpellPower", "bonusExpertiseRating", "bonusArmorPenetration", "bonusAttackPower", "bonusRangedAttackPower", "bonusFeralAttackPower", "bonusManaRegen") as $val) {
			$html .= ($tooltip_data[$val]) ? "<span class=\"q2\">".$this->armory_lang[$lang]['tooltip'][$val]." ".$tooltip_data[$val]."</span><br/>" : "";
		}
		foreach($tooltip_data['spells'] as $spell) {
			$html .= "<span class=\"q2\">".$this->armory_lang[$lang]['tooltip']['trigger'][$spell['trigger']]." ".$spell['desc']."</span><br />";
		}
		
		//set-bonus
		if($tooltip_data['set']['name']) {
			$html .= "<br /><span class=\"q\">".$tooltip_data['set']['name']."</span>";
			$set_count = 0;
			foreach($tooltip_data['set']['item'] as $sdata) {
				$sclass = 'q0';
				if($sdata['equipped']) {
					$set_count++;
					$sclass = 'q8';
				}
				$html .= "<div class=\"".$sclass." indent\">".$sdata['name']."</div>";
			}
			foreach($tooltip_data['set']['bonus'] as $bonus) {
				$sclass = 'q0';
				if($bonus['threshold'] <= $set_count) {
					$sclass = 'q2';
				}
				$html .= "<span class=\"".$sclass."\">(".$bonus['threshold'].") ".$bonus['desc']."</span><br />";
			}
			$html .= "<br />";
		}
		
		//yellow description
		$html .= ($tooltip_data['desc']) ? "<span class=\"q\">".$tooltip_data['desc']."</span><br />" : "";
		
		//drop-source
		$html .= "<span class=\"q\">".$this->armory_lang[$lang]['source']['source']."</span>: ".$this->armory_lang[$lang]['source'][$tooltip_data['drop']['value']]."<br />";
		if($tooltip_data['drop']['value'] == 'sourceType.creatureDrop') {
			$html .= ($tooltip_data['drop']['area']) ? "<span class=\"q\">".$this->armory_lang[$lang]['source']['dungeon']."</span> ".$tooltip_data['drop']['area']."<br />" : "";
			$html .= ($tooltip_data['drop']['boss']) ? "<span class=\"q\">".$this->armory_lang[$lang]['source']['boss']."</span> ".$tooltip_data['drop']['boss']."<br />" : "";
			$html .= "<span class=\"q\">".$this->armory_lang[$lang]['source']['droprate']."</span> ".$this->armory_lang[$lang]['drop'][$tooltip_data['drop']['drop']]."<br />";
		}
		$html .= "</td></tr></table>";
		$tpl_html = file_get_contents($this->root_path.'infotooltip/includes/parser/templates/wow_popup.tpl');
		$html = str_replace('{ITEM_HTML}', $html, $tpl_html);
		return $html;
	}

	private function load_armory_lang($lang)
	{
		if(!$this->armory_lang[$lang]) {
			$this->armory_lang[$lang] = unserialize(file_get_contents($this->cache->FilePath($this->config['game'].'_armory_lang_'.$lang.'.itt', 'itt_cache')));
			if(!$this->armory_lang[$lang]) {
				$xml_lang = $this->urlreader->GetURL('http://'.$this->url_prefix.'.wowarmory.com/_content/'.$this->av_langs[$lang].'/strings.xml', $lang);
				$this->parse_armory_lang($xml_lang, $lang);
				$this->cache->putContent(serialize($this->armory_lang[$lang]), $this->cache->FilePath($this->config['game'].'_armory_lang_'.$lang.'.itt', 'itt_cache'));
			}
		}
	}

	private function parse_armory_lang($xml_lang, $lang)
	{
		$xml = simplexml_load_string($xml_lang);
		foreach($xml->children() as $str) {
			if($str->getName() == 'itemTooltip') {
				foreach($str->children() as $sstr) {
					$this->armory_lang[$lang]['tooltip'][str_replace('armory.item-tooltip.', '', (string) $sstr['id'])] = (string) $sstr;
				}
			} elseif($str->getName() == 'itemSlot') {
				foreach($str->children() as $sstr) {
					$this->armory_lang[$lang]['slot'][str_replace('armory.itemslot.slot.', '', (string) $sstr['id'])] = (string) $sstr;
				}
			} elseif($str->getName() == 'spellType') {
				foreach($str->children() as $sstr) {
					$this->armory_lang[$lang]['spell'][str_replace('armory.spell-type.', '', (string) $sstr['id'])] = (string) $sstr;
				}
			} elseif($str->getName() == 'itemType') {
				foreach($str->children() as $sstr) {
					$this->armory_lang[$lang]['item'][str_replace('armory.item-type.', '', (string) $sstr['id'])] = (string) $sstr;
				}
			} elseif($str->getName() == 'itemInfo') {
				foreach($str->children() as $sstr) {
					if(strpos($sstr['id'], 'armory.item-info.drop-rate') !== false) {
						$this->armory_lang[$lang]['drop'][str_replace('armory.item-info.drop-rate.', '', (string) $sstr['id'])] = (string) $sstr;
					} else {
						$this->armory_lang[$lang]['info'][str_replace('armory.item-info.', '', (string) $sstr['id'])] = (string) $sstr;
					}
				}
			} elseif($str->getName() == 'itemsSearchColumns') {
				foreach($str->children() as $sstr) {
					if(strpos($sstr['id'], 'armory.searchColumn.sourceType.') !== false) {
						$this->armory_lang[$lang]['source'][str_replace('armory.searchColumn.', '', (string) $sstr['id'])] = (string) $sstr;
					} elseif($sstr['id'] == 'armory.searchColumn.source') {
						$this->armory_lang[$lang]['source']['source'] = (string) $sstr;
					} elseif(strpos($sstr['id'], 'armory.hover.') !== false) {
						$this->armory_lang[$lang]['source'][str_replace('armory.hover.', '', (string) $sstr['id'])] = (string) $sstr;
					}
				}
			}
		}
			$this->armory_lang[$lang]['tooltip']['bind'][1] = $this->armory_lang[$lang]['tooltip']['binds-pickup'];
			$this->armory_lang[$lang]['tooltip']['bind'][2] = $this->armory_lang[$lang]['tooltip']['binds-equipped'];
			$this->armory_lang[$lang]['tooltip']['bind'][3] = $this->armory_lang[$lang]['tooltip']['binds-used'];
			$this->armory_lang[$lang]['tooltip']['bind'][4] = $this->armory_lang[$lang]['tooltip']['quest-item'];
			$this->armory_lang[$lang]['slot'] = array(
        		0 => $this->armory_lang[$lang]['tooltip']["non-equip"],
	        	1 => $this->armory_lang[$lang]['slot']["head"],
	        	2 => $this->armory_lang[$lang]['slot']["neck"],
	        	3 => $this->armory_lang[$lang]['slot']["shoulders"],
	        	4 => $this->armory_lang[$lang]['slot']["shirt"],
	        	5 => $this->armory_lang[$lang]['slot']["chest"],
	        	6 => $this->armory_lang[$lang]['slot']["waist"],
	        	7 => $this->armory_lang[$lang]['slot']["legs"],
	        	8 => $this->armory_lang[$lang]['slot']["feet"],
	        	9 => $this->armory_lang[$lang]['slot']["wrist"],
	        	10 => $this->armory_lang[$lang]['slot']["hands"],
	        	11 => $this->armory_lang[$lang]['slot']["finger"],
	        	12 => $this->armory_lang[$lang]['slot']["trinket"],
	        	13 => $this->armory_lang[$lang]['tooltip']["one-hand"],
	        	14 => $this->armory_lang[$lang]['slot']["offHand"],
	        	15 => $this->armory_lang[$lang]['slot']["ranged"],
	        	16 => $this->armory_lang[$lang]['slot']["back"],
	        	17 => $this->armory_lang[$lang]['tooltip']["two-hand"],
	        	18 => $this->armory_lang[$lang]['tooltip']["bag-type"],
	        	19 => $this->armory_lang[$lang]['slot']["tabard"],
	        	20 => $this->armory_lang[$lang]['slot']["chest"],
	        	21 => $this->armory_lang[$lang]['slot']["mainHand"],
	        	22 => $this->armory_lang[$lang]['slot']["offHand"],
	        	23 => $this->armory_lang[$lang]['tooltip']["held-off-hand"],
	        	24 => $this->armory_lang[$lang]['tooltip']["projectile"],
	        	25 => $this->armory_lang[$lang]['tooltip']["thrown"],
	        	26 => $this->armory_lang[$lang]['slot']["ranged"],
	        	27 => $this->armory_lang[$lang]['tooltip']["quiver-type"],
	        	28 => $this->armory_lang[$lang]['slot']["relic"]
	        );
	        $this->armory_lang[$lang]['damage'] = array(
	        	0 => "",
	        	1 => $this->armory_lang[$lang]['tooltip']["holy-damage"],
	        	2 => $this->armory_lang[$lang]['tooltip']["fire-damage"],
	        	3 => $this->armory_lang[$lang]['tooltip']["nature-damage"],
	        	4 => $this->armory_lang[$lang]['tooltip']["frost-damage"],
	        	5 => $this->armory_lang[$lang]['tooltip']["shadow-damage"],
	        	6 => $this->armory_lang[$lang]['tooltip']["arcane-damage"],
	        );
	        $this->armory_lang[$lang]['tooltip']["blockValue"] = $this->armory_lang[$lang]['tooltip']["block"];
            $this->armory_lang[$lang]['tooltip']["bonusStrength"] = $this->armory_lang[$lang]['tooltip']["strength"];
            $this->armory_lang[$lang]['tooltip']["bonusAgility"] = $this->armory_lang[$lang]['tooltip']["agility"];
            $this->armory_lang[$lang]['tooltip']["bonusStamina"] = $this->armory_lang[$lang]['tooltip']["stamina"];
            $this->armory_lang[$lang]['tooltip']["bonusIntellect"] = $this->armory_lang[$lang]['tooltip']["intellect"];
            $this->armory_lang[$lang]['tooltip']["bonusSpirit"] = $this->armory_lang[$lang]['tooltip']["spirit"];
            $this->armory_lang[$lang]['tooltip']["fireresist"] = $this->armory_lang[$lang]['tooltip']["fire-resistance"];
            $this->armory_lang[$lang]['tooltip']["natureresist"] = $this->armory_lang[$lang]['tooltip']["nature-resistance"];
            $this->armory_lang[$lang]['tooltip']["frostresist"] = $this->armory_lang[$lang]['tooltip']["frost-resistance"];
            $this->armory_lang[$lang]['tooltip']["shadowresist"] = $this->armory_lang[$lang]['tooltip']["shadow-resistance"];
            $this->armory_lang[$lang]['tooltip']["arcaneresist"] = $this->armory_lang[$lang]['tooltip']["arcane-resistance"];
            $this->armory_lang[$lang]['tooltip']["bonusDefenseSkillRating"] = $this->armory_lang[$lang]['tooltip']["increase-defense"];
            $this->armory_lang[$lang]['tooltip']["increasedodge"] = $this->armory_lang[$lang]['tooltip']["increase-dodge"];
            $this->armory_lang[$lang]['tooltip']["bonusCritSpellRating"] = $this->armory_lang[$lang]['tooltip']["improve-spell-crit"];
            $this->armory_lang[$lang]['tooltip']["bonusHitSpellRating"] = $this->armory_lang[$lang]['tooltip']["improve-spell"];
            $this->armory_lang[$lang]['tooltip']["bonusCritRating"] = $this->armory_lang[$lang]['tooltip']["improve-crit-strike"];
            $this->armory_lang[$lang]['tooltip']["bonusHitRating"] = $this->armory_lang[$lang]['tooltip']["improve-hit-rating"];
            $this->armory_lang[$lang]['tooltip']["bonusResilience"] = $this->armory_lang[$lang]['tooltip']["improve-resilience"];
            $this->armory_lang[$lang]['tooltip']["bonusHasteSpellRating"] = $this->armory_lang[$lang]['tooltip']["bonusHasteSpellRating"];

            $this->armory_lang[$lang]['tooltip']['trigger'] = array(
            	0 => $this->armory_lang[$lang]['tooltip']['use'],
            	1 => $this->armory_lang[$lang]['tooltip']['equip'],
            	2 => $this->armory_lang[$lang]['tooltip']['chance-on-hit']
            );
	}
  }
}